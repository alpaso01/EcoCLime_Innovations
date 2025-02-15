<?php
/**
 * Affiliate links: Kadence Blocks
 *
 * @package ThemeREX Addons
 * @since v2.30.2
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}

// An array with links to replace all redirections to the plugin's site with affiliate links
define( 'TRX_ADDONS_AFF_LINKS_KADENCE_BLOCKS', array(
	'//www.kadencewp.com/blocks-pro' => 'https://stellarwp.pxf.io/5g4eG1',
	'//www.kadencewp.com/kadence-blocks' => 'https://stellarwp.pxf.io/5g4eG1',
	'//www.kadencewp.com/kadence-blocks/pro' => 'https://stellarwp.pxf.io/5g4eG1',
	'//www.kadencewp.com/account-auth' => 'https://stellarwp.pxf.io/6e4aKV',
) );

// An array with pages to replace all redirections to the plugin's site with affiliate links
define( 'TRX_ADDONS_AFF_PAGES_KADENCE_BLOCKS', array(
	'admin.php?page=kadence-blocks',
	'wp-admin/post.php',
	'wp-admin/post-new.php',
	'plugins.php'
) );

if ( ! function_exists( 'trx_addons_kadence_blocks_change_url_in_js' ) ) {
	// add_filter( 'trx_addons_filter_localize_script', 'trx_addons_kadence_blocks_change_url_in_js' );
	add_filter( 'trx_addons_filter_localize_script_admin', 'trx_addons_kadence_blocks_change_url_in_js' );
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
	function trx_addons_kadence_blocks_change_url_in_js( $vars ) {
		if ( ! isset( $vars['add_to_links_url'] ) ) {
			$vars['add_to_links_url'] = array();
		}
		if ( is_array( TRX_ADDONS_AFF_LINKS_KADENCE_BLOCKS ) ) {
			foreach( TRX_ADDONS_AFF_LINKS_KADENCE_BLOCKS as $mask => $url ) {
				$vars['add_to_links_url'][] = array(
					'slug' => 'kadence-blocks',
					'page' => defined( 'TRX_ADDONS_AFF_PAGES_KADENCE_BLOCKS' ) && is_array( TRX_ADDONS_AFF_PAGES_KADENCE_BLOCKS ) && count( TRX_ADDONS_AFF_PAGES_KADENCE_BLOCKS ) > 0 ? TRX_ADDONS_AFF_PAGES_KADENCE_BLOCKS : false,
					'mask' => $mask,	// if a link href contains this substring - replace it
					'link' => $url,		// new link to replace
				);
			}
		}
		return $vars;
	}
}
