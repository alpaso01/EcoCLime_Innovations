<?php
/**
 * Utility class (Singleton)
 *
 * @package ThemeREX Addons
 * @since v2.30.0
 */

namespace TrxAddons\ElementorWidgets;

use Elementor\Icons_Manager;

defined( 'ABSPATH' ) || exit;

/**
 * Utility functions.
 *
 * @package ThemeRex
 */
class Utils extends Base {

	protected static $post_types = array();
	protected static $post_tax   = array();
	protected static $tax_terms  = array();
	protected static $taxonomies = array();
	protected static $e_temps_list = null;

	/**
	 * Utils constructor.
	 */
	public function __construct() {
		parent::__construct();
	}

	/**
	 * Check if script debug is enabled.
	 *
	 * @return string  true if script debug is enabled, false otherwise.
	 */
	public static function is_script_debug() {
		return defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG;
	}

	/**
	 * Return an URL with a documentation for the specified element.
	 * 
	 * @param string $element  The element name.
	 * 
	 * @return string  The URL to the documentation.
	 */
	public static function get_doc_url( $element, $theme = '' ) {
		if ( empty( $theme ) ) {
			$theme = get_template();
		}
		return apply_filters( 'trx_addons_filter_get_doc_url', '//doc.themerex.net/' . $theme . '/#shortcodes_settings_' . $element, $element, $theme );
	}

	/**
	 * Validate an HTML tag against a safe allowed list.
	 *
	 * @param string $tag  The HTML tag to validate.
	 * 
	 * @return string  The validated HTML tag.
	 */
	public static function validate_html_tag( $tag ) {
		// Check if Elementor method exists, else we will run custom validation code.
		if ( method_exists( '\Elementor\Utils', 'validate_html_tag' ) ) {
			return \Elementor\Utils::validate_html_tag( $tag );
		} else {
			return in_array( strtolower( $tag ), array( 'article', 'aside', 'div', 'footer', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'header', 'main', 'nav', 'p', 'section', 'span' ) )
					 ? $tag
					 : 'div';
		}
	}

	/**
	 * Sanitizes text strings, but allowing some html tags usefull for styling and manipulating texts.
	 *
	 * @param string $content The content to be sanitized
	 * 
	 * @return string The sanitized string content.
	 */
	public static function esc_string( $content ) {
		$allowed_tags = [
			'strong' => array(),
			'em' => array(),
			'b' => array(),
			'i' => array(),
			'u' => array(),
			's' => array(),
			'sub' => array(),
			'sup' => array(),
			'span' => array(),
			'br' => array()
		];
		return wp_kses( $content, $allowed_tags );
	}

	/**
	 * Sanitizes SVG content
	 *
	 * @param string $svg The raw SVG content to be sanitized.
	 * 
	 * @return string The sanitized SVG content, with only allowed tags and attributes.
	 */
	public static function esc_svg( $svg ) {
		$default = wp_kses_allowed_html( 'post' );
		$args = array(
			'svg'   => array(
				'class' => true,
				'aria-hidden' => true,
				'aria-labelledby' => true,
				'role' => true,
				'xmlns' => true,
				'width' => true,
				'height' => true,
				// has to be lowercase
				'viewbox' => true,
				'preserveaspectratio' => true
			),
			'g'     => array( 'fill' => true ),
			'title' => array( 'title' => true ),
			'path'  => array(
				'd'               => true,
				'fill'            => true
			)
		);
		$allowed_tags = array_merge( $default, $args );
		return wp_kses( $svg, $allowed_tags );
	}

