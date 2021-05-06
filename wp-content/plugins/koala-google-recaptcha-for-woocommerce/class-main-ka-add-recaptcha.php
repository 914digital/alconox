<?php
/**
 * Plugin Name:       Google reCaptcha for WooCommerce    
 * Plugin URI:        https://woocommerce.com/products/koala-google-recaptcha-for-woocommerce
 * Description:       Add reCaptcha to your WooCommerce and WordPress login, registration, reset password and other pages..
 * Version:           1.0.0
 * Author:            KoalaApps
 * Developed By:      KoalaApps
 * Author URI:        http://www.koalaapps.net
 * Support:           http://www.koalaapps.net
 * Domain Path:       /languages
 * TextDomain :       recaptcha_verification
 * WC requires at least: 3.0.9
 * WC tested up to: 4.*.*
 * Woo: 7673745:724159e27ebb90c2e770cafffa65ec6a
/**
 * Define class.
 */

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * Class start.
 */

class Add_Recaptcha {
	/**
	 * Function construct start.
	 */
	public function __construct() {
		/**
		 *Define Global Constants.
		 */
		$this->apbgp_global_constents_vars();
		/**
		 *Load Text Domain.
		 */
		add_action( 'wp_loaded', array( $this, 'accm_init' ) );
		 
		/**
		 *Include other Files.
		 */
		if ( is_admin() ) {
			/**
			 *Include Admin Class.
			 */
			include_once KA_PLUGIN_DIR . 'class-admin-ka-add-recaptcha.php';
		} else {
			/**
			 *Include front clas.
			 */
			include_once KA_PLUGIN_DIR . 'class-frontend-ka-add-recaptcha.php';
		}
		add_action( 'wp_ajax_validation_captchav3', array( $this, 'validation_captchav3' ) );
		add_action( 'wp_ajax_nopriv_validation_captchav3', array( $this, 'validation_captchav3' ) );
	}
	
	
	/**
	 * Function start.
	 */
	public function apbgp_global_constents_vars() {
		if ( ! defined( 'KA_URL' ) ) {
			define( 'KA_URL', plugin_dir_url( __FILE__ ) );
		}
		if ( ! defined( 'KA_BASENAME' ) ) {
			define( 'KA_BASENAME', plugin_basename( __FILE__ ) );
		}
		if ( ! defined( 'KA_PLUGIN_DIR' ) ) {
			define( 'KA_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
		}
	}
	/**
	 * Function accm init start.
	 */
	public function accm_init() {
		if ( function_exists( 'load_plugin_textdomain' ) ) {
			load_plugin_textdomain( 'recaptcha_verification', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
		}
	}

	public function validation_captchav3() {

		$nonce = isset( $_POST['nonce'] ) && '' !== $_POST['nonce'] ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : 0;

		if ( !empty( $_POST['nonce'] ) && ! wp_verify_nonce( $nonce, 'captcha-ajax-nonce' ) ) {
			die( 'Failed ajax security check!' );
		}

		if ( isset( $_POST['current_form'] ) ) {
			parse_str( sanitize_meta('', $_POST['current_form'], ''), $current_form );
		}

		$captcha_token      = isset( $_POST['captcha_token'] ) ? sanitize_text_field( wp_unslash( $_POST['captcha_token'] ) ) : '';
		$recaptcha_url      = 'https://www.google.com/recaptcha/api/siteverify';
		$recaptcha_secret   = get_option('add_captcha_secret_key_field'); // Insert your secret key here
		$recaptcha_response = $captcha_token;
		$recaptcha          = wp_remote_get($recaptcha_url . '?secret=' . $recaptcha_secret . '&response=' . $recaptcha_response);
		$recaptcha          = json_decode((string) $recaptcha);
	  
		if ( isset($recaptcha->success )) {
		 
			die(true);
		} else {
			die(false);
		}
	}
}
if ( class_exists( 'Add_Recaptcha' ) ) {
	new Add_Recaptcha();
}
