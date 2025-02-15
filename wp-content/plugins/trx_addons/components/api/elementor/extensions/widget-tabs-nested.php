<?php
/**
 * Elementor extension: Improve core widget "Nested Tabs"
 *
 * @package ThemeREX Addons
 * @since v2.33.1
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}


if ( ! function_exists( 'trx_addons_elm_add_params_nested_tabs_title_description' ) ) {
	add_action( 'elementor/element/before_section_end', 'trx_addons_elm_add_params_nested_tabs_title_description', 10, 3 );
	/**
	 * Add a parameter 'Subtitle' to the Elementor tabs
	 * 
	 * @hooked elementor/element/before_section_end
	 *
	 * @param object $element     Elementor element
	 * @param string $section_id  Section ID
	 * @param array $args         Section arguments
	 */
	function trx_addons_elm_add_params_nested_tabs_title_description( $element, $section_id, $args ) {

		if ( ! is_object( $element ) ) {
			return;
		}
		
		$el_name = $element->get_name();
		
		if ( $el_name == 'nested-tabs' && $section_id == 'section_tabs' ) {

			// Detect edit mode
			// $is_edit_mode = trx_addons_elm_is_edit_mode();

			// Add the field 'tab_subtitle' to each tab
			$control   = $element->get_controls( 'tabs' );
			if ( ! empty( $control['fields'] ) && ! empty( $control['default'] ) ) {
				$fields    = $control['fields'];
				$default   = $control['default'];
				if ( is_array( $default ) ) {
					for( $i = 0; $i < count( $default ); $i++ ) {
						$default[$i]['tab_subtitle'] = '';
					}
				}
				trx_addons_array_insert_after( $fields, 'tab_title', array(
					'tab_subtitle' => array(
						'type' => \Elementor\Controls_Manager::TEXTAREA,
						'label' => __("Subtitle", 'trx_addons'),
						'label_block' => true,
						'placeholder' => __("Enter the tab's subtitle", 'trx_addons'),
						// 'dynamic' => [
						// 	'active' => true,
						// ],
						'name' => 'tab_subtitle'
					)
				) );
				$element->update_control( 'tabs', array(
					'default' => $default,
					'fields' => $fields
				) );
			}

			// Add the parameter 'prefix_class' to the field 'tabs_direction'
			$element->update_control( 'tabs_direction', array(
				'prefix_class' => 'e-n-tabs-direction-',
			) );

			$element->add_responsive_control( 'tabs_align_vertical', [
				'label' => esc_html__( 'Vertical Align Title', 'trx_addons' ),
				'type' => \Elementor\Controls_Manager::CHOOSE,
				'options' => [
					'flex-start' => [
						'title' => esc_html__( 'Start', 'trx_addons' ),
						'icon' => 'eicon-align-start-v',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'trx_addons' ),
						'icon' => 'eicon-align-center-v',
					],
					'flex-end' => [
						'title' => esc_html__( 'End', 'trx_addons' ),
						'icon' => 'eicon-align-end-v',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .e-n-tabs-heading .e-n-tab-title' => 'align-content: {{VALUE}}',
				],
				'condition' => [
					'tabs_direction' => [ 'block-start', 'block-end', 'top', 'bottom' ],
				],
			] );
	
			$element->add_control(
				'tabs_title_html_tag',
				array(
					'label'   => __( 'Title HTML Tag', 'trx_addons' ),
					'type'    => \Elementor\Controls_Manager::SELECT,
					'default' => 'span',
					'options' => array(
						'h1'   => __( 'H1', 'trx_addons' ),
						'h2'   => __( 'H2', 'trx_addons' ),
						'h3'   => __( 'H3', 'trx_addons' ),
						'h4'   => __( 'H4', 'trx_addons' ),
						'h5'   => __( 'H5', 'trx_addons' ),
						'h6'   => __( 'H6', 'trx_addons' ),
						'div'  => __( 'div', 'trx_addons' ),
						'span' => __( 'span', 'trx_addons' ),
						'p'    => __( 'p', 'trx_addons' ),
					),
				)
			);

			$element->add_responsive_control( 'tabs_title_max_width', [
				'label' => esc_html__( 'Title Max. Width', 'trx_addons' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'range' => [
					'%' => [
						'min' => 10,
						'max' => 100,
					],
					'px' => [
						'min' => 20,
						'max' => 600,
					],
				],
				'default' => [
					'unit' => '%',
				],
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'custom' ],
				'selectors' => [
					'{{WRAPPER}} .e-n-tabs-heading .e-n-tab-title' => 'max-width: {{SIZE}}{{UNIT}}',
				],
				'condition' => [
					'tabs_direction' => [ 'block-start', 'block-end', 'top', 'bottom' ],
				],
			] );

			$element->add_control( 'tabs_icon_placement', [
				'label' => esc_html__( 'Icon Placement', 'trx_addons' ),
				'type' => \Elementor\Controls_Manager::CHOOSE,
				'options' => [
					'inside' => [
						'title' => esc_html__( 'Inside', 'trx_addons' ),
						'icon' => 'eicon-single-post',
					],
					'outside' => [
						'title' => esc_html__( 'Outside', 'trx_addons' ),
						'icon' => 'eicon-site-identity',
					],
				],
				'default' => 'inside',
				'condition' => [
					'icon_position!' => ['block-start', 'block-end', 'top', 'bottom'],
				],
			] );

			$element->add_control(
				'tabs_subtitle_html_tag',
				array(
					'label'   => __( 'Subtitle HTML Tag', 'trx_addons' ),
					'type'    => \Elementor\Controls_Manager::SELECT,
					'default' => 'span',
					'options' => array(
						'h1'   => __( 'H1', 'trx_addons' ),
						'h2'   => __( 'H2', 'trx_addons' ),
						'h3'   => __( 'H3', 'trx_addons' ),
						'h4'   => __( 'H4', 'trx_addons' ),
						'h5'   => __( 'H5', 'trx_addons' ),
						'h6'   => __( 'H6', 'trx_addons' ),
						'div'  => __( 'div', 'trx_addons' ),
						'span' => __( 'span', 'trx_addons' ),
						'p' => __( 'p', 'trx_addons' ),
					),
					'condition' => [
						'tabs_icon_placement' => 'inside',
					],
					// 'conditions' => [
					// 	'relation' => 'or',
					// 	'terms' => [
					// 		[
					// 			'name' => 'icon_position',
					// 			'operator' => '==',
					// 			'value' => ['block-start', 'block-end', 'top', 'bottom'],
					// 		],
					// 		[
					// 			'name' => 'tabs_icon_placement',
					// 			'operator' => '==',
					// 			'value' => 'inside',
					// 		],
					// 	],
					// ],
				)
			);
		}
	}
}

