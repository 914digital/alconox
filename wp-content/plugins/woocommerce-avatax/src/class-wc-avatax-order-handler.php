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

use SkyVerge\WooCommerce\PluginFramework\v5_5_0 as Framework;

defined( 'ABSPATH' ) or exit;

/**
 * Handle the order-specific functionality.
 *
 * @since 1.0.0
 */
class WC_AvaTax_Order_Handler {


	/** @var string The prefix for order note error messages **/
	protected $error_prefix;

	/** @var WC_AvaTax_API_Tax_Response[] array or API response objects, with order IDs as keys */
	protected $calculated_order_taxes = array();


	/**
	 * Construct the class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		$this->error_prefix = '<strong>' . __( 'AvaTax Error', 'woocommerce-avatax' ) . '</strong> -';

		if ( wc_avatax()->get_tax_handler()->is_available() ) {

			// Set the effective tax date when a new order is placed
			add_action( 'woocommerce_checkout_order_processed', array( $this, 'set_checkout_order_meta' ) );

			// add addresses to order line items as they're created at checkout
			add_action( 'woocommerce_checkout_create_order_line_item', array( $this, 'add_new_order_item_addresses' ), 10, 2 );

			// add fee item meta
			add_action( 'woocommerce_checkout_create_order_fee_item', array( $this, 'add_new_order_fee_meta' ), 10, 3 );

			// set proper tax rate labels for new orders
			add_action( 'woocommerce_checkout_create_order_tax_item', array( $this, 'set_tax_item_labels' ), 10, 2 );

			// ensure AvaTax tax rate info is preserved after taxes are updated
			add_action( 'woocommerce_before_order_item_object_save', [ $this, 'maybe_restore_tax_item_properties' ] );

			// Calculate order taxes and send to Avalara tax when payment is complete
			add_action( 'woocommerce_payment_complete', array( $this, 'process_paid_order' ) );

			// Also calculate and send on order status change for gateways that don't call WC_Order::payment_complete
			add_action( 'woocommerce_order_status_on-hold_to_processing', array( $this, 'process_paid_order' ) );
			add_action( 'woocommerce_order_status_on-hold_to_completed',  array( $this, 'process_paid_order' ) );
			add_action( 'woocommerce_order_status_failed_to_processing',  array( $this, 'process_paid_order' ) );
			add_action( 'woocommerce_order_status_failed_to_completed',   array( $this, 'process_paid_order' ) );

			// add tax data to order items after manual calculation
			add_action( 'woocommerce_order_item_after_calculate_taxes',          array( $this, 'add_order_item_taxes' ) );
			add_action( 'woocommerce_order_item_shipping_after_calculate_taxes', array( $this, 'add_order_item_taxes' ) );

			// Calculate order taxes and send to Avalara manually through the admin action
			add_action( 'woocommerce_order_action_wc_avatax_send', array( $this, 'process_order' ) );

			// Void an order's Avalara document when cancelled
			add_action( 'woocommerce_order_status_cancelled', array( $this, 'void_order' ) );
		}
	}


	/**
	 * Set the effective tax date based on the order date.
	 *
	 * @since 1.0.0
	 * @param int $order_id The order ID
	 */
	public function set_checkout_order_meta( $order_id ) {

		$order    = wc_get_order( $order_id );
		$tax_data = WC()->cart->get_cart_contents_taxes();

		// if the cart has tax data, then tax was successfully estimated at checkout
		if ( $order && ! empty( $tax_data ) ) {

			update_post_meta( $order_id, '_wc_avatax_tax_calculated', 'yes' );

			if ( $date_created = $order->get_date_created( 'edit' ) ) {
				update_post_meta( $order_id, '_wc_avatax_tax_date', $date_created->date( 'Y-m-d' ) );
			}
		}

		// reset the address validated flag for future orders
		WC()->session->set( 'wc_avatax_address_validated', false );
	}


	/**
	 * Adds line item address data for new orders.
	 *
	 * @internal
	 *
	 * @since 1.5.0
	 *
	 * @param \WC_Order_Item $item item object
	 * @param string $cart_item_key cart index key
	 */
	public function add_new_order_item_addresses( $item, $cart_item_key ) {

		$session_addresses = WC()->session->get( 'wc_avatax_line_addresses', array() );

		if ( ! empty( $session_addresses[ $cart_item_key ]['origin'] ) ) {
			$item->add_meta_data( '_wc_avatax_origin_address', $session_addresses[ $cart_item_key ]['origin'] );
		}

		if ( ! empty( $session_addresses[ $cart_item_key ]['destination'] ) ) {
			$item->add_meta_data( '_wc_avatax_destination_address', $session_addresses[ $cart_item_key ]['destination'] );
		}
	}


