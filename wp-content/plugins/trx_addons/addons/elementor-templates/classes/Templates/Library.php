<?php
namespace TrxAddons\ElementorTemplates\Templates;

defined( 'ABSPATH' ) || exit;

use TrxAddons\ElementorTemplates\Utils;
// use TrxAddons\ElementorTemplates\Options;


/**
 * Class Library - show a Templates Library and import any template to the current page
 */
class Library {

	private $templates_library_cache_name = 'trx_addons_elementor_list_templates';
	private $templates_library_cache_time = 2 * 24 * 60 * 60;
	private $templates_library_option_favorites = 'trx_addons_elementor_favorite_templates';

	/**
	 * Constructor.
	 */
	public function __construct() {
		// Update list of templates
		add_action( 'admin_init', array( $this, 'update_list_templates' ) );

		// Refresh templates list
		add_action( 'wp_ajax_trx_addons_elementor_templates_library_refresh', array( $this, 'refresh_list_templates' ) );

		// Import template
		add_action( 'wp_ajax_trx_addons_elementor_templates_library_item_import', array( $this, 'import_template' ) );

		// Mark/unmark template as favorite
		add_action( 'wp_ajax_trx_addons_elementor_templates_library_item_favorite', array( $this, 'favorite_template' ) );

		// Enqueue scripts and styles
		add_action( 'elementor/editor/before_enqueue_scripts', array( $this, 'enqueue_editor_scripts' ) );
		add_action( 'elementor/preview/enqueue_styles', array( $this, 'enqueue_preview_scripts' ) );

		// Add messages to js-vars
		add_filter( 'trx_addons_filter_localize_script_admin', array( $this, 'localize_script_admin' ) );
	}

	/**
	 * Get list of templates
	 * 
	 * @return array  List of templates or false
	 */
	public function get_list_templates() {
		return get_transient( $this->templates_library_cache_name );
	}

	/**
	 * Save list of templates to the cache
	 * 
	 * @param array $templates  List of templates
	 */
	public function set_list_templates( $templates ) {
		set_transient( $this->templates_library_cache_name, $templates, $this->templates_library_cache_time );
	}

	/**
	 * Update list of templates
	 * 
	 * @param bool $force  If true - force update
	 * 
	 * @hooked admin_init
	 */
	public function update_list_templates( $force = false ) {
		$templates = $force ? false : $this->get_list_templates();
		if ( ! is_array( $templates ) ) {
			$templates_available = trx_addons_get_upgrade_data( array( 'action' => 'info_elementor_templates' ) );
			if ( empty( $templates_available['error'] ) && ! empty( $templates_available['data'] ) && $templates_available['data'][0] == '{' ) {
				$templates = json_decode( $templates_available['data'], true );
			}
			$this->set_list_templates( is_array( $templates ) ? $templates : array() );
		}
	}

	/**
	 * Get list of favorite templates
	 * 
	 * @return array  List of favorite templates
	 */
	public function get_list_favorites() {
		return get_option( $this->templates_library_option_favorites, array() );
	}

	/**
	 * Save list of favorite templates
	 * 
	 * @param array $templates  List of templates
	 */
	public function set_list_favorites( $templates ) {
		update_option( $this->templates_library_option_favorites, $templates );
	}

	/**
	 * Get template data by type and name
	 * 
	 * @param string $name  Template name
	 * @param string $type  Template type (not used now)
	 * 
	 * @return array  Template data
	 */
	public function get_template_data( $name, $type = '' ) {
		$templates = $this->get_list_templates();
		return ! empty( $templates[ $name ] ) ? $templates[ $name ] : false;
	}

