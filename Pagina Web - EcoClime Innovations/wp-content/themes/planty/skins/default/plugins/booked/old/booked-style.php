<?php
// Add plugin-specific colors and fonts to the custom CSS
if ( ! function_exists( 'planty_booked_get_css' ) ) {
	add_filter( 'planty_filter_get_css', 'planty_booked_get_css', 10, 2 );
	function planty_booked_get_css( $css, $args ) {

		if ( isset( $css['fonts'] ) && isset( $args['fonts'] ) ) {
			$fonts         = $args['fonts'];
			$css['fonts'] .= <<<CSS

body #booked-profile-page .booked-fea-appt-list .appt-block button.button-primary,
body #booked-profile-page .appt-block .booked-cal-buttons.addeventatc .google-cal-button,
body #booked-profile-page .booked-profile-appt-list .appt-block .booked-cal-buttons a,
body #booked-profile-page .appt-block .booked-cal-buttons .google-cal-button,
.booked-calendar-wrap .booked-appt-list .timeslot .timeslot-people button,
body #booked-profile-page input[type="submit"],
body #booked-profile-page button,
body .booked-list-view input[type="submit"],
body .booked-list-view button,
body table.booked-calendar input[type="submit"],
body table.booked-calendar button,
body .booked-modal input[type="submit"],
body .booked-modal button {
	{$fonts['button_font-family']}
	{$fonts['button_font-size']}
	{$fonts['button_font-weight']}
	{$fonts['button_font-style']}
	{$fonts['button_line-height']}
	{$fonts['button_text-decoration']}
	{$fonts['button_text-transform']}
	{$fonts['button_letter-spacing']}
}
body #booked-page-form input[type="text"],
body #booked-page-form input[type="password"],
body #booked-page-form input[type="email"],
body #booked-page-form textarea {
	{$fonts['input_font-family']}
	{$fonts['input_font-size']}
	{$fonts['input_font-weight']}
	{$fonts['input_font-style']}
	{$fonts['input_line-height']}
	{$fonts['input_text-decoration']}
	{$fonts['input_text-transform']}
	{$fonts['input_letter-spacing']}
}
body #profile-login label,
#profile-register label,
#profile-forgot label,
body #profile-edit #booked-page-form label,
body #booked-profile-page .booked-tabs li a,
body .booked-form .field label.field-label,
body .booked-modal .bm-window p.appointment-title,
body .booked-modal .bm-window p.calendar-name,
body .booked-modal .bm-window p small,
body table.booked-calendar tr.entryBlock,
body table.booked-calendar tr.days th,
body table.booked-calendar thead th .monthName,
body table.booked-calendar td .date .number {
	{$fonts['h5_font-family']}
}
body .booked-calendar-wrap.small table.booked-calendar td .date .number,
body .booked-modal .bm-window p {
	{$fonts['p_font-family']}
}


CSS;
		}

		return $css;
	}
}

