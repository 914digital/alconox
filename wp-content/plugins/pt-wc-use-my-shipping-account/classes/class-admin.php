<?php
class pt_wcumsa_hazmat_fee_admin {
	
	public function __construct() {

		// lets hook into shipping settings page
		add_filter( 'woocommerce_get_sections_shipping',  array( $this, 'add_hazmat_section' ), 40 );
		add_filter( 'woocommerce_get_settings_shipping',  array( $this, 'hazmat_settings' ), 20, 2 ); 

	}


	function add_hazmat_section( $sections ) {

		$new_sections = array();

		foreach ( $sections as $key => $value ) {

			// place our section before options
			if ( 'options' == $key ) {

				$new_sections['pt_wcumsa_hazmat_fee'] = esc_html__( 'Hazmat fee zones', 'pt-wc-use-my-shipping-account' );
			}

			$new_sections[$key] = $value;
		}


		return $new_sections;

	}

	function hazmat_settings( $settings, $current_section = '' ) {

		if ( $current_section == 'pt_wcumsa_hazmat_fee' ) {

			return $this->get_settings();

		} else {

			return $settings;

		}
	}


	public static function get_shipping_zones_options() {

		$options = array();

		foreach( WC_Shipping_Zones::get_zones() as $zone ) {

			$options[ $zone['id'] ] = $zone['zone_name'];

		}

		//$options[ 0 ] = __( 'Other locations', 'pt-wc-use-my-shipping-account' );

		return $options;
	}


	public static function get_settings() {

		$settings = array(

			'hazmat' => array(
					'name'	=>	__( 'Use my shipping account hazmat fee', 'pt-wc-use-my-shipping-account' ),
					'type'	=>	'title',
					'id'	=>	'_pt_wcumsa_hazmat_fee_settings'
			),
			array(
				'title'             => __( 'Zones for hazmat fee', 'pt-wc-use-my-shipping-account' ),
				'type'              => 'multiselect',
				'id'                => 'use_my_shipping_account_hazmat_zones',
				'class'             => 'wc-enhanced-select',
				'css'               => 'width: 600px;',
				'default'           => '',
				'description'       => __( 'Shipping zones that will have air fee.', 'pt-wc-use-my-shipping-account' ),
				'options'           => pt_wcumsa_hazmat_fee_admin::get_shipping_zones_options(),
				'desc_tip'          => true,
				'custom_attributes' => array(
					'data-placeholder' => __( 'Select shipping zones for hazmat fee when use my shipping account is selected', 'pt-wc-use-my-shipping-account' ),
				),
			),
			'hazmat_end'	=>	array(
									'type' => 'sectionend',
									'id' => '_pt_wcumsa_hazmat_fee_settings_end'
			),

		);
			
		return apply_filters( 'pt_wcumsa_hazmat_fee_settings', $settings );
	}

}

new pt_wcumsa_hazmat_fee_admin();
