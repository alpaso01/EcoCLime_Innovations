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
 * Class Skin8
 *
 * @property Products $parent
 */
class Skin8 extends BaseSkin {

	/**
	 * Get ID.
	 *
	 * @access public
	 */
	public function get_id() {
		return 'grid-8';
	}

	/**
	 * Get title.
	 *
	 * @access public
	 */
	public function get_title() {
		return __( 'Skin 8', 'trx_addons' );
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

		// Quick View Controls.
		add_action( 'elementor/element/trx_elm_woo_products/section_pagination_options/after_section_end', array( $this, 'register_quick_view_controls' ) );


		// -- Style Start
		// Product Overlay Style.
		add_action( 'elementor/element/trx_elm_woo_products/section_image_style/after_section_end', array( $this, 'register_image_overlay' ) );


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
	 * Register Image Overlay section.
	 *
	 * @access public
	 *
	 * @param Widget_Base $widget widget object.
	 */
	public function register_image_overlay( Widget_Base $widget ) {

		$this->parent = $widget;

		$this->start_controls_section(
			'section_overlay_controls',
			array(
				'label' => __( 'Overlay', 'trx_addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'overlay_background',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .trx-addons-woo-products-product-overlay',
			)
		);

		$this->add_responsive_control(
			'overlay_icons_align',
			array(
				'label'     => __( 'Icons Alignment', 'trx_addons' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'flex-start' => array(
						'title' => __( 'Top', 'trx_addons' ),
						'icon'  => 'eicon-arrow-up',
					),
					'center'     => array(
						'title' => __( 'Center', 'trx_addons' ),
						'icon'  => 'eicon-text-align-center',
					),
					'flex-end'   => array(
						'title' => __( 'Bottom', 'trx_addons' ),
						'icon'  => 'eicon-arrow-down',
					),
				),
				'default'   => 'flex-end',
				'toggle'    => false,
				'selectors' => array(
					'{{WRAPPER}} .trx-addons-woo-products-product-overlay'    => 'align-items: {{VALUE}}',
				),
			)
		);

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

		$this->add_responsive_control(
			'cta_size',
			array(
				'label'      => __( 'Size', 'trx_addons' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .trx-addons-woo-products .trx-addons-woo-products-atc-button' => 'font-size: {{SIZE}}{{UNIT}}; min-width: {{SIZE}}{{UNIT}}; min-height: {{SIZE}}{{UNIT}};',
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
					'{{WRAPPER}} .trx-addons-woo-products .trx-addons-woo-products-atc-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .trx-addons-woo-products.trx-addons-woo-products-skin-grid-8 .trx-addons-woo-products-atc-button .button' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'cta_background',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .trx-addons-woo-products .trx-addons-woo-products-atc-button',
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'cta_shadow',
				'selector' => '{{WRAPPER}} .trx-addons-woo-products .trx-addons-woo-products-atc-button',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'cta_border',
				'selector' => '{{WRAPPER}} .trx-addons-woo-products .trx-addons-woo-products-atc-button',
			)
		);

		$this->add_control(
			'cta_radius',
			array(
				'label'      => __( 'Border Radius', 'trx_addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .trx-addons-woo-products .trx-addons-woo-products-atc-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
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
					'{{WRAPPER}} .trx-addons-woo-products.trx-addons-woo-products-skin-grid-8 .trx-addons-woo-products-atc-button:hover .button' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'cta_background_hover',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .trx-addons-woo-products .trx-addons-woo-products-atc-button:hover',
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'cta_shadow_hover',
				'selector' => '{{WRAPPER}} .trx-addons-woo-products .trx-addons-woo-products-atc-button:hover',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'cta_border_hover',
				'selector' => '{{WRAPPER}} .trx-addons-woo-products .trx-addons-woo-products-atc-button:hover',
			)
		);

		$this->add_control(
			'cta_radius_hover',
			array(
				'label'      => __( 'Border Radius', 'trx_addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .trx-addons-woo-products .trx-addons-woo-products-atc-button:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'select_cta_icon',
			array(
				'label'                  => __( 'Choose Icon', 'trx_addons' ),
				'type'                   => Controls_Manager::ICONS,
				'fa4compatibility'       => 'cta_icon',
				'default'                => array(
					'value'   => 'fas fa-plus',
					'library' => 'fa-solid',
				),
				'recommended'            => array(
					'fa-solid'   => array(
						'plus',
						'plus-circle',
						'plus-square',
						'cart-plus',
					),
					'fa-regular' => array(
						'plus-square',
					),
				),
				'condition' => array(
					$this->get_control_id( 'product_cta' ) => 'yes',
				),
			)
		);

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
			'quick_view',
			array(
				'label'   => __( 'Enable Quick View', 'trx_addons' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
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
					'{{WRAPPER}} .trx-addons-woo-products .trx-addons-woo-products-qv-btn' => 'font-size: {{SIZE}}{{UNIT}}; min-width: {{SIZE}}{{UNIT}}; min-height: {{SIZE}}{{UNIT}};',
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
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .trx-addons-woo-products.trx-addons-woo-products-skin-grid-8 .trx-addons-woo-products-qv-icon' => 'color: {{VALUE}};',
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
					'{{WRAPPER}} .trx-addons-woo-products.trx-addons-woo-products-skin-grid-8 .trx-addons-woo-products-qv-icon:hover' => 'color: {{VALUE}}',
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
	 * render_product_structure_cta
	 *
	 * @return void
	 */
	public function render_product_structure_cta() {
		global $product;

		$settings   = $this->parent->get_settings_for_display();
		$product_id = $product->get_id();
		$attributes = count( $product->get_attributes() ) > 0 ? 'data-variations="true"' : '';
        $cart_class = $product->is_purchasable() && $product->is_in_stock() ? 'trx-addons-woo-products-product-cta-btn' : '';

        do_action( 'trx_addons_woo_products_product_before_cta', $product_id, $settings );
        ?>
		<div class="trx-addons-woo-products-atc-button" <?php esc_attr_e( $attributes ); ?>>
				<a href="<?php echo esc_url( $product->add_to_cart_url() ); ?>" class="button trx-addons-woo-products-cart-btn <?php esc_attr_e( $cart_class ); ?> product_type_<?php esc_attr_e( $product->get_type() ); ?>" data-product_id="<?php esc_html_e( $product_id ); ?>">
					<?php $this->render_common_icon( 'cta_icon', array( 'default_icon' => 'fas fa-plus' ) ); ?>
				</a>
		</div>
		<?php
        do_action( 'trx_addons_woo_products_product_after_cta', $product_id, $settings );
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
	 * Render Product Template.
	 */
	public function render_product_template() {

		global $product;

		// Ensure visibility.
		if ( empty( $product ) || ! $product->is_visible() ) {
			return;
		}

        ?>
        <li class=" <?php echo $this->get_product_wc_classes(); ?>">
            <div class="trx-addons-woo-products-product-wrapper">
                <div class="trx-addons-woo-products-product-thumbnail">
                    <div class="trx-addons-woo-products-product-overlay">
                        <?php
                        if ( 'yes' === $this->get_instance_value( 'product_cta' ) ) {
                            $this->render_product_structure_cta();
                        }

                        if ( 'yes' === $this->get_instance_value( 'quick_view' ) ) {
                            $this->render_quick_view();
                        }
                        ?>
                    </div>

                    <?php
                    $this->render_ribbon_container();

                    woocommerce_template_loop_product_link_open();

                    $this->render_product_image();

                    woocommerce_template_loop_product_link_close();
                    ?>
                </div>

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

                        if ( 'yes' === $this->get_instance_value( 'product_rating' ) ) {
                            $this->render_product_structure_ratings();
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