	/**
	 * Get templates tabs and categories
	 * 
	 * @return array  Templates tabs and categories
	 */
	public function get_tabs_and_categories() {
		$tabs = array();
		$templates = $this->get_list_templates();
		if ( is_array( $templates ) ) {
			foreach ( $templates as $name => $data ) {
				if ( ! empty( $data['type'] ) ) {
					if ( empty( $tabs[ $data['type'] ] ) ) {
						$tabs[ $data['type'] ] = array(
							'title' => $this->get_tab_title( $data['type'] ),
							'category' => array()
						);
					}
					$cats = array_map( 'trim', explode( ',', ! empty( $data['category'] ) ? $data['category'] : '' ) );
					foreach ( $cats as $cat ) {
						if ( empty( $cat ) ) {
							continue;
						}
						if ( ! isset( $tabs[ $data['type'] ]['category'][ $cat ] ) ) {
							$tabs[ $data['type'] ]['category'][ $cat ] = array(
								'title' => ucfirst( str_replace( array( '-', '_' ), ' ', $cat ) ),
								'total' => 0
							);
						}
						$tabs[ $data['type'] ]['category'][ $cat ]['total']++;
					}
					ksort( $tabs[ $data['type'] ]['category'] );
				}
			}
		}
		return $tabs;
	}

	
	/**
	 * Get the tab's title by type
	 * 
	 * @return string  Tab's title
	 */
	private function get_tab_title( $type ) {
		return $type == 'page' ? esc_html__( 'Pages', 'trx_addons' ) : esc_html__( 'Blocks', 'trx_addons' );
	}


	/**
	 * Refresh list of templates
	 * 
	 * @hooked wp_ajax_trx_addons_elementor_templates_library_refresh
	 */
	public function refresh_list_templates() {

		trx_addons_verify_nonce();

		$this->update_list_templates( true );

		$response = array(
			'error' => '',
			'data' => array(
				'templates' => $this->get_list_templates(),
				'tabs' => $this->get_tabs_and_categories(),
			)
		);

		trx_addons_ajax_response( $response );
	}

	/**
	 * Mark/unmark template as favorite
	 * 
	 * @hooked wp_ajax_trx_addons_elementor_templates_library_item_favorite
	 */
	public function favorite_template() {

		trx_addons_verify_nonce();

		$response = array(
			'error' => '',
			'data' => array()
		);

		$template_name = trx_addons_get_value_gp( 'template_name' );
		$favorite = (int)trx_addons_get_value_gp( 'favorite' );

		$templates = $this->get_list_favorites();
		if ( $favorite ) {
			$templates[ $template_name ] = true;
		} else {
			unset( $templates[ $template_name ] );
		}
		$this->set_list_favorites( $templates );

		trx_addons_ajax_response( $response );
	}


	/**
	 * Import template
	 * 
	 * @hooked wp_ajax_trx_addons_elementor_templates_library_item_import
	 */
	public function import_template() {

		trx_addons_verify_nonce();

		$response = array(
			'error' => '',
			'data' => array()
		);

		$template_name = trx_addons_get_value_gp( 'template_name' );
		$template_type = trx_addons_get_value_gp( 'template_type' );

		$templates = $this->get_list_templates();
		$template_data = ! empty( $templates[ $template_name ] ) ? $templates[ $template_name ] : false;

		if ( ! is_array( $template_data ) ) {
			$response['error'] = esc_html__( 'The contents of the selected template are inaccessible!', 'trx_addons' );
		} else if ( ! empty( $template_data['content'] ) ) {
			$response['data'] = $template_data['content'];
		} else {
			$key = trx_addons_get_theme_activation_code();
			if ( empty( $key ) ) {
				$response['error'] = esc_html__( 'Theme is not activated!', 'trx_addons' );
			} else {
				$template_content = trx_addons_get_upgrade_data( array(
					'action' => 'download_elementor_template',
					'key' => $key,
					'template' => $template_name,
					'type' => $template_type
				) );
				if ( ! empty( $template_content['error'] ) ) {
					$response['error'] = $template_content['error'];
				} else if ( empty( $template_content['data'] ) || $template_content['data'][0] != '{' ) {
					$response['error'] = esc_html__( 'The contents of the selected template are unavailable!', 'trx_addons' );
				} else {
					$response['data'] = json_decode( $template_content['data'], true );
					if ( ! is_array( $response['data']['content'] ) ) {
						$response['error'] = esc_html__( 'The contents of the selected template are corrupted!', 'trx_addons' );
					} else {
						// Download images from the template content and save them to the uploads folder. Replace URLs in the content
						$response['data']['content'] = $this->download_images( $response['data']['content'], $template_name );
						// Save template content to the cache
						$templates[ $template_name ]['content'] = $response['data'];
						$this->set_list_templates( $templates );
					}
				}
			}
		}

		trx_addons_ajax_response( $response );
	}

