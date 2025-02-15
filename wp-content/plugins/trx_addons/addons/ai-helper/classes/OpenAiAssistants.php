<?php
namespace TrxAddons\AiHelper;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Markdown\Parser\Parsedown;

/**
 * Class to make queries to the OpenAi Assistants API
 */
class OpenAiAssistants extends Api {

	/**
	 * Class constructor.
	 *
	 * @access protected
	 */
	protected function __construct() {
		parent::__construct();
		$this->logger_section = 'open-ai-assistants';
		$this->token_option = 'ai_helper_token_openai';
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
				$this->api = new \ThemeRex\OpenAi\OpenAiAssistants( $token );
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
		$default_model = trx_addons_get_option( 'ai_helper_text_model_default', '' );
		return Utils::is_openai_assistants_model( $default_model ) ? $default_model : '';
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
		$max_tokens = apply_filters( 'trx_addons_filter_ai_helper_openai_assistants_max_tokens', 4000 );
		return (int)$max_tokens;
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
			'system_prompt' => '',
			'frequency_penalty' => 0,
			'presence_penalty' => 0,
		), $args );

		// $args['max_tokens'] = ! empty( $args['max_tokens'] )
		// 						? min( $args['max_tokens'], self::get_max_tokens( $args['model'] ) )
		// 						: self::get_max_tokens( $args['model'] );

		$args['messages'] = array();
		if ( ! empty( $args['prompt'] ) && ! is_bool( $args['prompt'] ) ) {
			$args['messages'][] = array(
									'role' => 'user',
									'content' =>$args['prompt'],
								);
		}

		$response = false;

		if ( ! empty( $args['token'] ) && count( $args['messages'] ) > 0 ) {

			$args = $this->prepare_args( $args );

			if ( ! isset( $args['max_tokens'] ) || $args['max_tokens'] > 0 ) {

				$api = $this->get_api( $args['token'] );

				if ( empty( $args['thread_id'] ) ) {
					$thread = $api->createThread();
					if ( ! empty( $thread['id'] ) ) {
						$args['thread_id'] = $thread['id'];
					}
				}
				if ( empty( $args['thread_id'] ) ) {
					$response['error'] = ! empty( $thread['error']['message'] ) ? $thread['error']['message'] : __( "Can't create a thread", 'trx_addons' );
				} else {
					$message = $api->createMessage( $args['thread_id'], $args['messages'][ count( $args['messages'] ) - 1 ] );
					if ( empty( $message['id'] ) ) {
						$response['error'] = ! empty( $message['error']['message'] ) ? $message['error']['message'] : __( "Can't add a message to the thread", 'trx_addons' );
					} else {
						$run = $api->createRun( $args['thread_id'], array(
							'assistant_id' => $args['model'],
						) );
						if ( empty( $run['id'] ) ) {
							$response['error'] = ! empty( $run['error']['message'] ) ? $run['error']['message'] : __( "Can't run a thread", 'trx_addons' );
						} else {
							$args['run_id'] = $run['id'];
							if ( ! empty( $run['status'] ) && $run['status'] == 'completed' ) {
								$response = $this->get_answer( $args['thread_id'] );
								$response = $this->prepare_response( $response, $args );
								$this->logger->log( $response, 'query', $args, $this->logger_section );
							} else {
								$response = array(
									'finish_reason' => 'queued',
									'run_id' => $args['run_id'],
									'thread_id' => $args['thread_id'],
								);
							}
						}
					}
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
			'model' => $this->get_model(),
			'messages' => array(),
			'system_prompt' => '',
			'frequency_penalty' => 0,
			'presence_penalty' => 0,
			'thread_id' => '',
		), $args );

		// $args['max_tokens'] = ! empty( $args['max_tokens'] )
		// 						? min( $args['max_tokens'], self::get_max_tokens( $args['model'] ) )
		// 						: self::get_max_tokens( $args['model'] );

		$response = false;
		if ( ! empty( $args['token'] ) && ! empty( $args['model'] ) && count( $args['messages'] ) > 0 ) {

			$args = $this->prepare_args( $args );
			if ( ! isset( $args['max_tokens'] ) || $args['max_tokens'] > 0 ) {

				$api = $this->get_api( $args['token'] );

				$prev_messages = '';

				// Check if the thread is already created and available
				if ( ! empty( $args['thread_id'] ) ) {
					$thread = $api->retrieveThread( $args['thread_id'] );
					if ( empty( $thread['id'] ) ) {
						$args['thread_id'] = '';
					}
				}

				// If the thread is not created yet - create it
				if ( empty( $args['thread_id'] ) ) {
					// Filter messages to get only user messages (required for the thread creation) and remove the last message
					$messages = array_filter( array_slice( $args['messages'], 0, count( $args['messages'] ) - 1 ), function( $message ) {
						return ! empty( $message['content'] ) && ! empty( $message['role'] ) && $message['role'] == 'user';
					} );
					// If there are more than one messages - create a thread with messages
					if ( count( $messages ) > 0 ) {
						foreach( $messages as $message ) {
							$prev_messages .= ( ! empty( $prev_messages ) ? "\n" : '' ) . '{{' . $message['content'] . '}}';
						}
					}
					$thread = $api->createThread();
					if ( ! empty( $thread['id'] ) ) {
						$args['thread_id'] = $thread['id'];
					}
				}

				if ( empty( $args['thread_id'] ) ) {
					$response['error'] = ! empty( $thread['error']['message'] ) ? $thread['error']['message'] : __( "Can't create a thread", 'trx_addons' );
				} else {
					// Add the context to the last message (if there are previous messages)
					if ( ! empty( $prev_messages ) ) {
						$args['messages'][ count( $args['messages'] ) - 1 ]['content'] =
							__( "Previous user requests (Each user query starts with a new line and is enclosed in double curly braces. The queries are followed by a double line feed. Don't action them, but use them as context for the response.):", 'trx_addons' )
							. "\n"
							. $prev_messages
							. "\n\n"
							. __( 'Current user request (action it with the context of previous requests):', 'trx_addons' )
							. "\n"
							. $args['messages'][ count( $args['messages'] ) - 1 ]['content'];
					}
					// Add the last message to the thread
					$message = $api->createMessage( $args['thread_id'], $args['messages'][ count( $args['messages'] ) - 1 ] );
					if ( empty( $message['id'] ) ) {
						$response['error'] = ! empty( $message['error']['message'] ) ? $message['error']['message'] : __( "Can't add a message to the thread", 'trx_addons' );
					} else {
						// Run the thread
						$run_args = array(
							'assistant_id' => $args['model'],
						);
						if ( ! empty( $args['system_prompt'] ) ) {
							$run_args['instructions'] = $args['system_prompt'];
						}
						$run = $api->createRun( $args['thread_id'], $run_args );
						if ( empty( $run['id'] ) ) {
							$response['error'] = ! empty( $run['error']['message'] ) ? $run['error']['message'] : __( "Can't run a thread", 'trx_addons' );
						} else {
							$args['run_id'] = $run['id'];
							// If the run is completed - get the answer
							if ( ! empty( $run['status'] ) && $run['status'] == 'completed' ) {
								$response = $this->get_answer( $args['thread_id'] );
								$response = $this->prepare_response( $response, $args );
								$this->logger->log( $response, 'chat', $args, $this->logger_section );
							} else {
								// If the run is not completed - return the run status 'queued'
								$response = array(
									'finish_reason' => 'queued',
									'run_id' => $args['run_id'],
									'thread_id' => $args['thread_id'],
								);
							}
						}
					}
				}
			}
		}
		return $response;

	}

	/**
	 * Check the run status and return the response if the run is completed
	 * 
	 * @access public
	 * 
	 * @param string $thread_id  Thread id for the API
	 * @param string $run_id     Run id for the API
	 * 
	 * @return array  Response from the API
	 */
	public function fetch_answer( $thread_id, $run_id ) {
		$response = array(
			'finish_reason' => 'queued',
			'thread_id' => $thread_id,
			'run_id' => $run_id,
			'text' => '',
		);
		if ( ! empty( $thread_id ) && ! empty( $run_id ) ) {

			$api = $this->get_api( $this->get_token() );
			
			$run = $api->retrieveRun( $thread_id, $run_id );

			if ( ! empty( $run['status'] ) ) {
				// If a run is completed - get the answer
				if ( $run['status'] == 'completed' ) {
					$response = $this->get_answer( $thread_id );
					$args = array(
						'model' => $run['assistant_id'],
						'thread_id' => $thread_id,
						'run_id' => $run_id,
					);
					$response = $this->prepare_response( $response, $args );
					$this->logger->log( $response, 'chat', $args, $this->logger_section );

				// If a run is requires action - call the functions and submit the outputs
				} else if ( $run['status'] == 'requires_action' ) {
					if ( ! empty( $run['required_action']['submit_tool_outputs']['tool_calls'] ) && is_array( $run['required_action']['submit_tool_outputs']['tool_calls'] ) ) {
						$outputs = array();
						foreach ( $run['required_action']['submit_tool_outputs']['tool_calls'] as $k => $v ) {
							if ( $v['type'] == 'function' && ! empty( $v['function']['name'] ) ) {
								$rez = apply_filters(
											'trx_addons_filter_api_call',
											'',
											$v['function']['name'],
											! empty( $v['function']['arguments'] ) ? json_decode( $v['function']['arguments'], true ) : false
								);
								if ( empty( $rez ) ) {
									$rez = array(
											'status' => 'error',
											'message' => __( 'Unsupported function called', 'trx_addons' )
									);
								}
								$outputs[] = array(
												'tool_call_id' => $v['id'],
												'output'       => json_encode( $rez )
								);
							}
						}
						if ( ! empty( $outputs ) ) {
							$api->submitToolOutputsToRun( $thread_id, $run_id, array( 'tool_outputs' => $outputs ) );
						}
					}

				// If a run status is not 'queued' or 'in_progress' - return error
				} else if ( ! in_array( $run['status'], array( 'queued', 'in_progress' ) ) ) {
					$response['finish_reason'] = 'error';
					$response['error'] = ! empty( $run['last_error']['message'] )
											? $run['last_error']['message']
											: ( ! empty( $run['error']['message'] )
												? $run['error']['message']
												: __( 'The run failed. Please, try again.', 'trx_addons' )
												);
				}
			} else {
				$response['finish_reason'] = 'error';
				$response['error'] = __( 'Unexpected response from the server - no status field.', 'trx_addons' );
			}
		} else {
			$response['finish_reason'] = 'error';
			$response['error'] = __( 'Thread ID or Run ID is not specified.', 'trx_addons' );
		}
		return $response;
	}

	/**
	 * Get an answer from the thread by id
	 * 
	 * @access private
	 * 
	 * @param string $thread_id  Thread id for the API
	 * 
	 * @return array  Response from the API
	 */
	private function get_answer( $thread_id ) {
		$response = array(
			'finish_reason' => 'stop',
			'thread_id' => $thread_id,
			'text' => '',
			'question' => '',
		);
		if ( ! empty( $thread_id ) ) {

			$api = $this->get_api( $this->get_token() );
			
			$messages = $api->listMessages( $thread_id );
			
			if ( ! empty( $messages['data'] ) && is_array( $messages['data'] ) ) {
				// Parse the answer and replace all Markdown tags with HTML tags
				$parser = new Parsedown();
				foreach ( $messages['data'] as $message ) {
					if ( ! empty( $message['role'] ) ) {
						if ( ! empty( $message['content'] ) && is_array( $message['content'] ) ) {
							foreach ( $message['content'] as $k => $v ) {
								if ( ! empty( $v['text']['value'] ) ) {
									$key = $message['role'] == 'assistant' ? 'text' : 'question';
									$text = $parser->text( $v['text']['value'] );
									$is_html = preg_match( '/<(br|p|ol|ul|dl|h1|h2|h3|h4|h5|h6)[^>]*>/i', $text, $matches );
									// Replace annotations
									if ( $message['role'] == 'assistant' && ! empty( $v['text']['annotations'] ) && is_array( $v['text']['annotations'] ) ) {
										$add = array();
										$idx = 0;
										foreach ( $v['text']['annotations'] as $annotation ) {
											if ( ! empty( $annotation['text'] ) ) {
												$idx++;
												$parts = explode( '†', $annotation['text'] );
												$name = '';
												if ( ! empty( $parts[1] ) ) {
													$parts = explode( '】', $parts[1] );
													$name = $parts[0];
												}
												if ( ! empty( $name ) ) {
													$text = str_replace( $annotation['text'], ' ' . ( $is_html ? '<span class="sc_chat_list_item_annotation_source">' : '' ) . '[' . $idx . ']' . ( $is_html ? '</span>' : '' ) . ' ', $text );
													$add[] = $is_html
																? '<span class="sc_chat_list_item_annotation" data-tooltip-text="' . esc_attr( $name ) . '">[' . $idx . ']</span> '
																: '[' . $idx . ': ' . $name . '] ';
												}
											}
										}
										if ( ! empty( $add ) ) {
											$text .= ( $is_html ? '<br>' : "\n" ) . join( ' ', $add );
										}
									}
									// Add the text to the response
									$response[ $key ] .= ( ! empty( $response[ $key ] ) ? ( $is_html ? '<br>' : "\n" ) : '' )
														. str_replace( '<a ', '<a target="_blank" ', $text );
								}
							}
						}
						// Exit after the first user message to get the answer from assistant and a last user question only
						if ( $message['role'] == 'user' ) {
							break;
						}
					}
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
			$prompt_tokens = $this->count_tokens( $response['question'] );
			$completion_tokens = $this->count_tokens( $response['text'] );
			$response = array(
				'finish_reason' => 'stop',
				'model' => ! empty( $args['model'] )
							? $args['model']
							: ( ! empty( $response['model'] )
								? $response['model']
								: __( 'OpenAI Assistant', 'trx_addons' )
								),
				'run_id' => ! empty( $args['run_id'] ) ? $args['run_id'] : '',
				'thread_id' => ! empty( $args['thread_id'] ) ? $args['thread_id'] : '',
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
	 * @access protected
	 * 
	 * @param array $args  Query arguments
	 * 
	 * @return array  Prepared query arguments
	 */
	protected function prepare_args( $args = array() ) {
		if ( ! empty( $args['messages'] ) && is_array( $args['messages'] ) ) {
			// $tokens_total = 0;
			foreach ( $args['messages'] as $k => $message ) {
				if ( empty( $message['content'] ) ) {
					unset( $args['messages'][ $k ] );
					continue;
				}
				// Remove all HTML tags
				//$message['content'] = strip_tags( $message['content'] );
				// Remove duplicate newlines
				$message['content'] = preg_replace( '/[\\r\\n]{2,}/', "\n", $message['content'] );
				// Remove all Gutenberg block comments
				$message['content'] = preg_replace( '/<!--[^>]*-->/', '', $message['content'] );
				// Count tokens
				// $tokens_total += $this->count_tokens( $message['content'] );
				// Save the message
				$args['messages'][ $k ]['content'] = $message['content'];
			}
			// $args['max_tokens'] = max( 0, $args['max_tokens'] - $tokens_total );
			unset( $args['max_tokens'] );
		}
		if ( ! empty( $args['model'] ) ) {
			$args['model'] = str_replace( 'openai-assistants/', '', $args['model'] );
		}
		return $args;
	}

}
