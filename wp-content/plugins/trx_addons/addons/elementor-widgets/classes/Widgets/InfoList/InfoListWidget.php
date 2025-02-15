<?php
/**
 * Info List Widget
 *
 * @package ThemeREX Addons
 * @since v2.30.0
 */

namespace TrxAddons\ElementorWidgets\Widgets\InfoList;

use TrxAddons\ElementorWidgets\BaseWidget;
use TrxAddons\ElementorWidgets\Utils as TrxAddonsUtils;

// Elementor Classes.
use Elementor\Controls_Manager;
use Elementor\Control_Media;
use Elementor\Utils;
use Elementor\Repeater;
use Elementor\Icons_Manager;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Image_Size;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Info List Widget
 */
class InfoListWidget extends BaseWidget {

	/**
	 * Register widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 */
	protected function register_controls() {
		/* Content Tab */
		$this->register_content_list_items_controls();
		$this->register_content_help_docs_controls();

		/* Style Tab */
		$this->register_style_list_controls();
		$this->register_style_connector_controls();
		$this->register_style_icon_controls();
		$this->register_style_title_controls();
		$this->register_style_button_controls();
	}

	/*-----------------------------------------------------------------------------------*/
	/*	CONTENT TAB
	/*-----------------------------------------------------------------------------------*/

