<?php
namespace TrxAddons\ElementorWidgets\Widgets\WooProducts\Skins;

use TrxAddons\ElementorWidgets\BaseWidget;
use TrxAddons\ElementorWidgets\Widgets\WooProducts\WooProducts;

// Elementor Classes
use Elementor\Repeater;
use Elementor\Icons_Manager;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Css_Filter;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Background;
use Elementor\Skin_Base as Elementor_Skin_Base;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Skin Base
 */
abstract class BaseSkin extends Elementor_Skin_Base {

	/**
	 * Query object
	 *
	 * @var object $query
	 */
	public static $query;

	/**
	 * Query args
	 *
	 * @var object $query_args
	 */
	public static $query_args;

	/**
	 * Rendered Settings
	 *
	 * @var object $_render_attributes
	 */
	public $_render_attributes;

	protected function _register_controls_actions() {

		// -- Style Start
		// Product Title Style.
		add_action( 'elementor/element/trx_elm_woo_products/section_product_box_style/after_section_end', array( $this, 'register_title_style_controls' ), 10 );

		// Product Category Style.
		add_action( 'elementor/element/trx_elm_woo_products/section_product_box_style/after_section_end', array( $this, 'register_cat_style_controls' ), 20 );

		// Product Price Style.
		add_action( 'elementor/element/trx_elm_woo_products/section_product_box_style/after_section_end', array( $this, 'register_product_price_style' ), 40 );

		// Product Rating Style.
		add_action( 'elementor/element/trx_elm_woo_products/section_product_box_style/after_section_end', array( $this, 'register_product_rating_style' ), 50 );

		// Product Quick View Style.
		add_action( 'elementor/element/trx_elm_woo_products/section_carousel_style/after_section_end', array( $this, 'register_quick_view_modal_style_controls' ), 100 );
		add_action( 'elementor/element/trx_elm_woo_products/section_carousel_style/after_section_end', array( $this, 'register_quick_view_content_style_controls' ), 100 );
		add_action( 'elementor/element/trx_elm_woo_products/section_carousel_style/after_section_end', array( $this, 'register_quick_view_slider_style_controls' ), 100 );
	}

