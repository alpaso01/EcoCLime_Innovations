<?php
/**
 * Shortcode: Video Generator
 *
 * @package ThemeREX Addons
 * @since v2.20.2
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}

use TrxAddons\AiHelper\LumaLabs;
use TrxAddons\AiHelper\Utils;
use TrxAddons\AiHelper\Lists;


// Load required styles and scripts for the frontend
if ( ! function_exists( 'trx_addons_sc_vgenerator_load_scripts_front' ) ) {
	add_action( "wp_enqueue_scripts", 'trx_addons_sc_vgenerator_load_scripts_front', TRX_ADDONS_ENQUEUE_SCRIPTS_PRIORITY );
	add_action( 'trx_addons_action_pagebuilder_preview_scripts', 'trx_addons_sc_vgenerator_load_scripts_front', 10, 1 );
	function trx_addons_sc_vgenerator_load_scripts_front( $force = false ) {
		trx_addons_enqueue_optimized( 'sc_vgenerator', $force, array(
			'css'  => array(
				'trx_addons-sc_vgenerator' => array( 'src' => TRX_ADDONS_PLUGIN_ADDONS . 'ai-helper/shortcodes/vgenerator/vgenerator.css' ),
			),
			'js' => array(
				'trx_addons-sc_vgenerator' => array( 'src' => TRX_ADDONS_PLUGIN_ADDONS . 'ai-helper/shortcodes/vgenerator/vgenerator.js', 'deps' => 'jquery' ),
			),
			'check' => array(
				array( 'type' => 'sc',  'sc' => 'trx_sc_vgenerator' ),
				array( 'type' => 'gb',  'sc' => 'wp:trx-addons/vgenerator' ),
				array( 'type' => 'elm', 'sc' => '"widgetType":"trx_sc_vgenerator"' ),
				array( 'type' => 'elm', 'sc' => '"shortcode":"[trx_sc_vgenerator' ),
			)
		) );
	}
}

// Enqueue responsive styles for frontend
if ( ! function_exists( 'trx_addons_sc_vgenerator_load_scripts_front_responsive' ) ) {
	add_action( 'wp_enqueue_scripts', 'trx_addons_sc_vgenerator_load_scripts_front_responsive', TRX_ADDONS_ENQUEUE_RESPONSIVE_PRIORITY );
	add_action( 'trx_addons_action_load_scripts_front_sc_vgenerator', 'trx_addons_sc_vgenerator_load_scripts_front_responsive', 10, 1 );
	function trx_addons_sc_vgenerator_load_scripts_front_responsive( $force = false  ) {
		trx_addons_enqueue_optimized_responsive( 'sc_vgenerator', $force, array(
			'css'  => array(
				'trx_addons-sc_vgenerator-responsive' => array(
					'src' => TRX_ADDONS_PLUGIN_ADDONS . 'ai-helper/shortcodes/vgenerator/vgenerator.responsive.css',
					'media' => 'lg'
				),
			),
		) );
	}
}

// Add messages to the list with JS vars
if ( ! function_exists( 'trx_addons_sc_vgenerator_localize_script' ) ) {
	add_action( 'trx_addons_filter_localize_script', 'trx_addons_sc_vgenerator_localize_script' );
	function trx_addons_sc_vgenerator_localize_script( $vars ) {
		$vars['msg_ai_helper_download'] = __( 'Download', 'trx_addons' );
		$vars['msg_ai_helper_download_error'] = __( 'Error', 'trx_addons' );
		$vars['msg_ai_helper_download_expired'] = __( 'The generated video cache timed out. The download link is no longer valid.<br>But you can still download the video by right-clicking on it and selecting "Save Video As..."', 'trx_addons' );
		$vars['msg_ai_helper_vgenerator_disabled'] = __( 'Video generation is not available in edit mode!', 'trx_addons' );
		$vars['msg_ai_helper_wait_available'] = __( 'Wait for the video to become available on the rendering server', 'trx_addons' );
		return $vars;
	}
}

// Merge shortcode's specific styles to the single stylesheet
if ( ! function_exists( 'trx_addons_sc_vgenerator_merge_styles' ) ) {
	add_filter( "trx_addons_filter_merge_styles", 'trx_addons_sc_vgenerator_merge_styles' );
	function trx_addons_sc_vgenerator_merge_styles( $list ) {
		$list[ TRX_ADDONS_PLUGIN_ADDONS . 'ai-helper/shortcodes/vgenerator/vgenerator.css' ] = false;
		return $list;
	}
}

// Merge shortcode's specific styles to the single stylesheet (responsive)
if ( ! function_exists( 'trx_addons_sc_vgenerator_merge_styles_responsive' ) ) {
	add_filter("trx_addons_filter_merge_styles_responsive", 'trx_addons_sc_vgenerator_merge_styles_responsive' );
	function trx_addons_sc_vgenerator_merge_styles_responsive( $list ) {
		$list[ TRX_ADDONS_PLUGIN_ADDONS . 'ai-helper/shortcodes/vgenerator/vgenerator.responsive.css' ] = false;
		return $list;
	}
}

// Merge shortcode's specific scripts into single file
if ( ! function_exists( 'trx_addons_sc_vgenerator_merge_scripts' ) ) {
	add_action("trx_addons_filter_merge_scripts", 'trx_addons_sc_vgenerator_merge_scripts');
	function trx_addons_sc_vgenerator_merge_scripts($list) {
		$list[ TRX_ADDONS_PLUGIN_ADDONS . 'ai-helper/shortcodes/vgenerator/vgenerator.js' ] = false;
		return $list;
	}
}

// Load styles and scripts if present in the cache of the menu
if ( ! function_exists( 'trx_addons_sc_vgenerator_check_in_html_output' ) ) {
	add_filter( 'trx_addons_filter_get_menu_cache_html', 'trx_addons_sc_vgenerator_check_in_html_output', 10, 1 );
	add_action( 'trx_addons_action_show_layout_from_cache', 'trx_addons_sc_vgenerator_check_in_html_output', 10, 1 );
	add_action( 'trx_addons_action_check_page_content', 'trx_addons_sc_vgenerator_check_in_html_output', 10, 1 );
	function trx_addons_sc_vgenerator_check_in_html_output( $content = '' ) {
		$args = array(
			'check' => array(
				'class=[\'"][^\'"]*sc_vgenerator'
			)
		);
		if ( trx_addons_check_in_html_output( 'sc_vgenerator', $content, $args ) ) {
			trx_addons_sc_vgenerator_load_scripts_front( true );
		}
		return $content;
	}
}


// trx_sc_vgenerator
//-------------------------------------------------------------
/*
[trx_sc_vgenerator id="unique_id" number="2" prompt="prompt text for ai"]
*/
if ( ! function_exists( 'trx_addons_sc_vgenerator' ) ) {
	function trx_addons_sc_vgenerator( $atts, $content = '' ) {	
		$atts = trx_addons_sc_prepare_atts( 'trx_sc_vgenerator', $atts, trx_addons_sc_common_atts( 'trx_sc_vgenerator', 'id,title', array(
			// Individual params
			"type" => "default",
			"tags" => "",
			"tags_label" => "",
			"prompt" => "",
			"system_prompt" => "",
			"placeholder_text" => "",
			"prompt_width" => "100",
			"button_text" => "",
			"aspect_ratio" => "1:1",
			"model" => "",
			"show_settings" => 0,
			"show_limits" => 0,
			"show_download" => 0,
			"show_prompt_translated" => 1,
			"align" => "",
			"align_tablet" => "",
			"align_mobile" => "",
			"premium" => 0,
			"show_upload_frame0" => "",
			// "keyframes_frame0" => "",
			"show_upload_frame1" => "",
			// "keyframes_frame1" => "",
			"resolution" => "540p",
			"duration" => "5s",
			"allow_loop" => "",
			"demo_video" => ""
		) ) );

		// Load shortcode-specific scripts and styles
		trx_addons_sc_vgenerator_load_scripts_front( true );

		// Load template
		$output = '';
		if ( ! empty( $atts['demo_video'] ) && ! is_array( $atts['demo_video'] ) ) {
			$demo_video = explode( '|', $atts['demo_video'] );
			$atts['demo_video'] = array();
			foreach ( $demo_video as $url ) {
				$atts['demo_video'][] = array( 'video' => array( 'url' => $url ) );
			}
		}

		ob_start();
		if ( ! Utils::is_video_api_available() && empty( $atts['demo_video'] ) ) {
			trx_addons_get_template_part( 'templates/tpl.sc_placeholder.php',
				'trx_addons_args_sc_placeholder',
				apply_filters( 'trx_addons_filter_sc_placeholder_args', array(
					'sc' => 'trx_sc_mgenerator',
					'title' => __('AI Video Generator is not available - token for access to the API for video generation is not specified', 'trx_addons'),
					'class' => 'sc_placeholder_with_title'
					) )
			);
		} else {
			trx_addons_get_template_part( array(
										TRX_ADDONS_PLUGIN_ADDONS . 'ai-helper/shortcodes/vgenerator/tpl.' . trx_addons_esc( trx_addons_sanitize_file_name( $atts['type'] ) ) . '.php',
										TRX_ADDONS_PLUGIN_ADDONS . 'ai-helper/shortcodes/vgenerator/tpl.default.php'
										),
										'trx_addons_args_sc_vgenerator',
										$atts
									);
		}
		$output = ob_get_contents();
		ob_end_clean();
		return apply_filters( 'trx_addons_sc_output', $output, 'trx_sc_vgenerator', $atts, $content );
	}
}

