<?php
/**
 * Plugin Name: Amazin' Product Box
 * Plugin URI: http://majoh.dev
 * Description: Customizable product box for Amazon products with an affiliate link
 * Version: 1.0
 * Author: Mandi Grant
 * Author URI: http://majoh.dev
 */


if ( is_admin() ){ // admin actions
  add_action( 'admin_menu', 'amazin_plugin_menu' );
} else {
  // non-admin enqueues, actions, and filters
}

function amazin_plugin_menu() {
    add_menu_page( 'Amazin\' Product Box Management', 'Amazin\' Product Box', 'manage_options', 'amazin-product-box', 'amazin_display_management_page' );
}

/** Step 3. */
function amazin_display_management_page() {
    if ( !current_user_can( 'manage_options' ) )  {
        wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
    }
    echo '<div class="wrap">';
    echo '<h2>Amazin\' Product Box</h2>';
    echo '<p>Create, edit, delete and manage your product boxes here.</p>';
    echo '</div>';
}
?>
