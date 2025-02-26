<?php
/**
 * Theme lists
 *
 * @package PLANTY
 * @since PLANTY 1.0
 */

// Disable direct call
if ( ! defined( 'ABSPATH' ) ) {
	exit; }


if ( ! function_exists('planty_get_not_selected_text') ) {
	/**
	 * A text of the option 'Not selected' for all tags <select>.
	 *
	 * @param string $label  A text of the caption.
	 *
	 * @return string        A filtered text of the <option>
	 */
	function planty_get_not_selected_text( $label ) {
		return apply_filters( 'planty_filter_not_selected_text',
								sprintf( apply_filters( 'planty_filter_not_selected_mask', __( '- %s -', 'planty' ) ), $label )
							);
	}
}

if ( ! function_exists( 'planty_get_list_range' ) ) {
	/**
	 * An array with range of numbers.
	 *
	 * @param int  $from             Optional. A start number of the range. Default is 1.
	 * @param int  $to               Optional. An end number of the range. Default is 2.
	 * @param bool $prepend_inherit  Optional. Need to place 'Inherit' to the start of the array. Default is false.
	 *
	 * @return array                 An associative array with a range of numbers.
	 */
	function planty_get_list_range( $from = 1, $to = 2, $prepend_inherit = false ) {
		$list = array();
		for ( $i = $from; $i <= $to; $i++ ) {
			$list[ $i ] = $i;
		}
		return $prepend_inherit ? planty_array_merge( array( 'inherit' => esc_html__( 'Inherit', 'planty' ) ), $list ) : $list;
	}
}

if ( ! function_exists( 'planty_get_list_styles' ) ) {
	/**
	 * An array with styles in the format:
	 *
	 * 1 => 'Style 1', 2 => 'Style 2', etc.
	 *
	 * @param int  $from             Optional. A start number of the range. Default is 1.
	 * @param int  $to               Optional. An end number of the range. Default is 2.
	 * @param bool $prepend_inherit  Optional. Need to place 'Inherit' to the start of the array. Default is false.
	 *
	 * @return array                 An associative array with a range of styles.
	 */
	function planty_get_list_styles( $from = 1, $to = 2, $prepend_inherit = false ) {
		$list = array();
		for ( $i = $from; $i <= $to; $i++ ) {
			// Translators: Add number to the style name 'Style 1', 'Style 2' ...
			$list[ $i ] = sprintf( esc_html__( 'Style %d', 'planty' ), $i );
		}
		return $prepend_inherit ? planty_array_merge( array( 'inherit' => esc_html__( 'Inherit', 'planty' ) ), $list ) : $list;
	}
}

if ( ! function_exists( 'planty_get_list_yesno' ) ) {
	/**
	 * An array with 'yes' and 'no' items for <select> and <radio>:
	 *
	 * 'yes' => 'Yes', 'no' => 'No'.
	 *
	 * @param bool $prepend_inherit  Optional. Need to place 'Inherit' to the start of the array. Default is false.
	 *
	 * @return array                 An associative array with 'yes' and 'no' elements.
	 */
	function planty_get_list_yesno( $prepend_inherit = false ) {
		$list = array(
			'yes' => esc_html__( 'Yes', 'planty' ),
			'no'  => esc_html__( 'No', 'planty' ),
		);
		return $prepend_inherit ? planty_array_merge( array( 'inherit' => esc_html__( 'Inherit', 'planty' ) ), $list ) : $list;
	}
}

if ( ! function_exists( 'planty_get_list_checkbox_values' ) ) {
	/**
	 * An array with 'yes' and 'no' items for checkboxes:
	 *
	 * 1 => 'Yes', 0 => 'No'.
	 *
	 * @param bool $prepend_inherit  Optional. Need to place 'Inherit' to the start of the array. Default is false.
	 *
	 * @return array                 An associative array with 'yes' and 'no' elements.
	 */
	function planty_get_list_checkbox_values( $prepend_inherit = false ) {
		$list = apply_filters(
			'planty_filter_list_checkbox_values', array(
				1         => esc_html__( 'Yes', 'planty' ),
				0         => esc_html__( 'No', 'planty' ),
			)
		);
		return $prepend_inherit ? planty_array_merge( array( 'inherit' => esc_html__( 'Inherit', 'planty' ) ), $list ) : $list;
	}
}

if ( ! function_exists( 'planty_get_list_onoff' ) ) {
	/**
	 * An array with 'on' and 'off' items for <select> and <radio>:
	 *
	 * 'on' => 'On', 'off' => 'Off'.
	 *
	 * @param bool $prepend_inherit  Optional. Need to place 'Inherit' to the start of the array. Default is false.
	 *
	 * @return array                 An associative array with 'on' and 'off' elements.
	 */
	function planty_get_list_onoff( $prepend_inherit = false ) {
		$list = array(
			'on'  => esc_html__( 'On', 'planty' ),
			'off' => esc_html__( 'Off', 'planty' ),
		);
		return $prepend_inherit ? planty_array_merge( array( 'inherit' => esc_html__( 'Inherit', 'planty' ) ), $list ) : $list;
	}
}

if ( ! function_exists( 'planty_get_list_showhide' ) ) {
	/**
	 * An array with 'show' and 'hide' items for <select> and <radio>:
	 *
	 * 'show' => 'Show', 'hide' => 'Hide'.
	 *
	 * @param bool $prepend_inherit  Optional. Need to place 'Inherit' to the start of the array. Default is false.
	 *
	 * @return array                 An associative array with 'show' and 'hide' elements.
	 */
	function planty_get_list_showhide( $prepend_inherit = false ) {
		$list = array(
			'show' => esc_html__( 'Show', 'planty' ),
			'hide' => esc_html__( 'Hide', 'planty' ),
		);
		return $prepend_inherit ? planty_array_merge( array( 'inherit' => esc_html__( 'Inherit', 'planty' ) ), $list ) : $list;
	}
}

