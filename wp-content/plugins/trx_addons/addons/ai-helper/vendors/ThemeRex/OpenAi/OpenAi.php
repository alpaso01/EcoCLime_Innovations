<?php

namespace ThemeRex\OpenAi;

use Exception;
use ThemeRex\Ai\Api;

class OpenAi extends Api {

	private $engine = "davinci";
	private $model = "text-davinci-002";
	private $chatModel = "gpt-3.5-turbo";

	// Text to Speech
	private $speechModel = "tts-1";			// "tts-1-hd" for high quality
	private $speechVoice = "alloy";			// alloy | echo | fable | onyx | nova | shimmer
	private $speechOutputFormat = "mp3"; 	// mp3 | opus | aac | flac | wav | pcm
	private $speechSpeed = 1.0;				// 0.25 - 4.0

	// Transcription (Speech to Text)
	private $transcriptionModel = "whisper-1";
	private $transcriptionLanguage = "en";			// en | es | fr | de | it | pt | nl | pl | ru | tr | ja | ko | zh | ar | uk - language code in ISO 639-1
	private $transcriptionOutputFormat = "json";	// json | txt | srt | vtt | verbose_json

	private $customUrl = "";

	public function __construct( $api_key )	{
		parent::__construct( $api_key );
	}

	/**
	 * Set an organization ID for the API
	 * 
	 * @param  string  $org  The organization ID
	 */
	public function setOrganization( string $org ) {
		if ( ! empty( $org ) ) {
			$this->setHeaders( array( "OpenAI-Organization: {$org}" ) );
		}
	}

	/**
	 * Set a custom URL instead the default URL for the API requests
	 * 
	 * @param  string  $customUrl  The custom URL
	 */
	public function setBaseUrl( string $customUrl ) {
		if ( $customUrl != '' ) {
			$this->customUrl = $customUrl;
		}
	}

	/**
	 * Prepare a base URL for the API: if the custom URL is set, replace the default URL with it
	 * 
	 * @param  string  $url
	 */
	protected function baseUrl( string &$url ) {
		if ( $this->customUrl != "" ) {
			$url = str_replace( Url::ORIGIN, $this->customUrl, $url );
		}
		// Filter to allow the child-theme replace an URL with own server URL with local models
		$url = apply_filters( 'trx_addons_filter_ai_helper_openai_base_url', $url, Url::ORIGIN, $this->customUrl );
	}


	//============================ MODELS ============================

	/**
	 * Return a list of available models from the API
	 * 
	 * @return bool|array The response from the API
	 */
	public function listModels() {
		$url = Url::fineTuneModel();
		$this->baseUrl( $url );

		return $this->sendRequest( $url, 'GET' );
	}

	/**
	 * Get a model info from the API server
	 * 
	 * @param $model  The model name
	 * 
	 * @return bool|array  The response from the API
	 */
	public function retrieveModel( $model ) {
		$model = "/$model";
		$url   = Url::fineTuneModel() . $model;
		$this->baseUrl( $url );

		return $this->sendRequest( $url, 'GET' );
	}


	//============================ IMAGES ============================

	/**
	 * @param $opts
	 * 
	 * @return bool|array The response from the API
	 */
	public function image( $opts ) {
		$url = Url::imageUrl() . "/generations";
		$this->baseUrl( $url );

		return $this->sendRequest( $url, 'POST', $opts );
	}

	/**
	 * @param $opts
	 * 
	 * @return bool|array The response from the API
	 */
	public function imageEdit( $opts ) {
		$url = Url::imageUrl() . "/edits";
		$this->baseUrl( $url );

		// Get the image content
		if ( ! empty( $opts['image'] ) ) {
			$opts['content_type'] = 'multipart/form-data';
			$opts['image'] = curl_file_create( $opts['image'] );
		}
		if ( ! empty( $opts['mask'] ) ) {
			$opts['content_type'] = 'multipart/form-data';
			$opts['mask'] = curl_file_create( $opts['mask'] );
		}

		return $this->sendRequest( $url, 'POST', $opts );
	}

	/**
	 * @param $opts
	 * 
	 * @return bool|array The response from the API
	 */
	public function createImageVariation( $opts ) {
		$url = Url::imageUrl() . "/variations";
		$this->baseUrl( $url );

		// Get the image content
		if ( ! empty( $opts['image'] ) ) {
			$opts['content_type'] = 'multipart/form-data';
			$opts['image'] = curl_file_create( $opts['image'] );
		}

		return $this->sendRequest( $url, 'POST', $opts );
	}


	//============================ CHAT & COMPLETIONS ============================