	/**
	 * Adds fee item meta for new orders.
	 *
	 * @internal
	 *
	 * @since 1.5.0
	 *
	 * @param \WC_Order_Item_Fee $item item object
	 * @param int $fee_key cart fee key
	 * @param object $fee fee object
	 */
	public function add_new_order_fee_meta( $item, $fee_key, $fee ) {

		if ( Framework\SV_WC_Helper::str_starts_with( $fee->id, 'avatax-' ) ) {
			$item->add_meta_data( '_wc_avatax_source', 'avatax' );
		}
	}


	/**
	 * Sets proper tax rate labels for new orders.
	 *
	 * @internal
	 *
	 * @since 1.5.0
	 *
	 * @param \WC_Order_Item_Tax $item order tax item object
	 * @param string $tax_rate_code rate code
	 * @throws \WC_Data_Exception
	 */
	public function set_tax_item_labels( $item, $tax_rate_code ) {

		if ( ! empty( WC()->cart->avatax_rates ) ) {

			foreach ( WC()->cart->avatax_rates as $avatax_line_rates ) {

				if ( isset( $avatax_line_rates[ $tax_rate_code ] ) ) {

					$item->set_label( $avatax_line_rates[ $tax_rate_code ]->get_label() );
					break;
				}
			}
		}
	}


	/**
	 * Restores the value of the properties of an Order Tax Item before saving they are saved to the database.
	 *
	 * {@see \WC_Order::update_taxes() can accidentally overwrite the properties of AvaTax tax items with empty values.
	 * This method prevents information for AvaTAx tax items from being accidendatally erased.
	 *
	 * @internal
	 *
	 * @since 1.13.0
	 *
	 * @param \WC_Order_Item $item
	 */
	public function maybe_restore_tax_item_properties( $item ) {

		if ( $this->should_restore_tax_item_properties( $item ) ) {
			$this->restore_tax_item_properties( $item );
		}
	}


	/**
	 * Determines whether we should restore the value for the properties of the given item.
	 *
	 * @since 1.13.0
	 *
	 * @param WC_Order_Item_Tax $item tax item
	 *
	 * @return bool
	 */
	protected function should_restore_tax_item_properties( $item ) {

		if ( ! $item instanceof \WC_Order_Item_Tax ) {
			return false;
		}

		if ( ! isset( WC()->countries ) || ! is_callable( [ WC()->countries, 'tax_or_vat' ] ) ) {
			return false;
		}

		$changes = $item->get_changes();
		$data    = $item->get_data();

		// proceed only if the currently stored code starts with AvaTax's rate prefix
		if ( ! is_array( $changes ) || ! is_array( $data ) || ! isset( $data['rate_code'] ) || ! Framework\SV_WC_Helper::str_starts_with( $data['rate_code'], \WC_AvaTax_Tax_Handler::RATE_PREFIX ) ) {
			return false;
		}

		// proceed only if the label is being changed to the generic label
		// such change indicates that WooCommerce accidentally reset the properties of an AvaTax Tax Rate Item
		if ( empty( $data['label'] ) || empty( $changes['label'] ) || WC()->countries->tax_or_vat() !== $changes['label'] ) {
			return false;
		}

		return true;
	}


	/**
	 * Prevents WooCommerce from setting default values for the properties of tax items that represent AvaTax tax items.
	 *
	 * {@see \WC_Order::update_taxes() sets the values using the value returned by {@see \WC_Tax::get_rate_label()},
	 * and other methods that rely on tax rate information stored in the database, which doesn't exist for AvaTax
	 * tax items.
	 *
	 * @since 1.13.0
	 *
	 * @param \WC_Order_Item_Tax $item
	 */
	protected function restore_tax_item_properties( \WC_Order_Item_Tax $item ) {

		$changes = $item->get_changes();
		$data    = $item->get_data();

		if ( isset( $changes['rate_code'] ) && ! empty( $data['rate_code'] ) ) {
			$item->set_rate_code( $data['rate_code'] );
		}

		if ( isset( $changes['label'] ) && ! empty( $data['label'] ) ) {
			$item->set_label( $data['label'] );
		}

		if ( isset( $changes['compound'], $data['compound'] ) ) {
			$item->set_compound( $data['compound'] );
		}

		if ( isset( $changes['rate_percent'], $data['rate_percent'] ) ) {
			$item->set_rate_percent( $data['rate_percent'] );
		}

		if ( isset( $changes['tax_total'], $data['tax_total'] ) ) {
			$item->set_tax_total( $data['tax_total'] );
		}

		if ( isset( $changes['shipping_tax_total'], $data['shipping_tax_total'] ) ) {
			$item->set_shipping_tax_total( $data['shipping_tax_total'] );
		}
	}


