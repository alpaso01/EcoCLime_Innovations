<?php
/**
 * Elementor Templates - adds support for the Elementor templates and global colors and fonts
 *
 * @addon elementor-templates
 * @version 1.0
 *
 * @package ThemeREX Addons
 * @since v2.30
 */

namespace TrxAddons\ElementorTemplates;

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
	ElementorTemplates::instance();
}