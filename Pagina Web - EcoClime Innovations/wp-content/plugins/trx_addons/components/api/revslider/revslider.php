<?php
/**
 * Plugin support: Revolution Slider
 *
 * @package ThemeREX Addons
 * @since v1.0
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}

// Check if RevSlider installed and activated
// Attention! This function is used in many files and was moved to the api.php
/*
if ( !function_exists( 'trx_addons_exists_revslider' ) ) {
	function trx_addons_exists_revslider() {
		return function_exists('rev_slider_shortcode') || class_exists( 'RevSliderData' );
	}
}
*/

if ( ! function_exists( 'trx_addons_get_list_revsliders' ) ) {
	/**
	 * Return list of Revolution sliders
	 * 
	 * @param bool $prepend_inherit  If true - add first element to the list with 'inherit' value
	 * 
	 * @return array  List of sliders
	 */
	function trx_addons_get_list_revsliders( $prepend_inherit = false ) {
		static $list = false;
		if ( $list === false ) {
			$list = array();
			if ( trx_addons_exists_revslider() ) {
				global $wpdb;
				$rows = $wpdb->get_results( "SELECT alias, title FROM " . esc_sql($wpdb->prefix) . "revslider_sliders" );
				if ( is_array( $rows ) && count( $rows ) > 0 ) {
					foreach ( $rows as $row ) {
						$list[ $row->alias ] = $row->title;
					}
				}
			}
		}
		return $prepend_inherit ? array_merge( array( 'inherit' => esc_html__( "Inherit", 'trx_addons' ) ), $list ) : $list;
	}
}

if ( ! function_exists( 'trx_addons_add_revslider_to_engines' ) ) {
	add_filter( 'trx_addons_filter_get_list_sc_slider_engines', 'trx_addons_add_revslider_to_engines' );
	/**
	 * Add RevSlider to the slider engines list for our widget 'Slider'
	 * 
	 * @hooked trx_addons_filter_get_list_sc_slider_engines
	 *
	 * @param array $list  List of the slider engines
	 * 
	 * @return array       Modified list of the slider engines
	 */
	function trx_addons_add_revslider_to_engines( $list ) {
		if ( trx_addons_exists_revslider() ) {
			$list["revo"] = esc_html__("Layer slider (Revolution)", 'trx_addons');
		}
		return $list;
	}
}

if ( ! function_exists( 'trx_addons_check_revslider_in_content' ) ) {
	add_filter( 'revslider_include_libraries', 'trx_addons_check_revslider_in_content', 20 );
	/**
	 * Check if RevSlider is present in the current page content and allow to load its scripts and styles
	 * 
	 * @param bool $load    Current state of the flag
	 * @param int $post_id  Current post ID
	 * 
	 * @return bool         True if RevSlider is present in the current page content
	 */
	function trx_addons_check_revslider_in_content( $load, $post_id = -1 ) {
		if ( ! $load ) {
			$load = trx_addons_is_preview()					// Load if current page is builder preview
					|| trx_addons_sc_check_in_content(		// or if a shortcode is present in the current page
							array(
								'sc' => 'revslider',
								'entries' => array(
									array( 'type' => 'sc',  'sc' => 'rev_slider' ),
									array( 'type' => 'sc',  'sc' => 'trx_widget_slider',                'param' => 'engine="revo"' ),
									array( 'type' => 'gb',  'sc' => 'wp:trx-addons/slider',             'param' => '"engine":"revo"' ),
									array( 'type' => 'elm', 'sc' => '"widgetType":"trx_widget_slider',  'param' => '"engine":"revo"' ),
									array( 'type' => 'elm', 'sc' => '"widgetType":"wp-widget-rev-slider' ),
									array( 'type' => 'elm', 'sc' => '"shortcode":"[rev_slider' ),
									array( 'type' => 'elm', 'sc' => '"shortcode":"[trx_widget_slider',  'param' => 'engine="revo"' ),
								)
							),
							$post_id
						);
		}
		return $load;
	}
}

if ( ! function_exists( 'trx_addons_revslider_load_scripts_front' ) ) {
	add_action( "wp_enqueue_scripts", 'trx_addons_revslider_load_scripts_front', TRX_ADDONS_ENQUEUE_SCRIPTS_PRIORITY );
	add_action( 'trx_addons_action_pagebuilder_preview_scripts', 'trx_addons_revslider_load_scripts_front', 10, 1 );
	/**
	 * Load required styles and scripts for the frontend for the RevSlider
	 * 
	 * @hooked wp_enqueue_scripts
	 * @hooked trx_addons_action_pagebuilder_preview_scripts
	 * 
	 * @trigger trx_addons_action_load_scripts_front
	 *
	 * @param bool $force  Force load scripts
	 */
	function trx_addons_revslider_load_scripts_front( $force = false ) {
		if ( ! trx_addons_exists_revslider() ) {
			return;
		}
		trx_addons_enqueue_optimized( 'revslider', $force, array(
			'check' => array(
				array( 'type' => 'sc',  'sc' => 'rev_slider' ),
				array( 'type' => 'sc',  'sc' => 'trx_widget_slider',                'param' => 'engine="revo"' ),
				array( 'type' => 'gb',  'sc' => 'wp:trx-addons/slider',             'param' => '"engine":"revo"' ),
				array( 'type' => 'elm', 'sc' => '"widgetType":"trx_widget_slider',  'param' => '"engine":"revo"' ),
				array( 'type' => 'elm', 'sc' => '"widgetType":"wp-widget-rev-slider' ),
				array( 'type' => 'elm', 'sc' => '"shortcode":"[rev_slider' ),
				array( 'type' => 'elm', 'sc' => '"shortcode":"[trx_widget_slider',  'param' => 'engine="revo"' ),
			)
		) );
	}
}

