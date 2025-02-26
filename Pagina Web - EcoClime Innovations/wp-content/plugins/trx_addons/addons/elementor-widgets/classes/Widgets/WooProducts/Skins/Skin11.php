<?php
namespace TrxAddons\ElementorWidgets\Widgets\WooProducts\Skins;

// Elementor Classes
use Elementor\Controls_Manager;
use Elementor\Widget_Base;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Border;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // If this file is called directly, abort.
}

/**
 * Class Skin11
 *
 * @property Products $parent
 */
class Skin11 extends BaseSkin {

	/**
	 * Get ID.
	 *
	 * @access public
	 */
	public function get_id() {
		return 'grid-11';
	}

	/**
	 * Get title.
	 *
	 * @access public
	 */
	public function get_title() {
		return __( 'Skin 11', 'trx_addons' );
	}

	/**
	 * Register control actions.
	 *
	 * @access protected
	 */
	protected function _register_controls_actions() {

		parent::_register_controls_actions();

		// -- Content Start
		// Content Controls.
		add_action( 'elementor/element/trx_elm_woo_products/section_pagination_options/after_section_end', array( $this, 'register_render_options_controls' ) );

		// Add to Cart Control.
		add_action( 'elementor/element/trx_elm_woo_products/section_pagination_options/after_section_end', array( $this, 'register_add_to_cart_controls' ) );

		// Quick View Controls.
		add_action( 'elementor/element/trx_elm_woo_products/section_pagination_options/after_section_end', array( $this, 'register_quick_view_controls' ) );


		// -- Style Start
		// Product Images Style.
		add_action( 'elementor/element/trx_elm_woo_products/section_product_box_style/after_section_end', array( $this, 'register_gallery_style' ), 55 );

		// Product CTA Style.
		add_action( 'elementor/element/trx_elm_woo_products/section_product_box_style/after_section_end', array( $this, 'register_product_cta_style' ), 60 );


		// Product Quick View Style.
		add_action( 'elementor/element/trx_elm_woo_products/section_carousel_style/after_section_end', array( $this, 'register_quick_style_controls' ), 90 );
	}