	/**
	 * Render icon
	 *
	 * @param object $widget  The widget object.
	 */
	public static function render_icon( $icon, $wrapper_class = '', $default = '' ) {
		if ( empty( $icon ) || ( is_array( $icon ) && empty( $icon['value'] ) ) ) {
			if ( ! empty( $default ) ) {
				$icon = $default;
			} else {
				return '';
			}
		}
		if ( ! is_array( $icon ) ) {
			$icon = array(
				'value' => $icon,
				'library' => strpos( $icon, 'trx_addons_icon-' ) === 0 || strpos( $icon, 'icon-' ) === 0
								? 'theme'
								: ( strpos( $icon, 'far fa-' ) === 0
									? 'fa-regular'
									: ( strpos( $icon, 'fab fa-' ) === 0
										? 'fa-brands'
										: 'fa-solid'
										)
								)
			);
		}
		return ( ! empty( $wrapper_class ) ? '<span class="' . esc_attr( $wrapper_class ) . '">' : '' )
					. ( $icon['library'] == 'theme'
						? sprintf( '<i class="%1$s"></i>', $icon['value'] )
						: Icons_Manager::try_get_icon_html( $icon, array( 'aria-hidden' => 'true' ) )
						)
				. ( ! empty( $wrapper_class ) ? '</span>' : '' );
	}

	/**
	 * Get Post Types.
	 * 
	 * @return array  List of post types.
	 */
	public static function get_post_types() {

		if ( ! empty( self::$post_types ) ) {
			return self::$post_types;
		}

		$post_types = get_post_types(
			array(
				'public' => true,
			),
			'objects'
		);

		$list = array();

		foreach ( $post_types as $post_type ) {
			$list[ $post_type->name ] = $post_type->label;
		}

		self::$post_types = $list;

		return $list;
	}

	/**
	 * Get All Posts.
	 * 
	 * @return array  List of posts.
	 */
	public static function get_all_posts() {

		$post_list = get_posts(
			array(
				'post_type'      => 'post',
				'orderby'        => 'date',
				'order'          => 'DESC',
				'posts_per_page' => -1,
			)
		);

		$posts = array();

		if ( ! empty( $post_list ) && ! is_wp_error( $post_list ) ) {
			foreach ( $post_list as $post ) {
				$posts[ $post->ID ] = $post->post_title;
			}
		}

		return $posts;
	}

	/**
	 * Get All Posts by Post Type.
	 * 
	 * @param string $post_type  Post type.
	 * 
	 * @return array  List of posts.
	 */
	public static function get_all_posts_by_type( $post_type ) {

		$post_list = get_posts(
			array(
				'post_type'      => $post_type,
				'orderby'        => 'date',
				'order'          => 'DESC',
				'posts_per_page' => -1,
			)
		);

		$posts = array();

		if ( ! empty( $post_list ) && ! is_wp_error( $post_list ) ) {
			foreach ( $post_list as $post ) {
				$posts[ $post->ID ] = $post->post_title;
			}
		}

		return $posts;
	}

	/**
	 * Get Post Categories.
	 * 
	 * @return array  List of post categories.
	 */
	public static function get_post_categories() {

		$list = array();

		$terms = get_terms(
			array(
				'taxonomy'   => 'category',
				'hide_empty' => true,
			)
		);

		if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
			foreach ( $terms as $term ) {
				$list[ $term->term_id ] = $term->name;
			}
		}

