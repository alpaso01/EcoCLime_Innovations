(function(blocks, i18n, element) {

	// Set up variables
	var el = element.createElement,
		__ = i18n.__;

	// Register Block - Text Generator
	blocks.registerBlockType(
		'trx-addons/tgenerator',
		trx_addons_apply_filters( 'trx_addons_gb_map', {
			title: __( 'AI Helper Text Generator', "trx_addons" ),
			description: __( "AI Helper Text Generator form for frontend", "trx_addons" ),
			keywords: [ 'ai', 'helper', 'tgenerator', 'generator', 'text' ],
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
					prompt: {
						type: 'string',
						default: ''
					},
					prompt_width: {
						type: 'number',
						default: 100
					},
					placeholder_text: {
						type: 'string',
						default: ''
					},
					button_text: {
						type: 'string',
						default: ''
					},
					align: {
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
				! trx_addons_apply_filters( 'trx_addons_filter_add_title_param', true, 'trx-addons/tgenerator' ) ? {} : trx_addons_gutenberg_get_param_title(),
				! trx_addons_apply_filters( 'trx_addons_filter_add_title_param', true, 'trx-addons/tgenerator' ) ? {} : trx_addons_gutenberg_get_param_button(),
				trx_addons_gutenberg_get_param_id()
			), 'trx-addons/tgenerator' ),
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
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_layouts']['sc_tgenerator'] )
								},
								// Model
								{
									'name': 'model',
									'title': __( 'Model', "trx_addons" ),
									'descr': __( "Select a model for generation", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_tgenerator_models'], false )
								},
								// Override config (for Flowise AI API only)
								{
									'name': 'override_config',
									'title': __( 'Override config JSON', "trx_addons" ),
									'descr': __( 'If you want to override the default config JSON for the Flowise AI chatflow, you can do it here. The JSON should be a valid JSON object.', 'trx_addons' ),
									'type': 'textarea',
									'dependency': {
										'model': TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_tgenerator_models_flowise_ai']
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
								// Default prompt
								{
									'name': 'prompt',
									'title': __( 'Default prompt', "trx_addons" ),
									'type': 'text'
								},
								// Prompt width
								{
									'name': 'prompt_width',
									'title': __( 'Prompt field width', "trx_addons" ),
									'descr': __( "Specify a width of the prompt field (in %)", "trx_addons" ),
									'type': 'number',
									'min': 50,
									'max': 100
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
								// Align
								{
									'name': 'align',
									'title': __( 'Alignment', "trx_addons" ),
									'descr': __( "Alignment of the prompt field and tags", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_tgenerator_aligns'] )
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
							], 'trx-addons/tgenerator', props ), props )
						),
						'additional_params': el( wp.element.Fragment, { key: props.name + '-additional-params' },
							// Title params
							! trx_addons_apply_filters( 'trx_addons_filter_add_title_param', true, 'trx-addons/tgenerator' ) ? null : trx_addons_gutenberg_add_param_title( props, true ),
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
		'trx-addons/tgenerator'
	) );

})( window.wp.blocks, window.wp.i18n, window.wp.element );
