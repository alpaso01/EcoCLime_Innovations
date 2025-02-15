<?php
/**
 * Elementor extension: Add own hover animations to the Elementor's list of animations
 *
 * @package ThemeREX Addons
 * @since v2.34.1
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}

// Add hover animations
if ( ! function_exists( 'trx_addons_elm_add_hover_animations' ) ) {
	add_filter( 'elementor/controls/hover_animations/additional_animations', 'trx_addons_elm_add_hover_animations' );
	function trx_addons_elm_add_hover_animations( $animations ) {
		$animations[ 'trx_hover_zoom' ] = esc_html__( 'Masked Zoom', 'trx_addons' );
		return $animations;
	}
}


if ( ! function_exists( 'trx_addons_elm_modify_hover_animations_in_core_widgets' ) ) {
	add_action( 'elementor/element/before_section_end', 'trx_addons_elm_modify_hover_animations_in_core_widgets', 10, 3 );
	/**
	 * Add an option 'prefix_class' to the parameter 'Hover Animation' in all Elementor's widgets
	 * 
	 * @hooked elementor/element/before_section_end
	 *
	 * @param object $element     Elementor element
	 * @param string $section_id  Section ID
	 * @param array $args         Section arguments
	 */
	function trx_addons_elm_modify_hover_animations_in_core_widgets( $element, $section_id, $args ) {

		if ( ! is_object( $element ) ) {
			return;
		}
		
		$el_name = $element->get_name();

		if (
			( in_array( $el_name, array( 'icon-box', 'icon' ) ) && $section_id == 'section_style_icon' )
			||
			( in_array( $el_name, array( 'image-box', 'image' ) ) && $section_id == 'section_style_image' )
			||
			( in_array( $el_name, array( 'social-icons' ) ) && $section_id == 'section_social_hover' )
		) {
			// Detect edit mode
			// $is_edit_mode = trx_addons_elm_is_edit_mode();

			// Add the field 'tab_subtitle' to each tab
			$control = $element->get_controls( 'hover_animation' );
			if ( ! empty( $control['type'] ) ) {
				$element->update_control( 'hover_animation', array(
					'prefix_class' => 'with-elementor-animation-',
				) );
			}
		}
	}
}
