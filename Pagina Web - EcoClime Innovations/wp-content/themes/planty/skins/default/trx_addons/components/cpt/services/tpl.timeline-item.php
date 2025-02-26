<?php
/**
 * The style "timeline" of the Services
 *
 * @package ThemeREX Addons
 * @since v1.6.24
 */

$args = get_query_var('trx_addons_args_sc_services');
$number = get_query_var('trx_addons_args_item_number');

$meta = get_post_meta(get_the_ID(), 'trx_addons_options', true);
if (!is_array($meta)) $meta = array();
$meta['price'] = apply_filters( 'trx_addons_filter_custom_meta_value', !empty($meta['price']) ? $meta['price'] : '', 'price' );

$link = empty($args['no_links'])
			? (!empty($meta['link']) ? $meta['link'] : get_permalink())
			: '';

if (empty($args['id'])) $args['id'] = 'sc_services_'.str_replace('.', '', mt_rand());
if (empty($args['featured'])) $args['featured'] = 'image';
if (empty($args['featured_position'])) $args['featured_position'] = 'top';

$svg_present = false;
$price_showed = false;

?>
<div data-post-id="<?php the_ID(); ?>" <?php post_class( apply_filters( 'trx_addons_filter_services_item_class',
			'sc_services_item sc_item_container post_container'
			. (empty($link) ? ' no_links' : '')
			. (isset($args['hide_excerpt']) && (int)$args['hide_excerpt']>0 ? ' without_content' : ' with_content')
			. (!trx_addons_is_off($args['featured']) ? ' with_'.esc_attr($args['featured']) : '')
			. ' sc_services_item_featured_'.esc_attr($args['featured']!='none' ? $args['featured_position'] : 'none'),
			$args )
			);
	trx_addons_add_blog_animation('services', $args);
	if (!empty($args['popup'])) {
		?> data-post_id="<?php echo esc_attr(get_the_ID()); ?>"<?php
		?> data-post_type="<?php echo esc_attr(TRX_ADDONS_CPT_SERVICES_PT); ?>"<?php
	}
?>><?php

	do_action( 'trx_addons_action_services_item_start', $args );
	
	// Timeline
	?><div class="sc_services_item_timeline_point"></div><?php

	do_action( 'trx_addons_action_services_item_after_timeline', $args );

	?>	
	<div class="sc_services_item_info">
		<?php
		do_action( 'trx_addons_action_services_item_header_start', $args );
			if (!empty($link)) {
				?><a class="link_wrap" href="<?php echo esc_url($link); ?>"<?php if (!empty($meta['link']) && trx_addons_is_external_url($meta['link'])) echo ' target="_blank"'; ?>></a><?php
			}
		?>
		<div class="sc_services_item_header">
			<?php
			do_action( 'trx_addons_action_services_item_header_inner_start', $args );
			$terms = false;
			$title_tag = $args['featured']=='number' ? 'h4' : 'h6';
			?>
			<<?php echo esc_html($title_tag); ?> class="sc_services_item_title entry-title<?php if (!$price_showed && !empty($meta['price'])) echo ' with_price'; ?>"><?php
				if (!empty($link)) {
					?><a href="<?php echo esc_url($link); ?>"<?php if (!empty($meta['link']) && trx_addons_is_external_url($meta['link'])) echo ' target="_blank"'; ?>><?php
				}
				the_title();
				if (!empty($link)) {
					?></a><?php
				}
			?></<?php echo esc_html($title_tag); ?>><?php
		?></div><?php
		do_action( 'trx_addons_action_services_item_after_header_inner', $args );
		if (!isset($args['hide_excerpt']) || (int)$args['hide_excerpt']==0) {
			do_action( 'trx_addons_action_services_item_before_text', $args );
			?><div class="sc_services_item_content"><?php the_excerpt(); ?></div><?php
			do_action( 'trx_addons_action_services_item_after_text', $args );
		}
		do_action( 'trx_addons_action_services_item_header_end', $args );
	?></div>
</div><?php

if ($svg_present) {
	wp_enqueue_script( 'vivus', trx_addons_get_file_url(TRX_ADDONS_PLUGIN_SHORTCODES . 'icons/vivus.js'), array('jquery'), null, true );
	wp_enqueue_script( 'trx_addons-sc_icons', trx_addons_get_file_url(TRX_ADDONS_PLUGIN_SHORTCODES . 'icons/icons.js'), array('jquery'), null, true );
}
