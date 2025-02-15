<?php
namespace TrxAddons\AiHelper\Elementor;

use TrxAddons\AiHelper\OpenAi;
use TrxAddons\AiHelper\OpenAiAssistants;

if ( ! class_exists( 'ContentGenerator' ) ) {
    /**
	 * Abstract class for AI Content Generator
	 */
	abstract class ContentGenerator {

		var $generator_model = 'openai/gpt-4o-mini';

        /**
		 * Check if AI Helper is allowed
		 */
		public static function is_allowed() {
			return OpenAi::instance()->get_api_key() != ''
					|| OpenAiAssistants::instance()->get_api_key() != '';
		}

		/**
		 * Get the model for the company generator
		 * 
		 * @return string  The model name
		 */
		function get_model() {
			return apply_filters( 'trx_addons_filter_ai_helper_elementor_generator_model', $this->generator_model );
		}

		/**
		 * Callback function to fetch answer from the assistant
		 * 
		 * @hooked 'wp_ajax_trx_addons_ai_helper_company_generator_fetch'
		 * @hooked 'wp_ajax_nopriv_trx_addons_ai_helper_company_generator_fetch'
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
					'fields' => '',
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
			trx_addons_ajax_response( apply_filters( 'trx_addons_filter_ai_helper_generator_fetch', $answer ) );
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

			if ( ! empty( $response['choices'][0]['message']['role'] ) && $response['choices'][0]['message']['role'] == 'assistant'
				&& ! empty( $response['choices'][0]['message']['content'] ) && substr( $response['choices'][0]['message']['content'], 0, 1 ) == '{'
			) {
				$answer['data']['fields'] = json_decode( $response['choices'][0]['message']['content'], true );
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