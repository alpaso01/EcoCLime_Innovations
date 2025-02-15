<?php
/**
 * Affiliate links: RevSlider
 *
 * @package ThemeREX Addons
 * @since v2.30.2
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}

// An array with links to replace all redirections to the ThemePunch site with affiliate links
define( 'TRX_ADDONS_AFF_LINKS_REVSLIDER', array(
	// Dashboard
	'//account.sliderrevolution.com/portal' => 'https://themepunch.pxf.io/4ekEVG',
	// Go Premium
	'//account.sliderrevolution.com/portal/pricing' => 'https://themepunch.pxf.io/KeRz5z',
	// Premium Features
	'sliderrevolution.com/premium-slider-revolution' => 'https://themepunch.pxf.io/9W1nyy',
	// Support
	'//support.sliderrevolution.com' => 'https://themepunch.pxf.io/P0LbGq',
	// Help center
	'sliderrevolution.com/help-center' => 'https://themepunch.pxf.io/doXGdy',
	// Geting Started
	'sliderrevolution.com/manual' => 'https://themepunch.pxf.io/ZdkK3q',
	// Get on board
	'sliderrevolution.com/get-on-board-the-slider-revolution-dashboard' => 'https://themepunch.pxf.io/QOqb1z',
	// Addons
	'sliderrevolution.com/expand-possibilities-with-addons' => 'https://themepunch.pxf.io/6baEN3',
	// Templates
	'sliderrevolution.com/examples' => 'https://themepunch.pxf.io/rnvXdB',
	// Pro level design
	'sliderrevolution.com/pro-level-design-with-slider-revolution' => 'https://themepunch.pxf.io/jWEmda',
	// Privacy Policy
	'sliderrevolution.com/plugin-privacy-policy' => 'https://themepunch.pxf.io/gbzGE0',
	// FAQ: Licence deactivated
	'sliderrevolution.com/faq/why-was-my-slider-revolution-license-deactivated' => 'https://themepunch.pxf.io/RyxbVy',
	// FAQ: Clear caches
	'sliderrevolution.com/faq/updating-make-sure-clear-caches' => 'https://themepunch.pxf.io/Yg5Nzq',
	// FAQ: Where to find purchase code
	'sliderrevolution.com/faq/where-to-find-purchase-code' => 'https://themepunch.pxf.io/x9xZdO',
	// Documentation: Changelog
	'sliderrevolution.com/documentation/changelog' => 'https://themepunch.pxf.io/EanyNn',
	// Documentation: System requirements
	'sliderrevolution.com/documentation/system-requirements/' => 'https://themepunch.pxf.io/LPv2kO',
	// Site
	'sliderrevolution.com' => 'https://themepunch.pxf.io/DVEORn',
) );

// An array with pages to replace all redirections to the plugin's site with affiliate links
define( 'TRX_ADDONS_AFF_PAGES_REVSLIDER', array(
	'admin.php?page=revslider',
	'plugins.php'
) );

if ( ! function_exists( 'trx_addons_revslider_change_gopro_plugins' ) && defined('RS_PLUGIN_SLUG_PATH') ) {
	add_filter( 'plugin_action_links_' . RS_PLUGIN_SLUG_PATH, 'trx_addons_revslider_change_gopro_plugins', 11 );
	/**
	 * Change "Go Premium" link to our affiliate link in the plugin's page
	 * 
	 * @hooked plugin_action_links_revslider
	 * 
	 * @param array $links  List of links in the plugin's page
	 * 
	 * @return array        Modified list of links in the plugin's page
	 */
	function trx_addons_revslider_change_gopro_plugins( $links ) {
		if ( ! empty( $links['go_premium'] ) && preg_match( '/href="([^"]*)"/', $links['go_premium'], $matches ) && ! empty( $matches[1] ) ) {
			$links['go_premium'] = str_replace( $matches[1], trx_addons_get_url_by_mask( $matches[1], TRX_ADDONS_AFF_LINKS_REVSLIDER ), $links['go_premium'] );
		}
		return $links;
	}
}

if ( ! function_exists( 'trx_addons_revslider_change_gopro_menu' ) ) {
	add_filter( 'wp_redirect', 'trx_addons_revslider_change_gopro_menu', 11, 2 );
	/**
	 * Change "Go Premium" link to our affiliate link in the plugin's menu (while redirect to the plugin's page)
	 * 
	 * @hooked wp_redirect
	 * 
	 * @param string $link    Link to redirect
	 * @param int    $status  Redirect status
	 * 
	 * @return string         Modified link to redirect
	 */
	function trx_addons_revslider_change_gopro_menu( $link, $status = 0 ) {
		return trx_addons_get_url_by_mask( $link, TRX_ADDONS_AFF_LINKS_REVSLIDER );
	}
}

if ( ! function_exists( 'trx_addons_revslider_change_gopro_url_in_js' ) ) {
	// add_filter( 'trx_addons_filter_localize_script', 'trx_addons_revslider_change_gopro_url_in_js' );
	add_filter( 'trx_addons_filter_localize_script_admin', 'trx_addons_revslider_change_gopro_url_in_js' );
	/**
	 * Prepare variables to change "Go Premium" link to our affiliate link in JavaScript
	 * 
	 * @hooked trx_addons_filter_localize_script
	 * @hooked trx_addons_filter_localize_script_admin
	 * 
	 * @param array $vars  List of variables to localize
	 * 
	 * @return array       Modified list of variables to localize
	 */
	function trx_addons_revslider_change_gopro_url_in_js( $vars ) {
		if ( ! isset( $vars['add_to_links_url'] ) ) {
			$vars['add_to_links_url'] = array();
		}
		if ( is_array( TRX_ADDONS_AFF_LINKS_REVSLIDER ) ) {
			foreach( TRX_ADDONS_AFF_LINKS_REVSLIDER as $mask => $url ) {
				$vars['add_to_links_url'][] = array(
					'slug' => 'revslider',
					'page' => defined( 'TRX_ADDONS_AFF_PAGES_REVSLIDER' ) && is_array( TRX_ADDONS_AFF_PAGES_REVSLIDER ) && count( TRX_ADDONS_AFF_PAGES_REVSLIDER ) > 0 ? TRX_ADDONS_AFF_PAGES_REVSLIDER : false,
					'mask' => $mask,
					'link' => $url
				);
			}
		}
		return $vars;
	}
}
