<?php
/**
 * Shortcode: Music Generator
 *
 * @package ThemeREX Addons
 * @since v2.30.4
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}

use TrxAddons\AiHelper\ModelsLab;
use TrxAddons\AiHelper\Utils;


// Load required styles and scripts for the frontend
if ( ! function_exists( 'trx_addons_sc_mgenerator_load_scripts_front' ) ) {
	add_action( "wp_enqueue_scripts", 'trx_addons_sc_mgenerator_load_scripts_front', TRX_ADDONS_ENQUEUE_SCRIPTS_PRIORITY );
	add_action( 'trx_addons_action_pagebuilder_preview_scripts', 'trx_addons_sc_mgenerator_load_scripts_front', 10, 1 );
	function trx_addons_sc_mgenerator_load_scripts_front( $force = false ) {
		trx_addons_enqueue_optimized( 'sc_mgenerator', $force, array(
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
				'trx_addons-sc_mgenerator' => array( 'src' => TRX_ADDONS_PLUGIN_ADDONS . 'ai-helper/shortcodes/mgenerator/mgenerator.css' ),
			),
			'js' => array(
				'trx_addons-sc_mgenerator' => array( 'src' => TRX_ADDONS_PLUGIN_ADDONS . 'ai-helper/shortcodes/mgenerator/mgenerator.js', 'deps' => 'jquery' ),
			),
			'check' => array(
				array( 'type' => 'sc',  'sc' => 'trx_sc_mgenerator' ),
				array( 'type' => 'gb',  'sc' => 'wp:trx-addons/mgenerator' ),
				array( 'type' => 'elm', 'sc' => '"widgetType":"trx_sc_mgenerator"' ),
				array( 'type' => 'elm', 'sc' => '"shortcode":"[trx_sc_mgenerator' ),
			)
		) );
	}
}

// Enqueue responsive styles for frontend
if ( ! function_exists( 'trx_addons_sc_mgenerator_load_scripts_front_responsive' ) ) {
	add_action( 'wp_enqueue_scripts', 'trx_addons_sc_mgenerator_load_scripts_front_responsive', TRX_ADDONS_ENQUEUE_RESPONSIVE_PRIORITY );
	add_action( 'trx_addons_action_load_scripts_front_sc_mgenerator', 'trx_addons_sc_mgenerator_load_scripts_front_responsive', 10, 1 );
	function trx_addons_sc_mgenerator_load_scripts_front_responsive( $force = false  ) {
		trx_addons_enqueue_optimized_responsive( 'sc_mgenerator', $force, array(
			'css'  => array(
				'trx_addons-sc_mgenerator-responsive' => array(
					'src' => TRX_ADDONS_PLUGIN_ADDONS . 'ai-helper/shortcodes/mgenerator/mgenerator.responsive.css',
					'media' => 'lg'
				),
			),
		) );
	}
}

// Add messages to the list with JS vars
if ( ! function_exists( 'trx_addons_sc_mgenerator_localize_script' ) ) {
	add_action( 'trx_addons_filter_localize_script', 'trx_addons_sc_mgenerator_localize_script' );
	function trx_addons_sc_mgenerator_localize_script( $vars ) {
		$vars['msg_ai_helper_download'] = __( 'Download', 'trx_addons' );
		$vars['msg_ai_helper_download_error'] = __( 'Error', 'trx_addons' );
		$vars['msg_ai_helper_download_expired'] = __( 'The generated music cache timed out. The download link is no longer valid.<br>But you can still download the music by right-clicking on it and selecting "Save Media As..."', 'trx_addons' );
		$vars['msg_ai_helper_mgenerator_disabled'] = __( 'Music generation is not available in edit mode!', 'trx_addons' );
		$vars['msg_ai_helper_fetch_error'] = __( 'Error updating <audio> tag on page - object is not found!', 'trx_addons' );
		return $vars;
	}
}

// Merge shortcode's specific styles to the single stylesheet
if ( ! function_exists( 'trx_addons_sc_mgenerator_merge_styles' ) ) {
	add_filter( "trx_addons_filter_merge_styles", 'trx_addons_sc_mgenerator_merge_styles' );
	function trx_addons_sc_mgenerator_merge_styles( $list ) {
		$list[ TRX_ADDONS_PLUGIN_ADDONS . 'ai-helper/shortcodes/mgenerator/mgenerator.css' ] = false;
		return $list;
	}
}

// Merge shortcode's specific styles to the single stylesheet (responsive)
if ( ! function_exists( 'trx_addons_sc_mgenerator_merge_styles_responsive' ) ) {
	add_filter("trx_addons_filter_merge_styles_responsive", 'trx_addons_sc_mgenerator_merge_styles_responsive' );
	function trx_addons_sc_mgenerator_merge_styles_responsive( $list ) {
		$list[ TRX_ADDONS_PLUGIN_ADDONS . 'ai-helper/shortcodes/mgenerator/mgenerator.responsive.css' ] = false;
		return $list;
	}
}

// Merge shortcode's specific scripts into single file
if ( ! function_exists( 'trx_addons_sc_mgenerator_merge_scripts' ) ) {
	add_action("trx_addons_filter_merge_scripts", 'trx_addons_sc_mgenerator_merge_scripts');
	function trx_addons_sc_mgenerator_merge_scripts($list) {
		$list[ TRX_ADDONS_PLUGIN_ADDONS . 'ai-helper/shortcodes/mgenerator/mgenerator.js' ] = false;
		return $list;
	}
}

// Load styles and scripts if present in the cache of the menu
if ( ! function_exists( 'trx_addons_sc_mgenerator_check_in_html_output' ) ) {
	add_filter( 'trx_addons_filter_get_menu_cache_html', 'trx_addons_sc_mgenerator_check_in_html_output', 10, 1 );
	add_action( 'trx_addons_action_show_layout_from_cache', 'trx_addons_sc_mgenerator_check_in_html_output', 10, 1 );
	add_action( 'trx_addons_action_check_page_content', 'trx_addons_sc_mgenerator_check_in_html_output', 10, 1 );
	function trx_addons_sc_mgenerator_check_in_html_output( $content = '' ) {
		$args = array(
			'check' => array(
				'class=[\'"][^\'"]*sc_mgenerator'
			)
		);
		if ( trx_addons_check_in_html_output( 'sc_mgenerator', $content, $args ) ) {
			trx_addons_sc_mgenerator_load_scripts_front( true );
		}
		return $content;
	}
}

// Replace 'ai_helper_token_modelslab' with 'ai_helper_token_stable_diffusion' in the option name
if ( ! function_exists( 'trx_addons_sc_mgenerator_replace_option_name' ) ) {
	add_filter( "trx_addons_filter_get_option_name", 'trx_addons_sc_mgenerator_replace_option_name' );
	function trx_addons_sc_mgenerator_replace_option_name( $name ) {
		return $name == 'ai_helper_token_modelslab' ? 'ai_helper_token_stable_diffusion' : $name;
	}
}


// trx_sc_mgenerator
//-------------------------------------------------------------
/*
[trx_sc_mgenerator id="unique_id" number="2" prompt="prompt text for ai"]
*/
if ( ! function_exists( 'trx_addons_sc_mgenerator' ) ) {
	function trx_addons_sc_mgenerator( $atts, $content = '' ) {	
		$atts = trx_addons_sc_prepare_atts( 'trx_sc_mgenerator', $atts, trx_addons_sc_common_atts( 'trx_sc_mgenerator', 'id,title', array(
			// Individual params
			"type" => "default",
			"tags" => "",
			"tags_label" => "",
			"prompt" => "",
			"system_prompt" => "",
			"placeholder_text" => "",
			"prompt_width" => "100",
			"show_download" => 0,
			"show_prompt_translated" => 1,
			"show_upload_audio" => 1,
			"base64" => 0,
			"button_text" => "",
			"premium" => 0,
			"show_settings" => 0,
			"show_limits" => 0,
			"sampling_rate" => 32000,
			"duration" => 5,
			"align" => "",
			"align_tablet" => "",
			"align_mobile" => "",
			"demo_music" => "",
		) ) );

		// Load shortcode-specific scripts and styles
		trx_addons_sc_mgenerator_load_scripts_front( true );

		// Load template
		$output = '';
		if ( ! empty( $atts['demo_music'] ) && ! is_array( $atts['demo_music'] ) ) {
			$demo_music = explode( '|', $atts['demo_music'] );
			$atts['demo_music'] = array();
			foreach ( $demo_music as $url ) {
				$atts['demo_music'][] = array( 'music' => array( 'url' => $url ) );
			}
		}

		ob_start();
		if ( ! Utils::is_music_api_available() ) {
			trx_addons_get_template_part( 'templates/tpl.sc_placeholder.php',
				'trx_addons_args_sc_placeholder',
				apply_filters( 'trx_addons_filter_sc_placeholder_args', array(
					'sc' => 'trx_sc_mgenerator',
					'title' => __('AI Music Generator is not available - token for access to the API for music generation is not specified', 'trx_addons'),
					'class' => 'sc_placeholder_with_title'
					) )
			);
		} else {
			trx_addons_get_template_part( array(
										TRX_ADDONS_PLUGIN_ADDONS . 'ai-helper/shortcodes/mgenerator/tpl.' . trx_addons_esc( trx_addons_sanitize_file_name( $atts['type'] ) ) . '.php',
										TRX_ADDONS_PLUGIN_ADDONS . 'ai-helper/shortcodes/mgenerator/tpl.default.php'
										),
										'trx_addons_args_sc_mgenerator',
										$atts
									);
		}
		$output = ob_get_contents();
		ob_end_clean();
		return apply_filters( 'trx_addons_sc_output', $output, 'trx_sc_mgenerator', $atts, $content );
	}
}

