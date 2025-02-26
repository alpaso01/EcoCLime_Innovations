jQuery(document).ready(function() {
	'use strict';

	window.trx_addons_ai_helper_company_generator = function( action, rez ) {
		if ( typeof rez.data == 'undefined' || typeof rez.data.fields == 'undefined' ) {
			alert( TRX_ADDONS_STORAGE['elm_ai_company_generator_bad_data'] );
			return;
		}
		var fields = rez.data.fields.organization_model ? rez.data.fields.organization_model : rez.data.fields,
			i;
		// Company name
		if ( fields.company_name ) {
			jQuery('input[name="trx_addons_options_field_ai_helper_company_name"]').val( fields.company_name ).trigger('change');
		}
		// Industry
		if ( fields.industry ) {
			jQuery('input[name="trx_addons_options_field_ai_helper_industry"]').val( fields.industry ).trigger('change');
		}
		// Contacts
		if ( fields.contacts ) {
			// Address
			if ( fields.contacts.address ) {
				jQuery('input[name="trx_addons_options_field_ai_helper_company_address"]').val( fields.contacts.address ).trigger('change');
			}
			// Phone
			if ( fields.contacts.phone ) {
				jQuery('input[name="trx_addons_options_field_ai_helper_company_phone"]').val( fields.contacts.phone ).trigger('change');
			}
			// Email
			if ( fields.contacts.email ) {
				jQuery('input[name="trx_addons_options_field_ai_helper_company_email"]').val( fields.contacts.email ).trigger('change');
			}
		}
		// Description
		if ( fields.description ) {
			jQuery('textarea[name="trx_addons_options_field_ai_helper_company_description"]').val( fields.description ).trigger('change');
		}
		// Mission
		if ( fields.mission ) {
			jQuery('textarea[name="trx_addons_options_field_ai_helper_company_mission"]').val( fields.mission ).trigger('change');
		}
		// History
		if ( fields.history ) {
			jQuery('textarea[name="trx_addons_options_field_ai_helper_company_history"]').val( fields.history ).trigger('change');
		}
		// Values
		if ( fields.values ) {
			jQuery('textarea[name="trx_addons_options_field_ai_helper_company_values"]').val( fields.values.join( "\n" ) ).trigger('change');
		}
		// Services (group fields)
		if ( fields.services && fields.services.length > 0 ) {
			var $services = jQuery('input[name^="trx_addons_options_field_ai_helper_company_services["]').eq(0).parents('.trx_addons_options_group');
			if ( $services.length ) {
				// Remove all existing fields
				var $delete = $services.find('.trx_addons_options_clone_control_delete');
				if ( $delete.length > 1 ) {
					for ( i = $delete.length - 1; i > 0; i-- ) {
						$delete.eq(i).trigger('click');
					}
				}
				// Add new fields
				for ( i = 0; i < fields.services.length; i++ ) {
					if ( i > 0 ) {
						$services.find('.trx_addons_options_clone_button_add').trigger('click');
					}
					$services.find('[name="trx_addons_options_field_ai_helper_company_services[' + i + '][name]"]').eq(0).val( fields.services[i].service_name ).trigger('change');
					$services.find('[name="trx_addons_options_field_ai_helper_company_services[' + i + '][description]"]').eq(0).val( fields.services[i].service_description ).trigger('change');
					$services.find('[name="trx_addons_options_field_ai_helper_company_services[' + i + '][features]"]').eq(0).val( fields.services[i].service_features.join( "\n" ) ).trigger('change');
				}
			}
		}
		// Team (group fields)
		if ( fields.team && fields.team.length > 0 ) {
			var $team = jQuery('input[name^="trx_addons_options_field_ai_helper_company_team["]').eq(0).parents('.trx_addons_options_group');
			if ( $team.length ) {
				// Remove all existing fields
				var $delete = $team.find('.trx_addons_options_clone_control_delete');
				if ( $delete.length > 1 ) {
					for ( i = $delete.length - 1; i > 0; i-- ) {
						$delete.eq(i).trigger('click');
					}
				}
				// Add new fields
				for ( i = 0; i < fields.team.length; i++ ) {
					if ( i > 0 ) {
						$team.find('.trx_addons_options_clone_button_add').trigger('click');
					}
					$team.find('[name="trx_addons_options_field_ai_helper_company_team[' + i + '][name]"]').eq(0).val( fields.team[i].name ).trigger('change');
					$team.find('[name="trx_addons_options_field_ai_helper_company_team[' + i + '][position]"]').eq(0).val( fields.team[i].position ).trigger('change');
					$team.find('[name="trx_addons_options_field_ai_helper_company_team[' + i + '][bio]"]').eq(0).val( fields.team[i].bio ).trigger('change');
				}
			}
		}
	};

} );