<?php
/**
 * WooCommerce AvaTax
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce AvaTax to newer
 * versions in the future. If you wish to customize WooCommerce AvaTax for your
 * needs please refer to http://docs.woocommerce.com/document/woocommerce-avatax/
 *
 * @author    SkyVerge
 * @copyright Copyright (c) 2016-2021, SkyVerge, Inc.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

namespace SkyVerge\WooCommerce\AvaTax;

defined( 'ABSPATH' ) or exit;

use ActionScheduler_Store;
use Exception;
use SkyVerge\WooCommerce\AvaTax\API\Models\HS_Classification_Model;
use SkyVerge\WooCommerce\AvaTax\API\Models\HS_Item_Model;
use SkyVerge\WooCommerce\AvaTax\API\Responses\Abstract_HS_Classification_Response;
use SkyVerge\WooCommerce\PluginFramework\v5_5_0 as Framework;
use WC_Action_Queue;
use WC_Product;
use WC_Product_Query;
use WP_Post;

/**
 * Handles the synchronization process and notifies admins on important updates.
 *
 * @since 1.13.0
 */
class Landed_Cost_Sync_Handler {


	/** @var string the WooCommerce Action Queue hook name for syncing products */
	const PRODUCT_SYNC_ACTION_QUEUE_HOOK = 'wc_avatax_process_enqueued_product';

	/** @var string the WooCommerce Action Queue group name for syncing products */
	const PRODUCT_SYNC_ACTION_QUEUE_GROUP = 'wc_avatax_landed_cost_sync';

	/** @var string sync action name to create a product */
	const PRODUCT_SYNC_ACTION_CREATE = 'create';

	/** @var string sync action name to update a product */
	const PRODUCT_SYNC_ACTION_UPDATE = 'update';


	/** @var WC_Action_Queue instance */
	protected $action_queue;

	/** @var string option key for storing the syncing state */
	private $landed_cost_syncing_state_option_key = 'wc_avatax_landed_cost_syncing_state';

	/** @var string option key for storing the full sync status */
	private $landed_cost_full_sync_option_key = 'wc_avatax_landed_cost_full_sync';

	/** @var string option key for storing products data pending sync */
	private $landed_cost_products_pending_sync_option_key = 'wc_avatax_landed_cost_products_pending_sync';

	/** @var string option key for storing products that encountered sync errors */
	private $landed_cost_products_with_sync_errors_option_key = 'wc_avatax_landed_cost_products_with_sync_errors';

	/** @var string option key for storing products that had sync resolutions */
	private $landed_cost_products_with_sync_resolutions_option_key = 'wc_avatax_landed_cost_products_with_sync_resolutions';

	/** @var array list of products flagged to have classifications updated */
	protected $products_to_update_classification = [];

	/**
	 * Initializes the action queue and hooks.
	 *
	 * @since 1.13.0
	 */
	public function __construct() {

		$this->action_queue = new WC_Action_Queue();

		$this->add_hooks();
	}


	/**
	 * Gets the current instance of the action queue.
	 *
	 * @since 1.13.0
	 *
	 * @return WC_Action_Queue
	 */
	public function get_action_queue() : WC_Action_Queue {

		return $this->action_queue;
	}


	/**
	 * Adds handler actions and filters.
	 *
	 * @since 1.13.0
	 */
	protected function add_hooks() {

		add_action( self::PRODUCT_SYNC_ACTION_QUEUE_HOOK, [ $this, 'handle_enqueued_product' ] );
		add_action( 'admin_notices', [ $this, 'maybe_finish_full_sync' ] );
		add_action( 'pre_post_update', [ $this, 'flag_updated_products_to_enqueue' ], 10, 2 );
		add_action( 'save_post', [ $this, 'maybe_enqueue_saved_product' ], 10, 2 );
		add_action( 'woocommerce_save_product_variation', [ $this, 'maybe_enqueue_saved_product_from_variation' ] );

		// tests if background processing is supported
		add_action( 'wp_ajax_wc_avatax_landed_cost_sync_handler_test', [ $this, 'supports_background_processing' ] );
	}


