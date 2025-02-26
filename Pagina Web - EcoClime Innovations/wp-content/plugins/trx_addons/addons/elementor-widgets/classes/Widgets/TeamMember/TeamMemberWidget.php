<?php
/**
 * Team Member Widget
 *
 * @package ThemeREX Addons
 * @since v2.30.0
 */

namespace TrxAddons\ElementorWidgets\Widgets\TeamMember;

use TrxAddons\ElementorWidgets\BaseWidget;

// Elementor Classes.
use Elementor\Controls_Manager;
use Elementor\Utils;
use Elementor\Icons_Manager;
use Elementor\Repeater;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Modules\DynamicTags\Module as TagsModule;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Team Member Widget
 */
class TeamMemberWidget extends BaseWidget {

	/**
	 * Register widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 */
	protected function register_controls() {
		/* Content Tab */
		$this->register_content_image_controls();
		$this->register_content_details_controls();
		$this->register_content_social_links_controls();
		$this->register_content_settings_controls();
		$this->register_content_help_docs_controls();

		/* Style Tab */
		$this->register_style_item_controls();
		$this->register_style_overlay_controls();
		$this->register_style_image_controls();
		$this->register_style_content_controls();
		$this->register_style_name_controls();
		$this->register_style_position_controls();
		$this->register_style_description_controls();
		$this->register_style_social_links_controls();
	}

