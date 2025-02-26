/* global jQuery, PLANTY_STORAGE */

jQuery( document ).ready(
	function() {
		"use strict";

		// Hide empty meta-boxes
		jQuery( '.postbox > .inside' ).each(
			function() {
				if (jQuery( this ).html().length < 5) {
					jQuery( this ).parent().hide();
				}
			}
		);

		// Hide admin notice
		jQuery( '.planty_admin_notice .notice-dismiss' )
			.on( 'click', function(e) {
				jQuery.post(
					PLANTY_STORAGE['ajax_url'], {
						'action': 'planty_hide_' + jQuery( this ).parent().data( 'notice' ) + '_notice',
						'nonce': PLANTY_STORAGE['ajax_nonce']
					},
					function(response){}
				);
				e.preventDefault();
				return false;
			}
		);

		// TGMPA Source selector is changed
		jQuery( '.tgmpa_source_file' ).on(
			'change', function(e) {
				var chk = jQuery( this ).parents( 'tr' ).find( '>th>input[type="checkbox"]' );
				if (chk.length == 1) {
					if (jQuery( this ).val() !== '') {
						chk.attr( 'checked', 'checked' );
					} else {
						chk.removeAttr( 'checked' );
					}
				}
			}
		);

		// jQuery Tabs
		//---------------------------------
		if (jQuery.ui && jQuery.ui.tabs) {
			// Vertical tabs
			var $tabs = jQuery( '.planty_tabs_vertical:not(.inited)' );
			if ( $tabs.length > 0 ) {
				$tabs
					.on( 'click', '.planty_tabs_title:not(.planty_tabs_title_sub)', function(e) {
						var sup = jQuery(this),
							stop = false,
							first = true;
						sup.siblings( '.planty_tabs_title_sub' ).stop().slideUp( function() {
							sup.siblings( '.planty_tabs_title_super' ).removeClass( 'ui-tabs-active ui-state-active ui-state-focus' );
						} );
						sup.nextAll().each( function() {
							var sub = jQuery(this);
							if ( ! stop ) {
								if ( sub.hasClass( 'planty_tabs_title_sub' ) ) {
									sub.stop().slideDown();
									if ( first ) {
										first = false;
										sup.removeClass('ui-state-focus');							
										sub.addClass('ui-tabs-active ui-state-active ui-state-focus');							
									}
								} else {
									stop = true;
								}
							}
						} );
						// Animated scroll to the top of the tabs block
						planty_document_animate_to( jQuery( '.planty_options' ).eq(0) );
					} )
					.on( 'click', '.planty_tabs_title_sub', function(e) {
						var sub = jQuery(this),
							prev = sub.prev(),
							stop = false;
						sub.siblings( '.planty_tabs_title_sub' ).removeClass( 'ui-tabs-active ui-state-active ui-state-focus' );
						while ( prev.length == 1 ) {
							if ( prev.hasClass( 'planty_tabs_title_super' ) ) {
								prev.addClass('ui-tabs-active ui-state-active ui-state-focus');
								break;
							}
							prev = prev.prev();
						}
						// Animated scroll to the top of the tabs block
						planty_document_animate_to( jQuery( '.planty_options' ).eq(0) );
					} );
			}

			// Init all tabs
			jQuery( '.planty_tabs:not(.inited)' ).addClass( 'inited' ).tabs();

			// Open first panel
			if ( $tabs.length > 0 ) {
				$tabs.each( function() {
					var $first_title = jQuery( this ).find( '.trx_addons_tabs_title' ).eq(0);
					if ( $first_title.length > 0 && $first_title.hasClass( 'trx_addons_tabs_title_super' ) ) {
						$first_title.trigger( 'click' );
					}
				} );
			}
		}

		// jQuery Accordion
		//----------------------------------
		if (jQuery.ui && jQuery.ui.accordion) {
			jQuery( '.planty_accordion:not(.inited)' ).addClass( 'inited' ).accordion(
				{
					'header': '.planty_accordion_title',
					'heightStyle': 'content'
				}
			);
		}

		// Text Editor
		//------------------------------------------------------------------

		// Save editors content to the hidden field
		jQuery( document ).on(
			'tinymce-editor-init', function() {
				jQuery( '.planty_text_editor .wp-editor-area' ).each(
					function(){
						var tArea = jQuery( this ),
						id        = tArea.attr( 'id' ),
						input     = tArea.parents( '.planty_text_editor' ).prev(),
						editor    = tinyMCE.get( id ),
						content;
						// Duplicate content from TinyMCE editor
						if (editor) {
							editor.on(
								'change', function () {
									this.save();
									content = editor.getContent();
									input.val( content ).trigger( 'change' );
								}
							);
						}
						// Duplicate content from HTML editor
						tArea.css(
							{
								visibility: 'visible'
							}
						).on(
							'keyup', function(){
								content = tArea.val();
								input.val( content ).trigger( 'change' );
							}
						);
					}
				);
			}
		);

		// Link 'Edit layout'
		//------------------------------------------------------------------

		// Refresh link on the post editor when select with layout is changed in VC editor
		jQuery( '.planty_post_editor' ).each(
			function() {
				var link = jQuery( this );
				link.parent().parent().find( 'select' ).on(
					'change', function() {
						planty_change_post_edit_link( link );
					}
				).trigger('change');
			}
		);

		function planty_change_post_edit_link(a) {
			if (a.length > 0) {
				var sel = a.parent().parent().find( 'select' ),
					val = sel.val();
				if ( sel.length === 0 || val === null ) {
					a.addClass( 'planty_hidden' );
				} else {
					if (val == 'inherit') {
						if (sel.parent().hasClass( 'planty_options_item_field' )) {		// Theme Options
							var param_name = sel.parent().data( 'param' ).substr( 0, 12 );
							val            = sel.parents( '#planty_options_tabs' ).find( 'div[data-param="' + param_name + '"] > select' ).val();
						} else if (sel.data( 'customize-setting-link' ) !== undefined) {	// Customize
							var param_name = sel.data( 'customize-setting-link' ).substr( 0, 12 );
							val            = sel.parents( '#customize-theme-controls' ).find( 'select[data-customize-setting-link="' + param_name + '"]' ).val();
						}
					}
					var id = val !== '' && val !== 'inherit' && ( '' + val ).indexOf( '--' ) == -1
								? ('' + val).split( '-' ).pop()
								: 0;
					a.attr( 'href', a.attr( 'href' ).replace( /post=[0-9]{1,5}/, "post=" + id ) );
					if (id == 0 || id == 'none') {
						a.addClass( 'planty_hidden' );
					} else {
						a.removeClass( 'planty_hidden' );
					}
				}
			}
		}


		// Scheme Editor (need for Theme Options and for Customizer)
		//------------------------------------------------------------------

		// Detect WordPress Customizer
		var in_wp_customize = typeof wp.customize != 'undefined';

		// Backup scheme
		if (typeof planty_color_schemes !== 'undefined') {
			var planty_color_schemes_backup = planty_clone_object( planty_color_schemes );
		}

		// Internal ColorPicker
		if (jQuery( '.planty_scheme_editor_colors .iColorPicker' ).length > 0) {
			planty_color_picker();
			jQuery( '.planty_scheme_editor_colors .iColorPicker' )
				.each( function() {
					planty_scheme_editor_change_field_colors( jQuery( this ) );
				} )
				.on( 'focus', function (e) {
					planty_color_picker_show(
						null, jQuery( this ), function(fld, clr) {
							fld.val( clr ).trigger( 'change' );
							planty_scheme_editor_change_field_colors( fld );
						}
					);
				} )
				.on( 'change', function(e) {
					planty_scheme_editor_change_field_value( jQuery( this ) );
				} );

			// Tiny ColorPicker
		} else if (jQuery( '.planty_scheme_editor_colors .tinyColorPicker' ).length > 0) {
			jQuery( '.planty_scheme_editor_colors .tinyColorPicker' ).each(
				function() {
					jQuery( this )
						.colorPicker( {
							animationSpeed: 0,
							opacity: false,
							margin: '1px 0 0 0',
							renderCallback: function($elm, toggled) {
								var colors = this.color.colors,
								rgb        = colors.RND.rgb,
								clr        = (colors.alpha == 1
								? '#' + colors.HEX
								: 'rgba(' + rgb.r + ', ' + rgb.g + ', ' + rgb.b + ', ' + (Math.round( colors.alpha * 100 ) / 100) + ')'
								).toLowerCase();
								$elm.val( clr ).data( 'last-color', clr );
								if (toggled === undefined) {
									$elm.trigger( 'change' );
								}
							}
						}
					)
					.on(
						'change', function(e) {
							planty_scheme_editor_change_field_value( jQuery( this ) );
						}
					);
				}
			);

			// Spectrum ColorPicker
		} else if (jQuery( '.planty_scheme_editor_colors .spectrumColorPicker' ).length > 0) {
			jQuery( '.planty_scheme_editor_colors .spectrumColorPicker' ).each(
				function() {
					var fld = jQuery( this );
					fld.spectrum( {
							showInput: true,
							showInitial: true,
							preferredFormat: 'hex',
							cancelText: "Cancel",
							chooseText: "OK",
							change: function(e) {
								planty_scheme_editor_change_field_value( fld );
							}
						}
					);
				}
			);
		}

		// Update schemes in the 'scheme_storage' field
		function planty_update_scheme_storage(form) {
			if (in_wp_customize) {
				wp.customize( 'scheme_storage' ).set( planty_serialize( planty_color_schemes ) );
			} else {
				form.find( '[data-param="scheme_storage"] > input[type="hidden"]' )
					.val( planty_serialize( planty_color_schemes ) )
					.trigger( 'change' );
			}
		}

		// Show/Hide colors on change scheme editor type
		jQuery( '.planty_scheme_editor_type input' )
			.on( 'change', function() {
				var type = jQuery( this ).val();
				jQuery( this ).parents( '.planty_scheme_editor' )
					.find( '.planty_scheme_editor_colors .planty_scheme_editor_row' )
					.each( function() {
						var row = jQuery( this );
						var visible = type != 'simple';
						row.find( 'input' ).each(
							function() {
								var fld = jQuery( this );
								var color_name = fld.attr( 'name' ),
								fld_visible    = type != 'simple';
								if ( ! fld_visible) {
									for (var i in planty_simple_schemes) {
										if (i == color_name || typeof planty_simple_schemes[i][color_name] != 'undefined') {
											fld_visible = true;
											break;
										}
									}
								}
								if ( fld.next().hasClass('sp-replacer') ) {
									fld = fld.next();
								}
								if ( ! fld_visible) {
									fld.fadeOut();
								} else {
									fld.fadeIn();
								}
								visible = visible || fld_visible;
							}
						);
						if ( ! visible) {
							row.slideUp();
						} else {
							row.slideDown();
						}
					}
				);
			}
		);
		jQuery( '.planty_scheme_editor_type input:checked' ).trigger( 'change' );

		// Change colors on change color scheme
		jQuery( '.planty_scheme_editor_selector' )
			.on( 'change', function(e) {
				var scheme = jQuery( this ).val();
				for (var opt in planty_color_schemes[scheme].colors) {
					var fld = jQuery( this ).parents( '.planty_scheme_editor' ).find( '.planty_scheme_editor_colors input[name="' + opt + '"]' );
					if (fld.length === 0) {
						continue;
					}
					fld.val( planty_color_schemes[scheme].colors[opt] );
					planty_scheme_editor_change_field_colors( fld );
				}
			}
		);

		// Reset colors of the current scheme
		jQuery( '.planty_scheme_editor_control_reset' )
			.on( 'click', function() {
				if (confirm( PLANTY_STORAGE['msg_scheme_reset'] )) {
					var selector                         = jQuery( this ).parents( '.planty_scheme_editor' ).find( '.planty_scheme_editor_selector' ),
					scheme                               = selector.val();
					planty_color_schemes[scheme].colors = planty_clone_object( planty_color_schemes_backup[scheme].colors );
					planty_update_scheme_storage( jQuery( this ).parents( 'form' ) );
					selector.trigger( 'change' );
				}
			}
		);

		// Copy (duplicate) current scheme
		jQuery( '.planty_scheme_editor_control_copy' )
			.on( 'click', function() {
				var title = prompt( PLANTY_STORAGE['msg_scheme_copy'] );
				if (title) {
					var selector                             = jQuery( this ).parents( '.planty_scheme_editor' ).find( '.planty_scheme_editor_selector' ),
					scheme_new                               = title.toLowerCase().replace( /\s/g, '_' ).replace( /\W/g, '' ),
					scheme                                   = selector.val();
					planty_color_schemes_backup[scheme_new] = {
						'title': title,
						'colors': planty_clone_object( planty_color_schemes[scheme].colors )
					};
					planty_color_schemes[scheme_new]        = {
						'title': title,
						'colors': planty_clone_object( planty_color_schemes[scheme].colors )
					};
					// Refresh templates list in Customizer
					if (in_wp_customize) {
						wp.customize.trigger( 'refresh_schemes' );
					}
					// Update 'storage' with schemes
					planty_update_scheme_storage( jQuery( this ).parents( 'form' ) );
					// Add new scheme to the selector
					selector
						.append( '<option value="' + scheme_new + '">' + title + '</option>' )
						.val( scheme_new )
						.trigger( 'change' );
					// Lock css update
					if (in_wp_customize) {
						wp.customize.trigger( 'lock_css', true );
					}
					// Add new scheme to the options 'xxx_scheme' (e.g. 'color_scheme', 'sidebar_scheme' ...)
					selector
						.parents( in_wp_customize ? '#customize-theme-controls' : '#planty_options_form' )
						.find( in_wp_customize ? '.customize-control[id$="_scheme"]' : '.planty_options_item_field[data-param$="_scheme"]' )
						.each(
							function() {
								var fld = jQuery( this ),
								input   = fld.find( 'select,input' );
								// Add control with scheme
								if (input.prop( 'tagName' ) == 'SELECT') {
									input.find( 'option[value="' + scheme + '"]' ).eq( 0 ).clone( true ).val( scheme_new ).appendTo( input );
								} else {
									fld.find( '[value="' + scheme + '"]' ).each(
										function() {
											var obj = jQuery( this );
											// Add new DOM object
											clone_control( obj, scheme_new, title );
											// Add new control to the internal element content in Customizer
											if (in_wp_customize) {
												try {
													var param = obj.data( 'customize-setting-link' ),
													content   = jQuery( wp.customize.settings.controls[param].content );
													content.find( '[value="' + scheme + '"]' ).each(
														function() {
															var obj = jQuery( this );
															clone_control( obj, scheme_new, title );
														}
													);
													wp.customize.settings.controls[param].content = content.html();
													if (typeof wp.customize.settings.controls[param].linkElements !== 'undefined') {
														wp.customize.settings.controls[param].linkElements();
													}
												} catch (e) {
												}
											}
										}
									);
								}
							}
						);
					// Unlock css update
					if (in_wp_customize) {
						wp.customize.trigger( 'lock_css', false );
					}
				}

				function clone_control(obj, value, title) {
					var lbl = obj.parent();
					if ( lbl.prop( "tagName" ) == 'LABEL' || lbl.hasClass( 'customize-inside-control-row' ) ) {
						var lbl_new = lbl.clone( true );
						lbl_new.find( '> input' ).val( value ).removeAttr( 'checked' ).get(0).checked = false;
						lbl_new.find( '.planty_options_item_caption,label' ).text( title );
						lbl.parent().append( lbl_new );
					} else {
						var obj_new = obj.clone( true ).val( value );
						obj_new.removeAttr( 'checked' ).get( 0 ).checked = false;
						lbl.append( obj_new );
						lbl.append( title );
					}
				}
			}
		);

		// Delete current scheme
		jQuery( '.planty_scheme_editor_control_delete' ).on(
			'click', function() {
				var i    = 0,
				selector = jQuery( this ).parents( '.planty_scheme_editor' ).find( '.planty_scheme_editor_selector' ),
				scheme   = selector.val();

				for (var j in planty_color_schemes) {
					i++;
				}

				if (i < 2) {
					alert( PLANTY_STORAGE['msg_scheme_delete_last'] );

				} else if (typeof planty_color_schemes[scheme].internal !== 'undefined' && planty_color_schemes[scheme].internal) {
					alert( PLANTY_STORAGE['msg_scheme_delete_internal'] );

				} else if (confirm( PLANTY_STORAGE['msg_scheme_delete'] )) {
					// Remove option from the selector
					selector.find( 'option[value="' + scheme + '"]' ).remove();
					var scheme_new = selector.find( 'option' ).eq( 0 ).val();
					selector.val( scheme_new ).trigger( 'change' );
					// Lock css update
					if (in_wp_customize) {
						wp.customize.trigger( 'lock_css', true );
					}
					// Delete scheme from the options 'xxx_scheme' (e.g. 'color_scheme', 'sidebar_scheme' ...)
					selector
						.parents(
							in_wp_customize
								? '#customize-theme-controls'
								: '#planty_options_form'
						)
						.find(
							in_wp_customize
								? '.customize-control[id$="_scheme"]'
								: '.planty_options_item_field[data-param$="_scheme"]'
						)
						.each(
							function() {
								var fld = jQuery( this ),
								input   = fld.find( 'select,input:checked' );
								// Select new scheme instead deleted scheme
								if (input.val() == scheme) {
									if (in_wp_customize) {
										wp.customize( input.data( 'customize-setting-link' ) ).set( scheme_new );
									} else {
										if (input.prop( 'tagName' ) == 'SELECT') {
											input.val( scheme_new );
										} else {
											fld.find( 'input' ).each(
												function(){
													if (jQuery( this ).val() == scheme_new) {
														jQuery( this ).get( 0 ).checked = true;
													}
												}
											);
										}
									}
								}
								// Delete control with scheme
								fld.find( '[value="' + scheme + '"]' ).each(
									function() {
										var obj = jQuery( this ),
											lbl = obj.parent();
										if ( lbl.prop( "tagName" ) == 'LABEL' || lbl.hasClass( 'customize-inside-control-row' ) ) {
											lbl.remove();
										} else {
											obj.remove();
										}
									}
								);
							}
						);
					// Delete scheme from the list
					delete planty_color_schemes[scheme];
					delete planty_color_schemes_backup[scheme];
					// Refresh templates list in Customizer
					if (in_wp_customize) {
						wp.customize.trigger( 'refresh_schemes' );
					}
					// Unlock css update
					if (in_wp_customize) {
						wp.customize.trigger( 'lock_css', false );
					}
					// Update 'storage' with schemes
					planty_update_scheme_storage( jQuery( this ).parents( 'form' ) );
				}
			}
		);

		// Change colors of the field
		function planty_scheme_editor_change_field_colors(fld) {
			var clr = fld.val(),
			hsb     = planty_hex2hsb( clr );
			fld.css(
				{
					'backgroundColor': clr,
					'color': hsb['b'] < 70 ? '#fff' : '#000'
				}
			);
			if ( fld.hasClass( 'spectrumColorPicker' ) ) {
				fld.spectrum("set", clr);
			}
		}

		// Change value of the field
		function planty_scheme_editor_change_field_value(fld) {
			var color_name      = fld.attr( 'name' ),
				color_value     = fld.val(),
				scheme_editor   = fld.parents( '.planty_scheme_editor' ),
				scheme_selector = scheme_editor.find( '.planty_scheme_editor_selector' ),
				scheme_name     = scheme_selector.val();
			// Change dependent colors
			if ( scheme_editor.find( '.planty_scheme_editor_type input:checked' ).val() == 'simple') {
				if (typeof planty_simple_schemes[color_name] != 'undefined') {
					if (in_wp_customize) {
						wp.customize.trigger( 'lock_css', true );
					}
					for (var i in planty_simple_schemes[color_name]) {
						var chg_fld   = fld.parents( '.planty_scheme_editor_colors' ).find( 'input[name="' + i + '"]' ),
							chg_value = color_value;
						if (chg_fld.length > 0) {
							var level = planty_simple_schemes[color_name][i];
							// Make color_value darkness
							if (level != 1) {
								var hsb   = planty_hex2hsb( chg_value );
								hsb['b']  = Math.min( 100, Math.max( 0, hsb['b'] * (hsb['b'] < 70 ? 2 - level : level) ) );
								chg_value = planty_hsb2hex( hsb ).toLowerCase();
							}
							chg_fld.val( chg_value ).trigger('change');
							planty_scheme_editor_change_field_value( chg_fld );
						}
					}
					if (in_wp_customize) {
						wp.customize.trigger( 'lock_css', false );
					}
				}
			}
			// Change value in the color scheme storage
			planty_color_schemes[scheme_name].colors[color_name] = color_value;
			planty_update_scheme_storage( fld.parents( 'form' ) );
			// Change field colors
			planty_scheme_editor_change_field_colors( fld );
		}


		// Color preset field
		//----------------------------------
		jQuery( '.planty_options_item_field[data-param="color_preset"], #customize-control-color_preset' )
			.on( 'click', '.planty_list_choice_item', function(e) {
				var item   = jQuery( this ),
					list   = item.parents('.planty_list_choice'),
					input  = list.prev(),
					preset = item.data( 'choice' );
				if ( typeof planty_color_presets == 'object' && typeof planty_color_presets[preset] == 'object' ) {
					for ( var scheme in planty_color_presets[preset]['colors'] ) {
						for ( var color in planty_color_presets[preset]['colors'][scheme] ) {
							planty_color_schemes[scheme].colors[color] = planty_color_presets[preset]['colors'][scheme][color];
						}
					}
					var form = item.parents( 'form' );
					planty_update_scheme_storage( form );
					form.find('.planty_scheme_editor_selector').trigger( 'change' );
				}
			}
		);


		// Font preset field
		//----------------------------------
		jQuery( '.planty_options_item_field[data-param="font_preset"], #customize-control-font_preset' )
			.on( 'click', '.planty_list_choice_item:not(.planty_list_active)', function(e) {
				var item   = jQuery( this ),
					form   = item.parents( 'form' ),
					list   = item.parents('.planty_list_choice'),
					input  = list.prev(),
					preset = item.data( 'choice' );
				if ( typeof planty_font_presets == 'object' && typeof planty_font_presets[preset] == 'object' ) {
					var max_load_fonts = typeof planty_options_vars != 'undefined'
												? planty_options_vars['max_load_fonts']
												: ( typeof planty_customizer_vars != 'undefined'
														? planty_customizer_vars['max_load_fonts']
														: 0
														),
						load_fonts_parts = [ 'name', 'family', 'link', 'styles' ],
						i, j;
					// Replace fields in the section 'Load fonts'
					for ( i = 0; i < max_load_fonts; i++ ) {
						for ( j in load_fonts_parts ) {
							form.find('[data-param="load_fonts-' + ( i + 1 ) + '-' + load_fonts_parts[j] + '"] input[type="text"],'
								 + '[data-customize-setting-link="load_fonts-' + ( i + 1 ) + '-' + load_fonts_parts[j] + '"]')
								.val(
									typeof planty_font_presets[preset]['load_fonts'][i] != 'undefined' && planty_font_presets[preset]['load_fonts'][i][load_fonts_parts[j]]
										? planty_font_presets[preset]['load_fonts'][i][load_fonts_parts[j]]
										: ''
								)
								.trigger( 'change' );
						}
					}
					// Replace font settings for each tag
					if ( typeof planty_font_presets[preset]['theme_fonts'] != 'undefined' ) {
						for ( i in planty_font_presets[preset]['theme_fonts'] ) {
							for ( j in planty_font_presets[preset]['theme_fonts'][i] ) {
								// Update field
								var fld = form.find(  '[data-param="' + i + '_' + j + '"] input[type="text"],[data-param="' + i + '_' + j + '"] select,'
													+ '[data-customize-setting-link="' + i + '_' + j + '"]' );
								if ( fld.length ) {
									fld.val( planty_font_presets[preset]['theme_fonts'][i][j] ).trigger( 'change' );
									// Update fonts list
									planty_theme_fonts[i][j] = planty_font_presets[preset]['theme_fonts'][i][j];
								}
							}
						}
					}
					// Refresh preview page (if in customizer)
					if ( typeof planty_customizer_vars != 'undefined' ) {
						jQuery( '#customize-controls .customize-action-refresh' ).trigger( 'click' );
					}
				}
			}
		);


		// Get PRO Version
		//--------------------------------------------
		jQuery( '.planty_pro_link' ).on(
			'click', function(e) {
				jQuery( '.planty_pro_form_wrap' )
				.fadeIn()
				.delay( 200 )
				.find( '.planty_pro_form' )
				.animate(
					{
						'opacity': 1,
						'marginTop': 0
					}
				);
				e.preventDefault();
				return false;
			}
		);
		jQuery( '.planty_pro_close' ).on(
			'click', function(e) {
				jQuery( '.planty_pro_form' )
				.animate(
					{
						'opacity': 0,
						'marginTop': '50px'
					}
				)
				.delay( 200 )
				.parent()
				.fadeOut();
				e.preventDefault();
				return false;
			}
		);
		jQuery( '.planty_pro_key,.planty_pro_token' ).on(
			'keyup', function(e) {
				var key = jQuery( '.planty_pro_key' ).val(),
					token = jQuery( '.planty_pro_token' ).val();
				if (key !== '' && key.length > 10 && ( token === undefined || token.length > 20 ) ) {
					jQuery( '.planty_pro_upgrade' ).removeAttr( 'disabled' );
				} else {
					jQuery( '.planty_pro_upgrade' ).attr( 'disabled', 'disabled' );
				}
			}
		);
		jQuery( '.planty_pro_upgrade' ).on(
			'click', function(e) {
				var key = jQuery( '.planty_pro_key' ).val(),
					token = jQuery( '.planty_pro_token' ).val();
				if (key !== '' && ( token === undefined || token !== '' )) {
					planty_theme_get_pro_version( key, token );
				}
				e.preventDefault();
				return false;
			}
		);

		// Main upgrade procedure
		window.planty_theme_get_pro_version = function(key, token) {
			// Add progress spin and disable 'Upgrade' button
			jQuery( '.planty_pro_upgrade' )
				.attr( 'disabled', 'disabled' )
				.append( '<span class="planty_pro_upgrade_process trx_addons_icon-spin3 animate-spin"></span>' );
			// Post license key to the server
			jQuery.post(
				PLANTY_STORAGE['ajax_url'], {
					action: 'planty_get_pro_version',
					nonce: PLANTY_STORAGE['ajax_nonce'],
					license_key: key,
					access_token: token ? token : ''
				}
			).done(
				function(response) {
					var rez = {};
					if (response == '' || response == 0) {
						rez = { error: PLANTY_STORAGE['msg_ajax_error'] };
					} else {
						try {
							var pos = response.indexOf( '{"error":' );
							if (pos > 0) {
								console.log( PLANTY_STORAGE['msg_get_pro_upgrader'] );
								var log = response.substr( 0, pos ),
								msg     = '';
								jQuery( log ).find( 'p' ).each(
									function() {
										msg += (msg !== '' ? "\n" : '') + jQuery( this ).text();
									}
								);
								console.log( msg );
								response = response.substr( pos );
							}
							rez = JSON.parse( response );
						} catch (e) {
							rez = { error: PLANTY_STORAGE['msg_get_pro_error'] };
							console.log( response );
						}
					}
					// Remove progress spin
					jQuery( '.planty_pro_upgrade' )
					.find( 'span.planty_pro_upgrade_process' ).remove();
					// Show result
					alert( rez.error ? rez.error : PLANTY_STORAGE['msg_get_pro_success'] );
					// Reload current page after update (if success)
					if (rez.error == '') {
						location.reload( true );
					}
				}
			);
		};


		// Choice pictogram field
		//----------------------------------
		jQuery( '.planty_options, #customize-theme-controls, #elementor-panel' )
			.on( 'keydown', '.planty_list_choice_item', function( e ) {
				if ( [ 13, 32 ].indexOf( e.which ) >= 0 ) {
					jQuery( this ).trigger( 'click' );
					e.preventDefault();
					return false;
				}
				return true;
			})
			.on( 'click', '.planty_list_choice_item', function(e) {
				var item  = jQuery( this ),
					list  = item.parents('.planty_list_choice'),
					input = list.prev();
				list.find( '.planty_list_active' ).removeClass( 'planty_list_active' );
				item.addClass( 'planty_list_active' );
				input.val( item.data( 'choice' ) ).trigger( 'change' );
				e.preventDefault();
				return false;
			}
		);

		// Switch
		//-----------------------------------
		jQuery( '.planty_options, #customize-theme-controls, #elementor-panel' )
			.on( 'keydown', '.planty_options_item_switch .planty_options_item_holder', function( e ) {
				// If 'Enter', 'Space',  Left' or 'Right' arrow is pressed - switch state of the checkbox
				if ( [ 13, 32, 37, 39 ].indexOf( e.which ) >= 0 ) {
					var fld = jQuery( this ).prev().get( 0 );
					fld.checked = ! fld.checked;
					e.preventDefault();
					return false;
				}
				return true;
			} )
			.on( 'change', '.planty_options_item_switch input[type="checkbox"]', function() {
				var fld = jQuery(this).prev();
				fld.val( jQuery(this).get(0).checked ? 1 : 0 ).trigger('change');
			} );


		// Icon selector
		//-----------------------------------

		// Add icon selector after the menu item classes field
		jQuery( '.edit-menu-item-classes' )
			.on( 'change', function() {
				planty_menu_item_class_changed( jQuery( this ) );
			} )
			.each( function() {
				jQuery( this ).after( '<span class="planty_list_icons_selector" title="' + PLANTY_STORAGE['msg_icon_selector'] + '"></span>' );
				planty_menu_item_class_changed( jQuery( this ) );
			} );

		function planty_menu_item_class_changed(fld) {
			var icon     = planty_get_icon_class( fld.val() );
			var selector = fld.next( '.planty_list_icons_selector' );
			selector.attr( 'class', planty_chg_icon_class( selector.attr( 'class' ), icon ) );
			if ( ! icon) {
				selector.css( 'background-image', '' );
			} else if (icon.indexOf( 'image-' ) >= 0) {
				var list = jQuery( '.planty_list_icons' );
				if (list.length > 0) {
					var bg = list.find( '.' + icon.replace( 'image-', '' ) ).css( 'background-image' );
					if (bg && bg != 'none') {
						selector.css( 'background-image', bg );
					}
				}
			}
		}

		function planty_chg_icon_class(classes, icon, prefix) {
			var chg        = false,
				icon_parts = icon.split( '-' );
			if ( prefix === undefined ) {
				prefix = ['none', 'icon-', 'image-'];
			}
			prefix.push( icon_parts[0] + '-' );
			classes = planty_alltrim( classes ).split( ' ' );
			for (var i = 0; i < classes.length; i++) {
				for (var j = 0; j < prefix.length; j++ ) {
					if (classes[i].indexOf( prefix[j] ) >= 0) {
						classes[i] = [ 'none', 'image-none' ].indexOf( icon ) != -1 ? '' : icon;
						chg        = true;
						break;
					}
				}
				if ( chg ) break;
			}
			if ( ! chg && [ 'none', 'image-none' ].indexOf( icon ) == -1 ) {
				if (classes.length == 1 && classes[0] === '') {
					classes[0] = icon;
				} else {
					classes.push( icon );
				}
			}
			return classes.join( ' ' );
		}

		function planty_get_icon_class(classes) {
			var classes = planty_alltrim( classes ).split( ' ' );
			var icon    = '';
			for (var i = 0; i < classes.length; i++) {
				if (classes[i].indexOf( 'icon-' ) >= 0) {
					icon = classes[i];
					break;
				} else if (classes[i].indexOf( 'image-' ) >= 0) {
					icon = classes[i];
					break;
				}
			}
			return icon;
		}


		// Init other fields
		//-----------------------------------------------------------------------------
		planty_init_fields();
		jQuery(document).on( 'action.init_hidden_elements', planty_init_fields );


		// Init fields at first run and after clone group
		function planty_init_fields(e, container) {
			
			if (container === undefined) {
				container = jQuery('.planty_options,body').eq(0);
			}

			// Icons selector
			//----------------------------------
			container.find( '.planty_list_icons_selector:not(.inited)' ).addClass( 'inited' )
				.on( 'keydown', function( e ) {
					// If 'Enter' or 'Space' is pressed - switch state of the icons list
					if ( [ 13, 32 ].indexOf( e.which ) >= 0 ) {
						jQuery( this ).trigger( 'click' );
						e.preventDefault();
						return false;
					}
					return true;
				})
				.on( 'click', function(e) {
					var selector = jQuery( this );
					var input_id = selector.prev().attr( 'id' );
					if (input_id === undefined) {
						input_id = ('planty_icon_field_' + Math.random()).replace( /\./g, '' );
						selector.prev().attr( 'id', input_id );
					}
					var input_hidden = selector.prev().attr( 'type' ) != 'text';
					var in_menu = selector.parents( '.menu-item-settings' ).length > 0;
					var list    = in_menu ? jQuery( '.planty_list_icons' ) : selector.next( '.planty_list_icons' );
					if (list.length > 0) {
						if (list.css( 'display' ) == 'none') {
							list.find( 'span.planty_list_active' ).removeClass( 'planty_list_active' );
							var icon = planty_get_icon_class( selector.attr( 'class' ) );
							if (icon !== '') {
								list.find( 'span[class*="' + icon.replace( 'image-', '' ) + '"]' ).addClass( 'planty_list_active' );
							}
							var pos = in_menu ? selector.offset() : selector.position();
							list.find( '.planty_list_icons_search' ).val( '' );
							list.find( 'span' ).removeClass( 'planty_list_hidden' );
							list.data( 'input_id', input_id )
								.css( {
									'left': pos.left - (in_menu || input_hidden ? 0 : list.outerWidth() - selector.width() - 1),
									'top': pos.top + (in_menu ? 0 : selector.height() + 10)
								} )
								.fadeIn( 100, function() {
									list.find( '.planty_list_icons_search' ).get(0).focus();
								} );

						} else {
							list.fadeOut( 100 );
							selector.get(0).focus();
						}
					}
					e.preventDefault();
					return false;
				});

			container.find( '.planty_list_icons:not(.inited)' ).addClass( 'inited' )
				.on( 'keyup', '.planty_list_icons_search', function(e) {
					var list = jQuery( this ).parent(),
					val      = jQuery( this ).val();
					list.find( 'span' ).removeClass( 'planty_list_hidden' );
					if (val !== '') {
						list.find( 'span:not([data-icon*="' + val + '"])' ).addClass( 'planty_list_hidden' );
					}
				} )
				.on( 'keydown', 'span', function( e ) {
					var handled = false,
						icons = jQuery( this ).siblings( 'span' );
					// If 'Enter' or 'Space' is pressed - switch state of the icons list
					if ( [ 13, 32 ].indexOf( e.which ) >= 0 ) {
						jQuery( this ).trigger( 'click' );
						handled = true;
					} else if ( 37 == e.which ) {
						icons.get( Math.max( 0, jQuery( this ).index() - 1 ) ).focus();
						handled = true;
					} else if ( 38 == e.which ) {
						icons.get( Math.max( 0, jQuery( this ).index() - 8 ) ).focus();
						handled = true;
					} else if ( 39 == e.which ) {
						icons.get( Math.min( icons.length - 1, jQuery( this ).index() ) ).focus();
						handled = true;
					} else if ( 40 == e.which ) {
						icons.get( Math.min( icons.length - 1, jQuery( this ).index() + 7 ) ).focus();
						handled = true;
					} else if ( [ 27 ].indexOf( e.which ) >= 0 ) {
						jQuery( this ).parents('.planty_list_icons').prev('.planty_list_icons_selector').trigger('click');
						handled = true;
					}
					if ( handled ) {
						e.preventDefault();
						return false;
					}
					return true;
				} )
				.on( 'click', 'span', function(e) {
					var list     = jQuery( this ).parents('.planty_list_icons').fadeOut();
					var input    = jQuery( '#' + list.data( 'input_id' ) );
					var selector = input.next();
					var icon     = planty_alltrim( jQuery( this ).attr( 'class' ).replace( /planty_list_active/, '' ) );
					var bg       = jQuery( this ).css( 'background-image' );
					if (bg && bg != 'none') {
						icon = 'image-' + icon;
					}
					input.val( planty_chg_icon_class( input.val(), icon ) ).trigger( 'change' );
					selector
						.attr( 'class', planty_chg_icon_class( selector.attr( 'class' ), icon ) )
						.css('background-image', bg && bg != 'none' ? bg : 'none')
						.get(0).focus();
					e.preventDefault();
					return false;
				} );


			// Checklist
			//------------------------------------------------------
			container.find( '.planty_checklist:not(.inited)' ).addClass( 'inited' )
				.on( 'change', 'input[type="checkbox"]', function() {
					var choices = '';
					var cont    = jQuery( this ).parents( '.planty_checklist' );
					cont.find( 'input[type="checkbox"]' ).each(
						function() {
							choices += (choices ? '|' : '') + jQuery( this ).data( 'name' ) + '=' + (jQuery( this ).get( 0 ).checked ? jQuery( this ).val() : '0');
						}
					);
					cont.siblings( 'input[type="hidden"]' ).eq( 0 ).val( choices ).trigger( 'change' );
				} )
				.each( function() {
					if (jQuery.ui.sortable && jQuery( this ).hasClass( 'planty_sortable' )) {
						var id = jQuery( this ).attr( 'id' );
						if (id === undefined) {
							jQuery( this ).attr( 'id', 'planty_sortable_' + ('' + Math.random()).replace( '.', '' ) );
						}
						jQuery( this ).sortable(
							{
								items: ".planty_sortable_item",
								placeholder: ' planty_checklist_item_label planty_sortable_item planty_sortable_placeholder',
								update: function(event, ui) {
									var choices = '';
									ui.item.parent().find( 'input[type="checkbox"]' ).each(
										function() {
											choices += (choices ? '|' : '')
											+ jQuery( this ).data( 'name' ) + '=' + (jQuery( this ).get( 0 ).checked ? jQuery( this ).val() : '0');
										}
									);
									ui.item.parent().siblings( 'input[type="hidden"]' ).eq( 0 ).val( choices ).trigger( 'change' );
								}
							}
						)
						.disableSelection();
					}
				} );

			// Range Slider
			//------------------------------------
			if (jQuery.ui && jQuery.ui.slider) {
				container.find( '.planty_range_slider:not(.inited)' ).addClass( 'inited' )
					.each( function () {
						// Get parameters
						var range_slider = jQuery( this );
						var linked_field = range_slider.data( 'linked_field' );
						if (linked_field === undefined) {
							linked_field = range_slider.siblings( 'input[type="hidden"],input[type="text"]' );
						} else {
							linked_field = jQuery( '#' + linked_field );
						}
						if (linked_field.length == 0) {
							return;
						}
						linked_field.on(
							'change', function() {
								var minimum = range_slider.data( 'min' );
								if ( minimum === undefined ) {
									minimum = 0;
								} else {
									minimum = Number( ( '' + minimum ).replace( ',', '.' ) );
								}
								var maximum = range_slider.data( 'max' );
								if ( maximum === undefined ) {
									maximum = 0;
								} else {
									maximum = Number( ( '' + maximum ).replace( ',', '.' ) );
								}
								var values = jQuery( this ).val().split( ',' );
								for (var i = 0; i < values.length; i++) {
									if ( values[i] !== '' ) {
										if ( isNaN( values[i] ) ) {
											value[i] = minimum;
										}
										values[i] = Math.max( minimum, Math.min( maximum, Number( values[i] ) ) );
									}
									if ( values.length == 1 ) {
										range_slider.slider( 'value', values );
									} else {
										range_slider.slider( 'values', i, values[i] );
									}
								}
								update_cur_values( values );
								jQuery( this ).val( values.join( ',' ) );
							}
						);
						var range_slider_cur  = range_slider.find( '> .planty_range_slider_label_cur' );
						var range_slider_type = range_slider.data( 'range' );
						if ( range_slider_type === undefined ) {
							range_slider_type = 'min';
						}
						var values  = linked_field.val().split( ',' );
						var minimum = range_slider.data( 'min' );
						if ( minimum === undefined ) {
							minimum = 0;
						} else {
							minimum = Number( ( '' + minimum ).replace( ',', '.' ) );
						}
						var maximum = range_slider.data( 'max' );
						if ( maximum === undefined ) {
							maximum = 0;
						} else {
							maximum = Number( ( '' + maximum ).replace( ',', '.' ) );
						}
						var step = range_slider.data( 'step' );
						if ( step === undefined ) {
							step = 1;
						} else {
							step = Number( ( '' + step ).replace( ',', '.' ) );
						}
						// Init range slider
						var init_obj = {
							range: range_slider_type,
							min: minimum,
							max: maximum,
							step: step,
							slide: function(event, ui) {
								var cur_values = range_slider_type === 'min' ? [ui.value] : ui.values;
								linked_field.val( cur_values.join( ',' ) ).trigger( 'change' );
								update_cur_values( cur_values );
							},
							create: function(event, ui) {
								update_cur_values( values );
							}
						};
						function update_cur_values(cur_values) {
							for ( var i = 0; i < cur_values.length; i++ ) {
								range_slider_cur.eq( i )
									.html( cur_values[i] )
									.css( 'left', Math.max( 0, Math.min( 100, ( ( cur_values[i] === '' ? 0 : cur_values[i] ) - minimum ) * 100 / ( maximum - minimum ) ) ) + '%' );
							}
						}
						if ( range_slider_type === true ) {
							init_obj.values = values;
						} else {
							init_obj.value = values[0];
						}
						range_slider.addClass( 'inited' ).slider( init_obj );
					} );
			}

			// Standard WP Color Picker
			//-------------------------------------------------
			if (container.find( '.planty_color_selector' ).length > 0) {
				container.find( '.planty_color_selector' ).wpColorPicker(
					{
						// you can declare a default color here,
						// or in the data-default-color attribute on the input
						//defaultColor: false,

						// a callback to fire whenever the color changes to a valid color
						change: function(e, ui){
							jQuery( e.target ).val( ui.color ).trigger( 'change' );
						},

						// a callback to fire when the input is emptied or an invalid color
						clear: function(e) {
							jQuery( e.target ).prev().trigger( 'change' );
						}

						// hide the color picker controls on load
						//hide: true,

						// show a group of common colors beneath the square
						// or, supply an array of colors to customize further
						//palettes: true
					}
				);
			}

			// Media selector
			//--------------------------------------------
			if ( typeof( PLANTY_STORAGE['media_frame'] ) == 'undefined' ) {
				PLANTY_STORAGE['media_frame'] = {};
				PLANTY_STORAGE['media_link']  = {};
			}
			container.find( '.planty_media_selector:not(.inited)' ).addClass( 'inited' )
				.on( 'click', function(e) {
					planty_show_media_manager( this );
					e.preventDefault();
					return false;
				}
			);
			container.find( '.planty_media_selector_preview:not(.inited)' ).addClass( 'inited' )
				.on( 'keydown', '> .planty_media_selector_preview_image', function(e) {
					// If 'Enter' or 'Space' is pressed - remove image
					if ( [ 13, 32 ].indexOf( e.which ) >= 0 ) {
						jQuery( this ).trigger('click');
						e.preventDefault();
						return false;
					}
					return true;
				} )
				.on( 'click', '> .planty_media_selector_preview_image', function(e) {
					var image   = jQuery( this ),
						preview = image.parent(),
						button  = preview.siblings( '.planty_media_selector' ),
						field   = jQuery( '#' + button.data( 'linked-field' ) );
					if (field.length === 0) {
						return true;
					}
					if (button.data( 'multiple' ) == 1) {
						var val = field.val().split( '|' );
						val.splice( image.index(), 1 );
						field.val( val.join( '|' ) ).trigger( 'change' );
						image.remove();
					} else {
						field.val( '' ).trigger( 'change' );
						image.remove();
					}
					preview.toggleClass('planty_media_selector_preview_with_image', preview.find('> .planty_media_selector_preview_image').length > 0);
					e.preventDefault();
					return false;
				}
			);

			function planty_show_media_manager(el) {
				var media_id                            = jQuery( el ).attr( 'id' );
				PLANTY_STORAGE['media_link'][media_id] = jQuery( el );
				// If the media frame already exists, reopen it.
				if ( PLANTY_STORAGE['media_frame'][media_id] ) {
					PLANTY_STORAGE['media_frame'][media_id].open();
					return false;
				}
				var type = PLANTY_STORAGE['media_link'][media_id].data( 'type' )
							? PLANTY_STORAGE['media_link'][media_id].data( 'type' )
							: 'image';
				var args = {
					// Set the title of the modal.
					title: PLANTY_STORAGE['media_link'][media_id].data( 'choose' ),
					// Multiple choise
					multiple: PLANTY_STORAGE['media_link'][media_id].data( 'multiple' ) == 1
							? 'add'
							: false,
					// Customize the submit button.
					button: {
						// Set the text of the button.
						text: PLANTY_STORAGE['media_link'][media_id].data( 'update' ),
						// Tell the button not to close the modal, since we're
						// going to refresh the page when the image is selected.
						close: true
					}
				};
				// Allow sizes and filters for the images
				if (type == 'image') {
					args['frame'] = 'post';
				}
				// Tell the modal to show only selected post types
				if (type == 'image' || type == 'audio' || type == 'video') {
					args['library'] = {
						type: type
					};
				}
				PLANTY_STORAGE['media_frame'][media_id] = wp.media( args );

				// When an image is selected, run a callback.
				PLANTY_STORAGE['media_frame'][media_id].on( 'insert select', function(selection) {
					// Grab the selected attachment.
					var field      = jQuery( "#" + PLANTY_STORAGE['media_link'][media_id].data( 'linked-field' ) ).eq( 0 );
					var attachment = null, attachment_url = '';
					if (PLANTY_STORAGE['media_link'][media_id].data( 'multiple' ) === 1) {
						PLANTY_STORAGE['media_frame'][media_id].state().get( 'selection' ).map(
							function( att ) {
								attachment_url += (attachment_url ? "|" : "") + att.toJSON().url;
							}
						);
						var val        = field.val();
						attachment_url = val + (val ? "|" : '') + attachment_url;
					} else {
						attachment         = PLANTY_STORAGE['media_frame'][media_id].state().get( 'selection' ).first().toJSON();
						attachment_url     = attachment.url;
						// Get attachment size (for compatibility with old versions)
						if ( false && type == 'image' ) {
							var sizes_selector = jQuery( '.media-modal-content .attachment-display-settings select.size' );
							if (sizes_selector.length > 0) {
								var size = planty_get_listbox_selected_value( sizes_selector.get( 0 ) );
								if (size !== '') {
									attachment_url = attachment.sizes[size].url;
								}
							}
						}
					}
					// Display images in the preview area
					var preview = field.siblings( '.planty_media_selector_preview' );
					if (preview.length === 0) {
						jQuery( '<span class="planty_media_selector_preview"></span>' ).insertAfter( field );
						preview = field.siblings( '.planty_media_selector_preview' );
					}
					if (preview.length !== 0) {
						preview.find('.planty_media_selector_preview_image').remove();
					}
					var images = attachment_url.split( "|" );
					for (var i = 0; i < images.length; i++) {
						if (preview.length !== 0) {
							var ext = planty_get_file_ext( images[i] );
							preview.append(
									'<span class="planty_media_selector_preview_image" tabindex="0">'
										+ (ext == 'gif' || ext == 'jpg' || ext == 'jpeg' || ext == 'png'
												? '<img src="' + images[i] + '">'
												: '<a href="' + images[i] + '">' + planty_get_file_name( images[i] ) + '</a>'
											)
									+ '</span>'
								);
						}
					}
					preview.toggleClass('planty_media_selector_preview_with_image', preview.find('> .planty_media_selector_preview_image').length > 0);
					// Update field
					field.val( attachment_url ).trigger( 'change' );
				} );

				// Finally, open the modal.
				PLANTY_STORAGE['media_frame'][media_id].open();
				return false;
			}

		}
	}
);
