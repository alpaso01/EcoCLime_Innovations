<?php
namespace TrxAddons\AiHelper;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use \TrxAddons\Core\Singleton;

/**
 * Class to log queries to the OpenAi API: used tokens in prompt, completion and total
 */
class Logger extends Singleton {

	var $log = array();

	var $default_section = 'open-ai';

	/**
	 * Plugin constructor.
	 *
	 * @access protected
	 */
	protected function __construct() {
		parent::__construct();
		$saved = get_option( 'trx_addons_ai_helper_log' );
		if ( is_array( $saved ) ) {
			$this->log = ! isset( $saved[ $this->default_section ] ) ? array( $this->default_section => $saved ) : $saved;
		}
	}

	/**
	 * Return an empty array with log entries for the model
	 * 
	 * @access private
	 * 
	 * @return array  Array with log entries for the model
	 */
	private function get_empty_log() {
		return array(
			'total_tokens' => 0,
			'prompt_tokens' => 0,
			'completion_tokens' => 0,
		);
	}

	/**
	 * Log a query results
	 * 
	 * @access public
	 * 
	 * @param array $response  Response from OpenAi API with completion and usage data
	 */
	public function log( $response, $type = 'chat', $args = array(), $section = '' ) {

		if ( empty( $section ) ) {
			$section = $this->default_section;
		}
		if ( ! isset( $this->log[ $section ] ) ) {
			$this->log[ $section ] = array();
		}
		// Text generation usage
		if ( in_array( $type, array( 'query', 'chat' ) ) ) {
			if ( ! empty( $response['model'] ) && ! empty( $response['usage'] ) ) {
				if ( empty( $this->log[ $section ][ $response['model'] ] ) ) {
					$this->log[ $section ][ $response['model'] ] = $this->get_empty_log();
				}
				foreach ( array_keys( $this->log[ $section ][ $response['model'] ] ) as $k ) {
					if ( ! empty( $response['usage'][ $k ] ) ) {
						$this->log[ $section ][ $response['model'] ][ $k ] += $response['usage'][ $k ];
					}
				}
			}

		// Image generation: Open AI API
		} else if ( $section == 'open-ai' ) {
			// Convert entry 'images' to the 'images/default'
			if ( ! empty( $this->log[ $section ][ 'images' ] ) ) {
				$this->log[ $section ][ 'images/default' ] = ! empty( $this->log[ $section ][ 'images/default' ] )
																? array_merge( $this->log[ $section ][ 'images/default' ], $this->log[ $section ][ 'images' ] )
																: $this->log[ $section ][ 'images' ];
				unset( $this->log[ $section ][ 'images' ] );
			}
			// Convert entries without the prefix 'images' to the 'images/$type'
			if ( strpos( $type, 'images' ) === false ) {
				if ( ! empty( $this->log[ $section ][ $type ] ) ) {
					$this->log[ $section ][ 'images/' . $type ] = $this->log[ $section ][ $type ];
					unset( $this->log[ $section ][ $type ] );
				}
				// Add the prefix 'images' to the type
				$type = 'images/' . $type;
			}
			// Get the number of generated images
			$number = ! empty( $args['n'] ) ? (int)$args['n'] : 1;
			// if ( ! empty( $response['data'] ) ) {
			// 	$number = count( $response['data'] );
			// }
			// Get the size of generated images
			$size = ! empty( $args['size'] ) ? $args['size'] : 'unknown';
			// Save to the log
			if ( empty( $this->log[ $section ][ $type ] ) ) {
				$this->log[ $section ][ $type ] = array();
			} else if ( ! is_array( $this->log[ $section ][ $type ] ) ) {
				$this->log[ $section ][ $type ] = array( 'unknown' => $this->log[ $section ][ $type ] );
			}
			if ( empty( $this->log[ $section ][ $type ][ $size ] ) ) {
				$this->log[ $section ][ $type ][ $size ] = 0;
			}
			$this->log[ $section ][ $type ][ $size ] += $number;

		// Image generation: ModelsLab (ex Stable Diffusion) API & Stability AI
		} else if ( in_array( $section, array( 'stable-diffusion', 'stability-ai' ) ) ) {
			$number = ! empty( $args['samples'] )
						? (int)$args['samples']
						: ( ! empty( $args['n'] )
							? (int)$args['n']
							: 1
							);
			$size = ! empty( $args['size'] )
						? $args['size']
						: ( ! empty( $args['width'] ) ? $args['width'] . 'x' . $args['height'] : 'unknown' );
			// if ( ! empty( $response['output'] ) && is_array( $response['output'] ) ) {
			// 	$number = count( $response['output'] );
			// }
			if ( empty( $this->log[ $section ][ $type ] ) ) {
				$this->log[ $section ][ $type ] = array();
			}
			if ( empty( $this->log[ $section ][ $type ][ $size ] ) ) {
				$this->log[ $section ][ $type ][ $size ] = 0;
			}
			$this->log[ $section ][ $type ][ $size ] += $number;

		// Audio/Music generation
		} else if ( substr( $section, -6 ) == '/audio' ) {
			$number = ! empty( $args['n'] ) ? (int)$args['n'] : 1;
			$subtype = 'default';
			if ( empty( $this->log[ $section ][ $type ] ) ) {
				$this->log[ $section ][ $type ] = array();
			}
			if ( empty( $this->log[ $section ][ $type ][ $subtype ] ) ) {
				$this->log[ $section ][ $type ][ $subtype ] = 0;
			}
			$this->log[ $section ][ $type ][ $subtype ] += $number;

		// Support
		} else if ( $type == 'add-support' ) {
			$months = ! empty( $response['months'] ) ? (int)$response['months'] : 0;
			$this->log[ $section ][ date('Y-m-d H:i:s') ] = ! empty( $response['error'] )
																? ( ! empty( $response['error']['message'] )
																	? $response['error']['message']
																	: ( is_string( $response['error'] )
																		? $response['error']
																		: sprintf( __( 'Unexpected server response. Support period is not extended with key %s', 'trx_addons' ), $args['key'] )
																		)
																	)
																: ( $months > 0
																	? sprintf( __( 'Support period is extended for %d months with key %s', 'trx_addons' ), $months, $args['key'] )
																	: sprintf( __( 'Support period is not extended with key %s', 'trx_addons' ), $args['key'] )
																	);

		// Video generation
		} else if ( in_array( $section, array( 'lumalabs-ai' ) ) ) {
			$number = ! empty( $args['n'] ) ? (int)$args['n'] : 1;
			$subtype = 'default';
			if ( empty( $this->log[ $section ][ $type ] ) ) {
				$this->log[ $section ][ $type ] = array();
			}
			if ( empty( $this->log[ $section ][ $type ][ $subtype ] ) ) {
				$this->log[ $section ][ $type ][ $subtype ] = 0;
			}
			$this->log[ $section ][ $type ][ $subtype ] += $number;
		}
		update_option( 'trx_addons_ai_helper_log', $this->log );
	}

