<?php
/**
 * Base class for the meta fields
 *
 * @package ThemeREX Addons
 * @since v2.30.0
 */

namespace TrxAddons\ElementorWidgets;

use TrxAddons\ElementorWidgets\BaseTypeModule;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class BaseMetaModule extends BaseTypeModule {

	/**
	 * Returns array of component field types organized based on categories
	 *
	 * @return array  Field types.
	 */
	public function get_field_types() {
		return array();
	}

	/**
	 * Checks if given control field types match component field types
	 *
	 * @param  array $valid_types 	Sets of valid control field types
	 * @param  array $types 		Component field type to check against
	 * 
	 * @return bool  True if the field type is valid, false otherwise.
	 */
	protected function is_valid_field_type( $valid_types, $type ) {
		if ( ! $valid_types || ! $type ) {
			return false;
		}

		$field_types = $this->get_field_types();

		if ( is_array( $valid_types ) ) {
			foreach ( $valid_types as $valid_type ) {

				if ( is_array( $field_types[ $valid_type ] ) ) {
					if ( in_array( $type, $field_types[ $valid_type ] ) ) {
						return true;
					}
				} else {
					if ( $type === $field_types[ $valid_type ] ) {
						return true;
					}
				}
			}
		} else if ( in_array( $type, $field_types[ $valid_types ] ) ) {
			return true;
		}

		return false;
	}
}
