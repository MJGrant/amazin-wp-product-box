<?php

/**
 * Get all product box
 *
 * @param $args array
 *
 * @return array
 */
function apb_get_all_product_boxes( $args = array() ) {
    global $wpdb;

    $defaults = array(
        'number'     => 20,
        'offset'     => 0,
        'orderby'    => 'id',
        'order'      => 'ASC',
    );

    $args      = wp_parse_args( $args, $defaults );
    $cache_key = 'productbox-all';
    $items     = wp_cache_get( $cache_key, 'apb' );

    if ( false === $items ) {
        $items = $wpdb->get_results( 'SELECT * FROM ' . $wpdb->prefix . 'posts WHERE `post_type`="amazin_product_box" ORDER BY ' . $args['orderby'] .' ' . $args['order'] .' LIMIT ' . $args['offset'] . ', ' . $args['number'] );

        wp_cache_set( $cache_key, $items, 'apb' );
    }

    return $items;
}

/**
 * Fetch all product box from database
 *
 * @return array
 */
function apb_get_product_box_count() {
    global $wpdb;

    return (int) $wpdb->get_var( 'SELECT COUNT(*) FROM ' . $wpdb->prefix . 'posts WHERE `post_type`="amazin_product_box"' );
}

/**
 * Fetch a single product box from database
 *
 * @param int   $id
 *
 * @return array
 */
function apb_get_product_box( $id = 0 ) {
    global $wpdb;

    return $wpdb->get_row( $wpdb->prepare( 'SELECT * FROM ' . $wpdb->prefix . 'posts WHERE id = %d', $id ) );
}
