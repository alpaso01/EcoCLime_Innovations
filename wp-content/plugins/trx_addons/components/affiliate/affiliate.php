<?php
/**
 * Affiliate links for the ThemeREX Addons
 *
 * @package ThemeREX Addons
 * @since v2.30.2
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}

if ( ! defined( 'TRX_ADDONS_PLUGIN_AFFILIATE' ) ) {
	define( 'TRX_ADDONS_PLUGIN_AFFILIATE', TRX_ADDONS_PLUGIN_COMPONENTS . '/affiliate' );
}

if ( ! function_exists( 'trx_addons_affiliate_load' ) ) {
	add_action( 'after_setup_theme', 'trx_addons_affiliate_load', 2 );
	/**
	 * Load affiliate links supports
	 * 
	 * @hooked after_setup_theme, 2
	 */
	function trx_addons_affiliate_load() {
		static $loaded = false;
		if ( $loaded ) {
			return;
		}
		$loaded = true;

		// Elementor
		if ( trx_addons_exists_elementor() ) {
			require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_AFFILIATE . '/elementor.php';
		}

		// Revslider
		if ( trx_addons_exists_revslider() ) {
			require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_AFFILIATE . '/revslider.php';
		}

		// WP Rocket
		if ( defined( 'WP_ROCKET_VERSION' ) || function_exists( 'rocket_init' ) ) {
			require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_AFFILIATE . '/wp-rocket.php';
		}

		// SolidWP - Better WP Security
		if ( function_exists( 'itsec_load_textdomain' ) ) {
			require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_AFFILIATE . '/better-wp-security.php';
		}

		// Kadence Blocks
		if ( function_exists( 'kadence_blocks_init' ) ) {
			require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_AFFILIATE . '/kadence-blocks.php';
		}

		// Give WP
		if ( class_exists( 'Give' ) ) {
			require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_AFFILIATE . '/give.php';
		}

		// LearnDash
		if ( function_exists( 'learndash_deactivated' ) ) {
			require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_AFFILIATE . '/learndash.php';
		}

		// The Events Calendar
		if ( class_exists( 'Tribe__Events__Main' ) ) {
			require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_AFFILIATE . '/the-events-calendar.php';
		}

		// Restrict Content Pro
		if ( class_exists( 'RC_Requirements_Check' ) ) {
			require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_AFFILIATE . '/restrict-content.php';
		}

		// Orderable
		if ( class_exists( 'Orderable' ) ) {
			require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_AFFILIATE . '/orderable.php';
		}

		// AI Engine
		if ( defined( 'MWAI_VERSION' ) ) {
			require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_AFFILIATE . '/ai-engine.php';
		}

		// NitroPack
		if ( function_exists( 'nitropack_menu' ) ) {
			require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_AFFILIATE . '/nitropack.php';
		}

		// WordFence
		if ( class_exists( 'wordfence' ) ) {
			require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_AFFILIATE . '/wordfence.php';
		}

		// MetForm
		if ( class_exists( 'MetForm\Plugin' ) ) {
			require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_AFFILIATE . '/metform.php';
		}
	}
}
