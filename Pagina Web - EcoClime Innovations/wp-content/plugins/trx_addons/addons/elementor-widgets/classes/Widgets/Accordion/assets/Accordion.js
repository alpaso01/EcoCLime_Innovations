( function( $ ) {

	"use strict";

	var AccordionHandler = function ( $scope, $ ) {
		var accordionTitle = $scope.find('.trx-addons-accordion-tab-title'),
			elementSettings = trx_addons_elementor_get_settings( $scope ),
			accordionType = elementSettings.accordion_type,
			accordionSpeed = elementSettings.toggle_speed;

		// Open default actived tab
		accordionTitle.each( function () {
			var $self = $(this);
			if ( $self.hasClass('trx-addons-accordion-tab-active-default') ) {
				$self.addClass('trx-addons-accordion-tab-show trx-addons-accordion-tab-active');
				$self.next().slideDown( accordionSpeed );
			}
		} );

		// Remove multiple click event for nested accordion
		accordionTitle.unbind( 'click' );

		accordionTitle.on( 'click keypress', function( e ) {
			e.preventDefault();

			var validClick = ( e.which == 1 || e.which == 13 || e.which == 32 || e.which == undefined ) ? true : false;

			if ( ! validClick ) {
				return;
			}

			var $self = $(this),
				$item = $self.parent(),
				container = $self.closest('.trx-addons-accordion'),
				item = $self.closest('.trx-addons-accordion-item'),
				title = container.find('.trx-addons-accordion-tab-title'),
				content = container.find('.trx-addons-accordion-tab-content');

			$(document).trigger('trx-addons-accordion-switched', [$item]);

			if (accordionType === 'accordion') {
				title.removeClass('trx-addons-accordion-tab-active-default');
				content.removeClass('trx-addons-accordion-tab-active-default');

				if ( $self.hasClass( 'trx-addons-accordion-tab-show' ) ) {
					item.removeClass('trx-addons-accordion-item-active');
					$self.removeClass('trx-addons-accordion-tab-show trx-addons-accordion-tab-active');
					$self.attr('aria-expanded', 'false');
					$self.next().slideUp( accordionSpeed );
				} else {
					container.find('.trx-addons-accordion-item').removeClass('trx-addons-accordion-item-active');
					title.removeClass('trx-addons-accordion-tab-show trx-addons-accordion-tab-active');
					content.slideUp( accordionSpeed );
					$self.toggleClass('trx-addons-accordion-tab-show trx-addons-accordion-tab-active');
					title.attr('aria-expanded', 'false');
					item.toggleClass('trx-addons-accordion-item-active');
					if ( $self.hasClass( 'trx-addons-accordion-tab-title' ) ) {
						$self.attr('aria-expanded', 'true');
					}
					$self.next().slideToggle( accordionSpeed, function() {
						var $content = $(this);
						$(document).trigger( 'action.activate_accordion_tab', [$content] );
						$(document).trigger( 'action.init_hidden_elements', [$content] );
						// Way 1: works only with our handlers
						$(document).trigger( 'action.resize_trx_addons' );
					} );
				}
			} else {
				// For acccordion type 'toggle'
				if ( $self.hasClass( 'trx-addons-accordion-tab-show' ) ) {
					$self.removeClass('trx-addons-accordion-tab-show trx-addons-accordion-tab-active');
					$self.next().slideUp( accordionSpeed );
				} else {
					$self.addClass('trx-addons-accordion-tab-show trx-addons-accordion-tab-active');
					$self.next().slideDown( accordionSpeed, function() {
						var $content = $(this);
						$(document).trigger( 'action.activate_accordion_tab', [$content] );
						$(document).trigger( 'action.init_hidden_elements', [$content] );
						// Way 1: works only with our handlers
						$(document).trigger( 'action.resize_trx_addons' );
					} );
				}
			}
		} );

		// Trigger filter by hash parameter in URL.
		accordion_hashchange();

		// Trigger filter on hash change in URL.
		$( window ).on( 'hashchange', function () {
			accordion_hashchange();
		} );
	};

	function accordion_hashchange() {
		if ( location.hash && $( location.hash ).length > 0 ) {
			var element = $( location.hash + '.trx-addons-accordion-tab-title' );

			if ( element && element.length > 0 ) {
				location.href = '#';
				$( 'html, body' ).animate( {
					scrollTop: ( element.parents('.trx-addons-accordion-item').offset().top - 50) + 'px'
				}, 500, function () {
					if ( ! element.parents( '.trx-addons-accordion-item').hasClass('trx-addons-accordion-item-active' ) ) {
						element.trigger( 'click' );
					}
				} );
			}
		}
	}

	$( window ).on( 'elementor/frontend/init', function () {
		elementorFrontend.hooks.addAction( 'frontend/element_ready/trx_elm_accordion.default', AccordionHandler );
	} );

}( jQuery ) );