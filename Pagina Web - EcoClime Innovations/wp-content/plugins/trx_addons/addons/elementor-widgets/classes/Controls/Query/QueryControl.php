<?php
/**
 * Query Control
 *
 * @package ThemeREX Addons
 * @since v2.30.0
 */

namespace TrxAddons\ElementorWidgets\Controls\Query;

// Elementor Classes
use \Elementor\Control_Select2;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * A control for querying posts, terms, users, etc.
 */
class QueryControl extends Control_Select2 {

	/**
	 * Retrieve the control type, in this case `trx-addons-query`.
	 *
	 * @return string  Control type.
	 */
	public function get_type() {
		return 'trx-addons-query';
	}
}