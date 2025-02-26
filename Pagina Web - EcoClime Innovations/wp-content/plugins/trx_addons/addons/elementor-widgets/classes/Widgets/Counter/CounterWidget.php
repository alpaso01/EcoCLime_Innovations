<?php
/**
 * Counter Widget
 *
 * @package ThemeREX Addons
 * @since v2.30.0
 */

namespace TrxAddons\ElementorWidgets\Widgets\Counter;

use TrxAddons\ElementorWidgets\BaseWidget;
use TrxAddons\ElementorWidgets\Utils as TrxAddonsUtils;

// Elementor Classes.
use Elementor\Controls_Manager;
use Elementor\Utils;
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
 * Counter Widget
 */
class CounterWidget extends BaseWidget {

	/**
	 * Register widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 */
	protected function register_controls() {
		/* Content Tab */
		$this->register_content_counter_controls();
		$this->register_content_help_docs_controls();

		/* Style Tab */
		$this->register_style_counter_icon_controls();
		$this->register_style_counter_number_controls();
		$this->register_style_number_prefix_controls();
		$this->register_style_number_suffix_controls();
		$this->register_style_title_controls();
	}

	/*-----------------------------------------------------------------------------------*/
	/*	CONTENT TAB
	/*-----------------------------------------------------------------------------------*/

