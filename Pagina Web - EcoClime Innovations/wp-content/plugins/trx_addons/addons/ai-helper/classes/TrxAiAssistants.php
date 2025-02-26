<?php
namespace TrxAddons\AiHelper;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class to make queries to the TrxAiAssistants API
 */
class TrxAiAssistants extends OpenAiAssistants {

	/**
	 * Class constructor.
	 *
	 * @access protected
	 */
	protected function __construct() {
		parent::__construct();
		$this->logger_section = 'trx-ai-assistants';
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
				$this->api = new \ThemeRex\Ai\TrxAiAssistants( $token );
			}
		}
		return $this->api;
	}

	/**
	 * Return an API token for the API from the plugin options
	 * 
	 * @access protected
	 * 
	 * @return string  API token for the API
	 */
	protected function get_token() {
		return '-not-need-';
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
		return 'trx-ai-assistants/ai-assistant';
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
		$max_tokens = apply_filters( 'trx_addons_filter_ai_helper_trx_ai_assistants_max_tokens', 4000 );
		return (int)$max_tokens;
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
		$args = parent::prepare_args( $args );
		if ( ! empty( $args['model'] ) ) {
			$args['model'] = str_replace( 'trx-ai-assistants/', '', $args['model'] );
		}
		return $args;
	}


	/**
	 * Extend the support period
	 *
	 * @access public
	 * 
	 * @param array $support_key  A purchase key for the support
	 * 
	 * @return array  Response from the API
	 */
	public function add_support_key( $support_key ) {
		if ( ! empty( $support_key ) ) {
			$api = $this->get_api( $this->get_token() );
			$response = $api->addSupportKey( $support_key );
			$this->logger->log( $response, 'add-support', array( 'key' => $support_key ), $this->logger_section . '-support' );
		} else {
			$response = array(
				'status' => 'error',
				'error' => array( 
					'message' => __( 'Support key is empty', 'trx_addons' )
				)
			);
		}
		return $response;
	}

}
