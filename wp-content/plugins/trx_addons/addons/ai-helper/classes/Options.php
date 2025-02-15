<?php
namespace TrxAddons\AiHelper;

use TrxAddons\AiHelper\Utils;

if ( ! class_exists( 'Options' ) ) {

	/**
	 * Add options to the ThemeREX Addons Options
	 */
	class Options {

		/**
		 * Constructor
		 */
		function __construct() {
			add_filter( 'trx_addons_filter_options', array( $this, 'add_options' ) );
			add_filter( 'trx_addons_filter_before_show_options', array( $this, 'fill_options' ) );
			add_filter( 'trx_addons_filter_before_show_options', array( $this, 'fix_options' ) );
			add_filter( 'trx_addons_filter_export_options', array( $this, 'remove_token_from_export' ) );
			add_filter( 'trx_addons_filter_export_single_usermeta', array( $this, 'remove_chat_history_from_export' ), 10, 3 );
		}

		/**
		 * Add options to the ThemeREX Addons Options
		 * 
		 * @hooked trx_addons_filter_options
		 *
		 * @param array $options  Array of options
		 * 
		 * @return array  	  Modified array of options
		 */
		function add_options( $options ) {
			$is_options_page = trx_addons_get_value_gp( 'page' ) == 'trx_addons_options';

			// Get logs for the AI Helper
			$log_open_ai = $is_options_page ? Logger::instance()->get_log_report( 'open-ai') : '';
			$log_open_ai_assistants = $is_options_page ? Logger::instance()->get_log_report( 'open-ai-assistants') : '';
			$log_sd = $is_options_page ? Logger::instance()->get_log_report( 'stable-diffusion') : '';
			$log_stability_ai = $is_options_page ? Logger::instance()->get_log_report( 'stability-ai') : '';
			$log_flowise_ai = $is_options_page ? Logger::instance()->get_log_report( 'flowise-ai') : '';
			$log_google_ai = $is_options_page ? Logger::instance()->get_log_report( 'google-ai') : '';
			$log_lumalabs_ai = $is_options_page ? Logger::instance()->get_log_report( 'lumalabs-ai' ) : '';
			$log_add_support = '';
			$log_ai_assistant = $is_options_page ? Logger::instance()->get_log( 'trx-ai-assistants-support') : '';
			if ( is_array( $log_ai_assistant ) && count( $log_ai_assistant ) > 0 ) {
				krsort( $log_ai_assistant );
				$log_add_support .= '<div class="trx_addons_ai_helper_log_data"><pre><b><u>' . esc_html__( 'Adding periods of support:', 'trx_addons' ) . '</u></b>';
				foreach( $log_ai_assistant as $date => $msg ) {
					$log_add_support .= "\n" . esc_html( $date ) . '  ' . esc_html( trx_addons_strshort( ! empty( $msg['message'] ) ? $msg['message'] : $msg, 66, '&hellip;', false ) );
				}
				$log_add_support .= '</pre></div>';
			}

			// Create the object of the StableDiffusion API and set the API server (will be used in the options and when generating images)
			$sd_api_url = '';
			$sd_models_url = '';
			if ( $is_options_page ) {
				$sd_api = StableDiffusion::instance();
				$sd_api->set_api( trx_addons_get_option( 'ai_helper_use_api_stable_diffusion', 'sd' ) );
				$sd_api_url = $sd_api->get_url( 'settings/api' );
				$sd_models_url = $sd_api->get_url( 'models' );
			}

			trx_addons_array_insert_before( $options, 'users_section', apply_filters( 'trx_addons_filter_options_ai_helper', array(

				// Open panel "AI Helper"
				//---------------------------------------------------------------------
				'ai_helper_section' => array(
					"title" => esc_html__('AI Helper', 'trx_addons'),
					'icon' => 'trx_addons_icon-android',
					"type" => "panel"
				),

				// Common settings
				//---------------------------------------------------------------------
				'ai_helper_section_common' => array(
					"title" => esc_html__('Common settings', 'trx_addons'),
					"icon" => 'trx_addons_icon-tools',
					"type" => "section"
				),
				'ai_helper_trx_ai_assistants_info' => array(
					"title" => esc_html__('AI Assistant', 'trx_addons'),
					"desc" => wp_kses_data( __("AI Assistant display settings in the admin area, as well as extending the support period for using AI Assistant.", 'trx_addons') ),
					"type" => "info"
				),
				'ai_helper_trx_ai_assistants' => array(
					"title" => esc_html__('Allow AI Assistant', 'trx_addons'),
					"desc" => wp_kses_data( __('Allow the display of an intelligent assistant in the admin area that can display and change some theme settings, as well as answer questions related to theme customization.', 'trx_addons') ),
					"std" => "1",
					"type" => "switch",
				),
				'ai_helper_trx_ai_assistants_add_support' => array(
					"title" => esc_html__('Extend support', 'trx_addons'),
					"desc" => wp_kses_data( __('Extend the support period for using AI Assistant.', 'trx_addons') ) . $log_add_support,
					"caption" => esc_html__('Add a new support key', 'trx_addons'),
					"icon" => 'trx_addons_icon-key',
					"std" => "",
					"callback" => "trx_addons_ai_assistant_add_support",
					"type" => "button",
				),
				'ai_helper_common_info' => array(
					"title" => esc_html__('Common settings', 'trx_addons'),
					"desc" => wp_kses_data( __("Default model for text generations, settings for a selected text processing, etc.", 'trx_addons') ),
					"type" => "info"
				),
				'ai_helper_text_model_default' => array(
					"title" => esc_html__('Default text model', 'trx_addons'),
					"desc" => wp_kses_data( __('Select a text model to use as default for AI actions such as translation, process selected text, etc.', 'trx_addons') )
							. '<br />'
							. wp_kses_data( __("Attention! If the list of models is empty - it means that you have not connected any API for text generation. You need to specify an access token for at least one of the supported APIs - Open AI (preferably), Google AI or Flowise AI.", 'trx_addons') ),
					"std" => "",
					"options" => apply_filters( 'trx_addons_filter_ai_helper_list_models', array_merge( array( '' => __( '- Not selected -', 'trx_addons' ) ), $is_options_page ? Lists::get_list_ai_text_models() : array() ), 'text_model' ),
					"type" => "select",
				),
				// Leave name 'ai_helper_system_prompt_openai' for compatibility with old versions
				'ai_helper_system_prompt_openai' => array(
					"title" => esc_html__('System Prompt', 'trx_addons'),
					"desc" => wp_kses_data( __('System instructions for the AI Helper in the post editor. They serve as a guide for choosing the style of communication on the part of the AI.', 'trx_addons') ),
					"std" => __( 'You are an assistant for writing posts. Return only the result without any additional messages. Format the response with HTML tags.', 'trx_addons' ),
					"type" => "textarea",
					'dependency' => array(
						'ai_helper_text_model_default' => array( 'not_empty' )
					)
				),
				'ai_helper_process_selected_info' => array(
					"title" => esc_html__('Process selected text', 'trx_addons'),
					"desc" => wp_kses_data( __("Select post types and URL masks (optional) to add functionality to process (explain, summarize, translate) selected text", 'trx_addons') ),
					"type" => "info",
					'dependency' => array(
						'ai_helper_text_model_default' => array( 'not_empty' )
					)
				),
				'ai_helper_process_selected' => array(
					"title" => esc_html__('Process selected text', 'trx_addons'),
					"desc" => wp_kses_data( __('Add functionality to process (explain, summarize, translate) selected text for specified post types and/or URLs.', 'trx_addons') ),
					"std" => "1",
					"type" => "switch",
					"dependency" => array(
						'ai_helper_text_model_default' => array( 'not_empty' )
					),
				),
				'ai_helper_process_selected_post_types' => array(
					"title" => esc_html__("Post types", 'trx_addons'),
					"desc" => '',
					"dir" => 'horizontal',
					"std" => array( 'post' => 1 ),
					"options" => array(),
					"type" => "checklist",
					'dependency' => array(
						'ai_helper_process_selected' => array( '1' ),
						'ai_helper_text_model_default' => array( 'not_empty' )
					)
				),
				"ai_helper_process_selected_url_include" => array(
					"title" => esc_html__("URL include", 'trx_addons'),
					"desc" => wp_kses_data( __("'Process selected text' functionality will be enabled for pages that match URL parts listed here (comma-separated or each on a separate line)", 'trx_addons') ),
					"std" => "",
					"rows" => 10,
					"type" => "textarea",
					'dependency' => array(
						'ai_helper_process_selected' => array( '1' ),
						'ai_helper_text_model_default' => array( 'not_empty' )
					)
				),
				"ai_helper_process_selected_url_exclude" => array(
					"title" => esc_html__("URL exclude", 'trx_addons'),
					"desc" => wp_kses_data( __("'Process selected text' functionality will be disabled for pages that match URL parts listed here (comma-separated or each on a separate line)", 'trx_addons') ),
					"std" => "",
					"rows" => 10,
					"type" => "textarea",
					'dependency' => array(
						'ai_helper_process_selected' => array( '1' ),
						'ai_helper_text_model_default' => array( 'not_empty' )
					)
				),

				// AI Website Content Generator (Beta)
				//---------------------------------------------------------------------
				'ai_helper_section_content_generator' => array(
					"title" => esc_html__('AI Content Generator', 'trx_addons'),
					'icon' => 'trx_addons_icon-newspaper',
					"type" => "section"
				),
				'ai_helper_company_info' => array(
					"title" => esc_html__('AI Website Content Generator (Beta): Company Info', 'trx_addons'),
					"desc" => wp_kses_data( __("Provide a data about your company (name, industry, contacts, description/mission/history) that will be used as context for text generation and filling widget fields in the page editor.", 'trx_addons') ),
					"type" => "info",
					"dependency" => array(
						"ai_helper_token_openai" => array('not_empty')
					),
				),
				'ai_helper_company_info_disabled' => array(
					"title" => esc_html__('AI Website Content Generator (Beta): Company Info', 'trx_addons'),
					"desc" => wp_kses_data( __("Provide a data about your company (name, industry, contacts, description/mission/history) that will be used as context for text generation and filling widget fields in the page editor.", 'trx_addons') )
								. '<br />'
								. '<span class="trx_addons_options_warning">'
									. wp_kses_data( __("Please add your Open AI API key in the section 'AI Helper - Open AI API'.", 'trx_addons') )
								. '</span>',
					"type" => "info",
					"dependency" => array(
						"ai_helper_token_openai" => array('is_empty')
					),
				),
				'ai_helper_company_name' => array(
					'title' => esc_html__('Company name', 'trx_addons'),
					'desc' => '',
					'std' => '',
					'type' => 'text',
					'title_class' => 'required',
					"class" => "trx_addons_column-1_3",
					"dependency" => array(
						"ai_helper_token_openai" => array('not_empty')
					),
				),
				'ai_helper_company_industry' => array(
					'title' => esc_html__('Industry', 'trx_addons'),
					'desc' => '',
					'std' => '',
					'type' => 'text',
					'title_class' => 'required',
					"class" => "trx_addons_column-1_3",
					"dependency" => array(
						"ai_helper_token_openai" => array('not_empty')
					),
				),
				'ai_helper_company_generator' => array(
					'title' => esc_html__( 'Company Demo Data', 'trx_addons' ),
					'desc' => '',
					'std' => 'trx_addons_ai_helper_company_generator',	// This value is similar to the 'action' value
					'type' => 'button',
					'class_field' => 'trx_addons_button_accent',
					'caption' => esc_html__( 'Generate demo content', 'trx_addons' ),
					'fields' => array( 'ai_helper_company_name', 'ai_helper_company_industry' ),	// Fields to send to the server for processing the data. Can be a comma-separated list of field names or an array of field names
					// 'action' => 'trx_addons_ai_helper_company_generator',
					'callback' => 'trx_addons_ai_helper_company_generator',
					"class" => "trx_addons_column-1_3",
					"dependency" => array(
						"ai_helper_token_openai" => array('not_empty')
					),
				),
				'ai_helper_company_address' => array(
					'title' => esc_html__('Address', 'trx_addons'),
					'desc' => '',
					'std' => '',
					'type' => 'text',
					"class" => "trx_addons_column-1_3 trx_addons_new_row",
					"dependency" => array(
						"ai_helper_token_openai" => array('not_empty')
					),
				),
				'ai_helper_company_phone' => array(
					'title' => esc_html__('Phone', 'trx_addons'),
					'desc' => '',
					'std' => '',
					'type' => 'text',
					"class" => "trx_addons_column-1_3",
					"dependency" => array(
						"ai_helper_token_openai" => array('not_empty')
					),
				),
				'ai_helper_company_email' => array(
					'title' => esc_html__('E-mail', 'trx_addons'),
					'desc' => '',
					'std' => '',
					'type' => 'text',
					"class" => "trx_addons_column-1_3",
					"dependency" => array(
						"ai_helper_token_openai" => array('not_empty')
					),
				),
				'ai_helper_company_description' => array(
					'title' => esc_html__('Description', 'trx_addons'),
					'desc' => wp_kses_data( __("A brief description of the company (up to 250 words), outlining its mission, values, and main areas of activity.", 'trx_addons') ),
					'std' => '',
					'rows' => 10,
					'type' => 'textarea',
					"class" => "trx_addons_column-1_2 trx_addons_new_row",
					"dependency" => array(
						"ai_helper_token_openai" => array('not_empty')
					),
				),
				'ai_helper_company_mission' => array(
					'title' => esc_html__('Mission', 'trx_addons'),
					'desc' => wp_kses_data( __("The company's mission. A brief statement (up to 50 words) that reflects the main goal and philosophy of the company.", 'trx_addons') ),
					'std' => '',
					'rows' => 10,
					'type' => 'textarea',
					"class" => "trx_addons_column-1_2",
					"dependency" => array(
						"ai_helper_token_openai" => array('not_empty')
					),
				),
				'ai_helper_company_history' => array(
					'title' => esc_html__('History', 'trx_addons'),
					'desc' => wp_kses_data( __("A brief history of the company (up to 200 words), including the year of founding, key milestones, and achievements.", 'trx_addons') ),
					'std' => '',
					'rows' => 10,
					'type' => 'textarea',
					"class" => "trx_addons_column-1_2 trx_addons_new_row",
					"dependency" => array(
						"ai_helper_token_openai" => array('not_empty')
					),
				),
				'ai_helper_company_values' => array(
					'title' => esc_html__('Values', 'trx_addons'),
					'desc' => wp_kses_data( __("A key value or principle of the company. Each item must start on a new line and should be concise, meaningful, and reflect the company's culture and priorities.", 'trx_addons') ),
					'std' => '',
					'rows' => 10,
					'type' => 'textarea',
					"class" => "trx_addons_column-1_2",
					"dependency" => array(
						"ai_helper_token_openai" => array('not_empty')
					),
				),
				'ai_helper_company_services' => array(
					"title" => esc_html__("List of company services", 'trx_addons'),
					"desc" => '',
					"clone" => true,
					"std" => array(),
					"type" => "group",
					"dependency" => array(
						"ai_helper_token_openai" => array('not_empty')
					),
					"fields" => array(
						"name" => array(
							"title" => esc_html__("Service Name", 'trx_addons'),
							"desc" => wp_kses_data( __("The name of the service. It should be specific, reflect the essence of the provided service, and match the company's industry.", 'trx_addons') ),
							"class" => "trx_addons_column-1_3",
							"std" => "",
							"type" => "text"
						),
						"description" => array(
							"title" => esc_html__("Description", 'trx_addons'),
							"desc" => wp_kses_data( __("A detailed description of the service (up to 150 words), explaining what problem it solves and the benefits it brings to clients.", 'trx_addons') ),
							"class" => "trx_addons_column-1_3",
							"std" => "",
							"type" => "textarea"
						),
						"features" => array(
							"title" => esc_html__("Features", 'trx_addons'),
							"desc" => wp_kses_data( __("A key feature or advantage of the service. Each item must start on a new line and should be concise (up to 20 words), highlight unique offerings, and match the service description.", 'trx_addons') ),
							"class" => "trx_addons_column-1_3",
							"std" => '',
							"type" => "textarea"
						),
					)
				),
				'ai_helper_company_team' => array(
					"title" => esc_html__("Team members", 'trx_addons'),
					"desc" => '',
					"clone" => true,
					"std" => array(),
					"type" => "group",
					"dependency" => array(
						"ai_helper_token_openai" => array('not_empty')
					),
					"fields" => array(
						"name" => array(
							"title" => esc_html__("Employee Name", 'trx_addons'),
							"desc" => wp_kses_data( __("The full name of the employee.", 'trx_addons') ),
							"class" => "trx_addons_column-1_3",
							"std" => "",
							"type" => "text"
						),
						"position" => array(
							"title" => esc_html__("Position", 'trx_addons'),
							"desc" => wp_kses_data( __("The employee's position in the company, reflecting their role and responsibilities (e.g., 'CEO', 'Marketing Manager').", 'trx_addons') ),
							"class" => "trx_addons_column-1_3",
							"std" => "",
							"type" => "text"
						),
						"bio" => array(
							"title" => esc_html__("Bio", 'trx_addons'),
							"desc" => wp_kses_data( __("A brief biography of the employee (up to 100 words), including experience, achievements, and key skills.", 'trx_addons') ),
							"class" => "trx_addons_column-1_3",
							"std" => '',
							"type" => "textarea"
						),
					)
				),

				// Open AI API settings
				//---------------------------------------------------------------------
				'ai_helper_section_openai' => array(
					"title" => esc_html__('Open AI API', 'trx_addons'),
					"icon" => TRX_ADDONS_PLUGIN_ADDONS . 'ai-helper/images/icons/openai.svg',
					"type" => "section"
				),
				'ai_helper_info_openai' => array(
					"title" => esc_html__('Open AI', 'trx_addons'),
					"desc" => wp_kses_data( __("Settings of the AI Helper for Open AI API", 'trx_addons') )
							. ( ! empty( $log_open_ai ) ? wp_kses( $log_open_ai, 'trx_addons_kses_content' ) : '' ),
					"type" => "info"
				),
				'ai_helper_token_openai' => array(
					"title" => esc_html__('Token', 'trx_addons'),
					"desc" => wp_kses( sprintf(
													__('Specify a token to use the OpenAi API. You can generate a token in your personal account using the link %s', 'trx_addons'),
													apply_filters( 'trx_addons_filter_openai_api_key_url',
																	'<a href="' . esc_url( OpenAi::instance()->get_url( 'account/api-keys' ) ) . '" target="_blank">' . esc_url( OpenAi::instance()->get_url( 'account/api-keys' ) ) . '</a>'
																)
												),
										'trx_addons_kses_content'
									),
					"std" => "",
					"type" => "text"
				),
				'ai_helper_proxy_openai' => array(
					"title" => esc_html__('Proxy URL', 'trx_addons'),
					"desc" => wp_kses_data( __('Specify the address of the proxy-server (if needed).', 'trx_addons') ),
					"std" => "",
					"type" => "text",
					"dependency" => array(
						"ai_helper_token_openai" => array('not_empty')
					),
				),
				'ai_helper_proxy_auth_openai' => array(
					"title" => esc_html__('Proxy Auth', 'trx_addons'),
					"desc" => wp_kses_data( __('Specify the login and password to access a proxy-server (if needed) in format login:password', 'trx_addons') ),
					"std" => "",
					"type" => "text",
					"dependency" => array(
						"ai_helper_token_openai" => array('not_empty')
					),
				),
				'ai_helper_temperature_openai' => array(
					"title" => esc_html__('Temperature', 'trx_addons'),
					"desc" => wp_kses_data( __('Select a temperature to use with OpenAi API queries in the editor.', 'trx_addons') )
							. '<br />'
							. wp_kses_data( __('What sampling temperature to use, between 0 and 2. Higher values like 0.8 will make the output more random, while lower values like 0.2 will make it more focused and deterministic.', 'trx_addons') ),
					"std" => 1.0,
					"min" => 0,
					"max" => 2.0,
					"step" => 0.1,
					"type" => "slider",
					"dependency" => array(
						"ai_helper_token_openai" => array('not_empty')
					),
				),
				'ai_helper_chat_models_openai_batch' => array(
					"title" => esc_html__("Chat Models", 'trx_addons'),
					"dependency" => array(
						"ai_helper_token_openai" => array('not_empty')
					),
					"type" => "batch"
				),
				'ai_helper_chat_models_openai' => array(
					"title" => esc_html__("List of available chat models", 'trx_addons'),
					"desc" => wp_kses_data( __("Specify the id and name (title) of the model, and the maximum number of tokens the model is capable of processing on input and issuing as a response.", 'trx_addons') ),
					"dependency" => array(
						"ai_helper_token_openai" => array('not_empty')
					),
					"clone" => true,
					"std" => trx_addons_list_from_array( Lists::get_default_openai_chat_models() ),
					"type" => "group",
					"fields" => array(
						"id" => array(
							"title" => esc_html__("Model ID", 'trx_addons'),
							"class" => "trx_addons_column-1_4",
							"std" => "",
							"type" => "text"
						),
						"title" => array(
							"title" => esc_html__("Title", 'trx_addons'),
							"class" => "trx_addons_column-1_4",
							"std" => "",
							"type" => "text"
						),
						"max_tokens" => array(
							"title" => esc_html__("Input tokens", 'trx_addons'),
							"class" => "trx_addons_column-1_4",
							"std" => 4000,
							"min" => 0,
							"max" => Utils::get_default_max_tokens(),
							"step" => 100,
							"type" => "slider"
						),
						"output_tokens" => array(
							"title" => esc_html__("Output tokens", 'trx_addons'),
							"class" => "trx_addons_column-1_4",
							"std" => 4000,
							"min" => 0,
							"max" => Utils::get_default_max_tokens(),
							"step" => 100,
							"type" => "slider"
						),
					)
				),
				'ai_helper_image_models_openai_batch' => array(
					"title" => esc_html__("Image Models", 'trx_addons'),
					"type" => "batch"
				),
				'ai_helper_models_openai' => array(
					"title" => esc_html__("List of available image models", 'trx_addons'),
					"desc" => wp_kses_data( __("Specify an id and name (title) for each new model.", 'trx_addons') ),
					"dependency" => array(
						"ai_helper_token_openai" => array('not_empty')
					),
					"clone" => true,
					"std" => trx_addons_list_from_array( Lists::get_default_openai_models() ),
					"type" => "group",
					"fields" => array(
						"id" => array(
							"title" => esc_html__("Model ID", 'trx_addons'),
							"class" => "trx_addons_column-1_2",
							"std" => "",
							"type" => "text"
						),
						"title" => array(
							"title" => esc_html__("Title", 'trx_addons'),
							"class" => "trx_addons_column-1_2",
							"std" => "",
							"type" => "text"
						),
					)
				),
				'ai_helper_tts_models_openai_batch' => array(
					"title" => esc_html__("Text To Speech Models", 'trx_addons'),
					"type" => "batch"
				),
				'ai_helper_tts_models_openai' => array(
					"title" => esc_html__("List of available TTS models", 'trx_addons'),
					"desc" => wp_kses_data( __("Specify an id and name (title) for each new model for Text To Speech.", 'trx_addons') ),
					"dependency" => array(
						"ai_helper_token_openai" => array('not_empty')
					),
					"clone" => true,
					"std" => trx_addons_list_from_array( Lists::get_default_openai_audio_models( 'tts' ) ),
					"type" => "group",
					"fields" => array(
						"id" => array(
							"title" => esc_html__("Model ID", 'trx_addons'),
							"class" => "trx_addons_column-1_2",
							"std" => "",
							"type" => "text"
						),
						"title" => array(
							"title" => esc_html__("Title", 'trx_addons'),
							"class" => "trx_addons_column-1_2",
							"std" => "",
							"type" => "text"
						),
					)
				),
				'ai_helper_transcription_models_openai_batch' => array(
					"title" => esc_html__("Transcription Models", 'trx_addons'),
					"type" => "batch"
				),
				'ai_helper_transcription_models_openai' => array(
					"title" => esc_html__("List of available transcription models", 'trx_addons'),
					"desc" => wp_kses_data( __("Specify an id and name (title) for each new model for the audio transcription.", 'trx_addons') ),
					"dependency" => array(
						"ai_helper_token_openai" => array('not_empty')
					),
					"clone" => true,
					"std" => trx_addons_list_from_array( Lists::get_default_openai_audio_models( 'transcription' ) ),
					"type" => "group",
					"fields" => array(
						"id" => array(
							"title" => esc_html__("Model ID", 'trx_addons'),
							"class" => "trx_addons_column-1_2",
							"std" => "",
							"type" => "text"
						),
						"title" => array(
							"title" => esc_html__("Title", 'trx_addons'),
							"class" => "trx_addons_column-1_2",
							"std" => "",
							"type" => "text"
						),
					)
				),
				'ai_helper_translation_models_openai_batch' => array(
					"title" => esc_html__("Translation Models", 'trx_addons'),
					"type" => "batch"
				),
				'ai_helper_translation_models_openai' => array(
					"title" => esc_html__("List of available translation models", 'trx_addons'),
					"desc" => wp_kses_data( __("Specify an id and name (title) for each new model for the audio translation.", 'trx_addons') ),
					"dependency" => array(
						"ai_helper_token_openai" => array('not_empty')
					),
					"clone" => true,
					"std" => trx_addons_list_from_array( Lists::get_default_openai_audio_models( 'translation' ) ),
					"type" => "group",
					"fields" => array(
						"id" => array(
							"title" => esc_html__("Model ID", 'trx_addons'),
							"class" => "trx_addons_column-1_2",
							"std" => "",
							"type" => "text"
						),
						"title" => array(
							"title" => esc_html__("Title", 'trx_addons'),
							"class" => "trx_addons_column-1_2",
							"std" => "",
							"type" => "text"
						),
					)
				),
				'ai_helper_models_openai_batch_end' => array(
					"type" => "batch_end"
				),

				// Open AI Assistants API settings
				//---------------------------------------------------------------------
				'ai_helper_section_openai_assistants' => array(
					"title" => esc_html__('Open AI Assistants', 'trx_addons'),
					"icon" => TRX_ADDONS_PLUGIN_ADDONS . 'ai-helper/images/icons/openai.png',
					"type" => "section"
				),
				'ai_helper_info_openai_assistants' => array(
					"title" => esc_html__('Open AI Assistants', 'trx_addons'),
					"desc" => wp_kses_data( __("A list of assistants created in the GPT4 Plus user account and available for use as an embedded chatbot and/or model in the AI Chat shortcode.", 'trx_addons') )
							. ( ! empty( $log_open_ai_assistants ) ? wp_kses( $log_open_ai_assistants, 'trx_addons_kses_content' ) : '' ),
					"type" => "info"
				),
				'ai_helper_default_api_openai_assistants' => array(
					"title" => esc_html__('Assistants API version', 'trx_addons'),
					"desc" => wp_kses_data( __('Which API version will be used to access assistants? Some newer models, such as the GPT-4o, only support API v2.', 'trx_addons') ),
					"std" => "v2",
					"options" => array(
						"v2" => esc_html__("V2 (New)", 'trx_addons'),
						"v1" => esc_html__("V1 (Legacy)", 'trx_addons'),
					),
					"type" => "radio",
					"dependency" => array(
						"ai_helper_token_openai" => array('not_empty')
					),
				),
				'ai_helper_models_openai_assistants' => array(
					"title" => esc_html__("List of available assistants", 'trx_addons'),
					"desc" => wp_kses_data( __("Specify an id and name (title) for each new assistant.", 'trx_addons') ),
					"dependency" => array(
						"ai_helper_token_openai" => array('not_empty')
					),
					"clone" => true,
					"std" => array(),
					"type" => "group",
					"fields" => array(
						"id" => array(
							"title" => esc_html__("Assistant ID", 'trx_addons'),
							"class" => "trx_addons_column-1_2",
							"std" => "",
							"type" => "text"
						),
						"title" => array(
							"title" => esc_html__("Title", 'trx_addons'),
							"class" => "trx_addons_column-1_2",
							"std" => "",
							"type" => "text"
						),
					)
				),

				// ModelsLab (ex Stable Diffusion) API settings
				//---------------------------------------------------------------------
				'ai_helper_section_stable_diffusion' => array(
					"title" => esc_html__('ModelsLab API', 'trx_addons'),
					"icon" => TRX_ADDONS_PLUGIN_ADDONS . 'ai-helper/images/icons/modelslab.png',
					"type" => "section"
				),
				'ai_helper_info_stable_diffusion' => array(
					"title" => esc_html__('ModelsLab (ex Stable Diffusion)', 'trx_addons'),
					"desc" => wp_kses_data( __("Settings of the AI Helper for ModelsLab (ex Stable Diffusion) API", 'trx_addons') )
							. ( ! empty( $log_sd ) ? wp_kses( $log_sd, 'trx_addons_kses_content' ) : '' ),
					"type" => "info"
				),
				'ai_helper_token_stable_diffusion' => array(
					"title" => esc_html__('Token', 'trx_addons'),
					"desc" => wp_kses( sprintf(
													__('Specify a token to use the ModelsLab (ex Stable Diffusion) API. You can generate a token in your personal account using the link %s', 'trx_addons'),
													apply_filters( 'trx_addons_filter_stable_diffusion_api_key_url',
																	'<a href="' . esc_url( $sd_api_url ) . '" target="_blank">' . esc_html( $sd_api_url ) . '</a>'
																)
												),
										'trx_addons_kses_content'
									),
					"std" => "",
					"type" => "text"
				),
				'ai_helper_use_api_stable_diffusion' => array(
					"title" => esc_html__('Use API server', 'trx_addons'),
					"desc" => wp_kses_data( __('Which server should be used for accessing the API - modelslab.com (new) or stablediffusionapi.com (legacy)?', 'trx_addons') ),
					"std" => "ml",
					"options" => array(
						"ml" => esc_html__("ModelsLab (New)", 'trx_addons'),
						"sd" => esc_html__("Stable Diffusion (Legacy)", 'trx_addons'),
					),
					"type" => "radio",
					"dependency" => array(
						"ai_helper_token_stable_diffusion" => array('not_empty')
					),
				),
				'ai_helper_default_api_stable_diffusion' => array(
					"title" => esc_html__('Default SD model endpoint', 'trx_addons'),
					"desc" => wp_kses_data( __('Which endpoint should be used for accessing the default StableDiffusion model on the ModelsLab API server?', 'trx_addons') ),
					"std" => "v6",
					"options" => array(
						"v6" => esc_html__("V6 (New)", 'trx_addons'),
						"v3" => esc_html__("V3/V4 (Legacy)", 'trx_addons'),
					),
					"type" => "radio",
					"dependency" => array(
						"ai_helper_token_stable_diffusion" => array('not_empty'),
						"ai_helper_use_api_stable_diffusion" => array('ml')
					),
				),
				'ai_helper_guidance_scale_stable_diffusion' => array(
					"title" => esc_html__('Guidance scale', 'trx_addons'),
					"desc" => wp_kses_data( __('Scale for classifier-free guidance.', 'trx_addons') ),
					"std" => 7.5,
					"min" => 1,
					"max" => 20,
					"step" => 0.1,
					"type" => "slider",
					"dependency" => array(
						"ai_helper_token_stable_diffusion" => array('not_empty')
					),
				),
				'ai_helper_inference_steps_stable_diffusion' => array(
					"title" => esc_html__('Inference steps', 'trx_addons'),
					"desc" => wp_kses_data( __('Number of denoising steps. Available values: 21, 31, 41, 51.', 'trx_addons') ),
					"std" => 21,
					"min" => 21,
					"max" => 51,
					"step" => 10,
					"type" => "slider",
					"dependency" => array(
						"ai_helper_token_stable_diffusion" => array('not_empty')
					),
				),
				'ai_helper_autoload_models_stable_diffusion' => array(
					"title" => esc_html__('Autoload a list of models', 'trx_addons'),
					"desc" => wp_kses_data( __('Automatically load the model list from the API or maintain a manual model list.', 'trx_addons') ),
					"std" => "0",
					"type" => "switch",
					"dependency" => array(
						"ai_helper_token_stable_diffusion" => array('not_empty')
					),
				),
				'ai_helper_models_stable_diffusion' => array(
					"title" => esc_html__("List of available image models", 'trx_addons'),
					"desc" => wp_kses(
								sprintf(
									__("Specify an id and name (title) for each new model. A complete list of available models can be found at %s", 'trx_addons'),
									'<a href="' . esc_url( $sd_models_url ) . '" target="_blank">' . esc_html( $sd_models_url ) . '</a>'
								),
								'trx_addons_kses_content'
							),
					"dependency" => array(
						"ai_helper_token_stable_diffusion" => array('not_empty'),
						"ai_helper_autoload_models_stable_diffusion" => array('0')
					),
					"clone" => true,
					"std" => trx_addons_list_from_array( Lists::get_default_sd_models() ),
					"type" => "group",
					"fields" => array(
						"id" => array(
							"title" => esc_html__("Model ID", 'trx_addons'),
							"class" => "trx_addons_column-1_2",
							"std" => "",
							"type" => "text"
						),
						"title" => array(
							"title" => esc_html__("Title", 'trx_addons'),
							"class" => "trx_addons_column-1_2",
							"std" => "",
							"type" => "text"
						),
					)
				),

				// Stability AI API settings
				//---------------------------------------------------------------------
				'ai_helper_section_stability_ai' => array(
					"title" => esc_html__('Stability AI API', 'trx_addons'),
					"icon" => TRX_ADDONS_PLUGIN_ADDONS . 'ai-helper/images/icons/stability-ai.png',
					"type" => "section"
				),
				'ai_helper_info_stability_ai' => array(
					"title" => esc_html__('Stability AI', 'trx_addons'),
					"desc" => wp_kses_data( __("Settings of the AI Helper for Stability AI API", 'trx_addons') )
							. ( ! empty( $log_stability_ai ) ? wp_kses( $log_stability_ai, 'trx_addons_kses_content' ) : '' ),
					"type" => "info"
				),
				'ai_helper_token_stability_ai' => array(
					"title" => esc_html__('Token', 'trx_addons'),
					"desc" => wp_kses( sprintf(
													__('Specify a token to use the Stability AI API. You can generate a token in your personal account using the link %s', 'trx_addons'),
													apply_filters( 'trx_addons_filter_stability_ai_api_key_url',
																	'<a href="' . esc_url( StabilityAi::instance()->get_url( 'account/keys' ) ) . '" target="_blank">' . esc_url( StabilityAi::instance()->get_url( 'account/keys' ) ) . '</a>'
																)
												),
										'trx_addons_kses_content'
									),
					"std" => "",
					"type" => "text"
				),
				'ai_helper_prompt_weight_stability_ai' => array(
					"title" => esc_html__('Prompt weight', 'trx_addons'),
					"desc" => wp_kses_data( __('A weight of the text prompt.', 'trx_addons') ),
					"std" => 1.0,
					"min" => 0.1,
					"max" => 1.0,
					"step" => 0.1,
					"type" => "slider",
					"dependency" => array(
						"ai_helper_token_stability_ai" => array('not_empty')
					),
				),
				'ai_helper_cfg_scale_stability_ai' => array(
					"title" => esc_html__('Cfg scale', 'trx_addons'),
					"desc" => wp_kses_data( __('How strictly the diffusion process adheres to the prompt text (higher values keep your image closer to your prompt).', 'trx_addons') ),
					"std" => 7,
					"min" => 0,
					"max" => 35,
					"step" => 0.1,
					"type" => "slider",
					"dependency" => array(
						"ai_helper_token_stability_ai" => array('not_empty')
					),
				),
				'ai_helper_diffusion_steps_stability_ai' => array(
					"title" => esc_html__('Diffusion steps', 'trx_addons'),
					"desc" => wp_kses_data( __('Number of diffusion steps to run.', 'trx_addons') ),
					"std" => 50,
					"min" => 10,
					"max" => 150,
					"step" => 10,
					"type" => "slider",
					"dependency" => array(
						"ai_helper_token_stability_ai" => array('not_empty')
					),
				),
				'ai_helper_autoload_models_stability_ai' => array(
					"title" => esc_html__('Autoload a list of models', 'trx_addons'),
					"desc" => wp_kses_data( __('Automatically load the model list from the API or maintain a manual model list.', 'trx_addons') ),
					"std" => "0",
					"type" => "switch",
					"dependency" => array(
						"ai_helper_token_stability_ai" => array('not_empty')
					),
				),
				'ai_helper_models_stability_ai' => array(
					"title" => esc_html__("List of available image models", 'trx_addons'),
					"desc" => wp_kses(
								sprintf(
									__("Specify id and name (title) for each new model. A complete list of available models can be found at %s", 'trx_addons'),
									'<a href="' . esc_url( StabilityAi::instance()->get_url( 'pricing' ) ) . '" target="_blank">' . esc_url( StabilityAi::instance()->get_url( 'pricing' ) ) . '</a>'
								),
								'trx_addons_kses_content'
							),
					"dependency" => array(
						"ai_helper_token_stability_ai" => array('not_empty'),
						"ai_helper_autoload_models_stability_ai" => array('0')
					),
					"clone" => true,
					"std" => trx_addons_list_from_array( Lists::get_default_stability_ai_models() ),
					"type" => "group",
					"fields" => array(
						"id" => array(
							"title" => esc_html__("Model ID", 'trx_addons'),
							"class" => "trx_addons_column-1_2",
							"std" => "",
							"type" => "text"
						),
						"title" => array(
							"title" => esc_html__("Title", 'trx_addons'),
							"class" => "trx_addons_column-1_2",
							"std" => "",
							"type" => "text"
						),
					)
				),

				// Flowise AI API settings
				//---------------------------------------------------------------------
				'ai_helper_section_flowise_ai' => array(
					"title" => esc_html__('Flowise AI API', 'trx_addons'),
					"icon" => TRX_ADDONS_PLUGIN_ADDONS . 'ai-helper/images/icons/flowise-ai.png',
					"type" => "section"
				),
				'ai_helper_info_flowise_ai' => array(
					"title" => esc_html__('Flowise AI', 'trx_addons'),
					"desc" => wp_kses_data( __("Settings of the AI Helper for Flowise AI API", 'trx_addons') )
							. ( ! empty( $log_flowise_ai ) ? wp_kses( $log_flowise_ai, 'trx_addons_kses_content' ) : '' ),
					"type" => "info"
				),
				'ai_helper_token_flowise_ai' => array(
					"title" => esc_html__('API key', 'trx_addons'),
					"desc" => wp_kses_data( __('Specify a key to use the Flowise AI API. You can get a key in the Flowise Dashboard - API keys', 'trx_addons') ),
					"std" => "",
					"type" => "text"
				),
				'ai_helper_host_flowise_ai' => array(
					"title" => esc_html__('Flowise AI host URL', 'trx_addons'),
					"desc" => wp_kses_data( __('Specify the address of the server on which Flowise AI is deployed', 'trx_addons') ),
					"std" => "",
					"type" => "text",
					"dependency" => array(
						"ai_helper_token_flowise_ai" => array('not_empty')
					),
				),
				'ai_helper_models_flowise_ai' => array(
					"title" => esc_html__("List of available chat flows", 'trx_addons'),
					"desc" => wp_kses_data( __("Specify id and title for each new chat flow.", 'trx_addons') ),
					"dependency" => array(
						"ai_helper_token_flowise_ai" => array('not_empty')
					),
					"clone" => true,
					"std" => array(),
					"type" => "group",
					"fields" => array(
						"id" => array(
							"title" => esc_html__("Flow ID", 'trx_addons'),
							"class" => "trx_addons_column-1_4",
							"std" => "",
							"type" => "text"
						),
						"title" => array(
							"title" => esc_html__("Title", 'trx_addons'),
							"class" => "trx_addons_column-1_4",
							"std" => "",
							"type" => "text"
						),
						"max_tokens" => array(
							"title" => esc_html__("Input tokens", 'trx_addons'),
							"class" => "trx_addons_column-1_4",
							"std" => "4000",
							"type" => "text"
						),
						"output_tokens" => array(
							"title" => esc_html__("Output tokens", 'trx_addons'),
							"class" => "trx_addons_column-1_4",
							"std" => "4000",
							"type" => "text"
						),
					)
				),

				// Google AI API settings
				//---------------------------------------------------------------------
				'ai_helper_section_google_ai' => array(
					"title" => esc_html__('Google AI API', 'trx_addons'),
					"icon" => TRX_ADDONS_PLUGIN_ADDONS . 'ai-helper/images/icons/google-ai.png',
					"type" => "section"
				),
				'ai_helper_info_google_ai' => array(
					"title" => esc_html__('Google AI (Gemini)', 'trx_addons'),
					"desc" => wp_kses_data( __("Settings of the AI Helper for Google AI API", 'trx_addons') )
							. ( ! empty( $log_google_ai ) ? wp_kses( $log_google_ai, 'trx_addons_kses_content' ) : '' ),
					"type" => "info"
				),
				'ai_helper_token_google_ai' => array(
					"title" => esc_html__('API key', 'trx_addons'),
					"desc" => wp_kses( sprintf(
												__('Specify a token to use the Google AI API. You can generate a token in your personal account using the link %s', 'trx_addons'),
												apply_filters( 'trx_addons_filter_google_ai_api_key_url',
																'<a href="https://makersuite.google.com/app/apikey" target="_blank">https://makersuite.google.com/app/apikey</a>'
															)
											),
									'trx_addons_kses_content'
								),
					"std" => "",
					"type" => "text"
				),
				'ai_helper_proxy_google_ai' => array(
					"title" => esc_html__('Proxy URL', 'trx_addons'),
					"desc" => wp_kses_data( __('Specify the address of the proxy-server (if needed).', 'trx_addons') ),
					"std" => "",
					"type" => "text",
					"dependency" => array(
						"ai_helper_token_google_ai" => array('not_empty')
					),
				),
				'ai_helper_proxy_auth_google_ai' => array(
					"title" => esc_html__('Proxy Auth', 'trx_addons'),
					"desc" => wp_kses_data( __('Specify the login and password to access a proxy-server (if needed) in format login:password', 'trx_addons') ),
					"std" => "",
					"type" => "text",
					"dependency" => array(
						"ai_helper_token_openai" => array('not_empty')
					),
				),
				'ai_helper_autoload_models_google_ai' => array(
					"title" => esc_html__('Autoload a list of models', 'trx_addons'),
					"desc" => wp_kses_data( __('Automatically load the model list from the API or maintain a manual model list.', 'trx_addons') ),
					"std" => "0",
					"type" => "switch",
					"dependency" => array(
						"ai_helper_token_google_ai" => array('not_empty')
					),
				),
				'ai_helper_models_google_ai' => array(
					"title" => esc_html__("List of available chat models", 'trx_addons'),
					"desc" => wp_kses_data( __("Specify id and title for each new chat model.", 'trx_addons') ),
					"dependency" => array(
						"ai_helper_token_google_ai" => array('not_empty'),
						"ai_helper_autoload_models_google_ai" => array('0')
					),
					"clone" => true,
					"std" => array(),
					"type" => "group",
					"fields" => array(
						"id" => array(
							"title" => esc_html__("Model ID", 'trx_addons'),
							"class" => "trx_addons_column-1_4",
							"std" => "",
							"type" => "text"
						),
						"title" => array(
							"title" => esc_html__("Title", 'trx_addons'),
							"class" => "trx_addons_column-1_4",
							"std" => "",
							"type" => "text"
						),
						"max_tokens" => array(
							"title" => esc_html__("Input tokens", 'trx_addons'),
							"class" => "trx_addons_column-1_4",
							"std" => "16000",
							"type" => "text"
						),
						"output_tokens" => array(
							"title" => esc_html__("Output tokens", 'trx_addons'),
							"class" => "trx_addons_column-1_4",
							"std" => "0",
							"type" => "text"
						),
					)
				),

				// LumaLabs AI API settings
				//---------------------------------------------------------------------
				'ai_helper_section_lumalabs_ai' => array(
					"title" => esc_html__('LumaLabs AI API', 'trx_addons'),
					"icon" => TRX_ADDONS_PLUGIN_ADDONS . 'ai-helper/images/icons/lumalabs-ai.svg',
					"type" => "section"
				),
				'ai_helper_info_lumalabs_ai' => array(
					"title" => esc_html__('LumaLabs AI API (Dream Machine)', 'trx_addons'),
					"desc" => wp_kses_data( __("Settings of the AI Helper for LumaLabs AI API", 'trx_addons') )
							. ( ! empty( $log_lumalabs_ai ) ? wp_kses( $log_lumalabs_ai, 'trx_addons_kses_content' ) : '' ),
					"type" => "info"
				),
				'ai_helper_token_lumalabs_ai' => array(
					"title" => esc_html__('API key', 'trx_addons'),
					"desc" => wp_kses( sprintf(
												__('Specify a token to use the LumaLabs AI API. You can generate a token in your personal account using the link %s', 'trx_addons'),
												apply_filters( 'trx_addons_filter_lumalabs_ai_api_key_url',
																'<a href="https://lumalabs.ai/dream-machine/api/keys" target="_blank">https://lumalabs.ai/dream-machine/api/keys</a>'
															)
											),
									'trx_addons_kses_content'
								),
					"std" => "",
					"type" => "text"
				),
				'ai_helper_models_lumalabs_ai' => array(
					"title" => esc_html__("List of available video models", 'trx_addons'),
					"desc" => wp_kses(__("Specify id and name (title) for each new model.", 'trx_addons'), 'trx_addons_kses_content'),
					"dependency" => array(
						"ai_helper_token_lumalabs_ai" => array('not_empty')
					),
					"clone" => true,
					"std" => trx_addons_list_from_array( Lists::get_default_lumalabs_ai_models() ),
					"type" => "group",
					"fields" => array(
						"id" => array(
							"title" => esc_html__("Model ID", 'trx_addons'),
							"class" => "trx_addons_column-1_2",
							"std" => "",
							"type" => "text"
						),
						"title" => array(
							"title" => esc_html__("Title", 'trx_addons'),
							"class" => "trx_addons_column-1_2",
							"std" => "",
							"type" => "text"
						),
					)
				),

				// External Chats
				//---------------------------------------------------------------------
				'ai_helper_section_embed_chats' => array(
					"title" => esc_html__('Embed ext. chatbots', 'trx_addons'),
					"icon" => 'trx_addons_icon-code-1',
					"type" => "section"
				),
				'ai_helper_embed_chats_info' => array(
					"title" => esc_html__('External chatbots', 'trx_addons'),
					"desc" => wp_kses(
						__("Specify scope and html code for each new embedding.", 'trx_addons')
						. '<br />'
						. __("In the <b>'URL contain'</b> field, you can list the URL address parts (each entry separated by a comma or a new line). This block will be displayed on pages that match URL parts listed here.", 'trx_addons')
						. '<br />'
						. __("In the <b>'HTML code'</b> field, paste the code snippet you received when you created/exported the chatbot in your Flowise AI, VoiceFlow, etc. personal account.", 'trx_addons')
						. '<br />'
						. __("You can also use the shortcode <b>[trx_sc_chat type='popup' ...]</b> to insert 'AI Helper Chat'.", 'trx_addons'),
						'trx_addons_kses_content'
						),
					"type" => "info"
				),
				'ai_helper_embed_chats' => array(
					"title" => esc_html__("List of chatbots", 'trx_addons'),
					"desc" => '',
					"clone" => true,
					"std" => array(),
					"type" => "group",
					"fields" => array(
						"title" => array(
							"title" => esc_html__("Title", 'trx_addons'),
							"class" => "trx_addons_column-1_6",
							"std" => "",
							"type" => "text"
						),
						"scope" => array(
							"title" => esc_html__("Scope", 'trx_addons'),
							"class" => "trx_addons_column-1_6",
							"std" => "admin",
							"options" => array(
								"none" => esc_html__("Disabled", 'trx_addons'),
								"admin" => esc_html__("Admin", 'trx_addons'),
								"frontend" => esc_html__("Frontend", 'trx_addons'),
								"site" => esc_html__("Whole site", 'trx_addons'),
							),
							"dir" => "vertical",
							"type" => "radio"
						),
						"url_contain" => array(
							"title" => esc_html__("URL contain", 'trx_addons'),
							"class" => "trx_addons_column-1_6",
							"std" => "",
							"type" => "textarea"
						),
						"code" => array(
							"title" => esc_html__("HTML code", 'trx_addons'),
							"class" => "trx_addons_column-1_2",
							"std" => "",
							"type" => "textarea"
						),
					)
				),

				// Image Generator
				//---------------------------------------------------------------------
				'ai_helper_section_sc_igenerator' => array(
					"title" => esc_html__('SC Image Generator', 'trx_addons'),
					"icon" => 'trx_addons_icon-format-image',
					"type" => "section"
				),
				'ai_helper_sc_igenerator_common' => array(
					"title" => esc_html__('Shortcode "Image Generator": Common settings', 'trx_addons'),
					"type" => "info"
				),
				'ai_helper_sc_igenerator_api_order' => array(
					'title' => esc_html__( 'API order', 'trx_addons' ),
					'desc' => wp_kses_data( __( 'Turn on/off the available APIs and drag and drop them to specify the sequence', 'trx_addons' ) ),
					"dir" => 'vertical',
					"sortable" => true,
					"std" => array( 'openai' => 1, 'stable-diffusion' => 1, 'stability-ai' => 1 ),
					'options' => $is_options_page ? Lists::get_list_ai_image_apis() : array(),
					"type" => "checklist"
				),
				'ai_helper_sc_igenerator_translate_prompt' => array(
					"title" => esc_html__('Translate prompt', 'trx_addons'),
					"desc" => wp_kses_data( __('Always translate prompts into English. Most models are trained on English language datasets and therefore produce the most relevant results only if the prompt is formulated in English. If you have specified a token for the OpenAi API (see section above) - we can automatically translate prompts into English to improve image generation.', 'trx_addons') ),
					"std" => "1",
					"type" => "switch"
				),
				'ai_helper_sc_igenerator_free' => array(
					"title" => esc_html__('Limits for a Free Mode', 'trx_addons'),
					"type" => "info"
				),
				'ai_helper_sc_igenerator_limits' => array(
					"title" => esc_html__('Use limits', 'trx_addons'),
					"desc" => wp_kses_data( __('Use limits (per hour and per visitor) when generating images.', 'trx_addons') ),
					"std" => "1",
					"type" => "switch"
				),
				'ai_helper_sc_igenerator_limit_per_hour' => array(
					"title" => esc_html__('Images per 1 hour', 'trx_addons'),
					"desc" => wp_kses_data( __('How many images can all visitors generate in 1 hour?', 'trx_addons') ),
					"std" => 12,
					"min" => 0,
					"max" => 1000,
					"type" => "slider",
					"dependency" => array(
						"ai_helper_sc_igenerator_limits" => array(1)
					),
				),
				'ai_helper_sc_igenerator_limit_per_visitor' => array(
					"title" => esc_html__('Requests from 1 visitor', 'trx_addons'),
					"desc" => wp_kses_data( __('How many requests can a single visitor send in 1 hour?', 'trx_addons') ),
					"std" => 2,
					"min" => 0,
					"max" => 100,
					"type" => "slider",
					"dependency" => array(
						"ai_helper_sc_igenerator_limits" => array(1)
					),
				),
				'ai_helper_sc_igenerator_limit_alert' => array(
					"title" => esc_html__('Limits reached alert', 'trx_addons'),
					"desc" => wp_kses_data( __('The message that the visitor will see when the limit of requests or generated images (per hour) is exceeded.', 'trx_addons') )
							. ' ' . wp_kses_data( __('If Premium Mode is used, be sure to provide a link to the paid access page here.', 'trx_addons') ),
					"std" => wp_kses( apply_filters( 'trx_addons_sc_igenerator_limit_alert_default',
								'<h5>' . __( 'Limits are reached!', 'trx_addons' ) . '</h5>'
								. '<p>' . __( 'The limit of the number of requests from a single visitor or the number of images that can be generated per hour has been reached.', 'trx_addons' ) . '</p>'
								. '<p>' . __( 'In order to generate more images, sign up for our premium service.', 'trx_addons' ) . '</p>'
								. '<p><a href="#" class="trx_addons_sc_igenerator_link_premium">' . __( 'Sign Up Now', 'trx_addons' ) . '</a></p>'
							), 'trx_addons_kses_content' ),
					"type" => "text_editor",
					"dependency" => array(
						"ai_helper_sc_igenerator_limits" => array(1)
					),
				),
				'ai_helper_sc_igenerator_info_premium' => array(
					"title" => esc_html__('Limits for a Premium Mode', 'trx_addons'),
					"desc" => wp_kses_data('These options enable you to create a paid image generation service. Set limits for paid usage here. Applied to the Image Generator shortcode with the "Premium Mode" option enabled. Ensure restricted access to pages with this shortcode by providing a link to the paid access page in the alert message above.', 'trx_addons'),
					"type" => "info"
				),
				'ai_helper_sc_igenerator_limits_premium' => array(
					"title" => esc_html__('Use limits', 'trx_addons'),
					"desc" => wp_kses_data( __('Use limits (per hour and per visitor) when generating images.', 'trx_addons') ),
					"std" => "0",
					"type" => "switch"
				),
				'ai_helper_sc_igenerator_limit_per_hour_premium' => array(
					"title" => esc_html__('Images per 1 hour', 'trx_addons'),
					"desc" => wp_kses_data( __('How many images can all unlogged visitors generate in 1 hour?', 'trx_addons') ),
					"std" => 12,
					"min" => 0,
					"max" => 1000,
					"type" => "slider",
					"dependency" => array(
						"ai_helper_sc_igenerator_limits_premium" => array(1)
					),
				),
				'ai_helper_sc_igenerator_limit_per_visitor_premium' => array(
					"title" => esc_html__('Requests from 1 visitor', 'trx_addons'),
					"desc" => wp_kses_data( __('How many requests can a single unlogged visitor send in 1 hour?', 'trx_addons') ),
					"std" => 2,
					"min" => 0,
					"max" => 100,
					"type" => "slider",
					"dependency" => array(
						"ai_helper_sc_igenerator_limits_premium" => array(1)
					),
				),
				'ai_helper_sc_igenerator_levels_premium' => array(
					"title" => esc_html__("User levels with limits", 'trx_addons'),
					"desc" => wp_kses_data( __( 'How many images a user can generate depending on their subscription level. The "Default" limit is used for regular registered users. For more flexible settings, use special plugins to separate access levels.', 'trx_addons' ) ),
					"dependency" => array(
						"ai_helper_sc_igenerator_limits_premium" => array(1)
					),
					"clone" => true,
					"std" => array(),
					"type" => "group",
					"fields" => array(
						"level" => array(
							"title" => esc_html__("Level", 'trx_addons'),
							"class" => "trx_addons_column-1_3",
							"std" => "default",
							"options" => apply_filters( 'trx_addons_filter_sc_igenerator_list_user_levels', array( 'default' => __( 'Default', 'trx_addons' ) ) ),
							"type" => "select"
						),
						"limit" => array(
							"title" => esc_html__("Images limit", 'trx_addons'),
							"class" => "trx_addons_column-1_3",
							"std" => "",
							"type" => "text"
						),
						"per" => array(
							"title" => esc_html__("per", 'trx_addons'),
							"class" => "trx_addons_column-1_3",
							"std" => "day",
							"options" => $is_options_page ? Lists::get_list_periods() : array(),
							"type" => "select"
						),
					)
				),
				'ai_helper_sc_igenerator_limit_alert_premium' => array(
					"title" => esc_html__('Limits reached alert', 'trx_addons'),
					"desc" => wp_kses_data( __('The message that the visitor will see when the limit of requests or generated images (per hour) is exceeded.', 'trx_addons') ),
					"std" => wp_kses( apply_filters( 'trx_addons_sc_igenerator_limit_alert_default_premium',
								'<h5>' . __( 'Limits are reached!', 'trx_addons' ) . '</h5>'
								. '<p>' . __( 'The limit of the number of requests from a single visitor or the number of images that can be generated per hour has been reached.', 'trx_addons' ) . '</p>'
								. '<p>' . __( 'Please, try again later.', 'trx_addons' ) . '</p>'
							), 'trx_addons_kses_content' ),
					"type" => "text_editor",
					"dependency" => array(
						"ai_helper_sc_igenerator_limits_premium" => array(1)
					),
				),

				// Audio Generator
				//---------------------------------------------------------------------
				'ai_helper_section_sc_agenerator' => array(
					"title" => esc_html__('SC Audio Generator', 'trx_addons'),
					"icon" => 'trx_addons_icon-mic',
					"type" => "section"
				),
				'ai_helper_sc_agenerator_common' => array(
					"title" => esc_html__('Shortcode "Audio Generator": Common settings', 'trx_addons'),
					"type" => "info"
				),
				'ai_helper_sc_agenerator_api_order' => array(
					'title' => esc_html__( 'API order', 'trx_addons' ),
					'desc' => wp_kses_data( __( 'Turn on/off the available APIs and drag and drop them to specify the sequence', 'trx_addons' ) ),
					"dir" => 'vertical',
					"sortable" => true,
					"std" => array( 'openai' => 1, 'modelslab' => 1 ),
					'options' => $is_options_page ? Lists::get_list_ai_audio_apis() : array(),
					"type" => "checklist"
				),
				'ai_helper_sc_agenerator_free' => array(
					"title" => esc_html__('Limits for a Free Mode', 'trx_addons'),
					"type" => "info"
				),
				'ai_helper_sc_agenerator_limits' => array(
					"title" => esc_html__('Use limits', 'trx_addons'),
					"desc" => wp_kses_data( __('Use limits (per hour and per visitor) when generating audio.', 'trx_addons') ),
					"std" => "1",
					"type" => "switch"
				),
				'ai_helper_sc_agenerator_limit_per_hour' => array(
					"title" => esc_html__('Audio per 1 hour', 'trx_addons'),
					"desc" => wp_kses_data( __('How many audio can all visitors generate in 1 hour?', 'trx_addons') ),
					"std" => 12,
					"min" => 0,
					"max" => 1000,
					"type" => "slider",
					"dependency" => array(
						"ai_helper_sc_agenerator_limits" => array(1)
					),
				),
				'ai_helper_sc_agenerator_limit_per_visitor' => array(
					"title" => esc_html__('Requests from 1 visitor', 'trx_addons'),
					"desc" => wp_kses_data( __('How many requests can a single visitor send in 1 hour?', 'trx_addons') ),
					"std" => 2,
					"min" => 0,
					"max" => 100,
					"type" => "slider",
					"dependency" => array(
						"ai_helper_sc_agenerator_limits" => array(1)
					),
				),
				'ai_helper_sc_agenerator_limit_alert' => array(
					"title" => esc_html__('Limits reached alert', 'trx_addons'),
					"desc" => wp_kses_data( __('The message that the visitor will see when the limit of requests or generated audio (per hour) is exceeded.', 'trx_addons') )
							. ' ' . wp_kses_data( __('If Premium Mode is used, be sure to provide a link to the paid access page here.', 'trx_addons') ),
					"std" => wp_kses( apply_filters( 'trx_addons_sc_agenerator_limit_alert_default',
								'<h5>' . __( 'Limits are reached!', 'trx_addons' ) . '</h5>'
								. '<p>' . __( 'The limit of the number of requests from a single visitor or the number of audio that can be generated per hour has been reached.', 'trx_addons' ) . '</p>'
								. '<p>' . __( 'In order to generate more audio, sign up for our premium service.', 'trx_addons' ) . '</p>'
								. '<p><a href="#" class="trx_addons_sc_agenerator_link_premium">' . __( 'Sign Up Now', 'trx_addons' ) . '</a></p>'
							), 'trx_addons_kses_content' ),
					"type" => "text_editor",
					"dependency" => array(
						"ai_helper_sc_agenerator_limits" => array(1)
					),
				),
				'ai_helper_sc_agenerator_info_premium' => array(
					"title" => esc_html__('Limits for a Premium Mode', 'trx_addons'),
					"desc" => wp_kses_data('These options enable you to create a paid audio generation service. Set limits for paid usage here. Applied to the Audio Generator shortcode with the "Premium Mode" option enabled. Ensure restricted access to pages with this shortcode by providing a link to the paid access page in the alert message above.', 'trx_addons'),
					"type" => "info"
				),
				'ai_helper_sc_agenerator_limits_premium' => array(
					"title" => esc_html__('Use limits', 'trx_addons'),
					"desc" => wp_kses_data( __('Use limits (per hour and per visitor) when generating audio.', 'trx_addons') ),
					"std" => "0",
					"type" => "switch"
				),
				'ai_helper_sc_agenerator_limit_per_hour_premium' => array(
					"title" => esc_html__('Audio per 1 hour', 'trx_addons'),
					"desc" => wp_kses_data( __('How many audio can all unlogged visitors generate in 1 hour?', 'trx_addons') ),
					"std" => 12,
					"min" => 0,
					"max" => 1000,
					"type" => "slider",
					"dependency" => array(
						"ai_helper_sc_agenerator_limits_premium" => array(1)
					),
				),
				'ai_helper_sc_agenerator_limit_per_visitor_premium' => array(
					"title" => esc_html__('Requests from 1 visitor', 'trx_addons'),
					"desc" => wp_kses_data( __('How many requests can a single visitor send in 1 hour?', 'trx_addons') ),
					"std" => 2,
					"min" => 0,
					"max" => 100,
					"type" => "slider",
					"dependency" => array(
						"ai_helper_sc_agenerator_limits_premium" => array(1)
					),
				),
				'ai_helper_sc_agenerator_levels_premium' => array(
					"title" => esc_html__("User levels with limits", 'trx_addons'),
					"desc" => wp_kses_data( __( 'How many audio a user can generate depending on their subscription level. The "Default" limit is used for regular registered users. For more flexible settings, use special plugins to separate access levels.', 'trx_addons' ) ),
					"dependency" => array(
						"ai_helper_sc_agenerator_limits_premium" => array(1)
					),
					"clone" => true,
					"std" => array(),
					"type" => "group",
					"fields" => array(
						"level" => array(
							"title" => esc_html__("Level", 'trx_addons'),
							"class" => "trx_addons_column-1_3",
							"std" => "default",
							"options" => apply_filters( 'trx_addons_filter_sc_agenerator_list_user_levels', array( 'default' => __( 'Default', 'trx_addons' ) ) ),
							"type" => "select"
						),
						"limit" => array(
							"title" => esc_html__("Audio limit", 'trx_addons'),
							"class" => "trx_addons_column-1_3",
							"std" => "",
							"type" => "text"
						),
						"per" => array(
							"title" => esc_html__("per", 'trx_addons'),
							"class" => "trx_addons_column-1_3",
							"std" => "day",
							"options" => $is_options_page ? Lists::get_list_periods() : array(),
							"type" => "select"
						),
					)
				),
				'ai_helper_sc_agenerator_limit_alert_premium' => array(
					"title" => esc_html__('Limits reached alert', 'trx_addons'),
					"desc" => wp_kses_data( __('The message that the visitor will see when the limit of requests or generated audio (per hour) is exceeded.', 'trx_addons') ),
					"std" => wp_kses( apply_filters( 'trx_addons_sc_agenerator_limit_alert_default_premium',
								'<h5>' . __( 'Limits are reached!', 'trx_addons' ) . '</h5>'
								. '<p>' . __( 'The limit of the number of requests from a single visitor or the number of audio that can be generated per hour has been reached.', 'trx_addons' ) . '</p>'
								. '<p>' . __( 'Please, try again later.', 'trx_addons' ) . '</p>'
							), 'trx_addons_kses_content' ),
					"type" => "text_editor",
					"dependency" => array(
						"ai_helper_sc_agenerator_limits_premium" => array(1)
					),
				),

				// Music Generator
				//---------------------------------------------------------------------
				'ai_helper_section_sc_mgenerator' => array(
					"title" => esc_html__('SC Music Generator', 'trx_addons'),
					"icon" => 'trx_addons_icon-volume-up',
					"type" => "section"
				),
				'ai_helper_sc_mgenerator_common' => array(
					"title" => esc_html__('Shortcode "Music Generator": Common settings', 'trx_addons'),
					"type" => "info"
				),
				'ai_helper_sc_mgenerator_api_order' => array(
					'title' => esc_html__( 'API order', 'trx_addons' ),
					'desc' => wp_kses_data( __( 'Turn on/off the available APIs and drag and drop them to specify the sequence', 'trx_addons' ) ),
					"dir" => 'vertical',
					"sortable" => true,
					"std" => array( 'modelslab' => 1 ),
					'options' => $is_options_page ? Lists::get_list_ai_music_apis() : array(),
					"type" => "checklist"
				),
				'ai_helper_sc_mgenerator_translate_prompt' => array(
					"title" => esc_html__('Translate prompt', 'trx_addons'),
					"desc" => wp_kses_data( __('Always translate prompt into English. Most models are trained on English language datasets and therefore produce the most relevant results only if the prompt is formulated in English. If you have specified a token for the OpenAi API (see section above) - we can automatically translate prompts into English to improve music generation.', 'trx_addons') ),
					"std" => "1",
					"type" => "switch"
				),
				'ai_helper_sc_mgenerator_free' => array(
					"title" => esc_html__('Limits for a Free Mode', 'trx_addons'),
					"type" => "info"
				),
				'ai_helper_sc_mgenerator_limits' => array(
					"title" => esc_html__('Use limits', 'trx_addons'),
					"desc" => wp_kses_data( __('Use limits (per hour and per visitor) when generating music.', 'trx_addons') ),
					"std" => "1",
					"type" => "switch"
				),
				'ai_helper_sc_mgenerator_limit_per_hour' => array(
					"title" => esc_html__('Music per 1 hour', 'trx_addons'),
					"desc" => wp_kses_data( __('How many music can all visitors generate in 1 hour?', 'trx_addons') ),
					"std" => 12,
					"min" => 0,
					"max" => 1000,
					"type" => "slider",
					"dependency" => array(
						"ai_helper_sc_mgenerator_limits" => array(1)
					),
				),
				'ai_helper_sc_mgenerator_limit_per_visitor' => array(
					"title" => esc_html__('Requests from 1 visitor', 'trx_addons'),
					"desc" => wp_kses_data( __('How many requests can a single visitor send in 1 hour?', 'trx_addons') ),
					"std" => 2,
					"min" => 0,
					"max" => 100,
					"type" => "slider",
					"dependency" => array(
						"ai_helper_sc_mgenerator_limits" => array(1)
					),
				),
				'ai_helper_sc_mgenerator_limit_alert' => array(
					"title" => esc_html__('Limits reached alert', 'trx_addons'),
					"desc" => wp_kses_data( __('The message that the visitor will see when the limit of requests or generated music (per hour) is exceeded.', 'trx_addons') )
							. ' ' . wp_kses_data( __('If Premium Mode is used, be sure to provide a link to the paid access page here.', 'trx_addons') ),
					"std" => wp_kses( apply_filters( 'trx_addons_sc_mgenerator_limit_alert_default',
								'<h5>' . __( 'Limits are reached!', 'trx_addons' ) . '</h5>'
								. '<p>' . __( 'The limit of the number of requests from a single visitor or the number of music that can be generated per hour has been reached.', 'trx_addons' ) . '</p>'
								. '<p>' . __( 'In order to generate more music, sign up for our premium service.', 'trx_addons' ) . '</p>'
								. '<p><a href="#" class="trx_addons_sc_mgenerator_link_premium">' . __( 'Sign Up Now', 'trx_addons' ) . '</a></p>'
							), 'trx_addons_kses_content' ),
					"type" => "text_editor",
					"dependency" => array(
						"ai_helper_sc_mgenerator_limits" => array(1)
					),
				),
				'ai_helper_sc_mgenerator_info_premium' => array(
					"title" => esc_html__('Limits for a Premium Mode', 'trx_addons'),
					"desc" => wp_kses_data('These options enable you to create a paid music generation service. Set limits for paid usage here. Applied to the Music Generator shortcode with the "Premium Mode" option enabled. Ensure restricted access to pages with this shortcode by providing a link to the paid access page in the alert message above.', 'trx_addons'),
					"type" => "info"
				),
				'ai_helper_sc_mgenerator_limits_premium' => array(
					"title" => esc_html__('Use limits', 'trx_addons'),
					"desc" => wp_kses_data( __('Use limits (per hour and per visitor) when generating music.', 'trx_addons') ),
					"std" => "0",
					"type" => "switch"
				),
				'ai_helper_sc_mgenerator_limit_per_hour_premium' => array(
					"title" => esc_html__('Music per 1 hour', 'trx_addons'),
					"desc" => wp_kses_data( __('How many music can all unlogged visitors generate in 1 hour?', 'trx_addons') ),
					"std" => 12,
					"min" => 0,
					"max" => 1000,
					"type" => "slider",
					"dependency" => array(
						"ai_helper_sc_mgenerator_limits_premium" => array(1)
					),
				),
				'ai_helper_sc_mgenerator_limit_per_visitor_premium' => array(
					"title" => esc_html__('Requests from 1 visitor', 'trx_addons'),
					"desc" => wp_kses_data( __('How many requests can a single visitor send in 1 hour?', 'trx_addons') ),
					"std" => 2,
					"min" => 0,
					"max" => 100,
					"type" => "slider",
					"dependency" => array(
						"ai_helper_sc_mgenerator_limits_premium" => array(1)
					),
				),
				'ai_helper_sc_mgenerator_levels_premium' => array(
					"title" => esc_html__("User levels with limits", 'trx_addons'),
					"desc" => wp_kses_data( __( 'How many music a user can generate depending on their subscription level. The "Default" limit is used for regular registered users. For more flexible settings, use special plugins to separate access levels.', 'trx_addons' ) ),
					"dependency" => array(
						"ai_helper_sc_mgenerator_limits_premium" => array(1)
					),
					"clone" => true,
					"std" => array(),
					"type" => "group",
					"fields" => array(
						"level" => array(
							"title" => esc_html__("Level", 'trx_addons'),
							"class" => "trx_addons_column-1_3",
							"std" => "default",
							"options" => apply_filters( 'trx_addons_filter_sc_mgenerator_list_user_levels', array( 'default' => __( 'Default', 'trx_addons' ) ) ),
							"type" => "select"
						),
						"limit" => array(
							"title" => esc_html__("Music limit", 'trx_addons'),
							"class" => "trx_addons_column-1_3",
							"std" => "",
							"type" => "text"
						),
						"per" => array(
							"title" => esc_html__("per", 'trx_addons'),
							"class" => "trx_addons_column-1_3",
							"std" => "day",
							"options" => $is_options_page ? Lists::get_list_periods() : array(),
							"type" => "select"
						),
					)
				),
				'ai_helper_sc_mgenerator_limit_alert_premium' => array(
					"title" => esc_html__('Limits reached alert', 'trx_addons'),
					"desc" => wp_kses_data( __('The message that the visitor will see when the limit of requests or generated music (per hour) is exceeded.', 'trx_addons') ),
					"std" => wp_kses( apply_filters( 'trx_addons_sc_mgenerator_limit_alert_default_premium',
								'<h5>' . __( 'Limits are reached!', 'trx_addons' ) . '</h5>'
								. '<p>' . __( 'The limit of the number of requests from a single visitor or the number of music that can be generated per hour has been reached.', 'trx_addons' ) . '</p>'
								. '<p>' . __( 'Please, try again later.', 'trx_addons' ) . '</p>'
							), 'trx_addons_kses_content' ),
					"type" => "text_editor",
					"dependency" => array(
						"ai_helper_sc_mgenerator_limits_premium" => array(1)
					),
				),

				// Text Generator
				//---------------------------------------------------------------------
				'ai_helper_section_sc_tgenerator' => array(
					"title" => esc_html__('SC Text Generator', 'trx_addons'),
					"icon" => 'trx_addons_icon-doc-text',
					"type" => "section"
				),
				'ai_helper_sc_tgenerator_common' => array(
					"title" => esc_html__('Shortcode "Text Generator": Common settings', 'trx_addons'),
					"type" => "info"
				),
				'ai_helper_sc_tgenerator_api_order' => array(
					'title' => esc_html__( 'API order', 'trx_addons' ),
					'desc' => wp_kses_data( __( 'Turn on/off the available APIs and drag and drop them to specify the sequence', 'trx_addons' ) ),
					"dir" => 'vertical',
					"sortable" => true,
					"std" => array( 'openai' => 1, 'openai-assistants' => 1, 'flowise-ai' => 1, 'google-ai' => 1 ),
					'options' => $is_options_page ? Lists::get_list_ai_chat_apis() : array(),
					"type" => "checklist"
				),
				'ai_helper_sc_tgenerator_temperature' => array(
					"title" => esc_html__('Temperature', 'trx_addons'),
					"desc" => wp_kses_data( __('What sampling temperature to use, between 0 and 2. Higher values like 0.8 will make the output more random, while lower values like 0.2 will make it more focused and deterministic.', 'trx_addons') ),
					"std" => 1,
					"min" => 0,
					"max" => 2,
					"step" => 0.1,
					"type" => "slider"
				),
				'ai_helper_sc_tgenerator_system_prompt' => array(
					"title" => esc_html__('System Prompt', 'trx_addons'),
					"desc" => wp_kses_data( __('System instructions for the text generator. They serve as a guide for choosing the style of communication on the part of the AI.', 'trx_addons') ),
					"std" => __( 'You are an assistant for writing posts. Return only the result without any additional messages. Format the response with HTML tags.', 'trx_addons' ),
					"type" => "textarea",
					"dependency" => array(
						"ai_helper_token_openai" => array('not_empty')
					),
				),
				'ai_helper_sc_tgenerator_free' => array(
					"title" => esc_html__('Limits for a Free Mode', 'trx_addons'),
					"type" => "info"
				),
				'ai_helper_sc_tgenerator_limits' => array(
					"title" => esc_html__('Use limits', 'trx_addons'),
					"desc" => wp_kses_data( __('Use limits (per request, per hour and per visitor) when generating text.', 'trx_addons') ),
					"std" => "1",
					"type" => "switch"
				),
				'ai_helper_sc_tgenerator_limit_per_request' => array(
					"title" => esc_html__('Max. tokens per 1 request', 'trx_addons'),
					"desc" => wp_kses_data( __('How many tokens can be used per one request to the API?', 'trx_addons') ),
					"std" => 1000,
					"min" => 0,
					"max" => Utils::get_default_max_tokens(),
					"step" => 100,
					"type" => "slider",
					"dependency" => array(
						"ai_helper_sc_tgenerator_limits" => array(1)
					),
				),
				'ai_helper_sc_tgenerator_limit_per_hour' => array(
					"title" => esc_html__('Requests per 1 hour', 'trx_addons'),
					"desc" => wp_kses_data( __('How many requests can be processed for all visitors in 1 hour?', 'trx_addons') ),
					"std" => 8,
					"min" => 0,
					"max" => 1000,
					"type" => "slider",
					"dependency" => array(
						"ai_helper_sc_tgenerator_limits" => array(1)
					),
				),
				'ai_helper_sc_tgenerator_limit_per_visitor' => array(
					"title" => esc_html__('Requests from 1 visitor', 'trx_addons'),
					"desc" => wp_kses_data( __('How many requests can a single visitor send in 1 hour?', 'trx_addons') ),
					"std" => 2,
					"min" => 0,
					"max" => 100,
					"type" => "slider",
					"dependency" => array(
						"ai_helper_sc_tgenerator_limits" => array(1)
					),
				),
				'ai_helper_sc_tgenerator_limit_alert' => array(
					"title" => esc_html__('Limits reached alert', 'trx_addons'),
					"desc" => wp_kses_data( __('The message that the visitor will see when the limit of requests (per hour) is exceeded.', 'trx_addons') )
								. ' ' . wp_kses_data( __('If Premium Mode is used, be sure to provide a link to the paid access page here.', 'trx_addons') ),
					"std" => wp_kses( apply_filters( 'trx_addons_sc_tgenerator_limit_alert_default',
								'<h5>' . __( 'Limits are reached!', 'trx_addons' ) . '</h5>'
								. '<p>' . __( 'The limit of the number of requests from a single visitor per hour has been reached.', 'trx_addons' ) . '</p>'
								. '<p>' . __( 'In order to generate more texts, sign up for our premium service.', 'trx_addons' ) . '</p>'
								. '<p><a href="#" class="trx_addons_sc_tgenerator_link_premium">' . __( 'Sign Up Now', 'trx_addons' ) . '</a></p>'
							), 'trx_addons_kses_content' ),
					"type" => "text_editor",
					"dependency" => array(
						"ai_helper_sc_tgenerator_limits" => array(1)
					),
				),
				'ai_helper_sc_tgenerator_premium' => array(
					"title" => esc_html__('Limits for a Premium Mode', 'trx_addons'),
					"type" => "info"
				),
				'ai_helper_sc_tgenerator_limits_premium' => array(
					"title" => esc_html__('Use limits', 'trx_addons'),
					"desc" => wp_kses_data( __('Use limits (per request, per hour and per visitor) when generating text.', 'trx_addons') ),
					"std" => "1",
					"type" => "switch"
				),
				'ai_helper_sc_tgenerator_limit_per_request_premium' => array(
					"title" => esc_html__('Max. tokens per 1 request', 'trx_addons'),
					"desc" => wp_kses_data( __('How many tokens can be used per one request to the API?', 'trx_addons') ),
					"std" => 1000,
					"min" => 0,
					"max" => Utils::get_default_max_tokens(),
					"step" => 100,
					"type" => "slider",
					"dependency" => array(
						"ai_helper_sc_tgenerator_limits_premium" => array(1)
					),
				),
				'ai_helper_sc_tgenerator_limit_per_hour_premium' => array(
					"title" => esc_html__('Requests per 1 hour', 'trx_addons'),
					"desc" => wp_kses_data( __('How many requests can be processed for all visitors in 1 hour?', 'trx_addons') ),
					"std" => 8,
					"min" => 0,
					"max" => 1000,
					"type" => "slider",
					"dependency" => array(
						"ai_helper_sc_tgenerator_limits_premium" => array(1)
					),
				),
				'ai_helper_sc_tgenerator_limit_per_visitor_premium' => array(
					"title" => esc_html__('Requests from 1 visitor', 'trx_addons'),
					"desc" => wp_kses_data( __('How many requests can a single visitor send in 1 hour?', 'trx_addons') ),
					"std" => 2,
					"min" => 0,
					"max" => 100,
					"type" => "slider",
					"dependency" => array(
						"ai_helper_sc_tgenerator_limits_premium" => array(1)
					),
				),
				'ai_helper_sc_tgenerator_levels_premium' => array(
					"title" => esc_html__("User levels with limits", 'trx_addons'),
					"desc" => wp_kses_data( __( 'How many requests a user can generate depending on their subscription level. The "Default" limit is used for regular registered users. For more flexible settings, use special plugins to separate access levels.', 'trx_addons' ) ),
					"dependency" => array(
						"ai_helper_sc_tgenerator_limits_premium" => array(1)
					),
					"clone" => true,
					"std" => array(),
					"type" => "group",
					"fields" => array(
						"level" => array(
							"title" => esc_html__("Level", 'trx_addons'),
							"class" => "trx_addons_column-1_3",
							"std" => "default",
							"options" => apply_filters( 'trx_addons_filter_sc_tgenerator_list_user_levels', array( 'default' => __( 'Default', 'trx_addons' ) ) ),
							"type" => "select"
						),
						"limit" => array(
							"title" => esc_html__("Requests limit", 'trx_addons'),
							"class" => "trx_addons_column-1_3",
							"std" => "",
							"type" => "text"
						),
						"per" => array(
							"title" => esc_html__("per", 'trx_addons'),
							"class" => "trx_addons_column-1_3",
							"std" => "day",
							"options" => $is_options_page ? Lists::get_list_periods() : array(),
							"type" => "select"
						),
					)
				),
				'ai_helper_sc_tgenerator_limit_alert_premium' => array(
					"title" => esc_html__('Limits reached alert', 'trx_addons'),
					"desc" => wp_kses_data( __('The message that the visitor will see when the limit of requests (per hour) is exceeded.', 'trx_addons') ),
					"std" => wp_kses( apply_filters( 'trx_addons_sc_tgenerator_limit_alert_default_premium',
								'<h5>' . __( 'Limits are reached!', 'trx_addons' ) . '</h5>'
								. '<p>' . __( 'The limit of the number of requests from a single visitor per hour has been reached.', 'trx_addons' ) . '</p>'
								. '<p>' . __( 'Please, try again later.', 'trx_addons' ) . '</p>'
							), 'trx_addons_kses_content' ),
					"type" => "text_editor",
					"dependency" => array(
						"ai_helper_sc_tgenerator_limits_premium" => array(1)
					),
				),

				// Chat
				//---------------------------------------------------------------------
				'ai_helper_section_sc_chat' => array(
					"title" => esc_html__('SC AI Chat', 'trx_addons'),
					"icon" => 'trx_addons_icon-chat',
					"type" => "section"
				),
				'ai_helper_sc_chat_common' => array(
					"title" => esc_html__('Shortcode "AI Chat": Common settings', 'trx_addons'),
					"type" => "info"
				),
				'ai_helper_sc_chat_api_order' => array(
					'title' => esc_html__( 'API order', 'trx_addons' ),
					'desc' => wp_kses_data( __( 'Turn on/off the available APIs and drag and drop them to specify the sequence', 'trx_addons' ) ),
					"dir" => 'vertical',
					"sortable" => true,
					"std" => array( 'openai' => 1, 'openai-assistants' => 1, 'flowise-ai' => 1, 'google-ai' => 1 ),
					'options' => $is_options_page ? Lists::get_list_ai_chat_apis() : array(),
					"type" => "checklist"
				),
				'ai_helper_sc_chat_temperature' => array(
					"title" => esc_html__('Temperature', 'trx_addons'),
					"desc" => wp_kses_data( __('What sampling temperature to use, between 0 and 2. Higher values like 0.8 will make the output more random, while lower values like 0.2 will make it more focused and deterministic.', 'trx_addons') ),
					"std" => 1,
					"min" => 0,
					"max" => 2,
					"step" => 0.1,
					"type" => "slider"
				),
				'ai_helper_sc_chat_system_prompt' => array(
					"title" => esc_html__('System Prompt', 'trx_addons'),
					"desc" => wp_kses_data( __('System instructions for the chatbot (not included in the list of messages, serve as a guide for choosing the style of communication on the part of the chatbot).', 'trx_addons') ),
					"std" => __( 'You are an assistant for writing posts. Return only the result without any additional messages. Format the response with HTML tags.', 'trx_addons' ),
					"type" => "textarea",
					"dependency" => array(
						"ai_helper_token_openai" => array('not_empty')
					),
				),
				'ai_helper_sc_chat_free' => array(
					"title" => esc_html__('Limits for a Free Mode', 'trx_addons'),
					"type" => "info"
				),
				'ai_helper_sc_chat_limits' => array(
					"title" => esc_html__('Use limits', 'trx_addons'),
					"desc" => wp_kses_data( __('Use limits (per request, per hour and per visitor) when chatting.', 'trx_addons') ),
					"std" => "1",
					"type" => "switch"
				),
				'ai_helper_sc_chat_limit_per_request' => array(
					"title" => esc_html__('Max. tokens per 1 request', 'trx_addons'),
					"desc" => wp_kses_data( __('How many tokens can be used per one request to the chat?', 'trx_addons') ),
					"std" => 1000,
					"min" => 0,
					"max" => Utils::get_default_max_tokens(),
					"step" => 100,
					"type" => "slider",
					"dependency" => array(
						"ai_helper_sc_chat_limits" => array(1)
					),
				),
				'ai_helper_sc_chat_limit_per_hour' => array(
					"title" => esc_html__('Requests per 1 hour', 'trx_addons'),
					"desc" => wp_kses_data( __('How many requests can be processed for all visitors in 1 hour?', 'trx_addons') ),
					"std" => 80,
					"min" => 0,
					"max" => 1000,
					"type" => "slider",
					"dependency" => array(
						"ai_helper_sc_chat_limits" => array(1)
					),
				),
				'ai_helper_sc_chat_limit_per_visitor' => array(
					"title" => esc_html__('Requests from 1 visitor', 'trx_addons'),
					"desc" => wp_kses_data( __('How many requests can a single visitor send in 1 hour?', 'trx_addons') ),
					"std" => 10,
					"min" => 0,
					"max" => 100,
					"type" => "slider",
					"dependency" => array(
						"ai_helper_sc_chat_limits" => array(1)
					),
				),
				'ai_helper_sc_chat_limit_alert' => array(
					"title" => esc_html__('Limits reached alert', 'trx_addons'),
					"desc" => wp_kses_data( __('The message that the visitor will see when the limit of requests (per hour) is exceeded.', 'trx_addons') )
								. ' ' . wp_kses_data( __('If Premium Mode is used, be sure to provide a link to the paid access page here.', 'trx_addons') ),
					"std" => wp_kses( apply_filters( 'trx_addons_sc_chat_limit_alert_default',
								'<h5>' . __( 'Limits are reached!', 'trx_addons' ) . '</h5>'
								. '<p>' . __( 'The limit of the number of requests from a single visitor per hour has been reached.', 'trx_addons' ) . '</p>'
								. '<p>' . __( 'In order to generate more texts, sign up for our premium service.', 'trx_addons' ) . '</p>'
								. '<p><a href="#" class="trx_addons_sc_chat_link_premium">' . __( 'Sign Up Now', 'trx_addons' ) . '</a></p>'
							), 'trx_addons_kses_content' ),
					"type" => "text_editor",
					"dependency" => array(
						"ai_helper_sc_chat_limits" => array(1)
					),
				),
				'ai_helper_sc_chat_premium' => array(
					"title" => esc_html__('Limits for a Premium Mode', 'trx_addons'),
					"type" => "info"
				),
				'ai_helper_sc_chat_limits_premium' => array(
					"title" => esc_html__('Use limits', 'trx_addons'),
					"desc" => wp_kses_data( __('Use limits (per request, per hour and per visitor) when chatting.', 'trx_addons') ),
					"std" => "1",
					"type" => "switch"
				),
				'ai_helper_sc_chat_limit_per_request_premium' => array(
					"title" => esc_html__('Max. tokens per 1 request', 'trx_addons'),
					"desc" => wp_kses_data( __('How many tokens can be used per one request to the chat?', 'trx_addons') ),
					"std" => 1000,
					"min" => 0,
					"max" => Utils::get_default_max_tokens(),
					"step" => 100,
					"type" => "slider",
					"dependency" => array(
						"ai_helper_sc_chat_limits_premium" => array(1)
					),
				),
				'ai_helper_sc_chat_limit_per_hour_premium' => array(
					"title" => esc_html__('Requests per 1 hour', 'trx_addons'),
					"desc" => wp_kses_data( __('How many requests can be processed for all visitors in 1 hour?', 'trx_addons') ),
					"std" => 80,
					"min" => 0,
					"max" => 1000,
					"type" => "slider",
					"dependency" => array(
						"ai_helper_sc_chat_limits_premium" => array(1)
					),
				),
				'ai_helper_sc_chat_limit_per_visitor_premium' => array(
					"title" => esc_html__('Requests from 1 visitor', 'trx_addons'),
					"desc" => wp_kses_data( __('How many requests can a single visitor send in 1 hour?', 'trx_addons') ),
					"std" => 10,
					"min" => 0,
					"max" => 100,
					"type" => "slider",
					"dependency" => array(
						"ai_helper_sc_chat_limits_premium" => array(1)
					),
				),
				'ai_helper_sc_chat_levels_premium' => array(
					"title" => esc_html__("User levels with limits", 'trx_addons'),
					"desc" => wp_kses_data( __( 'How many requests a user can generate depending on their subscription level. The "Default" limit is used for regular registered users. For more flexible settings, use special plugins to separate access levels.', 'trx_addons' ) ),
					"dependency" => array(
						"ai_helper_sc_chat_limits_premium" => array(1)
					),
					"clone" => true,
					"std" => array(),
					"type" => "group",
					"fields" => array(
						"level" => array(
							"title" => esc_html__("Level", 'trx_addons'),
							"class" => "trx_addons_column-1_3",
							"std" => "default",
							"options" => apply_filters( 'trx_addons_filter_sc_chat_list_user_levels', array( 'default' => __( 'Default', 'trx_addons' ) ) ),
							"type" => "select"
						),
						"limit" => array(
							"title" => esc_html__("Requests limit", 'trx_addons'),
							"class" => "trx_addons_column-1_3",
							"std" => "",
							"type" => "text"
						),
						"per" => array(
							"title" => esc_html__("per", 'trx_addons'),
							"class" => "trx_addons_column-1_3",
							"std" => "day",
							"options" => $is_options_page ? Lists::get_list_periods() : array(),
							"type" => "select"
						),
					)
				),
				'ai_helper_sc_chat_limit_alert_premium' => array(
					"title" => esc_html__('Limits reached alert', 'trx_addons'),
					"desc" => wp_kses_data( __('The message that the visitor will see when the limit of requests (per hour) is exceeded.', 'trx_addons') ),
					"std" => wp_kses( apply_filters( 'trx_addons_sc_chat_limit_alert_default_premium',
								'<h5>' . __( 'Limits are reached!', 'trx_addons' ) . '</h5>'
								. '<p>' . __( 'The limit of the number of requests from a single visitor per hour has been reached.', 'trx_addons' ) . '</p>'
								. '<p>' . __( 'Please, try again later.', 'trx_addons' ) . '</p>'
							), 'trx_addons_kses_content' ),
					"type" => "text_editor",
					"dependency" => array(
						"ai_helper_sc_chat_limits_premium" => array(1)
					),
				),

				// Video Generator
				//---------------------------------------------------------------------
				'ai_helper_section_sc_vgenerator' => array(
					"title" => esc_html__('SC Video Generator', 'trx_addons'),
					"icon" => 'trx_addons_icon-video',
					"type" => "section"
				),
				'ai_helper_sc_vgenerator_common' => array(
					"title" => esc_html__('Shortcode "SC Video Generator": Common settings', 'trx_addons'),
					"type" => "info"
				),
				'ai_helper_sc_vgenerator_api_order' => array(
					'title' => esc_html__( 'API order', 'trx_addons' ),
					'desc' => wp_kses_data( __( 'Turn on/off the available APIs and drag and drop them to specify the sequence', 'trx_addons' ) ),
					"dir" => 'vertical',
					"sortable" => true,
					"std" => array( 'lumalabs-ai' => 1 ),
					'options' => $is_options_page ? Lists::get_list_ai_video_apis() : array(),
					"type" => "checklist"
				),
				'ai_helper_sc_vgenerator_translate_prompt' => array(
					"title" => esc_html__('Translate prompt', 'trx_addons'),
					"desc" => wp_kses_data( __('Always translate prompt into English. Most models are trained on English language datasets and therefore produce the most relevant results only if the prompt is formulated in English. If you have specified a token for the OpenAi API (see section above) - we can automatically translate prompts into English to improve video generation.', 'trx_addons') ),
					"std" => "1",
					"type" => "switch"
				),
				'ai_helper_sc_vgenerator_free' => array(
					"title" => esc_html__('Limits for a Free Mode', 'trx_addons'),
					"type" => "info"
				),
				'ai_helper_sc_vgenerator_limits' => array(
					"title" => esc_html__('Use limits', 'trx_addons'),
					"desc" => wp_kses_data( __('Use limits (per hour and per visitor) when generating video.', 'trx_addons') ),
					"std" => "1",
					"type" => "switch"
				),
				'ai_helper_sc_vgenerator_limit_per_hour' => array(
					"title" => esc_html__('Video per 1 hour', 'trx_addons'),
					"desc" => wp_kses_data( __('How many video can all visitors generate in 1 hour?', 'trx_addons') ),
					"std" => 12,
					"min" => 0,
					"max" => 1000,
					"type" => "slider",
					"dependency" => array(
						"ai_helper_sc_vgenerator_limits" => array(1)
					),
				),
				'ai_helper_sc_vgenerator_limit_per_visitor' => array(
					"title" => esc_html__('Requests from 1 visitor', 'trx_addons'),
					"desc" => wp_kses_data( __('How many requests can a single visitor send in 1 hour?', 'trx_addons') ),
					"std" => 2,
					"min" => 0,
					"max" => 100,
					"type" => "slider",
					"dependency" => array(
						"ai_helper_sc_vgenerator_limits" => array(1)
					),
				),
				'ai_helper_sc_vgenerator_limit_alert' => array(
					"title" => esc_html__('Limits reached alert', 'trx_addons'),
					"desc" => wp_kses_data( __('The message that the visitor will see when the limit of requests or generated video (per hour) is exceeded.', 'trx_addons') )
							. ' ' . wp_kses_data( __('If Premium Mode is used, be sure to provide a link to the paid access page here.', 'trx_addons') ),
					"std" => wp_kses( apply_filters( 'trx_addons_sc_vgenerator_limit_alert_default',
								'<h5>' . __( 'Limits are reached!', 'trx_addons' ) . '</h5>'
								. '<p>' . __( 'The limit of the number of requests from a single visitor or the number of video that can be generated per hour has been reached.', 'trx_addons' ) . '</p>'
								. '<p>' . __( 'In order to generate more video, sign up for our premium service.', 'trx_addons' ) . '</p>'
								. '<p><a href="#" class="trx_addons_sc_vgenerator_link_premium">' . __( 'Sign Up Now', 'trx_addons' ) . '</a></p>'
							), 'trx_addons_kses_content' ),
					"type" => "text_editor",
					"dependency" => array(
						"ai_helper_sc_vgenerator_limits" => array(1)
					),
				),
				'ai_helper_sc_vgenerator_info_premium' => array(
					"title" => esc_html__('Limits for a Premium Mode', 'trx_addons'),
					"desc" => wp_kses_data('These options enable you to create a paid video generation service. Set limits for paid usage here. Applied to the Video Generator shortcode with the "Premium Mode" option enabled. Ensure restricted access to pages with this shortcode by providing a link to the paid access page in the alert message above.', 'trx_addons'),
					"type" => "info"
				),
				'ai_helper_sc_vgenerator_limits_premium' => array(
					"title" => esc_html__('Use limits', 'trx_addons'),
					"desc" => wp_kses_data( __('Use limits (per hour and per visitor) when generating video.', 'trx_addons') ),
					"std" => "0",
					"type" => "switch"
				),
				'ai_helper_sc_vgenerator_limit_per_hour_premium' => array(
					"title" => esc_html__('Video per 1 hour', 'trx_addons'),
					"desc" => wp_kses_data( __('How many video can all unlogged visitors generate in 1 hour?', 'trx_addons') ),
					"std" => 12,
					"min" => 0,
					"max" => 1000,
					"type" => "slider",
					"dependency" => array(
						"ai_helper_sc_vgenerator_limits_premium" => array(1)
					),
				),
				'ai_helper_sc_vgenerator_limit_per_visitor_premium' => array(
					"title" => esc_html__('Requests from 1 visitor', 'trx_addons'),
					"desc" => wp_kses_data( __('How many requests can a single visitor send in 1 hour?', 'trx_addons') ),
					"std" => 2,
					"min" => 0,
					"max" => 100,
					"type" => "slider",
					"dependency" => array(
						"ai_helper_sc_vgenerator_limits_premium" => array(1)
					),
				),
				'ai_helper_sc_vgenerator_levels_premium' => array(
					"title" => esc_html__("User levels with limits", 'trx_addons'),
					"desc" => wp_kses_data( __( 'How many video a user can generate depending on their subscription level. The "Default" limit is used for regular registered users. For more flexible settings, use special plugins to separate access levels.', 'trx_addons' ) ),
					"dependency" => array(
						"ai_helper_sc_vgenerator_limits_premium" => array(1)
					),
					"clone" => true,
					"std" => array(),
					"type" => "group",
					"fields" => array(
						"level" => array(
							"title" => esc_html__("Level", 'trx_addons'),
							"class" => "trx_addons_column-1_3",
							"std" => "default",
							"options" => apply_filters( 'trx_addons_filter_sc_vgenerator_list_user_levels', array( 'default' => __( 'Default', 'trx_addons' ) ) ),
							"type" => "select"
						),
						"limit" => array(
							"title" => esc_html__("Video limit", 'trx_addons'),
							"class" => "trx_addons_column-1_3",
							"std" => "",
							"type" => "text"
						),
						"per" => array(
							"title" => esc_html__("per", 'trx_addons'),
							"class" => "trx_addons_column-1_3",
							"std" => "day",
							"options" => $is_options_page ? Lists::get_list_periods() : array(),
							"type" => "select"
						),
					)
				),
				'ai_helper_sc_vgenerator_limit_alert_premium' => array(
					"title" => esc_html__('Limits reached alert', 'trx_addons'),
					"desc" => wp_kses_data( __('The message that the visitor will see when the limit of requests or generated video (per hour) is exceeded.', 'trx_addons') ),
					"std" => wp_kses( apply_filters( 'trx_addons_sc_vgenerator_limit_alert_default_premium',
								'<h5>' . __( 'Limits are reached!', 'trx_addons' ) . '</h5>'
								. '<p>' . __( 'The limit of the number of requests from a single visitor or the number of video that can be generated per hour has been reached.', 'trx_addons' ) . '</p>'
								. '<p>' . __( 'Please, try again later.', 'trx_addons' ) . '</p>'
							), 'trx_addons_kses_content' ),
					"type" => "text_editor",
					"dependency" => array(
						"ai_helper_sc_vgenerator_limits_premium" => array(1)
					),
				),

				'ai_helper_section_end' => array(
					"type" => "panel_end"
				),
			) ) );

			return $options;
		}

		/**
		 * Fill 'Post types' before show ThemeREX Addons Options
		 * 
		 * @hooked trx_addons_filter_before_show_options
		 *
		 * @param array $options  Array of options
		 * 
		 * @return array  	  Modified array of options
		 */
		function fill_options( $options ) {
			if ( isset( $options['ai_helper_process_selected_post_types'] ) ) {
				$options['ai_helper_process_selected_post_types']['options'] = trx_addons_get_list_posts_types();
			}
			return $options;
		}

		/**
		 * Fix option params in the ThemeREX Addons Options
		 * 
		 * @hooked trx_addons_filter_before_show_options
		 *
		 * @param array $options  Array of options
		 * 
		 * @return array  	  Modified array of options
		 */
		function fix_options( $options ) {
			foreach ( array( 'ai_helper_sc_tgenerator_limit_per_request', 'ai_helper_sc_chat_limit_per_request' ) as $option ) {
				$max_tokens = Utils::get_max_tokens( $option == 'ai_helper_sc_tgenerator_limit_per_request' ? 'sc_tgenerator' : 'sc_chat' );
				foreach ( array( '', '_premium' ) as $suffix ) {
					$name = $option . $suffix;
					if ( ! empty( $options[ $name ]['std'] ) && $options[ $name ]['std'] > $max_tokens ) {
						$options[ $name ]['std'] = $max_tokens;
					}
					if ( ! empty( $options[ $name ]['val'] ) && $options[ $name ]['val'] > $max_tokens ) {
						$options[ $name ]['val'] = $max_tokens;
					}
					if ( ! empty( $options[ $name ]['max'] ) ) {
						$options[ $name ]['max'] = $max_tokens;
					}
				}
			}
			return $options;
		}

		/**
		 * Clear some addon specific options before export
		 * 
		 * @hooked trx_addons_filter_export_options
		 * 
		 * @param array $options  Array of options
		 * 
		 * @return array  	  Modified array of options
		 */
		 function remove_token_from_export( $options ) {
			// List options to reset: 'key' => default_value
			$reset_options = apply_filters( 'trx_addons_filter_ai_helper_options_to_reset', array(
				'ai_helper_token_openai' => array( 'default' => '' ),
				'ai_helper_proxy_openai' => array( 'default' => '' ),
				'ai_helper_proxy_auth_openai' => array( 'default' => '' ),
				'ai_helper_models_openai_assistants' => array( 'default' => array() ),
				'ai_helper_token_stable_diffusion' => array( 'default' => '' ),
				'ai_helper_token_stability_ai' => array( 'default' => '' ),
				'ai_helper_token_flowise_ai' => array( 'default' => '' ),
				'ai_helper_host_flowise_ai' => array( 'default' => '' ),
				'ai_helper_models_flowise_ai' => array( 'default' => array() ),
				'ai_helper_token_google_ai' => array( 'default' => '' ),
				'ai_helper_proxy_google_ai' => array( 'default' => '' ),
				'ai_helper_proxy_auth_google_ai' => array( 'default' => '' ),
				'ai_helper_token_lumalabs_ai' => array( 'default' => '' ),
				//'ai_helper_embed_chats' => array( 'default' => array(), 'field' => 'code', 'filter' => '<script' ),	// Remove chats if embed code contains <script> tag
				'ai_helper_embed_chats' => array( 'default' => array() ),
			) );
			// Reset options
			foreach ( $reset_options as $option => $value ) {
				if ( ! empty( $options['trx_addons_options'][ $option ] ) ) {
					// Remove options by filter
					if ( ! empty( $value['filter'] ) ) {
						if ( is_array( $options['trx_addons_options'][ $option ] ) ) {
							foreach( $options['trx_addons_options'][ $option ] as $k => $v ) {
								if ( ! empty( $v[ $value['field'] ] ) && strpos( $v[ $value['field'] ], $value['filter'] ) !== false ) {
									unset( $options['trx_addons_options'][ $option ][ $k ] );
								}
							}
						} else if ( strpos( $options['trx_addons_options'][ $option ], $value['filter'] ) !== false ) {
							$options['trx_addons_options'][ $option ] = $value['default'];
						}
					// Reset option to default value
					} else {
						$options['trx_addons_options'][ $option ] = $value['default'];
					}
				}
			}
			// Remove log
			if ( isset( $options['trx_addons_ai_helper_log'] ) ) {
				unset( $options['trx_addons_ai_helper_log'] );
			}
			// Remove chat topics
			if ( isset( $options['trx_addons_sc_chat_topics'] ) ) {
				unset( $options['trx_addons_sc_chat_topics'] );
			}
			return $options;
		}

		/**
		 * Clear a chat history before export
		 * 
		 * @hooked trx_addons_filter_export_single_usermeta
		 * 
		 * @param array $row  Array of usermeta
		 * @param array $original_row  Array of original usermeta
		 * @param object $importer  Importer object
		 * 
		 * @return array  	  Modified array of usermeta
		 */
		function remove_chat_history_from_export( $row, $original_row, $importer ) {
			if ( ! empty( $row['meta_key'] ) && $row['meta_key'] == 'trx_addons_sc_chat_history' ) {
				$row['meta_value'] = '';
			}
			return $row;
		}
	}
}
