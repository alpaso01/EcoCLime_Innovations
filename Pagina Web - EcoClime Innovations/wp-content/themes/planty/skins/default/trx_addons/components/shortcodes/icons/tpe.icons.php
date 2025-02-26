<?php
/**
 * Template to represent shortcode as a widget in the Elementor preview area
 *
 * Written as a Backbone JavaScript template and using to generate the live preview in the Elementor's Editor
 *
 * @package ThemeREX Addons
 * @since v1.6.41
 */

extract(get_query_var('trx_addons_args_sc_icons'));
?><#
if ( typeof window.trx_addons_elm_restore_global_params != 'undefined' ) {
settings = trx_addons_elm_prepare_global_params( settings );
}
var id = settings._element_id ? settings._element_id + '_sc' : 'sc_icons_'+(''+Math.random()).replace('.', ''),
    svg_bg_dir = '<?php echo trx_addons_get_svg_from_file(planty_get_file_dir('images/decor.svg')); ?>',
    number = 0;

if (settings.columns.size < 1) settings.columns.size = settings.icons.length;
settings.columns.size = Math.max(1, Math.min(settings.icons.length, settings.columns.size));
if (settings.columns_tablet.size > 0) settings.columns_tablet.size = Math.max(1, Math.min(settings.icons.length, settings.columns_tablet.size));
if (settings.columns_mobile.size > 0) settings.columns_mobile.size = Math.max(1, Math.min(settings.icons.length, settings.columns_mobile.size));
settings.slider = settings.slider > 0 && settings.icons.length > settings.columns.size;
settings.slides_space.size = Math.max(0, settings.slides_space.size);
if (settings.slider > 0 && settings.slider_pagination > 0) settings.slider_pagination = 'bottom';

var column_class = "<?php echo esc_attr(trx_addons_get_column_class(1, '##')); ?>";

