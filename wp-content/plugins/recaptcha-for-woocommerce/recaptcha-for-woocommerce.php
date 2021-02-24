<?php

/**
 * Plugin Name: reCAPTCHA for WooCommerce
 * Plugin URI: http://wpeliteplugins.com
 * Description: Add Google reCAPTCHA to WooCommerce Login, registration, lost password form and Checkout page.
 * Version: 1.0.9
 * Author: WPElitePlugins
 * Author URI: http://wpeliteplugins.com
 *
 * Text Domain: recaptcha-for-woocommerce
 * Domain Path: languages
 * 
 * WC tested up to: 4.1.0
 *
 * @package reCAPTCHA for WooCommerce
 * @category Core
 * @author WPElitePlugins
 */
// Exit if accessed directly
if (!defined('ABSPATH'))
    exit;

/**
 * Basic plugin definitions
 * 
 * @package reCAPTCHA for WooCommerce
 * @since 1.0.0
 */
if (!defined('WOO_RECAPTCHA_PLUGIN_VERSION')) {
    define('WOO_RECAPTCHA_PLUGIN_VERSION', '1.0.9'); //Plugin version number
}
if (!defined('WOO_RECAPTCHA_DIR')) {
    define('WOO_RECAPTCHA_DIR', dirname(__FILE__)); // plugin dir
}
if (!defined('WOO_RECAPTCHA_URL')) {
    define('WOO_RECAPTCHA_URL', plugin_dir_url(__FILE__)); // plugin url
}
if (!defined('WOO_RECAPTCHA_ADMIN')) {
    define('WOO_RECAPTCHA_ADMIN', WOO_RECAPTCHA_DIR . '/includes/admin'); // plugin admin dir
}
if (!defined('WOO_RECAPTCHA_PLUGIN_BASENAME')) {
    define('WOO_RECAPTCHA_PLUGIN_BASENAME', basename(WOO_RECAPTCHA_DIR)); //Plugin base name
}
if (!defined('WOO_RECAPTCHA_PLUGIN_KEY')) {
    define('WOO_RECAPTCHA_PLUGIN_KEY', 'woorecaptcha');
}

// Required updater functions file
if (!function_exists('wpeliteplugins_updater_install')) {
    require_once( 'includes/wpeliteplugins-upd-functions.php' );
}

/**
 * Load Text Domain
 * 
 * This gets the plugin ready for translation.
 * 
 * @package reCAPTCHA for WooCommerce
 * @since 1.0.0
 */
function woo_recaptcha_load_text_domain() {

    // Set filter for plugin's languages directory
    $woo_recaptcha_lang_dir = dirname(plugin_basename(__FILE__)) . '/languages/';
    $woo_recaptcha_lang_dir = apply_filters('woo_recaptcha_languages_directory', $woo_recaptcha_lang_dir);

    // Traditional WordPress plugin locale filter
    $locale = apply_filters('plugin_locale', get_locale(), 'recaptcha-for-woocommerce');
    $mofile = sprintf('%1$s-%2$s.mo', 'recaptcha-for-woocommerce', $locale);

    // Setup paths to current locale file
    $mofile_local = $woo_recaptcha_lang_dir . $mofile;
    $mofile_global = WP_LANG_DIR . '/' . WOO_RECAPTCHA_PLUGIN_BASENAME . '/' . $mofile;

    if (file_exists($mofile_global)) { // Look in global /wp-content/languages/recaptcha-for-woocommerce folder
        load_textdomain('recaptcha-for-woocommerce', $mofile_global);
    } elseif (file_exists($mofile_local)) { // Look in local /wp-content/plugins/recaptcha-for-woocommerce/languages/ folder
        load_textdomain('recaptcha-for-woocommerce', $mofile_local);
    } else { // Load the default language files
        load_plugin_textdomain('recaptcha-for-woocommerce', false, $woo_recaptcha_lang_dir);
    }
}

/**
 * Add plugin action links
 *
 * Adds a Settings, Support and Docs link to the plugin list.
 *
 * @package reCAPTCHA for WooCommerce
 * @since 1.0.0
 */
function woo_recaptcha_add_plugin_links($links) {
    $plugin_links = array(
        '<a href="admin.php?page=wc-settings&tab=woo_recaptcha">' . __('Settings', 'recaptcha-for-woocommerce') . '</a>',
        '<a href="http://documents.wpeliteplugins.com/recaptcha-for-woocommerce/">' . __('Docs', 'recaptcha-for-woocommerce') . '</a>'
    );

    return array_merge($plugin_links, $links);
}

add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'woo_recaptcha_add_plugin_links');

//add action to load plugin
add_action('plugins_loaded', 'woo_recaptcha_plugin_loaded');

/**
 * Load Plugin
 * 
 * Handles to load plugin after dependent plugin is loaded successfully
 * 
 * @package reCAPTCHA for WooCommerce
 * @since 1.0.0
 */
function woo_recaptcha_plugin_loaded() {

    //check Woocommerce is activated or not
    if (class_exists('Woocommerce')) {

        // load first plugin text domain
        woo_recaptcha_load_text_domain();

        //global variables
        global $woo_recaptcha_scripts, $woo_recaptcha_public, $woo_recaptcha_settings_tabs;

        //Public Class to handles most of functionalities of public side
        require_once( WOO_RECAPTCHA_DIR . '/includes/class-woo-recaptcha-public.php');
        $woo_recaptcha_public = new Woo_Recaptcha_Public();
        $woo_recaptcha_public->add_hooks();

        // Script Class to manage all scripts and styles
        include_once( WOO_RECAPTCHA_DIR . '/includes/class-woo-recaptcha-scripts.php' );
        $woo_recaptcha_scripts = new Woo_Recaptcha_Scripts();
        $woo_recaptcha_scripts->add_hooks();

        //Settings Tab class for handling settings tab content
        require_once( WOO_RECAPTCHA_ADMIN . '/class-woo-recaptcha-admin-settings-tabs.php' );
        $woo_recaptcha_settings_tabs = new Woo_Recaptcha_Settings_Tabs();
        $woo_recaptcha_settings_tabs->add_hooks();
    } //end if to check class Woocommerce is exist or not
}

//end if to check plugin loaded is called or not
//check WPElite Plugins Updater is activated
if (class_exists('WPElitePlugins_Upd_Admin')) {

    // Plugin updates
    wpeliteplugins_queue_update(plugin_basename(__FILE__), WOO_RECAPTCHA_PLUGIN_KEY);

    /**
     * Include Auto Updating Files
     * 
     * @package reCAPTCHA for WooCommerce
     * @since 1.0.1
     */
    require_once( WPELITEPLUGINS_UPD_DIR . '/updates/class-plugin-update-checker.php' ); // auto updating

    $WPEliteWooRecaptchaUpdateChecker = new WPElitePluginsUpdateChecker(
            'http://wpeliteplugins.com/Updates/recaptcha-for-woocommerce/license-info.php', __FILE__, WOO_RECAPTCHA_PLUGIN_KEY
    );

    /**
     * Auto Update
     * 
     * Get the license key and add it to the update checker.
     * 
     * @package reCAPTCHA for WooCommerce
     * @since 1.0.1
     */
    function woo_recaptcha_add_secret_key($query) {

        $plugin_key = WOO_RECAPTCHA_PLUGIN_KEY;

        $query['lickey'] = wpeliteplugins_get_plugin_purchase_code($plugin_key);
        return $query;
    }

    $WPEliteWooRecaptchaUpdateChecker->addQueryArgFilter('woo_recaptcha_add_secret_key');
} // end check WPElitePlugins Updater is activated

