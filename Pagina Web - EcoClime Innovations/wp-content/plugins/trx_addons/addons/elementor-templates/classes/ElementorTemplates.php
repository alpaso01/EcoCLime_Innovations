<?php
/**
 * Plugin support: Elementor Core Extensions.
 *
 * @package ThemeREX Addons
 * @since v2.30.0
 */

namespace TrxAddons\ElementorTemplates;

use TrxAddons\ElementorTemplates\Globals\Controller;
use TrxAddons\ElementorTemplates\Globals\ColorsEditor;
use TrxAddons\ElementorTemplates\Globals\TypographyEditor;

use TrxAddons\ElementorTemplates\Templates\Library;

/**
 * Intializes Elementor Core Extensions on Elementor editing page.
 */
class ElementorTemplates extends Base {

	private $templates_library;
	private $colors_editor;
	private $typography_editor;

	/**
	 * Constructor.
	 */
	public function __construct() {
		parent::__construct();

		add_action( 'elementor/controls/register', array( $this, 'register_controls' ) );

		$this->colors_editor = new ColorsEditor();
		$this->typography_editor = new TypographyEditor();
		$this->templates_library = new Library();

		$this->register_data_controllers();
	}

	/**
	 * Register custom Elementor control.
	 */
	public function register_controls() {
		\Elementor\Plugin::instance()->controls_manager->register( new Action() );
	}

	/**
	 * Register custom Elementor REST data controllers.
	 *
	 * @return void
	 */
	public function register_data_controllers() {
		\Elementor\Plugin::instance()->data_manager_v2->register_controller( new Controller() );
	}
}
