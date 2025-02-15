<?php
/**
 * Elementor Widgets - adds super customizable widgets for the Elementor
 *
 * @addon elementor-widgets
 * @version 1.0
 *
 * @package ThemeREX Addons
 * @since v2.30
 */

namespace TrxAddons\ElementorWidgets;

// Add templates library support for activated theme only
if ( trx_addons_exists_elementor() && trx_addons_is_theme_activated() ) {
	// Register autoloader for the addon's classes
	require_once TRX_ADDONS_PLUGIN_DIR_CLASSES . 'Autoloader.php';
	new \TrxAddons\Core\Autoloader( array(
		'path' => dirname( __FILE__ ),
		'namespace' => __NAMESPACE__,
		'classes_map' => array(
			'TrxAddons\\Core\\Singleton' => '/includes/classes/Singleton.php',	// Absolute path from the root of the plugin.
		),
	) );

	// Include the main addon class
	new ElementorWidgets();
}