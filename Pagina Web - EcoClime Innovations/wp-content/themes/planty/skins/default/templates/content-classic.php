<?php
/**
 * The Classic template to display the content
 *
 * Used for index/archive/search.
 *
 * @package PLANTY
 * @since PLANTY 1.0
 */

$planty_template_args = get_query_var( 'planty_template_args' );

if ( is_array( $planty_template_args ) ) {
	$planty_columns    = empty( $planty_template_args['columns'] ) ? 2 : max( 1, $planty_template_args['columns'] );
	$planty_blog_style = array( $planty_template_args['type'], $planty_columns );
    $planty_columns_class = planty_get_column_class( 1, $planty_columns, ! empty( $planty_template_args['columns_tablet']) ? $planty_template_args['columns_tablet'] : '', ! empty($planty_template_args['columns_mobile']) ? $planty_template_args['columns_mobile'] : '' );
} else {
	$planty_template_args = array();
	$planty_blog_style = explode( '_', planty_get_theme_option( 'blog_style' ) );
	$planty_columns    = empty( $planty_blog_style[1] ) ? 2 : max( 1, $planty_blog_style[1] );
    $planty_columns_class = planty_get_column_class( 1, $planty_columns );
}
$planty_expanded   = ! planty_sidebar_present() && planty_get_theme_option( 'expand_content' ) == 'expand';

$planty_post_format = get_post_format();
$planty_post_format = empty( $planty_post_format ) ? 'standard' : str_replace( 'post-format-', '', $planty_post_format );

?><div class="<?php
	if ( ! empty( $planty_template_args['slider'] ) ) {
		echo ' slider-slide swiper-slide';
	} else {
		echo ( planty_is_blog_style_use_masonry( $planty_blog_style[0] ) ? 'masonry_item masonry_item-1_' . esc_attr( $planty_columns ) : esc_attr( $planty_columns_class ) );
	}
?>"><article id="post-<?php the_ID(); ?>" data-post-id="<?php the_ID(); ?>"
	<?php
	post_class(
		'post_item post_item_container post_format_' . esc_attr( $planty_post_format )
				. ' post_layout_classic post_layout_classic_' . esc_attr( $planty_columns )
				. ' post_layout_' . esc_attr( $planty_blog_style[0] )
				. ' post_layout_' . esc_attr( $planty_blog_style[0] ) . '_' . esc_attr( $planty_columns )
	);
	planty_add_blog_animation( $planty_template_args );
	?>
>
	<?php

	// Sticky label
	if ( is_sticky() && ! is_paged() ) {
		?>
		<span class="post_label label_sticky"></span>
		<?php
	}

	// Featured image
	$planty_hover      = ! empty( $planty_template_args['hover'] ) && ! planty_is_inherit( $planty_template_args['hover'] )
							? $planty_template_args['hover']
							: planty_get_theme_option( 'image_hover' );

	$planty_components = ! empty( $planty_template_args['meta_parts'] )
							? ( is_array( $planty_template_args['meta_parts'] )
								? $planty_template_args['meta_parts']
								: explode( ',', $planty_template_args['meta_parts'] )
								)
							: planty_array_get_keys_by_value( planty_get_theme_option( 'meta_parts' ) );

	planty_show_post_featured( apply_filters( 'planty_filter_args_featured',
		array(
			'thumb_size' => ! empty( $planty_template_args['thumb_size'] )
				? $planty_template_args['thumb_size']
				: planty_get_thumb_size(
					'classic' == $planty_blog_style[0]
						? ( strpos( planty_get_theme_option( 'body_style' ), 'full' ) !== false
								? ( $planty_columns > 2 ? 'big' : 'huge' )
								: ( $planty_columns > 2
									? ( $planty_expanded ? 'square' : 'square' )
									: ($planty_columns > 1 ? 'square' : ( $planty_expanded ? 'huge' : 'big' ))
									)
							)
						: ( strpos( planty_get_theme_option( 'body_style' ), 'full' ) !== false
								? ( $planty_columns > 2 ? 'masonry-big' : 'full' )
								: ($planty_columns === 1 ? ( $planty_expanded ? 'huge' : 'big' ) : ( $planty_columns <= 2 && $planty_expanded ? 'masonry-big' : 'masonry' ))
							)
			),
			'hover'      => $planty_hover,
			'meta_parts' => $planty_components,
			'no_links'   => ! empty( $planty_template_args['no_links'] ),
        ),
        'content-classic',
        $planty_template_args
    ) );

	// Title and post meta
	$planty_show_title = get_the_title() != '';
	$planty_show_meta  = count( $planty_components ) > 0 && ! in_array( $planty_hover, array( 'border', 'pull', 'slide', 'fade', 'info' ) );

	if ( $planty_show_title ) {
		?>
		<div class="post_header entry-header">
			<?php

			// Post meta
			if ( apply_filters( 'planty_filter_show_blog_meta', $planty_show_meta, $planty_components, 'classic' ) ) {
				if ( count( $planty_components ) > 0 ) {
					do_action( 'planty_action_before_post_meta' );
					planty_show_post_meta(
						apply_filters(
							'planty_filter_post_meta_args', array(
							'components' => join( ',', $planty_components ),
							'seo'        => false,
							'echo'       => true,
						), $planty_blog_style[0], $planty_columns
						)
					);
					do_action( 'planty_action_after_post_meta' );
				}
			}

			// Post title
			if ( apply_filters( 'planty_filter_show_blog_title', true, 'classic' ) ) {
				do_action( 'planty_action_before_post_title' );
				if ( empty( $planty_template_args['no_links'] ) ) {
					the_title( sprintf( '<h4 class="post_title entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h4>' );
				} else {
					the_title( '<h4 class="post_title entry-title">', '</h4>' );
				}
				do_action( 'planty_action_after_post_title' );
			}

			if( !in_array( $planty_post_format, array( 'quote', 'aside', 'link', 'status' ) ) ) {
				// More button
				if ( apply_filters( 'planty_filter_show_blog_readmore', ! $planty_show_title || ! empty( $planty_template_args['more_button'] ), 'classic' ) ) {
					if ( empty( $planty_template_args['no_links'] ) ) {
						do_action( 'planty_action_before_post_readmore' );
						planty_show_post_more_link( $planty_template_args, '<div class="more-wrap">', '</div>' );
						do_action( 'planty_action_after_post_readmore' );
					}
				}
			}
			?>
		</div><!-- .entry-header -->
		<?php
	}

	// Post content
	if( in_array( $planty_post_format, array( 'quote', 'aside', 'link', 'status' ) ) ) {
		ob_start();
		if (apply_filters('planty_filter_show_blog_excerpt', empty($planty_template_args['hide_excerpt']) && planty_get_theme_option('excerpt_length') > 0, 'classic')) {
			planty_show_post_content($planty_template_args, '<div class="post_content_inner">', '</div>');
		}
		// More button
		if(! empty( $planty_template_args['more_button'] )) {
			if ( empty( $planty_template_args['no_links'] ) ) {
				do_action( 'planty_action_before_post_readmore' );
				planty_show_post_more_link( $planty_template_args, '<div class="more-wrap">', '</div>' );
				do_action( 'planty_action_after_post_readmore' );
			}
		}
		$planty_content = ob_get_contents();
		ob_end_clean();
		planty_show_layout($planty_content, '<div class="post_content entry-content">', '</div><!-- .entry-content -->');
	}
	?>

</article></div><?php
// Need opening PHP-tag above, because <div> is a inline-block element (used as column)!
