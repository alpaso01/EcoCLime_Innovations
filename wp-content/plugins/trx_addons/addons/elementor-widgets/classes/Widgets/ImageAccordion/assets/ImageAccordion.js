( function( $ ) {

	"use strict";

    var ImageAccordionHandler = function ($scope, $) {
		var imageAccordion   = $scope.find('.trx-addons-image-accordion').eq(0),
        	elementSettings  = trx_addons_elementor_get_settings( $scope ),
    		action           = elementSettings.accordion_action,
        	disableBodyClick = elementSettings.disable_body_click,
		    id               = imageAccordion.attr( 'id' ),
		    $item            = $('#'+ id +' .trx-addons-image-accordion-item'),
            holdHover        = imageAccordion.data( 'hold-hover' );
		if ( 'on-hover' === action ) {
			$item.hover(
				function() {
					var $self = $(this);
                    $item.css('flex', '1');
                    $item.removeClass('trx-addons-image-accordion-active');
                    $self.addClass('trx-addons-image-accordion-active');
                    $item.find('.trx-addons-image-accordion-content-wrap').removeClass('trx-addons-image-accordion-content-active');
                    $self.find('.trx-addons-image-accordion-content-wrap').addClass('trx-addons-image-accordion-content-active');
                    $self.css('flex', '3');
                },
                function() {
                    $item.css('flex', '1');
                    $item.find('.trx-addons-image-accordion-content-wrap').removeClass('trx-addons-image-accordion-content-active');
                    $item.removeClass('trx-addons-image-accordion-active');

                    if ( typeof holdHover !== undefined ) {
                        var holdItem = imageAccordion.find('.trx-addons-image-accordion-item').eq(holdHover);

                        holdItem.addClass('trx-addons-image-accordion-active');
                        holdItem.find('.trx-addons-image-accordion-content-wrap').addClass('trx-addons-image-accordion-content-active');
                        holdItem.css('flex', '3');
                    }
                }
            );
        }
		$item.on( 'click', function(e) {
			var $self = $(this),
				is_active = $self.hasClass('trx-addons-image-accordion-active');
			if ( 'on-click' === action || ! is_active ) {
				e.stopPropagation(); // when you click the button, it stops the page from seeing it as clicking the body too
				$item.css('flex', '1');
				$item.removeClass('trx-addons-image-accordion-active');
				$self.addClass('trx-addons-image-accordion-active');
				$item.find('.trx-addons-image-accordion-content-wrap').removeClass('trx-addons-image-accordion-content-active');
				$self.find('.trx-addons-image-accordion-content-wrap').addClass('trx-addons-image-accordion-content-active');
				$self.css('flex', '3');
				if ( ! is_active ) {
					e.preventDefault();
					return false;
				}
			}
		} );

		$('#'+ id).on( 'click', function(e) {
			e.stopPropagation(); // when you click within the content area, it stops the page from seeing it as clicking the body too
		} );

		if ( 'yes' !== disableBodyClick ) {
			$('body').on( 'click', function() {
				$item.css('flex', '1');
				$item.find('.trx-addons-image-accordion-content-wrap').removeClass('trx-addons-image-accordion-content-active');
				$item.removeClass('trx-addons-image-accordion-active');
			} );
		}
    };

	$( window ).on( 'elementor/frontend/init', function () {
		elementorFrontend.hooks.addAction('frontend/element_ready/trx_elm_image_accordion.default', ImageAccordionHandler);
	} );

}( jQuery ) );