<?php
/*
Plugin Name: Woocommerce Ebizcharge Gateway
Plugin URI: https://www.ebizcharge.com/woocommerce-payment-integration
Description: Accept all major credit cards directly on your WooCommerce site in a seamless and secure checkout environment with Ebizcharge.
Version: 3.9.2
Author: EBizCharge
Author URI: http://ebizcharge.com
License: Apache 2
*/
if ( !defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

add_action( 'plugins_loaded', 'woocommerce_ebizcharge_commerce_init', 0 );
register_activation_hook( __FILE__,  'econnect_install' );

$GLOBALS[ 'action_counter' ] = 0;
$GLOBALS[ 'completecapture' ] = 'no';

function woocommerce_ebizcharge_commerce_init() {

	if ( !class_exists( 'WC_Payment_Gateway' ) ) {
		return;
	};

	if ( !class_exists( 'Backwards_Compatible_Order' ) ) {
		include_once 'includes/class-wc-gateway-ebizcharge-migration-helper.php';
	}

	DEFINE( 'PLUGIN_DIR', plugins_url( basename( plugin_dir_path( __FILE__ ) ), basename( __FILE__ ) ) . '/' );

	/**
	 * ebizcharge Commerce Gateway Class
	 */
	class WC_ebizcharge extends WC_Payment_Gateway {
		public $cvv = 'yes';
		public $_paymentType = array( 'sale' => 'Authorize &amp; Capture', 'authonly' => 'Authorize Only' );

		public function __construct() {

			$this->id = 'ebizcharge';
			$this->method_title = __( 'EBizCharge', 'wc-ebizcharge' );
			$this->method_description = __( 'EBizCharge allows customers to checkout using a credit card', 'wc-ebizcharge' );
			$this->icon = PLUGIN_DIR . 'assets/images/cards.png';
			$this->has_fields = true;
			$this->supports = array(
				'products',
				'subscriptions',
				'subscription_cancellation',
				'subscription_suspension',
				'subscription_reactivation',
				'subscription_date_changes',
				'refunds',
			);

			// Load the form fields
			$this->init_form_fields();

			// Load the settings.
			$this->init_settings();
			$GLOBALS[ 'completecapture' ] = $this->settings[ 'completecapture' ];

			foreach ( $this->settings as $key => $val ) {
				$this->$key = $val;
			}

			include_once( 'includes/class-wc-gateway-ebizcharge-admin.php' );

			if ( $GLOBALS[ 'action_counter' ] == 0 ) {

				add_action( 'admin_notices', array( & $this, 'ebiz_commerce_ssl_check' ) );
				add_action( 'woocommerce_before_my_account', array( $this, 'add_payment_method_options' ) );
				add_action( 'woocommerce_process_refund', array( $this, 'process_refund' ) );
				add_action('wp_ajax_ebiz_sync_action', array($this, 'sync_actions'));
				add_action( 'woocommerce_receipt_ebizcharge', array( & $this, 'receipt_page' ) );
				if ( version_compare( WOOCOMMERCE_VERSION, '2.0.0', '>=' ) ) {
					add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( & $this, 'process_admin_options' ) );
				} else {
					add_action( 'woocommerce_update_options_payment_gateways', array( & $this, 'process_admin_options' ) );
				}
				add_action( 'wp_enqueue_scripts', array( & $this, 'add_ebizcharge_scripts' ) );

				$GLOBALS[ 'action_counter' ] = 1;
			}
		}

    /**
     * add Econnect sync options
     *
     * @access public
     * @return void
     */
    function econnect_sync_options()
    {
        global $woocommerce;

        $actions = array();

        $actions['syncCustomer'] = array(
                'button' => __('Sync Customers', 'woocommerce'),
                'class' => 'button button-primary'
            );
            $actions['syncItem'] = array(
                'button' => __('Sync Products', 'woocommerce'),
                'class' => 'button button-primary'
            );
            $actions['syncInvoice'] = array(
                'button' => __('Sync Invoices', 'woocommerce'),
                'class' => 'button button-primary'
            );
            $actions['syncOrder'] = array(
                'button' => __('Sync Orders', 'woocommerce'),
                'class' => 'button button-primary'
            );
            $actions['downloadCustomers'] = array(
                'button' => __('Download Customers', 'woocommerce'),
                'class' => 'button button-success'
            );

            $actions['downloadItems'] = array(
                'button' => __('Download Products', 'woocommerce'),
                'class' => 'button button-success'
            );

            $actions['downloadOrders'] = array(
                'button' => __('Download Orders', 'woocommerce'),
                'class' => 'button button-success'
            );

        $display = $this->enableEconnect == 'yes' ? '' : 'display:none';
        echo '<p id="econnect-buttons" class="buttons" style="'. $display. '">';

        foreach ($actions as $action_name => $action) {
            $class = $action['class'];
            if($action_name == 'downloadCustomers') {
                echo "</br></br>";
            }
            echo '<a href="#" class= '."'$class'".' data-action="' . $action_name . '" data-id="' . $action_name . '">' . $action['button'] . '</a> ';
        }
        echo '</p>';

        $js = "
            jQuery('#woocommerce_ebizcharge_enableEconnect').change(function() {
                jQuery('#econnect-buttons').toggle(this.checked);
            });

			jQuery('#econnect-buttons').on( 'click', 'a.button, a.refresh', function(){
				jQuery('#econnect-buttons').block(
				{ message: null, overlayCSS: { background: '#fff url(" . $woocommerce->plugin_url() . "/assets/images/ajax-loader.gif) no-repeat center', opacity: 0.6 } });

				var data = {
					action: 		'ebiz_sync_action',
					security: 		'" . wp_create_nonce("ebiz_sync_action") . "',
					ebiz_action: 	jQuery(this).data('action'),
					ebiz_id: 		jQuery(this).data('id'),
				};
				// Ajax action
				jQuery.ajax({
					url: '" . admin_url('admin-ajax.php') . "',
					data: data,
					type: 'POST',
					success: function( result ) {
						jQuery('#messages').html(result);
						jQuery('#econnect-buttons').unblock();
						jQuery('.notice-dismiss').click(function () {
                            jQuery(this).parent('div').hide();
                        });
					}
				});

				return false;
			});

		";

        if (function_exists('wc_enqueue_js')) {
            wc_enqueue_js($js);
        } else {
            $woocommerce->add_inline_js($js);
        }
    }

        /**
     * Perform sync actions for Econnect
     */
    public function sync_actions()
    {
        check_ajax_referer('ebiz_sync_action', 'security');

        $action = sanitize_title($_POST['ebiz_action']);

        $ebiz = new WC_ebizcharge();
        $tran = $ebiz->_initTransaction(true);

        $response = $tran->$action();
        echo $response; die;
    }

    /**
     * Process a manual refund in 'Admin - Edit Order' if supported.
     *
     * @param  int    $order_id Order ID.
     * @param  float  $amount Refund amount.
     * @param  string $reason Refund reason.
     * @return bool|WP_Error
     */
     public function process_refund( $order_id, $amount = null, $reason = '' ) {

            $order = (WC()->version < '2.7.0') ? new WC_Order($order_id) : new Backwards_Compatible_Order($order_id);
            $transaction_id = get_post_meta($order->get_id(), '_transaction_id', true);

            $paymentStatus = get_post_meta( $order->get_id(), '_payment_status', true );
            if($paymentStatus == 'Authorized') {
               return new WP_Error( 'error', __( 'The Authorized only payment cannot be refunded. Please capture the payment before refund.', 'woocommerce' ) );
            } else if($paymentStatus == 'Refunded') {
               return new WP_Error( 'error', __( 'The payment status is Refunded. You cannot refund anymore.', 'woocommerce' ) );
           }

            if ($order && $transaction_id && $amount > 0) {
                $tran = $this->_initTransaction();
                $tran->refnum = $transaction_id;
                $tran->amount = $amount;
                $tran->command = 'refund';
                $tran->orderid = $order_id;
                $tran->invoice = $order_id;
                $tran->ponum = $order_id;
				$tran->tax = $order->get_total_tax();
			    $tran->description = 'Refund Reason: '. $reason;
				$tran->shipping = $order->get_total_shipping();

                if ($tran->refundTransaction()) {
                    $order->add_order_note(__('EbizCharge payment refunded online. Transaction ID: ' . $transaction_id .' Amount: '. $amount. ' Reason: '.$reason, 'woocommerce'));
                    update_post_meta($order->get_id(), '_payment_status', 'Refunded');
                    return true;
                } else {
                    $order->add_order_note(__('EbizCharge payment refund failed. Transaction ID: ' . $transaction_id . ' Error: ' . $tran->error, 'woocommerce'));
                    return false;
                }

            } else {
                return new WP_Error( 'error', __( 'EbizCharge payment refund failed. Please select a valid amount.', 'woocommerce' ) );
            }

        }
		/**
		 * Check if SSL is enabled and notify the user.
		 */
		function ebiz_commerce_ssl_check()
		{
			/* if ( get_option( 'woocommerce_force_ssl_checkout' ) == 'no' && $this->enabled == 'yes' ) {
			echo '<div class="error"><p>' . sprintf( __('ebizcharge is enabled and the <a href="%s">force SSL option</a> is disabled; your checkout is not secure! Please enable SSL and ensure your server has a valid SSL certificate.', 'woothemes' ), admin_url( 'admin.php?page=woocommerce' ) ) . '</p></div>';
			}*/
		}

		/**
		 * Initialize Gateway Settings Form Fields.
		 */
		function init_form_fields()
		{
			$this->form_fields = array(

				'enabled' => array(
					'title' => __( 'Enable/Disable', 'woothemes' ),
					'label' => __( 'Enable EBizCharge', 'woothemes' ),
					'type' => 'checkbox',
					'description' => '',
					'default' => 'no'
				),
				'title' => array(
					'title' => __( 'Title', 'woothemes' ),
					'type' => 'text',
					'description' => __( 'This controls the title which the user sees during checkout.', 'woothemes' ),
					'default' => __( 'Credit Card (EBizCharge)', 'woothemes' )
				),
				'description' => array(
					'title' => __( 'Description', 'woothemes' ),
					'type' => 'textarea',
					'description' => __( 'This controls the description which the user sees during checkout.', 'woothemes' ),
					'default' => 'Pay with your credit card via EBizCharge.'
				),
				'securityid' => array(
					'title' => __( 'API Security ID', 'woothemes' ),
					'type' => 'text',
					'description' => __( 'This is the API Security ID generated within the EBizCharge gateway.', 'woothemes' ),
					'default' => ''
				),
				'username' => array(
					'title' => __( 'API User ID', 'woothemes' ),
					'type' => 'text',
					'description' => __( 'This is the API User ID generated within the EBizCharge gateway.', 'woothemes' ),
					'default' => ''
				),
				'password' => array(
					'title' => __( 'Pin', 'woothemes' ),
					'type' => 'text',
					'description' => __( 'This is the pin generated within the EBizCharge gateway.', 'woothemes' ),
					'default' => ''
				),
				'salemethod' => array(
					'title' => __( 'Sale Method', 'woothemes' ),
					'type' => 'select',
					'description' => __( 'Select which sale method to use. Authorize Only will authorize the customers card for the purchase amount only.  Authorize &amp; Capture will authorize the customer\'s card and collect funds.', 'woothemes' ),
					'options' => $this->_paymentType,
					'default' => 'Authorize &amp; Capture'
				),
				'completecapture' => array(
					'title' => __( 'Automatically Capture on Completed Order', 'woothemes' ),
					'type' => 'checkbox',
					'default' => 'no',
					'description' => __( 'If you select "Yes", when you change the order status to "Completed", the payment will be automatically captured.', 'woothemes' )
				),
				'cardtypes' => array(
					'title' => __( 'Accepted Cards', 'woothemes' ),
					'type' => 'multiselect',
					'description' => __( 'Select which card types to accept.', 'woothemes' ),
					'default' => '',
					'options' => array(
						'Visa' => 'Visa',
						'MasterCard' => 'MasterCard',
						'Discover' => 'Discover',
						'American Express' => 'American Express'
					),
				),
				'saveinfo' => array(
					'title' => __( 'Billing Information Storage', 'woothemes' ),
					'type' => 'checkbox',
					'label' => __( 'Allow customers to save billing information for future use', 'woothemes' ),
					'description' => __( 'This requires login and ebizcharge Method Id', 'woothemes' ),
					'default' => 'no'
				),
				'enableEconnect' => array(
					'title' => __( 'Econnect', 'woothemes' ),
					'label' => __( 'Enable Econnect', 'woothemes' ),
					'type' => 'checkbox',
					'description' => 'Options to sync customers, products, orders and invoices',
					'default' => 'no'
				),

			);
		}

		/**
		 * UI - Admin Panel Options
		 */
		function admin_options()
		{
			?>
			<h3>
				<?php _e( 'EBizCharge','woothemes' ); ?>
			</h3>
			<p>
				<?php _e( 'EBizCharge Gateway is simple and powerful.  The plugin works by adding credit card fields on the checkout page, and then sending the details to EBizCharge for verification.  <a href="http://ebizcharge.com/">Click here to get paid like the pros</a>.', 'woothemes' ); ?>
			</p>
			<table class="form-table">
				<?php $this->generate_settings_html(); ?>
			</table>
			<?php
			$this->econnect_sync_options();
		    echo '<p id="messages"></p>';
		}

		/**
		 * UI - Payment page fields for ebizcharge Commerce.
		 */
		function payment_fields()
		{
			$ebizApi = $this->_initTransaction();

			// Description of payment method from settings
			if ( $this->description ) {
				?>
				<p>
					<?php echo $this->description; ?>
				</p>
				<?php } ?>
				<fieldset>
					<?php
					$user = wp_get_current_user();
					//$this->check_payment_method_conversion( $user->user_login, $user->ID );
					if ( $this->user_has_stored_data( $user->ID ) && ($ebizApi->SearchCustomer($user->ID))) {
					?>
					<fieldset>
						<input type="radio" name="ebizcharge-use-stored-payment-info" id="ebizcharge-use-stored-payment-info-yes" value="yes" checked="checked" onclick="document.getElementById('ebizcharge-new-info').style.display='none'; document.getElementById('ebizcharge-stored-info').style.display='block'" ;/>
						<label for="ebizcharge-use-stored-payment-info-yes" style="display: inline;">
							<?php _e( 'Use a stored payment method', 'woocommerce' ) ?>
						</label>
						<div id="ebizcharge-stored-info">
							<select name="ebizcharge-payment-method">
								<?php foreach($ebizApi->getCustomerPaymentMethods($user->ID) as $method) { ?>
								<option value="<?php echo $method->MethodID ?>">
									<?php echo $method->CardNumber; ?> (
									<?php $exp = $method->CardExpiration;
									echo substr( $exp, -2 ) . '/' . substr( $exp, 2, 2 );
									?>)
								</option>
								<?php } ?>
							</select>
						</div>
					</fieldset>
					<fieldset>
						<p>
							<input type="radio" name="ebizcharge-use-stored-payment-info" id="ebizcharge-use-stored-payment-info-no" value="no" onclick="document.getElementById('ebizcharge-stored-info').style.display='none'; document.getElementById('ebizcharge-new-info').style.display='block'" ;/>
							<label for="ebizcharge-use-stored-payment-info-no" style="display: inline;">
								<?php _e( 'Use a new payment method', 'woocommerce' ) ?>
							</label>
						</p>
						<div id="ebizcharge-new-info" style="display:none">
							<?php } else { ?>
							<fieldset>
								<!-- Show input boxes for new data -->
								<div id="ebizcharge-new-info">
									<?php } ?>
									<!-- Credit card Holder Name -->
									<p class="form-row ">
										<label for="ccnum">
											<?php echo __( 'Name on Card', 'woocommerce' ) ?> <span class="required">*</span>
										</label>
										<input type="text" class="input-text" id="ccholder" name="ccholder" maxlength="50"/>
									</p>
									<!-- Credit card number -->
									<p class="form-row form-row-first">
										<label for="ccnum">
											<?php echo __( 'Credit Card number', 'woocommerce' ) ?> <span class="required">*</span>
										</label>
										<input onkeyup="getCardType(this.value)" type="text" class="input-text" id="ccnum" name="ccnum" maxlength="16"/>
									</p>
									<!-- Credit card type -->
									<p class="form-row form-row-last">
										<label for="cardtype">
											<?php echo __( 'Card type', 'woocommerce' ) ?> <span class="required">*</span>
										</label>
										<select name="cardtype" id="cardtype" class="woocommerce-select">
											<?php  foreach( $this->cardtypes as $type ) { ?>
											<option value="<?php echo $type ?>">
												<?php _e( $type, 'woocommerce' ); ?>
											</option>
											<?php } ?>
										</select>
									</p>
									<div class="clear"></div>
									<!-- Credit card expiration -->
									<p class="form-row form-row-first">
										<label for="cc-expire-month">
											<?php echo __( 'Expiration date', 'woocommerce') ?> <span class="required">*</span>
										</label>
										<select name="expmonth" id="expmonth" class="woocommerce-select woocommerce-cc-month">
											<option value="">
												<?php _e( 'Month', 'woocommerce' ) ?>
											</option>
											<?php
											$months = array();
											for ( $i = 1; $i <= 12; $i++ ) {
												$timestamp = mktime( 0, 0, 0, $i, 1 );
												$months[ date( 'n', $timestamp ) ] = date( 'F', $timestamp );
											}
											foreach ( $months as $num => $name ) {
												printf( '<option value="%u">%s</option>', $num, $name );
											}
											?>
										</select>
										<select name="expyear" id="expyear" class="woocommerce-select woocommerce-cc-year">
											<option value="">
												<?php _e( 'Year', 'woocommerce' ) ?>
											</option>
											<?php
											$years = array();
											for ( $i = date( 'y' ); $i <= date( 'y' ) + 15; $i++ ) {
												printf( '<option value="20%u">20%u</option>', $i, $i );
											}
											?>
										</select>
									</p>
									<?php

									// Credit card security code
									if ( $this->cvv == 'yes' ) {
										?>
									<p class="form-row form-row-last">
										<label for="cvv">
											<?php _e( 'Card security code', 'woocommerce' ) ?> <span class="required">*</span>
										</label>
										<input oninput="validate_cvv(this.value)" woocommerce_ebizcharge_salemethod="text" class="input-text" id="cvv" name="cvv" maxlength="4"/>
										<span class="help">
											<?php _e( '3 or 4 digits usually found on the signature strip.', 'woocommerce' ) ?>
										</span>
									</p>
									<?php
									}

									// Show Option to store credit card data only logged in users
									if ( is_user_logged_in() ) {
										// Option to store credit card data
										if ( $this->saveinfo == 'yes' && !( class_exists( 'WC_Subscriptions_Cart' ) && WC_Subscriptions_Cart::cart_contains_subscription() ) ) {
											?>
									<div style="clear: both;"></div>
									<p>
										<label for="saveinfo">
											<?php _e( 'Save this Payment method?', 'woocommerce' ) ?>
										</label>
										<input type="checkbox" class="input-checkbox" id="saveinfo" name="saveinfo"/>
										<span class="help">
											<?php _e( 'Select to store your billing information for future use.', 'woocommerce' ) ?>
										</span>
									</p>
									<?php
			}
		}
		?>
							</fieldset>
					</fieldset>
			<?php
			}

		public function _initTransaction($econnect = false)
		{
			if($econnect) {
			    include_once 'includes/class-wc-gateway-ebizcharge-econnect.php';
			    $tran = new WC_Gateway_EBizCharge_Econnect();

			} else {
			    include_once 'includes/class-wc-gateway-ebizcharge.php';
			    $tran = new WC_Gateway_EBizCharge();
			}

			$tran->key = $this->securityid;
			$tran->userid = $this->username;
			$tran->pin = $this->password;
			$tran->enableEconnect = (isset($this->enableEconnect) && $this->enableEconnect == 'yes') ? true : false;

			return $tran;
		}

		/**
		 * Process the payment and return the result.
		 */
		function process_payment( $order_id )
		{
			global $woocommerce;
			$order = ( WC()->version < '2.7.0' ) ? new WC_Order( $order_id ) : new Backwards_Compatible_Order( $order_id );
			$user = new WP_User( ( WC()->version < '2.7.0' ) ? $order->user_id : $order->get_user_id() );

			$tran = $this->_initTransaction();

			// Convert CC expiration date from (M)M-YYYY to MMYY
			$expmonth = $this->get_post( 'expmonth' );
			if ( $expmonth < 10 ) {
				$expmonth = '0' . $expmonth;
			}
			$expyear = '';
			if ( $this->get_post( 'expyear' ) != null ) {
				$expyear = substr( $this->get_post( 'expyear' ), -2 );
			}

			// general payment data
			$tran->command = $this->salemethod;
			$tran->amount = ( WC()->version < '2.7.0' ) ? $order->order_total : $order->get_total();
			$tran->cardholder = $this->get_post( 'ccholder' );
			$tran->card = $this->get_post( 'ccnum' );
			$cctype = $this->get_post( 'cardtype' );
			//$tran->cardtype = $cctype;
			$tran->exp = $expmonth . $expyear;
			$tran->cvv2 = $this->get_post( 'cvv' );

			if ( !empty( $order ) ) {
				// Generate a new customer vault id for the payment method
				$new_customer_vault_id = false;
				$custId = $user->ID;
				$orderid = $order->get_id();
				$tran->invoice = $orderid;
				$tran->orderid = $orderid;
				$tran->ponum = $orderid;
				$tran->ip = $_SERVER[ 'REMOTE_ADDR' ];
				$tran->custid = $custId;
				$tran->email = $order->billing_email;

				$tran->tax = $order->get_total_tax();
				$tran->shipping = $order->get_total_shipping();

				// avs data
				$tran->street = $order->billing_address_1;
				$tran->zip = $order->billing_postcode;

				$tran->description = 'description';

				// billing info
				if ( !empty( $order->billing_first_name ) ) {
					$tran->billfname = $order->billing_first_name;
					$tran->billlname = $order->billing_last_name;
					$tran->billcompany = $order->billing_company;
					$tran->billstreet = $order->billing_address_1;
					$tran->billstreet2 = $order->billing_address_2;
					$tran->billcity = $order->billing_city;
					$tran->billstate = $order->billing_state;
					$tran->billzip = $order->billing_postcode;
					$tran->billcountry = $order->billing_country;
					$tran->billphone = $order->billing_phone;
					//  $tran->custid = $billing->getCustomerId();
				}

				// shipping info
				if ( !empty( $order->shipping_first_name ) ) {
					$tran->shipfname = $order->shipping_first_name;
					$tran->shiplname = $order->shipping_last_name;
					$tran->shipcompany = $order->shipping_company;
					$tran->shipstreet = $order->shipping_address_1;
					$tran->shipstreet2 = $order->shipping_address_2;
					$tran->shipcity = $order->shipping_city;
					$tran->shipstate = $order->shipping_state;
					$tran->shipzip = $order->shipping_postcode;
					$tran->shipcountry = $order->shipping_country;
				}

				// line item data
				foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item )
				{
					$_product = apply_filters( 'woocommerce_cart_item_product', $cart_item[ 'data' ], $cart_item, $cart_item_key );

					$sku = $cart_item[ 'data' ]->get_sku();
					if ( empty( $sku ) ) {
						$sku = $_product->get_title();
					}

					$row_price = ( WC()->version < '2.7.0' ) ? $_product->get_price_excluding_tax( 1 ) : wc_get_price_excluding_tax( $_product, array( 'qty' => 1 ) );

					$prod_description = ( !empty($_product->get_short_description()) ) ? $_product->get_short_description() : $_product->get_description();

					if ( empty( $prod_description ) ) {
						$prod_description = $_product->get_title();
					}
					// for Lineitems
					$tran->addLine( $sku, $_product->get_title(), $prod_description, $row_price, $cart_item[ 'quantity' ], $cart_item[ 'line_tax' ] );
					// for tokenization
					$tran->addLineItem( $sku, $_product->get_title(), $prod_description, $row_price, $cart_item[ 'quantity' ], $cart_item[ 'line_tax' ] );
				}
			}
			// Create server request using stored or new payment details
			$CustNum = get_user_meta( $user->ID, 'CustNum', true );

			if ( $this->get_post( 'ebizcharge-use-stored-payment-info' ) == 'yes' && !empty( $CustNum ) )
			{
				//die('existing method');
				// Short request, use stored billing details
				$paymentMethodId = $this->get_post('ebizcharge-payment-method');

                $base_request[ 'customer_vault_id' ] = $user->user_login;
                $base_request[ 'billing_id' ] = $paymentMethodId;
                $base_request[ 'ver' ] = 2;

				$response[ 'response' ] = $tran->SavedProcess( $CustNum, $paymentMethodId );
			}
			else
			{
                $saveCardInfo = $this->get_post( 'saveinfo');
				if ( !$this->get_post( 'createaccount' ) && !empty( $CustNum ) )
				{
					//die('IF NewPaymentProcess'.$CustNum);
					$wpCustID = $user->ID;
					$response[ 'response' ] = $tran->NewPaymentProcess($CustNum, $wpCustID, $saveCardInfo);
					$new_customer_vault_id = $tran->methodID;
				}
			    else if ( ( $this->get_post( 'createaccount' ) &&  $saveCardInfo) || ( $user->ID && empty( $CustNum ) ) )
				{	//die('IF TokenProcess'); //OK
					$response[ 'response' ] = $tran->TokenProcess($custId, $saveCardInfo);
					if ( $response[ 'response' ] ) {
						update_user_meta( $user->ID, 'CustNum', $tran->custnum );
						$new_customer_vault_id = $tran->methodID;
					}
				} else {
					//die('guest');
					// Send request and get response from server
					$response[ 'response' ] = $tran->RunTransaction();
				}
				// Full request, new customer or new information
				$base_request = array(
					'ccnumber' => $this->get_post( 'ccnum' ),
					'cvv' => $this->get_post( 'cvv' ),
					'ccexp' => $expmonth . $expyear,
					'firstname' => $order->billing_first_name,
					'lastname' => $order->billing_last_name,
					'address1' => $order->billing_address_1,
					'city' => $order->billing_city,
					'state' => $order->billing_state,
					'zip' => $order->billing_postcode,
					'country' => $order->billing_country,
					'phone' => $order->billing_phone,
					'email' => $order->billing_email,
				);

				// If "save billing data" box is checked or order is a subscription, also request storage of customer payment information.
				if ( ( $this->get_post( 'saveinfo' ) || $this->is_subscription( $order ) ) && !empty( $new_customer_vault_id ) ) {

					$base_request[ 'customer_vault' ] = 'add_customer';
					// Set customer ID for new record
					$base_request[ 'customer_vault_id' ] = $new_customer_vault_id;
					//$base_request['customer_vault_id'] = $tran->methodID;

					// Set 'recurring' flag for subscriptions
					if ( $this->is_subscription( $order ) ) {
						$base_request[ 'billing_method' ] = 'recurring';
					}
				}
			}
			// Check response -- transaction processed successfully.
			if ($tran->resultcode == 'A' ) {
				if ( !empty( $tran->refnum ) ) {
					update_post_meta( $order->get_id(), '_transaction_id', $tran->refnum, false );

					$payType = ( $this->salemethod == 'sale' ) ? 'Captured' : 'Authorized';
					update_post_meta( $order->get_id(), '_payment_status', $payType, true );

					if(!empty($tran->card)) {

                        $ccCode = $this->get_post('cvv');
                        $ccType = $this->get_post('cardtype');
                        update_post_meta( $order->get_id(), '_card_holder', $tran->cardholder, false );
                        update_post_meta( $order->get_id(), '_card_number', 'XXXXXXXXXXXX' . substr($tran->card, 12, 16 ), false );
                        update_post_meta( $order->get_id(), '_card_expiry', $tran->exp, false );
                        update_post_meta( $order->get_id(), '_card_code', $ccCode, false );
                        update_post_meta( $order->get_id(), '_card_type', $ccType, false );
					}

					if((!empty($CustNum) &&  !empty($paymentMethodId = $this->get_post('ebizcharge-payment-method'))) || !empty($paymentMethodId = $tran->methodID)){
					    $tranMetaKey = "[EBIZCHARGE]|methodid|refnum|authcode|avsresultcode|cvv2resultcode|woocommrceorderid";
					    $tranMetaValue = "[EBIZCHARGE]" . "|" . $paymentMethodId . "|" . $tran->refnum . "|" . $tran->authcode . "|" .
					                $tran->avs_result_code . "|" . $tran->cvv2_result_code . "|" . $order->get_id();

					} else { // for guest customer
					    $tranMetaKey = "[EBIZCHARGE]|methodid|refnum|authcode|avsresultcode|cvv2resultcode";
					    $tranMetaValue = "[EBIZCHARGE]" . "| 0 |" . $tran->refnum . "|" . $tran->authcode . "|" .
					                $tran->avs_result_code . "|" . $tran->cvv2_result_code;
					}

					update_post_meta($order->get_id(), $tranMetaKey, $tranMetaValue, false );
				}
				// Success
				$order->add_order_note( __( 'ebizcharge payment ' . $payType . '. Transaction ID: ', 'woocommerce' ) . $tran->refnum );
				$order->payment_complete();

				if ( $this->get_post( 'ebizcharge-use-stored-payment-info' ) == 'yes' ) {

					if ( $this->is_subscription( $order ) ) {
						// Store payment method number for future subscription payments
						update_post_meta( $order->get_id(), 'payment_method_number', $this->get_post( 'ebizcharge-payment-method' ) );
					}

				} else if ( $this->get_post( 'saveinfo' ) || $this->is_subscription( $order ) ) {

					if (!empty($new_customer_vault_id) ) {
						$customer_vault_ids = get_user_meta( $user->ID, 'customer_vault_ids', true );

						if (!empty( $customer_vault_ids )) {
							$customer_vault_ids[] = $new_customer_vault_id;
						} else {
							$customer_vault_ids = array( $new_customer_vault_id );
						}

						update_user_meta( $user->ID, 'customer_vault_ids', $customer_vault_ids );
					}

					if ( $this->is_subscription( $order ) ) {
						// Store payment method number for future subscription payments
						update_post_meta( $order->get_id(), 'payment_method_number', count( $customer_vault_ids ) - 1 );
					}

				}

                if($tran->enableEconnect) {
                    $econnect = $this->_initTransaction(true);
                    $econnect->syncOrder($order->get_id());
                    $econnect->syncInvoice($order->get_id());
                }
				// Return thank you redirect
				return array(
					'result' => 'success',
					'redirect' => $this->get_return_url( $order ),
				);

			} else if ($tran->resultcode == 'E') { // Error
				// Other transaction error
				$order->add_order_note( __( 'EbizCharge payment failed. Error: ', 'woocommerce' ) . $tran->error );
				wc_add_notice( __( '(Transaction Error) ' . $tran->error, 'ebizcharge' ), 'error' );

			} else {
				$errormsg = isset($tran->error ) ? $tran->error : $response[ 'response' ];
				// No response or unexpected response
				$order->add_order_note( __( "EbizCharge payment failed. ", 'woocommerce' ) . $errormsg );
				wc_add_notice( __( '(Transaction Error) ' . $errormsg, 'ebizcharge' ), 'error' );
			}
		}

		/**
		 * Check if a user's stored billing records have been converted to Single Billing. If not, do it now.
		 */
		function check_payment_method_conversion( $user_login, $user_id ) 
		{
			//if( ! $this->user_has_stored_dathttp://excellencetechnologies.co.in/sanjiv/wptesta( $user_id ) && $this->get_mb_payment_methods( $user_login ) != null ) $this->convert_mb_payment_methods( $user_login, $user_id );
		}
		/**
		 * Check if the user has any billing records in the Customer Vault
		 */
		function user_has_stored_data( $user_id ) 
		{
			return get_user_meta( $user_id, 'customer_vault_ids', true ) != null;
		}

		/**
		 * Update a stored billing record with new CC number and expiration
		 */
		function update_payment_method( $payment_method, $ccnumber, $ccexp ) 
		{
			global $woocommerce;
			$response = '';
			$user = wp_get_current_user();
            $CustNum = get_user_meta( $user->ID, 'CustNum', true );
			try {
                $tran = $this->_initTransaction();
                $ueSecurityToken = $tran->_getUeSecurityToken();
                $client = new SoapClient( $tran->_getWsdlUrl(), $tran->SoapParams() );

                $methodProfiles = $client->GetCustomerPaymentMethodProfile(
                    array(
                        'securityToken' => $ueSecurityToken,
                        'customerToken' => $CustNum,
                        'paymentMethodId' => $payment_method
                    )
                );

                $paymentMethod = $methodProfiles->GetCustomerPaymentMethodProfileResult;

                $paymentMethod->CardNumber = 'XXXXXXXXXXXX' . substr( $ccnumber, 12, 16 );
                $paymentMethod->CardExpiration = substr( $ccexp, 0, 2 ) . substr( $ccexp, -2 );

                $updatedMethodProfile = $client->updateCustomerPaymentMethodProfile(
                    array(
                    'securityToken' => $ueSecurityToken,
                    'customerToken' => $CustNum,
                    'paymentMethodProfile' => $paymentMethod
                ));

                if (isset($updatedMethodProfile->UpdateCustomerPaymentMethodProfileResult))
                {
				    $response = true;
                }

			} catch ( SoapFault $e ) {
				$response = $e->getMessage();
			}

			if ( $response == 1 ) {
				wc_add_notice( __( 'The selected payment method has been updated successfully!', 'ebizcharge' ), 'success' );
			} else {
				wc_add_notice( __( 'Sorry, there was an error:' . $response, 'ebizcharge' ), 'error' );
			}

			wc_print_notices();
		}

		/**
		 * Delete a stored billing method
		 */
		function delete_payment_method( $paymentMethodId )
		{
			global $woocommerce;
			$user = wp_get_current_user();
			$customer_vault_ids = get_user_meta( $user->ID, 'customer_vault_ids', true );

			try {
                $tran = $this->_initTransaction();
                $client = new SoapClient( $tran->_getWsdlUrl(), $tran->SoapParams() );

                $CustNum = get_user_meta( $user->ID, 'CustNum', true );

                $deletePayment = $client->deleteCustomerPaymentMethodProfile(
                    array(
                        'securityToken' => $tran->_getUeSecurityToken(),
                        'customerToken' => $CustNum,
                        'paymentMethodId' => $paymentMethodId,
                    ));

                 $response['response'] =  $deletePayment->DeleteCustomerPaymentMethodProfileResult;

                if ( $response[ 'response' ] != 1 ) {
                    wc_add_notice( __( 'Sorry, there was an error:', 'ebizcharge' ), 'error' );

                    wc_print_notices();
                    return;
                }

			} catch (Exception $e) {
	            wc_add_notice( __( 'Sorry, The selected payment method not deleted: '. $e->getMessage(), 'ebizcharge' ), 'error' );
                wc_print_notices();
                return;
            }

			$last_method = count( $customer_vault_ids ) - 1;
			// Update subscription references
			if ( class_exists( 'WC_Subscriptions_Manager' ) ) {
				foreach ( ( array )( WC_Subscriptions_Manager::get_users_subscriptions( $user->ID ) ) as $subscription ) {
					$subscription_payment_method = get_post_meta( $subscription[ 'order_id' ], 'payment_method_number', true );
					// Cancel subscriptions that were purchased with the deleted method
					if ( $subscription_payment_method == $paymentMethodId ) {
						delete_post_meta( $subscription[ 'order_id' ], 'payment_method_number' );
						WC_Subscriptions_Manager::cancel_subscription( $user->ID, WC_Subscriptions_Manager::get_subscription_key( $subscription[ 'order_id' ] ) );
					} else if ( $subscription_payment_method == $last_method && $subscription[ 'status' ] != 'cancelled' ) {
						update_post_meta( $subscription[ 'order_id' ], 'payment_method_number', $paymentMethodId );
					}
				}
			}

			// if payment method found in $customer_vault_ids, delete it
			if (($key = array_search($paymentMethodId, $customer_vault_ids)) !== false) {
                unset( $customer_vault_ids[$key] );
            }

			update_user_meta( $user->ID, 'customer_vault_ids', $customer_vault_ids );

			wc_add_notice( __( 'The selected payment method has been deleted successfully!.', 'ebizcharge' ), 'success' );

			wc_print_notices();
		}

        function getCustomerData($user)
        {
             return array(
                'CustomerId' => $user->ID,
                'FirstName' => get_user_meta( $user->ID, 'first_name', true ),
                'LastName' => get_user_meta( $user->ID, 'last_name', true ),
                'CompanyName' => get_user_meta( $user->ID, 'billing_company', true ),
                'Phone' => get_user_meta( $user->ID, 'billing_phone', true ),
                'CellPhone' => get_user_meta( $user->ID, 'billing_phone', true ),
                'Fax' => '',
                'Email' => get_user_meta( $user->ID, 'billing_email', true ),
                'WebSite' => '',
                'BillingAddress' => array(
                    'FirstName' => get_user_meta( $user->ID, 'billing_first_name', true ),
                    'LastName' => get_user_meta( $user->ID, 'billing_last_name', true ),
                    'CompanyName' => get_user_meta( $user->ID, 'billing_company', true ),
                    'Address1' => get_user_meta( $user->ID, 'billing_address_1', true ),
                    'Address2' => get_user_meta( $user->ID, 'billing_address_2', true ),
                    'City' => get_user_meta( $user->ID, 'billing_city,', true ),
                    'State' => get_user_meta( $user->ID, 'billing_state', true ),
                    'ZipCode' => get_user_meta( $user->ID, 'billing_postcode', true ),
                    'Country' => get_user_meta( $user->ID, 'billing_country', true ),
                    'Phone' => get_user_meta( $user->ID, 'billing_phone', true ),
                    'Email' => get_user_meta( $user->ID, 'billing_email', true ),
                ),
                'ShippingAddress' => array(
                     'FirstName' => get_user_meta( $user->ID, 'shipping_first_name', true ),
                    'LastName' => get_user_meta( $user->ID, 'shipping_last_name', true ),
                    'CompanyName' => get_user_meta( $user->ID, 'shipping_company', true ),
                    'Address1' => get_user_meta( $user->ID, 'shipping_address_1', true ),
                    'Address2' => get_user_meta( $user->ID, 'shipping_address_2', true ),
                    'City' => get_user_meta( $user->ID, 'shipping_city,', true ),
                    'State' => get_user_meta( $user->ID, 'shipping_state', true ),
                    'ZipCode' => get_user_meta( $user->ID, 'shipping_postcode', true ),
                    'Country' => get_user_meta( $user->ID, 'shipping_country', true ),
                )
            );
        }

		function add_new_method() 
		{
			global $woocommerce;
			$new_customer_vault_id = 0;
			$user = wp_get_current_user();
			$customer_vault_ids = get_user_meta( $user->ID, 'customer_vault_ids', true );

			$tran = $this->_initTransaction();
			$securityToken = $tran->_getUeSecurityToken();
			$client = new SoapClient($tran->_getWsdlUrl(), $tran->SoapParams() );

            // Convert CC expiration date from (M)M-YYYY to MMYY
            $expmonth = $this->get_post( 'expmonth' );
            if ( $expmonth < 10 ) {
                $expmonth = '0' . $expmonth;
            }
            if ( $this->get_post( 'expyear' ) != null ) {
                $expyear = substr( $this->get_post( 'expyear' ), -2 );
            }

            $paymentMethod = array(
                'MethodName' => $this->get_post( 'cardtype' ) . ' ' . substr( $this->get_post( 'ccnum' ), -4 ) . ' - ' . $this->get_post( 'ccholder' ), # . ' - Expires on: ' . $this->exp,
                'SecondarySort' => 1,
                'Created' => date('Y-m-d\TH:i:s'),
                'Modified' => date('Y-m-d\TH:i:s'),
                'AvsStreet' => get_user_meta( $user->ID, 'billing_address_1', true ),
                'AvsZip' => get_user_meta($user->ID, 'billing_postcode', true ),
                'CardCode' => isset($payment['cc_cid']) ? $payment['cc_cid'] : '',
                'CardExpiration' => $expmonth . $expyear,
                'CardNumber' => $this->get_post( 'ccnum' ),
                'CardType' => $this->get_post( 'cardtype' )
            );

            try {
                //  If customer not exist in the gateway, add customer
                if (!$ebizCustomer = $tran->SearchCustomer($user->ID)) {
                    $customerResult = $client->AddCustomer(array(
                        'securityToken' => $securityToken,
                        'customer' => $this->getCustomerData($user)
                    ));

                    $ebizCustomer = $customerResult->AddCustomerResult;
                }
                //add customer payment method
                $paymentMethodResult = $client->addCustomerPaymentMethodProfile(
                    array(
                        'securityToken' => $securityToken,
                        'customerInternalId' => $ebizCustomer->CustomerInternalId,
                        'paymentMethodProfile' => $paymentMethod
                    ));

                $paymentMethodId = $paymentMethodResult->AddCustomerPaymentMethodProfileResult;

    			$new_customer_vault_id = $paymentMethodId;

                // The  ebiz cusNum should be available in API customer Object to save this request
                $customerToken = $client->GetCustomerToken(
                    array(
                        'securityToken' => $securityToken,
                        'customerInternalId' => $ebizCustomer->CustomerInternalId,
                        'CustomerId' => $user->ID
                    ));


                if (!empty($ebizCustomerNumber = $customerToken->GetCustomerTokenResult)) {
                    update_user_meta($user->ID, 'CustNum', $ebizCustomerNumber);
                }

            } catch (SoapFault $ex) {
                wc_add_notice( __( 'Sorry, there was an error:' . $ex->getMessage(), 'ebizcharge' ), 'error' );
                wc_print_notices();
                return;
            }

			if ( $new_customer_vault_id > 0 ) {
				$customer_vault_ids = get_user_meta( $user->ID, 'customer_vault_ids', true );
				//$customer_vault_ids[] = $new_customer_vault_id;

				if (!empty( $customer_vault_ids ) ) {
					$customer_vault_ids[] = $new_customer_vault_id;
				} else {
					$customer_vault_ids = array( $new_customer_vault_id );
				}

				update_user_meta( $user->ID, 'customer_vault_ids', $customer_vault_ids );

				wc_add_notice( __( 'The Payment Method has been added successfully!', 'ebizcharge' ), 'success' );
				wc_print_notices();
			}
		}

		/**
		 * Check payment details for valid format
		 */
		function validate_fields() 
		{

			if ( $this->get_post( 'ebizcharge-use-stored-payment-info' ) == 'yes' ) {
				return true;
			}

			global $woocommerce;

			// Check for saving payment info without having or creating an account
			if ( $this->get_post( 'saveinfo' ) && !is_user_logged_in() && !$this->get_post( 'createaccount' ) ) {
				//      $woocommerce->add_error( __( 'Sorry, you need to create an account in order for us to save your payment information.', 'woocommerce') );
				wc_add_notice( __( 'Sorry, you need to create an account in order for us to save your payment information. ', 'ebizcharge' ), 'error' );

				return false;
			}
			$ccholder = $this->get_post( 'ccholder' );
			$cardType = $this->get_post( 'cardtype' );
			$cardNumber = $this->get_post( 'ccnum' );
			$cardCSC = $this->get_post( 'cvv' );
			$cardExpirationMonth = $this->get_post( 'expmonth' );
			$cardExpirationYear = $this->get_post( 'expyear' );

			// Check card holder name
			if ( empty( $ccholder ) ) {
				wc_add_notice( __( 'Card holder name is empty. ', 'ebizcharge' ), 'error' );

				return false;
			}
			// Check card number
			if ( empty( $cardNumber ) || !ctype_digit( $cardNumber ) ) {
				wc_add_notice( __( 'Card number is invalid.', 'ebizcharge' ), 'error' );

				return false;
			}
			// Check card Type MasterCard
			if ( ( $cardType == "MasterCard" ) && ( !preg_match( '/^5[12345]\d{14}$/', $cardNumber ) ) ) {
				//$woocommerce->add_error( __( 'Card number is invalid.', 'woocommerce' ) );
				wc_add_notice( __( 'Invalid Master Card.', 'ebizcharge' ), 'error' );

				return false;
			}

			// Check card Type Visa
			if ( ( $cardType == "Visa" ) && ( !preg_match( '/^4\d{12}(\d\d\d){0,1}$/', $cardNumber ) ) ) {
				wc_add_notice( __( 'Invalid Visa Card.', 'ebizcharge' ), 'error' );

				return false;
			}
			// Check card Type Discover
			if ( ( $cardType == "Discover" ) && ( !preg_match( '/^6011\d{12}$/', $cardNumber ) ) ) {
				wc_add_notice( __( 'Invalid Discover Card.', 'ebizcharge' ), 'error' );

				return false;
			}
			// Check card Type American Express
			if ( ( $cardType == "American Express" ) && ( !preg_match( '/^3[47]\d{13}$/', $cardNumber ) ) ) {
				wc_add_notice( __( 'Invalid American Express Card.', 'ebizcharge' ), 'error' );

				return false;
			}

			if ( $this->cvv == 'yes' ) {
				// Check security code
				if ( !ctype_digit( $cardCSC ) ) {
					wc_add_notice( __( 'Card security code is invalid (only digits are allowed)', 'ebizcharge' ), 'error' );

					return false;
				}

				if ( ( strlen( $cardCSC ) != 3 && in_array( $cardType, array( 'Visa', 'MasterCard', 'Discover' ) ) ) || ( strlen( $cardCSC ) != 4 && $cardType == 'American Express' ) ) {
					//	$woocommerce->add_error( __( 'Card security code is invalid (wrong length).', 'woocommerce' ) );
					wc_add_notice( __( 'Card security code is invalid (wrong length).', 'ebizcharge' ), 'error' );

					return false;
				}

			}

			// Check expiration data
			$currentYear = date( 'Y' );

			if ( !ctype_digit( $cardExpirationMonth ) || !ctype_digit( $cardExpirationYear ) ||
				$cardExpirationMonth > 12 ||
				$cardExpirationMonth < 1 ||
				$cardExpirationYear < $currentYear ||
				$cardExpirationYear > $currentYear + 20
			) {
				wc_add_notice( __( 'Card expiration date is invalid.', 'ebizcharge' ), 'error' );

				return false;
			}

			return true;
		}

		/**
		 * Add ability to view and edit payment details on the My Account page.(The WooCommerce 'force ssl' option also secures the My Account page, so we don't need to do that.)
		 */
		function add_payment_method_options() 
		{
			$user = wp_get_current_user();
			$this->check_payment_method_conversion( $user->user_login, $user->ID );

			$this->payment_method_form_myaccount();
			if ( $this->get_post( 'add_new_method' ) != null ) {
				if ( $this->validate_fields() ) {
					$this->add_new_method();
				} else {
					global $woocommerce;
					wc_print_notices();
				}
			}
			// check for permission to save button
			// Show Option to store credit card data only logged in users
			if ( is_user_logged_in() ) {
				// Option to store credit card data
				if ( $this->saveinfo == 'yes' && !( class_exists( 'WC_Subscriptions_Cart' ) && WC_Subscriptions_Cart::cart_contains_subscription() ) )
				{
			echo ' <div id="hide-method-bar"><h3 class="pull-left">Add New Payment Method</h3><button id="Add-new-method" class="button pull-right mr-5">Add</button><br/></div>';
				}
			}

			if ( !$this->user_has_stored_data( $user->ID ) ) {
				return;
			}

			if ( $this->get_post( 'delete' ) != null ) {

				$method_to_delete = $this->get_post( 'delete' );
				$response = $this->delete_payment_method( $method_to_delete );

			} else if ( $this->get_post( 'update' ) != null ) {

				$method_to_update = $this->get_post( 'update' );
				$ccnumber = $this->get_post( 'edit-cc-number-' . $method_to_update );

				if ( empty( $ccnumber ) || !ctype_digit( $ccnumber ) ) {

					global $woocommerce;
					wc_add_notice( __( 'Card number is invalid.', 'ebizcharge' ), 'error' );

					wc_print_notices();

				} else {
					$ccexp = $this->get_post( 'edit-cc-exp-' . $method_to_update );
					$expmonth = substr( $ccexp, 0, 2 );
					$expyear = substr( $ccexp, -2 );
					$currentYear = substr( date( 'Y' ), -2 );

					if ( empty( $ccexp ) || !ctype_digit( str_replace( '/', '', $ccexp ) ) ||
						$expmonth > 12 || $expmonth < 1 ||
						$expyear < $currentYear || $expyear > $currentYear + 20 ) {

						global $woocommerce;
						//           $woocommerce->add_error( __( 'Card expiration date is invalid', 'woocommerce' ) );
						wc_add_notice( __( 'Card expiration date is invalid.', 'ebizcharge' ), 'error' );

						wc_print_notices();

					} else {

						$response = $this->update_payment_method( $method_to_update, $ccnumber, $ccexp );

					}
				}
			}
			?>

		<h2>Saved Payment Methods</h2>
		<p>This information is stored to save time at the checkout and to pay for subscriptions.</p>

		<?php
		$ebizApi = $this->_initTransaction();
		if ($ebizApi->searchCustomer($user->ID))
		{
		    foreach($ebizApi->getCustomerPaymentMethods($user->ID) as $currentMethod)
		    {
				?>
				<header class="title">
					<h3>Payment Method <?php echo $currentMethod->MethodName; ?> (<?php echo $currentMethod->CardType; ?>)</h3>

					<button class="button pull-right" id="unlock-delete-button-<?php echo $currentMethod->MethodID; ?>">
						<?php _e( 'Delete', 'woocommerce' ); ?>
					</button>

					<button style="display:none" class="button pull-right" id="cancel-delete-button-<?php echo $currentMethod->MethodID; ?>">
						<?php _e( 'No', 'woocommerce' ); ?>
					</button>
					<form action="<?php echo get_permalink( wc_get_page_id( 'myaccount' ) ) ?>" method="post" class="pull-right">
						<input type="submit" value="<?php _e( 'Yes', 'woocommerce' ); ?>"
						    class="button mr-5" id="delete-button-<?php echo $currentMethod->MethodID; ?>" style="display:none;">
						<input type="hidden" name="delete" value="<?php echo $currentMethod->MethodID ?>">
					</form>
					<span id="delete-confirm-msg-<?php echo $currentMethod->MethodID; ?>" class="pull-left" style="display:none">Are you sure? (Subscriptions purchased with this card will be canceled.)&nbsp;</span>

					<button class="button pull-right mr-5" id="edit-button-<?php echo $currentMethod->MethodID; ?>">
						<?php _e( 'Edit', 'woocommerce' ); ?>
					</button>
					<button style="display:none" class="button pull-right" id="cancel-button-<?php echo $currentMethod->MethodID; ?>">
						<?php _e( 'Cancel', 'woocommerce' ); ?>
					</button>

					<form action="<?php echo get_permalink( wc_get_page_id( 'myaccount' ) ) ?>" method="post">
						<input type="submit" value="<?php _e( 'Save', 'woocommerce' ); ?>" class="button pull-right mr-5" id="save-button-<?php echo $currentMethod->MethodID; ?>" style="display:none;">
						<span style="float:left">Credit card:&nbsp;</span>
						<input type="hidden" style="display:none" id="edit-cc-number-<?php echo $currentMethod->MethodID; ?>" name="edit-cc-number-<?php echo $currentMethod->MethodID; ?>" maxlength="16" value="<?php echo  '000000000000'.substr($currentMethod->CardNumber, 12,16); ?>"/>
						<?php echo  'XXXXXXXXXXXX'.substr($currentMethod->CardNumber, 12,16); ?>
						<span id="cc-number-<?php echo $currentMethod->MethodID; ?>"></span>
						<br/>
						<span class="pull-left">Expiration:&nbsp;</span>
						<input type="text" class="pull-left" style="display:none" id="edit-cc-exp-<?php echo $currentMethod->MethodID; ?>" name="edit-cc-exp-<?php echo $currentMethod->MethodID; ?>" maxlength="5" value="MM/YY"/>
						<span id="cc-exp-<?php echo $currentMethod->MethodID; ?>">
							<?php echo  substr( $currentMethod->CardExpiration, -2 ) . '/' . substr( $currentMethod->CardExpiration, 2,2 ); ?>
						</span>
						<input type="hidden" name="update" value="<?php echo $currentMethod->MethodID ?>">
					</form>

				</header>
				<?php
		    }
			
		} else {
		    echo "<p>There are no saved payment methods.</p>";
		}

		}

		function payment_method_form_myaccount() 
		{
			// check for permission to save form
			// Show Option to store credit card data only logged in users
			if ( is_user_logged_in() ) {
				// Option to store credit card data
				if ( $this->saveinfo == 'yes' && !( class_exists( 'WC_Subscriptions_Cart' ) && WC_Subscriptions_Cart::cart_contains_subscription() ) ) 
				{
			?>
		    <form action="<?php echo get_permalink( wc_get_page_id( 'myaccount' ) ) ?>" method="post" id="0007t" style="display: none;">
			<h3>Add New Payment Method</h3>
			<fieldset>
				<!-- Show input boxes for new data -->
				<div id="ebizcharge-new-info">

					<!-- Credit card Holder Name -->
					<p class="form-row ">
						<label for="ccnum">
							<?php echo __( 'Name on Card', 'woocommerce' ) ?> <span class="required">*</span>
						</label>
						<input type="text" class="input-text" id="ccholder" name="ccholder" maxlength="50"/>
					</p>
					<!-- Credit card number -->
					<p class="form-row form-row-first">
						<label for="ccnum">
							<?php echo __( 'Credit Card number', 'woocommerce' ) ?> <span class="required">*</span>
						</label>
						<input onkeyup="getCardType(this.value)" type="text" class="input-text" id="ccnum" name="ccnum" maxlength="16"/>
					</p>
					<!-- Credit card type -->
					<p class="form-row form-row-last">
						<label for="cardtype">
							<?php echo __( 'Card type', 'woocommerce' ) ?> <span class="required">*</span>
						</label>
						<select name="cardtype" id="cardtype" class="woocommerce-select">
							<?php  foreach( $this->cardtypes as $type ) { ?>
							<option value="<?php echo $type ?>">
								<?php _e( $type, 'woocommerce' ); ?>
							</option>
							<?php } ?>
						</select>
					</p>
					<div class="clear"></div>
					<!-- Credit card expiration -->
					<p class="form-row form-row-first">
						<label for="cc-expire-month">
							<?php echo __( 'Expiration date', 'woocommerce') ?> <span class="required">*</span>
						</label>
						<select name="expmonth" id="expmonth" class="woocommerce-select woocommerce-cc-month" class="mb-5">
							<option value="">
								<?php _e( 'Month', 'woocommerce' ) ?>
							</option>
							<?php
							$months = array();
							for ( $i = 1; $i <= 12; $i++ ) {
								$timestamp = mktime( 0, 0, 0, $i, 1 );
								$months[ date( 'n', $timestamp ) ] = date( 'F', $timestamp );
							}
							foreach ( $months as $num => $name ) {
								printf( '<option value="%u">%s</option>', $num, $name );
							}
							?>
						</select>
						<select name="expyear" id="expyear" class="woocommerce-select woocommerce-cc-year">
							<option value="">
								<?php _e( 'Year', 'woocommerce' ) ?>
							</option>
							<?php
							$years = array();
							for ( $i = date( 'y' ); $i <= date( 'y' ) + 15; $i++ ) {
								printf( '<option value="20%u">20%u</option>', $i, $i );
							}
							?>
						</select>
					</p>
					<?php

					// Credit card security code
					if ( $this->cvv == 'yes' ) {
						?>
					<p class="form-row form-row-last">
						<label for="cvv">
							<?php _e( 'Card security code', 'woocommerce' ) ?> <span class="required">*</span>
						</label>
						<input oninput="validate_cvv(this.value)" woocommerce_ebizcharge_salemethod="text" class="input-text" id="cvv" name="cvv" maxlength="4"/>
						<span class="help">
							<?php _e( '3 or 4 digits usually found on the signature strip.', 'woocommerce' ) ?>
						</span>
					</p>
					<?php
					}

					// Show Option to store credit card data only logged in users
					if ( is_user_logged_in() ) {
						// Option to store credit card data
						if ( $this->saveinfo == 'yes' && !( class_exists( 'WC_Subscriptions_Cart' ) && WC_Subscriptions_Cart::cart_contains_subscription() ) ) {
							?>
					<div style="clear: both;"></div>
					<input type="hidden" class="input-checkbox" id="saveinfo" name="saveinfo" value="yes"/>

					<?php 
		}
	}
	?>
				</div>
			</fieldset>
			<input type="submit" class="add_new_method" id="add_new_method" name="add_new_method" value="Save"/>
			<button id="cancel_button1" class="cancel-btn">Cancel</button>
		</form>
		    <?php
		        }
			}
		}

		function receipt_page( $order ) 
		{
			echo '<p>' . __( 'Thank you for your order.', 'woocommerce' ) . '</p>';
		}

        /**
         * Include jQuery and our scripts
         */
        function add_ebizcharge_scripts() 
		{
            wp_enqueue_script( 'jquery' );
            wp_enqueue_script( 'edit_billing_details', PLUGIN_DIR . 'assets/js/edit_billing_details.js', array( 'jquery' ), 1.0 );
			if ( $this->cvv == 'yes' ) 
			{
				wp_enqueue_script( 'check_cvv', PLUGIN_DIR . 'assets/js/check_cvv.js', array( 'jquery' ), 1.0 );
			}

			wp_enqueue_style('ebiz_css',  PLUGIN_DIR . 'assets/css/ebizcharge.css');

			if ( ! $this->user_has_stored_data( wp_get_current_user()->ID ) )
			{
				return;
			}
        }

        /**
         * Get the current user's login name
         */
        private function get_user_login() 
		{
            global $user_login;
            get_currentuserinfo();
            return $user_login;
		}

		/**
         * Get post data if set
         */
		private function get_post( $name ) 
		{
			if ( isset( $_POST[ $name ] ) ) {
				return $_POST[ $name ];
			}
			return null;
		}

		/**
         * Check whether an order is a subscription
         */
		private function is_subscription( $order ) 
		{
            return class_exists( 'WC_Subscriptions_Order' ) && WC_Subscriptions_Order::order_contains_subscription( $order );
		}

    }

	$GLOBALS['WC_ebizcharge'] = new WC_ebizcharge();

	/**
     * Add the gateway to woocommerce
     */
	function add_ebizcharge_commerce_gateway( $methods ) 
	{
		$methods[] = 'WC_ebizcharge';
		//$methods = array('WC_ebizcharge');
		return $methods;
	}

	add_filter( 'woocommerce_payment_gateways', 'add_ebizcharge_commerce_gateway' );
}


