<?php
namespace TrxAddons\ElementorTemplates\Globals;

defined( 'ABSPATH' ) || exit;

use TrxAddons\ElementorTemplates\Utils as TrxAddonsUtils;
// use TrxAddons\ElementorTemplates\Options;

use Elementor\Core\Base\Module;
use Elementor\Core\Kits\Controls\Repeater as Kit_Repeater;
use Elementor\Controls_Stack;
use Elementor\Controls_Manager;
use Elementor\Repeater;

/**
 * Class Colors.
 */
class ColorsEditor extends Module {

	// Elementor is don't support the defis in the global names
	var $color_prefix = 'theme_color_';
	var $scheme_prefix = 'trx_addons_global_colors_scheme_';

	var $elementor_kit_settings_meta_key = '_elementor_page_settings';
	var $elementor_kit_css_meta_key = '_elementor_css';

	/**
	 * Colors constructor.
	 */
	public function __construct() {

		add_action( 'elementor/element/kit/section_buttons/after_section_end', array( $this, 'register_global_colors' ), 10, 2 );
		add_filter( 'elementor/documents/ajax_save/return_data', array( $this, 'save_global_colors' ), 10, 2 );

		// Uncomment to reset global colors (dev only)
		// add_action( 'init', array( $this, 'reset_global_colors' ) );

		$theme_slug = str_replace( '-', '_', get_template() );

		if ( trx_addons_exists_elementor() ) {
			// Add CSS variables to the theme styles
			// add_filter( "{$theme_slug}_filter_get_css", array( $this, 'add_css_vars' ), 10, 2 );

			// Update global colors after theme options save
			add_action( "{$theme_slug}_action_just_save_options", array( $this, 'update_global_colors_after_theme_options_save' ), 10, 1 );

			if ( trx_addons_is_preview( 'elementor' ) ) {
				add_action( 'wp_footer', array( $this, 'add_preview_style' ) );
			}
		}

	}

