<?php
/**
 * Advanced Title Widget
 *
 * @package ThemeREX Addons
 * @since v2.30.0
 */

namespace TrxAddons\ElementorWidgets\Widgets\AdvancedTitle;

use TrxAddons\ElementorWidgets\BaseWidget;
use TrxAddons\ElementorWidgets\Utils as TrxAddonsUtils;

// Elementor Classes.
use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Utils;
use Elementor\Icons_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Text_Stroke;
use Elementor\Group_Control_Text_Shadow;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Advanced Title Widget
 */
class AdvancedTitleWidget extends BaseWidget {

	/**
	 * Register widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 */
	protected function register_controls() {
		/* Content Tab */
		$this->register_content_controls_general();
		$this->register_content_help_docs_controls();

		/* Style Tab */
		$this->register_style_controls_general();
		$this->register_style_controls_highlight();
	}

	/**
	 * Register General Controls in Content tab
	 *
	 * @return void
	 */
	protected function register_content_controls_general() {
		/**
		 * Content Tab: Advanced Title
		 * -------------------------------------------------
		 */
		$this->start_controls_section(
			'section_advanced_title',
			array(
				'label' => __( 'Advanced Title', 'trx_addons' ),
			)
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'type',
			[
				'label' => esc_html__( 'Type', 'trx_addons' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'text',
				'options' => [
					'text' => esc_html__( 'Text', 'trx_addons' ),
					'icon'  => esc_html__( 'Icon', 'trx_addons' ),
					'image' => esc_html__( 'Image', 'trx_addons' ),
				],
				'render_type' => 'template',
			]
		);

		$repeater->add_control(
			'text',
			[
				'label' => __( 'Text', 'trx_addons' ),
				'label_block' => true,
				'type' => Controls_Manager::TEXTAREA,
				'placeholder' => __( 'Enter your text here', 'trx_addons' ),
				'default' => __( 'Enter your text here', 'trx_addons' ),
				'condition' => [
					'type' => 'text'
				],
			]
		);

		$repeater->add_control(
			'icon',
			[
				'label' => esc_html__( 'Icon', 'trx_addons' ),
				'type' => Controls_Manager::ICONS,
				'default' => [
					'value' => 'fas fa-circle',
					'library' => 'fa-solid',
				],
				'recommended' => [
					'fa-solid' => [
						'circle',
						'dot-circle',
						'square-full',
					],
					'fa-regular' => [
						'circle',
						'dot-circle',
						'square-full',
					],
				],
				'condition' => [
					'type' => 'icon'
				],
			]
		);

		$repeater->add_control(
			'image',
			[
				'label' => esc_html__( 'Choose Image', 'trx_addons' ),
				'type' => Controls_Manager::MEDIA,
				'default' => [
					'url' => Utils::get_placeholder_image_src(),
				],
				'condition' => [
					'type' => 'image'
				],
			]
		);

		$repeater->add_control(
			'size',
			[
				'label' => __('Size', 'trx_addons'),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 100,
					'unit' => '%',
				],
				'range' => [
					'%' => [
						'min' => 0,
						'max' => 300,
					],
				],
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}}' => '--trx-addons-base-size: calc( {{SIZE}} / 100 );',
				],
				'condition' => [
					'type' => ['icon', 'image']
				],
			]
		);

		$repeater->add_control(
			'layout',
			[
				'label'        => __( 'Start New Line', 'trx_addons' ),
				'label_block'  => false,
				'type'         => Controls_Manager::SWITCHER,
				'default'      => '',
				'return_value' => 'block',
				'render_type'  => 'template',
			]
		);

		$repeater->add_control(
			'item_link',
			[
				'label' => __( 'Link', 'trx_addons' ),
				'label_block' => false,
				'type' => Controls_Manager::URL,
				'dynamic'     => [
					'active' => true,
				],
				'placeholder' => __('https://your-link.com', 'trx_addons'),
			]
		);

		$repeater->add_control(
			'decoration',
			[
				'label' => __( 'Custom Styling', 'trx_addons' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$repeater->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'item_typography',
				'selector' => '{{WRAPPER}} {{CURRENT_ITEM}}.trx-addons-advanced-title-item-text',
				'condition' => [
					'type' => ['text']
				],
			]
		);

		$repeater->start_controls_tabs( 'accordion_tabs_content_tabs' );

		$repeater->start_controls_tab(
			'advanced_title_tabs_normal_tab',
			[
				'label' => __( 'Normal', 'trx_addons' ),
			]
		);

		$repeater->add_control(
			'color',
			[
				'label' => esc_html__( 'Color', 'trx_addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}}.trx-addons-advanced-title-item-text,
					 {{WRAPPER}} {{CURRENT_ITEM}}.trx-addons-advanced-title-item-icon i,
					 {{WRAPPER}} {{CURRENT_ITEM}}.trx-addons-advanced-title-item-icon svg' => 'color: {{VALUE}}; fill: {{VALUE}}', //icon as a font + svg

				],
				'condition' => [
					'type' => ['icon','text']
				],
			]
		);

		$repeater->add_control(
			'background_as_text',
			[
				'label' => esc_html__( 'Text as Clipping Mask', 'trx_addons' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => false,
				'render_type' => 'template',
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}}.trx-addons-advanced-title-item-text,
					 {{WRAPPER}} {{CURRENT_ITEM}}.trx-addons-advanced-title-item-icon i,
					 {{WRAPPER}} {{CURRENT_ITEM}}.trx-addons-advanced-title-item-icon svg' => '-webkit-background-clip: text;background-clip: text;-webkit-text-fill-color: transparent;',
				],
				'condition' => [
					'type' => ['icon','text'],
					'background_background' => ['classic','gradient']
				],
			],
		);

		$repeater->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'background',
				'selector' => '{{WRAPPER}} {{CURRENT_ITEM}}.trx-addons-advanced-title-item-text,
								{{WRAPPER}} {{CURRENT_ITEM}}.trx-addons-advanced-title-item-icon i,
								{{WRAPPER}} {{CURRENT_ITEM}}.trx-addons-advanced-title-item-icon svg,
								{{WRAPPER}} {{CURRENT_ITEM}}.trx-addons-advanced-title-item-image img',
			]
		);

		$repeater->add_control(
			'gradient',
			[
				'label' => esc_html__( 'Advanced Gradient', 'trx_addons' ),
				'label_block' => true,
				'type' => 'trx_gradient',
				'default' => '',
				'render_type' => 'template',
				'picker_options' => [
					'modes' => ['linear-gradient', 'radial-gradient'],
				],
				'condition' => [
					'background_background' => ['gradient']
				],
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}}.trx-addons-advanced-title-item-text,
					 {{WRAPPER}} {{CURRENT_ITEM}}.trx-addons-advanced-title-item-icon i,
					 {{WRAPPER}} {{CURRENT_ITEM}}.trx-addons-advanced-title-item-icon svg,
					 {{WRAPPER}} {{CURRENT_ITEM}}.trx-addons-advanced-title-item-image img' => 'background-image: {{VALUE}};',
				],
			]
		);

		$repeater->add_control(
			'animate_gradient',
			[
				'label' => esc_html__( 'Animate Gradient', 'trx_addons' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => false,
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}}.trx-addons-advanced-title-item-text,
					 {{WRAPPER}} {{CURRENT_ITEM}}.trx-addons-advanced-title-item-icon i,
					 {{WRAPPER}} {{CURRENT_ITEM}}.trx-addons-advanced-title-item-icon svg' => 'background-size: 200% 100%; animation: trx-addons-advanced-title-animate-gradient 5s ease infinite;',
				],
				'condition' => [
					'type' => ['icon','text'],
					'background_background' => ['gradient']
				],
			]
		);

		$repeater->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'        => 'border',
				'label'       => __( 'Border', 'trx_addons' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'separator' => 'before',
				'selector'    => '{{WRAPPER}} {{CURRENT_ITEM}}.trx-addons-advanced-title-item-text,
								  {{WRAPPER}} {{CURRENT_ITEM}}.trx-addons-advanced-title-item-icon i,
								  {{WRAPPER}} {{CURRENT_ITEM}}.trx-addons-advanced-title-item-icon svg,
								  {{WRAPPER}} {{CURRENT_ITEM}}.trx-addons-advanced-title-item-image img',
			)
		);

		$repeater->add_responsive_control(
			'border_radius',
			array(
				'label'      => __( 'Border Radius', 'trx_addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors'  => array(
								'{{WRAPPER}} {{CURRENT_ITEM}}.trx-addons-advanced-title-item-text,
								 {{WRAPPER}} {{CURRENT_ITEM}}.trx-addons-advanced-title-item-icon i,
								 {{WRAPPER}} {{CURRENT_ITEM}}.trx-addons-advanced-title-item-icon svg,
								 {{WRAPPER}} {{CURRENT_ITEM}}.trx-addons-advanced-title-item-image img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$repeater->add_group_control(
			Group_Control_Text_Stroke::get_type(),
			[
				'name'      => 'text_stroke',
				'selector'  => '{{WRAPPER}} {{CURRENT_ITEM}}.trx-addons-advanced-title-item-text,
								{{WRAPPER}} {{CURRENT_ITEM}}.trx-addons-advanced-title-item-icon i,
								{{WRAPPER}} {{CURRENT_ITEM}}.trx-addons-advanced-title-item-icon svg',
				'condition' => array(
					'type' => array( 'text', 'icon' ),
				),
			]
		);

		$repeater->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name'      => 'title_text_shadow',
				'label'     => __( 'Text Shadow', 'trx_addons' ),
				'selector'  => '{{WRAPPER}} {{CURRENT_ITEM}}.trx-addons-advanced-title-item-text,
								{{WRAPPER}} {{CURRENT_ITEM}}.trx-addons-advanced-title-item-icon i,
								{{WRAPPER}} {{CURRENT_ITEM}}.trx-addons-advanced-title-item-icon svg',
				'condition' => array(
					'type' => array( 'text', 'icon' ),
				),
			)
		);

		$repeater->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'box_shadow',
				'selector' => '{{WRAPPER}} {{CURRENT_ITEM}}.trx-addons-advanced-title-item-text,
								{{WRAPPER}} {{CURRENT_ITEM}}.trx-addons-advanced-title-item-icon i,
								{{WRAPPER}} {{CURRENT_ITEM}}.trx-addons-advanced-title-item-icon svg,
								{{WRAPPER}} {{CURRENT_ITEM}}.trx-addons-advanced-title-item-image img',
			]
		);

		$repeater->end_controls_tab();

		$repeater->start_controls_tab(
			'advanced_title_tabs_hover_tab',
			[
				'label' => __( 'Hover', 'trx_addons' ),
			]
		);

		$repeater->add_control(
			'color_hover',
			[
				'label' => esc_html__( 'Text Color', 'trx_addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}}.trx-addons-advanced-title-item-text:hover,
					{{WRAPPER}} {{CURRENT_ITEM}}.trx-addons-advanced-title-item-icon:hover i,
					{{WRAPPER}} {{CURRENT_ITEM}}.trx-addons-advanced-title-item-icon:hover svg' => 'color: {{VALUE}}; fill: {{VALUE}}', //icon as a font + svg

				],
				'condition' => [
					'type' => ['icon','text']
				],
			]
		);

		$repeater->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'background_hover',
				'selector' => '{{WRAPPER}} {{CURRENT_ITEM}}.trx-addons-advanced-title-item-text:hover,
								{{WRAPPER}} {{CURRENT_ITEM}}.trx-addons-advanced-title-item-icon:hover i,
								{{WRAPPER}} {{CURRENT_ITEM}}.trx-addons-advanced-title-item-icon:hover svg,
								{{WRAPPER}} {{CURRENT_ITEM}}.trx-addons-advanced-title-item-image:hover img',
			]
		);

		$repeater->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'        => 'border_hover',
				'label'       => __( 'Border', 'trx_addons' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} {{CURRENT_ITEM}}.trx-addons-advanced-title-item-text:hover,
								{{WRAPPER}} {{CURRENT_ITEM}}.trx-addons-advanced-title-item-icon:hover i,
								{{WRAPPER}} {{CURRENT_ITEM}}.trx-addons-advanced-title-item-icon:hover svg,
								{{WRAPPER}} {{CURRENT_ITEM}}.trx-addons-advanced-title-item-image:hover img',
			)
		);

		$repeater->add_group_control(
			Group_Control_Text_Stroke::get_type(),
			[
				'name'      => 'text_stroke_hover',
				'selector'  => '{{WRAPPER}} {{CURRENT_ITEM}}.trx-addons-advanced-title-item-text:hover,
								{{WRAPPER}} {{CURRENT_ITEM}}.trx-addons-advanced-title-item-icon:hover i,
								{{WRAPPER}} {{CURRENT_ITEM}}.trx-addons-advanced-title-item-icon:hover svg',
				'condition' => array(
					'type' => array( 'text', 'icon' ),
				),
			]
		);

		$repeater->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name'      => 'title_text_shadow_hover',
				'label'     => __( 'Text Shadow', 'trx_addons' ),
				'selector'  => '{{WRAPPER}} {{CURRENT_ITEM}}.trx-addons-advanced-title-item-text:hover,
								{{WRAPPER}} {{CURRENT_ITEM}}.trx-addons-advanced-title-item-icon:hover i,
								{{WRAPPER}} {{CURRENT_ITEM}}.trx-addons-advanced-title-item-icon:hover svg',
				'condition' => array(
					'type' => array( 'text', 'icon' ),
				),
			)
		);

		$repeater->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'box_shadow_hover',
				'selector' => '{{WRAPPER}} {{CURRENT_ITEM}}.trx-addons-advanced-title-item-text:hover,
								{{WRAPPER}} {{CURRENT_ITEM}}.trx-addons-advanced-title-item-icon:hover i,
								{{WRAPPER}} {{CURRENT_ITEM}}.trx-addons-advanced-title-item-icon:hover svg,
								{{WRAPPER}} {{CURRENT_ITEM}}.trx-addons-advanced-title-item-image:hover img',
			]
		);

		$repeater->add_control(
			'highlight_color_hover',
			[
				'label' => esc_html__( 'Highlight Color', 'trx_addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}}:hover svg path' => 'stroke: {{VALUE}}', 

				],
				'condition' => [
					'highlight_style!' => ['none', 'color'], // 'color' is added for compatibility with old versions
					'type' => 'text',
				],
			]
		);

		$repeater->end_controls_tab();

		$repeater->end_controls_tabs();

		$repeater->add_responsive_control(
			'padding',
			array(
				'label'      => __( 'Padding', 'trx_addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'separator' => 'before',
				'selectors'  => array(
								'{{WRAPPER}} {{CURRENT_ITEM}}.trx-addons-advanced-title-item-text,
								 {{WRAPPER}} {{CURRENT_ITEM}}.trx-addons-advanced-title-item-icon i,
								 {{WRAPPER}} {{CURRENT_ITEM}}.trx-addons-advanced-title-item-icon svg,
								 {{WRAPPER}} {{CURRENT_ITEM}}.trx-addons-advanced-title-item-image img' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$repeater->add_responsive_control(
			'margin',
			array(
				'label'       => __( 'Margin', 'trx_addons' ),
				'type'        => Controls_Manager::DIMENSIONS,
				'size_units'  => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors'   => array(
								'{{WRAPPER}} {{CURRENT_ITEM}}.trx-addons-advanced-title-item-text,
								 {{WRAPPER}} {{CURRENT_ITEM}}.trx-addons-advanced-title-item-icon i,
								 {{WRAPPER}} {{CURRENT_ITEM}}.trx-addons-advanced-title-item-icon svg,
								 {{WRAPPER}} {{CURRENT_ITEM}}.trx-addons-advanced-title-item-image img' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$repeater->add_control(
			'heading_text_highlight',
			[
				'label' => __( 'Text Highlight', 'trx_addons' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'type' => 'text',
				]
			]
		);

		$repeater->add_control(
			'highlight_style',
			[
				'label' => __( 'Highlight Style', 'trx_addons' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'none',
				'options' => [
					'none'    => __( 'None', 'trx_addons' ),
					'stroke1' => __( 'Stroke 1', 'trx_addons' ),
					'stroke2' => __( 'Stroke 2', 'trx_addons' ),
					'stroke3' => __( 'Stroke 3', 'trx_addons' ),
					'stroke4' => __( 'Stroke 4', 'trx_addons' ),
					'stroke5' => __( 'Stroke 5', 'trx_addons' ),
					'stroke6' => __( 'Stroke 6', 'trx_addons' ),
					'stroke7' => __( 'Stroke 7', 'trx_addons' ),
					'stroke8' => __( 'Stroke 8', 'trx_addons' ),
					'stroke9' => __( 'Stroke 9', 'trx_addons' ),
				],
				'render_type' => 'template',
				'condition' => [
					'type' => 'text'
				],
			]
		);

		$repeater->add_control(
			'highlight_color',
			[
				'label' => esc_html__( 'Highlight Color', 'trx_addons' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#DF3232',
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}} svg path' => 'stroke: {{VALUE}}', 

				],
				'condition' => [
					'highlight_style!' => ['none', 'color'], // 'color' is added for compatibility with old versions
					'type' => 'text',
				],
			]
		);

		$repeater->add_control(
			'highlight_width',
			[
				'label' => __( 'Highlight Width', 'trx_addons' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 100,
					],
				],
				'default' => [
					'size' => '40',
					'unit' => 'px'
				],
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}} path' => 'stroke-width: {{SIZE}}',
				],
				'condition' => [
					'highlight_style!' => ['none', 'color'], // 'color' is added for compatibility with old versions
					'type' => 'text'
				],
			]
		);

		$repeater->add_control(
			'hightlight_offset',
			[
				'label' => __( 'Hightlight Offset', 'trx_addons' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'%' => [
						'min' => -100,
						'max' => 100,
					],
					'px' => [
						'min' => -100,
						'max' => 100,
					],
				],
				'default' => [
					'size' => '0',
					'unit' => '%',
				],
				'size_units' => ['%', 'px'],
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}} svg' => 'bottom: {{SIZE}}{{UNIT}}',
				],
				'condition' => [
					'highlight_style!' => ['none', 'color'], // 'color' is added for compatibility with old versions
					'type' => 'text'
				],
			]
		);

		$repeater->add_control(
			'hightlight_forward',
			[
				'label' => esc_html__( 'Bring Hightlight Forward', 'trx_addons' ),
				'type' => Controls_Manager::SWITCHER,
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}} svg,
					 {{WRAPPER}} {{CURRENT_ITEM}} .trx-addons-svg-wrapper .sc_item_animated_block' => 'z-index: 1;',
				],
				'condition' => [
					'highlight_style!' => ['none', 'color'], // 'color' is added for compatibility with old versions
					'type' => 'text'
				],
			]
		);

		$this->add_control(
			'content',
			[
				'label' => __( 'Content', 'trx_addons' ),
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'default' => [
					[
						'text' => __( 'Enter your text here', 'trx_addons' ),
						'highlight_style' => 'none',
					],
				],
				'title_field' => '{{{ type == "text" ? text : type }}}',
				'render_type' => 'template',
			]
		);

		$this->add_control(
			'add_separator',
			[
				'label' => __( 'Add Separator', 'trx_addons' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => '1',
				'return_value' => '1',
				'render_type' => 'template',
			]
		);

		$this->add_control(
			'link',
			[
				'label' => __( 'Link', 'trx_addons' ),
				'label_block' => true,
				'type' => Controls_Manager::URL,
				'dynamic'     => [
					'active' => true,
				],
				'placeholder' => __('https://your-link.com', 'trx_addons'),
			]
		);

		$this->add_control(
			'tag',
			[
				'label' => __( 'HTML Tag', 'trx_addons' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'h1' => 'H1',
					'h2' => 'H2',
					'h3' => 'H3',
					'h4' => 'H4',
					'h5' => 'H5',
					'h6' => 'H6',
					'div' => 'div',
					'span' => 'span',
					'p' => 'p',
				],
				'default' => 'h2',
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Register Advanced Title Controls in Style tab
	 *
	 * @return void
	 */
	protected function register_style_controls_general() {
		/**
		 * Style Tab: Advanced Title
		 * -------------------------------------------------
		 */
		$this->start_controls_section(
			'section_style_text',
			[
				'label' => __( 'Headline', 'trx_addons' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'alignment',
			[
				'label' => __( 'Alignment', 'trx_addons' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => __( 'Left', 'trx_addons' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'trx_addons' ),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => __( 'Right', 'trx_addons' ),
						'icon' => 'eicon-text-align-right',
					],
				],
				'default' => 'center',
				'selectors' => [
					'{{WRAPPER}}' => 'text-align: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'title_typography',
				'selector' => '{{WRAPPER}} .trx-addons-advanced-title-item-icon,
								{{WRAPPER}} .trx-addons-advanced-title-item-text,
								{{WRAPPER}} .trx-addons-advanced-title-item-image',
			]
		);

		$this->add_control(
			'text_color',
			[
				'label' => esc_html__( 'Color', 'trx_addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .trx-addons-advanced-title' => 'color: {{VALUE}}; fill: {{VALUE}}',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Register Highlight Controls in Style tab
	 *
	 * @return void
	 */
	protected function register_style_controls_highlight() {
		/**
		 * Style Tab: Highlight
		 * -------------------------------------------------
		 */
		$this->start_controls_section(
			'section_style_highlight',
			[
				'label' => __( 'Highlight Animation', 'trx_addons' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'highlight_animation',
			[
				'label' => __( 'Animation', 'trx_addons' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'On', 'trx_addons' ),
				'label_off' => __( 'Off', 'trx_addons' ),
				'default' => 'animate',
				'separator' => 'before',
				'return_value' =>'animate',
				'prefix_class' => 'trx-addons-',
			]
		);

		$this->add_control(
			'highlight_animation_delay',
			[
				'label' => __( 'Animation Delay', 'trx_addons' ) . ' (ms)',
				'type' => Controls_Manager::NUMBER,
				'default' => '',
				'min' => 0,
				'step' => 100,
				'render_type' => 'template',
				'condition' => [
					'highlight_animation' => 'animate',
				],
			]
		);

		$this->add_control(
			'highlight_animation_duration',
			[
				'label' => __( 'Animation Duration', 'trx_addons' ),
				'type' => Controls_Manager::NUMBER,
				'default' => '',
				'min' => 0,
				'step' => 100,
				'render_type' => 'template',
				'default' => 2000,
				'condition' => [
					'highlight_animation' => 'animate',
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
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @access protected
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		if ( (int)$settings['highlight_animation_duration'] > 0 ) {
			$this->add_render_attribute( 'advanced-title', [
				'style' => '--var-trx-addons-advanced-title-highlight-animation-duration: ' . $settings['highlight_animation_duration'] . 'ms;',
			] );
		}

		?><<?php echo esc_html( $settings['tag'] );
			echo ' class="trx-addons-advanced-title"';
			echo (int)$settings['highlight_animation_delay'] > 0 ? ' data-delay="' . esc_attr( $settings['highlight_animation_delay'] ) . '"' : '';
			echo ' ' . wp_kses_post( $this->get_render_attribute_string( 'advanced-title' ) );
		?>><?php

		foreach ( $settings['content'] as $item ) {

			$link_url = $link_target = $link_nofollow = '';
			if ( ! empty( $item['item_link']['url'] ) ) {
				$link_url = $item['item_link']['url'];
				$link_target = (int)$item['item_link']['is_external'] ? ' target="_blank"' : '';
				$link_nofollow = (int)$item['item_link']['nofollow'] ? ' rel="nofollow"' : '';
			} else if ( ! empty( $settings['link']['url'] ) ) {
				$link_url = $settings['link']['url'];
				$link_target = $settings['link']['is_external'] ? ' target="_blank"' : '';
				$link_nofollow = $settings['link']['nofollow'] ? ' rel="nofollow"' : '';
			}
			$start_tag = $link_url ? '<a href="' . $link_url . '"' . $link_target . $link_nofollow : '<span';
			$end_tag = $link_url ? '</a>' : '</span>';

			$separator = ( $item['layout'] === 'block' ? '<br />' : '' ) . ( ! empty( $settings['add_separator'] ) ? "\n" : '' );

			$svg_markup = null;

			switch ( $item['type'] ) {
				case 'text':
					if ( $item['highlight_style'] == 'none' || $item['highlight_style'] == 'color' ) {	// 'color' is added for compatibility with old versions
						echo $separator . $start_tag . ' class="trx-addons-advanced-title-item trx-addons-advanced-title-item-text elementor-repeater-item-' . esc_attr( $item['_id'] ) . '">' . TrxAddonsUtils::esc_string( $item['text'] ) . $end_tag;
					} else {
						$svg_markup = $this->get_svg( 'single', $item['highlight_style'] );
						echo $separator . $start_tag . ' class="trx-addons-advanced-title-item trx-addons-advanced-title-item-text elementor-repeater-item-' . esc_attr( $item['_id'] ) . '  trx-addons-advanced-title-item-highlighted trx-addons-advanced-title-' . esc_attr( $item['highlight_style'] ) . '">'
								. '<span class="trx-addons-advanced-title-text-wrap">'
									. '<span class="trx-addons-advanced-title-text">' . TrxAddonsUtils::esc_string( $item['text'] ) . '</span>'
									. TrxAddonsUtils::esc_svg( $svg_markup )
								. '</span>'
							. $end_tag;
					}
					break;
				case 'icon':
					echo $separator . $start_tag . ' class="trx-addons-advanced-title-item trx-addons-advanced-title-item-icon elementor-repeater-item-' . esc_attr( $item['_id'] ) . '">';
					Icons_Manager::render_icon( $item['icon'], [ 'aria-hidden' => 'true', 'class' => 'char' ] );
					echo $end_tag;
					break;
				case 'image':
					echo $separator . $start_tag . ' class="trx-addons-advanced-title-item trx-addons-advanced-title-item-image elementor-repeater-item-' . esc_attr( $item['_id'] ) . '">';
					if ( ! empty( $item['image']['id'] ) ) {
						echo wp_get_attachment_image( $item['image']['id'], 'medium', false, [ 'loading' => false, 'class' => 'char' ] );
					} else if ( ! empty( $item['image']['url'] ) ) {
						$url = trx_addons_get_attachment_url( $item['image']['url'], apply_filters( 'trx_addons_filter_thumb_size', trx_addons_get_thumb_size('medium'), 'advanced-title' ) );
						if ( ! empty( $url ) ) {
							echo '<img src="' . esc_url( $url ) . '" alt="" class="char" />';
						}
					}
					echo $end_tag;
					break;
				default:
					break;
			}
		}

		?></<?php echo esc_html( $settings['tag'] ); ?>><?php
	}

	/**
	 * Render a shape svg output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 * 
	 * @param string $return  Return type. Possible values are 'list' and 'single'.
	 * @param string $type    Shape type.
	 *
	 * @access protected
	 */
	protected function get_svg( $return, $type = '' ) {
		$paths = [
			'none'    => null,
			'stroke1' => "<path d='M15.2,133.3L15.2,133.3c121.9-7.6,244-9.9,366.1-6.8c34.6,0.9,69.1,2.3,103.7,4'/>",
			'stroke2' => "<path d='M479,122c-13.3-1-26.8-1-40.4-2.3c-31.8-1.2-63.7,0.4-95.3,0.6c-38.5,1.5-77,2.3-115.5,5.2
									c-41.6,1.2-83.3,5-124.9,5.2c-5.4,0.4-11,1-16.4,0.8c-21.9-0.4-44.1,1.9-65.6-3.5'/>",
			'stroke3' => "<path d='M15,133.4c19-12.7,48.1-11.4,69.2-8.2
									c6.3,1.1,12.9,2.1,19.2,3.4c16.5,3.2,33.5,6.3,50.6,5.5c12.7-0.6,24.9-3.4,36.7-6.1c11-2.5,22.4-5.1,34.2-5.9
									c24.3-1.9,48.5,3.4,71.9,8.4c27.6,6.1,53.8,11.8,80.4,6.8c9.9-1.9,19.2-5.3,28.3-8.4c8.2-3,16.9-5.9,25.9-8
									c20.3-4.4,45.8-1.1,53.6,12.2'/>",
			'stroke4' => "<path d='M18,122.6c42.3-4.6,87.4-5.1,130.3-1.6'/>
						<path d='M166.7,121.3c29.6,1.6,60,3.3,90.1,1.8c12.4-0.5,24.8-1.6,36.9-2.7c7.3-0.7,14.8-1.3,22.3-1.8
									c55.5-4.2,112.6-1.8,166,1.1'/>
						<path d='M57.8,133c30.8-0.7,62,1.1,92.1,2.7c30.5,1.8,62,3.6,93.2,2.7c20.4-0.5,41.1-2.4,61.1-4
									c37.6-3.1,76.5-6.4,113.7-2'/>",
			'stroke5' => "<path d='M53.4,135.8c-12.8-1.5-25.6-1.3-38.3,0.7'/>
						<path d='M111.2,136c-12.2-0.2-24.4-0.5-36.7-0.8'/>
						<path d='M163.3,135.2c-12.2,0.2-24.4,0.5-36.6,0.8'/>
						<path d='M217.8,134.7c-12.5,0.6-24.9,1.2-37.4,1.8'/>
						<path d='M274.7,135.5c-12.8,0.1-25.5,0.1-38.3,0.2'/>
						<path d='M327.6,135.1c-13.6-0.8-27.2-0.3-40.7,1.4'/>
						<path d='M378.8,134.7c-12.2,0.6-24.4,1.2-36.6,1.8'/>
						<path d='M432.5,136.4c-12.2-0.6-24.4-1.1-36.6-1.7'/>
						<path d='M487.9,136.1c-11.6-1.3-23.3-1.4-35-0.2'/>",
			'stroke6' => "<path d='M14.4,111.6c0,0,202.9-33.7,471.2,0c0,0-194-8.9-397.3,24.7c0,0,141.9-5.9,309.2,0'/>",
			'stroke7' => "<path d='M15.2 133.3H485'/>",
			'stroke8' => '<path d="M1.65186 148.981C1.65186 148.981 73.8781 98.5943 206.859 93.0135C339.841 87.4327 489.874 134.065 489.874 134.065"/>',
			'stroke9' => '<path d="M7 74.5C7 74.5 104 127 252 117C400 107 494.5 49 494.5 49C494.5 49 473.5 59 461.5 74.5C449.5 90 449.5 107 449.5 107"/>
						<path d="M20.5 101.5C20.5 101.5 93 133.5 180.5 142.5C268 151.5 347 127.5 347 127.5"/>'
		];

		if ( $return == 'list' ) {
			foreach ( $paths as $name => $path ) {
				if ( $name == 'none' ) {
					$svg[ $name ] = "";
				} else {
					$svg[ $name ] = '<span class="trx-addons-svg-wrapper"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 500 150" preserveAspectRatio="none">' . $path . '</svg></span>';
				}
			}
		} else {
			if ( $type == 'none' ) {
				$svg = "";
			} else {
				$svg = '<span class="trx-addons-svg-wrapper"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 500 150" preserveAspectRatio="none">' . $paths[ $type ] . '</svg></span>';
			}
		}

		return $svg;
	}

	/**
	 * Render a widget output in the editor.
	 *
	 * Written as a Backbone JavaScript template and used to generate the live preview.
	 *
	 * @access protected
	 */
	protected function content_template() {
		?><#
		var svgs = <?php echo wp_json_encode( $this->get_svg( 'list' ) ); ?>;

		if ( settings.highlight_animation_duration > 0 ) {
			view.addRenderAttribute( 'advanced-title', 'style', '--var-trx-addons-advanced-title-highlight-animation-duration: ' + settings.highlight_animation_duration + 'ms;' );
		}

		#><{{{ settings.tag }}} class="trx-addons-advanced-title"<# print( settings.highlight_animation_delay > 0 ? ' data-delay="' + settings.highlight_animation_delay + '"' : '' ); #> {{{ view.getRenderAttributeString( 'advanced-title' ) }}}><#

		settings.content.forEach( function( item ) {

			var link_url = '',
				link_target = '',
				link_nofollow = '';
			if ( item.item_link.url != '' ) {
				link_url = item.item_link.url;
				link_target = item.item_link.is_external ? ' target="_blank"' : '';
				link_nofollow = item.item_link.nofollow ? ' rel="nofollow"' : '';
			} else if ( settings.link.url ) {
				link_url = settings.link.url;
				link_target = settings.link.is_external ? ' target="_blank"' : '';
				link_nofollow = settings.link.nofollow ? ' rel="nofollow"' : '';
			}
			var start_tag = link_url ? '<a href="' + link_url + '"' + link_target + link_nofollow : '<span';
			var end_tag = link_url ? '</a>' : '</span>';

			var separator = ( item.layout == 'block' ? '<br />' : '' ) + ( settings.add_separator ? "\n" : '' );

			switch ( item.type ) {
				case 'text':
					if ( item.highlight_style == 'none' || item.highlight_style == 'color' ) {	// 'color' is added for compatibility with old versions
						#>{{{ separator }}}{{{ start_tag }}} class="trx-addons-advanced-title-item trx-addons-advanced-title-item-text elementor-repeater-item-{{{ item._id }}}">{{{ item.text }}}{{{ end_tag }}}<#
					} else {
						#>{{{ separator }}}{{{ start_tag }}} class="trx-addons-advanced-title-item trx-addons-advanced-title-item-text trx-addons-advanced-title-item-highlighted {{{ 'trx-addons-advanced-title-' + item.highlight_style }}} elementor-repeater-item-{{{ item._id }}}"><#
							#><span class="trx-addons-advanced-title-text-wrap"><#
								#><span class="trx-addons-advanced-title-text">{{{ item.text }}}</span><#
								#>{{{ svgs[ item.highlight_style ] }}}<#
							#></span><#
						#>{{{ end_tag }}}<#
					}
					break;
				case 'icon':
					var iconHTML = elementor.helpers.renderIcon( view, item.icon, { 'aria-hidden': true, 'class': 'char' }, 'i' , 'value' );
					if ( typeof iconHTML == 'object' && iconHTML.rendered ) {
						iconHTML = iconHTML.value;
					}
					#>{{{ separator }}}{{{ start_tag }}} class="trx-addons-advanced-title-item trx-addons-advanced-title-item-icon elementor-repeater-item-{{{ item._id }}}">{{{ iconHTML }}}{{{ end_tag }}}<#
					break;
				case 'image':
					var image = {
						id: item.image.id,
						url: item.image.url,
						size: item.thumbnail_size,
						dimension: item.thumbnail_custom_dimension,
						model: view.getEditModel()
					};
					var image_url = elementor.imagesManager.getImageUrl( image );
					#>{{{ separator }}}{{{ start_tag }}} class="trx-addons-advanced-title-item trx-addons-advanced-title-item-image elementor-repeater-item-{{{ item._id }}}"><img class="char" src="{{{ image_url }}}" />{{{ end_tag }}}<#
					break;
				default:
					break;
			}
		} );

		#></{{{ settings.tag }}}><?php
	}
}
