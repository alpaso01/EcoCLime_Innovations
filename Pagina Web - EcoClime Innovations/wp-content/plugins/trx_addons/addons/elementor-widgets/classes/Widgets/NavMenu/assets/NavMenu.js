(function ($) {

	"use strict";

	var NavMenuHandler = function( $scope, $ ) {

		var $widgetContainer = $scope.find('.trx-addons-nav-widget-container');

		// we don't need to wait for content dom load since the script is loaded in the footer.
		// $widgetContainer.removeClass('trx-addons-invisible');

		if ( ! elementorFrontend.isEditMode() ) {
			$widgetContainer.css({ visibility: 'inherit', opacity: 'inherit' });
		}

		var settings = $widgetContainer.data('settings');

		if ( ! settings ) {
			return;
		}

		var $menuContainer = $scope.find('.trx-addons-mobile-menu'),
			$menuToggler = $scope.find('.trx-addons-hamburger-toggle'),
			$hamMenuCloser = $scope.find('.trx-addons-mobile-menu-close'),
			$centeredItems = $scope.find('.trx-addons-mega-content-centered'),
			$fullWidthItems = $scope.find('.trx-addons-nav-menu-container').find('li[data-full-width="true"],li[class*="trx_addons_stretch_"]'),
			disablePageScroll = $scope.hasClass('trx-addons-disable-scroll-yes') ? true : false,
			delay = getComputedStyle( $scope[0] ).getPropertyValue( '--trx-addons-mega-menu-delay' ) || 300,
			hoverTimeout;

		// Get Element On Page Option
		$scope.find('div[data-mega-content]').each( function( index, elem ) {
			var $currentItem = $(elem),
				targetElement = $currentItem.data('mega-content');
			if ( $(targetElement).length > 0 ) {
				var $targetElement = $(targetElement);
				$targetElement.attr( 'data-menu-id', $scope.data('id') );
				$currentItem.append( $targetElement.clone(true).addClass('trx-addons-cloned-element') );
			}
		} );

		// Remove Element On Page Option If on Frontend
		if ( ! elementorFrontend.isEditMode() ) {
			$('div[data-menu-id="' + $scope.data('id') + '"]').not('.trx-addons-cloned-element').remove();
		}

		/**
		 * Save current device to use it later to determine if the device changed on resize.
		 */
		// window.trxCurrStickyDevice = elementorFrontend.getCurrentDeviceMode();

		// make sure it's removed when the option is disabled.
		if ( elementorFrontend.isEditMode() && ! disablePageScroll ) {
			$('body').removeClass('trx-addons-scroll-disabled');
		}

		$centeredItems.each( function( index, item ) {
			$(item).closest(".trx-addons-nav-menu-item").addClass("trx-addons-mega-item-static");
		} );

		if ( 'slide' === settings.mobileLayout || 'slide' === settings.mainLayout ) {
			$scope.addClass('trx-addons-ver-hamburger-menu');
		}

		// check badges dot/grow effect.
		if ( 'dot' === settings.hoverEffect ) {
			var $badgedItems = $scope.find('.trx-addons-mega-content-container .trx-addons-badge-dot, .trx-addons-sub-menu .trx-addons-badge-dot');
			$badgedItems.each( function( index, $item ) {
				$( $item )
					.mouseenter( function() {
						$( $item ).removeClass('trx-addons-badge-dot');
					} )
					.mouseleave( function() {
						$($item).addClass('trx-addons-badge-dot');
					} );
			} );
		}

		// close mobile menu after clicking.
		if ( settings.closeAfterClick ) {
			$menuContainer.find('.trx-addons-menu-link').on( 'click.trxAfterClick', function () {
				// check if it has children
				var hasChildern = itemHasChildren( this );

				if ( ! hasChildern ) {
					// close mobile menu
					if ( 'slide' === settings.mainLayout || 'slide' === settings.mobileLayout ) {
						// if ($scope.hasClass('trx-addons-nav-slide')) {
						$hamMenuCloser.trigger( 'click' );
					} else {
						$menuToggler.trigger( 'click' );
					}
				}
			} );
		}

		var isMobileMenu = null,
			isDesktopMenu = null;

		checkBreakPoint( settings );

		if ( $scope.hasClass('trx-addons-nav-hor') ) {
			$(window).trigger( 'resize' );
			// Check dimensions after all other handlers are done.
			setTimeout( checkMegaContentWidth, 100 );
		}

		// checkStickyEffect();

		if ( ['hor', 'ver'].includes( settings.mainLayout ) ) {

			if ( 'hover' === settings.submenuEvent ) {

				$scope.find('.trx-addons-nav-menu-item').on( 'mouseenter.trxItemHover', function(e) {
					e.stopPropagation();
					clearTimeout( hoverTimeout );
					$(this).siblings().removeClass('trx-addons-item-hovered'); // unset hovered items only for this menu.
					$(this).addClass('trx-addons-item-hovered');
					if ( $(this).hasClass('trx-addons-sub-menu-item') ) {
						$(this).parents('.trx-addons-nav-menu-item').addClass('trx-addons-item-hovered');
					}
					initHiddenElements( $(this) );
				} );

				$scope.on('mouseleave.trxItemHover', function(e) {
					hoverTimeout = setTimeout( function () {
						$scope.find('.trx-addons-item-hovered').removeClass('trx-addons-item-hovered');
					}, delay);
				} );

				// we need to make sure that trx-addons-item-hover is not removed when hovering over a sub/mega menu.
				$scope.find('.trx-addons-sub-menu, .trx-addons-mega-content-container')
					.on( 'mouseenter.trxItemHover', function(e) {
						var $menuItem = $(this).parents('.trx-addons-nav-menu-item').first();
						clearTimeout( hoverTimeout );
						$menuItem.siblings().removeClass('trx-addons-item-hovered'); // remove it from the menu item in the same widget only
						$menuItem.addClass('trx-addons-item-hovered');
						initHiddenElements( $menuItem );
					} )
					.on( 'mouseleave.trxItemHover', function(e) {
						clearTimeout( hoverTimeout );
						// $(this).parents('.trx-addons-nav-menu-item').first().removeClass('trx-addons-item-hovered');
					} );

			} else { // click

				var triggerSelector = 'item' === settings.submenuTrigger ? ' > .trx-addons-menu-link' : ' > .trx-addons-menu-link > .trx-addons-dropdown-icon',
					$trigger = $scope.find('.trx-addons-nav-menu-container .trx-addons-nav-menu-item.menu-item-has-children' + triggerSelector);

				// To prevent events overlapping if the user switched between hover/click while building the menu.
				if ( elementorFrontend.isEditMode() ) {
					$scope.off( 'mouseleave.trxItemHover' );
				}

				$trigger.off( 'click.trxItemClick' ); // to prevent duplications.
				$trigger.on( 'click.trxItemClick', function(e) {
					e.preventDefault();
					e.stopPropagation();
					var $menuItem = $(this).parents('.trx-addons-nav-menu-item').first();
					// remove it from the menu item in the same widget only
					$menuItem.siblings().removeClass('trx-addons-item-hovered').find('.trx-addons-item-hovered').removeClass('trx-addons-item-hovered');
					$menuItem.toggleClass('trx-addons-item-hovered');
					initHiddenElements( $menuItem );
				} );
			}
		}

		// Close the menu when clicking outside of it (on the overlay).
		$(document).on('click', '.trx-addons-nav-slide-overlay', function() {
			$scope.find('.trx-addons-mobile-menu-outer-container, .trx-addons-nav-slide-overlay').removeClass('trx-addons-vertical-toggle-open');
			$('body').removeClass('trx-addons-scroll-disabled');
			$menuToggler.removeClass('trx-addons-toggle-opened'); // show/hide close icon/text.
		} );

		// Close the menu when clicking on the close button.
		$hamMenuCloser.on( 'click', function() {
			$scope.find('.trx-addons-mobile-menu-outer-container, .trx-addons-nav-slide-overlay').removeClass('trx-addons-vertical-toggle-open');
			$('body').removeClass('trx-addons-scroll-disabled');
			$menuToggler.removeClass('trx-addons-toggle-opened'); // show/hide close icon/text.
		} );

		// Toggle the menu when clicking on the hamburger icon.
		$menuToggler.on( 'click', function () {
			if ('slide' === settings.mobileLayout || 'slide' === settings.mainLayout) {
				$scope.find('.trx-addons-mobile-menu-outer-container, .trx-addons-nav-slide-overlay').addClass('trx-addons-vertical-toggle-open');

				if (disablePageScroll) {
					$('body').addClass('trx-addons-scroll-disabled');
				}
			} else {
				// $menuContainer.toggleClass('trx-addons-active-menu');
				if ($($menuContainer).hasClass('trx-addons-active-menu')) {
					$scope.find('.trx-addons-mobile-menu-container').slideUp('slow', function () {
						$menuContainer.removeClass('trx-addons-active-menu');
						$scope.find('.trx-addons-mobile-menu-container').show();
					});
				} else {

					$menuContainer.addClass('trx-addons-active-menu');
				}
			}

			$menuToggler.toggleClass('trx-addons-toggle-opened'); // show/hide close icon/text.
		} );

		$menuContainer
			.find('.trx-addons-nav-menu-item.menu-item-has-children a, .trx-addons-mega-nav-item a')
			.on('click', function (e) {
				if ( $(this).find(".trx-addons-dropdown-icon").length < 1 ) {
					return;
				}
				var $parent = $(this).parent(".trx-addons-nav-menu-item");
				e.stopPropagation();
				e.preventDefault();
				//If it was opened, then close it.
				if ( $parent.hasClass('trx-addons-active-menu') ) {
					$parent.toggleClass('trx-addons-active-menu');
				} else {
					//Close any other opened items.
					$menuContainer.find('.trx-addons-active-menu').toggleClass('trx-addons-active-menu');
					//Then, open this item.
					$parent.toggleClass('trx-addons-active-menu');
					initHiddenElements( $parent );
					// make sure the parent node is always open whenever the child node is opened.
					$($parent).parents('.trx-addons-nav-menu-item.menu-item-has-children').toggleClass('trx-addons-active-menu');
				}
			} );

		$(document).on('click.trxCloseMegaMenu', function (event) {
			var isTabsItem = $(event.target).closest('.trx-addons-tabs-nav-list-item').length,
				isWidgetContainer = $(event.target).closest('.trx-addons-nav-widget-container').length;

			if ( ! isWidgetContainer && ! isTabsItem ) {
				if ( $( $menuContainer ).hasClass('trx-addons-active-menu') ) {
					$menuToggler.trigger( 'click' );
				}
				if ( 'click' === settings.submenuEvent ) {
					$scope.find('.trx-addons-nav-menu-container .trx-addons-item-hovered').removeClass('trx-addons-item-hovered')
				}
			}
		} );

		$(window).on('resize', function () {

			checkBreakPoint( settings );

			if ( $scope.hasClass('trx-addons-nav-hor') ) {
				checkMegaContentWidth();
			}
		} );

		// vertical toggler.
		if ( $scope.hasClass('trx-addons-ver-toggle-yes') && $scope.hasClass('trx-addons-ver-click') ) {
			$scope.find('.trx-addons-ver-toggler').on('click', function() {
				$widgetContainer.toggleClass( 'trx-addons-ver-collapsed', 500 );
			} );
		}

		$widgetContainer.addClass( 'trx-addons-nav-menu-inited' );

		//-------------------------------
		//  Helper Functions
		//-------------------------------

		// Call action.init_hidden_elements to initialize hidden elements.
		function initHiddenElements( $item ) {
			$(document).trigger( 'action.init_hidden_elements', [ $item ] );
			// setTimeout( function() {
			// }, 400 );
		}

		// Set menu items to full width
		function checkMegaContentWidth() {
			$fullWidthItems.each( function( index, item ) {
				fullWidthContent( $(item).find('>ul') );
			} );
		}

		// Full Width Mega Content.
		function fullWidthContent( $item ) {
			if ( typeof window.trx_addons_stretch_submenu !== 'undefined' ) {
				window.trx_addons_stretch_submenu( $item );
			} else {
				var isContainer = elementorFrontend.config.experimentalFeatures.container,
					$parentSec = $scope.parents('.e-con').last();

				$parentSec = ! isContainer || $parentSec.length < 1 ? $scope.closest('.elementor-top-section') : $parentSec;

				var width = $parentSec.outerWidth(),
					sectionLeft = $parentSec.offset().left - $item.offset().left;

				$($item).removeClass('trx-addons-mega-item-static').find('.trx-addons-mega-content-container, > .trx-addons-sub-menu').css({
					width: width + 'px',
					left: sectionLeft + 'px',
				} );
			}
		}

		function checkBreakPoint( settings ) {

			// Trigger small screen menu.
			if ( settings.breakpoint >= $(window).outerWidth() && ! isMobileMenu ) {
				// remove the vertical toggler.
				$scope.find('.trx-addons-ver-toggler').css('display', 'none');
				$scope.addClass('trx-addons-hamburger-menu');
				$scope.find('.trx-addons-active-menu').removeClass('trx-addons-active-menu');
				// stretchDropdown( $scope.find('.trx-addons-stretch-dropdown .trx-addons-mobile-menu-container') );

				isMobileMenu = true;
				isDesktopMenu = false;

			// Trigger large screen menu.
			} else if ( settings.breakpoint < $(window).outerWidth() && ! isDesktopMenu ) {

				// show the vertical toggler if enabled.
				if ( $scope.hasClass('trx-addons-ver-toggle-yes') ) {
					$scope.find('.trx-addons-ver-toggler').css('display', 'flex');
				}

				$menuToggler.removeClass('trx-addons-toggle-opened');
				$scope.find(".trx-addons-mobile-menu-container .trx-addons-active-menu").removeClass("trx-addons-active-menu");
				$scope.removeClass('trx-addons-hamburger-menu trx-addons-ham-dropdown');
				$scope.find('.trx-addons-vertical-toggle-open').removeClass('trx-addons-vertical-toggle-open');
				$scope.find('.trx-addons-nav-default').removeClass('trx-addons-nav-default');

				isDesktopMenu = true;
				isMobileMenu = false;
			}

			// Stretch dropdown menu.
			if ( $scope.hasClass( 'trx-addons-hamburger-menu' ) || $scope.hasClass( 'trx-addons-nav-dropdown' ) ) {
				stretchDropdown( $scope.find('.trx-addons-stretch-dropdown .trx-addons-mobile-menu-container') );
			}

		}

		// Full Width Option. Shows the mobile menu beneath the widget's parent(section).
		function stretchDropdown( $menu ) {

			if ( ! $menu.length ) {
				return;
			}

			var isContainer = elementorFrontend.config.experimentalFeatures.container,
				$parentSec = $scope.parents('.e-con').last();

			$parentSec = ! isContainer || $parentSec.length < 1 ? $scope.closest('.elementor-top-section') : $parentSec;

			var width = Math.min( trx_addons_window_width(), $($parentSec).outerWidth() ),
				widgetTop = $scope.offset().top,
				parentBottom = $($parentSec).offset().top + $($parentSec).outerHeight(),
				stretchTop = parentBottom - widgetTop,
				stretchLeft = $scope.offset().left - Math.max( 0, $($parentSec).offset().left );
			
			$( $menu ).css({
				width: width + 'px',
				left: '-' + stretchLeft + 'px',
				top: stretchTop + 'px',
			} );
		}

		/**
		 * Check if the item has children.
		 * 
		 * @param {link} $item .trx-addons-menu-link
		 * 
		 * @returns boolean  true if the item has children.
		 */
		function itemHasChildren( $item ) {
			return $( $item ).parent( '.trx-addons-nav-menu-item' ).hasClass( 'menu-item-has-children' );
		}
	};

	$( window ).on( 'elementor/frontend/init', function () {
		elementorFrontend.hooks.addAction('frontend/element_ready/trx_elm_nav_menu.default', NavMenuHandler);
	} );

}( jQuery ) );