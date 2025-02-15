<?php
/**
 * Shortcode: HScroll (Elementor support)
 *
 * @package ThemeREX Addons
 * @since v2.5.2
 */

// Disable direct call
if ( ! defined( 'ABSPATH' ) ) { exit; }




// Elementor Widget
//------------------------------------------------------
if ( ! function_exists( 'trx_addons_sc_hscroll_add_in_elementor' ) ) {
	add_action( trx_addons_elementor_get_action_for_widgets_registration(), 'trx_addons_sc_hscroll_add_in_elementor' );
	function trx_addons_sc_hscroll_add_in_elementor() {
		
		if ( ! class_exists( 'TRX_Addons_Elementor_Widget' ) ) return;	

		class TRX_Addons_Elementor_Widget_Hscroll extends TRX_Addons_Elementor_Widget {

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
					'bg_image' => 'url',
					'speed' => 'size',
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
				return 'trx_sc_hscroll';
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
				return __( 'HScroll', 'trx_addons' );
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
				return [ 'hscroll', 'horizontal', 'scroll' ];
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
				return 'eicon-slider-push trx_addons_elementor_widget_icon';
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
			 * Widget preview refresh button.
			 *
			 * @since 2.6.0
			 * @access public
			 */
			/*
			public function is_reload_preview_required() {
				return true;
			}
			*/

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

				// If open params in Elementor Editor
				$params = $this->get_sc_params();

				// Prepare lists
				$layouts = ! $is_edit_mode ? array() : trx_addons_array_merge(	array(
														0 => trx_addons_get_not_selected_text( __( 'Not selected', 'trx_addons' ) )
														),
													trx_addons_get_list_layouts()
													);
				$templates = ! $is_edit_mode ? array() : trx_addons_array_merge(	array(
														0 => trx_addons_get_not_selected_text( __( 'Not selected', 'trx_addons' ) )
														),
													trx_addons_get_list_elementor_templates()
													);
				$layout = 0;

				// Register controls
				$this->start_controls_section(
					'section_sc_hscroll',
					[
						'label' => __( 'HScroll', 'trx_addons' ),
					]
				);

				$this->add_control(
					'type',
					[
						'label' => __( 'Layout', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => apply_filters('trx_addons_sc_type', trx_addons_components_get_allowed_layouts('sc', 'hscroll'), 'trx_sc_hscroll'),
						'default' => 'default'
					]
				);

				$this->add_control(
					'warning',
					[
						'raw' => __( '<p>Important:</p><ul><li>Use this widget only on Fullscreen page.</li><li>Please make sure that "Stretch Section" option is disabled for sections below.</li></ul>', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::RAW_HTML,
						'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
					]
				);

				$this->add_control(
					'slides',
					[
						'label' => '',
						'type' => \Elementor\Controls_Manager::REPEATER,
						'default' => apply_filters('trx_addons_sc_param_group_value', [
							[
								'type' => 'section',
								'section' => '',
								'layout' => '',
								'template' => '',
								'background' => [ 'url' => '' ]
							]
						], 'trx_sc_hscroll'),
						'fields' => apply_filters('trx_addons_sc_param_group_params', [
							[
								'name' => 'type', 
								'label' => __("Content type", 'trx_addons'),
								'label_block' => false,
								'type' => \Elementor\Controls_Manager::SELECT,
								'options' => trx_addons_get_list_content_types(),
								'default' => 'section'
							],
							[
								'name' => 'section',
								'label' => __( 'Section ID', 'trx_addons' ),
								'label_block' => false,
								'type' => \Elementor\Controls_Manager::TEXT,
								'default' => '',
								'condition' => [
									'type' => 'section'
								]
							],
							[
								'name' => 'layout', 
								'label' => __("Custom Layout", 'trx_addons'),
								'label_block' => false,
								'description' => wp_kses( __("Select any previously created layout to insert to this page", 'trx_addons')
																. '<br>'
																. sprintf('<a href="%1$s" class="trx_addons_post_editor' . (intval($layout)==0 ? ' trx_addons_hidden' : '').'" target="_blank">%2$s</a>',
																			admin_url( sprintf( "post.php?post=%d&amp;action=elementor", $layout ) ),
																			__("Open selected layout in a new tab to edit", 'trx_addons')
																		),
															'trx_addons_kses_content'
															),
								'type' => \Elementor\Controls_Manager::SELECT,
								'options' => $layouts,
								'default' => 0,
								'condition' => [
									'type' => 'layout'
								]
							],
							[
								'name' => 'template', 
								'label' => __("Elementor's Template", 'trx_addons'),
								'label_block' => false,
								'description' => wp_kses( __("Select any previously created template to insert to this page", 'trx_addons')
																. '<br>'
																. sprintf('<a href="%1$s" class="trx_addons_post_editor' . (intval($layout)==0 ? ' trx_addons_hidden' : '').'" target="_blank">%2$s</a>',
																			admin_url( sprintf( "post.php?post=%d&amp;action=elementor", $layout ) ),
																			__("Open selected template in a new tab to edit", 'trx_addons')
																		),
															'trx_addons_kses_content'
															),
								'type' => \Elementor\Controls_Manager::SELECT,
								'options' => $templates,
								'default' => 0,
								'condition' => [
									'type' => 'template'
								]
							],
							[
								'name' => 'bg_image',
								'label' => __( 'Background image', 'trx_addons' ),
								'type' => \Elementor\Controls_Manager::MEDIA,
								'default' => [
									'url' => '',
								]
							],
						], 'trx_sc_hscroll' ),
						'title_field'        => '{{{ type === "section" ? "Section: " + section : ( type === "layout" ? "Layout ID: " + layout : "Template ID: " + template ) }}}',
//						'prevent_empty'      => false,
//						'frontend_available' => true
					]
				);

				$this->add_control(
					'disable_on_mobile',
					[
						'label' => __( 'Disable on mobile', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SWITCHER,
						'label_off' => __( 'No', 'trx_addons' ),
						'label_on' => __( 'Yes', 'trx_addons' ),
						'return_value' => '1',
						'default' => '1',
					]
				);

				$this->add_control(
					'reverse',
					[
						'label' => __( 'Reverse', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SWITCHER,
						'label_off' => __( 'Off', 'trx_addons' ),
						'label_on' => __( 'On', 'trx_addons' ),
						'return_value' => '1',
					]
				);

				$this->add_control(
					'speed',
					[
						'label' => __( 'Speed', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::SLIDER,
						'default' => [
							'size' => 10,
							'unit' => 'px'
						],
						'size_units' => [ 'px' ],
						'range' => [
							'px' => [
								'min' => 1,
								'max' => 20,
								'step' => 1
							]
						]
					]
				);

				$this->add_control(
					'bg_color',
					[
						'label' => __( 'Background Color', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::COLOR,
						'default' => '',
//						'global' => array(
//							'active' => false,
//						),
					]
				);

				$this->add_control(
					'bg_image',
					[
						'label' => __( 'Background Image', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::MEDIA,
						'default' => [
							'url' => '',
						]
					]
				);

				$this->add_control(
					'bullets',
					[
						'label' => __( 'Bullets', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SWITCHER,
						'label_off' => __( 'Off', 'trx_addons' ),
						'label_on' => __( 'On', 'trx_addons' ),
						'return_value' => '1',
					]
				);

				$this->add_control(
					'bullets_position',
					[
						'label' => __( 'Position', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => trx_addons_get_list_sc_hscroll_bullets_positions(),
						'default' => 'left',
						'condition' => [
							'bullets' => '1'
						]
					]
				);

				$this->add_control(
					'numbers',
					[
						'label' => __( 'Page numbers', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SWITCHER,
						'label_off' => __( 'Off', 'trx_addons' ),
						'label_on' => __( 'On', 'trx_addons' ),
						'return_value' => '1',
					]
				);

				$this->add_control(
					'numbers_position',
					[
						'label' => __( 'Position', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => trx_addons_get_list_sc_hscroll_numbers_positions(),
						'default' => 'center',
						'condition' => [
							'numbers' => '1'
						]
					]
				);

				$this->add_control(
					'progress',
					[
						'label' => __( 'Progress Bar', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SWITCHER,
						'label_off' => __( 'Off', 'trx_addons' ),
						'label_on' => __( 'On', 'trx_addons' ),
						'return_value' => '1',
					]
				);

				$this->add_control(
					'progress_position',
					[
						'label' => __( 'Position', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => trx_addons_get_list_sc_hscroll_progress_positions(),
						'default' => 'left',
						'condition' => [
							'progress' => '1'
						]
					]
				);

				$this->end_controls_section();
			}

		}
		
		// Register widget
		trx_addons_elm_register_widget( 'TRX_Addons_Elementor_Widget_Hscroll' );
	}
}

if ( ! function_exists( 'trx_addons_sc_hscroll_disable_cache_if_catch_output' ) ) {
	add_filter( 'elementor/frontend/builder_content_data', 'trx_addons_sc_hscroll_disable_cache_if_catch_output', 10, 2 );
	/**
	 * Disable Elementor's cache if the hscroll is present on the current page and any slide content contains a section
	 * 
	 * @hooked elementor/frontend/builder_content_data
	 * 
	 * @param array $data       The content data.
	 * @param int   $post_id    The post ID.
	 * @param bool  $recursive  The flag about recursive call.
	 * 
	 * @return array  The content data.
	 */
	function trx_addons_sc_hscroll_disable_cache_if_catch_output( $data, $post_id, $recursive = false ) {
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
					trx_addons_sc_hscroll_disable_cache_if_catch_output( $data[ $i ]['elements'], $post_id, true );
				} else if ( ! empty( $data[ $i ]['widgetType'] ) && $data[ $i ]['widgetType'] == 'trx_sc_hscroll' ) {
					if ( is_array( $data[ $i ]['settings']['slides'] ) ) {
						for ( $t = 0; $t < count( $data[ $i ]['settings']['slides'] ); $t++ ) {
							if ( ! empty( $data[ $i ]['settings']['slides'][ $t ]['type'] ) && $data[ $i ]['settings']['slides'][ $t ]['type'] == 'section' ) {
								$need_clear_cache = true;
								break;
							}
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
}

if ( ! function_exists( 'trx_addons_sc_hscroll_enable_cache_after_catch_output' ) ) {
	add_action( 'elementor/frontend/get_builder_content', 'trx_addons_sc_hscroll_enable_cache_after_catch_output', 10, 3 );
	/**
	 * Enable Elementor's cache after the page content is rendered
	 * 
	 * @hooked elementor/frontend/get_builder_content
	 * 
	 * @param Elementor\Document $document   The document.
	 * @param bool               $is_excerpt The flag about excerpt.
	 * @param bool               $with_css   The flag about CSS.
	 */
	function trx_addons_sc_hscroll_enable_cache_after_catch_output( $document, $is_excerpt, $with_css ) {
		global $TRX_ADDONS_STORAGE;
		$post_id = $document->get_main_id();
		if ( ! empty( $TRX_ADDONS_STORAGE['should_render_shortcode'][ $post_id ] ) ) {
			remove_filter( 'elementor/element/should_render_shortcode', '__return_false', 100 );
			unset( $TRX_ADDONS_STORAGE['should_render_shortcode'][ $post_id ] );
		}
	}
}

// Start buffering output of the sections with specified id
if ( ! function_exists( 'trx_addons_sc_hscroll_start_catch_output' ) ) {
	// Before Elementor 2.1.0
	add_action( 'elementor/frontend/element/before_render',  'trx_addons_sc_hscroll_start_catch_output', 10, 1 );
	// After Elementor 2.1.0
	add_action( 'elementor/frontend/section/before_render', 'trx_addons_sc_hscroll_start_catch_output', 10, 1 );
	// After Elementor 3.16.0
	add_action( 'elementor/frontend/container/before_render', 'trx_addons_sc_hscroll_start_catch_output', 10, 1 );
	function trx_addons_sc_hscroll_start_catch_output( $element ) {
		global $TRX_ADDONS_STORAGE;
		if ( ! empty( $TRX_ADDONS_STORAGE['capture_page'] ) && ! trx_addons_is_preview( 'elementor' ) ) {
			if ( is_object( $element ) && in_array( $element->get_name(), array( 'section', 'container' ) ) ) {
				$id = $element->get_settings( '_element_id' );
				if ( ! empty( $id ) && ! empty( $TRX_ADDONS_STORAGE['catch_output']['sc_hscroll'][ $id ] ) ) {
					ob_start();
				}
			}
		}
	}
}


// End buffering output of the sections with specified id
if ( ! function_exists( 'trx_addons_sc_hscroll_end_catch_output' ) ) {
	// Before Elementor 2.1.0
	add_action( 'elementor/frontend/element/after_render',  'trx_addons_sc_hscroll_end_catch_output', 10, 1 );
	// After Elementor 2.1.0
	add_action( 'elementor/frontend/section/after_render', 'trx_addons_sc_hscroll_end_catch_output', 10, 1 );
	// After Elementor 3.16.0
	add_action( 'elementor/frontend/container/after_render', 'trx_addons_sc_hscroll_end_catch_output', 10, 1 );
	function trx_addons_sc_hscroll_end_catch_output( $element ) {
		global $TRX_ADDONS_STORAGE;
		if ( ! empty( $TRX_ADDONS_STORAGE['capture_page'] ) && ! trx_addons_is_preview( 'elementor' ) ) {
			if ( is_object( $element ) && in_array( $element->get_name(), array( 'section', 'container' ) ) ) {
				$id = $element->get_settings( '_element_id' );
				if ( ! empty( $id ) && ! empty( $TRX_ADDONS_STORAGE['catch_output']['sc_hscroll'][ $id ] ) ) {
					$TRX_ADDONS_STORAGE['catch_output']['sc_hscroll'][ $id ] = ob_get_contents();
					ob_end_clean();
				}
			}
		}
	}
}


// Paste buffer to the sections with specified id
if ( ! function_exists( 'trx_addons_sc_hscroll_paste_catch_output' ) ) {
	add_filter( 'trx_addons_filter_page_content', 'trx_addons_sc_hscroll_paste_catch_output', 10, 1 );
	function trx_addons_sc_hscroll_paste_catch_output( $output ) {
		global $TRX_ADDONS_STORAGE;
		if ( ! trx_addons_is_preview( 'elementor' ) ) {
			if ( ! empty( $TRX_ADDONS_STORAGE['catch_output']['sc_hscroll'] ) && is_array( $TRX_ADDONS_STORAGE['catch_output']['sc_hscroll'] ) ) {
				foreach( $TRX_ADDONS_STORAGE['catch_output']['sc_hscroll'] as $id => $html ) {
					$output = preg_replace(
						'/(<div[^>]*class="sc_hscroll_section[^>]*data-section="' . esc_attr( $id ) . '"[^>]*>)[\s]*<\/div>/',
						'${1}' . $html . '</div>',
						$output );
				}
			}
		}
		return $output;
	}
}
