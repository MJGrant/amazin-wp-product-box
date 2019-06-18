<?php

/**
 * Handle the form submissions
 *
 * @package Package
 * @subpackage Sub Package
 */
class Form_Handler {

    /**
     * Hook 'em all
     */
    public function __construct() {
        add_action( 'admin_init', array( $this, 'handle_form' ) );
    }

    /**
     * Handle the product box new and edit form
     *
     * @return void
     */
    public function handle_form() {
        if ( ! isset( $_POST['submit_product_box'] ) ) {
            return;
        }

        if ( ! wp_verify_nonce( $_POST['_wpnonce'], '' ) ) {
            die( __( 'Are you cheating?', 'apb' ) );
        }

        if ( ! current_user_can( 'read' ) ) {
            wp_die( __( 'Permission Denied!', 'apb' ) );
        }

        $errors   = array();
        $page_url = admin_url( 'admin.php?page=amazinProductBox' );
        $field_id = isset( $_POST['field_id'] ) ? intval( $_POST['field_id'] ) : 0;

        $field_productName = isset( $_POST['Product-Name'] ) ? sanitize_text_field( $_POST['Product-Name'] ) : '';
        $field_tagline = isset( $_POST['Tagline'] ) ? sanitize_text_field( $_POST['Tagline'] ) : '';
        $field_description = isset( $_POST['Description'] ) ? wp_kses_post( $_POST['Description'] ) : '';
        $field_url = isset( $_POST['URL'] ) ? sanitize_text_field( $_POST['URL'] ) : '';
        $field_buttonText = isset( $_POST['Button-Text'] ) ? sanitize_text_field( $_POST['Button-Text'] ) : '';

        // some basic validation
        if ( ! $field_productName ) {
            $errors[] = __( 'Error: Product Name is required', 'apb' );
        }

        if ( ! $field_tagline ) {
            $errors[] = __( 'Error: Tagline is required', 'apb' );
        }

        if ( ! $field_description ) {
            $errors[] = __( 'Error: Description is required', 'apb' );
        }

        if ( ! $field_url ) {
            $errors[] = __( 'Error: URL is required', 'apb' );
        }

        if ( ! $field_buttonText ) {
            $errors[] = __( 'Error: Button Text is required', 'apb' );
        }

        // bail out if error found
        if ( $errors ) {
            $first_error = reset( $errors );
            $redirect_to = add_query_arg( array( 'error' => $first_error ), $page_url );
            wp_safe_redirect( $redirect_to );
            exit;
        }

        $id = $field_id;

        $content = array(
            'productName' => $field_productName,
            'productTagline' => $field_tagline,
            'productDescription' => $field_description,
            'productUrl' => $field_url,
            'productButtonText' => $field_buttonText
        );

        $product_box = array(
            'ID'            => $id,
            'post_title'    => $field_productName,
            'post_type'     => 'amazin_product_box',
            'post_content'  => wp_json_encode($content), //broke when switched this from 'none' to the content array
            'post_status'   => 'publish',
            'post_author'   => 1,
            'post_category' => array( 8,39 )
        );

        // New or edit?
        if ( ! $field_id ) {

            $insert_id = wp_insert_post( $product_box ); //apb_insert_product_box( $fields );

        } else {

            $fields['id'] = $field_id;

            $insert_id = wp_update_post ( $product_box ); //apb_insert_product_box( $fields );
        }

        if ( is_wp_error( $insert_id ) ) {
            $redirect_to = add_query_arg( array( 'message' => 'error' ), $page_url );
        } else {
            $redirect_to = add_query_arg( array( 'message' => 'success' ), $page_url );
        }

        wp_safe_redirect( $redirect_to );
        exit;
    }
}

new Form_Handler();
