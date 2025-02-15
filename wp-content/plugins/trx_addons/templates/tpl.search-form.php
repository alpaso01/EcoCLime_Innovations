<?php
$args = get_query_var('trx_addons_args_search');
?>
<div class="search_wrap search_style_<?php echo esc_attr( $args['style'] );
			if ( ! empty( $args['ajax'] ) ) echo ' search_ajax';
			if ( ! empty( $args['class'] ) ) echo ' ' . esc_attr( $args['class'] );
		?>"<?php
	if ( ! empty( $args['overlay_animation'] ) ) {
		echo ' data-overlay-animation="' . esc_attr( $args['overlay_animation'] ) . '"';
	}
	if ( ! empty( $args['overlay_animation_exit'] ) ) {
		echo ' data-overlay-animation-exit="' . esc_attr( $args['overlay_animation_exit'] ) . '"';
	}
	if ( ! empty( $args['overlay_animation_duration'] ) ) {
		echo ' data-overlay-animation-duration="' . esc_attr( $args['overlay_animation_duration'] ) . '"';
	}
?>>
	<div class="search_form_wrap">
		<form role="search" method="get" class="search_form" action="<?php echo esc_url( apply_filters( 'trx_addons_filter_search_form_url', home_url( '/' ) ) ); ?>">
			<input type="hidden" value="<?php
				if (!empty($args['post_types'])) {
					echo esc_attr( is_array($args['post_types']) ? join(',', $args['post_types']) : $args['post_types'] );
				}
			?>" name="post_types">
			<input type="text" class="search_field" placeholder="<?php echo ! empty( $args['placeholder_text'] ) ? $args['placeholder_text'] : ''; ?>" value="<?php echo esc_attr(get_search_query()); ?>" name="s">
			<button type="submit" class="search_submit<?php echo empty( $args['icon'] ) ? ' trx_addons_icon-search' : ( empty( $args['icon_extra'] ) ? ' ' . $args['icon'] : '' ); ?>" aria-label="<?php esc_attr_e( 'Start search', 'trx_addons' ); ?>"><?php
				if ( ! empty( $args['icon_extra'] ) && class_exists( '\Elementor\Icons_Manager' ) ) {
					?><span class="search_submit_icon"><?php
						\Elementor\Icons_Manager::render_icon( $args['icon_extra'], [ 'aria-hidden' => 'true', 'class' => 'search_submit_icon' ] );
					?></span><?php
				}
			?></button>
			<?php if ( $args['style'] == 'fullscreen' ) { ?>
				<a class="search_close<?php echo empty( $args['icon_close'] ) ? ' trx_addons_icon-delete' : ( empty( $args['icon_close_extra'] ) ? ' ' . $args['icon_close'] : ''  ); ?>"><?php
					if ( ! empty( $args['icon_close_extra'] ) && class_exists( '\Elementor\Icons_Manager' ) ) {
						?><span class="search_close_icon"><?php
							\Elementor\Icons_Manager::render_icon( $args['icon_close_extra'], [ 'aria-hidden' => 'true', 'class' => 'search_close_icon' ] );
						?></span><?php
					}
					if ( ! empty( $args['close_label_text'] ) ) {
						?><span class="search_close_label"><?php echo esc_html( $args['close_label_text'] ); ?></span><?php
					}
				?></a>
			<?php } ?>
		</form>
	</div>
	<?php
	if ( $args['style'] == 'fullscreen' ) {
		?><div class="search_form_overlay"></div><?php
	}
	if ( ! empty( $args['ajax'] ) ) {
		?><div class="search_results widget_area"><a href="#" class="search_results_close trx_addons_icon-cancel"></a><div class="search_results_content"></div></div><?php
	}
	?>
</div>