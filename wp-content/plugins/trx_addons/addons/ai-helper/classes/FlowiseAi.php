<?php
namespace TrxAddons\AiHelper;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class to make queries to the OpenAi API
 */
class FlowiseAi extends Api {

	/**
	 * Class constructor.
	 *
	 * @access protected
	 */
	protected function __construct() {
		parent::__construct();
		$this->logger_section = 'flowise-ai';
		$this->token_option = 'ai_helper_token_flowise_ai';
	}

	/**
	 * Return an object of the API
	 * 
	 * @param string $token  Access token for the API
	 * @param string $host   Host URL for the API
	 * 
	 * @return api  The object of the API
	 */
	public function get_api( $token = '', $host = '' ) {
		if ( empty( $this->api ) ) {
			if ( empty( $token ) ) {
				$token = $this->get_token();
			}
			if ( empty( $host ) ) {
				$host = $this->get_host();
			}
			if ( ! empty( $token ) && ! empty( $host ) ) {
				$this->api = new \ThemeRex\FlowiseAi\Query( $token, $host );
			}
		}
		return $this->api;
	}

	/**
	 * Return a host URL for the API from the plugin options
	 * 
	 * @access protected
	 * 
	 * @return string  Host URL for the API
	 */
	protected function get_host() {
		return trx_addons_get_option( 'ai_helper_host_flowise_ai' );
	}

	/**
	 * Return a model name for the API
	 * 
	 * @access static
	 * 
	 * 
	 * @return string  Model name for the API
	 */
	static function get_model() {
		$default_model = trx_addons_get_option( 'ai_helper_text_model_default', '' );
		return Utils::is_flowise_ai_model( $default_model ) ? $default_model : '';
	}

	/**
	 * Return a maximum number of tokens in the prompt and response for specified model or from all available models
	 *
	 * @access static
	 * 
	 * @param string $model  Model name (flow id) for the API. If '*' - return a maximum value from all models
	 * 
	 * @return int  The maximum number of tokens in the prompt and response for specified model or from all models
	 */
	static function get_max_tokens( $model = '' ) {
		$tokens = 0;
		if ( ! empty( $model ) ) {
			$model = str_replace( 'flowise-ai/', '', $model );
			$models = Lists::get_flowise_ai_chat_models();
			$tokens = static::get_tokens_from_list( $models, $model, 'max_tokens' );
		}
		return (int)$tokens;
	}

	/**
	 * Return a maximum number of tokens in the output (response) for specified model or from all available models
	 *
	 * @access static
	 * 
	 * @param string $model  Model name (flow id) for the API. If '*' - return a maximum value from all available models
	 * 
	 * @return int  The maximum number of tokens in the output (response) for specified model or from all models
	 */
	static function get_output_tokens( $model = '' ) {
		$tokens = 0;
		if ( ! empty( $model ) ) {
			$model = str_replace( 'flowise-ai/', '', $model );
			$models = Lists::get_flowise_ai_chat_models();
			$tokens = static::get_tokens_from_list( $models, $model, 'output_tokens' );
		}
		return (int)$tokens;
	}

	 /**
	 * Send a query to the API
	 *
	 * @access public
	 * 
	 * @param array $args  Query arguments
	 * 
	 * @return array  Response from the API
	 */
	public function query( $args = array(), $params = array() ) {
		$args = array_merge( array(
			'token' => $this->get_token(),
			'host' => $this->get_host(),
			'model' => $this->get_model(),
			'prompt' => '',
			'system_prompt' => '',
			'frequency_penalty' => 0,
			'presence_penalty' => 0,
		), $args );

		$args['max_tokens'] = ! empty( $args['max_tokens'] )
								? min( $args['max_tokens'], self::get_max_tokens( $args['model'] ) )
								: self::get_max_tokens( $args['model'] );

		$args['messages'] = array();
		if ( ! empty( $args['prompt'] ) && ! is_bool( $args['prompt'] ) ) {
			$args['messages'][] = array(
									'role' => 'user',
									'content' => $args['prompt']
								);
		}

		$response = false;

		if ( ! empty( $args['token'] ) && ! empty( $args['host'] ) && count( $args['messages'] ) > 0 ) {

			$args = $this->prepare_args( $args );

			if ( $args['max_tokens'] > 0 ) {
				$chat_args = $this->override_args( array(
					'question' => $args['messages'][ count( $args['messages'] ) - 1 ]['content'],
					'model' => $args['model'],
				), $args );

				$api = $this->get_api( $args['token'], $args['host'] );

				$response = $api->query( $chat_args );

				if ( is_array( $response ) ) {
					$response = $this->prepare_response( $response, $chat_args );
					$this->logger->log( $response, 'query', $args, $this->logger_section );
				} else {
					$response = false;
				}
			}
		}

		return $response;

	}

