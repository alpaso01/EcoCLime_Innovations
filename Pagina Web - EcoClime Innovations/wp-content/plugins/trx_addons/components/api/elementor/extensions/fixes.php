<?php
/**
 * Elementor extension: Fixes for compatibility with new versions
 *
 * @package ThemeREX Addons
 * @since v2.18.4
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}


// Convert args of controls with a type ::REPEATER
//-------------------------------------------------------

if ( ! function_exists( 'trx_addons_elm_get_repeater_controls' ) ) {
	add_filter( 'trx_addons_sc_param_group_params', 'trx_addons_elm_get_repeater_controls', 999, 2 );
	/**
	 * Convert args of fields for the type ::REPEATER for the new Elementor version.
	 * Make an associative array from a list by key 'name'.
	 * After the update Elementor 3.1.0+ (or near) internal structure of field type ::REPEATER was changed
	 * (fields list was converted to the associative array) and as result js-errors appears in the Elementor Editor:
	 * "Cannot read property 'global' of undefined".
	 * "TypeError: undefined is not an object (evaluating 't[o].global')".
	 *
	 * @param array $args  Array with a group of fields
	 * @param string $sc   Shortcode slug
	 * 
	 * @return array     Array with a group of fields for the type ::REPEATER for the new Elementor version
	 */
	function trx_addons_elm_get_repeater_controls( $args, $sc = '' ) {
		if ( trx_addons_exists_elementor() && ! empty( $args[0]['name'] ) && ! isset( $args['_id'] ) ) {
			$repeater = new \Elementor\Repeater();
			$tab = '';
			if ( is_array( $args ) ) {
				foreach ( $args as $k => $v ) {
					if ( ! empty( $v['name'] ) ) {
						$k = $v['name'];
					}
					if ( empty( $tab ) && ! empty( $v['tab'] ) ) {
						$tab = $v['tab'];
					}
					if ( ! empty( $v['responsive'] ) ) {
						unset( $v['responsive'] );
						$repeater->add_responsive_control( $k, $v );
					} else {
						$repeater->add_control( $k, $v );
					}
				}
			}
			$controls = $repeater->get_controls();
			if ( ! empty( $tab ) && ! empty( $controls['_id']['tab'] ) && $controls['_id']['tab'] != $tab ) {
				$controls['_id']['tab'] = $tab;
			}
			return $controls;
		}
		return $args;
	}
}


// Prepare global atts
//-----------------------------------------------

if ( ! function_exists( 'trx_addons_elm_prepare_global_params' ) ) {
	add_filter( 'trx_addons_filter_sc_prepare_atts', 'trx_addons_elm_prepare_global_params', 10, 2 );
	/**
	 * Prepare global atts for the new Elementor version: add array keys by 'name' from __globals__
	 * After the update Elementor 3.0+ (or later) for settings with type ::COLOR global selector appears.
	 * Color value from this selects is not placed to the appropriate settings.
	 * 
	 * @hooked trx_addons_sc_prepare_atts
	 * 
	 * @trigger trx_addons_filter_prepare_global_param
	 *
	 * @param array $args  Array with atts
	 * @param string $sc   Shortcode slug
	 * 
	 * @return array     Array with atts
	 */
	function trx_addons_elm_prepare_global_params( $args, $sc = '' ) {
		foreach ( $args as $k => $v ) {
			if ( is_array( $v ) ) {
				if ( is_string( $k ) && $k == '__globals__' ) {
					foreach ( $v as $k1 => $v1 ) {
						if ( ! empty( $v1 ) ) {
							$args[ $k1 ] = apply_filters( 'trx_addons_filter_prepare_global_param', $v1, $k1 );
						}
					}
				} else {
					$args[ $k ] = trx_addons_elm_prepare_global_params( $v, $sc );
				}
			}
		}
		return $args;
	}
}

if ( ! function_exists( 'trx_addons_elm_prepare_global_color' ) ) {
	add_filter( 'trx_addons_filter_prepare_global_param', 'trx_addons_elm_prepare_global_color', 10, 2 );
	/**
	 * Return a CSS-var from global color key, i.e. 'globals/colors?id=1855627f'
	 * 
	 * @hooked trx_addons_elm_prepare_global_params
	 *
	 * @param string $value  Value of the setting
	 * @param string $key    Key of the setting
	 * 
	 * @return string     Value of the setting
	 */
	function trx_addons_elm_prepare_global_color( $value, $key ) {
		$prefix = 'globals/colors?id=';
		if ( strpos( $value, $prefix ) === 0 ) {
			$id = str_replace( $prefix, '', $value );
			$value = "var(--e-global-color-{$id})";
		}
		return $value;
	}
}


// Conditions with unavailable characters
//-------------------------------------------------

