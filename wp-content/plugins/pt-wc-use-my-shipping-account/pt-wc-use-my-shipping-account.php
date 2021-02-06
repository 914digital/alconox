<?php
/*
 * 
 * Plugin Name: WooCommerce Use My Shipping Account
 * Plugin URI: https://pluginterritory.com/shop/woocommerce-ship-with-my-account-ups-fedex/
 * Description: Adds to WooCommerce shipping option of using FedEx or UPS shipping account.
 * Version: 4.4.1.4
 * Author: Plugin Territory
 * Author URI: http://pluginterritory.com
 * Copyright: 2015-20 Plugin Territory.
 * License: GNU General Public License, version 2
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * 
 * 
 * WC requires at least: 3.0
 * WC tested up to: 4.4.1
 * 
 */

/**
 * Localisation
 */
load_plugin_textdomain( 'pt-wc-use-my-shipping-account', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

/**
 * Plugin page links
 */
function pt_wc_umsa_plugin_links( $links ) {

	$plugin_links = array(
		'<a href="' . admin_url( 'admin.php?page=wc-settings&tab=shipping' ) . '">' . esc_html__( 'Settings', 'pt-wc-use-my-shipping-account' ) . '</a>',
		'<a href="' . admin_url( 'admin.php?page=wc-settings&tab=shipping&section=pt_wcumsa_hazmat_fee' ) . '">' . esc_html__( 'Hazmat fee zones', 'pt-wc-use-my-shipping-account' ) . '</a>'
	);

	return array_merge( $plugin_links, $links );
}

add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'pt_wc_umsa_plugin_links' );

add_action( 'plugins_loaded', 'pt_wc_umsa_loaded' );
function pt_wc_umsa_loaded() {

	if ( is_admin() ) {

		require_once( plugin_dir_path( __FILE__ ) . '/classes/class-admin.php' );

	}
}	


/**
 * pt_wc_umsa_init function.
 *
 * @access public
 * @return void
 */
function pt_wc_umsa_init() {

	include_once( 'classes/class-use-my-shipping-account.php' );

}

add_action( 'woocommerce_shipping_init', 'pt_wc_umsa_init' );

/**
 * pt_wc_umsa_add_method function.
 *
 * @access public
 * @param mixed $methods
 * @return void
 */
function pt_wc_umsa_add_method( $methods ) {

	$methods['use-my-shipping-account'] = 'WC_Use_My_Shipping_Account';
	return $methods;

}

add_filter( 'woocommerce_shipping_methods', 'pt_wc_umsa_add_method' );

/**
 * Filter our method label 
 **/
 
function pt_wc_umsa_cart_shipping_method_full_label( $label, $method ) {

	if ( 'use-my-shipping-account' === $method->id ) {

		$label = $method->label;

	}

	return $label;
}

add_filter( 'woocommerce_cart_shipping_method_full_label', 'pt_wc_umsa_cart_shipping_method_full_label', 10, 2 );

/**
 * Add the shipping account fields to checkout
 **/

function pt_wc_umsa_checkout_fields() {

	$checkout = WC()->checkout();

	echo '<div id="shipping_account_checkout_field">
		<h3>' . esc_html__( 'Shipping account', 'pt-wc-use-my-shipping-account' ) . '</h3>';
	
	echo '<p>' . esc_html__( 'UPS or FedEx Account Number', 'pt-wc-use-my-shipping-account' ) . '</p>';

	woocommerce_form_field( 'shipping_account', 
							array(
								'type'        => 'text',
								'class'       => array('shipping-account-class form-row-wide'),
								'required'    => true,
								'placeholder' => esc_html__( 'Please inform your shipping account number', 'pt-wc-use-my-shipping-account' ),
								), 
								$checkout->get_value( 'shipping_account' ) );
								
	echo '<p>' . esc_html__( 'Select Shipping Service Level', 'pt-wc-use-my-shipping-account ') . '</p>';
	
	woocommerce_form_field( 'shipping_service', 
							array(
								'type'        => 'select',
								'class'       => array('shipping-service-class form-row-wide'),
								'options'     => array(
													'UPS Ground'               => 'UPS Ground',
													'UPS 3 Day Select'         => 'UPS 3 Day Select',
													'UPS 2nd Day Air'          => 'UPS 2nd Day Air',
													'UPS Next Day Air Saver'   => 'UPS Next Day Air Saver',
													'UPS Next Day Air'         => 'UPS Next Day Air',
													'FedEx Ground'             => 'FedEx Ground',
													'FedEx 2Day'               => 'FedEx 2Day',
													'FedEx Standard Overnight' => 'FedEx Standard Overnight',
													'FedEx Priority Overnight' => 'FedEx Priority Overnight',
													//'FedEx First Overnight'    => 'FedEx First Overnight',
													)
								), 
								$checkout->get_value( 'shipping_service' )
							);
								
	echo '</div>';

}
//add_action( 'woocommerce_checkout_shipping',     'pt_wc_umsa_checkout_fields' );
//add_action( 'woocommerce_checkout_order_review', 'pt_wc_umsa_checkout_fields', 15 );


