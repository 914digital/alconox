<?php

add_filter( 'woocommerce_shipping_packages', 'pt_wc_ups_merger_merged_packages', 99 );
function pt_wc_ups_merger_merged_packages( $packages ) {

	$pt_wc_ups_merger_options = get_option( 'woocommerce_pt-ups-merger_settings' );

	//wc_add_notice( 'Merging package pt_wc_ups_merger_options <pre>' . print_r( $pt_wc_ups_merger_options, 1 ) . '</pre>', 'error' );

	if ( isset( $pt_wc_ups_merger_options['debug'] ) ) {

		$debug = ( 'yes' == $pt_wc_ups_merger_options['debug'] );

	} else {

		$debug = false;

	}

	if ( 'no' == $pt_wc_ups_merger_options['enabled'] ) {
		
		if ( $debug ) {

			$msg = __('UPS Merger : Not enabled `@woocommerce_shipping_packages`.', 'pt-wc-ups-merger' );

			if ( ! wc_has_notice( $msg ) ) {

				wc_add_notice( $msg );

			}
		}

		return $packages;

	}

	$zone_has_ups_merger = false;

	foreach ( $packages as $key => $package ) {

		$shipping_zone    = WC_Shipping_Zones::get_zone_matching_package( $package );
		$shipping_methods = $shipping_zone->get_shipping_methods( true );

		foreach ( $shipping_methods as $key => $shipping_method ) {

			if ( 'pt-ups-merger' == $shipping_method->id ) {

				$zone_has_ups_merger = true;
				break 2;

			}
		}
	}

	if ( ! $zone_has_ups_merger ) {

		if ( $debug ) {

			$msg = __('UPS Merger: No shipping zone having our method, nothing done.', 'pt-wc-ups-merger' );

			if ( ! wc_has_notice( $msg ) ) {

				wc_add_notice( $msg );

			}

		}
		
		return $packages;

	}


	$one_merged_package = array(
			'contents'        => array(),
			'contents_cost'   => 0,
			'applied_coupons' => array(),
			'destination'     => array(
				'country'   => '',
				'state'     => '',
				'postcode'  => '',
				'city'      => '',
				'address'   => '',
				'address_1' => '',
				'address_2' => '',
			),
			'ship_via'        => array(),
			'rates'           => array(),
	);

	foreach( $packages as $package ) {

		//wc_add_notice( 'Merging package <b>' . $package['name'] . '</b> <pre>' . print_r( $package['exclude_instances'], 1 ) . '</pre>' );

		$one_merged_package['contents']        += $package['contents'];

		$one_merged_package['contents_cost']   += $package['contents_cost'];

		$one_merged_package['applied_coupons'] += $package['applied_coupons'];
		
		$one_merged_package['destination']     = $package['destination'];

		if ( isset( $package['ship_via']  ) ) {
			
			$ship_via = $package['ship_via'];
		
			foreach ($ship_via as $key => $value ) {

				if ( 'ups' == $value ) {
					unset( $package['ship_via'][ $key ] );
				}

			}
			
			$one_merged_package['ship_via'] += $package['ship_via'];

		}

		// gather available UPS rates
		$ups_rates = array();
		foreach( $package['rates'] as $rate_id => $rate ) {

			if ( 'ups' == $rate->get_method_id() ) {

				if ( in_array( $rate->get_instance_id(), $package['exclude_instances'] ) ) {

					if ( 0 && $debug ) {

						$msg = 'Removed `' . $rate->get_label() . '` | id ' . $rate_id . ' | Instance id ' . $rate->get_instance_id() . ' | @woocommerce_shipping_packages merge';

						if ( ! wc_has_notice( $msg ) ) {

							wc_add_notice( $msg );

						}

					}

					continue;

				}

				$ups_rates[ $rate->get_label() ] = $rate;

			}

		}

		// merge rates
		$rates = $one_merged_package['rates']; // initialize rate values

		foreach( $package['rates'] as $rate_id => $rate ) {

			if ( 'ups' == $rate->get_method_id() ) {

						if ( 0 && $debug ) {

							wc_add_notice( 'Ignoring rate ' . $rate->get_label() . ', cost ' . wc_price( $rate->get_cost() ));

						}


				continue; // ignore UPS rates, they will be merged

			}

			$meta = $rate->get_meta_data();

			if ( 0 && $debug ) {

				wc_add_notice( 'Rate meta <pre>' . print_r( $meta, 1 ) . '</pre>' );

			}

			$keys = array_keys( array_intersect_key( $ups_rates, $meta ) );

			if ( empty( $keys ) ) {

				$rates[ $rate_id ] = $rate;

			} else {

				foreach( $keys as $key ) {

					$ups_rate = $ups_rates[ $key ];
					
					if ( ! isset( $rates[ $rate_id ] ) ) {

						// insert the rate

						$rates[ $rate_id ] = $rate;

						$rates[ $rate_id ]->set_cost( $ups_rate->get_cost() );
						$rates[ $rate_id ]->set_taxes( $ups_rate->get_taxes() );

						$rates[ $rate_id ]->add_meta_data( $ups_rate->get_label(), wc_price( $ups_rate->get_cost() ) );

						if ( $debug ) {

							wc_add_notice( 'Inserting rate ' . $ups_rate->get_label() . ', cost ' . wc_price( $ups_rate->get_cost() ));

						}


					} else {

						// update the rate

						$old_cost  = $rates[ $rate_id ]->get_cost();
						$new_cost  = $old_cost + $ups_rate->get_cost();

						$new_taxes = array_merge( $rates[ $rate_id ]->get_taxes(), $ups_rate->get_taxes() );

						if ( $debug ) {

							wc_add_notice( 'Updating with rate ' . $ups_rate->get_label() . ', cost ' . wc_price( $old_cost ) . ' + ' . wc_price( $ups_rate->get_cost() ) . ' = ' . wc_price( $new_cost ) );

						}

						$rates[ $rate_id ]->set_cost( $new_cost );
						$rates[ $rate_id ]->set_taxes( $new_taxes );

						$rates[ $rate_id ]->add_meta_data( $ups_rate->get_label(), wc_price( $ups_rate->get_cost() ) );

					}
				}
			}
		}

		// sort rates by cost
		uasort( $rates, 'pt_wc_ups_merger_sort_rates_by_cost' );

		$one_merged_package['rates'] = $rates;
		
	}

	$packages = array( $one_merged_package );

	if ( 'no' == $pt_wc_ups_merger_options['split'] ) {
		
		if ( $debug ) {

			$msg = __( 'UPS Merger: Split not enabled.', 'pt-wc-ups-merger' );

			if ( ! wc_has_notice( $msg ) ) {

				wc_add_notice( $msg );

			}

		}
		
		return $packages;

	}

	$must_use_ground = false;
	$must_use_air    = false;

	foreach ( WC()->cart->get_cart() as $item_key => $item ) {

		if ( $item['data']->needs_shipping() ) {

			$post_id    = $item['data']->get_id();
			$ground_air = get_post_meta( $post_id, '_pt_ups_merger_ground_air', true );

			if ( 'parent' == $ground_air ) {

				$ground_air = get_post_meta( $item['data']->get_parent_id(), '_pt_ups_merger_ground_air', true );

			}

			if ( empty( $ground_air ) ) { // set a default value

				$ground_air = 'either_ground_or_air';

			}

			if ( 'ground_only' == $ground_air) {

				$must_use_ground = true;

			} elseif ( 'air_only' == $ground_air) {

				$must_use_air = true;

			}

			if ( $must_use_ground && $must_use_air ) {

				break; // bail early, we already know that 2 packages will be needed.

			}

		}
	}

	$split = $item_split = 'none';

	if ( $must_use_ground && $must_use_air ) {

		$split = 'both';
	
	} elseif ( $must_use_ground ) {

		$split = 'ground_only';

	} elseif ( $must_use_air ) {

		$split = 'air_only';

	}

	if ( 'none' == $split ) { // no products requiring special shipment

		if ( $debug ) {

			wc_add_notice('UPS Merger:  <b>no ground/air split</b> needed.');

		}
		
		return $packages;
		
	} else { 

		if ( $debug ) {

			wc_add_notice('UPS Merger:  Doing <b>ground/air</b> split.');

		}

	}


	// if air only and no ground package then create/move to air package
	// if either and ground already exists, move to ground else move to air
	// if ground only, move to ground package and get those who are either from possible air package

	$new_package                    = current( $packages );
	$new_package['contents']        = array();
	$new_package['contents_cost']   = 0;
	$new_package['applied_coupons'] = array();

	$ground_package = $air_package = $new_package;

	$ground_package['name'] = esc_html__('Ground only', 'pt-ups-merger');
	$ground_package['type'] = 'ground';

	$air_package['name'] = esc_html__('Air options', 'pt-ups-merger');
	$air_package['type'] = 'air';


	//	wc_add_notice( 'ship via <pre>' . print_r( $ground_package['ship_via'],1 ) . ' </pre>');
	//	wc_add_notice( 'Spliting package <pre>' . print_r( $ground_package, 1 ) . '</pre>', 'error' );


	//	wc_add_notice( 'Spliting package <pre>' . print_r( $split, 1 ) . '</pre>', 'error' );
	//	wc_add_notice( 'Spliting package <pre>' . print_r( $ground_package['rates'], 1 ) . '</pre>', 'error' );

	foreach ( WC()->cart->get_cart() as $item_key => $item ) {

		if ( $item['data']->needs_shipping() ) {

			$found_item = true;

			if ( 'both' == $split ) {

				$post_id    = $item['data']->get_id();
				$item_split = get_post_meta( $post_id, '_pt_ups_merger_ground_air', true );

				if ( 'parent' == $item_split ) {

					$item_split = get_post_meta( $item['data']->get_parent_id(), '_pt_ups_merger_ground_air', true );

				}

				if ( empty( $item_split ) ) { // set a default value

					$item_split = 'either_ground_or_air';

				}

			}

			if ( 'ground_only' == $split || 'ground_only' == $item_split || ( $must_use_ground &&  'either_ground_or_air' == $item_split ) ) {

				$ground_package['contents'][ $item_key ]  = $item;
				$ground_package['contents_cost']         += $item['line_total'];

				/*

				$ship_via = $ground_package['ship_via'];
			
				foreach ( $ship_via as $key => $value ) {

					if ( 'ups' == $value ) {
						unset( $ground_package['ship_via'][ $key ] );
					}

				}

				*/

				$rates = $ground_package['rates'];
		
				foreach ( $rates as $id => $rate ) {

					if ( 'pt-ups-merger' == $rate->get_method_id() ) {

						$ups_rate_id = explode( ':' , $id );
						$code        = array_pop( $ups_rate_id );

						if ( $code != '03' ) { // Keep only Ground

							unset( $ground_package['rates'][ $id ] );

						}

					/*} elseif ( 'use_my_shipping_account' == $rate->get_method_id() && 13 == $rate->get_instance_id() ) {

						$ground_label = esc_html__( 'Ground', 'pt-ups-merger' );

						$ground_package['rates'][ $id ]->set_label( $ground_label . ' (' . $rates[ $id ]->get_label() . ')' );
						$ground_package['rates'][ $id ]->add_meta_data( $ground_label, wc_price( 0 ) );*/

					}
				}
			}

			if ( 'air_only' == $split  || 'air_only' == $item_split || ( ! $must_use_ground &&  'either_ground_or_air' == $item_split ) ) {

				$air_package['contents'][ $item_key ]  = $item;
				$air_package['contents_cost']         += $item['line_total'];

				/*$ship_via = $air_package['ship_via'];

				foreach ( $ship_via as $key => $value ) {

					if ( 'ups' == $value ) {
						unset( $air_package['ship_via'][ $key ] );
					}

				}*/

				$rates = $air_package['rates'];
		
				foreach ( $rates as $id => $rate ) {

					if ( 'pt-ups-merger' == $rate->get_method_id() ) {
			
						$ups_rate_id = explode( ':' , $id );
						$code        = array_pop( $ups_rate_id );

						if ( $code == '03' ) { // Remove Ground

							unset( $air_package['rates'][ $id ] );
							break;

						}

					/*} elseif ( 'use_my_shipping_account' == $rate->get_method_id() ) {

						$air_label = esc_html__( 'Air', 'pt-ups-merger' );

						$air_package['rates'][ $id ]->set_label( $rates[ $id ]->get_label() . ' (' . $air_label . ')' );
						$air_package['rates'][ $id ]->add_meta_data( $air_label, wc_price( 0 ) );*/

					}

				}
			}

			
			// Remove from original package

			$packages[0]['contents_cost'] = $packages[0]['contents_cost'] - $item['line_total'];
			unset( $packages[0]['contents'][ $item_key ] );


			// If there are no items left in the previous package, remove it completely.

			if ( empty( $packages[0]['contents'] ) ) {

				unset( $packages[0] );

			}
		}
	}
	
	if ( $found_item ) {

		if ( 'both' == $split ) {

			$packages[] = $ground_package;
			$packages[] = $air_package;

		} elseif ( 'ground_only' == $split ) {

			$packages[] = $ground_package;

		} elseif ( 'air_only' == $split ) {

			$packages[] = $air_package;
		}


	}

	//wc_add_notice( 'Merge packages <pre>' . print_r( $packages, 1 ) .'</pre>' );

	/*
	foreach ($packages as $i => $package ) {
		$product_names = array();
		foreach ( $package['contents'] as $item_id => $values ) {
			$product_names[ $item_id ] = $values['data']->get_name() . ' &times;' . $values['quantity'];
		}
		$name = apply_filters( 'woocommerce_shipping_package_name', ( ( $i + 1 ) > 1 ) ? sprintf( _x( 'Shipping %d', 'shipping packages', 'woocommerce' ), ( $i + 1 ) ) : _x( 'Shipping', 'shipping packages', 'woocommerce' ), $i, $package );
		wc_add_notice( '<strong>' . $name . '</strong>: ' . implode( ', ', $product_names ) . '<br /><pre> rates:' . print_r($package['rates'], 1 ) . '</pre>' , 'error' );
	} 
	*/

	return $packages;
}


function pt_wc_ups_merger_sort_rates_by_cost( $a, $b ) {

	if ( $a->get_cost() == $b->get_cost() ) {

		return 0;

	}
	
	return ( $a->get_cost() < $b->get_cost() ) ? -1 : 1;
}
