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
        console.log("Gonna edit a product box with ID:", e.target.id);
    } );

} );
