<?php
/**
 * Accordion Widget
 *
 * @package ThemeREX Addons
 * @since v2.30.0
 */

namespace TrxAddons\ElementorWidgets\Widgets\Accordion;

use TrxAddons\ElementorWidgets\BaseWidget;

// Elementor Classes.
use Elementor\Controls_Manager;
use Elementor\Control_Media;
use Elementor\Utils;
use Elementor\Icons_Manager;
use Elementor\Repeater;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Accordion Widget
 */
class AccordionWidget extends BaseWidget {

	/**
	 * Register widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 */
	protected function register_controls() {
		/* Content Tab */
		$this->register_content_accordion_controls();
		$this->register_content_toggle_icon_controls();
		$this->register_content_settings_controls();
		$this->register_content_help_docs_controls();

		/* Style Tab */
		$this->register_style_items_controls();
		$this->register_style_item_numbers_controls();
		$this->register_style_title_controls();
		$this->register_style_content_controls();
		$this->register_style_toggle_icon_controls();
	}

	/*-----------------------------------------------------------------------------------*/
	/*	CONTENT TAB
	/*-----------------------------------------------------------------------------------*/

	/**
	 * Register accordion content controls
	 */
	protected function register_content_accordion_controls() {

		$this->start_controls_section(
			'section_accordion_tabs',
			[
				'label'                 => esc_html__( 'Accordion', 'trx_addons' ),
			]
		);

		$repeater = new Repeater();

		$repeater->start_controls_tabs( 'accordion_tabs_content_tabs' );

			$repeater->start_controls_tab(
				'accordion_tabs_content_tab',
				[
					'label' => __( 'Content', 'trx_addons' ),
				]
			);

				$repeater->add_control(
					'tab_title',
					[
						'label'                 => __( 'Title', 'trx_addons' ),
						'type'                  => Controls_Manager::TEXT,
						'label_block'           => true,
						'default'               => __( 'Accordion Title', 'trx_addons' ),
						'dynamic'               => [
							'active'   => true,
						],
					]
				);

				$repeater->add_control(
					'content_type',
					[
						'label'                 => esc_html__( 'Content Type', 'trx_addons' ),
						'type'                  => Controls_Manager::SELECT,
						'label_block'           => false,
						'options'               => [
							'content'   => __( 'Content', 'trx_addons' ),
							'image'     => __( 'Image', 'trx_addons' ),
							// 'widget'    => __( 'Saved Widget', 'trx_addons' ),
							'section'   => __( 'Saved Section', 'trx_addons' ),
							'template'  => __( 'Saved Page Template', 'trx_addons' ),
						],
						'default'               => 'content',
					]
				);

				$repeater->add_control(
					'accordion_content',
					[
						'label'                 => esc_html__( 'Content', 'trx_addons' ),
						'type'                  => Controls_Manager::WYSIWYG,
						'default'               => esc_html__( 'Click edit button to change this text. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'trx_addons' ),
						'dynamic'               => [ 'active' => true ],
						'condition'             => [
							'content_type'  => 'content',
						],
					]
				);

				$repeater->add_control(
					'image',
					[
						'label'                 => __( 'Image', 'trx_addons' ),
						'type'                  => Controls_Manager::MEDIA,
						'dynamic'               => [
							'active'   => true,
						],
						'default'               => [
							'url' => Utils::get_placeholder_image_src(),
						],
						'conditions'            => [
							'terms' => [
								[
									'name'      => 'content_type',
									'operator'  => '==',
									'value'     => 'image',
								],
							],
						],
					]
				);

				$repeater->add_group_control(
					Group_Control_Image_Size::get_type(),
					[
						'name'                  => 'image',
						'label'                 => __( 'Image Size', 'trx_addons' ),
						'default'               => 'large',
						'exclude'               => [ 'custom' ],
						'conditions'            => [
							'terms' => [
								[
									'name'      => 'content_type',
									'operator'  => '==',
									'value'     => 'image',
								],
							],
						],
					]
				);

				// $repeater->add_control(
				// 	'saved_widget',
				// 	[
				// 		'label'                 => __( 'Choose Widget', 'trx_addons' ),
				// 		'type'                  => 'trx-addons-query',
				// 		'label_block'           => false,
				// 		'multiple'              => false,
				// 		'query_type'            => 'templates-widget',
				// 		'conditions'        => [
				// 			'terms' => [
				// 				[
				// 					'name'      => 'content_type',
				// 					'operator'  => '==',
				// 					'value'     => 'widget',
				// 				],
				// 			],
				// 		],
				// 	]
				// );

				$repeater->add_control(
					'saved_section',
					[
						'label'                 => __( 'Choose Section', 'trx_addons' ),
						'type'                  => 'trx-addons-query',
						'label_block'           => false,
						'multiple'              => false,
						'query_type'            => 'templates-section',
						'conditions'        => [
							'terms' => [
								[
									'name'      => 'content_type',
									'operator'  => '==',
									'value'     => 'section',
								],
							],
						],
					]
				);

				$repeater->add_control(
					'templates',
					[
						'label'                 => __( 'Choose Template', 'trx_addons' ),
						'type'                  => 'trx-addons-query',
						'label_block'           => false,
						'multiple'              => false,
						'query_type'            => 'templates-page',
						'conditions'        => [
							'terms' => [
								[
									'name'      => 'content_type',
									'operator'  => '==',
									'value'     => 'template',
								],
							],
						],
					]
				);

				$repeater->add_control(
					'accordion_tab_default_active',
					[
						'label'                 => esc_html__( 'Active as Default', 'trx_addons' ),
						'type'                  => Controls_Manager::SWITCHER,
						'default'               => 'no',
						'return_value'          => 'yes',
					]
				);

			$repeater->end_controls_tab();

			$repeater->start_controls_tab(
				'accordion_tabs_icon_tab',
				[
					'label' => __( 'Icon', 'trx_addons' ),
				]
			);

				$repeater->add_control(
					'tab_title_icon',
					[
						'label'                 => __( 'Icon', 'trx_addons' ),
						'type'                  => Controls_Manager::ICONS,
						'label_block'           => true,
						'fa4compatibility'      => 'accordion_tab_title_icon',
					]
				);

			$repeater->end_controls_tab();

			$repeater->start_controls_tab(
				'accordion_tabs_advanced_tab',
				[
					'label' => __( 'Advanced', 'trx_addons' ),
				]
			);

				$repeater->add_control(
					'accordion_tab_id',
					[
						'label'                 => __( 'Custom CSS ID', 'trx_addons' ),
						'description'           => __( 'This CSS ID will be applied to ID attribute of this tab in HTML. It should only contain dashes, underscores, letters or numbers. No spaces. Also make sure to use different ID for each tab.', 'trx_addons' ),
						'type'                  => Controls_Manager::TEXT,
						'label_block'           => true,
						'default'               => '',
						'ai'                    => [
							'active' => false,
						],
					]
				);

			$repeater->end_controls_tab();

		$repeater->end_controls_tabs();

		$this->add_control(
			'tabs',
			[
				'type'                  => Controls_Manager::REPEATER,
				'default'               => [
					[ 'tab_title' => esc_html__( 'Accordion Tab Title 1', 'trx_addons' ) ],
					[ 'tab_title' => esc_html__( 'Accordion Tab Title 2', 'trx_addons' ) ],
					[ 'tab_title' => esc_html__( 'Accordion Tab Title 3', 'trx_addons' ) ],
				],
				'fields'                => $repeater->get_controls(),
				'title_field'           => '{{tab_title}}',
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Register toggle icon content controls
	 *
	 * @return void
	 */
	protected function register_content_toggle_icon_controls() {

		$this->start_controls_section(
			'section_accordion_toggle_icon',
			[
				'label'                 => esc_html__( 'Toggle Icon', 'trx_addons' ),
			]
		);

		$this->add_control(
			'toggle_icon_show',
			[
				'label'                 => esc_html__( 'Toggle Icon', 'trx_addons' ),
				'type'                  => Controls_Manager::SWITCHER,
				'default'               => 'yes',
				'label_on'              => __( 'Show', 'trx_addons' ),
				'label_off'             => __( 'Hide', 'trx_addons' ),
				'return_value'          => 'yes',
			]
		);

		$this->add_control(
			'select_toggle_icon',
			[
				'label'                 => __( 'Normal Icon', 'trx_addons' ),
				'type'                  => Controls_Manager::ICONS,
				'label_block'           => false,
				'skin'                  => 'inline',
				'fa4compatibility'      => 'toggle_icon_normal',
				'default'               => [
					'value'     => 'fas fa-plus',
					'library'   => 'fa-solid',
				],
				'recommended'           => [
					'fa-regular' => [
						'plus-square',
						'caret-square-up',
						'caret-square-down',
					],
					'fa-solid' => [
						'chevron-up',
						'chevron-down',
						'angle-up',
						'angle-down',
						'angle-right',
						'angle-dowble-up',
						'angle-dowble-down',
						'caret-up',
						'caret-down',
						'caret-square-up',
						'caret-square-down',
						'plus',
						'plus-circle',
						'plus-square',
						'minus',
					],
				],
				'condition'             => [
					'toggle_icon_show' => 'yes',
				],
			]
		);

		$this->add_control(
			'select_toggle_icon_active',
			[
				'label'                 => __( 'Active Icon', 'trx_addons' ),
				'type'                  => Controls_Manager::ICONS,
				'label_block'           => false,
				'skin'                  => 'inline',
				'fa4compatibility'      => 'toggle_icon_active',
				'default'               => [
					'value'     => 'fas fa-minus',
					'library'   => 'fa-solid',
				],
				'recommended'           => [
					'fa-regular' => [
						'minus-square',
						'caret-square-up',
						'caret-square-down',
					],
					'fa-solid' => [
						'chevron-up',
						'chevron-down',
						'angle-up',
						'angle-down',
						'angle-dowble-up',
						'angle-dowble-down',
						'caret-up',
						'caret-down',
						'caret-square-up',
						'caret-square-down',
						'minus',
						'minus-circle',
						'minus-square',
					],
				],
				'condition'             => [
					'toggle_icon_show' => 'yes',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Register settings content controls
	 *
	 * @return void
	 */
	protected function register_content_settings_controls() {

		$this->start_controls_section(
			'section_accordion_settings',
			[
				'label'                 => esc_html__( 'Settings', 'trx_addons' ),
			]
		);

		$this->add_control(
			'accordion_type',
			[
				'label'                 => esc_html__( 'Accordion Type', 'trx_addons' ),
				'type'                  => Controls_Manager::SELECT,
				'default'               => 'accordion',
				'label_block'           => false,
				'options'               => [
					'accordion'     => esc_html__( 'Accordion', 'trx_addons' ),
					'toggle'        => esc_html__( 'Toggle', 'trx_addons' ),
				],
				'frontend_available'    => true,
			]
		);

		$this->add_control(
			'toggle_speed',
			[
				'label'                 => esc_html__( 'Toggle Speed (ms)', 'trx_addons' ),
				'type'                  => Controls_Manager::NUMBER,
				'label_block'           => false,
				'default'               => 300,
				'frontend_available'    => true,
			]
		);

		$this->add_control(
			'title_html_tag',
			[
				'label'                 => __( 'Title HTML Tag', 'trx_addons' ),
				'type'                  => Controls_Manager::SELECT,
				'options'               => [
					'h1' => 'H1',
					'h2' => 'H2',
					'h3' => 'H3',
					'h4' => 'H4',
					'h5' => 'H5',
					'h6' => 'H6',
					'div' => 'div',
				],
				'default'               => 'div',
			]
		);

		$this->add_control(
			'show_item_numbers',
			[
				'label'                 => esc_html__( 'Show Item Numbers', 'trx_addons' ),
				'type'                  => Controls_Manager::SWITCHER,
				'default'               => '',
				'return_value'          => 'yes',
			]
		);

		$this->add_control(
			'item_number_prefix',
			[
				'label'                 => __( 'Item Number Prefix', 'trx_addons' ),
				'type'                  => Controls_Manager::TEXT,
				'label_block'           => true,
				'default'               => '',
				'condition'             => [
					'show_item_numbers' => 'yes'
				]
			]
		);

		$this->add_control(
			'item_number_sufix',
			[
				'label'                 => __( 'Item Number Sufix', 'trx_addons' ),
				'type'                  => Controls_Manager::TEXT,
				'label_block'           => true,
				'default'               => '',
				'condition'             => [
					'show_item_numbers' => 'yes'
				]
			]
		);

		$this->add_control(
			'custom_id_prefix',
			[
				'label'       => __( 'Custom ID Prefix', 'trx_addons' ),
				'description' => __( 'A prefix that will be applied to ID attribute of tabs in HTML. For example, prefix "mytab" will be applied as "mytab-1", "mytab-2" in ID attribute of Tab 1 and Tab 2 respectively. It should only contain dashes, underscores, letters or numbers. No spaces.', 'trx_addons' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'default'     => '',
				'ai'          => [
					'active' => false,
				],
				'placeholder' => __( 'mytab', 'trx_addons' ),
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Register items style controls
	 *
	 * @return void
	 */
	protected function register_style_items_controls() {

		$this->start_controls_section(
			'section_accordion_items_style',
			[
				'label'                 => esc_html__( 'Items', 'trx_addons' ),
				'tab'                   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'accordion_items_spacing',
			[
				'label'                 => __( 'Items Gaps', 'trx_addons' ),
				'type'                  => Controls_Manager::SLIDER,
				'size_units'            => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'range'                 => [
					'px'    => [
						'min' => 0,
						'max' => 200,
					],
				],
				'default'               => [
					'size'  => 10,
				],
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-accordion-item:not(:last-child)' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->start_controls_tabs( 'accordion_tabs_style' );

		$this->start_controls_tab(
			'accordion_tab_normal',
			[
				'label'                 => __( 'Normal', 'trx_addons' ),
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'        => 'accordion_items_border',
				'label'       => __( 'Border', 'trx_addons' ),
				'placeholder' => '1px',
				'fields_options' => [
					'border' => [
						'default' => 'solid',
					],
					'width' => [
						'default' => [
							'top' => 1,
							'right' => 1,
							'bottom' => 1,
							'left' => 1,
							'unit' => 'px',
						],
					],
				],
				'selector'    => '{{WRAPPER}} .trx-addons-accordion-item',
			)
		);

		$this->add_responsive_control(
			'accordion_items_border_radius',
			[
				'label'                 => esc_html__( 'Border Radius', 'trx_addons' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-accordion-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'accordion_items_padding',
			[
				'label'                 => esc_html__( 'Padding', 'trx_addons' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-accordion-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'                  => 'accordion_items_box_shadow',
				'selector'              => '{{WRAPPER}} .trx-addons-accordion-item',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'accordion_tab_hover',
			[
				'label'                 => __( 'Hover', 'trx_addons' ),
			]
		);

		$this->add_control(
			'accordion_items_border_color_hover',
			[
				'label'                 => esc_html__( 'Border Color', 'trx_addons' ),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '',
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-accordion-item:hover' => 'border-color: {{VALUE}};',
				],
				// 'condition'             => [
				// 	'accordion_items_border_border!' => '',
				// ],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'                  => 'accordion_items_box_shadow_hover',
				'selector'              => '{{WRAPPER}} .trx-addons-accordion-item:hover',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'accordion_tab_active',
			[
				'label'                 => __( 'Active', 'trx_addons' ),
			]
		);

		$this->add_control(
			'accordion_items_border_color_active',
			[
				'label'                 => esc_html__( 'Border Color', 'trx_addons' ),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '',
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-accordion-item.trx-addons-accordion-item-active' => 'border-color: {{VALUE}};',
				],
				// 'condition'             => [
				// 	'accordion_items_border_border!' => '',
				// ],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'                  => 'accordion_items_box_shadow_active',
				'selector'              => '{{WRAPPER}} .trx-addons-accordion-item.trx-addons-accordion-item-active',
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * Register item numbers style controls
	 *
	 * @return void
	 */
	protected function register_style_item_numbers_controls() {

		$this->start_controls_section(
			'section_item_numbers_style',
			[
				'label'                 => esc_html__( 'Item Numbers', 'trx_addons' ),
				'tab'                   => Controls_Manager::TAB_STYLE,
				'condition'             => [
					'show_item_numbers' => 'yes'
				],
			]
		);

		$this->add_responsive_control(
			'item_numbers_right_spacing',
			[
				'label'                 => __( 'Spacing', 'trx_addons' ),
				'type'                  => Controls_Manager::SLIDER,
				'size_units'            => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'range'                 => [
					'px'    => [
						'min' => 0,
						'max' => 200,
					],
				],
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-accordion.trx-addons-accordion-show-numbers .trx-addons-accordion-tab-title,
					 {{WRAPPER}} .trx-addons-accordion.trx-addons-accordion-show-numbers .trx-addons-accordion-item .trx-addons-accordion-tab-content' => 'padding-left: {{SIZE}}{{UNIT}};',
				],
				'condition'             => [
					'show_item_numbers' => 'yes'
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'                  => 'item_numbers_typography',
				'selector'              => '{{WRAPPER}} .trx-addons-accordion-title-number .trx-addons-accordion-title-number-text',
				'global'                => [
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				],
				'condition'             => [
					'show_item_numbers' => 'yes'
				],
			]
		);

		$this->start_controls_tabs( 'accordion_item_numbers_tabs_style' );

		$this->start_controls_tab(
			'accordion_item_numbers_tab_normal',
			[
				'label'                 => __( 'Normal', 'trx_addons' ),
			]
		);

		$this->add_control(
			'item_numbers_text_color',
			[
				'label'                 => esc_html__( 'Text Color', 'trx_addons' ),
				'type'                  => Controls_Manager::COLOR,
				'global'                => [
					'default' => Global_Colors::COLOR_PRIMARY,
				],
				'default'               => '',
				'selectors' => [
					'{{WRAPPER}} .trx-addons-accordion-title-number .trx-addons-accordion-title-number-text' => 'color: {{VALUE}};',
				],
				'condition'             => [
					'show_item_numbers' => 'yes'
				],
			]
		);

		$this->add_control(
			'item_numbers_background_color',
			[
				'label'                 => esc_html__( 'Background Color', 'trx_addons' ),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '',
				'selectors' => [
					'{{WRAPPER}} .trx-addons-accordion-title-number' => 'background-color: {{VALUE}};',
				],
				'condition'             => [
					'show_item_numbers' => 'yes'
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'                  => 'item_numbers_border',
				'label'                 => esc_html__( 'Border', 'trx_addons' ),
				'selector'              => '{{WRAPPER}} .trx-addons-accordion-title-number',
				'condition'             => [
					'show_item_numbers' => 'yes'
				],
			]
		);

		$this->add_responsive_control(
			'item_numbers_border_radius',
			[
				'label'                 => esc_html__( 'Border Radius', 'trx_addons' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-accordion-title-number' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'             => [
					'show_item_numbers' => 'yes'
				],
			]
		);

		$this->add_responsive_control(
			'item_numbers_padding',
			[
				'label'                 => esc_html__( 'Padding', 'trx_addons' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-accordion-title-number' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'             => [
					'show_item_numbers' => 'yes'
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'accordion_item_numbers_tab_hover',
			[
				'label'                 => __( 'Hover', 'trx_addons' ),
			]
		);

		$this->add_control(
			'item_numbers_hover_color',
			[
				'label'                 => esc_html__( 'Text Color', 'trx_addons' ),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '',
				'selectors' => [
					'{{WRAPPER}} .trx-addons-accordion-item:hover .trx-addons-accordion-title-number .trx-addons-accordion-title-number-text' => 'color: {{VALUE}};',
				],
				'condition'             => [
					'show_item_numbers' => 'yes'
				],
			]
		);

		$this->add_control(
			'item_numbers_hover_background_color',
			[
				'label'                 => esc_html__( 'Background Color', 'trx_addons' ),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '',
				'selectors' => [
					'{{WRAPPER}} .trx-addons-accordion-item:hover .trx-addons-accordion-title-number' => 'background-color: {{VALUE}};',
				],
				'condition'             => [
					'show_item_numbers' => 'yes'
				],
			]
		);

		$this->add_control(
			'item_numbers_hover_border_color',
			[
				'label'                 => esc_html__( 'Border Color', 'trx_addons' ),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '',
				'selectors' => [
					'{{WRAPPER}} .trx-addons-accordion-item:hover .trx-addons-accordion-title-number' => 'border-color: {{VALUE}};',
				],
				'condition'             => [
					'show_item_numbers' => 'yes'
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'accordion_item_numbers_tab_active',
			[
				'label'                 => __( 'Active', 'trx_addons' ),
			]
		);

		$this->add_control(
			'item_numbers_active_color',
			[
				'label'                 => esc_html__( 'Text Color', 'trx_addons' ),
				'type'                  => Controls_Manager::COLOR,
				'global'                => [
					'default' => Global_Colors::COLOR_ACCENT,
				],
				'default'               => '',
				'selectors' => [
					'{{WRAPPER}} .trx-addons-accordion-item.trx-addons-accordion-item-active .trx-addons-accordion-title-number .trx-addons-accordion-title-number-text' => 'color: {{VALUE}};',
				],
				'condition'             => [
					'show_item_numbers' => 'yes'
				],
			]
		);

		$this->add_control(
			'item_numbers_active_background_color',
			[
				'label'                 => esc_html__( 'Background Color', 'trx_addons' ),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '',
				'selectors' => [
					'{{WRAPPER}} .trx-addons-accordion-item.trx-addons-accordion-item-active .trx-addons-accordion-title-number' => 'background-color: {{VALUE}};',
				],
				'condition'             => [
					'show_item_numbers' => 'yes'
				],
			]
		);

		$this->add_control(
			'item_numbers_active_border_color',
			[
				'label'                 => esc_html__( 'Border Color', 'trx_addons' ),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '',
				'selectors' => [
					'{{WRAPPER}} .trx-addons-accordion-item.trx-addons-accordion-item-active .trx-addons-accordion-title-number' => 'border-color: {{VALUE}};',
				],
				'condition'             => [
					'show_item_numbers' => 'yes'
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * Register title style controls
	 *
	 * @return void
	 */
	protected function register_style_title_controls() {

		$this->start_controls_section(
			'section_title_style',
			[
				'label'                 => esc_html__( 'Title', 'trx_addons' ),
				'tab'                   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->start_controls_tabs( 'accordion_title_tabs_style' );

		$this->start_controls_tab(
			'accordion_title_tab_normal',
			[
				'label'                 => __( 'Normal', 'trx_addons' ),
			]
		);

		$this->add_control(
			'tab_title_text_color',
			[
				'label'                 => esc_html__( 'Text Color', 'trx_addons' ),
				'type'                  => Controls_Manager::COLOR,
				'global'                => [
					'default' => Global_Colors::COLOR_PRIMARY,
				],
				'default'               => '',
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-accordion .trx-addons-accordion-tab-title' => 'color: {{VALUE}};',
					'{{WRAPPER}} .trx-addons-accordion .trx-addons-accordion-tab-title svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'tab_title_icon_color',
			[
				'label'                 => esc_html__( 'Icon Color', 'trx_addons' ),
				'type'                  => Controls_Manager::COLOR,
				'global'                => [
					'default' => Global_Colors::COLOR_PRIMARY,
				],
				'default'               => '',
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-accordion .trx-addons-accordion-tab-icon' => 'color: {{VALUE}};',
					'{{WRAPPER}} .trx-addons-accordion .trx-addons-accordion-tab-title svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'tab_title_bg_color',
			[
				'label'                 => esc_html__( 'Background Color', 'trx_addons' ),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '',
				'selectors' => [
					'{{WRAPPER}} .trx-addons-accordion .trx-addons-accordion-tab-title' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'                  => 'tab_title_typography',
				'selector'              => '{{WRAPPER}} .trx-addons-accordion .trx-addons-accordion-tab-title',
				'global'                => [
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'                  => 'tab_title_border',
				'label'                 => esc_html__( 'Border', 'trx_addons' ),
				'selector'              => '{{WRAPPER}} .trx-addons-accordion .trx-addons-accordion-tab-title',
			]
		);

		$this->add_responsive_control(
			'tab_title_border_radius',
			[
				'label'                 => esc_html__( 'Border Radius', 'trx_addons' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-accordion .trx-addons-accordion-tab-title' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'tab_title_padding',
			[
				'label'                 => esc_html__( 'Padding', 'trx_addons' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-accordion .trx-addons-accordion-tab-title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'accordion_title_bottom_spacing',
			[
				'label'                 => __( 'Bottom Spacing', 'trx_addons' ),
				'type'                  => Controls_Manager::SLIDER,
				'size_units'            => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'range'                 => [
					'px'    => [
						'min' => -200,
						'max' => 200,
					],
				],
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-accordion .trx-addons-accordion-tab-title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'accordion_title_tab_hover',
			[
				'label'                 => __( 'Hover', 'trx_addons' ),
			]
		);

		$this->add_control(
			'tab_title_text_color_hover',
			[
				'label'                 => esc_html__( 'Text Color', 'trx_addons' ),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '',
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-accordion .trx-addons-accordion-tab-title:hover' => 'color: {{VALUE}};',
					'{{WRAPPER}} .trx-addons-accordion .trx-addons-accordion-tab-title:hover .trx-addons-accordion-tab-icon' => 'color: {{VALUE}};',
					'{{WRAPPER}} .trx-addons-accordion .trx-addons-accordion-tab-title:hover svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'tab_title_icon_color_hover',
			[
				'label'                 => esc_html__( 'Icon Color', 'trx_addons' ),
				'type'                  => Controls_Manager::COLOR,
				'global'                => [
					'default' => Global_Colors::COLOR_PRIMARY,
				],
				'default'               => '',
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-accordion .trx-addons-accordion-tab-title:hover .trx-addons-accordion-tab-icon' => 'color: {{VALUE}};',
					'{{WRAPPER}} .trx-addons-accordion .trx-addons-accordion-tab-title:hover svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'tab_title_bg_color_hover',
			[
				'label'                 => esc_html__( 'Background Color', 'trx_addons' ),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '',
				'selectors' => [
					'{{WRAPPER}} .trx-addons-accordion .trx-addons-accordion-tab-title:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'tab_title_border_color_hover',
			[
				'label'                 => esc_html__( 'Border Color', 'trx_addons' ),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '',
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-accordion .trx-addons-accordion-tab-title:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'accordion_title_tab_active',
			[
				'label'                 => __( 'Active', 'trx_addons' ),
			]
		);

		$this->add_control(
			'tab_title_text_color_active',
			[
				'label'                 => esc_html__( 'Text Color', 'trx_addons' ),
				'type'                  => Controls_Manager::COLOR,
				'global'                => [
					'default' => Global_Colors::COLOR_ACCENT,
				],
				'default'               => '',
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-accordion .trx-addons-accordion-tab-title.trx-addons-accordion-tab-active' => 'color: {{VALUE}};',
					'{{WRAPPER}} .trx-addons-accordion .trx-addons-accordion-tab-title.trx-addons-accordion-tab-active .trx-addons-accordion-tab-icon' => 'color: {{VALUE}};',
					'{{WRAPPER}} .trx-addons-accordion .trx-addons-accordion-tab-title.trx-addons-accordion-tab-active svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'tab_title_icon_color_active',
			[
				'label'                 => esc_html__( 'Icon Color', 'trx_addons' ),
				'type'                  => Controls_Manager::COLOR,
				'global'                => [
					'default' => Global_Colors::COLOR_ACCENT,
				],
				'default'               => '',
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-accordion .trx-addons-accordion-tab-title.trx-addons-accordion-tab-active .trx-addons-accordion-tab-icon' => 'color: {{VALUE}};',
					'{{WRAPPER}} .trx-addons-accordion .trx-addons-accordion-tab-title.trx-addons-accordion-tab-active svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'tab_title_bg_color_active',
			[
				'label'                 => esc_html__( 'Background Color', 'trx_addons' ),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '',
				'selectors' => [
					'{{WRAPPER}} .trx-addons-accordion .trx-addons-accordion-tab-title.trx-addons-accordion-tab-active' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'tab_title_border_color_active',
			[
				'label'                 => esc_html__( 'Border Color', 'trx_addons' ),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '',
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-accordion .trx-addons-accordion-tab-title.trx-addons-accordion-tab-active' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'accordion_title_bottom_spacing_active',
			[
				'label'                 => __( 'Bottom Spacing', 'trx_addons' ),
				'type'                  => Controls_Manager::SLIDER,
				'size_units'            => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'range'                 => [
					'px'    => [
						'min' => -200,
						'max' => 200,
					],
				],
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-accordion .trx-addons-accordion-tab-title.trx-addons-accordion-tab-active' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'tab_icon_heading',
			[
				'label'                 => __( 'Icon', 'trx_addons' ),
				'type'                  => Controls_Manager::HEADING,
				'separator'             => 'before',
			]
		);

		$this->add_responsive_control(
			'tab_icon_size',
			[
				'label'                 => __( 'Icon Size', 'trx_addons' ),
				'type'                  => Controls_Manager::SLIDER,
				'default'               => [
					'size'  => 16,
					'unit'  => 'px',
				],
				'size_units'            => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'range'                 => [
					'px'        => [
						'min'   => 0,
						'max'   => 100,
						'step'  => 1,
					],
				],
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-accordion .trx-addons-accordion-tab-title .trx-addons-accordion-tab-icon' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'tab_icon_spacing',
			[
				'label'                 => __( 'Icon Spacing', 'trx_addons' ),
				'type'                  => Controls_Manager::SLIDER,
				'default'               => [
					'size'  => 10,
					'unit'  => 'px',
				],
				'size_units'            => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'range'                 => [
					'px'    => [
						'min'   => 0,
						'max'   => 100,
						'step'  => 1,
					],
				],
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-accordion .trx-addons-accordion-tab-title .trx-addons-accordion-tab-icon' => 'margin-right: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'tab_icon_vertical_offset',
			[
				'label'     => __( 'Vertical Offset', 'trx_addons' ),
				'type'      => Controls_Manager::SLIDER,
				'size_units'=> [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'range'     => [
					'px' => [
						'min' => -100,
						'max' => 100,
					],
					'em' => [
						'min' => -5,
						'max' => 5,
						'step' => 0.1
					],
				],
				'selectors' => [
					'{{WRAPPER}} .trx-addons-accordion .trx-addons-accordion-tab-title .trx-addons-accordion-tab-icon' => 'top: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Register content style controls
	 *
	 * @return void
	 */
	protected function register_style_content_controls() {

		$this->start_controls_section(
			'section_content_style',
			[
				'label'                 => esc_html__( 'Content', 'trx_addons' ),
				'tab'                   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'tab_content_bg_color',
			[
				'label'                 => esc_html__( 'Background Color', 'trx_addons' ),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '',
				'selectors' => [
					'{{WRAPPER}} .trx-addons-accordion .trx-addons-accordion-item .trx-addons-accordion-tab-content' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'tab_content_text_color',
			[
				'label'                 => esc_html__( 'Text Color', 'trx_addons' ),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '#333',
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-accordion .trx-addons-accordion-item .trx-addons-accordion-tab-content' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'                  => 'tab_content_typography',
				'selector'              => '{{WRAPPER}} .trx-addons-accordion .trx-addons-accordion-item .trx-addons-accordion-tab-content',
				'global'                => [
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				],
			]
		);

		$this->add_responsive_control(
			'tab_content_padding',
			[
				'label'                 => esc_html__( 'Padding', 'trx_addons' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-accordion .trx-addons-accordion-item .trx-addons-accordion-tab-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Register toggle icon style controls
	 *
	 * @return void
	 */
	protected function register_style_toggle_icon_controls() {

		$this->start_controls_section(
			'section_toggle_icon_style',
			[
				'label'                 => esc_html__( 'Toggle Icon', 'trx_addons' ),
				'tab'                   => Controls_Manager::TAB_STYLE,
				'condition' => [
					'toggle_icon_show' => 'yes',
				],
			]
		);

		$this->add_control(
			'toggle_icon_align',
			[
				'label'   => __( 'Alignment', 'trx_addons' ),
				'type'    => Controls_Manager::CHOOSE,
				'options' => [
					'left'  => [
						'title' => __( 'Start', 'trx_addons' ),
						'icon' => 'eicon-h-align-left',
					],
					'right' => [
						'title' => __( 'End', 'trx_addons' ),
						'icon' => 'eicon-h-align-right',
					],
				],
				'default' => is_rtl() ? 'left' : 'right',
				'toggle'  => false,
			]
		);

		$this->add_responsive_control(
			'toggle_icon_offset',
			[
				'label'     => __( 'Vertical offset', 'trx_addons' ),
				'type'      => Controls_Manager::SLIDER,
				'size_units'=> [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'range'     => [
					'px' => [
						'min' => -100,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .trx-addons-accordion-toggle-icon' => 'top: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'toggle_icon_spacing',
			[
				'label'     => __( 'Spacing', 'trx_addons' ),
				'type'      => Controls_Manager::SLIDER,
				'size_units'=> [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .trx-addons-toggle-icon-align-left .trx-addons-accordion-toggle-icon' => 'margin-right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .trx-addons-toggle-icon-align-right .trx-addons-accordion-toggle-icon' => 'margin-left: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'toggle_icon_size',
			[
				'label'      => __( 'Size', 'trx_addons' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => [
					'size'  => 16,
					'unit'  => 'px',
				],
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'range'      => [
					'px'    => [
						'min'   => 0,
						'max'   => 100,
						'step'  => 1,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .trx-addons-accordion .trx-addons-accordion-tab-title .trx-addons-accordion-toggle-icon .trx-addons-icon' => 'font-size: {{SIZE}}{{UNIT}};',
				],
				'condition'  => [
					'toggle_icon_show' => 'yes',
				],
			]
		);

		$this->start_controls_tabs( 'accordion_toggle_icon_tabs_style' );

		$this->start_controls_tab(
			'accordion_toggle_icon_tab_normal',
			[
				'label'                 => __( 'Normal', 'trx_addons' ),
			]
		);

		$this->add_control(
			'toggle_icon_color',
			[
				'label'                 => esc_html__( 'Color', 'trx_addons' ),
				'type'                  => Controls_Manager::COLOR,
				'global'                => [
					'default' => Global_Colors::COLOR_PRIMARY,
				],
				'default'               => '',
				'selectors' => [
					'{{WRAPPER}} .trx-addons-accordion .trx-addons-accordion-tab-title .trx-addons-accordion-toggle-icon' => 'color: {{VALUE}};',
					'{{WRAPPER}} .trx-addons-accordion .trx-addons-accordion-tab-title .trx-addons-accordion-toggle-icon svg' => 'fill: {{VALUE}};',
				],
				'condition' => [
					'toggle_icon_show' => 'yes',
				],
			]
		);

		$this->add_control(
			'toggle_icon_background_color',
			[
				'label'                 => esc_html__( 'Background Color', 'trx_addons' ),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '',
				'selectors' => [
					'{{WRAPPER}} .trx-addons-accordion .trx-addons-accordion-tab-title .trx-addons-accordion-toggle-icon' => 'background-color: {{VALUE}};',
				],
				'condition' => [
					'toggle_icon_show' => 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'                  => 'toggle_icon_border',
				'label'                 => esc_html__( 'Border', 'trx_addons' ),
				'selector'              => '{{WRAPPER}} .trx-addons-accordion .trx-addons-accordion-tab-title .trx-addons-accordion-toggle-icon',
			]
		);

		$this->add_responsive_control(
			'toggle_icon_border_radius',
			[
				'label'                 => esc_html__( 'Border Radius', 'trx_addons' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-accordion .trx-addons-accordion-tab-title .trx-addons-accordion-toggle-icon' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'toggle_icon_padding',
			[
				'label'                 => esc_html__( 'Padding', 'trx_addons' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-accordion .trx-addons-accordion-tab-title .trx-addons-accordion-toggle-icon' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'accordion_toggle_icon_tab_hover',
			[
				'label'                 => __( 'Hover', 'trx_addons' ),
			]
		);

		$this->add_control(
			'toggle_icon_hover_color',
			[
				'label'                 => esc_html__( 'Color', 'trx_addons' ),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '',
				'selectors' => [
					'.trx-addons-accordion-toggle-icon, {{WRAPPER}} .trx-addons-accordion .trx-addons-accordion-item:hover .trx-addons-accordion-tab-title .trx-addons-accordion-toggle-icon' => 'color: {{VALUE}};',
					'{{WRAPPER}} .trx-addons-accordion .trx-addons-accordion-item:hover .trx-addons-accordion-tab-title .trx-addons-accordion-toggle-icon svg' => 'fill: {{VALUE}};',
				],
				'condition'             => [
					'toggle_icon_show' => 'yes',
				],
			]
		);

		$this->add_control(
			'toggle_icon_hover_background_color',
			[
				'label'                 => esc_html__( 'Background Color', 'trx_addons' ),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '',
				'selectors' => [
					'{{WRAPPER}} .trx-addons-accordion .trx-addons-accordion-item:hover .trx-addons-accordion-tab-title .trx-addons-accordion-toggle-icon' => 'background-color: {{VALUE}};',
				],
				'condition'             => [
					'toggle_icon_show' => 'yes',
				],
			]
		);

		$this->add_control(
			'toggle_icon_hover_border_color',
			[
				'label'                 => esc_html__( 'Border Color', 'trx_addons' ),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '',
				'selectors' => [
					'{{WRAPPER}} .trx-addons-accordion .trx-addons-accordion-item:hover .trx-addons-accordion-tab-title .trx-addons-accordion-toggle-icon' => 'border-color: {{VALUE}};',
				],
				'condition'             => [
					'toggle_icon_show' => 'yes',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'accordion_toggle_icon_tab_active',
			[
				'label'                 => __( 'Active', 'trx_addons' ),
			]
		);

		$this->add_control(
			'toggle_icon_active_color',
			[
				'label'                 => esc_html__( 'Color', 'trx_addons' ),
				'type'                  => Controls_Manager::COLOR,
				'global'                => [
					'default' => Global_Colors::COLOR_ACCENT,
				],
				'default'               => '',
				'selectors' => [
					'{{WRAPPER}} .trx-addons-accordion .trx-addons-accordion-tab-title.trx-addons-accordion-tab-active .trx-addons-accordion-toggle-icon' => 'color: {{VALUE}};',
					'{{WRAPPER}} .trx-addons-accordion .trx-addons-accordion-tab-title.trx-addons-accordion-tab-active .trx-addons-accordion-toggle-icon svg' => 'fill: {{VALUE}};',
				],
				'condition'             => [
					'toggle_icon_show' => 'yes',
				],
			]
		);

		$this->add_control(
			'toggle_icon_active_background_color',
			[
				'label'                 => esc_html__( 'Background Color', 'trx_addons' ),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '',
				'selectors' => [
					'{{WRAPPER}} .trx-addons-accordion .trx-addons-accordion-tab-title.trx-addons-accordion-tab-active .trx-addons-accordion-toggle-icon' => 'background-color: {{VALUE}};',
				],
				'condition'             => [
					'toggle_icon_show' => 'yes',
				],
			]
		);

		$this->add_control(
			'toggle_icon_active_border_color',
			[
				'label'                 => esc_html__( 'Border Color', 'trx_addons' ),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '',
				'selectors' => [
					'{{WRAPPER}} .trx-addons-accordion .trx-addons-accordion-tab-title.trx-addons-accordion-tab-active .trx-addons-accordion-toggle-icon' => 'border-color: {{VALUE}};',
				],
				'condition'             => [
					'toggle_icon_show' => 'yes',
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
	 * Render a widget output in the frontend.
	 *
	 * @access protected
	 */
	protected function render() {
		$settings   = $this->get_settings_for_display();
		$id_int     = substr( $this->get_id_int(), 0, 3 );

		$this->add_render_attribute( 'accordion', [
			'class'             => [ 'trx-addons-accordion', 'trx-addons-toggle-icon-align-' . $settings['toggle_icon_align'] ],
			'id'                => 'trx-addons-accordion-' . esc_attr( $this->get_id() ),
			'data-accordion-id' => esc_attr( $this->get_id() ),
			'role'              => 'tablist',
		] );

		$item_number_mask = '';
		if ( 'yes' === $settings['show_item_numbers'] ) {

			$this->add_render_attribute( 'accordion', 'class', 'trx-addons-accordion-show-numbers' );

			if ( ! empty( $settings['item_number_prefix'] ) ) {
				$item_number_mask .= $settings['item_number_prefix'];
			}

			$item_number_mask .= '%s';

			if ( ! empty( $settings['item_number_sufix'] ) ) {
				$item_number_mask .= $settings['item_number_sufix'];
			}
		}
		?>
		<div <?php $this->print_render_attribute_string( 'accordion' ); ?>>
			<?php
			foreach ( $settings['tabs'] as $index => $tab ) :

				$tab_count = $index + 1;
				$tab_setting_key = $this->get_repeater_setting_key( 'item', 'tabs', $index );
				$tab_title_setting_key = $this->get_repeater_setting_key( 'tab_title', 'tabs', $index );
				$tab_content_setting_key = $this->get_repeater_setting_key( 'accordion_content', 'tabs', $index );

				$tab_class         = [ 'trx-addons-accordion-item' ];
				$tab_title_class   = [ 'trx-addons-accordion-tab-title' ];
				$tab_content_class = [ 'trx-addons-accordion-tab-content' ];

				if ( 'yes' === $tab['accordion_tab_default_active'] ) {
					$tab_class[]   = 'trx-addons-accordion-item-active';
					$tab_title_class[]   = 'trx-addons-accordion-tab-active-default';
					$tab_content_class[] = 'trx-addons-accordion-tab-active-default';
				}

				if ( $tab['accordion_tab_id'] ) {
					$tab_id = $tab['accordion_tab_id'];
				} elseif ( $settings['custom_id_prefix'] ) {
					$tab_id = $settings['custom_id_prefix'] . '-' . $tab_count;
				} else {
					$tab_id = 'trx-addons-accordion-tab-title-' . $id_int . $tab_count;
				}

				$this->add_render_attribute( $tab_setting_key, 'class', $tab_class );

				$this->add_render_attribute( $tab_title_setting_key, [
					'id'            => $tab_id,
					'class'         => $tab_title_class,
					'tabindex'      => '0',
					'data-tab'      => $tab_count,
					'role'          => 'tab',
					'aria-controls' => 'trx-addons-accordion-tab-content-' . $id_int . $tab_count,
					'aria-expanded' => ( 'yes' === $tab['accordion_tab_default_active'] ) ? 'true' : 'false',
				]);

				$this->add_render_attribute( $tab_content_setting_key, [
					'id'              => 'trx-addons-accordion-tab-content-' . $id_int . $tab_count,
					'class'           => $tab_content_class,
					'data-tab'        => $tab_count,
					'role'            => 'tabpanel',
					'aria-labelledby' => $tab_id,
				] );

				if ( 'content' === $tab['content_type'] ) {
					$this->add_inline_editing_attributes( $tab_content_setting_key, 'advanced' );
				}

				$migration_allowed = Icons_Manager::is_migration_allowed();

				// Title Icon - add old default
				if ( ! isset( $tab['accordion_tab_title_icon'] ) && ! $migration_allowed ) {
					$tab['accordion_tab_title_icon'] = isset( $fallback_defaults[ $index ] ) ? $fallback_defaults[ $index ] : '';
				}

				$migrated_title_icon = isset( $tab['__fa4_migrated']['tab_title_icon'] );
				$is_new_title_icon = ! isset( $tab['accordion_tab_title_icon'] ) && $migration_allowed;

				// Toggle Icon Normal
				if ( ! isset( $settings['toggle_icon_normal'] ) && ! $migration_allowed ) {
					// add old default
					$settings['toggle_icon_normal'] = 'fa fa-plus';
				}

				$has_toggle_icon = ! empty( $settings['toggle_icon_normal'] );

				if ( $has_toggle_icon ) {
					$this->add_render_attribute( 'toggle-icon', 'class', $settings['toggle_icon_normal'] );
					$this->add_render_attribute( 'toggle-icon', 'aria-hidden', 'true' );
				}

				if ( ! $has_toggle_icon && ! empty( $settings['select_toggle_icon']['value'] ) ) {
					$has_toggle_icon = true;
				}
				$migrated_normal = isset( $settings['__fa4_migrated']['select_toggle_icon'] );
				$is_new_normal = ! isset( $settings['toggle_icon_normal'] ) && $migration_allowed;

				// Toggle Icon Active
				if ( ! isset( $settings['toggle_icon_active'] ) && ! $migration_allowed ) {
					// add old default
					$settings['toggle_icon_active'] = 'fa fa-minus';
				}

				$has_toggle_active_icon = ! empty( $settings['toggle_icon_active'] );

				if ( $has_toggle_active_icon ) {
					$this->add_render_attribute( 'toggle-icon', 'class', $settings['toggle_icon_active'] );
					$this->add_render_attribute( 'toggle-icon', 'aria-hidden', 'true' );
				}

				if ( ! $has_toggle_active_icon && ! empty( $settings['select_toggle_icon_active']['value'] ) ) {
					$has_toggle_active_icon = true;
				}
				$migrated = isset( $settings['__fa4_migrated']['select_toggle_icon_active'] );
				$is_new = ! isset( $settings['toggle_icon_active'] ) && $migration_allowed;

				$item_number_count = ( $tab_count < 10 ) ? '0' . $tab_count : $tab_count;
				?>
				<div <?php $this->print_render_attribute_string( $tab_setting_key ); ?>>
					<?php $title_tag = $settings['title_html_tag']; ?>
					<<?php echo esc_html( $title_tag ); ?> <?php $this->print_render_attribute_string( $tab_title_setting_key ); ?>>
						<?php if ( 'yes' === $settings['show_item_numbers'] ) { ?>
							<div class="trx-addons-accordion-title-number">
								<span class="trx-addons-accordion-title-number-text"><?php echo sprintf( $item_number_mask, $item_number_count ); ?></span>
							</div>
						<?php } ?>
						<span class="trx-addons-accordion-title-icon">
							<?php if ( ! empty( $tab['accordion_tab_title_icon'] ) || ( ! empty( $tab['tab_title_icon']['value'] ) && $is_new_title_icon ) ) { ?>
								<span class="trx-addons-accordion-tab-icon trx-addons-icon">
									<?php
									if ( $is_new_title_icon || $migrated_title_icon ) {
										Icons_Manager::render_icon( $tab['tab_title_icon'], [ 'aria-hidden' => 'true' ] );
									} else { ?>
										<i class="<?php echo esc_attr( $tab['accordion_tab_title_icon'] ); ?>" aria-hidden="true"></i>
									<?php } ?>
								</span>
							<?php } ?>
							<span class="trx-addons-accordion-title-text">
								<?php echo wp_kses_post( $tab['tab_title'] ); ?>
							</span>
						</span>
						<?php if ( 'yes' === $settings['toggle_icon_show'] ) { ?>
							<span class="trx-addons-accordion-toggle-icon">
								<?php if ( $has_toggle_icon ) { ?>
									<span class='trx-addons-accordion-toggle-icon-close trx-addons-icon'>
										<?php
										if ( $is_new_normal || $migrated_normal ) {
											Icons_Manager::render_icon( $settings['select_toggle_icon'], [ 'aria-hidden' => 'true' ] );
										} elseif ( ! empty( $settings['toggle_icon_normal'] ) ) {
											?><i <?php $this->print_render_attribute_string( 'toggle-icon' ); ?>></i><?php
										}
										?>
									</span>
								<?php } ?>
								<?php if ( $has_toggle_active_icon ) { ?>
									<span class='trx-addons-accordion-toggle-icon-open trx-addons-icon'>
										<?php
										if ( $is_new_normal || $migrated_normal ) {
											Icons_Manager::render_icon( $settings['select_toggle_icon_active'], [ 'aria-hidden' => 'true' ] );
										} elseif ( ! empty( $settings['toggle_icon_active'] ) ) {
											?><i <?php $this->print_render_attribute_string( 'toggle-icon' ); ?>></i><?php
										}
										?>
									</span>
								<?php } ?>
							</span>
						<?php } ?>
					</<?php echo esc_html( $title_tag ); ?>>

					<div <?php $this->print_render_attribute_string( $tab_content_setting_key ); ?>>
						<?php echo $this->get_accordion_content( $tab ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
		<?php
	}

	/**
	 * Render accordion content.
	 */
	protected function get_accordion_content( $tab ) {
		$settings     = $this->get_settings_for_display();
		$content_type = $tab['content_type'];
		$output       = '';

		switch ( $content_type ) {
			case 'content':
				$output = do_shortcode( $tab['accordion_content'] );
				break;

			case 'image':
				$image_url = Group_Control_Image_Size::get_attachment_image_src( $tab['image']['id'], 'image', $tab );

				if ( ! $image_url ) {
					$image_url = $tab['image']['url'];
				}

				$image_html = '<div class="trx-addons-showcase-preview-image">';

				$image_html .= '<img src="' . esc_url( $image_url ) . '" alt="' . esc_attr( Control_Media::get_image_alt( $tab['image'] ) ) . '">';

				$image_html .= '</div>';

				$output = $image_html;
				break;

			case 'section':
				$output = \Elementor\Plugin::$instance->frontend->get_builder_content_for_display( $tab['saved_section'] );
				break;

			case 'template':
				$output = \Elementor\Plugin::$instance->frontend->get_builder_content_for_display( $tab['templates'] );
				break;

			case 'widget':
				$output = \Elementor\Plugin::$instance->frontend->get_builder_content_for_display( $tab['saved_widget'] );
				break;

			default:
				return;
		}

		return $output;
	}
}
