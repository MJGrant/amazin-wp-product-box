jQuery ( document ).ready ( function ( $ ) {
    'use strict';
    $ ( '#admin-table').on( 'click', '.delete-button', function(e) {
        var id = e.target.id;
        var tableRow = "#row-"+id;
        var nonce = e.target.nonce;

        $.ajax({
            type: 'post',
            url: MyAjax.ajaxurl,
            data: {
                action: 'amazin_delete_post',
                nonce: nonce,
                id: id
            },
            success: function ( result ) {
                if ( result === 'success' ) {
                    $ ( tableRow ).fadeOut( function() {
                        $ ( tableRow ).remove();
                    });
                }
            }
        });
        return false;
    });

    $ ( '#admin-table').on( 'click', '.edit-button', function(e) {
        var id = e.target.id;

        $.ajax({
            type: 'get',
            url: MyAjax.ajaxurl,
            data: {
                action: 'amazin_get_existing_post',
                id: id
            },
            success: function ( response ) {
                var id = response.productBoxID;
                var data = JSON.parse(response.productBoxData);

                $ ("#current-form-behavior-title").text("Editing Product Box ID " + id);

                $ ("#product-id").val(id);
                $ ("#product-name").val(data.productName);
                $ ("#product-tagline").val(data.productTagline);
                $ ("#product-description").val(data.productDescription);
                $ ("#product-url").val(data.productUrl);
                $ ("#product-button-text").val(data.productButtonText);

                //change "Submit" button text to "Update"
                $ ("#form-submit").val("Update");
                $ ("#form-cancel").val("Cancel Edit");

            }
        });
        return false;
    } );

    $ ( '#product-box-form').on( 'click', '#form-cancel', function(e) {

        // Reset title, clear form fields
        $ ("#current-form-behavior-title").text("Create a new Product Box");
        $ ("#product-box-form")[0].reset();

        // Put buttons back to "Submit" and "Clear" (for a new one)
        $ ("#form-submit").val("Submit");
        $ ("#form-cancel").val("Clear Form");

        //Clear ID so the form acts like "Submit new one" again
        $ ("#product-id").val(undefined);

        return false;
    } );

} );
