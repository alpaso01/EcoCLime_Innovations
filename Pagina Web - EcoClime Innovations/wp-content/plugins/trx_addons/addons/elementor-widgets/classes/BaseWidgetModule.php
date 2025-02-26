<?php
/**
 * Base Module class
 *
 * @package ThemeREX Addons
 * @since v2.30.0
 */

namespace TrxAddons\ElementorWidgets;

use TrxAddons\ElementorWidgets\BaseModule;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Base module
 */
abstract class BaseWidgetModule extends BaseModule {

	protected $widget_class = '';
	protected $widget_name  = '';

	protected $assets = array(
		'css' => false,
		'js'  => false,
	);

	/**
	 * Constructor.
	 *
	 * Initializing the module base class.
	 */
	public function __construct() {
		parent::__construct();
		// Module and Widget class names
		$this->widget_class = $this->module_class . 'Widget';
		$this->widget_name  = ElementorWidgets::instance()->widget_data( $this->module_class, 'name' );
		// Register the widget
		add_action( 'elementor/init', array( $this, 'register_widget' ) );
		// Enqueue scripts and styles
		add_action( 'wp_enqueue_scripts', array( $this, 'load_scripts_front' ), TRX_ADDONS_ENQUEUE_SCRIPTS_PRIORITY );
		add_action( 'trx_addons_action_pagebuilder_preview_scripts', array( $this, 'load_scripts_front' ), 10, 1 );
		// Check widget in the html output
		// add_filter( 'trx_addons_filter_get_menu_cache_html', array( $this, 'check_in_html_output' ), 10, 1 );
		// add_action( 'trx_addons_action_show_layout_from_cache', array( $this, 'check_in_html_output' ), 10, 1 );
		add_action( 'trx_addons_action_check_page_content', array( $this, 'check_in_html_output' ), 10, 1 );

		// Merge styles to the single stylesheet
		add_filter( 'trx_addons_filter_merge_styles', array( $this, 'merge_styles' ) );
		// Merge scripts to the single file
		add_filter( 'trx_addons_filter_merge_scripts', array( $this, 'merge_scripts' ) );

		// Add a selector for the animation type 'Item by item'
		add_filter( 'trx_addons_filter_elementor_animate_items', array( $this, 'add_separate_animation_selector' ) );
		add_filter( 'trx_addons_filter_elementor_animate_as_text', array( $this, 'add_text_animation_class' ) );
	}
	
	/**
	 * Create and register widget
	 * 
	 * @hooked 'elementor/init'
	 */
	public function register_widget() {
		$widget = "TrxAddons\\ElementorWidgets\\Widgets\\{$this->module_class}\\{$this->widget_class}";
		trx_addons_elm_register_widget( new $widget() );
	}

	/**
	 * Get module relative path to the assets folder
	 * 
	 * @return string  Relative path to the assets folder in the module folder.
	 */
	public function get_assets_path( $file ) {
		return TRX_ADDONS_PLUGIN_ADDONS . 'elementor-widgets/classes/Widgets/' . $this->module_class . ( substr( $file, 0, 3 ) == '../' ? '/' : '/assets/' ) . $file;
	}

