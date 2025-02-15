<?php

namespace ThemeRex\ModelsLab;

use ThemeRex\Ai\Api;

class Audio extends Api {

	private static $api_server  = "https://modelslab.com";	// URL to the API server
	private static $site_server = "https://modelslab.com";	// URL to the site server

	public function __construct( $api_key = '' )	{
		parent::__construct( $api_key );

		$this->setAuthMethod( 'Argument', 'key' );
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
	 * @return string  The URL to the API
	 */
	public function apiUrl( $endpoint ) {
		return self::baseUrl( "api/{$endpoint}" );
	}

	private function checkArgs( $args ) {
		return apply_filters( 'trx_addons_filter_ai_helper_check_args', $args, 'modelslab/audio' );
	}

	/**
	 * Generate an voice audio (speech) from a text prompt ( Text to Voice / Text to Speech )
	 * 
	 * @param array $opts  The options for the request
	 * 						Parameter		Description													Values
	 * 						--------------------------------------------------------------------------------------------
	 * 						key				Your API Key used for request authorization					string
	 * 						prompt			Text prompt describing the audio you want to generate		text
	 * 						init_audio		A valid URL of the audio you want to use for voice cloning.	MP3/WAV URL (max 30 seconds)
	 * 						voice_id		Optional. A valid ID from the list of available voices.
	 * 										See list of voices.
	 * 										Note: You can either pass init_audio or voice_id.
	 * 										However, if both are passed at the same time
	 * 										the init_audio takes preference.
	 * 						language		The language of the voice. Defaults to English.				english, arabic, spanish, german, czech, chinese,
	 * 																									dutch, french, hindi, hungarian, italian, japanese, korean,
	 *		 																							polish, russian, turkish
	 * 						emotion			The desired emotion for the voice. Defaults to neutral.		neutral, happy, sad, angry, dull
	 * 						base64			Whether the input sound clip is in base64 format.			TRUE or FALSE
	 * 										Defaults to false.
	 * 						temp			Whether you want temporary links, useful if your country	TRUE or FALSE
	 * 										blocks access to certain storage sites. Defaults to false.
	 * 						webhook			URL to receive a POST API call once the audio generation	URL
	 * 										is complete.
	 * 						track_id		ID returned in the response for the webhook API call,		integral value
	 * 										used to identify the webhook request.
	 * 
	 * @return bool|string  The response from the API
	 */
	public function textToAudio( $opts ) {
		$url = $this->apiUrl( 'v6/voice/text_to_audio' );
		return $this->sendRequest( $url, 'POST', $this->checkArgs( $opts ) );
	}

	/**
	 * Generate an audio from another audio ( Voice to Voice / Voice Conversion )
	 * 
	 * @param array $opts  The options for the request:
	 * 						Parameter		Description												Values
	 * 						--------------------------------------------------------------------------------------------
	 * 						key				Your API Key used for request authorization				string
	 *						init_audio		Source utterances. Must be a valid URL or base64 data	MP3/WAV URL or base64 data
	 *										for a wav/mp3 file. Maximum length: 30 seconds.
	 *						target_audio	Target voice that replicates the original utterances.	MP3/WAV URL or base64 data
	 *										Must be a valid URL or base64 data for a wav/mp3 file.
	 *										Maximum length: 30 seconds.
	 *						base64			Whether the input sound clip is in base64 format.		TRUE or FALSE
	 *										Defaults to false.
	 *						temp			Whether you want temporary links, useful if your		TRUE or FALSE
	 *										country blocks access to certain storage sites.
	 *										Defaults to false.
	 *						webhook			URL to receive a POST API call once the audio			URL
	 *										generation is complete.
	 *						track_id		ID returned in the response for the webhook API call,	integral value
	 *										used to identify the webhook request.
	 * 
	 * @return bool|string  The response from the API
	 */
	public function voiceToVoice( $opts ) {
		$url = $this->apiUrl( 'v6/voice/voice_to_voice' );
		return $this->sendRequest( $url, 'POST', $this->checkArgs( $opts ) );
	}

	/**
	 * Create an audio from an existing audio with a model ( Voice Covering )
	 * Get all voice models https://modelslab.com/models/category/voice-cloning
	 * 
	 * @param array $opts  The options for the request:
	 * 						Parameter		Description												Values
	 * 						--------------------------------------------------------------------------------------------
	 * 						key				Your API Key used for request authorization				string
	 * 						init_audio		URL (YouTube links supported) or valid .wav file		MP3/WAV URL or base64 data
	 * 										base64 data whose audio you want to clone
	 * 						model_id		ID of the voice cloning model. Get the model ID from	string
	 * 										the provided source.
	 * 						pitch			Controls the pitch transformation between voices.		One of ["m2f", "f2m", "none"]
	 * 						algorithm		Voice cloning algorithm to use. Defaults to rmvpe.		One of ["rmvpe", "mangio-crepe"]
	 * 						rate			Rate of control for generated voice leakage.			Floating point, between 0 and 1
	 * 										Higher values bias model towards training data.	
	 * 						seed			Seed used to reproduce results. Pass null for a random	integral value
	 * 										number.
	 * 						language		Language of the voice. Default is english. Supported:	english, arabic, brazilian portuguese, chinese, dutch,
	 * 																								french, hindi, hungarian, italian, japanese, korean,
	 * 																								polish, russian, turkish
	 * 						emotion			Emotion of the voice. Defaults to neutral.				One of ["neutral", "happy", "sad", "angry", "dull"]
	 * 						speed			Speed of the speaker. Defaults to 1.0.					Floating point
	 * 						radius			Median filtering length to reduce voice artifacts.		Floating point, between 0 and 3
	 * 										Defaults to 3.
	 * 						mix				Controls loudness similarity to the original.			Floating point, between 0 and 1
	 * 										Defaults to 0.25.
	 * 						hop_length		Pitch analysis frequency. Used with mangio-crepe		Integral value
	 * 										algorithm.
	 * 						originality		Controls similarity to original vocals' voiceless		Floating point, between 0 and 1
	 * 										constants. Defaults to 0.33.
	 * 						lead_voice_volume_delta		Controls lead vocals volume adjustment.		Integer, between -5 and +5
	 * 						backup_voice_volume_delta	Controls backup vocals volume adjustment.	Integer, between -5 and +5
	 * 						instrument_volume_delta		Controls instrumental volume adjustment.	Integer, between -5 and +5
	 * 						reverb_size		Size of reverb room. Defaults to 0.15.					Floating point, between 0 and 1
	 * 						wetness			Reverb for generated vocals. Defaults to 0.2.			Floating point, between 0 and 1
	 * 						dryness			Reverb for original vocals. Defaults to 0.8.			Floating point, between 0 and 1
	 * 						damping			Damping factor for high frequencies in reverb.			Floating point, between 0 and 1
	 * 										Defaults to 0.7.
	 * 						base64			Indicates if the input sound clip is in base64 format.	TRUE or FALSE
	 * 										Defaults to false.
	 * 						temp			Indicates if you want the output to be auto-deleted		TRUE or FALSE
	 * 										from the server. Defaults to false.
	 * 						webhook			URL to receive a POST API call once the audio			URL
	 * 										generation is complete.
	 * 						track_id		ID returned in the response for the webhook API call,	Integral value
	 * 										used to identify the request.
	 * 
	 * @return bool|string  The response from the API
	 */
	public function voiceCover( $opts ) {
		$url = $this->apiUrl( 'v6/voice/voice_cover' );
		return $this->sendRequest( $url, 'POST', $this->checkArgs( $opts ) );
	}

	/**
	 * Upload audio and get the voice_id to use them in other endpoints
	 * 
	 * @param array $opts  The options for the request
	 * 						Parameter		Description												Values
	 * 						--------------------------------------------------------------------------------------------
	 * 						key				Your API Key used for request authorization				string
	 * 						name			Display name of the voice you want to upload			string
	 * 						init_audio		Audio URL of the voice file to upload.					URL
	 * 										Only MP3 and WAV formats are allowed.
	 * 										Should be 10-25 seconds long for best results.
	 * 						language		Language of the voice. Ensure it matches the language	string
	 * 										of the uploaded voice.
	 * 										One of ["english", "arabic", "spanish", "german",
	 * 										"czech", "brazilian portuguese", "chinese", "dutch",
	 * 										"french", "hindi", "hungarian", "italian", "japanese",
	 * 										"korean", "polish", "russian", "turkish"]
	 * 
	 * @return bool|string  The response from the API
	 */
	public function voiceUpload( $opts ) {
		$url = $this->apiUrl( 'v6/voice/voice_upload' );
		return $this->sendRequest( $url, 'POST', $this->checkArgs( $opts ) );
	}

	/**
	 * Upload audio in base64 format and get the URL to use them in other endpoints
	 * 
	 * @param array $opts  The options for the request
	 * 						Parameter		Description												Values
	 * 						--------------------------------------------------------------------------------------------
	 * 						key				Your API Key used for request authorization				string
	 *						init_audio		Base64 format of the audio file. Only MP3 and WAV		base64 string
	 *										formats are allowed. Ensure the base64 audio is
	 *										between 10 and 15 seconds long for faster upload.
	 * 
	 * @return bool|string  The response from the API
	 */
	public function base64ToUrl( $opts ) {
		$url = $this->apiUrl( 'v6/voice/base64_to_url' );
		return $this->sendRequest( $url, 'POST', $this->checkArgs( $opts ) );
	}

	/**
	 * Generate audio based on input parameters ( Music Generation )
	 * 
	 * @param array $opts  The options for the request:
	 * 						Parameter		Description												Values
	 * 						--------------------------------------------------------------------------------------------
	 * 						key				Your API Key used for request authorization				string
	 * 						prompt			The input text for audio generation.					string
	 * 						init_audio		The conditioning melody for audio generation.			URL (can upload audio up to 30 seconds only)
	 * 						sampling_rate	The sampling rate of the generated audio.				integer, default: 32000, minimum: 10000
	 * 						max_new_token	The maximum number of new tokens for audio generation.	integer, range: 256 to 1024
	 * 						base64			Whether the input sound clip is in base64 format.		TRUE or FALSE
	 * 										Should be true or false. Defaults to false.
	 * 						temp			Whether you want temporary links. Useful if your		TRUE or FALSE
	 * 										country blocks access to storage sites.
	 * 						webhook			Set a URL to receive a POST API call once the audio		URL
	 * 										generation is complete.
	 * 						track_id		This ID is returned in the response to the webhook		integral value
	 * 										API call. Used to identify the webhook request.
	 * 
	 * @return bool|string  The response from the API
	 */
	public function musicGen( $opts ) {
		$url = $this->apiUrl( 'v6/voice/music_gen' );
		return $this->sendRequest( $url, 'POST', $this->checkArgs( $opts ) );
	}

	/**
	 * Fetch queued audio.
	 * 
	 * Usually more complex Voice generation requests take more time for processing.
	 * Such requests are being queued for processing and the output Voice are retrievable after some time.
	 * 
	 * @param array $opts  The options for the request:
	 * 						Parameter		Description												Values
	 * 						--------------------------------------------------------------------------------------------
	 * 						key				Your API Key used for request authorization				string
	 * 
	 * @return bool|string  The response from the API
	 */
	public function fetchAudio( $opts ) {
		if ( ! empty( $opts['fetch_url'] ) ) {
			$url = $this->apiUrl( $opts['fetch_url'] );
			unset( $opts['fetch_url'] );
		} else {
			$url = $this->apiUrl( "v6/voice/fetch/{$opts['fetch_id']}" );
		}
		unset( $opts['fetch_id'] );
		return $this->sendRequest( $url, 'POST', $this->checkArgs( $opts ) );
	}

	/**
	 * Transcribe a text from the audio with speech ( Speech To Text )
	 * 
	 * @param array $opts  The options for the request
	 * 						Parameter		Description													Values
	 * 						--------------------------------------------------------------------------------------------
	 * 						key				Your API Key used for request authorization					string
	 * 						audio			The audio file to be transcribed.							File (wav, mp3, flac, opus), max duration: 65 seconds
	 * 						audio_url		URL of the audio file to be transcribed.					string (URL)
	 * 						input_language	The language code of the audio content.						string (ISO 639-1, e.g., 'en', 'es', 'fr')
	 * 						timestamp_level	Specifies the level of detail for timestamps				'word', 'sentence', or null (default: null)
	 * 										 in the transcription.
	 * 						webhook			URL to receive a POST API call once the audio generation	URL
	 * 										is complete.
	 * 						track_id		ID returned in the response for the webhook API call,		integral value
	 * 										used to identify the webhook request.
	 * 
	 * @return bool|string  The response from the API
	 */
	public function speechToText( $opts ) {
		$url = $this->apiUrl( 'v6/whisper/transcribe' );
		if ( ! empty( $opts['audio'] ) && strlen( $opts['audio'] ) < 1000 ) {
			// $opts['content_type'] = 'multipart/form-data';
			// $opts['audio'] = curl_file_create( $opts['audio'] );
			$opts['audio_url'] = $opts['audio'];
			unset( $opts['audio'] );
		}
		return $this->sendRequest( $url, 'POST', $this->checkArgs( $opts ) );
	}

}
