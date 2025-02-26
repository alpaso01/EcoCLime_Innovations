<?php
/* WPBakery PageBuilder support functions
------------------------------------------------------------------------------- */

// Theme init priorities:
// 9 - register other filters (for installer, etc.)
if ( ! function_exists( 'planty_vc_theme_setup9' ) ) {
	add_action( 'after_setup_theme', 'planty_vc_theme_setup9', 9 );
	function planty_vc_theme_setup9() {

		if ( planty_exists_vc() ) {
		
			add_action( 'wp_enqueue_scripts', 'planty_vc_frontend_scripts', 1100 );
			add_action( 'wp_enqueue_scripts', 'planty_vc_responsive_styles', 2000 );
			add_filter( 'planty_filter_merge_styles', 'planty_vc_merge_styles' );
			add_filter( 'planty_filter_merge_styles_responsive', 'planty_vc_merge_styles_responsive' );
			
			// Add/Remove params to the trx_addons shortcodes for VC
			add_filter( 'trx_addons_sc_map', 'planty_trx_addons_sc_map', 10, 2 );

			// Add/Remove params to the standard VC shortcodes
			add_filter( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, 'planty_vc_add_params_classes', 10, 3 );
			add_filter( 'vc_iconpicker-type-fontawesome', 'planty_vc_iconpicker_type_fontawesome' );

			// Color scheme
			$scheme  = array(
				'param_name'  => 'scheme',
				'heading'     => esc_html__( 'Color scheme', 'planty' ),
				'description' => wp_kses_data( __( 'Select color scheme to decorate this block', 'planty' ) ),
				'group'       => esc_html__( 'Colors', 'planty' ),
				'admin_label' => true,
				'value'       => array_flip( planty_get_list_schemes( true ) ),
				'type'        => 'dropdown',
			);
			$sc_list = apply_filters( 'planty_filter_add_scheme_in_vc', array( 'vc_section', 'vc_row', 'vc_row_inner', 'vc_column', 'vc_column_inner', 'vc_column_text' ) );
			foreach ( $sc_list as $sc ) {
				vc_add_param( $sc, $scheme );
			}

			// Load custom VC styles for blog archive page
			add_filter( 'planty_filter_blog_archive_start', 'planty_vc_add_inline_css' );
		}

		if ( is_admin() ) {
			add_filter( 'planty_filter_tgmpa_required_plugins', 'planty_vc_tgmpa_required_plugins' );
		}
	}
}

// Filter to add in the required plugins list
if ( ! function_exists( 'planty_vc_tgmpa_required_plugins' ) ) {
	//Handler of the add_filter('planty_filter_tgmpa_required_plugins',	'planty_vc_tgmpa_required_plugins');
	function planty_vc_tgmpa_required_plugins( $list = array() ) {
		if ( planty_storage_isset( 'required_plugins', 'js_composer' ) && planty_storage_get_array( 'required_plugins', 'js_composer', 'install' ) !== false && planty_is_theme_activated() ) {
			$path = planty_get_plugin_source_path( 'plugins/js_composer/js_composer.zip' );
			if ( ! empty( $path ) || planty_get_theme_setting( 'tgmpa_upload' ) ) {
				$list[] = array(
					'name'     => planty_storage_get_array( 'required_plugins', 'js_composer', 'title' ),
					'slug'     => 'js_composer',
					'source'   => ! empty( $path ) ? $path : 'upload://js_composer.zip',
					'version'  => '6.5.0',
					'required' => false,
				);
			}
		}
		return $list;
	}
}

// Check if plugin installed and activated
if ( ! function_exists( 'planty_exists_vc' ) ) {
	function planty_exists_vc() {
		return class_exists( 'Vc_Manager' );
	}
}

// Check if plugin in frontend editor mode
if ( ! function_exists( 'planty_vc_is_frontend' ) ) {
	function planty_vc_is_frontend() {
		return ( isset( $_GET['vc_editable'] ) && 'true' == $_GET['vc_editable'] )
			|| ( isset( $_GET['vc_action'] ) && 'vc_inline' == $_GET['vc_action'] );
	}
}

