( function( $ ) {

	"use strict";

	$( window ).on('elementor/frontend/init', function () {

		var isEditMode = elementorFrontend.isEditMode();

		var TestimonialsHandler = elementorModules.frontend.handlers.Base.extend( {

			getDefaultSettings: function() {
				return {
					selectors: {
						testimonialsWrap: '.trx-addons-testimonials-box',
						testimonials: '.trx-addons-testimonials-container',

					}
				}
			},

			getDefaultElements: function () {
				var selectors = this.getSettings('selectors');
				return {
					$testimonialsWrap: this.$element.find( selectors.testimonialsWrap ),
					$testimonials: this.$element.find( selectors.testimonials ),
				}

			},

			bindEvents: function () {
				this.run();
			},

			equalHeight: function ( $scope, mySwiper ) {
				// Not need because the slider make equal height by option 'autoHeight: true'
				return;
				var activeSlide = $scope.find( '.swiper-slide' ).eq( mySwiper.activeIndex ),
					curSlide = activeSlide,
					perView = Math.max( 1, mySwiper.params.slidesPerView ),
					maxHeight = -1,
					i, item, itemHeight;
				// Detect max height of visible items in the current slider
				for ( i = 0; i < perView; i++ ) {
					item = curSlide.find( '.trx-addons-testimonials-container' );
					itemHeight = item.outerHeight();
					if ( maxHeight < itemHeight ) {
						maxHeight = itemHeight;
					}
					curSlide = curSlide.next();
				}
				// Set equal height for visible items in the current slider
				curSlide = activeSlide;
				for ( i = 0; i < perView; i++ ) {
					item = curSlide.find('.trx-addons-testimonials-container');
					if ( Math.abs( item.height() - maxHeight ) > 1 ) {
						item.animate( { height: maxHeight }, { duration: 200, easing: 'linear' } );
					}
					curSlide = curSlide.next();
				}
			},

			sliderInit: function( $scope, carousel, carouselWrap, elementSettings, mySwiper ) {

				carouselWrap.addClass( 'trx-addons-slider-inited' );
		
				this.equalHeight( $scope, mySwiper );
		
				var busy = false,
					busyTimer = 0,
					_this = this;
		
				$( window ).on( 'resize', trx_addons_debounce( function () {
					busy = true;
					busyTimer = setTimeout( function () {
						busy = false;
					}, 100 );
		
					// Reset height of each slide to recalculate it
					_this.elements.$testimonials.css( { height: 'auto' } );
					_this.equalHeight( $scope, mySwiper );
				}, 100 ) );
		
				mySwiper.on( 'slideChange', function () {
					if ( ! busy ) {
						_this.equalHeight( $scope, mySwiper );
					}
				} );
		
				if ( true === elementSettings.autoplay.pauseOnHover ) {
					carousel.on( 'mouseover', function () {
						mySwiper.autoplay.stop();
					} );
		
					carousel.on( 'mouseout', function () {
						mySwiper.autoplay.start();
					} );
				}
		
				if ( isEditMode ) {
					carouselWrap.resize( function () {
						//mySwiper.update();
					} );
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
							if ( wrap.find('.trx-addons-swiper-slider').length > 0 ) {
								setTimeout( function () {
									mySwiper.update();
								}, 100 );
							}
						} );
					}
				} );
				
				$( document ).trigger( 'action.slider_init', [carousel, carousel.attr('id')] );
			},

			run: function () {

				var $testimonialsWrap = this.elements.$testimonialsWrap;

				if ( ! $testimonialsWrap.length ) return;

				var settings = this.getElementSettings(),
					carousel = settings.slider,
					layout   = settings.layout, //$testimonialsWrap.eq(0).data['layout'],
					_this = this;

				if ( carousel == 'yes' ) {
					var carouselWrap = $testimonialsWrap.eq(0),
						carousel = $testimonialsWrap.find( '.trx-addons-testimonials-carousel' ).eq(0),
						sliderOptions = JSON.parse( carousel.attr( 'data-slider-settings' ) );

					if ( carousel.length > 0 ) {
						var asyncSwiper = elementorFrontend.utils.swiper;

						new asyncSwiper( carousel, sliderOptions ).then( function( newSwiperInstance ) {
							var mySwiper = newSwiperInstance;
							_this.sliderInit( $testimonialsWrap, carousel, carouselWrap, sliderOptions, mySwiper );
						} );
					}
				} else if ( 'masonry' == layout ) {
					$testimonialsWrap.eq(0).imagesLoaded( function ( e ) {
						var $masonry = $testimonialsWrap.eq(0).find('.trx-addons-testimonials-grid'),
							isotopeObj = $masonry.isotope( {
								layoutMode: layout,
								itemSelector: '.trx-addons-grid-item-wrap'
								// percentPosition: true,
								// masonry: {
								//   columnWidth: '.trx-addons-grid-item-wrap'
								// }
						} );
						$(window).on( 'resize', trx_addons_throttle( function() {
							isotopeObj.isotope( 'layout' );
						}, 200 ) );
					} );
				}

			}

		} );

		elementorFrontend.elementsHandler.attachHandler( 'trx_elm_testimonials', TestimonialsHandler );
	} );

/*
	$( window ).on('elementor/frontend/init', function () {
		if ( elementorFrontend.isEditMode() ) {
			isEditMode = true;
		}
		// elementorFrontend.hooks.addAction( 'frontend/element_ready/trx_elm_testimonials', TestimonialsHandler );
	} );
*/
}( jQuery ) );