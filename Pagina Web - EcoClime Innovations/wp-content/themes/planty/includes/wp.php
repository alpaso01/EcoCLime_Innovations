<?php
/**
 * WP tags and utils
 *
 * @package PLANTY
 * @since PLANTY 1.0
 */

if ( ! function_exists( 'planty_wp_theme_setup' ) ) {

	add_action( 'after_setup_theme', 'planty_wp_theme_setup' );

	/**
	 * Theme init
	 * 
	 * Add actions and filters on the theme start
	 */
	function planty_wp_theme_setup() {

		// Remove macros from title
		add_filter( 'wp_title', 'planty_remove_macros_from_site_title' );
		add_filter( 'wp_title_parts', 'planty_remove_macros_from_site_title' );
		add_filter( 'document_title_parts', 'planty_remove_macros_from_site_title' );

		// Breadcrumbs link 'All posts'
		add_filter( 'post_type_archive_link', 'planty_get_template_page_link', 10, 2 );

		// Adds a link to the 'Privacy Policy' page to the Privacy text
		// (uncomment a next line if you need it)
		// add_filter( 'planty_filter_privacy_text', 'planty_add_privacy_page_link' );
	}
}


if ( ! function_exists( 'planty_is_preview' ) ) {
	/**
	 * Check if current page is a preview mode of the specified Page Builder.
	 * 
	 * @param string $builder Slug of the supported Page Builder or 'any'
	 *                        for checking all builders.
	 * 
	 * @return bool           Return true if current page is a preview mode.
	 */
	function planty_is_preview( $builder = 'any' ) {
		return ( in_array( $builder, array( 'any', 'elm', 'elementor' ) ) && function_exists( 'planty_elementor_is_preview' ) && planty_elementor_is_preview() )
				||
				( in_array( $builder, array( 'any', 'gb', 'gutenberg' ) ) && function_exists( 'planty_gutenberg_is_preview' ) && planty_gutenberg_is_preview() );
	}
}


if ( ! function_exists( 'planty_get_wp_template_hooks' ) ) {
	/**
	 * Return a list with WordPress template hook names, like 'archive_template', 'frontpage_template', etc.
	 * 
	 * @return array  List with template hook names.
	 */
	function planty_get_wp_template_hooks() {
		return apply_filters( 'planty_filter_wp_template_hooks', array(
					'404_template',
					'archive_template',
					'attachment_template',
					'author_template',
					'category_template',
					'date_template',
					'embed_template',
					'frontpage_template',
					'home_template',
					'index_template',
					'page_template',
					'paged_template',
					'privacypolicy_template',
					'search_template',
					'single_template',
					'singular_template',
					'tag_template',
					'taxonomy_template'
				) );
	}
}


/* Blog utilities
-------------------------------------------------------------------------------- */

if ( ! function_exists( 'planty_detect_blog_mode' ) ) {
	/**
	 * Detect current blog mode.
	 * 
	 * Current blog mode is used to get correspond options (front|home|post|page|category|tag|author|search|blog|...)
	 * instead the main option's value. Blog mode is used as a suffix added to the option name to override main option value.
	 * 
	 * For example:
	 *   $pos = planty_get_theme_option( 'sidebar_position' );
	 * 
	 * If a current blog mode is 'search' and in the theme options exists an option with a name 'sidebar_position_search' -
	 * this option's value will be used instead the main option's 'sidebar_position' value (override it on the search pages).
	 * 
	 * @return string   Mode name (used as a suffix for override options).
	 */
	function planty_detect_blog_mode() {
		if ( is_front_page() && ! is_home() ) {
			$mode = 'front';
		} elseif ( is_home() ) {
			$mode = 'home';     // Specify 'blog' if you don't need a separate options for the homepage
		} elseif ( planty_is_single() ) {
			$mode = 'post';
		} elseif ( is_page() && ! planty_storage_isset( 'blog_archive' ) ) {
			$mode = 'page';
		} elseif ( is_category() ) {
			$mode = 'category';
		} elseif ( is_tag() ) {
			$mode = 'tag';
		} elseif ( is_author() ) {
			$mode = 'author';
		} elseif ( is_search() ) {
			$mode = 'search';
		} else {
			$mode = 'blog';
		}
		return apply_filters( 'planty_filter_detect_blog_mode', $mode );
	}
}

if ( ! function_exists( 'planty_is_blog_mode_custom' ) ) {
	/**
	 * Check if a current blog mode is custom
	 * ( not one of front|home|post|page|category|tag|author|search|blog ).
	 * 
	 * @return bool   Return true if current blog mode is custom.
	 */
	function planty_is_blog_mode_custom() {
		return ! in_array( planty_storage_get( 'blog_mode' ), apply_filters( 'planty_filter_blog_mode_standard', array(
					'front', 'home', 'post', 'page', 'category', 'tag', 'author', 'search', 'blog'
				) ) );

	}
}

if ( ! function_exists( 'planty_get_current_mode_image' ) ) {
	/**
	 * Return an image URL for current post or page or category or blog mode.
	 * 
	 * @param string $default An URL of the default image
	 *                        ( if no image for the current mode was found ).
	 * 
	 * @return string         URL of an image or empty string.
	 */
	function planty_get_current_mode_image( $default = '' ) {
		if ( is_category() || is_tax() ) {
			$img = planty_get_term_image();
			if ( '' != $img ) {
				$default = $img;
			}
		} elseif ( planty_is_singular() || planty_storage_isset( 'blog_archive' ) ) {
			if ( is_home() ) {
				$posts_page = (int)get_option( 'page_for_posts' );
				if ( $posts_page > 0 ) {
					// Get a page featured image of the page for posts
					$img = wp_get_attachment_image_src( get_post_thumbnail_id( $posts_page ), 'full' );
					if ( ! empty( $img[0] ) ) {
						$default = $img[0];
					}
				}
			} else if ( has_post_thumbnail() ) {
				$img = wp_get_attachment_image_src( get_post_thumbnail_id(), 'full' );
				if ( ! empty( $img[0] ) ) {
					$default = $img[0];
				}
			}
		}
		return planty_clear_thumb_size( $default );
	}
}

if ( ! function_exists( 'planty_blog_archive_start' ) ) {
	/**
	 * Starts a blog archive template.
	 * 
	 * Display a first part of the template (before the mask %%CONTENT%%)
	 * and store a second part to output later.
	 */
	function planty_blog_archive_start() {
		$main_post = planty_storage_get( 'blog_archive_template_post' );
		if ( is_object( $main_post ) ) {
			// Prepare post with template content
			$GLOBALS['post'] = $main_post;
			setup_postdata( $main_post );
			// Get template content
			$planty_content            = '';
			$planty_blog_archive_mask  = '%%CONTENT%%';
			$planty_blog_archive_subst = sprintf( '<div class="blog_archive">%s</div>', $planty_blog_archive_mask );
			$planty_content            = apply_filters( 'the_content', get_the_content() );
			// Destroy sc parameters from the content of the template
			set_query_var( 'planty_template_args', false );
			// Display parts of the template
			if ( '' != $planty_content ) {
				$planty_pos = stripos( $planty_content, $planty_blog_archive_mask );
				if ( false !== $planty_pos ) {
					$planty_content = preg_replace( '/(\<p\>\s*)?' . $planty_blog_archive_mask . '(\s*\<\/p\>)/i', $planty_blog_archive_subst, $planty_content );
				} else {
					$planty_content .= $planty_blog_archive_subst;
				}
				$planty_content = explode( $planty_blog_archive_mask, $planty_content );
				// Display first part
				planty_show_layout( apply_filters( 'planty_filter_blog_archive_start', $planty_content[0] ) );
				// And store second part
				planty_storage_set( 'blog_archive_end', $planty_content[1] );
			}
			// Restore current post data
			wp_reset_postdata();
		}
		// Destroy sc parameters from the content of the template
		set_query_var( 'planty_template_args', false );
	}
}

if ( ! function_exists( 'planty_blog_archive_end' ) ) {
	/**
	 * Display the end blog archive template.
	 * 
	 * Output a second part (stored in the previous call of planty_blog_archive_start).
	 */
	function planty_blog_archive_end() {
		$html = planty_storage_get( 'blog_archive_end' );
		if ( '' != $html ) {
			// Display second part of template content
			planty_show_layout( apply_filters( 'planty_filter_blog_archive_end', $html ) );
		}
	}
}

if ( ! function_exists( 'planty_blog_archive_get_template' ) ) {
	/**
	 * Return a name of the archive template for the current blog style.
	 * 
	 * @param string $blog_style Optional. The blog_style to detect a template.
	 * 
	 * @return string            Relative path for the template 
	 *                           or an empty string if not detected.
	 */
	function planty_blog_archive_get_template( $blog_style = '' ) {
		if ( empty( $blog_style ) ) {
			$blog_style = planty_get_theme_option( 'blog_style' );
		}
		$parts   = explode( '_', $blog_style );
		$archive = 'index';
		if ( planty_storage_isset( 'blog_styles', $parts[0], 'archive' ) ) {
			$archive = planty_storage_get_array( 'blog_styles', $parts[0], 'archive' );
		}
		return apply_filters( 'planty_filter_blog_archive_template', $archive, $blog_style );
	}
}

