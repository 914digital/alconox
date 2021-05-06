<?php
 /*
  * Plugin Name: WooCommerce UPS Merger
  *
  * Description: Calculates and merges the UPS rates from different shipping classes. Orders by lowest rate value.
  * Version: 4.8.0.2
  *
  * Author: Plugin Territory
  * Author URI: http://pluginterritory.com/
  * 
  * Text Domain: pt-wc-ups-merger
  * Domain Path: /languages
  *
  * WC requires at least: 3.0
  * WC tested up to: 4.8.0
  *
  * Requires at least: 3.5
  * Tested up to: 5.6
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
load_plugin_textdomain( 'pt-wc-ups-merger', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );


/**
 * Plugin page links
 */
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'pt_wc_ups_merger_plugin_links' );
function pt_wc_ups_merger_plugin_links( $links ) {

	$plugin_links = array(
		'<a href="' . admin_url( 'admin.php?page=wc-settings&tab=shipping&section=pt-ups-merger' ) . '">' . __( 'Settings', 'pt-wc-ups-merger' ) . '</a>',

	);

	return array_merge( $plugin_links, $links );
}

add_action( 'plugins_loaded', 'pt_wc_ups_merger_init' );
function pt_wc_ups_merger_init() {

	if ( ! class_exists( 'PT_UPS_Merger_Admin' ) ) {

		// we are good to start.
		pt_wc_ups_merger_set_our_hooks();

	} else {

		// other plugin/theme is using the same class name
		function pt_wc_ups_merger_admin_notice() {
			echo '
			<div class="error">
				<p>' . __( 'WooCommerce UPS Merger is in conflict with another plugin for PHP class `PT_UPS_Merger_Admin`. <br />
				Please deactivate the other plugin.', 'pt-wc-ups-merger' ) . '</p>
			</div>';
		}

		add_action( 'admin_notices', 'pt_wc_ups_merger_admin_notice' );

	}
}


/**
 * Let's fish!
 */
function pt_wc_ups_merger_set_our_hooks() {

	if ( is_admin() ) {

		require_once( plugin_dir_path( __FILE__ ) . 'classes/class-admin.php' );

	}

	require_once( plugin_dir_path( __FILE__ ) . 'includes/filter-rates.php' );
	require_once( plugin_dir_path( __FILE__ ) . 'includes/merge-packages.php' );
	require_once( plugin_dir_path( __FILE__ ) . 'includes/split-packages.php' );

	add_filter( 'woocommerce_locate_template', 'pt_wc_ups_merger_locate_template', 99, 3 ); // lets have our template, 99 hopes to be the last filter called

	add_action( 'wc_avatax_after_checkout_tax_calculated', 'pt_wc_ups_merger_remove_shipping_taxes' );
}

function pt_wc_ups_merger_remove_shipping_taxes() {

	$shipping_taxes = array_sum( WC()->cart->get_shipping_taxes() );

	WC()->cart->set_shipping_tax( 0 );
	WC()->cart->set_shipping_taxes( array() );
	WC()->cart->set_total_tax( WC()->cart->get_total_tax() - $shipping_taxes );

	WC()->cart->shipping_tax_total = 0;
	WC()->cart->total -= $shipping_taxes * 2;

	$packages = WC()->shipping()->get_packages();
	foreach ( $packages as $index => $package ) {
		foreach ( $package['rates'] as $method_id => $rate ) {
			$packages[ $index ]['rates'][ $method_id ]->taxes = array();
		}
	}
	WC()->shipping()->packages = $packages;
}

// use our template file
function pt_wc_ups_merger_locate_template( $template, $template_name, $template_path ) {

    if ( in_array( $template_name, array( 
        
            'cart/cart-shipping.php',

        ) ) ) { 

        // Use the plugin template
        $template = plugin_dir_path( __FILE__ ) . 'templates/' .$template_name;
    }
    return $template;
}


// filter the correct account # for UPS JC
// add the full ship to address
add_filter( 'woocommerce_shipping_ups_request', 'pt_wc_ups_merger_ups_request', 10, 3 );
function pt_wc_ups_merger_ups_request( $send_request, $package_requests, $package ) {

	if ( strpos( $send_request, '<PostalCode>07304</PostalCode>' ) ) {

		$send_request = str_replace( 
			'<ShipperNumber>A0687V</ShipperNumber>', 
			'<ShipperNumber>067789</ShipperNumber>', $send_request );

		$pt_wc_ups_merger_options = get_option( 'woocommerce_pt-ups-merger_settings' );

		if ( isset( $pt_wc_ups_merger_options['debug'] ) ) {

			$debug = ( 'yes' == $pt_wc_ups_merger_options['debug'] );

		} else {

			$debug = false;

		}

		/*if ( $debug ) {

			wc_add_notice( 'FILTERED UPS REQUEST: <pre>' . print_r( htmlspecialchars( $send_request ), true ) . '</pre>' );

		}*/


	}

	$new_address = '<AddressLine1>' . $package['destination']['address'] . '</AddressLine1>';

	if ( $package['destination']['address_2'] ) {
		$new_address .= '<AddressLine2>' . $package['destination']['address_2'] . '</AddressLine2>';
	}

	$new_address .= '<City>' . $package['destination']['city'] . '</City>';

	$send_request = str_replace( 
		'<ShipTo>			<Address>', 
		'<ShipTo><Address>' . $new_address, $send_request );

	return $send_request;

}

// filter to add shipping class to product name at cart 
// add_filter( 'woocommerce_cart_item_name', 'add_shipping_class', 10, 3 );
function add_shipping_class(  $name, $cart_item, $cart_item_key ) {
	return $name . '<br />' . print_r( $cart_item['data']->get_shipping_class(), 1 ); 
}


add_action( 'woocommerce_shipping_init', 'pt_wc_ups_merger_shipping_init' );
function pt_wc_ups_merger_shipping_init() {

	include_once( 'classes/class-merger.php' );

}


add_filter( 'woocommerce_shipping_methods', 'pt_wc_ups_merger_add_method' );
function pt_wc_ups_merger_add_method( $methods ) {

	$methods['pt-ups-merger'] = 'PT_UPS_MERGER';
	return $methods;

}


add_filter( 'woocommerce_shipping_package_name', 'pt_wc_ups_merger_rename_package', 10, 3 );
function pt_wc_ups_merger_rename_package( $package_name, $i, $package ) {

	if ( ! empty( $package['name'] ) ) {

		$package_name = $package['name'];

	}

	return $package_name;
}
