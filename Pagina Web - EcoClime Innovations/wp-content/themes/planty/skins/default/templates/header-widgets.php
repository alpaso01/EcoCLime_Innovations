<?php
/**
 * The template to display the widgets area in the header
 *
 * @package PLANTY
 * @since PLANTY 1.0
 */

// Header sidebar
$planty_header_name    = planty_get_theme_option( 'header_widgets' );
$planty_header_present = ! planty_is_off( $planty_header_name ) && is_active_sidebar( $planty_header_name );
if ( $planty_header_present ) {
	planty_storage_set( 'current_sidebar', 'header' );
	$planty_header_wide = planty_get_theme_option( 'header_wide' );
	ob_start();
	if ( is_active_sidebar( $planty_header_name ) ) {
		dynamic_sidebar( $planty_header_name );
	}
	$planty_widgets_output = ob_get_contents();
	ob_end_clean();
	if ( ! empty( $planty_widgets_output ) ) {
		$planty_widgets_output = preg_replace( "/<\/aside>[\r\n\s]*<aside/", '</aside><aside', $planty_widgets_output );
		$planty_need_columns   = strpos( $planty_widgets_output, 'columns_wrap' ) === false;
		if ( $planty_need_columns ) {
			$planty_columns = max( 0, (int) planty_get_theme_option( 'header_columns' ) );
			if ( 0 == $planty_columns ) {
				$planty_columns = min( 6, max( 1, planty_tags_count( $planty_widgets_output, 'aside' ) ) );
			}
			if ( $planty_columns > 1 ) {
				$planty_widgets_output = preg_replace( '/<aside([^>]*)class="widget/', '<aside$1class="column-1_' . esc_attr( $planty_columns ) . ' widget', $planty_widgets_output );
			} else {
				$planty_need_columns = false;
			}
		}
		?>
		<div class="header_widgets_wrap widget_area<?php echo ! empty( $planty_header_wide ) ? ' header_fullwidth' : ' header_boxed'; ?>">
			<?php do_action( 'planty_action_before_sidebar_wrap', 'header' ); ?>
			<div class="header_widgets_inner widget_area_inner">
				<?php
				if ( ! $planty_header_wide ) {
					?>
					<div class="content_wrap">
					<?php
				}
				if ( $planty_need_columns ) {
					?>
					<div class="columns_wrap">
					<?php
				}
				do_action( 'planty_action_before_sidebar', 'header' );
				planty_show_layout( $planty_widgets_output );
				do_action( 'planty_action_after_sidebar', 'header' );
				if ( $planty_need_columns ) {
					?>
					</div>	<!-- /.columns_wrap -->
					<?php
				}
				if ( ! $planty_header_wide ) {
					?>
					</div>	<!-- /.content_wrap -->
					<?php
				}
				?>
			</div>	<!-- /.header_widgets_inner -->
			<?php do_action( 'planty_action_after_sidebar_wrap', 'header' ); ?>
		</div>	<!-- /.header_widgets_wrap -->
		<?php
	}
}
