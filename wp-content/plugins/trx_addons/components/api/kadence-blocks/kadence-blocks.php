<?php
/**
 * Plugin support: Kadence Blocks
 *
 * @package ThemeREX Addons
 * @since v2.28.1
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}

if ( ! function_exists( 'trx_addons_exists_kadence_blocks' ) ) {
	/**
	 * Check if the plugin 'Kadence Blocks' is installed and activated
	 *
	 * @return bool  True if a plugin is installed and activated
	 */
	function trx_addons_exists_kadence_blocks() {
		return function_exists( 'kadence_blocks_init' );
	}
}

if ( ! function_exists( 'trx_addons_is_built_with_kadence_blocks' ) ) {
	/**
	 * Check if the post is built with Kadence Blocks
	 *
	 * @param int $post_id  post ID
	 * 
	 * @return bool  true if the post is built with Kadence Blocks
	 */
	function trx_addons_is_built_with_kadence_blocks( $post_id ) {
		$rez = false;
		if ( trx_addons_exists_kadence_blocks() && ! empty( $post_id ) ) {
			$content = '';
			if ( $post_id == get_the_ID() ) {
				$content = get_the_content();
			} else {
				$post = get_post( $post_id );
				if ( ! empty( $post ) ) {
					$content = $post->post_content;
				}
			}
			$rez = strpos( $content, '<!-- wp:kadence/') !== false;
		}
		return $rez;
	}
}

if ( ! function_exists( 'trx_addons_kadence_blocks_need_load_styles' ) ) {
	add_action( 'wp_enqueue_scripts', 'trx_addons_kadence_blocks_need_load_styles', TRX_ADDONS_ENQUEUE_SCRIPTS_PRIORITY );
	/**
	 * Detect if the page is built with Kadence Blocks and set flag to load styles
	 * 
	 * @hooked 'wp_enqueue_scripts'
	 */
	function trx_addons_kadence_blocks_need_load_styles() {
		if ( trx_addons_exists_kadence_blocks() ) {
			global $TRX_ADDONS_STORAGE;
			$TRX_ADDONS_STORAGE['cur_page_built_with_kadence_blocks'] = trx_addons_is_singular() && trx_addons_is_built_with_kadence_blocks( get_the_ID() );
		}
	}
}


if ( ! function_exists( 'trx_addons_kadence_blocks_add_styles_to_layout' ) ) {
	add_action( 'trx_addons_filter_sc_layout_content_from_builder', 'trx_addons_kadence_blocks_add_styles_to_layout', 10, 3 );
	/**
	 * Add styles to the layout from the Kadence Blocks plugin
	 * 
	 * @hooked 'trx_addons_filter_sc_layout_content_from_builder'
	 */
	function trx_addons_kadence_blocks_add_styles_to_layout( $content, $post_id, $builder ) {
		static $styles_included = array();
		if ( in_array( $builder, array( 'gb', 'gutenberg' ) )
			&& trx_addons_exists_kadence_blocks()
			&& class_exists( 'Kadence_Blocks_CSS' )
			&& empty( $TRX_ADDONS_STORAGE['cur_page_built_with_kadence_blocks'] )
			&& empty( $styles_included[ $post_id ] )
			&& trx_addons_is_built_with_kadence_blocks( $post_id )
		) {
			$styles_included[ $post_id ] = true;
			$kadence = Kadence_Blocks_CSS::get_instance();
			$styles = '';
			// Collect main styles
			if ( ! empty( $kadence::$styles ) && is_array( $kadence::$styles ) ) {
				foreach ( $kadence::$styles as $value ) {
					$styles .= $value;
				}
				$kadence::$styles = array();
			}
			// Collect custom styles
			if ( ! empty( $kadence::$custom_styles ) && is_array( $kadence::$custom_styles ) ) {
				foreach ( $kadence::$custom_styles as $value ) {
					$styles .= $value;
				}
				$kadence::$custom_styles = array();
			}
			// Add all styles to the layout
			if ( ! empty( $styles ) ) {

				// Add styles to the layout content
				if ( apply_filters( 'trx_addons_filter_add_kadence_blocks_styles_to_layout', true, $post_id, $builder ) ) {
					$content .= '<style id="trx_addons_kadence_blocks_css_' . esc_attr( $post_id ) . '" type="text/css">' . $styles . '</style>';

				// Enqueue styles
				} else {
					$id = "trx_addons_kadence_blocks_css_{$post_id}";
					wp_register_style( $id, false );
					wp_enqueue_style( $id );
					wp_add_inline_style( $id, $styles );
				}
			}
		}
		return $content;
	}
}

if ( ! function_exists( 'trx_addons_kadence_blocks_disable_move_styles_before_theme_styles' ) ) {
	add_filter( 'trx_addons_filter_move_3rd_party_styles_before_theme_styles', 'trx_addons_kadence_blocks_disable_move_styles_before_theme_styles', 10, 2 );
	/**
	 * Disable to move styles from the Kadence Blocks plugin before the theme styles - they are should be moved to the end of the head
	 * 
	 * @hooked 'trx_addons_filter_move_3rd_party_styles_before_theme_styles'
	 * 
	 * @param bool $allow  true - move styles before the theme styles, false - move styles after the theme styles (to the end of the head)
	 * @param string $tag  the tag content: <link> or <style>...</style>
	 * 
	 * @return bool  true - move styles before the theme styles, false - move styles after the theme styles (to the end of the head)
	 */
	function trx_addons_kadence_blocks_disable_move_styles_before_theme_styles( $allow, $tag = '' ) {
		if ( trx_addons_exists_kadence_blocks() && ( strpos( $tag, 'kadence-blocks' ) !== false || strpos( $tag, 'kadence_blocks' ) !== false ) ) {
			$allow = false;
		}
		return $allow;
	}
}