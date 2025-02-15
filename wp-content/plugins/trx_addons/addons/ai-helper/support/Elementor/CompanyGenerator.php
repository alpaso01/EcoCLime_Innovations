<?php
namespace TrxAddons\AiHelper\Elementor;

use TrxAddons\AiHelper\Utils;

if ( ! class_exists( 'CompanyGenerator' ) ) {

    /**
	 * Main class for AI Company Generator
	 */
	class CompanyGenerator extends ContentGenerator {

		/**
		 * Constructor
		 */
		function __construct() {
			// Add the 'Generate Company' button to the plugin's options
			add_action( "trx_addons_action_load_scripts_admin", array( $this, 'options_page_load_scripts' ) );
			add_filter( 'trx_addons_filter_localize_script_admin', array( $this, 'localize_script' ) );

			// AJAX callback for the 'Company Generator' button
			add_action( 'wp_ajax_trx_addons_ai_helper_company_generator', array( $this, 'company_generator' ) );

			// Callback function to fetch answer from the assistant
			add_action( 'wp_ajax_trx_addons_ai_helper_company_generator_fetch', array( $this, 'fetch_answer' ) );
		}

		/**
		 * Load scripts for ThemeREX Addons options page
		 * 
		 * @hooked trx_addons_action_load_scripts_admin
		 * 
		 * @trigger trx_addons_filter_need_options
		 * 
		 * @param bool $all Load all scripts. Default is false. Not used in this function
		 */
		function options_page_load_scripts( $all = false ) {
			if ( apply_filters('trx_addons_filter_need_options', isset( $_REQUEST['page'] ) && $_REQUEST['page'] == 'trx_addons_options' )
				&& self::is_allowed()
			) {
				wp_enqueue_script( 'trx_addons-ai-generator', trx_addons_get_file_url( TRX_ADDONS_PLUGIN_ADDONS . 'ai-helper/support/Elementor/assets/js/company-generator.js' ), array('jquery'), null, true );
			}
		}

		/**
		 * Localize script to show messages
		 * 
		 * @hooked 'trx_addons_filter_localize_script_admin'
		 * 
		 * @param array $vars  Array of variables to be passed to the script
		 * 
		 * @return array  Modified array of variables
		 */
		function localize_script( $vars ) {
			if ( apply_filters('trx_addons_filter_need_options', isset( $_REQUEST['page'] ) && $_REQUEST['page'] == 'trx_addons_options' )
				&& self::is_allowed()
			) {
				$vars['elm_ai_company_generator_bad_data'] = esc_html__( "Unexpected answer from the API server!", 'trx_addons' );
			}
			return $vars;
		}

		/**
		 * Send a query to API to generate a demo data for the company by the industry
		 * 
		 * @hooked 'wp_ajax_trx_addons_ai_helper_company_generator'
		 */
		function company_generator() {

			trx_addons_verify_nonce();

			$answer = array(
				'error' => '',
				'data' => array(
					'fields' => array(),
					'message' => ''
				)
			);

			$fields = trx_addons_get_value_gp( 'fields' );
			if ( empty( $fields['ai_helper_company_name'] ) || empty( $fields['ai_helper_company_industry'] ) ) {
				$answer['error'] = esc_html__( 'Please fill the company name and industry before generate a company data', 'trx_addons' );
			} else {
				$template = json_decode( trx_addons_fgc( trx_addons_get_file_dir( TRX_ADDONS_PLUGIN_ADDONS . 'ai-helper/support/Elementor/assets/ai-templates/company.json' ) ), true );
				if ( isset( $template['process_request']['input']['company_name'] ) && isset( $template['process_request']['input']['industry'] ) ) {
					$template['process_request']['input']['company_name'] = str_replace( '"', "'", $fields['ai_helper_company_name'] );
					$template['process_request']['input']['industry'] = str_replace( '"', "'", $fields['ai_helper_company_industry'] );

					$api = Utils::get_chat_api( $this->get_model() );

					$response = $api->query(
						array(
							'model' => $this->get_model(),
							'system_prompt' => wp_json_encode( $template ),
							'prompt' => true,	// Not need to send the prompt, because all insturctions are in the system_prompt
							'role' => 'company_generator',
							'n' => 1,
							'temperature' => 0.2,
							'max_tokens' => 4000,
						)
					);
					$answer = $this->parse_response( $response, $answer );
				} else {
					$answer['error'] = esc_html__( 'Can not load the company template', 'trx_addons' );
				}
			}

			// Return response to the AJAX handler
			trx_addons_ajax_response( $answer );
		}

    }
}