// Disabled because on the responsive mode the heading is hidden (display: contents) and the styles are not applied
// if ( ! function_exists( 'trx_addons_elm_add_params_nested_tabs_heading_style' ) ) {
// 	add_action( 'elementor/element/before_section_start', 'trx_addons_elm_add_params_nested_tabs_heading_style', 10, 3 );
// 	/**
// 	 * Add parameters to customize a heading wrapper in the Elementor tabs
// 	 * 
// 	 * @hooked elementor/element/before_section_start
// 	 *
// 	 * @param object $element     Elementor element
// 	 * @param string $section_id  Section ID
// 	 * @param array $args         Section arguments
// 	 */
// 	function trx_addons_elm_add_params_nested_tabs_heading_style( $element, $section_id, $args ) {

// 		if ( ! is_object( $element ) ) {
// 			return;
// 		}
		
// 		$el_name = $element->get_name();
		
// 		// Add template selector
// 		if ( $el_name == 'nested-tabs' && $section_id == 'section_tabs_style' ) {

// 			$element->start_controls_section(
// 				'section_nested_tabs_heading_style_settings',
// 				[
// 					'label' => __( 'Heading', 'trx_addons' ),
// 					'tab'   => \Elementor\Controls_Manager::TAB_STYLE
// 				]
// 			);

// 			$element->add_group_control(
// 				\Elementor\Group_Control_Background::get_type(),
// 				[
// 					'name' => 'nested_tabs_heading_background_color',
// 					'types' => [ 'classic', 'gradient' ],
// 					'exclude' => [ 'image' ],
// 					'selector' => "{{WRAPPER}} .e-n-tabs > .e-n-tabs-heading",
// 					'fields_options' => [
// 						'color' => [
// 							'label' => esc_html__( 'Background Color', 'trx_addons' ),
// 							'selectors' => [
// 								'{{SELECTOR}}' => 'background: {{VALUE}}',
// 							],
// 						],
// 					],
// 				]
// 			);
	
