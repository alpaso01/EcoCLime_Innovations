<?php
/**
 * Widget: WooCommerce Title (Shortcodes)
 *
 * @package ThemeREX Addons
 * @since v1.90.0
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}


/*
[trx_widget_woocommerce_title id="unique_id" show_title="1" show_breadcrumbs="0"]
*/
if ( ! function_exists( 'trx_addons_sc_widget_woocommerce_title' ) ) {
	/*
	 * Shortcode [trx_widget_woocommerce_title]
	 * 
	 * @trigger trx_addons_sc_output
	 * 
	 * @param array $atts  Shortcode attributes
	 * @param string $content  Shortcode content
	 * 
	 * @return string  Shortcode output
	 */
	function trx_addons_sc_widget_woocommerce_title( $atts, $content = '' ) {	
		$atts = trx_addons_sc_prepare_atts( 'trx_widget_woocommerce_title', $atts, trx_addons_sc_common_atts( 'trx_widget_woocommerce_title', 'id', array(
			// Individual params
			"archive" => array( 'breadcrumbs', 'title', 'description' ),
			"single"  => array( 'breadcrumbs', 'title', 'description' ),
		) ) );
		$wtype = 'trx_addons_widget_woocommerce_title';
		$output = '';
		global $wp_widget_factory;
		if ( is_object( $wp_widget_factory ) && isset( $wp_widget_factory->widgets, $wp_widget_factory->widgets[ $wtype ] ) ) {
			$output = '<div' . ( !empty($atts['id']) ? ' id="'.esc_attr($atts['id']).'"' : '')
							. ' class="widget_area sc_widget_woocommerce_title' 
								. (trx_addons_exists_vc() ? ' vc_widget_woocommerce_title wpb_content_element' : '') 
								. (!empty($atts['class']) ? ' ' . esc_attr($atts['class']) : '') 
								. '"'
							. ( !empty($atts['css']) ? ' style="'.esc_attr($atts['css']).'"' : '')
						. '>';
			ob_start();
			the_widget( $wtype, $atts, trx_addons_prepare_widgets_args(!empty($atts['id']) ? $atts['id'].'_widget' : 'widget_woocommerce_title', 'widget_woocommerce_title') );
			$output .= ob_get_contents();
			ob_end_clean();
			$output .= '</div>';
		}
		return apply_filters('trx_addons_sc_output', $output, 'trx_widget_woocommerce_title', $atts, $content);
	}
}

if ( ! function_exists( 'trx_addons_sc_widget_woocommerce_title_add_shortcode' ) ) {
	add_action( 'init', 'trx_addons_sc_widget_woocommerce_title_add_shortcode', 20 );
	/**
	 * Add shortcode [trx_widget_woocommerce_title]
	 * 
	 * @hooked init, 20
	 */
	function trx_addons_sc_widget_woocommerce_title_add_shortcode() {
		if ( ! trx_addons_exists_woocommerce() ) {
			return;
		}
		add_shortcode( "trx_widget_woocommerce_title", "trx_addons_sc_widget_woocommerce_title" );
	}
}

// Add shortcode's specific lists to the JS storage
if ( ! function_exists( 'trx_addons_sc_woocommerce_title_gutenberg_sc_params' ) ) {
	add_filter( 'trx_addons_filter_gutenberg_sc_params', 'trx_addons_sc_woocommerce_title_gutenberg_sc_params' );
	function trx_addons_sc_woocommerce_title_gutenberg_sc_params( $vars = array() ) {
		
		// If editor is active now
		$is_edit_mode = trx_addons_is_post_edit();
		
		// Return list of WooCommerce title parts for Products archive
		$vars['sc_archive_title_parts'] = !$is_edit_mode ? array() : trx_addons_get_list_woocommerce_title_parts();
		
		// Return list of WooCommerce title parts for Single product
		$vars['sc_single_title_parts'] = !$is_edit_mode ? array() : trx_addons_get_list_woocommerce_title_parts( false );
		
		return $vars;
	}
}
