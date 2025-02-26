<?php
/**
 * The style "default" of the Portfolio
 *
 * @package ThemeREX Addons
 * @since v1.5
 */

$args        = get_query_var('trx_addons_args_sc_portfolio');
$args['columns'] = 0;
$args['use_masonry'] = false;
$args['use_gallery'] = false;
$meta        = get_post_meta(get_the_ID(), 'trx_addons_options', true);
$link        = !empty($meta['link']) ? $meta['link'] : get_permalink();

$image       = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ), 'full' );

if ( empty( $args['type'] ) ) {
	$args['type'] = 'default';
}

$use_masonry = ( ! isset( $args['use_masonry'] ) && trx_addons_is_on( trx_addons_get_option( 'portfolio_use_masonry' ) ) ) || ( isset( $args['use_masonry'] ) && trx_addons_is_on( $args['use_masonry'] ) );
$use_gallery = ( ! isset( $args['use_gallery'] ) && trx_addons_is_on( trx_addons_get_option( 'portfolio_use_gallery' ) ) ) || ( isset( $args['use_gallery'] ) && trx_addons_is_on( $args['use_gallery'] ) );

$details     = '';
if ( $use_gallery ) {
	ob_start();
	trx_addons_cpt_portfolio_show_details( array(
												'meta'  => $meta,
												'class' => 'portfolio_page_details',
												'share' => true
												)
										);
	$details = ob_get_contents();
	ob_end_clean();
}
?>
<div data-post-id="<?php the_ID(); ?>" class="sc_portfolio_item sc_item_container post_container<?php
	if (isset($args['hide_excerpt']) && (int)$args['hide_excerpt']>0) echo ' without_content';
?>"
	<?php trx_addons_add_blog_animation('portfolio', $args); ?>
	data-size="<?php
		if ( ! empty( $image[1] ) && ! empty( $image[2] ) ) {
			echo intval( $image[1] ) . 'x' . intval( $image[2] );}
	?>"
	data-src="<?php
		if ( ! empty( $image[0] ) ) {
			echo esc_url( $image[0] );}
	?>"
	<?php if ( $use_gallery ) { ?>
		data-details="<?php
			echo esc_attr( '<div class="post_details">'
								. '<h2 class="post_title">'
									. '<a href="' . esc_url( $link ) . '">' . esc_html( get_the_title() ) . '</a>'
								. '</h2>'
								. '<div class="post_description">'
									. $details
									. ( empty( $planty_template_args['hide_excerpt'] )
										? '<div class="post_description_content">' . get_the_excerpt() . '</div>'
										: ''
										)
									. '<a href="' . esc_url( $link ) . '" class="theme_button post_readmore"><span class="post_readmore_label">' . esc_html__( 'Learn more', 'planty' ) . '</span></a>'
								. '</div>'
							. '</div>'
						);
		?>"
	<?php }
    $featured = get_the_post_thumbnail_url(null, planty_get_thumb_size('square'));
    ?>
>
    <div class="post_content_wrap" <?php if(!empty($featured)) { ?> data-mouse-helper-centered="0" data-mouse-helper="hover" data-mouse-helper-mode="normal" data-mouse-helper-image="<?php echo esc_url($featured); ?>" <?php } ?>>
        <?php
        planty_show_post_meta(
            array_merge(
                array(
                    'components' => 'categories',
                    'class'      => 'post_meta_categories',
                    'echo'       => true,
                ),
                $args
            )
        );
        ?>
            <h5 class="post_title">
            	<a href="<?php echo the_permalink(); ?>"><?php the_title(); ?></a>
            </h5>
    </div>
</div>