if ( ! function_exists( 'planty_blog_item_get_template' ) ) {
	/**
	 * Return a name of the blog item template for the current blog style.
	 * 
	 * @param string $blog_style Optional. The blog_style to detect a template.
	 * 
	 * @return string            Relative path for the template 
	 *                           or an empty string if not detected.
	 */
	function planty_blog_item_get_template( $blog_style = '' ) {
		if ( empty( $blog_style ) ) {
			$blog_style = planty_get_theme_option( 'blog_style' );
		}
		$parts = explode( '_', $blog_style );
		$item  = '';
		if ( strpos( $parts[0], 'blog-custom-' ) === 0 ) {
			$item = 'templates/content-custom';
		} elseif ( planty_storage_isset( 'blog_styles', $parts[0], 'item' ) ) {
			$item = planty_storage_get_array( 'blog_styles', $parts[0], 'item' );
		} else {
			$item = "templates/content-{$parts[0]}";
		}
		return $item;
	}
}

if ( ! function_exists('planty_get_the_ID') ) {
	/**
	 * Return current post ID
	 * 
	 * @return int  Current post ID
	 */
	function planty_get_the_ID() {
		global $wp_query;
		return in_the_loop() 
					? get_the_ID() 
					: ( ! empty( $wp_query->post->ID )
						? $wp_query->post->ID
						: ( planty_is_singular() && ! empty( $wp_query->queried_object->ID )
							? $wp_query->queried_object->ID
							: 0
							)
						);
	}
}

if ( ! function_exists( 'planty_get_post_id' ) ) {
	/**
	 * Return ID of the first post/page from the query.
	 * 
	 * @param array $args Optional. Query arguments to get posts.
	 * 
	 * @return int        ID of the first post from the query results
	 *                    or 0 if posts not found.
	 */
	function planty_get_post_id( $args = array() ) {
		$args  = array_merge(
			array(
				'posts_per_page' => 1,
			), $args
		);
		$id    = 0;
		$query = new WP_Query( $args );
		if ( $query->have_posts() ) {
			$id = ! empty( $query->posts[0]->ID )
				? $query->posts[0]->ID
				: ( ! empty( $query->post->ID )
					? $query->post->ID
					: 0
					);
		}
		return $id;
	}
}


if ( ! function_exists( 'planty_get_post_content' ) ) {
	/**
	 * Return a content of the post.
	 * 
	 * @param bool $apply_filters Optional. Whether to apply filters 'the_content' filters
	 *                                      to post content before return it.
	 * 
	 * @return string             A current post content.
	 */
	function planty_get_post_content( $apply_filters = false ) {
		global $post;
		$content = ! empty( $post->post_content ) ? $post->post_content : '';
		return $apply_filters ? apply_filters( 'the_content', $content ) : $content;
	}
}


if ( ! function_exists( 'planty_filter_post_content' ) ) {
	/**
	 * Prepare a post content in the blog posts instead applying 'the_content' filter
	 * to avoid conflicts with Gutenberg.
	 * 
	 * @param string $content A post content to processing with shortcodes and embeds.
	 * 
	 * @return string         A processed content.
	 */
	function planty_filter_post_content( $content ) {
		$content = apply_filters( 'planty_filter_post_content', $content );
		global $wp_embed;
		if ( is_object( $wp_embed ) ) {
			$content = $wp_embed->autoembed( $content );
		}
		return do_shortcode( $content );
	}
}

if ( ! function_exists( 'planty_get_template_page_id' ) ) {
	/**
	 * Return ID of the page with a specified template.
	 * 
	 * @param array $args Optional. Parameters to search a page template.
	 * 
	 * @return int        ID of the page with the specified template or 0.
	 */
	function planty_get_template_page_id( $args = array() ) {
		$args   = array_merge(
			array(
				'template'   => 'blog.php',
				'post_type'  => 'post',
				'parent_cat' => '',
			), $args
		);
		$q_args = array(
			'post_type'      => 'page',
			'post_status'    => 'publish',
			'posts_per_page' => 1,
			'orderby'        => 'id',
			'order'          => 'asc',
			'meta_query'     => array( 'relation' => 'AND' ),
		);
		if ( ! empty( $args['template'] ) ) {
			$q_args['meta_query'][] = array(
				'key'     => '_wp_page_template',
				'value'   => $args['template'],
				'compare' => '=',
			);
		}
		if ( ! empty( $args['post_type'] ) ) {
			$q_args['meta_query'][] = array(
				'key'     => 'planty_options_post_type',
				'value'   => $args['post_type'],
				'compare' => '=',
			);
		}
		if ( '' !== $args['parent_cat'] ) {
			$q_args['meta_query'][] = array(
				'key'     => 'planty_options_parent_cat',
				'value'   => $args['parent_cat'] > 0 ? $args['parent_cat'] : 1,
				'compare' => $args['parent_cat'] > 0 ? '=' : '<',
			);
		}
		return planty_get_post_id( $q_args );
	}
}

if ( ! function_exists( 'planty_get_template_page_link' ) ) {
	/**
	 * Return a link to the page with a theme specific archive template page
	 * for specified post type and/or category.
	 * 
	 * For example: [ 'page_template' => 'blog.php', 'post_type'=> 'post', 'parent_cat' => 0 ]
	 * 
	 * Handler of the add_filter('post_type_archive_link', 'planty_get_template_page_link', 10, 2 );
	 * 
	 * @param string $link      Optional. A filtering URL (may be overrided with a new URL)
	 * @param string $post_type Optional. A post type to detect a template link
	 * 
	 * @return string           A filtered link
	 */
	function planty_get_template_page_link( $link = '', $post_type = '' ) {
		if ( ! empty( $post_type ) ) {
			$id = planty_get_template_page_id(
				array(
					'post_type'  => $post_type,
					'parent_cat' => 0,
				)
			);
			if ( $id > 0 ) {
				$link = get_permalink( $id );
			}
		}
		return $link;
	}
}

if ( ! function_exists( 'planty_get_posts_archive_template' ) ) {

	add_filter( 'archive_template', 'planty_get_posts_archive_template', 100 );

	/**
	 * Change a standard archive template to the custom page.
	 * 
	 * Hooks:
	 *
	 * add_filter( 'archive_template', 'planty_get_posts_archive_template', 100 );
	 * 
	 * @param string $template Path to the .php-file with an archive template
	 * 
	 * @return string          A filtered path to the template
	 */
	function planty_get_posts_archive_template( $template ) {
		if ( planty_get_theme_option( 'use_blog_archive_pages' ) ) {
			if ( is_post_type_archive() ) {
				$obj = get_queried_object();
				if ( ! empty( $obj->name ) ) {
					$templates = get_option( 'planty_blog_archive_templates' );
					if ( ! empty( $templates[ $obj->name ] ) ) {
						$template = planty_redirect_to_archive_template( $template, $templates[ $obj->name ] );
					}
				}
			} else {
				$template = planty_get_tax_archive_template( $template );
			}
		}
		return $template;
	}	
}

if ( ! function_exists( 'planty_get_tax_archive_template' ) ) {

	add_filter( 'category_template', 'planty_get_tax_archive_template', 100 );
	add_filter( 'taxonomy_template', 'planty_get_tax_archive_template', 100 );

	/**
	 * Change a standard taxonomy template to the custom page.
	 *
	 * Hooks:
	 *
	 * add_filter( 'category_template', 'planty_get_tax_archive_template', 100 );
	 *
	 * add_filter( 'taxonomy_template', 'planty_get_tax_archive_template', 100 );
	 * 
	 * @param string $template Path to the .php-file with a template for category/taxonomy
	 * 
	 * @return string          A filtered path to the template
	 */
	function planty_get_tax_archive_template( $template ) {
		if ( planty_get_theme_option( 'use_blog_archive_pages' ) && ( is_category() || is_tag() || is_tax() ) ) {
			$obj = get_queried_object();
			global $wp_query;
			$tax  = ! empty( $obj->taxonomy ) ? $obj->taxonomy : '';
			$term = ! empty( $obj->term_id ) ? $obj->term_id : '';
			$pt   = ! empty( $wp_query->posts[0]->post_type ) ? $wp_query->posts[0]->post_type : '';
			if ( $pt && $tax && $term ) {
				$pt_tax  = planty_get_post_type_taxonomy( $pt );
				if ( $pt_tax == $tax ) {
					$templates = get_option( 'planty_blog_archive_templates' );
					if ( ! empty( $templates[ "{$pt}_{$tax}_{$term}" ] ) ) {
						$template = planty_redirect_to_archive_template( $template, $templates[ "{$pt}_{$tax}_{$term}" ] );
					} else {
						$found = false;
						do {
							$parent = isset( $obj->parent ) 
											? $obj->parent
											: ( isset( $obj->category_parent ) 
												? $obj->category_parent
												: 0 );
							if ( ! empty( $templates[ "{$pt}_{$tax}_{$parent}" ] ) ) {
								$template = planty_redirect_to_archive_template( $template, $templates[ "{$pt}_{$tax}_{$parent}" ], $term );
								$found = true;
								break;
							} else {
								$obj = get_term_by( 'id', $parent, $tax, OBJECT );
							}
						} while ( $parent > 0 );
						if ( ! $found && ! empty( $templates[ "{$pt}" ] ) ) {
							$template = planty_redirect_to_archive_template( $template, $templates[ "{$pt}" ], $term );
						}
					}
				}
			}
		}
		return $template;
	}	
}

