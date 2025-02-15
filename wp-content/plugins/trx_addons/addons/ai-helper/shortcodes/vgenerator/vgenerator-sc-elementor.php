<?php
/**
 * Shortcode: VGenerator (Elementor support)
 *
 * @package ThemeREX Addons
 * @since v2.20.2
 */

// Disable direct call
if ( ! defined( 'ABSPATH' ) ) { exit; }

use TrxAddons\AiHelper\Lists;
use TrxAddons\AiHelper\Utils;

use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Modules\DynamicTags\Module as TagsModule;

// Elementor Widget
//------------------------------------------------------
if ( ! function_exists('trx_addons_sc_vgenerator_add_in_elementor')) {
	add_action( trx_addons_elementor_get_action_for_widgets_registration(), 'trx_addons_sc_vgenerator_add_in_elementor' );
	function trx_addons_sc_vgenerator_add_in_elementor() {

		if ( ! class_exists( 'TRX_Addons_Elementor_Widget' ) ) return;	

		class TRX_Addons_Elementor_Widget_VGenerator extends TRX_Addons_Elementor_Widget {

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
					'prompt_width' => 'size',
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
				return 'trx_sc_vgenerator';
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
				return __( 'AI Helper Video Generator', 'trx_addons' );
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
				return [ 'ai', 'helper', 'generator', 'vgenerator', 'video' ];
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
				return 'eicon-youtube trx_addons_elementor_widget_icon';
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

				$this->register_content_controls_video_generator();
				$this->register_content_controls_generator_settings();
				$this->register_content_controls_demo_video();

				if ( apply_filters( 'trx_addons_filter_add_title_param', true, $this->get_name() ) ) {
					$this->add_title_param();
				}
			}

			/*-----------------------------------------------------------------------------------*/
			/*	CONTENT TAB
			/*-----------------------------------------------------------------------------------*/

			/**
			 * register_content_controls_video_generator
			 *
			 * @return void
			 */
			protected function register_content_controls_video_generator() {

				$this->start_controls_section(
					'section_sc_vgenerator',
					[
						'label' => __( 'AI Helper Video Generator', 'trx_addons' ),
					]
				);

				$this->add_control(
					'type',
					[
						'label' => __( 'Layout', 'trx_addons' ),
						'label_block' => false,
						'type' => Controls_Manager::SELECT,
						'options' => apply_filters( 'trx_addons_sc_type', Lists::get_list_sc_video_generator_layouts(), $this->get_name() ),
						'default' => 'default'
					]
				);

				$this->add_control(
					'prompt',
					[
						'label' => __( 'Default prompt', 'trx_addons' ),
						'label_block' => false,
						'type' => Controls_Manager::TEXT,
						'default' => ''
					]
				);

				$this->add_control(
					'placeholder_text',
					[
						'label' => __( 'Placeholder', 'trx_addons' ),
						'label_block' => false,
						'type' => Controls_Manager::TEXT,
						'default' => ''
					]
				);

				$this->add_control(
					'button_text',
					[
						'label' => __( 'Button text', 'trx_addons' ),
						'label_block' => false,
						'type' => Controls_Manager::TEXT,
						'default' => ''
					]
				);

				$this->add_control(
					'show_prompt_translated',
					[
						'label' => __( 'Show "Prompt translated"', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SWITCHER,
						'default' => '1',
						'return_value' => '1',
					]
				);

				$this->add_responsive_control(
					'prompt_width',
					[
						'label' => __( 'Prompt field width', 'trx_addons' ),
						'type' => Controls_Manager::SLIDER,
						'default' => [
							'size' => 100,
							'unit' => 'px'
						],
						'size_units' => [ 'px' ],
						'range' => [
							'px' => [
								'min' => 50,
								'max' => 100
							]
						],
						'selectors' => [
							'{{WRAPPER}} .sc_vgenerator_form_inner' => 'width: {{SIZE}}%;',
							'{{WRAPPER}} .sc_vgenerator_message' => 'max-width: {{SIZE}}%;',
							'{{WRAPPER}} .sc_vgenerator_limits' => 'max-width: {{SIZE}}%;',
						],
						'condition' => [
							'type' => 'default'
						]
					]
				);

				$this->add_responsive_control(
					'align',
					[
						'label' => __( 'Alignment', 'elementor' ),
						'type' => Controls_Manager::CHOOSE,
						'options' => trx_addons_get_list_sc_flex_aligns_for_elementor(),
						'default' => '',
						'render_type' => 'template',
						'selectors' => [
							'{{WRAPPER}} .sc_vgenerator_form' => 'align-items: {{VALUE}};',
							'{{WRAPPER}} .sc_vgenerator_form_inner' => 'align-items: {{VALUE}};',
						],
						'condition' => [
							'type' => 'default'
						]
					]
				);

				$this->add_control(
					'tags_label',
					[
						'label' => __( 'Tags label', 'trx_addons' ),
						'label_block' => false,
						'type' => Controls_Manager::TEXT,
						'default' => __( 'Popular Tags:', 'trx_addons' )
					]
				);

				$this->add_control(
					'tags',
					[
						'label' => __( 'Tags', 'trx_addons' ),
						'label_block' => true,
						'type' => Controls_Manager::REPEATER,
						'default' => apply_filters( 'trx_addons_sc_param_group_value', [
							[
								'title' => __( 'Cats', 'trx_addons' ),
								'prompt' => __( 'сats playing with a ball', 'trx_addons' ),
							],
							[
								'title' => __( 'Basketball', 'trx_addons' ),
								'prompt' => __( 'boys playing basketball', 'trx_addons' ),
							],
							[
								'title' => __( 'Storm', 'trx_addons' ),
								'prompt' => __( 'storm at sea', 'trx_addons' ),
							],
						], $this->get_name() ),
						'fields' => apply_filters( 'trx_addons_sc_param_group_params', [
							[
								'name' => 'title',
								'label' => __( 'Title', 'trx_addons' ),
								'label_block' => false,
								'type' => Controls_Manager::TEXT,
								'placeholder' => __( "Tag's title", 'trx_addons' ),
								'default' => ''
							],
							[
								'name' => 'prompt',
								'label' => __( 'Prompt', 'trx_addons' ),
								'label_block' => false,
								'type' => Controls_Manager::TEXT,
								'placeholder' => __( "Prompt", 'trx_addons' ),
								'default' => ''
							],
						], $this->get_name() ),
						'title_field' => '{{{ title }}}'
					]
				);

				$this->end_controls_section();
			}

			/**
			 * register_content_controls_generator_settings
			 *
			 * @return void
			 */
			protected function register_content_controls_generator_settings() {

				// Detect edit mode
				$is_edit_mode = trx_addons_elm_is_edit_mode();

				$this->start_controls_section(
					'section_sc_vgenerator_settings',
					[
						'label' => __( 'Generator Settings', 'trx_addons' ),
					]
				);

				$this->add_control(
					'premium',
					[
						'label' => __( 'Premium Mode', 'trx_addons' ),
						'label_block' => false,
						'description' => __( 'Enables you to set a broader range of limits for video generation, which can be used for a paid video generation service. The limits are configured in the global settings.', 'trx_addons' ),
						'type' => Controls_Manager::SWITCHER,
						'return_value' => '1',
					]
				);

				$this->add_control(
					'show_limits',
					[
						'label' => __( 'Show limits', 'trx_addons' ),
						'label_block' => false,
						'type' => Controls_Manager::SWITCHER,
						'return_value' => '1',
					]
				);

				$this->add_control(
					'model',
					[
						'label' => __( 'Default model', 'trx_addons' ),
						'label_block' => false,
						'separator' => 'before',
						'type' => Controls_Manager::SELECT,
						'options' => ! $is_edit_mode ? array() : Lists::get_list_ai_video_models( false ),
						'default' => Utils::get_default_video_model()
					]
				);

				$this->add_control(
					'system_prompt',
					[
						'label' => __( 'System prompt (Context)', 'trx_addons' ),
						'label_block' => true,
						'description' => __( 'These are instructions for the AI Model describing how it should generate text. If you leave this field empty - the System Prompt specified in the plugin options will be used.', 'trx_addons' ),
						'type' => Controls_Manager::TEXTAREA,
						'rows' => 5,
						'default' => ''
					]
				);

				$this->add_control(
					'show_settings',
					[
						'label' => __( 'Show button "Settings"', 'trx_addons' ),
						'label_block' => false,
						'type' => Controls_Manager::SWITCHER,
						'return_value' => '1'
					]
				);

				$this->add_control(
					'show_download',
					[
						'label' => __( 'Show button "Download"', 'trx_addons' ),
						'label_block' => false,
						'type' => Controls_Manager::SWITCHER,
						'return_value' => '1',
					]
				);

				$this->add_control(
					'allow_loop',
					[
						'label' => __( 'Allow loop', 'trx_addons' ),
						'label_block' => false,
						'description' => __( 'Whether to loop the video.', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::SWITCHER,
						'default' => '',
						'return_value' => '1',
						'condition' => [
							'type' => 'default'
						]
					]
				);

				$this->add_control(
					'show_upload_frame0',
					[
						'label' => __( 'Allow upload start keyframe ', 'trx_addons' ),
						'label_block' => false,
						'description' => wp_kses_data( __( "Allow users to upload their own start keyframe for generation variations. The keyframe will be temporary uploaded to the server and will be available for generation only for the current user.", 'trx_addons' ) ),
						'type' => \Elementor\Controls_Manager::SWITCHER,
						'default' => '',
						'return_value' => '1',
						'condition' => [
							'type' => 'default',
							'model' => Lists::get_list_models_for_access_ai_video_keyframes(),
							'allow_loop' => '',
						]
					]
				);

				// $this->add_control(
				// 	'keyframes_frame0',
				// 	[
				// 		'label' => __( 'Choose start keyframe', 'trx_addons' ),
				// 		'type' => Controls_Manager::MEDIA,
				// 		'condition' => [
				// 			'show_upload_frame0' => '',
				// 			'type' => 'default',
				// 			'model' => Lists::get_list_models_for_access_ai_video_keyframes(),
				// 			'allow_loop' => '',
				// 		]
				// 	]
				// );

				$this->add_control(
					'show_upload_frame1',
					[
						'label' => __( 'Allow upload end keyframe', 'trx_addons' ),
						'label_block' => false,
						'description' => wp_kses_data( __( "Allow users to upload their own end keyframe for generation variations. The keyframe will be temporary uploaded to the server and will be available for generation only for the current user.", 'trx_addons' ) ),
						'type' => \Elementor\Controls_Manager::SWITCHER,
						'default' => '',
						'return_value' => '1',
						'condition' => [
							'type' => 'default',
							'model' => Lists::get_list_models_for_access_ai_video_keyframes(),
							'allow_loop' => '',
						]
					]
				);

				// $this->add_control(
				// 	'keyframes_frame1',
				// 	[
				// 		'label' => __( 'Choose end keyframe', 'trx_addons' ),
				// 		'type' => Controls_Manager::MEDIA,
				// 		'condition' => [
				// 			'show_upload_frame1' => '',
				// 			'type' => 'default',
				// 			'model' => Lists::get_list_models_for_access_ai_video_keyframes(),
				// 			'allow_loop' => '',
				// 		]
				// 	]
				// );

				$this->add_control(
					'aspect_ratio',
					[
						'label' => __( 'Aspect Ratio', 'trx_addons' ),
						'label_block' => false,
						'description' => wp_kses_data( __( "Select the aspect ratio of generated video.", 'trx_addons' ) ),
						'type' => Controls_Manager::SELECT,
						'options' => Lists::get_list_ai_video_ar(),
						'default' => '1:1',
					]
				);

				$this->add_control(
					'resolution',
					[
						'label' => __( 'Resolution', 'trx_addons' ),
						'label_block' => false,
						'description' => wp_kses_data( __( "Select the resolution of generated video.", 'trx_addons' ) ),
						'type' => Controls_Manager::SELECT,
						'options' => Lists::get_list_ai_video_resolutions(),
						'default' => '540p',
						'condition' => [
							'model' => Lists::get_list_models_for_access_ai_video_resolution(),
						]
					]
				);

				$this->add_control(
					'duration',
					[
						'label' => __( 'Duration', 'trx_addons' ),
						'label_block' => false,
						'description' => wp_kses_data( __( "Select the duration of the generated video.", 'trx_addons' ) ),
						'type' => Controls_Manager::SELECT,
						'options' => Lists::get_list_ai_video_durations(),
						'default' => '5s',
						'condition' => [
							'model' => Lists::get_list_models_for_access_ai_video_duration(),
						]
					]
				);

				$this->end_controls_section();
			}

			/**
			 * register_content_controls_demo_video
			 *
			 * @return void
			 */
			protected function register_content_controls_demo_video() {

				$this->start_controls_section(
					'section_sc_vgenerator_demo',
					[
						'label' => __( 'Demo Video', 'trx_addons' ),
					]
				);

				$repeater = new Repeater();
		
				$repeater->add_control(
					'video',
					[
						'label' => __( 'Video', 'trx_addons' ),
						'description' => wp_kses_data( __("Selected files will be used instead of the video generator as a demo mode when limits are reached", 'trx_addons') ),
						'type' => Controls_Manager::MEDIA,
						'dynamic' => [
							'active' => true,
							'categories' => [
								TagsModule::MEDIA_CATEGORY,
							],
						],
						'media_types' => [
							'video',
						],
						'default' => [],
					]
				);

				$this->add_control(
					'demo_video',
					[
						'type'        => Controls_Manager::REPEATER,
						'fields'      => $repeater->get_controls(),
						'title_field' => '{{{trx_addons_get_file_name(video.url,false)}}}',
					]
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
				if ( ! Utils::is_video_api_available() ) {
					trx_addons_get_template_part( 'templates/tpe.sc_placeholder.php',
						'trx_addons_args_sc_placeholder',
						apply_filters( 'trx_addons_filter_sc_placeholder_args', array(
							'sc' => 'trx_sc_vgenerator',
							'title' => __( 'AI Video Generator is not available - token for access to the API for video generation is not specified', 'trx_addons' ),
							'class' => 'sc_placeholder_with_title'
						) )
					);
				} else {
					trx_addons_get_template_part( TRX_ADDONS_PLUGIN_ADDONS . 'ai-helper/shortcodes/vgenerator/tpe.vgenerator.php',
						'trx_addons_args_sc_vgenerator',
						array( 'element' => $this )
					);
				}
			}
		}

		// Register widget
		trx_addons_elm_register_widget( 'TRX_Addons_Elementor_Widget_VGenerator' );
	}
}
