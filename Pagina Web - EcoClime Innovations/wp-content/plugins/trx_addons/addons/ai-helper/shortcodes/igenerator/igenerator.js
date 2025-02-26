/**
 * Shortcode IGenerator - Generate images with AI
 *
 * @package ThemeREX Addons
 * @since v2.20.2
 */

/* global jQuery, TRX_ADDONS_STORAGE */


jQuery( document ).ready( function() {

	"use strict";

	var $window   = jQuery( window ),
		$document = jQuery( document ),
		$body     = jQuery( 'body' );

	// OnLoad event for images - remove 'loading' class from the wrapper 'sc_igenerator_image_inner'
	window.trx_addons_ai_helper_igenerator_load_image = function( $tag ) {
		$tag.parents( '.sc_igenerator_image_inner' ).removeClass( 'sc_igenerator_image_loading' );
	};

	// OnError event for images - reload image if error occurs after 5 seconds
	window.trx_addons_ai_helper_igenerator_reload_image = function( $tag ) {
		if ( ( $tag.data( 'total-reloads' ) || 0 ) < 20 && $tag.attr( 'src' ) !== '' && trx_addons_is_external_url( $tag.attr( 'src' ) ) ) {
			$tag
				.data( 'total-reloads', ( $tag.data( 'total-reloads' ) || 0 ) + 1 )
				.parents( '.sc_igenerator_image_inner' ).addClass( 'sc_igenerator_image_loading' );
			var src = $tag.attr( 'src' );
			setTimeout( function() {
				$tag.attr( 'src', '' );
				$tag.attr( 'src', src );
			}, 5000 );
		// } else {
		// 	trx_addons_ai_helper_igenerator_load_image( $tag );
		}
	};

	$document.on( 'action.init_hidden_elements', function(e, container) {

		if ( container === undefined ) {
			container = $body;
		}

		var animation_out = trx_addons_apply_filters( 'trx_addons_filter_sc_igenerator_animation_out', 'fadeOutDownSmall animated normal' ),
			animation_in = trx_addons_apply_filters( 'trx_addons_filter_sc_igenerator_animation_in', 'fadeInUpSmall animated normal' );

		// Init IGenerator
		container.find( '.sc_igenerator:not(.sc_igenerator_inited)' ).each( function() {

			var $sc = jQuery( this ).addClass( 'sc_igenerator_inited' ),
				$form = $sc.find( '.sc_igenerator_form' ),
				$prompt = $sc.find( '.sc_igenerator_form_field_prompt_text' ),
				$negative_prompt = $sc.find( '.sc_igenerator_form_field_negative_prompt_text' ),
				$upload_image = $sc.find( '.sc_igenerator_form_field_upload_image_field' ),
				$button = $sc.hasClass( 'sc_igenerator_default' ) ? $sc.find( '.sc_igenerator_form_field_prompt_button' ) : $sc.find( '.sc_igenerator_form_field_generate_button' ),
				$settings = $sc.find( '.sc_igenerator_form_settings' ),
				$settings_button = $sc.find( '.sc_igenerator_form_settings_button' ),
				settings_light = $sc.hasClass( 'sc_igenerator_default' ) && ! $settings.hasClass( 'sc_igenerator_form_settings_full' ),
				$model = $sc.hasClass( 'sc_igenerator_default' ) ? $settings.find( '[name="sc_igenerator_form_settings_field_model"]' ) : $sc.find( '[name="sc_igenerator_form_field_model"]'),
				$style = $sc.hasClass( 'sc_igenerator_default' ) ? $settings.find( '[name="sc_igenerator_form_settings_field_style"]' ) : $sc.find( '[name="sc_igenerator_form_field_style"]'),
				$style_openai = $sc.hasClass( 'sc_igenerator_default' ) ? $settings.find( '[name="sc_igenerator_form_settings_field_style_openai"]' ) : $sc.find( '[name="sc_igenerator_form_field_style_openai"]'),
				$size = $sc.hasClass( 'sc_igenerator_default' ) ? $settings.find( '[name="sc_igenerator_form_settings_field_size"]' ) : $sc.find( '[name="sc_igenerator_form_field_size"]'),
				$width = $sc.hasClass( 'sc_igenerator_default' ) ? $settings.find( '[name="sc_igenerator_form_settings_field_width"]' ) : $sc.find( '[name="sc_igenerator_form_field_width"]'),
				$height = $sc.hasClass( 'sc_igenerator_default' ) ? $settings.find( '[name="sc_igenerator_form_settings_field_height"]' ) : $sc.find( '[name="sc_igenerator_form_field_height"]'),
				$preview = $sc.find( '.sc_igenerator_images' ),
				$actions = $sc.find( '.sc_igenerator_form_actions' ),
				$actions_slider = $sc.find( '.sc_igenerator_form_actions_slider:not(.sc_igenerator_form_actions_slider_inited)' ).addClass('sc_igenerator_form_actions_slider_inited'),
				$upscaler = $sc.find( '[name="sc_igenerator_form_field_upscaler"]'),
				fetch_img = '';

			var need_resize = trx_addons_apply_filters( 'sc_igenerator_filter_need_resize', $sc.parents( '.sc_switcher' ).length > 0 ),
				resize_delay = trx_addons_apply_filters( 'sc_igenerator_filter_resize_delay', animation_in || animation_out ? 400 : 0 );
	
			// Show/hide settings popup
			$settings_button.on( 'click', function(e) {
				e.preventDefault();
				$settings.toggleClass( 'sc_igenerator_form_settings_show' );
				return false;
			} );
			// Hide popup on click outside
			$document.on( 'click', function(e) {
				if ( $settings.hasClass( 'sc_igenerator_form_settings_show' ) && ! jQuery( e.target ).closest( '.sc_igenerator_form_settings' ).length ) {
					$settings.removeClass( 'sc_igenerator_form_settings_show' );
				}
			} );
			// Hide popup on a model selected by click (not by arrow keys) if settings are in the light mode (single field with model selector only)
			if ( settings_light && $sc.hasClass( 'sc_igenerator_default' ) ) {
				$model.on( 'click', function(e) {
					setTimeout( function() {
						$settings.removeClass( 'sc_igenerator_form_settings_show' );
					}, 200 );
				} );
			}

			$model.on( 'change', function() {
				check_fields_visibility();
			} );

			$prompt.on( 'change keyup', function() {
				check_fields_visibility();
			} );

			$upload_image.on( 'change', function() {
				check_fields_visibility();
			} );

			$upscaler.on( 'change', function() {
				check_fields_visibility();
			} );

			$size.on( 'change', function( e, from_check ) {
				if ( ! from_check ) {
					check_fields_visibility();
				}
			} );

			// Inc/Dec the 'width' and 'height' fields on click on the arrows
			if ( ! settings_light ) {
				$sc
					.find( '.sc_igenerator_form_settings_field_numeric_wrap_button_inc,.sc_igenerator_form_settings_field_numeric_wrap_button_dec,.sc_igenerator_form_field_numeric_wrap_button_inc,.sc_igenerator_form_field_numeric_wrap_button_dec' )
						.on( 'click', function(e) {
							e.preventDefault();
							var $self = jQuery( this ),
								$field = $self.parents( '.sc_igenerator_form_settings_field_numeric_wrap,.sc_igenerator_form_field_numeric_wrap' ).eq(0),
								$input = $field.find( 'input' ),
								val = Number( $input.val() || 0 ),
								step = Number( $input.attr( 'step' ) || 1 ),
								min = Number( $input.attr( 'min' ) || 0 ),
								max = Number( $input.attr( 'max' ) || 1024 );
							if ( $self.hasClass( 'sc_igenerator_form_settings_field_numeric_wrap_button_inc' ) || $self.hasClass( 'sc_igenerator_form_field_numeric_wrap_button_inc' ) ) {
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
			}

			// Change the prompt text on click on the tag
			$sc.on( 'click', '.sc_igenerator_form_field_tags_item,.sc_igenerator_message_translation', function(e) {
				e.preventDefault();
				var $self = jQuery( this ),
					$prompt_field = $self.data( 'tag-type' ) == 'negative_prompt' ? $negative_prompt : $prompt;
				if ( ! $prompt_field.attr( 'disabled' ) ) {
					$prompt_field.val( $self.data( 'tag-prompt' ) ).trigger( 'change' ).get(0).focus();
				}
				return false;
			} );

			// Display file name in the decorated upload field text on change the file
			$upload_image.on( 'change', function(e) {
				var $self = jQuery( this ),
					file = $self.val().replace( /\\/g, '/' ).replace( /.*\//, '' );
				$self.parent()
					.toggleClass( 'filled', true )
					.find( '.sc_igenerator_form_field_upload_image_text' )
						.removeClass( 'theme_form_field_placeholder' )
						.text( file );
			} );

			// Close a message popup on click on the close button
			$sc.on( 'click', '.sc_igenerator_message_close', function(e) {
				e.preventDefault();
				$form.find( '.sc_igenerator_message' ).slideUp();
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

			// Layout-specific actions: 'Default'
			if ( $sc.hasClass( 'sc_igenerator_default' ) ) {
				// Set padding for the prompt field to avoid overlapping the button
				if ( $button.css( 'position' ) == 'absolute' ) {
					var set_prompt_padding = ( function() {
						$prompt.css( 'padding-right', ( Math.ceil( $button.outerWidth() ) + 10 ) + 'px' );
					} )();
					$window.on( 'resize', set_prompt_padding );
				}
			}

			// Layout-specific actions: 'Extended'
			if ( $sc.hasClass( 'sc_igenerator_extended' ) ) {

				// Switch actions on click on the action button
				$actions.on( 'click', 'a[data-action]', function(e) {
					e.preventDefault();
					var $self = jQuery( this ),
						$item = $self.parent();
					if ( ! $item.hasClass( 'sc_igenerator_form_actions_item_active' ) ) {
						$item.siblings( '.sc_igenerator_form_actions_item_active' ).removeClass( 'sc_igenerator_form_actions_item_active' );
						$item.addClass( 'sc_igenerator_form_actions_item_active' );
						trx_addons_ai_helper_igenerator_move_slider_to_active_item();
						check_fields_visibility();
					}
					return false;
				} );

				// Move slider to active item
				window.trx_addons_ai_helper_igenerator_move_slider_to_active_item = function() {
					var $active = $actions.find( '.sc_igenerator_form_actions_item_active a' );
					if ( $active.length ) {
						$actions_slider.css( {
							left: $active.offset().left - $actions.offset().left,
							width: $active.outerWidth()
						} );
					}
				};
				trx_addons_ai_helper_igenerator_move_slider_to_active_item();
	
				// Move slider to active item on resize
				$document.on( 'action.resize_trx_addons', trx_addons_debounce( trx_addons_ai_helper_igenerator_move_slider_to_active_item, 200 ) );
			}

			function check_fields_visibility() {

				var action = $sc.hasClass( 'sc_igenerator_extended' ) ? $actions.find( '.sc_igenerator_form_actions_item_active a' ).data( 'action' ) : 'generation';
				var model = ( $model.is('input[type="radio"]') ? $model.filter( ':checked' ).val() : $model.val() ) || '';

				// Enable/disable the button on change the prompt text
				var disabled = false;
				if ( action == 'generation' ) {
					disabled = $prompt.attr( 'disabled' ) == 'disabled' || $prompt.val() == '';
				} else if ( action == 'variations' || action == 'upscale' ) {
					disabled = $upload_image.attr( 'disabled' ) == 'disabled' || $upload_image.val() == '';
				}
				$button.toggleClass( 'sc_igenerator_form_field_prompt_button_disabled sc_igenerator_form_field_disabled', disabled );

				// Show/hide fields
				$form.find( '.sc_igenerator_form_field,.sc_igenerator_form_settings_field' ).each( function() {
					var $self = jQuery( this ),
						visible = ! $sc.hasClass( 'sc_igenerator_extended' ) || ( '' + $self.data( 'actions' ) ).indexOf( action ) >= 0;

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

					// If the field is 'model' in the layout 'extended' - disable the button 'settings' for the 'openai' model
					if ( ! settings_light && $sc.hasClass( 'sc_igenerator_extended' ) && $self.attr( 'class' ).indexOf( 'field_model' ) > 0 ) {
						$self.find( '.sc_igenerator_form_settings_button' ).toggleClass( 'trx_addons_hidden', model.indexOf( 'openai/' ) >= 0 );
					}

					// If the field is 'style' - show it only for the 'stability-ai' model
					if ( ! settings_light && $self.attr( 'class' ).indexOf( 'field_style' ) > 0 && $self.attr( 'class' ).indexOf( 'field_style_' ) < 0 ) {
						visible &&= model.indexOf( 'stability-ai/' ) >= 0;
					}

					// If the field is 'style_openai' - show it only for the 'openai' model 'DALL-E-3'
					if ( ! settings_light && $self.attr( 'class' ).indexOf( 'field_style_openai' ) > 0 ) {
						visible &&= model.indexOf( 'openai/dall-e-3' ) >= 0;
					}

					// If the field is 'lora_model' - show it only for the 'stable-diffusion' models
					if ( ! settings_light && $self.attr( 'class' ).indexOf( 'field_lora_model' ) > 0 ) {
						visible &&= model.indexOf( 'stable-diffusion/' ) >= 0 && model != 'stable-diffusion/default';
					}

					// If the field is 'size'
					if ( ! settings_light && $self.attr( 'class' ).indexOf( 'field_size' ) > 0 ) {
						// Hide unavailable sizes for the selected model
						$size.find( 'option' ).each( function() {
							var $option = jQuery( this ),
								val = $option.val(),
								text = $option.text();
							$option.toggleClass( 'trx_addons_hidden',
								( model.indexOf( 'openai/' ) >= 0 && ( ! TRX_ADDONS_STORAGE['ai_helper_sc_igenerator_openai_sizes'] || ! TRX_ADDONS_STORAGE['ai_helper_sc_igenerator_openai_sizes'][ val ] ) )
								|| ( model.indexOf( 'stable-diffusion/' ) >= 0 && text.indexOf( 'only' ) > 0 && text.indexOf( 'SD only' ) < 0 )
								|| ( model.indexOf( 'stability-ai/' ) >= 0 && text.indexOf( 'only' ) > 0 && text.indexOf( 'Stability AI only' ) < 0 )
							);
							if ( $option.is( ':selected' ) && $option.hasClass( 'trx_addons_hidden' ) ) {
								$size.val( '256x256' ).trigger( 'change', true );
							}
						} );
						var upscaler_model = $upscaler.length ? ( $upscaler.val() || '' ) : '';
						// Hide subfields 'width' and 'height' if the size is not 'custom'
						$self.find( '.sc_igenerator_form_field_dimensions_wrap' ).toggleClass( 'trx_addons_hidden', action == 'upscale' ? upscaler_model.indexOf( 'stability-ai/' ) < 0 : $size.val() != 'custom' );
						// Show/hide the 'size' field
						$self.find( '.sc_igenerator_form_field_size_wrap' ).toggleClass( 'trx_addons_hidden', action == 'upscale' );
						// Show/hide the 'scale' field
						$self.find( '.sc_igenerator_form_field_scale_wrap' ).toggleClass( 'trx_addons_hidden', action != 'upscale' || upscaler_model.indexOf( 'stable-diffusion/' ) < 0 );
					}

					// If the field is 'width' or 'height' in the layout 'default' (popup with settings) - show it only for the 'custom' size
					if ( ! settings_light && $sc.hasClass( 'sc_igenerator_default' ) && ( $self.attr( 'class' ).indexOf( 'field_width' ) > 0 || $self.attr( 'class' ).indexOf( 'field_height' ) > 0 ) ) {
						visible &&= $size.val() == 'custom';
					}

					// If the field is 'negative_prompt' - show it for the not 'openai' models
					if ( $self.attr( 'class' ).indexOf( 'field_negative_prompt' ) > 0 ) {
						visible &&= model.indexOf( 'openai/' ) < 0;
					}

					$self.toggleClass( 'trx_addons_hidden', ! visible );
				} );

				if ( need_resize ) {
					$document.trigger( 'action.resize_trx_addons' );
				}
			}

			check_fields_visibility();

			// Send request via AJAX to generate images
			//-----------------------------------------
			$button.on( 'click', function(e) {
				e.preventDefault();

				// if ( TRX_ADDONS_STORAGE['pagebuilder_preview_mode'] ) {
				// 	alert( TRX_ADDONS_STORAGE['msg_ai_helper_igenerator_disabled'] );
				// 	return false;
				// }

				var action_type = $sc.hasClass( 'sc_igenerator_default' )
									? ( $upload_image.length && $upload_image.val() ? 'variations' : 'generation' )
									: $actions.find( '.sc_igenerator_form_actions_item_active a' ).data( 'action' ),
					prompt = $prompt.val(),
					negative_prompt = $negative_prompt.length ? $negative_prompt.val() : '',
					model = action_type == 'upscale'
								? $upscaler.val()
								: ( ( settings_light ? $model.filter(':checked').val() : $model.val() ) || $form.data( 'igenerator-default-model' ) ),
					settings = $form.data( 'igenerator-settings' );

				if ( ( action_type != 'upscale' && ! prompt ) || ! check_limits() ) {
					return false;
				}

				$form.addClass( 'sc_igenerator_form_loading' );

				// Send request via AJAX
				var data = {
					nonce: TRX_ADDONS_STORAGE['ajax_nonce'],
					action: 'trx_addons_ai_helper_igenerator',
					action_type: action_type,
					settings: settings,
					prompt: prompt,
					negative_prompt: model.indexOf( 'openai/' ) >= 0 ? '' : negative_prompt,
					model: model,
					count: ( trx_addons_get_cookie( 'trx_addons_ai_helper_igenerator_count' ) || 0 ) * 1 + 1
				};
				if ( ! settings_light ) {
					data.size = $size.val();
					if ( data.size == 'custom' || action_type == 'upscale' ) {
						data.width = $width.val();
						data.height = $height.val();
					}
					data.style = model.indexOf( 'stability-ai/' ) >= 0
									? $style.val()
									: ( model.indexOf( 'openai/dall-e-3' ) >= 0
										? $style_openai.val()
										: ''
										);
					if ( model.indexOf( 'stable-diffusion/' ) >= 0 && model != 'stable-diffusion/default' ) {
						data.lora_model = $sc.find( '[name="sc_igenerator_form_field_lora_model"]' ).val();
					}
				}
				if ( $sc.hasClass( 'sc_igenerator_extended' ) ) {
					if ( action_type == 'upscale' ) {
						data.scale = $form.find('input[name="sc_igenerator_form_field_scale"]').val() || 2;
					}
					if ( data.model.indexOf( 'stable-diffusion/' ) >= 0 ) {
						data.guidance_scale = $sc.find( '[name="sc_igenerator_form_settings_field_guidance_scale"]' ).val();
						data.inference_steps = $sc.find( '[name="sc_igenerator_form_settings_field_inference_steps"]' ).val();
						data.seed = $sc.find( '[name="sc_igenerator_form_settings_field_seed"]' ).val();
					} else if ( data.model.indexOf( 'stability-ai/' ) >= 0 ) {
						data.cfg_scale = $sc.find( '[name="sc_igenerator_form_settings_field_cfg_scale"]' ).val();
						data.diffusion_steps = $sc.find( '[name="sc_igenerator_form_settings_field_diffusion_steps"]' ).val();
						data.seed = $sc.find( '[name="sc_igenerator_form_settings_field_seed"]' ).val();
					}
				}
				// If upload image is present - convert data to FormData object and send via method ajax()
				if ( $upload_image.length && $upload_image.val() && ['variations', 'upscale'].indexOf( data.action_type ) >= 0 ) {
					var formData = new FormData();
					for ( var key in data ) {
						formData.append( key, data[key] );
					}
					formData.append( 'upload_image', $upload_image.get(0).files[0], $upload_image.get(0).files[0].name );
					jQuery.ajax( {
						url: TRX_ADDONS_STORAGE['ajax_url'],
						type: "POST",
						data: formData,
						processData: false,		// Don't process fields to the string
						contentType: false,		// Prevent content type header
						success: getImages
					} );
				// Else send data via method post()
				} else {
					jQuery.post( TRX_ADDONS_STORAGE['ajax_url'], data, getImages );
				}

				// Callback to get images from server
				function getImages( response ) {
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

					$form.removeClass( 'sc_igenerator_form_loading' );

					// Show images
					if ( ! rez.error && rez.data ) {
						var i = 0;
						// If need to fetch images after timeout
						if ( rez.data.fetch_id ) {
							for ( i = 0; i < rez.data.fetch_number; i++ ) {
								rez.data.images.push( {
									url: rez.data.fetch_img
								} );
							}
							if ( ! fetch_img ) {
								fetch_img = rez.data.fetch_img;
							}
							var time = rez.data.fetch_time ? rez.data.fetch_time : 8000;
							setTimeout( function() {
								fetch_images( rez.data );
							}, time );
						}
						if ( rez.data.images.length > 0 ) {
							if ( ! rez.data.demo ) {
								update_limits_counter( rez.data.images.length );
								update_requests_counter();
							}
							var $images = $preview.find( '.sc_igenerator_image' );
							if ( animation_in || animation_out ) {
								$preview.css( {
									'height': $images.length ? $preview.height() + 'px' : '36vh'
								} );
							}
							if ( ! $images.length ) {
								$preview.show();
							} else if ( animation_out ) {
								$images.removeClass( animation_in ).addClass( animation_out );
							}
							setTimeout( function() {
								var currentDate = new Date();
								var timestamp = currentDate.getTime();
								var html = '<div class="sc_igenerator_columns_wrap sc_item_columns '
												+ TRX_ADDONS_STORAGE['columns_wrap_class']
												+ ' columns_padding_bottom'
												+ ( rez.data.columns >= rez.data.number ? ' ' + TRX_ADDONS_STORAGE['columns_in_single_row_class'] : '' )
												+ '">';
								for ( var i = 0; i < rez.data.images.length; i++ ) {
									html += '<div class="sc_igenerator_image ' + trx_addons_get_column_class( 1, rez.data.columns, rez.data.columns_tablet, rez.data.columns_mobile )
												+ ( rez.data.fetch_id ? ' sc_igenerator_image_fetch' : '' )
												+ ( animation_in ? ' ' + animation_in : '' )
											+ '">'
												+ '<div class="sc_igenerator_image_inner">'
													+ '<span class="sc_igenerator_image_wrap">'
														+ '<img src="' + rez.data.images[i].url + '" alt=""' + ( rez.data.fetch_id ? ' id="fetch-' + rez.data.fetch_id + '"' : '' )
															+ ' width="1000" height="1000"'	// Set the size for images to show 'loading' indicator
														+ '>'
														+ '<span class="sc_igenerator_image_wait_available">'
															+ '<span class="sc_igenerator_image_wait_icon"></span>'
															+ '<span class="sc_igenerator_image_wait_msg">' + TRX_ADDONS_STORAGE['msg_ai_helper_wait_available'] + '</span>'
														+ '</span>'
													+ '</span>'
													+ ( rez.data.fetch_id
														? '<span class="sc_igenerator_image_fetch_info">'
																+ '<span class="sc_igenerator_image_fetch_msg">' + rez.data.fetch_msg + '</span>'
																+ '<span class="sc_igenerator_image_fetch_progress">'
																	+ '<span class="sc_igenerator_image_fetch_progressbar"></span>'
																+ '</span>'
															+ '</span>'
														: ''
														)
													+ ( ! rez.data.demo && rez.data.show_download
														? '<a href="' + get_download_link( rez.data.images[i].url ) + '"'
															+ ' download="' + prompt.replace( /[\s]+/g, '-' ).toLowerCase() + '"'
															+ ' data-expired="' + ( ( rez.data.fetch_id ? 0 : timestamp ) + rez.data.show_download * 1000 ) + '"'
															+ ' data-elementor-open-lightbox="no"'
															//+ ' target="_blank"'
															+ ' class="sc_igenerator_image_link sc_button sc_button_default sc_button_size_small sc_button_with_icon sc_button_icon_left"'
															+ ' data-elementor-open-lightbox="no"'
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
									.find('.sc_igenerator_image_inner img')
									.on( 'load', function() {
										trx_addons_ai_helper_igenerator_load_image(jQuery(this));
									} )
									.on( 'error', function() {
										trx_addons_ai_helper_igenerator_reload_image(jQuery(this));
									} );
								$preview
									.find('.sc_igenerator_image_wait_available' )
									.on( 'click', function(e) {
										// Stop bubbling to avoid opening the image in the popup until the image is not available
										e.stopPropagation();
										return false;
									} );

								setTimeout( function() {
									$preview.css( 'height', 'auto' );
									$sc.addClass( 'sc_igenerator_images_show' );
									prepare_images_for_popup();
									if ( need_resize ) {
//										setTimeout( function() {
											trx_addons_when_images_loaded( $preview, function() {
												$document.trigger( 'action.resize_trx_addons' );
											} );
//										}, resize_delay );
									}
								}, animation_in ? 700 : 0 );
								// Check if download links are expired
								$preview.find( '.sc_igenerator_image_link' ).on( 'click', function( e ) {
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
							}, $images.length && animation_out ? 700 : 0 );
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

			// Fetch images
			function fetch_images(data) {
				jQuery.post( TRX_ADDONS_STORAGE['ajax_url'], {
					nonce: TRX_ADDONS_STORAGE['ajax_nonce'],
					action: 'trx_addons_ai_helper_fetch_images',
					fetch_id: data.fetch_id,
					fetch_url: data.fetch_url,
					fetch_model: data.fetch_model
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
						if ( rez.data && rez.data.images && rez.data.images.length > 0 ) {
							var images = rez.data.images,
								$fetch = $preview.find( 'img#fetch-' + data.fetch_id );
							// Fade out fetch placeholders
							if ( animation_out ) {
								for ( var i = 0; i < images.length; i++ ) {
									$fetch.eq( i ).parents( '.sc_igenerator_image_fetch' )
										.removeClass( animation_in )
										.addClass( animation_out );
								}
							}
							// Replace fetch placeholders with real images
							setTimeout( function() {
								var $download_link;
								var currentDate = new Date();
								var timestamp = currentDate.getTime();
								for ( var i = 0; i < images.length; i++ ) {
									$fetch.eq( i ).attr( 'src', images[i].url );
									$download_link = $fetch.eq( i ).parents( '.sc_igenerator_image' ).find( '.sc_igenerator_image_link' );
									$download_link.attr( 'href', get_download_link( images[i].url ) );
									$download_link.attr( 'data-expired', parseInt( $download_link.attr( 'data-expired' ), 10 ) + timestamp );
								}
								if ( need_resize ) {
									trx_addons_when_images_loaded( $preview, function() {
										$document.trigger( 'action.resize_trx_addons' );
									} );
								}
							}, animation_out ? 300 : 0 );
							// Fade in real images
							setTimeout( function() {
								for ( var i = 0; i < images.length; i++ ) {
									$fetch.eq( i )
										.parents( '.sc_igenerator_image_fetch' )
											.removeClass( 'sc_igenerator_image_fetch' )
											.find( '.sc_igenerator_image_fetch_info')
												.remove();
									if ( animation_in ) {
										trx_addons_when_images_loaded( $fetch.eq( i ).parents( '.sc_igenerator_image' ), function( $img ) {
											$img
												.removeClass( animation_out )
												.addClass( animation_in );
										} );
									}
								}
								prepare_images_for_popup();
								if ( need_resize ) {
									setTimeout( function() {
										trx_addons_when_images_loaded( $preview, function() {
											$document.trigger( 'action.resize_trx_addons' );
										} );
									}, resize_delay );
							}
							}, animation_out ? 800 : 0 );
						} else {
							setTimeout( function() {
								fetch_images( data );
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
					.find( '.sc_igenerator_message_inner' )
						.html( msg )
						.parent()
							.toggleClass( 'sc_igenerator_message_type_error', type == 'error' )
							.toggleClass( 'sc_igenerator_message_type_info', type == 'info' )
							.toggleClass( 'sc_igenerator_message_type_success', type == 'success' )
							.addClass( 'sc_igenerator_message_show' )
							.slideDown( function() {
								if ( need_resize ) {
									$document.trigger( 'action.resize_trx_addons' );
								}
							} );
			}

			// Check limits for generation images
			function check_limits() {
				// Block the button if the limits are exceeded only if the demo images are not selected in the shortcode params
				if ( ! $form.data( 'igenerator-demo-images' ) ) {
					var total, used, number;
					// Check limits for the image generation
					var $limit_total = $form.find( '.sc_igenerator_limits_total_value' ),
						$limit_used  = $form.find( '.sc_igenerator_limits_used_value' );
					if ( $limit_total.length && $limit_used.length ) {
						total = parseInt( $limit_total.text(), 10 );
						used  = parseInt( $limit_used.text(), 10 );
						number = parseInt( $form.data( 'igenerator-number' ), 10 );
						if ( ! isNaN( total ) && ! isNaN( used ) && ! isNaN( number ) ) {
							if ( used >= total ) {
								disable_fields();
								return false;
							}
						}
					}
					// Check limits for the generation requests
					var $requests_total = $form.find( '.sc_igenerator_limits_total_requests' ),
						$requests_used  = $form.find( '.sc_igenerator_limits_used_requests' );
					if ( $requests_total.length && $requests_used.length ) {
						total = parseInt( $requests_total.text(), 10 );
						//used  = parseInt( $requests_used.text(), 10 );
						used = ( trx_addons_get_cookie( 'trx_addons_ai_helper_igenerator_count' ) || 0 ) * 1;
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
				$button.toggleClass( 'sc_igenerator_form_field_prompt_button_disabled sc_igenerator_form_field_disabled', true );
				$prompt.attr( 'disabled', 'disabled' );
				$negative_prompt.attr( 'disabled', 'disabled' );
				$upload_image.attr( 'disabled', 'disabled' );
				if ( $sc.hasClass( 'sc_igenerator_extended' ) ) {
					$model.attr( 'disabled', 'disabled' );
					$size.attr( 'disabled', 'disabled' );
					$width.attr( 'disabled', 'disabled' );
					$height.attr( 'disabled', 'disabled' );
				}
				show_message( $form.data( 'igenerator-limit-exceed' ), 'error' );
			}

			// Update a counter of generated images inside a limits text
			function update_limits_counter( number ) {
				var total, used;
				// Update a counter of the generated images
				var $limit_total = $form.find( '.sc_igenerator_limits_total_value' ),
					$limit_used  = $form.find( '.sc_igenerator_limits_used_value' );
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
				var $requests_total = $form.find( '.sc_igenerator_limits_total_requests' ),
					$requests_used  = $form.find( '.sc_igenerator_limits_used_requests' );
				if ( $requests_total.length && $requests_used.length ) {
					total = parseInt( $requests_total.text(), 10 );
					// used  = parseInt( $requests_used.text(), 10 );
					used = ( trx_addons_get_cookie( 'trx_addons_ai_helper_igenerator_count' ) || 0 ) * 1;
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
				var count = trx_addons_get_cookie( 'trx_addons_ai_helper_igenerator_count' ) || 0,
					limit = 60 * 60 * 1000 * 1,	// 1 hour
					expired = limit - ( new Date().getTime() % limit );

				trx_addons_set_cookie( 'trx_addons_ai_helper_igenerator_count', ++count, expired );
			}

			// Return an URL to download the image
			function get_download_link( url ) {
				return trx_addons_add_to_url( TRX_ADDONS_STORAGE['site_url'], {
					'action': 'trx_addons_ai_helper_igenerator_download',
					'image': trx_addons_get_file_name( url )
				} );
			}

			// Wrap the image into the link to open it in the popup
			function prepare_images_for_popup() {
				var popup = $form.data( 'igenerator-popup' );
				if ( popup ) {
					var found = false;
					$preview.find( '.sc_igenerator_image:not(.sc_igenerator_image_fetch) img' ).each( function() {
						var $img = jQuery( this ),
							$wrap = $img.parent(),
							$link = $wrap.parent();
						if ( $link.is( 'a' ) ) {
							$link.attr( {
								'href': $img.attr( 'src' ),
								'data-elementor-open-lightbox': 'no'
							} );
						} else {
							$wrap.wrap( '<a class="sc_igenerator_image_wrap_link" href="' + $img.attr( 'src' ) + '" rel="' + ( TRX_ADDONS_STORAGE['popup_engine'] == 'pretty' ? 'prettyPhoto[slideshow]' : 'magnific' ) + '" data-elementor-open-lightbox="no"></a>' );
						}
						found = true;
					} );
					if ( found ) {
						$document.trigger( 'action.init_hidden_elements', [ $preview ] );
					}
				}
			}

		} );

	} );

} );