	/**
	 * Load required styles and scripts for the frontend
	 * 
	 * @hooked wp_enqueue_scripts
	 * @hooked trx_addons_action_pagebuilder_preview_scripts
	 * 
	 * @param bool $force  Optional. Force load scripts. Default is false.
	 */
	public function load_scripts_front( $force = false ) {
		if ( ! $this->assets['css'] && ! $this->assets['js'] ) {
			return;
		}
		$slug = str_replace( 'trx-elm-', 'trx-addons-elm-', str_replace( '_', '-', $this->widget_name ) );
		$assets = array(
			'check' => array(
				array( 'type' => 'elm', 'sc' => '"widgetType":"' . $this->widget_name . '"' ),
			)
		);
		if ( $this->assets['css'] ) {
			$assets['css'] = array(
				$slug => array( 'src' => $this->get_assets_path( "{$this->module_class}.css" ) ),
			);
		}
		if ( $this->assets['js'] ) {
			$assets['js'] = array(
				$slug => array( 'src' => $this->get_assets_path( "{$this->module_class}.js" ), 'deps' => 'jquery' ),
			);
		}
		if ( ! empty( $this->assets['localize'] ) && is_array( $this->assets['localize'] ) ) {
			$assets['localize'] = array(
				$slug => $this->assets['localize']
			);
		}
		if ( ! empty( $this->assets['lib'] ) && is_array( $this->assets['lib'] ) ) {
			$assets['lib'] = array();
			foreach ( array( 'js', 'css' ) as $type ) {
				if ( ! empty( $this->assets['lib'][ $type ] ) && is_array( $this->assets['lib'][ $type ] ) ) {
					foreach ( $this->assets['lib'][ $type ] as $handle => $params ) {
						if ( empty( $params['src'] ) ) {
							$assets['lib'][ $type ][ $handle ] = true;
						} else {
							$assets['lib'][ $type ][ $handle ] = array_merge( $params, array( 'src' => $this->get_assets_path( $params['src'] ) ) );
						}
					}
				}
			}
		}
		trx_addons_enqueue_optimized( $this->widget_name, $force, $assets );
	}

	/**
	 * Force load styles and scripts on homepage
	 * (if Settings - Reading - Your homepage displays - A latest posts is selected)
	 * Need, because in this case the action 'trx_addons_action_check_page_content'
	 * got not whole page content, but only the posts list
	 * 
	 * @return boolean  True if need to force load styles and scripts on homepage, false - otherwise.
	 */
	public function force_styles_on_homepage() {
		return false;
	}

	/**
	 * Load styles and scripts if present in the cache of the menu or layouts or finally in the whole page output
	 * 
	 * @hooked trx_addons_filter_get_menu_cache_html
	 * @hooked trx_addons_action_show_layout_from_cache
	 * @hooked trx_addons_action_check_page_content
	 * 
	 * @param string $content  HTML content to check
	 * 
	 * @return string  HTML content
	 */
	public function check_in_html_output( $content = '' ) {
		$args = array(
			'check' => array(
				'data-widget_type="' . $this->widget_name
			)
		);
		if ( trx_addons_check_in_html_output( $this->widget_name, $content, $args )
			|| ( is_home() && is_front_page() && $this->force_styles_on_homepage() )
		) {
			$this->load_scripts_front( true );
		}
		return $content;
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
		if ( $this->assets['css'] ) {
			$list[ $this->get_assets_path( "{$this->module_class}.css" ) ] = false;
		}
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
		if ( $this->assets['js'] ) {
			$list[ $this->get_assets_path( "{$this->module_class}.js" ) ] = false;
		}
		return $list;
	}

	/**
	 * Get the selector for the animation type 'Item by item'
	 * 
	 * @return string  The selector of the single item.
	 */
	public function get_separate_animation_selector() {
		return '';
	}

	/**
	 * Add a selector for the animation type 'Item by item'
	 * 
	 * @hooked trx_addons_filter_elementor_animate_items
	 * 
	 * @param array $selectors  List of selectors
	 * 
	 * @return array  Modified list of selectors
	 */
	public function add_separate_animation_selector( $selectors ) {
		$selector = $this->get_separate_animation_selector();
		if ( ! empty( $selector ) ) {
			$selectors[] = $selector;
		}
		return $selectors;
	}

	/**
	 * Get the class name for the text animation types 'Word by Word', 'Char by Char', etc.
	 * 
	 * @return string  The class name of the widget block with text suitable for this animation.
	 */
	public function get_text_animation_class() {
		return '';
	}

	/**
	 * Add a wrapper class for the text animation types 'Word by Word', 'Char by Char', etc.
	 * 
	 * @hooked trx_addons_filter_elementor_animate_as_text
	 * 
	 * @param array $classes  List of the class names of the widget block with text suitable for this animation.
	 * 
	 * @return array  Modified list of classes
	 */
	public function add_text_animation_class( $classes ) {
		$class = $this->get_text_animation_class();
		if ( ! empty( $class ) && is_array( $class ) ) {
			$classes = array_merge( $classes, $class );
		}
		return $classes;
	}
}
