<?php
/* Gutenberg support functions
------------------------------------------------------------------------------- */

// Theme init priorities:
// 9 - register other filters (for installer, etc.)
if ( ! function_exists( 'planty_gutenberg_theme_setup9' ) ) {
	add_action( 'after_setup_theme', 'planty_gutenberg_theme_setup9', 9 );
	function planty_gutenberg_theme_setup9() {

		// Add wide and full blocks support
		add_theme_support( 'align-wide' );

		// Add editor styles to backend
		add_theme_support( 'editor-styles' );
		if ( is_admin() && ( ! is_rtl() || ! is_customize_preview() ) ) {
			if ( planty_exists_gutenberg() && planty_gutenberg_is_preview() ) {
				if ( ! planty_get_theme_setting( 'gutenberg_add_context' ) ) {
					if ( ! planty_exists_trx_addons() ) {
						// Attention! This place need to use 'trx_addons_filter' instead 'planty_filter'
						add_editor_style( apply_filters( 'trx_addons_filter_add_editor_style', array(), 'gutenberg' ) );
					}
				}
			} else {
				add_editor_style( apply_filters( 'planty_filter_add_editor_style', array(
					planty_get_file_url( 'css/font-icons/css/fontello.css' ),
					planty_get_file_url( 'css/editor-style.css' )
					), 'editor' )
				);
			}
		}

		if ( planty_exists_gutenberg() ) {
			add_action( 'wp_enqueue_scripts', 'planty_gutenberg_frontend_scripts', 1100 );
			add_action( 'wp_enqueue_scripts', 'planty_gutenberg_responsive_styles', 2000 );
			add_filter( 'planty_filter_merge_styles', 'planty_gutenberg_merge_styles' );
			add_filter( 'planty_filter_merge_styles_responsive', 'planty_gutenberg_merge_styles_responsive' );
		}
		add_action( 'enqueue_block_editor_assets', 'planty_gutenberg_editor_scripts' );
		add_filter( 'planty_filter_localize_script_admin',	'planty_gutenberg_localize_script');
		add_action( 'after_setup_theme', 'planty_gutenberg_add_editor_colors' );
		if ( is_admin() ) {
			add_filter( 'planty_filter_tgmpa_required_plugins', 'planty_gutenberg_tgmpa_required_plugins' );
			add_filter( 'planty_filter_theme_plugins', 'planty_gutenberg_theme_plugins' );
		}
	}
}

// Add theme's icons styles to the Gutenberg editor
if ( ! function_exists( 'planty_gutenberg_add_editor_style_icons' ) ) {
	add_filter( 'trx_addons_filter_add_editor_style', 'planty_gutenberg_add_editor_style_icons', 10 );
	function planty_gutenberg_add_editor_style_icons( $styles ) {
		$planty_url = planty_get_file_url( 'css/font-icons/css/fontello.css' );
		if ( '' != $planty_url ) {
			$styles[] = $planty_url;
		}
		return $styles;
	}
}

// Add required styles to the Gutenberg editor
if ( ! function_exists( 'planty_gutenberg_add_editor_style' ) ) {
	add_filter( 'trx_addons_filter_add_editor_style', 'planty_gutenberg_add_editor_style', 1100 );
	function planty_gutenberg_add_editor_style( $styles ) {
		$planty_url = planty_get_file_url( 'plugins/gutenberg/gutenberg-preview.css' );
		if ( '' != $planty_url ) {
			$styles[] = $planty_url;
		}
		return $styles;
	}
}

// Add required styles to the Gutenberg editor
if ( ! function_exists( 'planty_gutenberg_add_editor_style_responsive' ) ) {
	add_filter( 'trx_addons_filter_add_editor_style', 'planty_gutenberg_add_editor_style_responsive', 2000 );
	function planty_gutenberg_add_editor_style_responsive( $styles ) {
		$planty_url = planty_get_file_url( 'plugins/gutenberg/gutenberg-preview-responsive.css' );
		if ( '' != $planty_url ) {
			$styles[] = $planty_url;
		}
		return $styles;
	}
}

// Add all skin-specific font-faces to the editor styles
if ( ! function_exists( 'planty_gutenberg_add_editor_style_font_urls' ) ) {
	add_filter( 'trx_addons_filter_add_editor_style', 'planty_gutenberg_add_editor_style_font_urls', 9990 );
	function planty_gutenberg_add_editor_style_font_urls( $styles ) {
		return array_merge( $styles, planty_theme_fonts_for_editor( true ) );
	}
}

