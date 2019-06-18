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

    new Amazin_Product_Box_Admin_Menu();
})

?>
