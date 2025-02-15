<?php

namespace ThemeRex\FlowiseAi;

use ThemeRex\Ai\Api;

class Query extends Api {

	public function __construct( $api_key, $api_host )	{
		parent::__construct( $api_key, $api_host );

		$this->setHeaders( array( 'Accept: application/json' ) );
	}

	/**
	 * Return an URL to the API
	 * 
	 * @param string $chatId  The chat ID
	 * 
	 * @return string  The URL to the API
	 */
	public function apiUrl( $chatId ) {
		return "{$this->api_host}/api/v1/prediction/{$chatId}";
	}

	private function checkArgs( $args ) {
		unset( $args['model'] );
		return apply_filters( 'trx_addons_filter_ai_helper_check_args', $args, 'flowise-ai' );
	}

	/**
	 * Generate an answer for a text prompt
	 * 
	 * @param array $opts  The options for the request
	 * 
	 * @return bool|string  The response from the API
	 */
	public function query( $opts ) {
		// Get the API URL
		$url = $this->apiUrl( $opts['model'] );
		// Send the request
		return $this->sendRequest( $url, 'POST', $this->checkArgs( $opts ) );
	}

}
