/* global planty_color_schemes, planty_dependencies, Color */

/**
 * Add a listener to the Color Scheme control to update other color controls to new values/defaults.
 * Also trigger an update of the Color Scheme CSS when a color is changed.
 */

( function( api ) {

	"use strict";

	var cssTemplate = {},
		updateCSS   = true,
		htmlEncoder = document.createElement( 'div' );

	// Add Templates with color schemes
	for (var i in planty_color_schemes) {
		cssTemplate['scheme_' + i] = wp.template( 'planty-color-scheme-' + i );
	}
	// Add Template with theme fonts
	cssTemplate['theme_fonts'] = wp.template( 'planty-fonts' );
	// Add Template with theme vars
	cssTemplate['theme_vars'] = wp.template( 'planty-vars' );

	// Set initial state of controls
	api.bind( 
		'ready', function() {

			// Add 'reset' button
			jQuery( '#customize-header-actions #save' )
				.before( '<input type="button" class="button customize-action-reset" value="' + planty_customizer_vars['msg_reset'] + '">' );
			jQuery( '#customize-header-actions .customize-action-reset' )
				.on( 'click', function(e) {
					if (confirm( planty_customizer_vars['msg_reset_confirm'] )) {
						api( 'reset_options' ).set( 1 );
						jQuery( '#customize-header-actions #save' ).removeAttr( 'disabled' ).trigger( 'click' );
						setTimeout( function() { location.reload( true ); }, 1000 );
					}
				} );

			// Add 'Refresh' button
			jQuery( '#customize-header-actions .spinner' )
				.after( '<button class="button customize-action-refresh icon-spin3" title="' + planty_customizer_vars['msg_reload_title'] + '">' + planty_customizer_vars['msg_reload'] + '</button>' );
			jQuery( '#customize-header-actions .customize-action-refresh' )
				.on( 'click', function(e) {
					api.previewer.send( 'refresh-preview' );
					setTimeout( function() { api.previewer.refresh(); }, 500 );
					e.preventDefault();
					return false;
				} );

			// Add suffix after the theme's name
			if (planty_customizer_vars && planty_customizer_vars['theme_name_suffix']) {
				jQuery( '#customize-info .site-title' ).append( planty_customizer_vars['theme_name_suffix'] );
			}

			// Blur the "load fonts" fields - regenerate options lists in the font-family controls
			jQuery( '#customize-theme-controls [id^="customize-control-load_fonts"]' )
				.on( 'change', planty_customizer_update_load_fonts );

			// Move all descriptions after the fields
			jQuery( '#customize-theme-controls .customize-control:not(.customize-control-info)' )
				.each( function() {
					var $self = jQuery( this ),
						$note = $self.find('.customize-control-notifications-container'),
						$desc = $self.find('.customize-control-description');
					if ( $note.length ) {
						$note.appendTo( $self );
					}
					if ( $desc.length ) {
						$desc.appendTo( $self );
					}
				} );

			// Click on the actions button
			jQuery( '#customize-theme-controls .control-section .customize-control-button input[type="button"]' )
				.on( 'click', planty_customizer_actions );

			// Check dependencies in the each section on ready
			jQuery( '#customize-theme-controls .control-section' )
				.each( function () {
					planty_customizer_check_dependencies( jQuery( this ) );
				} );

			// Check dependencies in the each section before open (for dinamically created sections)
			jQuery( '#customize-theme-controls .control-section > .accordion-section-title' )
				.on( 'click', function() {
					var id = jQuery( this ).parent().attr( 'aria-owns' );
					if (id !== '') {
						var section = jQuery( '#' + id );
						if (section.length > 0) {
							planty_customizer_check_dependencies( section );
						}
					}
				} );

			// Decorate subsections in the panel 'Typography'
			if ( planty_customizer_vars['decorate_fonts'] ) {

				jQuery('body').addClass( 'planty_decorate_fonts_section' );

				// Prepare section for open on title is clicked
				var planty_customizer_title_clicked = function( title ) {
					var id = title.parent().attr( 'aria-owns' );
					planty_customizer_close_opened_sections( title.parents( '#sub-accordion-panel-fonts' ), id );
					var section = jQuery( '#' + id ),
						section_height = section.outerHeight(),
						title_height = title.outerHeight() + 6,
						pos = title.position().top + title_height,
						sidebar = jQuery('.wp-full-overlay-sidebar-content'),
						sidebar_height = sidebar.height();
					section.css( 'margin-top', pos + 'px' );
					setTimeout( function() {
						jQuery( '#customize-theme-controls [id^="sub-accordion-section-"][id$="_font_section"]:not(.open)' ).css('margin-top', 0);
						sidebar.scrollLeft(0);
						if ( pos + section_height > sidebar.scrollTop() + sidebar_height ) {
							sidebar.scrollTop( section_height < sidebar_height ? pos + section_height - sidebar_height : pos - title_height );
						}
						jQuery( '#customize-theme-controls [id^="sub-accordion-section-"][id$="_font_section"].open [data-customize-setting-link]' ).eq(0).focus();
					}, 330 );
				};

				// Set y position for opened section in the font settings
				jQuery( '#customize-theme-controls [id^="accordion-section-"][id$="_font_section"] .accordion-section-title:not(.inited)' )
					.each ( function() {
						var $self = jQuery(this).addClass('inited');
						$self
							.on( 'keydown', function(e) {
								if ( e.which == 13 ) {
									planty_customizer_title_clicked( $self );
								}
							} )
							.on( 'click', function() {
								planty_customizer_title_clicked( $self );
							} );
					} );

				// Close all opened sections in the specified panel
				var planty_customizer_close_opened_sections = function( panel, current_id ) {
					if ( panel.length ) {
						panel.find( '.control-section[aria-owns]' ).each( function() {
							var $self = jQuery( this ),
								id = $self.attr( 'aria-owns' );
							if ( id != current_id ) {
								jQuery( '#' + id + '.open .customize-section-back' ).trigger( 'click' );
							}
						} );
					}
				};

				// Close opened section in the font settings on click button '.customize-panel-back'
				jQuery( '#customize-theme-controls #sub-accordion-panel-fonts .customize-panel-back' )
					.on( 'keydown', function(e) {
						if ( e.which == 13 ) {
							planty_customizer_close_opened_sections( jQuery(this).parents('#sub-accordion-panel-fonts') );
						}
					} )
					.on( 'click', function() {
						planty_customizer_close_opened_sections( jQuery(this).parents('#sub-accordion-panel-fonts') );
					} );
			}
		}
	);

	// On change any control - check for dependencies
	api.bind( 'change', function(obj) {
		// Correct value for the added color schemes
		var id = obj.id, val = obj();
		if (val === undefined && id.substr( -7 ) == '_scheme') {
			val = jQuery( '[data-customize-setting-link="' + id + '"]:checked' ).val();
			if (val !== undefined) {
				obj.set( val );
				return;
			}
		}
		planty_customizer_check_dependencies( jQuery( '#customize-theme-controls #customize-control-' + obj.id ).parents( '.control-section' ) );
		planty_customizer_refresh_preview( obj );
	} );

	// On add/delete scheme
	api.bind( 'refresh_schemes', function() {
		var i = '', tpl_idx = '', tpl_content = '';
		// Remove templates
		for (i in cssTemplate) {
			if (('' + i).indexOf( 'scheme_' ) !== 0) {
				continue;
			}
			i = i.replace( 'scheme_', '' );
			if (typeof planty_color_schemes[i] === 'undefined') {
				delete cssTemplate['scheme_' + i];
				jQuery( '#tmpl-planty-color-scheme-' + i ).remove();
			} else if (tpl_idx == '') {
				tpl_idx     = i;
				tpl_content = jQuery( '#tmpl-planty-color-scheme-' + i ).html().trim();
			}
		}
		// Add new templates
		var regex = new RegExp( "\.scheme_" + tpl_idx, "g" );
		for (i in planty_color_schemes) {
			if (typeof cssTemplate['scheme_' + i] === 'undefined') {
				jQuery( '#tmpl-planty-color-scheme-' + tpl_idx )
				.clone()
				.attr( 'id', 'tmpl-planty-color-scheme-' + i )
				.html( tpl_content.replace( regex, '.scheme_' + i ) )
				.insertAfter( jQuery( '#tmpl-planty-color-scheme-' + tpl_idx ) );
				cssTemplate['scheme_' + i] = wp.template( 'planty-color-scheme-' + i );
			}
		}
	} );

	// Disable/Enable update CSS
	api.bind( 'lock_css', function(lock) {
		updateCSS = ! lock;
	} );

	// Open specified url on expand section or panel
	for (var action in planty_customizer_vars['actions']['expand']) {
		if (action == 'length') {
			continue;
		}
		if (planty_customizer_vars['actions']['expand'][action]['type'] == 'panel') {
			api.panel(
				action, function( panel ) {
					panel.expanded.bind(
						function( isExpanded ) {
							if ( isExpanded ) {
								var data = planty_customizer_vars['actions']['expand'][panel.id];
								if (typeof data['url'] !== 'undefined' && data['url'] !== '') {
									api.previewer.previewUrl.set( data['url'] );
								}
								if (typeof data['callback'] !== 'undefined' && data['callback'] !== '' && typeof window[data['callback']] === 'function') {
									window[data['callback']]();
								}
							}
						}
					);
				}
			);
		} else {
			api.section(
				action, function( section ) {
					section.expanded.bind(
						function( isExpanded ) {
							if ( isExpanded ) {
								var data = planty_customizer_vars['actions']['expand'][section.id];
								if (typeof data['url'] !== 'undefined' && data['url'] !== '') {
									api.previewer.previewUrl.set( data['url'] );
								}
								if (typeof data['callback'] !== 'undefined' && data['callback'] !== '' && typeof window[data['callback']] === 'function') {
									window[data['callback']]();
								}
							}
						}
					);
				}
			);
		}
	}

	// Return value of the control
	function planty_customizer_get_field_value(fld) {
		var ctrl = fld.parents( '.customize-control' );
		var val  = fld.attr( 'type' ) == 'checkbox' || fld.attr( 'type' ) == 'radio'
					? (ctrl.find( '[data-customize-setting-link]:checked' ).length > 0
						? (ctrl.find( '[data-customize-setting-link]:checked' ).val() !== ''
							&& '' + ctrl.find( '[data-customize-setting-link]:checked' ).val() != '0'
								? ctrl.find( '[data-customize-setting-link]:checked' ).val()
								: 1
							)
						: 0
						)
					: fld.val();
		if (val === undefined || val === null) {
			val = '';
		}
		return val;
	}

	// Check for dependencies
	function planty_customizer_check_dependencies(cont) {
		if ( typeof planty_dependencies == 'undefined' || PLANTY_STORAGE['check_dependencies_now'] ) return;
		PLANTY_STORAGE['check_dependencies_now'] = true;
		cont.find( '.customize-control' ).each(
			function() {
				var ctrl = jQuery( this ), id = ctrl.attr( 'id' );
				if (id == undefined) {
					return;
				}
				id         = id.replace( 'customize-control-', '' );
				var fld    = null, val = '', i;
				var depend = false;
				for (fld in planty_dependencies) {
					if (fld == id) {
						depend = planty_dependencies[id];
						break;
					}
				}
				if (depend) {
					var dep_cnt    = 0, dep_all = 0;
					var dep_cmp    = typeof depend.compare != 'undefined' ? depend.compare.toLowerCase() : 'and';
					var dep_strict = typeof depend.strict != 'undefined';
					for (i in depend) {
						if (i == 'compare' || i == 'strict') {
							continue;
						}
						dep_all++;
						fld = cont.find( '[data-customize-setting-link="' + i + '"]' );
						if (fld.length > 0) {
							val = planty_customizer_get_field_value( fld );
							if ( val == 'inherit' ) {
								dep_cnt = 0;
								dep_all = 1;
								var tag;
								if ( ctrl.hasClass( 'customize-control-select' ) ) {
									tag = ctrl.find('select');
									if ( tag.find('option[value="inherit"]').length && tag.val() != 'inherit' ) {
										tag.val('inherit').trigger('change');
									}
								} else if ( ctrl.hasClass( 'customize-control-radio' ) ) {
									tag = ctrl.find('input[type="radio"][value="inherit"]');
									if ( tag.length && ! tag.get(0).checked ) {
										ctrl.find('input[type="radio"]:checked').get(0).checked = false;
										tag.get(0).checked = true;
										tag.trigger('change');
									}
								}
								break;
							} else {
								if ( typeof depend[i] != 'object' && typeof depend[i] != 'array' ) {
									depend[i] = { '0': depend[i] };
								}
								for (var j in depend[i]) {
									if (
										(depend[i][j] == 'not_empty' && val !== '')      // Main field value is not empty - show current field
										|| (depend[i][j] == 'is_empty' && val === '')    // Main field value is empty - show current field
										|| (val !== '' && ( ! isNaN( depend[i][j] )      // Main field value equal to specified value - show current field
															? val == depend[i][j]
															: (dep_strict
																	? val == depend[i][j]
																	: ('' + val).indexOf( depend[i][j] ) === 0
																)
														)
											)
										|| (val !== '' && ('' + depend[i][j]).charAt( 0 ) == '^' && ('' + val).indexOf( depend[i][j].substr( 1 ) ) == -1)	// Main field value not equal to specified value - show current field
									) {
										dep_cnt++;
										break;
									}
								}
							}
						} else {
							dep_all--;
						}
						if (dep_cnt > 0 && dep_cmp == 'or') {
							break;
						}
					}
					if (((dep_cnt > 0 || dep_all === 0) && dep_cmp == 'or') || (dep_cnt == dep_all && dep_cmp == 'and')) {
						ctrl.slideDown().removeClass( 'planty_options_no_use' );
					} else {
						ctrl.slideUp().addClass( 'planty_options_no_use' );
					}
				}

				// Individual dependencies
				//------------------------------------

				// Disable color schemes less then main scheme!
				// Not need for version with sorted schemes
				if (false && id == 'color_scheme') {
					fld = ctrl.find( '[data-customize-setting-link="' + id + '"]' );
					if (fld.length > 0) {
						val     = planty_customizer_get_field_value( fld );
						var num = 0;
						for (i in planty_color_schemes) {
							num++;
							if (i == val) {
								break;
							}
						}
						cont.find( '.customize-control' ).each(
							function() {
								var ctrl2 = jQuery( this ), id2 = ctrl2.attr( 'id' );
								if (id2 == undefined) {
									return;
								}
								id2 = id2.replace( 'customize-control-', '' );
								if (id2 == id || id2.substr( -7 ) != '_scheme') {
									return;
								}
								var fld2 = ctrl2.find( '[data-customize-setting-link="' + id2 + '"]' );
								if (fld2.attr( 'type' ) != 'radio') {
									fld2 = fld2.find( 'option' );
								}
								fld2.each(
									function(idx2) {
										var dom_obj      = jQuery( this ).get( 0 );
										dom_obj.disabled = idx2 !== 0 && idx2 < num;
										if (dom_obj.disabled) {
											if (jQuery( this ).val() == api( id2 )()) {
												api( id2 ).set( 'inherit' );
											}
										}
									}
								);
							}
						);
					}
				}
			}
		);
		PLANTY_STORAGE['check_dependencies_now'] = false;
	}

	// Refresh preview area on change any control
	function planty_customizer_refresh_preview(obj) {
		var id = obj.id, val = obj(), opt = '', rule = '';
		if (obj.transport != 'postMessage' && id.indexOf( 'load_fonts-' ) == -1) {
			return;
		}
		var processed = false, forceUpdateCSS = false;
		// Update the CSS whenever a color setting is changed.
		if (updateCSS) {
			// Any color in the scheme_storage is changed
			if (id == 'scheme_storage') {
				processed = true;

				// Any color in the scheme_storage is changed
			} else if (Object.values( planty_sorted_schemes ).indexOf( id ) >= 0) {
				forceUpdateCSS = true;

				// If section Front page section 'About' need page content - refresh preview area
			} else if (id == 'front_page_about_content' && val.indexOf( '%%CONTENT%%' ) >= 0) {
				api.previewer.send( 'refresh-preview' );
				setTimeout( function() { api.previewer.refresh(); }, 500 );
				processed = true;

				// If control from the theme vars
			} else if (jQuery( '[data-customize-setting-link="' + id + '"]' ).length > 0) {
				var var_name = jQuery( '[data-customize-setting-link="' + id + '"]' ).data( 'var_name' );
				if (var_name !== undefined) {
					// Store new value to the vars table
					planty_customizer_update_theme_vars( jQuery( '[data-customize-setting-link="' + id + '"]' ).data( 'var_name' ), val );
					processed = true;
				}

				// Any theme fonts parameter is changed
			} else {
				for (opt in planty_theme_fonts) {
					for (rule in planty_theme_fonts[opt]) {
						if (opt + '_' + rule == id) {
							// Store new value to the fonts table
							planty_customizer_update_theme_fonts( opt, rule, val );
							processed = true;
							break;
						}
					}
					if (processed) {
						break;
					}
				}
			}
			// Refresh CSS
			if (processed || forceUpdateCSS) {
				planty_customizer_update_css();
			}
		}
		// If not catch change above - send message to previewer
		if ( ! processed) {
			api.previewer.send( 'refresh-other-controls', {id: id, value: val} );
		}
	}

	// Actions buttons
	function planty_customizer_actions(e) {
		var action = jQuery( this ).data( 'action' );
		if (action == 'refresh') {
			api.previewer.send( 'refresh-preview' );
			setTimeout( function() { api.previewer.refresh(); }, 500 );
		}
	}

	// Store new value in the theme vars
	function planty_customizer_update_theme_vars(opt, value) {
		planty_theme_vars[opt] = parseFloat( value );
	}

	// Store new value in the theme fonts
	function planty_customizer_update_theme_fonts(opt, rule, value) {
		planty_theme_fonts[opt][rule] = value;
	}

	// Change theme fonts options if load fonts is changed
	function planty_customizer_update_load_fonts() {
		var opt_list = [], i, tag, sel, opt, name = '', family = '', val = '', new_val = '', sel_idx = 0;
		updateCSS    = false;
		for (i = 1; i <= planty_customizer_vars['max_load_fonts']; i++) {
			name = api( 'load_fonts-' + i + '-name' )();
			if (name === '') {
				continue;
			}
			family = api( 'load_fonts-' + i + '-family' )();
			opt_list.push( [name, family] );
		}
		for (tag in planty_theme_fonts) {
			sel = api.control( tag + '_font-family' ).container.find( 'select' );
			if (sel.length == 1) {
				opt     = sel.find( 'option' );
				sel_idx = sel.find( ':selected' ).index();
				// Remove empty options
				if (opt_list.length < opt.length - 1) {
					for (i = opt.length - 1; i > opt_list.length; i--) {
						opt.eq( i ).remove();
					}
				}
				// Add new options
				if (opt_list.length >= opt.length) {
					for (i = opt.length - 1; i <= opt_list.length - 1; i++) {
						val = planty_get_load_fonts_family_string( opt_list[i][0], opt_list[i][1] );
						sel.append( '<option value="' + val + '">' + opt_list[i][0] + '</option>' );
					}
				}
				// Set new value
				new_val = '';
				for (i = 0; i < opt_list.length; i++) {
					val = planty_get_load_fonts_family_string( opt_list[i][0], opt_list[i][1] );
					if (sel_idx - 1 == i) {
						new_val = val;
					}
					opt.eq( i + 1 ).val( val ).text( opt_list[i][0] );
				}
				api( tag + '_font-family' ).set( sel_idx > 0 && sel_idx <= opt_list.length && new_val ? new_val : 'inherit' );
			}
		}
		updateCSS = true;
	}

	// Generate the CSS for the current Color Scheme and send it to the preview window
	function planty_customizer_update_css() {

		if ( ! updateCSS) {
			return;
		}
		var css = '';

		// Add theme specific vars
		var vars = planty_customizer_add_theme_vars( planty_theme_vars );

		// Make styles and add its to the css
		css += planty_customizer_prepare_html_value( cssTemplate['theme_vars']( vars ) );

		// Add theme specific fonts rules
		var fonts = planty_customizer_add_theme_fonts( planty_theme_fonts );

		// Make styles and add its to the css
		css += planty_customizer_prepare_html_value( cssTemplate['theme_fonts']( fonts ) );

		// Add colors
		var sorted = {}, scheme = '';
		for (var param in planty_sorted_schemes) {
			if (param == 'length') {
				continue;
			}
			scheme = api( planty_sorted_schemes[param] )();
			if (scheme && scheme !== 'inherit') {
				sorted[scheme] = 1;
			}
		}
		for (scheme in planty_color_schemes) {
			sorted[scheme] = 1;
		}
		for (scheme in sorted) {
			var i, colors = [];
			// Copy all colors to the temp var
			for (i in planty_color_schemes[scheme].colors) {
				colors[i] = planty_color_schemes[scheme].colors[i];
			}
			// Add theme specific colors and tints
			colors = planty_customizer_add_theme_colors( colors );

			// Make styles and add its to the css

			// Attention! This way generate error 'Maximum call stack size exceeded' in Chrome!
			// css += cssTemplate['scheme_'+scheme]( colors );

			// This way work correctly in any browser
			var tmpl = jQuery( '#tmpl-planty-color-scheme-' + scheme ).html().trim();
			for (i in colors) {
				var regex = new RegExp( "{{ data\." + i + " }}", "g" );
				tmpl      = tmpl.replace( regex, colors[i] );
			}
			css += tmpl;
		}
		api.previewer.send( 'refresh-color-scheme-css', css );
	}

	// Additional (calculated) theme-specific colors
	function planty_customizer_add_theme_colors(colors) {
		if (planty_additional_colors) {
			var clr = '', v = '';
			for (var k in planty_additional_colors) {
				v   = planty_additional_colors[k];
				clr = colors[v['color']];
				if (typeof v['hue'] != 'undefined' || typeof v['saturation'] != 'undefined' || typeof v['brightness'] != 'undefined') {
					clr = planty_hsb2hex(
						planty_hex2hsb(
							clr,
							typeof v['hue'] != 'undefined' ? v['hue'] : 0,
							typeof v['saturation'] != 'undefined' ? v['saturation'] : 0,
							typeof v['brightness'] != 'undefined' ? v['brightness'] : 0
						)
					);
				}
				if (typeof v['alpha'] != 'undefined') {
					clr = Color( clr ).toCSS( 'rgba', v['alpha'] );
				}
				colors[k] = clr;
			}
		}
		return colors;
	}

	// Add custom theme fonts rules
	function planty_customizer_add_theme_fonts(fonts) {
		var rez = [];
		for (var tag in fonts) {
			
			rez[tag] = fonts[tag];
			
			rez[tag + '_font-family']     = typeof fonts[tag]['font-family'] != 'undefined'
									&& fonts[tag]['font-family'] !== ''
									&& fonts[tag]['font-family'] != 'inherit'
												? 'font-family:' + fonts[tag]['font-family'] + ';'
												: '';
			rez[tag + '_font-size']       = typeof fonts[tag]['font-size'] != 'undefined'
									&& fonts[tag]['font-size'] != 'inherit'
												? 'font-size:' + planty_customizer_prepare_css_value( fonts[tag]['font-size'] ) + ";"
												: '';
			rez[tag + '_line-height']     = typeof fonts[tag]['line-height'] != 'undefined'
									&& fonts[tag]['line-height'] !== ''
									&& fonts[tag]['line-height'] != 'inherit'
												? 'line-height:' + fonts[tag]['line-height'] + ";"
												: '';
			rez[tag + '_font-weight']     = typeof fonts[tag]['font-weight'] != 'undefined'
									&& fonts[tag]['font-weight'] !== ''
									&& fonts[tag]['font-weight'] != 'inherit'
												? 'font-weight:' + fonts[tag]['font-weight'] + ";"
												: '';
			rez[tag + '_font-style']      = typeof fonts[tag]['font-style'] != 'undefined'
									&& fonts[tag]['font-style'] !== ''
									&& fonts[tag]['font-style'] != 'inherit'
												? 'font-style:' + fonts[tag]['font-style'] + ";"
												: '';
			rez[tag + '_text-decoration'] = typeof fonts[tag]['text-decoration'] != 'undefined'
									&& fonts[tag]['text-decoration'] !== ''
									&& fonts[tag]['text-decoration'] != 'inherit'
												? 'text-decoration:' + fonts[tag]['text-decoration'] + ";"
												: '';
			rez[tag + '_text-transform']  = typeof fonts[tag]['text-transform'] != 'undefined'
									&& fonts[tag]['text-transform'] !== ''
									&& fonts[tag]['text-transform'] != 'inherit'
												? 'text-transform:' + fonts[tag]['text-transform'] + ";"
												: '';
			rez[tag + '_letter-spacing']  = typeof fonts[tag]['letter-spacing'] != 'undefined'
									&& fonts[tag]['letter-spacing'] !== ''
									&& fonts[tag]['letter-spacing'] != 'inherit'
												? 'letter-spacing:' + fonts[tag]['letter-spacing'] + ";"
												: '';
			rez[tag + '_margin-top']      = typeof fonts[tag]['margin-top'] != 'undefined'
									&& fonts[tag]['margin-top'] !== ''
									&& fonts[tag]['margin-top'] != 'inherit'
												? 'margin-top:' + planty_customizer_prepare_css_value( fonts[tag]['margin-top'] ) + ";"
												: '';
			rez[tag + '_margin-bottom']   = typeof fonts[tag]['margin-bottom'] != 'undefined'
									&& fonts[tag]['margin-bottom'] !== ''
									&& fonts[tag]['margin-bottom'] != 'inherit'
												? 'margin-bottom:' + planty_customizer_prepare_css_value( fonts[tag]['margin-bottom'] ) + ";"
												: '';
		}
		return rez;
	}

	// Add custom theme vars rules
	function planty_customizer_add_theme_vars(vars) {
		var rez = [];
		if ( typeof vars['rad'] != 'undefined' ) {
			if (vars['rad'] == '') {
				vars['rad'] = 0;
			}
			rez['rad']      = planty_customizer_prepare_css_value( vars['rad'] );
			rez['rad_koef'] = vars['rad'] > 0 ? 1 : 0;
		}
		if ( typeof vars['page_width'] != 'undefined' ) {
			vars['page_width'] = parseInt( vars['page_width'], 10 );
			if ( isNaN( vars['page_width'] ) || vars['page_width'] === 0) {
				vars['page_width'] = planty_customizer_vars['page_width_default'];
			}
			vars['sidebar_width']      = parseInt( vars['sidebar_width'], 10 );
			vars['sidebar_gap']        = parseInt( vars['sidebar_gap'], 10 );

			rez['page_width']          = planty_customizer_prepare_css_value( vars['page_width'] );
			rez['page_boxed_extra']    = planty_customizer_prepare_css_value( vars['page_boxed_extra'] );
			rez['page_fullwide_extra'] = planty_customizer_prepare_css_value( vars['page_fullwide_extra'] );
			rez['page_fullwide_max']   = planty_customizer_prepare_css_value( vars['page_fullwide_max'] );
			rez['grid_gap']            = planty_customizer_prepare_css_value( vars['grid_gap'] );
			rez['sidebar_prc']         = vars['sidebar_width'] / vars['page_width'];
			rez['sidebar_gap_prc']     = vars['sidebar_gap'] / vars['page_width'];
			rez['sidebar_width']       = planty_customizer_prepare_css_value( vars['sidebar_width'] );
			rez['sidebar_gap']         = planty_customizer_prepare_css_value( vars['sidebar_gap'] );
			rez['sidebar_gap_width']   = planty_customizer_prepare_css_value( vars['sidebar_gap'] );
		}
		return rez;
	}

	// Add ed to css value
	function planty_customizer_prepare_css_value(val) {
		if (val !== '' && val != 'inherit') {
			var ed = ('' + val).substr( -1 );
			if ('0' <= ed && ed <= '9') {
				val += 'px';
			}
		}
		return val;
	}

	// Convert HTML entities in the css value
	function planty_customizer_prepare_html_value(val) {
		return val.replace( /\&quot\;/g, '"' );
	}

} )( wp.customize );
