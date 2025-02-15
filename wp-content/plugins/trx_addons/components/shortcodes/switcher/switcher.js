/**
 * Shortcode Switcher
 *
 * @package ThemeREX Addons
 * @since v2.6.0
 */

/* global jQuery, TRX_ADDONS_STORAGE */


jQuery( document ).on( 'action.init_hidden_elements', function() {

	"use strict";

	jQuery( '.sc_switcher:not(.sc_switcher_inited)' ).each( function() {

		var $self = jQuery( this ).addClass( 'sc_switcher_inited' ),
			$slider = $self.find( '.sc_switcher_slider' ),
			$sections_wrap = $self.find( '.sc_switcher_sections' ),
			$sections = $self.find( '.sc_switcher_section' );

		// Move sections with specified ids inside the scroller if type="section" and a sections are not moved on the server side
		if ( typeof elementorFrontend == 'undefined' || ! elementorFrontend.isEditMode() ) {
			$sections.each( function() {
				var $section = jQuery( this ),
					id = $section.data( 'section' );
				if ( id && $section.html().length < 2 ) {
					$section.empty().append( jQuery( '#' + id ) );
				}
				$section.addClass('sc_switcher_section_inited');
			} );
		}

		// Type 'Default' or 'Modern'
		if ( $self.hasClass( 'sc_switcher_default' ) || $self.hasClass( 'sc_switcher_modern' ) ) {
			var $toggle = $self.find( '.sc_switcher_controls_toggle' ),
				$toggle_button = $self.find( '.sc_switcher_controls_toggle_button' );

			// Click on toggle
			$toggle.on( 'click', function() {
				sc_switcher_toggle_state(0);
			} );
			// Click on the left title
			$self.find('.sc_switcher_controls_section1').on( 'click', function() {
				sc_switcher_toggle_state(1);
			} );
			// Click on the right title
			$self.find('.sc_switcher_controls_section2').on( 'click', function() {
				sc_switcher_toggle_state(2);
			} );

		// Type 'Tabs'
		} else {
			var $tabs = $self.find( '.sc_switcher_tab' );
			$tabs.find( '.sc_switcher_tab_link' ).on( 'click', function( e ) {
				var $tab = jQuery( this ).parent(),
					idx = $tab.index();
				$tabs.removeClass( 'sc_switcher_tab_active' );
				$tab.addClass( 'sc_switcher_tab_active' );
				$sections
					.removeClass( 'sc_switcher_section_active' )
					.eq( idx ).addClass( 'sc_switcher_section_active' );
				$slider.get(0).style.setProperty( '--trx-addons-switcher-slide-active', idx );
				sc_switcher_change_height();
				e.preventDefault();
				return false;
			} );
		}

		// Set the width of the slider equal to the width of the active section
		sc_switcher_set_slider_width();

		// Change height of the shortcode container to the height of the active section
		sc_switcher_change_height();

		// Resize action
		jQuery( document ).on( 'action.resize_trx_addons', function() {
			sc_switcher_set_slider_width();
			sc_switcher_change_height();
		} );

		// Toggle state (for types 'Default' and 'Modern')
		function sc_switcher_toggle_state( state ) {
			if ( $toggle.hasClass( 'sc_switcher_controls_toggle_on' ) ) {
				if ( state === 0 || state == 2 ) {
					$self.removeClass( 'sc_switcher_toggle_on' );
					$toggle.removeClass( 'sc_switcher_controls_toggle_on' );
					$sections.eq(0).removeClass( 'sc_switcher_section_active' );
					$sections.eq(1).addClass( 'sc_switcher_section_active' );
					//$slider.animate( { left: '50%' }, 300 );
					$slider.get(0).style.setProperty( '--trx-addons-switcher-slide-active', 1 );
					sc_switcher_change_height();
				}
			} else {
				if ( state === 0 || state == 1 ) {
					$self.addClass( 'sc_switcher_toggle_on' );
					$toggle.addClass( 'sc_switcher_controls_toggle_on' );
					$sections.eq(0).addClass( 'sc_switcher_section_active' );
					$sections.eq(1).removeClass( 'sc_switcher_section_active' );
					$slider.get(0).style.setProperty( '--trx-addons-switcher-slide-active', 0 );
					sc_switcher_change_height();
				}				
			}
			sc_switcher_set_slider_width();
		}

		// If a widget type is 'modern' - resize slider after toggle and make its width equal to the width of the active section
		function sc_switcher_set_slider_width() {
			if ( $self.hasClass( 'sc_switcher_modern' ) && $self.hasClass( 'sc_switcher_autowidth' ) ) {
				// Set a width of the first control as a translate offset for the toggle button
				$toggle_button.get(0).style.setProperty( '--trx-addons-switcher-toggle-offset', $self.find( '.sc_switcher_controls_section1' ).outerWidth() + 'px' );
				// Set a width of the slider equal to the width of the active section
				var idx = $toggle.hasClass( 'sc_switcher_controls_toggle_on' ) ? 1 : 2,
					cw = $self.find( '.sc_switcher_controls_section' + idx ).outerWidth();
				$toggle_button.width( cw );
			}
		}

		// Change height of the shortcode container to the height of the active section
		function sc_switcher_change_height() {
			var $active = $sections.filter( '.sc_switcher_section_active' );
			if ( $active.length > 0 ) {
				$sections_wrap.css( 'height', $active.outerHeight() );
			}
		}

	} );

} );