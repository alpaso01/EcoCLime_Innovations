(function(blocks, i18n, element) {

	// Set up variables
	var el = element.createElement,
		__ = i18n.__;
	
	// Register Block - Dark/Light
	blocks.registerBlockType(
		'trx-addons/layouts-dark-light',
		trx_addons_apply_filters( 'trx_addons_gb_map', {
			title: __( 'Dark/Light switcher', "trx_addons" ),
			description: __( 'Insert the dark/light switcher to the custom layout', "trx_addons" ),
			keywords: [ 'dark', 'light', 'switcher', 'colors', 'scheme' ],
			icon: 'star-half',
			category: 'trx-addons-layouts',
			attributes: trx_addons_apply_filters( 'trx_addons_gb_map_get_params', trx_addons_object_merge(
				{
					type: {
						type: 'string',
						default: 'default'
					},
					permanent: {
						type: 'boolean',
						default: false
					},
					effect: {
						type: 'string',
						default: 'slide'
					},
					position: {
						type: 'string',
						default: 'static'
					},
					offset_x: {
						type: 'string',
						default: ''
					},
					offset_y: {
						type: 'string',
						default: ''
					},
					schemes_light1_area: {
						type: 'string',
						default: 'content'
					},
					schemes_light1_scheme: {
						type: 'string',
						default: 'default'
					},
					schemes_light1_selector: {
						type: 'string',
						default: 'html,body'
					},
					schemes_light2_area: {
						type: 'string',
						default: 'header'
					},
					schemes_light2_scheme: {
						type: 'string',
						default: 'default'
					},
					schemes_light2_selector: {
						type: 'string',
						default: '.top_panel'
					},
					schemes_light3_area: {
						type: 'string',
						default: 'footer'
					},
					schemes_light3_scheme: {
						type: 'string',
						default: 'default'
					},
					schemes_light3_selector: {
						type: 'string',
						default: '.footer_wrap'
					},
					schemes_light4_area: {
						type: 'string',
						default: 'sidebar'
					},
					schemes_light4_scheme: {
						type: 'string',
						default: 'default'
					},
					schemes_light4_selector: {
						type: 'string',
						default: '.sidebar'
					},
					schemes_dark1_area: {
						type: 'string',
						default: 'content'
					},
					schemes_dark1_scheme: {
						type: 'string',
						default: 'dark'
					},
					schemes_dark1_selector: {
						type: 'string',
						default: 'html,body'
					},
					schemes_dark2_area: {
						type: 'string',
						default: 'header'
					},
					schemes_dark2_scheme: {
						type: 'string',
						default: 'dark'
					},
					schemes_dark2_selector: {
						type: 'string',
						default: '.top_panel'
					},
					schemes_dark3_area: {
						type: 'string',
						default: 'footer'
					},
					schemes_dark3_scheme: {
						type: 'string',
						default: 'dark'
					},
					schemes_dark3_selector: {
						type: 'string',
						default: '.footer_wrap'
					},
					schemes_dark4_area: {
						type: 'string',
						default: 'sidebar'
					},
					schemes_dark4_scheme: {
						type: 'string',
						default: 'dark'
					},
					schemes_dark4_selector: {
						type: 'string',
						default: '.sidebar'
					},
					icon_light: {
						type: 'number',
						default: 0
					},
					icon_light_url: {
						type: 'string',
						default: ''
					},
					icon_color_light: {
						type: 'string',
						default: ''
					},
					bg_color_light: {
						type: 'string',
						default: ''
					},
					bd_color_light: {
						type: 'string',
						default: ''
					},
					icon_dark: {
						type: 'number',
						default: 0
					},
					icon_dark_url: {
						type: 'string',
						default: ''
					},
					icon_color_dark: {
						type: 'string',
						default: ''
					},
					bg_color_dark: {
						type: 'string',
						default: ''
					},
					bd_color_dark: {
						type: 'string',
						default: ''
					}
				},
				trx_addons_gutenberg_get_param_hide(),
				trx_addons_gutenberg_get_param_id()
			), 'trx-addons/layouts-dark-light' ),
			edit: function(props) {
				return trx_addons_gutenberg_block_params(
					{
						'render': true,
						'general_params': el( wp.element.Fragment, {},
							trx_addons_gutenberg_add_params( trx_addons_apply_filters( 'trx_addons_gb_map_add_params', [
								// Layout
								{
									'name': 'type',
									'title': __( 'Layout', "trx_addons" ),
									'descr': __( "Select layout's type", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_layouts']['sc_dark_light'] )
								},
								// Permanent
								{
									'name': 'permanent',
									'title': __( 'For whole site', "trx_addons" ),
									'descr': __( "Apply the selected scheme for whole site or for the current page only", "trx_addons" ),
									'type': 'boolean',
								},
								// Effect
								{
									'name': 'effect',
									'title': __( 'Effect', "trx_addons" ),
									'descr': __( "Effect of the switcher", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_dark_light_effects'] )
								},
								// Position
								{
									'name': 'position',
									'title': __( 'Position', "trx_addons" ),
									'descr': __( "Select shortcodes's position", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_dark_light_positions'] )
								},
								// Offset X
								{
									'name': 'offset_x',
									'title': __( 'Horizontal offset', "trx_addons" ),
									'descr': __( "Offset from the left/right side of the window", "trx_addons" ),
									'type': 'text',
								},
								// Offset Y
								{
									'name': 'offset_y',
									'title': __( 'Vertical offset', "trx_addons" ),
									'descr': __( "Offset from the top/bottom side of the window", "trx_addons" ),
									'type': 'text',
								},
							], 'trx-addons/layouts-dark-light', props ), props )
						),
						'additional_params': el( wp.element.Fragment, { key: props.name + '-additional-params' },
							// Light mode
							trx_addons_gutenberg_add_param_section( props, 'light', __( 'Light mode', "trx_addons" ), trx_addons_apply_filters( 'trx_addons_gb_map_add_params', [
								// Light mode: Area 1
								{
									'name': 'schemes_light1_area',
									'title': __( 'Area 1', "trx_addons" ),
									'descr': __( "Area to change a color scheme", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_dark_light_areas'] )
								},
								{
									'name': 'schemes_light1_scheme',
									'title': __( 'Scheme 1', "trx_addons" ),
									'descr': __( "Color scheme to apply to the area above", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_dark_light_schemes'] )
								},
								{
									'name': 'schemes_light1_selector',
									'title': __( 'CSS Selector 1', "trx_addons" ),
									'descr': __( "CSS selector for the specified area", "trx_addons" ),
									'type': 'text',
								},
								// Light mode: Area 2
								{
									'name': 'schemes_light2_area',
									'title': __( 'Area 2', "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_dark_light_areas'] )
								},
								{
									'name': 'schemes_light2_scheme',
									'title': __( 'Scheme 2', "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_dark_light_schemes'] )
								},
								{
									'name': 'schemes_light2_selector',
									'title': __( 'CSS Selector 2', "trx_addons" ),
									'type': 'text',
								},
								// Light mode: Area 3
								{
									'name': 'schemes_light3_area',
									'title': __( 'Area 3', "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_dark_light_areas'] )
								},
								{
									'name': 'schemes_light3_scheme',
									'title': __( 'Scheme 3', "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_dark_light_schemes'] )
								},
								{
									'name': 'schemes_light3_selector',
									'title': __( 'CSS Selector 3', "trx_addons" ),
									'type': 'text',
								},
								// Light mode: Area 4
								{
									'name': 'schemes_light4_area',
									'title': __( 'Area 4', "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_dark_light_areas'] )
								},
								{
									'name': 'schemes_light4_scheme',
									'title': __( 'Scheme 4', "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_dark_light_schemes'] )
								},
								{
									'name': 'schemes_light4_selector',
									'title': __( 'CSS Selector 4', "trx_addons" ),
									'type': 'text',
								},
								// Icon light
								{
									'name': 'icon_light',
									'name_url': 'icon_light_url',
									'title': __( 'Icon', "trx_addons" ),
									'type': 'image'
								},
								// Icon color
								{
									'name': 'icon_color_light',
									'title': __( 'Icon color', "trx_addons" ),
									'type': 'color'
								},
								// Background color
								{
									'name': 'bg_color_light',
									'title': __( 'Bg color', "trx_addons" ),
									'type': 'color'
								},
								// Border color
								{
									'name': 'bd_color_light',
									'title': __( 'Border color', "trx_addons" ),
									'type': 'color'
								},
							], 'trx-addons/layouts-dark-light', props ) ),
							// Dark mode
							trx_addons_gutenberg_add_param_section( props, 'dark', __( 'Dark mode', "trx_addons" ), trx_addons_apply_filters( 'trx_addons_gb_map_add_params', [
								// Dark mode: Area 1
								{
									'name': 'schemes_dark1_area',
									'title': __( 'Area 1', "trx_addons" ),
									'descr': __( "Area to change a color scheme", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_dark_light_areas'] )
								},
								{
									'name': 'schemes_dark1_scheme',
									'title': __( 'Scheme 1', "trx_addons" ),
									'descr': __( "Color scheme to apply to the area above", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_dark_light_schemes'] )
								},
								{
									'name': 'schemes_dark1_selector',
									'title': __( 'CSS Selector 1', "trx_addons" ),
									'descr': __( "CSS selector for the specified area", "trx_addons" ),
									'type': 'text',
								},
								// Dark mode: Area 2
								{
									'name': 'schemes_dark2_area',
									'title': __( 'Area 2', "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_dark_light_areas'] )
								},
								{
									'name': 'schemes_dark2_scheme',
									'title': __( 'Scheme 2', "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_dark_light_schemes'] )
								},
								{
									'name': 'schemes_dark2_selector',
									'title': __( 'CSS Selector 2', "trx_addons" ),
									'type': 'text',
								},
								// Dark mode: Area 3
								{
									'name': 'schemes_dark3_area',
									'title': __( 'Area 3', "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_dark_light_areas'] )
								},
								{
									'name': 'schemes_dark3_scheme',
									'title': __( 'Scheme 3', "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_dark_light_schemes'] )
								},
								{
									'name': 'schemes_dark3_selector',
									'title': __( 'CSS Selector 3', "trx_addons" ),
									'type': 'text',
								},
								// Dark mode: Area 4
								{
									'name': 'schemes_dark4_area',
									'title': __( 'Area 4', "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_dark_light_areas'] )
								},
								{
									'name': 'schemes_dark4_scheme',
									'title': __( 'Scheme 4', "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_dark_light_schemes'] )
								},
								{
									'name': 'schemes_dark4_selector',
									'title': __( 'CSS Selector 4', "trx_addons" ),
									'type': 'text',
								},
								// Icon dark
								{
									'name': 'icon_dark',
									'name_url': 'icon_dark_url',
									'title': __( 'Icon', "trx_addons" ),
									'type': 'image'
								},
								// Icon color
								{
									'name': 'icon_color_dark',
									'title': __( 'Icon color', "trx_addons" ),
									'type': 'color'
								},
								// Background color
								{
									'name': 'bg_color_dark',
									'title': __( 'Bg color', "trx_addons" ),
									'type': 'color'
								},
								// Border color
								{
									'name': 'bd_color_dark',
									'title': __( 'Border color', "trx_addons" ),
									'type': 'color'
								},
							], 'trx-addons/layouts-dark-light', props ) ),
							// Hide on devices params
							trx_addons_gutenberg_add_param_hide( props ),
							// ID, Class, CSS params
							trx_addons_gutenberg_add_param_id( props )
						)
					}, props
				);
			},
			save: function(props) {
				return el( '', null );
			}
		},
		'trx-addons/layouts-dark-light'
	) );

})( window.wp.blocks, window.wp.i18n, window.wp.element );