if ( ! function_exists( 'planty_get_list_visiblehidden' ) ) {
	/**
	 * An array with 'visible' and 'hidden' items for <select> and <radio>:
	 *
	 * 'visible' => 'Visible', 'hidden' => 'Hidden'.
	 *
	 * @param bool $prepend_inherit  Optional. Need to place 'Inherit' to the start of the array. Default is false.
	 *
	 * @return array                 An associative array with 'visible' and 'hidden' elements.
	 */
	function planty_get_list_visiblehidden( $prepend_inherit = false ) {
		$list = array(
			'visible' => esc_html__( 'Visible', 'planty' ),
			'hidden'  => esc_html__( 'Hidden', 'planty' ),
		);
		return $prepend_inherit ? planty_array_merge( array( 'inherit' => esc_html__( 'Inherit', 'planty' ) ), $list ) : $list;
	}
}

if ( ! function_exists( 'planty_get_list_directions' ) ) {
	/**
	 * An array with 'horizontal' and 'vertical' items for <select> and <radio>:
	 *
	 * 'horizontal' => 'Horizontal', 'vertical' => 'Vertical'.
	 *
	 * @param bool $prepend_inherit  Optional. Need to place 'Inherit' to the start of the array. Default is false.
	 *
	 * @return array                 An associative array with 'horizontal' and 'vertical' elements.
	 */
	function planty_get_list_directions( $prepend_inherit = false ) {
		$list = array(
			'horizontal' => esc_html__( 'Horizontal', 'planty' ),
			'vertical'   => esc_html__( 'Vertical', 'planty' ),
		);
		return $prepend_inherit ? planty_array_merge( array( 'inherit' => esc_html__( 'Inherit', 'planty' ) ), $list ) : $list;
	}
}

if ( ! function_exists( 'planty_get_list_paddings' ) ) {
	/**
	 * An array with padding sizes for <select> and <radio>.
	 *
	 * @param bool $prepend_inherit  Optional. Need to place 'Inherit' to the start of the array. Default is false.
	 *
	 * @return array                 An associative array with padding sizes in format:
	 *                               'slug' => array( 'title' => 'Title', 'icon' => 'relative path to the icon' )
	 *                               This format used to show elements in the 'icons' style instead dropdown.
	 */
	function planty_get_list_paddings( $prepend_inherit = false ) {
		$list = apply_filters(
			'planty_filter_list_paddings', array(
				'none'  => array(
							'title' => esc_html__( 'No Padding', 'planty' ),
							'icon'  => 'images/theme-options/section-padding/none.png',
						),
				'small'  => array(
							'title' => esc_html__( 'Small Padding', 'planty' ),
							'icon'  => 'images/theme-options/section-padding/small.png',
						),
				'medium' => array(
							'title' => esc_html__( 'Medium Padding', 'planty' ),
							'icon'  => 'images/theme-options/section-padding/medium.png',
						),
				'large' => array(
							'title' => esc_html__( 'Large Padding', 'planty' ),
							'icon'  => 'images/theme-options/section-padding/large.png',
						),
			)
		);
		return $prepend_inherit
					? planty_array_merge(
							array( 
								'inherit' => array(
												'title' => esc_html__( 'Inherit', 'planty' ),
												'icon'  => 'images/theme-options/inherit.png',
												),
							),
							$list
						)
					: $list;
	}
}

if ( ! function_exists( 'planty_get_list_hovers' ) ) {
	/**
	 * An array with a theme specific hovers for images.
	 *
	 * @param bool $prepend_inherit  Optional. Need to place 'Inherit' to the start of the array. Default is false.
	 *
	 * @return array                 An associative array with hovers in the format: 'slug' => 'Title'.
	 */
	function planty_get_list_hovers( $prepend_inherit = false ) {
		$list = apply_filters(
			'planty_filter_list_hovers', array(
				'dots'    => esc_html__( 'Dots', 'planty' ),
				'icon'    => esc_html__( 'Icon', 'planty' ),
				'icons'   => esc_html__( 'Icons', 'planty' ),
				'zoom'    => esc_html__( 'Zoom', 'planty' ),
				'fade'    => esc_html__( 'Fade', 'planty' ),
				'slide'   => esc_html__( 'Slide', 'planty' ),
				'pull'    => esc_html__( 'Pull', 'planty' ),
				'border'  => esc_html__( 'Border', 'planty' ),
				'excerpt' => esc_html__( 'Excerpt', 'planty' ),
				'info'    => esc_html__( 'Info', 'planty' ),
			)
		);
		return $prepend_inherit ? planty_array_merge( array( 'inherit' => esc_html__( 'Inherit', 'planty' ) ), $list ) : $list;
	}
}

if ( ! function_exists( 'planty_get_list_blog_contents' ) ) {
	/**
	 * An array with styles of the post content inside a blog archive:
	 *
	 * 'excerpt' - a short text (excerpt) in the each post in the blog archive page.
	 *
	 * 'fullpost' - a full post content in the each post in the blog archive page.
	 *
	 * @param bool $prepend_inherit  Optional. Need to place 'Inherit' to the start of the array. Default is false.
	 *
	 * @return array                 An associative array with styles in the format: 'slug' => 'Title'.
	 */
	function planty_get_list_blog_contents( $prepend_inherit = false ) {
		$list = array(
				'excerpt'  => esc_html__( 'Excerpt', 'planty' ),
				'fullpost' => esc_html__( 'Full post', 'planty' ),
		);
		return $prepend_inherit ? planty_array_merge( array( 'inherit' => esc_html__( 'Inherit', 'planty' ) ), $list ) : $list;
	}
}

if ( ! function_exists( 'planty_get_list_blog_paginations' ) ) {
	/**
	 * An array with a pagination styles for the blog archive page.
	 *
	 * 'pages' - a page numbers.
	 *
	 * 'links' - links 'Prev' and 'Next'.
	 *
	 * 'more' - a 'Load more' button to AJAX loading a next page.
	 *
	 * 'infinite' - an infinite scroll behaviour with AJAX loading a next page when page scrolled down.
	 *
	 * @param bool $prepend_inherit  Optional. Need to place 'Inherit' to the start of the array. Default is false.
	 *
	 * @return array                 An associative array with pagination styles in format:
	 *                               'slug' => array( 'title' => 'Title', 'icon' => 'relative path to the icon' )
	 *                               This format used to show elements in the 'icons' style instead dropdown.
	 */
	function planty_get_list_blog_paginations( $prepend_inherit = false ) {
		$list = array(
					'pages'    => array(
										'title' => esc_html__( 'Page numbers', 'planty' ),
										'icon'  => 'images/theme-options/pagination/page-numbers.png',
										),
					'links'    => array(
										'title' => esc_html__( 'Older/Newest', 'planty' ),
										'icon'  => 'images/theme-options/pagination/older-newest.png',
										),
					'more'     => array(
										'title' => esc_html__( 'Load more', 'planty' ),
										'icon'  => 'images/theme-options/pagination/load-more.png',
										),
					'infinite' => array(
										'title' => esc_html__( 'Infinite scroll', 'planty' ),
										'icon'  => 'images/theme-options/pagination/infinite-scroll.png',
										),
		);
		return $prepend_inherit
					? planty_array_merge(
							array( 
								'inherit' => array(
												'title' => esc_html__( 'Inherit', 'planty' ),
												'icon'  => 'images/theme-options/inherit.png',
												),
							),
							$list
						)
					: $list;
	}
}

