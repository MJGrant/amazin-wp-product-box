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

            <!-- product box name -->
            <div class="form-field">
                <label for="product-box-name">Product Box name</label>
                <input type="text" id="product-box-name" placeholder="Give this product box a useful name"/>
            </div>

            <!-- product name -->
            <div class="form-field">
                <label for="product-name">Product Name</label>
                <input type="text" id="product-name" placeholder="Enter the product name here"/>
            </div>

            <!-- product tagline -->
            <div class="form-field">
                <label for="product-tagline">Product Tagline</label>
                <input type="text" id="product-tagline" placeholder="Write a few words summarizing this product"/>
            </div>

            <!-- product description -->
            <div class="form-field">
                <label for="product-description">Product Description</label>
                <input type="text" id="product-description" placeholder="Write about 100 characters explaining why this product is great."/>
            </div>

            <!-- product URL -->
            <div class="form-field">
                <label for="product-url">Affiliate link</label>
                <input type="text" id="product-url" placeholder="http://amazon.com/affiliate-link-here"/>
            </div>

            <!-- Button text -->
            <div class="form-field">
                <label for="product-button-text">Button text</label>
                <input type="text" id="product-button-text" placeholder="See XYZ product on Amazon.com"/>
            </div>

            <input type="submit"/>
        </form>

    </div>
    <?php
    return;
}

function amazin_render_table() {
    ?>
    <table>
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
            <tr>
                <!-- for loop through saved boxes -->
                <td>[shortcode here]</td>
                <td>Sample Name</td>
                <td>Sample Author</td>
                <td>Sample Date</td>
                <td><button>Edit</button> <button>Delete</button></td>
            </tr>
        </tbody>
    </table>
    <?php
    return;
}

function post_new_product_box() {
    $my_post = array(
        'post_title'    => 'Test product box',
        'post_content'  => 'Test description',
        'post_status'   => 'publish',
        'post_author'   => 1,
        'post_category' => array( 8,39 )
    );

    // Insert the post into the database.
    wp_insert_post( $my_post );
}
?>