if ( false && ! function_exists('trx_addons_elm_remove_unavailable_conditions') ) {
	add_action( 'elementor/element/after_section_end', 'trx_addons_elm_remove_unavailable_conditions', 9999, 3 );
	/**
	 * Remove conditions where key contain unavailable characters.
	 * After the update Elementor 3.4.1 js-errors appears in the console and the Editor stop loading
	 * if the condition of any option contains a key with characters outside the range a-z 0-9 - _ [ ] !
	 * a mask '/([a-z_\-0-9]+)(?:\[([a-z_]+)])?(!?)$/i' is used in the editor.js and controls-stack.php
	 * This issue is resolved in Elementor 3.4.2 (according to it author)
	 * I leave this code commented for future cases (if appears)
	 * 
	 * @hooked elementor/element/after_section_end
	 *
	 * @param object $element  Elementor element object
	 * @param string $section_id  Section ID
	 * @param array $args  Array with additional arguments
	 */
	function trx_addons_elm_remove_unavailable_conditions( $element, $section_id='', $args='' ) {
		if ( ! is_object( $element ) ) {
			return;
		}
		$controls = $element->get_controls();
		if ( is_array( $controls ) ) {
			foreach( $controls as $k => $v ) {
				if ( ! empty( $v['condition'] ) && is_array( $v['condition'] ) ) {
					$chg = false;
					$condition = array();
					foreach( $v['condition'] as $k1 => $v1 ) {
						// If current condition contains a selector to the field  "Page template" - replace it with 'template'
						if ( strpos( $k1, '.editor-page-attributes__template' ) !== false || strpos( $k1, '#page_template' ) !== false ) {
							$condition['template'] = $v1;
							$chg = true;
						// Else if current condition contains any other selector - remove it
						} else if ( strpos( $k1, ' ' ) !== false || strpos( $k1, '.' ) !== false || strpos( $k1, '#' ) !== false ) {
							$chg = true;
						// Else - leave all other conditions unchanged
						} else {
							$condition[ $k1 ] = $v1;
						}
					}
					// Update 'condition' in the current control if changed
					if ( $chg ) {
						$element->update_control( $k, array(
										'condition' => $condition
									) );
					}
				}
			}
		}
	}
}


// Column paddings
//------------------------------------

if ( ! function_exists( 'trx_addons_elm_move_paddings_to_column_wrap' ) ) {
	add_action( 'elementor/element/before_section_end', 'trx_addons_elm_move_paddings_to_column_wrap', 10, 3 );
	/**
	 * Move a column paddings from the .elementor-element-wrap to the .elementor-column-wrap to compatibility with old themes
	 * 
	 * @hooked elementor/element/before_section_end
	 *
	 * @param object $element  Elementor element object
	 * @param string $section_id  Section ID
	 * @param array $args  Array with additional arguments
	 */
	function trx_addons_elm_move_paddings_to_column_wrap( $element, $section_id, $args ) {
		if ( is_object( $element ) ) {
			$el_name = $element->get_name();
			if ( 'column' == $el_name && 'section_advanced' == $section_id ) {
				$element->update_responsive_control( 'padding', array(
											'selectors' => array(
												'{{WRAPPER}} > .elementor-element-populated.elementor-column-wrap' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',	// Elm 2.9- (or DOM Optimization == Inactive)
												'{{WRAPPER}} > .elementor-element-populated.elementor-widget-wrap' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',	// Elm 3.0+
											)
										) );
			}
		}
	}
}


// Widgets and controls registration
//--------------------------------------------------

if ( ! function_exists( 'trx_addons_elm_register_control' ) ) {
	/**
	 * Wrapper for controls registration in Elementor according to the version of the plugin to prevent a deprecation warning.
	 * In Elementor 3.5.0 the method Plugin::$instance->controls_manager->register_control() is soft deprecated and
	 * replaced with Plugin::$instance->controls_manager->register().
	 *
	 * @param string $control_class  Control class name
	 * @param string $control_id     Control ID (for the legacy version of Elementor)
	 */
	function trx_addons_elm_register_control( $control_class, $control_id = '' ) {
		if ( is_object( $control_class ) || ( is_string( $control_class ) && class_exists( $control_class ) ) ) {
			if ( defined( 'ELEMENTOR_VERSION' ) && version_compare( ELEMENTOR_VERSION, '3.5.0', '>=' ) ) {
				\Elementor\Plugin::instance()->controls_manager->register( is_object( $control_class ) ? $control_class : new $control_class() );
			} else {
				\Elementor\Plugin::instance()->controls_manager->register_control( $control_id, is_object( $control_class ) ? $control_class : new $control_class() );
			}
		}
	}
}