	/**
	 * Download images from the content of Elementor's template and save them to the uploads folder.
	 * Replace URLs in the content and return modified content.
	 * 
	 * @param array $content  Template content
	 * 
	 * @return array  Modified template content
	 */
	public function download_images( $content, $template_name = '', $param_name = '' ) {
		static $loaded = array();
		if ( ! function_exists( 'media_handle_sideload' ) ) {
			require_once ABSPATH . 'wp-admin/includes/media.php';
			require_once ABSPATH . 'wp-admin/includes/file.php';
			require_once ABSPATH . 'wp-admin/includes/image.php';
		}
		$templates_url = untrailingslashit( trx_addons_get_upgrade_domain_url() ) . '/templates/elementor/' . $template_name . '/images/';
		$no_image_attachment = get_option( 'trx_addons_no_image_attachment', array() );
		if ( is_array( $content) || is_object( $content ) ) {
			foreach ( $content as $k => $v ) {
				if ( is_array( $v ) || is_object( $v ) ) {
					$content[ $k ] = $this->download_images( $v, $template_name, $k );
				} else if ( is_string( $v ) && stripos( $v, 'http' ) === 0 ) {					// Check if the string is a URL
					if ( preg_match( '/http[s]?:.*\.(jpg|jpeg|png|gif|svg|webp)/i', $v ) ) {	// Check if the URL is an image
						$image_name = basename( $v );
						$file_array = array(
							'name' => $image_name,
						);
						if ( ! isset( $loaded[ $v ] ) ) {
							$loaded[ $v ] = array(
								'url' => '',
								'id' => 0,
							);
							// Get an image content
							$is_no_image = false;
							$no_image_key = strtolower( pathinfo( $image_name, PATHINFO_EXTENSION ) ) == 'svg' ? 'svg' : 'img';
							$image_url = $templates_url . $image_name;
							$image_content = trx_addons_fgc( $image_url );
							if ( empty( $image_content ) && apply_filters( 'trx_addons_filter_elementor_templates_download_images_from_site', false ) ) {
								$image_content = trx_addons_fgc( $v );
							}
							if ( empty( $image_content ) ) {
								$image_name = $file_array['name'] = 'no-image.' . ( $no_image_key == 'svg' ? 'svg' : 'jpg' );
								if ( ! empty( $no_image_attachment[ $no_image_key ]['id'] ) && trx_addons_get_attachment_url( $no_image_attachment[ $no_image_key ]['id'] ) != '' ) {
									$loaded[ $v ] = array(
										'url' => $no_image_attachment[ $no_image_key ]['url'],
										'id' => $no_image_attachment[ $no_image_key ]['id'],
									);
								} else {
									$image_content = trx_addons_fgc( trx_addons_get_file_dir( 'css/images/no-image.' . ( $no_image_key == 'svg' ? 'svg' : 'jpg' ) ) );
									$is_no_image = true;
								}
							}
							if ( ! empty( $image_content ) ) {
								// Save a content to the file to temp location
								$temp_file_name = wp_tempnam( $image_name );
								if ( $temp_file_name && trx_addons_fpc( $temp_file_name, $image_content ) ) {
									$file_array['tmp_name'] = $temp_file_name;
								}
								if ( ! empty( $file_array['tmp_name'] ) ) {
									$attachment_post_data = array(
										'post_title' => $image_name,
										'post_content' => '',
										'post_excerpt' => '',
										'post_status' => 'inherit',
									);
									// Allow SVG uploading
									$old_setting = trx_addons_get_setting( 'allow_upload_svg', false );
									trx_addons_set_setting( 'allow_upload_svg', true );
									// Save an image to the media library
									$attachment_id = media_handle_sideload( $file_array, 0, null, $attachment_post_data );
									// Restore the old setting
									trx_addons_set_setting( 'allow_upload_svg', $old_setting );
									// Save the result to the cache
									if ( ! is_wp_error( $attachment_id ) ) {
										$loaded[ $v ] = array(
											'url' => trx_addons_get_attachment_url( $attachment_id ),
											'id' => $attachment_id,
										);
										// Save the no-image data
										if ( $is_no_image ) {
											$no_image_attachment[ $no_image_key ] = array(
												'url' => $loaded[ $v ]['url'],
												'id' => $loaded[ $v ]['id'],
											);
											update_option( 'trx_addons_no_image_attachment', $no_image_attachment );
										}
									}
								}
							}
						}
						// Replace the URL in the content
						if ( ! empty( $loaded[ $v ]['id'] ) && ! empty( $loaded[ $v ]['url'] ) ) {
							$content[ $k ] = $loaded[ $v ]['url'];
							if ( isset( $content['id'] ) ) {
								$content['id'] = $loaded[ $v ]['id'];
							}
						}
					} else if ( isset( $content['is_external'] ) ) {	// Replace all links in the content with '#' to prevent loading of external resources
						$content[ $k ] = '#';
						// $content['is_external'] = '';
					}
				}
			}
		}
		return $content;
	}

