<?php
/**
 * Elementor extension: Animation type for any element
 *
 * @package ThemeREX Addons
 * @since v2.18.4
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}


if ( ! function_exists( 'trx_addons_elm_add_params_animation_type' ) ) {
	add_action( 'elementor/element/before_section_end', 'trx_addons_elm_add_params_animation_type', 10, 3 );
	/**
	 * Add parameter 'Animation type' to the Elementor's sections, columns and all elements to the 'Effects' section.
	 * This parameter allows to animate whole block or split animation by items (if possible)
	 * 
	 * @hooked elementor/element/before_section_end
	 *
	 * @param object $element  Element object
	 * @param string $section_id  Section ID
	 * @param array $args  Section params
	 */
	function trx_addons_elm_add_params_animation_type( $element, $section_id, $args ) {

		if ( ! is_object( $element ) ) {
			return;
		}

		$el_name = $element->get_name();

		if ( $section_id == 'section_effects' && in_array( $el_name, array( 'section', 'column', 'container', 'common' ) ) ) {
			$types = array(
				'block'     => __( 'Whole block', 'trx_addons' ),
				'sequental' => __( 'Item by item', 'trx_addons' ),
				'random'    => __( 'Random items', 'trx_addons' ),
			);
			if ( $el_name == 'common' ) {
				$types['line'] = __( 'Line by line', 'trx_addons' );
				$types['word'] = __( 'Word by word', 'trx_addons' );
				$types['char'] = __( 'Char by char', 'trx_addons' );
			}
			$element->add_control( '_animation_type', array(
				'type' => \Elementor\Controls_Manager::SELECT,
				'label' => __( 'Animation type', 'trx_addons'),
				'label_block' => false,
				'description' => __( "Animate whole block or split animation by items (if possible). The 'Line, Word and Char' animations are only available for text widgets.", 'trx_addons' ),
				'options' => $types,
				'default' => 'block',
				'render_type' => 'template',
				'prefix_class' => 'animation_type_',
				'condition' => array(
					( $el_name == 'common' ? '_animation!' : 'animation!' ) => array( '', 'none' )
				),
			) );
			$element->add_control( '_animation_stagger', array(
				'type' => \Elementor\Controls_Manager::NUMBER,
				'label' => __( 'Stagger', 'trx_addons'),
				'description' => __( "A delay before the next item appears. If not specified - the value from the 'Animation Delay' field is used.", 'trx_addons' ),
				'default' => '',
				'min' => 0,
				'max' => 1000,
				'step' => 10,
				'render_type' => 'template',
				'condition' => array(
					( $el_name == 'common' ? '_animation!' : 'animation!' ) => array( '', 'none' ),
					'_animation_type!' => array( 'block' ),
				),
			) );
		}
	}
}

if ( ! function_exists( 'trx_addons_elm_add_animation_data_to_widgets' ) ) {
	// Before Elementor 2.1.0
	add_action( 'elementor/frontend/element/before_render',  'trx_addons_elm_add_animation_data_to_widgets', 10, 1 );
	// After Elementor 2.1.0
	add_action( 'elementor/frontend/section/before_render',  'trx_addons_elm_add_animation_data_to_widgets', 10, 1 );
	add_action( 'elementor/frontend/column/before_render',  'trx_addons_elm_add_animation_data_to_widgets', 10, 1 );
	add_action( 'elementor/frontend/widget/before_render',  'trx_addons_elm_add_animation_data_to_widgets', 10, 1 );
	// After Elementor 3.16.0
	add_action( 'elementor/frontend/container/before_render',  'trx_addons_elm_add_animation_data_to_widgets', 10, 1 );
	/**
	 * Add "data-animation-xxx" to the wrapper of the section, column or widget if entrance animation is enabled
	 * 
	 * @hooked elementor/frontend/container/before_render (after Elementor 3.16.0)
	 * @hooked elementor/frontend/section/before_render (after Elementor 2.1.0)
	 * @hooked elementor/frontend/column/before_render  (after Elementor 2.1.0)
	 * @hooked elementor/frontend/widget/before_render  (after Elementor 2.1.0)
	 * @hooked elementor/frontend/element/before_render (before Elementor 2.1.0)
	 *
	 * @param object $element  Elementor section, column or widget object
	 */
	function trx_addons_elm_add_animation_data_to_widgets( $element ) {
		//$settings = trx_addons_elm_prepare_global_params( $element->get_settings() );
		$animation = $element->get_settings( '_animation' );
		if ( empty( $animation ) ) {
			$animation = $element->get_settings( 'animation' );
		}
		if ( ! empty( $animation ) ) {
			$type = $element->get_settings( '_animation_type' );
			if ( empty( $type ) ) {
				$type = 'block';
			}
			$element->add_render_attribute( '_wrapper', 'data-animation-type', $type );
			if ( $type != 'block' ) {
				$stagger = $element->get_settings( '_animation_stagger' );
				$element->add_render_attribute( '_wrapper', 'data-animation-stagger', ! empty( $stagger ) ? $stagger : '' );
			}
		}
	}
}

// Add entrance animations
if ( ! function_exists( 'trx_addons_elm_add_entrance_animations' ) ) {
	add_filter( 'elementor/controls/animations/additional_animations', 'trx_addons_elm_add_entrance_animations' );
	function trx_addons_elm_add_entrance_animations( $animations ) {
		$animations[ __( 'ThemeRex Addons', 'trx_addons' ) ] = array(
			'trx_ani_slideInDownSmooth' => esc_html__( 'Slide In Down (Smooth)', 'trx_addons' )
		);
		return $animations;
	}
}

// Add exit animations
if ( ! function_exists( 'trx_addons_elm_add_exit_animations' ) ) {
	add_filter( 'elementor/controls/exit-animations/additional_animations', 'trx_addons_elm_add_exit_animations' );
	function trx_addons_elm_add_exit_animations( $animations ) {
		$animations[ __( 'ThemeRex Addons', 'trx_addons' ) ] = array(
			'trx_ani_slideInDownSmooth' => esc_html__( 'Slide Out Up (Smooth)', 'trx_addons' )
		);
		return $animations;
	}
}
