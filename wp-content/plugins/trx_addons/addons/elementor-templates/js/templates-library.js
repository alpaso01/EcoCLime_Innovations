/* global elementor, elementorCommon, TRX_ADDONS_STORAGE */
/* eslint-disable */

window.trx_addons_elementor_templates_library = window.trx_addons_elementor_templates_library || {};

typeof jQuery != 'undefined' &&	! ( function() {
	jQuery( function() {
		var $library = false;

		function templatesLibrary() {
			const insertIndex = jQuery(this).parents(".elementor-section-wrap").length ? jQuery(this).parents(".elementor-add-section").index() : -1;
			window.trx_addons_elementor_templates_library.insertIndex = insertIndex;

			elementorCommon
			&& ( window.trx_addons_elementor_templates_library.modal
				|| ( ( window.trx_addons_elementor_templates_library.modal = elementorCommon.dialogsManager.createWidget( "lightbox", {
						id: "trx_addons_elementor_templates_library_modal",
						headerMessage: '<span class="trx_addons_elementor_templates_library_logo"></span><span class="trx_addons_elementor_templates_library_title">' + TRX_ADDONS_STORAGE['msg_elementor_templates_library_title'] + '</span>',
						message: "",
						hide: {
							auto: false,
							onClick: false,
							onOutsideClick: false,
							onOutsideContextMenu: false,
							onBackgroundClick: true
						},
						position: {
							my: "center",
							at: "center"
						},
						onShow: function() {
							var content = window.trx_addons_elementor_templates_library.modal.getElements( 'content' );
							if ( content.find( '#trx_addons_elementor_templates_library' ).length > 0 ) {
								return;
							}
							var navi_style = TRX_ADDONS_STORAGE['elementor_templates_library_navigation_style'];
							var html = '<div id="trx_addons_elementor_templates_library" class="wrap trx_addons_elementor_templates_library_navigation_style_' + navi_style + '">'
											+ '<a href="#" class="trx_addons_elementor_templates_library_close trx_addons_button_close" title="' + TRX_ADDONS_STORAGE['msg_elementor_templates_library_close'] + '">'
												+ '<span class="trx_addons_button_close_icon"></span>'
											+ '</a>'
											+ '<a href="#" class="trx_addons_elementor_templates_library_refresh" title="' + TRX_ADDONS_STORAGE['msg_elementor_templates_library_refresh_title'] + '">'
												+ '<span class="trx_addons_elementor_templates_library_refresh_icon"></span>'
												+ '<span class="trx_addons_elementor_templates_library_refresh_text">' + TRX_ADDONS_STORAGE['msg_elementor_templates_library_refresh'] + '</span>'
											+ '</a>';
							// Tabs
							html += '<div class="trx_addons_elementor_templates_library_tabs">';
							var i = 0;
							for (var tab in TRX_ADDONS_STORAGE['elementor_templates_library_tabs'] ) {
								html += '<a href="#" class="trx_addons_elementor_templates_library_tab' + ( i++ === 0 ? ' trx_addons_elementor_templates_library_tab_active' : '' ) + '" data-tab="' + tab + '">' + TRX_ADDONS_STORAGE['elementor_templates_library_tabs'][tab]['title'] + '</a>';
							}
							html += '</div>';
							html += '<div class="trx_addons_elementor_templates_library_content">';
							i = 0;
							for ( tab in TRX_ADDONS_STORAGE['elementor_templates_library_tabs'] ) {
								html += '<div class="trx_addons_elementor_templates_library_tab_content' + ( i++ === 0 ? ' trx_addons_elementor_templates_library_tab_content_active' : '' ) + '" data-tab="' + tab + '">';
								// Toolbar or Sidebar navigation
								html += '<div class="trx_addons_elementor_templates_library_' + navi_style + '">'
								// Search
								html += '<div class="trx_addons_elementor_templates_library_search">'
											+ '<span class="trx_addons_elementor_templates_library_search_icon eicon-search"></span>'
											+ '<input type="text" placeholder="' + TRX_ADDONS_STORAGE['msg_elementor_templates_library_search'] + '">'
										+ '</div>';
								// Categories
								var cats = getCategoriesList( tab );
								if ( cats ) {
									html += '<div class="trx_addons_elementor_templates_library_categories">' + cats + '</div>';
								}
								// Close Toolbar or Sidebar navigation
								html += '</div>';
								// Items list
								html += '<div class="trx_addons_elementor_templates_library_items">';
								html += '</div>'
									+ '</div>';
							}
							html += '</div></div>';
							content.append( html );
							$library = jQuery( '#trx_addons_elementor_templates_library' );

							// Add items
							for ( tab in TRX_ADDONS_STORAGE['elementor_templates_library_tabs'] ) {
								updateItems( tab );
								break;
							}

							// Add event handlers
							var updateItemsThrottle = trx_addons_throttle( function() {
								var columns = getComputedStyle( $library.get(0) ).getPropertyValue('--trx-addons-elementor-templates-library-columns');
								if ( $library && $library.data( 'columns') != columns ) {
									updateItems( $library.find('.trx_addons_elementor_templates_library_tab_active').data('tab') );
								}
							}, 100 );
							jQuery(window).on( 'resize', updateItemsThrottle );

							// var event = new Event( 'modal-close' );
							$library
								// Close the modal window
								.on( 'click', '.trx_addons_elementor_templates_library_close', function( e ) {
									// document.dispatchEvent( event );
									e.preventDefault();
									window.trx_addons_elementor_templates_library.modal.hide();
									return false;
								} )
								// Refresh the library
								.on( 'click', '.trx_addons_elementor_templates_library_refresh', function( e ) {
									e.preventDefault();
									refreshItems( jQuery(this) );
									return false;
								} )
								// Switch tabs
								.on( 'click', '.trx_addons_elementor_templates_library_tab', function( e ) {
									e.preventDefault();
									var $self = jQuery(this),
										tab = $self.data('tab');
									if ( ! $self.hasClass('trx_addons_elementor_templates_library_tab_active') ) {
										updateItems( tab );
										jQuery('.trx_addons_elementor_templates_library_tab').removeClass('trx_addons_elementor_templates_library_tab_active');
										$self.addClass('trx_addons_elementor_templates_library_tab_active');
										jQuery('.trx_addons_elementor_templates_library_tab_content').removeClass('trx_addons_elementor_templates_library_tab_content_active');
										jQuery('.trx_addons_elementor_templates_library_tab_content[data-tab="' + tab + '"]').addClass('trx_addons_elementor_templates_library_tab_content_active');
									}
									return false;
								} )
								// Switch categories (sidebar style)
								.on( 'click', '.trx_addons_elementor_templates_library_sidebar .trx_addons_elementor_templates_library_category', function( e ) {
									e.preventDefault();
									var $self = jQuery(this);
									if ( ! $self.hasClass('trx_addons_elementor_templates_library_category_active') ) {
										$self.parents('.trx_addons_elementor_templates_library_categories').find('.trx_addons_elementor_templates_library_category_active').removeClass('trx_addons_elementor_templates_library_category_active');
										$self.addClass('trx_addons_elementor_templates_library_category_active');
										updateItems( $self.parents('.trx_addons_elementor_templates_library_tab_content').data('tab') );
									}
									return false;
								} )
								// Switch categories (toolbar style)
								.on( 'change', '.trx_addons_elementor_templates_library_categories_list', function( e ) {
									e.preventDefault();
									var $self = jQuery(this);
									updateItems( $self.parents('.trx_addons_elementor_templates_library_tab_content').data('tab') );
									return false;
								} )
								// Switch favorites (toolbar style)
								.on( 'click', '.trx_addons_elementor_templates_library_toolbar .trx_addons_elementor_templates_library_category_favorites', function( e ) {
									e.preventDefault();
									var $self = jQuery(this);
									$self.toggleClass( 'trx_addons_elementor_templates_library_category_active' );
									updateItems( $self.parents('.trx_addons_elementor_templates_library_tab_content').data('tab') );
									return false;
								} )
								// Switch pages
								.on( 'click', '.trx_addons_elementor_templates_library_page', function( e ) {
									e.preventDefault();
									var $self = jQuery(this),
										page = $self.data('page');
									if ( ! $self.hasClass('trx_addons_elementor_templates_library_page_active') ) {
										$self.parents('.trx_addons_elementor_templates_library_pagination').find('.trx_addons_elementor_templates_library_page_active').removeClass('trx_addons_elementor_templates_library_page_active');
										$self.addClass('trx_addons_elementor_templates_library_page_active');
										updateItems( $self.parents('.trx_addons_elementor_templates_library_tab_content').data('tab') );
									}
									return false;
								} )
								// Search
								.on( 'input', '.trx_addons_elementor_templates_library_search input', function( e ) {
									updateItems( jQuery(this).parents('.trx_addons_elementor_templates_library_tab_content').data('tab') );
								} )
								// Mark as favorite
								.on( 'click', '.trx_addons_elementor_templates_library_item_favorite', function( e ) {
									e.preventDefault();
									var $self = jQuery(this),
										template = $self.data('template'),
										state = $self.hasClass( 'trx_addons_elementor_templates_library_item_favorite_on' ),
										$item = $self.parents('.trx_addons_elementor_templates_library_item');
									// Toggle favorite state
									$self.toggleClass( 'trx_addons_elementor_templates_library_item_favorite_on' );
									state = ! state;
									TRX_ADDONS_STORAGE['elementor_templates_library_favorites'][ template ] = state;
									// Update the state in the item
									$item.data( 'template-favorite', state );
									// Update the counter in the category list
									var $counter = $self.parents('.trx_addons_elementor_templates_library_tab_content').find('.trx_addons_elementor_templates_library_category_favorites .trx_addons_elementor_templates_library_category_total'),
										count = parseInt( $counter.text(), 10 );
									if ( isNaN( count ) ) {
										count = 0;
									}
									$counter.text( count + ( state ? 1 : -1 ) );
									// Send AJAX request to mark/unmark as favorite
									jQuery.post( TRX_ADDONS_STORAGE['ajax_url'], {
										action: 'trx_addons_elementor_templates_library_item_favorite',
										nonce: TRX_ADDONS_STORAGE['ajax_nonce'],
										template_name: template,
										favorite: $self.hasClass( 'trx_addons_elementor_templates_library_item_favorite_on' ) ? 1 : 0
									}, function( response ) {
										var rez = {};
										if (response === '' || response === 0) {
											rez = { error: TRX_ADDONS_STORAGE['msg_ajax_error'] };
										} else {
											try {
												rez = JSON.parse( response );
											} catch (e) {
												rez = { error: TRX_ADDONS_STORAGE['msg_ajax_error'] };
												console.log( response );
											}
										}
										if ( rez.error ) {
											alert( rez.error );
										}
									} );
									return false;
								} )
								// Preview template
								.on( 'click', '.trx_addons_elementor_templates_library_item_preview', function( e ) {
									e.preventDefault();
									var $self = jQuery(this),
										$item = $self.parents('.trx_addons_elementor_templates_library_item'),
										template = $item.data('template-name'),
										tab = $item.parents('.trx_addons_elementor_templates_library_tab_content').data('tab');
									if ( TRX_ADDONS_STORAGE['elementor_templates_library'][ template ] ) {
										templatesPreview( template );
									}
									return false;
								} )
								// Import template
								.on( 'click', '.trx_addons_elementor_templates_library_item_import', function( e ) {
									e.preventDefault();
									var $self = jQuery(this),
										template = $self.data('template'),
										tab = $self.parents('.trx_addons_elementor_templates_library_tab_content').data('tab');
									if ( TRX_ADDONS_STORAGE['elementor_templates_library'][ template ]['attention'] ) {
										if ( ! confirm(
											TRX_ADDONS_STORAGE['elementor_templates_library'][ template ]['attention'].replace( /<br[\s]*\/?>/gi, "\n" )
											+ "\n"
											+ TRX_ADDONS_STORAGE['msg_elementor_templates_library_import_confirm'] )
										) {
											return false;
										}
									}
									if ( $self ) {
										$self.addClass( 'trx_addons_loading' );
										$self.parents( '.trx_addons_elementor_templates_library_item' ).addClass( 'trx_addons_elementor_templates_library_item_loading' );
									}
									jQuery.post( TRX_ADDONS_STORAGE['ajax_url'], {
											action: 'trx_addons_elementor_templates_library_item_import',
											nonce: TRX_ADDONS_STORAGE['ajax_nonce'],
											template_name: template,
											template_type: tab
										}, function( response ) {
											var rez = {};
											if ( $self ) {
												$self.removeClass( 'trx_addons_loading' );
												$self.parents('.trx_addons_elementor_templates_library_item').removeClass( 'trx_addons_elementor_templates_library_item_loading' );
											}
											if (response === '' || response === 0) {
												rez = { error: TRX_ADDONS_STORAGE['msg_ajax_error'] };
											} else {
												try {
													rez = JSON.parse( response );
												} catch (e) {
													rez = { error: TRX_ADDONS_STORAGE['msg_ajax_error'] };
													console.log( response );
												}
											}
											if ( rez.error ) {
												alert( rez.error );
											} else {
												insertContent( rez.data.content, tab );
												window.trx_addons_elementor_templates_library.modal.hide();
											}
										} );
									return false;
								} );
						},
						onHide: function() {}
					} ) ),
//					window.trx_addons_elementor_templates_library.modal.getElements( 'header' ).remove(),
					window.trx_addons_elementor_templates_library.modal.getElements( 'message' ).append( window.trx_addons_elementor_templates_library.modal.addElement( 'content' ) ) ),
					window.trx_addons_elementor_templates_library.modal.show()
				);
		}

		// Get the html layout with the categories list
		function getCategoriesList( tab, force_all ) {
			var navi_style = TRX_ADDONS_STORAGE['elementor_templates_library_navigation_style'];
			var cats = '', total = 0, favorites = 0;
			var $tab_content = jQuery( '#trx_addons_elementor_templates_library .trx_addons_elementor_templates_library_tab_content[data-tab="' + tab + '"]' );
			var cat_active = $tab_content.length === 0 || force_all
								? ''
								: ( navi_style == 'sidebar'
									? $tab_content.find('.trx_addons_elementor_templates_library_category_active').data('category')
									: $tab_content.find('.trx_addons_elementor_templates_library_categories_list').val()
									);
			for ( var cat in TRX_ADDONS_STORAGE['elementor_templates_library_tabs'][tab]['category'] ) {
				if ( navi_style == 'sidebar' ) {
					cats += '<a href="#" class="trx_addons_elementor_templates_library_category' + ( cat == cat_active ? ' trx_addons_elementor_templates_library_category_active' : '' ) + '" data-category="' + cat + '">'
								+ TRX_ADDONS_STORAGE['elementor_templates_library_tabs'][tab]['category'][cat]['title']
								+ '<span class="trx_addons_elementor_templates_library_category_total">' + TRX_ADDONS_STORAGE['elementor_templates_library_tabs'][tab]['category'][cat]['total'] + '</span>'
							+ '</a>';
				} else {
					cats += '<option value="' + cat + '"' + ( cat == cat_active ? ' selected="selected"' : '' ) + '>'
								+ TRX_ADDONS_STORAGE['elementor_templates_library_tabs'][tab]['category'][cat]['title']
								+ ' (' + TRX_ADDONS_STORAGE['elementor_templates_library_tabs'][tab]['category'][cat]['total'] + ')'
							+ '</option>';
				}
				total += TRX_ADDONS_STORAGE['elementor_templates_library_tabs'][tab]['category'][cat]['total'];
			}
			for ( var tpl in TRX_ADDONS_STORAGE['elementor_templates_library'] ) {
				var template = TRX_ADDONS_STORAGE['elementor_templates_library'][tpl];
				if ( template.type != tab ) {
					continue;
				}
				if ( TRX_ADDONS_STORAGE['elementor_templates_library_favorites'][ tpl ] ) {
					favorites++;
				}
			}
			if ( total > 0 ) {
				if ( navi_style == 'sidebar' ) {
					cats = 
							// Category "All"
							'<a href="#" class="trx_addons_elementor_templates_library_category trx_addons_elementor_templates_library_category_all' + ( ! cat_active ? ' trx_addons_elementor_templates_library_category_active' : '' ) + '" data-category="all">'
								+ TRX_ADDONS_STORAGE['msg_elementor_templates_library_category_all']
								+ '<span class="trx_addons_elementor_templates_library_category_total">' + total + '</span>'
							+ '</a>'
							// Favorites
							+ '<a href="#" class="trx_addons_elementor_templates_library_category trx_addons_elementor_templates_library_category_favorites" data-category="favorites">'
								+ TRX_ADDONS_STORAGE['msg_elementor_templates_library_category_favorites']
								+ '<span class="trx_addons_elementor_templates_library_category_total">' + favorites + '</span>'
							+ '</a>'
							// Other categories
							+ cats;
				} else {
					cats = '<select class="trx_addons_elementor_templates_library_categories_list">'
							+ '<option value="all"' + ( ! cat_active ? ' selected="selected"' : '' ) + '>' + TRX_ADDONS_STORAGE['msg_elementor_templates_library_category_all'] + ' (' + total + ')' + '</option>'
							+ cats
						+ '</select>'
						// Favorites
						+ '<a href="#" class="trx_addons_elementor_templates_library_category trx_addons_elementor_templates_library_category_favorites">'
							+ '<span class="trx_addons_elementor_templates_library_category_icon eicon-heart-o"></span>'
							+ TRX_ADDONS_STORAGE['msg_elementor_templates_library_category_favorites']
							+ ' (<span class="trx_addons_elementor_templates_library_category_total">' + favorites + '</span>)'
						+ '</a>';
				}
			}
			return cats;
		}

		// Preview the template
		function templatesPreview( template ) {
			elementorCommon
			&& ( window.trx_addons_elementor_templates_library.preview
				|| ( ( window.trx_addons_elementor_templates_library.preview = elementorCommon.dialogsManager.createWidget( "lightbox", {
						id: "trx_addons_elementor_templates_library_preview",
						headerMessage: '<span class="trx_addons_elementor_templates_library_title">' + TRX_ADDONS_STORAGE['elementor_templates_library'][ template ].title + '</span>',
						message: "",
						hide: {
							auto: false,
							onClick: false,
							onOutsideClick: false,
							onOutsideContextMenu: false,
							onBackgroundClick: true
						},
						position: {
							my: "center",
							at: "center"
						},
						onShow: function() {
							var template = window.trx_addons_elementor_templates_library.preview.template;
							var tpl_title   = TRX_ADDONS_STORAGE['elementor_templates_library'][ template ].title;
							var tpl_content = TRX_ADDONS_STORAGE['elementor_templates_library'][ template ].content;
							var tpl_image   = '<img src="' + TRX_ADDONS_STORAGE['elementor_templates_library_url'] + '/' + template + '/' + template + '.jpg" alt="' + tpl_title + '">';
							var content = window.trx_addons_elementor_templates_library.preview.getElements( 'content' );
							if ( content.find( '#trx_addons_elementor_templates_library_preview_wrap' ).length > 0 ) {
								// Dialog already exists - replace the content and header title
								content.find( '.trx_addons_elementor_templates_library_preview_content' ).html( tpl_image );
								jQuery( '#trx_addons_elementor_templates_library_preview' ).find( '.trx_addons_elementor_templates_library_title' ).text( tpl_title );
							} else {
								// Create the dialog content
								var html = '<div id="trx_addons_elementor_templates_library_preview_wrap" class="wrap">'
												+ '<a href="#" class="trx_addons_elementor_templates_library_close trx_addons_button_close" title="' + TRX_ADDONS_STORAGE['msg_elementor_templates_library_preview_close'] + '">'
													+ '<span class="trx_addons_button_close_icon"></span>'
												+ '</a>'
												+ '<div class="trx_addons_elementor_templates_library_preview_content">'
													+ tpl_image
												+ '</div>'
											+ '</div>';
								content.append( html );

								jQuery( '#trx_addons_elementor_templates_library_preview_wrap' )
									// Close the preview window
									.on( 'click', '.trx_addons_elementor_templates_library_close', function( e ) {
										// document.dispatchEvent( event );
										e.preventDefault();
										window.trx_addons_elementor_templates_library.preview.hide();
										return false;
									} );
							}
						},
						onHide: function() {}
					} ) ),
//					window.trx_addons_elementor_templates_library.preview.getElements( 'header' ).remove(),
					window.trx_addons_elementor_templates_library.preview.getElements( 'message' ).append( window.trx_addons_elementor_templates_library.preview.addElement( 'content' ) ) ),
					window.trx_addons_elementor_templates_library.preview.template = template,
					window.trx_addons_elementor_templates_library.preview.show()
				);
		}

		// Check for updates in the library server
		function refreshItems( $bt ) {
			var $icon = $bt.find('.trx_addons_elementor_templates_library_refresh_icon').addClass( 'trx_addons_loading' );
			jQuery.post( TRX_ADDONS_STORAGE['ajax_url'], {
				action: 'trx_addons_elementor_templates_library_refresh',
				nonce: TRX_ADDONS_STORAGE['ajax_nonce']
			}, function( response ) {
				var rez = {};
				$icon.removeClass( 'trx_addons_loading' );
				if (response === '' || response === 0) {
					rez = { error: TRX_ADDONS_STORAGE['msg_ajax_error'] };
				} else {
					try {
						rez = JSON.parse( response );
					} catch (e) {
						rez = { error: TRX_ADDONS_STORAGE['msg_ajax_error'] };
						console.log( response );
					}
				}
				if ( rez.error ) {
					alert( rez.error );
				} else {
					if ( rez.data && rez.data.templates && rez.data.tabs ) {
						TRX_ADDONS_STORAGE['elementor_templates_library'] = rez.data.templates;
						TRX_ADDONS_STORAGE['elementor_templates_library_tabs'] = rez.data.tabs;
						var tab = $bt.parents('#trx_addons_elementor_templates_library').find('.trx_addons_elementor_templates_library_tab_active').data('tab');
						// Update the categories list in the current tab
						var cats = getCategoriesList( tab, true );
						if ( cats ) {
							jQuery( '#trx_addons_elementor_templates_library .trx_addons_elementor_templates_library_tab_content[data-tab="' + tab + '"]' )
								.find('.trx_addons_elementor_templates_library_categories')
								.html( cats );
						}
						// Update the items in the current tab
						updateItems( tab );
					} else {
						alert( TRX_ADDONS_STORAGE['msg_ajax_error'] );
					}
				}
			} );
		}

		// Repaint items in the specified tab
		function updateItems( tab ) {
			var $library = jQuery( '#trx_addons_elementor_templates_library');
			var navi_style = TRX_ADDONS_STORAGE['elementor_templates_library_navigation_style'];
			var html = '';
			var items = [];
			var column = 0;
			var columns = getComputedStyle( $library.get(0) ).getPropertyValue('--trx-addons-elementor-templates-library-columns');
			var items_in_page = TRX_ADDONS_STORAGE['elementor_templates_library_pagination_items'][tab] || 20;
			var templates_url = TRX_ADDONS_STORAGE['elementor_templates_library_url'];
			var $tab_content = jQuery( '#trx_addons_elementor_templates_library .trx_addons_elementor_templates_library_tab_content[data-tab="' + tab + '"]' );
			var search = $tab_content.find('.trx_addons_elementor_templates_library_search input').val().toLowerCase();
			var cat = navi_style == 'sidebar'
						? $tab_content.find('.trx_addons_elementor_templates_library_category_active').data('category').toLowerCase()
						: $tab_content.find('.trx_addons_elementor_templates_library_categories_list').val().toLowerCase();
			var is_favorites = navi_style == 'sidebar' ? cat == 'favorites' : $tab_content.find( '.trx_addons_elementor_templates_library_category_favorites' ).hasClass( 'trx_addons_elementor_templates_library_category_active' );
			var page = $tab_content.find('.trx_addons_elementor_templates_library_page_active').data('page') || 1;
			var pages = 1;
			var new_pagination = false;
			var idx = 0;
			var tpl;
			var i;
			// Check if we need a new pagination (if a new search or category selected)
			if ( $tab_content.data( 'search' ) != search || $tab_content.data( 'cat' ) != cat ) {
				$tab_content.data( 'search', search );
				$tab_content.data( 'cat', cat );
				$tab_content.data( 'page', 1 );
				page = 1;
				new_pagination = true;
			}
			// Count favorites in the current category if navigation style is 'toolbar'
			if ( navi_style == 'toolbar' ) {
				var favorites = 0;
				for ( tpl in TRX_ADDONS_STORAGE['elementor_templates_library'] ) {
					var template = TRX_ADDONS_STORAGE['elementor_templates_library'][tpl];
					if ( template.type != tab
						|| ( cat != 'all' && ( ',' + template.category + ',').indexOf( ',' + cat + ',' ) < 0 )
					) {
						continue;
					}
					if ( TRX_ADDONS_STORAGE['elementor_templates_library_favorites'][ tpl ] ) {
						favorites++;
					}
				}
				// Update the counter in the toolbar
				$tab_content.find('.trx_addons_elementor_templates_library_category_favorites .trx_addons_elementor_templates_library_category_total').text( favorites );
			}
			// Init items array
			for ( i = 0; i < columns; i++ ) {
				items.push( '' );
			}
			// Fill items array by columns
			for ( tpl in TRX_ADDONS_STORAGE['elementor_templates_library'] ) {
				var template = TRX_ADDONS_STORAGE['elementor_templates_library'][tpl];
				if ( template.type != tab
					|| ( is_favorites && ! TRX_ADDONS_STORAGE['elementor_templates_library_favorites'][ tpl ] )
					|| ( cat != 'all' && cat != 'favorites' && ( ',' + template.category + ',').indexOf( ',' + cat + ',' ) < 0 )
					|| ( search != '' && template.keywords.indexOf( search ) < 0 && template.title.indexOf( search ) < 0 )
				) {
					continue;
				}
				idx++;
				if ( idx < items_in_page * ( page - 1 ) + 1 || idx > items_in_page * page ) {
					continue;
				}
				items[ column++ % columns ] += '<div class="trx_addons_elementor_templates_library_item"'
							+ ' data-template-name="' + tpl + '"'
							+ ' data-template-category="' + template.category + '"'
							+ ' data-template-keywords="' + template.keywords + '"'
							+ ' data-template-favorite="' + ( TRX_ADDONS_STORAGE['elementor_templates_library_favorites'][ tpl ] ? 1 : 0 ) + '"'
						+ '>'
							+ '<div class="trx_addons_elementor_templates_library_item_body">'
								+ '<img src="' + templates_url + '/' + tpl + '/' + tpl + '-small.jpg" alt="' + template.title + '">'
								+ '<div class="trx_addons_elementor_templates_library_item_preview">'
									// Icon "Zoom" at the center of overlay
									+ '<span class="eicon-zoom-in-bold" aria-hidden="true"></span>'
								+ '</div>'
							+ '</div>'
							+ '<div class="trx_addons_elementor_templates_library_item_footer">'
								+ '<a href="#" class="trx_addons_elementor_templates_library_item_import trx_addons_icon-download elementor-button" data-template="' + tpl + '">' + TRX_ADDONS_STORAGE['msg_elementor_templates_library_import_template'] + '</a>'
								+ '<span class="trx_addons_elementor_templates_library_item_title">' + template.title + '</span>'
								+ '<span class="trx_addons_elementor_templates_library_item_favorite'
										+ ( TRX_ADDONS_STORAGE['elementor_templates_library_favorites'][ tpl ] ? ' trx_addons_elementor_templates_library_item_favorite_on' : '' )
									+ '" data-template="' + tpl + '"'
								+'>'
									+ '<span class="trx_addons_elementor_templates_library_item_favorite_icon eicon-heart-o"></span>'
								+ '</span>'
							+ '</div>'
						+ '</div>';
			}
			if ( ! items[0] ) {
				html += '<div class="trx_addons_elementor_templates_library_empty">' + TRX_ADDONS_STORAGE['msg_elementor_templates_library_empty'] + '</div>';
			} else {
				html += '<div class="trx_addons_elementor_templates_library_list">';
				for ( var i = 0; i < columns; i++ ) {
					html += '<div class="trx_addons_elementor_templates_library_column">' + items[ i ] + '</div>';
				}
				html += '</div>';
			}
			$tab_content.find('.trx_addons_elementor_templates_library_items').html( html );
			$library.data( 'columns', columns );
			// Pagination
			if ( new_pagination ) {
				html = '';
				pages = Math.ceil( idx / items_in_page );
				if ( pages > 1 ) {
					html += '<div class="trx_addons_elementor_templates_library_pagination">';
					for ( var i = 1; i <= pages; i++ ) {
						html += '<a href="#" class="trx_addons_elementor_templates_library_page' + ( i == page ? ' trx_addons_elementor_templates_library_page_active' : '' ) + '" data-page="' + i + '">' + i + '</a>';
					}
					html += '</div>';
				}
				$tab_content.find('.trx_addons_elementor_templates_library_pagination').remove();
				$tab_content.append( html ).toggleClass( 'with_pagination', pages > 1 );
			}
		}

		function insertContent( content ) {
			var context = arguments.length > 1 && void 0 !== arguments[1] ? arguments[1] : "blocks",
				contextText = context === "blocks"
								? TRX_ADDONS_STORAGE['msg_elementor_templates_library_type_block']
								: TRX_ADDONS_STORAGE['msg_elementor_templates_library_type_page'];
			var insertIndex = window.trx_addons_elementor_templates_library && typeof window.trx_addons_elementor_templates_library.insertIndex != 'undefined'
								? window.trx_addons_elementor_templates_library.insertIndex
								: -1;
			if ( typeof $e != "undefined" ) {
				for ( var historyId = $e.internal( "document/history/start-log", {
						type: "add",
						title: "".concat( TRX_ADDONS_STORAGE['msg_elementor_templates_library_add_template'], " " ).concat( contextText )
					} ), i = 0; i < content.length; i++
				 ) {
					$e.run( "document/elements/create", {
						container: elementor.getPreviewContainer(),
						model: content[ i ],
						options: insertIndex >= 0 ? { at: insertIndex++ } : {}
					} );
				}
				$e.internal( "document/history/end-log", {
					id: historyId
				} );
			} else {
				var model = new Backbone.Model( {
					getTitle: function() {
						return TRX_ADDONS_STORAGE['msg_elementor_templates_library_title']
					}
				} );
				elementor.channels.data.trigger( "template:before:insert", model );
				for ( var _i = 0; _i < json.data.content.length; _i++ ) {
					elementor.getPreviewView().addChildElement( content[ _i ], insertIndex >= 0 ? { at: insertIndex++ } : null );
				}
				elementor.channels.data.trigger( "template:after:insert", {} )
			}
		}
	
		window.trx_addons_elementor_templates_library.modal = null;
		window.trx_addons_elementor_templates_library.preview = null;

		const template = jQuery( '#tmpl-elementor-add-section' );

		if ( template.length && typeof elementor !== undefined) {
			var text = template.text();

			text = text.replace(
				'<div class="elementor-add-section-drag-title',
				'<div class="elementor-add-section-area-button elementor-add-trx-addons-elementor-templates-library-button" title="' + TRX_ADDONS_STORAGE['msg_elementor_templates_library_title'] + '">'
					// + '<i class="eicon-posts-justified"></i>'
				+ '</div>'
				+ '<div class="elementor-add-section-drag-title'
			);
			template.text( text );
			elementor.on( 'preview:loaded', function() {
				jQuery( elementor.$previewContents[0].body ).on( 'click', '.elementor-add-trx-addons-elementor-templates-library-button', templatesLibrary );
			} );
		}
	} );
} )();