	/**
	 * Get log
	 *
	 * @access public
	 * 
	 * @param string $model  Model name
	 * @param string $key    Key to get from log
	 * 
	 * @return int|array     Value from log for the specified model and key or whole log for the specified model or whole log for all models
	 */
	public function get_log( $section = '' ) {
		if ( empty( $section ) ) {
			return $this->log;
		} else if ( ! empty( $this->log[ $section ] ) ) {
			return $this->log[ $section ];
		} else {
			return array();
		}
	}

	/**
	 * Get log as a report string
	 *
	 * @access public
	 */
	public function get_log_report( $section = '' ) {
		$report = '';
		$log = $this->get_log();
		if ( is_array( $log ) ) {
			foreach ( $log as $sec => $data ) {
				if ( ! empty( $section ) && $section != $sec ) {
					continue;
				}
				if ( empty( $data ) || ! is_array( $data ) ) {
					continue;
				}
				$images_total = 0;
				$subreport = '';
				// Sort by model name (put 'default' model first, 'images' model last)
				uksort( $data, function( $a, $b ) {
					if ( strpos( $a, 'images' ) !== false ) return 1;
					if ( strpos( $b, 'images' ) !== false ) return -1;
					if ( $a == 'default' ) return -1;
					if ( $b == 'default' ) return 1;
					return strcasecmp( $a, $b );
				} );
				foreach ( $data as $model => $tokens ) {
					if ( $model == 'chat' ) continue;		// Skip 'chat' model (legacy data, not used now)
					$total = 0;
					$details = '';
					// Chat usage
					if ( isset( $tokens['total_tokens'] ) ) {
						$total = $tokens['total_tokens'];
						$details = sprintf( __( '%1$d in prompts, %2$d in completions', 'trx_addons' ), $tokens['prompt_tokens'], $tokens['completion_tokens'] );
					// Images generated
					} else {
						if ( $model == 'images' && ! is_array( $tokens ) ) {
							$tokens = array( 'unknown' => $tokens );
						}
						// Sort by size
						if ( is_array( $tokens ) ) {
							uksort( $tokens, function( $a, $b ) {
								$a = explode( 'x', $a );
								$b = explode( 'x', $b );
								return ( (int)$a[0] * (int)($a[1] ?? 0) ) < ( (int)$b[0] * (int)($b[1] ?? 0) ) ? -1 : 1;
							} );
							foreach ( $tokens as $size => $count ) {
								$details .= ( ! empty( $details ) ? ', ' : '' ) . sprintf( __( '%1$s: <b>%2$d</b>', 'trx_addons' ), $size, (int)$count );
								$total += (int)$count;
								$images_total += (int)$count;
							}
						}
					}
					if ( $total > 0 ) {
						$subreport .= '<pre>'
										. str_repeat( '&nbsp;', 4 )
										. str_pad( sprintf( __( 'Model "%s":', 'trx_addons' ), $model ), 40 )
										. ' <b>' . str_pad( $total, 8, ' ', STR_PAD_LEFT ) . '</b>'
										. ' (' .  $details . ')'
									. '</pre>';
					}
				}
				$report = '<div class="trx_addons_ai_helper_log">'
								. '<div class="trx_addons_ai_helper_log_title">'
									. '<pre><b><u>'
										.  ( empty( $section )
											? ucfirst( str_replace( '-', ' ', $sec ) )
											: ( $section == 'open-ai'
												? __( 'Tokens usage & generated images', 'trx_addons' )
												: ( in_array( $section, array( 'open-ai-assistants', 'flowise-ai', 'google-ai' ) )
													? __( 'Tokens usage', 'trx_addons' )
													: ( $section == 'lumalabs-ai'
														? __( 'Generated videos:', 'trx_addons' )
														: sprintf( __( 'Generated images: %d', 'trx_addons' ), $images_total )
														)
													)
												)
											)
									. '</u></b></pre>'
								. '</div>'
								. '<div class="trx_addons_ai_helper_log_data">'
									. $subreport
								. '</div>'
							. '</div>';
			}
		}
		return $report;
	}
}
