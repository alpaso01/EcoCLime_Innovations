<?php
/**
 * Flip Box Widget
 *
 * @package ThemeREX Addons
 * @since v2.30.0
 */

namespace TrxAddons\ElementorWidgets\Widgets\FlipBox;

use TrxAddons\ElementorWidgets\BaseWidget;

// Elementor Classes.
use Elementor\Controls_Manager;
use Elementor\Utils;
use Elementor\Icons_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Image_Size;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Modules\DynamicTags\Module as TagsModule;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Flip Box Widget
 */
class FlipBoxWidget extends BaseWidget {

	/**
	 * Register widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 */
	protected function register_controls() {
		/* Content Tab */
		$this->register_content_flipbox_controls();
		$this->register_content_front_controls();
		$this->register_content_back_controls();
		$this->register_content_help_docs_controls();

		/* Style Tab */
		$this->register_style_flipbox_controls();
		$this->register_style_front_controls();
		$this->register_style_back_controls();
		$this->register_style_button_controls();
	}


	/*-----------------------------------------------------------------------------------*/
	/*	CONTENT TAB
	/*-----------------------------------------------------------------------------------*/

	/**
	 * Content Tab: Flip Box
	 */
	protected function register_content_flipbox_controls() {
		$this->start_controls_section(
			'section_flipbox',
			[
				'label'                 => esc_html__( 'Flip Box', 'trx_addons' ),
			]
		);

		$this->add_control(
			'show_back',
			[
				'label'                 => esc_html__( 'Show Back in preview', 'trx_addons' ),
				'label_block'           => false,
				'description'           => esc_html__( 'Display the backside of the widget in the preview area and lock it for easy customization of content and styles. Does not affect the frontend.', 'trx_addons' ),
				'type'                  => Controls_Manager::SWITCHER,
				'return_value'          => 'show',
				'prefix_class'          => 'trx-addons-flipbox-back-',
			]
		);

		$this->add_control(
			'flip_effect',
			[
				'label'                 => esc_html__( 'Flip Effect', 'trx_addons' ),
				'type'                  => Controls_Manager::SELECT,
				'default'               => 'flip',
				'label_block'           => false,
				'options'               => [
					'flip'     => esc_html__( 'Flip', 'trx_addons' ),
					'slide'    => esc_html__( 'Slide', 'trx_addons' ),
					'push'     => esc_html__( 'Push', 'trx_addons' ),
					'zoom-in'  => esc_html__( 'Zoom In', 'trx_addons' ),
					'zoom-out' => esc_html__( 'Zoom Out', 'trx_addons' ),
					'fade'     => esc_html__( 'Fade', 'trx_addons' ),
				],
				'separator'             => 'before',
			]
		);

		$this->add_control(
			'flip_direction',
			[
				'label'                 => esc_html__( 'Flip Direction', 'trx_addons' ),
				'type'                  => Controls_Manager::SELECT,
				'default'               => 'left',
				'label_block'           => false,
				'options'               => [
					'left'     => esc_html__( 'Left', 'trx_addons' ),
					'right'    => esc_html__( 'Right', 'trx_addons' ),
					'up'       => esc_html__( 'Top', 'trx_addons' ),
					'down'     => esc_html__( 'Bottom', 'trx_addons' ),
				],
				'condition'             => [
					'flip_effect!' => [
						'fade',
						'zoom-in',
						'zoom-out',
					],
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Content Tab: Front
	 */
	protected function register_content_front_controls() {
		$this->start_controls_section(
			'section_front',
			[
				'label' => esc_html__( 'Front', 'trx_addons' ),
			]
		);

		$this->add_control(
			'icon_type',
			[
				'label'       => esc_html__( 'Icon Type', 'trx_addons' ),
				'type'        => Controls_Manager::CHOOSE,
				'label_block' => false,
				'options'     => [
					'none'  => [
						'title' => esc_html__( 'None', 'trx_addons' ),
						'icon'  => 'eicon-ban',
					],
					'icon'  => [
						'title' => esc_html__( 'Icon', 'trx_addons' ),
						'icon'  => 'eicon-star',
					],
					'image' => [
						'title' => esc_html__( 'Image', 'trx_addons' ),
						'icon'  => 'eicon-image-bold',
					],
					'text'  => [
						'title' => esc_html__( 'Text', 'trx_addons' ),
						'icon'  => 'eicon-font',
					],
				],
				'default'     => 'icon',
			]
		);

		$this->add_control(
			'icon_image',
			[
				'label'     => esc_html__( 'Choose Image', 'trx_addons' ),
				'type'      => Controls_Manager::MEDIA,
				'dynamic'   => array(
					'active' => true,
				),
				'default'   => [
					'url' => Utils::get_placeholder_image_src(),
				],
				'condition' => [
					'icon_type' => 'image',
				],
			]
		);

		$this->add_control(
			'select_icon',
			[
				'label'            => __( 'Icon', 'trx_addons' ),
				'type'             => Controls_Manager::ICONS,
				'fa4compatibility' => 'icon',
				'default'          => [
					'value'   => 'fas fa-star',
					'library' => 'fa-solid',
				],
				'condition'        => [
					'icon_type' => 'icon',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name'      => 'thumbnail',
				'default'   => 'full',
				'condition' => [
					'icon_type'        => 'image',
					'icon_image[url]!' => '',
				],
			]
		);

		$this->add_control(
			'icon_text',
			array(
				'label'     => __( 'Icon Text', 'trx_addons' ),
				'type'      => Controls_Manager::TEXT,
				'dynamic'   => array(
					'active' => true,
				),
				'default'   => '1',
				'condition' => array(
					'icon_type' => 'text',
				),
			)
		);

		$this->add_control(
			'title_front',
			[
				'label'       => esc_html__( 'Title', 'trx_addons' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array(
					'active' => true,
				),
				'label_block' => true,
				'default'     => esc_html__( 'This is the heading', 'trx_addons' ),
				'separator'   => 'before',
			]
		);
		$this->add_control(
			'description_front',
			[
				'label'       => esc_html__( 'Description', 'trx_addons' ),
				'type'        => Controls_Manager::TEXTAREA,
				'dynamic'     => array(
					'active' => true,
				),
				'label_block' => true,
				'default'     => __( 'This is the front content. Click edit button to change this text. Lorem ipsum dolor sit amet consectetur adipiscing elit dolor', 'trx_addons' ),
			]
		);

		$this->add_control(
			'title_html_tag_front',
			array(
				'label'   => __( 'Title HTML Tag', 'trx_addons' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'h3',
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
	 * Content Tab: Back
	 */
	protected function register_content_back_controls() {
		$this->start_controls_section(
			'section_back',
			[
				'label' => esc_html__( 'Back', 'trx_addons' ),
			]
		);

		$this->add_control(
			'icon_type_back',
			[
				'label'       => esc_html__( 'Icon Type', 'trx_addons' ),
				'type'        => Controls_Manager::CHOOSE,
				'label_block' => false,
				'options'     => [
					'none'  => [
						'title' => esc_html__( 'None', 'trx_addons' ),
						'icon'  => 'eicon-ban',
					],
					'icon'  => [
						'title' => esc_html__( 'Icon', 'trx_addons' ),
						'icon'  => 'eicon-star',
					],
					'image' => [
						'title' => esc_html__( 'Image', 'trx_addons' ),
						'icon'  => 'eicon-image-bold',
					],
					'text'  => [
						'title' => esc_html__( 'Text', 'trx_addons' ),
						'icon'  => 'eicon-font',
					],
				],
				'default'     => 'icon',
			]
		);

		$this->add_control(
			'icon_image_back',
			[
				'label'     => esc_html__( 'Flipbox Image', 'trx_addons' ),
				'type'      => Controls_Manager::MEDIA,
				'dynamic'   => array(
					'active' => true,
				),
				'default'   => [
					'url' => Utils::get_placeholder_image_src(),
				],
				'condition' => [
					'icon_type_back' => 'image',
				],
			]
		);

		$this->add_control(
			'select_icon_back',
			[
				'label'            => __( 'Icon', 'trx_addons' ),
				'type'             => Controls_Manager::ICONS,
				'fa4compatibility' => 'icon_back',
				'default'          => [
					'value'     => 'far fa-snowflake',
					'library'   => 'fa-regular',
				],
				'condition'        => [
					'icon_type_back' => 'icon',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name'        => 'thumbnail_back',
				'default'     => 'full',
				'condition'   => [
					'icon_type_back'        => 'image',
					'icon_image_back[url]!' => '',
				],
			]
		);

		$this->add_control(
			'icon_text_back',
			array(
				'label'     => __( 'Icon Text', 'trx_addons' ),
				'type'      => Controls_Manager::TEXT,
				'dynamic'   => array(
					'active' => true,
				),
				'default'   => '1',
				'condition' => array(
					'icon_type_back' => 'text',
				),
			)
		);

		$this->add_control(
			'title_back',
			[
				'label'       => esc_html__( 'Title', 'trx_addons' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array(
					'active' => true,
				),
				'label_block' => true,
				'default'     => esc_html__( 'This is the heading', 'trx_addons' ),
				'separator'   => 'before',
			]
		);

		$this->add_control(
			'description_back',
			[
				'label'       => esc_html__( 'Description', 'trx_addons' ),
				'type'        => Controls_Manager::TEXTAREA,
				'dynamic'     => array(
					'active' => true,
				),
				'label_block' => true,
				'default'     => __( 'This is the front content. Click edit button to change this text. Lorem ipsum dolor sit amet consectetur adipiscing elit dolor', 'trx_addons' ),
			]
		);

		$this->add_control(
			'title_html_tag_back',
			array(
				'label'   => __( 'Title HTML Tag', 'trx_addons' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'h3',
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
			'link_type',
			[
				'label'                 => __( 'Link Type', 'trx_addons' ),
				'type'                  => Controls_Manager::SELECT,
				'default'               => 'none',
				'options'               => [
					'none'      => __( 'None', 'trx_addons' ),
					'title'     => __( 'Title', 'trx_addons' ),
					'button'    => __( 'Button', 'trx_addons' ),
					'box'       => __( 'Box', 'trx_addons' ),
				],
			]
		);

		$this->add_control(
			'link',
			[
				'label'                 => __( 'Link', 'trx_addons' ),
				'type'                  => Controls_Manager::URL,
				'dynamic'               => [
					'active'        => true,
					'categories'    => [
						TagsModule::POST_META_CATEGORY,
						TagsModule::URL_CATEGORY,
					],
				],
				'placeholder'           => 'https://www.your-link.com',
				'default'               => [
					'url' => '#',
				],
				'condition'             => [
					'link_type!'   => 'none',
				],
			]
		);

		$this->add_control(
			'flipbox_button_text',
			[
				'label'                 => __( 'Button Text', 'trx_addons' ),
				'type'                  => Controls_Manager::TEXT,
				'dynamic'               => [
					'active'   => true,
				],
				'default'               => __( 'Get Started', 'trx_addons' ),
				'condition'             => [
					'link_type'   => 'button',
				],
			]
		);

		$this->add_control(
			'select_button_icon',
			[
				'label'                 => __( 'Button Icon', 'trx_addons' ),
				'type'                  => Controls_Manager::ICONS,
				'fa4compatibility'      => 'button_icon',
				'condition'             => [
					'link_type'   => 'button',
				],
			]
		);

		$this->add_control(
			'button_icon_position',
			[
				'label'                 => __( 'Icon Position', 'trx_addons' ),
				'type'                  => Controls_Manager::SELECT,
				'default'               => 'after',
				'options'               => [
					'after'     => __( 'After', 'trx_addons' ),
					'before'    => __( 'Before', 'trx_addons' ),
				],
				'condition'             => [
					'link_type'     => 'button',
					'select_button_icon[value]!'  => '',
				],
			]
		);

		$this->end_controls_section();
	}

	/*-----------------------------------------------------------------------------------*/
	/*	STYLE TAB
	/*-----------------------------------------------------------------------------------*/

	/**
	 * Content Tab: Common
	 */
	protected function register_style_flipbox_controls() {
		$this->start_controls_section(
			'section_flipbox_style',
			[
				'label'                 => esc_html__( 'Flip Box', 'trx_addons' ),
				'tab'                   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'height',
			[
				'label'                 => __( 'Height', 'trx_addons' ),
				'type'                  => Controls_Manager::SLIDER,
				'size_units'            => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'range'                 => [
					'px' => [
						'min' => 100,
						'max' => 1000,
					],
					'vh' => [
						'min' => 10,
						'max' => 100,
					],
				],
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-flipbox-container' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'border_radius',
			[
				'label'                 => __( 'Border Radius', 'trx_addons' ),
				'type'                  => Controls_Manager::SLIDER,
				'size_units'            => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'range'                 => [
					'px' => [
						'min' => 0,
						'max' => 200,
					],
				],
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-flipbox-back, {{WRAPPER}} .trx-addons-flipbox-front' => 'border-radius: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Style Tab: Front
	 */
	protected function register_style_front_controls() {
		$this->start_controls_section(
			'section_front_style',
			[
				'label'                 => esc_html__( 'Front', 'trx_addons' ),
				'tab'                   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'padding_front',
			[
				'label'                 => esc_html__( 'Padding', 'trx_addons' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-flipbox-front .trx-addons-flipbox-overlay' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'content_alignment_front',
			[
				'label'                 => esc_html__( 'Alignment', 'trx_addons' ),
				'type'                  => Controls_Manager::CHOOSE,
				'label_block'           => false,
				'options'               => [
					'left' => [
						'title'   => esc_html__( 'Left', 'trx_addons' ),
						'icon'    => 'eicon-text-align-left',
					],
					'center' => [
						'title'   => esc_html__( 'Center', 'trx_addons' ),
						'icon'    => 'eicon-text-align-center',
					],
					'right' => [
						'title'   => esc_html__( 'Right', 'trx_addons' ),
						'icon'    => 'eicon-text-align-right',
					],
				],
				'default'               => 'center',
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-flipbox-front .trx-addons-flipbox-overlay' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'vertical_position_front',
			[
				'label'                 => __( 'Vertical Position', 'trx_addons' ),
				'type'                  => Controls_Manager::CHOOSE,
				'label_block'           => false,
				'options'               => [
					'top' => [
						'title' => __( 'Top', 'trx_addons' ),
						'icon' => 'eicon-v-align-top',
					],
					'middle' => [
						'title' => __( 'Middle', 'trx_addons' ),
						'icon' => 'eicon-v-align-middle',
					],
					'bottom' => [
						'title' => __( 'Bottom', 'trx_addons' ),
						'icon' => 'eicon-v-align-bottom',
					],
				],
				'selectors_dictionary'  => [
					'top' => 'flex-start',
					'middle' => 'center',
					'bottom' => 'flex-end',
				],
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-flipbox-front .trx-addons-flipbox-overlay' => 'justify-content: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'                  => 'background_front',
				'types'                 => [ 'classic', 'gradient' ],
				'selector'              => '{{WRAPPER}} .trx-addons-flipbox-front',
				'separator'             => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'                  => 'border_front',
				'label'                 => esc_html__( 'Border Style', 'trx_addons' ),
				'selector'              => '{{WRAPPER}} .trx-addons-flipbox-front',
				'separator'             => 'before',
			]
		);

		$this->add_control(
			'overlay_front',
			[
				'label'                 => esc_html__( 'Overlay', 'trx_addons' ),
				'type'                  => Controls_Manager::HEADING,
				'separator'             => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'                  => 'overlay_front',
				'types'                 => [ 'classic', 'gradient' ],
				'exclude'               => [ 'image' ],
				'selector'              => '{{WRAPPER}} .trx-addons-flipbox-front .trx-addons-flipbox-overlay',
			]
		);

		$this->add_control(
			'content_front_heading',
			[
				'label'                 => esc_html__( 'Content', 'trx_addons' ),
				'type'                  => Controls_Manager::HEADING,
				'separator'             => 'before',
			]
		);

		$this->add_control(
			'content_background_color_front',
			[
				'label'                 => esc_html__( 'Background Color', 'trx_addons' ),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '',
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-flipbox-front .trx-addons-flipbox-inner' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'content_padding_front',
			[
				'label'                 => esc_html__( 'Padding', 'trx_addons' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-flipbox-front .trx-addons-flipbox-inner' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'                  => 'content_border_front',
				'label'                 => esc_html__( 'Border Style', 'trx_addons' ),
				'selector'              => '{{WRAPPER}} .trx-addons-flipbox-front .trx-addons-flipbox-inner',
				'separator'             => 'before',
			]
		);

		$this->add_responsive_control(
			'content_border_radius_front',
			[
				'label'                 => __( 'Border Radius', 'trx_addons' ),
				'type'                  => Controls_Manager::SLIDER,
				'size_units'            => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'range'                 => [
					'px' => [
						'min' => 0,
						'max' => 200,
					],
				],
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-flipbox-front .trx-addons-flipbox-inner' => 'border-radius: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'image_style_heading_front',
			[
				'label'                 => esc_html__( 'Image', 'trx_addons' ),
				'type'                  => Controls_Manager::HEADING,
				'separator'             => 'before',
				'condition'             => [
					'icon_type' => 'image',
				],
			]
		);

		$this->add_responsive_control(
			'image_spacing_front',
			[
				'label'                 => __( 'Spacing', 'trx_addons' ),
				'type'                  => Controls_Manager::SLIDER,
				'size_units'            => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'range'                 => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-flipbox-icon-image' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
				'condition'             => [
					'icon_type' => 'image',
				],
			]
		);

		$this->add_responsive_control(
			'image_size_front',
			[
				'label'                 => esc_html__( 'Size', 'trx_addons' ),
				'type'                  => Controls_Manager::SLIDER,
				'default'               => [
					'size' => '',
				],
				'size_units'            => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors'             => [
					//'{{WRAPPER}} .trx-addons-flipbox-icon-image > img' => 'width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .trx-addons-flipbox-icon-image' => 'width: {{SIZE}}{{UNIT}};',
				],
				'condition'             => [
					'icon_type' => 'image',
				],
			]
		);

		$this->add_control(
			'icon_style_heading_front',
			[
				'label'                 => esc_html__( 'Icon', 'trx_addons' ),
				'type'                  => Controls_Manager::HEADING,
				'separator'             => 'before',
				'condition'             => [
					'icon_type' => [ 'icon', 'text' ],
				],
			]
		);

		$this->add_control(
			'icon_color_front',
			[
				'label'                 => esc_html__( 'Color', 'trx_addons' ),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '#ffffff',
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-flipbox-icon-image, {{WRAPPER}} .trx-addons-flipbox-icon-image i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .trx-addons-flipbox-icon-image svg' => 'fill: {{VALUE}};',
				],
				'condition'             => [
					'icon_type' => [ 'icon', 'text' ],
				],
			]
		);

		$this->add_responsive_control(
			'icon_size_front',
			[
				'label'                 => __( 'Icon Size', 'trx_addons' ),
				'type'                  => Controls_Manager::SLIDER,
				'default'               => [
					'size' => 40,
					'unit' => 'px',
				],
				'range'                 => [
					'px' => [
						'min' => 6,
						'max' => 300,
					],
				],
				'size_units'            => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors'             => [
					// '{{WRAPPER}} .trx-addons-flipbox-icon-image, {{WRAPPER}} .trx-addons-flipbox-icon-image i' => 'font-size: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .trx-addons-flipbox-icon-image, {{WRAPPER}} .trx-addons-flipbox-icon-image i' => 'font-size: {{SIZE}}{{UNIT}};',
				],
				'condition'             => [
					'icon_type' => 'icon',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'icon_typography_front',
				'label'     => __( 'Typography', 'trx_addons' ),
				'global'    => [
					'default' => Global_Typography::TYPOGRAPHY_ACCENT,
				],
				'selector'  => '{{WRAPPER}} .trx-addons-flipbox-icon-image .trx-addons-icon-text',
				'condition' => array(
					'icon_type' => 'text',
				),
			)
		);

		$this->add_responsive_control(
			'icon_spacing_front',
			[
				'label'                 => __( 'Spacing', 'trx_addons' ),
				'type'                  => Controls_Manager::SLIDER,
				'range'                 => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'size_units'            => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-flipbox-icon-image' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
				'condition'             => [
					'icon_type' => [ 'icon', 'text' ],
				],
			]
		);

		$this->add_control(
			'icon_bg_color_front',
			array(
				'label'     => __( 'Background Color', 'trx_addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .trx-addons-flipbox-icon-image' => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'        => 'icon_border_front',
				'label'       => __( 'Border', 'trx_addons' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'condition'   => array(
					'icon_type!' => 'none',
				),
				'selector'    => '{{WRAPPER}} .trx-addons-flipbox-icon-image',
			)
		);

		$this->add_responsive_control(
			'icon_border_radius_front',
			array(
				'label'      => __( 'Border Radius', 'trx_addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'condition'  => array(
					'icon_type!' => 'none',
				),
				'selectors'  => array(
					'{{WRAPPER}} .trx-addons-flipbox-icon-image' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'icon_padding_front',
			array(
				'label'      => __( 'Padding', 'trx_addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors'  => array(
					'{{WRAPPER}} .trx-addons-flipbox-icon-image' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'title_heading_front',
			[
				'label'                 => esc_html__( 'Title', 'trx_addons' ),
				'type'                  => Controls_Manager::HEADING,
				'separator'             => 'before',
				'condition'             => [
					'title_front!' => '',
				],
			]
		);

		$this->add_control(
			'title_color_front',
			[
				'label'                 => esc_html__( 'Color', 'trx_addons' ),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '#fff',
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-flipbox-front .trx-addons-flipbox-heading' => 'color: {{VALUE}};',
				],
				'condition'             => [
					'title_front!' => '',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'                  => 'title_typography_front',
				'selector'              => '{{WRAPPER}} .trx-addons-flipbox-front .trx-addons-flipbox-heading',
				'condition'             => [
					'title_front!' => '',
				],
			]
		);

		$this->add_responsive_control(
			'title_spacing_front',
			[
				'label'                 => __( 'Spacing', 'trx_addons' ),
				'type'                  => Controls_Manager::SLIDER,
				'range'                 => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'size_units'            => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-flipbox-front .trx-addons-flipbox-heading' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
				'condition'             => [
					'title_front!' => '',
				],
			]
		);

		$this->add_control(
			'description_heading_front',
			[
				'label'                 => esc_html__( 'Description', 'trx_addons' ),
				'type'                  => Controls_Manager::HEADING,
				'separator'             => 'before',
				'condition'             => [
					'description_front!' => '',
				],
			]
		);

		$this->add_control(
			'description_color_front',
			[
				'label'                 => esc_html__( 'Color', 'trx_addons' ),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '#fff',
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-flipbox-front .trx-addons-flipbox-content' => 'color: {{VALUE}};',
				],
				'condition'             => [
					'description_front!' => '',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'                  => 'description_typography_front',
				'selector'              => '{{WRAPPER}} .trx-addons-flipbox-front .trx-addons-flipbox-content',
				'condition'             => [
					'description_front!' => '',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function register_style_back_controls() {
		/**
		 * Style Tab: Back
		 */
		$this->start_controls_section(
			'section_back_style',
			[
				'label'                 => esc_html__( 'Back', 'trx_addons' ),
				'tab'                   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'padding_back',
			[
				'label'                 => esc_html__( 'Padding', 'trx_addons' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-flipbox-back .trx-addons-flipbox-overlay' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'content_alignment_back',
			[
				'label'                 => esc_html__( 'Alignment', 'trx_addons' ),
				'type'                  => Controls_Manager::CHOOSE,
				'label_block'           => false,
				'options'               => [
					'left' => [
						'title'   => esc_html__( 'Left', 'trx_addons' ),
						'icon'    => 'eicon-text-align-left',
					],
					'center' => [
						'title'   => esc_html__( 'Center', 'trx_addons' ),
						'icon'    => 'eicon-text-align-center',
					],
					'right' => [
						'title'   => esc_html__( 'Right', 'trx_addons' ),
						'icon'    => 'eicon-text-align-right',
					],
				],
				'default'               => 'center',
				'selectors' => [
					'{{WRAPPER}} .trx-addons-flipbox-back .trx-addons-flipbox-overlay' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'vertical_position_back',
			[
				'label'                 => __( 'Vertical Position', 'trx_addons' ),
				'type'                  => Controls_Manager::CHOOSE,
				'label_block'           => false,
				'options'               => [
					'top' => [
						'title' => __( 'Top', 'trx_addons' ),
						'icon' => 'eicon-v-align-top',
					],
					'middle' => [
						'title' => __( 'Middle', 'trx_addons' ),
						'icon' => 'eicon-v-align-middle',
					],
					'bottom' => [
						'title' => __( 'Bottom', 'trx_addons' ),
						'icon' => 'eicon-v-align-bottom',
					],
				],
				'selectors_dictionary'  => [
					'top' => 'flex-start',
					'middle' => 'center',
					'bottom' => 'flex-end',
				],
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-flipbox-back .trx-addons-flipbox-overlay' => 'justify-content: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'                  => 'background_back',
				'types'                 => [ 'classic', 'gradient' ],
				'selector'              => '{{WRAPPER}} .trx-addons-flipbox-back',
				'separator'             => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'                  => 'border_back',
				'label'                 => esc_html__( 'Border Style', 'trx_addons' ),
				'selector'              => '{{WRAPPER}} .trx-addons-flipbox-back',
				'separator'             => 'before',
			]
		);

		$this->add_control(
			'overlay_back',
			[
				'label'                 => esc_html__( 'Overlay', 'trx_addons' ),
				'type'                  => Controls_Manager::HEADING,
				'separator'             => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'                  => 'overlay_back',
				'types'                 => [ 'classic', 'gradient' ],
				'exclude'               => [ 'image' ],
				'selector'              => '{{WRAPPER}} .trx-addons-flipbox-back .trx-addons-flipbox-overlay',
			]
		);

		$this->add_control(
			'content_back_heading',
			[
				'label'                 => esc_html__( 'Content', 'trx_addons' ),
				'type'                  => Controls_Manager::HEADING,
				'separator'             => 'before',
			]
		);

		$this->add_control(
			'content_background_color_back',
			[
				'label'                 => esc_html__( 'Background Color', 'trx_addons' ),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '',
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-flipbox-back .trx-addons-flipbox-inner' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'content_padding_back',
			[
				'label'                 => esc_html__( 'Padding', 'trx_addons' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-flipbox-back .trx-addons-flipbox-inner' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'                  => 'content_border_back',
				'label'                 => esc_html__( 'Border Style', 'trx_addons' ),
				'selector'              => '{{WRAPPER}} .trx-addons-flipbox-back .trx-addons-flipbox-inner',
				'separator'             => 'before',
			]
		);

		$this->add_responsive_control(
			'content_border_radius_back',
			[
				'label'                 => __( 'Border Radius', 'trx_addons' ),
				'type'                  => Controls_Manager::SLIDER,
				'size_units'            => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'range'                 => [
					'px' => [
						'min' => 0,
						'max' => 200,
					],
				],
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-flipbox-back .trx-addons-flipbox-inner' => 'border-radius: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'image_style_heading_back',
			[
				'label'                 => esc_html__( 'Image', 'trx_addons' ),
				'type'                  => Controls_Manager::HEADING,
				'separator'             => 'before',
				'condition'             => [
					'icon_type_back'    => 'image',
				],
			]
		);

		$this->add_responsive_control(
			'image_spacing_back',
			[
				'label'                 => __( 'Spacing', 'trx_addons' ),
				'type'                  => Controls_Manager::SLIDER,
				'range'                 => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'size_units'            => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-flipbox-icon-image-back' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
				'condition'             => [
					'icon_type_back'    => 'image',
				],
			]
		);

		$this->add_responsive_control(
			'image_size_back',
			[
				'label'                 => esc_html__( 'Size', 'trx_addons' ),
				'type'                  => Controls_Manager::SLIDER,
				'default'               => [
					'size' => '',
				],
				'size_units'            => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors'             => [
					// '{{WRAPPER}} .trx-addons-flipbox-icon-image-back > img' => 'width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .trx-addons-flipbox-icon-image-back' => 'width: {{SIZE}}{{UNIT}};',
				],
				'condition'             => [
					'icon_type_back'    => 'image',
				],
			]
		);

		$this->add_control(
			'icon_style_heading_back',
			[
				'label'                 => esc_html__( 'Icon', 'trx_addons' ),
				'type'                  => Controls_Manager::HEADING,
				'separator'             => 'before',
				'condition'             => [
					'icon_type_back'    => [ 'icon', 'text' ],
				],
			]
		);

		$this->add_control(
			'icon_color_back',
			[
				'label'                 => esc_html__( 'Color', 'trx_addons' ),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '#ffffff',
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-flipbox-icon-image-back, {{WRAPPER}} .trx-addons-flipbox-icon-image-back i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .trx-addons-flipbox-icon-image-back svg' => 'fill: {{VALUE}};',
				],
				'condition'             => [
					'icon_type_back'    => [ 'icon', 'text' ],
				],
			]
		);

		$this->add_responsive_control(
			'icon_size_back',
			[
				'label'                 => __( 'Icon Size', 'trx_addons' ),
				'type'                  => Controls_Manager::SLIDER,
				'default'               => [
					'size' => 40,
					'unit' => 'px',
				],
				'range'                 => [
					'px' => [
						'min' => 6,
						'max' => 300,
					],
				],
				'size_units'            => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors'             => [
					// '{{WRAPPER}} .trx-addons-flipbox-icon-image-back, {{WRAPPER}} .trx-addons-flipbox-icon-image-back i' => 'font-size: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .trx-addons-flipbox-icon-image-back, {{WRAPPER}} .trx-addons-flipbox-icon-image-back i' => 'font-size: {{SIZE}}{{UNIT}};',
				],
				'condition'             => [
					'icon_type_back'    => 'icon',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'icon_typography_back',
				'label'     => __( 'Typography', 'trx_addons' ),
				'global'    => [
					'default' => Global_Typography::TYPOGRAPHY_ACCENT,
				],
				'selector'  => '{{WRAPPER}} .trx-addons-flipbox-icon-image-back .trx-addons-icon-text',
				'condition' => array(
					'icon_type' => 'text',
				),
			)
		);

		$this->add_responsive_control(
			'icon_spacing_back',
			[
				'label'                 => __( 'Spacing', 'trx_addons' ),
				'type'                  => Controls_Manager::SLIDER,
				'range'                 => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'size_units'            => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-flipbox-icon-image-back' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
				'condition'             => [
					'icon_type_back'    => [ 'icon', 'text' ],
				],
			]
		);

		$this->add_control(
			'icon_bg_color_back',
			array(
				'label'     => __( 'Background Color', 'trx_addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .trx-addons-flipbox-icon-image-back' => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'        => 'icon_border_back',
				'label'       => __( 'Border', 'trx_addons' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'condition'   => array(
					'icon_type!' => 'none',
				),
				'selector'    => '{{WRAPPER}} .trx-addons-flipbox-icon-image-back',
			)
		);

		$this->add_responsive_control(
			'icon_border_radius_back',
			array(
				'label'      => __( 'Border Radius', 'trx_addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'condition'  => array(
					'icon_type!' => 'none',
				),
				'selectors'  => array(
					'{{WRAPPER}} .trx-addons-flipbox-icon-image-back' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'icon_padding_back',
			array(
				'label'      => __( 'Padding', 'trx_addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors'  => array(
					'{{WRAPPER}} .trx-addons-flipbox-icon-image-back' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'title_heading_back',
			[
				'label'                 => esc_html__( 'Title', 'trx_addons' ),
				'type'                  => Controls_Manager::HEADING,
				'separator'             => 'before',
				'condition'             => [
					'title_back!' => '',
				],
			]
		);

		$this->add_control(
			'title_color_back',
			[
				'label'                 => esc_html__( 'Color', 'trx_addons' ),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '#fff',
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-flipbox-back .trx-addons-flipbox-heading' => 'color: {{VALUE}};',
				],
				'condition'             => [
					'title_back!' => '',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'                  => 'title_typography_back',
				'selector'              => '{{WRAPPER}} .trx-addons-flipbox-back .trx-addons-flipbox-heading',
				'condition'             => [
					'title_back!' => '',
				],
			]
		);

		$this->add_responsive_control(
			'title_spacing_back',
			[
				'label'                 => __( 'Spacing', 'trx_addons' ),
				'type'                  => Controls_Manager::SLIDER,
				'range'                 => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'size_units'            => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-flipbox-back .trx-addons-flipbox-heading' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
				'condition'             => [
					'title_back!' => '',
				],
			]
		);

		$this->add_control(
			'description_heading_back',
			[
				'label'                 => esc_html__( 'Description', 'trx_addons' ),
				'type'                  => Controls_Manager::HEADING,
				'separator'             => 'before',
				'condition'             => [
					'description_back!' => '',
				],
			]
		);

		$this->add_control(
			'description_color_back',
			[
				'label'                 => esc_html__( 'Color', 'trx_addons' ),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '',
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-flipbox-back .trx-addons-flipbox-content' => 'color: {{VALUE}};',
				],
				'condition'             => [
					'description_back!' => '',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'                  => 'description_typography_back',
				'selector'              => '{{WRAPPER}} .trx-addons-flipbox-back .trx-addons-flipbox-content',
				'condition'             => [
					'description_back!' => '',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function register_style_button_controls() {
		/**
		 * Style Tab: Button
		 * ------------------
		 */
		$this->start_controls_section(
			'section_flipbox_button_style',
			[
				'label'                 => __( 'Button', 'trx_addons' ),
				'tab'                   => Controls_Manager::TAB_STYLE,
				'condition'             => [
					'link_type'    => 'button',
				],
			]
		);

		$this->add_responsive_control(
			'button_spacing',
			[
				'label'                 => __( 'Spacing', 'trx_addons' ),
				'type'                  => Controls_Manager::SLIDER,
				'default'               => [
					'size' => 15,
				],
				'range'                 => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'size_units'            => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-flipbox-button' => 'margin-top: {{SIZE}}{{UNIT}};',
				],
				'condition'             => [
					'link_type'    => 'button',
				],
			]
		);

		$this->start_controls_tabs( 'tabs_button_style' );

		$this->start_controls_tab(
			'tab_button_normal',
			[
				'label'                 => __( 'Normal', 'trx_addons' ),
				'condition'             => [
					'link_type'    => 'button',
				],
			]
		);

		$this->add_control(
			'button_bg_color_normal',
			[
				'label'                 => __( 'Background Color', 'trx_addons' ),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '',
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-flipbox-button' => 'background-color: {{VALUE}}',
				],
				'condition'             => [
					'link_type'    => 'button',
				],
			]
		);

		$this->add_control(
			'button_text_color_normal',
			[
				'label'                 => __( 'Text Color', 'trx_addons' ),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '',
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-flipbox-button' => 'color: {{VALUE}}',
					'{{WRAPPER}} .trx-addons-flipbox-button .trx-addons-button-icon svg' => 'fill: {{VALUE}}',
				],
				'condition'             => [
					'link_type'    => 'button',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'                  => 'button_border_normal',
				'label'                 => __( 'Border', 'trx_addons' ),
				'placeholder'           => '1px',
				'default'               => '1px',
				'selector'              => '{{WRAPPER}} .trx-addons-flipbox-button',
				'condition'             => [
					'link_type'    => 'button',
				],
			]
		);

		$this->add_responsive_control(
			'button_border_radius',
			[
				'label'                 => __( 'Border Radius', 'trx_addons' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-flipbox-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'             => [
					'link_type'    => 'button',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'                  => 'button_typography',
				'label'                 => __( 'Typography', 'trx_addons' ),
				'global'                => [
					'default' => Global_Typography::TYPOGRAPHY_ACCENT,
				],
				'selector'              => '{{WRAPPER}} .trx-addons-flipbox-button',
				'condition'             => [
					'link_type'    => 'button',
				],
			]
		);

		$this->add_responsive_control(
			'button_padding',
			[
				'label'                 => __( 'Padding', 'trx_addons' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-flipbox-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'             => [
					'link_type'    => 'button',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'                  => 'button_box_shadow',
				'selector'              => '{{WRAPPER}} .trx-addons-flipbox-button',
				'condition'             => [
					'link_type'    => 'button',
				],
			]
		);

		$this->add_control(
			'flipbox_button_icon_heading',
			[
				'label'                 => __( 'Button Icon', 'trx_addons' ),
				'type'                  => Controls_Manager::HEADING,
				'separator'             => 'before',
				'condition'             => [
					'link_type'    => 'button',
					'select_button_icon[value]!' => '',
				],
			]
		);

		$this->add_responsive_control(
			'button_icon_margin',
			[
				'label'                 => __( 'Margin', 'trx_addons' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'condition'             => [
					'link_type'    => 'button',
					'select_button_icon[value]!' => '',
				],
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-flipbox-button .trx-addons-button-icon' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_button_hover',
			[
				'label'                 => __( 'Hover', 'trx_addons' ),
				'condition'             => [
					'link_type'    => 'button',
				],
			]
		);

		$this->add_control(
			'button_bg_color_hover',
			[
				'label'                 => __( 'Background Color', 'trx_addons' ),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '',
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-flipbox-button:hover' => 'background-color: {{VALUE}}',
				],
				'condition'             => [
					'link_type'    => 'button',
				],
			]
		);

		$this->add_control(
			'button_text_color_hover',
			[
				'label'                 => __( 'Text Color', 'trx_addons' ),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '',
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-flipbox-button:hover' => 'color: {{VALUE}}',
					'{{WRAPPER}} .trx-addons-flipbox-button:hover .trx-addons-button-icon svg' => 'fill: {{VALUE}}',
				],
				'condition'             => [
					'link_type'    => 'button',
				],
			]
		);

		$this->add_control(
			'button_border_color_hover',
			[
				'label'                 => __( 'Border Color', 'trx_addons' ),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '',
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-flipbox-button:hover' => 'border-color: {{VALUE}}',
				],
				'condition'             => [
					'link_type'    => 'button',
				],
			]
		);

		$this->add_control(
			'button_animation',
			[
				'label'                 => __( 'Animation', 'trx_addons' ),
				'type'                  => Controls_Manager::HOVER_ANIMATION,
				'condition'             => [
					'link_type'    => 'button',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'                  => 'button_box_shadow_hover',
				'selector'              => '{{WRAPPER}} .trx-addons-flipbox-button:hover',
				'condition'             => [
					'link_type'    => 'button',
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
	 * @access protected
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();
		$flipbox_if_html_tag = 'div';

		$this->add_render_attribute(
			[
				'flipbox-card' => [
					'class' => [
						'trx-addons-flipbox-flip-card',
					],
				],
				'flipbox-container' => [
					'class' => [
						'trx-addons-flipbox-container',
						'trx-addons-animate-' . esc_attr( $settings['flip_effect'] ),
						'trx-addons-direction-' . esc_attr( $settings['flip_direction'] ),
					],
				],
			]
		);
		?>
		<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'flipbox-container' ) ); ?>>
			<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'flipbox-card' ) ); ?>>
				<?php
					// Front
					$this->render_front();

					// Back
					$this->render_back();
				?>
			</div>
		</div>
		<?php
	}

	protected function render_front() {
		$settings = $this->get_settings_for_display();

		$title_tag_front = $settings['title_html_tag_front'];

		$this->add_render_attribute( 'icon-front', 'class', array( 'trx-addons-flipbox-icon-image', 'trx-addons-flipbox-icon-type-' . $settings['icon_type'] ) );

		if ( 'icon' === $settings['icon_type'] ) {
			$this->add_render_attribute( 'icon-front', 'class', 'trx-addons-icon' );
		}

		if ( ! isset( $settings['icon'] ) && ! Icons_Manager::is_migration_allowed() ) {
			// add old default
			$settings['icon'] = 'fa fa-star';
		}

		$has_icon = ! empty( $settings['icon'] );

		if ( $has_icon ) {
			$this->add_render_attribute( 'front-i', 'class', $settings['icon'] );
			$this->add_render_attribute( 'front-i', 'aria-hidden', 'true' );
		}

		if ( ! $has_icon && ! empty( $settings['select_icon']['value'] ) ) {
			$has_icon = true;
		}
		$migrated = isset( $settings['__fa4_migrated']['select_icon'] );
		$is_new = ! isset( $settings['icon'] ) && Icons_Manager::is_migration_allowed();
		?>
		<div class="trx-addons-flipbox-front">
			<div class="trx-addons-flipbox-overlay">
				<div class="trx-addons-flipbox-inner">
					<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'icon-front' ) ); ?>>
						<?php if ( 'icon' === $settings['icon_type'] && $has_icon ) { ?>
							<?php
							if ( $is_new || $migrated ) {
								Icons_Manager::render_icon( $settings['select_icon'], [ 'aria-hidden' => 'true' ] );
							} elseif ( ! empty( $settings['icon'] ) ) {
								?><i <?php echo wp_kses_post( $this->get_render_attribute_string( 'front-i' ) ); ?>></i><?php
							}
							?>
						<?php } elseif ( 'image' === $settings['icon_type'] ) { ?>
							<?php
								$flipbox_image = $settings['icon_image'];
								$flipbox_image_url = Group_Control_Image_Size::get_attachment_image_src( $flipbox_image['id'], 'thumbnail', $settings );
								$flipbox_image_url = ( empty( $flipbox_image_url ) ) ? $flipbox_image['url'] : $flipbox_image_url;
							?>
							<?php if ( $flipbox_image_url ) { ?>
								<img src="<?php echo esc_url( $flipbox_image_url ); ?>" alt="">
							<?php } ?>
						<?php } elseif ( 'text' === $settings['icon_type'] ) { ?>
							<span class="trx-addons-icon-text">
								<?php echo wp_kses_post( $settings['icon_text'] ); ?>
							</span>
						<?php } ?>
					</div>

					<<?php echo esc_html( $title_tag_front ); ?> class="trx-addons-flipbox-heading">
						<?php echo wp_kses_post( $settings['title_front'], 'trx_addons' ); ?>
					</<?php echo esc_html( $title_tag_front ); ?>>

					<div class="trx-addons-flipbox-content">
						<?php echo wp_kses_post( $settings['description_front'], 'trx_addons' ); ?>
					</div>
				</div>
			</div>
		</div>
		<?php
	}

	protected function render_back() {
		$settings = $this->get_settings_for_display();

		$title_tag = $settings['title_html_tag_back'];

		$this->add_render_attribute( 'title-container', 'class', 'trx-addons-flipbox-heading' );

		if ( 'none' !== $settings['icon_type_back'] ) {

			$this->add_render_attribute( 'icon-back', 'class', array( 'trx-addons-flipbox-icon-image-back', 'trx-addons-flipbox-icon-type-' . $settings['icon_type_back'] ) );

			if ( ! isset( $settings['icon_back'] ) && ! Icons_Manager::is_migration_allowed() ) {
				// add old default
				$settings['icon'] = 'fa fa-snowflake-o';
			}

			$has_icon_back = ! empty( $settings['icon_back'] );

			if ( $has_icon_back ) {
				$this->add_render_attribute( 'back-i', 'class', $settings['icon_back'] );
				$this->add_render_attribute( 'back-i', 'aria-hidden', 'true' );
			}

			if ( ! $has_icon_back && ! empty( $settings['select_icon_back']['value'] ) ) {
				$has_icon_back = true;
			}
			$migrated_icon_back = isset( $settings['__fa4_migrated']['select_icon_back'] );
			$is_new_icon_back = ! isset( $settings['icon_back'] ) && Icons_Manager::is_migration_allowed();

			if ( 'image' === $settings['icon_type_back'] ) {
				$flipbox_image_back = $settings['icon_image_back'];
				$flipbox_back_image_url = Group_Control_Image_Size::get_attachment_image_src( $flipbox_image_back['id'], 'thumbnail_back', $settings );
				$flipbox_back_image_url = ( empty( $flipbox_back_image_url ) ) ? $flipbox_image_back['url'] : $flipbox_back_image_url;

				$this->add_render_attribute(
					'icon-image-back',
					[
						'src'   => $flipbox_back_image_url,
						'alt'   => 'flipbox-image',
					]
				);
			} elseif ( 'icon' === $settings['icon_type_back'] ) {
				$this->add_render_attribute( 'icon-back', 'class', 'trx-addons-icon' );
			}
		}

		if ( 'none' !== $settings['link_type'] ) {
			if ( ! empty( $settings['link']['url'] ) ) {
				if ( 'title' === $settings['link_type'] ) {
					$title_tag = 'a';

					$this->add_render_attribute( 'title-container', 'class', 'trx-addons-flipbox-linked-title' );

					$this->add_link_attributes( 'title-container', $settings['link'] );

				} elseif ( 'button' === $settings['link_type'] ) {

					$this->add_render_attribute( 'button', 'class', [ 'elementor-button', 'trx-addons-flipbox-button' ] );

					if ( $settings['button_animation'] ) {
						$this->add_render_attribute( 'button', 'class', 'elementor-animation-' . $settings['button_animation'] );
					}
			
					$this->add_link_attributes( 'button', $settings['link'] );

				}
			}
		}
		?>
		<div class="trx-addons-flipbox-back">
			<?php
			if ( 'box' === $settings['link_type'] && $settings['link']['url'] ) {
				$this->add_render_attribute( 'box-link', 'class', 'trx-addons-flipbox-box-link' );

				$this->add_link_attributes( 'box-link', $settings['link'] );
				?>
				<a <?php echo wp_kses_post( $this->get_render_attribute_string( 'box-link' ) ); ?>></a>
			<?php } ?>
			<div class="trx-addons-flipbox-overlay">
				<div class="trx-addons-flipbox-inner">
					<?php if ( 'none' !== $settings['icon_type_back'] ) { ?>
						<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'icon-back' ) ); ?>>
							<?php if ( 'image' === $settings['icon_type_back'] ) { ?>
								<img <?php echo wp_kses_post( $this->get_render_attribute_string( 'icon-image-back' ) ); ?>>
							<?php } elseif ( 'icon' === $settings['icon_type_back'] && $has_icon_back ) { ?>
								<?php
								if ( $is_new_icon_back || $migrated_icon_back ) {
									Icons_Manager::render_icon( $settings['select_icon_back'], [ 'aria-hidden' => 'true' ] );
								} elseif ( ! empty( $settings['icon_back'] ) ) {
									?><i <?php echo wp_kses_post( $this->get_render_attribute_string( 'back-i' ) ); ?>></i><?php
								}
								?>
							<?php } elseif ( 'text' === $settings['icon_type_back'] ) { ?>
								<span class="trx-addons-icon-text">
									<?php echo wp_kses_post( $settings['icon_text_back'] ); ?>
								</span>
							<?php } ?>
						</div>
					<?php } ?>

					<?php if ( $settings['title_back'] ) { ?>
						<<?php echo esc_html( $title_tag ),' ', wp_kses_post( $this->get_render_attribute_string( 'title-container' ) ); ?>>
							<?php echo wp_kses_post( $settings['title_back'], 'trx_addons' ); ?>
						</<?php echo esc_html( $title_tag ); ?>>
					<?php } ?>

					<div class="trx-addons-flipbox-content">
						<?php echo wp_kses_post( $settings['description_back'], 'trx_addons' ); ?>
					</div>

					<?php if ( 'button' === $settings['link_type'] && ! empty( $settings['flipbox_button_text'] ) ) : ?>
						<a <?php echo wp_kses_post( $this->get_render_attribute_string( 'button' ) ); ?>>
							<?php
							if ( 'before' === $settings['button_icon_position'] ) :
								$this->render_button_icon();
							endif;

							echo esc_attr( $settings['flipbox_button_text'] );

							if ( 'after' === $settings['button_icon_position'] ) :
								$this->render_button_icon();
							endif;
							?>
						</a>
					<?php endif; ?>
				</div>
			</div>
		</div>
		<?php
	}

	protected function render_button_icon() {
		$settings = $this->get_settings_for_display();

		if ( ! isset( $settings['button_icon'] ) && ! Icons_Manager::is_migration_allowed() ) {
			// add old default
			$settings['icon'] = '';
		}

		$has_button_icon = ! empty( $settings['button_icon'] );

		if ( $has_button_icon ) {
			$this->add_render_attribute( 'button-i', 'class', $settings['button_icon'] );
			$this->add_render_attribute( 'button-i', 'aria-hidden', 'true' );
		}

		if ( ! $has_button_icon && ! empty( $settings['select_button_icon']['value'] ) ) {
			$has_button_icon = true;
		}

		$migrated_button_icon = isset( $settings['__fa4_migrated']['select_button_icon'] );
		$is_new_button_icon = ! isset( $settings['button_icon'] ) && Icons_Manager::is_migration_allowed();

		if ( $has_button_icon ) { ?>
			<span class="trx-addons-button-icon trx-addons-icon">
				<?php
				if ( $is_new_button_icon || $migrated_button_icon ) {
					Icons_Manager::render_icon( $settings['select_button_icon'], [ 'aria-hidden' => 'true' ] );
				} elseif ( ! empty( $settings['button_icon'] ) ) {
					?><i <?php echo wp_kses_post( $this->get_render_attribute_string( 'button-i' ) ); ?>></i><?php
				}
				?>
			</span>
			<?php
		}
	}

	/**
	 * Render flipbox widget output in the editor.
	 *
	 * Written as a Backbone JavaScript template and used to generate the live preview.
	 *
	 * @access protected
	 */
	protected function content_template() {
		?>
		<#
			view.addRenderAttribute( 'flipbox-card', {
				'class': [
					'trx-addons-flipbox-flip-card'
				],
			} );

			view.addRenderAttribute( 'flipbox-container', {
				'class': [
					'trx-addons-flipbox-container',
					'trx-addons-animate-' + settings.flip_effect,
					'trx-addons-direction-' + settings.flip_direction
				],
			} );

			function render_button_icon() {
				var buttonIconHTML = elementor.helpers.renderIcon( view, settings.select_button_icon, { 'aria-hidden': true }, 'i' , 'object' ),
					buttonMigrated = elementor.helpers.isIconMigrated( settings, 'select_button_icon' );

				if ( settings.button_icon || settings.select_button_icon ) { #>
					<span class="trx-addons-button-icon trx-addons-icon">
					<#
					if ( buttonIconHTML && buttonIconHTML.rendered && ( ! settings.button_icon || buttonMigrated ) ) { #>
						{{{ buttonIconHTML.value }}}
					<# } else if ( settings.button_icon ) { #>
						<i class="{{ settings.button_icon }}" aria-hidden="true"></i>
					<# } #>
					</span>
					<#
				}
			}

			function render_front() {
				view.addRenderAttribute( 'icon-front', 'class', ['trx-addons-flipbox-icon-image', 'trx-addons-flipbox-icon-type-' + settings.icon_type ] );

				var iconHTML = elementor.helpers.renderIcon( view, settings.select_icon, { 'aria-hidden': true }, 'i' , 'object' ),
					migrated = elementor.helpers.isIconMigrated( settings, 'select_icon' );

				if ( 'icon' === settings.icon_type ) {
					view.addRenderAttribute( 'icon-front', 'class', 'trx-addons-icon' );
				}
				#>
				<div class="trx-addons-flipbox-front">
					<div class="trx-addons-flipbox-overlay">
						<div class="trx-addons-flipbox-inner">
							<div {{{ view.getRenderAttributeString( 'icon-front' ) }}}>
								<#
								if ( 'icon' === settings.icon_type ) {
									if ( settings.icon || settings.select_icon ) {
										if ( iconHTML && iconHTML.rendered && ( ! settings.icon || migrated ) ) { #>
											{{{ iconHTML.value }}}
										<# } else { #>
											<i class="{{ settings.icon }}" aria-hidden="true"></i>
										<# }
									}
								} else if ( 'image' === settings.icon_type ) {
									var flipbox_image = {
										id: settings.icon_image.id,
										url: settings.icon_image.url,
										size: settings.thumbnail_size,
										dimension: settings.thumbnail_custom_dimension,
										model: view.getEditModel()
									};
									var flipbox_image_url = elementor.imagesManager.getImageUrl( flipbox_image );

									if ( flipbox_image_url ) { #>
										<img src="{{ _.escape( flipbox_image_url ) }}" />
									<# }
								} else if ( 'text' === settings.icon_type ) { #>
									<span class="trx-addons-icon-text">
										{{{ settings.icon_text }}}
									</span>
								<# } #>
							</div>

							<# var titleFrontHTMLTag = elementor.helpers.validateHTMLTag( settings.title_html_tag_front ); #>

							<{{{ titleFrontHTMLTag }}} class="trx-addons-flipbox-heading">
								{{{ settings.title_front }}}
							</{{{ titleFrontHTMLTag }}}>

							<div class="trx-addons-flipbox-content">
								{{{ settings.description_front }}}
							</div>
						</div>
					</div>
				</div>
				<#
			}

			function render_back() {
				var titleBackHTMLTag = elementor.helpers.validateHTMLTag( settings.title_html_tag_back );

				view.addRenderAttribute( 'title-container', 'class', 'trx-addons-flipbox-heading' );

				view.addRenderAttribute( 'icon-back', 'class', [ 'trx-addons-flipbox-icon-image-back', 'trx-addons-flipbox-icon-type-' + settings.icon_type_back ] );

				var iconHTML = elementor.helpers.renderIcon( view, settings.select_icon_back, { 'aria-hidden': true }, 'i' , 'object' ),
					migrated = elementor.helpers.isIconMigrated( settings, 'select_icon_back' );

				if ( 'icon' === settings.icon_type_back ) {
					view.addRenderAttribute( 'icon-back', 'class', 'trx-addons-icon' );
				}

				if ( 'none' !== settings.link_type ) {
					if ( settings.link.url ) {
						if ( 'title' === settings.link_type ) {

							var titleBackHTMLTag = 'a';

							view.addRenderAttribute( 'title-container', 'class', 'trx-addons-flipbox-linked-title' );
							view.addRenderAttribute( 'title-container', 'href', settings.link.url );

						} else if ( 'button' === settings.link_type ) {

							view.addRenderAttribute( 'button', {
								'class': [
									'elementor-button',
									'trx-addons-flipbox-button',
								],
								'href': [
									settings.link.url,
								],
							} );

							if ( settings.button_animation ) {
								view.addRenderAttribute( 'button', 'class', 'elementor-animation-' + settings.button_animation );
							}
						}
					}
				}
				#>
				<div class="trx-addons-flipbox-back">
					<#
					if ( 'box' === settings.link_type && settings.link.url ) {
						view.addRenderAttribute( 'box-link', 'class', 'trx-addons-flipbox-box-link' );
						view.addRenderAttribute( 'box-link', 'href', settings.link.url );
						#>
						<a <{{{ view.getRenderAttributeString( 'box-link' ) }}}></a>
					<# } #>
					<div class="trx-addons-flipbox-overlay">
						<div class="trx-addons-flipbox-inner">
							<# if ( 'none' !== settings.icon_type_back ) { #>
								<div {{{ view.getRenderAttributeString( 'icon-back' ) }}}>
									<#
									if ( 'icon' === settings.icon_type_back ) {
										if ( settings.icon_back || settings.select_icon_back ) {
											if ( iconHTML && iconHTML.rendered && ( ! settings.icon || migrated ) ) { #>
												{{{ iconHTML.value }}}
											<# } else { #>
												<i class="{{ settings.icon_back }}" aria-hidden="true"></i>
											<# }
										}
									} else if ( 'image' === settings.icon_type_back ) {
										var flipbox_image = {
											id: settings.icon_image_back.id,
											url: settings.icon_image_back.url,
											size: settings.thumbnail_back_size,
											dimension: settings.thumbnail_back_custom_dimension,
											model: view.getEditModel()
										};
										var flipbox_image_url = elementor.imagesManager.getImageUrl( flipbox_image );

										if ( flipbox_image_url ) { #>
											<img src="{{ _.escape( flipbox_image_url ) }}" />
										<# }
									} else if ( 'text' === settings.icon_type_back ) { #>
										<span class="trx-addons-icon-text">
											{{{ settings.icon_text_back }}}
										</span>
									<# } #>
								</div>
							<# } #>

							<{{{ titleBackHTMLTag }}} {{{ view.getRenderAttributeString( 'title-container' ) }}}>
								{{{ settings.title_back }}}
							</{{{ titleBackHTMLTag }}}>

							<div class="trx-addons-flipbox-content">
								{{{ settings.description_back }}}
							</div>

							<# if ( 'button' === settings.link_type && '' != settings.flipbox_button_text ) { #>
								<a {{{ view.getRenderAttributeString( 'button' ) }}}>
									<#
									if ( 'before' === settings.button_icon_position ) {
										render_button_icon();
									}
									#>

									{{{ settings.flipbox_button_text }}}

									<#
									if ( 'after' === settings.button_icon_position ) {
										render_button_icon();
									}
									#>
								</a>
							<# } #>
						</div>
					</div>
				</div>
				<#
			}
		#>
		<div {{{ view.getRenderAttributeString( 'flipbox-container' ) }}}>
			<div {{{ view.getRenderAttributeString( 'flipbox-card' ) }}}>
				<#
					render_front();

					render_back();
				#>
			</div>
		</div>
		<?php
	}
}