if ( ! function_exists( 'planty_get_list_sidebars' ) ) {
	/**
	 * An array with a sidebar list: start with a theme specific sidebars, then - all user-defined custom sidebars.
	 *
	 * @param bool $prepend_inherit  Optional. Need to place 'inherit' => 'Inherit' to the start of the array. Default is false.
	 * @param bool $add_hide         Optional. Need to place 'hide' => 'Select widgets' to the start of the array. Default is false.
	 *
	 * @return array                 An associative array with sidebars in the format: 'slug' => 'Title'.
	 */
	function planty_get_list_sidebars( $prepend_inherit = false, $add_hide = false ) {
		$list = planty_storage_get( 'list_sidebars' );
		if ( '' == $list ) {
			global $wp_registered_sidebars;
			$list = array();
			if ( is_array( $wp_registered_sidebars ) ) {
				foreach ( $wp_registered_sidebars as $k => $v ) {
					$list[ $v['id'] ] = $v['name'];
				}
			}
			planty_storage_set( 'list_sidebars', $list );
		}
		if ( $add_hide ) {
			$list = planty_array_merge( array( 'hide' => planty_get_not_selected_text( esc_html__( 'Select widgets', 'planty' ) ) ), $list );
		}
		return $prepend_inherit ? planty_array_merge( array( 'inherit' => esc_html__( 'Inherit', 'planty' ) ), $list ) : $list;
	}
}

if ( ! function_exists( 'planty_get_list_sidebars_positions' ) ) {
	/**
	 * An array with a list of sidebar positions.
	 *
	 * @param bool $prepend_inherit  Optional. Need to place 'Inherit' to the start of the array. Default is false.
	 *
	 * @return array                 An associative array with sidebar positions in format:
	 *                               'slug' => array( 'title' => 'Title', 'icon' => 'relative path to the icon' )
	 *                               This format used to show elements in the 'icons' style instead dropdown.
	 */
	function planty_get_list_sidebars_positions( $prepend_inherit = false ) {
		$list = apply_filters(
			'planty_filter_list_sidebars_positions', array(
				'hide'  => array(
							'title' => esc_html__( 'No sidebar', 'planty' ),
							'icon'  => 'images/theme-options/sidebar-position/hide.png',
						),
				'left'  => array(
							'title' => esc_html__( 'Left sidebar', 'planty' ),
							'icon'  => 'images/theme-options/sidebar-position/left.png',
						),
				'right' => array(
							'title' => esc_html__( 'Right sidebar', 'planty' ),
							'icon'  => 'images/theme-options/sidebar-position/right.png',
						),
			)
		);
		return $prepend_inherit
					? planty_array_merge(
							array( 
								'inherit' => array(
												'title' => esc_html__( 'Inherit', 'planty' ),
												'icon'  => 'images/theme-options/inherit.png',
												),
							),
							$list
						)
					: $list;
	}
}

if ( ! function_exists( 'planty_get_list_sidebars_positions_ss' ) ) {
	/**
	 * An array with a list of sidebar positions for the small screens.
	 *
	 * @param bool $prepend_inherit  Optional. Need to place 'inherit' => 'Inherit' to the start of the array. Default is false.
	 *
	 * @return array                 An associative array with sidebar positions in the format: 'slug' => 'Title'.
	 */
	function planty_get_list_sidebars_positions_ss( $prepend_inherit = false ) {
		$list = apply_filters(
			'planty_filter_list_sidebars_positions_ss', array(
				'above' => esc_html__( 'Above the content', 'planty' ),
				'below' => esc_html__( 'Below the content', 'planty' ),
				'float' => esc_html__( 'Floating bar', 'planty' ),
			)
		);
		return $prepend_inherit ? planty_array_merge( array( 'inherit' => esc_html__( 'Inherit', 'planty' ) ), $list ) : $list;
	}
}

if ( ! function_exists( 'planty_get_list_sidebar_styles' ) ) {
	/**
	 * An array with a list of sidebar styles.
	 *
	 * @param bool $prepend_inherit  Optional. Need to place 'inherit' => 'Inherit' to the start of the array. Default is false.
	 *
	 * @return array                 An associative array with sidebar styles in the format: 'slug' => 'Title'.
	 */
	function planty_get_list_sidebar_styles( $prepend_inherit = false ) {
		static $list = false;
		if ( ! $list ) {
			$list = apply_filters( 'planty_filter_list_sidebar_styles', array() );
		}
		return $prepend_inherit ? planty_array_merge( array( 'inherit' => esc_html__( 'Inherit', 'planty' ) ), $list ) : $list;
	}
}

if ( ! function_exists( 'planty_get_list_header_footer_types' ) ) {
	/**
	 * An array with a list of header/footer/sidebar types.
	 *
	 * 'default' - is a "hardcoded" style included in the theme as .php or .html file
	 *
	 * 'custom' - is a custom editable layout, created with a supported Page Builder.
	 *
	 * @param bool $prepend_inherit  Optional. Need to place 'inherit' => 'Inherit' to the start of the array. Default is false.
	 *
	 * @return array                 An associative array with layout types in the format: 'slug' => 'Title'.
	 */
	function planty_get_list_header_footer_types( $prepend_inherit = false ) {
		$list = apply_filters(
			'planty_filter_list_header_footer_types', array(
				'default' => esc_html__( 'Default', 'planty' ),
			)
		);
		return $prepend_inherit ? planty_array_merge( array( 'inherit' => esc_html__( 'Inherit', 'planty' ) ), $list ) : $list;
	}
}