	/**
	 * Register content control section.
	 *
	 * @access public
	 *
	 * @param Widget_Base $widget widget object.
	 */
	public function register_render_options_controls( Widget_Base $widget ) {

		$this->parent = $widget;

		$this->start_controls_section(
			'section_content_field',
			array(
				'label' => __( 'Display Options', 'trx_addons' ),
			)
		);

		$this->add_control(
			'product_image',
			array(
				'label'   => __( 'Image', 'trx_addons' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			)
		);

		$this->add_control(
			'product_title',
			array(
				'label'   => __( 'Title', 'trx_addons' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			)
		);

		$this->add_control(
			'title_html_tag',
			array(
				'label'     => __( 'HTML Tag', 'trx_addons' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'h2',
				'options'   => array(
					'h1'   => __( 'H1', 'trx_addons' ),
					'h2'   => __( 'H2', 'trx_addons' ),
					'h3'   => __( 'H3', 'trx_addons' ),
					'h4'   => __( 'H4', 'trx_addons' ),
					'h5'   => __( 'H5', 'trx_addons' ),
					'h6'   => __( 'H6', 'trx_addons' ),
					'div'  => __( 'div', 'trx_addons' ),
					'span' => __( 'span', 'trx_addons' ),
					'p'    => __( 'p', 'trx_addons' ),
				),
				'condition' => array(
					$this->get_control_id( 'product_title' ) => 'yes',
				),
			)
		);

		$this->add_control(
			'title_above_img',
			array(
				'label'        => __( 'Place Title Above Image', 'trx_addons' ),
				'type'         => Controls_Manager::SWITCHER,
				'prefix_class' => 'trx-addons-woo-products-title-above-',
				'render_type'  => 'template',
				'condition'    => array(
					$this->get_control_id( 'product_title' ) => 'yes',
					$this->get_control_id( 'product_image' ) => 'yes',
				),
			)
		);

		$this->add_control(
			'product_category',
			array(
				'label'   => __( 'Category', 'trx_addons' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			)
		);

		$this->add_control(
			'product_rating',
			array(
				'label'   => __( 'Rating', 'trx_addons' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			)
		);

		$this->add_control(
			'product_price',
			array(
				'label'   => __( 'Price', 'trx_addons' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			)
		);

		$this->add_control(
			'product_cta',
			array(
				'label'   => __( 'Add To Cart', 'trx_addons' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			)
		);

		$this->add_responsive_control(
			'alignment',
			array(
				'label'        => __( 'Alignment', 'trx_addons' ),
				'type'         => Controls_Manager::CHOOSE,
				'options'      => array(
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
				'default'      => 'center',
				'toggle'       => false,
				'prefix_class' => 'trx-addons-woo-products-product-align-',
				'selectors'    => array(
					'{{WRAPPER}} .trx-addons-woo-products-details-wrap, {{WRAPPER}} .trx-addons-woo-products-product__link'    => 'text-align: {{VALUE}}',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * register_add_to_cart_controls
	 *
	 * @return void
	 * 
	 *  @param Widget_Base $widget widget object.
	 */
	public function register_add_to_cart_controls( Widget_Base $widget ) {

		$this->parent = $widget;

		$this->start_controls_section(
			'section_add_to_cart',
			array(
				'label' => __( 'Add To Cart', 'trx_addons' ),
				'condition' => array(
					$this->get_control_id( 'product_cta' ) => 'yes'
				),
			)
		);

		$this->add_control(
			'cta_buy_now_label',
			array(
				'label'     => __( 'Buy Now Text', 'trx_addons' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'Buy Now', 'trx_addons' ),
			)
		);
		$this->add_control(
			'cta_read_more_label',
			array(
				'label'     => __( 'Read More Text', 'trx_addons' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'Read More', 'trx_addons' ),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Register Product Images style Controls.
	 *
	 * @access public
	 *
	 * @param Widget_Base $widget widget object.
	 */
	public function register_gallery_style( Widget_Base $widget ) {

		$this->parent = $widget;

		$this->start_controls_section(
			'section_gallery_style',
			array(
				'label' => __( 'Gallery', 'trx_addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'gal_size',
			array(
				'label'      => __( 'Size', 'trx_addons' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'range'      => array(
					'px' => array(
						'min' => 20,
						'max' => 200,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .trx-addons-woo-products .trx-addons-woo-products-product-gallery-images .trx-addons-woo-products-product__gallery_image' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'gal_padding',
			array(
				'label'      => __( 'Padding', 'trx_addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .trx-addons-woo-products .trx-addons-woo-products-product-gallery-images .trx-addons-woo-products-product__gallery_image' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'gal_margin',
			array(
				'label'      => __( 'Margin', 'trx_addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .trx-addons-woo-products .trx-addons-woo-products-product-gallery-images' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->start_controls_tabs( 'qal_style_tabs' );

		$this->start_controls_tab(
			'qal_style_tab_normal',
			array(
				'label' => __( 'Normal', 'trx_addons' ),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'gal_shadow',
				'selector' => '{{WRAPPER}} .trx-addons-woo-products .trx-addons-woo-products-product-gallery-images .trx-addons-woo-products-product__gallery_image',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'gal_border',
				'selector' => '{{WRAPPER}} .trx-addons-woo-products .trx-addons-woo-products-product-gallery-images .trx-addons-woo-products-product__gallery_image',
			)
		);

		$this->add_control(
			'gal_radius',
			array(
				'label'      => __( 'Border Radius', 'trx_addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .trx-addons-woo-products .trx-addons-woo-products-product-gallery-images .trx-addons-woo-products-product__gallery_image' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'gal_style_tab_hover',
			array(
				'label' => __( 'Hover', 'trx_addons' ),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'gal_shadow_hover',
				'selector' => '{{WRAPPER}} .trx-addons-woo-products .trx-addons-woo-products-product-gallery-images .trx-addons-woo-products-product__gallery_image:hover',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'gal_border_hover',
				'selector' => '{{WRAPPER}} .trx-addons-woo-products .trx-addons-woo-products-product-gallery-images .trx-addons-woo-products-product__gallery_image:hover',
			)
		);

		$this->add_control(
			'gal_radius_hover',
			array(
				'label'      => __( 'Border Radius', 'trx_addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .trx-addons-woo-products .trx-addons-woo-products-product-gallery-images .trx-addons-woo-products-product__gallery_image:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * Register Product CTA style Controls.
	 *
	 * @access public
	 *
	 * @param Widget_Base $widget widget object.
	 */
	public function register_product_cta_style( Widget_Base $widget ) {

		$this->parent = $widget;

		$this->start_controls_section(
			'section_button_style',
			array(
				'label'     => __( 'Add To Cart', 'trx_addons' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					$this->get_control_id( 'product_cta' ) => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'cta_typography',
				'selector' => '{{WRAPPER}} .trx-addons-woo-products .trx-addons-woo-products-details-wrap .trx-addons-woo-products-atc-button .button',
				'global'   => array(
					'default' => Global_Typography::TYPOGRAPHY_ACCENT,
				),
			)
		);

		$this->add_responsive_control(
			'cta_padding',
			array(
				'label'      => __( 'Padding', 'trx_addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .trx-addons-woo-products .trx-addons-woo-products-details-wrap .trx-addons-woo-products-atc-button .button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->start_controls_tabs( 'cta_style_tabs' );

		$this->start_controls_tab(
			'cta_style_tab_normal',
			array(
				'label' => __( 'Normal', 'trx_addons' ),
			)
		);

		$this->add_control(
			'cta_color',
			array(
				'label'     => __( 'Color', 'trx_addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .trx-addons-woo-products-details-wrap .trx-addons-woo-products-atc-button .button, {{WRAPPER}} .trx-addons-woo-products-cart-btn .trx-addons-woo-products-add-cart-icon' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'cta_background',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .trx-addons-woo-products-details-wrap .trx-addons-woo-products-atc-button .button, {{WRAPPER}} .trx-addons-woo-products-cart-btn',
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'cta_shadow',
				'selector' => '{{WRAPPER}} .trx-addons-woo-products-details-wrap .trx-addons-woo-products-atc-button .button, {{WRAPPER}} .trx-addons-woo-products-cart-btn',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'cta_border',
				'selector' => '{{WRAPPER}} .trx-addons-woo-products-details-wrap .trx-addons-woo-products-atc-button .button, {{WRAPPER}} .trx-addons-woo-products-cart-btn',
			)
		);

		$this->add_control(
			'cta_radius',
			array(
				'label'      => __( 'Border Radius', 'trx_addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .trx-addons-woo-products-details-wrap .trx-addons-woo-products-atc-button .button, {{WRAPPER}} .trx-addons-woo-products-cart-btn' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'cta_style_tab_hover',
			array(
				'label' => __( 'Hover', 'trx_addons' ),
			)
		);

		$this->add_control(
			'cta_color_hover',
			array(
				'label'     => __( 'Color', 'trx_addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .trx-addons-woo-products-details-wrap .trx-addons-woo-products-atc-button .button:hover, {{WRAPPER}} .trx-addons-woo-products-cart-btn:hover .trx-addons-woo-products-add-cart-icon' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'cta_background_hover',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .trx-addons-woo-products .trx-addons-woo-products-details-wrap .trx-addons-woo-products-atc-button .button:hover, {{WRAPPER}} .trx-addons-woo-products-cart-btn:hover',
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'cta_shadow_hover',
				'selector' => '{{WRAPPER}} .trx-addons-woo-products-details-wrap .trx-addons-woo-products-atc-button .button:hover, {{WRAPPER}} .trx-addons-woo-products-cart-btn:hover',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'cta_border_hover',
				'selector' => '{{WRAPPER}} .trx-addons-woo-products-details-wrap .trx-addons-woo-products-atc-button .button:hover, {{WRAPPER}} .trx-addons-woo-products-cart-btn:hover',
			)
		);

		$this->add_control(
			'cta_radius_hover',
			array(
				'label'      => __( 'Border Radius', 'trx_addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .trx-addons-woo-products-details-wrap .trx-addons-woo-products-atc-button .button:hover, {{WRAPPER}} .trx-addons-woo-products-cart-btn:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * Register Quick View Controls.
	 *
	 * @access public
	 *
	 * @param Widget_Base $widget widget object.
	 */
	public function register_quick_view_controls( Widget_Base $widget ) {

		$this->parent = $widget;

		$this->start_controls_section(
			'section_content_quick_view',
			array(
				'label' => __( 'Quick View', 'trx_addons' ),
			)
		);

		$this->add_control(
			'quick_view_notice',
			array(
				'raw'             => __( 'Please make sure that Display Options includes CTA', 'trx_addons' ),
				'type'            => Controls_Manager::RAW_HTML,
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
			)
		);

		$this->add_control(
			'quick_view',
			array(
				'label'   => __( 'Enable Quick View', 'trx_addons' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			)
		);

		$this->add_responsive_control(
			'qv_pos',
			array(
				'label'      => __( 'Vertical Position', 'trx_addons' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 200,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .trx-addons-woo-products .trx-addons-woo-products-qv-btn' => 'top: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					$this->get_control_id( 'quick_view' ) => 'yes',
					$this->get_control_id( 'product_title' ) => 'yes',
					$this->get_control_id( 'product_image' ) => 'yes',
					$this->get_control_id( 'title_above_img' ) => 'yes',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Register Style Quick View Controls.
	 *
	 * @access public
	 *
	 * @param Widget_Base $widget widget object.
	 */
	public function register_quick_style_controls( Widget_Base $widget ) {

		$this->parent = $widget;

		$this->start_controls_section(
			'section_quick_view_style',
			array(
				'label'     => __( 'Quick View Icon', 'trx_addons' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					$this->get_control_id( 'quick_view' ) => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'qv_size',
			array(
				'label'      => __( 'Size', 'trx_addons' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'range'      => array(
					'px' => array(
						'min' => 20,
						'max' => 200,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .trx-addons-woo-products .trx-addons-woo-products-qv-btn' => 'font-size: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'qv_padding',
			array(
				'label'      => __( 'Padding', 'trx_addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .trx-addons-woo-products .trx-addons-woo-products-qv-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->start_controls_tabs( 'qv_style_tabs' );

		$this->start_controls_tab(
			'qv_style_tab_normal',
			array(
				'label' => __( 'Normal', 'trx_addons' ),
			)
		);

		$this->add_control(
			'qv_color',
			array(
				'label'     => __( 'Color', 'trx_addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .trx-addons-woo-products .trx-addons-woo-products-qv-btn .trx-addons-woo-products-qv-icon' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'qv_background',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .trx-addons-woo-products .trx-addons-woo-products-qv-btn',
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'qv_shadow',
				'selector' => '{{WRAPPER}} .trx-addons-woo-products .trx-addons-woo-products-qv-btn',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'qv_border',
				'selector' => '{{WRAPPER}} .trx-addons-woo-products .trx-addons-woo-products-qv-btn',
			)
		);

		$this->add_control(
			'qv_radius',
			array(
				'label'      => __( 'Border Radius', 'trx_addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .trx-addons-woo-products .trx-addons-woo-products-qv-btn' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'qv_style_tab_hover',
			array(
				'label' => __( 'Hover', 'trx_addons' ),
			)
		);

		$this->add_control(
			'qv_color_hover',
			array(
				'label'     => __( 'Color', 'trx_addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .trx-addons-woo-products .trx-addons-woo-products-qv-btn:hover .trx-addons-woo-products-qv-icon' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'qv_background_hover',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .trx-addons-woo-products .trx-addons-woo-products-qv-btn:hover',
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'qv_shadow_hover',
				'selector' => '{{WRAPPER}} .trx-addons-woo-products .trx-addons-woo-products-qv-btn:hover',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'qv_border_hover',
				'selector' => '{{WRAPPER}} .trx-addons-woo-products .trx-addons-woo-products-qv-btn:hover',
			)
		);

		$this->add_control(
			'qv_radius_hover',
			array(
				'label'      => __( 'Border Radius', 'trx_addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .trx-addons-woo-products .trx-addons-woo-products-qv-btn:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * render_quick_view
	 *
	 * @return void
	 */
	public function render_quick_view() {
		global $product;

		$product_id = $product->get_id();

        ?>
		<div class="trx-addons-woo-products-qv-btn" data-product-id="<?php esc_attr_e( $product_id ); ?>">
			<?php $this->render_common_icon( 'qv_icon', array( 'default_icon' => 'far fa-eye', 'skin_used' => false, 'tag_name' => 'span' ) ); ?>
		</div>
		<?php
	}

	/**
	 * render_product_image_slide
	 *
	 * @return void
	 */
	public function render_product_image_slide() {
		global $product;

		$attachment_ids = $product->get_gallery_image_ids();

		if ( $attachment_ids ) {
			?><div class="trx-addons-woo-products-thumbnail-swiper-slide swiper-slide"><?php
		}
		woocommerce_template_loop_product_link_open();

		$this->render_product_image();

		woocommerce_template_loop_product_link_close();

		if ( $attachment_ids ) {
			?></div><?php
		}
	}

	/**
	 * Render Product Template.
	 */
	public function render_product_template() {

		global $product;

		// Ensure visibility.
		if ( empty( $product ) || ! $product->is_visible() ) {
			return;
		}

        ?>
        <li class="<?php echo $this->get_product_wc_classes(); ?>">
            <div class="trx-addons-woo-products-product-wrapper">
                <?php if ( 'yes' === $this->get_instance_value( 'product_image' ) ) : ?>
                    <div class="trx-addons-woo-products-product-thumbnail">
                        <?php $this->render_ribbon_container(); ?>

                        <div class="trx-addons-woo-products-product-thumbnail-wrapper">
                            <?php
							$this->render_product_thumbnail_slider_start();

							$this->render_product_image_slide();

                            $this->render_current_product_images_links();

							$this->render_product_thumbnail_slider_end();
                            ?>
                        </div>
                    </div>

                    <?php
                    if ( 'yes' === $this->get_instance_value( 'quick_view' ) ) {
                        $this->render_quick_view();
                    }
                    ?>
                <?php endif; ?>

                <?php
                $this->render_details_wrap_start();

                    if ( 'yes' === $this->get_instance_value( 'product_title' ) ) {
                        $this->render_product_structure_title();
                    }

                    if ( 'yes' === $this->get_instance_value( 'product_category' ) ) {
                        $this->render_product_structure_category();
                    }

                    ?>
					<div class="trx-addons-woo-products-product-info">
						<?php

                        if ( 'yes' === $this->get_instance_value( 'product_price' ) ) {
                            $this->render_product_structure_price();
                        }

                        if ( 'yes' === $this->get_instance_value( 'product_image' ) ) {
                            $this->render_current_product_gallery_images();
                        }

                        if ( 'yes' === $this->get_instance_value( 'product_rating' ) ) {
                            $this->render_product_structure_ratings();
                        }
						?>
					</div>

					<div class="trx-addons-woo-products-product-buttons">
						<?php
                        if ( 'yes' === $this->get_instance_value( 'product_cta' ) ) {
                            $this->render_product_structure_cta_hard();
                        }
						?>
					</div>
					<?php

                $this->render_details_wrap_end();
                ?>
            </div>
        </li>
        <?php
    }
}
