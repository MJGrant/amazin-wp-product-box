jQuery ( document ).ready ( function ( $ ) {
    'use strict';
    $ ( '#admin-table').on( 'click', '.delete-button', function(e) {
        console.log("Gonna delete a product box with ID:", e.target.id);
    } );

    $ ( '#admin-table').on( 'click', '.edit-button', function(e) {
        console.log("Gonna edit a product box with ID:", e.target.id);
    } );

} );
