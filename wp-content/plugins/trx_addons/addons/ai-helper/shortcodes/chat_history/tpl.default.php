<?php
/**
 * The style "default" of the Chat History
 *
 * @package ThemeREX Addons
 * @since v2.26.3
 */

$args = get_query_var('trx_addons_args_sc_chat_history');

$history = trx_addons_sc_chat_history_get_saved_messages( $args['number'] );

if ( count( $history ) > 0 ) {

	do_action( 'trx_addons_action_sc_chat_history_before', $args );

	?><div <?php if ( ! empty( $args['id'] ) ) echo ' id="' . esc_attr( $args['id'] ) . '"'; ?> 
		data-chat-id="<?php echo esc_attr( $args['chat_id'] ); ?>"
		data-max-items="<?php echo esc_attr( $args['number'] ); ?>"
		class="sc_chat_history sc_chat_history_<?php
			echo esc_attr( $args['type'] );
			if ( ! empty( $args['class'] ) ) echo ' ' . esc_attr( $args['class'] );
			?>"<?php
		if ( ! empty( $args['css'] ) ) echo ' style="' . esc_attr( $args['css'] ) . '"';
		trx_addons_sc_show_attributes( 'sc_chat_history', $args, 'sc_wrapper' );
	?>><?php

		trx_addons_sc_show_titles( 'sc_chat_history', $args );

		do_action( 'trx_addons_action_sc_chat_history_before_content', $args );

		?><div class="sc_chat_history_content sc_item_content"<?php trx_addons_sc_show_attributes( 'sc_chat_history', $args, 'sc_items_wrapper' ); ?>><?php
			do_action( 'trx_addons_action_sc_chat_history_before_list', $args );
			?><ul class="sc_chat_history_list">
				<?php
				for ( $i = 0; $i < min( $args['number'], count( $history ) ); $i++ ) {
					?><li class="sc_chat_history_item">
						<a href="javascript:void(0)"
							data-chat-messages="<?php echo esc_attr( json_encode( $history[ $i ]['messages'] ) ); ?>"
							data-chat-thread-id="<?php echo esc_attr( ! empty( $history[ $i ]['thread_id'] ) ? $history[ $i ]['thread_id'] : '' ); ?>"
						><?php
							echo esc_html( $history[ $i ]['topic'] );
						?></a>
					</li><?php
				}
				?>
			</ul><?php
			do_action( 'trx_addons_action_sc_chat_history_after_list', $args );
		?></div>

		<?php
		do_action( 'trx_addons_action_sc_chat_history_after_content', $args );

		trx_addons_sc_show_links( 'sc_chat_history', $args );
		?>

	</div><?php

	do_action( 'trx_addons_action_sc_chat_history_after', $args );
}