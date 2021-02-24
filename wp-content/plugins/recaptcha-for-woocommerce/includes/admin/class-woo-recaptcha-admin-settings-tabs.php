<?php

// Exit if accessed directly
if (!defined('ABSPATH'))
    exit;

/**
 * Setting page Class
 * 
 * Handles Settings page functionality of plugin
 * 
 * @package reCAPTCHA for WooCommerce
 * @since 1.0.0
 */
class Woo_Recaptcha_Settings_Tabs {

    function __construct() {
        # code...
    }

    /**
     * Add plugin settings
     * 
     * Handles to add plugin settings in Min/Max Quantites Settings Tab
     * 
     * @package reCAPTCHA for WooCommerce
     * @since 1.0.0
     */
    public function woo_recaptcha_get_settings() {

        $languages = array(
            '' => __('Auto Detect', 'recaptcha-for-woocommerce'),
            'ar' => __('Arabic', 'recaptcha-for-woocommerce'),
            'bg' => __('Bulgarian', 'recaptcha-for-woocommerce'),
            'ca' => __('Catalan', 'recaptcha-for-woocommerce'),
            'zh-CN' => __('Chinese (Simplified)', 'recaptcha-for-woocommerce'),
            'zh-TW' => __('Chinese (Traditional)', 'recaptcha-for-woocommerce'),
            'hr' => __('Croatian', 'recaptcha-for-woocommerce'),
            'cs' => __('Czech', 'recaptcha-for-woocommerce'),
            'da' => __('Danish', 'recaptcha-for-woocommerce'),
            'nl' => __('Dutch', 'recaptcha-for-woocommerce'),
            'en-GB' => __('English (UK)', 'recaptcha-for-woocommerce'),
            'en' => __('English (US)', 'recaptcha-for-woocommerce'),
            'fil' => __('Filipino', 'recaptcha-for-woocommerce'),
            'fi' => __('Finnish', 'recaptcha-for-woocommerce'),
            'fr' => __('French', 'recaptcha-for-woocommerce'),
            'fr-CA' => __('French (Canadian)', 'recaptcha-for-woocommerce'),
            'de' => __('German', 'recaptcha-for-woocommerce'),
            'de-AT' => __('German (Austria)', 'recaptcha-for-woocommerce'),
            'de-CH' => __('German (Switzerland)', 'recaptcha-for-woocommerce'),
            'el' => __('Greek', 'recaptcha-for-woocommerce'),
            'iw' => __('Hebrew', 'recaptcha-for-woocommerce'),
            'hi' => __('Hindi', 'recaptcha-for-woocommerce'),
            'hu' => __('Hungarain', 'recaptcha-for-woocommerce'),
            'id' => __('Indonesian', 'recaptcha-for-woocommerce'),
            'it' => __('Italian', 'recaptcha-for-woocommerce'),
            'ja' => __('Japanese', 'recaptcha-for-woocommerce'),
            'ko' => __('Korean', 'recaptcha-for-woocommerce'),
            'lv' => __('Latvian', 'recaptcha-for-woocommerce'),
            'lt' => __('Lithuanian', 'recaptcha-for-woocommerce'),
            'no' => __('Norwegian', 'recaptcha-for-woocommerce'),
            'fa' => __('Persian', 'recaptcha-for-woocommerce'),
            'pl' => __('Polish', 'recaptcha-for-woocommerce'),
            'pt' => __('Portuguese', 'recaptcha-for-woocommerce'),
            'pt-BR' => __('Portuguese (Brazil)', 'recaptcha-for-woocommerce'),
            'pt-PT' => __('Portuguese (Portugal)', 'recaptcha-for-woocommerce'),
            'ro' => __('Romanian', 'recaptcha-for-woocommerce'),
            'ru' => __('Russian', 'recaptcha-for-woocommerce'),
            'sr' => __('Serbian', 'recaptcha-for-woocommerce'),
            'sk' => __('Slovak', 'recaptcha-for-woocommerce'),
            'sl' => __('Slovenian', 'recaptcha-for-woocommerce'),
            'es' => __('Spanish', 'recaptcha-for-woocommerce'),
            'es-419' => __('Spanish (Latin America)', 'recaptcha-for-woocommerce'),
            'sv' => __('Swedish', 'recaptcha-for-woocommerce'),
            'th' => __('Thai', 'recaptcha-for-woocommerce'),
            'tr' => __('Turkish', 'recaptcha-for-woocommerce'),
            'uk' => __('Ukrainian', 'recaptcha-for-woocommerce'),
            'vi' => __('Vietnamese', 'recaptcha-for-woocommerce')
        );

        $languages = apply_filters('woo_recaptcha_add_language', $languages);

        // Global Settings for reCAPTCHA
        $woo_recaptcha_settings = array(
            array(
                'name' => __('Google reCAPTCHA Settings', 'recaptcha-for-woocommerce'),
                'type' => 'title',
                'desc' => '',
                'id' => 'woo_recaptcha_settings'
            ),
            array(
                'name' => __('Site Key', 'recaptcha-for-woocommerce'),
                'desc' => sprintf(__('Used for displaying the reCAPTCHA. Grab it %sHere%s.', 'recaptcha-for-woocommerce'), '<a target="_blank" href="https://www.google.com/recaptcha/admin">', '<a/>'),
                'id' => 'woo_recaptcha_site_key',
                'type' => 'text',
                'class' => 'large-text'
            ),
            array(
                'name' => __('Secret Key', 'recaptcha-for-woocommerce'),
                'desc' => sprintf(__('Used for communication between your site and Google. Grab it %sHere%s.', 'recaptcha-for-woocommerce'), '<a target="_blank" href="https://www.google.com/recaptcha/admin">', '<a/>'),
                'id' => 'woo_recaptcha_secret_key',
                'type' => 'text',
                'class' => 'large-text'
            ),
            array(
                'name' => __('Theme', 'recaptcha-for-woocommerce'),
                'desc' => __('Select reCAPTCHA theme', 'recaptcha-for-woocommerce'),
                'id' => 'woo_recaptcha_theme',
                'type' => 'select',
                'css' => 'min-width: 250px;',
                'default' => 'light',
                'options' => array(
                    'light' => __('Light', 'recaptcha-for-woocommerce'),
                    'dark' => __('Dark', 'recaptcha-for-woocommerce')
                )
            ),
            array(
                'name' => __('Language', 'recaptcha-for-woocommerce'),
                'desc' => __('Select reCAPTCHA language', 'recaptcha-for-woocommerce'),
                'id' => 'woo_recaptcha_language',
                'type' => 'select',
                'css' => 'min-width: 250px;',
                'default' => '',
                'options' => $languages
            ),
            array(
                'name' => __('Size', 'recaptcha-for-woocommerce'),
                'desc' => __('Select reCAPTCHA size', 'recaptcha-for-woocommerce'),
                'id' => 'woo_recaptcha_size',
                'type' => 'select',
                'css' => 'min-width: 250px;',
                'default' => 'normal',
                'options' => array(
                    'normal' => __('Normal', 'recaptcha-for-woocommerce'),
                    'compact' => __('Compact', 'recaptcha-for-woocommerce')
                )
            ),
            array(
                'name' => __('Error Message', 'recaptcha-for-woocommerce'),
                'desc' => __('Enter the Error Message to display when reCAPTCHA is ignored or it is invalid.', 'recaptcha-for-woocommerce'),
                'id' => 'woo_recaptcha_error_message',
                'type' => 'text',
                'default' => sprintf(__('Please retry CAPTCHA', 'recaptcha-for-woocommerce')),
                'class' => 'large-text'
            ),
            array(
                'type' => 'sectionend',
                'id' => 'woo_recaptcha_settings'
            ),
            array(
                'name' => __('Display Settings', 'recaptcha-for-woocommerce'),
                'type' => 'title',
                'desc' => '',
                'id' => 'woo_recaptcha_display_settings'
            ),
            array(
                'title' => __('Login Form', 'recaptcha-for-woocommerce'),
                'desc' => __('Check this box to enable reCAPTCHA in WooCommerce login form.', 'recaptcha-for-woocommerce'),
                'id' => 'woo_recaptcha_login',
                'default' => 'no',
                'type' => 'checkbox'
            ),
            array(
                'title' => __('Registration Form', 'recaptcha-for-woocommerce'),
                'desc' => __('Check this box to enable reCAPTCHA in WooCommerce registration form.', 'recaptcha-for-woocommerce'),
                'id' => 'woo_recaptcha_registration',
                'default' => 'no',
                'type' => 'checkbox'
            ),
            array(
                'title' => __('Lost Password Form', 'recaptcha-for-woocommerce'),
                'desc' => __('Check this box to enable reCAPTCHA in WooCommerce lost password form.', 'recaptcha-for-woocommerce'),
                'id' => 'woo_recaptcha_lost_password',
                'default' => 'no',
                'type' => 'checkbox'
            ),
            array(
                'title' => __('Checkout Page', 'recaptcha-for-woocommerce'),
                'desc' => __('Check this box to enable reCAPTCHA in WooCommerce checkout page.', 'recaptcha-for-woocommerce'),
                'id' => 'woo_recaptcha_checkout',
                'default' => 'no',
                'type' => 'checkbox'
            ),
            array(
                'title' => __('Captcha position', 'recaptcha-for-woocommerce'),
                'desc' => __('Select reCAPTCHA  position on checkout page', 'recaptcha-for-woocommerce'),
                'id' => 'woo_recaptcha_checkout_position',
                'type' => 'select',
                'css' => 'min-width: 250px;',
                'default' => 'after_checkout_form',
                'options' => array(
                    'after_checkout_form' => __('After Checkout Form', 'recaptcha-for-woocommerce'),
                    'before_chekout_form' => __('Before Checkout Form', 'recaptcha-for-woocommerce'),
                    'checkout_order_review' => __('Checkout Order Review', 'woo_reptcha'),
                    'checkout_after_order_review' => __('After Checkout Order Review', 'woo_reptcha'),
                    'before_place_order' => __('Before Place Order Button', 'woo_reptcha')
                )
            ),
            array(
                'type' => 'sectionend',
                'id' => 'woo_recaptcha_display_settings'
            ),
            array(
                'name' => __('Misc Settings', 'recaptcha-for-woocommerce'),
                'type' => 'title',
                'desc' => '',
                'id' => 'woo_recaptcha_misc_settings'
            ),
            array(
                'title' => __('reCAPTCHA Not Working?', 'recaptcha-for-woocommerce'),
                'desc' => __('By default, reCAPTCHA will only work on WooCommerce pages. Check this box, If your theme using custom popup/page for WooCommerce login/registration and reCAPTCHA is not working.', 'recaptcha-for-woocommerce'),
                'id' => 'woo_recaptcha_inlcude_script',
                'default' => 'no',
                'type' => 'checkbox'
            ),
            array(
                'type' => 'sectionend',
                'id' => 'woo_recaptcha_misc_settings'
            ),
        );

        return $woo_recaptcha_settings;
    }

