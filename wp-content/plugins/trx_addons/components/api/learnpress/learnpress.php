<?php
/**
 * Plugin support: LearnPress
 *
 * @package ThemeREX Addons
 * @since v1.6.62
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}

if ( ! defined( 'TRX_ADDONS_LP_COURSE_CPT' ) )		define( 'TRX_ADDONS_LP_COURSE_CPT', 'lp_course' );
if ( ! defined( 'TRX_ADDONS_LP_LESSON_CPT' ) )		define( 'TRX_ADDONS_LP_LESSON_CPT', 'lp_lesson' );
if ( ! defined( 'TRX_ADDONS_LP_QUESTION_CPT' ) )	define( 'TRX_ADDONS_LP_QUESTION_CPT', 'lp_question' );
if ( ! defined( 'TRX_ADDONS_LP_QUIZ_CPT' ) )		define( 'TRX_ADDONS_LP_QUIZ_CPT', 'lp_quiz' );
if ( ! defined( 'TRX_ADDONS_LP_ORDER_CPT' ) )		define( 'TRX_ADDONS_LP_ORDER_CPT', 'lp_order' );
if ( ! defined( 'TRX_ADDONS_LP_COURSE_CATEGORY' ) )	define( 'TRX_ADDONS_LP_COURSE_CATEGORY', 'course_category' );
if ( ! defined( 'TRX_ADDONS_LP_COURSE_TAG' ) )		define( 'TRX_ADDONS_LP_COURSE_TAG', 'course_tag' );

if ( ! function_exists( 'trx_addons_exists_learnpress' ) ) {
	/**
	 * Check if LearnPress plugin is installed and activated
	 *
	 * @return bool  true if plugin is installed and activated
	 */
	function trx_addons_exists_learnpress() {
		return class_exists('LearnPress');
	}
}

if ( ! function_exists( 'trx_addons_is_learnpress_page' ) ) {
	/**
	 * Check if current page is any LearnPress page
	 *
	 * @return bool  true if current page is any LearnPress page
	 */
	function trx_addons_is_learnpress_page() {
		$rez = false;
		if ( trx_addons_exists_learnpress() && ! is_search() ) {
			$rez = is_learnpress()
					|| ( function_exists( 'learn_press_is_profile' ) && learn_press_is_profile() )
					|| ( function_exists( 'learn_press_is_checkout' ) && learn_press_is_checkout() )
					|| ( function_exists( 'learn_press_is_instructors' ) && learn_press_is_instructors() )
					|| trx_addons_check_url( '/instructor/' )
					|| trx_addons_check_url( '/lp/v1/load_content_via_ajax/' );
			if ( ! $rez ) {
				$id = get_the_ID();
				if ( $id > 0 ) {
					$check_pages = apply_filters( 'trx_addons_filter_learnpress_pages', array( 'courses', 'instructors', 'single_instructor', 'profile', 'checkout', 'become_a_teacher', 'term_conditions' ) );
					foreach( $check_pages as $page ) {
						$page_id = learn_press_get_page_id( $page );
						if ( (int)$page_id > 0 && is_page() && $id == $page_id ) {
							$rez = true;
							break;
						}
					}
				}
			}
		}
		return $rez;
	}
}

if ( ! function_exists( 'trx_addons_learnpress_change_courses_slug' ) ) {
	add_filter('trx_addons_cpt_list', 'trx_addons_learnpress_change_courses_slug');
	/**
	 * Change slug for the internl courses post type to avoid conflicts with the LearnPress plugin
	 * 
	 * @hooked trx_addons_cpt_list
	 *
	 * @param array $list  List of post types parameters
	 * 
	 * @return array       Modified list of post types parameters
	 */
	function trx_addons_learnpress_change_courses_slug( $list ) {
		if ( ! empty( $list['courses']['post_type_slug'] ) && $list['courses']['post_type_slug'] == 'courses' ) {
			$list['courses']['post_type_slug'] = 'cpt_courses';
		}
		return $list;
	}
}