	/**
	 * Checks products main fields for change and flags it to enqueue update action.
	 *
	 * @since 1.13.0
	 *
	 * @internal
	 *
	 * @see Landed_Cost_Sync_Handler::should_resync_product()
	 *
	 * @param int $post_id
	 * @param array $data
	 */
	public function flag_updated_products_to_enqueue( $post_id, $data ) {

		// bail early if not a product post
		if ( empty( $data['post_type'] ) || empty( $data['post_name'] ) || 'product' !== $data['post_type'] ) {
			return;
		}

		$product_post = get_post( $post_id );

		// check for changed fields
		if ( $data['post_excerpt'] !== $product_post->post_excerpt ||
		     $data['post_content'] !== $product_post->post_content ||
		     $data['post_parent'] !== $product_post->post_parent ||
		     $data['post_name'] !== $product_post->post_name ||
		     $this->has_updated_categories( $post_id ) ) {

			$this->products_to_update_classification[ $post_id ] = true;
		}
	}


	/**
	 * Checks if the given product has updated/changed categories.
	 *
	 * @since 1.13.0
	 *
	 * @param int $product_id
	 *
	 * @return bool
	 */
	protected function has_updated_categories( int $product_id ) : bool {

		$posted_terms = Framework\SV_WC_Helper::get_posted_value( 'tax_input' );

		if ( is_array( $posted_terms ) && isset( $posted_terms['product_cat'] ) ) {

			$new_categories_ids = array_filter( array_map( 'absint', $posted_terms['product_cat'] ) );
			sort( $new_categories_ids, SORT_ASC );

			$old_categories_ids = wp_get_post_terms( $product_id, 'product_cat', [ 'fields' => 'ids' ] );
			sort( $old_categories_ids, SORT_ASC );

			$updated_categories_ids = array_unique( array_merge( $old_categories_ids, array_values( $new_categories_ids ) ) );
			sort( $updated_categories_ids, SORT_ASC );

			return $old_categories_ids !== $updated_categories_ids;
		}

		return false;
	}


	/**
	 * Maybe enqueues a product to be synced.
	 *
	 * @since 1.13.0
	 *
	 * @internal
	 *
	 * @param int $post_id
	 * @param WP_Post $post
	 */
	public function maybe_enqueue_saved_product( $post_id, $post ) {

		if ( ! isset( $post->post_type ) || empty( $post->post_name ) || 'product' !== $post->post_type ) {
			return;
		}

		$product = wc_get_product( $post_id );

		if ( ! $product ) {
			return;
		}

		$products = [];
		$product_id = $product->get_id();

		foreach ( wc_avatax()->get_landed_cost_handler()->get_countries_for_product_sync() as $country ) {

			$sync_action = $this->get_product_sync_action( $product, $country );

			if ( ! empty( $sync_action ) ) {
				$products[] = (new Landed_Cost_Sync_Enqueued_Product())
					->set_product_id( $product_id )
					->set_country_of_destination( $country )
					->set_action( $sync_action );
			}
		}

		$this->enqueue_products( $products );
	}


	/**
	 * Maybe enqueues a product variation’s parent to be synced.
	 *
	 * @since 1.13.0
	 *
	 * @internal
	 *
	 * @param int $variation_id
	 */
	public function maybe_enqueue_saved_product_from_variation( $variation_id ) {

		$product_variation = wc_get_product( $variation_id );

		if ( ! $product_variation ) {
			return;
		}

		$parent_id = $product_variation->get_parent_id();
		$parent_post = get_post( $parent_id );

		if ( ! $parent_id || ! $parent_post ) {
			return;
		}

		$this->maybe_enqueue_saved_product( $parent_id, $parent_post );
	}


	/**
	 * Determines the sync action to be executed for the product.
	 *
	 * @since 1.13.0
	 *
	 * @param WC_Product $product
	 * @param string $destination_country
	 * @return string
	 */
	protected function get_product_sync_action( WC_Product $product, string $destination_country ) : string {

		$classification_id = wc_avatax()->get_landed_cost_handler()->get_classification_id( $product, $destination_country );

		if ( empty( $classification_id ) ) {
			return self::PRODUCT_SYNC_ACTION_CREATE;
		}

		if ( $this->should_resync_product( $product ) ) {
			return self::PRODUCT_SYNC_ACTION_UPDATE;
		}

		return '';
	}


	/**
	 * Determines whether the product must be synced again.
	 *
	 * @since 1.13.0
	 *
	 * @param WC_Product $product
	 * @return bool
	 */
	public function should_resync_product( WC_Product $product ) : bool {

		// checks in the flagged products first
		if ( ! empty( $this->products_to_update_classification[ $product->get_id() ] ) ) {
			return true;
		}

		$changes = $product->get_changes();

		if ( empty( $changes ) ) {
			return false;
		}

		foreach ( [ 'description', 'short_description', 'name', 'parent_id', 'category_ids' ] as $property_name ) {
			if ( array_key_exists( $property_name, $changes ) ) {
				return true;
			}
		}

		return false;
	}


