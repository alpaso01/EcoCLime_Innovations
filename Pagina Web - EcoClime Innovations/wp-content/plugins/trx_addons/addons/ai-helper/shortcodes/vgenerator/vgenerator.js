/**
 * Shortcode VGenerator - Generate videos with AI
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

    // OnLoad event for videos - remove 'loading' class from the wrapper 'sc_vgenerator_video_inner'
	window.trx_addons_ai_helper_vgenerator_loadeddata_video = function( $tag ) {
		$tag.parents( '.sc_vgenerator_video_inner' ).removeClass( 'sc_vgenerator_video_loading' );
	};

    // OnError event for videos - reload video if error occurs after 5 seconds
	window.trx_addons_ai_helper_vgenerator_reload_video = function( $tag ) {
		if ( ( $tag.data( 'total-reloads' ) || 0 ) < 20 && $tag.attr( 'src' ) !== '' && trx_addons_is_external_url( $tag.attr( 'src' ) ) ) {
			$tag
				.data( 'total-reloads', ( $tag.data( 'total-reloads' ) || 0 ) + 1 )
				.parents( '.sc_vgenerator_video_inner' ).addClass( 'sc_vgenerator_video_loading' );
			var src = $tag.attr( 'src' );
			setTimeout( function() {
				$tag.attr( 'src', '' );
				$tag.attr( 'src', src );
			}, 5000 );
		// } else {
		// 	trx_addons_ai_helper_vgenerator_loadeddata_video( $tag );
		}
	};

    $document.on( 'action.init_hidden_elements', function(e, container) {

        if ( container === undefined ) {
			container = $body;
		}

        var animation_out = trx_addons_apply_filters( 'trx_addons_filter_sc_vgenerator_animation_out', 'fadeOutDownSmall animated normal' ),
			animation_in = trx_addons_apply_filters( 'trx_addons_filter_sc_vgenerator_animation_in', 'fadeInUpSmall animated normal' );

        // Init vgenerator
		container.find( '.sc_vgenerator:not(.sc_vgenerator_inited)' ).each( function() {

            var $sc = jQuery( this ).addClass( 'sc_vgenerator_inited' ),
                $form = $sc.find( '.sc_vgenerator_form' ),
                $prompt = $sc.find( '.sc_vgenerator_form_field_prompt_text' ),
                $button = $sc.find( '.sc_vgenerator_form_field_prompt_button' ),
				$upload_keyframes = $sc.find( '.sc_vgenerator_form_field_upload_keyframe_field' ),
				$keyframes_frame0 = $upload_keyframes.filter( '.sc_vgenerator_form_field_upload_start_keyframe_field' ),
				$keyframes_frame1 = $upload_keyframes.filter( '.sc_vgenerator_form_field_upload_end_keyframe_field' ),
                $settings = $sc.find( '.sc_vgenerator_form_settings' ),
                $settings_button = $sc.find( '.sc_vgenerator_form_settings_button' ),
                $model = $settings.find( '[name="sc_vgenerator_form_settings_field_model"]' ),
                $settings_aspect_ratio = $settings.find( '[name="sc_vgenerator_form_settings_field_aspect_ratio"]' ),
                $settings_resolution = $settings.find( '[name="sc_vgenerator_form_settings_field_resolution"]' ),
                $settings_duration = $settings.find( '[name="sc_vgenerator_form_settings_field_duration"]' ),
                $preview = $sc.find( '.sc_vgenerator_videos' );

            var need_resize = trx_addons_apply_filters( 'sc_vgenerator_filter_need_resize', $sc.parents( '.sc_switcher' ).length > 0 ),
				resize_delay = trx_addons_apply_filters( 'sc_vgenerator_filter_resize_delay', animation_in || animation_out ? 400 : 0 );

            // Show/hide settings popup
			$settings_button.on( 'click', function(e) {
				e.preventDefault();
				$settings.toggleClass( 'sc_vgenerator_form_settings_show' );
				return false;
			} );
			// Hide popup on click outside
			$document.on( 'click', function(e) {
				if ( $settings.hasClass( 'sc_vgenerator_form_settings_show' ) && ! jQuery( e.target ).closest( '.sc_vgenerator_form_settings' ).length ) {
					$settings.removeClass( 'sc_vgenerator_form_settings_show' );
				}
			} );

			$model.on( 'change', function() {
				check_fields_visibility();
			} );

			$prompt.on( 'change keyup', function() {
				check_fields_visibility();
			} );

			$upload_keyframes.on( 'change keyup', function() {
				check_fields_visibility();
			} );

            // Change the prompt text on click on the tag
			$sc.on( 'click', '.sc_vgenerator_form_field_tags_item,.sc_vgenerator_message_translation', function(e) {
				e.preventDefault();
				var $self = jQuery( this );
				if ( ! $prompt.attr( 'disabled' ) ) {
					$prompt.val( $self.data( 'tag-prompt' ) ).trigger( 'change' ).get(0).focus();
				}
				return false;
			} );

			// Display file name in the decorated upload field text on change the file
			$upload_keyframes.on( 'change', function(e) {
				var $self = jQuery( this ),
					file = $self.val() ? $self.val().replace( /\\/g, '/' ).replace( /.*\//, '' ) : $self.data( 'text-placeholder' ) || '';
				$self.parent()
					.toggleClass( 'filled', true )
					.find( '.sc_vgenerator_form_field_upload_keyframe_text' )
						.removeClass( 'theme_form_field_placeholder' )
						.text( file );
			} );

            // Close a message popup on click on the close button
			$sc.on( 'click', '.sc_vgenerator_message_close', function(e) {
				e.preventDefault();
				$form.find( '.sc_vgenerator_message' ).slideUp();
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

				var model = ( $model.is('input[type="radio"]') ? $model.filter( ':checked' ).val() : $model.val() ) || '';

				// Enable/disable the button on change the prompt text
				var disabled = $prompt.attr( 'disabled' ) == 'disabled' || $prompt.val() == '';
				$button.toggleClass( 'sc_vgenerator_form_field_prompt_button_disabled', disabled );

				$form.find( '.sc_vgenerator_form_field,.sc_vgenerator_form_settings_field' ).each( function () {
					var $self = jQuery( this );

					if ( $self.hasClass( 'sc_vgenerator_form_settings_field_resolution' ) || $self.hasClass( 'sc_vgenerator_form_settings_field_duration' ) ) {
						$self.toggleClass( 'trx_addons_hidden', model == 'lumalabs-ai/ray-1-6' );
					}

					if ( $self.hasClass( 'sc_vgenerator_form_field_upload_start_keyframe' ) || $self.hasClass( 'sc_vgenerator_form_field_upload_end_keyframe' ) || $self.hasClass( 'sc_vgenerator_form_field_upload_keyframe_wrap' ) ) {
						$self.toggleClass( 'trx_addons_hidden', model == 'lumalabs-ai/ray-2' );
					}
				} );

				if ( need_resize ) {
					$document.trigger( 'action.resize_trx_addons' );
				}
			}

			check_fields_visibility();

            // Send request via AJAX to generate video
			//-----------------------------------------
			$button.on( 'click', function(e) {
				e.preventDefault();

                // if ( TRX_ADDONS_STORAGE['pagebuilder_preview_mode'] ) {
				// 	alert( TRX_ADDONS_STORAGE['msg_ai_helper_vgenerator_disabled'] );
				// 	return false;
				// }

                var action_type = 'generation',
					prompt = $prompt.val(),
					settings = $form.data( 'vgenerator-settings' ),
					default_model = $form.data( 'vgenerator-default-model' );

                if ( ! prompt || ! check_limits() ) {
                    return false;
                }

                $form.addClass( 'sc_vgenerator_form_loading' );

                // Send request via AJAX
				var data = {
					nonce: TRX_ADDONS_STORAGE['ajax_nonce'],
					action: 'trx_addons_ai_helper_vgenerator',
					action_type: action_type,
					settings: settings,
					prompt: prompt,
					model: ( $model.length && $model.val() ) ? $model.val() : default_model,
					count: ( trx_addons_get_cookie( 'trx_addons_ai_helper_vgenerator_count' ) || 0 ) * 1 + 1
				};

				if ( $settings_aspect_ratio.length && $settings_aspect_ratio.val() ) {
					data.aspect_ratio = $settings_aspect_ratio.val();
				}

				if ( $settings_resolution.length && $settings_resolution.val() ) {
					data.resolution = $settings_resolution.val();
				}

				if ( $settings_duration.length && $settings_duration.val() ) {
					data.duration = $settings_duration.val();
				}

				// If upload image is present - convert data to FormData object and send via method ajax()
				if ( $upload_keyframes.length && ( $keyframes_frame0.val() || $keyframes_frame1.val() ) ) {
					var formData = new FormData();
					for ( var key in data ) {
						formData.append( key, data[key] );
					}

					if ( $keyframes_frame0.length && $keyframes_frame0.val() ) {
						formData.append( 'keyframes_frame0', $keyframes_frame0.get(0).files[0], $keyframes_frame0.get(0).files[0].name );
					}

					if ( $keyframes_frame1.length && $keyframes_frame1.val() ) {
						formData.append( 'keyframes_frame1', $keyframes_frame1.get(0).files[0], $keyframes_frame1.get(0).files[0].name );
					}
					jQuery.ajax( {
						url: TRX_ADDONS_STORAGE['ajax_url'],
						type: "POST",
						data: formData,
						processData: false,		// Don't process fields to the string
						contentType: false,		// Prevent content type header
						success: getVideos
					} );
				// Else send data via method post()
				} else {
					jQuery.post( TRX_ADDONS_STORAGE['ajax_url'], data, getVideos );
				}

                // Callback to get video from server
				function getVideos( response ) {
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

                    $form.removeClass( 'sc_vgenerator_form_loading' );

                    // Show videos
					if ( ! rez.error && rez.data ) {
						var i = 0;
						// If need to fetch videos after timeout
						if ( rez.data.fetch_id ) {
							for ( i = 0; i < rez.data.fetch_number; i++ ) {
								rez.data.video.push( {
									url_preview: rez.data.fetch_img,
								} );
							}
							var time = rez.data.fetch_time ? rez.data.fetch_time : 8000;
							setTimeout( function() {
								fetch_data( rez.data );
							}, time );
						}
                        if ( rez.data.video.length > 0 ) {
							if ( ! rez.data.demo ) {
								update_limits_counter( rez.data.video.length );
								update_requests_counter();
							}
							var $videos = $preview.find( '.sc_vgenerator_video' );
							if ( animation_in || animation_out ) {
								$preview.css( {
									'height': $videos.length ? $preview.height() + 'px' : ( $preview.width() * 9 / 16 ) + 'px'
								} );
							}
							if ( ! $videos.length ) {
								$preview.show();
							} else if ( animation_out ) {
								$videos.removeClass( animation_in ).addClass( animation_out );
							}
							setTimeout( function() {
								var currentDate = new Date();
								var timestamp = currentDate.getTime();
								var html = '<div class="sc_vgenerator_columns_wrap">';
								for ( var i = 0; i < rez.data.video.length; i++ ) {
									html += '<div class="sc_vgenerator_video sc_vgenerator_videos_item ' 
												+ ( rez.data.fetch_id ? ' sc_vgenerator_video_fetch' : '' )
												+ ( animation_in ? ' ' + animation_in : '' )
											+ '">'
												+ '<div class="sc_vgenerator_video_inner">'
													+ '<div class="sc_vgenerator_video_wrap">'
														+ '<video'
															+ ' poster="' + rez.data.video[i].url_preview + '"'
															+ ( rez.data.video[i].url ? ' src="' + rez.data.video[i].url + '"' : '' )
															+ ( rez.data.fetch_id ? ' id="fetch-' + rez.data.fetch_id + '"' : '' )
														+ '></video>'
														+ '<span class="sc_vgenerator_video_wait_available">'
															+ '<span class="sc_vgenerator_video_wait_icon"></span>'
															+ '<span class="sc_vgenerator_video_wait_msg">' + TRX_ADDONS_STORAGE['msg_ai_helper_wait_available'] + '</span>'
														+ '</span>'
													+ '</div>'
													+ ( rez.data.fetch_id
														? '<span class="sc_vgenerator_video_fetch_info">'
																+ '<span class="sc_vgenerator_video_fetch_msg">' + rez.data.fetch_msg + '</span>'
																+ '<span class="sc_vgenerator_video_fetch_progress">'
																	+ '<span class="sc_vgenerator_video_fetch_progressbar"></span>'
																+ '</span>'
															+ '</span>'
														: ''
														)
													+ ( ! rez.data.demo && rez.data.show_download
														? '<a href="' + get_download_link( rez.data.video[i].url ? rez.data.video[i].url : rez.data.video[i].url_preview ) + '"'
															+ ' download="' + prompt.replace( /[\s]+/g, '-' ).toLowerCase() + '"'
															+ ' data-expired="' + ( ( rez.data.fetch_id ? 0 : timestamp ) + rez.data.show_download * 1000 ) + '"'
															+ ' data-elementor-open-lightbox="no"'
															//+ ' target="_blank"'
															+ ' class="sc_vgenerator_video_link sc_button sc_button_default sc_button_size_small sc_button_with_icon sc_button_icon_left"'
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
									.find( '.sc_vgenerator_video_inner video' )
									.on( 'loadeddata', function() {
										trx_addons_ai_helper_vgenerator_loadeddata_video(jQuery(this));
									} )
									.on( 'error', function() {
										trx_addons_ai_helper_vgenerator_reload_video(jQuery(this));
									} );
								$preview
									.find('.sc_vgenerator_video_wait_available' )
									.on( 'click', function(e) {
										// Stop bubbling to avoid opening the video in the popup until the video is not available
										e.stopPropagation();
										return false;
									} );

								setTimeout( function() {
									$preview.css( 'height', 'auto' );
									$sc.addClass( 'sc_vgenerator_video_show' );
									if ( ! rez.data.fetch_id ) {
										$document.trigger( 'action.init_hidden_elements', [ $preview ] );
									}
									if ( need_resize ) {
//										setTimeout( function() {
                                            // trx_addons_when_images_loaded( $preview, function() {
												$document.trigger( 'action.resize_trx_addons' );
											// } );
//										}, resize_delay );
									}
								}, animation_in ? 700 : 0 );
								// Check if download links are expired
								$preview.find( '.sc_vgenerator_video_link' ).on( 'click', function( e ) {
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
							}, $videos.length && animation_out ? 700 : 0 );
						}
						if ( rez.data.message ) {
							show_message( rez.data.message, rez.data.message_type );
						}
                    } else {
						$preview.empty();
						//alert( rez.error );
						show_message( rez.error, 'error' );
					}
                };
            } );

            // Fetch data from the server
			function fetch_data(data) {
				jQuery.post( TRX_ADDONS_STORAGE['ajax_url'], {
					nonce: TRX_ADDONS_STORAGE['ajax_nonce'],
					action: 'trx_addons_ai_helper_fetch_video',
					fetch_id: data.fetch_id,
					fetch_model: data.fetch_model,
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
						if ( rez.data && rez.data.video && rez.data.video.length > 0 ) {
							var video = rez.data.video,
								$fetch = $preview.find( '#fetch-' + data.fetch_id );
							// Fade out fetch placeholders
							if ( animation_out ) {
								for ( var i = 0; i < video.length; i++ ) {
									$fetch.eq( i ).parents( '.sc_vgenerator_video_fetch' )
										.removeClass( animation_in )
										.addClass( animation_out );
								}
							}
							// Replace fetch placeholders with real video
							setTimeout( function() {
								var $download_link;
								var currentDate = new Date();
								var timestamp = currentDate.getTime();
								for ( var i = 0; i < video.length; i++ ) {
									$fetch.eq( i ).attr( {
										src: video[i].url,
										poster: video[i].url_preview,
										controls: true,
									} );
									$download_link = $fetch.eq( i ).parents( '.sc_vgenerator_video' ).find( '.sc_vgenerator_video_link' );
									$download_link.attr( 'href', get_download_link( video[i].url ) );
									$download_link.attr( 'data-expired', parseInt( $download_link.attr( 'data-expired' ), 10 ) + timestamp );
								}
								if ( need_resize ) {
									// trx_addons_when_images_loaded( $preview, function() {
										$document.trigger( 'action.resize_trx_addons' );
									// } );
								}
							}, animation_out ? 300 : 0 );
							// Fade in real video
							setTimeout( function() {
								for ( var i = 0; i < video.length; i++ ) {
									$fetch.eq( i )
										.parents( '.sc_vgenerator_video_fetch' )
											.removeClass( 'sc_vgenerator_video_fetch' )
											.find( '.sc_vgenerator_video_fetch_info')
												.remove();
									if ( animation_in ) {
										$fetch.eq( i ).parents( '.sc_vgenerator_video' )
														.removeClass( animation_out )
														.addClass( animation_in );
									}
								}
								$document.trigger( 'action.init_hidden_elements', [ $preview ] );
								if ( need_resize ) {
									setTimeout( function() {
										// trx_addons_when_images_loaded( $preview, function() {
											$document.trigger( 'action.resize_trx_addons' );
										// } );
									}, resize_delay );
								}
							}, animation_out ? 800 : 0 );
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
					.find( '.sc_vgenerator_message_inner' )
						.html( msg )
						.parent()
							.toggleClass( 'sc_vgenerator_message_type_error', type == 'error' )
							.toggleClass( 'sc_vgenerator_message_type_info', type == 'info' )
							.toggleClass( 'sc_vgenerator_message_type_success', type == 'success' )
							.addClass( 'sc_vgenerator_message_show' )
							.slideDown( function() {
								if ( need_resize ) {
									$document.trigger( 'action.resize_trx_addons' );
								}
							} );
			}

			// Check limits for generation video
			function check_limits() {
				// Block the button if the limits are exceeded only if the demo video are not selected in the shortcode params
				if ( ! $form.data( 'vgenerator-demo-video' ) ) {
					var total, used, number;
					// Check limits for the video generation
					var $limit_total = $form.find( '.sc_vgenerator_limits_total_value' ),
						$limit_used  = $form.find( '.sc_vgenerator_limits_used_value' );
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
					var $requests_total = $form.find( '.sc_vgenerator_limits_total_requests' ),
						$requests_used  = $form.find( '.sc_vgenerator_limits_used_requests' );
					if ( $requests_total.length && $requests_used.length ) {
						total = parseInt( $requests_total.text(), 10 );
						//used  = parseInt( $requests_used.text(), 10 );
						used = ( trx_addons_get_cookie( 'trx_addons_ai_helper_vgenerator_count' ) || 0 ) * 1;
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
				$button.toggleClass( 'sc_vgenerator_form_field_disabled', true );
				$prompt.attr( 'disabled', 'disabled' );
				show_message( $form.data( 'vgenerator-limit-exceed' ), 'error' );
			}

			// Update a counter of generated video inside a limits text
			function update_limits_counter( number ) {
				var total, used;
				// Update a counter of the generated video
				var $limit_total = $form.find( '.sc_vgenerator_limits_total_value' ),
					$limit_used  = $form.find( '.sc_vgenerator_limits_used_value' );
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
				var $requests_total = $form.find( '.sc_vgenerator_limits_total_requests' ),
					$requests_used  = $form.find( '.sc_vgenerator_limits_used_requests' );
				if ( $requests_total.length && $requests_used.length ) {
					total = parseInt( $requests_total.text(), 10 );
					// used  = parseInt( $requests_used.text(), 10 );
					used = ( trx_addons_get_cookie( 'trx_addons_ai_helper_vgenerator_count' ) || 0 ) * 1;
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
				var count = trx_addons_get_cookie( 'trx_addons_ai_helper_vgenerator_count' ) || 0,
					limit = 60 * 60 * 1000 * 1,	// 1 hour
					expired = limit - ( new Date().getTime() % limit );

				trx_addons_set_cookie( 'trx_addons_ai_helper_vgenerator_count', ++count, expired );
			}

			// Return an URL to download the video
			function get_download_link( url ) {
				return trx_addons_add_to_url( TRX_ADDONS_STORAGE['site_url'], {
					'action': 'trx_addons_ai_helper_vgenerator_download',
					'video': trx_addons_get_file_name( url )
				} );
			}
        } );
    } );
} );