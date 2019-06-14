<?php
/**
 * Plugin Name: Amazin' Product Box
 * Plugin URI: http://majoh.dev
 * Description: Customizable product box for Amazon products with an affiliate link
 * Version: 1.0
 * Author: Mandi Grant
 * Author URI: http://majoh.dev
 */

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

if ( is_admin() ){ // admin actions
    add_action( 'admin_menu', 'amazin_plugin_menu' );
    add_action( 'init', 'create_post_type' );
    add_action( 'wp_ajax_amazin_delete_post', 'amazin_delete_post' );
    add_action( 'wp_ajax_amazin_get_existing_post', 'amazin_get_existing_post' );

    $jsurl = plugin_dir_url(__FILE__) . 'scripts.js';
    wp_enqueue_script('scripts', $jsurl, array('jquery'), 1.61);

    wp_localize_script('scripts', 'MyAjax', array('ajaxurl' => admin_url('admin-ajax.php') ) );
} else {
  // non-admin enqueues, actions, and filters
}

function amazin_product_box_render_in_post($productBox) {
    ob_start();
    $productBoxTitle = $productBox->post_title;
    $stripped = stripslashes($productBox->post_content);
    $content = json_decode($stripped, true);
    ?>
        <div class="product-box" id="product-box-id" style="border:1px solid grey;">
            <h3><?php echo $productBoxTitle ?></h3>
            <p><?php echo $content['productTagline'] ?></p>
            <p><?php echo $content['productDescription'] ?></p>
            <a href="<?php echo $content['productLink'] ?>"><?php echo $content['productButtonText'] ?></a>
        </div>
    <?php
    return ob_get_clean();
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
add_shortcode( 'amazin-product-box', 'amazin_product_box_shortcode' );

function amazin_plugin_menu() {
    add_menu_page( 'Amazin\' Product Box Management', 'Amazin\' Product Box', 'manage_options', 'amazin-product-box', 'amazin_display_management_page' );
}

function amazin_display_management_page() {
    if ( !current_user_can( 'manage_options' ) )  {
        wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
    }
    amazin_render_form();
    amazin_render_table();
}

function amazin_render_form() {
    ?>
    <h2>Amazin' Product Box</h2>
    <p>Create, edit, delete and manage your product boxes here.</p>

    <h3 id="current-form-behavior-title">Create a new Product Box</h3>
    <div class="form-wrap">
        <form id="product-box-form" action="<?php echo esc_url( post_new_product_box() ); ?>" method="post">

            <?php wp_nonce_field( 'nonce_action', 'amazin_nonce_field', true, true ); ?>
            <!-- product ID, hidden, distinguishes new one from editing -->
            <div class="form-field">
                <input type="hidden" id="product-id" name="amazin-product-id"/>
            </div>

            <!-- product name -->
            <div class="form-field">
                <label for="product-name">Product Name</label>
                <input type="text" id="product-name" name="amazin-product-name" placeholder="Enter the product's name"/>
            </div>

            <!-- product tagline -->
            <div class="form-field">
                <label for="product-tagline">Product Tagline</label>
                <input type="text" id="product-tagline" name="amazin-product-tagline" placeholder="Write a few words summarizing this product"/>
            </div>

            <!-- product description -->
            <div class="form-field">
                <label for="product-description">Product Description</label>
                <input type="text" id="product-description" name="amazin-product-description" placeholder="Write about 100 characters explaining why this product is great."/>
            </div>

            <!-- product URL -->
            <div class="form-field">
                <label for="product-url">Affiliate link</label>
                <input type="text" id="product-url" name="amazin-product-url" placeholder="http://retailer.com/affiliate-link-here"/>
            </div>

            <!-- Button text -->
            <div class="form-field">
                <label for="product-button-text">Button text</label>
                <input type="text" id="product-button-text" name="amazin-product-button-text" placeholder="See XYZ product on Retailer.com"/>
            </div>

            <input type="submit" name="submit" value="Submit" id="form-submit"/>
            <input type="button" name="cancel" value="Cancel" id="form-cancel"/>
        </form>
    </div>
    <?php
    return;
}

function amazin_render_table() {
    $args = array(
        'post_type' =>  'amazin_product_box',
        'numberposts' => -1
        );
    $productBoxes = get_posts($args);
    ?>
    <table id="admin-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Shortcode</th>
                <th>Product Name</th>
                <th>Author</th>
                <th>Last Modified</th>
                <th>Manage</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($productBoxes):
                foreach ($productBoxes as $productBox):
                    $id=$productBox->ID;
                ?>
                <tr id="<?php echo "row-" . $id; ?>">
                    <!-- for loop through saved boxes -->
                    <td><?php echo $id ?></td>
                    <td>[shortcode here]</td>
                    <td><?php echo get_the_title($id); ?></td>
                    <td><?php echo get_the_author_meta( 'display_name', $productBox->post_author ); ?></td>
                    <td><?php echo get_the_modified_time('M d, Y h:i:s A', $id ); ?></td>
                    <td><input type="button" id="<?php echo $id; ?>" class="edit-button" value="Edit"/> <input type="button" id="<?php echo $id; ?>" class="delete-button" nonce="<?php echo wp_create_nonce('amazin_delete_post_nonce') ?>" value="Delete"/></td>
                </tr>
            <?php endforeach; wp_reset_postdata(); ?>
        <?php endif; ?>
        </tbody>
    </table>
    <?php
    return;
}

function create_post_type() {
    register_post_type('amazin_product_box',
        //custom post type options
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
}

function post_new_product_box() {
    if ( isset( $_POST['submit'] ) ) {
        //check nonce
        if (! isset ( $_POST['amazin_nonce_field'] ) || ! wp_verify_nonce( $_POST['amazin_nonce_field'], 'nonce_action' ) ) {
            print 'Sorry, your nonce did not verify.';
            exit;
        } else {
            //Distinguish between adding a new Product Box and editing an existing Product Box
            $id = $_POST['amazin-product-id'];

            $content = array(
                "productName" => $_POST['amazin-product-name'],
                "productTagline" => $_POST['amazin-product-tagline'],
                "productDescription" => $_POST['amazin-product-description'],
                "productUrl" => $_POST['amazin-product-url'],
                "productButtonText" => $_POST['amazin-product-button-text']
            );

            $product_box = array(
                'ID'            => $id,
                'post_title'    => $_REQUEST['amazin-product-name'],
                'post_type'     => 'amazin_product_box',
                'post_content'  => wp_json_encode($content), //broke when switched this from 'none' to the content array
                'post_status'   => 'publish',
                'post_author'   => 1,
                'post_category' => array( 8,39 )
            );

            if ($id) {
                // If $id exists, we're editing an existing one: update post
                wp_update_post ( $product_box );
            } else {
                // It's a new one: insert the post into the database.
                wp_insert_post( $product_box );
            }
        }
    }
}

function amazin_delete_post( ) {
    $permission = wp_verify_nonce( $_POST['nonce'], 'amazin_delete_post_nonce' );
    if ( $permission == false ) {
        echo 'error';
    } else {
        wp_delete_post($_REQUEST['id']);
        echo 'success';
    }
    die();
}

function amazin_get_existing_post( ) {
    $post = get_post($_REQUEST['id']);
    if ($post) {
        $postDataToBePassed = array(
            'productBoxProductName' => $post->post_title,
            'productBoxID' => $post->ID,
            'productBoxData' => $post->post_content
        );
        wp_send_json($postDataToBePassed);
    } else {
        echo 'error retrieving post';
    }

    die();
}

?>