	/**
	 * @param        $opts
	 * @param  null  $stream  The stream function
	 * 
	 * @return bool|array The response from the API
	 * 
	 * @throws Exception
	 */
	public function chat( $opts, $stream = null ) {
		if ( $stream != null && array_key_exists( 'stream', $opts ) ) {
			if ( ! $opts['stream'] ) {
				throw new Exception( __( 'Please provide a stream function.', 'trx_addons' ) );
			}

			$this->setStreamMethod( $stream );
		}

		$opts['model'] = $opts['model'] ?? $this->chatModel;
		$url           = Url::chatUrl();
		$this->baseUrl( $url );

		return $this->sendRequest( $url, 'POST', $opts );
	}

	/**
	 * @param        $opts
	 * @param  null  $stream
	 * 
	 * @return bool|array  The response from the API
	 * 
	 * @throws Exception
	 */
	public function completion( $opts, $stream = null ) {
		if ( $stream != null && array_key_exists( 'stream', $opts ) ) {
			if ( ! $opts['stream'] ) {
				throw new Exception( __( 'Please provide a stream function.', 'trx_addons' ) );
			}

			$this->setStreamMethod( $stream );
		}

		$opts['model'] = $opts['model'] ?? $this->model;
		$url           = Url::completionsURL();
		$this->baseUrl( $url );

		return $this->sendRequest( $url, 'POST', $opts );
	}


	//============================ SPEECH & AUDIO ============================

	/**
	 * @param $opts
	 * 
	 * @return bool|array The response from the API
	 */
	public function speech( $opts ) {
		$opts['model'] = $opts['model'] ?? $this->speechModel;
		$opts['voice'] = $opts['voice'] ?? $this->speechVoice;
		$opts['response_format'] = $opts['output'] ?? $this->speechOutputFormat;
		$opts['speed'] = max( 0.25, min( 4.0, $opts['speed'] ?? $this->speechSpeed ) );
		$url = Url::speechUrl();
		$this->baseUrl( $url );

		return $this->sendRequest( $url, 'POST', $opts, false );	// Don't decode the response, because it's a binary file
	}

	/**
	 * @param $opts
	 * 
	 * @return bool|array The response from the API
	 */
	public function transcription( $opts ) {
		$opts['model'] = $opts['model'] ?? $this->transcriptionModel;
		// $opts['language'] = $opts['language'] ?? $this->transcriptionLanguage;
		$opts['response_format'] = $opts['response_format'] ?? $this->transcriptionOutputFormat;
		$opts['content_type'] = 'multipart/form-data';
		$opts['file'] = curl_file_create( $opts['file'] );
		$url = Url::transcriptionsUrl();
		$this->baseUrl( $url );

		return $this->sendRequest( $url, 'POST', $opts );
	}

	/**
	 * @param $opts
	 * 
	 * @return bool|array The response from the API
	 */
	public function translate( $opts ) {
		$opts['model'] = $opts['model'] ?? $this->transcriptionModel;
		$opts['response_format'] = $opts['response_format'] ?? $this->transcriptionOutputFormat;
		$opts['content_type'] = 'multipart/form-data';
		$opts['file'] = curl_file_create( $opts['file'] );
		$url = Url::translationsUrl();
		$this->baseUrl( $url );

		return $this->sendRequest( $url, 'POST', $opts );
	}


	//============================ FILES ============================

	/**
	 * @return bool|array The response from the API
	 */
	public function listFiles() {
		$url = Url::filesUrl();
		$this->baseUrl( $url );

		return $this->sendRequest( $url, 'GET' );
	}

	/**
	 * @param $opts
	 * 
	 * @return bool|array The response from the API
	 */
	public function uploadFile( $opts ) {
		$url = Url::filesUrl();
		$this->baseUrl( $url );

		return $this->sendRequest( $url, 'POST', $opts );
	}

	/**
	 * @param $file_id
	 * 
	 * @return bool|array The response from the API
	 */
	public function retrieveFile( $file_id ) {
		$file_id = "/{$file_id}";
		$url     = Url::filesUrl() . $file_id;
		$this->baseUrl( $url );

		return $this->sendRequest( $url, 'GET', array(), false );	// Don't decode the response, because it's a binary file
	}

	/**
	 * @param $file_id
	 * 
	 * @return bool|array The response from the API
	 */
	public function retrieveFileContent( $file_id ) {
		$file_id = "/{$file_id}/content";
		$url     = Url::filesUrl() . $file_id;
		$this->baseUrl( $url );

		return $this->sendRequest( $url, 'GET' );
	}

	/**
	 * @param $file_id
	 * 
	 * @return bool|array The response from the API
	 */
	public function deleteFile( $file_id ) {
		$file_id = "/{$file_id}";
		$url     = Url::filesUrl() . $file_id;
		$this->baseUrl( $url );

		return $this->sendRequest( $url, 'DELETE' );
	}


	//============================ FINE TUNES ============================

	/**
	 * @return bool|array The response from the API
	 */
	public function listFineTunes() {
		$url = Url::fineTuneUrl();
		$this->baseUrl( $url );

		return $this->sendRequest( $url, 'GET' );
	}