if ( ! function_exists( 'trx_addons_revslider_check_in_html_output' ) ) {
//	add_filter( 'trx_addons_filter_get_menu_cache_html', 'trx_addons_revslider_check_in_html_output', 10, 1 );
//	add_action( 'trx_addons_action_show_layout_from_cache', 'trx_addons_revslider_check_in_html_output', 10, 1 );
	add_action( 'trx_addons_action_check_page_content', 'trx_addons_revslider_check_in_html_output', 10, 1 );
	/**
	 * Check if the RevoSlider is in the HTML of the current page output and force load its scripts and styles
	 * 
	 * @hooked trx_addons_action_check_page_content
	 *
	 * @param string $content  The HTML content of the current page
	 * 
	 * @return string          The HTML content of the current page
	 */
	function trx_addons_revslider_check_in_html_output( $content = '' ) {
		if ( ! trx_addons_exists_revslider() ) {
			return $content;
		}
		$args = array(
			'check' => array(
				'id=[\'"][^\'"]*rev_slider_',
				'<rs-module ',
				'<rs-slide '
			)
		);
		if ( trx_addons_check_in_html_output( 'revslider', $content, $args ) ) {
			trx_addons_revslider_load_scripts_front( true );
		}
		return $content;
	}
}

if ( !function_exists( 'trx_addons_revslider_filter_head_output' ) ) {
	add_filter( 'trx_addons_filter_page_head', 'trx_addons_revslider_filter_head_output', 10, 1 );
	/**
	 * Remove plugin-specific styles and scripts from the page head if they are present in the page head
	 * and an option 'Optimize scripts and styles loading' is enabled
	 * 
	 * @hooked trx_addons_filter_page_head
	 *
	 * @param string $content  The HTML content of the page head
	 * 
	 * @return string          The HTML content of the page head
	 */
	function trx_addons_revslider_filter_head_output( $content = '' ) {
		if ( ! trx_addons_exists_revslider() ) {
			return $content;
		}
		return trx_addons_filter_head_output( 'revslider', $content, array(
			'check' => array(
				'#<link[^>]*href=[\'"][^\'"]*/revslider/[^>]*>#'
			)
		) );
	}
}

if ( ! function_exists( 'trx_addons_revslider_filter_body_output' ) ) {
	add_filter( 'trx_addons_filter_page_content', 'trx_addons_revslider_filter_body_output', 10, 1 );
	/**
	 * Remove plugin-specific styles and scripts from the page body if they are present in the page body
	 * and an option 'Optimize scripts and styles loading' is enabled
	 * 
	 * @hooked trx_addons_filter_page_content
	 *
	 * @param string $content  The HTML content of the page body
	 * 
	 * @return string          The HTML content of the page body
	 */
	function trx_addons_revslider_filter_body_output( $content = '' ) {
		if ( ! trx_addons_exists_revslider() ) {
			return $content;
		}
		return trx_addons_filter_body_output( 'revslider', $content, array(
			'allow' => ! trx_addons_need_frontend_scripts( 'essential_grid' ),		// Essential Grid may use some scripts from EevSlider (tools.js)
			'check' => array(
				'#<link[^>]*href=[\'"][^\'"]*/revslider/[^>]*>#',
				'#<script[^>]*src=[\'"][^\'"]*/revslider/[^>]*>[\\s\\S]*</script>#U'
			)
		) );
	}
}

if ( ! function_exists( 'trx_addons_revslider_disable_welcome_screen' ) ) {
	add_action( 'admin_init', 'trx_addons_revslider_disable_welcome_screen', 0 );
	/**
	 * Disable welcome screen for the RevSlider plugin while the demo data is importing or plugins are installing
	 * 
	 * @hooked admin_init
	 */
	function trx_addons_revslider_disable_welcome_screen() {
		if ( trx_addons_exists_revslider() && class_exists( 'RevSliderAdmin' )
			&& (
				trx_addons_check_url( 'admin.php' ) && trx_addons_get_value_gp( 'page' ) == 'trx_addons_theme_panel'
				||
				(int) trx_addons_get_value_gp( 'admin-multi' ) == 1
				||
				trx_addons_get_value_gp( 'page' ) == 'tgmpa-install-plugins'
			) 
		) {
			//remove_action( 'admin_init', array( 'RevSliderAdmin', 'open_welcome_page' ) );
			trx_addons_remove_filter( 'admin_init', 'open_welcome_page' );
		}
	}
}


// Add shortcodes
//----------------------------------------------------------------------------

// Add shortcodes to Gutenberg
if ( trx_addons_exists_revslider() && trx_addons_exists_gutenberg() ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_API . 'revslider/revslider-sc-gutenberg.php';
}


// Demo data install
//----------------------------------------------------------------------------

// One-click import support
if ( is_admin() ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_API . 'revslider/revslider-demo-importer.php';
}

// OCDI support
if ( is_admin() && trx_addons_exists_revslider() && function_exists( 'trx_addons_exists_ocdi' ) && trx_addons_exists_ocdi() ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_API . 'revslider/revslider-demo-ocdi.php';
}
