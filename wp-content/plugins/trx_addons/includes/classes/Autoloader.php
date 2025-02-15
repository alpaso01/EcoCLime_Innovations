<?php
namespace TrxAddons\Core;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * ThemeREX Addons autoloader handler class is responsible for loading the different
 * classes needed to run the plugin
 */
class Autoloader {

	/**
	 * Options
	 *
	 * @access private
	 *
	 * @var array  Options for autoloader. Must be send to constructor.
	 *             Keys:
	 * 		       - path: Default path for classes.
	 * 		       - namespace: Default namespace for classes.
	 * 		       - allowed_namespaces: List of allowed namespaces for the current instance of autoloader.
	 * 		       - folders_map: Folders with classes used by plugin.
	 * 		       - classes_map: Classes and their file names (if not in the default folder).
	 */
	private $options;

	/**
	 * Create and run autoloader.
	 *
	 * Register a function as `__autoload()` implementation.
	 *
	 * @access public
	 * 
	 * @param array $options Autoloader options.
	 */
	public function __construct( $options = array() ) {
		$options = array_merge(
			array(
				'path' => '',					// Absolute path to the root folder for the current instance of autoloader. For example: dirname( __FILE__ )
				'namespace' => '',				// Default namespace for the current instance of autoloader. For example: __NAMESPACE__
				'allowed_namespaces' => array(	// List of allowed namespaces for the current instance of autoloader.
					// For example:
					// 'ThemeRex\\Ai',
					// 'Markdown\\Parser'
				),
				'folders_map' => array(			// Map with a part of namespace and a folder name with classes for this part of namespace.
					// For example:
					// 'ThemeRex\\Ai' => 'vendors',
				),
				'classes_map' => array(			// Map with class name and file name (if a file is not in the default path subfolder).
					// For example:
					// 'TrxAddons\\Core\\Singleton' => '/includes/classes/Singleton.php',		// Absolute path from the root of the plugin.
					// 'TrxAddons\\Elementor\\Globals\\Fonts' => 'globals/Typography.php',		// Relative path from the root of the addon.
				),
			),
			$options
		);

		if ( empty( $options['path'] ) ) {
			throw new \Exception( __( 'Autoloader: empty path', 'trx_addons' ) );
		}
		if ( ! in_array( substr( $options['path'], -1 ), array( '/', '\\' ) ) ? DIRECTORY_SEPARATOR : '' ) {
			$options['path'] .= DIRECTORY_SEPARATOR;
		}

		if ( empty( $options['namespace'] ) ) {
			throw new \Exception( __( 'Autoloader: empty namespace', 'trx_addons' ) );
		}

		$options['allowed_namespaces'] = array_merge(
			array(
				$options['namespace'],
				'TrxAddons\\Core',
			),
			$options['allowed_namespaces']
		);

		$this->options = $options;

		spl_autoload_register( array( $this, 'autoload' ) );
	}

	/**
	 * Load class.
	 *
	 * For a given class name, require the class file.
	 *
	 * @access private
	 *
	 * @param string $relative_class_name Class name.
	 */
	private function load_class( $relative_class_name ) {
		if ( isset( $this->options['classes_map'][ $relative_class_name ] ) ) {			// Class name is alias: 'UpdateManager' -> 'core/update/manager'
			$filename = substr( $this->options['classes_map'][ $relative_class_name ], 0, 1 ) == '/'
						? TRX_ADDONS_PLUGIN_DIR . substr( $this->options['classes_map'][ $relative_class_name ], 1 )
						: $this->options['path'] . $this->options['classes_map'][ $relative_class_name ];
		} else {														// Class name contain relative path
			$base_folder = '';
			foreach ( $this->options['folders_map'] as $namespace => $folder ) {
				if ( strpos( $relative_class_name, $namespace . '\\' ) === 0 ) {
					$base_folder = $folder;
					break;
				}
			}
			$filename = preg_replace(
				array( '/_/', '/\\\/' ), 				// '/([a-z])([A-Z])/',
				array( '-', DIRECTORY_SEPARATOR ),		// '$1-$2',
				$relative_class_name
			);
			$filename = $this->options['path'] . ( ! empty( $base_folder ) ? $base_folder : 'classes' ) . DIRECTORY_SEPARATOR . $filename . '.php';
		}
		if ( is_readable( $filename ) ) {
			require_once $filename;
		}
	}

	/**
	 * Autoload.
	 *
	 * For a given class, check if it exist and load it.
	 *
	 * @access private
	 *
	 * @param string $class Class name.
	 */
	private function autoload( $class ) {
		$found = false;
		foreach ( $this->options['allowed_namespaces'] as $namespace ) {
			if ( strpos( $class, $namespace . '\\' ) === 0 ) {
				$found = true;
				break;
			}
		}
		if ( ! $found ) {
			return;
		}
		if ( strpos( $class, $this->options['namespace'] . '\\' ) === 0 ) {
			$relative_class_name = substr( $class, strlen( $this->options['namespace'] . '\\' ) );
			$final_class_name = $this->options['namespace'] . '\\' . $relative_class_name;
		} else {
			$relative_class_name = $class;
			$final_class_name = $class;
		}
		if ( ! class_exists( $final_class_name ) ) {
			$this->load_class( $relative_class_name );
		}
	}
}
