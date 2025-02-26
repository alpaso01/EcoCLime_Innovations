<?php
/**
 * Shortcode: Audio Generator
 *
 * @package ThemeREX Addons
 * @since v2.31.0
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}

use TrxAddons\AiHelper\Utils;
use TrxAddons\AiHelper\Lists;


// Load required styles and scripts for the frontend
if ( ! function_exists( 'trx_addons_sc_agenerator_load_scripts_front' ) ) {
	add_action( "wp_enqueue_scripts", 'trx_addons_sc_agenerator_load_scripts_front', TRX_ADDONS_ENQUEUE_SCRIPTS_PRIORITY );
	add_action( 'trx_addons_action_pagebuilder_preview_scripts', 'trx_addons_sc_agenerator_load_scripts_front', 10, 1 );
	function trx_addons_sc_agenerator_load_scripts_front( $force = false ) {
		trx_addons_enqueue_optimized( 'sc_agenerator', $force, array(
/*
			'lib' => array(
				'css' => array(
					'msgbox' => array( 'src' => 'js/msgbox/msgbox.css' ),
				),
				'js' => array(
					'msgbox' => array( 'src' => 'js/msgbox/msgbox.js' ),
				)
			),
*/
			'css'  => array(
				'trx_addons-sc_agenerator' => array( 'src' => TRX_ADDONS_PLUGIN_ADDONS . 'ai-helper/shortcodes/agenerator/agenerator.css' ),
			),
			'js' => array(
				'trx_addons-sc_agenerator' => array( 'src' => TRX_ADDONS_PLUGIN_ADDONS . 'ai-helper/shortcodes/agenerator/agenerator.js', 'deps' => 'jquery' ),
			),
			'check' => array(
				array( 'type' => 'sc',  'sc' => 'trx_sc_agenerator' ),
				array( 'type' => 'gb',  'sc' => 'wp:trx-addons/agenerator' ),
				array( 'type' => 'elm', 'sc' => '"widgetType":"trx_sc_agenerator"' ),
				array( 'type' => 'elm', 'sc' => '"shortcode":"[trx_sc_agenerator' ),
			)
		) );
	}
}

// Enqueue responsive styles for frontend
if ( ! function_exists( 'trx_addons_sc_agenerator_load_scripts_front_responsive' ) ) {
	add_action( 'wp_enqueue_scripts', 'trx_addons_sc_agenerator_load_scripts_front_responsive', TRX_ADDONS_ENQUEUE_RESPONSIVE_PRIORITY );
	add_action( 'trx_addons_action_load_scripts_front_sc_agenerator', 'trx_addons_sc_agenerator_load_scripts_front_responsive', 10, 1 );
	function trx_addons_sc_agenerator_load_scripts_front_responsive( $force = false  ) {
		trx_addons_enqueue_optimized_responsive( 'sc_agenerator', $force, array(
			'css'  => array(
				'trx_addons-sc_agenerator-responsive' => array(
					'src' => TRX_ADDONS_PLUGIN_ADDONS . 'ai-helper/shortcodes/agenerator/agenerator.responsive.css',
					'media' => 'lg'
				),
			),
		) );
	}
}

// Add messages to the list with JS vars
if ( ! function_exists( 'trx_addons_sc_agenerator_localize_script' ) ) {
	add_action( 'trx_addons_filter_localize_script', 'trx_addons_sc_agenerator_localize_script' );
	function trx_addons_sc_agenerator_localize_script( $vars ) {
		$vars['msg_ai_helper_download'] = __( 'Download', 'trx_addons' );
		$vars['msg_ai_helper_download_error'] = __( 'Error', 'trx_addons' );
		$vars['msg_ai_helper_download_expired'] = __( 'The generated image cache timed out. The download link is no longer valid.<br>But you can still download the file by right-clicking on it and selecting "Save Media As..."', 'trx_addons' );
		$vars['msg_ai_helper_agenerator_disabled'] = __( 'Audio generation is not available in edit mode!', 'trx_addons' );
		return $vars;
	}
}

