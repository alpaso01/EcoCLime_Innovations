<?php
/**
 * Transition Control
 *
 * @package ThemeREX Addons
 * @since v2.30.0
 */

namespace TrxAddons\ElementorWidgets\Controls\Transition;

// Elementor Classes
use Elementor\Group_Control_Base;
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Custom transition group control
 */
class TransitionControl extends Group_Control_Base {
	protected static $fields;

	public static function get_type() {
		return 'trx-addons-transition';
	}

	/**
	 * Retrieve the effect easings
	 *
	 * @return array.  The available array of transition effects
	 */
	public static function get_transition_effects() {
		return [
			'linear' 		=> __( 'Linear', 'trx_addons' ),
			'ease'			=> __( 'Ease', 'trx_addons' ),
			'ease-in' 		=> __( 'Ease In', 'trx_addons' ),
			'ease-out' 		=> __( 'Ease Out', 'trx_addons' ),
			'ease-in-out' 	=> __( 'Ease In Out', 'trx_addons' ),
		];
	}

	/**
	 * @since 1.2.9
	 * @access protected
	 */
	protected function init_fields() {
		$controls = [];

		$controls['property'] = [
			'label'			=> _x( 'Property', 'Transition Control', 'trx_addons' ),
			'type' 			=> Controls_Manager::SELECT,
			'default' 		=> 'all',
			'options'		=> [
				'all'		=> __( 'All', 'trx_addons' ),
			],
			'selectors' => [
				'{{SELECTOR}}' => 'transition-property: {{VALUE}}',
			],
		];

		$controls['function'] = [
			'label'			=> _x( 'Effect', 'Transition Control', 'trx_addons' ),
			'type' 			=> Controls_Manager::SELECT,
			'default' 		=> 'linear',
			'options'		=> self::get_transition_effects(),
			'selectors' => [
				'{{SELECTOR}}' => 'transition-timing-function: {{VALUE}}',
			],
		];

		$controls['duration'] = [
			'label'			=> _x( 'Duration', 'Transition Control', 'trx_addons' ),
			'type' 			=> Controls_Manager::NUMBER,
			'default' 		=> 0.25,
			'min' 			=> 0.05,
			'max' 			=> 2,
			'step' 			=> 0.05,
			'selectors' 	=> [
				'{{SELECTOR}}' => 'transition-duration: {{VALUE}}s;',
			],
		];

		$controls['delay'] = [
			'label'			=> _x( 'Delay', 'Transition Control', 'trx_addons' ),
			'type' 			=> Controls_Manager::NUMBER,
			'default' 		=> 0,
			'min' 			=> 0,
			'max' 			=> 2,
			'step' 			=> 0.01,
			'selectors' 	=> [
				'{{SELECTOR}}' => 'transition-delay: {{VALUE}}s;',
			],
			'separator' 	=> 'after',
		];

		return $controls;
	}

	/**
	 * Prepare fields.
	 *
	 * @param array $fields  Control fields.
	 *
	 * @return array  Processed fields.
	 */
	protected function prepare_fields( $fields ) {
		array_walk( $fields, function( &$field, $field_name ) {
			if ( in_array( $field_name, [ 'transition', 'popover_toggle' ] ) ) {
				return;
			}
			$field['condition']['transition'] = 'custom';
		} );
		return parent::prepare_fields( $fields );
	}

	protected function get_default_options() {
		return [
			'popover' => [
				'starter_name' 	=> 'transition',
				'starter_title' => _x( 'Transition', 'Transition Control', 'trx_addons' ),
			],
		];
	}
}