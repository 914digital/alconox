<?php
/**
 * Define fille.

 * @package WOOCP
 */
if (! defined('ABSPATH') ) {
	exit; // restict for direct access.
}

if (! class_exists('Admin_Add_Recaptcha') ) {

	/**
	 * Class start.
	 
	 * @package WOOCP
	 */
	class Admin_Add_Recaptcha extends Add_Recaptcha {
	
		/**
		 * Function construct start.
		 */
		public function __construct() {

			add_action('admin_menu', array($this, 'add_recaptcha_menu'));
			add_action('admin_init', array($this, 'ka_add_register_fields'));
		
		
		}

		/**
		 * Function construct start.
		 */
		public function add_recaptcha_menu() {
			add_menu_page(
				
				'reCaptcha', // Page title.
				esc_html__('reCaptcha', 'recaptcha_verification'), // Title.
				'manage_options', // Capability.
				'ka_captcha', // slug.
				array( $this, 'create_recaptcha_setting_page' ),
				plugins_url( 'Assets/img/logo.png', __FILE__ )
				
			); // Callback.
  

		}
		
		

		/**
		 * Function construct start.
		 */
		public function create_recaptcha_setting_page() {
			global $active_tab;
			if (isset($_POST['base_infinite_nonce'])  
				&& wp_verify_nonce(
					sanitize_text_field(
						wp_unslash(
							$_POST['base_infinite_nonce'], 
							'infinite_nonce_action'
						)
					)
				) 
			) {
				print 'Sorry, your nonce did not verify.';
				exit;
			}
			if (isset($_GET['tab']) ) {
				$active_tab = sanitize_text_field(wp_unslash($_GET['tab']));
			} else {
				$active_tab = 'tab_one';
			}?>
					<div class="wrap">
					<!-- Title above Tabs -->
					<h2> 
					<?php 
					echo esc_html__('reCaptcha Settings', 'recaptcha_verification');
					?>
					</h2>
					<?php settings_errors(); ?>
					<h2 class="nav-tab-wrapper">
					<!-- General Setting Tab -->
					<a href="?page=ka_captcha&tab=tab_one" class="nav-tab <?php echo 'tab_one' === $active_tab ? 'nav-tab-active' : ''; ?>"><?php esc_html_e('General Settings', 'recaptcha_verification'); ?></a>
					<a href="?page=ka_captcha&tab=tab_two" class="nav-tab <?php echo 'tab_two' === $active_tab ? 'nav-tab-active' : ''; ?>"><?php esc_html_e('WC Registration', 'recaptcha_verification'); ?></a>
					<a href="?page=ka_captcha&tab=tab_three" class="nav-tab <?php echo 'tab_three' === $active_tab ? 'nav-tab-active' : ''; ?>"><?php esc_html_e('WC Login', 'recaptcha_verification'); ?></a>
					<a href="?page=ka_captcha&tab=tab_four" class="nav-tab <?php echo 'tab_four' === $active_tab ? 'nav-tab-active' : ''; ?>"><?php esc_html_e('WC Lost Password', 'recaptcha_verification'); ?></a>
					<a href="?page=ka_captcha&tab=tab_five" class="nav-tab <?php echo 'tab_five' === $active_tab ? 'nav-tab-active' : ''; ?>"><?php esc_html_e('WC Checkout', 'recaptcha_verification'); ?></a>
					<a href="?page=ka_captcha&tab=tab_six" class="nav-tab <?php echo 'tab_six' === $active_tab ? 'nav-tab-active' : ''; ?>"><?php esc_html_e('WP Registration', 'recaptcha_verification'); ?></a>
					<a href="?page=ka_captcha&tab=tab_seven" class="nav-tab <?php echo 'tab_seven' === $active_tab ? 'nav-tab-active' : ''; ?>"><?php esc_html_e('WP Login', 'recaptcha_verification'); ?></a>
					<a href="?page=ka_captcha&tab=tab_eight" class="nav-tab <?php echo 'tab_eight' === $active_tab ? 'nav-tab-active' : ''; ?>"><?php esc_html_e('WP Lost Password', 'recaptcha_verification'); ?></a>
					<a href="?page=ka_captcha&tab=tab_nine" class="nav-tab <?php echo 'tab_nine' === $active_tab ? 'nav-tab-active' : ''; ?>"><?php esc_html_e('WC Payment Method', 'recaptcha_verification'); ?></a>
					<a href="?page=ka_captcha&tab=tab_ten" class="nav-tab <?php echo 'tab_ten' === $active_tab ? 'nav-tab-active' : ''; ?>"><?php esc_html_e('WC Pay for order', 'recaptcha_verification'); ?></a>    
						</h2>
					<form method="POST" action="options.php" >
			<?php
			if ('tab_one' === $active_tab ) {
				settings_fields('wp_captcha_general');
				do_settings_sections('wp_recaptcha_general');
			} elseif ('tab_two' === $active_tab ) {
				settings_fields('captcha-woo-regs');
				do_settings_sections('recaptcha-woo-regs');
			} elseif ('tab_three' === $active_tab ) {
				settings_fields('captcha-woo-login');
				do_settings_sections('recaptcha-woo-login');
			} elseif ('tab_four' === $active_tab ) {
				settings_fields('captcha-woo-lpass');
				do_settings_sections('recaptcha-woo-lpass');
			} elseif ('tab_five' === $active_tab ) {
				settings_fields('captcha-woo-guest-checkout');
				do_settings_sections('recaptcha-woo-guest-checkout');
			} elseif ('tab_six' === $active_tab ) {
				settings_fields('wp_captcha_registration');
				do_settings_sections('wp_recaptcha_registration');
			} elseif ('tab_seven' === $active_tab ) {
				settings_fields('wp_captcha_login');
				do_settings_sections('wp_recaptcha_login');
			} elseif ('tab_eight' === $active_tab ) {
				settings_fields('wp_captcha_lostpassword');
				do_settings_sections('wp_recaptcha_lostpassword');
			} elseif ('tab_nine' === $active_tab ) {
				settings_fields('captcha-p_method-settings');
				do_settings_sections('recaptcha-p_method-settings');
			} elseif ('tab_ten' === $active_tab ) {
				settings_fields('captcha-p-order-settings');
				do_settings_sections('recaptcha-p-order-settings');
			}
			?>
			<?php submit_button(); ?>
				
							  </form>
				  </div>
			<?php
				
		}
		//wordpress backend.
		
	
		/**
		 * Function construct start.
		 */
		public function ka_add_register_fields() {
		   
			   include_once KA_PLUGIN_DIR . '/inc/settings/general_settings_bckend.php';
			   include_once KA_PLUGIN_DIR . '/inc/settings/woo_registration_page_settings.php';
			   include_once KA_PLUGIN_DIR . '/inc/settings/woo_login_page_settings.php';
			   include_once KA_PLUGIN_DIR . '/inc/settings/woo_lost_password_setting_captcha.php';
			   include_once KA_PLUGIN_DIR . '/inc/settings/woo_checkout_page.php';
			   include_once KA_PLUGIN_DIR . '/inc/settings/wp_login_captcha_setting.php';
			   include_once KA_PLUGIN_DIR . '/inc/settings/wp_registration_captcha_setting.php';
			   include_once KA_PLUGIN_DIR . '/inc/settings/wp_lost_password_page_captcha_settings.php';
			   include_once KA_PLUGIN_DIR . '/inc/settings/woo_payment_method_captcha_settings.php';
			   include_once KA_PLUGIN_DIR . '/inc/settings/woo_pay_for_order_captcha.php';
		}
	}
	
	new Admin_Add_Recaptcha();
}