if ( ! function_exists( 'trx_addons_elm_register_widget' ) ) {
	/**
	 * Wrapper for widgets registration in Elementor according to the version of the plugin to prevent a deprecation warning.
	 * In Elementor 3.5.0 the method Plugin::$instance->widgets_manager->register_widget_type() is soft deprecated and
	 * replaced with Plugin::$instance->widgets_manager->register().
	 *
	 * @param string $widget_class  Widget class name
	 */
	function trx_addons_elm_register_widget( $widget_class ) {
		if ( is_object( $widget_class ) || ( is_string( $widget_class ) && class_exists( $widget_class ) ) ) {
			if ( method_exists( \Elementor\Plugin::instance()->widgets_manager, 'register' ) ) {
				\Elementor\Plugin::instance()->widgets_manager->register( is_object( $widget_class ) ? $widget_class : new $widget_class() );
			} else {
				\Elementor\Plugin::instance()->widgets_manager->register_widget_type( is_object( $widget_class ) ? $widget_class : new $widget_class() );
			}
		}
	}
}

if ( ! function_exists( 'trx_addons_elementor_get_action_for_controls_registration' ) ) {
	/**
	 * Return an action name for controls registration in Elementor according to the version of the plugin
	 * to prevent a deprecation warning.
	 * In Elementor 3.5.0 the action 'elementor/controls/controls_registered' is soft deprecated
	 * and replaced with 'elementor/controls/register'.
	 *
	 * @return string  Action name
	 */
	function trx_addons_elementor_get_action_for_controls_registration() {
		return defined( 'ELEMENTOR_VERSION' ) && version_compare( ELEMENTOR_VERSION, '3.5.0', '>=' )
					? 'elementor/controls/register'
					: 'elementor/controls/controls_registered';
	}
}

if ( ! function_exists( 'trx_addons_elementor_get_action_for_widgets_registration' ) ) {
	/**
	 * Return an action name for widgets registration in Elementor according to the version of the plugin
	 * to prevent a deprecation warning.
	 * In Elementor 3.5.0 the action 'elementor/widgets/widgets_registered' is soft deprecated
	 * and replaced with 'elementor/widgets/register'.
	 *
	 * @return string  Action name
	 */
	function trx_addons_elementor_get_action_for_widgets_registration() {
		return defined( 'ELEMENTOR_VERSION' ) && version_compare( ELEMENTOR_VERSION, '3.5.0', '>=' )
					? 'elementor/widgets/register'
					: 'elementor/widgets/widgets_registered';
	}
}


// Elementor 3.16.0+ - use flexbox containers instead sections and columns
//--------------------------------------------------
if ( ! function_exists( 'trx_addons_elementor_add_body_class' ) ) {
	add_filter( 'body_class', 'trx_addons_elementor_add_body_class' );
	/**
	 * Add class to the body tag to detect Elementor 3.16.0+
	 * and use flexbox containers instead sections and columns
	 * 
	 * @hooked body_class
	 * 
	 * @param array $classes  Array with body classes
	 * 
	 * @return array     Array with body classes
	 */
	function trx_addons_elementor_add_body_class( $classes ) {
		if ( defined( 'ELEMENTOR_VERSION' ) && version_compare( ELEMENTOR_VERSION, '3.16.0', '>=' ) && trx_addons_elm_is_experiment_active( 'container' ) ) {
			$classes[] = 'elementor-use-container';
		}
		return $classes;
	}
}


// Elementor 3.18.0+ - disable to catch an output buffer if the experiment "Optimize Image Loading" is active
//--------------------------------------------------
if ( ! function_exists( 'trx_addons_elementor_disable_ob_start_footer' ) ) {
	add_action( 'init', 'trx_addons_elementor_disable_ob_start_footer', 9999 );
	/**
	 * Disable to catch an output buffer if the experiment "Optimize Image Loading" is active
	 * (in Elementor 3.18.0+ it is enabled by default)
	 * 
	 * @hooked init, 9999
	 */
	function trx_addons_elementor_disable_ob_start_footer() {
		if ( trx_addons_elm_is_experiment_active( 'elementor_optimized_image_loading', '1' ) ) {
			trx_addons_remove_action( 'get_footer', 'set_buffer', 'Elementor\\Modules\\ImageLoadingOptimization\\Module' );
		}
	}
}


