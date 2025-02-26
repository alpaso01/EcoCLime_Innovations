<?php
/**
 * The template to display the side menu
 *
 * @package PLANTY
 * @since PLANTY 1.0
 */
?>
<div class="menu_side_wrap
<?php
echo ' menu_side_' . esc_attr( planty_get_theme_option( 'menu_side_icons' ) > 0 ? 'icons' : 'dots' );
$planty_menu_scheme = planty_get_theme_option( 'menu_scheme' );
$planty_header_scheme = planty_get_theme_option( 'header_scheme' );
if ( ! empty( $planty_menu_scheme ) && ! planty_is_inherit( $planty_menu_scheme  ) ) {
	echo ' scheme_' . esc_attr( $planty_menu_scheme );
} elseif ( ! empty( $planty_header_scheme ) && ! planty_is_inherit( $planty_header_scheme ) ) {
	echo ' scheme_' . esc_attr( $planty_header_scheme );
}
?>
				">
	<span class="menu_side_button icon-menu-2"></span>

	<div class="menu_side_inner">
		<?php
		// Logo
		set_query_var( 'planty_logo_args', array( 'type' => 'side' ) );
		get_template_part( apply_filters( 'planty_filter_get_template_part', 'templates/header-logo' ) );
		set_query_var( 'planty_logo_args', array() );
		// Main menu button
		?>
		<div class="toc_menu_item"
			<?php
			if ( planty_mouse_helper_enabled() ) {
				echo ' data-mouse-helper="click" data-mouse-helper-axis="y" data-mouse-helper-text="' . esc_attr__( 'Open main menu', 'planty' ) . '"';
			}
			?>
		>
			<a href="#" class="toc_menu_description menu_mobile_description"><span class="toc_menu_description_title"><?php esc_html_e( 'Main menu', 'planty' ); ?></span></a>
			<a class="menu_mobile_button toc_menu_icon icon-menu-2" href="#"></a>
		</div>		
	</div>

</div><!-- /.menu_side_wrap -->
