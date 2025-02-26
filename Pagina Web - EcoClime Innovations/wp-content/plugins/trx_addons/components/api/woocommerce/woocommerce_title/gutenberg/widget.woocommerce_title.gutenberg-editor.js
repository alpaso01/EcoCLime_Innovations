(function(blocks, i18n, element) {
	// Set up variables
	var el = element.createElement,
			__ = i18n.__;
	
	// Register Block - WooCommerce Title
	blocks.registerBlockType(
			'trx-addons/woocommerce-title',
			trx_addons_apply_filters( 'trx_addons_gb_map', {
					title: __( 'ThemeREX WooCommerce Title', "trx_addons" ),
					description: __( "Display page title and breadcrumbs", "trx_addons" ),
					keywords: [ 'woocommerce', 'title', 'e-commerce', 'ecommerce', 'product' ],
					icon: 'admin-users',
					category: 'trx-addons-widgets',
					attributes: trx_addons_apply_filters( 'trx_addons_gb_map_get_params', trx_addons_object_merge(
						{
							archive: {
								type: 'string',
								default: ''
							},
							single: {
								type: 'string',
								default: ''
							},
						},
						trx_addons_gutenberg_get_param_id()
					), 'trx-addons/woocommerce-title' ),
					edit: function(props) {
						return trx_addons_gutenberg_block_params(
							{
									'render': true,
								'general_params': el( wp.element.Fragment, {},
										trx_addons_gutenberg_add_params( trx_addons_apply_filters( 'trx_addons_gb_map_add_params', [
											// Products archive
											{
												'name': 'archive',
												'title': __( 'Products archive', "trx_addons" ),
												'type': 'select',
												'multiple': true,
												'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_archive_title_parts'] )
											},
											// Single product
											{
												'name': 'single',
												'title': __( 'Single product', "trx_addons" ),
												'type': 'select',
												'multiple': true,
												'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_single_title_parts'] )
											},
										], 'trx-addons/woocommerce-title', props ), props )
								),
								'additional_params': el( wp.element.Fragment, { key: props.name + '-additional-params' },
										// ID, Class, CSS params
										trx_addons_gutenberg_add_param_id( props )
								)
								}, props
						)
					},
					save: function(props) {
						return el( '', null );
					}
				},
				'trx-addons/woocommerce-title'
			)
	)
})( window.wp.blocks, window.wp.i18n, window.wp.element );