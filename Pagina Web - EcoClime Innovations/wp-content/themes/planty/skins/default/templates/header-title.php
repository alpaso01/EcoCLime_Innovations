<?php
/**
 * The template to display the page title and breadcrumbs
 *
 * @package PLANTY
 * @since PLANTY 1.0
 */

// Page (category, tag, archive, author) title

if ( planty_need_page_title() ) {
	planty_sc_layouts_showed( 'title', true );
	planty_sc_layouts_showed( 'postmeta', true );
	?>
	<div class="top_panel_title sc_layouts_row sc_layouts_row_type_normal">
		<div class="content_wrap">
			<div class="sc_layouts_column sc_layouts_column_align_center">
				<div class="sc_layouts_item">
					<div class="sc_layouts_title sc_align_center">
						<?php
						// Post meta on the single post
						if ( is_single() ) {
							?>
							<div class="sc_layouts_title_meta">
							<?php
								planty_show_post_meta(
									apply_filters(
										'planty_filter_post_meta_args', array(
											'components' => join( ',', planty_array_get_keys_by_value( planty_get_theme_option( 'meta_parts' ) ) ),
											'counters'   => join( ',', planty_array_get_keys_by_value( planty_get_theme_option( 'counters' ) ) ),
											'seo'        => planty_is_on( planty_get_theme_option( 'seo_snippets' ) ),
										), 'header', 1
									)
								);
							?>
							</div>
							<?php
						}

						// Blog/Post title
						?>
						<div class="sc_layouts_title_title">
							<?php
							$planty_blog_title           = planty_get_blog_title();
							$planty_blog_title_text      = '';
							$planty_blog_title_class     = '';
							$planty_blog_title_link      = '';
							$planty_blog_title_link_text = '';
							if ( is_array( $planty_blog_title ) ) {
								$planty_blog_title_text      = $planty_blog_title['text'];
								$planty_blog_title_class     = ! empty( $planty_blog_title['class'] ) ? ' ' . $planty_blog_title['class'] : '';
								$planty_blog_title_link      = ! empty( $planty_blog_title['link'] ) ? $planty_blog_title['link'] : '';
								$planty_blog_title_link_text = ! empty( $planty_blog_title['link_text'] ) ? $planty_blog_title['link_text'] : '';
							} else {
								$planty_blog_title_text = $planty_blog_title;
							}
							?>
							<h1 itemprop="headline" class="sc_layouts_title_caption<?php echo esc_attr( $planty_blog_title_class ); ?>">
								<?php
								$planty_top_icon = planty_get_term_image_small();
								if ( ! empty( $planty_top_icon ) ) {
									$planty_attr = planty_getimagesize( $planty_top_icon );
									?>
									<img src="<?php echo esc_url( $planty_top_icon ); ?>" alt="<?php esc_attr_e( 'Site icon', 'planty' ); ?>"
										<?php
										if ( ! empty( $planty_attr[3] ) ) {
											planty_show_layout( $planty_attr[3] );
										}
										?>
									>
									<?php
								}
								echo wp_kses_data( $planty_blog_title_text );
								?>
							</h1>
							<?php
							if ( ! empty( $planty_blog_title_link ) && ! empty( $planty_blog_title_link_text ) ) {
								?>
								<a href="<?php echo esc_url( $planty_blog_title_link ); ?>" class="theme_button theme_button_small sc_layouts_title_link"><?php echo esc_html( $planty_blog_title_link_text ); ?></a>
								<?php
							}

							// Category/Tag description
							if ( ! is_paged() && ( is_category() || is_tag() || is_tax() ) ) {
								the_archive_description( '<div class="sc_layouts_title_description">', '</div>' );
							}

							?>
						</div>
						<?php

						// Breadcrumbs
						ob_start();
						do_action( 'planty_action_breadcrumbs' );
						$planty_breadcrumbs = ob_get_contents();
						ob_end_clean();
						planty_show_layout( $planty_breadcrumbs, '<div class="sc_layouts_title_breadcrumbs">', '</div>' );
						?>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php
}
