<?php
/**
 * Plugin support: Elementor Widgets
 *
 * @package ThemeREX Addons
 * @since v2.30.0
 */

namespace TrxAddons\ElementorWidgets;

use TrxAddons\ElementorWidgets\Utils as TrxAddonsUtils;

/**
 * Intializes Elementor Widgets addon
 */
class ElementorWidgets extends Base {

	private $allow_override_widgets_in_theme = false;

	/**
	 * Constructor.
	 */
	public function __construct() {
		parent::__construct();
		// Enqueue scripts and styles for the Elementor Frontend
		add_action( 'wp_enqueue_scripts', array( $this, 'load_scripts_front' ), TRX_ADDONS_ENQUEUE_SCRIPTS_PRIORITY );
		add_action( 'trx_addons_action_pagebuilder_preview_scripts', array( $this, 'load_scripts_front' ), 10, 1 );
		// Merge styles to the single stylesheet
		add_filter( 'trx_addons_filter_merge_styles', array( $this, 'merge_styles' ) );
		// Merge scripts to the single file
		add_filter( 'trx_addons_filter_merge_scripts', array( $this, 'merge_scripts' ) );

		// Enqueue scripts and styles for the Elementor Editor
		add_action( 'elementor/editor/after_enqueue_scripts', array( $this, 'editor_scripts' ) );
		add_action( 'elementor/editor/after_enqueue_styles', array( $this, 'editor_styles' ) );
		// Create the list of the widgets and load them
		$this->widgets_list();
		$this->widgets_load();
		// Create the list of the controls and load them
		$this->controls_list();
		$this->controls_load();
	}

	/**
	 * Enqueue frontend scripts and styles
	 */
	public function load_scripts_front() {
		wp_enqueue_style( 'trx_addons-elementor-widgets', trx_addons_get_file_url( TRX_ADDONS_PLUGIN_ADDONS . 'elementor-widgets/assets/frontend.css' ), array(), null );
		wp_enqueue_script( 'trx_addons-elementor-widgets', trx_addons_get_file_url( TRX_ADDONS_PLUGIN_ADDONS . 'elementor-widgets/assets/frontend.js' ), array( 'jquery' ), null, true );
	}

	/**
	 * Merge styles to the single stylesheet
	 * 
	 * @hooked trx_addons_filter_merge_styles
	 * 
	 * @param array $list  List of the styles
	 * 
	 * @return array  Modified list of the styles
	 */
	public function merge_styles( $list ) {
		$list[ TRX_ADDONS_PLUGIN_ADDONS . 'elementor-widgets/assets/frontend.css' ] = true;
		return $list;
	}
	
	/**
	 * Merge scripts to the single file
	 * 
	 * @hooked trx_addons_filter_merge_scripts
	 * 
	 * @param array $list  List of the scripts
	 * 
	 * @return array  Modified list of the scripts
	 */
	public function merge_scripts( $list ) {
		$list[ TRX_ADDONS_PLUGIN_ADDONS . 'elementor-widgets/assets/frontend.js' ] = true;
		return $list;
	}

	/**
	 * Enqueue editor styles
	 */
	public function editor_styles() {
		wp_enqueue_style( 'trx_addons-elementor-widgets-editor', trx_addons_get_file_url( TRX_ADDONS_PLUGIN_ADDONS . 'elementor-widgets/assets/editor.css' ), array(), null );
	}

	/**
	 * Enqueue editor scripts
	 */
	public function editor_scripts() {
		wp_enqueue_script( 'trx_addons-elementor-widgets-editor', trx_addons_get_file_url( TRX_ADDONS_PLUGIN_ADDONS . 'elementor-widgets/assets/editor.js' ), array( 'jquery' ), null, true );
	}


	/**********************************************************
	 * Widgets
	 **********************************************************/