// 			$element->add_group_control(
// 				\Elementor\Group_Control_Border::get_type(),
// 				[
// 					'name' => 'nested_tabs_heading_border',
// 					'selector' => "{{WRAPPER}} .e-n-tabs > .e-n-tabs-heading",
// 				]
// 			);

// 			$element->add_responsive_control(
// 				'nested_tabs_heading_border_radius',
// 				[
// 					'label' => esc_html__( 'Border Radius', 'trx_addons' ),
// 					'type' => \Elementor\Controls_Manager::DIMENSIONS,
// 					'size_units'   => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
// 					'selectors' => [
// 						'{{WRAPPER}} .e-n-tabs > .e-n-tabs-heading' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow: hidden;',
// 					],
// 				]
// 			);
	
// 			$element->add_responsive_control(
// 				'nested_tabs_heading_padding',
// 				[
// 					'label'        => __( 'Padding', 'trx_addons' ),
// 					'type'         => \Elementor\Controls_Manager::DIMENSIONS,
// 					'size_units'   => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
// 					'selectors'    => [
// 						'{{WRAPPER}} .e-n-tabs > .e-n-tabs-heading' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
// 					]
// 				]
// 			);
	
// 			$element->add_group_control(
// 				\Elementor\Group_Control_Box_Shadow::get_type(),
// 				[
// 					'name' => 'nested_tabs_heading_box_shadow',
// 					'selector' => "{{WRAPPER}} .e-n-tabs > .e-n-tabs-heading",
// 				]
// 			);
	
// 			$element->end_controls_section();
	
// 		}
// 	}
// }

