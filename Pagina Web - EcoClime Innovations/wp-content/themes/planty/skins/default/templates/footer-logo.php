<?php
/**
 * The template to display the site logo in the footer
 *
 * @package PLANTY
 * @since PLANTY 1.0.10
 */

// Logo
if ( planty_is_on( planty_get_theme_option( 'logo_in_footer' ) ) ) {
	$planty_logo_image = planty_get_logo_image( 'footer' );
	$planty_logo_text  = get_bloginfo( 'name' );
	if ( ! empty( $planty_logo_image['logo'] ) || ! empty( $planty_logo_text ) ) {
		?>
		<div class="footer_logo_wrap">
			<div class="footer_logo_inner">
				<?php
				if ( ! empty( $planty_logo_image['logo'] ) ) {
					$planty_attr = planty_getimagesize( $planty_logo_image['logo'] );
					echo '<a href="' . esc_url( home_url( '/' ) ) . '">'
							. '<img src="' . esc_url( $planty_logo_image['logo'] ) . '"'
								. ( ! empty( $planty_logo_image['logo_retina'] ) ? ' srcset="' . esc_url( $planty_logo_image['logo_retina'] ) . ' 2x"' : '' )
								. ' class="logo_footer_image"'
								. ' alt="' . esc_attr__( 'Site logo', 'planty' ) . '"'
								. ( ! empty( $planty_attr[3] ) ? ' ' . wp_kses_data( $planty_attr[3] ) : '' )
							. '>'
						. '</a>';
				} elseif ( ! empty( $planty_logo_text ) ) {
					echo '<h1 class="logo_footer_text">'
							. '<a href="' . esc_url( home_url( '/' ) ) . '">'
								. esc_html( $planty_logo_text )
							. '</a>'
						. '</h1>';
				}
				?>
			</div>
		</div>
		<?php
	}
}
