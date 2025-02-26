<?php
/**
 * Widget: Posts or Revolution slider (Elementor support)
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


// Elementor Widget: Slider
//------------------------------------------------------
if ( ! function_exists( 'trx_addons_sc_slider_add_in_elementor' ) ) {
	add_action( trx_addons_elementor_get_action_for_widgets_registration(), 'trx_addons_sc_slider_add_in_elementor' );
	function trx_addons_sc_slider_add_in_elementor() {
		
		if ( ! class_exists( 'TRX_Addons_Elementor_Widget' ) ) return;	

		class TRX_Addons_Elementor_Widget_Slider extends TRX_Addons_Elementor_Widget {

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
					'height' => 'size+unit',
					'slides_per_view' => 'size',
					'slides_per_view_widescreen' => 'size',
					'slides_per_view_desktop' => 'size',
					'slides_per_view_laptop' => 'size',
					'slides_per_view_tablet_extra' => 'size',
					'slides_per_view_tablet' => 'size',
					'slides_per_view_mobile_extra' => 'size',
					'slides_per_view_mobile' => 'size',
					'slides_space' => 'size',
					'slides_space_widescreen' => 'size',
					'slides_space_desktop' => 'size',
					'slides_space_laptop' => 'size',
					'slides_space_tablet_extra' => 'size',
					'slides_space_tablet' => 'size',
					'slides_space_mobile_extra' => 'size',
					'slides_space_mobile' => 'size',
					'slides_parallax' => 'size',
					'speed' => 'size',
					'interval' => 'size',
					'controller_per_view' => 'size',
					'controller_per_view_widescreen' => 'size',
					'controller_per_view_desktop' => 'size',
					'controller_per_view_laptop' => 'size',
					'controller_per_view_tablet_extra' => 'size',
					'controller_per_view_tablet' => 'size',
					'controller_per_view_mobile_extra' => 'size',
					'controller_per_view_mobile' => 'size',
					'controller_space' => 'size',
					'controller_space_widescreen' => 'size',
					'controller_space_desktop' => 'size',
					'controller_space_laptop' => 'size',
					'controller_space_tablet_extra' => 'size',
					'controller_space_tablet' => 'size',
					'controller_space_mobile_extra' => 'size',
					'controller_space_mobile' => 'size',
					'controller_height' => 'size+unit',
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
				return 'trx_widget_slider';
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
				return __( 'Slider', 'trx_addons' );
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
				return [ 'slider', 'carousel', 'gallery', 'swiper', 'revslider', 'revo', 'elastistack' ];
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
				return 'eicon-slides trx_addons_elementor_widget_icon';
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

				$styles_allowed = apply_filters( 'trx_addons_filter_allow_sc_styles_in_elementor', false, 'widget_slider' );
				$this->register_content_controls_slider();
				$this->register_content_controls_query();
				$this->register_content_controls_layout( $styles_allowed ? Controls_Manager::TAB_CONTENT : Controls_Manager::TAB_LAYOUT );
				$this->register_content_controls_controls( $styles_allowed ? Controls_Manager::TAB_CONTENT : Controls_Manager::TAB_LAYOUT );
				$this->register_content_controls_controller( $styles_allowed ? Controls_Manager::TAB_CONTENT : Controls_Manager::TAB_LAYOUT );

				if ( $styles_allowed ) {
					$this->register_style_controls_widget_title();
					$this->register_style_controls_slide();
					$this->register_style_controls_slide_subtitle();
					$this->register_style_controls_slide_title();
					$this->register_style_controls_slide_meta();
					$this->register_style_controls_slide_content();
					$this->register_style_controls_arrows();
					$this->register_style_controls_pagination();
					$this->register_style_controls_controller_slide();
					$this->register_style_controls_controller_slide_image();
					$this->register_style_controls_controller_slide_subtitle();
					$this->register_style_controls_controller_slide_title();
					$this->register_style_controls_controller_slide_meta();
					$this->register_style_controls_controller_arrows();
				}
			}

			/**
			 * Register content controls with general settings.
			 */
			protected function register_content_controls_slider() {
				// Detect edit mode
				$is_edit_mode = trx_addons_elm_is_edit_mode();

				$this->start_controls_section(
					'section_sc_slider',
					[
						'label' => __( 'Slider', 'trx_addons' ),
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
					'engine',
					[
						'label' => __( 'Slider engine', 'trx_addons' ),
						'label_block' => false,
						'type' => Controls_Manager::SELECT,
						'options' => ! $is_edit_mode ? array() : trx_addons_get_list_sc_slider_engines(),
						'default' => 'swiper'
					]
				);
				
				if (trx_addons_exists_revslider()) {
					$this->add_control(
						'alias',
						[
							'label' => __( 'RevSlider alias', 'trx_addons' ),
							'label_block' => false,
							'type' => Controls_Manager::SELECT,
							'options' => ! $is_edit_mode ? array() : trx_addons_get_list_revsliders(),
							'default' => '',
							'condition' => [
								'engine' => 'revo'
							]
						]
					);
				}

				$this->add_control(
					'slider_style',
					[
						'label' => __( 'Swiper style', 'trx_addons' ),
						'label_block' => false,
						'type' => Controls_Manager::SELECT,
						'options' => trx_addons_components_get_allowed_layouts('widgets', 'slider'),
						'default' => 'default',
						'condition' => [
							'engine' => 'swiper'
						]
					]
				);

				$this->add_control(
					'effect',
					[
						'label' => __( 'Swiper effect', 'trx_addons' ),
						'label_block' => false,
						'type' => Controls_Manager::SELECT,
						'options' => ! $is_edit_mode ? array() : trx_addons_get_list_sc_slider_effects(),
						'default' => 'slide',
						'condition' => [
							'engine' => 'swiper'
						]
					]
				);

				$this->add_control(
					'direction',
					[
						'label' => __( 'Slides change direction', 'trx_addons' ),
						'label_block' => false,
						'type' => Controls_Manager::SELECT,
						'options' => ! $is_edit_mode ? array() : trx_addons_get_list_sc_slider_directions(),
						'default' => 'horizontal',
						'condition' => [
							'engine' => 'swiper',
							'effect' => ['slide', 'coverflow', 'swap']
						]
					]
				);

				$this->add_responsive_control(
					'slides_per_view',
					[
						'label' => __( 'Slides per view', 'trx_addons' ),
						'type' => Controls_Manager::SLIDER,
						'default' => [
							'size' => 1
						],
						'range' => [
							'px' => [
								'min' => 1,
								'max' => 10
							],
						],
						'condition' => [
							'engine' => 'swiper',
							'effect' => ['slide', 'coverflow', 'swap', 'cards', 'creative']
						]
					]
				);

				$this->add_responsive_control(
					'slides_space',
					[
						'label' => __( 'Space between slides', 'trx_addons' ),
						'type' => Controls_Manager::SLIDER,
						'default' => [
							'size' => 0
						],
						'range' => [
							'px' => [
								'min' => 0,
								'max' => 100
							],
						],
						'condition' => [
							'engine' => 'swiper',
							'effect' => ['slide', 'coverflow', 'swap', 'cards', 'creative']
						]
					]
				);

				$this->add_control(
					'slides_parallax',
					[
						'label' => __( 'Parallax coeff', 'trx_addons' ),
						'type' => Controls_Manager::SLIDER,
						'default' => [
							'size' => 0
						],
						'range' => [
							'px' => [
								'min' => 0,
								'max' => 1,
								'step' => 0.01
							],
						],
						'condition' => [
							'engine' => 'swiper',
							'effect' => ['slide'],
							'slides_per_view' => 1
						]
					]
				);


				$this->add_control(
					'slave_id',
					[
						'label' => __( 'Slave ID', 'trx_addons' ),
						'label_block' => false,
						'description' => wp_kses_data(__('Specify the ID of the dependent slider if you want its slides to change when you switch slides of this slider.', 'trx_addons')),
						'type' => Controls_Manager::TEXT,
						'placeholder' => __( "Controlled slider ID", 'trx_addons' ),
						'default' => '',
						'condition' => [
							'engine' => 'swiper'
						]
					]
				);

				$this->end_controls_section();
			}


			/**
			 * Register content controls with a query settings or custom slides.
			 */
			protected function register_content_controls_query() {
				// Detect edit mode
				$is_edit_mode = trx_addons_elm_is_edit_mode();
				// If open params in Elementor Editor
				$params = $this->get_sc_params();
				// Prepare lists
				$post_type = !empty($params['post_type']) ? $params['post_type'] : 'post';
				$taxonomy = !empty($params['taxonomy']) ? $params['taxonomy'] : 'category';
				$tax_obj = get_taxonomy($taxonomy);

				$this->start_controls_section(
					'section_sc_slider_query',
					[
						'label' => __( 'Query or Custom slides', 'trx_addons' ),
					]
				);

				$this->add_control(
					'post_type',
					[
						'label' => __( 'Post type', 'trx_addons' ),
						'label_block' => false,
						'type' => Controls_Manager::SELECT,
						'options' => ! $is_edit_mode ? array() : trx_addons_get_list_posts_types(),
						'default' => 'post',
						'condition' => [
							'engine' => ['swiper', 'elastistack']
						]
					]
				);

				$this->add_control(
					'taxonomy',
					[
						'label' => __( 'Taxonomy', 'trx_addons' ),
						'label_block' => false,
						'type' => Controls_Manager::SELECT,
						'options' => ! $is_edit_mode
										? array()
										: trx_addons_get_list_taxonomies(false, $post_type),
						'default' => 'category',
						'condition' => [
							'engine' => ['swiper', 'elastistack']
						]
					]
				);

				$this->add_control(
					'category',
					[
						'label' => __( 'Category', 'trx_addons' ),
						'label_block' => false,
						'type' => Controls_Manager::SELECT,
						'options' => ! $is_edit_mode
										? array()
											// Make keys as string (add a space after the number) to preserve the order in the list
											// (otherwise the keys will be converted to numbers in the JS and the order will be broken)
										: trx_addons_array_make_string_keys(
												trx_addons_array_merge(
													array( 0 => trx_addons_get_not_selected_text( ! empty( $tax_obj->label ) ? $tax_obj->label : __( '- Not Selected -', 'trx_addons' ) ) ),
													array_map( 'strip_tags', $taxonomy == 'category' 
														? trx_addons_get_list_categories() 
														: trx_addons_get_list_terms(false, $taxonomy)
													)
												)
											),
						'default' => '0',
						'condition' => [
							'engine' => ['swiper', 'elastistack']
						]
					]
				);

				$this->add_control(
					'posts',
					[
						'label' => __( 'Posts number', 'trx_addons' ),
						'description' => wp_kses_data( __("Number of posts or comma separated post's IDs to show images", 'trx_addons') ),
						'type' => Controls_Manager::TEXT,
						'default' => '5',
						'condition' => [
							'engine' => ['swiper', 'elastistack']
						]
					]
				);

				// Custom slides
				//----------------------

				$this->add_control(
					'slides_heading',
					[
						'label' => __( 'or create Custom Slides', 'trx_addons' ),
						'type' => Controls_Manager::HEADING,
						'separator' => 'before',
						'condition' => [
							'engine' => ['swiper', 'elastistack']
						]
					]
				);

				$this->add_control(
					'slides_description',
					[
						'raw' => __( "If you don't want to use posts, you can create custom slides. As soon as you add at least one slide and select an image for it - the fields above (the query section) will stop applying.", 'trx_addons' ),
						'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
						'type' => Controls_Manager::RAW_HTML,
					]
				);
				
				$this->add_control(
					'slides',
					[
						'label' => '',
						'type' => Controls_Manager::REPEATER,
						'separator' => 'before',
						'condition' => [
							'engine' => ['swiper', 'elastistack']
						],
						'fields' => apply_filters('trx_addons_sc_param_group_params',
							[
								[
									'name' => 'title',
									'label' => __( 'Title', 'trx_addons' ),
									'label_block' => false,
									'type' => Controls_Manager::TEXT,
									'placeholder' => __( "Slide's title", 'trx_addons' ),
									'default' => ''
								],
								[
									'name' => 'subtitle',
									'label' => __( 'Subtitle', 'trx_addons' ),
									'label_block' => false,
									'type' => Controls_Manager::TEXT,
									'placeholder' => __( "Slide's subtitle", 'trx_addons' ),
									'default' => ''
								],
								[
									'name' => 'meta',
									'label' => __( 'Meta', 'trx_addons' ),
									'label_block' => false,
									'type' => Controls_Manager::TEXT,
									'placeholder' => __( "Slide's meta", 'trx_addons' ),
									'default' => ''
								],
								[
									'name' => 'content',
									'label' => __( 'Content', 'trx_addons' ),
									'label_block' => true,
									'type' => Controls_Manager::WYSIWYG,
									'placeholder' => __( "Slide's content", 'trx_addons' ),
									'default' => ''
								],
								[
									'name' => 'link',
									'label' => __( 'Link', 'trx_addons' ),
									'label_block' => false,
									'type' => Controls_Manager::URL,
									'default' => ['url' => ''],
									'placeholder' => __( '//your-link.com', 'trx_addons' ),
								],
								[
									'name' => 'image',
									'label' => __( 'Image', 'trx_addons' ),
									'type' => Controls_Manager::MEDIA,
									'default' => [
										'url' => '',
									],
								],
								[
									'name' => 'bg_color',
									'label' => __( 'Background Color', 'trx_addons' ),
									'type' => Controls_Manager::COLOR,
									'default' => '',
									// 'global' => array(
									// 	'active' => false,
									// ),
								],
								[
									'name' => 'video_url',
									'label' => __( 'Video URL', 'trx_addons' ),
									'label_block' => false,
									'description' => __( 'Enter link to the video (Note: read more about available formats at WordPress Codex page)', 'trx_addons' ),
									'type' => Controls_Manager::TEXT,
									'default' => '',
								],
								[
									'name' => 'video_embed',
									'label' => __( 'Video embed code', 'trx_addons' ),
									'label_block' => true,
									'description' => __( 'or paste the HTML code to embed video in this slide', 'trx_addons' ),
									'type' => Controls_Manager::TEXTAREA,
									'rows' => 10,
									'separator' => 'none',
									'default' => '',
								]
							],
							'trx_widget_slider'
						),
						'title_field' => '{{{ title }}}',
					]
				);

				$this->end_controls_section();
			}


			/**
			 * Register content controls with a layout settings
			 */
			protected function register_content_controls_layout( $tab ) {
				// Detect edit mode
				$is_edit_mode = trx_addons_elm_is_edit_mode();

				$this->start_controls_section(
					'section_sc_slider_layout',
					[
						'label' => __( 'Layout', 'trx_addons' ),
						'tab' => $tab
					]
				);

				$this->add_control(
					'slides_type',
					[
						'label' => __( 'Type of the slides content', 'trx_addons' ),
						'label_block' => false,
						'description' => wp_kses_data( __("Use images from slides as background (default) or insert it as tag inside each slide", 'trx_addons') ),
						'type' => Controls_Manager::SELECT,
						'options' => array(
							'bg' => esc_html__('Background', 'trx_addons'),
							'images' => esc_html__('Image tag', 'trx_addons')
						),
						'default' => 'bg',
						'condition' => [
							'engine' => ['swiper', 'elastistack']
						]
					]
				);

				$this->add_control(
					'noresize',
					[
						'label' => __( "No resize slide's content", 'trx_addons' ),
						'label_block' => false,
						'description' => wp_kses_data( __("Disable resize slide's content, stretch images to cover slide", 'trx_addons') ),
						'type' => Controls_Manager::SWITCHER,
						'label_off' => __( 'Off', 'trx_addons' ),
						'label_on' => __( 'On', 'trx_addons' ),
						'return_value' => '1',
						'condition' => [
							'engine' => ['swiper', 'elastistack']
						]
					]
				);
		
				$this->add_control(
					'height',
					[
						'label' => __( 'Slider height', 'trx_addons' ),
						'description' => wp_kses_data( __("Initial height of the slider. If empty - calculate from width and aspect ratio", 'trx_addons') ),
						'type' => Controls_Manager::SLIDER,
						'default' => [
							'size' => '',
							'unit' => 'px'
						],
						'range' => [
							'px' => [
								'min' => 50,
								'max' => 1000
							],
							'em' => [
								'min' => 2,
								'max' => 100
							],
							'vh' => [
								'min' => 0,
								'max' => 100
							]
						],
						'size_units' => [ 'px', 'em', 'vh' ],
						'condition' => [
							'noresize' => '1'
						]
					]
				);

				$this->add_control(
					'slides_ratio',
					[
						'label' => __( 'Slides ratio', 'trx_addons' ),
						'label_block' => false,
						'type' => Controls_Manager::TEXT,
						'placeholder' => __( "Ratio", 'trx_addons' ),
						'default' => '16:9',
						'condition' => [
							'noresize' => ''
						]
					]
				);

				$this->add_control(
					'slides_centered',
					[
						'label' => __( 'Slides centered', 'trx_addons' ),
						'label_block' => false,
						'description' => wp_kses_data( __("Used only for even number of slides per view", 'trx_addons') ),
						'type' => Controls_Manager::SWITCHER,
						'label_off' => __( 'Off', 'trx_addons' ),
						'label_on' => __( 'On', 'trx_addons' ),
						'return_value' => '1',
						'condition' => [
							'engine' => ['swiper']
						]
					]
				);

				$this->add_control(
					'slides_overflow',
					[
						'label' => __( 'Slides overflow visible', 'trx_addons' ),
						'label_block' => false,
						'type' => Controls_Manager::SWITCHER,
						'label_off' => __( 'Off', 'trx_addons' ),
						'label_on' => __( 'On', 'trx_addons' ),
						'return_value' => '1',
						'condition' => [
							'engine' => ['swiper']
						]
					]
				);

				$this->add_control(
					'titles',
					[
						'label' => __( 'Titles in the slides', 'trx_addons' ),
						'label_block' => false,
						'description' => wp_kses_data( __("Show post's titles and categories on the slides", 'trx_addons') ),
						'type' => Controls_Manager::SELECT,
						'options' => ! $is_edit_mode ? array() : trx_addons_get_list_sc_slider_titles(),
						'default' => 'center',
						'condition' => [
							'engine' => ['swiper', 'elastistack']
						]
					]
				);

				$this->add_control(
					'large',
					[
						'label' => __( 'Large titles', 'trx_addons' ),
						'label_block' => false,
						'type' => Controls_Manager::SWITCHER,
						'label_off' => __( 'Off', 'trx_addons' ),
						'label_on' => __( 'On', 'trx_addons' ),
						'return_value' => '1',
						'condition' => [
							'engine' => ['swiper', 'elastistack']
						]
					]
				);

				$this->end_controls_section();
			}


			/**
			 * Register content controls with a controls settings
			 */
			protected function register_content_controls_controls( $tab ) {
				// Detect edit mode
				$is_edit_mode = trx_addons_elm_is_edit_mode();

				$this->start_controls_section(
					'section_sc_slider_controls',
					[
						'label' => __( 'Controls', 'trx_addons' ),
						'tab' => $tab
					]
				);

				$this->add_control(
					'controls',
					[
						'label' => __( 'Arrows', 'trx_addons' ),
						'label_block' => false,
						'type' => Controls_Manager::SWITCHER,
						'label_off' => __( 'Off', 'trx_addons' ),
						'label_on' => __( 'On', 'trx_addons' ),
						'return_value' => '1',
						'condition' => [
							'engine' => ['swiper', 'elastistack']
						]
					]
				);

				$this->add_control(
					'controls_pos',
					[
						'label' => __( 'Arrows position', 'trx_addons' ),
						'label_block' => false,
						'type' => Controls_Manager::SELECT,
						'options' => ! $is_edit_mode ? array() : trx_addons_get_list_sc_slider_controls(''),
						'default' => 'side',
						'condition' => [
							'engine' => ['swiper'],
							'controls' => '1'
						]
					]
				);

				$this->add_control(
					'controls_visibility',
					[
						'label' => __( 'Arrows visible', 'trx_addons' ),
						'label_block' => false,
						'type' => Controls_Manager::SWITCHER,
						'label_off' => __( 'Hover', 'trx_addons' ),
						'label_on' => __( 'Always', 'trx_addons' ),
						'return_value' => '1',
						'selectors' => [
							'{{WRAPPER}} .slider_container.slider_controls_side .slider_controls_wrap > .slider_next,
							 {{WRAPPER}} .slider_outer_controls_side .slider_controls_wrap > .slider_next' => 'opacity: 1; margin-right: 0;',
							'{{WRAPPER}} .slider_container.slider_controls_side .slider_controls_wrap > .slider_prev,
							 {{WRAPPER}} .slider_outer_controls_side .slider_controls_wrap > .slider_prev' => 'opacity: 1; margin-left: 0;',
						],
						'condition' => [
							'engine' => ['swiper'],
							'controls' => '1',
							'controls_pos' => 'side'
						]
					]
				);

				$this->add_control(
					'label_prev',
					[
						'label' => __( 'Prev Slide', 'trx_addons' ),
						'label_block' => false,
						'description' => wp_kses_data( __("Label of the 'Prev Slide' button in the Swiper (Modern style). Use '|' to break line", 'trx_addons') ),
						'type' => Controls_Manager::TEXT,
						'placeholder' => __( "Prev Slide", 'trx_addons' ),
						'default' => esc_html__('Prev|PHOTO', 'trx_addons'),
						'condition' => [
							'controls' => '1',
							'slider_style' => 'modern'
						]
					]
				);

				$this->add_control(
					'label_next',
					[
						'label' => __( 'Next Slide', 'trx_addons' ),
						'label_block' => false,
						'description' => wp_kses_data( __("Label of the 'Next Slide' button in the Swiper (Modern style). Use '|' to break line", 'trx_addons') ),
						'type' => Controls_Manager::TEXT,
						'placeholder' => __( "Next Slide", 'trx_addons' ),
						'default' => esc_html__('Next|PHOTO', 'trx_addons'),
						'condition' => [
							'controls' => '1',
							'slider_style' => 'modern'
						]
					]
				);

				$this->add_control(
					'pagination',
					[
						'label' => __( 'Pagination', 'trx_addons' ),
						'label_block' => false,
						'type' => Controls_Manager::SWITCHER,
						'label_off' => __( 'Off', 'trx_addons' ),
						'label_on' => __( 'On', 'trx_addons' ),
						'return_value' => '1',
						'condition' => [
							'engine' => ['swiper']
						]
					]
				);

				$this->add_control(
					'pagination_type',
					[
						'label' => __( 'Pagination type', 'trx_addons' ),
						'type' => Controls_Manager::SELECT,
						'options' => ! $is_edit_mode ? array() : trx_addons_get_list_sc_slider_paginations_types(),
						'default' => 'bullets',
						'condition' => [
							'engine' => ['swiper'],
							'pagination' => '1'
						]
					]
				);

				$this->add_control(
					'pagination_pos',
					[
						'label' => __( 'Pagination position', 'trx_addons' ),
						'label_block' => false,
						'type' => Controls_Manager::SELECT,
						'options' => ! $is_edit_mode ? array() : trx_addons_get_list_sc_slider_paginations(''),
						'default' => 'bottom',
						'condition' => [
							'engine' => ['swiper'],
							'pagination' => '1'
						]
					]
				);

				$this->add_control(
					'noswipe',
					[
						'label' => __( 'Disable swipe', 'trx_addons' ),
						'label_block' => false,
						'type' => Controls_Manager::SWITCHER,
						'label_off' => __( 'Off', 'trx_addons' ),
						'label_on' => __( 'On', 'trx_addons' ),
						'return_value' => '1',
						'condition' => [
							'engine' => ['swiper']
						]
					]
				);

				$this->add_control(
					'mouse_wheel',
					[
						'label' => __( 'Enable mouse wheel', 'trx_addons' ),
						'label_block' => false,
						'type' => Controls_Manager::SWITCHER,
						'label_off' => __( 'Off', 'trx_addons' ),
						'label_on' => __( 'On', 'trx_addons' ),
						'return_value' => '1',
						'condition' => [
							'engine' => ['swiper']
						]
					]
				);

				$this->add_control(
					'free_mode',
					[
						'label' => __( 'Enable free mode', 'trx_addons' ),
						'label_block' => false,
						'type' => Controls_Manager::SWITCHER,
						'label_off' => __( 'Off', 'trx_addons' ),
						'label_on' => __( 'On', 'trx_addons' ),
						'return_value' => '1',
						'condition' => [
							'engine' => ['swiper']
						]
					]
				);

				$this->add_control(
					'loop',
					[
						'label' => __( 'Enable loop mode', 'trx_addons' ),
						'label_block' => false,
						'type' => Controls_Manager::SWITCHER,
						'label_off' => __( 'Off', 'trx_addons' ),
						'label_on' => __( 'On', 'trx_addons' ),
						'default' => '1',
						'return_value' => '1',
						'condition' => [
							'engine' => ['swiper'],
							'slides_overflow!' => '1'
						]
					]
				);

				$this->add_control(
					'autoplay',
					[
						'label' => __( 'Enable autoplay', 'trx_addons' ),
						'label_block' => false,
						'type' => Controls_Manager::SWITCHER,
						'label_off' => __( 'Off', 'trx_addons' ),
						'label_on' => __( 'On', 'trx_addons' ),
						'default' => '1',
						'return_value' => '1',
						'condition' => [
							'engine' => ['swiper']
						]
					]
				);

				$this->add_control(
					'speed',
					[
						'label' => __( 'Slides change speed', 'trx_addons' ),
						'type' => Controls_Manager::SLIDER,
						'default' => [
							'size' => 600
						],
						'range' => [
							'px' => [
								'min' => 300,
								'max' => 3000,
								'step' => 50
							],
						],
						'condition' => [
							'engine' => 'swiper'
						]
					]
				);

				$this->add_control(
					'interval',
					[
						'label' => __( 'Interval between slides change', 'trx_addons' ),
						'type' => Controls_Manager::SLIDER,
						'default' => [
							'size' => 7000
						],
						'range' => [
							'px' => [
								'min' => 0,
								'max' => 10000,
								'step' => 100
							],
						],
						'condition' => [
							'engine' => 'swiper'
						]
					]
				);

				$this->end_controls_section();
			}


			/**
			 * Register content controls with a controller (TOC) settings
			 */
			protected function register_content_controls_controller( $tab ) {
				// Detect edit mode
				$is_edit_mode = trx_addons_elm_is_edit_mode();

				$this->start_controls_section(
					'section_sc_slider_controller',
					[
						'label' => __( 'Table of contents (TOC)', 'trx_addons' ),
						'tab' => $tab
					]
				);

				$this->add_control(
					'controller',
					[
						'label' => __( 'Show TOC', 'trx_addons' ),
						'label_block' => false,
						'type' => Controls_Manager::SWITCHER,
						'label_off' => __( 'Off', 'trx_addons' ),
						'label_on' => __( 'On', 'trx_addons' ),
						'return_value' => '1',
						'condition' => [
							'engine' => ['swiper']
						]
					]
				);

				$this->add_control(
					'controller_style',
					[
						'label' => __( 'Style of the TOC', 'trx_addons' ),
						'label_block' => false,
						'type' => Controls_Manager::SELECT,
						'options' => ! $is_edit_mode ? array() : trx_addons_get_list_sc_slider_toc_styles(),
						'default' => 'default',
						'condition' => [
							'controller' => '1'
						]
					]
				);

				$this->add_control(
					'controller_pos',
					[
						'label' => __( 'Position of the TOC', 'trx_addons' ),
						'label_block' => false,
						'type' => Controls_Manager::SELECT,
						'options' => ! $is_edit_mode ? array() : trx_addons_get_list_sc_slider_toc_positions(),
						'default' => 'right',
						'condition' => [
							'controller' => '1'
						]
					]
				);

				$this->add_control(
					'controller_controls',
					[
						'label' => __( 'Show arrows', 'trx_addons' ),
						'label_block' => false,
						'type' => Controls_Manager::SWITCHER,
						'label_off' => __( 'Off', 'trx_addons' ),
						'label_on' => __( 'On', 'trx_addons' ),
						'return_value' => '1',
						'condition' => [
							'controller' => '1'
						]
					]
				);

				$this->add_control(
					'controller_effect',
					[
						'label' => __( 'Effect for change items', 'trx_addons' ),
						'label_block' => false,
						'type' => Controls_Manager::SELECT,
						'options' => ! $is_edit_mode ? array() : trx_addons_get_list_sc_slider_effects(),
						'default' => 'slide',
						'condition' => [
							'controller' => '1'
						]
					]
				);

				$this->add_responsive_control(
					'controller_per_view',
					[
						'label' => __( 'Items per view', 'trx_addons' ),
						'type' => Controls_Manager::SLIDER,
						'default' => [
							'size' => 3
						],
						'range' => [
							'px' => [
								'min' => 1,
								'max' => 10,
								'step' => 1
							],
						],
						'condition' => [
							'controller' => '1',
							'controller_effect' => ['slide','coverflow', 'swap', 'cards', 'creative']
						]
					]
				);

				$this->add_responsive_control(
					'controller_space',
					[
						'label' => __( 'Space between items', 'trx_addons' ),
						'type' => Controls_Manager::SLIDER,
						'default' => [
							'size' => 0
						],
						'range' => [
							'px' => [
								'min' => 0,
								'max' => 100,
								'step' => 1
							],
						],
						'condition' => [
							'controller' => '1'
						]
					]
				);

				$this->add_control(
					'controller_height',
					[
						'label' => __( 'Height of the TOC', 'trx_addons' ),
						'type' => Controls_Manager::SLIDER,
						'default' => [
							'size' => 0,
							'unit' => 'px'
						],
						'range' => [
							'px' => [
								'min' => 50,
								'max' => 300
							],
							'em' => [
								'min' => 2,
								'max' => 20
							],
						],
						'size_units' => [ 'px', 'em' ],
						'condition' => [
							'controller' => '1',
							'controller_pos' => [ 'bottom' ],
						]
					]
				);

				$this->end_controls_section();
			}

			/**
			 * Register style controls for the widget title.
			 */
			protected function register_style_controls_widget_title() {
				$this->start_controls_section(
					'section_sc_slider_style_widget_title',
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
							'{{WRAPPER}} .widget_slider .widget_title' => 'text-align: {{VALUE}}',
						),
					)
				);

				$this->add_group_control(
					Group_Control_Typography::get_type(),
					array(
						'name'     => 'widget_title_typography',
						'selector' => '{{WRAPPER}} .widget_slider .widget_title',
					)
				);

				$this->add_group_control(
					Group_Control_Background::get_type(),
					array(
						'name'     => 'widget_title_bg',
						'types'    => array( 'classic', 'gradient' ),
						'selector' => '{{WRAPPER}} .widget_slider .widget_title',
					)
				);

				$this->add_control(
					'widget_title_color',
					array(
						'label'     => __( 'Text Color', 'trx_addons' ),
						'type'      => Controls_Manager::COLOR,
						'selectors' => array(
							'{{WRAPPER}} .widget_slider .widget_title' => 'color: {{VALUE}};',
						),
					)
				);

				$this->add_group_control(
					Group_Control_Text_Shadow::get_type(),
					array(
						'name'     => 'widget_title_text_shadow',
						'selector' => '{{WRAPPER}} .widget_slider .widget_title',
					)
				);
		
				$this->add_group_control(
					Group_Control_Border::get_type(),
					array(
						'name'     => 'widget_title_border',
						'selector' => '{{WRAPPER}} .widget_slider .widget_title',
					)
				);
		
				$this->add_responsive_control(
					'widget_title_radius',
					array(
						'label'      => __( 'Border Radius', 'trx_addons' ),
						'type'       => Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
						'selectors'  => array(
							'{{WRAPPER}} .widget_slider .widget_title' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
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
							'{{WRAPPER}} .widget_slider .widget_title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
							'{{WRAPPER}} .widget_slider .widget_title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						),
					)
				);

				$this->add_group_control(
					Group_Control_Box_Shadow::get_type(),
					array(
						'name'     => 'widget_title_shadow',
						'selector' => '{{WRAPPER}} .widget_slider .widget_title',
					)
				);

				$this->end_controls_section();
			}

			/**
			 * Register style controls for the block with a slide info.
			 */
			protected function register_style_controls_slide() {

				$this->start_controls_section(
					'section_sc_slider_style_info',
					array(
						'label' => __( 'Slide', 'trx_addons' ),
						'tab' => Controls_Manager::TAB_STYLE,
					)
				);

				$this->add_control(
					'slide_vertical_align',
					[
						'label'                 => __( 'Vertical Alignment', 'trx_addons' ),
						'type'                  => Controls_Manager::CHOOSE,
						'label_block'           => false,
						'default'               => '',
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
							'{{WRAPPER}} .slider_container:not(.slider_controller_container) .slider-slide' => 'justify-content: {{VALUE}};',
						],
					]
				);

				$this->start_controls_tabs( 'tabs_info_style' );

				$this->start_controls_tab(
					'tab_info_normal',
					[
						'label' => __( 'Normal', 'trx_addons' ),
					]
				);

				$this->add_responsive_control(
					'slide_border_radius',
					array(
						'label'      => __( 'Border Radius', 'trx_addons' ),
						'type'       => Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
						'selectors'  => array(
							'{{WRAPPER}} .slider_container:not(.slider_controller_container)' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
							'{{WRAPPER}} .slider_container:not(.slider_controller_container) .slider-slide' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow: hidden;',
						),
					)
				);

				$this->add_control(
					'slide_overlay_heading',
					array(
						'label'      => __( 'Slide Overlay', 'trx_addons' ),
						'type'       => Controls_Manager::HEADING,
					)
				);

				$this->add_group_control(
					Group_Control_Background::get_type(),
					array(
						'name'     => 'slide_overlay_bg',
						'types'    => array( 'classic', 'gradient' ),
						'selector' => '{{WRAPPER}} .slider-slide .slide_overlay',
					)
				);

				$this->add_control(
					'info_heading',
					array(
						'label'      => __( 'Info Box', 'trx_addons' ),
						'type'       => Controls_Manager::HEADING,
					)
				);

				$this->add_responsive_control(
					'info_width',
					[
						'label' => __( 'Info Width', 'trx_addons' ),
						'type' => Controls_Manager::SLIDER,
						'range' => [
							'px' => [
								'min' => 0,
								'max' => 500,
								'step' => 1
							],
							'em' => [
								'min' => 0,
								'max' => 100,
								'step' => 0.1
							],
						],
						'default' => [
							'size' => '',
							'unit' => '%'
						],
						'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
						'selectors' => [
							'{{WRAPPER}} .slider-slide .slide_info' => 'width: {{SIZE}}{{UNIT}}; max-width: 100%; min-width: 20%;',
						]
					]
				);

				$this->add_control(
					'info_alignment',
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
							'{{WRAPPER}} .slider-slide .slide_info' => 'text-align: {{VALUE}}',
						),
					)
				);

				$this->add_group_control(
					Group_Control_Background::get_type(),
					array(
						'name'     => 'info_bg',
						'types'    => array( 'classic', 'gradient' ),
						'selector' => '{{WRAPPER}} .slider-slide .slide_info',
					)
				);

				$this->add_group_control(
					Group_Control_Border::get_type(),
					array(
						'name'     => 'info_border',
						'selector' => '{{WRAPPER}} .slider-slide .slide_info',
					)
				);
		
				$this->add_responsive_control(
					'info_radius',
					array(
						'label'      => __( 'Border Radius', 'trx_addons' ),
						'type'       => Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
						'selectors'  => array(
							'{{WRAPPER}} .slider-slide .slide_info' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
						),
					)
				);

				$this->add_responsive_control(
					'info_padding',
					array(
						'label'      => __( 'Padding', 'trx_addons' ),
						'type'       => Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
						'selectors'  => array(
							'{{WRAPPER}} .slider-slide .slide_info' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						),
					)
				);

				$this->add_responsive_control(
					'info_margin',
					array(
						'label'      => __( 'Margin', 'trx_addons' ),
						'type'       => Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
						'selectors'  => array(
							'{{WRAPPER}} .slider-slide .slide_info' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						),
					)
				);

				$this->add_group_control(
					Group_Control_Box_Shadow::get_type(),
					array(
						'name'     => 'info_shadow',
						'selector' => '{{WRAPPER}} .slider-slide .slide_info',
					)
				);

				$this->end_controls_tab();

				$this->start_controls_tab(
					'tab_info_hover',
					[
						'label' => __( 'Hover', 'trx_addons' ),
					]
				);

				$this->add_responsive_control(
					'slide_border_radius_hover',
					array(
						'label'      => __( 'Border Radius', 'trx_addons' ),
						'type'       => Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
						'selectors'  => array(
							'{{WRAPPER}} .slider_container:not(.slider_controller_container):hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
							'{{WRAPPER}} .slider_container:not(.slider_controller_container):hover .slider-slide' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow: hidden;',
						),
					)
				);

				$this->add_control(
					'slide_overlay_heading_hover',
					array(
						'label'      => __( 'Slide Overlay', 'trx_addons' ),
						'type'       => Controls_Manager::HEADING,
					)
				);

				$this->add_group_control(
					Group_Control_Background::get_type(),
					array(
						'name'     => 'slide_overlay_bg_hover',
						'types'    => array( 'classic', 'gradient' ),
						'selector' => '{{WRAPPER}} .slider-slide:hover .slide_overlay',
					)
				);

				$this->add_control(
					'slide_info_heading_hover',
					array(
						'label'      => __( 'Slide Info Box', 'trx_addons' ),
						'type'       => Controls_Manager::HEADING,
					)
				);

				$this->add_group_control(
					Group_Control_Background::get_type(),
					array(
						'name'     => 'info_bg_hover',
						'types'    => array( 'classic', 'gradient' ),
						'selector' => '{{WRAPPER}} .slider-slide:hover .slide_info',
					)
				);

				$this->add_control(
					'info_border_color_hover',
					array(
						'label'     => __( 'Border Color', 'trx_addons' ),
						'type'      => Controls_Manager::COLOR,
						'selectors' => array(
							'{{WRAPPER}} .slider-slide:hover .slide_info' => 'border-color: {{VALUE}};',
						),
					)
				);

				$this->add_group_control(
					Group_Control_Box_Shadow::get_type(),
					array(
						'name'     => 'info_shadow_hover',
						'selector' => '{{WRAPPER}} .slider-slide:hover .slide_info',
					)
				);

				$this->end_controls_tab();

				$this->end_controls_tabs();

				$this->end_controls_section();
			}

			/**
			 * Register style controls for the block with a slide subtitle.
			 */
			protected function register_style_controls_slide_subtitle() {
				$this->start_controls_section(
					'section_sc_slider_style_info_subtitle',
					array(
						'label' => __( 'Slide Subtitle', 'trx_addons' ),
						'tab' => Controls_Manager::TAB_STYLE,
					)
				);

				$this->start_controls_tabs( 'tabs_info_subtitle_style' );

				$this->start_controls_tab(
					'tab_info_subtitle_normal',
					[
						'label' => __( 'Normal', 'trx_addons' ),
					]
				);

				$this->add_group_control(
					Group_Control_Typography::get_type(),
					array(
						'name'     => 'info_subtitle_typography',
						'selector' => '{{WRAPPER}} .slider-slide .slide_info .slide_cats',
					)
				);

				$this->add_control(
					'info_subtitle_color',
					array(
						'label'     => __( 'Color', 'trx_addons' ),
						'type'      => Controls_Manager::COLOR,
						'selectors' => array(
							'{{WRAPPER}} .slider-slide .slide_info .slide_cats,
							 {{WRAPPER}} .slider-slide .slide_info .slide_cats > a' => 'color: {{VALUE}};',
						),
					)
				);

				$this->add_group_control(
					Group_Control_Text_Shadow::get_type(),
					array(
						'name'     => 'info_subtitle_text_shadow',
						'selector' => '{{WRAPPER}} .slider-slide .slide_info .slide_cats',
					)
				);

				$this->add_responsive_control(
					'info_subtitle_margin',
					array(
						'label'      => __( 'Margin', 'trx_addons' ),
						'type'       => Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
						'selectors'  => array(
							'{{WRAPPER}} .slider-slide .slide_info .slide_cats' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						),
					)
				);

				$this->end_controls_tab();

				$this->start_controls_tab(
					'tab_info_subtitle_hover',
					[
						'label' => __( 'Hover', 'trx_addons' ),
					]
				);

				$this->add_control(
					'info_subtitle_color_hover',
					array(
						'label'     => __( 'Color', 'trx_addons' ),
						'type'      => Controls_Manager::COLOR,
						'selectors' => array(
							'{{WRAPPER}} .slider-slide:hover .slide_info .slide_cats,
							 {{WRAPPER}} .slider-slide:hover .slide_info .slide_cats > a' => 'color: {{VALUE}};',
						),
					)
				);

				$this->add_group_control(
					Group_Control_Text_Shadow::get_type(),
					array(
						'name'     => 'info_subtitle_text_shadow_hover',
						'selector' => '{{WRAPPER}} .slider-slide:hover .slide_info .slide_cats',
					)
				);

				$this->end_controls_tab();

				$this->end_controls_tabs();

				$this->end_controls_section();
			}

			/**
			 * Register style controls for the block with a slide title.
			 */
			protected function register_style_controls_slide_title() {
				$this->start_controls_section(
					'section_sc_slider_style_info_title',
					array(
						'label' => __( 'Slide Title', 'trx_addons' ),
						'tab' => Controls_Manager::TAB_STYLE,
					)
				);

				$this->start_controls_tabs( 'tabs_info_title_style' );

				$this->start_controls_tab(
					'tab_info_title_normal',
					[
						'label' => __( 'Normal', 'trx_addons' ),
					]
				);

				$this->add_group_control(
					Group_Control_Typography::get_type(),
					array(
						'name'     => 'info_title_typography',
						'selector' => '{{WRAPPER}} .slider-slide .slide_info .slide_title',
					)
				);

				$this->add_control(
					'info_title_color',
					array(
						'label'     => __( 'Color', 'trx_addons' ),
						'type'      => Controls_Manager::COLOR,
						'selectors' => array(
							'{{WRAPPER}} .slider-slide .slide_info .slide_title,
							 {{WRAPPER}} .slider-slide .slide_info .slide_title > a' => 'color: {{VALUE}};',
						),
					)
				);

				$this->add_group_control(
					Group_Control_Text_Shadow::get_type(),
					array(
						'name'     => 'info_title_text_shadow',
						'selector' => '{{WRAPPER}} .slider-slide .slide_info .slide_title',
					)
				);

				$this->add_responsive_control(
					'info_title_margin',
					array(
						'label'      => __( 'Margin', 'trx_addons' ),
						'type'       => Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
						'selectors'  => array(
							'{{WRAPPER}} .slider-slide .slide_info .slide_title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						),
					)
				);

				$this->end_controls_tab();

				$this->start_controls_tab(
					'tab_info_title_hover',
					[
						'label' => __( 'Hover', 'trx_addons' ),
					]
				);

				$this->add_control(
					'info_title_color_hover',
					array(
						'label'     => __( 'Color', 'trx_addons' ),
						'type'      => Controls_Manager::COLOR,
						'selectors' => array(
							'{{WRAPPER}} .slider-slide:hover .slide_info .slide_title,
							 {{WRAPPER}} .slider-slide:hover .slide_info .slide_title > a' => 'color: {{VALUE}};',
						),
					)
				);

				$this->add_group_control(
					Group_Control_Text_Shadow::get_type(),
					array(
						'name'     => 'info_title_text_shadow_hover',
						'selector' => '{{WRAPPER}} .slider-slide:hover .slide_info .slide_title',
					)
				);

				$this->end_controls_tab();

				$this->end_controls_tabs();

				$this->end_controls_section();
			}

			/**
			 * Register style controls for the block with a slide meta.
			 */
			protected function register_style_controls_slide_meta() {
				$this->start_controls_section(
					'section_sc_slider_style_info_meta',
					array(
						'label' => __( 'Slide Meta', 'trx_addons' ),
						'tab' => Controls_Manager::TAB_STYLE,
					)
				);

				$this->start_controls_tabs( 'tabs_info_meta_style' );

				$this->start_controls_tab(
					'tab_info_meta_normal',
					[
						'label' => __( 'Normal', 'trx_addons' ),
					]
				);

				$this->add_group_control(
					Group_Control_Typography::get_type(),
					array(
						'name'     => 'info_meta_typography',
						'selector' => '{{WRAPPER}} .slider-slide .slide_info .slide_date',
					)
				);

				$this->add_control(
					'info_meta_color',
					array(
						'label'     => __( 'Color', 'trx_addons' ),
						'type'      => Controls_Manager::COLOR,
						'selectors' => array(
							'{{WRAPPER}} .slider-slide .slide_info .slide_date' => 'color: {{VALUE}};',
						),
					)
				);

				$this->add_group_control(
					Group_Control_Text_Shadow::get_type(),
					array(
						'name'     => 'info_meta_text_shadow',
						'selector' => '{{WRAPPER}} .slider-slide .slide_info .slide_date',
					)
				);

				$this->add_responsive_control(
					'info_meta_margin',
					array(
						'label'      => __( 'Margin', 'trx_addons' ),
						'type'       => Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
						'selectors'  => array(
							'{{WRAPPER}} .slider-slide .slide_info .slide_date' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						),
					)
				);

				$this->end_controls_tab();

				$this->start_controls_tab(
					'tab_info_meta_hover',
					[
						'label' => __( 'Hover', 'trx_addons' ),
					]
				);

				$this->add_control(
					'info_meta_color_hover',
					array(
						'label'     => __( 'Color', 'trx_addons' ),
						'type'      => Controls_Manager::COLOR,
						'selectors' => array(
							'{{WRAPPER}} .slider-slide:hover .slide_info .slide_date' => 'color: {{VALUE}};',
						),
					)
				);

				$this->add_group_control(
					Group_Control_Text_Shadow::get_type(),
					array(
						'name'     => 'info_meta_text_shadow_hover',
						'selector' => '{{WRAPPER}} .slider-slide:hover .slide_info .slide_date',
					)
				);

				$this->end_controls_tab();

				$this->end_controls_tabs();

				$this->end_controls_section();
			}

			/**
			 * Register style controls for the block with a slide content.
			 */
			protected function register_style_controls_slide_content() {
				$this->start_controls_section(
					'section_sc_slider_style_info_content',
					array(
						'label' => __( 'Slide Content', 'trx_addons' ),
						'tab' => Controls_Manager::TAB_STYLE,
					)
				);

				$this->start_controls_tabs( 'tabs_info_content_style' );

				$this->start_controls_tab(
					'tab_info_content_normal',
					[
						'label' => __( 'Normal', 'trx_addons' ),
					]
				);

				$this->add_responsive_control(
					'info_content_width',
					[
						'label' => __( 'Content Width', 'trx_addons' ),
						'type' => Controls_Manager::SLIDER,
						'range' => [
							'px' => [
								'min' => 0,
								'max' => 500,
								'step' => 1
							],
							'em' => [
								'min' => 0,
								'max' => 100,
								'step' => 0.1
							],
						],
						'default' => [
							'size' => '',
							'unit' => '%'
						],
						'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
						'selectors' => [
							'{{WRAPPER}} .slider-slide .slide_content' => 'width: {{SIZE}}{{UNIT}}; max-width: 100%; min-width: 20%;',
						]
					]
				);

				$this->add_control(
					'info_content_alignment',
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
							'{{WRAPPER}} .slider-slide .slide_content' => 'text-align: {{VALUE}}',
						),
					)
				);

				$this->add_group_control(
					Group_Control_Typography::get_type(),
					array(
						'name'     => 'info_content_typography',
						'selector' => '{{WRAPPER}} .slider-slide .slide_content',
					)
				);

				$this->add_control(
					'info_content_color',
					array(
						'label'     => __( 'Color', 'trx_addons' ),
						'type'      => Controls_Manager::COLOR,
						'selectors' => array(
							'{{WRAPPER}} .slider-slide .slide_content' => 'color: {{VALUE}};',
						),
					)
				);

				$this->add_group_control(
					Group_Control_Text_Shadow::get_type(),
					array(
						'name'     => 'info_content_text_shadow',
						'selector' => '{{WRAPPER}} .slider-slide .slide_content',
					)
				);

				$this->add_responsive_control(
					'info_content_margin',
					array(
						'label'      => __( 'Margin', 'trx_addons' ),
						'type'       => Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
						'selectors'  => array(
							'{{WRAPPER}} .slider-slide .slide_content' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						),
					)
				);

				$this->end_controls_tab();

				$this->start_controls_tab(
					'tab_info_content_hover',
					[
						'label' => __( 'Hover', 'trx_addons' ),
					]
				);

				$this->add_control(
					'info_content_color_hover',
					array(
						'label'     => __( 'Color', 'trx_addons' ),
						'type'      => Controls_Manager::COLOR,
						'selectors' => array(
							'{{WRAPPER}} .slider-slide:hover .slide_content' => 'color: {{VALUE}};',
						),
					)
				);

				$this->add_group_control(
					Group_Control_Text_Shadow::get_type(),
					array(
						'name'     => 'info_content_text_shadow_hover',
						'selector' => '{{WRAPPER}} .slider-slide:hover .slide_content',
					)
				);

				$this->end_controls_tab();

				$this->end_controls_tabs();

				$this->end_controls_section();
			}

			/**
			 * Register style controls for arrows
			 *
			 * @access protected
			 */
			protected function register_style_controls_arrows() {

				$this->start_controls_section(
					'section_sc_slider_style_arrows',
					[
						'label' => __( 'Arrows', 'trx_addons' ),
						'tab'   => Controls_Manager::TAB_STYLE,
						'condition' => [
							'engine' => ['swiper', 'elastistack'],
							'controls' => '1'
						]
					]
				);

				$this->add_responsive_control(
					'arrows_width',
					[
						'label' => __( 'Width', 'trx_addons' ),
						'type' => Controls_Manager::SLIDER,
						'range' => [
							'px' => [
								'min' => 0,
								'max' => 100,
								'step' => 1
							],
							'em' => [
								'min' => 0,
								'max' => 10,
								'step' => 0.1
							],
						],
						'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
						'selectors' => [
							'{{WRAPPER}} .slider_arrows_wrap > a' => 'width: {{SIZE}}{{UNIT}};',
							'{{WRAPPER}} .slider_style_modern' => 'padding-left: {{SIZE}}{{UNIT}}; padding-right: {{SIZE}}{{UNIT}};',
						]
					]
				);

				$this->add_responsive_control(
					'arrows_height',
					[
						'label' => __( 'Height', 'trx_addons' ),
						'type' => Controls_Manager::SLIDER,
						'range' => [
							'px' => [
								'min' => 0,
								'max' => 100,
								'step' => 1
							],
							'em' => [
								'min' => 0,
								'max' => 10,
								'step' => 0.1
							],
						],
						'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
						'selectors' => [
							'{{WRAPPER}} .slider_arrows_wrap > a' => 'height: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}};',
						],
						'condition' => [
							'slider_style' => ['default']
						]
					]
				);

				$this->add_responsive_control(
					'arrows_offset',
					[
						'label' => __( 'Offset', 'trx_addons' ),
						'type' => Controls_Manager::SLIDER,
						'range' => [
							'px' => [
								'min' => 0,
								'max' => 100,
								'step' => 1
							],
							'em' => [
								'min' => 0,
								'max' => 10,
								'step' => 0.1
							],
						],
						'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
						'selectors' => [
							'{{WRAPPER}} .slider_container.slider_controls_side .slider_arrows_wrap > .slider_prev,
							 {{WRAPPER}} .slider_outer_controls_side .slider_arrows_wrap > .slider_prev' => 'left: {{SIZE}}{{UNIT}};',
							'{{WRAPPER}} .slider_container.slider_controls_side .slider_arrows_wrap > .slider_next,
							 {{WRAPPER}} .slider_outer_controls_side .slider_arrows_wrap > .slider_next' => 'right: {{SIZE}}{{UNIT}};',
						],
						'condition' => [
							'controls_pos' => ['side']
						]
					]
				);

				$params = trx_addons_get_icon_param('icon');
				$params = trx_addons_array_get_first_value( $params );
				unset( $params['name'] );
				$this->add_control( 'controls_icon', $params );

				$this->add_control(
					'arrows_icon_size',
					[
						'label' => __( 'Icon size', 'trx_addons' ),
						'type' => Controls_Manager::SLIDER,
						'range' => [
							'px' => [
								'min' => 1,
								'max' => 100,
								'step' => 1
							],
							'em' => [
								'min' => 0.1,
								'max' => 10,
								'step' => 0.1
							],
						],
						'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
						'selectors' => [
							'{{WRAPPER}} .slider_arrows_wrap > a:before' => 'font-size: {{SIZE}}{{UNIT}};',
						],
					]
				);

				$this->start_controls_tabs( 'tabs_arrows_style' );

				$this->start_controls_tab(
					'tab_arrows_normal',
					[
						'label' => __( 'Normal', 'trx_addons' ),
					]
				);

				$this->add_group_control(
					Group_Control_Background::get_type(),
					[
						'name'     => 'arrows_background',
						'label'    => __( 'Background', 'trx_addons' ),
						'types'    => [ 'classic', 'gradient' ],
						'selector' => '{{WRAPPER}} .slider_arrows_wrap > a',
					]
				);

				$this->add_control(
					'arrows_icon_color',
					[
						'label' => __( 'Icon color', 'trx_addons' ),
						'label_block' => false,
						'type' => Controls_Manager::COLOR,
						'default' => '',
						// 'global' => array(
						// 	'active' => false,
						// ),
						'selectors' => [
							'{{WRAPPER}} .slider_arrows_wrap > a:before' => 'color: {{VALUE}};',
						],
					]
				);

				$this->add_control(
					'arrows_label_color',
					[
						'label' => __( 'Label color', 'trx_addons' ),
						'label_block' => false,
						'type' => Controls_Manager::COLOR,
						'default' => '',
						// 'global' => array(
						// 	'active' => false,
						// ),
						'selectors' => [
							'{{WRAPPER}} .slider_arrows_wrap > a .slider_controls_label' => 'color: {{VALUE}};',
						],
						'condition' => [
							'slider_style' => ['modern']
						]
					]
				);

				$this->add_group_control(
					Group_Control_Border::get_type(),
					[
						'name'        => 'arrows_border',
						'label'       => __( 'Border', 'trx_addons' ),
						'placeholder' => '1px',
						'default'     => '1px',
						'selector'    => '{{WRAPPER}} .slider_arrows_wrap > a',
					]
				);

				$this->add_responsive_control(
					'arrows_border_radius_prev',
					[
						'label'      => __( 'Border Radius (button "Prev")', 'trx_addons' ),
						'type'       => Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
						'selectors'  => [
							'{{WRAPPER}} .slider_arrows_wrap > .slider_prev' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
						'condition' => [
							'engine!' => 'elastistack'
						]
					]
				);

				$this->add_responsive_control(
					'arrows_border_radius_next',
					[
						'label'      => __( 'Border Radius (button "Next")', 'trx_addons' ),
						'type'       => Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
						'selectors'  => [
							'{{WRAPPER}} .slider_arrows_wrap > .slider_next' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
					]
				);

				$this->add_group_control(
					Group_Control_Box_Shadow::get_type(),
					[
						'name'      => 'arrows_box_shadow',
						'selector'  => '{{WRAPPER}} .slider_arrows_wrap > a',
					]
				);

				$this->end_controls_tab();

				$this->start_controls_tab(
					'tab_arrows_hover',
					[
						'label' => __( 'Hover', 'trx_addons' ),
					]
				);

				$this->add_group_control(
					Group_Control_Background::get_type(),
					[
						'name'     => 'arrows_background_hover',
						'label'    => __( 'Background', 'trx_addons' ),
						'types'    => [ 'classic', 'gradient' ],
						'selector' => '{{WRAPPER}} .slider_arrows_wrap > a:hover',
					]
				);

				$this->add_control(
					'arrows_icon_color_hover',
					[
						'label' => __( 'Icon color', 'trx_addons' ),
						'label_block' => false,
						'type' => Controls_Manager::COLOR,
						'default' => '',
						// 'global' => array(
						// 	'active' => false,
						// ),
						'selectors' => [
							'{{WRAPPER}} .slider_arrows_wrap > a:hover:before' => 'color: {{VALUE}};',
						],
					]
				);

				$this->add_control(
					'arrows_label_color:hover',
					[
						'label' => __( 'Label color', 'trx_addons' ),
						'label_block' => false,
						'type' => Controls_Manager::COLOR,
						'default' => '',
						// 'global' => array(
						// 	'active' => false,
						// ),
						'selectors' => [
							'{{WRAPPER}} .slider_arrows_wrap > a:hover .slider_controls_label' => 'color: {{VALUE}};',
						],
						'condition' => [
							'slider_style' => ['modern']
						]
					]
				);

				$this->add_control(
					'arrows_border_color_hover',
					[
						'label' => __( 'Border color', 'trx_addons' ),
						'label_block' => false,
						'type' => Controls_Manager::COLOR,
						'default' => '',
						// 'global' => array(
						// 	'active' => false,
						// ),
						'selectors' => [
							'{{WRAPPER}} .slider_arrows_wrap > a:hover' => 'border-color: {{VALUE}};',
						],
					]
				);

				$this->add_group_control(
					Group_Control_Box_Shadow::get_type(),
					[
						'name'      => 'arrows_box_shadow_hover',
						'selector'  => '{{WRAPPER}} .slider_arrows_wrap > a:hover',
					]
				);

				$this->end_controls_tab();

				$this->end_controls_tabs();

				$this->end_controls_section();
			}

			/**
			 * Register style controls for pagination
			 *
			 * @access protected
			 */
			protected function register_style_controls_pagination() {

				$this->start_controls_section(
					'section_sc_slider_style_pagination',
					[
						'label' => __( 'Pagination', 'trx_addons' ),
						'tab'   => Controls_Manager::TAB_STYLE,
						'condition' => [
							'engine' => ['swiper', 'elastistack'],
							'pagination' => '1'
						]
					]
				);

				$this->add_responsive_control(
					'pagination_size',
					[
						'label' => __( 'Size', 'trx_addons' ),
						'type' => Controls_Manager::SLIDER,
						'range' => [
							'px' => [
								'min' => 0,
								'max' => 100,
								'step' => 1
							],
							'em' => [
								'min' => 0,
								'max' => 10,
								'step' => 0.1
							],
						],
						'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
						'selectors' => [
							'{{WRAPPER}} .slider_pagination_wrap .swiper-pagination-bullet' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}};',
							'{{WRAPPER}} .slider_pagination_wrap.swiper-pagination-bullets' => 'height: calc( {{SIZE}}{{UNIT}} + 2px ); line-height: calc( {{SIZE}}{{UNIT}} + 2px );',
							'{{WRAPPER}} .slider_pagination_wrap.swiper-pagination-progressbar' => 'height: {{SIZE}}{{UNIT}};',
						],
						'condition' => [
							'pagination_type' => ['bullets', 'progressbar']
						]
					]
				);

				$this->add_responsive_control(
					'pagination_offset',
					[
						'label' => __( 'Offset', 'trx_addons' ),
						'type' => Controls_Manager::SLIDER,
						'range' => [
							'px' => [
								'min' => 0,
								'max' => 100,
								'step' => 1
							],
							'em' => [
								'min' => 0,
								'max' => 10,
								'step' => 0.1
							],
						],
						'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
						'selectors' => [
							'{{WRAPPER}} .slider_container .slider_pagination_wrap' => 'margin-bottom: {{SIZE}}{{UNIT}};',
							'{{WRAPPER}} .slider_container ~ .slider_pagination_wrap' => 'margin-top: {{SIZE}}{{UNIT}};',
						],
					]
				);

				$this->start_controls_tabs( 'tabs_pagination_style' );

				$this->start_controls_tab(
					'tab_pagination_normal',
					[
						'label' => __( 'Normal', 'trx_addons' ),
					]
				);

				$this->add_group_control(
					Group_Control_Typography::get_type(),
					array(
						'name'     => 'pagination_typography',
						'selector' => '{{WRAPPER}} .slider_pagination_wrap.swiper-pagination-fraction',
						'condition' => [
							'pagination_type' => ['fraction']
						]
					)
				);

				$this->add_group_control(
					Group_Control_Background::get_type(),
					[
						'name'     => 'pagination_background',
						'label'    => __( 'Background', 'trx_addons' ),
						'types'    => [ 'classic', 'gradient' ],
						'selector' => '{{WRAPPER}} .slider_pagination_wrap > .swiper-pagination-bullet,
									   {{WRAPPER}} .slider_pagination_wrap.swiper-pagination-progressbar',
						'condition' => [
							'pagination_type' => ['bullets', 'progressbar']
						]
					]
				);

				$this->add_control(
					'pagination_color',
					[
						'label' => __( 'Color', 'trx_addons' ),
						'label_block' => false,
						'type' => Controls_Manager::COLOR,
						'default' => '',
						// 'global' => array(
						// 	'active' => false,
						// ),
						'selectors' => [
							'{{WRAPPER}} .slider_pagination_wrap.swiper-pagination-fraction,
							 {{WRAPPER}} .slider_pagination_wrap.swiper-pagination-fraction .swiper-pagination-total' => 'color: {{VALUE}};',
						],
						'condition' => [
							'pagination_type' => ['fraction']
						]
					]
				);

				$this->add_group_control(
					Group_Control_Border::get_type(),
					[
						'name'        => 'pagination_border',
						'label'       => __( 'Border', 'trx_addons' ),
						'selector'    => '{{WRAPPER}} .slider_pagination_wrap > .swiper-pagination-bullet,
										  {{WRAPPER}} .slider_pagination_wrap.swiper-pagination-progressbar',
						'condition' => [
							'pagination_type' => ['bullets', 'progressbar']
						]
					]
				);

				$this->add_responsive_control(
					'pagination_border_radius',
					[
						'label'      => __( 'Border Radius', 'trx_addons' ),
						'type'       => Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
						'selectors'  => [
							'{{WRAPPER}} .slider_pagination_wrap > .swiper-pagination-bullet,
							 {{WRAPPER}} .slider_pagination_wrap.swiper-pagination-progressbar,
							 {{WRAPPER}} .slider_pagination_wrap.swiper-pagination-progressbar .swiper-pagination-progressbar-fill' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
						'condition' => [
							'pagination_type' => ['bullets', 'progressbar']
						]
					]
				);

				$this->add_group_control(
					Group_Control_Box_Shadow::get_type(),
					[
						'name'      => 'pagination_box_shadow',
						'selector'  => '{{WRAPPER}} .slider_pagination_wrap > .swiper-pagination-bullet,
										{{WRAPPER}} .slider_pagination_wrap.swiper-pagination-progressbar',
						'condition' => [
							'pagination_type' => ['bullets', 'progressbar']
						]
					]
				);

				$this->add_group_control(
					Group_Control_Text_Shadow::get_type(),
					[
						'name'      => 'pagination_text_shadow',
						'selector'  => '{{WRAPPER}} .slider_pagination_wrap.swiper-pagination-fraction',
						'condition' => [
							'pagination_type' => ['fraction']
						]
					]
				);

				$this->end_controls_tab();

				$this->start_controls_tab(
					'tab_pagination_hover',
					[
						'label' => __( 'Hover', 'trx_addons' ),
					]
				);

				$this->add_group_control(
					Group_Control_Background::get_type(),
					[
						'name'     => 'pagination_background_hover',
						'label'    => __( 'Background', 'trx_addons' ),
						'types'    => [ 'classic', 'gradient' ],
						'selector' => '{{WRAPPER}} .slider_pagination_wrap > .swiper-pagination-bullet:hover,
									   {{WRAPPER}} .slider_pagination_wrap.swiper-pagination-progressbar:hover',
						'condition' => [
							'pagination_type' => ['bullets', 'progressbar']
						]
					]
				);

				$this->add_group_control(
					Group_Control_Border::get_type(),
					[
						'name'        => 'pagination_border_hover',
						'label'       => __( 'Border', 'trx_addons' ),
						'selector'    => '{{WRAPPER}} .slider_pagination_wrap > .swiper-pagination-bullet:hover,
										  {{WRAPPER}} .slider_pagination_wrap.swiper-pagination-progressbar:hover',
						'condition' => [
							'pagination_type' => ['bullets', 'progressbar']
						]
					]
				);

				$this->add_group_control(
					Group_Control_Box_Shadow::get_type(),
					[
						'name'      => 'pagination_box_shadow_hover',
						'selector'  => '{{WRAPPER}} .slider_pagination_wrap > .swiper-pagination-bullet:hover,
										{{WRAPPER}} .slider_pagination_wrap.swiper-pagination-progressbar:hover',
						'condition' => [
							'pagination_type' => ['bullets', 'progressbar']
						]
					]
				);

				$this->end_controls_tab();

				$this->start_controls_tab(
					'tab_pagination_active',
					[
						'label' => __( 'Active', 'trx_addons' ),
					]
				);

				$this->add_group_control(
					Group_Control_Typography::get_type(),
					array(
						'name'     => 'pagination_typography_active',
						'selector' => '{{WRAPPER}} .slider_pagination_wrap.swiper-pagination-fraction .swiper-pagination-current',
						'condition' => [
							'pagination_type' => ['fraction']
						]
					)
				);

				$this->add_group_control(
					Group_Control_Background::get_type(),
					[
						'name'     => 'pagination_background_active',
						'label'    => __( 'Background', 'trx_addons' ),
						'types'    => [ 'classic', 'gradient' ],
						'selector' => '{{WRAPPER}} .slider_pagination_wrap > .swiper-pagination-bullet-active,
									   {{WRAPPER}} .slider_pagination_wrap.swiper-pagination-progressbar .swiper-pagination-progressbar-fill',
						'condition' => [
							'pagination_type' => ['bullets', 'progressbar']
						]
					]
				);

				$this->add_control(
					'pagination_color_active',
					[
						'label' => __( 'Color', 'trx_addons' ),
						'label_block' => false,
						'type' => Controls_Manager::COLOR,
						'default' => '',
						// 'global' => array(
						// 	'active' => false,
						// ),
						'selectors' => [
							'{{WRAPPER}} .slider_pagination_wrap.swiper-pagination-fraction .swiper-pagination-current' => 'color: {{VALUE}};',
						],
						'condition' => [
							'pagination_type' => ['fraction']
						]
					]
				);

				$this->add_group_control(
					Group_Control_Border::get_type(),
					[
						'name'        => 'pagination_border_active',
						'label'       => __( 'Border', 'trx_addons' ),
						'selector'    => '{{WRAPPER}} .slider_pagination_wrap > .swiper-pagination-bullet-active,
										  {{WRAPPER}} .slider_pagination_wrap.swiper-pagination-progressbar .swiper-pagination-progressbar-fill',
						'condition' => [
							'pagination_type' => ['bullets', 'progressbar']
						]
					]
				);

				$this->add_responsive_control(
					'pagination_border_radius_active',
					[
						'label'      => __( 'Border Radius', 'trx_addons' ),
						'type'       => Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
						'selectors'  => [
							'{{WRAPPER}} .slider_pagination_wrap > .swiper-pagination-bullet-active,
							 {{WRAPPER}} .slider_pagination_wrap.swiper-pagination-progressbar .swiper-pagination-progressbar-fill' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
						'condition' => [
							'pagination_type' => ['bullets', 'progressbar']
						]
					]
				);

				$this->end_controls_tab();

				$this->end_controls_tabs();

				$this->end_controls_section();
			}

			/**
			 * Register style controls for the controller slides
			 */
			protected function register_style_controls_controller_slide() {
				$this->start_controls_section(
					'section_sc_slider_style_controller_slide',
					array(
						'label' => __( 'Controller Slide', 'trx_addons' ),
						'tab' => Controls_Manager::TAB_STYLE,
						'condition' => [
							'engine' => ['swiper', 'elastistack'],
							'controller' => '1'
						]
					)
				);

				$this->start_controls_tabs( 'tabs_controller_slide_style' );

				$this->start_controls_tab(
					'tab_controller_slide_normal',
					[
						'label' => __( 'Normal', 'trx_addons' ),
					]
				);

				$this->add_control(
					'controller_slide_alignment',
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
							'{{WRAPPER}} .sc_slider_controller .slider-slide' => 'text-align: {{VALUE}}',
						),
					)
				);

				$this->add_group_control(
					Group_Control_Background::get_type(),
					array(
						'name'     => 'controller_slide_bg',
						'types'    => array( 'classic', 'gradient' ),
						'selector' => '{{WRAPPER}} .sc_slider_controller .slider-slide',
					)
				);

				$this->add_group_control(
					Group_Control_Border::get_type(),
					array(
						'name'     => 'controller_slide_border',
						'selector' => '{{WRAPPER}} .sc_slider_controller .slider-slide',
					)
				);
		
				$this->add_responsive_control(
					'controller_slide_radius',
					array(
						'label'      => __( 'Border Radius', 'trx_addons' ),
						'type'       => Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
						'selectors'  => array(
							'{{WRAPPER}} .sc_slider_controller .slider-slide' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
						),
					)
				);

				$this->add_responsive_control(
					'controller_slide_padding',
					array(
						'label'      => __( 'Padding', 'trx_addons' ),
						'type'       => Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
						'selectors'  => array(
							'{{WRAPPER}} .sc_slider_controller .slider-slide' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						),
					)
				);

				$this->add_group_control(
					Group_Control_Box_Shadow::get_type(),
					array(
						'name'     => 'controller_slide_shadow',
						'selector' => '{{WRAPPER}} .sc_slider_controller .slider-slide',
					)
				);

				$this->end_controls_tab();

				$this->start_controls_tab(
					'tab_controller_slide_hover',
					[
						'label' => __( 'Hover', 'trx_addons' ),
					]
				);

				$this->add_group_control(
					Group_Control_Background::get_type(),
					array(
						'name'     => 'controller_slide_bg_hover',
						'types'    => array( 'classic', 'gradient' ),
						'selector' => '{{WRAPPER}} .sc_slider_controller .slider-slide:hover',
					)
				);

				$this->add_control(
					'controller_slide_bd_color_hover',
					array(
						'label'     => __( 'Border Color', 'trx_addons' ),
						'type'      => Controls_Manager::COLOR,
						'selectors' => array(
							'{{WRAPPER}} .sc_slider_controller .slider-slide:hover' => 'border-color: {{VALUE}};',
						),
					)
				);

				$this->add_group_control(
					Group_Control_Box_Shadow::get_type(),
					array(
						'name'     => 'controller_slide_shadow_hover',
						'selector' => '{{WRAPPER}} .sc_slider_controller .slider-slide:hover',
					)
				);

				$this->end_controls_tab();

				$this->start_controls_tab(
					'tab_controller_slide_active',
					[
						'label' => __( 'Active', 'trx_addons' ),
					]
				);

				$this->add_group_control(
					Group_Control_Background::get_type(),
					array(
						'name'     => 'controller_slide_bg_active',
						'types'    => array( 'classic', 'gradient' ),
						'selector' => '{{WRAPPER}} .sc_slider_controller .swiper-slide-active',
					)
				);

				$this->add_control(
					'controller_slide_bd_color_active',
					array(
						'label'     => __( 'Border Color', 'trx_addons' ),
						'type'      => Controls_Manager::COLOR,
						'selectors' => array(
							'{{WRAPPER}} .sc_slider_controller .swiper-slide-active' => 'border-color: {{VALUE}};',
						),
					)
				);

				$this->add_control(
					'controller_slide_line_color_active',
					array(
						'label'     => __( 'Line Color', 'trx_addons' ),
						'type'      => Controls_Manager::COLOR,
						'selectors' => array(
							'{{WRAPPER}} .sc_slider_controller .swiper-slide-active:after' => 'background-color: {{VALUE}};',
						),
					)
				);

				$this->add_group_control(
					Group_Control_Box_Shadow::get_type(),
					array(
						'name'     => 'controller_slide_shadow_active',
						'selector' => '{{WRAPPER}} .sc_slider_controller .swiper-slide-active',
					)
				);

				$this->end_controls_tab();

				$this->end_controls_tabs();

				$this->end_controls_section();
			}

			/**
			 * Register style controls for the controller slide image
			 */
			protected function register_style_controls_controller_slide_image() {
				$this->start_controls_section(
					'section_sc_slider_style_controller_slide_image',
					array(
						'label' => __( 'Controller Slide Image', 'trx_addons' ),
						'tab' => Controls_Manager::TAB_STYLE,
						'condition' => [
							'engine' => ['swiper', 'elastistack'],
							'controller' => '1'
						]
					)
				);

				$this->start_controls_tabs( 'tabs_controller_slide_image_style' );

				$this->start_controls_tab(
					'tab_controller_slide_image_normal',
					[
						'label' => __( 'Normal', 'trx_addons' ),
					]
				);

				$this->add_group_control(
					Group_Control_Background::get_type(),
					array(
						'name'     => 'controller_slide_image_bg',
						'types'    => array( 'classic', 'gradient' ),
						'selector' => '{{WRAPPER}} .sc_slider_controller .sc_slider_controller_item_image',
					)
				);

				$this->add_group_control(
					Group_Control_Border::get_type(),
					array(
						'name'     => 'controller_slide_image_border',
						'selector' => '{{WRAPPER}} .sc_slider_controller .sc_slider_controller_item_image',
					)
				);
		
				$this->add_responsive_control(
					'controller_slide_image_radius',
					array(
						'label'      => __( 'Border Radius', 'trx_addons' ),
						'type'       => Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
						'selectors'  => array(
							'{{WRAPPER}} .sc_slider_controller .sc_slider_controller_item_image' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
						),
					)
				);

				$this->add_responsive_control(
					'controller_slide_image_padding',
					array(
						'label'      => __( 'Padding', 'trx_addons' ),
						'type'       => Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
						'selectors'  => array(
							'{{WRAPPER}} .sc_slider_controller .sc_slider_controller_item_image' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						),
					)
				);

				$this->add_group_control(
					Group_Control_Box_Shadow::get_type(),
					array(
						'name'     => 'controller_slide_image_shadow',
						'selector' => '{{WRAPPER}} .sc_slider_controller .sc_slider_controller_item_image',
					)
				);

				$this->add_responsive_control(
					'controller_slide_image_gap',
					[
						'label' => __( 'Gap to Content', 'trx_addons' ),
						'type' => Controls_Manager::SLIDER,
						'range' => [
							'px' => [
								'min' => 0,
								'max' => 100,
								'step' => 1
							],
							'em' => [
								'min' => 0,
								'max' => 10,
								'step' => 0.1
							],
						],
						'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
						'selectors' => [
							'{{WRAPPER}} .sc_slider_controller .sc_slider_controller_item_info' => 'padding-left: {{SIZE}}{{UNIT}};',
						],
					]
				);

				$this->end_controls_tab();

				$this->start_controls_tab(
					'tab_controller_slide_image_hover',
					[
						'label' => __( 'Hover', 'trx_addons' ),
					]
				);

				$this->add_group_control(
					Group_Control_Background::get_type(),
					array(
						'name'     => 'controller_slide_image_bg_hover',
						'types'    => array( 'classic', 'gradient' ),
						'selector' => '{{WRAPPER}} .sc_slider_controller .slider-slide:hover .sc_slider_controller_item_image',
					)
				);

				$this->add_control(
					'controller_slide_image_bd_hover',
					array(
						'label'     => __( 'Border Color', 'trx_addons' ),
						'type'      => Controls_Manager::COLOR,
						'selectors' => array(
							'{{WRAPPER}} .sc_slider_controller .slider-slide:hover .sc_slider_controller_item_image' => 'border-color: {{VALUE}};',
						),
					)
				);

				$this->add_group_control(
					Group_Control_Box_Shadow::get_type(),
					array(
						'name'     => 'controller_slide_image_shadow_hover',
						'selector' => '{{WRAPPER}} .sc_slider_controller .slider-slide:hover .sc_slider_controller_item_image',
					)
				);

				$this->end_controls_tab();

				$this->start_controls_tab(
					'tab_controller_slide_image_active',
					[
						'label' => __( 'Active', 'trx_addons' ),
					]
				);

				$this->add_group_control(
					Group_Control_Background::get_type(),
					array(
						'name'     => 'controller_slide_image_bg_active',
						'types'    => array( 'classic', 'gradient' ),
						'selector' => '{{WRAPPER}} .sc_slider_controller .swiper-slide-active .sc_slider_controller_item_image',
					)
				);

				$this->add_control(
					'controller_slide_image_bd_active',
					array(
						'label'     => __( 'Border Color', 'trx_addons' ),
						'type'      => Controls_Manager::COLOR,
						'selectors' => array(
							'{{WRAPPER}} .sc_slider_controller .swiper-slide-active .sc_slider_controller_item_image' => 'border-color: {{VALUE}};',
						),
					)
				);

				$this->add_group_control(
					Group_Control_Box_Shadow::get_type(),
					array(
						'name'     => 'controller_slide_image_shadow_active',
						'selector' => '{{WRAPPER}} .sc_slider_controller .swiper-slide-active .sc_slider_controller_item_image',
					)
				);

				$this->end_controls_tab();

				$this->end_controls_tabs();

				$this->end_controls_section();
			}

			/**
			 * Register style controls for the controller slide subtitle
			 */
			protected function register_style_controls_controller_slide_subtitle() {
				$this->start_controls_section(
					'section_sc_slider_style_controller_slide_subtitle',
					array(
						'label' => __( 'Controller Slide Subtitle', 'trx_addons' ),
						'tab' => Controls_Manager::TAB_STYLE,
						'condition' => [
							'engine' => ['swiper', 'elastistack'],
							'controller' => '1'
						]
					)
				);

				$this->start_controls_tabs( 'tabs_controller_slide_subtitle_style' );

				$this->start_controls_tab(
					'tab_controller_slide_subtitle_normal',
					[
						'label' => __( 'Normal', 'trx_addons' ),
					]
				);

				$this->add_group_control(
					Group_Control_Typography::get_type(),
					array(
						'name'     => 'controller_slide_subtitle_typography',
						'selector' => '{{WRAPPER}} .sc_slider_controller .sc_slider_controller_item_info_cats',
					)
				);

				$this->add_control(
					'controller_slide_subtitle_color',
					array(
						'label'     => __( 'Color', 'trx_addons' ),
						'type'      => Controls_Manager::COLOR,
						'selectors' => array(
							'{{WRAPPER}} .sc_slider_controller .sc_slider_controller_item_info_cats,
							 {{WRAPPER}} .sc_slider_controller .sc_slider_controller_item_info_cats > a' => 'color: {{VALUE}};',
						),
					)
				);

				$this->add_group_control(
					Group_Control_Text_Shadow::get_type(),
					array(
						'name'     => 'controller_slide_subtitle_text_shadow',
						'selector' => '{{WRAPPER}} .sc_slider_controller .sc_slider_controller_item_info_cats',
					)
				);

				$this->add_responsive_control(
					'controller_slide_subtitle_margin',
					array(
						'label'      => __( 'Margin', 'trx_addons' ),
						'type'       => Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
						'selectors'  => array(
							'{{WRAPPER}} .sc_slider_controller .sc_slider_controller_item_info_cats' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						),
					)
				);

				$this->end_controls_tab();

				$this->start_controls_tab(
					'tab_controller_slide_subtitle_hover',
					[
						'label' => __( 'Hover', 'trx_addons' ),
					]
				);

				$this->add_group_control(
					Group_Control_Typography::get_type(),
					array(
						'name'     => 'controller_slide_subtitle_typography_hover',
						'selector' => '{{WRAPPER}} .sc_slider_controller .slider-slide:hover .sc_slider_controller_item_info_cats',
					)
				);

				$this->add_control(
					'controller_slide_subtitle_color_hover',
					array(
						'label'     => __( 'Color', 'trx_addons' ),
						'type'      => Controls_Manager::COLOR,
						'selectors' => array(
							'{{WRAPPER}} .sc_slider_controller .slider-slide:hover .sc_slider_controller_item_info_cats,
							 {{WRAPPER}} .sc_slider_controller .slider-slide:hover .sc_slider_controller_item_info_cats > a' => 'color: {{VALUE}};',
						),
					)
				);

				$this->add_group_control(
					Group_Control_Text_Shadow::get_type(),
					array(
						'name'     => 'controller_slide_subtitle_text_shadow_hover',
						'selector' => '{{WRAPPER}} .sc_slider_controller .slider-slide:hover .sc_slider_controller_item_info_cats',
					)
				);

				$this->end_controls_tab();

				$this->start_controls_tab(
					'tab_controller_slide_subtitle_active',
					[
						'label' => __( 'Active', 'trx_addons' ),
					]
				);

				$this->add_group_control(
					Group_Control_Typography::get_type(),
					array(
						'name'     => 'controller_slide_subtitle_typography_active',
						'selector' => '{{WRAPPER}} .sc_slider_controller .swiper-slide-active .sc_slider_controller_item_info_cats',
					)
				);

				$this->add_control(
					'controller_slide_subtitle_color_active',
					array(
						'label'     => __( 'Color', 'trx_addons' ),
						'type'      => Controls_Manager::COLOR,
						'selectors' => array(
							'{{WRAPPER}} .sc_slider_controller .swiper-slide-active .sc_slider_controller_item_info_cats,
							 {{WRAPPER}} .sc_slider_controller .swiper-slide-active .sc_slider_controller_item_info_cats > a' => 'color: {{VALUE}};',
						),
					)
				);

				$this->add_group_control(
					Group_Control_Text_Shadow::get_type(),
					array(
						'name'     => 'controller_slide_subtitle_text_shadow_active',
						'selector' => '{{WRAPPER}} .sc_slider_controller .swiper-slide-active .sc_slider_controller_item_info_cats',
					)
				);

				$this->end_controls_tab();

				$this->end_controls_tabs();

				$this->end_controls_section();
			}

			/**
			 * Register style controls for the controller slide title
			 */
			protected function register_style_controls_controller_slide_title() {
				$this->start_controls_section(
					'section_sc_slider_style_controller_slide_title',
					array(
						'label' => __( 'Controller Slide Title', 'trx_addons' ),
						'tab' => Controls_Manager::TAB_STYLE,
						'condition' => [
							'engine' => ['swiper', 'elastistack'],
							'controller' => '1'
						]
					)
				);

				$this->start_controls_tabs( 'tabs_controller_slide_title_style' );

				$this->start_controls_tab(
					'tab_controller_slide_title_normal',
					[
						'label' => __( 'Normal', 'trx_addons' ),
					]
				);

				$this->add_group_control(
					Group_Control_Typography::get_type(),
					array(
						'name'     => 'controller_slide_title_typography',
						'selector' => '{{WRAPPER}} .sc_slider_controller .sc_slider_controller_item_info_title',
					)
				);

				$this->add_control(
					'controller_slide_title_number',
					array(
						'label'     => __( 'Slide Number', 'trx_addons' ),
						'label_yes' => __( 'Show', 'trx_addons' ),
						'label_no'  => __( 'Hide', 'trx_addons' ),
						'label_block' => false,
						'type'      => Controls_Manager::SWITCHER,
						'default'   => 'yes',
						'selectors_dictionary' => array(
							'yes' => '',
							''  => 'display: none;',
						),
						'selectors' => array(
							'{{WRAPPER}} .sc_slider_controller .sc_slider_controller_item_info_number' => '{{VALUE}}',
						),
					)
				);

				$this->add_control(
					'controller_slide_title_color',
					array(
						'label'     => __( 'Color', 'trx_addons' ),
						'type'      => Controls_Manager::COLOR,
						'selectors' => array(
							'{{WRAPPER}} .sc_slider_controller .sc_slider_controller_item_info_title,
							 {{WRAPPER}} .sc_slider_controller .sc_slider_controller_item_info_title > a' => 'color: {{VALUE}};',
						),
					)
				);

				$this->add_group_control(
					Group_Control_Text_Shadow::get_type(),
					array(
						'name'     => 'controller_slide_title_text_shadow',
						'selector' => '{{WRAPPER}} .sc_slider_controller .sc_slider_controller_item_info_title',
					)
				);

				$this->add_responsive_control(
					'controller_slide_title_margin',
					array(
						'label'      => __( 'Margin', 'trx_addons' ),
						'type'       => Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
						'selectors'  => array(
							'{{WRAPPER}} .sc_slider_controller .sc_slider_controller_item_info_title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						),
					)
				);

				$this->end_controls_tab();

				$this->start_controls_tab(
					'tab_controller_slide_title_hover',
					[
						'label' => __( 'Hover', 'trx_addons' ),
					]
				);

				$this->add_group_control(
					Group_Control_Typography::get_type(),
					array(
						'name'     => 'controller_slide_title_typography_hover',
						'selector' => '{{WRAPPER}} .sc_slider_controller .slider-slide:hover .sc_slider_controller_item_info_title',
					)
				);

				$this->add_control(
					'controller_slide_title_color_hover',
					array(
						'label'     => __( 'Color', 'trx_addons' ),
						'type'      => Controls_Manager::COLOR,
						'selectors' => array(
							'{{WRAPPER}} .sc_slider_controller .slider-slide:hover .sc_slider_controller_item_info_title,
							 {{WRAPPER}} .sc_slider_controller .slider-slide:hover .sc_slider_controller_item_info_title > a' => 'color: {{VALUE}};',
						),
					)
				);

				$this->add_group_control(
					Group_Control_Text_Shadow::get_type(),
					array(
						'name'     => 'controller_slide_title_text_shadow_hover',
						'selector' => '{{WRAPPER}} .sc_slider_controller .slider-slide:hover .sc_slider_controller_item_info_title',
					)
				);

				$this->end_controls_tab();

				$this->start_controls_tab(
					'tab_controller_slide_title_active',
					[
						'label' => __( 'Active', 'trx_addons' ),
					]
				);

				$this->add_group_control(
					Group_Control_Typography::get_type(),
					array(
						'name'     => 'controller_slide_title_typography_active',
						'selector' => '{{WRAPPER}} .sc_slider_controller .swiper-slide-active .sc_slider_controller_item_info_title',
					)
				);

				$this->add_control(
					'controller_slide_title_color_active',
					array(
						'label'     => __( 'Color', 'trx_addons' ),
						'type'      => Controls_Manager::COLOR,
						'selectors' => array(
							'{{WRAPPER}} .sc_slider_controller .swiper-slide-active .sc_slider_controller_item_info_title,
							 {{WRAPPER}} .sc_slider_controller .swiper-slide-active .sc_slider_controller_item_info_title > a' => 'color: {{VALUE}};',
						),
					)
				);

				$this->add_group_control(
					Group_Control_Text_Shadow::get_type(),
					array(
						'name'     => 'controller_slide_title_text_shadow_active',
						'selector' => '{{WRAPPER}} .sc_slider_controller .swiper-slide-active .sc_slider_controller_item_info_title',
					)
				);

				$this->end_controls_tab();

				$this->end_controls_tabs();

				$this->end_controls_section();
			}

			/**
			 * Register style controls for the controller slide meta
			 */
			protected function register_style_controls_controller_slide_meta() {
				$this->start_controls_section(
					'section_sc_slider_style_controller_slide_meta',
					array(
						'label' => __( 'Controller Slide Meta', 'trx_addons' ),
						'tab' => Controls_Manager::TAB_STYLE,
						'condition' => [
							'engine' => ['swiper', 'elastistack'],
							'controller' => '1'
						]
					)
				);

				$this->start_controls_tabs( 'tabs_controller_slide_meta_style' );

				$this->start_controls_tab(
					'tab_controller_slide_meta_normal',
					[
						'label' => __( 'Normal', 'trx_addons' ),
					]
				);

				$this->add_group_control(
					Group_Control_Typography::get_type(),
					array(
						'name'     => 'controller_slide_meta_typography',
						'selector' => '{{WRAPPER}} .sc_slider_controller .sc_slider_controller_item_info_date',
					)
				);

				$this->add_control(
					'controller_slide_meta_color',
					array(
						'label'     => __( 'Color', 'trx_addons' ),
						'type'      => Controls_Manager::COLOR,
						'selectors' => array(
							'{{WRAPPER}} .sc_slider_controller .sc_slider_controller_item_info_date' => 'color: {{VALUE}};',
						),
					)
				);

				$this->add_group_control(
					Group_Control_Text_Shadow::get_type(),
					array(
						'name'     => 'controller_slide_meta_text_shadow',
						'selector' => '{{WRAPPER}} .sc_slider_controller .sc_slider_controller_item_info_date',
					)
				);

				$this->add_responsive_control(
					'controller_slide_meta_margin',
					array(
						'label'      => __( 'Margin', 'trx_addons' ),
						'type'       => Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
						'selectors'  => array(
							'{{WRAPPER}} .sc_slider_controller .sc_slider_controller_item_info_date' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						),
					)
				);

				$this->end_controls_tab();

				$this->start_controls_tab(
					'tab_controller_slide_meta_hover',
					[
						'label' => __( 'Hover', 'trx_addons' ),
					]
				);

				$this->add_group_control(
					Group_Control_Typography::get_type(),
					array(
						'name'     => 'controller_slide_meta_typography_hover',
						'selector' => '{{WRAPPER}} .sc_slider_controller .slider-slide:hover .sc_slider_controller_item_info_date',
					)
				);

				$this->add_control(
					'controller_slide_meta_color_hover',
					array(
						'label'     => __( 'Color', 'trx_addons' ),
						'type'      => Controls_Manager::COLOR,
						'selectors' => array(
							'{{WRAPPER}} .sc_slider_controller .slider-slide:hover .sc_slider_controller_item_info_date' => 'color: {{VALUE}};',
						),
					)
				);

				$this->add_group_control(
					Group_Control_Text_Shadow::get_type(),
					array(
						'name'     => 'controller_slide_meta_text_shadow_hover',
						'selector' => '{{WRAPPER}} .sc_slider_controller .slider-slide:hover .sc_slider_controller_item_info_date',
					)
				);

				$this->end_controls_tab();

				$this->start_controls_tab(
					'tab_controller_slide_meta_active',
					[
						'label' => __( 'Active', 'trx_addons' ),
					]
				);

				$this->add_group_control(
					Group_Control_Typography::get_type(),
					array(
						'name'     => 'controller_slide_meta_typography_active',
						'selector' => '{{WRAPPER}} .sc_slider_controller .swiper-slide-active .sc_slider_controller_item_info_date',
					)
				);

				$this->add_control(
					'controller_slide_meta_color_active',
					array(
						'label'     => __( 'Color', 'trx_addons' ),
						'type'      => Controls_Manager::COLOR,
						'selectors' => array(
							'{{WRAPPER}} .sc_slider_controller .swiper-slide-active .sc_slider_controller_item_info_date' => 'color: {{VALUE}};',
						),
					)
				);

				$this->add_group_control(
					Group_Control_Text_Shadow::get_type(),
					array(
						'name'     => 'controller_slide_meta_text_shadow_active',
						'selector' => '{{WRAPPER}} .sc_slider_controller .swiper-slide-active .sc_slider_controller_item_info_date',
					)
				);

				$this->end_controls_tab();

				$this->end_controls_tabs();

				$this->end_controls_section();
			}

			/**
			 * Register style controls for arrows in the controller
			 *
			 * @access protected
			 */
			protected function register_style_controls_controller_arrows() {

				$this->start_controls_section(
					'section_sc_slider_style_controller_arrows',
					[
						'label' => __( 'Controller Arrows', 'trx_addons' ),
						'tab'   => Controls_Manager::TAB_STYLE,
						'condition' => [
							'engine' => ['swiper', 'elastistack'],
							'controller' => '1',
							'controller_controls' => '1'
						]
					]
				);

				$this->add_responsive_control(
					'controller_arrows_width',
					[
						'label' => __( 'Width', 'trx_addons' ),
						'type' => Controls_Manager::SLIDER,
						'range' => [
							'px' => [
								'min' => 0,
								'max' => 100,
								'step' => 1
							],
							'em' => [
								'min' => 0,
								'max' => 10,
								'step' => 0.1
							],
						],
						'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
						'selectors' => [
							'{{WRAPPER}} .slider_controller_arrows_wrap > a' => 'width: {{SIZE}}{{UNIT}};',
							// '{{WRAPPER}} .sc_slider_controller_horizontal' => 'padding-left: {{SIZE}}{{UNIT}}; padding-right: {{SIZE}}{{UNIT}};',
							// '{{WRAPPER}} .sc_slider_controller_horizontal .slider_controller_arrows_wrap > .slider_prev' => 'left: -{{SIZE}}{{UNIT}};',
							// '{{WRAPPER}} .sc_slider_controller_horizontal .slider_controller_arrows_wrap > .slider_next' => 'right: -{{SIZE}}{{UNIT}};',
						],
						// 'condition' => [
						// 	'controller_pos' => ['bottom']
						// ]
					]
				);

				$this->add_responsive_control(
					'controller_arrows_height',
					[
						'label' => __( 'Height', 'trx_addons' ),
						'type' => Controls_Manager::SLIDER,
						'range' => [
							'px' => [
								'min' => 0,
								'max' => 100,
								'step' => 1
							],
							'em' => [
								'min' => 0,
								'max' => 10,
								'step' => 0.1
							],
						],
						'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
						'selectors' => [
							'{{WRAPPER}} .slider_controller_arrows_wrap > a' => 'height: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}};',
							// '{{WRAPPER}} .sc_slider_controller_vertical' => 'padding-top: {{SIZE}}{{UNIT}}; padding-bottom: {{SIZE}}{{UNIT}};',
							// '{{WRAPPER}} .sc_slider_controller_vertical .slider_controller_arrows_wrap > .slider_prev' => 'top: -{{SIZE}}{{UNIT}};',
							// '{{WRAPPER}} .sc_slider_controller_vertical .slider_controller_arrows_wrap > .slider_next' => 'bottom: -{{SIZE}}{{UNIT}};',
						],
						// 'condition' => [
						// 	'controller_pos' => ['left', 'right']
						// ]
					]
				);

				$this->add_responsive_control(
					'controller_arrows_box_size',
					[
						'label' => __( 'Box Size', 'trx_addons' ),
						'type' => Controls_Manager::SLIDER,
						'range' => [
							'px' => [
								'min' => 0,
								'max' => 100,
								'step' => 1
							],
							'em' => [
								'min' => 0,
								'max' => 10,
								'step' => 0.1
							],
						],
						'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
						'selectors' => [
							'{{WRAPPER}} .sc_slider_controller_horizontal' => 'padding-left: {{SIZE}}{{UNIT}}; padding-right: {{SIZE}}{{UNIT}};',
							'{{WRAPPER}} .sc_slider_controller_horizontal .slider_controller_arrows_wrap > .slider_prev' => 'left: -{{SIZE}}{{UNIT}};',
							'{{WRAPPER}} .sc_slider_controller_horizontal .slider_controller_arrows_wrap > .slider_next' => 'right: -{{SIZE}}{{UNIT}};',
							'{{WRAPPER}} .sc_slider_controller_vertical' => 'padding-top: {{SIZE}}{{UNIT}}; padding-bottom: {{SIZE}}{{UNIT}};',
							'{{WRAPPER}} .sc_slider_controller_vertical .slider_controller_arrows_wrap > .slider_prev' => 'top: -{{SIZE}}{{UNIT}};',
							'{{WRAPPER}} .sc_slider_controller_vertical .slider_controller_arrows_wrap > .slider_next' => 'bottom: -{{SIZE}}{{UNIT}};',
						],
					]
				);

				$this->add_responsive_control(
					'controller_arrows_offset',
					[
						'label' => __( 'Offset', 'trx_addons' ),
						'type' => Controls_Manager::SLIDER,
						'range' => [
							'px' => [
								'min' => -100,
								'max' => 100,
								'step' => 1
							],
							'em' => [
								'min' => -10,
								'max' => 10,
								'step' => 0.1
							],
							'rem' => [
								'min' => -10,
								'max' => 10,
								'step' => 0.1
							],
						],
						'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
						'selectors' => [
							'{{WRAPPER}} .sc_slider_controller_horizontal .slider_controller_arrows_wrap > .slider_prev' => 'margin-left: {{SIZE}}{{UNIT}};',
							'{{WRAPPER}} .sc_slider_controller_horizontal .slider_controller_arrows_wrap > .slider_next' => 'margin-right: {{SIZE}}{{UNIT}};',
							'{{WRAPPER}} .sc_slider_controller_vertical .slider_controller_arrows_wrap > .slider_prev' => 'margin-top: {{SIZE}}{{UNIT}};',
							'{{WRAPPER}} .sc_slider_controller_vertical .slider_controller_arrows_wrap > .slider_next' => 'margin-bottom: {{SIZE}}{{UNIT}};',
						],
					]
				);

				$this->add_responsive_control(
					'controller_arrows_valign',
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
							'top'    => 'top: 0; bottom: auto;',
							'middle' => 'top: 50%; transform: translateY(-50%); bottom: auto;',
							'bottom' => 'top: auto; bottom: 0;',
						],
						'selectors'             => [
							'{{WRAPPER}} .slider_controller_arrows_wrap > a' => '{{VALUE}}',
						],
						'condition' => [
							'controller_pos' => ['bottom']
						]
					]
				);

				$this->add_responsive_control(
					'controller_arrows_halign',
					[
						'label'                 => __( 'Horizontal Position', 'trx_addons' ),
						'type'                  => Controls_Manager::CHOOSE,
						'label_block'           => false,
						'options'               => [
							'left' => [
								'title' => __( 'Left', 'trx_addons' ),
								'icon' => 'eicon-h-align-left',
							],
							'center' => [
								'title' => __( 'Center', 'trx_addons' ),
								'icon' => 'eicon-h-align-center',
							],
							'right' => [
								'title' => __( 'Right', 'trx_addons' ),
								'icon' => 'eicon-h-align-right',
							],
						],
						'selectors_dictionary'  => [
							'left'   => 'left: 0; right: auto;',
							'center' => 'left: 50%; transform: translateX(-50%); right: auto;',
							'right'  => 'left: auto; right: 0;',
						],
						'selectors'             => [
							'{{WRAPPER}} .slider_controller_arrows_wrap > a' => '{{VALUE}}',
						],
						'condition' => [
							'controller_pos' => ['left', 'right']
						]
					]
				);
		
				$params = trx_addons_get_icon_param('icon');
				$params = trx_addons_array_get_first_value( $params );
				unset( $params['name'] );
				$this->add_control( 'controller_icon', $params );

				$this->add_control(
					'controller_icon_size',
					[
						'label' => __( 'Icon size', 'trx_addons' ),
						'type' => Controls_Manager::SLIDER,
						'range' => [
							'px' => [
								'min' => 1,
								'max' => 100,
								'step' => 1
							],
							'em' => [
								'min' => 0.1,
								'max' => 10,
								'step' => 0.1
							],
						],
						'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
						'selectors' => [
							'{{WRAPPER}} .slider_controller_arrows_wrap > a:before' => 'font-size: {{SIZE}}{{UNIT}};',
						],
					]
				);

				$this->start_controls_tabs( 'tabs_controller_arrows_style' );

				$this->start_controls_tab(
					'tab_controller_arrows_normal',
					[
						'label' => __( 'Normal', 'trx_addons' ),
					]
				);

				$this->add_group_control(
					Group_Control_Background::get_type(),
					[
						'name'     => 'controller_arrows_background',
						'label'    => __( 'Background', 'trx_addons' ),
						'types'    => [ 'classic', 'gradient' ],
						'selector' => '{{WRAPPER}} .slider_controller_arrows_wrap > a',
					]
				);

				$this->add_control(
					'controller_icon_color',
					[
						'label' => __( 'Icon color', 'trx_addons' ),
						'label_block' => false,
						'type' => Controls_Manager::COLOR,
						'default' => '',
						// 'global' => array(
						// 	'active' => false,
						// ),
						'selectors' => [
							'{{WRAPPER}} .slider_controller_arrows_wrap > a:before' => 'color: {{VALUE}};',
						],
					]
				);

				$this->add_group_control(
					Group_Control_Border::get_type(),
					[
						'name'        => 'controller_arrows_border',
						'label'       => __( 'Border', 'trx_addons' ),
						'placeholder' => '1px',
						'default'     => '1px',
						'selector'    => '{{WRAPPER}} .slider_controller_arrows_wrap > a',
					]
				);

				$this->add_responsive_control(
					'controller_arrows_border_radius',
					[
						'label'      => __( 'Border Radius', 'trx_addons' ),
						'type'       => Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
						'selectors'  => [
							'{{WRAPPER}} .slider_controller_arrows_wrap > a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
					]
				);

				$this->add_group_control(
					Group_Control_Box_Shadow::get_type(),
					[
						'name'      => 'controller_arrows_box_shadow',
						'selector'  => '{{WRAPPER}} .slider_controller_arrows_wrap > a',
					]
				);

				$this->end_controls_tab();

				$this->start_controls_tab(
					'tab_controller_arrows_hover',
					[
						'label' => __( 'Hover', 'trx_addons' ),
					]
				);

				$this->add_group_control(
					Group_Control_Background::get_type(),
					[
						'name'     => 'controller_arrows_background_hover',
						'label'    => __( 'Background', 'trx_addons' ),
						'types'    => [ 'classic', 'gradient' ],
						'selector' => '{{WRAPPER}} .slider_controller_arrows_wrap > a:hover',
					]
				);

				$this->add_control(
					'controller_icon_color_hover',
					[
						'label' => __( 'Icon color', 'trx_addons' ),
						'label_block' => false,
						'type' => Controls_Manager::COLOR,
						'default' => '',
						// 'global' => array(
						// 	'active' => false,
						// ),
						'selectors' => [
							'{{WRAPPER}} .slider_controller_arrows_wrap > a:hover:before' => 'color: {{VALUE}};',
						],
					]
				);

				$this->add_control(
					'controller_arrows_border_color_hover',
					[
						'label' => __( 'Border color', 'trx_addons' ),
						'label_block' => false,
						'type' => Controls_Manager::COLOR,
						'default' => '',
						// 'global' => array(
						// 	'active' => false,
						// ),
						'selectors' => [
							'{{WRAPPER}} .slider_controller_arrows_wrap > a:hover' => 'border-color: {{VALUE}};',
						],
					]
				);

				$this->add_group_control(
					Group_Control_Box_Shadow::get_type(),
					[
						'name'      => 'controller_arrows_box_shadow_hover',
						'selector'  => '{{WRAPPER}} .slider_controller_arrows_wrap > a:hover',
					]
				);

				$this->end_controls_tab();

				$this->end_controls_tabs();

				$this->end_controls_section();
			}
		}
		
		// Register widget
		trx_addons_elm_register_widget( 'TRX_Addons_Elementor_Widget_Slider' );
	}
}


