<?php
/**
 * Affiliate links: WP Rocket
 *
 * @package ThemeREX Addons
 * @since v2.30.2
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}

// An array with links to replace all redirections to the plugin's site with affiliate links
define( 'TRX_ADDONS_AFF_LINKS_WP_ROCKET', array(
	'wp-rocket.me' => 'https://shareasale.com/r.cfm?b=1075949&u=4308345&m=74778&afftrack=',
) );

if ( ! function_exists( 'trx_addons_wp_rocket_change_url_in_js' ) ) {
	// add_filter( 'trx_addons_filter_localize_script', 'trx_addons_wp_rocket_change_url_in_js' );
	add_filter( 'trx_addons_filter_localize_script_admin', 'trx_addons_wp_rocket_change_url_in_js' );
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
	function trx_addons_wp_rocket_change_url_in_js( $vars ) {
		if ( ! isset( $vars['add_to_links_url'] ) ) {
			$vars['add_to_links_url'] = array();
		}
		if ( is_array( TRX_ADDONS_AFF_LINKS_WP_ROCKET ) ) {
			foreach( TRX_ADDONS_AFF_LINKS_WP_ROCKET as $mask => $url ) {
				$vars['add_to_links_url'][] = array(
					'slug' => 'wp-rocket',
					'mask' => $mask,	// if a link href contains this substring - replace it
					'link' => $url,		// new link to replace
					'args' => array(
						'urllink' => '@href'	// a name of url-parameter to add to the link with the original URL
					)
				);
			}
		}
		return $vars;
	}
}
