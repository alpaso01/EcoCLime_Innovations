<?php
/**
 * The style "qw-simple" of the Portfolio
 *
 * @package ThemeREX Addons
 * @since v1.5
 */

$args        = get_query_var('trx_addons_args_sc_portfolio');
$meta        = get_post_meta(get_the_ID(), 'trx_addons_options', true);
$link        = !empty($meta['link']) ? $meta['link'] : get_permalink();
$theme = wp_get_theme();
$tpl = $theme->template;

if ( empty( $args['type'] ) ) {
	$args['type'] = 'default';
}

$use_masonry = ( ! isset( $args['use_masonry'] ) && trx_addons_is_on( trx_addons_get_option( 'portfolio_use_masonry' ) ) ) || ( isset( $args['use_masonry'] ) && trx_addons_is_on( $args['use_masonry'] ) );

if ( ! empty($args['slider']) ) {
	?><div class="slider-slide swiper-slide"><?php
} else if ( $args['columns'] > 1 ) {
	if ( $use_masonry ) {
		?><div class="sc_portfolio_masonry_item sc_portfolio_masonry_item-1_<?php echo esc_attr( $args['columns'] ); ?>"><?php
	} else {
		?><div class="<?php echo esc_attr(trx_addons_get_column_class(1, $args['columns'], !empty($args['columns_tablet']) ? $args['columns_tablet'] : '', !empty($args['columns_mobile']) ? $args['columns_mobile'] : '')); ?>"><?php
	}
}
?>
<div data-post-id="<?php the_ID(); ?>" class="sc_portfolio_item sc_item_container post_container">
	<?php
	// Featured image or icon
	trx_addons_get_template_part(
		'templates/tpl.featured.php',
		'trx_addons_args_featured',
		apply_filters(
			'trx_addons_filter_args_featured',
			array(
				'class'         => 'sc_portfolio_item_thumb',
				'hover'         => '!link',
				'thumb_bg'      => true,
				'link'          => $link,
				'thumb_size'    => trx_addons_get_thumb_size($args['columns'] > 1 ? $tpl.'-thumb-medium-square' : 'full'),
                'thumb_only'    => empty( $meta['video'] ),
				'show_no_image' => true,
                'autoplay'      => ! empty( $meta['video'] ) && ! empty( $meta['video_autoplay_archive'] ),
                'video'         => empty( $meta['video'] )
										? ''
										: trx_addons_get_video_layout( array(
																			'link' => $meta['video'],
																			'autoplay' => ! empty( $meta['video_autoplay_archive'] ),
																			'mute' => ! empty( $meta['video_autoplay_archive'] ),
																			'show_cover' => empty( $meta['video_autoplay_archive'] )
																			)
										)
			),
			'portfolio-qw-simple'
		)
	);
?>
<div class="sc_portfolio_item_content">
	<h5 class="post_title"><a href="<?php echo esc_url( $link ); ?>"><?php the_title(); ?></a>
		<?php
		if ( !empty($args['slider']) && isset($args['item_number'])) { ?>
			<span class="number"><?php printf("%02d", $args['item_number']);?></span>
		<?php
		}
		?>
	</h5>
</div>
	
</div><?php
if (!empty($args['slider']) || $args['columns'] > 1) {
	?></div><?php
}