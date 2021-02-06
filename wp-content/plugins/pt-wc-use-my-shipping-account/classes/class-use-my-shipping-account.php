<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WC_Use_My_Shipping_Account class.
 *
 * @extends WC_Shipping_Method
 *
 */
class WC_Use_My_Shipping_Account extends WC_Shipping_Method {

	/**
	 * Constructor.
	 *
	 * @param int $instance_id Shipping method instance.
	 */
	public function __construct( $instance_id = 0 ) {
		$this->id                 = 'use-my-shipping-account';
		$this->instance_id        = absint( $instance_id );
		$this->method_title       = __( 'Use My Shipping Account', 'pt-wc-use-my-shipping-account' );
		$this->method_description = __( 'The Use My Shipping Account extension allows buyers to inform their FedEx or UPS shipping accounts for delivery.', 'pt-wc-use-my-shipping-account' );

		$this->supports           = array(
			'shipping-zones',
			'instance-settings',
			'instance-settings-modal',
		);

		$this->init();
	}

	/**
	 * init function.
	 *
	 * @access public
	 * @return void
	 */
	public function init() {
		
		// Load our settings.
		$this->init_form_fields();
		$this->init_settings();	

		// Define user set variables
		$this->title    = isset( $this->settings['title'] ) ? $this->settings['title'] : $this->method_title;
//		$this->enabled  = isset( $this->settings['enabled'] ) ? $this->settings['enabled'] : $this->enabled;
		$this->debug    = isset( $this->settings['debug'] ) && $this->settings['debug'] == 'yes' ? true : false;
				
		add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'process_admin_options' ) );
	}

	/**
	 * admin_options function.
	 *
	 * @access public
	 * @return void
	 */
	public function Xadmin_options() {
		// Show settings
		parent::admin_options();
	}

	/**
	 * init_form_fields function.
	 *
	 * @access public
	 * @return void
	 */
	public function init_form_fields() {
		$this->form_fields  = array(
			'enabled'          => array(
				'title'           => __( 'Enable/Disable', 'pt-wc-use-my-shipping-account' ),
				'type'            => 'checkbox',
				'label'           => __( 'Enable this shipping method', 'pt-wc-use-my-shipping-account' ),
				'default'         => 'no'
			),
			'debug'  => array(
				'title'           => __( 'Debug Mode', 'pt-wc-use-my-shipping-account' ),
				'label'           => __( 'Enable debug mode', 'pt-wc-use-my-shipping-account' ),
				'type'            => 'checkbox',
				'default'         => 'no',
				'description'     => __( 'Enable debug mode to show debugging information on your cart/checkout', 'pt-wc-use-my-shipping-account' )
			),
		);

		$this->instance_form_fields  = array(
			'title' => array(
				'title' 		=> __( 'Title', 'pt-wc-use-my-shipping-account' ),
				'type' 			=> 'text',
				'description' 	=> __( 'This controls the title which the user sees during checkout.', 'pt-wc-use-my-shipping-account' ),
				'default'		=> $this->method_title
			),
		);
	}
	
	
	/**
	 * calculate_shipping function.
	 *
	 * @access public
	 * @param mixed $package
	 * @return void
	 */
	public function calculate_shipping( $package = array() ) {
/*
		if ( 'no' === $this->enabled ) {
			return;
		}
		if ( empty( $package ) ) {
			return;
		}
*/
		$this->add_rate( array(
						'id' 	=> $this->id,
						'label' => $this->title,
						'cost' 	=> 0,
						'taxes' => false
							) );

		if ( $this->debug ) {
			wc_add_notice( 'Use My Shipping Account: Our rates <pre style="height:200px;overflow:scroll;">' . print_r( $this->rates, true )  . '</pre>' );
		}
	}
}
