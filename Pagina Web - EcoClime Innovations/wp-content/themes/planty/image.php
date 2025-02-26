<?php
/**
 * The template to display the attachment
 *
 * @package PLANTY
 * @since PLANTY 1.0
 */


get_header();

while ( have_posts() ) {
	the_post();

	// Display post's content
	get_template_part( apply_filters( 'planty_filter_get_template_part', 'templates/content', 'single-' . planty_get_theme_option( 'single_style' ) ), 'single-' . planty_get_theme_option( 'single_style' ) );

	// Parent post navigation.
	$planty_posts_navigation = planty_get_theme_option( 'posts_navigation' );
	if ( 'links' == $planty_posts_navigation ) {
		?>
		<div class="nav-links-single<?php
			if ( ! planty_is_off( planty_get_theme_option( 'posts_navigation_fixed' ) ) ) {
				echo ' nav-links-fixed fixed';
			}
		?>">
			<?php
			the_post_navigation( apply_filters( 'planty_filter_post_navigation_args', array(
					'prev_text' => '<span class="nav-arrow"></span>'
						. '<span class="meta-nav" aria-hidden="true">' . esc_html__( 'Published in', 'planty' ) . '</span> '
						. '<span class="screen-reader-text">' . esc_html__( 'Previous post:', 'planty' ) . '</span> '
						. '<h5 class="post-title">%title</h5>'
						. '<span class="post_date">%date</span>',
			), 'image' ) );
			?>
		</div>
		<?php
	}

	// Comments
	do_action( 'planty_action_before_comments' );
	comments_template();
	do_action( 'planty_action_after_comments' );
}

get_footer();