	/**
	 * Register image controls
	 *
	 * @return void
	 */
	protected function register_content_image_controls() {

		$this->start_controls_section(
			'section_image',
			[
				'label'                 => __( 'Image', 'trx_addons' ),
			]
		);

		$this->add_control(
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
			]
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name'                  => 'image',
				'label'                 => __( 'Image Size', 'trx_addons' ),
				'default'               => 'medium_large',
			]
		);

		$this->add_responsive_control(
			'member_image_width',
			[
				'label'                 => __( 'Image Width', 'trx_addons' ),
				'type'                  => Controls_Manager::SLIDER,
				'size_units'            => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'default'               => [
					'size' => 50,
					'unit' => '%',
				],
				'range'                 => [
					'px' => [
						'max' => 1200,
					],
				],
				'tablet_default'        => [
					'unit' => '%',
				],
				'mobile_default'        => [
					'unit' => '%',
				],
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-tm-image' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Register details controls
	 *
	 * @return void
	 */
	protected function register_content_details_controls() {

		$this->start_controls_section(
			'section_details',
			[
				'label'                 => __( 'Details', 'trx_addons' ),
			]
		);

		$this->add_control(
			'team_member_name',
			[
				'label'                 => __( 'Name', 'trx_addons' ),
				'type'                  => Controls_Manager::TEXT,
				'dynamic'               => [
					'active'   => true,
				],
				'default'               => __( 'John Doe', 'trx_addons' ),
			]
		);

		$this->add_control(
			'team_member_position',
			[
				'label'                 => __( 'Position', 'trx_addons' ),
				'type'                  => Controls_Manager::TEXT,
				'dynamic'               => [
					'active'   => true,
				],
				'default'               => __( 'WordPress Developer', 'trx_addons' ),
			]
		);

		$this->add_control(
			'team_member_description_switch',
			[
				'label'                 => __( 'Show Description', 'trx_addons' ),
				'type'                  => Controls_Manager::SWITCHER,
				'default'               => 'yes',
				'label_on'              => __( 'Yes', 'trx_addons' ),
				'label_off'             => __( 'No', 'trx_addons' ),
				'return_value'          => 'yes',
			]
		);

		$this->add_control(
			'team_member_description',
			[
				'label'                 => __( 'Description', 'trx_addons' ),
				'type'                  => Controls_Manager::WYSIWYG,
				'dynamic'               => [
					'active'   => true,
				],
				'default'               => __( 'Enter member description here which describes the position of member in company', 'trx_addons' ),
				'condition'             => [
					'team_member_description_switch' => 'yes',
				],
			]
		);

		$this->add_control(
			'link_type',
			[
				'label'                 => __( 'Link Type', 'trx_addons' ),
				'type'                  => Controls_Manager::SELECT,
				'default'               => 'none',
				'options'               => [
					'none'      => __( 'None', 'trx_addons' ),
					'image'     => __( 'Image', 'trx_addons' ),
					'title'     => __( 'Title', 'trx_addons' ),
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

		$this->end_controls_section();
	}

	/**
	 * Register icons controls
	 *
	 * @return void
	 */
	protected function register_content_social_links_controls() {

		$this->start_controls_section(
			'section_member_social_links',
			[
				'label'                 => __( 'Icons', 'trx_addons' ),
			]
		);

		$this->add_control(
			'member_social_links',
			[
				'label'                 => __( 'Show Icons', 'trx_addons' ),
				'type'                  => Controls_Manager::SWITCHER,
				'default'               => 'yes',
				'label_on'              => __( 'Yes', 'trx_addons' ),
				'label_off'             => __( 'No', 'trx_addons' ),
				'return_value'          => 'yes',
			]
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'select_social_icon',
			[
				'label'                 => __( 'Icon', 'trx_addons' ),
				'type'                  => Controls_Manager::ICONS,
				'fa4compatibility'      => 'social_icon',
				'recommended' => [
					'fa-brands' => [
						'android',
						'apple',
						'behance',
						'bitbucket',
						'codepen',
						'delicious',
						'deviantart',
						'digg',
						'dribbble',
						'elementor',
						'facebook',
						'flickr',
						'foursquare',
						'free-code-camp',
						'github',
						'gitlab',
						'globe',
						'google-plus',
						'houzz',
						'instagram',
						'jsfiddle',
						'linkedin',
						'medium',
						'meetup',
						'mixcloud',
						'odnoklassniki',
						'pinterest',
						'product-hunt',
						'reddit',
						'shopping-cart',
						'skype',
						'slideshare',
						'snapchat',
						'soundcloud',
						'spotify',
						'stack-overflow',
						'steam',
						'stumbleupon',
						'telegram',
						'thumb-tack',
						'tripadvisor',
						'tumblr',
						'twitch',
						'twitter',
						'viber',
						'vimeo',
						'vk',
						'weibo',
						'weixin',
						'whatsapp',
						'wordpress',
						'xing',
						'yelp',
						'youtube',
						'500px',
					],
					'fa-solid' => [
						'envelope',
						'link',
						'rss',
					],
				],
			]
		);

		$repeater->add_control(
			'social_link',
			[
				'label'                 => __( 'Link', 'trx_addons' ),
				'type'                  => Controls_Manager::URL,
				'dynamic'               => [
					'active'  => true,
				],
				'label_block'           => true,
				'placeholder'           => __( 'Enter URL', 'trx_addons' ),
			]
		);

		$this->add_control(
			'team_member_social',
			[
				'label'                 => __( 'Add Icons', 'trx_addons' ),
				'type'                  => Controls_Manager::REPEATER,
				'default'               => [
					[
						'select_social_icon' => [
							'value' => 'fab fa-facebook',
							'library' => 'fa-brands',
						],
						'social_link' => [
							'url' => '#',
						],
					],
					[
						'select_social_icon' => [
							'value' => 'fab fa-twitter',
							'library' => 'fa-brands',
						],
						'social_link' => [
							'url' => '#',
						],
					],
					[
						'select_social_icon' => [
							'value' => 'fab fa-youtube',
							'library' => 'fa-brands',
						],
						'social_link' => [
							'url' => '#',
						],
					],
				],
				'fields'                => $repeater->get_controls(),
				'title_field' => '<# var migrated = "undefined" !== typeof __fa4_migrated, social = ( "undefined" === typeof social ) ? false : social; #>{{{ elementor.helpers.getSocialNetworkNameFromIcon( select_social_icon, social, true, migrated, true ) }}}',
				'condition'             => [
					'member_social_links' => 'yes',
				],
			]
		);

		$this->add_control(
			'social_links_style',
			[
				'label'   => __( 'Links Style', 'trx_addons' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'icon',
				'options' => [
					'icon'   => __( 'Icon', 'trx_addons' ),
					'button' => __( 'Button', 'trx_addons' ),
				],
			]
		);

		$this->add_control(
			'shape',
			[
				'label'        => esc_html__( 'Shape', 'trx_addons' ),
				'type'         => Controls_Manager::SELECT,
				'default'      => 'rounded',
				'options'      => [
					'rounded' => esc_html__( 'Rounded', 'trx_addons' ),
					'square'  => esc_html__( 'Square', 'trx_addons' ),
					'circle'  => esc_html__( 'Circle', 'trx_addons' ),
				],
				'prefix_class' => 'elementor-shape-',
				'condition'    => [
					'social_links_style' => 'button',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Register settings controls
	 *
	 * @return void
	 */
	protected function register_content_settings_controls() {

		$this->start_controls_section(
			'section_member_box_settings',
			[
				'label'                 => __( 'Settings', 'trx_addons' ),
			]
		);

		$this->add_control(
			'name_html_tag',
			[
				'label'                => __( 'Name HTML Tag', 'trx_addons' ),
				'type'                 => Controls_Manager::SELECT,
				'default'              => 'h4',
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
			'position_html_tag',
			[
				'label'                => __( 'Position HTML Tag', 'trx_addons' ),
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
			'social_links_position',
			[
				'label'                => __( 'Icons Position', 'trx_addons' ),
				'type'                 => Controls_Manager::SELECT,
				'default'              => 'after_desc',
				'options'              => [
					'before_desc'      => __( 'Before Description', 'trx_addons' ),
					'after_desc'       => __( 'After Description', 'trx_addons' ),
				],
				'condition'            => [
					'member_social_links' => 'yes',
					'overlay_content'     => [ 'none', 'all_content' ],
				],
			]
		);

		$this->add_control(
			'overlay_content',
			[
				'label'                => __( 'Overlay Content', 'trx_addons' ),
				'type'                 => Controls_Manager::SELECT,
				'default'              => 'none',
				'options'              => [
					'none'         => __( 'None', 'trx_addons' ),
					'social_icons' => __( 'Social Icons', 'trx_addons' ),
					'content'      => __( 'Description', 'trx_addons' ),
					'all_content'  => __( 'Description + Social Icons', 'trx_addons' ),
				],
			]
		);

		$this->add_control(
			'member_title_divider',
			[
				'label'                 => __( 'Divider after Name', 'trx_addons' ),
				'type'                  => Controls_Manager::SWITCHER,
				'default'               => 'no',
				'label_on'              => __( 'Show', 'trx_addons' ),
				'label_off'             => __( 'Hide', 'trx_addons' ),
				'return_value'          => 'yes',
			]
		);

		$this->add_control(
			'member_position_divider',
			[
				'label'                 => __( 'Divider after Position', 'trx_addons' ),
				'type'                  => Controls_Manager::SWITCHER,
				'default'               => 'yes',
				'label_on'              => __( 'Show', 'trx_addons' ),
				'label_off'             => __( 'Hide', 'trx_addons' ),
				'return_value'          => 'yes',
				'condition'             => [
					'team_member_position!'  => '',
				],
			]
		);

		$this->add_control(
			'member_description_divider',
			[
				'label'                 => __( 'Divider after Description', 'trx_addons' ),
				'type'                  => Controls_Manager::SWITCHER,
				'default'               => 'hide',
				'label_on'              => __( 'Show', 'trx_addons' ),
				'label_off'             => __( 'Hide', 'trx_addons' ),
				'return_value'          => 'yes',
				'condition'             => [
					'team_member_description_switch'  => 'yes',
				],
			]
		);

		$this->end_controls_section();
	}

	/*-----------------------------------------------------------------------------------*/
	/*	STYLE TAB
	/*-----------------------------------------------------------------------------------*/

	/**
	 * Register whole item style controls
	 *
	 * @return void
	 */
	protected function register_style_item_controls() {

		$this->start_controls_section(
			'section_item_style',
			[
				'label'                 => __( 'Team member', 'trx_addons' ),
				'tab'                   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'member_box_vertical_align',
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
					'{{WRAPPER}}.trx-addons-tm-image-left .trx-addons-tm'  => 'align-items: {{VALUE}};',
					'{{WRAPPER}}.trx-addons-tm-image-right .trx-addons-tm' => 'align-items: {{VALUE}};',
				],
				'condition'             => [
					'member_image_position' => [ 'left', 'right' ],
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'                  => 'member_item_background',
				'label'                 => __( 'Background', 'trx_addons' ),
				'types'                 => [ 'classic', 'gradient' ],
				'separator'             => 'before',
				'selector'              => '{{WRAPPER}} .trx-addons-tm-wrapper',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'                  => 'member_item_border',
				'label'                 => __( 'Border', 'trx_addons' ),
				'placeholder'           => '1px',
				'default'               => '1px',
				'separator'             => 'before',
				'selector'              => '{{WRAPPER}} .trx-addons-tm-wrapper',
			]
		);

		$this->add_responsive_control(
			'member_item_border_radius',
			[
				'label'                 => __( 'Border Radius', 'trx_addons' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-tm-wrapper' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		
		$this->add_responsive_control(
			'member_item_padding',
			[
				'label'                 => __( 'Padding', 'trx_addons' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-tm-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'                  => 'member_item_box_shadow',
				'selector'              => '{{WRAPPER}} .trx-addons-tm-wrapper',
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
				'label'                 => __( 'Content', 'trx_addons' ),
				'tab'                   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'member_box_alignment',
			[
				'label'                 => __( 'Alignment', 'trx_addons' ),
				'type'                  => Controls_Manager::CHOOSE,
				'render_type'           => 'template',
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
				],
				'default'               => 'center',
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-tm-wrapper' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'                  => 'content_background',
				'label'                 => __( 'Background', 'trx_addons' ),
				'types'                 => [ 'classic', 'gradient' ],
				'separator'             => 'before',
				'selector'              => '{{WRAPPER}} .trx-addons-tm-content',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'                  => 'member_content_border',
				'label'                 => __( 'Border', 'trx_addons' ),
				'placeholder'           => '1px',
				'default'               => '1px',
				'separator'             => 'before',
				'selector'              => '{{WRAPPER}} .trx-addons-tm-content',
			]
		);

		$this->add_responsive_control(
			'member_content_border_radius',
			[
				'label'                 => __( 'Border Radius', 'trx_addons' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-tm-content' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'                  => 'content_box_shadow',
				'selector'              => '{{WRAPPER}} .trx-addons-tm-content',
			]
		);

		$this->add_responsive_control(
			'member_box_content_margin',
			[
				'label'                 => __( 'Margin', 'trx_addons' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'separator'             => 'before',
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-tm-content' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'member_box_content_padding',
			[
				'label'                 => __( 'Padding', 'trx_addons' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-tm-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Register overlay style controls
	 *
	 * @return void
	 */
	protected function register_style_overlay_controls() {

		$this->start_controls_section(
			'section_member_overlay_style',
			[
				'label'                 => __( 'Overlay', 'trx_addons' ),
				'tab'                   => Controls_Manager::TAB_STYLE,
				'condition'             => [
					'overlay_content!'  => 'none',
				],
			]
		);

		$this->add_responsive_control(
			'overlay_alignment',
			[
				'label'                 => __( 'Alignment', 'trx_addons' ),
				'type'                  => Controls_Manager::CHOOSE,
				'render_type'           => 'template',
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
				],
				'default'               => '',
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-tm-overlay-content-wrap' => 'text-align: {{VALUE}};',
				],
				'condition'             => [
					'overlay_content!'  => 'none',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'                  => 'overlay_background',
				'types'                 => [ 'classic', 'gradient' ],
				'selector'              => '{{WRAPPER}} .trx-addons-tm-overlay-content-wrap:before',
				'condition'             => [
					'overlay_content!'  => 'none',
				],
			]
		);

		$this->add_control(
			'overlay_opacity',
			[
				'label'                 => __( 'Opacity', 'trx_addons' ),
				'type'                  => Controls_Manager::SLIDER,
				'range'                 => [
					'px' => [
						'min'   => 0,
						'max'   => 1,
						'step'  => 0.1,
					],
				],
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-tm-overlay-content-wrap:before' => 'opacity: {{SIZE}};',
				],
				'condition'             => [
					'overlay_content!'  => 'none',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Register image style controls
	 *
	 * @return void
	 */
	protected function register_style_image_controls() {

		$this->start_controls_section(
			'section_member_image_style',
			[
				'label'                 => __( 'Image', 'trx_addons' ),
				'tab'                   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'member_image_position',
			array(
				'label'              => __( 'Image Position', 'trx_addons' ),
				'type'               => Controls_Manager::CHOOSE,
				'default'            => 'top',
				'options'            => array(
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
				'prefix_class'       => 'trx-addons-tm-image%s-',
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'member_image_bg_color',
			[
				'label'                 => __( 'Background Color', 'trx_addons' ),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '',
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-tm-image' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'                  => 'member_image_border',
				'label'                 => __( 'Border', 'trx_addons' ),
				'placeholder'           => '1px',
				'default'               => '1px',
				'selector'              => '{{WRAPPER}} .trx-addons-tm-image',
			]
		);

		$this->add_responsive_control(
			'member_image_border_radius',
			[
				'label'                 => __( 'Border Radius', 'trx_addons' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-tm-image' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					// '{{WRAPPER}} .trx-addons-tm-overlay-content-wrap:before' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'member_image_margin',
			[
				'label'                 => __( 'Margin', 'trx_addons' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-tm-image' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'member_image_padding',
			[
				'label'                 => __( 'Padding', 'trx_addons' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-tm-image' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'                  => 'member_image_shadow',
				'selector'              => '{{WRAPPER}} .trx-addons-tm-image',
			]
		);

		$this->add_control(
			'hover_animation',
			array(
				'label' => __( 'Animation', 'trx_addons' ),
				'type'  => Controls_Manager::HOVER_ANIMATION,
				'prefix_class' => 'with-elementor-animation-',
				'render_type' => 'template',
			)
		);

		$this->add_control(
			'hover_animation_on_widget',
			array(
				'label' => __( 'Hover on Widget', 'trx_addons' ),
				'type'  => Controls_Manager::SWITCHER,
				'return_value' => 'widget',
				'prefix_class' => 'with-hover-on-',
				'render_type' => 'template',
				'condition' => [
					'hover_animation!' => '',
				],
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Register name style controls
	 *
	 * @return void
	 */
	protected function register_style_name_controls() {

		$this->start_controls_section(
			'section_member_name_style',
			[
				'label'                 => __( 'Name', 'trx_addons' ),
				'tab'                   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'                  => 'member_name_typography',
				'label'                 => __( 'Typography', 'trx_addons' ),
				'global'                => [
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				],
				'selector'              => '{{WRAPPER}} .trx-addons-tm-name',
			]
		);

		$this->add_control(
			'member_name_text_color',
			[
				'label'                 => __( 'Color', 'trx_addons' ),
				'type'                  => Controls_Manager::COLOR,
				'global'                => [
					'default' => Global_Colors::COLOR_PRIMARY,
				],
				'default'               => '',
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-tm-name' => 'color: {{VALUE}}',
					'{{WRAPPER}} .trx-addons-tm-name a' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'member_name_text_hover',
			[
				'label'                 => __( 'Hover Color', 'trx_addons' ),
				'type'                  => Controls_Manager::COLOR,
				'global'                => [
					'default' => Global_Colors::COLOR_PRIMARY,
				],
				'default'               => '',
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-tm-name a:hover' => 'color: {{VALUE}}',
				],
				'condition'             => [
					'link_type' => 'title',
				],
			]
		);

		$this->add_responsive_control(
			'member_name_margin',
			[
				'label'                 => __( 'Margin Bottom', 'trx_addons' ),
				'type'                  => Controls_Manager::SLIDER,
				'default'               => [
					'size' => 10,
					'unit' => 'px',
				],
				'size_units'            => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'range'                 => [
					'px' => [
						'max' => 100,
					],
				],
				'tablet_default'        => [
					'unit' => 'px',
				],
				'mobile_default'        => [
					'unit' => 'px',
				],
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-tm-name' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'name_divider_heading',
			[
				'label'                 => __( 'Divider', 'trx_addons' ),
				'type'                  => Controls_Manager::HEADING,
				'separator'             => 'before',
				'condition'             => [
					'member_title_divider' => 'yes',
				],
			]
		);

		$this->add_control(
			'name_divider_color',
			[
				'label'                 => __( 'Divider Color', 'trx_addons' ),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '',
				'global'                => [
					'default' => Global_Colors::COLOR_PRIMARY,
				],
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-tm-title-divider' => 'border-bottom-color: {{VALUE}}',
				],
				'condition'             => [
					'member_title_divider' => 'yes',
				],
			]
		);

		$this->add_control(
			'name_divider_style',
			[
				'label'                => __( 'Divider Style', 'trx_addons' ),
				'type'                 => Controls_Manager::SELECT,
				'default'              => 'solid',
				'options'              => [
					'solid'     => __( 'Solid', 'trx_addons' ),
					'dotted'    => __( 'Dotted', 'trx_addons' ),
					'dashed'    => __( 'Dashed', 'trx_addons' ),
					'double'    => __( 'Double', 'trx_addons' ),
				],
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-tm-title-divider' => 'border-bottom-style: {{VALUE}}',
				],
				'condition'             => [
					'member_title_divider' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'name_divider_width',
			[
				'label'                 => __( 'Divider Width', 'trx_addons' ),
				'type'                  => Controls_Manager::SLIDER,
				'default'               => [
					'size' => 100,
					'unit' => 'px',
				],
				'size_units'            => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'range'                 => [
					'px' => [
						'max' => 800,
					],
				],
				'tablet_default'        => [
					'unit' => 'px',
				],
				'mobile_default'        => [
					'unit' => 'px',
				],
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-tm-title-divider' => 'width: {{SIZE}}{{UNIT}};',
				],
				'condition'             => [
					'member_title_divider' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'name_divider_height',
			[
				'label'                 => __( 'Divider Height', 'trx_addons' ),
				'type'                  => Controls_Manager::SLIDER,
				'default'               => [
					'size' => 1,
				],
				'size_units'            => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'range'                 => [
					'px' => [
						'max' => 20,
					],
				],
				'tablet_default'        => [
					'unit' => 'px',
				],
				'mobile_default'        => [
					'unit' => 'px',
				],
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-tm-title-divider' => 'border-bottom-width: {{SIZE}}{{UNIT}};',
				],
				'condition'             => [
					'member_title_divider' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'name_divider_margin',
			[
				'label'                 => __( 'Margin Bottom', 'trx_addons' ),
				'type'                  => Controls_Manager::SLIDER,
				'default'               => [
					'size' => 10,
					'unit' => 'px',
				],
				'size_units'            => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'range'                 => [
					'px' => [
						'max' => 100,
					],
				],
				'tablet_default'        => [
					'unit' => 'px',
				],
				'mobile_default'        => [
					'unit' => 'px',
				],
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-tm-title-divider-wrap' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
				'condition'             => [
					'member_title_divider' => 'yes',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Register position style controls
	 *
	 * @return void
	 */
	protected function register_style_position_controls() {

		$this->start_controls_section(
			'section_member_position_style',
			[
				'label'                 => __( 'Position', 'trx_addons' ),
				'tab'                   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'                  => 'member_position_typography',
				'label'                 => __( 'Typography', 'trx_addons' ),
				'global'                => [
					'default' => Global_Typography::TYPOGRAPHY_SECONDARY,
				],
				'selector'              => '{{WRAPPER}} .trx-addons-tm-position',
			]
		);

		$this->add_control(
			'member_position_text_color',
			[
				'label'                 => __( 'Text Color', 'trx_addons' ),
				'type'                  => Controls_Manager::COLOR,
				'global'                => [
					'default' => Global_Colors::COLOR_SECONDARY,
				],
				'default'               => '',
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-tm-position' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'member_position_margin',
			[
				'label'                 => __( 'Margin Bottom', 'trx_addons' ),
				'type'                  => Controls_Manager::SLIDER,
				'default'               => [
					'size' => 10,
					'unit' => 'px',
				],
				'size_units'            => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'range'                 => [
					'px' => [
						'max' => 100,
					],
				],
				'tablet_default'        => [
					'unit' => 'px',
				],
				'mobile_default'        => [
					'unit' => 'px',
				],
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-tm-position' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'position_divider_heading',
			[
				'label'                 => __( 'Divider', 'trx_addons' ),
				'type'                  => Controls_Manager::HEADING,
				'separator'             => 'before',
				'condition'             => [
					'member_position_divider' => 'yes',
				],
			]
		);

		$this->add_control(
			'position_divider_color',
			[
				'label'                 => __( 'Divider Color', 'trx_addons' ),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '',
				'global'                => [
					'default' => Global_Colors::COLOR_ACCENT,
				],
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-tm-position-divider' => 'border-bottom-color: {{VALUE}}',
				],
				'condition'             => [
					'member_position_divider' => 'yes',
				],
			]
		);

		$this->add_control(
			'position_divider_style',
			[
				'label'                => __( 'Divider Style', 'trx_addons' ),
				'type'                 => Controls_Manager::SELECT,
				'default'              => 'solid',
				'options'              => [
					'solid'     => __( 'Solid', 'trx_addons' ),
					'dotted'    => __( 'Dotted', 'trx_addons' ),
					'dashed'    => __( 'Dashed', 'trx_addons' ),
					'double'    => __( 'Double', 'trx_addons' ),
				],
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-tm-position-divider' => 'border-bottom-style: {{VALUE}}',
				],
				'condition'             => [
					'member_position_divider' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'position_divider_width',
			[
				'label'                 => __( 'Divider Width', 'trx_addons' ),
				'type'                  => Controls_Manager::SLIDER,
				'default'               => [
					'size' => 100,
					'unit' => 'px',
				],
				'size_units'            => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'range'                 => [
					'px' => [
						'max' => 800,
					],
				],
				'tablet_default'        => [
					'unit' => 'px',
				],
				'mobile_default'        => [
					'unit' => 'px',
				],
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-tm-position-divider' => 'width: {{SIZE}}{{UNIT}};',
				],
				'condition'             => [
					'member_position_divider' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'position_divider_height',
			[
				'label'                 => __( 'Divider Height', 'trx_addons' ),
				'type'                  => Controls_Manager::SLIDER,
				'default'               => [
					'size' => 1,
				],
				'size_units'            => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'range'                 => [
					'px' => [
						'max' => 20,
					],
				],
				'tablet_default'        => [
					'unit' => 'px',
				],
				'mobile_default'        => [
					'unit' => 'px',
				],
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-tm-position-divider' => 'border-bottom-width: {{SIZE}}{{UNIT}};',
				],
				'condition'             => [
					'member_position_divider' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'position_divider_margin',
			[
				'label'                 => __( 'Margin Bottom', 'trx_addons' ),
				'type'                  => Controls_Manager::SLIDER,
				'default'               => [
					'size' => 10,
					'unit' => 'px',
				],
				'size_units'            => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'range'                 => [
					'px' => [
						'max' => 100,
					],
				],
				'tablet_default'        => [
					'unit' => 'px',
				],
				'mobile_default'        => [
					'unit' => 'px',
				],
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-tm-position-divider-wrap' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
				'condition'             => [
					'member_position_divider' => 'yes',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Register description style controls
	 *
	 * @return void
	 */
	protected function register_style_description_controls() {

		$this->start_controls_section(
			'section_member_description_style',
			[
				'label'                 => __( 'Description', 'trx_addons' ),
				'tab'                   => Controls_Manager::TAB_STYLE,
				'condition'             => [
					'team_member_description_switch' => 'yes',
					'team_member_description!' => '',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'                  => 'member_description_typography',
				'label'                 => __( 'Typography', 'trx_addons' ),
				'global'                => [
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				],
				'selector'              => '{{WRAPPER}} .trx-addons-tm-description',
				'condition'             => [
					'team_member_description_switch' => 'yes',
					'team_member_description!' => '',
				],
			]
		);

		$this->add_control(
			'member_description_text_color',
			[
				'label'                 => __( 'Text Color', 'trx_addons' ),
				'type'                  => Controls_Manager::COLOR,
				'global'                => [
					'default' => Global_Colors::COLOR_TEXT,
				],
				'default'               => '',
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-tm-description' => 'color: {{VALUE}}',
				],
				'condition'             => [
					'team_member_description_switch' => 'yes',
					'team_member_description!' => '',
				],
			]
		);

		$this->add_responsive_control(
			'member_description_margin',
			[
				'label'                 => __( 'Margin Bottom', 'trx_addons' ),
				'type'                  => Controls_Manager::SLIDER,
				'default'               => [
					'size' => 10,
					'unit' => 'px',
				],
				'size_units'            => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'range'                 => [
					'px' => [
						'max' => 100,
					],
				],
				'tablet_default'        => [
					'unit' => 'px',
				],
				'mobile_default'        => [
					'unit' => 'px',
				],
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-tm-description' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
				'condition'             => [
					'team_member_description_switch' => 'yes',
					'team_member_description!' => '',
				],
			]
		);

		$this->add_control(
			'description_divider_heading',
			[
				'label'                 => __( 'Divider', 'trx_addons' ),
				'type'                  => Controls_Manager::HEADING,
				'separator'             => 'before',
				'condition'             => [
					'team_member_description_switch' => 'yes',
					'team_member_description!' => '',
					'member_description_divider' => 'yes',
				],
			]
		);

		$this->add_control(
			'description_divider_color',
			[
				'label'                 => __( 'Divider Color', 'trx_addons' ),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '',
				'global'                => [
					'default' => Global_Colors::COLOR_PRIMARY,
				],
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-tm-description-divider' => 'border-bottom-color: {{VALUE}}',
				],
				'condition'             => [
					'team_member_description_switch' => 'yes',
					'team_member_description!' => '',
					'member_description_divider' => 'yes',
				],
			]
		);

		$this->add_control(
			'description_divider_style',
			[
				'label'                => __( 'Divider Style', 'trx_addons' ),
				'type'                 => Controls_Manager::SELECT,
				'default'              => 'solid',
				'options'              => [
					'solid'     => __( 'Solid', 'trx_addons' ),
					'dotted'    => __( 'Dotted', 'trx_addons' ),
					'dashed'    => __( 'Dashed', 'trx_addons' ),
					'double'    => __( 'Double', 'trx_addons' ),
				],
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-tm-description-divider' => 'border-bottom-style: {{VALUE}}',
				],
				'condition'             => [
					'team_member_description_switch' => 'yes',
					'team_member_description!' => '',
					'member_description_divider' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'description_divider_width',
			[
				'label'                 => __( 'Divider Width', 'trx_addons' ),
				'type'                  => Controls_Manager::SLIDER,
				'default'               => [
					'size' => 100,
					'unit' => 'px',
				],
				'size_units'            => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'range'                 => [
					'px' => [
						'max' => 800,
					],
				],
				'tablet_default'        => [
					'unit' => 'px',
				],
				'mobile_default'        => [
					'unit' => 'px',
				],
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-tm-description-divider' => 'width: {{SIZE}}{{UNIT}};',
				],
				'condition'             => [
					'team_member_description_switch' => 'yes',
					'team_member_description!' => '',
					'member_description_divider' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'description_divider_height',
			[
				'label'                 => __( 'Divider Height', 'trx_addons' ),
				'type'                  => Controls_Manager::SLIDER,
				'default'               => [
					'size' => 1,
				],
				'size_units'            => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'range'                 => [
					'px' => [
						'max' => 20,
					],
				],
				'tablet_default'        => [
					'unit' => 'px',
				],
				'mobile_default'        => [
					'unit' => 'px',
				],
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-tm-description-divider' => 'border-bottom-width: {{SIZE}}{{UNIT}};',
				],
				'condition'             => [
					'team_member_description_switch' => 'yes',
					'team_member_description!' => '',
					'member_description_divider' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'description_divider_margin',
			[
				'label'                 => __( 'Margin Bottom', 'trx_addons' ),
				'type'                  => Controls_Manager::SLIDER,
				'default'               => [
					'size' => 10,
					'unit' => 'px',
				],
				'size_units'            => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'range'                 => [
					'px' => [
						'max' => 100,
					],
				],
				'tablet_default'        => [
					'unit' => 'px',
				],
				'mobile_default'        => [
					'unit' => 'px',
				],
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-tm-description-divider-wrap' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
				'condition'             => [
					'team_member_description_switch' => 'yes',
					'team_member_description!' => '',
					'member_description_divider' => 'yes',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Register Icons style controls
	 *
	 * @return void
	 */
	protected function register_style_social_links_controls() {

		$this->start_controls_section(
			'section_member_social_links_style',
			[
				'label'                 => __( 'Icons', 'trx_addons' ),
				'tab'                   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'member_icons_gap',
			[
				'label'                 => __( 'Icons Gap', 'trx_addons' ),
				'type'                  => Controls_Manager::SLIDER,
				'size_units'            => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'range'                 => [
					'px' => [
						'max' => 60,
					],
				],
				'tablet_default'        => [
					'unit' => 'px',
				],
				'mobile_default'        => [
					'unit' => 'px',
				],
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-tm-social-links' => 'gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'member_icon_size',
			[
				'label'                 => __( 'Icon Size', 'trx_addons' ),
				'type'                  => Controls_Manager::SLIDER,
				'size_units'            => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'range'                 => [
					'px' => [
						'max' => 30,
					],
				],
				'default'    => [
					'size' => '14',
					'unit' => 'px',
				],
				'tablet_default'        => [
					'unit' => 'px',
				],
				'mobile_default'        => [
					'unit' => 'px',
				],
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-tm-social-links .trx-addons-tm-social-icon-wrap' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'member_icon_color',
			[
				'label'     => esc_html__( 'Color', 'trx_addons' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'default',
				'options'   => [
					'default' => esc_html__( 'Official Color', 'trx_addons' ),
					'custom'  => esc_html__( 'Custom', 'trx_addons' ),
				],
				'condition' => [
					'social_links_style' => 'button',
				],
			]
		);

		$this->start_controls_tabs( 'tabs_links_style' );

		$this->start_controls_tab(
			'tab_links_normal',
			[
				'label'      => __( 'Normal', 'trx_addons' ),
				'conditions' => array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'name'     => 'social_links_style',
							'operator' => '===',
							'value'    => 'icon',
						),
						array(
							'relation' => 'and',
							'terms'    => array(
								array(
									'name'     => 'social_links_style',
									'operator' => '===',
									'value'    => 'button',
								),
								array(
									'name'     => 'member_icon_color',
									'operator' => '==',
									'value'    => 'custom',
								),
							),
						),
					),
				),
			]
		);

		$this->add_control(
			'member_links_icons_color',
			[
				'label'      => __( 'Color', 'trx_addons' ),
				'type'       => Controls_Manager::COLOR,
				'default'    => '',
				'selectors'  => [
					'{{WRAPPER}} .trx-addons-tm-social-links .trx-addons-tm-social-icon-wrap' => 'color: {{VALUE}};',
					'{{WRAPPER}} .trx-addons-tm-social-links .trx-addons-tm-social-icon-wrap.elementor-social-icon i' => 'color: inherit;',
					'{{WRAPPER}} .trx-addons-tm-social-links .trx-addons-tm-social-icon-wrap svg' => 'fill: {{VALUE}};',
				],
				'conditions' => array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'name'     => 'social_links_style',
							'operator' => '===',
							'value'    => 'icon',
						),
						array(
							'relation' => 'and',
							'terms'    => array(
								array(
									'name'     => 'social_links_style',
									'operator' => '===',
									'value'    => 'button',
								),
								array(
									'name'     => 'member_icon_color',
									'operator' => '==',
									'value'    => 'custom',
								),
							),
						),
					),
				),
			]
		);

		$this->add_control(
			'member_links_bg_color',
			[
				'label'      => __( 'Background Color', 'trx_addons' ),
				'type'       => Controls_Manager::COLOR,
				'default'    => '',
				'selectors'  => [
					'{{WRAPPER}} .trx-addons-tm-social-links .trx-addons-tm-social-icon-wrap' => 'background-color: {{VALUE}};',
				],
				'conditions' => array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'name'     => 'social_links_style',
							'operator' => '===',
							'value'    => 'icon',
						),
						array(
							'relation' => 'and',
							'terms'    => array(
								array(
									'name'     => 'social_links_style',
									'operator' => '===',
									'value'    => 'button',
								),
								array(
									'name'     => 'member_icon_color',
									'operator' => '==',
									'value'    => 'custom',
								),
							),
						),
					),
				),
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'                  => 'member_links_border',
				'label'                 => __( 'Border', 'trx_addons' ),
				'placeholder'           => '1px',
				'default'               => '1px',
				'separator'             => 'before',
				'selector'              => '{{WRAPPER}} .trx-addons-tm-social-links .trx-addons-tm-social-icon-wrap',
			]
		);

		$this->add_responsive_control(
			'member_links_border_radius',
			[
				'label'                 => __( 'Border Radius', 'trx_addons' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-tm-social-links .trx-addons-tm-social-icon-wrap' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'member_links_padding',
			[
				'label'                 => __( 'Padding', 'trx_addons' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'separator'             => 'before',
				'selectors'             => [
					'{{WRAPPER}} .trx-addons-tm-social-links .trx-addons-tm-social-icon-wrap' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_links_hover',
			[
				'label'      => __( 'Hover', 'trx_addons' ),
				'conditions' => array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'name'     => 'social_links_style',
							'operator' => '===',
							'value'    => 'icon',
						),
						array(
							'relation' => 'and',
							'terms'    => array(
								array(
									'name'     => 'social_links_style',
									'operator' => '===',
									'value'    => 'button',
								),
								array(
									'name'     => 'member_icon_color',
									'operator' => '==',
									'value'    => 'custom',
								),
							),
						),
					),
				),
			]
		);

		$this->add_control(
			'member_links_icons_color_hover',
			[
				'label'      => __( 'Color', 'trx_addons' ),
				'type'       => Controls_Manager::COLOR,
				'default'    => '',
				'selectors'  => [
					'{{WRAPPER}} .trx-addons-tm-social-links .trx-addons-tm-social-icon-wrap:hover' => 'color: {{VALUE}};',
					'{{WRAPPER}} .trx-addons-tm-social-links .trx-addons-tm-social-icon-wrap.elementor-social-icon:hover i' => 'color: inherit;',
					'{{WRAPPER}} .trx-addons-tm-social-links .trx-addons-tm-social-icon-wrap:hover svg' => 'fill: {{VALUE}};',
				],
				'conditions' => array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'name'     => 'social_links_style',
							'operator' => '===',
							'value'    => 'icon',
						),
						array(
							'relation' => 'and',
							'terms'    => array(
								array(
									'name'     => 'social_links_style',
									'operator' => '===',
									'value'    => 'button',
								),
								array(
									'name'     => 'member_icon_color',
									'operator' => '==',
									'value'    => 'custom',
								),
							),
						),
					),
				),
			]
		);

		$this->add_control(
			'member_links_bg_color_hover',
			[
				'label'      => __( 'Background Color', 'trx_addons' ),
				'type'       => Controls_Manager::COLOR,
				'default'    => '',
				'selectors'  => [
					'{{WRAPPER}} .trx-addons-tm-social-links .trx-addons-tm-social-icon-wrap:hover' => 'background-color: {{VALUE}};',
				],
				'conditions' => array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'name'     => 'social_links_style',
							'operator' => '===',
							'value'    => 'icon',
						),
						array(
							'relation' => 'and',
							'terms'    => array(
								array(
									'name'     => 'social_links_style',
									'operator' => '===',
									'value'    => 'button',
								),
								array(
									'name'     => 'member_icon_color',
									'operator' => '==',
									'value'    => 'custom',
								),
							),
						),
					),
				),
			]
		);

		$this->add_control(
			'member_links_border_color_hover',
			[
				'label'      => __( 'Border Color', 'trx_addons' ),
				'type'       => Controls_Manager::COLOR,
				'default'    => '',
				'selectors'  => [
					'{{WRAPPER}} .trx-addons-tm-social-links .trx-addons-tm-social-icon-wrap:hover' => 'border-color: {{VALUE}};',
				],
				'conditions' => array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'name'     => 'social_links_style',
							'operator' => '===',
							'value'    => 'icon',
						),
						array(
							'relation' => 'and',
							'terms'    => array(
								array(
									'name'     => 'social_links_style',
									'operator' => '===',
									'value'    => 'button',
								),
								array(
									'name'     => 'member_icon_color',
									'operator' => '==',
									'value'    => 'custom',
								),
							),
						),
					),
				),
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
		$link_key = 'link';

		if ( 'none' !== $settings['link_type'] ) {
			if ( ! empty( $settings['link']['url'] ) ) {
				$this->add_link_attributes( $link_key, $settings['link'] );
			}
		}
		?>
		<div class="trx-addons-tm-wrapper">
			<div class="trx-addons-tm">
				<div class="trx-addons-tm-image"> 
					<?php $this->render_image(); ?>

					<?php if ( 'none' !== $settings['overlay_content'] ) { ?>
						<div class="trx-addons-tm-overlay-content-wrap">
							<div class="trx-addons-tm-overlay-content">
								<?php
								if ( 'yes' === $settings['member_social_links'] ) {
									if ( 'social_icons' === $settings['overlay_content'] ) {
										$this->render_social_links( 'overlay' );
									} elseif ( 'all_content' === $settings['overlay_content'] ) {
										if ( 'before_desc' === $settings['social_links_position'] ) {
											$this->render_social_links( 'overlay' );
										}
									}
								}

								if ( 'content' === $settings['overlay_content'] || 'all_content' === $settings['overlay_content'] ) {
									$this->render_description();
								}

								if ( 'yes' === $settings['member_social_links'] && 'all_content' === $settings['overlay_content'] ) {
									if ( 'after_desc' === $settings['social_links_position'] ) {
										$this->render_social_links( 'overlay' );
									}
								}
								?>
							</div>
						</div>
					<?php } ?>
				</div>
				<div class="trx-addons-tm-content">
					<?php
					// Name
					$this->render_name();

					// Position
					$this->render_position();

					if ( 'yes' === $settings['member_social_links'] && ( 'none' === $settings['overlay_content'] || 'content' === $settings['overlay_content'] ) ) {
						if ( 'none' === $settings['overlay_content'] ) {
							if ( 'before_desc' === $settings['social_links_position'] ) {
								$this->render_social_links( 'content' );
							}
						} else {
							$this->render_social_links( 'content' );
						}
					}

					if ( 'none' === $settings['overlay_content'] || 'social_icons' === $settings['overlay_content'] ) {
						$this->render_description();
					}

					if ( 'yes' === $settings['member_social_links'] && ( 'none' === $settings['overlay_content'] || 'content' === $settings['overlay_content'] ) ) {
						if ( 'after_desc' === $settings['social_links_position'] && 'none' === $settings['overlay_content'] ) {
							$this->render_social_links( 'content' );
						}
					}
					?>
				</div>
			</div>
		</div>
		<?php
	}

	protected function render_image() {
		$settings = $this->get_settings_for_display();
		$link_key = 'link';

		if ( ! empty( $settings['image']['url'] ) ) {
			if ( 'image' === $settings['link_type'] && $settings['link']['url'] ) {
				?>
				<a <?php echo wp_kses_post( $this->get_render_attribute_string( $link_key ) ); ?>><?php echo wp_kses_post( Group_Control_Image_Size::get_attachment_image_html( $settings ) ); ?></a>
				<?php
			} else {
				echo wp_kses_post( Group_Control_Image_Size::get_attachment_image_html( $settings ) );
			}
		}
	}

	protected function render_name() {
		$settings = $this->get_settings_for_display();

		$member_key = 'team_member_name';
		$link_key   = 'link';

		$this->add_inline_editing_attributes( $member_key, 'none' );
		$this->add_render_attribute( $member_key, 'class', 'trx-addons-tm-name' );

		if ( $settings[ $member_key ] ) {
			if ( 'title' === $settings['link_type'] && $settings['link']['url'] ) {
				$name_html_tag = $settings['name_html_tag'];
				?>
				<<?php echo esc_html( $name_html_tag ); ?> <?php echo wp_kses_post( $this->get_render_attribute_string( $member_key ) ); ?>>
					<a <?php echo wp_kses_post( $this->get_render_attribute_string( $link_key ) ); ?>>
						<?php echo esc_html( $settings['team_member_name'] ); ?>
					</a>
				</<?php echo esc_html( $name_html_tag ); ?>>
				<?php
			} else {
				$name_html_tag = $settings['name_html_tag'];
				?>
				<<?php echo esc_html( $name_html_tag ); ?> <?php echo wp_kses_post( $this->get_render_attribute_string( $member_key ) ); ?>>
						<?php echo esc_html( $settings['team_member_name'] ); ?>
				</<?php echo esc_html( $name_html_tag ); ?>>
				<?php
			}
		}

		if ( 'yes' === $settings['member_title_divider'] ) {
			?>
			<div class="trx-addons-tm-title-divider-wrap">
				<div class="trx-addons-tm-divider trx-addons-tm-title-divider"></div>
			</div>
			<?php
		}
	}

	protected function render_position() {
		$settings = $this->get_settings_for_display();
		$this->add_inline_editing_attributes( 'team_member_position', 'none' );
		$this->add_render_attribute( 'team_member_position', 'class', 'trx-addons-tm-position' );

		if ( $settings['team_member_position'] ) {
			$position_html_tag = $settings['position_html_tag'];
			?>
			<<?php echo esc_html( $position_html_tag ); ?> <?php echo wp_kses_post( $this->get_render_attribute_string( 'team_member_position' ) ); ?>>
				<?php echo wp_kses_post( $settings['team_member_position'] ); ?>
			</<?php echo esc_html( $position_html_tag ); ?>>
			<?php
		}

		if ( 'yes' === $settings['member_position_divider'] ) {
			?>
			<div class="trx-addons-tm-position-divider-wrap">
				<div class="trx-addons-tm-divider trx-addons-tm-position-divider"></div>
			</div>
			<?php
		}
	}

	protected function render_description() {
		$settings = $this->get_settings_for_display();
		$this->add_inline_editing_attributes( 'team_member_description', 'basic' );
		$this->add_render_attribute( 'team_member_description', 'class', 'trx-addons-tm-description' );

		if ( 'yes' === $settings['team_member_description_switch'] ) {
			if ( $settings['team_member_description'] ) {
				?>
				<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'team_member_description' ) ); ?>>
					<?php echo wp_kses_post( $this->parse_text_editor( $settings['team_member_description'] ) ); ?>
				</div>
			<?php } ?>
			<?php if ( 'yes' === $settings['member_description_divider'] ) { ?>
				<div class="trx-addons-tm-description-divider-wrap">
					<div class="trx-addons-tm-divider trx-addons-tm-description-divider"></div>
				</div>
				<?php
			}
		}
	}

	protected function render_social_links( $pos ) {
		$settings = $this->get_settings_for_display();
		$i = 1;
		$alignment = $pos == 'overlay' ? $settings['overlay_alignment'] : $settings['member_box_alignment'];

		$fallback_defaults = [
			'fa fa-facebook',
			'fa fa-twitter',
			'fa fa-google-plus',
		];

		$migration_allowed = Icons_Manager::is_migration_allowed();

		// add old default
		if ( ! isset( $item['icon'] ) && ! $migration_allowed ) {
			$item['icon'] = isset( $fallback_defaults[ $index ] ) ? $fallback_defaults[ $index ] : 'fa fa-check';
		}

		$migrated = isset( $item['__fa4_migrated']['select_social_icon'] );
		$is_new = ! isset( $item['icon'] ) && $migration_allowed;
		?>
		<div class="trx-addons-tm-social-links-wrap">
			<ul class="trx-addons-tm-social-links trx-addons-tm-social-links-align-<?php echo esc_attr( $alignment ); ?>"><?php
				foreach ( $settings['team_member_social'] as $index => $item ) {
					$migrated = isset( $item['__fa4_migrated']['select_social_icon'] );
					$is_new = empty( $item['social_icon'] ) && $migration_allowed;
					$social = '';

					// add old default
					if ( empty( $item['social_icon'] ) && ! $migration_allowed ) {
						$item['social_icon'] = isset( $fallback_defaults[ $index ] ) ? $fallback_defaults[ $index ] : 'fa fa-wordpress';
					}

					if ( ! empty( $item['social_icon'] ) ) {
						$social = str_replace( 'fa fa-', '', $item['social_icon'] );
					}

					if ( ( $is_new || $migrated ) && 'svg' !== $item['select_social_icon']['library'] ) {
						$social = explode( ' ', $item['select_social_icon']['value'], 2 );
						if ( empty( $social[1] ) ) {
							$social = '';
						} else {
							$social = str_replace( 'fa-', '', $social[1] );
						}
					}
					if ( 'svg' === $item['select_social_icon']['library'] ) {
						$social = '';
					}

					$social_link_key = 'social_link' . $i;
					$icon_wrap_key   = 'icon_wrap' . $i;

					$this->add_render_attribute( $social_link_key, 'class', 'trx-addons-tm-social-link' );
					$this->add_render_attribute( $icon_wrap_key, 'class', array( 'trx-addons-tm-social-icon-wrap', 'trx-addons-social-icon-wrap', 'trx-addons-social-icon-wrap-' . $social ) );

					if ( 'button' === $settings['social_links_style'] ) {
						$this->add_render_attribute( $icon_wrap_key, 'class', [
							'elementor-icon',
							'elementor-social-icon',
							'elementor-social-icon-' . $social,
						] );
					}

					if ( ! empty( $item['social_link']['url'] ) ) {
						$this->add_link_attributes( $social_link_key, $item['social_link'] );
					}
					?><li>
						<a <?php echo wp_kses_post( $this->get_render_attribute_string( $social_link_key ) ); ?>>
							<span <?php echo wp_kses_post( $this->get_render_attribute_string( $icon_wrap_key ) ); ?>>
								<span class="elementor-screen-only"><?php echo esc_html( ucwords( $social ) ); ?></span>
								<span class="trx-addons-tm-social-icon trx-addons-icon">
								<?php
								if ( $is_new || $migrated ) {
									Icons_Manager::render_icon( $item['select_social_icon'], array( 'aria-hidden' => 'true' ) );
								} else {
									?>
									<i class="<?php echo esc_attr( $item['social_icon'] ); ?>"></i>
								<?php } ?>
								</span>
							</span>
						</a>
					</li><?php
					$i++;
				}
			?></ul>
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
		?><#
		function member_image() {
			if ( '' !== settings.image.url ) {
				var image = {
					id: settings.image.id,
					url: settings.image.url,
					size: settings.image_size,
					dimension: settings.image_custom_dimension,
					model: view.getEditModel()
				};

				var image_url = elementor.imagesManager.getImageUrl( image );

				var imageHtml = '<img src="' + _.escape( image_url ) + '"' + ( settings.hover_animation ? ' class="elementor-animation-' + settings.hover_animation + '"' : '' ) +  ' />';

				if ( settings.link_type == 'image' && settings.link.url != '' ) {
					imageHtml = '<a href="' + _.escape( settings.link.url ) + '">' + imageHtml + '</a>';
				}

				print( imageHtml );
			}
		}

		function member_name() {
			if ( settings.team_member_name != '' ) {
				var name = settings.team_member_name;

				view.addRenderAttribute( 'team_member_name', 'class', 'trx-addons-tm-name' );

				view.addInlineEditingAttributes( 'team_member_name' );

				var nameHTMLTag = elementor.helpers.validateHTMLTag( settings.name_html_tag ),
					name_html = '<' + nameHTMLTag  + ' ' + view.getRenderAttributeString( 'team_member_name' ) + '>' + name + '</' + nameHTMLTag + '>';
			}

			if ( settings.link_type == 'title' && settings.link.url != '' ) { #>
				<#
				var target = settings.link.is_external ? ' target="_blank"' : '';
				var nofollow = settings.link.nofollow ? ' rel="nofollow"' : '';
				#>
				<a href="{{ _.escape( settings.link.url ) }}"{{ target }}{{ nofollow }}>
					<# print( name_html ); #>
				</a>
			<# } else {
				print( name_html );
			}

			if ( settings.member_title_divider == 'yes' ) { #>
				<div class="trx-addons-tm-title-divider-wrap">
					<div class="trx-addons-tm-divider trx-addons-tm-title-divider"></div>
				</div>
			<# }
		}

		function member_position() {
			if ( settings.team_member_position != '' ) {
				var position = settings.team_member_position;

				view.addRenderAttribute( 'team_member_position', 'class', 'trx-addons-tm-position' );

				view.addInlineEditingAttributes( 'team_member_position' );

				var positionHTMLTag = elementor.helpers.validateHTMLTag( settings.position_html_tag ),
					position_html = '<' + positionHTMLTag  + ' ' + view.getRenderAttributeString( 'team_member_position' ) + '>' + position + '</' + positionHTMLTag + '>';

				print( position_html );
			}

			if ( settings.member_position_divider == 'yes' ) { #>
				<div class="trx-addons-tm-position-divider-wrap">
					<div class="trx-addons-tm-divider trx-addons-tm-position-divider"></div>
				</div>
			<# }
		}

		function member_description() {
			if ( settings.team_member_description_switch == 'yes' ) {
				if ( settings.team_member_description != '' ) {
					var description = settings.team_member_description;

					view.addRenderAttribute( 'team_member_description', 'class', 'trx-addons-tm-description' );

					view.addInlineEditingAttributes( 'team_member_description', 'advanced' );

					var descriptionHTMLTag = 'div',
						description_html = '<' + descriptionHTMLTag  + ' ' + view.getRenderAttributeString( 'team_member_description' ) + '>' + description + '</' + descriptionHTMLTag + '>';

					print( description_html );
				}

				if ( settings.member_description_divider == 'yes' ) { #>
					<div class="trx-addons-tm-description-divider-wrap">
						<div class="trx-addons-tm-divider trx-addons-tm-description-divider"></div>
					</div>
				<# }
			}
		}

		function member_social_links( pos ) { #>
			<#
			var iconsHTML = {},
				alignment = pos == 'overlay' ? settings.overlay_alignment : settings.member_box_alignment;
			#>
			<div class="trx-addons-tm-social-links-wrap">
				<ul class="trx-addons-tm-social-links trx-addons-tm-social-links-align-{{ alignment }}"><#
					_.each( settings.team_member_social, function( item, index ) {
						var link = item.social_link ? item.social_link.url : '',
							migrated = elementor.helpers.isIconMigrated( item, 'select_social_icon' );
							social = elementor.helpers.getSocialNetworkNameFromIcon( item.select_social_icon, item.social_icon, false, migrated );

							var socialLinkKey = view.getRepeaterSettingKey( 'text', 'social_link', index );
							var iconWrapKey = view.getRepeaterSettingKey( 'text', 'icon_wrap', index );

							view.addRenderAttribute( socialLinkKey, 'class', 'trx-addons-tm-social-link' );
							view.addRenderAttribute( iconWrapKey, 'class', [ 'trx-addons-tm-social-icon-wrap', 'trx-addons-social-icon-wrap', 'trx-addons-social-icon-wrap-' + social ] );

							if ( 'button' == settings.social_links_style ) {
								view.addRenderAttribute( iconWrapKey, 'class', [
									'elementor-icon',
									'elementor-social-icon',
									'elementor-social-icon-' + social,
								] );
							}

							view.addRenderAttribute( socialLinkKey, 'href', link );
						#><li><#
							if ( item.social_icon || item.select_social_icon ) {
								#><a {{{ view.getRenderAttributeString( socialLinkKey ) }}}>
									<span {{{ view.getRenderAttributeString( iconWrapKey ) }}}>
										<span class="trx-addons-tm-social-icon trx-addons-icon">
											<span class="elementor-screen-only">{{{ social }}}</span>
											<#
												iconsHTML[ index ] = elementor.helpers.renderIcon( view, item.select_social_icon, {}, 'i', 'object' );
												if ( ( ! item.social_icon || migrated ) && iconsHTML[ index ] && iconsHTML[ index ].rendered ) { #>
													{{{ iconsHTML[ index ].value }}}
												<# } else { #>
													<i class="{{ item.social_icon }}"></i>
												<# }
											#>
										</span>
									</span>
								</a><#
							}
						#></li><#
					} );
				#></ul>
			</div><#
		}
		#>

		<div class="trx-addons-tm-wrapper">
			<div class="trx-addons-tm">
				<div class="trx-addons-tm-image"> 
					<# member_image(); #>

					<# if ( settings.overlay_content != 'none' ) { #>
						<div class="trx-addons-tm-overlay-content-wrap">
							<div class="trx-addons-tm-overlay-content">
								<#
									if ( settings.member_social_links == 'yes' ) {
										if ( settings.overlay_content == 'social_icons' ) {
											member_social_links( 'overlay' );
										} else if ( settings.overlay_content == 'all_content' ) {
											if ( settings.social_links_position == 'before_desc' ) {
												member_social_links( 'overlay' );
											}
										}
									}

									if ( settings.overlay_content == 'content' || settings.overlay_content == 'all_content' ) {
										member_description();
									}

									if ( settings.member_social_links == 'yes' && settings.overlay_content == 'all_content' ) {
										if ( settings.social_links_position == 'after_desc' ) {
											member_social_links( 'overlay' );
										}
									}
								#>
							</div>
						</div>
					<# } #>
				</div>
				<div class="trx-addons-tm-content">
					<#
						member_name();
						member_position();

						if ( settings.member_social_links == 'yes' && ( settings.overlay_content == 'none' || settings.overlay_content == 'content' ) ) {
							if ( settings.overlay_content == 'none' ) {
								if ( settings.social_links_position == 'before_desc' ) {
									member_social_links( 'content' );
								}
							} else {
								member_social_links( 'content' );
							}
						}

						if ( settings.overlay_content == 'none' || settings.overlay_content == 'social_icons' ) {
							member_description();
						}

						if ( settings.member_social_links == 'yes' && ( settings.overlay_content == 'none' || settings.overlay_content == 'content' ) ) {
							if ( settings.social_links_position == 'after_desc' && settings.overlay_content == 'none' ) {
								member_social_links( 'content' );
							}
						}
					#>
				</div>
			</div>
		</div>
		<?php
	}
}