	/**
	 * @param $opts
	 * 
	 * @return bool|array The response from the API
	 */
	public function createFineTune( $opts ) {
		$url = Url::fineTuneUrl();
		$this->baseUrl( $url );

		return $this->sendRequest( $url, 'POST', $opts );
	}

	/**
	 * @param $fine_tune_id
	 * 
	 * @return bool|array The response from the API
	 */
	public function retrieveFineTune( $fine_tune_id ) {
		$fine_tune_id = "/{$fine_tune_id}";
		$url          = Url::fineTuneUrl() . $fine_tune_id;
		$this->baseUrl( $url );

		return $this->sendRequest( $url, 'GET' );
	}

	/**
	 * @param $fine_tune_id
	 * 
	 * @return bool|array The response from the API
	 */
	public function cancelFineTune( $fine_tune_id ) {
		$fine_tune_id = "/{$fine_tune_id}/cancel";
		$url          = Url::fineTuneUrl() . $fine_tune_id;
		$this->baseUrl( $url );

		return $this->sendRequest( $url, 'POST' );
	}

	/**
	 * @param $fine_tune_id
	 * 
	 * @return bool|array The response from the API
	 */
	public function listFineTuneEvents( $fine_tune_id ) {
		$fine_tune_id = "/{$fine_tune_id}/events";
		$url          = Url::fineTuneUrl() . $fine_tune_id;
		$this->baseUrl( $url );

		return $this->sendRequest( $url, 'GET' );
	}

	/**
	 * @param $fine_tune_id
	 * 
	 * @return bool|array The response from the API
	 */
	public function deleteFineTune( $fine_tune_id ) {
		$fine_tune_id = "/{$fine_tune_id}";
		$url          = Url::fineTuneModel() . $fine_tune_id;
		$this->baseUrl( $url );

		return $this->sendRequest( $url, 'DELETE' );
	}
	

	//============================ MODERATION ============================

	/**
	 * @param $opts
	 * 
	 * @return bool|array The response from the API
	 */
	public function moderation( $opts ) {
		$url = Url::moderationUrl();
		$this->baseUrl( $url );

		return $this->sendRequest( $url, 'POST', $opts );
	}

	/**
	 * @param $opts
	 * 
	 * @return bool|array  The response from the API
	 */
	public function createEdit( $opts ) {
		$url = Url::editsUrl();
		$this->baseUrl( $url );

		return $this->sendRequest( $url, 'POST', $opts );
	}


	//============================ ENGINES ============================

	/**
	 * @return bool|array The response from the API
	 * 
	 * @deprecated
	 */
	public function engines() {
		$url = Url::enginesUrl();
		$this->baseUrl( $url );

		return $this->sendRequest( $url, 'GET' );
	}

	/**
	 * @param $engine
	 * 
	 * @return bool|array The response from the API
	 * 
	 * @deprecated
	 */
	public function engine( $engine ) {
		$url = Url::engineUrl( $engine );
		$this->baseUrl( $url );

		return $this->sendRequest( $url, 'GET' );
	}


	//============================ EMBEDDINGS ============================

	/**
	 * @param $opts
	 * 
	 * @return bool|array The response from the API
	 */
	public function embeddings( $opts ) {
		$url = Url::embeddings();
		$this->baseUrl( $url );

		return $this->sendRequest( $url, 'POST', $opts );
	}


	//============================ DEPRECATED (LEGACY) ============================

	/**
	 * @param $opts
	 * 
	 * @return bool|array  The response from the API
	 * 
	 * @deprecated
	 */
	public function complete( $opts ) {
		$engine = $opts['engine'] ?? $this->engine;
		$url    = Url::completionURL( $engine );
		unset( $opts['engine'] );
		$this->baseUrl( $url );

		return $this->sendRequest( $url, 'POST', $opts );
	}

	/**
	 * @param $opts
	 * 
	 * @return bool|array The response from the API
	 * 
	 * @deprecated
	 */
	public function search( $opts ) {
		$engine = $opts['engine'] ?? $this->engine;
		$url    = Url::searchURL( $engine );
		unset( $opts['engine'] );
		$this->baseUrl( $url );

		return $this->sendRequest( $url, 'POST', $opts );
	}

	/**
	 * @param $opts
	 * 
	 * @return bool|array The response from the API
	 * 
	 * @deprecated
	 */
	public function answer( $opts ) {
		$url = Url::answersUrl();
		$this->baseUrl( $url );

		return $this->sendRequest( $url, 'POST', $opts );
	}

	/**
	 * @param $opts
	 * 
	 * @return bool|array The response from the API
	 * 
	 * @deprecated
	 */
	public function classification( $opts ) {
		$url = Url::classificationsUrl();
		$this->baseUrl( $url );

		return $this->sendRequest( $url, 'POST', $opts );
	}

}
