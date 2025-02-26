<?php
/**
 * Gutenberg Full-Site Editor (FSE) template manipulations.
 */

//------------------------------------------------------
//--  Replace a Front Page content
//------------------------------------------------------

if ( ! function_exists( 'planty_gutenberg_fse_replace_frontpage_content' ) ) {
	add_filter( 'frontpage_template', 'planty_gutenberg_fse_replace_frontpage_content', 9999, 3 );
	/**
	 * Substitute a content of the global variable $_wp_current_template_content
	 * with a content of the Front Page sections (if Front Page Builder is enabled in the Theme Options)
	 * or with the content of the page, specified in Settings - Reading - A static page.
	 * 
	 * Hooks: add_filter( 'frontpage_template', 'planty_gutenberg_fse_replace_frontpage_content', 9999, 3 );
	 *
	 * @param string   $template  Path to the template. See locate_template().
	 * @param string   $type      Sanitized filename without extension.
	 * @param string[] $templates A list of template candidates, in descending order of priority.
	 */
	function planty_gutenberg_fse_replace_frontpage_content( $template, $type = '', $templates = array() ) {
		if ( substr( $template, -19 ) == 'template-canvas.php' && get_option( 'show_on_front' ) == 'page' ) {
			if ( have_posts() ) {
				the_post();
			}
			global $_wp_current_template_content;
			ob_start();
			if ( planty_is_on( planty_get_theme_option( 'front_page_enabled', false ) ) ) {
				// Sections from Front Page Builder (if enabled)
				$planty_sections = planty_array_get_keys_by_value( planty_get_theme_option( 'front_page_sections' ) );
				if ( is_array( $planty_sections ) ) {
					foreach ( $planty_sections as $planty_section ) {
						get_template_part( apply_filters( 'planty_filter_get_template_part', 'front-page/section', $planty_section ), $planty_section );
					}
				}
			} else {
				// A current page content (if Settings - Reading - A static page is enabled)
				the_content();
			}
			$output = ob_get_contents();
			ob_end_clean();
			if ( ! empty( $output ) ) {
				$_wp_current_template_content = preg_replace(
													'#<!-- wp:query[\s\S]*<!-- /wp:query -->#',
													str_replace( '$', '\$', $output ),	// Escape a dollar sign in the replacement string to avoid interpretation as a reference to a capture group
													$_wp_current_template_content
												);
			}
		}
		return $template;
	}
}

//------------------------------------------------------
//--  Replace a Search page content if no posts found
//------------------------------------------------------

