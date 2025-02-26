<?php
/**
 * The style "alter" of the Team
 *
 * @package ThemeREX Addons
 * @since v1.4.3
 */

$args = get_query_var('trx_addons_args_sc_team');

$meta = get_post_meta(get_the_ID(), 'trx_addons_options', true);
$link = empty($args['no_links']) ? get_permalink() : '';

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
													'<div class="trx_addons_hover_team">'
													. ( has_excerpt()
														? '<div class="sc_team_item_content">'
														. (!empty($link) ? '<a href="' . esc_url($link) . '">' : '')
														. get_the_excerpt()
														. (!empty($link) ? '</a>' : '')
														. '</div>'
														: '' )
													. (!empty($meta["socials"][0]["name"])
																	? '<div class="sc_team_item_socials socials_wrap trx_addons_hover_info">'.trim(trx_addons_get_socials_links_custom($meta['socials'])).'</div>'
														: '')
													. ( true
														? '<a class="post_link sc_team_item_link" href="' . esc_url( $link ) . '"></a>'
														: ''
													)
													. '</div>',
																'team-short', $args )
												), 'team-short', $args )
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
	</div>
</div>
<?php
if ($args['slider'] || $args['columns'] > 1) {
	?></div><?php
}
?>