#><div id="{{ id }}" class="<# print( trx_addons_apply_filters('trx_addons_filter_sc_classes', 'sc_icons sc_icons_' + settings.type + ' sc_icons_size_' + settings.size + ' sc_align_' + settings.align, settings ) ); #>">
	
	<?php $element->sc_show_titles('sc_icons'); ?>

	<#
    if (settings.slider) {
        settings.slides_min_width = 250;
        #><?php $element->sc_show_slider_wrap_start('sc_icons'); ?><#
    } else if (settings.columns.size > 1) {
		#><div class="sc_icons_columns_wrap sc_item_columns 
			<?php echo esc_attr(trx_addons_get_columns_wrap_class()); ?>
			columns_padding_bottom<#
			if (settings.columns.size >= settings.icons.length ) {
				#> columns_in_single_row<#
			}
		#>"><#
	}
	
	_.each(settings.icons, function(item) {
        number++;
		if (item.color == '') item.color = settings.color;
		var item_color = item.color
							? item.color
							: settings.color,
			item_title_color = typeof item.item_title_color != 'undefined' && item.item_title_color
									? item.item_title_color
									: ( typeof settings.item_title_color != 'undefined' && settings.item_title_color
										? settings.item_title_color
										: ''
										),
			item_text_color = typeof item.item_text_color != 'undefined' && item.item_text_color
									? item.item_text_color
									: ( typeof settings.item_text_color != 'undefined' && settings.item_text_color
										? settings.item_text_color
										: ''
										);
        if (settings.slider == 1) {
            #><div class="slider-slide swiper-slide"><#
        } else if (settings.columns.size > 1) {
			#><div class="<#
				var classes = column_class.replace('##', settings.columns.size);
				if (settings.columns_tablet.size > 0) classes += ' ' + column_class.replace('##', settings.columns_tablet.size) + '-tablet';
				if (settings.columns_mobile.size > 0) classes += ' ' + column_class.replace('##', settings.columns_mobile.size) + '-mobile';
				print(classes);
			#>"><#
		}
        if ( settings.type == 'divider3' )  {
            #><div class="sc_icons_item_wrap"><#
        }
        #><div class="sc_icons_item<# if (item.link.url != '' ) print(' sc_icons_item_linked')
                                      if (item.link_text != '' ) print(' with_more'); #>"><#

            if ( settings.type == 'number' )  {
                #><span class="sc_icons_item_number"><# print(('' + number).padStart( 2, '0' )); #></span><#
            }
            if ( settings.type == 'number' )  {
                #><div class="sc_icons_item_header"><#
            }
            if (item.char != '') {
                #><div class="sc_icons_icon sc_icons_char" data-char="{{ item.char }}"<#
						if (item_color != '') print(' style="color: ' + item_color + '"');
					#>><span data-char="{{ item.char }}"<#
						if (item_color != '') print(' style="color: ' + item_color + '"');
					#>></span></div><#
                    if ( settings.type == 'creative' && svg_bg_dir != '' )  {
                        #><div class="sc_icons_decoration_bg"><# print(svg_bg_dir); #></div><#
                    }
            } else if (item.image.url != '') {
                #><div class="sc_icons_image"><img src="{{ item.image.url }}" alt="<?php esc_attr_e('Icon', 'planty'); ?>"></div><#
                    if ( settings.type == 'creative' && svg_bg_dir != '' )  {
                        #><div class="sc_icons_decoration_bg"><# print(svg_bg_dir); #></div><#
                    }
            } else {
                var icon = trx_addons_get_settings_icon( item.icon );
                if ( trx_addons_is_off(icon) ) icon = '';
                if (typeof item.icon_type == 'undefined') item.icon_type = '';
                if ( icon == '' && item.svg.url != '' ) {
					icon = item.svg.url;
				}

                if (icon != '') {
                    var img = '', svg = '';
                    if (icon.indexOf('//') >= 0) {
                        if (icon.indexOf('.svg') >= 0) {
                            svg = icon;
                            item.icon_type = 'svg';
                        } else {
                            img = icon;
                            item.icon_type = 'images';
                        }
                        icon = trx_addons_get_basename(icon);
                    }
                    #><div class="sc_icons_icon sc_icon_type_{{ item.icon_type }} {{ icon }}"<#
						if (item_color != '') print(' style="color: ' + item_color + '"');
                    #>><#
                        if (svg != '') {
                            var inline_svg = get_inline_svg( svg, view );
							if ( inline_svg ) {
								print( inline_svg );
							} else {
                            #><object type="image/svg+xml" data="{{ svg }}" border="0"></object><#
							}
                        } else if (img != '') {
                            #><img class="sc_icon_as_image" src="{{ img }}" alt="<?php esc_attr_e('Icon', 'planty'); ?>"><#
                        } else {
                            #><span class="sc_icon_type_{{ item.icon_type }} {{ icon }}"
								<# if (item_color != '') print(' style="color:' + item_color + '"'); #>
                            ></span><#
                        }
                        if ( settings.type == 'creative' && svg_bg_dir != '' )  {
                            #><div class="sc_icons_decoration_bg"><# print(svg_bg_dir); #></div><#
                        }
                    #></div><#
                }
            }
            if (item.title != '' && settings.type == 'number' ) {
                item.title = item.title.split('|');
                #><h4 class="sc_icons_item_title"<# if ( item_title_color ) print(' style="color:' + item_title_color + '"'); #>><#
                    _.each(item.title, function(str) {
                        if (item.link.url != '') {
                            #><a href="{{ item.link.url }}"><# print(str); #></a><#
                        } else {
                            #><span><# print(str); #></span><#
                         }
                    });
                #></h4><#
            }
            if ( settings.type == 'number' ) {
                #></div><#
            }
            if ((item.title != '' && settings.type != 'number') || item.description != '' || item.link.url != '') {
                #><div class="sc_icons_item_details"><#
            }
                if (item.title != '' && settings.type != 'number' ) {
                    item.title = item.title.split('|');
					#><h4 class="sc_icons_item_title"<# if ( item_title_color ) print(' style="color:' + item_title_color + '"'); #>><#
                        _.each(item.title, function(str) {
                            if (item.link.url != '') {
                                #><a href="{{ item.link.url }}"><# print(str); #></a><#
                            } else {
                                #><span><# print(str); #></span><#
                            }
                        });
                    #></h4><#
                }
                if (item.description != '') {
					#><div class="sc_icons_item_description"<# if ( item_text_color ) print(' style="color:' + item_text_color + '"'); #>><#
                        if (item.description.indexOf('<p>') < 0) {
                            item.description = item.description
                                                    .replace(/\[(.*)\]/g, '<b>$1</b>')
                                                    .replace(/\n/g, '|')
                                                    .split('|');
                            _.each(item.description, function(str) {
                                #><span><# print(str); #></span><#
                            });
                        } else
                            print(item.description);
                    #></div><#
                }
                if (item.link.url != '' && item.link_text != '' ) {
                    #><a href="{{ item.link.url }}" class="sc_icons_item_more_link"><#
                    if (item.link_text != '' ) {
                        #><span class="link_text"><# print(item.link_text); #></span><span class="link_icon"></span><#
                    }
                    #></a><#
                }
                if (item.link.url != '') {
                 #><a href="{{ item.link.url }}" class="sc_icons_item_link"></a><#
                }

            if ((item.title != '' && settings.type != 'number') || item.description != '' || item.link.url != '') {
                #></div><#
            }
        #></div><#
        if ( settings.type == 'divider3' ) {
            #></div><#
        }
        if (settings.slider || settings.columns.size > 1) {
            #></div><#
        }
	});

    if (settings.slider || settings.columns.size > 1) {
        #></div><#
    }

    if (settings.slider) {
        #><?php $element->sc_show_slider_wrap_end('sc_icons'); ?><#
    }

	#><?php $element->sc_show_links('sc_icons'); ?>

</div><#
if ( typeof window.trx_addons_elm_restore_global_params != 'undefined' ) {
settings = trx_addons_elm_restore_global_params( settings );
}
#>