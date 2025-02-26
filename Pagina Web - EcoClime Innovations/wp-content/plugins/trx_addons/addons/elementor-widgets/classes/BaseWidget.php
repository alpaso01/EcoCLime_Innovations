<?php
/**
 * Base Widget for Elementor
 *
 * @package ThemeREX Addons
 * @since v2.30.0
 */

namespace TrxAddons\ElementorWidgets;

use TrxAddons\ElementorWidgets\ElementorWidgets;

// Elementor Classes
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Icons_Manager;
use Elementor\Group_Control_Border;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Common Widget
 */
abstract class BaseWidget extends Widget_Base {

	protected $module_class = '';
	protected $widget_class = '';

	/**
	 * Widget base constructor.
	 *
	 * Initializing the widget base class.
	 *
	 * @param array       $data  Widget data. Default is an empty array.
	 * @param array|null  $args  Optional. Widget default arguments. Default is null.
	 */
	public function __construct( $data = [], $args = null ) {
		// Get the module and widget class names before calling the parent constructor to use them in the widget settings
		$class = explode( '\\', get_class( $this ) );
		$this->widget_class = end( $class );
		$this->module_class = str_replace( 'Widget', '', $this->widget_class );

		parent::__construct( $data, $args );
	}

	/**
	 * Get categories
	 */
	public function get_categories() {
		return ['trx_addons-elements'];
	}

	/**
	 * Get widget name
	 * 
	 * @return string  Widget name.
	 */
	public function get_name() {
		return ElementorWidgets::instance()->widget_data( $this->module_class, 'name' );
	}

	/**
	 * Get widget title
	 * 
	 * @return string  Widget title.
	 */
	public function get_title() {
		return ElementorWidgets::instance()->widget_data( $this->module_class, 'title' );
	}

	/**
	 * Get widget icon
	 * 
	 * @return string  Widget icon.
	 */
	public function get_icon() {
		return ElementorWidgets::instance()->widget_data( $this->module_class, 'icon' );
	}

	/**
	 * Get widget keywords
	 *
	 * @param string $widget Module class.
	 */
	public function get_keywords() {
		return ElementorWidgets::instance()->widget_data( $this->module_class, 'keywords' );
	}

	/**
	 * Register Help Docs Controls in Content tab.
	 * 
	 * This method has a public access to allow use it in the skin classes.
	 * 
	 * @access public
	 */
	public function register_content_help_docs_controls() {

		$help_docs = ElementorWidgets::instance()->widget_data( $this->module_class, 'help_docs' );

		if ( ! empty( $help_docs ) && is_array( $help_docs ) ) {

			/**
			 * Content Tab: Help Docs
			 */
			$this->start_controls_section(
				'section_help_docs',
				array(
					'label' => __( 'Help Docs', 'trx_addons' ),
				)
			);

			$hd_counter = 1;
			foreach ( $help_docs as $hd_title => $hd_link ) {
				$this->add_control(
					'help_doc_' . $hd_counter,
					array(
						'type'            => Controls_Manager::RAW_HTML,
						'raw'             => sprintf( '%1$s ' . $hd_title . ' %2$s', '<a href="' . $hd_link . '" target="_blank" rel="noopener">', '</a>' ),
						'content_classes' => 'trx-addons-editor-doc-links',
					)
				);

				$hd_counter++;
			}

			$this->end_controls_section();
		}
	}