	/**
	 * Add widgets list to the global storage
	 */
	public function widgets_list() {
		static $loaded = false;
		if ( $loaded ) return;
		$loaded = true;
		global $TRX_ADDONS_STORAGE;
		$TRX_ADDONS_STORAGE['elementor_widgets_list'] = apply_filters('trx_addons_elementor_widgets_list', array_merge( array(
			'Accordion' => array(
				'title' => __('Accordion', 'trx_addons'),
				'name' => 'trx_elm_accordion',
				'icon' => 'eicon-accordion trx_addons_elementor_widget_icon',
				'keywords' => array( 'accordion', 'toggle' ),
				'documentation' => array(
					__( 'Accordion Help', 'trx_addons' ) => TrxAddonsUtils::get_doc_url( 'accordion' )
				)
			),
			'Tabs' => array(
				'title' => __('Advanced Tabs', 'trx_addons'),
				'name' => 'trx_elm_tabs',
				'icon' => 'eicon-tabs trx_addons_elementor_widget_icon',
				'keywords' => array( 'tabs', 'advanced' ),
				'documentation' => array(
					__( 'Advanced Tabs Help', 'trx_addons' ) => TrxAddonsUtils::get_doc_url( 'tabs' )
				)
			),
			'Testimonials' => array(
				'title' => __('Advanced Testimonials', 'trx_addons'),
				'name' => 'trx_elm_testimonials',
				'icon' => 'eicon-testimonial trx_addons_elementor_widget_icon',
				'keywords' => array( 'testimonials', 'reviews', 'rating', 'stars', 'masonry', 'carousel', 'slider' ),
				'documentation' => array(
					__( 'Advanced Testimonials Help', 'trx_addons' ) => TrxAddonsUtils::get_doc_url( 'testimonials' )
				)
			),
			'AdvancedTitle' => array(
				'title' => __('Advanced Title', 'trx_addons'),
				'name' => 'trx_elm_advanced_title',
				'icon' => 'eicon-animated-headline trx_addons_elementor_widget_icon',
				'keywords' => array( 'advanced', 'animated', 'title', 'text' ),
				'documentation' => array(
					__( 'Advanced Title', 'trx_addons' ) => TrxAddonsUtils::get_doc_url( 'advanced_title' )
				)
			),
			'Counter' => array(
				'title' => __('Counter', 'trx_addons'),
				'name' => 'trx_elm_counter',
				'icon' => 'eicon-counter trx_addons_elementor_widget_icon',
				'keywords' => array( 'counter', 'digits' ),
				'documentation' => array(
					__( 'Counter Help', 'trx_addons' ) => TrxAddonsUtils::get_doc_url( 'counter' )
				)
			),
			'FlipBox' => array(
				'title' => __('Flip Box', 'trx_addons'),
				'name' => 'trx_elm_flip_box',
				'icon' => 'eicon-flip-box trx_addons_elementor_widget_icon',
				'keywords' => array( 'flip', 'info', 'box' ),
				'documentation' => array(
					__( 'Flip Box Help', 'trx_addons' ) => TrxAddonsUtils::get_doc_url( 'flip_box' )
				)
			),
			'IconList' => array(
				'title' => __('Icon List', 'trx_addons'),
				'name' => 'trx_elm_icon_list',
				'icon' => 'eicon-bullet-list trx_addons_elementor_widget_icon',
				'keywords' => array( 'icon', 'list' ),
				'documentation' => array(
					__( 'Icon List Help', 'trx_addons' ) => TrxAddonsUtils::get_doc_url( 'icon_list' )
				)
			),
			'ImageAccordion' => array(
				'title' => __('Image Accordion', 'trx_addons'),
				'name' => 'trx_elm_image_accordion',
				'icon' => 'eicon-image-before-after trx_addons_elementor_widget_icon',
				'keywords' => array( 'image', 'accordion', 'info' ),
				'documentation' => array(
					__( 'Image Accordion Help', 'trx_addons' ) => TrxAddonsUtils::get_doc_url( 'image_accordion' )
				)
			),
			'InfoBox' => array(
				'title' => __('Info Box', 'trx_addons'),
				'name' => 'trx_elm_info_box',
				'icon' => 'eicon-info-box trx_addons_elementor_widget_icon',
				'keywords' => array( 'icon', 'info', 'box' ),
				'documentation' => array(
					__( 'Info Box Help', 'trx_addons' ) => TrxAddonsUtils::get_doc_url( 'info_box' )
				)
			),
			'InfoList' => array(
				'title' => __('Info List', 'trx_addons'),
				'name' => 'trx_elm_info_list',
				'icon' => 'eicon-time-line trx_addons_elementor_widget_icon',
				'keywords' => array( 'icon', 'info', 'list', 'history', 'timeline' ),
				'documentation' => array(
					__( 'Info List Help', 'trx_addons' ) => TrxAddonsUtils::get_doc_url( 'info_list' )
				)
			),
			'NavMenu' => array(
				'title' => __('Nav Menu', 'trx_addons'),
				'name' => 'trx_elm_nav_menu',
				'icon' => 'eicon-nav-menu trx_addons_elementor_widget_icon',
				'keywords' => array( 'nav', 'menu' ),
				'documentation' => array(
					__( 'Nav Menu Help', 'trx_addons' ) => TrxAddonsUtils::get_doc_url( 'nav_menu' )
				)
			),
			'PostBreadcrumbs' => array(
				'title' => __('Page Breadcrumbs', 'trx_addons'),
				'name' => 'trx_elm_post_breadcrumbs',
				'icon' => 'eicon-form-vertical trx_addons_elementor_widget_icon',
				'keywords' => array( 'post', 'title', 'heading', 'breadcrumbs' ),
				'documentation' => array(
					__( 'Page Breadcrumbs Help', 'trx_addons' ) => TrxAddonsUtils::get_doc_url( 'post_breadcrumbs' )
				)
			),
			'PostTitle' => array(
				'title' => __('Page Title', 'trx_addons'),
				'name' => 'trx_elm_post_title',
				'icon' => 'eicon-t-letter trx_addons_elementor_widget_icon',
				'keywords' => array( 'post', 'title', 'heading' ),
				'documentation' => array(
					__( 'Page Title Help', 'trx_addons' ) => TrxAddonsUtils::get_doc_url( 'post_title' )
				)
			),
			'PostMeta' => array(
				'title' => __('Post Meta', 'trx_addons'),
				'name' => 'trx_elm_post_meta',
				'icon' => 'eicon-post-info trx_addons_elementor_widget_icon',
				'keywords' => array( 'post', 'meta', 'info', 'author', 'date', 'comments' ),
				'documentation' => array(
					__( 'Post Meta Help', 'trx_addons' ) => TrxAddonsUtils::get_doc_url( 'post_meta' )
				)
			),
			'Posts' => array(
				'title' => __('Posts', 'trx_addons'),
				'name' => 'trx_elm_posts',
				'icon' => 'eicon-posts-grid trx_addons_elementor_widget_icon',
				'keywords' => array( 'posts', 'grid', 'masonry', 'carousel', 'slider', 'loop', 'listing' ),
				'documentation' => array(
					__( 'Posts Help', 'trx_addons' ) => TrxAddonsUtils::get_doc_url( 'posts' )
				)
			),
			'PricingMenu' => array(
				'title' => __('Pricing Menu', 'trx_addons'),
				'name' => 'trx_elm_pricing_menu',
				'icon' => 'eicon-price-list trx_addons_elementor_widget_icon',
				'keywords' => array( 'price', 'menu', 'money', 'pricing' ),
				'documentation' => array(
					__( 'Pricing Menu Help', 'trx_addons' ) => TrxAddonsUtils::get_doc_url( 'pricing_menu' )
				)
			),
			'PricingTable' => array(
				'title' => __('Pricing Table', 'trx_addons'),
				'name' => 'trx_elm_pricing_table',
				'icon' => 'eicon-price-table trx_addons_elementor_widget_icon',
				'keywords' => array( 'price', 'table', 'money', 'pricing' ),
				'documentation' => array(
					__( 'Pricing Table Help', 'trx_addons' ) => TrxAddonsUtils::get_doc_url( 'pricing_table' )
				)
			),
			'TeamMember' => array(
				'title' => __('Team Member', 'trx_addons'),
				'name' => 'trx_elm_team_member',
				'icon' => 'eicon-person trx_addons_elementor_widget_icon',
				'keywords' => array( 'team', 'member' ),
				'documentation' => array(
					__( 'Team Member Help', 'trx_addons' ) => TrxAddonsUtils::get_doc_url( 'team_member' )
				)
			),
		), trx_addons_exists_woocommerce()
			? array(
				'WooProducts' => array(
					'title' => __('Woo Products', 'trx_addons'),
					'name' => 'trx_elm_woo_products',
					'icon' => 'eicon-products trx_addons_elementor_widget_icon',
					'keywords' => array( 'woocommerce', 'products', 'goods', 'grid', 'item', 'loop', 'listing' ),
					'documentation' => array(
						__( 'WooCommerce Products Help', 'trx_addons' ) => TrxAddonsUtils::get_doc_url( 'woo_products' )
					)
				)
			)
			: array()
		) );
	}

