<?php
/**
 * Required plugins
 *
 * @package PLANTY
 * @since PLANTY 1.76.0
 */

// THEME-SUPPORTED PLUGINS
// If plugin not need - remove its settings from next array
//----------------------------------------------------------
$planty_theme_required_plugins_groups = array(
	'core'          => esc_html__( 'Core', 'planty' ),
	'page_builders' => esc_html__( 'Page Builders', 'planty' ),
	'ecommerce'     => esc_html__( 'E-Commerce & Donations', 'planty' ),
	'socials'       => esc_html__( 'Socials and Communities', 'planty' ),
	'events'        => esc_html__( 'Events and Appointments', 'planty' ),
	'content'       => esc_html__( 'Content', 'planty' ),
	'other'         => esc_html__( 'Other', 'planty' ),
);
$planty_theme_required_plugins        = array(
	'trx_addons'                 => array(
		'title'       => esc_html__( 'ThemeREX Addons', 'planty' ),
		'description' => esc_html__( "Will allow you to install recommended plugins, demo content, and improve the theme's functionality overall with multiple theme options", 'planty' ),
		'required'    => true,
		'logo'        => 'trx_addons.png',
		'group'       => $planty_theme_required_plugins_groups['core'],
	),
	'elementor'                  => array(
		'title'       => esc_html__( 'Elementor', 'planty' ),
		'description' => esc_html__( "Is a beautiful PageBuilder, even the free version of which allows you to create great pages using a variety of modules.", 'planty' ),
		'required'    => false,
		'logo'        => 'elementor.png',
		'group'       => $planty_theme_required_plugins_groups['page_builders'],
	),
	'gutenberg'                  => array(
		'title'       => esc_html__( 'Gutenberg', 'planty' ),
		'description' => esc_html__( "It's a posts editor coming in place of the classic TinyMCE. Can be installed and used in parallel with Elementor", 'planty' ),
		'required'    => false,
		'install'     => false,          // Do not offer installation of the plugin in the Theme Dashboard and TGMPA
		'logo'        => 'gutenberg.png',
		'group'       => $planty_theme_required_plugins_groups['page_builders'],
	),
	'js_composer'                => array(
		'title'       => esc_html__( 'WPBakery PageBuilder', 'planty' ),
		'description' => esc_html__( "Popular PageBuilder which allows you to create excellent pages", 'planty' ),
		'required'    => false,
		'install'     => false,          // Do not offer installation of the plugin in the Theme Dashboard and TGMPA
		'logo'        => 'js_composer.jpg',
		'group'       => $planty_theme_required_plugins_groups['page_builders'],
	),
	'woocommerce'                => array(
		'title'       => esc_html__( 'WooCommerce', 'planty' ),
		'description' => esc_html__( "Connect the store to your website and start selling now", 'planty' ),
		'required'    => false,
                'install'     => false,
		'logo'        => 'woocommerce.png',
		'group'       => $planty_theme_required_plugins_groups['ecommerce'],
	),
	'elegro-payment'             => array(
		'title'       => esc_html__( 'Elegro Crypto Payment', 'planty' ),
		'description' => esc_html__( "Extends WooCommerce Payment Gateways with an elegro Crypto Payment", 'planty' ),
		'required'    => false,
                'install'     => false,
		'logo'        => 'elegro-payment.png',
		'group'       => $planty_theme_required_plugins_groups['ecommerce'],
	),
	'instagram-feed'             => array(
		'title'       => esc_html__( 'Instagram Feed', 'planty' ),
		'description' => esc_html__( "Displays the latest photos from your profile on Instagram", 'planty' ),
		'required'    => false,
		'logo'        => 'instagram-feed.png',
		'group'       => $planty_theme_required_plugins_groups['socials'],
	),
	'mailchimp-for-wp'           => array(
		'title'       => esc_html__( 'MailChimp for WP', 'planty' ),
		'description' => esc_html__( "Allows visitors to subscribe to newsletters", 'planty' ),
		'required'    => false,
		'logo'        => 'mailchimp-for-wp.png',
		'group'       => $planty_theme_required_plugins_groups['socials'],
	),
	'booked'                     => array(
		'title'       => esc_html__( 'Booked Appointments', 'planty' ),
		'description' => '',
		'required'    => false,
                'install'     => false,
		'logo'        => 'booked.png',
		'group'       => $planty_theme_required_plugins_groups['events'],
	),
	'quickcal'                     => array(
		'title'       => esc_html__( 'QuickCal', 'planty' ),
		'description' => '',
		'required'    => false,
                'install'     => false,
		'logo'        => 'quickcal.png',
		'group'       => $planty_theme_required_plugins_groups['events'],
	),
	'the-events-calendar'        => array(
		'title'       => esc_html__( 'The Events Calendar', 'planty' ),
		'description' => '',
		'required'    => false,
                'install'     => false,
		'logo'        => 'the-events-calendar.png',
		'group'       => $planty_theme_required_plugins_groups['events'],
	),
	'contact-form-7'             => array(
		'title'       => esc_html__( 'Contact Form 7', 'planty' ),
		'description' => esc_html__( "CF7 allows you to create an unlimited number of contact forms", 'planty' ),
		'required'    => false,
		'logo'        => 'contact-form-7.png',
		'group'       => $planty_theme_required_plugins_groups['content'],
	),

	'latepoint'                  => array(
		'title'       => esc_html__( 'LatePoint', 'planty' ),
		'description' => '',
		'required'    => false,
                'install'     => false,
		'logo'        => planty_get_file_url( 'plugins/latepoint/latepoint.png' ),
		'group'       => $planty_theme_required_plugins_groups['events'],
	),
	'advanced-popups'                  => array(
		'title'       => esc_html__( 'Advanced Popups', 'planty' ),
		'description' => '',
		'required'    => false,
		'logo'        => planty_get_file_url( 'plugins/advanced-popups/advanced-popups.jpg' ),
		'group'       => $planty_theme_required_plugins_groups['content'],
	),
	'devvn-image-hotspot'                  => array(
		'title'       => esc_html__( 'Image Hotspot by DevVN', 'planty' ),
		'description' => '',
		'required'    => false,
                'install'     => false,
		'logo'        => planty_get_file_url( 'plugins/devvn-image-hotspot/devvn-image-hotspot.png' ),
		'group'       => $planty_theme_required_plugins_groups['content'],
	),
	'ti-woocommerce-wishlist'                  => array(
		'title'       => esc_html__( 'TI WooCommerce Wishlist', 'planty' ),
		'description' => '',
		'required'    => false,
                'install'     => false,
		'logo'        => planty_get_file_url( 'plugins/ti-woocommerce-wishlist/ti-woocommerce-wishlist.png' ),
		'group'       => $planty_theme_required_plugins_groups['ecommerce'],
	),
	'woo-smart-quick-view'                  => array(
		'title'       => esc_html__( 'WPC Smart Quick View for WooCommerce', 'planty' ),
		'description' => '',
		'required'    => false,
                'install'     => false,
		'logo'        => planty_get_file_url( 'plugins/woo-smart-quick-view/woo-smart-quick-view.png' ),
		'group'       => $planty_theme_required_plugins_groups['ecommerce'],
	),
	'twenty20'                  => array(
		'title'       => esc_html__( 'Twenty20 Image Before-After', 'planty' ),
		'description' => '',
		'required'    => false,
        	'install'     => false,
		'logo'        => planty_get_file_url( 'plugins/twenty20/twenty20.png' ),
		'group'       => $planty_theme_required_plugins_groups['content'],
	),
	'essential-grid'             => array(
		'title'       => esc_html__( 'Essential Grid', 'planty' ),
		'description' => '',
		'required'    => false,
		'install'     => false,
		'logo'        => 'essential-grid.png',
		'group'       => $planty_theme_required_plugins_groups['content'],
	),
	'revslider'                  => array(
		'title'       => esc_html__( 'Revolution Slider', 'planty' ),
		'description' => '',
		'required'    => false,
        	'install'     => false,
		'logo'        => 'revslider.png',
		'group'       => $planty_theme_required_plugins_groups['content'],
	),
	'sitepress-multilingual-cms' => array(
		'title'       => esc_html__( 'WPML - Sitepress Multilingual CMS', 'planty' ),
		'description' => esc_html__( "Allows you to make your website multilingual", 'planty' ),
		'required'    => false,
		'install'     => false,      // Do not offer installation of the plugin in the Theme Dashboard and TGMPA
		'logo'        => 'sitepress-multilingual-cms.png',
		'group'       => $planty_theme_required_plugins_groups['content'],
	),
	'wp-gdpr-compliance'         => array(
		'title'       => esc_html__( 'Cookie Information', 'planty' ),
		'description' => esc_html__( "Allow visitors to decide for themselves what personal data they want to store on your site", 'planty' ),
		'required'    => false,
		'logo'        => 'wp-gdpr-compliance.png',
		'group'       => $planty_theme_required_plugins_groups['other'],
	),
	'trx_updater'                => array(
		'title'       => esc_html__( 'ThemeREX Updater', 'planty' ),
		'description' => esc_html__( "Update theme and theme-specific plugins from developer's upgrade server.", 'planty' ),
		'required'    => false,
		'logo'        => 'trx_updater.png',
		'group'       => $planty_theme_required_plugins_groups['other'],
	),
);

if ( PLANTY_THEME_FREE ) {
	unset( $planty_theme_required_plugins['js_composer'] );
	unset( $planty_theme_required_plugins['booked'] );
	unset( $planty_theme_required_plugins['quickcal'] );
	unset( $planty_theme_required_plugins['the-events-calendar'] );
	unset( $planty_theme_required_plugins['calculated-fields-form'] );
	unset( $planty_theme_required_plugins['essential-grid'] );
	unset( $planty_theme_required_plugins['revslider'] );
	unset( $planty_theme_required_plugins['sitepress-multilingual-cms'] );
	unset( $planty_theme_required_plugins['trx_updater'] );
	unset( $planty_theme_required_plugins['trx_popup'] );
}

// Add plugins list to the global storage
planty_storage_set( 'required_plugins', $planty_theme_required_plugins );
