<?php
/**
 * Elementor extension: Stack sections support
 *
 * @package ThemeREX Addons
 * @since v2.18.4
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}

if ( ! function_exists( 'trx_addons_elm_add_params_stack_section' ) ) {
	add_action( 'elementor/element/before_section_end', 'trx_addons_elm_add_params_stack_section', 10, 3 );
	/**
	 * Add parameters 'Stack section' to the Elementor sections and containers
	 * to add the ability to stack sections over each other on the page scroll.
	 * Available effects: 'slide' and 'fade'
	 * 
	 * @hooked elementor/element/before_section_end
	 *
	 * @param object $element     Elementor element
	 * @param string $section_id  Section ID
	 * @param array $args         Section arguments
	 */
	function trx_addons_elm_add_params_stack_section( $element, $section_id, $args ) {

		if ( ! is_object( $element ) ) {
			return;
		}
		
		$el_name = $element->get_name();

		// Add 'Stack section' to the sections
		if ( ( $el_name == 'section' && $section_id == 'section_advanced' ) || ( $el_name == 'container' && $section_id == 'section_layout' ) ) {
			$element->add_control( 'stack_section', array(
									'type' => \Elementor\Controls_Manager::SWITCHER,
									'label' => __("Stack section", 'trx_addons'),
									'label_on' => __( 'On', 'trx_addons' ),
									'label_off' => __( 'Off', 'trx_addons' ),
									'return_value' => 'on',
									'render_type' => 'template',
									'prefix_class' => 'sc_stack_section_',
								) );

			$element->add_control( 'stack_section_effect', array(
									'type' => \Elementor\Controls_Manager::SELECT,
									'label' => __("Stack effect", 'trx_addons'),
									'options' => apply_filters( 'trx_addons_filter_stack_section_effects', array(
													'slide' => __( 'Slide', 'trx_addons' ),
													'fade' => __( 'Fade', 'trx_addons' ),
													) ),
									'default' => 'slide',
									'condition' => array(
										'stack_section' => array( 'on' )
									),
									'prefix_class' => 'sc_stack_section_effect_',
								) );

			$element->add_control( 'stack_section_zoom', array(
									'type' => \Elementor\Controls_Manager::SWITCHER,
									'label' => __("Stack zoom", 'trx_addons'),
									'label_on' => __( 'On', 'trx_addons' ),
									'label_off' => __( 'Off', 'trx_addons' ),
									'return_value' => 'on',
									'render_type' => 'template',
									'prefix_class' => 'sc_stack_section_zoom_',
									'condition' => array(
										'stack_section' => array( 'on' ),
										'stack_section_effect' => array( 'slide' )
									),
								) );

			$element->add_control( 'stack_section_transparency', array(
									'type' => \Elementor\Controls_Manager::SWITCHER,
									'label' => __("Stack transparent", 'trx_addons'),
									'label_on' => __( 'On', 'trx_addons' ),
									'label_off' => __( 'Off', 'trx_addons' ),
									'return_value' => 'on',
									'render_type' => 'template',
									'prefix_class' => 'sc_stack_section_transparency_',
									'condition' => array(
										'stack_section' => array( 'on' ),
										'stack_section_effect' => array( 'slide' )
									),
								) );
		}
	}
}

if ( ! function_exists( 'trx_addons_elm_add_params_stack_section_before_render' ) ) {
	// Before Elementor 2.1.0
	add_action( 'elementor/frontend/element/before_render',  'trx_addons_elm_add_params_stack_section_before_render', 10, 1 );
	// After Elementor 2.1.0
	add_action( 'elementor/frontend/section/before_render', 'trx_addons_elm_add_params_stack_section_before_render', 10, 1 );
	// After Elementor 3.16.0
	add_action( 'elementor/frontend/container/before_render', 'trx_addons_elm_add_params_stack_section_before_render', 10, 1 );
	/**
	 * Enqueue scripts and styles before render the Elementor section
	 * 
	 * @hooked elementor/frontend/element/before_render
	 * @hooked elementor/frontend/section/before_render
	 * @hooked elementor/frontend/container/before_render
	 * 
	 * @param object $element Current element
	 */
	function trx_addons_elm_add_params_stack_section_before_render( $element ) {
		if ( is_object( $element ) ) {
			$el_name = $element->get_name();
			if ( 'section' == $el_name || 'container' == $el_name ) {
				//$settings = trx_addons_elm_prepare_global_params( $element->get_settings() );
				$stack = $element->get_settings( 'stack_section' );
				if ( $stack == 'on' ) {
					trx_addons_enqueue_tweenmax( array(
						'ScrollTrigger' => true
					) );
				}
			}
		}
	}
}

if ( !function_exists( 'trx_addons_elm_stack_section_check_in_html_output' ) ) {
	add_action( 'trx_addons_action_show_layout_from_cache', 'trx_addons_elm_stack_section_check_in_html_output', 10, 1 );
	add_action( 'trx_addons_action_check_page_content', 'trx_addons_elm_stack_section_check_in_html_output', 10, 1 );
	/**
	 * Load TweenMax library if the stack section is used in the page
	 * 
	 * @hooked trx_addons_action_show_layout_from_cache
	 * @hooked trx_addons_action_check_page_content
	 * 
	 * @param string $content Page content
	 * 
	 * @return string  Page content
	 */
	function trx_addons_elm_stack_section_check_in_html_output( $content = '' ) {
		$args = array(
			'check' => array(
				'class=[\'"][^\'"]*sc_stack_section_on'
			)
		);
		if ( trx_addons_check_in_html_output( 'sc_stack_section', $content, $args ) ) {
			trx_addons_enqueue_tweenmax( array(
				'ScrollTrigger' => true
			) );
		}
		return $content;
	}
}
