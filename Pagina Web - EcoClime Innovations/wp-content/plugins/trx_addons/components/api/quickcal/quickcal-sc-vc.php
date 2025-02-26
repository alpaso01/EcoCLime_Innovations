<?php
/**
 * Plugin support: QuickCal (WPBakery support)
 *
 * @package ThemeREX Addons
 * @since v2.26.0
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}

if ( ! function_exists( 'trx_addons_sc_quickcal_add_in_vc' ) ) {
	add_action( 'init', 'trx_addons_sc_quickcal_add_in_vc', 20 );
	/**
	 * Add core shortcodes of 'QuickCal' to the VC shortcodes list
	 * 
	 * @hooked init, 20
	 */
	function trx_addons_sc_quickcal_add_in_vc() {

		if ( ! trx_addons_exists_vc() || ! trx_addons_exists_quickcal() ) {
			return;
		}

		vc_lean_map( "quickcal-appointments", 'trx_addons_sc_quickcal_add_in_vc_params_ba');
		class WPBakeryShortCode_QuickCal_Appointments extends WPBakeryShortCode {}

		vc_lean_map( "quickcal-calendar", 'trx_addons_sc_quickcal_add_in_vc_params_bc');
		class WPBakeryShortCode_QuickCal_Calendar extends WPBakeryShortCode {}

		vc_lean_map( "quickcal-profile", 'trx_addons_sc_quickcal_add_in_vc_params_bp');
		class WPBakeryShortCode_QuickCal_Profile extends WPBakeryShortCode {}
	}
}

if ( ! function_exists( 'trx_addons_sc_quickcal_add_in_vc_params_ba' ) ) {
	/**
	 * Return array with parameters for shortcode 'QuickCal' for VC
	 * 
	 * @return array  Shortcode parameters
	 */
	function trx_addons_sc_quickcal_add_in_vc_params_ba() {
		return array(
				"base" => "quickcal-appointments",
				"name" => __("QuickCal Appointments", "trx_addons"),
				"description" => __("Display the currently logged in user's upcoming appointments", "trx_addons"),
				"category" => __('Content', 'trx_addons'),
				'icon' => 'icon_trx_sc_quickcal_appointments',
				"class" => "trx_sc_single trx_sc_quickcal_appointments",
				"content_element" => true,
				"is_container" => false,
				"show_settings_on_create" => false,
				"params" => array()
			);
	}
}

if ( ! function_exists( 'trx_addons_sc_quickcal_add_in_vc_params_bp' ) ) {
	/**
	 * Return array with parameters for shortcode 'QuickCal Profile' for VC
	 * 
	 * @return array  Shortcode parameters
	 */
	function trx_addons_sc_quickcal_add_in_vc_params_bp() {
		return array(
				"base" => "quickcal-profile",
				"name" => __("QuickCal Profile", "trx_addons"),
				"description" => __("Display the currently logged in user's profile", "trx_addons"),
				"category" => __('Content', 'trx_addons'),
				'icon' => 'icon_trx_sc_quickcal_profile',
				"class" => "trx_sc_single trx_sc_quickcal_profile",
				"content_element" => true,
				"is_container" => false,
				"show_settings_on_create" => false,
				"params" => array()
			);
	}
}

if ( ! function_exists( 'trx_addons_sc_quickcal_add_in_vc_params_bc' ) ) {
	/**
	 * Return array with parameters for shortcode 'QuickCal Calendar' for VC
	 * 
	 * @return array  Shortcode parameters
	 */
	function trx_addons_sc_quickcal_add_in_vc_params_bc() {
		return array(
				"base" => "quickcal-calendar",
				"name" => __("QuickCal Calendar", "trx_addons"),
				"description" => __("Insert quickcal calendar", "trx_addons"),
				"category" => __('Content', 'trx_addons'),
				'icon' => 'icon_trx_sc_quickcal_calendar',
				"class" => "trx_sc_single trx_sc_quickcal_calendar",
				"content_element" => true,
				"is_container" => false,
				"show_settings_on_create" => true,
				"params" => array(
					array(
						"param_name" => "style",
						"heading" => esc_html__("Layout", "trx_addons"),
						"description" => esc_html__("Select style of the quickcal calendar", "trx_addons"),
						"admin_label" => true,
						"std" => "0",
						"value" => array_flip(array(
											'calendar' => esc_html__('Calendar', 'trx_addons'),
											'list' => esc_html__('List', 'trx_addons')
											)),
						"type" => "dropdown"
					),
					array(
						"param_name" => "calendar",
						"heading" => esc_html__("Calendar", "trx_addons"),
						"description" => esc_html__("Select quickcal calendar to display", "trx_addons"),
						"admin_label" => true,
						"std" => "0",
						"value" => array_flip(trx_addons_array_merge( array( 0 => trx_addons_get_not_selected_text( esc_html__( 'Select calendar', 'trx_addons' ) ) ), trx_addons_get_list_terms(false, 'booked_custom_calendars' ) ) ),
						"type" => "dropdown"
					),
					array(
						"param_name" => "year",
						"heading" => esc_html__("Year", "trx_addons"),
						"description" => esc_html__("Year to display on calendar by default", "trx_addons"),
						'edit_field_class' => 'vc_col-sm-6',
						"admin_label" => true,
						"std" => date("Y"),
						"value" => date("Y"),
						"type" => "textfield"
					),
					array(
						"param_name" => "month",
						"heading" => esc_html__("Month", "trx_addons"),
						"description" => esc_html__("Month to display on calendar by default", "trx_addons"),
						'edit_field_class' => 'vc_col-sm-6',
						"admin_label" => true,
						"std" => date("m"),
						"value" => date("m"),
						"type" => "textfield"
					)
				)
			);
	}
}