	/**
	 * Processes the list of products in the sync queue.
	 *
	 * @since 1.13.0
	 *
	 * @internal
	 */
	public function handle_enqueued_product( array $product_data ) {

		if ( ! $this->is_syncing_active() ) {

			$pending_products_data = get_option( $this->landed_cost_products_pending_sync_option_key );

			if ( is_array( $pending_products_data ) ) {
				$pending_product_data = array_merge( $pending_products_data, [ $product_data ] );
			} else {
				$pending_product_data = [ $product_data ];
			}

			update_option( $this->landed_cost_products_pending_sync_option_key, $pending_product_data );

		} else {

			$this->process_product( new Landed_Cost_Sync_Enqueued_Product( $product_data ) );
		}

		// gives a one second break between each product sync action to prevent multiple API requests
		sleep( 1 );
	}


	/**
	 * Adds a list of products to the sync queue.
	 *
	 * @since 1.13.0
	 *
	 * @param Landed_Cost_Sync_Enqueued_Product[] $products
	 * @return Landed_Cost_Sync_Enqueued_Product[] enqueued products
	 */
	public function enqueue_products( array $products ) : array {

		$enqueued_products = [];

		foreach ( $products as $product ) {

			if ( ! $product instanceof Landed_Cost_Sync_Enqueued_Product || $this->is_product_scheduled( $product ) ) {
				continue;
			}

			try {

				$this->get_action_queue()->schedule_single(
					$product->get_timestamp() ?: time(),
					self::PRODUCT_SYNC_ACTION_QUEUE_HOOK,
					[ 'product' => $product->to_array() ],
					'wc_avatax_landed_cost_sync'
				);

				$enqueued_products[] = $product;

			} catch ( Exception $e ) {

				if ( wc_avatax()->logging_enabled() ) {
					wc_avatax()->log( $e->getMessage() );
				}
			}
		}

		return $enqueued_products;
	}


	/**
	 * Enqueues all products for syncing countries that have no HS code yet.
	 *
	 * @since 1.13.0
	 *
	 * @return Landed_Cost_Sync_Enqueued_Product[] enqueued products list
	 */
	public function enqueue_all_products() : array {

		return $this->enqueue_products(
			$this->get_products_to_be_synced($this->query_all_products())
		);
	}


	/**
	 * Maybe enqueues products that are pending sync.
	 *
	 * @since 1.13.0
	 */
	public function maybe_enqueue_pending_products() {

		$products_to_sync = [];

		foreach ( (array) get_option( $this->landed_cost_products_pending_sync_option_key, [] ) as $product_data ) {
			if ( is_array( $product_data ) ) {
				$products_to_sync[] = new Landed_Cost_Sync_Enqueued_Product( $product_data );
			}
		}

		$this->enqueue_products( $products_to_sync );

		update_option( $this->landed_cost_products_pending_sync_option_key, [] );
	}


	/**
	 * Maybe starts a new full database sync.
	 *
	 * @since 1.13.0
	 */
	public function maybe_start_full_sync() {

		if ( false === wc_string_to_bool( get_option( $this->landed_cost_full_sync_option_key, 'no' ) ) ) {

			update_option( $this->landed_cost_full_sync_option_key, 'yes' );
		}
	}


	/**
	 * Determines whether a full database sync is active or not.
	 *
	 * @since 1.13.0
	 *
	 * @return bool
	 */
	public function is_full_syncing_active() : bool {

		return 'yes' === get_option( $this->landed_cost_full_sync_option_key, 'no' );
	}


	/**
	 * Determines whether the landed cost syncing is active or not.
	 *
	 * @since 1.13.0
	 *
	 * @return bool
	 */
	public function is_syncing_active() : bool {

		return 'on' === get_option( $this->landed_cost_syncing_state_option_key, 'off' );
	}


