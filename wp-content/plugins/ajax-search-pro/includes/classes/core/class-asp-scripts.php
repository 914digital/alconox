<?php
/* Prevent direct access */
defined('ABSPATH') or die("You can't access this file directly.");

class WD_ASP_Scripts {
	private static $_instance;

	private $scripts = array(
		'wd-asp-photostack' => array(
			'src' => 'js/{js_source}/external/photostack.js',
			'prereq' => false
		),
		'wd-asp-select2' => array(
			'src' => 'js/{js_source}/external/jquery.select2.js',
			'prereq' => array('jquery')
		),
		'wd-asp-lazy' => array(
			'src' => 'js/{js_source}/external/lazy.js',
			'prereq' => array('wd-asp-ajaxsearchpro')
		),
		'wd-asp-scroll-simple' => array(
			'src' => 'js/{js_source}/external/simplebar.js',
			'prereq' => false
		),
		'wd-asp-nouislider' => array(
			'src' => 'js/{js_source}/external/nouislider.all.js',
			'prereq' => false
		),
		'wd-asp-rpp-isotope' => array(
			'src' => 'js/{js_source}/external/isotope.js',
			'prereq' => false
		),
		'wd-asp-ajaxsearchpro' => array(
			'src' => 'js/{js_source}/plugin/merged/asp.js',
			'prereq' => false
		),
		'wd-asp-async-loader' => array(
			'src' => 'js/{js_source}/plugin/shared/async-css.js',
			'prereq' => false
		),
		'wd-asp-prereq-and-wrapper' => array(
			'src' => 'js/{js_source}/plugin/merged/asp-prereq-and-wrapper.js',
			'prereq' => false
		)
	);

	private $optimized_scripts = array(
		'wd-asp-ajaxsearchpro' => array(
			'wd-asp-ajaxsearchpro-prereq' => array(
				'handle' => 'wd-asp-ajaxsearchpro',	// Handle alias, for the enqueue
				'src' => 'js/{js_source}/plugin/optimized/asp-prereq.js',
			),
			'wd-asp-ajaxsearchpro-core' => array(
				'src' => 'js/{js_source}/plugin/optimized/asp-core.js',
			),
			'wd-asp-ajaxsearchpro-settings' => array(
				'src' => 'js/{js_source}/plugin/optimized/asp-settings.js',
				'prereq' => array('wd-asp-ajaxsearchpro'),
			),
			'wd-asp-ajaxsearchpro-compact' => array(
				'src' => 'js/{js_source}/plugin/optimized/asp-compact.js',
				'prereq' => array('wd-asp-ajaxsearchpro'),
			),
			'wd-asp-ajaxsearchpro-vertical' => array(
				'src' => 'js/{js_source}/plugin/optimized/asp-results-vertical.js',
				'prereq' => array('wd-asp-ajaxsearchpro'),
			),
			'wd-asp-ajaxsearchpro-horizontal' => array(
				'src' => 'js/{js_source}/plugin/optimized/asp-results-horizontal.js',
				'prereq' => array('wd-asp-ajaxsearchpro'),
			),
			'wd-asp-ajaxsearchpro-polaroid' => array(
				'src' => 'js/{js_source}/plugin/optimized/asp-results-polaroid.js',
				'prereq' => array('wd-asp-ajaxsearchpro'),
			),
			'wd-asp-ajaxsearchpro-isotopic' => array(
				'src' => 'js/{js_source}/plugin/optimized/asp-results-isotopic.js',
				'prereq' => array('wd-asp-ajaxsearchpro'),
			),
			'wd-asp-ajaxsearchpro-ga' => array(
				'src' => 'js/{js_source}/plugin/optimized/asp-ga.js',
				'prereq' => array('wd-asp-ajaxsearchpro'),
			),
			'wd-asp-ajaxsearchpro-live' => array(
				'src' => 'js/{js_source}/plugin/optimized/asp-live.js',
				'prereq' => array('wd-asp-ajaxsearchpro'),
			),
			'wd-asp-ajaxsearchpro-autocomplete' => array(
				'src' => 'js/{js_source}/plugin/optimized/asp-autocomplete.js',
				'prereq' => array('wd-asp-ajaxsearchpro'),
			),
			'wd-asp-ajaxsearchpro-load' => array(
				'src' => 'js/{js_source}/plugin/optimized/asp-load.js',
				'prereq' => true, // TRUE => previously loaded script
			),
			'wd-asp-ajaxsearchpro-wrapper' => array(
				'src' => 'js/{js_source}/plugin/optimized/asp-wrapper.js',
				'prereq' => true, // TRUE => previously loaded script
			)
		)
	);

