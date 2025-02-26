<?php
namespace TrxAddons\AiHelper\AssistantTools;

use TrxAddons\AiHelper\TrxAiAssistants;

if ( ! class_exists( 'Helper' ) ) {

	/**
	 * Main class for AI Helper Assistent Tools support
	 */
	class Helper {

		private $logo_types = array( 'regular', 'retina' );
		private $logo_locations = array( 'header', 'mobile-header', 'mobile-menu', 'side-menu', 'footer' );
		private $logo_options_map = array(
			'header-regular' => 'custom_logo',
			'header-retina'  => 'logo_retina',
			'mobile-header-regular' => 'logo_mobile_header',
			'mobile-header-retina'  => 'logo_mobile_header_retina',
			'mobile-menu-regular' => 'logo_mobile',
			'mobile-menu-retina'  => 'logo_mobile_retina',
			'side-menu-regular' => 'logo_side',
			'side-menu-retina'  => 'logo_side_retina',
			'footer-regular' => 'logo_footer',
			'footer-retina'  => 'logo_footer_retina',
		);
		private $scheme_colors = array( 'bg_color', 'bd_color', 'text', 'text_dark', 'text_light', 'text_link', 'text_hover' );

		/**
		 * Constructor
		 */
		function __construct() {
			add_filter( 'trx_addons_filter_api_call', array( $this, 'api_call' ), 10, 3 );
		}

		/**
		 * Controller for the API calls
		 * 
		 * @hooked trx_addons_filter_api_call, 10, 3
		 *
		 * @param string|array $output - the output of the API call
		 * @param string $name - the name of the tool
		 * @param array $args - the arguments of the API call
		 * 
		 * @return string|array $output - the output of the API call. Empty string if the tool is not supported. Array if the tool is supported.
		 * 								  Format: array( 'status' => 'success|error', 'message' => 'message text', 'value' => 'result' )
		 */
		function api_call( $output, $name, $args ) {
			if ( $name == 'get_site_logo' ) {
				$output = $this->get_site_logo( $args );
			} else if ( $name == 'set_site_logo' ) {
				$output = $this->set_site_logo( $args );
			} else if ( $name == 'get_scheme_color' ) {
				$output = $this->get_scheme_color( $args );
			} else if ( $name == 'set_scheme_color' ) {
				$output = $this->set_scheme_color( $args );
			} else if ( $name == 'get_site_title' ) {
				$output = $this->get_site_title( $args );
			} else if ( $name == 'set_site_title' ) {
				$output = $this->set_site_title( $args );
			} else if ( $name == 'get_site_tagline' ) {
				$output = $this->get_site_tagline( $args );
			} else if ( $name == 'set_site_tagline' ) {
				$output = $this->set_site_tagline( $args );
			} else if ( $name == 'get_mouse_helper_state' ) {
				$output = $this->get_mouse_helper_state( $args );
			} else if ( $name == 'set_mouse_helper_state' ) {
				$output = $this->set_mouse_helper_state( $args );
			} else if ( $name == 'add_support_key' ) {
				$output = $this->add_support_key( $args );
			}
			return $output;
		}

		/**
		 * Check the tool arguments - required fields and its values
		 * 
		 * @param array $args - the arguments of the API call
		 * @param array $required - the required fields
		 * 
		 * @return string - Error message if the arguments are wrong. Empty string if the arguments are correct.
		 */
		private function check_args( $args, $required ) {
			$rez = '';
			foreach( $required as $field => $values ) {
				if ( ! isset( $args[ $field ] ) ) {
					$rez = sprintf( __( 'Missing argument: %s', 'trx_addons' ), $field );
					break;
				} else if ( is_array( $values ) && ! in_array( $args[ $field ], $values ) ) {
					$rez = sprintf( __( 'Wrong value "%1$s" for the argument "%2$s"', 'trx_addons' ), $args[ $field ], $field );
					break;
				} else if ( $values === true && empty( $args[ $field ] ) ) {
					$rez = sprintf( __( 'A value "%1$s" for the argument "%2$s" is empty', 'trx_addons' ), $args[ $field ], $field );
					break;
				}
			}
			return $rez;
		}

		/**
		 * Add a support key to extends the use of AI Assistant for the period specified when a customer purchase a "Technical support" or similar item.
		 * 
		 * @param array $args - the arguments of the API call. Required fields:
		 * 						'support_key' => 'support purchase key'
		 * 
		 * @return string|array $output - the output of the API call. Empty string if the tool is not supported. Array if the tool is supported.
		 * 								  Format: array( 'status' => 'success|error', 'message' => 'message text' )
		 */
		private function add_support_key( $args ) {
			if ( ! current_user_can( 'edit_theme_options' ) ) {
				return array( 'status' => 'error', 'message' => __( 'You have no permissions to extend a support period', 'trx_addons' ) );
			}

			$check_rez = $this->check_args( $args, array( 'support_key' => true ) );
			if ( ! empty( $check_rez ) ) {
				return array( 'status' => 'error', 'message' => $check_rez );
			}

			$response = TrxAiAssistants::instance()->add_support_key( $args['support_key'] );

			// Return the result
		 $months = ! empty( $response['months'] ) ? $response['months'] : 1;
			return empty( $response['error'] )
					? array(
						'status' => 'success',
						'message' => sprintf(
										__( 'The support period is extended for the key "%1$s" for %2$s', 'trx_addons'),
										$args['support_key'],
										$months . ' ' . _n( 'month', 'months', $months, 'trx_addons' )
									)
					)
					: array(
						'status' => 'error',
						'message' => ! empty( $response['error']['message'] ) ? $response['error']['message'] : $response['error']
					);
		}

		/**
		 * Get a site logo
		 * 
		 * @param array $args - the arguments of the API call. Required fields:
		 * 						'type' => 'regular|retina',
		 * 						'location' => 'header|mobile-header|mobile-menu|side-menu|footer'
		 * 
		 * @return string|array $output - the output of the API call. Empty string if the tool is not supported. Array if the tool is supported.
		 * 								  Format: array( 'status' => 'success|error', 'message' => 'message text', 'value' => 'result' )
		 */
		private function get_site_logo( $args ) {
			$logo_types = apply_filters( 'trx_addons_filter_ai_helper_tools_values', $this->logo_types, 'site_logo', 'type' );
			$logo_locations = apply_filters( 'trx_addons_filter_ai_helper_tools_values', $this->logo_locations, 'site_logo', 'location' );
			$logo_options_map = apply_filters( 'trx_addons_filter_ai_helper_tools_values', $this->logo_options_map, 'site_logo', 'options' );

			$check_rez = $this->check_args( $args, array( 'type' => $logo_types, 'location' => $logo_locations ) );
			if ( ! empty( $check_rez ) ) {
				return array( 'status' => 'error', 'message' => $check_rez );
			}
			// Get the theme options
			$options = trx_addons_get_theme_options();
			// Get the logo url
			$logo_url = '';
			if ( isset( $logo_options_map[ $args['location'] . '-' . $args['type'] ] ) && isset( $options[ $logo_options_map[ $args['location'] . '-' . $args['type'] ] ] ) ) {
				$logo_url = trx_addons_get_attachment_url( $options[ $logo_options_map[ $args['location'] . '-' . $args['type'] ] ], 'full' );
			} else {
				return array( 'status' => 'error', 'message' => sprintf( __( 'Can\'t get a %1$s logo option for %2$s', 'trx_addons' ), $args['type'], $args['location'] ) );
			}
			// Return the result
			return array(
				'status' => 'success',
				'message' => sprintf(
								__( 'A %1$s logo for %2$s is %3$s', 'trx_addons'),
								$args['type'],
								$args['location'],
								empty( $logo_url ) ? __( 'not selected', 'trx_addons' ) : '<a href="' . esc_url( $logo_url ) . '" target="_blank">' . trx_addons_get_file_name( $logo_url, false ) . '</a>'
							),
				'value' => $logo_url
			);
		}

		/**
		 * Set a site logo
		 * 
		 * @param array $args - the arguments of the API call. Required fields:
		 * 						'type' => 'regular|retina',
		 * 						'location' => 'header|mobile-header|mobile-menu|side-menu|footer'
		 * 						'image' => 'image url'
		 * 
		 * @return string|array $output - the output of the API call. Empty string if the tool is not supported. Array if the tool is supported.
		 * 								  Format: array( 'status' => 'success|error', 'message' => 'message text' )
		 */
		private function set_site_logo( $args ) {
			if ( ! current_user_can( 'edit_theme_options' ) ) {
				return array( 'status' => 'error', 'message' => __( 'You have no permissions to edit theme options', 'trx_addons' ) );
			}

			$logo_types = apply_filters( 'trx_addons_filter_ai_helper_tools_values', $this->logo_types, 'site_logo', 'type' );
			$logo_locations = apply_filters( 'trx_addons_filter_ai_helper_tools_values', $this->logo_locations, 'site_logo', 'location' );
			$logo_options_map = apply_filters( 'trx_addons_filter_ai_helper_tools_values', $this->logo_options_map, 'site_logo', 'options' );

			$check_rez = $this->check_args( $args, array( 'type' => $logo_types, 'location' => $logo_locations, 'image' => true ) );
			if ( ! empty( $check_rez ) ) {
				return array( 'status' => 'error', 'message' => $check_rez );
			}
			// Get the theme options
			$options = trx_addons_get_theme_options();
			// Update the logo url
			if ( isset( $logo_options_map[ $args['location'] . '-' . $args['type'] ] ) && isset( $options[ $logo_options_map[ $args['location'] . '-' . $args['type'] ] ] ) ) {
				// Upload the logo image to the media library
				$attach_id = trx_addons_save_image_to_uploads( array(
					'image' => '',					// binary data of the image
					'image_url' => $args['image'],	// or URL of the image
					'filename' => '',				// filename for the image in the media library. If empty - use the image name from the URL
					'caption' => '',				// caption for the image in the media library
				) );
				// If the image was uploaded successfully - update the logo URL with the attachment URL in the theme options
				if ( ! is_wp_error( $attach_id ) && (int)$attach_id > 0 ) {
					$logo_url = wp_get_attachment_url( $attach_id );
					// Update the logo URL in the theme options
					$options[ $logo_options_map[ $args['location'] . '-' . $args['type'] ] ] = $logo_url;
					// Update the theme options
					trx_addons_update_theme_options( $options );
				} else {
					return array( 'status' => 'error', 'message' => __( 'Can\'t upload a logo image into the media library', 'trx_addons' ) );
				}
			} else {
				return array( 'status' => 'error', 'message' => sprintf( __( 'Can\'t update a %1$s logo option for %2$s', 'trx_addons' ), $args['type'], $args['location'] ) );
			}
			// Return the result
			return array(
				'status' => 'success',
				'message' => sprintf(
								__( 'A %1$s logo for %2$s is updated with a new image %3$s.', 'trx_addons'),
								$args['type'],
								$args['location'],
								'<a href="' . esc_url( $logo_url ) . '" target="_blank">' . trx_addons_get_file_name( $logo_url, false ) . '</a>'
							),
				//'value' => $logo_url
			);
		}

		/**
		 * Get a color from the color scheme
		 * 
		 * @param array $args - the arguments of the API call. Required fields:
		 * 						'scheme' => 'scheme_slug',
		 * 						'color' => 'bg_color|bd_color|text|text_dark|text_light|text_link|text_hover'
		 * 
		 * @return string|array $output - the output of the API call. Empty string if the tool is not supported. Array if the tool is supported.
		 * 								  Format: array( 'status' => 'success|error', 'message' => 'message text', 'value' => 'result' )
		 */
		private function get_scheme_color( $args ) {
			$schemes = trx_addons_get_theme_color_schemes();
			if ( empty( $schemes ) ) {
				return array( 'status' => 'error', 'message' => __( 'Can\'t get color schemes', 'trx_addons' ) );
			}

			$scheme_slugs = apply_filters( 'trx_addons_filter_ai_helper_tools_values', array_keys( $schemes ), 'scheme_color', 'scheme' );
			$scheme_colors = apply_filters( 'trx_addons_filter_ai_helper_tools_values', $this->scheme_colors, 'scheme_color', 'color' );

			$check_rez = $this->check_args( $args, array( 'scheme' => $scheme_slugs, 'color' => $scheme_colors ) );
			if ( ! empty( $check_rez ) ) {
				return array( 'status' => 'error', 'message' => $check_rez );
			}
			// Get the scheme color
			$color = '';
			$color_name = apply_filters( 'trx_addons_filter_get_theme_color_name', $args['color'] );
			if ( isset( $schemes[ $args['scheme'] ]['colors'][ $color_name ] ) ) {
				$color = $schemes[ $args['scheme'] ]['colors'][ $color_name ];
			} else {
				return array( 'status' => 'error', 'message' => sprintf( __( 'Can\'t get a "%1$s" color from the scheme "%2$s"', 'trx_addons' ), $color_name, $args['scheme'] ) );
			}
			// Return the result
			return array(
				'status' => 'success',
				'message' => sprintf(
								__( 'A "%1$s" color from the scheme "%2$s" is "%3$s"', 'trx_addons'),
								$color_name,
								$args['scheme'],
								$color
							),
				'value' => $color
			);
		}

		/**
		 * Set/Update a color in the color scheme
		 * 
		 * @param array $args - the arguments of the API call. Required fields:
		 * 						'scheme' => 'scheme_slug',
		 * 						'color' => 'bg_color|bd_color|text|text_dark|text_light|text_link|text_hover',
		 * 						'value' => 'color value'
		 * 
		 * @return string|array $output - the output of the API call. Empty string if the tool is not supported. Array if the tool is supported.
		 * 								  Format: array( 'status' => 'success|error', 'message' => 'message text' )
		 */
		private function set_scheme_color( $args ) {
			if ( ! current_user_can( 'edit_theme_options' ) ) {
				return array( 'status' => 'error', 'message' => __( 'You have no permissions to edit theme options', 'trx_addons' ) );
			}
			// Get the theme options
			$options = trx_addons_get_theme_options();
			if ( ! empty( $options['scheme_storage'] ) ) {
				$schemes = trx_addons_unserialize( $options['scheme_storage'] );
			} else {
				$schemes = trx_addons_get_theme_color_schemes();
			}
			if ( empty( $schemes ) ) {
				return array( 'status' => 'error', 'message' => __( 'Can\'t update a theme color schemes', 'trx_addons' ) );
			}

			$scheme_slugs = apply_filters( 'trx_addons_filter_ai_helper_tools_values', array_keys( $schemes ), 'scheme_color', 'scheme' );
			$scheme_colors = apply_filters( 'trx_addons_filter_ai_helper_tools_values', $this->scheme_colors, 'scheme_color', 'color' );

			$check_rez = $this->check_args( $args, array( 'scheme' => $scheme_slugs, 'color' => $scheme_colors, 'value' => true ) );
			if ( ! empty( $check_rez ) ) {
				return array( 'status' => 'error', 'message' => $check_rez );
			}
			// Check if a new value is correct
			if ( substr( $args['value'], 0, 1 ) == '#' && ( strlen( $args['value'] ) != 4 || strlen( $args['value'] ) != 7 || $args['value'] == '#rrggbb' ) ) {
				return array( 'status' => 'error', 'message' => sprintf( __( 'Wrong color value "%s"', 'trx_addons' ), $args['value'] ) );
			}
			// Set the scheme color
			$color_name = apply_filters( 'trx_addons_filter_get_theme_color_name', $args['color'] );
			if ( isset( $schemes[ $args['scheme'] ]['colors'][ $color_name ] ) ) {
				$schemes[ $args['scheme'] ]['colors'][ $color_name ] = $args['value'];
				// Update the scheme colors in the theme options
				$options['scheme_storage'] = serialize( $schemes );
				// Update the theme options and set an action to update the styles
				trx_addons_update_theme_options( $options, true );
			} else {
				return array( 'status' => 'error', 'message' => sprintf( __( 'Can\'t set a new "%1$s" color value for the scheme "%2$s"', 'trx_addons' ), $color_name, $args['scheme'] ) );
			}
			// Return the result
			return array(
				'status' => 'success',
				'message' => sprintf(
								__( 'The "%1$s" color value for the scheme "%2$s" is updated', 'trx_addons'),
								$color_name,
								$args['scheme']
							),
				//'value' => $color
			);
		}

		/**
		 * Get a site title from the WordPress settings
		 * 
		 * @param array $args - the arguments of the API call. Not used in this call
		 * 
		 * @return string|array $output - the output of the API call. Empty string if the tool is not supported. Array if the tool is supported.
		 * 								  Format: array( 'status' => 'success|error', 'message' => 'message text', 'value' => 'result' )
		 */
		private function get_site_title( $args = array() ) {
			// Get the site title
			$site_title = get_bloginfo( 'name' );
			// Return the result
			return array(
				'status' => 'success',
				'message' => sprintf(
								__( 'A current title of the site is "%s"', 'trx_addons'),
								empty( $site_title ) ? __( 'not specified', 'trx_addons' ) : $site_title
							),
				'value' => $site_title
			);
		}

		/**
		 * Set a site title
		 * 
		 * @param array $args - the arguments of the API call. Required fields:
		 * 						'title' => 'new site title'
		 * 
		 * @return string|array $output - the output of the API call. Empty string if the tool is not supported. Array if the tool is supported.
		 * 								  Format: array( 'status' => 'success|error', 'message' => 'message text' )
		 */
		private function set_site_title( $args ) {
			if ( ! current_user_can( 'edit_theme_options' ) ) {
				return array( 'status' => 'error', 'message' => __( 'You have no permissions to edit site options', 'trx_addons' ) );
			}

			$check_rez = $this->check_args( $args, array( 'title' => true ) );
			if ( ! empty( $check_rez ) ) {
				return array( 'status' => 'error', 'message' => $check_rez );
			}

			// Update the site title
			if ( ! empty( $args['title'] ) ) {
				update_option( 'blogname', $args['title'] );
			} else {
				return array( 'status' => 'error', 'message' => __( 'Can\'t update a site title with an empty value', 'trx_addons' ) );
			}
			// Return the result
			return array(
				'status' => 'success',
				'message' => sprintf(
								__( 'A site title is updated with a new value "%s".', 'trx_addons'),
								$args['title']
							),
				//'value' => $logo_url
			);
		}

		/**
		 * Get a site tagline from the WordPress settings
		 * 
		 * @param array $args - the arguments of the API call. Not used in this call
		 * 
		 * @return string|array $output - the output of the API call. Empty string if the tool is not supported. Array if the tool is supported.
		 * 								  Format: array( 'status' => 'success|error', 'message' => 'message text', 'value' => 'result' )
		 */
		private function get_site_tagline( $args = array() ) {
			// Get the site tagline
			$site_tagline = get_bloginfo( 'description', 'display' );
			// Return the result
			return array(
				'status' => 'success',
				'message' => sprintf(
								__( 'A current tagline of the site is "%s"', 'trx_addons'),
								empty( $site_tagline ) ? __( 'not specified', 'trx_addons' ) : $site_tagline
							),
				'value' => $site_tagline
			);
		}

		/**
		 * Set a site tagline
		 * 
		 * @param array $args - the arguments of the API call. Required fields:
		 * 						'tagline' => 'new site tagline'
		 * 
		 * @return string|array $output - the output of the API call. Empty string if the tool is not supported. Array if the tool is supported.
		 * 								  Format: array( 'status' => 'success|error', 'message' => 'message text' )
		 */
		private function set_site_tagline( $args ) {
			if ( ! current_user_can( 'edit_theme_options' ) ) {
				return array( 'status' => 'error', 'message' => __( 'You have no permissions to edit site options', 'trx_addons' ) );
			}

			$check_rez = $this->check_args( $args, array( 'tagline' => true ) );
			if ( ! empty( $check_rez ) ) {
				return array( 'status' => 'error', 'message' => $check_rez );
			}

			// Update the site tagline
			if ( ! empty( $args['tagline'] ) ) {
				update_option( 'blogdescription', $args['tagline'] );
			} else {
				return array( 'status' => 'error', 'message' => __( 'Can\'t update a site tagline with an empty value', 'trx_addons' ) );
			}
			// Return the result
			return array(
				'status' => 'success',
				'message' => sprintf(
								__( 'A site tagline is updated with a new value "%s".', 'trx_addons'),
								$args['tagline']
							),
				//'value' => $logo_url
			);
		}

		/**
		 * Get a current state of the Mouse Helper from the plugin's options
		 * 
		 * @param array $args - the arguments of the API call. Not used in this call
		 * 
		 * @return string|array $output - the output of the API call. Empty string if the tool is not supported. Array if the tool is supported.
		 * 								  Format: array( 'status' => 'success|error', 'message' => 'message text', 'value' => 'result' )
		 */
		private function get_mouse_helper_state( $args = array() ) {
			// Get the option value
			$state = (bool)trx_addons_get_option( 'mouse_helper', 0 );
			$visible = (bool)trx_addons_get_option( 'mouse_helper_permanent', 0 );
			// Return the result
			return array(
				'status' => 'success',
				'message' => sprintf(
								__( 'Current Mouse Helper status - %s', 'trx_addons'),
								$state
									? __( 'disabled', 'trx_addons' )
									: __( 'enabled', 'trx_addons' )
										. '. ' . ( $visible ? __( "It's always visible", 'trx_addons' ) : __( "It's only displayed when you hover over supported areas - sliders, videos, etc.", 'trx_addons' ) )
							),
				'value' => $state
			);
		}

		/**
		 * Set a new state of Mouse Helper
		 * 
		 * @param array $args - the arguments of the API call. Required fields:
		 * 						'state' => true|false
		 * 
		 * @return string|array $output - the output of the API call. Empty string if the tool is not supported. Array if the tool is supported.
		 * 								  Format: array( 'status' => 'success|error', 'message' => 'message text' )
		 */
		private function set_mouse_helper_state( $args ) {
			if ( ! current_user_can( 'edit_theme_options' ) ) {
				return array( 'status' => 'error', 'message' => __( 'You have no permissions to edit theme options', 'trx_addons' ) );
			}

			$check_rez = $this->check_args( $args, array( 'state' => array( true, false ) ) );
			if ( ! empty( $check_rez ) ) {
				return array( 'status' => 'error', 'message' => $check_rez );
			}
			// Get the plugins options
			$options = apply_filters( 'trx_addons_filter_load_options', get_option( 'trx_addons_options' ) );
			// Update the mouse helper state
			if ( ! empty( $args['state'] ) ) {
				$options['mouse_helper'] = 1;
			}
			$options['mouse_helper_permanent'] = ! empty( $args['state'] ) ? 1 : 0;
			// Update the plugin options
			update_option( 'trx_addons_options', apply_filters('trx_addons_filter_options_save', $options ) );
			// Return the result
			return array(
				'status' => 'success',
				'message' => sprintf(
								__( 'Mouse Helper is %s.', 'trx_addons'),
								! empty( $args['state'] ) ? __( 'enabled', 'trx_addons' ) : __( 'disabled', 'trx_addons' )
							),
				//'value' => ! empty( $args['state'] )
			);
		}

	}
}
