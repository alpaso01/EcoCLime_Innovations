<?php
function rselements_shortcode_register_post_type() {
    $labels = array(
        'name'               => esc_html__( 'RS Global Shortcodes', 'shortcode-elementor'),
        'singular_name'      => esc_html__( 'Elementor Shortcodes', 'shortcode-elementor'),
        'add_new'            => esc_html__( 'Add New Shortcode', 'shortcode-elementor'),
        'add_new_item'       => esc_html__( 'Add New Shortcode', 'shortcode-elementor'),
        'edit_item'          => esc_html__( 'Edit Element', 'shortcode-elementor'),
        'new_item'           => esc_html__( 'New Shortcode', 'shortcode-elementor'),
        'all_items'          => esc_html__( 'All Shortcodes', 'shortcode-elementor'),
        'view_item'          => esc_html__( 'View Elements', 'shortcode-elementor'),    
        'not_found'          => esc_html__( 'No Elements found', 'shortcode-elementor'),
        'not_found_in_trash' => esc_html__( 'No Elements found in Trash', 'shortcode-elementor'),
        'parent_item_colon'  => esc_html__( 'Parent Team:', 'shortcode-elementor'),
        'menu_name'          => esc_html__( 'Shortcode Elementor', 'shortcode-elementor'),
    );  
    
    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_in_menu'       => true,
        'show_in_admin_bar'  => true,
        'can_export'         => true,
        'has_archive'        => false,
        'hierarchical'       => false,
        'menu_position'      => 20,     
        'menu_icon'          =>  plugins_url( 'img/icon.png', __FILE__ ),
        'supports'           => array( 'title', 'thumbnail', 'editor', 'page-attributes' )
    );
    register_post_type( 'rs_elements', $args );
}
add_action( 'init', 'rselements_shortcode_register_post_type' );

function rselements_add_meta_box(){
    add_meta_box('rs-shortcode-box','Elements Shortcode','rselements_shortcode_box','rs_elements','side','high');
}
add_action("add_meta_boxes", "rselements_add_meta_box");

function rselements_shortcode_box($post) {
    ?>
    <h4><?php echo esc_html__('Shortcode', 'shortcode-elementor'); ?></h4>
    <input type="text" class="widefat" 
        value="<?php echo esc_attr('[SHORTCODE_ELEMENTOR id="' . $post->ID . '"]'); ?>" 
        readonly="">

    <h4><?php echo esc_html__('PHP Code', 'shortcode-elementor'); ?></h4>
    <input type="text" class="widefat" 
        value="<?php echo esc_attr('&lt;?php echo do_shortcode(\'[SHORTCODE_ELEMENTOR id=&quot;' . $post->ID . '&quot;]\'); ?&gt;'); ?>" 
        readonly="">
    <?php
}

// Secure shortcode rendering
function rselements_render_shortcode( $atts ) {
    $atts = shortcode_atts( array(
        'id' => '',
    ), $atts, 'SHORTCODE_ELEMENTOR' );

    $post_id = intval( $atts['id'] );
    if ( ! $post_id ) {
        return esc_html__( 'Invalid post ID.', 'shortcode-elementor' );
    }

    // Fetch the post
    $post = get_post( $post_id );

    // Check post status
    if ( ! $post || 'publish' !== $post->post_status ) {
        return esc_html__( 'This content is not accessible.', 'shortcode-elementor' );
    }

    // Only allow the rs_elements post type
    if ( 'rs_elements' !== $post->post_type ) {
        return esc_html__( 'Invalid content type.', 'shortcode-elementor' );
    }
    // Add a permission check to ensure the current user can view this post
    if ( ! current_user_can( 'read_post', $post_id ) ) {
        return esc_html__( 'You do not have permission to access this content.', 'shortcode-elementor' );
    }

    // Return the content
    return apply_filters( 'the_content', wp_kses_post( $post->post_content ) );
}
add_shortcode( 'SHORTCODE_ELEMENTOR', 'rselements_render_shortcode' );