	/**
	 * Calculate order taxes and send to Avalara tax when payment is complete.
	 *
	 * @since 1.0.0
	 * @param WC_Order $order The order object.
	 */
	public function process_paid_order( $order ) {

		if ( ! $order instanceof WC_Order ) {
			$order = wc_get_order( $order );
		}

		if ( ! $order ) {
			return;
		}

		/**
		 * Filters whether an order should have its tax calculation recorded permanently in Avalara.
		 *
		 * @since 1.6.4
		 *
		 * @param bool $record whether an order should have its tax calculation recorded permanently in Avalara
		 * @param \WC_Order $order WooCommerce order object
		 */
		$record_order = (bool) apply_filters( 'wc_avatax_record_order_calculation', $this->record_calculations(), $order );

		// mark the order and bail if recording calculations is disabled
		if ( ! $record_order ) {

			$message  = '<strong>' . __( 'Order not sent to Avalara.', 'woocommerce-avatax' ) . '</strong> ';
			$message .= ! $this->record_calculations() ? __( 'AvaTax is configured to not record permanent calculations.', 'woocommerce-avatax' ) : __( 'Permanent calculations were disabled for this order.', 'woocommerce-avatax' );
			$message .= ' ' .__( 'Please add the order manually from your Avalara Control Panel.', 'woocommerce-avatax' );

			$order->add_order_note( $message );

			return;
		}

		// If tax was never calculated for the order (manually or at checkout), bail
		if ( ! get_post_meta( $order->get_id(), '_wc_avatax_tax_calculated', true ) ) {
			return;
		}

		// Calculate the order taxes and send a document to Avalara
		$this->process_order( $order );
	}


	/**
	 * Calculate order taxes and send to Avalara.
	 *
	 * @since 1.0.0
	 * @param WC_Order $order The order object.
	 * @return \WC_Order|bool $order The processed order or false on failure.
	 */
	public function process_order( WC_Order $order ) {

		// If this order has already been sent to Avalara, bail
		if ( $this->is_order_posted( $order ) || ! $this->is_order_taxable( $order ) ) {
			return false;
		}

		/**
		 * Fire before processing tax for an order.
		 *
		 * @since 1.0.0
		 * @param int $order_id The order ID.
		 */
		do_action( 'wc_avatax_before_order_processed', $order->get_id() );

		// Attempt the calculation
		$result = $this->calculate_order_tax( $order, true );

		// If failed, update the order accordingly
		if ( $result instanceof Framework\SV_WC_API_Exception ) {

			$this->add_status( $order, 'error' );

			$order->add_order_note(
				/* translators: Placeholders: %1$s - error indicator, %2$s - error message */
				sprintf( __( '%1$s Order could not be sent. %2$s', 'woocommerce-avatax' ),
					$this->error_prefix,
					$result->getMessage()
				)
			);

			/**
			 * Fire if an order failed to send to Avalara.
			 *
			 * @since 1.0.0
			 * @param int $order_id The order ID
			 */
			do_action( 'wc_avatax_order_failed', $order->get_id() );

		// Otherwise, continue processing
		} elseif ( $result instanceof WC_Order ) {

			// Remove any error status if it exists
			$this->remove_status( $order, 'error' );

			// Let the world know: this order has been posted to Avalara
			$this->add_status( $order, 'posted' );

			$order->add_order_note( __( 'Order sent to Avalara.', 'woocommerce-avatax' ), 0, doing_action( 'woocommerce_order_action_wc_avatax_send' ) );

			/**
			 * Fire when an order is sent to Avalara.
			 *
			 * @since 1.0.0
			 * @param int $order_id The order ID
			 */
			do_action( 'wc_avatax_order_processed', $order->get_id() );

			return $order;
		}
	}


	/**
	 * Estimates tax for an order.
	 *
	 * @since 1.5.0
	 *
	 * @param \WC_Order $order order object
	 *
	 * @return \WC_Order|Framework\SV_WC_API_Exception $order order object or an exception on failure
	 */
	public function estimate_tax( WC_Order $order ) {

		return $this->calculate_order_tax( $order, false, true );
	}


	/**
	 * Calculate and update taxes for an order.
	 *
	 * By default, this calculation is invisible to Avatax. If you want to record this transaction
	 * as an Avalara document you can set the `$commit` param to `true`.
	 *
	 * @since 1.0.0
	 *
	 * @param WC_Order $order The order object.
	 * @param bool $commit Whether to commit the transaction to Avalara
	 * @param bool $update_item_taxes whether the order items should store the returned tax values
	 *
	 * @return \WC_Order|Framework\SV_WC_API_Exception $order The processed order or an exception on failure
	 */
	public function calculate_order_tax( WC_Order $order, $commit = false, $update_item_taxes = false ) {

		try {

			/**
			 * Fire before calculating tax for an order.
			 *
			 * @since 1.0.0
			 * @param int $order_id The order ID.
			 */
			do_action( 'wc_avatax_before_order_tax_calculated', $order->get_id() );

			// Call the API
			$response = wc_avatax()->get_api()->calculate_order_tax( $order, $commit );

			// cache the response for use in later hooks if needed
			$this->calculated_order_taxes[ $order->get_id() ] = $response;

			$this->update_item_data( $order, $response->get_lines() );

			// always update the shipping items
			$this->update_shipping_item_taxes( $order, $response->get_shipping_lines() );

			// maybe update the tax data
			if ( $update_item_taxes ) {
				$order = $this->update_item_taxes( $order, $response );
			}

			// saves the overall tax transaction data to the order
			$this->store_tax_data( $order, $response );

			/**
			 * Fire after calculating tax for an order.
			 *
			 * @since 1.0.0
			 * @param int $order_id The order ID.
			 */
			do_action( 'wc_avatax_after_order_tax_calculated', $order->get_id(), $response );

			return $order;

		} catch ( \Exception $e ) {

			if ( wc_avatax()->logging_enabled() ) {
				wc_avatax()->log( $e->getMessage() );
			}

			return new Framework\SV_WC_API_Exception( $e->getMessage() );
		}
	}


