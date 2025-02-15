<?php
/**
 * Shortcode: Display Search form (Elementor support)
 *
 * @package ThemeREX Addons
 * @since v1.6.08
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Text_Shadow;


// Elementor Widget
//------------------------------------------------------
if ( ! function_exists( 'trx_addons_sc_layouts_search_add_in_elementor' ) ) {
	add_action( trx_addons_elementor_get_action_for_widgets_registration(), 'trx_addons_sc_layouts_search_add_in_elementor' );
	function trx_addons_sc_layouts_search_add_in_elementor() {
		
		if ( ! class_exists( 'TRX_Addons_Elementor_Layouts_Widget' ) ) return;	

		class TRX_Addons_Elementor_Widget_Layouts_Search extends TRX_Addons_Elementor_Layouts_Widget {

			/**
			 * Widget base constructor.
			 *
			 * Initializing the widget base class.
			 *
			 * @since 2.31.3
			 * @access public
			 *
			 * @param array      $data Widget data. Default is an empty array.
			 * @param array|null $args Optional. Widget default arguments. Default is null.
			 */
			public function __construct( $data = array(), $args = null ) {
				parent::__construct( $data, $args );
				$this->add_plain_params( array(
					'icon' => 'value',
					'icon_close' => 'value',
				) );
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
				return 'trx_sc_layouts_search';
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
				return __( 'Search Form', 'trx_addons' );
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
				return [ 'search', 'form', 'layouts' ];
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
				return 'eicon-search trx_addons_elementor_widget_icon';
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

				$this->register_content_controls();

				if ( apply_filters( 'trx_addons_filter_allow_sc_styles_in_elementor', false, 'sc_layouts_search' ) ) {
					$this->register_style_controls_form();
					$this->register_style_controls_icon();
					$this->register_style_controls_button();
					$this->register_style_controls_close_button();
					$this->register_style_controls_close_label();
					$this->register_style_controls_overlay();
					$this->register_style_controls_results();
				}
			}

			/**
			 * Register content controls.
			 */
			protected function register_content_controls() {
				// Detect edit mode
				$is_edit_mode = trx_addons_elm_is_edit_mode();

				// Register controls
				$post_types = ! $is_edit_mode ? array() : trx_addons_get_list_posts_types();

				$this->start_controls_section(
					'section_sc_layouts_search',
					[
						'label' => __( 'Layouts: Search', 'trx_addons' ),
					]
				);

				$this->add_control(
					'type',
					[
						'label' => __( 'Layout', 'trx_addons' ),
						'label_block' => false,
						'type' => Controls_Manager::SELECT,
						'options' => apply_filters('trx_addons_sc_type', array(
								'default' => esc_html__('Default', 'trx_addons'),
							), 'trx_sc_layouts_search'),
						'default' => 'default'
					]
				);

				$this->add_control(
					'style',
					[
						'label' => __( 'Style', 'trx_addons' ),
						'label_block' => false,
						'type' => Controls_Manager::SELECT,
						'options' => ! $is_edit_mode ? array() : apply_filters('trx_addons_sc_style', trx_addons_get_list_sc_layouts_search(), 'trx_sc_layouts_search'),
						'default' => 'normal'
					]
				);

				$this->add_control(
					'ajax',
					[
						'label' => __( 'AJAX search', 'trx_addons' ),
						'label_block' => false,
						'type' => Controls_Manager::SWITCHER,
						'label_off' => __( 'Off', 'trx_addons' ),
						'label_on' => __( 'On', 'trx_addons' ),
						'return_value' => '1'
					]
				);

				$this->add_control(
					'post_types',
					[
						'label' => __( 'Search in post types', 'trx_addons' ),
						'label_block' => false,
						'type' => Controls_Manager::SELECT2,
						'options' => $post_types,
						'multiple' => true,
						'default' => ''
					]
				);

				$this->add_control(
					'placeholder_text',
					array(
						'label'     => __( 'Placeholder Text', 'trx_addons' ),
						'label_block' => false,
						'default'   => __( 'Search', 'trx_addons' ),
						'type'      => Controls_Manager::TEXT,
					)
				);

				$this->add_control(
					'close_label_text',
					array(
						'label'     => __( 'Close Label Text', 'trx_addons' ),
						'label_block' => false,
						'default'   => '',
						'type'      => Controls_Manager::TEXT,
						'condition' => array(
							'style' => array( 'fullscreen' ),
						),
					)
				);

				$this->add_control(
					'search_opened',
					array(
						'label'       => __( 'Search Form Preview', 'trx_addons' ),
						'label_block' => false,
						'description' => wp_kses_data( __("Display the search form for preview (for editor only)", 'trx_addons') ),
						'type'        => Controls_Manager::SWITCHER,
						'return_value' => '1',
						'condition'   => array(
							'style' => array( 'expand', 'fullscreen' ),
						),
					)
				);

				$this->end_controls_section();
			}

			/**
			 * Register style controls for the form 'Search'
			 */
			protected function register_style_controls_form() {
				$this->start_controls_section(
					'section_sc_layouts_search_style_field',
					[
						'label' => __( 'Search Form', 'trx_addons' ),
						'tab' => Controls_Manager::TAB_STYLE,
					]
				);

				$this->add_control(
					'field_heading_text',
					array(
						'label'     => __( 'Text', 'trx_addons' ),
						'type'      => Controls_Manager::HEADING,
						// 'separator' => 'after',
					)
				);

				$this->add_group_control(
					Group_Control_Typography::get_type(),
					array(
						'name'     => 'field_typography',
						'selector' => '{{WRAPPER}} .search_style_normal .search_field,
										{{WRAPPER}} .search_style_expand.search_opened .search_field,
										{{WRAPPER}} .search_style_fullscreen.search_opened .search_field',
					)
				);

				$this->add_control(
					'field_alignment',
					array(
						'label'     => __( 'Text Alignment', 'trx_addons' ),
						'label_block' => false,
						'type'      => Controls_Manager::CHOOSE,
						'options'   => array(
							'left'   => array(
								'title' => __( 'Left', 'trx_addons' ),
								'icon'  => 'eicon-text-align-left',
							),
							'center' => array(
								'title' => __( 'Center', 'trx_addons' ),
								'icon'  => 'eicon-text-align-center',
							),
							'right'  => array(
								'title' => __( 'Right', 'trx_addons' ),
								'icon'  => 'eicon-text-align-right',
							),
						),
						'default'   => 'left',
						'toggle'    => false,
						'selectors' => array(
							'{{WRAPPER}} .search_style_normal .search_field,
							 {{WRAPPER}} .search_style_expand.search_opened .search_field,
							 {{WRAPPER}} .search_style_fullscreen.search_opened .search_field' => 'text-align: {{VALUE}}',
						),
					)
				);

				$this->start_controls_tabs( 'tabs_field_styles' );

				$this->start_controls_tab(
					'tab_field_style_normal',
					array(
						'label' => __( 'Normal', 'trx_addons' ),
					)
				);

				$this->add_control(
					'field_placeholder_color',
					array(
						'label'     => __( 'Placeholder Color', 'trx_addons' ),
						'type'      => Controls_Manager::COLOR,
						'selectors' => array(
							'{{WRAPPER}} .search_style_normal .search_field::placeholder,
							 {{WRAPPER}} .search_style_expand.search_opened .search_field::placeholder,
							 {{WRAPPER}} .search_style_fullscreen.search_opened .search_field::placeholder' => 'color: {{VALUE}};',
						),
					)
				);

				$this->add_control(
					'field_color',
					array(
						'label'     => __( 'Text Color', 'trx_addons' ),
						'type'      => Controls_Manager::COLOR,
						'selectors' => array(
							'{{WRAPPER}} .search_style_normal .search_field,
							 {{WRAPPER}} .search_style_expand.search_opened .search_field,
							 {{WRAPPER}} .search_style_fullscreen.search_opened .search_field' => 'color: {{VALUE}};',
						),
					)
				);
		
				$this->end_controls_tab();
		
				$this->start_controls_tab(
					'tab_field_style_focus',
					array(
						'label' => __( 'Focused', 'trx_addons' ),
					)
				);

				$this->add_control(
					'field_placeholder_color_focus',
					array(
						'label'     => __( 'Placeholder Color', 'trx_addons' ),
						'type'      => Controls_Manager::COLOR,
						'selectors' => array(
							'{{WRAPPER}} .search_style_normal .search_field:focus::placeholder,
							 {{WRAPPER}} .search_style_expand.search_opened .search_field:focus::placeholder,
							 {{WRAPPER}} .search_style_fullscreen.search_opened .search_field:focus::placeholder' => 'color: {{VALUE}};',
						),
					)
				);

				$this->add_control(
					'field_color_focus',
					array(
						'label'     => __( 'Text Color', 'trx_addons' ),
						'type'      => Controls_Manager::COLOR,
						'selectors' => array(
							'{{WRAPPER}} .search_style_normal .search_field:focus,
							 {{WRAPPER}} .search_style_expand.search_opened .search_field:focus,
							 {{WRAPPER}} .search_style_fullscreen.search_opened .search_field:focus' => 'color: {{VALUE}};',
						),
					)
				);

				$this->end_controls_tab();

				$this->end_controls_tabs();

				$this->add_control(
					'field_heading_form',
					array(
						'label'     => __( 'Form', 'trx_addons' ),
						'type'      => Controls_Manager::HEADING,
						'separator' => 'before',
					)
				);

				$this->add_responsive_control(
					'field_width',
					array(
						'label'     => __( 'Form Width', 'trx_addons' ),
						'type'      => Controls_Manager::SLIDER,
						'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
						'range'     => array(
							'px' => array(
								'min' => 50,
								'max' => 500,
							),
							'em' => array(
								'min' => 5,
								'max' => 50,
								'step' => 0.1,
							),
						),
						'selectors' => array(
							'{{WRAPPER}} .search_style_normal .search_field,
							 {{WRAPPER}} .search_style_expand.search_opened .search_field,
							 {{WRAPPER}} .search_style_fullscreen.search_opened .search_form' => 'width: {{SIZE}}{{UNIT}}',
						),
					)
				);
	
				$this->add_responsive_control(
					'field_height',
					array(
						'label'     => __( 'Form Height', 'trx_addons' ),
						'type'      => Controls_Manager::SLIDER,
						'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
						'selectors' => array(
							'{{WRAPPER}} .search_style_fullscreen.search_opened .search_field' => 'height: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}};',
						),
						'condition' => array(
							'style' => array( 'fullscreen' ),
						),
					)
				);

				$this->start_controls_tabs( 'tabs_form_styles' );

				$this->start_controls_tab(
					'tab_form_style_normal',
					array(
						'label' => __( 'Normal', 'trx_addons' ),
					)
				);

				$this->add_group_control(
					Group_Control_Background::get_type(),
					array(
						'name'     => 'field_bg',
						'types'    => array( 'classic', 'gradient' ),
						'selector' => '{{WRAPPER}} .search_style_normal .search_field,
									   {{WRAPPER}} .search_style_expand.search_opened .search_field,
									   {{WRAPPER}} .search_style_fullscreen.search_opened .search_field',
					)
				);
		
				$this->add_group_control(
					Group_Control_Border::get_type(),
					array(
						'name'     => 'field_border',
						'selector' => '{{WRAPPER}} .search_style_normal .search_field,
										{{WRAPPER}} .search_style_expand.search_opened .search_field,
										{{WRAPPER}} .search_style_fullscreen.search_opened .search_field',
					)
				);
		
				$this->add_control(
					'field_radius',
					array(
						'label'      => __( 'Border Radius', 'trx_addons' ),
						'type'       => Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
						'selectors'  => array(
							'{{WRAPPER}} .search_style_normal .search_field,
							 {{WRAPPER}} .search_style_expand.search_opened .search_field,
							 {{WRAPPER}} .search_style_fullscreen.search_opened .search_field' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
						),
					)
				);

				$this->add_group_control(
					Group_Control_Box_Shadow::get_type(),
					array(
						'name'     => 'field_shadow',
						'selector' => '{{WRAPPER}} .search_style_normal .search_field,
										{{WRAPPER}} .search_style_expand.search_opened .search_field,
										{{WRAPPER}} .search_style_fullscreen.search_opened .search_field',
					)
				);
		
				$this->end_controls_tab();
		
				$this->start_controls_tab(
					'tab_form_style_focus',
					array(
						'label' => __( 'Focused', 'trx_addons' ),
					)
				);

				$this->add_group_control(
					Group_Control_Background::get_type(),
					array(
						'name'           => 'field_bg_focus',
						'types'          => array( 'classic', 'gradient' ),
						'selector'       => '{{WRAPPER}} .search_style_normal .search_field:focus,
											 {{WRAPPER}} .search_style_expand.search_opened .search_field:focus,
											 {{WRAPPER}} .search_style_fullscreen.search_opened .search_field:focus',
						'fields_options' => array(
							'background' => array(
								'default' => 'classic',
							),
							// 'color'      => array(
							// 	'global' => array(
							// 		'default' => Global_Colors::COLOR_SECONDARY,
							// 	),
							// ),
						),
					)
				);

				$this->add_control(
					'field_border_color_focus',
					array(
						'label'     => __( 'Border Color', 'trx_addons' ),
						'type'      => Controls_Manager::COLOR,
						'selectors' => array(
							'{{WRAPPER}} .search_style_normal .search_field:focus,
							 {{WRAPPER}} .search_style_expand.search_opened .search_field:focus,
							 {{WRAPPER}} .search_style_fullscreen.search_opened .search_field:focus' => 'border-color: {{VALUE}};',
						),
					)
				);

				$this->add_group_control(
					Group_Control_Box_Shadow::get_type(),
					array(
						'name'     => 'field_shadow_focus',
						'selector' => '{{WRAPPER}} .search_style_normal .search_field:focus,
										{{WRAPPER}} .search_style_expand.search_opened .search_field:focus,
										{{WRAPPER}} .search_style_fullscreen.search_opened .search_field:focus',
					)
				);

				$this->end_controls_tab();

				$this->end_controls_tabs();

				$this->add_responsive_control(
					'field_padding',
					array(
						'label'      => __( 'Padding', 'trx_addons' ),
						'type'       => Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
						'selectors'  => array(
							'{{WRAPPER}} .search_style_normal .search_field,
							 {{WRAPPER}} .search_style_expand.search_opened .search_field,
							 {{WRAPPER}} .search_style_fullscreen.search_opened .search_field' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};', // !important;',
						),
						'separator'  => 'before',
					)
				);

				$this->end_controls_section();
			}

			/**
			 * Register style controls for the icon 'Search' in the opened form
			 */
			protected function register_style_controls_icon() {
				$this->start_controls_section(
					'section_sc_layouts_search_style_icon',
					[
						'label' => __( 'Search Icon', 'trx_addons' ),
						'tab' => Controls_Manager::TAB_STYLE,
					]
				);

				$this->add_control(
					'icon_hidden',
					array(
						'label'        => __( 'Hide icon in form', 'trx_addons' ),
						'label_block'  => false,
						'type'         => Controls_Manager::SWITCHER,
						'default'	   => '',
						'return_value' => '1',
					)
				);

				// $icon_param = trx_addons_array_get_first_value( $this->get_icon_param() );
				// unset( $icon_param['name'] );
				// $icon_param['condition'] = array(
				// 	'icon_hidden' => '',
				// );
				// $this->add_control( 'icon', $icon_param );

				$this->add_control(
					'icon',
					[
						'label' => esc_html__( 'Icon', 'trx_addons' ),
						'type' => Controls_Manager::ICONS,
						// 'default' => [
						// 	'value' => 'fas fa-search',
						// 	'library' => 'fa-solid',
						// ],
						'recommended' => [
							'fa-solid' => [
								'search',
								'search-dollar',
								'search-location',
								'search-minus',
								'search-plus',
							],
							'fa-brands' => [
								'searchengin',
							],
						],
						// 'condition' => [
						// 	'icon_hidden' => ''
						// ],
					]
				);

				$this->add_control(
					'icon_halign',
					array(
						'label'     => __( 'Position', 'trx_addons' ),
						'type'      => Controls_Manager::CHOOSE,
						'options'   => array(
							'left' => array(
								'title' => __( 'Left', 'trx_addons' ),
								'icon'  => 'eicon-h-align-left',
							),
							'right' => array(
								'title' => __( 'Right', 'trx_addons' ),
								'icon'  => 'eicon-h-align-right',
							),
						),
						'default'   => '',
						// 'toggle'    => false,
						'condition' => array(
							'icon_hidden' => '',
						),
					)
				);

				$this->add_control(
					'icon_valign',
					array(
						'label'     => __( 'Vertical Position', 'trx_addons' ),
						'type'      => Controls_Manager::CHOOSE,
						'options'   => array(
							'top' => array(
								'title' => __( 'Top', 'trx_addons' ),
								'icon'  => 'eicon-v-align-top',
							),
							'center'     => array(
								'title' => __( 'Center', 'trx_addons' ),
								'icon'  => 'eicon-v-align-middle',
							),
							'bottom' => array(
								'title' => __( 'Bottom', 'trx_addons' ),
								'icon'  => 'eicon-v-align-bottom',
							),
						),
						'default'   => '',
						// 'toggle'    => false,
						'condition' => array(
							'icon_hidden' => '',
						),
					)
				);

				$this->add_responsive_control(
					'icon_size',
					array(
						'label'      => __( 'Icon Size', 'trx_addons' ),
						'type'       => Controls_Manager::SLIDER,
						'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
						'selectors'  => array(
							'{{WRAPPER}} .search_style_normal .search_submit,
							 {{WRAPPER}} .search_style_expand.search_opened .search_submit,
							 {{WRAPPER}} .search_style_fullscreen.search_opened :where(.search_form_wrap) .search_submit' => 'font-size: {{SIZE}}{{UNIT}};',
						),
						'condition' => array(
							'icon_hidden' => '',
						),
					)
				);

				$this->add_responsive_control(
					'icon_box_size',
					array(
						'label'      => __( 'Icon Box Size', 'trx_addons' ),
						'type'       => Controls_Manager::SLIDER,
						'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
						'default'   => [
							'size' => '',
							'unit' => 'px',
						],
						'selectors'  => array(
							'{{WRAPPER}} .search_style_normal .search_submit,
							 {{WRAPPER}} .search_style_expand.search_opened .search_submit,
							 {{WRAPPER}} .search_style_fullscreen.search_opened :where(.search_form_wrap) .search_submit' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}}; text-align: center; box-sizing: content-box;',
							'{{WRAPPER}} .search_style_normal.search_ajax.search_icon_valign_center .search_submit,
							 {{WRAPPER}} .search_style_expand.search_opened.search_ajax.search_icon_valign_center .search_submit,
							 {{WRAPPER}} .search_style_fullscreen.search_opened.search_ajax.search_icon_valign_center :where(.search_form_wrap) .search_submit' => 'margin-top: calc( -1 * ( {{SIZE}}{{UNIT}} + {{icon_padding.TOP}}{{icon_padding.UNIT}} + {{icon_padding.BOTTOM}}{{icon_padding.UNIT}} ) / 2 );',
						),
						'condition' => array(
							'icon_hidden' => '',
						),
					)
				);

				$this->start_controls_tabs(
					'tabs_icon_styles',
					array(
						'condition' => array(
							'icon_hidden' => '',
						),
					)
				);

				$this->start_controls_tab(
					'tab_icon_style_normal',
					array(
						'label' => __( 'Normal', 'trx_addons' ),
					)
				);

				$this->add_control(
					'icon_color',
					array(
						'label'     => __( 'Icon Color', 'trx_addons' ),
						'type'      => Controls_Manager::COLOR,
						'selectors' => array(
							'{{WRAPPER}} .search_style_normal .search_submit:before,
							 {{WRAPPER}} .search_style_expand.search_opened .search_submit:before,
							 {{WRAPPER}} .search_style_fullscreen.search_opened :where(.search_form_wrap) .search_submit:before' => 'color: {{VALUE}};',
							'{{WRAPPER}} .search_style_normal .search_submit path,
							 {{WRAPPER}} .search_style_expand.search_opened .search_submit path,
							 {{WRAPPER}} .search_style_fullscreen.search_opened :where(.search_form_wrap) .search_submit path' => 'fill: {{VALUE}};',
						),
					)
				);

				$this->add_group_control(
					Group_Control_Background::get_type(),
					array(
						'name'     => 'icon_bg',
						'types'    => array( 'classic', 'gradient' ),
						'selector' => '{{WRAPPER}} .search_style_normal .search_submit,
										{{WRAPPER}} .search_style_expand.search_opened .search_submit,
										{{WRAPPER}} .search_style_fullscreen.search_opened :where(.search_form_wrap) .search_submit',
					)
				);
		
				$this->add_group_control(
					Group_Control_Border::get_type(),
					array(
						'name'     => 'icon_border',
						'selector' => '{{WRAPPER}} .search_style_normal .search_submit,
										{{WRAPPER}} .search_style_expand.search_opened .search_submit,
										{{WRAPPER}} .search_style_fullscreen.search_opened :where(.search_form_wrap) .search_submit',
					)
				);
		
				$this->add_control(
					'icon_radius',
					array(
						'label'      => __( 'Border Radius', 'trx_addons' ),
						'type'       => Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
						'selectors'  => array(
							'{{WRAPPER}} .search_style_normal .search_submit,
							 {{WRAPPER}} .search_style_expand.search_opened .search_submit,
							 {{WRAPPER}} .search_style_fullscreen.search_opened :where(.search_form_wrap) .search_submit' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
						),
					)
				);

				$this->add_group_control(
					Group_Control_Box_Shadow::get_type(),
					array(
						'name'     => 'icon_box_shadow',
						'selector' => '{{WRAPPER}} .search_style_normal .search_submit,
										{{WRAPPER}} .search_style_expand.search_opened .search_submit,
										{{WRAPPER}} .search_style_fullscreen.search_opened :where(.search_form_wrap) .search_submit',
					)
				);
		
				$this->end_controls_tab();

				$this->start_controls_tab(
					'tab_icon_style_hover',
					array(
						'label' => __( 'Hover', 'trx_addons' ),
					)
				);

				$this->add_control(
					'icon_color_hover',
					array(
						'label'     => __( 'Icon Color', 'trx_addons' ),
						'type'      => Controls_Manager::COLOR,
						'selectors' => array(
							'{{WRAPPER}} .search_style_normal .search_submit:hover:before,
							 {{WRAPPER}} .search_style_expand.search_opened .search_submit:hover:before,
							 {{WRAPPER}} .search_style_fullscreen.search_opened :where(.search_form_wrap) .search_submit:hover:before' => 'color: {{VALUE}};',
							'{{WRAPPER}} .search_style_normal .search_submit:hover path,
							 {{WRAPPER}} .search_style_expand.search_opened .search_submit:hover path,
							 {{WRAPPER}} .search_style_fullscreen.search_opened :where(.search_form_wrap) .search_submit:hover path' => 'fill: {{VALUE}};',
						),
					)
				);

				$this->add_group_control(
					Group_Control_Background::get_type(),
					array(
						'name'           => 'icon_bg_hover',
						'types'          => array( 'classic', 'gradient' ),
						'selector'       => '{{WRAPPER}} .search_style_normal .search_submit:hover,
											 {{WRAPPER}} .search_style_expand.search_opened .search_submit:hover,
											 {{WRAPPER}} .search_style_fullscreen.search_opened :where(.search_form_wrap) .search_submit:hover',
						'fields_options' => array(
							'background' => array(
								'default' => 'classic',
							),
							// 'color'      => array(
							// 	'global' => array(
							// 		'default' => Global_Colors::COLOR_SECONDARY,
							// 	),
							// ),
						),
					)
				);

				$this->add_control(
					'icon_border_color_hover',
					array(
						'label'     => __( 'Border Color', 'trx_addons' ),
						'type'      => Controls_Manager::COLOR,
						'selectors' => array(
							'{{WRAPPER}} .search_style_normal .search_submit:hover,
							 {{WRAPPER}} .search_style_expand.search_opened .search_submit:hover,
							 {{WRAPPER}} .search_style_fullscreen.search_opened :where(.search_form_wrap) .search_submit:hover' => 'border-color: {{VALUE}};',
						),
					)
				);

				$this->add_group_control(
					Group_Control_Box_Shadow::get_type(),
					array(
						'name'     => 'icon_box_shadow_hover',
						'selector' => '{{WRAPPER}} .search_style_normal .search_submit:hover,
										{{WRAPPER}} .search_style_expand.search_opened .search_submit:hover,
										{{WRAPPER}} .search_style_fullscreen.search_opened :where(.search_form_wrap) .search_submit:hover',
					)
				);
		
				$this->end_controls_tab();

				$this->start_controls_tab(
					'tab_icon_style_focus',
					array(
						'label' => __( 'Focused', 'trx_addons' ),
					)
				);

				$this->add_control(
					'icon_color_focus',
					array(
						'label'     => __( 'Icon Color', 'trx_addons' ),
						'type'      => Controls_Manager::COLOR,
						'selectors' => array(
							'{{WRAPPER}} .search_style_normal .search_field:focus + .search_submit:before,
							 {{WRAPPER}} .search_style_expand.search_opened .search_field:focus + .search_submit:before,
							 {{WRAPPER}} .search_style_fullscreen.search_opened :where(.search_form_wrap) .search_field:focus + .search_submit:before' => 'color: {{VALUE}};',
							'{{WRAPPER}} .search_style_normal .search_field:focus + .search_submit path,
							 {{WRAPPER}} .search_style_expand.search_opened .search_field:focus + .search_submit path,
							 {{WRAPPER}} .search_style_fullscreen.search_opened :where(.search_form_wrap) .search_field:focus + .search_submit path' => 'fill: {{VALUE}};',
						),
					)
				);

				$this->add_group_control(
					Group_Control_Background::get_type(),
					array(
						'name'           => 'icon_bg_focus',
						'types'          => array( 'classic', 'gradient' ),
						'selector'       => '{{WRAPPER}} .search_style_normal .search_field:focus + .search_submit,
											 {{WRAPPER}} .search_style_expand.search_opened .search_field:focus + .search_submit,
											 {{WRAPPER}} .search_style_fullscreen.search_opened :where(.search_form_wrap) .search_field:focus + .search_submit',
						'fields_options' => array(
							'background' => array(
								'default' => 'classic',
							),
							// 'color'      => array(
							// 	'global' => array(
							// 		'default' => Global_Colors::COLOR_SECONDARY,
							// 	),
							// ),
						),
					)
				);

				$this->add_control(
					'icon_border_color_focus',
					array(
						'label'     => __( 'Border Color', 'trx_addons' ),
						'type'      => Controls_Manager::COLOR,
						'selectors' => array(
							'{{WRAPPER}} .search_style_normal .search_field:focus + .search_submit,
							 {{WRAPPER}} .search_style_expand.search_opened .search_field:focus + .search_submit,
							 {{WRAPPER}} .search_style_fullscreen.search_opened :where(.search_form_wrap) .search_field:focus + .search_submit' => 'border-color: {{VALUE}};',
						),
					)
				);

				$this->add_group_control(
					Group_Control_Box_Shadow::get_type(),
					array(
						'name'     => 'icon_box_shadow_focus',
						'selector' => '{{WRAPPER}} .search_style_normal .search_field:focus + .search_submit,
										{{WRAPPER}} .search_style_expand.search_opened .search_field:focus + .search_submit,
										{{WRAPPER}} .search_style_fullscreen.search_opened :where(.search_form_wrap) .search_field:focus + .search_submit',
					)
				);

				$this->end_controls_tab();

				$this->end_controls_tabs();

				$this->add_responsive_control(
					'icon_padding',
					array(
						'label'      => __( 'Padding', 'trx_addons' ),
						'type'       => Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
						'selectors'  => array(
							'{{WRAPPER}} .search_style_normal .search_submit,
							 {{WRAPPER}} .search_style_expand.search_opened .search_submit,
							 {{WRAPPER}} .search_style_fullscreen.search_opened :where(.search_form_wrap) .search_submit' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						),
						'separator'  => 'before',
						'condition' => array(
							'icon_hidden' => '',
						),
					)
				);

				$this->add_responsive_control(
					'icon_margin',
					array(
						'label'      => __( 'Margin', 'trx_addons' ),
						'type'       => Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
						'selectors'  => array(
							'{{WRAPPER}} .search_style_normal .search_submit,
							 {{WRAPPER}} .search_style_expand.search_opened .search_submit,
							 {{WRAPPER}} .search_style_fullscreen.search_opened :where(.search_form_wrap) .search_submit' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						),
						'condition' => array(
							'icon_hidden' => '',
						),
					)
				);

				$this->end_controls_section();
			}

			/**
			 * Register style controls for the button 'Search' which opens the search form in the style 'expand' or 'fullscreen'
			 */
			protected function register_style_controls_button() {
				$this->start_controls_section(
					'section_sc_layouts_search_style_button',
					[
						'label' => __( 'Trigger Button', 'trx_addons' ),
						'tab' => Controls_Manager::TAB_STYLE,
						'condition' => array(
							'style' => array( 'expand', 'fullscreen' ),
						),
					]
				);

				$this->add_responsive_control(
					'bt_icon_size',
					array(
						'label'      => __( 'Icon Size', 'trx_addons' ),
						'type'       => Controls_Manager::SLIDER,
						'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
						'selectors'  => array(
							'{{WRAPPER}} .search_style_expand:not(.search_opened) .search_submit,
							 {{WRAPPER}} .search_style_fullscreen:not(.search_opened) .search_submit,
							 {{WRAPPER}} .search_style_fullscreen.search_opened .search_submit_placeholder' => 'font-size: {{SIZE}}{{UNIT}};',
						),
					)
				);

				$this->add_responsive_control(
					'bt_icon_box_size',
					array(
						'label'      => __( 'Icon Box Size', 'trx_addons' ),
						'type'       => Controls_Manager::SLIDER,
						'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
						'default'   => [
							'size' => '',
							'unit' => 'px',
						],
						'selectors'  => array(
							'{{WRAPPER}} .search_style_expand:not(.search_opened) .search_submit,
							 {{WRAPPER}} .search_style_fullscreen:not(.search_opened) .search_submit,
							 {{WRAPPER}} .search_style_fullscreen.search_opened .search_submit_placeholder' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}}; text-align: center; box-sizing: content-box;',
						),
					)
				);

				$this->start_controls_tabs( 'tabs_button_styles' );

				$this->start_controls_tab(
					'tab_button_style_normal',
					array(
						'label' => __( 'Normal', 'trx_addons' ),
					)
				);

				$this->add_control(
					'bt_icon_color',
					array(
						'label'     => __( 'Icon Color', 'trx_addons' ),
						'type'      => Controls_Manager::COLOR,
						'selectors' => array(
							'{{WRAPPER}} .search_style_expand:not(.search_opened) .search_submit:before,
							 {{WRAPPER}} .search_style_fullscreen:not(.search_opened) .search_submit:before,
							 {{WRAPPER}} .search_style_fullscreen.search_opened .search_submit_placeholder:before' => 'color: {{VALUE}};',
							'{{WRAPPER}} .search_style_expand:not(.search_opened) .search_submit path,
							 {{WRAPPER}} .search_style_fullscreen:not(.search_opened) .search_submit path,
							 {{WRAPPER}} .search_style_fullscreen.search_opened .search_submit_placeholder path' => 'fill: {{VALUE}};',
						),
					)
				);

				$this->add_group_control(
					Group_Control_Background::get_type(),
					array(
						'name'     => 'bt_icon_bg',
						'types'    => array( 'classic', 'gradient' ),
						'selector' => '{{WRAPPER}} .search_style_expand:not(.search_opened) .search_submit,
										{{WRAPPER}} .search_style_fullscreen:not(.search_opened) .search_submit,
										{{WRAPPER}} .search_style_fullscreen.search_opened .search_submit_placeholder',
					)
				);

				$this->add_group_control(
					Group_Control_Border::get_type(),
					array(
						'name'     => 'bt_icon_border',
						'selector' => '{{WRAPPER}} .search_style_expand:not(.search_opened) .search_submit,
										{{WRAPPER}} .search_style_fullscreen:not(.search_opened) .search_submit,
										{{WRAPPER}} .search_style_fullscreen.search_opened .search_submit_placeholder',
					)
				);

				$this->add_control(
					'bt_icon_radius',
					array(
						'label'      => __( 'Border Radius', 'trx_addons' ),
						'type'       => Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
						'selectors'  => array(
							'{{WRAPPER}} .search_style_expand:not(.search_opened) .search_submit,
							 {{WRAPPER}} .search_style_fullscreen:not(.search_opened) .search_submit,
							 {{WRAPPER}} .search_style_fullscreen.search_opened .search_submit_placeholder' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
						),
					)
				);

				$this->add_group_control(
					Group_Control_Box_Shadow::get_type(),
					array(
						'name'     => 'bt_icon_box_shadow',
						'selector' => '{{WRAPPER}} .search_style_expand:not(.search_opened) .search_submit,
										{{WRAPPER}} .search_style_fullscreen:not(.search_opened) .search_submit,
										{{WRAPPER}} .search_style_fullscreen.search_opened .search_submit_placeholder',
					)
				);
		
				$this->end_controls_tab();

				$this->start_controls_tab(
					'tab_button_style_hover',
					array(
						'label' => __( 'Hover', 'trx_addons' ),
					)
				);

				$this->add_control(
					'bt_icon_color_hover',
					array(
						'label'     => __( 'Icon Color', 'trx_addons' ),
						'type'      => Controls_Manager::COLOR,
						'selectors' => array(
							'{{WRAPPER}} .search_style_expand:not(.search_opened) .search_submit:hover:before,
							 {{WRAPPER}} .search_style_fullscreen:not(.search_opened) .search_submit:hover:before,
							 {{WRAPPER}} .search_style_fullscreen.search_opened .search_submit_placeholder:hover:before' => 'color: {{VALUE}};',
							'{{WRAPPER}} .search_style_expand:not(.search_opened) .search_submit:hover path,
							 {{WRAPPER}} .search_style_fullscreen:not(.search_opened) .search_submit:hover path,
							 {{WRAPPER}} .search_style_fullscreen.search_opened .search_submit_placeholder:hover path' => 'fill: {{VALUE}};',
						),
					)
				);

				$this->add_group_control(
					Group_Control_Background::get_type(),
					array(
						'name'           => 'bt_icon_bg_hover',
						'types'          => array( 'classic', 'gradient' ),
						'selector'       => '{{WRAPPER}} .search_style_expand:not(.search_opened) .search_submit:hover,
											 {{WRAPPER}} .search_style_fullscreen:not(.search_opened) .search_submit:hover,
											 {{WRAPPER}} .search_style_fullscreen.search_opened .search_submit_placeholder:hover',
						'fields_options' => array(
							'background' => array(
								'default' => 'classic',
							),
							// 'color'      => array(
							// 	'global' => array(
							// 		'default' => Global_Colors::COLOR_SECONDARY,
							// 	),
							// ),
						),
					)
				);

				$this->add_control(
					'bt_icon_border_color_hover',
					array(
						'label'     => __( 'Border Color', 'trx_addons' ),
						'type'      => Controls_Manager::COLOR,
						'selectors' => array(
							'{{WRAPPER}} .search_style_expand:not(.search_opened) .search_submit:hover,
							 {{WRAPPER}} .search_style_fullscreen:not(.search_opened) .search_submit:hover,
							 {{WRAPPER}} .search_style_fullscreen.search_opened .search_submit_placeholder:hover' => 'border-color: {{VALUE}};',
						),
					)
				);

				$this->add_group_control(
					Group_Control_Box_Shadow::get_type(),
					array(
						'name'     => 'bt_icon_box_shadow_hover',
						'selector' => '{{WRAPPER}} .search_style_expand:not(.search_opened) .search_submit:hover,
										{{WRAPPER}} .search_style_fullscreen:not(.search_opened) .search_submit:hover,
										{{WRAPPER}} .search_style_fullscreen.search_opened .search_submit_placeholder:hover',
					)
				);
		
				$this->end_controls_tab();

				$this->end_controls_tabs();

				$this->add_responsive_control(
					'bt_icon_padding',
					array(
						'label'      => __( 'Padding', 'trx_addons' ),
						'type'       => Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
						'selectors'  => array(
							'{{WRAPPER}} .search_style_expand:not(.search_opened) .search_submit,
							 {{WRAPPER}} .search_style_fullscreen:not(.search_opened) .search_submit,
							 {{WRAPPER}} .search_style_fullscreen.search_opened .search_submit_placeholder' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						),
						'separator'  => 'before',
					)
				);

				$this->add_responsive_control(
					'bt_icon_margin',
					array(
						'label'      => __( 'Margin', 'trx_addons' ),
						'type'       => Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
						'selectors'  => array(
							'{{WRAPPER}} .search_style_expand:not(.search_opened) .search_submit,
							 {{WRAPPER}} .search_style_fullscreen:not(.search_opened) .search_submit,
							 {{WRAPPER}} .search_style_fullscreen.search_opened .search_submit_placeholder' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						),
						'separator'  => 'before',
					)
				);

				$this->end_controls_section();
			}

			/**
			 * Register style controls for the button 'Close' in the Fullscreen mode
			 */
			protected function register_style_controls_close_button() {
				$this->start_controls_section(
					'section_sc_layouts_search_style_close_button',
					array(
						'label' => __( 'Close Button', 'trx_addons' ),
						'tab' => Controls_Manager::TAB_STYLE,
						'condition' => array(
							'style' => array( 'fullscreen' ),
						),
					)
				);

				// $icon_param = trx_addons_array_get_first_value( $this->get_icon_param() );
				// unset( $icon_param['name'] );
				// $this->add_control( 'icon_close', $icon_param );

				$this->add_control(
					'icon_close',
					[
						'label' => esc_html__( 'Close Icon', 'trx_addons' ),
						'type' => Controls_Manager::ICONS,
						// 'default' => [
						// 	'value' => 'fas fa-times',
						// 	'library' => 'fa-solid',
						// ],
						'recommended' => [
							'fa-solid' => [
								'times',
								'times-circle',
								'times-square',
								'window-close',
							],
							'fa-regular' => [
								'window-close',
							],
						],
					]
				);

				$this->add_responsive_control(
					'icon_close_size',
					array(
						'label'      => __( 'Icon Size', 'trx_addons' ),
						'type'       => Controls_Manager::SLIDER,
						'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
						'selectors'  => array(
							'{{WRAPPER}} .search_style_fullscreen.search_opened .search_close' => 'font-size: {{SIZE}}{{UNIT}};',
						),
					)
				);

				$this->add_responsive_control(
					'icon_close_box_size',
					array(
						'label'      => __( 'Icon Box Size', 'trx_addons' ),
						'type'       => Controls_Manager::SLIDER,
						'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
						'default'   => [
							'size' => '',
							'unit' => 'px',
						],
						'selectors'  => array(
							'{{WRAPPER}} .search_style_fullscreen.search_opened .search_close' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}}; text-align: center; box-sizing: content-box;',
						),
					)
				);

				$this->start_controls_tabs( 'tabs_icon_close_styles' );

				$this->start_controls_tab(
					'tab_icon_close_style_normal',
					array(
						'label' => __( 'Normal', 'trx_addons' ),
					)
				);

				$this->add_control(
					'icon_close_color',
					array(
						'label'     => __( 'Icon Color', 'trx_addons' ),
						'type'      => Controls_Manager::COLOR,
						'selectors' => array(
							'{{WRAPPER}} .search_style_fullscreen.search_opened .search_close:before' => 'color: {{VALUE}};',
							'{{WRAPPER}} .search_style_fullscreen.search_opened .search_close path' => 'fill: {{VALUE}};',
						),
					)
				);

				$this->add_group_control(
					Group_Control_Background::get_type(),
					array(
						'name'     => 'icon_close_bg',
						'types'    => array( 'classic', 'gradient' ),
						'selector' => '{{WRAPPER}} .search_style_fullscreen.search_opened .search_close',
					)
				);
		
				$this->add_group_control(
					Group_Control_Border::get_type(),
					array(
						'name'     => 'icon_close_border',
						'selector' => '{{WRAPPER}} .search_style_fullscreen.search_opened .search_close',
					)
				);
		
				$this->add_control(
					'icon_close_radius',
					array(
						'label'      => __( 'Border Radius', 'trx_addons' ),
						'type'       => Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
						'selectors'  => array(
							'{{WRAPPER}} .search_style_fullscreen.search_opened .search_close' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
						),
					)
				);

				$this->add_group_control(
					Group_Control_Box_Shadow::get_type(),
					array(
						'name'     => 'icon_close_box_shadow',
						'selector' => '{{WRAPPER}} .search_style_fullscreen.search_opened .search_close',
					)
				);

				$this->end_controls_tab();

				$this->start_controls_tab(
					'tab_icon_close_style_hover',
					array(
						'label' => __( 'Hover', 'trx_addons' ),
					)
				);

				$this->add_control(
					'icon_close_color_hover',
					array(
						'label'     => __( 'Icon Color', 'trx_addons' ),
						'type'      => Controls_Manager::COLOR,
						'selectors' => array(
							'{{WRAPPER}} .search_style_fullscreen.search_opened .search_close:hover:before' => 'color: {{VALUE}};',
							'{{WRAPPER}} .search_style_fullscreen.search_opened .search_close:hover path' => 'fill: {{VALUE}};',
						),
					)
				);

				$this->add_group_control(
					Group_Control_Background::get_type(),
					array(
						'name'           => 'icon_close_bg_hover',
						'types'          => array( 'classic', 'gradient' ),
						'selector'       => '{{WRAPPER}} .search_style_fullscreen.search_opened .search_close:hover',
						'fields_options' => array(
							'background' => array(
								'default' => 'classic',
							),
							// 'color'      => array(
							// 	'global' => array(
							// 		'default' => Global_Colors::COLOR_SECONDARY,
							// 	),
							// ),
						),
					)
				);

				$this->add_control(
					'icon_close_border_color_hover',
					array(
						'label'     => __( 'Border Color', 'trx_addons' ),
						'type'      => Controls_Manager::COLOR,
						'selectors' => array(
							'{{WRAPPER}} .search_style_fullscreen.search_opened .search_close:hover' => 'border-color: {{VALUE}};',
						),
					)
				);

				$this->add_group_control(
					Group_Control_Box_Shadow::get_type(),
					array(
						'name'     => 'icon_close_box_shadow_hover',
						'selector' => '{{WRAPPER}} .search_style_fullscreen.search_opened .search_close:hover',
					)
				);

				$this->end_controls_tab();

				$this->start_controls_tab(
					'tab_icon_close_style_focus',
					array(
						'label' => __( 'Focused', 'trx_addons' ),
					)
				);

				$this->add_control(
					'icon_close_color_focus',
					array(
						'label'     => __( 'Icon Color', 'trx_addons' ),
						'type'      => Controls_Manager::COLOR,
						'selectors' => array(
							'{{WRAPPER}} .search_style_fullscreen.search_opened .search_field:focus ~ .search_close:before' => 'color: {{VALUE}};',
							'{{WRAPPER}} .search_style_fullscreen.search_opened .search_field:focus ~ .search_close path' => 'fill: {{VALUE}};',
						),
					)
				);

				$this->add_group_control(
					Group_Control_Background::get_type(),
					array(
						'name'           => 'icon_close_bg_focus',
						'types'          => array( 'classic', 'gradient' ),
						'selector'       => '{{WRAPPER}} .search_style_fullscreen.search_opened .search_field:focus ~ .search_close',
						'fields_options' => array(
							'background' => array(
								'default' => 'classic',
							),
							// 'color'      => array(
							// 	'global' => array(
							// 		'default' => Global_Colors::COLOR_SECONDARY,
							// 	),
							// ),
						),
					)
				);

				$this->add_control(
					'icon_close_border_color_focus',
					array(
						'label'     => __( 'Border Color', 'trx_addons' ),
						'type'      => Controls_Manager::COLOR,
						'selectors' => array(
							'{{WRAPPER}} .search_style_fullscreen.search_opened .search_field:focus ~ .search_close' => 'border-color: {{VALUE}};',
						),
					)
				);

				$this->add_group_control(
					Group_Control_Box_Shadow::get_type(),
					array(
						'name'     => 'icon_close_box_shadow_focus',
						'selector' => '{{WRAPPER}} .search_style_fullscreen.search_opened .search_field:focus ~ .search_close',
					)
				);

				$this->end_controls_tab();

				$this->end_controls_tabs();

				$this->add_responsive_control(
					'icon_close_padding',
					array(
						'label'      => __( 'Padding', 'trx_addons' ),
						'type'       => Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
						'selectors'  => array(
							'{{WRAPPER}} .search_style_fullscreen.search_opened .search_close' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						),
						'separator'  => 'before',
					)
				);

				$this->add_responsive_control(
					'icon_close_margin',
					array(
						'label'      => __( 'Margin', 'trx_addons' ),
						'type'       => Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
						'selectors'  => array(
							'{{WRAPPER}} .search_style_fullscreen.search_opened .search_close' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						),
					)
				);

				$this->end_controls_section();
			}

			/**
			 * Register style controls for the label 'Close' in the Fullscreen mode
			 */
			protected function register_style_controls_close_label() {
				$this->start_controls_section(
					'section_sc_layouts_search_style_close_label',
					array(
						'label' => __( 'Close Label', 'trx_addons' ),
						'tab' => Controls_Manager::TAB_STYLE,
						'condition' => array(
							'style' => array( 'fullscreen' ),
						),
					)
				);

				$this->start_controls_tabs( 'tabs_close_label_styles' );

				$this->start_controls_tab(
					'tab_close_label_style_normal',
					array(
						'label' => __( 'Normal', 'trx_addons' ),
					)
				);

				$this->add_group_control(
					Group_Control_Typography::get_type(),
					array(
						'name'     => 'close_label_typography',
						'selector' => '{{WRAPPER}} .search_style_fullscreen.search_opened .search_close_label',
					)
				);

				$this->add_control(
					'close_label_color',
					array(
						'label'     => __( 'Text Color', 'trx_addons' ),
						'type'      => Controls_Manager::COLOR,
						'selectors' => array(
							'{{WRAPPER}} .search_style_fullscreen.search_opened .search_close_label' => 'color: {{VALUE}};',
						),
					)
				);

				$this->add_group_control(
					Group_Control_Background::get_type(),
					array(
						'name'     => 'close_label_bg',
						'types'    => array( 'classic', 'gradient' ),
						'selector' => '{{WRAPPER}} .search_style_fullscreen.search_opened .search_close_label',
					)
				);
		
				$this->add_group_control(
					Group_Control_Text_Shadow::get_type(),
					array(
						'name'     => 'close_label_text_shadow',
						'selector' => '{{WRAPPER}} .search_style_fullscreen.search_opened .search_close_label',
					)
				);
		
				$this->add_group_control(
					Group_Control_Border::get_type(),
					array(
						'name'     => 'close_label_border',
						'selector' => '{{WRAPPER}} .search_style_fullscreen.search_opened .search_close_label',
					)
				);
		
				$this->add_control(
					'close_label_radius',
					array(
						'label'      => __( 'Border Radius', 'trx_addons' ),
						'type'       => Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
						'selectors'  => array(
							'{{WRAPPER}} .search_style_fullscreen.search_opened .search_close_label' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
						),
					)
				);
		
				$this->end_controls_tab();

				$this->start_controls_tab(
					'tab_close_label_style_hover',
					array(
						'label' => __( 'Hover', 'trx_addons' ),
					)
				);

				$this->add_control(
					'close_label_color_hover',
					array(
						'label'     => __( 'Text Color', 'trx_addons' ),
						'type'      => Controls_Manager::COLOR,
						'selectors' => array(
							'{{WRAPPER}} .search_style_fullscreen.search_opened .search_close:hover .search_close_label' => 'color: {{VALUE}};',
						),
					)
				);

				$this->add_group_control(
					Group_Control_Background::get_type(),
					array(
						'name'           => 'close_label_bg_hover',
						'types'          => array( 'classic', 'gradient' ),
						'selector'       => '{{WRAPPER}} .search_style_fullscreen.search_opened .search_close:hover .search_close_label',
						'fields_options' => array(
							'background' => array(
								'default' => 'classic',
							),
							// 'color'      => array(
							// 	'global' => array(
							// 		'default' => Global_Colors::COLOR_SECONDARY,
							// 	),
							// ),
						),
					)
				);

				$this->add_group_control(
					Group_Control_Text_Shadow::get_type(),
					array(
						'name'     => 'close_label_text_shadow_hover',
						'selector' => '{{WRAPPER}} .search_style_fullscreen.search_opened .search_close:hover .search_close_label',
					)
				);

				$this->add_control(
					'close_label_border_color_hover',
					array(
						'label'     => __( 'Border Color', 'trx_addons' ),
						'type'      => Controls_Manager::COLOR,
						'selectors' => array(
							'{{WRAPPER}} .search_style_fullscreen.search_opened .search_close:hover .search_close_label' => 'border-color: {{VALUE}};',
						),
					)
				);
		
				$this->end_controls_tab();

				$this->start_controls_tab(
					'tab_close_label_style_focus',
					array(
						'label' => __( 'Focused', 'trx_addons' ),
					)
				);

				$this->add_control(
					'close_label_color_focus',
					array(
						'label'     => __( 'Text Color', 'trx_addons' ),
						'type'      => Controls_Manager::COLOR,
						'selectors' => array(
							'{{WRAPPER}} .search_style_fullscreen.search_opened .search_field:focus ~ .search_close .search_close_label' => 'color: {{VALUE}};',
						),
					)
				);

				$this->add_group_control(
					Group_Control_Background::get_type(),
					array(
						'name'           => 'close_label_bg_focus',
						'types'          => array( 'classic', 'gradient' ),
						'selector'       => '{{WRAPPER}} .search_style_fullscreen.search_opened .search_field:focus ~ .search_close .search_close_label',
						'fields_options' => array(
							'background' => array(
								'default' => 'classic',
							),
							// 'color'      => array(
							// 	'global' => array(
							// 		'default' => Global_Colors::COLOR_SECONDARY,
							// 	),
							// ),
						),
					)
				);

				$this->add_group_control(
					Group_Control_Text_Shadow::get_type(),
					array(
						'name'     => 'close_label_text_shadow_focus',
						'selector' => '{{WRAPPER}} .search_style_fullscreen.search_opened .search_field:focus ~ .search_close .search_close_label',
					)
				);

				$this->add_control(
					'close_label_border_color_focus',
					array(
						'label'     => __( 'Border Color', 'trx_addons' ),
						'type'      => Controls_Manager::COLOR,
						'selectors' => array(
							'{{WRAPPER}} .search_style_fullscreen.search_opened .search_field:focus ~ .search_close .search_close_label' => 'border-color: {{VALUE}};',
						),
					)
				);
		
				$this->end_controls_tab();

				$this->end_controls_tabs();

				$this->add_responsive_control(
					'close_label_padding',
					array(
						'label'      => __( 'Padding', 'trx_addons' ),
						'type'       => Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
						'selectors'  => array(
							'{{WRAPPER}} .search_style_fullscreen.search_opened .search_close_label' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						),
						'separator'  => 'before',
					)
				);

				$this->add_responsive_control(
					'close_label_margin',
					array(
						'label'      => __( 'Margin', 'trx_addons' ),
						'type'       => Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
						'selectors'  => array(
							'{{WRAPPER}} .search_style_fullscreen.search_opened .search_close_label' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						),
					)
				);

				$this->end_controls_section();
			}

			/**
			 * Register style controls for the overlay layer in the Fullscreen mode
			 */
			protected function register_style_controls_overlay() {
				$this->start_controls_section(
					'section_sc_layouts_search_style_overlay',
					array(
						'label' => __( 'Overlay', 'trx_addons' ),
						'tab' => Controls_Manager::TAB_STYLE,
						'condition' => array(
							'style' => array( 'fullscreen' ),
						),
					)
				);
				
				$this->add_responsive_control(
					'overlay_height',
					array(
						'label'      => __( 'Height', 'trx_addons' ),
						'type'       => Controls_Manager::SLIDER,
						'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
						'default'    => [
							'size' => '',
							'unit' => '%',
						],
						'selectors'  => array(
							// Old way: overlay is under the fixed rows
							// '{{WRAPPER}} .search_style_fullscreen.search_opened .search_form_wrap' => 'height: clamp( 0px, {{SIZE}}{{UNIT}}, calc( 100% - var(--fixed-rows-height) ) ); bottom: auto;',
							// New way: overlay is over the fixed rows
							'{{WRAPPER}} .search_style_fullscreen.search_opened .search_form_wrap' => 'height: clamp( 0px, {{SIZE}}{{UNIT}}, 100% ); bottom: auto;',
							'{{WRAPPER}} .search_style_fullscreen.search_opened .search_results' => 'top: calc( {{SIZE}}{{UNIT}} / 2 );',
						),
					)
				);

				$this->add_group_control(
					Group_Control_Background::get_type(),
					array(
						'name'     => 'overlay_bg',
						'types'    => array( 'classic', 'gradient' ),
						'selector' => '{{WRAPPER}} .search_style_fullscreen.search_opened .search_form_wrap',
					)
				);

				$this->add_group_control(
					Group_Control_Box_Shadow::get_type(),
					array(
						'name'     => 'overlay_box_shadow',
						'selector' => '{{WRAPPER}} .search_style_fullscreen.search_opened .search_form_wrap',
					)
				);

				$this->add_control(
					'overlay_animation',
					[
						'label' => esc_html__( 'Entrance Animation', 'trx_addons' ),
						'type' => Controls_Manager::ANIMATION,
						'frontend_available' => true,
					]
				);

				$this->add_control(
					'overlay_animation_exit',
					[
						'label' => esc_html__( 'Exit Animation', 'trx_addons' ),
						'type' => Controls_Manager::EXIT_ANIMATION,
						'frontend_available' => true,
					]
				);
		
				$this->add_control(
					'overlay_animation_duration',
					[
						'label' => esc_html__( 'Animation Duration', 'trx_addons' ),
						'type' => Controls_Manager::SELECT,
						'default' => '',
						'options' => [
							'slow' => esc_html__( 'Slow', 'trx_addons' ),
							'' => esc_html__( 'Normal', 'trx_addons' ),
							'fast' => esc_html__( 'Fast', 'trx_addons' ),
						],
						'prefix_class' => 'animated-',
						'conditions' => [
							'relation' => 'or',
							'terms' => array(
								array(
									'name'     => 'overlay_animation',
									'operator' => '!==',
									'value'    => '',
								),
								array(
									'name'     => 'overlay_animation_exit',
									'operator' => '!==',
									'value'    => '',
								),
							),
						],
					]
				);

				$this->add_control(
					'overlay_bg2_heading',
					[
						'label' => esc_html__( 'Overlay Background', 'trx_addons' ),
						'type' => Controls_Manager::HEADING,
						'separator' => 'before',
					]
				);

				$this->add_group_control(
					Group_Control_Background::get_type(),
					array(
						'name'     => 'overlay_bg2',
						'types'    => array( 'classic', 'gradient' ),
						'selector' => '{{WRAPPER}} .search_style_fullscreen.search_opened .search_form_overlay',
					)
				);

				$this->end_controls_section();
			}

			/**
			 * Register style controls for the ajax search results
			 */
			protected function register_style_controls_results() {
				$this->start_controls_section(
					'section_sc_layouts_search_style_results',
					array(
						'label' => __( 'Search Results', 'trx_addons' ),
						'tab' => Controls_Manager::TAB_STYLE,
						'condition' => array(
							'ajax' => array( '1' ),
						),
					)
				);
				
				$this->add_responsive_control(
					'results_width',
					array(
						'label'      => __( 'Width', 'trx_addons' ),
						'type'       => Controls_Manager::SLIDER,
						'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
						'selectors'  => array(
							'{{WRAPPER}} .search_wrap .search_results' => 'width: {{SIZE}}{{UNIT}};',
						),
					)
				);

				$this->add_responsive_control(
					'results_margin',
					array(
						'label'      => __( 'Margin', 'trx_addons' ),
						'type'       => Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
						'selectors'  => array(
							'{{WRAPPER}} .search_wrap .search_results' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						),
					)
				);

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
				trx_addons_get_template_part(TRX_ADDONS_PLUGIN_CPT_LAYOUTS_SHORTCODES . "search/tpe.search.php",
										'trx_addons_args_sc_layouts_search',
										array('element' => $this)
									);
			}
		}
		
		// Register widget
		trx_addons_elm_register_widget( 'TRX_Addons_Elementor_Widget_Layouts_Search' );
	}
}
