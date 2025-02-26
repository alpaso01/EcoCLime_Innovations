(function ($) {
    var isEditMode = false;

    var WooProductsHandler = function ($scope, $) {
        var instance = null;

        instance = new TRXAddonsWooProducts($scope);
        instance.init();
    };

    window.TRXAddonsWooProducts = function ($scope) {
        var self = this,
            $elem = $scope.find(".trx-addons-woo-products"),
            skin = $scope.find('.trx-addons-woo-products').data('skin'),
            html = null,
            canLoadMore = true;

        //Check Quick View
        var isQuickView = $elem.data("quick-view");

        if ("yes" === isQuickView) {

            var widgetID = $scope.data("id"),
                $modal = $elem.siblings(".trx-addons-woo-products-quick-view-" + widgetID),
                $qvModal = $modal.find('#trx-addons-woo-products-quick-view-modal'),
                $contentWrap = $qvModal.find('#trx-addons-woo-products-quick-view-content'),
                $wrapper = $qvModal.find('.trx-addons-woo-products-content-main-wrapper'),
                $backWrap = $modal.find('.trx-addons-woo-products-quick-view-back'),
                $qvLoader = $modal.find('.trx-addons-woo-products-quick-view-loader');

        }

        self.init = function () {
            self.handleProductsCarousel();

            if ("yes" === isQuickView) {
                self.handleProductQuickView();
            }

            self.handleProductPagination();

            self.handleLoadMore();

            self.handleAddToCart();

            if ("grid-6" === skin) {
                self.handleGalleryImages();
            }

            if (["grid-7", "grid-11"].includes(skin)) {
                self.handleGalleryCarousel(skin);

                if ("grid-11" === skin) {
                    self.handleGalleryNav();
                }
            }

            if ($elem.hasClass("trx-addons-woo-products-masonry")) {
                self.handleGridMasonry();

                $(window).on("resize", self.handleGridMasonry);
            }

            // place product title above thumbnail.
            if ($scope.hasClass('trx-addons-woo-products-title-above-yes')) {
                self.handleTitlePos();
            }
        };

        self.getIsoTopeSettings = function () {
            return {
                itemSelector: "li.product",
                percentPosition: true,
                animationOptions: {
                    duration: 750,
                    easing: "linear",
                    queue: false
                },
                layoutMode: "masonry",
            }
        };

        self.handleTitlePos = function () {
            var hasTitle = $elem.find('.woocommerce-loop-product__title').length > 0 ? true : false,
                hasImg = $elem.find('.trx-addons-woo-products-product-thumbnail .woocommerce-loop-product__link img').length > 0 ? true : false;

            if (!hasTitle || !hasImg) {
                return;
            }

            var $products = $elem.find('li.product');

            $products.each(function (index, product) {

                var $title = $(product).find('.woocommerce-loop-product__title').parent(),
                    $thumbnail = $(product).find('.trx-addons-woo-products-product-thumbnail');

                $title.insertBefore($thumbnail);

            });

            $elem.find(".trx-addons-woo-products-product__link").css("opacity", 1);
        };

        self.handleProductsCarousel = function () {
            var sliderOptions = $elem.data("slider-settings");

            if (!sliderOptions)
                return;

            var carouselWrap = $scope.find('.swiper-container-wrap').eq(0),
                carousel = $elem.find('.trx-addons-woo-products-carousel').eq(0);

            if ( carousel.length > 0 ) {
                var asyncSwiper = elementorFrontend.utils.swiper;

                new asyncSwiper( carousel, sliderOptions ).then( function( newSwiperInstance ) {
                    var mySwiper = newSwiperInstance;
                    self.swiperSliderAfterInit( $scope, carousel, carouselWrap, sliderOptions, mySwiper );
                    self.swiperSliderAfterInitPrimeSlider( mySwiper );
                } );
            }
        };

        // Make the height of the visible posts in the slider equal
        self.equalHeight = function ( $scope, mySwiper ) {
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
        };

        self.swiperSliderAfterInit = function( $scope, carousel, carouselWrap, elementSettings, mySwiper ) {
            carouselWrap.addClass( 'trx-addons-slider-inited' );

            self.equalHeight( $scope, mySwiper );

            var busy = false,
                busyTimer = 0;

            $( window ).resize( trx_addons_debounce( function () {
                busy = true;
                busyTimer = setTimeout( function () {
                    busy = false;
                }, 100 );

                // Reset height of each slide to recalculate it
                $scope.find( '.trx-addons-posts-item' ).css( { height: 'auto' } );
                self.equalHeight( $scope, mySwiper );
            }, 100 ) );

            mySwiper.on( 'slideChange', function () {
                if ( ! busy ) {
                    self.equalHeight( $scope, mySwiper );
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

        self.handleGridMasonry = function () {
            var $products = $elem.find("ul.products");

            $products
                .imagesLoaded(function () { })
                .done(
                    function () {
                        $products.isotope({
                            itemSelector: "li.product",
                            percentPosition: true,
                            animationOptions: {
                                duration: 750,
                                easing: "linear",
                                queue: false
                            },
                            layoutMode: "masonry",
                            // masonry: {
                            //     columnWidth: cellSize
                            // }
                        });
                    });
        };

        self.handleProductQuickView = function () {
            $modal.appendTo(document.body);

            $elem.on('click', '.trx-addons-woo-products-qv-btn, .trx-addons-woo-products-qv-data', self.triggerQuickViewModal);

            window.addEventListener("resize", function () {
                self.updateQuickViewHeight();
            });
        };

        self.triggerQuickViewModal = function (event) {
            event.preventDefault();

            var $this = $(this),
                productID = $this.data('product-id');

            if (!$qvModal.hasClass('loading'))
                $qvModal.addClass('loading');

            if (!$backWrap.hasClass('trx-addons-woo-products-quick-view-active'))
                $backWrap.addClass('trx-addons-woo-products-quick-view-active');

            self.getProductByAjax(productID);

            self.addCloseEvents();
        };

        self.getProductByAjax = function (itemID) {
            var pageID = $elem.data('page-id');

            $.ajax({
                url: trx_addons_woo_products_script.ajax_url,
                data: {
                    action: 'trx_addons_woo_products_get_product_qv',
                    pageID: pageID,
                    elemID: $scope.data('id'),
                    product_id: itemID,
                    security: trx_addons_woo_products_script.products_qv_nonce
                },
                dataType: 'html',
                type: 'POST',
                beforeSend: function () {
                    $qvLoader.append('<div class="premium-loading-feed"><div class="premium-loader"></div></div>');
                },
                success: function (data) {
                    $qvLoader.find('.premium-loading-feed').remove();

                    $elem.trigger('qv_loaded');

                    //Insert the product content in the quick view modal.
                    $contentWrap.html(data);
                    self.handleQuickViewModal();
                },
                error: function (err) {
                    console.log(err);
                }
            });
        };

        self.addCloseEvents = function () {
            var $closeBtn = $qvModal.find('.trx-addons-woo-products-quick-view-close');

            $(document).keyup(function (e) {
                if (e.keyCode === 27)
                    self.closeModal();
            });

            $closeBtn.on('click', function (e) {
                e.preventDefault();
                self.closeModal();
            });

            $wrapper.on('click', function (e) {
                if (this === e.target)
                    self.closeModal();
            });
        };

        self.handleQuickViewModal = function () {
            $contentWrap.imagesLoaded(function () {
                self.handleQuickViewSlider();
            });
        };

        self.getBarWidth = function () {
            var div = $('<div style="width:50px;height:50px;overflow:hidden;position:absolute;top:-200px;left:-200px;"><div style="height:100px;"></div>');
            // Append our div, do our calculation and then remove it
            $('body').append(div);
            var w1 = $('div', div).innerWidth();
            div.css('overflow-y', 'scroll');
            var w2 = $('div', div).innerWidth();
            $(div).remove();

            return (w1 - w2);
        };

        self.handleQuickViewSlider = function () {
            var $productSlider = $qvModal.find('.trx-addons-woo-products-qv-image-slider'),
                newArrows      = $qvModal.find( '.trx-addons-woo-products-qv-slides-direction .trx-addons-slider-arrow' );

            if ($productSlider.find('li').length > 1) {
                newArrows.show();

                $productSlider.flexslider({
                    animation: "slide",
                    customDirectionNav: ( newArrows.length > 0 ) ? newArrows : false,
                    start: function (slider) {
                        setTimeout(function () {
                            self.updateQuickViewHeight(true, true);
                        }, 300);
                    },
                });

            } else {
                newArrows.hide();
                setTimeout(function () {
                    self.updateQuickViewHeight(true);
                }, 300);
            }

            if (!$qvModal.hasClass('active')) {
                setTimeout(function () {
                    $qvModal.removeClass('loading').addClass('active');

                    var barWidth = self.getBarWidth();

                    $("html").css('margin-right', barWidth);
                    $("html").addClass('trx-addons-woo-products-qv-opened');
                }, 350);
            }
        };

        self.updateQuickViewHeight = function (update_css, isCarousel) {
            var $quickView = $contentWrap,
                imgHeight = $quickView.find('.product .trx-addons-woo-products-qv-image-slider').first().height(),
                summary = $quickView.find('.trx-addons-woo-products-product-summary'),
                content = summary.css('content');

            if ('undefined' != typeof content && 544 == content.replace(/[^0-9]/g, '') && 0 != imgHeight && null !== imgHeight) {
                summary.css('height', imgHeight);
            } else {
                summary.css('height', '');
            }

            if (true === update_css)
                $qvModal.css('opacity', 1);

            //Make sure slider images have same height as summary.
            if (isCarousel)
                $quickView.find('.product .trx-addons-woo-products-qv-image-slider img').height(summary.outerHeight());
        };

        self.closeModal = function () {
            $backWrap.removeClass('trx-addons-woo-products-quick-view-active');

            $qvModal.removeClass('active').removeClass('loading');

            $('html').removeClass('trx-addons-woo-products-qv-opened');

            $('html').css('margin-right', '');

            setTimeout(function () {
                $contentWrap.html('');
            }, 600);
        };

        self.handleAddToCart = function () {
            $elem
                .on('click', '.instock .trx-addons-woo-products-cart-btn.product_type_simple', self.onAddCartBtnClick).on('premium_product_add_to_cart', self.handleAddCartBtnClick)
                .on('click', '.instock .trx-addons-woo-products-atc-button .button.product_type_simple', self.onAddCartBtnClick).on('premium_product_add_to_cart', self.handleAddCartBtnClick);
        };

        self.onAddCartBtnClick = function (event) {
            var $this = $(this);

            var productID = $this.data('product_id'),
                quantity = 1;

            //If current product has no defined ID.
            if (!productID)
                return;

            if ($this.parent().data("variations"))
                return;

            if (!$this.data("added-to-cart")) {
                event.preventDefault();
            } else {
                return;
            }

            $this.removeClass('added').addClass('adding');

            if (!$this.hasClass('trx-addons-woo-products-cart-btn')) {
                $this.append('<span class="trx-addons-woo-products-cart-loader fas fa-cog"></span>')
            }

            $.ajax({
                url: trx_addons_woo_products_script.ajax_url,
                type: 'POST',
                data: {
                    action: 'trx_addons_woo_products_add_product_to_cart',
                    nonce: trx_addons_woo_products_script.products_add_to_cart_nonce,
                    product_id: productID,
                    quantity: quantity,
                },
                success: function () {
                    $(document.body).trigger('wc_fragment_refresh');
                    $elem.trigger('premium_product_add_to_cart', [$this]);

                    if ('grid-10' === skin || !$this.hasClass('trx-addons-woo-products-cart-btn')) {
                        setTimeout(function () {

                            var viewCartTxt = $this.siblings('.added_to_cart').text();

                            if ('' == viewCartTxt)
                                viewCartTxt = $scope.data('woo-cart-text') || '';

                            if ('' == viewCartTxt)
                                viewCartTxt = 'View Cart';

                            $this.removeClass('add_to_cart_button').attr('href', trx_addons_woo_products_script.woo_cart_url).text(viewCartTxt);

                            $this.attr('data-added-to-cart', true);
                        }, 200);
                    }
                }
            });
        };

        self.handleAddCartBtnClick = function (event, $btn) {
            if (!$btn)
                return;

            $btn.removeClass('adding').addClass('added');
        };

        self.handleGalleryImages = function () {
            $elem.on('click', '.trx-addons-woo-products-product__gallery_image', function () {
                var $thisImg = $(this),
                    $closestThumb = $thisImg.closest(".trx-addons-woo-products-product-thumbnail"),
                    imgSrc = $thisImg.attr('src');

                if ($closestThumb.find(".trx-addons-woo-products-product__on_hover").length < 1) {
                    $closestThumb.find(".woocommerce-loop-product__link img").replaceWith($thisImg.clone(true));
                } else {
                    $closestThumb.find(".trx-addons-woo-products-product__on_hover").attr('src', imgSrc);
                }
            });
        };

        self.handleGalleryNav = function () {
            $elem.on('click', '.trx-addons-woo-products-product-gallery-images .trx-addons-woo-products-product__gallery_image', function () {

                var imgParent = $(this).parentsUntil(".trx-addons-woo-products-product-wrapper")[2],
                    sliderContainer = $(imgParent).siblings('.trx-addons-woo-products-product-thumbnail').find('.trx-addons-woo-products-product-thumbnail-slider-container').get(0).swiper,
                    imgIndex = $(this).index() + 1;

                sliderContainer.slideTo(imgIndex);
            });
        };

        self.swiperSliderAfterInitPrimeSlider = function ( mySwiper ) {
            if ( ! [ 'grid-7', 'grid-11' ].includes(skin) ) {
                return false;
            }

            mySwiper.on( 'slideChange', function () {
                self.handleGalleryCarousel();
            } );
        };

        self.handleGalleryCarousel = function (skin) {

            var products = $elem.find('.trx-addons-woo-products-product-thumbnail-wrapper'),
                scope_id = $scope.data('id');

            products.each(function (index, product) {
                var carouselWrapProduct = $(product).find('.trx-addons-woo-products-product-thumbnail-slider').eq(0);
                $imgs = $(product).find('.swiper-slide').length;

                if ( $imgs > 1 && !carouselWrapProduct.hasClass( 'trx-addons-slider-inited' ) ) {
                    var carouselProduct = $(product).find('.trx-addons-woo-products-product-thumbnail-slider-container').eq(0),
                        product_id = carouselWrapProduct.data( 'product_id' ),
                        sliderOptions = {
                            loop: ( 'grid-11' !== skin ),
                            slidesPerView: 1,
                            slidesPerGroup: 1,
                            autoplay: false,
                            speed: 500,
                            // rtl: elementorFrontend.config.is_rtl,
                        };

                    if ( 'grid-11' !== skin ) {
                        sliderOptions.navigation = {
                            nextEl: '.swiper-button-next-thumbnail-' + scope_id + '-' + product_id,
                            prevEl: '.swiper-button-prev-thumbnail-' + scope_id + '-' + product_id,
                        };
                    }

                    if ( carouselProduct.length > 0 ) {
                        var asyncSwiper = elementorFrontend.utils.swiper;

                        new asyncSwiper( carouselProduct, sliderOptions ).then( function( newSwiperInstance ) {
                            var mySwiper = newSwiperInstance;
                            self.swiperSliderAfterInit( $scope, carouselProduct, carouselWrapProduct, sliderOptions, mySwiper );
                        } );
                    }
                }
            });
        };

        self.handleLoadMore = function () {

            var $loadMoreBtn = $elem.find(".trx-addons-woo-products-load-more-btn"),
                page_number = 2,
                pageID = $elem.data('page-id');

            if ($loadMoreBtn.length < 1)
                return;

            $loadMoreBtn.on('click', function (e) {

                if (!canLoadMore)
                    return;

                canLoadMore = false;

                $elem.find('ul.products').after('<div class="premium-loading-feed"><div class="premium-loader"></div></div>');

                $loadMoreBtn.css("opacity", 0.3);

                $.ajax({
                    url: trx_addons_woo_products_script.ajax_url,
                    data: {
                        action: 'trx_addons_action_get_product',
                        pageID: pageID,
                        elemID: $scope.data('id'),
                        category: $loadMoreBtn.data("tax"),
                        orderBy: $loadMoreBtn.data("order"),
                        skin: skin,
                        page_number: page_number,
                        nonce: trx_addons_woo_products_script.products_nonce,
                    },
                    dataType: 'json',
                    type: 'POST',
                    success: function (data) {
                        html = data.data.html;

                        //If the number of coming products is 0, then remove the button.
                        var newProductsLength = $loadMoreBtn.data("products") - html.match(/<li/g).length;
                        if (newProductsLength < 1)
                            $loadMoreBtn.remove();

                        canLoadMore = true;

                        $elem.find('.premium-loading-feed').remove();
                        $loadMoreBtn.css("opacity", 1);

                        var $currentProducts = $elem.find('ul.products');

                        //Remove the wrapper <ul>
                        html = html.replace(html.substring(0, html.indexOf('>') + 1), '');
                        html = html.replace("</ul>", "");

                        $loadMoreBtn.find(".trx-addons-woo-products-num").text("(" + newProductsLength + ")");

                        $loadMoreBtn.data("products", newProductsLength);

                        $currentProducts.append(html);

                        if ($elem.hasClass("trx-addons-woo-products-masonry")) {

                            $currentProducts.isotope('reloadItems');

                            setTimeout(function () {

                                $currentProducts.isotope({
                                    itemSelector: "li.product",
                                    percentPosition: true,
                                    layoutMode: "masonry",
                                });

                            }, 100);
                        }

                        // //Trigger carousel for products in the next pages.
                        if ("grid-7" === skin || "grid-11" === skin) {
                            self.handleGalleryCarousel(skin);
                        }

                        page_number++;

                    },
                    error: function (err) {
                        console.log(err);
                    }
                });


            });
        };

        self.handleProductPagination = function () {

            $elem.on('click', '.trx-addons-woo-products-pagination a.page-numbers', function (e) {

                var $targetPage = $(this);

                if ($elem.hasClass('trx-addons-woo-products-query-main'))
                    return;

                e.preventDefault();

                $elem.find('ul.products').after('<div class="premium-loading-feed"><div class="premium-loader"></div></div>');

                var pageID = $elem.data('page-id'),
                    currentPage = parseInt($elem.find('.page-numbers.current').html()),
                    page_number = 1;

                if ($targetPage.hasClass('next')) {
                    page_number = currentPage + 1;
                } else if ($targetPage.hasClass('prev')) {
                    page_number = currentPage - 1;
                } else {
                    page_number = $targetPage.html();
                }

                $.ajax({
                    url: trx_addons_woo_products_script.ajax_url,
                    data: {
                        action: 'trx_addons_action_get_product',
                        pageID: pageID,
                        elemID: $scope.data('id'),
                        category: '',
                        skin: skin,
                        page_number: page_number,
                        nonce: trx_addons_woo_products_script.products_nonce,
                    },
                    dataType: 'json',
                    type: 'POST',
                    success: function (data) {

                        $elem.find('.premium-loading-feed').remove();

                        $('html, body').animate({
                            scrollTop: (($scope.find('.trx-addons-woo-products').offset().top) - 100)
                        }, 'slow');

                        var $currentProducts = $elem.find('ul.products');

                        $currentProducts.replaceWith(data.data.html);

                        $elem.find('.trx-addons-woo-products-pagination').replaceWith(data.data.pagination);

                        //Trigger carousel for products in the next pages.
                        if ("grid-7" === skin || "grid-11" === skin) {
                            self.handleGalleryCarousel(skin);
                        }

                        if ($elem.hasClass("trx-addons-woo-products-masonry"))
                            self.handleGridMasonry();

                    },
                    error: function (err) {
                        console.log(err);
                    }
                });

            });

        };
    };


    //Elementor JS Hooks.
    $(window).on("elementor/frontend/init", function () {
        if ( elementorFrontend.isEditMode() ) {
			isEditMode = true;
		}
        elementorFrontend.hooks.addAction("frontend/element_ready/trx_elm_woo_products.grid-1", WooProductsHandler);
        elementorFrontend.hooks.addAction("frontend/element_ready/trx_elm_woo_products.grid-2", WooProductsHandler);
        elementorFrontend.hooks.addAction("frontend/element_ready/trx_elm_woo_products.grid-3", WooProductsHandler);
        elementorFrontend.hooks.addAction("frontend/element_ready/trx_elm_woo_products.grid-4", WooProductsHandler);
        elementorFrontend.hooks.addAction("frontend/element_ready/trx_elm_woo_products.grid-5", WooProductsHandler);
        elementorFrontend.hooks.addAction("frontend/element_ready/trx_elm_woo_products.grid-6", WooProductsHandler);
        elementorFrontend.hooks.addAction("frontend/element_ready/trx_elm_woo_products.grid-7", WooProductsHandler);
        elementorFrontend.hooks.addAction("frontend/element_ready/trx_elm_woo_products.grid-8", WooProductsHandler);
        elementorFrontend.hooks.addAction("frontend/element_ready/trx_elm_woo_products.grid-9", WooProductsHandler);
        elementorFrontend.hooks.addAction("frontend/element_ready/trx_elm_woo_products.grid-10", WooProductsHandler);
        elementorFrontend.hooks.addAction("frontend/element_ready/trx_elm_woo_products.grid-11", WooProductsHandler);
    });
})(jQuery);