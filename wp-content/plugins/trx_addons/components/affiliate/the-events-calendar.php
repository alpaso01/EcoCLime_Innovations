<?php
/**
 * Affiliate links: The Events Calendar
 *
 * @package ThemeREX Addons
 * @since v2.30.2
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}

// An array with links to replace all redirections to the plugin's site with affiliate links
define( 'TRX_ADDONS_AFF_LINKS_THE_EVENTS_CALENDAR', array(
	'//theeventscalendar.com/products/wordpress-events-calendar' => 'https://stellarwp.pxf.io/Mmr4OK',
	'//theeventscalendar.com/knowledgebase' => 'https://stellarwp.pxf.io/9gK1k3',
	'//evnt.is/' => 'https://stellarwp.pxf.io/Mmr4OK',
	'//theeventscalendar.com/wordpress-event-aggregator' => 'https://stellarwp.pxf.io/oqr2Mm',
	'//theeventscalendar.com/support/#contact' => 'https://stellarwp.pxf.io/zNJQM6',
) );

// An array with pages to replace all redirections to the plugin's site with affiliate links
define( 'TRX_ADDONS_AFF_PAGES_THE_EVENTS_CALENDAR', array(
	'post_type=tribe_events',
	'plugins.php'
) );

if ( ! function_exists( 'trx_addons_the_events_calendar_change_url_in_js' ) ) {
	// add_filter( 'trx_addons_filter_localize_script', 'trx_addons_the_events_calendar_change_url_in_js' );
	add_filter( 'trx_addons_filter_localize_script_admin', 'trx_addons_the_events_calendar_change_url_in_js' );
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
	function trx_addons_the_events_calendar_change_url_in_js( $vars ) {
		if ( ! isset( $vars['add_to_links_url'] ) ) {
			$vars['add_to_links_url'] = array();
		}
		if ( is_array( TRX_ADDONS_AFF_LINKS_THE_EVENTS_CALENDAR ) ) {
			foreach( TRX_ADDONS_AFF_LINKS_THE_EVENTS_CALENDAR as $mask => $url ) {
				$vars['add_to_links_url'][] = array(
					'slug' => 'the-events-calendar',
					'page' => defined( 'TRX_ADDONS_AFF_PAGES_THE_EVENTS_CALENDAR' ) && is_array( TRX_ADDONS_AFF_PAGES_THE_EVENTS_CALENDAR ) && count( TRX_ADDONS_AFF_PAGES_THE_EVENTS_CALENDAR ) > 0 ? TRX_ADDONS_AFF_PAGES_THE_EVENTS_CALENDAR : false,
					'mask' => $mask,	// if a link href contains this substring - replace it
					'link' => $url,		// new link to replace
				);
			}
		}
		return $vars;
	}
}