if ( ! function_exists( 'planty_redirect_to_archive_template' ) ) {
	/**
	 * Redirects to the page that is assigned as the archive template for the queried category.
	 * 
	 * @param string      $template Path to the .php-file with an archive template
	 * @param int         $page_id  ID of the page-template
	 * @param object|bool $term     The queried term object or false
	 * 
	 * @return string               A path to the template 'blog.php'
	 */
	function planty_redirect_to_archive_template( $template, $page_id, $term = false ) {
		// Store page number
		$page_number = is_paged()
							? ( get_query_var( 'paged' ) 
								? get_query_var( 'paged' ) 
								: ( get_query_var( 'page' ) 
									? get_query_var( 'page' ) 
									: 1 
									)
								)
							: 1;
		// Make new query
		$GLOBALS['wp_query'] = new WP_Query( array(
												'p' => $page_id,
												'post_type' => 'page'
												)
											);
		wp_reset_postdata();
		set_query_var( 'page_number', $page_number );
		// Load page options
		planty_override_theme_options( null, $page_id );
		// Override parent category
		if ( $term > 0 ) {
			planty_storage_set_array( 'options_meta', 'parent_cat', $term );
		}
		return planty_get_file_dir( 'blog.php' );
	}
}

if ( ! function_exists( 'planty_save_archive_template' ) ) {

	add_action( 'save_post', 'planty_save_archive_template', 11 );

	/**
	 * Store the saved page to the list with blog templates if in the parameter
	 * "Page template" user select "Blog archive".
	 * 
	 * @param int $post_id ID of the saved page.
	 * 
	 * @return int         ID of the saved page.
	 */
	function planty_save_archive_template( $post_id ) {
		// verify nonce
		if ( ! wp_verify_nonce( planty_get_value_gp( 'override_options_nonce' ), admin_url() ) ) {
			return $post_id;
		}

		// check autosave
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $post_id;
		}

		$post_type = wp_kses_data( wp_unslash( isset( $_POST['override_options_post_type'] ) ? $_POST['override_options_post_type'] : $_POST['post_type'] ) );

		// Check permissions
		$capability = 'page';
		$post_types = get_post_types( array( 'name' => $post_type ), 'objects' );
		if ( ! empty( $post_types ) && is_array( $post_types ) ) {
			foreach ( $post_types as $type ) {
				$capability = $type->capability_type;
				break;
			}
		}
		if ( ! current_user_can( 'edit_' . ( $capability ), $post_id ) ) {
			return $post_id;
		}

		// Save separate meta options to search template pages
		if ( 'page' == $post_type ) {
			$meta = get_post_meta( $post_id, 'planty_options', true );
			$page_template = isset( $_POST['page_template'] )
								? wp_kses_data( wp_unslash( $_POST['page_template'] ) )
								: get_post_meta( $post_id, '_wp_page_template', true );
			$templates = get_option( 'planty_blog_archive_templates' );
			if ( ! is_array( $templates ) ) {
				$templates = array();
			}
			if ( 'blog.php' == $page_template ) {
				$pt   = isset( $meta['post_type'] ) ? $meta['post_type'] : 'post';
				$tax  = planty_get_post_type_taxonomy( $pt );
				$term = isset( $meta['parent_cat'] ) ? $meta['parent_cat'] : 0;
				$templates = planty_array_delete_by_value( $templates, $post_id );
				$templates[ $pt . ( $term > 0 ? "_{$tax}_{$term}" : '' ) ] = $post_id;
			} else {
				$templates = planty_array_delete_by_value( $templates, $post_id );
			}
			update_option( 'planty_blog_archive_templates', $templates );
		}
	}
}

// Delete page from templates
if ( ! function_exists( 'planty_delete_archive_template' ) ) {

	add_action( 'delete_post', 'planty_delete_archive_template', 11 );

	/**
	 * Delete a page with specified ID from the list with blog templates
	 * after the post is deleted.
	 * 
	 * @param int $post_id ID of the deleted page.
	 */
	function planty_delete_archive_template( $post_id ) {
		$templates = get_option( 'planty_blog_archive_templates' );
		if ( is_array( $templates ) ) {
			$templates = planty_array_delete_by_value( $templates, $post_id );
			update_option( 'planty_blog_archive_templates', $templates );
		}
	}
}

if ( ! function_exists( 'planty_get_protocol' ) ) {
	/**
	 * Return a current protocol ( http or https ) of the site.
	 * 
	 * @return string A string with a protocol.
	 */
	function planty_get_protocol( $suffix = false ) {
		return ( is_ssl() ? 'https' : 'http' ) . ( ! empty( $suffix ) ? ':' : '' );
	}
}

if ( ! function_exists( 'planty_get_hash_link' ) ) {
	/**
	 * Converts an internal link depending on the current mode:
	 * returns the full URL in the Customizer, otherwise only its #hash part.
	 * 
	 * @return string  A converted URL.
	 */
	function planty_get_hash_link( $hash ) {
		if ( 0 !== strpos( $hash, 'http' ) ) {
			if ( '#' != $hash[0] ) {
				$hash = '#' . $hash;
			}
			if ( is_customize_preview() ) {
				$url = planty_get_current_url();
				$pos = strpos( $url, '#' );
				if ( false !== $pos ) {
					$url = substr( $url, 0, $pos );
				}
				$hash = $url . $hash;
			}
		}
		return $hash;
	}
}

if ( ! function_exists( 'planty_get_current_url' ) ) {
	/**
	 * Return an URL to the current page.
	 * 
	 * @return string  A current page URL.
	 */
	function planty_get_current_url() {
		return add_query_arg( array() );
	}
}

if ( ! function_exists( 'planty_check_url' ) ) {
	/**
	 * Check if an URL of the current page contain a specified string.
	 * 
	 * @param string|array $val A string or an array of strings to check.
	 * 
	 * @return bool             Return true if an URL contain a string.
	 */
	function planty_check_url( $val ) {
		if ( ! is_array( $val ) ) {
			$val = array( $val );
		}
		$rez = false;
		$url = planty_get_current_url();
		foreach	( $val as $s ) {
			$rez = false !== strpos( $url, $s );
			if ( $rez ) {
				break;
			}
		}
		return $rez;
	}
}

if ( ! function_exists( 'planty_attachment_url_to_postid' ) ) {
	/**
	 * Tries to convert an attachment URL into a post ID.
	 * 
	 * @param string $url The URL to resolve.
	 * 
	 * @return int        The found post ID, or 0 on failure.
	 */
	function planty_attachment_url_to_postid( $url ) {
		static $images = array();
		if ( ! isset( $images[ $url ] ) ) {
			$images[ $url ] = attachment_url_to_postid( planty_clear_thumb_size( $url, false ) );
		}
		return $images[ $url ];
	}
}

if ( ! function_exists( 'planty_remove_macros_from_site_title' ) ) {
	/**
	 * Remove macros from the title.
	 * 
	 * Hooks:
	 *
	 * add_filter( 'wp_title', 'planty_remove_macros_from_site_title' );
	 * add_filter( 'wp_title_parts', 'planty_remove_macros_from_site_title' );
	 * add_filter( 'document_title_parts', 'planty_remove_macros_from_site_title' );
	 * 
	 * @param string $title The site/page title to clean.
	 * 
	 * @return string       The processed title without macros.
	 */
	function planty_remove_macros_from_site_title( $title ) {
		if ( is_array( $title ) ) {
			foreach ( $title as $k => $v ) {
				$title[ $k ] = planty_remove_macros( $v );
			}
		} else {
			$title = planty_remove_macros( $title );
		}
		return $title;
	}
}

if ( ! function_exists( 'planty_get_blog_title' ) ) {
	/**
	 * Return a blog title for the current blog mode.
	 *
	 * @return string  A blog title.
	 */
	function planty_get_blog_title() {

		if ( is_front_page() ) {
			$title = esc_html__( 'Home', 'planty' );
		} elseif ( is_home() ) {
			$title = get_option( 'page_for_posts' ) == get_queried_object_id()
						? get_the_title( get_queried_object_id() )
						: esc_html__( 'All Posts', 'planty' );
		} elseif ( is_author() ) {
			$curauth = ( get_query_var( 'author_name' ) ) ? get_user_by( 'slug', get_query_var( 'author_name' ) ) : get_userdata( get_query_var( 'author' ) );
			// Translators: Add the author's name to the title
			$title = sprintf( esc_html__( 'Author page: %s', 'planty' ), $curauth->display_name );
		} elseif ( is_404() ) {
			$title = esc_html__( 'URL not found', 'planty' );
		} elseif ( is_search() ) {
			// Translators: Add the author's name to the title
			$title = sprintf( esc_html__( 'Search: %s', 'planty' ), get_search_query() );
		} elseif ( is_day() ) {
			// Translators: Add the queried date to the title
			$title = sprintf( esc_html__( 'Daily Archives: %s', 'planty' ), get_the_date() );
		} elseif ( is_month() ) {
			// Translators: Add the queried month to the title
			$title = sprintf( esc_html__( 'Monthly Archives: %s', 'planty' ), get_the_date( 'F Y' ) );
		} elseif ( is_year() ) {
			// Translators: Add the queried year to the title
			$title = sprintf( esc_html__( 'Yearly Archives: %s', 'planty' ), get_the_date( 'Y' ) );
		} elseif ( is_category() ) {
			$title = single_cat_title( '', false );
		} elseif ( is_tag() ) {
			// Translators: Add the tag's name to the title
			$title = sprintf( esc_html__( 'Tag: %s', 'planty' ), single_tag_title( '', false ) );
		} elseif ( is_tax() ) {
			$title = single_term_title( '', false );
		} elseif ( is_post_type_archive() ) {
			$obj   = get_queried_object();
			$title = ! empty( $obj->labels->all_items ) ? $obj->labels->all_items : '';
		} elseif ( is_attachment() ) {
			// Translators: Add the attachment's name to the title
			$title = sprintf( esc_html__( 'Attachment: %s', 'planty' ), get_the_title() );
		} elseif ( planty_is_single() || is_page() ) {
			$title = get_the_title();
		} else {
			$title = get_the_title();
		}
		return apply_filters( 'planty_filter_get_blog_title', $title );
	}
}

