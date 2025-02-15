<?php
/**
 * NavMenu Widget
 *
 * @package ThemeREX Addons
 * @since v2.30.0
 */

namespace TrxAddons\ElementorWidgets\Widgets\NavMenu;

use TrxAddons\ElementorWidgets\BaseWidget;
use TrxAddons\ElementorWidgets\Utils as TrxAddonsUtils;

// Elementor Classes.
use Elementor\Control_Media;
use Elementor\Repeater;
use Elementor\Utils;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * NavMenu Widget
 */
class NavMenuWidget extends BaseWidget {

	/**
	 * Register widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 */
	protected function register_controls() {
		/* Content Tab */
		$this->register_content_settings_controls();
		$this->register_content_menu_controls();
		$this->register_content_dropdown_controls();
		$this->register_content_help_docs_controls();

		/* Style Tab */
		// $this->register_style_sticky_controls();
		$this->register_style_vertical_toggler_controls();
		$this->register_style_menu_container_controls();
		$this->register_style_menu_item_controls();
		$this->register_style_menu_item_icon_controls();
		$this->register_style_menu_item_badge_controls();
		$this->register_style_submenu_container_controls();
		$this->register_style_submenu_item_controls();
		$this->register_style_submenu_item_icon_controls();
		$this->register_style_submenu_item_badge_controls();
		$this->register_style_toggle_menu_controls();
		$this->register_style_close_button_controls();
	}

	/*-----------------------------------------------------------------------------------*/
	/*	CONTENT TAB
	/*-----------------------------------------------------------------------------------*/

	/**
	 * Register menu content controls.
	 */
	private function register_content_settings_controls() {

		$this->start_controls_section(
			'nav_section',
			array(
				'label' => __( 'Menu Settings', 'trx_addons' ),
			)
		);

		$this->add_control(
			'menu_type',
			array(
				'label'   => __( 'Menu Type', 'trx_addons' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'wordpress_menu',
				'options' => array(
					'wordpress_menu' => __( 'WordPress Menu', 'trx_addons' ),
					'custom'         => __( 'Custom Menu', 'trx_addons' ),
				),
			)
		);

		$menu_list = $this->get_menu_list();

		if ( ! empty( $menu_list ) ) {

			$this->add_control(
				'nav_menus',
				array(
					'label'     => __( 'Menu', 'trx_addons' ),
					'type'      => Controls_Manager::SELECT,
					'options'   => $menu_list,
					'condition' => array(
						'menu_type' => 'wordpress_menu',
					),
				)
			);

		} else {
			$this->add_control(
				'empty_nav_menu_notice',
				array(
					'raw'             => '<strong>' . __( 'There are no menus in your site.', 'trx_addons' ) . '</strong><br>' . sprintf( __( 'Go to the <a href="%s" target="_blank">Menus screen</a> to create one.', 'trx_addons' ), admin_url( 'nav-menus.php?action=edit&menu=0' ) ),
					'type'            => Controls_Manager::RAW_HTML,
					'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
					'condition'       => array(
						'menu_type' => 'wordpress_menu',
					),
				)
			);
		}

		$this->add_control(
			'custom_nav_notice',
			array(
				'raw'             => __( 'It\'s not recommended to use Elementor Template and Link Submenu Items together under the same menu item', 'trx_addons' ),
				'type'            => Controls_Manager::RAW_HTML,
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning',
				'condition'       => array(
					'menu_type' => 'custom',
				),
			)
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'item_type',
			array(
				'label'   => __( 'Item Type', 'trx_addons' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'menu',
				'options' => array(
					'menu'    => __( 'Menu', 'trx_addons' ),
					'submenu' => __( 'Submenu', 'trx_addons' ),
				),
			)
		);

		$repeater->add_control(
			'menu_content_type',
			array(
				'label'     => __( 'Content Type', 'trx_addons' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					'link'           => __( 'Link', 'trx_addons' ),
					'layout'         => __( 'Theme Layout', 'trx_addons' ),
					'custom_content' => __( 'Elementor Template', 'trx_addons' ),
					'element'        => __( 'Element On Page', 'trx_addons' ),
				),
				'default'   => 'link',
				'condition' => array(
					'item_type' => 'submenu',
				),
			)
		);

		$repeater->add_control(
			'text',
			array(
				'label'      => __( 'Text', 'trx_addons' ),
				'type'       => Controls_Manager::TEXT,
				'default'    => __( 'Item', 'trx_addons' ),
				'dynamic'    => array(
					'active' => true,
				),
				'conditions' => array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'name'     => 'item_type',
							'operator' => '==',
							'value'    => 'menu',
						),
						array(
							'name'     => 'menu_content_type',
							'operator' => '==',
							'value'    => 'link',
						),
					),
				),
			)
		);

