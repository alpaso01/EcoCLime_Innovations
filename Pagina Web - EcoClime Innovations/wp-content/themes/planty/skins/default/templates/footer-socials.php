<?php
/**
 * The template to display the socials in the footer
 *
 * @package PLANTY
 * @since PLANTY 1.0.10
 */


// Socials
if ( planty_is_on( planty_get_theme_option( 'socials_in_footer' ) ) ) {
	$planty_output = planty_get_socials_links();
	if ( '' != $planty_output ) {
		?>
		<div class="footer_socials_wrap socials_wrap">
			<div class="footer_socials_inner">
				<?php planty_show_layout( $planty_output ); ?>
			</div>
		</div>
		<?php
	}
}
