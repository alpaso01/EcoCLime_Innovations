<?php
namespace TrxAddons\AiHelper;

if ( ! trait_exists( 'ListsMusicGeneration' ) ) {

	/**
	 * Return arrays with the lists used for the music generation
	 */
	trait ListsMusicGeneration {

		/**
		 * Return a list of music generation APIs
		 * 
		 * @access public
		 * @static
		 * 
		 * @return array  	  The list of music generation APIs
		 */
		static function get_list_ai_music_apis() {
			return apply_filters( 'trx_addons_filter_ai_helper_list_ai_music_apis', array(
				'modelslab' => esc_html__( 'ModelsLab', 'trx_addons' ),
			) );
		}

		/**
		 * Return a default list of music APIs with the enabled status
		 * 
		 * @access public
		 * @static
		 * 
		 * @return array  	  The default list of music APIs
		 */
		static function get_list_ai_music_apis_enabled() {
			$api_list = self::get_list_ai_music_apis();
			if ( ! is_array( $api_list ) ) {
				$api_list = array();
			}
			foreach( $api_list as $api => $title ) {
				$api_list[ $api ] = 1;
			}
			return $api_list;
		}

		/**
		 * Return a list of music models for AI
		 * 
		 * @access public
		 * @static
		 * 
		 * @return array  	  The list of music models
		 */
		static function get_list_ai_music_models( $groups = true ) {
			$api_order = trx_addons_get_option( 'ai_helper_sc_mgenerator_api_order', self::get_list_ai_music_apis_enabled() );
			$models = array();
			foreach( $api_order as $api => $enable ) {
				// ModelsLab
				if ( $api == 'modelslab' && (int)$enable > 0 ) {
					$ml_models = self::get_modelslab_music_models();
					if ( is_array( $ml_models ) && count( $ml_models ) > 0 ) {
						if ( $groups ) {
							$models[ 'modelslab/-' ] = '\\-' . __( 'ModelsLab models', 'trx_addons' );
						}
						foreach ( $ml_models as $k => $v ) {
							$models[ 'modelslab/' . $k ] = $v['title'];
						}
					}
				}
			}
			return apply_filters( 'trx_addons_filter_ai_helper_list_ai_music_models', $models );
		}

		/**
		 * Return a list of music generator layouts
		 * 
		 * @access public
		 * @static
		 * 
		 * @return array  	  The list of music generator layouts
		 */
		static function get_list_sc_music_generator_layouts() {
			return apply_filters( 'trx_addons_filter_ai_helper_list_sc_music_generator_layouts', array(
				'default'  => esc_html__( 'Simple', 'trx_addons' ),
				'extended' => esc_html__( 'Extended', 'trx_addons' ),
			) );
		}



		/* MODELSLAB API
		--------------------------------------------------------------------------------------- */

		/**
		 * Return a default list of music models for ModelsLab
		 * 
		 * @access public
		 * @static
		 * 
		 * @return array  	  The list of music models for ModelsLab
		 */
		static function get_default_modelslab_music_models() {
			return apply_filters( 'trx_addons_filter_ai_helper_default_modelslab_music_models', array(
				'music-generator' => array(
					'title' => esc_html__( 'Music Generator', 'trx_addons' )
				),
			) );
		}

		/**
		 * Return a list of music models for ModelsLab
		 * 
		 * @access public
		 * @static
		 * 
		 * @return array  	  The list of music models for ModelsLab
		 */
		static function get_modelslab_music_models() {
			$models = array();
			$token = trx_addons_get_option( 'ai_helper_token_modelslab', '' );
			if ( ! empty( $token ) ) {
				$models = trx_addons_get_option( 'ai_helper_music_models_modelslab', false );
				if ( empty( $models ) || ! is_array( $models ) || empty( $models[0]['id'] ) ) {
					$models = self::get_default_modelslab_music_models();
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