// Remove main-theme and child-theme urls from the editor style paths
if ( ! function_exists( 'planty_gutenberg_add_editor_style_remove_theme_url' ) ) {
	add_filter( 'trx_addons_filter_add_editor_style', 'planty_gutenberg_add_editor_style_remove_theme_url', 9999 );
	function planty_gutenberg_add_editor_style_remove_theme_url( $styles ) {
		if ( is_array( $styles ) ) {
			$template_uri   = trailingslashit( get_template_directory_uri() );
			$stylesheet_uri = trailingslashit( get_stylesheet_directory_uri() );
			$plugins_uri    = trailingslashit( defined( 'WP_PLUGIN_URL' ) ? WP_PLUGIN_URL : plugins_url() );
			$theme_replace  = '';
			$plugin_replace = '../'            // up to the folder 'themes'
								. '../'        // up to the folder 'wp-content'
								. 'plugins/';  // open the folder 'plugins'
			foreach( $styles as $k => $v ) {
				$styles[ $k ] = str_replace(
									array(
										$template_uri,
										strpos( $template_uri, 'http:' ) === 0 ? str_replace( 'http:', 'https:', $template_uri ) : $template_uri,
										$stylesheet_uri,
										strpos( $stylesheet_uri, 'http:' ) === 0 ? str_replace( 'http:', 'https:', $stylesheet_uri ) : $stylesheet_uri,
										$plugins_uri,
										strpos( $plugins_uri, 'http:' ) === 0 ? str_replace( 'http:', 'https:', $plugins_uri ) : $plugins_uri,
									),
									array(
										$theme_replace,
										$theme_replace,
										$theme_replace,
										$theme_replace,
										$plugin_replace,
										$plugin_replace,
									),
									$v
								);
			}
		}
		return $styles;
	}
}

// Filter to add in the required plugins list
if ( ! function_exists( 'planty_gutenberg_tgmpa_required_plugins' ) ) {
	//Handler of the add_filter('planty_filter_tgmpa_required_plugins',	'planty_gutenberg_tgmpa_required_plugins');
	function planty_gutenberg_tgmpa_required_plugins( $list = array() ) {
		if ( planty_storage_isset( 'required_plugins', 'gutenberg' ) ) {
			if ( planty_storage_get_array( 'required_plugins', 'gutenberg', 'install' ) !== false && version_compare( get_bloginfo( 'version' ), '5.0', '<' ) ) {
				$list[] = array(
					'name'     => planty_storage_get_array( 'required_plugins', 'gutenberg', 'title' ),
					'slug'     => 'gutenberg',
					'required' => false,
				);
			}
		}
		return $list;
	}
}

// Filter theme-supported plugins list
if ( ! function_exists( 'planty_gutenberg_theme_plugins' ) ) {
	//Handler of the add_filter( 'planty_filter_theme_plugins', 'planty_gutenberg_theme_plugins' );
	function planty_gutenberg_theme_plugins( $list = array() ) {
		$list = planty_add_group_and_logo_to_slave( $list, 'gutenberg', 'coblocks' );
		$list = planty_add_group_and_logo_to_slave( $list, 'gutenberg', 'kadence-blocks' );
		return $list;
	}
}


// Check if Gutenberg is installed and activated
if ( ! function_exists( 'planty_exists_gutenberg' ) ) {
	function planty_exists_gutenberg() {
		return function_exists( 'register_block_type' );
	}
}

// Return true if Gutenberg exists and current mode is preview
if ( ! function_exists( 'planty_gutenberg_is_preview' ) ) {
	function planty_gutenberg_is_preview() {
		return planty_exists_gutenberg() 
				&& (
					planty_gutenberg_is_block_render_action()
					||
					planty_is_post_edit()
					||
					planty_gutenberg_is_widgets_block_editor()
					||
					planty_gutenberg_is_site_editor()
					);
	}
}

// Return true if current mode is "Full Site Editor"
if ( ! function_exists( 'planty_gutenberg_is_site_editor' ) ) {
	function planty_gutenberg_is_site_editor() {
		return is_admin()
				&& planty_exists_gutenberg() 
				&& version_compare( get_bloginfo( 'version' ), '5.9', '>=' )
				&& planty_check_url( 'site-editor.php' )
				&& planty_gutenberg_is_fse_theme();
	}
}

// Return true if current mode is "Widgets Block Editor" (a new widgets panel with Gutenberg support)
if ( ! function_exists( 'planty_gutenberg_is_widgets_block_editor' ) ) {
	function planty_gutenberg_is_widgets_block_editor() {
		return is_admin()
				&& planty_exists_gutenberg() 
				&& version_compare( get_bloginfo( 'version' ), '5.8', '>=' )
				&& planty_check_url( 'widgets.php' )
				&& function_exists( 'wp_use_widgets_block_editor' )
				&& wp_use_widgets_block_editor();
	}
}

