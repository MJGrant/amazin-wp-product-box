<?php
// if uninstall.php is not called by WordPress, die
if (!defined('WP_UNINSTALL_PLUGIN')) {
    die;
}

$option_name = 'amazin_product_box_option_headline';

delete_option($option_name);

// for site options in Multisite
delete_site_option($option_name);

// drop a custom database table
global $wpdb;
$wpdb->query("DELETE FROM {$wpdb->prefix}posts WHERE post_type='amazin_product_box'");
?>