/**
 * Add the shipping account fields to checkout shipping methods
 **/

function pt_wc_umsa_checkout_fields_at_shipping() {

	$show_number                = false;
	$show_air_options           = false;
	$show_both_options          = false;
	$show_ground_options        = false;
	$show_canada_options        = false;
	$show_all_canada_options    = false;
	$show_international_options = false;

	$hazmat_zones = get_option( 'use_my_shipping_account_hazmat_zones' );

	//wc_add_notice( 'Hazmat zones <pre>' . print_r( $hazmat_zones, 1 ) .'</pre>', 'error' );

	//wc_print_notice( 'Chosen shipping methods <pre>' . print_r( WC()->session->chosen_shipping_methods, 1 ) .'</pre>', 'error' );

	//wc_print_notice( 'Packages <pre>' . print_r( WC()->shipping->get_packages(), 1 ) .'</pre>', 'error' );

	//exit;


	$packages = WC()->shipping->get_packages();
	foreach ( $packages as $i => $package ) {

		$chosen_method = isset( WC()->session->chosen_shipping_methods[ $i ] ) ? WC()->session->chosen_shipping_methods[ $i ] : '';

		if ( 'use-my-shipping-account' == $chosen_method ) {
			
			$show_number = true;

			// canada options only if we don't have any hazmat item

			if ( 'CA' == $package['destination']['country'] ) {

				$show_canada_options     = true;
				$show_all_canada_options = true;
				$hazmat_fee              = 0;

				foreach ( $package['contents'] as $item ) {

					if ( $item['variation_id'] ) {

						$hazmat_fee = get_post_meta( $item['variation_id'], '_pt_wc_international_hazmat_fee', true );
					}

					if ( ! $hazmat_fee ) {

						$hazmat_fee = get_post_meta( $item['product_id'], '_pt_wc_international_hazmat_fee_simple', true );

					}

					if ( $hazmat_fee ) {

						$show_all_canada_options = false;
						break;

					}
				}
			}

			if ( ! $show_canada_options && ! $show_all_canada_options ) {

				$zone = wc_get_shipping_zone( $package );

				if ( in_array( $zone->get_id(), $hazmat_zones ) ) {

					$show_international_options = true;

				} else {

					if ( isset( $package['type'] ) && 'air' == $package['type'] ) {

						$show_air_options = true;

					}

					if ( isset( $package['type'] ) && 'ground' == $package['type'] ) {

						$show_ground_options = true;

					}

					if ( ! isset( $package['type'] ) ) {

						$show_both_options = true;

					}
				}
			}
		}
	}

	if ( ! $show_number && ! $show_air_options && ! $show_both_options && ! $show_ground_options &&  ! $show_canada_options && ! $show_international_options ) {

		return;

	}

	$checkout = WC()->checkout();

	if ( $show_ground_options ) {

		echo '<tr><th>';

		echo esc_html__( 'Ground Shipping Options', 'pt-wc-use-my-shipping-account ') . '</th><td>';
		
		woocommerce_form_field( 'ground_shipping_service', 
								array(
									'type'        => 'select',
									'class'       => array('shipping-service-class form-row-wide'),
									'options'     => array(
														'UPS Ground'               => 'UPS Ground',
														'FedEx Ground'             => 'FedEx Ground',
														)
									), 
									$checkout->get_value( 'ground_shipping_service' )
								);
									
		echo '</td></tr>';

	}

	if ( $show_both_options ) {

		echo '<tr><th>';

		echo esc_html__( 'My Account Options', 'pt-wc-use-my-shipping-account ') . '</th><td>';
		
		woocommerce_form_field( 'both_shipping_service', 
								array(
									'type'        => 'select',
									'class'       => array('shipping-service-class form-row-wide'),
									'options'     => array(
														'UPS Ground'               => 'UPS Ground',
														'UPS 3 Day Select'         => 'UPS 3 Day Select',
														'UPS 2nd Day Air'          => 'UPS 2nd Day Air',
														'UPS Next Day Air Saver'   => 'UPS Next Day Air Saver',
														'UPS Next Day Air'         => 'UPS Next Day Air',
														'FedEx Ground'             => 'FedEx Ground',
														'FedEx 2Day'               => 'FedEx 2Day',
														'FedEx Standard Overnight' => 'FedEx Standard Overnight',
														'FedEx Priority Overnight' => 'FedEx Priority Overnight',
														//'FedEx First Overnight'    => 'FedEx First Overnight',

														)
									), 
									$checkout->get_value( 'both_shipping_service' )
								);
									
		echo '</td></tr>';

	}


	if ( $show_all_canada_options ) {

		echo '<tr><th>';

		echo esc_html__( 'Shipping Options', 'pt-wc-use-my-shipping-account ') . '</th><td>';
		
		woocommerce_form_field( 'shipping_service', 
								array(
									'type'        => 'select',
									'class'       => array('shipping-service-class form-row-wide'),
									'options'     => array(
														'UPS Ground'                   => 'UPS Ground',
														'UPS Standard'                 => 'UPS Standard',
														'UPS Worldwide Saver'          => 'UPS Worldwide Saver',
														'UPS Worldwide Express'        => 'UPS Worldwide Express',
														'FedEx Ground'                 => 'FedEx Ground',
														'FedEx International Priority' => 'FedEx International Priority',
														'FedEx International Economy'  => 'FedEx International Economy',
														)
									), 
									$checkout->get_value( 'shipping_service' )
								);
									
		echo '</td></tr>';

	} elseif ( $show_canada_options ) {

		echo '<tr><th>';

		echo esc_html__( 'Shipping Options', 'pt-wc-use-my-shipping-account ') . '</th><td>';
		
		woocommerce_form_field( 'shipping_service', 
								array(
									'type'        => 'select',
									'class'       => array('shipping-service-class form-row-wide'),
									'options'     => array(
														'UPS Worldwide Saver'          => 'UPS Worldwide Saver',
														'UPS Worldwide Express'        => 'UPS Worldwide Express',
														'FedEx International Priority' => 'FedEx International Priority',
														'FedEx International Economy'  => 'FedEx International Economy',
														)
									), 
									$checkout->get_value( 'shipping_service' )
								);
									
		echo '</td></tr>';

	}

	if ( $show_international_options ) {

		echo '<tr><th>';

		echo esc_html__( 'Air Shipping Options', 'pt-wc-use-my-shipping-account ') . '</th><td>';
		
		woocommerce_form_field( 'shipping_service', 
								array(
									'type'        => 'select',
									'class'       => array('shipping-service-class form-row-wide'),
									'options'     => array(
														'UPS Worldwide Saver'          => 'UPS Worldwide Saver',
														'UPS Worldwide Express'        => 'UPS Worldwide Express',
														'FedEx International Priority' => 'FedEx International Priority',
														'FedEx International Economy'  => 'FedEx International Economy',
														)
									), 
									$checkout->get_value( 'shipping_service' )
								);
									
		echo '</td></tr>';

	}

 
 	if ( $show_air_options ) {

		echo '<tr><th>';

		echo esc_html__( 'Air Shipping Options', 'pt-wc-use-my-shipping-account ') . '</th><td>';
		
		woocommerce_form_field( 'shipping_service', 
								array(
									'type'        => 'select',
									'class'       => array('shipping-service-class form-row-wide'),
									'options'     => array(
														'UPS 3 Day Select'         => 'UPS 3 Day Select',
														'UPS 2nd Day Air'          => 'UPS 2nd Day Air',
														'UPS Next Day Air Saver'   => 'UPS Next Day Air Saver',
														'UPS Next Day Air'         => 'UPS Next Day Air',
														'FedEx 2Day'               => 'FedEx 2Day',
														'FedEx Standard Overnight' => 'FedEx Standard Overnight',
														'FedEx Priority Overnight' => 'FedEx Priority Overnight',
														//'FedEx First Overnight'    => 'FedEx First Overnight',
														)
									), 
									$checkout->get_value( 'shipping_service' )
								);
									
		echo '</td></tr>';

	}

	if ( $show_number ) {

		echo '<tr><th>';

		echo esc_html__( 'My Account Number', 'pt-wc-use-my-shipping-account' ) . '</th><td>';

		echo '<style>.woocommerce form .form-row  input.shipping-account-number { width:108%; } </style>';

		woocommerce_form_field( 'shipping_account', 
								array(
									'type'        => 'text',
									'input_class' => array( 'shipping-account-number' ),
									'required'    => true,
									'placeholder' => esc_html__( 'UPS or FedEx number', 'pt-wc-use-my-shipping-account' ),
									), 
									$checkout->get_value( 'shipping_account' ) );
									
		echo '</td></tr>';

	}
}

