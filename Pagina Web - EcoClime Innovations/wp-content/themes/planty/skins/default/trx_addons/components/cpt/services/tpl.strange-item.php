<?php
/**
 * The style "Strange" of the Services item
 *
 * @package ThemeREX Addons
 * @since v1.6.13
 */

$args = get_query_var('trx_addons_args_sc_services');
$number = get_query_var('trx_addons_args_item_number');
if (empty($args['id'])) $args['id'] = 'sc_services_'.str_replace('.', '', mt_rand());

$meta = get_post_meta(get_the_ID(), 'trx_addons_options', true);
if (!is_array($meta)) $meta = array();
$meta['price'] = apply_filters( 'trx_addons_filter_custom_meta_value', !empty($meta['price']) ? $meta['price'] : '', 'price' );

$link = empty($args['no_links'])
			? (!empty($meta['link']) ? $meta['link'] : get_permalink())
			: '';

$svg_present = false;

if (!empty($args['slider'])) {
	?><div class="slider-slide swiper-slide"><?php
} else if ($args['columns'] > 1) {
	?><div class="<?php echo esc_attr(trx_addons_get_column_class(1, $args['columns'], !empty($args['columns_tablet']) ? $args['columns_tablet'] : '', !empty($args['columns_mobile']) ? $args['columns_mobile'] : '')); ?> "><?php
}
?>
<div data-post-id="<?php the_ID(); ?>" <?php post_class( apply_filters( 'trx_addons_filter_services_item_class',
			'sc_services_item sc_item_container post_container'
			. (empty($link) ? ' no_links' : '')
			. (isset($args['hide_excerpt']) && (int)$args['hide_excerpt']>0 ? ' without_content' : ' with_content'),
			$args )
			);
	trx_addons_add_blog_animation('services', $args);
	if (!empty($args['popup'])) {
		?> data-post_id="<?php echo esc_attr(get_the_ID()); ?>"<?php
		?> data-post_type="<?php echo esc_attr(TRX_ADDONS_CPT_SERVICES_PT); ?>"<?php
	}
