<?php
/**
 * The main template file.
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 * Learn more: //codex.wordpress.org/Template_Hierarchy
 *
 * @package PLANTY
 * @since PLANTY 1.0
 */

$planty_template = apply_filters( 'planty_filter_get_template_part', planty_blog_archive_get_template() );

if ( ! empty( $planty_template ) && 'index' != $planty_template ) {

	get_template_part( $planty_template );

} else {

	planty_storage_set( 'blog_archive', true );

	get_header();

	if ( have_posts() ) {

		// Query params
		$planty_stickies   = is_home()
								|| ( in_array( planty_get_theme_option( 'post_type' ), array( '', 'post' ) )
									&& (int) planty_get_theme_option( 'parent_cat' ) == 0
									)
										? get_option( 'sticky_posts' )
										: false;
		$planty_post_type  = planty_get_theme_option( 'post_type' );
		$planty_args       = array(
								'blog_style'     => planty_get_theme_option( 'blog_style' ),
								'post_type'      => $planty_post_type,
								'taxonomy'       => planty_get_post_type_taxonomy( $planty_post_type ),
								'parent_cat'     => planty_get_theme_option( 'parent_cat' ),
								'posts_per_page' => planty_get_theme_option( 'posts_per_page' ),
								'sticky'         => planty_get_theme_option( 'sticky_style' ) == 'columns'
															&& is_array( $planty_stickies )
															&& count( $planty_stickies ) > 0
															&& get_query_var( 'paged' ) < 1
								);

		planty_blog_archive_start();

		do_action( 'planty_action_blog_archive_start' );

		if ( is_author() ) {
			do_action( 'planty_action_before_page_author' );
			get_template_part( apply_filters( 'planty_filter_get_template_part', 'templates/author-page' ) );
			do_action( 'planty_action_after_page_author' );
		}

		if ( planty_get_theme_option( 'show_filters' ) ) {
			do_action( 'planty_action_before_page_filters' );
			planty_show_filters( $planty_args );
			do_action( 'planty_action_after_page_filters' );
		} else {
			do_action( 'planty_action_before_page_posts' );
			planty_show_posts( array_merge( $planty_args, array( 'cat' => $planty_args['parent_cat'] ) ) );
			do_action( 'planty_action_after_page_posts' );
		}

		do_action( 'planty_action_blog_archive_end' );

		planty_blog_archive_end();

	} else {

		if ( is_search() ) {
			get_template_part( apply_filters( 'planty_filter_get_template_part', 'templates/content', 'none-search' ), 'none-search' );
		} else {
			get_template_part( apply_filters( 'planty_filter_get_template_part', 'templates/content', 'none-archive' ), 'none-archive' );
		}
	}

	get_footer();
}
