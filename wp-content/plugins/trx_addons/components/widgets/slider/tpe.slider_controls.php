<?php
/**
 * Template to represent shortcode as a widget in the Elementor preview area
 *
 * Written as a Backbone JavaScript template and using to generate the live preview in the Elementor's Editor
 *
 * @package ThemeREX Addons
 * @since v1.6.41
 */

extract(get_query_var('trx_addons_args_widget_slider_controls'));
?><#
var id = settings._element_id ? settings._element_id + '_sc' : 'sc_slider_controls_'+(''+Math.random()).replace('.', '');
if (settings.slider_id !='') {
	#><div id="{{ id }}"
			class="sc_slider_controls sc_slider_controls_{{ settings.controls_style }} slider_pagination_style_{{ settings.pagination_style }} sc_align_{{ settings.align }}"
			data-slider-id="{{ settings.slider_id }}"
			data-style="{{ settings.controls_style }}"
			data-pagination-style="{{ settings.pagination_style }}"
			data-pagination-html-tag="{{ settings.pagination_title_html_tag }}">
		<div class="slider_controls_wrap<#
			if (settings.hide_prev == 0) print(' with_prev');
			if (settings.hide_next == 0) print(' with_next');
		#>"><#
			var arrow_prev = settings.icon ? settings.icon : 'slider_arrow_default',
				arrow_next = arrow_prev ? arrow_prev.replace( 'left', 'right' ) : '';
			if (settings.hide_prev == 0) { 
				#><a class="slider_prev <# if (settings.title_prev != '') print(' with_title'); #> {{ arrow_prev }}" href="#">{{ settings.title_prev }}</a><#
			}
			if (settings.hide_next == 0) { 
				#><a class="slider_next <# if (settings.title_next != '') print(' with_title'); #> {{ arrow_next }}" href="#">{{ settings.title_next }}</a><#
			}
			if (settings.pagination_style != 'none') { 
				#><div class="slider_pagination_wrap"><#
					if (settings.pagination_style == 'progressbar') {
						#><span class="slider_progress_bar"></span><#
					}
				#></div><#
			}
		#></div>
	</div><#
}
#>