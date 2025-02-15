<?php
/**
 * Affiliate links: LearnDash
 *
 * @package ThemeREX Addons
 * @since v2.30.2
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}

// An array with links to replace all redirections to the plugin's site with affiliate links
define( 'TRX_ADDONS_AFF_LINKS_LEARNDASH', array(
	'//www.learndash.com/pricing-and-purchase/' => 'https://stellarwp.pxf.io/eKmo7j',
	'//www.learndash.com/' => 'https://stellarwp.pxf.io/Py5LGY',
) );

// An array with pages to replace all redirections to the plugin's site with affiliate links
define( 'TRX_ADDONS_AFF_PAGES_LEARNDASH', array(
	'edit.php?post_type=sfwd',
	'admin.php?page=learndash',
	'plugins.php'
) );

if ( ! function_exists( 'trx_addons_learndash_change_url_in_js' ) ) {
	// add_filter( 'trx_addons_filter_localize_script', 'trx_addons_learndash_change_url_in_js' );
	add_filter( 'trx_addons_filter_localize_script_admin', 'trx_addons_learndash_change_url_in_js' );
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
	function trx_addons_learndash_change_url_in_js( $vars ) {
		if ( ! isset( $vars['add_to_links_url'] ) ) {
			$vars['add_to_links_url'] = array();
		}
		if ( is_array( TRX_ADDONS_AFF_LINKS_LEARNDASH ) ) {
			foreach( TRX_ADDONS_AFF_LINKS_LEARNDASH as $mask => $url ) {
				$vars['add_to_links_url'][] = array(
					'slug' => 'learndash',
					'page' => defined( 'TRX_ADDONS_AFF_PAGES_LEARNDASH' ) && is_array( TRX_ADDONS_AFF_PAGES_LEARNDASH ) && count( TRX_ADDONS_AFF_PAGES_LEARNDASH ) > 0 ? TRX_ADDONS_AFF_PAGES_LEARNDASH : false,
					'mask' => $mask,	// if a link href contains this substring - replace it
					'link' => $url,		// new link to replace
				);
			}
		}
		return $vars;
	}
}
