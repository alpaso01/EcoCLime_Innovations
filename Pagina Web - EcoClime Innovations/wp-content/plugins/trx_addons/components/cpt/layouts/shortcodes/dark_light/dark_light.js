/* global jQuery */

jQuery(document).on('action.ready_trx_addons', function() {

	"use strict";

	jQuery( document ).on( 'action.init_hidden_elements', function( e, $container ) {

		if ( $container === undefined ) {
			$container = jQuery( 'body' );
		}

		$container.find( '.sc_layouts_dark_light:not(.sc_layouts_dark_light_inited)' ).each( function() {
			jQuery( this )
				.addClass( 'sc_layouts_dark_light_inited' )
				.on( 'click', function(e) {
					var $self = jQuery( this ),
						$active = $self.find( '.sc_layouts_dark_light_active' ),
						active_mode = $active.hasClass( 'sc_layouts_dark_light_dark' ) ? 'dark' : 'light',
						$next = $active.siblings( '.sc_layouts_dark_light_item' ),
						next_mode = $next.hasClass( 'sc_layouts_dark_light_dark' ) ? 'dark' : 'light',
						schemes = $self.data( 'schemes' ),
						permanent = $self.data( 'permanent' ),
						i, data, option_name;

					// Change an active mode on all switchers on the page
					jQuery( '.sc_layouts_dark_light' ).each( function() {
						var $current = jQuery( this );
						$current
							.removeClass( 'sc_layouts_dark_light_active_' + active_mode )
							.addClass( 'sc_layouts_dark_light_active_' + next_mode )
							.find( '.sc_layouts_dark_light_item' )
								.toggleClass( 'sc_layouts_dark_light_active' );
					} );

					// Change shemes in all areas
					if ( schemes ) {
						for ( i = 0; i < schemes[active_mode].length; i++ ) {
							data = schemes[active_mode][i];
							jQuery( data.selector ).removeClass( 'scheme_' + data.scheme );
						}
						for ( i = 0; i < schemes[next_mode].length; i++ ) {
							data = schemes[next_mode][i];
							jQuery( data.selector ).addClass( 'scheme_' + data.scheme );
						}
					}

					// Save current mode in the cookies (if permanent is on) or delete cookies (if permanent is off)
					for ( i = 0; i < schemes[next_mode].length; i++ ) {
						data = schemes[next_mode][i];
						option_name = trx_addons_apply_filters( 'trx_addons_filter_dark_light_option_name', ( data.area == 'content' ? 'color' : ( data.area == 'other' ? 'menu' : data.area ) ) + '_scheme', data );
						if ( permanent ) {
							trx_addons_set_cookie( option_name, data.scheme );
						} else {
							trx_addons_del_cookie( option_name );
						}
					}

					e.preventDefault();
					return false;
				} );
		} );
	} );
} );