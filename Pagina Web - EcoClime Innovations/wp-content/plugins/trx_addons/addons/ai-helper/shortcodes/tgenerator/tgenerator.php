<?php
/**
 * Shortcode: Text Generator
 *
 * @package ThemeREX Addons
 * @since v2.22.0
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}

use TrxAddons\AiHelper\Lists;
use TrxAddons\AiHelper\Utils;
use TrxAddons\AiHelper\OpenAiAssistants;


// Load required styles and scripts for the frontend
if ( ! function_exists( 'trx_addons_sc_tgenerator_load_scripts_front' ) ) {
	add_action( "wp_enqueue_scripts", 'trx_addons_sc_tgenerator_load_scripts_front', TRX_ADDONS_ENQUEUE_SCRIPTS_PRIORITY );
	add_action( 'trx_addons_action_pagebuilder_preview_scripts', 'trx_addons_sc_tgenerator_load_scripts_front', 10, 1 );
	function trx_addons_sc_tgenerator_load_scripts_front( $force = false ) {
		trx_addons_enqueue_optimized( 'sc_tgenerator', $force, array(
			'css'  => array(
				'trx_addons-sc_tgenerator' => array( 'src' => TRX_ADDONS_PLUGIN_ADDONS . 'ai-helper/shortcodes/tgenerator/tgenerator.css' ),
			),
			'js' => array(
				'trx_addons-sc_tgenerator' => array( 'src' => TRX_ADDONS_PLUGIN_ADDONS . 'ai-helper/shortcodes/tgenerator/tgenerator.js', 'deps' => 'jquery' ),
			),
			'check' => array(
				array( 'type' => 'sc',  'sc' => 'trx_sc_tgenerator' ),
				array( 'type' => 'gb',  'sc' => 'wp:trx-addons/tgenerator' ),
				array( 'type' => 'elm', 'sc' => '"widgetType":"trx_sc_tgenerator"' ),
				array( 'type' => 'elm', 'sc' => '"shortcode":"[trx_sc_tgenerator' ),
			)
		) );
	}
}

// Enqueue responsive styles for frontend
if ( ! function_exists( 'trx_addons_sc_tgenerator_load_scripts_front_responsive' ) ) {
	add_action( 'wp_enqueue_scripts', 'trx_addons_sc_tgenerator_load_scripts_front_responsive', TRX_ADDONS_ENQUEUE_RESPONSIVE_PRIORITY );
	add_action( 'trx_addons_action_load_scripts_front_sc_tgenerator', 'trx_addons_sc_tgenerator_load_scripts_front_responsive', 10, 1 );
	function trx_addons_sc_tgenerator_load_scripts_front_responsive( $force = false  ) {
		trx_addons_enqueue_optimized_responsive( 'sc_tgenerator', $force, array(
			'css'  => array(
				'trx_addons-sc_tgenerator-responsive' => array(
					'src' => TRX_ADDONS_PLUGIN_ADDONS . 'ai-helper/shortcodes/tgenerator/tgenerator.responsive.css',
					'media' => 'sm'
				),
			),
		) );
	}
}

// Merge shortcode's specific styles to the single stylesheet
if ( ! function_exists( 'trx_addons_sc_tgenerator_merge_styles' ) ) {
	add_filter( "trx_addons_filter_merge_styles", 'trx_addons_sc_tgenerator_merge_styles' );
	function trx_addons_sc_tgenerator_merge_styles( $list ) {
		$list[ TRX_ADDONS_PLUGIN_ADDONS . 'ai-helper/shortcodes/tgenerator/tgenerator.css' ] = false;
		return $list;
	}
}

// Merge shortcode's specific styles to the single stylesheet (responsive)
if ( ! function_exists( 'trx_addons_sc_tgenerator_merge_styles_responsive' ) ) {
	add_filter("trx_addons_filter_merge_styles_responsive", 'trx_addons_sc_tgenerator_merge_styles_responsive' );
	function trx_addons_sc_tgenerator_merge_styles_responsive( $list ) {
		$list[ TRX_ADDONS_PLUGIN_ADDONS . 'ai-helper/shortcodes/tgenerator/tgenerator.responsive.css' ] = false;
		return $list;
	}
}

// Merge shortcode's specific scripts into single file
if ( ! function_exists( 'trx_addons_sc_tgenerator_merge_scripts' ) ) {
	add_action("trx_addons_filter_merge_scripts", 'trx_addons_sc_tgenerator_merge_scripts');
	function trx_addons_sc_tgenerator_merge_scripts($list) {
		$list[ TRX_ADDONS_PLUGIN_ADDONS . 'ai-helper/shortcodes/tgenerator/tgenerator.js' ] = false;
		return $list;
	}
}

// Load styles and scripts if present in the cache of the menu
if ( ! function_exists( 'trx_addons_sc_tgenerator_check_in_html_output' ) ) {
	add_filter( 'trx_addons_filter_get_menu_cache_html', 'trx_addons_sc_tgenerator_check_in_html_output', 10, 1 );
	add_action( 'trx_addons_action_show_layout_from_cache', 'trx_addons_sc_tgenerator_check_in_html_output', 10, 1 );
	add_action( 'trx_addons_action_check_page_content', 'trx_addons_sc_tgenerator_check_in_html_output', 10, 1 );
	function trx_addons_sc_tgenerator_check_in_html_output( $content = '' ) {
		$args = array(
			'check' => array(
				'class=[\'"][^\'"]*sc_tgenerator'
			)
		);
		if ( trx_addons_check_in_html_output( 'sc_tgenerator', $content, $args ) ) {
			trx_addons_sc_tgenerator_load_scripts_front( true );
		}
		return $content;
	}
}


// trx_sc_tgenerator
//-------------------------------------------------------------
/*
[trx_sc_tgenerator id="unique_id" prompt="prompt text for ai" command="blog-post"]
*/
if ( ! function_exists( 'trx_addons_sc_tgenerator' ) ) {
	function trx_addons_sc_tgenerator( $atts, $content = '' ) {	
		$atts = trx_addons_sc_prepare_atts( 'trx_sc_tgenerator', $atts, trx_addons_sc_common_atts( 'trx_sc_tgenerator', 'id,title', array(
			// Individual params
			"type" => "default",
			"prompt" => "",
			"prompt_width" => 100,
			"placeholder_text" => "",
			"button_text" => "",
			"align" => "",
			"model" => "",
			"flowise_override" => "",
			"premium" => 0,
			"show_limits" => 0,
			"system_prompt" => "",
			"temperature" => 0,
			"max_tokens" => 0,
		) ) );

		// Load shortcode-specific scripts and styles
		trx_addons_sc_tgenerator_load_scripts_front( true );

		// Load template
		$output = '';

		ob_start();
		if ( ! Utils::is_text_api_available() ) {
			trx_addons_get_template_part( 'templates/tpl.sc_placeholder.php',
				'trx_addons_args_sc_placeholder',
				apply_filters( 'trx_addons_filter_sc_placeholder_args', array(
					'sc' => 'trx_sc_tgenerator',
					'title' => __('AI Text Generator is not available - token for access to the API for text generation is not specified', 'trx_addons'),
					'class' => 'sc_placeholder_with_title'
					) )
			);
		} else {
			trx_addons_get_template_part( array(
										TRX_ADDONS_PLUGIN_ADDONS . 'ai-helper/shortcodes/tgenerator/tpl.' . trx_addons_esc( trx_addons_sanitize_file_name( $atts['type'] ) ) . '.php',
										TRX_ADDONS_PLUGIN_ADDONS . 'ai-helper/shortcodes/tgenerator/tpl.default.php'
										),
										'trx_addons_args_sc_tgenerator',
										$atts
									);
		}
		$output = ob_get_contents();
		ob_end_clean();
		return apply_filters( 'trx_addons_sc_output', $output, 'trx_sc_tgenerator', $atts, $content );
	}
}

