<?php
/**
 * Class provides access to options.
 *
 * @package ThemeREX Addons
 * @since v2.30.0
 */

namespace TrxAddons\ElementorTemplates;

/**
 * Class providing access to options.
 */
final class Options extends Base {

	private $prefix = 'trx_addons_elementor_';

	/**
	 * Returns the name of the option with the prefix if not already present.
	 * 
	 * @param string $option  Option name.
	 * 
	 * @return string  Option name with the prefix added (if not already present)
	 */
	public function option_name( $option ) {
		return ( strpos( $option, $this->prefix ) !== 0 ? $this->prefix : '' ) . $option;
	}

	/**
	 * Gets the value of the option.
	 *
	 * @param string $option  Option name.
	 * 
	 * @return mixed  Value set for the option, or false if not set.
	 */
	public function get( $option ) {
		return get_option( $this->option_name( $option ) );
	}

	/**
	 * Sets the value for a option.
	 *
	 * @param string $option  Option name.
	 * @param mixed  $value   Option value. Must be serializable if non-scalar.
	 * 
	 * @return bool True on success, false on failure.
	 */
	public function set( $option, $value ) {
		return update_option( $this->option_name( $option ), $value );
	}

	/**
	 * Deletes the given option.
	 *
	 * @param string $option  Option name.
	 * 
	 * @return bool True on success, false on failure.
	 */
	public function delete( $option ) {
		return delete_option( $this->option_name( $option ) );
	}
}