if ( ! function_exists( 'planty_get_list_header_styles' ) ) {
	/**
	 * An array with a header styles - a list of custom layouts, created with a supported Page Builder.
	 *
	 * @param bool $prepend_inherit  Optional. Need to place 'inherit' => 'Inherit' to the start of the array. Default is false.
	 *
	 * @return array                 An associative array with headers in the format: 'slug' => 'Title'.
	 */
	function planty_get_list_header_styles( $prepend_inherit = false ) {
		static $list = false;
		if ( ! $list ) {
			$list = apply_filters( 'planty_filter_list_header_styles', array() );
		}
		return $prepend_inherit ? planty_array_merge( array( 'inherit' => esc_html__( 'Inherit', 'planty' ) ), $list ) : $list;
	}
}

if ( ! function_exists( 'planty_get_list_header_positions' ) ) {
	/**
	 * An array with a header positions:
	 *
	 * 'default' - a header layout will be placed before the page content.
	 *
	 * 'over' - a header placed over the content (in fixed or absolute position)
	 *
	 * 'under' - a header placed under the content (in fixed position)
	 *
	 * @param bool $prepend_inherit  Optional. Need to place 'inherit' => 'Inherit' to the start of the array. Default is false.
	 *
	 * @return array                 An associative array with header positions in the format: 'slug' => 'Title'.
	 */
	function planty_get_list_header_positions( $prepend_inherit = false ) {
		$list = array(
			'default' => esc_html__( 'Default', 'planty' ),
			'over'    => esc_html__( 'Over', 'planty' ),
			'under'   => esc_html__( 'Under', 'planty' ),
		);
		return $prepend_inherit ? planty_array_merge( array( 'inherit' => esc_html__( 'Inherit', 'planty' ) ), $list ) : $list;
	}
}

if ( ! function_exists( 'planty_get_list_footer_styles' ) ) {
	/**
	 * An array with a footer styles - a list of custom layouts, created with a supported Page Builder.
	 *
	 * @param bool $prepend_inherit  Optional. Need to place 'inherit' => 'Inherit' to the start of the array. Default is false.
	 *
	 * @return array                 An associative array with footers in the format: 'slug' => 'Title'.
	 */
	function planty_get_list_footer_styles( $prepend_inherit = false ) {
		static $list = false;
		if ( ! $list ) {
			$list = apply_filters( 'planty_filter_list_footer_styles', array() );
		}
		return $prepend_inherit ? planty_array_merge( array( 'inherit' => esc_html__( 'Inherit', 'planty' ) ), $list ) : $list;
	}
}

if ( ! function_exists( 'planty_get_list_body_styles' ) ) {
	/**
	 * An array with a list of the body styles ( boxed | wide | fullwide | fullscreen ).
	 *
	 * @param bool $prepend_inherit   Optional. Need to place 'Inherit' to the start of the array. Default is false.
	 * @param bool $force_fullscreen  Optional. Add 'fullwide' and 'fullscreen' styles to the list. Default is false.
	 *
	 * @return array                  An associative array with a body styles in format:
	 *                                'slug' => array( 'title' => 'Title', 'icon' => 'relative path to the icon' )
	 *                                This format used to show elements in the 'icons' style instead dropdown.
	 */
	function planty_get_list_body_styles( $prepend_inherit = false, $force_fullscreen = false ) {
		$list = array(
			'boxed' => array(
						'title' => esc_html__( 'Boxed', 'planty' ),
						'icon'  => 'images/theme-options/body-style/boxed.png',
					),
			'wide'  => array(
						'title' => esc_html__( 'Wide', 'planty' ),
						'icon'  => 'images/theme-options/body-style/wide.png',
					),
		);
		if ( apply_filters( 'planty_filter_allow_fullscreen', $force_fullscreen || planty_get_theme_setting( 'allow_fullscreen' ) || planty_get_edited_post_type() == 'page' ) ) {
			$list['fullwide']   = array(
									'title' => esc_html__( 'Fullwidth', 'planty' ),
									'icon'  => 'images/theme-options/body-style/fullwide.png',
									);
			$list['fullscreen'] = array(
									'title' => esc_html__( 'Fullscreen', 'planty' ),
									'icon'  => 'images/theme-options/body-style/fullscreen.png',
									);
		}
		$list = apply_filters( 'planty_filter_list_body_styles', $list );
		return $prepend_inherit
					? planty_array_merge(
							array( 
								'inherit' => array(
												'title' => esc_html__( 'Inherit', 'planty' ),
												'icon'  => 'images/theme-options/inherit.png',
												),
							),
							$list
						)
					: $list;
	}
}

if ( ! function_exists( 'planty_get_list_expand_content' ) ) {
	/**
	 * An array with a list of the "expand content" choices ( narrow | normal | expand ) - a width of the post content
	 * when sidebar is not present on the page.
	 *
	 * @param bool $prepend_inherit  Optional. Need to place 'Inherit' to the start of the array. Default is false.
	 *
	 * @return array                 An associative array with an "expand content" choices in format:
	 *                               'slug' => array( 'title' => 'Title', 'icon' => 'relative path to the icon' )
	 *                               This format used to show elements in the 'icons' style instead dropdown.
	 */
	function planty_get_list_expand_content( $prepend_inherit = false, $narrow = false ) {
		$list = apply_filters(
			'planty_filter_list_expand_content', array_merge(
				( $narrow
					? array(
						'narrow' => array(
								'title' => esc_html__( 'Narrow', 'planty' ),
								'icon'  => 'images/theme-options/expand-content/narrow.png',
								)
						)
					: array()
				),
				array(
					'normal' => array(
							'title' => esc_html__( 'Normal', 'planty' ),
							'icon'  => 'images/theme-options/expand-content/normal.png',
							),
					'expand' => array(
							'title' => esc_html__( 'Wide', 'planty' ),
							'icon'  => 'images/theme-options/expand-content/wide.png',
						),
				)
			)
		);
		return $prepend_inherit
					? planty_array_merge(
							array( 
								'inherit' => array(
												'title' => esc_html__( 'Inherit', 'planty' ),
												'icon'  => 'images/theme-options/inherit.png',
												),
							),
							$list
						)
					: $list;
	}
}

