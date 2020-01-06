<?php
/**
 * Plugin Name: Amazin' Product Box
 * Plugin URI: http://majoh.dev
 * Description: Showcase your recommended products in your posts with eye-catching product boxes
 * Version: 1.1
 * Author: Mandi Grant
 * Author URI: http://majoh.dev
 */

defined( 'ABSPATH' ) OR exit;

add_action( 'init', function() {
    include dirname( __FILE__ ) . '/includes/class-amazin-product-box-admin-menu.php';
    include dirname( __FILE__ ) . '/includes/class-amazin-product-box-list-table.php';
    include dirname( __FILE__ ) . '/includes/class-amazin-product-box-form-handler.php';
    include dirname( __FILE__ ) . '/includes/amazin-product-box-functions.php';

    // WordPress image upload library
    wp_enqueue_media();
    $jsurl = plugin_dir_url(__FILE__) . 'admin.js';
    wp_enqueue_script('admin', $jsurl, array( 'jquery' ), 1.2, true);

    $cssurl = plugin_dir_url(__FILE__) . 'styles.css';
    wp_enqueue_style( 'amazin-product-box-stylesheet', $cssurl, array(), 1.36 );

    register_post_type('amazin_product_box',
        array(
            'labels' => array(
                'name' => __( 'Amazin Product Boxes' ),
                'singular_name' => __( ' Amazin Product Box ')
            ),
            'public'            => false,
            'show_ui'           => false,
            'query_var'         => false,
            'rewrite'           => false,
            'capability_type'   => 'amazin_product_box',
            'has_archive'       => true,
            'can_export'        => true,
        )
    );

    add_option( 'amazin_product_box_option_headline', 'We recommend');
    add_option( 'amazin_product_box_option_new_tab', false);
    register_setting( 'amazin_product_box_options_group', 'amazin_product_box_option_headline', 'amazin_product_box_callback' );
    register_setting( 'amazin_product_box_options_group', 'amazin_product_box_option_new_tab', 'amazin_product_box_callback' );

    add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'amazin_product_box_add_plugin_action_links' );

    new Amazin_Product_Box_Admin_Menu();
});

function amazin_product_box_add_plugin_action_links( $links ) {
    $plugin_url = admin_url( 'admin.php?page=amazinProductBox' );
    $links[] = '<a href="' . $plugin_url . '">' . __( 'Manage Product Boxes', 'apb' ) . '</a>';
    return $links;
}

function amazin_product_box_shortcode( $atts ) {
    $a = shortcode_atts( array(
        'id' => 'id'
        ), $atts );

    $productBox = get_post($a['id']);

    if ($productBox) {
        return amazin_product_box_render_in_post($productBox);
    } else {
        return 'Error displaying Amazin Product Box';
    }
}

function amazin_product_box_render_in_post($productBox) {
    ob_start();
    $id = $productBox->ID;
    $productBoxTitle = $productBox->post_title;
    $item = apb_get_product_box( $id );
    $content = json_decode($item->post_content, true);
    $newTab = get_option('amazin_product_box_option_new_tab') ? 'target="_blank"' : '';

    ?>
        <div class="amazin-product-box" id="<?php echo 'amazin-product-box-id-'.$id; ?>">
            <p class="amazin-product-box-recommend-text"><?php echo get_option('amazin_product_box_option_headline'); ?></p>
            <h3 class="amazin-product-box-product-name"><?php echo $productBoxTitle ?></h3>
            <div class="amazin-product-box-image-row">
                <div class="amazin-product-box-column amazin-product-box-left">
                    <img src="<?php echo wp_get_attachment_url( $content['productImage'] ) ?>"/>
                </div>
                <div class="amazin-product-box-column amazin-product-box-right">
                    <p class="amazin-product-box-tagline"><?php echo $content['productTagline'] ?></p>
                    <p class="amazin-product-box-description" ><?php echo $content['productDescription'] ?></p>
                </div>
            </div>
            <div class="amazin-product-box-button-wrap">
                <a href="<?php echo $content['productUrl'] ?>" class="amazin-product-box-button" <?php echo $newTab ?> ><?php echo $content['productButtonText'] ?></a>
            </div>
        </div>
    <?php
    return ob_get_clean();
}

add_shortcode( 'amazin-product-box', 'amazin_product_box_shortcode' );

?>
