<?php
/**
 * The template to display the widgets area in the footer
 *
 * @package PLANTY
 * @since PLANTY 1.0.10
 */

// Footer sidebar
$planty_footer_name    = planty_get_theme_option( 'footer_widgets' );
$planty_footer_present = ! planty_is_off( $planty_footer_name ) && is_active_sidebar( $planty_footer_name );
if ( $planty_footer_present ) {
	planty_storage_set( 'current_sidebar', 'footer' );
	$planty_footer_wide = planty_get_theme_option( 'footer_wide' );
	ob_start();
	if ( is_active_sidebar( $planty_footer_name ) ) {
		dynamic_sidebar( $planty_footer_name );
	}
	$planty_out = trim( ob_get_contents() );
	ob_end_clean();
	if ( ! empty( $planty_out ) ) {
		$planty_out          = preg_replace( "/<\\/aside>[\r\n\s]*<aside/", '</aside><aside', $planty_out );
		$planty_need_columns = true;   //or check: strpos($planty_out, 'columns_wrap')===false;
		if ( $planty_need_columns ) {
			$planty_columns = max( 0, (int) planty_get_theme_option( 'footer_columns' ) );			
			if ( 0 == $planty_columns ) {
				$planty_columns = min( 4, max( 1, planty_tags_count( $planty_out, 'aside' ) ) );
			}
			if ( $planty_columns > 1 ) {
				$planty_out = preg_replace( '/<aside([^>]*)class="widget/', '<aside$1class="column-1_' . esc_attr( $planty_columns ) . ' widget', $planty_out );
			} else {
				$planty_need_columns = false;
			}
		}
		?>
		<div class="footer_widgets_wrap widget_area<?php echo ! empty( $planty_footer_wide ) ? ' footer_fullwidth' : ''; ?> sc_layouts_row sc_layouts_row_type_normal">
			<?php do_action( 'planty_action_before_sidebar_wrap', 'footer' ); ?>
			<div class="footer_widgets_inner widget_area_inner">
				<?php
				if ( ! $planty_footer_wide ) {
					?>
					<div class="content_wrap">
					<?php
				}
				if ( $planty_need_columns ) {
					?>
					<div class="columns_wrap">
					<?php
				}
				do_action( 'planty_action_before_sidebar', 'footer' );
				planty_show_layout( $planty_out );
				do_action( 'planty_action_after_sidebar', 'footer' );
				if ( $planty_need_columns ) {
					?>
					</div><!-- /.columns_wrap -->
					<?php
				}
				if ( ! $planty_footer_wide ) {
					?>
					</div><!-- /.content_wrap -->
					<?php
				}
				?>
			</div><!-- /.footer_widgets_inner -->
			<?php do_action( 'planty_action_after_sidebar_wrap', 'footer' ); ?>
		</div><!-- /.footer_widgets_wrap -->
		<?php
	}
}