		return $list;
	}

	/**
	 * Get Post Taxonomies.
	 *
	 * @param string $post_type  Post type.
	 * 
	 * @return array  List of post taxonomies.
	 */
	public static function get_post_taxonomies( $post_type ) {
		$data       = array();
		$taxonomies = array();

		if ( ! empty( self::$post_tax ) ) {
			if ( isset( self::$post_tax[ $post_type ] ) ) {
				$data = self::$post_tax[ $post_type ];
			}
		}

		if ( empty( $data ) ) {
			$taxonomies = get_object_taxonomies( $post_type, 'objects' );

			foreach ( $taxonomies as $tax_slug => $tax ) {
				if ( ! $tax->public || ! $tax->show_ui ) {
					continue;
				}
				$data[ $tax_slug ] = $tax;
			}
			self::$post_tax[ $post_type ] = $data;
		}

		return apply_filters( 'trx_addons_filter_post_loop_taxonomies', $data, $taxonomies, $post_type );
	}

	/**
	 * Get Taxonomy Terms.
	 *
	 * @param string $taxonomy  Taxonomy name.
	 * 
	 * @return array  List of taxonomy terms.
	 */
	public static function get_tax_terms( $taxonomy ) {
		$terms = array();

		if ( ! empty( self::$tax_terms ) ) {
			if ( isset( self::$tax_terms[ $taxonomy ] ) ) {
				$terms = self::$tax_terms[ $taxonomy ];
			}
		}

		if ( empty( $terms ) ) {
			$terms                        = get_terms( $taxonomy );
			self::$tax_terms[ $taxonomy ] = $terms;
		}

		return $terms;
	}

	/**
	 * Get Post Tags.
	 * 
	 * @return array  List of post tags.
	 */
	public static function get_post_tags() {

		$list = array();

		$tags = get_tags();

		foreach ( $tags as $tag ) {
			$list[ $tag->term_id ] = $tag->name;
		}

		return $list;
	}

	/**
	 * Get custom excerpt.
	 * 
	 * @param int $limit  excerpt length.
	 * 
	 * @return string  The excerpt.
	 */
	public static function custom_excerpt( $limit = '' ) {

		$excerpt = explode( ' ', get_the_excerpt(), $limit );

		if ( count( $excerpt ) >= $limit ) {
			array_pop( $excerpt );
			$excerpt = implode( ' ', $excerpt ) . '...';
		} else {
			$excerpt = implode( ' ', $excerpt );
		}

		$excerpt = preg_replace( '`[[^]]*]`', '', $excerpt );

		return $excerpt;
	}

	/**
	 * Get all available taxonomies
	 * 
	 * @return array  List of taxonomies.
	 */
	public static function get_taxonomies_list() {
		if ( ! empty( self::$taxonomies ) ) {
			return self::$taxonomies;
		}

		$list = array();

		$taxonomies = get_taxonomies(
			array(
				'show_in_nav_menus' => true,
			),
			'objects'
		);

		if ( empty( $taxonomies ) ) {
			$list[''] = __( 'No taxonomies found', 'powerpack' );
			return $list;
		}

		foreach ( $taxonomies as $taxonomy ) {
			$list[ $taxonomy->name ] = $taxonomy->label;
		}

		self::$taxonomies = $list;

		return $list;
	}

	/**
	 * Get list of users.
	 *
	 * @return array $user_list  data for all users.
	 */
	public static function get_users() {

		$users     = get_users();
		$user_list = array();

		if ( empty( $users ) ) {
			return $user_list;
		}

		foreach ( $users as $user ) {
			$user_list[ $user->ID ] = $user->display_name;
		}

		return $user_list;
	}

	/**
	 * Formats a date meta value according to the specified format.
	 *
	 * @param mixed  $date    The date value to format.
	 * @param string $format  The format to use for formatting the date. Can be 'custom' or a predefined format.
	 * @param string $custom  The custom format to use if $format is set to 'custom'.
	 *
	 * @return string  The formatted date value.
	 */
	public static function format_date( $date, $format, $custom ) {
		if ( 'custom' === $format ) {
			$date_format = $custom;
		} else if ( 'default' === $format ) {
			$date_format = get_option( 'date_format' );
		} else {
			$date_format = $format;
		}
		return wp_kses_post( date_i18n( $date_format, $date ) );
	}

	/**
	 * Check if the post type is tribe events.
	 *
	 * @return bool  true if the post type is tribe events, false otherwise.
	 */
	public static function is_tribe_events_post( $post_id ) {
		return ( class_exists( 'Tribe__Events__Main' ) && 'tribe_events' === get_post_type( $post_id ) );
	}


	//==============================================================================
	// Elementor Template Functions
	//==============================================================================

	/**
	 * Get Elementor Template HTML Content
	 *
	 * @param string|int $title   Template Title||id.
	 * @param bool       $id      indicates if $title is the template title or id.
	 *
	 * @return $template_content string HTML Markup of the selected template.
	 */
	public static function get_template_content( $title, $id = false ) {

		$frontend = \Elementor\Plugin::instance()->frontend;

		$custom_temp = apply_filters( 'trx_addons_filter_template_id', false );

		if ( $custom_temp ) {
			$id = $title = $custom_temp;
		}

		if ( ! $id ) {
			$id = self::get_id_by_title( $title );

			$id = apply_filters( 'wpml_object_id', $id, 'elementor_library', true );
		} else {
			$id = $title;
		}

		$template_content = $frontend->get_builder_content_for_display( $id, true );

		return $template_content;
	}

	/**
	 * Get ID By Title
	 *
	 * Get Elementor Template ID by title
	 *
	 * @param string $title  template title.
	 *
	 * @return string $template_id  template ID.
	 */
	public static function get_id_by_title( $title ) {

		if ( empty( $title ) ) {
			return 0;
		}

		$args = array(
			'post_type'        => 'elementor_library',
			'post_status'      => 'publish',
			'posts_per_page'   => 1,
			'title'            => $title,
			//'suppress_filters' => true,
		);

		$query = new \WP_Query( $args );

		$post_id = '';

		if ( $query->have_posts() ) {
			$post_id = $query->post->ID;

			// while ( $query->have_posts() ) {
			// 	$query->the_post();
			// 	$post_id = get_the_ID();
			// }

			wp_reset_postdata();
		}

		return $post_id;
	}

	/**
	 * Get Elementor Page List
	 *
	 * Returns an array of Elementor templates
	 *
	 * @return array  Elementor Templates
	 */
	public static function get_elementor_page_list() {

		if ( null === self::$e_temps_list ) {
			self::$e_temps_list = get_posts(
				array(
					'post_type' => 'elementor_library',
					'showposts' => 999,
				)
			);
		}

		$list = array();
		$pagelist = self::$e_temps_list;

		if ( ! empty( $pagelist ) && ! is_wp_error( $pagelist ) ) {
			foreach ( $pagelist as $post ) {
				$list[ $post->post_title ] = $post->post_title;
			}
			//update_option( 'temp_count', $list );
		}

		return $list;
	}

	/**
	 * Get All Breakpoints.
	 *
	 * @param string $type  result return type.
	 *
	 * @return array $devices  enabled breakpoints.
	 */
	public static function get_all_breakpoints( $type = 'assoc' ) {

		$devices = array(
			'desktop' => __( 'Desktop', 'elementor' ),
			'tablet'  => __( 'Tablet', 'elementor' ),
			'mobile'  => __( 'Mobile', 'elementor' ),
		);

		$method_available = method_exists( \Elementor\Plugin::instance()->breakpoints, 'has_custom_breakpoints' );

		if ( ( defined( 'ELEMENTOR_VERSION' ) && version_compare( ELEMENTOR_VERSION, '3.4.0', '>' ) ) && $method_available ) {

			if ( \Elementor\Plugin::instance()->breakpoints->has_custom_breakpoints() ) {
				$devices = array_merge(
					$devices,
					array(
						'widescreen'   => __( 'Widescreen', 'elementor' ),
						'laptop'       => __( 'Laptop', 'elementor' ),
						'tablet_extra' => __( 'Tablet Extra', 'elementor' ),
						'mobile_extra' => __( 'Mobile Extra', 'elementor' ),
					)
				);
			}
		}

		if ( 'keys' === $type ) {
			$devices = array_keys( $devices );
		}

		return $devices;
	}

	/**
	 * Get Image Data
	 *
	 * Returns image data based on image id.
	 *
	 * @param int    $image_id Image ID.
	 * @param string $image_url Image URL.
	 * @param array  $image_size Image sizes array.
	 *
	 * @return array $data image data.
	 */
	public static function get_image_data( $image_id, $image_url, $image_size ) {

		if ( ! $image_id && ! $image_url ) {
			return false;
		}

		$data = array();

		$image_url = esc_url_raw( $image_url );

		if ( ! empty( $image_id ) ) { // Existing attachment.

			$attachment = get_post( $image_id );

			if ( is_object( $attachment ) ) {
				$data['id']  = $image_id;
				$data['url'] = $image_url;

				$data['image']       = wp_get_attachment_image( $attachment->ID, $image_size, true );
				$data['image_size']  = $image_size;
				$data['caption']     = $attachment->post_excerpt;
				$data['title']       = $attachment->post_title;
				$data['description'] = $attachment->post_content;

			}
		} else { // Placeholder image, most likely.

			if ( empty( $image_url ) ) {
				return;
			}

			$data['id']          = false;
			$data['url']         = $image_url;
			$data['image']       = '<img src="' . $image_url . '" alt="" title="" />';
			$data['image_size']  = $image_size;
			$data['caption']     = '';
			$data['title']       = '';
			$data['description'] = '';
		}

		return $data;
	}

}
