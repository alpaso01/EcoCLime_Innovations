<?php
/**
 * Shortcode: Display WooCommerce cart with items number and totals
 *
 * @package ThemeREX Addons
 * @since v1.6.08
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}

// Load required styles for the frontend
if ( !function_exists( 'trx_addons_cpt_layouts_cart_load_styles_front' ) ) {
	add_action("wp_enqueue_scripts", 'trx_addons_cpt_layouts_cart_load_styles_front', TRX_ADDONS_ENQUEUE_SCRIPTS_PRIORITY);
	function trx_addons_cpt_layouts_cart_load_styles_front() {
		if (trx_addons_exists_page_builder() && trx_addons_is_on(trx_addons_get_option('debug_mode'))) {
			wp_enqueue_style( 'trx_addons-sc_layouts-cart', trx_addons_get_file_url(TRX_ADDONS_PLUGIN_CPT_LAYOUTS_SHORTCODES . 'cart/cart.css'), array(), null );
		}
	}
}

// Load responsive styles for the frontend
if ( !function_exists( 'trx_addons_cpt_layouts_cart_load_responsive_styles' ) ) {
	add_action("wp_enqueue_scripts", 'trx_addons_cpt_layouts_cart_load_responsive_styles', TRX_ADDONS_ENQUEUE_RESPONSIVE_PRIORITY);
	function trx_addons_cpt_layouts_cart_load_responsive_styles() {
		if (trx_addons_exists_page_builder() && trx_addons_is_on(trx_addons_get_option('debug_mode'))) {
			wp_enqueue_style( 
				'trx_addons-sc_layouts-cart-responsive', 
				trx_addons_get_file_url(TRX_ADDONS_PLUGIN_CPT_LAYOUTS_SHORTCODES . 'cart/cart.responsive.css'), 
				array(), 
				null, 
				trx_addons_media_for_load_css_responsive( 'cpt-layouts-cart', 'md' ) 
			);
		}
	}
}

// Load required scripts for the frontend
if ( !function_exists( 'trx_addons_cpt_layouts_cart_load_scripts_front' ) ) {
	function trx_addons_cpt_layouts_cart_load_scripts_front() {
		if (trx_addons_exists_page_builder() && trx_addons_is_on(trx_addons_get_option('debug_mode'))) {
			wp_enqueue_script( 'trx_addons-sc_layouts_cart', trx_addons_get_file_url(TRX_ADDONS_PLUGIN_CPT_LAYOUTS_SHORTCODES . 'cart/cart.js'), array('jquery'), null, true );
		}
	}
}

// Merge shortcode specific styles into single stylesheet
if ( !function_exists( 'trx_addons_sc_layouts_cart_merge_styles' ) ) {
	add_filter("trx_addons_filter_merge_styles", 'trx_addons_sc_layouts_cart_merge_styles');
	add_filter("trx_addons_filter_merge_styles_layouts", 'trx_addons_sc_layouts_cart_merge_styles');
	function trx_addons_sc_layouts_cart_merge_styles($list) {
		$list[ TRX_ADDONS_PLUGIN_CPT_LAYOUTS_SHORTCODES . 'cart/cart.css' ] = true;
		return $list;
	}
}


// Merge shortcode's specific styles to the single stylesheet (responsive)
if ( !function_exists( 'trx_addons_sc_layouts_cart_merge_styles_responsive' ) ) {
	add_filter("trx_addons_filter_merge_styles_responsive", 'trx_addons_sc_layouts_cart_merge_styles_responsive');
	add_filter("trx_addons_filter_merge_styles_responsive_layouts", 'trx_addons_sc_layouts_cart_merge_styles_responsive');
	function trx_addons_sc_layouts_cart_merge_styles_responsive($list) {
		$list[ TRX_ADDONS_PLUGIN_CPT_LAYOUTS_SHORTCODES . 'cart/cart.responsive.css' ] = true;
		return $list;
	}
}

	
// Merge shortcode's specific scripts into single file
if ( !function_exists( 'trx_addons_sc_layouts_cart_merge_scripts' ) ) {
	add_action("trx_addons_filter_merge_scripts", 'trx_addons_sc_layouts_cart_merge_scripts');
	function trx_addons_sc_layouts_cart_merge_scripts($list) {
		$list[ TRX_ADDONS_PLUGIN_CPT_LAYOUTS_SHORTCODES . 'cart/cart.js' ] = true;
		return $list;
	}
}


// Load shortcode's specific scripts if current mode is Preview in the PageBuilder
if ( !function_exists( 'trx_addons_sc_layouts_cart_load_scripts' ) ) {
	add_action("trx_addons_action_pagebuilder_preview_scripts", 'trx_addons_sc_layouts_cart_load_scripts', 10, 1);
	function trx_addons_sc_layouts_cart_load_scripts( $editor = '', $force = false ) {
		if ( trx_addons_is_on( trx_addons_get_option( 'debug_mode' ) ) && $editor != 'gutenberg' ) {
			trx_addons_cpt_layouts_cart_load_styles_front();
			trx_addons_cpt_layouts_cart_load_responsive_styles();
			trx_addons_cpt_layouts_cart_load_scripts_front();
			do_action( 'trx_addons_action_load_scripts_front', $force, 'sc_layouts_cart' );
		}
	}
}

// Load styles and scripts if present in the cache of the menu or layouts or finally in the whole page output
if ( ! function_exists( 'trx_addons_sc_layouts_cart_check_in_html_output' ) ) {
	add_filter( 'trx_addons_filter_get_menu_cache_html', 'trx_addons_sc_layouts_cart_check_in_html_output', 10, 1 );
	add_action( 'trx_addons_action_show_layout_from_cache', 'trx_addons_sc_layouts_cart_check_in_html_output', 10, 1 );
	add_action( 'trx_addons_action_check_page_content', 'trx_addons_sc_layouts_cart_check_in_html_output', 10, 1 );
	function trx_addons_sc_layouts_cart_check_in_html_output( $content = '' ) {
		$args = array(
			'need' => trx_addons_is_on( trx_addons_get_option( 'debug_mode' ) ),
			'check' => array(
				'class=[\'"][^\'"]*sc_layouts_cart',
			)
		);
		if ( trx_addons_check_in_html_output( 'sc_layouts_cart', $content, $args ) ) {
			trx_addons_sc_layouts_cart_load_scripts( '', true );
		}
		return $content;
	}
}

// Add 'Cart' on action hook
if (!function_exists('trx_addons_add_cart')) {
	add_action('trx_addons_action_cart', 'trx_addons_add_cart');
	function trx_addons_add_cart($atts=array()) {
		trx_addons_show_layout(trx_addons_sc_layouts_cart($atts));
	}
}

//Add styles for Group_Control_Typography description
if ( apply_filters( 'trx_addons_filter_allow_sc_styles_in_elementor', false, 'trx_sc_layouts_cart' ) && ! function_exists( 'trx_addons_sc_layouts_cart_elementor_editor_after_enqueue_styles' ) ) {
	add_action( 'elementor/editor/after_enqueue_styles', 'trx_addons_sc_layouts_cart_elementor_editor_after_enqueue_styles' );
	function trx_addons_sc_layouts_cart_elementor_editor_after_enqueue_styles () {
		trx_addons_cpt_layouts_cart_load_styles_front();
	}
}

if ( apply_filters( 'trx_addons_filter_allow_sc_styles_in_elementor', false, 'trx_sc_layouts_cart' ) && ! class_exists( 'TRX_Addons_Widget_Layouts_Cart_Item_Redesign' ) ) {
	class TRX_Addons_Widget_Layouts_Cart_Item_Redesign {

		/**
		 * Constructor.
		 */
		public function __construct () {
			add_action( 'woocommerce_before_mini_cart', array( $this, 'add_filters' ), 1000 );
			add_action( 'woocommerce_after_mini_cart', array( $this, 'remove_filters' ), 1000 );
		}

		/**
		 * add_filters
		 *
		 * @return void
		 */
		public function add_filters () {
			add_filter( 'woocommerce_cart_item_thumbnail', array( $this, 'woocommerce_cart_item_thumbnail' ), 1000, 3 );
			add_filter( 'woocommerce_cart_item_name', array( $this, 'woocommerce_cart_item_name' ), 1000, 3 );
			add_filter( 'woocommerce_cart_item_remove_link', array( $this, 'woocommerce_cart_item_remove_link' ), 1000, 2 );
			add_filter( 'woocommerce_widget_cart_item_quantity', array( $this, 'woocommerce_widget_cart_item_quantity' ), 1000, 3 );
		}

		/**
		 * remove_filters
		 *
		 * @return void
		 */
		public function remove_filters () {
			remove_filter( 'woocommerce_cart_item_thumbnail', array( $this, 'woocommerce_cart_item_thumbnail' ), 1000, 3 );
			remove_filter( 'woocommerce_cart_item_name', array( $this, 'woocommerce_cart_item_name' ), 1000, 3 );
			remove_filter( 'woocommerce_cart_item_remove_link', array( $this, 'woocommerce_cart_item_remove_link' ), 1000, 2 );
			remove_filter( 'woocommerce_widget_cart_item_quantity', array( $this, 'woocommerce_widget_cart_item_quantity' ), 1000, 3 );
		}

		/**
		 * woocommerce_cart_item_thumbnail
		 *
		 * @param  mixed $thumbnail
		 * @param  mixed $cart_item
		 * @param  mixed $cart_item_key
		 * @return void
		 */
		public function woocommerce_cart_item_thumbnail ( $thumbnail, $cart_item, $cart_item_key ) {
			return '';
		}

		/**
		 * woocommerce_cart_item_name
		 *
		 * @param  mixed $name
		 * @param  mixed $cart_item
		 * @param  mixed $cart_item_key
		 * @return void
		 */
		public function woocommerce_cart_item_name ( $name, $cart_item, $cart_item_key ) {
			return '<span class="trx_addons_sc_layouts_cart_mini_cart_item_name">' . $name . '</span>';
		}

		/**
		 * woocommerce_cart_item_remove_link
		 *
		 * @param  mixed $remove_link
		 * @param  mixed $cart_item_key
		 * @return void
		 */
		public function woocommerce_cart_item_remove_link ( $remove_link, $cart_item_key ) {

			$products = WC()->cart->get_cart();
			$current_product = ! empty( $products[$cart_item_key] ) && isset( $products[$cart_item_key]['data'] ) && is_object( $products[$cart_item_key]['data'] ) ? $products[$cart_item_key] : false;

			$remove_link .= '<div class="trx_addons_customizable trx_addons_sc_layouts_cart_mini_cart_item_inner">';

			if ( $current_product ) {
				$product_permalink = apply_filters( 'woocommerce_cart_item_permalink', $current_product['data']->is_visible() ? $current_product['data']->get_permalink( $current_product ) : '', $current_product, $cart_item_key );

				$remove_link .= sprintf( 
									'<div class="%s">',
									! empty( $product_permalink ) ? 'trx_addons_sc_layouts_cart_mini_cart_item_link_image' : 'trx_addons_sc_layouts_cart_mini_cart_item_image'
								);

				if ( ! empty( $product_permalink ) ) {
					$remove_link .= '<a href="' . esc_url( $product_permalink ) . '">';
				}

				$remove_link .= $current_product['data']->get_image();

				if ( ! empty( $product_permalink ) ) {
					$remove_link .= '</a>';
				}

				$remove_link .= '</div>';
			}

			$remove_link .= '<div class="trx_addons_sc_layouts_cart_mini_cart_item_content">';

			return $remove_link;
		}

		/**
		 * woocommerce_widget_cart_item_quantity
		 *
		 * @param  mixed $cart_item_quantity
		 * @param  mixed $cart_item
		 * @param  mixed $cart_item_key
		 * @return void
		 */
		public function woocommerce_widget_cart_item_quantity ( $cart_item_quantity, $cart_item, $cart_item_key ) {
			return $cart_item_quantity . '</div></div>';
		}
	}

	new TRX_Addons_Widget_Layouts_Cart_Item_Redesign();
}


