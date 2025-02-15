/* global jQuery, TRX_ADDONS_STORAGE */

jQuery( document ).ready( function() {

	// Add buttons with actions to the body
	var $buttons = jQuery( '#trx_addons_ai_helper_process_selection' );
	if ( $buttons.length == 0 ) {
		jQuery( 'body' ).append( '<div id="trx_addons_ai_helper_process_selection">'
									+ '<a href="#" class="trx_addons_ai_helper_process_selection_button" data-command="process_explain">' + TRX_ADDONS_STORAGE['msg_process_explain'] + '</a>'
									+ '<a href="#" class="trx_addons_ai_helper_process_selection_button" data-command="process_summarize">' + TRX_ADDONS_STORAGE['msg_process_summarize'] + '</a>'
									+ '<a href="#" class="trx_addons_ai_helper_process_selection_button" data-command="process_translate">' + TRX_ADDONS_STORAGE['msg_process_translate'] + '</a>'
								+ '</div>'
								);
		$buttons = jQuery( '#trx_addons_ai_helper_process_selection' );
		$buttons.find( '.trx_addons_ai_helper_process_selection_button' ).on( 'click', function(e) {
			e.preventDefault();
			var command = jQuery( this ).data( 'command' );
			process_selection( command );
			return false;
		} );
	}

	// Show buttons with actions 
	function show_buttons() {
		var selection = document.getSelection();

		// Clone DOM-elements from every range (if selection is support multiple ranges - like in Firefox)
		// var cloned = document.createElement('div');
		// for ( var i = 0; i < selection.rangeCount; i++ ) {
		// 	cloned.append( selection.getRangeAt( i ).cloneContents() );
		// }

		// If selection is not empty - show buttons, else - hide buttons
		if ( selection.toString() ) {
			var lastNodeInSelection = selection.getRangeAt( selection.rangeCount - 1 ).endContainer.parentNode;
			var rect = lastNodeInSelection.getBoundingClientRect();
			$buttons
				.css( {
					'left': ( rect.left + trx_addons_window_scroll_left() ) + 'px',
					'top': ( rect.bottom + trx_addons_window_scroll_top() ) + 'px'
				} )
				.toggleClass( 'trx_addons_ai_helper_process_selection_show', true );
		} else {
			$buttons.toggleClass( 'trx_addons_ai_helper_process_selection_show', false );
		}
	};

	// Show/hide buttons on selection change
	document.onselectionchange = show_buttons;

	// Reposition buttons on resize window
	jQuery( window ).on( 'resize', trx_addons_debounce( show_buttons, 100 ) );

	// Send request via AJAX
	function process_selection( command ) {
		$buttons
			.addClass( 'trx_addons_ai_helper_process_selection_loading' )
			.find( '.trx_addons_ai_helper_process_selection_button[data-command="' + command + '"]' )
				.addClass( 'trx_addons_ai_helper_process_selection_button_loading' );
		var selection = document.getSelection().toString();
		var data = {
			nonce: TRX_ADDONS_STORAGE['ajax_nonce'],
			action: 'trx_addons_ai_helper_process_selection',
			command: command,
			content: selection,
			language: navigator.language || 'en-US'
		};
		jQuery.post( TRX_ADDONS_STORAGE['ajax_url'], data, function( response ) {
			show_answer( response, command )
		} );
	}

	// Fetch answer
	function fetch_answer( data, command ) {
		jQuery.post( TRX_ADDONS_STORAGE['ajax_url'], {
			nonce: TRX_ADDONS_STORAGE['ajax_nonce'],
			action: 'trx_addons_ai_helper_process_selection_fetch',
			thread_id: data.thread_id,
			run_id: data.run_id
		}, function( response ) {
			show_answer( response, command )
		} );
	}

	// Show answer
	function show_answer( response, command ) {
		// Prepare response
		var rez = {};
		if ( response == '' || response == 0 ) {
			rez = { error: TRX_ADDONS_STORAGE['msg_ai_helper_error'] };
		} else if ( typeof response == 'string' ) {
			try {
				rez = JSON.parse( response );
			} catch (e) {
				rez = { error: TRX_ADDONS_STORAGE['msg_ai_helper_error'] };
				console.log( response );
			}
		} else {
			rez = response;
		}

		// If queued - fetch answer again
		if ( rez.finish_reason == 'queued' ) {
			var time = rez.fetch_time ? rez.fetch_time : 2000;
			setTimeout( function() {
				fetch_answer( rez, command );
			}, time );
		} else {
			// Remove loading class
			$buttons
				.removeClass( 'trx_addons_ai_helper_process_selection_loading' )
				.find( '.trx_addons_ai_helper_process_selection_button_loading' )
					.removeClass( 'trx_addons_ai_helper_process_selection_button_loading' );
			// Show result
			if ( ! rez.error ) {
				if ( rez.data && rez.data.text ) {
					trx_addons_msgbox( {
						msg: rez.data.text,
						hdr: TRX_ADDONS_STORAGE['msg_' + command],
						icon: 'info',
						delay: 0,
						buttons: [ TRX_ADDONS_STORAGE['msg_caption_ok'] ],
					} );
				}
			} else {
				trx_addons_msgbox_warning( rez.error, TRX_ADDONS_STORAGE['msg_' + command] );
			}
		}
	}

} );