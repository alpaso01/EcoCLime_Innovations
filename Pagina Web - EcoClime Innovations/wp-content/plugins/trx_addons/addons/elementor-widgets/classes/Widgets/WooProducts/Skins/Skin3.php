<?php
namespace TrxAddons\ElementorWidgets\Widgets\WooProducts\Skins;

// Elementor Classes
use Elementor\Controls_Manager;
use Elementor\Widget_Base;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Repeater;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Border;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // If this file is called directly, abort.
}

/**
 * Class Skin_3
 *
 * @property Products $parent
 */
class Skin3 extends BaseSkin {

	/**
	 * Get ID.
	 *
	 * @access public
	 */
	public function get_id() {
		return 'grid-3';
	}

	/**
	 * Get title.
	 *
	 * @access public
	 */
	public function get_title() {
		return __( 'Skin 3', 'trx_addons' );
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
		// Product Description Style.
		add_action( 'elementor/element/trx_elm_woo_products/section_product_box_style/after_section_end', array( $this, 'register_product_excerpt_style' ), 30 );

		// Product CTA Style.
		add_action( 'elementor/element/trx_elm_woo_products/section_product_box_style/after_section_end', array( $this, 'register_product_cta_style' ), 60 );


		// Product Featured Ribbon Style.
		add_action( 'elementor/element/trx_elm_woo_products/section_carousel_style/after_section_end', array( $this, 'register_quick_style_controls' ), 90 );
	}

