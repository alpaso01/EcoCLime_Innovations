( function( $ ) {

	"use strict";

	var TabsHandler = function ( $scope, $ ) {

		$scope.find( '.trx-addons-tabs-'+ $scope.data("id") ).each( function() {
			var $self           = $( this ),
				$tabs 		    = $self.find( ' > .trx-addons-tabs-nav > [data-tab]' ),
				$contents	    = $self.find( ' > .trx-addons-tabs-content' ),
				isTabActive     = false,
				isContentActive = false;
			$tabs.each( function() {
				if ( $(this).hasClass( 'active' ) ) {
					isTabActive = true;
				}
			} );
			$contents.each( function() {
				if( $(this).hasClass( 'active' ) ) {
					isContentActive = true;
				}
			} );
			if ( ! isTabActive ) {
				$tabs.eq(0).addClass( 'active' );
			}
			if ( ! isContentActive ) {
				$contents.eq(0).addClass( 'active' );
			}
			$tabs.on( 'click', function() {
				var $tab = $(this);
				$tabs.removeClass( 'active' );
				$contents.removeClass( 'active' );
				$tab.addClass( 'active' );
				$tabs.eq( $tab.index() ).addClass( 'active' );
				$contents.eq( $tab.index() ).addClass( 'active' );
			} );
		} );
	};

	$( window ).on( 'elementor/frontend/init', function () {
		elementorFrontend.hooks.addAction( 'frontend/element_ready/trx_elm_tabs.default', TabsHandler );
	} );

}( jQuery ) );