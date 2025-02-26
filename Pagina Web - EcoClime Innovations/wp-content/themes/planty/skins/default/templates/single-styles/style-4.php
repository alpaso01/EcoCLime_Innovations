<?php
/**
 * The "Style 4" template to display the post header of the single post or attachment:
 * featured image and title placed in the post header
 *
 * @package PLANTY
 * @since PLANTY 1.75.0
 */

if ( apply_filters( 'planty_filter_single_post_header', is_singular( 'post' ) || is_singular( 'attachment' ) ) ) {
	$planty_post_format = str_replace( 'post-format-', '', get_post_format() );
    $post_meta = in_array( $planty_post_format, array( 'video' ) ) ? get_post_meta( get_the_ID(), 'trx_addons_options', true ) : false;
    $video_autoplay = ! empty( $post_meta['video_autoplay'] )
        && ! empty( $post_meta['video_list'] )
        && is_array( $post_meta['video_list'] )
        && count( $post_meta['video_list'] ) == 1
        && ( ! empty( $post_meta['video_list'][0]['video_url'] ) || ! empty( $post_meta['video_list'][0]['video_embed'] ) );

	// Post title and meta
    ob_start();
	planty_show_post_title_and_meta( array(
										'share_type'   => 'list',
										'author_avatar' => true,
										'show_labels'  => true,
										'add_spaces'    => true,
										)
									);
	$planty_post_header = ob_get_contents();
	ob_end_clean();

	// Featured image
	ob_start();
	planty_show_post_featured_image( array(
		'thumb_bg' => false,
		'popup'    => true,
        'class_avg' => $video_autoplay
            ? 'with_video with_video_autoplay'	// 'with_thumb' is removed
            : '',
        'autoplay'  => $video_autoplay,
        'post_meta' => $post_meta
	) );
	$planty_post_header .= ob_get_contents();
	ob_end_clean();

	$planty_with_featured_image = planty_is_with_featured_image( $planty_post_header );

	if ( strpos( $planty_post_header, 'post_featured' ) !== false
		|| strpos( $planty_post_header, 'post_title' ) !== false
		|| strpos( $planty_post_header, 'post_meta' ) !== false
	) {
		?>
		<div class="post_header_wrap post_header_wrap_in_header post_header_wrap_style_<?php
			echo esc_attr( planty_get_theme_option( 'single_style' ) );
			if ( $planty_with_featured_image ) {
				echo ' with_featured_image';
			}
		?>">
			<div class="content_wrap">
				<?php
				do_action( 'planty_action_before_post_header' );
				planty_show_layout( $planty_post_header );
				do_action( 'planty_action_after_post_header' );
				?>
			</div>
		</div>
		<?php
	}
}