	private $dev_scripts = array();
	private $dev_scripts_config = 'js/src/config.cfg';

	private function __construct() {
		if ( defined('ASP_DEBUG') && ASP_DEBUG == 1 ) {
			$blocks = $this->process_config(ASP_PATH . $this->dev_scripts_config);
			foreach($blocks as $handle => $block) {
				$this->dev_scripts[$handle] = array(
					'src' => $this->get_block_files_ordered($block)
				);
			}
		}
	}

	public function get( $handles = array(), $minified = true, $optimized = false, $except = array() ) {
		$handles = is_string($handles) ? array($handles) : $handles;
		$handles = count($handles) == 0 ? array_keys($this->scripts) : $handles;
		$js_source = $minified ? "min" : "nomin";
		$return = array();

		foreach ( $handles as $handle ) {
			if ( in_array($handle, $except) || !$this->isRequired($handle) ) {
				continue;
			}
			if ( isset($this->scripts[$handle]) ) {
				if ( defined('ASP_DEBUG') &&
					ASP_DEBUG == 1 &&
					isset($this->dev_scripts[$handle]) &&
					wd_asp()->manager->getContext() != "backend"
				) {
					$src = $this->dev_scripts[$handle]['src'];
					$src = !is_array($src) ? array($src) : $src;
					$i = 0;
					foreach ( $src as $file_path ) {
						$_handle = $i == 0 ? $handle : $handle . '_' . $i;
						$url = esc_url_raw(str_replace(
							wp_normalize_path( untrailingslashit( ABSPATH ) ),
							site_url(),
							wp_normalize_path( $file_path )
						));
						$return[] = array(
							'handle' => $_handle,
							'src' => str_replace(
									array('{js_source}'),
									array($js_source),
									$url
								),
							'prereq' => $this->scripts[$handle]['prereq']
						);
						++$i;
					}
					continue;
				}

				if ( $optimized && isset($this->optimized_scripts[$handle]) ) {
					$prev_handle = '';
					foreach ( $this->optimized_scripts[$handle] as $optimized_script_handle => $optimized_script ) {
						if ( in_array($optimized_script_handle, $except) || !$this->isRequired($optimized_script_handle) ) {
							continue;
						}
						$prereq = !isset($optimized_script['prereq']) || $optimized_script['prereq'] === false ? array() : $optimized_script['prereq'];
						if ( $prereq === true ) {
							$prereq = array($prev_handle);
						}
						$return[] = array(
							'handle' => isset($optimized_script['handle']) ? $optimized_script['handle'] : $optimized_script_handle,
							'src' => ASP_URL . str_replace(
									array('{js_source}'),
									array($js_source),
									$optimized_script['src']
								),
							'prereq' => $prereq
						);

						$prev_handle = $optimized_script_handle;
					}
					continue;
				}

				$return[] = array(
					'handle' => $handle,
					'src' => ASP_URL . str_replace(
						array('{js_source}'),
						array($js_source),
						$this->scripts[$handle]['src']
					),
					'prereq' => $this->scripts[$handle]['prereq']
				);
			} else if ( $optimized && wd_in_array_r($handle, $this->optimized_scripts) ) {
				foreach ( $this->optimized_scripts as $opt_handle => $scripts ) {
					if ( isset($scripts[$handle]) ) {
						$return[] = array(
							'handle' => $handle,
							'src' => ASP_URL . str_replace(
									array('{js_source}'),
									array($js_source),
									$scripts[$handle]['src']
								),
							'prereq' => $scripts[$handle]['prereq']
						);
					}
				}
			}
		}

		return $return;
	}

	public function enqueue( $scripts = array(), $args = array() ) {
		$defaults = array(
			'media_query' => '',
			'in_footer'	=> true,
			'prereq'	=> array()
		);
		$args = wp_parse_args($args, $defaults);
		foreach ( $scripts as $script ) {
			if ( isset($script['prereq']) ) {
				if ( $script['prereq'] === false ) {
					$prereq = array();
				} else {
					$prereq = $script['prereq'];
				}
			} else {
				$prereq = $args['prereq'];
			}
			wp_register_script(
				$script['handle'],
				$script['src'],
				$prereq,
				$args['media_query'],
				$args['in_footer']
			);
			wp_enqueue_script($script['handle']);
		}

		return $scripts;
	}

