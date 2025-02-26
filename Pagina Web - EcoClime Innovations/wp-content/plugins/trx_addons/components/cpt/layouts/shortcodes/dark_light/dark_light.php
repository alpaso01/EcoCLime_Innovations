<?php
/**
 * Shortcode: Display a Dark/Light switcher
 *
 * @package ThemeREX Addons
 * @since v2.28.0
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}


// Load required styles and scripts for the frontend
if ( ! function_exists( 'trx_addons_cpt_layouts_dark_light_load_scripts_front' ) ) {
	add_action( "wp_enqueue_scripts", 'trx_addons_cpt_layouts_dark_light_load_scripts_front', TRX_ADDONS_ENQUEUE_SCRIPTS_PRIORITY );
	function trx_addons_cpt_layouts_dark_light_load_scripts_front() {
		if ( trx_addons_exists_page_builder() && trx_addons_is_on( trx_addons_get_option( 'debug_mode' ) ) ) {
			wp_enqueue_style( 'trx_addons-sc_layouts-dark_light', trx_addons_get_file_url( TRX_ADDONS_PLUGIN_CPT_LAYOUTS_SHORTCODES . 'dark_light/dark_light.css' ), array(), null );
		}
	}
}

	
// Merge shortcode specific styles into single stylesheet
if ( ! function_exists( 'trx_addons_sc_layouts_dark_light_merge_styles' ) ) {
	add_filter( "trx_addons_filter_merge_styles", 'trx_addons_sc_layouts_dark_light_merge_styles' );
	add_filter( "trx_addons_filter_merge_styles_layouts", 'trx_addons_sc_layouts_dark_light_merge_styles' );
	function trx_addons_sc_layouts_dark_light_merge_styles( $list ) {
		$list[ TRX_ADDONS_PLUGIN_CPT_LAYOUTS_SHORTCODES . 'dark_light/dark_light.css' ] = true;
		return $list;
	}
}

	
// Merge shortcode's specific scripts into single file
if ( ! function_exists( 'trx_addons_sc_layouts_dark_light_merge_scripts' ) ) {
	add_action( "trx_addons_filter_merge_scripts", 'trx_addons_sc_layouts_dark_light_merge_scripts' );
	function trx_addons_sc_layouts_dark_light_merge_scripts( $list ) {
		$list[ TRX_ADDONS_PLUGIN_CPT_LAYOUTS_SHORTCODES . 'dark_light/dark_light.js' ] = true;
		return $list;
	}
}



// trx_sc_layouts_dark_light
//-------------------------------------------------------------
/*
[trx_sc_layouts_dark_light id="unique_id" dark_light="image_url" dark_light_retina="image_url"]
*/
if ( !function_exists( 'trx_addons_sc_layouts_dark_light' ) ) {
	function trx_addons_sc_layouts_dark_light( $atts, $content = '' ){	
		$atts = trx_addons_sc_prepare_atts( 'trx_sc_layouts_dark_light', $atts, trx_addons_sc_common_atts( 'trx_sc_layouts_dark_light', 'id,hide', array(
			// Individual params
			"type" => "default",
			"effect" => "slide",
			"permanent" => false,
			"schemes_light" => "",
			"schemes_dark" => "",
			"position" => "static",
			"position_tablet" => "",
			"position_mobile" => "",
			"offset_x" => 0,
			"offset_x_tablet" => "",
			"offset_x_mobile" => "",
			"offset_y" => 0,
			"offset_y_tablet" => "",
			"offset_y_mobile" => "",
			"icon_light" => "",
			"icon_color_light" => "",
			"bg_color_light" => "",
			"bd_color_light" => "",
			"icon_dark" => "",
			"icon_color_dark" => "",
			"bg_color_dark" => "",
			"bd_color_dark" => "",
		) ) );

		if ( trx_addons_is_on( trx_addons_get_option('debug_mode' ) ) ) {
			wp_enqueue_script( 'trx_addons-sc_layouts_dark_light', trx_addons_get_file_url( TRX_ADDONS_PLUGIN_CPT_LAYOUTS_SHORTCODES . 'dark_light/dark_light.js'), array('jquery'), null, true );
		}

		if ( function_exists( 'vc_param_group_parse_atts' ) && ! is_array( $atts['schemes_light'] ) ) {
			$atts['schemes_light'] = (array) vc_param_group_parse_atts( $atts['schemes_light'] );
		}
		if ( function_exists( 'vc_param_group_parse_atts' ) && ! is_array( $atts['schemes_dark'] ) ) {
			$atts['schemes_dark'] = (array) vc_param_group_parse_atts( $atts['schemes_dark'] );
		}

		ob_start();
		trx_addons_get_template_part( array(
										TRX_ADDONS_PLUGIN_CPT_LAYOUTS_SHORTCODES . 'dark_light/tpl.' . trx_addons_esc( trx_addons_sanitize_file_name( $atts['type'] ) ) . '.php',
										TRX_ADDONS_PLUGIN_CPT_LAYOUTS_SHORTCODES . 'dark_light/tpl.default.php'
										),
										'trx_addons_args_sc_layouts_dark_light',
										$atts
									);
		$output = ob_get_contents();
		ob_end_clean();
		
		return apply_filters( 'trx_addons_sc_output', $output, 'trx_sc_layouts_dark_light', $atts, $content );
	}
}


// Add shortcode [trx_sc_layouts_dark_light]
if (!function_exists('trx_addons_sc_layouts_dark_light_add_shortcode')) {
	function trx_addons_sc_layouts_dark_light_add_shortcode() {
		
		if ( ! trx_addons_cpt_layouts_sc_required() ) return;

		add_shortcode( 'trx_sc_layouts_dark_light', 'trx_addons_sc_layouts_dark_light' );
	}
	add_action( 'init', 'trx_addons_sc_layouts_dark_light_add_shortcode', 15 );
}


// Add shortcodes
//----------------------------------------------------------------------------

// Add shortcodes to Elementor
if ( trx_addons_exists_elementor() && function_exists('trx_addons_elm_init') ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_CPT_LAYOUTS_SHORTCODES . 'dark_light/dark_light-sc-elementor.php';
}

// Add shortcodes to Gutenberg
if ( trx_addons_exists_gutenberg() && function_exists( 'trx_addons_gutenberg_get_param_id' ) ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_CPT_LAYOUTS_SHORTCODES . 'dark_light/dark_light-sc-gutenberg.php';
}

// Add shortcodes to VC
if ( trx_addons_exists_vc() && function_exists( 'trx_addons_vc_add_id_param' ) ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_CPT_LAYOUTS_SHORTCODES . 'dark_light/dark_light-sc-vc.php';
}
