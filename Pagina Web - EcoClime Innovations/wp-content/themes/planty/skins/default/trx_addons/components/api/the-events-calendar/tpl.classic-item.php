<?php
/**
 * The style "classic" of the Events
 *
 * @package ThemeREX Addons
 * @since v1.6.51
 */

$args = get_query_var('trx_addons_args_sc_events');

$meta = get_post_meta(get_the_ID(), 'trx_addons_options', true);
$post_link = get_permalink();
$image = '';
if ( has_post_thumbnail() ) {
    $image = trx_addons_get_attachment_url(
        get_post_thumbnail_id( get_the_ID() ),
        apply_filters('trx_addons_filter_thumb_size', planty_get_thumb_size('masonry-big'), 'events-classic')
    );
}

if (!empty($args['slider'])) {
	?><div class="slider-slide swiper-slide"><?php
} else if ($args['columns'] > 1) {
	?><div class="<?php echo esc_attr(trx_addons_get_column_class(1, $args['columns'], !empty($args['columns_tablet']) ? $args['columns_tablet'] : '', !empty($args['columns_mobile']) ? $args['columns_mobile'] : '')); ?>"><?php
}
?>
<div class="sc_events_item sc_item_container post_container<?php echo esc_attr(!empty($args['more_text']) ? ' with_more' : '')?>">

    <div class="sc_events_item_content">

        <div class="sc_events_item_featured"<?php if (!empty($image)) echo ' style="background-image: url('.esc_url($image).');"'; ?>></div>

        <div class="sc_events_item_content_inner">

            <div class="sc_events_item_content_inner_top">
                <div class="sc_events_item_meta_categories"><?php planty_show_layout(trx_addons_get_post_terms(' ', get_the_ID(), Tribe__Events__Main::TAXONOMY))?></div>
            </div>

            <div class="sc_events_item_content_inner_bottom">
                <h4 class="sc_events_item_title"><a href="<?php echo esc_url($post_link); ?>"><?php the_title(); ?></a></h4>
                <div class="sc_events_item_meta">
                    <span class="sc_events_item_meta_item sc_events_item_meta_date"><?php
                        $dt = tribe_get_start_date(null, true, 'Y-m-d');
                        $dt2 = tribe_get_end_date(null, true, 'Y-m-d');
                        echo sprintf( $dt < date('Y-m-d')
                                        ? esc_html__('Started on %1$s to %2$s', 'planty')
                                        : esc_html__('Starting %1$s to %2$s', 'planty'),
                                    '<span class="sc_events_item_date sc_events_item_date_start">' . date_i18n(get_option('date_format'), strtotime($dt)) . '</span>',
                                    '<span class="sc_events_item_date sc_events_item_date_end">' . date_i18n(get_option('date_format'), strtotime($dt2)) . '</span>'
                                    );
                    ?></span>
                </div>
                <?php if ( ( $excerpt = get_the_excerpt()) != '' && (!isset($args['hide_excerpt']) || (int)$args['hide_excerpt']==0) ) { ?>
                    <div class="sc_events_item_text"><?php echo esc_html($excerpt); ?></div>
                <?php } ?>

                <?php if (!empty($args['more_text'])) { ?>
                    <a href="<?php echo esc_url($post_link); ?>" class="sc_events_item_more_link">
                        <span class="link_text"><?php echo esc_html($args['more_text']); ?></span><span class="link_icon"></span>
                    </a>
                <?php } ?>
            </div>
            <a class="sc_events_item_link" href="<?php echo esc_url($post_link); ?>"></a>
        </div>
    </div>
</div><?php
if (!empty($args['slider']) || $args['columns'] > 1) {
	?></div><?php
}