// Return true if current mode is "Block render"
if ( ! function_exists( 'planty_gutenberg_is_block_render_action' ) ) {
	function planty_gutenberg_is_block_render_action() {
		return planty_exists_gutenberg() 
				&& planty_check_url( 'block-renderer' ) && ! empty( $_GET['context'] ) && 'edit' == $_GET['context'];
	}
}

// Return true if content built with "Gutenberg"
if ( ! function_exists( 'planty_gutenberg_is_content_built' ) ) {
	function planty_gutenberg_is_content_built($content) {
		return planty_exists_gutenberg() 
				&& has_blocks( $content );	// This condition is equval to: strpos($content, '<!-- wp:') !== false;
	}
}

// Enqueue styles for frontend
if ( ! function_exists( 'planty_gutenberg_frontend_scripts' ) ) {
	//Handler of the add_action( 'wp_enqueue_scripts', 'planty_gutenberg_frontend_scripts', 1100 );
	function planty_gutenberg_frontend_scripts() {
		if ( planty_is_on( planty_get_theme_option( 'debug_mode' ) ) ) {
			// Theme-specific styles
			$planty_url = planty_get_file_url( 'plugins/gutenberg/gutenberg-general.css' );
			if ( '' != $planty_url ) {
				wp_enqueue_style( 'planty-gutenberg-general', $planty_url, array(), null );
			}
			// Skin-specific styles
			$planty_url = planty_get_file_url( 'plugins/gutenberg/gutenberg.css' );
			if ( '' != $planty_url ) {
				wp_enqueue_style( 'planty-gutenberg', $planty_url, array(), null );
			}
		}
	}
}

// Enqueue responsive styles for frontend
if ( ! function_exists( 'planty_gutenberg_responsive_styles' ) ) {
	//Handler of the add_action( 'wp_enqueue_scripts', 'planty_gutenberg_responsive_styles', 2000 );
	function planty_gutenberg_responsive_styles() {
		if ( planty_is_on( planty_get_theme_option( 'debug_mode' ) ) ) {
			// Theme-specific styles
			$planty_url = planty_get_file_url( 'plugins/gutenberg/gutenberg-general-responsive.css' );
			if ( '' != $planty_url ) {
				wp_enqueue_style( 'planty-gutenberg-general-responsive', $planty_url, array(), null, planty_media_for_load_css_responsive( 'gutenberg-general' ) );
			}
			// Skin-specific styles
			$planty_url = planty_get_file_url( 'plugins/gutenberg/gutenberg-responsive.css' );
			if ( '' != $planty_url ) {
				wp_enqueue_style( 'planty-gutenberg-responsive', $planty_url, array(), null, planty_media_for_load_css_responsive( 'gutenberg' ) );
			}
		}
	}
}

// Merge custom styles
if ( ! function_exists( 'planty_gutenberg_merge_styles' ) ) {
	//Handler of the add_filter('planty_filter_merge_styles', 'planty_gutenberg_merge_styles');
	function planty_gutenberg_merge_styles( $list ) {
		$list[ 'plugins/gutenberg/gutenberg-general.css' ] = true;
		$list[ 'plugins/gutenberg/gutenberg.css' ] = true;
		return $list;
	}
}

// Merge responsive styles
if ( ! function_exists( 'planty_gutenberg_merge_styles_responsive' ) ) {
	//Handler of the add_filter('planty_filter_merge_styles_responsive', 'planty_gutenberg_merge_styles_responsive');
	function planty_gutenberg_merge_styles_responsive( $list ) {
		$list[ 'plugins/gutenberg/gutenberg-general-responsive.css' ] = true;
		$list[ 'plugins/gutenberg/gutenberg-responsive.css' ] = true;
		return $list;
	}
}