if ( ! function_exists( 'planty_get_list_remove_margins' ) ) {
	/**
	 * An array with a list of the "remove margins" choices ( 0 - show margins | 1 - remove margins ) -
	 * need to remove margins from start and end of the page or not
	 *
	 * @param bool $prepend_inherit  Optional. Need to place 'Inherit' to the start of the array. Default is false.
	 *
	 * @return array                 An associative array with an "remove margins" choices in format:
	 *                               'slug' => array( 'title' => 'Title', 'icon' => 'relative path to the icon' )
	 *                               This format used to show elements in the 'icons' style instead dropdown.
	 */
	function planty_get_list_remove_margins( $prepend_inherit = false ) {
		$list = apply_filters(
			'planty_filter_list_remove_margins', array(
				'0'  => array(
							'title' => esc_html__( 'Margins On', 'planty' ),
							'icon'  => 'images/theme-options/remove-margins/on.png',
						),
				'1'  => array(
							'title' => esc_html__( 'Margins Off', 'planty' ),
							'icon'  => 'images/theme-options/remove-margins/off.png',
						),
			)
		);
		return $prepend_inherit
					? planty_array_merge(
							array( 
								'inherit' => array(
												'title' => esc_html__( 'Inherit', 'planty' ),
												'icon'  => 'images/theme-options/inherit.png',
												),
							),
							$list
						)
					: $list;
	}
}

if ( ! function_exists( 'planty_get_list_meta_parts' ) ) {
	/**
	 * An array with a list of meta parts ( author | date | modified | views | likes | comments | share | categories | edit | etc. )
	 *
	 * @param bool $prepend_inherit  Optional. Need to place 'inherit' => 'Inherit' to the start of the array. Default is false.
	 *
	 * @return array                 An associative array with a meta parts in the format: 'slug' => 'Title'.
	 */
	function planty_get_list_meta_parts( $prepend_inherit = false ) {
		$list = apply_filters(
			'planty_filter_list_meta_parts',
			array(
				'author'     => esc_html__( 'Post author', 'planty' ),
				'date'       => esc_html__( 'Published date', 'planty' ),
				'modified'   => esc_html__( 'Modified date', 'planty' ),
				'views'      => esc_html__( 'Views', 'planty' ),
				'likes'      => esc_html__( 'Likes', 'planty' ),
				'comments'   => esc_html__( 'Comments', 'planty' ),
				'share'      => esc_html__( 'Share links', 'planty' ),
				'categories' => esc_html__( 'Categories', 'planty' ),
				'edit'       => esc_html__( 'Edit link', 'planty' ),
			)
		);
		// Reorder meta_parts with last user's choise
		if ( planty_storage_isset( 'options', 'meta_parts', 'val' ) ) {
			$parts = explode( '|', planty_get_theme_option( 'meta_parts' ) );
			$list_new = array();
			foreach( $parts as $part ) {
				$part = explode( '=', $part );
				if ( isset( $list[ $part[0] ] ) ) {
					$list_new[ $part[0] ] = $list[ $part[0] ];
					unset( $list[ $part[0] ] );
				}
			}
			$list = count( $list ) > 0 ? array_merge( $list_new, $list ) : $list_new;
		}
		return $prepend_inherit ? planty_array_merge( array( 'inherit' => esc_html__( 'Inherit', 'planty' ) ), $list ) : $list;
	}
}

if ( ! function_exists( 'planty_get_list_share_links_positions' ) ) {
	/**
	 * An array with a list of the share links positions ( top | left | bottom ).
	 *
	 * @param bool $prepend_inherit  Optional. Need to place 'inherit' => 'Inherit' to the start of the array. Default is false.
	 *
	 * @return array                 An associative array with a share links positions in the format: 'slug' => 'Title'.
	 */
	function planty_get_list_share_links_positions( $prepend_inherit = false ) {
		$list = apply_filters(
			'planty_filter_list_share_links_positions',
			array(
				'top'    => esc_html__( 'Top', 'planty' ),
				'left'   => esc_html__( 'Left', 'planty' ),
				'bottom' => esc_html__( 'Bottom', 'planty' ),
			)
		);
		return $prepend_inherit ? planty_array_merge( array( 'inherit' => esc_html__( 'Inherit', 'planty' ) ), $list ) : $list;
	}
}

if ( ! function_exists( 'planty_get_list_blog_styles' ) ) {
	/**
	 * An array with a list of the theme specific blog styles ( used on the archive pages ).
	 *
	 * @param bool   $prepend_inherit  Optional. Need to place 'inherit' => 'Inherit' to the start of the array. Default is false.
	 * @param string $format           Optional. Format of the result. If equal to 'arh' - an array with keys 'title' and 'icon' is returned.
	 *
	 * @return array                   An associative array with a blog styles in the format 'slug' => 'Title'
	 *                                 or if the argument $filter == 'arh' in the format:
	 *                                 'slug' => array( 'title' => 'Title', 'icon' => 'relative path to the icon' )
	 *                                 This format used to show elements in the 'icons' style instead dropdown.
	 */
	function planty_get_list_blog_styles( $prepend_inherit = false, $filter = 'arh', $need_custom = true ) {
		$list   = array();
		$styles = planty_storage_get( 'blog_styles' );
		if ( is_array( $styles ) ) {
			foreach ( $styles as $k => $v ) {
				if ( empty( $filter ) || ! isset( $v[ "{$filter}_allowed" ] ) || $v[ "{$filter}_allowed" ] ) {
					if ( 'arh' == $filter && isset( $v['columns'] ) && is_array( $v['columns'] ) ) {
						$new_row = ! empty( $v['new_row'] );
						foreach ( $v['columns'] as $col ) {
							// Translators: Make blog style title: "Layout name X Columns"
							$list[ "{$k}_{$col}" ] = 'arh' == $filter
														? array(
															'title'   => sprintf( _n( '%1$s %2$d Column', '%1$s %2$d Columns', $col, 'planty' ), $v['title'], $col ),
															'icon'    => ! empty( $v['icon'] )
																			? ( strpos( $v['icon'], '%d' ) !== false ? sprintf( $v['icon'], $col ) : $v['icon'] )
																			: 'images/theme-options/blog-style/custom.png',
															'new_row' => $new_row,
															)
														: sprintf( _n( '%1$s %2$d Column', '%1$s %2$d Columns', $col, 'planty' ), $v['title'], $col );
							$new_row = false;
						}
					} else {
						$list[ $k ] = 'arh' == $filter
											? array(
													'title' => $v['title'],
													'icon'  => ! empty( $v['icon'] )
																	? ( strpos( $v['icon'], '%d' ) !== false ? sprintf( $v['icon'], $col ) : $v['icon'] )
																	: 'images/theme-options/blog-style/custom.png',
												)
											: $v['title'];
					}
				}
			}
		}
		$list = apply_filters( 'planty_filter_list_blog_styles', $list, $filter, $need_custom );
		return $prepend_inherit
					? planty_array_merge(
							array( 
								'inherit' => array(
												'title' => esc_html__( 'Inherit', 'planty' ),
												'icon'  => 'images/theme-options/inherit.png',
												),
							),
							$list
						)
					: $list;
	}
}

