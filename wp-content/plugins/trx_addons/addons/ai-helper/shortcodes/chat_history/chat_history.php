<?php
/**
 * Shortcode: AI Chat History
 *
 * @package ThemeREX Addons
 * @since v2.26.3
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}

// Load required styles and scripts for the frontend
if ( ! function_exists( 'trx_addons_sc_chat_history_load_scripts_front' ) ) {
	add_action( "wp_enqueue_scripts", 'trx_addons_sc_chat_history_load_scripts_front', TRX_ADDONS_ENQUEUE_SCRIPTS_PRIORITY );
	add_action( 'trx_addons_action_pagebuilder_preview_scripts', 'trx_addons_sc_chat_history_load_scripts_front', 10, 1 );
	function trx_addons_sc_chat_history_load_scripts_front( $force = false ) {
		trx_addons_enqueue_optimized( 'sc_chat_history', $force, array(
			'css'  => array(
				'trx_addons-sc_chat_history' => array( 'src' => TRX_ADDONS_PLUGIN_ADDONS . 'ai-helper/shortcodes/chat_history/chat_history.css' ),
			),
			'js' => array(
				'trx_addons-sc_chat_history' => array( 'src' => TRX_ADDONS_PLUGIN_ADDONS . 'ai-helper/shortcodes/chat_history/chat_history.js', 'deps' => 'jquery' ),
			),
			'check' => array(
				array( 'type' => 'sc',  'sc' => 'trx_sc_chat_history' ),
				array( 'type' => 'gb',  'sc' => 'wp:trx-addons/chat_history' ),
				array( 'type' => 'elm', 'sc' => '"widgetType":"trx_sc_chat_history"' ),
				array( 'type' => 'elm', 'sc' => '"shortcode":"[trx_sc_chat_history' ),
			)
		) );
	}
}

// Enqueue responsive styles for frontend
/*
if ( ! function_exists( 'trx_addons_sc_chat_history_load_scripts_front_responsive' ) ) {
	add_action( 'wp_enqueue_scripts', 'trx_addons_sc_chat_history_load_scripts_front_responsive', TRX_ADDONS_ENQUEUE_RESPONSIVE_PRIORITY );
	add_action( 'trx_addons_action_load_scripts_front_sc_chat_history', 'trx_addons_sc_chat_history_load_scripts_front_responsive', 10, 1 );
	function trx_addons_sc_chat_history_load_scripts_front_responsive( $force = false  ) {
		trx_addons_enqueue_optimized_responsive( 'sc_chat_history', $force, array(
			'css'  => array(
				'trx_addons-sc_chat_history-responsive' => array(
					'src' => TRX_ADDONS_PLUGIN_ADDONS . 'ai-helper/shortcodes/chat_history/chat_history.responsive.css',
					'media' => 'sm'
				),
			),
		) );
	}
}
*/

// Merge shortcode's specific styles to the single stylesheet
if ( ! function_exists( 'trx_addons_sc_chat_history_merge_styles' ) ) {
	add_filter( "trx_addons_filter_merge_styles", 'trx_addons_sc_chat_history_merge_styles' );
	function trx_addons_sc_chat_history_merge_styles( $list ) {
		$list[ TRX_ADDONS_PLUGIN_ADDONS . 'ai-helper/shortcodes/chat_history/chat_history.css' ] = false;
		return $list;
	}
}

// Merge shortcode's specific styles to the single stylesheet (responsive)
/*
if ( ! function_exists( 'trx_addons_sc_chat_history_merge_styles_responsive' ) ) {
	add_filter("trx_addons_filter_merge_styles_responsive", 'trx_addons_sc_chat_history_merge_styles_responsive' );
	function trx_addons_sc_chat_history_merge_styles_responsive( $list ) {
		$list[ TRX_ADDONS_PLUGIN_ADDONS . 'ai-helper/shortcodes/chat_history/chat_history.responsive.css' ] = false;
		return $list;
	}
}
*/

// Merge shortcode's specific scripts into single file
if ( ! function_exists( 'trx_addons_sc_chat_history_merge_scripts' ) ) {
	add_action("trx_addons_filter_merge_scripts", 'trx_addons_sc_chat_history_merge_scripts');
	function trx_addons_sc_chat_history_merge_scripts($list) {
		$list[ TRX_ADDONS_PLUGIN_ADDONS . 'ai-helper/shortcodes/chat_history/chat_history.js' ] = false;
		return $list;
	}
}

// Load styles and scripts if present in the cache of the menu
if ( ! function_exists( 'trx_addons_sc_chat_history_check_in_html_output' ) ) {
	add_filter( 'trx_addons_filter_get_menu_cache_html', 'trx_addons_sc_chat_history_check_in_html_output', 10, 1 );
	add_action( 'trx_addons_action_show_layout_from_cache', 'trx_addons_sc_chat_history_check_in_html_output', 10, 1 );
	add_action( 'trx_addons_action_check_page_content', 'trx_addons_sc_chat_history_check_in_html_output', 10, 1 );
	function trx_addons_sc_chat_history_check_in_html_output( $content = '' ) {
		$args = array(
			'check' => array(
				'class=[\'"][^\'"]*sc_chat_history'
			)
		);
		if ( trx_addons_check_in_html_output( 'sc_chat_history', $content, $args ) ) {
			trx_addons_sc_chat_history_load_scripts_front( true );
		}
		return $content;
	}
}


