<?php
/**
 * Base Control class
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
 * Base control
 */
abstract class BaseControlModule extends BaseModule {

	protected $control_class = '';

	/**
	 * Constructor.
	 *
	 * Initializing the control base class.
	 */
	public function __construct() {
		parent::__construct();
		// Control class names
		$this->control_class = $this->module_class . 'Control';
		// Register the control
		add_action( trx_addons_elementor_get_action_for_controls_registration(), array( $this, 'register_control' ) );
	}

	/**
	 * Get the type of the control
	 *
	 * @return string  The type of the control: control | group
	 
	 */
	abstract public function get_type();
	
	/**
	 * Create and register control
	 * 
	 * @hooked elementor/controls/register
	 * 
	 * @param object $controls_manager  Elementor controls manager
	 */
	public function register_control( $controls_manager) {
		$control_class = "TrxAddons\\ElementorWidgets\\Controls\\{$this->module_class}\\{$this->control_class}";
		$control_obj   = new $control_class();
		$control_id    = $control_obj->get_type();
		if ( $this->get_type() == 'control' ) {
			// Add a single control
			if ( defined( 'ELEMENTOR_VERSION' ) && version_compare( ELEMENTOR_VERSION, '3.5.0', '>=' ) ) {
				$controls_manager->register( $control_obj );
			} else {
				$controls_manager->register_control( $control_id, $control_obj );
			}
		} else {
			// Add control groups
			$controls_manager->add_group_control( $control_id, $control_obj );
		}

	}

	/**
	 * Get module relative path to the assets folder
	 * 
	 * @return string  Relative path to the assets folder in the module folder.
	 */
	public function get_assets_path( $file ) {
		return TRX_ADDONS_PLUGIN_ADDONS . 'elementor-widgets/classes/Controls/' . $this->module_class . '/assets/' . $file;
	}

}
