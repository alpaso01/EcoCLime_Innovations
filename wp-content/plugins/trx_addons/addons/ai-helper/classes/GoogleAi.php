<?php
namespace TrxAddons\AiHelper;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Markdown\Parser\Parsedown;

/**
 * Class to make queries to the OpenAi API
 */
class GoogleAi extends Api {

	/**
	 * Class constructor.
	 *
	 * @access protected
	 */
	protected function __construct() {
		parent::__construct();
		$this->logger_section = 'google-ai';
		$this->token_option = 'ai_helper_token_google_ai';
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
				$this->api = new \ThemeRex\GoogleAi\Gemini( $token );
				$proxy = trx_addons_get_option( 'ai_helper_proxy_google_ai', '' );
				$proxy_auth = trx_addons_get_option( 'ai_helper_proxy_auth_google_ai', '' );
				if ( ! empty( $proxy ) ) {
					$this->api->setProxy( $proxy, $proxy_auth );
				}
			}
		}
		return $this->api;
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
		$default_model = trx_addons_get_option( 'ai_helper_text_model_default', 'google-ai/gemini-pro' );
		return Utils::is_google_ai_model( $default_model ) ? $default_model : 'google-ai/gemini-pro';
	}

	/**
	 * Return a maximum number of tokens in the prompt and response for specified model or from all available models
	 *
	 * @access static
	 * 
	 * @param string $model  Model name (flow id) for the API. If '*' - return a maximum value from all available models
	 * 
	 * @return int  The maximum number of tokens in the prompt and response for specified model or from all models
	 */
	static function get_max_tokens( $model = '' ) {
		$tokens = 0;
		if ( ! empty( $model ) ) {
			$model = str_replace( 'google-ai/', '', $model );
			$models = Lists::get_google_ai_chat_models();
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
			$model = str_replace( 'google-ai/', '', $model );
			$models = Lists::get_google_ai_chat_models();
			$tokens = static::get_tokens_from_list( $models, $model, 'output_tokens' );
		}
		return (int)$tokens;
	}

	/**
	 * Return a list of available models for the API
	 *
	 * @access public
	 */
	public function list_models( $args = array() ) {
		$args = array_merge( array(
			'token' => $this->get_token(),
		), $args );

		$response = false;

		if ( ! empty( $args['token'] ) ) {
			$api = $this->get_api( $args['token'] );
			$response = $api->listModels();
			if ( is_array( $response ) && ! empty( $response['models'] ) ) {
				$response = $response['models'];
			} else {
				$response = false;
			}
		}

		return $response;
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
			'model' => $this->get_model(),
			'prompt' => '',
			'n' => 1,
			'system_prompt' => '',
			// 'frequency_penalty' => 0,
			// 'presence_penalty' => 0,
		), $args );

		$args['max_tokens'] = ! empty( $args['max_tokens'] )
								? min( $args['max_tokens'], self::get_max_tokens( $args['model'] ) )
								: self::get_max_tokens( $args['model'] );

		$response = false;

		if ( ! empty( $args['token'] ) && ! empty( $args['model'] ) && ! empty( $args['prompt'] ) ) {

			$api = $this->get_api( $args['token'] );

			$chat_args = array_merge( $args, array(
				'messages' =>  ! is_bool( $args['prompt'] )
								? array(
										array(
											'role' => 'user',
											'content' => $args['prompt']
										),
									)
								: array(),
			) );
			unset( $chat_args['prompt'] );
			unset( $chat_args['role'] );

			$chat_args = $this->prepare_args( $chat_args );

			if ( $chat_args['max_tokens'] > 0 ) {
				$response = $api->query( $chat_args );
				if ( is_array( $response ) ) {
					$response = $this->prepare_response( $response, $chat_args );
					$this->logger->log( $response, 'query', $args, $this->logger_section );
				} else {
					$response = false;
				}
			} else {
				$response = array(
					'error' => esc_html__( 'The number of tokens in request is over limits.', 'trx_addons' )
				);
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
			'model' => $this->get_model(),
			'messages' => array(),
			'n' => 1,
			'system_prompt' => '',
			// 'frequency_penalty' => 0,
			// 'presence_penalty' => 0,
		), $args );

		$args['max_tokens'] = ! empty( $args['max_tokens'] )
								? min( $args['max_tokens'], self::get_max_tokens( $args['model'] ) )
								: self::get_max_tokens( $args['model'] );

		$response = false;

		if ( ! empty( $args['token'] ) && ! empty( $args['model'] ) && count( $args['messages'] ) > 0 ) {

			$api = $this->get_api( $args['token'] );

			$chat_args = $this->prepare_args( array_merge( $args, array() ) );

			if ( $chat_args['max_tokens'] > 0 ) {

				// Send a request
				$response = $api->query( $chat_args );

				if ( is_array( $response ) ) {
					$response = $this->prepare_response( $response, $chat_args );
					$this->logger->log( $response, 'chat', $args, $this->logger_section );
				} else {
					$response = false;
				}
			} else {
				$response = array(
					'error' => esc_html__( 'The number of tokens in request is over limits.', 'trx_addons' )
				);
			}
		}

		return $response;

	}

	/**
	 * Get the system prompt
	 *
	 * @access private
	 * 
	 * @param string $role  Role of the assistant. Possible values: 'gb_assistant', 'translator', 'chat'
	 * 
	 * @return string  System prompt
	 */
	private function get_system_prompt( $role ) {
		$prompt = '';

		if ( in_array( $role, array( 'gb_assistant', 'text_generator' ) ) ) {
			$prompt = __( 'You are an assistant for writing posts. Return only the result without any additional messages. Format the response with HTML tags.', 'trx_addons' );
		} else if ( $role == 'translator' ) {
			$prompt = __( 'You are translator. Translate the text to English or leave it unchanged if it is already in English. Return only the translation result without any additional messages and formatting.', 'trx_addons' );
		}

		return apply_filters( 'trx_addons_filter_ai_helper_get_system_prompt', $prompt, $role );
	}

	/**
	 * Convert a response object to the format, compatible with OpenAI API response
	 */
	protected function prepare_response( $response, $args ) {
		if ( ! empty( $response['candidates'][0]['content']['parts'] ) && is_array( $response['candidates'][0]['content']['parts'] ) ) {
			// Combine all parts of the response to one text
			$text = '';
			foreach ( $response['candidates'][0]['content']['parts'] as $part ) {
				if ( ! empty( $part['text'] ) ) {
					$text .= "\n" . $part['text'];
				}
			}
			// Parse the markdown
			if ( ! empty( $text ) ) {
				$parser = new Parsedown();
				$text = $parser->text(  $text );
			}
			// Count tokens
			$prompt_tokens = ! empty( $args['contents'] ) && ! empty( $args['contents'][ count( $args['contents'] ) - 1 ]['parts'][0]['text'] )
								? $this->count_tokens( $args['contents'][ count( $args['contents'] ) - 1 ]['parts'][0]['text'] )
								: 0;
			$completion_tokens = $this->count_tokens( $text );
			// Prepare the response
			$response = array(
				'finish_reason' => 'stop',
				'model' => ! empty( $args['model'] ) ? $args['model'] : __( 'Google Gemini', 'trx_addons' ),
				'usage' => array(
							'prompt_tokens' => $prompt_tokens,
							'completion_tokens' => $completion_tokens,
							'total_tokens' => $prompt_tokens + $completion_tokens,
							),
				'choices' => array(
								array(
									'message' => array(
										'content' => $text,
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
			// Convert the array 'messages' to the array 'contents' and count used tokens
			$tokens_total = 0;
			$args['contents'] = array();
			foreach ( $args['messages'] as $k => $message ) {
				// If it's a first message - add a system prompt to the message
				// if ( ! empty( $args['system_prompt'] ) ) {
				// 	if ( count( $args['messages'] ) == 1 ) {
				// 		$message['content'] = $args['system_prompt'] . "\n" . $message['content'];
				// 	}
				// }
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
				// Save the message to the contents array
				$args['contents'][] = $this->prepare_message_content( $message, ! empty( $args['model'] ) ? $args['model'] : static::get_model() );
			}
			// Remove messages
			unset( $args['messages'] );

			// Add a system prompt to the array 'system_instruction'
			if ( empty( $args['system_instruction'] ) ) {
				$system_prompt = ! empty( $args['system_prompt'] ) ? $args['system_prompt'] : $this->get_system_prompt( 'chat' );
				if ( ! empty( $system_prompt ) ) {
					$args['system_instruction'] = array(
						// 'role' => 'system',
						'parts' => array(
							array(
								'text' => $system_prompt
							)
						)
					);
				}
			}
			// Remove a system prompt
			unset( $args['system_prompt'] );
			
			// Limit the number of tokens
			$args['max_tokens'] = max( 0, $args['max_tokens'] - $tokens_total );
			
			// Limits a max_tokens with output_tokens (if specified)
			$output_tokens = 0;
			if ( ! empty( $args['model'] ) ) {
				$output_tokens = self::get_output_tokens( $args['model'] );
				if ( $output_tokens > 0 ) {
					$args['max_tokens'] = min( $args['max_tokens'], $output_tokens );
				}
			}
			
			// Add 'generationConfig' to the args
			$args['generationConfig'] = array();
			if ( ! empty( $output_tokens ) || ! empty( $args['max_tokens'] ) ) {
				$args['generationConfig']['maxOutputTokens'] = ! empty( $output_tokens ) ? $output_tokens : $args['max_tokens'];
			}
			if ( ! empty( $args['n'] ) ) {
				$args['generationConfig']['candidateCount'] = max( 1, (int)$args['n'] );
				unset( $args['n'] );
			}
			if ( ! empty( $args['temperature'] ) ) {
				$args['generationConfig']['temperature'] = $args['temperature'];
				unset( $args['temperature'] );
			}
			if ( ! empty( $args['top_p'] ) ) {
				$args['generationConfig']['topP'] = $args['top_p'];
				unset( $args['top_p'] );
			}
			if ( ! empty( $args['top_k'] ) ) {
				$args['generationConfig']['topK'] = $args['top_k'];
				unset( $args['top_k'] );
			}
		}
		
		// Remove a prefix 'google-ai/' from the model name
		if ( ! empty( $args['model'] ) ) {
			$args['model'] = str_replace( 'google-ai/', '', $args['model'] );
		}

		return $args;
	}

	/**
	 * Return a message content for the API as a string (if no attachments) or as an array (if attachments are present)
	 *
	 * @access private
	 * 
	 * @param array $message  The array with a message content and attachments (if any)
	 * @param string $model   The model name
	 * 
	 * @return array|string  Prepared message content
	 */
	private function prepare_message_content( $message, $model ) {
		$content = array(
			'role' => empty( $message['role'] ) || $message['role'] == 'user' ? 'user' : $message['role'],	// 'model'
			'parts' => array()
		);

		// Add attachments if any
		if ( ! empty( $message['attachments'] ) ) {
			$allowed = Utils::get_allowed_attachments( $model );
			foreach ( $message['attachments'] as $attachment ) {
				if ( empty( $attachment['name'] ) || empty( $attachment['file'] ) || ! file_exists( $attachment['file'] ) ) {
					continue;
				}
				$ext = strtolower( pathinfo( $attachment['name'], PATHINFO_EXTENSION ) );
				if ( in_array( $ext, $allowed ) ) {
					$content['parts'][] = array(
						'inline_data' => array(
							'mime_type' => Utils::get_mime_type( $ext ), //mime_content_type( $attachment['file'] ),
							'data' => base64_encode( trx_addons_fgc( $attachment['file'] ) ),
						),
					);
				}
			}
		}

		// Add a text message after attachments
		$content['parts'][] = array(
			'text' => $message['content']
		);

		return $content;
	}

}