if ( ! function_exists( 'planty_gutenberg_fse_replace_no_posts_content' ) ) {
	add_filter( 'search_template',  'planty_gutenberg_fse_replace_no_posts_content', 9999, 3 );
	add_filter( 'index_template',   'planty_gutenberg_fse_replace_no_posts_content', 9999, 3 );
	add_filter( 'archive_template', 'planty_gutenberg_fse_replace_no_posts_content', 9999, 3 );
	/**
	 * Substitute a content of the global variable $_wp_current_template_content
	 * with a content of the template 'parts/none-archive.html' or 'templates/none-archive.html'.
	 * 
	 * Hooks: add_filter( 'search_template',  'planty_gutenberg_fse_replace_no_posts_content', 9999, 3 );
	 * 
	 *        add_filter( 'index_template',   'planty_gutenberg_fse_replace_no_posts_content', 9999, 3 );
	 * 
	 *        add_filter( 'archive_template', 'planty_gutenberg_fse_replace_no_posts_content', 9999, 3 );
	 *
	 * @param string   $template  Path to the template. See locate_template().
	 * @param string   $type      Sanitized filename without extension.
	 * @param string[] $templates A list of template candidates, in descending order of priority.
	 */
	function planty_gutenberg_fse_replace_no_posts_content( $template, $type = '', $templates = array() ) {
		if ( substr( $template, -19 ) == 'template-canvas.php' ) {
			if ( ! have_posts() ) {
				$part_name = is_search() ? 'none-search' : 'none-archive';
				// Check if exists a part of template with name 'none-posts.html'
				$part_exists = false;
				$data = planty_gutenberg_fse_theme_json_data();
				if ( ! empty( $data['templateParts'] ) && is_array( $data['templateParts'] ) ) {
					foreach ( $data['templateParts'] as $part ) {
						if ( ! empty( $part['name'] ) && $part['name'] == $part_name ) {
							$part_exists = true;
							break;
						}
					}
				}
				// If a file with the specified template exists
				$no_posts_file = planty_get_file_dir( ( $part_exists ? 'parts/' : 'templates/' ) . "{$part_name}.html" );
				if ( ! empty( $no_posts_file ) ) {
					// Check if exists a saved template with a same name
					$posts = get_posts( array(
						'name'           => $part_name,
						'posts_per_page' => 1,
						'post_type'      => defined( 'PLANTY_FSE_TEMPLATE_PART_PT' ) ? PLANTY_FSE_TEMPLATE_PART_PT : 'wp_template_part',
					) );
					// Get a template content from the database or from the file (if a saved version is not found)
					$content = ! empty( $posts[0]->post_content ) ? $posts[0]->post_content : planty_fgc( $no_posts_file );
					// Replace a template part (instead a wp:query part) or a whole template content
					if ( ! empty( $content ) ) {
						global $_wp_current_template_content;
						$_wp_current_template_content = $part_exists
															? preg_replace(
																	'#<!-- wp:query[\s\S]*<!-- /wp:query -->#',
																	str_replace( '$', '\$', $content ),	// Escape a dollar sign in the replacement string to avoid interpretation as a reference to a capture group
																	$_wp_current_template_content
																)
															: $content;
					}
				}
			}
		}
		return $template;
	}
}



//--------------------------------------------------------------
//--  Show an FSE template (header/footer/sidebar) inside a non-FSE templates
//--------------------------------------------------------------

if ( ! function_exists( 'planty_gutenberg_fse_show_template_as_layout' ) ) {
	add_filter( 'planty_filter_custom_layout_shown', 'planty_gutenberg_fse_show_template_as_layout', 10, 3 );
	/**
	 * Show an FSE template (header/footer/sidebar) inside a non-FSE templates.
	 * 
	 * Hook: add_filter( 'planty_filter_custom_layout_shown', 'planty_gutenberg_fse_show_template_as_layout', 10, 3 );
	 * 
	 * @param boolean $shown         A flag to detect what a filter was processed and a custom template is shown
	 * @param int|string $layout_id  An ID of the layout (don't show in this case) or a name of the template part to display
	 * @param int $post_id           Optional. A post ID to show. Is not used in this handler.
	 * 
	 * @return boolean  Return true if a layout_id is a string with a template part name
	 *                  and file with template-name.html is present in the theme subfolder '/parts'
	 *                  and its content is not empty and shown.
	 */
	function planty_gutenberg_fse_show_template_as_layout( $shown, $layout_id = '', $post_id = 0 ) {
		// If a layout_id is a string with a template part name
		if ( ! $shown && ! empty( $layout_id ) && is_string( $layout_id) && (int)$layout_id === 0 && planty_gutenberg_is_fse_theme() ) {
			// And file with template-name.html is present in the theme subfolder '/parts'
			$template_path = planty_get_file_dir( "/parts/{$layout_id}.html" );
			if ( ! empty( $template_path ) ) {
				$template_content = planty_fgc( $template_path );
				if ( ! empty( $template_content ) ) {
					planty_show_layout( planty_filter_post_content( $template_content ) );
					$shown = true;
				}
			}
		}
		return $shown;
	}
}




//--------------------------------------------------------------
//--  Replace a template parts according to the Theme Options
//--------------------------------------------------------------