if ( ! function_exists( 'trx_addons_learnpress_add_fields' ) ) {
	add_filter( 'learn_press_course_settings_meta_box_args', 'trx_addons_learnpress_add_fields' );
	/**
	 * Add additional meta-fields to the course
	 * 
	 * @hooked learn_press_course_settings_meta_box_args
	 *
	 * @param array $meta_box  Meta box parameters
	 * 
	 * @return array           Modified meta box parameters
	 */
	function trx_addons_learnpress_add_fields( $meta_box ) {
		$meta_box['fields'][] = array(
			'name' => __( 'Intro video (local)', 'trx_addons' ),
			'desc' => __( 'Video-presentation of the course uploaded to your site.', 'trx_addons' ),
			'id'   => '_lp_intro_video',
			'type' => 'video',
			'std'  => ''
		);
		$meta_box['fields'][] = array(
			'name' => __( 'Intro video (external)', 'trx_addons' ),
			'desc' => __( 'or specify url of the video-presentation from popular video hosting (like Youtube, Vimeo, etc.)', 'trx_addons' ),
			'id'   => '_lp_intro_video_external',
			'type' => 'text',
			'std'  => ''
		);
		$meta_box['fields'][] = array(
			'name' => __( 'Includes', 'trx_addons' ),
			'desc' => __( 'List of includes of the course.', 'trx_addons' ),
			'id'   => '_lp_course_includes',
			'type' => 'wysiwyg',
			'std'  => ''
		);
		return $meta_box;
	}
}

if ( ! function_exists( 'trx_addons_learnpress_load_scripts_front' ) ) {
	add_action( "wp_enqueue_scripts", 'trx_addons_learnpress_load_scripts_front', TRX_ADDONS_ENQUEUE_SCRIPTS_PRIORITY );
	add_action( 'trx_addons_action_pagebuilder_preview_scripts', 'trx_addons_learnpress_load_scripts_front', 10, 1 );
	/**
	 * Enqueue scripts and styles for frontend
	 * 
	 * @hooked wp_enqueue_scripts
	 * @hooked trx_addons_action_pagebuilder_preview_scripts
	 * 
	 * @trigger trx_addons_action_load_scripts_front
	 * 
	 * @param bool $force  Force enqueue scripts and styles (without check if it's necessary)
	 */
	function trx_addons_learnpress_load_scripts_front( $force = false ) {
		if ( ! trx_addons_exists_learnpress() ) {
			return;
		}
		trx_addons_enqueue_optimized( 'learnpress', $force, array(
			'need' => trx_addons_is_learnpress_page(),
			'check' => array(
				array( 'type' => 'sc',  'sc' => 'confirm_order' ),
				array( 'type' => 'sc',  'sc' => 'profile' ),
				array( 'type' => 'sc',  'sc' => 'become_teacher_form' ),
				array( 'type' => 'sc',  'sc' => 'login_form' ),
				array( 'type' => 'sc',  'sc' => 'register_form' ),
				array( 'type' => 'sc',  'sc' => 'checkout' ),
				array( 'type' => 'sc',  'sc' => 'recent_courses' ),
				array( 'type' => 'sc',  'sc' => 'featured_courses' ),
				array( 'type' => 'sc',  'sc' => 'popular_courses' ),
				array( 'type' => 'sc',  'sc' => 'button_enroll' ),
				array( 'type' => 'sc',  'sc' => 'button_purchase' ),
				array( 'type' => 'sc',  'sc' => 'button_course' ),
				array( 'type' => 'sc',  'sc' => 'course_curriculum' ),
				array( 'type' => 'sc',  'sc' => 'learn_press_archive_course' ),
				array( 'type' => 'gb',  'sc' => 'wp:learnpress/archive-course' ),
				array( 'type' => 'gb',  'sc' => 'wp:learnpress/item-curriculum-course' ),
				array( 'type' => 'gb',  'sc' => 'wp:learnpress/single-course' ),
				array( 'type' => 'elm', 'sc' => '"widgetType":"wp-widget-learnpress_widget_' ),
				array( 'type' => 'elm', 'sc' => '"widgetType":"become_a_teacher' ),
				array( 'type' => 'elm', 'sc' => '"widgetType":"list_courses' ),
				array( 'type' => 'elm', 'sc' => '"widgetType":"login_form' ),
				array( 'type' => 'elm', 'sc' => '"widgetType":"register_form' ),
				array( 'type' => 'elm', 'sc' => '"widgetType":"lp_course_material' ),
				array( 'type' => 'elm', 'sc' => '"widgetType":"list_courses_by_page' ),
				array( 'type' => 'elm', 'sc' => '"widgetType":"course_price' ),
				array( 'type' => 'elm', 'sc' => '"widgetType":"list_instructors' ),
				array( 'type' => 'elm', 'sc' => '"widgetType":"single_instructor' ),
				array( 'type' => 'elm', 'sc' => '"widgetType":"instructor_avatar' ),
				array( 'type' => 'elm', 'sc' => '"widgetType":"instructor_button_view' ),
				array( 'type' => 'elm', 'sc' => '"widgetType":"instructor_count_courses' ),
				array( 'type' => 'elm', 'sc' => '"widgetType":"instructor_count_students' ),
				array( 'type' => 'elm', 'sc' => '"widgetType":"instructor_description' ),
				array( 'type' => 'elm', 'sc' => '"widgetType":"instructor_name' ),
				array( 'type' => 'elm', 'sc' => '"shortcode":"[confirm_order' ),
				array( 'type' => 'elm', 'sc' => '"shortcode":"[profile' ),
				array( 'type' => 'elm', 'sc' => '"shortcode":"[become_teacher_form' ),
				array( 'type' => 'elm', 'sc' => '"shortcode":"[login_form' ),
				array( 'type' => 'elm', 'sc' => '"shortcode":"[register_form' ),
				array( 'type' => 'elm', 'sc' => '"shortcode":"[checkout' ),
				array( 'type' => 'elm', 'sc' => '"shortcode":"[recent_courses' ),
				array( 'type' => 'elm', 'sc' => '"shortcode":"[featured_courses' ),
				array( 'type' => 'elm', 'sc' => '"shortcode":"[popular_courses' ),
				array( 'type' => 'elm', 'sc' => '"shortcode":"[button_enroll' ),
				array( 'type' => 'elm', 'sc' => '"shortcode":"[button_purchase' ),
				array( 'type' => 'elm', 'sc' => '"shortcode":"[button_course' ),
				array( 'type' => 'elm', 'sc' => '"shortcode":"[course_curriculum' ),
				array( 'type' => 'elm', 'sc' => '"shortcode":"[learn_press_archive_course' ),
			)
		) );
	}
}

