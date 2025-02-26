<?php
/**
 * Shortcode: Chat (Gutenberg support)
 *
 * @package ThemeREX Addons
 * @since v2.22.0
 */

// Disable direct call
if ( ! defined( 'ABSPATH' ) ) { exit; }

use TrxAddons\AiHelper\OpenAi;
use TrxAddons\AiHelper\Lists;
use TrxAddons\AiHelper\Utils;

// Gutenberg Block
//------------------------------------------------------

// Add scripts and styles for the editor
if ( ! function_exists( 'trx_addons_gutenberg_sc_chat_editor_assets' ) ) {
	add_action( 'enqueue_block_editor_assets', 'trx_addons_gutenberg_sc_chat_editor_assets', TRX_ADDONS_GUTENBERG_EDITOR_BLOCK_REGISTRATION_PRIORITY );
	function trx_addons_gutenberg_sc_chat_editor_assets() {
		if ( trx_addons_exists_gutenberg() && trx_addons_get_setting( 'allow_gutenberg_blocks' ) ) {
			wp_enqueue_script(
				'trx-addons-gutenberg-editor-block-chat',
				trx_addons_get_file_url( TRX_ADDONS_PLUGIN_ADDONS . 'ai-helper/shortcodes/chat/gutenberg/chat.gutenberg-editor.js' ),
				trx_addons_block_editor_dependencis(),
				filemtime( trx_addons_get_file_dir( TRX_ADDONS_PLUGIN_ADDONS . 'ai-helper/shortcodes/chat/gutenberg/chat.gutenberg-editor.js' ) ),
				true
			);
		}
	}
}

// Block register
if ( ! function_exists( 'trx_addons_sc_chat_add_in_gutenberg' ) ) {
	add_action( 'init', 'trx_addons_sc_chat_add_in_gutenberg' );
	function trx_addons_sc_chat_add_in_gutenberg() {
		if ( trx_addons_exists_gutenberg() && trx_addons_get_setting( 'allow_gutenberg_blocks' ) ) {
			register_block_type(
				'trx-addons/chat',
				apply_filters('trx_addons_gb_map', array(
					'attributes'      => array_merge(
						array(
							'type'               => array(
								'type'    => 'string',
								'default' => 'default',
							),
							'model'              => array(
								'type'    => 'string',
								'default' => '',
							),						
							'override_config'    => array(
								'type'    => 'string',
								'default' => '',
							),						
							'title_text'         => array(
								'type'    => 'string',
								'default' => '',
							),						
							'new_chat_text'      => array(
								'type'    => 'string',
								'default' => '',
							),						
							'placeholder_text'   => array(
								'type'    => 'string',
								'default' => '',
							),						
							'prompt'             => array(
								'type'    => 'string',
								'default' => '',
							),						
							'button_text'        => array(
								'type'    => 'string',
								'default' => '',
							),						
							'tags_position'      => array(
								'type'    => 'string',
								'default' => 'none',
							),						
							'tags_label'         => array(
								'type'    => 'string',
								'default' => '',
							),						
							'tags'               => array(
								'type'    => 'string',
								'default' => '',
							),						
							'premium'            => array(
								'type'    => 'boolean',
								'default' => false,
							),
							'show_limits'        => array(
								'type'    => 'boolean',
								'default' => false,
							),
							'system_prompt'      => array(
								'type'    => 'string',
								'default' => '',
							),						
							'temperature'       => array(
								'type'    => 'number',
								'default' => (float)trx_addons_get_option( 'ai_helper_sc_tgenerator_temperature' ),
							),
							'max_tokens'        => array(
								'type'    => 'number',
								'default' => 0,
							),
							// Rerender
							'reload'             => array(
								'type'    => 'string',
								'default' => '',
							),
						),
						! apply_filters( 'trx_addons_filter_add_title_param', true, 'trx-addons/chat' ) ? array() : trx_addons_gutenberg_get_param_title(),
						! apply_filters( 'trx_addons_filter_add_title_param', true, 'trx-addons/chat' ) ? array() : trx_addons_gutenberg_get_param_button(),
						trx_addons_gutenberg_get_param_id()
					),
					'render_callback' => 'trx_addons_gutenberg_sc_chat_render_block',
				), 'trx-addons/chat' )
			);
		}
	}
}

// Block render
if ( ! function_exists( 'trx_addons_gutenberg_sc_chat_render_block' ) ) {
	function trx_addons_gutenberg_sc_chat_render_block( $attributes = array() ) {
		if ( ! empty( $attributes['tags'] ) && is_string( $attributes['tags'] ) ) {
			$attributes['tags'] = json_decode( $attributes['tags'], true );
		}
		return trx_addons_sc_chat( $attributes );
	}
}

// Return list of allowed layouts
if ( ! function_exists( 'trx_addons_gutenberg_sc_chat_get_layouts' ) ) {
	add_filter( 'trx_addons_filter_gutenberg_sc_layouts', 'trx_addons_gutenberg_sc_chat_get_layouts', 10, 1 );
	function trx_addons_gutenberg_sc_chat_get_layouts( $array = array() ) {
		$array['sc_chat'] = apply_filters( 'trx_addons_sc_type', array( 'default' => __( 'Default', 'trx_addons' ) ), 'trx_sc_chat' );
		return $array;
	}
}

// Add shortcode-specific lists to the js vars
if ( ! function_exists( 'trx_addons_gutenberg_sc_chat_params' ) ) {
	add_filter( 'trx_addons_filter_gutenberg_sc_params', 'trx_addons_gutenberg_sc_chat_params', 10, 1 );
	function trx_addons_gutenberg_sc_chat_params( $vars = array() ) {
		// If editor is active now
		$is_edit_mode = trx_addons_is_post_edit();

		$vars['sc_chat_max_tokens'] = ! $is_edit_mode ? Utils::get_default_max_tokens() : Utils::get_max_tokens( 'sc_chat' );
		$vars['sc_chat_temperature'] = (float)trx_addons_get_option( 'ai_helper_sc_chat_temperature' );
		$vars['sc_chat_models'] = ! $is_edit_mode ? array() : Lists::get_list_ai_chat_models();
		$vars['sc_chat_models_flowise_ai'] = ! $is_edit_mode ? array() : array_values( array_filter( array_keys( $vars['sc_chat_models'] ), function( $key ) { return Utils::is_flowise_ai_model( $key ); } ) );
		$vars['sc_chat_tags_positions'] = ! $is_edit_mode ? array() : Lists::get_list_ai_chat_tags_positions();
		return $vars;
	}
}
