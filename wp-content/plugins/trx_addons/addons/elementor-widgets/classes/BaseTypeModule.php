<?php
/**
 * Base class for the field types
 *
 * @package ThemeREX Addons
 * @since v2.30.0
 */

namespace TrxAddons\ElementorWidgets;

use TrxAddons\ElementorWidgets\BaseModule;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class BaseTypeModule extends BaseModule {

	/**
	 * Get the name of the module
	 *
	 * @return string  The name of the module.
	 */
	public function get_name() {}

	/**
	 * Gets autocomplete values
	 *
	 * @return array  Autocomplete values.
	 */
	protected function get_autocomplete_values( array $data ) {}

	/**
	 * Gets control values titles
	 *
	 * @return array  Control values titles.
	 */
	protected function get_value_titles( array $request ) {}
}
