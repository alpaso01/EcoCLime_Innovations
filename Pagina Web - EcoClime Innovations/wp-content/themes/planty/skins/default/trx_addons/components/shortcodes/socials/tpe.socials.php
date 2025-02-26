<?php
/**
 * Template to represent shortcode as a widget in the Elementor preview area
 *
 * Written as a Backbone JavaScript template and using to generate the live preview in the Elementor's Editor
 *
 * @package ThemeREX Addons
 * @since v1.6.41
 */

extract(get_query_var('trx_addons_args_sc_socials'));
?><#
var id = settings._element_id ? settings._element_id + '_sc' : 'sc_socials_'+(''+Math.random()).replace('.', '');

var icons = [];

_.each(settings.icons, function(item) {
	if (item.link != '') {
		icons.push({
					'name': trx_addons_get_settings_icon( item.icon ),
					'title': item.title,
					'url': item.link
					});
	}
});
if (icons.length == 0) {
	if (settings.icons_type == 'socials') {
		icons = JSON.parse('<?php
			$list = trx_addons_get_option('socials');
			echo addslashes( json_encode( is_array( $list ) ? $list : array() ) );
			?>');
	} else {
		icons = JSON.parse('<?php
			$list = trx_addons_get_option('share');
			if (is_array($list)) {
				foreach($list as $k=>$v) {
					$list[$k]['url'] = "#{$k}";
				}
			}
			echo addslashes( json_encode( is_array( $list ) ? $list : array() ) );
			?>');
	}
}
if (icons.length > 0) {

	#><div id="{{ id }}" class="<# print( trx_addons_apply_filters('trx_addons_filter_sc_classes', 
																   'sc_socials'
																   		+ (settings.type == 'extra' ? ' extra' : '') 
																		+ (settings.type == 'alter' ? ' alter' : '') 
																		+ (settings.type == 'modern' ? ' modern' : '') 
																		+ (settings.type == 'modern_2' ? ' modern_2' : '') 
																		+ (settings.type == 'simple' ? ' simple' : '') 
																		+ ' sc_socials_' 
																		+ (settings.type == 'alter' || settings.type == 'extra' 
																			? 'icons_names' 
																			: (settings.type == 'modern' || settings.type == 'modern_2' || settings.type == 'simple'
																			? 'default'	
																			: settings.type))
																		+ (settings.align != '' ? ' sc_align_'+settings.align : ''), settings ) ); #>">
	
		<?php $element->sc_show_titles('sc_socials'); ?>
	
		<div class="socials_wrap {{ settings.icons_type }}_wrap"><#

        if( 'alter' == settings.type || 'extra' == settings.type ) {
            settings.type = 'icons_names';
        }
        if ('modern' == settings.type || 'modern_2' == settings.type || 'simple' == settings.type ) {
            settings.type = 'default';
        }

		var show = settings.type.replace('default', 'icons'),
			socials_type = "<?php
							$socials_type = trx_addons_get_setting('socials_type');
							echo 'images' == $socials_type ? 'bg' : ('svg' == $socials_type ? 'svg' : 'icons');
							?>";
		print( trx_addons_get_socials_links(icons, socials_type, show) );
		
		#></div><!-- /.socials_wrap -->
	
		<?php $element->sc_show_links('sc_icons'); ?>
	
	</div><!-- /.sc_socials --><#
}
#>