	/**
	 * Load styles and scripts for the templates library editor area
	 *
	 * @return void
	 */
	public function enqueue_editor_scripts() {
		wp_enqueue_script( 'trx_addons_elementor_extension_templates_library', trx_addons_get_file_url( TRX_ADDONS_PLUGIN_ADDONS . 'elementor-templates/js/templates-library.js' ), array( 'jquery' ), null, false );
		wp_enqueue_style( 'trx_addons_elementor_extension_templates_library', trx_addons_get_file_url( TRX_ADDONS_PLUGIN_ADDONS . 'elementor-templates/css/templates-library.css'), array( 'dashicons' ), null );
	}

	/**
	 * Load styles and scripts for the templates library preview area
	 *
	 * @return void
	 */
	public function enqueue_preview_scripts() {
		wp_enqueue_style( 'trx_addons_elementor_extension_templates_library', trx_addons_get_file_url( TRX_ADDONS_PLUGIN_ADDONS . 'elementor-templates/css/templates-library-preview.css'), array( 'dashicons' ), null );
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
		$vars['elementor_templates_library'] = $this->get_list_templates();
		if ( ! is_array( $vars['elementor_templates_library'] ) ) $vars['elementor_templates_library'] = array();
		$vars['elementor_templates_library_favorites'] = $this->get_list_favorites();
		$vars['elementor_templates_library_tabs'] = $this->get_tabs_and_categories();
		$vars['elementor_templates_library_url'] = '//upgrade.themerex.net/templates/elementor';
		$vars['elementor_templates_library_pagination_items'] = array( 'block' => 50, 'page' => 20 );
		$vars['elementor_templates_library_navigation_style'] = apply_filters( 'trx_addons_filter_templates_library_navigation_style', 'toolbar' );	// 'toolbar' or 'sidebar'
		$vars['msg_elementor_templates_library_title'] = esc_html__( "ThemeREX Templates", 'trx_addons' );
		$vars['msg_elementor_templates_library_close'] = esc_html__( "Close Library", 'trx_addons' );
		$vars['msg_elementor_templates_library_preview_close'] = esc_html__( "Close preview", 'trx_addons' );
		$vars['msg_elementor_templates_library_refresh'] = esc_html__( "Refresh Library", 'trx_addons' );
		$vars['msg_elementor_templates_library_refresh_title'] = esc_html__( "Check for updates on the templates library server", 'trx_addons' );
		$vars['msg_elementor_templates_library_search'] = esc_html__( "SEARCH", 'trx_addons' );
		$vars['msg_elementor_templates_library_category_all'] = esc_html__( "All Templates", 'trx_addons' );
		$vars['msg_elementor_templates_library_category_favorites'] = esc_html__( "Favorites", 'trx_addons' );
		$vars['msg_elementor_templates_library_empty'] = esc_html__( "No templates available!", 'trx_addons' );
		$vars['msg_elementor_templates_library_type_page'] = esc_html__( "Page", 'trx_addons' );
		$vars['msg_elementor_templates_library_type_block'] = esc_html__( "Block", 'trx_addons' );
		$vars['msg_elementor_templates_library_add_template'] = esc_html__( "Add Template from Library", 'trx_addons' );
		$vars['msg_elementor_templates_library_import_template'] = esc_html__( "Insert", 'trx_addons' );
		$vars['msg_elementor_templates_library_import_confirm'] = esc_html__( "Insert a template into the page?", 'trx_addons' );
		return $vars;
	}

}
