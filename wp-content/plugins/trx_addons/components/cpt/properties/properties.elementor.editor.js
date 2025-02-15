/* global jQuery, elementor */

jQuery( document ).ready( function() {
	"use strict";

	var editing_el = false,	// A current editing element
		pmv = false;		// Store panel, model, view to use it when tabs are clicked

	// Refresh states list when country is changed in Elementor editor
	jQuery( '#elementor-panel' )
		.on( 'change.trx_addons_refresh_list', 'select[data-setting="properties_country"]', function () {
			var $slave_fld = jQuery(this).parents('.elementor-control').next().find('select[data-setting="properties_state"]');
			if ( $slave_fld.length > 0 ) {
				var $slave_lbl = $slave_fld.parents('.elementor-control').find('label.elementor-control-title'),
					editing_obj = editing_el;
				trx_addons_refresh_list( 'states', jQuery(this).val(), $slave_fld, $slave_lbl, true, editing_obj );
			}
			return false;
		} );

	// Refresh cities list when state is changed in Elementor editor
	jQuery( '#elementor-panel' )
		.on( 'change.trx_addons_refresh_list', 'select[data-setting="properties_state"]', function () {
			var $slave_fld = jQuery(this).parents('.elementor-control').next().find('select[data-setting="properties_city"]');
			if ( $slave_fld.length > 0 ) {
				var $slave_lbl = $slave_fld.parents('.elementor-control').find('label.elementor-control-title'),
					editing_obj = editing_el,
					country_val = jQuery(this).parents('.elementor-control').prev().find('select').val();
				trx_addons_refresh_list( 'cities', { 'state': jQuery(this).val(), 'country': country_val }, $slave_fld, $slave_lbl, true, editing_obj );
			}
			return false;
		} );

	// Refresh neighborhoods list when city is changed in Elementor editor
	jQuery( '#elementor-panel' )
		.on( 'change.trx_addons_refresh_list', 'select[data-setting="properties_city"]', function () {
			var $slave_fld = jQuery(this).parents('.elementor-control').next().find('select[data-setting="properties_neighborhood"]');
			if ( $slave_fld.length > 0 ) {
				var $slave_lbl = $slave_fld.parents('.elementor-control').find('label.elementor-control-title'),
					editing_obj = editing_el;
				trx_addons_refresh_list( 'neighborhoods', jQuery(this).val(), $slave_fld, $slave_lbl, true, editing_obj );
			}
			return false;
		} );

	// Add Elementor's hooks and elements
	if ( window.elementor !== undefined && window.elementor.hooks !== undefined ) {
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
					trx_addons_elementor_open_panel(pmv.panel, pmv.model, pmv.view, true);
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
			var country_fld = panel.content.$el.find( 'select[data-setting="properties_country"]' ),
				state_fld = panel.content.$el.find( 'select[data-setting="properties_state"]' ),
				city_fld = panel.content.$el.find( 'select[data-setting="properties_city"]' ),
				neighborhood_fld = panel.content.$el.find( 'select[data-setting="properties_neighborhood"]' );
			// If this widget haven't required fields - exit
			if ( country_fld.length === 0 || state_fld.length === 0 || city_fld.length === 0 || neighborhood_fld.length === 0 ) {
				return;
			}
			// If list of taxonomies is incorrect - trigger event 'change' to refresh it
			var state_val = model.getSetting( state_fld.data('setting') ),
				city_val = model.getSetting( city_fld.data('setting') ),
				neighborhood_val = model.getSetting( neighborhood_fld.data('setting') );
			// If list of taxonomies is correct - exit
			if ( state_fld.find( 'option[value="' + state_val + '"],option[value="' + state_val + ' "]' ).length === 0
				|| city_fld.find( 'option[value="' + city_val + '"],option[value="' + city_val + ' "]' ).length === 0
				|| neighborhood_fld.find( 'option[value="' + neighborhood_val + '"],option[value="' + neighborhood_val + ' "]' ).length === 0
			) {
				country_fld.trigger( 'change.trx_addons_refresh_list' );
			}
		}
	}
} );