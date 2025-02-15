(function(blocks, i18n, element) {

	// Set up variables
	var el = element.createElement,
		__ = i18n.__;

	// Register Block - Chat
	blocks.registerBlockType(
		'trx-addons/chat',
		trx_addons_apply_filters( 'trx_addons_gb_map', {
			title: __( 'AI Helper Chat', "trx_addons" ),
			description: __( "AI Helper Chat form for frontend", "trx_addons" ),
			keywords: [ 'ai', 'helper', 'chat', 'conversation', 'messages' ],
			icon: 'text',
			category: 'trx-addons-blocks',
			attributes: trx_addons_apply_filters( 'trx_addons_gb_map_get_params', trx_addons_object_merge(
				{
					type: {
						type: 'string',
						default: 'default'
					},
					model: {
						type: 'string',
						default: ''
					},
					override_config: {
						type: 'string',
						default: ''
					},
					title_text: {
						type: 'string',
						default: ''
					},
					new_chat_text: {
						type: 'string',
						default: ''
					},
					placeholder_text: {
						type: 'string',
						default: ''
					},
					prompt: {
						type: 'string',
						default: ''
					},
					button_text: {
						type: 'string',
						default: ''
					},
					tags_position: {
						type: 'string',
						default: 'none'
					},
					tags_label: {
						type: 'string',
						default: ''
					},
					tags: {
						type: 'string',
						default: ''
					},
					premium: {
						type: 'boolean',
						default: false
					},
					show_limits: {
						type: 'boolean',
						default: false
					},
					system_prompt: {
						type: 'string',
						default: ''
					},
					temperature: {
						type: 'number',
						default: TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_tgenerator_temperature']
					},
					max_tokens: {
						type: 'number',
						default: 0
					},
					// Reload block - hidden option
					reload: {
						type: 'string',
						default: ''
					}
				},
				! trx_addons_apply_filters( 'trx_addons_filter_add_title_param', true, 'trx-addons/chat' ) ? {} : trx_addons_gutenberg_get_param_title(),
				! trx_addons_apply_filters( 'trx_addons_filter_add_title_param', true, 'trx-addons/chat' ) ? {} : trx_addons_gutenberg_get_param_button(),
				trx_addons_gutenberg_get_param_id()
			), 'trx-addons/chat' ),
			edit: function(props) {
				return trx_addons_gutenberg_block_params(
					{
						'render': true,
						'render_button': true,
						'parent': true,
						'general_params': el( wp.element.Fragment, {},
							trx_addons_gutenberg_add_params( trx_addons_apply_filters( 'trx_addons_gb_map_add_params', [
								// Layout
								{
									'name': 'type',
									'title': __( 'Layout', "trx_addons" ),
									'descr': __( "Select shortcodes's layout", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_layouts']['sc_chat'] )
								},
								// Model
								{
									'name': 'model',
									'title': __( 'Model', "trx_addons" ),
									'descr': __( "Select a chat model", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_chat_models'], false )
								},
								// Override config (for Flowise AI API only)
								{
									'name': 'override_config',
									'title': __( 'Override config JSON', "trx_addons" ),
									'descr': __( 'If you want to override the default config JSON for the Flowise AI chatflow, you can do it here. The JSON should be a valid JSON object.', 'trx_addons' ),
									'type': 'textarea',
									'dependency': {
										'model': TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_chat_models_flowise_ai']
									}
								},
								// Premium Mode
								{
									'name': 'premium',
									'title': __( 'Premium Mode', "trx_addons" ),
									'descr': __( "Enables you to set a broader range of limits for text generation, which can be used for a paid text generation service. The limits are configured in the global settings.", "trx_addons" ),
									'type': 'boolean'
								},
								// Show "Limits" info
								{
									'name': 'show_limits',
									'title': __( 'Show limits', "trx_addons" ),
									'descr': __( "Show a message with available limits for generation", "trx_addons" ),
									'type': 'boolean'
								},
								// Title text
								{
									'name': 'title_text',
									'title': __( 'Title text', "trx_addons" ),
									'type': 'text'
								},
								// New chat text
								{
									'name': 'new_chat_text',
									'title': __( 'New chat text', "trx_addons" ),
									'type': 'text'
								},
								// Default prompt
								{
									'name': 'prompt',
									'title': __( 'Default prompt', "trx_addons" ),
									'type': 'text'
								},
								// Placeholder text
								{
									'name': 'placeholder_text',
									'title': __( 'Placeholder', "trx_addons" ),
									'type': 'text'
								},
								// Button text
								{
									'name': 'button_text',
									'title': __( 'Button text', "trx_addons" ),
									'type': 'text'
								},
								// Tags position
								{
									'name': 'tags_position',
									'title': __( 'Tags', "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_chat_tags_positions'], false )
								},
								// Tags label
								{
									'name': 'tags_label',
									'title': __( 'Tags label', "trx_addons" ),
									'type': 'text',
									'dependency': {
										'tags_position': ['^none']
									}
								},
								// System prompt
								{
									'name': 'system_prompt',
									'title': __( 'System prompt', "trx_addons" ),
									'descr': __( 'These are instructions for the AI Model describing how it should generate text. If you leave this field empty - the System Prompt specified in the plugin options will be used.', "trx_addons" ),
									'type': 'textarea'
								},
								// Temperature
								{
									'name': 'temperature',
									'title': __( 'Temperature', "trx_addons" ),
									'descr': __('What sampling temperature to use, between 0 and 2. Higher values like 0.8 will make the output more random, while lower values like 0.2 will make it more focused and deterministic.', 'trx_addons'),
									'type': 'number',
									'min': 0,
									'max': 2.0,
									'step': 0.1
								},
								// Max tokens
								{
									'name': 'max_tokens',
									'title': __( 'Max. tokens per request', "trx_addons" ),
									'descr': __('How many tokens can be used per one request to the API? If you leave this field empty - the value specified in the plugin options will be used.', 'trx_addons'),
									'type': 'number',
									'min': 0,
									'max': TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_tgenerator_max_tokens'],
									'step': 100
								},
							], 'trx-addons/chat', props ), props )
						),
						'additional_params': el( wp.element.Fragment, { key: props.name + '-additional-params' },
							// Title params
							! trx_addons_apply_filters( 'trx_addons_filter_add_title_param', true, 'trx-addons/chat' ) ? null : trx_addons_gutenberg_add_param_title( props, true ),
							// ID, Class, CSS params
							trx_addons_gutenberg_add_param_id( props )
						)
					}, props
				);
			},
			save: function(props) {
				// Get child block values of attributes
				if ( props.hasOwnProperty( 'innerBlocks' ) && props.innerBlocks.hasOwnProperty( 'length' ) && trx_addons_is_wp_editor_inited() ) {	// && props.innerBlocks.length
					props.attributes.tags = trx_addons_gutenberg_get_child_attr( props );
				}
				return el( trx_addons_get_wp_editor().InnerBlocks.Content, {} );
			},
		},
		'trx-addons/chat'
	) );

	// Register block Tag Item
	blocks.registerBlockType(
		'trx-addons/chat-item',
		trx_addons_apply_filters( 'trx_addons_gb_map', {
			title: __( 'Tag Item', "trx_addons" ),
			description: __( "Insert a tag for Chat", "trx_addons" ),
			keywords: [ 'ai', 'helper', 'chat', 'tag' ],
			icon: 'tag',
			category: 'trx-addons-blocks',
			parent: ['trx-addons/chat'],
			attributes: trx_addons_apply_filters( 'trx_addons_gb_map_get_params', {
				// Tag Item attributes
				title: {
					type: 'string',
					default: __( 'One', "trx_addons" )
				},
				prompt: {
					type: 'string',
					default: ''
				}
			}, 'trx-addons/chat-item' ),
			edit: function(props) {
				return trx_addons_gutenberg_block_params(
					{
						'title': __( 'Tag', "trx_addons" ) + (props.attributes.title ? ': ' + props.attributes.title : ''),
						'general_params': el( wp.element.Fragment, {},
							trx_addons_gutenberg_add_params( trx_addons_apply_filters( 'trx_addons_gb_map_add_params', [
								// Title
								{
									'name': 'title',
									'title': __( 'Title', "trx_addons" ),
									'descr': __( "Enter title of the tag", "trx_addons" ),
									'type': 'text'
								},
								// Prompt
								{
									'name': 'prompt',
									'title': __( 'Prompt', "trx_addons" ),
									'descr': __( "Enter a prompt associated with a tag", "trx_addons" ),
									'type': 'text'
								}
							], 'trx-addons/chat-item', props ), props )
						)
					}, props
				);
			},
			save: function(props) {
				return el( '', null );
			}
		},
		'trx-addons/chat-item'
	) );

})( window.wp.blocks, window.wp.i18n, window.wp.element );