if ( ! function_exists( 'planty_gutenberg_fse_modify_template_with_current_options' ) ) {
	foreach( planty_get_wp_template_hooks() as $hook ) {
		add_filter( $hook, 'planty_gutenberg_fse_modify_template_with_current_options', 30, 3 );
	}
	/**
	 * Transforms the content of the global variable $_wp_current_template_content
	 * according to the settings of the current page:
	 * 
	 * - Remove a sidebar block from the content if a sidebar is not present on the current page
	 * 
	 * - Replace a template name of the header / footer / sidebar
	 * 
	 * Trigger the filter 'planty_filter_wp_current_template_content' to allow other modules
	 * to modify the content.
	 *
	 * @param string $template   Path to the template. See locate_template().
	 * @param string $type       Sanitized filename without extension.
	 * @param array  $templates  A list of template candidates, in descending order of priority.
	 */
	function planty_gutenberg_fse_modify_template_with_current_options( $template, $type = '', $templates = array() ) {
		global $_wp_current_template_content;
		if ( substr( $template, -19 ) == 'template-canvas.php' && is_array( $templates ) ) {
			$_wp_current_template_content = apply_filters( 'planty_filter_wp_current_template_content', $_wp_current_template_content, $template, $type, $templates );
		}
		return $template;
	}
}

if ( ! function_exists( 'planty_gutenberg_fse_modify_template_replace_macros' ) ) {
//	add_filter( 'planty_filter_wp_current_template_content', 'planty_gutenberg_fse_modify_template_replace_macros', 100, 4 );
	/**
	 * Replace a macros {{Y}} and {Y} in the content of the current template to the current year.
	 * 
	 * Hooks: add_filter( 'planty_filter_wp_current_template_content', 'planty_gutenberg_fse_modify_template_replace_macros', 10, 4 );
	 *
	 * @param string $content    A content of the current template.
	 * @param string $template   Path to the template. See locate_template().
	 * @param string $type       Sanitized filename without extension.
	 * @param array  $templates  A list of template candidates, in descending order of priority.
	 * 
	 * @return string  A filtered content of the template.
	 */
	function planty_gutenberg_fse_modify_template_replace_macros( $content, $template, $type, $templates ) {
		return str_replace( array('{{Y}}', '{Y}'), date('Y'), $content );
	}
}



// Header
//----------------------------------------------
if ( ! function_exists( 'planty_gutenberg_fse_modify_template_replace_header' ) ) {
	add_filter( 'planty_filter_wp_current_template_content', 'planty_gutenberg_fse_modify_template_replace_header', 10, 4 );
	/**
	 * Replace a header in the content of the current template
	 * according to the Theme Options of the current page.
	 * 
	 * Hooks: add_filter( 'planty_filter_wp_current_template_content', 'planty_gutenberg_fse_modify_template_replace_header', 10, 4 );
	 *
	 * @param string $content    A content of the current template.
	 * @param string $template   Path to the template. See locate_template().
	 * @param string $type       Sanitized filename without extension.
	 * @param array  $templates  A list of template candidates, in descending order of priority.
	 * 
	 * @return string  A filtered content of the template.
	 */
	function planty_gutenberg_fse_modify_template_replace_header( $content, $template, $type, $templates ) {
		$header_type = planty_get_theme_option( 'header_type' );
		if ( 'custom' == $header_type ) {
			$header_style = planty_get_theme_option( "header_style" );
			$header_id = planty_get_custom_header_id();

			// FSE template part is selected as a header style
			if ( strpos( $header_style, "header-fse-template-" ) !== false ) {
				// Trigger action before the custom header to allow other modules include an additional content to the custom header
				ob_start();
				do_action( 'planty_action_fse_before_custom_header', $content, $template, $type, $templates );
				$before_header = ob_get_contents();
				ob_end_clean();
				// Trigger action after the custom header to allow other modules include an additional content to the custom header
				ob_start();
				do_action( 'planty_action_fse_after_custom_header', $content, $template, $type, $templates );
				$after_header = ob_get_contents();
				ob_end_clean();
				// Inject a new content before and after the header
				if ( ! empty( $before_header ) || ! empty( $after_header ) ) {
					$content = preg_replace(
									'#(<!-- wp:template-part[\s]*{[^}]*"slug":[\s]*"header[^"]*"[^>]*/-->)#U',
									( ! empty( $before_header ) ? str_replace( '$', '\$', $before_header ) : '' )
									. '${1}'
									. ( ! empty( $after_header ) ? str_replace( '$', '\$', $after_header ) : '' ),
									$content
								);
				}
				// Replace a template with a header
				$header_name = '';
				// Found a saved version
				if ( (int)$header_id > 0 ) {
					$post = get_post( $header_id );
					if ( ! empty( $post->post_name ) ) {
						$header_name = $post->post_name;
					}
				// Get a template from a folder 'parts'
				} else {
					$header_name = str_replace( "header-fse-template-", '', $header_style );
				}
				$content = preg_replace( '#(<!-- wp:template-part[\s]*{[^}]*"slug":[\s]*)("header[^"]*")#U', '${1}"' . esc_attr( $header_name ) . '"', $content );

			// Custom header's layout
			} else if ( planty_is_layouts_available() ) {
				ob_start();
				// Trigger action before the custom header to allow other modules include an additional content to the custom header
				do_action( 'planty_action_fse_before_custom_header', $content, $template, $type, $templates );
				// Custom header
				get_template_part( apply_filters( 'planty_filter_get_template_part', "templates/header-custom" ) );
				// Trigger action after the custom header to allow other modules include an additional content to the custom header
				do_action( 'planty_action_fse_after_custom_header', $content, $template, $type, $templates );
				// Get output
				$html = ob_get_contents();
				ob_end_clean();
				if ( ! empty( $html ) ) {
					$content = preg_replace(
									'#<!-- wp:template-part[\s]*{[^}]*"slug":[\s]*"header[^"]*"[^>]*/-->#U',
									str_replace( '$', '\$', $html ),	// Escape a dollar sign in the replacement string to avoid interpretation as a reference to a capture group
									$content
								);
				}
			}
		}
		return $content;
	}
}

