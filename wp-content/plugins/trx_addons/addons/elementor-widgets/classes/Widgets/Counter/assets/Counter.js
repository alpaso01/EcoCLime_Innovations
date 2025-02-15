( function( $ ) {

	"use strict";

    var CounterHandler = function ( $scope, $ ) {
        var counterElem   = $scope.find('.trx-addons-counter').eq(0),
            target        = counterElem.data('target'),
            separator     = $scope.find('.trx-addons-counter-number').data('separator'),
			separatorChar = $scope.find('.trx-addons-counter-number').data('separator-char'),
			format        = ( separatorChar !== '' ) ? '(' + separatorChar + 'ddd).dd' : '(,ddd).dd';

		var counter = function () {
			$(target).each(function () {
				var to     = $(this).data('to'),
					speed  = $(this).data('speed'),
					od     = new Odometer({
						el:       this,
						value:    0,
						duration: speed,
						format:   (separator === 'yes') ? format : ''
					});
				od.render();
				setInterval(function () {
					od.update(to);
				});
			})
		};

		if ( 'undefined' !== typeof elementorFrontend.waypoint ) {
			elementorFrontend.waypoint( counterElem, counter, { offset: '80%', triggerOnce: true } );
		} else {
			trx_addons_intersection_observer_add( counterElem, function( item, enter ) {
				if ( enter ) {
					trx_addons_intersection_observer_remove( item );
					if ( ! counterElem.hasClass( 'trx-addons-count-complete' ) ) {
						counterElem.addClass( 'trx-addons-count-complete' );
						counter();
					}
				}
			} );
		}
	};

	$( window ).on( 'elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction( 'frontend/element_ready/trx_elm_counter.default', CounterHandler );
	} );

}( jQuery ) );