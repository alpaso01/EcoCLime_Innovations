<?php
/**
 * Shortcode: AGenerator (Elementor support)
 *
 * @package ThemeREX Addons
 * @since v2.31.0
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
if ( ! function_exists('trx_addons_sc_agenerator_add_in_elementor')) {
	add_action( trx_addons_elementor_get_action_for_widgets_registration(), 'trx_addons_sc_agenerator_add_in_elementor' );
	function trx_addons_sc_agenerator_add_in_elementor() {
		
		if ( ! class_exists( 'TRX_Addons_Elementor_Widget' ) ) return;	

		class TRX_Addons_Elementor_Widget_AGenerator extends TRX_Addons_Elementor_Widget {

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
			public function __construct( $data = array(), $args = null ) {
				parent::__construct( $data, $args );
				$this->add_plain_params( array(
					'prompt_width' => 'size',
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
				return 'trx_sc_agenerator';
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
				return __( 'AI Helper Audio Generator', 'trx_addons' );
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
				return [ 'ai', 'helper', 'generator', 'agenerator', 'audio', 'voice', 'speech' ];
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
				return 'eicon-play trx_addons_elementor_widget_icon';
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

				// Detect edit mode
				$is_edit_mode = trx_addons_elm_is_edit_mode();

				// Register controls
				$this->start_controls_section(
					'section_sc_agenerator',
					[
						'label' => __( 'AI Helper Audio Generator', 'trx_addons' ),
					]
				);

				$this->add_control(
					'type',
					[
						'label' => __( 'Layout', 'trx_addons' ),
						'label_block' => false,
						'type' => Controls_Manager::SELECT,
						'options' => apply_filters('trx_addons_sc_type', Lists::get_list_sc_audio_generator_layouts(), 'trx_sc_agenerator'),
						'default' => 'default'
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

				$this->end_controls_section();

				// Section: Generator settings
				$this->start_controls_section(
					'section_sc_agenerator_settings',
					[
						'label' => __( 'Generator Settings', 'trx_addons' ),
					]
				);

				$this->add_control(
					'premium',
					[
						'label' => __( 'Premium Mode', 'trx_addons' ),
						'label_block' => false,
						'description' => __( 'Enables you to set a broader range of limits for image generation, which can be used for a paid image generation service. The limits are configured in the global settings.', 'trx_addons' ),
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
					'base64',
					[
						'label' => __( 'Use Base64', 'trx_addons' ),
						'label_block' => false,
						'description' => __( "Pass the original audio/voice to the generation server inside a query (using Base64 encoding) or via a temporary URL (the file will be cached on your server for some time, not suitable for local installations inaccessible from the Internet)", 'trx_addons' ),
						'type' => Controls_Manager::SWITCHER,
						'default' => '',
						'return_value' => '1',
					]
				);

				$this->end_controls_section();

				// Section: Demo audio
				$this->start_controls_section(
					'section_sc_mgenerator_demo',
					[
						'label' => __( 'Demo Audio', 'trx_addons' ),
					]
				);

				$repeater = new Repeater();
		
				$repeater->add_control(
					'audio',
					[
						'label' => __( 'Audio', 'trx_addons' ),
						'description' => wp_kses_data( __("Selected files will be used instead of the audio generator as a demo mode when limits are reached", 'trx_addons') ),
						'type' => Controls_Manager::MEDIA,
						'dynamic' => [
							'active' => true,
							'categories' => [
								TagsModule::MEDIA_CATEGORY,
							],
						],
						'media_types' => [
							'audio',
						],
						'default' => [],
					]
				);

				$this->add_control(
					'demo_audio',
					[
						'type'        => Controls_Manager::REPEATER,
						'fields'      => $repeater->get_controls(),
						'title_field' => '{{{trx_addons_get_file_name(audio.url,false)}}}',
					]
				);
		
				$this->end_controls_section();

				if ( apply_filters( 'trx_addons_filter_add_title_param', true, $this->get_name() ) ) {
					$this->add_title_param();
				}
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
				if ( ! Utils::is_audio_api_available() ) {
					trx_addons_get_template_part( 'templates/tpe.sc_placeholder.php',
						'trx_addons_args_sc_placeholder',
						apply_filters( 'trx_addons_filter_sc_placeholder_args', array(
							'sc' => 'trx_sc_agenerator',
							'title' => __('AI Audio Generator is not available - token for access to the API for audio generation is not specified', 'trx_addons'),
							'class' => 'sc_placeholder_with_title'
						) )
					);
				} else {
					trx_addons_get_template_part(TRX_ADDONS_PLUGIN_ADDONS . 'ai-helper/shortcodes/agenerator/tpe.agenerator.php',
						'trx_addons_args_sc_agenerator',
						array('element' => $this)
					);
				}
			}
		}
		
		// Register widget
		trx_addons_elm_register_widget( 'TRX_Addons_Elementor_Widget_AGenerator' );
	}
}
