(function() {

    var sharer;

    // to make sure we always trigger a share links click (also on ajax loaded ones)
    // we listen to the document click and work our way up the levels
    document.addEventListener( 'click', function( e ) {
        // clicked element
        var $link = e.target;

        // try to get the share link
        while ( $link.tagName != 'A' || $link.className.split( ' ' ).indexOf( 'share__link' ) < 0 ) {
            if ( $link instanceof HTMLBodyElement || !$link.parentElement ) return;
            $link = $link.parentElement;
        }

        // now we are pretty sure the user clicked on a share link

        // no sharer popup for ...
        if ( $link.href.match( new RegExp( '^(mailto|javascript|whatsapp|sms):' ) ) ) return;

        e.preventDefault();

        // only ONE sharer popup
        if (typeof sharer != 'undefined') {
            sharer.close();
            delete sharer;
        }

        // eventually open sharer popup
        sharer = window.open( $link.href , 'sharer', 'height=440, width=640, toolbar=no, menubar=no, scrollbars=no, resizable=no, location=no, directories=no, status=no');
    }, false );

})();
