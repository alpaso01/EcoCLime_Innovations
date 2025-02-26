<?php
/**
 * Compatibility fixes for WordPress updates, 3rd-party plugins, etc.
 *
 * @package ThemeREX Addons
 * @since v2.1.0
 */

// Disable direct call
if ( ! defined( 'ABSPATH' ) ) { exit; }



/* WordPress 5.8+: Widgets block editor in Customize don't allow moving sections with widgets
 *                 from the panel 'Widgets' to another panel
--------------------------------------------------------------------------------------------------- */
if ( ! function_exists( 'trx_addons_disable_moving_widgets_sections_in_customizer' ) ) {
	add_filter( 'after_setup_theme', 'trx_addons_disable_moving_widgets_sections_in_customizer', 1 );
	/**
	 * Disable moving widgets sections in Сustomizer (WordPress 5.8+) to prevent the bug with the widgets block editor in the panel 'Widgets'
	 * 
	 * @hooked 'after_setup_theme'
	 */
	function trx_addons_disable_moving_widgets_sections_in_customizer() {
		if ( version_compare( get_bloginfo( 'version' ), '5.8', '>=' ) ) {
			$slug = str_replace( '-', '_', get_template() );
			add_filter( "{$slug}_filter_front_page_options", 'trx_addons_disable_moving_widgets_sections_in_customizer_callback', 10000, 1 );
		}
	}
}

if ( ! function_exists( 'trx_addons_disable_moving_widgets_sections_in_customizer_callback' ) ) {
	/**
	 * Rename sections with widgets to prevent its moving
	 * 
	 * @param array $options  Theme options
	 * 
	 * @return array 		Modified theme options
	 */
	function trx_addons_disable_moving_widgets_sections_in_customizer_callback( $options ) {
		if ( isset( $options['front_page_sections']['options'] ) && is_array( $options['front_page_sections']['options'] ) ) {
			foreach ( $options['front_page_sections']['options'] as $k => $v ) {
				if ( isset( $options["sidebar-widgets-front_page_{$k}_widgets"] ) && ! isset( $options["front_page_{$k}_widgets"] ) ) {
					trx_addons_array_insert_after( $options, "sidebar-widgets-front_page_{$k}_widgets", array(
						"front_page_{$k}_widgets" => $options["sidebar-widgets-front_page_{$k}_widgets"]
					) );
					unset( $options["sidebar-widgets-front_page_{$k}_widgets"] );
				}
				if ( ! empty( $options["front_page_{$k}_widgets_info"]['desc'] ) && is_string( $options["front_page_{$k}_widgets_info"]['desc'] ) ) {
					$options["front_page_{$k}_widgets_info"]['desc'] .= '<br>&nbsp;<br><i>' . wp_kses_data( sprintf( __( 'Attention! Since WordPress 5.8+ you are not able to select widgets for this section here, in order to do that please go to Customize - Widgets - Front page section "%s"', 'trx_addons' ), $v ) . '</i>' );
				}
			}
		}
		return $options;
	}
}


/* WordPress 6.1+: If a parameter 'depth' greater then 0 - a class 'menu-item-has-children'
 *                 is not added to the submenu items
--------------------------------------------------------------------------------------------------- */
if ( ! function_exists( 'trx_addons_clear_depth_in_menu_args' ) ) {
	add_filter( str_replace( '-', '_', get_template() ) . '_filter_get_nav_menu_args', 'trx_addons_clear_depth_in_menu_args' );
	add_filter( 'trx_addons_filter_get_nav_menu_args', 'trx_addons_clear_depth_in_menu_args' );
	/**
	 * Clear 'depth' parameter in the menu args to prevent the bug with the submenu items - WordPress 6.1+
	 * If a parameter 'depth' greater then 0 - a class 'menu-item-has-children' is not added to the submenu items
	 * 
	 * @hooked 'trx_addons_filter_get_nav_menu_args'
	 * @hooked '{theme-slug}_filter_get_nav_menu_args'
	 * 
	 * @param array $args  Menu args
	 * 
	 * @return array 		Modified menu args
	 */
	function trx_addons_clear_depth_in_menu_args( $args ) {
		if ( version_compare( get_bloginfo( 'version' ), '6.1', '>=' ) ) {
			if ( ! empty( $args['depth'] ) ) {
				$args['depth'] = 0;
			}
		}
		return $args;
	}
}


