<?php
/**
 * Template to represent shortcode as a widget in the Elementor preview area
 *
 * Written as a Backbone JavaScript template and using to generate the live preview in the Elementor's Editor
 *
 * @package ThemeREX Addons
 * @since v2.26.3
 */

extract( get_query_var( 'trx_addons_args_sc_chat_history' ) );

?><#
settings = trx_addons_elm_prepare_global_params( settings );
var history = JSON.parse( '<?php echo addslashes( json_encode( trx_addons_sc_chat_history_get_saved_messages() ) ); ?>' );

var id = settings._element_id ? settings._element_id + '_sc' : 'sc_chat_history_' + ( '' + Math.random() ).replace( '.', '' );

#><div id="{{ id }}" data-chat-id="{{ settings.chat_id }}" class="<# print( trx_addons_apply_filters('trx_addons_filter_sc_classes', 'sc_chat_history sc_chat_history_' + settings.type, settings ) ); #>">

	<?php $element->sc_show_titles( 'sc_chat_history' ); ?>

	<# if ( history.length ) { #>
		<div class="sc_chat_history_content sc_item_content">
			<ul class="sc_chat_history_list">
				<# for ( var i = 0; i < Math.min( settings.number.size, history.length ); i++ ) { #>
					<li class="sc_chat_history_item">
						<a href="javascript:void(0)"
							data-chat-messages="{{{ JSON.stringify( history[i]['messages'] ).replace(/"/g, '&quot;') }}}"
							data-chat-thread-id="{{{ history[i]['thread_id'] ? history[i]['thread_id'] : '' }}}"
						>{{ history[i]['topic'] }}</a>
					</li>
				<# } #>
			</ul>
		</div>
	<# } #>

	<?php $element->sc_show_links('sc_chat_history'); ?>

</div><#

settings = trx_addons_elm_restore_global_params( settings );
#>