/* global jQuery, elementor */

jQuery( document ).ready( function() {
	"use strict";

	var editing_el = false,	// A current editing element
		pmv = false;		// Store panel, model, view to use it when tabs are clicked

	// Refresh competitions list when country is changed in Elementor editor
	jQuery( '#elementor-panel' )
		.on( 'change.trx_addons_refresh_list', 'select[data-setting="sport"]', function () {
			var $slave_fld = jQuery(this).parents('.elementor-control').next().find('select[data-setting="competition"]');
			if ( $slave_fld.length > 0) {
				var $slave_lbl = $slave_fld.parents('.elementor-control').find('label.elementor-control-title'),
					editing_obj = editing_el;
				trx_addons_refresh_list( 'competitions', jQuery(this).val(), $slave_fld, $slave_lbl, false, editing_obj );
			}
			return false;
		});

	// Refresh rounds list when state is changed in Elementor editor
	jQuery( '#elementor-panel' )
		.on( 'change.trx_addons_refresh_list', 'select[data-setting="competition"]', function () {
			var $slave_fld = jQuery(this).parents('.elementor-control').next().find('select[data-setting="round"]');
			if ( $slave_fld.length > 0 ) {
				var $slave_lbl = $slave_fld.parents('.elementor-control').find('label.elementor-control-title'),
					editing_obj = editing_el;
				trx_addons_refresh_list( 'rounds', jQuery(this).val(), $slave_fld, $slave_lbl, false, editing_obj );
			}
			return false;
		} );

	// Add Elementor's hooks and elements
	if (window.elementor !== undefined && window.elementor.hooks !== undefined) {
		// Add hook on panel open
		elementor.hooks.addAction( 'panel/open_editor/widget', trx_addons_elementor_open_panel );
	}

	// Add hooks on routes after tabs switched (instead of click on tabs - not work in the new Elementor version!)
	if ( window.top.$e !== undefined && window.top.$e.routes !== undefined ) {
		window.top.$e.routes.on( 'run:after', function ( component, route, args ) {
			if ( route.indexOf( 'panel/editor/' ) === 0 ) {
				if ( ! editing_el || editing_el.view.cid != args.view.cid ) {
					editing_el = args;
					pmv = false;
				}
				if ( pmv !== false ) {
					trx_addons_elementor_open_panel( pmv.panel, pmv.model, pmv.view, true );
				}
			}
		} );
	} else {
		jQuery( '#elementor-panel' )
			.on( 'click', '.elementor-panel-navigation-tab', function() {
				if ( pmv !== false ) {
					trx_addons_elementor_open_panel( pmv.panel, pmv.model, pmv.view, true );
				}
			} );
	}

	// Store taxonomies and terms to restore it when shortcode params open again
	function trx_addons_elementor_open_panel( panel, model, view, tab_chg ) {
		if ( panel.content !== undefined ) {
			// Save panel, model, view to use it when tabs are clicked
			if ( ! tab_chg ) {
				pmv = { 'panel': panel, 'model': model, 'view': view };
			}
			var sport_fld = panel.content.$el.find( 'select[data-setting="sport"]' ),
				competition_fld = panel.content.$el.find( 'select[data-setting="competition"]' ),
				round_fld = panel.content.$el.find( 'select[data-setting="round"]' );
			// If this widget haven't required fields - exit
			if ( sport_fld.length === 0 || competition_fld.length === 0 || round_fld.length === 0 ) {
				return;
			}
			// If list of taxonomies is incorrect - trigger event 'change' to refresh it
			var competition_val = model.getSetting( competition_fld.data('setting') ),
				round_val = model.getSetting( round_fld.data('setting') );
			if ( competition_fld.find( 'option[value="' + competition_val + '"],option[value="' + competition_val + ' "]').length === 0
				|| round_fld.find( 'option[value="' + round_val + '"],option[value="' + round_val + ' "]').length === 0
			) {
				sport_fld.trigger( 'change.trx_addons_refresh_list' );
			}
		}
	}
} );