	/**
	 * Processes a product to run the classification workflow.
	 *
	 * @since 1.13.0
	 *
	 * @param Landed_Cost_Sync_Enqueued_Product $product
	 */
	protected function process_product( Landed_Cost_Sync_Enqueued_Product $product ) {

		$wc_product = wc_get_product( $product->get_product_id() );

		if ( ! $wc_product ) {
			// bail early as the product is not found (maybe deleted)
			return;
		}

		$country_of_destination = $product->get_country_of_destination();
		$landed_cost_handled    = wc_avatax()->get_landed_cost_handler();

		if ( ! in_array( $country_of_destination, $landed_cost_handled->get_supported_countries(), true ) ) {
			// bail early as the destination country is not supported
			return;
		}

		$classification_id       = $landed_cost_handled->get_classification_id( $wc_product, $country_of_destination );
		$hs_item_model           = new HS_Item_Model( $wc_product );
		$hs_classification_model = new HS_Classification_Model();
		$hs_classification_model->set_item( $hs_item_model );
		$hs_classification_model->set_id( $classification_id );
		$hs_classification_model->set_country_of_destination( $country_of_destination );

		$api      = wc_avatax()->get_hs_api();
		$response = null;

		try {
			switch ( $product->get_action() ) {
				case 'create':
					$response = $api->create_hs_classification( $hs_classification_model );
					break;

				case 'update':
					$response = $api->update_hs_classification( $hs_classification_model );
					break;

				case 'get':
					$response = $api->get_hs_classification( $hs_classification_model );
					break;
			}
		} catch ( Framework\SV_WC_API_Exception $exception ) {
			if ( wc_avatax()->logging_enabled() ) {
				wc_avatax()->log( $exception->getMessage() );
			}
		}

		if ( null === $response ) {
			// bail as not appropriate API request/response found
			return;
		}

		if ( $response->has_errors() ) {
			$this->handle_error( $product, $response );
		} elseif ( $response->is_pending() ) {
			$this->handle_pending( $product, $wc_product, $response );
		} elseif ( $response->cannot_be_classified() ) {
			$this->handle_cannot_be_classified( $product, $response );
		} elseif ( $response->is_classified() ) {
			$this->handle_classified( $wc_product, $response );
		}
	}


	/**
	 * Handles an IC API error response.
	 *
	 * @since 1.13.0
	 *
	 * @param Landed_Cost_Sync_Enqueued_Product $product
	 * @param Abstract_HS_Classification_Response $response
	 */
	protected function handle_error( Landed_Cost_Sync_Enqueued_Product $product, Abstract_HS_Classification_Response $response ) {

		$errors = $response->get_errors();

		if ( wc_avatax()->logging_enabled() ) {
			foreach ( $errors->get_error_codes() as $error_code ) {
				wc_avatax()->log( $error_code . ': ' . $errors->get_error_message( $error_code ) );
			}
		}

		$this->store_error_product( $product );
	}


	/**
	 * Handles an IC API response indicating that the product is pending classification.
	 *
	 * @since 1.13.0
	 *
	 * @param Landed_Cost_Sync_Enqueued_Product $product
	 * @param WC_Product $wc_product
	 * @param Abstract_HS_Classification_Response $response
	 */
	protected function handle_pending(Landed_Cost_Sync_Enqueued_Product $product, WC_Product $wc_product, Abstract_HS_Classification_Response $response ) {

		wc_avatax()->get_landed_cost_handler()->save_classification_id( $wc_product, $response->get_country_of_destination(), $response->get_hs_classification_id() );

		$product->set_timestamp( time() + $this->get_wait_time_to_get_classifications( $product ) );
		$product->set_action( 'get' );

		$this->enqueue_products( [ $product ] );
	}


	/**
	 * Handles an IC API response indicating that the product cannot be classified.
	 *
	 * @since 1.13.0
	 *
	 * @param Landed_Cost_Sync_Enqueued_Product $product
	 * @param Abstract_HS_Classification_Response $response
	 */
	protected function handle_cannot_be_classified( Landed_Cost_Sync_Enqueued_Product $product, Abstract_HS_Classification_Response $response ) {

		if ( ( $resolution = $response->get_resolution() ) && wc_avatax()->logging_enabled() ) {
			$product->set_resolution( $resolution );

			wc_avatax()->log( Abstract_HS_Classification_Response::CLASSIFICATION_STATUS_UNAVAILABLE . ': ' . $resolution );
		}

		$this->store_product_that_cannot_be_classified( $product );
	}


	/**
	 * Handles an IC API response indicating that the product is classified.
	 *
	 * @since 1.13.0
	 *
	 * @param WC_Product $wc_product
	 * @param Abstract_HS_Classification_Response $response
	 */
	protected function handle_classified( WC_Product $wc_product, Abstract_HS_Classification_Response $response ) {

		wc_avatax()->get_landed_cost_handler()->save_hs_code( $wc_product, $response->get_country_of_destination(), $response->get_hs_code() );
	}