// trx_sc_chat_history
//-------------------------------------------------------------
/*
[trx_sc_chat_history chat_id="slave_chat_id" number="number of topics to show"]
*/
if ( ! function_exists( 'trx_addons_sc_chat_history' ) ) {
	function trx_addons_sc_chat_history( $atts, $content = '' ) {	
		$atts = trx_addons_sc_prepare_atts( 'trx_sc_chat_history', $atts, trx_addons_sc_common_atts( 'trx_sc_chat_history', 'id,title', array(
			// Individual params
			"type" => "default",
			"number" => "",
			"chat_id" => ""
		) ) );

		// Load shortcode-specific scripts and styles
		trx_addons_sc_chat_history_load_scripts_front( true );

		// Check atts
		if ( empty( $atts['number'] ) ) {
			$atts['number'] = 5;
		}
		$atts['number'] = max( 1, min( apply_filters( 'trx_addons_filter_sc_chat_history_max', 20 ), (int) $atts['number'] ) );

		// Load template
		$output = '';

		ob_start();
		trx_addons_get_template_part( array(
										TRX_ADDONS_PLUGIN_ADDONS . 'ai-helper/shortcodes/chat_history/tpl.' . trx_addons_esc( trx_addons_sanitize_file_name( $atts['type'] ) ) . '.php',
										TRX_ADDONS_PLUGIN_ADDONS . 'ai-helper/shortcodes/chat_history/tpl.default.php'
										),
										'trx_addons_args_sc_chat_history',
										$atts
									);
		$output = ob_get_contents();
		ob_end_clean();
		return apply_filters( 'trx_addons_sc_output', $output, 'trx_sc_chat_history', $atts, $content );
	}
}

// Add shortcode [trx_sc_chat_history]
if ( ! function_exists( 'trx_addons_sc_chat_history_add_shortcode' ) ) {
	add_action( 'init', 'trx_addons_sc_chat_history_add_shortcode', 20 );
	function trx_addons_sc_chat_history_add_shortcode() {
		add_shortcode( "trx_sc_chat_history", "trx_addons_sc_chat_history" );
	}
}

// Save all chat messages to show it in the shortcode "Chat history" if the user is logged in
if ( ! function_exists( 'trx_addons_sc_chat_history_save_messages' ) ) {
	add_filter( 'trx_addons_filter_sc_chat_answer', 'trx_addons_sc_chat_history_save_messages', 10, 2 );
	function trx_addons_sc_chat_history_save_messages( $answer, $chat ) {
		$user_id = get_current_user_id();
		if ( $user_id > 0 ) {
			if ( ! empty( $chat[0]['content'] ) ) {
				$topic = $chat[0]['content'];
				$messages = array_merge(
								$chat,
								! empty( $answer['data']['text'] )
									? array( array(
											'role' => 'assistant',
											'content' => $answer['data']['text']
										) )
									: array()
							);
				$max_items = apply_filters( 'trx_addons_filter_sc_chat_history_max', 20 );
				$history = trx_addons_sc_chat_history_get_saved_messages();
				$exists = -1;
				for ( $i = 0; $i < count( $history ); $i++ ) {
					if ( $history[ $i ]['topic'] == $topic ) {
						$exists = $i;
						break;
					}
				}
				if ( $exists < 0 ) {
					$history = array_merge(
									array(
										array(
											'topic' => $topic,
											'messages' => $messages,
											'thread_id' => ! empty( $answer['thread_id'] ) ? $answer['thread_id'] : ''
										)
									),
									$history
								);
					if ( count( $history ) > $max_items ) {
						$history = array_slice( $history, 0, -1 );
					}
				} else {
					$history[ $exists ]['messages'] = $messages;
				}
				update_user_meta( $user_id, 'trx_addons_sc_chat_history', $history );
			}
		}
		return $answer;
	}
}

// Add last answer to the list of the chat messages and save whole chat to show it in the shortcode "Chat history" if the user is logged in
if ( ! function_exists( 'trx_addons_sc_chat_history_add_answer' ) ) {
	add_filter( 'trx_addons_filter_sc_chat_fetch', 'trx_addons_sc_chat_history_add_answer' );
	function trx_addons_sc_chat_history_add_answer( $answer ) {
		$user_id = get_current_user_id();
		if ( $user_id > 0 && ! empty( $answer['data']['text'] ) ) {
			$history = trx_addons_sc_chat_history_get_saved_messages();
			if ( ! empty( $history[0]['messages'] ) ) {
				trx_addons_sc_chat_history_save_messages( $answer, $history[0]['messages'] );
			}
		}
		return $answer;
	}
}

// Get a list with saved history
if ( ! function_exists( 'trx_addons_sc_chat_history_get_saved_messages' ) ) {
	function trx_addons_sc_chat_history_get_saved_messages( $number = 0 ) {
		$history = array();
		$user_id = get_current_user_id();
		if ( $user_id > 0 ) {
			$meta = get_user_meta( $user_id, 'trx_addons_sc_chat_history', true );
			if ( ! empty( $meta ) && is_array( $meta ) ) {
				$history = $meta;
			}
		}
		return $number == 0 ? $history : array_slice( $history, 0, $number );
	}
}


// Add shortcodes
//----------------------------------------------------------------------------

// Add shortcodes to Elementor
if ( trx_addons_exists_elementor() && function_exists('trx_addons_elm_init') ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_ADDONS . 'ai-helper/shortcodes/chat_history/chat_history-sc-elementor.php';
}

// Add shortcodes to Gutenberg
if ( trx_addons_exists_gutenberg() && function_exists( 'trx_addons_gutenberg_get_param_id' ) ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_ADDONS . 'ai-helper/shortcodes/chat_history/chat_history-sc-gutenberg.php';
}
