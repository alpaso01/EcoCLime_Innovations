<?php

/**
 * Tabs Widget
 *
 * @package ThemeREX Addons
 * @since v2.30.0
 */

namespace TrxAddons\ElementorWidgets\Widgets\Tabs;

use TrxAddons\ElementorWidgets\BaseWidget;
use TrxAddons\ElementorWidgets\Utils as TrxAddonsUtils;

// Elementor Classes.
use \Elementor\Controls_Manager;
use \Elementor\Group_Control_Border;
use \Elementor\Group_Control_Box_Shadow;
use \Elementor\Group_Control_Image_Size;
use \Elementor\Group_Control_Background;
use \Elementor\Group_Control_Typography;
use \Elementor\Icons_Manager;
use \Elementor\Utils;
use \Elementor\Group_Control_Css_Filter;
use \Elementor\Plugin;
use \Elementor\Repeater;
use \Elementor\Core\Kits\Documents\Tabs\Global_Typography;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Tabs Widget
 */
class TabsWidget extends BaseWidget {

	/**
	 * Constructor.
	 *
	 * Initializing the module base class.
	 * 
	 * @param array       $data  Widget data. Default is an empty array.
	 * @param array|null  $args  Optional. Widget default arguments. Default is null.
	 */
	public function __construct( $data = [], $args = null ) {
		static $loaded = false;

		parent::__construct( $data, $args );

		if ( $loaded ) {
			return;
		}
		$loaded = true;

		// Buffering
		add_filter( 'elementor/frontend/builder_content_data', array( $this, 'disable_cache_if_catch_output' ), 10, 2 );
		add_action( 'elementor/frontend/get_builder_content', array( $this, 'enable_cache_after_catch_output' ), 10, 3 );
		add_action( 'elementor/frontend/before_render', array( $this, 'register_buffering_start_catch_output' ), 10, 1 );
		add_action( 'elementor/frontend/after_render', array( $this, 'register_buffering_end_catch_output' ), 10, 1 );
		add_filter( 'trx_addons_filter_page_content', array( $this, 'register_buffering_paste_catch_output' ), 10, 1 );
	}

	/**
	 * Register widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 */
	protected function register_controls()
	{
		/* Content Tab */
		$this->register_content_tabs_controls();
		$this->register_content_help_docs_controls();

		/* Style Tab */
		$this->register_style_tabs_controls();
		$this->register_style_tabs_icon_controls();
		$this->register_style_tabs_subtitle_controls();
		$this->register_style_tabs_content_controls();
		$this->register_style_tabs_content_image_controls();
		$this->register_style_tabs_content_button_controls();
	}

	/*-----------------------------------------------------------------------------------*/
	/*	CONTENT TAB
	/*-----------------------------------------------------------------------------------*/