// Add shortcode [trx_sc_tgenerator]
if ( ! function_exists( 'trx_addons_sc_tgenerator_add_shortcode' ) ) {
	add_action( 'init', 'trx_addons_sc_tgenerator_add_shortcode', 20 );
	function trx_addons_sc_tgenerator_add_shortcode() {
		add_shortcode( "trx_sc_tgenerator", "trx_addons_sc_tgenerator" );
	}
}

// Return a dropdown layout from the list
if ( ! function_exists( 'trx_addons_sc_tgenerator_get_dropdown' ) ) {
	function trx_addons_sc_tgenerator_get_dropdown( $list, $type, $class = '' ) {
		$html = '';
		$num = 0;
		foreach ( $list as $value => $title ) {
			if ( $num++ == 0 ) {
				$html .= '<span class="sc_tgenerator_form_field_select sc_tgenerator_form_field_' . esc_attr( $type ) . ( ! empty( $class ) ? ' ' . esc_attr( $class ) : '' ) . '"'
							. ' data-value="' . esc_attr( $value ) . '"'
							. ( ! empty( $title['prompt'] ) ? ' data-prompt="' . esc_attr( $title['prompt'] ) . '"' : '' )
							. ( ! empty( $title['variations'] ) ? ' data-variations="' . esc_attr( $title['variations'] ) . '"' : '' )
						. '>'
							. '<span class="sc_tgenerator_form_field_select_label" tabindex="0">'
								. esc_html( ! empty( $title['title'] ) ? $title['title'] : $title )
							. '</span>'
						. '<span class="sc_tgenerator_form_field_select_options">';
			}
			$html .= '<span class="sc_tgenerator_form_field_select_option"'
							. ' data-value="' . esc_attr( $value ) . '"'
							. ( ! empty( $title['prompt'] ) ? ' data-prompt="' . esc_attr( $title['prompt'] ) . '"' : '' )
							. ( ! empty( $title['variations'] ) ? ' data-variations="' . esc_attr( $title['variations'] ) . '"' : '' )
						. ' tabindex="0">'
							. esc_html( ! empty( $title['title'] ) ? $title['title'] : $title )
						. '</span>';
		}
		$html .= '</span></span>';
		return $html;
	}
}

