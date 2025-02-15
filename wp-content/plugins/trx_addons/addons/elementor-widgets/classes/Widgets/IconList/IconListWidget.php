<?php
/**
 * Icon List Widget
 *
 * @package ThemeREX Addons
 * @since v2.30.0
 */

namespace TrxAddons\ElementorWidgets\Widgets\IconList;

use TrxAddons\ElementorWidgets\BaseWidget;
use TrxAddons\ElementorWidgets\Utils as TrxAddonsUtils;

// Elementor Classes.
use Elementor\Controls_Manager;
use Elementor\Control_Media;
use Elementor\Utils;
use Elementor\Repeater;
use Elementor\Icons_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Image_Size;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Icon List Widget
 */
class IconListWidget extends BaseWidget {

	/**
	 * Register widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 */
	protected function register_controls() {
		/* Content Tab */
		$this->register_content_list_controls();
		$this->register_content_help_docs_controls();

		/* Style Tab */
		$this->register_style_list_controls();
		$this->register_style_icon_controls();
		$this->register_style_text_controls();
	}

	/*-----------------------------------------------------------------------------------*/
	/*	CONTENT TAB
	/*-----------------------------------------------------------------------------------*/

	protected function register_content_list_controls() {
		/**
		 * Content Tab: List
		 */
		$this->start_controls_section(
			'section_list',
			[
				'label'                 => __( 'Icon List', 'trx_addons' ),
			]
		);

		$this->add_control(
			'view',
			[
				'label'                 => __( 'Layout', 'trx_addons' ),
				'type'                  => Controls_Manager::CHOOSE,
				'default'               => 'traditional',
				'options'               => [
					'traditional'  => [
						'title'    => __( 'Default', 'trx_addons' ),
						'icon'     => 'eicon-editor-list-ul',
					],
					'inline'       => [
						'title'    => __( 'Inline', 'trx_addons' ),
						'icon'     => 'eicon-ellipsis-h',
					],
				],
				'render_type'           => 'template',
				'toggle'                => false,
				'prefix_class'          => 'trx-addons-icon-list-',
				'label_block'           => false,
			]
		);

		$this->add_control(
			'text_html_tag',
			array(
				'label'   => __( 'Text HTML Tag', 'trx_addons' ),
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

		$repeater = new Repeater();

		$repeater->start_controls_tabs( 'items_repeater' );

		$repeater->start_controls_tab( 'tab_content', [ 'label' => __( 'Content', 'trx_addons' ) ] );

		$repeater->add_control(
			'text',
			array(
				'label'       => __( 'Text', 'trx_addons' ),
				'label_block' => true,
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array(
					'active' => true,
				),
				'default'     => __( 'List Item #1', 'trx_addons' ),
			)
		);

		$repeater->add_control(
			'trx_icon_type',
			array(
				'label'       => esc_html__( 'Icon Type', 'trx_addons' ),
				'type'        => Controls_Manager::CHOOSE,
				'label_block' => false,
				'options'     => array(
					'none'  => array(
						'title' => esc_html__( 'None', 'trx_addons' ),
						'icon'  => 'eicon-ban',
					),
					'icon'  => array(
						'title' => esc_html__( 'Icon', 'trx_addons' ),
						'icon'  => 'eicon-star',
					),
					'image' => array(
						'title' => esc_html__( 'Image', 'trx_addons' ),
						'icon'  => 'eicon-image-bold',
					),
					'text'  => array(
						'title' => esc_html__( 'Text', 'trx_addons' ),
						'icon'  => 'eicon-font',
					),
				),
				'default'     => 'icon',
			)
		);

		$repeater->add_control(
			'icon',
			array(
				'label'            => __( 'Icon', 'trx_addons' ),
				'type'             => Controls_Manager::ICONS,
				'label_block'      => true,
				'default'          => array(
					'value'   => 'fas fa-check',
					'library' => 'fa-solid',
				),
				'fa4compatibility' => 'list_icon',
				'condition'        => array(
					'trx_icon_type' => 'icon',
				),
			)
		);

		$repeater->add_control(
			'list_image',
			array(
				'label'       => __( 'Image', 'trx_addons' ),
				'label_block' => true,
				'type'        => Controls_Manager::MEDIA,
				'dynamic'     => array(
					'active' => true,
				),
				'default'     => array(
					'url' => Utils::get_placeholder_image_src(),
				),
				'condition'   => array(
					'trx_icon_type' => 'image',
				),
			)
		);

		$repeater->add_control(
			'icon_text',
			array(
				'label'       => __( 'Text', 'trx_addons' ),
				'label_block' => false,
				'type'        => Controls_Manager::TEXT,
				'default'     => '',
				'condition'   => array(
					'trx_icon_type' => 'text',
				),
			)
		);

		$repeater->add_control(
			'link',
			array(
				'label'       => __( 'Link', 'trx_addons' ),
				'type'        => Controls_Manager::URL,
				'label_block' => true,
				'dynamic'     => array(
					'active' => true,
				),
				'placeholder' => __( 'http://your-link.com', 'trx_addons' ),
			)
		);

		$repeater->end_controls_tab();

		$repeater->start_controls_tab( 'tab_icon', [ 'label' => __( 'Style', 'trx_addons' ) ] );

		$repeater->add_responsive_control(
			'single_icon_size',
			[
				'label'                 => __( 'Icon Size', 'trx_addons' ),
				'type'                  => Controls_Manager::SLIDER,
				'range'                 => [
					'px' => [
						'min' => 6,
						'max' => 100,
					],
				],
				'size_units'            => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-list-items {{CURRENT_ITEM}} .trx-addons-icon-list-icon' => 'font-size: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}};',	// height: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}};
					'{{WRAPPER}} .trx-addons-list-items {{CURRENT_ITEM}} .trx-addons-icon-list-image img' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$repeater->add_responsive_control(
			'single_icon_box_size',
			[
				'label'                 => __( 'Box Size', 'trx_addons' ),
				'type'                  => Controls_Manager::SLIDER,
				'range'                 => [
					'px' => [
						'min' => 6,
						'max' => 100,
					],
				],
				'size_units'            => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-list-items {{CURRENT_ITEM}} .trx-addons-icon-list-icon' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .trx-addons-list-items {{CURRENT_ITEM}} .trx-addons-icon-list-image' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .trx-addons-list-items {{CURRENT_ITEM}} .trx-addons-icon-list-image img' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$repeater->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'                  => 'single_item_border',
				'label'                 => __( 'Border', 'trx_addons' ),
				'placeholder'           => '',
				'default'               => '',
				'selector'              => '{{WRAPPER}}  .trx-addons-list-items {{CURRENT_ITEM}}',
			]
		);

		$repeater->end_controls_tab();

		$repeater->end_controls_tabs();

		$this->add_control(
			'list_items',
			array(
				'label'       => __( 'Items', 'trx_addons' ),
				'type'        => Controls_Manager::REPEATER,
				'default'     => array(
					array(
						'text' => __( 'List Item #1', 'trx_addons' ),
						'icon' => __( 'fa fa-check', 'trx_addons' ),
					),
					array(
						'text' => __( 'List Item #2', 'trx_addons' ),
						'icon' => __( 'fa fa-check', 'trx_addons' ),
					),
					array(
						'text' => __( 'List Item #3', 'trx_addons' ),
						'icon' => __( 'fa fa-check', 'trx_addons' ),
					),
				),
				'fields'      => $repeater->get_controls(),
				'title_field' => '<i class="{{ icon }}" aria-hidden="true"></i> {{{ text }}}',
			)
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name'                  => 'image',
				'label'                 => __( 'Image Size', 'trx_addons' ),
				'default'               => 'full',
				'separator'             => 'before',
			]
		);

		$this->add_control(
			'link_click',
			array(
				'label'        => esc_html__( 'Apply Link On', 'trx_addons' ),
				'type'         => Controls_Manager::SELECT,
				'options'      => array(
					'full_width' => esc_html__( 'Full Width', 'trx_addons' ),
					'inline'     => esc_html__( 'Inline', 'trx_addons' ),
				),
				'default'      => 'inline',
				'prefix_class' => 'elementor-list-item-link-',
			)
		);

		$this->end_controls_section();
	}

	/*-----------------------------------------------------------------------------------*/
	/*	STYLE TAB
	/*-----------------------------------------------------------------------------------*/

	protected function register_style_list_controls() {
		/**
		 * Style Tab: List
		 */
		$this->start_controls_section(
			'section_list_style',
			[
				'label'                 => __( 'List', 'trx_addons' ),
				'tab'                   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'items_spacing',
			[
				'label'                 => __( 'List Items Gap', 'trx_addons' ),
				'type'                  => Controls_Manager::GAPS,
				'size_units'            => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-list-items.trx-addons-inline-items' => 'gap: {{ROW}}{{UNIT}} {{COLUMN}}{{UNIT}};',
					'{{WRAPPER}} .trx-addons-list-items:not(.trx-addons-inline-items) li:not(:last-child)' => 'margin-bottom: {{ROW}}{{UNIT}};',
					// 'body:not(.rtl) {{WRAPPER}} .trx-addons-list-items.trx-addons-inline-items li:not(:last-child)' => 'margin-right: {{SIZE}}{{UNIT}};',
					// 'body.rtl {{WRAPPER}} .trx-addons-list-items.trx-addons-inline-items li:not(:last-child)' => 'margin-left: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'                  => 'items_background',
				'label'                 => __( 'Background', 'trx_addons' ),
				'types'                 => [ 'classic', 'gradient' ],
				'selector'              => '{{WRAPPER}} .trx-addons-list-items li',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'                  => 'list_items_border',
				'label'                 => __( 'Border', 'trx_addons' ),
				'placeholder'           => '1px',
				'default'               => '1px',
				'selector'              => '{{WRAPPER}} .trx-addons-list-items li',
			]
		);

		$this->add_responsive_control(
			'list_items_border_radius',
			[
				'label'                 => __( 'Border Radius', 'trx_addons' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-list-items li' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'list_items_padding',
			[
				'label'                 => __( 'Padding', 'trx_addons' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-list-items li' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'list_items_alignment',
			[
				'label'                 => __( 'Alignment', 'trx_addons' ),
				'type'                  => Controls_Manager::CHOOSE,
				'options'               => [
					'left'      => [
						'title' => __( 'Left', 'trx_addons' ),
						'icon'  => 'eicon-h-align-left',
					],
					'center'    => [
						'title' => __( 'Center', 'trx_addons' ),
						'icon'  => 'eicon-h-align-center',
					],
					'right'     => [
						'title' => __( 'Right', 'trx_addons' ),
						'icon'  => 'eicon-h-align-right',
					],
				],
				'default'               => '',
				'selectors'             => [
					'{{WRAPPER}}.trx-addons-icon-list-traditional .trx-addons-list-items li, {{WRAPPER}}.trx-addons-icon-list-inline .trx-addons-list-items' => 'justify-content: {{VALUE}};',
				],
				'selectors_dictionary' => [
					'left' => 'flex-start',
					'right' => 'flex-end',
				],
			]
		);

		$this->add_control(
			'divider',
			[
				'label'                 => __( 'Divider', 'trx_addons' ),
				'type'                  => Controls_Manager::SWITCHER,
				'label_off'             => __( 'Off', 'trx_addons' ),
				'label_on'              => __( 'On', 'trx_addons' ),
				'separator'             => 'before',
			]
		);

		$this->add_control(
			'divider_style',
			[
				'label'                 => __( 'Style', 'trx_addons' ),
				'type'                  => Controls_Manager::SELECT,
				'options'               => [
					'solid'    => __( 'Solid', 'trx_addons' ),
					'double'   => __( 'Double', 'trx_addons' ),
					'dotted'   => __( 'Dotted', 'trx_addons' ),
					'dashed'   => __( 'Dashed', 'trx_addons' ),
					'groove'   => __( 'Groove', 'trx_addons' ),
					'ridge'    => __( 'Ridge', 'trx_addons' ),
				],
				'default'               => 'solid',
				'condition'             => [
					'divider' => 'yes',
				],
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-list-items:not(.trx-addons-inline-items) li:not(:last-child)' => 'border-bottom-style: {{VALUE}};',
					'{{WRAPPER}} .trx-addons-list-items.trx-addons-inline-items li:not(:last-child)' => 'border-right-style: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'divider_weight',
			[
				'label'                 => __( 'Weight', 'trx_addons' ),
				'type'                  => Controls_Manager::SLIDER,
				'default'               => [
					'size' => 1,
				],
				'range'                 => [
					'px'   => [
						'min' => 1,
						'max' => 10,
					],
				],
				'condition'             => [
					'divider' => 'yes',
				],
				'size_units'            => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-list-items:not(.trx-addons-inline-items) li:not(:last-child)' => 'border-bottom-width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .trx-addons-list-items.trx-addons-inline-items li:not(:last-child)' => 'border-right-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'divider_color',
			[
				'label'                 => __( 'Color', 'trx_addons' ),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '#ddd',
				'global'                => [
					'default' => Global_Colors::COLOR_TEXT,
				],
				'condition'             => [
					'divider'  => 'yes',
				],
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-list-items:not(.trx-addons-inline-items) li:not(:last-child)' => 'border-bottom-color: {{VALUE}};',
					'{{WRAPPER}} .trx-addons-list-items.trx-addons-inline-items li:not(:last-child)' => 'border-right-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function register_style_icon_controls() {
		/**
		 * Style Tab: Icon
		 */
		$this->start_controls_section(
			'section_icon_style',
			[
				'label'                 => __( 'Icon', 'trx_addons' ),
				'tab'                   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'icon_position',
			[
				'label'                 => __( 'Position', 'trx_addons' ),
				'type'                  => Controls_Manager::CHOOSE,
				'label_block'           => false,
				'toggle'                => false,
				'default'               => 'left',
				'options'               => [
					'left'      => [
						'title' => __( 'Left', 'trx_addons' ),
						'icon'  => 'eicon-h-align-left',
					],
					'right'     => [
						'title' => __( 'Right', 'trx_addons' ),
						'icon'  => 'eicon-h-align-right',
					],
				],
				'prefix_class'          => 'trx-addons-icon-',
			]
		);

		$this->add_control(
			'icon_vertical_align',
			[
				'label'                 => __( 'Vertical Alignment', 'trx_addons' ),
				'type'                  => Controls_Manager::CHOOSE,
				'label_block'           => false,
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
				'selectors_dictionary'  => [
					'top'          => 'flex-start',
					'middle'       => 'center',
					'bottom'       => 'flex-end',
				],
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-list-container .trx-addons-list-items li'   => 'align-items: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'icon_size',
			[
				'label'                 => __( 'Size', 'trx_addons' ),
				'type'                  => Controls_Manager::SLIDER,
				'default'               => [
					'size' => 14,
				],
				'range'                 => [
					'px' => [
						'min' => 6,
						'max' => 100,
					],
				],
				'size_units'            => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-list-items .trx-addons-icon-list-icon' => 'font-size: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .trx-addons-list-items .trx-addons-icon-list-image img' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'icon_box_size',
			[
				'label'                 => __( 'Box Size', 'trx_addons' ),
				'type'                  => Controls_Manager::SLIDER,
				'default'               => [
					'size' => '',
				],
				'range'                 => [
					'px' => [
						'min' => 6,
						'max' => 100,
					],
				],
				'size_units'            => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-list-items .trx-addons-icon-list-icon' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .trx-addons-list-items .trx-addons-icon-list-image' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .trx-addons-list-items .trx-addons-icon-list-image img' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'icon_spacing',
			[
				'label'                 => __( 'Spacing', 'trx_addons' ),
				'type'                  => Controls_Manager::SLIDER,
				'default'               => [
					'size' => 8,
				],
				'range'                 => [
					'px' => [
						'max' => 50,
					],
				],
				'size_units'            => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors'             => [
					'body:not(.rtl) {{WRAPPER}}.trx-addons-icon-left .trx-addons-list-items .trx-addons-icon-wrapper' => 'margin-right: {{SIZE}}{{UNIT}};',
					'body.rtl {{WRAPPER}}.trx-addons-icon-left .trx-addons-list-items .trx-addons-icon-wrapper' => 'margin-left: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.trx-addons-icon-right .trx-addons-list-items .trx-addons-icon-wrapper' => 'margin-left: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'icon_vertical_offset',
			[
				'label'                 => esc_html__( 'Vertical Offset', 'trx_addons' ),
				'type'                  => Controls_Manager::SLIDER,
				'size_units'            => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'default'               => [
					'size' => 0,
				],
				'range'                 => [
					'px' => [
						'min' => -15,
						'max' => 15,
					],
					'em' => [
						'min' => -1,
						'max' => 1,
						'step' => 0.1
					],
				],
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-icon-wrapper' => 'top: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->start_controls_tabs( 'tabs_icon_style' );

		$this->start_controls_tab(
			'tab_icon_normal',
			[
				'label'                 => __( 'Normal', 'trx_addons' ),
			]
		);

		$this->add_control(
			'icon_color',
			[
				'label'                 => __( 'Color', 'trx_addons' ),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '',
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-list-items .trx-addons-icon-list-icon' => 'color: {{VALUE}};',
					'{{WRAPPER}} .trx-addons-list-items .trx-addons-icon-list-icon svg' => 'fill: {{VALUE}};',
				],
				'global'                => [
					'default' => Global_Colors::COLOR_PRIMARY,
				],
			]
		);

		$this->add_control(
			'icon_bg_color',
			[
				'label'                 => __( 'Background Color', 'trx_addons' ),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '',
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-list-items .trx-addons-icon-wrapper' => 'background: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'                  => 'icon_border',
				'label'                 => __( 'Border', 'trx_addons' ),
				'placeholder'           => '1px',
				'default'               => '1px',
				'selector'              => '{{WRAPPER}} .trx-addons-list-items .trx-addons-icon-wrapper',
			]
		);

		$this->add_responsive_control(
			'icon_border_radius',
			[
				'label'                 => __( 'Border Radius', 'trx_addons' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-list-items .trx-addons-icon-wrapper, {{WRAPPER}} .trx-addons-list-items .trx-addons-icon-list-image img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'icon_padding',
			[
				'label'                 => __( 'Padding', 'trx_addons' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-list-items .trx-addons-icon-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_icon_hover',
			[
				'label'                 => __( 'Hover', 'trx_addons' ),
			]
		);

		$this->add_control(
			'icon_color_hover',
			[
				'label'                 => __( 'Color', 'trx_addons' ),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '',
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-icon-list-item:hover .trx-addons-icon-wrapper .trx-addons-icon-list-icon' => 'color: {{VALUE}};',
					'{{WRAPPER}} .trx-addons-icon-list-item:hover .trx-addons-icon-wrapper .trx-addons-icon-list-icon svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'icon_bg_color_hover',
			[
				'label'                 => __( 'Background Color', 'trx_addons' ),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '',
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-icon-list-item:hover .trx-addons-icon-wrapper' => 'background: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'icon_border_color_hover',
			[
				'label'                 => __( 'Border Color', 'trx_addons' ),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '',
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-icon-list-item:hover .trx-addons-icon-wrapper' => 'border-color: {{VALUE}};',
				],
				'global'                => [
					'default' => Global_Colors::COLOR_SECONDARY,
				],
			]
		);

		$this->add_control(
			'icon_hover_animation',
			[
				'label'                 => __( 'Animation', 'trx_addons' ),
				'type'                  => Controls_Manager::HOVER_ANIMATION,
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'icon_number_heading',
			array(
				'label'     => __( 'Icon Type: Text', 'trx_addons' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'icon_number_typography',
				'label'    => __( 'Typography', 'trx_addons' ),
				'global'   => [
					'default' => Global_Typography::TYPOGRAPHY_ACCENT,
				],
				'selector' => '{{WRAPPER}} .trx-addons-list-items .trx-addons-icon-list-number',
			)
		);

		$this->end_controls_section();
	}

	protected function register_style_text_controls() {
		/**
		 * Style Tab: Text
		 */
		$this->start_controls_section(
			'section_text_style',
			[
				'label'                 => __( 'Text', 'trx_addons' ),
				'tab'                   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->start_controls_tabs( 'tabs_text_style' );

		$this->start_controls_tab(
			'tab_text_normal',
			[
				'label'                 => __( 'Normal', 'trx_addons' ),
			]
		);

		$this->add_control(
			'text_color',
			[
				'label'                 => __( 'Text Color', 'trx_addons' ),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '',
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-icon-list-text' => 'color: {{VALUE}};',
				],
				'global'                => [
					'default' => Global_Colors::COLOR_SECONDARY,
				],
			]
		);

		$this->add_control(
			'text_bg_color',
			[
				'label'                 => __( 'Background Color', 'trx_addons' ),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '',
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-icon-list-text' => 'background: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'text_padding',
			[
				'label'                 => __( 'Padding', 'trx_addons' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-icon-list-text' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'                  => 'text_typography',
				'label'                 => __( 'Typography', 'trx_addons' ),
				'global'                => [
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				],
				'selector'              => '{{WRAPPER}} .trx-addons-icon-list-text',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_text_hover',
			[
				'label'                 => __( 'Hover', 'trx_addons' ),
			]
		);

		$this->add_control(
			'text_hover_color',
			[
				'label'                 => __( 'Text Color', 'trx_addons' ),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '',
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-icon-list-item:hover .trx-addons-icon-list-text' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'text_hover_bg_color',
			[
				'label'                 => __( 'Background Color', 'trx_addons' ),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '',
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-icon-list-item:hover .trx-addons-icon-list-text' => 'background: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

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

		$text_tag = TrxAddonsUtils::validate_html_tag( $settings['text_html_tag'] );

		$this->add_render_attribute( [
			'icon-list' => [
				'class' => 'trx-addons-list-items',
			],
			'icon'      => [
				'class' => 'trx-addons-icon-list-icon',
			],
			'icon-wrap' => [
				'class' => 'trx-addons-icon-wrapper',
			],
		] );

		if ( 'inline' === $settings['view'] ) {
			$this->add_render_attribute( 'icon-list', 'class', 'trx-addons-inline-items' );
		}

		$i = 1;
		?>
		<div class="trx-addons-list-container">
			<ul <?php echo wp_kses_post( $this->get_render_attribute_string( 'icon-list' ) ); ?>>
				<?php foreach ( $settings['list_items'] as $index => $item ) : ?>
					<?php if ( $item['text'] ) { ?>
						<?php
							$item_key = $this->get_repeater_setting_key( 'item', 'list_items', $index );
							$text_key = $this->get_repeater_setting_key( 'text', 'list_items', $index );

							$this->add_render_attribute( [
								$item_key => [
									'class' => [
										'trx-addons-icon-list-item',
										'elementor-repeater-item-' . $item['_id'],
									],
								],
								$text_key => [
									'class' => 'trx-addons-icon-list-text',
								],
							] );

							$this->add_inline_editing_attributes( $text_key, 'none' );
						?>
						<li <?php echo wp_kses_post( $this->get_render_attribute_string( $item_key ) ); ?>>
							<?php
							if ( '' !== $item['link']['url'] ) {
								$link_key = 'link_' . $i;
								$this->add_link_attributes( $link_key, $item['link'] );
								?>
								<a <?php echo wp_kses_post( $this->get_render_attribute_string( $link_key ) ); ?>>
								<?php
							}
							$this->render_iconlist_icon( $item, $i );
							?>
							<<?php echo esc_html( $text_tag ); ?> <?php echo wp_kses_post( $this->get_render_attribute_string( $text_key ) ); ?>>
								<?php echo wp_kses_post( $item['text'] ); ?>
							</<?php echo esc_html( $text_tag ); ?>>
							<?php
							if ( '' !== $item['link']['url'] ) {
								?>
								</a>
								<?php
							}
							?>
						</li>
					<?php } ?>
					<?php $i++;
				endforeach; ?>
			</ul>
		</div>
		<?php
	}

	/**
	 * Render info-box carousel icon output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @access protected
	 */
	protected function render_iconlist_icon( $item, $i ) {
		$settings = $this->get_settings_for_display();

		$fallback_defaults = [
			'fa fa-check',
			'fa fa-times',
			'fa fa-dot-circle-o',
		];

		$migration_allowed = Icons_Manager::is_migration_allowed();

		// add old default
		if ( ! isset( $item['list_icon'] ) && ! $migration_allowed ) {
			$item['list_icon'] = isset( $fallback_defaults[ $index ] ) ? $fallback_defaults[ $index ] : 'fa fa-check';
		}

		$migrated = isset( $item['__fa4_migrated']['icon'] );
		$is_new = ! isset( $item['list_icon'] ) && $migration_allowed;

		if ( 'none' !== $item['trx_icon_type'] ) {
			$icon_key = 'icon_' . $i;
			$this->add_render_attribute( $icon_key, 'class', 'trx-addons-icon-wrapper' );

			if ( $settings['icon_hover_animation'] ) {
				$icon_animation = 'elementor-animation-' . $settings['icon_hover_animation'];
			} else {
				$icon_animation = '';
			}
			?>
			<span <?php echo wp_kses_post( $this->get_render_attribute_string( $icon_key ) ); ?>>
				<?php
				if ( 'icon' === $item['trx_icon_type'] ) {
					if ( ! empty( $item['list_icon'] ) || ( ! empty( $item['icon']['value'] ) && $is_new ) ) { ?>
						<span class="trx-addons-icon-list-icon trx-addons-icon <?php echo esc_attr( $icon_animation ); ?>">
						<?php
						if ( $is_new || $migrated ) {
							Icons_Manager::render_icon( $item['icon'], [ 'aria-hidden' => 'true' ] );
						} else { ?>
							<i class="<?php echo esc_attr( $item['list_icon'] ); ?>" aria-hidden="true"></i>
							<?php
						}
						echo '</span>';
					}
				} elseif ( 'image' === $item['trx_icon_type'] ) {
					$image_url = Group_Control_Image_Size::get_attachment_image_src( $item['list_image']['id'], 'image', $settings );

					if ( $image_url ) {
						$image_html = '<img src="' . esc_url( $image_url ) . '" alt="' . esc_attr( Control_Media::get_image_alt( $item['list_image'] ) ) . '">';
					} else {
						$image_html = '<img src="' . esc_url( $item['list_image']['url'] ) . '">';
					}
					?>
					<span class="trx-addons-icon-list-image <?php echo esc_attr( $icon_animation ); ?>"><?php echo wp_kses_post( $image_html ); ?></span>
					<?php
				} elseif ( 'text' === $item['trx_icon_type'] ) {
					if ( $item['icon_text'] ) {
						$number = $item['icon_text'];
					} else {
						$number = $i;
					}
					?>
					<span class="trx-addons-icon-list-icon trx-addons-icon-list-number <?php echo esc_attr( $icon_animation ); ?>"><?php echo esc_attr( $number ); ?></span>
					<?php
				}
				?>
			</span>
			<?php
		}
	}

	/**
	 * Render icon list widget output in the editor.
	 *
	 * Written as a Backbone JavaScript template and used to generate the live preview.
	 *
	 * @access protected
	 */
	protected function content_template() {
		?>
		<div class="trx-addons-list-container">
			<#
			var iconsHTML = {},
				migrated = {},
				list_class = '';
			
			var textHTMLTag = elementor.helpers.validateHTMLTag( settings.text_html_tag );

			if ( settings.view == 'inline' ) {
				list_class = 'trx-addons-inline-items';
			}
			
			view.addRenderAttribute( 'list_items', 'class', [ 'trx-addons-list-items', list_class ] );
			#>
			<ul {{{ view.getRenderAttributeString( 'list_items' ) }}}>
				<#
				var i = 1;
				_.each( settings.list_items, function( item, index ) {

					var itemKey = view.getRepeaterSettingKey( 'item', 'list_items', index ),
						textKey = view.getRepeaterSettingKey( 'text', 'list_items', index );
				   
					view.addRenderAttribute( itemKey, {
						'class': [
							'trx-addons-icon-list-item',
							'elementor-repeater-item-' + item._id,
						]
					} );
					view.addRenderAttribute( textKey, {
						'class': 'trx-addons-icon-list-text'
					} );

					view.addInlineEditingAttributes( textKey );

					if ( item.text != '' ) {
						#><li {{{ view.getRenderAttributeString( itemKey ) }}}><#
						if ( item.link && item.link.url ) {
							#><a href="{{ _.escape( item.link.url ) }}"><#
						}
						if ( item.trx_icon_type != 'none' ) {
							if ( settings.icon_position == 'right' ) {
								var icon_class = 'trx-addons-icon-right';
							} else {
								var icon_class = 'trx-addons-icon-left';
							}
							#><span class="trx-addons-icon-wrapper {{ icon_class }}"><#
								if ( item.trx_icon_type == 'icon' ) {
									if ( item.list_icon || item.icon.value ) {
										#><span class="trx-addons-icon-list-icon trx-addons-icon elementor-animation-{{ settings.icon_hover_animation }}" aria-hidden="true"><#
											iconsHTML[ index ] = elementor.helpers.renderIcon( view, item.icon, { 'aria-hidden': true }, 'i', 'object' );
											migrated[ index ] = elementor.helpers.isIconMigrated( item, 'icon' );
											if ( iconsHTML[ index ] && iconsHTML[ index ].rendered && ( ! item.list_icon || migrated[ index ] ) ) {
												#>{{{ iconsHTML[ index ].value }}}<#
											} else {
												#><i class="{{ item.list_icon }}" aria-hidden="true"></i><#
											}
										#></span><#
									}
								} else if ( item.trx_icon_type == 'image' ) {
									#><span class="trx-addons-icon-list-image elementor-animation-{{ settings.icon_hover_animation }}"><#
										var image = {
											id: item.list_image.id,
											url: item.list_image.url,
											size: settings.image_size,
											dimension: settings.image_custom_dimension,
											model: view.getEditModel()
										};
										var image_url = elementor.imagesManager.getImageUrl( image );
										#><img src="{{ _.escape( image_url ) }}" />
									</span><#
								} else if ( item.trx_icon_type == 'text' ) {
									if ( item.icon_text ) {
										var number = item.icon_text;
									} else {
										var number = i;
									}
									#><span class="trx-addons-icon-list-icon trx-addons-icon-list-number elementor-animation-{{ settings.icon_hover_animation }}">
										{{ number }}
									</span><#
								}
							#></span><#
						}
						var text = item.text;

						var text_html = '<' + textHTMLTag + ' ' + view.getRenderAttributeString( textKey ) + ' >' + text + '</' + textHTMLTag + '>';

						print( text_html );
						if ( item.link && item.link.url ) {
							#></a><# 
						}
						#></li><#
					}
					i++
				} );
			#></ul>
		</div>
		<?php
	}
}