	/**
	 * Adds the AvaTax tax data to order items when order taxes are recalculated.
	 *
	 * @internal
	 *
	 * @since 1.5.1
	 *
	 * @param \WC_Order_Item $item order item
	 * @param \WC_AvaTax_API_Tax_Response|null AvaTax API response
	 */
	public function add_order_item_taxes( $item, $response = null ) {

		$order_id = $item->get_order_id();

		// try and retrieve any cached tax data from a previous calculation
		if ( ! $response && ! empty( $this->calculated_order_taxes[ $order_id ] ) ) {
			$response = $this->calculated_order_taxes[ $order_id ];
		}

		$order = wc_get_order( $order_id );

		// sanity check for the order object and valid tax API response data
		if ( ! $order || ! $response instanceof WC_AvaTax_API_Tax_Response ) {
			return;
		}

		$lines      = array_merge( $response->get_cart_lines(), $response->get_fee_lines(), $response->get_shipping_lines() );
		$line_ids   = wp_list_pluck( $lines, 'id' );
		$line_index = array_search( $item->get_id(), $line_ids, false );

		if ( false !== $line_index ) {

			$item_rates = $item->get_taxes();

			foreach ( $lines[ $line_index ]['rates'] as $code => $rate ) {
				$item_rates['total'][ $code ]    = $rate->get_total();
				$item_rates['subtotal'][ $code ] = $rate->get_total();
			}

			$item->set_taxes( $item_rates );

			$this->update_tax_totals( $order, $lines );
		}
	}


	/**
	 * Stores AvaTax line data like tax code & addresses to the order's items.
	 *
	 * @since 1.5.0
	 *
	 * @param \WC_Order $order order object
	 * @param array $lines response lines
	 * @return \WC_Order $order order object
	 * @throws \Exception
	 */
	protected function update_item_data( WC_Order $order, array $lines ) {

		foreach ( $lines as $line ) {

			$line_rate = 0;

			foreach ( $line['rates'] as $rate ) {
				$line_rate += $rate->get_rate();
			}

			$item_id = str_replace( array( 'fee_', 'shipping_' ), '', $line['id'] );

			wc_update_order_item_meta( $item_id, '_wc_avatax_code', wc_clean( $line['code'] ) );
			wc_update_order_item_meta( $item_id, '_wc_avatax_rate', (float) $line_rate );

			wc_update_order_item_meta( $item_id, '_wc_avatax_origin_address',      $line['origin'] );
			wc_update_order_item_meta( $item_id, '_wc_avatax_destination_address', $line['destination'] );
		}

		return $order;
	}


	/**
	 * Stores AvaTax rate data to an order's line items.
	 *
	 * This isn't needed for regular checkout orders since that data gets set
	 * based on the data already available in the cart object. However, when tax
	 * is calculated manually via the admin or for renewal orders, we need to
	 * store the results.
	 *
	 * @since 1.5.0
	 *
	 * @param \WC_Order $order order object
	 * @param \WC_AvaTax_API_Tax_Response $response tax transaction response object
	 * @return \WC_Order $order order object
	 * @throws \Exception
	 */
	protected function update_item_taxes( WC_Order $order, WC_AvaTax_API_Tax_Response $response ) {

		$order = $this->update_line_item_taxes( $order, $response->get_lines() );
		$order = $this->update_tax_totals( $order, $response->get_lines() );

		return $order;
	}


