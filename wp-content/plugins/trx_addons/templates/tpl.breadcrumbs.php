<?php
/**
 * The template to display breadcrumbs
 *
 * @package ThemeREX Addons
 * @since v1.6.08
 */

$args = get_query_var( 'trx_addons_args_show_breadcrumbs' );
if ( ! is_array( $args ) ) {
	$args = array();
}

if ( ( $trx_addons_breadcrumbs = trx_addons_get_breadcrumbs( $args ) ) != '' ) {
	?><div class="breadcrumbs"><?php trx_addons_show_layout( $trx_addons_breadcrumbs ); ?></div><?php
}