	/**
	 * Register tabs content controls
	 */
	protected function register_content_tabs_controls() {

		$this->start_controls_section(
			'section_tabs_content_settings',
			[
				'label' => esc_html__('Content', 'trx_addons')
			]
		);

		$tabs_repeater = new Repeater();

		$tabs_repeater->add_control(
			'tabs_show_as_default',
			[
				'label'        => __('Active as Default', 'trx_addons'),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'active'
			]
		);

		$tabs_repeater->add_control(
			'tabs_title_heading',
			[
				'label'     => esc_html__('Navigation Tab', 'trx_addons'),
				'type'      => Controls_Manager::HEADING,
				'separator' =>  'before'
			]
		);

		$tabs_repeater->add_control(
			'tabs_title',
			[
				'label'   => esc_html__('Title', 'trx_addons'),
				'type'    => Controls_Manager::TEXT,
				'label_block' => true,
				'default' => esc_html__('Tab Title', 'trx_addons'),
				'dynamic' => ['active' => true],
			]
		);

		$tabs_repeater->add_control(
			'tabs_subtitle',
			[
				'label' => __( 'Description', 'trx_addons' ),
				'type' => Controls_Manager::TEXTAREA,
				'placeholder' => __( 'Type your description here', 'trx_addons' ),
				'dynamic' => ['active' => true],
			]
		);

		$tabs_repeater->add_control(
			'tabs_icon_type',
			[
				'label'       => esc_html__('Icon Type', 'trx_addons'),
				'type'        => Controls_Manager::CHOOSE,
				'toggle'      => false,
				'label_block' => false,
				'options'     => [
					'none'      => [
						'title' => esc_html__('None', 'trx_addons'),
						'icon'  => 'eicon-ban'
					],
					'icon'      => [
						'title' => esc_html__('Icon', 'trx_addons'),
						'icon'  => 'eicon-info-circle'
					],
					'image'     => [
						'title' => esc_html__('Image', 'trx_addons'),
						'icon'  => 'eicon-image-bold'
					]
				],
				'default'       => 'none'
			]
		);

		$tabs_repeater->add_control(
			'tabs_title_icon',
			[
				'label'     => esc_html__('Icon', 'trx_addons'),
				'type'      => Controls_Manager::ICONS,
				'default'   => [
					'value'   => 'fas fa-home',
					'library' => 'fa-solid'
				],
				'condition' => [
					'tabs_icon_type' => 'icon'
				]
			]
		);

		$tabs_repeater->add_control(
			'tabs_title_image',
			[
				'label'   => esc_html__('Image', 'trx_addons'),
				'type'    => Controls_Manager::MEDIA,
				'default' => [
					'url' => Utils::get_placeholder_image_src()
				],
				'dynamic' => [
					'active' => true,
				],
				'condition' => [
					'tabs_icon_type' => 'image'
				]
			]
		);

		$tabs_repeater->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name'    => 'tabs_navigation_image_size',
				'default' => 'medium_large',
				'condition' => [
					'tabs_icon_type' => 'image',
				],
			]
		);

		$tabs_repeater->add_control(
			'tabs_content_heading',
			[
				'label'     => esc_html__('Content', 'trx_addons'),
				'type'      => Controls_Manager::HEADING,
				'separator' =>  'before'
			]
		);

		$tabs_repeater->add_control(
			'tabs_content_type',
			[
				'label'   => __('Content Type', 'trx_addons'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'content',
				'options' => [
					'content'       => __('Content', 'trx_addons'),
					'save_template' => __('Save Template', 'trx_addons'),
					'shortcode'     => __('ShortCode', 'trx_addons'),
					'section'       => __('Section ID', 'trx_addons'),
				]
			]
		);

		$tabs_repeater->add_control(
			'tabs_content_save_template',
			[
				'label'     => __('Select Section', 'trx_addons'),
				'type'      => Controls_Manager::SELECT,
				'options'   => $this->get_saved_template('section'),
				'default'   => '-1',
				'condition' => [
					'tabs_content_type' => 'save_template'
				]
			]
		);

		$tabs_repeater->add_control(
			'tabs_content_shortcode',
			[
				'label'       => __('Enter your shortcode', 'trx_addons'),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'placeholder' => __('[gallery]', 'trx_addons'),
				'condition'   => [
					'tabs_content_type' => 'shortcode'
				]
			]
		);

		$tabs_repeater->add_control(
			'tabs_content',
			[
				'label'   => esc_html__('Content', 'trx_addons'),
				'type'    => Controls_Manager::WYSIWYG,
				'default' => esc_html__('Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.', 'trx_addons'),
				'condition' => [
					'tabs_content_type' => 'content'
				]
			]
		);

		$tabs_repeater->add_control(
			'tabs_button_heading',
			[
				'label'     => esc_html__('Details Button', 'trx_addons'),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'tabs_content_type' => 'content'
				]
			]
		);

		$tabs_repeater->add_control(
			'tabs_details_btn_switcher',
			[
				'label'        => __('Show Button', 'trx_addons'),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
				'condition' => [
					'tabs_content_type' => 'content'
				]
			]
		);

		$tabs_repeater->add_control(
			'tabs_details_btn_text',
			[
				'label'     => __('Button Text', 'trx_addons'),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__('Read More', 'trx_addons'),
				'condition' => [
					'tabs_details_btn_switcher' => 'yes',
					'tabs_content_type' => 'content'
				],
				'dynamic' => [
					'active' => true,
				]
			]
		);

		$tabs_repeater->add_control(
			'tabs_details_btn_link',
			[
				'label'   => __('Button Link', 'trx_addons'),
				'type'    => Controls_Manager::URL,
				'default' => [
					'url'         => '#',
					'is_external' => ''
				],
				'show_external' => true,
				'condition' => [
					'tabs_details_btn_switcher' => 'yes',
					'tabs_content_type' => 'content'
				]
			]
		);

		$tabs_repeater->add_control(
			'select_tabs_details_btn_icon',
			array(
				'label'            => __( 'Button Icon', 'trx_addons' ),
				'type'             => Controls_Manager::ICONS,
				'fa4compatibility' => 'tabs_details_btn_icon',
				'condition' => [
					'tabs_details_btn_switcher' => 'yes',
					'tabs_content_type' => 'content'
				]
			)
		);

		$tabs_repeater->add_control(
			'tabs_details_btn_icon_position',
			array(
				'label'     => __( 'Icon Position', 'trx_addons' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'after',
				'options'   => array(
					'after'  => __( 'After', 'trx_addons' ),
					'before' => __( 'Before', 'trx_addons' ),
				),
				'condition' => [
					'tabs_details_btn_switcher' => 'yes',
					'tabs_content_type' => 'content',
					'select_tabs_details_btn_icon[value]!' => ''
				]
			)
		);

		$tabs_repeater->add_control(
			'tabs_image_heading',
			[
				'label'     => esc_html__('Image in Content', 'trx_addons'),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'tabs_content_type' => 'content'
				]
			]
		);

		$tabs_repeater->add_control(
			'tabs_image',
			[
				'label' => esc_html__('Choose Image', 'trx_addons'),
				'type'  => Controls_Manager::MEDIA,
				'dynamic' => [
					'active' => true,
				],
				'condition' => [
					'tabs_content_type' => 'content'
				]
			]
		);

		$tabs_repeater->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name'    => 'tabs_image_size',
				'label'   => esc_html__('Image Type', 'trx_addons'),
				'default' => 'medium',
				'condition' => [
					'tabs_content_type' => 'content'
				]
			]
		);

		$tabs_repeater->add_control(
			'tabs_section_id',
			[
				'label'     => __('Section ID', 'trx_addons'),
				'type'      => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
				'condition' => [
					'tabs_content_type' => 'section'
				]
			]
		);

		$this->add_control(
			'tabs',
			[
				'label'   => esc_html__('Tabs', 'trx_addons'),
				'type'      => Controls_Manager::REPEATER,
				'fields'  => $tabs_repeater->get_controls(),
				'seperator' => 'before',
				'default'   => [
					[
						'tabs_title' => esc_html__('Tab Title 1', 'trx_addons'),
						'tabs_show_as_default' => 'active'
					],
					[
						'tabs_title'   => esc_html__('Tab Title 2', 'trx_addons'),
						'tabs_content' => esc_html__('A quick brown fox jumps over the lazy dog. Optio, neque qui velit. Magni dolorum quidem ipsam eligendi, totam, facilis laudantium cum accusamus ullam voluptatibus commodi numquam, error, est. Ea, consequatur. Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.', 'trx_addons')
					],
					['tabs_title' => esc_html__('Tab Title 3', 'trx_addons')]
				],
				'title_field' => '{{tabs_title}}'
			]
		);

		$this->add_control(
			'tab_title_html_tag',
			array(
				'label'   => __( 'Tab Title HTML Tag', 'trx_addons' ),
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

		$this->end_controls_section();
	}

	/**
	 * Register tabs style controls
	 *
	 * @return void
	 */
	protected function register_style_tabs_controls() {

		$accent_color = apply_filters( 'trx_addons_filter_get_theme_accent_color', '#efa758' );

		$this->start_controls_section(
			'section_tabs_navigation_style_settings',
			[
				'label' => esc_html__('Tab Navigation', 'trx_addons'),
				'tab'   => Controls_Manager::TAB_STYLE
			]
		);

		$this->add_control(
			'tabs_orientation',
			[
				'label'   => esc_html__('Tab Orientation', 'trx_addons'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'trx-addons-tabs-horizontal-full-width',
				'options' => [
					'trx-addons-tabs-horizontal'            => esc_html__('Horizontal', 'trx_addons'),
					'trx-addons-tabs-horizontal-full-width' => esc_html__('Horizontal Full Width', 'trx_addons'),
					'trx-addons-tabs-vertical'              => esc_html__('Vertical', 'trx_addons')
				]
			]
		);

		$this->add_control(
			'tabs_navigation_alignment',
			[
				'label'   => __('Alignment', 'trx_addons'),
				'type'    => Controls_Manager::CHOOSE,
				'toggle'  => false,
				'default' => 'trx-addons-tabs-align-center',
				'options' => [
					'trx-addons-tabs-align-left'   => [
						'title' => __('Left', 'trx_addons'),
						'icon'  => 'eicon-text-align-left'
					],
					'trx-addons-tabs-align-center' => [
						'title' => __('Center', 'trx_addons'),
						'icon'  => 'eicon-text-align-center'
					],
					'trx-addons-tabs-align-right'  => [
						'title' => __('Right', 'trx_addons'),
						'icon'  => 'eicon-text-align-right'
					]
				],
				'condition' => [
					'tabs_orientation!' => 'trx-addons-tabs-vertical'
				]
			]
		);

		$this->add_control(
			'tabs_navigation_alignment_vertical',
			[
				'label'   => __('Alignment', 'trx_addons'),
				'type'    => Controls_Manager::CHOOSE,
				'toggle'  => false,
				'default' => 'trx-addons-tabs-v-align-top',
				'options' => [
					'trx-addons-tabs-v-align-top'   => [
						'title' => __('Top', 'trx_addons'),
						'icon'  => 'eicon-v-align-top'
					],
					'trx-addons-tabs-v-align-center' => [
						'title' => __('Center', 'trx_addons'),
						'icon'  => 'eicon-v-align-middle'
					],
					'trx-addons-tabs-v-align-bottom'  => [
						'title' => __('Bottom', 'trx_addons'),
						'icon'  => 'eicon-v-align-bottom'
					]
				],
				'condition' => [
					'tabs_orientation' => 'trx-addons-tabs-vertical'
				]
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'label'    => __( 'Title Typography', 'trx_addons' ),
				'name'     => 'tabs_navigation_typography',
				'selector' => '{{WRAPPER}} .trx-addons-tabs-{{ID}}.trx-addons-tabs > .trx-addons-tabs-nav li span.trx-addons-tabs-title',
				'fields_options'   => [
					'font_size'    => [
						'default'  => [
							'unit' => 'px',
							'size' => 16
						]
					]
				]
			]
		);

		$this->add_control(
			'tabs_navigation_bg',
			[
				'label'     => esc_html__('Navigation Container Background', 'trx_addons'),
				'type'      => Controls_Manager::COLOR,

				'selectors' => [
					'{{WRAPPER}} .trx-addons-tabs-{{ID}}.trx-addons-tabs > .trx-addons-tabs-nav' => 'background: {{VALUE}};'
				]
			]
		);

		$this->add_responsive_control(
			'tabs_navigation_list_padding',
			[
				'label'        => __('Padding', 'trx_addons'),
				'type'         => Controls_Manager::DIMENSIONS,
				'size_units'   => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'default'      => [
					'top'      => '16',
					'right'    => '24',
					'bottom'   => '16',
					'left'     => '24',
					'unit'     => 'px',
					'isLinked' => false
				],
				'selectors'    => [
					'{{WRAPPER}} .trx-addons-tabs-{{ID}}.trx-addons-tabs > .trx-addons-tabs-nav li' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
				]
			]
		);

		$this->add_responsive_control(
			'tabs_navigation_list_margin',
			[
				'label'      => __('Margin', 'trx_addons'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'default'    => [
					'top'      => '0',
					'right'    => '0',
					'bottom'   => '0',
					'left'     => '0',
					'unit'     => 'px',
					'isLinked' => true
				],
				'selectors'  => [
					'{{WRAPPER}} .trx-addons-tabs-{{ID}}.trx-addons-tabs > .trx-addons-tabs-nav li' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
				]
			]
		);

		$this->add_responsive_control(
			'tabs_navigation_list_width',
			[
				'label'       => __('List Item Width', 'trx_addons'),
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'range'       => [
					'px'      => [
						'min' => 0,
						'max' => 500
					],
					'%'       => [
						'min' => 0,
						'max' => 100
					]
				],
				'default'     => [
					'unit'    => 'px',
					'size'    => 200
				],
				'selectors'   => [
					'{{WRAPPER}} .trx-addons-tabs-{{ID}}.trx-addons-tabs-vertical > .trx-addons-tabs-nav li' => 'width: {{SIZE}}{{UNIT}};'
				],
				'condition'   => [
					'tabs_orientation' => 'trx-addons-tabs-vertical'
				]
			]
		);

		$this->add_responsive_control(
			'tabs_navigation_list_border_radius',
			[
				'label'      => __('Border Radius', 'trx_addons'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'default'    => [
					'top'      => '0',
					'right'    => '0',
					'bottom'   => '0',
					'left'     => '0',
					'unit'     => 'px',
					'isLinked' => true
				],
				'selectors'  => [
					'{{WRAPPER}} .trx-addons-tabs-{{ID}}.trx-addons-tabs > .trx-addons-tabs-nav li' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
				]
			]
		);

		$this->start_controls_tabs('tabs_navigation_tabs');

		// Normal State Tab
		$this->start_controls_tab('tabs_navigation_normal', ['label' => esc_html__('Normal', 'trx_addons')]);

		$this->add_control(
			'tabs_navigation_list_normal_text_color',
			[
				'label'     => __('Text Color', 'trx_addons'),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#8a8d91',
				'selectors' => [
					'{{WRAPPER}} .trx-addons-tabs-{{ID}}.trx-addons-tabs > .trx-addons-tabs-nav li' => 'color: {{VALUE}};'
				]
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'tabs_navigation_list_normal_background',
				'types'    => ['classic', 'gradient'],
				'selector' => '{{WRAPPER}} .trx-addons-tabs-nav li'
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'                 => 'tabs_navigation_list_normal_border',
				'fields_options'       => [
					'border'           => [
						'default'      => 'solid'
					],
					'width'            => [
						'default'      => [
							'top'      => '0',
							'right'    => '0',
							'bottom'   => '1',
							'left'     => '0',
							'isLinked' => false
						]
					],
					'color'            => [
						'default'      => '#e5e5e5'
					]
				],
				'selector'             => '{{WRAPPER}} .trx-addons-tabs-{{ID}}.trx-addons-tabs > .trx-addons-tabs-nav li'
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'tabs_navigation_list_box_shadow',
				'selector' => '{{WRAPPER}} .trx-addons-tabs-{{ID}}.trx-addons-tabs > .trx-addons-tabs-nav li'
			]
		);

		$this->end_controls_tab();

		// Active State Tab
		$this->start_controls_tab('tabs_navigation_active', ['label' => esc_html__('Active/Hover', 'trx_addons')]);

		$this->add_control(
			'tabs_navigation_list_hover_text_color',
			[
				'label'     => __('Text Color', 'trx_addons'),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#0a1724',
				'selectors' => [
					'{{WRAPPER}} .trx-addons-tabs-{{ID}}.trx-addons-tabs > .trx-addons-tabs-nav li.active, {{WRAPPER}} .trx-addons-tabs-{{ID}}.trx-addons-tabs > .trx-addons-tabs-nav li:hover' => 'color: {{VALUE}};'
				]
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'tabs_navigation_list_active_background',
				'types'    => ['classic', 'gradient'],
				'selector' => '{{WRAPPER}} .trx-addons-tabs-{{ID}}.trx-addons-tabs > .trx-addons-tabs-nav li.active, {{WRAPPER}} .trx-addons-tabs-{{ID}}.trx-addons-tabs > .trx-addons-tabs-nav li:hover'
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'                 => 'tabs_navigation_list_active_border',
				'fields_options'       => [
					'border'           => [
						'default'      => 'solid'
					],
					'width'            => [
						'default'      => [
							'top'      => '0',
							'right'    => '0',
							'bottom'   => '1',
							'left'     => '0',
							'isLinked' => false
						]
					],
					'color'            => [
						'default'      => $accent_color
					]
				],
				'selector'             => '{{WRAPPER}} .trx-addons-tabs-{{ID}}.trx-addons-tabs > .trx-addons-tabs-nav li.active, {{WRAPPER}} .trx-addons-tabs-{{ID}}.trx-addons-tabs > .trx-addons-tabs-nav li:hover'
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'tabs_navigation_list_active_box_shadow',
				'selector' => '{{WRAPPER}} .trx-addons-tabs-{{ID}}.trx-addons-tabs > .trx-addons-tabs-nav li.active, {{WRAPPER}} .trx-addons-tabs-{{ID}}.trx-addons-tabs > .trx-addons-tabs-nav li:hover'
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * Register tabs icon style controls
	 *
	 * @return void
	 */
	protected function register_style_tabs_icon_controls() {
		$this->start_controls_section(
			'section_tabs_icon_style_settings',
			[
				'label' => esc_html__('Navigation Icon/Image', 'trx_addons'),
				'tab'   => Controls_Manager::TAB_STYLE
			]
		);

		$this->add_control(
			'tabs_navigation_icon_style',
			[
				'label'     => esc_html__('Icon', 'trx_addons'),
				'type'      => Controls_Manager::HEADING,
				'separator' =>  'before'
			]
		);

		$this->add_control(
			'tabs_icon_box_show',
			[
				'label'        => esc_html__('Icon Box', 'trx_addons'),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'no',
				'return_value' => 'yes'
			]
		);

		$this->add_responsive_control(
			'tabs_icon_box_height',
			[
				'label'        => __('Icon Box Height', 'trx_addons'),
				'type'         => Controls_Manager::SLIDER,
				'size_units'   => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'range'        => [
					'px' 	   => [
						'min'  => 0,
						'max'  => 200,
						'step' => 1
					],
				],
				'default'      => [
					'unit'     => 'px',
					'size'     => 100
				],
				'selectors'    => [
					'{{WRAPPER}} .trx-addons-tabs-{{ID}}.trx-addons-tabs > .trx-addons-tabs-nav li .trx-addons-tabs-icon' => 'height: {{SIZE}}{{UNIT}};'
				],
				'condition'    => [
					'tabs_icon_box_show' => 'yes'
				]
			]
		);

		$this->add_responsive_control(
			'tabs_icon_box_width',
			[
				'label'        => __('Icon Box Width', 'trx_addons'),
				'type'         => Controls_Manager::SLIDER,
				'size_units'   => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'range'        => [
					'px' 	   => [
						'min'  => 0,
						'max'  => 200,
						'step' => 1
					],
				],
				'default'      => [
					'unit'     => 'px',
					'size'     => 100
				],
				'selectors'    => [
					'{{WRAPPER}} .trx-addons-tabs-{{ID}}.trx-addons-tabs > .trx-addons-tabs-nav li .trx-addons-tabs-icon' => 'width: {{SIZE}}{{UNIT}};'
				],
				'condition'    => [
					'tabs_icon_box_show' => 'yes'
				]
			]
		);

		$this->add_responsive_control(
			'tabs_navigation_icon_size',
			[
				'label'        => __('Icon Size', 'trx_addons'),
				'type'         => Controls_Manager::SLIDER,
				'size_units'   => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'range'        => [
					'px' 	   => [
						'min'  => 10,
						'max'  => 100,
						'step' => 1
					],
				],
				'default'      => [
					'unit'     => 'px',
					'size'     => 24
				],
				'selectors'    => [
					'{{WRAPPER}} .trx-addons-tabs-{{ID}}.trx-addons-tabs > .trx-addons-tabs-nav li .trx-addons-tabs-icon' => 'font-size: {{SIZE}}{{UNIT}};'
				]
			]
		);

		$this->add_responsive_control(
			'tabs_icon_box_line_height',
			[
				'label'        => __('Icon Line Height', 'trx_addons'),
				'type'         => Controls_Manager::SLIDER,
				'size_units'   => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'range'        => [
					'px' 	   => [
						'min'  => 0,
						'max'  => 200,
						'step' => 1
					],
				],
				'default'      => [
					'unit'     => 'px',
					'size'     => 50
				],
				'selectors'    => [
					'{{WRAPPER}} .trx-addons-tabs-{{ID}}.trx-addons-tabs > .trx-addons-tabs-nav li .trx-addons-tabs-icon' => 'line-height: {{SIZE}}{{UNIT}};'
				],
				'condition'    => [
					'tabs_icon_box_show' => 'yes'
				]
			]
		);

		$this->add_responsive_control(
			'tabs_icon_padding',
			[
				'label'        => __( 'Padding', 'trx_addons' ),
				'type'         => Controls_Manager::DIMENSIONS,
				'size_units'   => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors'    => [
					'{{WRAPPER}} .trx-addons-tabs-{{ID}}.trx-addons-tabs > .trx-addons-tabs-nav li.trx-addons-tabs-nav-item-icon-type-icon .trx-addons-tabs-icon' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
				]
			]
		);

		$this->add_responsive_control(
			'tabs_icon_margin',
			[
				'label'        => __('Margin', 'trx_addons'),
				'type'         => Controls_Manager::DIMENSIONS,
				'size_units'   => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'default'      => [
					'top'      => '0',
					'right'    => '10',
					'bottom'   => '0',
					'left'     => '0',
					'unit'     => 'px',
					'isLinked' => false
				],
				'selectors'    => [
					'{{WRAPPER}} .trx-addons-tabs-{{ID}}.trx-addons-tabs > .trx-addons-tabs-nav li .trx-addons-tabs-icon' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
				]
			]
		);

		$this->add_responsive_control(
			'tabs_navigation_icon_offset',
			[
				'label'        => __('Icon Offset', 'trx_addons'),
				'type'         => Controls_Manager::SLIDER,
				'size_units'   => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'range'        => [
					'px' 	   => [
						'min'  => -50,
						'max'  => 50,
						'step' => 1
					],
				],
				'default'      => [
					'unit'     => 'px',
					'size'     => 0
				],
				'selectors'    => [
					'{{WRAPPER}} .trx-addons-tabs-{{ID}}.trx-addons-tabs > .trx-addons-tabs-nav li .trx-addons-tabs-icon' => 'top: {{SIZE}}{{UNIT}};'
				]
			]
		);

		$this->add_control(
			'tabs_navigation_icon_border_type',
			[
				'label'     => __( 'Border Type', 'trx_addons' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'none',
				'options'   => [
					'none'   => __( 'None', 'trx_addons' ),
					'solid'  => __( 'Solid', 'trx_addons' ),
					'double' => __( 'Double', 'trx_addons' ),
					'dotted' => __( 'Dotted', 'trx_addons' ),
					'dashed' => __( 'Dashed', 'trx_addons' ),
				],
				'selectors' => [
					'{{WRAPPER}} .trx-addons-tabs-{{ID}}.trx-addons-tabs > .trx-addons-tabs-nav li.trx-addons-tabs-nav-item-icon-type-icon .trx-addons-tabs-icon' => 'border-style: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'tabs_navigation_icon_border_width',
			[
				'label'      => __( 'Border width', 'trx_addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .trx-addons-tabs-{{ID}}.trx-addons-tabs > .trx-addons-tabs-nav li.trx-addons-tabs-nav-item-icon-type-icon .trx-addons-tabs-icon' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'tabs_navigation_icon_border_type!' => 'none',
				],
			]
		);

		$this->add_responsive_control(
			'tabs_navigation_icon_border_radius',
			[
				'label' => __( 'Border radius', 'trx_addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', 'rem', '%' ],
				'selectors' => [
					'{{WRAPPER}} .trx-addons-tabs-{{ID}}.trx-addons-tabs > .trx-addons-tabs-nav li.trx-addons-tabs-nav-item-icon-type-icon .trx-addons-tabs-icon' => 'border-radius: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->start_controls_tabs('tabs_icon_style_tabs');

		// Normal State Tab
		$this->start_controls_tab('tabs_icon_normal', ['label' => esc_html__('Normal', 'trx_addons')]);

		$this->add_control(
			'tabs_navigation_icon_normal_color',
			[
				'label'     => esc_html__('Color', 'trx_addons'),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#0a1724',
				'selectors' => [
					'{{WRAPPER}} .trx-addons-tabs-{{ID}}.trx-addons-tabs > .trx-addons-tabs-nav li .trx-addons-tabs-icon' => 'color: {{VALUE}};',
					'{{WRAPPER}} .trx-addons-tabs-{{ID}}.trx-addons-tabs > .trx-addons-tabs-nav li .trx-addons-tabs-icon svg' => 'fill: {{VALUE}};'
				]
			]
		);

		$this->add_control(
			'tabs_navigation_icon_normal_bg_color',
			[
				'label'     => __( 'Background Color', 'trx_addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .trx-addons-tabs-{{ID}}.trx-addons-tabs > .trx-addons-tabs-nav li.trx-addons-tabs-nav-item-icon-type-icon .trx-addons-tabs-icon' => 'background-color: {{VALUE}};',
				]
			]
		);

		$this->add_control(
			'tabs_navigation_icon_normal_bd_color',
			[
				'label'     => __( 'Border Color', 'trx_addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .trx-addons-tabs-{{ID}}.trx-addons-tabs > .trx-addons-tabs-nav li.trx-addons-tabs-nav-item-icon-type-icon .trx-addons-tabs-icon' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'tabs_navigation_icon_border_type!' => 'none',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'tabs_navigation_icon_normal_box_shadow',
				'selector' => '{{WRAPPER}} .trx-addons-tabs-{{ID}}.trx-addons-tabs > .trx-addons-tabs-nav li.trx-addons-tabs-nav-item-icon-type-icon .trx-addons-tabs-icon'
			]
		);

		$this->end_controls_tab();

		// Active State Tab
		$this->start_controls_tab('tabs_icon_active', ['label' => esc_html__('Active', 'trx_addons')]);

		$this->add_control(
			'tabs_navigation_icon_active_color',
			[
				'label'     => esc_html__('Color', 'trx_addons'),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#0a1724',
				'selectors' => [
					'{{WRAPPER}} .trx-addons-tabs-{{ID}}.trx-addons-tabs > .trx-addons-tabs-nav li.active .trx-addons-tabs-icon, {{WRAPPER}} .trx-addons-tabs-{{ID}}.trx-addons-tabs > .trx-addons-tabs-nav li:hover .trx-addons-tabs-icon' => 'color: {{VALUE}};',
					'{{WRAPPER}} .trx-addons-tabs-{{ID}}.trx-addons-tabs > .trx-addons-tabs-nav li.active .trx-addons-tabs-icon svg, {{WRAPPER}} .trx-addons-tabs-{{ID}}.trx-addons-tabs > .trx-addons-tabs-nav li:hover .trx-addons-tabs-icon svg' => 'fill: {{VALUE}};'
				]
			]
		);

		$this->add_control(
			'tabs_navigation_icon_active_bg_color',
			[
				'label'     => __( 'Background Color', 'trx_addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .trx-addons-tabs-{{ID}}.trx-addons-tabs > .trx-addons-tabs-nav li.trx-addons-tabs-nav-item-icon-type-icon.active .trx-addons-tabs-icon, {{WRAPPER}} .trx-addons-tabs-{{ID}}.trx-addons-tabs > .trx-addons-tabs-nav li.trx-addons-tabs-nav-item-icon-type-icon:hover .trx-addons-tabs-icon' => 'background-color: {{VALUE}};',
				]
			]
		);

		$this->add_control(
			'tabs_navigation_icon_active_bd_color',
			[
				'label'     => __( 'Border Color', 'trx_addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .trx-addons-tabs-{{ID}}.trx-addons-tabs > .trx-addons-tabs-nav li.trx-addons-tabs-nav-item-icon-type-icon.active .trx-addons-tabs-icon, {{WRAPPER}} .trx-addons-tabs-{{ID}}.trx-addons-tabs > .trx-addons-tabs-nav li.trx-addons-tabs-nav-item-icon-type-icon:hover .trx-addons-tabs-icon' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'tabs_navigation_icon_border_type!' => 'none',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'tabs_navigation_icon_active_box_shadow',
				'selector' => '{{WRAPPER}} .trx-addons-tabs-{{ID}}.trx-addons-tabs > .trx-addons-tabs-nav li.trx-addons-tabs-nav-item-icon-type-icon.active .trx-addons-tabs-icon, {{WRAPPER}} .trx-addons-tabs-{{ID}}.trx-addons-tabs > .trx-addons-tabs-nav li.trx-addons-tabs-nav-item-icon-type-icon:hover .trx-addons-tabs-icon'
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'tabs_navigation_image_style',
			[
				'label'     => esc_html__('Image', 'trx_addons'),
				'type'      => Controls_Manager::HEADING,
				'separator' =>  'before'
			]
		);

		$this->add_responsive_control(
			'tabs_navigation_image_height',
			[
				'label'        => __('Image Height', 'trx_addons'),
				'type'         => Controls_Manager::SLIDER,
				'size_units'   => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'range'        => [
					'px' 	   => [
						'min'  => 0,
						'max'  => 200,
						'step' => 1
					],
					'em' 	   => [
						'min'  => 0,
						'max'  => 10,
						'step' => 0.1
					],
				],
				'default'      => [
					'unit'     => 'em',
					'size'     => 2
				],
				'selectors'    => [
					'{{WRAPPER}} .trx-addons-tabs-{{ID}}.trx-addons-tabs > .trx-addons-tabs-nav .trx-addons-tabs-image' => 'max-height: {{SIZE}}{{UNIT}};'
				],
			]
		);

		$this->add_responsive_control(
			'tabs_navigation_image_offset',
			[
				'label'        => __('Image Offset', 'trx_addons'),
				'type'         => Controls_Manager::SLIDER,
				'size_units'   => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'range'        => [
					'px' 	   => [
						'min'  => -50,
						'max'  => 50,
						'step' => 1
					],
				],
				'default'      => [
					'unit'     => 'px',
					'size'     => 0
				],
				'selectors'    => [
					'{{WRAPPER}} .trx-addons-tabs-{{ID}}.trx-addons-tabs > .trx-addons-tabs-nav li .trx-addons-tabs-image' => 'top: {{SIZE}}{{UNIT}};'
				]
			]
		);

		$this->add_responsive_control(
			'tabs_navigation_image_margin',
			[
				'label'        => __('Margin', 'trx_addons'),
				'type'         => Controls_Manager::DIMENSIONS,
				'size_units'   => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'default'      => [
					'top'      => '0',
					'right'    => '10',
					'bottom'   => '0',
					'left'     => '0',
					'unit'     => 'px',
					'isLinked' => false
				],
				'selectors'    => [
					'{{WRAPPER}} .trx-addons-tabs-{{ID}}.trx-addons-tabs > .trx-addons-tabs-nav li .trx-addons-tabs-image' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
				]
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Style Tab: Navigation Description
	 */
	protected function register_style_tabs_subtitle_controls() {

		$this->start_controls_section(
			'section_tabs_subtitle_style_settings',
			[
				'label' => __( 'Navigation Description', 'trx_addons' ),
				'tab'   => Controls_Manager::TAB_STYLE
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'label'    => __( 'Typography', 'trx_addons' ),
				'name'     => 'tabs_navigation_desc_typography',
				'selector' => '{{WRAPPER}} .trx-addons-tabs-{{ID}}.trx-addons-tabs > .trx-addons-tabs-nav li span.trx-addons-tabs-subtitle',
			]
		);

		$this->add_responsive_control(
			'tabs_navigation_subtitle_padding',
			[
				'label'        => __( 'Padding', 'trx_addons' ),
				'type'         => Controls_Manager::DIMENSIONS,
				'size_units'   => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors'    => [
					'{{WRAPPER}} .trx-addons-tabs-{{ID}}.trx-addons-tabs > .trx-addons-tabs-nav li .trx-addons-tabs-subtitle' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
				]
			]
		);

		$this->add_responsive_control(
			'tabs_navigation_subtitle_margin',
			[
				'label'      => __( 'Margin', 'trx_addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors'  => [
					'{{WRAPPER}} .trx-addons-tabs-{{ID}}.trx-addons-tabs > .trx-addons-tabs-nav li .trx-addons-tabs-subtitle' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
				]
			]
		);

		$this->start_controls_tabs( 'tabs_subtitle_style_tabs' );

		// Normal State Tab
		$this->start_controls_tab(
			'tabs_subtitle_normal',
			[
				'label' => __( 'Normal', 'trx_addons' ),
			]
		);

		$this->add_control(
			'tabs_navigation_subtitle_normal_color',
			[
				'label'     => __( 'Color', 'trx_addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .trx-addons-tabs-{{ID}}.trx-addons-tabs > .trx-addons-tabs-nav li .trx-addons-tabs-subtitle' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		// Active State Tab
		$this->start_controls_tab(
			'tabs_subtitle_active',
			[
				'label' => __( 'Active', 'trx_addons' ),
			]
		);

		$this->add_control(
			'tabs_navigation_subtitle_active_color',
			[
				'label'     => __( 'Color', 'trx_addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .trx-addons-tabs-{{ID}}.trx-addons-tabs > .trx-addons-tabs-nav li.active .trx-addons-tabs-subtitle,
					 {{WRAPPER}} .trx-addons-tabs-{{ID}}.trx-addons-tabs > .trx-addons-tabs-nav li:hover .trx-addons-tabs-subtitle' => 'color: {{VALUE}};',
				]
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * Register tabs content style controls
	 *
	 * @return void
	 */
	protected function register_style_tabs_content_controls() {
		$this->start_controls_section(
			'section_tabs_content_style_settings',
			[
				'label' => esc_html__('Content Area', 'trx_addons'),
				'tab'   => Controls_Manager::TAB_STYLE
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'tabs_content_description_typography',
				'selector' => '{{WRAPPER}} .trx-addons-tabs-{{ID}}.trx-addons-tabs > .trx-addons-tabs-content .trx-addons-tabs-content-description'
			]
		);

		$this->add_control(
			'tabs_content_description_color',
			[
				'label'     => esc_html__('Text Color', 'trx_addons'),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#0a1724',
				'selectors' => [
					'{{WRAPPER}} .trx-addons-tabs-{{ID}}.trx-addons-tabs > .trx-addons-tabs-content .trx-addons-tabs-content-description' => 'color: {{VALUE}};'
				]
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'tabs_content_background',
				'types'    => ['classic', 'gradient'],
				'selector' => '{{WRAPPER}} .trx-addons-tabs-{{ID}}.trx-addons-tabs > .trx-addons-tabs-content'
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'tabs_content_border',
				'selector' => '{{WRAPPER}} .trx-addons-tabs-{{ID}}.trx-addons-tabs > .trx-addons-tabs-content'
			]
		);

		$this->add_responsive_control(
			'tabs_content_radius',
			[
				'label'      => __('Border Radius', 'trx_addons'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units'   => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'default'    => [
					'top'      => '0',
					'right'    => '0',
					'bottom'   => '0',
					'left'     => '0',
					'unit'     => 'px',
					'isLinked' => true
				],
				'selectors'  => [
					'{{WRAPPER}} .trx-addons-tabs-{{ID}}.trx-addons-tabs > .trx-addons-tabs-content' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
				]
			]
		);

		$this->add_responsive_control(
			'tabs_content_padding',
			[
				'label'      => __('Padding', 'trx_addons'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units'   => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'default'    => [
					'top'      => '30',
					'right'    => '30',
					'bottom'   => '30',
					'left'     => '30',
					'unit'     => 'px',
					'isLinked' => true
				],
				'selectors'  => [
					'{{WRAPPER}} .trx-addons-tabs-{{ID}}.trx-addons-tabs > .trx-addons-tabs-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
				]
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Register tabs content image style controls
	 *
	 * @return void
	 */
	protected function register_style_tabs_content_image_controls() {

		$this->start_controls_section(
			'section_tabs_image_style_settings',
			[
				'label' => esc_html__('Content Image', 'trx_addons'),
				'tab'   => Controls_Manager::TAB_STYLE
			]
		);

		$this->add_responsive_control(
			'tabs_image_align',
			[
				'label'   => esc_html__('Image Position', 'trx_addons'),
				'type'    => Controls_Manager::CHOOSE,
				'toggle'  => false,
				'options' => [
					'left' => [
						'title' => esc_html__('Left', 'trx_addons'),
						'icon'  => 'eicon-h-align-left'
					],
					'top' => [
						'title' => esc_html__('Top', 'trx_addons'),
						'icon'  => 'eicon-v-align-top'
					],
					'right' => [
						'title' => esc_html__('Right', 'trx_addons'),
						'icon'  => 'eicon-h-align-right'
					]
				],
				'default' => 'top',
				'selectors_dictionary'  => [
					'top' => 'float: none;',
					'left' => 'float: left;',
					'right' => 'float: right;',
				],
				'selectors' => [
					'{{WRAPPER}} .trx-addons-tabs-{{ID}}.trx-addons-tabs > .trx-addons-tabs-content .trx-addons-tabs-content-thumb' => '{{VALUE}};'
				]
			]
		);

		$this->add_responsive_control(
			'tabs_image_width',
			[
				'label'        => __('Image Width', 'trx_addons'),
				'type'         => Controls_Manager::SLIDER,
				'size_units'   => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'range'        => [
					'px' 	   => [
						'min'  => 0,
						'max'  => 500,
						'step' => 1
					],
					'em' 	   => [
						'min'  => 0,
						'max'  => 20,
						'step' => 0.1
					],
					'%' 	   => [
						'min'  => 0,
						'max'  => 100,
						'step' => 1
					],
				],
				'default'      => [
					'unit'     => '%',
					'size'     => 100
				],
				'selectors'    => [
					'{{WRAPPER}} .trx-addons-tabs-{{ID}}.trx-addons-tabs > .trx-addons-tabs-content .trx-addons-tabs-content-thumb' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'tabs_image_margin',
			[
				'label'        => __('Margin', 'trx_addons'),
				'type'         => Controls_Manager::DIMENSIONS,
				'size_units'   => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'default'      => [
					'top'      => '0',
					'right'    => '0',
					'bottom'   => '20',
					'left'     => '0',
					'unit'     => 'px',
					'isLinked' => false
				],
				'selectors'    => [
					'{{WRAPPER}} .trx-addons-tabs-{{ID}}.trx-addons-tabs > .trx-addons-tabs-content .trx-addons-tabs-content-thumb' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				]
			]
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[
				'name' => 'tabs_image_css_filter',
				'selector' => '{{WRAPPER}} .trx-addons-tabs-{{ID}}.trx-addons-tabs > .trx-addons-tabs-content .trx-addons-tabs-content-thumb img',
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Register tabs content button style controls
	 *
	 * @return void
	 */
	protected function register_style_tabs_content_button_controls() {

		$accent_color = apply_filters( 'trx_addons_filter_get_theme_accent_color', '#efa758' );

		$this->start_controls_section(
			'section_tabs_btn_style',
			[
				'label' => esc_html__('Content Button', 'trx_addons'),
				'tab'   => Controls_Manager::TAB_STYLE
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'tabs_details_btn_typography',
				'label'     => __( 'Typography', 'trx_addons' ),
				'global'    => [
					'default' => Global_Typography::TYPOGRAPHY_ACCENT,
				],
				'selector' => '{{WRAPPER}} .trx-addons-tabs-btn'
			]
		);

		$this->start_controls_tabs( 'tabs_details_btn_tabs' );

		// Normal state tab
		$this->start_controls_tab(
			'tabs_details_btn_normal',
			[
				'label' => esc_html__('Normal', 'trx_addons')
			]
		);

		$this->add_control(
			'tabs_details_btn_normal_bg',
			[
				'label'     => esc_html__('Background Color', 'trx_addons'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .trx-addons-tabs-{{ID}}.trx-addons-tabs > .trx-addons-tabs-content .trx-addons-tabs-btn' => 'background: {{VALUE}};'
				]
			]
		);

		$this->add_control(
			'tabs_details_btn_normal_text_color',
			[
				'label'     => esc_html__('Text Color', 'trx_addons'),
				'type'      => Controls_Manager::COLOR,
				'default'   => $accent_color,
				'selectors' => [
					'{{WRAPPER}} .trx-addons-tabs-{{ID}}.trx-addons-tabs > .trx-addons-tabs-content .trx-addons-tabs-btn' => 'color: {{VALUE}};',
					'{{WRAPPER}} .trx-addons-tabs-{{ID}}.trx-addons-tabs > .trx-addons-tabs-content .trx-addons-tabs-btn .trx-addons-button-icon svg' => 'fill: {{VALUE}};'
				]
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'            => 'tabs_details_btn_normal_border',
				'label'       => __( 'Border', 'trx_addons' ),
				// 'fields_options'  => [
				// 	'border'      => [
				// 		'default' => 'solid'
				// 	],
				// 	'width'       => [
				// 		'default' => [
				// 			'top'    => '1',
				// 			'right'  => '1',
				// 			'bottom' => '1',
				// 			'left'   => '1'
				// 		]
				// 	],
				// 	'color'       => [
				// 		'default' => $accent_color
				// 	]
				// ],
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'        => '{{WRAPPER}} .trx-addons-tabs-{{ID}}.trx-addons-tabs > .trx-addons-tabs-content .trx-addons-tabs-btn'
			]
		);

		$this->add_responsive_control(
			'tabs_details_btn_radius',
			[
				'label'      => __('Border Radius', 'trx_addons'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units'   => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors'  => [
					'{{WRAPPER}} .trx-addons-tabs-{{ID}}.trx-addons-tabs > .trx-addons-tabs-content .trx-addons-tabs-btn' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
				]
			]
		);

		$this->add_responsive_control(
			'tabs_details_btn_padding',
			[
				'label'        => __('Padding', 'trx_addons'),
				'type'         => Controls_Manager::DIMENSIONS,
				'size_units'   => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors'    => [
					'{{WRAPPER}} .trx-addons-tabs-{{ID}}.trx-addons-tabs > .trx-addons-tabs-content .trx-addons-tabs-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
				]
			]
		);

		$this->add_responsive_control(
			'tabs_details_btn_margin',
			[
				'label'        => __('Margin', 'trx_addons'),
				'type'         => Controls_Manager::DIMENSIONS,
				'size_units'   => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'default'      => [
					'top'      => '20',
					'right'    => '0',
					'bottom'   => '0',
					'left'     => '0',
					'unit'     => 'px',
					'isLinked' => false
				],
				'selectors'    => [
					'{{WRAPPER}} .trx-addons-tabs-{{ID}}.trx-addons-tabs > .trx-addons-tabs-content .trx-addons-tabs-btn' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
				]
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'tabs_details_btn_normal_box_shadow',
				'selector' => '{{WRAPPER}} .trx-addons-tabs-{{ID}}.trx-addons-tabs > .trx-addons-tabs-content .trx-addons-tabs-btn'
			]
		);

		$this->add_control(
			'tabs_details_btn_icon_heading',
			array(
				'label'     => __( 'Button Icon', 'trx_addons' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'tabs_details_btn_switcher' => 'yes',
					'tabs_content_type' => 'content',
					'select_tabs_details_btn_icon[value]!' => ''
				],
			)
		);

		$this->add_responsive_control(
			'tabs_details_btn_icon_margin',
			array(
				'label'       => __( 'Margin', 'trx_addons' ),
				'type'        => Controls_Manager::DIMENSIONS,
				'size_units'  => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'condition' => [
					'tabs_details_btn_switcher' => 'yes',
					'tabs_content_type' => 'content',
					'select_tabs_details_btn_icon[value]!' => ''
				],
				'selectors'   => array(
					'{{WRAPPER}} .trx-addons-tabs-{{ID}}.trx-addons-tabs > .trx-addons-tabs-content .trx-addons-tabs-btn .trx-addons-button-icon' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_tab();

		// Hover state tab
		$this->start_controls_tab(
			'tabs_details_btn_hover',
			[
				'label' => esc_html__('Hover', 'trx_addons')
			]
		);

		$this->add_control(
			'tabs_details_btn_hover_bg',
			[
				'label'     => esc_html__('Background Color', 'trx_addons'),
				'type'      => Controls_Manager::COLOR,
				'default'   => $accent_color,
				'selectors' => [
					'{{WRAPPER}} .trx-addons-tabs-{{ID}}.trx-addons-tabs > .trx-addons-tabs-content .trx-addons-tabs-btn:hover' => 'background: {{VALUE}};'
				]
			]
		);

		$this->add_control(
			'tabs_details_btn_hover_text_color',
			[
				'label'     => esc_html__('Text Color', 'trx_addons'),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .trx-addons-tabs-{{ID}}.trx-addons-tabs > .trx-addons-tabs-content .trx-addons-tabs-btn:hover' => 'color: {{VALUE}};',
					'{{WRAPPER}} .trx-addons-tabs-{{ID}}.trx-addons-tabs > .trx-addons-tabs-content .trx-addons-tabs-btn:hover .trx-addons-button-icon svg' => 'fill: {{VALUE}};'
				]
			]
		);

		$this->add_control(
			'tabs_details_btn_hover_border_color',
			array(
				'label'     => __( 'Border Color', 'trx_addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .trx-addons-tabs-{{ID}}.trx-addons-tabs > .trx-addons-tabs-content .trx-addons-tabs-btn:hover' => 'border-color: {{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'tabs_details_btn_hover_box_shadow',
				'selector' => '{{WRAPPER}} .trx-addons-tabs-{{ID}}.trx-addons-tabs > .trx-addons-tabs-content .trx-addons-tabs-btn:hover'
			]
		);

		$this->add_control(
			'tabs_details_btn_animation',
			array(
				'label'     => __( 'Animation', 'trx_addons' ),
				'type'      => Controls_Manager::HOVER_ANIMATION,
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 *  Get Saved Widgets
	 *
	 *  @param string $type  Type of template.
	 * 
	 *  @return string  Saved Widgets
	 */
	public function get_saved_template($type = 'page') {
		$saved_widgets = $this->get_post_template( $type );
		$options[-1]   = __( 'Select', 'trx_addons' );
		if ( count( $saved_widgets ) ) {
			foreach ( $saved_widgets as $saved_row ) {
				$options[ $saved_row['id'] ] = $saved_row['name'];
			}
		} else {
			$options['no_template'] = __('No section template is added.', 'trx_addons');
		}
		return $options;
	}

	/**
	 *  Get Templates based on category
	 *
	 *  @param string $type  Type of template.
	 * 
	 *  @return string  Template content
	 */
	public function get_post_template($type = 'page') {

		$posts = get_posts(array(
			'post_type'        => 'elementor_library',
			'orderby'          => 'title',
			'order'            => 'ASC',
			'posts_per_page'   => '-1',
			'tax_query'        => array(
				array(
					'taxonomy' => 'elementor_library_type',
					'field'    => 'slug',
					'terms'    => $type
				)
			)
		) );

		$templates = array();

		if ( is_array( $posts ) ) {
			foreach ( $posts as $post ) {
				$templates[] = array(
					'id'   => $post->ID,
					'name' => $post->post_title
				);
			}
		}

		return $templates;
	}


	/*-----------------------------------------------------------------------------------*/
	/*	RENDER
	/*-----------------------------------------------------------------------------------*/

	protected function render() {

		$settings = $this->get_settings_for_display();

		$this->add_render_attribute(
			'tabs_wrapper',
			[
				'class'	 => [
					'trx-addons-tabs',
					'trx-addons-tabs-' . $this->get_id(),
					esc_attr( $settings['tabs_orientation'] ),
					esc_attr( $settings['tabs_navigation_alignment'] ),
					esc_attr( $settings['tabs_navigation_alignment_vertical'] ),
				]
			]
		);
		?><div <?php echo $this->get_render_attribute_string('tabs_wrapper'); ?> data-tabs>
			<ul class="trx-addons-tabs-nav"><?php
				foreach ( $settings['tabs'] as $tab ) {

					$tab_nav_item_classes = array();

					if ( ! empty( $tab['tabs_icon_type'] ) && $tab['tabs_icon_type'] != 'none' ) {
						$tab_nav_item_classes[] = 'trx-addons-tabs-nav-item-icon-type-' . $tab['tabs_icon_type'];
					}

					if ( ! empty( $tab['tabs_subtitle'] ) ) {
						$tab_nav_item_classes[] = 'trx-addons-tabs-nav-item-with-desc';
					}
					?><li data-tab class="trx-addons-tabs-nav-item <?php echo esc_attr( $tab['tabs_show_as_default'] ); ?> <?php echo esc_attr( implode( ' ', $tab_nav_item_classes ) ); ?>"><?php
						if ( 'icon' === $tab['tabs_icon_type'] && ! empty( $tab['tabs_title_icon']['value'] ) ) {
							?><span class="trx-addons-tabs-icon trx-addons-icon"><?php
								Icons_Manager::render_icon( $tab['tabs_title_icon'] );
							?></span><?php
						} else if ( $tab['tabs_icon_type'] === 'image' ) {
							if ( $tab['tabs_title_image']['url'] || $tab['tabs_title_image']['id'] ) {
								?><span class="trx-addons-tabs-icon trx-addons-tabs-image trx-addons-icon"><?php
									echo Group_Control_Image_Size::get_attachment_image_html($tab, 'tabs_navigation_image_size', 'tabs_title_image');
								?></span><?php
							}
						}
						$title_tag = TrxAddonsUtils::validate_html_tag( $settings['tab_title_html_tag'] );
						?><<?php echo esc_html( $title_tag ); ?> class="trx-addons-tabs-title"><?php echo wp_kses( $tab['tabs_title'], 'trx_addons_kses_content' ); ?></<?php echo esc_html( $title_tag ); ?>><?php
						if ( ! empty( $tab['tabs_subtitle'] ) ) {
							?><span class="trx-addons-tabs-subtitle"><?php echo wp_kses( $tab['tabs_subtitle'], 'trx_addons_kses_content' ); ?></span><?php
						}
						?>
					</li><?php
				}
			?></ul><?php

			foreach ( $settings['tabs'] as $key => $tab ) {
				$has_image = !empty($tab['tabs_image']['url']) ? 'yes' : 'no';
				$link_key  = 'link_' . $key;
				$link_icon_key  = 'link_icon_' . $key;

				if ( 'content' === $tab['tabs_content_type'] ) {
					$tabs_btn_link = $tab['tabs_details_btn_link']['url'];
					$this->add_render_attribute( $link_key, 'class', array(
						'trx-addons-tabs-btn',
						'trx-addons-tabs-btn-icon-' . $tab['tabs_details_btn_icon_position'],
						'elementor-button'
					) );
					if ( ! empty( $tabs_btn_link ) ) {
						$this->add_render_attribute( $link_key, 'href', esc_url( $tabs_btn_link ) );
						if ( $tab['tabs_details_btn_link']['is_external'] ) {
							$this->add_render_attribute( $link_key, 'target', '_blank' );
						}
						if ( $tab['tabs_details_btn_link']['nofollow'] ) {
							$this->add_render_attribute( $link_key, 'rel', 'nofollow' );
						}
					}
					if ( $settings['tabs_details_btn_animation'] ) {
						$this->add_render_attribute( $link_key, 'class', 'elementor-animation-' . $settings['tabs_details_btn_animation'] );
					}
					if ( ! isset( $tab['tabs_details_btn_icon'] ) && ! Icons_Manager::is_migration_allowed() ) {
						// add old default.
						$tab['tabs_details_btn_icon'] = '';
					}
			
					$has_icon = ! empty( $tab['tabs_details_btn_icon'] );
			
					if ( $has_icon ) {
						$this->add_render_attribute( $link_icon_key, 'class', $tab['tabs_details_btn_icon'] );
						$this->add_render_attribute( $link_icon_key, 'aria-hidden', 'true' );
					}
			
					if ( ! $has_icon && ! empty( $tab['select_tabs_details_btn_icon']['value'] ) ) {
						$has_icon = true;
					}
					$migrated = isset( $tab['__fa4_migrated']['select_tabs_details_btn_icon'] );
					$is_new   = ! isset( $tab['tabs_details_btn_icon'] ) && Icons_Manager::is_migration_allowed();
				}
				?><div class="trx-addons-tabs-content trx-addons-tabs-image-has-<?php echo esc_attr( $has_image ) . ' ' . esc_attr( $tab['tabs_show_as_default'] ); ?>"><?php
					if ( 'save_template' === $tab['tabs_content_type'] ) {
						?><div class="trx-addons-tabs-content-element"><?php
							echo Plugin::$instance->frontend->get_builder_content_for_display( wp_kses_post( $tab['tabs_content_save_template'] ) );
						?></div><?php
					} else if ( 'shortcode' === $tab['tabs_content_type'] ) {
						echo do_shortcode( $tab['tabs_content_shortcode'] );
					} else if ( 'section' === $tab['tabs_content_type'] ) {
						if ( ! isset( $GLOBALS['TRX_ADDONS_STORAGE']['catch_output']['trx_elm_tabs'] ) ) {
							$GLOBALS['TRX_ADDONS_STORAGE']['catch_output']['trx_elm_tabs'] = array();
						}
						$GLOBALS['TRX_ADDONS_STORAGE']['catch_output']['trx_elm_tabs'][ trim( $tab['tabs_section_id'] ) ] = 1;
						$this->add_render_attribute(
							'tabs_section_element_' . $key,
							array(
								'class' => array(
									'trx-addons-tabs-content-section',
									'trx-addons-tabs-content-element',
								),
								'data-section' => esc_attr( trim( $tab['tabs_section_id'] ) ),
							)
						);
						?><div <?php echo $this->get_render_attribute_string( 'tabs_section_element_' . $key ); ?>></div><?php
					} else {
						if ( ! empty( $tab['tabs_image']['url'] ) ) {
							?><div class="trx-addons-tabs-content-thumb"><?php
								echo Group_Control_Image_Size::get_attachment_image_html( $tab, 'tabs_image_size', 'tabs_image' );
							?></div><?php
						}
						if ( ! empty( $tab['tabs_content'] ) || 'yes' === $tab['tabs_details_btn_switcher'] ) {
							?><div class="trx-addons-tabs-content-element">
								<div class="trx-addons-tabs-content-description"><?php echo wp_kses_post( $tab['tabs_content'] ); ?></div><?php
								if ( 'yes' === $tab['tabs_details_btn_switcher'] ) {
									// Button
									echo '<a ' . $this->get_render_attribute_string( $link_key ) . '>';
										// Icon before
										if ( 'before' === $tab['tabs_details_btn_icon_position'] && $has_icon ) {
											?><span class='trx-addons-button-icon trx-addons-icon'><?php
												if ( $is_new || $migrated ) {
													Icons_Manager::render_icon( $tab['select_tabs_details_btn_icon'], array( 'aria-hidden' => 'true' ) );
												} else if ( ! empty( $tab['tabs_details_btn_icon'] ) ) {
													?><i <?php $this->print_render_attribute_string( $link_icon_key ); ?>></i><?php
												}
											?></span><?php
										}
										// Text
										echo esc_html( $tab['tabs_details_btn_text'] );
										// Icon after
										if ( 'after' === $tab['tabs_details_btn_icon_position'] && $has_icon ) {
											?><span class='trx-addons-button-icon trx-addons-icon'><?php
												if ( $is_new || $migrated ) {
													Icons_Manager::render_icon( $tab['select_tabs_details_btn_icon'], array( 'aria-hidden' => 'true' ) );
												} else if ( ! empty( $tab['tabs_details_btn_icon'] ) ) {
													?><i <?php $this->print_render_attribute_string( $link_icon_key ); ?>></i><?php
												}
											?></span><?php
										}
									echo '</a>';
								}
							?></div><?php
						}
					}
				?></div><?php
			}
		?></div><?php
	}

	/*-----------------------------------------------------------------------------------*/
	/*	BUFFERING
	/*-----------------------------------------------------------------------------------*/

	/**
	 * Disable Elementor's cache if the tabs is present on the current page and any tab content contains a section
	 * 
	 * @hooked elementor/frontend/builder_content_data
	 * 
	 * @param array $data       The content data.
	 * @param int   $post_id    The post ID.
	 * @param bool  $recursive  The flag about recursive call.
	 * 
	 * @return array  The content data.
	 */
	public function disable_cache_if_catch_output( $data, $post_id, $recursive = false ) {
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
					$this->disable_cache_if_catch_output( $data[ $i ]['elements'], $post_id, true );
				} else if ( ! empty( $data[ $i ]['widgetType'] ) && $data[ $i ]['widgetType'] == 'trx_elm_tabs' && ! empty( $data[ $i ]['settings']['tabs'] ) && is_array( $data[ $i ]['settings']['tabs'] ) ) {
					for ( $t = 0; $t < count( $data[ $i ]['settings']['tabs'] ); $t++ ) {
						if ( ! empty( $data[ $i ]['settings']['tabs'][ $t ]['tabs_content_type'] ) && $data[ $i ]['settings']['tabs'][ $t ]['tabs_content_type'] == 'section' ) {
							$need_clear_cache = true;
							break;
						}
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

	/**
	 * Enable Elementor's cache after the page content is rendered
	 * 
	 * @hooked elementor/frontend/get_builder_content
	 * 
	 * @param Elementor\Document $document   The document.
	 * @param bool               $is_excerpt The flag about excerpt.
	 * @param bool               $with_css   The flag about CSS.
	 */
	public function enable_cache_after_catch_output ( $document, $is_excerpt, $with_css ) {
		global $TRX_ADDONS_STORAGE;
		$post_id = $document->get_main_id();
		if ( ! empty( $TRX_ADDONS_STORAGE['should_render_shortcode'][ $post_id ] ) ) {
			remove_filter( 'elementor/element/should_render_shortcode', '__return_false', 100 );
			unset( $TRX_ADDONS_STORAGE['should_render_shortcode'][ $post_id ] );
		}
	}

	/**
	 * Start buffering for the element with the section content
	 * 
	 * @hooked elementor/frontend/before_render
	 * 
	 * @param Elementor\Element_Base $element The element.
	 */
	public function register_buffering_start_catch_output( $element ) {
		global $TRX_ADDONS_STORAGE;
		if ( ! empty( $TRX_ADDONS_STORAGE['capture_page'] ) && ! trx_addons_is_preview( 'elementor' ) ) {
			if ( is_object( $element ) ) {
				$id = $element->get_settings( '_element_id' );
				if ( ! empty( $id ) && ! empty( $TRX_ADDONS_STORAGE['catch_output']['trx_elm_tabs'][ $id ] ) ) {
					ob_start();
				}
			}
		}
	}

	/**
	 * End buffering for the element with the section content
	 * 
	 * @hooked elementor/frontend/after_render
	 * 
	 * @param Elementor\Element_Base $element The element.
	 */
	public function register_buffering_end_catch_output( $element ) {
		global $TRX_ADDONS_STORAGE;
		if ( ! empty( $TRX_ADDONS_STORAGE['capture_page'] ) && ! trx_addons_is_preview( 'elementor' ) ) {
			if ( is_object( $element ) ) {
				$id = $element->get_settings( '_element_id' );
				if ( ! empty( $id ) && ! empty( $TRX_ADDONS_STORAGE['catch_output']['trx_elm_tabs'][ $id ] ) ) {
					$TRX_ADDONS_STORAGE['catch_output']['trx_elm_tabs'][ $id ] = ob_get_contents();
					ob_end_clean();
				}
			}
		}
	}

	/**
	 * Replace the empty section content with the buffered content in the page output
	 * 
	 * @hooked trx_addons_filter_page_content
	 * 
	 * @param string $output  The content of the page.
	 * 
	 * @return string  Modified content of the page.
	 */
	public function register_buffering_paste_catch_output( $output ) {
		global $TRX_ADDONS_STORAGE;
		if ( ! trx_addons_is_preview( 'elementor' ) ) {
			if ( ! empty( $TRX_ADDONS_STORAGE['catch_output']['trx_elm_tabs'] ) && is_array( $TRX_ADDONS_STORAGE['catch_output']['trx_elm_tabs'] ) ) {
				foreach( $TRX_ADDONS_STORAGE['catch_output']['trx_elm_tabs'] as $id => $html ) {
					$output = preg_replace(
						'/(<div[^>]*class="trx-addons-tabs-content-section[^>]*data-section="' . esc_attr( $id ) . '"[^>]*>)[\s]*<\/div>/',
						'${1}' . $html . '</div>',
						$output );
				}
			}
		}
		return $output;
	}
}