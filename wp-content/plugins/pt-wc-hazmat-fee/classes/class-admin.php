<?php
class pt_wc_hazmat_fee_admin {
	
	public function __construct() {

		// simple products
		add_action( 'woocommerce_product_options_pricing',    array( $this, 'show_simple_fields' ) );

		add_action( 'woocommerce_process_product_meta_simple',   array( $this, 'save_simple_fields' ) );
		add_action( 'woocommerce_process_product_meta_variable', array( $this, 'save_simple_fields' ) );

		// variations
		add_action( 'woocommerce_variation_options_pricing',  array( $this, 'show_variable_fields' ), 10, 3 );

		add_action( 'woocommerce_process_product_meta_variable', array( $this, 'save_variable_fields' ) );
		add_action( 'woocommerce_ajax_save_product_variations',  array( $this, 'save_variable_fields' ) );

	}


	function show_simple_fields() {

		global $thepostid;

		$product = wc_get_product( $thepostid );
		if ( ! $product->is_type( 'simple' ) && ! $product->is_type( 'variable' ) ) {
			return;
		}

		echo '</div><div id="pt-wc-ups-spliter-simple-group-method" class="options_group show_if_simple show_if_variable">';

		woocommerce_wp_text_input(
			array(
				'id'        => '_pt_wc_local_hazmat_fee_simple',
				'value'     => wc_format_localized_price( get_post_meta( $product->get_id(), '_pt_wc_local_hazmat_fee_simple', true ) ),
				'label'     => esc_html__( 'Ground hazmat fee', 'pt-wc-hazmat-fee' ) . ' (' . get_woocommerce_currency_symbol() . ')',
				'data_type' => 'price',
			)
		);

		woocommerce_wp_text_input(
			array(
				'id'        => '_pt_wc_international_hazmat_fee_simple',
				'value'     => wc_format_localized_price( get_post_meta( $product->get_id(), '_pt_wc_international_hazmat_fee_simple', true ) ),
				'label'     => esc_html__( 'Air hazmat fee', 'pt-wc-hazmat-fee' ) . ' (' . get_woocommerce_currency_symbol() . ')',
				'data_type' => 'price',
			)
		);
	}


	function save_simple_fields( $post_id ) {

		update_post_meta( $post_id, '_pt_wc_local_hazmat_fee_simple', sanitize_text_field( $_POST[ '_pt_wc_local_hazmat_fee_simple' ] ) );

		update_post_meta( $post_id, '_pt_wc_international_hazmat_fee_simple', sanitize_text_field( $_POST[ '_pt_wc_international_hazmat_fee_simple' ] ) );

	}


	function show_variable_fields( $loop, $variation_data, $variation ) {

		?>
			</div>
			<div class="hazmat_fees">
		<?php 

				$label = sprintf(
					/* translators: %s: currency symbol */
					esc_html__( 'Ground hazmat fee (%s)', 'pt-wc-hazmat-fee' ),
					get_woocommerce_currency_symbol()
				);

				woocommerce_wp_text_input(
					array(
						'id'            => "_pt_wc_local_hazmat_fee{$loop}",
						'name'          => "_pt_wc_local_hazmat_fee[{$loop}]",
						'value'         => wc_format_localized_price( get_post_meta( $variation->ID, '_pt_wc_local_hazmat_fee', true ) ),
						'label'         => $label,
						'data_type'     => 'price',
						'wrapper_class' => 'form-row form-row-first',
						'placeholder'   => wc_format_localized_price( get_post_meta( $variation->post_parent, '_pt_wc_local_hazmat_fee_simple', true ) ) . __( ' (Same as parent)', 'pt-wc-hazmat-fee' ),

					)
				);

				$label = sprintf(
					/* translators: %s: currency symbol */
					esc_html__( 'Air hazmat fee (%s)', 'pt-wc-hazmat-fee' ),
					get_woocommerce_currency_symbol()
				);

				woocommerce_wp_text_input(
					array(
						'id'            => "_pt_wc_international_hazmat_fee{$loop}",
						'name'          => "_pt_wc_international_hazmat_fee[{$loop}]",
						'value'         => wc_format_localized_price( get_post_meta( $variation->ID, '_pt_wc_international_hazmat_fee', true ) ),
						'data_type'     => 'price',
						'label'         => $label,
						'wrapper_class' => 'form-row form-row-last',
						'placeholder'   => wc_format_localized_price( get_post_meta( $variation->post_parent, '_pt_wc_international_hazmat_fee_simple', true ) ) . __( ' (Same as parent)', 'pt-wc-hazmat-fee' ),


					)
				);


	}
	
	function save_variable_fields( ) {

		if ( 'variable' == $_POST[ 'product-type' ] && isset( $_POST[ 'variable_post_id' ] ) && is_array( $_POST[ 'variable_post_id' ] ) ) {

			foreach( $_POST[ 'variable_post_id' ] as $k => $post_id ) {

				update_post_meta( $post_id, '_pt_wc_local_hazmat_fee', sanitize_text_field( $_POST[ '_pt_wc_local_hazmat_fee' ][ $k ] ) );

				update_post_meta( $post_id, '_pt_wc_international_hazmat_fee', sanitize_text_field( $_POST[ '_pt_wc_international_hazmat_fee' ][ $k ] ) );

			}
		}
	}
}

new pt_wc_hazmat_fee_admin();
