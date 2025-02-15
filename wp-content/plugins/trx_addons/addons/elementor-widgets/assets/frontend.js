( function( $ ) {

	"use strict";
		
	var isEditMode = false;
	
	var swiperSliderInit = function( carousel, elementSettings, sliderOptions ) {
		$( carousel ).closest( '.elementor-widget-wrap' ).addClass( 'e-swiper-container' );
		$( carousel ).closest( '.elementor-widget' ).addClass( 'e-widget-swiper' );

		// if ( 'undefined' === typeof Swiper ) {
			var asyncSwiper = elementorFrontend.utils.swiper;

			new asyncSwiper( carousel, sliderOptions ).then( function( newSwiperInstance ) {
				var mySwiper = newSwiperInstance;
				swiperSliderAfterInit( carousel, elementSettings, mySwiper );
			} );
		// } else {
		// 	var mySwiper = new Swiper( carousel, sliderOptions );
		// 	swiperSliderAfterInit( carousel, elementSettings, mySwiper );
		// }
	};

	var swiperSliderAfterInit = function( carousel, carouselWrap, elementSettings, mySwiper ) {
		if ( 'yes' === elementSettings.pause_on_hover ) {
			carousel.on( 'mouseover', function() {
				mySwiper.autoplay.stop();
			} );
			carousel.on( 'mouseout', function() {
				mySwiper.autoplay.start();
			} );
		}
		if ( isEditMode ) {
			carouselWrap.resize( function() {
				// mySwiper.update();
			} );
		}
		widgetUpdate( mySwiper, '.trx-addons-swiper-slider', 'swiper' );
				
		$( document ).trigger( 'action.slider_init', [carousel, carousel.attr('id')] );
	};
	
	var swiperSliderHandler = function( $scope, $ ) {
		var elementSettings = trx_addons_elementor_get_settings( $scope ),
			carousel        = $scope.find( '.trx-addons-swiper-slider' ),
			sliderOptions   = ( carousel.attr( 'data-slider-settings' ) !== undefined ) ? JSON.parse( carousel.attr( 'data-slider-settings' ) ) : '';

		swiperSliderInit( carousel, elementSettings, sliderOptions );
	};
	
	var widgetUpdate = function( slider, selector, type ) {
		if ( 'undefined' === typeof type ){
			type = 'swiper';
		}

		var $triggers = [
			'trx-addons-action-tabs-switched',
			'trx-addons-action-toggle-switched',
			'trx-addons-action-accordion-switched',
			'trx-addons-action-popup-opened',
		];

		$triggers.forEach( function( trigger ) {
			if ( 'undefined' !== typeof trigger ) {
				$( document ).on( trigger, function( e, wrap ) {
					if ( trigger == 'trx-addons-action-popup-opened' ) {
						wrap = $( '.trx-addons-modal-popup-' + wrap );
					}
					if ( wrap.find( selector ).length > 0 ) {
						setTimeout( function() {
							if ( 'swiper' === type ) {
								slider.update();
							} else if ( 'gallery' === type ) {
								var $gallery = wrap.find( '.trx-addons-image-gallery' ).eq(0);
								$gallery.isotope( 'layout' );
							}
						}, 100 );
					}
				} );
			}
		} );
	};
	
	$( window ).on( 'elementor/frontend/init', function () {
        if ( elementorFrontend.isEditMode() ) {
			isEditMode = true;
		}
		// Posts slider is inited in the Posts.js
		//elementorFrontend.hooks.addAction( 'frontend/element_ready/trx_elm_posts.default', swiperSliderHandler );
	} );

}( jQuery ) );