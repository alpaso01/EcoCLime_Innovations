<?php

namespace ThemeRex\GoogleAi;

use ThemeRex\Ai\Api;

class Gemini extends Api {

	public function __construct( $api_key )	{
		parent::__construct( $api_key );

		$this->setAuthMethod( 'Url', 'key' );
		$this->setHeaders( array( 'Accept: application/json' ) );
	}

	/**
	 * Return an URL to the API
	 * 
	 * @param string $model  The model ID
	 * 
	 * @return string  The URL to the API
	 */
	public function apiUrl( $model = '', $action = 'generateContent' ) {
		return "https://generativelanguage.googleapis.com/v1beta/models"
				. ( ! empty( $model )
					? "/{$model}" . ( ! empty( $action ) ? ":{$action}" : '' )
					: ''
					);
	}

	/**
	 * Check and prepare the arguments for the request
	 * 
	 * @param array $args  The arguments for the request
	 * 
	 * @return array  The arguments for the request
	 */
	private function checkArgs( $args ) {
		unset( $args['system_prompt'] );
		unset( $args['model'] );
		unset( $args['token'] );
		unset( $args['max_tokens'] );
		unset( $args['n'] );
		return apply_filters( 'trx_addons_filter_ai_helper_check_args', $args, 'gemini' );
	}

	/**
	 * Get a list of models
	 * 
	 * @return array  The response from the API
	 */
	public function listModels() {
		// Get the API URL
		$url = $this->apiUrl();
		// Send the request
		return $this->sendRequest( $url, 'GET' );
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
