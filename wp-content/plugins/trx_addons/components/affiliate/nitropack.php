<?php
/**
 * Affiliate links: NitroPack
 *
 * @package ThemeREX Addons
 * @since v2.30.2
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}

// A referal hash to the "Go Pro" link
define( 'TRX_ADDONS_AFF_LINKS_NITROPACK_GO_PRO_HASH', 'NZDWDU' );

// An array with links to replace all redirections to the plugin's site with affiliate links
define( 'TRX_ADDONS_AFF_LINKS_NITROPACK', array(
	'nitropack.io/' => '',
) );

// An array with pages to replace all redirections to the plugin's site with affiliate links
define( 'TRX_ADDONS_AFF_PAGES_NITROPACK', array(
	'admin.php?page=nitropack',
	'plugins.php'
) );

if ( ! function_exists( 'trx_addons_nitropack_change_url_in_js' ) ) {
	// add_filter( 'trx_addons_filter_localize_script', 'trx_addons_nitropack_change_url_in_js' );
	add_filter( 'trx_addons_filter_localize_script_admin', 'trx_addons_nitropack_change_url_in_js' );
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
	function trx_addons_nitropack_change_url_in_js( $vars ) {
		if ( ! isset( $vars['add_to_links_url'] ) ) {
			$vars['add_to_links_url'] = array();
		}
		if ( is_array( TRX_ADDONS_AFF_LINKS_NITROPACK ) ) {
			foreach( TRX_ADDONS_AFF_LINKS_NITROPACK as $mask => $url ) {
				$vars['add_to_links_url'][] = array(
					'slug' => 'nitropack',
					'page' => defined( 'TRX_ADDONS_AFF_PAGES_NITROPACK' ) && is_array( TRX_ADDONS_AFF_PAGES_NITROPACK ) && count( TRX_ADDONS_AFF_PAGES_NITROPACK ) > 0 ? TRX_ADDONS_AFF_PAGES_NITROPACK : false,
					'mask' => $mask,	// if a link href contains this substring - replace it
					'hash' => TRX_ADDONS_AFF_LINKS_NITROPACK_GO_PRO_HASH,	// hash to add to the link
				);
			}
		}
		return $vars;
	}
}