/* WordPress 6.7+: Fix a title icon position in the Customizer
--------------------------------------------------------------------------------------------------- */
if ( ! function_exists( 'trx_addons_fix_title_icon_in_customizer' ) ) {
	add_action( 'customize_controls_print_styles', 'trx_addons_fix_title_icon_in_customizer', 1 );
	/**
	 * Add styles to fix the icon position inside the accordion title in the Customizer
	 * 
	 * @hooked 'customize_controls_print_styles'
	 */
	function trx_addons_fix_title_icon_in_customizer() {
		if ( version_compare( get_bloginfo( 'version' ), '6.7', '>=' ) ) {
			?><style id="trx_addons_fix_customize_title_icon" rel="stylesheet">
				#customize-theme-controls .customize-pane-parent {
					overflow-x: hidden !important;
				}
				#customize-theme-controls .accordion-section .accordion-section-title:before {
					position: absolute;
					left: 0.25em;
					top: 50%;
					transform: translateY(-50%);
					pointer-events: none;
				}
				#customize-theme-controls .accordion-section .accordion-section-title .accordion-trigger {
					padding-left: 32px;
				}
			</style><?php
		}
	}
}


/* Theme-specific fixes
--------------------------------------------------------------------------------------------------- */

if ( ! function_exists( 'trx_addons_theme_specific_post_meta_args' ) ) {
	add_filter( str_replace( '-', '_', get_template() ) . '_filter_post_meta_args', 'trx_addons_theme_specific_post_meta_args', 10, 3 );
	/**
	 * Hide some components in the post meta for the theme-specific pages (search results, archive pages, etc.)
	 * 
	 * @hooked '{theme-slug}_filter_post_meta_args'
	 * 
	 * @param array  $args       Meta args
	 * @param string $blog_style Blog style. Not used
	 * @param int    $columns    Number of columns. Not used
	 */
	function trx_addons_theme_specific_post_meta_args( $args, $blog_style = '', $columns = 1 ) {
		$hide_meta_components = apply_filters( 'trx_addons_filter_post_meta_args_hide_components', array() );
		$post_type = get_post_type();
		if ( ! empty( $args['components'] ) && ! empty( $hide_meta_components[ $post_type ] ) ) {
			$args['components'] = join( ',', trx_addons_array_delete_by_value(
												array_map( 'trim', explode( ',', $args['components'] ) ),
												$hide_meta_components[ $post_type ]
											) );
		}
		return $args;
	}
}

if ( ! function_exists( 'trx_addons_theme_specific_replace_url_for_theme_rate' ) ) {
	add_action( 'after_setup_theme', 'trx_addons_theme_specific_replace_url_for_theme_rate', 2 );
	/**
	 * Replace the URL for the theme rate
	 * 
	 * @hooked 'after_setup_theme', 2
	 */
	function trx_addons_theme_specific_replace_url_for_theme_rate() {
		$slug = strtoupper( str_replace( '-', '_', get_template() ) );
		if ( ! empty( $GLOBALS[ "{$slug}_STORAGE" ]['theme_rate_url'] ) ) {
			$GLOBALS[ "{$slug}_STORAGE" ]['theme_rate_url'] = '//themeforest.net/downloads';
		}
	}
}

// Correct theme-specific info - change the description for the custom action
if ( ! function_exists( 'trx_addons_theme_specific_change_custom_description' ) ) {
	add_filter( 'trx_addons_filter_get_theme_info', 'trx_addons_theme_specific_change_custom_description', 100 );
	function trx_addons_theme_specific_change_custom_description( $theme_info ) {
		if ( ! empty( $theme_info['theme_actions']['custom']['description'] ) ) {
			$theme_info['theme_actions']['custom']['description'] = __( 'You can order professional website customization. Experienced web studio will do it for you at a reasonable fee.', 'trx_addons' );
		}
		return $theme_info;
	}
}

