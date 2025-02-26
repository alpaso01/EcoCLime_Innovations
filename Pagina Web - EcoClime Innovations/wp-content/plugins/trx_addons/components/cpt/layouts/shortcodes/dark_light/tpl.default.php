<?php
/**
 * The style "default" of the Dark/Light switcher
 *
 * @package ThemeREX Addons
 * @since v1.6.08
 */

$args = get_query_var( 'trx_addons_args_sc_layouts_dark_light' );

$active = 'light';
$scheme = trx_addons_get_theme_option( 'color_scheme', 'default' );
if ( ! empty( $args['schemes_dark'] ) && is_array( $args['schemes_dark'] ) ) {
	foreach( $args['schemes_dark'] as $s ) {
		if ( $s['area'] == 'content' && $s['scheme'] == $scheme ) {
			$active = 'dark';
			break;
		}
	}
}

$breakpoints = apply_filters( 'trx_addons_filter_responsive_breakpoints', array(
	'desktop' => 10000,
	'tablet'  => 1279,
	'mobile'  => 767
) );

?><a href="#" <?php if ( ! empty( $args['id'] ) ) echo ' id="' . esc_attr( $args['id'] ) . '"'; ?>
	class="sc_layouts_dark_light sc_layouts_dark_light_<?php echo esc_attr( $args['type'] );
				trx_addons_cpt_layouts_sc_add_classes( $args );
				echo ' sc_layouts_dark_light_active_' . esc_attr( $active );
				echo ' sc_layouts_dark_light_effect_' . esc_attr( $args['effect'] );
				// Add responsive positions and offsets
				$position = '';
				$offset_x = '';
				$offset_y = '';
				$was_non_static = false;
				$class = trx_addons_generate_id( 'trx_addons_inline_' );
				echo ' ' . esc_attr( $class );
				$css = '';
				foreach( $breakpoints as $bp => $bp_max ) {
					$suffix = $bp == 'desktop' ? '' : '_' . $bp;
					$position_changed = false;
					$bp_css = '';
					if ( ! empty( $args['position' . $suffix] ) || $position === '' ) {
						$position = ! empty( $args['position' . $suffix] ) ? $args['position' . $suffix] : 'static';
						$position_changed = true;
						$bp_css .= 'position:' . esc_attr( $position == 'static' ? 'relative' : 'fixed' ) . ';'
								.   'z-index:' . esc_attr( $position == 'static' ? '1' : '999999' ) . ';';
					}
					if ( $position != 'static' ) {
						$was_non_static = true;
						if ( ! empty( $args['offset_x' . $suffix] ) || $offset_x === '' || $position_changed ) {
							$offset_x = ! empty( $args['offset_x' . $suffix] ) ? $args['offset_x' . $suffix] : ( $offset_x === '' ? 0 : $offset_x );
							if ( is_array( $offset_x ) ) {
								$offset_x = ( isset( $offset_x['size'] ) ? $offset_x['size'] : 0 ) . ( isset( $offset_x['unit'] ) ? $offset_x['unit'] : 'px' );
							}
							$bp_css .= 'left:' . ( in_array( $position, array( 'tl', 'bl' ) ) ? esc_attr( trx_addons_prepare_css_value( $offset_x ) ) : 'auto' ) . ';'
									. 'right:' . ( in_array( $position, array( 'tr', 'br' ) ) ? esc_attr( trx_addons_prepare_css_value( $offset_x ) ) : 'auto' ) . ';';
						}
						if ( ! empty( $args['offset_y' . $suffix] ) || $offset_y === '' || $position_changed ) {
							$offset_y = ! empty( $args['offset_y' . $suffix] ) ? $args['offset_y' . $suffix] : ( $offset_y === '' ? 0 : $offset_y );
							if ( is_array( $offset_y ) ) {
								$offset_y = ( isset( $offset_y['size'] ) ? $offset_y['size'] : 0 ) . ( isset( $offset_y['unit'] ) ? $offset_y['unit'] : 'px' );
							}
							$bp_css .=   'top:' . ( in_array( $position, array( 'tl', 'tr' ) ) ? esc_attr( trx_addons_prepare_css_value( $offset_y ) ) : 'auto' ) . ';'
									. 'bottom:' . ( in_array( $position, array( 'bl', 'br' ) ) ? esc_attr( trx_addons_prepare_css_value( $offset_y ) ) : 'auto' ) . ';';
						}
					} else if ( $was_non_static ) {
						$bp_css .= 'left:auto;right:auto;top:auto;bottom:auto;';
					}
					if ( ! empty( $bp_css ) ) {
						$css .= ( $bp != 'desktop' ? '@media (max-width: ' . esc_attr( $bp_max ) . 'px) {' : '' )
									. '.' . esc_attr( $class ) . ' {'
										. $bp_css
									. '}'
								. ( $bp != 'desktop' ? '}' : '' );
					}
				}
				trx_addons_add_inline_css( $css );
	?>"<?php
	if ( ! empty( $args['css'] ) ) {
		echo ' style="' . esc_attr( $args['css'] ) . '"';
	}
	trx_addons_sc_show_attributes( 'sc_layouts_dark_light', $args, 'sc_wrapper' );
	?>
	data-permanent="<?php echo esc_attr( ! empty( $args['permanent'] ) ? 1 : 0 ); ?>"
	data-schemes="<?php echo esc_attr( json_encode( array( 'light' => $args['schemes_light'], 'dark' => $args['schemes_dark'] ) ) ); ?>"