// Remove duplicate attributes 'loading', 'decoding' and 'fetchpriority' from the image tag
//--------------------------------------------------
if ( ! function_exists( 'trx_addons_elementor_remove_duplicate_atts_from image_tags' ) ) {
	add_filter( 'trx_addons_filter_page_content', 'trx_addons_elementor_remove_duplicate_atts_from' );
	/**
	 * Remove duplicate attributes 'loading', 'decoding' and 'fetchpriority' from the image tag
	 * 
	 * @hooked trx_addons_filter_page_content
	 * 
	 * @param string $content  Page content
	 * 
	 * @return string     Modified page content
	 */
	function trx_addons_elementor_remove_duplicate_atts_from( $content ) {
		if ( trx_addons_elm_is_experiment_active( 'elementor_optimized_image_loading', '1' ) ) {
			$content = preg_replace( '/(<img[^>]+)(loading="[^"]+")([^>]+)loading="[^"]+"([^>]+)>/', '$1$2$3$4>', $content );
			$content = preg_replace( '/(<img[^>]+)(decoding="[^"]+")([^>]+)decoding="[^"]+"([^>]+)>/', '$1$2$3$4>', $content );
			$content = preg_replace( '/(<img[^>]+)(fetchpriority="[^"]+")([^>]+)fetchpriority="[^"]+"([^>]+)>/', '$1$2$3$4>', $content );
		}
		return $content;
	}
}


// Disable dequeue WordPress block styles for post/page if the sidebar is present
//-------------------------------------------------------------------------------
if ( ! function_exists( 'trx_addons_elm_disable_dequeue_block_styles' ) ) {
	add_action( 'wp_enqueue_scripts', 'trx_addons_elm_disable_dequeue_block_styles' );
	/**
	 * Disable dequeue WordPress block styles for post/page if the sidebar is present
	 *
	 * Hooks: add_action('wp_enqueue_scripts', 'trx_addons_elm_disable_dequeue_block_styles');
	 */
	function trx_addons_elm_disable_dequeue_block_styles() {
		if ( (int)get_option( 'elementor_optimized_gutenberg_loading', 1 ) > 0			// Elementor 3.18.0+ - Optimize Gutenberg Loading is enabled by default
			&& (int)trx_addons_get_option( 'disable_widgets_block_editor', 0 ) == 0		// Disable widgets in the Block Editor is not enabled - a new Widgets Editor is active
			&& trx_addons_call_theme_function( 'sidebar_present' ) === true				// Sidebar is present (call a theme function to check it)
			&& ( ! is_singular()														// If not a single post/page
				|| ( function_exists( 'trx_addons_gutenberg_is_content_built' )			// or check if the page/post content is not built with the Gutenberg Editor
					&& ! trx_addons_gutenberg_is_content_built()
					)
				)
		) {
			trx_addons_remove_action( 'wp_enqueue_scripts', 'dequeue_assets', 'Elementor\Modules\Gutenberg\Module' );
		}
	}
}


// Move Elementor styles and links before the 'trx_addons' styles
//---------------------------------------------------------------
if ( ! function_exists( 'trx_addons_elm_move_elementor_styles_before_trx_addons' ) ) {
	add_action( 'trx_addons_action_after_move_styles', 'trx_addons_elm_move_elementor_styles_before_trx_addons' );
	/**
	 * Move Elementor v.3.24.0+ styles (tags <link> and <style>) before the 'trx_addons' styles in the head output
	 * to avoid overriding the 'trx_addons' and theme's styles by the Elementor styles with the same selectors.
	 *
	 * Hooks: add_action('trx_addons_action_after_move_styles', 'trx_addons_elm_move_elementor_styles_before_trx_addons');
	 */
	function trx_addons_elm_move_elementor_styles_before_trx_addons() {
		$need = trx_addons_exists_elementor()
				&& defined( 'ELEMENTOR_VERSION' )
				&& version_compare( ELEMENTOR_VERSION, '3.24.0', '>=' );
				// && version_compare( ELEMENTOR_VERSION, '3.24.3', '<' );
		if ( apply_filters( 'trx_addons_filter_move_elementor_styles_before_trx_addons', $need ) ) {
			global $TRX_ADDONS_STORAGE;
			$TRX_ADDONS_STORAGE['capture_links'] = '';
			$TRX_ADDONS_STORAGE['capture_head_html'] = preg_replace_callback(
				'/<link [^>]*(href=[\'"][^\'"]*elementor\\/(assets|css)[^\'"]*[\'"])[^>]*>|<style[^>]*id=[\'"](mobile-)?elementor-[^\'"]*[\'"][^>]*>[\s\S]*<\/style>/Uix',
				function( $matches ) {
					if ( ! empty( $matches[0] ) ) {
						global $TRX_ADDONS_STORAGE;
						$TRX_ADDONS_STORAGE['capture_links'] .= "\n" . $matches[0];
					}
					return '';
				},
				$TRX_ADDONS_STORAGE['capture_head_html']
			);
			if ( ! empty( $TRX_ADDONS_STORAGE['capture_links'] ) ) {
				$TRX_ADDONS_STORAGE['capture_links_moved'] = false;
				$TRX_ADDONS_STORAGE['capture_head_html'] = preg_replace_callback(
					// Insert styles before the tag below
					'/<link [^>]*(id=[\'"]trx_addons-css[\'"])[^>]*>/',
					function( $matches ) {
						if ( ! empty( $matches[0] ) ) {
							global $TRX_ADDONS_STORAGE;
							$TRX_ADDONS_STORAGE['capture_links_moved'] = true;
							return $TRX_ADDONS_STORAGE['capture_links']
									. "\n" . $matches[0];
						}
						return '';
					},
					$TRX_ADDONS_STORAGE['capture_head_html']
				);
				// If links present, but not moved yet - append its to the end of the head output
				if ( ! $TRX_ADDONS_STORAGE['capture_links_moved'] ) {
					$TRX_ADDONS_STORAGE['capture_head_html'] .= $TRX_ADDONS_STORAGE['capture_links'];
				}
			}
		}
	}
}


