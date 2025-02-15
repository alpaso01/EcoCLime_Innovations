/**
 * Shortcode AI Chat Topics
 *
 * @package ThemeREX Addons
 * @since v2.22.0
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

		// Init AI Chat
		container.find( '.sc_chat_topics:not(.sc_chat_topics_inited)' ).each( function() {

			var $wrapper = jQuery( this ),
				$topics = $wrapper.addClass( 'sc_chat_topics_inited' ).find( '.sc_chat_topics_item > a' );

			if ( ! $topics.length ) {
				return;
			}

			var chat_id = $wrapper.data( 'chat-id' ) || '',
				$chat_prompt = jQuery( ( chat_id ? '#' + chat_id + ' ' : '' ) + '.sc_chat_form_field_prompt_text' ).eq(0);
			
			if ( ! $chat_prompt.length ) {
				return;
			}

			$topics
				.on( 'keypress', function(e) {
					if ( e.keyCode == 13 ) {
						e.preventDefault();
						jQuery(this).trigger( 'click' );
					}
				} )
				.on( 'click', function(e) {
					$chat_prompt.val( jQuery(this).text() ).trigger( 'change' );
				} );

		} );

	} );

	// Add a new topic to the chat topics after the chat messages are updated
	trx_addons_add_action( 'trx_addons_action_sc_chat_updated', function( chat, $sc ) {
		if ( ! chat || chat.length == 0 || ! TRX_ADDONS_STORAGE['user_logged_in'] ) {
			return;
		}
		var sc_id = $sc.attr('id'),
			chat_id = sc_id.slice(-3) == '_sc' ? sc_id.slice(0, -3) : sc_id;
		var $topics = chat_id ? jQuery( '.sc_chat_topics[data-chat-id="' + chat_id + '"]' ) : jQuery( '.sc_chat_topics' ).eq(0);
		if ( chat_id && ! $topics.length ) {
			$topics = jQuery( '.sc_chat_topics' ).eq(0);
		}
		if ( $topics.length ) {
			var chat_topic = chat[0].content,
				found = false,
				max_items = $topics.data( 'max-items' ) || 5;
			$topics.find( '.sc_chat_topics_item a' ).each( function() {
				var $self = jQuery(this);
				if ( ! found && $self.text() == chat_topic ) {
					found = true;
				}
			} );
			if ( ! found ) {
				var $items = $topics.find( '.sc_chat_topics_item' ),
					$item = $items.eq(0).clone();
				$item.find( 'a' ).text( chat[0].content );
				if ( $items.length >= max_items ) {
					$items.eq( $items.length - 1 ).remove();
				}
				$topics.find( '.sc_chat_topics_list' ).prepend( $item );
			}
		}
	} );

} );