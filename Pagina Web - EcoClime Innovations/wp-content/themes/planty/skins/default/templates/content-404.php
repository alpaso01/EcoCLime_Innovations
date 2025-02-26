<article <?php post_class( 'post_item_single post_item_404' ); ?>>
	<div class="post_content">
		<h1 class="page_title"><?php esc_html_e( '404', 'planty' ); ?></h1>
		<div class="page_info">
			<h2 class="page_subtitle"><?php esc_html_e( 'Oops...', 'planty' ); ?></h2>
			<p class="page_description"><?php echo wp_kses( __( "We're sorry, but <br>something went wrong.", 'planty' ), 'planty_kses_content' ); ?></p>
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="go_home theme_button"><?php esc_html_e( 'Homepage', 'planty' ); ?></a>
		</div>

		<?php
		// SVG
		$svg_bg_1 = planty_get_svg_from_file(planty_get_file_dir('images/svg_bg_4.svg'));
		$svg_bg_2 = planty_get_svg_from_file(planty_get_file_dir('images/svg_bg_2.svg'));
		$svg_bg_3 = planty_get_svg_from_file(planty_get_file_dir('images/svg_bg_5.svg'));
		$svg_bg_4 = planty_get_svg_from_file(planty_get_file_dir('images/svg_bg_5.svg'));
		$svg_bg = ($svg_bg_1 ? '<span class="svg-1">'.$svg_bg_1.'</span>' : '').($svg_bg_2 ? '<span class="svg-2">'.$svg_bg_2.'</span>' : '').($svg_bg_3 ? '<span class="svg-3">'.$svg_bg_3.'</span>' : '').($svg_bg_4 ? '<span class="svg-4">'.$svg_bg_4.'</span>' : '');
		if(!empty($svg_bg)){ ?>
		<div class="all-svg">
			<?php planty_show_layout($svg_bg); ?>
		</div>
		<?php }	?>

	</div>
</article>