// Elementor Pro
//------------------------------------------
if ( ! function_exists( 'trx_addons_elm_pro_woocommerce_wordpress_widget_css_class' ) ) {
	add_filter( 'elementor/widgets/wordpress/widget_args', 'trx_addons_elm_pro_woocommerce_wordpress_widget_css_class', 11, 2 );
	/**
	 * Fix for Elementor Pro 3.5.0+ - prevent to cross the WooCommerce widget's wrapper
	 * 
	 * @hook elementor/widgets/wordpress/widget_args
	 */
	function trx_addons_elm_pro_woocommerce_wordpress_widget_css_class( $default_widget_args, $widget ) {
		if ( is_object( $widget ) ) {
			$widget_instance = $widget->get_widget_instance();
			if ( ! empty( $widget_instance->widget_cssclass ) ) {
				$open_tag = sprintf( '<div class="%s">', $widget_instance->widget_cssclass );
				if ( substr( $default_widget_args['before_widget'], -strlen( $open_tag ) ) == $open_tag
					&& $default_widget_args['after_widget'] == '</aside></div>'
				) {
					$default_widget_args['after_widget'] = '</div></aside>';
				}
			}
		}
		return $default_widget_args;
	}
}


// Prevent to redirect to the Elementor's setup wizard after the plugin activation
//------------------------------------------

if ( ! function_exists( 'trx_addons_elm_prevent_redirect_to_wizard_after_activation' ) ) {
	add_action( 'admin_init', 'trx_addons_elm_prevent_redirect_to_wizard_after_activation', 1 );
	/**
	 * Fix for Elementor 3.6.8+ - prevent to redirect to the Elementor's setup wizard after the plugin activation
	 * until theme-specific plugins are installed and activated completely
	 * 
	 * @hook admin_init, 1
	 */
	function trx_addons_elm_prevent_redirect_to_wizard_after_activation() {
		if ( trx_addons_get_value_gp( 'page' ) == 'trx_addons_theme_panel' && get_transient( 'elementor_activation_redirect' ) ) {
			delete_transient( 'elementor_activation_redirect' );
		}
	}
}


// Convert old Elementor's settings to the new format: add a space after each category/term ID
//------------------------------------------

if ( ! function_exists( 'trx_addons_elm_convert_params_with_term_ids' ) ) {
	add_action( 'trx_addons_action_is_new_version_of_plugin', 'trx_addons_elm_convert_params_with_term_ids', 10, 2 );
	add_action( 'trx_addons_action_importer_import_end', 'trx_addons_elm_convert_params_with_term_ids' );
	add_action( 'admin_init', 'trx_addons_elm_convert_params_with_term_ids' );
	/**
	 * Convert old parameters to the new format after update plugin ThemeREX Addons to the new version or after import demo data.
	 * Get all metadata '_elementor_data' and convert old parameters with a term (category) IDs - add a space after each ID.
	 *
	 * @hooked trx_addons_action_is_new_version_of_plugin
	 * @hooked trx_addons_action_importer_import_end
	 * @hooked admin_init
	 * 
	 * @param string $new_version New version of the plugin.
	 * @param string $old_version Old version of the plugin.
	 */
	function trx_addons_elm_convert_params_with_term_ids( $new_version = '', $old_version = '' ) {
		$db_key    = 'elm_convert_params_with_term_ids';
		$db_limit  = trx_addons_db_update_get_limit();
		$db_offset = trx_addons_db_update_get_state( $db_key );
		if ( $db_offset === 0 ) {
			return;
		} else if ( $db_offset === false ) {
			$db_offset = 0;
		}
		if ( empty( $old_version ) ) {
			$old_version = get_option( 'trx_addons_version', '1.0' );
		}
		if ( version_compare( $old_version, '2.27.0', '<' ) || current_action() == 'trx_addons_action_importer_import_end' || $db_offset > 0 ) {
			global $wpdb;
			$rows = $wpdb->get_results( "SELECT post_id, meta_id, meta_value
											FROM {$wpdb->postmeta}
											WHERE meta_key='_elementor_data' && meta_value!=''
											ORDER BY meta_id ASC
											LIMIT {$db_offset}, {$db_limit}"
										);
			$total = is_array( $rows ) ? count( $rows ) : 0;
			if ( $total > 0 ) {
				foreach ( $rows as $row ) {
					$data = json_decode( $row->meta_value, true );
					if ( trx_addons_elm_convert_data_with_term_ids( $data ) ) {
						$wpdb->query( "UPDATE {$wpdb->postmeta} SET meta_value = '" . wp_slash( wp_json_encode( $data ) ) . "' WHERE meta_id = {$row->meta_id} LIMIT 1" );
					}
				}
				if ( $total == $db_limit ) {
					$db_offset += $total;
					trx_addons_db_update_set_state( $db_key, $db_offset );
				} else {
					trx_addons_db_update_set_state( $db_key, 0 );
				}
			} else {
				trx_addons_db_update_set_state( $db_key, 0 );
			}
		}
	}
}