//add_action( 'woocommerce_review_order_before_shipping', 'pt_wc_umsa_checkout_fields_at_shipping' );
add_action( 'woocommerce_review_order_after_shipping',  'pt_wc_umsa_checkout_fields_at_shipping' );

function pt_wc_umsa_is_us_continental() { // 

	if ( empty ( WC()->customer->get_shipping_country() ) || empty ( WC()->customer->get_shipping_state() ) ) {

		return true; // by default we assume empty addresses will become US addresses

	}

	$us_excluded_states = array( 'AK', 'HI' ); //, 'GU', 'PR', 'AA', 'AE','AP', 'AS', 'GU', 'MP', 'PR', 'UM', 'VI' );

	return ( 'US' == WC()->customer->get_shipping_country() && ! in_array( WC()->customer->get_shipping_state(), $us_excluded_states ) );

}

/**
 * Process the checkout
 **/

function pt_wc_umsa_checkout_process() {

	if ( isset( $_POST['shipping_method'] ) && in_array( 'use-my-shipping-account', $_POST['shipping_method'] ) ) {

		if ( empty( $_POST['shipping_account'] ) ) {

			wc_add_notice( __('Missing information about <strong>Shipping Account</strong>, please inform it.', 'pt-wc-use-my-shipping-account'), 'error' );

		}
		    
		// don't accept ground products if we don't have ground available
		if ( ( ! isset( $_POST['both_shipping_service'] ) && ! isset ( $_POST['ground_shipping_service'] ) )
			|| ( ! pt_wc_umsa_is_us_continental() ) ) {

			foreach ( WC()->cart->get_cart() as $item_key => $item ) {

				if ( $item['data']->needs_shipping() ) {

					$post_id    = $item['data']->get_id();
					$ground_air = get_post_meta( $post_id, '_pt_ups_merger_ground_air', true );

					if ( empty( $ground_air ) ) {

						$ground_air = 'either_ground_or_air';

					}

					if ( 'parent' == $ground_air ) {

						$ground_air = get_post_meta( $item['data']->get_parent_id(), '_pt_ups_merger_ground_air', true );

					}

					if ( empty( $ground_air ) ) {

						$ground_air = 'either_ground_or_air';

					}

					if ( 'ground_only' == $ground_air ) {

						wc_add_notice( sprintf( __( '<strong>%s</strong> can only be shipped by Ground, please remove it or use an address in continental United States.', 'pt-wc-use-my-shipping-account' ), $item['data']->get_name() ), 'error' );

					}
				}
			}
		}
	}

	//wc_add_notice( '<pre>'. print_r($_POST, 1 ) . '</pre>', 'error' );
	//wc_add_notice( 'WC()->customer->get_shipping_state() <pre>'. print_r(WC()->customer->get_shipping_state(), 1 ) . '</pre>', 'error' );

	//wc_add_notice( 'Packages <pre>' . print_r( WC()->shipping->get_packages(), 1 ) .'</pre>', 'error' );
	//wc_add_notice( 'Cart <pre>' . print_r( WC()->cart->get_cart(), 1 ) .'</pre>', 'error' );
}