if ( ! function_exists( 'planty_get_list_single_styles' ) ) {
	/**
	 * An array with a list of the theme specific single styles ( used on the single post pages ).
	 *
	 * @param bool   $prepend_inherit  Optional. Need to place 'Inherit' to the start of the array. Default is false.
	 *
	 * @return array                   An associative array with a single styles in the format:
	 *                                 'slug' => array( 'title' => 'Title', 'icon' => 'relative path to the icon' )
	 *                                 This format used to show elements in the 'icons' style instead dropdown.
	 */
	function planty_get_list_single_styles( $prepend_inherit = false ) {
		$list = apply_filters( 'planty_filter_list_single_styles', planty_storage_get( 'single_styles' ) );
		return $prepend_inherit
					? planty_array_merge(
							array( 
								'inherit' => array(
												'title' => esc_html__( 'Inherit', 'planty' ),
												'icon'  => 'images/theme-options/inherit.png',
												),
							),
							$list
						)
					: $list;
	}
}

if ( ! function_exists( 'planty_get_list_categories' ) ) {
	/**
	 * An array with a list of categories (terms) available for posts.
	 *
	 * @param bool $prepend_inherit  Optional. Need to place 'inherit' => 'Inherit' to the start of the array. Default is false.
	 *
	 * @return array                 An associative array with categories in the format: 'slug' => 'Title'.
	 */
	function planty_get_list_categories( $prepend_inherit = false ) {
		$list = planty_storage_get( 'list_categories' );
		if ( '' == $list ) {
			$list       = array();
			$taxonomies = get_categories(
				array(
					'type'         => 'post',
					'orderby'      => 'name',
					'order'        => 'ASC',
					'hide_empty'   => 0,
					'hierarchical' => 1,
					'taxonomy'     => 'category',
					'pad_counts'   => false,
				)
			);
			if ( is_array( $taxonomies ) && count( $taxonomies ) > 0 ) {
				foreach ( $taxonomies as $cat ) {
					$list[ $cat->term_id ] = apply_filters( 'planty_filter_term_name', $cat->name, $cat );
				}
			}
			planty_storage_set( 'list_categories', $list );
		}
		return $prepend_inherit ? planty_array_merge( array( 'inherit' => esc_html__( 'Inherit', 'planty' ) ), $list ) : $list;
	}
}

if ( ! function_exists( 'planty_get_list_terms' ) ) {
	/**
	 * An array with a list of terms of the specified taxonomy.
	 *
	 * @param bool   $prepend_inherit  Optional. Need to place 'inherit' => 'Inherit' to the start of the array. Default is false.
	 * @param string $taxonomy         Optional. A taxonomy slug to return its terms. Default is 'category'.
	 *
	 * @return array                   An associative array with terms in the format: 'slug' => 'Title'.
	 */
	function planty_get_list_terms( $prepend_inherit = false, $taxonomy = 'category' ) {
		$list = planty_storage_get( 'list_taxonomies_' . ( $taxonomy ) );
		if ( '' == $list ) {
			$list       = array();
			$taxonomies = get_terms(
				$taxonomy, array(
					'orderby'      => 'name',
					'order'        => 'ASC',
					'hide_empty'   => 0,
					'hierarchical' => 1,
					'taxonomy'     => $taxonomy,
					'pad_counts'   => false,
				)
			);
			if ( is_array( $taxonomies ) && count( $taxonomies ) > 0 ) {
				foreach ( $taxonomies as $cat ) {
					// Remove false to append term names with taxonomy name
					$list[ $cat->term_id ] = apply_filters( 'planty_filter_term_name', $cat->name . ( false && 'category' != $taxonomy ? " /{$cat->taxonomy}/" : '' ), $cat );
				}
			}
			planty_storage_set( 'list_taxonomies_' . ( $taxonomy ), $list );
		}
		return $prepend_inherit ? planty_array_merge( array( 'inherit' => esc_html__( 'Inherit', 'planty' ) ), $list ) : $list;
	}
}

if ( ! function_exists( 'planty_get_list_posts_types' ) ) {
	/**
	 * An array with a list of registered public post types.
	 *
	 * @param bool   $prepend_inherit  Optional. Need to place 'inherit' => 'Inherit' to the start of the array. Default is false.
	 *
	 * @return array                   An associative array with post types in the format: 'slug' => 'Title'.
	 */
	function planty_get_list_posts_types( $prepend_inherit = false ) {
		$list = planty_storage_get( 'list_posts_types' );
		if ( '' == $list ) {
			$list = apply_filters(
				'planty_filter_list_posts_types', array(
					'post' => esc_html__( 'Post', 'planty' ),
				)
			);
			planty_storage_set( 'list_posts_types', $list );
		}
		return $prepend_inherit ? planty_array_merge( array( 'inherit' => esc_html__( 'Inherit', 'planty' ) ), $list ) : $list;
	}
}

