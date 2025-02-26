<?php
/**
 * The template to display default site footer
 *
 * @package PLANTY
 * @since PLANTY 1.0.10
 */

?>
<footer class="footer_wrap footer_default
<?php
$planty_footer_scheme = planty_get_theme_option( 'footer_scheme' );
if ( ! empty( $planty_footer_scheme ) && ! planty_is_inherit( $planty_footer_scheme  ) ) {
	echo ' scheme_' . esc_attr( $planty_footer_scheme );
}
?>
				">
	<?php

	// Footer widgets area
	get_template_part( apply_filters( 'planty_filter_get_template_part', 'templates/footer-widgets' ) );

	// Logo
	get_template_part( apply_filters( 'planty_filter_get_template_part', 'templates/footer-logo' ) );

	// Socials
	get_template_part( apply_filters( 'planty_filter_get_template_part', 'templates/footer-socials' ) );

	// Copyright area
	get_template_part( apply_filters( 'planty_filter_get_template_part', 'templates/footer-copyright' ) );

	?>
</footer><!-- /.footer_wrap -->