// trx_sc_layouts_cart
//-------------------------------------------------------------
/*
[trx_sc_layouts_cart id="unique_id" text="Shopping cart"]
*/
if ( !function_exists( 'trx_addons_sc_layouts_cart' ) ) {
	function trx_addons_sc_layouts_cart($atts, $content = ''){	
		$atts = trx_addons_sc_prepare_atts( 'trx_sc_layouts_cart', $atts, trx_addons_sc_common_atts( 'trx_sc_layouts_cart', 'id,hide', array_merge( array(
				// Individual params
				"type" => "default",
				"market" => "woocommerce",
				"text" => "",
			),
			apply_filters( 'trx_addons_filter_allow_sc_styles_in_elementor', false, 'trx_sc_layouts_cart' )
				? array(
					// Individual params
					"product_count" => "text",
					"badge_position" => "top_right",
					"cart_panel_style_class" => "",
					"cart_preview" => "",

					// Style params
					"select_cart_icon" => "",
					"select_cart_dropdown_close_icon" => "",
					"cart_dropdown_style_background_background" => "",
					"cart_dropdown_style_background_image" => "",
					"select_type_panel_close_icon_remove" => "",
					"cart_item_style_vertical_alignment" => "top",
					"cart_item_style_justify_content" => "start",
					"select_cart_item_remove_icon" => "",
					"cart_item_remove_icon_style_position" => "left",
					"cart_item_image_style_position" => "left",
					"buttons_style_display" => "inline",
				)
				: array()
		) ) );

		$output = '';

		if ( apply_filters( 'trx_addons_filter_sc_layouts_cart_show', $atts['market'] == 'woocommerce' && trx_addons_exists_woocommerce(), $atts ) ) {

			if ( $atts['type'] == 'panel' && ! function_exists( 'trx_addons_sc_layouts' ) ) {
				$atts['type'] = 'default';
			}

			// Force enqueue WooCommerce scripts (if a cart is used on non-woocommerce pages)
			// if ( $atts['market'] == 'woocommerce' && trx_addons_exists_woocommerce() ) {
			// 	wp_enqueue_script( 'woocommerce' );
			// 	wp_enqueue_script( 'wc-cart-fragments' );
			// }

			trx_addons_cpt_layouts_cart_load_scripts_front();

			ob_start();
			trx_addons_get_template_part( array(
											TRX_ADDONS_PLUGIN_CPT_LAYOUTS_SHORTCODES . 'cart/tpl.' . trx_addons_esc( trx_addons_sanitize_file_name( $atts['type'] ) ) . '.php',
											TRX_ADDONS_PLUGIN_CPT_LAYOUTS_SHORTCODES . 'cart/tpl.default.php'
											),
											'trx_addons_args_sc_layouts_cart',
											$atts
										);
			$output = ob_get_contents();
			ob_end_clean();
		}

		return apply_filters( 'trx_addons_sc_output', $output, 'trx_sc_layouts_cart', $atts, $content );
	}
}


// Add shortcode [trx_sc_layouts_cart]
if (!function_exists('trx_addons_sc_layouts_cart_add_shortcode')) {
	function trx_addons_sc_layouts_cart_add_shortcode() {
		
		if (!trx_addons_cpt_layouts_sc_required()) return;
		
		add_shortcode("trx_sc_layouts_cart", "trx_addons_sc_layouts_cart");

	}
	add_action('init', 'trx_addons_sc_layouts_cart_add_shortcode', 15);
}


// Add shortcodes
//----------------------------------------------------------------------------

// Add shortcodes to Elementor
if ( trx_addons_exists_elementor() && function_exists('trx_addons_elm_init') ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_CPT_LAYOUTS_SHORTCODES . 'cart/cart-sc-elementor.php';
}

// Add shortcodes to Gutenberg
if ( trx_addons_exists_gutenberg() && function_exists( 'trx_addons_gutenberg_get_param_id' ) ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_CPT_LAYOUTS_SHORTCODES . 'cart/cart-sc-gutenberg.php';
}

// Add shortcodes to VC
if ( trx_addons_exists_vc() && function_exists( 'trx_addons_vc_add_id_param' ) ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_CPT_LAYOUTS_SHORTCODES . 'cart/cart-sc-vc.php';
}