	protected function register_content_list_items_controls() {
		/**
		 * Content Tab: List Items
		 */
		$this->start_controls_section(
			'section_list',
			array(
				'label' => __( 'List Items', 'trx_addons' ),
			)
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'text',
			array(
				'label'       => __( 'Title', 'trx_addons' ),
				'label_block' => true,
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array(
					'active' => true,
				),
				'default'     => __( 'List Item #1', 'trx_addons' ),
			)
		);

		$repeater->add_control(
			'description',
			array(
				'label'       => __( 'Description', 'trx_addons' ),
				'label_block' => true,
				'type'        => Controls_Manager::TEXTAREA,
				'dynamic'     => array(
					'active' => true,
				),
				'default'     => __( 'List Item Description', 'trx_addons' ),
			)
		);

		$repeater->add_control(
			'trx_addons_icon_type',
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
					'trx_addons_icon_type' => 'icon',
				),
			)
		);

		$repeater->add_control(
			'list_image',
			array(
				'label'       => __( 'Image', 'trx_addons' ),
				'label_block' => true,
				'type'        => Controls_Manager::MEDIA,
				'default'     => array(
					'url' => Utils::get_placeholder_image_src(),
				),
				'condition'   => array(
					'trx_addons_icon_type' => 'image',
				),
			)
		);

		$repeater->add_control(
			'icon_text',
			array(
				'label'       => __( 'Icon Text', 'trx_addons' ),
				'label_block' => false,
				'type'        => Controls_Manager::TEXT,
				'default'     => __( '1', 'trx_addons' ),
				'condition'   => array(
					'trx_addons_icon_type' => 'text',
				),
			)
		);

		$repeater->add_control(
			'link_type',
			array(
				'label'   => __( 'Link Type', 'trx_addons' ),
				'type'    => Controls_Manager::SELECT,
				'options' => array(
					'none'   => __( 'None', 'trx_addons' ),
					'box'    => __( 'Box', 'trx_addons' ),
					'title'  => __( 'Title', 'trx_addons' ),
					'button' => __( 'Button', 'trx_addons' ),
				),
				'default' => 'none',
			)
		);

		$repeater->add_control(
			'button_text',
			array(
				'label'     => __( 'Button Text', 'trx_addons' ),
				'type'      => Controls_Manager::TEXT,
				'dynamic'   => array(
					'active' => true,
				),
				'default'   => __( 'Get Started', 'trx_addons' ),
				'condition' => array(
					'link_type' => 'button',
				),
			)
		);

		$repeater->add_control(
			'selected_icon',
			array(
				'label'            => __( 'Button Icon', 'trx_addons' ),
				'type'             => Controls_Manager::ICONS,
				'label_block'      => true,
				'fa4compatibility' => 'button_icon',
				'condition'        => array(
					'link_type' => 'button',
				),
			)
		);

		$repeater->add_control(
			'button_icon_position',
			array(
				'label'     => __( 'Icon Position', 'trx_addons' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'after',
				'options'   => array(
					'after'  => __( 'After', 'trx_addons' ),
					'before' => __( 'Before', 'trx_addons' ),
				),
				'condition' => array(
					'link_type' => 'button',
				),
			)
		);

		$repeater->add_control(
			'link',
			array(
				'label'       => __( 'Link', 'trx_addons' ),
				'type'        => Controls_Manager::URL,
				'dynamic'     => array(
					'active' => true,
				),
				'label_block' => true,
				'placeholder' => __( 'http://your-link.com', 'trx_addons' ),
				'default'     => array(
					'url' => '#',
				),
				'conditions'  => array(
					'terms' => array(
						array(
							'name'     => 'link_type',
							'operator' => '!=',
							'value'    => 'none',
						),
					),
				),
			)
		);

		$this->add_control(
			'list_items',
			array(
				'label'       => '',
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
				),
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ text }}}',
			)
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			array(
				'name'      => 'thumbnail', // Usage: `{name}_size` and `{name}_custom_dimension`, in this case `thumbnail_size` and `thumbnail_custom_dimension`.,
				'label'     => __( 'Image Size', 'trx_addons' ),
				'default'   => 'full',
				'separator' => 'before',
			)
		);

		$this->add_control(
			'title_html_tag',
			array(
				'label'   => __( 'Title HTML Tag', 'trx_addons' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'div',
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
			'icon_text_html_tag',
			array(
				'label'   => __( 'Icon Text HTML Tag', 'trx_addons' ),
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
			'connector',
			array(
				'label'        => __( 'Connector', 'trx_addons' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'label_on'     => __( 'Yes', 'trx_addons' ),
				'label_off'    => __( 'No', 'trx_addons' ),
				'return_value' => 'yes',
			)
		);

		$this->add_control(
			'corner_lines',
			array(
				'label'        => __( 'Hide Corner Lines', 'trx_addons' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => '',
				'label_on'     => __( 'Yes', 'trx_addons' ),
				'label_off'    => __( 'No', 'trx_addons' ),
				'return_value' => 'yes',
				'condition'    => array(
					'connector' => 'yes',
				),
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
			array(
				'label' => __( 'List', 'trx_addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'items_spacing',
			array(
				'label'     => __( 'Item Spacing', 'trx_addons' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => array(
					'size' => 10,
				),
				'range'     => array(
					'px' => array(
						'max' => 50,
					),
				),
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors' => array(
					'{{WRAPPER}}.trx-addons-info-list-icon-left .trx-addons-info-list-item:not(:first-child) .trx-addons-info-list-item-inner'  => 'margin-top: calc( {{SIZE}}{{UNIT}} / 2 );',
					'{{WRAPPER}}.trx-addons-info-list-icon-left .trx-addons-info-list-item:not(:last-child) .trx-addons-info-list-item-inner'  => 'margin-bottom: calc( {{SIZE}}{{UNIT}} / 2 );',
					'{{WRAPPER}}.trx-addons-info-list-icon-right .trx-addons-info-list-item:not(:first-child) .trx-addons-info-list-item-inner' => 'margin-top: calc( {{SIZE}}{{UNIT}} / 2 );',
					'{{WRAPPER}}.trx-addons-info-list-icon-right .trx-addons-info-list-item:not(:last-child) .trx-addons-info-list-item-inner' => 'margin-bottom: calc( {{SIZE}}{{UNIT}} / 2 );',
					'{{WRAPPER}}.trx-addons-info-list-icon-top .trx-addons-info-list-item .trx-addons-info-list-item-inner' => 'margin-right: calc( {{SIZE}}{{UNIT}} / 2 ); margin-left: calc( {{SIZE}}{{UNIT}} / 2 );',
					'{{WRAPPER}}.trx-addons-info-list-icon-top .trx-addons-list-items' => 'margin-right: calc( -{{SIZE}}{{UNIT}} / 2 ); margin-left: calc( -{{SIZE}}{{UNIT}} / 2 );',

					'(tablet){{WRAPPER}}.trx-addons-info-list-stack-tablet.trx-addons-info-list-icon-top .trx-addons-info-list-item .trx-addons-info-list-item-inner' => 'margin-bottom: {{SIZE}}{{UNIT}}; margin-left: 0; margin-right: 0;',
					'(tablet){{WRAPPER}}.trx-addons-info-list-stack-tablet.trx-addons-info-list-icon-top .trx-addons-list-items' => 'margin-right: 0; margin-left: 0;',

					'(mobile){{WRAPPER}}.trx-addons-info-list-stack-mobile.trx-addons-info-list-icon-top .trx-addons-info-list-item .trx-addons-info-list-item-inner' => 'margin-bottom: {{SIZE}}{{UNIT}}; margin-left: 0; margin-right: 0;',
					'(mobile){{WRAPPER}}.trx-addons-info-list-stack-mobile.trx-addons-info-list-icon-top .trx-addons-list-items' => 'margin-right: 0; margin-left: 0;',
				),
			)
		);

		$this->add_control(
			'icon_position',
			array(
				'label'        => __( 'Position', 'trx_addons' ),
				'type'         => Controls_Manager::CHOOSE,
				'label_block'  => false,
				'toggle'       => false,
				'default'      => 'left',
				'options'      => array(
					'left'  => array(
						'title' => __( 'Left', 'trx_addons' ),
						'icon'  => 'eicon-h-align-left',
					),
					'top'   => array(
						'title' => __( 'Top', 'trx_addons' ),
						'icon'  => 'eicon-v-align-top',
					),
					'right' => array(
						'title' => __( 'Right', 'trx_addons' ),
						'icon'  => 'eicon-h-align-right',
					),
				),
				'prefix_class' => 'trx-addons-info-list-icon-',
			)
		);

		$this->add_control(
			'responsive_breakpoint',
			array(
				'label'        => __( 'Responsive Breakpoint', 'trx_addons' ),
				'type'         => Controls_Manager::SELECT,
				'label_block'  => false,
				'default'      => 'mobile',
				'options'      => array(
					''       => __( 'None', 'trx_addons' ),
					'tablet' => __( 'Tablet', 'trx_addons' ),
					'mobile' => __( 'Mobile', 'trx_addons' ),
				),
				'prefix_class' => 'trx-addons-info-list-stack-',
				'condition'    => array(
					'icon_position' => 'top',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function register_style_connector_controls() {
		/**
		 * Style Tab: Connector
		 */
		$this->start_controls_section(
			'section_connector_style',
			array(
				'label'     => __( 'Connector', 'trx_addons' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'connector' => 'yes',
				),
			)
		);

		$this->add_control(
			'connector_color',
			array(
				'label'     => __( 'Color', 'trx_addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .trx-addons-info-list-connector .trx-addons-info-list-icon-wrapper:before, {{WRAPPER}} .trx-addons-info-list-connector .trx-addons-info-list-icon-wrapper:after' => 'border-color: {{VALUE}};',
				),
				'condition' => array(
					'connector' => 'yes',
				),
			)
		);

		$this->add_control(
			'connector_style',
			array(
				'label'     => __( 'Style', 'trx_addons' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					'solid'  => __( 'Solid', 'trx_addons' ),
					'double' => __( 'Double', 'trx_addons' ),
					'dotted' => __( 'Dotted', 'trx_addons' ),
					'dashed' => __( 'Dashed', 'trx_addons' ),
				),
				'default'   => 'solid',
				'selectors' => array(
					'{{WRAPPER}}.trx-addons-info-list-icon-left .trx-addons-info-list-connector .trx-addons-info-list-icon-wrapper:before, {{WRAPPER}}.trx-addons-info-list-icon-left .trx-addons-info-list-connector .trx-addons-info-list-icon-wrapper:after' => 'border-right-style: {{VALUE}};',
					'{{WRAPPER}}.trx-addons-info-list-icon-right .trx-addons-info-list-connector .trx-addons-info-list-icon-wrapper:before, {{WRAPPER}}.trx-addons-info-list-icon-right .trx-addons-info-list-connector .trx-addons-info-list-icon-wrapper:after' => 'border-left-style: {{VALUE}};',
					'{{WRAPPER}}.trx-addons-info-list-icon-top .trx-addons-info-list-connector .trx-addons-info-list-icon-wrapper:before, {{WRAPPER}}.trx-addons-info-list-icon-top .trx-addons-info-list-connector .trx-addons-info-list-icon-wrapper:after' => 'border-top-style: {{VALUE}};',
					'(tablet){{WRAPPER}}.trx-addons-info-list-stack-tablet.trx-addons-info-list-icon-top .trx-addons-info-list-connector .trx-addons-info-list-icon-wrapper:before, {{WRAPPER}}.trx-addons-info-list-icon-top .trx-addons-info-list-connector .trx-addons-info-list-icon-wrapper:after' => 'border-right-style: {{VALUE}};',
					'(mobile){{WRAPPER}}.trx-addons-info-list-stack-mobile.trx-addons-info-list-icon-top .trx-addons-info-list-connector .trx-addons-info-list-icon-wrapper:before, {{WRAPPER}}.trx-addons-info-list-icon-top .trx-addons-info-list-connector .trx-addons-info-list-icon-wrapper:after' => 'border-right-style: {{VALUE}};',
				),
				'condition' => array(
					'connector' => 'yes',
				),
			)
		);

		$this->add_control(
			'connector_width',
			array(
				'label'     => __( 'Width', 'trx_addons' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => array(
					'size' => 1,
				),
				'range'     => array(
					'px' => array(
						'min' => 1,
						'max' => 20,
					),
				),
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors' => array(
					'{{WRAPPER}}.trx-addons-info-list-icon-left .trx-addons-info-list-connector .trx-addons-info-list-icon-wrapper:before, {{WRAPPER}}.trx-addons-info-list-icon-left .trx-addons-info-list-connector .trx-addons-info-list-icon-wrapper:after' => 'border-right-width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.trx-addons-info-list-icon-right .trx-addons-info-list-connector .trx-addons-info-list-icon-wrapper:before, {{WRAPPER}}.trx-addons-info-list-icon-right .trx-addons-info-list-icon-wrapper:after' => 'border-left-width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.trx-addons-info-list-icon-top .trx-addons-info-list-connector .trx-addons-info-list-icon-wrapper:before, {{WRAPPER}}.trx-addons-info-list-icon-top .trx-addons-info-list-connector .trx-addons-info-list-icon-wrapper:after' => 'border-top-width: {{SIZE}}{{UNIT}};',
					'(tablet){{WRAPPER}}.trx-addons-info-list-stack-tablet.trx-addons-info-list-icon-top .trx-addons-info-list-connector .trx-addons-info-list-icon-wrapper:before, {{WRAPPER}}.trx-addons-info-list-icon-top .trx-addons-info-list-connector .trx-addons-info-list-icon-wrapper:after' => 'border-right-width: {{SIZE}}{{UNIT}};',
					'(mobile){{WRAPPER}}.trx-addons-info-list-stack-mobile.trx-addons-info-list-icon-top .trx-addons-info-list-connector .trx-addons-info-list-icon-wrapper:before, {{WRAPPER}}.trx-addons-info-list-icon-top .trx-addons-info-list-connector .trx-addons-info-list-icon-wrapper:after' => 'border-right-width: {{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					'connector' => 'yes',
				),
			)
		);

		$this->add_control(
			'connector_gap',
			array(
				'label'     => __( 'Gap', 'trx_addons' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => array(
					'size' => 0,
				),
				'range'     => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'condition' => array(
					'connector' => 'yes',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function register_style_icon_controls() {
		/**
		 * Style Tab: Icon
		 */
		$this->start_controls_section(
			'section_icon_style',
			array(
				'label' => __( 'Icon', 'trx_addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'icon_vertical_align',
			array(
				'label'                => __( 'Vertical Align', 'trx_addons' ),
				'type'                 => Controls_Manager::CHOOSE,
				'label_block'          => false,
				'toggle'               => false,
				'default'              => 'middle',
				'options'              => array(
					'top'    => array(
						'title' => __( 'Top', 'trx_addons' ),
						'icon'  => 'eicon-v-align-top',
					),
					'middle' => array(
						'title' => __( 'Center', 'trx_addons' ),
						'icon'  => 'eicon-v-align-middle',
					),
					'bottom' => array(
						'title' => __( 'Bottom', 'trx_addons' ),
						'icon'  => 'eicon-v-align-bottom',
					),
				),
				'selectors_dictionary' => array(
					'top'    => 'flex-start',
					'middle' => 'center',
					'bottom' => 'flex-end',
				),
				'prefix_class'         => 'trx-addons-info-list-icon-vertical-',
				'condition'            => array(
					'icon_position' => array( 'left', 'right' ),
				),
			)
		);

		$this->add_responsive_control(
			'icon_size',
			array(
				'label'     => __( 'Size', 'trx_addons' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => array(
					'size' => 14,
				),
				'range'     => array(
					'px' => array(
						'min' => 6,
						'max' => 100,
					),
				),
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors' => array(
					'{{WRAPPER}} .trx-addons-list-items .trx-addons-info-list-icon' => 'font-size: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}};',	// height: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}};
					'{{WRAPPER}} .trx-addons-list-items .trx-addons-info-list-image img' => 'width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'icon_box_size',
			array(
				'label'     => __( 'Box Size', 'trx_addons' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => array(
					'size' => 14,
				),
				'range'     => array(
					'px' => array(
						'min' => 6,
						'max' => 200,
					),
				),
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors' => array(
					'{{WRAPPER}} .trx-addons-info-list-icon-wrapper' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',

					'{{WRAPPER}}.trx-addons-info-list-icon-left .trx-addons-info-list-connector .trx-addons-info-list-icon-wrapper:before' => 'left: calc( ( {{SIZE}}{{UNIT}} / 2 ) - ( {{connector_width.SIZE}}{{connector_width.UNIT}} / 2 ) ); bottom: calc( {{SIZE}}{{UNIT}} + {{connector_gap.SIZE}}{{connector_gap.UNIT}} );',
					'{{WRAPPER}}.trx-addons-info-list-icon-left .trx-addons-info-list-connector .trx-addons-info-list-icon-wrapper:after' => 'left: calc( ( {{SIZE}}{{UNIT}} / 2 ) - ( {{connector_width.SIZE}}{{connector_width.UNIT}} / 2 ) ); top: calc( {{SIZE}}{{UNIT}} + {{connector_gap.SIZE}}{{connector_gap.UNIT}} );',
					'{{WRAPPER}}.trx-addons-info-list-icon-right .trx-addons-info-list-connector .trx-addons-info-list-icon-wrapper:before' => 'right: calc( ( {{SIZE}}{{UNIT}} / 2 ) - ( {{connector_width.SIZE}}{{connector_width.UNIT}} / 2 ) ); bottom: calc( {{SIZE}}{{UNIT}} + {{connector_gap.SIZE}}{{connector_gap.UNIT}} );',
					'{{WRAPPER}}.trx-addons-info-list-icon-right .trx-addons-info-list-connector .trx-addons-info-list-icon-wrapper:after' => 'right: calc( ( {{SIZE}}{{UNIT}} / 2 ) - ( {{connector_width.SIZE}}{{connector_width.UNIT}} / 2 ) ); top: calc( {{SIZE}}{{UNIT}} + {{connector_gap.SIZE}}{{connector_gap.UNIT}} );',
					'{{WRAPPER}}.trx-addons-info-list-icon-top .trx-addons-info-list-connector .trx-addons-info-list-icon-wrapper:before' => 'top: calc( ( {{SIZE}}{{UNIT}} / 2 ) - ( {{connector_width.SIZE}}{{connector_width.UNIT}} / 2 ) ); right: calc( {{SIZE}}{{UNIT}} + {{connector_gap.SIZE}}{{connector_gap.UNIT}} );',
					'{{WRAPPER}}.trx-addons-info-list-icon-top .trx-addons-info-list-connector .trx-addons-info-list-icon-wrapper:after' => 'top: calc( ( {{SIZE}}{{UNIT}} / 2 ) - ( {{connector_width.SIZE}}{{connector_width.UNIT}} / 2 ) ); left: calc( {{SIZE}}{{UNIT}} + {{connector_gap.SIZE}}{{connector_gap.UNIT}} );',

					'(tablet){{WRAPPER}}.trx-addons-info-list-stack-tablet.trx-addons-info-list-icon-top .trx-addons-info-list-connector .trx-addons-info-list-icon-wrapper:before' => 'left: calc( ( {{SIZE}}{{UNIT}} / 2 ) - ( {{connector_width.SIZE}}{{connector_width.UNIT}} / 2 ) ); bottom: calc( {{SIZE}}{{UNIT}} + {{connector_gap.SIZE}}{{connector_gap.UNIT}} ); right: auto; top: auto;',
					'(tablet){{WRAPPER}}.trx-addons-info-list-stack-tablet.trx-addons-info-list-icon-top .trx-addons-info-list-connector .trx-addons-info-list-icon-wrapper:after' => 'left: calc( ( {{SIZE}}{{UNIT}} / 2 ) - ( {{connector_width.SIZE}}{{connector_width.UNIT}} / 2 ) ); top: calc( {{SIZE}}{{UNIT}} + {{connector_gap.SIZE}}{{connector_gap.UNIT}} );',

					'(mobile){{WRAPPER}}.trx-addons-info-list-stack-mobile.trx-addons-info-list-icon-top .trx-addons-info-list-connector .trx-addons-info-list-icon-wrapper:before' => 'left: calc( ( {{SIZE}}{{UNIT}} / 2 ) - ( {{connector_width.SIZE}}{{connector_width.UNIT}} / 2 ) ); bottom: calc( {{SIZE}}{{UNIT}} + {{connector_gap.SIZE}}{{connector_gap.UNIT}} ); right: auto; top: auto;',
					'(mobile){{WRAPPER}}.trx-addons-info-list-stack-mobile.trx-addons-info-list-icon-top .trx-addons-info-list-connector .trx-addons-info-list-icon-wrapper:after' => 'left: calc( ( {{SIZE}}{{UNIT}} / 2 ) - ( {{connector_width.SIZE}}{{connector_width.UNIT}} / 2 ) ); top: calc( {{SIZE}}{{UNIT}} + {{connector_gap.SIZE}}{{connector_gap.UNIT}} );',
				),
			)
		);

		$this->add_responsive_control(
			'icon_spacing',
			array(
				'label'     => __( 'Spacing', 'trx_addons' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => array(
					'size' => 8,
				),
				'range'     => array(
					'px' => array(
						'max' => 50,
					),
				),
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors' => array(
					'{{WRAPPER}}.trx-addons-info-list-icon-left .trx-addons-info-list-icon-wrapper' => 'margin-right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.trx-addons-info-list-icon-right .trx-addons-info-list-icon-wrapper' => 'margin-left: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.trx-addons-info-list-icon-top .trx-addons-info-list-icon-wrapper' => 'margin-bottom: {{SIZE}}{{UNIT}};',

					'(tablet){{WRAPPER}}.trx-addons-info-list-stack-tablet.trx-addons-info-list-icon-top .trx-addons-info-list-icon-wrapper' => 'margin-right: {{SIZE}}{{UNIT}}; margin-bottom: 0;',

					'(mobile){{WRAPPER}}.trx-addons-info-list-stack-mobile.trx-addons-info-list-icon-top .trx-addons-info-list-icon-wrapper' => 'margin-right: {{SIZE}}{{UNIT}}; margin-bottom: 0;',
				),
			)
		);

		$this->add_responsive_control(
			'icon_vertical_offset',
			array(
				'label'     => __( 'Vertical Offset', 'trx_addons' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => array(
					'size' => 0,
				),
				'range'     => array(
					'px' => array(
						'min' => -20,
						'max' => 20,
					),
					'em' => array(
						'min' => -2,
						'max' => 2,
						'step' => 0.1
					),
				),
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors' => array(
					// '{{WRAPPER}} .trx-addons-list-items trx-addons-info-list-icon-wrapper' => 'top: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.trx-addons-info-list-icon-left .trx-addons-info-list-icon-wrapper' => 'top: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.trx-addons-info-list-icon-right .trx-addons-info-list-icon-wrapper' => 'top: {{SIZE}}{{UNIT}};',
				),
				'condition'            => array(
					'icon_position' => array( 'left', 'right' ),
				),
			)
		);

		$this->add_control(
			'icon_horizontal_align',
			array(
				'label'                => __( 'Horizontal Align', 'trx_addons' ),
				'type'                 => Controls_Manager::CHOOSE,
				'label_block'          => false,
				'toggle'               => false,
				'options'              => array(
					'left'   => array(
						'title' => __( 'Left', 'trx_addons' ),
						'icon'  => 'eicon-h-align-left',
					),
					'center' => array(
						'title' => __( 'Center', 'trx_addons' ),
						'icon'  => 'eicon-h-align-center',
					),
					'right'  => array(
						'title' => __( 'Right', 'trx_addons' ),
						'icon'  => 'eicon-h-align-right',
					),
				),
				'default'              => 'center',
				'selectors_dictionary' => array(
					'left'   => 'flex-start',
					'center' => 'center',
					'right'  => 'flex-end',
				),
				'prefix_class'         => 'trx-addons-info-list-icon-horizontal-',
				'condition'            => array(
					'icon_position' => 'top',
				),
			)
		);

		$this->start_controls_tabs( 'tabs_icon_style' );

		$this->start_controls_tab(
			'tab_icon_normal',
			array(
				'label' => __( 'Normal', 'trx_addons' ),
			)
		);

		$this->add_control(
			'icon_color',
			array(
				'label'     => __( 'Icon Color', 'trx_addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .trx-addons-list-items .trx-addons-info-list-icon' => 'color: {{VALUE}};',
					'{{WRAPPER}} .trx-addons-list-items .trx-addons-info-list-icon svg' => 'fill: {{VALUE}};',
				),
				'global'    => [
					'default' => Global_Colors::COLOR_SECONDARY,
				],
			)
		);

		$this->add_control(
			'icon_bg_color',
			array(
				'label'     => __( 'Background Color', 'trx_addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .trx-addons-list-items .trx-addons-info-list-icon-wrapper' => 'background: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'        => 'icon_border',
				'label'       => __( 'Border', 'trx_addons' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .trx-addons-list-items .trx-addons-info-list-icon-wrapper',
			)
		);

		$this->add_responsive_control(
			'icon_border_radius',
			array(
				'label'      => __( 'Border Radius', 'trx_addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors'  => array(
					'{{WRAPPER}} .trx-addons-list-items .trx-addons-info-list-icon-wrapper, {{WRAPPER}} .trx-addons-list-items .trx-addons-info-list-image img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_icon_hover',
			array(
				'label' => __( 'Hover', 'trx_addons' ),
			)
		);

		$this->add_control(
			'icon_color_hover',
			array(
				'label'     => __( 'Color', 'trx_addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .trx-addons-list-items .trx-addons-info-list-item:hover .trx-addons-info-list-icon' => 'color: {{VALUE}};',
					'{{WRAPPER}} .trx-addons-list-items .trx-addons-info-list-item:hover .trx-addons-info-list-icon svg' => 'fill: {{VALUE}};',
				),
				'global'    => [
					'default' => Global_Colors::COLOR_SECONDARY,
				],
			)
		);

		$this->add_control(
			'icon_bg_color_hover',
			array(
				'label'     => __( 'Background Color', 'trx_addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .trx-addons-list-items .trx-addons-info-list-item:hover' => 'background: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'icon_border_color_hover',
			array(
				'label'     => __( 'Border Color', 'trx_addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .trx-addons-list-items .trx-addons-info-list-item:hover' => 'border-color: {{VALUE}};',
				),
				'global'    => [
					'default' => Global_Colors::COLOR_SECONDARY,
				],
			)
		);

		$this->add_control(
			'icon_hover_animation',
			array(
				'label' => __( 'Animation', 'trx_addons' ),
				'type'  => Controls_Manager::HOVER_ANIMATION,
			)
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
				'selector' => '{{WRAPPER}} .trx-addons-list-items .trx-addons-info-list-number',
			)
		);

		$this->end_controls_section();
	}

	protected function register_style_title_controls() {
		/**
		 * Style Tab: Title
		 */
		$this->start_controls_section(
			'section_content_style',
			array(
				'label' => __( 'Content', 'trx_addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'content_align',
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
					'{{WRAPPER}} .trx-addons-info-list-content-wrapper' => 'text-align: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'content_padding',
			array(
				'label'      => __( 'Padding', 'trx_addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors'  => array(
					'{{WRAPPER}} .trx-addons-info-list-content-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'show_separator',
			[
				'label'     => esc_html__( 'Separator', 'trx_addons' ),
				'type'      => Controls_Manager::SWITCHER,
				'label_off' => esc_html__( 'Hide', 'trx_addons' ),
				'label_on'  => esc_html__( 'Show', 'trx_addons' ),
				'default'   => '',
				'separator' => 'before',
				'condition' => [
					'icon_position!' => 'top',
				],
			]
		);

		$this->add_control(
			'separator_color',
			[
				'label'     => esc_html__( 'Color', 'trx_addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#e1e8ed',
				'selectors' => [
					'{{WRAPPER}} .trx-addons-info-list-item:not(:last-child) .trx-addons-info-list-content-wrapper' => 'border-bottom-color: {{VALUE}}',
				],
				'condition' => [
					'icon_position!'  => 'top',
					'show_separator!' => '',
				],
			]
		);

		$this->add_control(
			'separator_style',
			array(
				'label'     => __( 'Style', 'trx_addons' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					'solid'  => __( 'Solid', 'trx_addons' ),
					'double' => __( 'Double', 'trx_addons' ),
					'dotted' => __( 'Dotted', 'trx_addons' ),
					'dashed' => __( 'Dashed', 'trx_addons' ),
				),
				'default'   => 'solid',
				'condition' => array(
					'icon_position!'  => 'top',
					'show_separator!' => '',
				),
				'selectors' => array(
					'{{WRAPPER}} .trx-addons-info-list-item:not(:last-child) .trx-addons-info-list-content-wrapper' => 'border-bottom-style: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'separator_size',
			[
				'label'     => esc_html__( 'Size', 'trx_addons' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => array(
					'size' => 1,
				),
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 20,
					],
				],
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'condition' => [
					'icon_position!'  => 'top',
					'show_separator!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .trx-addons-info-list-item:not(:last-child) .trx-addons-info-list-content-wrapper' => 'border-bottom-width: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'title_heading',
			array(
				'label'     => __( 'Title', 'trx_addons' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'title_color',
			array(
				'label'     => __( 'Color', 'trx_addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .trx-addons-info-list-title' => 'color: {{VALUE}};',
				),
				'global'    => [
					'default' => Global_Colors::COLOR_PRIMARY,
				],
			)
		);

		$this->add_control(
			'title_color_hover',
			array(
				'label'     => __( 'Hover Color', 'trx_addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .trx-addons-info-list-item:hover .trx-addons-info-list-title' => 'color: {{VALUE}};',
				),
				'global'    => [
					'default' => Global_Colors::COLOR_PRIMARY,
				],
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'title_typography',
				'label'    => __( 'Typography', 'trx_addons' ),
				'global'   => [
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				],
				'selector' => '{{WRAPPER}} .trx-addons-info-list-title',
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name'      => 'title_text_shadow',
				'label'     => __( 'Text Shadow', 'trx_addons' ),
				'selector'  => '{{WRAPPER}} .trx-addons-info-list-title',
			)
		);

		$this->add_responsive_control(
			'title_margin',
			array(
				'label'      => __( 'Spacing', 'trx_addons' ),
				'type'       => Controls_Manager::SLIDER,
				'range'      => array(
					'px' => array(
						'min'  => 0,
						'max'  => 50,
						'step' => 1,
					),
					'%'  => array(
						'min'  => 0,
						'max'  => 30,
						'step' => 1,
					),
				),
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors'  => array(
					'{{WRAPPER}} .trx-addons-info-list-title' => 'margin-bottom: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->add_control(
			'description_heading',
			array(
				'label'     => __( 'Description', 'trx_addons' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'description_color',
			array(
				'label'     => __( 'Color', 'trx_addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .trx-addons-info-list-description' => 'color: {{VALUE}};',
				),
				'global'    => [
					'default' => Global_Colors::COLOR_TEXT,
				],
			)
		);

		$this->add_control(
			'description_color_hover',
			array(
				'label'     => __( 'Hover Color', 'trx_addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .trx-addons-info-list-item:hover .trx-addons-info-list-description' => 'color: {{VALUE}};',
				),
				'global'    => [
					'default' => Global_Colors::COLOR_TEXT,
				],
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'description_typography',
				'label'    => __( 'Typography', 'trx_addons' ),
				'global'   => [
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				],
				'selector' => '{{WRAPPER}} .trx-addons-info-list-description',
			)
		);

		$this->end_controls_section();
	}

	protected function register_style_button_controls() {
		/**
		 * Style Tab: Button
		 * -------------------------------------------------
		 */
		$this->start_controls_section(
			'section_info_box_button_style',
			array(
				'label' => __( 'Button', 'trx_addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'button_margin',
			array(
				'label'      => __( 'Spacing', 'trx_addons' ),
				'type'       => Controls_Manager::SLIDER,
				'range'      => array(
					'px' => array(
						'min'  => 0,
						'max'  => 50,
						'step' => 1,
					),
					'%'  => array(
						'min'  => 0,
						'max'  => 30,
						'step' => 1,
					),
				),
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors'  => array(
					'{{WRAPPER}} .trx-addons-info-list-button-wrapper' => 'margin-top: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->start_controls_tabs( 'tabs_button_style' );

		$this->start_controls_tab(
			'tab_button_normal',
			array(
				'label' => __( 'Normal', 'trx_addons' ),
			)
		);

		$this->add_control(
			'button_bg_color_normal',
			array(
				'label'     => __( 'Background Color', 'trx_addons' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => [
					'default' => Global_Colors::COLOR_ACCENT,
				],
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .trx-addons-info-list-button' => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'button_text_color_normal',
			array(
				'label'     => __( 'Text Color', 'trx_addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .trx-addons-info-list-button'     => 'color: {{VALUE}}',
					'{{WRAPPER}} .trx-addons-info-list-button svg' => 'fill: {{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'        => 'button_border_normal',
				'label'       => __( 'Border', 'trx_addons' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .trx-addons-info-list-button',
			)
		);

		$this->add_responsive_control(
			'button_border_radius',
			array(
				'label'      => __( 'Border Radius', 'trx_addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors'  => array(
					'{{WRAPPER}} .trx-addons-info-list-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'button_typography',
				'label'    => __( 'Typography', 'trx_addons' ),
				'global'   => [
					'default' => Global_Typography::TYPOGRAPHY_ACCENT,
				],
				'selector' => '{{WRAPPER}} .trx-addons-info-list-button',
			)
		);

		$this->add_responsive_control(
			'button_padding',
			array(
				'label'      => __( 'Padding', 'trx_addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors'  => array(
					'{{WRAPPER}} .trx-addons-info-list-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'button_box_shadow',
				'selector' => '{{WRAPPER}} .trx-addons-info-list-button',
			)
		);

		$this->add_control(
			'info_box_button_icon_heading',
			array(
				'label'     => __( 'Button Icon', 'trx_addons' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_responsive_control(
			'button_icon_margin',
			array(
				'label'       => __( 'Margin', 'trx_addons' ),
				'type'        => Controls_Manager::DIMENSIONS,
				'size_units'  => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors'   => array(
					'{{WRAPPER}} .trx-addons-info-list-button .trx-addons-button-icon' => 'margin-top: {{TOP}}{{UNIT}}; margin-left: {{LEFT}}{{UNIT}}; margin-right: {{RIGHT}}{{UNIT}}; margin-bottom: {{BOTTOM}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_button_hover',
			array(
				'label' => __( 'Hover', 'trx_addons' ),
			)
		);

		$this->add_control(
			'button_bg_color_hover',
			array(
				'label'     => __( 'Background Color', 'trx_addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .trx-addons-info-list-button:hover' => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'button_text_color_hover',
			array(
				'label'     => __( 'Text Color', 'trx_addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .trx-addons-info-list-button:hover' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'button_border_color_hover',
			array(
				'label'     => __( 'Border Color', 'trx_addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .trx-addons-info-list-button:hover' => 'border-color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'button_animation',
			array(
				'label' => __( 'Animation', 'trx_addons' ),
				'type'  => Controls_Manager::HOVER_ANIMATION,
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'button_box_shadow_hover',
				'selector' => '{{WRAPPER}} .trx-addons-info-list-button:hover',
			)
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	
	/*-----------------------------------------------------------------------------------*/
	/*	RENDER
	/*-----------------------------------------------------------------------------------*/

	/**
	 * Render info list widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @access protected
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		$this->add_render_attribute(
			array(
				'info-list'        => array(
					'class' => array(
						'trx-addons-info-list-container',
						'trx-addons-list-container',
					),
				),
				'info-list-items'  => array(
					'class' => 'trx-addons-list-items',
				),
				'list-item'        => array(
					'class' => 'trx-addons-info-list-item',
				),
				'icon'             => array(
					'class' => array( 'trx-addons-info-list-icon', 'trx-addons-icon' ),
				),
				'info-list-button' => array(
					'class' => array(
						'trx-addons-info-list-button',
						'elementor-button',
					),
				),
			)
		);

		if ( 'yes' === $settings['connector'] ) {
			$this->add_render_attribute( 'info-list', 'class', 'trx-addons-info-list-connector' );
			if ( 'yes' === $settings['corner_lines'] ) {
				$this->add_render_attribute( 'info-list', 'class', 'trx-addons-info-list-corners-hide' );
			}
		}

		if ( $settings['button_animation'] ) {
			$this->add_render_attribute( 'info-list-button', 'class', 'elementor-animation-' . $settings['button_animation'] );
		}

		$i = 1;
		?>
		<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'info-list' ) ); ?>>
			<ul <?php echo wp_kses_post( $this->get_render_attribute_string( 'info-list-items' ) ); ?>>
				<?php foreach ( $settings['list_items'] as $index => $item ) : ?>
					<?php if ( $item['text'] || $item['description'] ) { ?>
						<li <?php echo wp_kses_post( $this->get_render_attribute_string( 'list-item' ) ); ?>>
							<?php
								$text_key = $this->get_repeater_setting_key( 'text', 'list_items', $index );
								$this->add_render_attribute( $text_key, 'class', 'trx-addons-info-list-title' );
								$this->add_inline_editing_attributes( $text_key, 'none' );

								$description_key = $this->get_repeater_setting_key( 'description', 'list_items', $index );
								$this->add_render_attribute( $description_key, 'class', 'trx-addons-info-list-description' );
								$this->add_inline_editing_attributes( $description_key, 'basic' );

								$button_key = $this->get_repeater_setting_key( 'button-wrap', 'list_items', $index );
								$this->add_render_attribute( $button_key, 'class', 'trx-addons-info-list-button-wrapper trx-addons-info-list-button-icon-' . $item['button_icon_position'] );

								if ( ! empty( $item['link']['url'] ) ) {
									$link_key = 'link_' . $i;

									$this->add_link_attributes( $link_key, $item['link'] );
								}
							?>
							<?php if ( ! empty( $item['link']['url'] ) && 'box' === $item['link_type'] ) { ?>
								<a <?php echo wp_kses_post( $this->get_render_attribute_string( $link_key ) ); ?>>
							<?php }  ?>
								<div class="trx-addons-info-list-item-inner">
									<?php $this->render_infolist_icon( $item, $i ); ?>
									<div class="trx-addons-info-list-content-wrapper">
										<?php
										if ( $item['text'] ) {
											$title_tag = TrxAddonsUtils::validate_html_tag( $settings['title_html_tag'] );
											?>
											<<?php echo esc_html( $title_tag ); ?> <?php echo wp_kses_post( $this->get_render_attribute_string( $text_key ) ); ?>>
											<?php if ( ! empty( $item['link']['url'] ) && 'title' === $item['link_type'] ) { ?>
												<a <?php echo wp_kses_post( $this->get_render_attribute_string( $link_key ) ); ?>>
											<?php } ?>
												<?php echo wp_kses_post( $item['text'] ); ?>
											<?php if ( ! empty( $item['link']['url'] ) && 'title' === $item['link_type'] ) { ?>
												</a>
											<?php } ?>
											</<?php echo esc_html( $title_tag ); ?>>
										<?php } ?>
										<?php
										if ( $item['description'] ) {
											?>
											<div <?php echo wp_kses_post( $this->get_render_attribute_string( $description_key ) ); ?>>
												<?php echo $this->parse_text_editor( $item['description'] ); //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
											</div>
											<?php
										}
										?>
										<?php if ( 'button' === $item['link_type'] && ! empty( $item['link']['url'] ) ) { ?>
											<div <?php echo wp_kses_post( $this->get_render_attribute_string( $button_key ) ); ?>>
												<a <?php echo wp_kses_post( $this->get_render_attribute_string( $link_key ) ); ?>>
													<span <?php echo wp_kses_post( $this->get_render_attribute_string( 'info-list-button' ) ); ?>>
														<?php $this->render_infolist_button_icon( $item ); ?>

														<?php if ( ! empty( $item['button_text'] ) ) { ?>
															<span <?php echo wp_kses_post( $this->get_render_attribute_string( 'button_text' ) ); ?>>
																<?php echo wp_kses_post( $item['button_text'] ); ?>
															</span>
														<?php } ?>
													</span>
												</a>
											</div>
										<?php } ?>
									</div>
								</div>
							<?php if ( ! empty( $item['link']['url'] ) && 'box' === $item['link_type'] ) { ?>
								</a>
							<?php } ?>
						</li>
					<?php } ?>
					<?php
					$i++;
				endforeach;
				?>
			</ul>
		</div>
		<?php
	}

	/**
	 * Render a button icon output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @access protected
	 */
	protected function render_infolist_button_icon( $item ) {
		$settings = $this->get_settings_for_display();

		$migration_allowed = Icons_Manager::is_migration_allowed();

		// add old default
		if ( ! isset( $item['button_icon'] ) && ! $migration_allowed ) {
			$item['button_icon'] = '';
		}

		$migrated = isset( $item['__fa4_migrated']['icon'] );
		$is_new   = empty( $item['button_icon'] ) && $migration_allowed;

		if ( ! empty( $item['button_icon'] ) || ( ! empty( $item['selected_icon']['value'] ) && $is_new ) ) {
			?>
			<span class="trx-addons-button-icon trx-addons-icon">
				<?php
				if ( $is_new || $migrated ) {
					Icons_Manager::render_icon( $item['selected_icon'], array( 'aria-hidden' => 'true' ) );
				} else {
					?>
					<i class="<?php echo esc_attr( $item['button_icon'] ); ?>" aria-hidden="true"></i>
					<?php
				}
				?>
			</span>
			<?php
		}
	}

	/**
	 * Render an icon output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @access protected
	 */
	protected function render_infolist_icon( $item, $i ) {
		$settings = $this->get_settings_for_display();

		$fallback_defaults = array(
			'fa fa-check',
			'fa fa-times',
			'fa fa-dot-circle-o',
		);

		$migration_allowed = Icons_Manager::is_migration_allowed();

		// add old default
		if ( ! isset( $item['list_icon'] ) && ! $migration_allowed ) {
			$item['list_icon'] = isset( $fallback_defaults[ $index ] ) ? $fallback_defaults[ $index ] : 'fa fa-check';
		}

		$migrated = isset( $item['__fa4_migrated']['icon'] );
		$is_new   = empty( $item['list_icon'] ) && $migration_allowed;

		if ( 'none' !== $item['trx_addons_icon_type'] ) {
			$icon_wrap_key = $this->get_repeater_setting_key( 'icon_wrap', 'list_items', $i );
			$icon_key      = $this->get_repeater_setting_key( 'icon', 'list_items', $i );

			if ( '' !== $settings['icon_hover_animation'] ) {
				$icon_animation = 'elementor-animation-' . $settings['icon_hover_animation'];
			} else {
				$icon_animation = '';
			}

			$this->add_render_attribute( $icon_wrap_key, 'class', 'trx-addons-info-list-icon-wrapper' );
			$this->add_render_attribute(
				$icon_key,
				'class',
				array(
					'trx-addons-info-list-icon',
					'trx-addons-icon',
					esc_attr( $icon_animation ),
				)
			);
			?>
			<div <?php echo wp_kses_post( $this->get_render_attribute_string( $icon_wrap_key ) ); ?>>
				<?php
				if ( 'icon' === $item['trx_addons_icon_type'] ) {
					if ( ! empty( $item['list_icon'] ) || ( ! empty( $item['icon']['value'] ) && $is_new ) ) {
						?>
						<span <?php echo wp_kses_post( $this->get_render_attribute_string( $icon_key ) ); ?>>
							<?php
							if ( $is_new || $migrated ) {
								Icons_Manager::render_icon( $item['icon'], array( 'aria-hidden' => 'true' ) );
							} else {
								?>
								<i class="<?php echo esc_attr( $item['list_icon'] ); ?>" aria-hidden="true"></i>
								<?php
							}
							?>
						</span>
						<?php
					}
				} elseif ( 'image' === $item['trx_addons_icon_type'] ) {
					$image_url = Group_Control_Image_Size::get_attachment_image_src( $item['list_image']['id'], 'thumbnail', $settings );

					if ( $image_url ) {
						?>
						<span class="trx-addons-info-list-image <?php echo esc_attr( $icon_animation ); ?>"><img src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( Control_Media::get_image_alt( $item['list_image'] ) ); ?>"></span>
						<?php
					} else {
						?>
						<img src="<?php echo esc_url( $item['list_image']['url'] ); ?>">
						<?php
					}
				} elseif ( 'text' === $item['trx_addons_icon_type'] ) {
					$text_tag = TrxAddonsUtils::validate_html_tag( $settings['icon_text_html_tag'] );
					?>
					<<?php echo esc_html( $text_tag ); ?> class="trx-addons-info-list-icon trx-addons-info-list-number <?php echo esc_attr( $icon_animation ); ?>">
						<?php echo wp_kses_post( $item['icon_text'] ); ?>
					</<?php echo esc_html( $text_tag ); ?>>
					<?php
				}
				?>
			</div>
			<?php
		}
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
		view.addRenderAttribute(
			'info-list',
			{
				'class': [ 'trx-addons-info-list-container', 'trx-addons-list-container' ],
			}
		);

		if ( settings.connector == 'yes' ) {
			view.addRenderAttribute( 'info-list', 'class', 'trx-addons-info-list-connector' );  
			if ( settings.corner_lines == 'yes' ) {
			view.addRenderAttribute( 'info-list', 'class', 'trx-addons-info-list-corners-hide' );
			}
		}
			   
		var iconsHTML = {},
			migrated = {},
			buttonIconHTML = {},
			buttonMigrated = {};
		#>
		<div {{{ view.getRenderAttributeString( 'info-list' ) }}}>
			<ul class="trx-addons-list-items">
				<# var i = 1; #>
				<# _.each( settings.list_items, function( item, index ) { #>
					<#
						var text_key = 'list_items.' + (i - 1) + '.text';
						var description_key = 'list_items.' + (i - 1) + '.description';

						view.addInlineEditingAttributes( text_key );

						view.addRenderAttribute( description_key, 'class', 'trx-addons-info-list-description' );
						view.addInlineEditingAttributes( description_key );
					#>
					<# if ( item.text || item.description ) { #>
						<li class="trx-addons-info-list-item">
							<# if ( item.link.url != '' && item.link_type == 'box' ) { #>
								<a href="{{ _.escape( item.link.url ) }}">
							<# } #>
								<div class="trx-addons-info-list-item-inner">
									<# if ( item.trx_addons_icon_type != 'none' ) { #>
										<div class="trx-addons-info-list-icon-wrapper">
											<# if ( item.trx_addons_icon_type == 'icon' ) { #>
												<# if ( item.list_icon || item.icon.value ) { #>
													<span class="trx-addons-info-list-icon trx-addons-icon elementor-animation-{{ settings.icon_hover_animation }}" aria-hidden="true">
													<#
														iconsHTML[ index ] = elementor.helpers.renderIcon( view, item.icon, { 'aria-hidden': true }, 'i', 'object' );
														migrated[ index ] = elementor.helpers.isIconMigrated( item, 'icon' );
														if ( iconsHTML[ index ] && iconsHTML[ index ].rendered && ( ! item.list_icon || migrated[ index ] ) ) { #>
															{{{ iconsHTML[ index ].value }}}
														<# } else { #>
															<i class="{{ item.list_icon }}" aria-hidden="true"></i>
														<# }
													#>
													</span>
												<# } #>
											<# } else if ( item.trx_addons_icon_type == 'image' ) { #>
												<span class="trx-addons-info-list-image elementor-animation-{{ settings.icon_hover_animation }}">
													<#
													var image = {
														id: item.list_image.id,
														url: item.list_image.url,
														size: settings.thumbnail_size,
														dimension: settings.thumbnail_custom_dimension,
														model: view.getEditModel()
													};
													var image_url = elementor.imagesManager.getImageUrl( image );
													#>
													<img src="{{ _.escape( image_url ) }}" />
												</span>
											<# } else if ( item.trx_addons_icon_type == 'text' ) { #>
												<# var textHTMLTag = elementor.helpers.validateHTMLTag( settings.icon_text_html_tag ); #>
												<{{{ textHTMLTag }}} class="trx-addons-info-list-icon trx-addons-info-list-number elementor-animation-{{ settings.icon_hover_animation }}">
													{{ item.icon_text }}
												</{{{ textHTMLTag }}}>
											<# } #>
										</div>
									<# } #>
									<div class="trx-addons-info-list-content-wrapper">
										<# if ( item.text ) { #>
											<# var titleHTMLTag = elementor.helpers.validateHTMLTag( settings.title_html_tag ); #>
											<{{{ titleHTMLTag }}} class="trx-addons-info-list-title">
												<# if ( item.link.url != '' && item.link_type == 'title' ) { #>
													<a href="{{ _.escape( item.link.url ) }}">
												<# } #>
												<span {{{ view.getRenderAttributeString( 'list_items.' + (i - 1) + '.text' ) }}}>
												{{{ item.text }}}
												</span>
												<# if ( item.link.url != '' && item.link_type == 'title' ) { #>
													</a>
												<# } #>
											</{{{ titleHTMLTag }}}>
										<# } #>
										<# if ( item.description ) { #>
										<div {{{ view.getRenderAttributeString( description_key ) }}}>
											{{{ item.description }}}
										</div>
										<# } #>
										<# if ( item.link.url != '' && item.link_type == 'button' ) { #>
											<div class="trx-addons-info-list-button-wrapper trx-addons-info-list-button-icon-{{ item.button_icon_position }}">
												<a href="{{ _.escape( item.link.url ) }}">
													<span class="trx-addons-info-list-button elementor-button elementor-animation-{{ settings.button_animation }}">
														<#
															buttonIconHTML[ index ] = elementor.helpers.renderIcon( view, item.selected_icon, { 'aria-hidden': true }, 'i', 'object' );
															buttonMigrated[ index ] = elementor.helpers.isIconMigrated( item, 'selected_icon' );
														#>
														<# if ( buttonIconHTML[ index ] && buttonIconHTML[ index ].rendered && ( ! item.button_icon || buttonMigrated[ index ] ) ) { #>
															<span class="trx-addons-button-icon trx-addons-icon">
																{{{ buttonIconHTML[ index ].value }}}
															</span>
														<# } else if ( item.button_icon ) { #>
															<span class="trx-addons-button-icon trx-addons-icon">
																<i class="{{ item.button_icon }}" aria-hidden="true"></i>
															</span>
														<# } #>

														<# if ( item.button_text != '' ) { #>
															<span class="trx-addons-button-text">
																{{{ item.button_text }}}
															</span>
														<# } #>
													</span>
												</a>
											</div>
										<# } #>
									</div>
								</div>
							<# if ( item.link_type == 'box' ) { #>
								</a>
							<# } #>
						</li>
					<# } #>
				<# i++ } ); #>
			</ul>
		</div>
		<?php
	}
}