	/**
	 * Load widgets from the global list
	 * 
	 * @hooked 'elementor/init'
	 */
	public function widgets_load() {
		static $loaded = false;
		if ( $loaded ) return;
		$loaded = true;
		global $TRX_ADDONS_STORAGE;
		if ( is_array( $TRX_ADDONS_STORAGE['elementor_widgets_list'] ) ) {
			foreach ( $TRX_ADDONS_STORAGE['elementor_widgets_list'] as $widget => $params ) {
				if ( $this->widget_is_allowed( $widget ) ) {
					if ( $this->allow_override_widgets_in_theme ) {
						if ( ( $fdir = trx_addons_get_file_dir( TRX_ADDONS_PLUGIN_ADDONS . "elementor-widgets/classes/Widgets/{$widget}/{$widget}.php" ) ) != '' ) { 
							include_once $fdir;
							$this->widget_is_loaded( $widget, true );
						}
					}
					// Create the module class
					$module = "TrxAddons\\ElementorWidgets\\Widgets\\{$widget}\\{$widget}";
					new $module();
				}
			}
		}
	}

	/**
	 * Return a data of the widget
	 * 
	 * @param string $widget  the widget name
	 * @param string $key     the key of the data
	 * 
	 * @return array  the widget data
	 */
	public function widget_data( $widget, $key = '' ) {
		global $TRX_ADDONS_STORAGE;
		return $key !== ''
				? ( isset( $TRX_ADDONS_STORAGE['elementor_widgets_list'][ $widget ][ $key ] )
					? $TRX_ADDONS_STORAGE['elementor_widgets_list'][ $widget ][ $key ]
					: ''
				 	)
				: ( isset( $TRX_ADDONS_STORAGE['elementor_widgets_list'][ $widget ] )
					? $TRX_ADDONS_STORAGE['elementor_widgets_list'][ $widget ]
					: array()
				);
	}

