<?php
/**
 * The style "default" of the Cart
 *
 * @package ThemeREX Addons
 * @since v1.6.08
 */

$args = get_query_var('trx_addons_args_sc_layouts_cart');

$show_cart = trx_addons_is_preview( 'elementor' ) && get_post_type()==TRX_ADDONS_CPT_LAYOUTS_PT ? 'preview' : '';
$allow_sc_styles_in_elementor = apply_filters( 'trx_addons_filter_allow_sc_styles_in_elementor', false, 'trx_sc_layouts_cart' );
$preview_show_cart = ! empty( $args['cart_preview'] )
						&& trx_addons_is_preview( 'elementor' )
						&& $allow_sc_styles_in_elementor
						&& ( in_array( trx_addons_get_edited_post_type( 'elementor' ), array( trx_addons_cpt_param( 'layouts', 'post_type' ), 'elementor_library' ) )
							|| doing_action( 'wp_ajax_elementor_ajax' )
							);

$cart_items = $cart_summa = 0;

//If it's a preview for WooCommerce Cart init WC()->cart
if ( $preview_show_cart && $args['market'] == 'woocommerce' && trx_addons_exists_woocommerce() && !is_cart() && !is_checkout() && empty( WC()->cart ) ) {
	wc_load_cart();
}

if (empty($show_cart) || $preview_show_cart) {
	// If it's a WooCommerce Cart
	if ($args['market'] == 'woocommerce' && trx_addons_exists_woocommerce() && !is_cart() && !is_checkout() && !empty(WC()->cart)) {
		$cart_items = WC()->cart->get_cart_contents_count();
		$cart_summa = strip_tags(WC()->cart->get_cart_subtotal());
		$show_cart = 'woocommerce';

	// If it's a EDD Cart
	} else if ($args['market'] == 'edd' && trx_addons_exists_edd()) {
		$cart_items = edd_get_cart_quantity();
		$cart_summa = edd_currency_filter( edd_format_amount( edd_get_cart_total() ) );
		$show_cart = 'edd';
	}
}

