<?php
namespace TrxAddons\ElementorTemplates\Globals;

defined( 'ABSPATH' ) || exit;

use TrxAddons\ElementorTemplates\Utils as TrxAddonsUtils;
// use TrxAddons\ElementorTemplates\Options;

use Elementor\Core\Base\Module;
use Elementor\Controls_Manager;
use Elementor\Controls_Stack;
use Elementor\Core\Kits\Controls\Repeater as Global_Style_Repeater;
use Elementor\Group_Control_Typography;
use Elementor\Repeater;

/**
 * Class Typography.
 */
class TypographyEditor extends Module {

	// Elementor is not support the defis in the global names
	var $theme_fonts_setting = 'trx_addons_global_theme_fonts';		// Name of theme fonts setting in the Globals
	var $font_prefix = 'theme_font_';								// Prefix for the each font settings

	var $elementor_kit_settings_meta_key = '_elementor_page_settings';
	var $elementor_kit_css_meta_key = '_elementor_css';

	var $allowed_props = array( 'font-family', 'font-size', 'font-weight', 'text-transform', 'font-style', 'text-decoration', 'line-height', 'letter-spacing' );	//, 'word-spacing'

	/**
	 * Typography constructor.
	 */
	public function __construct() {
		add_action( 'elementor/element/kit/section_buttons/after_section_end', array( $this, 'register_global_fonts' ), 10, 2 );
		add_filter( 'elementor/documents/ajax_save/return_data', array( $this, 'save_global_fonts' ), 10, 2 );

		// Uncomment to reset global colors (dev only)
		// add_action( 'init', array( $this, 'reset_global_fonts' ) );

		$theme_slug = str_replace( '-', '_', get_template() );

		if ( trx_addons_exists_elementor() ) {
			// Add CSS variables to the theme styles
			add_filter( "{$theme_slug}_filter_get_css", array( $this, 'add_css_vars' ), 10, 2 );

			// Update global fonts after theme options are saved.
			// Action "action_just_save_options" is not used, because a theme_fonts are updated after this action is called.
			// add_action( "{$theme_slug}_action_just_save_options", array( $this, 'update_global_fonts_after_theme_options_save' ), 10, 1 );
			add_action( "{$theme_slug}_action_save_options", array( $this, 'update_global_fonts_after_theme_options_save' ) );

			if ( trx_addons_is_preview( 'elementor' ) ) {
				add_action( 'wp_footer', array( $this, 'add_preview_style' ) );
			}
		}
	}

	/**
	 * Get public name for control.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'trx-addons-typography';
	}

	/**
	 * Get label tooltip.
	 *
	 * @param string $text  Tooltip text.
	 *
	 * @return string  Tooltip HTML.
	 */
	protected function get_tooltip( $text ) {
		return ' <span class="hint--top-right hint--medium" aria-label="' . $text . '"><i class="fa fa-info-circle"></i></span>';
	}

	/**
	 * Reset global fonts in the default Elementor's kit
	 */
	public function reset_global_fonts() {
		$remove_keys = array(
			$this->theme_fonts_setting
		);
		$kit_id = \Elementor\Plugin::instance()->kits_manager->get_active_id();
		if ( ! empty( $kit_id ) ) {
			$meta = get_post_meta( $kit_id, $this->elementor_kit_settings_meta_key, true );
			if ( is_array( $meta ) ) {
				$need_update = false;
				foreach ( $meta as $k => $v ) {
					foreach ( $remove_keys as $key ) {
						if ( strpos( $k, $key ) === 0 ) {
							unset( $meta[ $k ] );
							$need_update = true;
						}
					}
				}
				if ( $need_update ) {
					update_post_meta( $kit_id, $this->elementor_kit_settings_meta_key, $meta );
				}
			}
		}
	}

