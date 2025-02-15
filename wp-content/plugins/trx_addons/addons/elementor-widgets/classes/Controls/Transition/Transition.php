<?php
/**
 * Transition Control Module
 *
 * @package ThemeREX Addons
 * @since v2.30.0
 */

namespace TrxAddons\ElementorWidgets\Controls\Transition;

use TrxAddons\ElementorWidgets\BaseControlModule;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Transition Control module
 */
class Transition extends BaseControlModule {

	/**
	 * Get the name of the module
	 *
	 * @return string  The name of the module.
	 */
	public function get_name() {
		return 'transition-control';
	}

	/**
	 * Get the type of the control
	 *
	 * @return string  The type of the control: control | group
	 */
	public function get_type() {
		return 'group';
	}

}
