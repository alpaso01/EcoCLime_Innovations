<?php
namespace TrxAddons\AiHelper;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class to make queries to the Stable Diffusion API
 */
class LumaLabs extends Api {

	/**
	 * Plugin constructor.
	 *
	 * @access protected
	 */
	protected function __construct() {
		parent::__construct();
		$this->logger_section = 'lumalabs-ai';
		$this->token_option = 'ai_helper_token_lumalabs_ai';
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
		return \ThemeRex\LumaLabs\Video::baseUrl( $endpoint, $type );
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
				$this->api = new \ThemeRex\LumaLabs\Video( $token );
			}
		}
		return $this->api;
	}

	/**
	 * Prepare arguments for the API format
	 * 
	 * @access protected
	 * 
	 * @param array $args   Arguments to prepare
	 * @param string $type  The type of the arguments. Default: video
	 * 
	 * @return array  Prepared arguments
	 */
	protected function prepare_args( $args, $type = 'video' ) {
        //Model type/name => name
        if ( ! empty( $args['model'] ) ) {
            $args['model'] = str_replace( 'lumalabs-ai/', '', $args['model'] );
        }
		return $args;
	}

	/**
	 * Generate video via API
	 *
	 * @access public
	 * 
	 * @param array $args  Query arguments
	 * 
	 * @return array  Response from the API
	 */
	public function generate_video( $args = array() ) {
		$args = array_merge( array(
            'generation_type' => 'video',
			'prompt' => '',
		), $args );

		// Save a model name for the log
		$model = str_replace( 'lumalabs-ai/', '', ! empty( $args['model'] ) ? $args['model'] : 'lumalabs-ai/ray-1-6' );
		$args_orig = $args;

		// Prepare arguments for SD API format
		$args = $this->prepare_args( $args, 'video' );

		$response = false;

		if ( ! empty( $args['prompt'] ) ) {

			$api = $this->get_api();

			$response = $api->videoGen( $args );

			if ( is_array( $response ) ) {
				$this->logger->log( $response, $model, $args_orig, $this->logger_section );
			} else {
				$response = false;
			}
		}
		return $response;

	}

	/**
	 * Fetch queued video via API
	 *
	 * @access public
	 * 
	 * @param array $args   Query arguments
	 * @param string $type  The type of the fetch. Default: video
	 * 
	 * @return array  Response from the API
	 */
	public function fetch_video( $args = array(), $type = 'video' ) {
		$args = array_merge( array(
			'token' => $this->get_token(),
			'fetch_id' => '',
		), $args );

		// Prepare arguments for LumaLabs API format
		$args = $this->prepare_args( $args, $type );

		$response = false;

		if ( ! empty( $args['fetch_id'] ) ) {

			$api = $this->get_api();

			$response = $api->fetchVideo( $args );

			if ( ! is_array( $response ) ) {
				$response = false;
			}
		}
	
		return $response;

	}

}
