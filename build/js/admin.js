(function ( $, window, undefined ) {

    /**
     * Icon upload,
     */

    var mediaUploader;

    $( '#share-settings, #follow-settings' ).on( 'click', 'span.dashicons-format-gallery', function ( e ) {
        var $input = $( this ).prev(),
            mediaUploader = wp.media( {
                title: 'Choose Image',
                button: {
                    text: 'Choose Image'
                }, multiple: false
            } ).on( 'select', function () {
                var attachment = mediaUploader.state().get( 'selection' ).first().toJSON();
                $input.val( attachment.url );
            } ).open();
    } );

    /**
     * Share.
     */

    $( 'div.nav-tab-wrapper, #share ul.tabs' ).each( function () {
        var $this = $( this ),
            $tabs = $( 'a', $this ).on( 'click', function ( e ) {
                e.preventDefault();

                // deselect
                $tabs.removeClass( 'nav-tab-active' ).each( function () {
                    // hide all sections
                    $( $( this ).attr( 'href' ) ).hide();
                } );

                // activate clicked section
                $( $( this ).blur().addClass( 'nav-tab-active' ).attr( 'href' ) ).show();
            } );

        // activate first tab
        $this.find( 'a' ).first().trigger( 'click' );

        if ( !$this.hasClass( 'tabs' ) ) return;

        // sortable tabs
        $this.sortable( {
            axis: 'y',
            containment: 'parent',
            stop: function ( event ) {
                // reorder panels
                $.each( $( event.target ).find( 'a' ).toArray().reverse(), function () {
                    $this.next().prepend( $this.next().find( $( this ).attr( 'href' ) ) );
                } );
            }
        } );

        /**
         * Patterns selection.
         */

        $( 'tr.patterns b', $this.next() ).on( 'click', function () {
            var range = document.createRange();
            range.selectNodeContents( this );

            window.getSelection().addRange( range );
        } );
    } );

    /**
     * Follow.
     */

    var $followList = $( 'tbody', '#follow-list' ).on( 'click', 'span.dashicons-no-alt', function () {
        $( this ).closest( 'tr' ).remove();

        renameListOrder();
    } ).sortable( {
        axis: 'y',
        containment: 'parent',
        tolerance: 'pointer',
        'start': function ( event, ui ) {
            ui.placeholder.html( '<tr><td colspan="2"><input type="text" /></td></tr>' )
        },
        stop: renameListOrder
    } );

    $( '#add-follow-network', '#follow-settings' ).on( 'click', function ( e ) {
        e.preventDefault();

        // add network entry
        $followList.append( $( $( 'tr:first-child', $followList ).clone() ).show() );

        renameListOrder();
    } );

    // rename order
    function renameListOrder() {
        $( 'tr', $followList ).each( function ( nb ) {
            if ( !nb-- ) return; // ignore first (template)

            $( ':input', this ).each( function () {
                $( this ).attr( 'name', $( this ).attr( 'name' ).replace( /^share\[follow\]\[\d*\]\[(.*)\]$/, 'share[follow][' + nb + '][$1]' ) );
            } );
        } );
    }

})( jQuery, this, this.document );