	/**
	 * Updates an order's line & fee item taxes.
	 *
	 * @since 1.5.0
	 *
	 * @param \WC_Order $order order object
	 * @param array $lines response lines
	 * @return \WC_Order $order order object
	 * @throws \Exception
	 */
	protected function update_line_item_taxes( WC_Order $order, array $lines ) {

		foreach ( $lines as $line ) {

			// skip shipping lines
			if ( Framework\SV_WC_Helper::str_starts_with( $line['id'], 'shipping' ) ) {
				continue;
			}

			$item_id = str_replace( 'fee_', '', $line['id'] );

			$line_tax          = wc_get_order_item_meta( $item_id, '_line_tax' );
			$line_subtotal_tax = wc_get_order_item_meta( $item_id, '_line_subtotal_tax' );

			wc_update_order_item_meta( $item_id, '_line_tax', (float) $line_tax + (float) $line['tax'] );
			wc_update_order_item_meta( $item_id, '_line_subtotal_tax', (float) $line_subtotal_tax + (float) $line['tax'] );

			$taxes = wc_get_order_item_meta( $item_id, '_line_tax_data' );

			// sanity check to prevent PHP errors in the rare possibility the retrieved meta is not an array containing the keys accessed below
			if ( ! is_array( $taxes ) || empty( $taxes ) ) {
				$taxes = [ 'total' => [], 'subtotal' => [] ];
			}

			foreach ( $line['rates'] as $code => $rate ) {
				// use $code from rates array keys instead of value from $rate->get_code() to handle possible iterations of rates with the same name
				$taxes['total'][ $code ]    = $rate->get_total();
				$taxes['subtotal'][ $code ] = $rate->get_total();
			}

			wc_update_order_item_meta( $item_id, '_line_tax_data', $taxes );
		}

		return $order;
	}


	/**
	 * Updates an order's shipping item taxes.
	 *
	 * @since 1.5.0
	 *
	 * @param \WC_Order $order order object
	 * @param array $lines response lines
	 * @return \WC_Order $order order object
	 * @throws \Exception
	 */
	protected function update_shipping_item_taxes( WC_Order $order, array $lines ) {

		foreach ( $lines as $line ) {

			$item_id = str_replace( 'shipping_', '', $line['id'] );

			$taxes          = array();
			$existing_taxes = wc_get_order_item_meta( $item_id, 'taxes' );

			// can't do a strict WC 3.0+ check since subscription renewals could
			// still have the 2.6 tax data format
			if ( isset( $existing_taxes['total'] ) ) {
				$existing_taxes = $existing_taxes['total'];
			}

			foreach ( $line['rates'] as $rate ) {

				if ( isset( $taxes[ $rate->get_code() ] ) ) {
					$taxes[ $rate->get_code() ] += $rate->get_total();
				} else {
					$taxes[ $rate->get_code() ] = $rate->get_total();
				}
			}

			// we cannot use array_merge() here
			// WC core rates use the numeric rate ID as the index, so any core
			// rates would be re-indexed and no longer point to the correct rate ID
			$taxes = $taxes + $existing_taxes;

			// use the updated format for WC 3.0+
			$taxes = array(
				'total' => $taxes,
			);

			$line_tax = wc_get_order_item_meta( $item_id, 'total_tax' );

			wc_update_order_item_meta( $item_id, 'total_tax', $line_tax + $line['tax'] );

			wc_update_order_item_meta( $item_id, 'taxes', $taxes );
		}

		return $order;
	}


	/**
	 * Updates the tax totals for an order.
	 *
	 * @since 1.5.0
	 *
	 * @param \WC_Order $order order object
	 * @param array $lines response lines
	 * @return \WC_Order $order order object
	 * @throws \WC_Data_Exception
	 */
	protected function update_tax_totals( WC_Order $order, array $lines ) {

		$order = $this->remove_taxes( $order );

		$taxes_labels   = [];
		$cart_taxes     = [];
		$shipping_taxes = [];

		foreach ( $lines as $line ) {

			foreach ( $line['rates'] as $code => $rate ) {

				$taxes_labels[ $code ] = $rate->get_label();

				if ( Framework\SV_WC_Helper::str_starts_with( $line['id'], 'shipping_' ) ) {

					if ( isset( $shipping_taxes[ $code ] ) ) {
						$shipping_taxes[ $code ] += $rate->get_total();
					} else {
						$shipping_taxes[ $code ] = $rate->get_total();
					}

				} else {

					if ( isset( $cart_taxes[ $code ] ) ) {
						$cart_taxes[ $code ] += $rate->get_total();
					} else {
						$cart_taxes[ $code ] = $rate->get_total();
					}
				}
			}
		}

		// add the tax line items
		// we cannot use array_merge() here
		// WC core rates use the numeric rate ID as the index, so any core
		// rates would be re-indexed and no longer point to the correct rate ID
		foreach ( array_keys( $cart_taxes + $shipping_taxes ) as $code ) {

			$item = new WC_Order_Item_Tax();

			$item->set_rate_code( $code );
			$item->set_tax_total( isset( $cart_taxes[ $code ] ) ? $cart_taxes[ $code ] : 0 );
			$item->set_shipping_tax_total( ! empty( $shipping_taxes[ $code ] ) ? $shipping_taxes[ $code ] : 0 );

			if ( isset( $taxes_labels[ $code ] ) ) {
				$item->set_label( $taxes_labels[ $code ] );
			}

			$order->add_item( $item );
		}

		$order->set_shipping_tax( WC_Tax::round( array_sum( $shipping_taxes ) ) );
		$order->set_cart_tax( WC_Tax::round( array_sum( $cart_taxes ) ) );

		$order->calculate_totals( false );

		return $order;
	}


