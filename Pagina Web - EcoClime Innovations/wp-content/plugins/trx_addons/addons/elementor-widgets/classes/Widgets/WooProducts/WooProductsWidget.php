<?php
/**
 * WooProducts Widget
 *
 * @package ThemeREX Addons
 * @since v2.30.0
 */

namespace TrxAddons\ElementorWidgets\Widgets\WooProducts;

use TrxAddons\ElementorWidgets\BaseWidget;
use TrxAddons\ElementorWidgets\Utils as TrxAddonsUtils;
use TrxAddons\ElementorWidgets\Controls\Transition\TransitionControl;

// Elementor Classes.
use Elementor\Controls_Manager;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Css_Filter;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * WooProducts Widget
 */
class WooProductsWidget extends BaseWidget {

	protected $query         = null;
	protected $query_filters = null;

	protected $_has_template_content = false;

	/**
	 * Register Skins.
	 */
	protected function register_skins() {
		$this->add_skin( new Skins\Skin1( $this ) );
		$this->add_skin( new Skins\Skin2( $this ) );
		$this->add_skin( new Skins\Skin3( $this ) );
		$this->add_skin( new Skins\Skin4( $this ) );
		$this->add_skin( new Skins\Skin6( $this ) );

		$this->add_skin( new Skins\Skin7( $this ) );
		$this->add_skin( new Skins\Skin8( $this ) );
		$this->add_skin( new Skins\Skin9( $this ) );
		$this->add_skin( new Skins\Skin10( $this ) );
		$this->add_skin( new Skins\Skin11( $this ) );
	}

	/**
	 * Register widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 */
	protected function register_controls() {
        // content tab.
		$this->register_content_general_section();
		$this->register_content_grid_section();
		$this->register_content_carousel_section();
		$this->register_content_query_section();
		$this->register_content_pagination_section();
        $this->register_quick_view_settings();
		$this->register_content_ribbon_section();

		// style tab.
		$this->register_style_general_section();
		$this->register_style_image_section();
		$this->register_style_box_controls();
		$this->register_style_sale_controls();
		$this->register_style_sold_out_controls();
		$this->register_style_pagination_section();
		$this->register_style_loadmore_section();
		$this->register_style_featured_controls();
		$this->register_style_carousel_section();
	}

    /*-----------------------------------------------------------------------------------*/
	/*	CONTENT TAB
	/*-----------------------------------------------------------------------------------*/