add_action( 'woocommerce_checkout_process', 'pt_wc_umsa_checkout_process' );


/**
 * Don't allow ground products in cart if destination is not continental US
 **/
function pt_wc_umsa_is_purchasable( $purchasable, $product ) {

	/*if ( is_checkout() ) {

		return $purchasable;

	}*/

	//wc_add_notice( $product->get_id() . ' ' .  $product->get_name() . ' ' . WC()->customer->get_shipping_country() );

	if ( $purchasable && $product->needs_shipping() && ! pt_wc_umsa_is_us_continental() ) {

		$post_id    = $product->get_id();
		$ground_air = get_post_meta( $post_id, '_pt_ups_merger_ground_air', true );

		if ( empty( $ground_air ) ) {

			$ground_air = 'either_ground_or_air';

		}

		if ( 'parent' == $ground_air ) {

			$ground_air = get_post_meta( $product->get_parent_id(), '_pt_ups_merger_ground_air', true );

	//wc_add_notice( 'Parent ' . $ground_air . ' ' . $product->get_parent_id() . ' ' .  $product->get_name() . ' ' . WC()->customer->get_shipping_country(), 'error' );


		} else {

	//wc_add_notice( '`' . $ground_air . '`' . $product->get_id() . ' ' .  $product->get_name() . ' ' . WC()->customer->get_shipping_country(), 'success' );


		}

		if ( empty( $ground_air ) ) {

			$ground_air = 'either_ground_or_air';

		}


		if ( 'ground_only' == $ground_air) {

			if ( ! pt_wc_umsa_is_us_continental() ) {

				/*$notice = sprintf( __( 'DEBUG <strong>%s</strong> can only be shipped by Ground, please use an address in continental United States.', 'pt-wc-use-my-shipping-account' ), $product->get_name() );

				if ( ! wc_has_notice( $notice, 'error' ) ) {

					wc_add_notice( $notice, 'error' );

				}*/

				$purchasable = false;

			}
		}
	}

	return $purchasable;
}

