<?php
/**
 * The template to display default site footer
 *
 * @package PLANTY
 * @since PLANTY 1.0.10
 */

$planty_footer_id = planty_get_custom_footer_id();
$planty_footer_meta = get_post_meta( $planty_footer_id, 'trx_addons_options', true );
if ( ! empty( $planty_footer_meta['margin'] ) ) {
	planty_add_inline_css( sprintf( '.page_content_wrap{padding-bottom:%s}', esc_attr( planty_prepare_css_value( $planty_footer_meta['margin'] ) ) ) );
}
?>
<footer class="footer_wrap footer_custom footer_custom_<?php echo esc_attr( $planty_footer_id ); ?> footer_custom_<?php echo esc_attr( sanitize_title( get_the_title( $planty_footer_id ) ) ); ?>
						<?php
						$planty_footer_scheme = planty_get_theme_option( 'footer_scheme' );
						if ( ! empty( $planty_footer_scheme ) && ! planty_is_inherit( $planty_footer_scheme  ) ) {
							echo ' scheme_' . esc_attr( $planty_footer_scheme );
						}
						?>
						">
	<?php
	// Custom footer's layout
	do_action( 'planty_action_show_layout', $planty_footer_id );
	?>
</footer><!-- /.footer_wrap -->
