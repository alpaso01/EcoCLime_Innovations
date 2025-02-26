/* global jQuery, elementor */

jQuery( document ).ready( function() {
	"use strict";

	var editing_el = false,	// A current editing element
		pmv = false;		// Store panel, model, view to use it when tabs are clicked

	// Refresh models list when maker is changed in Elementor editor
	jQuery( '#elementor-panel' )
		.on( 'change.trx_addons_refresh_list', 'select[data-setting="cars_maker"]', function () {
			var $model_fld = jQuery(this).parents('.elementor-control').next().find('select[data-setting="cars_model"]');
			if ( $model_fld.length > 0 ) {
				var $model_lbl = $model_fld.parents('.elementor-control').find('label.elementor-control-title'),
					editing_obj = editing_el;
				trx_addons_refresh_list( 'models', jQuery(this).val(), $model_fld, $model_lbl, true, editing_obj );
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
			.on( 'click', '.elementor-panel-navigation-tab span', function() {
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
			var tax_fld = panel.content.$el.find( 'select[data-setting="cars_maker"]' );
			var terms_fld = panel.content.$el.find( 'select[data-setting="cars_model"]' );
			// If this widget haven't fields 'cars_maker' or 'cars_model' - exit
			if ( tax_fld.length === 0 || terms_fld.length === 0) {
				return;
			}
			// If list of taxonomies is incorrect - trigger event 'change' to refresh it
			var terms_val = model.getSetting( terms_fld.data('setting') );
			if ( terms_fld.find( 'option[value="' + terms_val + '"],option[value="' + terms_val + ' "]').length === 0 ) {
				tax_fld.trigger( 'change.trx_addons_refresh_list' );
			}
		}
	}
} );