// Load required styles and scripts for Gutenberg Editor mode
if ( ! function_exists( 'planty_gutenberg_editor_scripts' ) ) {
	//Handler of the add_action( 'enqueue_block_editor_assets', 'planty_gutenberg_editor_scripts');
	function planty_gutenberg_editor_scripts() {
		planty_admin_scripts(true);
		planty_admin_localize_scripts();
		// Editor styles
		wp_enqueue_style( 'planty-gutenberg-editor', planty_get_file_url( 'plugins/gutenberg/gutenberg-editor.css' ), array(), null );
		// Block styles
		if ( planty_get_theme_setting( 'gutenberg_add_context' ) ) {
			wp_enqueue_style( 'planty-gutenberg-preview', planty_get_file_url( 'plugins/gutenberg/gutenberg-preview.css' ), array(), null );
			wp_enqueue_style( 'planty-gutenberg-preview-responsive', planty_get_file_url( 'plugins/gutenberg/gutenberg-preview-responsive.css' ), array(), null );
		}
		// Load merged scripts ?????
		wp_enqueue_script( 'planty-main', planty_get_file_url( 'js/__scripts-full.js' ), apply_filters( 'planty_filter_script_deps', array( 'jquery' ) ), null, true );
		// Editor scripts
		wp_enqueue_script( 'planty-gutenberg-preview', planty_get_file_url( 'plugins/gutenberg/gutenberg-preview.js' ), array( 'jquery' ), null, true );
	}
}

// Add plugin's specific variables to the scripts
if ( ! function_exists( 'planty_gutenberg_localize_script' ) ) {
	//Handler of the add_filter( 'planty_filter_localize_script_admin',	'planty_gutenberg_localize_script');
	function planty_gutenberg_localize_script( $arr ) {
		// Not overridden options
		$arr['color_scheme']     = planty_get_theme_option( 'color_scheme' );
		// Overridden options
		$arr['override_classes'] = apply_filters( 'planty_filter_override_options_list', array(
													'body_style'       => 'body_style_%s',
													'sidebar_position' => 'sidebar_position_%s',
													'expand_content'   => '%s_content'
									) );
		$post_id   = planty_get_value_gpc( 'post' );
		$post_type = '';
		$post_slug = '';
		if ( planty_gutenberg_is_preview() )  {
			if ( ! empty( $post_id ) ) {		// Edit post
				$post_type = planty_get_edited_post_type();
				$meta = get_post_meta( $post_id, 'planty_options', true );
			} else {							// New post
				$post_type = planty_get_value_gpc( 'post_type' );
				if ( empty( $post_type ) ) {
					$post_type = 'post';
				}
			}
			if ( ! empty( $post_type ) ) {
				$post_slug = str_replace( 'cpt_', '', $post_type );
			}
		}
		foreach( $arr['override_classes'] as $opt => $class_mask ) {
			$arr[ $opt ] = 'inherit';
			if ( ! empty( $post_type ) ) {
				// Get an overridden value from the post meta
				if ( 'page' != $post_type && ! empty( $meta["{$opt}_single"] ) ) {
					$arr[ $opt ] = $meta["{$opt}_single"];
				} elseif ( 'page' == $post_type && ! empty( $meta[ $opt ] ) ) {
					$arr[ $opt ] = $meta[ $opt ];
				}
				// Get an overridden value from the theme options
				if ( 'inherit' == $arr[ $opt ] ) {
					if ( 'post' == $post_type ) {
						if ( planty_check_theme_option( "{$opt}_single" ) ) {
							$arr[ $opt ] = planty_get_theme_option( "{$opt}_single" );
						}
						if ( 'inherit' == $arr[ $opt ] && planty_check_theme_option( "{$opt}_blog" ) ) {
							$arr[ $opt ] = planty_get_theme_option( "{$opt}_blog" );
						}
					} else if ( 'page' != $post_type && planty_check_theme_option( "{$opt}_single_" . sanitize_title( $post_slug ) ) ) {
						$arr[ $opt ] = planty_get_theme_option( "{$opt}_single_" . sanitize_title( $post_slug ) );
						if ( 'inherit' == $arr[ $opt ] && planty_check_theme_option( "{$opt}_" . sanitize_title( $post_slug ) ) ) {
							$arr[ $opt ] = planty_get_theme_option( "{$opt}_" . sanitize_title( $post_slug ) );
						}
					}
				}
			}
			if ( 'inherit' == $arr[ $opt ] ) {
				$arr[ $opt ] = planty_get_theme_option( $opt );
			}
		}
		return $arr;
	}
}