// Merge shortcode's specific styles to the single stylesheet
if ( ! function_exists( 'trx_addons_sc_agenerator_merge_styles' ) ) {
	add_filter( "trx_addons_filter_merge_styles", 'trx_addons_sc_agenerator_merge_styles' );
	function trx_addons_sc_agenerator_merge_styles( $list ) {
		$list[ TRX_ADDONS_PLUGIN_ADDONS . 'ai-helper/shortcodes/agenerator/agenerator.css' ] = false;
		return $list;
	}
}

// Merge shortcode's specific styles to the single stylesheet (responsive)
if ( ! function_exists( 'trx_addons_sc_agenerator_merge_styles_responsive' ) ) {
	add_filter("trx_addons_filter_merge_styles_responsive", 'trx_addons_sc_agenerator_merge_styles_responsive' );
	function trx_addons_sc_agenerator_merge_styles_responsive( $list ) {
		$list[ TRX_ADDONS_PLUGIN_ADDONS . 'ai-helper/shortcodes/agenerator/agenerator.responsive.css' ] = false;
		return $list;
	}
}

// Merge shortcode's specific scripts into single file
if ( ! function_exists( 'trx_addons_sc_agenerator_merge_scripts' ) ) {
	add_action("trx_addons_filter_merge_scripts", 'trx_addons_sc_agenerator_merge_scripts');
	function trx_addons_sc_agenerator_merge_scripts($list) {
		$list[ TRX_ADDONS_PLUGIN_ADDONS . 'ai-helper/shortcodes/agenerator/agenerator.js' ] = false;
		return $list;
	}
}

// Load styles and scripts if present in the cache of the menu
if ( ! function_exists( 'trx_addons_sc_agenerator_check_in_html_output' ) ) {
	add_filter( 'trx_addons_filter_get_menu_cache_html', 'trx_addons_sc_agenerator_check_in_html_output', 10, 1 );
	add_action( 'trx_addons_action_show_layout_from_cache', 'trx_addons_sc_agenerator_check_in_html_output', 10, 1 );
	add_action( 'trx_addons_action_check_page_content', 'trx_addons_sc_agenerator_check_in_html_output', 10, 1 );
	function trx_addons_sc_agenerator_check_in_html_output( $content = '' ) {
		$args = array(
			'check' => array(
				'class=[\'"][^\'"]*sc_agenerator'
			)
		);
		if ( trx_addons_check_in_html_output( 'sc_agenerator', $content, $args ) ) {
			trx_addons_sc_agenerator_load_scripts_front( true );
		}
		return $content;
	}
}


// trx_sc_agenerator
//-------------------------------------------------------------
/*
[trx_sc_agenerator id="unique_id" number="2" prompt="prompt text for ai"]
*/
if ( ! function_exists( 'trx_addons_sc_agenerator' ) ) {
	function trx_addons_sc_agenerator( $atts, $content = '' ) {	
		$atts = trx_addons_sc_prepare_atts( 'trx_sc_agenerator', $atts, trx_addons_sc_common_atts( 'trx_sc_agenerator', 'id,title', array(
			// Individual params
			"type" => "default",
			"tags" => "",
			"tags_label" => "",
			"prompt" => "",
			"prompt_width" => "",
			"placeholder_text" => "",
			"button_text" => "",
			"language" => "english",
			"emotion" => "neutral",
			"voice" => "",
			"model" => "",
			"show_settings" => 0,
			"show_limits" => 0,
			"show_download" => 0,
			"base64" => 0,
			"align" => "",
			"align_tablet" => "",
			"align_mobile" => "",
			"premium" => 0,
			"system_prompt" => "",
			"demo_audio" => "",
			"demo_audio_url" => "",
		) ) );

		// Load shortcode-specific scripts and styles
		trx_addons_sc_agenerator_load_scripts_front( true );

		// Load template
		$output = '';
		if ( ! empty( $atts['demo_audio'] ) && ! is_array( $atts['demo_audio'] ) ) {
			$demo_audio = explode( '|', $atts['demo_audio'] );
			$atts['demo_audio'] = array();
			foreach ( $demo_audio as $url ) {
				$atts['demo_audio'][] = array( 'audio' => array( 'url' => $url ) );
			}
		}

		ob_start();
		if ( ! Utils::is_audio_api_available() ) {
			trx_addons_get_template_part( 'templates/tpl.sc_placeholder.php',
				'trx_addons_args_sc_placeholder',
				apply_filters( 'trx_addons_filter_sc_placeholder_args', array(
					'sc' => 'trx_sc_agenerator',
					'title' => __('AI Audio Generator is not available - token for access to the API for audio generation is not specified', 'trx_addons'),
					'class' => 'sc_placeholder_with_title'
					) )
			);
		} else {
			trx_addons_get_template_part( array(
										TRX_ADDONS_PLUGIN_ADDONS . 'ai-helper/shortcodes/agenerator/tpl.' . trx_addons_esc( trx_addons_sanitize_file_name( $atts['type'] ) ) . '.php',
										TRX_ADDONS_PLUGIN_ADDONS . 'ai-helper/shortcodes/agenerator/tpl.default.php'
										),
										'trx_addons_args_sc_agenerator',
										$atts
									);
		}
		$output = ob_get_contents();
		ob_end_clean();
		return apply_filters( 'trx_addons_sc_output', $output, 'trx_sc_agenerator', $atts, $content );
	}
}

