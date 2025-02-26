<?php

namespace ThemeRex\StabilityAi;

use ThemeRex\Ai\Api;

class Images extends Api {

	private static $api_server  = "https://api.stability.ai";
	private static $site_server = "https://platform.stability.ai";

	public function __construct( $api_key )	{
		parent::__construct( $api_key );

		$this->setHeaders( array( 'Accept: application/json' ) );
	}

	/**
	 * Return a base URL to the vendor site
	 * 
	 * @param string $endpoint  The endpoint to use
	 * @param string $type  The type of the URL: api, site. Default: api
	 * 
	 * @return string  The URL to the vendor site
	 */
	public static function baseUrl( $endpoint = '', $type = 'api' ) {
		return ( $type == 'api' ? self::$api_server : self::$site_server ) . ( ! empty( $endpoint ) ? "/{$endpoint}" : '' );
	}

	/**
	 * Return an URL to the API
	 * 
	 * @param string $engine    The engine to use. For image generation, use the model ID.
	 * @param string $endpoint  The endpoint to use: text-to-image, image-to-image
	 * 
	 * @return string  The URL to the API
	 */
	public function apiUrl( $engine, $endpoint ) {
		return self::baseUrl( "v1/{$engine}/{$endpoint}" );
	}

	private function checkArgs( $args ) {
		return apply_filters( 'trx_addons_filter_ai_helper_check_args', $args, 'stability-ai' );
	}

	/**
	 * Return a list of available models
	 * 
	 * @return bool|string  The response from the API
	 */
	public function listModels( $opts = array() ) {
		$url = $this->apiUrl( 'engines', 'list' );
		return $this->sendRequest( $url, 'GET', array( 'key' => ! empty( $opts['key'] ) ? $opts['key'] : $this->api_key ) );
	}

	/**
	 * Generate an image from a text prompt
	 * 
	 * @param array $opts  The options for the request
	 * 
	 * @return bool|string  The response from the API
	 */
	public function textToImage( $opts ) {
		// Get the API URL
		$url = $this->apiUrl( "generation/{$opts['model_id']}", 'text-to-image' );
		// Send the request
		return $this->sendRequest( $url, 'POST', $this->checkArgs( $opts ) );
	}

	/**
	 * Generate an image from another image
	 * 
	 * @param array $opts  The options for the request
	 * 
	 * @return bool|string  The response from the API
	 */
	public function imageToImage( $opts ) {
		// Get the image from the URL
		if ( ! empty( $opts['init_image'] ) ) {
			$opts['content_type'] = 'multipart/form-data';
			$opts['init_image'] = curl_file_create( $opts['init_image'] );
		}
		// Get the API URL
		$url = $this->apiUrl( "generation/{$opts['model_id']}", 'image-to-image' );
		// Remove unnessesary parameters
		unset( $opts['model_id'] );
		// Send the request
		return $this->sendRequest( $url, 'POST', $this->checkArgs( $opts ) );
	}

	/**
	 * Upscale an image
	 * 
	 * @param array $opts  The options for the request
	 * 
	 * @return bool|string  The response from the API
	 */
	public function imageUpscale( $opts ) {
		// Get the image from the URL
		if ( ! empty( $opts['init_image'] ) ) {
			$opts['content_type'] = 'multipart/form-data';
			$opts['image'] = curl_file_create( $opts['init_image'] );
			unset( $opts['init_image'] );
		}
		// Get the API URL
		$url = $this->apiUrl( "generation/{$opts['model_id']}", 'image-to-image/upscale' );
		// Remove unnessesary parameters
		unset( $opts['model_id'] );
		// Send the request
		return $this->sendRequest( $url, 'POST', $this->checkArgs( $opts ) );
	}

}
