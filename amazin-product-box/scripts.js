jQuery ( document ).ready ( function ( $ ) {
    'use strict';
    $ ( '#admin-table').on( 'click', '.delete-button', function(e) {
        console.log("Gonna delete a product box with ID:", e.target.id);
        var id = e.target.id;
        var tableRow = "#row-"+id;

        $.ajax({
            type: 'post',
            url: MyAjax.ajaxurl,
            data: {
                action: 'amazin_delete_post',
                id: id
            },
            success: function ( result ) {
                if ( result === 'success' ) {
                    $ ( tableRow ).fadeOut( function() {
                        tableRow.remove();
                    });
                    console.log("successfully removed post");
                }
            }
        });
        return false;
    });

    $ ( '#admin-table').on( 'click', '.edit-button', function(e) {
        console.log("Gonna edit a product box with ID:", e.target.id);
    } );

} );