if ( ! function_exists( 'trx_addons_learnpress_check_in_html_output' ) ) {
	add_filter( 'trx_addons_filter_get_menu_cache_html', 'trx_addons_learnpress_check_in_html_output', 10, 1 );
	add_action( 'trx_addons_action_show_layout_from_cache', 'trx_addons_learnpress_check_in_html_output', 10, 1 );
	add_action( 'trx_addons_action_check_page_content', 'trx_addons_learnpress_check_in_html_output', 10, 1 );
	/**
	 * Check if LearnPress shortcodes are present in the HTML output of the page or in the menu or the layouts cache
	 * and force loading scripts and styles
	 * 
	 * @hooked trx_addons_filter_get_menu_cache_html
	 * @hooked trx_addons_action_show_layout_from_cache
	 * @hooked trx_addons_action_check_page_content
	 *
	 * @param string $content  HTML output to check
	 * 
	 * @return string          Checked HTML output
	 */
	function trx_addons_learnpress_check_in_html_output( $content = '' ) {
		if ( ! trx_addons_exists_learnpress() ) {
			return $content;
		}
		$args = array(
			'check' => array(
				'class=[\'"][^\'"]*learnpress',
				'<(div|section|form|table|ul)[^>]*id=[\'"][^\'"]*learnpress',
				'class=[\'"][^\'"]*type\\-(lp_course|lp_lesson|lp_question|lp_quiz|lp_order)',
				'class=[\'"][^\'"]*(course_category|course_tag|question_tag)\\-',
			)
		);
		if ( trx_addons_check_in_html_output( 'learnpress', $content, $args ) ) {
			trx_addons_learnpress_load_scripts_front( true );
		}
		return $content;
	}
}

