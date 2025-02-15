<?php
/**
 * The style "qw-case" of the Portfolio
 *
 * @package ThemeREX Addons
 * @since v1.5
 */

$args        = get_query_var('trx_addons_args_sc_portfolio');

$meta        = get_post_meta(get_the_ID(), 'trx_addons_options', true);
$link        = !empty($meta['link']) ? $meta['link'] : get_permalink();
$theme = wp_get_theme();
$tpl = $theme->template;

?>
<div data-post-id="<?php the_ID(); ?>" class="sc_portfolio_item sc_item_container post_container">
<div class="sc_portfolio_item_inner">
	<?php
	// Featured image or icon
	trx_addons_get_template_part(
		'templates/tpl.featured.php',
		'trx_addons_args_featured',
		apply_filters(
			'trx_addons_filter_args_featured',
			array(
				'class'         => 'sc_portfolio_item_thumb',
				'hover'         => '!none',
				'thumb_bg'      => false,
				'link'          => $link,
				'thumb_size'    => trx_addons_get_thumb_size($args['count'] > 2 ? $tpl.'-thumb-square' : 'full'),
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
			'portfolio-qw-case'
		)
	);
?>
<div class="sc_portfolio_item_content">
	<?php
		trx_addons_sc_show_post_meta( 'trx_sc_portfolio', apply_filters( 'trx_addons_filter_post_meta_args', array(
			'components' => 'categories',
			'theme_specific' => false,
			'class'      => 'post_meta_categories',
			'echo' => true
		), 'trx_sc_portfolio'.$args['type'] ) );
	?>
	<h5 class="post_title"><a href="<?php echo esc_url( $link ); ?>"><?php the_title(); ?></a></h5>
	<a class="post-more-link" href="<?php echo esc_url( $link ); ?>"><?php esc_html_e('Read More', 'trx_addons'); ?></a>
</div>
	
</div>
</div>