if ( ! function_exists( 'planty_gutenberg_fse_custom_header_add_side_menu' ) ) {
	add_action( 'planty_action_fse_after_custom_header', 'planty_gutenberg_fse_custom_header_add_side_menu', 10, 4 );
	/**
	 * Add a side menu to the custom header layout.
	 * 
	 * Hooks: add_action( 'planty_action_fse_after_custom_header', 'planty_gutenberg_fse_custom_header_add_side_menu', 10, 4 );
	 *
	 * @param string $content    A content of the current template.
	 * @param string $template   Path to the template. See locate_template().
	 * @param string $type       Sanitized filename without extension.
	 * @param array  $templates  A list of template candidates, in descending order of priority.
	 */
	function planty_gutenberg_fse_custom_header_add_side_menu( $content, $template, $type, $templates ) {
		// Side menu
		if ( in_array( planty_get_theme_option( 'menu_side' ), array( 'left', 'right' ) ) ) {
			get_template_part( apply_filters( 'planty_filter_get_template_part', 'templates/header-navi-side' ) );
		}
	}
}

if ( ! function_exists( 'planty_gutenberg_fse_custom_header_add_mobile_menu' ) ) {
	add_action( 'planty_action_fse_after_custom_header', 'planty_gutenberg_fse_custom_header_add_mobile_menu', 10, 4 );
	/**
	 * Add a mobile menu to the custom header layout.
	 * 
	 * Hooks: add_action( 'planty_action_fse_after_custom_header', 'planty_gutenberg_fse_custom_header_add_mobile_menu', 10, 4 );
	 *
	 * @param string $content    A content of the current template.
	 * @param string $template   Path to the template. See locate_template().
	 * @param string $type       Sanitized filename without extension.
	 * @param array  $templates  A list of template candidates, in descending order of priority.
	 */
	function planty_gutenberg_fse_custom_header_add_mobile_menu( $content, $template, $type, $templates ) {
		// Mobile menu
		get_template_part( apply_filters( 'planty_filter_get_template_part', 'templates/header-navi-mobile' ) );
	}
}

