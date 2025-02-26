<?php
/**
 * Widget: Recent posts (Elementor support)
 *
 * @package ThemeREX Addons
 * @since v1.0
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
if ( ! function_exists( 'trx_addons_sc_widget_recent_posts_add_in_elementor' ) ) {
	add_action( trx_addons_elementor_get_action_for_widgets_registration(), 'trx_addons_sc_widget_recent_posts_add_in_elementor' );
	function trx_addons_sc_widget_recent_posts_add_in_elementor() {
		
		if ( ! class_exists( 'TRX_Addons_Elementor_Widget' ) ) return;	

		class TRX_Addons_Elementor_Widget_Recent_Posts extends TRX_Addons_Elementor_Widget {

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
				$this->add_plain_params([
					'number' => 'size'
				]);
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
				return 'trx_widget_recent_posts';
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
				return __( 'Recent Posts', 'trx_addons' );
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
				return [ 'recent', 'posts', 'news' ];
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
				return 'eicon-post-list trx_addons_elementor_widget_icon';
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
				return ['trx_addons-elements'];
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

				if ( apply_filters( 'trx_addons_filter_allow_sc_styles_in_elementor', false, 'widget_recent_posts' ) ) {
					$this->register_style_controls_widget_title();
					$this->register_style_controls_post_item();
					$this->register_style_controls_image();
					$this->register_style_controls_post_content();
					$this->register_style_controls_title();
					$this->register_style_controls_categories();
					$this->register_style_controls_counters();
					$this->register_style_controls_meta();
				}
			}

			/**
			 * Register content controls.
			 */
			protected function register_content_controls() {

				$this->start_controls_section(
					'section_sc_recent_posts',
					[
						'label' => __( 'Recent Posts', 'trx_addons' ),
					]
				);

				$this->add_control(
					'title',
					[
						'label' => __( 'Title', 'trx_addons' ),
						'label_block' => false,
						'type' => Controls_Manager::TEXT,
						'placeholder' => __( "Widget title", 'trx_addons' ),
						'default' => ''
					]
				);

				$this->add_control(
					'number',
					[
						'label' => __( 'Number of posts to display', 'trx_addons' ),
						'type' => Controls_Manager::SLIDER,
						'default' => [
							'size' => 4,
						],
						'range' => [
							'px' => [
								'min' => 1,
								'max' => 12
							]
						]
					]
				);

				$this->add_control(
					'details',
					[
						'label' => __( 'Details', 'elementor' ),
						'type' => Controls_Manager::HEADING,
						'separator' => 'before',
					]
				);

				$this->add_control(
					'show_image',
					[
						'label' => __( "Show image", 'trx_addons' ),
						'label_block' => false,
						'type' => Controls_Manager::SWITCHER,
						'label_off' => __( 'Hide', 'trx_addons' ),
						'label_on' => __( 'Show', 'trx_addons' ),
						'default' => '1',
						'return_value' => '1'
					]
				);

				$this->add_control(
					'show_author',
					[
						'label' => __( "Show author", 'trx_addons' ),
						'label_block' => false,
						'type' => Controls_Manager::SWITCHER,
						'label_off' => __( 'Hide', 'trx_addons' ),
						'label_on' => __( 'Show', 'trx_addons' ),
						'default' => '1',
						'return_value' => '1'
					]
				);

				$this->add_control(
					'show_date',
					[
						'label' => __( "Show date", 'trx_addons' ),
						'label_block' => false,
						'type' => Controls_Manager::SWITCHER,
						'label_off' => __( 'Hide', 'trx_addons' ),
						'label_on' => __( 'Show', 'trx_addons' ),
						'default' => '1',
						'return_value' => '1'
					]
				);

				$this->add_control(
					'show_counters',
					[
						'label' => __( "Show view count", 'trx_addons' ),
						'label_block' => false,
						'type' => Controls_Manager::SWITCHER,
						'label_off' => __( 'Hide', 'trx_addons' ),
						'label_on' => __( 'Show', 'trx_addons' ),
						'default' => '1',
						'return_value' => '1'
					]
				);

				$this->add_control(
					'show_categories',
					[
						'label' => __( "Show categories", 'trx_addons' ),
						'label_block' => false,
						'type' => Controls_Manager::SWITCHER,
						'label_off' => __( 'Hide', 'trx_addons' ),
						'label_on' => __( 'Show', 'trx_addons' ),
						'default' => '1',
						'return_value' => '1'
					]
				);

				$this->end_controls_section();
			}

			/**
			 * Register style controls for the widget title.
			 */
			protected function register_style_controls_widget_title() {
				$this->start_controls_section(
					'section_recent_posts_style_widget_title',
					array(
						'label' => __( 'Widget Title', 'trx_addons' ),
						'tab' => Controls_Manager::TAB_STYLE,
					)
				);

				$this->add_control(
					'widget_title_alignment',
					array(
						'label'     => __( 'Alignment', 'trx_addons' ),
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
						'default'   => '',
						// 'toggle'    => false,
						'selectors' => array(
							'{{WRAPPER}} .widget_recent_posts .widget_title' => 'text-align: {{VALUE}}',
						),
					)
				);

				$this->add_group_control(
					Group_Control_Typography::get_type(),
					array(
						'name'     => 'widget_title_typography',
						'selector' => '{{WRAPPER}} .widget_recent_posts .widget_title',
					)
				);

				$this->add_group_control(
					Group_Control_Background::get_type(),
					array(
						'name'     => 'widget_title_bg',
						'types'    => array( 'classic', 'gradient' ),
						'selector' => '{{WRAPPER}} .widget_recent_posts .widget_title',
					)
				);

				$this->add_control(
					'widget_title_color',
					array(
						'label'     => __( 'Text Color', 'trx_addons' ),
						'type'      => Controls_Manager::COLOR,
						'selectors' => array(
							'{{WRAPPER}} .widget_recent_posts .widget_title' => 'color: {{VALUE}};',
						),
					)
				);

				$this->add_group_control(
					Group_Control_Text_Shadow::get_type(),
					array(
						'name'     => 'widget_title_text_shadow',
						'selector' => '{{WRAPPER}} .widget_recent_posts .widget_title',
					)
				);
		
				$this->add_group_control(
					Group_Control_Border::get_type(),
					array(
						'name'     => 'widget_title_border',
						'selector' => '{{WRAPPER}} .widget_recent_posts .widget_title',
					)
				);
		
				$this->add_responsive_control(
					'widget_title_radius',
					array(
						'label'      => __( 'Border Radius', 'trx_addons' ),
						'type'       => Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
						'selectors'  => array(
							'{{WRAPPER}} .widget_recent_posts .widget_title' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
						),
					)
				);

				$this->add_responsive_control(
					'widget_title_padding',
					array(
						'label'      => __( 'Padding', 'trx_addons' ),
						'type'       => Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
						'selectors'  => array(
							'{{WRAPPER}} .widget_recent_posts .widget_title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						),
					)
				);

				$this->add_responsive_control(
					'widget_title_margin',
					array(
						'label'      => __( 'Margin', 'trx_addons' ),
						'type'       => Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
						'selectors'  => array(
							'{{WRAPPER}} .widget_recent_posts .widget_title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						),
					)
				);

				$this->add_group_control(
					Group_Control_Box_Shadow::get_type(),
					array(
						'name'     => 'widget_title_shadow',
						'selector' => '{{WRAPPER}} .widget_recent_posts .widget_title',
					)
				);

				$this->end_controls_section();
			}

			/**
			 * Register style controls for the post items.
			 */
			protected function register_style_controls_post_item() {
				$this->start_controls_section(
					'section_recent_posts_style_post_item',
					array(
						'label' => __( 'Post Item', 'trx_addons' ),
						'tab' => Controls_Manager::TAB_STYLE,
					)
				);

				$this->add_group_control(
					Group_Control_Background::get_type(),
					array(
						'name'     => 'post_item_bg',
						'types'    => array( 'classic', 'gradient' ),
						'selector' => '{{WRAPPER}} .widget_recent_posts .post_item',
					)
				);
		
				$this->add_group_control(
					Group_Control_Border::get_type(),
					array(
						'name'     => 'post_item_border',
						'selector' => '{{WRAPPER}} .widget_recent_posts .post_item',
					)
				);
		
				$this->add_responsive_control(
					'post_item_radius',
					array(
						'label'      => __( 'Border Radius', 'trx_addons' ),
						'type'       => Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
						'selectors'  => array(
							'{{WRAPPER}} .widget_recent_posts .post_item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
						),
					)
				);

				$this->add_responsive_control(
					'post_item_padding',
					array(
						'label'      => __( 'Padding', 'trx_addons' ),
						'type'       => Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
						'selectors'  => array(
							'{{WRAPPER}} .widget_recent_posts .post_item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						),
					)
				);

				$this->add_responsive_control(
					'post_item_margin',
					array(
						'label'      => __( 'Margin', 'trx_addons' ),
						'type'       => Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
						'selectors'  => array(
							'{{WRAPPER}} .widget_recent_posts .post_item' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						),
					)
				);

				$this->add_group_control(
					Group_Control_Box_Shadow::get_type(),
					array(
						'name'     => 'post_item_shadow',
						'selector' => '{{WRAPPER}} .widget_recent_posts .post_item',
					)
				);

				$this->end_controls_section();
			}

			/**
			 * Register style controls for the post image.
			 */
			protected function register_style_controls_image() {
				$this->start_controls_section(
					'section_recent_posts_style_image',
					array(
						'label' => __( 'Post Image', 'trx_addons' ),
						'tab' => Controls_Manager::TAB_STYLE,
						'condition' => [
							'show_image' => '1',
						],
					)
				);

				$this->add_responsive_control(
					'image_position',
					array(
						'label'     => __( 'Position', 'trx_addons' ),
						'type'      => Controls_Manager::CHOOSE,
						'options'   => array(
							'0' => array(
								'title' => __( 'Left', 'trx_addons' ),
								'icon'  => 'eicon-h-align-left',
							),
							'1' => array(
								'title' => __( 'Right', 'trx_addons' ),
								'icon'  => 'eicon-h-align-right',
							),
						),
						'default'   => 'left',
						'toggle'    => false,
						'selectors' => array(
							'{{WRAPPER}} .widget_recent_posts .post_item .post_thumb' => 'order: {{VALUE}}',
						),
					)
				);

				$this->add_control(
					'thumb_size',
					[
						'type' => Controls_Manager::SELECT,
						'label' => __( 'Thumb Size', 'trx_addons' ),
						'label_block' => false,
						'description' => wp_kses_data( __("Leave 'Default' to use default size defined in the shortcode template or any registered size to override thumbnail size with the selected value.", 'trx_addons') ),
						'options' => array_merge( array( '' => __( 'Default', 'trx_addons' ) ), trx_addons_get_list_thumbnail_sizes() ),
						'default' => '',
					]
				);

				$this->add_responsive_control(
					'image_width',
					array(
						'label'      => __( 'Image Width', 'trx_addons' ),
						'type'       => Controls_Manager::SLIDER,
						'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
						'selectors'  => array(
							'{{WRAPPER}} .widget_recent_posts .post_item .post_thumb' => 'width: {{SIZE}}{{UNIT}}; height: auto;',
						),
					)
				);

				$this->add_group_control(
					Group_Control_Border::get_type(),
					array(
						'name'     => 'image_border',
						'selector' => '{{WRAPPER}} .widget_recent_posts .post_item .post_thumb',
					)
				);
		
				$this->add_responsive_control(
					'image_radius',
					array(
						'label'      => __( 'Border Radius', 'trx_addons' ),
						'type'       => Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
						'selectors'  => array(
							'{{WRAPPER}} .widget_recent_posts .post_item .post_thumb' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
							'{{WRAPPER}} .widget_recent_posts .post_item .post_thumb img' => 'border-radius: 0',
						),
					)
				);

				$this->add_responsive_control(
					'image_margin',
					array(
						'label'      => __( 'Margin', 'trx_addons' ),
						'type'       => Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
						'selectors'  => array(
							'{{WRAPPER}} .widget_recent_posts .post_item .post_thumb' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						),
					)
				);

				$this->add_group_control(
					Group_Control_Box_Shadow::get_type(),
					array(
						'name'     => 'image_shadow',
						'selector' => '{{WRAPPER}} .widget_recent_posts .post_item .post_thumb',
					)
				);

				$this->end_controls_section();
			}

			/**
			 * Register style controls for the post content.
			 */
			protected function register_style_controls_post_content() {
				$this->start_controls_section(
					'section_recent_posts_style_post_content',
					array(
						'label' => __( 'Post Content', 'trx_addons' ),
						'tab' => Controls_Manager::TAB_STYLE,
					)
				);

				$this->add_responsive_control(
					'post_content_valign',
					array(
						'label'     => __( 'Vertical Alignment', 'trx_addons' ),
						'type'      => Controls_Manager::CHOOSE,
						'options'   => array(
							'flex-start'    => array(
								'title' => __( 'Top', 'trx_addons' ),
								'icon'  => 'eicon-v-align-top',
							),
							'center'        => array(
								'title' => __( 'Center', 'trx_addons' ),
								'icon'  => 'eicon-v-align-middle',
							),
							'flex-end'      => array(
								'title' => __( 'Bottom', 'trx_addons' ),
								'icon'  => 'eicon-v-align-bottom',
							),
						),
						'default'   => 'flex-start',
						'toggle'    => false,
						'selectors' => array(
							'{{WRAPPER}} .widget_recent_posts .post_item' => 'align-items: {{VALUE}};',
						),
					)
				);
		
				$this->add_group_control(
					Group_Control_Background::get_type(),
					array(
						'name'     => 'post_content_bg',
						'types'    => array( 'classic', 'gradient' ),
						'selector' => '{{WRAPPER}} .widget_recent_posts .post_item .post_content',
					)
				);
		
				$this->add_group_control(
					Group_Control_Border::get_type(),
					array(
						'name'     => 'post_content_border',
						'selector' => '{{WRAPPER}} .widget_recent_posts .post_item .post_content',
					)
				);
		
				$this->add_responsive_control(
					'post_content_radius',
					array(
						'label'      => __( 'Border Radius', 'trx_addons' ),
						'type'       => Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
						'selectors'  => array(
							'{{WRAPPER}} .widget_recent_posts .post_item .post_content' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
						),
					)
				);

				$this->add_responsive_control(
					'post_content_padding',
					array(
						'label'      => __( 'Padding', 'trx_addons' ),
						'type'       => Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
						'selectors'  => array(
							'{{WRAPPER}} .widget_recent_posts .post_item .post_content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						),
					)
				);

				$this->add_responsive_control(
					'post_content_margin',
					array(
						'label'      => __( 'Margin', 'trx_addons' ),
						'type'       => Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
						'selectors'  => array(
							'{{WRAPPER}} .widget_recent_posts .post_item .post_content' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						),
					)
				);

				$this->add_group_control(
					Group_Control_Box_Shadow::get_type(),
					array(
						'name'     => 'post_content_shadow',
						'selector' => '{{WRAPPER}} .widget_recent_posts .post_item .post_content',
					)
				);

				$this->end_controls_section();
			}

			/**
			 * Register style controls for the post title.
			 */
			protected function register_style_controls_title() {
				$this->start_controls_section(
					'section_recent_posts_style_title',
					array(
						'label' => __( 'Post Title', 'trx_addons' ),
						'tab' => Controls_Manager::TAB_STYLE,
					)
				);

				$this->add_control(
					'title_alignment',
					array(
						'label'     => __( 'Alignment', 'trx_addons' ),
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
						'default'   => '',
						// 'toggle'    => false,
						'selectors' => array(
							'{{WRAPPER}} .widget_recent_posts .post_title' => 'text-align: {{VALUE}}',
						),
					)
				);

				$this->add_group_control(
					Group_Control_Typography::get_type(),
					array(
						'name'     => 'title_typography',
						'selector' => '{{WRAPPER}} .widget_recent_posts .post_title',
					)
				);

				$this->start_controls_tabs( 'tabs_title_styles' );

				$this->start_controls_tab(
					'tab_title_style_normal',
					array(
						'label' => __( 'Normal', 'trx_addons' ),
					)
				);

				$this->add_group_control(
					Group_Control_Background::get_type(),
					array(
						'name'     => 'title_bg',
						'types'    => array( 'classic', 'gradient' ),
						'selector' => '{{WRAPPER}} .widget_recent_posts .post_title a',
					)
				);

				$this->add_control(
					'title_color',
					array(
						'label'     => __( 'Text Color', 'trx_addons' ),
						'type'      => Controls_Manager::COLOR,
						'selectors' => array(
							'{{WRAPPER}} .widget_recent_posts .post_title a' => 'color: {{VALUE}};',
						),
					)
				);

				$this->add_group_control(
					Group_Control_Text_Shadow::get_type(),
					array(
						'name'     => 'title_text_shadow',
						'selector' => '{{WRAPPER}} .widget_recent_posts .post_title a',
					)
				);

				$this->add_group_control(
					Group_Control_Border::get_type(),
					array(
						'name'     => 'title_border',
						'selector' => '{{WRAPPER}} .widget_recent_posts .post_title a',
					)
				);
		
				$this->add_responsive_control(
					'title_radius',
					array(
						'label'      => __( 'Border Radius', 'trx_addons' ),
						'type'       => Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
						'selectors'  => array(
							'{{WRAPPER}} .widget_recent_posts .post_title a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
						),
					)
				);

				$this->add_responsive_control(
					'title_padding',
					array(
						'label'      => __( 'Padding', 'trx_addons' ),
						'type'       => Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
						'selectors'  => array(
							'{{WRAPPER}} .widget_recent_posts .post_title a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						),
					)
				);

				$this->add_responsive_control(
					'title_margin',
					array(
						'label'      => __( 'Margin', 'trx_addons' ),
						'type'       => Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
						'selectors'  => array(
							'{{WRAPPER}} .widget_recent_posts .post_title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						),
					)
				);

				$this->add_group_control(
					Group_Control_Box_Shadow::get_type(),
					array(
						'name'     => 'title_shadow',
						'selector' => '{{WRAPPER}} .widget_recent_posts .post_title a',
					)
				);

				$this->end_controls_tab();

				$this->start_controls_tab(
					'tab_title_style_hover',
					array(
						'label' => __( 'Hover', 'trx_addons' ),
					)
				);

				$this->add_group_control(
					Group_Control_Background::get_type(),
					array(
						'name'     => 'title_bg_hover',
						'types'    => array( 'classic', 'gradient' ),
						'selector' => '{{WRAPPER}} .widget_recent_posts .post_title a:hover',
					)
				);

				$this->add_control(
					'title_color_hover',
					array(
						'label'     => __( 'Text Color', 'trx_addons' ),
						'type'      => Controls_Manager::COLOR,
						'selectors' => array(
							'{{WRAPPER}} .widget_recent_posts .post_title a:hover' => 'color: {{VALUE}};',
						),
					)
				);

				$this->add_group_control(
					Group_Control_Text_Shadow::get_type(),
					array(
						'name'     => 'title_text_shadow_hover',
						'selector' => '{{WRAPPER}} .widget_recent_posts .post_title a:hover',
					)
				);

				$this->add_group_control(
					Group_Control_Border::get_type(),
					array(
						'name'     => 'title_border_hover',
						'selector' => '{{WRAPPER}} .widget_recent_posts .post_title a:hover',
					)
				);

				$this->add_group_control(
					Group_Control_Box_Shadow::get_type(),
					array(
						'name'     => 'title_shadow_hover',
						'selector' => '{{WRAPPER}} .widget_recent_posts .post_title a:hover',
					)
				);

				$this->end_controls_tab();

				$this->end_controls_tabs();

				$this->end_controls_section();
			}

			/**
			 * Register style controls for the post categories.
			 */
			protected function register_style_controls_categories() {
				$this->start_controls_section(
					'section_recent_posts_style_categories',
					array(
						'label' => __( 'Post Categories', 'trx_addons' ),
						'tab' => Controls_Manager::TAB_STYLE,
						'condition' => [
							'show_categories' => '1',
						],
					)
				);

				$this->add_group_control(
					Group_Control_Typography::get_type(),
					array(
						'name'     => 'categories_typography',
						'selector' => '{{WRAPPER}} .widget_recent_posts .post_categories',
					)
				);

				$this->add_responsive_control(
					'categories_block_margin',
					array(
						'label'      => __( 'Block Margin', 'trx_addons' ),
						'type'       => Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
						'selectors'  => array(
							'{{WRAPPER}} .widget_recent_posts .post_categories' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						),
					)
				);

				$this->add_control(
					'hide_categories_delimiter',
					array(
						'label' => __( 'Hide Delimiter', 'trx_addons' ),
						'label_block' => false,
						'type' => Controls_Manager::SWITCHER,
						'label_off' => __( 'Off', 'trx_addons' ),
						'label_on' => __( 'On', 'trx_addons' ),
						'return_value' => '1'
					)
				);

				$this->start_controls_tabs( 'tabs_categories_styles' );

				$this->start_controls_tab(
					'tab_categories_style_normal',
					array(
						'label' => __( 'Normal', 'trx_addons' ),
					)
				);

				$this->add_group_control(
					Group_Control_Background::get_type(),
					array(
						'name'     => 'categories_bg',
						'types'    => array( 'classic', 'gradient' ),
						'selector' => '{{WRAPPER}} .widget_recent_posts .post_categories > a',
					)
				);

				$this->add_control(
					'categories_color',
					array(
						'label'     => __( 'Text Color', 'trx_addons' ),
						'type'      => Controls_Manager::COLOR,
						'selectors' => array(
							'{{WRAPPER}} .widget_recent_posts .post_categories > a' => 'color: {{VALUE}};',
						),
					)
				);

				$this->add_group_control(
					Group_Control_Text_Shadow::get_type(),
					array(
						'name'     => 'categories_text_shadow',
						'selector' => '{{WRAPPER}} .widget_recent_posts .post_categories > a',
					)
				);

				$this->add_group_control(
					Group_Control_Border::get_type(),
					array(
						'name'     => 'categories_border',
						'selector' => '{{WRAPPER}} .widget_recent_posts .post_categories > a',
					)
				);
		
				$this->add_responsive_control(
					'categories_radius',
					array(
						'label'      => __( 'Border Radius', 'trx_addons' ),
						'type'       => Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
						'selectors'  => array(
							'{{WRAPPER}} .widget_recent_posts .post_categories > a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
						),
					)
				);

				$this->add_responsive_control(
					'categories_padding',
					array(
						'label'      => __( 'Padding', 'trx_addons' ),
						'type'       => Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
						'selectors'  => array(
							'{{WRAPPER}} .widget_recent_posts .post_categories > a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						),
					)
				);

				$this->add_responsive_control(
					'categories_margin',
					array(
						'label'      => __( 'Margin', 'trx_addons' ),
						'type'       => Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
						'selectors'  => array(
							'{{WRAPPER}} .widget_recent_posts .post_categories > a' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						),
					)
				);

				$this->add_group_control(
					Group_Control_Box_Shadow::get_type(),
					array(
						'name'     => 'categories_shadow',
						'selector' => '{{WRAPPER}} .widget_recent_posts .post_categories > a',
					)
				);

				$this->end_controls_tab();

				$this->start_controls_tab(
					'tab_categories_style_hover',
					array(
						'label' => __( 'Hover', 'trx_addons' ),
					)
				);

				$this->add_group_control(
					Group_Control_Background::get_type(),
					array(
						'name'     => 'categories_bg_hover',
						'types'    => array( 'classic', 'gradient' ),
						'selector' => '{{WRAPPER}} .widget_recent_posts .post_categories > a:hover',
					)
				);

				$this->add_control(
					'categories_color_hover',
					array(
						'label'     => __( 'Text Color', 'trx_addons' ),
						'type'      => Controls_Manager::COLOR,
						'selectors' => array(
							'{{WRAPPER}} .widget_recent_posts .post_categories > a:hover' => 'color: {{VALUE}};',
						),
					)
				);

				$this->add_group_control(
					Group_Control_Text_Shadow::get_type(),
					array(
						'name'     => 'categories_text_shadow_hover',
						'selector' => '{{WRAPPER}} .widget_recent_posts .post_categories > a:hover',
					)
				);

				$this->add_group_control(
					Group_Control_Border::get_type(),
					array(
						'name'     => 'categories_border_hover',
						'selector' => '{{WRAPPER}} .widget_recent_posts .post_categories > a:hover',
					)
				);

				$this->add_group_control(
					Group_Control_Box_Shadow::get_type(),
					array(
						'name'     => 'categories_shadow_hover',
						'selector' => '{{WRAPPER}} .widget_recent_posts .post_categories > a:hover',
					)
				);

				$this->end_controls_tab();

				$this->end_controls_tabs();

				$this->end_controls_section();
			}

			/**
			 * Register style controls for the post views counter.
			 */
			protected function register_style_controls_counters() {
				$this->start_controls_section(
					'section_recent_posts_style_counters',
					array(
						'label' => __( 'Post View Count', 'trx_addons' ),
						'tab' => Controls_Manager::TAB_STYLE,
						'condition' => [
							'show_counters' => '1',
						],
					)
				);

				$this->add_group_control(
					Group_Control_Typography::get_type(),
					array(
						'name'     => 'counters_typography',
						'selector' => '{{WRAPPER}} .widget_recent_posts .post_categories .post_meta_views',
					)
				);

				$this->start_controls_tabs( 'tabs_counters_styles' );

				$this->start_controls_tab(
					'tab_counters_style_normal',
					array(
						'label' => __( 'Normal', 'trx_addons' ),
					)
				);

				$this->add_group_control(
					Group_Control_Background::get_type(),
					array(
						'name'     => 'counters_bg',
						'types'    => array( 'classic', 'gradient' ),
						'selector' => '{{WRAPPER}} .widget_recent_posts .post_categories .post_meta_views',
					)
				);

				$this->add_control(
					'counters_color',
					array(
						'label'     => __( 'Text Color', 'trx_addons' ),
						'type'      => Controls_Manager::COLOR,
						'selectors' => array(
							'{{WRAPPER}} .widget_recent_posts .post_categories .post_meta_views' => 'color: {{VALUE}};',
						),
					)
				);

				$this->add_group_control(
					Group_Control_Text_Shadow::get_type(),
					array(
						'name'     => 'counters_text_shadow',
						'selector' => '{{WRAPPER}} .widget_recent_posts .post_categories .post_meta_views',
					)
				);

				$this->add_group_control(
					Group_Control_Border::get_type(),
					array(
						'name'     => 'counters_border',
						'selector' => '{{WRAPPER}} .widget_recent_posts .post_categories .post_meta_views',
					)
				);
		
				$this->add_responsive_control(
					'counters_radius',
					array(
						'label'      => __( 'Border Radius', 'trx_addons' ),
						'type'       => Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
						'selectors'  => array(
							'{{WRAPPER}} .widget_recent_posts .post_categories .post_meta_views' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
						),
					)
				);

				$this->add_responsive_control(
					'counters_padding',
					array(
						'label'      => __( 'Padding', 'trx_addons' ),
						'type'       => Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
						'selectors'  => array(
							'{{WRAPPER}} .widget_recent_posts .post_categories .post_meta_views' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						),
					)
				);

				$this->add_responsive_control(
					'counters_margin',
					array(
						'label'      => __( 'Margin', 'trx_addons' ),
						'type'       => Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
						'selectors'  => array(
							'{{WRAPPER}} .widget_recent_posts .post_categories .post_meta_views' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						),
					)
				);

				$this->add_group_control(
					Group_Control_Box_Shadow::get_type(),
					array(
						'name'     => 'counters_shadow',
						'selector' => '{{WRAPPER}} .widget_recent_posts .post_categories .post_meta_views',
					)
				);

				$this->end_controls_tab();

				$this->start_controls_tab(
					'tab_counters_style_hover',
					array(
						'label' => __( 'Hover', 'trx_addons' ),
					)
				);

				$this->add_group_control(
					Group_Control_Background::get_type(),
					array(
						'name'     => 'counters_bg_hover',
						'types'    => array( 'classic', 'gradient' ),
						'selector' => '{{WRAPPER}} .widget_recent_posts .post_categories .post_meta_views:hover',
					)
				);

				$this->add_control(
					'counters_color_hover',
					array(
						'label'     => __( 'Text Color', 'trx_addons' ),
						'type'      => Controls_Manager::COLOR,
						'selectors' => array(
							'{{WRAPPER}} .widget_recent_posts .post_categories .post_meta_views:hover' => 'color: {{VALUE}};',
						),
					)
				);

				$this->add_group_control(
					Group_Control_Text_Shadow::get_type(),
					array(
						'name'     => 'counters_text_shadow_hover',
						'selector' => '{{WRAPPER}} .widget_recent_posts .post_categories .post_meta_views:hover',
					)
				);

				$this->add_group_control(
					Group_Control_Border::get_type(),
					array(
						'name'     => 'counters_border_hover',
						'selector' => '{{WRAPPER}} .widget_recent_posts .post_categories .post_meta_views:hover',
					)
				);

				$this->add_group_control(
					Group_Control_Box_Shadow::get_type(),
					array(
						'name'     => 'counters_shadow_hover',
						'selector' => '{{WRAPPER}} .widget_recent_posts .post_categories .post_meta_views:hover',
					)
				);

				$this->end_controls_tab();

				$this->end_controls_tabs();

				$this->end_controls_section();
			}

			/**
			 * Register style controls for the post date and author.
			 */
			protected function register_style_controls_meta() {
				$this->start_controls_section(
					'section_recent_posts_style_meta',
					array(
						'label' => __( 'Post Date & Author', 'trx_addons' ),
						'tab' => Controls_Manager::TAB_STYLE,
						'conditions' => array(
							'relation' => 'or',
							'terms' => array(
								array(
									'name'     => 'show_date',
									'operator' => '==',
									'value'    => '1',
								),
								array(
									'name'     => 'show_author',
									'operator' => '==',
									'value'    => '1',
								),
							),
						),
					)
				);

				$this->add_group_control(
					Group_Control_Typography::get_type(),
					array(
						'name'     => 'meta_typography',
						'selector' => '{{WRAPPER}} .widget_recent_posts .post_info',
					)
				);

				$this->add_control(
					'meta_alignment',
					array(
						'label'     => __( 'Alignment', 'trx_addons' ),
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
						'default'   => '',
						// 'toggle'    => false,
						'selectors' => array(
							'{{WRAPPER}} .widget_recent_posts .post_info' => 'text-align: {{VALUE}}',
						),
					)
				);

				$this->start_controls_tabs( 'tabs_meta_styles' );

				$this->start_controls_tab(
					'tab_meta_style_normal',
					array(
						'label' => __( 'Normal', 'trx_addons' ),
					)
				);

				$this->add_group_control(
					Group_Control_Background::get_type(),
					array(
						'name'     => 'meta_bg',
						'types'    => array( 'classic', 'gradient' ),
						'selector' => '{{WRAPPER}} .widget_recent_posts .post_info',
					)
				);

				$this->add_control(
					'meta_color',
					array(
						'label'     => __( 'Text Color', 'trx_addons' ),
						'type'      => Controls_Manager::COLOR,
						'selectors' => array(
							'{{WRAPPER}} .widget_recent_posts .post_info .post_info_item' => 'color: {{VALUE}};',
						),
					)
				);

				$this->add_control(
					'meta_link_color',
					array(
						'label'     => __( 'Link Color', 'trx_addons' ),
						'type'      => Controls_Manager::COLOR,
						'selectors' => array(
							'{{WRAPPER}} .widget_recent_posts .post_info .post_info_item a' => 'color: {{VALUE}};',
						),
					)
				);

				$this->add_group_control(
					Group_Control_Text_Shadow::get_type(),
					array(
						'name'     => 'meta_text_shadow',
						'selector' => '{{WRAPPER}} .widget_recent_posts .post_info .post_info_item',
					)
				);

				$this->add_group_control(
					Group_Control_Border::get_type(),
					array(
						'name'     => 'meta_border',
						'selector' => '{{WRAPPER}} .widget_recent_posts .post_info',
					)
				);
		
				$this->add_responsive_control(
					'meta_radius',
					array(
						'label'      => __( 'Border Radius', 'trx_addons' ),
						'type'       => Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
						'selectors'  => array(
							'{{WRAPPER}} .widget_recent_posts .post_info' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
						),
					)
				);

				$this->add_responsive_control(
					'meta_padding',
					array(
						'label'      => __( 'Padding', 'trx_addons' ),
						'type'       => Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
						'selectors'  => array(
							'{{WRAPPER}} .widget_recent_posts .post_info' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						),
					)
				);

				$this->add_responsive_control(
					'meta_margin',
					array(
						'label'      => __( 'Margin', 'trx_addons' ),
						'type'       => Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
						'selectors'  => array(
							'{{WRAPPER}} .widget_recent_posts .post_info' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						),
					)
				);

				$this->add_group_control(
					Group_Control_Box_Shadow::get_type(),
					array(
						'name'     => 'meta_shadow',
						'selector' => '{{WRAPPER}} .widget_recent_posts .post_info',
					)
				);

				$this->end_controls_tab();

				$this->start_controls_tab(
					'tab_meta_style_hover',
					array(
						'label' => __( 'Hover', 'trx_addons' ),
					)
				);

				$this->add_group_control(
					Group_Control_Background::get_type(),
					array(
						'name'     => 'meta_bg_hover',
						'types'    => array( 'classic', 'gradient' ),
						'selector' => '{{WRAPPER}} .widget_recent_posts .post_info:hover',
					)
				);

				$this->add_control(
					'meta_color_hover',
					array(
						'label'     => __( 'Text Color', 'trx_addons' ),
						'type'      => Controls_Manager::COLOR,
						'selectors' => array(
							'{{WRAPPER}} .widget_recent_posts .post_info:hover .post_info_item' => 'color: {{VALUE}};',
						),
					)
				);

				$this->add_control(
					'meta_link_color_hover',
					array(
						'label'     => __( 'Link Color', 'trx_addons' ),
						'type'      => Controls_Manager::COLOR,
						'selectors' => array(
							'{{WRAPPER}} .widget_recent_posts .post_info .post_info_item a:hover' => 'color: {{VALUE}};',
						),
					)
				);

				$this->add_group_control(
					Group_Control_Text_Shadow::get_type(),
					array(
						'name'     => 'meta_text_shadow_hover',
						'selector' => '{{WRAPPER}} .widget_recent_posts .post_info:hover .post_info_item',
					)
				);

				$this->add_group_control(
					Group_Control_Border::get_type(),
					array(
						'name'     => 'meta_border_hover',
						'selector' => '{{WRAPPER}} .widget_recent_posts .post_info:hover',
					)
				);

				$this->add_group_control(
					Group_Control_Box_Shadow::get_type(),
					array(
						'name'     => 'meta_shadow_hover',
						'selector' => '{{WRAPPER}} .widget_recent_posts .post_info:hover',
					)
				);

				$this->end_controls_tab();

				$this->end_controls_tabs();

				$this->end_controls_section();
			}
		}
		
		// Register widget
		trx_addons_elm_register_widget( 'TRX_Addons_Elementor_Widget_Recent_Posts' );
	}
}


// Disable our widgets (shortcodes) to use in Elementor
// because we create special Elementor's widgets instead
if (!function_exists('trx_addons_widget_recent_posts_black_list')) {
	add_action( 'elementor/widgets/black_list', 'trx_addons_widget_recent_posts_black_list' );
	function trx_addons_widget_recent_posts_black_list($list) {
		$list[] = 'trx_addons_widget_recent_posts';
		return $list;
	}
}
