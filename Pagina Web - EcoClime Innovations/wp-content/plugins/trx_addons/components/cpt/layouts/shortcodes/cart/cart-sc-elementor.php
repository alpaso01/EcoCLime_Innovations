<?php
/**
 * Shortcode: Display WooCommerce cart with items number and totals (Elementor support)
 *
 * @package ThemeREX Addons
 * @since v1.6.08
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}



// Elementor Widget
//------------------------------------------------------
if (!function_exists('trx_addons_sc_layouts_cart_add_in_elementor')) {
	add_action( trx_addons_elementor_get_action_for_widgets_registration(), 'trx_addons_sc_layouts_cart_add_in_elementor' );
	function trx_addons_sc_layouts_cart_add_in_elementor() {
		
		if ( ! class_exists( 'TRX_Addons_Elementor_Layouts_Widget' ) ) return;

		if ( ! apply_filters( 'trx_addons_filter_sc_layouts_cart_enabled', function_exists( 'trx_addons_exists_woocommerce' ) && trx_addons_exists_woocommerce() ) ) return;

		class TRX_Addons_Elementor_Widget_Layouts_Cart extends TRX_Addons_Elementor_Layouts_Widget {

			/**
			 * Retrieve widget name.
			 *
			 * @since 1.6.41
			 * @access public
			 *
			 * @return string Widget name.
			 */
			public function get_name() {
				return 'trx_sc_layouts_cart';
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
				return __( 'Shopping Cart', 'trx_addons' );
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
				return [ 'cart', 'woocommerce', 'ecommerce', 'e-commerce', 'basket', 'layouts' ];
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
				return 'eicon-cart trx_addons_elementor_widget_icon';
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
				return ['trx_addons-layouts'];
			}

			/**
			 * allow_sc_styles_in_elementor
			 *
			 * @return void
			 */
			protected function allow_sc_styles_in_elementor() {
				return apply_filters( 'trx_addons_filter_allow_sc_styles_in_elementor', false, 'trx_sc_layouts_cart' );
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

				if ( $this->allow_sc_styles_in_elementor() ) {
					$this->register_style_controls_shopping_cart();
					$this->register_style_controls_cart_dropdown();
					$this->register_style_controls_cart_side_panel();
					$this->register_style_controls_cart_item();
					$this->register_style_controls_buttons();
					$this->register_style_controls_overlay();
				}

			}

			/*-----------------------------------------------------------------------------------*/
			/*	CONTENT TAB
			/*-----------------------------------------------------------------------------------*/

			protected function register_content_controls() {

				$this->start_controls_section(
					'section_sc_layouts_cart',
					[
						'label' => __( 'Shopping Cart', 'trx_addons' ),
					]
				);

				$this->add_control(
					'type',
					[
						'label' => __( 'Layout', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => apply_filters('trx_addons_sc_type', trx_addons_get_list_sc_layouts_cart_types(), 'trx_sc_layouts_cart'),
						'default' => 'default'
					]
				);

				$this->add_control(
					'market',
					[
						'label' => __( 'Market', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => apply_filters('trx_addons_sc_cart_market', array(
								'woocommerce' => esc_html__('WooCommerce', 'trx_addons'),
							), 'trx_sc_layouts_cart'),
						'default' => 'woocommerce'
					]
				);

				if ( $this->allow_sc_styles_in_elementor() ) {
					$this->add_control(
						'product_count',
						[
							'label' => __( 'Product Count', 'trx_addons' ),
							'label_block' => false,
							'type' => \Elementor\Controls_Manager::SELECT,
							'options' => apply_filters( 'trx_addons_sc_cart_product_count', array(
								'text'  => esc_html__( 'Text', 'trx_addons' ),
								'badge' => esc_html__( 'Badge', 'trx_addons' ),
							), 'trx_sc_layouts_cart' ),
							'default' => 'text',
						]
					);

					$this->add_control(
						'badge_position',
						[
							'label' => __( 'Badge Position', 'trx_addons' ),
							'label_block' => false,
							'type' => \Elementor\Controls_Manager::SELECT,
							'options' => apply_filters( 'trx_addons_sc_cart_badge_position', array(
								'top_left'     => esc_html__( 'Top Left', 'trx_addons' ),
								'top_right'    => esc_html__( 'Top Right', 'trx_addons' ),
								'bottom_left'  => esc_html__( 'Bottom Left', 'trx_addons' ),
								'bottom_right' => esc_html__( 'Bottom Right', 'trx_addons' ),
							), 'trx_sc_layouts_cart' ),
							'default' => 'top_right',
							'condition' => [
								'product_count' => 'badge',
							],
						]
					);
				}

				$this->add_control(
					'text',
					[
						'label' => __( 'Cart text', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::TEXT,
						'default' => ''
					]
				);

				if ( $this->allow_sc_styles_in_elementor() ) {
					$this->add_control(
						'cart_preview',
						[
							'label' => __( 'Cart Preview', 'trx_addons' ),
							'label_on' => __( 'On', 'trx_addons' ),
							'label_off' => __( 'Off', 'trx_addons' ),
							'label_block' => false,
							'description' => __( "Display the shopping cart's contents for preview (for editor only)", 'trx_addons' ),
							'type' => \Elementor\Controls_Manager::SWITCHER,
							'default' => '',
							'return_value' => '1',
							'condition' => [
								'type!' => 'button',
							],
						]
					);
				}

				$this->end_controls_section();
			}

			/*-----------------------------------------------------------------------------------*/
			/*	STYLE TAB
			/*-----------------------------------------------------------------------------------*/

			/**
			 * Style Tab: Shopping Cart
			 */
			protected function register_style_controls_shopping_cart() {

				$this->start_controls_section(
					'section_style_shopping_cart',
					[
						'label' => __( 'Shopping Cart', 'trx_addons' ),
						'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
					]
				);

				$this->add_control(
					'select_cart_icon',
					[
						'label'       => __( 'Cart Icon', 'trx_addons' ),
						'type'        => \Elementor\Controls_Manager::ICONS,
						'default'     => [
							'value' => '',
							'library' => 'fa-solid',
						],
						'recommended' => [
							'fa-solid'   => [
								'shopping-basket',
								'box-open',
							],
						],
					]
				);

				$this->add_responsive_control(
					'select_cart_icon_size',
					[
						'label' => __( 'Icon Size', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::SLIDER,
						'size_units' => [ 'px', 'em', 'rem', '%' ],
						'selectors' => [
							'{{WRAPPER}} .sc_layouts_item_icon' => 'font-size: {{SIZE}}{{UNIT}};',
						],
					]
				);

				$this->add_group_control(
					\Elementor\Group_Control_Typography::get_type(),
					[
						'name' => 'cart_text_style_typography',
						'selector' => '{{WRAPPER}} .sc_layouts_item_details_line1',
					]
				);

				$this->start_controls_tabs( 'tabs_cart_icon_style' );

				$this->start_controls_tab(
					'tab_cart_icon_normal',
					[
						'label' => __( 'Normal', 'trx_addons' ),
					]
				);

				$this->add_control(
					'cart_icon_color',
					[
						'label'     => __( 'Icon Color', 'trx_addons' ),
						'type'      => \Elementor\Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .sc_layouts_cart_icon' => 'color: {{VALUE}};',
							'{{WRAPPER}} .sc_layouts_cart_icon > svg' => 'fill: {{VALUE}};',
						],
					]
				);

				$this->add_control(
					'cart_text_style_color',
					[
						'label'     => __( 'Text Color', 'trx_addons' ),
						'type'      => \Elementor\Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .sc_layouts_item_details_line1' => 'color: {{VALUE}};',
						],
					]
				);

				$this->end_controls_tab();

				$this->start_controls_tab(
					'tab_cart_icon_hover',
					[
						'label' => __( 'Hover', 'trx_addons' ),
					]
				);

				$this->add_control(
					'cart_icon_color_hover',
					[
						'label'     => __( 'Icon Color', 'trx_addons' ),
						'type'      => \Elementor\Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .sc_layouts_cart:hover .sc_layouts_cart_icon' => 'color: {{VALUE}};',
							'{{WRAPPER}} .sc_layouts_cart:hover .sc_layouts_cart_icon > svg' => 'fill: {{VALUE}};',
						],
					]
				);

				$this->add_control(
					'cart_text_style_hover_color',
					[
						'label'     => __( 'Text Color', 'trx_addons' ),
						'type'      => \Elementor\Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .sc_layouts_cart:hover .sc_layouts_item_details_line1' => 'color: {{VALUE}};',
						],
					]
				);

				$this->end_controls_tab();

				$this->end_controls_tabs();

				$this->add_control(
					'heading_product_count_style',
					[
						'label' => __( 'Product Count', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::HEADING,
						'separator' => 'before',
					]
				);

				$this->add_group_control(
					\Elementor\Group_Control_Typography::get_type(),
					[
						'name' => 'product_count_style_typography',
						'selector' => '{{WRAPPER}} .sc_layouts_item_details_line2,
									   {{WRAPPER}} .trx_addons_sc_cart_product_count_badge > .sc_layouts_cart_items_short',
					]
				);

				$this->start_controls_tabs( 'tabs_cart_product_count_style' );

				$this->start_controls_tab(
					'tab_cart_product_count_normal',
					[
						'label' => __( 'Normal', 'trx_addons' ),
					]
				);

				$this->add_control(
					'cart_product_count_color',
					[
						'label'     => __( 'Text Color', 'trx_addons' ),
						'type'      => \Elementor\Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .sc_layouts_item_details_line2,
							 {{WRAPPER}} .trx_addons_sc_cart_product_count_badge > .sc_layouts_cart_items_short' => 'color: {{VALUE}};',
						],
					]
				);

				$this->add_control(
					'cart_product_count_bg_color',
					[
						'label'     => __( 'Background Color', 'trx_addons' ),
						'type'      => \Elementor\Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .sc_layouts_item_details_line2,
							 {{WRAPPER}} .trx_addons_sc_cart_product_count_badge > .sc_layouts_cart_items_short' => 'background-color: {{VALUE}};',
						],
					]
				);

				$this->add_control(
					'cart_product_count_border_color',
					[
						'label'     => __( 'Border Color', 'trx_addons' ),
						'type'      => \Elementor\Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .sc_layouts_item_details_line2,
							 {{WRAPPER}} .trx_addons_sc_cart_product_count_badge > .sc_layouts_cart_items_short' => 'border-color: {{VALUE}};',
						],
						'condition' => [
							'cart_product_count_border_type!' => 'none',
						],
					]
				);

				$this->end_controls_tab();

				$this->start_controls_tab(
					'tab_cart_product_count_hover',
					[
						'label' => __( 'Hover', 'trx_addons' ),
					]
				);

				$this->add_control(
					'cart_product_count_color_hover',
					[
						'label'     => __( 'Text Color', 'trx_addons' ),
						'type'      => \Elementor\Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .sc_layouts_cart:hover .sc_layouts_item_details_line2,
							 {{WRAPPER}} .sc_layouts_cart:hover .sc_layouts_cart_items_short' => 'color: {{VALUE}};',
						],
					]
				);

				$this->add_control(
					'cart_product_count_bg_color_hover',
					[
						'label'     => __( 'Background Color', 'trx_addons' ),
						'type'      => \Elementor\Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .sc_layouts_cart:hover .sc_layouts_item_details_line2,
							 {{WRAPPER}} .sc_layouts_cart:hover .sc_layouts_cart_items_short' => 'background-color: {{VALUE}};',
						],
					]
				);

				$this->add_control(
					'cart_product_count_border_color_hover',
					[
						'label'     => __( 'Border Color', 'trx_addons' ),
						'type'      => \Elementor\Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .sc_layouts_cart:hover .sc_layouts_item_details_line2,
							 {{WRAPPER}} .sc_layouts_cart:hover .sc_layouts_cart_items_short' => 'border-color: {{VALUE}};',
						],
						'condition' => [
							'cart_product_count_border_type!' => 'none',
						],
					]
				);

				$this->end_controls_tab();

				$this->end_controls_tabs();

				$this->add_control(
					'cart_product_count_border_type',
					[
						'label'     => __( 'Border Type', 'trx_addons' ),
						'type'      => \Elementor\Controls_Manager::SELECT,
						'default'   => 'none',
						'options'   => [
							'none'   => __( 'None', 'trx_addons' ),
							'solid'  => __( 'Solid', 'trx_addons' ),
							'double' => __( 'Double', 'trx_addons' ),
							'dotted' => __( 'Dotted', 'trx_addons' ),
							'dashed' => __( 'Dashed', 'trx_addons' ),
						],
						'selectors' => [
							'{{WRAPPER}} .sc_layouts_item_details_line2,
							 {{WRAPPER}} .trx_addons_sc_cart_product_count_badge > .sc_layouts_cart_items_short' => 'border-style: {{VALUE}}',
						],
					]
				);

				$this->add_responsive_control(
					'cart_product_count_border_width',
					[
						'label'      => __( 'Border width', 'trx_addons' ),
						'type'       => \Elementor\Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px' ],
						'selectors'  => [
							'{{WRAPPER}} .sc_layouts_item_details_line2,
							 {{WRAPPER}} .trx_addons_sc_cart_product_count_badge > .sc_layouts_cart_items_short' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
						'condition' => [
							'cart_product_count_border_type!' => 'none',
						],
					]
				);

				$this->add_responsive_control(
					'cart_product_count_border_radius',
					[
						'label' => __( 'Border Radius', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::SLIDER,
						'size_units' => [ 'px', 'em', 'rem', '%' ],
						'selectors' => [
							'{{WRAPPER}} .sc_layouts_item_details_line2,
							 {{WRAPPER}} .trx_addons_sc_cart_product_count_badge > .sc_layouts_cart_items_short' => 'border-radius: {{SIZE}}{{UNIT}};',
						],
					]
				);

				$this->add_responsive_control(
					'cart_product_count_min_width',
					[
						'label' => __( 'Box Size', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::SLIDER,
						'size_units' => [ 'px', 'em', 'rem', '%' ],
						'selectors' => [
							'{{WRAPPER}} .trx_addons_sc_cart_product_count_badge > .sc_layouts_cart_items_short' => 'min-width: {{SIZE}}{{UNIT}}; min-height: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}};',
						],
						'condition' => [
							'product_count' => 'badge',
						],
					]
				);

				$this->add_responsive_control(
					'cart_product_count_padding',
					[
						'label'      => __( 'Padding', 'trx_addons' ),
						'type'       => \Elementor\Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', 'em', 'rem', '%' ],
						'selectors'  => [
							'{{WRAPPER}} .sc_layouts_item_details_line2,
							 {{WRAPPER}} .trx_addons_sc_cart_product_count_badge > .sc_layouts_cart_items_short' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
						// 'condition' => [
						// 	'cart_product_count_min_width[size]' => '',
						// ],
					]
				);

				$this->add_responsive_control(
					'cart_product_count_margin',
					[
						'label'      => __( 'Margin', 'trx_addons' ),
						'type'       => \Elementor\Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', 'em', 'rem', '%' ],
						'selectors'  => [
							'{{WRAPPER}} .sc_layouts_item_details_line2,
							 {{WRAPPER}} .trx_addons_sc_cart_product_count_badge > .sc_layouts_cart_items_short' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
					]
				);

				$this->end_controls_section();
			}

			/**
			 * Style Tab: Cart Dropdown
			 */
			protected function register_style_controls_cart_dropdown() {

				$this->start_controls_section(
					'section_style_cart_dropdown',
					[
						'label' => __( 'Cart Dropdown', 'trx_addons' ),
						'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
						'condition' => [
							'type' => 'default',
						],
					]
				);

				$this->add_group_control(
					\Elementor\Group_Control_Typography::get_type(),
					[
						'name' => 'trx_addons_cart_dropdown_empty_style_typography',
						'selector' => '{{WRAPPER}} .sc_layouts_cart_widget .woocommerce-mini-cart__empty-message',
						'classes' => "mycustomclass",
						'condition' => [
							'type' => 'default',
						],
					]
				);

				$this->add_control(
					'cart_dropdown_empty_style_typography_description',
					[
						'raw' => __( 'For the empty cart message', 'trx_addons' ),
						'content_classes' => 'elementor-control-field-description',
						'type' => \Elementor\Controls_Manager::RAW_HTML,
						'condition' => [
							'type' => 'default',
						],
					]
				);

				$this->add_responsive_control(
					'cart_dropdown_style_max_height',
					[
						'label'      => __( 'Cart Items Max Height', 'trx_addons' ),
						'type'       => \Elementor\Controls_Manager::SLIDER,
						'default'    => [
							'size' => '215',
							'unit' => 'px',
						],
						'range' => [
							'px' => [
								'min' => 0,
								'max' => 500,
								'step' => 1,
							],
						],
						'size_units' => [ 'px', 'em', 'rem', '%' ],
						'selectors'  => [
							'{{WRAPPER}} .sc_layouts_cart_widget .cart_list' => 'max-height: {{SIZE}}{{UNIT}};',
						],
						'condition' => [
							'type' => 'default',
						],
					]
				);

				$this->add_group_control(
					\Elementor\Group_Control_Background::get_type(),
					[
						'name' => 'cart_dropdown_style_background',
						'types' => [ 'classic', 'gradient' ],
						'selector' => '{{WRAPPER}} .sc_layouts_cart_widget, {{WRAPPER}} .sc_layouts_cart_widget:after',
						'condition' => [
							'type' => 'default',
						],
					]
				);

				$this->add_control(
					'cart_dropdown_style_border_type',
					[
						'label'     => __( 'Border Type', 'trx_addons' ),
						'classes' => "mycustomclass",
						'type'      => \Elementor\Controls_Manager::SELECT,
						'default'   => 'solid',
						'options'   => [
							'none'   => __( 'None', 'trx_addons' ),
							'solid'  => __( 'Solid', 'trx_addons' ),
							'double' => __( 'Double', 'trx_addons' ),
							'dotted' => __( 'Dotted', 'trx_addons' ),
							'dashed' => __( 'Dashed', 'trx_addons' ),
						],
						'selectors' => [
							'{{WRAPPER}} .sc_layouts_cart_widget' => 'border-style: {{VALUE}};',
							'{{WRAPPER}} .sc_layouts_cart_widget:after' => 'border-left-style: {{VALUE}}; border-top-style: {{VALUE}};',
						],
						'condition' => [
							'type' => 'default',
						],
					]
				);

				$this->add_responsive_control(
					'cart_dropdown_style_border_width',
					[
						'label'      => __( 'Border width', 'trx_addons' ),
						'type'       => \Elementor\Controls_Manager::SLIDER,
						'size_units' => [ 'px', 'em', 'rem', '%' ],
						'selectors'  => [
							'{{WRAPPER}} .sc_layouts_cart_widget' => 'border-width: {{SIZE}}{{UNIT}};',
							'{{WRAPPER}} .sc_layouts_cart_widget:after' => 'border-left-width: {{SIZE}}{{UNIT}}; border-top-width: {{SIZE}}{{UNIT}};',
						],
						'condition' => [
							'type' => 'default',
							'cart_dropdown_style_border_type!' => 'none',
						],
					]
				);

				$this->add_control(
					'cart_dropdown_style_border_color',
					[
						'label'     => __( 'Border Color', 'trx_addons' ),
						'type'      => \Elementor\Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .sc_layouts_cart_widget' => 'border-color: {{VALUE}};',
							'{{WRAPPER}} .sc_layouts_cart_widget:after' => 'border-left-color: {{VALUE}}; border-top-color: {{VALUE}};',
						],
						'condition' => [
							'type' => 'default',
							'cart_dropdown_style_border_type!' => 'none',
						],
					]
				);

				$this->add_responsive_control(
					'cart_dropdown_style_border_radius',
					[
						'label' => __( 'Border Radius', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::SLIDER,
						'size_units' => [ 'px', 'em', 'rem', '%' ],
						'selectors' => [
							'{{WRAPPER}} .sc_layouts_cart_widget' => 'border-radius: {{SIZE}}{{UNIT}};',
						],
						'condition' => [
							'type' => 'default',
						],
					]
				);

				$this->add_responsive_control(
					'cart_dropdown_style_padding',
					[
						'label'      => __( 'Padding', 'trx_addons' ),
						'type'       => \Elementor\Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', 'em', 'rem', '%' ],
						'selectors'  => [
							'{{WRAPPER}} .sc_layouts_cart_widget' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
						'condition' => [
							'type' => 'default',
						],
					]
				);

				$this->add_responsive_control(
					'cart_dropdown_style_margin',
					[
						'label'      => __( 'Margin', 'trx_addons' ),
						'type'       => \Elementor\Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', 'em', 'rem', '%' ],
						'selectors'  => [
							'{{WRAPPER}} .sc_layouts_cart_widget' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
						'condition' => [
							'type' => 'default',
						],
					]
				);

				$this->add_control(
					'select_cart_dropdown_close_icon',
					[
						'label'       => __( 'Close Icon', 'trx_addons' ),
						'type'        => \Elementor\Controls_Manager::ICONS,
						'default'     => [
							'value' => '',
							'library' => 'fa-solid',
						],
						'separator'   => 'before',
						'recommended' => [
							'fa-regular' => [
								'window-close',
							],
							'fa-solid'   => [
								'window-close',
							],
						],
						'condition' => [
							'type' => 'default',
						],
					]
				);

				$this->add_responsive_control(
					'select_cart_dropdown_close_icon_size',
					[
						'label' => __( 'Icon Size', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::SLIDER,
						'size_units' => [ 'px', 'em', 'rem', '%' ],
						'selectors' => [
							'{{WRAPPER}} .sc_layouts_cart_widget_close' => 'font-size: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
						],
					]
				);

				$this->start_controls_tabs( 'tabs_cart_dropdown_close_icon_style' );

				$this->start_controls_tab(
					'tab_cart_dropdown_close_icon_normal',
					[
						'label' => __( 'Normal', 'trx_addons' ),
						'condition' => [
							'type' => 'default',
						],
					]
				);

				$this->add_control(
					'cart_dropdown_close_icon_color',
					[
						'label'     => __( 'Icon Color', 'trx_addons' ),
						'type'      => \Elementor\Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .sc_layouts_cart_widget_close_icon:before,
							 {{WRAPPER}} .sc_layouts_cart_widget_close_icon:after' => 'border-top-color: {{VALUE}};',
							'{{WRAPPER}} .sc_layouts_cart_widget_close_icon' => 'color: {{VALUE}};',
							'{{WRAPPER}} .sc_layouts_cart_widget_close svg' => 'fill: {{VALUE}};',
						],
						'condition' => [
							'type' => 'default',
						],
					]
				);

				$this->end_controls_tab();

				$this->start_controls_tab(
					'tab_cart_dropdown_close_icon_hover',
					[
						'label' => __( 'Hover', 'trx_addons' ),
						'condition' => [
							'type' => 'default',
						],
					]
				);

				$this->add_control(
					'cart_dropdown_close_icon_color_hover',
					[
						'label'     => __( 'Icon Color', 'trx_addons' ),
						'type'      => \Elementor\Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .sc_layouts_cart_widget_close:hover .sc_layouts_cart_widget_close_icon:before,
							 {{WRAPPER}} .sc_layouts_cart_widget_close:hover .sc_layouts_cart_widget_close_icon:after' => 'border-top-color: {{VALUE}};',
							'{{WRAPPER}} .sc_layouts_cart_widget_close:hover .sc_layouts_cart_widget_close_icon' => 'color: {{VALUE}};',
							'{{WRAPPER}} .sc_layouts_cart_widget_close:hover > svg' => 'fill: {{VALUE}};',
						],
						'condition' => [
							'type' => 'default',
						],
					]
				);

				$this->end_controls_tab();

				$this->end_controls_tabs();

				$this->add_responsive_control(
					'cart_dropdown_close_icon_margin',
					[
						'label'      => __( 'Margin', 'trx_addons' ),
						'type'       => \Elementor\Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', 'em', 'rem', '%' ],
						'selectors'  => [
							'{{WRAPPER}} .sc_layouts_cart_widget_close' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
						],
						'condition' => [
							'type' => 'default',
						],
					]
				);

				$this->add_control(
					'heading_product_subtotal_style',
					[
						'label' => __( 'Price Total', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::HEADING,
						'separator' => 'before',
						'condition' => [
							'type' => 'default',
						],
					]
				);

				$this->add_group_control(
					\Elementor\Group_Control_Typography::get_type(),
					[
						'name'      => 'product_subtotal_style_typography',
						'label' => __( 'Price Typography', 'trx_addons' ),
						'selector'  => '{{WRAPPER}} .sc_layouts_cart_widget .total .amount',
						'condition' => [
							'type' => 'default',
						],
					]
				);

				$this->add_control(
					'product_subtotal_style_color',
					[
						'label'     => __( 'Price Color', 'trx_addons' ),
						'type'      => \Elementor\Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .sc_layouts_cart_widget .total .amount' => 'color: {{VALUE}};',
						],
						'condition' => [
							'type' => 'default',
						],
					]
				);

				$this->add_group_control(
					\Elementor\Group_Control_Typography::get_type(),
					[
						'name'      => 'product_subtotal_label_style_typography',
						'label' => __( 'Label Typography', 'trx_addons' ),
						'selector'  => '{{WRAPPER}} .sc_layouts_cart_widget .total > strong',
						'condition' => [
							'type' => 'default',
						],
					]
				);

				$this->add_control(
					'product_subtotal_label_style_color',
					[
						'label'     => __( 'Label Color', 'trx_addons' ),
						'type'      => \Elementor\Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .sc_layouts_cart_widget .total > strong' => 'color: {{VALUE}};',
						],
						'condition' => [
							'type' => 'default',
						],
					]
				);

				$this->add_responsive_control(
					'product_subtotal_margin',
					[
						'label'      => __( 'Margin', 'trx_addons' ),
						'type'       => \Elementor\Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', 'em', 'rem', '%' ],
						'default'    => [
							'top'      => '0',
							'right'    => '0',
							'bottom'   => '1',
							'left'     => '0',
							'unit'     => 'em',
							'isLinked' => false,
						],
						'selectors'  => [
							'{{WRAPPER}} .sc_layouts_cart_widget .total' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
						'condition' => [
							'type' => 'default',
						],
					]
				);

				$this->add_responsive_control(
					'product_subtotal_padding',
					[
						'label'      => __( 'Padding', 'trx_addons' ),
						'type'       => \Elementor\Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', 'em', 'rem', '%' ],
						'selectors'  => [
							'{{WRAPPER}} .sc_layouts_cart_widget .total' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
						'condition' => [
							'type' => 'default',
						],
					]
				);

				$this->add_control(
					'product_subtotal_style_border_type',
					[
						'label'     => __( 'Border Type', 'trx_addons' ),
						'type'      => \Elementor\Controls_Manager::SELECT,
						'default'   => 'none',
						'options'   => [
							'none'   => __( 'None', 'trx_addons' ),
							'solid'  => __( 'Solid', 'trx_addons' ),
							'double' => __( 'Double', 'trx_addons' ),
							'dotted' => __( 'Dotted', 'trx_addons' ),
							'dashed' => __( 'Dashed', 'trx_addons' ),
						],
						'selectors' => [
							'{{WRAPPER}} .sc_layouts_cart_widget .total' => 'border-top-style: {{VALUE}};',
						],
						'condition' => [
							'type' => 'default',
						],
					]
				);

				$this->add_responsive_control(
					'product_subtotal_style_border_width',
					[
						'label' => __( 'Border Width', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::SLIDER,
						'default' => [
							'size' => '1',
							'unit' => 'px'
						],
						'size_units' => [ 'px', 'em', 'rem', '%' ],
						'selectors' => [
							'{{WRAPPER}} .sc_layouts_cart_widget .total' => 'border-top-width: {{SIZE}}{{UNIT}};',
						],
						'condition' => [
							'type' => 'default',
							'product_subtotal_style_border_type!' => 'none',
						],
					]
				);

				$this->add_control(
					'product_subtotal_style_border_color',
					[
						'label'     => __( 'Border Color', 'trx_addons' ),
						'type'      => \Elementor\Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .sc_layouts_cart_widget .total' => 'border-top-color: {{VALUE}};',
						],
						'condition' => [
							'type' => 'default',
							'product_subtotal_style_border_type!' => 'none',
						],
					]
				);

				$this->end_controls_section();
			}

			/**
			 * Style Tab: Cart Side Panel
			 */
			protected function register_style_controls_cart_side_panel() {

				$this->start_controls_section(
					'section_style_cart_side_panel',
					[
						'label' => __( 'Cart Side Panel', 'trx_addons' ),
						'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
						'condition' => [
							'type' => 'panel',
						],
					]
				);

				$this->add_group_control(
					\Elementor\Group_Control_Typography::get_type(),
					[
						'name' => 'trx_addons_cart_type_panel_empty_style_typography',
						'selector' => '.sc_layouts_cart_panel_style_{{ID}}.sc_layouts_cart_panel.sc_layouts_panel .sc_layouts_cart_panel_widget .woocommerce-mini-cart__empty-message',
						'condition' => [
							'type' => 'panel',
						],
					]
				);

				$this->add_control(
					'type_panel_empty_style_typography_description',
					[
						'raw' => __( 'For the empty cart message', 'trx_addons' ),
						'content_classes' => 'elementor-control-field-description',
						'type' => \Elementor\Controls_Manager::RAW_HTML,
						'condition' => [
							'type' => 'panel',
						],
					]
				);

				$this->add_group_control(
					\Elementor\Group_Control_Background::get_type(),
					[
						'name' => 'type_panel_style_background',
						'types' => [ 'classic', 'gradient' ],
						'selector' => '.sc_layouts_cart_panel_style_{{ID}}.sc_layouts_cart_panel.sc_layouts_panel .sc_layouts_panel_inner',
						'condition' => [
							'type' => 'panel',
						],
					]
				);

				$this->add_control(
					'type_panel_style_border_type',
					[
						'label'     => __( 'Border Type', 'trx_addons' ),
						'type'      => \Elementor\Controls_Manager::SELECT,
						'default'   => 'none',
						'options'   => [
							'none'   => __( 'None', 'trx_addons' ),
							'solid'  => __( 'Solid', 'trx_addons' ),
							'double' => __( 'Double', 'trx_addons' ),
							'dotted' => __( 'Dotted', 'trx_addons' ),
							'dashed' => __( 'Dashed', 'trx_addons' ),
						],
						'selectors' => [
							'.sc_layouts_cart_panel_style_{{ID}}.sc_layouts_cart_panel.sc_layouts_panel .sc_layouts_panel_inner' => 'border-style: {{VALUE}};',
						],
						'condition' => [
							'type' => 'panel',
						],
					]
				);

				$this->add_responsive_control(
					'type_panel_style_border_width',
					[
						'label'      => __( 'Border Width', 'trx_addons' ),
						'type'       => \Elementor\Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', 'em', 'rem', '%' ],
						'selectors'  => [
							'.sc_layouts_cart_panel_style_{{ID}}.sc_layouts_cart_panel.sc_layouts_panel .sc_layouts_panel_inner' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
						'condition' => [
							'type' => 'panel',
							'type_panel_style_border_type!' => 'none',
						],
					]
				);

				$this->add_control(
					'type_panel_style_border_color',
					[
						'label'     => __( 'Border Color', 'trx_addons' ),
						'type'      => \Elementor\Controls_Manager::COLOR,
						'selectors' => [
							'.sc_layouts_cart_panel_style_{{ID}}.sc_layouts_cart_panel.sc_layouts_panel .sc_layouts_panel_inner' => 'border-color: {{VALUE}};',
						],
						'condition' => [
							'type' => 'panel',
							'type_panel_style_border_type!' => 'none',
						],
					]
				);

				$this->add_responsive_control(
					'type_panel_style_border_radius',
					[
						'label' => __( 'Border Radius', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::SLIDER,
						'size_units' => [ 'px', 'em', 'rem', '%' ],
						'selectors' => [
							'.sc_layouts_cart_panel_style_{{ID}}.sc_layouts_cart_panel.sc_layouts_panel .sc_layouts_panel_inner' => 'border-radius: {{SIZE}}{{UNIT}};',
						],
						'condition' => [
							'type' => 'panel',
						],
					]
				);

				$this->add_responsive_control(
					'type_panel_style_padding',
					[
						'label'      => __( 'Padding', 'trx_addons' ),
						'type'       => \Elementor\Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', 'em', 'rem', '%' ],
						'selectors'  => [
							'.sc_layouts_cart_panel_style_{{ID}}.sc_layouts_cart_panel.sc_layouts_panel .sc_layouts_panel_inner' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
						'condition' => [
							'type' => 'panel',
						],
					]
				);

				$this->add_responsive_control(
					'type_panel_style_content_padding',
					[
						'label'      => __( 'Content Padding', 'trx_addons' ),
						'type'       => \Elementor\Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', 'em', 'rem', '%' ],
						'selectors'  => [
							'.sc_layouts_cart_panel_style_{{ID}}.sc_layouts_cart_panel.sc_layouts_panel .sc_layouts_panel_inner .sc_layouts_cart_panel_widget .widget_shopping_cart' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
						'condition' => [
							'type' => 'panel',
						],
					]
				);

				$this->add_control(
					'select_type_panel_close_icon_remove',
					[
						'label'       => __( 'Close Icon', 'trx_addons' ),
						'type'        => \Elementor\Controls_Manager::ICONS,
						'default'     => [
							'value' => '',
							'library' => 'fa-solid',
						],
						'separator'   => 'before',
						'recommended' => [
							'fa-regular' => [
								'window-close',
							],
							'fa-solid'   => [
								'window-close',
							],
						],
						'condition' => [
							'type' => 'panel',
						],
					]
				);

				$this->add_responsive_control(
					'select_type_panel_close_icon_remove_size',
					[
						'label' => __( 'Icon Size', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::SLIDER,
						'size_units' => [ 'px', 'em', 'rem', '%' ],
						'selectors' => [
							'.sc_layouts_cart_panel_style_{{ID}} .trx_addons_button_close' => 'font-size: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
						],
					]
				);

				$this->start_controls_tabs( 'tabs_type_panel_close_icon_style' );

				$this->start_controls_tab(
					'tab_type_panel_close_icon_normal',
					[
						'label' => __( 'Normal', 'trx_addons' ),
						'condition' => [
							'type' => 'panel',
						],
					]
				);

				$this->add_control(
					'type_panel_close_icon_color',
					[
						'label'     => __( 'Close Icon Color', 'trx_addons' ),
						'type'      => \Elementor\Controls_Manager::COLOR,
						'selectors' => [
							'.sc_layouts_cart_panel_style_{{ID}}.sc_layouts_cart_panel.sc_layouts_panel .trx_addons_button_close' => 'color: {{VALUE}};',
							'.sc_layouts_cart_panel_style_{{ID}}.sc_layouts_cart_panel.sc_layouts_panel .trx_addons_button_close svg' => 'fill: {{VALUE}};',
							'.sc_layouts_cart_panel_style_{{ID}}.sc_layouts_cart_panel.sc_layouts_panel .trx_addons_button_close_icon:before,
							 .sc_layouts_cart_panel_style_{{ID}}.sc_layouts_cart_panel.sc_layouts_panel .trx_addons_button_close_icon:after' => 'border-color: {{VALUE}};',
						],
						'condition' => [
							'type' => 'panel',
						],
					]
				);

				$this->end_controls_tab();

				$this->start_controls_tab(
					'tab_type_panel_close_icon_hover',
					[
						'label' => __( 'Hover', 'trx_addons' ),
						'condition' => [
							'type' => 'panel',
						],
					]
				);

				$this->add_control(
					'type_panel_close_icon_color_hover',
					[
						'label'     => __( 'Close Icon Color', 'trx_addons' ),
						'type'      => \Elementor\Controls_Manager::COLOR,
						'selectors' => [
							'.sc_layouts_cart_panel_style_{{ID}}.sc_layouts_cart_panel.sc_layouts_panel .trx_addons_button_close:hover' => 'color: {{VALUE}};',
							'.sc_layouts_cart_panel_style_{{ID}}.sc_layouts_cart_panel.sc_layouts_panel .trx_addons_button_close:hover svg' => 'fill: {{VALUE}};',
							'.sc_layouts_cart_panel_style_{{ID}}.sc_layouts_cart_panel.sc_layouts_panel .trx_addons_button_close:hover .trx_addons_button_close_icon:before,
							 .sc_layouts_cart_panel_style_{{ID}}.sc_layouts_cart_panel.sc_layouts_panel .trx_addons_button_close:hover .trx_addons_button_close_icon:after' => 'border-color: {{VALUE}};',
						],
						'condition' => [
							'type' => 'panel',
						],
					]
				);

				$this->end_controls_tab();

				$this->end_controls_tabs();

				$this->add_responsive_control(
					'type_panel_close_icon_margin',
					[
						'label'      => __( 'Close Icon Margin', 'trx_addons' ),
						'type'       => \Elementor\Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', 'em', 'rem', '%' ],
						'selectors'  => [
							'.sc_layouts_cart_panel_style_{{ID}}.sc_layouts_cart_panel.sc_layouts_panel .trx_addons_button_close' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
						],
						'condition' => [
							'type' => 'panel',
						],
					]
				);

				$this->add_control(
					'heading_top_bar_style',
					[
						'label' => __( 'Top Bar', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::HEADING,
						'separator' => 'before',
						'condition' => [
							'type' => 'panel',
						],
					]
				);

				$this->add_control(
					'top_bar_bg_color',
					[
						'label'     => __( 'Top Bar Color', 'trx_addons' ),
						'type'      => \Elementor\Controls_Manager::COLOR,
						'selectors' => [
							'.sc_layouts_cart_panel_style_{{ID}}.sc_layouts_cart_panel.sc_layouts_panel .sc_layouts_panel_inner .sc_layouts_cart_panel_header' => 'background-color: {{VALUE}};',
						],
						'condition' => [
							'type' => 'panel',
						],
					]
				);

				$this->add_control(
					'top_bar_border_type',
					[
						'label'     => __( 'Border Type', 'trx_addons' ),
						'type'      => \Elementor\Controls_Manager::SELECT,
						'default'   => 'none',
						'options'   => [
							'none'   => __( 'None', 'trx_addons' ),
							'solid'  => __( 'Solid', 'trx_addons' ),
							'double' => __( 'Double', 'trx_addons' ),
							'dotted' => __( 'Dotted', 'trx_addons' ),
							'dashed' => __( 'Dashed', 'trx_addons' ),
						],
						'selectors' => [
							'.sc_layouts_cart_panel_style_{{ID}}.sc_layouts_cart_panel.sc_layouts_panel .sc_layouts_panel_inner .sc_layouts_cart_panel_header' => 'border-style: {{VALUE}};',
						],
						'condition' => [
							'type' => 'panel',
						],
					]
				);

				$this->add_responsive_control(
					'top_bar_border_width',
					[
						'label'      => __( 'Border width', 'trx_addons' ),
						'type'       => \Elementor\Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', 'em', 'rem', '%' ],
						'selectors'  => [
							'.sc_layouts_cart_panel_style_{{ID}}.sc_layouts_cart_panel.sc_layouts_panel .sc_layouts_panel_inner .sc_layouts_cart_panel_header' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
						'condition' => [
							'type' => 'panel',
							'top_bar_border_type!' => 'none',
						],
					]
				);

				$this->add_control(
					'top_bar_border_color',
					[
						'label'     => __( 'Border Color', 'trx_addons' ),
						'type'      => \Elementor\Controls_Manager::COLOR,
						'selectors' => [
							'.sc_layouts_cart_panel_style_{{ID}}.sc_layouts_cart_panel.sc_layouts_panel .sc_layouts_panel_inner .sc_layouts_cart_panel_header' => 'border-color: {{VALUE}};',
						],
						'condition' => [
							'type' => 'panel',
							'top_bar_border_type!' => 'none',
						],
					]
				);

				$this->add_responsive_control(
					'top_bar_style_padding',
					[
						'label'      => __( 'Padding', 'trx_addons' ),
						'type'       => \Elementor\Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', 'em', 'rem', '%' ],
						'selectors'  => [
							'.sc_layouts_cart_panel_style_{{ID}}.sc_layouts_cart_panel.sc_layouts_panel .sc_layouts_panel_inner .sc_layouts_cart_panel_header' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
						'condition' => [
							'type' => 'panel',
						],
					]
				);

				$this->add_responsive_control(
					'top_bar_style_margin',
					[
						'label'      => __( 'Margin', 'trx_addons' ),
						'type'       => \Elementor\Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', 'em', 'rem', '%' ],
						'separator'  => 'after',
						'selectors'  => [
							'.sc_layouts_cart_panel_style_{{ID}}.sc_layouts_cart_panel.sc_layouts_panel .sc_layouts_panel_inner .sc_layouts_cart_panel_header' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
						'condition' => [
							'type' => 'panel',
						],
					]
				);

				$this->add_group_control(
					\Elementor\Group_Control_Typography::get_type(),
					[
						'label' => __( 'Cart Title Typography', 'trx_addons' ),
						'name'      => 'cart_title_style_typography',
						'selector'  => '.sc_layouts_cart_panel_style_{{ID}}.sc_layouts_cart_panel.sc_layouts_panel .sc_layouts_cart_panel_title_text',
						'condition' => [
							'type' => 'panel',
						],
					]
				);

				$this->add_control(
					'cart_title_style_color',
					[
						'label'     => __( 'Cart Title Color', 'trx_addons' ),
						'type'      => \Elementor\Controls_Manager::COLOR,
						'selectors' => [
							'.sc_layouts_cart_panel_style_{{ID}}.sc_layouts_cart_panel.sc_layouts_panel .sc_layouts_cart_panel_title .sc_layouts_cart_panel_title_text' => 'color: {{VALUE}};',
						],
						'condition' => [
							'type' => 'panel',
						],
					]
				);

				$this->add_control(
					'heading_product_count_badge_style',
					[
						'label' => __( 'Product Count Badge', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::HEADING,
						'separator' => 'before',
						'condition' => [
							'type' => 'panel',
						],
					]
				);

				$this->add_control(
					'panel_product_count_style_va',
					[
						'label'     => __( 'Vertical Alignment', 'trx_addons' ),
						'type'      => \Elementor\Controls_Manager::CHOOSE,
						'default'   => 'top',
						'options'   => [
							'top' => [
								'title' => __( 'Top', 'trx_addons' ),
								'icon' => 'eicon-justify-start-v',
							],
							'middle' => [
								'title' => __( 'Middle', 'trx_addons' ),
								'icon' => 'eicon-align-center-v',
							],
							'bottom' => [
								'title' => __( 'Bottom', 'trx_addons' ),
								'icon' => 'eicon-justify-end-v',
							],
						],
						'selectors' => [
							'.sc_layouts_cart_panel_style_{{ID}}.sc_layouts_cart_panel.sc_layouts_panel .sc_layouts_cart_panel_title .sc_layouts_cart_items_short' => 'vertical-align: {{VALUE}};',
						],
						'condition' => [
							'type' => 'panel',
						],
					]
				);

				$this->add_group_control(
					\Elementor\Group_Control_Typography::get_type(),
					[
						'label'     => __( 'Product Count Typography', 'trx_addons' ),
						'name'      => 'panel_product_count_style_typography',
						'selector'  => '.sc_layouts_cart_panel_style_{{ID}}.sc_layouts_cart_panel.sc_layouts_panel .sc_layouts_cart_panel_title .sc_layouts_cart_items_short',
						'condition' => [
							'type' => 'panel',
						],
					]
				);

				$this->add_control(
					'panel_product_count_style_color',
					[
						'label'     => __( 'Product Count Color', 'trx_addons' ),
						'type'      => \Elementor\Controls_Manager::COLOR,
						'selectors' => [
							'.sc_layouts_cart_panel_style_{{ID}}.sc_layouts_cart_panel.sc_layouts_panel .sc_layouts_cart_panel_title .sc_layouts_cart_items_short' => 'color: {{VALUE}};',
						],
						'condition' => [
							'type' => 'panel',
						],
					]
				);

				$this->add_control(
					'panel_product_count_style_bg_color',
					[
						'label'     => __( 'Background Color', 'trx_addons' ),
						'type'      => \Elementor\Controls_Manager::COLOR,
						'selectors' => [
							'.sc_layouts_cart_panel_style_{{ID}}.sc_layouts_cart_panel.sc_layouts_panel .sc_layouts_cart_panel_title .sc_layouts_cart_items_short' => 'background-color: {{VALUE}};',
						],
						'condition' => [
							'type' => 'panel',
						],
					]
				);

				$this->add_responsive_control(
					'panel_product_count_style_min_width',
					[
						'label' => __( 'Box Size', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::SLIDER,
						'size_units' => [ 'px', 'em', 'rem', '%' ],
						'selectors' => [
							'.sc_layouts_cart_panel_style_{{ID}}.sc_layouts_cart_panel.sc_layouts_panel .sc_layouts_cart_panel_title .sc_layouts_cart_items_short' => 'min-width: {{SIZE}}{{UNIT}}; min-height: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}};',
						],
						'condition' => [
							'type' => 'panel',
						],
					]
				);

				$this->add_responsive_control(
					'panel_product_count_style_padding',
					[
						'label'      => __( 'Product Count Padding', 'trx_addons' ),
						'type'       => \Elementor\Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', 'em', 'rem', '%' ],
						'selectors'  => [
							'.sc_layouts_cart_panel_style_{{ID}}.sc_layouts_cart_panel.sc_layouts_panel .sc_layouts_cart_panel_title .sc_layouts_cart_items_short' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
						'condition' => [
							'type' => 'panel',
						],
					]
				);

				$this->add_responsive_control(
					'panel_product_count_style_margin',
					[
						'label'      => __( 'Product Count Margin', 'trx_addons' ),
						'type'       => \Elementor\Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', 'em', 'rem', '%' ],
						'selectors'  => [
							'.sc_layouts_cart_panel_style_{{ID}}.sc_layouts_cart_panel.sc_layouts_panel .sc_layouts_cart_panel_title .sc_layouts_cart_items_short' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
						'condition' => [
							'type' => 'panel',
						],
					]
				);

				$this->add_control(
					'panel_product_count_style_border_type',
					[
						'label'     => __( 'Border Type', 'trx_addons' ),
						'type'      => \Elementor\Controls_Manager::SELECT,
						'default'   => 'none',
						'options'   => [
							'none'   => __( 'None', 'trx_addons' ),
							'solid'  => __( 'Solid', 'trx_addons' ),
							'double' => __( 'Double', 'trx_addons' ),
							'dotted' => __( 'Dotted', 'trx_addons' ),
							'dashed' => __( 'Dashed', 'trx_addons' ),
						],
						'selectors' => [
							'.sc_layouts_cart_panel_style_{{ID}}.sc_layouts_cart_panel.sc_layouts_panel .sc_layouts_cart_panel_title .sc_layouts_cart_items_short' => 'border-style: {{VALUE}};',
						],
						'condition' => [
							'type' => 'panel',
						],
					]
				);

				$this->add_responsive_control(
					'panel_product_count_style_border_width',
					[
						'label'      => __( 'Border width', 'trx_addons' ),
						'type'       => \Elementor\Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', 'em', 'rem', '%' ],
						'selectors'  => [
							'.sc_layouts_cart_panel_style_{{ID}}.sc_layouts_cart_panel.sc_layouts_panel .sc_layouts_cart_panel_title .sc_layouts_cart_items_short' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
						'condition' => [
							'type' => 'panel',
							'panel_product_count_style_border_type!' => 'none',
						],
					]
				);

				$this->add_control(
					'panel_product_count_style_border_color',
					[
						'label'     => __( 'Border Color', 'trx_addons' ),
						'type'      => \Elementor\Controls_Manager::COLOR,
						'selectors' => [
							'.sc_layouts_cart_panel_style_{{ID}}.sc_layouts_cart_panel.sc_layouts_panel .sc_layouts_cart_panel_title .sc_layouts_cart_items_short' => 'border-color: {{VALUE}};',
						],
						'condition' => [
							'type' => 'panel',
							'panel_product_count_style_border_type!' => 'none',
						],
					]
				);

				$this->add_responsive_control(
					'panel_product_count_style_border_radius',
					[
						'label' => __( 'Border Radius', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::SLIDER,
						'size_units' => [ 'px', 'em', 'rem', '%' ],
						'selectors' => [
							'.sc_layouts_cart_panel_style_{{ID}}.sc_layouts_cart_panel.sc_layouts_panel .sc_layouts_cart_panel_title .sc_layouts_cart_items_short' => 'border-radius: {{SIZE}}{{UNIT}};',
						],
						'condition' => [
							'type' => 'panel',
						],
					]
				);

				$this->add_control(
					'heading_panel_subtotal_style',
					[
						'label' => __( 'Price Total', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::HEADING,
						'separator' => 'before',
						'condition' => [
							'type' => 'panel',
						],
					]
				);

				$this->add_group_control(
					\Elementor\Group_Control_Typography::get_type(),
					[
						'name'      => 'panel_subtotal_style_typography',
						'label' => __( 'Price Typography', 'trx_addons' ),
						'selector'  => '.sc_layouts_cart_panel_style_{{ID}}.sc_layouts_cart_panel.sc_layouts_panel .woocommerce.widget_shopping_cart .total .amount',
						'condition' => [
							'type' => 'panel',
						],
					]
				);

				$this->add_control(
					'panel_subtotal_style_color',
					[
						'label'     => __( 'Price Color', 'trx_addons' ),
						'type'      => \Elementor\Controls_Manager::COLOR,
						'selectors' => [
							'.sc_layouts_cart_panel_style_{{ID}}.sc_layouts_cart_panel.sc_layouts_panel .woocommerce.widget_shopping_cart .total .amount' => 'color: {{VALUE}};',
						],
						'condition' => [
							'type' => 'panel',
						],
					]
				);

				$this->add_group_control(
					\Elementor\Group_Control_Typography::get_type(),
					[
						'name'      => 'panel_subtotal_label_style_typography',
						'label' => __( 'Label Typography', 'trx_addons' ),
						'selector'  => '.sc_layouts_cart_panel_style_{{ID}}.sc_layouts_cart_panel.sc_layouts_panel .woocommerce.widget_shopping_cart .total',
						'condition' => [
							'type' => 'panel',
						],
					]
				);

				$this->add_control(
					'panel_subtotal_label_style_color',
					[
						'label'     => __( 'Label Color', 'trx_addons' ),
						'type'      => \Elementor\Controls_Manager::COLOR,
						'selectors' => [
							'.sc_layouts_cart_panel_style_{{ID}}.sc_layouts_cart_panel.sc_layouts_panel .woocommerce.widget_shopping_cart .total' => 'color: {{VALUE}};',
						],
						'condition' => [
							'type' => 'panel',
						],
					]
				);

				$this->add_responsive_control(
					'panel_subtotal_margin',
					[
						'label'      => __( 'Margin', 'trx_addons' ),
						'type'       => \Elementor\Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', 'em', 'rem', '%' ],
						'default'    => [
							'top'      => '0',
							'right'    => '0',
							'bottom'   => '1',
							'left'     => '0',
							'unit'     => 'em',
							'isLinked' => false,
						],
						'selectors'  => [
							'.sc_layouts_cart_panel_style_{{ID}}.sc_layouts_cart_panel.sc_layouts_panel .woocommerce.widget_shopping_cart .total' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
						'condition' => [
							'type' => 'panel',
						],
					]
				);

				$this->add_responsive_control(
					'panel_subtotal_padding',
					[
						'label'      => __( 'Padding', 'trx_addons' ),
						'type'       => \Elementor\Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', 'em', 'rem', '%' ],
						'selectors'  => [
							'.sc_layouts_cart_panel_style_{{ID}}.sc_layouts_cart_panel.sc_layouts_panel .woocommerce.widget_shopping_cart .total' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
						'condition' => [
							'type' => 'panel',
						],
					]
				);

				$this->add_control(
					'panel_subtotal_style_border_type',
					[
						'label'     => __( 'Border Type', 'trx_addons' ),
						'type'      => \Elementor\Controls_Manager::SELECT,
						'default'   => 'none',
						'options'   => [
							'none'   => __( 'None', 'trx_addons' ),
							'solid'  => __( 'Solid', 'trx_addons' ),
							'double' => __( 'Double', 'trx_addons' ),
							'dotted' => __( 'Dotted', 'trx_addons' ),
							'dashed' => __( 'Dashed', 'trx_addons' ),
						],
						'selectors' => [
							'.sc_layouts_cart_panel_style_{{ID}}.sc_layouts_cart_panel.sc_layouts_panel .sc_layouts_cart_panel_widget .total' => 'border-top-style: {{VALUE}};',
						],
						'condition' => [
							'type' => 'panel',
						],
					]
				);

				$this->add_responsive_control(
					'panel_subtotal_style_border_width',
					[
						'label' => __( 'Border Width', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::SLIDER,
						'default' => [
							'size' => '3',
							'unit' => 'px'
						],
						'size_units' => [ 'px', 'em', 'rem', '%' ],
						'selectors' => [
							'.sc_layouts_cart_panel_style_{{ID}}.sc_layouts_cart_panel.sc_layouts_panel .sc_layouts_cart_panel_widget .total' => 'border-top-width: {{SIZE}}{{UNIT}};',
						],
						'condition' => [
							'type' => 'panel',
							'panel_subtotal_style_border_type!' => 'none',
						],
					]
				);

				$this->add_control(
					'panel_subtotal_style_border_color',
					[
						'label'     => __( 'Border Color', 'trx_addons' ),
						'type'      => \Elementor\Controls_Manager::COLOR,
						'selectors' => [
							'.sc_layouts_cart_panel_style_{{ID}}.sc_layouts_cart_panel.sc_layouts_panel .sc_layouts_cart_panel_widget .total' => 'border-top-color: {{VALUE}};',
						],
						'condition' => [
							'type' => 'panel',
							'panel_subtotal_style_border_type!' => 'none',
						],
					]
				);

				$this->end_controls_section();
			}

			/**
			 * Style Tab: Cart Item
			 */
			protected function register_style_controls_cart_item() {

				$this->start_controls_section(
					'section_cart_item',
					[
						'label' => __( 'Cart Items', 'trx_addons' ),
						'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
						'condition' => [
							'type!' => 'button',
						],
					]
				);

				$this->add_responsive_control(
					'cart_list_style_gap',
					[
						'label' => __( 'Cart Items Gap', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::SLIDER,
						'size_units' => [ 'px', 'em', 'rem', '%' ],
						'selectors' => [
							'{{WRAPPER}} .woocommerce ul.product_list_widget li:nth-child(n + 2),
							 .sc_layouts_cart_panel_style_{{ID}} .woocommerce ul.product_list_widget li:nth-child(n + 2)' => 'margin-top: {{SIZE}}{{UNIT}};',
						],
						'condition' => [
							'type!' => 'button',
						],
					]
				);

				// Single Item
				//--------------------------------------
				$this->add_control(
					'cart_item_style_vertical_alignment',
					[
						'label'     => __( 'Vertical Alignment', 'trx_addons' ),
						'type'      => \Elementor\Controls_Manager::CHOOSE,
						'default'   => 'top',
						'options'   => [
							'top' => [
								'title' => __( 'Top', 'trx_addons' ),
								'icon' => 'eicon-justify-start-v',
							],
							'center' => [
								'title' => __( 'Center', 'trx_addons' ),
								'icon' => 'eicon-align-center-v',
							],
							'bottom' => [
								'title' => __( 'Bottom', 'trx_addons' ),
								'icon' => 'eicon-justify-end-v',
							],
						],
						'condition' => [
							'type!' => 'button',
						],
					]
				);

				$this->add_control(
					'heading_cart_item_style',
					[
						'label' => __( 'Product Item', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::HEADING,
						'separator' => 'before',
						'condition' => [
							'type!' => 'button',
						],
					]
				);

				$this->add_control(
					'cart_item_style_bg_color',
					[
						'label'     => __( 'Background Color', 'trx_addons' ),
						'type'      => \Elementor\Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} ul.cart_list li,
							 .sc_layouts_cart_panel_style_{{ID}} ul.cart_list li' => 'background-color: {{VALUE}};',
						],
						'condition' => [
							'type!' => 'button',
						],
					]
				);

				$this->add_responsive_control(
					'cart_item_style_padding',
					[
						'label'      => __( 'Padding', 'trx_addons' ),
						'type'       => \Elementor\Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', 'em', 'rem', '%' ],
						'selectors'  => [
							'{{WRAPPER}} .woocommerce.widget_shopping_cart ul.cart_list li,
							 .sc_layouts_cart_panel_style_{{ID}}.sc_layouts_cart_panel .sc_layouts_cart_panel_widget .widget_shopping_cart ul.cart_list li' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
						'condition' => [
							'type!' => 'button',
						],
					]
				);

				$this->add_control(
					'cart_item_style_border_type',
					[
						'label'     => __( 'Border Type', 'trx_addons' ),
						'type'      => \Elementor\Controls_Manager::SELECT,
						'default'   => 'none',
						'options'   => [
							'none'   => __( 'None', 'trx_addons' ),
							'solid'  => __( 'Solid', 'trx_addons' ),
							'double' => __( 'Double', 'trx_addons' ),
							'dotted' => __( 'Dotted', 'trx_addons' ),
							'dashed' => __( 'Dashed', 'trx_addons' ),
						],
						'selectors' => [
							'{{WRAPPER}} ul.cart_list li,
							 .sc_layouts_cart_panel_style_{{ID}} ul.cart_list li' => 'border-style: {{VALUE}};',
						],
						'condition' => [
							'type!' => 'button',
						],
					]
				);

				$this->add_responsive_control(
					'cart_item_style_border_width',
					[
						'label'      => __( 'Border width', 'trx_addons' ),
						'type'       => \Elementor\Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', 'em', 'rem', '%' ],
						'selectors'  => [
							'{{WRAPPER}} ul.cart_list li,
							 .sc_layouts_cart_panel_style_{{ID}} ul.cart_list li' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
						'condition' => [
							'type!' => 'button',
							'cart_item_style_border_type!' => 'none',
						],
					]
				);

				$this->add_control(
					'cart_item_style_border_color',
					[
						'label'     => __( 'Border Color', 'trx_addons' ),
						'type'      => \Elementor\Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} ul.cart_list li,
							 .sc_layouts_cart_panel_style_{{ID}} ul.cart_list li' => 'border-color: {{VALUE}};',
						],
						'condition' => [
							'type!' => 'button',
							'cart_item_style_border_type!' => 'none',
						],
					]
				);

				$this->add_responsive_control(
					'cart_item_style_border_radius',
					[
						'label' => __( 'Border Radius', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::SLIDER,
						'size_units' => [ 'px', 'em', 'rem', '%' ],
						'selectors' => [
							'{{WRAPPER}} ul.cart_list li,
							 .sc_layouts_cart_panel_style_{{ID}} ul.cart_list li' => 'border-radius: {{SIZE}}{{UNIT}};',
						],
						'condition' => [
							'type!' => 'button',
						],
					]
				);

				$this->add_group_control(
					\Elementor\Group_Control_Typography::get_type(),
					[
						'label' => __( 'Product Name', 'trx_addons' ),
						'name'      => 'cart_item_name_style_typography',
						'selector'  => '{{WRAPPER}} .trx_addons_sc_layouts_cart_mini_cart_item_name,
										 .sc_layouts_cart_panel_style_{{ID}} .trx_addons_sc_layouts_cart_mini_cart_item_name',
						'condition' => [
							'type!' => 'button',
						],
					]
				);

				$this->add_control(
					'cart_item_name_style_color',
					[
						'label'     => __( 'Product Name Color', 'trx_addons' ),
						'type'      => \Elementor\Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .trx_addons_sc_layouts_cart_mini_cart_item_name,
							 .sc_layouts_cart_panel_style_{{ID}} .trx_addons_sc_layouts_cart_mini_cart_item_name' => 'color: {{VALUE}};',
						],
						'condition' => [
							'type!' => 'button',
						],
					]
				);

				$this->add_responsive_control(
					'cart_item_name_bottom_gap',
					[
						'label' => __( 'Product Name Gap', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::SLIDER,
						'size_units' => [ 'px', 'em', 'rem' ],
						'selectors' => [
							'{{WRAPPER}} .trx_addons_sc_layouts_cart_mini_cart_item_name,
							 .sc_layouts_cart_panel_style_{{ID}} .trx_addons_sc_layouts_cart_mini_cart_item_name' => 'margin-bottom: {{SIZE}}{{UNIT}};',
						],
						'condition' => [
							'type!' => 'button',
						],
					]
				);

				$this->add_group_control(
					\Elementor\Group_Control_Typography::get_type(),
					[
						'label' => __( 'Price & Quantity', 'trx_addons' ),
						'name'      => 'cart_item_price_quantity_style_typography',
						'selector'  => '{{WRAPPER}} .widget_shopping_cart_content ul.cart_list li .quantity,
										 .sc_layouts_cart_panel_style_{{ID}} .widget_shopping_cart_content ul.cart_list li .quantity',
						'condition' => [
							'type!' => 'button',
						],
					]
				);

				$this->add_control(
					'cart_item_price_quantity_style_color',
					[
						'label'     => __( 'Price & Quantity Color', 'trx_addons' ),
						'type'      => \Elementor\Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .widget_shopping_cart_content ul.cart_list li .quantity,
							 .sc_layouts_cart_panel_style_{{ID}} .widget_shopping_cart_content ul.cart_list li .quantity,
							 {{WRAPPER}} .widget_shopping_cart_content ul.cart_list li .quantity .amount,
							 .sc_layouts_cart_panel_style_{{ID}} .widget_shopping_cart_content ul.cart_list li .quantity .amount' => 'color: {{VALUE}};',
						],
						'condition' => [
							'type!' => 'button',
						],
					]
				);

				// Item Image
				//---------------------------------------
				$this->add_control(
					'heading_cart_item_image_style',
					[
						'label' => __( 'Product Image', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::HEADING,
						'separator' => 'before',
						'condition' => [
							'type!' => 'button',
						],
					]
				);

				$this->add_responsive_control(
					'cart_item_image_style_size',
					[
						'label' => __( 'Image Size', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::SLIDER,
						'size_units' => [ 'px', 'em', 'rem', '%' ],
						'selectors' => [
							'{{WRAPPER}} .widget_shopping_cart ul.cart_list li img,
							 .sc_layouts_cart_panel_style_{{ID}}.sc_layouts_cart_panel .sc_layouts_cart_panel_widget .widget_shopping_cart ul.cart_list li img' => 'width: {{SIZE}}{{UNIT}};',
						],
					]
				);

				$this->add_control(
					'cart_item_image_style_position',
					[
						'label' => __( 'Image Position', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::CHOOSE,
						'default' => 'left',
						'options' => [
							'left' => [
								'title' => __( 'Before', 'trx_addons' ),
								'icon' => 'eicon-h-align-left',
							],
							'right' => [
								'title' => __( 'After', 'trx_addons' ),
								'icon' => 'eicon-h-align-right',
							],
						],
						'condition' => [
							'type!' => 'button',
						],
					]
				);

				$this->add_control(
					'cart_item_image_style_border_type',
					[
						'label'     => __( 'Image Border', 'trx_addons' ),
						'type'      => \Elementor\Controls_Manager::SELECT,
						'default'   => 'none',
						'options'   => [
							'none'   => __( 'None', 'trx_addons' ),
							'solid'  => __( 'Solid', 'trx_addons' ),
							'double' => __( 'Double', 'trx_addons' ),
							'dotted' => __( 'Dotted', 'trx_addons' ),
							'dashed' => __( 'Dashed', 'trx_addons' ),
						],
						'selectors' => [
							'{{WRAPPER}} ul.cart_list img,
							 .sc_layouts_cart_panel_style_{{ID}} ul.cart_list img' => 'border-style: {{VALUE}};',
						],
						'condition' => [
							'type!' => 'button',
						],
					]
				);

				$this->add_responsive_control(
					'cart_item_image_style_border_width',
					[
						'label'      => __( 'Border width', 'trx_addons' ),
						'type'       => \Elementor\Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', 'em', 'rem', '%' ],
						'selectors'  => [
							'{{WRAPPER}} ul.cart_list img,
							 .sc_layouts_cart_panel_style_{{ID}} ul.cart_list img' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
						'condition' => [
							'type!' => 'button',
							'cart_item_image_style_border_type!' => 'none',
						],
					]
				);

				$this->add_control(
					'cart_item_image_style_border_color',
					[
						'label'     => __( 'Border Color', 'trx_addons' ),
						'type'      => \Elementor\Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} ul.cart_list img,
							 .sc_layouts_cart_panel_style_{{ID}} ul.cart_list img' => 'border-color: {{VALUE}};',
						],
						'condition' => [
							'type!' => 'button',
							'cart_item_image_style_border_type!' => 'none',
						],
					]
				);

				$this->add_responsive_control(
					'cart_item_image_style_border_radius',
					[
						'label' => __( 'Border Radius', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::SLIDER,
						'size_units' => [ 'px', 'em', 'rem', '%' ],
						'selectors' => [
							'{{WRAPPER}} ul.cart_list img,
							 .sc_layouts_cart_panel_style_{{ID}}.sc_layouts_cart_panel .sc_layouts_cart_panel_widget .widget_shopping_cart ul.cart_list li img' => 'border-radius: {{SIZE}}{{UNIT}};',
						],
						'condition' => [
							'type!' => 'button',
						],
					]
				);

				$this->add_responsive_control(
					'cart_item_image_style_margin',
					[
						'label'      => __( 'Image Margins', 'trx_addons' ),
						'type'       => \Elementor\Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', 'em', 'rem', '%' ],
						'selectors'  => [
							'{{WRAPPER}} .woocommerce ul.cart_list li .trx_addons_sc_layouts_cart_mini_cart_item_link_image,
							 .sc_layouts_cart_panel_style_{{ID}} .woocommerce ul.cart_list li .trx_addons_sc_layouts_cart_mini_cart_item_link_image,
							 {{WRAPPER}} .woocommerce ul.cart_list li .trx_addons_sc_layouts_cart_mini_cart_item_image,
							 .sc_layouts_cart_panel_style_{{ID}} .woocommerce ul.cart_list li .trx_addons_sc_layouts_cart_mini_cart_item_image' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
						'condition' => [
							'type!' => 'button',
						],
					]
				);

				// Item Remove Button
				//---------------------------------------
				$this->add_control(
					'heading_cart_item_remove_icon',
					[
						'label' => __( 'Remove Icon', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::HEADING,
						'separator' => 'before',
						'condition' => [
							'type!' => 'button',
						],
					]
				);

				$this->add_control(
					'select_cart_item_remove_icon',
					[
						'label'       => __( 'Choose Icon', 'trx_addons' ),
						'type'        => \Elementor\Controls_Manager::ICONS,
						'default'     => [
							'value' => '',
							'library' => 'fa-solid',
						],
						'recommended' => [
							'fa-regular' => [
								'window-close',
							],
							'fa-solid'   => [
								'window-close',
							],
						],
						'condition' => [
							'type!' => 'button',
						],
					]
				);

				$this->add_responsive_control(
					'select_cart_item_remove_icon_size',
					[
						'label' => __( 'Icon Size', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::SLIDER,
						'size_units' => [ 'px', 'em', 'rem', '%' ],
						'selectors' => [
							'{{WRAPPER}} ul.cart_list a.remove,
							 .sc_layouts_cart_panel_style_{{ID}} ul.cart_list a.remove' => 'font-size: {{SIZE}}{{UNIT}};',
						],
					]
				);

				$this->add_control(
					'cart_item_remove_icon_style_position',
					[
						'label' => __( 'Icon Position', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::CHOOSE,
						'default' => 'left',
						'options' => [
							'left' => [
								'title' => __( 'Before', 'trx_addons' ),
								'icon' => 'eicon-h-align-left',
							],
							'right' => [
								'title' => __( 'After', 'trx_addons' ),
								'icon' => 'eicon-h-align-right',
							],
						],
						'condition' => [
							'type!' => 'button',
						],
					]
				);

				$this->add_control(
					'cart_item_remove_icon_style_valign',
					[
						'label' => __( 'Vertical Alignment', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::CHOOSE,
						'default' => 'flex-start',
						'options' => [
							'flex-start' => [
								'title' => __( 'Top', 'trx_addons' ),
								'icon' => 'eicon-align-start-v',
							],
							'center' => [
								'title' => __( 'Center', 'trx_addons' ),
								'icon' => 'eicon-align-center-v',
							],
							'flex-end' => [
								'title' => __( 'Bottom', 'trx_addons' ),
								'icon' => 'eicon-align-end-v',
							],
						],
						'selectors' => [
							'{{WRAPPER}} ul.cart_list > li,
							 .sc_layouts_cart_panel_style_{{ID}} ul.cart_list > li' => 'align-items: {{VALUE}};',
						],
						'condition' => [
							'type!' => 'button',
						],
					]
				);

				$this->add_responsive_control(
					'cart_item_remove_style_margin',
					[
						'label'      => __( 'Icon Margins', 'trx_addons' ),
						'type'       => \Elementor\Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', 'em', 'rem', '%' ],
						'default'    => [
							'top'	  => '0',
							'right'	  => '6',
							'bottom'  => '0',
							'left'	  => '0',
							'isLinked' => false,
						],
						'selectors'  => [
							'{{WRAPPER}} .sc_layouts_cart_widget .widget_shopping_cart ul.cart_list li a.remove,
							 .sc_layouts_cart_panel_style_{{ID}}.sc_layouts_cart_panel .sc_layouts_cart_panel_widget .widget_shopping_cart ul.cart_list li a.remove' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
						'condition' => [
							'type!' => 'button',
						],
					]
				);

				$this->start_controls_tabs( 'tabs_cart_item_remove_icon_style' );

				$this->start_controls_tab(
					'tab_cart_item_remove_icon_normal',
					[
						'label' => __( 'Normal', 'trx_addons' ),
						'condition' => [
							'type!' => 'button',
						],
					]
				);

				$this->add_control(
					'cart_item_remove_icon_color',
					[
						'label'     => __( 'Icon Color', 'trx_addons' ),
						'type'      => \Elementor\Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} ul.cart_list .mini_cart_item a.remove,
							 .sc_layouts_cart_panel_style_{{ID}} ul.cart_list .mini_cart_item a.remove' => '--var-trx_addons_sc_layouts_cart_item_close_color: {{VALUE}} !important;',
							'{{WRAPPER}} ul.cart_list a.remove .sc_layouts_cart_item_close_icon,
							 .sc_layouts_cart_panel_style_{{ID}} ul.cart_list a.remove .sc_layouts_cart_item_close_icon' => 'color: {{VALUE}};',
							'{{WRAPPER}} ul.cart_list a.remove svg,
							 .sc_layouts_cart_panel_style_{{ID}} ul.cart_list a.remove svg' => 'fill: {{VALUE}};',
						],
						'condition' => [
							'type!' => 'button',
						],
					]
				);

				$this->end_controls_tab();

				$this->start_controls_tab(
					'tab_cart_item_remove_icon_hover',
					[
						'label' => __( 'Hover', 'trx_addons' ),
						'condition' => [
							'type!' => 'button',
						],
					]
				);

				$this->add_control(
					'cart_item_remove_icon_color_hover',
					[
						'label'     => __( 'Icon Color', 'trx_addons' ),
						'type'      => \Elementor\Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} ul.cart_list .mini_cart_item a.remove:hover,
							 .sc_layouts_cart_panel_style_{{ID}} ul.cart_list .mini_cart_item a.remove:hover' => '--var-trx_addons_sc_layouts_cart_item_close_color: {{VALUE}} !important;',
							'{{WRAPPER}} ul.cart_list a.remove:hover .sc_layouts_cart_item_close_icon,
							 .sc_layouts_cart_panel_style_{{ID}} ul.cart_list a.remove:hover .sc_layouts_cart_item_close_icon' => 'color: {{VALUE}};',
							'{{WRAPPER}} ul.cart_list a.remove:hover svg,
							 .sc_layouts_cart_panel_style_{{ID}} ul.cart_list a.remove:hover svg' => 'fill: {{VALUE}};',
						],
						'condition' => [
							'type!' => 'button',
						],
					]
				);

				$this->end_controls_tab();

				$this->end_controls_tabs();

				$this->end_controls_section();
			}

			/**
			 * Style Tab: Buttons
			 */
			protected function register_style_controls_buttons() {

				$this->start_controls_section(
					'section_buttons_style',
					[
						'label' => __( 'Buttons', 'trx_addons' ),
						'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
						'condition' => [
							'type!' => 'button',
						],
					]
				);

				$this->add_control(
					'buttons_style_display',
					[
						'label'     => __( 'Icon Display', 'trx_addons' ),
						'type'      => \Elementor\Controls_Manager::SELECT,
						'default'   => 'inline',
						'options'   => [
							'inline' => __( 'Inline', 'trx_addons' ),
							'block'  => __( 'Block', 'trx_addons' ),
						],
						'condition' => [
							'type!' => 'button',
						],
					]
				);

				$this->add_responsive_control(
					'buttons_style_gap',
					[
						'label' => __( 'Buttons Gap', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::SLIDER,
						'size_units' => [ 'px', 'em', 'rem', '%' ],
						'selectors' => [
							'{{WRAPPER}} .sc_layouts_cart_buttons-inline .woocommerce-mini-cart__buttons > a:not(:last-child),
							 .sc_layouts_cart_panel_style_{{ID}}.sc_layouts_cart_buttons-inline .woocommerce-mini-cart__buttons > a:not(:last-child)' => 'margin-right: {{SIZE}}{{UNIT}};',
							'{{WRAPPER}} .sc_layouts_cart_buttons-block .woocommerce-mini-cart__buttons > a:not(:last-child),
							 .sc_layouts_cart_panel_style_{{ID}}.sc_layouts_cart_buttons-block .woocommerce-mini-cart__buttons > a:not(:last-child)' => 'margin-bottom: {{SIZE}}{{UNIT}};',
						],
						'condition' => [
							'type!' => 'button',
						],
					]
				);

				$this->add_control(
					'heading_buttons_style_view_cart',
					[
						'label' => __( 'View Cart', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::HEADING,
						'condition' => [
							'type!' => 'button',
						],
					]
				);

				$this->add_group_control(
					\Elementor\Group_Control_Typography::get_type(),
					[
						'name'      => 'buttons_style_view_cart_typography',
						'label'     => __( 'Typography', 'trx_addons' ),
						'selector'  => '{{WRAPPER}} .woocommerce-mini-cart__buttons > a:nth-child(1),
									     .sc_layouts_cart_panel_style_{{ID}} .woocommerce-mini-cart__buttons > a:nth-child(1)',
						'condition' => [
							'type!' => 'button',
						],
					]
				);

				$this->add_responsive_control(
					'buttons_style_view_cart_padding',
					[
						'label'      => __( 'Padding', 'trx_addons' ),
						'type'       => \Elementor\Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
						'selectors'  => [
							'{{WRAPPER}} .woocommerce-mini-cart__buttons > a:nth-child(1),
							 .sc_layouts_cart_panel_style_{{ID}} .woocommerce-mini-cart__buttons > a:nth-child(1)' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
						'condition'  => [
							'type!' => 'button',
						],
					]
				);

				$this->add_control(
					'buttons_style_view_cart_style_border_type',
					[
						'label'     => __( 'Border Type', 'trx_addons' ),
						'type'      => \Elementor\Controls_Manager::SELECT,
						'default'   => 'none',
						'options'   => [
							'none'   => __( 'None', 'trx_addons' ),
							'solid'  => __( 'Solid', 'trx_addons' ),
							'double' => __( 'Double', 'trx_addons' ),
							'dotted' => __( 'Dotted', 'trx_addons' ),
							'dashed' => __( 'Dashed', 'trx_addons' ),
						],
						'selectors' => [
							'{{WRAPPER}} .woocommerce-mini-cart__buttons > a:nth-child(1),
							 .sc_layouts_cart_panel_style_{{ID}} .woocommerce-mini-cart__buttons > a:nth-child(1)' => 'border-style: {{VALUE}};',
						],
						'condition' => [
							'type!' => 'button',
						],
					]
				);

				$this->add_responsive_control(
					'buttons_style_view_cart_style_border_width',
					[
						'label'      => __( 'Border width', 'trx_addons' ),
						'type'       => \Elementor\Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', 'em', 'rem', '%' ],
						'selectors'  => [
							'{{WRAPPER}} .woocommerce-mini-cart__buttons > a:nth-child(1),
							 .sc_layouts_cart_panel_style_{{ID}} .woocommerce-mini-cart__buttons > a:nth-child(1)' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
						],
						'condition' => [
							'type!' => 'button',
							'buttons_style_view_cart_style_border_type!' => 'none',
						],
					]
				);

				$this->add_responsive_control(
					'buttons_style_view_cart_border_radius',
					[
						'label'      => __( 'Border Radius', 'trx_addons' ),
						'type'       => \Elementor\Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
						'selectors'  => [
							'{{WRAPPER}} .woocommerce-mini-cart__buttons > a:nth-child(1),
							 .sc_layouts_cart_panel_style_{{ID}} .woocommerce-mini-cart__buttons > a:nth-child(1)' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
						'condition'  => [
							'type!' => 'button',
						],
					]
				);

				$this->start_controls_tabs( 'tabs_buttons_style_view_cart' );

				$this->start_controls_tab(
					'tab_buttons_style_view_cart_normal',
					[
						'label'     => __( 'Normal', 'trx_addons' ),
						'condition' => [
							'type!' => 'button',
						],
					]
				);

				$this->add_control(
					'buttons_style_view_cart_bg_color_normal',
					[
						'label'     => __( 'Background Color', 'trx_addons' ),
						'type'      => \Elementor\Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .woocommerce-mini-cart__buttons > a:nth-child(1),
							 .sc_layouts_cart_panel_style_{{ID}} .woocommerce-mini-cart__buttons > a:nth-child(1)' => '--theme-color-text_link: {{VALUE}}; background-color: {{VALUE}};',
						],
						'condition' => [
							'type!' => 'button',
						],
					]
				);

				$this->add_control(
					'buttons_style_view_cart_text_color_normal',
					[
						'label'     => __( 'Text Color', 'trx_addons' ),
						'type'      => \Elementor\Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .woocommerce-mini-cart__buttons > a:nth-child(1),
							 .sc_layouts_cart_panel_style_{{ID}} .woocommerce-mini-cart__buttons > a:nth-child(1)' => 'color: {{VALUE}};',
						],
						'condition' => [
							'type!' => 'button',
						],
					]
				);

				$this->add_control(
					'buttons_style_view_cart_style_border_color',
					[
						'label'     => __( 'Border Color', 'trx_addons' ),
						'type'      => \Elementor\Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .woocommerce-mini-cart__buttons > a:nth-child(1),
							 .sc_layouts_cart_panel_style_{{ID}} .woocommerce-mini-cart__buttons > a:nth-child(1)' => 'border-color: {{VALUE}};',
						],
						'condition' => [
							'type!' => 'button',
							'buttons_style_view_cart_style_border_type!' => 'none',
						],
					]
				);

				$this->end_controls_tab();

				$this->start_controls_tab(
					'tab_buttons_style_view_cart_hover',
					[
						'label'     => __( 'Hover', 'trx_addons' ),
						'condition' => [
							'type!' => 'button',
						],
					]
				);

				$this->add_control(
					'buttons_style_view_cart_bg_color_hover',
					[
						'label'     => __( 'Background Color', 'trx_addons' ),
						'type'      => \Elementor\Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .woocommerce-mini-cart__buttons.buttons > a:nth-child(1),
							 .sc_layouts_cart_panel_style_{{ID}} .woocommerce-mini-cart__buttons.buttons > a:nth-child(1)' => '--theme-color-text_hover: {{VALUE}};',
							'{{WRAPPER}} .woocommerce-mini-cart__buttons.buttons > a:nth-child(1):hover,
							 .sc_layouts_cart_panel_style_{{ID}} .woocommerce-mini-cart__buttons.buttons > a:nth-child(1):hover' => 'background-color: {{VALUE}};',
						],
						'condition' => [
							'type!' => 'button',
						],
					]
				);

				$this->add_control(
					'buttons_style_view_cart_text_color_hover',
					[
						'label'     => __( 'Text Color', 'trx_addons' ),
						'type'      => \Elementor\Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .woocommerce-mini-cart__buttons > a:nth-child(1):hover,
							 .sc_layouts_cart_panel_style_{{ID}} .woocommerce-mini-cart__buttons > a:nth-child(1):hover' => 'color: {{VALUE}}',
						],
						'condition' => [
							'type!' => 'button',
						],
					]
				);

				$this->add_control(
					'buttons_style_view_cart_border_color_hover',
					[
						'label'     => __( 'Border Color', 'trx_addons' ),
						'type'      => \Elementor\Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .woocommerce-mini-cart__buttons > a:nth-child(1):hover,
							 .sc_layouts_cart_panel_style_{{ID}} .woocommerce-mini-cart__buttons > a:nth-child(1):hover' => 'border-color: {{VALUE}}',
						],
						'condition' => [
							'type!' => 'button',
							'buttons_style_view_cart_style_border_type!' => 'none',
						],
					]
				);

				$this->end_controls_tab();

				$this->end_controls_tabs();

				$this->add_control(
					'heading_buttons_style_checkout_style_checkout',
					[
						'label' => __( 'Checkout', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::HEADING,
						'separator' => 'before',
						'condition' => [
							'type!' => 'button',
						],
					]
				);

				$this->add_group_control(
					\Elementor\Group_Control_Typography::get_type(),
					[
						'name'      => 'buttons_style_checkout_typography',
						'label'     => __( 'Typography', 'trx_addons' ),
						'selector'  => '{{WRAPPER}} .woocommerce-mini-cart__buttons > a:nth-child(2),
									     .sc_layouts_cart_panel_style_{{ID}} .woocommerce-mini-cart__buttons > a:nth-child(2)',
						'condition' => [
							'type!' => 'button',
						],
					]
				);

				$this->add_responsive_control(
					'buttons_style_checkout_padding',
					[
						'label'      => __( 'Padding', 'trx_addons' ),
						'type'       => \Elementor\Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
						'selectors'  => [
							'{{WRAPPER}} .woocommerce-mini-cart__buttons > a:nth-child(2),
							 .sc_layouts_cart_panel_style_{{ID}} .woocommerce-mini-cart__buttons > a:nth-child(2)' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
						'condition'  => [
							'type!' => 'button',
						],
					]
				);

				$this->add_control(
					'buttons_style_checkout_style_border_type',
					[
						'label'     => __( 'Border Type', 'trx_addons' ),
						'type'      => \Elementor\Controls_Manager::SELECT,
						'default'   => 'none',
						'options'   => [
							'none'   => __( 'None', 'trx_addons' ),
							'solid'  => __( 'Solid', 'trx_addons' ),
							'double' => __( 'Double', 'trx_addons' ),
							'dotted' => __( 'Dotted', 'trx_addons' ),
							'dashed' => __( 'Dashed', 'trx_addons' ),
						],
						'selectors' => [
							'{{WRAPPER}} .woocommerce-mini-cart__buttons > a:nth-child(2),
							 .sc_layouts_cart_panel_style_{{ID}} .woocommerce-mini-cart__buttons > a:nth-child(2)' => 'border-style: {{VALUE}};',
						],
						'condition' => [
							'type!' => 'button',
						],
					]
				);

				$this->add_responsive_control(
					'buttons_style_checkout_style_border_width',
					[
						'label'      => __( 'Border width', 'trx_addons' ),
						'type'       => \Elementor\Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', 'em', 'rem', '%' ],
						'selectors'  => [
							'{{WRAPPER}} .woocommerce-mini-cart__buttons > a:nth-child(2),
							 .sc_layouts_cart_panel_style_{{ID}} .woocommerce-mini-cart__buttons > a:nth-child(2)' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
						],
						'condition' => [
							'type!' => 'button',
							'buttons_style_checkout_style_border_type!' => 'none',
						],
					]
				);

				$this->add_responsive_control(
					'buttons_style_checkout_border_radius',
					[
						'label'      => __( 'Border Radius', 'trx_addons' ),
						'type'       => \Elementor\Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
						'selectors'  => [
							'{{WRAPPER}} .woocommerce-mini-cart__buttons > a:nth-child(2),
							 .sc_layouts_cart_panel_style_{{ID}} .woocommerce-mini-cart__buttons > a:nth-child(2)' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
						'condition'  => [
							'type!' => 'button',
						],
					]
				);

				$this->start_controls_tabs( 'tabs_buttons_style_checkout' );

				$this->start_controls_tab(
					'tab_buttons_style_checkout_normal',
					[
						'label'     => __( 'Normal', 'trx_addons' ),
						'condition' => [
							'type!' => 'button',
						],
					]
				);

				$this->add_control(
					'buttons_style_checkout_bg_color_normal',
					[
						'label'     => __( 'Background Color', 'trx_addons' ),
						'type'      => \Elementor\Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .woocommerce-mini-cart__buttons > a:nth-child(2),
							 .sc_layouts_cart_panel_style_{{ID}} .woocommerce-mini-cart__buttons > a:nth-child(2)' => '--theme-color-text_link: {{VALUE}}; background-color: {{VALUE}};',
						],
						'condition' => [
							'type!' => 'button',
						],
					]
				);

				$this->add_control(
					'buttons_style_checkout_text_color_normal',
					[
						'label'     => __( 'Text Color', 'trx_addons' ),
						'type'      => \Elementor\Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .woocommerce-mini-cart__buttons > a:nth-child(2),
							 .sc_layouts_cart_panel_style_{{ID}} .woocommerce-mini-cart__buttons > a:nth-child(2)' => 'color: {{VALUE}};',
						],
						'condition' => [
							'type!' => 'button',
						],
					]
				);

				$this->add_control(
					'buttons_style_checkout_style_border_color',
					[
						'label'     => __( 'Border Color', 'trx_addons' ),
						'type'      => \Elementor\Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .woocommerce-mini-cart__buttons > a:nth-child(2),
							 .sc_layouts_cart_panel_style_{{ID}} .woocommerce-mini-cart__buttons > a:nth-child(2)' => 'border-color: {{VALUE}};',
						],
						'condition' => [
							'type!' => 'button',
							'buttons_style_checkout_style_border_type!' => 'none',
						],
					]
				);

				$this->end_controls_tab();

				$this->start_controls_tab(
					'tab_buttons_style_checkout_hover',
					[
						'label'     => __( 'Hover', 'trx_addons' ),
						'condition' => [
							'type!' => 'button',
						],
					]
				);

				$this->add_control(
					'buttons_style_checkout_bg_color_hover',
					[
						'label'     => __( 'Background Color', 'trx_addons' ),
						'type'      => \Elementor\Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .woocommerce-mini-cart__buttons.buttons > a:nth-child(2),
							 .sc_layouts_cart_panel_style_{{ID}} .woocommerce-mini-cart__buttons.buttons > a:nth-child(2)' => '--theme-color-text_hover: {{VALUE}};',
							'{{WRAPPER}} .woocommerce-mini-cart__buttons.buttons > a:nth-child(2):hover,
							 .sc_layouts_cart_panel_style_{{ID}} .woocommerce-mini-cart__buttons.buttons > a:nth-child(2):hover' => 'background-color: {{VALUE}};',
						],
						'condition' => [
							'type!' => 'button',
						],
					]
				);

				$this->add_control(
					'buttons_style_checkout_text_color_hover',
					[
						'label'     => __( 'Text Color', 'trx_addons' ),
						'type'      => \Elementor\Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .woocommerce-mini-cart__buttons > a:nth-child(2):hover,
							 .sc_layouts_cart_panel_style_{{ID}} .woocommerce-mini-cart__buttons > a:nth-child(2):hover' => 'color: {{VALUE}}',
						],
						'condition' => [
							'type!' => 'button',
						],
					]
				);

				$this->add_control(
					'buttons_style_checkout_border_color_hover',
					[
						'label'     => __( 'Border Color', 'trx_addons' ),
						'type'      => \Elementor\Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .woocommerce-mini-cart__buttons > a:nth-child(2):hover,
							 .sc_layouts_cart_panel_style_{{ID}} .woocommerce-mini-cart__buttons > a:nth-child(2):hover' => 'border-color: {{VALUE}}',
						],
						'condition' => [
							'type!' => 'button',
							'buttons_style_checkout_style_border_type!' => 'none',
						],
					]
				);

				$this->end_controls_tab();

				$this->end_controls_tabs();

				$this->end_controls_section();
			}

			/**
			 * Style Tab: Overlay
			 */
			protected function register_style_controls_overlay() {

				$this->start_controls_section(
					'section_panel_overlay_style',
					[
						'label' => __( 'Overlay', 'trx_addons' ),
						'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
						'condition' => [
							'type' => 'panel',
						],
					]
				);

				$this->add_control(
					'panel_overlay_bg_color',
					[
						'label'     => __( 'Background Color', 'trx_addons' ),
						'type'      => \Elementor\Controls_Manager::COLOR,
						'selectors' => [
							'.sc_layouts_cart_panel_style_{{ID}}-panel_overlay.sc_layouts_panel_hide_content.sc_layouts_panel_opened,
							 .sc_layouts_cart_panel_style_{{ID}}-panel_overlay.sc_layouts_panel_hide_content.sc_layouts_cart_preview_init' => 'background-color: {{VALUE}}',
						],
						'condition' => [
							'type' => 'panel',
						],
					]
				);

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

				if ( $this->allow_sc_styles_in_elementor() ) {
					return;
				}

				trx_addons_get_template_part(TRX_ADDONS_PLUGIN_CPT_LAYOUTS_SHORTCODES . "cart/tpe.cart.php",
										'trx_addons_args_sc_layouts_cart',
										array('element' => $this)
									);
			}
		}
		
		// Register widget
		trx_addons_elm_register_widget( 'TRX_Addons_Elementor_Widget_Layouts_Cart' );
	}
}
