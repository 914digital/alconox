<?php

// Exit if accessed directly
if (!defined('ABSPATH'))
    exit;

/** public class
 * 
 * @package reCAPTCHA for WooCommerce
 * @since 1.0.0
 */
class Woo_Recaptcha_Public {

    /**
     * Reacaptcha Site Key
     *
     * @var string
     */
    private $sitekey;

    /**
     * Recaptcha Theme
     *
     * @var string
     */
    private $theme;

    /**
     * Recaptcha Size
     *
     * @var string
     */
    private $size;

    /**
     * Count Total Recaptcha on Page
     *
     * @var int
     */
    private static $captcha_count = 0;

    function __construct() {

        // get size key
        $this->sitekey = get_option('woo_recaptcha_site_key');

        // get theme
        $this->theme = get_option('woo_recaptcha_theme');

        // get size
        $this->size = get_option('woo_recaptcha_size');
    }

    /**
     * Return total captcha
     *
     * @return init
     */
    public function woo_total_captcha() {

        return self::$captcha_count;
    }

    /**
     * Handle to add action and filter if particular display settins is enabled
     *
     * @package reCAPTCHA for WooCommerce
     * @since 1.0.0
     */
    public function woo_recaptcha_display() {

        // check if login is enabled
        $login = get_option('woo_recaptcha_login');
        if (!empty($login) && $login == 'yes') {

            // add action to display captcha to the login form
            add_filter('woocommerce_login_form', array($this, 'woo_display_login_recaptcha'));

            // add action to validate recaptcha
            add_action('woocommerce_process_login_errors', array($this, 'woo_validate_login_recaptcha'), 15, 3);
        }

        // Check if registration is activated
        $registration = get_option('woo_recaptcha_registration');
        if (!empty($registration) && $registration == 'yes') {

            // add action to display captcha to the registration form
            add_action('woocommerce_register_form', array($this, 'woo_display_register_recaptcha'));

            // add action to validate recaptcha
            add_filter('woocommerce_registration_errors', array($this, 'woo_validate_registration_recaptcha'), 10, 3);
        }

        // check if lost paassword is activated
        $lost_password = get_option('woo_recaptcha_lost_password');
        if (!empty($lost_password) && $lost_password == 'yes') {

            // add action to display captcha to lost password form
            add_filter('woocommerce_lostpassword_form', array($this, 'woo_display_lostpassword_recaptcha'));

            // add action to validate recaptcha
            //add_action('allow_password_reset', array($this, 'woo_validate_lost_password_recaptcha'), 10, 2);
            add_action( 'lostpassword_post', array( $this, 'woo_validate_lost_password_recaptcha' ), 10 );
        }

        // check if checkout recaptcha is activated
        $checkout = get_option('woo_recaptcha_checkout');
        if (!empty($checkout) && $checkout == 'yes') {

            // get position of recaptcha on checkout page
            $position = get_option('woo_recaptcha_checkout_position');

            // add action to display capcaptcha on checkout page																	
            if (empty($position) || $position == 'after_checkout_form') {
                add_action('woocommerce_after_checkout_billing_form', array($this, 'woo_display_checkout_recaptcha'));
            } elseif ($position == 'before_chekout_form') {
                add_action('woocommerce_before_checkout_form', array($this, 'woo_display_checkout_recaptcha'));
            } elseif ($position == 'checkout_order_review') {
                add_action('woocommerce_checkout_order_review', array($this, 'woo_display_checkout_recaptcha'));
            } elseif ($position == 'checkout_after_order_review') {
                add_action('woocommerce_checkout_after_order_review', array($this, 'woo_display_checkout_recaptcha'), 0);
            } else if ($position == 'before_place_order') {
                add_action('woocommerce_review_order_before_submit', array($this, 'woo_display_checkout_bpo_recaptcha'));
            }

            // add action to validate recaptcha
            add_action('woocommerce_checkout_process', array($this, 'woo_validate_checkout_recaptcha'), 10);
        }
    }