if ( ! function_exists( 'planty_get_post_categories' ) ) {
	/**
	 * Return the string with links to all categories of the current post
	 * (or a post with the specified ID).
	 *
	 * @param string   $delimiter A delimiter between links.
	 * @param int|bool $id        A post ID to return links or false if the current post must be used.
	 * @param bool     $links     true if categories must be wrapped with a tag <a>
	 *                            (otherwise categories will be wrapped with a tag <span>).
	 *
	 * @return string  A string with category terms.
	 */
	function planty_get_post_categories( $delimiter = ', ', $id = false, $links = true ) {
		return planty_get_post_terms( $delimiter, $id, 'category', $links );
	}
}

if ( ! function_exists( 'planty_get_post_terms' ) ) {
	/**
	 * Return the string with links to all terms from the specified taxonomy of the current post
	 * (or a post with the specified ID).
	 *
	 * @param string   $delimiter Optional. A delimiter between links. ', ' is ised by default.
	 * @param int|bool $id        Optional. A post ID to return links. If omitted or false -
	 *                            the current post is used.
	 * @param string   $category  Optional. A taxonomy slug to return terms. 'category' is used by default.
	 * @param bool     $links     Optional. true or omitted - categories must be wrapped with a tag <a>
	 *                            (otherwise categories will be wrapped with a tag <span>).
	 *
	 * @return string  A string with terms of the current post.
	 */
	function planty_get_post_terms( $delimiter = ', ', $id = false, $taxonomy = 'category', $links = true ) {
		$output = '';
		if ( empty( $id ) ) {
			$id = get_the_ID();
		}
		if ( empty( $taxonomy ) ) {
			$taxonomy = planty_get_post_type_taxonomy( get_post_type( $id ) );
		}
		$terms = get_the_terms( $id, $taxonomy );
		if ( ! empty( $terms ) && is_array( $terms ) ) {
			foreach ( $terms as $term ) {
				if ( empty( $term->term_id ) ) {
					continue;
				}
				$output .= ( ! empty( $output )
								? $delimiter
								: ''
								)
							. ( $links
								? '<a href="' . esc_url( get_term_link( $term->term_id, $taxonomy ) ) . '"'
										// Translators: Add the term's name to the title
										. ' title="' . sprintf( esc_attr__( 'View all posts in %s', 'planty' ), $term->name ) . '"'
										. '>'
								: '<span>'
								)
								. apply_filters( 'planty_filter_term_name', $term->name, $term )
							. ( $links
								? '</a>'
								: '</span>'
								);
			}
		}
		return $output;
	}
}

if ( ! function_exists( 'planty_get_post_type_taxonomy' ) ) {
	/**
	 * Return a first (or main) taxonomy for the specified post type.
	 *
	 * @param string $post_type Optional. A post type to return taxonomy.
	 *                          If empty or omitted - a taxonomy of the current post is returned.
	 *
	 * @return string           A taxonomy name.
	 */
	function planty_get_post_type_taxonomy( $post_type = '' ) {
		if ( empty( $post_type ) ) {
			$post_type = get_post_type();
		}
		if ( 'post' == $post_type ) {
			$tax = 'category';
		} else {
			$taxonomy_names = get_object_taxonomies( $post_type );
			$tax            = ! empty( $taxonomy_names[0] ) ? $taxonomy_names[0] : '';
		}
		return apply_filters( 'planty_filter_post_type_taxonomy', $tax, $post_type );
	}
}

if ( ! function_exists( 'planty_get_edited_post_id' ) ) {
	/**
	 * The ID of the edited post, or 0 for the new post,
	 * or false if a current screen is not an edit page.
	 *
	 * @return false|int The ID of the edited post.
	 */
	function planty_get_edited_post_id() {
		$id = false;
		if ( is_admin() ) {
			$url = planty_get_current_url();
			if ( strpos( $url, 'post.php' ) !== false ) {
				if ( planty_get_value_gp( 'action' ) == 'edit' ) {
					$post_id = planty_get_value_gp( 'post' );
					if ( 0 < $post_id ) {
						$id = $post_id;
					}
				}
			} elseif ( strpos( $url, 'post-new.php' ) !== false ) {
				$id = 0;
			}
		}
		return $id;
	}
}

if ( ! function_exists( 'planty_get_edited_post_type' ) ) {
	/**
	 * The a post type of the edited post
	 * or empty string if a current screen is not an edit page.
	 *
	 * @return string The post type of the edited post.
	 */
	function planty_get_edited_post_type() {
		$pt = '';
		if ( is_admin() ) {
			$url = planty_get_current_url();
			if ( strpos( $url, 'post.php' ) !== false ) {
				if ( in_array( planty_get_value_gp( 'action' ), array( 'edit', 'elementor' ) ) ) {
					$id = planty_get_value_gp( 'post' );
					if ( 0 < $id ) {
						$post = get_post( (int) $id );
						if ( is_object( $post ) && ! empty( $post->post_type ) ) {
							$pt = $post->post_type;
						}
					}
				}
			} elseif ( strpos( $url, 'post-new.php' ) !== false ) {
				$pt = planty_get_value_gp( 'post_type' );
			}
		}
		return $pt;
	}
}

if ( ! function_exists( 'planty_is_post_edit' ) ) {
	/**
	 * Detect if a current mode is "Edit post" or "Site editor".
	 *
	 * @return bool Return true if current mode is "Edit post" or "Edit site".
	 */
	function planty_is_post_edit() {
		return ( planty_check_url( 'post.php' ) && ! empty( $_GET['action'] ) && $_GET['action'] == 'edit')
				||
				planty_check_url( 'post-new.php' )
				||
				( planty_check_url( '/block-renderer/trx-addons/' ) && ! empty( $_GET['context'] ) && $_GET['context'] == 'edit' )
				||
				( planty_check_url( 'admin.php' ) && ! empty( $_GET['page'] ) && $_GET['page'] == 'gutenberg-edit-site' )
				||
				( planty_check_url( 'site-editor.php' ) && ! empty( $_GET['postType'] ) )	// || $_GET['postType'] == 'wp_template' ) )
				||
				planty_check_url( 'widgets.php' );
	}
}

if ( ! function_exists( 'planty_is_singular' ) ) {
	/**
	 * Detect if the current page is a singular page of the specified post type.
	 *
	 * @param string $type Optional. A post type to detect a singular page.
	 *
	 * @return bool        true if the current mode is singular page.
	 */
	function planty_is_singular( $type = '' ) {
		global $wp_query;
		return apply_filters( 'planty_filter_is_singular', ! empty( $wp_query->queried_object->ID ) && is_singular( $type ), $type );
	}
}

if ( ! function_exists( 'planty_is_single' ) ) {
	/**
	 * Detect if the current post is a single post.
	 *
	 * @return bool  true if the current mode is a single post.
	 */
	function planty_is_single() {
		global $wp_query;
		return apply_filters( 'planty_filter_is_single', ! empty( $wp_query->queried_object->ID ) && is_single() );
	}
}

if ( ! function_exists( 'planty_add_seo_itemprops' ) ) {
	/**
	 * Add SEO params to the article tag.
	 */
	function planty_add_seo_itemprops() {
		if ( planty_is_on( planty_get_theme_option( 'seo_snippets' ) ) ) {
			?>
			itemscope="itemscope" 
			itemprop="<?php
				if ( 'page' == get_post_type() ) {
					echo 'mainEntityOfPage';
				} else {
					echo 'articleBody';					
				}
			?>" 
			itemtype="<?php echo esc_attr( planty_get_protocol( true ) ); ?>//schema.org/<?php echo esc_attr( planty_get_markup_schema() ); ?>" 
			itemid="<?php echo esc_url( get_the_permalink() ); ?>"
			content="<?php the_title_attribute( '' ); ?>"
			<?php
		}
	}
}

if ( ! function_exists( 'planty_add_seo_snippets' ) ) {
	/**
	 * Add SEO meta to the post from the template 'templates/seo.php'
	 */
	function planty_add_seo_snippets() {
		if ( planty_is_on( planty_get_theme_option( 'seo_snippets' ) ) ) {
			get_template_part( apply_filters( 'planty_filter_get_template_part', 'templates/seo' ) );
		}
	}
}


/* Menu utilities
-------------------------------------------------------------------------------- */