// Disable our widgets (shortcodes) to use in Elementor
// because we create special Elementor's widgets instead
if (!function_exists('trx_addons_widget_slider_black_list')) {
	add_action( 'elementor/widgets/black_list', 'trx_addons_widget_slider_black_list' );
	function trx_addons_widget_slider_black_list($list) {
		$list[] = 'trx_addons_widget_slider';
		return $list;
	}
}



// Elementor Slider Controller
//------------------------------------------------------
if (!function_exists('trx_addons_sc_slider_controller_add_in_elementor')) {
	add_action( trx_addons_elementor_get_action_for_widgets_registration(), 'trx_addons_sc_slider_controller_add_in_elementor' );
	function trx_addons_sc_slider_controller_add_in_elementor() {
		
		if (!class_exists('TRX_Addons_Elementor_Widget')) return;	

		class TRX_Addons_Elementor_Widget_Slider_Controller extends TRX_Addons_Elementor_Widget {

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
					'height' => 'size+unit',
					'slides_per_view' => 'size',
					'slides_space' => 'size',
					'interval' => 'size'
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
				return 'trx_sc_slider_controller';
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
				return __( 'Slider Controller', 'trx_addons' );
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
				return [ 'slider', 'carousel', 'gallery', 'swiper', 'controller' ];
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
				return 'eicon-slider-device trx_addons_elementor_widget_icon';
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
				$this->register_content_controls_controller();
				if ( apply_filters( 'trx_addons_filter_allow_sc_styles_in_elementor', false, 'widget_slider_controller' ) ) {
					$this->register_style_controls_controller_slide();
					$this->register_style_controls_controller_slide_info();
					$this->register_style_controls_controller_arrows();
				}
			}

			/**
			 * Register content controls with general settings.
			 */
			protected function register_content_controls_controller() {

				// Detect edit mode
				$is_edit_mode = trx_addons_elm_is_edit_mode();

				// Register controls
				$this->start_controls_section(
					'section_sc_slider_controller',
					[
						'label' => __( 'Slider Controller', 'trx_addons' ),
					]
				);
				
				$this->add_control(
					'slider_id',
					[
						'label' => __( 'Slave slider ID', 'trx_addons' ),
						'label_block' => false,
						'type' => Controls_Manager::TEXT,
						'placeholder' => __( "Controlled ID", 'trx_addons' ),
						'default' => ''
					]
				);

				$this->add_control(
					'height',
					[
						'label' => __( 'Controller height', 'trx_addons' ),
						'type' => Controls_Manager::SLIDER,
						'default' => [
							'size' => 0,
							'unit' => 'px'
						],
						'range' => [
							'px' => [
								'min' => 0,
								'max' => 300
							],
							'em' => [
								'min' => 0,
								'max' => 20
							]
						],
						'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
					]
				);

				$this->add_control(
					'controls',
					[
						'label' => __( 'Controls', 'trx_addons' ),
						'label_block' => false,
						'type' => Controls_Manager::SWITCHER,
						'label_off' => __( 'Off', 'trx_addons' ),
						'label_on' => __( 'On', 'trx_addons' ),
						'return_value' => '1'
					]
				);

				$this->add_control(
					'controller_style',
					[
						'label' => __( 'Style', 'trx_addons' ),
						'label_block' => false,
						'type' => Controls_Manager::SELECT,
						'options' => ! $is_edit_mode ? array() : trx_addons_get_list_sc_slider_controller_styles(),
						'default' => 'thumbs'
					]
				);

				$this->add_control(
					'effect',
					[
						'label' => __( 'Swiper effect', 'trx_addons' ),
						'label_block' => false,
						'type' => Controls_Manager::SELECT,
						'options' => ! $is_edit_mode ? array() : trx_addons_get_list_sc_slider_effects(),
						'default' => 'slide'
					]
				);

				$this->add_control(
					'direction',
					[
						'label' => __( 'Slides change direction', 'trx_addons' ),
						'label_block' => false,
						'type' => Controls_Manager::SELECT,
						'options' => ! $is_edit_mode ? array() : trx_addons_get_list_sc_slider_directions(),
						'default' => 'horizontal',
						'condition' => [
							'effect' => ['slide']
						]
					]
				);

				$this->add_control(
					'slides_per_view',
					[
						'label' => __( 'Slides per view', 'trx_addons' ),
						'type' => Controls_Manager::SLIDER,
						'default' => [
							'size' => 3
						],
						'range' => [
							'px' => [
								'min' => 1,
								'max' => 10
							],
						],
						'condition' => [
							'effect' => ['slide', 'coverflow', 'swap', 'cards', 'creative']
						]
					]
				);

				$this->add_control(
					'slides_space',
					[
						'label' => __( 'Space between slides', 'trx_addons' ),
						'type' => Controls_Manager::SLIDER,
						'default' => [
							'size' => 0
						],
						'range' => [
							'px' => [
								'min' => 0,
								'max' => 100
							],
						],
						'condition' => [
							'effect' => ['slide', 'coverflow', 'swap', 'cards', 'creative']
						]
					]
				);

				$this->add_control(
					'interval',
					[
						'label' => __( 'Interval between slides change', 'trx_addons' ),
						'description' => __( 'If empty or 0 - do not apply autoplay to the controller', 'trx_addons' ),
						'type' => Controls_Manager::SLIDER,
						'default' => [
							'size' => 7000
						],
						'range' => [
							'px' => [
								'min' => 0,
								'max' => 10000,
								'step' => 100
							],
						]
					]
				);
				
				$this->end_controls_section();
			}

			/**
			 * Register style controls for the controller slides
			 */
			protected function register_style_controls_controller_slide() {
				$this->start_controls_section(
					'section_sc_slider_controller_style_controller_slide',
					array(
						'label' => __( 'Controller Slide', 'trx_addons' ),
						'tab' => Controls_Manager::TAB_STYLE
					)
				);

				$this->start_controls_tabs( 'tabs_slider_controller_slide_style' );

				$this->start_controls_tab(
					'tab_slider_controller_slide_normal',
					[
						'label' => __( 'Normal', 'trx_addons' ),
					]
				);

				$this->add_group_control(
					Group_Control_Background::get_type(),
					array(
						'name'     => 'controller_slide_bg',
						'types'    => array( 'classic', 'gradient' ),
						'selector' => '{{WRAPPER}} .sc_slider_controller .slider-slide',
					)
				);

				$this->add_control(
					'controller_slide_overlay',
					array(
						'label'     => __( 'Overlay', 'trx_addons' ),
						'type'      => Controls_Manager::COLOR,
						'selectors' => array(
							'{{WRAPPER}} .sc_slider_controller .slider-slide:before' => 'background: {{VALUE}};',
						),
						'condition' => [
							'controller_style' => ['thumbs', 'thumbs_titles']
						]
					)
				);

				$this->add_group_control(
					Group_Control_Border::get_type(),
					array(
						'name'     => 'controller_slide_border',
						'selector' => '{{WRAPPER}} .sc_slider_controller .slider-slide',
					)
				);
		
				$this->add_responsive_control(
					'controller_slide_radius',
					array(
						'label'      => __( 'Border Radius', 'trx_addons' ),
						'type'       => Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
						'selectors'  => array(
							'{{WRAPPER}} .sc_slider_controller .slider-slide' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
						),
					)
				);

				$this->add_group_control(
					Group_Control_Box_Shadow::get_type(),
					array(
						'name'     => 'controller_slide_shadow',
						'selector' => '{{WRAPPER}} .sc_slider_controller .slider-slide',
					)
				);

				$this->end_controls_tab();

				$this->start_controls_tab(
					'tab_slider_controller_slide_hover',
					[
						'label' => __( 'Hover', 'trx_addons' ),
					]
				);

				$this->add_group_control(
					Group_Control_Background::get_type(),
					array(
						'name'     => 'controller_slide_bg_hover',
						'types'    => array( 'classic', 'gradient' ),
						'selector' => '{{WRAPPER}} .sc_slider_controller .slider-slide:hover',
					)
				);

				$this->add_control(
					'controller_slide_overlay_hover',
					array(
						'label'     => __( 'Overlay', 'trx_addons' ),
						'type'      => Controls_Manager::COLOR,
						'selectors' => array(
							'{{WRAPPER}} .sc_slider_controller .slider-slide:hover:before' => 'background: {{VALUE}};',
						),
						'condition' => [
							'controller_style' => ['thumbs', 'thumbs_titles']
						]
					)
				);

				$this->add_control(
					'controller_slide_bd_color_hover',
					array(
						'label'     => __( 'Border Color', 'trx_addons' ),
						'type'      => Controls_Manager::COLOR,
						'selectors' => array(
							'{{WRAPPER}} .sc_slider_controller .slider-slide:hover' => 'border-color: {{VALUE}};',
						),
					)
				);

				$this->add_group_control(
					Group_Control_Box_Shadow::get_type(),
					array(
						'name'     => 'controller_slide_shadow_hover',
						'selector' => '{{WRAPPER}} .sc_slider_controller .slider-slide:hover',
					)
				);

				$this->end_controls_tab();

				$this->start_controls_tab(
					'tab_slider_controller_slide_active',
					[
						'label' => __( 'Active', 'trx_addons' ),
					]
				);

				$this->add_group_control(
					Group_Control_Background::get_type(),
					array(
						'name'     => 'controller_slide_bg_active',
						'types'    => array( 'classic', 'gradient' ),
						'selector' => '{{WRAPPER}} .sc_slider_controller .swiper-slide-active',
					)
				);

				$this->add_control(
					'controller_slide_overlay_active',
					array(
						'label'     => __( 'Overlay', 'trx_addons' ),
						'type'      => Controls_Manager::COLOR,
						'selectors' => array(
							'{{WRAPPER}} .sc_slider_controller .swiper-slide-active:before' => 'background: {{VALUE}};',
						),
						'condition' => [
							'controller_style' => ['thumbs', 'thumbs_titles']
						]
					)
				);

				$this->add_control(
					'controller_slide_bd_color_active',
					array(
						'label'     => __( 'Border Color', 'trx_addons' ),
						'type'      => Controls_Manager::COLOR,
						'selectors' => array(
							'{{WRAPPER}} .sc_slider_controller .swiper-slide-active' => 'border-color: {{VALUE}};',
						),
					)
				);

				$this->add_control(
					'controller_slide_line_color_active',
					array(
						'label'     => __( 'Line Color', 'trx_addons' ),
						'type'      => Controls_Manager::COLOR,
						'selectors' => array(
							'{{WRAPPER}} .sc_slider_controller .swiper-slide-active:after' => 'background-color: {{VALUE}};',
						),
					)
				);

				$this->add_group_control(
					Group_Control_Box_Shadow::get_type(),
					array(
						'name'     => 'controller_slide_shadow_active',
						'selector' => '{{WRAPPER}} .sc_slider_controller .swiper-slide-active',
					)
				);

				$this->end_controls_tab();

				$this->end_controls_tabs();

				$this->end_controls_section();
			}

			/**
			 * Register style controls for the controller slide info
			 */
			protected function register_style_controls_controller_slide_info() {
				$this->start_controls_section(
					'section_sc_slider_controller_style_controller_slide_info',
					array(
						'label' => __( 'Controller Slide Info', 'trx_addons' ),
						'tab' => Controls_Manager::TAB_STYLE,
					)
				);

				$this->start_controls_tabs( 'tabs_slider_controller_slide_info_style' );

				$this->start_controls_tab(
					'tab_slider_controller_slide_info_normal',
					[
						'label' => __( 'Normal', 'trx_addons' ),
					]
				);

				$this->add_control(
					'controller_slide_info_alignment',
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
							'{{WRAPPER}} .sc_slider_controller .sc_slider_controller_info' => 'text-align: {{VALUE}}',
						),
						'condition' => [
							'controller_style' => ['titles', 'thumbs_titles']
						]
					)
				);

				$this->add_group_control(
					Group_Control_Typography::get_type(),
					array(
						'name'     => 'controller_slide_info_typography',
						'selector' => '{{WRAPPER}} .sc_slider_controller .sc_slider_controller_info',
					)
				);

				$this->add_responsive_control(
					'controller_slide_info_width',
					array(
						'label'      => __( 'Width', 'trx_addons' ),
						'type'       => Controls_Manager::SLIDER,
						'default'    => [
							'size' => '',
							'unit' => '%'
						],
						'size_units' => [ '%' ],
						'selectors'  => array(
							'{{WRAPPER}} .sc_slider_controller .sc_slider_controller_info' => 'width: {{SIZE}}{{UNIT}};',
						),
					)
				);

				$this->add_control(
					'controller_slide_info_number',
					array(
						'label'     => __( 'Slide Number', 'trx_addons' ),
						'label_yes' => __( 'Show', 'trx_addons' ),
						'label_no'  => __( 'Hide', 'trx_addons' ),
						'label_block' => false,
						'type'      => Controls_Manager::SWITCHER,
						'default'   => 'yes',
						'selectors_dictionary' => array(
							'yes' => '',
							''  => 'display: none;',
						),
						'selectors' => array(
							'{{WRAPPER}} .sc_slider_controller .sc_slider_controller_info_number' => '{{VALUE}}',
						),
					)
				);

				$this->add_responsive_control(
					'controller_slide_info_number_gap',
					array(
						'label'      => __( 'Number Gap', 'trx_addons' ),
						'type'       => Controls_Manager::SLIDER,
						'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
						'selectors'  => array(
							'{{WRAPPER}} .sc_slider_controller .sc_slider_controller_info_number' => 'margin-right: {{SIZE}}{{UNIT}};',
						),
					)
				);

				$this->add_control(
					'controller_slide_info_number_color',
					array(
						'label'     => __( 'Number Color', 'trx_addons' ),
						'type'      => Controls_Manager::COLOR,
						'selectors' => array(
							'{{WRAPPER}} .sc_slider_controller .sc_slider_controller_info_number' => 'color: {{VALUE}};',
						),
					)
				);

				$this->add_control(
					'controller_slide_info_title_color',
					array(
						'label'     => __( 'Title Color', 'trx_addons' ),
						'type'      => Controls_Manager::COLOR,
						'selectors' => array(
							'{{WRAPPER}} .sc_slider_controller .sc_slider_controller_info_title' => 'color: {{VALUE}};',
						),
					)
				);

				$this->add_group_control(
					Group_Control_Text_Shadow::get_type(),
					array(
						'name'     => 'controller_slide_info_text_shadow',
						'selector' => '{{WRAPPER}} .sc_slider_controller .sc_slider_controller_info',
					)
				);

				$this->add_responsive_control(
					'controller_slide_info_margin',
					array(
						'label'      => __( 'Margin', 'trx_addons' ),
						'type'       => Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
						'selectors'  => array(
							'{{WRAPPER}} .sc_slider_controller .sc_slider_controller_info' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						),
					)
				);

				$this->end_controls_tab();

				$this->start_controls_tab(
					'tab_slider_controller_slide_info_hover',
					[
						'label' => __( 'Hover', 'trx_addons' ),
					]
				);

				$this->add_group_control(
					Group_Control_Typography::get_type(),
					array(
						'name'     => 'controller_slide_info_typography_hover',
						'selector' => '{{WRAPPER}} .sc_slider_controller .slider-slide:hover .sc_slider_controller_info',
					)
				);

				$this->add_control(
					'controller_slide_info_number_color_hover',
					array(
						'label'     => __( 'Number Color', 'trx_addons' ),
						'type'      => Controls_Manager::COLOR,
						'selectors' => array(
							'{{WRAPPER}} .sc_slider_controller .slider-slide:hover .sc_slider_controller_info_number' => 'color: {{VALUE}};',
						),
					)
				);

				$this->add_control(
					'controller_slide_info_title_color_hover',
					array(
						'label'     => __( 'Title Color', 'trx_addons' ),
						'type'      => Controls_Manager::COLOR,
						'selectors' => array(
							'{{WRAPPER}} .sc_slider_controller .slider-slide:hover .sc_slider_controller_info_title' => 'color: {{VALUE}};',
						),
					)
				);

				$this->add_group_control(
					Group_Control_Text_Shadow::get_type(),
					array(
						'name'     => 'controller_slide_info_text_shadow_hover',
						'selector' => '{{WRAPPER}} .sc_slider_controller .slider-slide:hover .sc_slider_controller_info',
					)
				);

				$this->end_controls_tab();

				$this->start_controls_tab(
					'tab_slider_controller_slide_info_active',
					[
						'label' => __( 'Active', 'trx_addons' ),
					]
				);

				$this->add_group_control(
					Group_Control_Typography::get_type(),
					array(
						'name'     => 'controller_slide_info_typography_active',
						'selector' => '{{WRAPPER}} .sc_slider_controller .swiper-slide-active .sc_slider_controller_info',
					)
				);

				$this->add_control(
					'controller_slide_info_number_color_active',
					array(
						'label'     => __( 'Number Color', 'trx_addons' ),
						'type'      => Controls_Manager::COLOR,
						'selectors' => array(
							'{{WRAPPER}} .sc_slider_controller .swiper-slide-active .sc_slider_controller_info_number' => 'color: {{VALUE}};',
						),
					)
				);

				$this->add_control(
					'controller_slide_info_title_color_active',
					array(
						'label'     => __( 'Title Color', 'trx_addons' ),
						'type'      => Controls_Manager::COLOR,
						'selectors' => array(
							'{{WRAPPER}} .sc_slider_controller .swiper-slide-active .sc_slider_controller_info_title' => 'color: {{VALUE}};',
						),
					)
				);

				$this->add_group_control(
					Group_Control_Text_Shadow::get_type(),
					array(
						'name'     => 'controller_slide_infotext_shadow_active',
						'selector' => '{{WRAPPER}} .sc_slider_controller .swiper-slide-active .sc_slider_controller_info',
					)
				);

				$this->end_controls_tab();

				$this->end_controls_tabs();

				$this->end_controls_section();
			}

			/**
			 * Register style controls for arrows in the controller
			 *
			 * @access protected
			 */
			protected function register_style_controls_controller_arrows() {

				$this->start_controls_section(
					'section_sc_slider_controller_style_controller_arrows',
					[
						'label' => __( 'Controller Arrows', 'trx_addons' ),
						'tab'   => Controls_Manager::TAB_STYLE,
						'condition' => [
							'controls' => '1'
						]
					]
				);

				$this->add_responsive_control(
					'controller_arrows_width',
					[
						'label' => __( 'Width', 'trx_addons' ),
						'type' => Controls_Manager::SLIDER,
						'range' => [
							'px' => [
								'min' => 0,
								'max' => 100,
								'step' => 1
							],
							'em' => [
								'min' => 0,
								'max' => 10,
								'step' => 0.1
							],
						],
						'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
						'selectors' => [
							'{{WRAPPER}} .slider_controls_wrap > a' => 'width: {{SIZE}}{{UNIT}};',
						],
					]
				);

				$this->add_responsive_control(
					'controller_arrows_height',
					[
						'label' => __( 'Height', 'trx_addons' ),
						'type' => Controls_Manager::SLIDER,
						'range' => [
							'px' => [
								'min' => 0,
								'max' => 100,
								'step' => 1
							],
							'em' => [
								'min' => 0,
								'max' => 10,
								'step' => 0.1
							],
						],
						'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
						'selectors' => [
							'{{WRAPPER}} .slider_controls_wrap > a' => 'height: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}};',
						],
					]
				);

				$this->add_responsive_control(
					'controller_arrows_box_size',
					[
						'label' => __( 'Box Size', 'trx_addons' ),
						'type' => Controls_Manager::SLIDER,
						'range' => [
							'px' => [
								'min' => 0,
								'max' => 100,
								'step' => 1
							],
							'em' => [
								'min' => 0,
								'max' => 10,
								'step' => 0.1
							],
						],
						'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
						'selectors' => [
							'{{WRAPPER}} .sc_slider_controller_horizontal' => 'padding-left: {{SIZE}}{{UNIT}}; padding-right: {{SIZE}}{{UNIT}};',
							'{{WRAPPER}} .sc_slider_controller_horizontal .slider_controls_wrap > .slider_prev' => 'left: -{{SIZE}}{{UNIT}};',
							'{{WRAPPER}} .sc_slider_controller_horizontal .slider_controls_wrap > .slider_next' => 'right: -{{SIZE}}{{UNIT}};',
							'{{WRAPPER}} .sc_slider_controller_vertical' => 'padding-top: {{SIZE}}{{UNIT}}; padding-bottom: {{SIZE}}{{UNIT}};',
							'{{WRAPPER}} .sc_slider_controller_vertical .slider_controls_wrap > .slider_prev' => 'top: -{{SIZE}}{{UNIT}};',
							'{{WRAPPER}} .sc_slider_controller_vertical .slider_controls_wrap > .slider_next' => 'bottom: -{{SIZE}}{{UNIT}};',
						],
					]
				);

				$this->add_responsive_control(
					'controller_arrows_offset',
					[
						'label' => __( 'Offset', 'trx_addons' ),
						'type' => Controls_Manager::SLIDER,
						'range' => [
							'px' => [
								'min' => -100,
								'max' => 100,
								'step' => 1
							],
							'em' => [
								'min' => -10,
								'max' => 10,
								'step' => 0.1
							],
							'rem' => [
								'min' => -10,
								'max' => 10,
								'step' => 0.1
							],
						],
						'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
						'selectors' => [
							'{{WRAPPER}} .sc_slider_controller_horizontal .slider_controls_wrap > .slider_prev' => 'margin-left: {{SIZE}}{{UNIT}};',
							'{{WRAPPER}} .sc_slider_controller_horizontal .slider_controls_wrap > .slider_next' => 'margin-right: {{SIZE}}{{UNIT}};',
							'{{WRAPPER}} .sc_slider_controller_vertical .slider_controls_wrap > .slider_prev' => 'margin-top: {{SIZE}}{{UNIT}};',
							'{{WRAPPER}} .sc_slider_controller_vertical .slider_controls_wrap > .slider_next' => 'margin-bottom: {{SIZE}}{{UNIT}};',
						],
					]
				);

				$this->add_responsive_control(
					'controller_arrows_valign',
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
							'top'    => 'top: 0; bottom: auto;',
							'middle' => 'top: 50%; transform: translateY(-50%); bottom: auto;',
							'bottom' => 'top: auto; bottom: 0;',
						],
						'selectors'             => [
							'{{WRAPPER}} .slider_controls_wrap > a' => '{{VALUE}}',
						],
						'condition' => [
							'direction' => ['horizontal']
						]
					]
				);

				$this->add_responsive_control(
					'controller_arrows_halign',
					[
						'label'                 => __( 'Horizontal Position', 'trx_addons' ),
						'type'                  => Controls_Manager::CHOOSE,
						'label_block'           => false,
						'options'               => [
							'left' => [
								'title' => __( 'Left', 'trx_addons' ),
								'icon' => 'eicon-h-align-left',
							],
							'center' => [
								'title' => __( 'Center', 'trx_addons' ),
								'icon' => 'eicon-h-align-center',
							],
							'right' => [
								'title' => __( 'Right', 'trx_addons' ),
								'icon' => 'eicon-h-align-right',
							],
						],
						'selectors_dictionary'  => [
							'left'   => 'left: 0; right: auto;',
							'center' => 'left: 50%; transform: translateX(-50%); right: auto;',
							'right'  => 'left: auto; right: 0;',
						],
						'selectors'             => [
							'{{WRAPPER}} .slider_controls_wrap > a' => '{{VALUE}}',
						],
						'condition' => [
							'direction' => ['vertical']
						]
					]
				);
		
				$params = trx_addons_get_icon_param('icon');
				$params = trx_addons_array_get_first_value( $params );
				unset( $params['name'] );
				$this->add_control( 'icon', $params );

				$this->add_control(
					'controller_icon_size',
					[
						'label' => __( 'Icon size', 'trx_addons' ),
						'type' => Controls_Manager::SLIDER,
						'range' => [
							'px' => [
								'min' => 1,
								'max' => 100,
								'step' => 1
							],
							'em' => [
								'min' => 0.1,
								'max' => 10,
								'step' => 0.1
							],
						],
						'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
						'selectors' => [
							'{{WRAPPER}} .slider_controls_wrap > a:before' => 'font-size: {{SIZE}}{{UNIT}};',
						],
					]
				);

				$this->start_controls_tabs( 'tabs_slider_controller_arrows_style' );

				$this->start_controls_tab(
					'tab_slider_controller_arrows_normal',
					[
						'label' => __( 'Normal', 'trx_addons' ),
					]
				);

				$this->add_group_control(
					Group_Control_Background::get_type(),
					[
						'name'     => 'controller_arrows_background',
						'label'    => __( 'Background', 'trx_addons' ),
						'types'    => [ 'classic', 'gradient' ],
						'selector' => '{{WRAPPER}} .slider_controls_wrap > a',
					]
				);

				$this->add_control(
					'controller_icon_color',
					[
						'label' => __( 'Icon color', 'trx_addons' ),
						'label_block' => false,
						'type' => Controls_Manager::COLOR,
						'default' => '',
						// 'global' => array(
						// 	'active' => false,
						// ),
						'selectors' => [
							'{{WRAPPER}} .slider_controls_wrap > a:before' => 'color: {{VALUE}};',
						],
					]
				);

				$this->add_group_control(
					Group_Control_Border::get_type(),
					[
						'name'        => 'controller_arrows_border',
						'label'       => __( 'Border', 'trx_addons' ),
						'placeholder' => '1px',
						'default'     => '1px',
						'selector'    => '{{WRAPPER}} .slider_controls_wrap > a',
					]
				);

				$this->add_responsive_control(
					'controller_arrows_border_radius',
					[
						'label'      => __( 'Border Radius', 'trx_addons' ),
						'type'       => Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
						'selectors'  => [
							'{{WRAPPER}} .slider_controls_wrap > a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
					]
				);

				$this->add_group_control(
					Group_Control_Box_Shadow::get_type(),
					[
						'name'      => 'controller_arrows_box_shadow',
						'selector'  => '{{WRAPPER}} .slider_controls_wrap > a',
					]
				);

				$this->end_controls_tab();

				$this->start_controls_tab(
					'tab_slider_controls_hover',
					[
						'label' => __( 'Hover', 'trx_addons' ),
					]
				);

				$this->add_group_control(
					Group_Control_Background::get_type(),
					[
						'name'     => 'controller_arrows_background_hover',
						'label'    => __( 'Background', 'trx_addons' ),
						'types'    => [ 'classic', 'gradient' ],
						'selector' => '{{WRAPPER}} .slider_controls_wrap > a:hover',
					]
				);

				$this->add_control(
					'controller_icon_color_hover',
					[
						'label' => __( 'Icon color', 'trx_addons' ),
						'label_block' => false,
						'type' => Controls_Manager::COLOR,
						'default' => '',
						// 'global' => array(
						// 	'active' => false,
						// ),
						'selectors' => [
							'{{WRAPPER}} .slider_controls_wrap > a:hover:before' => 'color: {{VALUE}};',
						],
					]
				);

				$this->add_control(
					'controller_arrows_border_color_hover',
					[
						'label' => __( 'Border color', 'trx_addons' ),
						'label_block' => false,
						'type' => Controls_Manager::COLOR,
						'default' => '',
						// 'global' => array(
						// 	'active' => false,
						// ),
						'selectors' => [
							'{{WRAPPER}} .slider_controls_wrap > a:hover' => 'border-color: {{VALUE}};',
						],
					]
				);

				$this->add_group_control(
					Group_Control_Box_Shadow::get_type(),
					[
						'name'      => 'controller_arrows_box_shadow_hover',
						'selector'  => '{{WRAPPER}} .slider_controls_wrap > a:hover',
					]
				);

				$this->end_controls_tab();

				$this->end_controls_tabs();

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
				trx_addons_get_template_part(TRX_ADDONS_PLUGIN_WIDGETS . "slider/tpe.slider_controller.php",
										'trx_addons_args_widget_slider_controller',
										array('element' => $this)
									);
			}
		}
		
		// Register widget
		trx_addons_elm_register_widget( 'TRX_Addons_Elementor_Widget_Slider_Controller' );
	}
}