if (!empty($show_cart)) {

	$sc_layouts_cart_custom_classes = array();
	$sc_layouts_cart_custom_attr = array();

	if ( $allow_sc_styles_in_elementor ) {
		$sc_layouts_cart_custom_classes[] = 'trx_addons_customizable';

		if ( $preview_show_cart ) {
			$sc_layouts_cart_custom_classes[] = 'sc_layouts_cart_preview_init';
		}

		if ( ! empty( $args['product_count'] ) ) {
			$sc_layouts_cart_custom_classes[] = 'trx_addons_sc_cart_product_count_' . $args['product_count'];

			if ( ! empty( $args['badge_position'] ) && $args['product_count'] == 'badge' ) {
				$sc_layouts_cart_custom_classes[] = 'trx_addons_sc_cart_badge_position_' . $args['badge_position'];
			}
		}

		if ( 
			! empty( $args['cart_dropdown_style_background_background'] )
			&& (
				( $args['cart_dropdown_style_background_background'] == 'classic' && ! empty( $args['cart_dropdown_style_background_image']['url'] ) )
				|| $args['cart_dropdown_style_background_background'] == 'gradient' 
			)
		) {
			$sc_layouts_cart_custom_classes[] = 'sc_layouts_cart_dropdown_remove_decode';
		}

		if ( ! empty( $args['cart_item_remove_icon_style_position'] ) ) {
			$sc_layouts_cart_custom_classes[] = 'sc_layouts_cart_item_close_icon-' . $args['cart_item_remove_icon_style_position'];
		}

		if ( ! empty( $args['cart_item_image_style_position'] ) ) {
			$sc_layouts_cart_custom_classes[] = 'sc_layouts_cart_item_image-' . $args['cart_item_image_style_position'];
		}

		if ( ! empty( $args['buttons_style_display'] ) ) {
			$sc_layouts_cart_custom_classes[] = 'sc_layouts_cart_buttons-' . $args['buttons_style_display'];
		}

		if ( ! empty( $args['cart_item_style_vertical_alignment'] ) ) {
			$sc_layouts_cart_custom_classes[] = 'sc_layouts_cart_item_va-' . $args['cart_item_style_vertical_alignment'];
		}

		if ( ! empty( $args['select_cart_item_remove_icon'] ) && ! empty( $args['select_cart_item_remove_icon']['value'] ) && class_exists( '\Elementor\Icons_Manager' ) ) {
			ob_start();
			\Elementor\Icons_Manager::render_icon( $args['select_cart_item_remove_icon'], [
				'aria-hidden' => 'true',
				'class' => 'sc_layouts_cart_item_close_icon',
			], 'span' );
			$sc_layouts_cart_custom_attr[] = 'data-cart-item-close-icon="' . esc_attr( htmlentities( ob_get_contents(), ENT_QUOTES, 'UTF-8' ) ) . '"';
			ob_end_clean();
		}
	}

	?><div<?php if (!empty($args['id'])) echo ' id="'.esc_attr($args['id']).'"'; ?> class="sc_layouts_cart sc_layouts_cart_market_<?php
			echo esc_attr( $args['market'] );
			trx_addons_cpt_layouts_sc_add_classes($args);
			echo esc_attr( ' ' . implode( ' ', $sc_layouts_cart_custom_classes ) );
		?>"<?php
		echo ' ' . implode( ' ', $sc_layouts_cart_custom_attr );
		if (!empty($args['css'])) echo ' style="'.esc_attr($args['css']).'"';
		trx_addons_sc_show_attributes('sc_layouts_cart', $args, 'sc_wrapper');
	?>>
		<?php if ( $allow_sc_styles_in_elementor && ! empty( $args['select_cart_icon'] ) && ! empty( $args['select_cart_icon']['value'] ) && class_exists( '\Elementor\Icons_Manager' ) ) { ?>
			<span class="sc_layouts_item_icon sc_layouts_cart_icon sc_icons_type_icons sc_layouts_cart_icon_custom">
				<?php \Elementor\Icons_Manager::render_icon( $args['select_cart_icon'], [
					'aria-hidden' => 'true',
				], 'span' ); ?>
			</span>
		<?php } else { ?>
			<span class="sc_layouts_item_icon sc_layouts_cart_icon sc_icons_type_icons trx_addons_icon-basket"></span>
		<?php } ?>

		<span class="sc_layouts_item_details sc_layouts_cart_details">
			<?php if (!empty($args['text'])) { ?>
			<span class="sc_layouts_item_details_line1 sc_layouts_cart_label"><?php echo esc_html($args['text']); ?></span>
			<?php } ?>
			<span class="sc_layouts_item_details_line2 sc_layouts_cart_totals">
				<span class="sc_layouts_cart_items" data-item="<?php echo esc_attr_x( 'item', 'single form', 'trx_addons' ); ?>" data-items="<?php echo esc_attr_x( 'items', 'plural form', 'trx_addons' ); ?>"><?php
					echo esc_html( $cart_items ) . ' ' . esc_html( _nx( 'item', 'items', $cart_items, 'after items number', 'trx_addons' ) );
				?></span>
				- 
				<span class="sc_layouts_cart_summa"><?php trx_addons_show_layout($cart_summa); ?></span>
			</span>
		</span>
		<span class="sc_layouts_cart_items_short"><?php echo esc_html($cart_items); ?></span>
		<div class="sc_layouts_cart_widget widget_area">
			<span class="sc_layouts_cart_widget_close trx_addons_button_close">
				<?php if ( $allow_sc_styles_in_elementor && ! empty( $args['select_cart_dropdown_close_icon'] ) && ! empty( $args['select_cart_dropdown_close_icon']['value'] ) && class_exists( '\Elementor\Icons_Manager' ) ) { ?>
					<?php \Elementor\Icons_Manager::render_icon( $args['select_cart_dropdown_close_icon'], [
						'aria-hidden' => 'true',
						'class' => 'sc_layouts_cart_widget_close_icon',	// add class 'trx_addons_button_close_icon' if you need to rotate icon on hover
					], 'span' ); ?>
				<?php } else { ?>
					<span class="sc_layouts_cart_widget_close_icon trx_addons_button_close_icon"></span>
				<?php } ?>
			</span>
			<?php
			// Show WooCommerce Cart
			do_action( 'trx_addons_action_before_cart', $show_cart, $args );
			if ($show_cart == 'woocommerce') {
				ob_start();
				the_widget( 'WC_Widget_Cart', 'title=&hide_if_empty=0' );
				$wc_mini_cart_widget = ob_get_clean();

				$wc_mini_cart_widget_content = '<div class="widget_shopping_cart_content">';

				// Show Preview WooCommerce Cart
				if ( $preview_show_cart ) {
					ob_start();
					woocommerce_mini_cart();
					$wc_mini_cart_widget_content .= ob_get_clean();
				}

				$wc_mini_cart_widget_content .= '</div>';

				echo apply_filters( 'trx_addons_filter_sc_layouts_cart_wc', 
					str_replace(
						'<div class="widget_shopping_cart_content"></div>',
						$wc_mini_cart_widget_content,
						$wc_mini_cart_widget
					),
					$args
				);

			// Show EDD Cart
			} else if ($show_cart == 'edd') {
				the_widget( 'edd_cart_widget', 'title=&hide_on_checkout=0&hide_on_empty=0' );

			// Show preview Cart
			} else {
				?><div class="sc_layouts_cart_preview"><?php esc_html_e('Placeholder for Cart items'); ?></div><?php
			}
			do_action( 'trx_addons_action_after_cart', $show_cart, $args );
			?>
		</div>
	</div><?php

	trx_addons_sc_layouts_showed('cart', true);
}