if ( ! function_exists( 'planty_get_nav_menu' ) ) {
	/**
	 * Return a html layout of the navigation menu, specified by the location or the menu slug.
	 *
	 * @param string $location      Optional. A menu location. Default is an empty string.
	 * @param string $menu          Optional. A menu slug. Default is an empty string.
	 * @param int $depth            Optional. A maximum depth to build menu layout. Default is 0 (all).
	 * @param bool|object $custom_walker  Optional. An object with a custom walker used instead a standard menu builder
	 * 									  or true to use a custom walker from the theme with class 'planty_custom_menu_walker'.
	 * 									  Default is false.
	 *
	 * @return string               A menu layout or an error message.
	 */
	function planty_get_nav_menu( $location = '', $menu = '', $depth = 0, $custom_walker = false ) {
		static $list = array();
		$slug = $location . '_' . $menu;
		if ( empty( $list[ $slug ] ) ) {
			$list[ $slug ] = esc_html__( 'You are trying to use a menu inserted in himself!', 'planty' );
			$args          = array(
								'menu'            => empty( $menu ) || 'default' == $menu || planty_is_inherit( $menu ) ? '' : $menu,
								'container'       => '',
								'container_class' => '',
								'container_id'    => '',
								'items_wrap'      => '<ul id="%1$s" class="%2$s">%3$s</ul>',
								'menu_class'      => 'sc_layouts_menu_nav ' . ( ! empty( $location ) ? esc_attr( $location ) : 'menu_main' ) . '_nav',
								'menu_id'         => ( ! empty( $location ) ? esc_attr( $location ) : 'menu_main' ),
								'echo'            => false,
								'fallback_cb'     => '',
								'before'          => '',
								'after'           => '',
								'link_before'     => planty_get_theme_setting( 'wrap_menu_items_with_span' ) ? '<span>' : '',
								'link_after'      => planty_get_theme_setting( 'wrap_menu_items_with_span' ) ? '</span>' : '',
								'depth'           => $depth,
							);
			if ( ! empty( $location ) ) {
				$args['theme_location'] = $location;
			}
			if ( $custom_walker ) {
				if ( is_object( $custom_walker ) ) {
					$args['walker'] = $custom_walker;
				} else if ( $custom_walker === true && class_exists( 'planty_custom_menu_walker' ) ) {
					$args['walker'] = new planty_custom_menu_walker;
				}
			}
			$list[ $slug ] = wp_nav_menu( apply_filters( 'planty_filter_get_nav_menu_args', $args ) );
		}
		return apply_filters( 'planty_filter_get_nav_menu', $list[ $slug ], $location, $menu );
	}
}

if ( ! function_exists( 'planty_remove_empty_spaces_between_menu_items' ) ) {

    add_action( 'wp_nav_menu', 'planty_remove_empty_spaces_between_menu_items', 98, 2 );

	/**
	 * Remove empty spaces between menu items in the menu layout.
	 *
	 * Hooks:
	 *
	 * add_action( 'wp_nav_menu', 'planty_remove_empty_spaces_between_menu_items', 98, 2 );
	 *
	 * @param string $html  Optional. A string with a menu layout.
	 * @param array $args   Optional. A menu built arguments.
	 *
	 * @return string       A clean string.
	 */
	function planty_remove_empty_spaces_between_menu_items( $html = '', $args = array() ) {
		return preg_replace(
							array( "/>[\r\n\s]*<li/", "/>[\r\n\s]*<\\/ul>/" ),
							array( "><li", "></ul>" ),
							$html
							);
	}
}

if ( ! function_exists( 'planty_remove_empty_menu_items' ) ) {

	add_action( 'wp_nav_menu', 'planty_remove_empty_menu_items', 99, 2 );

	/**
	 * Remove empty items from the menu layout.
	 *
	 * Hooks:
	 *
	 * add_action( 'wp_nav_menu', 'planty_remove_empty_menu_items', 99, 2 );
	 *
	 * @param string $html  Optional. A string with a menu layout.
	 * @param array $args   Optional. A menu built arguments.
	 *
	 * @return string       A clean string.
	 */
	function planty_remove_empty_menu_items( $html = '', $args = array() ) {
		return planty_get_theme_setting( 'remove_empty_menu_items' )
					? preg_replace(
							"/<li[^>]*>[\r\n\s]*<a[^>]*>[\r\n\s]*(<span>[\r\n\s]*<\\/span>[\r\n\s]*)?<\\/a>[\r\n\s]*<\\/li>/",
							"",
							$html
							)
					: $html;
	}
}


/* Query manipulations
-------------------------------------------------------------------------------- */

//
if ( ! function_exists( 'planty_new_main_query' ) ) {
	/**
	 * Make a new main query - it used insted a default query if the current page
	 * is a blog template and a posts archive will be queried insted a single page.
	 *
	 * @param array $args A new query arguments.
	 */
	function planty_new_main_query( $args ) {
		$args = array_merge( array(
			'post_ids'            => '',
			'post_type'           => '',
			'category'            => '',
			'posts_per_page'      => '',
			'page'                => 1
		), $args );
		$query_args  = array();
		if ( ! empty( $args['post_type'] ) || ! empty( $args['category'] ) ) {
			$query_args  = planty_query_add_posts_and_cats( $query_args, $args['post_ids'], $args['post_type'], $args['category'] );
		}
		if ( $args[ 'page' ] > 1 ) {
			$query_args['paged']               = $args[ 'page' ];
			$query_args['ignore_sticky_posts'] = true;
		}
		if ( 0 != (int) $args[ 'posts_per_page' ] ) {
			$query_args['posts_per_page'] = (int) $args[ 'posts_per_page' ];
		}
		if ( count( $query_args ) > 0 ) {
			$query_args['post_status'] = current_user_can( 'read_private_pages' ) && current_user_can( 'read_private_posts' )
											? array( 'publish', 'private' )
											: 'publish';
			$GLOBALS['wp_the_query']->query( $query_args );
			$GLOBALS['wp_query'] = $GLOBALS['wp_the_query'];
		}
	}
}

if ( ! function_exists( 'planty_query_add_sort_order' ) ) {
	/**
	 * Add sorting parameter to the query arguments.
	 *
	 * @param array $args      A query arguments.
	 * @param string $orderby  Optional. A sort order. Default is 'date'.
	 * @param string $order    Optional. A sort direction. Default is 'desc'.
	 *
	 * @return array           A query arguments with sorting parameters
	 */
	function planty_query_add_sort_order( $args, $orderby = 'date', $order = 'desc' ) {
		if ( ! empty( $orderby ) && ( empty( $args['orderby'] ) || 'none' != $orderby ) ) {
			$q          = apply_filters( 'planty_filter_add_sort_order', array(), $orderby, $order );
			$q['order'] = 'asc' == $order ? 'asc' : 'desc';
			if ( empty( $q['orderby'] ) ) {
				if ( 'none' == $orderby ) {
					$q['orderby'] = 'none';
				} elseif ( 'ID' == $orderby ) {
					$q['orderby'] = 'ID';
				} elseif ( 'comments' == $orderby ) {
					$q['orderby'] = 'comment_count';
				} elseif ( 'title' == $orderby || 'alpha' == $orderby ) {
					$q['orderby'] = 'title';
				} elseif ( 'rand' == $orderby || 'random' == $orderby ) {
					$q['orderby'] = 'rand';
				} else {
					$q['orderby'] = 'post_date';
				}
			}
			foreach ( $q as $mk => $mv ) {
				if ( is_array( $args ) ) {
					$args[ $mk ] = $mv;
				} else {
					$args->set( $mk, $mv );
				}
			}
		}
		return apply_filters( 'planty_filter_add_sort_order_args', $args, $orderby, $order );
	}
}

if ( ! function_exists( 'planty_query_add_posts_and_cats' ) ) {
	/**
	 * Add a post type and a posts list or a categories list to the query arguments.
	 *
	 * @param array  $args       A query arguments.
	 * @param string $ids        Optional. A comma separated string with a post IDs. Default is empty string.
	 * @param string $post_type  Optional. A post type to insert to the query arguments. Default is 'post'.
	 * @param string|array $cat  Optional. A category terms to add to the meta query section.
	 *                           A comma separated string or an array are commited.
	 * @param string $taxonomy   Optional. A taxonomy slug. If empty - using autodetect by the specified post type.
	 *
	 * @return array             A query arguments with posts and cats added.
	 */
	function planty_query_add_posts_and_cats( $args, $ids = '', $post_type = '', $cat = '', $taxonomy = '' ) {
		if ( ! empty( $ids ) ) {
			$args['post_type'] = empty( $args['post_type'] )
									? ( empty( $post_type ) ? array( 'post', 'page' ) : $post_type )
									: $args['post_type'];
			$args['post__in']  = explode( ',', str_replace( ' ', '', $ids ) );
			if ( empty( $args['orderby'] ) || 'none' == $args['orderby'] ) {
				$args['orderby'] = 'post__in';
				if ( isset( $args['order'] ) ) {
					unset( $args['order'] );
				}
			}
		} else {
			$args['post_type'] = empty( $args['post_type'] )
									? ( empty( $post_type ) ? 'post' : $post_type )
									: $args['post_type'];
			$post_type         = is_array( $args['post_type'] ) ? $args['post_type'][0] : $args['post_type'];
			if ( ! empty( $cat ) ) {
				$cats = ! is_array( $cat ) ? explode( ',', $cat ) : $cat;
				if ( empty( $taxonomy ) ) {
					$taxonomy = planty_get_post_type_taxonomy( $post_type );
				}
				if ( 'category' == $taxonomy ) {              // Add standard categories
					if ( is_array( $cats ) && count( $cats ) > 1 ) {
						$cats_ids = array();
						foreach ( $cats as $c ) {
							$c = trim( $c );
							if ( empty( $c ) ) {
								continue;
							}
							if ( 0 == (int) $c ) {
								$cat_term = get_term_by( 'slug', $c, $taxonomy, OBJECT );
								if ( $cat_term ) {
									$c = $cat_term->term_id;
								}
							}
							if ( 0 == $c ) {
								continue;
							}
							$cats_ids[] = (int) $c;
							$children   = get_categories(
								array(
									'type'         => $post_type,
									'child_of'     => $c,
									'hide_empty'   => 0,
									'hierarchical' => 0,
									'taxonomy'     => $taxonomy,
									'pad_counts'   => false,
								)
							);
							if ( is_array( $children ) && count( $children ) > 0 ) {
								foreach ( $children as $c ) {
									if ( ! in_array( (int) $c->term_id, $cats_ids ) ) {
										$cats_ids[] = (int) $c->term_id;
									}
								}
							}
						}
						if ( count( $cats_ids ) > 0 ) {
							$args['category__in'] = $cats_ids;
						}
					} else {
						if ( 0 < (int) $cat ) {
							$args['cat'] = (int) $cat;
						} else {
							$args['category_name'] = $cat;
						}
					}
				} else {                                    // Add custom taxonomies
					if ( ! isset( $args['tax_query'] ) ) {
						$args['tax_query'] = array();
					}
					$args['tax_query']['relation'] = 'AND';
					$args['tax_query'][]           = array(
						'taxonomy'         => $taxonomy,
						'include_children' => true,
						'field'            => (int) $cats[0] > 0 ? 'id' : 'slug',
						'terms'            => $cats,
					);
				}
			}
		}
		return $args;
	}
}

