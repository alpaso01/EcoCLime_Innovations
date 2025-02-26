<?php
/**
 * The Footer: widgets area, logo, footer menu and socials
 *
 * @package PLANTY
 * @since PLANTY 1.0
 */

							do_action( 'planty_action_page_content_end_text' );
							
							// Widgets area below the content
							planty_create_widgets_area( 'widgets_below_content' );
						
							do_action( 'planty_action_page_content_end' );
							?>
						</div>
						<?php
						
						do_action( 'planty_action_after_page_content' );

						// Show main sidebar
						get_sidebar();

						do_action( 'planty_action_content_wrap_end' );
						?>
					</div>
					<?php

					do_action( 'planty_action_after_content_wrap' );

					// Widgets area below the page and related posts below the page
					$planty_body_style = planty_get_theme_option( 'body_style' );
					$planty_widgets_name = planty_get_theme_option( 'widgets_below_page' );
					$planty_show_widgets = ! planty_is_off( $planty_widgets_name ) && is_active_sidebar( $planty_widgets_name );
					$planty_show_related = planty_is_single() && planty_get_theme_option( 'related_position' ) == 'below_page';
					if ( $planty_show_widgets || $planty_show_related ) {
						if ( 'fullscreen' != $planty_body_style ) {
							?>
							<div class="content_wrap">
							<?php
						}
						// Show related posts before footer
						if ( $planty_show_related ) {
							do_action( 'planty_action_related_posts' );
						}

						// Widgets area below page content
						if ( $planty_show_widgets ) {
							planty_create_widgets_area( 'widgets_below_page' );
						}
						if ( 'fullscreen' != $planty_body_style ) {
							?>
							</div>
							<?php
						}
					}
					do_action( 'planty_action_page_content_wrap_end' );
					?>
			</div>
			<?php
			do_action( 'planty_action_after_page_content_wrap' );

			// Don't display the footer elements while actions 'full_post_loading' and 'prev_post_loading'
			if ( ( ! planty_is_singular( 'post' ) && ! planty_is_singular( 'attachment' ) ) || ! in_array ( planty_get_value_gp( 'action' ), array( 'full_post_loading', 'prev_post_loading' ) ) ) {
				
				// Skip link anchor to fast access to the footer from keyboard
				?>
				<a id="footer_skip_link_anchor" class="planty_skip_link_anchor" href="#"></a>
				<?php

				do_action( 'planty_action_before_footer' );

				// Footer
				$planty_footer_type = planty_get_theme_option( 'footer_type' );
				if ( 'custom' == $planty_footer_type && ! planty_is_layouts_available() ) {
					$planty_footer_type = 'default';
				}
				get_template_part( apply_filters( 'planty_filter_get_template_part', "templates/footer-" . sanitize_file_name( $planty_footer_type ) ) );

				do_action( 'planty_action_after_footer' );

			}
			?>

			<?php do_action( 'planty_action_page_wrap_end' ); ?>

		</div>

		<?php do_action( 'planty_action_after_page_wrap' ); ?>

	</div>

	<?php do_action( 'planty_action_after_body' ); ?>

	<?php wp_footer(); ?>

</body>
</html>