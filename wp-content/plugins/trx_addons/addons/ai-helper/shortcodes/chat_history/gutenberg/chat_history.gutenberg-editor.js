(function(blocks, i18n, element) {

	// Set up variables
	var el = element.createElement,
		__ = i18n.__;

	// Register Block - Chat
	blocks.registerBlockType(
		'trx-addons/chat-history',
		trx_addons_apply_filters( 'trx_addons_gb_map', {
			title: __( 'AI Helper Chat History', "trx_addons" ),
			description: __( "AI Helper Chat History - list latest conversations from the AI Chat", "trx_addons" ),
			keywords: [ 'ai', 'helper', 'chat', 'conversation', 'messages', 'topics', 'history' ],
			icon: 'text',
			category: 'trx-addons-blocks',
			attributes: trx_addons_apply_filters( 'trx_addons_gb_map_get_params', trx_addons_object_merge(
				{
					type: {
						type: 'string',
						default: 'default'
					},
					number: {
						type: 'number',
						default: 5
					},
					chat_id: {
						type: 'string',
						default: ''
					},
					// Reload block - hidden option
					reload: {
						type: 'string',
						default: ''
					}
				},
				! trx_addons_apply_filters( 'trx_addons_filter_add_title_param', true, 'trx-addons/chat-history' ) ? {} : trx_addons_gutenberg_get_param_title(),
				! trx_addons_apply_filters( 'trx_addons_filter_add_title_param', true, 'trx-addons/chat-history' ) ? {} : trx_addons_gutenberg_get_param_button(),
				trx_addons_gutenberg_get_param_id()
			), 'trx-addons/chat-history' ),
			edit: function(props) {
				return trx_addons_gutenberg_block_params(
					{
						'render': true,
						'render_button': true,
						'parent': false,
						'general_params': el( wp.element.Fragment, {},
							trx_addons_gutenberg_add_params( trx_addons_apply_filters( 'trx_addons_gb_map_add_params', [
								// Layout
								{
									'name': 'type',
									'title': __( 'Layout', "trx_addons" ),
									'descr': __( "Select shortcodes's layout", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_layouts']['sc_chat_history'] )
								},
								// Number of items
								{
									'name': 'number',
									'title': __( 'Number of items', "trx_addons" ),
									'type': 'number',
									'min': 1,
									'max': trx_addons_apply_filters( 'trx_addons_filter_sc_chat_history_max', 20 ),
								},
								// Chat ID
								{
									'name': 'chat_id',
									'title': __( 'Chat ID', "trx_addons" ),
									'type': 'text'
								}
							], 'trx-addons/chat-history', props ), props )
						),
						'additional_params': el( wp.element.Fragment, { key: props.name + '-additional-params' },
							// Title params
							! trx_addons_apply_filters( 'trx_addons_filter_add_title_param', true, 'trx-addons/chat-history' ) ? null : trx_addons_gutenberg_add_param_title( props, true ),
							// ID, Class, CSS params
							trx_addons_gutenberg_add_param_id( props )
						)
					}, props
				);
			},
			save: function(props) {
				return el( '', null );
			},
		},
		'trx-addons/chat-history'
	) );

})( window.wp.blocks, window.wp.i18n, window.wp.element );
