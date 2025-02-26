<?php
/**
 * The style "Creative" of the Team
 *
 * @package ThemeREX Addons
 * @since v1.4.3
 */

$args = get_query_var('trx_addons_args_sc_team');

$meta = get_post_meta(get_the_ID(), 'trx_addons_options', true);
$link = empty($args['no_links']) ? get_permalink() : '';

// SVG
$svg_bg_1 = trx_addons_get_svg_from_file(planty_get_file_dir('images/svg_bg_1.svg'));
$svg_bg_2 = trx_addons_get_svg_from_file(planty_get_file_dir('images/svg_bg_2.svg'));
$svg_bg_3 = trx_addons_get_svg_from_file(planty_get_file_dir('images/svg_bg_3.svg'));
$svg_bg = ($svg_bg_1 ? '<span class="svg-1">'.$svg_bg_1.'</span>' : '').($svg_bg_2 ? '<span class="svg-2">'.$svg_bg_2.'</span>' : '').($svg_bg_3 ? '<span class="svg-3">'.$svg_bg_3.'</span>' : '');


if ($args['slider']) {
	?><div class="slider-slide swiper-slide"><?php
} else if ($args['columns'] > 1) {
	?><div class="<?php echo esc_attr(trx_addons_get_column_class(1, $args['columns'], !empty($args['columns_tablet']) ? $args['columns_tablet'] : '', !empty($args['columns_mobile']) ? $args['columns_mobile'] : '')); ?>"><?php
}
?>
<div data-post-id="<?php the_ID(); ?>" <?php
	post_class( 'sc_team_item sc_item_container post_container' . (empty($post_link) ? ' no_links' : '') );
	trx_addons_add_blog_animation('team', $args);
?>>
	<?php
	// Featured image
	trx_addons_get_template_part('templates/tpl.featured.php',
								'trx_addons_args_featured',
								apply_filters( 'trx_addons_filter_args_featured', array(
												'allow_theme_replace' => false,
												'class' => 'sc_team_item_thumb',
												'no_links' => false,
												'hover' => 'info_anim',
												'thumb_size' => planty_get_thumb_size('rectangle'),
												'post_info' => apply_filters('trx_addons_filter_post_info',
													( true
														? '<a class="post_link sc_team_item_link" href="' . esc_url( $link ) . '"></a>'
														: ''
													)
													.(!empty($svg_bg) ? '<div class="all-svg">'.$svg_bg.'</div>' : ''),
															'team-creative', $args )
												), 'team-creative', $args )
								);
	?>
	<div class="sc_team_item_info">
		<div class="sc_team_item_header">
			<h4 class="sc_team_item_title entry-title"><?php
				if (!empty($link)) {
					?><a href="<?php echo esc_url($link); ?>"><?php
				}
				the_title();
				if (!empty($link)) {
					?></a><?php
				}
			?></h4>
			<?php
			if ( ! empty( $meta['subtitle'] ) ) {
				?><div class="sc_team_item_subtitle"><?php trx_addons_show_layout($meta['subtitle']);?></div><?php
			}
			?>
		</div>
		<?php
		if(!empty($meta["socials"][0]["name"])) { ?>
			<div class="trx_addons_hover_team"><div class="sc_team_item_socials socials_wrap trx_addons_hover_info"> <?php echo trim(trx_addons_get_socials_links_custom($meta['socials'])); ?></div></div>
			<?php
		}
		?>
	</div>

</div>
<?php
if ($args['slider'] || $args['columns'] > 1) {
	?></div><?php
}
?>