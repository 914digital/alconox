<?php

/**
 * Front file of Module
 *
 * Manage actions of front
 *
 * @package addify-abandoned-cart-recovery/includes
 * @version 1.0.0
 */

if (! defined('ABSPATH') ) {
	exit; // Exit if accessed directly.
}

if (! class_exists('Ka_Recaptcha_Front_End') ) {
	 /**
	 * Front class of module.
	 */
	class Ka_Recaptcha_Front_End {
	
		/**
		* Define class.
		*/
		public function __construct() {
			$v3 =get_option('captcha_type_option'); 
			if (0 == $v3) {//adding recaptcha V3.
				
			
				if (get_option('add_captcha_enable_check')==1) {
					add_action('woocommerce_register_form', array( $this,'captcha_v3_woo_registration'));// woo registration

				}

				if (get_option('login_add_captcha_enable_check')==1) {
					add_action('woocommerce_login_form', array( $this,'captcha_v3_woo_login'));//woo login
				}


				if (get_option('woo_lpass_add_captcha_enable_check')==1) {
					add_action('woocommerce_lostpassword_form', array( $this,'captcha_v3_woo_lostpassword'));//woo lost password

				}

				if (get_option('guest_checkout_add_captcha_enable_check')==1) {
					add_action('woocommerce_review_order_before_submit', array( $this, 'captcha_v3_woo_checkout'));
				}// checkout
		  
		
				if (get_option('pay_order_add_captcha_enable_check')==1) {
					add_action('woocommerce_pay_order_before_submit', array( $this,'captcha_wp_v3_pay_for_order'));// pay for order
				} 

		  
				if (get_option('wp_login_captcha_check')==1) {
					add_action('login_form', array( $this,'captcha_v3_wp_login'));// WP login form
				}
	
				if (get_option('p_method_add_captcha_enable_check')==1) { 
					add_action('woocommerce_review_order_before_payment', array( $this,'captcha_v3_woo_payment_method'));
				}


				if (get_option('wp_regs_captcha_check')==1) {
					add_action('register_form', array( $this,'captcha_v3_wp_registration'));// WP registration form
				}

				if (get_option('wp_lpass_page_enable_check')==1) {
					add_action('lostpassword_form', array( $this,'captcha_v3_wp_lostpassword')); }
			} else {
				
	  
				if (get_option('add_captcha_enable_check')==1) {
					add_action('woocommerce_register_form', array( $this,'captcha_woo_registration'));// woo registration
				}
		 
				if (get_option('login_add_captcha_enable_check')==1) {
					add_action('woocommerce_login_form', array( $this,'captcha_woo_login'));//woo login
				}

				if (get_option('woo_lpass_add_captcha_enable_check')==1) {
					add_action('woocommerce_lostpassword_form', array( $this,'captcha_woo_lostpassword'));//woo lost password

				}

				if (get_option('guest_checkout_add_captcha_enable_check')==1) {
					add_action('woocommerce_review_order_before_submit', array( $this, 'captcha_woo_guestcheckout'));
				}// checkout
		  
		
				if (get_option('pay_order_add_captcha_enable_check')==1) {
					add_action('woocommerce_pay_order_before_submit', array( $this,'captcha_wp_pay_for_order'));// pay for order
				} 

		  
				if (get_option('wp_login_captcha_check')==1) {
					add_action('login_form', array( $this,'wp_captcha_show_login'));// WP login form
				}
	
				if (get_option('p_method_add_captcha_enable_check')==1) { 
					add_action('woocommerce_review_order_before_payment', array( $this,'captcha_woo_payment_method'));
				}


				if (get_option('wp_regs_captcha_check')==1) {
					add_action('register_form', array( $this,'captcha_wp_registration'));// WP registration form
				}

				if (get_option('wp_lpass_page_enable_check')==1) {
					add_action('lostpassword_form', array( $this,'captcha_wp_lostpassword'));// wp lost pawword form
				}
			}
	 
		}

		public function ka_captcha_my_load_scripts() {
			wp_enqueue_script('ka_cap_admin_api', 'https://www.google.com/recaptcha/api.js', array(), '1.0', true);
			wp_enqueue_script('jquery');
			wp_enqueue_script('ka_cap_admin_scr', plugins_url('/Assets/js/captcha.js', __FILE__), false, '1.0', false);
		}
 
  
		
 
		public function front_end_field( $themes, $size, $field_title, $class, $data_callback, $ka_captcha_validation_expired, $ka_captcha_validation_failed, $button_name, $sitekey) {
			 
			?>
	
	
   <label><?php esc_html_e( $field_title ); ?></label>
   <div class="g-recaptcha <?php esc_html_e( $class ); ?>" data-sitekey="<?php esc_html_e( $sitekey ); ?>" data-callback="<?php esc_html_e($data_callback); ?>" data-expired-callback="<?php esc_html_e($ka_captcha_validation_expired); ?>"  data-error-callback="<?php esc_html_e($ka_captcha_validation_expired); ?>" 
	data-theme="<?php esc_html_e ( $themes ); ?>" data-size="<?php esc_html_e ( $size ); ?>"></div>
 <div id="error_message_div"><label id="ka_captcha_failed"><?php esc_html_e('Please check reCAPTCHA to enable ' . $button_name . '*', 'recaptcha_verification'); ?></label></div>
	
			<?php
			if ( function_exists('is_ajax') && is_ajax() ) {
				
				?>
				<script>
					var script = document.createElement('script');
					script.setAttribute('src', 'https://www.google.com/recaptcha/api.js');

					jQuery(document).find(".g-recaptcha").after( script );
					jQuery('div.g-recaptcha').closest('form').find('button[type="submit"]').click(function( event ) {
				  event.preventDefault();});
				  jQuery('div.g-recaptcha').closest('form').find('input[type="submit"]').click(function( event ) {
				  event.preventDefault();});
				</script>
				<?php
				return;
			}


			

			if (wp_script_is('ka_cap_admin_api')) {
				return;
			} else {
				$this->ka_captcha_my_load_scripts();
			}
		   
		}
 
		public function captcha_woo_login() {
			$sitekey                       =get_option('add_captcha_site_key_field');
			$Cap_fieldtxt                  =get_option('woo_login_add_captcha_field_title');
			$cap_themes                    = 'light';
			$cap_size                      = 'normal';
			$data_callback                 ='login_ka_captcha_validation_success';
			$class                         = 'woo_login';
			$ka_captcha_validation_expired ='ka_captcha_validation_expired';
			$ka_captcha_validation_failed  ='ka_captcha_validation_failed';
			$button_name                   = 'login button';
			if (get_option('login_add_captcha_authentication_key_fields')==0) {
				$cap_themes = 'dark';
			}
	
			if (get_option('login_add_captcha_size_radio')==0) {
				$cap_size = 'compact';
			}
	
			$this->front_end_field($cap_themes, $cap_size, $Cap_fieldtxt, $class, $data_callback, $ka_captcha_validation_expired, $ka_captcha_validation_failed, $button_name, $sitekey);
		}

		public function captcha_woo_registration() {
			$sitekey                       =get_option('add_captcha_site_key_field');
			$cap_fieldtxt                  =get_option('woo_regs_enable_captcha');
			$cap_themes                    = 'light';
			$cap_size                      = 'normal';
			$class                         = 'woo_regs';
			$ka_captcha_validation_expired ='ka_captcha_validation_expired';
			$ka_captcha_validation_failed  ='ka_captcha_validation_failed';
			$data_callback                 ='regs_ka_captcha_validation_success';
			$button_name                   = 'registration button';
			if (get_option('woo_regs_add_captcha_authentication_key_fields')==0) {
				$cap_themes = 'dark';
			}
			if (get_option('add_captcha_size_radio')==0) {
				$cap_size = 'compact';
			}
	
			$this->front_end_field($cap_themes, $cap_size, $cap_fieldtxt, $class, $data_callback, $ka_captcha_validation_expired, $ka_captcha_validation_failed, $button_name, $sitekey);
		}
  
		public function captcha_woo_lostpassword() {
			$sitekey                       =get_option('add_captcha_site_key_field');
			$cap_fieldtxt                  =get_option('lpass_add_captcha_field_title');
			$cap_themes                    = 'light';
			$cap_size                      = 'normal';
			$class                         ='';
			$ka_captcha_validation_expired ='ka_captcha_validation_expired';
			$ka_captcha_validation_failed  ='ka_captcha_validation_failed';
			$data_callback                 ='ka_captcha_validation_success';
			$button_name                   = 'lost password button';
			if (get_option('woo_lpass_add_captcha_authentication_key_fields')==0) {
				$cap_themes = 'dark';
			}
			if (get_option('woo_lpass_add_captcha_size_radio')==0) {
				$cap_size = 'compact';
			}
			$this->front_end_field($cap_themes, $cap_size, $cap_fieldtxt, $class, $data_callback, $ka_captcha_validation_expired, $ka_captcha_validation_failed, $button_name, $sitekey);
		}

		public function captcha_woo_guestcheckout() {
			$sitekey                       =get_option('add_captcha_site_key_field');
			$cap_fieldtxt                  =get_option('guest_checkout_add_captcha_field_title');
			$cap_themes                    = 'light';
			$cap_size                      = 'normal';
			$class                         ='woo_checkout';
			$data_callback                 ='ka_checkout_captcha_validation_success';
			$ka_captcha_validation_expired ='ka_captcha_validation_expired';
			$ka_captcha_validation_failed  ='ka_captcha_validation_failed';
			$button_name                   = 'place order button';
			if (get_option('check_out_page_captcha_themes')==0) {
				$cap_themes = 'dark';
			}
	
			if (get_option('guest_checkout_add_captcha_size_radio')==0) {
				$cap_size = 'compact';
			}
	
			$this->front_end_field($cap_themes, $cap_size, $cap_fieldtxt, $class, $data_callback, $ka_captcha_validation_expired, $ka_captcha_validation_failed, $button_name, $sitekey);
		}

		public function wp_captcha_show_login() {
			$sitekey                       =get_option('add_captcha_site_key_field');
			$cap_fieldtxt                  =get_option('wp_login_captcha_title');
			$cap_themes                    = 'light';
			$cap_size                      = 'normal';
			$class                         ='';
			$ka_captcha_validation_expired ='ka_captcha_validation_expired';
			$ka_captcha_validation_failed  ='ka_captcha_validation_failed';
			$data_callback                 ='ka_captcha_validation_success';
			$button_name                   = 'login button';
			if (get_option('wp_login_captcha_theme_fields')==0) {
				$cap_themes = 'dark';
			}
	
			if (get_option('wp_login_captcha_size_radio')==0) {
				$cap_size = 'compact';
			}
	
			$this->front_end_field($cap_themes, $cap_size, $cap_fieldtxt, $class, $data_callback, $ka_captcha_validation_expired, $ka_captcha_validation_failed, $button_name, $sitekey);
		}
		public function captcha_wp_registration() {
			$sitekey                       =get_option('add_captcha_site_key_field');
			$cap_fieldtxt                  =get_option('wp_regs_captcha_title');
			$cap_themes                    = 'light';
			$class                         ='';
			$cap_size                      = 'normal';
			$data_callback                 ='ka_captcha_validation_success';
			$ka_captcha_validation_expired ='ka_captcha_validation_expired';
			$ka_captcha_validation_failed  ='ka_captcha_validation_failed';
			$button_name                   = 'registration button';
			if (get_option('wp_regs_captcha_theme_fields')==0) {
				$cap_themes = 'dark';
			}
	
			if (get_option('wp_regs_add_captcha_size_radio')==0) {
				$cap_size = 'compact';
			}
	
			$this->front_end_field($cap_themes, $cap_size, $cap_fieldtxt, $class, $data_callback, $ka_captcha_validation_expired, $ka_captcha_validation_failed, $button_name, $sitekey);
		}

		public function captcha_wp_lostpassword() {
			$sitekey                       =get_option('add_captcha_site_key_field');
			$cap_fieldtxt                  =get_option('wp_lpass_add_captcha_field_title');
			$class                         ='';
			$cap_themes                    = 'light';
			$cap_size                      = 'normal';
			$data_callback                 ='ka_captcha_validation_success';
			$ka_captcha_validation_expired ='ka_captcha_validation_expired';
			$ka_captcha_validation_failed  ='ka_captcha_validation_failed';
			$button_name                   = 'lost password button';

			if (get_option('wp_lpass_add_captcha_authentication_key_fields')==0) {
				$cap_themes = 'dark';
			}
			if (get_option('wp_lpass_add_captcha_size_radio')==0) {
				$cap_size = 'compact';
			}
   
			$this->front_end_field($cap_themes, $cap_size, $cap_fieldtxt, $class, $data_callback, $ka_captcha_validation_expired, $ka_captcha_validation_failed, $button_name, $sitekey);
		}

		public function captcha_wp_pay_for_order() {
			$sitekey                       =get_option('add_captcha_site_key_field');
			$cap_fieldtxt                  =get_option('add_p_order_captcha_field_title');
			$cap_themes                    = 'light';
			$cap_size                      = 'normal';
			$data_callback                 ='ka_captcha_validation_success';
			$ka_captcha_validation_expired ='ka_captcha_validation_expired';
			$ka_captcha_validation_failed  ='ka_captcha_validation_failed';
			$class                         ='';
			$button_name                   = 'pay for order button';
			if (get_option('p_order_authentication_key_radio')==0) {
				$cap_themes = 'dark';
			}
			if (get_option('p_order_add_captcha_size_radio')==0) {
				$cap_size = 'compact';
			}
			$this->front_end_field($cap_themes, $cap_size, $cap_fieldtxt, $class, $data_callback, $ka_captcha_validation_expired, $ka_captcha_validation_failed, $button_name, $sitekey);
		}
		public function captcha_woo_payment_method() {
			$sitekey                       =get_option('add_captcha_site_key_field');
			$class                         ='woo_payment';
			$cap_fieldtxt                  =get_option('add_p_method_captcha_field_title');
			$cap_themes                    = 'light';
			$cap_size                      = 'normal';
			$ka_captcha_validation_expired ='pay_ka_captcha_validation_expired';
			$ka_captcha_validation_failed  ='pay_ka_captcha_validation_failed';
			$data_callback                 ='pay_ka_captcha_validation_success';
			$button_name                   = 'payment methods';
			if (get_option('p_method_captcha_themes')==0) {
				$cap_themes = 'dark';
			}
			if (get_option('p_method_add_captcha_size_radio')==0) {
				$cap_size = 'compact';
			}
			$this->front_end_field($cap_themes, $cap_size, $cap_fieldtxt, $class, $data_callback, $ka_captcha_validation_expired, $ka_captcha_validation_failed, $button_name, $sitekey);
		}


		public function ka_captcha_my_load_scripts_v3() {
			$sitekey =get_option('add_captcha_site_key_field');
			wp_enqueue_script('ka_cap_v3_admin_api', 'https://www.google.com/recaptcha/api.js?render=' . $sitekey, array(), '1.0', true);
			wp_enqueue_script('jquery');
			wp_enqueue_script('ka_cap_v3_admin_scr', plugins_url('/Assets/js/captchav3.js', __FILE__), false, '1.0', false);

			$localize_data = array(
			'nonce'     => wp_create_nonce( 'recaptcha-ajax-nonce' ),
			'admin_url' => admin_url( 'admin-ajax.php' ),
			'v3_sitekey' => get_option('add_captcha_site_key_field')
			);
			wp_localize_script( 'ka_cap_v3_admin_scr', 'php_vars', $localize_data );
			

		}


		public function captcha_v3_woo_registration() {
	  
			?>
	  <input type= "hidden" value="true" name="grecaptcha_required" class="grecaptcha_required"></input>
	 
			<?php
		

			if (wp_script_is('ka_cap_v3_admin_scr')) {
				return;
			} else {
				$this->ka_captcha_my_load_scripts_v3();
			}
	  
			
		}
		public function captcha_v3_woo_login() {
			?>
	  <input type= "hidden" value="true" name="grecaptcha_required" class="grecaptcha_required"></input>
			<?php
		

			if (wp_script_is('ka_cap_v3_admin_scr')) {
				return;
			} else {
				$this->ka_captcha_my_load_scripts_v3();
			}
		}

		

		public function captcha_v3_woo_lostpassword() {
			?>
	  <input type= "hidden" value="true" name="grecaptcha_required" class="grecaptcha_required"></input>
			<?php
		

			if (wp_script_is('ka_cap_v3_admin_scr')) {
				return;
			} else {
				$this->ka_captcha_my_load_scripts_v3();
			}
		}
		public function captcha_v3_woo_checkout() {

			?>
	  <input type= "hidden" value="true" name="grecaptcha_required" class="grecaptcha_required"></input>
			<?php
		

			if (wp_script_is('ka_cap_v3_admin_scr')) {
				return;
			} else {
				$this->ka_captcha_my_load_scripts_v3();
			}
			

		

		}
		public function captcha_wp_v3_pay_for_order() {
			?>
	  <input type= "hidden" value="true" name="grecaptcha_required" class="grecaptcha_required"></input>
			<?php
		

			if (wp_script_is('ka_cap_v3_admin_scr')) {
				return;
			} else {
				$this->ka_captcha_my_load_scripts_v3();
			}
		}
		public function captcha_v3_woo_payment_method() {
			?>
	  <input type= "hidden" value="true" name="grecaptcha_required" class="grecaptcha_required"></input>
			<
			<?php
		

			if (wp_script_is('ka_cap_v3_admin_scr')) {
				return;
			} else {
				$this->ka_captcha_my_load_scripts_v3();
			}
		}

		public function captcha_v3_wp_registration() {
			?>
	  <input type= "hidden" value="true" name="grecaptcha_required" class="grecaptcha_required"></input>
			<?php
		

			if (wp_script_is('ka_cap_v3_admin_scr')) {
				return;
			} else {
				$this->ka_captcha_my_load_scripts_v3();
			}
	   
	   
	  
		}
		public function captcha_v3_wp_lostpassword() {
			?>
	  <input type= "hidden" value="true" name="grecaptcha_required" class="grecaptcha_required"></input>
			<?php
			if (wp_script_is('ka_cap_v3_admin_scr')) {
				return;
			} else {
				$this->ka_captcha_my_load_scripts_v3();
			}
	  
		}
		public function captcha_v3_wp_login() {
			?>
	  <input type= "hidden" value="true" name="grecaptcha_required" class="grecaptcha_required"></input>
			<?php
			if (wp_script_is('ka_cap_v3_admin_scr')) {
				return;
			} else {
				$this->ka_captcha_my_load_scripts_v3();
			}
			

		


		}

	}

	new ka_Recaptcha_Front_End();
}
