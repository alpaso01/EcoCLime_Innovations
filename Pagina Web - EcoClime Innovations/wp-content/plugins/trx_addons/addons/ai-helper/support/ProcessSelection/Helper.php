<?php
namespace TrxAddons\AiHelper\ProcessSelection;

use TrxAddons\AiHelper\OpenAiAssistants;
use TrxAddons\AiHelper\Lists;
use TrxAddons\AiHelper\Utils;

if ( ! class_exists( 'Helper' ) ) {

	/**
	 * Main class for AI Helper ProcessSelection support
	 */
	class Helper {

		/**
		 * Constructor
		 */
		function __construct() {
			// Enqueue scripts and styles for the frontend
			add_action( 'trx_addons_action_load_scripts_front', array( $this, 'enqueue_scripts' ) );
			add_filter( 'trx_addons_filter_localize_script', array( $this, 'localize_script' ) );

			// AJAX callback for the 'Process Selection' buttons
			add_action( 'wp_ajax_trx_addons_ai_helper_process_selection', array( $this, 'process_selection' ) );
			// Callback function to fetch answer from the assistant
			add_action( 'wp_ajax_trx_addons_ai_helper_process_selection_fetch', array( $this, 'fetch_answer' ) );
		}

		/**
		 * Check if AI Helper is allowed
		 */
		public static function is_allowed() {
			//$allowed = OpenAi::instance()->get_api_key() != ''
			//			|| OpenAiAssistants::instance()->get_api_key() != ''
			//			|| GoogleAi::instance()->get_api_key() != ''
			//			|| FlowiseAi::instance()->get_api_key() != '';

			// Check if a default text model is selected
			$allowed = ! is_admin() && (int)trx_addons_get_option( 'ai_helper_process_selected', 1 ) > 0 && trx_addons_get_option( 'ai_helper_text_model_default' ) != '';
			// Check a current page to match the selected post types and URLs
			if ( $allowed ) {
				$allowed_url = false;
				// Check if a current page is a single page or an archive page for the selected post type
				$post_types = trx_addons_get_option( 'ai_helper_process_selected_post_types' );
				$post_type = get_post_type();
				if ( is_array( $post_types ) && ! empty( $post_types[ $post_type ] ) ) {
					$allowed_url = is_singular( $post_type )
									|| is_post_type_archive( $post_type )
									|| is_tax( trx_addons_get_post_type_taxonomy( $post_type ) );
				}
				// Check if an URL of the current page is in the list of allowed URLs (this is an additive check, not a restrictive one)
				$include = trx_addons_get_option( 'ai_helper_process_selected_url_include' );
				if ( ! $allowed_url && ! empty( $include ) ) {
					$url = trx_addons_get_current_url();
					$parts = array_map( 'trim', explode( "\n", str_replace( ',', "\n", $include ) ) );
					foreach( $parts as $part ) {
						if ( strpos( $url, $part ) !== false ) {
							$allowed_url = true;
							break;
						}
					}
				}
				// Check if an URL of the current page is in the list of restricted URLs
				$exclude = trx_addons_get_option( 'ai_helper_process_selected_url_exclude' );
				if ( $allowed_url && ! empty( $exclude ) ) {
					$url = trx_addons_get_current_url();
					$parts = array_map( 'trim', explode( "\n", str_replace( ',', "\n", $exclude ) ) );
					foreach( $parts as $part ) {
						if ( strpos( $url, $part ) !== false ) {
							$allowed_url = false;
							break;
						}
					}
				}
				$allowed = $allowed_url;
			}
			return apply_filters( 'trx_addons_filter_ai_helper_process_selection_allowed', $allowed );
		}

		/**
		 * Enqueue scripts and styles for the frontend
		 * 
		 * @hooked 'trx_addons_action_load_scripts_front'
		 */
		function enqueue_scripts() {
			if ( self::is_allowed() ) {
				wp_enqueue_style( 'trx_addons-ai-helper-process-selection', trx_addons_get_file_url( TRX_ADDONS_PLUGIN_ADDONS . 'ai-helper/support/ProcessSelection/assets/css/index.css' ), array(), null );
				wp_enqueue_script( 'trx_addons-ai-helper-process-selection', trx_addons_get_file_url( TRX_ADDONS_PLUGIN_ADDONS . 'ai-helper/support/ProcessSelection/assets/js/index.js' ), array( 'jquery' ), null, true );
				trx_addons_enqueue_msgbox();
			}
		}

		/**
		 * Localize script to show messages
		 * 
		 * @hooked 'trx_addons_filter_localize_script'
		 * 
		 * @param array $vars  Array of variables to be passed to the script
		 * 
		 * @return array  Modified array of variables
		 */
		function localize_script( $vars ) {
			if ( self::is_allowed() ) {
				$vars['msg_ai_helper_error'] = esc_html__( "AI Helper unrecognized response", 'trx_addons' );
				$vars['msg_process_explain'] = esc_html__( "Explain", 'trx_addons' );
				$vars['msg_process_summarize'] = esc_html__( "Summarize", 'trx_addons' );
				$vars['msg_process_translate'] = esc_html__( "Translate", 'trx_addons' );
			}
			return $vars;
		}

		/**
		 * Send a query to API to process text
		 * 
		 * @hooked 'wp_ajax_trx_addons_ai_helper_process_selection'
		 * 
		 * @param WP_REST_Request  $request  Full details about the request.
		 */
		function process_selection() {

			trx_addons_verify_nonce();

			$answer = array(
				'error' => '',
				'data' => array(
					'text' => '',
					'message' => ''
				)
			);

			$commands = Lists::get_list_ai_commands();
			$command  = trx_addons_get_value_gp( 'command' );
			if ( empty( $command ) || empty( $commands[ $command ] ) ) {
				$command = 'process_explain';
			}

			$content = strip_tags( trx_addons_get_value_gp( 'content' ) );

			$prompt = trx_addons_strdot( $commands[ $command ]['prompt'] );
			if ( $command == 'process_translate' ) {
				$language = trx_addons_get_value_gp( 'language' );
				if ( empty( $language ) ) {
					$language = 'en-US';
				}
				$prompt = str_replace( '%language%', sprintf( __( 'the language with code %s', 'trx_addons' ), $language ), $prompt );
			}
			$prompt .= ( $command != 'process_translate'
							? ' ' . __( 'Respond in the same language as the text being processed.', 'trx_addons' )
							: ''
							)
					. ' ' . sprintf( 
									__( 'The text to be processed is enclosed to double curly braces: %s', 'trx_addons' ),
									'{{ ' . preg_replace( "/(\r?\n){2,}/", '$1', $content ) . ' }}'
									);

			$params = compact( 'command', 'prompt' );
			
			if ( ! empty( $content ) ) {

				$api = Utils::get_chat_api();

				$response = $api->query(
					array(
						'prompt' => apply_filters( 'trx_addons_filter_ai_helper_prompt', $prompt, $params, 'process_selection' ),
						'role' => 'text_generator',
						'system_prompt' => apply_filters( 'trx_addons_filter_ai_helper_system_prompt', trx_addons_get_option( 'ai_helper_system_prompt_openai' ) ),
						'n' => 1,
					),
					$params
				);

				$answer = $this->parse_response( $response, $answer );

			} else {
				$answer['error'] = __( 'Error! Text is not specified.', 'trx_addons' );
			}

			// Return response to the AJAX handler
			trx_addons_ajax_response( $answer );
		}

		/**
		 * Callback function to fetch answer from the assistant
		 * 
		 * @hooked 'wp_ajax_trx_addons_ai_helper_process_selection_fetch'
		 * @hooked 'wp_ajax_nopriv_trx_addons_ai_helper_process_selection_fetch'
		 */
		function fetch_answer() {

			trx_addons_verify_nonce();

			$run_id = trx_addons_get_value_gp( 'run_id' );
			$thread_id = trx_addons_get_value_gp( 'thread_id' );

			$answer = array(
				'error' => '',
				'finish_reason' => 'queued',
				'run_id' => $run_id,
				'thread_id' => $thread_id,
				'data' => array(
					'text' => '',
					'message' => ''
				)
			);

			$api = OpenAiAssistants::instance();

			if ( $api->get_api_key() != '' ) {

				$response = $api->fetch_answer( $thread_id, $run_id );

				// $answer = trx_addons_sc_tgenerator_parse_response( $response, $answer );
				$answer = $this->parse_response( $response, $answer );

			} else {
				$answer['error'] = __( 'Error! API key is not specified.', 'trx_addons' );
			}

			// Return response to the AJAX handler
			trx_addons_ajax_response( apply_filters( 'trx_addons_filter_process_selection_fetch', $answer ) );
		}

		/**
		 * Parse response from the API
		 * 
		 * @param array $response  The response from the API
		 * @param array $answer    The answer to return
		 * 
		 * @return array  The answer
		 */
		function parse_response( $response, $answer ) {

			if ( ! empty( $response['finish_reason'] ) ) {
				$answer['finish_reason'] = $response['finish_reason'];
			}
	
			if ( ! empty( $response['thread_id'] ) ) {
				$answer['thread_id'] = $response['thread_id'];
			}

			if ( ! empty( $response['choices'][0]['message']['content'] ) ) {
				$answer['data']['text'] = array( str_replace( array( '{{', '}}' ), '', $response['choices'][0]['message']['content'] ) );
			} else if ( ! empty( $response['finish_reason'] ) && $response['finish_reason'] == 'queued' && ! empty( $response['run_id'] ) ) {
				$answer['finish_reason'] = $response['finish_reason'];
				$answer['run_id'] = $response['run_id'];
			} else {
				if ( ! empty( $response['error']['message'] ) ) {
					$answer['error'] = $response['error']['message'];
				} else if ( ! empty( $response['error'] ) && is_string( $response['error'] ) ) {
					$answer['error'] = $response['error'];
				} else {
					$answer['error'] = __( 'Error! Unknown response from the API. Maybe the API server is not available right now.', 'trx_addons' );
				}
			}

			return $answer;
		}
	}
}