if ( ! function_exists( 'trx_addons_elm_add_params_nested_tabs_border_as_separator_style' ) ) {
	add_action( 'elementor/element/before_section_end', 'trx_addons_elm_add_params_nested_tabs_border_as_separator_style', 10, 3 );
	/**
	 * Add parameter "Border as Separator" to the Elementor tabs
	 * 
	 * @hooked elementor/element/before_section_end
	 *
	 * @param object $element     Elementor element
	 * @param string $section_id  Section ID
	 * @param array $args         Section arguments
	 */
	function trx_addons_elm_add_params_nested_tabs_border_as_separator_style( $element, $section_id, $args ) {

		if ( ! is_object( $element ) ) {
			return;
		}
		
		$el_name = $element->get_name();
		
		if ( $el_name == 'nested-tabs' && $section_id == 'section_tabs_style' ) {
			$control = $element->get_controls( 'tabs_title_border_color' );
			if ( ! empty( $control['selectors'] ) && is_array( $control['selectors'] ) ) {
				$control['selectors']['{{WRAPPER}}.elementor-widget-n-tabs.e-n-tabs-heading-border-as-separator.e-n-tabs-direction-inline-start .e-n-tabs > .e-n-tabs-heading > .e-n-tab-title:first-child[aria-selected="true"]'] = 'border-top-color: {{VALUE}} !important;';
				$control['selectors']['{{WRAPPER}}.elementor-widget-n-tabs.e-n-tabs-heading-border-as-separator.e-n-tabs-direction-inline-start .e-n-tabs > .e-n-tabs-heading > .e-n-tab-title:first-child[aria-selected="false"]:hover'] = 'border-top-color: {{VALUE}} !important;';
				$control['selectors']['{{WRAPPER}}.elementor-widget-n-tabs.e-n-tabs-heading-border-as-separator.e-n-tabs-direction-inline-end .e-n-tabs > .e-n-tabs-heading > .e-n-tab-title:first-child[aria-selected="true"]'] = 'border-top-color: {{VALUE}} !important;';
				$control['selectors']['{{WRAPPER}}.elementor-widget-n-tabs.e-n-tabs-heading-border-as-separator.e-n-tabs-direction-inline-end .e-n-tabs > .e-n-tabs-heading > .e-n-tab-title:first-child[aria-selected="false"]:hover'] = 'border-top-color: {{VALUE}} !important;';
				$control['selectors']['{{WRAPPER}}.elementor-widget-n-tabs.e-n-tabs-heading-border-as-separator.e-n-tabs-direction-block-start .e-n-tabs > .e-n-tabs-heading > .e-n-tab-title:first-child[aria-selected="true"]'] = 'border-left-color: {{VALUE}} !important;';
				$control['selectors']['{{WRAPPER}}.elementor-widget-n-tabs.e-n-tabs-heading-border-as-separator.e-n-tabs-direction-block-start .e-n-tabs > .e-n-tabs-heading > .e-n-tab-title:first-child[aria-selected="false"]:hover'] = 'border-left-color: {{VALUE}} !important;';
				$control['selectors']['{{WRAPPER}}.elementor-widget-n-tabs.e-n-tabs-heading-border-as-separator.e-n-tabs-direction-block-end .e-n-tabs > .e-n-tabs-heading > .e-n-tab-title:first-child[aria-selected="true"]'] = 'border-left-color: {{VALUE}} !important;';
				$control['selectors']['{{WRAPPER}}.elementor-widget-n-tabs.e-n-tabs-heading-border-as-separator.e-n-tabs-direction-block-end .e-n-tabs > .e-n-tabs-heading > .e-n-tab-title:first-child[aria-selected="false"]:hover'] = 'border-left-color: {{VALUE}} !important;';
				$element->update_control( 'tabs_title_border_color', array(
					'selectors' => $control['selectors']
				) );
			}

			$element->add_control(
				'border_as_separator',
				[
					'label' => __( 'Border as Separator', 'trx_addons' ),
					'type' => \Elementor\Controls_Manager::SWITCHER,
					'label_on' => __( 'On', 'trx_addons' ),
					'label_off' => __( 'Off', 'trx_addons' ),
					'prefix_class' => 'e-n-tabs-heading-border-as-',
					'return_value' => 'separator',
					'default' => '',
					// 'condition' => [
					// 	'tabs_direction' => [ 'inline-start', 'inline-end', 'start', 'end' ],
					// ],
				]
			);
			$element->add_control(
				'border_hide_first',
				[
					'label' => __( 'Hide First Border', 'trx_addons' ),
					'label_block' => false,
					// 'type' => \Elementor\Controls_Manager::SWITCHER,
					// 'label_on' => __( 'On', 'trx_addons' ),
					// 'label_off' => __( 'Off', 'trx_addons' ),
					// 'return_value' => 'first',
					'type' => \Elementor\Controls_Manager::SELECT,
					'options' => [
						'no' => __( 'No', 'trx_addons' ),
						'border' => __( 'Border Only', 'trx_addons' ),
						'border-padding' => __( 'Border & Padding', 'trx_addons' ),
					],
					'prefix_class' => 'e-n-tabs-heading-border-hide-first-',
					'default' => 'no',
					'condition' => [
						'border_as_separator!' => '',
					],
				]
			);
			$element->add_control(
				'border_hide_last',
				[
					'label' => __( 'Hide Last Border', 'trx_addons' ),
					// 'type' => \Elementor\Controls_Manager::SWITCHER,
					// 'label_on' => __( 'On', 'trx_addons' ),
					// 'label_off' => __( 'Off', 'trx_addons' ),
					// 'return_value' => 'last',
					'type' => \Elementor\Controls_Manager::SELECT,
					'options' => [
						'no' => __( 'No', 'trx_addons' ),
						'border' => __( 'Border Only', 'trx_addons' ),
						'border-padding' => __( 'Border & Padding', 'trx_addons' ),
					],
					'prefix_class' => 'e-n-tabs-heading-border-hide-last-',
					'default' => 'no',
					'condition' => [
						'border_as_separator!' => '',
					],
				]
			);
		}
	}
}

