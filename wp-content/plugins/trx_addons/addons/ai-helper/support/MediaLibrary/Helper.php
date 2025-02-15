<?php
namespace TrxAddons\AiHelper\MediaLibrary;

use TrxAddons\AiHelper\OpenAi;
use TrxAddons\AiHelper\StableDiffusion;
use TrxAddons\AiHelper\StabilityAi;
use TrxAddons\AiHelper\Lists;
use TrxAddons\AiHelper\Utils;

if ( ! class_exists( 'Helper' ) ) {

	/**
	 * Main class for AI Helper MediaSelector support
	 */
	class Helper {

		/**
		 * Constructor
		 */
		function __construct() {
			add_action( 'trx_addons_action_load_scripts_admin', array( $this, 'enqueue_scripts_admin' ) );
			add_filter( 'trx_addons_filter_localize_script_admin', array( $this, 'localize_script_admin' ) );

			// AJAX callback for the 'Generate images' button
			add_action( 'wp_ajax_trx_addons_ai_helper_generate_images', array( $this, 'generate_images' ) );

			// AJAX callback for the 'Make variations' button
			add_action( 'wp_ajax_trx_addons_ai_helper_make_variations', array( $this, 'make_variations' ) );

			// AJAX callback for the 'Make upscale' button
			add_action( 'wp_ajax_trx_addons_ai_helper_make_upscale', array( $this, 'make_upscale' ) );

			// AJAX callback for the 'Add to Uploads' button
			add_action( 'wp_ajax_trx_addons_ai_helper_add_to_uploads', array( $this, 'add_to_uploads' ) );

			// AJAX callback for the 'Fetch images'
			add_action( 'wp_ajax_trx_addons_ai_helper_fetch_images', array( $this, 'fetch_images' ) );
			add_action( 'wp_ajax_nopriv_trx_addons_ai_helper_fetch_images', array( $this, 'fetch_images' ) );
		}

		/**
		 * Check if AI Helper is allowed for MediaSelector
		 */
		public static function is_allowed() {
			return OpenAi::instance()->get_api_key() != ''
				|| StableDiffusion::instance()->get_api_key() != ''
				|| StabilityAi::instance()->get_api_key() != '';
		}

		/**
		 * Enqueue scripts and styles for the admin mode
		 * 
		 * @hooked 'admin_enqueue_scripts'
		 */
		function enqueue_scripts_admin() {
			if ( self::is_allowed() ) {
				wp_enqueue_style( 'trx_addons-ai-helper-media-selector', trx_addons_get_file_url( TRX_ADDONS_PLUGIN_ADDONS . 'ai-helper/support/MediaLibrary/assets/css/index.css' ), array(), null );
				wp_enqueue_script( 'trx_addons-ai-helper-media-selector', trx_addons_get_file_url( TRX_ADDONS_PLUGIN_ADDONS . 'ai-helper/support/MediaLibrary/assets/js/index.js' ), array( 'jquery' ), null, true );
			}
		}

		/**
		 * Localize script to show messages in the admin mode
		 * 
		 * @hooked 'trx_addons_filter_localize_script_admin'
		 * 
		 * @param array $vars  Array of variables to be passed to the script
		 * 
		 * @return array  Modified array of variables
		 */
		function localize_script_admin( $vars ) {
			if ( self::is_allowed() ) {
				$vars['msg_ai_helper_error'] = esc_html__( "AI Helper unrecognized response", 'trx_addons' );
				$vars['msg_ai_helper_prompt_error'] = esc_html__( "Prompt is empty!", 'trx_addons' );
				$vars['msg_ai_helper_delete_image'] = esc_html__( "Do you really want to delete the selected image from the preview area?", 'trx_addons' );
				$vars['ai_helper_generate_image_models'] = Lists::get_list_ai_image_models();
				$vars['ai_helper_generate_image_styles'] = Lists::get_list_stability_ai_styles();
				$vars['ai_helper_generate_image_styles_openai'] = Lists::get_list_openai_styles();
				$vars['ai_helper_generate_image_sizes'] = Lists::get_list_ai_image_sizes();
				$vars['ai_helper_generate_image_sizes_openai'] = Lists::get_list_ai_image_sizes( 'openai' );
				$vars['ai_helper_generate_image_numbers'] = trx_addons_get_list_range( 1, 10 );
				$vars['ai_helper_generate_image_guidance_scale'] = trx_addons_get_option( 'ai_helper_guidance_scale_stable_diffusion' );
				$vars['ai_helper_generate_image_inference_steps'] = trx_addons_get_option( 'ai_helper_inference_steps_stable_diffusion' );
				$vars['ai_helper_generate_image_cfg_scale'] = trx_addons_get_option( 'ai_helper_cfg_scale_stability_ai' );
				$vars['ai_helper_generate_image_diffusion_steps'] = trx_addons_get_option( 'ai_helper_diffusion_steps_stability_ai' );
				$vars['ai_helper_generate_image_upscalers'] = Lists::get_list_ai_image_upscalers();
			}
			return $vars;
		}

		/**
		 * Send a query to API to generate images from the prompt
		 * 
		 * @hooked 'wp_ajax_trx_addons_ai_helper_generate_images'
		 * 
		 * @param WP_REST_Request  $request  Full details about the request.
		 */
		function generate_images( $request = false ) {

			trx_addons_verify_nonce();

			$answer = array(
				'error' => '',
				'data' => array(
					'images' => array()
				)
			);
			if ( current_user_can( 'edit_posts' ) ) {
				if ( $request ) {
					$params  = $request->get_params();
					$model   = ! empty( $params['model'] ) ? $params['model'] : Utils::get_default_image_model();
					$style   = ! empty( $params['style'] ) ? $params['style'] : '';
					$quality = ! empty( $params['quality'] ) ? $params['quality'] : '';
					$size    = ! empty( $params['size'] ) ? $params['size'] : Utils::get_default_image_size();
					$width   = $size == 'custom' && ! empty( $params['width'] ) ? (int)$params['width'] : 0;
					$height  = $size == 'custom' && ! empty( $params['height'] ) ? (int)$params['height'] : 0;
					$number  = ! empty( $params['number'] ) ? (int)$params['number'] : 1;
					$prompt  = ! empty( $params['prompt'] ) ? $params['prompt'] : '';
					$lora_model = ! empty( $params['lora_model'] ) ? $params['lora_model'] : '';
					$negative_prompt = ! empty( $params['negative_prompt'] ) ? $params['negative_prompt'] : '';
					$guidance_scale = ! empty( $params['guidance_scale'] ) ? (float)$params['guidance_scale'] : 0;
					$inference_steps = ! empty( $params['inference_steps'] ) ? (int)$params['inference_steps'] : 0;
					$cfg_scale = ! empty( $params['cfg_scale'] ) ? (float)$params['cfg_scale'] : 0;
					$diffusion_steps = ! empty( $params['diffusion_steps'] ) ? (int)$params['diffusion_steps'] : 0;
					$seed = ! empty( $params['seed'] ) ? (int)$params['seed'] : 0;
				} else {
					$model   = trx_addons_get_value_gp( 'model', Utils::get_default_image_model() );
					$style   = trx_addons_get_value_gp( 'style', '' );
					$quality = trx_addons_get_value_gp( 'quality', '' );
					$size    = trx_addons_get_value_gp( 'size', Utils::get_default_image_size() );
					$width   = $size == 'custom' ? (int)trx_addons_get_value_gp( 'width', 0 ) : 0;
					$height  = $size == 'custom' ? (int)trx_addons_get_value_gp( 'height', 0 ) : 0;
					$number  = (int)trx_addons_get_value_gp( 'number', 1 );
					$prompt  = trx_addons_get_value_gp( 'prompt' );
					$lora_model = trx_addons_get_value_gp( 'lora_model', '' );
					$negative_prompt = trx_addons_get_value_gp( 'negative_prompt' );
					$guidance_scale = (float)trx_addons_get_value_gp( 'guidance_scale' );
					$inference_steps = (int)trx_addons_get_value_gp( 'inference_steps' );
					$cfg_scale = (float)trx_addons_get_value_gp( 'cfg_scale' );
					$diffusion_steps = (int)trx_addons_get_value_gp( 'diffusion_steps' );
					$seed = (int)trx_addons_get_value_gp( 'seed' );
					$params = compact( 'model', 'style', 'size', 'width', 'height', 'number', 'prompt', 'negative_prompt', 'guidance_scale', 'inference_steps', 'cfg_scale', 'diffusion_steps', 'seed' );
				}
				$number = max( 1, min( 10, $number ) );
				if ( Utils::is_stable_diffusion_model( $model ) ) {
					$number = max( 1, min( 4, $number ) );
				}
				if ( ! empty( $prompt ) ) {
					$api = Utils::get_image_api( $model );
					$args = array(
						'model' => $model,
						'prompt' => apply_filters( 'trx_addons_filter_ai_helper_prompt', $prompt, $params, 'media_library_generate_images' ),
						'size' => Utils::check_image_size( $size ),
						'n' => (int)$number,
					);
					if ( ! Utils::is_model_support_negative_prompt( $model ) ) {
						$negative_prompt = '';
					}
					if ( ! empty( $negative_prompt ) ) {
						$args['negative_prompt'] = apply_filters( 'trx_addons_filter_ai_helper_negative_prompt', $negative_prompt, compact( 'model', 'size', 'number' ), 'media_library_generate_images' );
					}
					if ( Utils::is_model_support_image_dimensions( $model ) ) {
						$width  = max( 0, min( Utils::get_max_image_width(), $width ) );
						$height = max( 0, min( Utils::get_max_image_height(), $height ) );
						if ( $size == 'custom' && $width > 0 && $height > 0 ) {
							$args['width'] = (int)$width;
							$args['height'] = (int)$height;
						}
					}
					if ( ! empty( $style ) ) {
						$args['style'] = $style;
					}
					if ( ! empty( $quality ) ) {
						$args['quality'] = $quality;
					}
					if ( Utils::is_stable_diffusion_model( $model ) ) {
						if ( $guidance_scale > 0 ) {
							$args['guidance_scale'] = $guidance_scale;
						}
						if ( $inference_steps > 0 ) {
							$args['num_inference_steps'] = $inference_steps;
						}
						if ( $seed > 0 ) {
							$args['seed'] = $seed;
						}
						if ( ! empty( $lora_model ) ) {
							$args['lora_model'] = $lora_model;
						}
					} else if ( Utils::is_stability_ai_model( $model ) ) {
						if ( $cfg_scale > 0 ) {
							$args['cfg_scale'] = $cfg_scale;
						}
						if ( $diffusion_steps > 0 ) {
							$args['steps'] = $diffusion_steps;
						}
						if ( $seed > 0 ) {
							$args['seed'] = $seed;
						}
					}
					$response = $api->generate_images( apply_filters( 'trx_addons_filter_ai_helper_generate_images_args', $args, 'media_library_generate_images' ) );
					$answer = Utils::parse_response( $response, $model, $answer, 'image' );
				} else {
					$answer['error'] = __( 'Error! Empty prompt.', 'trx_addons' );
				}
			}

			if ( $request ) {
				// Return response to the REST API
				return rest_ensure_response( $answer );
			} else {
				// Return response to the AJAX handler
				trx_addons_ajax_response( $answer );
			}
		}

		/**
		 * Send a query to API to make variations of the image
		 * 
		 * @hooked 'wp_ajax_trx_addons_ai_helper_make_variations'
		 * 
		 * @param WP_REST_Request  $request  Full details about the request.
		 */
		function make_variations( $request = false ) {

			trx_addons_verify_nonce();

			$answer = array(
				'error' => '',
				'data' => array(
					'images' => array()
				)
			);
			if ( current_user_can( 'edit_posts' ) ) {
				if ( $request ) {
					$params = $request->get_params();
					$prompt = ! empty( $params['prompt'] ) ? $params['prompt'] : '';
					$negative_prompt = ! empty( $params['negative_prompt'] ) ? $params['negative_prompt'] : '';
					$model  = ! empty( $params['model'] ) ? $params['model'] : Utils::get_default_image_model();
					$style  = ! empty( $params['style'] ) ? $params['style'] : '';
					$size   = ! empty( $params['size'] ) ? (int)$params['size'] : Utils::get_default_image_size();
					$width  = ! empty( $params['width'] ) ? (int)$params['width'] : 0;
					$height = ! empty( $params['height'] ) ? (int)$params['height'] : 0;
					$number = ! empty( $params['number'] ) ? (int)$params['number'] : 1;
					$image  = ! empty( $params['image'] ) ? $params['image'] : '';
					$lora_model = ! empty( $params['lora_model'] ) ? $params['lora_model'] : '';
					$guidance_scale = ! empty( $params['guidance_scale'] ) ? (float)$params['guidance_scale'] : 0;
					$inference_steps = ! empty( $params['inference_steps'] ) ? (int)$params['inference_steps'] : 0;
					$cfg_scale = ! empty( $params['cfg_scale'] ) ? (float)$params['cfg_scale'] : 0;
					$diffusion_steps = ! empty( $params['diffusion_steps'] ) ? (int)$params['diffusion_steps'] : 0;
					$seed = ! empty( $params['seed'] ) ? (int)$params['seed'] : 0;
				} else {
					$prompt = trx_addons_get_value_gp( 'prompt', '' );
					$negative_prompt = trx_addons_get_value_gp( 'negative_prompt', '' );
					$model  = trx_addons_get_value_gp( 'model', Utils::get_default_image_model() );
					$style  = trx_addons_get_value_gp( 'style', '' );
					$size   = trx_addons_get_value_gp( 'size', Utils::get_default_image_size() );
					$width  = (int)trx_addons_get_value_gp( 'width', 0 );
					$height = (int)trx_addons_get_value_gp( 'height', 0 );
					$number = (int)trx_addons_get_value_gp( 'number', 1 );
					$image  = trx_addons_get_value_gp( 'image' );
					$lora_model = trx_addons_get_value_gp( 'lora_model', '' );
					$guidance_scale = (float)trx_addons_get_value_gp( 'guidance_scale' );
					$inference_steps = (int)trx_addons_get_value_gp( 'inference_steps' );
					$cfg_scale = (float)trx_addons_get_value_gp( 'cfg_scale' );
					$diffusion_steps = (int)trx_addons_get_value_gp( 'diffusion_steps' );
					$seed = (int)trx_addons_get_value_gp( 'seed' );
					$params = compact( 'prompt', 'negative_prompt', 'model', 'size', 'width', 'height', 'number', 'image', 'guidance_scale', 'inference_steps', 'cfg_scale', 'diffusion_steps', 'seed' );
				}
				$number = max( 1, min( 10, $number ) );
				if ( Utils::is_stable_diffusion_model( $model ) ) {
					$number = max( 1, min( 4, $number ) );
				}
				if ( ! empty( $image ) ) {
					$api = Utils::get_image_api( $model );
					$args = array(
						'image' => $image,
						'n'     => (int)$number,
					);
					if ( Utils::is_stable_diffusion_model( $model ) || Utils::is_stability_ai_model( $model ) ) {
						$args['prompt'] = apply_filters( 'trx_addons_filter_ai_helper_prompt', $prompt, $args, 'media_library_variations' );
					}
					if ( ! Utils::is_model_support_negative_prompt( $model ) ) {
						$negative_prompt = '';
					}
					if ( ! empty( $negative_prompt ) ) {
						$args['negative_prompt'] = apply_filters( 'trx_addons_filter_ai_helper_negative_prompt', $negative_prompt, compact( 'model', 'size', 'number' ), 'media_library_variations' );
					}
					if ( $size !== 'custom' ) {
						$args['size'] = Utils::check_image_size( $size );
					}
					if ( Utils::is_model_support_image_dimensions( $model ) ) {
						$args['model']  = $model;
						if ( $size == 'custom' ) {
							$width  = max( 0, min( Utils::get_max_image_width(), $width ) );
							$height = max( 0, min( Utils::get_max_image_height(), $height ) );
							if ( $width > 0 && $height > 0 ) {
								$args['width'] = (int)$width;
								$args['height'] = (int)$height;
							}
						}
					}
					if ( Utils::is_stable_diffusion_model( $model ) ) {
						if ( $guidance_scale > 0 ) {
							$args['guidance_scale'] = $guidance_scale;
						}
						if ( $inference_steps > 0 ) {
							$args['num_inference_steps'] = $inference_steps;
						}
						if ( $seed > 0 ) {
							$args['seed'] = $seed;
						}
						if ( ! empty( $lora_model ) ) {
							$args['lora_model'] = $lora_model;
						}
					}
					if ( Utils::is_stability_ai_model( $model ) ) {
						if ( ! empty( $style ) ) {
							$args['style'] = $style;
						}
						if ( $cfg_scale > 0 ) {
							$args['cfg_scale'] = $cfg_scale;
						}
						if ( $diffusion_steps > 0 ) {
							$args['steps'] = $diffusion_steps;
						}
						if ( $seed > 0 ) {
							$args['seed'] = $seed;
						}
					}
					$response = $api->make_variations( apply_filters( 'trx_addons_filter_ai_helper_variations_args', $args, 'media_library_variations' ) );
					$answer = Utils::parse_response( $response, $model, $answer, 'image' );
				} else {
					$answer['error'] = __( 'Error! Image is not specified.', 'trx_addons' );
				}
			}

			if ( $request ) {
				// Return response to the REST API
				return rest_ensure_response( $answer );
			} else {
				// Return response to the AJAX handler
				trx_addons_ajax_response( $answer );
			}
		}

		/**
		 * Send a query to API to upscale of the image
		 * 
		 * @hooked 'wp_ajax_trx_addons_ai_helper_make_upscale'
		 * 
		 * @param WP_REST_Request  $request  Full details about the request.
		 */
		function make_upscale( $request = false ) {

			trx_addons_verify_nonce();

			$answer = array(
				'error' => '',
				'data' => array(
					'images' => array()
				)
			);
			if ( current_user_can( 'edit_posts' ) ) {
				if ( $request ) {
					$params = $request->get_params();
					$model  = ! empty( $params['model'] ) ? $params['model'] : trx_addons_array_get_first( Lists::get_list_ai_image_upscalers() );
					$scale  = ! empty( $params['scale'] ) ? max( 2, min( 4, (int)$params['scale'] ) ) : 2;
					$width  = ! empty( $params['width'] ) ? (int)$params['width'] : 0;
					$height = ! empty( $params['height'] ) ? (int)$params['height'] : 0;
					$image  = ! empty( $params['image'] ) ? $params['image'] : '';
				} else {
					$model  = trx_addons_get_value_gp( 'model', trx_addons_array_get_first( Lists::get_list_ai_image_upscalers() ) );
					$scale  = max( 2, min( 4, trx_addons_get_value_gp( 'scale', 2 ) ) );
					$width  = (int)trx_addons_get_value_gp( 'width', 0 );
					$height = (int)trx_addons_get_value_gp( 'height', 0 );
					$image  = trx_addons_get_value_gp( 'image' );
					$params = compact( 'model', 'scale', 'width', 'height', 'image' );
				}
				if ( ! empty( $image ) ) {
					$api = Utils::get_image_api( $model );
					$args = array(
						'image' => $image,
						'n'     => 1,
					);
					if ( Utils::is_stable_diffusion_model( $model ) ) {
						$args['scale'] = $scale;
					} else if ( Utils::is_stability_ai_model( $model ) ) {
						if ( $width > 0 ) {
							$args['width'] = $width;
						} else if ( $height > 0 ) {
							$args['height'] = $height;
						}
					}
					if ( Utils::is_openai_model( $model ) ) {
						$answer['error'] = __( 'OpenAi API is not support upscaling!', 'trx_addons' );
					} else {
						if ( ! empty( $args['image'] ) ) {
							$response = $api->upscale( apply_filters( 'trx_addons_filter_ai_helper_upscale_args', $args, 'media_library_upscale' ) );
						} else {
							$answer['error'] = __( 'Error! The image is not uploaded.', 'trx_addons' );
						}
					}
					$answer = Utils::parse_response( $response, $model, $answer, 'image' );
				} else {
					$answer['error'] = __( 'Error! Image is not specified.', 'trx_addons' );
				}
			}

			if ( $request ) {
				// Return response to the REST API
				return rest_ensure_response( $answer );
			} else {
				// Return response to the AJAX handler
				trx_addons_ajax_response( $answer );
			}
		}

		/**
		 * Add an image to the media library
		 * 
		 * @hooked 'wp_ajax_trx_addons_ai_helper_add_to_uploads'
		 * 
		 * @param WP_REST_Request  $request  Full details about the request.
		 */
		function add_to_uploads( $request = false ) {

			trx_addons_verify_nonce();

			$answer = array(
				'error' => '',
				'data' => ''
			);
			if ( current_user_can( 'edit_posts' ) ) {
				if ( $request ) {
					$params = $request->get_params();
					$image = ! empty( $params['image'] ) ? $params['image'] : '';
					$filename = ! empty( $params['filename'] ) ? $params['filename'] : '';
					$caption = ! empty( $params['caption'] ) ? $params['caption'] : '';
				} else {
					$image = trx_addons_get_value_gp( 'image' );
					$filename = trx_addons_get_value_gp( 'filename' );
					$caption = trx_addons_get_value_gp( 'caption' );
				}
				if ( ! empty( $image ) ) {
					$parts = explode( '.', trim( $filename ) );
					$filename = trx_addons_esc( str_replace( ' ', '-', $parts[0] ) . '.png' );
					$attach_id = trx_addons_save_image_to_uploads( array(
						'image' => '',				// binary data of the image
						'image_url' => $image,		// or URL of the image
						'filename' => $filename,	// filename for the image in the media library
						'caption' => $caption,		// caption for the image in the media library
					) );
					if ( $attach_id == 0 || is_wp_error( $attach_id ) ) {
						$answer['error'] = is_wp_error( $attach_id ) ? $attach_id->get_error_message() : __( "Error! Can't insert an image into the media library.", 'trx_addons' );
					} else {
						$answer['data'] = $attach_id;
					}
				} else {
					$answer['error'] = __( 'Error! Image URL is empty.', 'trx_addons' );
				}
			}
			if ( $request ) {
				// Return response to the REST API
				return rest_ensure_response( $answer );
			} else {
				// Return response to the AJAX handler
				trx_addons_ajax_response( $answer );
			}
		}

		/**
		 * Fetch images from the ModelsLab (ex Stable Diffusion) API
		 * 
		 * @hooked 'wp_ajax_trx_addons_ai_helper_fetch_images'
		 * 
		 * @param WP_REST_Request  $request  Full details about the request.
		 */
		function fetch_images( $request = false ) {

			trx_addons_verify_nonce();

			$answer = array(
				'error' => '',
				'data' => array(
					'images' => array()
				)
			);

			if ( $request ) {
				$params = $request->get_params();
				$model  = ! empty( $params['fetch_model'] ) ? $params['fetch_model'] : Utils::get_default_image_model();
				$id     = ! empty( $params['fetch_id'] ) ? $params['fetch_id'] : '';
				$url    = ! empty( $params['fetch_url'] ) ? $params['fetch_url'] : '';
			} else {
				$model   = trx_addons_get_value_gp( 'fetch_model', Utils::get_default_image_model() );
				$id      = trx_addons_get_value_gp( 'fetch_id', '' );
				$url     = trx_addons_get_value_gp( 'fetch_url', '' );
			}

			if ( ! empty( $id ) ) {
				// Check if the id is in the cache and it is the same model
				$saved_model = Utils::get_data_from_cache( $id );
				if ( $saved_model == $model ) {
					$api = StableDiffusion::instance();
					$response = $api->fetch_images( array(
						'fetch_id'  => $id,
						'fetch_url' => $url,
						'model'     => $model,
					) );
					$answer = Utils::parse_response( $response, $model, $answer, 'image' );
					// Remove id from the cache if images are fetched
					if ( count( $answer['data']['images'] ) > 0 ) {
						Utils::delete_data_from_cache( $id );
					}
				} else {
					$answer['error'] = __( 'Error! Incorrect the queue ID for fetch images from server.', 'trx_addons' );
				}
			} else {
				$answer['error'] = __( 'Error! Need the queue ID for fetch images from server.', 'trx_addons' );
			}

			if ( $request ) {
				// Return response to the REST API
				return rest_ensure_response( $answer );
			} else {
				// Return response to the AJAX handler
				trx_addons_ajax_response( $answer );
			}
		}
	}
}
