/* global jQuery, PLANTY_STORAGE */

jQuery( window ).on( 'load', function() {

	"use strict";

	var $editor_wrapper = jQuery( '#editor,#site-editor,#widgets-editor' ).eq(0);
	var editor_selector = {
		'post':    '.edit-post-visual-editor',
		'site':    '.edit-site-visual-editor',
		'widgets': '.edit-widgets-block-editor'
	};
	if ( $editor_wrapper.length ) {
		var editor_type = $editor_wrapper.attr( 'id' ) == 'widgets-editor'
							? 'widgets'
							: ( $editor_wrapper.attr( 'id' ) == 'site-editor'
								? 'site'
								: 'post'
								);
		var $skeleton_content = false;
		if ( typeof window.MutationObserver !== 'undefined' ) {
			// Create the observer to reinit visual editor after switch from code editor to visual editor
			planty_create_observer( 'planty-check-visual-editor-wrapper', $editor_wrapper, function( mutationsList ) {
				planty_gutenberg_editor_init();
			} );
		} else {
		 	planty_gutenberg_editor_init();
		}
	}

	// Return Gutenberg editor object
	function planty_gutenberg_editor_object() {
		var editor = {
			$editor: false,
			$frame: false,
			$styles_wrapper: false,
			$writing_flow: false
		};
		if ( ! $skeleton_content || ! $skeleton_content.length ) {
			$skeleton_content = $editor_wrapper.find( '.interface-interface-skeleton__content' ).eq(0);
		}
		if ( $skeleton_content.length ) {
			var $editor = $skeleton_content.find( '>' + editor_selector[editor_type] ).eq( 0 );
			if ( $editor.length ) {
				editor.$editor = $editor;
				if ( editor_type == 'site' ) {
					editor.$frame = $editor.find( 'iframe[name="editor-canvas"]' );
					if ( editor.$frame.length && editor.$frame.get(0).contentDocument ) {
						editor.$styles_wrapper = jQuery( editor.$frame.get(0).contentDocument.body );
						if ( editor.$styles_wrapper.hasClass( 'planty_inited' ) ) {
							editor.$editor = editor.$frame = editor.$styles_wrapper = false;
						}
					} else {
						editor.$frame = false;
					}
				} else {
					if ( ! editor.$editor.hasClass( 'planty_inited' ) ) {
						editor.$writing_flow = $editor.find( '.block-editor-writing-flow' );
						if ( ! editor.$writing_flow.length ) {
							editor.$writing_flow = false;
						}
						editor.$styles_wrapper = editor.$writing_flow && editor.$writing_flow.hasClass( 'editor-styles-wrapper' )
													? editor.$writing_flow
													: $editor.find( '.editor-styles-wrapper' );
						if ( ! editor.$styles_wrapper.length ) {
							editor.$styles_wrapper = false;
						}
					} else {
						editor.$editor = false;
					}
				}
			}
		}
		return editor;
	}

	// Init on page load
	function planty_gutenberg_editor_init() {

		// Get Gutenberg editor object
		var editor = planty_gutenberg_editor_object();
		if ( ! editor.$editor ) {
			return;
		}

		// Common actions
		//-----------------------------------------------------------
		function add_class_with_color_scheme( $obj ) {
			if ( ! $obj.hasClass( 'scheme_' + PLANTY_STORAGE['color_scheme'] ) ) {
				$obj.addClass( 'scheme_' + PLANTY_STORAGE['color_scheme'] );
			}
		}

		function add_class_with_overridden_options( $obj ) {
			for ( var i in PLANTY_STORAGE['override_classes'] ) {
				$obj.addClass( PLANTY_STORAGE['override_classes'][i].replace( '%s', PLANTY_STORAGE[ i ] ) );
			}
		}

		// Add color scheme to the writing_flow
		if ( editor.$writing_flow ) {
			add_class_with_color_scheme( editor.$writing_flow );
		}
		// Add color scheme to the styles_wrapper
		if ( editor.$styles_wrapper ) {
			editor.$styles_wrapper.each( function() {
				add_class_with_color_scheme( jQuery( this ) );
			} );
		}

		// Post Editor
		//----------------------------------------------------------
		if ( editor_type == 'post' && editor.$styles_wrapper ) {

			// Add a class with a post type to the styles_wrapper
			editor.$styles_wrapper.addClass( planty_get_class_by_prefix( editor.$editor.attr( 'class' ), 'post-type-' ) );

			// Add the sidebar and the body style classes to the styles wrapper
			add_class_with_overridden_options( editor.$styles_wrapper );

			// Add a sidebar placeholder
			if ( editor.$writing_flow && PLANTY_STORAGE['sidebar_position'] != 'hide' ) {
				editor.$writing_flow.append( '<div class="editor-post-sidebar-holder"></div>' );
			}

		}

		// Site Editor
		//----------------------------------------------------------
		if ( editor_type == 'site' && editor.$styles_wrapper ) {
			// Add the sidebar and the body style classes to the styles wrapper
			add_class_with_overridden_options( editor.$styles_wrapper );
			// Mark as inited
			editor.$styles_wrapper.addClass( 'planty_inited' );
		}

		// Widgets Editor
		//----------------------------------------------------------
		if ( editor_type == 'widgets' && editor.$writing_flow ) {

			// Add the class 'scheme_xxx' to the just opened panel
			// after the panel header is clicked
			// editor.$writing_flow.on( 'click', '.components-panel__body-toggle', function() {
			// 	set_color_scheme( jQuery( this ).closest( '.components-panel__body' ).find( '.editor-styles-wrapper' ) );
			// } );
			
			// Create an observer to add the class 'scheme_xxx' to the each new sidebar
			planty_remove_observer( 'planty-check-editor-styles-wrapper' );
			planty_create_observer( 'planty-check-editor-styles-wrapper', editor.$writing_flow, function( mutationsList ) {
				editor.$styles_wrapper = editor.$writing_flow.find( '.editor-styles-wrapper:not([class*="scheme_"])' );
				editor.$styles_wrapper.each( function() {
					add_class_with_color_scheme( jQuery( this ) );
				} );
			} );
		}

		// Finish actions
		//----------------------------------------------------------

		// Mark the editor as inited
		editor.$editor.addClass( 'planty_inited' );
	}
} );
