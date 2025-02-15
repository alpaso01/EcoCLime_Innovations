<?php
/**
 * Affiliate links: WpMet
 *
 * @package ThemeREX Addons
 * @since v2.30.2
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}

// An array with links to replace all redirections to the ThemePunch site with affiliate links
define( 'TRX_ADDONS_AFF_LINKS_WPMET', array(
	// Support
	'//wpmet.com/support-ticket' => 'https://wpmet.com/support-ticket/?rui=1071',
	// Pricing
	'//wpmet.com/metform-pricing' => 'https://wpmet.com/metform-pricing/?rui=1071',
	// Documentation
	'//wpmet.com/doc/metform' => 'https://wpmet.com/doc/metform/?rui=1071',
	// Support ticket
	'//wpmet.com/support-ticket-form' => 'https://wpmet.com/support-ticket-form/?rui=1071',
	// Roadmap
	'//wpmet.com/plugin/metform/roadmaps' => 'https://wpmet.com/plugin/metform/roadmaps/?rui=1071',
	// Roadmap (ideas)
	'//wpmet.com/plugin/metform/roadmaps/#ideas' => 'https://wpmet.com/plugin/metform/roadmaps/?rui=1071#ideas',
	// Plugins
	'//wpmet.com/plugin/metform' => 'https://wpmet.com/plugin/metform/?rui=1071',
	// FB Group
	'//wpmet.com/fb-group' => 'https://wpmet.com/fb-group/?rui=1071',
) );

// An array with pages to replace all redirections to the plugin's site with affiliate links
define( 'TRX_ADDONS_AFF_PAGES_WPMET', array(
    'edit.php?post_type=metform-form',
    'edit.php?post_type=metform-entry',
    'admin.php?page=metform-menu-settings',
    'admin.php?page=metform_get_help',
    'admin.php?page=metform_wpmet_plugins',
	'index.php',
	'plugins.php',
) );

// Variables to replace PRO links in admin_menu
define( 'TRX_ADDONS_AFF_LINKS_WPMET_SUBMENU_KEY', 'metform-menu' );
define( 'TRX_ADDONS_AFF_LINKS_WPMET_SUBMENU_ITEM_KEY', '//wpmet.com/metform-pricing' );
define( 'TRX_ADDONS_AFF_LINKS_WPMET_SUBMENU_ITEM_REPLACE_URL', 'https://wpmet.com/metform-pricing/?rui=1071' );

// Variables to replace PRO links in elementor
define( 'TRX_ADDONS_AFF_LINKS_WPMET_ELEMENTOR_CONFIG_FIND', '//wpmet.com/doc/form-widgets/' );
define( 'TRX_ADDONS_AFF_LINKS_WPMET_ELEMENTOR_CONFIG_REPLACER', '//wpmet.com/doc/form-widgets/?rui=1071' );

if ( ! function_exists( 'trx_addons_wpmet_change_gopro_url_admin_menu' ) ) {
    add_action( 'admin_menu', 'trx_addons_wpmet_change_gopro_url_admin_menu', 100000 );
    /**
     * Prepare variables to change "Go Premium" link to our affiliate link in admin_menu
	 * 
	 * @hooked admin_menu
     * 
     * @return [type]
     */
    function trx_addons_wpmet_change_gopro_url_admin_menu () {
        global $submenu;

        if ( isset( $submenu[ TRX_ADDONS_AFF_LINKS_WPMET_SUBMENU_KEY ] ) && is_array( $submenu[ TRX_ADDONS_AFF_LINKS_WPMET_SUBMENU_KEY ] ) ) {
            foreach ( $submenu[TRX_ADDONS_AFF_LINKS_WPMET_SUBMENU_KEY] as $key_sub_menu_item => $sub_menu_item ) {
                if ( ! empty( $sub_menu_item[2] ) && stripos( $sub_menu_item[2], TRX_ADDONS_AFF_LINKS_WPMET_SUBMENU_ITEM_KEY ) !== false ) {
                    $submenu[ TRX_ADDONS_AFF_LINKS_WPMET_SUBMENU_KEY ][ $key_sub_menu_item ][2] = TRX_ADDONS_AFF_LINKS_WPMET_SUBMENU_ITEM_REPLACE_URL;
                }
            }
        }
    }
}

