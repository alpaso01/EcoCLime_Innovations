<?php
/**
 * Pricing Menu Widget
 *
 * @package ThemeREX Addons
 * @since v2.30.0
 */

namespace TrxAddons\ElementorWidgets\Widgets\PricingMenu;

use TrxAddons\ElementorWidgets\BaseWidget;

// Elementor Classes.
use Elementor\Controls_Manager;
use Elementor\Control_Media;
use Elementor\Utils;
use Elementor\Repeater;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Pricing Menu Widget
 */
class PricingMenuWidget extends BaseWidget {

	/**
	 * Register widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 */
	protected function register_controls() {
		/* Content Tab */
		$this->register_content_price_menu_controls();
		$this->register_content_help_docs_controls();

		/* Style Tab */
		$this->register_style_items_controls();
		$this->register_style_content_controls();
		$this->register_style_title_controls();
		$this->register_style_title_separator_controls();
		$this->register_style_price_controls();
		$this->register_style_description_controls();
		$this->register_style_image_controls();
		$this->register_style_title_connector_controls();
	}

	/*-----------------------------------------------------------------------------------*/
	/*	Content Tab
	/*-----------------------------------------------------------------------------------*/

	protected function register_content_price_menu_controls() {

		$this->start_controls_section(
			'section_price_menu',
			array(
				'label' => __( 'Price Menu', 'trx_addons' ),
			)
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'menu_title',
			array(
				'label'       => __( 'Title', 'trx_addons' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array(
					'active' => true,
				),
				'label_block' => true,
				'placeholder' => __( 'Title', 'trx_addons' ),
				'default'     => __( 'Title', 'trx_addons' ),
			)
		);

		$repeater->add_control(
			'menu_description',
			array(
				'label'       => __( 'Description', 'trx_addons' ),
				'type'        => Controls_Manager::TEXTAREA,
				'dynamic'     => array(
					'active' => true,
				),
				'label_block' => true,
				'default'     => __( 'I am item content. Double click here to change this text. Lorem ipsum dolor sit amet, consectetur adipiscing elit.', 'trx_addons' ),
			)
		);

		$repeater->add_control(
			'menu_price',
			array(
				'label'   => __( 'Price', 'trx_addons' ),
				'type'    => Controls_Manager::TEXT,
				'dynamic' => array(
					'active' => true,
				),
				'default' => '$49',
			)
		);

		$repeater->add_control(
			'discount',
			array(
				'label'        => __( 'Discount', 'trx_addons' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'no',
				'label_on'     => __( 'On', 'trx_addons' ),
				'label_off'    => __( 'Off', 'trx_addons' ),
				'return_value' => 'yes',
			)
		);

		$repeater->add_control(
			'original_price',
			array(
				'label'      => __( 'Original Price', 'trx_addons' ),
				'type'       => Controls_Manager::TEXT,
				'dynamic'    => array(
					'active' => true,
				),
				'default'    => '$69',
				'conditions' => array(
					'terms' => array(
						array(
							'name'     => 'discount',
							'operator' => '==',
							'value'    => 'yes',
						),
					),
				),
			)
		);

		$repeater->add_control(
			'image_switch',
			array(
				'label'        => __( 'Show Image', 'trx_addons' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'label_on'     => __( 'On', 'trx_addons' ),
				'label_off'    => __( 'Off', 'trx_addons' ),
				'return_value' => 'yes',
			)
		);

		$repeater->add_control(
			'image',
			array(
				'name'       => 'image',
				'label'      => __( 'Image', 'trx_addons' ),
				'type'       => Controls_Manager::MEDIA,
				'default'    => array(
					'url' => Utils::get_placeholder_image_src(),
				),
				'dynamic'    => array(
					'active' => true,
				),
				'conditions' => array(
					'terms' => array(
						array(
							'name'     => 'image_switch',
							'operator' => '==',
							'value'    => 'yes',
						),
					),
				),
			)
		);

		$repeater->add_control(
			'link',
			array(
				'name'        => 'link',
				'label'       => __( 'Link', 'trx_addons' ),
				'type'        => Controls_Manager::URL,
				'dynamic'     => array(
					'active' => true,
				),
				'placeholder' => 'https://www.your-link.com',
			)
		);

		$this->add_control(
			'menu_items',
			array(
				'label'       => '',
				'type'        => Controls_Manager::REPEATER,
				'default'     => array(
					array(
						'menu_title' => __( 'Menu Item #1', 'trx_addons' ),
						'menu_price' => '$49',
					),
					array(
						'menu_title' => __( 'Menu Item #2', 'trx_addons' ),
						'menu_price' => '$49',
					),
					array(
						'menu_title' => __( 'Menu Item #3', 'trx_addons' ),
						'menu_price' => '$49',
					),
				),
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ menu_title }}}',
			)
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			array(
				'name'      => 'image_size',
				'label'     => __( 'Image Size', 'trx_addons' ),
				'default'   => 'thumbnail',
				'separator' => 'before',
			)
		);

		$this->add_control(
			'menu_style',
			array(
				'label'   => __( 'Menu Style', 'trx_addons' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'style-1',
				'options' => array(
					'style-0' => __( 'Style 0', 'trx_addons' ),
					'style-1' => __( 'Style 1', 'trx_addons' ),
					'style-2' => __( 'Style 2', 'trx_addons' ),
					'style-3' => __( 'Style 3', 'trx_addons' ),
					'style-4' => __( 'Style 4', 'trx_addons' ),
				),
			)
		);

		$this->add_responsive_control(
			'menu_align',
			array(
				'label'     => __( 'Alignment', 'trx_addons' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'left'    => array(
						'title' => __( 'Left', 'trx_addons' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center'  => array(
						'title' => __( 'Center', 'trx_addons' ),
						'icon'  => 'eicon-text-align-center',
					),
					'right'   => array(
						'title' => __( 'Right', 'trx_addons' ),
						'icon'  => 'eicon-text-align-right',
					),
					'justify' => array(
						'title' => __( 'Justified', 'trx_addons' ),
						'icon'  => 'eicon-text-align-justify',
					),
				),
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .trx-addons-restaurant-menu-style-4'   => 'text-align: {{VALUE}};',
				),
				'condition' => array(
					'menu_style' => 'style-4',
				),
			)
		);

		$this->add_control(
			'title_price_connector',
			array(
				'label'        => __( 'Title-Price Connector', 'trx_addons' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'no',
				'label_on'     => __( 'Yes', 'trx_addons' ),
				'label_off'    => __( 'No', 'trx_addons' ),
				'return_value' => 'yes',
				'condition'    => array(
					'menu_style' => 'style-1',
				),
			)
		);

		$this->add_control(
			'title_separator',
			array(
				'label'        => __( 'Title Separator', 'trx_addons' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'no',
				'label_on'     => __( 'Yes', 'trx_addons' ),
				'label_off'    => __( 'No', 'trx_addons' ),
				'return_value' => 'yes',
			)
		);

		$this->end_controls_section();
	}

	/*-----------------------------------------------------------------------------------*/
	/*	Style Tab
	/*-----------------------------------------------------------------------------------*/

	protected function register_style_items_controls() {
		/**
		 * Style Tab: Menu Items
		 */
		$this->start_controls_section(
			'section_items_style',
			[
				'label'                 => __( 'Menu Items', 'trx_addons' ),
				'tab'                   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'items_bg_color',
			[
				'label'                 => __( 'Background Color', 'trx_addons' ),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '',
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-restaurant-menu .trx-addons-restaurant-menu-item' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'items_spacing',
			[
				'label'                 => __( 'Items Spacing', 'trx_addons' ),
				'type'                  => Controls_Manager::SLIDER,
				'range'                 => [
					'%' => [
						'min'   => 0,
						'max'   => 100,
						'step'  => 1,
					],
				],
				'size_units'            => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-restaurant-menu-item-wrap + .trx-addons-restaurant-menu-item-wrap' => 'margin-top: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'items_padding',
			[
				'label'                 => __( 'Padding', 'trx_addons' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-restaurant-menu .trx-addons-restaurant-menu-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'                  => 'items_border',
				'label'                 => __( 'Border', 'trx_addons' ),
				'placeholder'           => '1px',
				'default'               => '1px',
				'selector'              => '{{WRAPPER}} .trx-addons-restaurant-menu .trx-addons-restaurant-menu-item',
			]
		);

		$this->add_control(
			'border_as_separator',
			[
				'label' => __( 'Border as Separator', 'trx_addons' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'On', 'trx_addons' ),
				'label_off' => __( 'Off', 'trx_addons' ),
				'prefix_class' => 'trx-addons-restaurant-menu-border-as-',
				'return_value' => 'separator',
				'default' => '',
			]
		);

		$this->add_responsive_control(
			'items_border_radius',
			[
				'label'                 => __( 'Border Radius', 'trx_addons' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-restaurant-menu .trx-addons-restaurant-menu-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'             => [
					'border_as_separator!' => 'separator',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'                  => 'pricing_table_shadow',
				'selector'              => '{{WRAPPER}} .trx-addons-restaurant-menu-item',
				'separator'             => 'before',
			]
		);

		$this->end_controls_section();
	}

	protected function register_style_content_controls() {
		/**
		 * Style Tab: Content
		 */
		$this->start_controls_section(
			'section_content_style',
			[
				'label'                 => __( 'Content', 'trx_addons' ),
				'tab'                   => Controls_Manager::TAB_STYLE,
				'condition'             => [
					'menu_style' => 'style-0',
				],
			]
		);

		$this->add_responsive_control(
			'content_padding',
			[
				'label'                 => __( 'Padding', 'trx_addons' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-restaurant-menu-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'             => [
					'menu_style' => 'style-0',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function register_style_title_controls() {
		/**
		 * Style Tab: Title Section
		 */
		$this->start_controls_section(
			'section_title_style',
			[
				'label'                 => __( 'Title', 'trx_addons' ),
				'tab'                   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'title_html_tag',
			array(
				'label'   => __( 'HTML Tag', 'trx_addons' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'h4',
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

		$this->add_control(
			'title_color',
			[
				'label'                 => __( 'Color', 'trx_addons' ),
				'type'                  => Controls_Manager::COLOR,
				'global'                => [
					'default' => Global_Colors::COLOR_PRIMARY,
				],
				'default'               => '',
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-restaurant-menu .trx-addons-restaurant-menu-title' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'                  => 'title_typography',
				'label'                 => __( 'Typography', 'trx_addons' ),
				'global'                => [
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				],
				'selector'              => '{{WRAPPER}} .trx-addons-restaurant-menu .trx-addons-restaurant-menu-title',
			]
		);

		$this->add_responsive_control(
			'title_margin',
			[
				'label'                 => __( 'Margin Bottom', 'trx_addons' ),
				'type'                  => Controls_Manager::SLIDER,
				'range'                 => [
					'%' => [
						'min'   => 0,
						'max'   => 40,
						'step'  => 1,
					],
				],
				'size_units'            => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-restaurant-menu .trx-addons-restaurant-menu-header' => 'margin-bottom: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function register_style_title_separator_controls() {
		/**
		 * Style Tab: Title Separator
		 */
		$this->start_controls_section(
			'section_title_separator_style',
			[
				'label'                 => __( 'Title Separator', 'trx_addons' ),
				'tab'                   => Controls_Manager::TAB_STYLE,
				'condition'             => [
					'title_separator' => 'yes',
				],
			]
		);

		$this->add_control(
			'divider_title_border_type',
			[
				'label'                 => __( 'Border Type', 'trx_addons' ),
				'type'                  => Controls_Manager::SELECT,
				'default'               => 'dotted',
				'options'               => [
					'none'      => __( 'None', 'trx_addons' ),
					'solid'     => __( 'Solid', 'trx_addons' ),
					'double'    => __( 'Double', 'trx_addons' ),
					'dotted'    => __( 'Dotted', 'trx_addons' ),
					'dashed'    => __( 'Dashed', 'trx_addons' ),
				],
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-restaurant-menu .trx-addons-price-menu-divider' => 'border-bottom-style: {{VALUE}}',
				],
				'condition'             => [
					'title_separator' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'divider_title_border_weight',
			[
				'label'                 => __( 'Border Height', 'trx_addons' ),
				'type'                  => Controls_Manager::SLIDER,
				'default'               => [
					'size'      => 1,
				],
				'range'                 => [
					'px' => [
						'min'   => 1,
						'max'   => 20,
						'step'  => 1,
					],
				],
				'size_units'            => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-restaurant-menu .trx-addons-price-menu-divider' => 'border-bottom-width: {{SIZE}}{{UNIT}}',
				],
				'condition'             => [
					'title_separator' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'divider_title_border_width',
			[
				'label'                 => __( 'Border Width', 'trx_addons' ),
				'type'                  => Controls_Manager::SLIDER,
				'default'               => [
					'size'      => 100,
					'unit'      => '%',
				],
				'size_units'            => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-restaurant-menu .trx-addons-price-menu-divider' => 'width: {{SIZE}}{{UNIT}}',
				],
				'condition'             => [
					'title_separator' => 'yes',
				],
			]
		);

		$this->add_control(
			'divider_title_border_color',
			[
				'label'                 => __( 'Border Color', 'trx_addons' ),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '',
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-restaurant-menu .trx-addons-price-menu-divider' => 'border-bottom-color: {{VALUE}}',
				],
				'condition'             => [
					'title_separator' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'divider_title_spacing',
			[
				'label'                 => __( 'Margin Bottom', 'trx_addons' ),
				'type'                  => Controls_Manager::SLIDER,
				'range'                 => [
					'%' => [
						'min'   => 0,
						'max'   => 100,
						'step'  => 1,
					],
				],
				'size_units'            => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-restaurant-menu .trx-addons-price-menu-divider' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function register_style_price_controls() {

		$this->start_controls_section(
			'section_price_style',
			[
				'label'                 => __( 'Price', 'trx_addons' ),
				'tab'                   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'price_badge_heading',
			[
				'label'                 => __( 'Price Badge', 'trx_addons' ),
				'type'                  => Controls_Manager::HEADING,
				'separator'             => 'before',
				'condition'             => [
					'menu_style' => 'style-0',
				],
			]
		);

		$this->add_control(
			'badge_text_color',
			[
				'label'                 => __( 'Text Color', 'trx_addons' ),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '',
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-restaurant-menu-style-0 .trx-addons-restaurant-menu-price' => 'color: {{VALUE}}',
				],
				'condition'             => [
					'menu_style' => 'style-0',
				],
			]
		);

		$this->add_control(
			'badge_bg_color',
			[
				'label'                 => __( 'Background Color', 'trx_addons' ),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '',
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-restaurant-menu-style-0 .trx-addons-restaurant-menu-price:after' => 'border-right-color: {{VALUE}}',
				],
				'condition'             => [
					'menu_style' => 'style-0',
				],
			]
		);

		$this->add_control(
			'price_html_tag',
			array(
				'label'   => __( 'HTML Tag', 'trx_addons' ),
				'type'    => Controls_Manager::SELECT,
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

		$this->add_control(
			'price_color',
			[
				'label'                 => __( 'Color', 'trx_addons' ),
				'type'                  => Controls_Manager::COLOR,
				'global'                => [
					'default' => Global_Colors::COLOR_PRIMARY,
				],
				'default'               => '',
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-restaurant-menu .trx-addons-restaurant-menu-price-discount' => 'color: {{VALUE}}',
				],
				'condition'             => [
					'menu_style!' => 'style-0',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'                  => 'price_typography',
				'label'                 => __( 'Typography', 'trx_addons' ),
				'global'                => [
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				],
				'selector'              => '{{WRAPPER}} .trx-addons-restaurant-menu .trx-addons-restaurant-menu-price-discount',
			]
		);

		$this->add_responsive_control(
			'price_margin',
			[
				'label'                 => __( 'Margin', 'trx_addons' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-restaurant-menu .trx-addons-restaurant-menu-price' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'original_price_heading',
			[
				'label'                 => __( 'Original Price', 'trx_addons' ),
				'type'                  => Controls_Manager::HEADING,
				'separator'             => 'before',
			]
		);

		$this->add_control(
			'original_price_strike',
			[
				'label'                 => __( 'Strikethrough', 'trx_addons' ),
				'type'                  => Controls_Manager::SWITCHER,
				'default'               => 'yes',
				'label_on'              => __( 'On', 'trx_addons' ),
				'label_off'             => __( 'Off', 'trx_addons' ),
				'return_value'          => 'yes',
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-restaurant-menu .trx-addons-restaurant-menu-price-original' => 'text-decoration: line-through;',
				],
			]
		);

		$this->add_control(
			'original_price_color',
			[
				'label'                 => __( 'Original Price Color', 'trx_addons' ),
				'type'                  => Controls_Manager::COLOR,
				'global'                => [
					'default' => Global_Colors::COLOR_TEXT,
				],
				'default'               => '',
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-restaurant-menu .trx-addons-restaurant-menu-price-original' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'                  => 'original_price_typography',
				'label'                 => __( 'Original Price Typography', 'trx_addons' ),
				'global'                => [
					'default' => Global_Typography::TYPOGRAPHY_ACCENT,
				],
				'selector'              => '{{WRAPPER}} .trx-addons-restaurant-menu .trx-addons-restaurant-menu-price-original',
			]
		);

		$this->add_responsive_control(
			'original_price_margin',
			[
				'label'                 => __( 'Margin', 'trx_addons' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-restaurant-menu .trx-addons-restaurant-menu-price-original' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function register_style_description_controls() {

		$this->start_controls_section(
			'section_description_style',
			[
				'label'                 => __( 'Description', 'trx_addons' ),
				'tab'                   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'description_color',
			[
				'label'                 => __( 'Color', 'trx_addons' ),
				'type'                  => Controls_Manager::COLOR,
				'global'                => [
					'default' => Global_Colors::COLOR_TEXT,
				],
				'default'               => '',
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-restaurant-menu-description' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'                  => 'description_typography',
				'label'                 => __( 'Typography', 'trx_addons' ),
				'global'                => [
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				],
				'selector'              => '{{WRAPPER}} .trx-addons-restaurant-menu-description',
			]
		);

		$this->add_responsive_control(
			'description_spacing',
			[
				'label'                 => __( 'Margin Bottom', 'trx_addons' ),
				'type'                  => Controls_Manager::SLIDER,
				'range'                 => [
					'%' => [
						'min'   => 0,
						'max'   => 100,
						'step'  => 1,
					],
				],
				'size_units'            => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-restaurant-menu-description' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function register_style_image_controls() {
		/**
		 * Style Tab: Image Section
		 */
		$this->start_controls_section(
			'section_image_style',
			[
				'label'                 => __( 'Image', 'trx_addons' ),
				'tab'                   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'image_bg_color',
			[
				'label'                 => __( 'Background Color', 'trx_addons' ),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '',
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-restaurant-menu-image img' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'image_width',
			[
				'label'                 => __( 'Width', 'trx_addons' ),
				'type'                  => Controls_Manager::SLIDER,
				'range'                 => [
					'px' => [
						'min'   => 20,
						'max'   => 300,
						'step'  => 1,
					],
					'%' => [
						'min'   => 5,
						'max'   => 50,
						'step'  => 1,
					],
				],
				'size_units'            => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'default'               => [
					'unit' => 'px',
					'size' => 150,
				],
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-restaurant-menu-image img' => 'min-width: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_responsive_control(
			'image_margin',
			[
				'label'                 => __( 'Margin', 'trx_addons' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-restaurant-menu-image' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'image_padding',
			[
				'label'                 => __( 'Padding', 'trx_addons' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-restaurant-menu-image img' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'                  => 'image_border',
				'label'                 => __( 'Border', 'trx_addons' ),
				'placeholder'           => '1px',
				'default'               => '1px',
				'selector'              => '{{WRAPPER}} .trx-addons-restaurant-menu-image img',
			]
		);

		$this->add_responsive_control(
			'image_border_radius',
			[
				'label'                 => __( 'Border Radius', 'trx_addons' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-restaurant-menu-image img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'image_vertical_position',
			[
				'label'                 => __( 'Vertical Position', 'trx_addons' ),
				'type'                  => Controls_Manager::CHOOSE,
				'label_block'           => false,
				'options'               => [
					'top'       => [
						'title' => __( 'Top', 'trx_addons' ),
						'icon'  => 'eicon-v-align-top',
					],
					'middle'    => [
						'title' => __( 'Middle', 'trx_addons' ),
						'icon'  => 'eicon-v-align-middle',
					],
					'bottom'    => [
						'title' => __( 'Bottom', 'trx_addons' ),
						'icon'  => 'eicon-v-align-bottom',
					],
				],
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-restaurant-menu .trx-addons-restaurant-menu-image' => 'align-self: {{VALUE}}',
				],
				'selectors_dictionary'  => [
					'top'      => 'flex-start',
					'middle'   => 'center',
					'bottom'   => 'flex-end',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function register_style_title_connector_controls() {
		/**
		 * Style Tab: Items Divider Section
		 */
		$this->start_controls_section(
			'section_table_title_connector_style',
			[
				'label'                 => __( 'Title-Price Connector', 'trx_addons' ),
				'tab'                   => Controls_Manager::TAB_STYLE,
				'condition'             => [
					'title_price_connector' => 'yes',
					'menu_style' => 'style-1',
				],
			]
		);

		$this->add_control(
			'title_connector_vertical_align',
			[
				'label'                 => __( 'Vertical Alignment', 'trx_addons' ),
				'type'                  => Controls_Manager::CHOOSE,
				'default'               => 'middle',
				'options'               => [
					'top'          => [
						'title'    => __( 'Top', 'trx_addons' ),
						'icon'     => 'eicon-v-align-top',
					],
					'middle'       => [
						'title'    => __( 'Center', 'trx_addons' ),
						'icon'     => 'eicon-v-align-middle',
					],
					'bottom'       => [
						'title'    => __( 'Bottom', 'trx_addons' ),
						'icon'     => 'eicon-v-align-bottom',
					],
				],
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-restaurant-menu-style-1 .trx-addons-price-title-connector'   => 'align-self: {{VALUE}};',
				],
				'selectors_dictionary'  => [
					'top'          => 'flex-start',
					'middle'       => 'center',
					'bottom'       => 'flex-end',
				],
				'condition'             => [
					'title_price_connector' => 'yes',
					'menu_style' => 'style-1',
				],
			]
		);

		$this->add_responsive_control(
			'title_connector_vertical_offset',
			array(
				'label'     => __( 'Vertical Offset', 'trx_addons' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => array(
					'size' => 0,
				),
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'range'     => [
					'px' => [
						'min' => -50,
						'max' => 50,
					],
					'em' => [
						'min' => -2,
						'max' => 2,
						'step' => 0.1
					],
				],
				'selectors' => array(
					'{{WRAPPER}} .trx-addons-restaurant-menu-style-1 .trx-addons-price-title-connector' => 'top: {{SIZE}}{{UNIT}};',
				),
				'condition'             => [
					'title_price_connector' => 'yes',
					'menu_style' => 'style-1',
				],
			)
		);

		$this->add_control(
			'items_divider_style',
			[
				'label'                 => __( 'Style', 'trx_addons' ),
				'type'                  => Controls_Manager::SELECT,
				'default'               => 'dashed',
				'options'              => [
					'solid'     => __( 'Solid', 'trx_addons' ),
					'dashed'    => __( 'Dashed', 'trx_addons' ),
					'dotted'    => __( 'Dotted', 'trx_addons' ),
					'double'    => __( 'Double', 'trx_addons' ),
				],
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-restaurant-menu-style-1 .trx-addons-price-title-connector' => 'border-bottom-style: {{VALUE}}',
				],
				'condition'             => [
					'title_price_connector' => 'yes',
					'menu_style' => 'style-1',
				],
			]
		);

		$this->add_control(
			'items_divider_color',
			[
				'label'                 => __( 'Color', 'trx_addons' ),
				'type'                  => Controls_Manager::COLOR,
				'global'                => [
					'default' => Global_Colors::COLOR_SECONDARY,
				],
				'default'               => '',
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-restaurant-menu-style-1 .trx-addons-price-title-connector' => 'border-bottom-color: {{VALUE}}',
				],
				'condition'             => [
					'title_price_connector' => 'yes',
					'menu_style' => 'style-1',
				],
			]
		);

		$this->add_responsive_control(
			'items_divider_weight',
			[
				'label'                 => __( 'Divider Weight', 'trx_addons' ),
				'type'                  => Controls_Manager::SLIDER,
				'default'               => [ 'size' => '1' ],
				'range'                 => [
					'px' => [
						'min'   => 0,
						'max'   => 30,
						'step'  => 1,
					],
				],
				'size_units'            => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-restaurant-menu-style-1 .trx-addons-price-title-connector' => 'border-bottom-width: {{SIZE}}{{UNIT}}; bottom: calc((-{{SIZE}}{{UNIT}})/2)',
				],
				'condition'             => [
					'title_price_connector' => 'yes',
					'menu_style' => 'style-1',
				],
			]
		);

		$this->end_controls_section();
	}


	/*-----------------------------------------------------------------------------------*/
	/*	RENDER
	/*-----------------------------------------------------------------------------------*/

	/**
	 * Render a widget output on the frontend.
	 *
	 * @access protected
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();
		$i = 1;
		$this->add_render_attribute( 'price-menu', 'class', 'trx-addons-restaurant-menu' );

		if ( $settings['menu_style'] ) {
			$this->add_render_attribute( 'price-menu', 'class', 'trx-addons-restaurant-menu-' . $settings['menu_style'] );
		}
		?>
		<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'price-menu' ) ); ?>>
			<div class="trx-addons-restaurant-menu-items">
				<?php foreach ( $settings['menu_items'] as $index => $item ) : ?>
					<?php
						$title_key = $this->get_repeater_setting_key( 'menu_title', 'menu_items', $index );
						$this->add_render_attribute( $title_key, 'class', 'trx-addons-restaurant-menu-title-text' );
						$this->add_inline_editing_attributes( $title_key, 'none' );

						$description_key = $this->get_repeater_setting_key( 'menu_description', 'menu_items', $index );
						$this->add_render_attribute( $description_key, 'class', 'trx-addons-restaurant-menu-description' );
						$this->add_inline_editing_attributes( $description_key, 'basic' );

						$discount_price_key = $this->get_repeater_setting_key( 'menu_price', 'menu_items', $index );
						$this->add_render_attribute( $discount_price_key, 'class', 'trx-addons-restaurant-menu-price-discount' );
						$this->add_inline_editing_attributes( $discount_price_key, 'none' );

						$original_price_key = $this->get_repeater_setting_key( 'original_price', 'menu_items', $index );
						$this->add_render_attribute( $original_price_key, 'class', 'trx-addons-restaurant-menu-price-original' );
						$this->add_inline_editing_attributes( $original_price_key, 'none' );
					?>
					<div class="trx-addons-restaurant-menu-item-wrap">
						<div class="trx-addons-restaurant-menu-item">
							<?php if ( 'yes' === $item['image_switch'] ) { ?>
								<div class="trx-addons-restaurant-menu-image">
									<?php
									if ( ! empty( $item['image']['url'] ) ) :
										$image = $item['image'];
										$image_url = Group_Control_Image_Size::get_attachment_image_src( $image['id'], 'image_size', $settings );

										if ( $image_url ) {
											echo '<img src="' . esc_url( $image_url ) . '" alt="' . esc_attr( Control_Media::get_image_alt( $item['image'] ) ) . '">';
										} else {
											echo '<img src="' . esc_url( $item['image']['url'] ) . '">';
										}
										?>
									<?php endif; ?>
								</div>
							<?php } ?>

							<div class="trx-addons-restaurant-menu-content">
								<div class="trx-addons-restaurant-menu-header">
									<?php
									if ( ! empty( $item['menu_title'] ) ) {
										$title_tag = $settings['title_html_tag'];
										?>
										<<?php echo esc_html( $title_tag ); ?> class="trx-addons-restaurant-menu-title">
											<?php
											if ( ! empty( $item['link']['url'] ) ) {
												$title_link_key = $this->get_repeater_setting_key( 'menu_title_link', 'menu_items', $index );
												$this->add_link_attributes( $title_link_key, $item['link'] );
												?>
												<a <?php echo wp_kses_post( $this->get_render_attribute_string( $title_link_key ) ); ?>>
													<span <?php echo wp_kses_post( $this->get_render_attribute_string( $title_key ) ); ?>>
														<?php $this->print_unescaped_setting( 'menu_title', 'menu_items', $index ); ?>
													</span>
												</a>
												<?php
											} else {
												?>
												<span <?php echo wp_kses_post( $this->get_render_attribute_string( $title_key ) ); ?>>
													<?php $this->print_unescaped_setting( 'menu_title', 'menu_items', $index ); ?>
												</span>
												<?php
											}
											?>
										</<?php echo esc_html( $title_tag ); ?>>
										<?php
									}

									if ( 'yes' === $settings['title_price_connector'] ) { ?>
										<span class="trx-addons-price-title-connector"></span>
										<?php
									}

									$price_tag = $settings['price_html_tag'];

									if ( 'style-1' === $settings['menu_style'] ) { ?>
										<?php if ( ! empty( $item['menu_price'] ) ) { ?>
											<<?php echo esc_html( $price_tag ); ?> class="trx-addons-restaurant-menu-price">
												<?php if ( 'yes' === $item['discount'] ) { ?>
													<span <?php echo wp_kses_post( $this->get_render_attribute_string( $original_price_key ) ); ?>>
														<?php $this->print_unescaped_setting( 'original_price', 'menu_items', $index ); ?>
													</span>
												<?php } ?>
												<span <?php echo wp_kses_post( $this->get_render_attribute_string( $discount_price_key ) ); ?>>
													<?php $this->print_unescaped_setting( 'menu_price', 'menu_items', $index ); ?>
												</span>
											</<?php echo esc_html( $price_tag ); ?>>
										<?php } ?>
									<?php } ?>
								</div>

								<?php if ( 'yes' === $settings['title_separator'] ) { ?>
									<div class="trx-addons-price-menu-divider-wrap">
										<div class="trx-addons-price-menu-divider"></div>
									</div>
								<?php } ?>

								<?php
								if ( '' !== $item['menu_description'] ) {
									?>
									<div <?php echo wp_kses_post( $this->get_render_attribute_string( $description_key ) ); ?>>
										<?php $this->print_unescaped_setting( 'menu_description', 'menu_items', $index ); ?>
									</div>
									<?php
								}
								?>

								<?php if ( 'style-1' !== $settings['menu_style'] ) { ?>
									<?php if ( '' !== $item['menu_price'] ) { ?>
										<<?php echo esc_html( $price_tag ); ?> class="trx-addons-restaurant-menu-price">
											<?php if ( 'yes' === $item['discount'] ) { ?>
												<span <?php echo wp_kses_post( $this->get_render_attribute_string( $original_price_key ) ); ?>>
													<?php $this->print_unescaped_setting( 'original_price', 'menu_items', $index ); ?>
												</span>
											<?php } ?>
											<span <?php echo wp_kses_post( $this->get_render_attribute_string( $discount_price_key ) ); ?>>
												<?php $this->print_unescaped_setting( 'menu_price', 'menu_items', $index ); ?>
											</span>
										</<?php echo esc_html( $price_tag ); ?>>
									<?php } ?>
								<?php } ?>
							</div>
						</div>
					</div>
					<?php $i++;
				endforeach; ?>
			</div>
		</div>
		<?php
	}

	/**
	 * Render a widget output in the editor.
	 *
	 * Written as a Backbone JavaScript template and used to generate the live preview.
	 *
	 * @access protected
	 */
	protected function content_template() {
		?>
		<#
			var $i = 1;

			function price_template( item ) {
				if ( item.menu_price != '' ) {
					var price_tag = elementor.helpers.validateHTMLTag( settings.price_html_tag );
					#>
					<{{ price_tag }} class="trx-addons-restaurant-menu-price">
						<#
							if ( item.discount == 'yes' ) {
								var original_price = item.original_price;

								view.addRenderAttribute( 'menu_items.' + ($i - 1) + '.original_price', 'class', 'trx-addons-restaurant-menu-price-original' );

								view.addInlineEditingAttributes( 'menu_items.' + ($i - 1) + '.original_price' );

								var original_price_html = '<span' + ' ' + view.getRenderAttributeString( 'menu_items.' + ($i - 1) + '.original_price' ) + '>' + original_price + '</span>';

								print( original_price_html );
							}

							var menu_price = item.menu_price;

							view.addRenderAttribute( 'menu_items.' + ($i - 1) + '.menu_price', 'class', 'trx-addons-restaurant-menu-price-discount' );

							view.addInlineEditingAttributes( 'menu_items.' + ($i - 1) + '.menu_price' );

							var menu_price_html = '<span' + ' ' + view.getRenderAttributeString( 'menu_items.' + ($i - 1) + '.menu_price' ) + '>' + menu_price + '</span>';

							print( menu_price_html );
						#>
					</{{ price_tag }}>
					<#
				}
			}

			function title_template( item ) {
				var title = item.menu_title;

				view.addRenderAttribute( 'menu_items.' + ($i - 1) + '.menu_title', 'class', 'trx-addons-restaurant-menu-title-text' );

				view.addInlineEditingAttributes( 'menu_items.' + ($i - 1) + '.menu_title' );

				var title_html = '<div' + ' ' + view.getRenderAttributeString( 'menu_items.' + ($i - 1) + '.menu_title' ) + '>' + title + '</div>';

				print( title_html );
			}
		#>
		<div class="trx-addons-restaurant-menu trx-addons-restaurant-menu-{{ settings.menu_style }}">
			<div class="trx-addons-restaurant-menu-items">
				<# _.each( settings.menu_items, function( item ) { #>
					<div class="trx-addons-restaurant-menu-item-wrap">
						<div class="trx-addons-restaurant-menu-item">
							<# if ( item.image_switch == 'yes' ) { #>
								<div class="trx-addons-restaurant-menu-image">
									<# if ( item.image.url != '' ) { #>
										<#
										var image = {
											id: item.image.id,
											url: item.image.url,
											size: settings.image_size_size,
											dimension: settings.image_size_custom_dimension,
											model: view.getEditModel()
										};
										var image_url = elementor.imagesManager.getImageUrl( image );
										#>
										<img src="{{ _.escape( image_url ) }}" />
									<# } #>
								</div>
							<# } #>

							<div class="trx-addons-restaurant-menu-content">
								<div class="trx-addons-restaurant-menu-header">
									<# if ( item.menu_title != '' ) { #>
										<# var titleHTMLTag = elementor.helpers.validateHTMLTag( settings.title_html_tag ); #>
										<{{{ titleHTMLTag }}} class="trx-addons-restaurant-menu-title">
											<# if ( item.link && item.link.url ) { #>
												<a href="{{ _.escape( item.link.url ) }}">
													<# title_template( item ) #>
												</a>
											<# } else { #>
												<# title_template( item ) #>
											<# } #>
										</{{{ titleHTMLTag }}}>
									<# }

									if ( settings.title_price_connector == 'yes' ) { #>
										<span class="trx-addons-price-title-connector"></span>
									<# }

									if ( settings.menu_style == 'style-1' ) {
										price_template( item );
									} #>
								</div>

								<# if ( settings.title_separator == 'yes' ) { #>
									<div class="trx-addons-price-menu-divider-wrap">
										<div class="trx-addons-price-menu-divider"></div>
									</div>
								<# }

								if ( item.menu_description != '' ) {
									var description = item.menu_description;

									view.addRenderAttribute( 'menu_items.' + ($i - 1) + '.menu_description', 'class', 'trx-addons-restaurant-menu-description' );

									view.addInlineEditingAttributes( 'menu_items.' + ($i - 1) + '.menu_description' );

									var description_html = '<div' + ' ' + view.getRenderAttributeString( 'menu_items.' + ($i - 1) + '.menu_description' ) + '>' + description + '</div>';

									print( description_html );
								}

								if ( settings.menu_style != 'style-1' ) {
									price_template( item );
								} #>
							</div>
						</div>
					</div>
				<# $i++; } ); #>
			</div>
		</div>
		<?php
	}
}