	/**
	 * Register display options controls.
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
			'title_above_img',
			array(
				'label'        => __( 'Place Title Above Image', 'trx_addons' ),
				'type'         => Controls_Manager::SWITCHER,
				'prefix_class' => 'trx-addons-woo-products-title-above-',
				'render_type'  => 'template',
				'condition'    => array(
					$this->get_control_id( 'product_image' ) => 'yes',
				),
			)
		);

		$repeater = new REPEATER();

		$repeater->add_control(
			'product_segment',
			array(
				'label'    => __( 'Select Product Segment', 'trx_addons' ),
				'type'     => Controls_Manager::SELECT,
				'options'  => array(
					'title'    => __( 'Title', 'trx_addons' ),
					'category' => __( 'Category', 'trx_addons' ),
					'ratings'  => __( 'Rating', 'trx_addons' ),
					'price'    => __( 'Price', 'trx_addons' ),
					'desc'     => __( 'Excerpt', 'trx_addons' ),
					'cta'      => __( 'Add To Cart', 'trx_addons' ),
				),
				'multiple' => true,
			)
		);

		$repeater->add_control(
			'excerpt_length',
			array(
				'label'     => __( 'Excerpt Length', 'trx_addons' ),
				'type'      => Controls_Manager::NUMBER,
				'condition' => array(
					'product_segment' => 'desc',
				),
			)
		);

		$repeater->add_control(
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
					'product_segment' => 'title',
				),
			)
		);

		$this->add_control(
			'product_structure',
			array(
				'label'       => __( 'Product Structure', 'trx_addons' ),
				'type'        => Controls_Manager::REPEATER,
				'default'     => array(
					array(
						'product_segment' => 'title',
					),
					array(
						'product_segment' => 'category',
					),
					array(
						'product_segment' => 'ratings',
					),
					array(
						'product_segment' => 'price',
					),
					array(
						'product_segment' => 'cta',
					),
				),
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ product_segment }}}',
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
	 * Register product excerpt style.
	 * Register product excerpt style Controls.
	 *
	 * @access public
	 *
	 * @param Widget_Base $widget widget object.
	 */
	public function register_product_excerpt_style( Widget_Base $widget ) {

		$this->parent = $widget;

		$this->start_controls_section(
			'section_desc_style',
			array(
				'label' => __( 'Description', 'trx_addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'desc_color',
			array(
				'label'     => __( 'Color', 'trx_addons' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_TEXT,
				),
				'selectors' => array(
					'{{WRAPPER}} .trx-addons-woo-products .trx-addons-woo-products-product-desc' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'desc_typography',
				'global'   => array(
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				),
				'selector' => '{{WRAPPER}} .trx-addons-woo-products .trx-addons-woo-products-product-desc',
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name'     => 'desc_text_shadow',
				'selector' => '{{WRAPPER}} .trx-addons-woo-products .trx-addons-woo-products-product-desc',
			)
		);

		$this->add_responsive_control(
			'desc_spacing',
			array(
				'label'      => __( 'Margin', 'trx_addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .trx-addons-woo-products .trx-addons-woo-products-product-desc' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Register product CTA style.
	 * Register product CTA style Controls.
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
				'label' => __( 'Add To Cart', 'trx_addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
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
					'{{WRAPPER}} .trx-addons-woo-products .trx-addons-woo-products-details-wrap .trx-addons-woo-products-atc-button .button' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'cta_background',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .trx-addons-woo-products .trx-addons-woo-products-details-wrap .trx-addons-woo-products-atc-button .button',
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'cta_shadow',
				'selector' => '{{WRAPPER}} .trx-addons-woo-products .trx-addons-woo-products-details-wrap .trx-addons-woo-products-atc-button .button',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'cta_border',
				'selector' => '{{WRAPPER}} .trx-addons-woo-products .trx-addons-woo-products-details-wrap .trx-addons-woo-products-atc-button .button',
			)
		);

		$this->add_control(
			'cta_radius',
			array(
				'label'      => __( 'Border Radius', 'trx_addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .trx-addons-woo-products .trx-addons-woo-products-details-wrap .trx-addons-woo-products-atc-button .button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
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
					'{{WRAPPER}} .trx-addons-woo-products .trx-addons-woo-products-details-wrap .trx-addons-woo-products-atc-button .button:hover' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'cta_background_hover',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .trx-addons-woo-products .trx-addons-woo-products-details-wrap .trx-addons-woo-products-atc-button .button:hover',
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'cta_shadow_hover',
				'selector' => '{{WRAPPER}} .trx-addons-woo-products .trx-addons-woo-products-details-wrap .trx-addons-woo-products-atc-button .button:hover',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'cta_border_hover',
				'selector' => '{{WRAPPER}} .trx-addons-woo-products .trx-addons-woo-products-details-wrap .trx-addons-woo-products-atc-button .button:hover',
			)
		);

		$this->add_control(
			'cta_radius_hover',
			array(
				'label'      => __( 'Border Radius', 'trx_addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .trx-addons-woo-products .trx-addons-woo-products-details-wrap .trx-addons-woo-products-atc-button .button:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
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
			'quick_view',
			array(
				'label'   => __( 'Enable Quick View', 'trx_addons' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			)
		);

		$this->add_control(
			'quick_view_type',
			array(
				'label'     => __( 'Quick View', 'trx_addons' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					'button' => __( 'On Button Click', 'trx_addons' ),
					'image'  => __( 'On Image Click', 'trx_addons' ),
				),
				'default'   => 'button',
				'condition' => array(
					$this->get_control_id( 'quick_view' ) => 'yes',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Register Quick View Style Controls.
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
				'label'     => __( 'Quick View Trigger Button', 'trx_addons' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					$this->get_control_id( 'quick_view' ) => 'yes',
					$this->get_control_id( 'quick_view_type' ) => 'button',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'qv_typography',
				'selector' => '{{WRAPPER}} .trx-addons-woo-products .trx-addons-woo-products-qv-btn',
				'global'   => array(
					'default' => Global_Typography::TYPOGRAPHY_ACCENT,
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
					'{{WRAPPER}} .trx-addons-woo-products .trx-addons-woo-products-qv-btn' => 'color: {{VALUE}};',
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
					'{{WRAPPER}} .trx-addons-woo-products .trx-addons-woo-products-qv-btn:hover' => 'color: {{VALUE}}',
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

		?>
		<div class="trx-addons-woo-products-product-actions-wrapper">
			<?php 
				do_action( 'trx_addons_woo_products_product_before_cta', $product_id, $settings );
				add_filter( 'woocommerce_product_add_to_cart_text', array( $this, 'wc_product_add_to_cart_text' ), 10, 2 );
			?>
			<div class="trx-addons-woo-products-atc-button" <?php esc_attr_e( $attributes ); ?>>
				<?php woocommerce_template_loop_add_to_cart(); ?>
			</div>
			<?php 
				remove_filter( 'woocommerce_product_add_to_cart_text', array( $this, 'wc_product_add_to_cart_text' ), 10, 2 );
				do_action( 'trx_addons_woo_products_product_after_cta', $product_id, $settings );
			?>
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
                    <?php
                    $this->render_ribbon_container();

                    woocommerce_template_loop_product_link_open();

                    $this->render_product_image();

                    woocommerce_template_loop_product_link_close();

                    $this->render_quick_view();
                    ?>
                </div>

                <?php $this->render_product_structure_wrapper(); ?>
            </div>
        </li>
        <?php
    }
}
