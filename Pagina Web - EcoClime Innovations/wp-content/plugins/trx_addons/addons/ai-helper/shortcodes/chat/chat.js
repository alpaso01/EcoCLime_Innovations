/**
 * Shortcode AI Chat
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
		container.find( '.sc_chat:not(.sc_chat_inited)' ).each( function() {

			var $sc = jQuery( this ).addClass( 'sc_chat_inited' ),
				sc_id = $sc.attr('id') || '',
				$form = $sc.find( '.sc_chat_form' ),
				$prompt = $sc.find( '.sc_chat_form_field_prompt_text' ),
				$button = $sc.find( '.sc_chat_form_field_prompt_button' ),
				$attachments_field = $sc.find( '.sc_chat_form_field_upload_attachments_field' ),
				$attachments_list = $sc.find( '.sc_chat_form_field_upload_attachments_list' ),
				$result = $sc.find( '.sc_chat_list' ),
				$start_new = $sc.find( '.sc_chat_form_start_new' ),
				chat = [],
				chat_position = 0;

			// Return an attachment type by file name
			var attachment_type = function( file ) {
				var ext = trx_addons_get_file_ext( file ).toLowerCase();
				return ['ai', 'avi', 'css', 'csv', 'dbf', 'doc', 'docx', 'dwg', 'gif', 'htm', 'html',
					    'jpg', 'jpeg', 'js', 'json', 'mp3', 'mp4', 'pdf', 'png', 'ppt', 'pptx',
						'psd', 'rtf', 'svg', 'txt', 'xls', 'xlsx', 'xml', 'zip' ].indexOf( ext ) >= 0 ? ext : 'unknown';
			};

			// Update the list of attachments on change the file field
			if ( $attachments_field.length ) {
				$attachments_field.on( 'change', function(e) {
					var files = e.target.files;
					if ( files && files.length ) {
						var clear_icon = $attachments_list.data( 'clear-icon' ) || 'trx_addons_icon-cancel-2';
						$attachments_list.empty();
						for ( var i = 0; i < files.length; i++ ) {
							$attachments_list.append(
								'<span class="sc_chat_form_field_upload_attachments_list_item" title="' + files[i].name + '">'
									+ '<span class="sc_chat_form_field_upload_attachments_list_item_icon" style="background-image: url(' + TRX_ADDONS_STORAGE['plugin_url'] + '/css/file-types/' + attachment_type( files[i].name ) + '.png)"></span>'
									+ '<span class="sc_chat_form_field_upload_attachments_list_item_name">' + files[i].name + '</span>'
								+ '</span>'
							);
						}
						$attachments_list.append(
							'<a href="#" class="sc_chat_form_field_upload_attachments_list_clear">'
								+ '<span class="sc_chat_form_field_upload_attachments_list_clear_icon ' + clear_icon + '"></span>'
								+ '<span class="sc_chat_form_field_upload_attachments_list_clear_name">' + TRX_ADDONS_STORAGE['msg_ai_helper_sc_chat_clear'] + '</span>'
							+ '</a>'
						);
						$sc.addClass( 'sc_chat_with_attachments_selected' );
					} else {
						$attachments_list.empty();
						$sc.removeClass( 'sc_chat_with_attachments_selected' );
					}
				} );
			}

			// Remove attachments on click on the clear link
			if ( $attachments_list.length ) {
				$attachments_list.on( 'click', '.sc_chat_form_field_upload_attachments_list_clear', function(e) {
					e.preventDefault();
					$attachments_field.val( '' ).trigger( 'change' );
					return false;
				} );
			}

			// Open/Close chat in the popup
			if ( $sc.hasClass( 'sc_chat_popup' ) ) {
				var $popup_content = $sc.find( '.sc_chat_content' ),
					$popup_button = $sc.find( '.sc_chat_popup_button' );
				$popup_button.on( 'click', function(e) {
					e.preventDefault();
					$sc.addClass( 'sc_chat_opening' );
					$popup_content.slideToggle( function() {
						$sc.removeClass( 'sc_chat_opening' );
						$sc.toggleClass( 'sc_chat_opened' );
						if ( $sc.hasClass( 'sc_chat_opened' ) ) {
							if ( $popup_button.data( 'chat-opened-svg' ) ) {
								$popup_button.find( '.sc_chat_popup_button_svg' ).html( $popup_button.data( 'chat-opened-svg' ) );
							} else if ( $popup_button.data( 'chat-opened-image' ) ) {
								$popup_button.find( '.sc_chat_popup_button_image' ).attr( 'src', $popup_button.data( 'chat-opened-image' ) );
							} else if ( $popup_button.data( 'chat-opened-icon' ) ) {
								$popup_button.find( '.sc_chat_popup_button_icon' ).removeClass( $popup_button.data( 'chat-icon' ) ).addClass( $popup_button.data( 'chat-opened-icon' ) );
							}
							$document.trigger( 'action.sc_chat_popup_opened', [ $popup_content ] );
						} else {
							if ( $popup_button.data( 'chat-svg' ) ) {
								$popup_button.find( '.sc_chat_popup_button_svg' ).html( $popup_button.data( 'chat-svg' ) );
							} else if ( $popup_button.data( 'chat-image' ) ) {
								$popup_button.find( '.sc_chat_popup_button_image' ).attr( 'src', $popup_button.data( 'chat-image' ) );
							} else if ( $popup_button.data( 'chat-icon' ) ) {
								$popup_button.find( '.sc_chat_popup_button_icon' ).removeClass( $popup_button.data( 'chat-opened-icon' ) ).addClass( $popup_button.data( 'chat-icon' ) );
							}
							$document.trigger( 'action.sc_chat_popup_closed', [ $popup_content ] );
						}
					} );
					return false;
				} );
				// Open on load
				if ( $sc.hasClass( 'sc_chat_open_on_load' ) ) {
					$popup_button.trigger( 'click' );
				}
			}

			// Enable/disable button "Generate"
			$prompt.on( 'change keyup', function(e) {
				$button.toggleClass( 'sc_chat_form_field_prompt_button_disabled', $prompt.val() == '' );
			} )
			.trigger( 'change' );

			// Set padding for the prompt field to avoid overlapping the button
			if ( $button.css( 'position' ) == 'absolute' ) {
				var set_prompt_padding = function() {
					var button_size = Math.ceil( $button.outerWidth() ) + 10;
					if ( button_size > 0 ) {
						$prompt.css( 'padding-right', button_size + 'px' );
					}
				};
				set_prompt_padding();
				$window.on( 'resize', set_prompt_padding );
				$document.on( 'action.sc_chat_popup_opened', set_prompt_padding );
			}
				
			// Close a message popup on click on the close button
			$sc.on( 'click', '.sc_chat_message_close', function(e) {
				e.preventDefault();
				$form.find( '.sc_chat_message' ).slideUp();
				return false;
			} );

			// Set a new list of messages
			$sc.on( 'trx_addons_action_sc_chat_update', function(e, messages) {
				if ( ! messages || ! messages.length ) {
					return;
				}
				// Clear previous chat
				chat = [];
				chat_position = 0;
				$result.empty();
				$form.data( 'chat-thread-id', '' );
				// Hide the link "Start new chat"
				$start_new.removeClass( 'trx_addons_hidden' );
				// Hide the message popup
				$form.find( '.sc_chat_message' ).slideUp();
				// Add new messages to the chat
				for ( var i = 0; i < messages.length; i++ ) {
					add_to_chat( messages[ i ].role, messages[ i ].content, messages[ i ].attachments );
				}
				// Clear the prompt field and set focus to it
				$prompt.val( '' ).trigger( 'change' );
				$prompt.get(0).focus();
			} );

			// Start a new chat
			$start_new.on( 'click', function(e) {
				e.preventDefault();
				// Clear previous chat
				chat = [];
				chat_position = 0;
				$result.empty();
				$form.data( 'chat-thread-id', '' );
				trx_addons_do_action( 'trx_addons_action_sc_chat_updated', chat, $sc );
				// Hide the link "Start new chat"
				$start_new.addClass( 'trx_addons_hidden' );
				// Hide the message popup
				$form.find( '.sc_chat_message' ).slideUp();
				// Clear the prompt field and set focus to it
				$prompt.val( '' ).trigger( 'change' );
				$prompt.get(0).focus();
				return false;
			} );

			// Change the prompt text on click on the tag
			$sc.on( 'click', '.sc_chat_form_field_tags_item', function(e) {
				e.preventDefault();
				var $self = jQuery( this );
				if ( ! $prompt.attr( 'disabled' ) ) {
					$prompt.val( $self.data( 'tag-prompt' ) ).trigger( 'change' ).get(0).focus();
				}
				return false;
			} );

			// Show previous/next message
			$prompt.on( 'keydown', function(e) {
				var i;
				if ( e.keyCode == 38 ) {
					e.preventDefault();
					if ( chat_position > 0 ) {
						for ( i = chat_position - 1; i >= 0; i-- ) {
							if ( chat[i].role == 'user' ) {
								$prompt.val( chat[i].content ).trigger( 'change' );
								chat_position = i;
								break;
							}
						}
					}
				} else if ( e.keyCode == 40 ) {
					e.preventDefault();
					if ( chat_position < chat.length - 1 ) {
						for ( i = chat_position + 1; i <= chat.length; i++ ) {
							if ( i == chat.length ) {
								$prompt.val( '' ).trigger( 'change' );
								chat_position = i;
								break;
							} else if ( chat[i].role == 'user' ) {
								$prompt.val( chat[i].content ).trigger( 'change' );
								chat_position = i;
								break;
							}
						}
					}
				}
			} );

			// Generate answer
			$prompt.on( 'keypress', function(e) {
				if ( e.keyCode == 13 ) {
					e.preventDefault();
					$button.trigger( 'click' );
				}
			} );

			$button.on( 'click', function(e) {
				e.preventDefault();
				var prompt = $prompt.val(),
					settings = $form.data( 'chat-settings' );

				if ( ! prompt || ! checkLimits() ) {
					return;
				}

				// Add prompt to the chat
				add_to_chat( 'user', prompt, $attachments_field.length ? $attachments_field.get(0).files : [] );

				// Display loading animation
				show_loading();

				// Display the link "Start new chat"
				$start_new.removeClass( 'trx_addons_hidden' );

				// Send request via AJAX
				var attachments_present = $attachments_field.length && $attachments_field.get(0).files.length > 0,
					args = {
						nonce: TRX_ADDONS_STORAGE['ajax_nonce'],
						action: 'trx_addons_ai_helper_chat',
						count: ( trx_addons_get_cookie( 'trx_addons_ai_helper_chat_count' ) || 0 ) * 1 + 1,
						chat: attachments_present ? chat : JSON.stringify( chat ),
						settings: settings,
						thread_id: $form.data( 'chat-thread-id' ) || '',
					};
				if ( attachments_present ) {
					args = prepare_chat_args_for_ajax( args );
					jQuery.ajax( {
						url: TRX_ADDONS_STORAGE['ajax_url'],
						type: "POST",
						data: args,
						processData: false,		// Don't process fields to the string
						contentType: false,		// Prevent content type header
						success: show_answer
					} );
				} else {
					jQuery.post( TRX_ADDONS_STORAGE['ajax_url'], args, show_answer );
				}
			} );

			// Fetch answer
			function fetch_answer( data ) {
				jQuery.post( TRX_ADDONS_STORAGE['ajax_url'], {
					nonce: TRX_ADDONS_STORAGE['ajax_nonce'],
					action: 'trx_addons_ai_helper_chat_fetch',
					thread_id: data.thread_id,
					run_id: data.run_id,
					settings: $form.data( 'chat-settings' )
				}, show_answer );
			}

			// Show answer
			function show_answer( response ) {
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

				// Save thread ID
				if ( rez.thread_id ) {
					$form.data( 'chat-thread-id', rez.thread_id );
				}
				// If queued - fetch answer again
				if ( rez.finish_reason == 'queued' ) {
					var time = rez.fetch_time ? rez.fetch_time : 4000;
					setTimeout( function() {
						fetch_answer( rez );
					}, time );
				} else {
					// Hide loading animation
					hide_loading();
					// Set focus to the prompt field
					$prompt.get(0).focus();
					// Show result
					if ( ! rez.error && rez.data.text ) {
						add_to_chat( 'assistant', rez.data.text, rez.data.attachments );
						$prompt.val( '' ).trigger( 'change' );
						if ( $attachments_field.length ) {
							$attachments_field.val( '' ).trigger( 'change' );
						}
						updateLimitsCounter();
						updateRequestsCounter();
						trx_addons_do_action( 'trx_addons_action_sc_chat_updated', chat, $sc );
					}
					if ( rez.error ) {
						showMessage( rez.error, 'error' );
					} else if ( rez.data.message ) {
						showMessage( rez.data.message, 'info' );
					}
				}
			}

			// Prepare arguments for the AJAX request. If last message in the param 'chat' contain attachments - send all arguments as FormData
			function prepare_chat_args_for_ajax( args, parent_key, data ) {
				if ( parent_key === undefined ) {
					parent_key = '';
					if ( typeof args.chat == 'object' && args.chat.length > 1 ) {
						// Remove all attachments from old messages (leave attachments in the last user message only)
						for ( var i = args.chat.length - 2; i >= 0; i-- ) {
							if ( args.chat[i].attachments ) {
								delete args.chat[i].attachments;
							}
						}
					}
				}
				if ( data === undefined ) {
					data = new FormData();
				}
				var new_key = '';
				for ( var key in args ) {
					new_key = parent_key ? parent_key + '[' + key + ']' : key;
					if ( key == 'attachments' && typeof args[ key ] == 'object' && args[ key ].length > 0 ) {
						for ( var i = 0; i < args[ key ].length; i++ ) {
							if ( typeof args[ key ][ i ] == 'object' ) {
								data.append( new_key + '[]', args[ key ][ i ].name );
								data.append( new_key + '[files][]', args[ key ][ i ], args[ key ][ i ].name );
							} else {
								data.append( new_key + '[]', args[ key ][ i ] );
							}
						}
					} else if ( typeof args[ key ] == 'object' ) {
						data = prepare_chat_args_for_ajax( args[ key ], new_key, data );
					} else {
						data.append( new_key, args[ key ] );
					}
				}
				return data;
			}

			// Return a layout of the chat list item
			function get_chat_list_item( role, message, attachments ) {
				var dt = new Date(),
					hours = dt.getHours(),
					minutes = dt.getMinutes() < 10 ? '0' + dt.getMinutes() : dt.getMinutes(),
					am = hours < 12 ? 'AM' : 'PM',
					use_am = trx_addons_apply_filters( 'trx_addons_filter_sc_chat_time_use_am', true ),
					hours = use_am && hours > 12 ? hours - 12 : hours;
				var style = $form.data( 'chat-style' );
				var id = 'sc_chat_list_item_' + ( '' + Math.random() ).replace( '.', '' ),
					name = [ 'assistant', 'loading' ].indexOf( role ) >= 0 && style.assistant_name
								? style.assistant_name
								: ( role == 'user' && style.user_name
									? style.user_name
									: ''
									),
					icon = [ 'assistant', 'loading' ].indexOf( role ) >= 0 && style.assistant_icon
								? style.assistant_icon
								: ( role == 'user' && style.user_icon
									? style.user_icon
									: ''
									),
					image = [ 'assistant', 'loading' ].indexOf( role ) >= 0 && style.assistant_image
								? style.assistant_image
								: ( role == 'user' && style.user_image
									? style.user_image
									: ''
									),
					image_type = image ? trx_addons_get_file_ext( image ) : '',
					image_svg  = image_type == 'svg'
									? ( [ 'assistant', 'loading' ].indexOf( role ) >= 0 && style.assistant_svg
										? style.assistant_svg
										: ( role == 'user' && style.user_svg
											? style.user_svg
											: trx_addons_get_inline_svg( image, {
													render: function( html ) {
														if ( html ) {
															jQuery( '#' + id + ' .sc_chat_list_item_svg' ).html( html );
														}
													}
												} )
											)
										)
									: '';
				var attachments_list = '';
				if ( attachments && attachments.length > 0 ) {
					attachments_list = '<span class="sc_chat_list_item_attachments_list">';
					for ( var i = 0; i < attachments.length; i++ ) {
						var attachments_name = typeof attachments[i] == 'object' ? attachments[i].name : attachments[i];
						attachments_list += '<span class="sc_chat_list_item_attachments_list_item" title="' + attachments_name + '">'
							+ '<span class="sc_chat_list_item_attachments_list_item_icon" style="background-image: url(' + TRX_ADDONS_STORAGE['plugin_url'] + '/css/file-types/' + attachment_type( attachments_name ) + '.png)"></span>'
							+ '<span class="sc_chat_list_item_attachments_list_item_name">' + attachments_name + '</span>'
						+ '</span>';
					}
					attachments_list += '</span>';
				}
				return trx_addons_apply_filters(
					'trx_addons_filter_sc_chat_list_item',
					'<li id="' + id + '" class="sc_chat_list_item sc_chat_list_item_' + role
						+ ( role == 'loading' ? ' sc_chat_list_item_assistant' : '' )
						+ ( image || icon ? ' sc_chat_list_item_with_avatar' : ' sc_chat_list_item_without_avatar' )
						+ ( attachments_list ? ' sc_chat_list_item_with_attachments' : '' )
					+ '">'
						+ ( image || icon
							? '<span class="sc_chat_list_item_avatar">'
								+ ( image
									? ( image_type == 'svg'
										? '<span class="sc_chat_list_item_svg">' + image_svg + '</span>'
										: '<img src="' + image + '" alt="' + name + '" class="sc_chat_list_item_image">'
										)
									: '<span class="sc_chat_list_item_icon ' + icon + '"></span>'
									)
								+ '</span>'
							: ''
							)
						+ '<span class="sc_chat_list_item_wrap">'
							+ '<span class="sc_chat_list_item_content">' + message + '</span>'
							+ ( role != 'loading' ? '<span class="sc_chat_list_item_time">' + hours + ':' + minutes + ( use_am ? ' ' + am : '' ) + '</span>' : '' )
							+ attachments_list
						+ '</span>'
					+ '</li>',
					role, message, attachments
				);
			}

			// Add a new message to the chat and display it
			function add_to_chat( role, message, attachments ) {
				// Add to the list of messages
				if ( chat.length === 0 || chat[chat.length-1].role != role || chat[chat.length-1].content != message ) {
					var msg_obj = {
						'role': role,
						'content': message
					};
					if ( attachments && attachments.length > 0 ) {
						msg_obj.attachments = attachments;
					}
					chat.push( msg_obj );
					chat_position = chat.length;
					// Display message
					$result.append( get_chat_list_item( role, message, attachments ) );
					// Display chat if it's hidden
					if ( chat.length == 1 ) {
						$result.parent().slideDown( function() {
							scroll_to_bottom();
						});
					} else {
						scroll_to_bottom();
					}
				}
			}

			// Show loading animation
			function show_loading() {
				$form.addClass( 'sc_chat_form_loading' );
				// Add loading animation to the chat
				$result.append( get_chat_list_item( 'loading', '<span class="sc_chat_list_item_loading_dot"></span><span class="sc_chat_list_item_loading_dot"></span><span class="sc_chat_list_item_loading_dot"></span>' ) );
				// Scroll chat to the bottom
				scroll_to_bottom();
			}

			// Hide loading animation
			function hide_loading() {
				$form.removeClass( 'sc_chat_form_loading' );
				$result.find( '.sc_chat_list_item_loading' ).remove();
			}

			// Scroll the chat to the bottom
			function scroll_to_bottom() {
				$result.parent().animate( { scrollTop: $result.parent().prop( 'scrollHeight' ) }, 500 );
			}

			// Show message
			function showMessage( msg, type ) {
				$form
					.find( '.sc_chat_message_inner' )
						.html( msg )
						.parent()
							.toggleClass( 'sc_chat_message_type_error', type == 'error' )
							.toggleClass( 'sc_chat_message_type_info', type == 'info' )
							.toggleClass( 'sc_chat_message_type_success', type == 'success' )
							.addClass( 'sc_chat_message_show' )
							.slideDown();
			}

			// Check limits for generation images
			function checkLimits() {
				// Block the button if the limits are exceeded
				var total, used;
				// Check limits for the generation requests from all users
				var $limit_total = $form.find( '.sc_chat_limits_total_value' ),
					$limit_used  = $form.find( '.sc_chat_limits_used_value' );
				if ( $limit_total.length && $limit_used.length ) {
					total = parseInt( $limit_total.text(), 10 );
					used  = parseInt( $limit_used.text(), 10 );
					if ( ! isNaN( total ) && ! isNaN( used ) ) {
						if ( used >= total ) {
							$button.toggleClass( 'sc_chat_form_field_prompt_button_disabled', true );
							$prompt.attr( 'disabled', 'disabled' );
							showMessage( $form.data( 'chat-limit-exceed' ), 'error' );
							return false;
						}
					}
				}
				// Check limits for the generation requests from the current user
				var $requests_total = $form.find( '.sc_chat_limits_total_requests' ),
					$requests_used  = $form.find( '.sc_chat_limits_used_requests' );
				if ( $requests_total.length && $requests_used.length ) {
					total = parseInt( $requests_total.text(), 10 );
					//used  = parseInt( $requests_used.text(), 10 );
					used = ( trx_addons_get_cookie( 'trx_addons_ai_helper_chat_count' ) || 0 ) * 1;
					if ( ! isNaN( total ) && ! isNaN( used ) ) {
						if ( used >= total ) {
							$button.toggleClass( 'sc_chat_form_field_prompt_button_disabled', true );
							$prompt.attr( 'disabled', 'disabled' );
							showMessage( $form.data( 'chat-limit-exceed' ), 'error' );
							return false;
						}
					}
				}
				return true;
			}
			
			// Update a counter of requests inside a limits text
			function updateLimitsCounter() {
				var total, used;
				// Update a counter of the total requests
				var $limit_total = $form.find( '.sc_chat_limits_total_value' ),
					$limit_used  = $form.find( '.sc_chat_limits_used_value' );
				if ( $limit_total.length && $limit_used.length ) {
					total = parseInt( $limit_total.text(), 10 );
					used  = parseInt( $limit_used.text(), 10 );
					if ( ! isNaN( total ) && ! isNaN( used ) ) {
						if ( used < total ) {
							used = Math.min( used + 1, total );
							$limit_used.text( used );
						}
					}
				}
				// Update a counter of the user requests
				var $requests_total = $form.find( '.sc_chat_limits_total_requests' ),
					$requests_used  = $form.find( '.sc_chat_limits_used_requests' );
				if ( $requests_total.length && $requests_used.length ) {
					total = parseInt( $requests_total.text(), 10 );
					// used  = parseInt( $requests_used.text(), 10 );
					used = ( trx_addons_get_cookie( 'trx_addons_ai_helper_chat_count' ) || 0 ) * 1;
					if ( ! isNaN( total ) && ! isNaN( used ) ) {
						if ( used < total ) {
							used = Math.min( used + 1, total );
							$requests_used.text( used );
						}
					}
				}
			}

			// Update a counter of the generation requests
			function updateRequestsCounter() {
				// Save a number of requests to the client storage
				var count = trx_addons_get_cookie( 'trx_addons_ai_helper_chat_count' ) || 0,
					limit = 60 * 60 * 1000 * 1,	// 1 hour
					expired = limit - ( new Date().getTime() % limit );

				trx_addons_set_cookie( 'trx_addons_ai_helper_chat_count', ++count, expired );
			}
			
			// Load/Save a chat messages to the client storage
			if ( $form.data( 'chat-save-history' ) ) {

				// Load a chat messages from the client storage
				var loadChatStorage = function() {
					// Load saved chats from the client storage
					var storage = trx_addons_get_storage( 'trx_addons_sc_chats' );
					if ( storage && storage.charAt(0) == '{' ) {
						storage = JSON.parse( storage );
					} else {
						storage = {};
					}
					// Remove old chats (more than 1 day)
					for ( var id in storage ) {
						if ( storage[id].date < ( new Date().getTime() - 24 * 60 * 60 * 1000 ) ) {
							delete storage[id];
						}
					}
					return storage;
				};

				// Save a chat messages to the client storage
				var saveChatStorage = function( storage ) {
					trx_addons_set_storage( 'trx_addons_sc_chats', JSON.stringify( storage ) );
				};

				// Update a chat messages in the client storage
				var updateChatStorage = function( chat, $sc ) {
					if ( ! trx_addons_is_local_storage_exists() ) {
						return;
					}
					// Get saved chats from the client storage
					var storage = loadChatStorage();
					// Add new chat to the storage
					if ( sc_id ) {
						if ( ! chat || chat.length == 0 ) {
							delete storage[sc_id];
						} else {
							storage[sc_id] = {
								date: new Date().getTime(),
								chat: chat,
								thread_id: $form.data( 'chat-thread-id' ) || ''
							};
						}
					}
					// Save storage to the client storage
					saveChatStorage( storage );
				};

				// Restore a chat messages from the client storage on page load
				var storage = loadChatStorage();
				if ( sc_id && storage[ sc_id ] ) {
					$sc.trigger( 'trx_addons_action_sc_chat_update', [ storage[ sc_id ].chat ] );
					// Thread ID should be restored after the chat messages are updated because the thread ID is set to empty value while this action
					$form.data( 'chat-thread-id', storage[sc_id].thread_id );
				}

				// Save a chat messages to the client storage after the chat messages are updated
				$sc.on( 'trx_addons_action_sc_chat_update', function( e, messages ) {
					updateChatStorage( messages );
				 } );
				trx_addons_add_action( 'trx_addons_action_sc_chat_updated', updateChatStorage );
			}

		} );

	} );

} );