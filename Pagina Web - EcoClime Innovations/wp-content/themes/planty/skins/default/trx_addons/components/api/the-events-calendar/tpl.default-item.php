<?php
/**
 * The style "default" of the Events
 *
 * @package ThemeREX Addons
 * @since v1.6.54.3
 */

$args = get_query_var('trx_addons_args_sc_events');

$meta = get_post_meta(get_the_ID(), 'trx_addons_options', true);
$dt = tribe_get_start_date(null, true, 'Y-m-d');
$tm = tribe_get_start_time(null, 'H:i');
$dt2 = tribe_get_end_date(null, true, 'Y-m-d');
$tm2 = tribe_get_end_time(null, 'H:i');

$city = tribe_get_city(null);
$cost = tribe_get_formatted_cost();
$post_link = get_permalink();

if (!empty($args['slider'])) {
	?><div class="slider-slide swiper-slide"><?php
} else if ($args['columns'] > 1) {
	?><div class="<?php echo esc_attr(trx_addons_get_column_class(1, $args['columns'], !empty($args['columns_tablet']) ? $args['columns_tablet'] : '', !empty($args['columns_mobile']) ? $args['columns_mobile'] : '')); ?>"><?php
}
?>
<div class="sc_events_item sc_item_container post_container">

	<div class="sc_events_item_info">

        <div class="sc_events_item_date_start">
            <div class="sc_events_item_date_day"><?php planty_show_layout( date_i18n('d', strtotime($dt)) )?></div><div class="sc_events_item_date_month"><?php planty_show_layout( date_i18n('M, Y', strtotime($dt)) )?></div>
        </div>

        <div class="sc_events_item_content_wrap">

            <?php
            if (has_post_thumbnail()) {
                do_action( 'trx_addons_action_sc_events_item_before_featured', $args );
                trx_addons_get_template_part('templates/tpl.featured.php',
                    'trx_addons_args_featured',
                    apply_filters('trx_addons_filter_args_featured', array(
                        'class' => 'sc_events_item_thumb',
                        'post_info' => '',
                        'thumb_size' => apply_filters('trx_addons_filter_thumb_size',
                            planty_get_thumb_size('square'),
                            'events-'.$args['type'], $args
                        ),
                    ), 'events-'.$args['type'], $args )
                );
                do_action( 'trx_addons_action_sc_events_item_after_featured', $args );
            }

            ?>
            <div class="sc_events_item_content">
                <h4 class="sc_events_item_title"><a href="<?php echo esc_url($post_link); ?>"><?php the_title(); ?></a></h4>
                <div class="sc_events_item_meta"><?php
                   if ($city !== '') {
                        ?><span class="sc_events_item_meta_item sc_events_item_meta_locality"><span class="sc_events_item_meta_locality_city"><?php trx_addons_show_layout($city)?></span></span><?php
                   } ?>
                   <span class="sc_events_item_meta_item sc_events_item_meta_date"><?php
                        if ($dt == $dt2) {
                            if ( $tm == null ) {
                                trx_addons_show_layout(date_i18n(get_option('date_format'), strtotime($dt)), '<span class="sc_events_item_meta_date_start">', '</span>');
                            } else {
                                trx_addons_show_layout(date_i18n(get_option('date_format'), strtotime($dt)) . ' ' . $tm, '<span class="sc_events_item_meta_time_start">', '</span>');
                            }
                        } else {
                            if ( $tm == null ) {
                                trx_addons_show_layout(date_i18n(get_option('date_format'), strtotime($dt)), '<span class="sc_events_item_meta_date_start">', '</span>');
                                ?><span class="sc_events_item_meta_date_separator">-</span><?php
                                trx_addons_show_layout(date_i18n(get_option('date_format'), strtotime($dt2)), '<span class="sc_events_item_meta_date_end">', '</span>');
                            } else {
                                trx_addons_show_layout(date_i18n(get_option('date_format'), strtotime($dt)) . ' ' . $tm, '<span class="sc_events_item_meta_date_start">', '</span>');
                                ?><span class="sc_events_item_meta_date_separator">-</span><?php
                                trx_addons_show_layout(date_i18n(get_option('date_format'), strtotime($dt2)) . ' ' . $tm2, '<span class="sc_events_item_meta_date_end">', '</span>');
                            }
                        }
                        ?></span>
                </div>

                <?php if ( ( $excerpt = get_the_excerpt()) != '' && (!isset($args['hide_excerpt']) || (int)$args['hide_excerpt']==0) ) { ?>
                    <div class="sc_events_item_text"><?php echo esc_html($excerpt); ?></div><?php
                }

                if (!empty($args['more_text'])) { ?>
                    <a href="<?php echo esc_url($post_link); ?>" class="sc_events_item_more_link">
                        <span class="link_text"><?php echo esc_html($args['more_text']); ?></span><span class="link_icon"></span>
                    </a>
                <?php } ?>
            </div>
        </div>
    </div><?php
    if ($cost !== '') { ?>
        <div class="sc_events_item_price<?php echo esc_attr('Free' == $cost ? ' free_price' : '')?>"><?php planty_show_layout($cost);?></div><?php
    } ?>

    <a class="sc_events_item_link" href="<?php echo esc_url($post_link); ?>"></a>
</div><?php
if (!empty($args['slider']) || $args['columns'] > 1) {
	?></div><?php
}