	/**
	 * Removes tax totals from an order.
	 *
	 * @since 1.5.0
	 *
	 * @param \WC_Order $order order object
	 * @return \WC_Order $order order object
	 */
	protected function remove_taxes( WC_Order $order ) {

		foreach ( $order->get_taxes() as $tax_item ) {
			$order->remove_item( $tax_item->get_id() );
		}

		return $order;
	}


	/**
	 * Stores AvaTax transaction data for an order.
	 *
	 * This ensures the original tax calculation details are available in case
	 * of a refund down the road, instead of pulling from the settings which
	 * may have changed.
	 *
	 * @since 1.5.0
	 *
	 * @param \WC_Order $order order object
	 * @param \WC_AvaTax_API_Tax_Response $response tax transaction response object
	 */
	protected function store_tax_data( WC_Order $order, WC_AvaTax_API_Tax_Response $response ) {

		// save the effective tax date
		update_post_meta( $order->get_id(), '_wc_avatax_tax_date', $response->get_tax_date() );

		// save the calculated addresses as order meta in case refund calculation is needed
		update_post_meta( $order->get_id(), '_wc_avatax_origin_address', $response->get_origin_address() );
		update_post_meta( $order->get_id(), '_wc_avatax_destination_address', $response->get_destination_address() );

		// save the customer use code, if any
		update_post_meta( $order->get_id(), '_wc_avatax_exemption', get_user_meta( $order->get_user_id(), 'wc_avatax_tax_exemption', true ) );

		// tax has been calculated
		update_post_meta( $order->get_id(), '_wc_avatax_tax_calculated', 'yes' );

		// mark the order as having landed cost when there are AvaTax fees present
		if ( wc_avatax()->get_landed_cost_handler()->is_available() ) {

			$is_landed_cost = false;

			foreach ( $order->get_fees() as $fee ) {

				if ( $fee instanceof WC_Order_Item_Fee ) {
					$source = $fee->get_meta( '_wc_avatax_source' );
				} else {
					$source = isset( $fee['wc_avatax_source'] ) ? $fee['wc_avatax_source'] : '';
				}

				if ( 'avatax' === $source ) {
					$is_landed_cost = true;
				}
			}

			if ( $is_landed_cost ) {
				$order->update_meta_data( '_wc_avatax_landed_cost', 'yes' );
				$order->save_meta_data();
			}
		}
	}


	/**
	 * Calculate refund taxes and send to Avalara.
	 *
	 * @since 1.0.0
	 *
	 * @param \WC_Order_Refund $refund The order refund object.
	 */
	public function process_refund( WC_Order_Refund $refund ) {

		$order = wc_get_order( $refund->get_parent_id( 'edit' ) );

		if ( ! $order ) {
			return;
		}

		// if this is a full refund, ignore line items and just cancel the tax document
		if ( (float) $refund->get_total() === (float) $order->get_total() ) {
			$this->void_order( $order->get_id() );
		}

		try {

			/**
			 * Fire before processing tax for a refund.
			 *
			 * @since 1.0.0
			 * @param int $refund_id The refund ID.
			 */
			do_action( 'wc_avatax_before_refund_processed', $refund->get_id() );

			// Make the call
			wc_avatax()->get_api()->calculate_refund_tax( $refund );

			// Add the refunded status to the original order
			$this->add_status( $order, 'refunded' );

			$order->add_order_note( sprintf( __( 'Refund #%s sent to Avalara.', 'woocommerce-avatax' ), $refund->get_id() ) );

			/**
			 * Fire after processing tax for a refund.
			 *
			 * @since 1.0.0
			 * @param int $refund_id The refund ID.
			 */
			do_action( 'wc_avatax_after_refund_processed', $refund->get_id() );

		} catch ( Framework\SV_WC_API_Exception $e ) {

			if ( wc_avatax()->logging_enabled() ) {
				wc_avatax()->log( $e->getMessage() );
			}

			$this->add_status( $order, 'error' );

			$order->add_order_note(
				/* translators: Placeholders: %1$s - error indicator, %2$s - error message */
				sprintf( __( '%1$s Refund could not be sent. %2$s Please add the refund manually from your Avalara Control Panel.', 'woocommerce-avatax' ),
					$this->error_prefix,
					$e->getMessage()
				)
			);
		}
	}