	/**
	 * Register Style Kits Global Font controls.
	 *
	 * @param Controls_Stack $element Controls object.
	 * @param string         $section_id Section ID.
	 */
	public function register_global_fonts( Controls_Stack $element, $section_id ) {

		if ( ! is_object( $element ) ) {
			return;
		}

		$element->start_controls_section( 'trx_addons_global_fonts_section', array(
			'label' => esc_html__( 'Theme Fonts', 'trx_addons' ),
			'tab'   => 'global-typography',
		) );

		$element->add_control( 'trx_addons_global_fonts_description', array(
			'raw'             => __( 'You can edit a theme typography also in adminmenu "Theme Panel - Theme Options - Typography" or in "Appearance - Сustomizer".', 'trx_addons' ),
			'type'            => \Elementor\Controls_Manager::RAW_HTML,
			'content_classes' => 'elementor-descriptor',
		) );

		$repeater = new Repeater();

		$repeater->add_control(
			'title',
			array(
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'required'    => true,
			)
		);

		$load_fonts = trx_addons_call_theme_function( 'get_list_load_fonts', array( true ), array() );
		$new_fonts = array();
		if ( is_array( $load_fonts ) ) {
			foreach ( $load_fonts as $k => $v ) {
				$parts = explode( ',', $k );
				$k = str_replace( '"' , '', $parts[0] );
				$new_fonts[ $k ] = $v;
			}
		}

		$repeater->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'           => 'typography',
				'label'          => '',
				'global'         => array(
					'active' => false,
				),
				'fields_options' => array(
					'font_family'     => array(
						'type'    => Controls_Manager::SELECT,
						'options' => $new_fonts,
						'default' => 'inherit',
						'selectors' => array(
							'{{SELECTOR}}' => '--e-global-typography-{{external._id.VALUE}}-font-family: "{{VALUE}}"',
						),
					),
					'font_size'       => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--e-global-typography-{{external._id.VALUE}}-font-size: {{SIZE}}{{UNIT}}',
						),
					),
					'font_weight'     => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--e-global-typography-{{external._id.VALUE}}-font-weight: {{VALUE}}',
						),
					),
					'text_transform'  => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--e-global-typography-{{external._id.VALUE}}-text-transform: {{VALUE}}',
						),
					),
					'font_style'      => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--e-global-typography-{{external._id.VALUE}}-font-style: {{VALUE}}',
						),
					),
					'text_decoration' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--e-global-typography-{{external._id.VALUE}}-text-decoration: {{VALUE}}',
						),
					),
					'line_height'     => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--e-global-typography-{{external._id.VALUE}}-line-height: {{SIZE}}{{UNIT}}',
						),
					),
					'letter_spacing'  => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--e-global-typography-{{external._id.VALUE}}-letter-spacing: {{SIZE}}{{UNIT}}',
						),
					),
					'word_spacing'    => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--e-global-typography-{{external._id.VALUE}}-word-spacing: {{SIZE}}{{UNIT}}',
						),
					),
				),
			)
		);

		$element->add_control( $this->theme_fonts_setting, array(
			'type'         => Global_Style_Repeater::CONTROL_TYPE,
			'fields'       => $repeater->get_controls(),
			'default'      => $this->get_typography_from_fonts(),
			'item_actions' => array(
				'add'    => false,
				'remove' => false,
				'sort'   => false,
			),
			'separator'    => 'after',
		) );

		$element->add_control( 'trx_addons_global_reset_fonts', array(
			'label' => __( 'Reset labels & fonts', 'trx_addons' ),
			'type'  => 'button',
			'text'  => __( 'Reset', 'trx_addons' ),
			'event' => 'trx_addons_elementor_extension:resetGlobalFonts',
		) );

		$element->end_controls_section();
	}

	// Save Page Options via AJAX from Elementor Editor
	// (called when any option is changed)
	public function save_global_fonts( $response_data, $document ) {
		$post_id = $document->get_main_id();
		if ( $post_id > 0 ) {
			$actions = json_decode( trx_addons_get_value_gp( 'actions' ), true );
			if ( is_array( $actions ) && isset( $actions['save_builder']['data']['settings'] ) && is_array( $actions['save_builder']['data']['settings'] ) ) {
				$settings = $actions['save_builder']['data']['settings'];
				if ( is_array( $settings ) ) {
					$load_fonts = trx_addons_get_theme_data( 'load_fonts' );
					$fonts = trx_addons_get_theme_fonts();
					$values  = trx_addons_get_theme_options();
					$breakpoints = trx_addons_call_theme_function( 'get_theme_breakpoints', array(), array(
						'desktop' => array(),
						'tablet' => array(),
						'mobile' => array(),
					) );
					$updated = false;
					if ( ! empty( $settings[ $this->theme_fonts_setting ] ) && is_array( $settings[ $this->theme_fonts_setting ] ) ) {
						foreach ( $settings[ $this->theme_fonts_setting ] as $typography ) {
							$tag = str_replace( $this->font_prefix, '', $typography['_id'] );
							foreach ( $typography as $css_prop => $css_value ) {
								$css_prop = str_replace( 'typography_', '', $css_prop );
								$css_prop_real = '';
								foreach ( $breakpoints as $bp => $bpv ) {
									$suffix = $bp == 'desktop' ? '' : '_' . $bp;
									if ( empty( $suffix ) || substr( $css_prop, -strlen( $suffix ) ) == $suffix ) {
										if ( ! empty( $suffix ) ) {
											$css_prop = str_replace( $suffix, '', $css_prop );
										}
										if ( in_array( str_replace( '_', '-', $css_prop ), $this->allowed_props ) ) {
											$css_prop = str_replace( '_', '-', $css_prop );
											$css_prop_real = $css_prop . $suffix;
											break;
										}
									}
								}
								if ( ! in_array( $css_prop, $this->allowed_props ) ) {
									continue;
								}
								$is_empty = false;
								if ( is_array( $css_value ) ) {
									if ( isset( $css_value['size'] ) && isset( $css_value['unit'] ) ) {
										$is_empty = $css_value['size'] === '';
										$css_value = ! $is_empty ? $css_value['size'] . $css_value['unit'] : '';
									} else {
										$is_empty = true;
										$css_value = '';
									}
								}
								// if ( ! $is_empty ) {
									if ( $css_prop == 'font-family' && ! empty( $css_value ) && $css_value != 'inherit' && is_array( $load_fonts ) ) {
										foreach ( $load_fonts as $f ) {
											if ( $f['name'] == $css_value ) {
												$css_value = $this->get_font_family_string( $f );
												break;
											}
										}
									}
									if ( $css_value != $fonts[ $tag ][ $css_prop_real ] ) {
										$values[ "{$tag}_{$css_prop_real}" ] = $css_value;
										$updated = true;
									}
								// }
							}
						}			
						// If fonts are updated - save them to the theme options
						if ( $updated ) {
							trx_addons_update_theme_options( $values, true );
						}
					}
				}
			}
		}
		return $response_data;
	}

	// Return font-family string from the font parameters
	private function get_font_family_string( $font ) {
		$parts = array( $font['name'] );
		if ( ! empty( $font['family'] ) ) {
			$parts = array_merge( $parts, explode( ',', $font['family'] ) );
		}
		foreach( $parts as $k => $v ) {
			$v = trim( $v );
			$parts[ $k ] = strpos( $v, '"' ) === false && strpos( $v, ' ' ) !== false
								? '"' . trim( $v ) . '"'
								: $v;
		}
		return join( ',', $parts );
	}

	// Add Elementor-specific fonts to the theme custom CSS
	public function add_css_vars( $css, $args ) {
		if ( isset( $css['fonts'] ) && isset( $args['fonts'] ) ) {
			$fonts = $args['fonts'];
			if ( is_array( $fonts ) && count( $fonts ) > 0 ) {
				$breakpoints = trx_addons_get_theme_breakpoints();
				$tmp = '';
				$kit_id = TrxAddonsUtils::get_active_kit_id();
				$selector = $kit_id > 0 ? '.elementor-kit-' . $kit_id : ':root';
				foreach ( $breakpoints as $bp => $bpv ) {
					$suffix = $bp == 'desktop' ? '' : '_' . $bp;
					if ( ! empty( $suffix ) ) {
						$tmp .= "@media (max-width: {$bpv['max']}px) {\n";
					}
					$tmp .= $selector . " {\n";
					foreach( $fonts as $tag => $font ) {
						if ( is_array( $font ) ) {
							foreach ( $font as $css_prop => $css_value ) {
								if ( in_array( $css_prop, array( 'title', 'description' ) ) ) {
									continue;
								}
								// Skip responsive values
								if ( strpos( $css_prop, '_' ) !== false ) {
									continue;
								}
								if ( empty( $suffix ) || ! empty( $font["{$css_prop}{$suffix}"] ) ) {
									$param = $css_name = $css_prop;
									if ( strpos( $css_prop, ':' ) !== false ) {
										$css_name = str_replace( ':', '-', $css_prop );
										$parts = explode( ':', $css_prop );
										$css_prop = $parts[0];
									}
									$tmp .= "--e-global-typography-{$this->font_prefix}{$tag}-{$css_name}: "
												. ( ! empty( $font["{$param}{$suffix}"] ) && ! trx_addons_is_inherit( $font["{$param}{$suffix}"] )
													? ( in_array( $css_prop, array( 'font-size', 'letter-spacing', 'margin-top', 'margin-bottom', 'border-width', 'border-radius' ) )
														? trx_addons_prepare_css_value( $font["{$param}{$suffix}"] )
														: $font["{$param}{$suffix}"]
														)
													: 'inherit'
												) . ";\n";
								}
							}
						}
					}
					$tmp .= "\n}\n";
					if ( ! empty( $suffix ) ) {
						$tmp .= "\n}\n";
					}
				}
				$css['fonts'] = $tmp . $css['fonts'];
			}
		}
		return $css;
	}

	/**
	 * Update global fonts after theme options save
	 * 
	 * @hooked trx_addons_action_just_save_options
	 *
	 * @param array $values Theme options.
	 */
	public function update_global_fonts_after_theme_options_save( $values ) {
		// Get the default Elementor's kit.
		// In this point we can't use the \Elementor\Plugin::instance()->kits_manager->get_active_id()
		// because the theme options are saved before the Elementor's kit is activated.
		$kit_id = TrxAddonsUtils::get_active_kit_id();
		if ( ! empty( $kit_id ) ) {
			// Update settings in the default Elementor's kit
			$meta = get_post_meta( $kit_id, $this->elementor_kit_settings_meta_key, true );
			if ( is_array( $meta ) ) {
				$meta[ $this->theme_fonts_setting ] = $this->get_typography_from_fonts( true );
				// Save the updated settings
				update_post_meta( $kit_id, $this->elementor_kit_settings_meta_key, $meta );
				// Clear a kit CSS to apply the new colors
				update_post_meta( $kit_id, $this->elementor_kit_css_meta_key, '' );
			}
		}
	}

	/**
	 * Convert a theme fonts to the Elementor format
	 * 
	 * @param bool $db  If true - add the empty 'sizes' array to the font sizes for the Elementor DB format
	 * 
	 * @return array  The theme fonts in the Elementor format
	 */
	private function get_typography_from_fonts( $db = false ) {
		$theme_typography = array();
		// Get the theme fonts
		$fonts = trx_addons_get_theme_fonts();
		if ( is_array( $fonts ) ) {
			foreach ( $fonts as $tag => $v ) {
				$tag_settings = array(
					'_id'                   => $this->font_prefix . $tag,
					'title'                 => $v['title'],
					'typography_typography' => 'custom',
				);
				foreach ( $v as $css_prop => $css_value ) {
					$parts = explode( '_', $css_prop );
					if ( ! in_array( $parts[0], $this->allowed_props ) ) {
						continue;
					}
					$is_empty = empty( $css_value );
					// Convert options with units to the Elementor format (e.g. 'font-size' => '16px' to 'font-size' => array( 'size' => 16', 'unit' => 'px' )
					if ( in_array( $parts[0], array( 'font-size', 'line-height', 'letter-spacing', 'margin-top', 'margin-bottom' ) ) ) {
						$css_value = TrxAddonsUtils::parse_css_value( $css_value, $db );
					} else if ( in_array( $parts[0], array( 'font-family' ) ) ) {
						$parts2 = explode( ',', $css_value );
						$css_value = str_replace( array( '"', "'" ), '', $parts2[0] );
					}
					// Add the empty values to the Elementor format if this is not a DB call or if it's a desktop value
					if ( ! $db || count( $parts ) < 2 || ! $is_empty ) {
						$tag_settings[ 'typography_' . str_replace( '-', '_', $css_prop ) ] = $css_value;
					}
				}
				$theme_typography[] = $tag_settings;
			}
		}
		return $theme_typography;
	}

	// Add a custom styles to the Elementor's preview with a theme colors as an Elementor global colors
	public function add_preview_style() {
		$fonts = trx_addons_get_theme_fonts();
		if ( is_array( $fonts ) ) {
			$kit_id = TrxAddonsUtils::get_active_kit_id();
			$selector = $kit_id > 0 ? '.elementor-kit-' . $kit_id : ':root';
			$css = "{$selector} {";
			foreach ( $fonts as $tag => $v ) {
				foreach ( $v as $css_prop => $css_value ) {
					$parts = explode( '_', $css_prop );
					if ( ! in_array( $parts[0], $this->allowed_props ) ) {
						continue;
					}
					$css .= "--theme-font-{$tag}_{$parts[0]}: var(--e-global-typography-{$this->font_prefix}{$tag}-{$parts[0]});";
				}
			}
			$css .= "}";
			trx_addons_add_inline_css( $css );
		}
	}

}