	public function isRequired( $handle ) {
		if ( wd_asp()->manager->getContext() == "backend" ) {
			return true;
		}

		$required = false;
		switch ( $handle ) {
			case 'wd-asp-photostack':
			case 'wd-asp-ajaxsearchpro-polaroid':
				if ( asp_is_asset_required('polaroid') ) {
					$required = true;
				}
				break;
			case 'wd-asp-select2':
				if ( asp_is_asset_required('select2') ) {
					$required = true;
				}
				break;
			case 'wd-asp-lazy':
				if ( wd_asp()->o['asp_compatibility']['load_lazy_js'] == 1 ) {
					$required = true;
				}
				break;
			case 'wd-asp-scroll-simple':
				if (
					wd_asp()->o['asp_compatibility']['load_mcustom_js'] == "yes"
					&& asp_is_asset_required('simplebar')
				) {
					$required = true;
				}
				break;
			case 'wd-asp-nouislider':
				if ( asp_is_asset_required('noui') ) {
					$required = true;
				}
				break;
			case 'wd-asp-rpp-isotope':
				if ( asp_is_asset_required('isotope') ) {
					$required = true;
				}
				break;
			case 'wd-asp-ajaxsearchpro-settings':
				if ( asp_is_asset_required('settings') ) {
					$required = true;
				}
				break;
			case 'wd-asp-ajaxsearchpro-compact':
				if ( asp_is_asset_required('compact') ) {
					$required = true;
				}
				break;
			case 'wd-asp-ajaxsearchpro-vertical':
				if ( asp_is_asset_required('vertical') ) {
					$required = true;
				}
				break;
			case 'wd-asp-ajaxsearchpro-horizontal':
				if ( asp_is_asset_required('horizontal') ) {
					$required = true;
				}
				break;
			case 'wd-asp-ajaxsearchpro-isotopic':
				if ( asp_is_asset_required('isotopic') ) {
					$required = true;
				}
				break;
			case 'wd-asp-ajaxsearchpro-autopopulate':
				if ( asp_is_asset_required('autopopulate') ) {
					$required = true;
				}
				break;
			case 'wd-asp-ajaxsearchpro-autocomplete':
				if ( asp_is_asset_required('autocomplete') ) {
					$required = true;
				}
				break;
			case 'wd-asp-ajaxsearchpro-live':
			default:
				$required = true;
				break;
		}

		return $required;
	}

	private function process_config($cfg) {
		$blocks = $all_blocks = parse_ini_file( $cfg, true);

		foreach ( $blocks as $name => &$block ) {
			if ( isset($block['config']) ) {
				foreach ( $block['config'] as $config ) {
					$all_blocks = array_merge($all_blocks, $this->process_config( $config ));
				}
				unset($all_blocks[$name]);
			} else {
				if ( !isset($block['input_dir']) ) {
					$all_blocks[$name]['input_dir'] = dirname($cfg) . "/";
				}
			}
		}

		return $all_blocks;
	}

	private function get_block_files_ordered($block, $extensions = array('js')) {
		$parsed = array();
		$input_dir = isset($block['input_dir']) ? $block['input_dir'] : $_SERVER['DOCUMENT_ROOT'] . '/';
		$exclude = isset($block['exclude']) ? $block['exclude'] : array();
		foreach ( $exclude as &$e ) {
			$e = $input_dir . $e;
		}
		foreach ( $block['input'] as $input ) {
			$path = $input_dir . $input;
			if ( is_dir($path) ) {
				$dir = array_diff(scandir($path),  array('..', '.'));
				foreach ( $dir as $file ) {
					$filepath = $path . '/' . $file;

					if ( is_dir($filepath) ) {
						continue;
					}

					if (
						in_array(pathinfo($filepath)['extension'], $extensions) &&
						!in_array($filepath, $exclude) &&
						!in_array($filepath, $parsed)
					) {
						$parsed[] = $filepath;
					}
				}
			} else {
				if ( !in_array($path, $parsed) ) {
					$parsed[] = $path;
				}
			}
		}

		return $parsed;
	}

	/**
	 * Get the instane
	 *
	 * @return self
	 */
	public static function getInstance() {
		if (!(self::$_instance instanceof self)) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}
}