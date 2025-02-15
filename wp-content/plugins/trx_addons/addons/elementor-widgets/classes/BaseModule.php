<?php
/**
 * Base Module class
 *
 * @package ThemeREX Addons
 * @since v2.30.0
 */

namespace TrxAddons\ElementorWidgets;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Base control
 */
abstract class BaseModule {

	protected $module_class = '';

	private $components = array();

	/**
	 * Constructor.
	 *
	 * Initializing the control base class.
	 */
	public function __construct() {
		$class = explode( '\\', get_class( $this ) );
		$this->module_class = end( $class );
	}

	/**
	 * Get the name of the module
	 *
	 * @return string  The name of the module.
	 */
	abstract public function get_name();

	/**
	 * Add new component to the current module.
	 *
	 * @param string $id       Component ID.
	 * @param mixed  $instance An instance of the component.
	 */
	public function add_component( $id, $instance ) {
		$this->components[ $id ] = $instance;
	}

	/**
	 * Retrieve the module components.
	 * 
	 * @return array  An array of the module components.
	 */
	public function get_components() {
		return $this->components;
	}

	/**
	 * Retrieve the module component.
	 *
	 * @param string $id Component ID.
	 *
	 * @return mixed An instance of the component, or `false` if the component doesn't exist.
	 */
	public function get_component( $id ) {
		if ( isset( $this->components[ $id ] ) ) {
			return $this->components[ $id ];
		}
		return false;
	}

}
