<?php
/**
 * Shortcode: Images compare (Elementor support)
 *
 * @package ThemeREX Addons
 * @since v1.97.0
 */

// Disable direct call
if ( ! defined( 'ABSPATH' ) ) { exit; }




// Elementor Widget
//------------------------------------------------------
if ( ! function_exists( 'trx_addons_sc_icompare_add_in_elementor' ) ) {
	add_action( trx_addons_elementor_get_action_for_widgets_registration(), 'trx_addons_sc_icompare_add_in_elementor' );
	function trx_addons_sc_icompare_add_in_elementor() {
		
		if ( ! class_exists( 'TRX_Addons_Elementor_Widget' ) ) return;	

		class TRX_Addons_Elementor_Widget_Icompare extends TRX_Addons_Elementor_Widget {

			/**
			 * Widget base constructor.
			 *
			 * Initializing the widget base class.
			 *
			 * @since 1.6.41
			 * @access public
			 *
			 * @param array      $data Widget data. Default is an empty array.
			 * @param array|null $args Optional. Widget default arguments. Default is null.
			 */
			public function __construct( $data = [], $args = null ) {
				parent::__construct( $data, $args );
				$this->add_plain_params([
					'image1' => 'url',
					'image2' => 'url',
					'handler_image' => 'url',
					'handler_pos' => 'size',
					'handler_size' => 'size+units',
					'handler_icon_size' => 'size+units',
					'handler_image_size' => 'size+units',
					'handler_border_size' => 'size+units',
				]);
			}

			/**
			 * Retrieve widget name.
			 *
			 * @since 1.6.41
			 * @access public
			 *
			 * @return string Widget name.
			 */
			public function get_name() {
				return 'trx_sc_icompare';
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
				return __( 'Images Compare', 'trx_addons' );
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
				return [ 'icompare', 'image', 'compare', 'before', 'after' ];
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
				return 'eicon-image-before-after trx_addons_elementor_widget_icon';
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
			 * Register widget controls.
			 *
			 * Adds different input fields to allow the user to change and customize the widget settings.
			 *
			 * @since 1.6.41
			 * @access protected
			 */
			protected function register_controls() {
				$this->register_content_controls();
				$this->register_style_controls_image();
				$this->register_style_controls_handler();
				$this->register_style_controls_before_after();

				if ( apply_filters( 'trx_addons_filter_add_title_param', true, $this->get_name() ) ) {
					$this->add_title_param();
				}
			}

			/**
			 * Register widget content controls.
			 *
			 * @access protected
			 */
			protected function register_content_controls() {

				// Detect edit mode
				$is_edit_mode = trx_addons_elm_is_edit_mode();

				// Register controls
				$this->start_controls_section(
					'section_sc_icompare',
					[
						'label' => __( 'Images Compare', 'trx_addons' ),
					]
				);

				$this->add_control(
					'type',
					[
						'label' => __( 'Layout', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => apply_filters('trx_addons_sc_type', trx_addons_components_get_allowed_layouts('sc', 'icompare'), 'trx_sc_icompare'),
						'default' => 'default'
					]
				);

				$this->add_control(
					'image1',
					[
						'label' => __( 'Image 1 (before)', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::MEDIA,
						'default' => [ 'url' => '' ]
					]
				);

				$this->add_control(
					'image2',
					[
						'label' => __( 'Image 2 (after)', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::MEDIA,
						'default' => [ 'url' => '' ]
					]
				);

				$this->add_control(
					'direction',
					[
						'label' => __( 'Direction', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => ! $is_edit_mode ? array() : trx_addons_get_list_sc_directions(),
						'default' => 'vertical'
					]
				);

				$this->add_control(
					'event',
					[
						'label' => __( 'Move on', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => ! $is_edit_mode ? array() : trx_addons_get_list_sc_mouse_events(),
						'default' => 'drag'
					]
				);

				$this->add_control(
					'handler_separator',
					[
						'label' => __( 'Show separator', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SWITCHER,
						'label_off' => __( 'Off', 'trx_addons' ),
						'label_on' => __( 'On', 'trx_addons' ),
						'return_value' => '1'
					]
				);

				$this->end_controls_section();
			}

			/**
			 * Register an image style controls.
			 *
			 * @access protected
			 */
			protected function register_style_controls_image() {

				$this->start_controls_section(
					'section_sc_icompare_image_style',
					[
						'label' => __( 'Image style', 'trx_addons' ),
						'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
					]
				);

				$this->add_group_control(
					\Elementor\Group_Control_Border::get_type(),
					[
						'name'     => 'image_border',
						'label'    => esc_html__( 'Border', 'trx_addons' ),
						'selector' => '{{WRAPPER}} .sc_icompare_content'
					]
				);

				$this->add_responsive_control(
					'image_border_radius',
					[
						'label'      => esc_html__( 'Border Radius', 'trx_addons' ),
						'type'       => \Elementor\Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
						'selectors'  => [
							'{{WRAPPER}} .sc_icompare_content' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
					]
				);

				$this->add_group_control(
					\Elementor\Group_Control_Box_Shadow::get_type(),
					[
						'name'      => 'image_box_shadow',
						'selector'  => '{{WRAPPER}} .sc_icompare_content',
					]
				);

				$this->end_controls_section();
			}

			/**
			 * Register a handler style controls.
			 *
			 * @access protected
			 */
			protected function register_style_controls_handler() {

				// Detect edit mode
				$is_edit_mode = trx_addons_elm_is_edit_mode();

				$this->start_controls_section(
					'section_sc_icompare_handler_style',
					[
						'label' => __( 'Handler style', 'trx_addons' ),
						'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
					]
				);

				$this->add_control(
					'handler',
					[
						'label' => __( 'Handler style', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => ! $is_edit_mode ? array() : trx_addons_get_list_sc_icompare_handlers(),
						'default' => 'round'
					]
				);

				$this->add_control(
					'handler_pos',
					[
						'label' => __( 'Handler position (in %)', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::SLIDER,
						'default' => [
							'size' => 50,
							'unit' => '%'
						],
						'range' => [
							'%' => [
								'min' => 0,
								'max' => 100,
								'step' => 0.1
							],
						],
						'size_units' => [ '%' ]
					]
				);

				$this->add_control(
					'handler_size',
					[
						'label' => __( 'Handler size', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::SLIDER,
						'default' => [
							'size' => 50,
							'unit' => 'px'
						],
						'range' => [
							'px' => [
								'min' => 0,
								'max' => 100,
								'step' => 1
							],
							'em' => [
								'min' => 0,
								'max' => 10,
								'step' => 0.1
							],
						],
						'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
						'selectors' => [
							'{{WRAPPER}} .sc_icompare_handler' => '--sc-icompare-handler-size: {{SIZE}}{{UNIT}};',
						]
					]
				);

				$this->add_control(
					'handler_image',
					[
						'label' => __( 'Handler image', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::MEDIA,
						'default' => [ 'url' => '' ]
					]
				);

				$this->add_control(
					'handler_image_size',
					[
						'label' => __( 'Image size', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::SLIDER,
						'default' => [
							'size' => 100,
							'unit' => '%'
						],
						'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
						'selectors' => [
							'{{WRAPPER}} .sc_icompare_handler_image' => 'width: {{SIZE}}{{UNIT}};',
						],
						'condition' => [
							'handler_image[url]!' => ''
						],
					]
				);

				$params = trx_addons_get_icon_param('icon');
				$params = trx_addons_array_get_first_value( $params );
				unset( $params['name'] );
				$this->add_control( 'icon', $params );

				$this->add_control(
					'handler_icon_size',
					[
						'label' => __( 'Icon size', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::SLIDER,
						'default' => [
							'size' => 1.5,
							'unit' => 'em'
						],
						'range' => [
							'px' => [
								'min' => 1,
								'max' => 100,
								'step' => 1
							],
							'em' => [
								'min' => 0.1,
								'max' => 10,
								'step' => 0.1
							],
						],
						'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
						'selectors' => [
							'{{WRAPPER}} .sc_icompare_handler' => '--sc-icompare-handler-icon-size: {{SIZE}}{{UNIT}};',
						],
						'condition' => [
							'handler_image[url]' => ''
						],
					]
				);

				$this->start_controls_tabs( 'tabs_handler_style' );

				$this->start_controls_tab(
					'tab_handler_normal',
					[
						'label'                 => __( 'Normal', 'trx_addons' ),
					]
				);

				$this->add_control(
					'handler_background_color',
					[
						'label' => __( 'Background color', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::COLOR,
						'default' => '',
//						'global' => array(
//							'active' => false,
//						),
						'selectors' => [
							'{{WRAPPER}} .sc_icompare_handler' => 'background-color: {{VALUE}};',
						],
					]
				);

				$this->add_control(
					'handler_icon_color',
					[
						'label' => __( 'Icon color', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::COLOR,
						'default' => '',
//						'global' => array(
//							'active' => false,
//						),
						'selectors' => [
							'{{WRAPPER}} .sc_icompare_handler' => 'color: {{VALUE}};',
							'{{WRAPPER}} .sc_icompare_direction_vertical .sc_icompare_handler_arrows:before' => 'border-right-color: {{VALUE}};',
							'{{WRAPPER}} .sc_icompare_direction_vertical .sc_icompare_handler_arrows:after' => 'border-left-color: {{VALUE}};',
							'{{WRAPPER}} .sc_icompare_direction_horizontal .sc_icompare_handler_arrows:before' => 'border-bottom-color: {{VALUE}};',
							'{{WRAPPER}} .sc_icompare_direction_horizontal .sc_icompare_handler_arrows:after' => 'border-top-color: {{VALUE}};',
						],
						'condition' => [
							'handler_image[url]' => ''
						],
					]
				);

				$this->add_control(
					'handler_border_size',
					[
						'label' => __( 'Border size', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::SLIDER,
						'default' => [
							'size' => 2,
							'unit' => 'px'
						],
						'range' => [
							'px' => [
								'min' => 0,
								'max' => 10,
								'step' => 1
							],
						],
						'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
						'selectors' => [
							'{{WRAPPER}} .sc_icompare_handler' => '--sc-icompare-handler-border: {{SIZE}}{{UNIT}};',
						]
					]
				);

				$this->add_control(
					'handler_border_color',
					[
						'label' => __( 'Border color', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::COLOR,
						'default' => '',
//						'global' => array(
//							'active' => false,
//						),
						'selectors' => [
							'{{WRAPPER}} .sc_icompare_handler' => 'border-color: {{VALUE}};',
							'{{WRAPPER}} .sc_icompare_handler_separator' => 'background-color: {{VALUE}};',
						],
					]
				);

				$this->add_responsive_control(
					'handler_border_radius',
					[
						'label'      => esc_html__( 'Border Radius', 'trx_addons' ),
						'type'       => \Elementor\Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
						'selectors'  => [
							'{{WRAPPER}} .sc_icompare_handler' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
							'{{WRAPPER}} .sc_icompare_handler_image' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
						'condition' => [
							'handler' => 'square'
						]
					]
				);

				$this->end_controls_tab();

				$this->start_controls_tab(
					'tab_handler_hover',
					[
						'label'                 => __( 'Hover', 'trx_addons' ),
					]
				);

				$this->add_control(
					'handler_background_color_hover',
					[
						'label' => __( 'Background color', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::COLOR,
						'default' => '',
//						'global' => array(
//							'active' => false,
//						),
						'selectors' => [
							'{{WRAPPER}} .sc_icompare_handler:hover' => 'background-color: {{VALUE}};',
						],
					]
				);

				$this->add_control(
					'handler_icon_color_hover',
					[
						'label' => __( 'Icon color', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::COLOR,
						'default' => '',
//						'global' => array(
//							'active' => false,
//						),
						'selectors' => [
							'{{WRAPPER}} .sc_icompare_handler:hover' => 'color: {{VALUE}};',
							'{{WRAPPER}} .sc_icompare_direction_vertical .sc_icompare_handler:hover .sc_icompare_handler_arrows:before' => 'border-right-color: {{VALUE}};',
							'{{WRAPPER}} .sc_icompare_direction_vertical .sc_icompare_handler:hover .sc_icompare_handler_arrows:after' => 'border-left-color: {{VALUE}};',
							'{{WRAPPER}} .sc_icompare_direction_horizontal .sc_icompare_handler:hover .sc_icompare_handler_arrows:before' => 'border-bottom-color: {{VALUE}};',
							'{{WRAPPER}} .sc_icompare_direction_horizontal .sc_icompare_handler:hover .sc_icompare_handler_arrows:after' => 'border-top-color: {{VALUE}};',
						],
						'condition' => [
							'handler_image[url]' => ''
						],
					]
				);

				$this->add_control(
					'handler_border_color_hover',
					[
						'label' => __( 'Border color', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::COLOR,
						'default' => '',
//						'global' => array(
//							'active' => false,
//						),
						'selectors' => [
							'{{WRAPPER}} .sc_icompare_handler:hover' => 'border-color: {{VALUE}};',
							'{{WRAPPER}} .sc_icompare_handler:hover ~ .sc_icompare_handler_separator' => 'background-color: {{VALUE}};',
						],
					]
				);

				$this->end_controls_tab();

				$this->end_controls_tabs();
		
				$this->end_controls_section();
			}

			/**
			 * Register a before/after style controls.
			 *
			 * @access protected
			 */
			protected function register_style_controls_before_after() {

				$this->start_controls_section(
					'section_sc_icompare_before_after_style',
					[
						'label' => __( 'Before/After style', 'trx_addons' ),
						'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
					]
				);

				$this->start_controls_tabs( 'tabs_before_after_style' );

				$this->start_controls_tab(
					'icompare_tab_before',
					[
						'label'      => __( 'Before', 'trx_addons' ),
					]
				);

				$this->add_control(
					'before_text',
					[
						'label' => __( 'Text "Before"', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::TEXT,
						'default' => ''
					]
				);

				$this->add_control(
					'before_pos',
					[
						'label' => __( 'Position', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => trx_addons_get_list_sc_positions(),
						'default' => 'tl',
						'condition' => [
							'before_text!' => ''
						]
					]
				);

				$this->add_control(
					'before_bg_color',
					[
						'label' => __( 'Background color', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::COLOR,
						'default' => '',
//						'global' => array(
//							'active' => false,
//						),
						'selectors' => [
							'{{WRAPPER}} .sc_icompare_text_before' => 'background-color: {{VALUE}};',
						],
						'condition' => [
							'before_text!' => ''
						]
					]
				);

				$this->add_control(
					'before_color',
					[
						'label' => __( 'Text color', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::COLOR,
						'default' => '',
//						'global' => array(
//							'active' => false,
//						),
						'selectors' => [
							'{{WRAPPER}} .sc_icompare_text_before' => 'color: {{VALUE}};',
						],
						'condition' => [
							'before_text!' => ''
						]
					]
				);

				$this->add_group_control(
					\Elementor\Group_Control_Border::get_type(),
					[
						'name'     => 'before_border',
						'label'    => esc_html__( 'Border', 'trx_addons' ),
						'selector' => '{{WRAPPER}} .sc_icompare_text_before',
						'condition' => [
							'before_text!' => ''
						]
					]
				);

				$this->add_responsive_control(
					'before_border_radius',
					[
						'label'      => esc_html__( 'Border Radius', 'trx_addons' ),
						'type'       => \Elementor\Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
						'selectors'  => [
							'{{WRAPPER}} .sc_icompare_text_before' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
						'condition' => [
							'before_text!' => ''
						]
					]
				);

				$this->add_responsive_control(
					'before_padding',
					[
						'label'      => esc_html__( 'Padding', 'trx_addons' ),
						'type'       => \Elementor\Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
						'selectors'  => [
							'{{WRAPPER}} .sc_icompare_text_before' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
						'condition' => [
							'before_text!' => ''
						]
					]
				);

				$this->add_responsive_control(
					'before_margin',
					[
						'label'      => esc_html__( 'Margin', 'trx_addons' ),
						'type'       => \Elementor\Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
						'selectors'  => [
							'{{WRAPPER}} .sc_icompare_text_before' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
						'condition' => [
							'before_text!' => ''
						]
					]
				);

				$this->add_group_control(
					\Elementor\Group_Control_Text_Shadow::get_type(),
					array(
						'name'      => 'before_text_shadow',
						'label'     => __( 'Text Shadow', 'trx_addons' ),
						'selector'  => '{{WRAPPER}} .sc_icompare_text_before',
						'condition' => [
							'before_text!' => ''
						]
					)
				);
		
				$this->add_group_control(
					\Elementor\Group_Control_Box_Shadow::get_type(),
					[
						'name'      => 'before_box_shadow',
						'selector'  => '{{WRAPPER}} .sc_icompare_text_before',
						'condition' => [
							'before_text!' => ''
						]
					]
				);

				$this->end_controls_tab();

				$this->start_controls_tab(
					'icompare_tab_after',
					[
						'label'      => __( 'After', 'trx_addons' ),
					]
				);

				$this->add_control(
					'after_text',
					[
						'label' => __( 'Text "After"', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::TEXT,
						'default' => ''
					]
				);

				$this->add_control(
					'after_pos',
					[
						'label' => __( 'Position "After"', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => trx_addons_get_list_sc_positions(),
						'default' => 'br',
						'condition' => [
							'after_text!' => ''
						]
					]
				);

				$this->add_control(
					'after_bg_color',
					[
						'label' => __( 'Background color', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::COLOR,
						'default' => '',
//						'global' => array(
//							'active' => false,
//						),
						'selectors' => [
							'{{WRAPPER}} .sc_icompare_text_after' => 'background-color: {{VALUE}};',
						],
						'condition' => [
							'after_text!' => ''
						]
					]
				);

				$this->add_control(
					'after_color',
					[
						'label' => __( 'Text color', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::COLOR,
						'default' => '',
//						'global' => array(
//							'active' => false,
//						),
						'selectors' => [
							'{{WRAPPER}} .sc_icompare_text_after' => 'color: {{VALUE}};',
						],
						'condition' => [
							'after_text!' => ''
						]
					]
				);

				$this->add_group_control(
					\Elementor\Group_Control_Border::get_type(),
					[
						'name'     => 'after_border',
						'label'    => esc_html__( 'Border', 'trx_addons' ),
						'selector' => '{{WRAPPER}} .sc_icompare_text_after',
						'condition' => [
							'after_text!' => ''
						]
					]
				);
				$this->add_responsive_control(
					'after_border_radius',
					[
						'label'      => esc_html__( 'Border Radius', 'trx_addons' ),
						'type'       => \Elementor\Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
						'selectors'  => [
							'{{WRAPPER}} .sc_icompare_text_after' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
						'condition' => [
							'after_text!' => ''
						]
					]
				);

				$this->add_responsive_control(
					'after_padding',
					[
						'label'      => esc_html__( 'Padding', 'trx_addons' ),
						'type'       => \Elementor\Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
						'selectors'  => [
							'{{WRAPPER}} .sc_icompare_text_after' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
						'condition' => [
							'after_text!' => ''
						]
					]
				);

				$this->add_responsive_control(
					'after_margin',
					[
						'label'      => esc_html__( 'Margin', 'trx_addons' ),
						'type'       => \Elementor\Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
						'selectors'  => [
							'{{WRAPPER}} .sc_icompare_text_after' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
						'condition' => [
							'after_text!' => ''
						]
					]
				);

				$this->add_group_control(
					\Elementor\Group_Control_Text_Shadow::get_type(),
					array(
						'name'      => 'after_text_shadow',
						'label'     => __( 'Text Shadow', 'trx_addons' ),
						'selector'  => '{{WRAPPER}} .sc_icompare_text_after',
						'condition' => [
							'after_text!' => ''
						]
					)
				);

				$this->add_group_control(
					\Elementor\Group_Control_Box_Shadow::get_type(),
					[
						'name'      => 'after_box_shadow',
						'selector'  => '{{WRAPPER}} .sc_icompare_text_after',
						'condition' => [
							'after_text!' => ''
						]
					]
				);

				$this->end_controls_tab();

				$this->end_controls_tabs();

				$this->end_controls_section();
			}

			/**
			 * Render widget's template for the editor.
			 *
			 * Written as a Backbone JavaScript template and used to generate the live preview.
			 *
			 * @since 1.6.41
			 * @access protected
			 */
			protected function content_template() {
				trx_addons_get_template_part(TRX_ADDONS_PLUGIN_SHORTCODES . "icompare/tpe.icompare.php",
										'trx_addons_args_sc_icompare',
										array('element' => $this)
									);
			}
		}
		
		// Register widget
		trx_addons_elm_register_widget( 'TRX_Addons_Elementor_Widget_Icompare' );
	}
}