// Prevent to install a deprecated plugins via Theme Panel
if ( ! function_exists( 'trx_addons_theme_specific_prevent_install_deprecated_plugins' ) ) {
	add_filter( 'trx_addons_filter_get_theme_info', 'trx_addons_theme_specific_prevent_install_deprecated_plugins', 100 );
	function trx_addons_theme_specific_prevent_install_deprecated_plugins( $theme_info ) {
		$deprecated = apply_filters( 'trx_addons_filter_deprecated_plugins_list', array(
			'elegro-payment',
		) );
		if ( ! empty( $deprecated ) ) {
			foreach ( $deprecated as $plugin ) {
				if ( ! empty( $theme_info['theme_plugins'][ $plugin ] ) ) {
					$theme_info['theme_plugins'][ $plugin ]['install'] = false;
				}
			}
		}
		return $theme_info;
	}
}

// Prevent to install a deprecated plugins via TGMPA
if ( ! function_exists( 'trx_addons_theme_specific_prevent_install_deprecated_plugins_tgmpa' ) ) {
	add_filter( str_replace( '-', '_', get_template() ) . '_filter_tgmpa_required_plugins', 'trx_addons_theme_specific_prevent_install_deprecated_plugins_tgmpa', 11 );
	function trx_addons_theme_specific_prevent_install_deprecated_plugins_tgmpa( $list = array() ) {
		if ( is_array( $list ) ) {
			$deprecated = apply_filters( 'trx_addons_filter_deprecated_plugins_list', array(
				'elegro-payment',
			) );
			if ( ! empty( $deprecated ) ) {
				foreach ( $list as $k => $v ) {
					if ( ! empty( $v['slug'] ) && in_array( $v['slug'], $deprecated ) ) {
						unset( $list[ $k ] );
					}
				}
			}
		}
		return $list;
	}
}

// if ( ! function_exists( 'trx_addons_theme_specific_change_ti_woocommerce_wishlist_source' ) ) {
// 	add_filter( str_replace( '-', '_', get_template() ) . '_filter_tgmpa_required_plugins', 'trx_addons_theme_specific_change_ti_woocommerce_wishlist_source', 100 );
// 	/**
// 	 * Modify TI WooCommerce Wishlist plugin source path in the list to allow to install a fixed version from the own source
// 	 *
// 	 * @hooked THEME_SLUG_filter_tgmpa_required_plugins
// 	 * 
// 	 * @param array $list  List of required plugins
// 	 * 
// 	 * @return array  Modified list of required plugins
// 	 */
// 	function trx_addons_theme_specific_change_ti_woocommerce_wishlist_source( $list = array() ) {
// 		if ( is_array( $list ) && count( $list ) > 0 ) {
// 			foreach ( $list as $k => $v ) {
// 				if ( is_array( $v ) && isset( $v['slug'] ) && $v['slug'] == 'ti-woocommerce-wishlist' && empty( $v['source'] ) ) {
// 					if ( empty( $list[ $k ]['version'] ) || version_compare( $list[ $k ]['version'], '2.8.2.1', '<' ) ) {
// 						$fn = str_replace( '-', '_', get_template() ) . '_get_plugin_source_path';
// 						$path = function_exists( $fn ) ? call_user_func( $fn, 'plugins/ti-woocommerce-wishlist/ti-woocommerce-wishlist.zip' ) : '';
// 						$list[ $k ]['source'] = ! empty( $path ) ? $path : 'upload://ti-woocommerce-wishlist.zip';
// 						$list[ $k ]['version'] = '2.8.2.1';
// 					}
// 					break;
// 				}
// 			}
// 		}
// 		return $list;
// 	}
// }

// if ( ! function_exists( 'trx_addons_theme_specific_disable_replace_featured_image_renderer' ) ) {
// 	add_action( 'after_setup_theme', 'trx_addons_theme_specific_disable_replace_featured_image_renderer' );
// 	/**
// 	 * Disable the theme-specific function to replace the featured image block renderer
// 	 * 
// 	 * @hooked 'after_setup_theme'
// 	 */
// 	function trx_addons_theme_specific_disable_replace_featured_image_renderer() {
// 		$slug = str_replace( '-', '_', get_template() );
// 		remove_filter( 'block_type_metadata_settings', "{$slug}_gutenberg_fse_replace_featured_image_renderer" );
// 	}
// }
