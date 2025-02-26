<?php
/**
 * The style "default" of the Blogger
 *
 * @package ThemeREX Addons
 * @since v1.2
 */

$args = get_query_var('trx_addons_args_sc_blogger');

$templates = trx_addons_components_get_allowed_templates('sc', 'blogger');
$template  = ! empty( $args['template_'.$args['type']] ) && isset($templates[$args['type']][$args['template_'.$args['type']]])
				? $templates[$args['type']][$args['template_'.$args['type']]]
				: $templates['default']['classic'];

// Override the shortcode's args from the template parameter 'args'
if ( !empty($template['args']) && is_array($template['args']) ) {
	$args = array_merge( $args, $template['args'] );
}

$posts_in_grid = isset($template['grid']) ? count($template['grid']) : 0;

$query_args = array(
// Attention! Parameter 'suppress_filters' is damage WPML-queries!
	'post_status' => 'publish',
	'ignore_sticky_posts' => true
);

// Posts per page
if ( empty( $args['ids'] ) ) {
	$query_args['posts_per_page'] = $args['count'];
	if ( ! trx_addons_is_off($args['pagination']) && $args['page'] > 1 ) {
		if ( empty( $args['offset'] ) ) {
			$query_args['paged'] = $args['page'];
		} else {
			$query_args['offset'] = $args['offset'] + $args['count'] * ( $args['page'] - 1 );
		}
	} else {
		$query_args['offset'] = $args['offset'];
	}
}

// Post type
$query_args = trx_addons_query_add_posts_and_cats($query_args, $args['ids'], $args['post_type']);

// Sort order
$query_args = trx_addons_query_add_sort_order($query_args, $args['orderby'], $args['order']);

// Filters
$tabs = trx_addons_sc_get_filters_tabs('sc_blogger', $args);
if (count($tabs) > 0 && !empty($args['filters_active']) && $args['filters_active'] != 'all') {
	$query_args = trx_addons_query_add_posts_and_cats($query_args, '', '', $args['filters_active'], $args['filters_taxonomy']);
} else if ( empty($args['ids']) ) {
	$query_args = trx_addons_query_add_posts_and_cats($query_args, '', '', $args['cat'], $args['taxonomy']);
}

$query_args = apply_filters( 'trx_addons_filter_query_args', $query_args, 'sc_blogger' );

$query = new WP_Query( $query_args );

