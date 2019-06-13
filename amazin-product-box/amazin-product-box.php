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

    $jsurl = plugin_dir_url(__FILE__) . 'scripts.js';
    wp_enqueue_script('scripts', $jsurl, array('jquery'), 1.31);
    wp_localize_script('scripts', 'MyAjax', array('ajaxurl' => admin_url('admin-ajax.php') ) );
} else {
  // non-admin enqueues, actions, and filters
}

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
    <div class="form-wrap">
        <form action="<?php echo esc_url( post_new_product_box() ); ?>" method="post">

            <?php wp_nonce_field( 'nonce_action', 'amazin_nonce_field', true, true ); ?>
            <!-- product box name -->
            <div class="form-field">
                <label for="product-box-name">Product Box name</label>
                <input type="text" id="product-box-name" name="amazin-product-box-name" placeholder="Give this product box a useful name"/>
            </div>

            <!-- product name -->
            <div class="form-field">
                <label for="product-name">Product Name</label>
                <input type="text" id="product-name" name="amazin-product-name" placeholder="Enter the product name here"/>
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
                <input type="text" id="product-url" name="amazin-product-url" placeholder="http://amazon.com/affiliate-link-here"/>
            </div>

            <!-- Button text -->
            <div class="form-field">
                <label for="product-button-text">Button text</label>
                <input type="text" id="product-button-text" name="amazin-product-button-text" placeholder="See XYZ product on Amazon.com"/>
            </div>

            <input type="submit" name="submit"/>
        </form>
    </div>
    <?php
    return;
}

function amazin_render_table() {
    $args = array(
        'post_type' =>  'amazin_product_box'
        );
    $productBoxes = get_posts($args);
    ?>
    <table id="admin-table">
        <thead>
            <tr>
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
            //process form data
            // retrieve the form data by using the element's name attributes
            // value as key $firstname = $_GET['firstname']; $lastname = $_GET['lastname'];
            // display the results echo '<h3>Form GET Method</h3>'; echo 'Your name is ' . $lastname . ' ' . $firstname; exit;
            $content = array(
                "amazin-product-name" => $_POST['amazin-product-name'],
                "amazin-product-tagline" => $_POST['amazin-product-tagline'],
                "amazin-product-description" => $_POST['amazin-product-description'],
                "amazin-product-url" => $_POST['amazin-product-url'],
                "amazin-product-button-text" => $_POST['amazin-product-button-text']
            );

            $product_box = array(
                'post_title'    => $_REQUEST['amazin-product-box-name'],
                'post_type'     => 'amazin_product_box',
                'post_content'  => wp_json_encode($content), //broke when switched this from 'none' to the content array
                'post_status'   => 'publish',
                'post_author'   => 1,
                'post_category' => array( 8,39 )
            );

            // Insert the post into the database.
            wp_insert_post( $product_box );
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
?>