		$repeater->add_control(
			'link',
			array(
				'label'      => __( 'Link', 'trx_addons' ),
				'type'       => Controls_Manager::URL,
				'default'    => array(
					'url'         => '#',
					'is_external' => '',
				),
				'dynamic'    => array(
					'active' => true,
				),
				'conditions' => array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'name'     => 'item_type',
							'operator' => '==',
							'value'    => 'menu',
						),
						array(
							'name'     => 'menu_content_type',
							'operator' => '==',
							'value'    => 'link',
						),
					),
				),
			)
		);

		$repeater->add_control(
			'element_selector',
			array(
				'label'     => __( 'Element CSS Selector', 'trx_addons' ),
				'type'      => Controls_Manager::TEXT,
				'condition' => array(
					'item_type'         => 'submenu',
					'menu_content_type' => 'element',
				),
			)
		);

		// $repeater->add_control(
		// 	'live_temp_content',
		// 	array(
		// 		'label'       => __( 'Template Title', 'trx_addons' ),
		// 		'type'        => Controls_Manager::TEXT,
		// 		'classes'     => 'trx-addons-live-temp-title control-hidden',
		// 		'label_block' => true,
		// 		'condition'   => array(
		// 			'item_type'         => 'submenu',
		// 			'menu_content_type' => 'custom_content',
		// 		),
		// 	)
		// );

		// $repeater->add_control(
		// 	'submenu_item_live',
		// 	array(
		// 		'type'        => Controls_Manager::BUTTON,
		// 		'label_block' => true,
		// 		'button_type' => 'default papro-btn-block',
		// 		'text'        => __( 'Create / Edit Template', 'trx_addons' ),
		// 		'event'       => 'createLiveTemp',
		// 		'condition'   => array(
		// 			'item_type'         => 'submenu',
		// 			'menu_content_type' => 'custom_content',
		// 		),
		// 	)
		// );

		// Detect edit mode
		$is_edit_mode = trx_addons_elm_is_edit_mode();
		$templates = ! $is_edit_mode ? array() : TrxAddonsUtils::get_elementor_page_list();	//trx_addons_get_list_elementor_templates();
		$layouts   = ! $is_edit_mode ? array() : trx_addons_get_list_layouts();

		$repeater->add_control(
			'submenu_item',
			array(
				'label'       => __( 'Select Existing Template', 'trx_addons' ),
				'type'        => Controls_Manager::SELECT2,
				'classes'     => 'trx-addons-live-temp-label',
				'label_block' => true,
				'options'     => $templates,
				'condition'   => array(
					'item_type'         => 'submenu',
					'menu_content_type' => 'custom_content',
				),
			)
		);

		$repeater->add_control(
			'submenu_layout',
			array(
				'label'       => __( 'Select Existing Layout', 'trx_addons' ),
				'type'        => Controls_Manager::SELECT2,
				'classes'     => 'trx-addons-live-temp-label',
				'label_block' => true,
				'options'     => $layouts,
				'condition'   => array(
					'item_type'         => 'submenu',
					'menu_content_type' => 'layout',
				),
			)
		);

		$repeater->add_control(
			'section_full_width',
			array(
				'label'       => __( 'Full Width Dropdown', 'trx_addons' ),
				'type'        => Controls_Manager::SWITCHER,
				'description' => __( 'Enable this option to set the dropdown width to the same width of the wrapper below', 'trx_addons' ),
				'condition'   => array(
					'item_type' => 'menu',
				),
			)
		);

		$repeater->add_control(
			'section_full_width_wrapper',
			array(
				'label'       => __( 'Full Width Wrapper', 'trx_addons' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'window'       => __( 'Window', 'trx_addons' ),
					'window_boxed' => __( 'Window Boxed', 'trx_addons' ),
					'content'      => __( 'Content', 'trx_addons' ),
				),
				'default'     => 'content',
				'condition'   => array(
					'item_type' => 'menu',
					'section_full_width' => 'yes',
				),
			)
		);

		$repeater->add_responsive_control(
			'section_width',
			array(
				'label'     => __( 'Dropdown Minimum Width', 'trx_addons' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min' => 0,
						'max' => 1500,
					),
				),
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors' => array(
					'{{WRAPPER}} {{CURRENT_ITEM}} ul.trx-addons-submenu,
					 {{WRAPPER}} {{CURRENT_ITEM}} .trx-addons-mega-content-container' => 'min-width: {{SIZE}}{{UNIT}}',
				),
				'condition' => array(
					'item_type'           => 'menu',
					'section_full_width!' => 'yes',
				),
			)
		);

		$repeater->add_responsive_control(
			'section_position',
			array(
				'label'       => __( 'Align to Widget Center', 'trx_addons' ),
				'type'        => Controls_Manager::SWITCHER,
				'description' => __( 'This option centers the mega content to the center of the widget container. <b> Only works when Full Width Dropdown option is disabled </b>', 'trx_addons' ),
				'condition'   => array(
					'item_type'         => 'submenu',
					'menu_content_type' => array( 'custom_content', 'element' ),
				),
			)
		);

		$repeater->add_control(
			'icon_switcher',
			array(
				'label'      => __( 'Icon', 'trx_addons' ),
				'type'       => Controls_Manager::SWITCHER,
				'conditions' => array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'name'     => 'item_type',
							'operator' => '==',
							'value'    => 'menu',
						),
						array(
							'name'     => 'menu_content_type',
							'operator' => '==',
							'value'    => 'link',
						),
					),
				),
			)
		);

		$repeater->add_control(
			'icon_type',
			array(
				'label'       => __( 'Icon Type', 'trx_addons' ),
				'type'        => Controls_Manager::SELECT,
				'description' => __( 'Use a font awesome icon or upload a custom image', 'trx_addons' ),
				'options'     => array(
					'icon'      => __( 'Icon', 'trx_addons' ),
					'image'     => __( 'Image', 'trx_addons' ),
					// 'animation' => __( 'Lottie Animation', 'trx_addons' ),
				),
				'default'     => 'icon',
				'conditions'  => array(
					'relation' => 'and',
					'terms'    => array(
						array(
							'name'  => 'icon_switcher',
							'value' => 'yes',
						),
						array(
							'relation' => 'or',
							'terms'    => array(
								array(
									'name'     => 'item_type',
									'operator' => '==',
									'value'    => 'menu',
								),
								array(
									'name'     => 'menu_content_type',
									'operator' => '==',
									'value'    => 'link',
								),
							),
						),

					),
				),
			)
		);

		$repeater->add_control(
			'item_icon',
			array(
				'label'                  => __( 'Select an Icon', 'trx_addons' ),
				'label_block'            => false,
				'type'                   => Controls_Manager::ICONS,
				'skin'                   => 'inline',
				// 'exclude_inline_options' => array( 'svg' ),
				'conditions'             => array(
					'relation' => 'and',
					'terms'    => array(
						array(
							'name'  => 'icon_switcher',
							'value' => 'yes',
						),
						array(
							'name'  => 'icon_type',
							'value' => 'icon',
						),
						array(
							'relation' => 'or',
							'terms'    => array(
								array(
									'name'     => 'item_type',
									'operator' => '==',
									'value'    => 'menu',
								),
								array(
									'name'     => 'menu_content_type',
									'operator' => '==',
									'value'    => 'link',
								),
							),
						),

					),
				),
			)
		);

		$repeater->add_control(
			'item_image',
			array(
				'label'      => __( 'Upload Image', 'trx_addons' ),
				'type'       => Controls_Manager::MEDIA,
				'default'    => array(
					'url' => Utils::get_placeholder_image_src(),
				),
				'conditions' => array(
					'relation' => 'and',
					'terms'    => array(
						array(
							'name'  => 'icon_switcher',
							'value' => 'yes',
						),
						array(
							'name'  => 'icon_type',
							'value' => 'image',
						),
						array(
							'relation' => 'or',
							'terms'    => array(
								array(
									'name'     => 'item_type',
									'operator' => '==',
									'value'    => 'menu',
								),
								array(
									'name'     => 'menu_content_type',
									'operator' => '==',
									'value'    => 'link',
								),
							),
						),

					),
				),
			)
		);

		$repeater->add_control(
			'lottie_url',
			array(
				'label'       => __( 'Animation JSON URL', 'trx_addons' ),
				'type'        => Controls_Manager::TEXT,
				'description' => 'Get JSON code URL from <a href="https://lottiefiles.com/" target="_blank">here</a>',
				'label_block' => true,
				'conditions'  => array(
					'relation' => 'and',
					'terms'    => array(
						array(
							'name'  => 'icon_switcher',
							'value' => 'yes',
						),
						array(
							'name'  => 'icon_type',
							'value' => 'animation',
						),
						array(
							'relation' => 'or',
							'terms'    => array(
								array(
									'name'     => 'item_type',
									'operator' => '==',
									'value'    => 'menu',
								),
								array(
									'name'     => 'menu_content_type',
									'operator' => '==',
									'value'    => 'link',
								),
							),
						),

					),
				),
			)
		);

		$repeater->add_control(
			'badge_switcher',
			array(
				'label'      => __( 'Badge', 'trx_addons' ),
				'type'       => Controls_Manager::SWITCHER,
				'separator'  => 'before',
				'conditions' => array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'name'     => 'item_type',
							'operator' => '==',
							'value'    => 'menu',
						),
						array(
							'name'     => 'menu_content_type',
							'operator' => '==',
							'value'    => 'link',
						),
					),
				),
			)
		);

		$repeater->add_control(
			'badge_text',
			array(
				'label'      => __( 'Badge Text', 'trx_addons' ),
				'type'       => Controls_Manager::TEXT,
				'conditions' => array(
					'relation' => 'and',
					'terms'    => array(
						array(
							'name'  => 'badge_switcher',
							'value' => 'yes',
						),
						array(
							'relation' => 'or',
							'terms'    => array(
								array(
									'name'     => 'item_type',
									'operator' => '==',
									'value'    => 'menu',
								),
								array(
									'name'     => 'menu_content_type',
									'operator' => '==',
									'value'    => 'link',
								),
							),
						),

					),
				),
			)
		);

		// do_action( 'trx_addons_action_custom_menu_controls', $this, $repeater );

		$this->add_control( 'menu_items', array(
			'type' => \Elementor\Controls_Manager::REPEATER,
			'label' => __( 'Menu items', 'trx_addons' ),
			'fields' => apply_filters('trx_addons_sc_param_group_params', $repeater->get_controls(), 'trx_sc_bg_slides' ),
			'title_field' => '{{{ text }}} | {{{ item_type }}} ({{{ menu_content_type }}})',
			'condition' => array(
				'menu_type' => 'custom',
			)
		) );

		$this->end_controls_section();
	}

	/**
	 * Register menu content controls.
	 */
	private function register_content_menu_controls() {

		$this->start_controls_section(
			'display_options_section',
			array(
				'label' => __( 'Display Options', 'trx_addons' ),
			)
		);

		$this->add_control(
			'menu_heading',
			array(
				'label' => __( 'Menu Settings', 'trx_addons' ),
				'type'  => Controls_Manager::HEADING,
			)
		);

		$this->add_control(
			'nav_menu_layout',
			array(
				'label'        => __( 'Layout', 'trx_addons' ),
				'type'         => Controls_Manager::SELECT,
				'prefix_class' => 'trx-addons-nav-',
				'options'      => array(
					'hor'      => 'Horizontal',
					'ver'      => 'Vertical',
					'dropdown' => 'Expand',
					'slide'    => 'Slide',
				),
				'render_type'  => 'template',
				'default'      => 'hor',
			)
		);

		$align_left  = is_rtl() ? 'flex-end' : 'flex-start';
		$align_right = is_rtl() ? 'flex-start' : 'flex-end';
		$align_def   = is_rtl() ? 'flex-end' : 'flex-start';

		$this->add_responsive_control(
			'nav_menu_align',
			array(
				'label'     => __( 'Menu Alignment', 'trx_addons' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					$align_left     => array(
						'title' => __( 'Left', 'trx_addons' ),
						'icon'  => 'eicon-h-align-left',
					),
					'center'        => array(
						'title' => __( 'Center', 'trx_addons' ),
						'icon'  => 'eicon-h-align-center',
					),
					$align_right    => array(
						'title' => __( 'Right', 'trx_addons' ),
						'icon'  => 'eicon-h-align-right',
					),
					'space-between' => array(
						'title' => __( 'Strech', 'trx_addons' ),
						'icon'  => 'eicon-h-align-stretch',
					),
				),
				'default'   => 'center',
				'toggle'    => false,
				'selectors' => array(
					'{{WRAPPER}} .trx-addons-main-nav-menu' => 'justify-content: {{VALUE}}',
				),
				'condition' => array(
					'nav_menu_layout' => 'hor',
				),
			)
		);

		$this->add_responsive_control(
			'nav_menu_align_ver',
			array(
				'label'     => __( 'Menu Alignment', 'trx_addons' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					$align_left  => array(
						'title' => __( 'Left', 'trx_addons' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center'     => array(
						'title' => __( 'Center', 'trx_addons' ),
						'icon'  => 'eicon-text-align-center',
					),
					$align_right => array(
						'title' => __( 'Right', 'trx_addons' ),
						'icon'  => 'eicon-text-align-right',
					),
				),
				'default'   => $align_def,
				'toggle'    => false,
				'selectors' => array(
					'{{WRAPPER}} .trx-addons-main-nav-menu > .trx-addons-nav-menu-item > .trx-addons-menu-link' => 'justify-content: {{VALUE}}',
				),
				'condition' => array(
					'nav_menu_layout' => 'ver',
				),
			)
		);

		$this->add_control(
			'pointer',
			array(
				'label'          => __( 'Item Hover Effect', 'trx_addons' ),
				'type'           => Controls_Manager::SELECT,
				'default'        => 'none',
				'options'        => array(
					'none'        => __( 'None', 'trx_addons' ),
					'underline'   => __( 'Underline', 'trx_addons' ),
					'overline'    => __( 'Overline', 'trx_addons' ),
					'double-line' => __( 'Double Line', 'trx_addons' ),
					'framed'      => __( 'Framed', 'trx_addons' ),
					'background'  => __( 'Background', 'trx_addons' ),
					'text'        => __( 'Text', 'trx_addons' ),
				),
				'style_transfer' => true,
			)
		);

		$this->add_control(
			'animation_line',
			array(
				'label'     => __( 'Animation', 'trx_addons' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'fade',
				'options'   => array(
					'fade'     => 'Fade',
					'slide'    => 'Slide',
					'grow'     => 'Grow',
					'drop-in'  => 'Drop In',
					'drop-out' => 'Drop Out',
					'none'     => 'None',
				),
				'condition' => array(
					'pointer' => array( 'underline', 'overline', 'double-line' ),
				),
			)
		);

		$this->add_control(
			'animation_framed',
			array(
				'label'     => __( 'Animation', 'trx_addons' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'fade',
				'options'   => array(
					'fade'    => 'Fade',
					'grow'    => 'Grow',
					'shrink'  => 'Shrink',
					'draw'    => 'Draw',
					'corners' => 'Corners',
					'none'    => 'None',
				),
				'condition' => array(
					'pointer' => 'framed',
				),
			)
		);

		$this->add_control(
			'animation_background',
			array(
				'label'     => __( 'Animation', 'trx_addons' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'fade',
				'options'   => array(
					'fade'                   => 'Fade',
					'grow'                   => 'Grow',
					'shrink'                 => 'Shrink',
					'sweep-left'             => 'Sweep Left',
					'sweep-right'            => 'Sweep Right',
					'sweep-up'               => 'Sweep Up',
					'sweep-down'             => 'Sweep Down',
					'shutter-in-vertical'    => 'Shutter In Vertical',
					'shutter-out-vertical'   => 'Shutter Out Vertical',
					'shutter-in-horizontal'  => 'Shutter In Horizontal',
					'shutter-out-horizontal' => 'Shutter Out Horizontal',
					'none'                   => 'None',
				),
				'condition' => array(
					'pointer' => 'background',
				),
			)
		);

		$this->add_control(
			'animation_text',
			array(
				'label'     => __( 'Animation', 'trx_addons' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'grow',
				'options'   => array(
					'grow'   => 'Grow',
					'shrink' => 'Shrink',
					'sink'   => 'Sink',
					'float'  => 'Float',
					'skew'   => 'Skew',
					'rotate' => 'Rotate',
					'none'   => 'None',
				),
				'condition' => array(
					'pointer' => 'text',
				),
			)
		);

		$this->add_control(
			'load_hidden',
			array(
				'label'       => __( 'Hide Menu Until Content Loaded', 'trx_addons' ),
				'type'        => Controls_Manager::SWITCHER,
				'description' => __( 'This option hides the menu by default until all the content inside it is loaded.', 'trx_addons' ),
				'default'     => 'yes',
				'condition'    => array(
					'nav_menu_layout' => array( 'dropdown', 'slide' ),
				),
			)
		);

		$this->add_control(
			'ver_toggle_switcher',
			array(
				'label'        => __( 'Enable Collapsed Menu', 'trx_addons' ),
				'type'         => Controls_Manager::SWITCHER,
				'separator'    => 'before',
				'render_type'  => 'template',
				'prefix_class' => 'trx-addons-ver-toggle-',
				'condition'    => array(
					'nav_menu_layout' => 'ver',
				),
			)
		);

		$this->add_control(
			'ver_toggle_txt',
			array(
				'label'     => __( 'Title', 'trx_addons' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'Show Menu', 'trx_addons' ),
				'condition' => array(
					'nav_menu_layout'     => 'ver',
					'ver_toggle_switcher' => 'yes',
				),
			)
		);

		$this->add_control(
			'ver_toggle_event',
			array(
				'label'        => __( 'Open On', 'trx_addons' ),
				'type'         => Controls_Manager::SELECT,
				'default'      => 'click',
				'render_type'  => 'template',
				'prefix_class' => 'trx-addons-ver-',
				'options'      => array(
					'hover'  => __( 'Hover', 'trx_addons' ),
					'click'  => __( 'Click', 'trx_addons' ),
					'always' => __( 'Always', 'trx_addons' ),
				),
				'condition'    => array(
					'nav_menu_layout'     => 'ver',
					'ver_toggle_switcher' => 'yes',
				),
			)
		);

		$this->add_control(
			'ver_toggle_open',
			array(
				'label'       => __( 'Opened By Default', 'trx_addons' ),
				'type'        => Controls_Manager::SWITCHER,
				'render_type' => 'template',
				'condition'   => array(
					'nav_menu_layout'     => 'ver',
					'ver_toggle_switcher' => 'yes',
					'ver_toggle_event'    => 'click',
				),
			)
		);

		$this->add_control(
			'ver_toggle_main_icon',
			array(
				'label'       => __( 'Title Icon', 'trx_addons' ),
				'label_block' => false,
				'type'        => Controls_Manager::ICONS,
				'skin'        => 'inline',
				'default'     => array(
					'value'   => 'fas fa-bars',
					'library' => 'fa-solid',
				),
				'condition'   => array(
					'nav_menu_layout'     => 'ver',
					'ver_toggle_switcher' => 'yes',
				),
			)
		);

		$this->add_control(
			'ver_toggle_toggle_icon',
			array(
				'label'       => __( 'Toggle Icon', 'trx_addons' ),
				'label_block' => false,
				'type'        => Controls_Manager::ICONS,
				'skin'        => 'inline',
				'default'     => array(
					'value'   => 'fas fa-angle-down',
					'library' => 'fa-solid',
				),
				'condition'   => array(
					'nav_menu_layout'     => 'ver',
					'ver_toggle_switcher' => 'yes',
				),
			)
		);

		$this->add_control(
			'ver_toggle_close_icon',
			array(
				'label'       => __( 'Close Icon', 'trx_addons' ),
				'label_block' => false,
				'type'        => Controls_Manager::ICONS,
				'skin'        => 'inline',
				'default'     => array(
					'value'   => 'fas fa-angle-up',
					'library' => 'fa-solid',
				),
				'condition'   => array(
					'nav_menu_layout'     => 'ver',
					'ver_toggle_switcher' => 'yes',
					'ver_toggle_event!'   => 'always',
				),
			)
		);

		$this->add_control(
			'ver_spacing',
			array(
				'label'       => __( 'Title Spacing', 'trx_addons' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'description' => __( 'Use this option to control the spacing between the title icon and the title.', 'trx_addons' ),
				'selectors'   => array(
					'{{WRAPPER}} .trx-addons-ver-toggler-txt' => 'text-indent: {{SIZE}}{{UNIT}};',
				),
				'condition'   => array(
					'nav_menu_layout'     => 'ver',
					'ver_toggle_switcher' => 'yes',
				),
			)
		);

		// $this->add_control(
		// 	'sticky_switcher',
		// 	array(
		// 		'label'              => __( 'Enable Sticky Menu', 'trx_addons' ),
		// 		'type'               => Controls_Manager::SWITCHER,
		// 		'frontend_available' => true,
		// 		'separator'          => 'before',
		// 		'render_type'        => 'template',
		// 		'prefix_class'       => 'trx-addons-nav-sticky-',
		// 		'condition'          => array(
		// 			'nav_menu_layout' => 'hor',
		// 		),
		// 	)
		// );

		// $this->add_control(
		// 	'sticky_target',
		// 	array(
		// 		'label'              => __( 'Sticky Target ID', 'trx_addons' ),
		// 		'type'               => Controls_Manager::TEXT,
		// 		'frontend_available' => true,
		// 		'render_type'        => 'template',
		// 		'placeholder'        => 'sticky-target',
		// 		'description'        => __( 'The target id to apply sticky effect on ( without the "#" ).', 'trx_addons' ),
		// 		'condition'          => array(
		// 			'nav_menu_layout' => 'hor',
		// 			'sticky_switcher' => 'yes',
		// 		),
		// 	)
		// );

		// $this->add_control(
		// 	'sticky_on_scroll',
		// 	array(
		// 		'label'              => __( 'Sticky on Scroll Up', 'trx_addons' ),
		// 		'type'               => Controls_Manager::SWITCHER,
		// 		'frontend_available' => true,
		// 		'render_type'        => 'template',
		// 		'condition'          => array(
		// 			'nav_menu_layout' => 'hor',
		// 			'sticky_switcher' => 'yes',
		// 		),
		// 	)
		// );

		// $this->add_control(
		// 	'sticky_disabled_on',
		// 	array(
		// 		'label'              => __( 'Disable On', 'trx_addons' ),
		// 		'type'               => Controls_Manager::SELECT2,
		// 		'frontend_available' => true,
		// 		'options'            => TrxAddonsUtils::get_all_breakpoints(),
		// 		'multiple'           => true,
		// 		'label_block'        => true,
		// 		'render_type'        => 'template',
		// 		'default'            => array( 'tablet', 'mobile' ),
		// 		'condition'          => array(
		// 			'nav_menu_layout' => 'hor',
		// 			'sticky_switcher' => 'yes',
		// 		),
		// 	)
		// );

		$this->add_control(
			'submenu_heading',
			array(
				'label'     => __( 'Submenu Settings', 'trx_addons' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'submenu_icon',
			array(
				'label'                  => __( 'Submenu Indicator Icon', 'trx_addons' ),
				'label_block'            => false,
				'type'                   => Controls_Manager::ICONS,
				'default'                => array(
					'value'   => 'fas fa-angle-down',
					'library' => 'fa-solid',
				),
				'recommended'            => array(
					'fa-solid' => array(
						'chevron-down',
						'angle-down',
						'caret-down',
						'plus',
					),
				),
				'default'                => array(
					'value'   => 'fas fa-angle-down',
					'library' => 'fa-solid',
				),
				'skin'                   => 'inline',
				// 'exclude_inline_options' => array( 'svg' ),
				'frontend_available'     => true,
			)
		);

		$this->add_control(
			'submenu_item_icon',
			array(
				'label'                  => __( 'Submenu Item Icon', 'trx_addons' ),
				'label_block'            => false,
				'type'                   => Controls_Manager::ICONS,
				'recommended'            => array(
					'fa-solid' => array(
						'chevron-right',
						'angle-right',
						'caret-right',
					),
				),
				'default'                => array(
					'value'   => 'fas fa-angle-right',
					'library' => 'fa-solid',
				),
				'skin'                   => 'inline',
				// 'exclude_inline_options' => array( 'svg' ),
				'frontend_available'     => true,
				'condition'              => array(
					'menu_type' => 'wordpress_menu',
				),
			)
		);

		$default_pos   = is_rtl() ? 'left' : 'right';
		$default_align = is_rtl() ? 'flex-end' : 'flex-start';

		$this->add_responsive_control(
			'nav_ver_submenu',
			array(
				'label'        => __( 'Submenu Position', 'trx_addons' ),
				'type'         => Controls_Manager::CHOOSE,
				'render_type'  => 'template',
				'prefix_class' => 'trx-addons-vertical-',
				'options'      => array(
					'left'  => array(
						'title' => __( 'Left', 'trx_addons' ),
						'icon'  => 'eicon-h-align-left',
					),
					'right' => array(
						'title' => __( 'Right', 'trx_addons' ),
						'icon'  => 'eicon-h-align-right',
					),
				),
				'default'      => $default_pos,
				'toggle'       => false,
				'condition'    => array(
					'nav_menu_layout' => 'ver',
				),
			)
		);

		$this->add_responsive_control(
			'submenu_align',
			array(
				'label'     => __( 'Content Alignment', 'trx_addons' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'flex-start' => array(
						'title' => __( 'Left', 'trx_addons' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center'     => array(
						'title' => __( 'Center', 'trx_addons' ),
						'icon'  => 'eicon-text-align-center',
					),
					'flex-end'   => array(
						'title' => __( 'Right', 'trx_addons' ),
						'icon'  => 'eicon-text-align-right',
					),
				),
				'default'   => $default_align,
				'toggle'    => false,
				'selectors' => array(
					'{{WRAPPER}} .trx-addons-submenu .trx-addons-submenu-link' => 'justify-content: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'submenu_event',
			array(
				'label'       => __( 'Open Submenu On', 'trx_addons' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'hover',
				'render_type' => 'template',
				'options'     => array(
					'hover' => __( 'Hover', 'trx_addons' ),
					'click' => __( 'Click', 'trx_addons' ),
				),
				'condition'   => array(
					'nav_menu_layout' => array( 'hor', 'ver' ),
				),
			)
		);

		$this->add_control(
			'submenu_trigger',
			array(
				'label'       => __( 'Submenu Trigger', 'trx_addons' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'item',
				'render_type' => 'template',
				'options'     => array(
					'icon' => __( 'Submenu Dropdwon Icon', 'trx_addons' ),
					'item' => __( 'Submenu Item', 'trx_addons' ),
				),
				'condition'   => array(
					'nav_menu_layout' => array( 'hor', 'ver' ),
					'submenu_event'      => 'click',
				),
			)
		);

		$this->add_control(
			'submenu_slide',
			array(
				'label'        => __( 'Submenu Animation', 'trx_addons' ),
				'type'         => Controls_Manager::SELECT,
				'prefix_class' => 'trx-addons-nav-',
				'default'      => 'none',
				'options'      => array(
					'none'        => __( 'None', 'trx_addons' ),
					'slide-up'    => __( 'Slide Up', 'trx_addons' ),
					'slide-down'  => __( 'Slide Down', 'trx_addons' ),
					'slide-left'  => __( 'Slide Left', 'trx_addons' ),
					'slide-right' => __( 'Slide Right', 'trx_addons' ),
				),
				'condition'    => array(
					'nav_menu_layout' => array( 'hor', 'ver' ),
				),
			)
		);

		// sub-items badge hover.
		$this->add_control(
			'sub_badge_hv_effects',
			array(
				'label'       => __( 'Badge Effects', 'trx_addons' ),
				'type'        => Controls_Manager::SELECT,
				'render_type' => 'template',
				'default'     => '',
				'options'     => array(
					''            => __( 'None', 'trx_addons' ),
					'dot'         => __( 'Grow', 'trx_addons' ),
					'expand'      => __( 'Expand', 'trx_addons' ),
					'pulse'       => __( 'Pulse', 'trx_addons' ),
					'buzz'        => __( 'Buzz', 'trx_addons' ),
					'slide-right' => __( 'Slide Right', 'trx_addons' ),
					'slide-left'  => __( 'Slide Left', 'trx_addons' ),
				),
			)
		);

		$this->add_responsive_control(
			'dot_size',
			array(
				'label'       => __( 'Dot Size', 'trx_addons' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'range'       => array(
					'px' => array(
						'min' => 0,
						'max' => 10,
					),
				),
				'selectors'   => array(
					'{{WRAPPER}} .trx-addons-badge-dot .trx-addons-sub-item-badge' => 'padding: {{SIZE}}{{UNIT}};',
				),
				'condition'   => array(
					'sub_badge_hv_effects' => 'dot',
				),
			)
		);

		// toggle menu settings.
		$this->add_control(
			'toggle_heading',
			array(
				'label'     => __( 'Mobile Menu Settings', 'trx_addons' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array(
					'nav_menu_layout' => array( 'hor', 'ver' ),
				),
			)
		);

		$this->add_control(
			'mobile_menu_layout',
			array(
				'label'        => __( 'Layout', 'trx_addons' ),
				'type'         => Controls_Manager::SELECT,
				'render_type'  => 'template',
				'prefix_class' => 'trx-addons-ham-',
				'options'      => array(
					'dropdown' => 'Expand',
					'slide'    => 'Slide',
				),
				'default'      => 'dropdown',
				'condition'    => array(
					'nav_menu_layout' => array( 'hor', 'ver' ),
				),
			)
		);

		$bp_list = array_merge( trx_addons_elm_get_breakpoints(), array( 'custom' => __( 'Custom', 'trx_addons' ) ) );
		unset( $bp_list['widescreen'] );
		unset( $bp_list['desktop'] );
		$breakpoints = array();
		foreach ( $bp_list as $bp => $width ) {
			$breakpoints[ $bp ] = ucfirst( str_replace( '_', ' ', $bp ) ) . ( $bp !== 'custom' ? ' (' . $width . ')' : '' );
		}

		$this->add_control(
			'mobile_menu_breakpoint',
			array(
				'label'     => __( 'Breakpoint', 'trx_addons' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => $breakpoints,
				'default'   => 'tablet',
				'condition' => array(
					'nav_menu_layout' => array( 'hor', 'ver' ),
				),
			)
		);

		$this->add_control(
			'custom_breakpoint',
			array(
				'label'       => __( 'Custom Breakpoint (px)', 'trx_addons' ),
				'type'        => Controls_Manager::NUMBER,
				'min'         => 0,
				'max'         => 2000,
				'step'        => 5,
				'description' => 'Use this option to control when to turn your menu into a toggle menu, Default is 1025',
				'condition'   => array(
					'nav_menu_layout'        => array( 'hor', 'ver' ),
					'mobile_menu_breakpoint' => 'custom',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Get dropdown content settings.
	 */
	private function register_content_dropdown_controls() {

		$align_left  = is_rtl() ? 'flex-end' : 'flex-start';
		$align_right = is_rtl() ? 'flex-start' : 'flex-end';

		$this->start_controls_section(
			'dropdown_section',
			array(
				'label' => __( 'Expand/Slide Menu Settings', 'trx_addons' ),
			)
		);

		$this->add_control(
			'btn_toggle_heading',
			array(
				'label' => __( 'Toggle Button Settings', 'trx_addons' ),
				'type'  => Controls_Manager::HEADING,
			)
		);

		$this->add_control(
			'mobile_toggle_text',
			array(
				'label'   => __( 'Text', 'trx_addons' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Menu', 'trx_addons' ),
			)
		);

		$this->add_control(
			'mobile_toggle_close',
			array(
				'label'   => __( 'Close Text', 'trx_addons' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Close', 'trx_addons' ),
			)
		);

		$this->add_control(
			'mobile_toggle_icon',
			array(
				'label'       => __( 'Icon', 'trx_addons' ),
				'label_block' => false,
				'type'        => Controls_Manager::ICONS,
				'skin'        => 'inline',
				'default'     => array(
					'value'   => 'fas fa-bars',
					'library' => 'fa-solid',
				),
			)
		);

		$this->add_control(
			'mobile_close_icon',
			array(
				'label'       => __( 'Close Icon', 'trx_addons' ),
				'label_block' => false,
				'type'        => Controls_Manager::ICONS,
				'skin'        => 'inline',
				'default'     => array(
					'value'   => 'fas fa-times',
					'library' => 'fa-solid',
				),
			)
		);

		$this->add_responsive_control(
			'mobile_toggle_pos',
			array(
				'label'     => __( 'Toggle Button Position', 'trx_addons' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					$align_left  => array(
						'title' => __( 'Left', 'trx_addons' ),
						'icon'  => 'eicon-h-align-left',
					),
					'center'     => array(
						'title' => __( 'Center', 'trx_addons' ),
						'icon'  => 'eicon-h-align-center',
					),
					$align_right => array(
						'title' => __( 'Right', 'trx_addons' ),
						'icon'  => 'eicon-h-align-right',
					),
				),
				'default'   => 'center',
				'toggle'    => false,
				'selectors' => array(
					'{{WRAPPER}} .trx-addons-hamburger-toggle' => 'justify-content: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'mobile_menu_heading',
			array(
				'label'      => __( 'Menu Settings', 'trx_addons' ),
				'type'       => Controls_Manager::HEADING,
				'separator'  => 'before',
			)
		);

		$this->add_control(
			'mobile_menu_pos',
			array(
				'label'      => __( 'Menu Position', 'trx_addons' ),
				'type'       => Controls_Manager::CHOOSE,
				'options'    => array(
					$align_left  => array(
						'title' => __( 'Left', 'trx_addons' ),
						'icon'  => 'eicon-h-align-left',
					),
					'center'     => array(
						'title' => __( 'Center', 'trx_addons' ),
						'icon'  => 'eicon-h-align-center',
					),
					$align_right => array(
						'title' => __( 'Right', 'trx_addons' ),
						'icon'  => 'eicon-h-align-right',
					),
				),
				'default'    => 'center',
				'toggle'     => false,
				'selectors'  => array(
					'{{WRAPPER}}.trx-addons-ham-dropdown .trx-addons-mobile-menu-container,
					 {{WRAPPER}}.trx-addons-nav-dropdown .trx-addons-mobile-menu-container' => 'justify-content: {{VALUE}}',
				),
				'conditions' => array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'name'  => 'nav_menu_layout',
							'value' => 'dropdown',
						),
						array(
							'relation' => 'and',
							'terms'    => array(
								array(
									'name'  => 'mobile_menu_layout',
									'value' => 'dropdown',
								),
								array(
									'name'     => 'nav_menu_layout',
									'operator' => 'in',
									'value'    => array( 'hor', 'ver' ),
								),
							),
						),
					),
				),
			)
		);

		$this->add_control(
			'slide_menu_effect',
			array(
				'label'      => __( 'Slide Menu Effect', 'trx_addons' ),
				'type'       => Controls_Manager::SELECT,
				'options'    => array(
					'slide' => __( 'Slide', 'trx_addons' ),
					'fade'  => __( 'Fade', 'trx_addons' ),
				),
				'default'    => 'slide',
				'prefix_class' => 'trx-addons-slide-menu-effect-',
				'conditions' => array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'name'  => 'nav_menu_layout',
							'value' => 'slide',
						),
						array(
							'relation' => 'and',
							'terms'    => array(
								array(
									'name'  => 'mobile_menu_layout',
									'value' => 'slide',
								),
								array(
									'name'     => 'nav_menu_layout',
									'operator' => 'in',
									'value'    => array( 'hor', 'ver' ),
								),
							),
						),
					),
				),
			)
		);

		$this->add_control(
			'slide_menu_pos',
			array(
				'label'      => __( 'Slide Menu Position', 'trx_addons' ),
				'type'       => Controls_Manager::CHOOSE,
				'options'    => array(
					'left'  => array(
						'title' => __( 'Left', 'trx_addons' ),
						'icon'  => 'eicon-h-align-left',
					),
					'right' => array(
						'title' => __( 'Right', 'trx_addons' ),
						'icon'  => 'eicon-h-align-right',
					),
				),
				'default'    => 'left',
				'toggle'     => false,
				'prefix_class' => 'trx-addons-ver-hamburger-menu-',
				'conditions' => array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'name'  => 'nav_menu_layout',
							'value' => 'slide',
						),
						array(
							'relation' => 'and',
							'terms'    => array(
								array(
									'name'  => 'mobile_menu_layout',
									'value' => 'slide',
								),
								array(
									'name'     => 'nav_menu_layout',
									'operator' => 'in',
									'value'    => array( 'hor', 'ver' ),
								),
							),
						),
					),
				),
			)
		);

		$this->add_control(
			'mobile_menu_align',
			array(
				'label'     => __( 'Menu Alignment', 'trx_addons' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					$align_left  => array(
						'title' => __( 'Left', 'trx_addons' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center'     => array(
						'title' => __( 'Center', 'trx_addons' ),
						'icon'  => 'eicon-text-align-center',
					),
					$align_right => array(
						'title' => __( 'Right', 'trx_addons' ),
						'icon'  => 'eicon-text-align-right',
					),
				),
				'default'   => $align_left,
				'prefix_class' => 'trx-addons-mobile-menu-align-',
				'toggle'    => false,
				'selectors' => array(
					'{{WRAPPER}}.trx-addons-hamburger-menu .trx-addons-main-mobile-menu > .trx-addons-nav-menu-item > .trx-addons-menu-link,
					 {{WRAPPER}}.trx-addons-nav-dropdown .trx-addons-main-mobile-menu > .trx-addons-nav-menu-item > .trx-addons-menu-link,
					 {{WRAPPER}}.trx-addons-nav-slide .trx-addons-main-mobile-menu > .trx-addons-nav-menu-item > .trx-addons-menu-link' => 'justify-content: {{VALUE}}',
					'{{WRAPPER}}.trx-addons-hamburger-menu .trx-addons-main-mobile-menu .trx-addons-submenu .trx-addons-submenu-link,
					 {{WRAPPER}}.trx-addons-nav-dropdown .trx-addons-main-mobile-menu .trx-addons-submenu .trx-addons-submenu-link,
					 {{WRAPPER}}.trx-addons-nav-slide .trx-addons-main-mobile-menu .trx-addons-submenu .trx-addons-submenu-link' => 'justify-content: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'slide_menu_valign',
			array(
				'label'      => __( 'Menu Vertical Alignment', 'trx_addons' ),
				'type'       => Controls_Manager::CHOOSE,
				'options'    => array(
					'flex-start'  => array(
						'title' => __( 'Top', 'trx_addons' ),
						'icon'  => 'eicon-v-align-top',
					),
					'center' => array(
						'title' => __( 'Center', 'trx_addons' ),
						'icon'  => 'eicon-v-align-middle',
					),
					'flex-end' => array(
						'title' => __( 'Bottom', 'trx_addons' ),
						'icon'  => 'eicon-v-align-bottom',
					),
				),
				'default'    => 'flex-start',
				'toggle'     => false,
				'conditions' => array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'name'  => 'nav_menu_layout',
							'value' => 'slide',
						),
						array(
							'relation' => 'and',
							'terms'    => array(
								array(
									'name'  => 'mobile_menu_layout',
									'value' => 'slide',
								),
								array(
									'name'     => 'nav_menu_layout',
									'operator' => 'in',
									'value'    => array( 'hor', 'ver' ),
								),
							),
						),
					),
				),
				'selectors' => array(
					'{{WRAPPER}}.trx-addons-ver-hamburger-menu .trx-addons-mobile-menu-outer-container' => 'justify-content: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'slide_menu_margin',
			array(
				'label'      => __( 'Menu Margin', 'trx_addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'conditions' => array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'name'  => 'nav_menu_layout',
							'value' => 'slide',
						),
						array(
							'relation' => 'and',
							'terms'    => array(
								array(
									'name'  => 'mobile_menu_layout',
									'value' => 'slide',
								),
								array(
									'name'     => 'nav_menu_layout',
									'operator' => 'in',
									'value'    => array( 'hor', 'ver' ),
								),
							),
						),
					),
				),
				'default'   => array(
					'top'    => '50',
					'right'  => '0',
					'bottom' => '0',
					'left'   => '0',
					'unit'   => 'px',
					'isLinked' => false,
				),
				'selectors' => array(
					'{{WRAPPER}}.trx-addons-ver-hamburger-menu .trx-addons-mobile-menu-outer-container .trx-addons-mobile-menu-container' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'ham_menu_width',
			array(
				'label'       => __( 'Toggle Menu Width', 'trx_addons' ),
				'type'        => Controls_Manager::SLIDER,
				'separator'   => 'before',
				'size_units'  => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'range'       => array(
					'px' => array(
						'min' => 0,
						'max' => 1000,
					),
				),
				'selectors'   => array(
					'{{WRAPPER}}.trx-addons-ham-dropdown .trx-addons-main-mobile-menu,
					 {{WRAPPER}}.trx-addons-nav-dropdown .trx-addons-main-mobile-menu' => 'width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.trx-addons-ham-slide .trx-addons-mobile-menu-outer-container,
					 {{WRAPPER}}.trx-addons-nav-slide .trx-addons-mobile-menu-outer-container' => 'width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.trx-addons-ham-slide.trx-addons-ver-hamburger-menu-left .trx-addons-mobile-menu-outer-container,
					 {{WRAPPER}}.trx-addons-nav-slide.trx-addons-ver-hamburger-menu-left .trx-addons-mobile-menu-outer-container' => 'transform:translateX( -{{SIZE}}{{UNIT}} );',
					'{{WRAPPER}}.trx-addons-ham-slide.trx-addons-ver-hamburger-menu-right .trx-addons-mobile-menu-outer-container,
					 {{WRAPPER}}.trx-addons-nav-slide.trx-addons-ver-hamburger-menu-right .trx-addons-mobile-menu-outer-container' => 'transform:translateX( {{SIZE}}{{UNIT}} );',
				),
				'condition'   => array(
					'toggle_full!' => 'yes',
				),
			)
		);

		$this->add_control(
			'toggle_full',
			array(
				'label'       => __( 'Full Width', 'trx_addons' ),
				'type'        => Controls_Manager::SWITCHER,
				'render_type' => 'template',
				'conditions'  => array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'name'  => 'nav_menu_layout',
							'value' => 'dropdown',
						),
						array(
							'relation' => 'and',
							'terms'    => array(
								array(
									'name'  => 'mobile_menu_layout',
									'value' => 'dropdown',
								),
								array(
									'name'     => 'nav_menu_layout',
									'operator' => 'in',
									'value'    => array( 'hor', 'ver' ),
								),
							),
						),
					),
				),
			)
		);

		$this->add_control(
			'mobile_hide_icon',
			array(
				'label'        => __( 'Hide Items Icon', 'trx_addons' ),
				'type'         => Controls_Manager::SWITCHER,
				'prefix_class' => 'trx-addons-hidden-icon-',
			)
		);

		$this->add_control(
			'mobile_hide_badge',
			array(
				'label'        => __( 'Hide Items Badge', 'trx_addons' ),
				'type'         => Controls_Manager::SWITCHER,
				'prefix_class' => 'trx-addons-hidden-badge-',
			)
		);

		$this->add_control(
			'close_after_click',
			array(
				'label'       => __( 'Close Menu After Click', 'trx_addons' ),
				'type'        => Controls_Manager::SWITCHER,
				'render_type' => 'template',
			)
		);

		$this->add_control(
			'disable_page_scroll',
			array(
				'label'        => __( 'Disable Page Scroll', 'trx_addons' ),
				'type'         => Controls_Manager::SWITCHER,
				'prefix_class' => 'trx-addons-disable-scroll-',
				'description'  => __( 'Enable this option to disable page scroll when the slide menu is opened', 'trx_addons' ),
				'render_type'  => 'template',
				'conditions'   => array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'name'     => 'nav_menu_layout',
							'operator' => '===',
							'value'    => 'slide',
						),
						array(
							'name'     => 'mobile_menu_layout',
							'operator' => '===',
							'value'    => 'slide',
						),
					),
				),
			)
		);

		$this->end_controls_section();
	}

	/*-----------------------------------------------------------------------------------*/
	/*	STYLE TAB
	/*-----------------------------------------------------------------------------------*/

	/**
	 * Register sticky style options.
	 */
	private function register_style_sticky_controls() {

		$this->start_controls_section(
			'sticky_style_sec',
			array(
				'label'     => __( 'Sticky Menu Style', 'trx_addons' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'sticky_switcher' => 'yes',
					'nav_menu_layout' => 'hor',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'           => 'sticky_shadow',
				'label'          => __( 'Shadow', 'trx_addons' ),
				'fields_options' => array(
					'box_shadow' => array(
						'selectors' => array(
							'.trx-addons-sticky-parent-{{ID}}' => 'box-shadow: {{HORIZONTAL}}px {{VERTICAL}}px {{BLUR}}px {{SPREAD}}px {{COLOR}} {{box_shadow_position.VALUE}} !important;',
						),
					),
				),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'sticky_bg',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '.trx-addons-sticky-parent-{{ID}}',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'sticky_border',
				'selector' => '.trx-addons-sticky-parent-{{ID}}',
			)
		);

		$this->add_responsive_control(
			'sticky_padding',
			array(
				'label'      => __( 'Padding', 'trx_addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors'  => array(
					'.trx-addons-sticky-parent-{{ID}}' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Get Vertical toggler style.
	 */
	private function register_style_vertical_toggler_controls() {

		$this->start_controls_section(
			'ver_toggler_style_section',
			array(
				'label'     => __( 'Collapsed Menu Style', 'trx_addons' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'ver_toggle_switcher' => 'yes',
					'nav_menu_layout'     => 'ver',
				),
			)
		);

		$this->add_control(
			'ver_title_heading',
			array(
				'label' => __( 'Title', 'trx_addons' ),
				'type'  => Controls_Manager::HEADING,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'ver_title_typo',
				'selector' => '{{WRAPPER}} .trx-addons-ver-toggler-txt',
			)
		);

		$this->add_control(
			'ver_title_icon_size',
			array(
				'label'      => __( 'Icon Size', 'trx_addons' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors'  => array(
					'{{WRAPPER}} .trx-addons-ver-title-icon i' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .trx-addons-ver-title-icon svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}',
				),
				'condition'  => array(
					'ver_toggle_main_icon[value]!' => '',
				),
			)
		);

		$this->start_controls_tabs( 'ver_title_tabs' );

		$this->start_controls_tab(
			'ver_title_tab_normal',
			array(
				'label' => __( 'Normal', 'trx_addons' ),
			)
		);

		$this->add_control(
			'ver_title_color',
			array(
				'label'     => __( 'Color', 'trx_addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .trx-addons-ver-toggler-txt' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'ver_title_icon_color',
			array(
				'label'     => __( 'Icon Color', 'trx_addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .trx-addons-ver-title-icon i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .trx-addons-ver-title-icon svg path' => 'fill: {{VALUE}};',
				),
				'condition' => array(
					'ver_toggle_main_icon[value]!' => '',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'ver_title_tab_hov',
			array(
				'label' => __( 'Hover', 'trx_addons' ),
			)
		);

		$this->add_control(
			'ver_title_color_hov',
			array(
				'label'     => __( 'Color', 'trx_addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .trx-addons-ver-toggler:hover .trx-addons-ver-toggler-txt' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'ver_title_icon_color_hov',
			array(
				'label'     => __( 'Icon Color', 'trx_addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .trx-addons-ver-toggler:hover .trx-addons-ver-title-icon i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .trx-addons-ver-toggler:hover .trx-addons-ver-title-icon svg path' => 'fill: {{VALUE}};',
				),
				'condition' => array(
					'ver_toggle_main_icon[value]!' => '',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'ver_toggle_heading',
			array(
				'label'      => __( 'Toggle Icon', 'trx_addons' ),
				'type'       => Controls_Manager::HEADING,
				'separator'  => 'before',
				'conditions' => array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'name'     => 'ver_toggle_toggle_icon[value]',
							'operator' => '!==',
							'value'    => '',
						),
						array(
							'name'     => 'ver_toggle_close_icon[value]',
							'operator' => '!==',
							'value'    => '',
						),
					),
				),
			)
		);

		$this->add_control(
			'ver_toggle_icon_size',
			array(
				'label'      => __( 'Size', 'trx_addons' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors'  => array(
					'{{WRAPPER}} .trx-addons-ver-toggler-btn i' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .trx-addons-ver-toggler-btn svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}',
				),
				'conditions' => array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'name'     => 'ver_toggle_toggle_icon[value]',
							'operator' => '!==',
							'value'    => '',
						),
						array(
							'name'     => 'ver_toggle_close_icon[value]',
							'operator' => '!==',
							'value'    => '',
						),
					),
				),
			)
		);

		$this->start_controls_tabs( 'ver_toggle_icon_tabs' );

		$this->start_controls_tab(
			'ver_toggle_tab_normal',
			array(
				'label'      => __( 'Normal', 'trx_addons' ),
				'conditions' => array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'name'     => 'ver_toggle_toggle_icon[value]',
							'operator' => '!==',
							'value'    => '',
						),
						array(
							'name'     => 'ver_toggle_close_icon[value]',
							'operator' => '!==',
							'value'    => '',
						),
					),
				),
			)
		);

		$this->add_control(
			'ver_toggle_icon_color',
			array(
				'label'     => __( 'Color', 'trx_addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .trx-addons-ver-toggler-btn i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .trx-addons-ver-toggler-btn svg path' => 'fill: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'ver_toggle_tab_hov',
			array(
				'label'      => __( 'Hover', 'trx_addons' ),
				'conditions' => array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'name'     => 'ver_toggle_toggle_icon[value]',
							'operator' => '!==',
							'value'    => '',
						),
						array(
							'name'     => 'ver_toggle_close_icon[value]',
							'operator' => '!==',
							'value'    => '',
						),
					),
				),
			)
		);

		$this->add_control(
			'ver_toggle_icon_color_hov',
			array(
				'label'     => __( 'Color', 'trx_addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .trx-addons-ver-toggler:hover .trx-addons-ver-toggler-btn i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .trx-addons-ver-toggler:hover .trx-addons-ver-toggler-btn svg path' => 'fill: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'ver_container_heading',
			array(
				'label'     => __( 'Container', 'trx_addons' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->start_controls_tabs( 'ver_toggler_tabs' );

		$this->start_controls_tab(
			'ver_toggler_tab_normal',
			array(
				'label' => __( 'Normal', 'trx_addons' ),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'ver_toggler_bg',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .trx-addons-ver-toggler',
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'ver_toggler_shadow',
				'selector' => '{{WRAPPER}} .trx-addons-ver-toggler',
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'ver_toggler_tab_hov',
			array(
				'label' => __( 'Hover', 'trx_addons' ),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'ver_toggler_bg_hov',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .trx-addons-ver-toggler:hover',
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'ver_toggler_shadow_hov',
				'selector' => '{{WRAPPER}} .trx-addons-ver-toggler:hover',
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'      => 'ver_toggler_border',
				'selector'  => '{{WRAPPER}} .trx-addons-ver-toggler',
				'separator' => 'before',
			)
		);

		$this->add_control(
			'ver_toggler_rad',
			array(
				'label'      => __( 'Border Radius', 'trx_addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors'  => array(
					'{{WRAPPER}} .trx-addons-ver-toggler' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
			)
		);

		$this->add_responsive_control(
			'ver_toggler_padding',
			array(
				'label'      => __( 'Padding', 'trx_addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors'  => array(
					'{{WRAPPER}} .trx-addons-ver-toggler' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Get menu container style.
	 */
	private function register_style_menu_container_controls() {

		$this->start_controls_section(
			'nav_style_section',
			array(
				'label'     => __( 'Desktop Menu Style', 'trx_addons' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'nav_menu_layout' => array( 'hor', 'ver' ),
				),
			)
		);

		$this->add_responsive_control(
			'nav_menu_height',
			array(
				'label'       => __( 'Height', 'trx_addons' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors'   => array(
					'{{WRAPPER}}.trx-addons-nav-hor > .elementor-widget-container > .trx-addons-nav-widget-container > .trx-addons-ver-inner-container > .trx-addons-nav-menu-container' => 'height: {{SIZE}}{{UNIT}};',
				),
				'condition'   => array(
					'nav_menu_layout' => 'hor',
				),
			)
		);

		$this->add_responsive_control(
			'nav_menu_width',
			array(
				'label'       => __( 'Width', 'trx_addons' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'range'       => array(
					'px' => array(
						'min' => 0,
						'max' => 1000,
					),
				),
				'selectors'   => array(
					'{{WRAPPER}}.trx-addons-nav-ver .trx-addons-ver-inner-container' => 'width: {{SIZE}}{{UNIT}};',
				),
				'condition'   => array(
					'nav_menu_layout' => 'ver',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'nav_menu_shadow',
				'selector' => '{{WRAPPER}} .trx-addons-nav-menu-container',
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'nav_menu_background',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .trx-addons-nav-menu-container',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'nav_menu_border',
				'selector' => '{{WRAPPER}} .trx-addons-nav-menu-container',
			)
		);

		$this->add_control(
			'nav_menu_rad',
			array(
				'label'      => __( 'Border Radius', 'trx_addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors'  => array(
					'{{WRAPPER}} .trx-addons-nav-menu-container' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
			)
		);

		$this->add_responsive_control(
			'nav_menu_padding',
			array(
				'label'      => __( 'Padding', 'trx_addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors'  => array(
					'{{WRAPPER}} .trx-addons-nav-menu-container' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Get menu item style.
	 */
	private function register_style_menu_item_controls() {

		$this->start_controls_section(
			'nav_item_style_section',
			array(
				'label' => __( 'Menu Item Style', 'trx_addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'nav_item_typo',
				'global'   => array(
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				),
				'selector' => '{{WRAPPER}} .trx-addons-main-nav-menu > .trx-addons-nav-menu-item > .trx-addons-menu-link',
			)
		);

		$this->add_responsive_control(
			'pointer_thinkness',
			array(
				'label'      => __( 'Pointer Thickness', 'trx_addons' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 50,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .trx-addons-nav-pointer-underline .trx-addons-menu-link-parent::after,
					{{WRAPPER}} .trx-addons-nav-pointer-overline .trx-addons-menu-link-parent::before,
					{{WRAPPER}} .trx-addons-nav-pointer-double-line .trx-addons-menu-link-parent::before,
					{{WRAPPER}} .trx-addons-nav-pointer-double-line .trx-addons-menu-link-parent::after' => 'height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .trx-addons-nav-pointer-framed:not(.trx-addons-nav-animation-draw):not(.trx-addons-nav-animation-corners) .trx-addons-menu-link-parent::before' => 'border-width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .trx-addons-nav-pointer-framed.trx-addons-nav-animation-draw .trx-addons-menu-link-parent::before' => 'border-width: 0 0 {{SIZE}}{{UNIT}} {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .trx-addons-nav-pointer-framed.trx-addons-nav-animation-draw .trx-addons-menu-link-parent::after' => 'border-width: {{SIZE}}{{UNIT}} {{SIZE}}{{UNIT}} 0 0;',
					'{{WRAPPER}} .trx-addons-nav-pointer-framed.trx-addons-nav-animation-corners .trx-addons-menu-link-parent::before' => 'border-width: {{SIZE}}{{UNIT}} 0 0 {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .trx-addons-nav-pointer-framed.trx-addons-nav-animation-corners .trx-addons-menu-link-parent::after' => 'border-width: 0 {{SIZE}}{{UNIT}} {{SIZE}}{{UNIT}} 0',
				),
				'condition'  => array(
					'pointer!' => array( 'none', 'text', 'background' ),
				),

			)
		);

		$this->add_responsive_control(
			'nav_item_drop_icon_size',
			array(
				'label'      => __( 'Dropdown Icon Size', 'trx_addons' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors'  => array(
					'{{WRAPPER}} .trx-addons-main-nav-menu > .trx-addons-nav-menu-item > .trx-addons-menu-link .trx-addons-dropdown-icon' => 'font-size: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'nav_item_drop_icon_margin',
			array(
				'label'      => __( 'Dropdown Icon Margin', 'trx_addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors'  => array(
					'{{WRAPPER}} .trx-addons-main-nav-menu > .trx-addons-nav-menu-item > .trx-addons-menu-link .trx-addons-dropdown-icon' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->start_controls_tabs( 'nav_items_styles' );

		$this->start_controls_tab(
			'nav_item_normal',
			array(
				'label' => __( 'Normal', 'trx_addons' ),
			)
		);

		$this->add_control(
			'nav_item_color',
			array(
				'label'     => __( 'Color', 'trx_addons' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_TEXT,
				),
				'selectors' => array(
					'{{WRAPPER}} .trx-addons-main-nav-menu > .trx-addons-nav-menu-item > .trx-addons-menu-link' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'nav_item_drop_icon_color',
			array(
				'label'     => __( 'Dropdown Icon Color', 'trx_addons' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_TEXT,
				),
				'selectors' => array(
					'{{WRAPPER}} .trx-addons-main-nav-menu > .trx-addons-nav-menu-item > .trx-addons-menu-link .trx-addons-dropdown-icon' => 'color: {{VALUE}};',
					'{{WRAPPER}} .trx-addons-main-nav-menu > .trx-addons-nav-menu-item > .trx-addons-menu-link .trx-addons-dropdown-icon svg path' => 'fill: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'nav_item_bg',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .trx-addons-main-nav-menu > .trx-addons-nav-menu-item > .trx-addons-menu-link',
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name'     => 'nav_item_shadow',
				'selector' => '{{WRAPPER}} .trx-addons-main-nav-menu > .trx-addons-nav-menu-item > .trx-addons-menu-link',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'nav_item_border',
				'selector' => '{{WRAPPER}} .trx-addons-main-nav-menu > .trx-addons-nav-menu-item > .trx-addons-menu-link',
			)
		);

		$this->add_control(
			'nav_item_rad',
			array(
				'label'      => __( 'Border Radius', 'trx_addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors'  => array(
					'{{WRAPPER}} .trx-addons-main-nav-menu > .trx-addons-nav-menu-item > .trx-addons-menu-link' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
			)
		);

		$this->add_responsive_control(
			'nav_item_padding',
			array(
				'label'      => __( 'Padding', 'trx_addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors'  => array(
					'{{WRAPPER}} .trx-addons-main-nav-menu > .trx-addons-nav-menu-item > .trx-addons-menu-link' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'nav_item_margin',
			array(
				'label'      => __( 'Margin', 'trx_addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors'  => array(
					'{{WRAPPER}} .trx-addons-main-nav-menu > .trx-addons-nav-menu-item' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'nav_item_hover',
			array(
				'label' => __( 'Hover', 'trx_addons' ),
			)
		);

		$this->add_control(
			'nav_item_color_hover',
			array(
				'label'     => __( 'Color', 'trx_addons' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .trx-addons-main-nav-menu > .trx-addons-nav-menu-item:hover > .trx-addons-menu-link' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'nav_item_drop_icon_hover',
			array(
				'label'     => __( 'Dropdown Icon Color', 'trx_addons' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .trx-addons-main-nav-menu > .trx-addons-nav-menu-item:hover > .trx-addons-menu-link .trx-addons-dropdown-icon' => 'color: {{VALUE}};',
					'{{WRAPPER}} .trx-addons-main-nav-menu > .trx-addons-nav-menu-item:hover > .trx-addons-menu-link .trx-addons-dropdown-icon svg path' => 'fill: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'nav_item_bg_hover',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .trx-addons-main-nav-menu > .trx-addons-nav-menu-item:hover > .trx-addons-menu-link',
			)
		);

		$this->add_control(
			'menu_item_pointer_color_hover',
			array(
				'label'     => __( 'Item Hover Effect Color', 'trx_addons' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .trx-addons-nav-widget-container:not(.trx-addons-nav-pointer-framed) .trx-addons-menu-link-parent:before,
					 {{WRAPPER}} .trx-addons-nav-widget-container:not(.trx-addons-nav-pointer-framed) .trx-addons-menu-link-parent:after' => 'background-color: {{VALUE}}',
					'{{WRAPPER}} .trx-addons-nav-pointer-framed .trx-addons-menu-link-parent:before,
					 {{WRAPPER}} .trx-addons-nav-pointer-framed .trx-addons-menu-link-parent:after' => 'border-color: {{VALUE}}',
				),
				'condition' => array(
					'pointer!' => array( 'none', 'text' ),
				),
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name'     => 'nav_item_shadow_hover',
				'selector' => '{{WRAPPER}} .trx-addons-main-nav-menu > .trx-addons-nav-menu-item:hover > .trx-addons-menu-link',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'nav_item_border_hover',
				'selector' => '{{WRAPPER}} .trx-addons-main-nav-menu > .trx-addons-nav-menu-item:hover > .trx-addons-menu-link',
			)
		);

		$this->add_control(
			'nav_item_rad_hover',
			array(
				'label'      => __( 'Border Radius', 'trx_addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors'  => array(
					'{{WRAPPER}} .trx-addons-main-nav-menu > .trx-addons-nav-menu-item:hover > .trx-addons-menu-link' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
			)
		);

		$this->add_responsive_control(
			'nav_item_padding_hover',
			array(
				'label'      => __( 'Padding', 'trx_addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors'  => array(
					'{{WRAPPER}} .trx-addons-main-nav-menu > .trx-addons-nav-menu-item:hover > .trx-addons-menu-link' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'nav_item_margin_hover',
			array(
				'label'      => __( 'Margin', 'trx_addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors'  => array(
					'{{WRAPPER}} .trx-addons-main-nav-menu > .trx-addons-nav-menu-item:hover' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'nav_item_active',
			array(
				'label' => __( 'Active', 'trx_addons' ),
			)
		);

		$this->add_control(
			'nav_item_color_active',
			array(
				'label'     => __( 'Color', 'trx_addons' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_ACCENT,
				),
				'selectors' => array(
					'{{WRAPPER}} .trx-addons-main-nav-menu > .trx-addons-active-item > .trx-addons-menu-link,
					{{WRAPPER}} .trx-addons-main-nav-menu > .current-menu-ancestor > .trx-addons-menu-link' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'nav_item_drop_icon_active',
			array(
				'label'     => __( 'Dropdown Icon Color', 'trx_addons' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_ACCENT,
				),
				'selectors' => array(
					'{{WRAPPER}} .trx-addons-main-nav-menu > .trx-addons-active-item > .trx-addons-menu-link .trx-addons-dropdown-icon,
					 {{WRAPPER}} .trx-addons-main-nav-menu > .current-menu-ancestor > .trx-addons-menu-link .trx-addons-dropdown-icon' => 'color: {{VALUE}};',
					'{{WRAPPER}} .trx-addons-main-nav-menu > .trx-addons-active-item > .trx-addons-menu-link .trx-addons-dropdown-icon svg path,
					 {{WRAPPER}} .trx-addons-main-nav-menu > .current-menu-ancestor > .trx-addons-menu-link .trx-addons-dropdown-icon svg path' => 'fill: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'nav_item_bg_active',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .trx-addons-main-nav-menu > .trx-addons-active-item > .trx-addons-menu-link,
								{{WRAPPER}} .trx-addons-main-nav-menu > .current-menu-ancestor > .trx-addons-menu-link',
			)
		);

		$this->add_control(
			'menu_item_pointer_color_active',
			array(
				'label'     => __( 'Item Hover Effect Color', 'trx_addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .trx-addons-nav-widget-container:not(.trx-addons-nav-pointer-framed) .trx-addons-active-item .trx-addons-menu-link-parent:before,
					 {{WRAPPER}} .trx-addons-nav-widget-container:not(.trx-addons-nav-pointer-framed) .trx-addons-active-item .trx-addons-menu-link-parent:after,
					 {{WRAPPER}} .trx-addons-nav-widget-container:not(.trx-addons-nav-pointer-framed) .current-menu-ancestor .trx-addons-menu-link-parent:before,
					 {{WRAPPER}} .trx-addons-nav-widget-container:not(.trx-addons-nav-pointer-framed) .current-menu-ancestor .trx-addons-menu-link-parent:after' => 'background-color: {{VALUE}}',
					'{{WRAPPER}} .trx-addons-nav-pointer-framed .trx-addons-active-item .trx-addons-menu-link-parent:before,
					 {{WRAPPER}} .trx-addons-nav-pointer-framed .trx-addons-active-item .trx-addons-menu-link-parent:after,
					 {{WRAPPER}} .trx-addons-nav-pointer-framed .current-menu-ancestor .trx-addons-menu-link-parent:before,
					 {{WRAPPER}} .trx-addons-nav-pointer-framed .current-menu-ancestor .trx-addons-menu-link-parent:after' => 'border-color: {{VALUE}}',
				),
				'condition' => array(
					'pointer!' => array( 'none', 'text' ),
				),
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name'     => 'nav_item_shadow_active',
				'selector' => '{{WRAPPER}} .trx-addons-main-nav-menu > .trx-addons-active-item > .trx-addons-menu-link,
								{{WRAPPER}} .trx-addons-main-nav-menu > .current-menu-ancestor > .trx-addons-menu-link',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'nav_item_border_active',
				'selector' => '{{WRAPPER}} .trx-addons-main-nav-menu > .trx-addons-active-item > .trx-addons-menu-link,
								{{WRAPPER}} .trx-addons-main-nav-menu > .current-menu-ancestor > .trx-addons-menu-link',
			)
		);

		$this->add_control(
			'nav_item_rad_active',
			array(
				'label'      => __( 'Border Radius', 'trx_addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors'  => array(
					'{{WRAPPER}} .trx-addons-main-nav-menu > .trx-addons-active-item > .trx-addons-menu-link,
					 {{WRAPPER}} .trx-addons-main-nav-menu > .current-menu-ancestor > .trx-addons-menu-link' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
			)
		);

		$this->add_responsive_control(
			'nav_item_padding_active',
			array(
				'label'      => __( 'Padding', 'trx_addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors'  => array(
					'{{WRAPPER}} .trx-addons-main-nav-menu > .trx-addons-active-item > .trx-addons-menu-link,
					 {{WRAPPER}} .trx-addons-main-nav-menu > .current-menu-ancestor > .trx-addons-menu-link' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'nav_item_margin_active',
			array(
				'label'      => __( 'Margin', 'trx_addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors'  => array(
					'{{WRAPPER}} .trx-addons-main-nav-menu > .trx-addons-active-item,
					 {{WRAPPER}} .trx-addons-main-nav-menu > .current-menu-ancestor' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * Adds Menu Item's Icon Style.
	 */
	private function register_style_menu_item_icon_controls() {

		$this->start_controls_section(
			'nav_item_icon_style_section',
			array(
				'label' => __( 'Menu Item Icon', 'trx_addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$left_order  = is_rtl() ? '1' : '0';
		$right_order = is_rtl() ? '0' : '1';
		$default     = is_rtl() ? $right_order : $left_order;

		$this->add_responsive_control(
			'nav_item_icon_pos',
			array(
				'label'     => __( 'Position', 'trx_addons' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					$left_order  => array(
						'title' => __( 'Left', 'trx_addons' ),
						'icon'  => 'eicon-h-align-left',
					),
					$right_order => array(
						'title' => __( 'Right', 'trx_addons' ),
						'icon'  => 'eicon-h-align-right',
					),
				),
				'default'   => $default,
				'toggle'    => false,
				'selectors' => array(
					'{{WRAPPER}} .trx-addons-nav-menu-item > .trx-addons-menu-link > .trx-addons-item-icon' => 'order: {{VALUE}}',
				),
			)
		);

		$this->add_responsive_control(
			'nav_item_icon_size',
			array(
				'label'       => __( 'Size', 'trx_addons' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'range'       => array(
					'px' => array(
						'min' => 0,
						'max' => 300,
					),
				),
				'selectors'   => array(
					'{{WRAPPER}} .trx-addons-nav-menu-item > .trx-addons-menu-link > .trx-addons-item-icon' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .trx-addons-nav-menu-item > .trx-addons-menu-link > .trx-addons-item-icon.dashicons,
					 {{WRAPPER}} .trx-addons-nav-menu-item > .trx-addons-menu-link > img.trx-addons-item-icon,
					 {{WRAPPER}} .trx-addons-nav-menu-item > .trx-addons-menu-link > .trx-addons-item-icon.trx-addons-lottie-animation' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->start_controls_tabs( 'tabs_nav_items_icon' );

		$this->start_controls_tab(
			'nav_item_icon_style',
			array(
				'label' => __( 'Normal', 'trx_addons' ),
			)
		);

		$this->add_control(
			'menu_item_icon_back_color',
			array(
				'label'     => __( 'Background Color', 'trx_addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .trx-addons-nav-menu-item > .trx-addons-menu-link > .trx-addons-item-icon' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'menu_item_icon_color',
			array(
				'label'     => __( 'Color', 'trx_addons' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .trx-addons-nav-menu-item > .trx-addons-menu-link > .trx-addons-item-icon' => 'color: {{VALUE}};',
					'{{WRAPPER}} .trx-addons-nav-menu-item > .trx-addons-menu-link > .trx-addons-item-icon svg path' => 'fill: {{VALUE}};',
				),
				'condition' => array(
					'menu_type' => 'custom',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'menu_item_icon_border',
				'selector' => '{{WRAPPER}} .trx-addons-nav-menu-item > .trx-addons-menu-link > .trx-addons-item-icon',
			)
		);

		$this->add_control(
			'menu_item_icon_radius',
			array(
				'label'      => __( 'Border Radius', 'trx_addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors'  => array(
					'{{WRAPPER}} .trx-addons-nav-menu-item > .trx-addons-menu-link > .trx-addons-item-icon' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
			)
		);

		$this->add_responsive_control(
			'nav_item_icon_padding',
			array(
				'label'      => __( 'Padding', 'trx_addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors'  => array(
					'{{WRAPPER}} .trx-addons-nav-menu-item > .trx-addons-menu-link > .trx-addons-item-icon' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'nav_item_icon_margin',
			array(
				'label'      => __( 'Margin', 'trx_addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors'  => array(
					'{{WRAPPER}} .trx-addons-nav-menu-item > .trx-addons-menu-link > .trx-addons-item-icon' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'nav_item_icon_style_hover',
			array(
				'label' => __( 'Hover', 'trx_addons' ),
			)
		);

		$this->add_control(
			'menu_item_icon_back_color_hover',
			array(
				'label'     => __( 'Background Color', 'trx_addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .trx-addons-nav-menu-item > .trx-addons-menu-link:hover > .trx-addons-item-icon' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'menu_item_icon_color_hover',
			array(
				'label'     => __( 'Color', 'trx_addons' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .trx-addons-nav-menu-item > .trx-addons-menu-link:hover > .trx-addons-item-icon' => 'color: {{VALUE}};',
					'{{WRAPPER}} .trx-addons-nav-menu-item > .trx-addons-menu-link:hover > .trx-addons-item-icon svg path' => 'fill: {{VALUE}};',
				),
				'condition' => array(
					'menu_type' => 'custom',
				),
			)
		);

		$this->add_control(
			'menu_item_icon_border_color_hover',
			array(
				'label'     => __( 'Border Color', 'trx_addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .trx-addons-nav-menu-item > .trx-addons-menu-link:hover > .trx-addons-item-icon' => 'border-color: {{VALUE}};',
				),
				'condition' => array(
					'menu_type' => 'custom',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'nav_item_icon_style_active',
			array(
				'label' => __( 'Active', 'trx_addons' ),
			)
		);

		$this->add_control(
			'menu_item_icon_back_color_active',
			array(
				'label'     => __( 'Background Color', 'trx_addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .trx-addons-nav-menu-item.trx-addons-active-item > .trx-addons-menu-link > .trx-addons-item-icon,
					 {{WRAPPER}} .trx-addons-nav-menu-item.current-menu-ancestor > .trx-addons-menu-link > .trx-addons-item-icon' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'menu_item_icon_color_active',
			array(
				'label'     => __( 'Color', 'trx_addons' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .trx-addons-nav-menu-item.trx-addons-active-item > .trx-addons-menu-link > .trx-addons-item-icon,
					 {{WRAPPER}} .trx-addons-nav-menu-item.current-menu-ancestor > .trx-addons-menu-link > .trx-addons-item-icon' => 'color: {{VALUE}};',
					'{{WRAPPER}} .trx-addons-nav-menu-item.trx-addons-active-item > .trx-addons-menu-link > .trx-addons-item-icon svg path,
					 {{WRAPPER}} .trx-addons-nav-menu-item.current-menu-ancestor > .trx-addons-menu-link > .trx-addons-item-icon svg path' => 'fill: {{VALUE}};',
				),
				'condition' => array(
					'menu_type' => 'custom',
				),
			)
		);

		$this->add_control(
			'menu_item_icon_border_color_active',
			array(
				'label'     => __( 'Border Color', 'trx_addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .trx-addons-nav-menu-item.trx-addons-active-item > .trx-addons-menu-link > .trx-addons-item-icon,
					 {{WRAPPER}} .trx-addons-nav-menu-item.current-menu-ancestor > .trx-addons-menu-link > .trx-addons-item-icon' => 'border-color: {{VALUE}};',
				),
				'condition' => array(
					'menu_type' => 'custom',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * Adds Menu Item's Badge Style.
	 */
	private function register_style_menu_item_badge_controls() {

		$this->start_controls_section(
			'nav_item_badge_style_section',
			array(
				'label' => __( 'Menu Item Badge', 'trx_addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'nav_item_badge_typo',
				'selector' => '{{WRAPPER}} .trx-addons-nav-menu-item > .trx-addons-menu-link > .trx-addons-item-badge,
				{{WRAPPER}} .trx-addons-ver-inner-container > div .trx-addons-main-nav-menu > .trx-addons-nav-menu-item > .trx-addons-rn-badge,
				{{WRAPPER}} .trx-addons-ver-inner-container > div .trx-addons-main-nav-menu > .trx-addons-nav-menu-item > .trx-addons-menu-link > .trx-addons-rn-badge',
			)
		);

		$this->start_controls_tabs( 'tabs_nav_items_badge' );

		$this->start_controls_tab(
			'nav_item_badge_style',
			array(
				'label' => __( 'Normal', 'trx_addons' ),
			)
		);

		$this->add_control(
			'item_badge_back_color',
			array(
				'label'     => __( 'Background Color', 'trx_addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .trx-addons-nav-menu-item > .trx-addons-menu-link > .trx-addons-item-badge,
					 {{WRAPPER}} .trx-addons-ver-inner-container > div .trx-addons-main-nav-menu > .trx-addons-nav-menu-item > .trx-addons-rn-badge,
					 {{WRAPPER}} .trx-addons-ver-inner-container > div .trx-addons-main-nav-menu > .trx-addons-nav-menu-item > .trx-addons-menu-link > .trx-addons-rn-badge' => 'background-color: {{VALUE}};',
				),
				'condition' => array(
					'menu_type' => 'custom',
				),
			)
		);

		$this->add_control(
			'item_badge_color',
			array(
				'label'     => __( 'Color', 'trx_addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .trx-addons-nav-menu-item > .trx-addons-menu-link > .trx-addons-item-badge,
					{{WRAPPER}} .trx-addons-ver-inner-container > div .trx-addons-main-nav-menu > .trx-addons-nav-menu-item > .trx-addons-rn-badge,
					 {{WRAPPER}} .trx-addons-ver-inner-container > div .trx-addons-main-nav-menu > .trx-addons-nav-menu-item > .trx-addons-menu-link > .trx-addons-rn-badge' => 'color: {{VALUE}};',
				),
				'condition' => array(
					'menu_type' => 'custom',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'nav_item_badge_border',
				'selector' => '{{WRAPPER}} .trx-addons-nav-menu-item > .trx-addons-menu-link > .trx-addons-item-badge,
				{{WRAPPER}} .trx-addons-ver-inner-container > div .trx-addons-main-nav-menu > .trx-addons-nav-menu-item > .trx-addons-rn-badge,
				{{WRAPPER}} .trx-addons-ver-inner-container > div .trx-addons-main-nav-menu > .trx-addons-nav-menu-item > .trx-addons-menu-link > .trx-addons-rn-badge',
			)
		);

		$this->add_control(
			'nav_item_badge_rad',
			array(
				'label'      => __( 'Border Radius', 'trx_addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors'  => array(
					'{{WRAPPER}} .trx-addons-nav-menu-item > .trx-addons-menu-link > .trx-addons-item-badge,
					{{WRAPPER}} .trx-addons-ver-inner-container > div .trx-addons-main-nav-menu > .trx-addons-nav-menu-item > .trx-addons-rn-badge,
					{{WRAPPER}} .trx-addons-ver-inner-container > div .trx-addons-main-nav-menu > .trx-addons-nav-menu-item > .trx-addons-menu-link > .trx-addons-rn-badge' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
			)
		);

		$this->add_responsive_control(
			'nav_item_badge_padding',
			array(
				'label'      => __( 'Padding', 'trx_addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors'  => array(
					'{{WRAPPER}} .trx-addons-nav-menu-item > .trx-addons-menu-link > .trx-addons-item-badge,
					 {{WRAPPER}} .trx-addons-ver-inner-container > div .trx-addons-main-nav-menu > .trx-addons-nav-menu-item > .trx-addons-rn-badge,
					 {{WRAPPER}} .trx-addons-ver-inner-container > div .trx-addons-main-nav-menu > .trx-addons-nav-menu-item > .trx-addons-menu-link > .trx-addons-rn-badge' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'nav_item_badge_margin',
			array(
				'label'      => __( 'Margin', 'trx_addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors'  => array(
					'{{WRAPPER}} .trx-addons-nav-menu-item > .trx-addons-menu-link > .trx-addons-item-badge,
					 {{WRAPPER}} .trx-addons-ver-inner-container > div .trx-addons-main-nav-menu > .trx-addons-nav-menu-item > .trx-addons-rn-badge,
					 {{WRAPPER}} .trx-addons-ver-inner-container > div .trx-addons-main-nav-menu > .trx-addons-nav-menu-item > .trx-addons-menu-link > .trx-addons-rn-badge' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'nav_item_badge_style_hover',
			array(
				'label' => __( 'Hover', 'trx_addons' ),
			)
		);

		$this->add_control(
			'item_badge_back_color_hover',
			array(
				'label'     => __( 'Background Color', 'trx_addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .trx-addons-nav-menu-item > .trx-addons-menu-link:hover > .trx-addons-item-badge,
					 {{WRAPPER}} .trx-addons-ver-inner-container > div .trx-addons-main-nav-menu > .trx-addons-nav-menu-item:hover > .trx-addons-rn-badge,
					 {{WRAPPER}} .trx-addons-ver-inner-container > div .trx-addons-main-nav-menu > .trx-addons-nav-menu-item > .trx-addons-menu-link:hover > .trx-addons-rn-badge' => 'background-color: {{VALUE}};',
				),
				'condition' => array(
					'menu_type' => 'custom',
				),
			)
		);

		$this->add_control(
			'item_badge_color_hover',
			array(
				'label'     => __( 'Color', 'trx_addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .trx-addons-nav-menu-item > .trx-addons-menu-link:hover > .trx-addons-item-badge,
					{{WRAPPER}} .trx-addons-ver-inner-container > div .trx-addons-main-nav-menu > .trx-addons-nav-menu-item:hover > .trx-addons-rn-badge,
					 {{WRAPPER}} .trx-addons-ver-inner-container > div .trx-addons-main-nav-menu > .trx-addons-nav-menu-item > .trx-addons-menu-link:hover > .trx-addons-rn-badge' => 'color: {{VALUE}};',
				),
				'condition' => array(
					'menu_type' => 'custom',
				),
			)
		);

		$this->add_control(
			'item_badge_border_color_hover',
			array(
				'label'     => __( 'Border Color', 'trx_addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .trx-addons-nav-menu-item > .trx-addons-menu-link:hover > .trx-addons-item-badge,
					 {{WRAPPER}} .trx-addons-ver-inner-container > div .trx-addons-main-nav-menu > .trx-addons-nav-menu-item:hover > .trx-addons-rn-badge,
					 {{WRAPPER}} .trx-addons-ver-inner-container > div .trx-addons-main-nav-menu > .trx-addons-nav-menu-item > .trx-addons-menu-link:hover > .trx-addons-rn-badge' => 'border-color: {{VALUE}};',
				),
				'condition' => array(
					'menu_type' => 'custom',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'nav_item_badge_style_active',
			array(
				'label' => __( 'Active', 'trx_addons' ),
			)
		);

		$this->add_control(
			'item_badge_back_color_active',
			array(
				'label'     => __( 'Background Color', 'trx_addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .trx-addons-nav-menu-item.trx-addons-active-item > .trx-addons-menu-link > .trx-addons-item-badge,
					 {{WRAPPER}} .trx-addons-ver-inner-container > div .trx-addons-main-nav-menu > .trx-addons-nav-menu-item.trx-addons-active-item > .trx-addons-rn-badge,
					 {{WRAPPER}} .trx-addons-ver-inner-container > div .trx-addons-main-nav-menu > .trx-addons-nav-menu-item.trx-addons-active-item > .trx-addons-menu-link > .trx-addons-rn-badge,
					 {{WRAPPER}} .trx-addons-nav-menu-item.current-menu-ancestor > .trx-addons-menu-link > .trx-addons-item-badge,
					 {{WRAPPER}} .trx-addons-ver-inner-container > div .trx-addons-main-nav-menu > .trx-addons-nav-menu-item.current-menu-ancestor > .trx-addons-rn-badge,
					 {{WRAPPER}} .trx-addons-ver-inner-container > div .trx-addons-main-nav-menu > .trx-addons-nav-menu-item.current-menu-ancestor > .trx-addons-menu-link > .trx-addons-rn-badge' => 'background-color: {{VALUE}};',
				),
				'condition' => array(
					'menu_type' => 'custom',
				),
			)
		);

		$this->add_control(
			'item_badge_color_active',
			array(
				'label'     => __( 'Color', 'trx_addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .trx-addons-nav-menu-item.trx-addons-active-item > .trx-addons-menu-link > .trx-addons-item-badge,
					 {{WRAPPER}} .trx-addons-ver-inner-container > div .trx-addons-main-nav-menu > .trx-addons-nav-menu-item.trx-addons-active-item > .trx-addons-rn-badge,
					 {{WRAPPER}} .trx-addons-ver-inner-container > div .trx-addons-main-nav-menu > .trx-addons-nav-menu-item.trx-addons-active-item > .trx-addons-menu-link > .trx-addons-rn-badge,
					 {{WRAPPER}} .trx-addons-nav-menu-item.current-menu-ancestor > .trx-addons-menu-link > .trx-addons-item-badge,
					 {{WRAPPER}} .trx-addons-ver-inner-container > div .trx-addons-main-nav-menu > .trx-addons-nav-menu-item.current-menu-ancestor > .trx-addons-rn-badge,
					 {{WRAPPER}} .trx-addons-ver-inner-container > div .trx-addons-main-nav-menu > .trx-addons-nav-menu-item.current-menu-ancestor > .trx-addons-menu-link > .trx-addons-rn-badge' => 'color: {{VALUE}};',
				),
				'condition' => array(
					'menu_type' => 'custom',
				),
			)
		);

		$this->add_control(
			'item_badge_border_color_active',
			array(
				'label'     => __( 'Border Color', 'trx_addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .trx-addons-nav-menu-item.trx-addons-active-item > .trx-addons-menu-link > .trx-addons-item-badge,
					 {{WRAPPER}} .trx-addons-ver-inner-container > div .trx-addons-main-nav-menu > .trx-addons-nav-menu-item.trx-addons-active-item > .trx-addons-rn-badge,
					 {{WRAPPER}} .trx-addons-ver-inner-container > div .trx-addons-main-nav-menu > .trx-addons-nav-menu-item.trx-addons-active-item > .trx-addons-menu-link > .trx-addons-rn-badge,
					 {{WRAPPER}} .trx-addons-nav-menu-item.current-menu-ancestor > .trx-addons-menu-link > .trx-addons-item-badge,
					 {{WRAPPER}} .trx-addons-ver-inner-container > div .trx-addons-main-nav-menu > .trx-addons-nav-menu-item.current-menu-ancestor > .trx-addons-rn-badge,
					 {{WRAPPER}} .trx-addons-ver-inner-container > div .trx-addons-main-nav-menu > .trx-addons-nav-menu-item.current-menu-ancestor > .trx-addons-menu-link > .trx-addons-rn-badge' => 'border-color: {{VALUE}};',
				),
				'condition' => array(
					'menu_type' => 'custom',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * Get submenu container style.
	 */
	private function register_style_submenu_container_controls() {

		$this->start_controls_section(
			'submenu_style_section',
			array(
				'label' => __( 'Submenu Style', 'trx_addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->start_controls_tabs( 'submenus_style' );

		$this->start_controls_tab(
			'sub_simple',
			array(
				'label' => __( 'Simple Panel', 'trx_addons' ),
			)
		);

		$this->add_responsive_control(
			'sub_minwidth',
			array(
				'label'       => __( 'Minimum Width', 'trx_addons' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'range'       => array(
					'px' => array(
						'min' => 0,
						'max' => 1000,
					),
				),
				'selectors'   => array(
					'{{WRAPPER}} .trx-addons-mobile-menu-container .trx-addons-submenu,
                    {{WRAPPER}}.trx-addons-nav-ver .trx-addons-nav-menu-item.menu-item-has-children .trx-addons-submenu,
                    {{WRAPPER}}.trx-addons-nav-hor .trx-addons-nav-menu-item.menu-item-has-children .trx-addons-submenu' => 'min-width: {{SIZE}}{{UNIT}};',
				),
				'condition'   => array(
					'menu_type!' => 'custom',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'sub_shadow',
				'selector' => '{{WRAPPER}} .trx-addons-nav-menu-container .trx-addons-submenu,
								{{WRAPPER}} .trx-addons-mobile-menu-container .trx-addons-submenu',
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'sub_bg',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .trx-addons-nav-menu-container .trx-addons-submenu,
								{{WRAPPER}} .trx-addons-mobile-menu-container .trx-addons-submenu',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'sub_border',
				'selector' => '{{WRAPPER}} .trx-addons-nav-menu-container .trx-addons-submenu,
								{{WRAPPER}} .trx-addons-mobile-menu-container .trx-addons-submenu',
			)
		);

		$this->add_control(
			'sub_rad',
			array(
				'label'      => __( 'Border Radius', 'trx_addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors'  => array(
					'{{WRAPPER}} .trx-addons-nav-menu-container .trx-addons-submenu,
					 {{WRAPPER}} .trx-addons-mobile-menu-container .trx-addons-submenu' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
			)
		);

		$this->add_responsive_control(
			'sub_padding',
			array(
				'label'      => __( 'Padding', 'trx_addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors'  => array(
					'{{WRAPPER}} .trx-addons-nav-menu-container .trx-addons-submenu,
					 {{WRAPPER}} .trx-addons-mobile-menu-container .trx-addons-submenu' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'sub_margin_1',
			array(
				'label'      => __( 'Margin (1st level)', 'trx_addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors'  => array(
					'{{WRAPPER}} .trx-addons-nav-menu-container > .trx-addons-nav-menu > .trx-addons-nav-menu-item > .trx-addons-submenu,
					 {{WRAPPER}} .trx-addons-mobile-menu-container > .trx-addons-nav-menu > .trx-addons-nav-menu-item > .trx-addons-submenu' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'sub_margin_2',
			array(
				'label'      => __( 'Margin (2nd+ level)', 'trx_addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors'  => array(
					'{{WRAPPER}} .trx-addons-nav-menu-container .trx-addons-submenu .trx-addons-nav-menu-item > .trx-addons-submenu,
					 {{WRAPPER}} .trx-addons-mobile-menu-container .trx-addons-submenu .trx-addons-nav-menu-item > .trx-addons-submenu' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'sub_mega',
			array(
				'label' => __( 'Mega Panel', 'trx_addons' ),
			)
		);

		$mega_pos = is_rtl() ? 'right' : 'left';

		$this->add_responsive_control(
			'sub_mega_offset',
			array(
				'label'       => __( 'Offset', 'trx_addons' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'range'       => array(
					'px' => array(
						'min' => -1000,
						'max' => 2000,
					),
					'%'  => array(
						'min' => -100,
						'max' => 100,
					),
				),
				'selectors'   => array(
					'{{WRAPPER}}.trx-addons-nav-hor .trx-addons-nav-menu-container .trx-addons-mega-content-container' => $mega_pos . ': {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.trx-addons-nav-ver .trx-addons-nav-menu-container .trx-addons-mega-content-container' => 'top: {{SIZE}}{{UNIT}};',
				),
				'condition'   => array(
					'menu_type!' => 'custom',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'sub_mega_shadow',
				'selector' => '{{WRAPPER}} .trx-addons-nav-menu-container .trx-addons-mega-content-container,
								{{WRAPPER}} .trx-addons-mobile-menu-container .trx-addons-mega-content-container',
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'sub_mega_bg',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .trx-addons-nav-menu-container .trx-addons-mega-content-container,
								{{WRAPPER}} .trx-addons-mobile-menu-container .trx-addons-mega-content-container',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'sub_mega_border',
				'selector' => '{{WRAPPER}} .trx-addons-nav-menu-container .trx-addons-mega-content-container,
								{{WRAPPER}} .trx-addons-mobile-menu-container .trx-addons-mega-content-container',
			)
		);

		$this->add_control(
			'sub_mega_rad',
			array(
				'label'      => __( 'Border Radius', 'trx_addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors'  => array(
					'{{WRAPPER}} .trx-addons-nav-menu-container .trx-addons-mega-content-container,
					 {{WRAPPER}} .trx-addons-mobile-menu-container .trx-addons-mega-content-container' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
			)
		);

		$this->add_responsive_control(
			'sub_mega_padding',
			array(
				'label'      => __( 'Padding', 'trx_addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors'  => array(
					'{{WRAPPER}} .trx-addons-nav-menu-container .trx-addons-nav-menu-item .trx-addons-mega-content-container,
					 {{WRAPPER}} .trx-addons-mobile-menu-container .trx-addons-nav-menu-item .trx-addons-mega-content-container' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'sub_mega_margin_1',
			array(
				'label'      => __( 'Margin (1st level)', 'trx_addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors'  => array(
					'{{WRAPPER}} .trx-addons-nav-menu-container > .trx-addons-nav-menu > .trx-addons-nav-menu-item > .trx-addons-mega-content-container,
					 {{WRAPPER}} .trx-addons-mobile-menu-container > .trx-addons-nav-menu > .trx-addons-nav-menu-item > .trx-addons-mega-content-container' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'sub_mega_margin_2',
			array(
				'label'      => __( 'Margin (2nd+ level)', 'trx_addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors'  => array(
					'{{WRAPPER}} .trx-addons-nav-menu-container .trx-addons-submenu .trx-addons-nav-menu-item > .trx-addons-mega-content-container,
					 {{WRAPPER}} .trx-addons-mobile-menu-container .trx-addons-submenu .trx-addons-nav-menu-item > .trx-addons-mega-content-container' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * Get submenu item style.
	 */
	private function register_style_submenu_item_controls() {

		$this->start_controls_section(
			'submenu_item_style_section',
			array(
				'label' => __( 'Submenu Item Style', 'trx_addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'sub_item_typo',
				'global'   => array(
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				),
				'selector' => '{{WRAPPER}} .trx-addons-main-nav-menu .trx-addons-submenu .trx-addons-submenu-link',
			)
		);

		$this->add_responsive_control(
			'sub_item_drop_icon_size',
			array(
				'label'      => __( 'Dropdown Icon Size', 'trx_addons' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors'  => array(
					'{{WRAPPER}} .trx-addons-main-nav-menu .trx-addons-submenu .trx-addons-submenu-link .trx-addons-dropdown-icon' => 'font-size: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'menu_type!' => 'custom',
				),
			)
		);

		$this->add_responsive_control(
			'sub_item_drop_icon_position',
			array(
				'label'        => __( 'Dropdown Icon Position', 'trx_addons' ),
				'type'         => Controls_Manager::SELECT,
				'options'      => array(
					'inline' => __( 'Inline', 'trx_addons' ),
					'fixed'  => __( 'Fixed', 'trx_addons' ),
				),
				'default'      => 'inline',
				'prefix_class' => 'trx-addons-dropdown-icon-',
			)
		);

		$this->add_responsive_control(
			'sub_item_drop_icon_margin',
			array(
				'label'      => __( 'Dropdown Icon Margin', 'trx_addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors'  => array(
					'{{WRAPPER}} .trx-addons-main-nav-menu .trx-addons-submenu .trx-addons-submenu-link .trx-addons-dropdown-icon' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'  => array(
					'menu_type!' => 'custom',
				),
			)
		);

		$this->start_controls_tabs( 'sub_items_styles' );

		$this->start_controls_tab(
			'sub_item_normal',
			array(
				'label' => __( 'Normal', 'trx_addons' ),
			)
		);

		$this->add_control(
			'sub_item_color',
			array(
				'label'     => __( 'Color', 'trx_addons' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .trx-addons-main-nav-menu .trx-addons-submenu .trx-addons-submenu-link' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'sub_item_drop_icon_color',
			array(
				'label'     => __( 'Dropdown Icon Color', 'trx_addons' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .trx-addons-main-nav-menu .trx-addons-submenu .trx-addons-submenu-link .trx-addons-dropdown-icon' => 'color: {{VALUE}};',
					'{{WRAPPER}} .trx-addons-main-nav-menu .trx-addons-submenu .trx-addons-submenu-link .trx-addons-dropdown-icon svg path' => 'fill: {{VALUE}};',
				),
				'condition' => array(
					'menu_type!' => 'custom',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'sub_item_bg',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .trx-addons-main-nav-menu .trx-addons-submenu .trx-addons-submenu-item > .trx-addons-submenu-link',
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name'     => 'sub_item_shadow',
				'selector' => '{{WRAPPER}} .trx-addons-main-nav-menu .trx-addons-submenu .trx-addons-submenu-item > .trx-addons-submenu-link',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'sub_item_border',
				'selector' => '{{WRAPPER}} .trx-addons-main-nav-menu .trx-addons-submenu .trx-addons-submenu-item > .trx-addons-submenu-link',
			)
		);

		$this->add_control(
			'sub_item_rad',
			array(
				'label'      => __( 'Border Radius', 'trx_addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors'  => array(
					'{{WRAPPER}} .trx-addons-main-nav-menu .trx-addons-submenu .trx-addons-submenu-item > .trx-addons-submenu-link' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
			)
		);

		$this->add_responsive_control(
			'sub_item_padding',
			array(
				'label'      => __( 'Padding', 'trx_addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors'  => array(
					'{{WRAPPER}} .trx-addons-main-nav-menu .trx-addons-submenu .trx-addons-submenu-item > .trx-addons-submenu-link' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'sub_item_margin',
			array(
				'label'      => __( 'Margin', 'trx_addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors'  => array(
					'{{WRAPPER}} .trx-addons-main-nav-menu .trx-addons-submenu .trx-addons-submenu-item' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'sub_item_hover',
			array(
				'label' => __( 'Hover', 'trx_addons' ),
			)
		);

		$this->add_control(
			'sub_item_color_hover',
			array(
				'label'     => __( 'Color', 'trx_addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#000',
				'selectors' => array(
					'{{WRAPPER}} .trx-addons-main-nav-menu .trx-addons-submenu-item:hover > .trx-addons-submenu-link' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'sub_item_drop_icon_hover',
			array(
				'label'     => __( 'Dropdown Icon Color', 'trx_addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#000',
				'selectors' => array(
					'{{WRAPPER}} .trx-addons-main-nav-menu .trx-addons-submenu-item:hover > .trx-addons-submenu-link .trx-addons-dropdown-icon' => 'color: {{VALUE}};',
					'{{WRAPPER}} .trx-addons-main-nav-menu .trx-addons-submenu-item:hover > .trx-addons-submenu-link .trx-addons-dropdown-icon svg path' => 'fill: {{VALUE}};',
				),
				'condition' => array(
					'menu_type!' => 'custom',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'           => 'sub_item_bg_hover',
				'types'          => array( 'classic', 'gradient' ),
				'selector'       => '{{WRAPPER}}:not(.trx-addons-hamburger-menu):not(.trx-addons-nav-slide):not(.trx-addons-nav-dropdown) .trx-addons-main-nav-menu .trx-addons-submenu .trx-addons-submenu-item:hover > .trx-addons-submenu-link,
									{{WRAPPER}}.trx-addons-hamburger-menu .trx-addons-main-nav-menu .trx-addons-submenu > .trx-addons-submenu-item:hover > .trx-addons-submenu-link,
									{{WRAPPER}}.trx-addons-nav-slide .trx-addons-main-nav-menu .trx-addons-submenu > .trx-addons-submenu-item:hover > .trx-addons-submenu-link,
									{{WRAPPER}}.trx-addons-nav-dropdown .trx-addons-main-nav-menu .trx-addons-submenu > .trx-addons-submenu-item:hover > .trx-addons-submenu-link',
				'fields_options' => array(
					'background' => array(
						'default' => 'classic',
					),
					'color'      => array(
						'global' => array(
							'default' => Global_Colors::COLOR_SECONDARY,
						),
					),
				),
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name'     => 'sub_item_shadow_hover',
				'selector' => '{{WRAPPER}} .trx-addons-main-nav-menu .trx-addons-submenu .trx-addons-submenu-item:hover',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'sub_item_border_hover',
				'selector' => '{{WRAPPER}} .trx-addons-main-nav-menu .trx-addons-submenu .trx-addons-submenu-item:hover > .trx-addons-submenu-link',
			)
		);

		$this->add_control(
			'sub_item_rad_hover',
			array(
				'label'      => __( 'Border Radius', 'trx_addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors'  => array(
					'{{WRAPPER}} .trx-addons-main-nav-menu .trx-addons-submenu .trx-addons-submenu-item:hover > .trx-addons-submenu-link' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
			)
		);

		$this->add_responsive_control(
			'sub_item_padding_hover',
			array(
				'label'      => __( 'Padding', 'trx_addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors'  => array(
					'{{WRAPPER}} .trx-addons-main-nav-menu .trx-addons-submenu .trx-addons-submenu-item:hover > .trx-addons-submenu-link' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'sub_item_margin_hover',
			array(
				'label'      => __( 'Margin', 'trx_addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors'  => array(
					'{{WRAPPER}} .trx-addons-main-nav-menu .trx-addons-submenu .trx-addons-submenu-item:hover' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'sub_item_active',
			array(
				'label' => __( 'Active', 'trx_addons' ),
			)
		);

		$this->add_control(
			'sub_item_color_active',
			array(
				'label'     => __( 'Color', 'trx_addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .trx-addons-main-nav-menu .trx-addons-submenu .trx-addons-active-item .trx-addons-submenu-link,
					 {{WRAPPER}} .trx-addons-main-nav-menu .trx-addons-submenu .current-menu-ancestor .trx-addons-submenu-link' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'sub_item_drop_icon_active',
			array(
				'label'     => __( 'Dropdown Icon Color', 'trx_addons' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .trx-addons-main-nav-menu .trx-addons-submenu .trx-addons-active-item .trx-addons-submenu-link .trx-addons-dropdown-icon,
					 {{WRAPPER}} .trx-addons-main-nav-menu .trx-addons-submenu .current-menu-ancestor .trx-addons-submenu-link .trx-addons-dropdown-icon' => 'color: {{VALUE}};',
					'{{WRAPPER}} .trx-addons-main-nav-menu .trx-addons-submenu .trx-addons-active-item .trx-addons-submenu-link .trx-addons-dropdown-icon svg path,
					 {{WRAPPER}} .trx-addons-main-nav-menu .trx-addons-submenu .current-menu-ancestor .trx-addons-submenu-link .trx-addons-dropdown-icon svg path' => 'fill: {{VALUE}};',
				),
				'condition' => array(
					'menu_type!' => 'custom',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'sub_item_bg_active',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .trx-addons-main-nav-menu .trx-addons-submenu .trx-addons-active-item > .trx-addons-submenu-link,
								{{WRAPPER}} .trx-addons-main-nav-menu .trx-addons-submenu .current-menu-ancestor > .trx-addons-submenu-link',
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name'     => 'sub_item_shadow_active',
				'selector' => '{{WRAPPER}} .trx-addons-main-nav-menu .trx-addons-submenu .trx-addons-active-item,
								{{WRAPPER}} .trx-addons-main-nav-menu .trx-addons-submenu .current-menu-ancestor',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'sub_item_border_active',
				'selector' => '{{WRAPPER}} .trx-addons-main-nav-menu .trx-addons-submenu .trx-addons-active-item > .trx-addons-submenu-link,
								{{WRAPPER}} .trx-addons-main-nav-menu .trx-addons-submenu .current-menu-ancestor > .trx-addons-submenu-link',
			)
		);

		$this->add_control(
			'sub_item_rad_active',
			array(
				'label'      => __( 'Border Radius', 'trx_addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors'  => array(
					'{{WRAPPER}} .trx-addons-main-nav-menu .trx-addons-submenu .trx-addons-active-item > .trx-addons-submenu-link,
					 {{WRAPPER}} .trx-addons-main-nav-menu .trx-addons-submenu .current-menu-ancestor > .trx-addons-submenu-link' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
			)
		);

		$this->add_responsive_control(
			'sub_item_padding_active',
			array(
				'label'      => __( 'Padding', 'trx_addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors'  => array(
					'{{WRAPPER}} .trx-addons-main-nav-menu .trx-addons-submenu .trx-addons-active-item > .trx-addons-submenu-link,
					 {{WRAPPER}} .trx-addons-main-nav-menu .trx-addons-submenu .current-menu-ancestor > .trx-addons-submenu-link' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'sub_item_margin_active',
			array(
				'label'      => __( 'Margin', 'trx_addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors'  => array(
					'{{WRAPPER}} .trx-addons-main-nav-menu .trx-addons-submenu .trx-addons-active-item,
					 {{WRAPPER}} .trx-addons-main-nav-menu .trx-addons-submenu .current-menu-ancestor' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * Adds Submenu Items' Icon Style.
	 */
	private function register_style_submenu_item_icon_controls() {

		$this->start_controls_section(
			'sub_items_icon_style',
			array(
				'label' => __( 'Submenu Item Icon', 'trx_addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$left_order  = is_rtl() ? '1' : '0';
		$right_order = is_rtl() ? '0' : '1';
		$default     = is_rtl() ? $right_order : $left_order;

		$this->add_responsive_control(
			'sub_icon_pos',
			array(
				'label'     => __( 'Position', 'trx_addons' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					$left_order  => array(
						'title' => __( 'Left', 'trx_addons' ),
						'icon'  => 'eicon-h-align-left',
					),
					$right_order => array(
						'title' => __( 'Right', 'trx_addons' ),
						'icon'  => 'eicon-h-align-right',
					),
				),
				'default'   => $default,
				'toggle'    => false,
				'selectors' => array(
					'{{WRAPPER}} .trx-addons-submenu-item .trx-addons-submenu-link .trx-addons-sub-item-icon' => 'order: {{VALUE}}',
				),
			)
		);

		$this->add_responsive_control(
			'sub_icon_size',
			array(
				'label'       => __( 'Size', 'trx_addons' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'range'       => array(
					'px' => array(
						'min' => 0,
						'max' => 300,
					),
				),
				'selectors'   => array(
					'{{WRAPPER}} .trx-addons-submenu-item .trx-addons-submenu-link .trx-addons-sub-item-icon' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .trx-addons-submenu-item .trx-addons-submenu-link .trx-addons-sub-item-icon.dashicons,
					 {{WRAPPER}} .trx-addons-submenu-item .trx-addons-submenu-link img.trx-addons-sub-item-icon,
					 {{WRAPPER}} .trx-addons-submenu-item .trx-addons-submenu-link .trx-addons-sub-item-icon.trx-addons-lottie-animation' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->start_controls_tabs( 'tabs_sub_items_icon' );

		$this->start_controls_tab(
			'sub_icon_style',
			array(
				'label' => __( 'Normal', 'trx_addons' ),
			)
		);

		$this->add_control(
			'sub_item_icon_back_color',
			array(
				'label'     => __( 'Background Color', 'trx_addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .trx-addons-submenu-item .trx-addons-submenu-link .trx-addons-sub-item-icon' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'sub_item_icon_color',
			array(
				'label'     => __( 'Color', 'trx_addons' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .trx-addons-submenu-item .trx-addons-submenu-link .trx-addons-sub-item-icon' => 'color: {{VALUE}};',
					'{{WRAPPER}} .trx-addons-submenu-item .trx-addons-submenu-link .trx-addons-sub-item-icon svg path' => 'fill: {{VALUE}};',
				),
				'condition' => array(
					'menu_type' => 'custom',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'sub_item_icon_border',
				'selector' => '{{WRAPPER}} .trx-addons-submenu-item .trx-addons-submenu-link .trx-addons-sub-item-icon',
			)
		);

		$this->add_control(
			'sub_item_icon_radius',
			array(
				'label'      => __( 'Border Radius', 'trx_addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors'  => array(
					'{{WRAPPER}} .trx-addons-submenu-item .trx-addons-submenu-link .trx-addons-sub-item-icon' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
			)
		);

		$this->add_responsive_control(
			'sub_icon_padding',
			array(
				'label'      => __( 'Padding', 'trx_addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors'  => array(
					'{{WRAPPER}} .trx-addons-submenu-item .trx-addons-submenu-link .trx-addons-sub-item-icon' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'sub_icon_margin',
			array(
				'label'      => __( 'Margin', 'trx_addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors'  => array(
					'{{WRAPPER}} .trx-addons-submenu-item .trx-addons-submenu-link .trx-addons-sub-item-icon' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'sub_icon_style_hover',
			array(
				'label' => __( 'Hover', 'trx_addons' ),
			)
		);

		$this->add_control(
			'sub_item_icon_back_color_hover',
			array(
				'label'     => __( 'Background Color', 'trx_addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .trx-addons-submenu-item .trx-addons-submenu-link:hover .trx-addons-sub-item-icon' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'sub_item_icon_color_hover',
			array(
				'label'     => __( 'Color', 'trx_addons' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .trx-addons-submenu-item .trx-addons-submenu-link:hover .trx-addons-sub-item-icon' => 'color: {{VALUE}};',
					'{{WRAPPER}} .trx-addons-submenu-item .trx-addons-submenu-link:hover .trx-addons-sub-item-icon svg path' => 'fill: {{VALUE}};',
				),
				'condition' => array(
					'menu_type' => 'custom',
				),
			)
		);

		$this->add_control(
			'sub_item_icon_border_color_hover',
			array(
				'label'     => __( 'Border Color', 'trx_addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .trx-addons-submenu-item .trx-addons-submenu-link:hover .trx-addons-sub-item-icon' => 'border-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'sub_icon_style_active',
			array(
				'label' => __( 'Active', 'trx_addons' ),
			)
		);

		$this->add_control(
			'sub_item_icon_back_color_active',
			array(
				'label'     => __( 'Background Color', 'trx_addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .trx-addons-submenu-item.trx-addons-active-item .trx-addons-submenu-link .trx-addons-sub-item-icon,
					 {{WRAPPER}} .trx-addons-submenu-item.current-menu-ancestor .trx-addons-submenu-link .trx-addons-sub-item-icon' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'sub_item_icon_color_active',
			array(
				'label'     => __( 'Color', 'trx_addons' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .trx-addons-submenu-item.trx-addons-active-item .trx-addons-submenu-link .trx-addons-sub-item-icon,
					 {{WRAPPER}} .trx-addons-submenu-item.current-menu-ancestor .trx-addons-submenu-link .trx-addons-sub-item-icon' => 'color: {{VALUE}};',
					'{{WRAPPER}} .trx-addons-submenu-item.trx-addons-active-item .trx-addons-submenu-link .trx-addons-sub-item-icon svg path,
					 {{WRAPPER}} .trx-addons-submenu-item.current-menu-ancestor .trx-addons-submenu-link .trx-addons-sub-item-icon svg path' => 'fill: {{VALUE}};',
				),
				'condition' => array(
					'menu_type' => 'custom',
				),
			)
		);

		$this->add_control(
			'sub_item_icon_border_color_active',
			array(
				'label'     => __( 'Border Color', 'trx_addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .trx-addons-submenu-item.trx-addons-active-item .trx-addons-submenu-link .trx-addons-sub-item-icon,
					 {{WRAPPER}} .trx-addons-submenu-item.current-menu-ancestor .trx-addons-submenu-link .trx-addons-sub-item-icon' => 'border-color: {{VALUE}};',
				),
				'condition' => array(
					'menu_type' => 'custom',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * Adds Submenu Items' Badge Style.
	 */
	private function register_style_submenu_item_badge_controls() {

		$this->start_controls_section(
			'sub_extra_style',
			array(
				'label' => __( 'Submenu Item Badge', 'trx_addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'sub_badge_typo',
				'selector' => '{{WRAPPER}} .trx-addons-submenu-item .trx-addons-submenu-link .trx-addons-sub-item-badge,
								{{WRAPPER}} .trx-addons-submenu-item .trx-addons-rn-badge,
								{{WRAPPER}} .trx-addons-mega-content-container .trx-addons-rn-badge',
			)
		);

		// TODO: check the all the badges CSS.
		$badge_pos = is_rtl() ? 'left' : 'right';

		$this->add_responsive_control(
			'sub_badge_hor',
			array(
				'label'       => __( 'Horizontal Offset', 'trx_addons' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'range'       => array(
					'px' => array(
						'min' => 0,
						'max' => 1000,
					),
				),
				'selectors'   => array(
					'{{WRAPPER}}:not(.trx-addons-nav-ver) .trx-addons-submenu-item .trx-addons-submenu-link .trx-addons-sub-item-badge,
					 {{WRAPPER}}:not(.trx-addons-nav-ver) .trx-addons-submenu-item .trx-addons-rn-badge,
					 {{WRAPPER}} .trx-addons-mega-content-container .trx-addons-rn-badge' => $badge_pos . ' : {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.trx-addons-nav-ver.trx-addons-vertical-right .trx-addons-submenu-item .trx-addons-submenu-link .trx-addons-sub-item-badge,
					 {{WRAPPER}}.trx-addons-nav-ver.trx-addons-vertical-right .trx-addons-submenu-item .trx-addons-rn-badge' => 'right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.trx-addons-nav-ver.trx-addons-vertical-left .trx-addons-submenu-item .trx-addons-submenu-link .trx-addons-sub-item-badge,
					 {{WRAPPER}}.trx-addons-nav-ver.trx-addons-vertical-left .trx-addons-submenu-item .trx-addons-rn-badge' => 'left: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'sub_badge_ver',
			array(
				'label'       => __( 'Vertical Offset', 'trx_addons' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'range'       => array(
					'px' => array(
						'min' => 0,
						'max' => 1000,
					),
				),
				'selectors'   => array(
					'{{WRAPPER}} .trx-addons-submenu-item .trx-addons-submenu-link .trx-addons-sub-item-badge,
					 {{WRAPPER}} .trx-addons-submenu-item .trx-addons-rn-badge,
					 {{WRAPPER}} .trx-addons-mega-content-container .trx-addons-rn-badge' => 'top: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->start_controls_tabs( 'tabs_sub_items_badge' );

		$this->start_controls_tab(
			'sub_badge_style',
			array(
				'label' => __( 'Normal', 'trx_addons' ),
			)
		);

		$this->add_control(
			'sub_item_badge_back_color',
			array(
				'label'     => __( 'Background Color', 'trx_addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .trx-addons-submenu-item .trx-addons-submenu-link .trx-addons-sub-item-badge,
					 {{WRAPPER}} .trx-addons-submenu-item .trx-addons-rn-badge,
					 {{WRAPPER}} .trx-addons-mega-content-container .trx-addons-rn-badge' => 'background-color: {{VALUE}};',
				),
				'condition' => array(
					'menu_type' => 'custom',
				),
			)
		);

		$this->add_control(
			'sub_item_badge_color',
			array(
				'label'     => __( 'Color', 'trx_addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .trx-addons-submenu-item .trx-addons-submenu-link .trx-addons-sub-item-badge,
					 {{WRAPPER}} .trx-addons-submenu-item .trx-addons-rn-badge,
					 {{WRAPPER}} .trx-addons-mega-content-container .trx-addons-rn-badge' => 'color: {{VALUE}};',
				),
				'condition' => array(
					'menu_type' => 'custom',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'sub_badge_border',
				'selector' => '{{WRAPPER}} .trx-addons-submenu-item .trx-addons-submenu-link .trx-addons-sub-item-badge,
								{{WRAPPER}} .trx-addons-submenu-item .trx-addons-rn-badge,
								{{WRAPPER}} .trx-addons-mega-content-container .trx-addons-rn-badge',
			)
		);

		$this->add_control(
			'sub_badge_rad',
			array(
				'label'      => __( 'Border Radius', 'trx_addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors'  => array(
					'{{WRAPPER}} .trx-addons-submenu-item .trx-addons-submenu-link .trx-addons-sub-item-badge,
					 {{WRAPPER}} .trx-addons-submenu-item .trx-addons-rn-badge,
					 {{WRAPPER}} .trx-addons-mega-content-container .trx-addons-rn-badge' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
			)
		);

		$this->add_responsive_control(
			'sub_badge_padding',
			array(
				'label'      => __( 'Padding', 'trx_addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors'  => array(
					'{{WRAPPER}} .trx-addons-submenu-item .trx-addons-submenu-link .trx-addons-sub-item-badge,
					 {{WRAPPER}} .trx-addons-submenu-item .trx-addons-rn-badge,
					 {{WRAPPER}} .trx-addons-mega-content-container .trx-addons-rn-badge' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'sub_badge_style_hover',
			array(
				'label' => __( 'Hover', 'trx_addons' ),
			)
		);

		$this->add_control(
			'sub_item_badge_back_color_hover',
			array(
				'label'     => __( 'Background Color', 'trx_addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .trx-addons-submenu-item .trx-addons-submenu-link:hover .trx-addons-sub-item-badge,
					 {{WRAPPER}} .trx-addons-submenu-item:hover .trx-addons-rn-badge,
					 {{WRAPPER}} .trx-addons-mega-content-container .trx-addons-rn-badge:hover' => 'background-color: {{VALUE}};',
				),
				'condition' => array(
					'menu_type' => 'custom',
				),
			)
		);

		$this->add_control(
			'sub_item_badge_color_hover',
			array(
				'label'     => __( 'Color', 'trx_addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .trx-addons-submenu-item .trx-addons-submenu-link:hover .trx-addons-sub-item-badge,
					 {{WRAPPER}} .trx-addons-submenu-item:hover .trx-addons-rn-badge,
					 {{WRAPPER}} .trx-addons-mega-content-container .trx-addons-rn-badge:hover' => 'color: {{VALUE}};',
				),
				'condition' => array(
					'menu_type' => 'custom',
				),
			)
		);

		$this->add_control(
			'sub_item_badge_border_color_hover',
			array(
				'label'     => __( 'Border Color', 'trx_addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .trx-addons-submenu-item .trx-addons-submenu-link:hover .trx-addons-sub-item-badge,
					 {{WRAPPER}} .trx-addons-submenu-item:hover .trx-addons-rn-badge,
					 {{WRAPPER}} .trx-addons-mega-content-container .trx-addons-rn-badge:hover' => 'border-color: {{VALUE}};',
				),
				'condition' => array(
					'menu_type' => 'custom',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'sub_badge_style_active',
			array(
				'label' => __( 'Active', 'trx_addons' ),
			)
		);

		$this->add_control(
			'sub_item_badge_back_color_active',
			array(
				'label'     => __( 'Background Color', 'trx_addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .trx-addons-submenu-item.trx-addons-active-item .trx-addons-submenu-link .trx-addons-sub-item-badge,
					 {{WRAPPER}} .trx-addons-submenu-item.trx-addons-active-item .trx-addons-rn-badge,
					 {{WRAPPER}} .trx-addons-submenu-item.current-menu-ancestor .trx-addons-submenu-link .trx-addons-sub-item-badge,
					 {{WRAPPER}} .trx-addons-submenu-item.current-menu-ancestor .trx-addons-rn-badge' => 'background-color: {{VALUE}};',
				),
				'condition' => array(
					'menu_type' => 'custom',
				),
			)
		);

		$this->add_control(
			'sub_item_badge_color_active',
			array(
				'label'     => __( 'Color', 'trx_addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .trx-addons-submenu-item.trx-addons-active-item .trx-addons-submenu-link .trx-addons-sub-item-badge,
					 {{WRAPPER}} .trx-addons-submenu-item.trx-addons-active-item .trx-addons-rn-badge,
					 {{WRAPPER}} .trx-addons-submenu-item.current-menu-ancestor .trx-addons-submenu-link .trx-addons-sub-item-badge,
					 {{WRAPPER}} .trx-addons-submenu-item.current-menu-ancestor .trx-addons-rn-badge' => 'color: {{VALUE}};',
				),
				'condition' => array(
					'menu_type' => 'custom',
				),
			)
		);

		$this->add_control(
			'sub_item_badge_border_color_active',
			array(
				'label'     => __( 'Border Color', 'trx_addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .trx-addons-submenu-item.trx-addons-active-item .trx-addons-submenu-link .trx-addons-sub-item-badge,
					 {{WRAPPER}} .trx-addons-submenu-item.trx-addons-active-item .trx-addons-rn-badge,
					 {{WRAPPER}} .trx-addons-submenu-item.current-menu-ancestor .trx-addons-submenu-link .trx-addons-sub-item-badge,
					 {{WRAPPER}} .trx-addons-submenu-item.current-menu-ancestor .trx-addons-rn-badge' => 'border-color: {{VALUE}};',
				),
				'condition' => array(
					'menu_type' => 'custom',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * Get toggle menu container style.
	 */
	private function register_style_toggle_menu_controls() {

		$this->start_controls_section(
			'toggle_menu_style_section',
			array(
				'label' => __( 'Expand/Slide Menu Style', 'trx_addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'ham_toggle_style',
			array(
				'label' => __( 'Toggle Button', 'trx_addons' ),
				'type'  => Controls_Manager::HEADING,
			)
		);

		$this->add_responsive_control(
			'ham_toggle_width',
			array(
				'label'       => __( 'Button Width', 'trx_addons' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'range'       => array(
					'px' => array(
						'min' => 0,
						'max' => 1000,
					),
				),
				'selectors'   => array(
					'{{WRAPPER}} .trx-addons-hamburger-toggle' => 'width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'ham_toggle_align',
			array(
				'label'     => __( 'Button Alignment', 'trx_addons' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'flex-start'    => array(
						'title' => __( 'Left', 'trx_addons' ),
						'icon'  => 'eicon-h-align-left',
					),
					'center'        => array(
						'title' => __( 'Center', 'trx_addons' ),
						'icon'  => 'eicon-h-align-center',
					),
					'flex-end'      => array(
						'title' => __( 'Right', 'trx_addons' ),
						'icon'  => 'eicon-h-align-right',
					),
				),
				'default'   => 'center',
				'toggle'    => false,
				'selectors' => array(
					'{{WRAPPER}} .trx-addons-hamburger-toggle' => 'align-self: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'ham_toggle_bg',
			array(
				'label'     => __( 'Background Color', 'trx_addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .trx-addons-hamburger-toggle' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'ham_toggle_bg_hover',
			array(
				'label'     => __( 'Hover Background Color', 'trx_addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .trx-addons-hamburger-toggle:hover' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'ham_toggle_shadow',
				'selector' => '{{WRAPPER}} .trx-addons-hamburger-toggle',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'ham_toggle_border',
				'selector' => '{{WRAPPER}} .trx-addons-hamburger-toggle',
			)
		);

		$this->add_control(
			'ham_toggle_rad',
			array(
				'label'      => __( 'Border Radius', 'trx_addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors'  => array(
					'{{WRAPPER}} .trx-addons-hamburger-toggle' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
			)
		);

		$this->add_responsive_control(
			'ham_toggle_padding',
			array(
				'label'      => __( 'Padding', 'trx_addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors'  => array(
					'{{WRAPPER}} .trx-addons-hamburger-toggle' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'ham_toggle_margin',
			array(
				'label'      => __( 'Margin', 'trx_addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors'  => array(
					'{{WRAPPER}} .trx-addons-hamburger-toggle' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->start_controls_tabs( 'ham_toggle_style_tabs' );

		$this->start_controls_tab(
			'ham_toggle_icon_tab',
			array(
				'label' => __( 'Icon', 'trx_addons' ),
			)
		);

		$this->add_responsive_control(
			'ham_toggle_icon_size',
			array(
				'label'       => __( 'Size', 'trx_addons' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors'   => array(
					'{{WRAPPER}} .trx-addons-hamburger-toggle i' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .trx-addons-hamburger-toggle svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'ham_toggle_color',
			array(
				'label'     => __( 'Color', 'trx_addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .trx-addons-hamburger-toggle i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .trx-addons-hamburger-toggle svg path' => 'fill: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'ham_toggle_color_hover',
			array(
				'label'     => __( 'Hover Color', 'trx_addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .trx-addons-hamburger-toggle:hover i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .trx-addons-hamburger-toggle:hover svg path' => 'fill: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'ham_toggle_icon_margin',
			array(
				'label'      => __( 'Margin', 'trx_addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors'  => array(
					'{{WRAPPER}} .trx-addons-hamburger-toggle .trx-addons-toggle-text i,
					 {{WRAPPER}} .trx-addons-hamburger-toggle .trx-addons-toggle-text svg,
					 {{WRAPPER}}.trx-addons-nav-dropdown .trx-addons-hamburger-toggle .trx-addons-toggle-close i,
					 {{WRAPPER}}.trx-addons-nav-dropdown .trx-addons-hamburger-toggle .trx-addons-toggle-close svg,
					 {{WRAPPER}}.trx-addons-ham-dropdown .trx-addons-hamburger-toggle .trx-addons-toggle-close i,
					 {{WRAPPER}}.trx-addons-ham-dropdown .trx-addons-hamburger-toggle .trx-addons-toggle-close svg' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'ham_toggle_label_tab',
			array(
				'label' => __( 'Text', 'trx_addons' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'ham_toggle_txt_typo',
				'selector' => '{{WRAPPER}} .trx-addons-hamburger-toggle .trx-addons-toggle-text,
								{{WRAPPER}}.trx-addons-nav-dropdown .trx-addons-hamburger-toggle .trx-addons-toggle-close,
								{{WRAPPER}}.trx-addons-ham-dropdown .trx-addons-hamburger-toggle .trx-addons-toggle-close',
			)
		);

		$this->add_control(
			'ham_toggle_txt_color',
			array(
				'label'     => __( 'Color', 'trx_addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .trx-addons-hamburger-toggle .trx-addons-toggle-text,
					 {{WRAPPER}}.trx-addons-nav-dropdown .trx-addons-hamburger-toggle .trx-addons-toggle-close,
					 {{WRAPPER}}.trx-addons-ham-dropdown .trx-addons-hamburger-toggle .trx-addons-toggle-close' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'ham_toggle_txt_color_hover',
			array(
				'label'     => __( 'Hover Color', 'trx_addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .trx-addons-hamburger-toggle:hover .trx-addons-toggle-text,
					 {{WRAPPER}}.trx-addons-nav-dropdown .trx-addons-hamburger-toggle:hover .trx-addons-toggle-close,
					 {{WRAPPER}}.trx-addons-ham-dropdown .trx-addons-hamburger-toggle:hover .trx-addons-toggle-close' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'ham_toggle_txt_margin',
			array(
				'label'      => __( 'Margin', 'trx_addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors'  => array(
					'{{WRAPPER}} .trx-addons-hamburger-toggle .trx-addons-toggle-text,
					 {{WRAPPER}}.trx-addons-nav-dropdown .trx-addons-hamburger-toggle .trx-addons-toggle-close,
					 {{WRAPPER}}.trx-addons-ham-dropdown .trx-addons-hamburger-toggle .trx-addons-toggle-close' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		// Toggle menu styles
		$this->add_control(
			'ham_menu_style',
			array(
				'label'     => __( 'Toggle Menu', 'trx_addons' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'ham_menu_overlay',
			array(
				'label'      => __( 'Overlay Color', 'trx_addons' ),
				'type'       => Controls_Manager::COLOR,
				'selectors'  => array(
					'{{WRAPPER}} .trx-addons-nav-slide-overlay' => 'background: {{VALUE}};',
				),
				'conditions' => array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'name'  => 'nav_menu_layout',
							'value' => 'slide',
						),
						array(
							'relation' => 'and',
							'terms'    => array(
								array(
									'name'  => 'mobile_menu_layout',
									'value' => 'slide',
								),
								array(
									'name'     => 'nav_menu_layout',
									'operator' => 'in',
									'value'    => array( 'hor', 'ver' ),
								),
							),
						),
					),
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'ham_menu_shadow',
				'selector' => '{{WRAPPER}}.trx-addons-ham-dropdown .trx-addons-mobile-menu,
								{{WRAPPER}}.trx-addons-nav-dropdown .trx-addons-mobile-menu,
								{{WRAPPER}} .trx-addons-mobile-menu-outer-container',
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'ham_menu_background',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}}.trx-addons-ham-dropdown .trx-addons-mobile-menu,
								{{WRAPPER}}.trx-addons-nav-dropdown .trx-addons-mobile-menu,
								{{WRAPPER}} .trx-addons-mobile-menu-outer-container,
								{{WRAPPER}}:not(.trx-addons-nav-slide):not(.trx-addons-ham-slide) .trx-addons-mobile-menu',	//-container

			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'ham_menu_border',
				'selector' => '{{WRAPPER}}.trx-addons-ham-dropdown .trx-addons-mobile-menu,
								{{WRAPPER}}.trx-addons-nav-dropdown .trx-addons-mobile-menu,
								{{WRAPPER}} .trx-addons-mobile-menu-outer-container',
			)
		);

		$this->add_control(
			'ham_menu_rad',
			array(
				'label'      => __( 'Border Radius', 'trx_addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors'  => array(
					'{{WRAPPER}}.trx-addons-ham-dropdown .trx-addons-mobile-menu,
					 {{WRAPPER}}.trx-addons-nav-dropdown .trx-addons-mobile-menu' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
				'condition'  => array(
					'mobile_menu_layout' => 'dropdown',
				),
			)
		);

		$this->add_responsive_control(
			'ham_menu_padding',
			array(
				'label'      => __( 'Menu Padding', 'trx_addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors'  => array(
					'{{WRAPPER}}.trx-addons-ham-dropdown .trx-addons-mobile-menu,
					 {{WRAPPER}}.trx-addons-nav-dropdown .trx-addons-mobile-menu,
					 {{WRAPPER}} .trx-addons-mobile-menu-outer-container' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'ham_submenu_padding',
			array(
				'label'      => __( 'Submenu Padding', 'trx_addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors'  => array(
					'{{WRAPPER}}.trx-addons-ham-dropdown .trx-addons-mobile-menu .trx-addons-submenu,
					 {{WRAPPER}}.trx-addons-nav-dropdown .trx-addons-mobile-menu .trx-addons-submenu,
					 {{WRAPPER}} .trx-addons-mobile-menu-outer-container .trx-addons-submenu' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'ham_megamenu_padding',
			array(
				'label'      => __( 'Megamenu Padding', 'trx_addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors'  => array(
					'{{WRAPPER}}.trx-addons-ham-dropdown .trx-addons-mobile-menu .trx-addons-mega-content-container,
					 {{WRAPPER}}.trx-addons-nav-dropdown .trx-addons-mobile-menu .trx-addons-mega-content-container,
					 {{WRAPPER}} .trx-addons-mobile-menu-outer-container .trx-addons-mega-content-container' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Get the button 'Close' style.
	 */
	private function register_style_close_button_controls() {

		// Show close button style if desktop or mobile menu is set to slide.
		$this->start_controls_section(
			'ham_close_style_section',
			array(
				'label'      => __( 'Close Button Style', 'trx_addons' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'conditions' => array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'name'  => 'nav_menu_layout',
							'value' => 'slide',
						),
						array(
							'name'  => 'mobile_menu_layout',
							'value' => 'slide',
						),
					),
				),
			)
		);

		$this->add_control(
			'ham_close_bg',
			array(
				'label'     => __( 'Background Color', 'trx_addons' ),
				'type'      => Controls_Manager::COLOR,
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}} .trx-addons-mobile-menu-outer-container .trx-addons-mobile-menu-close' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'ham_close_bg_hover',
			array(
				'label'     => __( 'Hover Background Color', 'trx_addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .trx-addons-mobile-menu-outer-container .trx-addons-mobile-menu-close:hover' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'ham_close_shadow',
				'selector' => '{{WRAPPER}} .trx-addons-mobile-menu-outer-container .trx-addons-mobile-menu-close',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'ham_close_border',
				'selector' => '{{WRAPPER}} .trx-addons-mobile-menu-outer-container .trx-addons-mobile-menu-close',
			)
		);

		$this->add_control(
			'ham_close_rad',
			array(
				'label'      => __( 'Border Radius', 'trx_addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors'  => array(
					'{{WRAPPER}} .trx-addons-mobile-menu-outer-container .trx-addons-mobile-menu-close' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
			)
		);

		$this->add_responsive_control(
			'ham_close_padding',
			array(
				'label'      => __( 'Padding', 'trx_addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'default'    => array(
					'top'    => '0',
					'right'  => '10',
					'bottom' => '0',
					'left'   => '10',
					'unit'   => 'px',
					'isLinked' => false,
				),
				'selectors'  => array(
					'{{WRAPPER}} .trx-addons-mobile-menu-outer-container .trx-addons-mobile-menu-close' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'ham_close_margin',
			array(
				'label'      => __( 'Margin', 'trx_addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'default'    => array(
					'top'    => '5',
					'right'  => '5',
					'bottom' => '5',
					'left'   => '5',
					'unit'   => 'px',
					'isLinked' => true,
				),
				'selectors'  => array(
					'{{WRAPPER}} .trx-addons-mobile-menu-outer-container .trx-addons-mobile-menu-close' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->start_controls_tabs( 'ham_close_style_tabs' );

		$this->start_controls_tab(
			'ham_close_icon_tab',
			array(
				'label' => __( 'Icon', 'trx_addons' ),
			)
		);

		$this->add_responsive_control(
			'ham_close_size',
			array(
				'label'       => __( 'Size', 'trx_addons' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors'   => array(
					'{{WRAPPER}} .trx-addons-mobile-menu-outer-container .trx-addons-mobile-menu-close i' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .trx-addons-mobile-menu-outer-container .trx-addons-mobile-menu-close svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'ham_close_color',
			array(
				'label'     => __( 'Color', 'trx_addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .trx-addons-mobile-menu-outer-container .trx-addons-mobile-menu-close i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .trx-addons-mobile-menu-outer-container .trx-addons-mobile-menu-close svg path' => 'fill: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'ham_close_color_hover',
			array(
				'label'     => __( 'Hover Color', 'trx_addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .trx-addons-mobile-menu-outer-container .trx-addons-mobile-menu-close:hover i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .trx-addons-mobile-menu-outer-container .trx-addons-mobile-menu-close:hover svg path' => 'fill: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'ham_close_txt_tab',
			array(
				'label' => __( 'Text', 'trx_addons' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'ham_close_txt_typo',
				'selector' => '{{WRAPPER}} .trx-addons-mobile-menu-outer-container .trx-addons-mobile-menu-close .trx-addons-toggle-close',
			)
		);

		$this->add_control(
			'ham_close_txt_color',
			array(
				'label'     => __( 'Color', 'trx_addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .trx-addons-mobile-menu-outer-container .trx-addons-mobile-menu-close .trx-addons-toggle-close' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'ham_close_txt_color_hover',
			array(
				'label'     => __( 'Hover Color', 'trx_addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .trx-addons-mobile-menu-outer-container .trx-addons-mobile-menu-close:hover .trx-addons-toggle-close' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'ham_close_txt_margin',
			array(
				'label'      => __( 'Margin', 'trx_addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors'  => array(
					'{{WRAPPER}} .trx-addons-mobile-menu-outer-container .trx-addons-mobile-menu-close .trx-addons-toggle-close' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
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
	 * Render a widget output in the frontend.
	 *
	 * @access protected
	 */
	protected function render() {

		$settings = $this->get_settings_for_display();

		$menu_type = $settings['menu_type'];

		$menu_id = 'wordpress_menu' === $menu_type ? $settings['nav_menus'] : false;

		if ( 'wordpress_menu' === $menu_type ) {

			$is_valid = $this->is_valid_menu( $menu_id );

			if ( ! $is_valid ) {
				?>
				<div class="trx-addons-error-notice">
					<?php echo esc_html( __( 'This is an empty menu. Please make sure your menu has items.', 'trx_addons' ) ); ?>
				</div>
				<?php

				return;
			}
		}

		$breakpoint = '';
		if ( 'custom' === $settings['mobile_menu_breakpoint'] ) {
			$breakpoint = $settings['custom_breakpoint'];
		} else if ( (int)$settings['mobile_menu_breakpoint'] > 0 ) {
			$breakpoint = (int)$settings['mobile_menu_breakpoint'];
		} else {
			$breakpoints = trx_addons_elm_get_breakpoints();
			if ( ! empty( $breakpoints[ $settings['mobile_menu_breakpoint'] ] ) ) {
				$breakpoint = $breakpoints[ $settings['mobile_menu_breakpoint'] ];
			}
		}
		if ( empty( $breakpoint ) ) {
			$breakpoint = '1025';
		}

		$stretch_dropdown = 'yes' === $settings['toggle_full'];

		$close_after_click = 'yes' === $settings['close_after_click'];

		$is_click = 'click' === $settings['ver_toggle_event'] && 'yes' !== $settings['ver_toggle_open'];

		$is_hover = 'hover' === $settings['ver_toggle_event'];

		if ( 'wordpress_menu' === $menu_type ) {

			$menu_list = $this->get_menu_list();

			if ( ! $menu_list ) {
				return;
			}
		}

		$div_end = '';

		$menu_settings = array(
			'breakpoint'      => (int)$breakpoint,
			'mobileLayout'    => $settings['mobile_menu_layout'],
			'mainLayout'      => $settings['nav_menu_layout'],
			'stretchDropdown' => $stretch_dropdown,
			'hoverEffect'     => $settings['sub_badge_hv_effects'],
			'submenuEvent'    => $settings['submenu_event'],
			'submenuTrigger'  => $settings['submenu_trigger'],
			'closeAfterClick' => $close_after_click,
		);

		// if ( 'yes' === $settings['sticky_switcher'] ) {

		// 	$sticky_options = array(
		// 		'targetId'  => $settings['sticky_target'],
		// 		'onScroll'  => 'yes' === $settings['sticky_on_scroll'] ? true : false,
		// 		'disableOn' => $settings['sticky_disabled_on'],
		// 	);

		// 	$menu_settings['stickyOptions'] = $sticky_options;
		// }

		$is_edit_mode = \Elementor\Plugin::$instance->editor->is_edit_mode();

		$this->add_render_attribute(
			'wrapper',
			array(
				'data-settings' => json_encode( $menu_settings ),
				'class'         => array(
					'trx-addons-nav-widget-container',
					'trx-addons-nav-pointer-' . $settings['pointer'],
				),
			)
		);

		if ( 'yes' === $settings['load_hidden'] && ! $is_edit_mode && ! trx_addons_is_preview( 'elementor' ) ) {
			$this->add_render_attribute( 'wrapper', 'style', 'visibility:hidden; opacity:0;' );
		}

		if ( $stretch_dropdown ) {
			$this->add_render_attribute( 'wrapper', 'class', 'trx-addons-stretch-dropdown' );
		}

		if ( 'yes' === $settings['ver_toggle_switcher'] && ( $is_click || $is_hover ) ) {
			$this->add_render_attribute( 'wrapper', 'class', 'trx-addons-ver-collapsed' );
		}

		switch ( $settings['pointer'] ) {
			case 'underline':
			case 'overline':
			case 'double-line':
				$this->add_render_attribute( 'wrapper', 'class', 'trx-addons-nav-animation-' . $settings['animation_line'] );
				break;
			case 'framed':
				$this->add_render_attribute( 'wrapper', 'class', 'trx-addons-nav-animation-' . $settings['animation_framed'] );
				break;
			case 'text':
				$this->add_render_attribute( 'wrapper', 'class', 'trx-addons-nav-animation-' . $settings['animation_text'] );
				break;
			case 'background':
				$this->add_render_attribute( 'wrapper', 'class', 'trx-addons-nav-animation-' . $settings['animation_background'] );
				break;
		}

		/**
		 * Hamburger Menu Button.
		 */
		?>
		<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'wrapper' ) ); ?>>
			<div class="trx-addons-ver-inner-container">
				<div class="trx-addons-hamburger-toggle trx-addons-mobile-menu-icon" role="button" aria-label="Toggle Menu">
					<span class="trx-addons-toggle-text">
						<?php
							echo TrxAddonsUtils::render_icon( $settings['mobile_toggle_icon'] );
							echo esc_html( $settings['mobile_toggle_text'] );
						?>
					</span>
					<span class="trx-addons-toggle-close">
						<?php
							echo TrxAddonsUtils::render_icon( $settings['mobile_close_icon'] );
							echo esc_html( $settings['mobile_toggle_close'] );
						?>
					</span>
				</div>
				<?php

				if ( 'yes' === $settings['ver_toggle_switcher'] ) {
					$this->add_vertical_toggler();
				}

				if ( 'wordpress_menu' === $menu_type ) {
					$args = array(
						'container'   => '',
						'menu'        => $menu_id,
						'menu_class'  => 'trx-addons-nav-menu trx-addons-main-nav-menu',
						'echo'        => false,
						'fallback_cb' => 'wp_page_menu',
						'walker'      => new NavMenuWalker( $settings ),
					);

					$menu_html = wp_nav_menu( $args );

					if ( in_array( $settings['nav_menu_layout'], array( 'hor', 'ver' ), true ) ) {
						?>
						<div class="trx-addons-nav-menu-container trx-addons-nav-default">
							<?php echo $menu_html; ?>
						</div>
						<?php
					}
				} else {
					?>
					<div class="trx-addons-nav-menu-container">
						<ul class="trx-addons-nav-menu trx-addons-main-nav-menu">
							<?php
								$menu_html = $this->get_custom_menu();
								echo $menu_html;
							?>
						</ul>
					</div>
					<?php
				}

				if ( 'slide' === $settings['mobile_menu_layout'] || 'slide' === $settings['nav_menu_layout'] ) {
					$div_end = '</div>';
					?>
					<div class="trx-addons-nav-slide-overlay"></div>
					<div class="trx-addons-mobile-menu-outer-container">
						<div class="trx-addons-mobile-menu-close" role="button" aria-label="Close Menu">
							<?php
							echo TrxAddonsUtils::render_icon( $settings['mobile_close_icon'] );
							if ( ! empty( $settings['mobile_toggle_close'] ) ) {
								?>
								<span class="trx-addons-toggle-close"><?php echo esc_html( $settings['mobile_toggle_close'] ); ?></span>
								<?php
							}
							?>
						</div>
					<?php
					/**
					 * @param int|bool $menu_id WordPress menu id | false if it's a custom menu.
					 * @param array $settings  menu settings.
					 */
					do_action( 'trx_addons_action_slide_menu_top_template', $menu_id, $settings );
				}

				if ( 'wordpress_menu' === $menu_type ) {
					?>
					<div class="trx-addons-mobile-menu-container">
						<?php echo $this->mobile_menu_filter( $menu_html, $menu_id ); ?>
					</div>
					<?php

				} else {
					?>
					<div class="trx-addons-mobile-menu-container">
						<ul class="trx-addons-mobile-menu trx-addons-main-mobile-menu trx-addons-main-nav-menu">
							<?php
								echo $menu_html;
							?>
						</ul>
					</div>
					<?php
				}

				if ( 'slide' === $settings['mobile_menu_layout'] || 'slide' === $settings['nav_menu_layout'] ) {
					do_action( 'trx_addons_action_slide_menu_bottom_template', $menu_id, $settings );
				}

				echo wp_kses_post( $div_end );
				?>
			</div>
		</div>
		<?php
	}

	/**
	 * Add Collapsed Menu.
	 */
	private function add_vertical_toggler() {

		$settings = $this->get_settings_for_display();
		$id       = $this->get_id();

		?>
		<div class="trx-addons-ver-toggler trx-addons-ver-toggler-<?php echo esc_attr( $id ); ?>">
			<div class="trx-addons-ver-toggler-title">
				<?php echo TrxAddonsUtils::render_icon( $settings['ver_toggle_main_icon'], 'trx-addons-ver-title-icon' ); ?>
				<span class="trx-addons-ver-toggler-txt">
					<?php echo esc_html( $settings['ver_toggle_txt'] ); ?>
				</span>
			</div>
			<div class="trx-addons-ver-toggler-btn">
				<?php echo TrxAddonsUtils::render_icon( $settings['ver_toggle_toggle_icon'], 'trx-addons-ver-open' ); ?>
				<?php if ( 'always' !== $settings['ver_toggle_event'] ) : ?>
					<?php echo TrxAddonsUtils::render_icon( $settings['ver_toggle_close_icon'], 'trx-addons-ver-close' ); ?>
				<?php endif; ?>
			</div>
		</div>
		<?php
	}

	/**
	 * Is Valid Menu.
	 *
	 * @param string|int $id  menu id.
	 *
	 * @return bool   true if the menu has items.
	 */
	private function is_valid_menu( $id ) {

		$is_valid = false;
		$menu = wp_get_nav_menu_object( $id );
		$item_count = is_object( $menu ) && ! empty ( $menu->count ) ? $menu->count : 0;

		if ( 0 < $item_count ) {
			$is_valid = true;
		}

		return $is_valid;
	}

	/**
	 * Filters mobile menus.
	 * Changes the menu id to prevent duplicated ids in the DOM.
	 *
	 * @param string     $menu_html  desktop menu html.
	 * @param stirng|int $menu_id    menu id.
	 *
	 * @return string   filtered html.
	 */
	private function mobile_menu_filter( $menu_html, $menu_id ) {

		// Increment the mobile menu id & change its classes to mobile menu classes.
		$slug    = 'menu-' . wp_get_nav_menu_object( $menu_id )->slug;
		if ( preg_match( '/<ul id="(' . $slug . '[\-0-9]*)"/', $menu_html, $matches ) ) {
			$slug = $matches[1];
		}
		$search  = array( 'id="' . $slug . '"', 'class="trx-addons-nav-menu trx-addons-main-nav-menu"' );
		$replace = array( 'id="' . $slug . '-' . mt_rand( 1000, 9999 ) . '"', 'class="trx-addons-mobile-menu trx-addons-main-mobile-menu trx-addons-main-nav-menu"' );

		$menu_html = $this->fix_duplicated_ids( $menu_html, 'trx-addons-nav-menu-item-' ); // fixes's the items duplicated ids.
		$menu_html = $this->fix_duplicated_ids( $menu_html, 'trx-addons-mega-content-' ); // fixes's the items duplicated ids.
		return str_replace( $search, $replace, $menu_html );
	}

	/**
	 * Filters mobile menus.
	 * 
	 * Changes the menu id to prevent duplicated ids in the DOM.
	 *
	 * @param string $menu_html  desktop menu html.
	 * @param string $slug       menu item id.
	 *
	 * @return string  filtered html.
	 */
	private function fix_duplicated_ids( $html, $slug ) {
		$pattern    = '/id="' . $slug . '(\d+)"/';
		$id_counter = 1;

		// Replace duplicated IDs
		return preg_replace_callback(
			$pattern,
			function ( $matches ) use ( &$id_counter, &$slug ) {
				$id     = $matches[1];
				$new_id = $slug . $id . $id_counter++;
				return 'id="' . $new_id . '"';
			},
			$html
		);
	}


	/**
	 * Get Custom Menu.
	 * 
	 * @return string  menu html.
	 */
	private function get_custom_menu() {

		$settings = $this->get_settings_for_display();

		$badge_effect = $settings['sub_badge_hv_effects'];

		$menu_items = $settings['menu_items'];

		$is_submenu = false;
		$i           = 0;
		$is_child    = false;
		$is_link     = false;
		$html_output = '';

		foreach ( $menu_items as $index => $item ) {

			$item_link = $this->get_repeater_setting_key( 'link', 'menu_items', $index );

			if ( ! empty( $item['link']['url'] ) ) {

				$this->add_link_attributes( $item_link, $item['link'] );
			}

			$this->add_render_attribute(
				'menu-item-' . $index,
				array(
					// 'id'    => 'trx-addons-nav-menu-item-' . $item['_id'],
					'class' => array(
						'menu-item',
						'trx-addons-nav-menu-item',
						'elementor-repeater',
						'elementor-repeater-item-' . $item['_id'],
					),
				)
			);

			if ( 'submenu' === $item['item_type'] ) {

				if ( 'link' === $item['menu_content_type'] ) {

					// If no submenu items was rendered before.
					if ( false === $is_child ) {
						$html_output .= "<ul class='trx-addons-submenu'>";
						$is_link      = true;
					}

					$this->add_render_attribute( 'menu-item-' . $index, 'class', 'trx-addons-submenu-item' );

					if ( strpos( $item['link']['url'], trx_addons_get_current_url() ) !== false ) {
						$this->add_render_attribute( 'menu-item-' . $index, 'class', 'trx-addons-active-item' );
					}
	
					if ( 'yes' === $item['badge_switcher'] ) {
						$this->add_render_attribute( 'menu-item-' . $index, 'class', 'has-trx-addons-badge' );

						if ( '' !== $badge_effect ) {
							$this->add_render_attribute( 'menu-item-' . $index, 'class', 'trx-addons-badge-' . $badge_effect );
						}
					}

					$html_output .= '<li ' . $this->get_render_attribute_string( 'menu-item-' . $index ) . '>';

					$html_output .= '<a ' . $this->get_render_attribute_string( $item_link ) . " class='trx-addons-menu-link trx-addons-submenu-link'>";

					$html_output .= $this->get_icon_html( $item, 'sub-' );

					$html_output .= '<span class="trx-addons-menu-link-text trx-addons-submenu-link-text">' . esc_html( $item['text'] ) . ' </span>';

					$html_output .= $this->get_badge_html( $item, 'sub-' );

					$html_output .= '</a>';
					$html_output .= '</li>';

				} else {

					$this->add_render_attribute(
						'menu-content-item-' . $item['_id'],
						array(
							'class' => 'trx-addons-mega-content-container',
						)
					);

					if ( 'yes' === $item['section_position'] ) {
						$this->add_render_attribute( 'menu-content-item-' . $item['_id'], 'class', 'trx-addons-mega-content-centered' );
					}

					if ( 'element' === $item['menu_content_type'] ) {
						$this->add_render_attribute( 'menu-content-item-' . $item['_id'], 'data-mega-content', $item['element_selector'] );
					}

					$html_output .= '<div ' . $this->get_render_attribute_string( 'menu-content-item-' . $item['_id'] ) . '>';

					if ( 'custom_content' === $item['menu_content_type'] ) {

						if ( ! empty( $item['submenu_item'] ) ) {
							$temp_id      = $item['submenu_item'];
							$html_output .= TrxAddonsUtils::get_template_content( $temp_id ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						}

					} else if ( 'layout' === $item['menu_content_type'] ) {

						if ( ! empty( $item['submenu_layout'] ) ) {
							$temp_id      = $item['submenu_layout'];
							$html_output .= trx_addons_cpt_layouts_show_layout( $temp_id, 0, false ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						}
	
					}

					$html_output .= '</div>';

				}

				$is_child    = true;
				$is_submenu = true;

			} else {

				$next_item_exists   = array_key_exists( $index + 1, $menu_items );
				$next_item_is_child = $next_item_exists && 'submenu' === $menu_items[ $index + 1 ]['item_type'];

				if ( $next_item_is_child ) {
					$this->add_render_attribute( 'menu-item-' . $index, 'class', 'menu-item-has-children' );
				}

				if ( 'yes' === $item['badge_switcher'] ) {
					$this->add_render_attribute( 'menu-item-' . $index, 'class', 'has-trx-addons-badge' );
				}

				if ( strpos( $item['link']['url'], trx_addons_get_current_url() ) !== false ) {
					$this->add_render_attribute( 'menu-item-' . $index, 'class', 'trx-addons-active-item' );
				}

				if ( 'yes' === $item['section_full_width'] ) {
					$this->add_render_attribute( 'menu-item-' . $index, 'data-full-width', 'true' );
					$this->add_render_attribute( 'menu-item-' . $index, 'class', 'trx_addons_stretch_' . ( ! empty( $item['section_full_width_wrapper'] ) ? $item['section_full_width_wrapper'] : 'content' ) );
				}

				if ( $next_item_exists ) {
					if ( 'submenu' === $menu_items[ $index + 1 ]['item_type'] && 'link' !== $menu_items[ $index + 1 ]['menu_content_type'] ) {
						$this->add_render_attribute( 'menu-item-' . $index, 'class', 'trx-addons-mega-nav-item' );
					}
				}

				$is_child = false;

				// If we need to create a new main item.
				if ( true === $is_submenu ) {
					$is_submenu = false;

					if ( $is_link ) {
						$html_output .= '</ul>';
						$is_link      = false;
					}

					$html_output .= '</li>';
				}

				$html_output .= '<li ' . $this->get_render_attribute_string( 'menu-item-' . $index ) . '>';

				$html_output .= '<a ' . $this->get_render_attribute_string( $item_link ) . " class='trx-addons-menu-link trx-addons-menu-link-parent'>";

				$html_output .= $this->get_icon_html( $item );

				$html_output .= '<span class="trx-addons-menu-link-text">' . esc_html( $item['text'] ) . '</span>';

				if ( array_key_exists( $index + 1, $menu_items ) ) {
					if ( 'submenu' === $menu_items[ $index + 1 ]['item_type'] && ! empty( $settings['submenu_icon']['value'] ) ) {
						$icon_class   = 'trx-addons-dropdown-icon ' . esc_attr( $settings['submenu_icon']['value'] );
						$html_output .= sprintf( '<i class="%1$s"></i>', $icon_class );
					}
				}

				$html_output .= $this->get_badge_html( $item );

				$html_output .= '</a>';

			}
		}

		return $html_output; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	/**
	 * Get Icon HTML.
	 *
	 * @param array  $item  repeater item.
	 * @param string $type  type.
	 *
	 * @return string  icon html.
	 */
	private function get_icon_html( $item, $type = '' ) {

		$html = '';

		if ( 'yes' !== $item['icon_switcher'] ) {
			return '';
		}

		$class = 'trx-addons-' . $type . 'item-icon ';

		if ( 'icon' === $item['icon_type'] ) {

			$html .= TrxAddonsUtils::render_icon( $item['item_icon'], $class );

		} elseif ( 'image' === $item['icon_type'] ) {

			$html .= '<img class="' . esc_attr( $class ) . ' trx-addons-' . esc_attr( $type ) . 'item-image" src="' . esc_attr( $item['item_image']['url'] ) . '" alt="' . esc_attr( Control_Media::get_image_alt( $item['item_image'] ) ) . '">';

		} else {

			$html .= '<div class="trx-addons-lottie-animation ' . esc_attr( $class ) . '" data-lottie-url="' . esc_attr( $item['lottie_url'] ) . '" data-lottie-loop="true"></div>';

		}

		return $html;
	}

	/**
	 * Get Badge HTML.
	 *
	 * @param array  $item  repeater item.
	 * @param string $type  type.
	 *
	 * @return string  badge html.
	 */
	private function get_badge_html( $item, $type = '' ) {

		if ( 'yes' !== $item['badge_switcher'] ) {
			return '';
		}

		$class = 'trx-addons-' . $type . 'item-badge';

		$html = '<span class="' . esc_attr( $class ) . '">' . wp_kses_post( $item['badge_text'] ) . '</span>';

		return $html;
	}

	/**
	 * Get Menu List.
	 *
	 * @return array  List of menus.
	 */
	private function get_menu_list() {
		return wp_list_pluck( wp_get_nav_menus(), 'name', 'term_id' );  // Convert array to the list with the format: term_id => name
	}
}