    /**
     * Output the reCAPTCHA field on login form
     * 
     * @package reCAPTCHA for WooCommerce
     * @since 1.0.2
     */
    public function woo_display_login_recaptcha() {

        self::$captcha_count++;

        $toal_captcha = $this->woo_total_captcha();

        if (is_checkout())  // if its checkout page
            $class = apply_filters('woo_display_login_recaptcha', 'form-row');
        else      // if its my account page
            $class = apply_filters('woo_display_login_recaptcha', 'woocommerce-FormRow woocommerce-FormRow--wide form-row form-row-wide');

        echo '<div class="woo_recaptcha_field ' . $class . '">
                        <div data-sitekey="' . $this->sitekey . '" id="woo_recaptcha_field_' . $toal_captcha . '" class="g-recaptcha"></div>
                </div>';
    }

    /**
     * Output the reCAPTCHA field on registraiton form
     * 
     * @package reCAPTCHA for WooCommerce
     * @since 1.0.2
     */
    public function woo_display_register_recaptcha() {

        self::$captcha_count++;

        $toal_captcha = $this->woo_total_captcha();

        $class = apply_filters('woo_display_register_recaptcha', 'woocommerce-FormRow woocommerce-FormRow--wide form-row form-row-wide');

        echo '<div class="woo_recaptcha_field ' . $class . '">
                        <div data-sitekey="' . $this->sitekey . '" id="woo_recaptcha_field_' . $toal_captcha . '" class="g-recaptcha"></div>
                </div>';
    }

    /**
     * Output the reCAPTCHA field on lost password form
     * 
     * @package reCAPTCHA for WooCommerce
     * @since 1.0.2
     */
    public function woo_display_lostpassword_recaptcha() {

        self::$captcha_count++;

        $toal_captcha = $this->woo_total_captcha();

        $class = apply_filters('woo_display_lostpassword_recaptcha', 'woocommerce-FormRow woocommerce-FormRow--first form-row');

        echo '<div class="woo_recaptcha_field ' . $class . '">
                        <div data-sitekey="' . $this->sitekey . '" id="woo_recaptcha_field_' . $toal_captcha . '" class="g-recaptcha"></div>
                </div>';
    }

    /**
     * Output the reCAPTCHA field 
     * Before place order button on checkout page
     * 
     * @package reCAPTCHA for WooCommerce
     * @since 1.0.2
     */
    public function woo_display_checkout_bpo_recaptcha() {

        $class = apply_filters('woo_display_checkout_bpo_recaptcha', '');

        $style = apply_filters('woo_display_checkout_bpo_recaptcha_style', 'display:inline-block;float:left;');

        echo '<div class="woo_recaptcha_field ' . $class . '" style="' . $style . '">
                        <div data-sitekey="' . $this->sitekey . '" id="woo_recaptcha_field_2" class="g-recaptcha"></div>
                </div>';
    }

    /**
     * Output the reCAPTCHA field on checkout page
     * 
     * @package reCAPTCHA for WooCommerce
     * @since 1.0.0
     */
    public function woo_display_checkout_recaptcha() {

        self::$captcha_count++;

        $toal_captcha = $this->woo_total_captcha();

        echo '<div class="woo_recaptcha_field form-row form-row-wide"><div data-sitekey="' . $this->sitekey . '" id="woo_recaptcha_field_' . $toal_captcha . '" class="g-recaptcha"></div></div>';
    }

    /**
     * Verify the captcha answer on login form
     *
     * @package reCAPTCHA for WooCommerce
     * @since 1.0.0
     * 
     * @param $user string login username
     * @param $password string login password
     *
     * @return WP_Error|WP_user
     */
    public function woo_validate_login_recaptcha($validation_error, $username, $password) {

        $error_message = get_option('woo_recaptcha_error_message');

        if (!isset($_POST['g-recaptcha-response']) || !$this->woo_verify_recaptcha()) {
            $validation_error = new WP_Error('recaptcha_error', $error_message);
        }

        return $validation_error;
    }

