<?php
/**
 * Template to represent shortcode as a widget in the Elementor preview area
 *
 * Written as a Backbone JavaScript template and using to generate the live preview in the Elementor's Editor
 *
 * @package ThemeREX Addons
 * @since v1.6.41
 */

extract( get_query_var( 'trx_addons_args_sc_layouts_dark_light' ) );
?><#
var active = 'light';

var breakpoints = TRX_ADDONS_STORAGE['elementor_breakpoints']
					? TRX_ADDONS_STORAGE['elementor_breakpoints']
					: {
						'desktop': 10000,
						'tablet': 1279,
						'mobile': 767
					};

// Add responsive positions and offsets
var position = '',
	offset_x = '',
	offset_y = '',
	css = '',
	bp_css = '',
	suffix = '',
	was_non_static = false,
	position_changed = false,
	className = 'trx_addons_inline_' + ( '' + Math.random() ).replace( /\D/g, '' );

for ( var bp in breakpoints ) {
	suffix = bp == 'desktop' ? '' : '_' + bp;
	position_changed = false;
	bp_css = '';
	if ( settings['position' + suffix] !== '' || position === '' ) {
		position = settings['position' + suffix] != '' ? settings['position' + suffix] : 'static';
		position_changed = true;
		bp_css += 'position:' + ( position == 'static' ? 'relative' : 'fixed' ) + ';'
					+ 'z-index:' + ( position == 'static' ? '1' : '999999' ) + ';';
	}
	if ( position != 'static' ) {
		was_non_static = true;
		if ( settings['offset_x' + suffix]['size'] !== '' || offset_x === '' || position_changed ) {
			offset_x = settings['offset_x' + suffix]['size'] !== '' ? settings['offset_x' + suffix]['size'] + settings['offset_x' + suffix]['unit'] : ( offset_x === '' ? 0 : offset_x );
			bp_css +=  'left:' + ( position == 'tl' || position == 'bl' ? trx_addons_prepare_css_value( offset_x ) : 'auto' ) + ';'
					+ 'right:' + ( position == 'tr' || position == 'br' ? trx_addons_prepare_css_value( offset_x ) : 'auto' ) + ';';
		}
		if ( settings['offset_y' + suffix]['size'] !== '' || offset_y === '' || position_changed ) {
			offset_y = settings['offset_y' + suffix]['size'] !== '' ? settings['offset_y' + suffix]['size'] + settings['offset_y' + suffix]['unit'] : ( offset_y === '' ? 0 : offset_y );
			bp_css +=    'top:' + ( position == 'tl' || position == 'tr' ? trx_addons_prepare_css_value( offset_y ) : 'auto' ) + ';'
					+ 'bottom:' + ( position == 'bl' || position == 'br' ? trx_addons_prepare_css_value( offset_y ) : 'auto' ) + ';';
		}
	} else if ( was_non_static ) {
		bp_css += 'left:auto;right:auto;top:auto;bottom:auto;';
	}
	if ( bp_css ) {
		css += ( bp != 'desktop' ? '@media (max-width: ' + breakpoints[ bp ] + 'px) {' : '' )
					+ '.' + className + ' {'
						+ bp_css
					+ '}'
				+ ( bp != 'desktop' ? '}' : '' );
	}
}
if ( css ) {
	#><style type="text/css">{{ css }}</style><#
}

#><a href="#" class="sc_layouts_dark_light sc_layouts_dark_light_{{ settings.type }} sc_layouts_dark_light_effect_{{ settings.effect }} sc_layouts_dark_light_active_{{ active }} {{ className }}<?php
		$element->sc_add_common_classes('sc_layouts_dark_light');
	?>"
	data-permanent="{{ settings.permanent }}"
	data-schemes="<# print( JSON.stringify( { light: settings.schemes_light, dark: settings.schemes_dark } ).replace(/\"/g,'&quot;') ); #>"
><#
	var icon = '', icon_type = 'svg';
	if ( settings.icon_light ) {
		if ( settings.icon_light.value ) {
			icon = settings.icon_light.value.url;
		} else if ( settings.icon_light.url ) {
			icon = settings.icon_light.url;
			icon_type = trx_addons_get_file_ext( icon );
		}
	}
	#><span class="sc_layouts_dark_light_item sc_layouts_dark_light_type_{{ icon_type }} sc_layouts_dark_light_light<# if ( active == 'light' ) print( ' sc_layouts_dark_light_active' ); #>"><#
		if ( icon ) {
			if ( icon_type == 'svg' ) {
				print( trx_addons_get_inline_svg( icon, {
					render: function( html ) {
						if ( html ) {
							elementor.$previewContents.find( '.sc_layouts_dark_light_light' ).html( html );
						}
					}
				} ) );
			} else {
				#><img src="{{ icon }}" alt="" /><#
			}
		} else {
			#><?php trx_addons_show_layout( trx_addons_get_svg_from_file( trx_addons_get_file_dir( TRX_ADDONS_PLUGIN_CPT_LAYOUTS_SHORTCODES . 'dark_light/images/light.svg' ) ) ); ?><#
		}
	#></span><#
	icon = icon_type = 'svg';
	if ( settings.icon_dark ) {
		if ( settings.icon_dark.value ) {
			icon = settings.icon_dark.value.url;
		} else if ( settings.icon_dark.url ) {
			icon = settings.icon_dark.url;
			icon_type = trx_addons_get_file_ext( icon );
		}
	}
	#><span class="sc_layouts_dark_light_item sc_layouts_dark_light_type_{{ icon_type }} sc_layouts_dark_light_dark<# if ( active != 'light' ) print( ' sc_layouts_dark_light_active' ); #>"><#
		if ( icon ) {
			if ( icon_type == 'svg' ) {
				print( trx_addons_get_inline_svg( icon, {
					render: function( html ) {
						if ( html ) {
							elementor.$previewContents.find( '.sc_layouts_dark_light_dark' ).html( html );
						}
					}
				} ) );
			} else {
				#><img src="{{ icon }}" alt="" /><#
			}
		} else {
			#><?php trx_addons_show_layout( trx_addons_get_svg_from_file( trx_addons_get_file_dir( TRX_ADDONS_PLUGIN_CPT_LAYOUTS_SHORTCODES . 'dark_light/images/dark.svg' ) ) ); ?><#
		}
	#></span>
</a>