	protected function register_content_counter_controls() {

		/**
		 * Content Tab: Counter
		 */
		$this->start_controls_section(
			'section_counter',
			[
				'label'                 => __( 'Counter', 'trx_addons' ),
			]
		);

		$this->add_control(
			'starting_number',
			[
				'label'                 => __( 'Starting Number', 'trx_addons' ),
				'type'                  => Controls_Manager::NUMBER,
				'dynamic'               => [
					'active'   => true,
				],
				'default'               => 0,
			]
		);

		$this->add_control(
			'ending_number',
			[
				'label'                 => __( 'Ending Number', 'trx_addons' ),
				'type'                  => Controls_Manager::NUMBER,
				'dynamic'               => [
					'active'   => true,
				],
				'default'               => 250,
			]
		);

		$this->add_control(
			'number_prefix',
			[
				'label'                 => __( 'Number Prefix', 'trx_addons' ),
				'type'                  => Controls_Manager::TEXT,
				'dynamic'               => [
					'active'   => true,
				],
			]
		);

		$this->add_control(
			'number_suffix',
			[
				'label'                 => __( 'Number Suffix', 'trx_addons' ),
				'type'                  => Controls_Manager::TEXT,
				'dynamic'               => [
					'active'   => true,
				],
			]
		);

		$this->add_control(
			'num_divider',
			[
				'label'                 => __( 'Number Divider', 'trx_addons' ),
				'type'                  => Controls_Manager::SWITCHER,
				'default'               => '',
				'label_on'              => __( 'On', 'trx_addons' ),
				'label_off'             => __( 'Off', 'trx_addons' ),
				'return_value'          => 'yes',
				'condition'             => [
					'counter_layout'    => [
						'layout-1',
						'layout-3',
						'layout-5',
						'layout-6',
					],
				],
			]
		);

		$this->add_control(
			'thousand_separator',
			[
				'label'     => __( 'Thousand Separator', 'trx_addons' ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'label_on'  => __( 'Show', 'trx_addons' ),
				'label_off' => __( 'Hide', 'trx_addons' ),
			]
		);

		$this->add_control(
			'thousand_separator_char',
			[
				'label'     => __( 'Separator', 'trx_addons' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => [
					''  => __( 'Comma', 'trx_addons' ),
					'.' => __( 'Dot', 'trx_addons' ),
					' ' => __( 'Space', 'trx_addons' ),
				],
				'condition' => [
					'thousand_separator' => 'yes',
				],
			]
		);

		$this->add_control(
			'number_html_tag',
			[
				'label'                => __( 'Number HTML Tag', 'trx_addons' ),
				'type'                 => Controls_Manager::SELECT,
				'default'              => 'div',
				'options'              => [
					'h1'     => __( 'H1', 'trx_addons' ),
					'h2'     => __( 'H2', 'trx_addons' ),
					'h3'     => __( 'H3', 'trx_addons' ),
					'h4'     => __( 'H4', 'trx_addons' ),
					'h5'     => __( 'H5', 'trx_addons' ),
					'h6'     => __( 'H6', 'trx_addons' ),
					'div'    => __( 'div', 'trx_addons' ),
					'span'   => __( 'span', 'trx_addons' ),
					'p'      => __( 'p', 'trx_addons' ),
				],
			]
		);

		$this->add_control(
			'icon_heading',
			[
				'label'                 => __( 'Icon', 'trx_addons' ),
				'type'                  => Controls_Manager::HEADING,
				'separator'             => 'before',
			]
		);

		$this->add_control(
			'trx_addons_icon_type',
			[
				'label'                 => esc_html__( 'Icon Type', 'trx_addons' ),
				'type'                  => Controls_Manager::CHOOSE,
				'label_block'           => false,
				'toggle'                => false,
				'options'               => [
					'none'        => [
						'title'   => esc_html__( 'None', 'trx_addons' ),
						'icon'    => 'eicon-ban',
					],
					'icon'        => [
						'title'   => esc_html__( 'Icon', 'trx_addons' ),
						'icon'    => 'eicon-star',
					],
					'image'       => [
						'title'   => esc_html__( 'Image', 'trx_addons' ),
						'icon'    => 'eicon-image-bold',
					],
				],
				'default'               => 'none',
			]
		);

		$this->add_control(
			'icon',
			[
				'label'                 => __( 'Icon', 'trx_addons' ),
				'type'                  => Controls_Manager::ICONS,
				'fa4compatibility'      => 'counter_icon',
				'default'               => [
					'value'     => 'fas fa-star',
					'library'   => 'fa-solid',
				],
				'condition'             => [
					'trx_addons_icon_type'  => 'icon',
				],
			]
		);

		$this->add_control(
			'icon_image',
			[
				'label'                 => __( 'Image', 'trx_addons' ),
				'type'                  => Controls_Manager::MEDIA,
				'default'               => [
					'url' => Utils::get_placeholder_image_src(),
				],
				'condition'             => [
					'trx_addons_icon_type'  => 'image',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name'                  => 'image', // Usage: '{name}_size' and '{name}_custom_dimension', in this case 'image_size' and 'image_custom_dimension'.
				'default'               => 'full',
				'separator'             => 'none',
				'condition'             => [
					'trx_addons_icon_type'  => 'image',
				],
			]
		);

		$this->add_control(
			'icon_divider',
			[
				'label'                 => __( 'Icon Divider', 'trx_addons' ),
				'type'                  => Controls_Manager::SWITCHER,
				'default'               => 'no',
				'label_on'              => __( 'On', 'trx_addons' ),
				'label_off'             => __( 'Off', 'trx_addons' ),
				'return_value'          => 'yes',
				'condition'             => [
					'trx_addons_icon_type!'     => 'none',
					'counter_layout'    => [ 'layout-1', 'layout-2' ],
				],
			]
		);

		$this->add_control(
			'title_heading',
			[
				'label'                 => __( 'Title', 'trx_addons' ),
				'type'                  => Controls_Manager::HEADING,
				'separator'             => 'before',
			]
		);

		$this->add_control(
			'counter_title',
			[
				'label'                 => __( 'Title', 'trx_addons' ),
				'type'                  => Controls_Manager::TEXT,
				'dynamic'               => [
					'active'   => true,
				],
				'default'               => __( 'Counter Title', 'trx_addons' ),
			]
		);

		$this->add_control(
			'title_html_tag',
			[
				'label'                => __( 'Title HTML Tag', 'trx_addons' ),
				'type'                 => Controls_Manager::SELECT,
				'default'              => 'div',
				'options'              => [
					'h1'     => __( 'H1', 'trx_addons' ),
					'h2'     => __( 'H2', 'trx_addons' ),
					'h3'     => __( 'H3', 'trx_addons' ),
					'h4'     => __( 'H4', 'trx_addons' ),
					'h5'     => __( 'H5', 'trx_addons' ),
					'h6'     => __( 'H6', 'trx_addons' ),
					'div'    => __( 'div', 'trx_addons' ),
					'span'   => __( 'span', 'trx_addons' ),
					'p'      => __( 'p', 'trx_addons' ),
				],
				'condition'             => [
					'counter_title!'    => '',
				],
			]
		);

		$this->add_control(
			'counter_subtitle',
			[
				'label'                 => __( 'Subtitle', 'trx_addons' ),
				'type'                  => Controls_Manager::TEXT,
				'dynamic'               => [
					'active'   => true,
				],
				'default'               => '',
			]
		);

		$this->add_control(
			'subtitle_html_tag',
			[
				'label'                => __( 'Subtitle HTML Tag', 'trx_addons' ),
				'type'                 => Controls_Manager::SELECT,
				'default'              => 'div',
				'options'              => [
					'h1'     => __( 'H1', 'trx_addons' ),
					'h2'     => __( 'H2', 'trx_addons' ),
					'h3'     => __( 'H3', 'trx_addons' ),
					'h4'     => __( 'H4', 'trx_addons' ),
					'h5'     => __( 'H5', 'trx_addons' ),
					'h6'     => __( 'H6', 'trx_addons' ),
					'div'    => __( 'div', 'trx_addons' ),
					'span'   => __( 'span', 'trx_addons' ),
					'p'      => __( 'p', 'trx_addons' ),
				],
				'condition'             => [
					'counter_subtitle!' => '',
				],
			]
		);

		$layouts = array();
		for ( $x = 1; $x <= 10; $x++ ) {
			$layouts[ 'layout-' . $x ] = __( 'Layout', 'trx_addons' ) . ' ' . $x;
		}

		$this->add_control(
			'counter_layout',
			[
				'label'                => __( 'Layout', 'trx_addons' ),
				'type'                 => Controls_Manager::SELECT,
				'default'              => 'layout-1',
				'options'              => $layouts,
				'separator'            => 'before',
			]
		);

		$this->add_control(
			'counter_speed',
			[
				'label'                 => __( 'Counter Speed', 'trx_addons' ),
				'type'                  => Controls_Manager::SLIDER,
				'default'               => [ 'size' => 1500 ],
				'range'                 => [
					'px' => [
						'min'   => 100,
						'max'   => 2000,
						'step'  => 1,
					],
				],
				'size_units'            => '',
			]
		);

		$this->add_responsive_control(
			'counter_align',
			[
				'label'                 => __( 'Alignment', 'trx_addons' ),
				'type'                  => Controls_Manager::CHOOSE,
				'options'               => [
					'left'      => [
						'title' => __( 'Left', 'trx_addons' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center'    => [
						'title' => __( 'Center', 'trx_addons' ),
						'icon'  => 'eicon-text-align-center',
					],
					'right'     => [
						'title' => __( 'Right', 'trx_addons' ),
						'icon'  => 'eicon-text-align-right',
					],
					'justify'   => [
						'title' => __( 'Justified', 'trx_addons' ),
						'icon'  => 'eicon-text-align-justify',
					],
				],
				'default'               => 'center',
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-counter-container'   => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();
	}

	/*-----------------------------------------------------------------------------------*/
	/*	STYLE TAB
	/*-----------------------------------------------------------------------------------*/

	protected function register_style_counter_icon_controls() {
		/**
		 * Style Tab: Icon
		 */
		$this->start_controls_section(
			'section_counter_icon_style',
			[
				'label'                 => __( 'Icon', 'trx_addons' ),
				'tab'                   => Controls_Manager::TAB_STYLE,
				'condition'             => [
					'trx_addons_icon_type!' => 'none',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'                  => 'counter_icon_bg',
				'label'                 => __( 'Background', 'trx_addons' ),
				'types'                 => [ 'none', 'classic', 'gradient' ],
				'condition'             => [
					'trx_addons_icon_type!' => 'none',
				],
				'selector'              => '{{WRAPPER}} .trx-addons-counter-icon',
			]
		);

		$this->add_control(
			'counter_icon_color',
			[
				'label'                 => __( 'Color', 'trx_addons' ),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '',
				'global'                => [
					'default' => Global_Colors::COLOR_PRIMARY,
				],
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-counter-icon' => 'color: {{VALUE}};',
					'{{WRAPPER}} .trx-addons-counter-icon svg' => 'fill: {{VALUE}};',
				],
				'condition'             => [
					'trx_addons_icon_type'  => 'icon',
				],
			]
		);

		$this->add_responsive_control(
			'counter_icon_size',
			[
				'label'                 => __( 'Size', 'trx_addons' ),
				'type'                  => Controls_Manager::SLIDER,
				'range'                 => [
					'px' => [
						'min'   => 5,
						'max'   => 100,
						'step'  => 1,
					],
				],
				'size_units'            => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'default'               => [
					'unit' => 'px',
					'size' => 40,
				],
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-counter-icon' => 'font-size: {{SIZE}}{{UNIT}}',
				],
				'condition'             => [
					'trx_addons_icon_type'  => 'icon',
				],
			]
		);

		$this->add_responsive_control(
			'counter_icon_img_width',
			[
				'label'                 => __( 'Image Width', 'trx_addons' ),
				'type'                  => Controls_Manager::SLIDER,
				'range'                 => [
					'px' => [
						'min'   => 10,
						'max'   => 500,
						'step'  => 1,
					],
				],
				'size_units'            => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'default'               => [
					'unit' => 'px',
					'size' => 80,
				],
				'condition'             => [
					'trx_addons_icon_type'  => 'image',
				],
				'selectors'             => [
					// '{{WRAPPER}} .trx-addons-counter-icon img' => 'width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .trx-addons-counter-icon' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'counter_icon_rotation',
			[
				'label'                 => __( 'Rotation', 'trx_addons' ),
				'type'                  => Controls_Manager::SLIDER,
				'range'                 => [
					'px' => [
						'min'   => 0,
						'max'   => 360,
						'step'  => 1,
					],
				],
				'size_units'            => '',
				'condition'             => [
					'trx_addons_icon_type!' => 'none',
				],
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-counter-icon .fa, {{WRAPPER}} .trx-addons-counter-icon img' => 'transform: rotate( {{SIZE}}deg );',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'                  => 'counter_icon_border',
				'label'                 => __( 'Border', 'trx_addons' ),
				'placeholder'           => '1px',
				'default'               => '1px',
				'selector'              => '{{WRAPPER}} .trx-addons-counter-icon',
				'condition'             => [
					'trx_addons_icon_type!' => 'none',
				],
			]
		);

		$this->add_responsive_control(
			'counter_icon_border_radius',
			[
				'label'                 => __( 'Border Radius', 'trx_addons' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-counter-icon' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'             => [
					'trx_addons_icon_type!' => 'none',
				],
			]
		);

		$this->add_responsive_control(
			'counter_icon_padding',
			[
				'label'                 => __( 'Padding', 'trx_addons' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-counter-icon' => 'padding-top: {{TOP}}{{UNIT}}; padding-left: {{LEFT}}{{UNIT}}; padding-right: {{RIGHT}}{{UNIT}}; padding-bottom: {{BOTTOM}}{{UNIT}};',
				],
				'condition'             => [
					'trx_addons_icon_type!' => 'none',
				],
			]
		);

		$this->add_responsive_control(
			'counter_icon_margin',
			[
				'label'                 => __( 'Margin', 'trx_addons' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-counter-icon-wrap' => 'margin-top: {{TOP}}{{UNIT}}; margin-left: {{LEFT}}{{UNIT}}; margin-right: {{RIGHT}}{{UNIT}}; margin-bottom: {{BOTTOM}}{{UNIT}};',
				],
				'condition'             => [
					'trx_addons_icon_type!' => 'none',
				],
			]
		);

		$this->add_control(
			'icon_divider_heading',
			[
				'label'                 => __( 'Icon Divider', 'trx_addons' ),
				'type'                  => Controls_Manager::HEADING,
				'separator'             => 'before',
				'condition'             => [
					'trx_addons_icon_type!' => 'none',
					'icon_divider'  => 'yes',
				],
			]
		);

		$this->add_control(
			'icon_divider_type',
			[
				'label'                     => __( 'Divider Type', 'trx_addons' ),
				'type'                  => Controls_Manager::SELECT,
				'default'               => 'solid',
				'options'               => [
					'solid'     => __( 'Solid', 'trx_addons' ),
					'double'    => __( 'Double', 'trx_addons' ),
					'dotted'    => __( 'Dotted', 'trx_addons' ),
					'dashed'    => __( 'Dashed', 'trx_addons' ),
				],
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-counter-icon-divider' => 'border-bottom-style: {{VALUE}}',
				],
				'condition'             => [
					'trx_addons_icon_type!' => 'none',
					'icon_divider'  => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'icon_divider_height',
			[
				'label'                 => __( 'Height', 'trx_addons' ),
				'type'                  => Controls_Manager::SLIDER,
				'default'               => [
					'size'  => 2,
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
					'{{WRAPPER}} .trx-addons-counter-icon-divider' => 'border-bottom-width: {{SIZE}}{{UNIT}}',
				],
				'condition'             => [
					'trx_addons_icon_type!' => 'none',
					'icon_divider'  => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'icon_divider_width',
			[
				'label'                 => __( 'Width', 'trx_addons' ),
				'type'                  => Controls_Manager::SLIDER,
				'default'               => [
					'size'  => 30,
				],
				'range'                 => [
					'px' => [
						'min'   => 1,
						'max'   => 1000,
						'step'  => 1,
					],
					'%' => [
						'min'   => 1,
						'max'   => 100,
						'step'  => 1,
					],
				],
				'size_units'            => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-counter-icon-divider' => 'width: {{SIZE}}{{UNIT}}',
				],
				'condition'             => [
					'trx_addons_icon_type!' => 'none',
					'icon_divider'  => 'yes',
				],
			]
		);

		$this->add_control(
			'icon_divider_color',
			[
				'label'                 => __( 'Color', 'trx_addons' ),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '',
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-counter-icon-divider' => 'border-bottom-color: {{VALUE}}',
				],
				'condition'             => [
					'trx_addons_icon_type!' => 'none',
					'icon_divider'  => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'icon_divider_margin',
			[
				'label'                 => __( 'Spacing', 'trx_addons' ),
				'type'                  => Controls_Manager::SLIDER,
				'range'                 => [
					'px' => [
						'min'   => 0,
						'max'   => 100,
						'step'  => 1,
					],
					'%' => [
						'min'   => 0,
						'max'   => 30,
						'step'  => 1,
					],
				],
				'size_units'            => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-counter-icon-divider-wrap' => 'margin-bottom: {{SIZE}}{{UNIT}}',
				],
				'condition'             => [
					'trx_addons_icon_type!' => 'none',
					'icon_divider'  => 'yes',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function register_style_counter_number_controls() {
		/**
		 * Style Tab: Number
		 */
		$this->start_controls_section(
			'section_counter_num_style',
			[
				'label'                 => __( 'Number', 'trx_addons' ),
				'tab'                   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'counter_num_color',
			[
				'label'                 => __( 'Color', 'trx_addons' ),
				'type'                  => Controls_Manager::COLOR,
				'global'                => [
					'default' => Global_Colors::COLOR_PRIMARY,
				],
				'default'               => '',
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-counter-number' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'                  => 'counter_num_typography',
				'label'                 => __( 'Typography', 'trx_addons' ),
				'global'                => [
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				],
				'selector'              => '{{WRAPPER}} .trx-addons-counter-number-wrap',
			]
		);

		$this->add_responsive_control(
			'counter_num_margin',
			[
				'label'                 => __( 'Margin', 'trx_addons' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-counter-number-wrap' => 'margin-top: {{TOP}}{{UNIT}}; margin-left: {{LEFT}}{{UNIT}}; margin-right: {{RIGHT}}{{UNIT}}; margin-bottom: {{BOTTOM}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'num_divider_heading',
			[
				'label'                 => __( 'Number Divider', 'trx_addons' ),
				'type'                  => Controls_Manager::HEADING,
				'separator'             => 'before',
				'condition'             => [
					'num_divider'       => 'yes',
					'counter_layout'    => [
						'layout-1',
						'layout-3',
						'layout-5',
						'layout-6',
					],
				],
			]
		);

		$this->add_control(
			'num_divider_type',
			[
				'label'                 => __( 'Divider Type', 'trx_addons' ),
				'type'                  => Controls_Manager::SELECT,
				'default'               => 'solid',
				'options'               => [
					'solid'     => __( 'Solid', 'trx_addons' ),
					'double'    => __( 'Double', 'trx_addons' ),
					'dotted'    => __( 'Dotted', 'trx_addons' ),
					'dashed'    => __( 'Dashed', 'trx_addons' ),
				],
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-counter-num-divider' => 'border-bottom-style: {{VALUE}}',
				],
				'condition'             => [
					'num_divider'       => 'yes',
					'counter_layout'    => [
						'layout-1',
						'layout-3',
						'layout-5',
						'layout-6',
					],
				],
			]
		);

		$this->add_responsive_control(
			'num_divider_height',
			[
				'label'                 => __( 'Height', 'trx_addons' ),
				'type'                  => Controls_Manager::SLIDER,
				'default'               => [
					'size'  => 2,
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
					'{{WRAPPER}} .trx-addons-counter-num-divider' => 'border-bottom-width: {{SIZE}}{{UNIT}}',
				],
				'condition'             => [
					'num_divider'       => 'yes',
					'counter_layout'    => [
						'layout-1',
						'layout-3',
						'layout-5',
						'layout-6',
					],
				],
			]
		);

		$this->add_responsive_control(
			'num_divider_width',
			[
				'label'                 => __( 'Width', 'trx_addons' ),
				'type'                  => Controls_Manager::SLIDER,
				'default'               => [
					'size'  => 30,
				],
				'range'                 => [
					'px' => [
						'min'   => 1,
						'max'   => 1000,
						'step'  => 1,
					],
					'%' => [
						'min'   => 1,
						'max'   => 100,
						'step'  => 1,
					],
				],
				'size_units'            => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-counter-num-divider' => 'width: {{SIZE}}{{UNIT}}',
				],
				'condition'             => [
					'num_divider'       => 'yes',
					'counter_layout'    => [
						'layout-1',
						'layout-3',
						'layout-5',
						'layout-6',
					],
				],
			]
		);

		$this->add_control(
			'num_divider_color',
			[
				'label'                 => __( 'Color', 'trx_addons' ),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '',
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-counter-num-divider' => 'border-bottom-color: {{VALUE}}',
				],
				'condition'             => [
					'num_divider'       => 'yes',
					'counter_layout'    => [
						'layout-1',
						'layout-3',
						'layout-5',
						'layout-6',
					],
				],
			]
		);

		$this->add_responsive_control(
			'num_divider_margin',
			[
				'label'                 => __( 'Spacing', 'trx_addons' ),
				'type'                  => Controls_Manager::SLIDER,
				'range'                 => [
					'px' => [
						'min'   => 0,
						'max'   => 100,
						'step'  => 1,
					],
					'%' => [
						'min'   => 0,
						'max'   => 30,
						'step'  => 1,
					],
				],
				'size_units'            => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-counter-num-divider-wrap' => 'margin-bottom: {{SIZE}}{{UNIT}}',
				],
				'condition'             => [
					'num_divider'       => 'yes',
					'counter_layout'    => [
						'layout-1',
						'layout-3',
						'layout-5',
						'layout-6',
					],
				],
			]
		);

		$this->end_controls_section();
	}

	protected function register_style_number_prefix_controls() {
		/**
		 * Style Tab: Prefix
		 */
		$this->start_controls_section(
			'section_number_prefix_style',
			[
				'label'                 => __( 'Prefix', 'trx_addons' ),
				'tab'                   => Controls_Manager::TAB_STYLE,
				'condition'             => [
					'number_prefix!' => '',
				],
			]
		);

		$this->add_control(
			'number_prefix_color',
			[
				'label'                 => __( 'Color', 'trx_addons' ),
				'type'                  => Controls_Manager::COLOR,
				'global'                => [
					'default' => Global_Colors::COLOR_PRIMARY,
				],
				'default'               => '',
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-counter-number-prefix' => 'color: {{VALUE}};',
				],
				'condition'             => [
					'number_prefix!' => '',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'                  => 'number_prefix_typography',
				'label'                 => __( 'Typography', 'trx_addons' ),
				'selector'              => '{{WRAPPER}} .trx-addons-counter-number-prefix',
				'condition'             => [
					'number_prefix!' => '',
				],
			]
		);

		$this->add_responsive_control(
			'number_prefix_margin',
			[
				'label'                 => __( 'Margin', 'trx_addons' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-counter-number-prefix' => 'margin-top: {{TOP}}{{UNIT}}; margin-left: {{LEFT}}{{UNIT}}; margin-right: {{RIGHT}}{{UNIT}}; margin-bottom: {{BOTTOM}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function register_style_number_suffix_controls() {
		/**
		 * Style Tab: Suffix
		 */
		$this->start_controls_section(
			'section_number_suffix_style',
			[
				'label'                 => __( 'Suffix', 'trx_addons' ),
				'tab'                   => Controls_Manager::TAB_STYLE,
				'condition'             => [
					'number_suffix!' => '',
				],
			]
		);

		$this->add_control(
			'section_number_suffix_color',
			[
				'label'                 => __( 'Color', 'trx_addons' ),
				'type'                  => Controls_Manager::COLOR,
				'global'                => [
					'default' => Global_Colors::COLOR_PRIMARY,
				],
				'default'               => '',
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-counter-number-suffix' => 'color: {{VALUE}};',
				],
				'condition'             => [
					'number_suffix!' => '',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'                  => 'section_number_suffix_typography',
				'label'                 => __( 'Typography', 'trx_addons' ),
				'selector'              => '{{WRAPPER}} .trx-addons-counter-number-suffix',
				'condition'             => [
					'number_suffix!' => '',
				],
			]
		);

		$this->add_responsive_control(
			'number_suffix_margin',
			[
				'label'                 => __( 'Margin', 'trx_addons' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-counter-number-suffix' => 'margin-top: {{TOP}}{{UNIT}}; margin-left: {{LEFT}}{{UNIT}}; margin-right: {{RIGHT}}{{UNIT}}; margin-bottom: {{BOTTOM}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function register_style_title_controls() {
		/**
		 * Style Tab: Title
		 */
		$this->start_controls_section(
			'section_counter_title_style',
			[
				'label'                 => __( 'Title', 'trx_addons' ),
				'tab'                   => Controls_Manager::TAB_STYLE,
				'condition'             => [
					'counter_title!' => '',
				],
			]
		);

		$this->add_control(
			'counter_title_bg_color',
			[
				'label'                 => __( 'Background Color', 'trx_addons' ),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '',
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-counter-title-wrap' => 'background-color: {{VALUE}};',
				],
				'condition'             => [
					'counter_title!' => '',
				],
			]
		);

		$this->add_control(
			'title_style_heading',
			[
				'label'                 => __( 'Title', 'trx_addons' ),
				'type'                  => Controls_Manager::HEADING,
				'separator'             => 'before',
				'condition'             => [
					'counter_title!' => '',
				],
			]
		);

		$this->add_control(
			'counter_title_color',
			[
				'label'                 => __( 'Text Color', 'trx_addons' ),
				'type'                  => Controls_Manager::COLOR,
				'global'                => [
					'default' => Global_Colors::COLOR_SECONDARY,
				],
				'default'               => '',
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-counter-title' => 'color: {{VALUE}};',
				],
				'condition'             => [
					'counter_title!' => '',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'                  => 'counter_title_typography',
				'label'                 => __( 'Typography', 'trx_addons' ),
				'selector'              => '{{WRAPPER}} .trx-addons-counter-title',
				'global'                => [
					'default' => Global_Typography::TYPOGRAPHY_SECONDARY,
				],
				'condition'             => [
					'counter_title!' => '',
				],
			]
		);

		$this->add_responsive_control(
			'counter_title_spacer',
			[
				'label'                 => __( 'Spacer', 'trx_addons' ),
				'type'                  => Controls_Manager::SLIDER,
				'size_units'            => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'default'               => [
					'unit' => 'px',
					'size' => '',
				],
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-counter-title' => 'margin-bottom: {{SIZE}}{{UNIT}}',
				],
				'condition'             => [
					'counter_title!'  => '',
				],
			]
		);

		$this->add_control(
			'subtitle_style_heading',
			[
				'label'                 => __( 'Subtitle', 'trx_addons' ),
				'type'                  => Controls_Manager::HEADING,
				'separator'             => 'before',
				'condition'             => [
					'counter_subtitle!' => '',
				],
			]
		);

		$this->add_control(
			'counter_subtitle_color',
			[
				'label'                 => __( 'Text Color', 'trx_addons' ),
				'type'                  => Controls_Manager::COLOR,
				'global'                => [
					'default' => Global_Colors::COLOR_TEXT,
				],
				'default'               => '',
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-counter-subtitle' => 'color: {{VALUE}};',
				],
				'condition'             => [
					'counter_subtitle!' => '',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'                  => 'counter_subtitle_typography',
				'label'                 => __( 'Typography', 'trx_addons' ),
				'global'                => [
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				],
				'selector'              => '{{WRAPPER}} .trx-addons-counter-subtitle',
				'condition'             => [
					'counter_subtitle!' => '',
				],
			]
		);

		$this->add_responsive_control(
			'counter_title_margin',
			[
				'label'                 => __( 'Margin', 'trx_addons' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-counter-title-wrap' => 'margin-top: {{TOP}}{{UNIT}}; margin-left: {{LEFT}}{{UNIT}}; margin-right: {{RIGHT}}{{UNIT}}; margin-bottom: {{BOTTOM}}{{UNIT}};',
				],
				'separator'             => 'before',
				'condition'             => [
					'counter_title!' => '',
				],
			]
		);

		$this->add_responsive_control(
			'counter_title_padding',
			[
				'label'                 => __( 'Padding', 'trx_addons' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-counter-title-wrap' => 'padding-top: {{TOP}}{{UNIT}}; padding-left: {{LEFT}}{{UNIT}}; padding-right: {{RIGHT}}{{UNIT}}; padding-bottom: {{BOTTOM}}{{UNIT}};',
				],
				'condition'             => [
					'counter_title!' => '',
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

		$starting_number = ( $settings['starting_number'] ) ? $settings['starting_number'] : 0;
		$ending_number = ( $settings['ending_number'] ) ? $settings['ending_number'] : 250;
		$counter_speed = ( $settings['counter_speed']['size'] ) ? $settings['counter_speed']['size'] : 1500;

		$this->add_render_attribute([
			'counter' => [
				'class' => [
					'trx-addons-counter trx-addons-counter-' . esc_attr( $this->get_id() ),
					'trx-addons-counter-' . $settings['counter_layout'],
				],
				'data-target' => '.trx-addons-counter-number-' . esc_attr( $this->get_id() ),
			],
			'counter-number' => [
				'class'               => 'trx-addons-counter-number trx-addons-counter-number-' . esc_attr( $this->get_id() ),
				'data-from'           => $starting_number,
				'data-to'             => $ending_number,
				'data-speed'          => $counter_speed,
				'data-separator'      => $settings['thousand_separator'],
				'data-separator-char' => $settings['thousand_separator_char'],
			],
		]);
		?>
		<div class="trx-addons-counter-container">
			<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'counter' ) ); ?>><?php
				if ( 'layout-1' === $settings['counter_layout'] || 'layout-5' === $settings['counter_layout'] || 'layout-6' === $settings['counter_layout'] ) {
					// Counter Icon
					$this->render_icon();
					?><div class="trx-addons-counter-number-title-wrap"><?php
						// Counter Number
						$this->render_counter_number();

						if ( 'yes' === $settings['num_divider'] ) {
							?><div class="trx-addons-counter-num-divider-wrap"><span class="trx-addons-counter-num-divider"></span></div><?php
						}
						
						// Counter Title
						$this->render_counter_title();
					?></div><?php

				} else if ( 'layout-2' === $settings['counter_layout'] ) {
					// Counter Icon
					$this->render_icon();

					// Counter Title
					$this->render_counter_title();

					// Counter Number
					$this->render_counter_number();

					if ( 'yes' === $settings['num_divider'] ) {
						?><div class="trx-addons-counter-num-divider-wrap"><span class="trx-addons-counter-num-divider"></span></div><?php
					}

				} else if ( 'layout-3' === $settings['counter_layout'] ) {

					// Counter Number
					$this->render_counter_number();
					
					if ( 'yes' === $settings['num_divider'] ) {
						?><div class="trx-addons-counter-num-divider-wrap"><span class="trx-addons-counter-num-divider"></span></div><?php
					}
					
					?><div class="trx-addons-icon-title-wrap"><?php
						// Counter Icon
						$this->render_icon();

						// Counter Title
						$this->render_counter_title();
					?></div><?php

				} else if ( 'layout-4' === $settings['counter_layout'] ) {
					?><div class="trx-addons-icon-title-wrap"><?php
						// Counter Icon
						$this->render_icon();

						// Counter Title
						$this->render_counter_title();
					?></div><?php
					
					// Counter Number
					$this->render_counter_number();
					
					if ( 'yes' === $settings['num_divider'] ) {
						?><div class="trx-addons-counter-num-divider-wrap"><span class="trx-addons-counter-num-divider"></span></div><?php
					}

				} else if ( 'layout-7' === $settings['counter_layout'] || 'layout-8' === $settings['counter_layout'] ) {

					// Counter Number
					$this->render_counter_number();

					?><div class="trx-addons-icon-title-wrap"><?php
						// Counter Icon
						$this->render_icon();

						// Counter Title
						$this->render_counter_title();
					?></div><?php

				} else if ( 'layout-9' === $settings['counter_layout'] ) {
					?><div class="trx-addons-icon-number-wrap"><?php
						// Counter Icon
						$this->render_icon();

						// Counter Number
						$this->render_counter_number();
					?></div><?php
					
					// Counter Title
					$this->render_counter_title();

				} else if ( 'layout-10' === $settings['counter_layout'] ) {
					?><div class="trx-addons-icon-number-wrap"><?php
						// Counter Number
						$this->render_counter_number();

						// Counter Icon
						$this->render_icon();
					?></div><?php
					
					// Counter Title
					$this->render_counter_title();
				}
			?></div>
		</div>
		<?php
	}

	/**
	 * Render a counter icon output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @access protected
	 */
	private function render_icon() {
		$settings = $this->get_settings_for_display();

		if ( ! isset( $settings['counter_icon'] ) && ! Icons_Manager::is_migration_allowed() ) {
			// add old default
			$settings['counter_icon'] = 'fa fa-star';
		}

		$has_icon = ! empty( $settings['counter_icon'] );

		if ( $has_icon ) {
			$this->add_render_attribute( 'i', 'class', $settings['counter_icon'] );
			$this->add_render_attribute( 'i', 'aria-hidden', 'true' );
		}

		$icon_attributes = $this->get_render_attribute_string( 'counter_icon' );

		if ( ! $has_icon && ! empty( $settings['icon']['value'] ) ) {
			$has_icon = true;
		}
		$migrated = isset( $settings['__fa4_migrated']['icon'] );
		$is_new = ! isset( $settings['counter_icon'] ) && Icons_Manager::is_migration_allowed();

		if ( 'icon' === $settings['trx_addons_icon_type'] ) {
			?><div class="trx-addons-counter-icon-wrap">
				<span class="trx-addons-counter-icon trx-addons-icon"><?php
					if ( $is_new || $migrated ) {
						Icons_Manager::render_icon( $settings['icon'], [ 'aria-hidden' => 'true' ] );
					} elseif ( ! empty( $settings['counter_icon'] ) ) {
						?><i <?php echo wp_kses_post( $this->get_render_attribute_string( 'i' ) ); ?>></i><?php
					}
				?></span>
			</div><?php

		} elseif ( 'image' === $settings['trx_addons_icon_type'] ) {
			$image = $settings['icon_image'];
			if ( $image['url'] ) {
				?><div class="trx-addons-counter-icon-wrap">
					<span class="trx-addons-counter-icon trx-addons-counter-icon-img"><?php
						echo wp_kses_post( Group_Control_Image_Size::get_attachment_image_html( $settings, 'image', 'icon_image' ) );
					?></span>
				</div><?php
			}
		}

		if ( 'yes' === $settings['icon_divider'] ) {
			if ( 'layout-1' === $settings['counter_layout'] || 'layout-2' === $settings['counter_layout'] ) {
				?><div class="trx-addons-counter-icon-divider-wrap"><span class="trx-addons-counter-icon-divider"></span></div><?php
			}
		}
	}

	/**
	 * Render a counter number output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @access protected
	 */
	private function render_counter_number() {
		$settings = $this->get_settings_for_display();
		$tag = TrxAddonsUtils::validate_html_tag( $settings['number_html_tag'] );
		?><<?php echo esc_html( $tag ); ?> class="trx-addons-counter-number-wrap"><?php
			if ( $settings['number_prefix'] ) {
				?><span class="trx-addons-counter-number-prefix"><?php
					echo esc_attr( $settings['number_prefix'] );
				?></span><?php
			}
			?><span <?php echo wp_kses_post( $this->get_render_attribute_string( 'counter-number' ) ); ?>><?php
				$starting_number = ( $settings['starting_number'] ) ? $settings['starting_number'] : 0;
				echo esc_attr( $starting_number );
			?></span><?php
			if ( $settings['number_suffix'] ) {
				?><span class="trx-addons-counter-number-suffix"><?php
					echo esc_attr( $settings['number_suffix'] );
				?></span><?php
			}
		?></<?php echo esc_html( $tag ); ?>><?php
	}

	/**
	 * Render a counter title output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @access protected
	 */
	private function render_counter_title() {
		$settings = $this->get_settings_for_display();

		if ( $settings['counter_title'] || $settings['counter_subtitle'] ) {
			$this->add_inline_editing_attributes( 'counter_title', 'none' );
			$this->add_render_attribute( 'counter_title', 'class', 'trx-addons-counter-title' );
			$this->add_inline_editing_attributes( 'counter_subtitle', 'none' );
			$this->add_render_attribute( 'counter_subtitle', 'class', 'trx-addons-counter-subtitle' );
			
			?><div class="trx-addons-counter-title-wrap"><?php
				if ( $settings['counter_title'] ) {
					$title_tag = TrxAddonsUtils::validate_html_tag( $settings['title_html_tag'] );
					?><<?php echo esc_html( $title_tag ); ?> <?php echo wp_kses_post( $this->get_render_attribute_string( 'counter_title' ) ); ?>><?php
						echo esc_attr( $settings['counter_title'] );
					?></<?php echo esc_html( $title_tag ); ?>><?php
				}
				if ( $settings['counter_subtitle'] ) {
					$subtitle_tag = TrxAddonsUtils::validate_html_tag( $settings['subtitle_html_tag'] );
					?><<?php echo esc_html( $subtitle_tag ); ?> <?php echo wp_kses_post( $this->get_render_attribute_string( 'counter_subtitle' ) ); ?>><?php
						echo esc_attr( $settings['counter_subtitle'] );
					?></<?php echo esc_html( $subtitle_tag ); ?>><?php
				}
			?></div><?php
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
		?><#
		function icon_template() {
			var iconHTML = elementor.helpers.renderIcon( view, settings.icon, { 'aria-hidden': true }, 'i' , 'object' ),
				migrated = elementor.helpers.isIconMigrated( settings, 'icon' );
		
			if ( settings.trx_addons_icon_type == 'icon' ) {
				if ( settings.counter_icon || settings.icon ) {
					#><div class="trx-addons-counter-icon-wrap">
						<span class="trx-addons-counter-icon trx-addons-icon">
							<# if ( iconHTML && iconHTML.rendered && ( ! settings.counter_icon || migrated ) ) { #>
								{{{ iconHTML.value }}}
							<# } else { #>
								<i class="{{ settings.counter_icon }}" aria-hidden="true"></i>
							<# } #>
						</span>
					</div><#
				}
			} else if ( settings.trx_addons_icon_type == 'image' ) {
				if ( settings.icon_image.url != '' ) {
					#><div class="trx-addons-counter-icon-wrap">
						<span class="trx-addons-counter-icon trx-addons-counter-icon-img">
							<#
							var image = {
								id: settings.icon_image.id,
								url: settings.icon_image.url,
								size: settings.image_size,
								dimension: settings.image_custom_dimension,
								model: view.getEditModel()
							};

							var imageUrl = elementor.imagesManager.getImageUrl( image );
							#>
							<img src="{{ _.escape( imageUrl ) }}" />
						</span>
					</div><#
				}
			}
						
			if ( settings.icon_divider == 'yes' ) {
				if ( settings.counter_layout == 'layout-1' || settings.counter_layout == 'layout-2' ) {
					#><div class="trx-addons-counter-icon-divider-wrap"><span class="trx-addons-counter-icon-divider"></span></div><#
				}
			}
		}
						
		function number_template() {
			var numberHTMLTag = elementor.helpers.validateHTMLTag( settings.number_html_tag );
			#><{{ numberHTMLTag }} class="trx-addons-counter-number-wrap"><#
				if ( settings.number_prefix != '' ) {
					var prefix = settings.number_prefix;
					view.addRenderAttribute( 'prefix', 'class', 'trx-addons-counter-number-prefix' );
					var prefix_html = '<span' + ' ' + view.getRenderAttributeString( 'prefix' ) + '>' + prefix + '</span>';
					print( prefix_html );
				}
			#><span class="trx-addons-counter-number" data-from="{{ settings.starting_number }}" data-to="{{ settings.ending_number }}" data-speed="{{ settings.counter_speed.size }}" data-separator="{{ settings.thousand_separator }}" data-separator-char="{{ settings.thousand_separator_char }}">
				{{{ settings.starting_number }}}
			</span><#
				if ( settings.number_suffix != '' ) {
					var suffix = settings.number_suffix;
					view.addRenderAttribute( 'suffix', 'class', 'trx-addons-counter-number-suffix' );
					var suffix_html = '<span' + ' ' + view.getRenderAttributeString( 'suffix' ) + '>' + suffix + '</span>';
					print( suffix_html );
				}
			#></{{ numberHTMLTag }}><#
		}

		function title_template() {
			if ( settings.counter_title != '' || settings.counter_subtitle != '' ) {
				#><div class="trx-addons-counter-title-wrap"><#
					if ( settings.counter_title != '' ) {
						var title = settings.counter_title;
						view.addRenderAttribute( 'counter_title', 'class', 'trx-addons-counter-title' );
						view.addInlineEditingAttributes( 'counter_title' );
						var titleHTMLTag = elementor.helpers.validateHTMLTag( settings.title_html_tag ),
							title_html = '<' + titleHTMLTag  + ' ' + view.getRenderAttributeString( 'counter_title' ) + '>' + title + '</' + titleHTMLTag + '>';
						print( title_html );
					}
					if ( settings.counter_subtitle != '' ) {
						var title = settings.counter_subtitle;
						view.addRenderAttribute( 'counter_subtitle', 'class', 'trx-addons-counter-subtitle' );
						view.addInlineEditingAttributes( 'counter_subtitle' );
						var subtitleHTMLTag = elementor.helpers.validateHTMLTag( settings.subtitle_html_tag ),
							subtitle_html = '<' + subtitleHTMLTag  + ' ' + view.getRenderAttributeString( 'counter_subtitle' ) + '>' + title + '</' + subtitleHTMLTag + '>';
						print( subtitle_html );
					}
				#></div><#
			}
		}

		#><div class="trx-addons-counter-container">
			<div class="trx-addons-counter trx-addons-counter-{{ settings.counter_layout }}" data-target=".trx-addons-counter-number"><#

				if ( settings.counter_layout == 'layout-1' || settings.counter_layout == 'layout-5' || settings.counter_layout == 'layout-6' ) {
					icon_template();
					
					#><div class="trx-addons-counter-number-title-wrap"><#
						number_template();
						
						if ( settings.num_divider == 'yes' ) {
							#><div class="trx-addons-counter-num-divider-wrap"><span class="trx-addons-counter-num-divider"></span></div><#
						}

						title_template();
					#></div><#
				
				} else if ( settings.counter_layout == 'layout-2' ) {
					icon_template();
					title_template();
					number_template();
					
					if ( settings.num_divider == 'yes' ) {
						#><div class="trx-addons-counter-num-divider-wrap"><span class="trx-addons-counter-num-divider"></span></div><#
					}
				
				} else if ( settings.counter_layout == 'layout-3' ) {
					number_template();
					
					if ( settings.num_divider == 'yes' ) {
						#><div class="trx-addons-counter-num-divider-wrap"><span class="trx-addons-counter-num-divider"></span></div><#
					}
					#><div class="trx-addons-icon-title-wrap"><#
						icon_template();
						title_template();
					#></div><#
				
				} else if ( settings.counter_layout == 'layout-4' ) {
					#><div class="trx-addons-icon-title-wrap"><#
						icon_template();
						title_template();
					#></div><#
					
					number_template();

					if ( settings.num_divider == 'yes' ) {
						#><div class="trx-addons-counter-num-divider-wrap"><span class="trx-addons-counter-num-divider"></span></div><#
					}
				
				} else if ( settings.counter_layout == 'layout-7' || settings.counter_layout == 'layout-8' ) {
					number_template();
					
					#><div class="trx-addons-icon-title-wrap"><#
						icon_template();
						title_template();
					#></div><#
				
				} else if ( settings.counter_layout == 'layout-9' ) {
					#><div class="trx-addons-icon-number-wrap"><#
						icon_template();
						number_template();
					#></div><#
					
					title_template();
				
				} else if ( settings.counter_layout == 'layout-10' ) {
					#><div class="trx-addons-icon-number-wrap"><#
						number_template();
						icon_template();
					#></div><#
					
					title_template();
				}
			#></div>
		</div>
		<?php
	}
}
