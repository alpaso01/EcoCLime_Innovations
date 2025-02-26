<?php
/**
 * Shortcode: AI Chat
 *
 * @package ThemeREX Addons
 * @since v2.22.0
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}

use TrxAddons\AiHelper\Utils;
use TrxAddons\AiHelper\OpenAiAssistants;


// Load required styles and scripts for the frontend
if ( ! function_exists( 'trx_addons_sc_chat_load_scripts_front' ) ) {
	add_action( "wp_enqueue_scripts", 'trx_addons_sc_chat_load_scripts_front', TRX_ADDONS_ENQUEUE_SCRIPTS_PRIORITY );
	add_action( 'trx_addons_action_pagebuilder_preview_scripts', 'trx_addons_sc_chat_load_scripts_front', 10, 1 );
	function trx_addons_sc_chat_load_scripts_front( $force = false ) {
		trx_addons_enqueue_optimized( 'sc_chat', $force, array(
			'css'  => array(
				'trx_addons-sc_chat' => array( 'src' => TRX_ADDONS_PLUGIN_ADDONS . 'ai-helper/shortcodes/chat/chat.css' ),
			),
			'js' => array(
				'trx_addons-sc_chat' => array( 'src' => TRX_ADDONS_PLUGIN_ADDONS . 'ai-helper/shortcodes/chat/chat.js', 'deps' => 'jquery' ),
			),
			'check' => array(
				array( 'type' => 'sc',  'sc' => 'trx_sc_chat' ),
				array( 'type' => 'gb',  'sc' => 'wp:trx-addons/chat' ),
				array( 'type' => 'elm', 'sc' => '"widgetType":"trx_sc_chat"' ),
				array( 'type' => 'elm', 'sc' => '"shortcode":"[trx_sc_chat' ),
			)
		) );
	}
}

// Enqueue responsive styles for frontend
if ( ! function_exists( 'trx_addons_sc_chat_load_scripts_front_responsive' ) ) {
	add_action( 'wp_enqueue_scripts', 'trx_addons_sc_chat_load_scripts_front_responsive', TRX_ADDONS_ENQUEUE_RESPONSIVE_PRIORITY );
	add_action( 'trx_addons_action_load_scripts_front_sc_chat', 'trx_addons_sc_chat_load_scripts_front_responsive', 10, 1 );
	function trx_addons_sc_chat_load_scripts_front_responsive( $force = false  ) {
		trx_addons_enqueue_optimized_responsive( 'sc_chat', $force, array(
			'css'  => array(
				'trx_addons-sc_chat-responsive' => array(
					'src' => TRX_ADDONS_PLUGIN_ADDONS . 'ai-helper/shortcodes/chat/chat.responsive.css',
					'media' => 'lg'
				),
			),
		) );
	}
}

// Merge shortcode's specific styles to the single stylesheet
if ( ! function_exists( 'trx_addons_sc_chat_merge_styles' ) ) {
	add_filter( "trx_addons_filter_merge_styles", 'trx_addons_sc_chat_merge_styles' );
	function trx_addons_sc_chat_merge_styles( $list ) {
		$list[ TRX_ADDONS_PLUGIN_ADDONS . 'ai-helper/shortcodes/chat/chat.css' ] = false;
		return $list;
	}
}

// Merge shortcode's specific styles to the single stylesheet (responsive)
if ( ! function_exists( 'trx_addons_sc_chat_merge_styles_responsive' ) ) {
	add_filter("trx_addons_filter_merge_styles_responsive", 'trx_addons_sc_chat_merge_styles_responsive' );
	function trx_addons_sc_chat_merge_styles_responsive( $list ) {
		$list[ TRX_ADDONS_PLUGIN_ADDONS . 'ai-helper/shortcodes/chat/chat.responsive.css' ] = false;
		return $list;
	}
}

// Merge shortcode's specific scripts into single file
if ( ! function_exists( 'trx_addons_sc_chat_merge_scripts' ) ) {
	add_action("trx_addons_filter_merge_scripts", 'trx_addons_sc_chat_merge_scripts');
	function trx_addons_sc_chat_merge_scripts($list) {
		$list[ TRX_ADDONS_PLUGIN_ADDONS . 'ai-helper/shortcodes/chat/chat.js' ] = false;
		return $list;
	}
}

// Load styles and scripts if present in the cache of the menu
if ( ! function_exists( 'trx_addons_sc_chat_check_in_html_output' ) ) {
	add_filter( 'trx_addons_filter_get_menu_cache_html', 'trx_addons_sc_chat_check_in_html_output', 10, 1 );
	add_action( 'trx_addons_action_show_layout_from_cache', 'trx_addons_sc_chat_check_in_html_output', 10, 1 );
	add_action( 'trx_addons_action_check_page_content', 'trx_addons_sc_chat_check_in_html_output', 10, 1 );
	function trx_addons_sc_chat_check_in_html_output( $content = '' ) {
		$args = array(
			'check' => array(
				'class=[\'"][^\'"]*sc_chat'
			)
		);
		if ( trx_addons_check_in_html_output( 'sc_chat', $content, $args ) ) {
			trx_addons_sc_chat_load_scripts_front( true );
		}
		return $content;
	}
}

// Add messages to the list with JS vars
if ( ! function_exists( 'trx_addons_sc_chat_localize_script' ) ) {
	add_action( 'trx_addons_filter_localize_script', 'trx_addons_sc_chat_localize_script' );
	function trx_addons_sc_chat_localize_script( $vars ) {
		$vars['msg_ai_helper_sc_chat_clear'] = __( 'Clear', 'trx_addons' );
		return $vars;
	}
}

// Get extensions list for the chat attachments
if ( ! function_exists( 'trx_addons_sc_chat_get_allowed_attachments' ) ) {
	function trx_addons_sc_chat_get_allowed_attachments( $type = '', $model = '' ) {
		return Utils::get_allowed_attachments( $type, $model );
	}
}


// trx_sc_chat
//-------------------------------------------------------------
/*
[trx_sc_chat id="unique_id" prompt="prompt text for ai" command="blog-post"]
*/
if ( ! function_exists( 'trx_addons_sc_chat' ) ) {
	function trx_addons_sc_chat( $atts, $content = '' ) {
		// Convert atts 'tagNtitle' and 'tagNprompt' to array 'tags'
		if ( ! empty( $atts['tag1title'] ) && ! empty( $atts['tag1prompt'] ) && empty( $atts['tags'] ) ) {
			$atts['tags'] = array();
			$num = 1;
			while ( ! empty( $atts["tag{$num}title"] ) && ! empty( $atts["tag{$num}prompt"] ) ) {
				$atts['tags'][] = array(
					'title' => $atts["tag{$num}title"],
					'prompt' => $atts["tag{$num}prompt"]
				);
				unset( $atts["tag{$num}title"], $atts["tag{$num}prompt"] );
				$num++;
			}
		}
		$atts = trx_addons_sc_prepare_atts( 'trx_sc_chat', $atts, trx_addons_sc_common_atts( 'trx_sc_chat', 'id,title', array(
			// Individual params
			"type" => "default",
			"position" => "",
			"offset_x" => "",
			"offset_y" => "",
			"premium" => 0,
			"nolimits" => 0,
			"model" => "",
			"flowise_override" => "",
			"show_limits" => 0,
			"system_prompt" => "",
			"temperature" => 0,
			"max_tokens" => 0,
			"save_history" => 0,
			"open_on_load" => 0,
			"allow_attachments" => 0,
			"tags" => "",
			"tags_label" => "",
			"tags_position" => "none",
			// Chat window
			"chat_bg_color" => "",
			"chat_bd_color" => "",
			"chat_bd_width" => "",
			"chat_bd_radius" => "",
			"chat_shadow" => "",
			// Title
			"title_text" => "",
			"title_text_color" => "",
			"title_bg_color" => "",
			"title_icon" => "",
			"title_icon_color" => "",
			"title_image" => "",
			// Link "New chat"
			"new_chat_text" => "",
			"new_chat_text_color" => "",
			"new_chat_text_hover" => "",
			"new_chat_icon" => "",
			"new_chat_icon_color" => "",
			"new_chat_icon_hover" => "",
			"new_chat_image" => "",
			// Assistant message
			"assistant_text_color" => "",
			"assistant_bg_color" => "",
			"assistant_bd_color" => "",
			"assistant_bd_width" => "",
			"assistant_bd_radius" => "",
			"assistant_time_color" => "",
			"assistant_icon" => "",
			"assistant_icon_size" => "",
			"assistant_icon_color" => "",
			"assistant_icon_bg_color" => "",
			"assistant_icon_bd_color" => "",
			"assistant_icon_bd_width" => "",
			"assistant_icon_bd_radius" => "",
			"assistant_image" => "",
			"assistant_shadow" => "",
			// User message
			"user_text_color" => "",
			"user_bg_color" => "",
			"user_bd_color" => "",
			"user_bd_width" => "",
			"user_bd_radius" => "",
			"user_time_color" => "",
			"user_icon" => "",
			"user_icon_size" => "",
			"user_icon_color" => "",
			"user_icon_bg_color" => "",
			"user_icon_bd_color" => "",
			"user_icon_bd_width" => "",
			"user_icon_bd_radius" => "",
			"user_image" => "",
			"user_shadow" => "",
			// Prompt field
			"prompt" => "",
			"prompt_text_color" => "",
			"prompt_bg_color" => "",
			"prompt_bd_color" => "",
			"prompt_bd_width" => "",
			"prompt_bd_radius" => "",
			"prompt_shadow" => "",
			"placeholder_text" => "",
			"placeholder_text_color" => "",
			// Tags
			"tags_text_color" => "",
			"tags_text_hover" => "",
			"tags_bg_color" => "",
			"tags_bg_hover" => "",
			"tags_bd_color" => "",
			"tags_bd_hover" => "",
			"tags_bd_width" => "",
			"tags_bd_radius" => "",
			"tags_shadow" => "",
			// Button "Send"
			"button_text" => "",
			"button_text_color" => "",
			"button_text_hover" => "",
			"button_bg_color" => "",
			"button_bg_hover" => "",
			"button_bd_color" => "",
			"button_bd_hover" => "",
			"button_bd_width" => "",
			"button_bd_radius" => "",
			"button_icon" => "",
			"button_icon_color" => "",
			"button_icon_hover" => "",
			"button_image" => "",
			"button_text_disabled" => "",
			"button_bg_disabled" => "",
			"button_bd_disabled" => "",
			"button_icon_disabled" => "",
			"button_shadow" => "",
			// Button "Add attachments"
			"attachments_add_icon" => "",
			"attachments_add_icon_color" => "",
			"attachments_add_bg_color" => "",
			"attachments_add_bd_color" => "",
			"attachments_add_bd_width" => "",
			"attachments_add_bd_radius" => "",
			"attachments_add_shadow" => "",
			"attachments_add_icon_hover" => "",
			"attachments_add_bg_hover" => "",
			"attachments_add_bd_hover" => "",
			// Button "Clear attachments"
			"attachments_clear_icon" => "",
			"attachments_clear_icon_color" => "",
			"attachments_clear_text_color" => "",
			"attachments_clear_bg_color" => "",
			"attachments_clear_bd_color" => "",
			"attachments_clear_bd_width" => "",
			"attachments_clear_bd_radius" => "",
			"attachments_clear_padding" => "",
			"attachments_clear_margin" => "",
			"attachments_clear_shadow" => "",
			"attachments_clear_icon_hover" => "",
			"attachments_clear_text_hover" => "",
			"attachments_clear_bg_hover" => "",
			"attachments_clear_bd_hover" => "",
			// Attachments list
			"attachment_items_show_names" => 0,
			"attachment_items_text_color" => "",
			"attachment_items_bg_color" => "",
			"attachment_items_bd_color" => "",
			"attachment_items_bd_width" => "",
			"attachment_items_bd_radius" => "",
			"attachment_items_padding" => "",
			"attachment_items_shadow" => "",
			"attachment_items_text_hover" => "",
			"attachment_items_bg_hover" => "",
			"attachment_items_bd_hover" => "",
			// Limits
			"limits_text_color" => "",
			// Popup Button
			"popup_button_size" => "",
			"popup_button_bg_color" => "",
			"popup_button_bg_hover" => "",
			"popup_button_bd_color" => "",
			"popup_button_bd_hover" => "",
			"popup_button_bd_width" => "",
			"popup_button_bd_radius" => "",
			"popup_button_icon" => "",
			"popup_button_icon_color" => "",
			"popup_button_icon_hover" => "",
			"popup_button_image" => "",
			"popup_button_icon_opened" => "",
			"popup_button_image_opened" => "",
			"popup_button_shadow" => "",
		) ) );

		if ( empty( $atts['id'] ) ) {
			// Disallow save history for the shortcode without ID
			$atts['save_history'] = 0;
			// Generate unique ID if it wasn't set in the shortcode's attributes, because it's required for the shortcode's styles
			$atts['id'] = 'sc_chat_' . mt_rand();
		}

		trx_addons_sc_chat_add_inline_css( $atts );

		// Load shortcode-specific scripts and styles
		trx_addons_sc_chat_load_scripts_front( true );

		// Load template
		$output = '';

		ob_start();
		if ( ! Utils::is_chat_api_available( $atts['model'] ) ) {
			trx_addons_get_template_part( 'templates/tpl.sc_placeholder.php',
				'trx_addons_args_sc_placeholder',
				apply_filters( 'trx_addons_filter_sc_placeholder_args', array(
					'sc' => 'trx_sc_chat',
					'title' => __('AI Chat is not available - token for access to the API for text generation is not specified', 'trx_addons'),
					'class' => 'sc_placeholder_with_title'
					) )
			);
		} else {
			trx_addons_get_template_part( array(
										TRX_ADDONS_PLUGIN_ADDONS . 'ai-helper/shortcodes/chat/tpl.' . trx_addons_esc( trx_addons_sanitize_file_name( $atts['type'] ) ) . '.php',
										TRX_ADDONS_PLUGIN_ADDONS . 'ai-helper/shortcodes/chat/tpl.default.php'
										),
										'trx_addons_args_sc_chat',
										$atts
									);
		}
		$output = ob_get_contents();
		ob_end_clean();
		return apply_filters( 'trx_addons_sc_output', $output, 'trx_sc_chat', $atts, $content );
	}
}

