<?php
/**
 * The default template to displaying related posts
 *
 * @package PLANTY
 * @since PLANTY 1.0.54
 */

$planty_link        = get_permalink();
$planty_post_format = get_post_format();
$planty_post_format = empty( $planty_post_format ) ? 'standard' : str_replace( 'post-format-', '', $planty_post_format );
?><div id="post-<?php the_ID(); ?>" <?php post_class( 'related_item post_format_' . esc_attr( $planty_post_format ) ); ?> data-post-id="<?php the_ID(); ?>">
	<?php
	planty_show_post_featured(
		array(
			'thumb_size' => apply_filters( 'planty_filter_related_thumb_size', planty_get_thumb_size( (int) planty_get_theme_option( 'related_posts' ) == 1 ? 'huge' : 'big' ) ),
		)
	);
	?>
	<div class="post_header entry-header">
		<h6 class="post_title entry-title"><a href="<?php echo esc_url( $planty_link ); ?>"><?php
			if ( '' == get_the_title() ) {
				esc_html_e( '- No title -', 'planty' );
			} else {
				the_title();
			}
		?></a></h6>
		<?php
		if ( in_array( get_post_type(), array( 'post', 'attachment' ) ) ) {
			?>
			<span class="post_date"><a href="<?php echo esc_url( $planty_link ); ?>"><?php echo wp_kses_data( planty_get_date() ); ?></a></span>
			<?php
		}
		?>
	</div>
</div>
