<?php
/**
 * The template to display the logo or the site name and the slogan in the Header
 *
 * @package PLANTY
 * @since PLANTY 1.0
 */

$planty_args = get_query_var( 'planty_logo_args' );

// Site logo
$planty_logo_type   = isset( $planty_args['type'] ) ? $planty_args['type'] : '';
$planty_logo_image  = planty_get_logo_image( $planty_logo_type );
$planty_logo_text   = planty_is_on( planty_get_theme_option( 'logo_text' ) ) ? get_bloginfo( 'name' ) : '';
$planty_logo_slogan = get_bloginfo( 'description', 'display' );
if ( ! empty( $planty_logo_image['logo'] ) || ! empty( $planty_logo_text ) ) {
	?><a class="sc_layouts_logo" href="<?php echo esc_url( home_url( '/' ) ); ?>">
		<?php
		if ( ! empty( $planty_logo_image['logo'] ) ) {
			if ( empty( $planty_logo_type ) && function_exists( 'the_custom_logo' ) && is_numeric($planty_logo_image['logo']) && (int) $planty_logo_image['logo'] > 0 ) {
				the_custom_logo();
			} else {
				$planty_attr = planty_getimagesize( $planty_logo_image['logo'] );
				echo '<img src="' . esc_url( $planty_logo_image['logo'] ) . '"'
						. ( ! empty( $planty_logo_image['logo_retina'] ) ? ' srcset="' . esc_url( $planty_logo_image['logo_retina'] ) . ' 2x"' : '' )
						. ' alt="' . esc_attr( $planty_logo_text ) . '"'
						. ( ! empty( $planty_attr[3] ) ? ' ' . wp_kses_data( $planty_attr[3] ) : '' )
						. '>';
			}
		} else {
			planty_show_layout( planty_prepare_macros( $planty_logo_text ), '<span class="logo_text">', '</span>' );
			planty_show_layout( planty_prepare_macros( $planty_logo_slogan ), '<span class="logo_slogan">', '</span>' );
		}
		?>
	</a>
	<?php
}
