<?php
/**
 * Generate custom CSS
 *
 * @package PLANTY
 * @since PLANTY 1.0
 */

// Return CSS with custom colors and fonts
if ( ! function_exists( 'planty_customizer_get_css' ) ) {

	function planty_customizer_get_css( $args = array() ) {

		$colors        = isset( $args['colors'] ) ? $args['colors'] : null;
		$scheme        = isset( $args['scheme'] ) ? $args['scheme'] : null;
		$fonts         = isset( $args['fonts'] ) ? $args['fonts'] : null;
		$vars          = isset( $args['vars'] ) ? $args['vars'] : null;
		$remove_spaces = isset( $args['remove_spaces'] ) ? $args['remove_spaces'] : true;

		$css = array(
			'vars'   => '',
			'fonts'  => '',
			'colors' => '',
		);

		// Theme fonts
		//---------------------------------------------
		if ( null === $fonts ) {
			$fonts = planty_get_theme_fonts();
		}

		if ( $fonts ) {

			// Make theme-specific fonts rules
			$fonts        = planty_customizer_add_theme_fonts( $fonts );
			$rez          = array();
			$article_font = ( ! empty( $fonts['post_font-family'] )
								|| ! empty( $fonts['post_font-weight'] )
								|| ! empty( $fonts['post_font-style'] )
								|| ! empty( $fonts['post_text-decoration'] )
								|| ! empty( $fonts['post_text-transform'] )
								|| ! empty( $fonts['post_letter-spacing'] )
								? "
/* Article text*/
.post_item_single.post_type_post .post_content_single,
body.post-type-post .editor-block-list__layout {
	{$fonts['post_font-family']}
	{$fonts['post_font-weight']}
	{$fonts['post_font-style']}
	{$fonts['post_text-decoration']}
	{$fonts['post_text-transform']}
	{$fonts['post_letter-spacing']}
}
"
								: ''
							)
							. ( ! empty( $fonts['post_margin-top'] )
								|| ! empty( $fonts['post_margin-bottom'] )
								? "
.post_item_single.post_type_post .post_content_single p,
.post_item_single.post_type_post .post_content_single ul,
.post_item_single.post_type_post .post_content_single ol,
.post_item_single.post_type_post .post_content_single dl,
.post_item_single.post_type_post .post_content_single table,
.post_item_single.post_type_post .post_content_single blockquote,
.post_item_single.post_type_post .post_content_single address,
.post_item_single.post_type_post .post_content_single .wp-block-button,
.post_item_single.post_type_post .post_content_single .wp-block-cover,
.post_item_single.post_type_post .post_content_single .wp-block-image,
.post_item_single.post_type_post .post_content_single .wp-block-video,
.post_item_single.post_type_post .post_content_single .wp-block-media-text,
body.post-type-post .editor-block-list__layout p,
body.post-type-post .editor-block-list__layout ul,
body.post-type-post .editor-block-list__layout ol,
body.post-type-post .editor-block-list__layout dl,
body.post-type-post .editor-block-list__layout table,
body.post-type-post .editor-block-list__layout blockquote,
body.post-type-post .editor-block-list__layout address,
body.post-type-post .editor-block-list__layout .wp-block-button,
body.post-type-post .editor-block-list__layout .wp-block-cover,
body.post-type-post .editor-block-list__layout .wp-block-image,
body.post-type-post .editor-block-list__layout .wp-block-video,
body.post-type-post .editor-block-list__layout .wp-block-media-text {
	{$fonts['post_margin-top']}
	{$fonts['post_margin-bottom']}
}
"
								: ''
							)
							. ( ! empty( $fonts['post_font-size'] )
								? '
.post_item_single.post_type_post .post_content_single p:not([class*="-font-size"]):not(.wp-block-cover-text),
.post_item_single.post_type_post .post_content_single ul:not([class*="-font-size"]),
.post_item_single.post_type_post .post_content_single ol:not([class*="-font-size"]),
.post_item_single.post_type_post .post_content_single dl:not([class*="-font-size"]),
.post_item_single.post_type_post .post_content_single table:not([class*="-font-size"]),
.post_item_single.post_type_post .post_content_single blockquote:not([class*="-font-size"]),
.post_item_single.post_type_post .post_content_single address:not([class*="-font-size"]),
body.post-type-post .editor-block-list__layout p:not([class*="-font-size"]):not(.wp-block-cover-text),
body.post-type-post .editor-block-list__layout ul:not([class*="-font-size"]),
body.post-type-post .editor-block-list__layout ol:not([class*="-font-size"]),
body.post-type-post .editor-block-list__layout dl:not([class*="-font-size"]),
body.post-type-post .editor-block-list__layout table:not([class*="-font-size"]),
body.post-type-post .editor-block-list__layout blockquote:not([class*="-font-size"]),
body.post-type-post .editor-block-list__layout address:not([class*="-font-size"]) {
' . $fonts['post_font-size'] . '
}
.post_item_single.post_type_post .post_content_single form p:not([style*="font-size"]) {
	font-size: 1em;
}
'
								: ''
							)
							. ( ! empty( $fonts['post_line-height'] )
								? '
.post_item_single.post_type_post .post_content_single p:not([style*="font-size"]):not(.wp-block-cover-text),
.post_item_single.post_type_post .post_content_single ul:not([style*="font-size"]),
.post_item_single.post_type_post .post_content_single ol:not([style*="font-size"]),
.post_item_single.post_type_post .post_content_single dl:not([style*="font-size"]),
.post_item_single.post_type_post .post_content_single table:not([style*="font-size"]),
.post_item_single.post_type_post .post_content_single blockquote:not([style*="font-size"]),
.post_item_single.post_type_post .post_content_single address:not([style*="font-size"]),
body.post-type-post .editor-block-list__layout p:not([style*="font-size"]):not(.wp-block-cover-text),
body.post-type-post .editor-block-list__layout ul:not([style*="font-size"]),
body.post-type-post .editor-block-list__layout ol:not([style*="font-size"]),
body.post-type-post .editor-block-list__layout dl:not([style*="font-size"]),
body.post-type-post .editor-block-list__layout table:not([style*="font-size"]),
body.post-type-post .editor-block-list__layout blockquote:not([style*="font-size"]),
body.post-type-post .editor-block-list__layout address:not([style*="font-size"]) {
' . $fonts['post_line-height'] . '
}
'
								: ''
							);

			$rez['fonts'] = <<<CSS

/* Main text*/
body {
	{$fonts['p_font-family']}
	{$fonts['p_font-size']}
	{$fonts['p_font-weight']}
	{$fonts['p_font-style']}
	{$fonts['p_line-height']}
	{$fonts['p_text-decoration']}
	{$fonts['p_text-transform']}
	{$fonts['p_letter-spacing']}
}
p, ul, ol, dl, blockquote, address,
.wp-block-button,
.wp-block-cover,
.wp-block-image,
.wp-block-video,
.wp-block-search,
.wp-block-archives,
.wp-block-archives-dropdown,
.wp-block-categories,
.wp-block-calendar,
.wp-block-media-text {
	{$fonts['p_margin-top']}
	{$fonts['p_margin-bottom']}
}
p[style*="font-size"],	/* tag p need if custom font size to the paragraph is applied. Thanks to @goodkindman */
.has-small-font-size,
.has-normal-font-size,
.has-medium-font-size {
	{$fonts['p_line-height']}	
}

/* Article text*/
{$article_font}

h1, .front_page_section_caption {
	{$fonts['h1_font-family']}
	{$fonts['h1_font-size']}
	{$fonts['h1_font-weight']}
	{$fonts['h1_font-style']}
	{$fonts['h1_line-height']}
	{$fonts['h1_text-decoration']}
	{$fonts['h1_text-transform']}
	{$fonts['h1_letter-spacing']}
	{$fonts['h1_margin-top']}
	{$fonts['h1_margin-bottom']}
}
h2 {
	{$fonts['h2_font-family']}
	{$fonts['h2_font-size']}
	{$fonts['h2_font-weight']}
	{$fonts['h2_font-style']}
	{$fonts['h2_line-height']}
	{$fonts['h2_text-decoration']}
	{$fonts['h2_text-transform']}
	{$fonts['h2_letter-spacing']}
	{$fonts['h2_margin-top']}
	{$fonts['h2_margin-bottom']}
}
h3 {
	{$fonts['h3_font-family']}
	{$fonts['h3_font-size']}
	{$fonts['h3_font-weight']}
	{$fonts['h3_font-style']}
	{$fonts['h3_line-height']}
	{$fonts['h3_text-decoration']}
	{$fonts['h3_text-transform']}
	{$fonts['h3_letter-spacing']}
	{$fonts['h3_margin-top']}
	{$fonts['h3_margin-bottom']}
}
h4 {
	{$fonts['h4_font-family']}
	{$fonts['h4_font-size']}
	{$fonts['h4_font-weight']}
	{$fonts['h4_font-style']}
	{$fonts['h4_line-height']}
	{$fonts['h4_text-decoration']}
	{$fonts['h4_text-transform']}
	{$fonts['h4_letter-spacing']}
	{$fonts['h4_margin-top']}
	{$fonts['h4_margin-bottom']}
}
h5 {
	{$fonts['h5_font-family']}
	{$fonts['h5_font-size']}
	{$fonts['h5_font-weight']}
	{$fonts['h5_font-style']}
	{$fonts['h5_line-height']}
	{$fonts['h5_text-decoration']}
	{$fonts['h5_text-transform']}
	{$fonts['h5_letter-spacing']}
	{$fonts['h5_margin-top']}
	{$fonts['h5_margin-bottom']}
}
h6 {
	{$fonts['h6_font-family']}
	{$fonts['h6_font-size']}
	{$fonts['h6_font-weight']}
	{$fonts['h6_font-style']}
	{$fonts['h6_line-height']}
	{$fonts['h6_text-decoration']}
	{$fonts['h6_text-transform']}
	{$fonts['h6_letter-spacing']}
	{$fonts['h6_margin-top']}
	{$fonts['h6_margin-bottom']}
}

input[type="text"],
input[type="number"],
input[type="email"],
input[type="url"],
input[type="tel"],
input[type="search"],
input[type="password"],
textarea,
textarea.wp-editor-area,
.select_container,
select,
.select_container select {
	{$fonts['input_font-family']}
	{$fonts['input_font-size']}
	{$fonts['input_font-weight']}
	{$fonts['input_font-style']}
	{$fonts['input_line-height']}
	{$fonts['input_text-decoration']}
	{$fonts['input_text-transform']}
	{$fonts['input_letter-spacing']}
}

.sc_item_pagination_load_more .nav-links,
.nav-links-more .nav-load-more,
.nav-links-more .woocommerce-load-more,
.woocommerce-links-more .woocommerce-load-more,
.sidebar_small_screen_above .sidebar_control,
.trx_addons_popup_form_field_submit .submit_button,
.simple_text_link,
.show_comments_single .show_comments_button,
form button:not(.components-button),
input[type="button"],
input[type="reset"],
input[type="submit"],
.theme_button,
.sc_layouts_row .sc_button,
.sc_portfolio_preview_show .post_readmore,
.wp-block-button__link,
.post_item .more-link,
div.esg-filter-wrapper .esg-filterbutton > span,
.mptt-navigation-tabs li a,
.planty_tabs .planty_tabs_titles li a {
	{$fonts['button_font-family']}
	{$fonts['button_font-size']}
	{$fonts['button_font-weight']}
	{$fonts['button_font-style']}
	{$fonts['button_line-height']}
	{$fonts['button_text-decoration']}
	{$fonts['button_text-transform']}
	{$fonts['button_letter-spacing']}
}

.adp-popup-type-notification-box .adp-popup-button,
.adp-popup-type-notification-bar .adp-popup-button,
#sb_instagram[data-shortcode-atts*="feedOne"] .sbi_follow_btn a,
#sb_instagram.feedOne .sbi_follow_btn a,
.post-more-link,
.nav-links-old,
.latepoint-book-button,
.round-square-2 .elementor-button {
	{$fonts['button_font-family']}
}

.top_panel .slider_engine_revo .slide_title {
	{$fonts['h1_font-family']}
}

blockquote {
	{$fonts['other_font-family']}
}

.sc_layouts_cart_items_short,
.sc_layouts_menu_nav > li[class*="columns-"] li.menu-item-has-children > a,
.sc_layouts_menu_nav li.menu-collapse li[class*="columns-"] li.menu-item-has-children > a,
.sticky_socials_wrap.sticky_socials_modern .social_item .social_name,
.search_modern .search_wrap .search_field,
.search_style_fullscreen.search_opened .search_field,
.comments_list_wrap .comment_reply,
.author_info .author_label,
.nav-links-single .nav-links .nav-arrow-label,
.post_item_single .post_tags_single a,
.sc_layouts_row_type_compact .sc_layouts_item_details,
.post_meta_item.post_categories,
div.esg-filters, .woocommerce nav.woocommerce-pagination ul, .comments_pagination, .nav-links, .page_links,
.wp-playlist.wp-audio-playlist .wp-playlist-tracks,
.wp-playlist.wp-audio-playlist .wp-playlist-item-title,
.mejs-container *,
.format-audio .post_featured .post_audio_author,
.single-format-audio .post_featured .post_audio_author,
.sc_layouts_blog_item_featured .post_featured .post_audio_author,
#powerTip .box_view_html,
.widget_product_tag_cloud, .widget_tag_cloud, .wp-block-tag-cloud,
.custom-html-widget .extra_item,
.post_meta_item.post_author,
.post_info_item.post_info_posted_by,
.post_info_item.post_categories,
table th,
mark, ins,
.logo_text,
.theme_button_close_text,
.post_price.price,
.theme_scroll_down,
.post_meta_item .post_sponsored_label,
 
.latepoint-lightbox-w h1,
.latepoint-lightbox-w h2,
.latepoint-lightbox-w h3,
.latepoint-lightbox-w h4,
.latepoint-lightbox-w h5,
.latepoint-lightbox-w h6,

.has-drop-cap:not(:focus):first-letter,

.widget_calendar caption,
.wp-block-calendar caption,

.sc_layouts_title .breadcrumbs,
blockquote > cite,
blockquote > p > cite,
blockquote > .wp-block-pullquote__citation,
.wp-block-quote .wp-block-quote__citation {
	{$fonts['h5_font-family']}
}

.post_meta {
	{$fonts['info_font-family']}
	{$fonts['info_font-size']}
	{$fonts['info_font-weight']}
	{$fonts['info_font-style']}
	{$fonts['info_line-height']}
	{$fonts['info_text-decoration']}
	{$fonts['info_text-transform']}
	{$fonts['info_letter-spacing']}
	{$fonts['info_margin-top']}
	{$fonts['info_margin-bottom']}
}

.post-date, .rss-date,
.post_date, .post_meta_item,
.post_meta .vc_inline-link,
.comments_list_wrap .comment_date,
.comments_list_wrap .comment_time,
.comments_list_wrap .comment_counters,
.top_panel .slider_engine_revo .slide_subtitle,
.logo_slogan,
.trx_addons_audio_player .audio_author,
.post_item_single .post_content .post_meta,
.author_bio .author_link,
.comments_list_wrap .comment_posted,
.comments_list_wrap .comment_reply {
	{$fonts['info_font-family']}
}

.wpgdprc,
option,
fieldset legend,
figure figcaption,
.wp-caption .wp-caption-text,
.wp-caption .wp-caption-dd,
.wp-caption-overlay .wp-caption .wp-caption-text,
.wp-caption-overlay .wp-caption .wp-caption-dd,

.wp-playlist.wp-audio-playlist .wp-playlist-tracks .wp-playlist-item-artist,
.backstage-customizer-access-wrapper .backstage-customizer-access-button,
.latepoint-w,
.search_wrap .search_results .post_meta_item {
	{$fonts['p_font-family']}
}

.logo_text {
	{$fonts['logo_font-family']}
	{$fonts['logo_font-size']}
	{$fonts['logo_font-weight']}
	{$fonts['logo_font-style']}
	{$fonts['logo_line-height']}
	{$fonts['logo_text-decoration']}
	{$fonts['logo_text-transform']}
	{$fonts['logo_letter-spacing']}
}
.logo_footer_text {
	{$fonts['logo_font-family']}
}

.sc_layouts_menu_dir_vertical.sc_layouts_submenu_dropdown .sc_layouts_menu_nav > li > ul {
	{$fonts['menu_font-family']}
}

.menu_main_nav_area > ul,
.sc_layouts_menu_nav,
.sc_layouts_menu_dir_vertical .sc_layouts_menu_nav {
	{$fonts['menu_font-family']}
	{$fonts['menu_font-size']}
	{$fonts['menu_line-height']}
}
.menu_main_nav > li > a,
.sc_layouts_menu_nav > li > a {
	{$fonts['menu_font-weight']}
	{$fonts['menu_font-style']}
	{$fonts['menu_text-decoration']}
	{$fonts['menu_text-transform']}
	{$fonts['menu_letter-spacing']}
}

.sc_layouts_menu_nav > li.current-menu-item > a,
.sc_layouts_menu_nav > li.current-menu-parent > a,
.sc_layouts_menu_nav > li.current-menu-ancestor > a,

.menu_main_nav > li[class*="current-menu-"] > a .sc_layouts_menu_item_description,
.sc_layouts_menu_nav > li[class*="current-menu-"] > a .sc_layouts_menu_item_description {
	{$fonts['menu_font-weight']}
}
.menu_main_nav > li > ul,
.sc_layouts_menu_nav > li > ul,
.sc_layouts_menu_popup .sc_layouts_menu_nav {
	{$fonts['submenu_font-family']}
	{$fonts['submenu_font-size']}
	{$fonts['submenu_line-height']}
}
.menu_main_nav > li ul > li > a,
.sc_layouts_menu_nav > li ul > li > a,
.sc_layouts_menu_popup .sc_layouts_menu_nav > li > a {
	{$fonts['submenu_font-weight']}
	{$fonts['submenu_font-style']}
	{$fonts['submenu_text-decoration']}
	{$fonts['submenu_text-transform']}
	{$fonts['submenu_letter-spacing']}
}
.sc_layouts_panel_menu .sc_layouts_menu_dir_horizontal .sc_layouts_menu_nav > ul,
.sc_layouts_panel_menu .sc_layouts_menu_dir_vertical.sc_layouts_submenu_dropdown > ul,
.menu_mobile .menu_mobile_nav_area > ul {
	{$fonts['menu_font-family']}
}
.sc_layouts_panel_menu .sc_layouts_menu_dir_horizontal .sc_layouts_menu_nav > li > ul,
.sc_layouts_panel_menu .sc_layouts_menu_dir_vertical.sc_layouts_submenu_dropdown > ul > li ul,
.menu_mobile .menu_mobile_nav_area > ul > li ul {
	{$fonts['submenu_font-family']}
}
CSS;
			$rez          = apply_filters( 'planty_filter_get_css', $rez, array( 'fonts' => $fonts ) );
			$css['fonts'] = $rez['fonts'];
		}

		// Theme vars
		//---------------------------------------------
		if ( null === $vars ) {
			$vars = planty_get_theme_vars();
		}

		if ( $vars ) {

			// Make theme-specific vars
			$vars = planty_customizer_add_theme_vars( $vars );

			$rez         = array();
			$rez['vars'] = '';
			$rez         = apply_filters( 'planty_filter_get_css', $rez, array( 'vars' => $vars ) );
			$css['vars'] = $rez['vars'];
		}

		// Theme colors
		//--------------------------------------
		if ( false !== $colors ) {
			$schemes = empty( $scheme ) ? array_keys( planty_get_sorted_schemes() ) : array( $scheme );
			if ( count( $schemes ) > 0 ) {
				$rez = array();
				foreach ( $schemes as $s ) {
					// Prepare colors
					if ( empty( $scheme ) ) {
						$colors = planty_get_scheme_colors( $s );
					}

					// Make theme-specific colors and tints
					$colors         = planty_customizer_add_theme_colors( $colors );
					$rez['colors']  = '';
					$rez            = apply_filters(
						'planty_filter_get_css', $rez, array(
							'colors' => $colors,
							'scheme' => $s,
						)
					);
					$css['colors'] .= $rez['colors'];
				}
			}
		}

		$css_str = ( ! empty( $css['vars'] ) ? $css['vars'] : '' )
				. ( ! empty( $css['fonts'] ) ? $css['fonts'] : '' )
				. ( ! empty( $css['colors'] ) ? $css['colors'] : '' );

		return apply_filters( 'planty_filter_prepare_css', $css_str, $remove_spaces );
	}
}
