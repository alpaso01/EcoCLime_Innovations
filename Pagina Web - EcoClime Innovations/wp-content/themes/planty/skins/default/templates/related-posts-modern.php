<?php
/**
 * The template 'Style 1' to displaying related posts
 *
 * @package PLANTY
 * @since PLANTY 1.0
 */

$planty_link        = get_permalink();
$planty_post_format = get_post_format();
$planty_post_format = empty( $planty_post_format ) ? 'standard' : str_replace( 'post-format-', '', $planty_post_format );
?><div id="post-<?php the_ID(); ?>" <?php post_class( 'related_item post_format_' . esc_attr( $planty_post_format ) ); ?> data-post-id="<?php the_ID(); ?>">
	<?php
	planty_show_post_featured(
		array(
			'thumb_size'    => apply_filters( 'planty_filter_related_thumb_size', planty_get_thumb_size( (int) planty_get_theme_option( 'related_posts' ) == 1 ? 'huge' : 'big' ) ),
			'post_info'     => '<div class="post_header entry-header">'
									. '<div class="post_categories">' . wp_kses( planty_get_post_categories( '' ), 'planty_kses_content' ) . '</div>'
									. '<h6 class="post_title entry-title"><a href="' . esc_url( $planty_link ) . '">'
										. wp_kses_data( '' == get_the_title() ? esc_html__( '- No title -', 'planty' ) : get_the_title() )
									. '</a></h6>'
									. ( in_array( get_post_type(), array( 'post', 'attachment' ) )
											? '<div class="post_meta"><a href="' . esc_url( $planty_link ) . '" class="post_meta_item post_date">' . wp_kses_data( planty_get_date() ) . '</a></div>'
											: '' )
								. '</div>',
		)
	);
	?>
</div>