    /**
     * Settings Tab
     * 
     * Adds the reCapcha tab to the WooCommerce settings page.
     *
     * @package reCAPTCHA for WooCommerce
     * @since 1.0.0
     */
    public function woo_recaptcha_add_settings_tab($tabs) {

        $tabs['woo_recaptcha'] = __('reCAPTCHA', 'recaptcha-for-woocommerce');

        return $tabs;
    }

    /**
     * Settings Tab Content
     * 
     * Adds the settings content to the min/max qunatities tab.
     *
     * @package reCAPTCHA for WooCommerce
     * @since 1.0.0
     */
    public function woo_recaptcha_settings_tab_content() {

        woocommerce_admin_fields($this->woo_recaptcha_get_settings());
    }

    /**
     * Update Settings
     * 
     * Updates the settings when being saved.
     *
     *  @package reCAPTCHA for WooCommerce
     * @since 1.0.0
     */
    public function woo_recaptcha_update_settings() {

        woocommerce_update_options($this->woo_recaptcha_get_settings());
    }

    /**
     * Adding Hooks
     * 
     * Adding proper hooks for the shortcodes.
     * 
     * @package reCAPTCHA for WooCommerce
     * @since 1.0.0
     */
    public function add_hooks() {

        // Add filter to addd Min/Max Quantities tab on woocommerce setting page
        add_filter('woocommerce_settings_tabs_array', array($this, 'woo_recaptcha_add_settings_tab'), 99);

        // Add action to add Min/Max Quantities tab content
        add_action('woocommerce_settings_tabs_woo_recaptcha', array($this, 'woo_recaptcha_settings_tab_content'));

        // Add action to save custom update content
        add_action('woocommerce_update_options_woo_recaptcha', array($this, 'woo_recaptcha_update_settings'), 100);
    }

}

?>