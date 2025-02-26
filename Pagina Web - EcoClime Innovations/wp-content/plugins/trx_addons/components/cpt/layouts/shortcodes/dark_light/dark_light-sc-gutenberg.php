<?php
/**
 * Shortcode: Display a Dark/Light switcher (Elementor support)
 *
 * @package ThemeREX Addons
 * @since v2.27.1
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}


// Gutenberg Block
//------------------------------------------------------

// Add scripts and styles for the editor
if ( ! function_exists( 'trx_addons_gutenberg_sc_dark_light_editor_assets' ) ) {
	add_action( 'enqueue_block_editor_assets', 'trx_addons_gutenberg_sc_dark_light_editor_assets', TRX_ADDONS_GUTENBERG_EDITOR_BLOCK_REGISTRATION_PRIORITY );
	function trx_addons_gutenberg_sc_dark_light_editor_assets() {
		if ( trx_addons_exists_gutenberg() && trx_addons_get_setting( 'allow_gutenberg_blocks' ) ) {
			wp_enqueue_script(
				'trx-addons-gutenberg-editor-block-dark_light',
				trx_addons_get_file_url( TRX_ADDONS_PLUGIN_CPT_LAYOUTS_SHORTCODES . 'dark_light/gutenberg/dark_light.gutenberg-editor.js' ),
				trx_addons_block_editor_dependencis(),
				filemtime( trx_addons_get_file_dir( TRX_ADDONS_PLUGIN_CPT_LAYOUTS_SHORTCODES . 'dark_light/gutenberg/dark_light.gutenberg-editor.js' ) ),
				true
			);
		}
	}
}

// Block register
if ( ! function_exists( 'trx_addons_sc_dark_light_add_in_gutenberg' ) ) {
	add_action( 'init', 'trx_addons_sc_dark_light_add_in_gutenberg' );
	function trx_addons_sc_dark_light_add_in_gutenberg() {
		if ( trx_addons_exists_gutenberg() && trx_addons_get_setting( 'allow_gutenberg_blocks' ) ) {
			register_block_type(
				'trx-addons/layouts-dark-light',
				apply_filters( 'trx_addons_gb_map', array(
					'attributes'      => array_merge(
						array(
							'type'             => array(
								'type'    => 'string',
								'default' => 'default',
							),
							'permanent'        => array(
								'type'    => 'boolean',
								'default' => false,
							),
							'effect'         => array(
								'type'    => 'string',
								'default' => 'slide',
							),
							'position'         => array(
								'type'    => 'string',
								'default' => 'static',
							),
							'offset_x'         => array(
								'type'    => 'string',
								'default' => '',
							),
							'offset_y'         => array(
								'type'    => 'string',
								'default' => '',
							),
							'schemes_light1_area' => array(
								'type'    => 'string',
								'default' => 'content',
							),
							'schemes_light1_scheme' => array(
								'type'    => 'string',
								'default' => 'default',
							),
							'schemes_light1_selector' => array(
								'type'    => 'string',
								'default' => 'html,body',
							),
							'schemes_light2_area' => array(
								'type'    => 'string',
								'default' => 'header',
							),
							'schemes_light2_scheme' => array(
								'type'    => 'string',
								'default' => 'default',
							),
							'schemes_light2_selector' => array(
								'type'    => 'string',
								'default' => '.top_panel',
							),
							'schemes_light3_area' => array(
								'type'    => 'string',
								'default' => 'footer',
							),
							'schemes_light3_scheme' => array(
								'type'    => 'string',
								'default' => 'default',
							),
							'schemes_light3_selector' => array(
								'type'    => 'string',
								'default' => '.footer_wrap',
							),
							'schemes_light4_area' => array(
								'type'    => 'string',
								'default' => 'sidebar',
							),
							'schemes_light4_scheme' => array(
								'type'    => 'string',
								'default' => 'default',
							),
							'schemes_light4_selector' => array(
								'type'    => 'string',
								'default' => '.sidebar',
							),
							'schemes_dark1_area' => array(
								'type'    => 'string',
								'default' => 'content',
							),
							'schemes_dark1_scheme' => array(
								'type'    => 'string',
								'default' => 'dark',
							),
							'schemes_dark1_selector' => array(
								'type'    => 'string',
								'default' => 'html,body',
							),
							'schemes_dark2_area' => array(
								'type'    => 'string',
								'default' => 'header',
							),
							'schemes_dark2_scheme' => array(
								'type'    => 'string',
								'default' => 'dark',
							),
							'schemes_dark2_selector' => array(
								'type'    => 'string',
								'default' => '.top_panel',
							),
							'schemes_dark3_area' => array(
								'type'    => 'string',
								'default' => 'footer',
							),
							'schemes_dark3_scheme' => array(
								'type'    => 'string',
								'default' => 'dark',
							),
							'schemes_dark3_selector' => array(
								'type'    => 'string',
								'default' => '.footer_wrap',
							),
							'schemes_dark4_area' => array(
								'type'    => 'string',
								'default' => 'sidebar',
							),
							'schemes_dark4_scheme' => array(
								'type'    => 'string',
								'default' => 'dark',
							),
							'schemes_dark4_selector' => array(
								'type'    => 'string',
								'default' => '.sidebar',
							),
							'icon_light'             => array(
								'type'    => 'number',
								'default' => 0,
							),
							'icon_light_url'         => array(
								'type'    => 'string',
								'default' => '',
							),
							'icon_color_light'       => array(
								'type'    => 'string',
								'default' => '',
							),
							'bg_color_light'         => array(
								'type'    => 'string',
								'default' => '',
							),
							'bd_color_light'         => array(
								'type'    => 'string',
								'default' => '',
							),
							'icon_dark'              => array(
								'type'    => 'number',
								'default' => 0,
							),
							'icon_dark_url'          => array(
								'type'    => 'string',
								'default' => '',
							),
							'icon_color_dark'        => array(
								'type'    => 'string',
								'default' => '',
							),
							'bg_color_dark'          => array(
								'type'    => 'string',
								'default' => '',
							),
							'bd_color_dark'          => array(
								'type'    => 'string',
								'default' => '',
							),
						),
						trx_addons_gutenberg_get_param_hide(),
						trx_addons_gutenberg_get_param_id()
					),
					'render_callback' => 'trx_addons_gutenberg_sc_dark_light_render_block',
				), 'trx-addons/layouts-dark-light' )
			);
		}
	}
}

// Block render
if ( ! function_exists( 'trx_addons_gutenberg_sc_dark_light_render_block' ) ) {
	function trx_addons_gutenberg_sc_dark_light_render_block( $attributes = array() ) {
		// Prepare schemes
		$attributes['schemes_light'] = array();
		$attributes['schemes_dark'] = array();
		for ( $i = 1; $i <= 4; $i++ ) {
			$attributes['schemes_light'][] = array(
				'area' => $attributes['schemes_light' . $i . '_area'],
				'scheme' => $attributes['schemes_light' . $i . '_scheme'],
				'selector' => $attributes['schemes_light' . $i . '_selector'],
			);
			$attributes['schemes_dark'][] = array(
				'area' => $attributes['schemes_dark' . $i . '_area'],
				'scheme' => $attributes['schemes_dark' . $i . '_scheme'],
				'selector' => $attributes['schemes_dark' . $i . '_selector'],
			);
			unset(
				$attributes['schemes_light' . $i . '_area'],
				$attributes['schemes_light' . $i . '_scheme'],
				$attributes['schemes_light' . $i . '_selector'],
				$attributes['schemes_dark' . $i . '_area'],
				$attributes['schemes_dark' . $i . '_scheme'],
				$attributes['schemes_dark' . $i . '_selector']
			);
		}
		// Render the block
		$output = trx_addons_sc_layouts_dark_light( $attributes );
		if ( empty( $output ) && trx_addons_is_preview( 'gutenberg' ) ) {
			return TRX_ADDONS_GUTENBERG_EDITOR_MSG_BLOCK_IS_EMPTY;
		}
		return $output;
	}
}

// Return list of allowed layouts
if ( ! function_exists( 'trx_addons_gutenberg_sc_dark_light_get_layouts' ) ) {
	add_filter( 'trx_addons_filter_gutenberg_sc_layouts', 'trx_addons_gutenberg_sc_dark_light_get_layouts', 10, 1 );
	function trx_addons_gutenberg_sc_dark_light_get_layouts( $array = array() ) {
		$array['sc_dark_light'] = apply_filters( 'trx_addons_sc_type', trx_addons_get_list_sc_dark_light_layouts(), 'trx_sc_layouts_dark_light' );
		return $array;
	}
}

// Add shortcode's specific vars to the JS storage
if ( ! function_exists( 'trx_addons_gutenberg_sc_dark_light_params' ) ) {
	add_filter( 'trx_addons_filter_gutenberg_sc_params', 'trx_addons_gutenberg_sc_dark_light_params' );
	function trx_addons_gutenberg_sc_dark_light_params( $vars = array() ) {
		if ( trx_addons_exists_gutenberg() && trx_addons_get_setting( 'allow_gutenberg_blocks' ) ) {

			// If editor is active now
			$is_edit_mode = trx_addons_is_post_edit();

			$vars['sc_dark_light_effects'] = ! $is_edit_mode ? array() : trx_addons_get_list_sc_dark_light_effects();
			$vars['sc_dark_light_positions'] = ! $is_edit_mode ? array() : trx_addons_get_list_sc_fixed_positions();
			$vars['sc_dark_light_areas'] = ! $is_edit_mode ? array() : trx_addons_get_list_color_scheme_areas();
			$vars['sc_dark_light_schemes'] = ! $is_edit_mode ? array() : trx_addons_get_list_color_schemes();

			return $vars;
		}
	}
}