if ( ! function_exists( 'planty_get_list_posts' ) ) {
	/**
	 * An array with a list of posts filtered by specified options.
	 *
	 * @param bool  $prepend_inherit  Optional. Need to place 'inherit' => 'Inherit' to the start of the array. Default is false.
	 * @param array $opt              Optional. An array with a query options to select posts:
	 *
	 *                                - 'post_type': post type to select posts. Default is 'post'.
	 *
	 *                                - 'post_status': post status to select posts. Default is 'publish'.
	 *
	 *                                - 'post_parent': a parent post ID to select children posts. Default is ''.
	 *
	 *                                - 'taxonomy': a slug of the taxonomy to select posts. Default is 'category'.
	 *
	 *                                - 'taxonomy_value': a term of the taxonomy to select posts. Default is ''.
	 *
	 *                                - 'meta_key': a meta key to filter posts. Default is ''.
	 *
	 *                                - 'meta_value': a meta value to filter posts. Default is ''.
	 *
	 *                                - 'meta_compare': a comparsion operator to compare meta values. Default is ''.
	 *
	 *                                - 'posts_per_page': a number posts to return. Default is -1 (all found posts).
	 *
	 *                                - 'orderby': a sort criteria. Default is 'post_date'.
	 *
	 *                                - 'order': a sort order. Default is 'desc'.
	 *
	 *                                - 'not_selected': true (default) if need to place 'none' => 'Not selected' to the start of the array.
	 *
	 *                                - 'return': what field will be used as keys of the result array? Default is 'id'
	 *
	 * @return array                  An associative array with posts in the format: 'ID' => 'Title' or 'title' => 'Title'
	 *                                ( if option 'return' is not equal to 'id' ).
	 */
	function planty_get_list_posts( $prepend_inherit = false, $opt = array() ) {
		$opt = array_merge(
			array(
				'post_type'        => 'post',
				'post_status'      => 'publish',
				'post_parent'      => '',
				'taxonomy'         => 'category',
				'taxonomy_value'   => '',
				'meta_key'         => '',
				'meta_value'       => '',
				'meta_compare'     => '',
				'suppress_filters' => false,  // Need to compatibility with WPML, because default value is true in the get_posts()
				'posts_per_page'   => -1,
				'orderby'          => 'post_date',
				'order'            => 'desc',
				'not_selected'     => true,
				'return'           => 'id',
			), is_array( $opt ) ? $opt : array( 'post_type' => $opt )
		);

		$hash = 'list_posts'
				. '_' . ( is_array( $opt['post_type'] ) ? join( '_', $opt['post_type'] ) : $opt['post_type'] )
				. '_' . ( is_array( $opt['post_parent'] ) ? join( '_', $opt['post_parent'] ) : $opt['post_parent'] )
				. '_' . ( $opt['taxonomy'] )
				. '_' . ( is_array( $opt['taxonomy_value'] ) ? join( '_', $opt['taxonomy_value'] ) : $opt['taxonomy_value'] )
				. '_' . ( $opt['meta_key'] )
				. '_' . ( $opt['meta_compare'] )
				. '_' . ( $opt['meta_value'] )
				. '_' . ( $opt['orderby'] )
				. '_' . ( $opt['order'] )
				. '_' . ( $opt['return'] )
				. '_' . ( $opt['posts_per_page'] );
		$list = planty_storage_get( $hash );
		if ( '' == $list ) {
			$list = array();
			if ( false !== $opt['not_selected'] ) {
				$list['none'] = true === $opt['not_selected'] ? planty_get_not_selected_text( esc_html__( 'Not selected', 'planty' ) ) : $opt['not_selected'];
			}
			$args = array(
				'post_type'           => $opt['post_type'],
				'post_status'         => $opt['post_status'],
				'posts_per_page'      => -1 == $opt['posts_per_page'] ? 1000 : $opt['posts_per_page'],
				'ignore_sticky_posts' => true,
				'orderby'             => $opt['orderby'],
				'order'               => $opt['order'],
			);
			if ( ! empty( $opt['post_parent'] ) ) {
				if ( is_array( $opt['post_parent'] ) ) {
					$args['post_parent__in'] = $opt['post_parent'];
				} else {
					$args['post_parent'] = $opt['post_parent'];
				}
			}
			if ( ! empty( $opt['taxonomy_value'] ) ) {
				$args['tax_query'] = array(
					array(
						'taxonomy' => $opt['taxonomy'],
						'field'    => is_array( $opt['taxonomy_value'] )
										? ( (int) $opt['taxonomy_value'][0] > 0 ? 'term_taxonomy_id' : 'slug' )
										: ( (int) $opt['taxonomy_value'] > 0 ? 'term_taxonomy_id' : 'slug' ),
						'terms'    => is_array( $opt['taxonomy_value'] )
										? $opt['taxonomy_value']
										: ( (int) $opt['taxonomy_value'] > 0 ? (int) $opt['taxonomy_value'] : $opt['taxonomy_value'] ),
					),
				);
			}
			if ( ! empty( $opt['meta_key'] ) ) {
				$args['meta_key'] = $opt['meta_key'];
			}
			if ( ! empty( $opt['meta_value'] ) ) {
				$args['meta_value'] = $opt['meta_value'];
			}
			if ( ! empty( $opt['meta_compare'] ) ) {
				$args['meta_compare'] = $opt['meta_compare'];
			}
			$posts = get_posts( $args );
			if ( is_array( $posts ) && count( $posts ) > 0 ) {
				foreach ( $posts as $post ) {
					$list[ 'id' == $opt['return'] ? $post->ID : ( 'post_name' == $opt['return'] ? $post->post_name : $post->post_title ) ] = $post->post_title;
				}
			}
			planty_storage_set( $hash, $list );
		}
		return $prepend_inherit ? planty_array_merge( array( 'inherit' => esc_html__( 'Inherit', 'planty' ) ), $list ) : $list;
	}
}


if ( ! function_exists( 'planty_get_list_users' ) ) {
	/**
	 * An array with a list of registered users filtered by user roles.
	 *
	 * @param bool  $prepend_inherit  Optional. Need to place 'inherit' => 'Inherit' to the start of the array. Default is false.
	 * @param array $roles            Optional. An array with a user roles to select users:
	 *                                'administrator', 'editor', 'author', 'contributor', 'shop_manager', etc.
	 *
	 * @return array                  An associative array with users in the format: 'ID' => 'Title'.
	 */
	function planty_get_list_users( $prepend_inherit = false, $roles = array( 'administrator', 'editor', 'author', 'contributor', 'shop_manager' ) ) {
		$list = planty_storage_get( 'list_users' );
		if ( '' == $list ) {
			$list         = array();
			$list['none'] = planty_get_not_selected_text( esc_html__( 'Not selected', 'planty' ) );
			$users        = get_users(
				array(
					'orderby'  => 'display_name',
					'order'    => 'ASC',
					'role__in' => $roles
				)
			);
			if ( is_array( $users ) && count( $users ) > 0 ) {
				foreach ( $users as $user ) {
					$accept = true;
					//--- Not need to check roles because a param 'role__in' is added to the query above
					//--- ( this param help filter records and increase a query speed:
					//---   if a site has many subscribers - they are not included in the array $users )
					if ( false && is_array( $user->roles ) && count( $user->roles ) > 0 ) {
						$accept = false;
						foreach ( $user->roles as $role ) {
							if ( in_array( $role, $roles ) ) {
								$accept = true;
								break;
							}
						}
					}
					//---
					if ( $accept ) {
						$list[ $user->user_login ] = $user->display_name;
					}
				}
			}
			planty_storage_set( 'list_users', $list );
		}
		return $prepend_inherit ? planty_array_merge( array( 'inherit' => esc_html__( 'Inherit', 'planty' ) ), $list ) : $list;
	}
}

