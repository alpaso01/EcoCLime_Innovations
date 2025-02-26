<?php
/**
 * The template to display single post
 *
 * @package PLANTY
 * @since PLANTY 1.0
 */

// Full post loading
$full_post_loading          = planty_get_value_gp( 'action' ) == 'full_post_loading';

// Prev post loading
$prev_post_loading          = planty_get_value_gp( 'action' ) == 'prev_post_loading';
$prev_post_loading_type     = planty_get_theme_option( 'posts_navigation_scroll_which_block' );

// Position of the related posts
$planty_related_position   = planty_get_theme_option( 'related_position' );

// Type of the prev/next post navigation
$planty_posts_navigation   = planty_get_theme_option( 'posts_navigation' );
$planty_prev_post          = false;
$planty_prev_post_same_cat = planty_get_theme_option( 'posts_navigation_scroll_same_cat' );

// Rewrite style of the single post if current post loading via AJAX and featured image and title is not in the content
if ( ( $full_post_loading 
		|| 
		( $prev_post_loading && 'article' == $prev_post_loading_type )
	) 
	&& 
	! in_array( planty_get_theme_option( 'single_style' ), array( 'style-6' ) )
) {
	planty_storage_set_array( 'options_meta', 'single_style', 'style-6' );
}

do_action( 'planty_action_prev_post_loading', $prev_post_loading, $prev_post_loading_type );

get_header();

while ( have_posts() ) {

	the_post();

	// Type of the prev/next post navigation
	if ( 'scroll' == $planty_posts_navigation ) {
		$planty_prev_post = get_previous_post( $planty_prev_post_same_cat );  // Get post from same category
		if ( ! $planty_prev_post && $planty_prev_post_same_cat ) {
			$planty_prev_post = get_previous_post( false );                    // Get post from any category
		}
		if ( ! $planty_prev_post ) {
			$planty_posts_navigation = 'links';
		}
	}

	// Override some theme options to display featured image, title and post meta in the dynamic loaded posts
	if ( $full_post_loading || ( $prev_post_loading && $planty_prev_post ) ) {
		planty_sc_layouts_showed( 'featured', false );
		planty_sc_layouts_showed( 'title', false );
		planty_sc_layouts_showed( 'postmeta', false );
	}

	// If related posts should be inside the content
	if ( strpos( $planty_related_position, 'inside' ) === 0 ) {
		ob_start();
	}

	// Display post's content
	get_template_part( apply_filters( 'planty_filter_get_template_part', 'templates/content', 'single-' . planty_get_theme_option( 'single_style' ) ), 'single-' . planty_get_theme_option( 'single_style' ) );

	// If related posts should be inside the content
	if ( strpos( $planty_related_position, 'inside' ) === 0 ) {
		$planty_content = ob_get_contents();
		ob_end_clean();

		ob_start();
		do_action( 'planty_action_related_posts' );
		$planty_related_content = ob_get_contents();
		ob_end_clean();

		if ( ! empty( $planty_related_content ) ) {
			$planty_related_position_inside = max( 0, min( 9, planty_get_theme_option( 'related_position_inside' ) ) );
			if ( 0 == $planty_related_position_inside ) {
				$planty_related_position_inside = mt_rand( 1, 9 );
			}

			$planty_p_number         = 0;
			$planty_related_inserted = false;
			$planty_in_block         = false;
			$planty_content_start    = strpos( $planty_content, '<div class="post_content' );
			$planty_content_end      = strrpos( $planty_content, '</div>' );

			for ( $i = max( 0, $planty_content_start ); $i < min( strlen( $planty_content ) - 3, $planty_content_end ); $i++ ) {
				if ( $planty_content[ $i ] != '<' ) {
					continue;
				}
				if ( $planty_in_block ) {
					if ( strtolower( substr( $planty_content, $i + 1, 12 ) ) == '/blockquote>' ) {
						$planty_in_block = false;
						$i += 12;
					}
					continue;
				} else if ( strtolower( substr( $planty_content, $i + 1, 10 ) ) == 'blockquote' && in_array( $planty_content[ $i + 11 ], array( '>', ' ' ) ) ) {
					$planty_in_block = true;
					$i += 11;
					continue;
				} else if ( 'p' == $planty_content[ $i + 1 ] && in_array( $planty_content[ $i + 2 ], array( '>', ' ' ) ) ) {
					$planty_p_number++;
					if ( $planty_related_position_inside == $planty_p_number ) {
						$planty_related_inserted = true;
						$planty_content = ( $i > 0 ? substr( $planty_content, 0, $i ) : '' )
											. $planty_related_content
											. substr( $planty_content, $i );
					}
				}
			}
			if ( ! $planty_related_inserted ) {
				if ( $planty_content_end > 0 ) {
					$planty_content = substr( $planty_content, 0, $planty_content_end ) . $planty_related_content . substr( $planty_content, $planty_content_end );
				} else {
					$planty_content .= $planty_related_content;
				}
			}
		}

		planty_show_layout( $planty_content );
	}

	// Comments
	do_action( 'planty_action_before_comments' );
	comments_template();
	do_action( 'planty_action_after_comments' );

	// Related posts
	if ( 'below_content' == $planty_related_position
		&& ( 'scroll' != $planty_posts_navigation || planty_get_theme_option( 'posts_navigation_scroll_hide_related' ) == 0 )
		&& ( ! $full_post_loading || planty_get_theme_option( 'open_full_post_hide_related' ) == 0 )
	) {
		do_action( 'planty_action_related_posts' );
	}

	// Post navigation: type 'scroll'
	if ( 'scroll' == $planty_posts_navigation && ! $full_post_loading ) {
		?>
		<div class="nav-links-single-scroll"
			data-post-id="<?php echo esc_attr( get_the_ID( $planty_prev_post ) ); ?>"
			data-post-link="<?php echo esc_attr( get_permalink( $planty_prev_post ) ); ?>"
			data-post-title="<?php the_title_attribute( array( 'post' => $planty_prev_post ) ); ?>"
			<?php do_action( 'planty_action_nav_links_single_scroll_data', $planty_prev_post ); ?>
		></div>
		<?php
	}
}

get_footer();
