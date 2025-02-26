<?php
/**
 * The template to display custom header from the ThemeREX Addons Layouts
 *
 * @package PLANTY
 * @since PLANTY 1.0.06
 */

$planty_header_css   = '';
$planty_header_image = get_header_image();
$planty_header_video = planty_get_header_video();
if ( ! empty( $planty_header_image ) && planty_trx_addons_featured_image_override( is_singular() || planty_storage_isset( 'blog_archive' ) || is_category() ) ) {
	$planty_header_image = planty_get_current_mode_image( $planty_header_image );
}

$planty_header_id = planty_get_custom_header_id();
$planty_header_meta = get_post_meta( $planty_header_id, 'trx_addons_options', true );
if ( ! empty( $planty_header_meta['margin'] ) ) {
	planty_add_inline_css( sprintf( '.page_content_wrap{padding-top:%s}', esc_attr( planty_prepare_css_value( $planty_header_meta['margin'] ) ) ) );
}

?><header class="top_panel top_panel_custom top_panel_custom_<?php echo esc_attr( $planty_header_id ); ?> top_panel_custom_<?php echo esc_attr( sanitize_title( get_the_title( $planty_header_id ) ) ); ?>
				<?php
				echo ! empty( $planty_header_image ) || ! empty( $planty_header_video )
					? ' with_bg_image'
					: ' without_bg_image';
				if ( '' != $planty_header_video ) {
					echo ' with_bg_video';
				}
				if ( '' != $planty_header_image ) {
					echo ' ' . esc_attr( planty_add_inline_css_class( 'background-image: url(' . esc_url( $planty_header_image ) . ');' ) );
				}
				if ( is_single() && has_post_thumbnail() ) {
					echo ' with_featured_image';
				}
				if ( planty_is_on( planty_get_theme_option( 'header_fullheight' ) ) ) {
					echo ' header_fullheight planty-full-height';
				}
				$planty_header_scheme = planty_get_theme_option( 'header_scheme' );
				if ( ! empty( $planty_header_scheme ) && ! planty_is_inherit( $planty_header_scheme  ) ) {
					echo ' scheme_' . esc_attr( $planty_header_scheme );
				}
				?>
">
	<?php

	// Background video
	if ( ! empty( $planty_header_video ) ) {
		get_template_part( apply_filters( 'planty_filter_get_template_part', 'templates/header-video' ) );
	}

	// Custom header's layout
	do_action( 'planty_action_show_layout', $planty_header_id );

	// Header widgets area
	get_template_part( apply_filters( 'planty_filter_get_template_part', 'templates/header-widgets' ) );

	?>
</header>