?>><?php
	do_action( 'trx_addons_action_services_item_start', $args );
	trx_addons_get_template_part('templates/tpl.featured.php',
									'trx_addons_args_featured',
									apply_filters( 'trx_addons_filter_args_featured', array(
														'class' => 'sc_services_item_header',
														'show_no_image' => true,
														'no_links' => empty($link),
														'link' => $link,
														'thumb_bg' => true,
														'thumb_size' => ! empty( $args['thumb_size'] )
																			? $args['thumb_size']
																			: apply_filters( 'trx_addons_filter_thumb_size', trx_addons_get_thumb_size('masonry-big'), 'services-strange', $args ),
														'post_info' => apply_filters('trx_addons_filter_post_info',
																		! empty( $link )
																			? '<a class="post_link sc_services_item_link" href="' . esc_url( $link ) . '"></a>'
																			: '',
																		'services-strange', $args )
													),
													'services-strange', $args
												)
								);
	do_action( 'trx_addons_action_services_item_after_featured', $args );
	?>
	<div class="sc_services_item_content">
        <div class="sc_services_item_content_inner">
		<?php
		do_action( 'trx_addons_action_services_item_content_start', $args );

        if (!empty($meta['icon'])) {
            $svg = $img = '';
            if (trx_addons_is_url($meta['icon'])) {
                if (strpos($meta['icon'], '.svg') !== false) {
                    $svg = $meta['icon'];
                    $svg_present = !empty($args['icons_animation']);
                } else {
                    $img = $meta['icon'];
                }
                $meta['icon'] = basename($meta['icon']);
            } else if (!empty($args['icons_animation']) && ($svg = trx_addons_get_file_dir('css/icons.svg/'.trx_addons_clear_icon_name($meta['icon']).'.svg')) != '')
                $svg_present = true;
            echo !empty($link)
                ? '<a href="'.esc_url($link).'"'.((!empty($meta['link']) && trx_addons_is_external_url($meta['link'])) ? ' target="_blank"' : '')
                : '<span';
            ?> id="<?php echo esc_attr($args['id'].'_'.trim($meta['icon']).'_'.trim($number)); ?>"
            class="sc_services_item_icon<?php
            if ($svg_present) echo ' sc_icon_animation';
            echo !empty($svg)
                ? ' sc_icon_type_svg'
                : (!empty($img)
                    ? ' sc_icon_type_images'
                    : ' sc_icon_type_icons ' . esc_attr($meta['icon'])
                );
            ?>"<?php
            if (!empty($meta['icon_color'])) {
                echo ' style="color:'.esc_attr($meta['icon_color']).'"';
            }
            ?>><?php
            if (!empty($svg)) {
                trx_addons_show_layout(trx_addons_get_svg_from_file($svg));
            } else if (!empty($img)) {
                $attr = trx_addons_getimagesize($img);
                ?><img class="sc_icon_as_image" src="<?php echo esc_url($img); ?>" alt="<?php esc_attr_e('Icon', 'planty'); ?>"<?php echo (!empty($attr[3]) ? ' '.trim($attr[3]) : ''); ?>><?php
            }
            echo !empty($link)
                ? '</a>'
                : '</span>';
        }

		$title_tag = 'h6';
		if ($args['columns'] == 1) $title_tag = 'h4';
		?>
		<<?php echo esc_attr($title_tag); ?> class="sc_services_item_title<?php if (!empty($meta['price'])) echo ' with_price'; ?>"><?php
			if (!empty($link)) {
				?><a href="<?php echo esc_url($link); ?>"<?php if (!empty($meta['link']) && trx_addons_is_external_url($meta['link'])) echo ' target="_blank"'; ?>><?php
			}
			the_title();
			// Price
			if (!empty($meta['price'])) {
				?><div class="sc_services_item_price"><?php trx_addons_show_layout($meta['price']); ?></div><?php
			}
			if (!empty($link)) {
				?></a><?php
			}
		?></<?php echo esc_attr($title_tag); ?>>
		<?php do_action( 'trx_addons_action_services_item_after_title', $args );

        if  ( !isset( $args['show_subtitle'] ) || (int) $args['show_subtitle']==0 ) {
            ?><div class="sc_services_item_subtitle"><?php
                 $terms = trx_addons_get_post_terms(', ', get_the_ID(), trx_addons_get_post_type_taxonomy());
                 if (empty($link)) $terms = trx_addons_links_to_span($terms);
                     trx_addons_show_layout($terms);
            ?></div><?php
        }
        do_action( 'trx_addons_action_services_item_after_subtitle', $args );

		if (!isset($args['hide_excerpt']) || (int)$args['hide_excerpt']==0) { ?>
			<div class="sc_services_item_text"><?php the_excerpt(); ?></div><?php
		}
        if (!empty($link) && !empty($args['more_text'])) {
            ?><div class="sc_services_item_button sc_item_button">
            <a href="<?php echo esc_url($link); ?>"<?php if (!empty($meta['link']) && trx_addons_is_external_url($meta['link'])) echo ' target="_blank"'; ?> class="sc_services_item_more_link">
                <span class="link_text"><?php echo esc_html($args['more_text']); ?></span><span class="link_icon"></span>
            </a>
            </div><?php
        }
		do_action( 'trx_addons_action_services_item_content_end', $args );
		?>
	</div>
    </div>
</div>
<?php
if (!empty($args['slider']) || $args['columns'] > 1) {
	?></div><?php
}
if ($svg_present) {
    wp_enqueue_script( 'vivus', trx_addons_get_file_url(TRX_ADDONS_PLUGIN_SHORTCODES . 'icons/vivus.js'), array('jquery'), null, true );
    wp_enqueue_script( 'trx_addons-sc_icons', trx_addons_get_file_url(TRX_ADDONS_PLUGIN_SHORTCODES . 'icons/icons.js'), array('jquery'), null, true );
}