	/**
	 * Determines whether a product is already scheduled to be synced.
	 *
	 * @since 1.13.0
	 *
	 * @param Landed_Cost_Sync_Enqueued_Product $product
	 * @return bool
	 */
	public function is_product_scheduled( Landed_Cost_Sync_Enqueued_Product $product ) : bool {

		return ! empty( $this->get_action_queue()->search( [
			'hook'   => self::PRODUCT_SYNC_ACTION_QUEUE_HOOK,
			'args'   => [ 'product' => $product->to_array() ],
			'group'  => self::PRODUCT_SYNC_ACTION_QUEUE_GROUP,
			'status' => ActionScheduler_Store::STATUS_PENDING,
		], 'ids' ) );
	}


	/**
	 * Determines whether the site supports background processing or not.
	 *
	 * @see Framework\SV_WP_Background_Job_Handler::test_connection() handling
	 *
	 * @since 1.13.0
	 *
	 * @return bool
	 */
	public function supports_background_processing() : bool {

		$test_url = add_query_arg( 'action', 'wc_avatax_landed_cost_sync_handler_test', admin_url( 'admin-ajax.php' ) );
		$result   = wp_safe_remote_get( $test_url );
		$body     = ! is_wp_error( $result ) ? wp_remote_retrieve_body( $result ) : null;

		// some servers may add a UTF8-BOM at the beginning of the response body, so we check if our test
		// string is included in the body, as an equal check would produce a false negative test result
		$supports_loopback = $body && Framework\SV_WC_Helper::str_exists( $body, '[TEST_LOOPBACK]' );

		/**
		 * Filters whether the current server supports background processing.
		 *
		 * @since 1.13.0
		 *
		 * @param bool $supports_loopback
		 */
		return (bool) apply_filters( 'wc_avatax_supports_background_processing', $supports_loopback );
	}


	/**
	 * Maybe notifies the admin that the full sync is finished.
	 *
	 * @since 1.13.0
	 *
	 * @internal
	 */
	public function maybe_finish_full_sync() {

		if ( false === $this->is_full_syncing_active() ) {
			// bail early as full sync is not active
			return;
		}

		if ( $this->count_pending_sync_actions() > 0 ) {
			// bail early as there are some scheduled actions in the works
			return;
		}

		wc_avatax()->get_admin_notice_handler()->add_admin_notice(
			sprintf(
				/* translators: Placeholders: %1$s - <strong> tag, %2$s - </strong> tag */
				__( '%1$sYour catalog is synced to AvaTax!%2$s Cross-border tax calculations can now take place at checkout. Catalog updates will be synced to AvaTax as you add, update, or delete products in WooCommerce.', 'woocommerce-avatax' ),
				'<strong>', '</strong>'
			),
			'wc-avatax-full-sync-started-notice',
			[
				'dismissible' => true,
			]
		);

		// disable the notice flag
		update_option( $this->landed_cost_full_sync_option_key, 'no' );
	}


	/**
	 * Adds products with errors and products that cannot be classified back to the sync queue.
	 *
	 * @since 1.13.0
	 *
	 * @return array the list of enqueued products
	 */
	public function resync_products_with_errors() {

		$products_with_sync_errors      = (array) get_option( $this->landed_cost_products_with_sync_errors_option_key, [] );
		$products_with_sync_resolutions = (array) get_option( $this->landed_cost_products_with_sync_resolutions_option_key, [] );
		$products = [];

		foreach ( array_merge( $products_with_sync_errors, $products_with_sync_resolutions ) as $product_data ) {
			$product = new Landed_Cost_Sync_Enqueued_Product( $product_data );
			$product->set_timestamp( time() + $this->get_wait_time_to_get_classifications( $product ) );
			$products[] = $product;
		}

		$enqueued_products = $this->enqueue_products( $products );

		update_option( $this->landed_cost_products_with_sync_errors_option_key, [] );
		update_option( $this->landed_cost_products_with_sync_resolutions_option_key, [] );

		return $enqueued_products;
	}


	/**
	 * Toggles the landed cost syncing state.
	 *
	 * If the background sync is running, it will be turned off and vice-versa.
	 *
	 * @since 1.13.0
	 */
	public function toggle_syncing() {

		$current_sync_status = 'on' === get_option( $this->landed_cost_syncing_state_option_key );

		$new_sync_status = $current_sync_status ? 'off' : 'on';

		update_option( $this->landed_cost_syncing_state_option_key, $new_sync_status );

		if ( 'on' === $new_sync_status ) {
			$this->enqueue_all_products();
			$this->maybe_start_full_sync();
			$this->maybe_enqueue_pending_products();
		}
	}


