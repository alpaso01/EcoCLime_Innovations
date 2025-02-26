<?php
namespace TrxAddons\AiHelper;

if ( ! trait_exists( 'ListsImageGeneration' ) ) {

	/**
	 * Return arrays with the lists used for the image generation
	 */
	trait ListsImageGeneration {

		/**
		 * Return a list of image generation APIs
		 * 
		 * @access public
		 * @static
		 * 
		 * @return array  	  The list of image generation APIs
		 */
		static function get_list_ai_image_apis() {
			return apply_filters( 'trx_addons_filter_ai_helper_list_ai_image_apis', array(
				'openai' => esc_html__( 'Open AI', 'trx_addons' ),
				'stable-diffusion' => esc_html__( 'ModelsLab (ex Stable Diffusion)', 'trx_addons' ),
				'stability-ai' => esc_html__( 'Stability AI', 'trx_addons' ),
			) );
		}

		/**
		 * Return a default list of image APIs with the enabled status
		 * 
		 * @access public
		 * @static
		 * 
		 * @return array  	  The default list of image APIs
		 */
		static function get_list_ai_image_apis_enabled() {
			$api_list = self::get_list_ai_image_apis();
			if ( ! is_array( $api_list ) ) {
				$api_list = array();
			}
			foreach( $api_list as $api => $title ) {
				$api_list[ $api ] = 1;
			}
			return $api_list;
		}

		/**
		 * Return a list of image models for AI
		 * 
		 * @access public
		 * @static
		 * 
		 * @return array  	  The list of image models
		 */
		static function get_list_ai_image_models( $groups = true ) {
			$api_order = trx_addons_get_option( 'ai_helper_sc_igenerator_api_order', self::get_list_ai_image_apis_enabled() );
			$models = array();
			foreach( $api_order as $api => $enable ) {
				// Open AI
				if ( $api == 'openai' && (int)$enable > 0 ) {
					$openai_models = self::get_openai_models();
					if ( is_array( $openai_models ) && count( $openai_models ) > 0 ) {
						if ( $groups ) {
							$models[ 'openai/-' ] = '\\-' . __( 'Open AI models', 'trx_addons' );
						}
						foreach ( $openai_models as $k => $v ) {
							$models[ 'openai/' . $k ] = $v['title'];
						}
					}
				}
				// ModelsLab (ex Stable Diffusion)
				if ( $api == 'stable-diffusion' && (int)$enable > 0 ) {
					$sd_models = self::get_sd_models();
					if ( is_array( $sd_models ) && count( $sd_models ) > 0 ) {
						if ( $groups ) {
							$models[ 'stable-diffusion/-' ] = '\\-' . __( 'ModelsLab (ex Stable Diffusion) models', 'trx_addons' );
						}
						foreach ( $sd_models as $k => $v ) {
							$models[ 'stable-diffusion/' . $k ] = $v['title'];
						}
					}
				}
				// Stability AI
				if ( $api == 'stability-ai' && (int)$enable > 0 ) {
					$stability_models = self::get_stability_ai_models();
					if ( is_array( $stability_models ) && count( $stability_models ) > 0 ) {
						if ( $groups ) {
							$models[ 'stability-ai/-' ] = '\\-' . __( 'Stability AI models', 'trx_addons' );
						}
						foreach ( $stability_models as $k => $v ) {
							$models[ 'stability-ai/' . $k ] = $v['title'];
						}
					}
				}
			}
			return apply_filters( 'trx_addons_filter_ai_helper_list_ai_image_models', $models );
		}

		/**
		 * Return a list of image upscalers for AI
		 * 
		 * @access public
		 * @static
		 * 
		 * @return array  	  The list of image upscalers
		 */
		static function get_list_ai_image_upscalers() {
			$api_order = trx_addons_get_option( 'ai_helper_sc_igenerator_api_order' );
			$upscalers = array();
			foreach( $api_order as $api => $enable ) {
				// Open AI
				if ( $api == 'openai' && (int)$enable > 0 ) {
					$openai_upscalers = self::get_openai_upscalers();
					foreach ( $openai_upscalers as $k => $v ) {
						$upscalers[ 'openai/' . $k ] = $v['title'];
					}
				}
				// ModelsLab (ex Stable Diffusion)
				if ( $api == 'stable-diffusion' && (int)$enable > 0 ) {
					$sd_upscalers = self::get_sd_upscalers();
					foreach ( $sd_upscalers as $k => $v ) {
						$upscalers[ 'stable-diffusion/' . $k ] = $v['title'];
					}
				}
				// Stability AI
				if ( $api == 'stability-ai' && (int)$enable > 0 ) {
					$stability_upscalers = self::get_stability_ai_upscalers();
					foreach ( $stability_upscalers as $k => $v ) {
						$upscalers[ 'stability-ai/' . $k ] = $v['title'];
					}
				}
			}
			return apply_filters( 'trx_addons_filter_ai_helper_list_ai_image_upscalers', $upscalers );
		}


		/**
		 * Return a list of image generator layouts
		 * 
		 * @access public
		 * @static
		 * 
		 * @return array  	  The list of image generator layouts
		 */
		static function get_list_sc_image_generator_layouts() {
			return apply_filters( 'trx_addons_filter_ai_helper_list_sc_image_generator_layouts', array(
				'default'  => esc_html__( 'Simple', 'trx_addons' ),
				'extended' => esc_html__( 'Extended', 'trx_addons' ),
			) );
		}

		/**
		 * Return a list of image prompt templates for AI
		 * 
		 * @access public
		 * @static
		 * 
		 * @return array  	  The list of image prompt templates
		 */
		static function get_list_ai_image_templates() {
			return apply_filters( 'trx_addons_filter_ai_helper_list_ai_image_templates', array(
				'default'   => array( 'title' => esc_html__( 'Default', 'trx_addons' ), 'prompt' => '' ),
				'steampunk' => array( 'title' => esc_html__( 'Steampunk Architecture', 'trx_addons' ), 'prompt' => esc_html__( 'steampunk architecture, exterior view, award-winning architectural photography from magazine, trees, theater', 'trx_addons' ) ),
				'ghilbi' => array( 'title' => esc_html__( 'Ghilbi Inspired' ), 'prompt' => esc_html__( 'japan, tokyo, trees, izakaya, anime oil painting, high resolution, ghibli inspired, 4k', 'trx_addons' ) ),
				'modern' => array( 'title' => esc_html__( 'Modern Illustration' ), 'prompt' => esc_html__( 'illustration of a ..., modern design, for the web, cute, happy, 4k, high resolution, trending in artstation', 'trx_addons' ) ),
			) );
		}

		/**
		 * Return a list of image sizes for AI
		 * 
		 * @access public
		 * @static
		 * 
		 * @param string $api  The name of API:
		 *						'all' - all APIs,
		 *						'openai' - OpenAi,
		 *						'sd' | 'stable-diffusion' | 'ml' | 'modelslab' - ModelsLab (ex Stable Diffusion),
		 *						'stability-ai' - Stability AI
		 * 
		 * @return array  	  The list of image sizes
		 */
		static function get_list_ai_image_sizes( $api = 'all' ) {
			$openai_enable = trx_addons_get_option( 'ai_helper_token_openai', '' ) != '';
			$sd_enable = trx_addons_get_option( 'ai_helper_token_stable_diffusion', '' ) != '';
			$stability_enable = trx_addons_get_option( 'ai_helper_token_stability_ai', '' ) != '';
			return apply_filters( 'trx_addons_filter_ai_helper_list_ai_image_sizes', array_merge(
					// Open AI and Stable Diffusion
					in_array( $api, array( 'all', 'openai', 'sd', 'stable-diffusion', 'ml', 'modelslab' ) ) && ( $openai_enable || $sd_enable )
						? array(
							'256x256'   => esc_html__( ' 256 x  256', 'trx_addons' ),
							'512x512'   => esc_html__( ' 512 x  512', 'trx_addons' ),
							)
						: array(),
					// Any model
					$openai_enable || $sd_enable || $stability_enable
						? array(
							'1024x1024' => esc_html__( '1024 x 1024', 'trx_addons' ),
							)
						: array(),
					// Open AI DALL-E-3 only
					in_array( $api, array( 'all', 'openai' ) ) && $openai_enable
						? array(
							'1792x1024' => esc_html__( '1792 x 1024 (Open AI DALL-E-3 only)', 'trx_addons' ),
							'1024x1792' => esc_html__( '1024 x 1792 (Open AI DALL-E-3 only)', 'trx_addons' ),
							)
						: array(),
					// ModelsLab (ex Stable Diffusion) only
					in_array( $api, array( 'all', 'sd', 'stable-diffusion', 'ml', 'modelslab' ) ) && $sd_enable
						? array(
							'1024x512' => esc_html__( '1024 x  512 (2:1, SD only)', 'trx_addons' ),
							'1024x576' => esc_html__( '1024 x  576 (16:9, SD only)', 'trx_addons' ),
							'1024x640' => esc_html__( '1024 x  640 (16:10, SD only)', 'trx_addons' ),
							'1024x768' => esc_html__( '1024 x  768 (4:3, SD only)', 'trx_addons' ),
							'512x1024' => esc_html__( ' 512 x 1024 (1:2, SD only)', 'trx_addons' ),
							'576x1024' => esc_html__( ' 576 x 1024 (9:16, SD only)', 'trx_addons' ),
							'640x1024' => esc_html__( ' 640 x 1024 (10:16, SD only)', 'trx_addons' ),
							'768x1024' => esc_html__( ' 768 x 1024 (3:4, SD only)', 'trx_addons' ),
							)
						: array(),
					// Stability AI only
					in_array( $api, array( 'all', 'stability-ai' ) ) && $stability_enable
						? array(
							'1152x896' => esc_html__( '1152 x  896 (Stability AI only)', 'trx_addons' ),
							'1216x832' => esc_html__( '1216 x  832 (Stability AI only)', 'trx_addons' ),
							'1344x768' => esc_html__( '1344 x  768 (Stability AI only)', 'trx_addons' ),
							'1536x640' => esc_html__( '1536 x  640 (Stability AI only)', 'trx_addons' ),
							'640x1536' => esc_html__( ' 640 x 1536 (Stability AI only)', 'trx_addons' ),
							'768x1344' => esc_html__( ' 768 x 1344 (Stability AI only)', 'trx_addons' ),
							'832x1216' => esc_html__( ' 832 x 1216 (Stability AI only)', 'trx_addons' ),
							'896x1152' => esc_html__( ' 896 x 1152 (Stability AI only)', 'trx_addons' ),
							)
						: array(),
					// ModelsLab (ex Stable Diffusion) and Stability AI only
					in_array( $api, array( 'all', 'sd', 'stable-diffusion', 'ml', 'modelslab', 'stability-ai' ) ) && ( $sd_enable || $stability_enable )
						? array(
							'custom'   => esc_html__( 'Custom (SD and Stability AI)', 'trx_addons' ),
							)
						: array()
					),
					$api
				);
		}



		/* OPENAI API
		--------------------------------------------------------------------------------------- */

		/**
		 * Return a default list of image models for OpenAi
		 * 
		 * @access public
		 * @static
		 * 
		 * @return array  	  The list of image models for OpenAi
		 */
		static function get_default_openai_models() {
			return apply_filters( 'trx_addons_filter_ai_helper_default_openai_models', array(
				'default' => array(
					'title' => esc_html__( 'Open AI Default', 'trx_addons' )
				),
				'dall-e-2' => array(
					'title' => esc_html__( 'DALL-E-2', 'trx_addons' )
				),
				'dall-e-3' => array(
					'title' => esc_html__( 'DALL-E-3', 'trx_addons' )
				),
			) );
		}

		/**
		 * Return a list of image models for OpenAi
		 * 
		 * @access public
		 * @static
		 * 
		 * @return array  	  The list of image models for OpenAi
		 */
		static function get_openai_models() {
			$models = array();
			$token = trx_addons_get_option( 'ai_helper_token_openai', '' );
			if ( ! empty( $token ) ) {
				$models = trx_addons_get_option( 'ai_helper_models_openai' );
				if ( empty( $models ) || ! is_array( $models ) || empty( $models[0]['id'] ) ) {
					$models = self::get_default_openai_models();
				} else {
					$new_models = array();
					foreach ( $models as $k => $v ) {
						if ( ! empty( $v['id'] ) ) {
							$new_models[ $v['id'] ] = $v;
							unset( $new_models[ $v['id'] ]['id'] );
						}
					}
					if ( ! isset( $new_models['default'] ) ) {
						$new_models = array_merge( array( 'default' => array( 'title' => esc_html__( 'Open AI default', 'trx_addons' ) ) ), $new_models );
					}
					$models = $new_models;
				}
			}

			return $models;
		}

		/**
		 * Return a default list of upscalers for OpenAi
		 * 
		 * @access public
		 * @static
		 * 
		 * @return array  	  The list of upscalers for OpenAi
		 */
		static function get_default_openai_upscalers() {
			return apply_filters( 'trx_addons_filter_ai_helper_default_openai_upscalers', array() );
		}

		/**
		 * Return a list of upscalers for OpenAi with max tokens for each model
		 * 
		 * @access public
		 * @static
		 * 
		 * @return array  	  The list of upscalers for OpenAi
		 */
		static function get_openai_upscalers() {
			$upscalers = array();
			$token = trx_addons_get_option( 'ai_helper_token_openai', '' );
			if ( ! empty( $token ) ) {
				$upscalers = self::get_default_openai_upscalers();
			}
			return $upscalers;
		}

		/**
		 * Return a list of text languages for AI translations
		 * 
		 * @access public
		 * @static
		 * 
		 * @return array  	  The list of languages
		 */
		static function get_list_openai_styles() {
			return apply_filters( 'trx_addons_filter_ai_helper_list_openai_styles', array(
				'' => esc_html__( 'None', 'trx_addons' ),
				'vivid' => esc_html__( 'Vivid', 'trx_addons' ),
				'natural' => esc_html__( 'Natural', 'trx_addons' ),
			) );
		}



		/* MODELSLAB (ex STABLE DIFFUSION / SD) API
		--------------------------------------------------------------------------------------- */

		/**
		 * Return a default list of models for SD
		 * 
		 * @access public
		 * @static
		 * 
		 * @return array  	  The list of models for SD
		 */
		static function get_default_sd_models() {
			return apply_filters( 'trx_addons_filter_ai_helper_default_sd_models', array(
				'default' => array( 
					'title' => esc_html__( 'Stable Diffusion (SD) Default', 'trx_addons' ),
				),
				'midjourney' => array(
					'title' => esc_html__( 'MidJourney', 'trx_addons' ),
				),
				'realistic-vision-v13' => array(
					'title' => esc_html__( 'Realistic Vision V1.3', 'trx_addons' ),
				),
				'dream-shaper-8797' => array(
					'title' => esc_html__( 'Dream Shaper', 'trx_addons' ),
				),
				'protogen-3.4' => array(
					'title' => esc_html__( 'Protogen x3.4', 'trx_addons' ),
				),
				'f222-diffusion' => array(
					'title' => esc_html__( 'F222', 'trx_addons' ),
				),
				'portraitplus-diffusion' => array(
					'title' => esc_html__( 'Portrait+', 'trx_addons' ),
				),
				'perfect-deli-appfact' => array(
					'title' => esc_html__( 'Perfect deli appfactory', 'trx_addons' ),
				),
				'deliberateappfactory' => array(
					'title' => esc_html__( 'Deliberate_appfactory', 'trx_addons' ),
				),
				'anything-v5' => array(
					'title' => esc_html__( 'Anything V5', 'trx_addons' ),
				),
				'anything-v4' => array(
					'title' => esc_html__( 'Anything V4', 'trx_addons' ),
				),
				'anything-v3' => array(
					'title' => esc_html__( 'Anything V3', 'trx_addons' ),
				),
				'gta5-artwork-diffusi' => array(
					'title' => esc_html__( 'GTA5 Artwork Diffusion', 'trx_addons' ),
				),
				'wifu-diffusion' => array(
					'title' => esc_html__( 'Wifu Diffusion', 'trx_addons' ),
				),
			) );
		}

		/**
		 * Return a list of models for SD with max tokens for each model
		 * 
		 * @access public
		 * @static
		 * 
		 * @return array  	  The list of models for SD
		 */
		static function get_sd_models() {
			$models = array();
			$token = trx_addons_get_option( 'ai_helper_token_stable_diffusion', '' );
			if ( ! empty( $token ) ) {
				$autoload = trx_addons_get_option( 'ai_helper_autoload_models_stable_diffusion' );
				if ( (int)$autoload > 0 ) {
					$models = get_transient( "trx_addons_ai_helper_list_models_stable_diffusion" );
					if ( ! is_array( $models ) || count( $models ) == 0 ) {
						$models = StableDiffusion::instance()->list_models();
						if ( is_array( $models ) && count( $models ) > 0 ) {
							$new_models = array();
							foreach ( $models as $v ) {
								if ( ! empty( $v['model_id'] ) && ! empty( $v['model_name'] ) ) {
									$new_models[ $v['model_id'] ] = array( 'title' => $v['model_name'] );
								}
							}
							if ( ! isset( $new_models['default'] ) ) {
								$new_models = array_merge( array( 'default' => array( 'title' => esc_html__( 'Stable Diffusion default', 'trx_addons' ) ) ), $new_models );
							}
							$models = $new_models;
						} else {
							$models = self::get_default_sd_models();
						}
						set_transient( "trx_addons_ai_helper_list_models_stable_diffusion", $models, 7 * 24 * 60 * 60 );	// 7 days
					}
				} else {
					$models = trx_addons_get_option( 'ai_helper_models_stable_diffusion' );
					if ( empty( $models ) || ! is_array( $models ) || empty( $models[0]['id'] ) ) {
						$models = self::get_default_sd_models();
					} else {
						$new_models = array();
						foreach ( $models as $v ) {
							if ( ! empty( $v['id'] ) ) {
								$new_models[ $v['id'] ] = $v;
								unset( $new_models[ $v['id'] ]['id'] );
							}
						}
						if ( ! isset( $new_models['default'] ) ) {
							$new_models = array_merge( array( 'default' => array( 'title' => esc_html__( 'Stable Diffusion default', 'trx_addons' ) ) ), $new_models );
						}
						$models = $new_models;
					}
				}
			}
			return $models;
		}

		/**
		 * Return a default list of upscalers for SD
		 * 
		 * @access public
		 * @static
		 * 
		 * @return array  	  The list of upscalers for SD
		 */
		static function get_default_sd_upscalers() {
			return apply_filters( 'trx_addons_filter_ai_helper_default_sd_upscalers', array(
				'upscale-sd-default' => array( 
					'title' => esc_html__( 'Stable Diffusion Default Upscaler', 'trx_addons' ),
					'model_id' => ''	//'realesr-general-x4v3'
				),
				'upscale-RealESRGAN_x4plus' => array( 
					'title' => esc_html__( 'Stable Diffusion RealESRGAN_x4plus', 'trx_addons' ),
					'model_id' => 'RealESRGAN_x4plus'
				),
				'upscale-RealESRNet_x4plus' => array( 
					'title' => esc_html__( 'Stable Diffusion RealESRNet_x4plus', 'trx_addons' ),
					'model_id' => 'RealESRNet_x4plus'
				),
				'upscale-RealESRGAN_x4plus_anime_6B' => array( 
					'title' => esc_html__( 'Stable Diffusion RealESRGAN_x4plus_anime_6B', 'trx_addons' ),
					'model_id' => 'RealESRGAN_x4plus_anime_6B'
				),
				'upscale-RealESRGAN_x2plus' => array( 
					'title' => esc_html__( 'Stable Diffusion RealESRGAN_x2plus', 'trx_addons' ),
					'model_id' => 'RealESRGAN_x2plus'
				),
			) );
		}

		/**
		 * Return a list of upscalers for SD with max tokens for each model
		 * 
		 * @access public
		 * @static
		 * 
		 * @return array  	  The list of upscalers for SD
		 */
		static function get_sd_upscalers() {
			$upscalers = array();
			$token = trx_addons_get_option( 'ai_helper_token_stable_diffusion', '' );
			if ( ! empty( $token ) ) {
				$upscalers = trx_addons_get_option( 'ai_helper_upscalers_stable_diffusion', '' );
				if ( empty( $upscalers ) || ! is_array( $upscalers ) || empty( $upscalers[0]['id'] ) ) {
					$upscalers = self::get_default_sd_upscalers();
				} else {
					$new_upscalers = array();
					foreach ( $upscalers as $k => $v ) {
						if ( ! empty( $v['id'] ) ) {
							$new_upscalers[ $v['id'] ] = $v;
							unset( $new_upscalers[ $v['id'] ]['id'] );
						}
					}
					if ( ! isset( $new_upscalers['default'] ) ) {
						$new_upscalers = array_merge(
											array(
												'upscale-sd-default' => array(
													'title' => esc_html__( 'Stable Diffusion Default Upscaler', 'trx_addons' ),
													'model_id' => ''	// 'realesr-general-x4v3'
												)
											),
											$new_upscalers
										);
					}
					$upscalers = $new_upscalers;
				}
			}
			return $upscalers;
		}

		/**
		 * Return a list of Safety Checkers for SD
		 * 
		 * @access public
		 * @static
		 * 
		 * @return array  	  The list of safety checkers for SD
		 */
		static function get_sd_safety_checkers() {
			return apply_filters( 'trx_addons_filter_ai_helper_sd_safety_checkers', array(
				'none'                   => esc_html__( 'None', 'trx_addons' ),
				'sensitive_content_text' => esc_html__( 'Sensitive content text', 'trx_addons' ),
				'blur'                   => esc_html__( 'Blur', 'trx_addons' ),
				'pixelate'               => esc_html__( 'Pixelate', 'trx_addons' ),
				'black'                  => esc_html__( 'Black', 'trx_addons' ),
			) );
		}



		/* STABILITY AI API
		--------------------------------------------------------------------------------------- */

		/**
		 * Return a default list of models for Stability AI
		 * 
		 * @access public
		 * @static
		 * 
		 * @return array  	  The list of models for Stability AI
		 */
		static function get_default_stability_ai_models() {
			return apply_filters( 'trx_addons_filter_ai_helper_default_stability_ai_models', array(
				'stable-diffusion-xl-1024-v0-9' => array(
					'title' => esc_html__( 'Stable Diffusion XL 0.9', 'trx_addons' ),
				),
				'stable-diffusion-xl-1024-v1-0' => array( 
					'title' => esc_html__( 'Stable Diffusion XL 1.0', 'trx_addons' ),
				),
				'stable-diffusion-v1-6' => array(
					'title' => esc_html__( 'Stable Diffusion 1.6', 'trx_addons' ),
				),
				'stable-diffusion-512-v2-1' => array(
					'title' => esc_html__( 'Stable Diffusion 2.1', 'trx_addons' ),
				),
				'stable-diffusion-xl-beta-v2-2-2 ' => array(
					'title' => esc_html__( 'Stable Diffusion XL Beta v.2.2.2', 'trx_addons' ),
				),
			) );
		}

		/**
		 * Return a list of models for Stability AI with max tokens for each model
		 * 
		 * @access public
		 * @static
		 * 
		 * @return array  	  The list of models for Stability AI
		 */
		static function get_stability_ai_models() {
			$models = array();
			$token = trx_addons_get_option( 'ai_helper_token_stability_ai', '' );
			if ( ! empty( $token ) ) {
				$autoload = trx_addons_get_option( 'ai_helper_autoload_models_stability_ai' );
				if ( (int)$autoload > 0 ) {
					$models = get_transient( "trx_addons_ai_helper_list_models_stability_ai" );
					if ( ! is_array( $models ) || count( $models ) == 0 ) {
						$models = StabilityAi::instance()->list_models();
						if ( is_array( $models ) && count( $models ) > 0 ) {
							$new_models = array();
							foreach ( $models as $v ) {
								if ( ! empty( $v['id'] ) && ! empty( $v['name'] ) ) {
									$new_models[ $v['id'] ] = array( 'title' => $v['name'] );
								}
							}
							$models = $new_models;
						} else {
							$models = self::get_default_sd_models();
						}
						set_transient( "trx_addons_ai_helper_list_models_stability_ai", $models, 7 * 24 * 60 * 60 );	// 7 days
					}
				} else {
					$models = trx_addons_get_option( 'ai_helper_models_stability_ai' );
					if ( empty( $models ) || ! is_array( $models ) || empty( $models[0]['id'] ) ) {
						$models = self::get_default_stability_ai_models();
					} else {
						$new_models = array();
						foreach ( $models as $k => $v ) {
							if ( ! empty( $v['id'] ) ) {
								$new_models[ $v['id'] ] = $v;
								unset( $new_models[ $v['id'] ]['id'] );
							}
						}
						$models = $new_models;
					}
				}
			}
			return $models;
		}

		/**
		 * Return a default list of upscalers for Stability AI
		 * 
		 * @access public
		 * @static
		 * 
		 * @return array  	  The list of upscalers for Stability AI
		 */
		static function get_default_stability_ai_upscalers() {
			return apply_filters( 'trx_addons_filter_ai_helper_default_stability_ai_upscalers', array(
				// Legacy model, not supported now
				// 'upscale-stable-diffusion-x4-latent-upscaler' => array( 
				// 	'title' => esc_html__( 'Stability AI Default Upscaler', 'trx_addons' ),
				// 	'model_id' => 'stable-diffusion-x4-latent-upscaler'
				// ),
				'upscale-esrgan-v1-x2plus' => array( 
					'title' => esc_html__( 'Stability AI esrgan-v1-x2plus', 'trx_addons' ),
					'model_id' => 'esrgan-v1-x2plus'
				),
			) );
		}

		/**
		 * Return a list of upscalers for Stability AI with max tokens for each model
		 * 
		 * @access public
		 * @static
		 * 
		 * @return array  	  The list of upscalers for Stability AI
		 */
		static function get_stability_ai_upscalers() {
			$upscalers = array();
			$token = trx_addons_get_option( 'ai_helper_token_stability_ai', '' );
			if ( ! empty( $token ) ) {
				$upscalers = trx_addons_get_option( 'ai_helper_upscalers_stability_ai', '' );
				if ( empty( $upscalers ) || ! is_array( $upscalers ) || empty( $upscalers[0]['id'] ) ) {
					$upscalers = self::get_default_stability_ai_upscalers();
				} else {
					$new_upscalers = array();
					foreach ( $upscalers as $k => $v ) {
						if ( ! empty( $v['id'] ) ) {
							$new_upscalers[ $v['id'] ] = $v;
							unset( $new_upscalers[ $v['id'] ]['id'] );
						}
					}
					$upscalers = $new_upscalers;
				}
			}
			return $upscalers;
		}

		/**
		 * Return a list of text languages for AI translations
		 * 
		 * @access public
		 * @static
		 * 
		 * @return array  	  The list of languages
		 */
		static function get_list_stability_ai_styles() {
			return apply_filters( 'trx_addons_filter_ai_helper_list_stability_ai_styles', array(
				'' => esc_html__( 'None', 'trx_addons' ),
				'3d-model' => esc_html__( '3D model', 'trx_addons' ),
				'analog-film' => esc_html__( 'Analog Film', 'trx_addons' ),
				'anime' => esc_html__( 'Anime', 'trx_addons' ),
				'cinematic' => esc_html__( 'Cinematic', 'trx_addons' ),
				'comic-book' => esc_html__( 'Comic Book', 'trx_addons' ),
				'digital-art' => esc_html__( 'Digital Art', 'trx_addons' ),
				'enhance' => esc_html__( 'Enhance', 'trx_addons' ),
				'fantasy-art' => esc_html__( 'Fantasy Art', 'trx_addons' ),
				'isometric' => esc_html__( 'Isometric', 'trx_addons' ),
				'line-art' => esc_html__( 'Line Art', 'trx_addons' ),
				'low-poly' => esc_html__( 'Low Poly', 'trx_addons' ),
				'modeling-compound' => esc_html__( 'Modeling Compound', 'trx_addons' ),
				'neon-punk' => esc_html__( 'Neon Punk', 'trx_addons' ),
				'origami' => esc_html__( 'Origami', 'trx_addons' ),
				'photographic' => esc_html__( 'Photographic', 'trx_addons' ),
				'pixel-art' => esc_html__( 'Pixel Art', 'trx_addons' ),
				'tile-texture' => esc_html__( 'Tile Texture', 'trx_addons' ),
			) );
		}
	}
}