if ( ! function_exists( 'trx_addons_elm_convert_data_with_term_ids' ) ) {
	/**
	 * Convert old parameters of shortcodes with a 'post_type' - 'taxonomy' - 'cat|category|terms|...'
	 * to the new format for each element in the Elementor data: add a space after each category/term ID.
	 * Attention! The parameter $elements passed by reference and modified inside this function!
	 * Return true if $elements is modified (converted) and needs to be saved
	 *
	 * @param array $elements  Array of elements. Passed by reference and modified inside this function!
	 * 
	 * @return boolean True if parameters was changed and needs to be saved
	 */
	function trx_addons_elm_convert_data_with_term_ids( &$elements ) {
		$modified = false;
		$sc = array( 'trx_sc_services', 'trx_sc_team', 'trx_sc_blogger', 'trx_sc_squeeze', 'trx_widget_categories_list', 'trx_widget_slider', 'trx_widget_video_list' );
		$fields = array( 'cat', 'cat_1', 'cat_2', 'cat_3' );
		if ( is_array( $elements ) ) {
			foreach( $elements as $k => $elm ) {
				// Convert parameters
				if ( ! empty( $elm['widgetType'] )
					&& in_array( $elm['widgetType'], $sc )
					&& ! empty( $elm['settings'] )
					&& is_array( $elm['settings'] )
					// If parameters 'post_type' and/or 'taxonomy' are not changed in the editor - they are not saved in the data
					// && ( ! empty( $elm['settings']['post_type'] ) || ! empty( $elm['settings']['post_type_1'] ) )
					// && ( ! empty( $elm['settings']['taxonomy'] ) || ! empty( $elm['settings']['taxonomy_1'] ) )
					&& ( ! empty( $elm['settings']['cat'] ) || ! empty( $elm['settings']['cat_1'] ) )
				) {
					foreach( $fields as $field ) {
						if ( ! empty( $elm['settings'][ $field ] ) ) {
							if ( is_array( $elm['settings'][ $field ] ) ) {
								$new_data = array();
								foreach( $elm['settings'][ $field ] as $cat ) {
									if ( $cat !== '' && $cat !== ' ' ) {
										$new_data[] = trim( (string)$cat ) . ' ';
									}
								}
								$elements[ $k ]['settings'][ $field ] = $new_data;
							} else {
								$elements[ $k ]['settings'][ $field ] = join( ' ,', array_map( 'trim', explode( ',', $elm['settings'][ $field ] ) ) ) . ' ';
							}
							$modified = true;
						}
					}
				}
				// Process inner elements
				if ( ! empty( $elm['elements'] ) && is_array( $elm['elements'] ) ) {
					$modified = trx_addons_elm_convert_data_with_term_ids( $elements[ $k ]['elements'] ) || $modified;
				}
			}
		}
		return $modified;
	}
}