if ( ! function_exists( 'planty_query_add_filters' ) ) {
	/**
	 * Add filters (meta parameters) to the query arguments.
	 *
	 * @param array       $args     A query arguments.
	 * @param array|false $filters  Optional. A filters to add a meta queries to the arguments.
	 *                              'thumbs', 'video', 'audio', 'gallery' are allowed as the filter values.
	 *
	 * @return array                A query arguments with filters added.
	 */
	function planty_query_add_filters( $args, $filters = false ) {
		if ( ! empty( $filters ) ) {
			if ( ! is_array( $filters ) ) {
				$filters = array( $filters );
			}
			foreach ( $filters as $v ) {
				$found = false;
				if ( 'thumbs' == $v ) {                                                      // Filter with meta_query
					if ( ! isset( $args['meta_query'] ) ) {
						$args['meta_query'] = array();
					} else {
						for ( $i = 0; $i < count( $args['meta_query'] ); $i++ ) {
							if ( $args['meta_query'][ $i ]['meta_filter'] == $v ) {
								$found = true;
								break;
							}
						}
					}
					if ( ! $found ) {
						$args['meta_query']['relation'] = 'AND';
						if ( 'thumbs' == $v ) {
							$args['meta_query'][] = array(
								'meta_filter' => $v,
								'key'         => '_thumbnail_id',
								'value'       => false,
								'compare'     => '!=',
							);
						}
					}
				} elseif ( in_array( $v, array( 'video', 'audio', 'gallery' ) ) ) {          // Filter with tax_query
					if ( ! isset( $args['tax_query'] ) ) {
						$args['tax_query'] = array();
					} else {
						for ( $i = 0; $i < count( $args['tax_query'] ); $i++ ) {
							if ( $args['tax_query'][ $i ]['tax_filter'] == $v ) {
								$found = true;
								break;
							}
						}
					}
					if ( ! $found ) {
						$args['tax_query']['relation'] = 'AND';
						if ( 'video' == $v ) {
							$args['tax_query'][] = array(
								'tax_filter' => $v,
								'taxonomy'   => 'post_format',
								'field'      => 'slug',
								'terms'      => array( 'post-format-video' ),
							);
						} elseif ( 'audio' == $v ) {
							$args['tax_query'] = array(
								'tax_filter' => $v,
								'taxonomy'   => 'post_format',
								'field'      => 'slug',
								'terms'      => array( 'post-format-audio' ),
							);
						} elseif ( 'gallery' == $v ) {
							$args['tax_query'] = array(
								'tax_filter' => $v,
								'taxonomy'   => 'post_format',
								'field'      => 'slug',
								'terms'      => array( 'post-format-gallery' ),
							);
						}
					}
				}
			}
		}
		return $args;
	}
}




/* Widgets utils
------------------------------------------------------------------------------------- */

if ( ! function_exists( 'planty_create_widgets_area' ) ) {
	/**
	 * Create a widgets area - a tag div with a class 'widgets_area' and a widgets set
	 * form the specified by name widgets area.
	 *
	 * @param string $name         A widgets area name to inserts widgets from.
	 * @param string $add_classes  Optional. An additional classes list to add to the wrapper.
	 */
	function planty_create_widgets_area( $name, $add_classes = '' ) {
		$widgets_name = planty_get_theme_option( $name );
		if ( ! planty_is_off( $widgets_name ) && is_active_sidebar( $widgets_name ) ) {
			planty_storage_set( 'current_sidebar', $name );
			ob_start();
			dynamic_sidebar( $widgets_name );
			$out = trim( ob_get_contents() );
			ob_end_clean();
			if ( ! empty( $out ) ) {
				$out          = preg_replace( "/<\/aside>[\r\n\s]*<aside/", '</aside><aside', $out );
				$need_columns = strpos( $out, 'columns_wrap' ) === false && apply_filters( 'planty_filter_widgets_area_need_columns', 'footer' == $name, $name, $out );
				if ( $need_columns ) {
					$columns = apply_filters( 'planty_filter_widgets_area_columns', min( 4, max( 1, planty_tags_count( $out, 'aside' ) ) ), $name );
					$out     = preg_replace( '/<aside([^>]*)class="widget/', '<aside$1class="column-1_' . esc_attr( $columns ) . ' widget', $out );
				}
				?>
				<div class="<?php echo esc_attr( $name ); ?> <?php echo esc_attr( $name ); ?>_wrap widget_area">
					<?php do_action( 'planty_action_before_sidebar_wrap' ); ?>
					<div class="<?php echo esc_attr( $name ); ?>_inner <?php echo esc_attr( $name ); ?>_inner widget_area_inner">
						<?php
						do_action( 'planty_action_before_sidebar' );
						planty_show_layout(
							$out,
							true == $need_columns ? '<div class="columns_wrap">' : '',
							true == $need_columns ? '</div>' : ''
						);
						do_action( 'planty_action_after_sidebar' );
						?>
					</div>
					<?php do_action( 'planty_action_after_sidebar_wrap' ); ?>
				</div>
				<?php
			}
		}
	}
}

if ( ! function_exists( 'planty_sidebar_present' ) ) {
	/**
	 * Check if a sidebar is selected for the current page in the Theme Options
	 * or in the Page Options or inherits from the parent mode.
	 *
	 * @return bool  Return true if a sidebar is present on the current page.
	 */
	function planty_sidebar_present() {
		global $wp_query;
		$sidebar_position = planty_get_theme_option( 'sidebar_position' );
		$sidebar_type     = planty_get_theme_option( 'sidebar_type' );
		$sidebar_name     = planty_get_theme_option( 'sidebar_widgets' );
		return apply_filters(
			'planty_filter_sidebar_present',
			! planty_is_off( $sidebar_position )
				&& (
						( 'default' == $sidebar_type && ! planty_is_off( $sidebar_name ) && is_active_sidebar( $sidebar_name ) )
						||
						( 'custom' == $sidebar_type && planty_is_layouts_available() )
					)
				&& ! is_404()
				&& ( ! is_search() || $wp_query->found_posts > 0 )
		);
	}
}

if ( ! function_exists( 'planty_get_content_width' ) ) {
	/**
	 * Calculate a content width: return a page_width if a sidebar is not present on the current page
	 * or a page_width - sidebar_width - sidebar_gap if a sidebar is present on the current page.
	 *
	 * @return int  A content width (in pixels).
	 */
	function planty_get_content_width() {
		$pg_width = apply_filters( 'planty_filter_content_width', (int)planty_get_theme_option( 'page_width' ) );
		$sb_width = 0;
		$sb_gap   = 0;
		if ( planty_sidebar_present() ) {
			$planty_sidebar_type = planty_get_theme_option( 'sidebar_type' );
			if ( 'custom' == $planty_sidebar_type && ! planty_is_layouts_available() ) {
				$planty_sidebar_type = 'default';
			}
			if ( 'default' == $planty_sidebar_type ) {
				$planty_sidebar_name = planty_get_theme_option( 'sidebar_widgets' );
				if ( is_active_sidebar( $planty_sidebar_name ) ) {
					$sb_width = planty_get_theme_option( 'sidebar_width' );
					$sb_gap   = planty_get_theme_option( 'sidebar_gap' );
				}
			} else {
				$sb_width = planty_get_theme_option( 'sidebar_width' );
				$sb_gap   = planty_get_theme_option( 'sidebar_gap' );
			}
		}
		return $pg_width - ( $sb_width > 0 ? $sb_width + $sb_gap : 0 );
	}
}




/* Inline styles and scripts
------------------------------------------------------------------------------------- */

if ( ! function_exists( 'planty_add_inline_css_class' ) ) {
	/**
	 * Add inline styles (only rules delimited with ';') to the current page inline css
	 * and return a class name to use this styles in the layout.
	 *
	 * @param string $css     CSS rules to add to the inline css.
	 * @param string $suffix  Optional. A suffix for the generated class.
	 *                        If started with ':' - used as pseudoclass after the class name,
	 *                        else - used as child class name to append after the generated class name.
	 *
	 * @return string         A generated class name to use it inside the html layout
	 */
	function planty_add_inline_css_class( $css, $suffix = '' ) {
		$class_name = planty_generate_id( 'planty_inline_' );
		planty_add_inline_css(
			sprintf(
				'.%s%s{%s}',
				$class_name,
				! empty( $suffix )
					? ( substr( $suffix, 0, 1 ) != ':' ? ' ' : '' ) . str_replace( ',', ",.{$class_name} ", $suffix )
					: '',
				$css
			)
		);
		return $class_name;
	}
}

