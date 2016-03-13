( function( window, document, undefined ) {

    document.addEventListener( "DOMContentLoaded", function() {

        var shareLinks = document.getElementsByClassName( 'share__link' ),
            sharer;

        if ( !shareLinks.length ) return;

        for ( var i = 0; i < shareLinks.length; i++ ) {
            if ( shareLinks[i].classList.contains( 'share__comments' ) ) continue;

            shareLinks[i].addEventListener( 'click', function( e ) {
                if ( this.href.match( new RegExp( '^(mailto|javascript|whatsapp|sms):' ) ) ) return;

                e.preventDefault();

                // Only allow ONE sharer popup
                if (typeof sharer != 'undefined') {
                    sharer.close();
                    delete sharer;
                }

                sharer = window.open( this.href , 'sharer', 'height=440, width=640, toolbar=no, menubar=no, scrollbars=no, resizable=no, location=no, directories=no, status=no');
            } );
        }

    }, false );

} )( this, this.document );