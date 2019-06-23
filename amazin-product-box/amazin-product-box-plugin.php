<?php
/**
 * Plugin Name: Amazin' Product Box
 * Plugin URI: http://majoh.dev
 * Description: Customizable product box for Amazon products with an affiliate link
 * Version: 1.0
 * Author: Mandi Grant
 * Author URI: http://majoh.dev
 */

add_action( 'init', function() {
    include dirname( __FILE__ ) . '/includes/class-amazin-product-box-admin-menu.php';
    include dirname( __FILE__ ) . '/includes/class-amazin-product-box-list-table.php';
    include dirname( __FILE__ ) . '/includes/class-form-handler.php';
    include dirname( __FILE__ ) . '/includes/amazin-product-box-functions.php';

    // WordPress image upload library
    wp_enqueue_media();
    $jsurl = plugin_dir_url(__FILE__) . 'admin.js';
    wp_enqueue_script('admin', $jsurl, array( 'jquery' ), 1.1, true);

    $cssurl = plugin_dir_url(__FILE__) . 'styles.css';
    wp_enqueue_style( 'amazin-stylesheet', $cssurl, array(), 1.30 );

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
    register_setting( 'amazin_product_box_options_group', 'amazin_product_box_option_headline', 'amazin_product_box_callback' );

    new Amazin_Product_Box_Admin_Menu();
});

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
    $stripped = stripslashes($productBox->post_content);
    $content = json_decode($stripped, true);
    ?>
        <div class="amazin-product-box" id="<?php echo 'amazin-product-box-id-'.$id; ?>">
            <p class="amazin-product-box-recommend-text"><?php echo get_option('amazin_product_box_option_headline'); ?></p>
            <h3 class="amazin-product-box-product-name"><?php echo $productBoxTitle ?></h3>
            <div class="row amazin-product-box-image-row">
                <div class="amazin-product-box-column amazin-product-box-left">
                    <img src="<?php echo wp_get_attachment_url( $content['productImage'] ) ?>"/>
                </div>
                <div class="amazin-product-box-column amazin-product-box-right">
                    <p class="amazin-product-box-tagline"><?php echo $content['productTagline'] ?></p>
                    <p class="amazin-product-box-description" ><?php echo $content['productDescription'] ?></p>
                </div>
            </div>
            <div class="amazin-product-box-button-wrap">
                <a href="<?php echo $content['productLink'] ?>" class="amazin-product-box-button"><?php echo $content['productButtonText'] ?></a>
            </div>
        </div>
    <?php
    return ob_get_clean();
}

add_shortcode( 'amazin-product-box', 'amazin_product_box_shortcode' );

?>
