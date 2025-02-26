<?php
/**
 * The style "button" of the Cart
 *
 * @package ThemeREX Addons
 * @since v1.95.0
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

if ( empty( $show_cart ) || $preview_show_cart ) {
	// If it's a WooCommerce Cart
	if ( $args['market'] == 'woocommerce' && trx_addons_exists_woocommerce() && ! is_cart() && ! is_checkout() && ! empty( WC()->cart ) ) {
		$cart_items = WC()->cart->get_cart_contents_count();
		$cart_summa = strip_tags( WC()->cart->get_cart_subtotal() );
		$show_cart = 'woocommerce';

	// If it's a EDD Cart
	} else if ( $args['market'] == 'edd' && trx_addons_exists_edd() ) {
		$cart_items = edd_get_cart_quantity();
		$cart_summa = edd_currency_filter( edd_format_amount( edd_get_cart_total() ) );
		$show_cart = 'edd';
	}
}

if ( ! empty( $show_cart ) ) {

	$button_id = $args['type']  == 'button'
					? ( ! empty( $args['id'] )
						? $args['id'] . '_button'
						: 'sc_layouts_cart_button_' . mt_rand()
						)
					: '';
	$button_link_data  = ! empty( $button_id ) ? ' data-button-id="' . esc_attr( $button_id ) . '"' : '';
	$cart_url = $show_cart == 'woocommerce' && function_exists( 'wc_get_cart_url' )
					? wc_get_cart_url()
					: '';

	$sc_layouts_cart_custom_classes = array();

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
	}

	?><div<?php if ( ! empty( $args['id'] ) ) echo ' id="' . esc_attr( $args['id'] ) . '"'; ?> class="sc_layouts_cart<?php
				trx_addons_cpt_layouts_sc_add_classes( $args );
				echo esc_attr( ' ' . implode( ' ', $sc_layouts_cart_custom_classes ) );
		?>"<?php
		if ( ! empty( $args['css'] ) ) echo ' style="' . esc_attr( $args['css'] ) . '"';
		trx_addons_sc_show_attributes( 'sc_layouts_cart', $args, 'sc_wrapper' );
	?>><?php

		if ( ! empty( $cart_url ) ) {
			?><a href="<?php echo esc_url( $cart_url ); ?>" class="sc_layouts_cart_link"><?php
		}

		?>
		<?php if ( $allow_sc_styles_in_elementor && ! empty( $args['select_cart_icon'] ) && ! empty( $args['select_cart_icon']['value'] ) && class_exists( '\Elementor\Icons_Manager' ) ) { ?>
			<span class="sc_layouts_item_icon sc_layouts_cart_icon sc_icons_type_icons"<?php
				echo wp_kses( $button_link_data, 'trx_addons_kses_content' );
			?>>
				<?php
					\Elementor\Icons_Manager::render_icon( $args['select_cart_icon'], [
						'aria-hidden' => 'true',
					], 'span' );
				?>
			</span>
		<?php } else { ?>
			<span class="sc_layouts_item_icon sc_layouts_cart_icon sc_icons_type_icons trx_addons_icon-basket"<?php
				echo wp_kses( $button_link_data, 'trx_addons_kses_content' );
			?>></span>
		<?php } ?>

		<span class="sc_layouts_item_details sc_layouts_cart_details"<?php
			echo wp_kses( $button_link_data, 'trx_addons_kses_content' );
		?>>
			<?php if ( ! empty( $args['text'] ) ) { ?>
			<span class="sc_layouts_item_details_line1 sc_layouts_cart_label"><?php echo esc_html( $args['text'] ); ?></span>
			<?php } ?>
			<span class="sc_layouts_item_details_line2 sc_layouts_cart_totals">
				<span class="sc_layouts_cart_items" data-item="<?php echo esc_attr_x( 'item', 'single form', 'trx_addons' ); ?>" data-items="<?php echo esc_attr_x( 'items', 'plural form', 'trx_addons' ); ?>"><?php
					echo esc_html( $cart_items ) . ' ' . esc_html( _nx( 'item', 'items', $cart_items, 'after items number', 'trx_addons' ) );
				?></span>
				- 
				<span class="sc_layouts_cart_summa"><?php trx_addons_show_layout( $cart_summa ); ?></span>
			</span>
		</span>
		<span class="sc_layouts_cart_items_short"><?php echo esc_html( $cart_items ); ?></span><?php

		if ( ! empty( $cart_url ) ) {
			?></a><?php
		}

		// If 'type' == 'button'
		if ( $args['type']  == 'button' ) {
			ob_start();
		}

		?><div class="sc_layouts_cart_widget widget_area">
			<span class="sc_layouts_cart_widget_close trx_addons_button_close"><span class="sc_layouts_cart_widget_close_icon trx_addons_button_close_icon"></span></span>
			<?php
			// Show WooCommerce Cart
			do_action( 'trx_addons_action_before_cart', $show_cart, $args );
			if ( $show_cart == 'woocommerce' ) {
				the_widget( 'WC_Widget_Cart', 'title=&hide_if_empty=0' );

			// Show EDD Cart
			} else if ( $show_cart == 'edd' ) {
				the_widget( 'edd_cart_widget', 'title=&hide_on_checkout=0&hide_on_empty=0' );

			// Show preview Cart
			} else {
				?><div class="sc_layouts_cart_preview"><?php esc_html_e('Placeholder for Cart items'); ?></div><?php
			}
			do_action( 'trx_addons_action_after_cart', $show_cart, $args );
		?></div><?php

		// If 'type' == 'button'
		if ( $args['type'] == 'button' ) {
			$output = ob_get_contents();
			ob_end_clean();
			$output = apply_filters( 'trx_addons_filter_sc_layouts_cart_button_html',
							sprintf( '<div id="%1$s" class="sc_layouts_cart_button_wrap">'
										. '<span class="sc_layouts_cart_button_sonar"></span>'
										. ( ! empty( $cart_url ) ? '<a href="' . esc_url( $cart_url ) . '"' : '<span' ) . ' class="sc_layouts_cart_button">'
											. '<span class="sc_layouts_cart_button_icon sc_icons_type_icons trx_addons_icon-basket"></span>'
											. '<span class="sc_layouts_cart_items_short">%2$d</span>'
										. ( ! empty( $cart_url ) ? '</a>' : '</span>' )
										. '%3$s'
									. '</div>',
									esc_attr( $button_id ),
									esc_html( $cart_items ),
									$output
							),
							$args
						);
			trx_addons_add_inline_html( $output );
		}

	?></div><?php

	trx_addons_sc_layouts_showed('cart', true);
}
