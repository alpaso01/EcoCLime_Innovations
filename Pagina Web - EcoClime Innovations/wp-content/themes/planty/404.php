<?php
/**
 * The template to display the 404 page
 *
 * @package PLANTY
 * @since PLANTY 1.0
 */

get_header();

get_template_part( apply_filters( 'planty_filter_get_template_part', 'templates/content', '404' ), '404' );

get_footer();
