/**
 * Shortcode AI Chat History
 *
 * @package ThemeREX Addons
 * @since v2.26.3
 */

/* global jQuery, TRX_ADDONS_STORAGE */


jQuery( document ).ready( function() {

	"use strict";

	var $window             = jQuery( window ),
		$document           = jQuery( document ),
		$body               = jQuery( 'body' );

	$document.on( 'action.init_hidden_elements', function(e, container) {

		if ( container === undefined ) {
			container = $body;
		}

		// Init AI Chat History
		container.find( '.sc_chat_history:not(.sc_chat_history_inited)' ).each( function() {

			var $history = jQuery( this ).addClass( 'sc_chat_history_inited' );

			if ( ! $history.length ) {
				return;
			}

			var chat_id = $history.data( 'chat-id' ) || '',
				$chat = jQuery( chat_id ? '#' + chat_id + ' .sc_chat,' + '#' + chat_id + '.sc_chat' : '.sc_chat' ).eq(0);
			
			if ( ! $chat.length ) {
				return;
			}

			$history
				.on( 'keypress', '.sc_chat_history_item > a', function(e) {
					if ( e.keyCode == 13 ) {
						e.preventDefault();
						jQuery(this).trigger( 'click' );
					}
				} )
				.on( 'click', '.sc_chat_history_item > a', function(e) {
					var $self = jQuery(this);
					// Restore chat messages
					$chat.trigger( 'trx_addons_action_sc_chat_update', [ $self.data( 'chat-messages' ) ] );
					// Restore thread ID if not empty
					if ( $self.data( 'chat-thread-id' ) ) {
						$chat.find( '.sc_chat_form' ).data( 'chat-thread-id', $self.data( 'chat-thread-id' ) );
					}
				} );

		} );

	} );

	// Add a new topic to the chat history after the chat messages are updated
	trx_addons_add_action( 'trx_addons_action_sc_chat_updated', function( chat, $sc ) {
		if ( ! chat || chat.length == 0 || ! TRX_ADDONS_STORAGE['user_logged_in'] ) {
			return;
		}
		var sc_id = $sc.attr('id'),
			chat_id = sc_id.slice(-3) == '_sc' ? sc_id.slice(0, -3) : sc_id;
		var $history = chat_id ? jQuery( '.sc_chat_history[data-chat-id="' + chat_id + '"]' ) : jQuery( '.sc_chat_history' ).eq(0);
		if ( chat_id && ! $history.length ) {
			$history = jQuery( '.sc_chat_history' ).eq(0);
		}
		if ( $history.length ) {
			var chat_topic = chat[0].content,
				updated = false,
				max_items = $history.data( 'max-items' ) || 5;
			$history.find( '.sc_chat_history_item a' ).each( function() {
				var $self = jQuery(this);
				if ( ! updated && $self.text() == chat_topic ) {
					$self.data( {
						'chat-messages': chat,
						'chat-thread-id': $sc.find( '.sc_chat_form' ).data( 'chat-thread-id' ) || ''
					} );
					updated = true;
				}
			} );
			if ( ! updated ) {
				var $items = $history.find( '.sc_chat_history_item' ),
					$item = $items.eq(0).clone();
				$item.find( 'a' )
					.text( chat[0].content )
					.data( {
						'chat-messages': chat,
						'chat-thread-id': $sc.find( '.sc_chat_form' ).data( 'chat-thread-id' ) || ''
					} );
				if ( $items.length >= max_items ) {
					$items.eq( $items.length - 1 ).remove();
				}
				$history.find( '.sc_chat_history_list' ).prepend( $item );
			}
		}
	} );

} );