	/**
	 * Content Tab: General
	 *
	 * @return void
	 */
	public function register_content_general_section() {

		$this->start_controls_section(
			'general_section',
			array(
				'label' => __( 'General', 'trx_addons' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'layout_type',
			array(
				'label'     => __( 'Layout', 'trx_addons' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'grid',
				'options'   => array(
					'grid'     => __( 'Grid', 'trx_addons' ),
					'masonry'  => __( 'Masonry', 'trx_addons' ),
					'carousel' => __( 'Carousel', 'trx_addons' ),
				),
			)
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			array(
				'name'      => 'featured_image',
				'default'   => 'full',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Content Tab: Grid
	 *
	 * @return void
	 */
	public function register_content_grid_section() {
		$this->start_controls_section(
			'section_grid_options',
			array(
				'label'     => __( 'Grid', 'trx_addons' ),
				'condition' => array(
					'layout_type' => array( 'grid', 'masonry' ),
				),
			)
		);

		$this->add_responsive_control(
			'columns',
			array(
				'label'          => __( 'Products Per Row', 'trx_addons' ),
				'type'           => Controls_Manager::SELECT,
				'options'        => array(
					'100%'    => __( '1 Column', 'trx_addons' ),
					'50%'     => __( '2 Columns', 'trx_addons' ),
					'33.33%'  => __( '3 Columns', 'trx_addons' ),
					'25%'     => __( '4 Columns', 'trx_addons' ),
					'20%'     => __( '5 Columns', 'trx_addons' ),
					'16.667%' => __( '6 Columns', 'trx_addons' ),
				),
				'default'        => '33.33%',
				'tablet_default' => '50%',
				'mobile_default' => '100%',
				'render_type'    => 'template',
				'selectors'      => array(
					'{{WRAPPER}} .trx-addons-woo-products-grid .trx-addons-woo-products-inner li.product' => 'width: {{VALUE}}',
					'{{WRAPPER}} .trx-addons-woo-products-masonry .trx-addons-woo-products-inner li.product' => 'width: {{VALUE}}',
				),
				'condition'      => array(
					'layout_type' => array( 'grid', 'masonry' ),
				),
			)
		);

		$this->add_control(
			'products_numbers',
			array(
				'label'       => __( 'Products Per Page', 'trx_addons' ),
				'description' => __( 'Choose how many products do you want to be displayed per page', 'trx_addons' ),
				'type'        => Controls_Manager::NUMBER,
				'min'         => 1,
				'default'     => 6,
			)
		);

		$this->add_control(
			'load_more',
			array(
				'label'     => __( 'Load More Button', 'trx_addons' ),
				'type'      => Controls_Manager::SWITCHER,
				'condition' => array(
					'pagination!'  => 'yes',
					'layout_type!' => 'carousel',
					'query_type!'  => array( 'cross-sells', 'up-sells' ),
				),
			)
		);

		$this->add_control(
			'load_more_text',
			array(
				'label'     => __( 'Button Text', 'trx_addons' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'Load More', 'trx_addons' ),
				'dynamic'   => array( 'active' => true ),
				'condition' => array(
					'load_more'   => 'yes',
					'pagination!' => 'yes',
					'query_type!' => array( 'cross-sells', 'up-sells' ),
				),
			)
		);

		$this->add_responsive_control(
			'load_more_align',
			array(
				'label'     => __( 'Button Alignment', 'trx_addons' ),
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
				'default'   => 'center',
				'selectors' => array(
					'{{WRAPPER}} .trx-addons-woo-products-load-more' => 'text-align: {{VALUE}};',
				),
				'condition' => array(
					'load_more'   => 'yes',
					'pagination!' => 'yes',
					'query_type!' => array( 'cross-sells', 'up-sells' ),
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Content Tab: Carousel Options
	 *
	 * @return void
	 */
	public function register_content_carousel_section() {
		$this->start_controls_section(
			'section_carousel_options',
			array(
				'label'     => __( 'Carousel Options', 'trx_addons' ),
				'type'      => Controls_Manager::SECTION,
				'condition' => array(
					'layout_type' => 'carousel',
				),
			)
		);

		$this->add_control(
			'arrows',
			array(
				'label'   => __( 'Show Arrows', 'trx_addons' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			)
		);

		$this->add_control(
			'select_arrow_icon',
			array(
				'label'                  => __( 'Choose Arrow', 'trx_addons' ),
				'type'                   => Controls_Manager::ICONS,
				'fa4compatibility'       => 'arrow_icon',
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
				'condition' => array(
					'layout_type' => 'carousel',
					'arrows'      => 'yes',
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
				'condition'  => array(
					'arrows' => 'yes',
				),
				'selectors'  => array(
					'{{WRAPPER}} .trx-addons-woo-products a.carousel-arrow.carousel-next' => 'right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .trx-addons-woo-products a.carousel-arrow.carousel-prev' => 'left: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'dots',
			array(
				'label'   => __( 'Show Dots', 'trx_addons' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			)
		);

		// $this->add_control(
		// 'dots_position',
		// array(
		// 'label'     => __( 'Position', 'trx_addons' ),
		// 'type'      => Controls_Manager::SELECT,
		// 'default'   => 'below',
		// 'options'   => array(
		// 'below' => __( 'Below Slides', 'trx_addons' ),
		// 'above' => __( 'On Slides', 'trx_addons' ),
		// ),
		// 'condition' => array(
		// 'dots' => 'yes',
		// ),
		// )
		// );

		$this->add_responsive_control(
			'dots_hoffset',
			array(
				'label'      => __( 'Horizontal Offset', 'trx_addons' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .trx-addons-woo-products-dots-above ul.slick-dots' => 'left: {{SIZE}}{{UNIT}}',
				),
				'condition'  => array(
					'dots'          => 'yes',
					'dots_position' => 'above',
				),
			)
		);

		$this->add_responsive_control(
			'dots_voffset',
			array(
				'label'      => __( 'Vertical Offset', 'trx_addons' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%' ),
				'default'    => array(
					'unit' => '%',
					'size' => 50,
				),
				'selectors'  => array(
					'{{WRAPPER}} .trx-addons-woo-products-above ul.slick-dots' => 'top: {{SIZE}}{{UNIT}}',
				),
				'condition'  => array(
					'dots'          => 'yes',
					'dots_position' => 'above',
				),
			)
		);

		$this->add_control(
			'total_carousel_products',
			array(
				'label'     => __( 'Number of Products', 'trx_addons' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => '8',
				'condition' => array(
					'query_type!' => 'main',
				),
			)
		);

		$this->add_responsive_control(
			'products_show',
			array(
				'label'              => __( 'Products to Show', 'trx_addons' ),
				'type'               => Controls_Manager::NUMBER,
				'description'        => __( 'Make sure to have the number of products larger than the number of products to show', 'trx_addons' ),
				'default'            => 3,
				'tablet_default'     => 2,
				'mobile_default'     => 1,
				'frontend_available' => true,
			)
		);

		$this->add_responsive_control(
			'products_on_scroll',
			array(
				'label'              => __( 'Products to Scroll', 'trx_addons' ),
				'type'               => Controls_Manager::NUMBER,
				'default'            => 1,
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'autoplay_slides',
			array(
				'label'     => __( 'Autoplay Slides', 'trx_addons' ),
				'type'      => Controls_Manager::SWITCHER,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'autoplay_speed',
			array(
				'label'     => __( 'Autoplay Interval', 'trx_addons' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 3000,
				'condition' => array(
					'autoplay_slides' => 'yes',
				),
			)
		);

		$this->add_control(
			'hover_pause',
			array(
				'label'     => __( 'Pause on Hover', 'trx_addons' ),
				'type'      => Controls_Manager::SWITCHER,
				'condition' => array(
					'autoplay_slides' => 'yes',
				),
			)
		);

		$this->add_control(
			'infinite_loop',
			array(
				'label'     => __( 'Infinite Loop', 'trx_addons' ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'separator' => 'before',
			)
		);

		$this->add_control(
			'speed',
			array(
				'label'     => __( 'Autoplay Speed', 'trx_addons' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 500,
				'condition' => array(
					'autoplay_slides' => 'yes',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Content Tab: Query
	 *
	 * @return void
	 */
	public function register_content_query_section() {
		$this->start_controls_section(
			'section_query_settings',
			array(
				'label'     => __( 'Query', 'trx_addons' ),
			)
		);

		$this->add_control(
			'query_type',
			array(
				'label'   => __( 'Source', 'trx_addons' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'all',
				'options' => $this->get_queries(),
			)
		);

		$this->add_control(
			'woo_upsells_notice',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => __( 'Upsells query can be used only on single product template.', 'trx_addons' ),
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info trx-addons-woo-products-notice',
				'condition'       => array(
					'query_type' => 'up-sells',
				),
			)
		);

		$this->add_control(
			'categories_filter_rule',
			array(
				'label'     => __( 'Category Filter Rule', 'trx_addons' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'IN',
				'options'   => array(
					'IN'     => __( 'Match Categories', 'trx_addons' ),
					'NOT IN' => __( 'Exclude Categories', 'trx_addons' ),
				),
				'condition' => array(
					'query_type' => 'custom',
				),
			)
		);

		$this->add_control(
			'categories',
			array(
				'label'     => __( 'Select Categories', 'trx_addons' ),
				'type'      => Controls_Manager::SELECT2,
				'multiple'  => true,
				'options'   => $this->get_woo_categories(),
				'condition' => array(
					'query_type' => 'custom',
				),
			)
		);

		$this->add_control(
			'tags_filter_rule',
			array(
				'label'     => __( 'Tag Filter Rule', 'trx_addons' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'IN',
				'options'   => array(
					'IN'     => __( 'Match Tags', 'trx_addons' ),
					'NOT IN' => __( 'Exclude Tags', 'trx_addons' ),
				),
				'condition' => array(
					'query_type' => 'custom',
				),
			)
		);

		$this->add_control(
			'tags',
			array(
				'label'     => __( 'Select Tags', 'trx_addons' ),
				'type'      => Controls_Manager::SELECT2,
				'multiple'  => true,
				'options'   => $this->get_woo_tags(),
				'condition' => array(
					'query_type' => 'custom',
				),
			)
		);

		$this->add_control(
			'product_filter_rule',
			array(
				'label'     => __( 'Product Filter Rule', 'trx_addons' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'post__in',
				'options'   => array(
					'post__in'     => __( 'Match Product', 'trx_addons' ),
					'post__not_in' => __( 'Exclude Product', 'trx_addons' ),
				),
				'condition' => array(
					'query_type' => 'custom',
				),
			)
		);

		$this->add_control(
			'products',
			array(
				'label'     => __( 'Select Products', 'trx_addons' ),
				'type'      => Controls_Manager::SELECT2,
				'multiple'  => true,
				'options'   => $this->get_default_posts_list( 'product' ),
				'condition' => array(
					'query_type' => 'custom',
				),
			)
		);

		$this->add_control(
			'offset',
			array(
				'label'       => __( 'Offset', 'trx_addons' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => 0,
				'description' => __( 'Set the starting index.', 'trx_addons' ),
				'condition'   => array(
					'query_type' => 'custom',
				),
			)
		);

		$this->add_control(
			'exclude_current_product',
			array(
				'label'       => __( 'Exclude Current Product', 'trx_addons' ),
				'type'        => Controls_Manager::SWITCHER,
				'label_on'    => __( 'Yes', 'trx_addons' ),
				'label_off'   => __( 'No', 'trx_addons' ),
				'description' => __( 'This option will remove the current from the query.', 'trx_addons' ),
				'condition'   => array(
					'query_type' => array( 'all', 'custom' ),
				),
			)
		);

		$this->add_control(
			'advanced_query_heading',
			array(
				'label'     => __( 'Advanced', 'trx_addons' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array(
					'query_type!' => array( 'main', 'related' ),
				),
			)
		);

		$this->add_control(
			'filter_by',
			array(
				'label'     => __( 'Filter By', 'trx_addons' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => '',
				'options'   => array(
					''         => __( 'None', 'trx_addons' ),
					'featured' => __( 'Featured', 'trx_addons' ),
					'sale'     => __( 'Sale', 'trx_addons' ),
				),
				'condition' => array(
					'query_type!' => array( 'main', 'related', 'cross-sells', 'up-sells' ),
				),
			)
		);

		$this->add_control(
			'orderby',
			array(
				'label'     => __( 'Order by', 'trx_addons' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'date',
				'options'   => array(
					'title'      => __( 'Title', 'trx_addons' ),
					'date'       => __( 'Date', 'trx_addons' ),
					'popularity' => __( 'Popularity', 'trx_addons' ),
					'price'      => __( 'Price', 'trx_addons' ),
					'rating'     => __( 'Rating', 'trx_addons' ),
					'rand'       => __( 'Random', 'trx_addons' ),
					'menu_order' => __( 'Menu Order', 'trx_addons' ),
				),
				'condition' => array(
					'query_type!' => array( 'main', 'related' ),
				),
			)
		);

		$this->add_control(
			'order',
			array(
				'label'     => __( 'Order', 'trx_addons' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'desc',
				'options'   => array(
					'desc' => __( 'Descending', 'trx_addons' ),
					'asc'  => __( 'Ascending', 'trx_addons' ),
				),
				'condition' => array(
					'query_type!' => array( 'main', 'related' ),
				),
			)
		);

		$this->add_control(
			'empty_products_msg',
			array(
				'label'       => __( 'Empty Query Message', 'trx_addons' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'No products were found for this query.', 'trx_addons' ),
				'label_block' => true,
				'condition'   => array(
					'query_type!' => array( 'up-sells' ),
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Content Tab: Pagination
	 *
	 * @return void
	 */
	public function register_content_pagination_section() {
		$this->start_controls_section(
			'section_pagination_options',
			array(
				'label'     => __( 'Pagination', 'trx_addons' ),
				'condition' => array(
					'layout_type' => array( 'grid', 'masonry' ),
					'load_more!'  => 'yes',
					'query_type!' => array( 'cross-sells', 'up-sells' ),
				),
			)
		);

		$this->add_control(
			'pagination',
			array(
				'label' => __( 'Enable Pagination', 'trx_addons' ),
				'type'  => Controls_Manager::SWITCHER,
			)
		);

		$this->add_control(
			'pagination_type',
			array(
				'label'     => __( 'Type', 'trx_addons' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					'numbers'       => __( 'Numbers', 'trx_addons' ),
					'numbers_arrow' => __( 'Numbers + Pre/Next Arrow', 'trx_addons' ),
				),
				'default'   => 'numbers',
				'condition' => array(
					'pagination' => 'yes',
				),
			)
		);

		$this->add_control(
			'prev_string',
			array(
				'label'     => __( 'Previous Page String', 'trx_addons' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( '« Previous', 'trx_addons' ),
				'condition' => array(
					'pagination'      => 'yes',
					'pagination_type' => 'numbers_arrow',
				),
			)
		);

			$this->add_control(
				'next_string',
				array(
					'label'     => __( 'Next Page String', 'trx_addons' ),
					'type'      => Controls_Manager::TEXT,
					'default'   => __( 'Next »', 'trx_addons' ),
					'condition' => array(
						'pagination'      => 'yes',
						'pagination_type' => 'numbers_arrow',
					),
				)
			);

		$this->add_responsive_control(
			'pagination_align',
			array(
				'label'     => __( 'Alignment', 'trx_addons' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'flex-start' => array(
						'title' => __( 'Left', 'trx_addons' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center'     => array(
						'title' => __( 'Center', 'trx_addons' ),
						'icon'  => 'eicon-text-align-center',
					),
					'flex-end'   => array(
						'title' => __( 'Right', 'trx_addons' ),
						'icon'  => 'eicon-text-align-right',
					),
				),
				'default'   => 'center',
				'selectors' => array(
					'{{WRAPPER}} .trx-addons-woo-products-pagination .page-numbers'  => 'justify-content: {{VALUE}}',
				),
				'toggle'    => false,
				'condition' => array(
					'pagination' => 'yes',
				),
			)
		);

		$this->end_controls_section();
	}

    /**
     * Content Tab: Quick View Settings
     *
     * @return void
     */
    public function register_quick_view_settings() {

        $this->start_controls_section(
			'section_quick_view_settings',
			array(
				'label' => __( 'Quick View Settings', 'trx_addons' ),
			)
		);

        $this->add_control(
			'qv_sale',
			array(
				'label'   => __( 'Hide Sale Ribbon', 'trx_addons' ),
				'type'    => Controls_Manager::SWITCHER,
				'selectors' => array(
					'#trx-addons-woo-products-quick-view-{{ID}} .trx-addons-qv-badge' => 'display: none !important',
				),
			)
		);

        $this->add_control(
			'qv_rating',
			array(
				'label'   => __( 'Hide Product Rating', 'trx_addons' ),
				'type'    => Controls_Manager::SWITCHER,
				'selectors' => array(
					'#trx-addons-woo-products-quick-view-{{ID}} .star-rating' => 'display: none !important',
				),
			)
		);

        $this->add_control(
			'qv_price',
			array(
				'label'   => __( 'Hide Product Price', 'trx_addons' ),
				'type'    => Controls_Manager::SWITCHER,
                'selectors' => array(
					'#trx-addons-woo-products-quick-view-{{ID}} .price' => 'display: none',
				),
			)
		);

        $this->add_control(
			'qv_desc',
			array(
				'label'   => __( 'Hide Product Description', 'trx_addons' ),
				'type'    => Controls_Manager::SWITCHER,
                'selectors' => array(
					'#trx-addons-woo-products-quick-view-{{ID}} .trx-addons-woo-products-qv-desc' => 'display: none',
				),
			)
		);

        $this->add_control(
			'qv_atc',
			array(
				'label'   => __( 'Hide Call to Action', 'trx_addons' ),
				'type'    => Controls_Manager::SWITCHER,
                'selectors' => array(
					'#trx-addons-woo-products-quick-view-{{ID}} .trx-addons-woo-products-atc-button' => 'display: none',
				),
			)
		);

        $this->add_control(
			'qv_meta',
			array(
				'label'   => __( 'Hide Product Meta', 'trx_addons' ),
				'type'    => Controls_Manager::SWITCHER,
				'selectors' => array(
					'#trx-addons-woo-products-quick-view-{{ID}} .trx-addons-woo-products-qv-meta' => 'display: none !important',
				),
			)
		);

        $this->add_responsive_control(
			'qv_display',
			array(
				'label'       => __( 'Image/Content Display', 'trx_addons' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'inline' => __( 'Inline', 'trx_addons' ),
					'block'  => __( 'Block', 'trx_addons' ),
				),
				'default'     => 'inline',
				'label_block' => true,
			)
		);

        $this->add_control(
			'qv_hide_responsive',
			array(
				'label'   => __( 'Hide On Mobile Devices', 'trx_addons' ),
				'type'    => Controls_Manager::SWITCHER,
                'default'=> 'yes',
                'prefix_class'=> 'trx-addons-woo-products-qv-hidden-'
			)
		);

		$this->add_control(
			'qv_text',
			array(
				'label'     => __( 'Label', 'trx_addons' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'Quick View', 'trx_addons' ),
			)
		);

		$this->add_control(
			'select_qv_icon',
			array(
				'label'                  => __( 'Choose Icon', 'trx_addons' ),
				'type'                   => Controls_Manager::ICONS,
				'fa4compatibility'       => 'qv_icon',
				'default'                => array(
					'value'   => 'far fa-eye',
					'library' => 'fa-regular',
				),
				'recommended'            => array(
					'fa-regular' => array(
						'eye',
					),
					'fa-solid'   => array(
						'eye',
					),
				),
				'condition' => array(
					'_skin!' => array( 'grid-2', 'grid-9' ),
				),
			)
		);

        $this->end_controls_section();

    }

	/**
	 * Content Tab: Sale/Featured Ribbons
	 *
	 * @return void
	 */
	public function register_content_ribbon_section() {

		$this->start_controls_section(
			'section_ribbons_settings',
			array(
				'label'     => __( 'Sale/Featured Ribbons', 'trx_addons' ),
			)
		);

		$this->add_control(
			'sale',
			array(
				'label'   => __( 'Show Sale Ribbon', 'trx_addons' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			)
		);

		$this->add_control(
			'sale_type',
			array(
				'label'     => __( 'Type', 'trx_addons' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					''       => __( 'Default', 'trx_addons' ),
					'custom' => __( 'Custom', 'trx_addons' ),
				),
				'default'   => '',
				'condition' => array(
					'sale' => 'yes',
				),
			)
		);

		$this->add_control(
			'sale_string',
			array(
				'label'       => __( 'String', 'trx_addons' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '[value]%',
				'description' => __( 'Use [value] to get the discount in perecentage, or [sale] to get the absolute value of the discount.', 'trx_addons' ),
				'condition'   => array(
					'sale'      => 'yes',
					'sale_type' => 'custom',
				),
			)
		);

		$this->add_control(
			'featured',
			array(
				'label' => __( 'Show Featured Ribbon', 'trx_addons' ),
				'type'  => Controls_Manager::SWITCHER,
			)
		);

		$this->add_control(
			'featured_string',
			array(
				'label'     => __( 'String', 'trx_addons' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'Hot', 'trx_addons' ),
				'condition' => array(
					'featured' => 'yes',
				),
			)
		);

		$dir = is_rtl() ? 'right' : 'left';

		$this->add_responsive_control(
			'ribbons_hor',
			array(
				'label'      => __( 'Horizontal Offset', 'trx_addons' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 500,
					),
				),
				'conditions' => array(
					'relation' => 'and',
					'terms'    => array(
						// array(
						// 	'name'     => '_skin',
						// 	'operator' => '===',
						// 	'value'    => 'grid-9',
						// ),
						array(
							'relation' => 'or',
							'terms'    => array(
								array(
									'name'  => 'sale',
									'value' => 'yes',
								),
								array(
									'name'  => 'featured',
									'value' => 'yes',
								),
							),
						),
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .trx-addons-woo-products-ribbon-container' => $dir . ': {{SIZE}}{{UNIT}}; transform: translateX(0)',
				),
			)
		);

		$this->add_responsive_control(
			'ribbons_ver',
			array(
				'label'      => __( 'Vertical Offset', 'trx_addons' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 500,
					),
				),
				'conditions' => array(
					'relation' => 'and',
					'terms'    => array(
						// array(
						// 	'name'     => '_skin',
						// 	'operator' => '===',
						// 	'value'    => 'grid-9',
						// ),
						array(
							'relation' => 'or',
							'terms'    => array(
								array(
									'name'  => 'sale',
									'value' => 'yes',
								),
								array(
									'name'  => 'featured',
									'value' => 'yes',
								),
							),
						),
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .trx-addons-woo-products-ribbon-container' => 'top: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'ribbons_spacing',
			array(
				'label'      => __( 'Spacing', 'trx_addons' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array(
						'min' => -10,
						'max' => 200,
					),
				),
				'conditions' => array(
					'relation' => 'and',
					'terms'    => array(
						// array(
						// 	'name'  => '_skin',
						// 	'value' => 'grid-9',
						// ),
						array(
							'name'  => 'sale',
							'value' => 'yes',
						),
						array(
							'name'  => 'featured',
							'value' => 'yes',
						),
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .trx-addons-woo-products-product-featured-wrap' => 'margin-top: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_sold_out_trx_settings',
			array(
				'label'     => __( 'Out Of Stock Ribbon', 'trx_addons' ),
			)
		);

		$this->add_control(
			'sold_out',
			array(
				'label' => __( 'Show Ribbon', 'trx_addons' ),
				'type'  => Controls_Manager::SWITCHER,
				'default' => 'yes',
			)
		);

		$this->add_control(
			'sold_out_string',
			array(
				'label'     => __( 'String', 'trx_addons' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'Out Of Stock', 'trx_addons' ),
				'condition' => array(
					'sold_out' => 'yes',
				),
			)
		);

		$this->end_controls_section();
	}

    /*-----------------------------------------------------------------------------------*/
	/*	STYLE TAB
	/*-----------------------------------------------------------------------------------*/

	/**
	 * Style tab: Products
	 *
	 * @return void
	 */
	public function register_style_general_section() {

		$this->start_controls_section(
			'section_design_layout',
			array(
				'label'     => __( 'Products', 'trx_addons' ),
				'tab'       => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'product_list_style_heading',
			array(
				'label'     => __( 'Product List', 'trx_addons' ),
				'type'      => Controls_Manager::HEADING,
			)
		);

		$this->add_responsive_control(
			'rows_spacing',
			array(
				'label'       => __( 'Rows Spacing', 'trx_addons' ),
				'type'        => Controls_Manager::SLIDER,
				'range'       => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'render_type' => 'template',
				'selectors'   => array(
					'{{WRAPPER}} .trx-addons-woo-products li.product' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				),
				'condition'   => array(
					'layout_type' => array( 'grid', 'masonry' ),
				),
			)
		);

		$this->add_responsive_control(
			'columns_spacing',
			array(
				'label'     => __( 'Columns Spacing', 'trx_addons' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => array(
					'size' => 10,
				),
				'range'     => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .trx-addons-woo-products li.product' => 'padding-right: calc( {{SIZE}}{{UNIT}}/2 ); padding-left: calc( {{SIZE}}{{UNIT}}/2 );',
					'{{WRAPPER}} .trx-addons-woo-products ul.products' => 'margin-left: calc( -{{SIZE}}{{UNIT}}/2 ); margin-right: calc( -{{SIZE}}{{UNIT}}/2 );',
				),
			)
		);

		$this->add_control(
			'product_item_style_heading',
			array(
				'label'     => __( 'Product Item', 'trx_addons' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array(
					'_skin!' => 'grid-4',
				),
			)
		);

		$this->start_controls_tabs( 'product_style_tabs' );

		$this->start_controls_tab(
			'product_style_normal',
			array(
				'label' => __( 'Normal', 'trx_addons' ),
				'condition' => array(
					'_skin!' => 'grid-4',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'product_background',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .trx-addons-woo-products-product-wrapper',
				'condition' => array(
					'_skin!' => 'grid-4',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'product_border',
				'selector' => '{{WRAPPER}} .trx-addons-woo-products-product-wrapper',
				'condition' => array(
					'_skin!' => 'grid-4',
				),
			)
		);

		$this->add_control(
			'product_radius',
			array(
				'label'      => __( 'Border Radius', 'trx_addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .trx-addons-woo-products-product-wrapper' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
				'condition' => array(
					'_skin!' => 'grid-4',
				),
			)
		);

		$this->add_responsive_control(
			'product_card_padding',
			array(
				'label'      => __( 'Padding', 'trx_addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors'  => array(
					'{{WRAPPER}} .trx-addons-woo-products-product-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition' => array(
					'_skin!' => 'grid-4',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'product_shadow',
				'selector' => '{{WRAPPER}} .trx-addons-woo-products-product-wrapper',
				'condition' => array(
					'_skin!' => 'grid-4',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'product_style_hover',
			array(
				'label' => __( 'Hover', 'trx_addons' ),
				'condition' => array(
					'_skin!' => 'grid-4',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'product_hover_background',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .trx-addons-woo-products-product-wrapper:hover',
				'condition' => array(
					'_skin!' => 'grid-4',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'product_hover_border',
				'selector' => '{{WRAPPER}} .trx-addons-woo-products-product-wrapper:hover',
				'condition' => array(
					'_skin!' => 'grid-4',
				),
			)
		);

		$this->add_control(
			'product_hover_radius',
			array(
				'label'      => __( 'Border Radius', 'trx_addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .trx-addons-woo-products-product-wrapper:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
				'condition' => array(
					'_skin!' => 'grid-4',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'product_hover_shadow',
				'selector' => '{{WRAPPER}} .trx-addons-woo-products-product-wrapper:hover',
				'condition' => array(
					'_skin!' => 'grid-4',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * Style tab: Image
	 *
	 * @return void
	 */
	public function register_style_image_section() {

		$this->start_controls_section(
			'section_image_style',
			array(
				'label'     => __( 'Image', 'trx_addons' ),
				'tab'       => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'hover_style',
			array(
				'label'   => __( 'Image Hover Style', 'trx_addons' ),
				'type'    => Controls_Manager::SELECT,
				'options' => array(
					''        => __( 'None', 'trx_addons' ),
					'swap'    => __( 'Swap Images', 'trx_addons' ),
					'zoomin'  => __( 'Zoom In', 'trx_addons' ),
					'zoomout' => __( 'Zoom Out', 'trx_addons' ),
					'scale'   => __( 'Scale', 'trx_addons' ),
					'gray'    => __( 'Grayscale', 'trx_addons' ),
					'bright'  => __( 'Bright', 'trx_addons' ),
					'sepia'   => __( 'Sepia', 'trx_addons' ),
					'trans'   => __( 'Translate', 'trx_addons' ),
					'custom'  => __( 'Custom', 'trx_addons' ),
				),
				'default' => 'swap',
			)
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			array(
				'name'      => 'hover_css_filters',
				'selector'  => '{{WRAPPER}} li:hover .trx-addons-woo-products-product-thumbnail img',
				'condition' => array(
					'hover_style' => 'custom',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'product_img_border',
				'selector' => '{{WRAPPER}} .woocommerce-loop-product__link, {{WRAPPER}} .trx-addons-woo-products-product-gallery-images img',
			)
		);

		$this->add_responsive_control(
			'product_img_height',
			array(
				'label'      => __( 'Height', 'trx_addons' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 1000,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .trx-addons-woo-products .woocommerce-loop-product__link img' => 'height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'product_img_fit',
			array(
				'label'     => __( 'Image Fit', 'trx_addons' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'cover',
				'options'   => array(
					'fill'    => __( 'Fill', 'trx_addons' ),
					'cover'   => __( 'Cover', 'trx_addons' ),
					'contain' => __( 'Contain', 'trx_addons' ),
				),
				'selectors' => array(
					'{{WRAPPER}} .trx-addons-woo-products .woocommerce-loop-product__link img' => 'object-fit: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'product_img_border_radius',
			array(
				'label'      => __( 'Border Radius', 'trx_addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors'  => array(
					'{{WRAPPER}} .trx-addons-woo-products-product-prime-image' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'product_image_spacing',
			array(
				'label'     => __( 'Spacing', 'trx_addons' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'max' => 100,
					),
				),
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors' => array(
					'{{WRAPPER}} .trx-addons-woo-products-product-thumbnail' => 'margin-bottom: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->start_controls_tabs( 'product_thumbnail_effects_tabs' );

		$this->start_controls_tab(
			'normal',
			array(
				'label'     => __( 'Normal', 'trx_addons' ),
			)
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			array(
				'name'      => 'thumbnail_filters',
				'selector'  => '{{WRAPPER}} .trx-addons-woo-products-product-prime-image',
			)
		);

		$this->add_group_control(
			TransitionControl::get_type(),
			array(
				'name'      => 'image_transition',
				'selector'  => '{{WRAPPER}} .trx-addons-woo-products-product-prime-image',
				'separator' => '',
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'hover',
			array(
				'label'     => __( 'Hover', 'trx_addons' ),
			)
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			array(
				'name'      => 'thumbnail_hover_filters',
				'selector'  => '{{WRAPPER}} .product:hover .trx-addons-woo-products-product-prime-image',
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * Style Tab: Box
	 */
	protected function register_style_box_controls() {
		$this->start_controls_section(
			'section_product_box_style',
			array(
				'label' => __( 'Info Box', 'trx_addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->start_controls_tabs( 'tabs_product_box_style' );

		$this->start_controls_tab(
			'tab_product_box_normal',
			array(
				'label' => __( 'Normal', 'trx_addons' ),
			)
		);

		$this->add_control(
			'product_box_bg',
			array(
				'label'     => __( 'Background Color', 'trx_addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .trx-addons-products-info-box' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'        => 'product_box_border',
				'label'       => __( 'Border', 'trx_addons' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .trx-addons-products-info-box',
			)
		);

		$this->add_responsive_control(
			'product_box_border_radius',
			array(
				'label'      => __( 'Border Radius', 'trx_addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors'  => array(
					'{{WRAPPER}} .trx-addons-products-info-box' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'product_box_padding',
			array(
				'label'      => __( 'Padding', 'trx_addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'range'          => array(
					'em' => array(
						'min'  => 0,
						'max'  => 5,
						'step' => 0.1,
					),
				),
				'default'    => array(
					'top'      => '1',
					'right'    => '1.2',
					'bottom'   => '1',
					'left'     => '1.2',
					'unit'     => 'em',
					'isLinked' => false
				),
				'selectors'  => array(
					'{{WRAPPER}} .trx-addons-products-info-box' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'product_box_shadow',
				'selector' => '{{WRAPPER}} .trx-addons-products-info-box',
				'fields_options' => [
					'box_shadow_type' => [
						'prefix_class' => 'trx-addons-products-with-box-shadow-',
					],
					'box_shadow_position' => [
						'prefix_class' => 'trx-addons-products-box-shadow-position-',
					],
					'box_shadow' => [
						'selectors' => [
							'{{SELECTOR}}' => 'box-shadow: {{HORIZONTAL}}px {{VERTICAL}}px {{BLUR}}px {{SPREAD}}px {{COLOR}} {{box_shadow_position.VALUE}};',
						],
					],
				],
				'condition' => array(
					'_skin' => array( 'grid-4' )
				)
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_product_box_hover',
			array(
				'label' => __( 'Hover', 'trx_addons' ),
			)
		);

		$this->add_control(
			'product_box_bg_hover',
			array(
				'label'     => __( 'Background Color', 'trx_addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .product:hover .trx-addons-products-info-box' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'product_box_border_color_hover',
			array(
				'label'     => __( 'Border Color', 'trx_addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .product:hover .trx-addons-products-info-box' => 'border-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'product_box_shadow_hover',
				'selector' => '{{WRAPPER}} .product:hover .trx-addons-products-info-box',
				'condition' => array(
					'_skin' => array( 'grid-4' )
				)
			)
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->add_group_control(
			TransitionControl::get_type(),
			array(
				'name'      => 'product_box_transition',
				'selector'  => '{{WRAPPER}} .trx-addons-products-info-box',
				'separator' => '',
			)
		);

		$this->add_responsive_control(
			'product_box_width',
			array(
				'label'      => __( 'Width', 'trx_addons' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'range'      => array(
					'%'  => array(
						'min'  => 1,
						'max'  => 100,
						'step' => 1,
					),
					'px' => array(
						'min'  => 10,
						'max'  => 200,
						'step' => 1,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .trx-addons-products-info-box' => 'width: {{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					'_skin' => array( 'grid-4' )
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Style tab: Pagination
	 *
	 * @return void
	 */
	public function register_style_pagination_section() {
		$this->start_controls_section(
			'section_pagination_style',
			array(
				'label'     => __( 'Pagination', 'trx_addons' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'layout_type' => array( 'grid', 'masonry' ),
					'load_more!'  => 'yes',
					'pagination'  => 'yes',
					'query_type!' => array( 'cross-sells', 'up-sells' ),
				),
			)
		);

		$this->add_responsive_control(
			'pagination_padding',
			array(
				'label'      => __( 'Padding', 'trx_addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .trx-addons-woo-products-pagination ul li .page-numbers' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'pagination_typography',
				'global'   => array(
					'default' => Global_Typography::TYPOGRAPHY_ACCENT,
				),
				'selector' => '{{WRAPPER}} .trx-addons-woo-products-pagination ul li > .page-numbers',
			)
		);

		$this->start_controls_tabs( 'pagination_style_tabs' );

		$this->start_controls_tab(
			'pagination_style_normal',
			array(
				'label' => __( 'Normal', 'trx_addons' ),
			)
		);

		$this->add_control(
			'pagination_color',
			array(
				'label'     => __( 'Color', 'trx_addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .trx-addons-woo-products-pagination ul li .page-numbers' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'pagination_background',
			array(
				'label'     => __( 'Background Color', 'trx_addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .trx-addons-woo-products-pagination ul li .page-numbers' => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'pagination_border',
				'selector' => '{{WRAPPER}} .trx-addons-woo-products-pagination ul li .page-numbers',
			)
		);

		$this->add_control(
			'pagination_radius',
			array(
				'label'      => __( 'Border Radius', 'trx_addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .trx-addons-woo-products-pagination ul li .page-numbers' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'pagination_style_hover',
			array(
				'label' => __( 'Hover', 'trx_addons' ),
			)
		);

		$this->add_control(
			'pagination_hover_color',
			array(
				'label'     => __( 'Color', 'trx_addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .trx-addons-woo-products-pagination ul li .page-numbers:hover' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'pagination_hover_background',
			array(
				'label'     => __( 'Background Color', 'trx_addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .trx-addons-woo-products-pagination ul li .page-numbers:hover' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'pagination_hover_border',
				'selector' => '{{WRAPPER}} .trx-addons-woo-products-pagination ul li .page-numbers:hover',
			)
		);

		$this->add_control(
			'pagination_hover_radius',
			array(
				'label'      => __( 'Border Radius', 'trx_addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .trx-addons-woo-products-pagination ul li .page-numbers:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'pagination_style_active',
			array(
				'label' => __( 'Active', 'trx_addons' ),
			)
		);

		$this->add_control(
			'pagination_active_color',
			array(
				'label'     => __( 'Color', 'trx_addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .trx-addons-woo-products-pagination ul li span.current' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'pagination_active_background',
			array(
				'label'     => __( 'Background Color', 'trx_addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .trx-addons-woo-products-pagination ul li span.current' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'pagination_active_border',
				'selector' => '{{WRAPPER}} .trx-addons-woo-products-pagination ul li span.current',
			)
		);

		$this->add_control(
			'pagination_active_radius',
			array(
				'label'      => __( 'Border Radius', 'trx_addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .trx-addons-woo-products-pagination ul li span.current' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * Style tab: Load More Button
	 *
	 * @return void
	 */
	public function register_style_loadmore_section() {

		$this->start_controls_section(
			'button_style_settings',
			array(
				'label'     => __( 'Load More Button', 'trx_addons' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'layout_type' => array( 'grid', 'masonry' ),
					'load_more'   => 'yes',
					'pagination!' => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'button_typo',
				'global'   => array(
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				),
				'selector' => '{{WRAPPER}} .trx-addons-woo-products-load-more-btn',
			)
		);

		$this->start_controls_tabs( 'button_style_tabs' );

		$this->start_controls_tab(
			'button_style_normal',
			array(
				'label' => __( 'Normal', 'trx_addons' ),
			)
		);

		$this->add_control(
			'button_color',
			array(
				'label'     => __( 'Text Color', 'trx_addons' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .trx-addons-woo-products-load-more-btn'  => 'color: {{VALUE}};',
					'{{WRAPPER}} .trx-addons-woo-products-load-more-btn .trx-addons-woo-products-loader'  => 'border-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name'     => 'button_text_shadow',
				'selector' => '{{WRAPPER}} .trx-addons-woo-products-load-more-btn',
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'           => 'button_background',
				'types'          => array( 'classic', 'gradient' ),
				'selector'       => '{{WRAPPER}} .trx-addons-woo-products-load-more-btn',
				'fields_options' => array(
					'background' => array(
						'default' => 'classic',
					),
					'color'      => array(
						'global' => array(
							'default' => Global_Colors::COLOR_PRIMARY,
						),
					),
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'button_border',
				'selector' => '{{WRAPPER}} .trx-addons-woo-products-load-more-btn',
			)
		);

		$this->add_responsive_control(
			'button_border_radius',
			array(
				'label'      => __( 'Border Radius', 'trx_addons' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .trx-addons-woo-products-load-more-btn' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'button_box_shadow',
				'selector' => '{{WRAPPER}} .trx-addons-woo-products-load-more-btn',
			)
		);

		$this->add_responsive_control(
			'button_margin',
			array(
				'label'      => __( 'Margin', 'trx_addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .trx-addons-woo-products-load-more' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'button_padding',
			array(
				'label'      => __( 'Padding', 'trx_addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .trx-addons-woo-products-load-more-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'button_style_hover',
			array(
				'label' => __( 'Hover', 'trx_addons' ),
			)
		);

		$this->add_control(
			'button_hover_color',
			array(
				'label'     => __( 'Text Hover Color', 'trx_addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .trx-addons-woo-products-load-more-btn:hover'  => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name'     => 'button_text_shadow_hover',
				'selector' => '{{WRAPPER}} .trx-addons-woo-products-load-more-btn:hover',
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'button_background_hover',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .trx-addons-woo-products-load-more-btn:hover',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'button_border_hover',
				'selector' => '{{WRAPPER}} .trx-addons-woo-products-load-more-btn:hover',
			)
		);

		$this->add_responsive_control(
			'button_border_radius_hover',
			array(
				'label'      => __( 'Border Radius', 'trx_addons' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .trx-addons-woo-products-load-more-btn:hover' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'button_shadow_hover',
				'selector' => '{{WRAPPER}} .trx-addons-woo-products-load-more-btn:hover',
			)
		);

		$this->add_responsive_control(
			'button_margin_hover',
			array(
				'label'      => __( 'Margin', 'trx_addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .trx-addons-woo-products-load-more-btn:hover' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'button_padding_hover',
			array(
				'label'      => __( 'Padding', 'trx_addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .trx-addons-woo-products-load-more-btn:hover' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * Style tab: Sale Ribbon
	 *
	 * @return void
	 */
	public function register_style_sale_controls() {

		$this->start_controls_section(
			'section_sale_style',
			array(
				'label'     => __( 'Sale Ribbon', 'trx_addons' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'sale'   => 'yes',
					// $this->get_control_id( 'product_image' ) => 'yes', >> found in skin 9
				),
			)
		);

		$this->add_responsive_control(
			'sale_size',
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
					'{{WRAPPER}} .trx-addons-woo-products:not(.trx-addons-woo-products-skin-grid-10) .trx-addons-woo-products-product-sale-wrap .trx-addons-woo-products-product-onsale' => 'min-height: {{SIZE}}{{UNIT}}; min-width: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'_skin!' => 'grid-10',
				),
			)
		);

		$this->add_control(
			'sale_color',
			array(
				'label'     => __( 'Color', 'trx_addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'white',
				'selectors' => array(
					'{{WRAPPER}} .trx-addons-woo-products-product-sale-wrap .trx-addons-woo-products-product-onsale' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'sale_background',
			array(
				'label'     => __( 'Background Color', 'trx_addons' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .trx-addons-woo-products:not(.trx-addons-woo-products-skin-grid-10) .trx-addons-woo-products-product-sale-wrap .trx-addons-woo-products-product-onsale, {{WRAPPER}} .trx-addons-woo-products.trx-addons-woo-products-skin-grid-10 .trx-addons-woo-products-product-sale-wrap' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .trx-addons-woo-products.trx-addons-woo-products-skin-grid-10 .trx-addons-woo-products-product-sale-wrap::after' => 'border-left-color:{{VALUE}}; border-right-color:{{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name'     => 'sale_text_shadow',
				'selector' => '{{WRAPPER}} .trx-addons-woo-products-product-sale-wrap .trx-addons-woo-products-product-onsale',
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'sale_shadow',
				'selector' => '{{WRAPPER}} .trx-addons-woo-products:not(.trx-addons-woo-products-skin-grid-10) .trx-addons-woo-products-product-sale-wrap .trx-addons-woo-products-product-onsale, {{WRAPPER}} .trx-addons-woo-products.trx-addons-woo-products-skin-grid-10 .trx-addons-woo-products-product-sale-wrap',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'           => 'sale_typography',
				'global'         => array(
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				),
				'fields_options' => array(
					'font_size'   => array(
						'selectors' => array(
							'{{WRAPPER}} .trx-addons-woo-products:not(.trx-addons-woo-products-skin-grid-10) .trx-addons-woo-products-product-sale-wrap .trx-addons-woo-products-product-onsale' => 'font-size: {{SIZE}}{{UNIT}};',
							'{{WRAPPER}} .trx-addons-woo-products.trx-addons-woo-products-skin-grid-10 .trx-addons-woo-products-product-sale-wrap' => 'width: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}};',
							'{{WRAPPER}} .trx-addons-woo-products.trx-addons-woo-products-skin-grid-10 .trx-addons-woo-products-product-sale-wrap .trx-addons-woo-products-product-onsale' => 'font-size: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}};',
							'{{WRAPPER}} .trx-addons-woo-products.trx-addons-woo-products-skin-grid-10 .trx-addons-woo-products-product-sale-wrap::after' => 'border-left-width: calc( {{SIZE}}{{UNIT}} / 2); border-right-width: calc( {{SIZE}}{{UNIT}} / 2);',
						),
					),
					'line_height' => array(
						'default'   => array(
							'size' => '32',
							'unit' => 'px',
						),
						'selectors' => array(
							'{{WRAPPER}} .trx-addons-woo-products:not(.trx-addons-woo-products-skin-grid-10) .trx-addons-woo-products-product-sale-wrap .trx-addons-woo-products-product-onsale' => 'line-height: {{SIZE}}{{UNIT}};',
							'{{WRAPPER}} .trx-addons-woo-products.trx-addons-woo-products-skin-grid-10 .trx-addons-woo-products-product-sale-wrap' => 'width: {{SIZE}}{{UNIT}};',
							'{{WRAPPER}} .trx-addons-woo-products.trx-addons-woo-products-skin-grid-10 .trx-addons-woo-products-product-sale-wrap::after' => 'border-left-width: calc( {{SIZE}}{{UNIT}} / 2); border-right-width: calc( {{SIZE}}{{UNIT}} / 2);',
						),
					),
				),
				'selector'       => '{{WRAPPER}} .trx-addons-woo-products-product-sale-wrap .trx-addons-woo-products-product-onsale',
			)
		);

		$this->add_control(
			'sales_notice',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => __( 'Use <b>Line Height</b> to control the ribbon size.', 'trx_addons' ),
				'content_classes' => 'papro-upgrade-notice',
				'condition'       => array(
					'_skin' => 'grid-10',
				),
			)
		);

		$this->add_responsive_control(
			'sale_radius',
			array(
				'label'      => __( 'Border Radius', 'trx_addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .trx-addons-woo-products:not(.trx-addons-woo-products-skin-grid-10) .trx-addons-woo-products-product-sale-wrap .trx-addons-woo-products-product-onsale' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
				'condition'  => array(
					'_skin!' => 'grid-10',
				),
			)
		);

		$this->add_responsive_control(
			'sale_padding',
			array(
				'label'      => __( 'Padding', 'trx_addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .trx-addons-woo-products:not(.trx-addons-woo-products-skin-grid-10) .trx-addons-woo-products-product-sale-wrap .trx-addons-woo-products-product-onsale' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'  => array(
					'_skin!' => 'grid-10',
				),
			)
		);

		$this->add_responsive_control(
			'sale_padding_10',
			array(
				'label'              => __( 'Padding', 'trx_addons' ),
				'type'               => Controls_Manager::DIMENSIONS,
				'allowed_dimensions' => 'vertical',
				'size_units'         => array( 'px', 'em', '%' ),
				'selectors'          => array(
					'{{WRAPPER}} .trx-addons-woo-products.trx-addons-woo-products-skin-grid-10 .trx-addons-woo-products-product-sale-wrap .trx-addons-woo-products-product-onsale' => 'padding: {{TOP}}{{UNIT}} 0 {{BOTTOM}}{{UNIT}} 0;',
				),
				'condition'          => array(
					'_skin' => 'grid-10',
				),
			)
		);

		$this->add_responsive_control(
			'sale_margin',
			array(
				'label'      => __( 'Margin', 'trx_addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'default'    => array(
					'top'      => '1',
					'bottom'   => '1',
					'left'     => '1',
					'right'    => '1',
					'unit'     => 'em',
					'isLinked' => true
				),
				'selectors'  => array(
					'{{WRAPPER}} .trx-addons-woo-products:not(.trx-addons-woo-products-skin-grid-10) .trx-addons-woo-products-product-sale-wrap .trx-addons-woo-products-product-onsale, {{WRAPPER}} .trx-addons-woo-products.trx-addons-woo-products-skin-grid-10 .trx-addons-woo-products-product-sale-wrap' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Style tab: Featured Ribbon
	 *
	 * @return void
	 */
	public function register_style_featured_controls() {

		$this->start_controls_section(
			'section_featured_style',
			array(
				'label'     => __( 'Featured Ribbon', 'trx_addons' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'featured' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'featured_size',
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
					'{{WRAPPER}} .trx-addons-woo-products:not(.trx-addons-woo-products-skin-grid-10) .trx-addons-woo-products-product-featured-wrap .trx-addons-woo-products-product-featured' => 'min-height: {{SIZE}}{{UNIT}}; min-width: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'_skin!' => 'grid-10',
				),
			)
		);

		$this->add_control(
			'featured_color',
			array(
				'label'     => __( 'Color', 'trx_addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .trx-addons-woo-products-product-featured-wrap .trx-addons-woo-products-product-featured' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'featured_background',
			array(
				'label'     => __( 'Background Color', 'trx_addons' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'selectors' => array(
					// '{{WRAPPER}} .trx-addons-woo-products:not(.trx-addons-woo-products-skin-grid-10) .trx-addons-woo-products-product-featured-wrap .trx-addons-woo-products-product-featured, {{WRAPPER}} .trx-addons-woo-products.trx-addons-woo-products-skin-grid-10 .trx-addons-woo-products-product-featured-wrap' => 'background-color: {{VALUE}};',
					'{{WRAPPER}}:not([data-widget_type="trx-addons-woo-products-products.grid-10"]) .trx-addons-woo-products-product-featured-wrap .trx-addons-woo-products-product-featured, {{WRAPPER}}[data-widget_type="trx-addons-woo-products-products.grid-10"] .trx-addons-woo-products-product-featured-wrap' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .trx-addons-woo-products.trx-addons-woo-products-skin-grid-10 .trx-addons-woo-products-product-featured-wrap::after' => 'border-left-color:{{VALUE}}; border-right-color:{{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name'     => 'featured_text_shadow',
				'selector' => '{{WRAPPER}} .trx-addons-woo-products-product-featured-wrap .trx-addons-woo-products-product-featured',
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'featured_shadow',
				'selector' => '{{WRAPPER}}:not([data-widget_type="trx-addons-woo-products-products.grid-10"]) .trx-addons-woo-products-product-featured-wrap .trx-addons-woo-products-product-featured, {{WRAPPER}}[data-widget_type="trx-addons-woo-products-products.grid-10"] .trx-addons-woo-products-product-featured-wrap',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'           => 'featured_typography',
				'global'         => array(
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				),
				'fields_options' => array(
					'font_size'   => array(
						'selectors' => array(
							'{{WRAPPER}} .trx-addons-woo-products:not(.trx-addons-woo-products-skin-grid-10) .trx-addons-woo-products-product-featured-wrap' => 'font-size: {{SIZE}}{{UNIT}};',
							'{{WRAPPER}} .trx-addons-woo-products.trx-addons-woo-products-skin-grid-10 .trx-addons-woo-products-product-featured-wrap' => 'width: {{SIZE}}{{UNIT}};  line-height: {{SIZE}}{{UNIT}};',
							'{{WRAPPER}} .trx-addons-woo-products.trx-addons-woo-products-skin-grid-10 .trx-addons-woo-products-product-featured-wrap .trx-addons-woo-products-product-featured' => 'font-size: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}};',
							'{{WRAPPER}} .trx-addons-woo-products.trx-addons-woo-products-skin-grid-10 .trx-addons-woo-products-product-featured-wrap::after' => 'border-left-width: calc( {{SIZE}}{{UNIT}} / 2); border-right-width: calc( {{SIZE}}{{UNIT}} / 2);',
						),
					),
					'line_height' => array(
						'default'   => array(
							'size' => '32',
							'unit' => 'px',
						),
						'selectors' => array(
							'{{WRAPPER}} .trx-addons-woo-products:not(.trx-addons-woo-products-skin-grid-10) .trx-addons-woo-products-product-featured-wrap' => 'line-height: {{SIZE}}{{UNIT}};',
							'{{WRAPPER}} .trx-addons-woo-products.trx-addons-woo-products-skin-grid-10 .trx-addons-woo-products-product-featured-wrap' => 'width: {{SIZE}}{{UNIT}};',
							'{{WRAPPER}} .trx-addons-woo-products.trx-addons-woo-products-skin-grid-10 .trx-addons-woo-products-product-featured-wrap::after' => 'border-left-width: calc( {{SIZE}}{{UNIT}} / 2); border-right-width: calc( {{SIZE}}{{UNIT}} / 2);',
						),
					),
				),
				'selector'       => '{{WRAPPER}} .trx-addons-woo-products-product-featured-wrap .trx-addons-woo-products-product-featured',
			)
		);

		$this->add_control(
			'featured_notice',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => __( 'Use <b>Line Height</b> to control the ribbon size.', 'trx_addons' ),
				'content_classes' => 'papro-upgrade-notice',
				'condition'       => array(
					'_skin' => 'grid-10',
				),
			)
		);

		$this->add_responsive_control(
			'featured_radius',
			array(
				'label'      => __( 'Border Radius', 'trx_addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .trx-addons-woo-products-product-featured-wrap .trx-addons-woo-products-product-featured' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'  => array(
					'_skin!' => 'grid-10',
				),
			)
		);

		$this->add_responsive_control(
			'featured_padding',
			array(
				'label'      => __( 'Padding', 'trx_addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .trx-addons-woo-products-product-featured-wrap .trx-addons-woo-products-product-featured' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'  => array(
					'_skin!' => 'grid-10',
				),
			)
		);

		$this->add_responsive_control(
			'featured_padding_10',
			array(
				'label'              => __( 'Padding', 'trx_addons' ),
				'type'               => Controls_Manager::DIMENSIONS,
				'allowed_dimensions' => 'vertical',
				'size_units'         => array( 'px', 'em', '%' ),
				'selectors'          => array(
					'{{WRAPPER}} .trx-addons-woo-products-product-featured-wrap .trx-addons-woo-products-product-featured' => 'padding: {{TOP}}{{UNIT}} 0 {{BOTTOM}}{{UNIT}} 0;',
				),
				'condition'          => array(
					'_skin' => 'grid-10',
				),
			)
		);

		$this->add_responsive_control(
			'featured_margin',
			array(
				'label'      => __( 'Margin', 'trx_addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .trx-addons-woo-products-product-featured-wrap .trx-addons-woo-products-product-featured' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Style tab: Carousel
	 *
	 * @return void
	 */
	public function register_style_carousel_section() {

		$this->start_controls_section(
			'section_carousel_style',
			array(
				'label'     => __( 'Carousel', 'trx_addons' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'layout_type' => 'carousel',
				),
			)
		);

		$this->add_control(
			'content_carousel_arrows',
			array(
				'label'     => esc_html__( 'Arrows', 'trx_addons' ),
				'type'      => Controls_Manager::HEADING,
				'condition' => array(
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
					'arrows' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'arrows_position',
			array(
				'label'      => __( 'Arrows Position', 'trx_addons' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors'  => array(
					'{{WRAPPER}} .trx-addons-woo-products-prime-arrow-next' => 'right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .trx-addons-woo-products-prime-arrow-prev' => 'left: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
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
					'arrows' => 'yes',
				),
			)
		);

		$this->add_control(
			'content_carousel_dots',
			array(
				'label'     => esc_html__( 'Dots', 'trx_addons' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array(
					'dots' => 'yes',
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
					'dots' => 'yes',
				),
			]
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
					'{{WRAPPER}} .swiper-container-wrap .swiper-pagination-bullet' => 'height: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}}',
				),
				'condition'  => array(
					'dots' => 'yes',
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
					'{{WRAPPER}} .swiper-container-wrap .swiper-pagination-bullet' => 'margin-left: {{SIZE}}{{UNIT}}; margin-right: {{SIZE}}{{UNIT}}',
				),
				'condition'  => array(
					'dots' => 'yes',
				),
			)
		);

		$this->start_controls_tabs( 'tabs_dots_style' );

		$this->start_controls_tab(
			'tab_dots_normal',
			array(
				'label'     => __( 'Normal', 'trx_addons' ),
				'condition' => array(
					'dots' => 'yes',
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
					'dots' => 'yes',
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
					'dots' => 'yes',
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
					'dots' => 'yes',
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
					'dots' => 'yes',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_dots_active',
			array(
				'label'     => __( 'Active', 'trx_addons' ),
				'condition' => array(
					'dots' => 'yes',
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
					'dots' => 'yes',
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
					'dots' => 'yes',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_dots_hover',
			array(
				'label'     => __( 'Hover', 'trx_addons' ),
				'condition' => array(
					'dots' => 'yes',
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
					'dots' => 'yes',
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
					'dots' => 'yes',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * Style tab: Out Of Stock Ribbon
	 *
	 * @return void
	 */
	public function register_style_sold_out_controls() {

		$this->start_controls_section(
			'section_sold_out_style',
			array(
				'label'     => __( 'Out Of Stock Ribbon', 'trx_addons' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'sold_out' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'sold_out_width',
			array(
				'label'      => __( 'Size', 'trx_addons' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%' ),
				'range'      => array(
					'px' => array(
						'min' => 20,
						'max' => 200,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .trx-addons-woo-products-out-of-stock' => 'width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'sold_out_hor',
			array(
				'label'      => __( 'Horizontal Offset', 'trx_addons' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 200,
					),
				),
				'default'    => array(
					'size' => 9,
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .trx-addons-woo-products-out-of-stock' => 'left: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'sold_out_ver',
			array(
				'label'      => __( 'Vertical Offset', 'trx_addons' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 200,
					),
				),
				'default'    => array(
					'size' => 7,
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .trx-addons-woo-products-out-of-stock' => 'top: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'sold_out_typography',
				'global'   => array(
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				),
				'selector' => '{{WRAPPER}} .trx-addons-woo-products-out-of-stock',
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'sold_out_shadow',
				'selector' => '{{WRAPPER}} .trx-addons-woo-products-out-of-stock',
			)
		);

		$this->add_control(
			'sold_out_color',
			array(
				'label'     => __( 'Color', 'trx_addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'white',
				'selectors' => array(
					'{{WRAPPER}} .trx-addons-woo-products-out-of-stock' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'sold_out_background',
			array(
				'label'     => __( 'Background Color', 'trx_addons' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .trx-addons-woo-products-out-of-stock' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'sold_out_radius',
			array(
				'label'      => __( 'Border Radius', 'trx_addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .trx-addons-woo-products-out-of-stock' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
			)
		);

		$this->add_responsive_control(
			'sold_out_padding',
			array(
				'label'      => __( 'Padding', 'trx_addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .trx-addons-woo-products-out-of-stock' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Get queries.
     * 
	 * @return void
	 */
	public function get_queries() {

		$query_type = array(
			'all'    => __( 'All Products', 'trx_addons' ),
			'custom' => __( 'Custom Query', 'trx_addons' ),
			'main'   => __( 'Main Query', 'trx_addons' ),
		);

		if ( defined( 'ELEMENTOR_PRO_VERSION' ) ) {
			$query_type['related']     = __( 'Related Products', 'trx_addons' );
			$query_type['cross-sells'] = __( 'Cross-Sells', 'trx_addons' );
			$query_type['up-sells']    = __( 'Upsells', 'trx_addons' );
		}

		return $query_type;
	}

	/**
	 * Get Woocommerce Categories.
	 *
	 * @param  mixed $id
	 * @return void
	 */
	protected function get_woo_categories( $id = 'slug' ) {

		$product_cat = array();

		$cat_args = array(
            'taxonomy'   => 'product_cat',
			'orderby'    => 'name',
			'order'      => 'asc',
			'hide_empty' => false,
		);

		$product_categories = get_terms( $cat_args );

		if ( ! empty( $product_categories ) ) {

			foreach ( $product_categories as $key => $category ) {

				$cat_id                 = 'slug' === $id ? $category->slug : $category->term_id;
				$product_cat[ $cat_id ] = $category->name;

			}
		}

		return $product_cat;
	}

	/**
	 * Get woo tags.
	 *
	 * @access protected
	 *
	 * @return array
	 */
	protected function get_woo_tags() {

		$product_tag = array();

		$tag_args = array(
            'taxonomy'  => 'product_tag',
			'orderby'    => 'name',
			'order'      => 'asc',
			'hide_empty' => false,
		);

		$terms = get_terms( $tag_args );

		if ( ! empty( $terms ) ) {

			foreach ( $terms as $key => $tag ) {

				$product_tag[ $tag->slug ] = $tag->name;
			}
		}

		return $product_tag;
	}

	/**
	 * Get posts list
	 *
	 * Used to set Premium_Post_Filter control default settings.
	 *
	 * @param  mixed $post_type
	 * @return void
	 */
	protected function get_default_posts_list( $post_type ) {

        global $wpdb;

		$list = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT ID, post_title FROM $wpdb->posts WHERE post_type = %s AND post_status = 'publish'",
                $post_type
            )
        );

        $options = array();

        if ( ! empty( $list ) ) {
            foreach ( $list as $post ) {
                $options[ $post->ID ] = $post->post_title;
            }
        }

		return $options;
	}
}