><?php
	// Light icon
	$icon = ! empty( $args['icon_light'] )
			? ( is_numeric( $args['icon_light'] ) && (int)$args['icon_light'] > 0
				? trx_addons_get_attachment_url( $args['icon_light'], apply_filters( 'trx_addons_filter_thumb_size', trx_addons_get_thumb_size( 'masonry' ), 'dark-light' ) )
				: $args['icon_light']
				)
			: trx_addons_get_file_dir( TRX_ADDONS_PLUGIN_CPT_LAYOUTS_SHORTCODES . 'dark_light/images/light.svg' );
	$icon_type = trx_addons_get_file_ext( $icon );
	if ( ! isset( $args['icon_light_extra'] ) ) {
		if ( ! empty( $args['icon_color_light'] ) ) {
			trx_addons_add_inline_css( sprintf( '.%s .sc_layouts_dark_light_light svg * { fill: %s; }', $class, esc_attr( $args['icon_color_light'] ) ) );
		}
		if ( ! empty( $args['bg_color_light'] ) ) {
			trx_addons_add_inline_css( sprintf( '.%s.sc_layouts_dark_light_active_light { background-color: %s; }', $class, esc_attr( $args['bg_color_light'] ) ) );
		}
		if ( ! empty( $args['bd_color_light'] ) ) {
			trx_addons_add_inline_css( sprintf( '.%s.sc_layouts_dark_light_active_light { border-color: %s; }', $class, esc_attr( $args['bd_color_light'] ) ) );
		}
	}
	?><span class="sc_layouts_dark_light_item sc_layouts_dark_light_type_<?php echo esc_attr( $icon_type ); ?> sc_layouts_dark_light_light<?php if ( $active == 'light' ) echo ' sc_layouts_dark_light_active'; ?>"><?php
		if ( $icon_type == 'svg' ) {
			trx_addons_show_layout( trx_addons_get_svg_from_file( $icon ) );
		} else {
			?><img src="<?php echo esc_url( $icon ); ?>" alt="" /><?php
		}
	?></span><?php
	// Dark icon
	$icon = ! empty( $args['icon_dark'] )
			? ( is_numeric( $args['icon_dark'] ) && (int)$args['icon_dark'] > 0
				? trx_addons_get_attachment_url( $args['icon_dark'], apply_filters( 'trx_addons_filter_thumb_size', trx_addons_get_thumb_size( 'masonry' ), 'dark-light' ) )
				: $args['icon_dark']
				)
			: trx_addons_get_file_dir( TRX_ADDONS_PLUGIN_CPT_LAYOUTS_SHORTCODES . 'dark_light/images/dark.svg' );
	$icon_type = trx_addons_get_file_ext( $icon );
	if ( ! isset( $args['icon_dark_extra'] ) ) {
		if ( ! empty( $args['icon_color_dark'] ) ) {
			trx_addons_add_inline_css( sprintf( '.%s .sc_layouts_dark_light_dark svg * { fill: %s; }', $class, esc_attr( $args['icon_color_dark'] ) ) );
		}
		if ( ! empty( $args['bg_color_dark'] ) ) {
			trx_addons_add_inline_css( sprintf( '.%s.sc_layouts_dark_light_active_dark { background-color: %s; }', $class, esc_attr( $args['bg_color_dark'] ) ) );
		}
		if ( ! empty( $args['bd_color_dark'] ) ) {
			trx_addons_add_inline_css( sprintf( '.%s.sc_layouts_dark_light_active_light { border-color: %s; }', $class, esc_attr( $args['bd_color_dark'] ) ) );
		}
	}
	?><span class="sc_layouts_dark_light_item sc_layouts_dark_light_dark<?php if ( $active != 'light' ) echo ' sc_layouts_dark_light_active'; ?>"><?php
		if ( $icon_type == 'svg' ) {
			trx_addons_show_layout( trx_addons_get_svg_from_file( $icon ) );
		} else {
			?><img src="<?php echo esc_url( $icon ); ?>" alt="" /><?php
		}
	?></span>
</a><?php

trx_addons_sc_layouts_showed( 'sc_dark_light', true );