// Add shortcode [trx_sc_agenerator]
if ( ! function_exists( 'trx_addons_sc_agenerator_add_shortcode' ) ) {
	add_action( 'init', 'trx_addons_sc_agenerator_add_shortcode', 20 );
	function trx_addons_sc_agenerator_add_shortcode() {
		add_shortcode( "trx_sc_agenerator", "trx_addons_sc_agenerator" );
	}
}

// Prepare a data for generated audio
if ( ! function_exists( 'trx_addons_sc_agenerator_prepare_total_generated' ) ) {
	function trx_addons_sc_agenerator_prepare_total_generated( $data ) {
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

// Add number of generated audio to the total number
if ( ! function_exists( 'trx_addons_sc_agenerator_set_total_generated' ) ) {
	function trx_addons_sc_agenerator_set_total_generated( $number = 1, $suffix = '', $user_id = 0 ) {
		$data = trx_addons_sc_agenerator_prepare_total_generated( $user_id > 0 && ! empty( $suffix )
					? get_user_meta( $user_id, 'trx_addons_sc_agenerator_total', true )
					: get_transient( "trx_addons_sc_agenerator_total{$suffix}" )
				);
		$hour = (int) date( 'H' );
		$data['per_hour'][ $hour ] += $number;
		$data['per_day'] += $number;
		$data['per_week'] += $number;
		$data['per_month'] += $number;
		$data['per_year'] += $number;
		if ( $user_id > 0 ) {
			update_user_meta( $user_id, 'trx_addons_sc_agenerator_total', $data );
		} else {
			set_transient( "trx_addons_sc_agenerator_total{$suffix}", $data, 24 * 60 * 60 );
		}
	}
}

// Get number of generated audio
if ( ! function_exists( 'trx_addons_sc_agenerator_get_total_generated' ) ) {
	function trx_addons_sc_agenerator_get_total_generated( $per = 'hour', $suffix = '', $user_id = 0 ) {
		$data = trx_addons_sc_agenerator_prepare_total_generated( $user_id > 0 && ! empty( $suffix )
					? get_user_meta( $user_id, 'trx_addons_sc_agenerator_total', true )
					: get_transient( "trx_addons_sc_agenerator_total{$suffix}" )
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
if ( ! function_exists( 'trx_addons_sc_agenerator_log_to_json' ) ) {
	function trx_addons_sc_agenerator_log_to_json( $number = 1, $suffix = '' ) {
		$ip = ! empty( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : 'Unknown';
		$date = date( 'Y-m-d' );
		$time = date( 'H:i:s' );
		$hour = date( 'H' );
		$json = trx_addons_fgc( TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_ADDONS . "ai-helper/shortcodes/agenerator/agenerator{$suffix}.log" );
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
		trx_addons_fpc( TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_ADDONS . "ai-helper/shortcodes/agenerator/agenerator{$suffix}.log", json_encode( $ips, JSON_PRETTY_PRINT ) );
	}
}

// Callback function to generate audio from the shortcode AJAX request
if ( ! function_exists( 'trx_addons_sc_agenerator_generate_audio' ) ) {
	add_action( 'wp_ajax_nopriv_trx_addons_ai_helper_agenerator', 'trx_addons_sc_agenerator_generate_audio' );
	add_action( 'wp_ajax_trx_addons_ai_helper_agenerator', 'trx_addons_sc_agenerator_generate_audio' );
	function trx_addons_sc_agenerator_generate_audio() {

		trx_addons_verify_nonce();

		$settings = trx_addons_decode_settings( trx_addons_get_value_gp( 'settings' ) );
		$action = trx_addons_get_value_gp( 'action_type', 'tts' );
		$model  = trx_addons_get_value_gp( 'model' );
		if ( empty( $model ) ) {
			$model = trx_addons_array_get_first( Lists::get_list_ai_audio_models( $action ) );
		}
		$prompt = $action == 'tts' ? trx_addons_get_value_gp( 'prompt' ) : '';
		$number = 1;
		$count  = (int)trx_addons_get_value_gp( 'count' );

		$base64 = ! empty( $settings['base64'] ) && (int)$settings['base64'] == 1 && Utils::is_model_support_base64( $model );

		$premium = ! empty( $settings['premium'] ) && (int)$settings['premium'] == 1;
		$suffix = $premium ? '_premium' : '';

		$answer = array(
			'error' => '',
			'data' => array(
				'audio' => array(),
				'demo' => false,
				'show_download' => ! empty( $settings['show_download'] ) ? Utils::$cache_time - 5 : 0,
				'number' => $number,
				'message' => '',
				'message_type' => 'error',
			)
		);

		if ( ! empty( $model ) && ( $action != 'tts' || ! empty( $prompt ) ) ) {

			$limits = (int)trx_addons_get_option( "ai_helper_sc_agenerator_limits{$suffix}" ) > 0;
			$lph = $lpv = $lpu = false;
			$used_limits = '';
			$generated = 0;
			$user_id = 0;

			if ( $limits ) {
				$user_level = '';
				$user_limit = false;
				if ( $premium ) {
					$user_id = get_current_user_id();
					$user_level = apply_filters( 'trx_addons_filter_sc_agenerator_user_level', $user_id > 0 ? 'default' : '', $user_id );
					if ( ! empty( $user_level ) ) {
						$levels = trx_addons_get_option( "ai_helper_sc_agenerator_levels_premium" );
						$level_idx = trx_addons_array_search( $levels, 'level', $user_level );
						$user_limit = $level_idx !== false ? $levels[ $level_idx ] : false;
						if ( isset( $user_limit['limit'] ) && trim( $user_limit['limit'] ) !== '' ) {
							$generated = trx_addons_sc_agenerator_get_total_generated( $user_limit['per'], $suffix, $user_id );
							if ( (int)$user_limit['limit'] - $generated > 0 && (int)$user_limit['limit'] - $generated < $number ) {
								$number = $answer['data']['number'] = (int)$user_limit['limit'] - $generated;
							}
							$lpu = (int)$user_limit['limit'] < $generated + $number;
							$used_limits = 'user';
						}
					}
				}
				if ( ! $premium || empty( $user_level ) || ! isset( $user_limit['limit'] ) || trim( $user_limit['limit'] ) === '' ) {
					$generated = trx_addons_sc_agenerator_get_total_generated( 'hour', $suffix );
					$lph = (int)trx_addons_get_option( "ai_helper_sc_agenerator_limit_per_hour{$suffix}" ) < $generated + $number;
					$lpv = (int)trx_addons_get_option( "ai_helper_sc_agenerator_limit_per_visitor{$suffix}" ) < $count;
					$used_limits = 'visitor';
				}
			}

			$demo = $count == 0 || $lpu || $lph || $lpv;

			$api = Utils::get_audio_api( $model );

			if ( $api->get_api_key() != '' && ! $demo ) {

				// Log a visitor ip address to the json file
				//trx_addons_sc_agenerator_log_to_json( $number, $suffix );

				$args = array(
					'model' => $model,
				);
				// Add field 'prompt'
				if ( $action == 'tts' ) {
					$args['prompt'] = $prompt;
				}
				// Add field 'upload_audio' => 'init_audio'
				if ( ! empty( $_FILES["upload_audio"]["tmp_name"] ) && ( $action != 'tts' || Utils::is_model_support_init_audio( $model ) ) ) {
					$validate = wp_check_filetype( $_FILES["upload_audio"]["name"] );
					if ( $validate['type'] == false ) {
						$answer['error'] = __( "File type is not allowed.", "trx_addons" );
					} else {
						if ( $base64 ) {
							$args['init_audio'] = base64_encode( trx_addons_fgc( $_FILES["upload_audio"]["tmp_name"] ) );
							$args['base64'] = true;
						} else {
							$ext = trx_addons_get_file_ext( $_FILES["upload_audio"]["name"] );
							if ( empty( $ext ) ) {
								$ext = 'mp3';
							}
							$args['init_audio'] = trx_addons_uploads_save_data( trx_addons_fgc( $_FILES["upload_audio"]["tmp_name"] ), array(
								'expire' => apply_filters( 'trx_addons_filter_ai_helper_uploaded_audio_expire_time', 10 * 60 ),
								'ext' => $ext,
							) );
							$args['base64'] = false;
						}
					}
				}
				// Add field 'voice'
				if ( $action == 'tts' || ( $action == 'voice-cover' && Utils::is_modelslab_model( $model ) ) ) {
					$args['voice'] = trx_addons_get_value_gp( 'voice' );
				}
				// Add field 'upload_voice' => 'target_audio'
				if ( ! empty( $_FILES["upload_voice"]["tmp_name"] ) && in_array( $action, array( 'tts', 'voice-cover' ) ) && Utils::is_modelslab_model( $model ) ) {
					$validate = wp_check_filetype( $_FILES["upload_audio"]["name"] );
					if ( $validate['type'] == false ) {
						$answer['error'] = __( "File type is not allowed.", "trx_addons" );
					} else {
						$key = $action == 'tts' ? 'init_audio' : 'target_audio';
						if ( $base64 ) {
							$args[ $key ] = base64_encode( trx_addons_fgc( $_FILES["upload_voice"]["tmp_name"] ) );
							$args['base64'] = true;
						} else {
							$ext = trx_addons_get_file_ext( $_FILES["upload_voice"]["name"] );
							if ( empty( $ext ) ) {
								$ext = 'mp3';
							}
							$args[ $key ] = trx_addons_uploads_save_data( trx_addons_fgc( $_FILES["upload_voice"]["tmp_name"] ), array(
								'expire' => apply_filters( 'trx_addons_filter_ai_helper_uploaded_audio_expire_time', 10 * 60 ),
								'ext' => $ext,
							) );
							$args['base64'] = false;
						}
					}
				}
				// Add field 'language'
				if ( in_array( $action, array( 'tts', 'transcription', 'voice-cover' ) ) && Utils::is_model_support_language( $model ) ) {
					$language = trim( trx_addons_get_value_gp( 'language' ) );
					if ( ! empty( $language ) ) {
						$args['language'] = $language;
					}
				}
				// Add field 'emotion'
				if ( in_array( $action, array( 'tts', 'transcription', 'voice-cover' ) ) && Utils::is_model_support_emotions( $model ) ) {
					$args['emotion'] = trx_addons_get_value_gp( 'emotion' );
				}
				// Add field 'speed'
				if ( $action == 'tts' && Utils::is_openai_model( $model ) ) {
					$args['speed'] = (float)trx_addons_get_value_gp( 'speed' );
				}
				// Add settings field 'temperature'
				if ( in_array( $action, array( 'translation', 'transcription' ) ) && Utils::is_openai_model( $model ) ) {
					$args['temperature'] = (float)trx_addons_get_value_gp( 'temperature' );
				}
				// Add settings fields 'rate', 'radius', 'originality' for the ModelsLab model
				if ( $action == 'voice-cover' && Utils::is_modelslab_model( $model ) ) {
					$args['rate'] = (float)trx_addons_get_value_gp( 'rate' );
					$args['radius'] = (float)trx_addons_get_value_gp( 'radius' );
					$args['originality'] = (float)trx_addons_get_value_gp( 'originality' );
				}

				// Generate audio
				if ( empty( $answer['error'] ) ) {				
					if ( $action == 'tts' ) {
						$response = $api->generate_audio( apply_filters( 'trx_addons_filter_ai_helper_generate_audio_args', $args, 'sc_agenerator' ) );
					} else if ( ! empty( $args['init_audio'] ) ) {
						if ( $action == 'transcription' ) {
							$response = $api->transcription( apply_filters( 'trx_addons_filter_ai_helper_transcription_audio_args', $args, 'sc_agenerator' ) );
						} else if ( $action == 'translation' ) {
							$response = $api->translation( apply_filters( 'trx_addons_filter_ai_helper_translation_audio_args', $args, 'sc_agenerator' ) );
						} else if ( $action == 'voice-cover' ) {
							$response = $api->voice_cover( apply_filters( 'trx_addons_filter_ai_helper_voice_cover_args', $args, 'sc_agenerator' ) );
						}
					} else {
						$answer['error'] = __( 'Error! The original audio is not uploaded.', 'trx_addons' );
					}
					$answer = Utils::parse_response( $response, $model, $answer, 'audio' );
					// Parse response
					if ( ! empty( $answer['data']['fetch_id'] ) ) {
						$answer['data']['fetch_number'] = $number;
						$answer['data']['fetch_time'] = apply_filters( 'trx_addons_filter_sc_agenerator_fetch_time', 8000 );
					}
					trx_addons_sc_agenerator_set_total_generated( $number, $suffix, $used_limits == 'user' ? $user_id : 0 );
				}
			} else {
				$answer['data']['demo'] = true;
				// Get demo audio from the settings
				if ( ! empty( $settings['demo_audio'] ) && ! empty( $settings['demo_audio'][0]['audio']['url'] ) ) {
					$audio = array();
					foreach ( $settings['demo_audio'] as $file ) {
						$audio[] = $file['audio']['url'];
					}
				}
				if ( $api->get_api_key() != '' && $demo ) {
					$msg = trx_addons_get_option( "ai_helper_sc_agenerator_limit_alert{$suffix}" );
					$answer['data']['message'] = ! empty( $msg )
													? $msg
													: apply_filters( "trx_addons_filter_sc_agenerator_limit_alert{$suffix}",
														'<h5 data-lp="' . ( $lpu ? 'lpu' . $generated : ( $lph ? 'lph' . $generated : ( $lpv ? 'lpv' : '' ) ) ) . '">' . __( 'Limits are reached!', 'trx_addons' ) . '</h5>'
														. '<p>' . __( 'The limit of the number of requests from a single visitor or the number of audio that can be generated per hour has been reached.', 'trx_addons' ) . '</p>'
														. ( is_array( $audio ) && count( $audio ) > 0 ? __( 'Therefore, instead of generated audio, you see demo samples.', 'trx_addons' ) : '' )
														. '<p>' . __( ' Please try again later.', 'trx_addons' ) . '</p>'
													);
				}
				if ( is_array( $audio ) && count( $audio ) > 0 ) {
					shuffle( $audio );
					for ( $i = 0; $i < min( $number, count( $audio ) ); $i++ ) {
						$answer['data']['audio'][] = array(
							'url' => $audio[ $i ]
						);
					}
				} else if ( $api->get_api_key() == '' )  {
					$answer['error'] = __( 'Error! API key is not specified.', 'trx_addons' );
				}
			}
		} else {
			$answer['error'] = __( 'Error! The prompt is empty.', 'trx_addons' );
		}

		// Return response to the AJAX handler
		trx_addons_ajax_response( apply_filters( 'trx_addons_filter_sc_agenerator_answer', $answer ) );
	}
}

if ( ! function_exists( 'trx_addons_sc_agenerator_fetch_audio' ) ) {
	add_action( 'wp_ajax_trx_addons_ai_helper_fetch_audio', 'trx_addons_sc_agenerator_fetch_audio' );
	add_action( 'wp_ajax_nopriv_trx_addons_ai_helper_fetch_audio', 'trx_addons_sc_agenerator_fetch_audio' );
	/**
	 * Fetch audio from the ModelsLab API
	 * 
	 * @hooked 'wp_ajax_trx_addons_ai_helper_fetch_audio'
	 * @hooked 'wp_ajax_nopriv_trx_addons_ai_helper_fetch_audio'
	 * 
	 * @param WP_REST_Request  $request  Full details about the request.
	 */
	function trx_addons_sc_agenerator_fetch_audio() {
		trx_addons_verify_nonce();

		$answer = array(
			'error' => '',
			'data' => array(
				'audio' => array()
			)
		);

		$model = trx_addons_get_value_gp( 'fetch_model', Utils::get_default_audio_model() );
		$id    = trx_addons_get_value_gp( 'fetch_id', '' );
		$url   = trx_addons_get_value_gp( 'fetch_url', '' );

		if ( ! empty( $id ) ) {
			// Check if the id is in the cache and it is the same model
			$saved_model = Utils::get_data_from_cache( $id );
			if ( $saved_model == $model ) {
				$api = ModelsLab::instance();
				$response = $api->fetch_audio( array(
					'fetch_id'  => $id,
					'fetch_url' => $url,
					// 'model'     => $model,
				) );
				$answer = Utils::parse_response( $response, $model, $answer, 'audio' );
				// Remove id from the cache if audio is fetched
				if ( count( $answer['data']['audio'] ) > 0 ) {
					Utils::delete_data_from_cache( $id );
				} else if ( ! empty( $answer['data']['fetch_id'] ) ) {
					$answer['data']['fetch_time'] = apply_filters( 'trx_addons_filter_sc_agenerator_fetch_time', 8000 );
				}
			} else {
				$answer['error'] = __( 'Error! Incorrect the queue ID for fetch audio from server.', 'trx_addons' );
			}
		} else {
			$answer['error'] = __( 'Error! Need the queue ID for fetch audio from server.', 'trx_addons' );
		}

		// Return response to the AJAX handler
		trx_addons_ajax_response( $answer );
	}
}

// Callback function to return a generated audio from the API server
if ( ! function_exists( 'trx_addons_sc_agenerator_download_audio' ) ) {
	add_action( 'init', 'trx_addons_sc_agenerator_download_audio' );
	function trx_addons_sc_agenerator_download_audio() {
		if ( trx_addons_get_value_gp( 'action' ) != 'trx_addons_ai_helper_agenerator_download' ) {
			return;
		}
		$audio = trx_addons_get_value_gp( 'audio' );
		$audio_url = Utils::get_data_from_cache( $audio );
		$audio_ext = trx_addons_get_file_ext( $audio );
		$audio_content = '';
		if ( ! empty( $audio_url ) ) {
			$audio_content = trx_addons_fgc( $audio_url );
		}
		if ( empty( $audio_content ) ) {
			header( 'HTTP/1.0 404 Not found' );
		} else {
			header( "Content-Type: audio/{$audio_ext}" );
			header( 'Content-Disposition: attachment; filename="' . $audio . '"' );
			header( 'Content-Length: ' . strlen( $audio_content ) );
			echo $audio_content;
		}
		die();
	}
}

// Add shortcodes
//----------------------------------------------------------------------------

// Add shortcodes to Elementor
if ( trx_addons_exists_elementor() && function_exists('trx_addons_elm_init') ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_ADDONS . 'ai-helper/shortcodes/agenerator/agenerator-sc-elementor.php';
}
