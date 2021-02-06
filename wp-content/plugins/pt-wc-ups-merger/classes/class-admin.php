<?php
class PT_UPS_Merger_Admin {
	
	public function __construct() {

		// lets hook into product settings page

		// simple products
		add_action( 'woocommerce_product_options_dimensions',    array( $this, 'show_simple_fields' ) );

		add_action( 'woocommerce_process_product_meta_simple',   array( $this, 'save_simple_fields' ) );
		add_action( 'woocommerce_process_product_meta_variable', array( $this, 'save_simple_fields' ) );

		// variations
		add_action( 'woocommerce_variation_options_dimensions',  array( $this, 'show_variable_fields' ), 10, 3 );

		add_action( 'woocommerce_process_product_meta_variable', array( $this, 'save_variable_fields' ) );
		add_action( 'woocommerce_ajax_save_product_variations',  array( $this, 'save_variable_fields' ) );

	}


	function get_shipment_methods_options() {
		return array( 
				'either_ground_or_air' => esc_html__( 'Either ground or air', 'pt-wc-ups-merger' ),
				'air_only'             => esc_html__( 'Air only', 'pt-wc-ups-merger' ),
				'ground_only'          => esc_html__( 'Ground only', 'pt-wc-ups-merger' ),
			);

	}


	function show_simple_fields() {

		global $thepostid;

		$product = wc_get_product( $thepostid );
		if ( ! $product->is_type( 'simple' ) && ! $product->is_type( 'variable' ) ) {
			return;
		}

		echo '</div><div id="pt-wc-ups-merger-simple-group-method" class="options_group show_if_simple show_if_variable">';

		woocommerce_wp_select(
				array(
					'id'            => "_pt_ups_merger_parent_ground_air",
					'name'          => "_pt_ups_merger_parent_ground_air",
					'value'         => get_post_meta( $product->get_id(), '_pt_ups_merger_ground_air', true ),
					'label'         => __( 'Shipment methods', 'pt-wc-ups-merger' ),
					'class'         => 'select short',
					'options'       => $this->get_shipment_methods_options(),
					'desc_tip'      => 'true',
					'description'   => __( 'Choose allowed shipping methods for this product.', 'pt-wc-ups-merger' ),
				)
			);

	}


	function save_simple_fields( $post_id ) {

		update_post_meta( $post_id, '_pt_ups_merger_ground_air', sanitize_text_field( $_POST[ '_pt_ups_merger_parent_ground_air' ] ) );

	}


	function show_variable_fields( $loop, $variation_data, $variation ) {

		$options = $this->get_shipment_methods_options();
		
		$parent_method_option = get_post_meta( $variation->post_parent, '_pt_ups_merger_ground_air', true );
		$parent_method        = $options[ $parent_method_option ];

		$options = array( 'parent' => sprintf( __( 'Same as parent (%s)', 'pt-wc-ups-merger' ), $parent_method ) ) 
					+ $options;

		?>

			<div>

				<?php 

					woocommerce_wp_select(
						array(
							'id'            => "_pt_ups_merger_ground_air[{$loop}]",
							'name'          => "_pt_ups_merger_ground_air[{$loop}]",
							'value'         => get_post_meta( $variation->ID, '_pt_ups_merger_ground_air', true ),
							'label'         => __( 'Shipment methods', 'pt-wc-ups-merger' ),
							'options'       => $options,
							'desc_tip'      => 'true',
							'description'   => __( 'Choose allowed shipping methods for this product variation.', 'pt-wc-ups-merger' ),
							'wrapper_class' => 'form-row form-row-full',
						)
					);

				?>

			</div>

		<?php
	}
	
	function save_variable_fields( ) {

		if ( 'variable' == $_POST[ 'product-type' ] && isset( $_POST[ 'variable_post_id' ] ) && is_array( $_POST[ 'variable_post_id' ] ) ) {

			foreach( $_POST[ 'variable_post_id' ] as $k => $post_id ) {

				update_post_meta( $post_id, '_pt_ups_merger_ground_air', sanitize_text_field( $_POST[ '_pt_ups_merger_ground_air' ][ $k ] ) );

			}
		}
	}
}

new PT_UPS_Merger_Admin();
