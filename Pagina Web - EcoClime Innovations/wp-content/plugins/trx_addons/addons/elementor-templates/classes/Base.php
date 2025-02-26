<?php
/**
 * Base class (Singleton)
 *
 * @package ThemeREX Addons
 * @since v2.30.0
 */

namespace TrxAddons\ElementorTemplates;

defined( 'ABSPATH' ) || exit;

use \TrxAddons\Core\Singleton;

class Base extends Singleton {

	/**
	 * Regenerate the CSS for an Elementor post with the given ID.
	 *
	 * @param int $post_id Post ID to regenerate CSS for.
	 * @return void
	 */
	public function regenerate_elementor_css( $post_id ) {
		if ( class_exists( '\Elementor\Core\Files\CSS\Post' ) ) {
			$post_css = new \Elementor\Core\Files\CSS\Post( $post_id );
			$post_css->enqueue();
			$post_css->update();

			\Elementor\Plugin::instance()->frontend->enqueue_styles();
		}
	}
}
