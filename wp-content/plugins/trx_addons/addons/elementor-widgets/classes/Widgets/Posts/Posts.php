<?php
/**
 * Posts Module
 *
 * @package ThemeREX Addons
 * @since v2.30.0
 */

namespace TrxAddons\ElementorWidgets\Widgets\Posts;

use TrxAddons\ElementorWidgets\BaseWidgetModule;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Posts module
 */
class Posts extends BaseWidgetModule {

	public static $displayed_ids = [];

	/**
	 * Constructor.
	 *
	 * Initializing the module base class.
	 */
	public function __construct() {

		parent::__construct();

		$this->assets = array(
			'css' => true,
			'js'  => true,
			'localize' => array( 'trx_addons_posts_script' => array(
				'ajax_url'    => admin_url( 'admin-ajax.php' ),
				'posts_nonce' => wp_create_nonce( 'trx-addons-posts-widget-nonce' ),
			) ),
			'lib' => array(
				'js' => array(
					'isotope' => array( 'src' => '../../../assets/isotope/isotope.pkgd.min.js' ),
					'imagesloaded' => true,
					'swiper' => true,
				)
			)
		);

		/**
		 * Pagination Break.
		 *
		 * @see https://codex.wordpress.org/Making_Custom_Queries_using_Offset_and_Pagination
		 */
		add_action( 'pre_get_posts', [ $this, 'fix_query_offset' ], 1 );
		add_filter( 'found_posts', [ $this, 'fix_query_found_posts' ], 1, 2 );

		add_action( 'wp_ajax_trx_addons_action_get_post', array( $this, 'get_post_data' ) );
		add_action( 'wp_ajax_nopriv_trx_addons_action_get_post', array( $this, 'get_post_data' ) );
	}

	/**
	 * Get the name of the module
	 *
	 * @return string  The name of the module.
	 */
	public function get_name() {
		return 'posts';
	}

	/**
	 * Get the selector for the animation type 'Item by item'
	 * 
	 * @return string  The selector of the single item.
	 */
	public function get_separate_animation_selector() {
		return '.trx-addons-posts-item-wrap';
	}

	/**
	 * Add posts list to the list of displayed posts
	 * 
	 * @param array $ids  List of post IDs.
	 */
	public static function add_to_avoid_list( $ids ) {
		self::$displayed_ids = array_unique( array_merge( self::$displayed_ids, $ids ) );
	}

	/**
	 * Get the list of displayed posts
	 * 
	 * @return array  List of post IDs.
	 */
	public static function get_avoid_list_ids() {
		return self::$displayed_ids;
	}

	/**
	 * Query Offset Fix.
	 *
	 * @param object $query query object.
	 */
	public function fix_query_offset( &$query ) {
		if ( ! empty( $query->query_vars['offset_to_fix'] ) ) {
			if ( $query->is_paged ) {
				$query->query_vars['offset'] = $query->query_vars['offset_to_fix'] + ( ( $query->query_vars['paged'] - 1 ) * $query->query_vars['posts_per_page'] );
			} else {
				$query->query_vars['offset'] = $query->query_vars['offset_to_fix'];
			}
		}
	}

	/**
	 * Query Found Posts Fix.
	 *
	 * @param int    $found_posts  found posts.
	 * @param object $query  query object.
	 * 
	 * @return int  found posts count.
	 */
	public function fix_query_found_posts( $found_posts, $query ) {
		$offset_to_fix = $query->get( 'offset_to_fix' );

		if ( $offset_to_fix ) {
			$found_posts -= $offset_to_fix;
		}

		return $found_posts;
	}
	
	public function get_post_data() {

		check_ajax_referer( 'trx-addons-posts-widget-nonce', 'nonce' );
		
		$post_id   = $_POST['page_id'];
		$widget_id = $_POST['widget_id'];
		$filter  = isset( $_POST['category'] ) ? $_POST['category'] : '';
		$filter   = str_replace( '.', '', $filter );
		$taxonomy_filter  = isset( $_POST['taxonomy'] ) ? $_POST['taxonomy'] : '';
		$taxonomy_filter   = str_replace( '.', '', $taxonomy_filter );
		$search_filter  = isset( $_POST['search'] ) ? $_POST['search'] : '';

		$elementor = \Elementor\Plugin::instance();
		$meta      = $elementor->documents->get( $post_id )->get_elements_data();

		$widget_data = $this->find_element_recursive( $meta, $widget_id );

		if ( isset( $widget_data['templateID'] ) ) {
			$template_data = $elementor->templates_manager->get_template_data( [
				'source' 		=> 'local',
				'template_id' 	=> $widget_data['templateID'],
			] );

			if ( is_array( $template_data ) && isset( $template_data['content'] ) ) {
				$widget_data = $template_data['content'][0];
			}
		}
		
		$data = array(
			'message'    => __( 'Saved', 'trx_addons' ),
			'ID'         => '',
			'skin_id'    => '',
			'html'       => '',
			'pagination' => '',
		);
		
		if ( null != $widget_data ) {
			
			// Restore default values.
			$widget = $elementor->elements_manager->create_element_instance( $widget_data );
			$skin = $widget->get_current_skin();
			$skin_body = $skin->render_ajax_post_body( $filter, $taxonomy_filter, $search_filter );
			$pagination = $skin->render_ajax_pagination();
		
			$data['ID']         = $widget->get_id();
			$data['skin_id']    = $widget->get_current_skin_id();
			$data['html']		= $skin_body;
			$data['pagination'] = $pagination;
		}
		wp_send_json_success( $data );
	}

	/**
	 * Find Element Recursive
	 *
	 * @param array  $elements  Element array.
	 * @param string $form_id   Element ID.
	 * 
	 * @return object|Boolean  Element object or false.
	 */
	public function find_element_recursive( $elements, $form_id ) {

		foreach ( $elements as $element ) {
			if ( $form_id === $element['id'] ) {
				return $element;
			}

			if ( ! empty( $element['elements'] ) ) {
				$element = $this->find_element_recursive( $element['elements'], $form_id );

				if ( $element ) {
					return $element;
				}
			}
		}

		return false;
	}

	/**
	 * Get Post Parts
	 *
	 * @return array  List of post parts.
	 */
	public static function get_post_parts() {
		$post_parts = [
			'thumbnail',
			'terms',
			'title',
			'meta',
			'excerpt',
			// 'button',
		];

		return $post_parts;
	}

	/**
	 * Get Meta Items
	 *
	 * @return array  List of meta items.
	 */
	public static function get_meta_items() {
		$meta_items = [
			'author',
			'date',
			'comments',
		];

		return $meta_items;
	}

}
