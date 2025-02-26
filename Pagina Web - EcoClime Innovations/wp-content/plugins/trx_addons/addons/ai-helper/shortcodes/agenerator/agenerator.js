/**
 * Shortcode AGenerator - Generate audio with AI
 *
 * @package ThemeREX Addons
 * @since v2.31.0
 */

/* global jQuery, TRX_ADDONS_STORAGE */


jQuery( document ).ready( function() {

	"use strict";

	var $window   = jQuery( window ),
		$document = jQuery( document ),
		$body     = jQuery( 'body' );

	// OnLoad event for audio - remove 'loading' class from the wrapper 'sc_agenerator_audio_inner'
	window.trx_addons_ai_helper_agenerator_load_audio = function( $tag ) {
		$tag.parents( '.sc_agenerator_audio_inner' ).removeClass( 'sc_agenerator_audio_loading' );
	};

	// OnError event for audio - reload audio if error occurs after 5 seconds
	window.trx_addons_ai_helper_agenerator_reload_audio = function( $tag ) {
		if ( ( $tag.data( 'total-reloads' ) || 0 ) < 20 && $tag.attr( 'src' ) !== '' && trx_addons_is_external_url( $tag.attr( 'src' ) ) ) {
			$tag
				.data( 'total-reloads', ( $tag.data( 'total-reloads' ) || 0 ) + 1 )
				.parents( '.sc_agenerator_audio_inner' ).addClass( 'sc_agenerator_audio_loading' );
			var src = $tag.attr( 'src' );
			setTimeout( function() {
				$tag.attr( 'src', '' );
				$tag.attr( 'src', src );
			}, 5000 );
		// } else {
		// 	trx_addons_ai_helper_agenerator_load_audio( $tag );
		}
	};

	$document.on( 'action.init_hidden_elements', function(e, container) {

		if ( container === undefined ) {
			container = $body;
		}

		var animation_out = trx_addons_apply_filters( 'trx_addons_filter_sc_agenerator_animation_out', 'fadeOutDownSmall animated normal' ),
			animation_in = trx_addons_apply_filters( 'trx_addons_filter_sc_agenerator_animation_in', 'fadeInUpSmall animated normal' );

		// Init AGenerator
		container.find( '.sc_agenerator:not(.sc_agenerator_inited)' ).each( function() {

			var $sc = jQuery( this ).addClass( 'sc_agenerator_inited' ),
				$form = $sc.find( '.sc_agenerator_form' ),
				$prompt = $sc.find( '.sc_agenerator_form_field_prompt_text' ),
				$upload_audio = $sc.find( '.sc_agenerator_form_field_upload_audio_field' ),
				$button = $sc.find( '.sc_agenerator_form_field_generate_button' ),
				$settings = $sc.find( '.sc_agenerator_form_settings' ),
				$settings_button = $sc.find( '.sc_agenerator_form_settings_button' ),
				$model = $sc.find( '[name="sc_agenerator_form_field_model"]'),
				$language = $sc.find( '[name="sc_agenerator_form_field_language"]'),
				$emotion = $sc.find( '[name="sc_agenerator_form_field_emotion"]'),
				$voice_openai = $sc.find( '[name="sc_agenerator_form_field_voice_openai"]'),
				$voice_modelslab = $sc.find( '[name="sc_agenerator_form_field_voice_modelslab"]'),
				$upload_voice_modelslab = $sc.find( '.sc_agenerator_form_field_upload_voice_modelslab_field' ),
				$preview = $sc.find( '.sc_agenerator_audio' ),
				$actions = $sc.find( '.sc_agenerator_form_actions' ),
				$actions_slider = $sc.find( '.sc_agenerator_form_actions_slider:not(.sc_agenerator_form_actions_slider_inited)' ).addClass('sc_agenerator_form_actions_slider_inited');

			var need_resize = trx_addons_apply_filters( 'sc_agenerator_filter_need_resize', $sc.parents( '.sc_switcher' ).length > 0 ),
				resize_delay = trx_addons_apply_filters( 'sc_agenerator_filter_resize_delay', animation_in || animation_out ? 400 : 0 );
	
			// Show/hide settings popup
			$settings_button.on( 'click', function(e) {
				e.preventDefault();
				if ( $settings.find( '.sc_agenerator_form_settings_field:not(.trx_addons_hidden)' ).length > 0 ) {
					$settings.toggleClass( 'sc_agenerator_form_settings_show' );
				}
				return false;
			} );
			// Hide popup on click outside
			$document.on( 'click', function(e) {
				if ( $settings.hasClass( 'sc_agenerator_form_settings_show' ) && ! jQuery( e.target ).closest( '.sc_agenerator_form_settings' ).length ) {
					$settings.removeClass( 'sc_agenerator_form_settings_show' );
				}
			} );

			$model.on( 'change', function() {
				check_fields_visibility();
			} );

			$prompt.on( 'change keyup', function() {
				check_fields_visibility();
			} );

			$upload_audio.on( 'change', function() {
				check_fields_visibility();
			} );

			// Inc/Dec the numeric fields on click on the arrows
			$sc
				.find( '.sc_agenerator_form_settings_field_numeric_wrap_button_inc,.sc_agenerator_form_settings_field_numeric_wrap_button_dec,.sc_agenerator_form_field_numeric_wrap_button_inc,.sc_agenerator_form_field_numeric_wrap_button_dec' )
					.on( 'click', function(e) {
						e.preventDefault();
						var $self = jQuery( this ),
							$field = $self.parents( '.sc_agenerator_form_settings_field_numeric_wrap,.sc_agenerator_form_field_numeric_wrap' ).eq(0),
							$input = $field.find( 'input' ),
							val = Number( $input.val() || 0 ),
							step = Number( $input.attr( 'step' ) || 1 ),
							min = Number( $input.attr( 'min' ) || 0 ),
							max = Number( $input.attr( 'max' ) || 1024 );
						if ( $self.hasClass( 'sc_agenerator_form_settings_field_numeric_wrap_button_inc' ) || $self.hasClass( 'sc_agenerator_form_field_numeric_wrap_button_inc' ) ) {
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

			// Display file name in the decorated upload field text on change the file
			$upload_audio.on( 'change', function(e) {
				var $self = jQuery( this ),
					file = $self.val().replace( /\\/g, '/' ).replace( /.*\//, '' );
				$self.parent()
					.toggleClass( 'filled', true )
					.find( '.sc_agenerator_form_field_upload_audio_text' )
						.removeClass( 'theme_form_field_placeholder' )
						.text( file );
			} );
			$upload_voice_modelslab.on( 'change', function(e) {
				var $self = jQuery( this ),
					file = $self.val().replace( /\\/g, '/' ).replace( /.*\//, '' );
				$self.parent()
					.toggleClass( 'filled', true )
					.find( '.sc_agenerator_form_field_upload_voice_modelslab_text' )
						.removeClass( 'theme_form_field_placeholder' )
						.text( file );
			} );

			// Close a message popup on click on the close button
			$sc.on( 'click', '.sc_agenerator_message_close', function(e) {
				e.preventDefault();
				$form.find( '.sc_agenerator_message' ).slideUp();
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

			// Switch actions on click on the action button
			$actions.on( 'click', 'a[data-action]', function(e) {
				e.preventDefault();
				var $self = jQuery( this ),
					$item = $self.parent();
				if ( ! $item.hasClass( 'sc_agenerator_form_actions_item_active' ) ) {
					$item.siblings( '.sc_agenerator_form_actions_item_active' ).removeClass( 'sc_agenerator_form_actions_item_active' );
					$item.addClass( 'sc_agenerator_form_actions_item_active' );
					trx_addons_ai_helper_agenerator_move_slider_to_active_item();
					change_models( $self.data( 'action' ) );
					check_fields_visibility();
				}
				return false;
			} );

			// Move slider to active item
			window.trx_addons_ai_helper_agenerator_move_slider_to_active_item = function() {
				var $active = $actions.find( '.sc_agenerator_form_actions_item_active a' );
				if ( $active.length ) {
					$actions_slider.css( {
						left: $active.offset().left - $actions.offset().left,
						width: $active.outerWidth()
					} );
				}
			};
			trx_addons_ai_helper_agenerator_move_slider_to_active_item();

			// Move slider to active item on resize
			$document.on( 'action.resize_trx_addons', trx_addons_debounce( trx_addons_ai_helper_agenerator_move_slider_to_active_item, 200 ) );

			function change_models( action ) {
				var models = $model.data( 'models' ) || '';
				if ( models[ action ] ) {
					var output = '';
					var group = false;
					for ( var model in models[ action ] ) {
						if ( model.slice( -2 ) == '/-' || models[ action ][ model ].slice( 0, 2 ) == '\\-' ) {
							if ( group ) {
								output += '</optgroup>';
							}
							group = true;
							output += '<optgroup label="' + models[ action ][ model ].slice( 2 ) + '">';
						} else {
							output += '<option value="' + model + '">' + models[ action ][ model ] + '</option>';
						}
					}
					if ( group ) {
						output += '</optgroup>';
					}
					$model.html( output );
				}
			}

			function check_fields_visibility() {

				var action = $actions.find( '.sc_agenerator_form_actions_item_active a' ).data( 'action' );
				var model = ( $model.is('input[type="radio"]') ? $model.filter( ':checked' ).val() : $model.val() ) || '';

				// Enable/disable the button on change the prompt text
				var disabled = false;
				if ( action == 'tts' ) {
					disabled = $prompt.attr( 'disabled' ) == 'disabled' || $prompt.val() == '';
				} else if ( action !== 'tts' ) {
					disabled = $upload_audio.attr( 'disabled' ) == 'disabled' || $upload_audio.val() == '';
				}
				$button.toggleClass( 'sc_agenerator_form_field_disabled', disabled );

				// Show/hide fields
				$form.find( '.sc_agenerator_form_field,.sc_agenerator_form_settings_field' ).each( function() {
					var $self = jQuery( this ),
						actions = $self.data( 'actions' ),
						visible = ! actions || actions.indexOf( action ) >= 0;

					// Check if the field available for the current model
					if ( $self.data( 'models' ) ) {
						var parts = $self.data( 'models' ).split( ',' ),
							allow = false;
						for ( var i = 0; i < parts.length; i++ ) {
							if ( model.indexOf( parts[i] ) >= 0 ) {
								allow = true;
								break;
							}
						}
						visible &&= allow;
					}

					// If the field is 'upload_audio' - hide it for the 'openai' model and action 'tts'
					if ( $self.attr( 'class' ).indexOf( 'upload_audio' ) > 0 ) {
						visible &&= ! ( model.indexOf( 'openai/' ) >= 0 && action == 'tts' );
					}

					// Trigger the resize event if any field visibility is changed
					need_resize |= ( visible && $self.hasClass( 'trx_addons_hidden' ) ) || ( ! visible && ! $self.hasClass( 'trx_addons_hidden' ) );

					$self.toggleClass( 'trx_addons_hidden', ! visible );
				} );

				if ( $settings.find( '.sc_agenerator_form_settings_field:not(.trx_addons_hidden)' ).length == 0 ) {
					$settings_button.attr( 'disabled', 'disabled' );
				} else {
					$settings_button.removeAttr( 'disabled' );
				}

				if ( need_resize ) {
					$document.trigger( 'action.resize_trx_addons' );
				}
			}

			check_fields_visibility();

			// Send request via AJAX to generate audio
			//-----------------------------------------
			$button.on( 'click', function(e) {
				e.preventDefault();

				// if ( TRX_ADDONS_STORAGE['pagebuilder_preview_mode'] ) {
				// 	alert( TRX_ADDONS_STORAGE['msg_ai_helper_agenerator_disabled'] );
				// 	return false;
				// }

				var action_type = $actions.find( '.sc_agenerator_form_actions_item_active a' ).data( 'action' ),
					model = $model.val(),
					prompt = '',
					settings = $form.data( 'agenerator-settings' ),
					form_type = 'data',
					error_msg = '';

				if ( ! check_limits() ) {
					return false;
				}

				$form.addClass( 'sc_agenerator_form_loading' );

				// Send request via AJAX
				var data = {
					nonce: TRX_ADDONS_STORAGE['ajax_nonce'],
					action: 'trx_addons_ai_helper_agenerator',
					action_type: action_type,
					settings: settings,
					model: model,
					count: ( trx_addons_get_cookie( 'trx_addons_ai_helper_agenerator_count' ) || 0 ) * 1 + 1
				};
				// Add field 'prompt'
				if ( action_type == 'tts' ) {
					prompt = data.prompt = $prompt.val() || '';
				}
				// Add field 'upload_audio'
				if ( $upload_audio.length && $upload_audio.val() && ( action_type != 'tts' || data.model.indexOf( 'modelslab/' ) >= 0 ) ) {
					data.upload_audio = { file: $upload_audio.get(0).files[0], name: $upload_audio.get(0).files[0].name };
					form_type = 'file';
				}
				// Add field 'voice'
				if ( action_type == 'tts' || ( action_type == 'voice-cover' && data.model.indexOf( 'modelslab/' ) >= 0 ) ) {
					data.voice = data.model.indexOf( 'openai/' ) >= 0
									? $form.find('select[name="sc_agenerator_form_field_voice_openai"]').val() || ''
									: ( action_type == 'tts'
										? $form.find('input[name="sc_agenerator_form_field_voice_modelslab"]').val() || ''
										: $form.find('input[name="sc_agenerator_form_field_voice_cloning_modelslab"]').val() || ''
										);
				}
				// Add field 'upload_voice'
				if ( ( action_type == 'tts' || action_type == 'voice-cover' ) && data.model.indexOf( 'modelslab/' ) >= 0 ) {
					if ( $upload_voice_modelslab.length && $upload_voice_modelslab.val() ) {
						data.upload_voice = { file: $upload_voice_modelslab.get(0).files[0], name: $upload_voice_modelslab.get(0).files[0].name };
						form_type = 'file';
					}
				}
				// Add field 'language'
				if ( ['tts', 'transcription', 'voice-cover'].indexOf( action_type) >= 0 && data.model.indexOf( 'modelslab/' ) >= 0 ) {
					data.language = $form.find('select[name="sc_agenerator_form_field_language"]').val() || 'english';
				}
				// Add field 'emotion'
				if ( ['tts', 'voice-cover'].indexOf( action_type) >= 0 && data.model.indexOf( 'modelslab/' ) >= 0 ) {
					data.emotion = $form.find('select[name="sc_agenerator_form_field_emotion"]').val() || 'neutral';
				}
				// Add settings field 'speed'
				if ( action_type == 'tts' && data.model.indexOf( 'openai/' ) >= 0 ) {
					data.speed = $form.find('input[name="sc_agenerator_form_settings_field_speed"]').val() || 1;
				}
				// Add settings field 'temperature'
				if ( ['translation', 'transcription'].indexOf( action_type ) >= 0 && data.model.indexOf( 'openai/' ) >= 0 ) {
					data.temperature = $form.find('input[name="sc_agenerator_form_settings_field_temperature"]').val() || 0;
				}
				// Add settings fields rate, radius, originality
				if ( action_type == 'voice-cover' && data.model.indexOf( 'modelslab/' ) >= 0 ) {
					data.rate = $form.find('input[name="sc_agenerator_form_settings_field_rate"]').val() || 0.5;
					data.radius = $form.find('input[name="sc_agenerator_form_settings_field_radius"]').val() || 3;
					data.originality = $form.find('input[name="sc_agenerator_form_settings_field_originality"]').val() || 0.33;
				}
				// Send data via formData object
				if ( form_type == 'file' ) {
					var formData = new FormData();
					for ( var key in data ) {
						if ( typeof data[key] == 'object' && data[key].file ) {
							formData.append( key, data[key].file, data[key].name );
						} else {
							formData.append( key, data[key] );
						}
					}
					jQuery.ajax( {
						url: TRX_ADDONS_STORAGE['ajax_url'],
						type: "POST",
						data: formData,
						processData: false,		// Don't process fields to the string
						contentType: false,		// Prevent content type header
						success: ['tts','voice-cover'].indexOf( action_type ) >= 0 ? getAudio : getResponse
					} );
				// Else send data via method post()
				} else {
					jQuery.post( TRX_ADDONS_STORAGE['ajax_url'], data, ['tts','voice-cover'].indexOf( action_type ) >= 0 ? getAudio : getResponse );
				}

				// Callback to get audio from server
				function getAudio( response ) {
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

					$form.removeClass( 'sc_agenerator_form_loading' );

					// Show audio
					if ( ! rez.error && rez.data ) {
						var i = 0;
						// If need to fetch audio after timeout
						if ( rez.data.fetch_id ) {
							for ( i = 0; i < rez.data.fetch_number; i++ ) {
								rez.data.audio.push( {
									url: rez.data.fetch_audio
								} );
							}
							var time = rez.data.fetch_time ? rez.data.fetch_time : 8000;
							setTimeout( function() {
								fetch_data( rez.data );
							}, time );
						}
						if ( rez.data.audio.length > 0 ) {
							if ( ! rez.data.demo ) {
								update_limits_counter( rez.data.audio.length );
								update_requests_counter();
							}
							var $audio = $preview.find( '.sc_agenerator_audio_item' );
							if ( animation_in || animation_out ) {
								$preview.css( {
									'height': $audio.length ? $preview.height() + 'px' : '12em',
								} );
							}
							if ( ! $audio.length ) {
								$preview.show();
							} else if ( animation_out ) {
								$audio.removeClass( animation_in ).addClass( animation_out );
							}
							setTimeout( function() {
								var currentDate = new Date();
								var timestamp = currentDate.getTime();
								var html = '<div class="sc_agenerator_audio_item_wrap">';
								for ( var i = 0; i < rez.data.audio.length; i++ ) {
									html += '<div class="sc_agenerator_audio_item'
												+ ( rez.data.fetch_id ? ' sc_agenerator_audio_fetch' : '' )
												+ ( animation_in ? ' ' + animation_in : '' )
											+ '">'
												+ '<div class="sc_agenerator_audio_inner">'
													+ '<span class="sc_agenerator_audio_wrap">'
													+ '<audio controls="controls" preload="metadata"'	// autoplay="autoplay" loop="loop"
														+ ( rez.data.fetch_id
															? ' id="fetch-' + rez.data.fetch_id + '"'
															: ' src="' + rez.data.audio[i].url + '"'
															)
													+ '></audio>'
													+ ( rez.data.fetch_id
														? '<span class="sc_agenerator_audio_fetch_info">'
																+ '<span class="sc_agenerator_audio_fetch_progress">'
																	+ '<span class="sc_agenerator_audio_fetch_progressbar"></span>'
																+ '</span>'
																+ '<span class="sc_agenerator_audio_fetch_msg">' + rez.data.fetch_msg + '</span>'
															+ '</span>'
														: ''
														)
													+ '</span>'
													+ ( ! rez.data.demo && rez.data.show_download
														? '<a href="' + get_download_link( rez.data.audio[i].url ? rez.data.audio[i].url : '#' ) + '"'
															+ ' download="' + prompt.replace( /[\s]+/g, '-' ).toLowerCase() + '"'
															+ ' data-expired="' + ( ( rez.data.fetch_id ? 0 : timestamp ) + rez.data.show_download * 1000 ) + '"'
															//+ ' target="_blank"'
															+ ' class="sc_agenerator_audio_link sc_button sc_button_default sc_button_size_small sc_button_with_icon sc_button_icon_left"'
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
									.find('.sc_agenerator_audio_inner audio')
									.on( 'load', function() {
										trx_addons_ai_helper_agenerator_load_audio(jQuery(this));
									} )
									.on( 'error', function() {
										trx_addons_ai_helper_agenerator_reload_audio(jQuery(this));
									} );

								$preview.css( 'height', 'auto' );
								$sc.addClass( 'sc_agenerator_audio_show' );
								// Trigger the init event to allow 3rd party script to initialize the audio player (for example, MediaElement)
								if ( ! rez.data.fetch_id ) {
									$document.trigger( 'action.init_hidden_elements', [ $preview ] );
								}
								if ( need_resize ) {
									setTimeout( function() {
										$document.trigger( 'action.resize_trx_addons' );
									}, resize_delay );
								}

								// Check if download links are expired
								$preview.find( '.sc_agenerator_audio_link' ).on( 'click', function( e ) {
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
							}, $audio.length && animation_out ? 700 : 0 );
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

				// Callback to get response from server on actions 'trascription' and 'translation'
				function getResponse( response ) {
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

					$form.removeClass( 'sc_agenerator_form_loading' );

					// Show response
					if ( ! rez.error && rez.data ) {
						if ( rez.data.text ) {
							var $text = $preview.find( '.sc_agenerator_text_wrap' );
							$preview.show();
							if ( animation_in || animation_out ) {
								$preview.css( {
									'height': $preview.height() + 'px'
								} );
							}
							if ( animation_out ) {
								$text.removeClass( animation_in ).addClass( animation_out );
							}
							setTimeout( function() {
								$preview.html( '<div class="sc_agenerator_text_wrap">'
													+ '<textarea class="sc_agenerator_text">'
														+ rez.data.text
													+ '</textarea>'
												+ '</div>'
								);
								$preview.css( 'height', 'auto' );
								$sc.addClass( 'sc_agenerator_audio_show' );
								if ( need_resize ) {
									setTimeout( function() {
										$document.trigger( 'action.resize_trx_addons' );
									}, resize_delay );
								}

							}, animation_out ? 700 : 0 );
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
					action: 'trx_addons_ai_helper_fetch_audio',
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
						if ( rez.data && rez.data.audio && rez.data.audio.length > 0 ) {
							var audio = rez.data.audio,
								$fetch = $preview.find( '#fetch-' + data.fetch_id );
							if ( $fetch.length ) {
								// Replace fetch placeholders with real audio
								var $download_link;
								var currentDate = new Date();
								var timestamp = currentDate.getTime();
								var $audio;
								for ( var i = 0; i < audio.length; i++ ) {
									if ( $fetch.eq( i ).is( 'audio' ) ) {
										$audio = $fetch.eq( i );
									} else {
										$audio = $fetch.eq( i ).find( 'audio' );
									}
									$audio
										.attr( 'src', audio[i].url )
										.parents( '.sc_agenerator_audio_fetch' )
											.removeClass( 'sc_agenerator_audio_fetch' )
											.find( '.sc_agenerator_audio_fetch_info')
												.remove();
									$download_link = $fetch.eq( i ).parents( '.sc_agenerator_audio_item' ).find( '.sc_agenerator_audio_link' );
									$download_link.attr( 'href', get_download_link( audio[i].url ) );
									$download_link.attr( 'data-expired', parseInt( $download_link.attr( 'data-expired' ), 10 ) + timestamp );
									// Trigger the init event to allow 3rd party script to initialize the audio player (for example, MediaElement)
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
					.find( '.sc_agenerator_message_inner' )
						.html( msg )
						.parent()
							.toggleClass( 'sc_agenerator_message_type_error', type == 'error' )
							.toggleClass( 'sc_agenerator_message_type_info', type == 'info' )
							.toggleClass( 'sc_agenerator_message_type_success', type == 'success' )
							.addClass( 'sc_agenerator_message_show' )
							.slideDown( function() {
								if ( need_resize ) {
									$document.trigger( 'action.resize_trx_addons' );
								}
							} );
			}

			// Check limits for generation audio
			function check_limits() {
				// Block the button if the limits are exceeded only if the demo audio is not selected in the shortcode params
				if ( ! $form.data( 'agenerator-demo-audio' ) ) {
					var total, used, number;
					// Check limits for the audio generation
					var $limit_total = $form.find( '.sc_agenerator_limits_total_value' ),
						$limit_used  = $form.find( '.sc_agenerator_limits_used_value' );
					if ( $limit_total.length && $limit_used.length ) {
						total = parseInt( $limit_total.text(), 10 );
						used  = parseInt( $limit_used.text(), 10 );
						number = parseInt( $form.data( 'agenerator-number' ), 10 );
						if ( ! isNaN( total ) && ! isNaN( used ) && ! isNaN( number ) ) {
							if ( used >= total ) {
								disable_fields();
								return false;
							}
						}
					}
					// Check limits for the generation requests
					var $requests_total = $form.find( '.sc_agenerator_limits_total_requests' ),
						$requests_used  = $form.find( '.sc_agenerator_limits_used_requests' );
					if ( $requests_total.length && $requests_used.length ) {
						total = parseInt( $requests_total.text(), 10 );
						//used  = parseInt( $requests_used.text(), 10 );
						used = ( trx_addons_get_cookie( 'trx_addons_ai_helper_agenerator_count' ) || 0 ) * 1;
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
				$button.toggleClass( 'sc_agenerator_form_field_disabled', true );
				$prompt.attr( 'disabled', 'disabled' );
				$upload_audio.attr( 'disabled', 'disabled' );
				$model.attr( 'disabled', 'disabled' );
				$voice_openai.attr( 'disabled', 'disabled' );
				$voice_modelslab.attr( 'disabled', 'disabled' );
				$language.attr( 'disabled', 'disabled' );
				$emotion.attr( 'disabled', 'disabled' );
				show_message( $form.data( 'agenerator-limit-exceed' ), 'error' );
			}

			// Update a counter of generated audio inside a limits text
			function update_limits_counter( number ) {
				var total, used;
				// Update a counter of the generated audio
				var $limit_total = $form.find( '.sc_agenerator_limits_total_value' ),
					$limit_used  = $form.find( '.sc_agenerator_limits_used_value' );
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
				var $requests_total = $form.find( '.sc_agenerator_limits_total_requests' ),
					$requests_used  = $form.find( '.sc_agenerator_limits_used_requests' );
				if ( $requests_total.length && $requests_used.length ) {
					total = parseInt( $requests_total.text(), 10 );
					// used  = parseInt( $requests_used.text(), 10 );
					used = ( trx_addons_get_cookie( 'trx_addons_ai_helper_agenerator_count' ) || 0 ) * 1;
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
				var count = trx_addons_get_cookie( 'trx_addons_ai_helper_agenerator_count' ) || 0,
					limit = 60 * 60 * 1000 * 1,	// 1 hour
					expired = limit - ( new Date().getTime() % limit );

				trx_addons_set_cookie( 'trx_addons_ai_helper_agenerator_count', ++count, expired );
			}

			// Return an URL to download the audio
			function get_download_link( url ) {
				return trx_addons_add_to_url( TRX_ADDONS_STORAGE['site_url'], {
					'action': 'trx_addons_ai_helper_agenerator_download',
					'audio': trx_addons_get_file_name( url )
				} );
			}

		} );

	} );

} );