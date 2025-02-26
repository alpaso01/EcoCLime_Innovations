<?php

/*plugin name: Shortcodes for Elementor
 * Plugin URI:  https://wordpress.org/plugins/shortcode-elementor/
 * Description: Insert your elementor pages and sections templates anywhere using shortcode
 * Version:     1.0.6
 * Author:      RSTheme
 * Author URI:  https://rstheme.com/
 * Text Domain: shortcode-elementor
 * Author URI: 	http://rstheme.com
 * Plugin URI: 	https://wordpress.org/plugins/shortcode-elementor/
 * License: 	GPL v2 or later
 * License URI:	http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path: /languages
 * Requires PHP: 7.0.0
 * Requires at least: 5.5
 */

define( 'RS_Elements__FILE__', __FILE__ );
define( 'RS_Elements_PLUGIN_BASE', plugin_basename( RS_Elements__FILE__ ) );
define( 'RS_Elements_URL', plugins_url( '/', RS_Elements__FILE__ ) );
define( 'RS_Elements_PATH', plugin_dir_path( RS_Elements__FILE__ ) );

require_once( RS_Elements_PATH . 'includes/post-type.php' );
require_once( RS_Elements_PATH . 'includes/settings.php' );
require_once( RS_Elements_PATH . 'includes/plugin-settings.php' );

// Get Ready Plugin Translation
function rselements_load_textdomain_lite() {
    load_plugin_textdomain('shortcode-elementor', false, dirname(plugin_basename(__FILE__)) . '/languages/');
}
add_action('plugins_loaded', 'rselements_load_textdomain_lite');

class RSElements_Elementor_Shortcode{

	function __construct(){
		add_action( 'manage_rs_elements_posts_custom_column' , array( $this, 'rselements_rs_global_templates_columns' ), 10, 2);
		add_filter( 'manage_rs_elements_posts_columns', array($this,'rselements_custom_edit_global_templates_posts_columns' ));
	}

	function rselements_custom_edit_global_templates_posts_columns($columns) {
		
		$columns['rs_shortcode_column'] = __( 'Shortcode', 'shortcode-elementor' );
		return $columns;
	}

    function rselements_rs_global_templates_columns( $column, $post_id ) {
        switch ( $column ) {
            case 'rs_shortcode_column' :
                // Escape the post ID and output the shortcode
                echo '<input type="text" class="widefat" value="' . esc_attr( '[SHORTCODE_ELEMENTOR id="' . $post_id . '"]' ) . '" readonly="">';
                break;
        }
    }
        
}
new RSElements_Elementor_Shortcode();

