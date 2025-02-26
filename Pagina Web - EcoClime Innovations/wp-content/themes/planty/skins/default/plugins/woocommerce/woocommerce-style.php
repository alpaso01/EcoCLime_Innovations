<?php
// Add plugin-specific colors and fonts to the custom CSS
if ( ! function_exists( 'planty_woocommerce_get_css' ) ) {
	add_filter( 'planty_filter_get_css', 'planty_woocommerce_get_css', 10, 2 );
	function planty_woocommerce_get_css( $css, $args ) {

		if ( isset( $css['fonts'] ) && isset( $args['fonts'] ) ) {
			$fonts         = $args['fonts'];
			$css['fonts'] .= <<<CSS
.woocommerce-form-login label.woocommerce-form-login__rememberme,
.woocommerce-checkout-payment .wpgdprc-checkbox label,
.woocommerce ul.products li.product .post_header .post_tags,
#add_payment_method #payment div.payment_box,
.woocommerce-cart #payment div.payment_box,
.woocommerce-checkout #payment div.payment_box,
.woocommerce div.product .product_meta span > a,
.woocommerce div.product .product_meta span > span,
.woocommerce .checkout table.shop_table .product-name .variation,
.woocommerce .shop_table.order_details td.product-name .variation,
.woocommerce td.product-name dl.variation dd,
.woocommerce td.product-name dl.variation dt,
.woocommerce-checkout-payment .checkbox .woocommerce-terms-and-conditions-checkbox-text {
	{$fonts['p_font-family']}
}
.woocommerce-grouped-product-list-item__label,
.woocommerce-grouped-product-list-item__price,
.woocommerce #review_form #respond #reply-title,
.tinv-wishlist th,
.tinv-wishlist td,
.tinv-wishlist td *,
.woocommerce-error,
.woocommerce-info,
.woocommerce-message,
.shop_table_checkout_review table,
form.woocommerce-checkout label,
.woocommerce_status_bar,
.woocommerce .cart-collaterals span.amount,
.woocommerce .comment-form .comment-form-comment label,
.woocommerce .comment-form .comment-form-rating label,
.woocommerce .comment-form .comment-form-author label,
.woocommerce .comment-form .comment-form-email label,
.woocommerce div.product .woocommerce-tabs ul.tabs li a, .woocommerce #content div.product .woocommerce-tabs ul.tabs li a, .woocommerce-page div.product .woocommerce-tabs ul.tabs li a, .woocommerce-page #content div.product .woocommerce-tabs ul.tabs li a,
.woocommerce .product_meta span,
.woocommerce div.product form.cart .variations .label,
.woocommerce.widget_shopping_cart .total,
.woocommerce-page.widget_shopping_cart .total,
.woocommerce .widget_shopping_cart .total,
.woocommerce-page .widget_shopping_cart .total,
.woocommerce.widget_shopping_cart .quantity,
.woocommerce-page.widget_shopping_cart .quantity,
.woocommerce .widget_shopping_cart .quantity,
.woocommerce-page .widget_shopping_cart .quantity,
.woocommerce ul.cart_list li > .amount,
.woocommerce-page ul.cart_list li > .amount,
.woocommerce ul.product_list_widget li > .amount,
.woocommerce-page ul.product_list_widget li > .amount,
.woocommerce ul.cart_list li span .amount,
.woocommerce-page ul.cart_list li span .amount,
.woocommerce ul.product_list_widget li span .amount,
.woocommerce-page ul.product_list_widget li span .amount,
.woocommerce ul.cart_list li ins .amount,
.woocommerce-page ul.cart_list li ins .amount,
.woocommerce ul.product_list_widget li ins .amount,
.woocommerce-page ul.product_list_widget li ins .amount,
.woocommerce ul.products li.product .outofstock_label,
.woocommerce ul.cart_list li > b, .woocommerce ul.cart_list li a, .woocommerce-page ul.cart_list li a, .woocommerce ul.product_list_widget li a, .woocommerce-page ul.product_list_widget li a,
.woocommerce ul.products li.product .onsale, .woocommerce-page ul.products li.product .onsale,
.woocommerce ul.products li.product .price, .woocommerce-page ul.products li.product .price,
.woocommerce ul.products li.product .post_header, .woocommerce-page ul.products li.product .post_header,
.single-product div.product .woocommerce-tabs .wc-tabs li a,
.woocommerce .shop_table th,
.woocommerce span.onsale,
.woocommerce div.product p.price, .woocommerce div.product span.price,
.woocommerce div.product .summary .stock,
.woocommerce #reviews #comments ol.commentlist li .comment-text p.meta strong,
.woocommerce-page #reviews #comments ol.commentlist li .comment-text p.meta strong,
.woocommerce table.cart td.product-name .product-info > b, .woocommerce table.cart td.product-name a, .woocommerce-page table.cart td.product-name a, 
.woocommerce #content table.cart td.product-name a, .woocommerce-page #content table.cart td.product-name a,
.woocommerce .checkout table.shop_table .product-name,
.woocommerce .shop_table.order_details td.product-name,
.woocommerce .order_details li strong,
.woocommerce-MyAccount-navigation,
.woocommerce-MyAccount-content .woocommerce-Address-title a,
.woocommerce .woocommerce-cart-form table.shop_table tbody span.amount, 
.woocommerce .woocommerce-cart-form table.shop_table tbody span.amount .woocommerce-Price-currencySymbol, 
.woocommerce .woocommerce-cart-form table.shop_table tbody .product-price span.amount,
.trx_addons_woocommerce_search .sc_form_field_title_caption,
.woocommerce table.shop_table_responsive tr td td:before,
.woocommerce-page table.shop_table_responsive tr td td:before {
	{$fonts['h5_font-family']}
}
.woocommerce ul.products li.product .post_data .add_to_cart_wrap .added_to_cart,
.woocommerce-page ul.products li.product .post_data .add_to_cart_wrap .added_to_cart,
.woocommerce #btn-buy,
.tinv-wishlist .tinvwl_added_to_wishlist.tinv-modal button,
.woocommerce ul.products li.product .button,
.woocommerce div.product form.cart .button,
.woocommerce #review_form #respond p.form-submit input[type="submit"],
.woocommerce-page #review_form #respond p.form-submit input[type="submit"],
.woocommerce table.my_account_orders .order-actions .button,
.woocommerce .button,
.woocommerce-page .button,
.woocommerce a.button,
.woocommerce button.button,
.woocommerce input.button,
.woocommerce #respond input#submit,
.woocommerce .hidden-title-form a.hide-title-form,
.woocommerce input[type="button"], .woocommerce-page input[type="button"],
.woocommerce input[type="submit"], .woocommerce-page input[type="submit"] {
	{$fonts['button_font-family']}
	{$fonts['button_font-size']}
	{$fonts['button_font-weight']}
	{$fonts['button_font-style']}
	{$fonts['button_line-height']}
	{$fonts['button_text-decoration']}
	{$fonts['button_text-transform']}
	{$fonts['button_letter-spacing']}
}
.woocommerce button.button *,
.post_featured.hover_shop .bottom-info > div > a,
.woocommerce ul.products.products_style_simple li.product .post_data .add_to_cart_wrap .added_to_cart,
.woocommerce ul.products.products_style_simple li.product .post_data .add_to_cart_wrap .button {
    {$fonts['button_font-family']}
}
.woocommerce-input-wrapper,
.woocommerce table.cart td.actions .coupon .input-text,
.woocommerce #content table.cart td.actions .coupon .input-text,
.woocommerce-page table.cart td.actions .coupon .input-text,
.woocommerce-page #content table.cart td.actions .coupon .input-text {
	{$fonts['input_font-family']}
	{$fonts['input_font-size']}
	{$fonts['input_font-weight']}
	{$fonts['input_font-style']}
	{$fonts['input_line-height']}
	{$fonts['input_text-decoration']}
	{$fonts['input_text-transform']}
	{$fonts['input_letter-spacing']}
}
.woocommerce ul.products li.product .post_header .post_tags,
.woocommerce div.product form.cart .reset_variations,
.woocommerce #reviews #comments ol.commentlist li .comment-text p.meta time, .woocommerce-page #reviews #comments ol.commentlist li .comment-text p.meta time {
	{$fonts['info_font-family']}
}
.tinv-wishlist td .tinvwl-input-group select,
.tinv-wishlist td .tinvwl-input-group select * {
	{$fonts['p_font-family']}
}

CSS;
		}

		return $css;
	}
}


// Load skin-specific functions
$fdir = planty_get_file_dir( 'plugins/woocommerce/woocommerce-skin.php' );
if ( ! empty( $fdir ) ) {
	require_once $fdir;
}
