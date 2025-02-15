"use strict";
jQuery( document ).ready( function() {
	if ( typeof elementorFrontend == 'undefined' ) {
		return;
	}
	// Add animation for the SVG paths after the timeout to allow the animation script breaks the text into words or chars
	setTimeout( function() {
		if ( ! elementorFrontend.isEditMode() ) {
			// Paint in Frontend only after the element comes into the view
			jQuery( '.elementor-widget-trx_elm_advanced_title.trx-addons-animate .trx-addons-advanced-title').each( function() {
				var $self = jQuery( this ),
					delay = $self.data( 'delay' ) || 0;
				$self.find( '.trx-addons-svg-wrapper path' ).each( function( idx ) {
					var $path = jQuery( this );
					var handler = function() {
						if ( ! $path.hasClass( 'trx-addons-animate-complete' ) ) {
							$path.addClass( 'trx-addons-animate-complete' );
							setTimeout( function() {
								$path.css( 'animation-play-state', 'running' );
							}, 300 * idx + 400 + parseInt( delay ) );
						}
					};
					if ( 'undefined' !== typeof elementorFrontend.waypoint ) {
						elementorFrontend.waypoint( $path.get(0), handler, { offset: '90%', triggerOnce: true } );					
					} else {
						trx_addons_intersection_observer_add( $path, function( item, enter ) {
							if ( enter ) {
								trx_addons_intersection_observer_remove( item );
								handler();
							}
						} );
					}
				} );
			} );
		} else {
			// Repaint after the elementor is changed in the Editor
			elementorFrontend.hooks.addAction( 'frontend/element_ready/global', function( $cont ) {
				$cont.find( '.trx-addons-advanced-title' ).each( function() {
					var $self = jQuery( this ),
						delay = $self.data( 'delay' ) || 0;
					$self.find( '.trx-addons-svg-wrapper path' ).each( function( idx ) {
						var $path = jQuery( this );
						setTimeout( function() {
							$path.css( 'animation-play-state', 'running' );
						}, 300 * idx + 400 + parseInt( delay ) );
					} );
				} );
			} );
		}
	}, 100 );

	// Remove wrappers .sc_item_animated_block and .sc_item_word around svg inside .trx-addons-svg-wrapper
	trx_addons_add_filter( 'trx_addons_filter_animation_wrap_items', function( html ) {
		if ( html.indexOf( 'class="trx-addons-svg-wrapper' ) >= 0 ) {
			var $obj = jQuery( html );
			$obj.find( '.trx-addons-svg-wrapper' ).each( function() {
				var $wrap = jQuery( this );
				if ( $wrap.find( '.sc_item_animated_block' ).length > 0 || $wrap.find( '.sc_item_word' ).length > 0 ) {
					var $svg = $wrap.find( 'svg' );
					if ( $svg.length ) {
						html = html.replace( $wrap.html(), $svg.get(0).outerHTML );
					}
				}
			} );
		}
		return html;
	} );
} );