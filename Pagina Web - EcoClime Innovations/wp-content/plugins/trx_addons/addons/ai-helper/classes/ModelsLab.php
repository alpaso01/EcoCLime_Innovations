<?php
namespace TrxAddons\AiHelper;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class to make queries to the Stable Diffusion API
 */
class ModelsLab extends Api {

	/**
	 * Plugin constructor.
	 *
	 * @access protected
	 */
	protected function __construct() {
		parent::__construct();
		$this->logger_section = 'modelslab';
		$this->token_option = 'ai_helper_token_stable_diffusion';
	}

	/**
	 * Return a base URL to the vendor site
	 * 
	 * @param string $endpoint  The endpoint to use
	 * @param string $type      The type of the URL: api or site. Default: site
	 * 
	 * @return string  The URL to the vendor site
	 */
	public function get_url( $endpoint = '', $type = 'site' ) {
		return \ThemeRex\ModelsLab\Audio::baseUrl( $endpoint, $type );
	}

	/**
	 * Return an object of the API
	 * 
	 * @param string $token  API token for the API
	 * 
	 * @return api  The object of the API
	 */
	public function get_api( $token = '' ) {
		if ( empty( $this->api ) ) {
			if ( empty( $token ) ) {
				$token = $this->get_token();
			}
			if ( ! empty( $token ) ) {
				$this->api = new \ThemeRex\ModelsLab\Audio( $token );
			}
		}
		return $this->api;
	}

	/**
	 * Return a sampling rate for the API
	 * 
	 * @access protected
	 * 
	 * @return int  Sampling rate for the API
	 */
	protected function get_sampling_rate() {
		return 32000;
	}

	/**
	 * Return max new tokens for the API
	 * 
	 * @access protected
	 * 
	 * @return int  max new tokens for the API
	 */
	protected function get_max_new_token() {
		return 256;
	}

	/**
	 * Prepare arguments for the API format
	 * 
	 * @access protected
	 * 
	 * @param array $args   Arguments to prepare
	 * @param string $type  The type of the arguments. Default: audio
	 * 
	 * @return array  Prepared arguments
	 */
	protected function prepare_args( $args, $type = 'audio' ) {
		// token => key
		if ( ! isset( $args['key'] ) ) {
			$args['key'] = $args['token'];
			unset( $args['token'] );
		}
		// model => model_id
		if ( ! isset( $args['model_id'] ) && isset( $args['model'] ) ) {
			$args['model_id'] = $args['model'];
			unset( $args['model'] );
		}
		if ( ! empty( $args['model_id'] ) ) {
			$args['model_id'] = str_replace(
				array( 'modelslab/default', 'modelslab/' ),
				array( '', '' ),
				$args['model_id']
			);
		}
		if ( empty( $args['model_id'] ) ) {
			unset( $args['model_id'] );
		}
		// audio => init_audio
		if ( ! isset( $args['init_audio'] ) && isset( $args['audio'] ) ) {
			$args['init_audio'] = $args['audio'];
			unset( $args['audio'] );
		}
		// voice => voice_id
		if ( ! isset( $args['voice_id'] ) && isset( $args['voice'] ) ) {
			$args['voice_id'] = $args['voice'];
			unset( $args['voice'] );
		}
		return $args;
	}

	/**
	 * Generate music via API
	 *
	 * @access public
	 * 
	 * @param array $args  Query arguments
	 * 
	 * @return array  Response from the API
	 */
	public function generate_music( $args = array() ) {
		$args = array_merge( array(
			'token' => $this->get_token(),
			'prompt' => '',
			'max_new_token' => (int)$this->get_max_new_token(),
			'sampling_rate' => (int)$this->get_sampling_rate(),
		), $args );

		// Save a model name for the log
		$model = str_replace( 'modelslab/', '', ! empty( $args['model'] ) ? $args['model'] : 'modelslab/default' );
		$args_orig = $args;

		// Prepare arguments for SD API format
		$args = $this->prepare_args( $args, 'music' );

		$response = false;

		if ( ! empty( $args['key'] ) && ! empty( $args['prompt'] ) ) {

			$api = $this->get_api( $args['key'] );

			$response = $api->musicGen( $args );

			if ( is_array( $response ) ) {
				$this->logger->log( $response, $model, $args_orig, $this->logger_section . '/audio' );
			} else {
				$response = false;
			}
		}
		return $response;

	}

	/**
	 * Fetch queued music via API
	 *
	 * @access public
	 * 
	 * @param array $args   Query arguments
	 * @param string $type  The type of the fetch. Default: music
	 * 
	 * @return array  Response from the API
	 */
	public function fetch_music( $args = array(), $type = 'music' ) {
		$args = array_merge( array(
			'token' => $this->get_token(),
			'fetch_url' => '',
			'fetch_id' => '',
		), $args );

		// Prepare arguments for ModelsLab API format
		$args = $this->prepare_args( $args, $type );

		$response = false;

		if ( ! empty( $args['key'] ) && ! empty( $args['fetch_id'] ) ) {

			$api = $this->get_api( $args['key'] );

			$response = $api->fetchAudio( $args );

			if ( ! is_array( $response ) ) {
				$response = false;
			}
		}
	
		return $response;

	}