    /**
     * Verify the captcha answer on registration form
     *
     * @package reCAPTCHA for WooCommerce
     * @since 1.0.0
     * 
     * @param $validation_errors
     * @param $username
     * @param $email
     *
     * @return WP_Error
     */
    public function woo_validate_registration_recaptcha($validation_errors, $username, $email) {

        // to check that function is called from registeration form	
        if (isset($_POST['register']) && !empty($_POST['register'])) {

            $error_message = get_option('woo_recaptcha_error_message');

            if (!isset($_POST['g-recaptcha-response']) || !$this->woo_verify_recaptcha()) {
                $validation_errors = new WP_Error('recaptcha_error', $error_message);
            }
        }

        return $validation_errors;
    }

    /**
     * Verify the captcha answer on lost password form.
     *
     * @package reCAPTCHA for WooCommerce
     * @since 1.0.0
     * 
     * @return WP_Error
     */
    public function woo_validate_lost_password_recaptcha( $errros ) {

        $error_message = get_option('woo_recaptcha_error_message');

        if (!isset($_POST['g-recaptcha-response']) || !$this->woo_verify_recaptcha()) {
            //return new WP_Error('recaptcha_error', $error_message);
            $errros->add( 'recaptcha_error', $error_message );
        }

        return $errros;
    }

    /**
     * Verify the captcha answer on checkout page
     *
     * @param unknown_type $validation_errors
     * @param unknown_type $username
     * @param unknown_type $email
     * 
     * @package reCAPTCHA for WooCommerce
     * @since 1.0.0
     */
    public function woo_validate_checkout_recaptcha() {

        $error_message = get_option('woo_recaptcha_error_message');

        if (!isset($_POST['g-recaptcha-response']) || !$this->woo_verify_recaptcha()) {

            // get position of recaptcha on checkout page
            $position = apply_filters('woo_recaptcha_checkout_position', get_option('woo_recaptcha_checkout_position'));

            // get recaptcha verified value
            $g_recaptcha_verified = WC()->session->get('g_recaptcha_verified');

            // check that recaptcha on checkout is already verified. if yes then no need to validation again 
            // as google recaptcha not validate second time untill page is refreshed
            // also check that position is not place order because it will reset on place order reload
            if ($g_recaptcha_verified === true && $position != 'before_place_order') {
                return;
            }

            wc_add_notice($error_message, 'error');
        }
    }

    /**
     * Verify capcha selected by user
     *
     * @package reCAPTCHA for WooCommerce
     * @since 1.0.0 
     * 
     * @return bool
     */
    public function woo_verify_recaptcha() {

        // get secret key
        $secret_key = get_option('woo_recaptcha_secret_key');

        // get user selected value
        $response = isset($_POST['g-recaptcha-response']) ? esc_attr($_POST['g-recaptcha-response']) : '';

        // remote ip
        $remote_ip = $_SERVER["REMOTE_ADDR"];

        // make a GET request to the Google reCAPTCHA Server
        $request = wp_remote_get(
                'https://www.google.com/recaptcha/api/siteverify?secret=' . $secret_key . '&response=' . $response . '&remoteip=' . $remote_ip
        );

        // get the request response body
        $response_body = wp_remote_retrieve_body($request);

        $result = json_decode($response_body, true);

        if (!empty($result['success'])) {

            // if recaptcha is validated then set session variable so that problem will not come on second time
            WC()->session->set('g_recaptcha_verified', true);
        }

        return $result['success'];
    }

    /**
     * Clear session variable if its set.
     *
     * @package reCAPTCHA for WooCommerce
     * @since 1.0.0 
     */
    public function woo_recaptcha_clear_session_var($order_id) {

        WC()->session->set('g_recaptcha_verified', 'null');
    }

    /**
     * Add hooks
     *
     * @package reCAPTCHA for WooCommerce
     * @since 1.0.0
     */
    public function add_hooks() {

        // add action to display recaptcha if any display settings is enbabled
        add_action('init', array($this, 'woo_recaptcha_display'));

        // add action to clear recaptcha variable from session
        add_action('woocommerce_checkout_order_processed', array($this, 'woo_recaptcha_clear_session_var'));
    }

}