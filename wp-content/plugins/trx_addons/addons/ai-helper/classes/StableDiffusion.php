<?php
namespace TrxAddons\AiHelper;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class to make queries to the Stable Diffusion API
 */
class StableDiffusion extends Api {

	/**
	 * The API to use: 'sd' - Stable Diffusion, 'ml' - ModelsLab
	 * 
	 * @access private
	 * 
	 * @var use_api  The API to use
	 */
	var $use_api = '';

	/**
	 * Plugin constructor.
	 *
	 * @access protected
	 */
	protected function __construct() {
		parent::__construct();
		$this->logger_section = 'stable-diffusion';
		$this->token_option = 'ai_helper_token_stable_diffusion';
	}

	/**
	 * Return a base URL to the vendor site
	 * 
	 * @param string $endpoint  The endpoint to use
	 * @param string $type      The type of the URL: api or site. Default: site
	 * 
	 * @return string  The URL to the vendor site
	 */
	public function get_url( $endpoint = '', $type = 'site' ) {
		return $this->use_api == 'sd'
					? \ThemeRex\StableDiffusion\Images::baseUrl( $endpoint, $type )
					: \ThemeRex\ModelsLab\Images::baseUrl( $endpoint, $type );
	}

	/**
	 * Set the API to use: 'sd' - Stable Diffusion, 'ml' - ModelsLab
	 * 
	 * @param string $use_api  API to use
	 */
	public function set_api( $use_api ) {
		$this->use_api = $use_api;
		$this->api = null;
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
				if ( empty( $this->use_api ) ) {
					$this->use_api = trx_addons_get_option( 'ai_helper_use_api_stable_diffusion', 'sd' );
				}
				$this->api = $this->use_api == 'sd'
								? new \ThemeRex\StableDiffusion\Images( $token )
								: new \ThemeRex\ModelsLab\Images( $token );
			}
		}
		return $this->api;
	}

	/**
	 * Return a guidance scale for the API
	 * 
	 * @access protected
	 * 
	 * @return float  Guidance scale for the API
	 */
	protected function get_guidance_scale() {
		return (float)trx_addons_get_option( 'ai_helper_guidance_scale_stable_diffusion', 7.5 );
	}

	/**
	 * Return inference steps for the API
	 * 
	 * @access protected
	 * 
	 * @return int  Inference steps for the API
	 */
	protected function get_inference_steps() {
		return (int)trx_addons_get_option( 'ai_helper_inference_steps_stable_diffusion', 21 );
	}

	/**
	 * Prepare arguments for the API format
	 * 
	 * @access protected
	 * 
	 * @param array $args  Arguments to prepare
	 * 
	 * @return array  Prepared arguments
	 */
	protected function prepare_args( $args ) {
		// token => key
		if ( ! isset( $args['key'] ) ) {
			$args['key'] = $args['token'];
			unset( $args['token'] );
		}
		// size => width, height
		if ( ! isset( $args['width'] ) && ! empty( $args['size'] ) ) {
			$size = explode( 'x', $args['size'] );
			if ( count( $size ) == 2 ) {
				$args['width'] = (int)$size[0];
				$args['height'] = (int)$size[1];
			}
		}
		unset( $args['size'] );
		// n => samples
		if ( ! isset( $args['samples'] ) && isset( $args['n'] ) ) {
			$args['samples'] = max( 1, min( 4, (int)$args['n'] ) );
			unset( $args['n'] );
		}
		// model => model_id
		if ( ! isset( $args['model_id'] ) && isset( $args['model'] ) ) {
			$args['model_id'] = $args['model'];
			unset( $args['model'] );
		}
		if ( ! empty( $args['model_id'] ) ) {
			$args['model_id'] = str_replace(
				array( 'stable-diffusion/default', 'stable-diffusion/' ),
				array( '', '' ),
				$args['model_id']
			);
		}
		if ( empty( $args['model_id'] ) ) {
			unset( $args['model_id'] );
		}
		// image => init_image
		if ( ! isset( $args['init_image'] ) && isset( $args['image'] ) ) {
			$args['init_image'] = $args['image'];
			unset( $args['image'] );
		}
		// LoRA models and strength
		if ( ! empty( $args['model_id'] ) && ! empty( $args['lora_model'] ) ) {
			$parts = array_map( 'trim', explode( ',', $args['lora_model'] ) );
			$lora_model = '';
			$lora_strength = '';
			foreach( $parts as $part ) {
				$parts2 = array_map( 'trim', explode( '=', $part ) );
				$lora_model .= ( $lora_model ? ',' : '' ) . $parts2[0];
				if ( count( $parts2 ) > 1 ) {
					$lora_strength .= ( $lora_strength ? ',' : '' ) . $parts2[1];
				}
			}
			if ( ! empty( $lora_model ) ) {
				$args['lora_model'] = $lora_model;
				if ( ! empty( $lora_strength ) ) {
					$args['lora_strength'] = $lora_strength;
				}
			} else {
				unset( $args['lora_model'] );
			}
		}
		return $args;
	}

	/**
	 * Get a list of available models
	 *
	 * @access public
	 * 
	 * @param array $args  Query arguments
	 * 
	 * @return array  Response from the API
	 */
	public function list_models( $args = array(), $type = 'stable_diffusion' ) {
		$args = array_merge( array(
			'token' => $this->get_token(),
		), $args );

		// Prepare arguments for SD API format
		$args = $this->prepare_args( $args );

		$response = false;

		if ( ! empty( $args['key'] ) ) {

			$api = $this->get_api( $args['key'] );

			$response = $api->listModels( $args );

			if ( is_array( $response ) ) {
				if ( ! empty( $type ) && $type != 'all' && is_array( $response ) ) {
					foreach( $response as $k => $v ) {
						if ( ! empty( $v['model_category'] ) && strpos( $v['model_category'], $type ) === false ) {
							unset( $response[ $k ] );
						}
					}
				}
			} else {
				$response = false;
			}
		}

		return $response;
	}

	/**
	 * Generate images via API
	 *
	 * @access public
	 * 
	 * @param array $args  Query arguments
	 * 
	 * @return array  Response from the API
	 */
	public function generate_images( $args = array() ) {
		$args = array_merge( array(
			'token' => $this->get_token(),
			'prompt' => '',
			'negative_prompt' => '',
			'size' => '1024x1024',
			'n' => 1,
			'model' => '',
			'num_inference_steps' => (int)$this->get_inference_steps(),
			'guidance_scale' => (float)$this->get_guidance_scale()
		), $args );

		// Save a model name for the log
		$model = str_replace( 'stable-diffusion/', '', ! empty( $args['model'] ) ? $args['model'] : 'stable-diffusion/default' );
		$args_orig = $args;

		// Prepare arguments for SD API format
		$args = $this->prepare_args( $args );

		$response = false;

		if ( ! empty( $args['key'] ) && ! empty( $args['prompt'] ) ) {

			$api = $this->get_api( $args['key'] );

			$response = $api->textToImage( $args );

			if ( is_array( $response ) ) {
				$this->logger->log( $response, $model, $args_orig, $this->logger_section );
			} else {
				$response = false;
			}
		}
		return $response;

	}


	/**
	 * Make variations of the image via API
	 *
	 * @access public
	 * 
	 * @param array $args  Query arguments
	 * 
	 * @return array  Response from the API
	 */
	public function make_variations( $args = array() ) {
		$args = array_merge( array(
			'token' => $this->get_token(),
			'prompt' => '',
			'image' => '',
			'size' => '1024x1024',
			'n' => 1,
			'model' => '',
			'num_inference_steps' => (int)$this->get_inference_steps(),
			'guidance_scale' => (float)$this->get_guidance_scale()
		), $args );

		// Save a model name for the log
		$model = str_replace( 'stable-diffusion/', '', ! empty( $args['model'] ) ? $args['model'] : 'stable-diffusion/default' );
		$args_orig = $args;

		// Prepare arguments for SD API format
		$args = $this->prepare_args( $args );
		
		$response = false;

		if ( ! empty( $args['key'] ) && ! empty( $args['init_image'] ) ) {

			$api = $this->get_api( $args['key'] );

			if ( empty( $args['prompt'] ) ) {
				$args['prompt'] = __( 'Make variations of the image.', 'trx_addons' );
			}

			$response = $api->imageToImage( $args );

			if ( is_array( $response ) ) {
				$this->logger->log( $response, $model, $args_orig, $this->logger_section );
			} else {
				$response = false;
			}
		}

		return $response;

	}


	/**
	 * Upscale the image via API
	 *
	 * @access public
	 * 
	 * @param array $args  Query arguments
	 * 
	 * @return array  Response from the API
	 */
	public function upscale( $args = array() ) {
		$args = array_merge( array(
			'token' => $this->get_token(),
			'model' => 'default',
			'image' => '',
			'scale' => 2,
		), $args );

		// Save a model name for the log
		$model = str_replace( 'stable-diffusion/', '', ! empty( $args['model'] ) ? $args['model'] : 'stable-diffusion/upscale-sd-default' );
		$args_orig = $args;

		// Prepare arguments for SD API format
		$args = $this->prepare_args( $args );

		unset( $args['model'] );
		unset( $args['model_id'] );

		$upscalers = Lists::get_sd_upscalers();
		if ( ! empty( $upscalers[ $model ]['model_id'] ) ) {
			$args['model_id'] = $upscalers[ $model ]['model_id'];
		}

		if ( ! empty( $args['init_image'] ) ) {
			$args['url'] = $args['init_image'];
			unset( $args['init_image'] );
		}
		
		$response = false;

		if ( ! empty( $args['key'] ) && ! empty( $args['url'] ) ) {

			$api = $this->get_api( $args['key'] );

			$response = $api->imageUpscale( $args );

			if ( is_array( $response ) ) {
				if ( ! empty( $response['output'] ) && ! is_array( $response['output'] ) ) {
					$response['output'] = array( $response['output'] );
				}
				$this->logger->log( $response, $model, $args_orig, $this->logger_section );
			} else {
				$response = false;
			}
		}

		return $response;

	}

	/**
	 * Fetch queued images via API
	 *
	 * @access public
	 * 
	 * @param array $args  Query arguments
	 * 
	 * @return array  Response from the API
	 */
	public function fetch_images( $args = array() ) {
		$args = array_merge( array(
			'token' => $this->get_token(),
			'fetch_url' => '',
			'fetch_id' => '',
		), $args );

		// Prepare arguments for SD API format
		$args = $this->prepare_args( $args );

		$response = false;

		if ( ! empty( $args['key'] ) && ! empty( $args['fetch_id'] ) ) {

			$api = $this->get_api( $args['key'] );

			$response = $api->fetchImages( $args );

			if ( ! is_array( $response ) ) {
				$response = false;
			}
		}
	
		return $response;

	}

}
