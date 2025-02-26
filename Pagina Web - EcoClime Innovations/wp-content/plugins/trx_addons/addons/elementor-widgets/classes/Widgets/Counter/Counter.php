<?php
/**
 * Counter Module
 *
 * @package ThemeREX Addons
 * @since v2.30.0
 */

namespace TrxAddons\ElementorWidgets\Widgets\Counter;

use TrxAddons\ElementorWidgets\BaseWidgetModule;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Counter module
 */
class Counter extends BaseWidgetModule {

	/**
	 * Constructor.
	 *
	 * Initializing the module base class.
	 */
	public function __construct() {
		parent::__construct();
		$this->assets = array(
			'css' => true,
			'js'  => true,
			'lib' => array(
				'js' => array(
					'elementor-waypoints' => true,
					'odometer' => array( 'src' => 'odometer/odometer.min.js' ),
				),
				'css' => array(
					'odometer' => array( 'src' => 'odometer/odometer-theme-default.css' ),
				)
			)
		);
	}

	/**
	 * Get the name of the module
	 *
	 * @return string  The name of the module.
	 */
	public function get_name() {
		return 'counter';
	}

}
