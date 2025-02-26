<?php
/**
 * Affiliate links: Better WP Security (iThemes Security, Solid WP Security)
 *
 * @package ThemeREX Addons
 * @since v2.30.2
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}

// An array with links to replace all redirections to the plugin's site with affiliate links
define( 'TRX_ADDONS_AFF_LINKS_BETTER_WP_SECURITY', array(
	'go.solidwp.com/basic-to-pro' => 'https://stellarwp.pxf.io/q4PRob',
) );

// An array with pages to replace all redirections to the plugin's site with affiliate links
define( 'TRX_ADDONS_AFF_PAGES_BETTER_WP_SECURITY', array(
	'admin.php?page=itsec-go-pro',
	'admin.php?page=itsec',
) );

if ( ! function_exists( 'trx_addons_better_wp_security_change_url_in_js' ) ) {
	// add_filter( 'trx_addons_filter_localize_script', 'trx_addons_better_wp_security_change_url_in_js' );
	add_filter( 'trx_addons_filter_localize_script_admin', 'trx_addons_better_wp_security_change_url_in_js' );
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
	function trx_addons_better_wp_security_change_url_in_js( $vars ) {
		if ( ! isset( $vars['add_to_links_url'] ) ) {
			$vars['add_to_links_url'] = array();
		}
		if ( is_array( TRX_ADDONS_AFF_LINKS_BETTER_WP_SECURITY ) ) {
			foreach( TRX_ADDONS_AFF_LINKS_BETTER_WP_SECURITY as $mask => $url ) {
				$vars['add_to_links_url'][] = array(
					'slug' => 'better-wp-security',
					'page' => defined( 'TRX_ADDONS_AFF_PAGES_BETTER_WP_SECURITY' ) && is_array( TRX_ADDONS_AFF_PAGES_BETTER_WP_SECURITY ) && count( TRX_ADDONS_AFF_PAGES_BETTER_WP_SECURITY ) > 0 ? TRX_ADDONS_AFF_PAGES_BETTER_WP_SECURITY : false,
					'mask' => $mask,	// if a link href contains this substring - replace it
					'link' => $url,		// new link to replace
				);
			}
		}
		return $vars;
	}
}