if ( ! function_exists( 'planty_get_list_menus' ) ) {
	/**
	 * An array with a list of registered menus.
	 *
	 * @param bool  $prepend_inherit  Optional. Need to place 'inherit' => 'Inherit' to the start of the array. Default is false.
	 *
	 * @return array                  An associative array with registered menus in the format: 'slug' => 'Title'.
	 */
	function planty_get_list_menus( $prepend_inherit = false ) {
		$list = planty_storage_get( 'list_menus' );
		if ( '' == $list ) {
			$list            = array();
			$list['default'] = esc_html__( 'Default', 'planty' );
			$menus           = wp_get_nav_menus();
			if ( is_array( $menus ) && count( $menus ) > 0 ) {
				foreach ( $menus as $menu ) {
					$list[ $menu->slug ] = $menu->name;
				}
			}
			planty_storage_set( 'list_menus', $list );
		}
		return $prepend_inherit ? planty_array_merge( array( 'inherit' => esc_html__( 'Inherit', 'planty' ) ), $list ) : $list;
	}
}

if ( ! function_exists( 'planty_get_list_icons' ) ) {
	/**
	 * An array with a list of specified icons (font icons, svg icons or png icons).
	 *
	 * @param string $style  Optional. Style of the desired icons:
	 *
	 *                       - 'icons': Default. Return a list of font icons in the format 'slug' => 'Title'
	 *
	 *                       - 'images': Return a list of png icons from the theme subfolder 'css/icons.png'
	 *                       in the format 'file_name' => 'file_url'
	 *
	 *                       - 'svg': Return a list of svg icons from the theme subfolder 'css/icons.svg'
	 *                       in the format 'file_name' => 'file_url'
	 *
	 * @param bool   $prepend_inherit  Optional. Need to place 'inherit' => 'Inherit' to the start of the array. Default is false.
	 *
	 * @return array         An associative array with icons.
	 */
	function planty_get_list_icons( $style = 'icons', $prepend_inherit = false ) {
		$lists = get_transient( 'planty_list_icons' );
		if ( ! is_array( $lists ) || ! isset( $lists[ $style ] ) || ! is_array( $lists[ $style ] ) || count( $lists[ $style ] ) < 2 ) {
			$lists = array();
			if ( 'icons' == $style ) {
				$lists[ $style ] = planty_array_from_list( planty_get_list_icons_classes( $prepend_inherit ) );
			} elseif ( 'images' == $style ) {
				$lists[ $style ] = planty_get_list_images();
			} else { // 'svg'
				$lists[ $style ] = planty_get_list_images( false, 'svg' );
			}
			if ( is_admin() && is_array( $lists[ $style ] ) && count( $lists[ $style ] ) > 1 ) {
				set_transient( 'planty_list_icons', $lists, 6 * 60 * 60 );       // Store to the cache for 6 hours
			}
		}
		return $lists[ $style ];
	}
}

if ( ! function_exists( 'planty_get_list_icons_classes' ) ) {
	/**
	 * An array with a list of font icons in the format 'slug' => 'Title'.
	 *
	 * @param bool  $prepend_inherit  Optional. Need to place 'inherit' => 'Inherit' to the start of the array. Default is false.
	 *
	 * @return array                  An associative array with icons.
	 */
	function planty_get_list_icons_classes( $prepend_inherit = false ) {
		static $list = false;
		if ( ! is_array( $list ) ) {
			$list = ! is_admin() ? array() : planty_parse_icons_classes( planty_get_file_dir( 'css/font-icons/css/fontello-codes.css' ) );
		}
		$list = ! is_array( $list ) ? array() : planty_array_merge( array( 'none' => 'none' ), $list );
		return $prepend_inherit ? planty_array_merge( array( 'inherit' => esc_html__( 'Inherit', 'planty' ) ), $list ) : $list;
	}
}

if ( ! function_exists( 'planty_get_list_images' ) ) {
	/**
	 * An array with a list of images in the format 'file_name' => 'file_url'.
	 *
	 * @param bool   $prepend_inherit  Optional. Need to place 'inherit' => 'Inherit' to the start of the array. Default is false.
	 * @param string $type             Optional. An image type to return. Default is 'png'.
	 *
	 * @return array                   An associative array with images.
	 */
	function planty_get_list_images( $prepend_inherit = false, $type = 'png' ) {
		$list = function_exists( 'trx_addons_get_list_files' )
				? trx_addons_get_list_files( "css/icons.{$type}", $type )
				: array();
		return $prepend_inherit ? planty_array_merge( array( 'inherit' => esc_html__( 'Inherit', 'planty' ) ), $list ) : $list;
	}
}

if ( ! function_exists( 'planty_get_list_sc_color_styles' ) ) {
	/**
	 * An array with a list of the theme specific color styles.
	 *
	 * @param bool   $prepend_inherit  Optional. Need to place 'inherit' => 'Inherit' to the start of the array. Default is false.
	 *
	 * @return array                   An associative array with a color styles.
	 */
	function planty_get_list_sc_color_styles( $prepend_inherit = false ) {
		$list = apply_filters(
			'planty_filter_get_list_sc_color_styles', array(
				'default' => esc_html__( 'Default', 'planty' ),
				'link2'   => esc_html__( 'Accent 2', 'planty' ),
				'link3'   => esc_html__( 'Accent 3', 'planty' ),
				'dark'    => esc_html__( 'Dark', 'planty' ),
			)
		);
		return $prepend_inherit ? planty_array_merge( array( 'inherit' => esc_html__( 'Inherit', 'planty' ) ), $list ) : $list;
	}
}
