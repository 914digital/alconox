<?php

/**
 * Split different shipping class products and different shipping types (ground/air) in separate packages
 */

add_filter( 'woocommerce_cart_shipping_packages', 'pt_wc_ups_merger_split_shipping_packages_shipping_class', 199 );
function pt_wc_ups_merger_split_shipping_packages_shipping_class( $packages ) {

	$pt_wc_ups_merger_options = get_option( 'woocommerce_pt-ups-merger_settings' );

	if ( isset( $pt_wc_ups_merger_options['debug'] ) ) {

		$debug = ( 'yes' == $pt_wc_ups_merger_options['debug'] );

	} else {

		$debug = false;

	}

	if ( 'no' == $pt_wc_ups_merger_options['enabled'] ) {
		
		if ( $debug ) {

			$msg = __('UPS Merger: Not enabled `@woocommerce_cart_shipping_packages`.', 'pt-wc-ups-merger' );

			if ( ! wc_has_notice( $msg ) ) {

				wc_add_notice( $msg );

			}
			
		}
		
		return $packages;

	}

	if ( 'no' == $pt_wc_ups_merger_options['split'] ) {
		
		if ( $debug ) {

			$msg = __('UPS Merger: Split not enabled `@woocommerce_cart_shipping_packages`.', 'pt-wc-ups-merger' );

			if ( ! wc_has_notice( $msg ) ) {

				wc_add_notice( $msg );

			}

		}
		
		return $packages;

	}

	$zone_has_ups_merger = false;

	foreach ( $packages as $package ) {

		$shipping_zone    = WC_Shipping_Zones::get_zone_matching_package( $package );
		$shipping_methods = $shipping_zone->get_shipping_methods( true );

		foreach ( $shipping_methods as $key => $shipping_method ) {

			if ( 'pt-ups-merger' == $shipping_method->id ) {

				$zone_has_ups_merger = true;
				break;

			}

		}

		$zones[ $shipping_zone->get_zone_name() ] = $shipping_methods ;


	}

	if ( ! $zone_has_ups_merger ) {

		if ( $debug ) {

			$msg = __('UPS Merger: No shipping zone having our method `@woocommerce_cart_shipping_packages`.', 'pt-wc-ups-merger' );

			if ( ! wc_has_notice( $msg ) ) {

				wc_add_notice( $msg );

			}

		}
		
		return $packages;

	}

	// Shipping class slug for White Plains
	$white_plains_shipping_class = 'white-plains'; 

	// Shipping class slug for Jersey City
	$jersey_city_shipping_class = 'jersey-city'; 
	

	$must_use_ground  = false;
	$must_use_air     = false;

	foreach ( WC()->cart->get_cart() as $item_key => $item ) {

		if ( $item['data']->needs_shipping() ) {

			$post_id    = $item['data']->get_id();
			$ground_air = get_post_meta( $post_id, '_pt_ups_merger_ground_air', true );

			if ( empty( $ground_air ) ) {

				$ground_air = 'either_ground_or_air';
				update_post_meta( $post_id, '_pt_ups_merger_ground_air', $ground_air );

			}

			if ( $debug ) {

				if ( 'parent' == $ground_air ) {

					$debug_ground_air = 'parent :: ' . get_post_meta( $item['data']->get_parent_id(), '_pt_ups_merger_ground_air', true );

				} else {

					$debug_ground_air = $ground_air;

				}


				$msg =  __( 'UPS Merger: split_packages `@woocommerce_cart_shipping_packages` #' . $post_id . ' ' . $item['data']->get_title() . ' | `' . $debug_ground_air . '` | ' . $item['data']->get_shipping_class(), 'pt-wc-ups-merger' );

				if ( ! wc_has_notice( $msg ) ) {

					wc_add_notice( $msg );

				}
		
			}

			if ( 'parent' == $ground_air ) {

				$ground_air = get_post_meta( $item['data']->get_parent_id(), '_pt_ups_merger_ground_air', true );

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

		$split_packages = array(

			'JC' => array( 
					'name' => 'Jersey City', 
					'type' => 'either', 
					'exclude_instances' => array( 2, 22 ), // UPS WP instance_ids
					'items' => array() ), 

			'WP' => array( 
					'name' => 'White Plains',
					'type' => 'either',
					'exclude_instances' => array( 3, 23 ), // UPS JC instance_ids
					'items' => array() ), 

		 );


		foreach ( WC()->cart->get_cart() as $item_key => $item ) {

			if ( $item['data']->needs_shipping() ) {

				if ( $white_plains_shipping_class == $item['data']->get_shipping_class() ) {

					$split_packages['WP']['items'][ $item_key ] = $item;

				}

				if ( $jersey_city_shipping_class == $item['data']->get_shipping_class() ) {

					$split_packages['JC']['items'][ $item_key ] = $item;

				}

			}
		}

		// Reset all packages and add our split packages
		$packages = array();

		foreach ( $split_packages as $key => $package ) {

			if ( $debug ) {
				wc_add_notice( 'Split only source packages <b>' . $package['name'] .'</b>: ' . count( $package['items'] ) );
			}

			if ( $package['items'] ) {

				$packages[] = array(
					'contents'          => $package['items'],
					'name'              => $package['name'],
					'type'              => $package['type'],
					'exclude_instances' => $package['exclude_instances'],
					'contents_cost'     => array_sum( wp_list_pluck( $package['items'], 'line_total' ) ),
					'applied_coupons'   => WC()->cart->get_applied_coupons(),
					'user'              => array(
						 'ID' => get_current_user_id(),
					),
					'destination'       => array(
						'country'    => WC()->customer->get_shipping_country(),
						'state'      => WC()->customer->get_shipping_state(),
						'postcode'   => WC()->customer->get_shipping_postcode(),
						'city'       => WC()->customer->get_shipping_city(),
						'address'    => WC()->customer->get_shipping_address(),
						'address_2'  => WC()->customer->get_shipping_address_2()
					)
				);
			}
		}

		//wc_add_notice( 'Split only source packages<pre>' . print_r( $packagess, 1 ) .'</pre>' );

		return $packages;
	
	}

	$split_packages = array(

			'JCG' => array( 
					'name' => 'Jersey City Ground', 
					'type' => 'ground', 
					'exclude_instances' => array( 2, 22 ), // UPS WP instance_ids
					'items' => array() ), 
			'JCA' => array( 
					'name' => 'Jersey City Air',
					'type' => 'air',
					'exclude_instances' => array( 2, 22 ), // UPS WP instance_id
					'items' => array() ), 
			'JCE' => array( 
					'name' => 'Jersey City Either Air or Ground',
					'type' => 'either',
					'exclude_instances' => array( 2, 22 ), // UPS WP instance_id
					'items' => array() ), 

			'WPG' => array( 
					'name' => 'White Plains Ground', 
					'type' => 'ground', 
					'exclude_instances' => array( 3, 23 ), // UPS JC instance_id
					'items' => array() ), 
			'WPA' => array( 
					'name' => 'White Plains Air',
					'type' => 'air',
					'exclude_instances' => array( 3, 23 ), // UPS JC instance_id
					'items' => array() ), 
			'WPE' => array( 
					'name' => 'White Plains Either Air or Ground',
					'type' => 'either',
					'exclude_instances' => array( 3, 23 ), // UPS JC instance_id
					'items' => array() ), 


		 );


	foreach ( WC()->cart->get_cart() as $item_key => $item ) {

		if ( $item['data']->needs_shipping() ) {

			$post_id    = $item['data']->get_id();
			$ground_air = get_post_meta( $post_id, '_pt_ups_merger_ground_air', true );

			if ( empty( $ground_air ) ) {

				$ground_air = 'either_ground_or_air';

			}

			if ( 'parent' == $ground_air ) {

				$ground_air = get_post_meta( $item['data']->get_parent_id(), '_pt_ups_merger_ground_air', true );

			}

			if ( empty( $ground_air ) ) {

				$ground_air = 'either_ground_or_air';

			}

			if ( 'both' == $split ) {

				$post_id    = $item['data']->get_id();
				$item_split = get_post_meta( $post_id, '_pt_ups_merger_ground_air', true );

				if ( 'parent' == $item_split ) {

					$item_split = get_post_meta( $item['data']->get_parent_id(), '_pt_ups_merger_ground_air', true );

				}

				if ( empty( $item_split ) ) {

					$item_split = 'either_ground_or_air';

				}


			}

			if ( 'ground_only' == $split || 'ground_only' == $item_split ) {


				if ( $white_plains_shipping_class == $item['data']->get_shipping_class() ) {

					$split_packages['WPG']['items'][ $item_key ] = $item;

				} else {

					$split_packages['JCG']['items'][ $item_key ] = $item;

				}

			}

			if ( 'air_only' == $split || 'air_only' == $item_split ) {


				if ( $white_plains_shipping_class == $item['data']->get_shipping_class() ) {

					$split_packages['WPA']['items'][ $item_key ] = $item;

				} else {

					$split_packages['JCA']['items'][ $item_key ] = $item;

				}

			}

			if ( 'either_ground_or_air' == $split || 'either_ground_or_air' == $item_split ) {

				if ( $white_plains_shipping_class == $item['data']->get_shipping_class() ) {

					$split_packages['WPE']['items'][ $item_key ] = $item;

				} else {

					$split_packages['JCE']['items'][ $item_key ] = $item;

				}
			}


		}
	}


	// if air only and no ground package then create/move to air package
	// if either and ground already exists, move to ground else move to air
	// if ground only, move to ground package and get those who are either from possible air package

	if ( count( $split_packages['JCG']['items'] ) ) {

		foreach ( $split_packages['JCE']['items'] as $item_key => $item ) {

			$split_packages['JCG']['items'][ $item_key ] = $item;

		}

	} else {

		foreach ( $split_packages['JCE']['items'] as $item_key => $item ) {

			$split_packages['JCA']['items'][ $item_key ] = $item;

		}

	}


	if ( count( $split_packages['WPG']['items'] ) ) {

		foreach ( $split_packages['WPE']['items'] as $item_key => $item ) {

			$split_packages['WPG']['items'][ $item_key ] = $item;

		}

	} else {

		foreach ( $split_packages['WPE']['items'] as $item_key => $item ) {

			$split_packages['WPA']['items'][ $item_key ] = $item;

		}

	}

	unset( $split_packages['JCE'], $split_packages['WPE'] );


	// Reset all packages and add our split packages
	$packages = array();

	foreach ( $split_packages as $key => $package ) {

		if ( $package['items'] ) {

			$packages[] = array(
				'contents'          => $package['items'],
				'name'              => $package['name'],
				'type'              => $package['type'],
				'exclude_instances' => $package['exclude_instances'],
				'contents_cost'     => array_sum( wp_list_pluck( $package['items'], 'line_total' ) ),
				'applied_coupons'   => WC()->cart->get_applied_coupons(),
				'user'              => array(
					 'ID' => get_current_user_id(),
				),
				'destination'       => array(
					'country'    => WC()->customer->get_shipping_country(),
					'state'      => WC()->customer->get_shipping_state(),
					'postcode'   => WC()->customer->get_shipping_postcode(),
					'city'       => WC()->customer->get_shipping_city(),
					'address'    => WC()->customer->get_shipping_address(),
					'address_2'  => WC()->customer->get_shipping_address_2()
				)
			);
		}
	}

	//wc_add_notice( 'Split service packages <pre>' . print_r( $packages, 1 ) .'</pre>' );


	/*foreach ($packages as $i => $package ) {

		$product_names = array();
		foreach ( $package['contents'] as $item_id => $values ) {
			$product_names[ $item_id ] = $values['data']->get_name() . ' &times;' . $values['quantity'];
		}
		$name = apply_filters( 'woocommerce_shipping_package_name', ( ( $i + 1 ) > 1 ) ? sprintf( _x( 'Shipping %d', 'shipping packages', 'woocommerce' ), ( $i + 1 ) ) : _x( 'Shipping', 'shipping packages', 'woocommerce' ), $i, $package );
		wc_add_notice( 'Split packages | <strong>' . $name . '</strong>: ' . implode( ', ', $product_names ) . '<br />' , 'error' );
	}*/ 
	

	return $packages;
}