if ( ! function_exists( 'planty_add_inline_css' ) ) {
	/**
	 * Add inline styles (rules with selector) to the current page inline css.
	 *
	 * @param string $css     CSS rules with selector to add to the inline css.
	 */
	function planty_add_inline_css( $css ) {
		if ( function_exists( 'trx_addons_add_inline_css' ) ) {
			trx_addons_add_inline_css( $css );
		} else {
			planty_storage_concat( 'inline_styles', $css );
		}
	}
}

if ( ! function_exists( 'planty_get_inline_css' ) ) {
	/**
	 * Return an inline css to append it to the html code of the current page
	 */
	function planty_get_inline_css() {
		return wp_doing_ajax() && function_exists( 'trx_addons_get_inline_css' )
					? trx_addons_get_inline_css()
					: planty_storage_get( 'inline_styles' );
	}
}




/* Optimize loading styles and scripts
------------------------------------------------------------------------------------- */
if ( ! function_exists( 'planty_enqueue_styles' ) ) {
	/**
	 * Enqueue a list of styles
	 *
	 * @param array $list - styles to enqueue
	 */
	function planty_enqueue_styles( $list, $sc ) {
		if ( is_array( $list ) ) {
			foreach( $list as $handle => $data ) {
				$lib_url = planty_get_file_url( $data['src'] );
				if ( $lib_url ) {
					wp_enqueue_style(
						$handle,
						$lib_url,
						! empty( $data['deps'] ) ? (array)$data['deps'] : array(),
						! empty( $data['ver'] ) ? $data['ver'] : null,
						! empty( $data['media'] ) ? planty_media_for_load_css_responsive( str_replace( '_', '-', $sc ), $data['media'] ) : 'all'
					);
				}
			}
		}
	}
}

if ( ! function_exists( 'planty_enqueue_scripts' ) ) {
	/**
	 * Enqueue a list of scripts
	 *
	 * @param array $list - scripts to enqueue
	 */
	function planty_enqueue_scripts( $list, $sc ) {
		if ( is_array( $list ) ) {
			foreach( $list as $handle => $data ) {
				$lib_url = planty_get_file_url( $data['src'] );
				if ( $lib_url ) {
					wp_enqueue_script(
						$handle,
						$lib_url,
						! empty( $data['deps'] ) ? (array)$data['deps'] : array(),
						! empty( $data['ver'] ) ? $data['ver'] : null,
						isset( $data['footer'] ) ? $data['footer'] : true
					);
				}
			}
		}
	}
}


if ( ! function_exists( 'planty_enqueue_optimized' ) ) {
	/**
	 * Enqueue styles and scripts only if a shortcode (widget) is used on the page or 'Optimize CSS and JS loading' option is off
	 * 
	 * @param string $sc - shortcode (widget) slug
	 * @param bool $force - force enqueue styles and scripts
	 * @param array $args - arguments with styles and scripts to enqueue
	 */
	function planty_enqueue_optimized( $sc, $force, $args ) {
		static $loaded = array();
		if ( empty( $loaded[ $sc ] ) && (
			current_action() == 'wp_enqueue_scripts' && planty_need_frontend_scripts( ! empty( $args['slug'] ) ? $args['slug'] :  $sc )
			||
			current_action() != 'wp_enqueue_scripts' && $force === true
			)
		) {
			$loaded[ $sc ] = true;
			if ( ! isset( $args['need'] ) || $args['need'] ) {
				if ( ! empty( $args['css'] ) ) {
					planty_enqueue_styles( $args['css'], $sc );
				}
				if ( ! empty( $args['js'] ) ) {
					planty_enqueue_scripts( $args['js'], $sc );
				}
				if ( ! empty( $args['callback'] ) ) {
					$args['callback']();
				}
			}
		}
	}
}


if ( ! function_exists( 'planty_enqueue_optimized_responsive' ) ) {
	/**
	 * Enqueue responsive styles only if a shortcode (widget) is used on the page or 'Optimize CSS and JS loading' option is off
	 * 
	 * @param string $sc - shortcode (widget) slug
	 * @param bool $force - force enqueue styles and scripts
	 * @param array $args - arguments with styles and scripts to enqueue
	 */
	function planty_enqueue_optimized_responsive( $sc, $force, $args ) {
		static $loaded = array();
		if ( empty( $loaded[ $sc ] ) && (
			current_action() == 'wp_enqueue_scripts' && planty_need_frontend_scripts( ! empty( $args['slug'] ) ? $args['slug'] :  $sc )
			||
			current_action() != 'wp_enqueue_scripts' && $force === true
			)
		) {
			$loaded[ $sc ] = true;
			if ( ! isset( $args['need'] ) || $args['need'] ) {
				if ( ! empty( $args['css'] ) ) {
					planty_enqueue_styles( $args['css'], $sc );
				}
				if ( ! empty( $args['js'] ) ) {
					planty_enqueue_scripts( $args['js'], $sc );
				}
				if ( ! empty( $args['callback'] ) ) {
					$args['callback']();
				}
			}
		}
	}
}




/* Date & Time
----------------------------------------------------------------------------------------------------- */

if ( ! function_exists( 'planty_get_date' ) ) {
	/**
	 * Return the date in the specified format of in the human-friendly date difference (if a format is omitted).
	 *
	 * @param string $dt      Optional. Date to format. If omitted - a post date is used.
	 * @param string $format  Optional. A date format. If omitted - the site setting 'date_format' is used.
	 *
	 * @return string         A formatted date.
	 */
	function planty_get_date( $dt = '', $format = '' ) {
		if ( '' == $dt ) {
			$dt = get_the_time( 'U' );
		}
		if ( date( 'U' ) - $dt > intval( planty_get_theme_option( 'time_diff_before' ) ) * 24 * 3600 ) {
			$dt = date_i18n( '' == $format ? get_option( 'date_format' ) : $format, $dt );
		} else {
			// Translators: Add the human-friendly date difference
			$dt = sprintf( esc_html__( '%s ago', 'planty' ), human_time_diff( $dt, current_time( 'timestamp' ) ) );
		}
		return $dt;
	}
}


/* Lazy load images
----------------------------------------------------------------------------------------------------- */

if ( ! function_exists( 'planty_lazy_load_off' ) ) {
	/**
	 * Disable a lazy load behaviour for rest images on the current page
	 */
	function planty_lazy_load_off() {
		if ( ! planty_lazy_load_is_off() ) {
			add_filter( 'wp_lazy_loading_enabled', 'planty_lazy_load_disabled' );
		}
	}
}

if ( ! function_exists( 'planty_lazy_load_disabled' ) ) {
	/**
	 * Always return false to disable lazy load behaviour.
	 *
	 * Hooks:
	 *
	 * add_filter( 'wp_lazy_loading_enabled', 'planty_lazy_load_disabled' );
	 *
	 * @return bool  Always return false.
	 */
	function planty_lazy_load_disabled() {
		return false;
	}
}

if ( ! function_exists( 'planty_lazy_load_on' ) ) {
	/**
	 * Enable a lazy load behaviour for rest images on the current page
	 */
	function planty_lazy_load_on() {
		if ( planty_lazy_load_is_off() ) {
			remove_action( 'wp_lazy_loading_enabled', 'planty_lazy_load_disabled' );	// Is equal to rf
		}
	}
}

if ( ! function_exists( 'planty_lazy_load_is_off' ) ) {
	/**
	 * Return a current state of the lazy load behaviour
	 *
	 * @return bool  A current lazy load state
	 */
	function planty_lazy_load_is_off() {
		return has_filter( 'wp_lazy_loading_enabled', 'planty_lazy_load_disabled' );
	}
}



/* Structured Data
----------------------------------------------------------------------------------------------------- */

if ( ! function_exists( 'planty_get_markup_schema' ) ) {
	/**
	 * Return a structured data markup schema for the current page.
	 *
	 * @return string  A schema of the current page.
	 */
	function planty_get_markup_schema() {
		if ( planty_is_single() ) {                                        // Is single post
			$type = 'Article';
		} elseif ( is_home() || is_archive() || is_category() ) {    // Is blog home, archive or category
			$type = 'Blog';
		} elseif ( is_front_page() ) {                                // Is static front page
			$type = 'Website';
		} else { // Is a general page
			$type = 'WebPage';
		}
		return $type;
	}
}

if ( ! function_exists( 'planty_get_privacy_text' ) ) {
	/**
	 * Return a text for the Privacy Policy checkbox.
	 *
	 * @return string  A label text.
	 */
	function planty_get_privacy_text() {
		return apply_filters( 'planty_filter_privacy_text', wp_kses( trim( planty_get_theme_option( 'privacy_text' ) ), 'planty_kses_content' ) );
	}
}

if ( ! function_exists( 'planty_add_privacy_page_link' ) ) {
	/**
	 * Adds the Privacy Policy page link to the Privacy text
	 * 
	 * @hooked filter planty_filter_privacy_text
	 * 
	 * @param string $text - the text to modify
	 *
	 * @return string  A label text.
	 */
	function planty_add_privacy_page_link( $text ) {
		if ( ! empty( $text ) ) {
			$page = get_option( 'wp_page_for_privacy_policy' );
			if ( ! empty( $page ) ) {
				$text .=  ( ! in_array( substr( $text, -1 ), array( '.', '!' ) ) ? '.' : '' )
							. ' '
							// Translators: Add the url to the Privacy Policy page
							. sprintf(
								__( 'For further details on handling user data, see our %s.', 'planty' ),
								'<a href="' . esc_url( get_permalink( $page ) ) . '" target="_blank">' . __( 'Privacy Policy', 'planty' ) . '</a>'
							);
			}
		}
		return $text;
	}
}


