<?php
// Exit if accessed directly
if (!defined('ABSPATH'))
    exit;

/**
 * Scripts Class
 *
 * Contains logic to add scripts and style in admin and public site 
 *
 * @package reCAPTCHA for WooCommerce
 * @since 1.0.0
 */
class Woo_Recaptcha_Scripts {

    public $public;

    function __construct() {

        global $woo_recaptcha_public;
        $this->public = $woo_recaptcha_public;
    }

    /**
     * Enqueue admin script
     *
     * @package reCAPTCHA for WooCommerce
     * @since 1.0.0
     */
    public function woo_recaptcha_admin_script($hook_suffix) {

        if ($hook_suffix == 'woocommerce_page_wc-settings' && isset($_REQUEST['tab']) && $_REQUEST['tab'] == 'woo_recaptcha') {
            wp_register_script('woo-recaptcha-admin-script', WOO_RECAPTCHA_URL . 'includes/js/woo-recaptcha-admin.js', array('jquery'), WOO_RECAPTCHA_PLUGIN_VERSION, true);
            wp_enqueue_script('woo-recaptcha-admin-script');
        }
    }

    /**
     * Enqueue public script
     *
     * @package reCAPTCHA for WooCommerce
     * @since 1.0.2
     */
    public function woo_recaptcha_public_script() {

        if (is_checkout()) {
            wp_register_script('woo-recaptcha-public-script', WOO_RECAPTCHA_URL . 'includes/js/woo-recaptcha-public.js', array('jquery'), WOO_RECAPTCHA_PLUGIN_VERSION, true);
            wp_enqueue_script('woo-recaptcha-public-script');

            $sitekey = get_option('woo_recaptcha_site_key');
            $theme = get_option('woo_recaptcha_theme');
            $size = get_option('woo_recaptcha_size');

            wp_localize_script('woo-recaptcha-public-script', 'WooRecaptchaPulicVar', array(
                'sitekey' => $sitekey,
                'theme' => $theme,
                'size' => $size));
        }
    }

    /**
     * Add action to enqueue reCAPCHA script
     *
     * @package reCAPTCHA for WooCommerce
     * @since 1.0.0
     */
    public function woo_recaptcha_load_script() {

        $login = get_option('woo_recaptcha_login');
        $registration = get_option('woo_recaptcha_registration');
        $lost_password = get_option('woo_recaptcha_lost_password');
        $checkout = get_option('woo_recaptcha_checkout');

        if ((!empty($login) && $login == "yes" ) ||
                (!empty($registration) && $registration == "yes" ) ||
                (!empty($lost_password) && $lost_password == "yes" ) ||
                (!empty($checkout) && $checkout == "yes" )) {

            // add action to enqueue recapcha js
            add_action('wp_footer', array($this, 'woo_recaptcha_script'), 99);
        }
    }

    /**
     * Enqueue reCAPCHA scripts
     * 
     * @package reCAPTCHA for WooCommerce
     * @since 1.0.0
     */
    public function woo_recaptcha_script() {

        $include_script = get_option('woo_recaptcha_inlcude_script');

        if (is_checkout() || is_account_page() || $include_script == "yes" || apply_filters('woo_include_recaptcha_script', false) ) {

            $selected_lang = get_option( 'woo_recaptcha_language' ); 				
            // if language is empty (auto detected chosen) do nothing otherwise add the lang query to the
            // reCAPTCHA script url
            if ( isset( $selected_lang ) && ( ! empty( $selected_lang ) ) ) {
                    $lang = "&hl=$selected_lang";
            } else {
                $selected_lang = get_locale();
                $lang = "&hl=$selected_lang";
            }

            $total_captcha = $this->public->woo_total_captcha();

            $site_key = get_option('woo_recaptcha_site_key');
            $theme = get_option('woo_recaptcha_theme');
            $size = get_option('woo_recaptcha_size');
            ?>

            <script type="text/javascript">
                var recaptchaCallBack = function () {
            <?php for ($i = 1; $i <= $total_captcha; $i++) { ?>
                        var woo_recaptcha_<?php echo $i; ?>;
                        if( document.getElementById('woo_recaptcha_field_<?php echo $i; ?>') ) {
                            woo_recaptcha_<?php echo $i; ?> = grecaptcha.render('woo_recaptcha_field_<?php echo $i; ?>', {
                                'sitekey': '<?php echo esc_js($site_key); ?>',
                                'theme': '<?php echo esc_js($theme); ?>',
                                'size': '<?php echo esc_js($size); ?>'
                            });
                        }
            <?php } ?>
                };
            </script>

            <script src="https://www.google.com/recaptcha/api.js?onload=recaptchaCallBack&render=explicit<?php echo esc_js($lang); ?>" async defer></script><?php
        }
    }

    /* Add hooks ( action and filters). 
     * 
     * contains all action and filter related to scripts
     *
     * @package reCAPTCHA for WooCommerce
     * @since 1.0.0
     */

    public function add_hooks() {

        // add actio to add admin script
        add_action('admin_enqueue_scripts', array($this, 'woo_recaptcha_admin_script'));

        // add action to load scripts	
        add_action('init', array($this, 'woo_recaptcha_load_script'));

        // add action to add public script
        add_action('wp_enqueue_scripts', array($this, 'woo_recaptcha_public_script'));
    }

}
