<?php
/**
 * The template to display the copyright info in the footer
 *
 * @package PLANTY
 * @since PLANTY 1.0.10
 */

// Copyright area
?> 
<div class="footer_copyright_wrap
<?php
$planty_copyright_scheme = planty_get_theme_option( 'copyright_scheme' );
if ( ! empty( $planty_copyright_scheme ) && ! planty_is_inherit( $planty_copyright_scheme  ) ) {
	echo ' scheme_' . esc_attr( $planty_copyright_scheme );
}
?>
				">
	<div class="footer_copyright_inner">
		<div class="content_wrap">
			<div class="copyright_text">
			<?php
				$planty_copyright = planty_get_theme_option( 'copyright' );
			if ( ! empty( $planty_copyright ) ) {
				// Replace {{Y}} or {Y} with the current year
				$planty_copyright = str_replace( array( '{{Y}}', '{Y}' ), date( 'Y' ), $planty_copyright );
				// Replace {{...}} and ((...)) on the <i>...</i> and <b>...</b>
				$planty_copyright = planty_prepare_macros( $planty_copyright );
				// Display copyright
				echo wp_kses( nl2br( $planty_copyright ), 'planty_kses_content' );
			}
			?>
			</div>
		</div>
	</div>
</div>