if ( ! function_exists( 'trx_addons_wpmet_change_gopro_url_admin_notices_start' ) && ! function_exists( 'trx_addons_wpmet_change_gopro_url_admin_notices_end' ) ) {
    add_action( 'admin_notices', 'trx_addons_wpmet_change_gopro_url_admin_notices_start', 0 );
    add_action( 'admin_notices', 'trx_addons_wpmet_change_gopro_url_admin_notices_end', 100000 );
    /**
     * Prepare variables to change "Go Premium" link to our affiliate link in admin_notices
	 * 
	 * @hooked admin_notices
     * 
     * @return [type]
     */
    function trx_addons_wpmet_change_gopro_url_admin_notices_start () {
        ob_start();
    }
    function trx_addons_wpmet_change_gopro_url_admin_notices_end () {
        $content = ob_get_clean();

        if ( preg_match_all( '/href="([^"]*)"/', $content, $matches ) && is_array( TRX_ADDONS_AFF_LINKS_WPMET ) ) {
            foreach ( $matches[1] as $match ) {
                foreach( TRX_ADDONS_AFF_LINKS_WPMET as $key_link => $link ) {
                    if ( stripos( $match, $key_link ) !== false ) {
                        $content = str_replace( $match, $link, $content );
                    }
                }
            }
        }

        echo $content;
    }
}

if ( ! function_exists( 'trx_addons_wpmet_change_gopro_url_in_config' ) ) {
    add_filter( 'elementor/editor/localize_settings', 'trx_addons_wpmet_change_gopro_url_in_config' );
    /**
     * Replace all go_pro URLs to new link in the Elementor config
     * 
     * @hooked elementor/editor/localize_settings
     * 
     * @param array $config  An array of Elementor config
     * 
     * @return array  	A modified array of Elementor config
     */
    function trx_addons_wpmet_change_gopro_url_in_config( $config ) {
        if ( is_array( $config ) ) {
			foreach( $config as $k => $v ) {
				if ( is_array( $v ) ) {
					$config[ $k ] = trx_addons_wpmet_change_gopro_url_in_config( $v );
				} else if ( is_string( $v ) 
                        && $k === 'help_url' 
                        && stripos( $v, TRX_ADDONS_AFF_LINKS_WPMET_ELEMENTOR_CONFIG_FIND ) !== false
                ) {
					$config[ $k ] = str_replace( TRX_ADDONS_AFF_LINKS_WPMET_ELEMENTOR_CONFIG_FIND, TRX_ADDONS_AFF_LINKS_WPMET_ELEMENTOR_CONFIG_REPLACER, $v );
				}
			}
		}
        return $config;
    }
}

if ( ! function_exists( 'trx_addons_wpmet_change_gopro_url_in_js' ) ) {
	// add_filter( 'trx_addons_filter_localize_script', 'trx_addons_wpmet_change_gopro_url_in_js' );
	add_filter( 'trx_addons_filter_localize_script_admin', 'trx_addons_wpmet_change_gopro_url_in_js' );
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
	function trx_addons_wpmet_change_gopro_url_in_js( $vars ) {
        if ( ! isset( $vars['add_to_links_url'] ) ) {
            $vars['add_to_links_url'] = array();
        }

        if ( is_array( TRX_ADDONS_AFF_LINKS_WPMET ) ) {
            foreach( TRX_ADDONS_AFF_LINKS_WPMET as $mask => $url ) {
                $vars['add_to_links_url'][] = array(
                    'slug' => 'wpmet',
                    'page' => defined( 'TRX_ADDONS_AFF_PAGES_WPMET' ) && is_array( TRX_ADDONS_AFF_PAGES_WPMET ) && count( TRX_ADDONS_AFF_PAGES_WPMET ) > 0 ? TRX_ADDONS_AFF_PAGES_WPMET : false,
                    'mask' => $mask,
                    'link' => $url
                );
            }
        }
		return $vars;
	}
}