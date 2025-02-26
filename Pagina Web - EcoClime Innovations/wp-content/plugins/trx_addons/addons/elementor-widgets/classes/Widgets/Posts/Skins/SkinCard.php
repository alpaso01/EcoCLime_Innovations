<?php
namespace TrxAddons\ElementorWidgets\Widgets\Posts\Skins;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Card Skin for Posts widget
 */
class SkinCard extends BaseSkin {

	/**
	 * Retrieve Skin ID.
	 *
	 * @return string Skin ID.
	 */
	public function get_id() {
		return 'card';
	}

	/**
	 * Retrieve Skin title.
	 *
	 * @return string Skin title.
	 */
	public function get_title() {
		return __( 'Card', 'trx_addons' );
	}
}