if ( ! function_exists( 'trx_addons_elm_add_params_nested_tabs_subtitle_style' ) ) {
	add_action( 'elementor/element/after_section_end', 'trx_addons_elm_add_params_nested_tabs_subtitle_style', 10, 3 );
	/**
	 * Add a parameters to customize the 'Subtitle' in the Elementor tabs
	 * 
	 * @hooked elementor/element/after_section_end
	 *
	 * @param object $element     Elementor element
	 * @param string $section_id  Section ID
	 * @param array $args         Section arguments
	 */
	function trx_addons_elm_add_params_nested_tabs_subtitle_style( $element, $section_id, $args ) {

		if ( ! is_object( $element ) ) {
			return;
		}
		
		$el_name = $element->get_name();
		
		if ( $el_name == 'nested-tabs' && $section_id == 'section_title_style' ) {

			$element->start_controls_section(
				'section_nested_tabs_subtitle_style_settings',
				[
					'label' => __( 'Subtitle', 'trx_addons' ),
					'tab'   => \Elementor\Controls_Manager::TAB_STYLE
				]
			);
	
			$element->add_group_control(
				\Elementor\Group_Control_Typography::get_type(),
				[
					'label'    => __( 'Typography', 'trx_addons' ),
					'name'     => 'nested_tabs_subtitle_typography',
					'selector' => '{{WRAPPER}} .e-n-tab-subtitle',
				]
			);
	
			$element->add_responsive_control(
				'nested_tabs_subtitle_padding',
				[
					'label'        => __( 'Padding', 'trx_addons' ),
					'type'         => \Elementor\Controls_Manager::DIMENSIONS,
					'size_units'   => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
					'selectors'    => [
						'{{WRAPPER}} .e-n-tab-subtitle' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
					]
				]
			);
	
			$element->add_responsive_control(
				'nested_tabs_subtitle_margin',
				[
					'label'      => __( 'Margin', 'trx_addons' ),
					'type'       => \Elementor\Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
					'selectors'  => [
						'{{WRAPPER}} .e-n-tab-subtitle' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
					]
				]
			);
	
			$element->start_controls_tabs( 'tabs_subtitle_style_tabs' );
	
			// Normal State Tab
			$element->start_controls_tab(
				'tabs_subtitle_normal',
				[
					'label' => __( 'Normal', 'trx_addons' ),
				]
			);
	
			$element->add_control(
				'nested_tabs_subtitle_normal_color',
				[
					'label'     => __( 'Color', 'trx_addons' ),
					'type'      => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .e-n-tab-subtitle' => 'color: {{VALUE}};',
					],
				]
			);
	
			$element->end_controls_tab();
	
			// Active State Tab
			$element->start_controls_tab(
				'tabs_subtitle_active',
				[
					'label' => __( 'Active', 'trx_addons' ),
				]
			);
	
			$element->add_control(
				'nested_tabs_subtitle_active_color',
				[
					'label'     => __( 'Color', 'trx_addons' ),
					'type'      => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .e-n-tab-title[aria-selected="true"] .e-n-tab-subtitle,
						 {{WRAPPER}} .e-n-tab-title:hover .e-n-tab-subtitle' => 'color: {{VALUE}};',
					]
				]
			);
	
			$element->end_controls_tab();
	
			$element->end_controls_tabs();
	
			$element->end_controls_section();
	
		}
	}
}

if ( ! function_exists( 'trx_addons_elm_add_params_nested_tabs_title_icon_style' ) ) {
	add_action( 'elementor/element/before_section_end', 'trx_addons_elm_add_params_nested_tabs_title_icon_style', 10, 3 );
	/**
	 * Add the parameter ' Vertical Alignment' for the Icon in the Elementor tabs
	 * 
	 * @hooked elementor/element/before_section_end
	 *
	 * @param object $element     Elementor element
	 * @param string $section_id  Section ID
	 * @param array $args         Section arguments
	 */
	function trx_addons_elm_add_params_nested_tabs_title_icon_style( $element, $section_id, $args ) {

		if ( ! is_object( $element ) ) {
			return;
		}
		
		$el_name = $element->get_name();
		
		if ( $el_name == 'nested-tabs' && $section_id == 'icon_section_style' ) {

			$element->add_responsive_control( 'tabs_icon_valign', [
				'label' => esc_html__( 'Vertical Alignment', 'trx_addons' ),
				'type' => \Elementor\Controls_Manager::CHOOSE,
				'options' => [
					'flex-start' => [
						'title' => esc_html__( 'Top', 'trx_addons' ),
						'icon' => 'eicon-v-align-top',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'trx_addons' ),
						'icon' => 'eicon-v-align-middle',
					],
					'flex-end' => [
						'title' => esc_html__( 'Bottom', 'trx_addons' ),
						'icon' => 'eicon-v-align-bottom',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .e-n-tabs' => '--n-tabs-title-align-items-toggle: {{VALUE}};',
				],
				'condition' => [
					'icon_position!' => ['block-start', 'block-end', 'top', 'bottom'],
				],
			] );

			$element->add_responsive_control(
				'tabs_icon_margin',
				[
					'label'        => __('Margin', 'trx_addons'),
					'type'         => \Elementor\Controls_Manager::DIMENSIONS,
					'size_units'   => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
					'selectors'    => [
						'{{WRAPPER}} .e-n-tab-icon' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					]
				]
			);
	
		}
	}
}