	/**
	 * Void an order's Avalara document.
	 *
	 * @since 1.0.0
	 * @param int $order_id The order ID.
	 */
	public function void_order( $order_id ) {

		// If the order has already been voided, bail
		if ( $this->is_order_voided( $order_id ) || ! $this->is_order_posted( $order_id ) ) {
			return;
		}

		$order = wc_get_order( $order_id );

		if ( ! $order ) {
			return;
		}

		try {

			/**
			 * Fire before voiding tax for an order.
			 *
			 * @since 1.0.0
			 * @param int $order_id The order ID.
			 */
			do_action( 'wc_avatax_before_order_voided', $order_id );

			$response = wc_avatax()->get_api()->void_order( $order_id );

			// make sure the response isn't empty
			if ( ! empty ( $response->response_data ) ) {

				$this->add_status( $order_id, 'voided' );

				$order->add_order_note( __( 'Order voided in Avalara.', 'woocommerce-avatax' ) );

				/**
				 * Fire after voiding tax for an order.
				 *
				 * @since 1.0.0
				 * @param int $order_id The order ID.
				 */
				do_action( 'wc_avatax_after_order_voided', $order_id );

			} else {

				// if the response is empty, throw new error
				throw new Framework\SV_WC_API_Exception ( 'Invalid response data from Avalara.' );
			}

		} catch ( Framework\SV_WC_API_Exception $e ) {

			if ( wc_avatax()->logging_enabled() ) {
				wc_avatax()->log( $e->getMessage() );
			}

			$this->add_status( $order_id, 'error' );

			$order->add_order_note(
				/* translators: Placeholders: %1$s - error indicator, %2$s - error message */
				sprintf( __( '%1$s Order could not be voided. %2$s Please void manually from your Avalara Control Panel.', 'woocommerce-avatax' ),
					$this->error_prefix,
					$e->getMessage()
				)
			);
		}
	}


	/**
	 * Determines if an order is taxable based on its addresses.
	 *
	 * @since 1.6.1
	 *
	 * @param \WC_Abstract_Order $order order or refund object
	 * @return bool
	 */
	public function is_order_taxable( WC_Abstract_Order $order ) {

		$taxable_address = $this->get_taxable_address( $order );

		/**
		 * Filters whether an order is taxable.
		 *
		 * @since 1.6.1
		 *
		 * @param bool $taxable whether the order is taxable
		 * @param \WC_Abstract_Order $order order or refund object
		 */
		return (bool) apply_filters( 'wc_avatax_is_order_taxable', wc_avatax()->get_tax_handler()->is_location_taxable( $taxable_address[0], $taxable_address[1] ), $order );
	}


	/**
	 * Gets the taxable address for an order.
	 *
	 * We have no session here, so we need to do a bit of duplication of WC_Customer::get_taxable_address().
	 *
	 * @since 1.6.1
	 *
	 * @param \WC_Abstract_Order $order order or refund object
	 * @return string[] taxable address
	 */
	public function get_taxable_address( WC_Abstract_Order $order ) {

		$tax_based_on = get_option( 'woocommerce_tax_based_on', '' );

		if ( 'base' === $tax_based_on ) {

			$country  = WC()->countries->get_base_country();
			$state    = WC()->countries->get_base_state();
			$postcode = WC()->countries->get_base_postcode();
			$city     = WC()->countries->get_base_city();

		} elseif ( 'shipping' === $tax_based_on && $order->has_shipping_address() ) {

			$country  = $order->get_shipping_country( 'edit' );
			$state    = $order->get_shipping_state( 'edit' );
			$postcode = $order->get_shipping_postcode( 'edit' );
			$city     = $order->get_shipping_city( 'edit' );

		} else {

			$country  = $order->get_billing_country( 'edit' );
			$state    = $order->get_billing_state( 'edit' );
			$postcode = $order->get_billing_postcode( 'edit' );
			$city     = $order->get_billing_city( 'edit' );
		}

		/* this filter is documented in woocommerce/includes/class-wc-customer.php */
		return apply_filters( 'woocommerce_customer_taxable_address', array( $country, $state, $postcode, $city ) );
	}


	/**
	 * Add an AvaTax status to an order.
	 *
	 * @since 1.0.0
	 * @param \WC_Order|int $order The order object or ID.
	 * @param string $status The AvaTax status to add.
	 * @return int|false The resulting meta ID on success, false on failure.
	 */
	public function add_status( $order, $status ) {

		if ( is_numeric( $order ) ) {
			$order = wc_get_order( $order );
		}

		// Add the status if it doesn't already exist
		if ( ! $this->order_has_status( $order, $status ) ) {
			return add_post_meta( $order->get_id(), '_wc_avatax_status', $status );
		} else {
			return false;
		}
	}


	/**
	 * Remove an AvaTax status from an order.
	 *
	 * @since 1.0.0
	 * @param \WC_Order|int $order The order object or ID.
	 * @param string $status The AvaTax status to remove.
	 * @return bool
	 */
	public function remove_status( $order, $status ) {

		return delete_post_meta( $order->get_id(), '_wc_avatax_status', $status );
	}


