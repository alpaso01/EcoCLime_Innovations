<?php
namespace TrxAddons\AiHelper;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use \TrxAddons\Core\Singleton;

/**
 * Class to make queries to the OpenAi API
 */
abstract class Api extends Singleton {

	/**
	 * The object to log queries to the API
	 *
	 * @access private
	 * 
	 * @var Logger  The object to log queries to the API
	 */
	var $logger = null;
	var $logger_section = '';

	/**
	 * The option name which store the token to take access to the API
	 */
	var $token_option = '';

	/**
	 * The object of the API
	 *
	 * @access private
	 * 
	 * @var api  The object of the API
	 */
	var $api = null;

	/**
	 * Class constructor.
	 *
	 * @access protected
	 */
	protected function __construct() {
		parent::__construct();
		$this->logger = Logger::instance();
	}

	/**
	 * Return an API token for the API from the plugin options.
	 * This method is a wrapper for the get_token() method to allow to override it in the child classes.
	 * 
	 * @access public
	 * 
	 * @return string  API key for the API
	 */
	public function get_api_key() {
		return $this->get_token();
	}

	/**
	 * Return an API token for the API from the plugin options
	 * 
	 * @access protected
	 * 
	 * @return string  API token for the API
	 */
	protected function get_token() {
		return trx_addons_get_option( $this->token_option, '' );
	}

	/**
	 * Return an object of the API
	 * 
	 * @param string $token  Access token for the API
	 * 
	 * @return api  The object of the API
	 */
	abstract public function get_api( $token = '' );

	/**
	 * Return a number of tokens from the models list for the specified model
	 *
	 * @access static
	 * 
	 * @param string $models  List of models with their parameters
	 * @param string $model   Model name for the API. If '*' - return a maximum value from all models
	 * @param string $key     The key of the parameter to get
	 * 
	 * @return int  The number of tokens from the models list for the specified model
	 */
	static function get_tokens_from_list( $models, $model, $key ) {
		$tokens = 0;
		if ( ! empty( $model ) && ! empty( $models ) && is_array( $models ) ) {
			foreach ( $models as $k => $v ) {
				if ( $model == '*' ) {
					$tokens = max( $tokens, ! empty( $v[ $key ] ) ? (int)$v[ $key ] : 0 );
				} else {
					if ( $k == $model ) {
						$tokens = ! empty( $v[ $key ] ) ? (int)$v[ $key ] : 0;
						break;
					}
				}
			}
		}
		return $tokens;
	}

	/**
	 * Calculate the number of tokens for the API
	 * 
	 * @access protected
	 * 
	 * @param string $text  Text to calculate
	 * 
	 * @return int  Number of tokens for the API
	 */
	protected function count_tokens( $text ) {
		$tokens = 0;

		// Way 1: Get number of words and multiply by coefficient		
		// $words = count( explode( ' ', $text ) );
		// $coeff = strpos( $text, '<!-- wp:' ) !== false ? $this->blocks_to_tokens_coeff : $this->words_to_tokens_coeff;
		// $tokens = round( $words * $coeff );

		// Way 2: Get number of tokens via utility function with tokenizer
		// if ( ! function_exists( 'gpt_encode' ) ) {
		// 	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_ADDONS . 'ai-helper/vendors/gpt3-encoder/gpt3-encoder.php';
		// }
		// $tokens = count( (array) gpt_encode( $text ) );

		// Way 3: Get number of tokens via class tokenizer (same algorithm)
		$tokens = count( (array) \Rahul900day\Gpt3Encoder\Encoder::encode( $text ) );

		return $tokens;
	}

}
