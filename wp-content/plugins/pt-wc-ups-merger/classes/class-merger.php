<?php
/**
 * PT_UPS_MERGER class.
 *
 * @extends WC_Shipping_Method
 *
 */
class PT_UPS_MERGER extends WC_Shipping_Method {

	private $services = array(    // Same as UPS
		// Domestic
		"12" => "3 Day Select",
		"03" => "Ground",
		"02" => "2nd Day Air",
		"59" => "2nd Day Air AM",
		"01" => "Next Day Air",
		"13" => "Next Day Air Saver",
		"14" => "Next Day Air Early AM",

		// International
		"11" => "Standard",
		"07" => "Worldwide Express",
		"54" => "Worldwide Express Plus",
		"08" => "Worldwide Expedited",
		"65" => "Worldwide Saver",
	);
	
	/**
	 * __construct function.
	 *
	 * @param int $instance_id Instance ID.
	 * @access public
	 * @return void
	 */
	public function __construct( $instance_id = 0 ) {

		$this->id                 = 'pt-ups-merger';
		$this->instance_id        = absint( $instance_id );
		$this->method_title       = __( 'UPS Merger', 'pt-wc-ups-merger' );
		$this->method_description = __( 'The <i>UPS Merger</i> merges the rates of the same shipping methods presenting one value.<br /><strong>MUST BE THE LAST METHOD</strong> or rates won\'t be merged!!' , 'pt-wc-ups-merger' );
		$this->supports           = array(
			'shipping-zones',
			'instance-settings',
			'settings',
		);
		/**
		 * Features this method supports. Possible features used by core:
		 * - shipping-zones Shipping zone functionality + instances
		 * - instance-settings Instance settings screens.
		 * - settings Non-instance settings screens. Enabled by default for BW compatibility with methods before instances existed.
		 * - instance-settings-modal Allows the instance settings to be loaded within a modal in the zones UI.
		 *
		 * @var array
		 */
		$this->init();
	}

	private function set_settings() {
		// Define user set variables.
		$this->title   = $this->get_option( 'title', $this->method_title );
		$this->enabled = $this->get_option( 'enabled' ) === 'yes';
		$this->split   = $this->get_option( 'split' ) === 'yes';
		$this->debug   = $this->get_option( 'debug' ) === 'yes';
	}	