// Add shortcode [trx_sc_vgenerator]
if ( ! function_exists( 'trx_addons_sc_vgenerator_add_shortcode' ) ) {
	add_action( 'init', 'trx_addons_sc_vgenerator_add_shortcode', 20 );
	function trx_addons_sc_vgenerator_add_shortcode() {
		add_shortcode( "trx_sc_vgenerator", "trx_addons_sc_vgenerator" );
	}
}

// Prepare a data for generated video
if ( ! function_exists( 'trx_addons_sc_vgenerator_prepare_total_generated' ) ) {
	function trx_addons_sc_vgenerator_prepare_total_generated( $data ) {
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

// Add number of generated video to the total number
if ( ! function_exists( 'trx_addons_sc_vgenerator_set_total_generated' ) ) {
	function trx_addons_sc_vgenerator_set_total_generated( $number = 1, $suffix = '', $user_id = 0 ) {
		$data = trx_addons_sc_vgenerator_prepare_total_generated( $user_id > 0 && ! empty( $suffix )
					? get_user_meta( $user_id, 'trx_addons_sc_vgenerator_total', true )
					: get_transient( "trx_addons_sc_vgenerator_total{$suffix}" )
				);
		$hour = (int) date( 'H' );
		$data['per_hour'][ $hour ] += $number;
		$data['per_day'] += $number;
		$data['per_week'] += $number;
		$data['per_month'] += $number;
		$data['per_year'] += $number;
		if ( $user_id > 0 ) {
			update_user_meta( $user_id, 'trx_addons_sc_vgenerator_total', $data );
		} else {
			set_transient( "trx_addons_sc_vgenerator_total{$suffix}", $data, 24 * 60 * 60 );
		}
	}
}

// Get number of generated video
if ( ! function_exists( 'trx_addons_sc_vgenerator_get_total_generated' ) ) {
	function trx_addons_sc_vgenerator_get_total_generated( $per = 'hour', $suffix = '', $user_id = 0 ) {
		$data = trx_addons_sc_vgenerator_prepare_total_generated( $user_id > 0 && ! empty( $suffix )
					? get_user_meta( $user_id, 'trx_addons_sc_vgenerator_total', true )
					: get_transient( "trx_addons_sc_vgenerator_total{$suffix}" )
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
if ( ! function_exists( 'trx_addons_sc_vgenerator_log_to_json' ) ) {
	function trx_addons_sc_vgenerator_log_to_json( $number, $suffix = '' ) {
		$ip = ! empty( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : 'Unknown';
		$date = date( 'Y-m-d' );
		$time = date( 'H:i:s' );
		$hour = date( 'H' );
		$json = trx_addons_fgc( TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_ADDONS . "ai-helper/shortcodes/vgenerator/vgenerator{$suffix}.log" );
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
		trx_addons_fpc( TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_ADDONS . "ai-helper/shortcodes/vgenerator/vgenerator{$suffix}.log", json_encode( $ips, JSON_PRETTY_PRINT ) );
	}
}

// Callback function to generate video from the shortcode AJAX request
if ( ! function_exists( 'trx_addons_sc_vgenerator_generate_video' ) ) {
	add_action( 'wp_ajax_nopriv_trx_addons_ai_helper_vgenerator', 'trx_addons_sc_vgenerator_generate_video' );
	add_action( 'wp_ajax_trx_addons_ai_helper_vgenerator', 'trx_addons_sc_vgenerator_generate_video' );
	function trx_addons_sc_vgenerator_generate_video() {

		trx_addons_verify_nonce();

		$settings = trx_addons_decode_settings( trx_addons_get_value_gp( 'settings' ) );

		$model = trx_addons_get_value_gp( 'model' );
		if ( empty( $model ) ) {
			$model = ! empty( $settings['model'] ) ? $settings['model'] : Utils::get_default_video_model();
		}
		$prompt = trx_addons_get_value_gp( 'prompt' );
		if ( empty( $prompt ) ) {
			$prompt = __( 'Generate the video a-la disco 80s.', 'trx_addons' );
		}

		$premium = ! empty( $settings['premium'] ) && (int)$settings['premium'] == 1;
		$suffix = $premium ? '_premium' : '';

		$count = (int)trx_addons_get_value_gp( 'count' );

		$system_prompt = ! empty( $settings['system_prompt'] )
							? trim( $settings['system_prompt'] )
							: apply_filters( 'trx_addons_filter_sc_vgenerator_system_prompt', '' );
		if ( ! empty( $system_prompt ) ) {
			$prompt = $prompt . trx_addons_strdot( $prompt ) . ' ' . $system_prompt;
		}

		$answer = array(
			'error' => '',
			'data' => array(
				'video' => array(),
				'demo' => false,
				'show_download' => ! empty( $settings['show_download'] ) ? Utils::$cache_time - 5 : 0,
				'message' => '',
				'message_type' => 'error',
			)
		);

		if ( ! empty( $prompt ) ) {

			$limits = (int)trx_addons_get_option( "ai_helper_sc_vgenerator_limits{$suffix}" ) > 0;
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
					$user_level = apply_filters( 'trx_addons_filter_sc_vgenerator_user_level', $user_id > 0 ? 'default' : '', $user_id );
					if ( ! empty( $user_level ) ) {
						$levels = trx_addons_get_option( "ai_helper_sc_vgenerator_levels_premium" );
						$level_idx = trx_addons_array_search( $levels, 'level', $user_level );
						$user_limit = $level_idx !== false ? $levels[ $level_idx ] : false;
						if ( isset( $user_limit['limit'] ) && trim( $user_limit['limit'] ) !== '' ) {
							$generated = trx_addons_sc_vgenerator_get_total_generated( $user_limit['per'], $suffix, $user_id );
							$lpu = (int)$user_limit['limit'] < $generated + $number;
							$used_limits = 'user';
						}
					}
				}
				if ( ! $premium || empty( $user_level ) || ! isset( $user_limit['limit'] ) || trim( $user_limit['limit'] ) === '' ) {
					$generated = trx_addons_sc_vgenerator_get_total_generated( 'hour', $suffix );
					$lph = (int)trx_addons_get_option( "ai_helper_sc_vgenerator_limit_per_hour{$suffix}" ) < $generated + $number;
					$lpv = (int)trx_addons_get_option( "ai_helper_sc_vgenerator_limit_per_visitor{$suffix}" ) < $count;
					$used_limits = 'visitor';
				}
			}

			$demo = $count == 0 || $lpu || $lph || $lpv;
$demo = true;

			$api = Utils::get_video_api( $model );

			if ( $api->get_api_key() != '' && ! $demo ) {

				// Log a visitor ip address to the json file
				//trx_addons_sc_vgenerator_log_to_json( $number, $suffix );

				$aspect_ratio = trx_addons_get_value_gp( 'aspect_ratio', '' );
				if ( empty( $aspect_ratio ) ) {
					$aspect_ratio = $settings['aspect_ratio'];
				}

				$resolution = trx_addons_get_value_gp( 'resolution', '' );
				if ( empty( $resolution ) ) {
					$resolution = $settings['resolution'];
				}

				$duration = trx_addons_get_value_gp( 'duration', '' );
				if ( empty( $duration ) ) {
					$duration = $settings['duration'];
				}

				$args = array(
					'model' => $model,
					'loop' => ! empty( $settings['allow_loop'] ) ? true : false,
					'prompt' => apply_filters( 'trx_addons_filter_ai_helper_prompt', $prompt, compact( 'model' ), 'sc_vgenerator' ),
				);

				$translated = $prompt != $args['prompt'];
				if ( $translated && ! empty( $settings['show_prompt_translated'] ) ) {
					$answer['data']['message_type'] = 'info';
					$answer['data']['message'] = apply_filters( 'trx_addons_filter_sc_vgenerator_translated_message',
																'<p>'
																	. str_replace( '\n', '<br>', sprintf(
																		__( 'Your prompt was automatically translated into English:\n%s', 'trx_addons' ),
																		'<a href="#" class="sc_vgenerator_message_translation" title="' . esc_attr__( 'Click to use as a prompt', 'trx_addons' ) . '" data-tag-prompt="' . esc_attr( $args['prompt'] ) . '">' . $args['prompt'] . '</a>'
																	) )
																. '</p>'
															);
				}

				if ( in_array( $model, Lists::get_list_models_for_access_ai_video_keyframes() ) && empty( $settings['allow_loop'] ) ) {
					if ( ! empty( $_FILES["keyframes_frame0"]["tmp_name"] ) ) {
						$validate = wp_check_filetype( $_FILES["keyframes_frame0"]["name"] );
						if ( $validate['type'] == false ) {
							$answer['error'] = __( "Start keyframe: File type is not allowed.", "trx_addons" );
						} else {
							$ext = trx_addons_get_file_ext( $_FILES["keyframes_frame0"]["name"] );
							if ( empty( $ext ) ) {
								$ext = 'png';
							}
							$keyframes_frame0 = trx_addons_uploads_save_data( trx_addons_fgc( $_FILES["keyframes_frame0"]["tmp_name"] ), array(
								'expire' => apply_filters( 'trx_addons_filter_ai_helper_uploaded_video_expire_time', 10 * 60 ),
								'return' => 'url',
								'ext' => $ext,
							) );
						}
					} else if ( ! empty( $settings['keyframes_frame0'] ) ) {
						$keyframes_frame0 = $settings['keyframes_frame0'];
					}

					if ( ! empty( $_FILES["keyframes_frame1"]["tmp_name"] ) ) {
						$validate = wp_check_filetype( $_FILES["keyframes_frame1"]["name"] );
						if ( $validate['type'] == false ) {
							$answer['error'] = ( ! empty( $answer['error'] ) ? $answer['error'] . ' <br>' : '' ) . __( "End keyframe: File type is not allowed.", "trx_addons" );
						} else {
							$ext = trx_addons_get_file_ext( $_FILES["keyframes_frame1"]["name"] );
							if ( empty( $ext ) ) {
								$ext = 'png';
							}
							$keyframes_frame1 = trx_addons_uploads_save_data( trx_addons_fgc( $_FILES["keyframes_frame1"]["tmp_name"] ), array(
								'expire' => apply_filters( 'trx_addons_filter_ai_helper_uploaded_video_expire_time', 10 * 60 ),
								'return' => 'url',
								'ext' => $ext,
							) );
						}
					} else if ( ! empty( $settings['keyframes_frame0'] ) ) {
						$keyframes_frame1 = $settings['keyframes_frame1'];
					}

					if ( ! empty( $keyframes_frame0 ) || ! empty( $keyframes_frame1 ) ) {
						$args['keyframes'] = array();

						if ( ! empty( $keyframes_frame0 ) ) {
							$args['keyframes']['frame0'] = array(
								'type' => 'image',
								'url' => $keyframes_frame0,
							);
						}

						if ( ! empty( $keyframes_frame1 ) ) {
							$args['keyframes']['frame1'] = array(
								'type' => 'image',
								'url' => $keyframes_frame1,
							);
						}
					}
				}

				if ( array_key_exists( $aspect_ratio, Lists::get_list_ai_video_ar() ) ) {
					$args['aspect_ratio'] = $aspect_ratio;
				}

				if ( in_array( $args['model'], Lists::get_list_models_for_access_ai_video_resolution() ) && in_array( $resolution, Lists::get_list_ai_video_resolutions() ) ) {
					$args['resolution'] = $resolution;
				}

				if ( in_array( $args['model'], Lists::get_list_models_for_access_ai_video_duration() ) && in_array( $duration, Lists::get_list_ai_video_durations() ) ) {
					$args['duration'] = $duration;
				}

				// Generate video
				if ( empty( $answer['error'] ) ) {
					$response = $api->generate_video( apply_filters( 'trx_addons_filter_ai_helper_generate_video_args', $args, 'sc_vgenerator' ) );

					// Parse response
					$answer = Utils::parse_response( $response, $model, $answer, 'video' );
					if ( ! empty( $answer['data']['fetch_id'] ) ) {
						$answer['data']['fetch_number'] = $number;
						$answer['data']['fetch_time'] = apply_filters( 'trx_addons_filter_sc_vgenerator_fetch_time', 8000 );
					}
					trx_addons_sc_vgenerator_set_total_generated( $number, $suffix, $used_limits == 'user' ? $user_id : 0 );
				}

			} else {

				$answer['data']['demo'] = true;
				// Get demo video from the settings
				if ( ! empty( $settings['demo_video'] ) && ! empty( $settings['demo_video'][0]['video']['url'] ) ) {
					$video = array();
					foreach ( $settings['demo_video'] as $file ) {
						$video[] = $file['video']['url'];
					}
				}
				if ( $api->get_api_key() != '' && $demo ) {
					$msg = trx_addons_get_option( "ai_helper_sc_vgenerator_limit_alert{$suffix}" );
					$answer['data']['message'] = ! empty( $msg )
													? $msg
													: apply_filters( "trx_addons_filter_sc_vgenerator_limit_alert{$suffix}",
														'<h5 data-lp="' . ( $lpu ? 'lpu' . $generated : ( $lph ? 'lph' . $generated : ( $lpv ? 'lpv' : '' ) ) ) . '">' . __( 'Limits are reached!', 'trx_addons' ) . '</h5>'
														. '<p>' . __( 'The limit of the number of requests from a single visitor or the number of video that can be generated per hour has been reached.', 'trx_addons' ) . '</p>'
														. ( is_array( $video ) && count( $video ) > 0 ? __( 'Therefore, instead of generated video, you see demo sample.', 'trx_addons' ) : '' )
														. '<p>' . __( ' Please try again later.', 'trx_addons' ) . '</p>'
													);
				}
				if ( is_array( $video ) && count( $video ) > 0 ) {
					shuffle( $video );
					for ( $i = 0; $i < min( $number, count( $video ) ); $i++ ) {
						$answer['data']['video'][] = array(
							'url' => $video[ $i ],
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
		trx_addons_ajax_response( apply_filters( 'trx_addons_filter_sc_vgenerator_answer', $answer ) );
	}
}

if ( ! function_exists( 'trx_addons_sc_vgenerator_fetch_video' ) ) {
	add_action( 'wp_ajax_trx_addons_ai_helper_fetch_video', 'trx_addons_sc_vgenerator_fetch_video' );
	add_action( 'wp_ajax_nopriv_trx_addons_ai_helper_fetch_video', 'trx_addons_sc_vgenerator_fetch_video' );
	/**
	 * Fetch video from the ModelsLab API
	 * 
	 * @hooked 'wp_ajax_trx_addons_ai_helper_fetch_video'
	 * @hooked 'wp_ajax_nopriv_trx_addons_ai_helper_fetch_video'
	 * 
	 * @param WP_REST_Request  $request  Full details about the request.
	 */
	function trx_addons_sc_vgenerator_fetch_video() {
		trx_addons_verify_nonce();

		$answer = array(
			'error' => '',
			'data' => array(
				'video' => array()
			)
		);

		$model = trx_addons_get_value_gp( 'fetch_model', Utils::get_default_video_model() );
		$id    = trx_addons_get_value_gp( 'fetch_id', '' );

		if ( ! empty( $id ) ) {
			// Check if the id is in the cache and it is the same model
			$saved_model = Utils::get_data_from_cache( $id );
			if ( $saved_model == $model ) {
				$api = LumaLabs::instance();
				$response = $api->fetch_video( array(
					'fetch_id'  => $id,
				) );
				$answer = Utils::parse_response( $response, $model, $answer, 'video' );
				// Remove id from the cache if video is fetched
				if ( count( $answer['data']['video'] ) > 0 ) {
					Utils::delete_data_from_cache( $id );
				} else if ( ! empty( $answer['data']['fetch_id'] ) ) {
					$answer['data']['fetch_time'] = apply_filters( 'trx_addons_filter_sc_vgenerator_fetch_time', 8000 );
				}
			} else {
				$answer['error'] = __( 'Error! Incorrect the queue ID for fetch video from server.', 'trx_addons' );
			}
		} else {
			$answer['error'] = __( 'Error! Need the queue ID for fetch video from server.', 'trx_addons' );
		}

		// Return response to the AJAX handler
		trx_addons_ajax_response( $answer );
	}
}

// Callback function to return a generated video from the API server
if ( ! function_exists( 'trx_addons_sc_vgenerator_download_video' ) ) {
	add_action( 'init', 'trx_addons_sc_vgenerator_download_video' );
	function trx_addons_sc_vgenerator_download_video() {
		if ( trx_addons_get_value_gp( 'action' ) != 'trx_addons_ai_helper_vgenerator_download' ) {
			return;
		}
		$video = trx_addons_get_value_gp( 'video' );
		$video_url = Utils::get_data_from_cache( $video );
		$video_ext = trx_addons_get_file_ext( $video );
		$video_content = '';
		if ( ! empty( $video_url ) ) {
			$video_content = trx_addons_fgc( $video_url );
		}
		if ( empty( $video_content ) ) {
			header( 'HTTP/1.0 404 Not found' );
		} else {
			header( "Content-Type: video/{$video_ext}" );
			header( 'Content-Disposition: attachment; filename="' . $video . '"' );
			header( 'Content-Length: ' . strlen( $video_content ) );
			echo $video_content;
		}
		die();
	}
}

// Translate the prompt if it contains non-English characters
if ( ! function_exists( 'trx_addons_sc_vgenerator_translate_prompt' ) ) {
	add_filter( 'trx_addons_filter_ai_helper_prompt', 'trx_addons_sc_vgenerator_translate_prompt', 10, 3 );
	function trx_addons_sc_vgenerator_translate_prompt( $prompt, $args, $from = '' ) {
		// Translate only if this filter was called from the shortcode [trx_sc_vgenerator] or from the Media Library
		// and only if the option 'Translate prompt' is enabled
		// and only if the prompt contains non-English characters
		// and only if the model is not 'dall-e-3'
		if ( in_array( $from, array( 'sc_vgenerator' ) )
			&& (int)trx_addons_get_option( 'ai_helper_sc_vgenerator_translate_prompt' ) == 1
			&& trx_addons_str_is_not_english( $prompt )
		) {
			$model = ! empty( $args['model'] ) ? $args['model'] : Utils::get_default_video_model();
			if ( apply_filters( 'trx_addons_filter_sc_vgenerator_auto_translate_prompt', true || ! Utils::is_lumalabs_ai_model( $model ), $model ) ) {
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
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_ADDONS . 'ai-helper/shortcodes/vgenerator/vgenerator-sc-elementor.php';
}