	/**
	 * Check if the widget is allowed
	 *
	 * @param string $widget  the widget name
	 * 
	 * @return bool  true if the widget is allowed
	 */
	private function widget_is_allowed( $widget ) {
		return true || trx_addons_components_is_allowed( 'elementor_widgets', $widget );
	}

	/**
	 * Check if the widget is loaded or set a new state
	 * 
	 * @param string $widget  the widget name
	 * @param int $set        the new state
	 * 
	 * @return bool  true if the widget is loaded
	 */
	private function widget_is_loaded( $widget, $set = -1 ) {
		return true || trx_addons_components_is_loaded( 'elementor_widgets', $widget, $set );
	}



	/**********************************************************
	 * Controls
	 **********************************************************/

	/**
	 * Add controls list to the global storage
	 */
	public function controls_list() {
		static $loaded = false;
		if ( $loaded ) return;
		$loaded = true;
		global $TRX_ADDONS_STORAGE;
		$TRX_ADDONS_STORAGE['elementor_controls_list'] = apply_filters('trx_addons_elementor_controls_list', array(
			'Query' => array(
				'title' => __('Query', 'trx_addons'),
			),
			'Transition' => array(
				'title' => __('Transition', 'trx_addons'),
			),
		) );
	}

	/**
	 * Load controls from the global list
	 * 
	 * @hooked 'elementor/init'
	 */
	public function controls_load() {
		static $loaded = false;
		if ( $loaded ) return;
		$loaded = true;
		global $TRX_ADDONS_STORAGE;
		if ( is_array( $TRX_ADDONS_STORAGE['elementor_controls_list'] ) ) {
			foreach ( $TRX_ADDONS_STORAGE['elementor_controls_list'] as $control => $params ) {
				if ( $this->allow_override_widgets_in_theme ) {
					if ( ( $fdir = trx_addons_get_file_dir( TRX_ADDONS_PLUGIN_ADDONS . "elementor-widgets/classes/Controls/{$control}/{$control}.php" ) ) != '' ) { 
						include_once $fdir;
					}
				}
				// Create the module class
				$module = "TrxAddons\\ElementorWidgets\\Controls\\{$control}\\{$control}";
				new $module();
			}
		}
	}

}
