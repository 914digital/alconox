<?php

add_filter( 'woocommerce_package_rates', 'pt_wc_ups_merger_exclude_shipping_rates', 10, 2 );
function pt_wc_ups_merger_exclude_shipping_rates( $rates, $package ) {

	//wc_add_notice( 'pt_wc_ups_merger_exclude_shipping_rates $package[type]=' . $package['type'] . '' );

	if ( ! isset( $package['type'] ) || 'either' == $package['type'] ) {
		return $rates;
	}

	if ( 'air' == $package['type'] ) {

		$excluded_rates = array( 'ups:03' );

	} else {

		$excluded_rates = array( 
								'ups:12', 
								'ups:02', 
								'ups:59', 
								'ups:01', 
								'ups:13', 
								'ups:14', 
								'ups:11', 
								'ups:07', 
								'ups:54', 
								'ups:08', 
								'ups:65',
								);

	}

	if ( isset( $package['exclude_instances'] ) ) {

		$excluded_rates += $package['exclude_instances'];

	}

	// remove unwanted instances and services
	foreach ( $rates as $k => $rate ) {

		$id = 'ups:' . substr( $k, -2 );

		if ( array_intersect( array( $rate->id, $rate->method_id, $rate->instance_id, $id ), $excluded_rates ) ) {

			unset( $rates[ $k ] );

		}
	}

	return $rates;
}

add_filter( 'woocommerce_package_rates', 'pt_wc_ups_merger_exclude_hazmat_canada_rates', 5, 2 );
function pt_wc_ups_merger_exclude_hazmat_canada_rates( $rates, $package ) {

	if ( 'CA' == $package['destination']['country'] ) {

		$show_all_options = true;

		foreach ( $package['contents'] as $item ) {

			if ( $item['variation_id'] ) {

				$hazmat_fee = get_post_meta( $item['variation_id'], '_pt_wc_international_hazmat_fee', true );
			}

			if ( ! $hazmat_fee ) {

				$hazmat_fee = get_post_meta( $item['product_id'], '_pt_wc_international_hazmat_fee_simple', true );

			}

			if ( $hazmat_fee ) {

				$show_all_options = false;
				break;

			}

		}

		if ( ! $show_all_options ) {
		
			// remove all other rates
			$rates = array( 'use-my-shipping-account' => $rates[ 'use-my-shipping-account'] );
			
		}

	}

	return $rates;
}
