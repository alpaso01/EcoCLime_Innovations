<?php
namespace TrxAddons\AiHelper;

if ( ! trait_exists( 'ListsTextGeneration' ) ) {

	/**
	 * Return arrays with the lists used for the text generation / chats / assistants
	 */
	trait ListsTextGeneration {

		/**
		 * Return a list of chat APIs
		 * 
		 * @access public
		 * @static
		 * 
		 * @return array  	  The list of chat APIs
		 */
		static function get_list_ai_chat_apis() {
			return apply_filters( 'trx_addons_filter_ai_helper_list_ai_chat_apis', array(
				'openai' => esc_html__( 'Open AI', 'trx_addons' ),
				'openai-assistants' => esc_html__( 'Open AI Assistants', 'trx_addons' ),
				'flowise-ai' => esc_html__( 'Flowise AI', 'trx_addons' ),
				'google-ai' => esc_html__( 'Google AI (Gemini)', 'trx_addons' ),
			) );
		}

		/**
		 * Return a default list of chat APIs with the enabled status
		 * 
		 * @access public
		 * @static
		 * 
		 * @return array  	  The default list of chat APIs
		 */
		static function get_list_ai_chat_apis_enabled() {
			$api_list = self::get_list_ai_chat_apis();
			if ( ! is_array( $api_list ) ) {
				$api_list = array();
			}
			foreach( $api_list as $api => $title ) {
				$api_list[ $api ] = 1;
			}
			return $api_list;
		}

		/**
		 * Return a list of chat models for AI
		 * 
		 * @access public
		 * @static
		 * 
		 * @return array  	  The list of chat models
		 */
		static function get_list_ai_chat_models( $sc = 'sc_chat' ) {
			// Get an API order
			$api_order = trx_addons_get_option( "ai_helper_{$sc}_api_order", self::get_list_ai_chat_apis_enabled() );
			// Prepare a list of models
			$models = array(
//				'' => esc_html__( 'Default model', 'trx_addons' ),
			);
			foreach( $api_order as $api => $enable ) {
				// Open AI
				if ( $api == 'openai' && (int)$enable > 0 ) {
					$openai_models = self::get_openai_chat_models();
					foreach ( $openai_models as $k => $v ) {
						$models[ 'openai/' . $k ] = $v['title'];
					}
				}
				// Open AI Assistants
				if ( $api == 'openai-assistants' && (int)$enable > 0 ) {
					$openai_assistants = self::get_openai_assistants();
					foreach ( $openai_assistants as $k => $v ) {
						$models[ 'openai-assistants/' . $k ] = $v['title'];
					}
				}
				// Flowise AI
				if ( $api == 'flowise-ai' && (int)$enable > 0 ) {
					$flowise_models = self::get_flowise_ai_chat_models();
					foreach ( $flowise_models as $k => $v ) {
						$models[ 'flowise-ai/' . $k ] = $v['title'];
					}
				}
				// Google AI
				if ( $api == 'google-ai' && (int)$enable > 0 ) {
					$google_models = self::get_google_ai_chat_models();
					foreach ( $google_models as $k => $v ) {
						$models[ 'google-ai/' . $k ] = $v['title'];
					}
				}
			}
			return apply_filters( 'trx_addons_filter_ai_helper_list_ai_chat_models', $models, $sc );
		}

		/**
		 * Return a list of text models for AI
		 * 
		 * @access public
		 * @static
		 * 
		 * @return array  	  The list of text models
		 */
		static function get_list_ai_text_models() {
			return self::get_list_ai_chat_models( 'sc_tgenerator' );
		}

		/**
		 * Return a list of AI Commands
		 * 
		 * @access public
		 * @static
		 * 
		 * @return array  	  The list of AI Commands
		 */
		static function get_list_ai_commands() {
			return apply_filters( 'trx_addons_filter_ai_helper_list_ai_commands', array(

				'/-content' => array(
					'title' => esc_html__( '- Content -', 'trx_addons' )
				),
				'write_blog' => array(
					'title' => esc_html__( 'Blog post', 'trx_addons' ),
					'prompt' => esc_html__( 'Write a blog post about', 'trx_addons' )
				),
				'write_social' => array(
					'title' => esc_html__( 'Social media post', 'trx_addons' ),
					'prompt' => esc_html__( 'Write a social media post about', 'trx_addons' ),
				),
				'write_outline' => array(
					'title' => esc_html__( 'Outline', 'trx_addons' ),
					'prompt' => esc_html__( 'Write an outline about', 'trx_addons' ),
				),
				'write_press' => array(
					'title' => esc_html__( 'Press Release', 'trx_addons' ),
					'prompt' => esc_html__( 'Write a press release about', 'trx_addons' ),
				),
				'write_creative' => array(
					'title' => esc_html__( 'Creative story', 'trx_addons' ),
					'prompt' => esc_html__( 'Write a creative story about', 'trx_addons' ),
				),
				'write_essay' => array(
					'title' => esc_html__( 'Essay', 'trx_addons' ),
					'prompt' => esc_html__( 'Write an essay about', 'trx_addons' ),
				),
				'write_poem' => array(
					'title' => esc_html__( 'Poem', 'trx_addons' ),
					'prompt' => esc_html__( 'Write a poem about', 'trx_addons' ),
				),
				'write_todo' => array(
					'title' => esc_html__( 'To-Do list', 'trx_addons' ),
					'prompt' => esc_html__( 'Write a todo list about', 'trx_addons' ),
				),
				'write_agenda' => array(
					'title' => esc_html__( 'Meeting agenda', 'trx_addons' ),
					'prompt' => esc_html__( 'Write a meeting agenda about', 'trx_addons' ),
				),
				'write_pros' => array(
					'title' => esc_html__( 'Pros and Cons list', 'trx_addons' ),
					'prompt' => esc_html__( 'Write a pros and cons list about', 'trx_addons' ),
				),
				'write_job' => array(
					'title' => esc_html__( 'Job description', 'trx_addons' ),
					'prompt' => esc_html__( 'Write a job description about', 'trx_addons' ),
				),
				'write_sales' => array(
					'title' => esc_html__( 'Sales email', 'trx_addons' ),
					'prompt' => esc_html__( 'Write a sales email about', 'trx_addons' ),
				),
				'write_recruiting' => array(
					'title' => esc_html__( 'Recruiting email', 'trx_addons' ),
					'prompt' => esc_html__( 'Write a recruiting email about', 'trx_addons' ),
				),
				'write_brainstorm' => array(
					'title' => esc_html__( 'Brainstorm ideas', 'trx_addons' ),
					'prompt' => esc_html__( 'Brainstorm ideas on', 'trx_addons' ),
				),

				'/-process' => array(
					'title' => esc_html__( '- Text processing -', 'trx_addons' ),
				),
				'process_title' => array(
					'title' => esc_html__( 'Generate a post title', 'trx_addons' ),
					'prompt' => esc_html__( 'Write a post title about', 'trx_addons' ),
					'variation_name' => esc_html__( 'post title', 'trx_addons' ),
					'variations' => 5,
				),
				'process_excerpt' => array(
					'title' => esc_html__( 'Generate a post excerpt', 'trx_addons' ),
					'prompt' => esc_html__( 'Write a post excerpt about', 'trx_addons' ),
					'variation_name' => esc_html__( 'post excerpt', 'trx_addons' ),
					'variations' => 3,
				),
				'process_heading' => array(
					'title' => esc_html__( 'Generate a text heading', 'trx_addons' ),
					'prompt' => esc_html__( 'Write a text heading', 'trx_addons' ),
					'variation_name' => esc_html__( 'text heading', 'trx_addons' ),
					'variations' => 5,
				),
				'process_continue' => array(
					'title' => esc_html__( 'Continue writing', 'trx_addons' ),
					'prompt' => esc_html__( 'Write a continuation of the text', 'trx_addons' ),
				),
				'process_longer' => array(
					'title' => esc_html__( 'Make longer', 'trx_addons' ),
					'prompt' => esc_html__( 'Make text longer', 'trx_addons' ),
				),
				'process_shorter' => array(
					'title' => esc_html__( 'Make shorter', 'trx_addons' ),
					'prompt' => esc_html__( 'Make text shorter', 'trx_addons' ),
				),
				'process_summarize' => array(
					'title' => esc_html__( 'Summarize', 'trx_addons' ),
					'prompt' => esc_html__( 'Read the following text and provide a condensed version of it, highlighting the main ideas and key points. Retain important details and conclusions, but remove secondary information and excessive details. The goal is to create a brief, yet accurate and informative summary of the content of the original text.', 'trx_addons' ),
					'variation_name' => esc_html__( 'text summary', 'trx_addons' ),
					'variations' => 3,
				),
				'process_explain' => array(
					'title' => esc_html__( 'Explain', 'trx_addons' ),
					'prompt' => esc_html__( 'Read the provided text and create an extended version of it. Focus on elucidating the nuances, complexities, and subtleties of the subject matter. Add detailed explanations, examples, and context where necessary to ensure a deeper understanding of the text. The aim is to enhance clarity and provide a comprehensive view of the topic, making it accessible and understandable for someone unfamiliar with the subject.', 'trx_addons' ),
				),
				'process_spell' => array(
					'title' => esc_html__( 'Spell check', 'trx_addons' ),
					'prompt' => esc_html__( 'Fix spelling and grammar', 'trx_addons' ),
				),
				'process_tone' => array(
					'title' => esc_html__( 'Change tone', 'trx_addons' ),
					'prompt' => esc_html__( 'Change a tone of the text to %tone%', 'trx_addons' ),
				),
				'process_translate' => array(
					'title' => esc_html__( 'Translate', 'trx_addons' ),
					'prompt' => esc_html__( 'Translate a text to %language%', 'trx_addons' ),
				),
			) );
		}

		/**
		 * Return a list of parts of text used as a source (base) for AI
		 * 
		 * @access public
		 * @static
		 * 
		 * @return array  	  The list of parts of text
		 */
		static function get_list_ai_bases() {
			return apply_filters( 'trx_addons_filter_ai_helper_list_ai_base', array(
				'prompt' => esc_html__( 'Prompt', 'trx_addons' ),
				'title' => esc_html__( 'Post title', 'trx_addons' ),
				'excerpt' => esc_html__( 'Post excerpt', 'trx_addons' ),
				'content' => esc_html__( 'Post content', 'trx_addons' ),
				'selected' => esc_html__( 'Selected text', 'trx_addons' ),
			) );
		}

		/**
		 * Return a list of text tones for AI
		 * 
		 * @access public
		 * @static
		 * 
		 * @return array  	  The list of text tones
		 */
		static function get_list_ai_text_tones() {
			return apply_filters( 'trx_addons_filter_ai_helper_list_ai_text_tones', array(
				'normal' => esc_html__( 'Normal', 'trx_addons' ),
				'professional' => esc_html__( 'Professional', 'trx_addons' ),
				'casual' => esc_html__( 'Casual', 'trx_addons' ),
				'confident' => esc_html__( 'Confident', 'trx_addons' ),
				'friendly' => esc_html__( 'Friendly', 'trx_addons' ),
				'straightforward' => esc_html__( 'Straightforward', 'trx_addons' ),
			) );
		}

		/**
		 * Return a list of text languages for AI translations
		 * 
		 * @access public
		 * @static
		 * 
		 * @return array  	  The list of languages
		 */
		static function get_list_ai_text_languages() {
			return apply_filters( 'trx_addons_filter_ai_helper_list_ai_translations', array(
				'English' => esc_html__( 'English', 'trx_addons' ),
				'French' => esc_html__( 'French', 'trx_addons' ),
				'German' => esc_html__( 'German', 'trx_addons' ),
				'Spanish' => esc_html__( 'Spanish', 'trx_addons' ),
				'Portuguese' => esc_html__( 'Portuguese', 'trx_addons' ),
				'Italian' => esc_html__( 'Italian', 'trx_addons' ),
				'Dutch' => esc_html__( 'Dutch', 'trx_addons' ),
				'Ukrainian' => esc_html__( 'Ukrainian', 'trx_addons' ),
				'Chinese' => esc_html__( 'Chinese', 'trx_addons' ),
				'Japanese' => esc_html__( 'Japanese', 'trx_addons' ),
				'Korean' => esc_html__( 'Korean', 'trx_addons' ),
			) );
		}

		/**
		 * Return a list of layouts for AI Chat
		 * 
		 * @access public
		 * @static
		 * 
		 * @return array  	  The list of layouts for AI Chat
		 */
		static function get_list_ai_chat_layouts() {
			return apply_filters( 'trx_addons_filter_ai_helper_ai_chat_layouts', array(
				'default' => esc_html__( 'Default', 'trx_addons' ),
				'popup' => esc_html__( 'Popup', 'trx_addons' ),
			) );
		}

		/**
		 * Return a list of tags positions for AI Chat
		 * 
		 * @access public
		 * @static
		 * 
		 * @return array  	  The list of tags positions for AI Chat
		 */
		static function get_list_ai_chat_tags_positions() {
			return apply_filters( 'trx_addons_filter_ai_helper_ai_chat_tags_positions', array(
				'none' => esc_html__( 'No tags', 'trx_addons' ),
				'before' => esc_html__( 'Before the prompt', 'trx_addons' ),
				'after' => esc_html__( 'After the prompt', 'trx_addons' ),
			) );
		}



		/* OPENAI API
		--------------------------------------------------------------------------------------- */

		/**
		 * Return a list of chat models for Open AI with max tokens for each model
		 * 
		 * @access public
		 * @static
		 * 
		 * @return array  	  The list of chat models for Open AI
		 */
		static function get_default_openai_chat_models() {
			return apply_filters( 'trx_addons_filter_ai_helper_ai_models', array(
				'gpt-3.5-turbo' => array(
					'id' => 'gpt-3.5-turbo',
					'title' => esc_html__( 'GPT-3.5 turbo', 'trx_addons' ),
					'max_tokens' => 16000,
					'output_tokens' => 4000,
				),
				'gpt-4' => array(
					'id' => 'gpt-4',
					'title' => esc_html__( 'GPT-4', 'trx_addons' ),
					'max_tokens' => 8000,
					'output_tokens' => 4000,
				),
				'gpt-4-turbo' => array(
					'id' => 'gpt-4-turbo',
					'title' => esc_html__( 'GPT-4 turbo', 'trx_addons' ),
					'max_tokens' => 128000,
					'output_tokens' => 4000,
				),
				'gpt-4-turbo-preview' => array(
					'id' => 'gpt-4-turbo-preview',
					'title' => esc_html__( 'GPT-4 turbo preview', 'trx_addons' ),
					'max_tokens' => 128000,
					'output_tokens' => 4000,
				),
				'gpt-4o' => array(
					'id' => 'gpt-4o',
					'title' => esc_html__( 'GPT-4o', 'trx_addons' ),
					'max_tokens' => 128000,
					'output_tokens' => 4000,
				)
			) );
		}

		/**
		 * Return a list of chat models for OpenAi
		 * 
		 * @access public
		 * @static
		 * 
		 * @return array  	  The list of chat models for OpenAi
		 */
		static function get_openai_chat_models() {
			$models = array();
			$token = trx_addons_get_option( 'ai_helper_token_openai', '' );
			if ( ! empty( $token ) ) {
				$models = trx_addons_get_option( 'ai_helper_chat_models_openai', array() );
				if ( empty( $models ) || ! is_array( $models ) || empty( $models[0]['id'] ) ) {
					$models = self::get_default_openai_chat_models();
				} else {
					$new_models = array();
					foreach ( $models as $v ) {
						if ( ! empty( $v['id'] ) ) {
							$new_models[ $v['id'] ] = $v;
						}
					}
					$models = $new_models;
				}
			}
			return $models;
		}

		/**
		 * Return a list of chat models for Open AI
		 * 
		 * @access public
		 * @static
		 * 
		 * @return array  	  The list of chat models for Open AI
		 */
		static function get_list_openai_chat_models() {
			return apply_filters( 'trx_addons_filter_ai_helper_list_ai_models', trx_addons_array_from_list( self::get_openai_chat_models() ) );
		}



		/* OPEN AI ASSISTANTS API
		--------------------------------------------------------------------------------------- */

		/**
		 * Return a list of assistants for Open Ai
		 * 
		 * @access public
		 * @static
		 * 
		 * @return array  	  The list of assistants for Flowise Ai
		 */
		static function get_openai_assistants() {
			$assistants = array();
			$token = trx_addons_get_option( 'ai_helper_token_openai', '' );
			if ( ! empty( $token ) ) {
				$assistants = trx_addons_get_option( 'ai_helper_models_openai_assistants', array() );
				if ( empty( $assistants ) || ! is_array( $assistants ) || empty( $assistants[0]['id'] ) ) {
					$assistants = array();
				} else {
					$new_assistants = array();
					foreach ( $assistants as $k => $v ) {
						if ( ! empty( $v['id'] ) ) {
							$new_assistants[ $v['id'] ] = $v;
							unset( $new_assistants[ $v['id'] ]['id'] );
						}
					}
					$assistants = $new_assistants;
				}
			}
			return $assistants;
		}



		/* FLOWISE AI API
		--------------------------------------------------------------------------------------- */

		/**
		 * Return a list of models for Flowise Ai
		 * 
		 * @access public
		 * @static
		 * 
		 * @return array  	  The list of models for Flowise Ai
		 */
		static function get_flowise_ai_chat_models() {
			$models = array();
			$token = trx_addons_get_option( 'ai_helper_token_flowise_ai', '' );
			if ( ! empty( $token ) ) {
				$models = trx_addons_get_option( 'ai_helper_models_flowise_ai' );
				if ( empty( $models ) || ! is_array( $models ) || empty( $models[0]['id'] ) ) {
					$models = array();
				} else {
					$new_models = array();
					foreach ( $models as $k => $v ) {
						if ( ! empty( $v['id'] ) ) {
							$new_models[ $v['id'] ] = $v;
							unset( $new_models[ $v['id'] ]['id'] );
						}
					}
					$models = $new_models;
				}
			}
			return $models;
		}



		/* GOOGLE AI API
		--------------------------------------------------------------------------------------- */

		/**
		 * Return a list of chat models for Google AI with max tokens for each model
		 * 
		 * @access public
		 * @static
		 * 
		 * @return array  	  The list of chat models for Google AI
		 */
		static function get_default_google_ai_chat_models() {
			return apply_filters( 'trx_addons_filter_ai_helper_google_ai_models', array(
				'gemini-pro' => array(
					'id' => 'gemini-pro',
					'title' => esc_html__( 'Gemini Pro', 'trx_addons' ),
					'max_tokens' => 30700,		// inputTokenLimit: 30720
					'output_tokens' => 2048,	// outputTokenLimit: 2048
				),
				'gemini-pro-vision' => array(
					'id' => 'gemini-pro-vision',
					'title' => esc_html__( 'Gemini Pro Vision', 'trx_addons' ),
					'max_tokens' => 12200,		// inputTokenLimit: 12288
					'output_tokens' => 4000,	// outputTokenLimit: 4096
				)
			) );
		}

		/**
		 * Return a list of chat models for Google Ai
		 * 
		 * @access public
		 * @static
		 * 
		 * @return array  	  The list of chat models for Google Ai
		 */
		static function get_google_ai_chat_models() {
			$models = array();
			$token = trx_addons_get_option( 'ai_helper_token_google_ai', '' );
			if ( ! empty( $token ) ) {
				$autoload = trx_addons_get_option( 'ai_helper_autoload_models_google_ai' );
				if ( (int)$autoload > 0 ) {
					$models = get_transient( "trx_addons_ai_helper_list_models_google_ai" );
					if ( ! is_array( $models ) || count( $models ) == 0 ) {
						$models = GoogleAi::instance()->list_models();
						if ( is_array( $models ) && count( $models ) > 0 ) {
							$new_models = array();
							foreach ( $models as $v ) {
								if ( ! empty( $v['name'] )
									&& ! empty( $v['displayName'] )
									&& ! empty( $v['supportedGenerationMethods'] )
									&& in_array( 'generateContent', $v['supportedGenerationMethods'] )
								) {
									$id = str_replace( 'models/', '', $v['name'] );
									$new_models[ $id ] = array(
										'id' => $id,
										'title' => $v['displayName'],
										'max_tokens' => ! empty( $v['inputTokenLimit'] )	// && ! empty( $v['outputTokenLimit'] )
															? (int)$v['inputTokenLimit']	// + (int)$v['outputTokenLimit']
															: 16000,
										'output_tokens' => ! empty( $v['outputTokenLimit'] )
															? (int)$v['outputTokenLimit']
															: 4000,
									);
								}
							}
							$models = $new_models;
						} else {
							$models = self::get_default_google_ai_chat_models();
						}
						set_transient( "trx_addons_ai_helper_list_models_google_ai", $models, 7 * 24 * 60 * 60 );	// 7 days
					}
				} else {
					$models = trx_addons_get_option( 'ai_helper_chat_models_google_ai', array() );
					if ( empty( $models ) || ! is_array( $models ) || empty( $models[0]['id'] ) ) {
						$models = self::get_default_google_ai_chat_models();
					} else {
						$new_models = array();
						foreach ( $models as $v ) {
							if ( ! empty( $v['id'] ) ) {
								$new_models[ $v['id'] ] = $v;
							}
						}
						$models = $new_models;
					}
				}
			}
			return $models;
		}

		/**
		 * Return a list of chat models for Google AI
		 * 
		 * @access public
		 * @static
		 * 
		 * @return array  	  The list of chat models for Google AI
		 */
		static function get_list_google_ai_chat_models() {
			return apply_filters( 'trx_addons_filter_ai_helper_list_google_ai_models', trx_addons_array_from_list( self::get_google_ai_chat_models() ) );
		}

	}
}
