<?php
/**
 * WooProducts Module
 *
 * @package ThemeREX Addons
 * @since v2.30.1
 */

namespace TrxAddons\ElementorWidgets\Widgets\WooProducts;

use TrxAddons\ElementorWidgets\BaseWidgetModule;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * WooProducts module
 */
class WooProducts extends BaseWidgetModule {

	/**
	 * Constructor.
	 *
	 * Initializing the module base class.
	 */
	public function __construct() {

		parent::__construct();

		$this->assets = array(
			'css' => true,
			'js'  => true,
            'localize' => array( 'trx_addons_woo_products_script' => array(
				'ajax_url'                   => admin_url( 'admin-ajax.php' ),
				'products_nonce'             => wp_create_nonce( 'trx-addons-woo-products-pagination-nonce' ),
				'products_qv_nonce'          => wp_create_nonce( 'trx-addons-woo-products-qv-nonce' ),
				'products_add_to_cart_nonce' => wp_create_nonce( 'trx-addons-woo-products-add-to-cart-nonce' ),
			) ),
			'lib' => array(
				'css' => array(
					'editorstyle' => array( 'src' => '../WooProducts/assets/WooProducts.css' ),
				),
				'js'  => array(
					'isotope'      => array( 'src' => '../../../assets/isotope/isotope.pkgd.min.js' ),
					'flexslider'   => true,
					'imagesloaded' => true,
					'swiper'       => true,
					// 'editorscript' => array( 'src' => '../WooProducts/assets/WooProducts.js' ),
				)
			)
		);

		// Trigger AJAX Hooks for pagination.
		add_action( 'wp_ajax_trx_addons_action_get_product', array( $this, 'get_woo_products' ) );
		add_action( 'wp_ajax_nopriv_trx_addons_action_get_product', array( $this, 'get_woo_products' ) );

		// Trigger AJAX Hooks for product view.
		add_action( 'wp_ajax_trx_addons_woo_products_get_product_qv', array( $this, 'get_woo_product_quick_view' ) );
		add_action( 'wp_ajax_nopriv_trx_addons_woo_products_get_product_qv', array( $this, 'get_woo_product_quick_view' ) );

		// Trigger AJAX Hooks for add to cart.
		add_action( 'wp_ajax_trx_addons_woo_products_add_product_to_cart', array( $this, 'add_product_to_cart' ) );
		add_action( 'wp_ajax_nopriv_trx_addons_woo_products_add_product_to_cart', array( $this, 'add_product_to_cart' ) );
	}

	/**
	 * Get the name of the module
	 *
	 * @return string  The name of the module.
	 */
	public function get_name() {
		return 'woo-products';
	}

	/**
	 * Get the selector for the animation type 'Item by item'
	 * 
	 * @return string  The selector of the single item.
	 */
	public function get_separate_animation_selector() {
		return '.trx-addons-woo-products-container';
	}

	/**
	 * Get Woo Products.
	 *
	 * @access public
	 */
	public function get_woo_products() {

		check_ajax_referer( 'trx-addons-woo-products-pagination-nonce', 'nonce' );

		if ( ! isset( $_POST['pageID'] ) || ! isset( $_POST['elemID'] ) || ! isset( $_POST['skin'] ) ) {
			return;
		}

		$post_id   = sanitize_text_field( wp_unslash( $_POST['pageID'] ) );
		$widget_id = sanitize_text_field( wp_unslash( $_POST['elemID'] ) );
		$style_id  = sanitize_text_field( wp_unslash( $_POST['skin'] ) );

		$elementor = \Elementor\Plugin::instance();
		$meta      = $elementor->documents->get( $post_id )->get_elements_data();

		$widget_data = $this->find_element_recursive( $meta, $widget_id );

		$data = array(
			'message'    => __( 'Saved', 'trx_addons' ),
			'ID'         => '',
			'skin_id'    => '',
			'html'       => '',
			'pagination' => '',
		);

		if ( null !== $widget_data ) {

			// Restore default values.
			$widget = $elementor->elements_manager->create_element_instance( $widget_data );
			$skin = $widget->get_current_skin();
			$skin_body = $skin->render_ajax_post_body();
			$pagination = $skin->render_ajax_pagination();

			$data['ID']         = $widget->get_id();
			$data['skin_id']    = $widget->get_current_skin_id();
			$data['html']       = $skin_body;
			$data['pagination'] = $pagination;
		}

		wp_send_json_success( $data );
	}