// Add shortcode [trx_sc_mgenerator]
if ( ! function_exists( 'trx_addons_sc_mgenerator_add_shortcode' ) ) {
	add_action( 'init', 'trx_addons_sc_mgenerator_add_shortcode', 20 );
	function trx_addons_sc_mgenerator_add_shortcode() {
		add_shortcode( "trx_sc_mgenerator", "trx_addons_sc_mgenerator" );
	}
}

// Prepare a data for generated music
if ( ! function_exists( 'trx_addons_sc_mgenerator_prepare_total_generated' ) ) {
	function trx_addons_sc_mgenerator_prepare_total_generated( $data ) {
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

// Add number of generated music to the total number
if ( ! function_exists( 'trx_addons_sc_mgenerator_set_total_generated' ) ) {
	function trx_addons_sc_mgenerator_set_total_generated( $number = 1, $suffix = '', $user_id = 0 ) {
		$data = trx_addons_sc_mgenerator_prepare_total_generated( $user_id > 0 && ! empty( $suffix )
					? get_user_meta( $user_id, 'trx_addons_sc_mgenerator_total', true )
					: get_transient( "trx_addons_sc_mgenerator_total{$suffix}" )
				);
		$hour = (int) date( 'H' );
		$data['per_hour'][ $hour ] += $number;
		$data['per_day'] += $number;
		$data['per_week'] += $number;
		$data['per_month'] += $number;
		$data['per_year'] += $number;
		if ( $user_id > 0 ) {
			update_user_meta( $user_id, 'trx_addons_sc_mgenerator_total', $data );
		} else {
			set_transient( "trx_addons_sc_mgenerator_total{$suffix}", $data, 24 * 60 * 60 );
		}
	}
}

// Get number of generated music
if ( ! function_exists( 'trx_addons_sc_mgenerator_get_total_generated' ) ) {
	function trx_addons_sc_mgenerator_get_total_generated( $per = 'hour', $suffix = '', $user_id = 0 ) {
		$data = trx_addons_sc_mgenerator_prepare_total_generated( $user_id > 0 && ! empty( $suffix )
					? get_user_meta( $user_id, 'trx_addons_sc_mgenerator_total', true )
					: get_transient( "trx_addons_sc_mgenerator_total{$suffix}" )
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
if ( ! function_exists( 'trx_addons_sc_mgenerator_log_to_json' ) ) {
	function trx_addons_sc_mgenerator_log_to_json( $number, $suffix = '' ) {
		$ip = ! empty( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : 'Unknown';
		$date = date( 'Y-m-d' );
		$time = date( 'H:i:s' );
		$hour = date( 'H' );
		$json = trx_addons_fgc( TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_ADDONS . "ai-helper/shortcodes/mgenerator/mgenerator{$suffix}.log" );
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
		trx_addons_fpc( TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_ADDONS . "ai-helper/shortcodes/mgenerator/mgenerator{$suffix}.log", json_encode( $ips, JSON_PRETTY_PRINT ) );
	}
}

// Callback function to generate music from the shortcode AJAX request
if ( ! function_exists( 'trx_addons_sc_mgenerator_generate_music' ) ) {
	add_action( 'wp_ajax_nopriv_trx_addons_ai_helper_mgenerator', 'trx_addons_sc_mgenerator_generate_music' );
	add_action( 'wp_ajax_trx_addons_ai_helper_mgenerator', 'trx_addons_sc_mgenerator_generate_music' );
	function trx_addons_sc_mgenerator_generate_music() {

		trx_addons_verify_nonce();

		$settings = trx_addons_decode_settings( trx_addons_get_value_gp( 'settings' ) );

		$model = trx_addons_get_value_gp( 'model' );
		if ( empty( $model ) ) {
			$model = ! empty( $settings['model'] ) ? $settings['model'] : Utils::get_default_music_model();
		}
		$prompt = trx_addons_get_value_gp( 'prompt' );
		if ( empty( $prompt ) ) {
			$prompt = __( 'Generate the music a-la disco 80s.', 'trx_addons' );
		}

		$premium = ! empty( $settings['premium'] ) && (int)$settings['premium'] == 1;
		$suffix = $premium ? '_premium' : '';

		$count = (int)trx_addons_get_value_gp( 'count' );

		$system_prompt = ! empty( $settings['system_prompt'] )
							? trim( $settings['system_prompt'] )
							: apply_filters( 'trx_addons_filter_sc_mgenerator_system_prompt', '' );
		if ( ! empty( $system_prompt ) ) {
			$prompt = $prompt . trx_addons_strdot( $prompt ) . ' ' . $system_prompt;
		}

		$base64 = ! empty( $settings['base64'] ) && (int)$settings['base64'] == 1;

		$answer = array(
			'error' => '',
			'data' => array(
				'music' => array(),
				'demo' => false,
				'show_download' => ! empty( $settings['show_download'] ) ? Utils::$cache_time - 5 : 0,
				'message' => '',
				'message_type' => 'error',
			)
		);

		if ( ! empty( $prompt ) ) {

			$limits = (int)trx_addons_get_option( "ai_helper_sc_mgenerator_limits{$suffix}" ) > 0;
			$lph = $lpv = $lpu = false;
			$used_limits = '';
			$generated = 0;
			$user_id = 0;
			$number = 1;

			if ( $limits ) {
				$user_level = '';
				$user_limit = false;
				if ( $premium ) {
					$user_id = get_current_user_id();
					$user_level = apply_filters( 'trx_addons_filter_sc_mgenerator_user_level', $user_id > 0 ? 'default' : '', $user_id );
					if ( ! empty( $user_level ) ) {
						$levels = trx_addons_get_option( "ai_helper_sc_mgenerator_levels_premium" );
						$level_idx = trx_addons_array_search( $levels, 'level', $user_level );
						$user_limit = $level_idx !== false ? $levels[ $level_idx ] : false;
						if ( isset( $user_limit['limit'] ) && trim( $user_limit['limit'] ) !== '' ) {
							$generated = trx_addons_sc_mgenerator_get_total_generated( $user_limit['per'], $suffix, $user_id );
							$lpu = (int)$user_limit['limit'] < $generated + $number;
							$used_limits = 'user';
						}
					}
				}
				if ( ! $premium || empty( $user_level ) || ! isset( $user_limit['limit'] ) || trim( $user_limit['limit'] ) === '' ) {
					$generated = trx_addons_sc_mgenerator_get_total_generated( 'hour', $suffix );
					$lph = (int)trx_addons_get_option( "ai_helper_sc_mgenerator_limit_per_hour{$suffix}" ) < $generated + $number;
					$lpv = (int)trx_addons_get_option( "ai_helper_sc_mgenerator_limit_per_visitor{$suffix}" ) < $count;
					$used_limits = 'visitor';
				}
			}

			$demo = $count == 0 || $lpu || $lph || $lpv;

			$api = Utils::get_music_api( $model );

			if ( $api->get_api_key() != '' && ! $demo ) {

				// Log a visitor ip address to the json file
				//trx_addons_sc_mgenerator_log_to_json( $number, $suffix );

				$args = array(
					// 'model' => $model,
					'prompt' => apply_filters( 'trx_addons_filter_ai_helper_prompt', $prompt, compact( 'model' ), 'sc_mgenerator' ),
					'temp' => true
				);
				$rate = trx_addons_get_value_gp( 'sampling_rate', ! empty( $settings['sampling_rate'] ) ? $settings['sampling_rate'] : 32000 );
				if ( ! empty( $rate ) ) {
					$args['sampling_rate'] = (int)$rate;
				}
				$duration = trx_addons_get_value_gp( 'duration', ! empty( $settings['duration'] ) ? $settings['duration'] : 5 );
				$args['max_new_token'] = max( 256, min( 1024, round( (float)$duration / 5 * 256 ) ) );
				if ( ! empty( $_FILES["upload_audio"]["tmp_name"] ) ) {
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
								$ext = 'wav';
							}
							$args['init_audio'] = trx_addons_uploads_save_data( trx_addons_fgc( $_FILES["upload_audio"]["tmp_name"] ), array(
								'expire' => apply_filters( 'trx_addons_filter_ai_helper_uploaded_audio_expire_time', 10 * 60 ),
								'ext' => $ext,
							) );
							$args['base64'] = false;
						}
					}
				}

				$translated = $prompt != $args['prompt'];
				if ( $translated && ! empty( $settings['show_prompt_translated'] ) ) {
					$answer['data']['message_type'] = 'info';
					$answer['data']['message'] = apply_filters( 'trx_addons_filter_sc_mgenerator_translated_message',
																'<p>'
																	. str_replace( '\n', '<br>', sprintf(
																				__( 'Your prompt was automatically translated into English:\n%s', 'trx_addons' ),
																				'<a href="#" class="sc_mgenerator_message_translation" title="' . esc_attr__( 'Click to use as a prompt', 'trx_addons' ) . '" data-tag-prompt="' . esc_attr( $args['prompt'] ) . '">' . $args['prompt'] . '</a>'
																			)
																		)
																. '</p>'
															);
				}
				// Generate music
				if ( empty( $answer['error'] ) ) {
					$response = $api->generate_music( apply_filters( 'trx_addons_filter_ai_helper_generate_music_args', $args, 'sc_mgenerator' ) );
					// Parse response
					$answer = Utils::parse_response( $response, $model, $answer, 'music' );
					if ( ! empty( $answer['data']['fetch_id'] ) ) {
						$answer['data']['fetch_number'] = $number;
						$answer['data']['fetch_time'] = apply_filters( 'trx_addons_filter_sc_mgenerator_fetch_time', 8000 );
					}
					trx_addons_sc_mgenerator_set_total_generated( $number, $suffix, $used_limits == 'user' ? $user_id : 0 );
				}
			} else {
				$answer['data']['demo'] = true;
				// Get demo music from the settings
				if ( ! empty( $settings['demo_music'] ) && ! empty( $settings['demo_music'][0]['music']['url'] ) ) {
					$music = array();
					foreach ( $settings['demo_music'] as $file ) {
						$music[] = $file['music']['url'];
					}
				}
				if ( $api->get_api_key() != '' && $demo ) {
					$msg = trx_addons_get_option( "ai_helper_sc_mgenerator_limit_alert{$suffix}" );
					$answer['data']['message'] = ! empty( $msg )
													? $msg
													: apply_filters( "trx_addons_filter_sc_mgenerator_limit_alert{$suffix}",
														'<h5 data-lp="' . ( $lpu ? 'lpu' . $generated : ( $lph ? 'lph' . $generated : ( $lpv ? 'lpv' : '' ) ) ) . '">' . __( 'Limits are reached!', 'trx_addons' ) . '</h5>'
														. '<p>' . __( 'The limit of the number of requests from a single visitor or the number of music that can be generated per hour has been reached.', 'trx_addons' ) . '</p>'
														. ( is_array( $music ) && count( $music ) > 0 ? __( 'Therefore, instead of generated music, you see demo sample.', 'trx_addons' ) : '' )
														. '<p>' . __( ' Please try again later.', 'trx_addons' ) . '</p>'
													);
				}
				if ( is_array( $music ) && count( $music ) > 0 ) {
					shuffle( $music );
					for ( $i = 0; $i < min( $number, count( $music ) ); $i++ ) {
						$answer['data']['music'][] = array(
							'url' => $music[ $i ]
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
		trx_addons_ajax_response( apply_filters( 'trx_addons_filter_sc_mgenerator_answer', $answer ) );
	}
}

if ( ! function_exists( 'trx_addons_sc_mgenerator_fetch_music' ) ) {
	add_action( 'wp_ajax_trx_addons_ai_helper_fetch_music', 'trx_addons_sc_mgenerator_fetch_music' );
	add_action( 'wp_ajax_nopriv_trx_addons_ai_helper_fetch_music', 'trx_addons_sc_mgenerator_fetch_music' );
	/**
	 * Fetch music from the ModelsLab API
	 * 
	 * @hooked 'wp_ajax_trx_addons_ai_helper_fetch_music'
	 * @hooked 'wp_ajax_nopriv_trx_addons_ai_helper_fetch_music'
	 * 
	 * @param WP_REST_Request  $request  Full details about the request.
	 */
	function trx_addons_sc_mgenerator_fetch_music() {
		trx_addons_verify_nonce();

		$answer = array(
			'error' => '',
			'data' => array(
				'music' => array()
			)
		);

		$model = trx_addons_get_value_gp( 'fetch_model', Utils::get_default_music_model() );
		$id    = trx_addons_get_value_gp( 'fetch_id', '' );
		$url   = trx_addons_get_value_gp( 'fetch_url', '' );

		if ( ! empty( $id ) ) {
			// Check if the id is in the cache and it is the same model
			$saved_model = Utils::get_data_from_cache( $id );
			if ( $saved_model == $model ) {
				$api = ModelsLab::instance();
				$response = $api->fetch_music( array(
					'fetch_id'  => $id,
					'fetch_url' => $url,
					// 'model'     => $model,
				) );
				$answer = Utils::parse_response( $response, $model, $answer, 'music' );
				// Remove id from the cache if music is fetched
				if ( count( $answer['data']['music'] ) > 0 ) {
					Utils::delete_data_from_cache( $id );
				} else if ( ! empty( $answer['data']['fetch_id'] ) ) {
					$answer['data']['fetch_time'] = apply_filters( 'trx_addons_filter_sc_mgenerator_fetch_time', 8000 );
				}
			} else {
				$answer['error'] = __( 'Error! Incorrect the queue ID for fetch music from server.', 'trx_addons' );
			}
		} else {
			$answer['error'] = __( 'Error! Need the queue ID for fetch music from server.', 'trx_addons' );
		}

		// Return response to the AJAX handler
		trx_addons_ajax_response( $answer );
	}
}

// Callback function to return a generated music from the API server
if ( ! function_exists( 'trx_addons_sc_mgenerator_download_music' ) ) {
	add_action( 'init', 'trx_addons_sc_mgenerator_download_music' );
	function trx_addons_sc_mgenerator_download_music() {
		if ( trx_addons_get_value_gp( 'action' ) != 'trx_addons_ai_helper_mgenerator_download' ) {
			return;
		}
		$music = trx_addons_get_value_gp( 'music' );
		$music_url = Utils::get_data_from_cache( $music );
		$music_ext = trx_addons_get_file_ext( $music );
		$music_content = '';
		if ( ! empty( $music_url ) ) {
			$music_content = trx_addons_fgc( $music_url );
		}
		if ( empty( $music_content ) ) {
			header( 'HTTP/1.0 404 Not found' );
		} else {
			header( "Content-Type: audio/{$music_ext}" );
			header( 'Content-Disposition: attachment; filename="' . $music . '"' );
			header( 'Content-Length: ' . strlen( $music_content ) );
			echo $music_content;
		}
		die();
	}
}

// Translate the prompt if it contains non-English characters
if ( ! function_exists( 'trx_addons_sc_mgenerator_translate_prompt' ) ) {
	add_filter( 'trx_addons_filter_ai_helper_prompt', 'trx_addons_sc_mgenerator_translate_prompt', 10, 3 );
	function trx_addons_sc_mgenerator_translate_prompt( $prompt, $args, $from = '' ) {
		// Translate only if this filter was called from the shortcode [trx_sc_mgenerator] or from the Media Library
		// and only if the option 'Translate prompt' is enabled
		// and only if the prompt contains non-English characters
		if ( in_array( $from, array( 'sc_mgenerator', 'media_library_generate_music' ) )
			&& (int)trx_addons_get_option( 'ai_helper_sc_mgenerator_translate_prompt' ) == 1
			&& trx_addons_str_is_not_english( $prompt )
		) {
			$model = ! empty( $args['model'] ) ? $args['model'] : Utils::get_default_music_model();
			if ( apply_filters( 'trx_addons_filter_sc_mgenerator_auto_translate_prompt', true, $model ) ) {
				$prompt = Utils::translate( $prompt );
			}
		}
		return $prompt;
	}
}

// Add shortcodes
//----------------------------------------------------------------------------

// Add shortcodes to Elementor
if ( trx_addons_exists_elementor() && function_exists('trx_addons_elm_init') ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_ADDONS . 'ai-helper/shortcodes/mgenerator/mgenerator-sc-elementor.php';
}
