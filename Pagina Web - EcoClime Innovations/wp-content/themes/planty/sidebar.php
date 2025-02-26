<?php
/**
 * The Sidebar containing the main widget areas.
 *
 * @package PLANTY
 * @since PLANTY 1.0
 */

if ( planty_sidebar_present() ) {
	
	$planty_sidebar_type = planty_get_theme_option( 'sidebar_type' );
	if ( 'custom' == $planty_sidebar_type && ! planty_is_layouts_available() ) {
		$planty_sidebar_type = 'default';
	}
	
	// Catch output to the buffer
	ob_start();
	if ( 'default' == $planty_sidebar_type ) {
		// Default sidebar with widgets
		$planty_sidebar_name = planty_get_theme_option( 'sidebar_widgets' );
		planty_storage_set( 'current_sidebar', 'sidebar' );
		if ( is_active_sidebar( $planty_sidebar_name ) ) {
			dynamic_sidebar( $planty_sidebar_name );
		}
	} else {
		// Custom sidebar from Layouts Builder
		$planty_sidebar_id = planty_get_custom_sidebar_id();
		do_action( 'planty_action_show_layout', $planty_sidebar_id );
	}
	$planty_out = trim( ob_get_contents() );
	ob_end_clean();
	
	// If any html is present - display it
	if ( ! empty( $planty_out ) ) {
		$planty_sidebar_position    = planty_get_theme_option( 'sidebar_position' );
		$planty_sidebar_position_ss = planty_get_theme_option( 'sidebar_position_ss' );
		?>
		<div class="sidebar widget_area
			<?php
			echo ' ' . esc_attr( $planty_sidebar_position );
			echo ' sidebar_' . esc_attr( $planty_sidebar_position_ss );
			echo ' sidebar_' . esc_attr( $planty_sidebar_type );

			$planty_sidebar_scheme = apply_filters( 'planty_filter_sidebar_scheme', planty_get_theme_option( 'sidebar_scheme' ) );
			if ( ! empty( $planty_sidebar_scheme ) && ! planty_is_inherit( $planty_sidebar_scheme ) && 'custom' != $planty_sidebar_type ) {
				echo ' scheme_' . esc_attr( $planty_sidebar_scheme );
			}
			?>
		" role="complementary">
			<?php

			// Skip link anchor to fast access to the sidebar from keyboard
			?>
			<a id="sidebar_skip_link_anchor" class="planty_skip_link_anchor" href="#"></a>
			<?php

			do_action( 'planty_action_before_sidebar_wrap', 'sidebar' );

			// Button to show/hide sidebar on mobile
			if ( in_array( $planty_sidebar_position_ss, array( 'above', 'float' ) ) ) {
				$planty_title = apply_filters( 'planty_filter_sidebar_control_title', 'float' == $planty_sidebar_position_ss ? esc_html__( 'Show Sidebar', 'planty' ) : '' );
				$planty_text  = apply_filters( 'planty_filter_sidebar_control_text', 'above' == $planty_sidebar_position_ss ? esc_html__( 'Show Sidebar', 'planty' ) : '' );
				?>
				<a href="#" class="sidebar_control" title="<?php echo esc_attr( $planty_title ); ?>"><?php echo esc_html( $planty_text ); ?></a>
				<?php
			}
			?>
			<div class="sidebar_inner">
				<?php
				do_action( 'planty_action_before_sidebar', 'sidebar' );
				planty_show_layout( preg_replace( "/<\/aside>[\r\n\s]*<aside/", '</aside><aside', $planty_out ) );
				do_action( 'planty_action_after_sidebar', 'sidebar' );
				?>
			</div>
			<?php

			do_action( 'planty_action_after_sidebar_wrap', 'sidebar' );

			?>
		</div>
		<div class="clearfix"></div>
		<?php
	}
}