	/**
	 * Register Product Rating Style Controls.
	 *
	 * @return void
	 */
	public function register_product_rating_style() {

		$this->start_controls_section(
			'section_rating_style',
			array(
				'label' => __( 'Rating', 'trx_addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'star_color',
			array(
				'label'     => __( 'Star Color', 'trx_addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .trx-addons-woo-products li.product div.star-rating span' => 'color: {{VALUE}}',
					'{{WRAPPER}} .trx-addons-woo-products li.product div.star-rating' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'empty_star_color',
			array(
				'label'     => __( 'Empty Star Color', 'trx_addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .trx-addons-woo-products li.product div.star-rating::before' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'star_size',
			array(
				'label'     => __( 'Star Size', 'trx_addons' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => array(
					'unit' => 'em',
				),
				'range'     => array(
					'em' => array(
						'min'  => 0,
						'max'  => 4,
						'step' => 0.1,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .trx-addons-woo-products li.product .star-rating' => 'font-size: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->add_responsive_control(
			'rating_spacing',
			array(
				'label'      => __( 'Bottom Spacing', 'trx_addons' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'default'    => array(
					'size'  => 20,
					'unit' => 'px',
				),
				'range'      => array(
					'em' => array(
						'min'  => 0,
						'max'  => 5,
						'step' => 0.1,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}}  .trx-addons-woo-products li.product .star-rating' => 'margin-bottom: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Register Product Price Style Controls.
	 *
	 * @return void
	 */
	public function register_product_price_style() {

		$this->start_controls_section(
			'section_price_style',
			array(
				'label' => __( 'Price', 'trx_addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name'     => 'price_text_shadow',
				'selector' => '{{WRAPPER}} .trx-addons-woo-products li.product .price',
			)
		);

		$this->add_responsive_control(
			'price_spacing',
			array(
				'label'      => __( 'Margin', 'trx_addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'default'    => array(
					'top'      => '0',
					'bottom'   => '0.5',
					'left'     => '0',
					'right'    => '0',
					'unit'     => 'em',
					'isLinked' => false
				),
				'range'      => array(
					'em' => array(
						'step' => 0.5,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .trx-addons-woo-products li.product .price' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->start_controls_tabs(
			'price_style_tabs'
		);

		$this->start_controls_tab(
			'price_tab',
			array(
				'label' => __( 'Price', 'trx_addons' ),
			)
		);

		$this->add_control(
			'price_color',
			array(
				'label'     => __( 'Price Color', 'trx_addons' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_TEXT,
				),
				'selectors' => array(
					'{{WRAPPER}} .trx-addons-woo-products li.product .price' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'price_typography',
				'global'   => array(
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				),
				'selector' => '{{WRAPPER}} .trx-addons-woo-products li.product .price',
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'slashed_price_tab',
			array(
				'label' => __( 'Slashed', 'trx_addons' ),
			)
		);

		$this->add_control(
			'slashed_price_color',
			array(
				'label'     => __( 'Slashed Price Color', 'trx_addons' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_TEXT,
				),
				'selectors' => array(
					'{{WRAPPER}} .trx-addons-woo-products li.product .price del' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'slashed_price_typography',
				'global'   => array(
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				),
				'exclude'  => array( 'word_spacing' ),
				'selector' => '{{WRAPPER}} .trx-addons-woo-products li.product .price del',
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * Register Title Style Controls.
	 *
	 * @return void
	 */
	public function register_title_style_controls() {

		$this->start_controls_section(
			'section_title_style',
			array(
				'label' => __( 'Title', 'trx_addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'title_color',
			array(
				'label'     => __( 'Color', 'trx_addons' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .trx-addons-woo-products .woocommerce-loop-product__title' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'title_hover_color',
			array(
				'label'     => __( 'Hover Color', 'trx_addons' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .trx-addons-woo-products .woocommerce-loop-product__title:hover' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'title_typography',
				'global'   => array(
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				),
				'selector' => '{{WRAPPER}} .trx-addons-woo-products .woocommerce-loop-product__title',
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name'     => 'title_text_shadow',
				'selector' => '{{WRAPPER}} .trx-addons-woo-products .woocommerce-loop-product__title',
			)
		);

		$this->add_responsive_control(
			'title_spacing',
			array(
				'label'      => __( 'Margin', 'trx_addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .trx-addons-woo-products .woocommerce-loop-product__title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Register Category Style Controls.
	 *
	 * @return void
	 */
	public function register_cat_style_controls() {

		$this->start_controls_section(
			'section_category_style',
			array(
				'label' => __( 'Category', 'trx_addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'category_color',
			array(
				'label'     => __( 'Color', 'trx_addons' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_TEXT,
				),
				'selectors' => array(
					'{{WRAPPER}} .trx-addons-woo-products .trx-addons-woo-products-product-category' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'category_typography',
				'global'   => array(
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				),
				'selector' => '{{WRAPPER}} .trx-addons-woo-products .trx-addons-woo-products-product-category',
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name'     => 'category_text_shadow',
				'selector' => '{{WRAPPER}} .trx-addons-woo-products .trx-addons-woo-products-product-category',
			)
		);

		$this->add_responsive_control(
			'category_spacing',
			array(
				'label'      => __( 'Margin', 'trx_addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .trx-addons-woo-products .trx-addons-woo-products-product-category' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Register Quick View Style Controls.
	 *
	 * @return void
	 */
	public function register_quick_view_modal_style_controls() {

		$this->start_controls_section(
			'quick_view_modal_style',
			array(
				'label' => __( 'Quick View Modal', 'trx_addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'qv_width',
			array(
				'label'       => __( 'Width', 'trx_addons' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => array( 'px', 'vw', '%', 'custom' ),
				'range'       => array(
					'px' => array(
						'min' => 50,
						'max' => 1500,
					),
					'em' => array(
						'min' => 1,
						'max' => 50,
					),
				),
				'selectors'   => array(
					'#trx-addons-woo-products-quick-view-{{ID}} .trx-addons-woo-products-lightbox-content'  => 'width: {{SIZE}}{{UNIT}} !important',
				),
			)
		);

		$this->add_control(
			'lightbox_overlay_color',
			array(
				'label'     => __( 'Overlay Color', 'trx_addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'#trx-addons-woo-products-quick-view-{{ID}} .trx-addons-woo-products-quick-view-back' => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'      => 'qv_container_background',
				'types'     => array( 'classic', 'gradient' ),
				'selector'  => '#trx-addons-woo-products-quick-view-{{ID}} #trx-addons-woo-products-quick-view-modal .trx-addons-woo-products-lightbox-content',
			)
		);

		$this->add_responsive_control(
			'lightbox_padding',
			array(
				'label'      => __( 'Padding', 'trx_addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'#trx-addons-woo-products-quick-view-{{ID}} .trx-addons-woo-products-lightbox-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'lightbox_border',
				'selector' => '#trx-addons-woo-products-quick-view-{{ID}} .trx-addons-woo-products-lightbox-content',
			)
		);

		$this->add_responsive_control(
			'lightbox_border_radius',
			array(
				'label'      => __( 'Border Radius', 'trx_addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'#trx-addons-woo-products-quick-view-{{ID}} .trx-addons-woo-products-lightbox-content' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
			)
		);

		$this->add_control(
			'qv_close_heading',
			array(
				'label' => __( 'Close Icon', 'trx_addons' ),
				'type'  => Controls_Manager::HEADING,
			)
		);

		$this->add_control(
			'close_icon_color',
			array(
				'label'     => __( 'Icon Color', 'trx_addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'#trx-addons-woo-products-quick-view-{{ID}} .trx-addons-woo-products-quick-view-close' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'close_icon_color_hover',
			array(
				'label'     => __( 'Icon Hover Color', 'trx_addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'#trx-addons-woo-products-quick-view-{{ID}} .trx-addons-woo-products-quick-view-close:hover' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_responsive_control(
			'close_icon_size',
			array(
				'label'     => __( 'Icon Size', 'trx_addons' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min' => 10,
						'max' => 50,
					),
				),
				'selectors' => array(
					'#trx-addons-woo-products-quick-view-{{ID}} .trx-addons-woo-products-quick-view-close' => 'font-size: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'close_icon_backcolor',
			array(
				'label'     => __( 'Background Color', 'trx_addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'#trx-addons-woo-products-quick-view-{{ID}} .trx-addons-woo-products-quick-view-close' => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_responsive_control(
			'close_icon_border_radius',
			array(
				'label'      => __( 'Border Radius', 'trx_addons' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'#trx-addons-woo-products-quick-view-{{ID}} .trx-addons-woo-products-quick-view-close' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'close_icon_padding',
			array(
				'label'      => __( 'Padding', 'trx_addons' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'#trx-addons-woo-products-quick-view-{{ID}} .trx-addons-woo-products-quick-view-close' => 'padding: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Register Quick View Style Controls.
	 *
	 * @return void
	 */
	public function register_quick_view_content_style_controls() {

		$this->start_controls_section(
			'quick_view_content_style',
			array(
				'label' => __( 'Quick View Content', 'trx_addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

        $this->add_control(
			'qv_ribbon_heading',
			array(
				'label' => __( 'Sale Ribbon', 'trx_addons' ),
				'type'  => Controls_Manager::HEADING,
                'condition' => array(
					'qv_sale!' => 'yes',
				),
			)
		);

        $this->add_control(
			'qv_ribbon_color',
			array(
				'label'     => __( 'Color', 'trx_addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'#trx-addons-woo-products-quick-view-{{ID}} .trx-addons-woo-products-qv-badge .corner' => 'color: {{VALUE}};',
				),
                'condition' => array(
					'qv_sale!' => 'yes',
				),
			)
		);

        $this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'qv_ribbon_typography',
				'global'   => array(
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				),
				'selector' => '#trx-addons-woo-products-quick-view-{{ID}} .trx-addons-woo-products-qv-badge .corner',
                'condition' => array(
					'qv_sale!' => 'yes',
				),
			)
		);

        $this->add_control(
			'qv_ribbon_backcolor',
			array(
				'label'     => __( 'Background Color', 'trx_addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'#trx-addons-woo-products-quick-view-{{ID}} .trx-addons-woo-products-qv-badge .corner' => 'background-color: {{VALUE}};',
				),
                'condition' => array(
					'qv_sale!' => 'yes',
				),
			)
		);

		$this->add_control(
			'qv_name_heading',
			array(
				'label' => __( 'Product Name', 'trx_addons' ),
				'type'  => Controls_Manager::HEADING,
			)
		);

		$this->add_control(
			'qv_name_color',
			array(
				'label'     => __( 'Color', 'trx_addons' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'selectors' => array(
					'#trx-addons-woo-products-quick-view-{{ID}} .woocommerce-loop-product__title' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'qv_name_hover_color',
			array(
				'label'     => __( 'Hover Color', 'trx_addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'#trx-addons-woo-products-quick-view-{{ID}} .woocommerce-loop-product__title:hover' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'qv_name_typography',
				'global'   => array(
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				),
				'selector' => '#trx-addons-woo-products-quick-view-{{ID}} .woocommerce-loop-product__title',
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name'     => 'qv_name_text_shadow',
				'selector' => '#trx-addons-woo-products-quick-view-{{ID}} .woocommerce-loop-product__title',
			)
		);

		$this->add_responsive_control(
			'qv_name_spacing',
			array(
				'label'      => __( 'Margin', 'trx_addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'#trx-addons-woo-products-quick-view-{{ID}} .woocommerce-loop-product__title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'qv_rating_heading',
			array(
				'label'     => __( 'Product Rating', 'trx_addons' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array(
					'qv_rating!' => 'yes',
				),
			)
		);

		$this->add_control(
			'qv_star_color',
			array(
				'label'     => __( 'Star Color', 'trx_addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'#trx-addons-woo-products-quick-view-{{ID}} div.star-rating' => 'color: {{VALUE}}',
				),
				'condition' => array(
					'qv_rating!' => 'yes',
				),
			)
		);

		$this->add_control(
			'qv_empty_star_color',
			array(
				'label'     => __( 'Empty Star Color', 'trx_addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'#trx-addons-woo-products-quick-view-{{ID}} div.star-rating::before' => 'color: {{VALUE}}',
				),
				'condition' => array(
					'qv_rating!' => 'yes',
				),
			)
		);

		$this->add_control(
			'qv_star_size',
			array(
				'label'     => __( 'Star Size', 'trx_addons' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => array(
					'unit' => 'em',
				),
				'range'     => array(
					'em' => array(
						'min'  => 0,
						'max'  => 4,
						'step' => 0.1,
					),
				),
				'selectors' => array(
					'#trx-addons-woo-products-quick-view-{{ID}} div.star-rating' => 'font-size: {{SIZE}}{{UNIT}}',
				),
				'condition' => array(
					'qv_rating!' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'qv_rating_spacing',
			array(
				'label'      => __( 'Bottom Spacing', 'trx_addons' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'range'      => array(
					'em' => array(
						'min'  => 0,
						'max'  => 5,
						'step' => 0.1,
					),
				),
				'selectors'  => array(
					'#trx-addons-woo-products-quick-view-{{ID}} div.star-rating' => 'margin-bottom: {{SIZE}}{{UNIT}}',
				),
				'condition'  => array(
					'qv_rating!' => 'yes',
				),
			)
		);

		$this->add_control(
			'qv_price_heading',
			array(
				'label'     => __( 'Product Price', 'trx_addons' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array(
					'qv_price!' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'qv_price_spacing',
			array(
				'label'      => __( 'Bottom Spacing', 'trx_addons' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'range'      => array(
					'em' => array(
						'min'  => 0,
						'max'  => 5,
						'step' => 0.1,
					),
				),
				'selectors'  => array(
					'#trx-addons-woo-products-quick-view-{{ID}} .price' => 'margin-bottom: {{SIZE}}{{UNIT}}',
				),
				'condition'  => array(
					'qv_price!' => 'yes',
				),
			)
		);

		$this->start_controls_tabs( 'qv_price_style_tabs' );

		$this->start_controls_tab(
			'qv_price_tab',
			array(
				'label'     => __( 'Price', 'trx_addons' ),
				'condition' => array(
					'qv_price!' => 'yes',
				),
			)
		);

		$this->add_control(
			'qv_price_color',
			array(
				'label'     => __( 'Price Color', 'trx_addons' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_TEXT,
				),
				'selectors' => array(
					'#trx-addons-woo-products-quick-view-{{ID}} div.product p.price' => 'color: {{VALUE}}',
				),
				'condition' => array(
					'qv_price!' => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'qv_price_typography',
				'global'    => array(
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				),
				'selector'  => '#trx-addons-woo-products-quick-view-{{ID}} div.product p.price, .trx-addons-woo-products-quick-view-{{ID}} div.product p.price ins',
				'condition' => array(
					'qv_price!' => 'yes',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'qv_slashed_price_tab',
			array(
				'label'     => __( 'Slashed', 'trx_addons' ),
				'condition' => array(
					'qv_price!' => 'yes',
				),
			)
		);

		$this->add_control(
			'qv_slashed_price_color',
			array(
				'label'     => __( 'Slashed Price Color', 'trx_addons' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_TEXT,
				),
				'selectors' => array(
					'#trx-addons-woo-products-quick-view-{{ID}} .price del' => 'color: {{VALUE}}',
				),
				'condition' => array(
					'qv_price!' => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'qv_slashed_price_typography',
				'selector'  => '#trx-addons-woo-products-quick-view-{{ID}} .price del',
				'exclude'  => array( 'word_spacing' ),
				'condition' => array(
					'qv_price!' => 'yes',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

        $this->add_control(
			'qv_desc_heading',
			array(
				'label'     => __( 'Product Description', 'trx_addons' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
                'condition' => array(
					'qv_desc!' => 'yes',
				),
			)
		);

		$this->add_control(
			'qv_desc_color',
			array(
				'label'     => __( 'Color', 'trx_addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'#trx-addons-woo-products-quick-view-{{ID}} .trx-addons-woo-products-qv-desc' => 'color: {{VALUE}}',
				),
				'condition' => array(
					'qv_desc!' => 'yes',
				),
			)
		);

        $this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'qv_desc_typography',
				'selector'  => '#trx-addons-woo-products-quick-view-{{ID}} .trx-addons-woo-products-qv-desc',
				'global'    => array(
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				),
				'condition' => array(
					'qv_desc!' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'qv_desc_margin',
			array(
				'label'      => __( 'Margin', 'trx_addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'#trx-addons-woo-products-quick-view-{{ID}} .trx-addons-woo-products-qv-desc' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'  => array(
					'qv_desc!' => 'yes',
				),
			)
		);

		$this->add_control(
			'qv_atc_heading',
			array(
				'label'     => __( 'Product CTA', 'trx_addons' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array(
					'qv_atc!' => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'qv_cta_typography',
				'selector'  => '#trx-addons-woo-products-quick-view-{{ID}} .trx-addons-woo-products-atc-button button.button.alt',
				'global'    => array(
					'default' => Global_Typography::TYPOGRAPHY_ACCENT,
				),
				'condition' => array(
					'qv_atc!' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'qv_cta_padding',
			array(
				'label'      => __( 'Padding', 'trx_addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'#trx-addons-woo-products-quick-view-{{ID}} .trx-addons-woo-products-atc-button button.button.alt' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'  => array(
					'qv_atc!' => 'yes',
				),
			)
		);

		$this->start_controls_tabs( 'qv_cta_style_tabs' );

		$this->start_controls_tab(
			'qv_cta_style_tab_normal',
			array(
				'label'     => __( 'Normal', 'trx_addons' ),
				'condition' => array(
					'qv_atc!' => 'yes',
				),
			)
		);

		$this->add_control(
			'qv_cta_color',
			array(
				'label'     => __( 'Color', 'trx_addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'#trx-addons-woo-products-quick-view-{{ID}} .trx-addons-woo-products-atc-button button.button.alt' => 'color: {{VALUE}};',
				),
				'condition' => array(
					'qv_atc!' => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'      => 'qv_cta_background',
				'types'     => array( 'classic', 'gradient' ),
				'selector'  => '#trx-addons-woo-products-quick-view-{{ID}} .trx-addons-woo-products-atc-button button.button.alt',
				'condition' => array(
					'qv_atc!' => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'      => 'qv_cta_shadow',
				'selector'  => '#trx-addons-woo-products-quick-view-{{ID}} .trx-addons-woo-products-atc-button button.button.alt',
				'condition' => array(
					'qv_atc!' => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'      => 'qv_cta_border',
				'selector'  => '#trx-addons-woo-products-quick-view-{{ID}} .trx-addons-woo-products-atc-button button.button.alt',
				'condition' => array(
					'qv_atc!' => 'yes',
				),
			)
		);

		$this->add_control(
			'qv_cta_radius',
			array(
				'label'      => __( 'Border Radius', 'trx_addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'#trx-addons-woo-products-quick-view-{{ID}} .trx-addons-woo-products-atc-button button.button.alt' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
				'condition'  => array(
					'qv_atc!' => 'yes',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'qv_cta_style_tab_hover',
			array(
				'label'     => __( 'Hover', 'trx_addons' ),
				'condition' => array(
					'qv_atc!' => 'yes',
				),
			)
		);

		$this->add_control(
			'qv_cta_color_hover',
			array(
				'label'     => __( 'Color', 'trx_addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'#trx-addons-woo-products-quick-view-{{ID}} .trx-addons-woo-products-atc-button button.button.alt:hover' => 'color: {{VALUE}}',
				),
				'condition' => array(
					'qv_atc!' => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'      => 'qv_cta_background_hover',
				'types'     => array( 'classic', 'gradient' ),
				'selector'  => '#trx-addons-woo-products-quick-view-{{ID}} .trx-addons-woo-products-atc-button button.button.alt:hover',
				'condition' => array(
					'qv_atc!' => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'      => 'qv_cta_shadow_hover',
				'selector'  => '#trx-addons-woo-products-quick-view-{{ID}} .trx-addons-woo-products-atc-button button.button.alt:hover',
				'condition' => array(
					'qv_atc!' => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'      => 'qv_cta_border_hover',
				'selector'  => '#trx-addons-woo-products-quick-view-{{ID}} .trx-addons-woo-products-atc-button button.button.alt:hover',
				'condition' => array(
					'qv_atc!' => 'yes',
				),
			)
		);

		$this->add_control(
			'qv_cta_radius_hover',
			array(
				'label'      => __( 'Border Radius', 'trx_addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'#trx-addons-woo-products-quick-view-{{ID}} .trx-addons-woo-products-atc-button button.button.alt:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
				'condition'  => array(
					'qv_atc!' => 'yes',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'qv_cart_heading',
			array(
				'label'     => __( 'View Cart Text', 'trx_addons' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
                'condition' => array(
					'qv_atc!' => 'yes',
				),
			)
		);

		$this->add_control(
			'qv_cart_color',
			array(
				'label'     => __( 'Color', 'trx_addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'#trx-addons-woo-products-quick-view-{{ID}} .added_to_cart' => 'color: {{VALUE}}',
				),
				'condition' => array(
					'qv_atc!' => 'yes',
				),
			)
		);

		$this->add_control(
			'qv_cart_color_hover',
			array(
				'label'     => __( 'Hover Color', 'trx_addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'#trx-addons-woo-products-quick-view-{{ID}} .added_to_cart:hover' => 'color: {{VALUE}}',
				),
				'condition' => array(
					'qv_atc!' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'cart_spacing',
			array(
				'label'      => __( 'Margin', 'trx_addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'#trx-addons-woo-products-quick-view-{{ID}} .added_to_cart' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'  => array(
					'qv_atc!' => 'yes',
				),
			)
		);

        $this->add_control(
			'qv_meta_heading',
			array(
				'label' => __( 'Product Meta', 'trx_addons' ),
				'type'  => Controls_Manager::HEADING,
                'condition' => array(
					'qv_meta!' => 'yes',
				),
			)
		);

        $this->add_control(
			'qv_meta_name_color',
			array(
				'label'     => __( 'Meta Name Color', 'trx_addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'#trx-addons-woo-products-quick-view-{{ID}} .product_meta > span' => 'color: {{VALUE}};',
				),
                'condition' => array(
					'qv_meta!' => 'yes',
				),
			)
		);

        $this->add_control(
			'qv_meta_value_color',
			array(
				'label'     => __( 'Meta Value Color', 'trx_addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'#trx-addons-woo-products-quick-view-{{ID}} .product_meta > span span, .trx-addons-woo-products-quick-view-{{ID}} .product_meta a' => 'color: {{VALUE}};',
				),
                'condition' => array(
					'qv_meta!' => 'yes',
				),
			)
		);

        $this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'qv_meta_typography',
				'selector' => '#trx-addons-woo-products-quick-view-{{ID}} .product_meta > span',
                'condition' => array(
					'qv_meta!' => 'yes',
				),
			)
		);

        $this->add_responsive_control(
			'qv_meta_spacing',
			array(
				'label'      => __( 'Bottom Spacing', 'trx_addons' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'range'      => array(
					'em' => array(
						'min'  => 0,
						'max'  => 5,
						'step' => 0.1,
					),
				),
				'selectors'  => array(
					'#trx-addons-woo-products-quick-view-{{ID}} .product_meta > span' => 'margin-bottom: {{SIZE}}{{UNIT}}',
				),
				'condition'  => array(
					'qv_meta!' => 'yes',
				),
			)
		);

		$this->add_control(
			'qv_quantity_heading',
			array(
				'label' => __( 'Quantity Field', 'trx_addons' ),
				'type'  => Controls_Manager::HEADING,
				'separator'=> 'before',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'qv_quantitiy_border',
				'selector' => '#trx-addons-woo-products-quick-view-{{ID}} div.quantity .qty'
			)
		);

		$this->add_responsive_control(
			'qv_quantitiy_border_radius',
			array(
				'label'      => __( 'Border Radius', 'trx_addons' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'#trx-addons-woo-products-quick-view-{{ID}} div.quantity .qty' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Register Quick View Slider Style Controls.
	 *
	 * @return void
	 */
	public function register_quick_view_slider_style_controls() {

		$this->start_controls_section(
			'quick_view_slider_style',
			array(
				'label' => __( 'Quick View Carousel', 'trx_addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'qv_dots_heading',
			array(
				'label' => __( 'Dots', 'trx_addons' ),
				'type'  => Controls_Manager::HEADING,
			)
		);

		$this->add_control(
			'carousel_dot_color',
			array(
				'label'     => __( 'Color', 'trx_addons' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'#trx-addons-woo-products-quick-view-{{ID}} .flex-control-nav a' => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'carousel_dot_active_color',
			array(
				'label'     => __( 'Active Color', 'trx_addons' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'selectors' => array(
					'#trx-addons-woo-products-quick-view-{{ID}} a.flex-active' => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'qv_arrow_heading',
			array(
				'label' => __( 'Arrow', 'trx_addons' ),
				'type'  => Controls_Manager::HEADING,
			)
		);

		$this->add_control(
			'arrow_color',
			array(
				'label'     => __( 'Color', 'trx_addons' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'selectors' => array(
					'#trx-addons-woo-products-quick-view-{{ID}} .trx-addons-slider-arrow' => 'color: {{VALUE}} !important',
				),
			)
		);

		$this->add_responsive_control(
			'arrows_pos',
			array(
				'label'      => __( 'Position', 'trx_addons' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'range'      => array(
					'px' => array(
						'min' => -100,
						'max' => 100,
					),
					'em' => array(
						'min' => -10,
						'max' => 10,
					),
				),
				'selectors'  => array(
					'#trx-addons-woo-products-quick-view-{{ID}} .trx-addons-arrow-prev' => 'left: {{SIZE}}{{UNIT}};',
					'#trx-addons-woo-products-quick-view-{{ID}} .trx-addons-arrow-next' => 'right: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'carousel_arrow_size',
			array(
				'label'      => __( 'Size', 'trx_addons' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'#trx-addons-woo-products-quick-view-{{ID}} .trx-addons-slider-arrow svg' => 'height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'carousel_arrow_background',
			array(
				'label'     => __( 'Background Color', 'trx_addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'#trx-addons-woo-products-quick-view-{{ID}} .trx-addons-slider-arrow' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'carousel_border_radius',
			array(
				'label'      => __( 'Border Radius', 'trx_addons' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'#trx-addons-woo-products-quick-view-{{ID}} .trx-addons-slider-arrow' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'carousel_arrow_padding',
			array(
				'label'      => __( 'Padding', 'trx_addons' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'#trx-addons-woo-products-quick-view-{{ID}} .trx-addons-slider-arrow' => 'padding: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Get query products based on settings.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @param  mixed $ajax
	 * @return void
	 */
	public function render_query( $ajax = false ) {

		$settings = $this->parent->get_settings_for_display();

		if ( 'main' === $settings['query_type'] ) {

			if ( $ajax ) {

				$query_args = array(
					'post_type'      => 'product',
					'post_status'    => 'publish',
					'posts_per_page' => -1,
					'paged'          => 1,
				);

				if ( $settings['products_numbers'] > 0 ) {
					$query_args['posts_per_page'] = $settings['products_numbers'];
				}

				$paged = $this->get_paged();

				$nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : false;

				$orderby = 'menu_order title';

				if ( $nonce && wp_verify_nonce( $nonce, 'trx-addons-woo-products-widget-nonce' ) ) {
					if ( isset( $_POST['orderBy'] ) && '' !== $_POST['orderBy'] ) {
						$orderby = sanitize_text_field( wp_unslash( $_POST['orderBy'] ) );
					}
				}

				$query_args['paged']   = $paged;
				$query_args['orderby'] = $orderby;

				if ( isset( $_POST['category'] ) && '' !== $_POST['category'] ) {
					$query_args['product_cat'] = sanitize_text_field( wp_unslash( $_POST['category'] ) );
				}

				$query_args['order'] = 'ASC';

				self::$query_args = $query_args;

				self::$query = new \WP_Query( $query_args );

			} else {

				global $wp_query;

				$main_query = clone $wp_query;

				$query_args = apply_filters( 'trx_addons_woo_products_query_args', array_merge( $main_query->query_vars, array(
					'post_type'      => 'product',
					'post_status'    => 'publish',
					'posts_per_page' => -1,
					'paged'          => 1,
				) ), $settings );

				self::$query_args = $query_args;

				self::$query = new \WP_Query( $query_args );

			}
		} elseif ( 'related' === $settings['query_type'] ) {

			if ( is_product() ) {

				global $product;

				$product_id                  = $product->get_id();
				$product_visibility_term_ids = wc_get_product_visibility_term_ids();

				$query_args = array(
					'post_type'      => 'product',
					'post_status'    => 'publish',
					'posts_per_page' => -1,
					'paged'          => 1,
					'post__not_in'   => array(),
				);

				if ( 'grid' === $settings['layout_type'] || 'masonry' === $settings['layout_type'] ) {

					if ( $settings['products_numbers'] > 0 ) {
						$query_args['posts_per_page'] = $settings['products_numbers'];
					}

					if ( 'yes' === $settings['pagination'] || 'yes' === $settings['load_more'] ) {

						$paged = $this->get_paged();

						$query_args['paged'] = $paged;
					}
				} elseif ( $settings['total_carousel_products'] > 0 ) {

						$query_args['posts_per_page'] = $settings['total_carousel_products'];
				}

				// Get current post categories and pass to filter.
				$product_cat = array();

				$product_categories = wp_get_post_terms( $product_id, 'product_cat' );

				if ( ! empty( $product_categories ) ) {

					foreach ( $product_categories as $key => $category ) {

						$product_cat[] = $category->slug;
					}
				}

				if ( ! empty( $product_cat ) ) {

					$query_args['tax_query'][] = array(
						'taxonomy' => 'product_cat',
						'field'    => 'slug',
						'terms'    => $product_cat,
						'operator' => 'IN',
					);
				}

				// Exclude current product.
				$query_args['post__not_in'][] = $product_id;

				if ( 'yes' === get_option( 'woocommerce_hide_out_of_stock_items' ) ) {

					$query_args['tax_query'][] = array(
						'taxonomy' => 'product_visibility',
						'field'    => 'term_taxonomy_id',
						'terms'    => $product_visibility_term_ids['outofstock'],
						'operator' => 'NOT IN',
					);
				}

				if ( ! empty( $product_visibility_term_ids['exclude-from-catalog'] ) ) {

					$query_args['tax_query'][] = array(
						'taxonomy' => 'product_visibility',
						'field'    => 'term_taxonomy_id',
						'terms'    => $product_visibility_term_ids['exclude-from-catalog'],
						'operator' => 'NOT IN',
					);
				}

				$query_args = apply_filters( 'trx_addons_woo_products_query_args', $query_args, $settings );

				self::$query = new \WP_Query( $query_args );

			} else {

				$query_args = array(
					'post_type'      => 'product',
					'post_status'    => 'publish',
					'posts_per_page' => -1,
					'paged'          => 1,
					'post__in'       => array( 0 ),
				);

				$query_args = apply_filters( 'trx_addons_woo_products_query_args', $query_args, $settings );

				self::$query = new \WP_Query( $query_args );
			}
		} elseif ( 'cross-sells' === $settings['query_type'] ) {

			$cross_sells_ids = $this->get_cross_sells_ids();

			$product_visibility_term_ids = wc_get_product_visibility_term_ids();

			if ( ! $cross_sells_ids ) {
				$cross_sells_ids = array( 0 );
			}

			$query_args = array(
				'post_type'      => 'product',
				'post_status'    => 'publish',
				'posts_per_page' => -1,
				'paged'          => 1,
				'post__in'       => $cross_sells_ids,
			);

			/**
			 * Filters.
			 */

			// carousel.
			if ( 'grid' === $settings['layout_type'] || 'masonry' === $settings['layout_type'] ) {

				if ( $settings['products_numbers'] > 0 ) {
					$query_args['posts_per_page'] = $settings['products_numbers'];
				}
			} elseif ( $settings['total_carousel_products'] > 0 ) {

					$query_args['posts_per_page'] = $settings['total_carousel_products'];
			}

			// Default ordering args.
			$ordering_args = WC()->query->get_catalog_ordering_args( $settings['orderby'], $settings['order'] );

			$query_args['orderby'] = $ordering_args['orderby'];
			$query_args['order']   = $ordering_args['order'];

			if ( 'yes' === get_option( 'woocommerce_hide_out_of_stock_items' ) ) {

				$query_args['tax_query'][] = array(
					'taxonomy' => 'product_visibility',
					'field'    => 'term_taxonomy_id',
					'terms'    => $product_visibility_term_ids['outofstock'],
					'operator' => 'NOT IN',
				);
			}

			if ( ! empty( $product_visibility_term_ids['exclude-from-catalog'] ) ) {

				$query_args['tax_query'][] = array(
					'taxonomy' => 'product_visibility',
					'field'    => 'term_taxonomy_id',
					'terms'    => $product_visibility_term_ids['exclude-from-catalog'],
					'operator' => 'NOT IN',
				);
			}

			$query_args = apply_filters( 'trx_addons_woo_products_query_args', $query_args, $settings );

			self::$query = new \WP_Query( $query_args );

		} elseif ( 'up-sells' === $settings['query_type'] ) {

			/**
			 * Up-sells are products that you recommend instead of the currently viewed product.
			 */
			if ( is_product() ) {

				global $product;

				$product_upsell = 0 === count( $product->get_upsell_ids() ) ? array( 0 ) : $product->get_upsell_ids();

				$product_visibility_term_ids = wc_get_product_visibility_term_ids();

				$query_args = array(
					'post_type'      => 'product',
					'post_status'    => 'publish',
					'posts_per_page' => -1,
					'paged'          => 1,
					'post__in'       => $product_upsell,
				);

				/**
				 * Filters.
				 */

				// carousel.
				if ( 'grid' === $settings['layout_type'] || 'masonry' === $settings['layout_type'] ) {

					if ( $settings['products_numbers'] > 0 ) {
						$query_args['posts_per_page'] = $settings['products_numbers'];
					}
				} elseif ( $settings['total_carousel_products'] > 0 ) {

						$query_args['posts_per_page'] = $settings['total_carousel_products'];
				}

				// Default ordering args.
				$ordering_args = WC()->query->get_catalog_ordering_args( $settings['orderby'], $settings['order'] );

				$query_args['orderby'] = $ordering_args['orderby'];
				$query_args['order']   = $ordering_args['order'];

				if ( 'yes' === get_option( 'woocommerce_hide_out_of_stock_items' ) ) {

					$query_args['tax_query'][] = array(
						'taxonomy' => 'product_visibility',
						'field'    => 'term_taxonomy_id',
						'terms'    => $product_visibility_term_ids['outofstock'],
						'operator' => 'NOT IN',
					);
				}

				if ( ! empty( $product_visibility_term_ids['exclude-from-catalog'] ) ) {

					$query_args['tax_query'][] = array(
						'taxonomy' => 'product_visibility',
						'field'    => 'term_taxonomy_id',
						'terms'    => $product_visibility_term_ids['exclude-from-catalog'],
						'operator' => 'NOT IN',
					);
				}

				$query_args = apply_filters( 'trx_addons_woo_products_query_args', $query_args, $settings );

				self::$query = new \WP_Query( $query_args );

			} else {

				$query_args = array(
					'post_type'      => 'product',
					'post_status'    => 'publish',
					'posts_per_page' => -1,
					'paged'          => 1,
					'post__in'       => array( 0 ),
				);

				$query_args = apply_filters( 'trx_addons_woo_products_query_args', $query_args, $settings );

				self::$query = new \WP_Query( $query_args );
			}
		} else {

			global $post;

			$product_visibility_term_ids = wc_get_product_visibility_term_ids();

			$query_args = array(
				'post_type'      => 'product',
				'post_status'    => 'publish',
				'posts_per_page' => -1,
				'paged'          => 1,
				'post__not_in'   => array(),
			);

			if ( 'grid' === $settings['layout_type'] || 'masonry' === $settings['layout_type'] ) {

				if ( $settings['products_numbers'] > 0 ) {
					$query_args['posts_per_page'] = $settings['products_numbers'];
				}

				if ( 'yes' === $settings['pagination'] || 'yes' === $settings['load_more'] ) {

					$paged = $this->get_paged();

					$query_args['paged'] = $paged;
				}
			} elseif ( $settings['total_carousel_products'] > 0 ) {

					$query_args['posts_per_page'] = $settings['total_carousel_products'];
			}

			// Default ordering args.
			$ordering_args = WC()->query->get_catalog_ordering_args( $settings['orderby'], $settings['order'] );

			$query_args['orderby'] = $ordering_args['orderby'];
			$query_args['order']   = $ordering_args['order'];

			if ( $ordering_args['meta_key'] ) {
				$query_args['meta_key'] = $ordering_args['meta_key'];
			}

			if ( 'sale' === $settings['filter_by'] ) {

				$query_args['post__in'] = array_merge( array( 0 ), wc_get_product_ids_on_sale() );
			} elseif ( 'featured' === $settings['filter_by'] ) {

				$query_args['tax_query'][] = array(
					'taxonomy' => 'product_visibility',
					'field'    => 'term_taxonomy_id',
					'terms'    => $product_visibility_term_ids['featured'],
				);
			}

			if ( 'custom' === $settings['query_type'] ) {

				if ( ! empty( $settings['categories'] ) ) {

					$cat_rule = $settings['categories_filter_rule'];

					$query_args['tax_query'][] = array(
						'taxonomy' => 'product_cat',
						'field'    => 'slug',
						'terms'    => $settings['categories'],
						'operator' => $cat_rule,
					);
				}

				if ( ! empty( $settings['tags'] ) ) {

					$tag_rule = $settings['tags_filter_rule'];

					$query_args['tax_query'][] = array(
						'taxonomy' => 'product_tag',
						'field'    => 'slug',
						'terms'    => $settings['tags'],
						'operator' => $tag_rule,
					);
				}

				if ( ! empty( $settings['products'] ) ) {
					$query_args[ $settings['product_filter_rule'] ] = $settings['products'];
				}

				if ( 0 < $settings['offset'] ) {

					$query_args['offset_to_fix'] = $settings['offset'];
				}
			}

			if ( 'manual' === $settings['query_type'] ) {

				$manual_ids = $settings['query_manual_ids'];

				$query_args['post__in'] = $manual_ids;
			}

			if ( 'manual' !== $settings['query_type'] && 'main' !== $settings['query_type'] ) {

				// if ( '' !== $settings['exclude_products'] ) {

				// $exclude_ids = $settings['exclude_products'];

				// $query_args['post__not_in'] = $exclude_ids;
				// }

				if ( 'yes' === $settings['exclude_current_product'] ) {

					$query_args['post__not_in'][] = $post->ID;
				}
			}

			if ( 'yes' === get_option( 'woocommerce_hide_out_of_stock_items' ) ) {

				$query_args['tax_query'][] = array(
					'taxonomy' => 'product_visibility',
					'field'    => 'term_taxonomy_id',
					'terms'    => $product_visibility_term_ids['outofstock'],
					'operator' => 'NOT IN',
				);
			}

			if ( ! empty( $product_visibility_term_ids['exclude-from-catalog'] ) ) {

				$query_args['tax_query'][] = array(
					'taxonomy' => 'product_visibility',
					'field'    => 'term_taxonomy_id',
					'terms'    => $product_visibility_term_ids['exclude-from-catalog'],
					'operator' => 'NOT IN',
				);
			}

			$query_args = apply_filters( 'trx_addons_woo_products_query_args', $query_args, $settings );

			self::$query_args = $query_args;

			self::$query = new \WP_Query( $query_args );

		}
	}

	/**
	 * Get empty products found message.
	 *
	 * Returns the no products found message HTML.
	 *
	 * @return void
	 */
	public function render_empty() {
		$settings = $this->parent->get_settings_for_display();

		?>
		<div class="trx-addons-woo-products-empty">
			<p><?php echo esc_html( $settings['empty_products_msg'] ); ?></p>
		</div>
		<?php
	}

	/**
	 * Render Editor Masonry Script.
	 *
	 * @return void
	 */
	protected function render_editor_script() {

		?>
		<script type="text/javascript">
			jQuery( document ).ready( function( $ ) {

				$( '.trx-addons-woo-products-masonry .products' ).each( function() {

					var selector 	= $(this);


					if ( selector.closest( '.trx-addons-woo-products' ).length < 1 ) {
						return;
					}


					var masonryArgs = {
						itemSelector	: 'li.product',
						percentPosition : true,
						layoutMode		: 'masonry',
					};

					var $isotopeObj = {};

					selector.imagesLoaded( function() {

						$isotopeObj = selector.isotope( masonryArgs );

						$isotopeObj.imagesLoaded().progress(function() {
							$isotopeObj.isotope("layout");
						});

						selector.find('li.product').resize( function() {
							$isotopeObj.isotope( 'layout' );
						});
					});

				});
			});
		</script>
		<?php
	}

	/**
	 * Register Get Query.
	 *
	 * @return void
	 */
	public function get_query() {
		return self::$query;
	}

	/**
	 * Render loop required arguments.
	 *
	 * @return void
	 */
	public function set_query_args() {
		$query = $this->get_query();

		global $woocommerce_loop;

		$settings = $this->parent->get_settings_for_display();

		if ( 'grid' === $settings['layout_type'] || 'masonry' === $settings['layout_type'] ) {

			$woocommerce_loop['columns'] = intval( 100 / substr( $settings['columns'], 0, strpos( $settings['columns'], '%' ) ) );

			if ( '16.667%' === $settings['columns'] ) {
				$woocommerce_loop['columns'] = 6;
			}

			if ( 0 < $settings['products_numbers'] && '' !== $settings['pagination'] ) {
				/* Pagination */
				$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;

				$nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : false;

				if ( $nonce && wp_verify_nonce( $nonce, 'trx-addons-woo-products-widget-nonce' ) ) {
					if ( isset( $_POST['page_number'] ) && '' !== $_POST['page_number'] ) {
						$paged = sanitize_text_field( wp_unslash( $_POST['page_number'] ) );
					}
				}

				$woocommerce_loop['paged']        = $paged;
				$woocommerce_loop['total']        = $query->found_posts;
				$woocommerce_loop['post_count']   = $query->post_count;
				$woocommerce_loop['per_page']     = $settings['products_numbers'];
				$woocommerce_loop['total_pages']  = ceil( $query->found_posts / $settings['products_numbers'] );
				$woocommerce_loop['current_page'] = $paged;
			}

			$divider = $this->get_instance_value( 'divider' );

			if ( 'yes' === $divider && 'grid' === $settings['layout_type'] ) {
				$this->add_render_attribute( 'wrapper', 'class', 'trx-addons-woo-products-grid-' . $woocommerce_loop['columns'] );
			} elseif ( 'masonry' === $settings['layout_type'] ) {
				$this->add_render_attribute( 'wrapper', 'class', 'trx-addons-woo-products-masonry-' . $woocommerce_loop['columns'] );
			}
		}
	}

	/**
	 * Add render attribute.
	 *
	 * Used to add attributes to a specific HTML element.
	 *
	 * The HTML tag is represented by the element parameter, then you need to
	 * define the attribute key and the attribute key. The final result will be:
	 * `<element attribute_key="attribute_value">`.
	 *
	 * Example usage:
	 *
	 * `$this->add_render_attribute( 'wrapper', 'class', 'custom-widget-wrapper-class' );`
	 * `$this->add_render_attribute( 'widget', 'id', 'custom-widget-id' );`
	 * `$this->add_render_attribute( 'button', [ 'class' => 'custom-button-class', 'id' => 'custom-button-id' ] );`
	 *
	 *
	 * @param array|string $element   The HTML element.
	 * @param array|string $key       Optional. Attribute key. Default is null.
	 * @param array|string $value     Optional. Attribute value. Default is null.
	 * @param bool         $overwrite Optional. Whether to overwrite existing
	 *                                attribute. Default is false, not to overwrite.
	 *
	 * @return Element_Base Current instance of the element.
	 */
	public function add_render_attribute( $element, $key = null, $value = null, $overwrite = false ) {
		if ( is_array( $element ) ) {
			foreach ( $element as $element_key => $attributes ) {
				$this->add_render_attribute( $element_key, $attributes, null, $overwrite );
			}

			return $this;
		}

		if ( is_array( $key ) ) {
			foreach ( $key as $attribute_key => $attributes ) {
				$this->add_render_attribute( $element, $attribute_key, $attributes, $overwrite );
			}

			return $this;
		}

		if ( empty( $this->_render_attributes[ $element ][ $key ] ) ) {
			$this->_render_attributes[ $element ][ $key ] = array();
		}

		settype( $value, 'array' );

		if ( $overwrite ) {
			$this->_render_attributes[ $element ][ $key ] = $value;
		} else {
			$this->_render_attributes[ $element ][ $key ] = array_merge( $this->_render_attributes[ $element ][ $key ], $value );
		}

		return $this;
	}

	/**
	 * Get render attribute string.
	 *
	 * Used to retrieve the value of the render attribute.
	 *
	 * @access public
	 *
	 * @param array|string $element The element.
	 *
	 * @return string Render attribute string, or an empty string if the attribute
	 *                is empty or not exist.
	 */
	public function get_render_attribute_string( $element ) {
		if ( empty( $this->_render_attributes[ $element ] ) ) {
			return '';
		}

		$render_attributes = $this->_render_attributes[ $element ];

		$attributes = array();

		foreach ( $render_attributes as $attribute_key => $attribute_values ) {
			$attributes[] = sprintf( '%1$s="%2$s"', $attribute_key, esc_attr( implode( ' ', $attribute_values ) ) );
		}

		return implode( ' ', $attributes );
	}

	/**
	 * Get Wrapper Classes.
	 *
	 * @since 4.7.0
	 * @access public
	 */
	public function set_slider_attr() {
		$settings = $this->parent->get_settings_for_display();

		if ( 'carousel' !== $settings['layout_type'] ) {
			return;
		}

		$is_rtl = is_rtl();

		$slider_options = [
			'direction'             => 'horizontal',
			'speed'                 => ( $settings['speed'] ) ? absint( $settings['speed'] ) : 500,
			'slidesPerView'         => ( $settings['products_show'] ) ? absint( $settings['products_show'] ) : 4,
			'slidesPerGroup'        => ( $settings['products_on_scroll'] ) ? absint( $settings['products_on_scroll'] ) : 1,
			'watchSlidesVisibility' => true,
			'loop'                  => ( 'yes' === $settings['infinite_loop'] ),
		];

		if ( 'yes' === $settings['autoplay_slides'] ) {
			$autoplay_speed = ( $settings['autoplay_speed'] ) ? absint( $settings['autoplay_speed'] ) : 999999;
		} else {
			$autoplay_speed = 999999;
		}

		$slider_options['autoplay'] = [
			'delay'                => $autoplay_speed,
			'pauseOnHover'         => ( 'yes' === $settings['hover_pause'] ),
			'disableOnInteraction' => ( 'yes' === $settings['hover_pause'] ),
		];

		if ( 'yes' === $settings['dots'] ) {
			$slider_options['pagination'] = [
				'el'                 => '.swiper-pagination-' . esc_attr( $this->parent->get_id() ),
				'clickable'          => true,
			];
		}

		if ( 'yes' === $settings['arrows'] ) {
			$slider_options['navigation'] = [
				'nextEl'             => '.swiper-button-next-' . esc_attr( $this->parent->get_id() ),
				'prevEl'             => '.swiper-button-prev-' . esc_attr( $this->parent->get_id() ),
			];
		}

		$slider_options['breakpoints'] = array();

		$slider_options['breakpoints'][1024] = array(
			'slidesPerView'  => ( $settings['products_show'] ) ? absint( $settings['products_show'] ) : 4,
			'slidesPerGroup' => ( $settings['products_on_scroll'] ) ? absint( $settings['products_on_scroll'] ) : 1,
		);

		if ( $settings['products_show_tablet'] || $settings['products_show_mobile'] ) {

			if ( $settings['products_show_tablet'] ) {

				$tablet_show   = absint( $settings['products_show_tablet'] );
				$tablet_scroll = ( $settings['products_on_scroll_tablet'] ) ? absint( $settings['products_on_scroll_tablet'] ) : $tablet_show;

				$slider_options['breakpoints'][768] = array(
					'slidesPerView'  => $tablet_show,
					'slidesPerGroup' => $tablet_scroll,
				);
			}

			if ( $settings['products_show_mobile'] ) {

				$mobile_show   = absint( $settings['products_show_mobile'] );
				$mobile_scroll = ( $settings['products_on_scroll_mobile'] ) ? absint( $settings['products_on_scroll_mobile'] ) : $mobile_show;

				$slider_options['breakpoints'][320] = array(
					'slidesPerView'  => $mobile_show,
					'slidesPerGroup' => $mobile_scroll,
				);
			}
		}

		$this->add_render_attribute(
			'wrapper',
			array(
				// 'class'             => 'premium-carousel-hidden',
				'data-slider-settings' => wp_json_encode( $slider_options ),
			)
		);
	}

	
	/**
	 * Render team member carousel dots output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @access protected
	 */
	protected function render_dots() {
		$settings = $this->parent->get_settings_for_display();

		if ( 'carousel' !== $settings['layout_type'] ) {
			return;
		}

		if ( 'yes' === $settings['dots'] ) {
			?>
			<!-- Add Pagination -->
			<div class="swiper-pagination swiper-pagination-<?php echo esc_attr( $this->parent->get_id() ); ?>"></div>
			<?php
		}
	}

	/**
	 * Render carousel arrows output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @access protected
	 */
	protected function render_arrows() {
		$settings          = $this->parent->get_settings_for_display();
		$skin              = $this->get_id();
		$layout            = $settings['layout_type'];
		$arrows            = $settings['arrows'];
		$select_arrow_icon = $settings['select_arrow_icon'];

		if ( 'carousel' !== $layout ) {
			return;
		}

		if ( ! isset( $settings['arrow_icon'] ) && ! Icons_Manager::is_migration_allowed() ) {
			// add old default.
			$settings['arrow_icon'] = 'fa fa-angle-right';
		}

		$has_icon = ! empty( $settings['arrow_icon'] );

		if ( ! $has_icon && ! empty( $select_arrow_icon['value'] ) ) {
			$has_icon = true;
		}

		if ( ! empty( $settings['arrow'] ) ) {
			$this->parent->add_render_attribute( 'arrow-icon', 'class', $settings['arrow_icon'] );
			$this->parent->add_render_attribute( 'arrow-icon', 'aria-hidden', 'true' );
		}

		$migrated = isset( $settings['__fa4_migrated']['select_arrow_icon'] );
		$is_new   = ! isset( $settings['arrow_icon'] ) && Icons_Manager::is_migration_allowed();

		if ( 'yes' === $arrows ) {
			if ( $has_icon ) {
				if ( $is_new || $migrated ) {
					$next_arrow_icon = $select_arrow_icon;
					$prev_arrow_icon = str_replace( 'right', 'left', $select_arrow_icon );
				} else {
					$next_arrow_icon = $settings['arrow'];
					$prev_arrow_icon = str_replace( 'right', 'left', $arrow );
				}
			} else {
				$next_arrow_icon = 'fa fa-angle-right';
				$prev_arrow_icon = 'fa fa-angle-left';
			}

			if ( ! empty( $settings['arrow_icon'] ) || ( ! empty( $select_arrow_icon['value'] ) && $is_new ) ) { ?>
				<div class="trx-addons-slider-arrow trx-addons-arrow-prev trx-addons-woo-products-prime-arrow-prev elementor-swiper-button-prev swiper-button-prev-<?php echo esc_attr( $this->parent->get_id() ); ?>">
					<?php if ( $is_new || $migrated ) :
						Icons_Manager::render_icon( $prev_arrow_icon, [ 'aria-hidden' => 'true' ] );
					else : ?>
						<i <?php $this->parent->print_render_attribute_string( 'arrow-icon' ); ?>></i>
					<?php endif; ?>
				</div>
				<div class="trx-addons-slider-arrow trx-addons-arrow-next trx-addons-woo-products-prime-arrow-next elementor-swiper-button-next swiper-button-next-<?php echo esc_attr( $this->parent->get_id() ); ?>">
					<?php if ( $is_new || $migrated ) :
						Icons_Manager::render_icon( $next_arrow_icon, [ 'aria-hidden' => 'true' ] );
					else : ?>
						<i <?php $this->parent->print_render_attribute_string( 'arrow-icon' ); ?>></i>
					<?php endif; ?>
				</div>
			<?php }
		}
	}

	/**
	 * Render wrapper start.
	 *
	 * @return void
	 */
	public function start_loop_wrapper() {

		$settings   = $this->parent->get_settings_for_display();
		$quick_view = $this->get_instance_value( 'quick_view' );
		$skin       = $this->get_id();
		$skin_slug  = str_replace( '_', '-', $skin );

		$page_id = 0;

		if ( null !== \Elementor\Plugin::$instance->documents->get_current() ) {
			$page_id = \Elementor\Plugin::$instance->documents->get_current()->get_main_id();
		}

		$this->set_slider_attr();

		$classes = array(
			'trx-addons-woo-products',
			'trx-addons-woo-products-' . $settings['layout_type'],
			'trx-addons-woo-products-skin-' . $skin_slug,
			'trx-addons-woo-products-query-' . $settings['query_type'],
		);

		if ( 'carousel' === $settings['layout_type'] ) {
			$classes[] = 'swiper-container-wrap swiper';

			if ( $settings['dots_position'] ) {
				$classes[] = 'swiper-container-wrap-dots-' . $settings['dots_position'];
			}
		}

		$this->add_render_attribute(
			'wrapper',
			array(
				'class'           => $classes,
				'data-page-id'    => $page_id,
				'data-skin'       => $skin,
				'data-quick-view' => $quick_view,
			)
		);

		?>
		<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'wrapper' ) ); ?> >
		<?php
	}

	/**
	 * Render wrapper end.
	 *
	 * @return void
	 */
	public function end_loop_wrapper() {
		?>
		</div>
		<?php
	}

	/**
	 * Render inner container start.
	 *
	 * @return void
	 */
	public function start_loop_inner() {
		$settings = $this->parent->get_settings_for_display();

		$this->add_render_attribute(
			'inner',
			array(
				'class' => array(
					'trx-addons-woo-products-inner',
				),
			)
		);

		if ( '' !== $settings['hover_style'] ) {
			$this->add_render_attribute(
				'inner',
				array(
					'class' => array(
						'trx-addons-woo-products-product__hover-' . $settings['hover_style'],
					),
				)
			);
		}

		if ( 'carousel' === $settings['layout_type'] ) {
			$this->add_render_attribute(
				'inner',
				array(
					'class' => array(
						'trx-addons-woo-products-swiper-outer',
					),
				)
			);
		}

		?>
		<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'inner' ) ); ?> >
		<?php
	}

	/**
	 * Render inner container end.
	 *
	 * @since 1.1.0
	 */
	public function end_loop_inner() {
		?>
		</div>
		<?php
	}

	/**
	 * render_details_wrap_start
	 *
	 * @return void
	 */
	public function render_details_wrap_start() {

		global $product;

		$product_id = $product->get_id();
		$settings   = $this->parent->get_settings_for_display();

		do_action( 'trx_addons_woo_products_product_before_details_wrap_start', $product_id, $settings );
		?>
		<div class="trx-addons-woo-products-details-wrap trx-addons-products-info-box">
		<?php
		do_action( 'trx_addons_woo_products_product_after_details_wrap_start', $product_id, $settings );
	}

	/**
	 * render_details_wrap_end
	 *
	 * @return void
	 */
	public function render_details_wrap_end() {

		global $product;

		$product_id = $product->get_id();
		$settings   = $this->parent->get_settings_for_display();

		do_action( 'trx_addons_woo_products_product_before_details_wrap_end', $product_id, $settings );
		?>
		</div>
		<?php
		do_action( 'trx_addons_woo_products_product_after_details_wrap_end', $product_id, $settings );
	}

	/**
	 * Render woo loop.
	 */
	public function render_woo_products() {

		$settings    = $this->parent->get_settings_for_display();
		$layout_type = $settings['layout_type'];

		$query = $this->get_query();

		woocommerce_product_loop_start();

		if ( 'carousel' === $layout_type ) {
			trx_addons_enqueue_slider();
			?>
			<div class="swiper-container trx-addons-woo-products-carousel trx-addons-swiper-slider">
				<div class="swiper-wrapper">
			<?php
		}

		while ( $query->have_posts() ) :
			$query->the_post();

			if ( 'carousel' === $layout_type ) {
				?><div class="swiper-slide"><?php
			}

			$this->render_product_template();

			if ( 'carousel' === $layout_type ) {
				?></div><?php
			}
		endwhile;

		if ( 'carousel' === $layout_type ) {
			?>
				</div>
			</div>
			<?php
		}

		$this->render_dots();

		$this->render_arrows();

		woocommerce_product_loop_end();
	}

	/**
	 * Render reset loop.
	 */
	public function render_reset_loop() {

		woocommerce_reset_loop();

		wp_reset_postdata();
	}

	/**
	 * Pagination Structure.
	 */
	public function render_pagination_structure() {

		$settings          = $this->parent->get_settings_for_display();
		$is_recommendation = in_array( $settings['query_type'], array( 'cross-sells', 'up-sells' ), true ) ? true : false;

		if ( 'yes' === $settings['pagination'] && ! $is_recommendation ) {
			add_action( 'trx_addons_woo_products_pagination_render', array( $this, 'woo_pagination_template' ), 10 );
			do_action( 'trx_addons_woo_products_pagination_render' );
		}
	}

	/**
	 * Change pagination arguments based on settings.
	 */
	public function woo_pagination_template() {

		$settings = $this->parent->get_settings_for_display();

		$total   = isset( $total ) ? $total : wc_get_loop_prop( 'total_pages' );
		// $current = isset( $current ) ? $current : wc_get_loop_prop( 'current_page' );
		$base    = isset( $base ) ? $base : esc_url_raw( str_replace( 999999999, '%#%', remove_query_arg( 'add-to-cart', get_pagenum_link( 999999999, false ) ) ) );
		$format  = isset( $format ) ? $format : '';

		$current = $this->get_paged();

		if ( $total <= 1 ) {
			return;
		}

		?>
		<nav class="trx-addons-woo-products-pagination">
			<?php
				echo wp_kses_post(
					paginate_links(
						apply_filters(
							'trx_addons_woo_products_pagination_args',
							array( // WPCS: XSS ok.
								'base'      => $base,
								'format'    => $format,
								'add_args'  => false,
								'current'   => max( 1, $current ),
								'total'     => $total,
								'prev_text' => ( '' !== $settings['prev_string'] ) ? $settings['prev_string'] : '&larr;',
								'next_text' => ( '' !== $settings['next_string'] ) ? $settings['next_string'] : '&rarr;',
								'type'      => 'list',
								'end_size'  => 3,
								'mid_size'  => 3,
								'prev_next' => ( 'numbers_arrow' === $settings['pagination_type'] ),
							)
						)
					)
				);
				?>
		</nav>
		<?php
	}

	/**
	 * Render Load More Button
	 */
	public function render_load_more_button() {

		$settings = $this->parent->get_settings_for_display();

		if ( 'yes' !== $settings['load_more'] || 'carousel' == $settings['layout_type'] ) {
			return;
		}

		$posts_per_page = self::$query_args['posts_per_page'];

		$args    = self::$query_args;
		$orderby = $args['orderby'];

		if ( 'main' === $settings['query_type'] ) {

			$args = array(
				'post_type'   => 'product',
				'product_cat' => $args['product_cat'],
			);

		}

		$args['posts_per_page'] = -1;

		$all_products = new \WP_Query( $args );

		if ( ! isset( $all_products->found_posts ) ) {
			return;
		}

		$more_products = $all_products->found_posts - $posts_per_page;

		$category = isset( $args['product_cat'] ) && ! empty( $args['product_cat'] ) ? $args['product_cat'] : '';

		if ( $more_products < 1 ) {
			return;
		}

		?>
			<div class="trx-addons-woo-products-load-more">
				<button class="trx-addons-woo-products-load-more-btn" data-products="<?php esc_attr_e( $more_products ); ?>" data-order="<?php esc_attr_e( $orderby ); ?>" data-tax="<?php esc_attr_e( $category ); ?>">
					<span><?php echo wp_kses_post( $settings['load_more_text'] ); ?></span>
					<span class="trx-addons-woo-products-num">(<?php echo wp_kses_post( $more_products ); ?>)</span>
				</button>
			</div>
		<?php
	}

	/**
	 * Quick View.
	 *
	 * @access public
	 */
	public function quick_view_modal() {

		$quick_view = $this->get_instance_value( 'quick_view' );

		if ( 'yes' === $quick_view ) {
			wp_enqueue_script( 'wc-add-to-cart-variation' );

			$widget_id = $this->parent->get_id();

			?>
			<div id="trx-addons-woo-products-quick-view-<?php esc_attr_e( $widget_id ); ?>" class="trx-addons-woo-products-quick-view-<?php esc_attr_e( $widget_id ); ?>">
				<div class="trx-addons-woo-products-quick-view-back">
					<div class="trx-addons-woo-products-quick-view-loader"></div>
				</div>
				<div id="trx-addons-woo-products-quick-view-modal">
					<div class="trx-addons-woo-products-content-main-wrapper"><?php /*Don't remove this html comment*/ ?><!--
					--><div class="trx-addons-woo-products-content-main">
							<div class="trx-addons-woo-products-lightbox-content">

								<a href="#" class="trx-addons-woo-products-quick-view-close fa fa-close"></a>

								<div id="trx-addons-woo-products-quick-view-content" class="woocommerce single-product"></div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<?php
		}
	}

	/**
	 * Render output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @param string $style Skin ID.
	 * @param array  $settings Settings Object.
	 * @param string $node_id Node ID.
	 * @since 4.7.0
	 * @access public
	 */
	public function render_skin() {

		$style    = $this->get_id();
		$settings = $this->parent->get_settings_for_display();
		$node_id  = $this->parent->get_id();

		$this->render_query();

		$query = self::$query;

		if ( ! $query->have_posts() ) {
			$this->render_empty();
			return;
		}

		if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {

			if ( 'masonry' === $settings['layout_type'] ) {
				$this->render_editor_script();
			}
		}

		$this->set_query_args();

		$this->start_loop_wrapper();

		$this->start_loop_inner();

		$this->render_woo_products();

		$this->render_pagination_structure();

		$this->render_load_more_button();

		$this->render_reset_loop();

		$this->end_loop_inner();

		$this->end_loop_wrapper();

		$this->quick_view_modal();
	}

	/**
	 * Render Main HTML.
	 *
	 * @access protected
	 */
	public function render() {
		echo wp_kses_post( sanitize_text_field( $this->render_skin() ) );
    }
	
	/**
	 * woo_loop_sale_ribbon
	 *
	 * @return void
	 */
	public function woo_loop_sale_ribbon() {

		$settings = $this->parent->get_settings_for_display();
		
		global $post, $product;

		$product_type = $product->get_type();

		$sale_string = __( 'Sale!', 'trx_addons' );

		if ( 'custom' === $settings['sale_type'] ) {

			$original_price = 'variable' === $product_type ? $product->get_variation_regular_price() : $product->get_regular_price();

			$sale_price = 'variable' === $product_type ? $product->get_variation_sale_price() : $product->get_sale_price();

			if ( $sale_price ) {
				$sale_string  = $settings['sale_string'];
				$sale_amount  = $original_price - $sale_price;
				$percent_sale = round( ( ( ( $sale_amount ) / $original_price ) * 100 ), 0 );
				$sale_string  = $sale_string ? $sale_string : '-[value]%';
				$sale_string  = str_replace( array( '[value]', '[sale]' ), array( $percent_sale, $sale_amount ), $sale_string );
			}
		}

		if ( $product->is_on_sale() ) : ?>
			<div class="trx-addons-woo-products-product-sale-wrap">
				<span class="trx-addons-woo-products-product-onsale"><?php echo esc_html( $sale_string ); ?> </span>
			</div>
		<?php
		endif;
	}
	
	/**
	 * woo_loop_featured_ribbon
	 *
	 * @return void
	 */
	public function woo_loop_featured_ribbon() {

		$settings = $this->parent->get_settings_for_display();

		global $post, $product;

		$featured_text = __( 'New', 'trx_addons' );

		if ( '' !== $settings['featured_string'] ) {
			$featured_text = $settings['featured_string'];
		}

		?>
		<?php if ( $product->is_featured() ) : ?>
			<div class="trx-addons-woo-products-product-featured-wrap">
				<span class="trx-addons-woo-products-product-featured"><?php echo esc_html( $featured_text ); ?></span>
			</div>
			<?php
		endif;
	}

	/**
	 * Get Current Product Swap Image.
	 *
	 * @access public
	 *
	 * @param string $size image size.
	 */
	public static function get_current_product_swap_image( $size ) {

		global $product;

		$attachment_ids = $product->get_gallery_image_ids();

		if ( $attachment_ids ) {

			$image_size = apply_filters( 'single_product_archive_thumbnail_size', $size );

			echo wp_kses_post( apply_filters( 'trx_addons_woo_product_swap_image', wp_get_attachment_image( reset( $attachment_ids ), $image_size, false, array( 'class' => 'trx-addons-woo-products-product__on_hover' ) ) ) );
		}
	}

	/**
	 * Get Product Short Description
	 *
	 * @access public
	 *
	 * @param integer $length excerpt length.
	 */
	public static function get_product_excerpt( $length ) {

		if ( has_excerpt() ) {

			$excerpt = trim( get_the_excerpt() );

			if ( ! empty( $length ) ) {

				$words = explode( ' ', $excerpt, $length + 1 );

				if ( count( $words ) > $length ) {

					array_pop( $words );

					array_push( $words, '…' );

				}

				$excerpt = implode( ' ', $words );

			}

			?>
			<div class="trx-addons-woo-products-product-desc">
				<?php echo wp_kses_post( $excerpt ); ?>
			</div>
			<?php
		}
	}

	/**
	 * Get Current Product Category
	 *
	 * @access public
	 */
	public static function get_current_product_category() {
		if ( apply_filters( 'trx_addons_woo_products_product_parent_category', true ) ) :
			?>
			<span class="trx-addons-woo-products-product-category">
				<?php
					global $product;
					$product_categories = function_exists( 'wc_get_product_category_list' ) ? wc_get_product_category_list( get_the_ID(), '&', '', '' ) : $product->get_categories( '&', '', '' );

					$product_categories = wp_strip_all_tags( $product_categories );

				if ( $product_categories ) {
					list( $parent_cat ) = explode( '&', $product_categories );

					echo esc_html( $parent_cat );
				}
				?>
			</span>
			<?php
		endif;
	}
	
	/**
	 * render_ribbon_container
	 *
	 * @return void
	 */
	public function render_ribbon_container() {
		global $product;

		$product_id      = $product->get_id();
		$settings        = $this->parent->get_settings_for_display();
		$sale_ribbon     = $settings['sale'];
        $featured_ribbon = $settings['featured'];
		$out_of_stock    = 'outofstock' === get_post_meta( $product_id, '_stock_status', true ) && 'yes' === $settings['sold_out'];

		if ( $out_of_stock ) {
			?>
			<span class="trx-addons-woo-products-out-of-stock">
				<?php esc_html_e( $settings['sold_out_string'] ); ?>
			</span>
			<?php
		} elseif ( $product->is_on_sale() || $product->is_featured() ) {
			?>
			<div class="trx-addons-woo-products-ribbon-container">
				<?php
				if ( 'yes' === $sale_ribbon ) {
					$this->woo_loop_sale_ribbon();
				}

				if ( 'yes' === $featured_ribbon ) {
					$this->woo_loop_featured_ribbon();
				}
				?>
			</div>
			<?php
		}
	}

	/**
	 * render_product_image
	 *
	 * @return void
	 */
	public function render_product_image() {
		global $product;

		$product_id = $product->get_id();
		$settings   = $this->parent->get_settings_for_display();
		$image_size = $settings['featured_image_size'];

		if ( 'yes' === $this->get_instance_value( 'product_image' ) ) {

			$thumb_id = get_post_thumbnail_id( $product_id );
			$alt      = get_post_meta( $thumb_id, '_wp_attachment_image_alt', true );

			$product_thumb = has_post_thumbnail( $product_id ) ? get_the_post_thumbnail_url( $product_id, $image_size ) : wc_placeholder_img_src( $image_size );
			?>
			<img class="trx-addons-woo-products-product-prime-image" src="<?php echo esc_url( $product_thumb ); ?>" alt="<?php esc_attr_e( $alt ); ?>">
			<?php

			if ( 'swap' === $settings['hover_style'] ) {
				$this->get_current_product_swap_image( $image_size );
			}
		}
	}

	/**
	 * render_quick_view
	 *
	 * @return void
	 */
	public function render_quick_view() {
		global $product;

		$settings   = $this->parent->get_settings_for_display();
		$product_id = $product->get_id();
		$quick_view = $this->get_instance_value( 'quick_view' );
		$qv_text    = $settings['qv_text'];
		$qv_type    = $this->get_instance_value( 'quick_view_type' );

		$product_quick_view = apply_filters( 'trx_addons_woo_products_product_quick_view', $qv_text );

		if ( 'yes' === $quick_view ) {
			if ( 'button' === $qv_type ) {
				?>
				<div class="trx-addons-woo-products-qv-btn trx-addons-woo-products-qv-btn-translate" data-product-id="<?php esc_attr_e( $product_id ); ?>">
					<span class="trx-addons-woo-products-qv-btn-text"><?php esc_html_e( $product_quick_view  ); ?></span>
					<?php $this->render_common_icon( 'qv_icon', array( 'default_icon' => 'far fa-eye', 'skin_used' => false, 'tag_name' => 'span' ) ); ?>
				</div>
				<?php

			} elseif ( 'image' === $qv_type && 'yes' === $this->get_instance_value( 'product_image' ) ) {
				?>
				<div class="trx-addons-woo-products-qv-data" data-product-id="<?php esc_attr_e( $product_id ); ?>"></div>
				<?php
			}
		}
	}

	/**
	 * render_cta_position_above
	 *
	 * @return void
	 */
	public function render_cta_position_above() {
		global $product;

		$product_id = $product->get_id();
		$cta_position = $this->get_instance_value( 'cta_position' );
		$cart_class = $product->is_purchasable() && $product->is_in_stock() ? 'trx-addons-woo-products-product-cta-btn' : '';

		if ( 'above' === $cta_position ) {
			?>
			<div class="trx-addons-woo-products-product-actions-wrapper">
				<a href="<?php echo esc_url( $product->add_to_cart_url() ); ?>" class="trx-addons-woo-products-cart-btn trx-addons-woo-products-cart-btn-above <?php esc_attr_e( $cart_class ); ?> product_type_<?php esc_attr_e( $product->get_type() ); ?>" data-product_id="<?php esc_attr_e( $product_id ); ?>">
					<?php $this->render_cta_position_above_icon(); ?>
				</a>
			</div>
			<?php
		}
	}

	/**
	 * render_cta_position_above_icon
	 *
	 * @return void
	 */
	public function render_cta_position_above_icon() {
		?>
		<span class="trx-addons-woo-products-add-cart-icon trx-addons-icon trx-addons-woo-products-cta-icon trx-addons-woo-products-cta-icon-<?php echo $this->get_id(); ?>">
			<svg aria-hidden="true" fill="currentColor" class="e-font-icon-svg e-fas-shopping-bag" viewBox="0 0 448 512" xmlns="http://www.w3.org/2000/svg"><path d="M352 160v-32C352 57.42 294.579 0 224 0 153.42 0 96 57.42 96 128v32H0v272c0 44.183 35.817 80 80 80h288c44.183 0 80-35.817 80-80V160h-96zm-192-32c0-35.29 28.71-64 64-64s64 28.71 64 64v32H160v-32zm160 120c-13.255 0-24-10.745-24-24s10.745-24 24-24 24 10.745 24 24-10.745 24-24 24zm-192 0c-13.255 0-24-10.745-24-24s10.745-24 24-24 24 10.745 24 24-10.745 24-24 24z"></path></svg>
		</span>
		<?php
	}

	/**
	 * render_product_structure_title
	 *
	 * @return void
	 */
	public function render_product_structure_title( $title_html_tag = '' ) {
		global $product;

		$settings   = $this->parent->get_settings_for_display();
		$product_id = $product->get_id();
		$title_link = apply_filters( 'trx_addons_woo_products_product_title_link', get_the_permalink() );

		if ( ! $title_html_tag ) {
			$title_html_tag = $this->get_instance_value( 'title_html_tag' );
		}

		if ( ! $title_html_tag ) {
			$title_html_tag = 'h2';
		}

		do_action( 'trx_addons_woo_products_product_before_title', $product_id, $settings );
		?>
		<a href="<?php echo esc_url( $title_link ); ?>" class="trx-addons-woo-products-product__link">
			<?php echo '<' . $title_html_tag . ' class="' . esc_attr( apply_filters( 'woocommerce_product_loop_title_classes', 'woocommerce-loop-product__title' ) ) . '">' . get_the_title() . '</' . $title_html_tag . '>'; ?>
		</a>
		<?php
		do_action( 'trx_addons_woo_products_product_after_title', $product_id, $settings );
	}

	/**
	 * render_product_structure_price
	 *
	 * @return void
	 */
	public function render_product_structure_price() {
		global $product;

		$settings   = $this->parent->get_settings_for_display();
		$product_id = $product->get_id();

		do_action( 'trx_addons_woo_products_product_before_price', $product_id, $settings );
		woocommerce_template_loop_price();
		do_action( 'trx_addons_woo_products_product_after_price', $product_id, $settings );
	}

	/**
	 * render_product_structure_ratings
	 *
	 * @return void
	 */
	public function render_product_structure_ratings() {
		global $product;

		$settings   = $this->parent->get_settings_for_display();
		$product_id = $product->get_id();

		do_action( 'trx_addons_woo_products_product_before_rating', $product_id, $settings );
		woocommerce_template_loop_rating();
		do_action( 'trx_addons_woo_products_product_after_rating', $product_id, $settings );
	}

	/**
	 * render_product_structure_desc
	 *
	 * @return void
	 */
	public function render_product_structure_desc( $excerpt_length = 0 ) {
		global $product;

		$settings   = $this->parent->get_settings_for_display();
		$product_id = $product->get_id();

		do_action( 'trx_addons_woo_products_product_before_desc', $product_id, $settings );
		$this->get_product_excerpt( $excerpt_length );
		do_action( 'trx_addons_woo_products_product_after_desc', $product_id, $settings );
	}

	/**
	 * render_product_structure_cta
	 *
	 * @return void
	 */
	public function render_product_structure_cta() {
		$cta_position = $this->get_instance_value( 'cta_position' );

		if ( 'below' === $cta_position ) {
			$this->render_product_structure_cta_hard();
		}
	}

	/**
	 * render_product_structure_cta_hard
	 *
	 * @return void
	 */
	public function render_product_structure_cta_hard() {
		global $product;

		$settings   = $this->parent->get_settings_for_display();
		$product_id = $product->get_id();
		$attributes = count( $product->get_attributes() ) > 0 ? 'data-variations="true"' : '';

		do_action( 'trx_addons_woo_products_product_before_cta', $product_id, $settings );
		add_filter( 'woocommerce_product_add_to_cart_text', array( $this, 'wc_product_add_to_cart_text' ), 10, 2 );
		?>
		<div class="trx-addons-woo-products-atc-button" <?php esc_attr_e( $attributes ); ?> >
			<?php woocommerce_template_loop_add_to_cart(); ?>
		</div>
		<?php
		remove_filter( 'woocommerce_product_add_to_cart_text', array( $this, 'wc_product_add_to_cart_text' ), 10, 2 );
		do_action( 'trx_addons_woo_products_product_after_cta', $product_id, $settings );
	}

	/**
	 * wc_product_add_to_cart_text
	 *
	 * @param  mixed $text
	 * @param  mixed $product
	 * @return void
	 */
	public function wc_product_add_to_cart_text( $text, $product ) {

		$label_buy_now   = $this->get_instance_value( 'cta_buy_now_label' );
		$label_read_more = $this->get_instance_value( 'cta_read_more_label' );

		if ( $product->is_in_stock() && ! empty( $label_buy_now ) ) {
			$text = $label_buy_now;
		} elseif ( ! empty( $label_read_more ) ) {
			$text = $label_read_more;
		}

		return apply_filters(
			'trx_addons_products_add_to_cart_text',
			$text,
			$product
		);
	}

	/**
	 * render_product_structure_category
	 *
	 * @return void
	 */
	public function render_product_structure_category() {
		global $product;

		$settings   = $this->parent->get_settings_for_display();
		$product_id = $product->get_id();

		do_action( 'trx_addons_woo_products_product_before_cat', $product_id, $settings );
		$this->get_current_product_category();
		do_action( 'trx_addons_woo_products_product_after_cat', $product_id, $settings );
	}

	/**
     * render_product_structure
     *
     * @param  mixed $product_structure
     * @return void
     */
    public function render_product_structure( $product_structure ) {
        if ( empty( $product_structure ) || !is_array( $product_structure ) ) {
            return;
        }

		foreach ( $product_structure as $index => $segment ) {
			$value = $segment['product_segment'];
			switch ( $value ) {
				case 'title':
					$this->render_product_structure_title( $segment['title_html_tag'] );
					break;

				case 'price':
					$this->render_product_structure_price();
					break;

				case 'ratings':
					$this->render_product_structure_ratings();
					break;

				case 'desc':
					$this->render_product_structure_desc( $segment['excerpt_length'] );
					break;

				case 'cta':
					$this->render_product_structure_cta();
					break;

				case 'category':
					$this->render_product_structure_category();
					break;

				default:
					break;
			}
		}
	}

	/**
	 * render_product_structure_wrapper
	 *
	 * @return void
	 */
	public function render_product_structure_wrapper() {
		$product_structure = $this->get_instance_value( 'product_structure' );

		if ( count( $product_structure ) ) {

			$this->render_details_wrap_start();

			$this->render_product_structure( $product_structure );

			$this->render_details_wrap_end();
		}
	}

	/**
	 * render_current_product_images
	 *
	 * @return void
	 */
	public function render_current_product_images() {
		global $product;

		$product_id = $product->get_id();
		$settings   = $this->parent->get_settings_for_display();
		$size       = $settings['featured_image_size'];

		$attachment_ids = $product->get_gallery_image_ids();

		if ( $attachment_ids ) {

			$image_size = apply_filters( 'single_product_archive_thumbnail_size', $size );

			foreach ( $attachment_ids as $index => $id ) {
				if ( $index > 2 ) {
					break;
				}

				$gallery_image = apply_filters( 'trx_addons_woo_products_product_gallery_image', wp_get_attachment_image( $id, $image_size, false, array( 'class' => 'trx-addons-woo-products-product__gallery_image' ) ) );

				echo wp_kses_post( $gallery_image );
			}
		}
	}

	/**
	 * render_current_product_images_links
	 *
	 * @return void
	 */
	public function render_current_product_images_links() {
		global $product;

		$product_id = $product->get_id();
		$settings   = $this->parent->get_settings_for_display();
		$size       = $settings['featured_image_size'];

		$attachment_ids = $product->get_gallery_image_ids();

		if ( $attachment_ids ) {

			$image_size = apply_filters( 'single_product_archive_thumbnail_size', $size );

			foreach ( $attachment_ids as $index => $id ) {
				if ( $index > 2 ) {
					break;
				}

				$gallery_image = apply_filters( 'trx_addons_woo_products_product_gallery_image', wp_get_attachment_image( $id, $image_size, false, array( 'class' => 'trx-addons-woo-products-product__gallery_image' ) ) );


				?><div class="trx-addons-woo-products-thumbnail-swiper-slide swiper-slide"><?php
				woocommerce_template_loop_product_link_open();

				echo wp_kses_post( $gallery_image );

				woocommerce_template_loop_product_link_close();
				?></div><?php
			}
		}
	}

	/**
	 * render_product_thumbnail_slider_start
	 *
	 * @return void
	 */
	public function render_product_thumbnail_slider_start() {
		global $product;

		$attachment_ids = $product->get_gallery_image_ids();

		if ( $attachment_ids ) {
			?>
			<div class="swiper-container-wrap swiper trx-addons-woo-products-product-thumbnail-slider" data-product_id="<?php echo $product->get_id(); ?>">
				<div class="swiper-container trx-addons-swiper-slider trx-addons-woo-products-product-thumbnail-slider-container">
					<div class="swiper-wrapper">
			<?php
		}
	}

	/**
	 * render_product_thumbnail_slider_end
	 *
	 * @return void
	 */
	public function render_product_thumbnail_slider_end() {
		global $product;

		$attachment_ids = $product->get_gallery_image_ids();

		if ( $attachment_ids ) {
			?>
					</div>
				</div>
				<?php $this->render_product_thumbnail_slider_arrows(); ?>
			</div>
			<?php
		}
	}

	/**
	 * render_product_thumbnail_slider_arrows
	 *
	 * @return void
	 */
	public function render_product_thumbnail_slider_arrows() {
		global $product;

		$attachment_ids = $product->get_gallery_image_ids();

		if ( $attachment_ids && 'grid-11' !== $this->get_id() ) {
			?>
			<div class="trx-addons-slider-arrow trx-addons-arrow-prev elementor-swiper-button-prev swiper-button-prev-thumbnail-<?php echo $this->parent->get_id(); ?>-<?php echo $product->get_id(); ?>">
				<svg aria-hidden="true" class="e-font-icon-svg e-fas-angle-left" viewBox="0 0 256 512" xmlns="http://www.w3.org/2000/svg"><path d="M31.7 239l136-136c9.4-9.4 24.6-9.4 33.9 0l22.6 22.6c9.4 9.4 9.4 24.6 0 33.9L127.9 256l96.4 96.4c9.4 9.4 9.4 24.6 0 33.9L201.7 409c-9.4 9.4-24.6 9.4-33.9 0l-136-136c-9.5-9.4-9.5-24.6-.1-34z"></path></svg>
			</div>
			<div class="trx-addons-slider-arrow trx-addons-arrow-next elementor-swiper-button-next swiper-button-next-thumbnail-<?php echo $this->parent->get_id(); ?>-<?php echo $product->get_id(); ?>">
				<svg aria-hidden="true" class="e-font-icon-svg e-fas-angle-right" viewBox="0 0 256 512" xmlns="http://www.w3.org/2000/svg"><path d="M224.3 273l-136 136c-9.4 9.4-24.6 9.4-33.9 0l-22.6-22.6c-9.4-9.4-9.4-24.6 0-33.9l96.4-96.4-96.4-96.4c-9.4-9.4-9.4-24.6 0-33.9L54.3 103c9.4-9.4 24.6-9.4 33.9 0l136 136c9.5 9.4 9.5 24.6.1 34z"></path></svg>
			</div>
			<?php
		}
	}

	/**
	 * render_current_product_gallery_images
	 *
	 * @return void
	 */
	public function render_current_product_gallery_images() {
		?>
		<div class="trx-addons-woo-products-product-gallery-images">
			<?php $this->render_current_product_images(); ?>
		</div>
		<?php
	}

	/**
	 * get_product_wc_classes
	 *
	 * @param  mixed $class
	 * @param  mixed $classes
	 * @return void
	 */
	public function get_product_wc_classes( $class = array(), $classes = array() ) {
		global $product;

		$product_id = $product->get_id();

		$classes[]  = 'post-' . $product_id;

		return esc_attr( implode( ' ', wc_product_post_class( $classes, $class, $product_id ) ) );
	}

	/**
	 * Returns the paged number for the query.
	 * 
	 * @return int
	 */
	public function get_paged() {
		$settings = $this->parent->get_settings_for_display();

		global $wp_the_query, $paged;

		if ( isset( $_POST['nonce'] ) && wp_verify_nonce( $_POST['nonce'], 'trx-addons-woo-products-pagination-nonce' ) ) {
			if ( isset( $_POST['page_number'] ) && '' !== $_POST['page_number'] ) {
				return $_POST['page_number'];
			}
		}

		if ( 'yes' === $settings['pagination'] ) {
			// Check the 'paged' query var.
			$paged_qv = $wp_the_query->get( 'paged' );

			if ( is_numeric( $paged_qv ) ) {
				return $paged_qv;
			}

			// Check the 'page' query var.
			$page_qv = $wp_the_query->get( 'page' );

			if ( is_numeric( $page_qv ) ) {
				return $page_qv;
			}

			// Check the $paged global?
			if ( is_numeric( $paged ) ) {
				return $paged;
			}

			return 0;
		} else {
			return max( 1, get_query_var( 'paged' ), get_query_var( 'page' ) );
		}
	}
	
	/**
	 * get_posts_nav_link
	 *
	 * @param  mixed $page_limit
	 * @return void
	 */
	public function get_posts_nav_link( $page_limit = null ) {
		if ( ! $page_limit ) {
			$page_limit = $this->query->max_num_pages;
		}

		$return = array();

		$paged = $this->get_paged();

		$link_template     = '<a class="page-numbers %s" href="%s">%s</a>';
		$disabled_template = '<span class="page-numbers %s">%s</span>';

		if ( $paged > 1 ) {
			$next_page = intval( $paged ) - 1;
			if ( $next_page < 1 ) {
				$next_page = 1;
			}

			$return['prev'] = sprintf( $link_template, 'prev', $this->get_wp_link_page( $next_page ), $this->parent->get_settings_for_display( 'pagination_prev_label' ) );
		} else {
			$return['prev'] = sprintf( $disabled_template, 'prev', $this->parent->get_settings_for_display( 'pagination_prev_label' ) );
		}

		$next_page = intval( $paged ) + 1;

		if ( $next_page <= $page_limit ) {
			$return['next'] = sprintf( $link_template, 'next', $this->get_wp_link_page( $next_page ), $this->parent->get_settings_for_display( 'pagination_next_label' ) );
		} else {
			$return['next'] = sprintf( $disabled_template, 'next', $this->parent->get_settings_for_display( 'pagination_next_label' ) );
		}

		return $return;
	}

	/**
	 * render_common_icon
	 *
	 * @return void
	 */
	public function render_common_icon( $icon_name, $options ) {
		if ( !$icon_name ) {
			return;
		}

		$options = array_merge(array(
			'skin_used'    => true,
			'default_icon' => '',
			'tag_name'     => 'div'
		), $options);

		$settings         = $this->parent->get_settings_for_display();
		$skin             = $this->get_id();

		$icon_slug        = str_replace( '_', '-', $icon_name );
		$icon_name_select = 'select_' . $icon_name;

		if ( $options['skin_used'] ) {
			$icon         = $this->get_instance_value( $icon_name );
			$select_icon  = $this->get_instance_value( $icon_name_select );
		} else {
			$icon         = isset( $settings[$icon_name] ) ? $settings[$icon_name] : '';
			$select_icon  = $settings[$icon_name_select];
		}

		$migration_allowed = Icons_Manager::is_migration_allowed();

		if ( ! isset( $settings[$skin . '_' . $icon_name] ) && ! Icons_Manager::is_migration_allowed() && !empty( $options['default_icon'] ) ) {
			// add old default.
			$settings[ $skin . '_' . $icon_name ] = $options['default_icon'];
		}

		if ( ! empty( $settings[$skin . '_' . $icon_name] ) ) {
			$this->add_render_attribute( $icon_slug, 'class', $settings[$skin . '_' . $icon_name] );
			$this->add_render_attribute( $icon_slug, 'aria-hidden', 'true' );
		}

		$migrated = isset( $settings['__fa4_migrated'][ $skin . '_' . $icon_name_select ] );
		$is_new   = ! isset( $settings[$skin . '_' . $icon_name] ) && Icons_Manager::is_migration_allowed();

		if ( ! empty( $icon ) || ( ! empty( $select_icon['value'] ) && $is_new ) ) { ?>
			<<?php echo $options['tag_name'] ?> class="trx-addons-icon trx-addons-woo-products-<?php echo $icon_slug; ?> trx-addons-woo-products-<?php echo $icon_slug; ?>-<?php esc_attr_e( $this->get_id() ); ?>">
				<?php if ( $is_new || $migrated ) :
					Icons_Manager::render_icon( $select_icon, [ 'aria-hidden' => 'true', 'fill' => 'currentColor' ] );
				else : ?>
					<i <?php echo $this->get_render_attribute_string( $icon_slug ); ?>></i>
				<?php endif; ?>
			</<?php echo $options['tag_name'] ?>>
		<?php }
	}

	/**
	 * Render post body output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @access protected
	 */
	public function render_ajax_pagination() {
		ob_start();

		$this->render_pagination_structure();

		return ob_get_clean();
	}

	/**
	 * Render product body output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @access protected
	 */
	public function render_ajax_post_body() {
		ob_start();

		$this->render_query( true );

		$this->set_query_args();

		$this->render_woo_products();

		return ob_get_clean();
	}
}