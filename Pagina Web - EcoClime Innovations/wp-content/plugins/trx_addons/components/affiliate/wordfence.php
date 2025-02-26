<?php
/**
 * Affiliate links: WordFence
 *
 * @package ThemeREX Addons
 * @since v2.30.2
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}

// An array with links to replace all redirections to the ThemePunch site with affiliate links
define( 'TRX_ADDONS_AFF_LINKS_WORDFENCE', array(
	// Registration
	'//www.wordfence.com/plugin/registration' => 'https://www.wordfence.com/r/3ccb3ed3c485ee74/products/pricing',
	// Pricing
	'//www.wordfence.com/gnl1menuUpgradePremium/products/pricing' => 'https://www.wordfence.com/r/3ccb3ed3c485ee74/products/pricing',
	// SingUp
	'//www.wordfence.com/gnl1supportUpgrade/wordfence-signup' => 'https://www.wordfence.com/r/3ccb3ed3c485ee74/products/pricing',
	// Help
	'//www.wordfence.com/help' => 'https://www.wordfence.com/r/12853029782c3986/help',
	// Support
	'//wordpress.org/support/plugin/wordfence' => 'https://www.wordfence.com/r/7819b5060137b57e/support/plugin/wordfence',
	// Free
	'//www.wordfence.com/products/wordfence-free' => 'https://www.wordfence.com/r/7253af51ac9e9c77/products/wordfence-free',
) );

// An array with pages to replace all redirections to the plugin's site with affiliate links
define( 'TRX_ADDONS_AFF_PAGES_WORDFENCE', array(
	'admin.php?page=Wordfence',
	'admin.php?page=WordfenceWAF',
	'admin.php?page=WordfenceScan',
	'admin.php?page=WordfenceTools',
	'admin.php?page=WFLS',
	'admin.php?page=WordfenceOptions',
	'admin.php?page=WordfenceSupport',
	'admin.php?page=WordfenceInstall',
	'plugins.php'
) );

// Variables to replace PRO links in admin_menu
define( 'TRX_ADDONS_AFF_LINKS_WORDFENCE_SUBMENU_KEY', 'Wordfence' );
define( 'TRX_ADDONS_AFF_LINKS_WORDFENCE_SUBMENU_ITEM_KEY', 'WordfenceUpgradeToPremium' );
define( 'TRX_ADDONS_AFF_LINKS_WORDFENCE_SUBMENU_ITEM_REPLACE_URL', 'https://www.wordfence.com/r/3ccb3ed3c485ee74/products/pricing' );

if ( ! function_exists( 'trx_addons_wordfence_change_gopro_url_admin_menu' ) ) {
    add_action( 'admin_menu', 'trx_addons_wordfence_change_gopro_url_admin_menu', 1000 );
    /**
     * Prepare variables to change "Go Premium" link to our affiliate link in admin_menu
	 * 
	 * @hooked admin_menu
     * 
     * @return [type]
     */
    function trx_addons_wordfence_change_gopro_url_admin_menu () {
        global $submenu;

        if ( isset( $submenu[ TRX_ADDONS_AFF_LINKS_WORDFENCE_SUBMENU_KEY ] ) && is_array( $submenu[ TRX_ADDONS_AFF_LINKS_WORDFENCE_SUBMENU_KEY ] ) ) {
            foreach ( $submenu[TRX_ADDONS_AFF_LINKS_WORDFENCE_SUBMENU_KEY] as $key_sub_menu_item => $sub_menu_item ) {
                if ( ! empty( $sub_menu_item[2] ) && $sub_menu_item[2] == TRX_ADDONS_AFF_LINKS_WORDFENCE_SUBMENU_ITEM_KEY ) {
                    $submenu[ TRX_ADDONS_AFF_LINKS_WORDFENCE_SUBMENU_KEY ][ $key_sub_menu_item ][2] = TRX_ADDONS_AFF_LINKS_WORDFENCE_SUBMENU_ITEM_REPLACE_URL;
                }
            }
        }
    }
}

if ( ! function_exists( 'trx_addons_wordfence_change_gopro_url_in_js' ) ) {
	// add_filter( 'trx_addons_filter_localize_script', 'trx_addons_wordfence_change_gopro_url_in_js' );
	add_filter( 'trx_addons_filter_localize_script_admin', 'trx_addons_wordfence_change_gopro_url_in_js' );
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
	function trx_addons_wordfence_change_gopro_url_in_js( $vars ) {
        if ( ! isset( $vars['add_to_links_url'] ) ) {
            $vars['add_to_links_url'] = array();
        }
        if ( is_array( TRX_ADDONS_AFF_LINKS_WORDFENCE ) ) {
            foreach( TRX_ADDONS_AFF_LINKS_WORDFENCE as $mask => $url ) {
                $vars['add_to_links_url'][] = array(
                    'slug' => 'wordfence',
                    'page' => defined( 'TRX_ADDONS_AFF_PAGES_WORDFENCE' ) && is_array( TRX_ADDONS_AFF_PAGES_WORDFENCE ) && count( TRX_ADDONS_AFF_PAGES_WORDFENCE ) > 0 ? TRX_ADDONS_AFF_PAGES_WORDFENCE : false,
                    'mask' => $mask,
                    'link' => $url
                );
            }
        }
		return $vars;
	}
}