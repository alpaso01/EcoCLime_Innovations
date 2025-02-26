<?php
namespace TrxAddons\AiHelper\Gutenberg;

use TrxAddons\AiHelper\OpenAi;
use TrxAddons\AiHelper\OpenAiAssistants;
use TrxAddons\AiHelper\Lists;
use TrxAddons\AiHelper\Utils;

if ( ! class_exists( 'Helper' ) ) {

	/**
	 * Main class for AI Helper Gutenberg support
	 */
	class Helper {

		/**
		 * Constructor
		 */
		function __construct() {

			// Register AI Helper block

			// 1 way) Used only for static blocks (all functionality is in the block's js-file. No PHP-file is required)
			// add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue_block_editor_scripts' ) );

			// 2 way) Used for both static and dynamic blocks
			add_action( 'init', array( $this, 'register_blocks' ) );

			// REST API callbacks - return titles, excerpt and content for the post
			add_action( 'rest_api_init', array( $this, 'register_rest_api_callbacks' ) );

			// Add messages to js-vars
			add_filter( 'trx_addons_filter_localize_script_admin', array( $this, 'localize_script_admin' ) );
		}

		/**
		 * Check if AI Helper is allowed for Gutenberg
		 */
		public static function is_allowed() {
			return trx_addons_exists_gutenberg()
					&& trx_addons_get_setting( 'allow_gutenberg_blocks' );
		}

		/**
		 * Localize script to show messages in the admin mode
		 * 
		 * @hooked 'trx_addons_filter_localize_script_admin'
		 * 
		 * @param array $vars  Array of variables to be passed to the script
		 * 
		 * @return array  Modified array of variables
		 */
		function localize_script_admin( $vars ) {
			if ( self::is_allowed() ) {
				$vars['msg_ai_helper_error'] = esc_html__( "AI Helper error", 'trx_addons' );
				$vars['msg_ai_helper_response'] = esc_html__( "Check/Edit a response and apply it", 'trx_addons' );
				$vars['msg_ai_helper_response_variations'] = esc_html__( "Please, select a variation", 'trx_addons' );
				$vars['msg_ai_helper_bt_caption_replace'] = esc_html__( "Replace", 'trx_addons' );
				$vars['msg_ai_helper_bt_caption_prepend'] = esc_html__( "Prepend", 'trx_addons' );
				$vars['msg_ai_helper_bt_caption_append'] = esc_html__( "Append", 'trx_addons' );
				$vars['ai_helper_list_models'] = Lists::get_list_ai_text_models();
				$vars['ai_helper_list_commands'] = Lists::get_list_ai_commands();
				$vars['ai_helper_list_bases'] = Lists::get_list_ai_bases();
				$vars['ai_helper_list_text_tones'] = Lists::get_list_ai_text_tones();
				$vars['ai_helper_list_text_languages'] = Lists::get_list_ai_text_languages();
			}
			return $vars;
		}

		// 1st way) Used only for static blocks (all functionality is in the block's js-file. No PHP-file is required)
		// function enqueue_block_editor_scripts() {
		// 	if ( trx_addons_exists_gutenberg() && trx_addons_get_setting( 'allow_gutenberg_blocks' ) ) {
		// 		wp_enqueue_script(
		// 			'trx_addons-ai-helper-gutenberg-editor-panel',
		// 			trx_addons_get_file_url( TRX_ADDONS_PLUGIN_ADDONS . 'ai-helper/support/gutenberg/blocks/build/index.js' ),
		// 			trx_addons_block_editor_dependencis(),
		// 			filemtime( trx_addons_get_file_dir( TRX_ADDONS_PLUGIN_ADDONS . 'ai-helper/support/gutenberg/blocks/build/index.js' ) ),
		// 			true
		// 		);
		// 	}
		// }

		/**
		 * Register blocks (2nd way: Used for both static and dynamic blocks)
		 */
		function register_blocks() {

			if ( ! self::is_allowed() ) {
				return;
			}

			global $pagenow;
			if ( 'widgets.php' == $pagenow ) {
				return;
			}

			// Register style and script for Editor mode
			wp_register_style( 'trx_addons-ai-helper-gutenberg-editor-panel', trx_addons_get_file_url( TRX_ADDONS_PLUGIN_ADDONS . 'ai-helper/support/Gutenberg/blocks/build/index.css' ) );
			wp_register_script( 'trx_addons-ai-helper-gutenberg-editor-panel', trx_addons_get_file_url( TRX_ADDONS_PLUGIN_ADDONS . 'ai-helper/support/Gutenberg/blocks/build/index.js' ), array( 'wp-blocks', 'wp-element', 'wp-components', 'wp-plugins', 'wp-edit-post', 'wp-i18n' ) ); //, 'wp-editor'

			// Register block
			register_block_type( 'trx-addons/ai-helper-panel', apply_filters( 'trx_addons_gb_map', array(
				// Style and script files for Editor mode
				'editor_style' => 'trx_addons-ai-helper-gutenberg-editor-panel',
				'editor_script' => 'trx_addons-ai-helper-gutenberg-editor-panel',
				//'attributes'      => array(),
				//'render_callback' => array( $this, 'render_block' ),
			), 'trx-addons/ai-helper-panel' ) );
		}

		/**
		 * Register REST API callbacks
		 */
		function register_rest_api_callbacks() {
			if ( ! self::is_allowed() ) {
				return;
			}

			register_rest_route( 'ai-helper/v1', 'get-response', array(
				'methods' => \WP_REST_SERVER::CREATABLE,	// 'POST'
				'callback' => array( $this, 'get_response' ),
				'permission_callback' => array( $this, 'get_response_permissions_check' ),
			) );
			register_rest_route( 'ai-helper/v1', 'fetch-answer', array(
				'methods' => \WP_REST_SERVER::CREATABLE,	// 'POST'
				'callback' => array( $this, 'fetch_answer' ),
				'permission_callback' => array( $this, 'get_response_permissions_check' ),
			) );
		}

		/**
		 * Check if a user has permissions to get response from OpenAi API
		 * 
		 * @param WP_REST_Request  $request  Full details about the request.
		 */
		function get_response_permissions_check() {
			// Way 1: Allow access to registered users only (all users who have the edit_posts capability)
			return current_user_can( 'edit_posts' );

			// Way 2: Restrict endpoint to only users who have the edit_posts capability. For others return an error.
			// if ( ! current_user_can( 'edit_posts' ) ) {
			// 	return new WP_Error( 'rest_forbidden', esc_html__( 'OMG you can not edit posts.', 'trx_addons' ), array( 'status' => 401 ) );
			// }
			// return true;
		}

		/**
		 * Send a query to OpenAi API with a post content or a prompt and return response
		 * 
		 * @param WP_REST_Request  $request  Full details about the request.
		 */
		function get_response( $request ) {
			$answer = array(
				'error' => '',
				'data' => array(
					'text' => '',
					'message' => ''
				)
			);
			if ( current_user_can( 'edit_posts' ) ) {
				$params = $request->get_params();
				$model    = ! empty( $params['model'] ) ? $params['model'] : trx_addons_get_option( 'ai_helper_text_model_default', 'openai/default' );
				$commands = Lists::get_list_ai_commands();
				$command  = ! empty( $params['command'] ) ? $params['command'] : 'write_blog';
				$base_on  = ! empty( $params['base_on'] ) ? $params['base_on'] : 'prompt';
				$prompt   = ! empty( $params['prompt'] )  ? trim( $params['prompt'] ) : $commands[ $command ]['prompt'];
				$hint     = ! empty( $params['hint'] )  ? trim( $params['hint'] ) : '';
				$content  = ! empty( $params['content'] ) ? trim( $params['content'] ) : '';
				$text_tone = ! empty( $params['text_tone'] ) ? trim( $params['text_tone'] ) : 'normal';
				$text_language = ! empty( $params['text_language'] ) ? trim( $params['text_language'] ) : 'english';
				if ( ! empty( $model ) && ! empty( $command ) && ! empty( $commands[ $command ] )
					&& ( $base_on == 'prompt' && ! empty( $prompt ) || $base_on != 'prompt' && ! empty( $content ) )
				) {
					$msg = '';
					if ( ! in_array( $command, array( 'process_tone', 'process_translate' ) ) ) {
						$content = strip_tags( $content );
					}
					if ( $command == 'process_tone' ) {
						$prompt = str_replace( '%tone%', $text_tone, $prompt );
					}
					if ( $command == 'process_translate' ) {
						$prompt = str_replace( '%language%', $text_language, $prompt );
					}
					// Prepage a prompt part for variations
					$variations = '';
					if ( ! empty( $commands[ $command ]['variations'] ) ) {
						$variations = sprintf( __( 'Generate %d variants of the %s based on the post content (as a single sentence for each variant). Start each variant on a new line and enclose it in double curly braces. Return only text without numeration and any other messages.', 'trx_addons' ),
												apply_filters( 'trx_addons_filter_ai_helper_variations_total', $commands[ $command ]['variations'], $command ),
												$commands[ $command ]['variation_name'] 
						);
					}
					if ( $base_on == 'prompt' ) {
						$msg = trx_addons_strdot( $prompt ) . ' ' . $variations;
					} else {
						$msg = strpos( $command, 'write_' ) !== false
								? $prompt . ': ' . trx_addons_strdot( $content )
									. ( ! empty( $hint ) ? ' ' . trx_addons_strdot( $hint ) : '' )
									. $this->get_subprompt( $params )
								: ( ! empty( $variations ) ? $variations : trx_addons_strdot( $prompt ) )
									. ( ! empty( $hint ) ? ' ' . trx_addons_strdot( $hint ) : '' )
									. $this->get_subprompt( $params )
									. ( $command != 'process_translate'
										? ' ' . __( 'Respond in the same language as the text being processed.', 'trx_addons' )
										: ''
										)
									. ' ' . sprintf( __( 'The text to be processed is enclosed to double curly braces: %s', 'trx_addons' ),
													'{{ ' . preg_replace( "/(\r?\n){2,}/", '$1', $content ) . ' }}'
													);
					}

					$api = Utils::get_chat_api( $model );

					$chat_args = array(
						'prompt' => apply_filters( 'trx_addons_filter_ai_helper_prompt', $msg, $params, 'gutenberg' ),
						'role' => 'gb_assistant',
						'system_prompt' => apply_filters( 'trx_addons_filter_ai_helper_system_prompt', trx_addons_get_option( 'ai_helper_system_prompt_openai' ) ),
						'n' => 1,
					);
					if ( ! empty( $model ) ) {
						$chat_args['model'] = $model;
						if ( Utils::is_flowise_ai_model( $model ) ) {
							$chat_args['override_config'] = ! empty( $params['flowise_override'] ) ? $params['flowise_override'] : '';
						}
					}

					$response = $api->query( $chat_args, compact( 'model', 'command', 'base_on', 'prompt', 'content', 'text_tone', 'text_language' ) );

					$answer = $this->parse_response( $response, $answer );

				}
			}
			return rest_ensure_response( apply_filters( 'trx_addons_filter_ai_helper_get_response', $answer ) );
		}

		/**
		 * AJAX handler for the 'trx_addons_ajax_sc_tgenerator_fetch' action
		 * 
		 * @hooked 'wp_ajax_trx_addons_ajax_sc_tgenerator_fetch'
		 */
		function fetch_answer( $request ) {
			$answer = array(
				'error' => '',
				'data' => array(
					'text' => '',
					'message' => ''
				)
			);
			if ( current_user_can( 'edit_posts' ) ) {

				$params    = $request->get_params();
				$run_id    = ! empty( $params['run_id'] ) ? $params['run_id'] : '';
				$thread_id = ! empty( $params['thread_id'] ) ? $params['thread_id'] : '';
		
				$answer['finish_reason'] = 'queued';
				$answer['run_id'] = $run_id;
				$answer['thread_id'] = $thread_id;

				$api = OpenAiAssistants::instance();

				if ( $api->get_api_key() != '' ) {
		
					$response = $api->fetch_answer( $thread_id, $run_id );
		
					$answer = $this->parse_response( $response, $answer );
		
				} else {
					$answer['error'] = __( 'Error! API key is not specified.', 'trx_addons' );
				}
			}
			// Return response to the AJAX handler
			return rest_ensure_response( apply_filters( 'trx_addons_filter_ai_helper_fetch_answer', $answer ) );
		}
	
		/**
		 * Parse response from API
		 * 
		 * @param string $response  Response from API
		 * @param array  $answer    Current answer
		 * 
		 * @return array  Modified answer
		 */
		function parse_response( $response, $answer ) {
			if ( ! empty( $response['finish_reason'] ) ) {
				$answer['finish_reason'] = $response['finish_reason'];
			}
	
			if ( ! empty( $response['thread_id'] ) ) {
				$answer['thread_id'] = $response['thread_id'];
			}

			if ( ! empty( $response['choices'][0]['message']['content'] ) ) {
				// Get all variations from the response. Each variation is separated by a new line and encosed in double curly brackets.
				if ( substr_count( $response['choices'][0]['message']['content'], '{{' ) > 1 ) {
					if ( preg_match_all( '/{{(.*)}}/U', $response['choices'][0]['message']['content'], $matches ) ) {
						$answer['data']['text'] = $matches[1];
					} else {
						$answer['data']['text'] = array( $this->fix_headings_in_content( str_replace( array( '{{', '}}' ), '', $response['choices'][0]['message']['content'] ) ) );
					}
	
				// Get whole text from the response as a single variant
				} else {
					if ( preg_match( '#<body>([\s\S]*)</body>#U', $response['choices'][0]['message']['content'], $matches ) ) {
						$answer['data']['text'] = $this->fix_headings_in_content( $matches[1] );
					} else {
						$answer['data']['text'] = array( $this->fix_headings_in_content( str_replace( array( '{{', '}}' ), '', $response['choices'][0]['message']['content'] ) ) );
					}
				}
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

		/**
		 * Fix headings in the generated content: add an attribute {"level": N} to the wp:heading block
		 * 
		 * @param string $content  Generated content
		 * 
		 * @return string  Fixed content
		 */
		function fix_headings_in_content( $content ) {
			for ( $i = 1; $i <= 6; $i++ ) {
				$content = preg_replace( '/^<!-- wp:heading -->([\s]*<h' . $i . ')/m', '<!-- wp:heading {"level":' . $i . '} -->$1', $content );
			}
			return $content;
		}

		/**
		 * Get subprompt for the some commands
		 * 
		 * @param array $params  Parameters of the request
		 * 
		 * @return string  Subprompt
		 */
		function get_subprompt( $params ) {
			$subprompt = '';
			if ( ! empty( $params['command'] ) && strpos( $params['command'], 'write_' ) !== false ) {
				$subprompt = ( ! empty( $subprompt ) ? ' ' : '' )
								. apply_filters( 'trx_addons_filter_ai_helper_write_post_subprompt', __( 'The text should consist of at least three sections with subheadings.', 'trx_addons' ), $params );
			}
			return ! empty( $subprompt ) ? ' ' . $subprompt : '';
		}
	}
}