	/**
	 * Send a chat messages to the API
	 *
	 * @access public
	 * 
	 * @param array $args  Query arguments
	 * 
	 * @return array  Response from the API
	 */
	public function chat( $args = array(), $params = array() ) {
		$args = array_merge( array(
			'token' => $this->get_token(),
			'host' => $this->get_host(),
			'model' => $this->get_model(),
			'messages' => array(),
			'system_prompt' => '',
			'frequency_penalty' => 0,
			'presence_penalty' => 0,
		), $args );

		$args['max_tokens'] = ! empty( $args['max_tokens'] )
								? min( $args['max_tokens'], self::get_max_tokens( $args['model'] ) )
								: self::get_max_tokens( $args['model'] );

		$response = false;

		if ( ! empty( $args['token'] ) && ! empty( $args['host'] ) && ! empty( $args['model'] ) && count( $args['messages'] ) > 0 ) {
			$args = $this->prepare_args( $args );
			
			if ( $args['max_tokens'] > 0 ) {
				$chat_args = $this->override_args( array(
					'question' => $args['messages'][ count( $args['messages'] ) - 1 ]['content'],
					'model' => $args['model'],
				), $args );
				
				$api = $this->get_api( $args['token'], $args['host'] );

				$response = $api->query( $chat_args );

				if ( is_array( $response ) ) {
					$response = $this->prepare_response( $response, $chat_args );
					$this->logger->log( $response, 'chat', $args, $this->logger_section );
				} else {
					$response = false;
				}
			}
		}

		return $response;

	}

	/**
	 * Convert a response object to the format, compatible with OpenAI API response
	 */
	protected function prepare_response( $response, $args ) {
		if ( ! empty( $response['text'] ) ) {
			$prompt_tokens = $this->count_tokens( $args['question'] );
			$completion_tokens = $this->count_tokens( $response['text'] );
			$response = array(
				'finish_reason' => 'stop',
				'model' => ! empty( $args['model'] ) ? $args['model'] : __( 'FlowiseAI Chatbot', 'trx_addons' ),
				'usage' => array(
							'prompt_tokens' => $prompt_tokens,
							'completion_tokens' => $completion_tokens,
							'total_tokens' => $prompt_tokens + $completion_tokens,
							),
				'choices' => array(
								array(
									'message' => array(
										'content' => $response['text']
									)
								)
							)
			);
		}
		return $response;
	}

	/**
	 * Prepare args for the API: limit the number of tokens
	 *
	 * @access private
	 * 
	 * @param array $args  Query arguments
	 * 
	 * @return array  Prepared query arguments
	 */
	private function prepare_args( $args = array() ) {
		if ( ! empty( $args['messages'] ) && is_array( $args['messages'] ) ) {
			$tokens_total = 0;
			foreach ( $args['messages'] as $k => $message ) {
				// Remove all HTML tags
				//$message['content'] = strip_tags( $message['content'] );
				// Remove duplicate newlines
				$message['content'] = preg_replace( '/[\\r\\n]{2,}/', "\n", $message['content'] );
				// Remove all Gutenberg block comments
				$message['content'] = preg_replace( '/<!--[^>]*-->/', '', $message['content'] );
				// Count tokens
				$tokens_total += $this->count_tokens( $message['content'] );
				// Save the message
				$args['messages'][ $k ]['content'] = $message['content'];
			}
			$args['max_tokens'] = max( 0, $args['max_tokens'] - $tokens_total );
			// Limits a max_tokens with output_tokens (if specified)
			if ( ! empty( $args['model'] ) ) {
				$output_tokens = self::get_output_tokens( $args['model'] );
				if ( $output_tokens > 0 ) {
					$args['max_tokens'] = min( $args['max_tokens'], $output_tokens );
				}
			}
		}
		if ( ! empty( $args['model'] ) ) {
			$args['model'] = str_replace( 'flowise-ai/', '', $args['model'] );
		}
		return $args;
	}

	/**
	 * Add OverrideConfig with chat args
	 *
	 * @access private
	 * 
	 * @param array $args  Query arguments
	 * 
	 * @return array  Query arguments with OverrideConfig
	 */
	private function override_args( $chat_args = array(), $args = array() ) {
		$override = array();
		if ( ! empty( $args['system_prompt'] ) ) {
			$override['systemMessagePrompt'] = $args['system_prompt'];
		}
		if ( ! empty( $args['max_tokens'] ) ) {
			$override['maxTokens'] = $args['max_tokens'];
		}
		if ( ! empty( $args['temperature'] ) ) {
			$override['temperature'] = $args['temperature'];
		}
		if ( ! empty( $args['frequency_penalty'] ) ) {
			$override['frequencyPenalty'] = $args['frequency_penalty'];
		}
		if ( ! empty( $args['presence_penalty'] ) ) {
			$override['presencePenalty'] = $args['presence_penalty'];
		}
		if ( ! empty( $args['override_config'] ) ) {
			$json = json_decode( $args['override_config'], true );
			if ( ! empty( $json ) && is_array( $json ) ) {
				$override = array_merge( $override, $json );
			}
		}
		if ( count( $override ) > 0 ) {
			$chat_args['overrideConfig'] = $override;
		}
		return $chat_args;
	}

}
