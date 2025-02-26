<?php

namespace ThemeRex\Ai;

use Exception;

class Api {

	protected $api_key = "";
	protected $api_host = "";

	private $headers = array();
	private $auth_method = "Bearer";		// Authentification method: Bearer, Header, Url, Argument
	private $auth_param = "key";
	private $stream_method;
	private $timeout = 0;
	private $proxy = "";
	private $proxy_auth = "";

	public function __construct( $api_key, $api_host = "" )	{
		$this->api_key = $api_key;
		if ( ! empty( $api_host ) ) {
			$this->api_host = untrailingslashit( $api_host );
		}
	}

	/**
	 * Set a new timeout for API requests
	 * 
	 * @param  int  $timeout  The timeout in seconds
	 */
	public function setTimeout( int $timeout ) {
		$this->timeout = $timeout;
	}

	/**
	 * Set an authentification method:
	 * - Bearer: use the API key as a bearer token in the header entry "Authorization: Bearer XXX". Default
	 * - Header: use the API key as a value of the header entry "auth_param: XXX"
	 * - Url: add the API key to the URL as a query parameter "url?auth_param=XXX"
	 * - Argument: add the API key to the request arguments as a key "auth_param=XXX"
	 * 
	 * @param  string  $auth_method  The authentification method
	 * @param  string  $auth_param   The name of the parameter for the authentification method
	 */
	public function setAuthMethod( string $auth_method, string $auth_param = '' ) {
		$this->auth_method = $auth_method;
		if ( ! empty( $auth_param ) ) {
			$this->auth_param = $auth_param;
		}
	}

	/**
	 * Set an object as a stream method
	 * 
	 * @param  object  $stream  The stream method
	 */
	public function setStreamMethod( object $stream ) {
		$this->stream_method = $stream;
	}

	/**
	 * Set an URL of proxy server for API requests
	 * 
	 * @param  string  $proxy  The URL of proxy server
	 * @param  string  $auth   The login:password to access to the proxy server
	 */
	public function setProxy( string $proxy, string $auth = '' ) {
		// if ( ! empty( $proxy ) && strpos( $proxy, '://' ) === false ) {
		// 	$proxy = 'https://' . $proxy;
		// }
		$this->proxy = $proxy;
		$this->proxy_auth = $auth;
	}

	/**
	 * @param  array  $headers
	 */
	public function setHeaders( array $headers ) {
		if ( is_array( $headers ) && count( $headers ) > 0 ) {
			$this->headers = array_merge( $this->headers, $headers );
		}
	}

	/**
	 * Send request to the API
	 * 
	 * @param  string  $url     The URL of API request
	 * @param  string  $method  The method of API request: GET, POST, PUT, DELETE
	 * @param  array   $opts    The additional options (data for the POST/PUT requests) for API request
	 * @param  bool    $decodeResponse  Decode response from JSON to array
	 * 
	 * @return bool|array The response from the API
	 */
	protected function sendRequest( string $url, string $method, array $opts = [], $decodeResponse = true ) {
		// Get a key
		$key = $this->api_key;
		if ( ! empty( $opts['key'] ) ) {
			$key = $opts['key'];
			unset( $opts['key'] );
		}
		if ( empty( $key ) ) {
			throw new Exception( 'API key is missing' );
		}

		// Get a content type
		$type = 'application/json';
		if ( ! empty( $opts['content_type'] ) ) {
			$type = $opts['content_type'];
			unset( $opts['content_type'] );
		}

		// Get a headers
		$headers = $this->headers;
		$headers[] = "Content-Type: {$type}";

		// Add authentification method
		if ( ! empty( $this->auth_method ) ) {
			switch ( $this->auth_method ) {
				case 'Bearer':
					$headers[] = "Authorization: Bearer {$key}";
					break;
				case 'Header':
					$headers[] = "{$this->auth_param}: {$key}";
					break;
				case 'Url':
					$url = trx_addons_add_to_url( $url, array( $this->auth_param => $key ) );
					break;
				case 'Argument':
					if ( ! isset( $opts[$this->auth_param] ) ) {
						$opts[ $this->auth_param ] = $key;
					}
					break;
			}
		}

		// Prepare query arguments
		$curl_info = [
			CURLOPT_URL            => $url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING       => '',
			CURLOPT_MAXREDIRS      => 10,
			CURLOPT_TIMEOUT        => $this->timeout,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
			CURLOPT_SSL_VERIFYPEER => 0,
			CURLOPT_SSL_VERIFYHOST => 0,
			CURLOPT_CUSTOMREQUEST  => $method,
			CURLOPT_HTTPHEADER     => $headers,
		];

		if ( in_array( $method, array( 'POST', 'PUT' ) ) && ! empty( $opts ) ) {
			$curl_info[CURLOPT_POSTFIELDS] = $type == 'application/json'
												? json_encode( $opts )
												: $opts;
		}

		if ( ! empty( $this->proxy ) ) {
			$curl_info[ CURLOPT_PROXY ] = $this->proxy;
			$curl_info[ CURLOPT_HTTPPROXYTUNNEL ] = 1;
			$curl_info[ CURLOPT_PROXYTYPE ] = strpos( $this->proxy, 'https://' ) !== false ? CURLPROXY_HTTPS : CURLPROXY_HTTP;	// CURLPROXY_HTTP, CURLPROXY_HTTPS, CURLPROXY_SOCKS4, CURLPROXY_SOCKS5, CURLPROXY_SOCKS4A, CURLPROXY_SOCKS5_HOSTNAME
			$curl_info[ CURLOPT_PROXY_SSL_VERIFYPEER ] = 0;
			$curl_info[ CURLOPT_PROXY_SSL_VERIFYHOST ] = 0;
			if ( ! empty( $this->proxy_auth ) ) {
				$curl_info[ CURLOPT_PROXYUSERPWD ] = $this->proxy_auth;
			}
		}

		if ( array_key_exists( 'stream', $opts ) && $opts['stream'] ) {
			$curl_info[ CURLOPT_WRITEFUNCTION ] = $this->stream_method;
		}

		$curl = curl_init();

		curl_setopt_array( $curl, $curl_info );

		$response = curl_exec( $curl );

		curl_close( $curl );

		if ( $decodeResponse && is_string( $response ) && ( substr( $response, 0, 1 ) == '{' || substr( $response, 0, 2 ) == '[{' ) ) {
			$response = json_decode( $response, true );
		}

		return $response;
	}
}
