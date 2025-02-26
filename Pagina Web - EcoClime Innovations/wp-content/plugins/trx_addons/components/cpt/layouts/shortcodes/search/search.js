/* global jQuery, TRX_ADDONS_STORAGE */

jQuery(document).on('action.ready_trx_addons', function() {
	"use strict";

	if (jQuery('.search_wrap:not(.inited)').length > 0) {
		jQuery('.search_wrap:not(.inited)').each(function() {
			var search_wrap = jQuery(this).addClass('inited'),
				search_field = search_wrap.find('.search_field'),
				search_button = search_wrap.find('.search_submit');
			var ajax_timer = null;

			search_field.on('keyup', function(e) {
				// ESC is pressed
				if (e.keyCode == 27) {
					search_field.val('');
					trx_addons_search_close(search_wrap);
					e.preventDefault();
					return;
				}
				// AJAX search
				if (search_wrap.hasClass('search_ajax')) {
					var s = search_field.val();
					if (ajax_timer) {
						clearTimeout(ajax_timer);
						ajax_timer = null;
					}
					if (s.length >= 4) {
						ajax_timer = setTimeout(function() {
							search_wrap.addClass('search_progress');
							jQuery.post(TRX_ADDONS_STORAGE['ajax_url'], {
								action: 'ajax_search',
								nonce: TRX_ADDONS_STORAGE['ajax_nonce'],
								text: s,
								post_types: search_wrap.find('input[name="post_types"]').val()
							}).done(function(response) {
								clearTimeout(ajax_timer);
								ajax_timer = null;
								var rez = {};
								if (response=='' || response==0) {
									rez = { error: TRX_ADDONS_STORAGE['msg_search_error'] };
								} else {
									try {
										rez = JSON.parse(response);
									} catch (e) {
										rez = { error: TRX_ADDONS_STORAGE['msg_search_error'] };
										console.log(response);
									}
								}
								var msg = rez.error === '' ? rez.data : rez.error;
								search_wrap.removeClass('search_progress');
								search_field.parents('.search_ajax').find('.search_results_content').empty().append(msg);
								search_field.parents('.search_ajax').find('.search_results').fadeIn();
								jQuery( document ).trigger( 'action.got_ajax_response', {
									action: 'ajax_search',
									result: rez
								});
							});
						}, 500);
					}
				}
			});

			// Click "Search submit"
			search_wrap.find('.search_submit').on('click', function(e) {
				e.preventDefault();
				if ( ( search_wrap.hasClass('search_style_expand') || search_wrap.hasClass('search_style_fullscreen') ) && ! search_wrap.hasClass('search_opened') ) {
					var duration = trx_addons_apply_filters( 'trx_addons_filter_search_fullscreen_fade_duration', 0 );
					if ( search_wrap.hasClass('search_style_fullscreen') ) {
						jQuery('body').addClass('sc_layouts_search_opened');
						var animation = search_wrap.data( 'overlay-animation' );
						if ( animation ) {
							var animation_duration = search_wrap.data( 'overlay-animation-duration' );
							search_wrap
								.find( '.search_form_wrap')
									.addClass( animation + ' animated' + ( animation_duration ? ' animated-' + animation_duration : '' ) );
							search_wrap
								.find( '.search_form_overlay')
									.addClass( 'animated' + ( animation_duration ? '-' + animation_duration : '' ) );
						} else {
							search_wrap
								.find( '.search_form_overlay')
									.toggleClass( 'trx_addons_no_transition', true );	//search_form_overlay_no_transition
							}
						// var sw = search_wrap.width(),
						// 	sh = search_wrap.height();
						var placeholder = search_wrap.find('> .search_submit_placeholder');
						search_wrap.hide();
						if ( ! placeholder.length ) {
							// search_wrap.append( '<div class="search_submit_placeholder"></div>' );
							search_button.clone().appendTo( search_wrap ).addClass( 'search_submit_placeholder' );
							placeholder = search_wrap.find('> .search_submit_placeholder');
						}
						// placeholder.css( {
						// 	width: sw,
						// 	height: sh
					 	// } );
						search_wrap.addClass('search_opened').fadeIn( duration );
					} else {
						search_wrap.addClass('search_opened');
					}
					setTimeout( function() {
						search_field.get(0).focus();
					}, 0 );	// duration + 200 );
				} else if ( search_field.val() === '' ) {
					if ( search_wrap.hasClass('search_opened') && search_wrap.hasClass('search_style_expand') ) {
						trx_addons_search_close(search_wrap);
					} else {
						search_field.get(0).focus();
					}
				} else {
					search_wrap.find('form').get(0).submit();
				}
				return false;
			});
			// Click on the overlay
			search_wrap.find('.search_form_overlay').on('click', function(e) {
				e.preventDefault();
				trx_addons_search_close(search_wrap);
				return false;
			});
			// Click "Search close"
			search_wrap.find('.search_close').on('click', function(e) {
				e.preventDefault();
				trx_addons_search_close(search_wrap);
				return false;
			});
			// Click "Close search results"
			search_wrap.find('.search_results_close').on('click', function(e) {
				e.preventDefault();
				jQuery(this).parent().fadeOut();
				return false;
			});
			// Click "More results"
			search_wrap.on('click', '.search_more', function(e) {
				e.preventDefault();
				if (search_field.val() !== '') {
					search_wrap.find('form').get(0).submit();
				}
				return false;
			});
		});
	}
	
	// Close search field (remove class 'search_opened' and close search results)
	function trx_addons_search_close(search_wrap) {
		var duration = trx_addons_apply_filters( 'trx_addons_filter_search_fullscreen_fade_duration', 0 );
		search_wrap.find('.search_field').get(0).blur();
		if ( search_wrap.hasClass('search_style_fullscreen') ) {
			jQuery('body').removeClass('sc_layouts_search_opened');
			search_wrap.find('.search_form_overlay').addClass( 'search_form_overlay_hide' );
			if ( duration > 0 ) {
				search_wrap.find('.search_results').fadeOut( duration );
			} else {
				search_wrap.find('.search_results').hide();
			}
			var on_end_animation = function() {
				var search_button = search_wrap.find('.search_submit');
				search_button
					.addClass( 'trx_addons_no_transition' );
				search_wrap
					.removeClass( 'search_opened' )
					.removeAttr( 'style' )
					.show();
				search_wrap
					.find('.search_form_overlay')
					.removeClass( 'search_form_overlay_hide' );
				search_button
					.removeClass( 'trx_addons_no_transition' );
			};
			var animation_exit = search_wrap.data( 'overlay-animation-exit' ) || '';
			if ( animation_exit ) {
				animation_exit = animation_exit.replace( 'In', 'Out' ).replace( 'Down', 'Up' );
				var animation = search_wrap.data( 'overlay-animation' );
				var animation_duration = search_wrap.data( 'overlay-animation-duration' );
				var search_form_wrap = search_wrap.find( '.search_form_wrap');
				if ( animation ) {
					search_form_wrap.removeClass( animation + ' animated' + ( animation_duration ? ' animated-' + animation_duration : '' ) )
				}
				trx_addons_on_end_animation( search_form_wrap.get(0), function() {
					search_form_wrap
						.removeClass( animation_exit ? animation_exit + ' animated' + ( animation_duration ? ' animated-' + animation_duration : '' ) : '' );
						on_end_animation();
				}, animation_duration == 'slow' ? 1500 : ( animation_duration == 'fast' ? 750 : 1000 ) );
				search_form_wrap.addClass( animation_exit ? animation_exit + ' animated' + ( animation_duration ? ' animated-' + animation_duration : '' ) : '' );
			} else {
				if ( duration > 0 ) {
					search_wrap.fadeTo( duration / 5 * 4, 0.33, on_end_animation );
				} else {
					on_end_animation();
				}
			}
		} else {
			search_wrap
				.removeClass('search_opened')
				.find('.search_results')
					.fadeOut();
		}
	}

});