// Return a <select> from the list of a text tones
if ( ! function_exists( 'trx_addons_sc_tgenerator_get_list_tones' ) ) {
	function trx_addons_sc_tgenerator_get_list_tones() {
		return apply_filters( 'trx_addons_filter_sc_tgenerator_get_list_tones', trx_addons_sc_tgenerator_get_dropdown( Lists::get_list_ai_text_tones(), 'tone', 'sc_tgenerator_form_field_hidden' ) );
	}
}

// Return a <select> from the list of a text languages
if ( ! function_exists( 'trx_addons_sc_tgenerator_get_list_languages' ) ) {
	function trx_addons_sc_tgenerator_get_list_languages() {
		return apply_filters( 'trx_addons_filter_sc_tgenerator_get_list_languages', trx_addons_sc_tgenerator_get_dropdown( Lists::get_list_ai_text_languages(), 'language', 'sc_tgenerator_form_field_hidden' ) );
	}
}

// Return a dropdown from the list of commands
if ( ! function_exists( 'trx_addons_sc_tgenerator_get_list_commands' ) ) {
	function trx_addons_sc_tgenerator_get_list_commands( $type ) {
		$commands = Lists::get_list_ai_commands();
		$filtered = array_merge(
						array( '' => sprintf( '- %s -', $type == 'write' ? esc_html__( 'text type', 'trx_addons' ) : esc_html__( 'process text', 'trx_addons' ) ) ),
						array_filter( $commands, function( $command ) use( $type ) {
							return strpos( $command, $type ) === 0 && ! in_array( $command, array( 'process_title', 'process_excerpt', 'process_continue' ) );
						}, ARRAY_FILTER_USE_KEY )
					);
		return apply_filters( 'trx_addons_filter_sc_tgenerator_get_list_commands', trx_addons_sc_tgenerator_get_dropdown( $filtered, $type ) );
	}
}

