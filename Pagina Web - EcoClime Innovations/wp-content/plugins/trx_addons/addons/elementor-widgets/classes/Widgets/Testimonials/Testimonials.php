<?php
/**
 * Testimonials Module
 *
 * @package ThemeREX Addons
 * @since v2.30.1
 */

namespace TrxAddons\ElementorWidgets\Widgets\Testimonials;

use TrxAddons\ElementorWidgets\BaseWidgetModule;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Testimonials module
 */
class Testimonials extends BaseWidgetModule {

	/**
	 * Constructor.
	 *
	 * Initializing the module base class.
	 */
	public function __construct() {
		parent::__construct();
		$this->assets = array(
			'css' => true,
			'js'  => true,
			'lib' => array(
				'js' => array(
					'isotope' => array( 'src' => '../../../assets/isotope/isotope.pkgd.min.js' ),
					'imagesloaded' => true,
					'swiper' => true,
				)
			)
		);
	}

	/**
	 * Get the name of the module
	 *
	 * @return string  The name of the module.
	 */
	public function get_name() {
		return 'testimonials';
	}

	/**
	 * Get the selector for the animation type 'Item by item'
	 * 
	 * @return string  The selector of the single item.
	 */
	public function get_separate_animation_selector() {
		return '.trx-addons-testimonials-container';
	}

}
