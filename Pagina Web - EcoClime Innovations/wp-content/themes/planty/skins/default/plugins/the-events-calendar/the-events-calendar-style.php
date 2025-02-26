<?php
// Add plugin-specific colors and fonts to the custom CSS
if ( ! function_exists( 'planty_tribe_events_get_css' ) ) {
	add_filter( 'planty_filter_get_css', 'planty_tribe_events_get_css', 10, 2 );
	function planty_tribe_events_get_css( $css, $args ) {
		if ( isset( $css['fonts'] ) && isset( $args['fonts'] ) ) {
			$fonts         = $args['fonts'];
			$css['fonts'] .= <<<CSS
			
.tribe-events-list .tribe-events-list-event-title {
	{$fonts['h3_font-family']}
}

#tribe-events .tribe-events-button,
.tribe-events-button,
.tribe-events .tribe-events-c-ical__link,
/* Tribe Events 5.0+ */
.tribe-common .tribe-common-c-btn,
.tribe-common a.tribe-common-c-btn,
.tribe-events .tribe-events-c-subscribe-dropdown .tribe-events-c-subscribe-dropdown__button .tribe-events-c-subscribe-dropdown__button-text,
/* Tribe Events 6.0.13 */
.tribe-common button.tribe-common-c-btn {
	{$fonts['button_font-family']}
	{$fonts['button_font-size']}
	{$fonts['button_font-weight']}
	{$fonts['button_font-style']}
	{$fonts['button_line-height']}
	{$fonts['button_text-decoration']}
	{$fonts['button_text-transform']}
	{$fonts['button_letter-spacing']}
}
/* Tribe Events 6.0.13 */
.tribe-events .tribe-events-c-search__button:focus,
.tribe-events .tribe-events-c-search__button:hover,
.tribe-events button.tribe-events-c-search__button:focus,
.tribe-events button.tribe-events-c-search__button:hover {
	{$fonts['button_font-size']}
	{$fonts['button_font-weight']}
	{$fonts['button_font-style']}
	{$fonts['button_text-decoration']}
}
.tribe-events-cal-links a,
.tribe-events-sub-nav li a {
	{$fonts['button_font-family']}
	{$fonts['button_font-style']}
	{$fonts['button_line-height']}
	{$fonts['button_text-decoration']}
	{$fonts['button_text-transform']}
	{$fonts['button_letter-spacing']}
}
.tribe-events .tribe-events-calendar-month__calendar-event-datetime,
.tribe-common--breakpoint-medium.tribe-common .tribe-common-form-control-text__input, 
.tribe-common .tribe-common-form-control-text__input {
	{$fonts['p_font-family']}
}
.tribe-common .tribe-common-c-btn-border, 
.tribe-common a.tribe-common-c-btn-border,
#tribe-bar-form button, #tribe-bar-form a,
.tribe-events-read-more {
	{$fonts['button_font-family']}
	{$fonts['button_letter-spacing']}
}
.tribe-events-single .tribe-events-sub-nav,
.tribe-events-single-event-title,
.tribe-events .tribe-events-calendar-month-mobile-events__mobile-event-cost,
.tribe-events .tribe-events-c-nav__list-item--today .tribe-events-c-nav__today,
.tribe-events .tribe-events-c-top-bar .tribe-events-c-top-bar__today-button,
.tribe-events .tribe-events-c-nav__list-item--prev .tribe-events-c-nav__prev,
.tribe-events .tribe-events-c-nav__list-item--next .tribe-events-c-nav__next,
.tribe-events .datepicker .dow,
.tribe-events .datepicker .datepicker-switch,
.tribe-events .datepicker .month,
.tribe-events .datepicker .year,
.tribe-common .tribe-common-h1, 
.tribe-common .tribe-common-h2,
.tribe-common .tribe-common-h3,
.tribe-common .tribe-common-h4, 
.tribe-common .tribe-common-h5,
.tribe-common .tribe-common-h6,
.tribe-common .tribe-common-h7,
.tribe-common .tribe-common-h8,
.tribe-events .tribe-events-calendar-list__event-date-tag-weekday,
.tribe-events .tribe-events-calendar-latest-past__event-date-tag-month,
.tribe-events .tribe-events-calendar-latest-past__event-date-tag-year,
.tribe-events .tribe-events-calendar-month__calendar-event-tooltip-cost,
.tribe-events .tribe-events-c-view-selector__list-item-text,
.tribe-common .tribe-events-calendar-list__event-cost.tribe-common-b3,
.tribe-common .tribe-events-calendar-day__event-cost.tribe-common-b3,
.tribe-common .tribe-events-calendar-month__calendar-event-tooltip-cost.tribe-common-b3,
.tribe-events-list .tribe-events-event-cost span,
#tribe-bar-views .tribe-bar-views-list .tribe-bar-views-option,
.tribe-bar-mini #tribe-bar-views .tribe-bar-views-list .tribe-bar-views-option,
.single-tribe_events #tribe-events-content .tribe-events-event-meta dt,
.tribe-events-list .tribe-events-list-separator-month,
.tribe-events-calendar thead th,
.tribe-events-schedule .tribe-events-cost,
.tribe-events .tribe-events-calendar-month__header .tribe-events-calendar-month__header-column-title {
	{$fonts['h5_font-family']}
}
.tribe-events .tribe-events-c-subscribe-dropdown .tribe-events-c-subscribe-dropdown__list-item,
.single-tribe_events .tribe-events-event-meta,
.single-tribe_events .tribe-events-content,
.tribe-events-schedule,
.tribe-events-schedule h2, 
.tribe-events .datepicker .day,
.tribe-common .tribe-common-b2,
.tribe-common .tribe-common-b3,
.tribe-events .tribe-events-calendar-month__calendar-event-tooltip-datetime,
#tribe-bar-form input, #tribe-events-content.tribe-events-month,
#tribe-events-content .tribe-events-calendar div[id*="tribe-events-event-"] h3.tribe-events-month-event-title,
#tribe-mobile-container .type-tribe_events,
.tribe-events-list-widget ol li .tribe-event-title {
	{$fonts['p_font-family']}
}
.tribe-events-loop .tribe-event-schedule-details,
.single-tribe_events #tribe-events-content .tribe-events-event-meta dt,
#tribe-mobile-container .type-tribe_events .tribe-event-date-start {
	{$fonts['info_font-family']};
}
.single-tribe_events #tribe-events-content.tribe-blocks-editor .tribe-events-event-meta .tribe-events-single-section-title {
	{$fonts['h3_font-family']}
	{$fonts['h3_font-weight']}
	{$fonts['h3_letter-spacing']}
	{$fonts['h3_line-height']}
}

CSS;
		}

		return $css;
	}
}

