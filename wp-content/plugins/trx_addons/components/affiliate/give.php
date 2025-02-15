<?php
/**
 * Affiliate links: Give WP
 *
 * @package ThemeREX Addons
 * @since v2.30.2
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}

// An array with links to replace all redirections to the plugin's site with affiliate links
define( 'TRX_ADDONS_AFF_LINKS_GIVE', array(
	'//givewp.com/pricing' => 'https://stellarwp.pxf.io/AW4zDx',
	'givewp.com/acplans' => 'https://stellarwp.pxf.io/AW4zDx',
	'//givewp.com/addons' => 'https://stellarwp.pxf.io/WqVjaG',
	'//go.givewp.com' => 'https://stellarwp.pxf.io/WqVjaG',
) );

// An array with pages to replace all redirections to the plugin's site with affiliate links
define( 'TRX_ADDONS_AFF_PAGES_GIVE', array(
	'edit.php?post_type=give_forms&page=give',
	'wp-admin/post.php',
	'wp-admin/post-new.php',
	'plugins.php'
) );

if ( ! function_exists( 'trx_addons_give_change_url_in_js' ) ) {
	// add_filter( 'trx_addons_filter_localize_script', 'trx_addons_give_change_url_in_js' );
	add_filter( 'trx_addons_filter_localize_script_admin', 'trx_addons_give_change_url_in_js' );
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
	function trx_addons_give_change_url_in_js( $vars ) {
		if ( ! isset( $vars['add_to_links_url'] ) ) {
			$vars['add_to_links_url'] = array();
		}
		if ( is_array( TRX_ADDONS_AFF_LINKS_GIVE ) ) {
			foreach( TRX_ADDONS_AFF_LINKS_GIVE as $mask => $url ) {
				$vars['add_to_links_url'][] = array(
					'slug' => 'give',
					'page' => defined( 'TRX_ADDONS_AFF_PAGES_GIVE' ) && is_array( TRX_ADDONS_AFF_PAGES_GIVE ) && count( TRX_ADDONS_AFF_PAGES_GIVE ) > 0 ? TRX_ADDONS_AFF_PAGES_GIVE : false,
					'mask' => $mask,	// if a link href contains this substring - replace it
					'link' => $url,		// new link to replace
				);
			}
		}
		return $vars;
	}
}
