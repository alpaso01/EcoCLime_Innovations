<?php
/**
 * Utility class (Singleton)
 *
 * @package ThemeREX Addons
 * @since v2.30.0
 */

namespace TrxAddons\ElementorTemplates;

defined( 'ABSPATH' ) || exit;

use Elementor\Core\Base\Document;
use Elementor\Core\Kits\Manager;
use Elementor\TemplateLibrary\Source_Local;

/**
 * Utility functions.
 *
 * @package ThemeRex
 */
class Utils extends Base {

	/**
	 * Utils constructor.
	 */
	public function __construct() {
		parent::__construct();

		add_action( 'delete_post', array( $this, 'delete_kit_cache' ) );
		add_action( 'save_post', array( $this, 'delete_kit_cache' ) );
	}

	/**
	 * Clear Elementor's cache.
	 *
	 * Delete all meta containing files data. And delete the actual
	 * files from the upload directory.
	 */
	public static function clear_elementor_cache() {
		\Elementor\Plugin::instance()->files_manager->clear_cache();
	}

	/**
	 * Delete kit cache.
	 *
	 * @param int $post_id Post ID.
	 */
	public function delete_kit_cache( $post_id ) {
		if ( Source_Local::CPT !== get_post_type( $post_id ) ) {
			return;
		}

		$type = get_post_meta( $post_id, '_elementor_template_type', true );
		if ( 'kit' !== $type ) {
			return;
		}

		Transients::instance()->delete( 'get_kits' );
	}

	/**
	 * Get a list of all Elementor Kits.
	 * Returns an associative arrray with [id] => [title].
	 *
	 * @param bool $prefix Whether to prefix Global Kit with "Global :".
	 *
	 * @return array
	 */
	public static function get_kits( $prefix = true ) {
		$posts = Transients::instance()->get( 'get_kits' );

		if ( ! $posts ) {
			$posts = \get_posts(
				array(
					'post_type'      => Source_Local::CPT,
					'post_status'    => array( 'publish' ),
					'posts_per_page' => -1,
					'orderby'        => 'title',
					'order'          => 'DESC',
					'meta_query'     => array( // @codingStandardsIgnoreLine
						array(
							'key'   => Document::TYPE_META_KEY,
							'value' => 'kit',
						),
					),
				)
			);

			Transients::instance()->set( 'get_kits', $posts, WEEK_IN_SECONDS );
		}

		$kits = array();

		foreach ( $posts as $post ) {
			$global_kit = (int) get_option( Manager::OPTION_ACTIVE );

			$title = $post->post_title;

			if ( $global_kit && $post->ID === $global_kit && $prefix ) {
				/* translators: Global Style Kit post title. */
				$title = sprintf( __( 'Global: %s', 'trx_addons' ), $title );
			}

			$kits[ $post->ID ] = $title;
		}

		return $kits;
	}

	/**
	 * Log a message to CLI.
	 *
	 * @param string $message CLI message to output.
	 *
	 * @return string|void Return message if in CLI, or void.
	 */
	public static function cli_log( $message ) {
		if ( defined( 'WP_CLI' ) && WP_CLI ) {
			\WP_CLI::line( $message );
		}
	}

	/**
	 * Get the current active kit ID
	 *
	 * @return int
	 */
	public static function get_active_kit_id() {
		$active_kit = \get_option( Manager::OPTION_ACTIVE );
		return $active_kit;
	}

	/**
	 * Get specific Kit setting.
	 *
	 * @param int         $kit_id Kit ID.
	 * @param null|string $setting Optional. Post meta key to retrieve value for.
	 *
	 * @return mixed
	 */
	public static function get_kit_settings( $kit_id, $setting = null ) {
		$document = \Elementor\Plugin::instance()->documents->get( $kit_id );

		if ( ! $document ) {
			return false;
		}

		return $document->get_settings( $setting );
	}


	/**
	 * Get Kit active on document.
	 *
	 * @param int $post_id Post ID.
	 *
	 * @return mixed
	 */
	public static function get_document_kit( $post_id ) {
		if ( ! $post_id ) {
			return false;
		}

		$document = \Elementor\Plugin::instance()->documents->get_doc_for_frontend( $post_id );

		if ( ! $document ) {
			return false;
		}

		$kit_id = $document->get_settings( 'trx_addons_elementor_kit_id' );

		// Check if this is a valid kit or not.
		if ( ! \Elementor\Plugin::instance()->kits_manager->is_kit( $kit_id ) ) {
			return false;
		}

		return \Elementor\Plugin::instance()->documents->get_doc_for_frontend( $kit_id );
	}

	/**
	 * Determine the tab settings should be added to.
	 *
	 * @return string
	 */
	public static function get_kit_settings_tab() {
		$tab = 'theme-style-kits';

		return $tab;
	}

	/**
	 * Get the current kit ID.
	 *
	 * @param $id int
	 *
	 * @return bool
	 */
	public static function set_elementor_active_kit( $id ) {
		$default_kit       = Options::instance()->get( 'global_kit' );
		$elementor_kit_key = Manager::OPTION_ACTIVE;
		$elementor_kit     = \get_option( $elementor_kit_key );

		if ( $id !== $default_kit || $id !== $elementor_kit ) {
			if ( empty( $id ) || '-1' === $id ) {
				\update_option( $elementor_kit_key, Options::instance()->get( 'default_kit' ) );
			}

			\update_option( $elementor_kit_key, $id );

			return true;
		}

		return false;
	}

	/**
	 * Get the global color value by ID.
	 *
	 * @param string $id Color ID.
	 *
	 * @return string
	 */
	public static function get_global_color( $id ) {
		$global_color = '';

		if ( ! $id ) {
			return $global_color;
		}
		
		$el_page_settings 	= [];

		$kit_id = self::get_active_kit_id();

		if ( $kit_id ) {
			//$el_page_settings = get_post_meta( $kit_id, '_elementor_page_settings', true );
			$el_page_settings = self::get_kit_settings( $kit_id );

			if( ! empty( $el_page_settings ) && isset( $el_page_settings['system_colors'] ) ) {
				foreach( $el_page_settings['system_colors'] as $key => $val ) {
					if ( $val['_id'] == $id ) {
						$global_color = $val['color'];
					}
				}
			}
		}

		return $global_color;
	}

	/**
	 * Parse the CSS value with units (px, em, rem, %) abd return the array in the Elementor format (with unit and size).
	 */
	public static function parse_css_value( $value, $need_sizes = false ) {
		$css_value = array(
			'size' => '',
			'unit' => 'px',
		);
		if ( $need_sizes ) {
			$css_value['sizes'] = array();
		}

		if ( empty( $value ) ) {
			return $css_value;
		}

		$css_value['size'] = preg_replace( '/[a-z%]/', '', $value );
		$css_value['unit'] = preg_replace( '/[^a-z%]/', '', $value );

		return $css_value;
	}

	/**
	 * Returns true if Elementor Container experiment is on.
	 *
	 * @return bool
	 */
	public static function is_elementor_container() {
		return trx_addons_elm_is_experiment_active( 'container' );
	}
}

new Utils();
