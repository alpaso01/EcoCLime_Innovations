<?php
/**
 * The default template to display the content
 *
 * Used for index/archive/search.
 *
 * @package PLANTY
 * @since PLANTY 1.0
 */

$planty_template_args = get_query_var( 'planty_template_args' );
$planty_columns = 1;
if ( is_array( $planty_template_args ) ) {
	$planty_columns    = empty( $planty_template_args['columns'] ) ? 1 : max( 1, $planty_template_args['columns'] );
	$planty_blog_style = array( $planty_template_args['type'], $planty_columns );
	if ( ! empty( $planty_template_args['slider'] ) ) {
		?><div class="slider-slide swiper-slide">
		<?php
	} elseif ( $planty_columns > 1 ) {
	    $planty_columns_class = planty_get_column_class( 1, $planty_columns, ! empty( $planty_template_args['columns_tablet']) ? $planty_template_args['columns_tablet'] : '', ! empty($planty_template_args['columns_mobile']) ? $planty_template_args['columns_mobile'] : '' );
		?>
		<div class="<?php echo esc_attr( $planty_columns_class ); ?>">
		<?php
	}
} else {
	$planty_template_args = array();
}
$planty_expanded    = ! planty_sidebar_present() && planty_get_theme_option( 'expand_content' ) == 'expand';
$planty_post_format = get_post_format();
$planty_post_format = empty( $planty_post_format ) ? 'standard' : str_replace( 'post-format-', '', $planty_post_format );
?>
<article id="post-<?php the_ID(); ?>" data-post-id="<?php the_ID(); ?>"
	<?php
	post_class( 'post_item post_item_container post_layout_excerpt post_format_' . esc_attr( $planty_post_format ) );
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
								: array_map( 'trim', explode( ',', $planty_template_args['meta_parts'] ) )
								)
							: planty_array_get_keys_by_value( planty_get_theme_option( 'meta_parts' ) );
	planty_show_post_featured( apply_filters( 'planty_filter_args_featured',
		array(
			'no_links'   => ! empty( $planty_template_args['no_links'] ),
			'hover'      => $planty_hover,
			'meta_parts' => $planty_components,
			'thumb_size' => ! empty( $planty_template_args['thumb_size'] )
							? $planty_template_args['thumb_size']
							: planty_get_thumb_size( strpos( planty_get_theme_option( 'body_style' ), 'full' ) !== false
								? 'full'
								: ( $planty_expanded 
									? 'huge' 
									: 'big' 
									)
								),
		),
		'content-excerpt',
		$planty_template_args
	) );

	// Title and post meta
	$planty_show_title = get_the_title() != '';
	$planty_show_meta  = count( $planty_components ) > 0 && ! in_array( $planty_hover, array( 'border', 'pull', 'slide', 'fade', 'info' ) );

	if ( $planty_show_title ) {
		?>
		<div class="post_header entry-header">
			<?php
			// Post title
			if ( apply_filters( 'planty_filter_show_blog_title', true, 'excerpt' ) ) {
				do_action( 'planty_action_before_post_title' );
				if ( empty( $planty_template_args['no_links'] ) ) {
					the_title( sprintf( '<h3 class="post_title entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h3>' );
				} else {
					the_title( '<h3 class="post_title entry-title">', '</h3>' );
				}
				do_action( 'planty_action_after_post_title' );
			}
			?>
		</div><!-- .post_header -->
		<?php
	}

	// Post content
	if ( apply_filters( 'planty_filter_show_blog_excerpt', empty( $planty_template_args['hide_excerpt'] ) && planty_get_theme_option( 'excerpt_length' ) > 0, 'excerpt' ) ) {
		?>
		<div class="post_content entry-content">
			<?php

			// Post meta
			if ( apply_filters( 'planty_filter_show_blog_meta', $planty_show_meta, $planty_components, 'excerpt' ) ) {
				if ( count( $planty_components ) > 0 ) {
					do_action( 'planty_action_before_post_meta' );
					planty_show_post_meta(
						apply_filters(
							'planty_filter_post_meta_args', array(
								'components' => join( ',', $planty_components ),
								'seo'        => false,
								'echo'       => true,
							), 'excerpt', 1
						)
					);
					do_action( 'planty_action_after_post_meta' );
				}
			}

			if ( planty_get_theme_option( 'blog_content' ) == 'fullpost' ) {
				// Post content area
				?>
				<div class="post_content_inner">
					<?php
					do_action( 'planty_action_before_full_post_content' );
					the_content( '' );
					do_action( 'planty_action_after_full_post_content' );
					?>
				</div>
				<?php
				// Inner pages
				wp_link_pages(
					array(
						'before'      => '<div class="page_links"><span class="page_links_title">' . esc_html__( 'Pages:', 'planty' ) . '</span>',
						'after'       => '</div>',
						'link_before' => '<span>',
						'link_after'  => '</span>',
						'pagelink'    => '<span class="screen-reader-text">' . esc_html__( 'Page', 'planty' ) . ' </span>%',
						'separator'   => '<span class="screen-reader-text">, </span>',
					)
				);
			} else {
				// Post content area
				planty_show_post_content( $planty_template_args, '<div class="post_content_inner">', '</div>' );
			}

			// More button
			if ( apply_filters( 'planty_filter_show_blog_readmore',  ! isset( $planty_template_args['more_button'] ) || ! empty( $planty_template_args['more_button'] ), 'excerpt' ) ) {
				if ( empty( $planty_template_args['no_links'] ) ) {
					do_action( 'planty_action_before_post_readmore' );
					if ( planty_get_theme_option( 'blog_content' ) != 'fullpost' ) {
						planty_show_post_more_link( $planty_template_args, '<p>', '</p>' );
					} else {
						planty_show_post_comments_link( $planty_template_args, '<p>', '</p>' );
					}
					do_action( 'planty_action_after_post_readmore' );
				}
			}

			?>
		</div><!-- .entry-content -->
		<?php
	}
	?>
</article>
<?php

if ( is_array( $planty_template_args ) ) {
	if ( ! empty( $planty_template_args['slider'] ) || $planty_columns > 1 ) {
		?>
		</div>
		<?php
	}
}
