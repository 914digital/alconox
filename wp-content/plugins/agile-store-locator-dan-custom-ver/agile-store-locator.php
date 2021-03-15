<?php
/**
 *
 * @link              https://agilestorelocator.com/
 * @since             1.0.0
 * @package           AgileStoreLocator
 *
 * Plugin Name:       Agile Store Locator
 * Plugin URI:        https://agilestorelocator.com
 * Description:       Agile Store Locator is a Premium Store Finder Plugin designed to offer you immediate access to all the best stores in your local area. It enables you to find the very best stores and their location thanks to the power of Google Maps.
 * Version:           14.6.6
 * Author:            AGILELOGIX
 * Author URI:        https://agilestorelocator.com/
 * License:           Copyrights 2020
 * Text Domain:       asl_locator
 * Domain Path:       /languages/
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
  die;
}



if ( !class_exists( 'ASL_Store_locator' ) ) {


  class ASL_Store_locator {
        
        /**
         * Class constructor
         */          
        function __construct() {
                                    
            $this->define_constants();
            $this->includes();


            register_activation_hook( __FILE__, array( $this, 'activate') );
            register_deactivation_hook( __FILE__, array( $this, 'deactivate') );
        }
        
        /**
         * Setup plugin constants.
         *
         * @since 1.0.0
         * @return void
         */
        public function define_constants() {

          global $wpdb;


          define( 'ASL_URL_PATH', plugin_dir_url( __FILE__ ) );
          define( 'ASL_PLUGIN_PATH', plugin_dir_path(__FILE__) );
          define( 'ASL_BASE_PATH', dirname( plugin_basename( __FILE__ ) ) );
          define( 'ASL_PREFIX', $wpdb->prefix."asl_" );
          define( 'ASL_CVERSION', "4.6.1" );
        }
        
        /**
         * Include the required files.
         *
         * @since 1.0.0
         * @return void
         */
        public function includes() {

          require_once ASL_PLUGIN_PATH . 'includes/class-agile-store-locator.php';
          
          $asl_core = new AgileStoreLocator();
          $asl_core->run();
        }
        

        /**
         * The code that runs during plugin activation.
         * This action is documented in includes/class-agile-store-locator-activator.php
         */
        public function activate() {
          
          require_once ASL_PLUGIN_PATH . 'includes/class-agile-store-locator-activator.php';
          AgileStoreLocator_Activator::activate();
        }

        /**
         * The code that runs during plugin deactivation.
         * This action is documented in includes/class-agile-store-locator-deactivator.php
         */
        public function deactivate() {
          
          require_once ASL_PLUGIN_PATH . 'includes/class-agile-store-locator-deactivator.php';
          AgileStoreLocator_Deactivator::deactivate();
        }
  }


  $asl_instance = new ASL_Store_locator();
}

