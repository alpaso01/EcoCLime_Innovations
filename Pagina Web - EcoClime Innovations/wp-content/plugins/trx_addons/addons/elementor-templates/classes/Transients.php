<?php
/**
 * Class provides access to transients.
 *
 * @package ThemeREX Addons
 * @since v2.30.0
 */

namespace TrxAddons\ElementorTemplates;

/**
 * Class providing access to transients.
 */
final class Transients extends Base {

	private $prefix = 'trx_addons_elementor_';

	/**
	 * Returns the name of the transient with the prefix if not already present.
	 * 
	 * @param string $transient  Transient name.
	 * 
	 * @return string  Transient name with the prefix added (if not already present)
	 */
	public function transient_name( $transient ) {
		return ( strpos( $transient, $this->prefix ) !== 0 ? $this->prefix : '' ) . $transient;
	}

	/**
	 * Gets the value of the transient.
	 *
	 * @param string $transient  Transient name.
	 * 
	 * @return mixed  Value set for the transient, or false if not set.
	 */
	public function get( $transient ) {
		return get_transient( $this->transient_name( $transient ) );
	}

	/**
	 * Sets the value for a transient.
	 *
	 * @param string $transient  Transient name.
	 * @param mixed  $value      Transient value. Must be serializable if non-scalar.
	 * @param int    $expiration Optional. Time until expiration in seconds. Default 0 (no expiration).
	 * 
	 * @return bool True on success, false on failure.
	 */
	public function set( $transient, $value, $expiration = 0 ) {
		return set_transient( $this->transient_name( $transient ), $value, $expiration );
	}

	/**
	 * Deletes the given transient.
	 *
	 * @param string $transient  Transient name.
	 * 
	 * @return bool True on success, false on failure.
	 */
	public function delete( $transient ) {
		return delete_transient( $this->transient_name( $transient ) );
	}
}