	/**
	 * Get Woo Products View.
	 *
	 * @access public
	 */
	public function get_woo_product_quick_view() {

		check_ajax_referer( 'trx-addons-woo-products-qv-nonce', 'security' );

		if ( ! isset( $_REQUEST['product_id'] ) ) {
			die();
		}

		$post_id   = isset( $_POST['pageID'] ) ? sanitize_text_field( wp_unslash( $_POST['pageID'] ) ) : 0;
		$widget_id = isset( $_POST['elemID'] ) ? sanitize_text_field( wp_unslash( $_POST['elemID'] ) ) : 0;

		$elementor = \Elementor\Plugin::instance();
		$meta      = $elementor->documents->get( $post_id )->get_elements_data();

		$widget_data = $this->find_element_recursive( $meta, $widget_id );

		if ( null === $widget_data ) {

			wp_send_json_error( 'Widget settings not found.' );

		}

		// Restore default values.
		$widget = $elementor->elements_manager->create_element_instance( $widget_data );

		$settings = $widget->get_settings();

		$this->quick_view_content_actions();

		$product_id = intval( $_REQUEST['product_id'] );

		// set the main wp query for the product.
		wp( 'p=' . $product_id . '&post_type=product' );

		ob_start();

		while ( have_posts() ) :
			the_post();
		
			$post_id = get_the_ID();
		
			if ( ! $post_id || ! in_array( get_post_type( $post_id ), array( 'product', 'product_variation' ), true ) ) {
				return $classes;
			}
		
			$product = wc_get_product( $post_id );
		
			if ( $product ) {
				$classes[] = 'product';
				$classes[] = wc_get_loop_class();
				$classes[] = $product->get_stock_status();
		
				if ( $product->is_on_sale() ) {
					$classes[] = 'sale';
				}
				if ( $product->is_featured() ) {
					$classes[] = 'featured';
				}
				if ( $product->is_downloadable() ) {
					$classes[] = 'downloadable';
				}
				if ( $product->is_virtual() ) {
					$classes[] = 'virtual';
				}
				if ( $product->is_sold_individually() ) {
					$classes[] = 'sold-individually';
				}
				if ( $product->is_taxable() ) {
					$classes[] = 'taxable';
				}
				if ( $product->is_shipping_taxable() ) {
					$classes[] = 'shipping-taxable';
				}
				if ( $product->is_purchasable() ) {
					$classes[] = 'purchasable';
				}
				if ( $product->get_type() ) {
					$classes[] = 'product-type-' . $product->get_type();
				}
				if ( $product->is_type( 'variable' ) ) {
					if ( ! $product->get_default_attributes() ) {
						$classes[] = 'has-default-attributes';
					}
					if ( $product->has_child() ) {
						$classes[] = 'has-children';
					}
				}
			}
		
			$classes[] = $settings['qv_display'];
		
			$key = array_search( 'hentry', $classes, true );
			if ( false !== $key ) {
				unset( $classes[ $key ] );
			}
			?>
			<div class="trx-addons-woo-products-product">
				<div id="product-<?php echo esc_attr( $post_id ); ?>" class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>">
					<?php do_action( 'trx_addons_woo_products_qv_image' ); ?>
					<div class="trx-addons-woo-products-product-summary entry-summary">
						<?php do_action( 'trx_addons_woo_products_quick_view_product', $settings ); ?>
					</div>
				</div>
			</div>
			<?php
		endwhile;

		echo ob_get_clean(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		die();
	}

	/**
	 * Find Element Recursive.
	 *
	 * @access public
	 *
	 * @param array $elements  elements.
	 * @param int   $elem_id     element id.
	 *
	 * @return object|boolean
	 */
	public function find_element_recursive( $elements, $elem_id ) {

		foreach ( $elements as $element ) {
			if ( $elem_id === $element['id'] ) {
				return $element;
			}

			if ( ! empty( $element['elements'] ) ) {
				$element = $this->find_element_recursive( $element['elements'], $elem_id );

				if ( $element ) {
					return $element;
				}
			}
		}

		return false;
	}

	/**
	 * Quick View Content Actions.
	 *
	 * @access public
	 */
	public function quick_view_content_actions() {
		// Image.
		add_action( 'trx_addons_woo_products_qv_image', array( $this, 'product_quick_view_image_content' ), 20 );
		// Summary.
		add_action( 'trx_addons_woo_products_quick_view_product', array( $this, 'product_quick_view_content' ), 10 );
	}

	/**
	 * Product Quick View Image Content.
	 * Include qv image.
	 *
	 * @access public
	 */
	public function product_quick_view_image_content() {

		global $post, $product, $woocommerce;

		?>
		<div class="trx-addons-woo-products-qv-image-slider flexslider">

			<?php if( $product->is_on_sale() ) : ?>
				<div class="premium-qv-badge">
					<div class="corner">
						<span><?php echo __('Sale!', 'trx_addons'); ?></span>
					</div>
				</div>
			<?php endif; ?>
			<div class="trx-addons-woo-products-qv-slides slides">
				<?php
				if ( has_post_thumbnail() ) {
					$attachment_ids = $product->get_gallery_image_ids();
					$props          = wc_get_product_attachment_props( get_post_thumbnail_id(), $post );
					$image          = get_the_post_thumbnail(
						$post->ID,
						'shop_single',
						array(
							'title' => $props['title'],
							'alt'   => $props['alt'],
						)
					);
					printf(
						'<li class="woocommerce-product-gallery__image">%s</li>',
						wp_kses_post( $image )
					);

					if ( $attachment_ids ) {
						$loop = 0;

						foreach ( $attachment_ids as $attachment_id ) {

							$props = wc_get_product_attachment_props( $attachment_id, $post );

							if ( ! $props['url'] ) {
								continue;
							}

							echo wp_kses_post(
								sprintf(
									'<li>%s</li>',
									wp_get_attachment_image( $attachment_id, 'shop_single', 0, $props )
								)
							);

							++$loop;
						}
					}
				} else {

					printf( '<li><img src="%s" alt="%s" /></li>', wp_kses_post( wc_placeholder_img_src() ), esc_html( __( 'Placeholder', 'trx_addons' ) ) );
				}
				?>
			</div>

			<div class="trx-addons-woo-products-qv-slides-direction">
				<div class="trx-addons-slider-arrow trx-addons-arrow-prev elementor-swiper-button-prev swiper-button-prev-modal flex-prev">
					<svg aria-hidden="true" class="e-font-icon-svg e-fas-angle-left" viewBox="0 0 256 512" xmlns="http://www.w3.org/2000/svg"><path d="M31.7 239l136-136c9.4-9.4 24.6-9.4 33.9 0l22.6 22.6c9.4 9.4 9.4 24.6 0 33.9L127.9 256l96.4 96.4c9.4 9.4 9.4 24.6 0 33.9L201.7 409c-9.4 9.4-24.6 9.4-33.9 0l-136-136c-9.5-9.4-9.5-24.6-.1-34z"></path></svg>
				</div>
				<div class="trx-addons-slider-arrow trx-addons-arrow-next elementor-swiper-button-next swiper-button-next-modal flex-next">
					<svg aria-hidden="true" class="e-font-icon-svg e-fas-angle-right" viewBox="0 0 256 512" xmlns="http://www.w3.org/2000/svg"><path d="M224.3 273l-136 136c-9.4 9.4-24.6 9.4-33.9 0l-22.6-22.6c-9.4-9.4-9.4-24.6 0-33.9l96.4-96.4-96.4-96.4c-9.4-9.4-9.4-24.6 0-33.9L54.3 103c9.4-9.4 24.6-9.4 33.9 0l136 136c9.5 9.4 9.5 24.6.1 34z"></path></svg>
				</div>
			</div>
		</div><?php
	}

	/**
	 * Product Quick View Content.
	 * Gets product quick view content.
	 *
	 * @param array $settings
	 */
	public function product_quick_view_content( $settings ) {

		global $product;

		$post_id = $product->get_id();

		$single_structure = apply_filters(
			'trx_addons_woo_products_qv_structure',
			array(
				'title',
				'ratings',
				'price',
				'short_desc',
				'add_cart',
				'meta',
			)
		);

		if ( is_array( $single_structure ) && ! empty( $single_structure ) ) {

			foreach ( $single_structure as $value ) {

				switch ( true ) {
					case 'title' === $value:
						echo '<a href="' . esc_url( apply_filters( 'trx_addons_woo_products_product_title_link', get_the_permalink() ) ) . '" class="trx-addons-woo-products-product__link">';
							woocommerce_template_loop_product_title();
						echo '</a>';
						break;
					case 'price' === $value:
						woocommerce_template_single_price();
						break;
					case 'ratings' === $value:
						woocommerce_template_loop_rating();
						break;
					case 'short_desc' === $value:
						echo '<div class="trx-addons-woo-products-qv-desc">';
							woocommerce_template_single_excerpt();
						echo '</div>';
						break;
					case 'add_cart' === $value:
							$attributes = count( $product->get_attributes() ) > 0 ? 'data-variations="true"' : '';
							echo '<div class="trx-addons-woo-products-atc-button" ' . esc_attr( $attributes ) . '>';
								woocommerce_template_single_add_to_cart();
							echo '</div>';
						break;
					case 'meta' === $value:
						$attributes = count( $product->get_attributes() ) > 0 ? 'data-variations="true"' : '';
						echo '<div class="trx-addons-woo-products-qv-meta">';
							woocommerce_template_single_meta();
						echo '</div>';
						break;

					default:
						break;
				}
			}
		}
	}

	/**
	 * Add Product To Cart.
	 * Adds product to cart.
	 *
	 * @access public
	 */
	public function add_product_to_cart() {

		check_ajax_referer( 'trx-addons-woo-products-add-to-cart-nonce', 'nonce' );

		$product_id   = isset( $_POST['product_id'] ) ? sanitize_text_field( wp_unslash( $_POST['product_id'] ) ) : 0;
		$variation_id = isset( $_POST['variation_id'] ) ? sanitize_text_field( wp_unslash( $_POST['variation_id'] ) ) : 0;
		$quantity     = isset( $_POST['quantity'] ) ? sanitize_text_field( wp_unslash( $_POST['quantity'] ) ) : 0;

		if ( $variation_id ) {
			WC()->cart->add_to_cart( $product_id, $quantity, $variation_id );
		} else {
			WC()->cart->add_to_cart( $product_id, $quantity );
		}
		die();
	}
}
