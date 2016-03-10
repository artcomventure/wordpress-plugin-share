( function( $, window, document, undefined ) {

    $( '#share__socials').sortable( {
        axis: 'y',
        containment: 'parent',
        items: 'tr'
    } );

} )( jQuery, this, this.document );