if ( $query->post_count > 0 || count($tabs) > 0 ) {

	$posts_count = ($args['count'] > $query->post_count) ? $query->post_count : $args['count'];

	$args['slides_space'] = max(0, (int) $args['slides_space']);

	if ( empty($args['template_' . $args['type']]) ) {
		$args['template_' . $args['type']] = 'default';
	}

	?><div <?php if ( ! empty( $args['id'] ) ) echo ' id="' . esc_attr( $args['id'] ) . '"'; ?>
		class="sc_blogger sc_blogger_<?php
			echo esc_attr( $args['type'] );
			echo ' sc_blogger_' . esc_attr( $args['type'] ) . '_' . esc_attr( $args[ 'template_' . $args['type'] ] );
			echo ' sc_item_filters_tabs_' . esc_attr( count( $tabs ) > 0 ? $args['filters_tabs_position'] : 'none' );
			if ( ! empty( $args['align'] ) && ( ! function_exists( 'trx_addons_gutenberg_is_preview' ) || ! trx_addons_gutenberg_is_preview() ) ) echo ' align' . esc_attr( $args['align'] ); 
			if ( ! empty( $args['class'] ) ) echo ' ' . esc_attr( $args['class'] ); 
			?>"<?php
		if ( ! empty( $args['css'] ) ) echo ' style="' . esc_attr( $args['css'] ) . '"';
		trx_addons_sc_show_attributes('sc_blogger', $args, 'sc_wrapper');
		?>><?php
        ?><div class="decor_image_wrap"><?php
        ?></div><?php

        ?><div class="portfolio_preview scheme_dark">
            <div class="inner_preview">
                <div class="close"></div>
                <div class="portfolio_image"></div>
                <div class="portfolio_description"></div>
            </div>
        </div><?php

		// Show titles
		trx_addons_sc_show_titles('sc_blogger', $args);

		// Show filters
		if ( count($tabs) > 0 ) {
			?><div class="sc_item_filters_wrap"><?php
		}
		trx_addons_sc_show_filters('sc_blogger', $args, $tabs);

		// Shortcode's wrapper
        ?><div class="sc_blogger_content sc_item_content sc_item_posts_container<?php
        if ( ! empty($args['posts_container']) ) {
            echo ' '.esc_attr($args['posts_container']);
        }

        ?>"<?php trx_addons_sc_show_attributes('sc_blogger', $args, 'sc_items_wrapper'); ?>><?php

		$args['item_number'] = 0;
		$posts_rest = $posts_count;

		$add_html = '';

		while ( $query->have_posts() ) { $query->the_post();

			$args['item_number']++;
			
			$args_orig = $args;		// Save original shortcode's agruments
			
			if ( $posts_in_grid > 0 ) {
				if ( ($args['item_number'] - 1) % $posts_in_grid == 0 ) {
					$posts_rest = $posts_count - $args['item_number'] + 1;
				}
				$grid_num = min( $posts_rest, $posts_in_grid );
				$grid_subnum = ($args['item_number'] - 1) % $posts_in_grid;
				$grid_layout = explode('/', str_replace(' ', '', $template['grid'][$grid_num-1]['grid-layout'][$grid_subnum]['template']));
				$args['grid'] = $posts_in_grid;
				$args['type'] = $grid_layout[0];
				$args['template_'.$grid_layout[0]] = $grid_layout[1];
				if ( ! empty($template['grid'][$grid_num-1]['grid-layout'][$grid_subnum]['args']) ) {
					$args = array_merge( $args, $template['grid'][$grid_num-1]['grid-layout'][$grid_subnum]['args'] );
				}
				if ( ($args['item_number'] - 1) % $posts_in_grid == 0 ) {
					if ($args['item_number'] > 1) {
						?></div>
                            </div><?php
					}
					?><div class="sc_blogger_grid_wrap sc_blogger_grid_<?php
						echo esc_attr( $grid_num );
					?>"<?php trx_addons_sc_show_attributes('sc_blogger', $args, 'sc_grid_wrapper'); ?>><?php
				}
			}
			
			if ( ! apply_filters('trx_addons_filter_sc_blogger_template', false, $args) ) {
				$args['item_number'] += !trx_addons_is_off($args['pagination']) && $args['page'] > 1 ? ( $args['page'] - 1 ) * $args['count'] : 0;
				trx_addons_get_template_part(array(
											TRX_ADDONS_PLUGIN_SHORTCODES . 'blogger/tpl.'.trx_addons_esc($args['type']).'-item.php',
											TRX_ADDONS_PLUGIN_SHORTCODES . 'blogger/tpl.default-item.php'
											),
											'trx_addons_args_sc_blogger', 
											$args
										);
			}

			$args = $args_orig;		// Restore original shortcode's arguments

			if ( $args['type'] == 'panel' ) {
				$thumb_class = 'without_image';
				$featured_args = apply_filters( 'trx_addons_filter_args_featured', array(), 'blogger-'.$args['type'], $args );
				if ( ! empty($featured_args['autoplay']) ) {
					if ( empty($featured_args['video']) ) {
						$post_meta = get_post_meta( get_the_ID(), 'trx_addons_options', true );
						$key = 'video_autoplay' . ( is_single() ? '' : '_archive' );
						if ( ! empty( $post_meta[$key] ) && ! empty( $post_meta['video_list'] ) && is_array( $post_meta['video_list'] ) && count( $post_meta['video_list'] ) > 0 ) {
							$featured_args['video'] = trx_addons_get_video_layout( array(
																						'link' => $post_meta['video_list'][0]['video_url'],
																						'embed' => $post_meta['video_list'][0]['video_embed'],
																						'autoplay' => true,
																						'mute' => true,
																						'show_cover' => false
																						)
																				);
						}
					}
					if ( ! empty($featured_args['video']) ) {
						$thumb_class = 'with_image with_video';
					}
				} else {
					$thumb_id = get_post_thumbnail_id( get_the_ID() );
					if ( ! empty( $thumb_id )) {
						$image = wp_get_attachment_image_src( $thumb_id, 'full' );
						$thumb_class = 'with_image ' . trx_addons_add_inline_css_class( 'background-image: url(' . esc_url( $image[0] ) . ');' );
					}
				}
				if ( empty( $add_html ) ) {
					$thumb_class .= ' sc_panel_thumb_active';
				}
				$add_html .= '<div class="sc_panel_thumb ' . esc_attr( $thumb_class ) . '"'
								. ' data-thumb-number="' . esc_attr($args['item_number']) . '"'
							. '>'
								. ( ! empty($featured_args['video']) ? $featured_args['video'] : '' )
							. '</div>';
			}
		}

		wp_reset_postdata();

		trx_addons_show_layout( $add_html, '<div class="sc_blogger_panel_thumbs">', '</div>' );

		if ( $posts_in_grid > 0 ) {
			?></div>
            </div><?php
		}

		?></div><?php
		trx_addons_sc_show_pagination('sc_blogger', $args, $query);

		if ( count($tabs) > 0 ) {
			?></div><!-- /.sc_item_filters_wrap --><?php
		}

		trx_addons_sc_show_links('sc_blogger', $args);

	?></div><!-- /.sc_blogger --><?php
}
