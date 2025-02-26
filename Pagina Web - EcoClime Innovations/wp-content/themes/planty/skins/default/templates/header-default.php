<?php
/**
 * The template to display default site header
 *
 * @package PLANTY
 * @since PLANTY 1.0
 */

$planty_header_css   = '';
$planty_header_image = get_header_image();
$planty_header_video = planty_get_header_video();
if ( ! empty( $planty_header_image ) && planty_trx_addons_featured_image_override( is_singular() || planty_storage_isset( 'blog_archive' ) || is_category() ) ) {
	$planty_header_image = planty_get_current_mode_image( $planty_header_image );
}

?><header class="top_panel top_panel_default
	<?php
	echo ! empty( $planty_header_image ) || ! empty( $planty_header_video ) ? ' with_bg_image' : ' without_bg_image';
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

	// Main menu
	get_template_part( apply_filters( 'planty_filter_get_template_part', 'templates/header-navi' ) );

	// Mobile header
	if ( planty_is_on( planty_get_theme_option( 'header_mobile_enabled' ) ) ) {
		get_template_part( apply_filters( 'planty_filter_get_template_part', 'templates/header-mobile' ) );
	}

	// Page title and breadcrumbs area
	if ( ! is_single() ) {
		get_template_part( apply_filters( 'planty_filter_get_template_part', 'templates/header-title' ) );
	}

	// Header widgets area
	get_template_part( apply_filters( 'planty_filter_get_template_part', 'templates/header-widgets' ) );
	?>
</header>
