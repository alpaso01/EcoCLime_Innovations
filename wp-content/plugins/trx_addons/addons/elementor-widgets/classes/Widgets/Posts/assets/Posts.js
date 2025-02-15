( function( $ ) {

	"use strict";

	var loadStatus = true;
	var count = 1;
	var loader = '';
	var total = 0;
	var isEditMode = false;

	// Make the height of the visible posts in the slider equal
	function equalHeight( $scope, mySwiper ) {
		if ( ! $scope.hasClass( 'trx-addons-posts-equal-height-yes' ) ) {
			return;
		}
		var activeSlide = $scope.find( '.swiper-slide' ).eq( mySwiper.activeIndex ),
			curSlide = activeSlide,
			perView = Math.max( 1, mySwiper.params.slidesPerView ),
			maxHeight = -1,
			i, post, postHeight;
		// Detect max height of visible posts in the current slider
		for ( i = 0; i < perView; i++ ) {
			post = curSlide.find( '.trx-addons-posts-item' );
			postHeight = post.outerHeight();
			if ( maxHeight < postHeight ) {
				maxHeight = postHeight;
			}
			curSlide = curSlide.next();
		}
		// Set equal height for visible posts in the current slider
		curSlide = activeSlide;
		for ( i = 0; i < perView; i++ ) {
			post = curSlide.find('.trx-addons-posts-item');
			if ( Math.abs( post.height() - maxHeight ) > 1 ) {
				post.animate( { height: maxHeight }, { duration: 200, easing: 'linear' } );
			}
			curSlide = curSlide.next();
		}
	}

	var swiperSliderAfterInit = function( $scope, carousel, carouselWrap, elementSettings, mySwiper ) {

		carouselWrap.addClass( 'trx-addons-slider-inited' );

		equalHeight( $scope, mySwiper );

		var busy = false,
			busyTimer = 0;

		$( window ).resize( trx_addons_debounce( function () {
			busy = true;
			busyTimer = setTimeout( function () {
				busy = false;
			}, 100 );

			// Reset height of each slide to recalculate it
			$scope.find( '.trx-addons-posts-item' ).css( { height: 'auto' } );
			equalHeight( $scope, mySwiper );
		}, 100 ) );

		mySwiper.on( 'slideChange', function () {
			if ( ! busy ) {
				equalHeight( $scope, mySwiper );
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
	};

	var PostsHandler = function ( $scope, $ ) {

		var container = $scope.find( '.trx-addons-posts-container' ),
			selector = $scope.find( '.trx-addons-posts-grid' ),
			layout = $scope.find( '.trx-addons-posts' ).data( 'layout' ),
			loader = $scope.find( '.trx-addons-posts-loader' );

		if ( 'masonry' == layout ) {

			$scope.imagesLoaded( function ( e ) {
				var isotopeObj = selector.isotope( {
					layoutMode: layout,
					itemSelector: '.trx-addons-grid-item-wrap',
				} );
				$(window).on( 'resize', trx_addons_throttle( function() {
					isotopeObj.isotope( 'layout' );
				}, 200 ) );
			} );

		} else if ( 'carousel' == layout ) {

			var carouselWrap = $scope.find( '.swiper-container-wrap' ).eq(0),
				carousel = $scope.find( '.trx-addons-posts-carousel' ).eq(0),
				sliderOptions = JSON.parse( carousel.attr( 'data-slider-settings' ) );


			if ( carousel.length > 0 ) {
				var asyncSwiper = elementorFrontend.utils.swiper;

				new asyncSwiper( carousel, sliderOptions ).then( function( newSwiperInstance ) {
					var mySwiper = newSwiperInstance;
					swiperSliderAfterInit( $scope, carousel, carouselWrap, sliderOptions, mySwiper );
				} );
			}
		}
	};

	$( 'body' ).on( 'click', '.trx-addons-posts-pagination-ajax .page-numbers', function(e) {

		var $self = $( this ),
			$scope = $self.closest( '.elementor-widget-trx_elm_posts' );

		if ( 'main' == $scope.find( '.trx-addons-posts-grid' ).data( 'query-type' ) ) {
			return;
		}

		e.preventDefault();

		var page_number = 1;
		var curr = parseInt( $scope.find('.trx-addons-posts-pagination .page-numbers.current').html() );

		if ( $self.hasClass('next') ) {
			page_number = curr + 1;
		} else if ( $self.hasClass('prev') ) {
			page_number = curr - 1;
		} else {
			page_number = $self.html();
		}

		$scope
			.find( '.trx-addons-posts-grid' )
			.append( '<div class="trx-addons-posts-loader"><div class="trx-addons-loader"></div><div class="trx-addons-loader-overlay"></div></div>' );

		var $args = {
			'page_id': $scope.find('.trx-addons-posts-grid').data('page'),
			'widget_id': $scope.data('id'),
			'skin': $scope.find('.trx-addons-posts-grid').data('skin'),
			'page_number': page_number
		};

		$('html, body').animate( {
			scrollTop: ( ( $scope.find('.trx-addons-posts-container').offset().top ) - 30 )
		}, 'slow');

		_callAjax( $scope, $args );

		return false;

	} );

	$( 'body' ).on( 'click', '.trx-addons-posts-pagination-load_more .trx-addons-load_more-button', function (e) {
		var $self = $( this ),
			$scope = $self.closest( '.elementor-widget-trx_elm_posts' );

		if ( 'main' == $scope.find( '.trx-addons-posts-grid' ).data( 'query-type' ) ) {
			return;
		}

		e.preventDefault();

		var curr = parseInt( $scope.find( '.trx-addons-posts-pagination-load_more' ).data( 'page' ) );
		var page_number = curr + 1;

		$scope.find('.trx-addons-posts-pagination').hide();

		if (page_number > $scope.find( '.trx-addons-posts-pagination-load_more' ).data( 'max-page' )) {
			return;
		}

		var $args = {
			'page_id': $scope.find('.trx-addons-posts-grid').data('page'),
			'widget_id': $scope.data('id'),
			'skin': $scope.find('.trx-addons-posts-grid').data('skin'),
			'page_number': page_number
		};

		_callAjax( $scope, $args, true );

		return false;
	} );

	$( window ).on( 'scroll', function (e) {
		let scopes = $( '.elementor-widget-trx_elm_posts .trx-addons-posts-pagination-wrap[data-pagination_type="infinite"]' );

		if ( $( scopes ).length > 0 ) {
			$( scopes ).each(( scope_index, scope_item ) => {
				let $scope = $( scope_item ).closest( '.elementor-widget-trx_elm_posts' );

				if ( 'main' == $scope.find( '.trx-addons-posts-grid' ).data( 'query-type' ) ) {
					return true;
				}

				if ( $scope.find( '.trx-addons-posts-pagination-infinite' ).length <= 0 ) {
					return true;
				}

				let $scope_top = $scope.offset().top + $scope.outerHeight( true ),
				$window_top = $( window ).scrollTop() + $( window ).height();

				if ($window_top < $scope_top) {
					return true;
				}
				
				var curr = parseInt( $scope.find( '.trx-addons-posts-pagination-infinite' ).data( 'page' ) );
				var page_number = curr + 1;

				if (page_number > $scope.find( '.trx-addons-posts-pagination-infinite' ).data( 'max-page' )) {
					return true;
				}

				var $args = {
					'page_id': $scope.find('.trx-addons-posts-grid').data('page'),
					'widget_id': $scope.data('id'),
					'skin': $scope.find('.trx-addons-posts-grid').data('skin'),
					'page_number': page_number
				};

				_callAjax( $scope, $args, true );
			})
		}
	} );

	var _callAjax = function( $scope, $obj, $append, $count ) {

		if ( ! loadStatus ) return false;
		loadStatus = false;

		var loader = $scope.find('.trx-addons-posts-loader').fadeIn();

		$.ajax( {
			url: trx_addons_posts_script.ajax_url,
			data: {
				action: 'trx_addons_action_get_post',
				page_id: $obj.page_id,
				widget_id: $obj.widget_id,
				skin: $obj.skin,
				page_number: $obj.page_number,
				nonce: trx_addons_posts_script.posts_nonce,
			},
			dataType: 'json',
			type: 'POST',
			success: function (data) {

				var sel = $scope.find('.trx-addons-posts-grid');

				if ( true == $append ) {
					sel.append( data.data.html );
				} else {
					sel.html( data.data.html );
				}

				var pagination_type = $scope.find('.trx-addons-posts-pagination-wrap').data('pagination_type');

				if ( 'load_more' == pagination_type || 'infinite' == pagination_type ) {
					$scope.find('.trx-addons-posts-pagination').data( 'page', $obj.page_number );
				} else {
					$scope.find('.trx-addons-posts-pagination-wrap').html( data.data.pagination );
				}

				if ('load_more' == pagination_type) {
					if ( $obj.page_number + 1 > $scope.find( '.trx-addons-posts-pagination-load_more' ).data( 'max-page' )) {
						$scope.find('.trx-addons-posts-pagination').hide();
					} else {
						$scope.find('.trx-addons-posts-pagination').show();
					}
				}

				var layout = $scope.find('.trx-addons-posts-grid').data('layout'),
					selector = $scope.find('.trx-addons-posts-grid');

				if ( 'masonry' == layout ) {
					let window_top = $( window ).scrollTop();

					$scope.imagesLoaded( function () {
						selector.isotope( 'destroy' );
						selector.isotope({
							layoutMode: layout,
							itemSelector: '.trx-addons-grid-item-wrap',
						} );

						if ( 'load_more' == pagination_type || 'infinite' == pagination_type ) {
							window.scrollTo( { top: window_top, behavior: "instant" } );
						}
					} );
				}

				//	Complete the process 'loadStatus'
				loadStatus = true;
				if ( true == $append ) {
					loader.fadeOut();
				}

				$count = $count + 1;

				$scope.trigger('posts.rendered');
			}
		});
	};

	$( window ).on('elementor/frontend/init', function () {
		if ( elementorFrontend.isEditMode() ) {
			isEditMode = true;
		}
		elementorFrontend.hooks.addAction('frontend/element_ready/trx_elm_posts.classic', PostsHandler);
		elementorFrontend.hooks.addAction('frontend/element_ready/trx_elm_posts.card', PostsHandler);
		elementorFrontend.hooks.addAction('frontend/element_ready/trx_elm_posts.checkerboard', PostsHandler);
		elementorFrontend.hooks.addAction('frontend/element_ready/trx_elm_posts.creative', PostsHandler);
		elementorFrontend.hooks.addAction('frontend/element_ready/trx_elm_posts.event', PostsHandler);
		elementorFrontend.hooks.addAction('frontend/element_ready/trx_elm_posts.news', PostsHandler);
		elementorFrontend.hooks.addAction('frontend/element_ready/trx_elm_posts.portfolio', PostsHandler);
		elementorFrontend.hooks.addAction('frontend/element_ready/trx_elm_posts.overlap', PostsHandler);
		elementorFrontend.hooks.addAction('frontend/element_ready/trx_elm_posts.template', PostsHandler);
	} );

} )( jQuery );