// Elementor Slider Controls
//------------------------------------------------------
if (!function_exists('trx_addons_sc_slider_controls_add_in_elementor')) {
	add_action( trx_addons_elementor_get_action_for_widgets_registration(), 'trx_addons_sc_slider_controls_add_in_elementor' );
	function trx_addons_sc_slider_controls_add_in_elementor() {
		
		if (!class_exists('TRX_Addons_Elementor_Widget')) return;	

		class TRX_Addons_Elementor_Widget_Slider_Controls extends TRX_Addons_Elementor_Widget {

			/**
			 * Retrieve widget name.
			 *
			 * @since 1.6.41
			 * @access public
			 *
			 * @return string Widget name.
			 */
			public function get_name() {
				return 'trx_sc_slider_controls';
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
				return __( 'Slider Controls', 'trx_addons' );
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
				return [ 'slider', 'carousel', 'gallery', 'swiper', 'controls', 'arrows' ];
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
				return 'eicon-post-navigation trx_addons_elementor_widget_icon';
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
				$this->register_content_controls_slider();
				if ( apply_filters( 'trx_addons_filter_allow_sc_styles_in_elementor', false, 'widget_slider' ) ) {
					$this->register_style_controls_arrows();
					$this->register_style_controls_pagination();
				}
			}

			/**
			 * Register content controls with general settings.
			 */
			protected function register_content_controls_slider() {

				// Detect edit mode
				$is_edit_mode = trx_addons_elm_is_edit_mode();

				// Register controls
				$this->start_controls_section(
					'section_sc_slider_controls',
					[
						'label' => __( 'Slider Controls', 'trx_addons' ),
					]
				);
				
				$this->add_control(
					'slider_id',
					[
						'label' => __( 'Target slider ID', 'trx_addons' ),
						'label_block' => false,
						'type' => Controls_Manager::TEXT,
						'placeholder' => __( "Controlled ID", 'trx_addons' ),
						'default' => ''
					]
				);

				$this->add_control(
					'controls_style',
					[
						'label' => __( 'Style', 'trx_addons' ),
						'label_block' => false,
						'type' => Controls_Manager::SELECT,
						'options' => ! $is_edit_mode ? array() : trx_addons_get_list_sc_slider_controls_styles(),
						'default' => 'default'
					]
				);

				$this->add_control(
					'align',
					[
						'label' => __( 'Alignment', 'trx_addons' ),
						'label_block' => false,
						'type' => Controls_Manager::SELECT,
						'options' => ! $is_edit_mode ? array() : trx_addons_get_list_sc_aligns(false, false),
						'default' => 'left'
					]
				);

				$this->add_control(
					'hide_prev',
					[
						'label' => __( "Hide the button 'Prev'", 'trx_addons' ),
						'label_block' => false,
						'type' => Controls_Manager::SWITCHER,
						'label_off' => __( 'Off', 'trx_addons' ),
						'label_on' => __( 'On', 'trx_addons' ),
						'return_value' => '1'
					]
				);

				$this->add_control(
					'title_prev',
					[
						'label' => __( "Title of the button 'Prev'", 'trx_addons' ),
						'label_block' => false,
						'type' => Controls_Manager::TEXT,
						'placeholder' => __( "Prev", 'trx_addons' ),
						'default' => '',
						'condition' => [
							'hide_prev' => ''
						]
					]
				);

				$this->add_control(
					'hide_next',
					[
						'label' => __( "Hide the button 'Next'", 'trx_addons' ),
						'label_block' => false,
						'type' => Controls_Manager::SWITCHER,
						'label_off' => __( 'Off', 'trx_addons' ),
						'label_on' => __( 'On', 'trx_addons' ),
						'return_value' => '1'
					]
				);

				$this->add_control(
					'title_next',
					[
						'label' => __( "Title of the button 'Next'", 'trx_addons' ),
						'label_block' => false,
						'type' => Controls_Manager::TEXT,
						'placeholder' => __( "Next", 'trx_addons' ),
						'default' => '',
						'condition' => [
							'hide_next' => ''
						]
					]
				);

				$this->add_control(
					'pagination_style',
					[
						'label' => __( "Show pagination", 'trx_addons' ),
						'label_block' => false,
						'type' => Controls_Manager::SELECT,
						'options' => ! $is_edit_mode ? array() : trx_addons_get_list_sc_slider_controls_paginations_types(),
						'default' => 'none'
					]
				);

				$this->add_control(
					'pagination_title_html_tag',
					[
						'label'                => __( 'Pagination HTML Tag', 'trx_addons' ),
						'type'                 => Controls_Manager::SELECT,
						'default'              => 'span',
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
						'condition' => [
							'pagination_style' => 'titles'
						]
					]
				);

				$this->end_controls_section();
			}

			/**
			 * Register style controls for arrows
			 *
			 * @access protected
			 */
			protected function register_style_controls_arrows() {

				$this->start_controls_section(
					'section_sc_slider_controls_style_arrows',
					[
						'label' => __( 'Arrows', 'trx_addons' ),
						'tab'   => Controls_Manager::TAB_STYLE,
						'condition' => [
							'hide_prev' => '',
							'hide_next' => ''
						]
					]
				);

				$this->add_responsive_control(
					'arrows_width',
					[
						'label' => __( 'Width', 'trx_addons' ),
						'type' => Controls_Manager::SLIDER,
						'range' => [
							'px' => [
								'min' => 0,
								'max' => 100,
								'step' => 1
							],
							'em' => [
								'min' => 0,
								'max' => 10,
								'step' => 0.1
							],
						],
						'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
						'selectors' => [
							'{{WRAPPER}} .slider_controls_wrap > a' => 'width: {{SIZE}}{{UNIT}};',
						]
					]
				);

				$this->add_responsive_control(
					'arrows_height',
					[
						'label' => __( 'Height', 'trx_addons' ),
						'type' => Controls_Manager::SLIDER,
						'range' => [
							'px' => [
								'min' => 0,
								'max' => 100,
								'step' => 1
							],
							'em' => [
								'min' => 0,
								'max' => 10,
								'step' => 0.1
							],
						],
						'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
						'selectors' => [
							'{{WRAPPER}} .slider_controls_wrap > a' => 'height: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}};',
						],
					]
				);

				$this->add_responsive_control(
					'arrows_offset',
					[
						'label' => __( 'Offset', 'trx_addons' ),
						'type' => Controls_Manager::SLIDER,
						'range' => [
							'px' => [
								'min' => 0,
								'max' => 100,
								'step' => 1
							],
							'em' => [
								'min' => 0,
								'max' => 10,
								'step' => 0.1
							],
						],
						'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
						'selectors' => [
							'{{WRAPPER}} .slider_controls_wrap > .slider_prev' => 'margin-left: {{SIZE}}{{UNIT}};',
							'{{WRAPPER}} .slider_controls_wrap > .slider_next' => 'margin-right: {{SIZE}}{{UNIT}};',
						],
					]
				);

				$this->add_responsive_control(
					'arrows_gap',
					[
						'label' => __( 'Gap', 'trx_addons' ),
						'type' => Controls_Manager::SLIDER,
						'range' => [
							'px' => [
								'min' => 0,
								'max' => 100,
								'step' => 1
							],
							'em' => [
								'min' => 0,
								'max' => 10,
								'step' => 0.1
							],
						],
						'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
						'selectors' => [
							'{{WRAPPER}} .slider_controls_wrap > .slider_prev + .slider_next' => 'margin-left: {{SIZE}}{{UNIT}};',
							'{{WRAPPER}} .slider_controls_wrap > .slider_prev + .slider_pagination_wrap' => 'margin-left: {{SIZE}}{{UNIT}};',
							'{{WRAPPER}} .slider_controls_wrap > .slider_next + .slider_pagination_wrap' => 'margin-left: {{SIZE}}{{UNIT}};',
						],
					]
				);

				// $params = trx_addons_get_icon_param('icon');
				// $params = trx_addons_array_get_first_value( $params );
				// unset( $params['name'] );
				// $this->add_control( 'controls_icon', $params );
				$this->add_icon_param();

				$this->add_control(
					'arrows_icon_size',
					[
						'label' => __( 'Icon size', 'trx_addons' ),
						'type' => Controls_Manager::SLIDER,
						'range' => [
							'px' => [
								'min' => 1,
								'max' => 100,
								'step' => 1
							],
							'em' => [
								'min' => 0.1,
								'max' => 10,
								'step' => 0.1
							],
						],
						'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
						'selectors' => [
							'{{WRAPPER}} .slider_controls_wrap > a:before' => 'font-size: {{SIZE}}{{UNIT}};',
						],
					]
				);

				$this->add_group_control(
					Group_Control_Typography::get_type(),
					array(
						'name'     => 'arrows_typography',
						'selector' => '{{WRAPPER}} .slider_controls_wrap > a',
						'condition' => [
							'title_prev!' => '',
							'title_next!' => ''
						]
					)
				);

				$this->start_controls_tabs( 'tabs_slider_controls_arrows_style' );

				$this->start_controls_tab(
					'tab_slider_controls_arrows_normal',
					[
						'label' => __( 'Normal', 'trx_addons' ),
					]
				);

				$this->add_group_control(
					Group_Control_Background::get_type(),
					[
						'name'     => 'arrows_background',
						'label'    => __( 'Background', 'trx_addons' ),
						'types'    => [ 'classic', 'gradient' ],
						'selector' => '{{WRAPPER}} .slider_controls_wrap > a',
					]
				);

				$this->add_control(
					'arrows_icon_color',
					[
						'label' => __( 'Icon/Text color', 'trx_addons' ),
						'label_block' => false,
						'type' => Controls_Manager::COLOR,
						'default' => '',
						// 'global' => array(
						// 	'active' => false,
						// ),
						'selectors' => [
							'{{WRAPPER}} .slider_controls_wrap > a' => 'color: {{VALUE}};',
						],
					]
				);

				$this->add_group_control(
					Group_Control_Border::get_type(),
					[
						'name'        => 'arrows_border',
						'label'       => __( 'Border', 'trx_addons' ),
						'placeholder' => '1px',
						'default'     => '1px',
						'selector'    => '{{WRAPPER}} .slider_controls_wrap > a',
					]
				);

				$this->add_responsive_control(
					'arrows_border_radius_prev',
					[
						'label'      => __( 'Border Radius (button "Prev")', 'trx_addons' ),
						'type'       => Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
						'selectors'  => [
							'{{WRAPPER}} .slider_controls_wrap > .slider_prev' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
					]
				);

				$this->add_responsive_control(
					'arrows_border_radius_next',
					[
						'label'      => __( 'Border Radius (button "Next")', 'trx_addons' ),
						'type'       => Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
						'selectors'  => [
							'{{WRAPPER}} .slider_controls_wrap > .slider_next' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
					]
				);

				$this->add_group_control(
					Group_Control_Box_Shadow::get_type(),
					[
						'name'      => 'arrows_box_shadow',
						'selector'  => '{{WRAPPER}} .slider_controls_wrap > a',
					]
				);

				$this->end_controls_tab();

				$this->start_controls_tab(
					'tab_slider_controls_arrows_hover',
					[
						'label' => __( 'Hover', 'trx_addons' ),
					]
				);

				$this->add_group_control(
					Group_Control_Background::get_type(),
					[
						'name'     => 'arrows_background_hover',
						'label'    => __( 'Background', 'trx_addons' ),
						'types'    => [ 'classic', 'gradient' ],
						'selector' => '{{WRAPPER}} .slider_controls_wrap > a:hover',
					]
				);

				$this->add_control(
					'arrows_icon_color_hover',
					[
						'label' => __( 'Icon color', 'trx_addons' ),
						'label_block' => false,
						'type' => Controls_Manager::COLOR,
						'default' => '',
						// 'global' => array(
						// 	'active' => false,
						// ),
						'selectors' => [
							'{{WRAPPER}} .slider_controls_wrap > a:hover' => 'color: {{VALUE}};',
						],
					]
				);

				$this->add_control(
					'arrows_border_color_hover',
					[
						'label' => __( 'Border color', 'trx_addons' ),
						'label_block' => false,
						'type' => Controls_Manager::COLOR,
						'default' => '',
						// 'global' => array(
						// 	'active' => false,
						// ),
						'selectors' => [
							'{{WRAPPER}} .slider_controls_wrap > a:hover' => 'border-color: {{VALUE}};',
						],
					]
				);

				$this->add_group_control(
					Group_Control_Box_Shadow::get_type(),
					[
						'name'      => 'arrows_box_shadow_hover',
						'selector'  => '{{WRAPPER}} .slider_controls_wrap > a:hover',
					]
				);

				$this->end_controls_tab();

				$this->end_controls_tabs();

				$this->end_controls_section();
			}

			/**
			 * Register style controls for pagination
			 *
			 * @access protected
			 */
			protected function register_style_controls_pagination() {

				$this->start_controls_section(
					'section_sc_slider_controls_style_pagination',
					[
						'label' => __( 'Pagination', 'trx_addons' ),
						'tab'   => Controls_Manager::TAB_STYLE,
						'condition' => [
							'pagination_style!' => ['none']
						]
					]
				);

				$this->add_responsive_control(
					'pagination_size',
					[
						'label' => __( 'Size', 'trx_addons' ),
						'type' => Controls_Manager::SLIDER,
						'range' => [
							'px' => [
								'min' => 0,
								'max' => 100,
								'step' => 1
							],
							'em' => [
								'min' => 0,
								'max' => 10,
								'step' => 0.1
							],
						],
						'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
						'selectors' => [
							'{{WRAPPER}} .sc_slider_controls.slider_pagination_style_bullets .slider_pagination_wrap .swiper-pagination-bullet' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}};',
							'{{WRAPPER}} .sc_slider_controls.slider_pagination_style_bullets .slider_pagination_wrap' => 'height: calc( {{SIZE}}{{UNIT}} + 2px ); line-height: calc( {{SIZE}}{{UNIT}} + 2px );',
							'{{WRAPPER}} .sc_slider_controls.slider_pagination_style_thumbs .slider_pagination_wrap' => 'height: {{SIZE}}{{UNIT}};',
							'{{WRAPPER}} .sc_slider_controls.slider_pagination_style_progressbar .slider_pagination_wrap' => 'height: {{SIZE}}{{UNIT}};',
						],
						'condition' => [
							'pagination_style' => ['bullets', 'thumbs', 'progressbar']
						]
					]
				);

				$this->add_responsive_control(
					'pagination_gap',
					[
						'label' => __( 'Gap', 'trx_addons' ),
						'type' => Controls_Manager::SLIDER,
						'range' => [
							'px' => [
								'min' => 0,
								'max' => 100,
								'step' => 1
							],
							'em' => [
								'min' => 0,
								'max' => 10,
								'step' => 0.1
							],
						],
						'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
						'selectors' => [
							'{{WRAPPER}} .sc_slider_controls.slider_pagination_style_bullets .slider_pagination_wrap .swiper-pagination-bullet' => 'margin-left: calc( {{SIZE}}{{UNIT}} / 2 ); margin-right: calc( {{SIZE}}{{UNIT}} / 2 );',
							'{{WRAPPER}} .sc_slider_controls.slider_pagination_style_thumbs .slider_pagination_wrap .slider_pagination_button' => 'margin-left: calc( {{SIZE}}{{UNIT}} / 2 ); margin-right: calc( {{SIZE}}{{UNIT}} / 2 );',
							'{{WRAPPER}} .sc_slider_controls.slider_pagination_style_fraction .slider_pagination_wrap .slider_pagination_current' => 'margin-right: {{SIZE}}{{UNIT}};',
							'{{WRAPPER}} .sc_slider_controls.slider_pagination_style_fraction .slider_pagination_wrap .slider_pagination_total' => 'margin-left: {{SIZE}}{{UNIT}};',
							'{{WRAPPER}} .sc_slider_controls.slider_pagination_style_titles .slider_pagination_wrap .slider_pagination_title + .slider_pagination_title' => 'margin-top: {{SIZE}}{{UNIT}};',
						],
						'condition' => [
							'pagination_style' => ['bullets', 'thumbs', 'fraction', 'titles']
						]
					]
				);

				$this->start_controls_tabs( 'tabs_pagination_style' );

				$this->start_controls_tab(
					'tab_pagination_normal',
					[
						'label' => __( 'Normal', 'trx_addons' ),
					]
				);

				$this->add_group_control(
					Group_Control_Typography::get_type(),
					array(
						'name'     => 'pagination_typography',
						'selector' => '{{WRAPPER}} .sc_slider_controls.slider_pagination_style_fraction .slider_pagination_wrap, 
									   {{WRAPPER}} .sc_slider_controls.slider_pagination_style_titles .slider_pagination_wrap .slider_pagination_title',
						'condition' => [
							'pagination_style' => ['fraction', 'titles']
						]
					)
				);

				$this->add_group_control(
					Group_Control_Background::get_type(),
					[
						'name'     => 'pagination_background',
						'label'    => __( 'Background', 'trx_addons' ),
						'types'    => [ 'classic', 'gradient' ],
						'selector' => '{{WRAPPER}} .sc_slider_controls.slider_pagination_style_bullets .slider_pagination_wrap > .swiper-pagination-bullet,
									   {{WRAPPER}} .sc_slider_controls.slider_pagination_style_progressbar .slider_pagination_wrap,
									   {{WRAPPER}} .sc_slider_controls.slider_pagination_style_thumbs .slider_pagination_wrap .slider_pagination_button:before,
									   {{WRAPPER}} .sc_slider_controls.slider_pagination_style_titles .slider_pagination_wrap .slider_pagination_title',
						'condition' => [
							'pagination_style' => ['bullets', 'progressbar', 'thumbs', 'titles']
						]
					]
				);

				$this->add_responsive_control(
					'pagination_opacity',
					[
						'label' => __( 'Overlay Opacity', 'trx_addons' ),
						'type' => Controls_Manager::SLIDER,
						'range' => [
							'px' => [
								'min' => 0,
								'max' => 1,
								'step' => 0.05
							],
						],
						'size_units' => [ 'px' ],
						'selectors' => [ 
							'{{WRAPPER}} .sc_slider_controls.slider_pagination_style_thumbs .slider_pagination_wrap .slider_pagination_button:before' => 'opacity: {{SIZE}};',
						],
						'condition' => [
							'pagination_style' => ['thumbs']
						]
					]
				);

				$this->add_control(
					'pagination_color',
					[
						'label' => __( 'Color', 'trx_addons' ),
						'label_block' => false,
						'type' => Controls_Manager::COLOR,
						'default' => '',
						// 'global' => array(
						// 	'active' => false,
						// ),
						'selectors' => [
							'{{WRAPPER}} .sc_slider_controls.slider_pagination_style_fraction .slider_pagination_wrap,
							 {{WRAPPER}} .sc_slider_controls.slider_pagination_style_fraction .slider_pagination_wrap .swiper-pagination-total,
							 {{WRAPPER}} .sc_slider_controls.slider_pagination_style_titles .slider_pagination_wrap .slider_pagination_title' => 'color: {{VALUE}};',
						],
						'condition' => [
							'pagination_style' => ['fraction', 'titles']
						]
					]
				);

				$this->add_group_control(
					Group_Control_Border::get_type(),
					[
						'name'        => 'pagination_border',
						'label'       => __( 'Border', 'trx_addons' ),
						'selector'    => '{{WRAPPER}} .sc_slider_controls.slider_pagination_style_bullets .slider_pagination_wrap > .swiper-pagination-bullet,
										  {{WRAPPER}} .sc_slider_controls.slider_pagination_style_progressbar .slider_pagination_wrap,
										  {{WRAPPER}} .sc_slider_controls.slider_pagination_style_thumbs .slider_pagination_wrap .slider_pagination_button,
										  {{WRAPPER}} .sc_slider_controls.slider_pagination_style_titles .slider_pagination_wrap .slider_pagination_title',
						'condition' => [
							'pagination_style' => ['bullets', 'progressbar', 'thumbs', 'titles']
						]
					]
				);

				$this->add_responsive_control(
					'pagination_border_radius',
					[
						'label'      => __( 'Border Radius', 'trx_addons' ),
						'type'       => Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
						'selectors'  => [
							'{{WRAPPER}} .sc_slider_controls.slider_pagination_style_bullets .slider_pagination_wrap > .swiper-pagination-bullet,
							 {{WRAPPER}} .sc_slider_controls.slider_pagination_style_progressbar .slider_pagination_wrap,
							 {{WRAPPER}} .sc_slider_controls.slider_pagination_style_progressbar .slider_pagination_wrap .slider_progress_bar,
							 {{WRAPPER}} .sc_slider_controls.slider_pagination_style_thumbs .slider_pagination_wrap .slider_pagination_button,
							 {{WRAPPER}} .sc_slider_controls.slider_pagination_style_titles .slider_pagination_wrap .slider_pagination_title' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
						'condition' => [
							'pagination_style' => ['bullets', 'progressbar', 'thumbs', 'titles']
						]
					]
				);

				$this->add_control(
					'pagination_padding',
					[
						'label'      => __( 'Padding', 'trx_addons' ),
						'type'       => Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
						'selectors'  => [
							'{{WRAPPER}} .sc_slider_controls.slider_pagination_style_titles .slider_pagination_wrap .slider_pagination_title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
						'condition' => [
							'pagination_style' => ['titles']
						]
					]
				);

				$this->add_group_control(
					Group_Control_Box_Shadow::get_type(),
					[
						'name'      => 'pagination_box_shadow',
						'selector'  => '{{WRAPPER}} .sc_slider_controls.slider_pagination_style_bullets .slider_pagination_wrap > .swiper-pagination-bullet,
										{{WRAPPER}} .sc_slider_controls.slider_pagination_style_progressbar .slider_pagination_wrap,
										{{WRAPPER}} .sc_slider_controls.slider_pagination_style_thumbs .slider_pagination_wrap .slider_pagination_button,
										{{WRAPPER}} .sc_slider_controls.slider_pagination_style_titles .slider_pagination_wrap .slider_pagination_title',
						'condition' => [
							'pagination_style' => ['bullets', 'progressbar', 'thumbs', 'titles']
						]
					]
				);

				$this->add_group_control(
					Group_Control_Text_Shadow::get_type(),
					[
						'name'      => 'pagination_text_shadow',
						'selector'  => '{{WRAPPER}} .sc_slider_controls.slider_pagination_style_fraction .slider_pagination_wrap,
										{{WRAPPER}} .sc_slider_controls.slider_pagination_style_titles .slider_pagination_wrap .slider_pagination_title',
						'condition' => [
							'pagination_style' => ['fraction', 'titles']
						]
					]
				);

				$this->end_controls_tab();

				$this->start_controls_tab(
					'tab_pagination_hover',
					[
						'label' => __( 'Hover', 'trx_addons' ),
					]
				);

				$this->add_group_control(
					Group_Control_Background::get_type(),
					[
						'name'     => 'pagination_background_hover',
						'label'    => __( 'Background', 'trx_addons' ),
						'types'    => [ 'classic', 'gradient' ],
						'selector' => '{{WRAPPER}} .sc_slider_controls.slider_pagination_style_bullets .slider_pagination_wrap > .swiper-pagination-bullet:hover,
									   {{WRAPPER}} .sc_slider_controls.slider_pagination_style_progressbar .slider_pagination_wrap:hover,
									   {{WRAPPER}} .sc_slider_controls.slider_pagination_style_thumbs .slider_pagination_wrap .slider_pagination_button:hover:before,
									   {{WRAPPER}} .sc_slider_controls.slider_pagination_style_titles .slider_pagination_wrap .slider_pagination_title:hover',
						'condition' => [
							'pagination_style' => ['bullets', 'progressbar', 'thumbs', 'titles']
						]
					]
				);

				$this->add_control(
					'pagination_color_hover',
					[
						'label' => __( 'Color', 'trx_addons' ),
						'label_block' => false,
						'type' => Controls_Manager::COLOR,
						'default' => '',
						'selectors' => [
							'{{WRAPPER}} .sc_slider_controls.slider_pagination_style_titles .slider_pagination_wrap .slider_pagination_title:hover' => 'color: {{VALUE}};',
						],
						'condition' => [
							'pagination_style' => ['titles']
						]
					]
				);

				$this->add_responsive_control(
					'pagination_opacity_hover',
					[
						'label' => __( 'Overlay Opacity', 'trx_addons' ),
						'type' => Controls_Manager::SLIDER,
						'range' => [
							'px' => [
								'min' => 0,
								'max' => 1,
								'step' => 0.05
							],
						],
						'size_units' => [ 'px' ],
						'selectors' => [ 
							'{{WRAPPER}} .sc_slider_controls.slider_pagination_style_thumbs .slider_pagination_wrap .slider_pagination_button:hover:before' => 'opacity: {{SIZE}};',
						],
						'condition' => [
							'pagination_style' => ['thumbs']
						]
					]
				);

				$this->add_group_control(
					Group_Control_Border::get_type(),
					[
						'name'        => 'pagination_border_hover',
						'label'       => __( 'Border', 'trx_addons' ),
						'selector'    => '{{WRAPPER}} .sc_slider_controls.slider_pagination_style_bullets .slider_pagination_wrap > .swiper-pagination-bullet:hover,
										  {{WRAPPER}} .sc_slider_controls.slider_pagination_style_progressbar .slider_pagination_wrap:hover,
										  {{WRAPPER}} .sc_slider_controls.slider_pagination_style_thumbs .slider_pagination_wrap .slider_pagination_button:hover,
										  {{WRAPPER}} .sc_slider_controls.slider_pagination_style_titles .slider_pagination_wrap .slider_pagination_title:hover',
						'condition' => [
							'pagination_style' => ['bullets', 'progressbar', 'thumbs', 'titles']
						]
					]
				);

				$this->add_responsive_control(
					'pagination_border_radius_hover',
					[
						'label'      => __( 'Border Radius', 'trx_addons' ),
						'type'       => Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
						'selectors'  => [
							'{{WRAPPER}} .sc_slider_controls.slider_pagination_style_bullets .slider_pagination_wrap > .swiper-pagination-bullet:hover,
							 {{WRAPPER}} .sc_slider_controls.slider_pagination_style_progressbar .slider_pagination_wrap .slider_progress_bar:hover,
							 {{WRAPPER}} .sc_slider_controls.slider_pagination_style_thumbs .slider_pagination_wrap .slider_pagination_button:hover,
							 {{WRAPPER}} .sc_slider_controls.slider_pagination_style_titles .slider_pagination_wrap .slider_pagination_title.slider_pagination_title:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
						'condition' => [
							'pagination_style' => ['bullets', 'progressbar', 'thumbs', 'titles']
						]
					]
				);

				$this->add_group_control(
					Group_Control_Box_Shadow::get_type(),
					[
						'name'      => 'pagination_box_shadow_hover',
						'selector'  => '{{WRAPPER}} .sc_slider_controls.slider_pagination_style_bullets .slider_pagination_wrap > .swiper-pagination-bullet:hover,
										{{WRAPPER}} .sc_slider_controls.slider_pagination_style_progressbar .slider_pagination_wrap:hover,
										{{WRAPPER}} .sc_slider_controls.slider_pagination_style_thumbs .slider_pagination_wrap .slider_pagination_button:hover,
										{{WRAPPER}} .sc_slider_controls.slider_pagination_style_titles .slider_pagination_wrap .slider_pagination_title.slider_pagination_title:hover',
						'condition' => [
							'pagination_style' => ['bullets', 'progressbar', 'thumbs', 'titles']
						]
					]
				);

				$this->end_controls_tab();

				$this->start_controls_tab(
					'tab_pagination_active',
					[
						'label' => __( 'Active', 'trx_addons' ),
					]
				);

				$this->add_group_control(
					Group_Control_Typography::get_type(),
					array(
						'name'     => 'pagination_typography_active',
						'selector' => '{{WRAPPER}} .sc_slider_controls.slider_pagination_style_fraction .slider_pagination_wrap .swiper-pagination-current,
									   {{WRAPPER}} .sc_slider_controls.slider_pagination_style_titles .slider_pagination_wrap .slider_pagination_title.slider_pagination_title_active',
						'condition' => [
							'pagination_style' => ['fraction', 'titles']
						]
					)
				);

				$this->add_group_control(
					Group_Control_Background::get_type(),
					[
						'name'     => 'pagination_background_active',
						'label'    => __( 'Background', 'trx_addons' ),
						'types'    => [ 'classic', 'gradient' ],
						'selector' => '{{WRAPPER}} .sc_slider_controls.slider_pagination_style_bullets .slider_pagination_wrap > .swiper-pagination-bullet-active,
									   {{WRAPPER}} .sc_slider_controls.slider_pagination_style_progressbar .slider_pagination_wrap .slider_progress_bar,
									   {{WRAPPER}} .sc_slider_controls.slider_pagination_style_thumbs .slider_pagination_wrap .slider_pagination_button_active:before,
									   {{WRAPPER}} .sc_slider_controls.slider_pagination_style_titles .slider_pagination_wrap .slider_pagination_title.slider_pagination_title_active',
						'condition' => [
							'pagination_style' => ['bullets', 'progressbar', 'thumbs', 'titles']
						]
					]
				);

				$this->add_responsive_control(
					'pagination_opacity_active',
					[
						'label' => __( 'Overlay Opacity', 'trx_addons' ),
						'type' => Controls_Manager::SLIDER,
						'range' => [
							'px' => [
								'min' => 0,
								'max' => 1,
								'step' => 0.05
							],
						],
						'size_units' => [ 'px' ],
						'selectors' => [ 
							'{{WRAPPER}} .sc_slider_controls.slider_pagination_style_thumbs .slider_pagination_wrap .slider_pagination_button_active:before' => 'opacity: {{SIZE}};',
						],
						'condition' => [
							'pagination_style' => ['thumbs']
						]
					]
				);

				$this->add_control(
					'pagination_color_active',
					[
						'label' => __( 'Color', 'trx_addons' ),
						'label_block' => false,
						'type' => Controls_Manager::COLOR,
						'default' => '',
						// 'global' => array(
						// 	'active' => false,
						// ),
						'selectors' => [
							'{{WRAPPER}} .sc_slider_controls.slider_pagination_style_fraction .slider_pagination_wrap .swiper-pagination-current,
							 {{WRAPPER}} .sc_slider_controls.slider_pagination_style_titles .slider_pagination_wrap .slider_pagination_title.slider_pagination_title_active' => 'color: {{VALUE}};',
						],
						'condition' => [
							'pagination_style' => ['fraction', 'titles']
						]
					]
				);

				$this->add_group_control(
					Group_Control_Border::get_type(),
					[
						'name'        => 'pagination_border_active',
						'label'       => __( 'Border', 'trx_addons' ),
						'selector'    => '{{WRAPPER}} .sc_slider_controls.slider_pagination_style_bullets .slider_pagination_wrap > .swiper-pagination-bullet-active,
										  {{WRAPPER}} .sc_slider_controls.slider_pagination_style_thumbs .slider_pagination_wrap .slider_pagination_button_active,
										  {{WRAPPER}} .sc_slider_controls.slider_pagination_style_titles .slider_pagination_wrap .slider_pagination_title.slider_pagination_title_active',
						'condition' => [
							'pagination_style' => ['bullets', 'thumbs', 'titles']
						]
					]
				);

				$this->add_responsive_control(
					'pagination_border_radius_active',
					[
						'label'      => __( 'Border Radius', 'trx_addons' ),
						'type'       => Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
						'selectors'  => [
							'{{WRAPPER}} .sc_slider_controls.slider_pagination_style_bullets .slider_pagination_wrap > .swiper-pagination-bullet-active,
							 {{WRAPPER}} .sc_slider_controls.slider_pagination_style_thumbs .slider_pagination_wrap .slider_pagination_button_active,
							 {{WRAPPER}} .sc_slider_controls.slider_pagination_style_titles .slider_pagination_wrap .slider_pagination_title.slider_pagination_title_active' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
						'condition' => [
							'pagination_style' => ['bullets', 'thumbs', 'titles']
						]
					]
				);

				$this->add_group_control(
					Group_Control_Box_Shadow::get_type(),
					[
						'name'      => 'pagination_box_shadow_active',
						'selector'  => '{{WRAPPER}} .sc_slider_controls.slider_pagination_style_bullets .slider_pagination_wrap > .swiper-pagination-bullet-active,
										{{WRAPPER}} .sc_slider_controls.slider_pagination_style_thumbs .slider_pagination_wrap .slider_pagination_button_active,
										{{WRAPPER}} .sc_slider_controls.slider_pagination_style_titles .slider_pagination_wrap .slider_pagination_title.slider_pagination_title_active',
						'condition' => [
							'pagination_style' => ['bullets', 'thumbs', 'titles']
						]
					]
				);

				$this->end_controls_tab();

				$this->end_controls_tabs();

				$this->end_controls_section();
			}

			/**
			 * Render widget output in the editor.
			 *
			 * Written as a Backbone JavaScript template and used to generate the live preview.
			 *
			 * @since 1.6.41
			 * @access protected
			 */
			protected function content_template() {
				trx_addons_get_template_part(TRX_ADDONS_PLUGIN_WIDGETS . "slider/tpe.slider_controls.php",
										'trx_addons_args_widget_slider_controls',
										array('element' => $this)
									);
			}
		}
		
		// Register widget
		trx_addons_elm_register_widget( 'TRX_Addons_Elementor_Widget_Slider_Controls' );
	}
}