// Elementor 3.21.0+ - add default value for the 'spacer' widget
//--------------------------------------------------
if ( ! function_exists( 'trx_addons_elm_add_spacer_default_value' ) ) {
	add_action( 'trx_addons_action_is_new_version_of_plugin', 'trx_addons_elm_add_spacer_default_value', 10, 2 );
	add_action( 'trx_addons_action_importer_import_end', 'trx_addons_elm_add_spacer_default_value' );
	add_action( 'admin_init', 'trx_addons_elm_add_spacer_default_value' );
	/**
	 * Add a default value to all widgets 'Spacer' after update plugin ThemeREX Addons to the new version or after import demo data
	 * (because a new version of Elementor don't display the 'Spacer' widget with an empty value)
	 *
	 * @hooked trx_addons_action_is_new_version_of_plugin
	 * @hooked trx_addons_action_importer_import_end
	 * @hooked admin_init
	 * 
	 * @param string $new_version New version of the plugin.
	 * @param string $old_version Old version of the plugin.
	 */
	function trx_addons_elm_add_spacer_default_value( $new_version = '', $old_version = '' ) {
		$db_key    = 'elm_add_spacer_default_value';
		$db_limit  = trx_addons_db_update_get_limit();
		$db_offset = trx_addons_db_update_get_state( $db_key );
		if ( $db_offset === 0 ) {
			return;
		} else if ( $db_offset === false ) {
			$db_offset = 0;
		}
		if ( empty( $old_version ) ) {
			$old_version = get_option( 'trx_addons_version', '1.0' );
		}
		if ( version_compare( $old_version, '2.29.1', '<' ) || current_action() == 'trx_addons_action_importer_import_end' || $db_offset > 0 ) {
			global $wpdb;
			$rows = $wpdb->get_results( "SELECT post_id, meta_id, meta_value
											FROM {$wpdb->postmeta}
											WHERE meta_key='_elementor_data' && meta_value!=''
											ORDER BY meta_id ASC
											LIMIT {$db_offset}, {$db_limit}"
										);
			$total = is_array( $rows ) ? count( $rows ) : 0;
			if ( $total > 0 ) {
				foreach ( $rows as $row ) {
					$data = json_decode( $row->meta_value, true );
					if ( trx_addons_elm_convert_spacer_default_value( $data ) ) {
						$wpdb->query( "UPDATE {$wpdb->postmeta} SET meta_value = '" . wp_slash( wp_json_encode( $data ) ) . "' WHERE meta_id = {$row->meta_id} LIMIT 1" );
					}
				}
				if ( $total == $db_limit ) {
					$db_offset += $total;
					trx_addons_db_update_set_state( $db_key, $db_offset );
				} else {
					trx_addons_db_update_set_state( $db_key, 0 );
				}
			} else {
				trx_addons_db_update_set_state( $db_key, 0 );
			}
		}
	}
}

if ( ! function_exists( 'trx_addons_elm_convert_spacer_default_value' ) ) {
	/**
	 * Add a default value to all widgets 'Spacer' to the all elements in the Elementor data
	 * Attention! The parameter $elements passed by reference and modified inside this function!
	 * Return true if $elements is modified (converted) and needs to be saved
	 *
	 * @param array $elements  Array of elements. Passed by reference and modified inside this function!
	 * 
	 * @return boolean True if parameters was changed and needs to be saved
	 */
	function trx_addons_elm_convert_spacer_default_value( &$elements ) {
		$modified = false;
		$sc = array( 'spacer' );
		if ( is_array( $elements ) ) {
			foreach( $elements as $k => $elm ) {
				// Convert parameters
				if (   ! empty( $elm['widgetType'] )
					&&   in_array( $elm['widgetType'], $sc )
					&& ! empty( $elm['settings']['alter_height'] )
					&& ! empty( $elm['settings']['space'] )
					&&   empty( $elm['settings']['space']['size'] )
				) {
					unset( $elements[ $k ]['settings']['space'] );
					$modified = true;
				}
				// Process inner elements
				if ( ! empty( $elm['elements'] ) && is_array( $elm['elements'] ) ) {
					$modified = trx_addons_elm_convert_spacer_default_value( $elements[ $k ]['elements'] ) || $modified;
				}
			}
		}
		return $modified;
	}
}


