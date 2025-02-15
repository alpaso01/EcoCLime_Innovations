/**
 * Shortcode MGenerator - Generate music with AI
 *
 * @package ThemeREX Addons
 * @since v2.30.4
 */

/* global jQuery, TRX_ADDONS_STORAGE */


jQuery( document ).ready( function() {

	"use strict";

	var $window   = jQuery( window ),
		$document = jQuery( document ),
		$body     = jQuery( 'body' );

	// OnLoad event for music - remove 'loading' class from the wrapper 'sc_mgenerator_music_inner'
	window.trx_addons_ai_helper_mgenerator_load_music = function( $tag ) {
		$tag.parents( '.sc_mgenerator_music_inner' ).removeClass( 'sc_mgenerator_music_loading' );
	};

	// OnError event for music - reload music if error occurs after 5 seconds
	window.trx_addons_ai_helper_mgenerator_reload_music = function( $tag ) {
		if ( ( $tag.data( 'total-reloads' ) || 0 ) < 20 && $tag.attr( 'src' ) !== '' && trx_addons_is_external_url( $tag.attr( 'src' ) ) ) {
			$tag
				.data( 'total-reloads', ( $tag.data( 'total-reloads' ) || 0 ) + 1 )
				.parents( '.sc_mgenerator_music_inner' ).addClass( 'sc_mgenerator_music_loading' );
			var src = $tag.attr( 'src' );
			setTimeout( function() {
				$tag.attr( 'src', '' );
				$tag.attr( 'src', src );
			}, 5000 );
		// } else {
		// 	trx_addons_ai_helper_mgenerator_load_music( $tag );
		}
	};

	$document.on( 'action.init_hidden_elements', function(e, container) {

		if ( container === undefined ) {
			container = $body;
		}

		var animation_out = trx_addons_apply_filters( 'trx_addons_filter_sc_mgenerator_animation_out', 'fadeOutDownSmall animated normal' ),
			animation_in = trx_addons_apply_filters( 'trx_addons_filter_sc_mgenerator_animation_in', 'fadeInUpSmall animated normal' );

		// Init mgenerator
		container.find( '.sc_mgenerator:not(.sc_mgenerator_inited)' ).each( function() {

			var $sc = jQuery( this ).addClass( 'sc_mgenerator_inited' ),
				$form = $sc.find( '.sc_mgenerator_form' ),
				$prompt = $sc.find( '.sc_mgenerator_form_field_prompt_text' ),
				$upload_audio = $sc.find( '.sc_mgenerator_form_field_upload_audio_field' ),
				$button = $sc.find( '.sc_mgenerator_form_field_prompt_button' ),
				$settings = $sc.find( '.sc_mgenerator_form_settings' ),
				$settings_button = $sc.find( '.sc_mgenerator_form_settings_button' ),
				$sampling_rate = $settings.find( '[name="sc_mgenerator_form_settings_field_sampling_rate"]' ),
				$duration = $settings.find( '[name="sc_mgenerator_form_settings_field_duration"]' ),
				$preview = $sc.find( '.sc_mgenerator_music' );

			var need_resize = trx_addons_apply_filters( 'sc_mgenerator_filter_need_resize', $sc.parents( '.sc_switcher' ).length > 0 ),
				resize_delay = trx_addons_apply_filters( 'sc_mgenerator_filter_resize_delay', animation_in || animation_out ? 400 : 0 );
	
			// Show/hide settings popup
			$settings_button.on( 'click', function(e) {
				e.preventDefault();
				$settings.toggleClass( 'sc_mgenerator_form_settings_show' );
				return false;
			} );
			// Hide popup on click outside
			$document.on( 'click', function(e) {
				if ( $settings.hasClass( 'sc_mgenerator_form_settings_show' ) && ! jQuery( e.target ).closest( '.sc_mgenerator_form_settings' ).length ) {
					$settings.removeClass( 'sc_mgenerator_form_settings_show' );
				}
			} );

			$prompt.on( 'change keyup', function() {
				check_fields_visibility();
			} );

			$upload_audio.on( 'change', function() {
				check_fields_visibility();
			} );

			// Inc/Dec the numeric fields on click on the arrows
			$sc.find( '.sc_mgenerator_form_settings_field_numeric_wrap_button_inc,.sc_mgenerator_form_settings_field_numeric_wrap_button_dec' )
				.on( 'click', function(e) {
					e.preventDefault();
					var $self = jQuery( this ),
						$field = $self.parents( '.sc_mgenerator_form_settings_field_numeric_wrap' ).eq(0),
						$input = $field.find( 'input' ),
						val = Number( $input.val() || 0 ),
						step = Number( $input.attr( 'step' ) || 1 ),
						min = Number( $input.attr( 'min' ) || 0 ),
						max = Number( $input.attr( 'max' ) || 1024 );
					if ( $self.hasClass( 'sc_mgenerator_form_settings_field_numeric_wrap_button_inc' ) ) {
						val = Math.min( max, val + step );
					} else {
						val = Math.max( min, val - step );
					}
					// Round the value to 1 decimal place if the step is less than 1 to avoid endless digitals (7.699999999999999 instead of 7.7)
					if ( step < 1 ) {
						val = Math.round( val * 10 ) / 10;
					}
					$input.val( val ).trigger( 'change' );
					return false;
				} );

			// Change the prompt text on click on the tag
			$sc.on( 'click', '.sc_mgenerator_form_field_tags_item,.sc_mgenerator_message_translation', function(e) {
				e.preventDefault();
				var $self = jQuery( this );
				if ( ! $prompt.attr( 'disabled' ) ) {
					$prompt.val( $self.data( 'tag-prompt' ) ).trigger( 'change' ).get(0).focus();
				}
				return false;
			} );

			// Display file name in the decorated upload field text on change the file
			$upload_audio.on( 'change', function(e) {
				var $self = jQuery( this ),
					file = $self.val().replace( /\\/g, '/' ).replace( /.*\//, '' );
				$self.parent()
					.toggleClass( 'filled', true )
					.find( '.sc_mgenerator_form_field_upload_audio_text' )
						.removeClass( 'theme_form_field_placeholder' )
						.text( file );
			} );

			// Close a message popup on click on the close button
			$sc.on( 'click', '.sc_mgenerator_message_close', function(e) {
				e.preventDefault();
				$form.find( '.sc_mgenerator_message' ).slideUp();
				return false;
			} );

			// Trigger the button on Enter key
			$prompt.on( 'keydown', function(e) {
				if ( e.keyCode == 13 ) {
					e.preventDefault();
					$button.trigger( 'click' );
					return false;
				}
			} );

			// Set padding for the prompt field to avoid overlapping the button
			if ( $button.css( 'position' ) == 'absolute' ) {
				var set_prompt_padding = ( function() {
					$prompt.css( 'padding-right', ( Math.ceil( $button.outerWidth() ) + 10 ) + 'px' );
				} )();
				$window.on( 'resize', set_prompt_padding );
			}

			// Check fields visibility
			function check_fields_visibility() {
				// Enable/disable the button on change the prompt text
				var disabled = $prompt.attr( 'disabled' ) == 'disabled' || $prompt.val() == '';
				$button.toggleClass( 'sc_mgenerator_form_field_disabled', disabled );
				if ( need_resize ) {
					$document.trigger( 'action.resize_trx_addons' );
				}
			}

			check_fields_visibility();

			// Send request via AJAX to generate music
			//-----------------------------------------
			$button.on( 'click', function(e) {
				e.preventDefault();

				// if ( TRX_ADDONS_STORAGE['pagebuilder_preview_mode'] ) {
				// 	alert( TRX_ADDONS_STORAGE['msg_ai_helper_mgenerator_disabled'] );
				// 	return false;
				// }

				var action_type = 'generation',
					prompt = $prompt.val(),
					settings = $form.data( 'mgenerator-settings' );

				if ( ! prompt || ! check_limits() ) {
					return false;
				}

				$form.addClass( 'sc_mgenerator_form_loading' );

				// Send request via AJAX
				var data = {
					nonce: TRX_ADDONS_STORAGE['ajax_nonce'],
					action: 'trx_addons_ai_helper_mgenerator',
					action_type: action_type,
					settings: settings,
					prompt: prompt,
					sampling_rate: $sampling_rate.length ? $sampling_rate.val() : 0,
					duration: $duration.length ? $duration.val() : 0,
					count: ( trx_addons_get_cookie( 'trx_addons_ai_helper_mgenerator_count' ) || 0 ) * 1 + 1
				};
				// If upload audio is present - convert data to FormData object and send via method ajax()
				if ( $upload_audio.length && $upload_audio.val() ) {
					var formData = new FormData();
					for ( var key in data ) {
						formData.append( key, data[key] );
					}
					formData.append( 'upload_audio', $upload_audio.get(0).files[0], $upload_audio.get(0).files[0].name );
					jQuery.ajax( {
						url: TRX_ADDONS_STORAGE['ajax_url'],
						type: "POST",
						data: formData,
						processData: false,		// Don't process fields to the string
						contentType: false,		// Prevent content type header
						success: getMusic
					} );
				// Else send data via method post()
				} else {
					jQuery.post( TRX_ADDONS_STORAGE['ajax_url'], data, getMusic );
				}

				// Callback to get music from server
				function getMusic( response ) {
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

					$form.removeClass( 'sc_mgenerator_form_loading' );

					// Show music
					if ( ! rez.error && rez.data ) {
						var i = 0;
						// If need to fetch music after timeout
						if ( rez.data.fetch_id ) {
							for ( i = 0; i < rez.data.fetch_number; i++ ) {
								rez.data.music.push( {
									url: rez.data.fetch_music
								} );
							}
							var time = rez.data.fetch_time ? rez.data.fetch_time : 8000;
							setTimeout( function() {
								fetch_data( rez.data );
							}, time );
						}
						if ( rez.data.music.length > 0 ) {
							if ( ! rez.data.demo ) {
								update_limits_counter( rez.data.music.length );
								update_requests_counter();
							}
							var $music = $preview.find( '.sc_mgenerator_music_item' );
							if ( animation_in || animation_out ) {
								$preview.css( {
									'height': $music.length ? $preview.height() + 'px' : '12em'
								} );
							}
							if ( ! $music.length ) {
								$preview.show();
							} else if ( animation_out ) {
								$music.removeClass( animation_in ).addClass( animation_out );
							}
							setTimeout( function() {
								var currentDate = new Date();
								var timestamp = currentDate.getTime();
								var html = '<div class="sc_mgenerator_music_item_wrap">';
								for ( var i = 0; i < rez.data.music.length; i++ ) {
									html += '<div class="sc_mgenerator_music_item'
												+ ( rez.data.fetch_id ? ' sc_mgenerator_music_fetch' : '' )
												+ ( animation_in ? ' ' + animation_in : '' )
											+ '">'
												+ '<div class="sc_mgenerator_music_inner">'
													+ '<span class="sc_mgenerator_music_wrap">'
														+ '<audio controls="controls" preload="metadata"'	// autoplay="autoplay" loop="loop"
															+ ( rez.data.fetch_id
																? ' id="fetch-' + rez.data.fetch_id + '"'
																: ' src="' + rez.data.music[i].url + '"'
																)
														+ '></audio>'
														+ ( rez.data.fetch_id
															? '<span class="sc_mgenerator_music_fetch_info">'
																	+ '<span class="sc_mgenerator_music_fetch_progress">'
																		+ '<span class="sc_mgenerator_music_fetch_progressbar"></span>'
																	+ '</span>'
																	+ '<span class="sc_mgenerator_music_fetch_msg">' + rez.data.fetch_msg + '</span>'
																+ '</span>'
															: ''
															)
													+ '</span>'
													+ ( ! rez.data.demo && rez.data.show_download
														? '<a href="' + get_download_link( rez.data.music[i].url ? rez.data.music[i].url : '#' ) + '"'
															+ ' download="' + prompt.replace( /[\s]+/g, '-' ).toLowerCase() + '"'
															+ ' data-expired="' + ( ( rez.data.fetch_id ? 0 : timestamp ) + rez.data.show_download * 1000 ) + '"'
															//+ ' target="_blank"'
															+ ' class="sc_mgenerator_music_link sc_button sc_button_default sc_button_size_small sc_button_with_icon sc_button_icon_left"'
															+ '>'
																+ '<span class="sc_button_icon"><span class="trx_addons_icon-download"></span></span>'
																+ '<span class="sc_button_text"><span class="sc_button_title">' + TRX_ADDONS_STORAGE['msg_ai_helper_download'] + '</span></span>'
															+ '</a>'
														: ''
														)
												+ '</div>'
											+ '</div>';
								}
								html += '</div>';
								$preview.html( html );
								$preview
									.find('.sc_mgenerator_music_inner audio')
									.on( 'load', function() {
										trx_addons_ai_helper_mgenerator_load_music( jQuery(this) );
									} )
									.on( 'error', function() {
										trx_addons_ai_helper_mgenerator_reload_music( jQuery(this) );
									} );
								$preview.css( 'height', 'auto' );
								$sc.addClass( 'sc_igenerator_music_show' );
								// Trigger the init event to allow 3rd party script to initialize the music player (for example, MediaElement)
								if ( ! rez.data.fetch_id ) {
									$document.trigger( 'action.init_hidden_elements', [ $preview ] );
								}
								if ( need_resize ) {
									setTimeout( function() {
										$document.trigger( 'action.resize_trx_addons' );
									}, resize_delay );
								}
										
								// Check if download links are expired
								$preview.find( '.sc_mgenerator_music_link' ).on( 'click', function( e ) {
									var currentDate = new Date();
									var timestamp = currentDate.getTime();
									var $link = jQuery( this );
									if ( $link.attr( 'data-expired' ) && parseInt( $link.attr( 'data-expired' ), 10 ) < timestamp ) {
										e.preventDefault();
										if ( typeof trx_addons_msgbox_warning == 'function' ) {
											trx_addons_msgbox_warning(
												TRX_ADDONS_STORAGE['msg_ai_helper_download_expired'],
												TRX_ADDONS_STORAGE['msg_ai_helper_download_error'],
												'attention',
												0,
												[ TRX_ADDONS_STORAGE['msg_caption_ok'] ]
											);
										} else {
											//alert( TRX_ADDONS_STORAGE['msg_ai_helper_download_expired'].replace( /<br>/g, "\n" ) );
											show_message( TRX_ADDONS_STORAGE['msg_ai_helper_download_expired'], 'error' );
										}
										return false;
									}
								} );
							}, $music.length && animation_out ? 700 : 0 );
						}
						if ( rez.data.message ) {
							show_message( rez.data.message, rez.data.message_type );
						}
					} else {
						if ( typeof trx_addons_msgbox_warning == 'function' ) {
							trx_addons_msgbox_warning(
								rez.error,
								TRX_ADDONS_STORAGE['msg_ai_helper_download_error'],
								'attention',
								0,
								[ TRX_ADDONS_STORAGE['msg_caption_ok'] ]
							);
						} else {
							//alert( rez.error );
							show_message( rez.error, 'error' );
						}
					}
				}
			} );

			// Fetch data from the server
			function fetch_data(data) {
				jQuery.post( TRX_ADDONS_STORAGE['ajax_url'], {
					nonce: TRX_ADDONS_STORAGE['ajax_nonce'],
					action: 'trx_addons_ai_helper_fetch_music',
					fetch_id: data.fetch_id,
					fetch_url: data.fetch_url,
				}, function( response ) {
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
					if ( ! rez.error ) {
						if ( rez.data && rez.data.music && rez.data.music.length > 0 ) {
							var music = rez.data.music,
								$fetch = $preview.find( '#fetch-' + data.fetch_id );
							if ( $fetch.length ) {
								// Replace fetch placeholders with real music
								var $download_link;
								var currentDate = new Date();
								var timestamp = currentDate.getTime();
								var $audio;
								for ( var i = 0; i < music.length; i++ ) {
									if ( $fetch.eq( i ).is( 'audio' ) ) {
										$audio = $fetch.eq( i );
									} else {
										$audio = $fetch.eq( i ).find( 'audio' );
									}
									$audio
										.attr( 'src', music[i].url )
										.parents( '.sc_mgenerator_music_fetch' )
											.removeClass( 'sc_mgenerator_music_fetch' )
											.find( '.sc_mgenerator_music_fetch_info')
												.remove();
									$download_link = $fetch.eq( i ).parents( '.sc_mgenerator_music_item' ).find( '.sc_mgenerator_music_link' );
									$download_link.attr( 'href', get_download_link( music[i].url ) );
									$download_link.attr( 'data-expired', parseInt( $download_link.attr( 'data-expired' ), 10 ) + timestamp );
									// Trigger the init event to allow 3rd party script to initialize the music player (for example, MediaElement)
									$document.trigger( 'action.init_hidden_elements', [ $preview ] );
								}
							} else {
								$preview.empty();
								show_message( TRX_ADDONS_STORAGE['msg_ai_helper_fetch_error'], 'error' );
							}
						} else {
							setTimeout( function() {
								fetch_data( data );
							}, data.fetch_time ? data.fetch_time : 8000 );
						}
					} else {
						$preview.empty();
						//alert( rez.error );
						show_message( rez.error, 'error' );
					}
				} );
			}

			// Show message
			function show_message( msg, type ) {
				$form
					.find( '.sc_mgenerator_message_inner' )
						.html( msg )
						.parent()
							.toggleClass( 'sc_mgenerator_message_type_error', type == 'error' )
							.toggleClass( 'sc_mgenerator_message_type_info', type == 'info' )
							.toggleClass( 'sc_mgenerator_message_type_success', type == 'success' )
							.addClass( 'sc_mgenerator_message_show' )
							.slideDown( function() {
								if ( need_resize ) {
									$document.trigger( 'action.resize_trx_addons' );
								}
							} );
			}

			// Check limits for generation music
			function check_limits() {
				// Block the button if the limits are exceeded only if the demo music are not selected in the shortcode params
				if ( ! $form.data( 'mgenerator-demo-music' ) ) {
					var total, used, number;
					// Check limits for the music generation
					var $limit_total = $form.find( '.sc_mgenerator_limits_total_value' ),
						$limit_used  = $form.find( '.sc_mgenerator_limits_used_value' );
					if ( $limit_total.length && $limit_used.length ) {
						total = parseInt( $limit_total.text(), 10 );
						used  = parseInt( $limit_used.text(), 10 );
						number = 1;
						if ( ! isNaN( total ) && ! isNaN( used ) && ! isNaN( number ) ) {
							if ( used >= total ) {
								disable_fields();
								return false;
							}
						}
					}
					// Check limits for the generation requests
					var $requests_total = $form.find( '.sc_mgenerator_limits_total_requests' ),
						$requests_used  = $form.find( '.sc_mgenerator_limits_used_requests' );
					if ( $requests_total.length && $requests_used.length ) {
						total = parseInt( $requests_total.text(), 10 );
						//used  = parseInt( $requests_used.text(), 10 );
						used = ( trx_addons_get_cookie( 'trx_addons_ai_helper_mgenerator_count' ) || 0 ) * 1;
						if ( ! isNaN( total ) && ! isNaN( used ) ) {
							if ( used >= total ) {
								disable_fields();
								return false;
							}
						}
					}
				}
				return true;
			}

			// Disable fields if limits are exceeded
			function disable_fields() {
				$button.toggleClass( 'sc_mgenerator_form_field_disabled', true );
				$prompt.attr( 'disabled', 'disabled' );
				$upload_audio.attr( 'disabled', 'disabled' );
				show_message( $form.data( 'mgenerator-limit-exceed' ), 'error' );
			}

			// Update a counter of generated music inside a limits text
			function update_limits_counter( number ) {
				var total, used;
				// Update a counter of the generated music
				var $limit_total = $form.find( '.sc_mgenerator_limits_total_value' ),
					$limit_used  = $form.find( '.sc_mgenerator_limits_used_value' );
				if ( $limit_total.length && $limit_used.length ) {
					total = parseInt( $limit_total.text(), 10 );
					used  = parseInt( $limit_used.text(), 10 );
					if ( ! isNaN( total ) && ! isNaN( used ) && ! isNaN( number ) ) {
						if ( used < total ) {
							used = Math.min( used + number, total );
							$limit_used.text( used );
						}
					}
				}
				// Update a counter of the generation requests
				var $requests_total = $form.find( '.sc_mgenerator_limits_total_requests' ),
					$requests_used  = $form.find( '.sc_mgenerator_limits_used_requests' );
				if ( $requests_total.length && $requests_used.length ) {
					total = parseInt( $requests_total.text(), 10 );
					// used  = parseInt( $requests_used.text(), 10 );
					used = ( trx_addons_get_cookie( 'trx_addons_ai_helper_mgenerator_count' ) || 0 ) * 1;
					if ( ! isNaN( total ) && ! isNaN( used ) ) {
						if ( used < total ) {
							used = Math.min( used + 1, total );
							$requests_used.text( used );
						}
					}
				}
			}

			// Update a counter of the generation requests
			function update_requests_counter() {
				// Save a number of requests to the client storage
				var count = trx_addons_get_cookie( 'trx_addons_ai_helper_mgenerator_count' ) || 0,
					limit = 60 * 60 * 1000 * 1,	// 1 hour
					expired = limit - ( new Date().getTime() % limit );

				trx_addons_set_cookie( 'trx_addons_ai_helper_mgenerator_count', ++count, expired );
			}

			// Return an URL to download the music
			function get_download_link( url ) {
				return trx_addons_add_to_url( TRX_ADDONS_STORAGE['site_url'], {
					'action': 'trx_addons_ai_helper_mgenerator_download',
					'music': trx_addons_get_file_name( url )
				} );
			}

		} );

	} );

} );