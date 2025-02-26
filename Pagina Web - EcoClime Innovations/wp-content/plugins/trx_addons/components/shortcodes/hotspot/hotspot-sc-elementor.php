<?php
/**
 * Shortcode: Hotspot (Elementor support)
 *
 * @package ThemeREX Addons
 * @since v1.94.0
 */

// Disable direct call
if ( ! defined( 'ABSPATH' ) ) { exit; }




// Elementor Widget
//------------------------------------------------------
if ( ! function_exists( 'trx_addons_sc_hotspot_add_in_elementor' ) ) {
	add_action( trx_addons_elementor_get_action_for_widgets_registration(), 'trx_addons_sc_hotspot_add_in_elementor' );
	function trx_addons_sc_hotspot_add_in_elementor() {
		
		if ( ! class_exists( 'TRX_Addons_Elementor_Widget' ) ) return;	

		class TRX_Addons_Elementor_Widget_Hotspot extends TRX_Addons_Elementor_Widget {

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
					'spot_x' => 'size+unit',
					'spot_y' => 'size+unit',
					'spot_size' => 'size+unit',
					'spot_image' => 'url',
					'image_link' => 'url'
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
				return 'trx_sc_hotspot';
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
				return __( 'Hotspot', 'trx_addons' );
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
				return [ 'hotspot', 'spot', 'marker' ];
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
				return 'eicon-image-hotspot trx_addons_elementor_widget_icon';
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
				$this->register_style_controls_popup();
				$this->register_style_controls_popup_image();
				$this->register_style_controls_popup_subtitle();
				$this->register_style_controls_popup_title();
				$this->register_style_controls_popup_price();
				$this->register_style_controls_popup_description();
				$this->register_style_controls_popup_link();

				if ( apply_filters( 'trx_addons_filter_add_title_param', true, $this->get_name() ) ) {
					$this->add_title_param();
				}
			}

			/**
			 * Register widget controls.
			 *
			 * Adds different input fields to allow the user to change and customize the widget settings.
			 *
			 * @since 1.6.41
			 * @access protected
			 */
			protected function register_content_controls() {

				// Detect edit mode
				$is_edit_mode = trx_addons_elm_is_edit_mode();

				// Register controls
				$this->start_controls_section(
					'section_sc_hotspot',
					[
						'label' => __( 'Hotspot', 'trx_addons' ),
					]
				);

				$this->add_control(
					'type',
					[
						'label' => __( 'Layout', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => apply_filters('trx_addons_sc_type', trx_addons_components_get_allowed_layouts('sc', 'hotspot'), 'trx_sc_hotspot'),
						'default' => 'default'
					]
				);

				$this->add_control(
					'image',
					[
						'label' => __( 'Image', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::MEDIA,
						'default' => [ 'url' => '' ]
					]
				);

				$this->add_control(
					'image_link',
					[
						'label' => __( 'Image link', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::URL,
						'placeholder' => __( '//your-link.com', 'trx_addons' ),
						'default' => ['url' => '']
					]
				);

				$icon_param = $this->get_icon_param();
				foreach( $icon_param as $k => $v ) {
					$icon_param[ $k ] = array_merge( $v, [
															'condition' => [
																'spot_symbol' => [ 'icon' ]
															],
														] );
				}

				$this->add_control(
					'spots',
					[
						'label' => '',
						'type' => \Elementor\Controls_Manager::REPEATER,
						'default' => apply_filters('trx_addons_sc_param_group_value', [
							[
								// Spot
								'spot_visible' => 'always',
								'spot_x' => ['size' => 50, 'unit' => '%' ],
								'spot_y' => ['size' => 50, 'unit' => '%' ],
								'spot_size' => ['size' => 20, 'unit' => 'px' ],
								'spot_symbol' => 'none',
								'spot_icon' => trx_addons_elementor_set_settings_icon( 'icon-plus' ),
								'spot_char' => '',
								'spot_image' => ['url' => ''],
								'spot_color' => '',
								'spot_bg_color' => '',
								'spot_sonar_color' => '',
								// Popup
								'align' => 'center',
								'source' => 'custom',
								'post' => 'none',
								'parts' => [],
								'image' => ['url' => ''],
								'title' => esc_html__( 'First spot', 'trx_addons' ),
								'subtitle' => $this->get_default_subtitle(),
								'description' => $this->get_default_description(),
								'price' => '',
								'link' => ['url' => ''],
								'link_text' => '',
								'position' => 'bc'
							]
						], 'trx_sc_hotspot'),
						'fields' => apply_filters('trx_addons_sc_param_group_params', array_merge(
							[
								[
									'name' => 'spot_visible',
									'label' => __( 'Visible', 'trx_addons' ),
									'label_block' => false,
									'type' => \Elementor\Controls_Manager::SWITCHER,
									'label_off' => __( 'Hover', 'trx_addons' ),
									'label_on' => __( 'Always', 'trx_addons' ),
									'return_value' => '1',
									'default' => '1',
								],
								[
									'name' => 'spot_x',
									'label' => __( 'X position (in %)', 'trx_addons' ),
									'type' => \Elementor\Controls_Manager::SLIDER,
									'default' => [
										'size' => 0,
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
								],
								[
									'name' => 'spot_y',
									'label' => __( 'Y position (in %)', 'trx_addons' ),
									'type' => \Elementor\Controls_Manager::SLIDER,
									'default' => [
										'size' => 0,
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
								],
								[
									'name' => 'spot_size',
									'label' => __( 'Size', 'trx_addons' ),
									'type' => \Elementor\Controls_Manager::SLIDER,
									'range' => [
										'px' => [
											'min' => 0,
											'max' => 200
										],
										'em' => [
											'min' => 0,
											'max' => 10,
											'step' => 0.1
										],
										'rem' => [
											'min' => 0,
											'max' => 10,
											'step' => 0.1
										],
									],
									'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
								],
								[
									'name' => 'spot_symbol',
									'label' => __( 'Spot symbol', 'trx_addons' ),
									'type' => \Elementor\Controls_Manager::SELECT,
									'options' => ! $is_edit_mode ? array() : trx_addons_get_list_sc_hotspot_symbols(),
									'default' => 'none',
								],
								[
									'name' => 'spot_image',
									'label' => __( 'Image', 'trx_addons' ),
									'type' => \Elementor\Controls_Manager::MEDIA,
									'condition' => [
										'spot_symbol' => [ 'image' ]
									],
									'default' => [ 'url' => '' ],
								],
							],
							$icon_param,
							[
								[
									'name' => 'spot_char',
									'label' => __( 'Character', 'trx_addons' ),
									'type' => \Elementor\Controls_Manager::TEXT,
									'condition' => [
										'spot_symbol' => [ 'custom' ]
									],
									'default' => '',
								],
								[
									'name' => 'spot_color',
									'label' => __( 'Spot icon color', 'trx_addons' ),
									'type' => \Elementor\Controls_Manager::COLOR,
									'default' => '',
//									'global' => array(
//										'active' => false,
//									),
									'condition' => [
										'spot_symbol!' => [ 'none' ]
									],
								],
								[
									'name' => 'spot_bg_color',
									'label' => __( 'Spot bg color', 'trx_addons' ),
									'type' => \Elementor\Controls_Manager::COLOR,
									'default' => '',
//									'global' => array(
//										'active' => false,
//									),
								],
								[
									'name' => 'spot_sonar_color',
									'label' => __( 'Spot sonar color', 'trx_addons' ),
									'type' => \Elementor\Controls_Manager::COLOR,
									'default' => '',
//									'global' => array(
//										'active' => false,
//									),
								],
								[
									'name' => 'position',
									'label' => __( 'Popup position', 'trx_addons' ),
									'label_block' => false,
									'type' => \Elementor\Controls_Manager::SELECT,
									'options' => ! $is_edit_mode ? array() : trx_addons_get_list_sc_positions(),
									'default' => 'bc',
									'responsive' => true,
								],
								[
									'name' => 'align',
									'label' => esc_html__( 'Popup alignment', 'elementor' ),
									'label_block' => false,
									'type' => \Elementor\Controls_Manager::CHOOSE,
									'options' => trx_addons_get_list_sc_aligns_for_elementor(),
									'default' => 'center',
								],
								[
									'name' => 'open',
									'label' => __( 'Popup open on', 'trx_addons' ),
									'label_block' => false,
									'type' => \Elementor\Controls_Manager::SWITCHER,
									'label_off' => __( 'Hover', 'trx_addons' ),
									'label_on' => __( 'Click', 'trx_addons' ),
									'return_value' => '1',
									'default' => '1',
								],
								[
									'name' => 'opened',
									'label' => __( 'Open on load', 'trx_addons' ),
									'label_block' => false,
									'type' => \Elementor\Controls_Manager::SWITCHER,
									'return_value' => '1',
									'default' => '',
								],
								[
									'name' => 'source',
									'label' => __( 'Data source', 'trx_addons' ),
									'label_block' => false,
									'type' => \Elementor\Controls_Manager::SELECT,
									'options' => ! $is_edit_mode ? array() : trx_addons_get_list_sc_hotspot_sources(),
									'default' => 'custom',
								],
								[
									'name' => 'post_parts',
									'label' => __( 'Show parts', 'trx_addons' ),
									'label_block' => false,
									'type' => \Elementor\Controls_Manager::SELECT2,
									'options' => ! $is_edit_mode ? array() : trx_addons_get_list_sc_hotspot_post_parts(),
									'multiple' => true,
									'default' => array( 'image', 'title', 'category', 'price' ),
									'condition' => [
										'source' => [ 'post' ],
									]
								],
								array_merge(
									[
										'name' => 'post',
										'label' => __( 'Post', 'trx_addons' ),
										'label_block' => true,
										'type' => \Elementor\Controls_Manager::SELECT2,
										'options' => ! $is_edit_mode ? array() : trx_addons_get_list_posts( false, array(
																						'post_type' => 'any',
																						'order' => 'asc',
																						'orderby' => 'title'
																						)
																					),
										'default' => '',
										'condition' => [
											'source' => [ 'post' ]
										]
									],
									trx_addons_is_on( trx_addons_get_option( 'use_ajax_to_get_ids', 0 ) )
										? array(
												'select2options' => array(
																		'ajax' => array(
																						'delay' => 600,
																						'type' => 'post',
																						'dataType' => 'json',
																						'url' => esc_url( trx_addons_add_to_url( admin_url('admin-ajax.php'), array(
																									'action' => 'ajax_sc_posts_search'
																								) ) ),
																						)
																		),
											)
										: array()
								),
								[
									'name' => 'image',
									'label' => __( 'Image', 'trx_addons' ),
									'type' => \Elementor\Controls_Manager::MEDIA,
									'default' => [
										'url' => '',
									],
									'condition' => [
										'source' => [ 'custom' ]
									]
								],
								[
									'name' => 'title',
									'label' => __( 'Title', 'trx_addons' ),
									'label_block' => false,
									'type' => \Elementor\Controls_Manager::TEXT,
									'placeholder' => __( "Item's title", 'trx_addons' ),
									'default' => '',
									'condition' => [
										'source' => [ 'custom' ]
									]
								],
								[
									'name' => 'subtitle',
									'label' => __( 'Subtitle', 'trx_addons' ),
									'label_block' => false,
									'type' => \Elementor\Controls_Manager::TEXT,
									'placeholder' => __( "Item's subtitle", 'trx_addons' ),
									'default' => '',
									'condition' => [
										'source' => [ 'custom' ]
									]
								],
								[
									'name' => 'price',
									'label' => __( 'Price or other meta', 'trx_addons' ),
									'label_block' => false,
									'type' => \Elementor\Controls_Manager::TEXT,
									'placeholder' => __( "Price or meta", 'trx_addons' ),
									'default' => '',
									'condition' => [
										'source' => [ 'custom' ]
									]
								],
								[
									'name' => 'description',
									'label' => __( 'Description', 'trx_addons' ),
									'label_block' => true,
									'type' => \Elementor\Controls_Manager::TEXTAREA,
									'placeholder' => __( "Short description of this item", 'trx_addons' ),
									'default' => '',
									'separator' => 'none',
									'rows' => 10,
									'show_label' => false,
									'condition' => [
										'source' => [ 'custom' ]
									]
								],
								[
									'name' => 'link',
									'label' => __( 'Link', 'trx_addons' ),
									'label_block' => false,
									'type' => \Elementor\Controls_Manager::URL,
									'placeholder' => __( '//your-link.com', 'trx_addons' ),
									'default' => ['url' => ''],
									'condition' => [
										'source' => [ 'custom' ]
									]
								],
								[
									'name' => 'link_text',
									'label' => __( "Link's text", 'trx_addons' ),
									'label_block' => false,
									'type' => \Elementor\Controls_Manager::TEXT,
									'placeholder' => __( "Link's text", 'trx_addons' ),
									'default' => ''
								],
							] ),
						'trx_sc_hotspot' ),
						'title_field' => '{{{ title }}}'
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
					'section_sc_hotspot_image_style',
					[
						'label' => __( 'Image Style', 'trx_addons' ),
						'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
					]
				);

				$this->add_group_control(
					\Elementor\Group_Control_Border::get_type(),
					[
						'name'     => 'image_border',
						'label'    => esc_html__( 'Border', 'trx_addons' ),
						'selector' => '{{WRAPPER}} .sc_hotspot_image',
						'condition' => [
							'image[url]!' => ''
						]
					]
				);

				$this->add_responsive_control(
					'image_border_radius',
					[
						'label'      => esc_html__( 'Border Radius', 'trx_addons' ),
						'type'       => \Elementor\Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
						'selectors'  => [
							'{{WRAPPER}} .sc_hotspot_image' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
						'condition' => [
							'image[url]!' => ''
						]
					]
				);

				$this->add_group_control(
					\Elementor\Group_Control_Box_Shadow::get_type(),
					[
						'name'      => 'image_box_shadow',
						'selector'  => '{{WRAPPER}} .sc_hotspot_image',
					]
				);

				$this->end_controls_section();
			}

			/**
			 * Register a popup style controls.
			 *
			 * @access protected
			 */
			protected function register_style_controls_popup() {

				$this->start_controls_section(
					'section_sc_hotspot_popup_style',
					[
						'label' => __( 'Popup Style', 'trx_addons' ),
						'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
					]
				);

				$this->add_control(
					'popup_bg_color',
					[
						'label' => __( 'Background Color', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::COLOR,
						'default' => '',
//						'global' => array(
//							'active' => false,
//						),
						'selectors' => [
							'{{WRAPPER}} .sc_hotspot_item_popup' => 'background-color: {{VALUE}};',
						],
					]
				);

				$this->add_responsive_control(
					'popup_padding',
					[
						'label'      => esc_html__( 'Padding', 'trx_addons' ),
						'type'       => \Elementor\Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
						'selectors'  => [
							'{{WRAPPER}} .sc_hotspot_item_popup' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
					]
				);

				$this->add_group_control(
					\Elementor\Group_Control_Border::get_type(),
					[
						'name'     => 'popup_border',
						'label'    => esc_html__( 'Border', 'trx_addons' ),
						'selector' => '{{WRAPPER}} .sc_hotspot_item_popup',
					]
				);

				$this->add_responsive_control(
					'popup_border_radius',
					[
						'label'      => esc_html__( 'Border Radius', 'trx_addons' ),
						'type'       => \Elementor\Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
						'selectors'  => [
							'{{WRAPPER}} .sc_hotspot_item_popup' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						]
					]
				);

				$this->add_group_control(
					\Elementor\Group_Control_Box_Shadow::get_type(),
					[
						'name'      => 'popup_box_shadow',
						'selector'  => '{{WRAPPER}} .sc_hotspot_item_popup',
					]
				);

				$this->add_control(
					'popup_close_color',
					[
						'label' => __( 'Icon "Close" Color', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::COLOR,
						'default' => '',
//						'global' => array(
//							'active' => false,
//						),
						'selectors' => [
							'{{WRAPPER}} .sc_hotspot_item_popup_close .trx_addons_button_close_icon:before' => 'border-color: {{VALUE}};',
							'{{WRAPPER}} .sc_hotspot_item_popup_close .trx_addons_button_close_icon:after' => 'border-color: {{VALUE}};',
						],
					]
				);

				$this->add_control(
					'popup_close_hover',
					[
						'label' => __( 'Icon "Close" Hover', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::COLOR,
						'default' => '',
//						'global' => array(
//							'active' => false,
//						),
						'selectors' => [
							'{{WRAPPER}} .sc_hotspot_item_popup_close:hover .trx_addons_button_close_icon:before' => 'border-color: {{VALUE}};',
							'{{WRAPPER}} .sc_hotspot_item_popup_close:hover .trx_addons_button_close_icon:after' => 'border-color: {{VALUE}};',
						],
					]
				);

				$this->end_controls_section();
			}

			/**
			 * Register a popup image style controls.
			 *
			 * @access protected
			 */
			protected function register_style_controls_popup_image() {

				$this->start_controls_section(
					'section_sc_hotspot_popup_image_style',
					[
						'label' => __( 'Popup Image', 'trx_addons' ),
						'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
					]
				);

				$this->add_responsive_control(
					'popup_image_size',
					[
						'label' => __( 'Image Size (in %)', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::SLIDER,
						'range' => [
							'%' => [
								'min' => 0,
								'max' => 100,
							],
						],
						'size_units' => [ '%' ],
						'selectors' => [
							'{{WRAPPER}} .sc_hotspot_item_image img' => 'max-width: {{SIZE}}%;',
						],
					]
				);

				$this->add_control(
					'popup_image_bg_color',
					[
						'label' => __( 'Background Color', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::COLOR,
						'default' => '',
//						'global' => array(
//							'active' => false,
//						),
						'selectors' => [
							'{{WRAPPER}} .sc_hotspot_item_image' => 'background-color: {{VALUE}};',
						],
					]
				);

				$this->add_responsive_control(
					'popup_image_padding',
					[
						'label'      => esc_html__( 'Padding', 'trx_addons' ),
						'type'       => \Elementor\Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
						'selectors'  => [
							'{{WRAPPER}} .sc_hotspot_item_image' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
					]
				);

				$this->add_group_control(
					\Elementor\Group_Control_Border::get_type(),
					[
						'name'     => 'popup_image_border',
						'label'    => esc_html__( 'Border', 'trx_addons' ),
						'selector' => '{{WRAPPER}} .sc_hotspot_item_image',
					]
				);

				$this->add_responsive_control(
					'popup_image_border_radius',
					[
						'label'      => esc_html__( 'Border Radius', 'trx_addons' ),
						'type'       => \Elementor\Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
						'selectors'  => [
							'{{WRAPPER}} .sc_hotspot_item_image' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
							'{{WRAPPER}} .sc_hotspot_item_image img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						]
					]
				);

				$this->add_group_control(
					\Elementor\Group_Control_Box_Shadow::get_type(),
					[
						'name'      => 'popup_image_box_shadow',
						'selector'  => '{{WRAPPER}} .sc_hotspot_item_image',
					]
				);

				$this->end_controls_section();
			}

			/**
			 * Register a popup subtitle style controls.
			 *
			 * @access protected
			 */
			protected function register_style_controls_popup_subtitle() {

				$this->start_controls_section(
					'section_sc_hotspot_popup_subtitle_style',
					[
						'label' => __( 'Popup Subtitle', 'trx_addons' ),
						'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
					]
				);

				$this->add_group_control(
					\Elementor\Group_Control_Typography::get_type(),
					[
						'name'     => 'popup_subtitle_typography',
						'selector' => '{{WRAPPER}} .sc_hotspot_item_subtitle',
						// 'global'   => [
						// 	'default' => \Elementor\Global_Typography::TYPOGRAPHY_SECONDARY,
						// ],
					]
				);

				$this->add_control(
					'popup_subtitle_color',
					[
						'label' => __( 'Color', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::COLOR,
						'default' => '',
//						'global' => array(
//							'active' => false,
//						),
						'selectors' => [
							'{{WRAPPER}} .sc_hotspot_item_subtitle' => 'color: {{VALUE}};',
						],
					]
				);

				$this->add_responsive_control(
					'popup_subtitle_offset',
					[
						'label' => __( 'Offset', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::SLIDER,
						'range' => [
							'px' => [
								'min' => -100,
								'max' => 100,
							],
							'em' => [
								'min' => -10,
								'max' => 10,
								'step' => 0.1
							],
							'rem' => [
								'min' => -10,
								'max' => 10,
								'step' => 0.1
							],
						],
						'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
						'selectors' => [
							'{{WRAPPER}} .sc_hotspot_item_subtitle' => 'margin-top: {{SIZE}}{{UNIT}};',
						],
					]
				);

				$this->end_controls_section();
			}

			/**
			 * Register a popup title style controls.
			 *
			 * @access protected
			 */
			protected function register_style_controls_popup_title() {

				$this->start_controls_section(
					'section_sc_hotspot_popup_title_style',
					[
						'label' => __( 'Popup Title', 'trx_addons' ),
						'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
					]
				);

				$this->add_group_control(
					\Elementor\Group_Control_Typography::get_type(),
					[
						'name'     => 'popup_title_typography',
						'selector' => '{{WRAPPER}} .sc_hotspot_item_title',
						// 'global'   => [
						// 	'default' => \Elementor\Global_Typography::TYPOGRAPHY_PRIMARY,
						// ],
					]
				);

				$this->add_control(
					'popup_title_color',
					[
						'label' => __( 'Color', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::COLOR,
						'default' => '',
//						'global' => array(
//							'active' => false,
//						),
						'selectors' => [
							'{{WRAPPER}} .sc_hotspot_item_title' => 'color: {{VALUE}};',
						],
					]
				);

				$this->add_responsive_control(
					'popup_title_offset',
					[
						'label' => __( 'Offset', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::SLIDER,
						'range' => [
							'px' => [
								'min' => -100,
								'max' => 100,
							],
							'em' => [
								'min' => -10,
								'max' => 10,
								'step' => 0.1
							],
							'rem' => [
								'min' => -10,
								'max' => 10,
								'step' => 0.1
							],
						],
						'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
						'selectors' => [
							'{{WRAPPER}} .sc_hotspot_item_title' => 'margin-top: {{SIZE}}{{UNIT}};',
						],
					]
				);

				$this->end_controls_section();
			}

			/**
			 * Register a popup price style controls.
			 *
			 * @access protected
			 */
			protected function register_style_controls_popup_price() {

				$this->start_controls_section(
					'section_sc_hotspot_popup_price_style',
					[
						'label' => __( 'Popup Price (Meta)', 'trx_addons' ),
						'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
					]
				);

				$this->add_group_control(
					\Elementor\Group_Control_Typography::get_type(),
					[
						'name'     => 'popup_price_typography',
						'selector' => '{{WRAPPER}} .sc_hotspot_item_price',
						// 'global'   => [
						// 	'default' => \Elementor\Global_Typography::TYPOGRAPHY_PRIMARY,
						// ],
					]
				);

				$this->add_control(
					'popup_price_color',
					[
						'label' => __( 'Color', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::COLOR,
						'default' => '',
//						'global' => array(
//							'active' => false,
//						),
						'selectors' => [
							'{{WRAPPER}} .sc_hotspot_item_price' => 'color: {{VALUE}};',
						],
					]
				);

				$this->add_responsive_control(
					'popup_price_offset',
					[
						'label' => __( 'Offset', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::SLIDER,
						'range' => [
							'px' => [
								'min' => -100,
								'max' => 100,
							],
							'em' => [
								'min' => -10,
								'max' => 10,
								'step' => 0.1
							],
							'rem' => [
								'min' => -10,
								'max' => 10,
								'step' => 0.1
							],
						],
						'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
						'selectors' => [
							'{{WRAPPER}} .sc_hotspot_item_price' => 'margin-top: {{SIZE}}{{UNIT}};',
						],
					]
				);

				$this->end_controls_section();
			}

			/**
			 * Register a popup description style controls.
			 *
			 * @access protected
			 */
			protected function register_style_controls_popup_description() {

				$this->start_controls_section(
					'section_sc_hotspot_popup_description_style',
					[
						'label' => __( 'Popup Description', 'trx_addons' ),
						'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
					]
				);

				$this->add_group_control(
					\Elementor\Group_Control_Typography::get_type(),
					[
						'name'     => 'popup_description_typography',
						'selector' => '{{WRAPPER}} .sc_hotspot_item_description',
						// 'global'   => [
						// 	'default' => \Elementor\Global_Typography::TYPOGRAPHY_PRIMARY,
						// ],
					]
				);

				$this->add_control(
					'popup_description_color',
					[
						'label' => __( 'Color', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::COLOR,
						'default' => '',
//						'global' => array(
//							'active' => false,
//						),
						'selectors' => [
							'{{WRAPPER}} .sc_hotspot_item_description' => 'color: {{VALUE}};',
						],
					]
				);

				$this->add_responsive_control(
					'popup_description_offset',
					[
						'label' => __( 'Offset', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::SLIDER,
						'range' => [
							'px' => [
								'min' => -100,
								'max' => 100,
							],
							'em' => [
								'min' => -10,
								'max' => 10,
								'step' => 0.1
							],
							'rem' => [
								'min' => -10,
								'max' => 10,
								'step' => 0.1
							],
						],
						'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
						'selectors' => [
							'{{WRAPPER}} .sc_hotspot_item_description' => 'margin-top: {{SIZE}}{{UNIT}};',
						],
					]
				);

				$this->end_controls_section();
			}

			/**
			 * Register a popup link style controls.
			 *
			 * @access protected
			 */
			protected function register_style_controls_popup_link() {

				$this->start_controls_section(
					'section_sc_hotspot_popup_link_style',
					[
						'label' => __( 'Popup Link', 'trx_addons' ),
						'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
					]
				);

				$this->add_group_control(
					\Elementor\Group_Control_Typography::get_type(),
					[
						'name'     => 'popup_link_typography',
						'selector' => '{{WRAPPER}} .sc_hotspot_item_link',
						// 'global'   => [
						// 	'default' => \Elementor\Global_Typography::TYPOGRAPHY_PRIMARY,
						// ],
					]
				);

				$this->start_controls_tabs( 'popup_link_style_tabs' );

				$this->start_controls_tab(
					'popup_link_tab_normal',
					[
						'label' => __( 'Normal', 'trx_addons' ),
					]
				);

				$this->add_control(
					'popup_link_color',
					[
						'label' => __( 'Color', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::COLOR,
						'default' => '',
//						'global' => array(
//							'active' => false,
//						),
						'selectors' => [
							'{{WRAPPER}} .sc_hotspot_item_link' => 'color: {{VALUE}};',
						],
					]
				);

				$this->add_control(
					'popup_link_bg_color',
					[
						'label' => __( 'Background Color', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::COLOR,
						'default' => '',
//						'global' => array(
//							'active' => false,
//						),
						'selectors' => [
							'{{WRAPPER}} .sc_hotspot_item_link' => 'background-color: {{VALUE}};',
						],
					]
				);

				$this->add_responsive_control(
					'popup_link_padding',
					[
						'label'      => esc_html__( 'Padding', 'trx_addons' ),
						'type'       => \Elementor\Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
						'selectors'  => [
							'{{WRAPPER}} .sc_hotspot_item_link' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
					]
				);

				$this->add_group_control(
					\Elementor\Group_Control_Border::get_type(),
					[
						'name'     => 'popup_link_border',
						'label'    => esc_html__( 'Border', 'trx_addons' ),
						'selector' => '{{WRAPPER}} .sc_hotspot_item_link',
					]
				);

				$this->add_responsive_control(
					'popup_link_border_radius',
					[
						'label'      => esc_html__( 'Border Radius', 'trx_addons' ),
						'type'       => \Elementor\Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
						'selectors'  => [
							'{{WRAPPER}} .sc_hotspot_item_link' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						]
					]
				);

				$this->add_group_control(
					\Elementor\Group_Control_Box_Shadow::get_type(),
					[
						'name'      => 'popup_link_box_shadow',
						'selector'  => '{{WRAPPER}} .sc_hotspot_item_link',
					]
				);

				$this->add_responsive_control(
					'popup_link_offset',
					[
						'label' => __( 'Offset', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::SLIDER,
						'range' => [
							'px' => [
								'min' => -100,
								'max' => 100,
							],
							'em' => [
								'min' => -10,
								'max' => 10,
								'step' => 0.1
							],
							'rem' => [
								'min' => -10,
								'max' => 10,
								'step' => 0.1
							],
						],
						'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
						'selectors' => [
							'{{WRAPPER}} .sc_hotspot_item_link' => 'margin-top: {{SIZE}}{{UNIT}};',
						],
					]
				);

				$this->end_controls_tab();

				$this->start_controls_tab(
					'popup_link_tab_hover',
					[
						'label' => __( 'Hover', 'trx_addons' ),
					]
				);

				$this->add_control(
					'popup_link_hover',
					[
						'label' => __( 'Color', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::COLOR,
						'default' => '',
//						'global' => array(
//							'active' => false,
//						),
						'selectors' => [
							'{{WRAPPER}} .sc_hotspot_item_link:hover' => 'color: {{VALUE}};',
						],
					]
				);

				$this->add_control(
					'popup_link_bg_hover',
					[
						'label' => __( 'Background Color', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::COLOR,
						'default' => '',
//						'global' => array(
//							'active' => false,
//						),
						'selectors' => [
							'{{WRAPPER}} .sc_hotspot_item_link:hover' => 'background-color: {{VALUE}};',
						],
					]
				);

				$this->add_control(
					'popup_link_bd_hover',
					[
						'label' => __( 'Border Color', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::COLOR,
						'default' => '',
//						'global' => array(
//							'active' => false,
//						),
						'selectors' => [
							'{{WRAPPER}} .sc_hotspot_item_link:hover' => 'border-color: {{VALUE}};',
						],
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
				trx_addons_get_template_part(TRX_ADDONS_PLUGIN_SHORTCODES . "hotspot/tpe.hotspot.php",
										'trx_addons_args_sc_hotspot',
										array('element' => $this)
									);
			}
		}
		
		// Register widget
		trx_addons_elm_register_widget( 'TRX_Addons_Elementor_Widget_Hotspot' );
	}
}
