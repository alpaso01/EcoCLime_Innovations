<?php
/**
 * The style "default" of the Search form
 *
 * @package ThemeREX Addons
 * @since v1.6.08
 */

$args = get_query_var('trx_addons_args_sc_layouts_search');

?><div<?php if (!empty($args['id'])) echo ' id="' . esc_attr( $args['id'] ) . '"'; ?> class="sc_layouts_search<?php
		trx_addons_cpt_layouts_sc_add_classes($args);
	?>"<?php
	if ( ! empty( $args['css'] ) ) echo ' style="' . esc_attr( $args['css'] ) . '"';
	trx_addons_sc_show_attributes( 'sc_layouts_search', $args, 'sc_wrapper' );
?>><?php

	$args['class'] = ( ! empty( $args['class'] ) ? ' ' : '' ) . 'layouts_search'
					. ( apply_filters( 'trx_addons_filter_allow_sc_styles_in_elementor', false, 'sc_layouts_search' ) ? ' trx_addons_customizable' : '' )
					. ( ! empty( $args['search_opened'] )
						&& trx_addons_is_preview()
						&& ( in_array( trx_addons_get_edited_post_type( 'elementor' ), array( trx_addons_cpt_param( 'layouts', 'post_type' ), 'elementor_library' ) )
							|| doing_action( 'wp_ajax_elementor_ajax' )
							)
						 	? ' search_opened'
							: ''
						)
					. ( ! empty( $args['icon_hidden'] ) && (int)$args['icon_hidden'] == 1 ? ' search_icon_hidden' : '' )
					. ( ! empty( $args['icon_halign'] ) ? ' search_icon_halign_' . esc_attr( $args['icon_halign'] ) : '' )
					. ( ! empty( $args['icon_valign'] ) ? ' search_icon_valign_' . esc_attr( $args['icon_valign'] ) : '' );

	do_action( 'trx_addons_action_search', $args );
	
?></div><?php

trx_addons_sc_layouts_showed( 'search', true );
