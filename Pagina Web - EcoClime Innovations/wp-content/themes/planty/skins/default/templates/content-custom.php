<?php
/**
 * The custom template to display the content
 *
 * Used for index/archive/search.
 *
 * @package PLANTY
 * @since PLANTY 1.0.50
 */

$planty_template_args = get_query_var( 'planty_template_args' );
if ( is_array( $planty_template_args ) ) {
	$planty_columns    = empty( $planty_template_args['columns'] ) ? 2 : max( 1, $planty_template_args['columns'] );
	$planty_blog_style = array( $planty_template_args['type'], $planty_columns );
} else {
	$planty_template_args = array();
	$planty_blog_style = explode( '_', planty_get_theme_option( 'blog_style' ) );
	$planty_columns    = empty( $planty_blog_style[1] ) ? 2 : max( 1, $planty_blog_style[1] );
}
$planty_blog_id       = planty_get_custom_blog_id( join( '_', $planty_blog_style ) );
$planty_blog_style[0] = str_replace( 'blog-custom-', '', $planty_blog_style[0] );
$planty_expanded      = ! planty_sidebar_present() && planty_get_theme_option( 'expand_content' ) == 'expand';
$planty_components    = ! empty( $planty_template_args['meta_parts'] )
							? ( is_array( $planty_template_args['meta_parts'] )
								? join( ',', $planty_template_args['meta_parts'] )
								: $planty_template_args['meta_parts']
								)
							: planty_array_get_keys_by_value( planty_get_theme_option( 'meta_parts' ) );
$planty_post_format   = get_post_format();
$planty_post_format   = empty( $planty_post_format ) ? 'standard' : str_replace( 'post-format-', '', $planty_post_format );

$planty_blog_meta     = planty_get_custom_layout_meta( $planty_blog_id );
$planty_custom_style  = ! empty( $planty_blog_meta['scripts_required'] ) ? $planty_blog_meta['scripts_required'] : 'none';

if ( ! empty( $planty_template_args['slider'] ) || $planty_columns > 1 || ! planty_is_off( $planty_custom_style ) ) {
	?><div class="
		<?php
		if ( ! empty( $planty_template_args['slider'] ) ) {
			echo 'slider-slide swiper-slide';
		} else {
			echo esc_attr( ( planty_is_off( $planty_custom_style ) ? 'column' : sprintf( '%1$s_item %1$s_item', $planty_custom_style ) ) . "-1_{$planty_columns}" );
		}
		?>
	">
	<?php
}
?>
<article id="post-<?php the_ID(); ?>" data-post-id="<?php the_ID(); ?>"
	<?php
	post_class(
			'post_item post_item_container post_format_' . esc_attr( $planty_post_format )
					. ' post_layout_custom post_layout_custom_' . esc_attr( $planty_columns )
					. ' post_layout_' . esc_attr( $planty_blog_style[0] )
					. ' post_layout_' . esc_attr( $planty_blog_style[0] ) . '_' . esc_attr( $planty_columns )
					. ( ! planty_is_off( $planty_custom_style )
						? ' post_layout_' . esc_attr( $planty_custom_style )
							. ' post_layout_' . esc_attr( $planty_custom_style ) . '_' . esc_attr( $planty_columns )
						: ''
						)
		);
	planty_add_blog_animation( $planty_template_args );
	?>
>
	<?php
	// Sticky label
	if ( is_sticky() && ! is_paged() ) {
		?>
		<span class="post_label label_sticky"></span>
		<?php
	}
	// Custom layout
	do_action( 'planty_action_show_layout', $planty_blog_id, get_the_ID() );
	?>
</article><?php
if ( ! empty( $planty_template_args['slider'] ) || $planty_columns > 1 || ! planty_is_off( $planty_custom_style ) ) {
	?></div><?php
	// Need opening PHP-tag above just after </div>, because <div> is a inline-block element (used as column)!
}