// Add shortcode [trx_sc_chat]
if ( ! function_exists( 'trx_addons_sc_chat_add_shortcode' ) ) {
	add_action( 'init', 'trx_addons_sc_chat_add_shortcode', 20 );
	function trx_addons_sc_chat_add_shortcode() {
		add_shortcode( "trx_sc_chat", "trx_addons_sc_chat" );
	}
}

if ( ! function_exists( 'trx_addons_sc_chat_add_inline_css' ) ) {
	/**
	 * Add inline styles to the page if a shortcode was called not from Elementor
	 * ( Elementor adds styles automatically )
	 * 
	 * @param array $atts - shortcode's attributes
	 */
	function trx_addons_sc_chat_add_inline_css( $atts ) {
		// Check if an attribute 'xxx_extra' is present in the shortcode's attributes - it means that the shortcode was called from Elementor and the styles are already added
		if ( isset( $atts['max_tokens_extra'] ) ) {
			return;
		}
		// Params and corresponding CSS rules
		$params = array(
			// Chat window
			'offset_x'					=> array( '{{WRAPPER}}.sc_chat_popup' => '--trx-addons-ai-helper-popup-offset-x: {{SIZE}}{{UNIT}};' ),
			'offset_y'					=> array( '{{WRAPPER}}.sc_chat_popup' => '--trx-addons-ai-helper-popup-offset-y: {{SIZE}}{{UNIT}};' ),
			'chat_bg_color'				=> array( '{{WRAPPER}} .sc_chat_content' => 'background-color: {{VALUE}};' ),
			'chat_bd_color'				=> array( '{{WRAPPER}} .sc_chat_content' => 'border-color: {{VALUE}};' ),
			'chat_bd_width'				=> array( '{{WRAPPER}} .sc_chat_content' => 'border-width: {{SIZE}}{{UNIT}}; border-style: solid;' ),
			'chat_bd_radius'			=> array( '{{WRAPPER}} .sc_chat_content' => '--trx-addons-ai-helper-chat-content-border-radius: {{SIZE}}{{UNIT}};' ),
			'chat_shadow'				=> array( '{{WRAPPER}} .sc_chat_content' => 'box-shadow: {{VALUE}};' ),
			'limits_text_color'			=> array( '{{WRAPPER}} .sc_chat_limits' => 'color: {{VALUE}};' ),
			// Chat Title
			'title_text_color'			=> array( '{{WRAPPER}} .sc_chat_form_field_prompt_label' => 'color: {{VALUE}};' ),
			'title_bg_color'			=> array( '{{WRAPPER}} .sc_chat_form_field_prompt_label' => 'background-color: {{VALUE}};' ),
			'title_icon_color'			=> array( '{{WRAPPER}} .sc_chat_form_title_icon' => 'color: {{VALUE}};',
												  '{{WRAPPER}} .sc_chat_form_title svg' => 'fill: {{VALUE}};'
												),
			// Link "New Chat"
			'new_chat_text_color'		=> array( '{{WRAPPER}} .sc_chat_form_start_new' => 'color: {{VALUE}};' ),
			'new_chat_text_hover'		=> array( '{{WRAPPER}} .sc_chat_form_start_new:hover' => 'color: {{VALUE}};' ),
			'new_chat_icon_color'		=> array( '{{WRAPPER}} .sc_chat_form_start_new_icon' => 'color: {{VALUE}};',
												  '{{WRAPPER}} .sc_chat_form_start_new svg' => 'fill: {{VALUE}};'
												),
			'new_chat_icon_hover'		=> array( '{{WRAPPER}} .sc_chat_form_start_new:hover .sc_chat_form_start_new_icon' => 'color: {{VALUE}};',
												  '{{WRAPPER}} .sc_chat_form_start_new:hover svg' => 'fill: {{VALUE}};'
												),
			// Assistant message & avatar
			'assistant_text_color'		=> array( '{{WRAPPER}} .sc_chat_list_item_assistant .sc_chat_list_item_content' => 'color: {{VALUE}};' ),
			'assistant_bg_color'		=> array( '{{WRAPPER}} .sc_chat_list_item_assistant .sc_chat_list_item_content' => 'background-color: {{VALUE}};' ),
			'assistant_bd_color'		=> array( '{{WRAPPER}} .sc_chat_list_item_assistant .sc_chat_list_item_content' => 'border-color: {{VALUE}};' ),
			'assistant_bd_width'		=> array( '{{WRAPPER}} .sc_chat_list_item_assistant .sc_chat_list_item_content' => 'border-width: {{SIZE}}{{UNIT}}; border-style: solid;' ),
			'assistant_bd_radius'		=> array( '{{WRAPPER}} .sc_chat_list_item_assistant .sc_chat_list_item_content' => '--trx-addons-ai-helper-chat-items-border-radius: {{SIZE}}{{UNIT}};' ),
			'assistant_shadow'			=> array( '{{WRAPPER}} .sc_chat_list_item_assistant .sc_chat_list_item_content' => 'box-shadow: {{VALUE}};',
												  '{{WRAPPER}} .sc_chat_list_item_assistant .sc_chat_list_item_avatar' => 'box-shadow: {{VALUE}};'
												),
			'assistant_time_color'		=> array( '{{WRAPPER}} .sc_chat_list_item_assistant .sc_chat_list_item_time' => 'color: {{VALUE}};' ),
			'assistant_icon_size'		=> array( '{{WRAPPER}} .sc_chat_list_item_assistant .sc_chat_list_item_avatar' => '--trx-addons-ai-helper-chat-items-icon-size: {{SIZE}}{{UNIT}};' ),
			'assistant_icon_color'		=> array( '{{WRAPPER}} .sc_chat_list_item_assistant .sc_chat_list_item_avatar' => 'color: {{VALUE}};',
												  '{{WRAPPER}} .sc_chat_list_item_assistant .sc_chat_list_item_svg svg' => 'fill: {{VALUE}};'
												),
			'assistant_icon_bg_color'	=> array( '{{WRAPPER}} .sc_chat_list_item_assistant .sc_chat_list_item_avatar' => 'background-color: {{VALUE}};' ),
			'assistant_icon_bd_color'	=> array( '{{WRAPPER}} .sc_chat_list_item_assistant .sc_chat_list_item_avatar' => 'border-color: {{VALUE}};' ),
			'assistant_icon_bd_width'	=> array( '{{WRAPPER}} .sc_chat_list_item_assistant .sc_chat_list_item_avatar' => 'border-width: {{SIZE}}{{UNIT}}; border-style: solid;' ),
			'assistant_icon_bd_radius'	=> array( '{{WRAPPER}} .sc_chat_list_item_assistant .sc_chat_list_item_avatar' => '--trx-addons-ai-helper-chat-items-icon-border-radius: {{SIZE}}{{UNIT}};' ),
			// User message & avatar
			'user_text_color'			=> array( '{{WRAPPER}} .sc_chat_list_item_user .sc_chat_list_item_content' => 'color: {{VALUE}};' ),
			'user_bg_color'				=> array( '{{WRAPPER}} .sc_chat_list_item_user .sc_chat_list_item_content' => 'background-color: {{VALUE}};' ),
			'user_bd_color'				=> array( '{{WRAPPER}} .sc_chat_list_item_user .sc_chat_list_item_content' => 'border-color: {{VALUE}};' ),
			'user_bd_width'				=> array( '{{WRAPPER}} .sc_chat_list_item_user .sc_chat_list_item_content' => 'border-width: {{SIZE}}{{UNIT}}; border-style: solid;' ),
			'user_bd_radius'			=> array( '{{WRAPPER}} .sc_chat_list_item_user .sc_chat_list_item_content' => '--trx-addons-ai-helper-chat-items-border-radius: {{SIZE}}{{UNIT}};' ),
			'user_shadow'				=> array( '{{WRAPPER}} .sc_chat_list_item_user .sc_chat_list_item_content' => 'box-shadow: {{VALUE}};',
												  '{{WRAPPER}} .sc_chat_list_item_user .sc_chat_list_item_avatar' => 'box-shadow: {{VALUE}};'
												),
			'user_time_color'			=> array( '{{WRAPPER}} .sc_chat_list_item_user .sc_chat_list_item_time' => 'color: {{VALUE}};' ),
			'user_icon_size'			=> array( '{{WRAPPER}} .sc_chat_list_item_user .sc_chat_list_item_avatar' => '--trx-addons-ai-helper-chat-items-icon-size: {{SIZE}}{{UNIT}};' ),
			'user_icon_color'			=> array( '{{WRAPPER}} .sc_chat_list_item_user .sc_chat_list_item_avatar' => 'color: {{VALUE}};',
												  '{{WRAPPER}} .sc_chat_list_item_user .sc_chat_list_item_svg svg' => 'fill: {{VALUE}};'
												),
			'user_icon_bg_color'		=> array( '{{WRAPPER}} .sc_chat_list_item_user .sc_chat_list_item_avatar' => 'background-color: {{VALUE}};' ),
			'user_icon_bd_color'		=> array( '{{WRAPPER}} .sc_chat_list_item_user .sc_chat_list_item_avatar' => 'border-color: {{VALUE}};' ),
			'user_icon_bd_width'		=> array( '{{WRAPPER}} .sc_chat_list_item_user .sc_chat_list_item_avatar' => 'border-width: {{SIZE}}{{UNIT}}; border-style: solid;' ),
			'user_icon_bd_radius'		=> array( '{{WRAPPER}} .sc_chat_list_item_user .sc_chat_list_item_avatar' => '--trx-addons-ai-helper-chat-items-icon-border-radius: {{SIZE}}{{UNIT}};' ),
			// Prompt field
			'prompt_text_color'			=> array( '{{WRAPPER}} .sc_chat_form_field_prompt_text' => 'color: {{VALUE}};' ),
			'placeholder_text_color'	=> array( '{{WRAPPER}} .sc_chat_form_field_prompt_text::placeholder' => 'color: {{VALUE}};',
												  '{{WRAPPER}} .sc_chat_form_field_prompt_text::-moz-placeholder' => 'color: {{VALUE}};',
												  '{{WRAPPER}} .sc_chat_form_field_prompt_text::-webkit-input-placeholder' => 'color: {{VALUE}};'
												),
			'prompt_bg_color'			=> array( '{{WRAPPER}} .sc_chat_form_field_prompt_text' => 'background-color: {{VALUE}};' ),
			'prompt_bd_color'			=> array( '{{WRAPPER}} .sc_chat_form_field_prompt_text' => 'border-color: {{VALUE}};' ),
			'prompt_bd_width'			=> array( '{{WRAPPER}} .sc_chat_form_field_prompt_text' => 'border-width: {{SIZE}}{{UNIT}}; border-style: solid;' ),
			'prompt_bd_radius'			=> array( '{{WRAPPER}} .sc_chat_form_field_prompt_text' => '--trx-addons-ai-helper-chat-fields-border-radius: {{SIZE}}{{UNIT}};' ),
			'prompt_shadow'				=> array( '{{WRAPPER}} .sc_chat_form_field_prompt_text' => 'box-shadow: {{VALUE}};' ),
			// Button "Send"
			'button_text_color'			=> array( '{{WRAPPER}} .sc_chat_form_field_prompt_button' => 'color: {{VALUE}};' ),
			'button_icon_color'			=> array( '{{WRAPPER}} .sc_chat_form_field_prompt_button .sc_chat_form_field_prompt_button_icon' => 'color: {{VALUE}};',
												  '{{WRAPPER}} .sc_chat_form_field_prompt_button .sc_chat_form_field_prompt_button_svg svg' => 'fill: {{VALUE}};'
												),
			'button_bg_color'			=> array( '{{WRAPPER}} .sc_chat_form_field_prompt_button' => 'background-color: {{VALUE}};' ),
			'button_bd_color'			=> array( '{{WRAPPER}} .sc_chat_form_field_prompt_button' => 'border-color: {{VALUE}};' ),
			'button_bd_width'			=> array( '{{WRAPPER}} .sc_chat_form_field_prompt_button' => 'border-width: {{SIZE}}{{UNIT}}; border-style: solid;' ),
			'button_bd_radius'			=> array( '{{WRAPPER}} .sc_chat_form_field_prompt_button' => '--trx-addons-ai-helper-chat-button-border-radius: {{SIZE}}{{UNIT}};' ),
			'button_shadow'				=> array( '{{WRAPPER}} .sc_chat_form_field_prompt_button' => 'box-shadow: {{VALUE}};' ),
			'button_text_hover'			=> array( '{{WRAPPER}} .sc_chat_form_field_prompt_button:not(.sc_chat_form_field_prompt_button_disabled):hover' => 'color: {{VALUE}};' ),
			'button_icon_hover'			=> array( '{{WRAPPER}} .sc_chat_form_field_prompt_button:not(.sc_chat_form_field_prompt_button_disabled):hover .sc_chat_form_field_prompt_button_icon' => 'color: {{VALUE}};',
												  '{{WRAPPER}} .sc_chat_form_field_prompt_button:not(.sc_chat_form_field_prompt_button_disabled):hover .sc_chat_form_field_prompt_button_svg svg' => 'fill: {{VALUE}};'
												),
			'button_bg_hover'			=> array( '{{WRAPPER}} .sc_chat_form_field_prompt_button:not(.sc_chat_form_field_prompt_button_disabled):hover' => 'background-color: {{VALUE}};' ),
			'button_bd_hover'			=> array( '{{WRAPPER}} .sc_chat_form_field_prompt_button:not(.sc_chat_form_field_prompt_button_disabled):hover' => 'border-color: {{VALUE}};' ),
			'button_text_disabled'		=> array( '{{WRAPPER}} .sc_chat_form_field_prompt_button_disabled' => 'color: {{VALUE}};' ),
			'button_icon_disabled'		=> array( '{{WRAPPER}} .sc_chat_form_field_prompt_button_disabled .sc_chat_form_field_prompt_button_icon' => 'color: {{VALUE}};',
												  '{{WRAPPER}} .sc_chat_form_field_prompt_button_disabled .sc_chat_form_field_prompt_button_svg svg' => 'fill: {{VALUE}};'
												),
			'button_bg_disabled'		=> array( '{{WRAPPER}} .sc_chat_form_field_prompt_button_disabled' => 'background-color: {{VALUE}};' ),
			'button_bd_disabled'		=> array( '{{WRAPPER}} .sc_chat_form_field_prompt_button_disabled' => 'border-color: {{VALUE}};' ),
			// Button "Add attachments"
			"attachments_add_icon_color"	=> [ '{{WRAPPER}} .sc_chat_form_field_upload_attachments_label' => 'color: {{VALUE}};',
												  '{{WRAPPER}} .sc_chat_form_field_upload_attachments_label svg' => 'fill: {{VALUE}};',
												],
			"attachments_add_bg_color"		=> [ '{{WRAPPER}} .sc_chat_form_field_upload_attachments_label' => 'background-color: {{VALUE}};' ],
			"attachments_add_bd_color"		=> [ '{{WRAPPER}} .sc_chat_form_field_upload_attachments_label' => 'border-color: {{VALUE}};' ],
			"attachments_add_bd_width"		=> [ '{{WRAPPER}} .sc_chat_form_field_upload_attachments_label' => 'border-width: {{SIZE}}{{UNIT}}; border-style: solid;' ],
			"attachments_add_bd_radius"		=> [ '{{WRAPPER}} .sc_chat_form_field_upload_attachments_label' => '--trx-addons-ai-helper-chat-attachment-button-border-radius: {{SIZE}}{{UNIT}};' ],
			"attachments_add_shadow"		=> [ '{{WRAPPER}} .sc_chat_form_field_upload_attachments_label' => 'box-shadow: {{VALUE}};' ],
			"attachments_add_icon_hover"	=> [ '{{WRAPPER}} .sc_chat_form_field_upload_attachments_label:hover' => 'color: {{VALUE}};',
												 '{{WRAPPER}} .sc_chat_form_field_upload_attachments_label:hover svg' => 'fill: {{VALUE}};',
												],
			"attachments_add_bg_hover"		=> [ '{{WRAPPER}} .sc_chat_form_field_upload_attachments_label:hover' => 'background-color: {{VALUE}};' ],
			"attachments_add_bd_hover"		=> [ '{{WRAPPER}} .sc_chat_form_field_upload_attachments_label:hover' => 'border-color: {{VALUE}};' ],
			// Button "Clear attachments"
			"attachments_clear_icon_color"	=> [ '{{WRAPPER}} .sc_chat_form_field_upload_attachments_list_clear .sc_chat_form_field_upload_attachments_list_clear_icon' => 'color: {{VALUE}};',
												 '{{WRAPPER}} .sc_chat_form_field_upload_attachments_list_clear .sc_chat_form_field_upload_attachments_list_clear_svg svg' => 'fill: {{VALUE}};',
												],
			"attachments_clear_text_color"	=> [ '{{WRAPPER}} .sc_chat_form_field_upload_attachments_list_clear' => 'color: {{VALUE}};' ],
			"attachments_clear_bg_color"	=> [ '{{WRAPPER}} .sc_chat_form_field_upload_attachments_list_clear' => 'background-color: {{VALUE}};' ],
			"attachments_clear_bd_color"	=> [ '{{WRAPPER}} .sc_chat_form_field_upload_attachments_list_clear' => 'border-color: {{VALUE}};' ],
			"attachments_clear_bd_width"	=> [ '{{WRAPPER}} .sc_chat_form_field_upload_attachments_list_clear' => 'border-width: {{SIZE}}{{UNIT}}; border-style: solid;' ],
			"attachments_clear_bd_radius"	=> [ '{{WRAPPER}} .sc_chat_form_field_upload_attachments_list_clear' => '--trx-addons-ai-helper-chat-attachments-clear-border-radius: {{SIZE}}{{UNIT}};' ],
			"attachments_clear_padding"		=> [ '{{WRAPPER}} .sc_chat_form_field_upload_attachments_list_clear' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
			"attachments_clear_margin"		=> [ '{{WRAPPER}} .sc_chat_form_field_upload_attachments_list_clear' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
			"attachments_clear_shadow"		=> [ '{{WRAPPER}} .sc_chat_form_field_upload_attachments_list_clear' => 'box-shadow: {{VALUE}};' ],
			"attachments_clear_icon_hover"	=> [ '{{WRAPPER}} .sc_chat_form_field_upload_attachments_list_clear:hover .sc_chat_form_field_upload_attachments_list_clear_icon' => 'color: {{VALUE}};',
												 '{{WRAPPER}} .sc_chat_form_field_upload_attachments_list_clear:hover .sc_chat_form_field_upload_attachments_list_clear_svg svg' => 'fill: {{VALUE}};',
												],
			"attachments_clear_text_hover"	=> [ '{{WRAPPER}} .sc_chat_form_field_upload_attachments_list_clear:hover' => 'color: {{VALUE}};' ],
			"attachments_clear_bg_hover"	=> [ '{{WRAPPER}} .sc_chat_form_field_upload_attachments_list_clear:hover' => 'background-color: {{VALUE}};' ],
			"attachments_clear_bd_hover"	=> [ '{{WRAPPER}} .sc_chat_form_field_upload_attachments_list_clear:hover' => 'border-color: {{VALUE}};' ],
			// Attachments list
			"attachment_items_text_color"	=> [ '{{WRAPPER}} .sc_chat_form_field_upload_attachments_list_item' => 'color: {{VALUE}};' ],
			"attachment_items_bg_color"		=> [ '{{WRAPPER}} .sc_chat_form_field_upload_attachments_list_item' => 'background-color: {{VALUE}};' ],
			"attachment_items_bd_color"		=> [ '{{WRAPPER}} .sc_chat_form_field_upload_attachments_list_item' => 'border-color: {{VALUE}};' ],
			"attachment_items_bd_width"		=> [ '{{WRAPPER}} .sc_chat_form_field_upload_attachments_list_item' => 'border-width: {{SIZE}}{{UNIT}}; border-style: solid;' ],
			"attachment_items_bd_radius"	=> [ '{{WRAPPER}} .sc_chat_form_field_upload_attachments_list_item' => '--trx-addons-ai-helper-chat-attachment-items-border-radius: {{SIZE}}{{UNIT}};' ],
			"attachment_items_padding"		=> [ '{{WRAPPER}} .sc_chat_form_field_upload_attachments_list_item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
			"attachment_items_shadow"		=> [ '{{WRAPPER}} .sc_chat_form_field_upload_attachments_list_item' => 'box-shadow: {{VALUE}};' ],
			"attachment_items_text_hover"	=> [ '{{WRAPPER}} .sc_chat_form_field_upload_attachments_list_item:hover' => 'color: {{VALUE}};' ],
			"attachment_items_bg_hover"		=> [ '{{WRAPPER}} .sc_chat_form_field_upload_attachments_list_item:hover' => 'background-color: {{VALUE}};' ],
			"attachment_items_bd_hover"		=> [ '{{WRAPPER}} .sc_chat_form_field_upload_attachments_list_item:hover' => 'border-color: {{VALUE}};' ],
			// Popup Button
			'popup_button_size'			=> array( '{{WRAPPER}} .sc_chat_popup_button' => '--trx-addons-ai-helper-popup-button-size: {{SIZE}}{{UNIT}};' ),
			'popup_button_bg_color'		=> array( '{{WRAPPER}} .sc_chat_popup_button' => 'background-color: {{VALUE}};' ),
			'popup_button_bd_color'		=> array( '{{WRAPPER}} .sc_chat_popup_button' => 'border-color: {{VALUE}};' ),
			'popup_button_bd_width'		=> array( '{{WRAPPER}} .sc_chat_popup_button' => 'border-width: {{SIZE}}{{UNIT}}; border-style: solid;' ),
			'popup_button_bd_radius'	=> array( '{{WRAPPER}} .sc_chat_popup_button' => '--trx-addons-ai-helper-popup-button-border-radius: {{SIZE}}{{UNIT}};' ),
			'popup_button_shadow'		=> array( '{{WRAPPER}} .sc_chat_popup_button' => 'box-shadow: {{VALUE}};' ),
			'popup_button_icon_color'	=> array( '{{WRAPPER}} .sc_chat_popup_button .sc_chat_popup_button_icon' => 'color: {{VALUE}};',
												  '{{WRAPPER}} .sc_chat_popup_button .sc_chat_popup_button_svg svg' => 'fill: {{VALUE}};'
												),
			'popup_button_bg_hover'		=> array( '{{WRAPPER}} .sc_chat_popup_button:hover' => 'background-color: {{VALUE}};' ),
			'popup_button_bd_hover'		=> array( '{{WRAPPER}} .sc_chat_popup_button:hover' => 'border-color: {{VALUE}};' ),
			'popup_button_icon_hover'	=> array( '{{WRAPPER}} .sc_chat_popup_button:hover .sc_chat_popup_button_icon' => 'color: {{VALUE}};',
												  '{{WRAPPER}} .sc_chat_popup_button:hover .sc_chat_popup_button_svg svg' => 'fill: {{VALUE}};'
												),
		);
		// Prepare CSS
		$css = '';
		foreach ( $params as $param => $rules ) {
			if ( ! empty( $atts[ $param ] ) ) {
				foreach ( $rules as $selector => $rule ) {
					$css .= str_replace( '{{WRAPPER}}', '#' . esc_attr( $atts['id'] ), $selector ) . '{'
								. str_replace(
									'{{VALUE}}',
									trx_addons_prepare_css_value( $atts[ $param ] ),
									str_replace(
										array( '{{SIZE}}{{UNIT}}', '{{SIZE}}' ),
										'{{VALUE}}',
										str_replace(
											'{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
											'{{VALUE}}',
											$rule
										)
									)
								)
							. '}';
				}
			}
		}
		// Add CSS to the page
		if ( ! empty( $css ) ) {
			if ( trx_addons_is_preview( 'gb') ) {
				trx_addons_show_layout( $css, '<!-- Chat CSS --><style>', '</style>' );
			} else {
				trx_addons_add_inline_css( $css );
			}
		}
	}
}

// Prepare a data for a requests statistics
if ( ! function_exists( 'trx_addons_sc_chat_prepare_total_generated' ) ) {
	function trx_addons_sc_chat_prepare_total_generated( $data ) {
		if ( ! is_array( $data ) ) {
			$data = array(
				'per_hour' => array_fill( 0, 24, 0 ),
				'per_day' => 0,
				'per_week' => 0,
				'per_month' => 0,
				'per_year' => 0,
				'date' => date( 'Y-m-d' ),
				'week' => date( 'W' ),
				'month' => date( 'm' ),
				'year' => date( 'Y' ),
			);
		}
		if ( $data['date'] != date( 'Y-m-d' ) ) {
			$data['per_hour'] = array_fill( 0, 24, 0 );
			$data['per_day'] = 0;
			$data['date'] = date( 'Y-m-d' );
		}
		if ( ! isset( $data['week'] ) || $data['week'] != date( 'W' ) ) {
			$data['per_week'] = 0;
			$data['week'] = date( 'W' );
		}
		if ( ! isset( $data['month'] ) || $data['month'] != date( 'm' ) ) {
			$data['per_month'] = 0;
			$data['month'] = date( 'm' );
		}
		if ( ! isset( $data['year'] ) || $data['year'] != date( 'Y' ) ) {
			$data['per_year'] = 0;
			$data['year'] = date( 'Y' );
		}
		return $data;
	}
}

// Add number of requests to the total number
if ( ! function_exists( 'trx_addons_sc_chat_set_total_generated' ) ) {
	function trx_addons_sc_chat_set_total_generated( $number, $suffix = '', $user_id = 0 ) {
		$data = trx_addons_sc_chat_prepare_total_generated( $user_id > 0 && ! empty( $suffix )
					? get_user_meta( $user_id, 'trx_addons_sc_chat_total', true )
					: get_transient( "trx_addons_sc_chat_total{$suffix}" )
				);
		$hour = (int) date( 'H' );
		$data['per_hour'][ $hour ] += $number;
		$data['per_day'] += $number;
		$data['per_week'] += $number;
		$data['per_month'] += $number;
		$data['per_year'] += $number;
		if ( $user_id > 0 ) {
			update_user_meta( $user_id, 'trx_addons_sc_chat_total', $data );
		} else {
			set_transient( "trx_addons_sc_chat_total{$suffix}", $data, 24 * 60 * 60 );
		}
	}
}

// Get number of requests
if ( ! function_exists( 'trx_addons_sc_chat_get_total_generated' ) ) {
	function trx_addons_sc_chat_get_total_generated( $per = 'hour', $suffix = '', $user_id = 0 ) {
		$data = trx_addons_sc_chat_prepare_total_generated( $user_id > 0 && ! empty( $suffix )
					? get_user_meta( $user_id, 'trx_addons_sc_chat_total', true )
					: get_transient( "trx_addons_sc_chat_total{$suffix}" )
				);
		if ( $per == 'hour' ) {
			$hour = (int) date( 'H' );
			return $data['per_hour'][ $hour ];
		} else if ( $per == 'day' ) {
			return $data['per_day'];
		} else if ( $per == 'week' ) {
			return $data['per_week'];
		} else if ( $per == 'month' ) {
			return $data['per_month'];
		} else if ( $per == 'year' ) {
			return $data['per_year'];
		} else if ( $per == 'all' ) {
			return $data;
		} else {
			return 0;
		}
	}
}

// Log a visitor ip address to the json file
if ( ! function_exists( 'trx_addons_sc_chat_log_to_json' ) ) {
	function trx_addons_sc_chat_log_to_json( $number ) {
		$ip = ! empty( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : 'Unknown';
		$date = date( 'Y-m-d' );
		$time = date( 'H:i:s' );
		$hour = date( 'H' );
		$json = trx_addons_fgc( TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_ADDONS . 'ai-helper/shortcodes/chat/chat.log' );
		if ( empty( $json ) ) $json = '[]';
		$ips = json_decode( $json, true );
		if ( ! is_array( $ips ) ) {
			$ips = array();
		}
		if ( empty( $ips[ $date ] ) ) {
			$ips[ $date ] = array( 'total' => 0, 'ip' => array(), 'hour' => array() );
		}
		// Log total
		$ips[ $date ]['total'] += $number;
		// Log by IP
		if ( empty( $ips[ $date ]['ip'][ $ip ] ) ) {
			$ips[ $date ]['ip'][ $ip ] = array();
		}
		if ( empty( $ips[ $date ]['ip'][ $ip ][ $time ] ) ) {
			$ips[ $date ]['ip'][ $ip ][ $time ] = 0;
		}
		$ips[ $date ]['ip'][ $ip ][ $time ] += $number;
		// Log by hour
		if ( empty( $ips[ $date ]['hour'][ $hour ] ) ) {
			$ips[ $date ]['hour'][ $hour ] = array();
		}
		if ( empty( $ips[ $date ]['hour'][ $hour ][ $time ] ) ) {
			$ips[ $date ]['hour'][ $hour ][ $time ] = 0;
		}
		$ips[ $date ]['hour'][ $hour ][ $time ] += $number;
		trx_addons_fpc( TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_ADDONS . 'ai-helper/shortcodes/chat/chat.log', json_encode( $ips, JSON_PRETTY_PRINT ) );
	}
}

// Callback function to generate text from the shortcode AJAX request
if ( ! function_exists( 'trx_addons_sc_chat_generate_text' ) ) {
	add_action( 'wp_ajax_nopriv_trx_addons_ai_helper_chat', 'trx_addons_sc_chat_generate_text' );
	add_action( 'wp_ajax_trx_addons_ai_helper_chat', 'trx_addons_sc_chat_generate_text' );
	function trx_addons_sc_chat_generate_text() {

		trx_addons_verify_nonce();

		$count = (int)trx_addons_get_value_gp( 'count' );

		$chat = trx_addons_get_value_gp( 'chat' );
		if ( is_string( $chat ) && substr( $chat, 0, 2 ) == '[{' ) {
			$chat = json_decode( $chat, true );
		}
		if ( is_array( $chat ) ) {
			$chat_last = count( $chat ) - 1;
			if ( $chat_last >= 0 && ! empty( $chat[ $chat_last ]['attachments'] ) && is_array( $chat[ $chat_last ]['attachments'] ) ) {
				for ( $i = 0; $i < count( $chat[ $chat_last ]['attachments'] ); $i++ ) {
					if ( ! empty( $_FILES['chat']['tmp_name'][ $chat_last ]['attachments']['files'][ $i ] ) ) {
						$chat[ $chat_last ]['attachments'][ $i ] = array(
							'name' => $chat[ $chat_last ]['attachments'][ $i ],
							'file' => $_FILES['chat']['tmp_name'][ $chat_last ]['attachments']['files'][ $i ],
						);
					} else {
						unset( $chat[ $chat_last ]['attachments'][ $i ] );
					}
				}
				if ( count( $chat[ $chat_last ]['attachments'] ) == 0 ) {
					unset( $chat[ $chat_last ]['attachments'] );
				}
			}
		}

		$settings = trx_addons_decode_settings( trx_addons_get_value_gp( 'settings' ) );
		$number = 1;	// Number of requests to increment the total number of generated texts

		$model = ! empty( $settings['model'] ) ? $settings['model'] : '';

		$premium = ! empty( $settings['premium'] ) && (int)$settings['premium'] == 1;
		$suffix = $premium ? '_premium' : '';

		$system_prompt = ! empty( $settings['system_prompt'] )
							? $settings['system_prompt']
							: ( empty( $model ) || Utils::is_openai_model( $model ) || Utils::is_google_ai_model( $model )
								? apply_filters( 'trx_addons_filter_sc_chat_system_prompt', trx_addons_get_option( 'ai_helper_sc_chat_system_prompt', __( 'Format the response with HTML tags.', 'trx_addons' ) ) )
								: ''
								);

		$temperature = max( 0, min( 2, ! empty( $settings['temperature'] )
										? $settings['temperature']
										: (float)trx_addons_get_option( 'ai_helper_sc_chat_temperature' )
						) );

		$max_tokens = ! empty( $settings['max_tokens'] )
						? $settings['max_tokens']
						: 0;

		$params = compact( 'chat', 'count', 'system_prompt', 'temperature', 'max_tokens' );
	
		$answer = array(
			'error' => '',
			'data' => array(
				'text' => '',
				'message' => ''
			)
		);

		if ( is_array( $chat ) && count( $chat ) > 0 ) {

			$limits = (int)trx_addons_get_option( "ai_helper_sc_chat_limits{$suffix}" ) > 0 && empty( $settings['nolimits'] );
			$limit_per_request = $max_tokens;
			$lph = $lpv = $lpu = false;
			$used_limits = '';
			$generated = 0;
			$user_id = 0;

			if ( $limits ) {
				$user_level = '';
				$user_limit = false;
				if ( $premium ) {
					$user_id = get_current_user_id();
					$user_level = apply_filters( 'trx_addons_filter_sc_chat_user_level', $user_id > 0 ? 'default' : '', $user_id );
					if ( ! empty( $user_level ) ) {
						$levels = trx_addons_get_option( "ai_helper_sc_chat_levels_premium" );
						$level_idx = trx_addons_array_search( $levels, 'level', $user_level );
						$user_limit = $level_idx !== false ? $levels[ $level_idx ] : false;
						if ( isset( $user_limit['limit'] ) && trim( $user_limit['limit'] ) !== '' ) {
							$generated = trx_addons_sc_chat_get_total_generated( $user_limit['per'], $suffix, $user_id );
							if ( (int)$user_limit['limit'] - $generated > 0 && (int)$user_limit['limit'] - $generated < $number ) {
								$number = $answer['data']['number'] = (int)$user_limit['limit'] - $generated;
							}
							$lpu = (int)$user_limit['limit'] < $generated + $number;
							$used_limits = 'user';
						}
					}
				}
				if ( ! $premium || empty( $user_level ) || ! isset( $user_limit['limit'] ) || trim( $user_limit['limit'] ) === '' ) {
					$generated = trx_addons_sc_chat_get_total_generated( 'hour', $suffix );
					$lph = (int)trx_addons_get_option( "ai_helper_sc_chat_limit_per_hour{$suffix}" ) < $generated + $number;
					$lpv = (int)trx_addons_get_option( "ai_helper_sc_chat_limit_per_visitor{$suffix}" ) < $count;
					$used_limits = 'visitor';
				}
				if ( empty( $limit_per_request ) ) {
					$limit_per_request = (int)trx_addons_get_option( "ai_helper_sc_chat_limit_per_request{$suffix}" );
				}
			}
	
			$demo = $count == 0 || $lpu || $lph || $lpv;

			$api = Utils::get_chat_api( $model );

			if ( $api->get_api_key() != '' && ! $demo ) {

				// Log a visitor ip address to the json file
				//trx_addons_sc_chat_log_to_json( 1 );	// Save to the log a number of requests or tokens number (use $limit_per_request as an argument)?

				// Call the API
				$chat_args = array(
					'messages' => $chat,
					'system_prompt' => $system_prompt,
					'n' => 1,
					'max_tokens' => $limit_per_request,
					'temperature' => $temperature,
				);
				$thread_id = trx_addons_get_value_gp( 'thread_id' );
				if ( ! empty( $thread_id ) ) {
					$chat_args['thread_id'] = $thread_id;
				}
				if ( ! empty( $model ) ) {
					$chat_args['model'] = $model;
					if ( Utils::is_flowise_ai_model( $model ) ) {
						$chat_args['override_config'] = ! empty( $settings['flowise_override'] ) ? $settings['flowise_override'] : '';
					}
				}

				$response = $api->chat( $chat_args, $params );

				$answer = trx_addons_sc_chat_parse_response( $response, $answer );

				trx_addons_sc_chat_set_total_generated( $number, $suffix, $used_limits == 'user' ? $user_id : 0 );

			} else {
				if ( $api->get_api_key() != '' ) {
					$msg = trx_addons_get_option( "ai_helper_sc_chat_limit_alert{$suffix}" );
					$answer['error'] = ! empty( $msg )
													? $msg
													: apply_filters( "trx_addons_filter_sc_chat_limit_alert{$suffix}",
														'<h5 data-lp="' . ( $lpu ? 'lpu' . $generated : ( $lph ? 'lph' . $generated : ( $lpv ? 'lpv' : '' ) ) ) . '">' . __( 'Limits are reached!', 'trx_addons' ) . '</h5>'
														. '<p>' . __( 'The limit of the number of tokens that can be generated per hour has been reached.', 'trx_addons' ) . '</p>'
														. '<p>' . __( ' Please try again later.', 'trx_addons' ) . '</p>'
														);
				} else {
					$answer['error'] = __( 'Error! API key is not specified.', 'trx_addons' );
				}
			}
		} else {
			$answer['error'] = __( 'Error! The prompt is empty.', 'trx_addons' );
		}

		// Return response to the AJAX handler
		trx_addons_ajax_response( apply_filters( 'trx_addons_filter_sc_chat_answer', $answer, $chat ) );
	}
}

// Callback function to fetch answer from the assistant
if ( ! function_exists( 'trx_addons_sc_chat_fetch_answer' ) ) {
	add_action( 'wp_ajax_nopriv_trx_addons_ai_helper_chat_fetch', 'trx_addons_sc_chat_fetch_answer' );
	add_action( 'wp_ajax_trx_addons_ai_helper_chat_fetch', 'trx_addons_sc_chat_fetch_answer' );
	function trx_addons_sc_chat_fetch_answer() {

		trx_addons_verify_nonce();

		$run_id = trx_addons_get_value_gp( 'run_id' );
		$thread_id = trx_addons_get_value_gp( 'thread_id' );
		$settings = trx_addons_decode_settings( trx_addons_get_value_gp( 'settings' ) );

		$answer = array(
			'error' => '',
			'finish_reason' => 'queued',
			'run_id' => $run_id,
			'thread_id' => $thread_id,
			'data' => array(
				'text' => '',
				'message' => ''
			)
		);

		$api = ! empty( $settings['model'] ) ? Utils::get_chat_api( $settings['model'] ) : OpenAiAssistants::instance();

		if ( $api->get_api_key() != '' ) {

			$response = $api->fetch_answer( $thread_id, $run_id );

			$answer = trx_addons_sc_chat_parse_response( $response, $answer );

		} else {
			$answer['error'] = __( 'Error! API key is not specified.', 'trx_addons' );
		}

		// Return response to the AJAX handler
		trx_addons_ajax_response( apply_filters( 'trx_addons_filter_sc_chat_fetch', $answer ) );
	}
}


// Parse chat pesponse from the API and return the answer
if ( ! function_exists( 'trx_addons_sc_chat_parse_response' ) ) {
	function trx_addons_sc_chat_parse_response( $response, $answer ) {

		if ( ! empty( $response['finish_reason'] ) ) {
			$answer['finish_reason'] = $response['finish_reason'];
		}

		if ( ! empty( $response['thread_id'] ) ) {
			$answer['thread_id'] = $response['thread_id'];
		}

		if ( ! empty( $response['choices'][0]['message']['content'] ) ) {
			if ( preg_match( '#<body>([\s\S]*)</body>#U', $response['choices'][0]['message']['content'], $matches ) ) {
				$answer['data']['text'] = wpautop( $matches[1] );
			} else {
				$answer['data']['text'] = preg_match( '/<(br|p|ol|ul|dl|h1|h2|h3|h4|h5|h6)[^>]*>/i', $response['choices'][0]['message']['content'], $matches )
											? wpautop( $response['choices'][0]['message']['content'] )
											: nl2br( str_replace( "\n\n", "\n", $response['choices'][0]['message']['content'] ) );
			}
		} else if ( ! empty( $response['finish_reason'] ) && $response['finish_reason'] == 'queued' && ! empty( $response['run_id'] ) ) {
			$answer['finish_reason'] = $response['finish_reason'];
			$answer['run_id'] = $response['run_id'];
		} else {
			if ( ! empty( $response['error']['message'] ) ) {
				$answer['error'] = $response['error']['message'];
			} else if ( ! empty( $response['error'] ) && is_string( $response['error'] ) ) {
				$answer['error'] = $response['error'];
			} else {
				$answer['error'] = __( 'Error! Unknown response from the API. Maybe the API server is not available right now.', 'trx_addons' );
			}
		}
		return $answer;
	}
}


// Add shortcodes
//----------------------------------------------------------------------------

// Add shortcodes to Elementor
if ( trx_addons_exists_elementor() && function_exists('trx_addons_elm_init') ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_ADDONS . 'ai-helper/shortcodes/chat/chat-sc-elementor.php';
}

// Add shortcodes to Gutenberg
if ( trx_addons_exists_gutenberg() && function_exists( 'trx_addons_gutenberg_get_param_id' ) ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_ADDONS . 'ai-helper/shortcodes/chat/chat-sc-gutenberg.php';
}
