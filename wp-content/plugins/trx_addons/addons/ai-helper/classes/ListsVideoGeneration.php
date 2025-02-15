<?php
namespace TrxAddons\AiHelper;

if ( ! trait_exists( 'ListsVideoGeneration' ) ) {

	/**
	 * Return arrays with the lists used for the video generation
	 */
	trait ListsVideoGeneration {

		/**
		 * Return a list of video generation APIs
		 * 
		 * @access public
		 * @static
		 * 
		 * @return array  	  The list of video generation APIs
		 */
		static function get_list_ai_video_apis() {
			return apply_filters( 'trx_addons_filter_ai_helper_list_ai_video_apis', array(
				'lumalabs-ai' => esc_html__( 'LumaLabs AI', 'trx_addons' ),
			) );
		}

        /**
		 * Return a default list of video APIs with the enabled status
		 * 
		 * @access public
		 * @static
		 * 
		 * @return array  	  The default list of video APIs
		 */
		static function get_list_ai_video_apis_enabled() {
			$api_list = self::get_list_ai_video_apis();
			if ( ! is_array( $api_list ) ) {
				$api_list = array();
			}
			foreach( $api_list as $api => $title ) {
				$api_list[ $api ] = 1;
			}
			return $api_list;
		}

		/**
		 * Return a list of video generator layouts
		 * 
		 * @access public
		 * @static
		 * 
		 * @return array  	  The list of video generator layouts
		 */
		static function get_list_sc_video_generator_layouts() {
			return apply_filters( 'trx_addons_filter_ai_helper_list_sc_video_generator_layouts', array(
				'default'  => esc_html__( 'Simple', 'trx_addons' ),
			) );
		}

		/**
		 * Return a list of video models for AI
		 * 
		 * @access public
		 * @static
		 * 
		 * @return array  	  The list of video models
		 */
		static function get_list_ai_video_models( $groups = true ) {
			$api_order = trx_addons_get_option( 'ai_helper_sc_vgenerator_api_order', self::get_list_ai_video_apis_enabled() );
			$models = array();
			foreach( $api_order as $api => $enable ) {
				// Open AI
				if ( $api == 'lumalabs-ai' && (int)$enable > 0 ) {
					$la_models = self::get_la_models();
					if ( is_array( $la_models ) && count( $la_models ) > 0 ) {
						if ( $groups ) {
							$models[ 'lumalabs-ai/-' ] = '\\-' . __( 'LumaLabs AI models', 'trx_addons' );
						}
						foreach ( $la_models as $k => $v ) {
							$models[ 'lumalabs-ai/' . $k ] = $v['title'];
						}
					}
				}
			}
			return apply_filters( 'trx_addons_filter_ai_helper_list_ai_video_models', $models );
		}

		/**
		 * Return a list of video aspect ratios for AI
		 * 
		 * @access public
		 * @static
		 * 
		 * @param string $api  The name of API:
		 *						'all' - all APIs,
		 *						'lumalabs-ai' - LumaLabs APIs,
		 * 
		 * @return array  	  The list of video aspect ratios
		 */
		static function get_list_ai_video_ar( $api = 'all' ) {
			$la_enable = trx_addons_get_option( 'ai_helper_token_lumalabs_ai', '' ) != '';
			return apply_filters( 'trx_addons_filter_ai_helper_list_ai_video_ar', array_merge(
					// LumaLabs API
					in_array( $api, array( 'all', 'lumalabs-ai' ) ) && $la_enable
						? array(
							'1:1'  => esc_html__( '1 : 1', 'trx_addons' ),
							'16:9' => esc_html__( '16 : 9', 'trx_addons' ),
							'9:16' => esc_html__( '9 : 16', 'trx_addons' ),
							'4:3'  => esc_html__( '4 : 3', 'trx_addons' ),
							'3:4'  => esc_html__( '3 : 4', 'trx_addons' ),
							'21:9' => esc_html__( '21 : 9', 'trx_addons' ),
							'9:21' => esc_html__( '9 : 21', 'trx_addons' ),
							)
						: array(),
					),
					$api
				);
		}

		/**
		 * Return a list of video resolutions for AI
		 * 
		 * @access public
		 * @static
		 * 
		 * @param string $api  The name of API:
		 *						'all' - all APIs,
		 *						'lumalabs-ai' - LumaLabs APIs,
		 * 
		 * @return array  	  The list of resolutions
		 */
		static function get_list_ai_video_resolutions( $api = 'all' ) {
			$la_enable = trx_addons_get_option( 'ai_helper_token_lumalabs_ai', '' ) != '';
			return apply_filters( 'trx_addons_filter_ai_helper_list_ai_video_resolutions', array_merge(
					// LumaLabs API for models >= Ray 2.0
					in_array( $api, array( 'all', 'lumalabs-ai' ) ) && $la_enable
						? array(
							'540p' => esc_html__( '540p', 'trx_addons' ),
							'720p' => esc_html__( '720p', 'trx_addons' ),
							)
						: array(),
					),
					$api
				);
		}

		/**
		 * Return a list of video durations for AI
		 * 
		 * @access public
		 * @static
		 * 
		 * @param string $api  The name of API:
		 *						'all' - all APIs,
		 *						'lumalabs-ai' - LumaLabs APIs,
		 * 
		 * @return array  	  The list of durations
		 */
		static function get_list_ai_video_durations( $api = 'all' ) {
			$la_enable = trx_addons_get_option( 'ai_helper_token_lumalabs_ai', '' ) != '';
			return apply_filters( 'trx_addons_filter_ai_helper_list_ai_video_durations', array_merge(
					// LumaLabs API for models >= Ray 2.0
					in_array( $api, array( 'all', 'lumalabs-ai' ) ) && $la_enable
						? array(
							'5s' => esc_html__( '5s', 'trx_addons' ),
							'9s' => esc_html__( '9s', 'trx_addons' ),
							)
						: array(),
					),
					$api
				);
		}

		/**
		 * Returns a list of models that are allowed to use the video resolutions
		 *
		 * @return array	The list of models
		 */
		static function get_list_models_for_access_ai_video_resolution() {
			return apply_filters( 'trx_addons_filter_ai_helper_list_models_for_access_ai_video_resolution', array(
				'lumalabs-ai/ray-2'
			) );
		}

		/**
		 * Returns a list of models that are allowed to use the video durations
		 *
		 * @return array	The list of models
		 */
		static function get_list_models_for_access_ai_video_duration() {
			return apply_filters( 'trx_addons_filter_ai_helper_list_models_for_access_ai_video_duration', array(
				'lumalabs-ai/ray-2'
			) );
		}

		/**
		 * Returns a list of models that are allowed to use the video keyframes
		 *
		 * @return array	The list of models
		 */
		static function get_list_models_for_access_ai_video_keyframes() {
			return apply_filters( 'trx_addons_filter_ai_helper_list_models_for_access_ai_video_keyframes', array(
				'lumalabs-ai/ray-1-6'
			) );
		}



		/* LUMALABS API
		--------------------------------------------------------------------------------------- */

		/**
		 * Return a default list of video models for LumaLabs
		 * 
		 * @access public
		 * @static
		 * 
		 * @return array  	  The list of video models for LumaLabs
		 */
		static function get_default_lumalabs_ai_models() {
			return apply_filters( 'trx_addons_filter_ai_helper_default_lumalabs_ai_models', array(
				'ray-1-6' => array(
					'title' => esc_html__( 'Ray 1.6', 'trx_addons' )
				),
				'ray-2' => array(
					'title' => esc_html__( 'Ray 2.0', 'trx_addons' )
				),
			) );
		}

		/**
		 * Return a list of video models for LumaLabs
		 * 
		 * @access public
		 * @static
		 * 
		 * @return array  	  The list of video models for LumaLabs
		 */
		static function get_la_models() {
			$models = array();
			$token = trx_addons_get_option( 'ai_helper_token_lumalabs_ai', '' );
			if ( ! empty( $token ) ) {
				$models = trx_addons_get_option( 'ai_helper_models_lumalabs_ai' );
				if ( empty( $models ) || ! is_array( $models ) || empty( $models[0]['id'] ) ) {
					$models = self::get_default_lumalabs_ai_models();
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

			return $models;
		}
    }
}