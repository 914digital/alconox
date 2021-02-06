<?php
 /*
  * Plugin Name: WooCommerce Hazmat Fee
  *
  * Description: Allows adding a fee to hazardous material products.
  * Version: 4.4.1
  *
  * Author: Plugin Territory
  * Author URI: http://pluginterritory.com/
  * 
  * Text Domain: pt-wc-hazmat-fee
  * Domain Path: /languages
  *
  * WC requires at least: 3.0
  * WC tested up to: 4.4.1
  *
  * Requires at least: 3.5
  * Tested up to: 5.5
  *
  *
  * Copyright: 2020 Plugin Territory
  *
  * License: GNU General Public License v2.0
  * License URI: http://www.gnu.org/licenses/gpl-2.0.html
  *
  */

/**
 * Localisation
 */
load_plugin_textdomain( 'pt-wc-hazmat-fee', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );


/**
 * Plugin page links
 */
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'pt_wc_hazmat_fee_plugin_links' );
function pt_wc_hazmat_fee_plugin_links( $links ) {

	$plugin_links = array(
		'<a href="' . admin_url( 'admin.php?page=wc-settings&tab=products&section=pt_wc_hazmat_fee' ) . '">' . __( 'Settings', 'pt-wc-hazmat-fee' ) . '</a>',

	);

	return array_merge( $plugin_links, $links );
}


add_action( 'plugins_loaded', 'pt_wc_hazmat_fee_init' );
function pt_wc_hazmat_fee_init() {

	if ( ! class_exists( 'pt_wc_hazmat_fee_admin' ) ) {

		// we are good to start.
		pt_wc_hazmat_fee_set_our_hooks();

	} else {

		// other plugin/theme is using the same class name
		function pt_wc_hazmat_fee_admin_notice() {
			echo '
			<div class="error">
				<p>' . __( 'WooCommerce Hazmat Fee is in conflict with another plugin for PHP class `pt_wc_hazmat_fee_admin`. <br />
				Please deactivate the other plugin.', 'pt-wc-hazmat-fee' ) . '</p>
			</div>';
		}

		add_action( 'admin_notices', 'pt_wc_hazmat_fee_admin_notice' );

	}
}


/**
 * Let's fish!
 */
function pt_wc_hazmat_fee_set_our_hooks() {

	if ( is_admin() ) {

		require_once( plugin_dir_path( __FILE__ ) . '/classes/class-admin.php' );

	}

	add_action( 'woocommerce_cart_calculate_fees', 'pt_wc_hazmat_fee_calculate' );
}

function pt_wc_hazmat_fee_calculate( $cart ) {

	if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {

		return;

	}

	$total_fee      = 0;

	$hazmat_zones   = get_option( 'use_my_shipping_account_hazmat_zones' );
	$chosen_methods = WC()->session->get( 'chosen_shipping_methods' );
    $packages       = WC()->shipping->get_packages();

	foreach ( $packages as $i => $package ) {

		$local         = false;
		$international = false;

		if ( isset( $package['type'] ) ) {

			$local         = ( 'ground' == $package['type'] );
			$international = ( 'air'    == $package['type'] );

		}

		foreach ( $package['contents'] as $item_id => $values ) {

			$shipping_method = explode( ':', $chosen_methods[ $i ] );

			//$cart->add_fee( $chosen_methods[ $i ], 0 ); 
			//$cart->add_fee( $shipping_method[0], 0 ); 

			if ( 'use-my-shipping-account' == $shipping_method[0] ) {

				$zone = wc_get_shipping_zone( $package );

				//$cart->add_fee( 'zone id ' .  $zone->get_id(), 0 ); 
				//$cart->add_fee( 'hazmat_zones ' .  print_r( $hazmat_zones, 1 ), 0 ); 

				if ( ! in_array( $zone->get_id(), $hazmat_zones ) ) {

					$local         = false;
					$international = false;

				}

			} elseif ( ! isset( $package['type'] ) ) {

				if ( 'pt-ups-merger' == $shipping_method[0] ) {

					if ( '03' == $shipping_method[2] ) { // ground

						$local         = true;
						$international = false;

					} else {

						$local         = false;
						$international = true;

					}
				} 
			}

			if ( $local ) {

				$fee = get_post_meta( $values['data']->get_id(), '_pt_wc_local_hazmat_fee', true );

				if ( ! $fee ) {

					$fee = get_post_meta( $values['data']->get_parent_id(), '_pt_wc_local_hazmat_fee_simple', true );

				}

	//$cart->add_fee( esc_html__( 'Hazmat fee ground ' . $item_id . ' ' . $fee . ' * ' . $values['quantity'], 'pt-wc-hazmat-fee' ), (float) $fee * $values['quantity'], true );


				$total_fee += (float) $fee * $values['quantity'];

			}

			if ( $international ) {

				$fee = get_post_meta( $values['data']->get_id(), '_pt_wc_international_hazmat_fee', true );

				if ( ! $fee ) {

					$fee = get_post_meta( $values['data']->get_parent_id(), '_pt_wc_international_hazmat_fee_simple', true );

				}

	//$cart->add_fee( esc_html__( 'Hazmat fee air ' . $item_id . ' ' . $fee . ' * ' . $values['quantity'], 'pt-wc-hazmat-fee' ), (float) $fee * $values['quantity'], true );
				$total_fee += (float) $fee * $values['quantity'];

			}
		}
	}

	if ( $total_fee ) {

		$cart->add_fee( esc_html__( 'Hazmat fee', 'pt-wc-hazmat-fee' ), $total_fee, true );

	}

}
