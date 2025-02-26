/* global jQuery, elementor, elementorCommon, TRX_ADDONS_ELEMENTOR_EXTENSION_ACTION, TRX_ADDONS_STORAGE, cssbeautify, elementorModules, $e */

jQuery( window ).on( 'elementor/init', function() {
	var trx_addons_elementor_extension = window.trx_addons_elementor_extension = window.trx_addons_elementor_extension || {};

	/**
	 * Escape characters in during Regexp.
	 *
	 * @param {string} String to replace.
	 *
	 * @return {void | *}
	 */
	function escapeRegExp( string ) {
		return string.replace( /[.*+?^${}()|[\]\\]/g, "\\$&" );
	}

	/**
	 * Define function to find and replace specified term with replacement string.
	 *
	 * @param {string} str String to replace.
	 * @param {string} term Search string.
	 * @param {string}replacement Replacement string.
	 *
	 * @return {string}
	 */
	function replaceAll(str, term, replacement) {
		return str.replace(new RegExp( escapeRegExp( term ), 'g' ), replacement );
	}

	/**
	 * Redirect to specific section.
	 *
	 * @since 1.6.2
	 *
	 * @param {string} section Panel/Section ID.
	 * @param {string} panel Panel ID for Theme Style window panels.
	 * 
	 * @return void
	 */
	trx_addons_elementor_extension.redirectToSection = function( tab = 'settings', section = 'trx_addons_style_settings', page = 'page-settings', kit = false ) {
		$e.route( `panel/${ page }/${ tab }` );

		if ( kit ) {
			elementor.getPanelView().getCurrentPageView().content.currentView.activateSection(section).render();
		} else {
			elementor.getPanelView().getCurrentPageView().activateSection(section)._renderChildren();
		}

		return false;
	};

	/**
	 * Opens global panel and redirects to specific section.
	 *
	 * @param {string} section Panel/Section ID.
	 * @param {string} panel Panel ID for Theme Style window panels.
	 * 
	 * @return void
	 */
	trx_addons_elementor_extension.redirectToPanel = ( section, panel = 'theme-style-kits' ) => {
		$e.run( 'panel/global/open' ).then( () => {
			$e.route( `panel/global/${panel}` );
			elementor.getPanelView().getCurrentPageView().content.currentView.activateSection(section).render();
		});
	};

	function refreshPageConfig( id ) {
		elementor.documents.invalidateCache( id );
		elementor.documents.request( id )
			.then( ( config ) => {
				elementor.documents.addDocumentByConfig(config);

				$e.internal( 'editor/documents/load', { config } ).then( () => {
					elementor.reloadPreview();
				} );
			});
	}

	/**
	 * Reset global colors to default values.
	 */
	trx_addons_elementor_extension.handleGlobalColorsReset = () => {
		elementorCommon.dialogsManager.createWidget( 'confirm', {
			message: TRX_ADDONS_ELEMENTOR_EXTENSION_ACTION['translate']['resetGlobalColorsMessage'],
			headerMessage: TRX_ADDONS_ELEMENTOR_EXTENSION_ACTION['translate']['resetHeader'],
			strings: {
				confirm: elementor.translate( 'yes' ),
				cancel: elementor.translate( 'cancel' ),
			},
			defaultOption: 'cancel',
			onConfirm: trx_addons_elementor_extension.resetGlobalColors,
		} ).show();
	};

	trx_addons_elementor_extension.resetGlobalColors = () => {

		var defaultValues = {};

		// Get defaults for each scheme
		Object.keys( TRX_ADDONS_ELEMENTOR_EXTENSION_ACTION['schemes'] ).forEach( ( scheme ) => {
			const option_name = 'trx_addons_global_colors_scheme_' + scheme;
			const options = elementor.documents.documents[elementor.config.kit_id].container.controls[ option_name ];
			if ( undefined === options || null === options ) {
				return;
			}
			// Use saved values
			defaultValues[ option_name ] = options.default;
			// Override with factory values if they exist
			if ( TRX_ADDONS_ELEMENTOR_EXTENSION_ACTION['schemesFactory'] && typeof TRX_ADDONS_ELEMENTOR_EXTENSION_ACTION['schemesFactory'][ scheme ] === 'object' ) {
				defaultValues[ option_name ] = TRX_ADDONS_ELEMENTOR_EXTENSION_ACTION['schemesFactory'][ scheme ];
			}
		} );

		// Reset the selected settings to their default values
		$e.run( 'document/elements/settings', {
			container: elementor.documents.documents[ elementor.config.kit_id ].container,
			settings: defaultValues,
			options: {
				external: true,
			},
		} );

		// Save changes and reopen the global colors panel
		$e.run('document/save/update').then( () => $e.run( 'panel/global/close' ).then( () => trx_addons_elementor_extension.openGlobalColors() ) );
	};

	trx_addons_elementor_extension.openGlobalColors = () => {
		trx_addons_elementor_extension.redirectToPanel( 'trx_addons_global_colors_section', 'global-colors' );
	};

	/**
	 * Reset global fonts to default values.
	 */
	trx_addons_elementor_extension.handleGlobalFontsReset = () => {
		elementorCommon.dialogsManager.createWidget( 'confirm', {
			message: TRX_ADDONS_ELEMENTOR_EXTENSION_ACTION['translate']['resetGlobalFontsMessage'],
			headerMessage: TRX_ADDONS_ELEMENTOR_EXTENSION_ACTION['translate']['resetHeader'],
			strings: {
				confirm: elementor.translate( 'yes' ),
				cancel: elementor.translate( 'cancel' ),
			},
			defaultOption: 'cancel',
			onConfirm: trx_addons_elementor_extension.resetGlobalFonts,
		} ).show();
	};

	trx_addons_elementor_extension.resetGlobalFonts = () => {

		var setting = 'trx_addons_global_theme_fonts';
		var defaultValues = {};

		const options = elementor.documents.documents[elementor.config.kit_id].container.controls[setting];
		if ( undefined === options || null === options ) {
			return;
		}
		// Use saved values
		defaultValues[ setting ] = options.default;
		// Override with factory values if they exist
		if ( TRX_ADDONS_ELEMENTOR_EXTENSION_ACTION['fontsFactory'] && typeof TRX_ADDONS_ELEMENTOR_EXTENSION_ACTION['fontsFactory'] === 'object' ) {
			defaultValues[ setting ] = TRX_ADDONS_ELEMENTOR_EXTENSION_ACTION['fontsFactory'];
		}
		// Reset the selected settings to their default values
		$e.run( 'document/elements/settings', {
			container: elementor.documents.documents[elementor.config.kit_id].container,
			settings: defaultValues,
			options: {
				external: true,
			},
		} );

		// Reset value render hack.
		$e.run('document/save/update').then( () => $e.run( 'panel/global/close' ).then( () => trx_addons_elementor_extension.openGlobalFonts() ));
	};

	trx_addons_elementor_extension.openGlobalFonts = () => {
		trx_addons_elementor_extension.redirectToPanel( 'trx_addons_global_fonts_section', 'global-typography' );
	};

	// Add actions to reset global colors and fonts
	elementor.channels.editor.on( 'trx_addons_elementor_extension:resetGlobalColors', trx_addons_elementor_extension.handleGlobalColorsReset );
	elementor.channels.editor.on( 'trx_addons_elementor_extension:resetGlobalFonts', trx_addons_elementor_extension.handleGlobalFontsReset );
} );