// Sidebar
//----------------------------------------------
if ( ! function_exists( 'planty_gutenberg_fse_add_sidebar_to_default_areas' ) ) {
	add_filter( 'default_wp_template_part_areas', 'planty_gutenberg_fse_add_sidebar_to_default_areas' );
	/**
	 * Filters the list of allowed template part area values and add 'sidebar' to this list.
	 * 
	 * Hook: add_filter( 'default_wp_template_part_areas', 'planty_gutenberg_fse_add_sidebar_to_default_areas' );
	 *
	 * @param $default_area_definitions  A list with default area definitions for FSE.
	 * 
	 * @return array  A filtered array with area definitions.
	 */
	function planty_gutenberg_fse_add_sidebar_to_default_areas( $default_area_definitions ) {
		$default_area_definitions[] = array(
			'area'        => PLANTY_FSE_TEMPLATE_PART_AREA_SIDEBAR,
			'label'       => esc_html__( 'Sidebar', 'planty' ),
			'description' => esc_html__( 'The Sidebar template defines a page area that typically contains a widgets.', 'planty' ),
			'icon'        => 'sidebar',
			'area_tag'    => 'div',
		);
		return $default_area_definitions;
	}
}

if ( ! function_exists( 'planty_gutenberg_fse_modify_template_replace_sidebar' ) ) {
	add_filter( 'planty_filter_wp_current_template_content', 'planty_gutenberg_fse_modify_template_replace_sidebar', 10, 4 );
	/**
	 * Replace a sidebar in the content of the current template according to the Theme Options of the current page or
	 * Remove a sidebar block from the content of the current template if a sidebar is not present on the current page.
	 * 
	 * Hooks: add_filter( 'planty_filter_wp_current_template_content', 'planty_gutenberg_fse_modify_template_replace_sidebar', 10, 4 );
	 *
	 * @param string $content    A content of the current template.
	 * @param string $template   Path to the template. See locate_template().
	 * @param string $type       Sanitized filename without extension.
	 * @param array  $templates  A list of template candidates, in descending order of priority.
	 * 
	 * @return string  A filtered content of the template.
	 */
	function planty_gutenberg_fse_modify_template_replace_sidebar( $content, $template, $type, $templates ) {
		// If sidebar present - replace it with the sidebar selected in the options
		if ( planty_sidebar_present() ) {
			// Replace sidebar only if the plugin 'trx_addons' is active (because a blocks from this plugin are used)
			if ( planty_exists_trx_addons() ) {
				$sidebar_type = planty_get_theme_option( 'sidebar_type' );
				if ( 'custom' == $sidebar_type && ! planty_is_layouts_available() ) {
					$sidebar_type = 'default';
				}
				// Masks to search a sidebar block in the content
				$sidebar_start = apply_filters( 'planty_filter_wp_block_with_sidebar_mask_start',
												'(<!-- wp:group[\s]*{[^}]*"className":[\s]*"[^"]*sidebar[^"]*"[^>]*-->[\s]*'
												. '<div[^>]*class="[^"]*sidebar[^"]*"[^>]*>)'
												);
				$sidebar_end = apply_filters( 'planty_filter_wp_block_with_sidebar_mask_end',
												'(</div>[\s]*'
												. '<!-- /wp:group -->)'
												);
				$sidebar_mask = "#{$sidebar_start}([\s\S]*){$sidebar_end}#U";
				// Default sidebar with widgets is selected
				if ( 'default' == $sidebar_type ) {
					$sidebar_name = planty_get_theme_option( 'sidebar_widgets' );
					planty_storage_set( 'current_sidebar', 'sidebar' );
					if ( is_active_sidebar( $sidebar_name ) ) {
						// Replace a sidebar block in the content
						if ( preg_match( $sidebar_mask, $content ) ) {
							// Old way - if a sidebar is placed to the template as wp:group
							$content = preg_replace( $sidebar_mask,
													'${1}'
														. '<!-- wp:trx-addons/layouts-widgets {"widgets":"' . esc_attr( $sidebar_name ) . '"} /-->'
													. '${3}',
													$content
												);
						} else {
							// New way - if a sidebar is placed to the template as wp:template-part
							$need_wrapper = apply_filters( 'planty_filter_wp_block_with_sidebar_wrapper_need', false );
							$content = preg_replace( '#<!-- wp:template-part[\s]*{[^}]*"slug":[\s]*"sidebar[^"]*"[^>]*/-->#U',
														( $need_wrapper
															? apply_filters(
																	'planty_filter_wp_block_with_sidebar_wrapper_start',
																	'<!-- wp:group {"className":"sidebar"} -->'
																	. "\n"
																	. '<div class="wp-block-group sidebar">'
																)
															: ''
														)
															. '<!-- wp:trx-addons/layouts-widgets {'
																		. '"widgets":"' . esc_attr( $sidebar_name ) . '"'
																		. ( ! $need_wrapper ? ', "className":"sidebar"' : '' )
															. '} /-->'
														. ( $need_wrapper
															? apply_filters(
																	'planty_filter_wp_block_with_sidebar_wrapper_end',
																	'</div>'
																	. "\n"
																	. '<!-- /wp:group -->'
																)
															: ''
														),
														$content
													);
						}
					}

				// A custom sidebar (built with FSE or with any other builder)
				} else {
					$sidebar_style = planty_get_theme_option( "sidebar_style" );
					$sidebar_id = planty_get_custom_sidebar_id();

					// FSE template part is selected as a sidebar style
					if ( strpos( $sidebar_style, "sidebar-fse-template-" ) !== false ) {
						$sidebar_name = '';
						// Found a saved version
						if ( (int)$sidebar_id > 0 ) {
							$post = get_post( $sidebar_id );
							if ( ! empty( $post->post_name ) ) {
								$sidebar_name = $post->post_name;
							}
						// Get a template from a folder 'parts'
						} else {
							$sidebar_name = str_replace( "sidebar-fse-template-", '', $sidebar_style );
						}
						// Replace a sidebar block in the content
						if ( preg_match( $sidebar_mask, $content ) ) {
							// Old way - if a sidebar is placed to the template as wp:group
							$content = preg_replace( $sidebar_mask,
													'${1}'
														. '<!-- wp:template-part {"slug":"' . esc_attr( $sidebar_name ) . '", "theme":"' . get_stylesheet() . '"} /-->'
													. '${3}',
													$content
												);
						} else {
							// New way - if a sidebar is placed to the template as wp:template-part
							$content = preg_replace( '#(<!-- wp:template-part[\s]*{[^}]*"slug":[\s]*)("sidebar[^"]*")#U', '${1}"' . esc_attr( $sidebar_name ) . '"', $content );
						}

					// Custom sidebar's layout
					} else if ( planty_is_layouts_available() ) {
						ob_start();
						// Trigger action before the custom sidebar to allow other modules include an additional content to the custom sidebar
						do_action( 'planty_action_fse_before_custom_sidebar', $content, $template, $type, $templates );
						// Custom sidebar
						do_action( 'planty_action_show_layout', $sidebar_id );
						// Trigger action after th custom sidebar to allow other modules include an additional content to the custom sidebar
						do_action( 'planty_action_fse_after_custom_sidebar', $content, $template, $type, $templates );
						// Get output
						$html = ob_get_contents();
						ob_end_clean();
						if ( ! empty( $html ) ) {
							$html = preg_replace( "/<\/aside>[\r\n\s]*<aside/", '</aside><aside', $html );
							// Replace a sidebar block in the content
							if ( preg_match( $sidebar_mask, $content ) ) {
								// Old way - if a sidebar is placed to the template as wp:group
								$content = preg_replace( $sidebar_mask,
														'${1}' 
															. str_replace( '$', '\$', $html )	// Escape a dollar sign in the replacement string to avoid interpretation as a reference to a capture group
														. '${3}',
														$content
													);
							} else {
								// New way - if a sidebar is placed to the template as wp:template-part
								$content = preg_replace(
												'#<!-- wp:template-part[\s]*{[^}]*"slug":[\s]*"sidebar[^"]*"[^>]*/-->#U',
												str_replace( '$', '\$', $html ),	// Escape a dollar sign in the replacement string to avoid interpretation as a reference to a capture group
												$content
											);
							}
						}
					}
				}
			}

		// Remove sidebar
		} else {
			// Old way - if a sidebar is placed to the template as wp:group
			$content = preg_replace( '#<!-- wp:group[\s]*{[^}]*"className":[\s]*"[^"]*sidebar[\s\S]*<!-- /wp:group -->#U', '', $content );
			// New way - if a sidebar is placed to the template as wp:template-part
			$content = preg_replace( '#<!-- wp:template-part[\s]*{[^}]*"slug":[\s]*"sidebar[^"]*"[^>]*/-->#U', '', $content );
		}
		return $content;
	}
}