// Save CSS with custom colors and fonts to the gutenberg-preview.css
if ( ! function_exists( 'planty_gutenberg_save_css' ) ) {
	add_action( 'planty_action_save_options', 'planty_gutenberg_save_css', 30 );
	add_action( 'trx_addons_action_save_options', 'planty_gutenberg_save_css', 30 );
	function planty_gutenberg_save_css() {

		$msg = '/* ' . esc_html__( "ATTENTION! This file was generated automatically! Don't change it!!!", 'planty' )
				. "\n----------------------------------------------------------------------- */\n";

		$add_context = array(
							'context'      => '.edit-post-visual-editor ',
							'context_self' => array( 'html', 'body', '.edit-post-visual-editor' )
							);

		// Get main styles
		//----------------------------------------------
		$css = '';
		// Add styles from the theme style.css file is not recommended, because this file contains reset styles and it's can broke the editor styles
		if ( apply_filters( 'planty_filter_add_style_css_to_gutenberg_preview', false ) ) {
			$css = planty_fgc( planty_get_file_dir( 'style.css' ) );
		}
		// Allow to add a skin-specific styles
		$css = apply_filters( 'planty_filter_gutenberg_get_styles', $css );

		// Append single post styles
		if ( apply_filters( 'planty_filters_separate_single_styles', false ) ) {
			$css .= planty_fgc( planty_get_file_dir( 'css/__single.css' ) );
		}
		// Append supported plugins styles
		$css .= planty_fgc( planty_get_file_dir( 'css/__plugins-full.css' ) );
		// Append theme-vars styles
		$css .= planty_customizer_get_css();
		// Add context class to each selector
		if ( planty_get_theme_setting( 'gutenberg_add_context' ) && function_exists( 'trx_addons_css_add_context' ) ) {
			$css = trx_addons_css_add_context( $css, $add_context );
		} else {
			$css = apply_filters( 'planty_filter_prepare_css', $css );
		}

		// Get responsive styles
		//-----------------------------------------------
		$css_responsive = apply_filters( 'planty_filter_gutenberg_get_styles_responsive',
								planty_fgc( planty_get_file_dir( 'css/__responsive-full.css' ) )
								. ( apply_filters( 'planty_filters_separate_single_styles', false )
									? planty_fgc( planty_get_file_dir( 'css/__single-responsive.css' ) )
									: ''
									)
								);
		// Add context class to each selector
		if ( planty_get_theme_setting( 'gutenberg_add_context' ) && function_exists( 'trx_addons_css_add_context' ) ) {
			$css_responsive = trx_addons_css_add_context( $css_responsive, $add_context );
		} else {
			$css_responsive = apply_filters( 'planty_filter_prepare_css', $css_responsive );
		}

		// Save styles to separate files
		//-----------------------------------------------

		// Save responsive styles
		$preview = planty_get_file_dir( 'plugins/gutenberg/gutenberg-preview-responsive.css' );
		if ( $preview ) {
			planty_fpc( $preview, $msg . $css_responsive );
			$css_responsive = '';
		}
		// Save main styles (and append responsive if its not saved to the separate file)
		planty_fpc( planty_get_file_dir( 'plugins/gutenberg/gutenberg-preview.css' ), $msg . $css . $css_responsive );
	}
}


// Add theme-specific colors to the Gutenberg color picker
if ( ! function_exists( 'planty_gutenberg_add_editor_colors' ) ) {
	//Handler of the add_action( 'after_setup_theme', 'planty_gutenberg_add_editor_colors' );
	function planty_gutenberg_add_editor_colors() {
		$scheme = planty_get_scheme_colors();
		$groups = planty_storage_get( 'scheme_color_groups' );
		$names  = planty_storage_get( 'scheme_color_names' );
		$colors = array();
		foreach( $groups as $g => $group ) {
			foreach( $names as $n => $name ) {
				$c = 'main' == $g ? ( 'text' == $n ? 'text_color' : $n ) : $g . '_' . str_replace( 'text_', '', $n );
				if ( isset( $scheme[ $c ] ) ) {
					$colors[] = array(
						'slug'  => preg_replace( '/([a-z])([0-9])+/', '$1-$2', str_replace( '_', '-', $c ) ),
						'name'  => ( 'main' == $g ? '' : $group['title'] . ' ' ) . $name['title'],
						'color' => $scheme[ $c ]
					);
				}
			}
			// Add only one group of colors
			// Delete next condition (or add false && to them) to add all groups
			if ( 'main' == $g ) {
				break;
			}
		}
		add_theme_support( 'editor-color-palette', $colors );
	}
}

// Add plugin-specific colors and fonts to the custom CSS
if ( planty_exists_gutenberg() ) {
	$planty_fdir = planty_get_file_dir( 'plugins/gutenberg/gutenberg-style.php' );
	if ( ! empty( $planty_fdir ) ) {
		require_once $planty_fdir;
	}
	$planty_fdir = planty_get_file_dir( 'plugins/gutenberg/gutenberg-fse.php' );
	if ( ! empty( $planty_fdir ) ) {
		require_once $planty_fdir;
	}
}