	/**
	 * Determine if an order has already been posted to AvaTax.
	 *
	 * @since 1.0.0
	 * @param \WC_Order|int $order The order object or ID.
	 * @return bool Whether the order has already been posted to AvaTax.
	 */
	public function is_order_posted( $order ) {

		if ( is_numeric( $order ) ) {
			$order = wc_get_order( $order );
		}

		return ( $this->order_has_status( $order, 'posted' ) );
	}


	/**
	 * Determine if an order's refund has been posted to AvaTax.
	 *
	 * @since 1.0.0
	 * @param \WC_Order|int $order The order object or ID.
	 * @return bool Whether the order's refund has been posted to AvaTax.
	 */
	public function is_order_refunded( $order ) {

		if ( is_numeric( $order ) ) {
			$order = wc_get_order( $order );
		}

		return ( $this->order_has_status( $order, 'refunded' ) );
	}


	/**
	 * Determine if an order has been voided in AvaTax.
	 *
	 * @since 1.0.0
	 * @param \WC_Order|int $order The order object or ID.
	 * @return bool Whether the order has been voided in AvaTax.
	 */
	public function is_order_voided( $order ) {

		if ( is_numeric( $order ) ) {
			$order = wc_get_order( $order );
		}

		return ( $this->order_has_status( $order, 'voided' ) );
	}


	/**
	 * Determine if an order has a specific AvaTax status.
	 *
	 * @since 1.0.0
	 * @param \WC_Order|int $order The order object or ID.
	 * @param string $status Optional. The AvaTax status to check. If none set, it checks if any
	 *                       status is set.
	 * @return bool Whether the order has the specific status.
	 */
	public function order_has_status( $order, $status = '' ) {

		if ( is_numeric( $order ) ) {
			$order = wc_get_order( $order );
		}

		$statuses = $this->get_order_statuses( $order );

		// Check for any status if no specific status is passed
		if ( ! $status ) {
			return ! empty( $statuses );
		}

		return in_array( $status, $statuses );
	}


	/**
	 * Get the statuses of an order when last posted to AvaTax.
	 *
	 * Orders can have multiple statuses, like `posted` and 'refunded'.
	 *
	 * @since 1.0.0
	 * @param \WC_Order|int $order The order object or ID.
	 * @return array The order's AvaTax statuses.
	 */
	public function get_order_statuses( $order ) {

		if ( is_numeric( $order ) ) {
			$order = wc_get_order( $order );
		}

		$statuses = get_post_meta( $order->get_id(), '_wc_avatax_status' );

		if ( ! $statuses ) {
			$statuses = array();
		}

		return $statuses;
	}


	/**
	 * Determine if an order is ready to be sent to AvaTax.
	 *
	 * The primary factor is if the order has a status that identifies it as "paid".
	 *
	 * @since 1.0.0
	 * @param WC_Order $order The order object
	 * @return bool Whether the order is ready to be sent to AvaTax.
	 */
	public function is_order_ready( WC_Order $order ) {

		// Assume it's not ready
		$is_ready = false;

		// Only continue checking if the order hasn't already been sent to AvaTax
		if ( ! $this->is_order_posted( $order ) ) {

			$status = $order->get_status();

			/**
			 * Filter the order statuses that allow manual order sending.
			 *
			 * @since 1.0.0
			 * @param array $ready_statuses The valid statuses.
			 */
			$ready_statuses = apply_filters( 'wc_avatax_order_ready_statuses', array(
				'on-hold',
				'processing',
				'completed',
			) );

			// See if the order has one of the ready statuses
			$is_ready = in_array( $status, $ready_statuses );

			// If not, and Order Status Manager is active, then check the status' paid property
			if ( class_exists( 'WC_Order_Status_Manager_Order_Status' ) && ! $is_ready ) {

				$status = new WC_Order_Status_Manager_Order_Status( $status );

				$is_ready = ( $status->get_id() > 0 && ! $status->is_core_status() && $status->is_paid() );
			}
		}

		/**
		 * Filter whether an order is ready to be sent to AvaTax.
		 *
		 * @since 1.0.0
		 * @param bool $is_ready
		 * @param int $order_id The order ID
		 */
		return apply_filters( 'wc_avatax_order_is_ready', $is_ready, $order->get_id() );
	}


	/**
	 * Determines whether tax calculation for new orders should be recorded permanently in Avalara.
	 *
	 * If disabled, taxes will still be calculated at checkout but won't result
	 * in a final permanent transaction on the Avalara side.
	 *
	 * This can be overridden on an order-by-order basis using the 'wc_avatax_record_order' filter.
	 *
	 * @since 1.6.4
	 *
	 * @return bool
	 */
	public function record_calculations() {

		/**
		 * Filters whether tax calculation for new orders should be recorded permanently in Avalara.
		 *
		 * @since 1.6.4
		 *
		 * @param bool $record whether tax calculation for new orders should be recorded permanently in Avalara
		 */
		return (bool) apply_filters( 'wc_avatax_record_calculations', 'yes' === get_option( 'wc_avatax_record_calculations', 'yes' ) );
	}


}