// Footer
//----------------------------------------------
if ( ! function_exists( 'planty_gutenberg_fse_modify_template_replace_footer' ) ) {
	add_filter( 'planty_filter_wp_current_template_content', 'planty_gutenberg_fse_modify_template_replace_footer', 10, 4 );
	/**
	 * Replace a footer in the content of the current template
	 * according to the Theme Options of the current page.
	 * 
	 * Hooks: add_filter( 'planty_filter_wp_current_template_content', 'planty_gutenberg_fse_modify_template_replace_footer', 10, 4 );
	 *
	 * @param string $content    A content of the current template.
	 * @param string $template   Path to the template. See locate_template().
	 * @param string $type       Sanitized filename without extension.
	 * @param array  $templates  A list of template candidates, in descending order of priority.
	 * 
	 * @return string  A filtered content of the template.
	 */
	function planty_gutenberg_fse_modify_template_replace_footer( $content, $template, $type, $templates ) {
		$footer_type = planty_get_theme_option( 'footer_type' );
		if ( 'custom' == $footer_type ) {
			$footer_style = planty_get_theme_option( "footer_style" );
			$footer_id = planty_get_custom_footer_id();

			// FSE template part is selected as a footer style
			if ( strpos( $footer_style, "footer-fse-template-" ) !== false ) {
				// Trigger action before the custom footer to allow other modules include an additional content to the custom footer
				ob_start();
				do_action( 'planty_action_fse_before_custom_footer', $content, $template, $type, $templates );
				$before_footer = ob_get_contents();
				ob_end_clean();
				// Trigger action after the custom footer to allow other modules include an additional content to the custom footer
				ob_start();
				do_action( 'planty_action_fse_after_custom_footer', $content, $template, $type, $templates );
				$after_footer = ob_get_contents();
				ob_end_clean();
				// Inject a new content before and after the footer
				if ( ! empty( $before_footer ) || ! empty( $after_footer ) ) {
					$content = preg_replace(
									'#(<!-- wp:template-part[\s]*{[^}]*"slug":[\s]*"footer[^"]*"[^>]*/-->)#U',
									( ! empty( $before_footer ) ? str_replace( '$', '\$', $before_footer ) : '' )
									. '${1}'
									. ( ! empty( $after_footer ) ? str_replace( '$', '\$', $after_footer ) : '' ),
									$content
								);
				}
				// Replace a template with a footer
				$footer_name = '';
				// Found a saved version
				if ( (int)$footer_id > 0 ) {
					$post = get_post( $footer_id );
					if ( ! empty( $post->post_name ) ) {
						$footer_name = $post->post_name;
					}
				// Get a template from a folder 'parts'
				} else {
					$footer_name = str_replace( "footer-fse-template-", '', $footer_style );
				}
				$content = preg_replace( '#(<!-- wp:template-part[\s]*{[^}]*"slug":[\s]*)("footer[^"]*")#U', '${1}"' . esc_attr( $footer_name ) . '"', $content );

			// Custom footer's layout
			} else if ( planty_is_layouts_available() ) {
				ob_start();
				// Trigger action before the custom footer to allow other modules include an additional content to the custom footer
				do_action( 'planty_action_fse_before_custom_footer', $content, $template, $type, $templates );
				// Custom footer
				get_template_part( apply_filters( 'planty_filter_get_template_part', "templates/footer-custom" ) );
				// Trigger action after th custom footer to allow other modules include an additional content to the custom footer
				do_action( 'planty_action_fse_after_custom_footer', $content, $template, $type, $templates );
				// Get output
				$html = ob_get_contents();
				ob_end_clean();
				if ( ! empty( $html ) ) {
					$content = preg_replace(
									'#<!-- wp:template-part[\s]*{[^}]*"slug":[\s]*"footer[^"]*"[^>]*/-->#U',
									str_replace( '$', '\$', $html ),	// Escape a dollar sign in the replacement string to avoid interpretation as a reference to a capture group
									$content
								);
				}
			}
		}
		return $content;
	}
}



