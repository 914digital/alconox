<?php
/**
 * Plugin Name:       Shipment Detail in Admin Email
 * Plugin URI:        https://creativehassan.com
 * Description:       Shipment Detail in Admin Email
 * Version:           1.0.0
 * Author:            Hassan Ali
 * Author URI:        https://creativehassan.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       shipment-details-admin-email
 */

// If this file is called directly, abort.
if (! defined('WPINC')) {
    die;
}

add_filter('woocommerce_email_order_details', 'add_shipment_details_admin_email', 9, 4);
function add_shipment_details_admin_email($order, $sent_to_admin, $plain_text, $email)
{
    if ($sent_to_admin) {
        if ($order->has_shipping_method('use-my-shipping-account')) {
            echo '<p><strong>' . esc_html__('Shipping account:', 'pt-wc-use-my-shipping-account') . '</strong> ' . $order->get_meta('_shipping_account') . '</p>';

            if ($order->get_meta('_ground_shipping_service')) {
                echo '<p><strong>' . esc_html__('Ground shipping service:', 'pt-wc-use-my-shipping-account') . '</strong> ' . $order->get_meta('_ground_shipping_service') . '</p>';
            }

            if ($order->get_meta('_shipping_service')) {
                echo '<p><strong>' . esc_html__('Air shipping service:', 'pt-wc-use-my-shipping-account') . '</strong> ' . $order->get_meta('_shipping_service') . '</p>';
            }

            if ($order->get_meta('_both_shipping_service')) {
                echo '<p><strong>' . esc_html__('Shipping services:', 'pt-wc-use-my-shipping-account') . '</strong> ' . $order->get_meta('_both_shipping_service') . '</p>';
            }
        }
    }
}