	/**
	 * Get module name.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'trx-addons-colors';
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
	 * Reset global colors in the default Elementor's kit
	 */
	public function reset_global_colors() {
		$remove_keys = array(
			$this->scheme_prefix,
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
	 * Register Global Color controls for theme-specific color schemes.
	 *
	 * @param Controls_Stack $element Controls object.
	 * @param string         $section_id Section ID.
	 */
	public function register_global_colors( Controls_Stack $element, $section_id ) {

		if ( ! is_object( $element ) ) {
			return;
		}
		
		// Get the theme color schemes
		$schemes = trx_addons_get_theme_color_schemes();
		if ( empty( $schemes ) || ! is_array( $schemes ) ) {
			return;
		}
		$default_scheme = trx_addons_get_theme_option( 'color_scheme', 'default' );

		$element->start_controls_section( 'trx_addons_global_colors_section', array(
			'label' => esc_html__( 'Theme Colors', 'trx_addons' ),
			'tab'   => 'global-colors',
		) );

		$element->add_control( 'trx_addons_global_colors_description', array(
			'raw'             => __( 'You can edit color schemes also in adminmenu "Theme Panel - Theme Options - Colors" or in "Appearance - Сustomizer".', 'trx_addons' ),
			'type'            => Controls_Manager::RAW_HTML,
			'content_classes' => 'elementor-descriptor',
		) );

		$element->start_controls_tabs( 'trx_addons_global_colors_section_tabs', array(
			'separator' => 'before',
		) );

		foreach( $schemes as $scheme => $data ) {

			// Show colors for the current scheme only
			if ( $scheme != $default_scheme ) {
				continue;
			}

			$element->start_controls_tab(
				'trx_addons_tab_global_colors_' . $scheme,
				array( 'label' => $data['title'] )
			);

			$repeater = new Repeater();

			$repeater->add_control( 'title', array(
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'required'    => true,
			) );

			// Color Value
			$repeater->add_control( 'color', array(
				'type'        => Controls_Manager::COLOR,
				'label_block' => true,
				'dynamic'     => array(),
				'selectors'   => array(
					'{{WRAPPER}}' => '--e-global-color-{{_id.VALUE}}: {{VALUE}}',
//					'{{WRAPPER}}' => '--{{_id.VALUE}}: {{VALUE}}',
				),
				'global'      => array(
					'active' => false,
				),
			) );

			// Scheme Colors
			$scheme_colors = array();
			foreach ( $data['colors'] as $key => $value ) {
				$scheme_colors[] = array(
					'_id'   => $this->color_prefix . $key,
					'title' => apply_filters( 'trx_addons_filter_global_color_title', sprintf( _x( 'Theme %s', 'Global color title in Elementor', 'trx_addons' ), ucfirst( str_replace( '_', ' ', $key ) ) ), $key ),
					'color' => $value,
				);
			}

			$element->add_control( $this->scheme_prefix . $scheme, array(
				'type'         => Kit_Repeater::CONTROL_TYPE,
				'fields'       => $repeater->get_controls(),
				'default'      => $scheme_colors,
				'item_actions' => array(
					'add'    => false,
					'remove' => false,
					'sort'   => false,

				),
				'separator'    => 'after',
			) );

			$element->end_controls_tab();
		}

		$element->end_controls_tabs();

		$element->add_control( 'trx_addons_global_reset_colors', array(
			'label' => __( 'Reset labels & colors', 'trx_addons' ),
			'type'  => 'button',
			'text'  => __( 'Reset', 'trx_addons' ),
			'event' => 'trx_addons_elementor_extension:resetGlobalColors',
		) );

		$element->end_controls_section();
	}

	// Save Page Options via AJAX from Elementor Editor
	// (called when any option is changed)
	public function save_global_colors( $response_data, $document ) {
		$post_id = $document->get_main_id();
		if ( $post_id > 0 ) {
			$actions = json_decode( trx_addons_get_value_gp( 'actions' ), true );
			if ( is_array( $actions ) && isset( $actions['save_builder']['data']['settings'] ) && is_array( $actions['save_builder']['data']['settings'] ) ) {
				$settings = $actions['save_builder']['data']['settings'];
				if ( is_array( $settings ) ) {
					$schemes = trx_addons_get_theme_color_schemes();
					$updated = false;
					if ( ! empty( $schemes ) && is_array( $schemes ) ) {
						foreach( $schemes as $scheme => $data ) {
							if ( ! empty( $data['colors'] ) && is_array( $data['colors'] ) && isset( $settings[ $this->scheme_prefix . $scheme ] ) && is_array( $settings[ $this->scheme_prefix . $scheme ] ) ) {
								foreach ( $settings[ $this->scheme_prefix . $scheme ] as $color ) {
									$color_id = str_replace( $this->color_prefix, '', $color['_id'] );
									if ( isset( $color['color'] ) && isset( $data['colors'][ $color_id ] ) && $color['color'] != $data['colors'][ $color_id ] ) {
										$schemes[ $scheme ]['colors'][ $color_id ] = $color['color'];
										$updated = true;
									}
								}
							}
						}
						// If colors are updated - save them to the theme options
						if ( $updated ) {
							$options = trx_addons_get_theme_options();
							$options['scheme_storage'] = serialize( $schemes );
							trx_addons_update_theme_options( $options, true );
						}
					}			
				}
			}
		}
		return $response_data;
	}

	// Add Elementor-specific colors to the theme custom CSS
	function add_css_vars( $css, $args ) {
		if ( isset( $css['colors'] ) && isset( $args['colors'] ) ) {
			$colors = $args['colors'];
			if ( is_array( $colors ) && count( $colors ) > 0 ) {
				$tmp = "body.scheme_{$args['scheme']},.scheme_{$args['scheme']} {\n";
				foreach ( $colors as $color => $value ) {
					$tmp .= "--e-global-color-{$this->color_prefix}{$color}: {$value};\n";
				}
				$css['colors'] = $tmp . "\n}\n" . $css['colors'];
			}
		}
		return $css;
	}

	/**
	 * Update global colors after theme options save
	 * 
	 * @hooked trx_addons_action_just_save_options
	 *
	 * @param array $values Theme options.
	 */
	public function update_global_colors_after_theme_options_save( $values ) {
		if ( ! empty( $values['scheme_storage'] ) ) {
			$schemes = trx_addons_unserialize( $values['scheme_storage'] );
			$default_scheme = ! empty( $values['color_scheme'] ) ? $values['color_scheme'] : 'default';
			if ( ! empty( $schemes[ $default_scheme ] ) ) {
				// Get the colors from the theme options and prepare them for the Elementor
				$new_colors = array();
				foreach ( $schemes[ $default_scheme ]['colors'] as $key => $value ) {
					$new_colors[] = array(
						'_id'   => $this->color_prefix . $key,
						'title' => ucfirst( str_replace( '_', ' ', $key ) ),
						'color' => $value,
					);
				}
				// Get the default Elementor's kit.
				// In this point we can't use the \Elementor\Plugin::instance()->kits_manager->get_active_id()
				// because the theme options are saved before the Elementor's kit is activated.
				$kit_id = TrxAddonsUtils::get_active_kit_id();
				if ( ! empty( $kit_id ) ) {
					// Get settings from the default Elementor's kit
					$meta = get_post_meta( $kit_id, $this->elementor_kit_settings_meta_key, true );
					if ( isset( $meta[ $this->scheme_prefix . $default_scheme ] ) && is_array( $meta[ $this->scheme_prefix . $default_scheme ] ) ) {
						// If the settings are contain the global colors - update them
						$updated = false;
						foreach ( $new_colors as $color ) {
							foreach ( $meta[ $this->scheme_prefix . $default_scheme ] as $k => $v ) {
								if ( $color['_id'] == $v['_id'] ) {
									if ( $color['color'] != $v['color'] ) {
										$meta[ $this->scheme_prefix . $default_scheme ][ $k ] = $color;
										$updated = true;
									}
									break;
								}
							}
						}
					} else {
						// Add the new colors to the default Elementor's kit
						$meta[ $this->scheme_prefix . $default_scheme ] = $new_colors;
						$updated = true;
					}
					if ( $updated ) {
						// Save the updated settings
						update_post_meta( $kit_id, $this->elementor_kit_settings_meta_key, $meta );
						// Clear a kit CSS to apply the new colors
						update_post_meta( $kit_id, $this->elementor_kit_css_meta_key, '' );
					}
				}
			}
		}
	}

	// Add a custom styles to the Elementor's preview with a theme colors as an Elementor global colors
	public function add_preview_style() {
		$schemes = trx_addons_get_theme_color_schemes();
		$default_scheme = trx_addons_get_theme_option( 'color_scheme', 'default' );
		if ( ! empty( $schemes ) && is_array( $schemes ) && isset( $schemes[ $default_scheme ] ) ) {
			$css = ".scheme_{$default_scheme},body.scheme_{$default_scheme},.scheme_{$default_scheme}:where(.editor-styles-wrapper) {";
			foreach ( $schemes[ $default_scheme ]['colors'] as $key => $value ) {
				$css .= "--theme-color-{$key}: var(--e-global-color-{$this->color_prefix}{$key});";
			}
			$css .= "}";
			trx_addons_add_inline_css( $css );
		}
	}
}