// Prepare a data for a requests statistics
if ( ! function_exists( 'trx_addons_sc_tgenerator_prepare_total_generated' ) ) {
	function trx_addons_sc_tgenerator_prepare_total_generated( $data ) {
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
if ( ! function_exists( 'trx_addons_sc_tgenerator_set_total_generated' ) ) {
	function trx_addons_sc_tgenerator_set_total_generated( $number, $suffix = '', $user_id = 0 ) {
		$data = trx_addons_sc_tgenerator_prepare_total_generated( $user_id > 0 && ! empty( $suffix )
					? get_user_meta( $user_id, 'trx_addons_sc_tgenerator_total', true )
					: get_transient( "trx_addons_sc_tgenerator_total{$suffix}" )
				);
		$hour = (int) date( 'H' );
		$data['per_hour'][ $hour ] += $number;
		$data['per_day'] += $number;
		$data['per_week'] += $number;
		$data['per_month'] += $number;
		$data['per_year'] += $number;
		if ( $user_id > 0 ) {
			update_user_meta( $user_id, 'trx_addons_sc_tgenerator_total', $data );
		} else {
			set_transient( "trx_addons_sc_tgenerator_total{$suffix}", $data, 24 * 60 * 60 );
		}
	}
}

// Get number of requests
if ( ! function_exists( 'trx_addons_sc_tgenerator_get_total_generated' ) ) {
	function trx_addons_sc_tgenerator_get_total_generated( $per = 'hour', $suffix = '', $user_id = 0 ) {
		$data = trx_addons_sc_tgenerator_prepare_total_generated( $user_id > 0 && ! empty( $suffix )
					? get_user_meta( $user_id, 'trx_addons_sc_tgenerator_total', true )
					: get_transient( "trx_addons_sc_tgenerator_total{$suffix}" )
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
if ( ! function_exists( 'trx_addons_sc_tgenerator_log_to_json' ) ) {
	function trx_addons_sc_tgenerator_log_to_json( $number ) {
		$ip = ! empty( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : 'Unknown';
		$date = date( 'Y-m-d' );
		$time = date( 'H:i:s' );
		$hour = date( 'H' );
		$json = trx_addons_fgc( TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_ADDONS . 'ai-helper/shortcodes/tgenerator/tgenerator.log' );
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
		trx_addons_fpc( TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_ADDONS . 'ai-helper/shortcodes/tgenerator/tgenerator.log', json_encode( $ips, JSON_PRETTY_PRINT ) );
	}
}

// Callback function to generate text from the shortcode AJAX request
if ( ! function_exists( 'trx_addons_sc_tgenerator_generate_text' ) ) {
	add_action( 'wp_ajax_nopriv_trx_addons_ai_helper_tgenerator', 'trx_addons_sc_tgenerator_generate_text' );
	add_action( 'wp_ajax_trx_addons_ai_helper_tgenerator', 'trx_addons_sc_tgenerator_generate_text' );
	function trx_addons_sc_tgenerator_generate_text() {

		trx_addons_verify_nonce();

		$prompt = trx_addons_get_value_gp( 'prompt' );
		$count = (int)trx_addons_get_value_gp( 'count' );
		$command = trx_addons_get_value_gp( 'command' );
		$tone = trx_addons_get_value_gp( 'tone' );
		$language = trx_addons_get_value_gp( 'language' );
		$content = trx_addons_get_value_gp( 'content' );
		if ( ! in_array( $command, array( 'process_tone', 'process_translate' ) ) ) {
			$content = strip_tags( $content );
		}

		$settings = trx_addons_decode_settings( trx_addons_get_value_gp( 'settings' ) );
		$number = 1;	// Number of requests to increment the total number of generated texts

		$model = ! empty( $settings['model'] ) ? $settings['model'] : '';

		$premium = ! empty( $settings['premium'] ) && (int)$settings['premium'] == 1;
		$suffix = $premium ? '_premium' : '';

		$system_prompt = ! empty( $settings['system_prompt'] )
							? $settings['system_prompt']
							: ( empty( $model ) || Utils::is_openai_model( $model )
								? apply_filters( 'trx_addons_filter_sc_tgenerator_system_prompt', trx_addons_get_option( 'ai_helper_sc_tgenerator_system_prompt', __( 'Format the response with HTML tags.', 'trx_addons' ) ) )
								: ''
								);

		$temperature = max( 0, min( 2, ! empty( $settings['temperature'] )
										? $settings['temperature']
										: (float)trx_addons_get_option( 'ai_helper_sc_tgenerator_temperature' )
						) );

		$max_tokens = ! empty( $settings['max_tokens'] )
						? $settings['max_tokens']
						: 0;

		$params = compact( 'command', 'prompt', 'content', 'tone', 'language', 'system_prompt', 'temperature', 'max_tokens' );
	
		$answer = array(
			'error' => '',
			'data' => array(
				'text' => '',
				'message' => ''
			)
		);

		if ( ! empty( $prompt ) ) {

			$limits = (int)trx_addons_get_option( "ai_helper_sc_tgenerator_limits{$suffix}" ) > 0;
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
					$user_level = apply_filters( 'trx_addons_filter_sc_tgenerator_user_level', $user_id > 0 ? 'default' : '', $user_id );
					if ( ! empty( $user_level ) ) {
						$levels = trx_addons_get_option( "ai_helper_sc_tgenerator_levels_premium" );
						$level_idx = trx_addons_array_search( $levels, 'level', $user_level );
						$user_limit = $level_idx !== false ? $levels[ $level_idx ] : false;
						if ( isset( $user_limit['limit'] ) && trim( $user_limit['limit'] ) !== '' ) {
							$generated = trx_addons_sc_tgenerator_get_total_generated( $user_limit['per'], $suffix, $user_id );
							if ( (int)$user_limit['limit'] - $generated > 0 && (int)$user_limit['limit'] - $generated < $number ) {
								$number = $answer['data']['number'] = (int)$user_limit['limit'] - $generated;
							}
							$lpu = (int)$user_limit['limit'] < $generated + $number;
							$used_limits = 'user';
						}
					}
				}
				if ( ! $premium || empty( $user_level ) || ! isset( $user_limit['limit'] ) || trim( $user_limit['limit'] ) === '' ) {
					$generated = trx_addons_sc_tgenerator_get_total_generated( 'hour', $suffix );
					$lph = (int)trx_addons_get_option( "ai_helper_sc_tgenerator_limit_per_hour{$suffix}" ) < $generated + $number;
					$lpv = (int)trx_addons_get_option( "ai_helper_sc_tgenerator_limit_per_visitor{$suffix}" ) < $count;
					$used_limits = 'visitor';
				}
				if ( empty( $limit_per_request ) ) {
					$limit_per_request = (int)trx_addons_get_option( "ai_helper_sc_tgenerator_limit_per_request{$suffix}" );
				}
			}
	
			$demo = $count == 0 || $lpu || $lph || $lpv;

			$api = Utils::get_chat_api( $model );

			if ( $api->get_api_key() != '' && ! $demo ) {

				// Log a visitor ip address to the json file
				//trx_addons_sc_tgenerator_log_to_json( 1 );	// Save to the log a number of requests or tokens number (use $limit_per_request as an argument)?

				$commands = Lists::get_list_ai_commands();

				// Prepage a prompt part for variations
				$variations = '';
				if ( ! empty( $commands[ $command ]['variations'] ) ) {
					$variations = sprintf( __( 'Generate %d variants of the %s as a single sentence for each variant. Start each variant on a new line and enclose it in double curly brackets. Return only text without numeration and any other messages.', 'trx_addons' ),
											apply_filters( 'trx_addons_filter_ai_helper_variations_total', $commands[ $command ]['variations'], $command ),
											$commands[ $command ]['variation_name'] 
					);
					$prompt .= trx_addons_strdot( $prompt ) . ' ' . $variations;
				}

				// Combine a prompt with a content and variations part
				$prompt = strpos( $command, 'write_' ) !== false
							? $prompt . ': ' . trx_addons_strdot( $content )
								. ' ' . apply_filters( 'trx_addons_filter_ai_helper_write_post_subprompt', __( 'The text should consist of several sections with subheadings and each section should consist at least 3-4 paragraphs.', 'trx_addons' ), $params )
							: ( ! empty( $variations ) ? $variations : trx_addons_strdot( $prompt ) )
								. ( $command != 'process_translate'
									? ' ' . __( 'Respond in the same language as the text being processed.', 'trx_addons' )
									: ''
									)
								. ' ' . sprintf( __( "The text to be processed is enclosed to double curly braces: %s", 'trx_addons' ),
												'{{ ' . preg_replace( "/(\r?\n){2,}/", '$1', $content ) . ' }}'
												);

				// Call the API
				$chat_args = array(
					'prompt' => apply_filters( 'trx_addons_filter_tgenerator_prompt', $prompt, $params ),
					'role' => 'text_generator',
					'system_prompt' => $system_prompt,
					'n' => 1,
					'max_tokens' => $limit_per_request,
					'temperature' => $temperature,
				);
				if ( ! empty( $model ) ) {
					$chat_args['model'] = $model;
					if ( Utils::is_flowise_ai_model( $model ) ) {
						$chat_args['override_config'] = ! empty( $settings['flowise_override'] ) ? $settings['flowise_override'] : '';
					}
				}

				$response = $api->query( $chat_args, $params );

				$answer = trx_addons_sc_tgenerator_parse_response( $response, $answer );

				trx_addons_sc_tgenerator_set_total_generated( $number, $suffix, $used_limits == 'user' ? $user_id : 0 );

			} else {
				if ( $api->get_api_key() != '' ) {
					$msg = trx_addons_get_option( "ai_helper_sc_tgenerator_limit_alert{$suffix}" );
					$answer['error'] = ! empty( $msg )
													? $msg
													: apply_filters( "trx_addons_filter_sc_tgenerator_limit_alert{$suffix}",
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
		trx_addons_ajax_response( apply_filters( 'trx_addons_filter_sc_tgenerator_answer', $answer ) );
	}
}

// Callback function to fetch answer from the assistant
if ( ! function_exists( 'trx_addons_sc_tgenerator_fetch_answer' ) ) {
	add_action( 'wp_ajax_nopriv_trx_addons_ai_helper_tgenerator_fetch', 'trx_addons_sc_tgenerator_fetch_answer' );
	add_action( 'wp_ajax_trx_addons_ai_helper_tgenerator_fetch', 'trx_addons_sc_tgenerator_fetch_answer' );
	function trx_addons_sc_tgenerator_fetch_answer() {

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

			$answer = trx_addons_sc_tgenerator_parse_response( $response, $answer );

		} else {
			$answer['error'] = __( 'Error! API key is not specified.', 'trx_addons' );
		}

		// Return response to the AJAX handler
		trx_addons_ajax_response( apply_filters( 'trx_addons_filter_sc_tgenerator_fetch', $answer ) );
	}
}


// Parse chat pesponse from the API and return the answer
if ( ! function_exists( 'trx_addons_sc_tgenerator_parse_response' ) ) {
	function trx_addons_sc_tgenerator_parse_response( $response, $answer ) {

		if ( ! empty( $response['finish_reason'] ) ) {
			$answer['finish_reason'] = $response['finish_reason'];
		}

		if ( ! empty( $response['thread_id'] ) ) {
			$answer['thread_id'] = $response['thread_id'];
		}

		if ( ! empty( $response['choices'][0]['message']['content'] ) ) {
			// Get all variations from the response. Each variation is separated by a new line and encosed in double curly brackets.
			if ( substr_count( $response['choices'][0]['message']['content'], '{{' ) > 1 ) {
				if ( preg_match_all( '/{{(.*)}}/U', $response['choices'][0]['message']['content'], $matches ) ) {
					$answer['data']['text'] = $matches[1];
				} else {
					$answer['data']['text'] = preg_match( '/<(br|p|ol|ul|dl|h1|h2|h3|h4|h5|h6)[^>]*>/i', $response['choices'][0]['message']['content'], $matches )
												? wpautop( str_replace( array( '{{', '}}' ), '', $response['choices'][0]['message']['content'] ) )
												: nl2br( str_replace( array( '{{', '}}', "\n\n" ), array( '', '', "\n"), $response['choices'][0]['message']['content'] ) );
				}

			// Get whole text from the response as a single variant
			} else {
				if ( preg_match( '#<body>([\s\S]*)</body>#U', $response['choices'][0]['message']['content'], $matches ) ) {
					$answer['data']['text'] = wpautop( $matches[1] );
				} else {
					$answer['data']['text'] = preg_match( '/<(br|p|ol|ul|dl|h1|h2|h3|h4|h5|h6)[^>]*>/i', $response['choices'][0]['message']['content'], $matches )
												? wpautop( $response['choices'][0]['message']['content'] )
												: nl2br( str_replace( "\n\n", "\n", $response['choices'][0]['message']['content'] ) );
				}
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
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_ADDONS . 'ai-helper/shortcodes/tgenerator/tgenerator-sc-elementor.php';
}

// Add shortcodes to Gutenberg
if ( trx_addons_exists_gutenberg() && function_exists( 'trx_addons_gutenberg_get_param_id' ) ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_ADDONS . 'ai-helper/shortcodes/tgenerator/tgenerator-sc-gutenberg.php';
}