// Enqueue styles for frontend
if ( ! function_exists( 'planty_vc_frontend_scripts' ) ) {
	//Handler of the add_action( 'wp_enqueue_scripts', 'planty_vc_frontend_scripts', 1100 );
	function planty_vc_frontend_scripts() {
		if ( planty_is_on( planty_get_theme_option( 'debug_mode' ) ) ) {
			$planty_url = planty_get_file_url( 'plugins/js_composer/js_composer.css' );
			if ( '' != $planty_url ) {
				wp_enqueue_style( 'planty-js-composer', $planty_url, array(), null );
			}
		}
	}
}

// Enqueue responsive styles for frontend
if ( ! function_exists( 'planty_vc_responsive_styles' ) ) {
	//Handler of the add_action( 'wp_enqueue_scripts', 'planty_vc_responsive_styles', 2000 );
	function planty_vc_responsive_styles() {
		if ( planty_is_on( planty_get_theme_option( 'debug_mode' ) ) ) {
			$planty_url = planty_get_file_url( 'plugins/js_composer/js_composer-responsive.css' );
			if ( '' != $planty_url ) {
				wp_enqueue_style( 'planty-js-composer-responsive', $planty_url, array(), null, planty_media_for_load_css_responsive( 'vc' ) );
			}
		}
	}
}

// Merge custom styles
if ( ! function_exists( 'planty_vc_merge_styles' ) ) {
	//Handler of the add_filter('planty_filter_merge_styles', 'planty_vc_merge_styles');
	function planty_vc_merge_styles( $list ) {
		$list[ 'plugins/js_composer/js_composer.css' ] = true;
		return $list;
	}
}

// Merge responsive styles
if ( ! function_exists( 'planty_vc_merge_styles_responsive' ) ) {
	//Handler of the add_filter('planty_filter_merge_styles_responsive', 'planty_vc_merge_styles_responsive');
	function planty_vc_merge_styles_responsive( $list ) {
		$list[ 'plugins/js_composer/js_composer-responsive.css' ] = true;
		return $list;
	}
}

// Add VC custom styles to the inline CSS
if ( ! function_exists( 'planty_vc_add_inline_css' ) ) {
	//Handler of the add_filter('planty_filter_blog_archive_start', 'planty_vc_add_inline_css');
	function planty_vc_add_inline_css( $html ) {
		$vc_custom_css = get_post_meta( get_the_ID(), '_wpb_shortcodes_custom_css', true );
		if ( ! empty( $vc_custom_css ) ) {
			planty_add_inline_css( strip_tags( $vc_custom_css ) );
		}
		return $html;
	}
}



// Shortcodes support
//------------------------------------------------------------------------

// Add params to the standard VC shortcodes
if ( ! function_exists( 'planty_vc_add_params_classes' ) ) {
	//Handler of the add_filter( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, 'planty_vc_add_params_classes', 10, 3 );
	function planty_vc_add_params_classes( $classes, $sc, $atts ) {
		// Add color scheme
		if ( in_array( $sc, apply_filters( 'planty_filter_add_scheme_in_vc', array( 'vc_section', 'vc_row', 'vc_row_inner', 'vc_column', 'vc_column_inner', 'vc_column_text' ) ) ) ) {
			if ( ! empty( $atts['scheme'] ) && ! planty_is_inherit( $atts['scheme'] ) ) {
				$classes .= ( $classes ? ' ' : '' ) . 'scheme_' . $atts['scheme'];
			}
		}
		return $classes;
	}
}

// Add theme icons to the VC iconpicker list
if ( ! function_exists( 'planty_vc_iconpicker_type_fontawesome' ) ) {
	//Handler of the add_filter( 'vc_iconpicker-type-fontawesome',	'planty_vc_iconpicker_type_fontawesome' );
	function planty_vc_iconpicker_type_fontawesome( $icons ) {
		$list = planty_get_list_icons_classes();
		if ( ! is_array( $list ) || count( $list ) == 0 ) {
			return $icons;
		}
		$rez = array();
		foreach ( $list as $icon ) {
			$rez[] = array( $icon => str_replace( 'icon-', '', $icon ) );
		}
		return array_merge( $icons, array( esc_html__( 'Theme Icons', 'planty' ) => $rez ) );
	}
}