	/**
	 * Generate audio via API (Text to Voice)
	 *
	 * @access public
	 * 
	 * @param array $args  Query arguments
	 * 
	 * @return array  Response from the API
	 */
	public function generate_audio( $args = array() ) {
		$args = array_merge( array(
			'token' => $this->get_token(),
			'prompt' => '',
		), $args );

		// Save a model name for the log
		$model = str_replace( 'modelslab/', '', ! empty( $args['model'] ) ? $args['model'] : 'modelslab/default' );
		$args_orig = $args;

		// Prepare arguments for ModelsLab API format
		$args = $this->prepare_args( $args, 'audio' );

		$response = array(
			'status' => 'error',
			'message' => esc_html__( 'The audio generation is failed. Try again later.', 'trx_addons' )
		);

		if ( ! empty( $args['key'] ) && ! empty( $args['prompt'] ) ) {

			$api = $this->get_api( $args['key'] );

			$response = $api->textToAudio( $args );

			if ( is_array( $response ) ) {
				$this->logger->log( $response, $model, $args_orig, $this->logger_section . '/audio' );
			} else {
				$response = false;
			}
		}
		return $response;

	}

	/**
	 * Fetch queued audio via API
	 *
	 * @access public
	 * 
	 * @param array $args   Query arguments
	 * @param string $type  The type of the fetch. Default: audio
	 * 
	 * @return array  Response from the API
	 */
	public function fetch_audio( $args = array(), $type = 'audio' ) {
		return $this->fetch_music( $args, $type );
	}

	/**
	 * Transcription audio via API (Speech To Text)
	 *
	 * @access public
	 * 
	 * @param array $args  Query arguments
	 * 
	 * @return array  Response from the API
	 */
	public function transcription( $args = array() ) {
		$args = array_merge( array(
			'token' => $this->get_token(),
		), $args );

		// Save a model name for the log
		$model = str_replace( 'modelslab/', '', ! empty( $args['model'] ) ? $args['model'] : 'modelslab/default' );
		$args_orig = $args;

		// Prepare arguments for ModelsLab API format
		$args = $this->prepare_args( $args, 'audio' );

		// Prepare arguments for the endpoint "speechToText"
		if ( empty( $args['audio'] ) && ! empty( $args['init_audio'] ) ) {
			$args['audio'] = $args['init_audio'];
			unset( $args['init_audio'] );
		}
		if ( empty( $args['input_language'] ) && ! empty( $args['language'] ) ) {
			$args['input_language'] = Utils::language_to_iso( $args['language'] );
			unset( $args['language'] );
		}

		$response = array(
			'status' => 'error',
			'message' => esc_html__( 'The audio transcription is failed. Try again later.', 'trx_addons' )
		);

		if ( ! empty( $args['key'] ) && ( ! empty( $args['audio'] ) || ! empty( $args['audio_url'] ) ) ) {

			$api = $this->get_api( $args['key'] );

			$response = $api->speechToText( $args );

			if ( is_array( $response ) ) {
				$this->logger->log( $response, $model, $args_orig, $this->logger_section . '/audio' );
			} else {
				$response = false;
			}
		}
		return $response;

	}

	/**
	 * Voice to voice audio via API (Voice To Voice and Voice Cover)
	 * If a parameter "voice_upload" is set, the audio will be generated with a voice
	 * from the uploaded file (Voice Cover). Otherwise, the audio will be generated
	 * with a voice from the parameter "voice" (Voice To Voice).
	 *
	 * @access public
	 * 
	 * @param array $args  Query arguments
	 * 
	 * @return array  Response from the API
	 */
	public function voice_cover( $args = array() ) {
		$args = array_merge( array(
			'token' => $this->get_token(),
		), $args );

		// Save a model name for the log
		$model = str_replace( 'modelslab/', '', ! empty( $args['model'] ) ? $args['model'] : 'modelslab/default' );
		$args_orig = $args;

		// Prepare arguments for ModelsLab API format
		$args = $this->prepare_args( $args, 'audio' );

		// Prepare arguments
		$mode = ! empty( $args['target_audio'] ) ? 'voice-to-voice' : 'voice-cover';
		if ( $mode == 'voice-to-voice' ) {
			unset( $args['language'], $args['emotion'], $args['speed'], $args['temperature'], $args['rate'], $args['radius'], $args['originality'] );
		} else {
			if ( ! empty( $args['voice_id'] ) ) {
				$args['model_id'] = $args['voice_id'];
				unset( $args['voice_id'] );
			}
		}

		$response = array(
			'status' => 'error',
			'message' => esc_html__( 'The audio transcription is failed. Try again later.', 'trx_addons' )
		);

		if ( ! empty( $args['key'] ) && ! empty( $args['init_audio'] ) ) {

			$api = $this->get_api( $args['key'] );

			$response = $mode == 'voice-to-voice' ? $api->voiceToVoice( $args ) : $api->voiceCover( $args );

			if ( is_array( $response ) ) {
				$this->logger->log( $response, $model, $args_orig, $this->logger_section . '/audio' );
			} else {
				$response = false;
			}
		}
		return $response;

	}

}