if ( ! function_exists( 'trx_addons_elm_nested_tabs_render_content' ) ) {
	add_filter( 'elementor/widget/render_content', 'trx_addons_elm_nested_tabs_render_content', 10, 2 );
	/**
	 * Add a subtitle to the tab's title in the Elementor Nested Tabs widget
	 * 
	 * @hooked elementor/widget/render_content
	 * 
	 * @param string $html     The tab's content.
	 * @param object $element  The tab's element object.
	 * 
	 * @return string  The tab's content with the subtitle.
	 */
	function trx_addons_elm_nested_tabs_render_content( $html, $element ) {
		if ( is_object( $element ) ) {
			$el_name = $element->get_name();
			if ( $el_name == 'nested-tabs' ) {
				// Get render attributes to get the tab's content ID for the further replacement
				// $render_atts = $element->get_render_attributes(); 
				// Get tabs settings
				$settings = $element->get_settings();
				if ( ! empty( $settings['tabs'] ) && is_array( $settings['tabs'] ) && ! empty( $settings['tabs_icon_placement'] ) ) {	// tabs_icon_placement is empty if a layout from cache
					$icon_position = ! empty( $settings['icon_position'] ) ? $settings['icon_position'] : 'inline-start';
					$icon_placement = in_array( $icon_position, array( 'block-start', 'block-end', 'top', 'bottom' ) ) ? 'inside' : $settings['tabs_icon_placement'];
					$title_tag = ! empty( $settings['tabs_title_html_tag'] ) ? $settings['tabs_title_html_tag'] : 'span';
					$subtitle_tag = $icon_placement == 'inside' && ! empty( $settings['tabs_subtitle_html_tag'] ) ? $settings['tabs_subtitle_html_tag'] : 'span';
					foreach( $settings['tabs'] as $k => $tab ) {
						// Add a subtitle to each tab
						$html = preg_replace_callback(
							'~(<button[^>]*class="e-n-tab-title[^>]*data-tab-index="' . ( $k + 1 ) . '"[^>]*>)([\s\S]*)(</button>)~U',
							function( $matches ) use ( $k, $tab, $icon_placement, $title_tag, $subtitle_tag ) {
								// Replace a tab's tag <button> with <div>
								$matches[1] = str_replace( '<button ', '<div tabindex="0" ', $matches[1] );
								$matches[3] = str_replace( '</button>', '</div>', $matches[3] );
								// Add a subtitle classes to the title wrapper
								if ( ! empty( $tab['tab_subtitle'] ) ) {
									$matches[1] = str_replace( 'class="e-n-tab-title', 'class="e-n-tab-title e-n-tab-title-with-subtitle e-n-tab-title-icon-placement-' . esc_attr( $icon_placement ), $matches[1] );
								}
								// Add a subtitle inside or after the title text (depends on the icon placement)
								$matches[2] = preg_replace_callback(
									'~(<span[^>]*class="e-n-tab-title-text[^>]*>)([\s\S]*)(</span>)~U',
									function( $matches ) use ( $k, $tab, $icon_placement, $title_tag, $subtitle_tag ) {
										// Replace a wrapper tag <span> with the custom tag
										if ( $title_tag != 'span' ) {
											$matches[1] = str_replace( '<span', '<' . $title_tag, $matches[1] );
											$matches[3] = str_replace( '</span>', '</' . $title_tag . '>', $matches[3] );
										}
										$output = '';
										// Add Subtitle layout
										$subtitle = ! empty( $tab['tab_subtitle'] )
													? '<' . $subtitle_tag . ' class="e-n-tab-subtitle">' . esc_html( $tab['tab_subtitle'] ) . '</' . $subtitle_tag . '>'
													: '';
										return $matches[1]
												. ( ! empty( $tab['tab_subtitle'] ) && $icon_placement == 'outside'
													? '<span class="e-n-tab-title-text-inner">' . $matches[2] . '</span>' . $subtitle
													: $matches[2]
													)
												. $matches[3]
												. ( ! empty( $tab['tab_subtitle'] ) && $icon_placement == 'inside' ? $subtitle : '' );
									},
									$matches[2]
								);
								return $matches[1] . $matches[2] . $matches[3];
							},
							$html
						);
					}
				}
			}
		}
		return $html;
	}
}
