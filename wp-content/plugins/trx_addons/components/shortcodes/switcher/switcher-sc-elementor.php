<?php
/**
 * Shortcode: Switcher (Elementor support)
 *
 * @package ThemeREX Addons
 * @since v2.6.0
 */

// Disable direct call
if ( ! defined( 'ABSPATH' ) ) { exit; }

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;


// Elementor Widget
//------------------------------------------------------
if ( ! function_exists( 'trx_addons_sc_switcher_add_in_elementor' ) ) {
	add_action( trx_addons_elementor_get_action_for_widgets_registration(), 'trx_addons_sc_switcher_add_in_elementor' );
	function trx_addons_sc_switcher_add_in_elementor() {
		
		if ( ! class_exists( 'TRX_Addons_Elementor_Widget' ) ) return;	

		class TRX_Addons_Elementor_Widget_Switcher extends TRX_Addons_Elementor_Widget {

			/**
			 * Retrieve widget name.
			 *
			 * @since 1.6.41
			 * @access public
			 *
			 * @return string Widget name.
			 */
			public function get_name() {
				return 'trx_sc_switcher';
			}

			/**
			 * Retrieve widget title.
			 *
			 * @since 1.6.41
			 * @access public
			 *
			 * @return string Widget title.
			 */
			public function get_title() {
				return __( 'Content Switcher', 'trx_addons' );
			}

			/**
			 * Get widget keywords.
			 *
			 * Retrieve the list of keywords the widget belongs to.
			 *
			 * @since 2.27.2
			 * @access public
			 *
			 * @return array Widget keywords.
			 */
			public function get_keywords() {
				return [ 'switcher', 'slider', 'carousel', 'content', 'tabs' ];
			}

			/**
			 * Retrieve widget icon.
			 *
			 * @since 1.6.41
			 * @access public
			 *
			 * @return string Widget icon.
			 */
			public function get_icon() {
				return 'eicon-adjust trx_addons_elementor_widget_icon';
			}

			/**
			 * Retrieve the list of categories the widget belongs to.
			 *
			 * Used to determine where to display the widget in the editor.
			 *
			 * @since 1.6.41
			 * @access public
			 *
			 * @return array Widget categories.
			 */
			public function get_categories() {
				return ['trx_addons-elements'];
			}

			/**
			 * Widget preview refresh button.
			 *
			 * @since 2.6.0
			 * @access public
			 */
			/*
			public function is_reload_preview_required() {
				return true;
			}
			*/

			/**
			 * Register widget controls.
			 *
			 * Adds different input fields to allow the user to change and customize the widget settings.
			 *
			 * @since 1.6.41
			 * @access protected
			 */
			protected function register_controls() {
				$this->register_content_controls();
				$this->register_style_controls();
			}

			/**
			 * Register widget content controls.
			 *
			 * Adds different input fields to allow the user to change and customize the widget settings.
			 *
			 * @since 1.6.41
			 * @access protected
			 */
			protected function register_content_controls() {

				// Detect edit mode
				$is_edit_mode = trx_addons_elm_is_edit_mode();

				// If open params in Elementor Editor
				$params = $this->get_sc_params();

				// Prepare lists
				$layouts = ! $is_edit_mode ? array() : trx_addons_array_merge(	array(
														0 => trx_addons_get_not_selected_text( __( 'Not selected', 'trx_addons' ) )
														),
													trx_addons_get_list_layouts()
													);
				$templates = ! $is_edit_mode ? array() : trx_addons_array_merge(	array(
														0 => trx_addons_get_not_selected_text( __( 'Not selected', 'trx_addons' ) )
														),
													trx_addons_get_list_elementor_templates()
													);
				$layout = 0;

				// Register controls
				$this->start_controls_section(
					'section_sc_switcher',
					[
						'label' => __( 'Switcher', 'trx_addons' ),
					]
				);

				$this->add_control(
					'type',
					[
						'label' => __( 'Layout', 'trx_addons' ),
						'label_block' => false,
						'type' => Controls_Manager::SELECT,
						'options' => apply_filters('trx_addons_sc_type', trx_addons_components_get_allowed_layouts('sc', 'switcher'), 'trx_sc_switcher'),
						'default' => 'default'
					]
				);

				$this->add_control(
					'effect',
					[
						'label' => __( 'Effect', 'trx_addons' ),
						'label_block' => false,
						'type' => Controls_Manager::SELECT,
						'options' => trx_addons_get_list_sc_switcher_effects(),
						'default' => 'fade'
					]
				);

				$this->add_control(
					'effect_duration',
					[
						'label'                 => __( 'Duration (s)', 'trx_addons' ),
						'description'           => __( 'Duration of the slide change effect (in seconds)', 'trx_addons' ),
						'type'                  => Controls_Manager::SLIDER,
						'default'               => [
							'unit' => 'px',
							'size' => 0.3,
						],
						'size_units' 			=> [ 'px' ],
						'range'                 => [
							'px' => [
								'min' => 0,
								'max' => 2,
								'step' => 0.1,
							],
						],
						'selectors'             => [
							'{{WRAPPER}} .sc_switcher' => '--trx-addons-switcher-effect-duration: {{SIZE}}s;',
						],
						'condition' => [
							'effect!' => 'none'
						],
					]
				);

				$this->add_control(
					'effect_delay',
					[
						'label'                 => __( 'Delay (s)', 'trx_addons' ),
						'description'           => __( 'Delay before the new active slide effect starts', 'trx_addons' ),
						'type'                  => Controls_Manager::SLIDER,
						'size_units' 			=> [ 'px' ],
						'range'                 => [
							'px' => [
								'min' => 0,
								'max' => 2,
								'step' => 0.1,
							],
						],
						'selectors'             => [
							'{{WRAPPER}} .sc_switcher' => '--trx-addons-switcher-effect-delay: {{SIZE}}s;',
						],
						'condition' => [
							'effect!' => 'none'
						],
					]
				);

				$this->add_control(
					'title_html_tag',
					array(
						'label'       => __( 'Label HTML Tag', 'trx_addons' ),
						'label_block' => false,
						'type'        => Controls_Manager::SELECT,
						'options'     => array(
							'h1'   => 'H1',
							'h2'   => 'H2',
							'h3'   => 'H3',
							'h4'   => 'H4',
							'h5'   => 'H5',
							'h6'   => 'H6',
							'div'  => 'div',
							'span' => 'span',
							'p'    => 'p',
						),
						'default'     => 'h3',
						'condition' => [
							'type' => ['default', 'modern']
						]
					)
				);
		
				$this->add_control(
					'autowidth',
					array(
						'label'        => __( 'Switcher Width', 'trx_addons' ),
						'type'         => Controls_Manager::SWITCHER,
						'label_on'     => __( 'Auto', 'trx_addons' ),
						'label_off'    => __( 'Fixed', 'trx_addons' ),
						'default'      => 'on',
						'return_value' => 'on',
						'condition' => [
							'type' => 'modern'
						]
					)
				);
		
				// Layout: Default, Modern
				$this->start_controls_tabs( 'tabs_switcher_controls' );

				$this->start_controls_tab(
					'tab_switcher_controls_slide1',
					array(
						'label' => __( 'Section 1', 'trx_addons' ),
						'condition' => [
							'type' => ['default', 'modern']
						]
					)
				);

				$this->add_control(
					'slide1_title',
					[
						'label' => __( 'Label', 'trx_addons' ),
						'label_block' => false,
						'type' => Controls_Manager::TEXT,
						'default' => '',
						'condition' => [
							'type' => ['default', 'modern']
						]
					]
				);

				$this->add_control(
					'slide1_type',
					[
						'label' => __("Content type", 'trx_addons'),
						'label_block' => false,
						'type' => Controls_Manager::SELECT,
						'options' => trx_addons_get_list_content_types( true ),
						'default' => 'section',
						'condition' => [
							'type' => ['default', 'modern']
						]
					]
				);

				$this->add_control(
					'slide1_content',
					[
						'label' => __( 'Content', 'trx_addons' ),
						'type' => Controls_Manager::WYSIWYG,
						//'separator' => 'none',
						'default' => '',
						'condition' => [
							'type' => ['default', 'modern'],
							'slide1_type' => 'content'
						]
					]
				);

				$this->add_control(
					'slide1_section',
					[
						'label' => __( 'Section ID', 'trx_addons' ),
						'label_block' => false,
						'type' => Controls_Manager::TEXT,
						'default' => '',
						'condition' => [
							'type' => ['default', 'modern'],
							'slide1_type' => 'section'
						]
					]
				);

				$this->add_control(
					'slide1_layout',
					[
						'label' => __("Custom Layout", 'trx_addons'),
						'label_block' => false,
						'description' => wp_kses( __("Select any previously created layout to insert to this page", 'trx_addons')
														. '<br>'
														. sprintf('<a href="%1$s" class="trx_addons_post_editor' . (intval($layout)==0 ? ' trx_addons_hidden' : '').'" target="_blank">%2$s</a>',
																	admin_url( sprintf( "post.php?post=%d&amp;action=elementor", $layout ) ),
																	__("Open selected layout in a new tab to edit", 'trx_addons')
																),
													'trx_addons_kses_content'
													),
						'type' => Controls_Manager::SELECT,
						'options' => $layouts,
						'default' => 0,
						'condition' => [
							'type' => ['default', 'modern'],
							'slide1_type' => 'layout'
						]
					]
				);

				$this->add_control(
					'slide1_template',
					[
						'label' => __("Elementor's Template", 'trx_addons'),
						'label_block' => false,
						'description' => wp_kses( __("Select any previously created template to insert to this page", 'trx_addons')
														. '<br>'
														. sprintf('<a href="%1$s" class="trx_addons_post_editor' . (intval($layout)==0 ? ' trx_addons_hidden' : '').'" target="_blank">%2$s</a>',
																	admin_url( sprintf( "post.php?post=%d&amp;action=elementor", $layout ) ),
																	__("Open selected template in a new tab to edit", 'trx_addons')
																),
													'trx_addons_kses_content'
													),
						'type' => Controls_Manager::SELECT,
						'options' => $templates,
						'default' => 0,
						'condition' => [
							'type' => ['default', 'modern'],
							'slide1_type' => 'template'
						]
					]
				);

				$this->end_controls_tab();

				$this->start_controls_tab(
					'tab_switcher_controls_slide2',
					array(
						'label' => __( 'Section 2', 'trx_addons' ),
						'condition' => [
							'type' => ['default', 'modern']
						]
					)
				);

				$this->add_control(
					'slide2_title',
					[
						'label' => __( 'Label', 'trx_addons' ),
						'label_block' => false,
						'type' => Controls_Manager::TEXT,
						'default' => '',
						'condition' => [
							'type' => ['default', 'modern']
						]
					]
				);

				$this->add_control(
					'slide2_type',
					[
						'label' => __("Content type", 'trx_addons'),
						'label_block' => false,
						'type' => Controls_Manager::SELECT,
						'options' => trx_addons_get_list_content_types( true ),
						'default' => 'section',
						'condition' => [
							'type' => ['default', 'modern']
						]
					]
				);

				$this->add_control(
					'slide2_content',
					[
						'label' => __( 'Content', 'trx_addons' ),
						'type' => Controls_Manager::WYSIWYG,
						//'separator' => 'none',
						'default' => '',
						'condition' => [
							'type' => ['default', 'modern'],
							'slide2_type' => 'content'
						]
					]
				);

				$this->add_control(
					'slide2_section',
					[
						'label' => __( 'Section ID', 'trx_addons' ),
						'label_block' => false,
						'type' => Controls_Manager::TEXT,
						'default' => '',
						'condition' => [
							'type' => ['default', 'modern'],
							'slide2_type' => 'section'
						]
					]
				);

				$this->add_control(
					'slide2_layout',
					[
						'label' => __("Custom Layout", 'trx_addons'),
						'label_block' => false,
						'description' => wp_kses( __("Select any previously created layout to insert to this page", 'trx_addons')
														. '<br>'
														. sprintf('<a href="%1$s" class="trx_addons_post_editor' . (intval($layout)==0 ? ' trx_addons_hidden' : '').'" target="_blank">%2$s</a>',
																	admin_url( sprintf( "post.php?post=%d&amp;action=elementor", $layout ) ),
																	__("Open selected layout in a new tab to edit", 'trx_addons')
																),
													'trx_addons_kses_content'
													),
						'type' => Controls_Manager::SELECT,
						'options' => $layouts,
						'default' => 0,
						'condition' => [
							'type' => ['default', 'modern'],
							'slide2_type' => 'layout'
						]
					]
				);

				$this->add_control(
					'slide2_template',
					[
						'label' => __("Elementor's Template", 'trx_addons'),
						'label_block' => false,
						'description' => wp_kses( __("Select any previously created template to insert to this page", 'trx_addons')
														. '<br>'
														. sprintf('<a href="%1$s" class="trx_addons_post_editor' . (intval($layout)==0 ? ' trx_addons_hidden' : '').'" target="_blank">%2$s</a>',
																	admin_url( sprintf( "post.php?post=%d&amp;action=elementor", $layout ) ),
																	__("Open selected template in a new tab to edit", 'trx_addons')
																),
													'trx_addons_kses_content'
													),
						'type' => Controls_Manager::SELECT,
						'options' => $templates,
						'default' => 0,
						'condition' => [
							'type' => ['default', 'modern'],
							'slide2_type' => 'template'
						]
					]
				);

				$this->end_controls_tab();

				$this->end_controls_tabs();

				// Layout: Tabs
				$this->add_control(
					'slides',
					[
						'label' => esc_html__( 'Tabs', 'trx_addons' ),
						'type' => Controls_Manager::REPEATER,
						'condition' => [
							'type' => 'tabs'
						],
						'default' => apply_filters('trx_addons_sc_param_group_value', [
							[
								'slide_title' => esc_html__( 'Tab 1', 'trx_addons' ),
								'slide_type' => 'section',
								'slide_section' => '',
								'slide_layout' => 0,
								'slide_template' => 0,
							],
							[
								'slide_title' => esc_html__( 'Tab 2', 'trx_addons' ),
								'slide_type' => 'section',
								'slide_section' => '',
								'slide_layout' => 0,
								'slide_template' => 0,
							],
							[
								'slide_title' => esc_html__( 'Tab 3', 'trx_addons' ),
								'slide_type' => 'section',
								'slide_section' => '',
								'slide_layout' => 0,
								'slide_template' => 0,
							],
						], 'trx_sc_switcher'),
						'fields' => apply_filters('trx_addons_sc_param_group_params', array_merge(
							[
								[
									'name' => 'slide_title',
									'label' => __( 'Label', 'trx_addons' ),
									'type' => Controls_Manager::TEXT,
									//'separator' => 'none',
									'default' => '',
								],
								[
									'name' => 'slide_type',
									'label' => __("Content type", 'trx_addons'),
									'label_block' => false,
									'type' => Controls_Manager::SELECT,
									'options' => trx_addons_get_list_content_types( true ),
									'default' => 'section',
								],
								[
									'name' => 'slide_content',
									'label' => __( 'Content', 'trx_addons' ),
									'type' => Controls_Manager::WYSIWYG,
									//'separator' => 'none',
									'default' => '',
									'condition' => [
										'slide_type' => 'content'
									]
								],
								[
									'name' => 'slide_section',
									'label' => __( 'Section ID', 'trx_addons' ),
									'label_block' => false,
									'type' => Controls_Manager::TEXT,
									'default' => '',
									'condition' => [
										'slide_type' => 'section'
									]
								],
								[
									'name' => 'slide_layout',
									'label' => __("Custom Layout", 'trx_addons'),
									'label_block' => false,
									'description' => wp_kses( __("Select any previously created layout to insert to this page", 'trx_addons')
																	. '<br>'
																	. sprintf('<a href="%1$s" class="trx_addons_post_editor' . (intval($layout)==0 ? ' trx_addons_hidden' : '').'" target="_blank">%2$s</a>',
																				admin_url( sprintf( "post.php?post=%d&amp;action=elementor", $layout ) ),
																				__("Open selected layout in a new tab to edit", 'trx_addons')
																			),
																'trx_addons_kses_content'
																),
									'type' => Controls_Manager::SELECT,
									'options' => $layouts,
									'default' => 0,
									'condition' => [
										'slide_type' => 'layout'
									]
								],
								[
									'name' => 'slide_template',
									'label' => __("Elementor's Template", 'trx_addons'),
									'label_block' => false,
									'description' => wp_kses( __("Select any previously created template to insert to this page", 'trx_addons')
																	. '<br>'
																	. sprintf('<a href="%1$s" class="trx_addons_post_editor' . (intval($layout)==0 ? ' trx_addons_hidden' : '').'" target="_blank">%2$s</a>',
																				admin_url( sprintf( "post.php?post=%d&amp;action=elementor", $layout ) ),
																				__("Open selected template in a new tab to edit", 'trx_addons')
																			),
																'trx_addons_kses_content'
																),
									'type' => Controls_Manager::SELECT,
									'options' => $templates,
									'default' => 0,
									'condition' => [
										'slide_type' => 'template'
									]
								]
							] ),
							'trx_sc_switcher' 
						),
						'title_field' => '{{{ slide_title }}}'
					]
				);

				$this->end_controls_section();

				if ( apply_filters( 'trx_addons_filter_add_title_param', true, $this->get_name() ) ) {
					$this->add_title_param();
				}
			}

			/*-----------------------------------------------------------------------------------*/
			/*	STYLE TAB
			/*-----------------------------------------------------------------------------------*/

			protected function register_style_controls() {

				$this->start_controls_section(
					'section_sc_switcher_style',
					[
						'label' => esc_html__( 'Switcher', 'trx_addons' ),
						'tab'   => Controls_Manager::TAB_STYLE,
					]
				);

				$this->add_group_control(
					Group_Control_Typography::get_type(),
					array(
						'name'      => 'switcher_typography',
						'label'     => __( 'Typography', 'trx_addons' ),
						// 'global'    => [
						// 	'default' => Global_Typography::TYPOGRAPHY_ACCENT,
						// ],
						'selector'  => '{{WRAPPER}} .sc_switcher_controls_section_title,
										{{WRAPPER}} .sc_switcher_tab',
					)
				);

				// Style for Default, Modern
				//----------------------------------------------

				$this->add_responsive_control(
					'switcher_height',
					[
						'label'                 => __( 'Height', 'trx_addons' ),
						'type'                  => Controls_Manager::SLIDER,
						'size_units' 			=> [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
						'range'                 => [
							'px' => [
								'min' => 0,
								'max' => 100,
							],
							'em' => [
								'min' => 0,
								'max' => 10,
								'step' => 0.1,
							],
							'rem' => [
								'min' => 0,
								'max' => 10,
								'step' => 0.1,
							],
							'vw' => [
								'min' => 0,
								'max' => 100,
								'step' => 0.1,
							],
							'vh' => [
								'min' => 0,
								'max' => 100,
								'step' => 0.1,
							],
						],
						'selectors'             => [
							'{{WRAPPER}} .sc_switcher_controls' => '--trx-addons-switcher-toggle-height: {{SIZE}}{{UNIT}};',
						],
						'condition' => [
							'type' => 'default'
						],
					]
				);

				$this->add_control(
					'switcher_bg_color',
					[
						'label' => __( 'Background Color', 'trx_addons' ),
						'label_block' => false,
						'type' => Controls_Manager::COLOR,
						'default' => '',
//						'global' => array(
//							'active' => false,
//						),
						'condition' => [
							'type' => ['default', 'modern']
						],
						'selectors' => [
							'{{WRAPPER}} .sc_switcher_controls_toggle' => 'background-color: {{VALUE}};',
						],
					]
				);

				$this->add_responsive_control(
					'switcher_border_radius',
					array(
						'label'      => __( 'Border Radius', 'trx_addons' ),
						'type'       => Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
						'condition' => [
							'type' => ['default', 'modern']
						],
						'selectors'  => array(
							'{{WRAPPER}} .sc_switcher_controls_toggle,
							 {{WRAPPER}} .sc_switcher_controls_toggle_button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						),
					)
				);

				$this->add_responsive_control(
					'switcher_title_padding',
					array(
						'label'      => __( 'Label Padding', 'trx_addons' ),
						'type'       => Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
						'condition' => [
							'type' => ['default', 'modern']
						],
						'selectors'  => array(
							'{{WRAPPER}} .sc_switcher_controls_section1,
							 {{WRAPPER}} .sc_switcher_controls_section2' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						),
					)
				);

				$this->add_responsive_control(
					'switcher_margin',
					array(
						'label'      => __( 'Switcher Margin', 'trx_addons' ),
						'type'       => Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
						'condition' => [
							'type' => ['default', 'modern']
						],
						'selectors'  => array(
							'{{WRAPPER}} .sc_switcher_controls' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						),
					)
				);
		
				$this->add_group_control(
					Group_Control_Box_Shadow::get_type(),
					[
						'name'     => 'switcher_box_shadow',
						'selector' => '{{WRAPPER}} .sc_switcher_controls_toggle',
						'condition' => [
							'type' => ['default', 'modern']
						],
					]
				);

				$this->start_controls_tabs( 'tabs_switcher_controls_styles' );

				$this->start_controls_tab(
					'tab_switcher_controls_style__slide1',
					array(
						'label' => __( 'Section 1', 'trx_addons' ),
						'condition' => [
							'type' => ['default', 'modern']
						]
					)
				);
		
				$this->add_control(
					'slide1_title_color',
					[
						'label' => __( 'Label Color', 'trx_addons' ),
						'label_block' => false,
						'type' => Controls_Manager::COLOR,
						'default' => '',
//						'global' => array(
//							'active' => false,
//						),
						'condition' => [
							'type' => ['default', 'modern']
						],
						'selectors' => [
							'{{WRAPPER}} .sc_switcher:not(.sc_switcher_toggle_on) .sc_switcher_controls_section1 .sc_switcher_controls_section_title' => 'color: {{VALUE}};',
						],
					]
				);
		
				$this->add_control(
					'slide1_title_hover',
					[
						'label' => __( 'Label Hover', 'trx_addons' ),
						'label_block' => false,
						'type' => Controls_Manager::COLOR,
						'default' => '',
//						'global' => array(
//							'active' => false,
//						),
						'condition' => [
							'type' => ['default', 'modern']
						],
						'selectors' => [
							'{{WRAPPER}} .sc_switcher:not(.sc_switcher_toggle_on) .sc_switcher_controls_section1:hover .sc_switcher_controls_section_title' => 'color: {{VALUE}};',
						],
					]
				);

				$this->add_control(
					'slide1_active_color',
					[
						'label' => __( 'Active Color', 'trx_addons' ),
						'label_block' => false,
						'type' => Controls_Manager::COLOR,
						'default' => '',
//						'global' => array(
//							'active' => false,
//						),
						'condition' => [
							'type' => ['default', 'modern']
						],
						'selectors' => [
							'{{WRAPPER}} .sc_switcher_toggle_on .sc_switcher_controls_section1 .sc_switcher_controls_section_title' => 'color: {{VALUE}};',
						],
					]
				);

				$this->add_control(
					'slide1_switcher_color',
					[
						'label' => __( 'Switcher Color', 'trx_addons' ),
						'label_block' => false,
						'type' => Controls_Manager::COLOR,
						'default' => '',
//						'global' => array(
//							'active' => false,
//						),
						'condition' => [
							'type' => ['default', 'modern']
						],
						'selectors' => [
							'{{WRAPPER}} .sc_switcher_controls_toggle_on .sc_switcher_controls_toggle_button' => 'background-color: {{VALUE}};',
						],
					]
				);

				$this->end_controls_tab();

				$this->start_controls_tab(
					'tab_switcher_controls_style_slide2',
					array(
						'label' => __( 'Section 2', 'trx_addons' ),
						'condition' => [
							'type' => ['default', 'modern']
						]
					)
				);

				$this->add_control(
					'slide2_title_color',
					[
						'label' => __( 'Label Color', 'trx_addons' ),
						'label_block' => false,
						'type' => Controls_Manager::COLOR,
						'default' => '',
//						'global' => array(
//							'active' => false,
//						),
						'condition' => [
							'type' => ['default', 'modern']
						],
						'selectors' => [
							'{{WRAPPER}} .sc_switcher_toggle_on .sc_switcher_controls_section2 .sc_switcher_controls_section_title' => 'color: {{VALUE}};',
						],
					]
				);

				$this->add_control(
					'slide2_title_hover',
					[
						'label' => __( 'Label Hover', 'trx_addons' ),
						'label_block' => false,
						'type' => Controls_Manager::COLOR,
						'default' => '',
//						'global' => array(
//							'active' => false,
//						),
						'condition' => [
							'type' => ['default', 'modern']
						],
						'selectors' => [
							'{{WRAPPER}} .sc_switcher_toggle_on .sc_switcher_controls_section2:hover .sc_switcher_controls_section_title' => 'color: {{VALUE}};',
						],
					]
				);

				$this->add_control(
					'slide2_active_color',
					[
						'label' => __( 'Active Color', 'trx_addons' ),
						'label_block' => false,
						'type' => Controls_Manager::COLOR,
						'default' => '',
//						'global' => array(
//							'active' => false,
//						),
						'condition' => [
							'type' => ['default', 'modern']
						],
						'selectors' => [
							'{{WRAPPER}} .sc_switcher:not(.sc_switcher_toggle_on) .sc_switcher_controls_section2 .sc_switcher_controls_section_title' => 'color: {{VALUE}};',
						],
					]
				);

				$this->add_control(
					'slide2_switcher_color',
					[
						'label' => __( 'Switcher Color', 'trx_addons' ),
						'label_block' => false,
						'type' => Controls_Manager::COLOR,
						'default' => '',
//						'global' => array(
//							'active' => false,
//						),
						'condition' => [
							'type' => ['default', 'modern']
						],
						'selectors' => [
							'{{WRAPPER}} .sc_switcher_controls_toggle:not(.sc_switcher_controls_toggle_on) .sc_switcher_controls_toggle_button' => 'background-color: {{VALUE}};',
						],
					]
				);

				$this->end_controls_tab();

				$this->end_controls_tabs();


				// Style for Tabs
				//----------------------------------------------

				$this->start_controls_tabs( 'tabs_button_style' );

				$this->start_controls_tab(
					'switcher_tab_normal',
					[
						'label'      => __( 'Normal', 'trx_addons' ),
						'condition'  => [
							'type' => 'tabs',
						],
					]
				);

				$this->add_control(
					'switcher_tab_color',
					[
						'label' => __( 'Text Color', 'trx_addons' ),
						'label_block' => false,
						'type' => Controls_Manager::COLOR,
						'default' => '',
//						'global' => array(
//							'active' => false,
//						),
						'condition' => [
							'type' => 'tabs'
						],
						'selectors' => [
							'{{WRAPPER}} .sc_switcher_tab' => 'color: {{VALUE}};',
						],
					]
				);

				$this->add_control(
					'switcher_tab_bg_color',
					[
						'label' => __( 'Background Color', 'trx_addons' ),
						'label_block' => false,
						'type' => Controls_Manager::COLOR,
						'default' => '',
//						'global' => array(
//							'active' => false,
//						),
						'condition' => [
							'type' => 'tabs'
						],
						'selectors' => [
							'{{WRAPPER}} .sc_switcher_tab' => 'background-color: {{VALUE}};',
						],
					]
				);

				$this->add_group_control(
					Group_Control_Border::get_type(),
					[
						'name'     => 'switcher_tab_border',
						'label'    => esc_html__( 'Border', 'trx_addons' ),
						'selector' => '{{WRAPPER}} .sc_switcher_tab',
						'condition' => [
							'type' => 'tabs'
						],
					]
				);

				$this->add_responsive_control(
					'switcher_tab_border_radius',
					[
						'label'      => esc_html__( 'Border Radius', 'trx_addons' ),
						'type'       => Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
						'selectors'  => [
							'{{WRAPPER}} .sc_switcher_tab' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
						'condition' => [
							'type' => 'tabs'
						],
					]
				);

				$this->add_responsive_control(
					'switcher_tab_padding',
					[
						'label'      => esc_html__( 'Padding', 'trx_addons' ),
						'type'       => Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
						'selectors'  => [
							'{{WRAPPER}} .sc_switcher_tab' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
						'condition' => [
							'type' => 'tabs'
						],
					]
				);

				$this->add_group_control(
					Group_Control_Box_Shadow::get_type(),
					[
						'name'     => 'switcher_tab_box_shadow',
						'selector' => '{{WRAPPER}} .sc_switcher_tab',
						'condition' => [
							'type' => 'tabs'
						],
					]
				);

				$this->add_responsive_control(
					'switcher_tab_space',
					[
						'label'                 => __( 'Space between tabs', 'trx_addons' ),
						'type'                  => Controls_Manager::SLIDER,
						'size_units' 			=> [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
						'range'                 => [
							'px' => [
								'min' => 0,
								'max' => 100,
							],
							'em' => [
								'min' => 0,
								'max' => 10,
								'step' => 0.1,
							],
							'rem' => [
								'min' => 0,
								'max' => 10,
								'step' => 0.1,
							],
							'vw' => [
								'min' => 0,
								'max' => 100,
								'step' => 0.1,
							],
						],
						'selectors'             => [
							'{{WRAPPER}} .sc_switcher_tab + .sc_switcher_tab' => 'margin-left: {{SIZE}}{{UNIT}};',
						],
						'condition' => [
							'type' => 'tabs'
						],
					]
				);
		
				$this->end_controls_tab();

				$this->start_controls_tab(
					'switcher_tab_hover',
					[
						'label'      => __( 'Hover', 'trx_addons' ),
						'condition'  => [
							'type' => 'tabs',
						],
					]
				);

				$this->add_control(
					'switcher_tab_hover_color',
					[
						'label' => __( 'Tab Color', 'trx_addons' ),
						'label_block' => false,
						'type' => Controls_Manager::COLOR,
						'default' => '',
//						'global' => array(
//							'active' => false,
//						),
						'condition' => [
							'type' => 'tabs'
						],
						'selectors' => [
							'{{WRAPPER}} .sc_switcher_tab:hover' => 'color: {{VALUE}};',
						],
					]
				);

				$this->add_control(
					'switcher_tab_hover_bg_color',
					[
						'label' => __( 'Background Color', 'trx_addons' ),
						'label_block' => false,
						'type' => Controls_Manager::COLOR,
						'default' => '',
//						'global' => array(
//							'active' => false,
//						),
						'condition' => [
							'type' => 'tabs'
						],
						'selectors' => [
							'{{WRAPPER}} .sc_switcher_tab:hover' => 'background-color: {{VALUE}};',
						],
					]
				);

				$this->add_control(
					'switcher_tab_hover_bd_color',
					[
						'label' => __( 'Border Color', 'trx_addons' ),
						'label_block' => false,
						'type' => Controls_Manager::COLOR,
						'default' => '',
//						'global' => array(
//							'active' => false,
//						),
						'condition' => [
							'type' => 'tabs'
						],
						'selectors' => [
							'{{WRAPPER}} .sc_switcher_tab:hover' => 'border-color: {{VALUE}};',
						],
					]
				);

				$this->end_controls_tab();

				$this->start_controls_tab(
					'switcher_tab_active',
					[
						'label'      => __( 'Active', 'trx_addons' ),
						'condition'  => [
							'type' => 'tabs',
						],
					]
				);

				$this->add_control(
					'switcher_tab_active_color',
					[
						'label' => __( 'Tab Color', 'trx_addons' ),
						'label_block' => false,
						'type' => Controls_Manager::COLOR,
						'default' => '',
	//						'global' => array(
	//							'active' => false,
	//						),
						'condition' => [
							'type' => 'tabs'
						],
						'selectors' => [
							'{{WRAPPER}} .sc_switcher_tab_active' => 'color: {{VALUE}};',
						],
					]
				);

				$this->add_control(
					'switcher_tab_active_bg_color',
					[
						'label' => __( 'Background Color', 'trx_addons' ),
						'label_block' => false,
						'type' => Controls_Manager::COLOR,
						'default' => '',
	//						'global' => array(
	//							'active' => false,
	//						),
						'condition' => [
							'type' => 'tabs'
						],
						'selectors' => [
							'{{WRAPPER}} .sc_switcher_tab_active' => 'background-color: {{VALUE}};',
						],
					]
				);

				$this->add_control(
					'switcher_tab_active_bd_color',
					[
						'label' => __( 'Border Color', 'trx_addons' ),
						'label_block' => false,
						'type' => Controls_Manager::COLOR,
						'default' => '',
	//						'global' => array(
	//							'active' => false,
	//						),
						'condition' => [
							'type' => 'tabs'
						],
						'selectors' => [
							'{{WRAPPER}} .sc_switcher_tab_active' => 'border-color: {{VALUE}};',
						],
					]
				);

				$this->end_controls_tab();

				$this->end_controls_tabs();
		
				$this->end_controls_section();
			}

		}
		
		// Register widget
		trx_addons_elm_register_widget( 'TRX_Addons_Elementor_Widget_Switcher' );
	}
}

if ( ! function_exists( 'trx_addons_sc_switcher_disable_cache_if_catch_output' ) ) {
	add_filter( 'elementor/frontend/builder_content_data', 'trx_addons_sc_switcher_disable_cache_if_catch_output', 10, 2 );
	/**
	 * Disable Elementor's cache if the switcher is present on the current page and any slide content contains a section
	 * 
	 * @hooked elementor/frontend/builder_content_data
	 * 
	 * @param array $data       The content data.
	 * @param int   $post_id    The post ID.
	 * @param bool  $recursive  The flag about recursive call.
	 * 
	 * @return array  The content data.
	 */
	function trx_addons_sc_switcher_disable_cache_if_catch_output( $data, $post_id, $recursive = false ) {
		global $TRX_ADDONS_STORAGE;
		static $checked = array();
		if ( ! $recursive && ( ! trx_addons_elm_is_experiment_active( 'e_element_cache' ) || in_array( $post_id, $checked ) ) ) {
			return $data;
		}
		$checked[] = $post_id;
		$need_clear_cache = false;
		if ( is_array( $data ) && empty( $TRX_ADDONS_STORAGE['should_render_shortcode'][ $post_id ] ) ) {
			for ( $i = 0; $i < count( $data ); $i++ ) {
				if ( ! empty( $data[ $i ]['elements'] ) && is_array( $data[ $i ]['elements'] ) && count( $data[ $i ]['elements'] ) > 0 ) {
					trx_addons_sc_switcher_disable_cache_if_catch_output( $data[ $i ]['elements'], $post_id, true );
				} else if ( ! empty( $data[ $i ]['widgetType'] ) && $data[ $i ]['widgetType'] == 'trx_sc_switcher' ) {
					if ( ! empty( $data[ $i ]['settings']['type'] ) && $data[ $i ]['settings']['type'] == 'tabs' ) {
						if ( is_array( $data[ $i ]['settings']['slides'] ) ) {
							for ( $t = 0; $t < count( $data[ $i ]['settings']['slides'] ); $t++ ) {
								if ( ! empty( $data[ $i ]['settings']['slides'][ $t ]['slide_type'] ) && $data[ $i ]['settings']['slides'][ $t ]['slide_type'] == 'section' ) {
									$need_clear_cache = true;
									break;
								}
							}
						}
					} else if ( ! empty( $data[ $i ]['settings']['slide1_type'] ) && $data[ $i ]['settings']['slide1_type'] == 'section' ) {
						$need_clear_cache = true;
					} else if ( ! empty( $data[ $i ]['settings']['slide2_type'] ) && $data[ $i ]['settings']['slide2_type'] == 'section' ) {
						$need_clear_cache = true;
					}
					if ( $need_clear_cache ) {
						break;
					}
				}
			}
		}
		if ( $need_clear_cache ) {
			trx_addons_elm_clear_document_cache( $post_id );
			add_filter( 'elementor/element/should_render_shortcode', '__return_false', 100 );
			if ( ! isset( $TRX_ADDONS_STORAGE['should_render_shortcode'] ) ) {
				$TRX_ADDONS_STORAGE['should_render_shortcode'] = array();
			}
			$TRX_ADDONS_STORAGE['should_render_shortcode'][ $post_id ] = true;
		}
		return $data;
	}
}

if ( ! function_exists( 'trx_addons_sc_switcher_enable_cache_after_catch_output' ) ) {
	add_action( 'elementor/frontend/get_builder_content', 'trx_addons_sc_switcher_enable_cache_after_catch_output', 10, 3 );
	/**
	 * Enable Elementor's cache after the page content is rendered
	 * 
	 * @hooked elementor/frontend/get_builder_content
	 * 
	 * @param Elementor\Document $document   The document.
	 * @param bool               $is_excerpt The flag about excerpt.
	 * @param bool               $with_css   The flag about CSS.
	 */
	function trx_addons_sc_switcher_enable_cache_after_catch_output( $document, $is_excerpt, $with_css ) {
		global $TRX_ADDONS_STORAGE;
		$post_id = $document->get_main_id();
		if ( ! empty( $TRX_ADDONS_STORAGE['should_render_shortcode'][ $post_id ] ) ) {
			remove_filter( 'elementor/element/should_render_shortcode', '__return_false', 100 );
			unset( $TRX_ADDONS_STORAGE['should_render_shortcode'][ $post_id ] );
		}
	}
}

// Start buffering output of the sections with specified id
if ( ! function_exists( 'trx_addons_sc_switcher_start_catch_output' ) ) {
	// Before Elementor 2.1.0
	add_action( 'elementor/frontend/element/before_render',  'trx_addons_sc_switcher_start_catch_output', 10, 1 );
	// After Elementor 2.1.0
	add_action( 'elementor/frontend/section/before_render', 'trx_addons_sc_switcher_start_catch_output', 10, 1 );
	// After Elementor 3.16.0
	add_action( 'elementor/frontend/container/before_render', 'trx_addons_sc_switcher_start_catch_output', 10, 1 );
	function trx_addons_sc_switcher_start_catch_output( $element ) {
		global $TRX_ADDONS_STORAGE;
		if ( ! empty( $TRX_ADDONS_STORAGE['capture_page'] ) && ! trx_addons_is_preview( 'elementor' ) ) {
			if ( is_object( $element ) && in_array( $element->get_name(), array( 'section', 'container' ) ) ) {
				$id = $element->get_settings( '_element_id' );
				if ( ! empty( $id ) && ! empty( $TRX_ADDONS_STORAGE['catch_output']['sc_switcher'][ $id ] ) ) {
					ob_start();
				}
			}
		}
	}
}


// End buffering output of the sections with specified id
if ( ! function_exists( 'trx_addons_sc_switcher_end_catch_output' ) ) {
	// Before Elementor 2.1.0
	add_action( 'elementor/frontend/element/after_render',  'trx_addons_sc_switcher_end_catch_output', 10, 1 );
	// After Elementor 2.1.0
	add_action( 'elementor/frontend/section/after_render', 'trx_addons_sc_switcher_end_catch_output', 10, 1 );
	// After Elementor 3.16.0
	add_action( 'elementor/frontend/container/after_render', 'trx_addons_sc_switcher_end_catch_output', 10, 1 );
	function trx_addons_sc_switcher_end_catch_output( $element ) {
		global $TRX_ADDONS_STORAGE;
		if ( ! empty( $TRX_ADDONS_STORAGE['capture_page'] ) && ! trx_addons_is_preview( 'elementor' ) ) {
			if ( is_object( $element ) && in_array( $element->get_name(), array( 'section', 'container' ) ) ) {
				$id = $element->get_settings( '_element_id' );
				if ( ! empty( $id ) && ! empty( $TRX_ADDONS_STORAGE['catch_output']['sc_switcher'][ $id ] ) ) {
					$TRX_ADDONS_STORAGE['catch_output']['sc_switcher'][ $id ] = ob_get_contents();
					ob_end_clean();
				}
			}
		}
	}
}


// Paste buffer to the sections with specified id
if ( ! function_exists( 'trx_addons_sc_switcher_paste_catch_output' ) ) {
	add_filter( 'trx_addons_filter_page_content', 'trx_addons_sc_switcher_paste_catch_output', 10, 1 );
	function trx_addons_sc_switcher_paste_catch_output( $output ) {
		global $TRX_ADDONS_STORAGE;
		if ( ! trx_addons_is_preview( 'elementor' ) ) {
			if ( ! empty( $TRX_ADDONS_STORAGE['catch_output']['sc_switcher'] ) && is_array( $TRX_ADDONS_STORAGE['catch_output']['sc_switcher'] ) ) {
				foreach( $TRX_ADDONS_STORAGE['catch_output']['sc_switcher'] as $id => $html ) {
					$output = preg_replace(
						'/(<div[^>]*class="sc_switcher_section[^>]*data-section="' . esc_attr( $id ) . '"[^>]*>)[\s]*<\/div>/',
						'${1}' . $html . '</div>',
						$output );
				}
			}
		}
		return $output;
	}
}
