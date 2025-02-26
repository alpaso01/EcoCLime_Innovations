<?php
/**
 * The style "timeline" of the Services
 *
 * @package ThemeREX Addons
 * @since v1.4
 */

$args = get_query_var('trx_addons_args_sc_services');
$query_args = array(
// Attention! Parameter 'suppress_filters' is damage WPML-queries!
	'post_status' => 'publish',
	'ignore_sticky_posts' => true
);
if (empty($args['ids'])) {
	$query_args['posts_per_page'] = $args['count'];
	if ( !trx_addons_is_off($args['pagination']) && $args['page'] > 1 ) {
		if ( empty( $args['offset'] ) ) {
			$query_args['paged'] = $args['page'];
		} else {
			$query_args['offset'] = $args['offset'] + $args['count'] * ( $args['page'] - 1 );
		}
	} else {
		$query_args['offset'] = $args['offset'];
	}
}

$query_args = trx_addons_query_add_sort_order($query_args, $args['orderby'], $args['order']);
$query_args = trx_addons_query_add_posts_and_cats($query_args, $args['ids'], $args['post_type'], $args['cat'], $args['taxonomy']);

$query_args = apply_filters( 'trx_addons_filter_query_args', $query_args, 'sc_services' );

$query = new WP_Query( $query_args );

if ($query->post_count > 0) {
	$posts_count = ($args['count'] > $query->post_count) ? $query->post_count : $args['count'];
	if ($args['columns'] < 1) $args['columns'] = $posts_count;
	$args['columns'] = max(1, min(12, (int) $args['columns']));
	if (!empty($args['columns_tablet'])) $args['columns_tablet'] = max(1, min(12, (int) $args['columns_tablet']));
	if (!empty($args['columns_mobile'])) $args['columns_mobile'] = max(1, min(12, (int) $args['columns_mobile']));
	$args['slides_space'] = max(0, (int) $args['slides_space']);
	?><div<?php if (!empty($args['id'])) echo ' id="'.esc_attr($args['id']).'"'; ?> class="sc_services sc_services_<?php 
			echo esc_attr($args['type']);
			echo ' sc_services_featured_'.esc_attr($args['featured_position']);
			if (!empty($args['class'])) echo ' '.esc_attr($args['class']); 
			?>"<?php
		if (!empty($args['css'])) echo ' style="'.esc_attr($args['css']).'"';
		if ($args['type']=='timeline' && $args['featured_position']=='bottom') echo ' data-equal-height=".sc_services_item"';
		?>><?php

		trx_addons_sc_show_titles('sc_services', $args);

		?><div class="sc_services_content sc_item_content sc_item_posts_container"><?php

		set_query_var('trx_addons_args_sc_services', $args);

		$trx_addons_number = $args['offset'];
		$trx_addons_count = 0;
		while ( $query->have_posts() ) { $query->the_post();
			$trx_addons_number++;

			if ($trx_addons_count % 2 == 0){
				echo '<div class="extra_row">';
			}

			trx_addons_get_template_part(array(
											TRX_ADDONS_PLUGIN_CPT . 'services/tpl.'.trx_addons_esc($args['type']).'-item.php',
                                            TRX_ADDONS_PLUGIN_CPT . 'services/tpl.default-item.php'
                                            ),
                                            'trx_addons_args_item_number',
                                            $trx_addons_number
                                        );

			if ($trx_addons_count % 2 != 0){
				echo '</div>';
			}

			$trx_addons_count++;
		}

		wp_reset_postdata();

		?></div><?php

		trx_addons_sc_show_pagination('sc_services', $args, $query);
		
		trx_addons_sc_show_links('sc_services', $args);

	?></div><!-- /.sc_services --><?php
}