// Fix the name of options and its values after import demo data: replace old names '...stabble...' with the new one '...stable...'
//---------------------------------------------------------------------------------------------------------------------------------
if ( ! function_exists( 'trx_addons_fix_stable_diffusion_names' ) ) {
	add_action( 'trx_addons_action_is_new_version_of_plugin', 'trx_addons_fix_stable_diffusion_names', 10, 2 );
	add_action( 'trx_addons_action_importer_import_end', 'trx_addons_fix_stable_diffusion_names' );
	/**
	 * Fix the name of options and its values after import demo data:
	 * replace old names '...stabble...' with the new one '...stable...'
	 *
	 * @hooked trx_addons_action_is_new_version_of_plugin
	 * @hooked trx_addons_action_importer_import_end
	 * 
	 * @param string $new_version New version of the plugin.
	 * @param string $old_version Old version of the plugin.
	 */
	function trx_addons_fix_stable_diffusion_names( $new_version = '', $old_version = '' ) {
		if ( empty( $old_version ) ) {
			$old_version = get_option( 'trx_addons_version', '1.0' );
		}
		if ( version_compare( $old_version, '2.30.2.2', '<' ) || current_action() == 'trx_addons_action_importer_import_end' ) {
			global $wpdb;
			// Fix the values of widget options in the Elementor data
			$rows = $wpdb->get_results( "SELECT post_id, meta_id, meta_value
											FROM {$wpdb->postmeta}
											WHERE meta_key='_elementor_data' && meta_value LIKE '%stabble%'"
										);
			if ( is_array( $rows ) && count( $rows ) > 0 ) {
				foreach ( $rows as $row ) {
					$wpdb->query( "UPDATE {$wpdb->postmeta} SET meta_value = '" . wp_slash( str_replace( 'stabble', 'stable', $row->meta_value ) ) . "' WHERE meta_id = {$row->meta_id} LIMIT 1" );
				}
			}
			// Fix the post content
			$rows = $wpdb->get_results( "SELECT ID, post_content
											FROM {$wpdb->posts}
											WHERE post_content LIKE '%stabble%'"
										);
			if ( is_array( $rows ) && count( $rows ) > 0 ) {
				foreach ( $rows as $row ) {
					$wpdb->query( "UPDATE {$wpdb->posts} SET post_content = '" . wp_slash( str_replace( 'stabble', 'stable', $row->post_content ) ) . "' WHERE ID = {$row->ID} LIMIT 1" );
				}
			}
			// Fix the values of Plugin options and AI Helper Logs (serialized arrays)
			$opt_list = array( 'trx_addons_options', 'trx_addons_ai_helper_log' );
			foreach( $opt_list as $opt_name ) {
				$opt_value = get_option( $opt_name, '' );
				if ( is_array( $opt_value ) ) {
					$opt_value = json_decode( str_replace( 'stabble', 'stable', json_encode( $opt_value ) ), true );
					if ( is_array( $opt_value ) ) {
						update_option( $opt_name, $opt_value );
					}
				}
			}
		}
	}
}


// Fix a key 'color' in the Elementor's kit settings (global colors) after import demo data
//---------------------------------------------------------------------------------------------------------------------------------
if ( ! function_exists( 'trx_addons_fix_global_colors' ) ) {
	add_action( 'trx_addons_action_is_new_version_of_plugin', 'trx_addons_fix_global_colors', 10, 2 );
	add_action( 'trx_addons_action_importer_import_end', 'trx_addons_fix_global_colors' );
	/**
	 * Fix a key 'color' in the Elementor's kit settings (global colors) after import demo data
	 *
	 * @hooked trx_addons_action_is_new_version_of_plugin
	 * @hooked trx_addons_action_importer_import_end
	 * 
	 * @param string $new_version New version of the plugin.
	 * @param string $old_version Old version of the plugin.
	 */
	function trx_addons_fix_global_colors( $new_version = '', $old_version = '' ) {
		if ( empty( $old_version ) ) {
			$old_version = get_option( 'trx_addons_version', '1.0' );
		}
		if ( version_compare( $old_version, '2.31.1.3', '<' ) || current_action() == 'trx_addons_action_importer_import_end' ) {
			$kit = get_option( 'elementor_active_kit' );
			if ( ! empty( $kit ) ) {
				$options = get_post_meta( $kit, '_elementor_page_settings', true );
				$modified = false;
				foreach ( array( 'system_colors', 'custom_colors' ) as $key ) {
					if ( ! empty( $options[ $key ] ) && is_array( $options[ $key ] ) ) {
						foreach( $options[ $key ] as $k => $v ) {
							if ( is_array( $v ) && ! isset( $v['color'] ) ) {
								$options[ $key ][ $k ]['color'] = trx_addons_fix_global_colors_get_default_color( $v );
								$modified = true;
							}
						}
					}
				}
				if ( $modified ) {
					update_post_meta( $kit, '_elementor_page_settings', $options );
				}
			}
		}
	}
}

if ( ! function_exists( 'trx_addons_fix_global_colors_get_default_color' ) ) {
	/**
	 * Return a default color value for the key 'color' in the Elementor's kit settings (global colors)
	 * for the specified color ID for system_colors
	 * 
	 * @param array $color  Color data from Elementor's kit settings
	 * 
	 * @return string     Default color value for the color ID from the color data
	 */
	function trx_addons_fix_global_colors_get_default_color( $color ) {
		$default_colors = array(
			'primary'   => '#6EC1E4',
			'secondary' => '#54595F',
			'text'      => '#7A7A7A',
			'accent'    => '#61CE70',
		);
		return ! empty( $color['_id'] ) && ! empty( $default_colors[ $color['_id'] ] ) ? $default_colors[ $color['_id'] ] : '';
	}
}
