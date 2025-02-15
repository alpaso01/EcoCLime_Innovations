<?php
/**
 * Shortcode: Display a Dark/Light switcher (Elementor support)
 *
 * @package ThemeREX Addons
 * @since v2.27.1
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}


// Elementor Widget
//------------------------------------------------------
if ( ! function_exists( 'trx_addons_sc_layouts_dark_light_add_in_elementor' ) ) {
	add_action( trx_addons_elementor_get_action_for_widgets_registration(), 'trx_addons_sc_layouts_dark_light_add_in_elementor' );
	function trx_addons_sc_layouts_dark_light_add_in_elementor() {
		
		if ( ! class_exists( 'TRX_Addons_Elementor_Layouts_Widget' ) ) return;

		class TRX_Addons_Elementor_Widget_Layouts_Dark_Light extends TRX_Addons_Elementor_Layouts_Widget {

			/**
			 * Widget base constructor.
			 *
			 * Initializing the widget base class.
			 *
			 * @since 1.6.41
			 * @access public
			 *
			 * @param array      $data Widget data. Default is an empty array.
			 * @param array|null $args Optional. Widget default arguments. Default is null.
			 */
			public function __construct( $data = [], $args = null ) {
				parent::__construct( $data, $args );
				$this->add_plain_params( [
					'offset_x' => 'size+unit',
					'offset_x_tablet' => 'size+unit',
					'offset_x_mobile' => 'size+unit',
					'offset_y' => 'size+unit',
					'offset_y_tablet' => 'size+unit',
					'offset_y_mobile' => 'size+unit',
					// 'icon_light' => 'value.url',		// ::ICONS
					'icon_light' => 'url',				// ::MEDIA
					// 'icon_dark' => 'value.url',		// ::ICONS
					'icon_dark' => 'url',				// ::MEDIA
				] );
			}

			/**
			 * Retrieve widget name.
			 *
			 * @since 1.6.41
			 * @access public
			 *
			 * @return string Widget name.
			 */
			public function get_name() {
				return 'trx_sc_layouts_dark_light';
			}

			/**
			 * Retrieve widget title.
			 *
			 * @since 1.6.41
			 * @access public
			 *
			 * @return string Widget title.
			 */
			public function get_title() {
				return __( 'Layouts: Dark/Light switcher', 'trx_addons' );
			}

			/**
			 * Get widget keywords.
			 *
			 * Retrieve the list of keywords the widget belongs to.
			 *
			 * @since 2.27.2
			 * @access public
			 *
			 * @return array Widget keywords.
			 */
			public function get_keywords() {
				return [ 'dark', 'light', 'switcher', 'layouts', 'color', 'scheme' ];
			}

			/**
			 * Retrieve widget icon.
			 *
			 * @since 1.6.41
			 * @access public
			 *
			 * @return string Widget icon.
			 */
			public function get_icon() {
				return 'eicon-adjust trx_addons_elementor_widget_icon';
			}

			/**
			 * Retrieve the list of categories the widget belongs to.
			 *
			 * Used to determine where to display the widget in the editor.
			 *
			 * @since 1.6.41
			 * @access public
			 *
			 * @return array Widget categories.
			 */
			public function get_categories() {
				return ['trx_addons-layouts'];
			}

			/**
			 * Register widget controls.
			 *
			 * Adds different input fields to allow the user to change and customize the widget settings.
			 *
			 * @since 1.6.41
			 * @access protected
			 */
			protected function register_controls() {

				// Detect edit mode
				$is_edit_mode = trx_addons_elm_is_edit_mode();

				$this->start_controls_section(
					'section_sc_layouts_dark_light',
					[
						'label' => __( 'Layouts: Dark/Light switcher', 'trx_addons' ),
					]
				);

				$this->add_control(
					'type',
					[
						'label' => __( 'Layout', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => apply_filters('trx_addons_sc_type', trx_addons_get_list_sc_dark_light_layouts(), 'trx_sc_layouts_dark_light'),
						'default' => 'default'
					]
				);

				$this->add_control(
					'permanent',
					[
						'label' => __( 'For whole site', 'trx_addons' ),
						'label_block' => false,
						'description' => wp_kses_data( __("Apply the selected scheme for whole site or for the current page only", 'trx_addons') ),
						'type' => \Elementor\Controls_Manager::SWITCHER,
						'return_value' => '1'
					]
				);

				$this->add_control(
					'effect',
					[
						'label' => __( 'Effect', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => trx_addons_get_list_sc_dark_light_effects(),
						'default' => 'slide'
					]
				);

				$this->add_responsive_control(
					'position',
					[
						'label' => __( 'Position', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => trx_addons_get_list_sc_fixed_positions(),
						'default' => 'static',
						'prefix_class' => 'sc_layouts_dark_light_position%s_'
					]
				);

				$this->add_responsive_control(
					'offset_x',
					[
						'label' => __( 'Horizontal offset', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::SLIDER,
						'default' => [
							'size' => 0,
							'unit' => 'px'
						],
						'size_units' => ['px', 'em'],
						'range' => [
							'px' => [
								'min' => 0,
								'max' => 200
							],
							'em' => [
								'min' => 0,
								'max' => 20
							]
							],
							'condition' => [
								'position!' => 'static'
							],
					]
				);

				$this->add_responsive_control(
					'offset_y',
					[
						'label' => __( 'Vertical offset', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::SLIDER,
						'default' => [
							'size' => 0,
							'unit' => 'px'
						],
						'size_units' => ['px', 'em'],
						'range' => [
							'px' => [
								'min' => 0,
								'max' => 200
							],
							'em' => [
								'min' => 0,
								'max' => 20
							]
						],
						'condition' => [
							'position!' => 'static'
						],
					]
				);

				$this->add_control(
					'schemes_light',
					[
						'label' => __( 'Light mode', 'trx_addons' ),
						'label_block' => true,
						'type' => \Elementor\Controls_Manager::REPEATER,
						'default' => apply_filters('trx_addons_sc_param_group_value', [
							[
								'area' => 'content',
								'scheme' => 'default',
								'selector' => 'html,body'
							],
							[
								'area' => 'header',
								'scheme' => 'default',
								'selector' => '.top_panel'
							],
							[
								'area' => 'footer',
								'scheme' => 'default',
								'selector' => '.footer_wrap'
							],
							[
								'area' => 'sidebar',
								'scheme' => 'default',
								'selector' => '.sidebar'
							],
						], 'trx_sc_layouts_dark_light'),
						'fields' => apply_filters('trx_addons_sc_param_group_params', [
							[
								'name' => 'area',
								'label' => __( 'Area', 'trx_addons' ),
								'label_block' => false,
								'type' => \Elementor\Controls_Manager::SELECT,
								'options' => ! $is_edit_mode ? array() : trx_addons_get_list_color_scheme_areas(),
								'default' => 'content'
							],
							[
								'name' => 'scheme',
								'label' => __( 'Color Scheme', 'trx_addons' ),
								'label_block' => false,
								'type' => \Elementor\Controls_Manager::SELECT,
								'options' => ! $is_edit_mode ? array() : trx_addons_get_list_color_schemes(),
								'default' => 'default'
							],
							[
								'name' => 'selector',
								'label' => __( 'CSS Selector', 'trx_addons' ),
								'label_block' => false,
								'type' => \Elementor\Controls_Manager::TEXT,
								'placeholder' => __( 'CSS selector for the specified area', 'trx_addons' ),
								'default' => ''
							],
						], 'trx_sc_layouts_dark_light' ),
						'title_field' => '{{{ area }}} - {{{ scheme }}}'
					]
				);

				$this->add_control(
					'schemes_dark',
					[
						'label' => __( 'Dark mode', 'trx_addons' ),
						'label_block' => true,
						'type' => \Elementor\Controls_Manager::REPEATER,
						'default' => apply_filters('trx_addons_sc_param_group_value', [
							[
								'area' => 'content',
								'scheme' => 'dark',
								'selector' => 'html,body'
							],
							[
								'area' => 'header',
								'scheme' => 'dark',
								'selector' => '.top_panel'
							],
							[
								'area' => 'footer',
								'scheme' => 'dark',
								'selector' => '.footer_wrap'
							],
							[
								'area' => 'sidebar',
								'scheme' => 'dark',
								'selector' => '.sidebar'
							],
						], 'trx_sc_layouts_dark_light'),
						'fields' => apply_filters('trx_addons_sc_param_group_params', [
							[
								'name' => 'area',
								'label' => __( 'Area', 'trx_addons' ),
								'label_block' => false,
								'type' => \Elementor\Controls_Manager::SELECT,
								'options' => ! $is_edit_mode ? array() : trx_addons_get_list_color_scheme_areas(),
								'default' => 'content'
							],
							[
								'name' => 'scheme',
								'label' => __( 'Color Scheme', 'trx_addons' ),
								'label_block' => false,
								'type' => \Elementor\Controls_Manager::SELECT,
								'options' => ! $is_edit_mode ? array() : trx_addons_get_list_color_schemes(),
								'default' => 'dark'
							],
							[
								'name' => 'selector',
								'label' => __( 'CSS Selector', 'trx_addons' ),
								'label_block' => false,
								'type' => \Elementor\Controls_Manager::TEXT,
								'placeholder' => __( 'CSS selector for the specified area', 'trx_addons' ),
								'default' => ''
							],
						], 'trx_sc_layouts_dark_light' ),
						'title_field' => '{{{ area }}} - {{{ scheme }}}'
					]
				);

				$this->end_controls_section();

				$this->start_controls_section( 'section_sc_layouts_dark_light_style', array(
					'tab' => \Elementor\Controls_Manager::TAB_STYLE,
					'label' => __( 'Colors', 'trx_addons' )
				) );

				$this->add_control( "light_separator", array(
					'label' => __( 'Light mode', 'trx_addons' ),
					'type' => \Elementor\Controls_Manager::HEADING,
				) );

				$this->add_control( 'icon_light',
					[
						'label' => esc_html__( 'Icon', 'elementor' ),
						'type' => \Elementor\Controls_Manager::MEDIA,
						'media_types' => [ 'image', 'svg' ],
						// 'should_include_svg_inline_option' => true,
						// 'library_type' => 'image/svg+xml',
						// 'dynamic' => [
						// 	'active' => true,
						// ],
					]
				);
		
				// $this->add_control( 'icon_light', array(
				// 	'type' => \Elementor\Controls_Manager::ICONS,
				// 	'label' => esc_html__( 'Icon', 'trx_addons' ),
				// 	'label_block' => false,
				// 	'fa4compatibility' => 'icon',
				// 	'default' => array(
				// 		'value' => '',
				// 		'library' => 'svg'
				// 	),
				// 	'exclude_inline_options' => array( 'icon' ),
				// 	'skin' => 'inline',
				// ) );
	
				$this->add_control( 'icon_color_light', array(
					'label' => __( 'Icon color', 'trx_addons' ),
					'label_block' => false,
					'type' => \Elementor\Controls_Manager::COLOR,
					'default' => '',
					'selectors' => [
						'{{WRAPPER}} .sc_layouts_dark_light_light svg *' => 'fill: {{VALUE}};',
					],
					// 'global' => array(
					// 	'active' => false,
					// ),
				) );

				$this->add_control( 'bg_color_light', array(
					'label' => __( 'Bg color', 'trx_addons' ),
					'label_block' => false,
					'type' => \Elementor\Controls_Manager::COLOR,
					'default' => '',
					'selectors' => [
						'{{WRAPPER}} .sc_layouts_dark_light_active_light' => 'background-color: {{VALUE}};',
					],
					// 'global' => array(
					// 	'active' => false,
					// ),
				) );

				$this->add_control( 'bd_color_light', array(
					'label' => __( 'Border color', 'trx_addons' ),
					'label_block' => false,
					'type' => \Elementor\Controls_Manager::COLOR,
					'default' => '',
					'selectors' => [
						'{{WRAPPER}} .sc_layouts_dark_light_active_light' => 'border-color: {{VALUE}};',
					],
					// 'global' => array(
					// 	'active' => false,
					// ),
				) );

				$this->add_control( "dark_separator", array(
					'label' => __( 'Dark mode', 'trx_addons' ),
					'separator' => 'before',
					'type' => \Elementor\Controls_Manager::HEADING,
				) );

				$this->add_control( 'icon_dark',
					[
						'label' => esc_html__( 'Icon', 'elementor' ),
						'type' => \Elementor\Controls_Manager::MEDIA,
						'media_types' => [ 'image', 'svg' ],
						// 'should_include_svg_inline_option' => true,
						// 'library_type' => 'image/svg+xml',
						// 'dynamic' => [
						// 	'active' => true,
						// ],
					]
				);

				// $this->add_control( 'icon_dark', array(
				// 	'type' => \Elementor\Controls_Manager::ICONS,
				// 	'label' => esc_html__( 'Icon', 'trx_addons' ),
				// 	'label_block' => false,
				// 	'fa4compatibility' => 'icon',
				// 	'default' => array(
				// 		'value' => '',
				// 		'library' => 'svg'
				// 	),
				// 	'exclude_inline_options' => array( 'icon' ),
				// 	'skin' => 'inline',
				// ) );

				$this->add_control( 'icon_color_dark', array(
					'label' => __( 'Icon color', 'trx_addons' ),
					'label_block' => false,
					'type' => \Elementor\Controls_Manager::COLOR,
					'default' => '',
					'selectors' => [
						'{{WRAPPER}} .sc_layouts_dark_light_dark svg *' => 'fill: {{VALUE}};',
					],
					// 'global' => array(
					// 	'active' => false,
					// ),
				) );

				$this->add_control( 'bg_color_dark', array(
					'label' => __( 'Bg color', 'trx_addons' ),
					'label_block' => false,
					'type' => \Elementor\Controls_Manager::COLOR,
					'default' => '',
					'selectors' => [
						'{{WRAPPER}} .sc_layouts_dark_light_active_dark' => 'background-color: {{VALUE}};',
					],
					// 'global' => array(
					// 	'active' => false,
					// ),
				) );

				$this->add_control( 'bd_color_dark', array(
					'label' => __( 'Border color', 'trx_addons' ),
					'label_block' => false,
					'type' => \Elementor\Controls_Manager::COLOR,
					'default' => '',
					'selectors' => [
						'{{WRAPPER}} .sc_layouts_dark_light_active_dark' => 'border-color: {{VALUE}};',
					],
					// 'global' => array(
					// 	'active' => false,
					// ),
				) );

				$this->end_controls_section();
			}

			/**
			 * Render widget's template for the editor.
			 *
			 * Written as a Backbone JavaScript template and used to generate the live preview.
			 *
			 * @since 1.6.41
			 * @access protected
			 */
			protected function content_template() {
				trx_addons_get_template_part( TRX_ADDONS_PLUGIN_CPT_LAYOUTS_SHORTCODES . "dark_light/tpe.dark_light.php",
										'trx_addons_args_sc_layouts_dark_light',
										array( 'element' => $this )
									);
			}
		}
		
		// Register widget
		trx_addons_elm_register_widget( 'TRX_Addons_Elementor_Widget_Layouts_Dark_Light' );
	}
}