add_filter( 'woocommerce_is_purchasable', 'pt_wc_umsa_is_purchasable', 10, 2 );

function pt_wc_umsa_remove_is_purchasable_filter() {
	remove_filter( 'woocommerce_is_purchasable', 'pt_wc_umsa_is_purchasable', 10, 2 );
}

add_action( 'woocommerce_load_cart_from_session', 'pt_wc_umsa_remove_is_purchasable_filter' );


function pt_wc_umsa_add_is_purchasable_filter() {
	add_filter( 'woocommerce_is_purchasable', 'pt_wc_umsa_is_purchasable', 10, 2 );	
}

add_action( 'woocommerce_cart_loaded_from_session', 'pt_wc_umsa_add_is_purchasable_filter' );


/**
 * Update the order meta with fields values
 **/
function pt_wc_umsa_update_order_meta( $order_id, $posted ) {

	if ( isset( $_POST['shipping_method'] ) &&  in_array( 'use-my-shipping-account', $_POST['shipping_method'] ) ) {

		update_post_meta( $order_id, '_shipping_account', esc_attr($_POST['shipping_account']));
		update_post_meta( $order_id, '_shipping_service', esc_attr($_POST['shipping_service']));
		update_post_meta( $order_id, '_both_shipping_service', esc_attr($_POST['both_shipping_service']));
		update_post_meta( $order_id, '_ground_shipping_service', esc_attr($_POST['ground_shipping_service']));

	}
}

add_action('woocommerce_checkout_update_order_meta', 'pt_wc_umsa_update_order_meta', 10, 2);


/**
 * Display field value on the order edition page
 **/