if ( ! function_exists( 'trx_addons_learnpress_pre_get_avatar' ) ) {
	add_filter( 'pre_get_avatar', 'trx_addons_learnpress_pre_get_avatar' );
	/**
	 * Add the class 'learnpress_avatar' to the avatar layout
	 * if the avatar is returned by the LearnPress plugin
	 * 
	 * @hooked pre_get_avatar
	 * 
	 * @param string $avatar  HTML code of the avatar
	 * 
	 * @return string         Modified HTML code of the avatar
	 */
	function trx_addons_learnpress_pre_get_avatar( $avatar ) {
		if ( ! empty( $avatar ) && has_filter( 'pre_get_avatar', 'learn_press_pre_get_avatar_callback' ) ) {
			$avatar = strpos( $avatar, 'class="' ) !== false
						? str_replace( 'class="', 'class="learnpress_avatar ', $avatar )
						: str_replace( '<img ', '<img class="learnpress_avatar avatar" ', $avatar );
		}
		return $avatar;
	}
}

if ( ! function_exists( 'trx_addons_learnpress_extended_taxonomy_allow_in_the_terms' ) ) {
	add_filter( 'trx_addons_filter_extended_taxonomy_filter_get_the_terms', 'trx_addons_learnpress_extended_taxonomy_allow_in_the_terms' );
	/**
	 * Allow the extended taxonomy in the get_the_terms() function
	 * 
	 * @hooked trx_addons_filter_extended_taxonomy_filter_get_the_terms
	 * 
	 * @param bool $allow  true - allow, false - disallow
	 * 
	 * @return bool        true - allow, false - disallow
	 */
	function trx_addons_learnpress_extended_taxonomy_allow_in_the_terms( $allow = false) {
		if ( trx_addons_is_learnpress_page() ) {
			$allow = true;
		}
		return $allow;
	}
}

if ( ! function_exists( 'trx_addons_learnpress_extended_taxonomy_add_inline_css' ) ) {
	add_filter( 'rest_pre_serve_request', 'trx_addons_learnpress_extended_taxonomy_add_inline_css', 10, 4 );
	/**
	 * Add inline styles for the extended taxonomy
	 * 
	* @param boolean          $false
	* @param WP_REST_Response $result
	* @param WP_REST_Request  $request
	* @param WP_REST_Server   $server
	*/
	function trx_addons_learnpress_extended_taxonomy_add_inline_css( $false, $result, $request, $server ) {
		if ( trx_addons_check_url( '/lp/v1/load_content_via_ajax/' ) && is_object( $result ) && method_exists( $result, 'get_data' ) ) {
			$data = $result->get_data();
			if ( ! empty( $data->data->content ) && strpos( $data->data->content, 'trx_addons_extended_taxonomy' ) !== false ) {
				$css = trx_addons_get_inline_css( true );
				if ( ! empty( $css ) ) {
					$css = '<style type="text/css">' . $css . '</style>';
					$data->data->content = $css . $data->data->content;
					$result->set_data( $data );
				}
			}
		}
		return $false;
	}
}

if ( ! function_exists( 'trx_addons_learnpress_sc_layouts_content_add_inline_css' ) ) {
	add_filter( 'trx_addons_filter_sc_layout_content_need_inline_css', 'trx_addons_learnpress_sc_layouts_content_add_inline_css', 10, 2 );
	/**
	 * Add inline styles for the extended taxonomy
	 * 
	* @param boolean $add      true - add inline styles, false - not
	* @param int     $post_id  Post ID
	*/
	function trx_addons_learnpress_sc_layouts_content_add_inline_css( $add, $post_id = 0 ) {
		if ( trx_addons_exists_learnpress() && trx_addons_is_learnpress_page() ) {		// && learn_press_is_course()
			$add = true;
		}
		return $add;
	}
}


// Demo data install
//----------------------------------------------------------------------------

// One-click import support
if ( is_admin() ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_API . 'learnpress/learnpress-demo-importer.php';
}
