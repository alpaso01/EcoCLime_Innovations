<?php
/**
 * Post Meta Module
 *
 * @package ThemeREX Addons
 * @since v2.30.2
 */

namespace TrxAddons\ElementorWidgets\Widgets\PostMeta;

use TrxAddons\ElementorWidgets\BaseWidgetModule;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Post Meta module
 */
class PostMeta extends BaseWidgetModule {

	/**
	 * Constructor.
	 *
	 * Initializing the module base class.
	 */
	public function __construct() {
		parent::__construct();
		$this->assets = array(
			'css' => true,
			'js'  => false,
		);
	}

	/**
	 * Get the name of the module
	 *
	 * @return string  The name of the module.
	 */
	public function get_name() {
		return 'post-meta';
	}

	/**
	 * Get the selector for the animation type 'Item by item'
	 * 
	 * @return string  The selector of the single item.
	 */
	public function get_separate_animation_selector() {
		return '.trx-addons-post-meta-item, trx-addons-post-meta-item-separator';
	}

}
