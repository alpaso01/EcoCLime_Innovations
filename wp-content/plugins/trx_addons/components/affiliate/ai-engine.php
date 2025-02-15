<?php
/**
 * Affiliate links: AI Engine
 *
 * @package ThemeREX Addons
 * @since v2.30.2
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}

// A referal attribute to the "Go Pro" link
define( 'TRX_ADDONS_AFF_LINKS_AI_ENGINE_GO_PRO_REF', '296' );

// An array with links to replace all redirections to the plugin's site with affiliate links
define( 'TRX_ADDONS_AFF_LINKS_AI_ENGINE', array(
	'//meowapps.com' => '',
) );

// An array with pages to replace all redirections to the plugin's site with affiliate links
define( 'TRX_ADDONS_AFF_PAGES_AI_ENGINE', array(
	'tools.php?page=mwai',
	'edit.php?page=mwai',
	'admin.php?page=mwai',
	'admin.php?page=meowapps',
	'plugins.php'
) );

if ( ! function_exists( 'trx_addons_ai_engine_change_url_in_js' ) ) {
	// add_filter( 'trx_addons_filter_localize_script', 'trx_addons_ai_engine_change_url_in_js' );
	add_filter( 'trx_addons_filter_localize_script_admin', 'trx_addons_ai_engine_change_url_in_js' );
	/**
	 * Prepare variables to change links to our affiliate link in JavaScript
	 * 
	 * @hooked trx_addons_filter_localize_script
	 * @hooked trx_addons_filter_localize_script_admin
	 * 
	 * @param array $vars  List of variables to localize
	 * 
	 * @return array       Modified list of variables to localize
	 */
	function trx_addons_ai_engine_change_url_in_js( $vars ) {
		if ( ! isset( $vars['add_to_links_url'] ) ) {
			$vars['add_to_links_url'] = array();
		}
		if ( is_array( TRX_ADDONS_AFF_LINKS_AI_ENGINE ) ) {
			foreach( TRX_ADDONS_AFF_LINKS_AI_ENGINE as $mask => $url ) {
				$vars['add_to_links_url'][] = array(
					'slug' => 'ai-engine',
					'page' => defined( 'TRX_ADDONS_AFF_PAGES_AI_ENGINE' ) && is_array( TRX_ADDONS_AFF_PAGES_AI_ENGINE ) && count( TRX_ADDONS_AFF_PAGES_AI_ENGINE ) > 0 ? TRX_ADDONS_AFF_PAGES_AI_ENGINE : false,
					'mask' => $mask,	// if a link href contains this substring - replace it
					'args' => array( 'ref' => TRX_ADDONS_AFF_LINKS_AI_ENGINE_GO_PRO_REF ),		// url atts to add to the link
				);
			}
		}
		// Prepare the list of events to refresh links on the page.
		// For example, update links in the content after the tab clicked
		if ( ! isset( $vars['add_to_links_url_events'] ) ) {
			$vars['add_to_links_url_events'] = array();
		}
		$vars['add_to_links_url_events'][] = array(
			'slug' => 'ai-engine',
			'event' => 'click',
			'selector' => '#meow-common-dashboard .neko-tab-title',
			'delay' => 100,
		);
		return $vars;
	}
}