// Add new params to the shortcodes VC map
if ( ! function_exists( 'planty_trx_addons_sc_map' ) ) {
	//Handler of the add_filter( 'trx_addons_sc_map', 'planty_trx_addons_sc_map', 10, 2 );
	function planty_trx_addons_sc_map( $params, $sc ) {

		// Param 'scheme'
		if ( in_array(
			$sc, array(
				'trx_sc_action',
				'trx_sc_blogger',
				'trx_sc_cars',
				'trx_sc_courses',
				'trx_sc_content',
				'trx_sc_dishes',
				'trx_sc_events',
				'trx_sc_form',
				'trx_sc_googlemap',
				'trx_sc_osmap',
				'trx_sc_portfolio',
				'trx_sc_price',
				'trx_sc_promo',
				'trx_sc_properties',
				'trx_sc_services',
				'trx_sc_skills',
				'trx_sc_socials',
				'trx_sc_table',
				'trx_sc_team',
				'trx_sc_testimonials',
				'trx_sc_title',
				'trx_widget_audio',
				'trx_widget_twitter',
				'trx_sc_layouts',
				'trx_sc_layouts_container',
			)
		) ) {
			if ( empty( $params['params'] ) || ! is_array( $params['params'] ) ) {
				$params['params'] = array();
			}
			$params['params'][] = array(
				'param_name'  => 'scheme',
				'heading'     => esc_html__( 'Color scheme', 'planty' ),
				'description' => wp_kses_data( __( 'Select color scheme to decorate this block', 'planty' ) ),
				'group'       => esc_html__( 'Colors', 'planty' ),
				'admin_label' => true,
				'value'       => array_flip( planty_get_list_schemes( true ) ),
				'type'        => 'dropdown',
			);
		}
		// Param 'color_style'
		$param = array(
			'param_name'       => 'color_style',
			'heading'          => esc_html__( 'Color style', 'planty' ),
			'description'      => wp_kses_data( __( 'Select color style to decorate this block', 'planty' ) ),
			'edit_field_class' => 'vc_col-sm-4',
			'admin_label'      => true,
			'value'            => array_flip( planty_get_list_sc_color_styles() ),
			'type'             => 'dropdown',
		);
		if ( in_array( $sc, array( 'trx_sc_button' ) ) ) {
			if ( empty( $params['params'] ) || ! is_array( $params['params'] ) ) {
				$params['params'] = array();
			}
			foreach ( $params['params'] as $k => $p ) {
				if ( 'buttons' == $p['param_name'] ) {
					if ( ! empty( $p['params'] ) ) {
						$new_params = array();
						foreach ( $p['params'] as $v ) {
							$new_params[] = $v;
							if ( 'size' == $v['param_name'] ) {
								$new_params[] = $param;
							}
						}
						$params['params'][ $k ]['params'] = $new_params;
					}
				}
			}
		} elseif ( in_array(
			$sc, array(
				'trx_sc_action',
				'trx_sc_blogger',
				'trx_sc_cars',
				'trx_sc_courses',
				'trx_sc_content',
				'trx_sc_dishes',
				'trx_sc_events',
				'trx_sc_form',
				'trx_sc_icons',
				'trx_sc_googlemap',
				'trx_sc_osmap',
				'trx_sc_portfolio',
				'trx_sc_price',
				'trx_sc_promo',
				'trx_sc_properties',
				'trx_sc_services',
				'trx_sc_skills',
				'trx_sc_socials',
				'trx_sc_table',
				'trx_sc_team',
				'trx_sc_testimonials',
				'trx_sc_title',
				'trx_widget_audio',
				'trx_widget_twitter',
			)
		) ) {
			if ( empty( $params['params'] ) || ! is_array( $params['params'] ) ) {
				$params['params'] = array();
			}
			$new_params = array();
			foreach ( $params['params'] as $v ) {
				if ( in_array( $v['param_name'], array( 'title_style', 'title_tag', 'title_align' ) ) ) {
					$v['edit_field_class'] = 'vc_col-sm-6';
				}
				$new_params[] = $v;
				if ( 'title_align' == $v['param_name'] ) {
					if ( ! empty( $v['group'] ) ) {
						$param['group'] = $v['group'];
					}
					$param['edit_field_class'] = 'vc_col-sm-6';
					$new_params[]              = $param;
				}
			}
			$params['params'] = $new_params;
		}
		return $params;
	}
}


// Add plugin-specific colors and fonts to the custom CSS
if ( planty_exists_vc() ) {
	$planty_fdir = planty_get_file_dir( 'plugins/js_composer/js_composer-style.php' );
	if ( ! empty( $planty_fdir ) ) {
		require_once $planty_fdir;
	}
}

