<?php

namespace ThemeRex\LumaLabs;

use ThemeRex\Ai\Api;

class Video extends Api {

	private static $api_server  = "https://api.lumalabs.ai";	// URL to the API server
	private static $site_server = "https://api.lumalabs.ai";	// URL to the site server

	public function __construct( $api_key = '' )	{
		parent::__construct( $api_key );

		$this->setAuthMethod( 'Bearer' );
		$this->setHeaders( array( 'Accept: application/json' ) );
        $this->setTimeout( 30 );
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
	 * @return string  The URL to the API
	 */
	public function apiUrl( $endpoint ) {
		return self::baseUrl( "dream-machine/{$endpoint}" );
	}

	private function checkArgs( $args ) {
		return apply_filters( 'trx_addons_filter_ai_helper_check_args', $args, 'lumalabs-ai/video' );
	}

	/**
	 * Generate video based on input parameters ( Video Generation )
	 * 
	 * @param array $opts  The options for the request
	 * 
	 * @return bool|string  The response from the API
	 */
	public function videoGen( $opts ) {
		$url = $this->apiUrl( 'v1/generations' );
		return $this->sendRequest( $url, 'POST', $this->checkArgs( $opts ) );
	}

	/**
	 * Fetch queued video.
	 * 
	 * @param array $opts  The options for the request
	 * 
	 * @return bool|string  The response from the API
	 */
	public function fetchVideo( $opts ) {
		$url = $this->apiUrl( "v1/generations/{$opts['fetch_id']}" );
		unset( $opts['fetch_id'] );
		return $this->sendRequest( $url, 'GET' );
	}
}