/* Common core utilities
----------------------------------------------------------------------------------------------------- */

if ( ! function_exists( 'planty_kses_allowed_html' ) ) {
	add_filter( 'wp_kses_allowed_html', 'planty_kses_allowed_html', 10, 2 );
	/**
	 * Return a theme-specific tags list with allowed attributes for using in the wp_kses() calls
	 * ( if $context is equal to 'planty_kses_content' or 'trx_addons_kses_content' ).
	 *
	 * Hooks:
	 *
	 * add_filter( 'wp_kses_allowed_html', 'planty_kses_allowed_html', 10, 2 );
	 *
	 * @param array $tags      A default tags list.
	 * @param string $context  A context name.
	 *
	 * @return array           The modified tags list.
	 */
	function planty_kses_allowed_html( $tags, $context ) {
		if ( in_array( $context, array( 'planty_kses_content', 'trx_addons_kses_content' ) ) ) {
			$tags = array( 
				'h1'     => array( 'id' => array(), 'class' => array(), 'style' => array(), 'title' => array(), 'align' => array() ),
				'h2'     => array( 'id' => array(), 'class' => array(), 'style' => array(), 'title' => array(), 'align' => array() ),
				'h3'     => array( 'id' => array(), 'class' => array(), 'style' => array(), 'title' => array(), 'align' => array() ),
				'h4'     => array( 'id' => array(), 'class' => array(), 'style' => array(), 'title' => array(), 'align' => array() ),
				'h5'     => array( 'id' => array(), 'class' => array(), 'style' => array(), 'title' => array(), 'align' => array() ),
				'h6'     => array( 'id' => array(), 'class' => array(), 'style' => array(), 'title' => array(), 'align' => array() ),
				'p'      => array( 'id' => array(), 'class' => array(), 'style' => array(), 'title' => array(), 'align' => array() ),
				'span'   => array( 'id' => array(), 'class' => array(), 'style' => array(), 'title' => array() ),
				'div'    => array( 'id' => array(), 'class' => array(), 'style' => array(), 'title' => array(), 'align' => array() ),
				'a'      => array( 'id' => array(), 'class' => array(), 'style' => array(), 'title' => array(), 'href' => array(), 'target' => array() ),
				'b'      => array( 'id' => array(), 'class' => array(), 'style' => array(), 'title' => array() ),
				'strong' => array( 'id' => array(), 'class' => array(), 'title' => array() ),
				'i'      => array( 'id' => array(), 'class' => array(), 'style' => array(), 'title' => array() ),
				'em'     => array( 'id' => array(), 'class' => array(), 'title' => array() ),
				'img'    => array( 'id' => array(), 'class' => array(), 'style' => array(), 'title' => array(), 'src' => array(), 'width' => array(), 'height' => array(), 'alt' => array() ),
				'br'     => array( 'clear' => array() ),
				'u'      => array( 'id' => array(), 'class' => array(), 'style' => array(), 'title' => array() ),
				's'      => array( 'id' => array(), 'class' => array(), 'style' => array(), 'title' => array() ),
				'ins'    => array( 'id' => array(), 'class' => array(), 'style' => array(), 'title' => array() ),
				'del'    => array( 'id' => array(), 'class' => array(), 'style' => array(), 'title' => array(), 'aria-hidden' => array() ),
				'pre'    => array( 'id' => array(), 'class' => array(), 'style' => array(), 'title' => array() ),
				'tt'     => array( 'id' => array(), 'class' => array(), 'style' => array(), 'title' => array() ),
				'bdi'    => array( 'id' => array(), 'class' => array(), 'style' => array(), 'title' => array(), 'dir' => array() ),
				'small'  => array( 'id' => array(), 'class' => array(), 'style' => array(), 'title' => array() ),
				'big'    => array( 'id' => array(), 'class' => array(), 'style' => array(), 'title' => array() ),
				'abbr'   => array( 'id' => array(), 'class' => array(), 'style' => array(), 'title' => array() ),
				'ul'     => array( 'id' => array(), 'class' => array(), 'style' => array(), 'title' => array(), 'type' => array() ),
				'ol'     => array( 'id' => array(), 'class' => array(), 'style' => array(), 'title' => array(), 'type' => array(), 'start' => array() ),
				'li'     => array( 'id' => array(), 'class' => array(), 'style' => array(), 'title' => array(), 'type' => array(), 'start' => array() ),
			);
		}
		return $tags;
	}
}



/* AJAX utilities
----------------------------------------------------------------------------------------------------- */

if ( ! function_exists( 'planty_verify_nonce' ) ) {
	/**
	 * Verify a nonce and exit if it's not valid.
	 *
	 * @param string $nonce  Optional. A nonce string ( from the http query ).
	 * @param string $mask   Optional. A mask string, used while a nonce was created.
	 * @param bool $die      Optional. If true - exit if the nonce is not valid.
	 */
	function planty_verify_nonce( $nonce = 'nonce', $mask = '', $die = true ) {
		$rez = true;
		if ( empty( $mask ) ) {
			$mask = admin_url('admin-ajax.php');
		}
		if ( ! wp_verify_nonce( planty_get_value_gp( $nonce ), $mask ) ) {
			$rez = false;
			if ( $die ) {
				planty_forbidden();
			}
		}
		return $rez;
	}
}

if ( ! function_exists( 'planty_exit' ) ) {
	/**
	 * Exit with default code 200 (OK) and output a message with a title.
	 *
	 * @param string $message  Optional. Message to output while exit.
	 * @param string $title    Optional. Title to output while exit.
	 * @param int $code        Optional. Exit code.
	 */
	function planty_exit( $message = '', $title = '', $code = 200 ) {
		wp_die( $message, $title, array( 'response' => $code, 'exit' => empty( $message ) && empty( $title ) ) );
	}
}

if ( ! function_exists( 'planty_forbidden' ) ) {
	/**
	 * Exit with the code 403 (Forbidden) and output a message with a title.
	 *
	 * @param string $message  Optional. Message to output while exit.
	 * @param string $title    Optional. Title to output while exit.
	 */
	function planty_forbidden( $message = '', $title = '' ) {
		planty_exit( $message, $title, 403 );
	}
}

if ( ! function_exists( 'planty_ajax_response' ) ) {
	/**
	 * Send an ajax response and exit.
	 *
	 * @param mixed $response  A response to send to the client.
	 */
	function planty_ajax_response( $response ) {
		echo wp_json_encode( $response );
		wp_die( '', '', array( 'exit' => true ) );
	}
}


/* Upgrade utilities
----------------------------------------------------------------------------------------------------- */

if ( ! function_exists( 'planty_get_upgrade_url' ) ) {
	/**
	 * Return the upgrade server URL with specified parameters.
	 *
	 * @param array $params  Optional. Parameters to add to the query URL.
	 *
	 * @return string        An upgrade server URL.
	 */
	function planty_get_upgrade_url( $params = array() ) {
		$theme_folder = get_template();
		$theme_slug   = apply_filters( 'planty_filter_original_theme_slug', $theme_folder );
		$default      = array(
							'action'     => '',
							'theme_slug' => $theme_slug,
						);
		if ( ! empty( $params['action'] ) && ! in_array( $params['action'], array( 'info_skins' ) ) ) {
			$default['key']        = '';
			$default['src']        = planty_get_theme_pro_key();
			$default['theme_name'] = wp_get_theme( $theme_folder )->get( 'Name' );
			$default['domain']     = planty_remove_protocol_from_url( get_home_url(), true );
			// Allow caching all info requests to reduce server load
			if ( ! empty( $params['action'] ) && strpos( $params['action'], 'info_' ) === false ) {
				$default['rnd'] = mt_rand();
			}
		}
		$params = array_merge( $default, $params );
		return planty_add_to_url( trailingslashit( planty_storage_get( 'theme_upgrade_url' ) ) . 'upgrade.php', $params );
	}
}

if ( ! function_exists( 'planty_get_upgrade_data' ) ) {
	/**
	 * Call the upgrade servers and return an answer.
	 *
	 * @param array $params  Optional. Parameters to add to the query URL.
	 *
	 * @return array         An upgrade server response.
	 *
	 * @throws Exception     Throw an exception if the response can't be unserialized.
	 */
	function planty_get_upgrade_data( $params = array() ) {
		$url = planty_get_upgrade_url( $params );
		$result = function_exists( 'trx_addons_fgc' ) ? trx_addons_fgc( $url ) : planty_fgc( $url );
		if ( is_serialized( $result ) ) {
			try {
				$result = planty_unserialize( $result );
			} catch ( Exception $e ) {
			}
		}
		if ( ! isset( $result['error'] ) || ! isset( $result['data'] ) ) {
			$result = array(
				'error' => esc_html__( 'Unrecognized server answer!', 'planty' ),
				'data'  => ''
			);
		}
		return $result;
	}
}

if ( ! function_exists( 'planty_allow_upload_archives' ) ) {
	/**
	 * Allow WordPress to upload archives before get an upgrade package from the upgrade server.
	 */
	function planty_allow_upload_archives() {
		if ( function_exists( 'trx_addons_allow_upload_archives' ) ) {
			trx_addons_allow_upload_archives();
		}
	}
}

if ( ! function_exists( 'planty_disallow_upload_archives' ) ) {
	/**
	 * Disallow WordPress to upload archives after the upgrade package received.
	 */
	function planty_disallow_upload_archives() {
		if ( function_exists( 'trx_addons_disallow_upload_archives' ) ) {
			trx_addons_disallow_upload_archives();
		}
	}
}
