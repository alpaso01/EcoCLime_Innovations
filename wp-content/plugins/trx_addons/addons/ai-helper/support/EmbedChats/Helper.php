<?php
namespace TrxAddons\AiHelper\EmbedChats;

if ( ! class_exists( 'Helper' ) ) {

	/**
	 * Main class for AI Helper EmbedChat support
	 */
	class Helper {

		/**
		 * Constructor
		 */
		function __construct() {
			add_action( 'wp_footer', array( $this, 'embed_chats' ) );
			add_action( 'admin_footer', array( $this, 'embed_chats' ) );
		}

		/**
		 * Embed chats to the admin footer
		 * 
		 * @hooked 'admin_footer'
		 */
		function embed_chats() {
			$chats = trx_addons_get_option( 'ai_helper_embed_chats' );
			if ( is_array( $chats ) && count( $chats ) > 0 ) {
				foreach( $chats as $chat ) {
					$enable = ! empty( $chat['code'] )
								&& ! empty( $chat['scope'] )
								&& (
									( in_array( $chat['scope'], array( 'admin', 'site' ) ) && is_admin() )
									||
									( in_array( $chat['scope'], array( 'frontend', 'site' ) ) && ! is_admin() )
									);
					if ( $enable && ! empty( $chat['url_contain'] ) ) {
						$enable = false;
						$url = trx_addons_get_current_url();
						$parts = array_map( 'trim', explode( "\n", str_replace( ',', "\n", $chat['url_contain'] ) ) );
						foreach( $parts as $part ) {
							if ( strpos( $url, $part ) !== false ) {
								$enable = true;
								break;
							}
						}
					}
					if ( $enable ) {
						?>
						<!-- EmbedChat <?php echo esc_attr( $chat['title'] ); ?> -->
						<?php
						// Remove comments from the chat code (if exists), but keep the new lines
						$chat['code'] = trx_addons_remove_comments( $chat['code'], false );
						// Replace {images} with the path to the folder 'addons/ai-helper/images'
						$chat['code'] = str_replace(
							array( '{images}' ),
							array( trx_addons_get_folder_url( TRX_ADDONS_PLUGIN_ADDONS . 'ai-helper/images' ) ),
							trim( $chat['code'] )
						);
						// Add assistants prefix to the model name if it's not set
						$chat['code'] = str_replace(
							array( 'model="asst_', "model='asst_" ),
							array( 'model="openai-assistants/asst_', "model='openai-assistants/asst_" ),
							$chat['code']
						);
						// Replace our shortcodes [trx_sc_...] with the shortcode output
						if ( strpos( $chat['code'], '[trx_sc_' ) !== false ) {
							// Force to enqueue styles for the shortcodes
							add_filter( 'trx_addons_filter_force_enqueue_styles', '__return_true' );
							// Do shortcodes
							$chat['code'] = do_shortcode( $chat['code'] );
							// Remove the filter
							remove_filter( 'trx_addons_filter_force_enqueue_styles', '__return_true' );
						}
						// Embed the chat code
						trx_addons_show_layout( $chat['code'] );
						?>
						<!-- /EmbedChat <?php echo esc_attr( $chat['title'] ); ?> -->
						<?php
					}
				}
			}
		}
	}
}
