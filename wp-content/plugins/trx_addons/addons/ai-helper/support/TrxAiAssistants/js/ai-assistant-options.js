function trx_addons_ai_assistant_add_support() {
	var $support_key = prompt( TRX_ADDONS_STORAGE['msg_ai_assistant_add_support'], '' );
	if ( $support_key ) {
		// Send support key to the server
		jQuery.post(
			TRX_ADDONS_STORAGE['ajax_url'],
			{
				action: 'trx_addons_ai_assistant_add_support',
				nonce: TRX_ADDONS_STORAGE['ajax_nonce'],
				key: $support_key
			},
			function( response ) {
				var rez = JSON.parse( response );
				if ( rez.error === '' ) {
					trx_addons_msgbox_success( rez.data, TRX_ADDONS_STORAGE['msg_ai_assistant_add_support_header'] );
				} else {
					trx_addons_msgbox_warning( rez.error, TRX_ADDONS_STORAGE['msg_ai_assistant_add_support_header'] );
				}
			}
		);
		// Close the message box in the chat popup
		jQuery('.sc_chat_message_close').trigger( 'click' );
	}
}