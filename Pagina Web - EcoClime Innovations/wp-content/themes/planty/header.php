<?php
/**
 * The Header: Logo and main menu
 *
 * @package PLANTY
 * @since PLANTY 1.0
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js<?php
	// Class scheme_xxx need in the <html> as context for the <body>!
	echo ' scheme_' . esc_attr( planty_get_theme_option( 'color_scheme' ) );
?>">

<head>
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>

	<?php
	if ( function_exists( 'wp_body_open' ) ) {
		wp_body_open();
	} else {
		do_action( 'wp_body_open' );
	}
	do_action( 'planty_action_before_body' );
	?>

	<div class="<?php echo esc_attr( apply_filters( 'planty_filter_body_wrap_class', 'body_wrap' ) ); ?>" <?php do_action('planty_action_body_wrap_attributes'); ?>>

		<?php do_action( 'planty_action_before_page_wrap' ); ?>

		<div class="<?php echo esc_attr( apply_filters( 'planty_filter_page_wrap_class', 'page_wrap' ) ); ?>" <?php do_action('planty_action_page_wrap_attributes'); ?>>

			<?php do_action( 'planty_action_page_wrap_start' ); ?>

			<?php
			$planty_full_post_loading = ( planty_is_singular( 'post' ) || planty_is_singular( 'attachment' ) ) && planty_get_value_gp( 'action' ) == 'full_post_loading';
			$planty_prev_post_loading = ( planty_is_singular( 'post' ) || planty_is_singular( 'attachment' ) ) && planty_get_value_gp( 'action' ) == 'prev_post_loading';

			// Don't display the header elements while actions 'full_post_loading' and 'prev_post_loading'
			if ( ! $planty_full_post_loading && ! $planty_prev_post_loading ) {

				// Short links to fast access to the content, sidebar and footer from the keyboard
				?>
				<a class="planty_skip_link skip_to_content_link" href="#content_skip_link_anchor" tabindex="<?php echo esc_attr( apply_filters( 'planty_filter_skip_links_tabindex', 1 ) ); ?>"><?php esc_html_e( "Skip to content", 'planty' ); ?></a>
				<?php if ( planty_sidebar_present() ) { ?>
				<a class="planty_skip_link skip_to_sidebar_link" href="#sidebar_skip_link_anchor" tabindex="<?php echo esc_attr( apply_filters( 'planty_filter_skip_links_tabindex', 1 ) ); ?>"><?php esc_html_e( "Skip to sidebar", 'planty' ); ?></a>
				<?php } ?>
				<a class="planty_skip_link skip_to_footer_link" href="#footer_skip_link_anchor" tabindex="<?php echo esc_attr( apply_filters( 'planty_filter_skip_links_tabindex', 1 ) ); ?>"><?php esc_html_e( "Skip to footer", 'planty' ); ?></a>

				<?php
				do_action( 'planty_action_before_header' );

				// Header
				$planty_header_type = planty_get_theme_option( 'header_type' );
				if ( 'custom' == $planty_header_type && ! planty_is_layouts_available() ) {
					$planty_header_type = 'default';
				}
				get_template_part( apply_filters( 'planty_filter_get_template_part', "templates/header-" . sanitize_file_name( $planty_header_type ) ) );

				// Side menu
				if ( in_array( planty_get_theme_option( 'menu_side' ), array( 'left', 'right' ) ) ) {
					get_template_part( apply_filters( 'planty_filter_get_template_part', 'templates/header-navi-side' ) );
				}

				// Mobile menu
				get_template_part( apply_filters( 'planty_filter_get_template_part', 'templates/header-navi-mobile' ) );

				do_action( 'planty_action_after_header' );

			}
			?>

			<?php do_action( 'planty_action_before_page_content_wrap' ); ?>

			<div class="page_content_wrap<?php
				if ( planty_is_off( planty_get_theme_option( 'remove_margins' ) ) ) {
					if ( empty( $planty_header_type ) ) {
						$planty_header_type = planty_get_theme_option( 'header_type' );
					}
					if ( 'custom' == $planty_header_type && planty_is_layouts_available() ) {
						$planty_header_id = planty_get_custom_header_id();
						if ( $planty_header_id > 0 ) {
							$planty_header_meta = planty_get_custom_layout_meta( $planty_header_id );
							if ( ! empty( $planty_header_meta['margin'] ) ) {
								?> page_content_wrap_custom_header_margin<?php
							}
						}
					}
					$planty_footer_type = planty_get_theme_option( 'footer_type' );
					if ( 'custom' == $planty_footer_type && planty_is_layouts_available() ) {
						$planty_footer_id = planty_get_custom_footer_id();
						if ( $planty_footer_id ) {
							$planty_footer_meta = planty_get_custom_layout_meta( $planty_footer_id );
							if ( ! empty( $planty_footer_meta['margin'] ) ) {
								?> page_content_wrap_custom_footer_margin<?php
							}
						}
					}
				}
				do_action( 'planty_action_page_content_wrap_class', $planty_prev_post_loading );
				?>"<?php
				if ( apply_filters( 'planty_filter_is_prev_post_loading', $planty_prev_post_loading ) ) {
					?> data-single-style="<?php echo esc_attr( planty_get_theme_option( 'single_style' ) ); ?>"<?php
				}
				do_action( 'planty_action_page_content_wrap_data', $planty_prev_post_loading );
			?>>
				<?php
				do_action( 'planty_action_page_content_wrap', $planty_full_post_loading || $planty_prev_post_loading );

				// Single posts banner
				if ( apply_filters( 'planty_filter_single_post_header', planty_is_singular( 'post' ) || planty_is_singular( 'attachment' ) ) ) {
					if ( $planty_prev_post_loading ) {
						if ( planty_get_theme_option( 'posts_navigation_scroll_which_block' ) != 'article' ) {
							do_action( 'planty_action_between_posts' );
						}
					}
					// Single post thumbnail and title
					$planty_path = apply_filters( 'planty_filter_get_template_part', 'templates/single-styles/' . planty_get_theme_option( 'single_style' ) );
					if ( planty_get_file_dir( $planty_path . '.php' ) != '' ) {
						get_template_part( $planty_path );
					}
				}

				// Widgets area above page
				$planty_body_style   = planty_get_theme_option( 'body_style' );
				$planty_widgets_name = planty_get_theme_option( 'widgets_above_page' );
				$planty_show_widgets = ! planty_is_off( $planty_widgets_name ) && is_active_sidebar( $planty_widgets_name );
				if ( $planty_show_widgets ) {
					if ( 'fullscreen' != $planty_body_style ) {
						?>
						<div class="content_wrap">
							<?php
					}
					planty_create_widgets_area( 'widgets_above_page' );
					if ( 'fullscreen' != $planty_body_style ) {
						?>
						</div>
						<?php
					}
				}

				// Content area
				do_action( 'planty_action_before_content_wrap' );
				?>
				<div class="content_wrap<?php echo 'fullscreen' == $planty_body_style ? '_fullscreen' : ''; ?>">

					<?php do_action( 'planty_action_content_wrap_start' ); ?>

					<div class="content">
						<?php
						do_action( 'planty_action_page_content_start' );

						// Skip link anchor to fast access to the content from keyboard
						?>
						<a id="content_skip_link_anchor" class="planty_skip_link_anchor" href="#"></a>
						<?php
						// Single posts banner between prev/next posts
						if ( ( planty_is_singular( 'post' ) || planty_is_singular( 'attachment' ) )
							&& $planty_prev_post_loading 
							&& planty_get_theme_option( 'posts_navigation_scroll_which_block' ) == 'article'
						) {
							do_action( 'planty_action_between_posts' );
						}

						// Widgets area above content
						planty_create_widgets_area( 'widgets_above_content' );

						do_action( 'planty_action_page_content_start_text' );