function econnect_install()
{
    global $wpdb;

    $tables = array($wpdb->prefix . 'posts', $wpdb->prefix . 'users');
    $fields = array(
        'ec_status' => 'varchar(20)',
        'ec_status_code' => 'varchar(20)',
        'ec_error' => 'varchar(255)',
        'ec_error_code' => 'varchar(20)',
        'ec_last_modified_date' => 'datetime',
        'ec_internal_id' => 'varchar(255)',
    );

    foreach ($tables as $table) {
        foreach ($fields as $field => $type) {
           addFieldIfNotExist($table, $field, $type);
        }
    }

    addSalesOrderFields();
}
/**
* This function add Econenct sales order sync related fields
*/
function addSalesOrderFields()
{
    global $wpdb;
    $fields = array(
        'ec_order_status' => 'varchar(20)', // transaction status (success/failed)
        'ec_order_error' => 'varchar(255)', // transaction error
        'ec_order_last_modified_date' => 'datetime',
        'ec_order_internal_id' => 'varchar(255)',
    );

    foreach ($fields as $field => $type) {
        addFieldIfNotExist($wpdb->prefix . 'posts', $field, $type);
    }
}

function addFieldIfNotExist($table, $column, $type)
{
    global $wpdb;
    try {
        $sql = "SELECT COLUMN_NAME FROM information_schema.columns WHERE COLUMN_NAME = '" . $column . "' AND table_name = '" . $table . "'; ";
        //Add column if not present.
        if(empty($wpdb->get_results($sql))){
            $wpdb->query("alter table " . $table . " add column " . $column . " $type NULL; ");
        }
    } catch (Exception $e) {
        print_r($e->getMessage());
    }
}