	/**
	 * init function.
	 *
	 * @access public
	 * @return void
	 */
	private function init() {
		
		if ( class_exists( 'WC_Shipping_UPS', false ) ) {

			$this->ups = new WC_Shipping_UPS();
			
			// Load the UPS settings.
			//$this->ups->set_settings();
	
			// User set UPS variables
			$this->ups->enabled	= isset( $this->ups->settings['enabled'] ) ? $this->ups->settings['enabled'] : $this->ups->enabled;
			$this->ups->title   = isset( $this->ups->settings['title'] ) ? $this->ups->settings['title'] : $this->ups->method_title;
	
			// UPS API Settings
			$this->ups->user_id         	= isset( $this->ups->settings['user_id'] ) ? $this->ups->settings['user_id'] : '';
			$this->ups->password        	= isset( $this->ups->settings['password'] ) ? $this->ups->settings['password'] : '';
			$this->ups->access_key      	= isset( $this->ups->settings['access_key'] ) ? $this->ups->settings['access_key'] : '';
			$this->ups->shipper_number  	= isset( $this->ups->settings['shipper_number'] ) ? $this->ups->settings['shipper_number'] : '';

		}

		// Load our settings.
		$this->init_form_fields();
		$this->set_settings();	
		
		add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'process_admin_options' ) );
		add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'clear_transients' ) );

		add_filter( 'woocommerce_shipping_' . $this->id . '_instance_settings_values', array( $this, 'save_instance_settings' ), 10, 2 );

		include_once( WP_PLUGIN_DIR  . '/woocommerce/includes/wc-notice-functions.php' );

	}

	/**
	 * Process settings on save.
	 *
	 * @access public
	 * @return void
	 */
	public function process_admin_options() {
		
		parent::process_admin_options();

		$this->set_settings();
	}


	public function save_instance_settings( $instance_settings, $instance ) {

		$post_data = $this->get_post_data();

		$instance_settings['shipping_classes']  = $post_data['shipping_class'];
		$instance_settings['merge_services']    = explode( ',', $post_data['merged_services'] );
		$instance_settings['disabled_services'] = $post_data['disabled_services'];

		return $instance_settings;
	}


	/**
	 * environment_check function.
	 *
	 * @access public
	 * @return void
	 */
	private function environment_check() {
		
		if ( ! class_exists( 'WC_Shipping_UPS', false ) ) {
			
			$error_message = '<p>' . __( 'Plugin WooCommerce UPS Shipping must be installed and active.', 'pt-wc-ups-merger' ) . '</p>';

		} else {

			$error_message = '';
		
			// Check for UPS enabled
			if ( 'no' == $this->ups->enabled ) {
				$error_message .= '<p>' . sprintf( __( 'Shipping method <strong>%s</strong> must be enabled.', 'pt-wc-ups-merger' ), $this->ups->title ) . '</p>';
			}		

			// Check for UPS User ID
			if ( ! $this->ups->user_id ) {
				$error_message .= '<p>' . __( 'UPS User ID has not been set.', 'pt-wc-ups-merger' ) . '</p>';
			}
	
			// Check for UPS Password
			if ( ! $this->ups->password ) {
				$error_message .= '<p>' . __( 'UPS Password has not been set.', 'pt-wc-ups-merger' ) . '</p>';
			}
	
			// Check for UPS Access Key
			if ( ! $this->ups->access_key ) {
				$error_message .= '<p>' . __( 'UPS Access Key has not been set.', 'pt-wc-ups-merger' ) . '</p>';
			}
	
			// Check for UPS Shipper Number
			if ( ! $this->ups->shipper_number ) {
				$error_message .= '<p>' . __( 'UPS Shipper Number has not been set.', 'pt-wc-ups-merger' ) . '</p>';
			}
	
		}

		if ( ! $error_message == '' ) {
			echo '<div class="error">';
			echo $error_message;
			echo '</div>';
		}
	}

	/**
	 * admin_options function.
	 *
	 * @access public
	 * @return void
	 */
	public function admin_options() {
		// Check users environment supports this method
		$this->environment_check();
		// Show settings
		parent::admin_options();
	}


	/**
	 * clear_transients function.
	 *
	 * @access public
	 * @return void
	 */
	public function clear_transients() {
		//delete_transient( '' );
	}


	/**
	 * init_form_fields function.
	 *
	 * @access public
	 * @return void
	 */
	public function init_form_fields() {
		
		
		$this->instance_form_fields = array(
			'core' => array(
					'title'       => __( 'Method & Origin Settings', 'pt-wc-ups-merger' ),
					'type'        => 'title',
					'description' => '',
					'class'       => 'ups-merge-section-title',
				),
			'title' => array(
					'title'       => __( 'Method Title', 'pt-wc-ups-merger' ),
					'type'        => 'text',
					'description' => __( 'This controls the title which the user sees during checkout.', 'pt-wc-ups-merger' ),
					'default'     => __( 'UPS Merger', 'pt-wc-ups-merger' ),
					'desc_tip'    => true,
				),
			'shipping_classes' => array(
					'type'        => 'instance_settings',
				),
			'merge_services'   => array(
					'type'        => 'hidden',
				),
			'disabled_services' => array(
					'type'        => 'hidden',
				),
		);


		$this->form_fields  = array(
			'enabled'          => array(
				'title'           => __( 'Enable/Disable', 'pt-wc-ups-merger' ),
				'type'            => 'checkbox',
				'label'           => __( 'Enable this shipping functionality', 'pt-wc-ups-merger' ),
				'default'         => 'yes'
			),
			'split'  => array(
				'title'           => __( 'Ground | Air Split Mode', 'pt-wc-ups-merger' ),
				'label'           => __( 'Enable ground | air split mode', 'pt-wc-ups-merger' ),
				'type'            => 'checkbox',
				'default'         => 'yes',
				'description'     => __( 'Enable split mode to create ground and air packages if needed.', 'pt-wc-ups-merger' )
			),

			'debug'  => array(
				'title'           => __( 'Debug Mode', 'pt-wc-ups-merger' ),
				'label'           => __( 'Enable debug mode', 'pt-wc-ups-merger' ),
				'type'            => 'checkbox',
				'default'         => 'yes',
				'description'     => __( 'Enable debug mode to show debugging information on your cart/checkout.', 'pt-wc-ups-merger' )
			),
			'api'           => array(
				'title'           => __( 'API Settings', 'pt-wc-ups-merger' ),
				'type'            => 'title',
				'description'     => __( 'Settings come from UPS Shipping plugin.', 'pt-wc-ups-merger' ),
			),
			'ups'           => array(
				'type'            => 'ups_settings',
			),
		);
	}


	/**
	 * generate_ups_merger_html function.
	 *
	 * @access public
	 * @return void
	 */
	function generate_instance_settings_html() {

		$instance_id      = $this->instance_id;
		$zone             = WC_Shipping_Zones::get_zone_by( 'instance_id', $instance_id );
		$shipping_methods = $zone->get_shipping_methods( true );

		$ups_instances    = array();

		foreach( $shipping_methods as $shipping_method ) {

			if ( 'yes' == $shipping_method->enabled && 'ups' == $shipping_method->id  ) {

				$ups_instances[ $shipping_method->title] = $shipping_method;

			}
		}

		if ( empty( $ups_instances ) ) {

			$rows = '
						<tr>
							<td colspan="3">
								' . esc_html__('No UPS instances found, merge will not be done.', 'pt-wc-ups-merger') . '
							</td>
						</tr>';

		} else {

			$rows                     = '';
			$merge_services           = $this->services;
			$enabled_shipping_classes = array();

			// get_instance_option() does not work after saving options, using get_option instead
			// $shipping_classes= $this->get_instance_option( 'shipping_classes' ); 

			$instance_options = get_option( $this->get_instance_option_key() );

			$disabled_services = (array) $instance_options['disabled_services'];
			$shipping_classes  = $instance_options['shipping_classes'];

			foreach( $ups_instances as $name => $ups_instance ) {

				$enabled_services  = array();
				$instance_services = $ups_instance->get_option( 'services' );

				foreach( $instance_services as $service_id => $service ) {

					if ( $service['enabled'] ) {

						$enabled_services[] = $this->services[ $service_id ];	

					} else {

						unset( $merge_services[ $service_id ] );

					}

				}

				if ( ! isset( $shipping_classes[ $ups_instance->instance_id ] ) ) {

					$selected = '';

				} else {

					$selected       = $shipping_classes[ $ups_instance->instance_id ];
					$shipping_class = get_term_by( 'id', $selected, 'product_shipping_class' );

					if ( $shipping_class ) {

						$enabled_shipping_classes[] = $shipping_class->name;

					}

				}

				$args = array(
					'taxonomy'         => 'product_shipping_class',
					'hide_empty'       => 0,
					'show_option_none' => __( 'No shipping class', 'pt-wc-ups-merger' ),
					'name'             => 'shipping_class[' . $ups_instance->instance_id .']',
					'id'               => 'instance_shipping_class_' . $ups_instance->instance_id,
					'selected'         => $selected,
					'class'            => 'select short',
					'orderby'          => 'name',
					'show_count'       => 1,
					'echo'             => 0,
				);

				$rows .= '
						<tr class="wc-shipping-zone-method-rows">
							<td>
								' . $name . '
							</td>
							<td>
								' . implode( ', ', $enabled_services ) . '
							</td>
							<td>
								<p class="form-field shipping_class_field">
									' . wp_dropdown_categories( $args ) . '
									' . wc_help_tip( __( 'Shipping classes are used to group products under this method.', 'pt-wc-ups-merger' ) ) . '
								</p>
							</td>
						</tr>';

			}

			$rows .= '
						<tr class="wc-shipping-zone-method-rows">
							<td>
								' . $this->title . '
							</td>
							<td>
								' . implode( ', ', $merge_services ) . '
							</td>
							<td>
								' . implode( ', ', $enabled_shipping_classes ) . ' 
							</td>
						</tr>';


			
			$service_rows = '';

			foreach ( $merge_services as $code => $name ) {

				if ( ! isset( $disabled_services[$code] ) ) {
					$disabled_services[$code] = 'yes';
				}

				$service_rows .= '
									<tr>
										<td>
											<input type="text" disabled="disabled" placeholder="' . $name . ' (' . $this->title . ')" size="50" />
										</td>
										<td>
											<label for="enabled_'     . $code . '">' . __( 'Yes', 'pt-wc-ups-merger' ) . ' <input type="radio" id="enabled_'     . $code . '" value="yes" name="disabled_services[' . $code . ']" '. checked( $disabled_services[$code], 'yes', false ) . ' /></label>
										</td>
										<td>
											<label for="not_enabled_' . $code . '">' . __( 'No', 'pt-wc-ups-merger' )  . ' <input type="radio" id="not_enabled_' . $code . '" value="no"  name="disabled_services[' . $code . ']" '. checked( $disabled_services[$code], 'no',  false ) . ' /></label>
										</td>
									</tr>';
			}

			$rows .= '
						<tr class="wc-shipping-zone-method-rows">
							<td style="padding-left: 10px;" colspan="3">
								<table class="ups_merger_services widefat">
									<thead>
										<tr>
											<th>' . __( 'Service', 'pt-wc-ups-merger' ) . '</th>
											<th colspan="2">' . __( 'Enabled', 'pt-wc-ups-merger' ) . '</th>
										</tr>
									</thead>
									<tbody>
										' . $service_rows . '
									</tbody>
								</table>
							</td>
						</tr>';
		
		}

		ob_start();

		?>
		<tr valign="top" id="api_options">
			<th scope="row" class="titledesc"><?php _e( 'UPS Merger methods', 'pt-wc-ups-merger' ); ?></th>
			<td class="forminp">
				<table class="wc-shipping-zone-methods widefat">
					<thead>
						<tr>
							<th style="padding-left: 10px;"><?php _e( 'Title', 'pt-wc-ups-merger' ); ?></th>
							<th><?php _e( 'Services', 'pt-wc-ups-merger' ); ?></th>
							<th><?php _e( 'Shipping Class', 'pt-wc-ups-merger' ); ?></th>
						</tr>
					</thead>
					<tfoot>
						<tr>
							<th style="padding-left: 10px;">&nbsp;</th>
							<th colspan="2" style="padding-left: 10px;">
								<small class="description"><?php _e( '<em>Services source: WooCommerce UPS Shipping plugin</em>.', 'pt-wc-ups-merger' ); ?></small><br/>
							</th>
						</tr>
					</tfoot>
					<tbody>
						<?php echo $rows; ?>
					</tbody>
				</table>
				<input type="hidden" name="merged_services" value="<?php echo implode( ',', array_keys( $merge_services ) ); ?>" />
			</td>
		</tr>
		<?php
		return ob_get_clean();
	}


	/**
	 * generate_ups_settings_html function.
	 *
	 * @access public
	 * @return void
	 */
	function generate_ups_settings_html() {
		ob_start();
		?>
		<tr valign="top" id="api_options">
			<th scope="row" class="titledesc"><?php _e( 'UPS Settings', 'pt-wc-ups-merger' ); ?></th>
			<td class="forminp">
				<table class="ups_options widefat">
					<thead>
						<tr>
							<th style="padding-left: 10px;"><?php _e( 'API Setting', 'pt-wc-ups-merger' ); ?></th>
							<th><?php _e( 'Value', 'pt-wc-ups-merger' ); ?></th>
						</tr>
					</thead>
					<tfoot>
						<tr>
							<th colspan="2" style="padding-left: 10px;">
								<small class="description"><?php _e( '<em>Source: WooCommerce UPS Shipping plugin</em>.', 'pt-wc-ups-merger' ); ?></small><br/>
							</th>
						</tr>
					</tfoot>
					<tbody>
						<tr>
							<td><?php _e( 'UPS User ID: ', 'pt-wc-ups-merger' )?></td>
							<td><?php echo $this->ups->user_id;?></td>
						</tr>					
						<tr>
							<td><?php _e( 'UPS Password: ', 'pt-wc-ups-merger' )?></td>
							<td><?php echo $this->ups->password;?></td>
						</tr>
						<tr>
							<td><?php _e( 'UPS Access Key: ', 'pt-wc-ups-merger' )?></td>
							<td><?php echo $this->ups->access_key;?></td>
						</tr>
						<tr>
							<td><?php _e( 'UPS Account Number for White Plains: ', 'pt-wc-ups-merger' )?></td>
							<td><?php echo $this->ups->shipper_number;?></td>
						</tr>
						<tr>
							<td><?php _e( 'UPS Account Number for Jersey City: ', 'pt-wc-ups-merger' )?></td>
							<td>067789 (Hardcoded for shiping from Postal Code 07304)</td>
						</tr>

					</tbody>
				</table>
			</td>
		</tr>
		<?php
		return ob_get_clean();
	}

	
	/**
	 * calculate_shipping function. Just add our shipping options.
	 *
	 * @access public
	 * @param mixed $package
	 * @return void
	 */
	public function calculate_shipping( $package = array() ) {

		$pt_wc_ups_merger_options = get_option( 'woocommerce_pt-ups-merger_settings' );

		if ( isset( $pt_wc_ups_merger_options['debug'] ) ) {

			$debug = ( 'yes' == $pt_wc_ups_merger_options['debug'] );

		} else {

			$debug = false;

		}

		if ( 'no' == $pt_wc_ups_merger_options['enabled'] ) {
		
			if ( $debug ) {

				$msg = __( 'UPS Merger : Not enabled `@calculate_shipping`.', 'pt-wc-ups-merger' );

				if ( ! wc_has_notice( $msg ) ) {

					wc_add_notice( $msg );

				}

			}
			
			return;
		}
		
		if ( empty( $package ) ) {

			return;
		}

		if ( empty( $package['rates']  ) ) {
			
			if ( $debug ) {

				wc_add_notice( __('UPS Merger : No rates found, nothing to be merged.', 'pt-wc-ups-merger') );
				//wc_add_notice( 'RATES <pre>' . print_r( $package['rates'], 1 ) . '</pre>' );
				
			}
			
			return;
		}

		$instance_options  = get_option( $this->get_instance_option_key() );
		$disabled_services = (array) $instance_options['disabled_services'];

		$enabled_services = array();
		foreach( $disabled_services as $code => $status ) {

			if ( 'yes' == $status ) {

				$enabled_services[ $code ] = $this->services[ $code ]; // todo: instead of our private variable could be a value from instance settings //

			}

		}

		if ( empty( $enabled_services ) ) {

			if ( $debug ) {

				$zone = WC_Shipping_Zones::get_zone_by( 'instance_id', $this->instance_id );

				wc_add_notice( sprintf( __('UPS Merger : no services enabled, check UPS merge instance settings at %s.', 'pt-wc-ups-merger'), $zone->get_zone_name() ) );

			}

			return;
		}


		if ( $debug ) {
			//wc_add_notice( 'calculate_shipping package <pre>' . print_r( $package, 1 ) . '</pre>' );
			//wc_add_notice( 'Calculate Shipping - RATES - <pre>' . print_r( $package['rates'], 1 ) . '</pre>' );
		}


		$sort            = 1;
		$enabled_rates   = array();
		$processed_rates = array();

		foreach( $package['rates'] as $id => $rate ) {

			if ( 'ups' == $rate->get_method_id() ) {
			
				if ( in_array( $rate->get_instance_id(), $package['exclude_instances'] ) ) {

					if ( 0 && $debug ) {

						$msg = 'Not used UPS `' . $rate->get_label() . '` | id ' . $id . ' | Instance id ' . $rate->get_instance_id();

						if ( ! wc_has_notice( $msg ) ) {

							wc_add_notice( $msg );

						}

					}

					continue;
				}

				$ups_rate_id = explode( ':' , $id );
				$code        = array_pop( $ups_rate_id );

				if ( in_array( $code, array_keys( $enabled_services ) ) ) {

					$rate_id       = $this->get_rate_id( $code );
					$instance_code = $rate->get_instance_id() . ':' . $code;

					if ( ! in_array( $instance_code, $processed_rates ) ) { 

						$processed_rates[] = $instance_code;
						$rate_cost         = $rate->get_cost();

						if ( array_key_exists( $rate_id, $enabled_rates ) ) {

							if ( 0 && $debug ) {

								$msg = 'Equivalent `' . $rate->get_label() . '` | id ' . $id . ' |';

								if ( ! wc_has_notice( $msg ) ) {

									wc_add_notice( $msg );

								}

							}

							$enabled_rates [ $rate_id ] ['meta_data' ][ $rate->get_label() ] = $id;

						} else {

							// new rate, set values

							$rate_name = $enabled_services[ $code ] . ' (' . $this->title . ')';
							$rate_sort = $sort++;
							$rate_meta = array( $rate->get_label() => $id );

							if ( 0 && $debug ) {

								$msg = 'Equivalent `' . $rate->get_label() . '` | id ' . $id . ' |';

								if ( ! wc_has_notice( $msg ) ) {

									wc_add_notice( $msg );

								}

							}

							$enabled_rates [ $rate_id ] = array(
																'id'        => $rate_id,
																'label'     => $rate_name,
																'cost'      => 0 * $rate_cost,
																'sort'      => $rate_sort,
																'meta_data' => $rate_meta,
															);
						}
					}
				}
			}
		}

		if ( empty( $enabled_rates ) && $debug ) {

			wc_add_notice( __('UPS Merger: No enabled UPS rates found, nothing to be merged.', 'pt-wc-ups-merger') );

		}

		foreach ( $enabled_rates as $id => $rate ) {

			$this->add_rate( $rate );

		}

		if ( 0 && 12043 == get_current_user_id() ) {
			//wc_add_notice( 'Package name <pre>' . print_r( $package['name'], 1 ) . '</pre>', 'notice' );
			//wc_add_notice( 'Package <pre>' . print_r( $package, 1 ) . '</pre>', 'notice' );
			wc_add_notice( 'Package name <b>' . $package['name'] . '</b>, enabled rates (calculate_shipping)<pre>' . print_r( $enabled_rates, 1 ) . '</pre>', 'notice' );
			wc_print_notices();
		}

		return; 

	}
	
		
	function show_message() {

		if ( get_option( 'woocommerce_shipping_cost_requires_address' ) == 'yes' ) {

			if ( ! WC()->customer->has_calculated_shipping() ) {

				if ( ! WC()->customer->get_shipping_country() || ( ! WC()->customer->get_shipping_state() && ! WC()->customer->get_shipping_postcode() ) ) {
					?>

						<tr>
							<td colspan="6" class="actions">
								<?php _e('You need to provide an address to see shipping rates.', 'pt-wc-ups-merger' ); ?>
							</td>
						</tr>

					<?php
				}
			}
		}
	}	
}