	/**
	 * Add a placeholder for the widget in the elementor editor
	 */
	public function render_editor_placeholder( $args = array() ) {

		if ( ! \Elementor\Plugin::instance()->editor->is_edit_mode() ) {
			return;
		}

		$defaults = [
			'title' => $this->get_title(),
			'body'  => __( 'This is a placeholder for this widget and is visible only in the editor.', 'trx_addons' ),
		];

		$args = wp_parse_args( $args, $defaults );

		$this->add_render_attribute( array(
			'wrapper' => [
				'class' => 'trx_addons_elementor_editor_placeholder',
			],
			'title' => [
				'class' => 'trx_addons_elementor_editor_placeholder_title',
			],
			'content' => [
				'class' => 'trx_addons_elementor_editor_placeholder_content',
			],
		) );

		?><div <?php echo wp_kses_post( $this->get_render_attribute_string( 'wrapper' ) ); ?>>
			<h4 <?php echo wp_kses_post( $this->get_render_attribute_string( 'title' ) ); ?>>
				<?php echo esc_html( $args['title'] ); ?>
			</h4>
			<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'content' ) ); ?>>
				<?php echo esc_html( $args['body'] ); ?>
			</div>
		</div><?php
	}



	/*-----------------------------------------------------------------------------------*/
	/*	SLIDER CONTROLS AND STYLES
	/*-----------------------------------------------------------------------------------*/

	/**
	 * Register slider controls
	 */
	public function register_content_slider_controls() {

		$this->start_controls_section(
			'section_slider_options',
			array(
				'label'     => __( 'Carousel Options', 'trx_addons' ),
				'tab'       => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'slider',
			array(
				'label'              => __( 'Carousel', 'premium-addons-for-elementor' ),
				'label_block'        => false,
				'type'               => Controls_Manager::SWITCHER,
				'default'               => '',
				'return_value'          => 'yes',
				'frontend_available' => true,
			)
		);

		$slides_per_view = range( 1, 10 );
		$slides_per_view = array_combine( $slides_per_view, $slides_per_view );

		$this->add_responsive_control(
			'slides_to_scroll',
			array(
				'type'               => Controls_Manager::SELECT,
				'label'              => __( 'Slides to Scroll', 'trx_addons' ),
				'description'        => __( 'Set how many slides are scrolled per swipe.', 'trx_addons' ),
				'options'            => $slides_per_view,
				'default'            => '1',
				'tablet_default'     => '1',
				'mobile_default'     => '1',
				'condition'          => array(
					'slider' => 'yes',
				),
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'animation_speed',
			array(
				'label'              => __( 'Animation Speed', 'trx_addons' ),
				'type'               => Controls_Manager::NUMBER,
				'default'            => 600,
				'frontend_available' => true,
				'condition'          => array(
					'slider' => 'yes',
				),
			)
		);

		$this->add_control(
			'arrows',
			array(
				'label'              => __( 'Arrows', 'trx_addons' ),
				'type'               => Controls_Manager::SWITCHER,
				'default'            => 'yes',
				'label_on'           => __( 'Yes', 'trx_addons' ),
				'label_off'          => __( 'No', 'trx_addons' ),
				'return_value'       => 'yes',
				'frontend_available' => true,
				'condition'          => array(
					'slider' => 'yes',
				),
			)
		);

		$this->add_control(
			'dots',
			array(
				'label'              => __( 'Dots', 'trx_addons' ),
				'type'               => Controls_Manager::SWITCHER,
				'default'            => '',
				'label_on'           => __( 'Yes', 'trx_addons' ),
				'label_off'          => __( 'No', 'trx_addons' ),
				'return_value'       => 'yes',
				'frontend_available' => true,
				'condition'          => array(
					'slider' => 'yes',
				),
			)
		);

		$this->add_control(
			'autoplay',
			array(
				'label'              => __( 'Autoplay', 'trx_addons' ),
				'type'               => Controls_Manager::SWITCHER,
				'default'            => 'yes',
				'label_on'           => __( 'Yes', 'trx_addons' ),
				'label_off'          => __( 'No', 'trx_addons' ),
				'return_value'       => 'yes',
				'frontend_available' => true,
				'condition'          => array(
					'slider' => 'yes',
				),
			)
		);

		$this->add_control(
			'autoplay_speed',
			array(
				'label'              => __( 'Autoplay Speed', 'trx_addons' ),
				'type'               => Controls_Manager::NUMBER,
				'default'            => 6000,
				'frontend_available' => true,
				'condition'          => array(
					'slider' => 'yes',
					'autoplay' => 'yes',
				),
			)
		);

		$this->add_control(
			'pause_on_hover',
			array(
				'label'              => __( 'Pause on Hover', 'trx_addons' ),
				'type'               => Controls_Manager::SWITCHER,
				'default'            => 'yes',
				'label_on'           => __( 'Yes', 'trx_addons' ),
				'label_off'          => __( 'No', 'trx_addons' ),
				'return_value'       => 'yes',
				'frontend_available' => true,
				'condition'          => array(
					'slider' => 'yes',
					'autoplay' => 'yes',
				),
			)
		);

		$this->add_control(
			'infinite_loop',
			array(
				'label'              => __( 'Infinite Loop', 'trx_addons' ),
				'type'               => Controls_Manager::SWITCHER,
				'default'            => 'yes',
				'label_on'           => __( 'Yes', 'trx_addons' ),
				'label_off'          => __( 'No', 'trx_addons' ),
				'return_value'       => 'yes',
				'frontend_available' => true,
				'condition'          => array(
					'slider' => 'yes',
				),
			)
		);

		$this->add_control(
			'adaptive_height',
			array(
				'label'              => __( 'Adaptive Height', 'trx_addons' ),
				'type'               => Controls_Manager::SWITCHER,
				'default'            => 'yes',
				'label_on'           => __( 'Yes', 'trx_addons' ),
				'label_off'          => __( 'No', 'trx_addons' ),
				'return_value'       => 'yes',
				'frontend_available' => true,
				'condition'          => array(
					'slider' => 'yes',
				),
			)
		);

		$this->add_control(
			'center_mode',
			[
				'label'                 => __( 'Center Mode', 'trx_addons' ),
				'type'                  => Controls_Manager::SWITCHER,
				'default'               => '',
				'label_on'              => __( 'Yes', 'trx_addons' ),
				'label_off'             => __( 'No', 'trx_addons' ),
				'return_value'          => 'yes',
				'frontend_available'    => true,
				'condition'          => array(
					'slider' => 'yes',
				),
			]
		);

		// $this->add_control(
		// 	'direction',
		// 	array(
		// 		'label'              => __( 'Direction', 'trx_addons' ),
		// 		'type'               => Controls_Manager::CHOOSE,
		// 		'label_block'        => false,
		// 		'toggle'             => false,
		// 		'options'            => array(
		// 			'left'  => array(
		// 				'title' => __( 'Left', 'trx_addons' ),
		// 				'icon'  => 'eicon-h-align-left',
		// 			),
		// 			'right' => array(
		// 				'title' => __( 'Right', 'trx_addons' ),
		// 				'icon'  => 'eicon-h-align-right',
		// 			),
		// 		),
		// 		'default'            => 'left',
		// 		'frontend_available' => true,
		// 		'condition'          => array(
		// 			'slider' => 'yes',
		// 		),
		// 	)
		// );

		$this->end_controls_section();
	}

	/**
	 * Register slider arrows style controls
	 */
	public function register_style_arrows_controls() {
		$this->start_controls_section(
			'section_arrows_style',
			array(
				'label'     => __( 'Arrows', 'trx_addons' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'slider' => 'yes',
					'arrows' => 'yes',
				),
			)
		);

		$this->add_control(
			'select_arrow',
			array(
				'label'                  => __( 'Choose Arrow', 'trx_addons' ),
				'type'                   => Controls_Manager::ICONS,
				'fa4compatibility'       => 'arrow',
				'label_block'            => false,
				'default'                => array(
					'value'   => 'fas fa-angle-right',
					'library' => 'fa-solid',
				),
				'skin'                   => 'inline',
				'exclude_inline_options' => 'svg',
				'recommended'            => array(
					'fa-regular' => array(
						'arrow-alt-circle-right',
						'caret-square-right',
						'hand-point-right',
					),
					'fa-solid'   => array(
						'angle-right',
						'angle-double-right',
						'chevron-right',
						'chevron-circle-right',
						'arrow-right',
						'long-arrow-alt-right',
						'caret-right',
						'caret-square-right',
						'arrow-circle-right',
						'arrow-alt-circle-right',
						'toggle-right',
						'hand-point-right',
					),
				),
				'condition'          => array(
					'slider' => 'yes',
					'arrows' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'arrows_size',
			array(
				'label'      => __( 'Arrows Size', 'trx_addons' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => array( 'size' => '22' ),
				'range'      => array(
					'px' => array(
						'min'  => 15,
						'max'  => 100,
						'step' => 1,
					),
				),
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors'  => array(
					'{{WRAPPER}} .trx-addons-slider-arrow' => 'font-size: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'slider' => 'yes',
					'arrows' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'arrows_position',
			array(
				'label'      => __( 'Arrows Position', 'trx_addons' ),
				'type'       => Controls_Manager::SLIDER,
				'range'      => array(
					'px' => array(
						'min'  => -100,
						'max'  => 50,
						'step' => 1,
					),
				),
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors'  => array(
					'{{WRAPPER}} .trx-addons-arrow-next' => 'right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .trx-addons-arrow-prev' => 'left: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'slider' => 'yes',
					'arrows' => 'yes',
				),
			)
		);

		$this->add_control(
			'arrows_show_on_hover',
			array(
				'label'        => __( 'Show on hover', 'trx_addons' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => '',
				'label_on'     => __( 'Yes', 'trx_addons' ),
				'label_off'    => __( 'No', 'trx_addons' ),
				'return_value' => 'yes',
				'prefix_class' => 'trx-addons-slider-arrows-show-on-hover-',
				'render_type'  => 'template',
				'condition'    => array(
					'slider' => 'yes',
					'arrows' => 'yes',
				),
			)
		);

		$this->add_control(
			'arrows_hide_on',
			array(
				'label'        => __( 'Hide on', 'trx_addons' ),
				'label_block'  => false,
				'type'         => Controls_Manager::SELECT,
				'options'      => array(
					''       => __( 'No hide', 'trx_addons' ),
					'tablet' => __( 'Tablet', 'trx_addons' ),
					'mobile' => __( 'Mobile', 'trx_addons' ),
				),
				'default'      => '',
				'prefix_class' => 'trx-addons-slider-arrows-hide-on-',
				'render_type'  => 'template',
				'condition'    => array(
					'slider' => 'yes',
					'arrows' => 'yes',
				),
			)
		);

		$this->start_controls_tabs( 'tabs_arrows_style' );

		$this->start_controls_tab(
			'tab_arrows_normal',
			array(
				'label'     => __( 'Normal', 'trx_addons' ),
				'condition' => array(
					'slider' => 'yes',
					'arrows' => 'yes',
				),
			)
		);

		$this->add_control(
			'arrows_bg_color_normal',
			array(
				'label'     => __( 'Background Color', 'trx_addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .trx-addons-slider-arrow' => 'background-color: {{VALUE}};',
				),
				'condition' => array(
					'slider' => 'yes',
					'arrows' => 'yes',
				),
			)
		);

		$this->add_control(
			'arrows_color_normal',
			array(
				'label'     => __( 'Color', 'trx_addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .trx-addons-slider-arrow' => 'color: {{VALUE}};',
				),
				'condition' => array(
					'slider' => 'yes',
					'arrows' => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'        => 'arrows_border_normal',
				'label'       => __( 'Border', 'trx_addons' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .trx-addons-slider-arrow',
				'condition'   => array(
					'slider' => 'yes',
					'arrows' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'arrows_border_radius_normal',
			array(
				'label'      => __( 'Border Radius', 'trx_addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors'  => array(
					'{{WRAPPER}} .trx-addons-slider-arrow' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'  => array(
					'slider' => 'yes',
					'arrows' => 'yes',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_arrows_hover',
			array(
				'label'     => __( 'Hover', 'trx_addons' ),
				'condition' => array(
					'slider' => 'yes',
					'arrows' => 'yes',
				),
			)
		);

		$this->add_control(
			'arrows_bg_color_hover',
			array(
				'label'     => __( 'Background Color', 'trx_addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .trx-addons-slider-arrow:hover' => 'background-color: {{VALUE}};',
				),
				'condition' => array(
					'slider' => 'yes',
					'arrows' => 'yes',
				),
			)
		);

		$this->add_control(
			'arrows_color_hover',
			array(
				'label'     => __( 'Color', 'trx_addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .trx-addons-slider-arrow:hover' => 'color: {{VALUE}};',
				),
				'condition' => array(
					'slider' => 'yes',
					'arrows' => 'yes',
				),
			)
		);

		$this->add_control(
			'arrows_border_color_hover',
			array(
				'label'     => __( 'Border Color', 'trx_addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .trx-addons-slider-arrow:hover' => 'border-color: {{VALUE}};',
				),
				'condition' => array(
					'slider' => 'yes',
					'arrows' => 'yes',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'arrows_padding',
			array(
				'label'      => __( 'Padding', 'trx_addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors'  => array(
					'{{WRAPPER}} .trx-addons-slider-arrow' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'separator'  => 'before',
				'condition'  => array(
					'slider' => 'yes',
					'arrows' => 'yes',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Register slider dots style controls
	 */
	public function register_style_dots_controls() {
		$this->start_controls_section(
			'section_dots_style',
			array(
				'label'     => __( 'Dots', 'trx_addons' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'slider' => 'yes',
					'dots'   => 'yes',
				),
			)
		);

		$this->add_control(
			'dots_position',
			[
				'label'                 => __( 'Position', 'trx_addons' ),
				'type'                  => Controls_Manager::SELECT,
				'options'               => [
					'inside'     => __( 'Inside', 'trx_addons' ),
					'outside'    => __( 'Outside', 'trx_addons' ),
				],
				'default'               => 'outside',
				'condition' => array(
					'slider' => 'yes',
					'dots'   => 'yes',
				),
			]
		);

		$this->add_responsive_control(
			'dots_align',
			array(
				'label'                => __( 'Alignment', 'trx_addons' ),
				'type'                 => Controls_Manager::CHOOSE,
				'options'              => array(
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
				'default'              => 'center',
				'selectors'            => array(
					'{{WRAPPER}} .swiper-container-wrap .swiper-pagination' => 'text-align: {{VALUE}}',
				),
			)
		);

		$this->add_responsive_control(
			'dots_size',
			array(
				'label'      => __( 'Size', 'trx_addons' ),
				'type'       => Controls_Manager::SLIDER,
				'range'      => array(
					'px' => array(
						'min'  => 2,
						'max'  => 40,
						'step' => 1,
					),
				),
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors'  => array(
					'{{WRAPPER}} .swiper-container-wrap .swiper-pagination-bullet' => 'height: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .swiper-container-wrap .swiper-pagination-horizontal' => 'height: {{SIZE}}{{UNIT}};',
//					'{{WRAPPER}} .swiper-container-wrap-dots-outside' => 'padding-bottom: calc( 22px + {{SIZE}}{{UNIT}} );',
				),
				'condition'  => array(
					'slider' => 'yes',
					'dots'   => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'dots_spacing',
			array(
				'label'      => __( 'Spacing', 'trx_addons' ),
				'type'       => Controls_Manager::SLIDER,
				'range'      => array(
					'px' => array(
						'min'  => 1,
						'max'  => 30,
						'step' => 1,
					),
				),
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors'  => array(
					'{{WRAPPER}} .swiper-container-wrap .swiper-pagination-horizontal .swiper-pagination-bullet + .swiper-pagination-bullet' => 'margin-left: {{SIZE}}{{UNIT}}',
				),
				'condition'  => array(
					'slider' => 'yes',
					'dots'   => 'yes',
				),
			)
		);

		$this->start_controls_tabs( 'tabs_dots_style' );

		$this->start_controls_tab(
			'tab_dots_normal',
			array(
				'label'     => __( 'Normal', 'trx_addons' ),
				'condition' => array(
					'slider' => 'yes',
					'dots'   => 'yes',
				),
			)
		);

		$this->add_control(
			'dots_color_normal',
			array(
				'label'     => __( 'Color', 'trx_addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .swiper-container-wrap .swiper-pagination-bullet' => 'background: {{VALUE}};',
				),
				'condition' => array(
					'slider' => 'yes',
					'dots'   => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'        => 'dots_border_normal',
				'label'       => __( 'Border', 'trx_addons' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .swiper-container-wrap .swiper-pagination-bullet',
				'condition'   => array(
					'slider' => 'yes',
					'dots'   => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'dots_border_radius_normal',
			array(
				'label'      => __( 'Border Radius', 'trx_addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors'  => array(
					'{{WRAPPER}} .swiper-container-wrap .swiper-pagination-bullet' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'  => array(
					'slider' => 'yes',
					'dots'   => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'dots_margin',
			array(
				'label'              => __( 'Margin', 'trx_addons' ),
				'type'               => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'allowed_dimensions' => 'vertical',
				'placeholder'        => array(
					'top'    => '',
					'right'  => 'auto',
					'bottom' => '',
					'left'   => 'auto',
				),
				'selectors'          => array(
					'{{WRAPPER}} .swiper-container-wrap .swiper-pagination-bullets' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'          => array(
					'slider' => 'yes',
					'dots'   => 'yes',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_dots_active',
			array(
				'label'     => __( 'Active', 'trx_addons' ),
				'condition' => array(
					'slider' => 'yes',
					'dots'   => 'yes',
				),
			)
		);

		$this->add_control(
			'dots_color_active',
			array(
				'label'     => __( 'Color', 'trx_addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .swiper-container-wrap .swiper-pagination-bullet.swiper-pagination-bullet-active' => 'background: {{VALUE}};',
				),
				'condition' => array(
					'slider' => 'yes',
					'dots'   => 'yes',
				),
			)
		);

		$this->add_control(
			'dots_border_color_active',
			array(
				'label'     => __( 'Border Color', 'trx_addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .swiper-container-wrap .swiper-pagination-bullet.swiper-pagination-bullet-active' => 'border-color: {{VALUE}};',
				),
				'condition' => array(
					'slider' => 'yes',
					'dots'   => 'yes',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_dots_hover',
			array(
				'label'     => __( 'Hover', 'trx_addons' ),
				'condition' => array(
					'slider' => 'yes',
					'dots'   => 'yes',
				),
			)
		);

		$this->add_control(
			'dots_color_hover',
			array(
				'label'     => __( 'Color', 'trx_addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .swiper-container-wrap .swiper-pagination-bullet:hover' => 'background: {{VALUE}};',
				),
				'condition' => array(
					'slider' => 'yes',
					'dots'   => 'yes',
				),
			)
		);

		$this->add_control(
			'dots_border_color_hover',
			array(
				'label'     => __( 'Border Color', 'trx_addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .swiper-container-wrap .swiper-pagination-bullet:hover' => 'border-color: {{VALUE}};',
				),
				'condition' => array(
					'slider' => 'yes',
					'dots'   => 'yes',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * Add render attribute with a carousel settings.
	 * 
	 * @param string $wrapper  The wrapper class.
	 */
	protected function render_slider_settings( $wrapper ) {
		$settings        = $this->get_settings_for_display();
	
		$center_mode     = $settings['center_mode'];
		$autoplay        = $settings['autoplay'];
		$autoplay_speed  = $settings['autoplay_speed'];
		$arrows          = $settings['arrows'];
		$dots            = $settings['dots'];
		$animation_speed = $settings['animation_speed'];
		$infinite_loop   = $settings['infinite_loop'];
		$pause_on_hover  = $settings['pause_on_hover'];
		$adaptive_height = $settings['adaptive_height'];
		// $direction       = $settings['direction'];

		$slides_to_show          = ( $settings['columns'] !== '' ) ? absint( $settings['columns'] ) : 3;
		$slides_to_show_tablet   = ( $settings['columns_tablet'] !== '' ) ? absint( $settings['columns_tablet'] ) : 2;
		$slides_to_show_mobile   = ( $settings['columns_mobile'] !== '' ) ? absint( $settings['columns_mobile'] ) : 2;
		$slides_to_scroll        = ( $settings['slides_to_scroll'] !== '' ) ? min( $slides_to_show, absint( $settings['slides_to_scroll'] ) ) : 1;
		$slides_to_scroll_tablet = ( $settings['slides_to_scroll_tablet'] !== '' ) ? min( $slides_to_show_tablet, absint( $settings['slides_to_scroll_tablet'] ) ) : 1;
		$slides_to_scroll_mobile = ( $settings['slides_to_scroll_mobile'] !== '' ) ? min( $slides_to_show_mobile, absint( $settings['slides_to_scroll_mobile'] ) ) : 1;

		// if ( 'right' === $direction ) {
		// 	$slider_options['rtl'] = true;
		// }

		$slider_options = [
			'direction'             => 'horizontal',
			'speed'                 => ( $animation_speed ) ? absint( $animation_speed ) : 600,
			'slidesPerView'         => $slides_to_show,
			'slidesPerGroup'        => $slides_to_scroll,
			'autoHeight'            => ( 'yes' === $adaptive_height ),
			'watchSlidesVisibility' => true,
			'centeredSlides'        => ( 'yes' === $center_mode ),
			'loop'                  => ( 'yes' === $infinite_loop ),
		];

		if ( 'yes' === $autoplay ) {
			$autoplay_speed = ( $autoplay_speed ) ? $autoplay_speed : 999999;
		} else {
			$autoplay_speed = 999999;
		}

		$slider_options['autoplay'] = [
			'delay'                => $autoplay_speed,
			'pauseOnHover'         => ( 'yes' === $pause_on_hover ),
			'disableOnInteraction' => ( 'yes' === $pause_on_hover ),
		];

		if ( 'yes' === $dots ) {
			$slider_options['pagination'] = [
				'el'                 => '.swiper-pagination-' . esc_attr( $this->get_id() ),
				'clickable'          => true,
			];
		}

		if ( 'yes' === $arrows ) {
			$slider_options['navigation'] = [
				'nextEl'             => '.swiper-button-next-' . esc_attr( $this->get_id() ),
				'prevEl'             => '.swiper-button-prev-' . esc_attr( $this->get_id() ),
			];
		}

		$elementor_bp_lg = get_option( 'elementor_viewport_lg' );
		$elementor_bp_md = get_option( 'elementor_viewport_md' );
		$bp_desktop      = ! empty( $elementor_bp_lg ) ? $elementor_bp_lg : 1025;
		$bp_tablet       = ! empty( $elementor_bp_md ) ? $elementor_bp_md : 768;
		$bp_mobile       = 320;

		$items        = ( isset( $settings['items']['size'] ) && '' !== $settings['items']['size'] ) ? absint( $settings['items']['size'] ) : 3;
		$items_tablet = ( isset( $settings['items_tablet']['size'] ) && '' !== $settings['items_tablet']['size'] ) ? absint( $settings['items_tablet']['size'] ) : 2;
		$items_mobile = ( isset( $settings['items_mobile']['size'] ) && '' !== $settings['items_mobile']['size'] ) ? absint( $settings['items_mobile']['size'] ) : 1;

		$margin        = ( isset( $settings['margin']['size'] ) && '' !== $settings['margin']['size'] ) ? absint( $settings['margin']['size'] ) : 10;
		$margin_tablet = ( isset( $settings['margin_tablet']['size'] ) && '' !== $settings['margin_tablet']['size'] ) ? absint( $settings['margin_tablet']['size'] ) : 10;
		$margin_mobile = ( isset( $settings['margin_mobile']['size'] ) && '' !== $settings['margin_mobile']['size'] ) ? absint( $settings['margin_mobile']['size'] ) : 10;

		$slider_options['breakpoints'] = [
			$bp_desktop => [
				'slidesPerView' => $slides_to_show,
				'slidesPerGroup' => $slides_to_scroll,
				//'spaceBetween'  => $margin,
			],
			$bp_tablet  => [
				'slidesPerView'  => $slides_to_show_tablet,
				'slidesPerGroup' => $slides_to_scroll_tablet,
				//'spaceBetween'   => $margin_tablet,
			],
			$bp_mobile  => [
				'slidesPerView' => $slides_to_show_mobile,
				'slidesPerGroup' => $slides_to_scroll_mobile,
				//'spaceBetween'  => $margin_mobile,
			],
		];

		$this->add_render_attribute(
			$wrapper,
			array(
				'data-slider-settings' => wp_json_encode( $slider_options ),
			)
		);
	}

	/**
	 * Render carousel dots output on the frontend.
	 */
	protected function render_dots() {
		$settings = $this->get_settings_for_display();
		$dots     = $settings['dots'];
		if ( 'yes' === $dots ) {
			?>
			<!-- Add Pagination -->
			<div class="swiper-pagination swiper-pagination-<?php echo esc_attr( $this->get_id() ); ?>"></div>
			<?php
		}
	}

	/**
	 * Render carousel arrows output on the frontend.
	 */
	protected function render_arrows() {
		$settings        = $this->get_settings_for_display();

		$migration_allowed = Icons_Manager::is_migration_allowed();

		if ( ! isset( $settings[ 'arrow' ] ) && ! $migration_allowed ) {
			$settings[ 'arrow' ] = 'fa fa-angle-right';
		}

		$arrows          = $settings['arrows'];
		$arrow           = ! empty( $settings['arrow'] ) ? $settings['arrow'] : '';
		$select_arrow    = $settings['select_arrow'];

		$has_icon = ! empty( $settings[ 'arrow' ] );

		if ( ! $has_icon && ! empty( $select_arrow['value'] ) ) {
			$has_icon = true;
		}

		if ( ! empty( $settings['arrow'] ) ) {
			$this->add_render_attribute( 'arrow-icon', 'class', $settings[ 'arrow' ] );
			$this->add_render_attribute( 'arrow-icon', 'aria-hidden', 'true' );
		}

		$migrated = isset( $settings['__fa4_migrated'][ 'select_arrow' ] );
		$is_new   = ! isset( $settings[ 'arrow' ] ) && $migration_allowed;

		if ( 'yes' === $arrows ) {
			if ( $has_icon ) {
				if ( $is_new || $migrated ) {
					$next_arrow = $select_arrow;
					$prev_arrow = str_replace( 'right', 'left', $select_arrow );
				} else {
					$next_arrow = $settings['arrow'];
					$prev_arrow = str_replace( 'right', 'left', $arrow );
				}
			} else {
				$next_arrow = 'fa fa-angle-right';
				$prev_arrow = 'fa fa-angle-left';
			}

			if ( ! empty( $arrow ) || ( ! empty( $select_arrow['value'] ) && $is_new ) ) { ?>
				<div class="trx-addons-slider-arrow trx-addons-arrow-prev elementor-swiper-button-prev swiper-button-prev-<?php echo esc_attr( $this->get_id() ); ?>">
					<?php if ( $is_new || $migrated ) :
						Icons_Manager::render_icon( $prev_arrow, [ 'aria-hidden' => 'true' ] );
					else : ?>
						<i <?php $this->print_render_attribute_string( 'arrow-icon' ); ?>></i>
					<?php endif; ?>
				</div>
				<div class="trx-addons-slider-arrow trx-addons-arrow-next elementor-swiper-button-next swiper-button-next-<?php echo esc_attr( $this->get_id() ); ?>">
					<?php if ( $is_new || $migrated ) :
						Icons_Manager::render_icon( $next_arrow, [ 'aria-hidden' => 'true' ] );
					else : ?>
						<i <?php $this->print_render_attribute_string( 'arrow-icon' ); ?>></i>
					<?php endif; ?>
				</div>
			<?php }
		}
	}

	/**
	 * Get swiper slider settings
	 */
	public function get_swiper_slider_settings( $settings, $new = false ) {
		$pagination = ( $new ) ? $settings['pagination'] : $settings['dots'];

		$effect = ( isset( $settings['carousel_effect'] ) && ( $settings['carousel_effect'] ) ) ? $settings['carousel_effect'] : 'slide';

		$slider_options = [
			'direction'     => 'horizontal',
			'effect'        => $effect,
			'speed'         => ( '' !== $settings['slider_speed']['size'] ) ? $settings['slider_speed']['size'] : 400,
			'slidesPerView' => ( '' !== $settings['items']['size'] ) ? absint( $settings['items']['size'] ) : 3,
			'spaceBetween'  => ( '' !== $settings['margin']['size'] ) ? absint( $settings['margin']['size'] ) : 10,
			'grabCursor'    => ( 'yes' === $settings['grab_cursor'] ),
			'autoHeight'    => ( 'yes' === $settings['adaptive_height'] ),
			'loop'          => ( 'yes' === $settings['infinite_loop'] ),
		];

		$autoplay_speed = 999999;

		if ( 'yes' === $settings['autoplay'] ) {
			if ( isset( $settings['autoplay_speed']['size'] ) ) {
				$autoplay_speed = $settings['autoplay_speed']['size'];
			} elseif ( $settings['autoplay_speed'] ) {
				$autoplay_speed = $settings['autoplay_speed'];
			}
		}

		$slider_options['autoplay'] = [
			'delay'                => $autoplay_speed,
			'disableOnInteraction' => ( 'yes' === $settings['pause_on_interaction'] ),
		];

		if ( 'yes' === $pagination ) {
			$slider_options['pagination'] = [
				'el'        => '.swiper-pagination-' . esc_attr( $this->get_id() ),
				'type'      => $settings['pagination_type'],
				'clickable' => true,
			];
		}

		if ( 'yes' === $settings['arrows'] ) {
			$slider_options['navigation'] = [
				'nextEl' => '.swiper-button-next-' . esc_attr( $this->get_id() ),
				'prevEl' => '.swiper-button-prev-' . esc_attr( $this->get_id() ),
			];
		}

		$elementor_bp_lg = get_option( 'elementor_viewport_lg' );
		$elementor_bp_md = get_option( 'elementor_viewport_md' );
		$bp_desktop      = ! empty( $elementor_bp_lg ) ? $elementor_bp_lg : 1025;
		$bp_tablet       = ! empty( $elementor_bp_md ) ? $elementor_bp_md : 768;
		$bp_mobile       = 320;

		$items        = ( isset( $settings['items']['size'] ) && '' !== $settings['items']['size'] ) ? absint( $settings['items']['size'] ) : 3;
		$items_tablet = ( isset( $settings['items_tablet']['size'] ) && '' !== $settings['items_tablet']['size'] ) ? absint( $settings['items_tablet']['size'] ) : 2;
		$items_mobile = ( isset( $settings['items_mobile']['size'] ) && '' !== $settings['items_mobile']['size'] ) ? absint( $settings['items_mobile']['size'] ) : 1;

		$margin        = ( isset( $settings['margin']['size'] ) && '' !== $settings['margin']['size'] ) ? absint( $settings['margin']['size'] ) : 10;
		$margin_tablet = ( isset( $settings['margin_tablet']['size'] ) && '' !== $settings['margin_tablet']['size'] ) ? absint( $settings['margin_tablet']['size'] ) : 10;
		$margin_mobile = ( isset( $settings['margin_mobile']['size'] ) && '' !== $settings['margin_mobile']['size'] ) ? absint( $settings['margin_mobile']['size'] ) : 10;

		$slider_options['breakpoints'] = [
			$bp_desktop => [
				'slidesPerView' => $items,
				'spaceBetween'  => $margin,
			],
			$bp_tablet  => [
				'slidesPerView' => $items_tablet,
				'spaceBetween'  => $margin_tablet,
			],
			$bp_mobile  => [
				'slidesPerView' => $items_mobile,
				'spaceBetween'  => $margin_mobile,
			],
		];

		return $slider_options;
	}

	/**
	 * Get swiper slider settings for content_template function
	 */
	public function get_swiper_slider_settings_js() {
		$elementor_bp_tablet    = get_option( 'elementor_viewport_lg' );
		$elementor_bp_mobile    = get_option( 'elementor_viewport_md' );
		$elementor_bp_lg        = get_option( 'elementor_viewport_lg' );
		$elementor_bp_md        = get_option( 'elementor_viewport_md' );
		$bp_desktop             = ! empty( $elementor_bp_lg ) ? $elementor_bp_lg : 1025;
		$bp_tablet              = ! empty( $elementor_bp_md ) ? $elementor_bp_md : 768;
		$bp_mobile              = 320;
		?>
		<#
			function get_slider_settings( settings ) {
		   
				if (typeof settings.effect !== 'undefined') {
					var $effect = settings.effect;
				} else {
					var $effect = 'slide';
				}

				var $items          = ( settings.items.size !== '' || settings.items.size !== undefined ) ? settings.items.size : 3,
					$items_tablet   = ( settings.items_tablet.size !== '' || settings.items_tablet.size !== undefined ) ? settings.items_tablet.size : 2,
					$items_mobile   = ( settings.items_mobile.size !== '' || settings.items_mobile.size !== undefined ) ? settings.items_mobile.size : 1,
					$margin         = ( settings.margin.size !== '' || settings.margin.size !== undefined ) ? settings.margin.size : 10,
					$margin_tablet  = ( settings.margin_tablet.size !== '' || settings.margin_tablet.size !== undefined ) ? settings.margin_tablet.size : 10,
					$margin_mobile  = ( settings.margin_mobile.size !== '' || settings.margin_mobile.size !== undefined ) ? settings.margin_mobile.size : 10,
					$autoplay       = ( settings.autoplay == 'yes' && settings.autoplay_speed.size != '' ) ? settings.autoplay_speed.size : 999999;

				return {
					direction:              "horizontal",
					speed:                  ( settings.slider_speed.size !== '' || settings.slider_speed.size !== undefined ) ? settings.slider_speed.size : 400,
					effect:                 $effect,
					slidesPerView:          $items,
					spaceBetween:           $margin,
					grabCursor:             ( settings.grab_cursor === 'yes' ) ? true : false,
					autoHeight:             ( settings.adaptive_height === 'yes' ) ? true : false,,
					loop:                   ( settings.infinite_loop === 'yes' ),
					autoplay: {
						delay: $autoplay,
						disableOnInteraction: ( settings.disableOnInteraction === 'yes' ),
					},
					pagination: {
						el: '.swiper-pagination',
						type: settings.pagination_type,
						clickable: true,
					},
					navigation: {
						nextEl: '.swiper-button-next',
						prevEl: '.swiper-button-prev',
					},
					breakpoints: {
						<?php echo esc_attr( $bp_desktop ); ?>: {
							slidesPerView:  $items,
							spaceBetween:   $margin
						},
						<?php echo esc_attr( $bp_tablet ); ?>: {
							slidesPerView:  $items_tablet,
							spaceBetween:   $margin_tablet
						},
						<?php echo esc_attr( $bp_mobile ); ?>: {
							slidesPerView:  $items_mobile,
							spaceBetween:   $margin_mobile
						}
					}
				};
			};
		#>
		<?php
	}
}
