<?php
/**
 * Base Module class for admin part of the widgets
 *
 * @package ThemeREX Addons
 * @since v2.30.0
 */

namespace TrxAddons\ElementorWidgets;

use TrxAddons\ElementorWidgets\BaseModule;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Base module
 */
abstract class BaseAdminModule extends BaseModule {

	protected $admin_class = '';

	/**
	 * Constructor.
	 *
	 * Initializing the module base class.
	 */
	public function __construct() {
		parent::__construct();
		$this->admin_class = $this->module_class;
		$this->module_class = str_replace( 'Admin', '', $this->module_class );
	}

	/**
	 * Get module relative path to the assets folder
	 * 
	 * @return string  Relative path to the assets folder in the module folder.
	 */
	public function get_assets_path( $file ) {
		return TRX_ADDONS_PLUGIN_ADDONS . 'elementor-widgets/classes/Widgets/' . $this->module_class . '/admin/assets/' . $file;
	}
}
