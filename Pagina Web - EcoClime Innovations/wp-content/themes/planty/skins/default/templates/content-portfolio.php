<?php
/**
 * The Portfolio template to display the content
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

$planty_post_format = get_post_format();
$planty_post_format = empty( $planty_post_format ) ? 'standard' : str_replace( 'post-format-', '', $planty_post_format );

?><div class="
<?php
if ( ! empty( $planty_template_args['slider'] ) ) {
	echo ' slider-slide swiper-slide';
} else {
	echo ( planty_is_blog_style_use_masonry( $planty_blog_style[0] ) ? 'masonry_item masonry_item-1_' . esc_attr( $planty_columns ) : esc_attr( $planty_columns_class ));
}
?>
"><article id="post-<?php the_ID(); ?>" 
	<?php
	post_class(
		'post_item post_item_container post_format_' . esc_attr( $planty_post_format )
		. ' post_layout_portfolio'
		. ' post_layout_portfolio_' . esc_attr( $planty_columns )
		. ( 'portfolio' != $planty_blog_style[0] ? ' ' . esc_attr( $planty_blog_style[0] )  . '_' . esc_attr( $planty_columns ) : '' )
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

	$planty_hover   = ! empty( $planty_template_args['hover'] ) && ! planty_is_inherit( $planty_template_args['hover'] )
								? $planty_template_args['hover']
								: planty_get_theme_option( 'image_hover' );

	if ( 'dots' == $planty_hover ) {
		$planty_post_link = empty( $planty_template_args['no_links'] )
								? ( ! empty( $planty_template_args['link'] )
									? $planty_template_args['link']
									: get_permalink()
									)
								: '';
		$planty_target    = ! empty( $planty_post_link ) && false === strpos( $planty_post_link, home_url() )
								? ' target="_blank" rel="nofollow"'
								: '';
	}
	
	// Meta parts
	$planty_components = ! empty( $planty_template_args['meta_parts'] )
							? ( is_array( $planty_template_args['meta_parts'] )
								? $planty_template_args['meta_parts']
								: explode( ',', $planty_template_args['meta_parts'] )
								)
							: planty_array_get_keys_by_value( planty_get_theme_option( 'meta_parts' ) );

	// Featured image
	planty_show_post_featured( apply_filters( 'planty_filter_args_featured',
        array(
			'hover'         => $planty_hover,
			'no_links'      => ! empty( $planty_template_args['no_links'] ),
			'thumb_size'    => ! empty( $planty_template_args['thumb_size'] )
								? $planty_template_args['thumb_size']
								: planty_get_thumb_size(
									planty_is_blog_style_use_masonry( $planty_blog_style[0] )
										? (	strpos( planty_get_theme_option( 'body_style' ), 'full' ) !== false || $planty_columns < 3
											? 'masonry-big'
											: 'masonry'
											)
										: (	strpos( planty_get_theme_option( 'body_style' ), 'full' ) !== false || $planty_columns < 3
											? 'square'
											: 'square'
											)
								),
			'thumb_bg' => planty_is_blog_style_use_masonry( $planty_blog_style[0] ) ? false : true,
			'show_no_image' => true,
			'meta_parts'    => $planty_components,
			'class'         => 'dots' == $planty_hover ? 'hover_with_info' : '',
			'post_info'     => 'dots' == $planty_hover
										? '<div class="post_info"><h5 class="post_title">'
											. ( ! empty( $planty_post_link )
												? '<a href="' . esc_url( $planty_post_link ) . '"' . ( ! empty( $target ) ? $target : '' ) . '>'
												: ''
												)
												. esc_html( get_the_title() ) 
											. ( ! empty( $planty_post_link )
												? '</a>'
												: ''
												)
											. '</h5></div>'
										: '',
            'thumb_ratio'   => 'info' == $planty_hover ?  '100:102' : '',
        ),
        'content-portfolio',
        $planty_template_args
    ) );
	?>
</article></div><?php
// Need opening PHP-tag above, because <article> is a inline-block element (used as column)!