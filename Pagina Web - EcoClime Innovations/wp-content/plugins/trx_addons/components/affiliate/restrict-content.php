<?php
/**
 * Affiliate links: Restrict Content
 *
 * @package ThemeREX Addons
 * @since v2.30.2
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}

// An array with links to replace all redirections to the plugin's site with affiliate links
define( 'TRX_ADDONS_AFF_LINKS_RESTRICT_CONTENT', array(
	'//restrictcontentpro.com/add-ons/pro/' => 'https://stellarwp.pxf.io/Py5L5e',
	'//restrictcontentpro.com/add-on/' => 'https://stellarwp.pxf.io/Py5L5e',
	'//restrictcontentpro.com/add-ons' => 'https://stellarwp.pxf.io/AW4z4o',
	'//restrictcontentpro.com/why-go-pro' => 'https://stellarwp.pxf.io/banZnv',
	'//restrictcontentpro.com/pricing' => 'https://stellarwp.pxf.io/vNALAv',
	'//restrictcontentpro.com/demo' => 'https://stellarwp.pxf.io/Y9Z5ZP',
	'//restrictcontentpro.com' => 'https://stellarwp.pxf.io/Vm1r1O',
) );

// An array with pages to replace all redirections to the plugin's site with affiliate links
define( 'TRX_ADDONS_AFF_PAGES_RESTRICT_CONTENT', array(
	'admin.php?page=restrict-content',
	'admin.php?page=rcp-',
	'plugins.php'
) );

if ( ! function_exists( 'trx_addons_restrict_content_change_url_in_js' ) ) {
	// add_filter( 'trx_addons_filter_localize_script', 'trx_addons_restrict_content_change_url_in_js' );
	add_filter( 'trx_addons_filter_localize_script_admin', 'trx_addons_restrict_content_change_url_in_js' );
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
	function trx_addons_restrict_content_change_url_in_js( $vars ) {
		if ( ! isset( $vars['add_to_links_url'] ) ) {
			$vars['add_to_links_url'] = array();
		}
		if ( is_array( TRX_ADDONS_AFF_LINKS_RESTRICT_CONTENT ) ) {
			foreach( TRX_ADDONS_AFF_LINKS_RESTRICT_CONTENT as $mask => $url ) {
				$vars['add_to_links_url'][] = array(
					'slug' => 'restrict-content',
					'page' => defined( 'TRX_ADDONS_AFF_PAGES_RESTRICT_CONTENT' ) && is_array( TRX_ADDONS_AFF_PAGES_RESTRICT_CONTENT ) && count( TRX_ADDONS_AFF_PAGES_RESTRICT_CONTENT ) > 0 ? TRX_ADDONS_AFF_PAGES_RESTRICT_CONTENT : false,
					'mask' => $mask,	// if a link href contains this substring - replace it
					'link' => $url,		// new link to replace
				);
			}
		}
		return $vars;
	}
}
