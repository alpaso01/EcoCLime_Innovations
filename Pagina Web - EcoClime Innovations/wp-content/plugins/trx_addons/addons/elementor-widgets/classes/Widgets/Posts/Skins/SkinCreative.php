<?php
namespace TrxAddons\ElementorWidgets\Widgets\Posts\Skins;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Creative Skin for Posts widget
 */
class SkinCreative extends BaseSkin {

	/**
	 * Retrieve Skin ID.
	 *
	 * @return string Skin ID.
	 */
	public function get_id() {
		return 'creative';
	}

	/**
	 * Retrieve Skin title.
	 *
	 * @return string Skin title.
	 */
	public function get_title() {
		return __( 'Creative', 'trx_addons' );
	}
}
