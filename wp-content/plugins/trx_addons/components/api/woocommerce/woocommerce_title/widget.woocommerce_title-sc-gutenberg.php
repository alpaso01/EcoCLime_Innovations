<?php
/**
 * Widget: WooCommerce Title (Gutenberg support)
 *
 * @package ThemeREX Addons
 * @since v1.0
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}

// Gutenberg Block
//------------------------------------------------------

// Add scripts and styles for the editor
if ( ! function_exists( 'trx_addons_gutenberg_sc_woocommerce_title_editor_assets' ) ) {
	add_action( 'enqueue_block_editor_assets', 'trx_addons_gutenberg_sc_woocommerce_title_editor_assets', TRX_ADDONS_GUTENBERG_EDITOR_BLOCK_REGISTRATION_PRIORITY );
	function trx_addons_gutenberg_sc_woocommerce_title_editor_assets() {
		if ( trx_addons_exists_gutenberg() && trx_addons_get_setting( 'allow_gutenberg_blocks' ) ) {
			if ( function_exists( 'trx_addons_exists_woocommerce' ) && trx_addons_exists_woocommerce() ) {
				// Scripts
				wp_enqueue_script(
						'trx-addons-gutenberg-editor-block-woocommerce-title',
						trx_addons_get_file_url( TRX_ADDONS_PLUGIN_API . 'woocommerce/woocommerce_title/gutenberg/widget.woocommerce_title.gutenberg-editor.js' ),
						trx_addons_block_editor_dependencis(),
						filemtime( trx_addons_get_file_dir( TRX_ADDONS_PLUGIN_API . 'woocommerce/woocommerce_title/gutenberg/widget.woocommerce_title.gutenberg-editor.js' ) ),
						true
				);
			}
		}
	}
}

// Block register
if ( ! function_exists( 'trx_addons_sc_woocommerce_title_add_in_gutenberg' ) ) {
	add_action('init', 'trx_addons_sc_woocommerce_title_add_in_gutenberg');
	function trx_addons_sc_woocommerce_title_add_in_gutenberg()
	{
		if (trx_addons_exists_gutenberg() && trx_addons_get_setting('allow_gutenberg_blocks')) {
			if (function_exists('trx_addons_exists_woocommerce') && trx_addons_exists_woocommerce()) {
				register_block_type(
						'trx-addons/woocommerce-title',
						apply_filters('trx_addons_gb_map', array(
							'attributes'      => array_merge(
								array(
									'archive' => array(
										'type'    => 'string',
										'default' => '',
									),
									'single'  => array(
										'type'    => 'string',
										'default' => '',
									),
								),
								trx_addons_gutenberg_get_param_id()
							),
							'render_callback' => 'trx_addons_gutenberg_woocommerce_title_render_block',
						), 'trx-addons/woocommerce-title' )
				);
			}
		}
	}
}

// Block render
if ( ! function_exists( 'trx_addons_gutenberg_woocommerce_title_render_block' ) ) {
	function trx_addons_gutenberg_woocommerce_title_render_block( $attributes = array() ) {
		// Convert 'archive' and 'single' attributes to arrays
		if ( ! empty( $attributes['archive'] ) && is_string( $attributes['archive'] ) ) {
			$attributes['archive'] = explode( ',', $attributes['archive'] );
		}
		if ( ! empty( $attributes['single'] ) && is_string( $attributes['single'] ) ) {
			$attributes['single'] = explode( ',', $attributes['single'] );
		}
		return trx_addons_sc_widget_woocommerce_title( $attributes );
	}
}