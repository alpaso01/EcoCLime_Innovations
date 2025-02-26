(function(blocks, i18n, element) {

	// Set up variables
	var el = element.createElement,
		__ = i18n.__;

	// Register Block - Rrecent Posts
	blocks.registerBlockType(
		'trx-addons/recent-posts',
		trx_addons_apply_filters( 'trx_addons_gb_map', {
			title: __( 'ThemeREX Recent Posts', "trx_addons" ),
			description: __( "Insert recent posts list with thumbs, meta and category", "trx_addons" ),
			keywords: [ 'recent', 'posts', 'news' ],
			icon: 'list-view',
			category: 'trx-addons-widgets',
			attributes: trx_addons_apply_filters( 'trx_addons_gb_map_get_params', trx_addons_object_merge(
				{				
					title: {
						type: 'string',
						default: ''
					},
					number: {
						type: 'number',
						default: 4
					},
					show_date: {
						type: 'boolean',
						default: true
					},
					show_image: {
						type: 'boolean',
						default: true
					},
					show_author: {
						type: 'boolean',
						default: true
					},
					show_counters: {
						type: 'boolean',
						default: true
					},
					show_categories: {
						type: 'boolean',
						default: true
					}
				},
				trx_addons_gutenberg_get_param_id()
			), 'trx-addons/recent-posts' ),
			edit: function(props) {
				return trx_addons_gutenberg_block_params(
					{
						'render': true,
						'general_params': el( wp.element.Fragment, {},
							trx_addons_gutenberg_add_params( trx_addons_apply_filters( 'trx_addons_gb_map_add_params', [
								// Widget title
								{
									'name': 'title',
									'title': __( 'Widget title', "trx_addons" ),
									'descr': __( "Title of the widget", "trx_addons" ),
									'type': 'text',
								},
								// Number posts to show
								{
									'name': 'number',
									'title': __( 'Number of posts to display', "trx_addons" ),
									'descr': __( "How many posts display in the widget?", "trx_addons" ),
									'type': 'number',
									'min': 1
								},
								// Show image
								{
									'name': 'show_image',
									'title': __( "Show image", "trx_addons" ),
									'descr': __( "Do you want display a featured image?", "trx_addons" ),
									'type': 'boolean'
								},
								// Show date
								{
									'name': 'show_date',
									'title': __( "Show date", "trx_addons" ),
									'descr': __( "Do you want display a publish date?", "trx_addons" ),
									'type': 'boolean'
								},
								// Show author
								{
									'name': 'show_author',
									'title': __( "Show author", "trx_addons" ),
									'descr': __( "Do you want display an author?", "trx_addons" ),
									'type': 'boolean'
								},
								// Show counters
								{
									'name': 'show_counters',
									'title': __( "Show view count", "trx_addons" ),
									'descr': __( "Do you want display a view count?", "trx_addons" ),
									'type': 'boolean'
								},
								// Show categories
								{
									'name': 'show_categories',
									'title': __( "Show categories", "trx_addons" ),
									'descr': __( "Do you want display categories?", "trx_addons" ),
									'type': 'boolean'
								}
							], 'trx-addons/recent-posts', props ), props )
						),
						'additional_params': el( wp.element.Fragment, { key: props.name + '-additional-params' },
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
		'trx-addons/recent-posts'
	) );
})( window.wp.blocks, window.wp.i18n, window.wp.element );