//------------------------------------------------------
//--  Replace featured image with a largest size
//------------------------------------------------------
if ( ! function_exists( 'planty_gutenberg_fse_replace_featured_image_renderer' ) ) {
	add_filter( 'block_type_metadata_settings', 'planty_gutenberg_fse_replace_featured_image_renderer', 10, 2 );
	/**
	 * Replace a render_callback of the post featured image to increase its thumb size.
	 *
	 * @param array $settings Array of determined settings for registering a block type.
	 * @param array $metadata Metadata provided for registering a block type.
	 */
	function planty_gutenberg_fse_replace_featured_image_renderer( $settings = array(), $metadata = array() ) {
		if ( ! empty( $settings['render_callback'] ) && $settings['render_callback'] == 'render_block_core_post_featured_image' ) {
			$settings['render_callback'] = 'planty_gutenberg_fse_featured_image_renderer';
		}
		return $settings;
	}
}

if ( ! function_exists( 'planty_gutenberg_fse_featured_image_renderer' ) ) {
	/**
	 * Renders the 'core/post-featured-image' block on the server with a theme-specific thumb size.
	 *
	 * @param array    $attributes Block attributes.
	 * @param string   $content    Block default content.
	 * @param WP_Block $block      Block instance.
	 * 
	 * @return string  Returns the featured image for the current post.
	 */
	function planty_gutenberg_fse_featured_image_renderer( $attributes, $content, $block ) {
		if ( ! isset( $block->context['postId'] ) ) {
			return '';
		}
		$post_ID = $block->context['postId'];

		if ( empty( $post_ID ) ) {
			return '';
		}

		// Set a current post to allow using a post template functions
		$GLOBALS['post'] = get_post( $post_ID );
		setup_postdata( $GLOBALS['post'] );

		ob_start();

		$planty_expanded   = ! planty_sidebar_present() && planty_get_theme_option( 'expand_content' ) == 'expand';
		$planty_hover      = planty_get_theme_option( 'image_hover' );
		$planty_components = planty_array_get_keys_by_value( planty_get_theme_option( 'meta_parts' ) );

		$css = '';
		if ( ! empty( $attributes['width'] ) ) {
			$css .= "width:{$attributes['width']};";
		}
		if ( ! empty( $attributes['height'] ) ) {
			$css .= "height:{$attributes['height']};";
		}
		if ( ! empty( $attributes['scale'] ) ) {
			$css .= "object-fit:{$attributes['scale']};";
		}

		planty_show_post_featured( apply_filters( 'planty_filter_args_featured',
			array(
				'css'        => $css,
				'no_links'   => empty( $attributes['isLink'] ),
				'hover'      => $planty_hover,
				'meta_parts' => $planty_components,
				'singular'   => planty_is_singular(),
				'thumb_size' => planty_get_thumb_size( strpos( planty_get_theme_option( 'body_style' ), 'full' ) !== false
									? 'full'
									: ( $planty_expanded || planty_is_singular()
										? 'huge' 
										: 'big' 
										)
									),
			),
			'wp-block-featured-image',
			$attributes
		) );

		$featured_image = ob_get_contents();

		ob_end_clean();

		// Restore current post data
		wp_reset_postdata();

		return $featured_image;
	}
}
