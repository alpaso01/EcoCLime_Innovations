// Buttons decoration (add 'hover' class)
// Attention! Not use cont.find('selector')! Use jQuery('selector') instead!

jQuery( document ).on(
	'action.init_hidden_elements', function(e, cont) {
		"use strict";

		// Add button's hover class (selected in Theme Options) to the tags 'button' and 'a'
		// 'sc_button_hover_fade' | 'sc_button_hover_slide_left' | 'sc_button_hover_slide_right' | 'sc_button_hover_slide_top' | 'sc_button_hover_slide_bottom'
		if (PLANTY_STORAGE['button_hover'] && PLANTY_STORAGE['button_hover'] != 'default') {
			jQuery(
				// Tag 'button'
				'button:not(.search_submit):not(.full_post_close):not([class*="sc_button_hover_"]),'
				+ '.sc_form_field button:not([class*="sc_button_hover_"]),'
				// Theme and sc_button
				+ '.theme_button:not([class*="sc_button_hover_"]),'
				+ '.sc_button:not([class*="sc_button_simple"]):not([class*="sc_button_bordered"]):not([class*="sc_button_hover_"]),'
				// Theme tabs
				+ '.planty_tabs .planty_tabs_titles li a:not([class*="sc_button_hover_"]),'
				// More link
				+ '.post_item .more-link:not([class*="sc_button_hover_"]),'
				// Links in the trx_addons hover with back info
				+ '.trx_addons_hover_content .trx_addons_hover_links a:not([class*="sc_button_hover_"]),'
				// BuddyPress
				+ '#buddypress a.button:not([class*="sc_button_hover_"])'
				// MP Time table
				+ '.mptt-navigation-tabs li a:not([class*="sc_button_hover_style_"]),'
				// EDD (Easy digital downloads)
				+ '.edd_download_purchase_form .button:not([class*="sc_button_hover_style_"]),'
				+ '.edd-submit.button:not([class*="sc_button_hover_style_"]),'
				+ '.widget_edd_cart_widget .edd_checkout a:not([class*="sc_button_hover_style_"]),'
				// WooCommerce
				+ '.hover_shop_buttons .icons a:not([class*="sc_button_hover_style_"]),'
				+ '.woocommerce #respond input#submit:not([class*="sc_button_hover_"]),'
				+ '.woocommerce .button:not([class*="shop_"]):not([class*="view"]):not([class*="sc_button_hover_"]),'
				+ '.woocommerce-page .button:not([class*="shop_"]):not([class*="view"]):not([class*="sc_button_hover_"])'
			).addClass( 'sc_button_hover_just_init sc_button_hover_' + PLANTY_STORAGE['button_hover'] );

			// Add button's hover class if not based on :before or :after elements (like 'sc_button_hover_arrow')
			if (PLANTY_STORAGE['button_hover'] != 'arrow') {
				jQuery(
					// Form buttons
					'input[type="submit"]:not([class*="sc_button_hover_"]),'
					+ 'input[type="button"]:not([class*="sc_button_hover_"]),'
					// Tag cloud
					+ '.tagcloud > a:not([class*="sc_button_hover_"]),'
					// VC tabs and accordion
					+ '.vc_tta-accordion .vc_tta-panel-heading .vc_tta-controls-icon:not([class*="sc_button_hover_"]),'
					+ '.vc_tta-color-grey.vc_tta-style-classic .vc_tta-tab > a:not([class*="sc_button_hover_"]),'
					// WooCommerce
					+ '.woocommerce nav.woocommerce-pagination ul li a:not([class*="sc_button_hover_"]),'
					// Tribe Events Calendar
					+ '.tribe-events-button:not([class*="sc_button_hover_"]),'
					+ '#tribe-bar-views .tribe-bar-views-list .tribe-bar-views-option a:not([class*="sc_button_hover_"]),'
					+ '.tribe-bar-mini #tribe-bar-views .tribe-bar-views-list .tribe-bar-views-option a:not([class*="sc_button_hover_"]),'
					+ '.tribe-events-cal-links a:not([class*="sc_button_hover_"]),'
					+ '.tribe-events-sub-nav li a:not([class*="sc_button_hover_"]),'
					// ThemeREX Addons buttons
					+ '.isotope_filters_button:not([class*="sc_button_hover_"]),'
					+ '.trx_addons_scroll_to_top:not([class*="sc_button_hover_"]),'
					+ '.sc_promo_modern .sc_promo_link2:not([class*="sc_button_hover_"]),'
					+ '.slider_container .slider_prev:not([class*="sc_button_hover_"]),'
					+ '.slider_container .slider_next:not([class*="sc_button_hover_"]),'
					+ '.sc_slider_controller_titles .slider_controls_wrap > a:not([class*="sc_button_hover_"])'
				).addClass( 'sc_button_hover_just_init sc_button_hover_' + PLANTY_STORAGE['button_hover'] );
			}

			// Alter style: sc_button_hover_style_default
			jQuery(
				// Controls in the slider controller (style 'Titles') - arrows at the left and right side
				'.sc_slider_controller_titles .slider_controls_wrap > a:not([class*="sc_button_hover_style_"])'
			).addClass( 'sc_button_hover_just_init sc_button_hover_style_default' );

			// Alter style: sc_button_hover_style_inverse
			jQuery(
				// Links in the trx_addons hover with back info
				'.trx_addons_hover_content .trx_addons_hover_links a:not([class*="sc_button_hover_style_"]),'
				// WooCommerce single product: related products and up-sells
				+ '.single-product ul.products li.product .post_data .button:not([class*="sc_button_hover_style_"])'
			).addClass( 'sc_button_hover_just_init sc_button_hover_style_inverse' );

			// Alter style: sc_button_hover_style_hover
			jQuery(
				// Social share icons
				'.post_item_single .post_content .post_meta .post_share .socials_type_block .social_item .social_icon:not([class*="sc_button_hover_style_"]),'
				// WooCommerce buttons with class 'alt'
				+ '.woocommerce #respond input#submit.alt:not([class*="sc_button_hover_style_"]),'
				+ '.woocommerce a.button.alt:not([class*="sc_button_hover_style_"]),'
				+ '.woocommerce button.button.alt:not([class*="sc_button_hover_style_"]),'
				+ '.woocommerce input.button.alt:not([class*="sc_button_hover_style_"])'
			).addClass( 'sc_button_hover_just_init sc_button_hover_style_hover' );

			// Alter style: sc_button_hover_style_alter
			jQuery(
				// WooCommerce buttons in the notice and info areas
				'.woocommerce .woocommerce-message .button:not([class*="sc_button_hover_style_"]),'
				+ '.woocommerce .woocommerce-info .button:not([class*="sc_button_hover_style_"])'
			).addClass( 'sc_button_hover_just_init sc_button_hover_style_alter' );

			// Alter style: sc_button_hover_style_alterbd
			jQuery(
				// ThemeREX Addons tabs in the sidebar
				'.sidebar .trx_addons_tabs .trx_addons_tabs_titles li a:not([class*="sc_button_hover_style_"]),'
				// Theme tabs
				+ '.planty_tabs .planty_tabs_titles li a:not([class*="sc_button_hover_style_"]),'
				// Tag cloud
				+ '.widget_tag_cloud a:not([class*="sc_button_hover_style_"]),'
				// Woocommerce tag cloud
				+ '.widget_product_tag_cloud a:not([class*="sc_button_hover_style_"])'
			).addClass( 'sc_button_hover_just_init sc_button_hover_style_alterbd' );

			// Alter style: sc_button_hover_style_dark
			jQuery(
				// VC tabs and accordion
				'.vc_tta-accordion .vc_tta-panel-heading .vc_tta-controls-icon:not([class*="sc_button_hover_style_"]),'
				+ '.vc_tta-color-grey.vc_tta-style-classic .vc_tta-tab > a:not([class*="sc_button_hover_style_"]),'
				// WooCommerce tabs in the single product
				+ '.single-product div.product > .woocommerce-tabs .wc-tabs li a:not([class*="sc_button_hover_style_"]),'
				// sc_button with color style 'dark'
				+ '.sc_button.color_style_dark:not([class*="sc_button_simple"]):not([class*="sc_button_hover_style_"]),'
				// Slider controls (arrows)
				+ '.slider_prev:not([class*="sc_button_hover_style_"]),'
				+ '.slider_next:not([class*="sc_button_hover_style_"]),'
				// Video player - button 'Play'
				+ '.trx_addons_video_player.with_cover .video_hover:not([class*="sc_button_hover_style_"]),'
				// ThemeREX Addons tabs
				+ '.trx_addons_tabs .trx_addons_tabs_titles li a:not([class*="sc_button_hover_style_"])'
			).addClass( 'sc_button_hover_just_init sc_button_hover_style_dark' );

			// Alter style: sc_button_hover_style_extra
			jQuery(
				// Price table links
				'.sc_price_item_link:not([class*="sc_button_hover_style_"])'
			).addClass( 'sc_button_hover_just_init sc_button_hover_style_extra' );

			// Alter style: sc_button_hover_style_link2
			jQuery(
				// sc_button with color style 'link2'
				'.sc_button.color_style_link2:not([class*="sc_button_simple"]):not([class*="sc_button_hover_style_"])'
			).addClass( 'sc_button_hover_just_init sc_button_hover_style_link2' );

			// Alter style: sc_button_hover_style_link3
			jQuery(
				// sc_button with color style 'link3'
				'.sc_button.color_style_link3:not([class*="sc_button_simple"]):not([class*="sc_button_hover_style_"])'
			).addClass( 'sc_button_hover_just_init sc_button_hover_style_link3' );

			// Remove hover class from some elements for which hover-effect should not be used
			jQuery(
				// Media player 'Media elements'
				'.mejs-controls button,'
				// Icon inside submenu navigation
				+ '.wp-block-navigation__submenu-icon,'
				// Magnific popup close button
				+ '.mfp-close,'
				// sc_button with background image
				+ '.sc_button_bg_image,'
				// Buttons in rows with type 'Narrow' inside custom layouts
				+ '.sc_layouts_row_type_narrow .sc_button,'
				// Tribe Events 5.0+ new design
				+ '.tribe-common-c-btn-icon,'
				+ '.tribe-events-c-top-bar__datepicker-button,'
				+ '.tribe-events-calendar-list-nav button,'
				+ '.tribe-events-cal-links .tribe-events-button,'
				// Links in the hover 'Shop'
				+ '.hover_shop_buttons a,'
				// Woocommerce
				+ 'button.pswp__button,'
				+ '.woocommerce-orders-table__cell-order-actions .button'
			).removeClass( 'sc_button_hover_' + PLANTY_STORAGE['button_hover'] );

			// Remove temporary class 'sc_button_hover_just_init'
			setTimeout(
				function() {
					jQuery( '.sc_button_hover_just_init' ).removeClass( 'sc_button_hover_just_init' );
				}, 500
			);

		}

        //Underline Hover
        //++++++++++++++++
        jQuery(
            '.sc_icons_simple .sc_icons_item_description a:not(.underline_hover),'
            + '.sc_icons_plate .sc_icons_item a.sc_icons_item_more_link:not(.underline_hover),'
            + '.sc_layouts_title_breadcrumbs .breadcrumbs a:not(.underline_hover)'
        ).addClass( 'underline_hover' );

        //Underline Hover Reverse
        //++++++++++++++++
        jQuery(
            '.sc_icons_plain .sc_icons_item .sc_icons_item_more_link:not(.underline_hover_reverse),'
            + '.sc_icons_bordered .sc_icons_item_description a:not(.underline_hover_reverse)'
        ).addClass( 'underline_hover_reverse' );


        //Underline Animation
        //+++++++++++++++++++
		
        /*
		jQuery(
            ''
        ).addClass( 'underline_anim' );
		*/

        jQuery(window).scroll(function() {
            jQuery( '.underline_anim:not(.underline_do_hover)' ).each( function() {
                var item = jQuery(this);
                if ( item.offset().top < jQuery( window ).scrollTop() + jQuery( window ).height() - 80 ) {
                    item.addClass( 'underline_do_hover' );
                }
            } );
        });

	}
);