	/**
	 * Stores a product in an error list.
	 *
	 * The list may be used later for re-syncing matters or even to let merchants know which products must be fixed.
	 *
	 * @since 1.13.0
	 *
	 * @param Landed_Cost_Sync_Enqueued_Product $product
	 */
	public function store_error_product( Landed_Cost_Sync_Enqueued_Product $product ) {

		$this->store_product_for_later_resync( $product, $this->landed_cost_products_with_sync_errors_option_key );
	}


	/**
	 * Stores a product in a list to indicate that the product cannot be classified.
	 *
	 * The list may be used later for resyncing matters or even to let merchants know which products must be fixed.
	 *
	 * @since 1.13.0
	 *
	 * @param Landed_Cost_Sync_Enqueued_Product $product
	 */
	public function store_product_that_cannot_be_classified( Landed_Cost_Sync_Enqueued_Product $product ) {

		$this->store_product_for_later_resync( $product, $this->landed_cost_products_with_sync_resolutions_option_key );
	}


	/**
	 * Stores the product in a list that may be used later for re-syncing.
	 *
	 * @since 1.13.0
	 *
	 * @param Landed_Cost_Sync_Enqueued_Product $product the product to be re-synced later
	 * @param string $option_name the WP option name used to store the list
	 */
	protected function store_product_for_later_resync( Landed_Cost_Sync_Enqueued_Product $product, string $option_name ) {

		$product_list = get_option( $option_name, [] );

		if ( is_array( $product_list ) ) {
			$product_list[ $product->get_product_id() ] = $product->to_array();
		} else {
			$product_list = [ $product->get_product_id() => $product->to_array() ];
		}

		update_option( $option_name, $product_list );
	}


	/**
	 * Gets all products in the database that are candidates to be synced.
	 *
	 * @since 1.13.0
	 *
	 * @return int[] an array of products IDs
	 */
	protected function query_all_products() : array {

		return ( new WC_Product_Query( [ 'return' => 'ids' ] ) )->get_products();
	}


	/**
	 * Gets a list of enqueable products to be synced.
	 *
	 * @since 1.13.0
	 *
	 * @param int[] $products_ids an array of products IDs
	 * @return Landed_Cost_Sync_Enqueued_Product[] a list of products to be synced
	 */
	protected function get_products_to_be_synced( array $products_ids ) : array {

		$enqueable_products = [];

		foreach ( $products_ids as $product_id ) {

			foreach ( wc_avatax()->get_landed_cost_handler()->get_countries_for_product_sync() as $country ) {

				$product = wc_get_product( $product_id );

				if ( empty( wc_avatax()->get_landed_cost_handler()->get_hs_code( $product, $country ) ) ) {

					$enqueable_products[] = new Landed_Cost_Sync_Enqueued_Product( [
						'product_id'             => $product->get_id(),
						'country_of_destination' => $country,
						'action'                 => 'create',
					] );
				}
			}
		}

		return $enqueable_products;
	}


	/**
	 * Gets the number of pending sync actions.
	 *
	 * It basically counts how many actions are scheduled for the handler callback that are pending.
	 *
	 * @since 1.13.0
	 *
	 * @return int
	 */
	protected function count_pending_sync_actions() : int {

		return count( $this->action_queue->search( [
			'hook' => self::PRODUCT_SYNC_ACTION_QUEUE_HOOK,
			'status' => ActionScheduler_Store::STATUS_PENDING
		], 'ids' ) );
	}


    /**
     * Gets the time in seconds to wait before attempting to get a classification.
     *
     * It defaults to 24 hours (86400), but can be filtered.
     *
     * @since 1.13.0
     *
     * @param Landed_Cost_Sync_Enqueued_Product $product the product to be synced
     * @return int the time in seconds to wait before attempting to get a classification
     */
	private function get_wait_time_to_get_classifications( Landed_Cost_Sync_Enqueued_Product $product ) : int {

        /**
         * Filters the number of seconds a product sync is delayed while waiting to get a classification.
         *
         * @since 1.13.0
         *
         * @param int $delay The number of seconds between create and get calls.
         * @param Landed_Cost_Sync_Enqueued_Product $product The product to be synced
         * @param Landed_Cost_Sync_Handler $this The current instance of the sync handler
         */
        return apply_filters( 'wc_avatax_wait_time_to_get_classifications', DAY_IN_SECONDS, $product, $this );
    }


}