function pt_wc_umsa_display_admin_order_meta( $order ) {

	if ( $order->has_shipping_method( 'use-my-shipping-account' ) ) {

		echo '<p><strong>' . esc_html__('Shipping account:', 'pt-wc-use-my-shipping-account') . '</strong> ' . $order->get_meta( '_shipping_account' ) . '</p>';

		if ( $order->get_meta( '_ground_shipping_service' ) ) {

			echo '<p><strong>' . esc_html__('Ground shipping service:', 'pt-wc-use-my-shipping-account') . '</strong> ' . $order->get_meta( '_ground_shipping_service' ) . '</p>';
			
		 }

		if ( $order->get_meta( '_shipping_service' ) ) {

			echo '<p><strong>' . esc_html__('Air shipping service:', 'pt-wc-use-my-shipping-account') . '</strong> ' . $order->get_meta( '_shipping_service' ) . '</p>';

		 }

		if ( $order->get_meta( '_both_shipping_service' ) ) {

			echo '<p><strong>' . esc_html__('Shipping services:', 'pt-wc-use-my-shipping-account') . '</strong> ' . $order->get_meta( '_both_shipping_service' ) . '</p>';

		 }


	}
}

add_action( 'woocommerce_admin_order_data_after_shipping_address', 'pt_wc_umsa_display_admin_order_meta' ); 


/**
 * Display info at product page
 **/
function pt_wc_umsa_check_destination_allowed( $args, $product, $variation ) {

	if ( ! $args['is_purchasable'] ) {

		if ( empty( $args['availability_html'] ) && ! pt_wc_umsa_is_us_continental() ) {

			$notice = sprintf( __( '<strong>%s</strong> can only be shipped by Ground, please use an address in continental United States.', 'pt-wc-use-my-shipping-account' ), $variation->get_name() );

			if ( get_current_user_id() ) {

				$notice .= '<br />' . esc_html__( 'Change', 'pt-wc-use-my-shipping-account' ) . ' <a href="' . esc_url( wc_get_endpoint_url( 'edit-address', 'shipping' ) ) . '" class="edit">' . esc_html__( 'shipping address', 'pt-wc-use-my-shipping-account' ) . '</a>.';

			}

			$destination = array( 
					'country'  => WC()->customer->get_shipping_country(),
					'state'    => WC()->customer->get_shipping_state(),
					'postcode' => WC()->customer->get_shipping_postcode()
				);

			$notice .= 	'<hr />Shipping info: ' . WC()->countries->get_formatted_address( $destination, ', ' ) . '<hr />';


			$args['availability_html'] = $notice;

		}
	}

	/*
		apply_filters(
			'woocommerce_available_variation',
			array(
				'attributes'            => $variation->get_variation_attributes(),
				'availability_html'     => wc_get_stock_html( $variation ),
				'backorders_allowed'    => $variation->backorders_allowed(),
				'dimensions'            => $variation->get_dimensions( false ),
				'dimensions_html'       => wc_format_dimensions( $variation->get_dimensions( false ) ),
				'display_price'         => wc_get_price_to_display( $variation ),
				'display_regular_price' => wc_get_price_to_display( $variation, array( 'price' => $variation->get_regular_price() ) ),
				'image'                 => wc_get_product_attachment_props( $variation->get_image_id() ),
				'image_id'              => $variation->get_image_id(),
				'is_downloadable'       => $variation->is_downloadable(),
				'is_in_stock'           => $variation->is_in_stock(),
				'is_purchasable'        => $variation->is_purchasable(),
				'is_sold_individually'  => $variation->is_sold_individually() ? 'yes' : 'no',
				'is_virtual'            => $variation->is_virtual(),
				'max_qty'               => 0 < $variation->get_max_purchase_quantity() ? $variation->get_max_purchase_quantity() : '',
				'min_qty'               => $variation->get_min_purchase_quantity(),
				'price_html'            => $show_variation_price ? '<span class="price">' . $variation->get_price_html() . '</span>' : '',
				'sku'                   => $variation->get_sku(),
				'variation_description' => wc_format_content( $variation->get_description() ),
				'variation_id'          => $variation->get_id(),
				'variation_is_active'   => $variation->variation_is_active(),
				'variation_is_visible'  => $variation->variation_is_visible(),
				'weight'                => $variation->get_weight(),
				'weight_html'           => wc_format_weight( $variation->get_weight() ),
			),
			$this,
			$variation
		);
	*/

	return $args;
}

add_filter( 'woocommerce_available_variation', 'pt_wc_umsa_check_destination_allowed', 10, 3 );
