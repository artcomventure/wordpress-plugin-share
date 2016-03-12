( function( $, window, undefined ) {

    var $form = $( '#share-settings-form'),
        $tabsWrap = $( '#share-settings' ),
        $tabs = $( 'ul.tabs > li', $tabsWrap ),
        $currentTab,
        $panelsWrap = $( 'div.panels', $tabsWrap),
        $panels = $( '> *', $panelsWrap );

    $form.data( 'action', $form.attr( 'action' ) );

    // tabs action
    $tabs.on( 'click', 'a', function( e ) {
        var $link = $( this ).blur(),
            sHash = $link.attr( 'href' ),
            $panel;

        e.preventDefault();

        // don't do anything if the click is for the tab already showing
        if ( $link.is( '.active a' ) )
            return false;

        // set hash
        window.location.hash = sHash;
        $form.attr( 'action', $form.data( 'action' ) + '#' + sHash );

        // links
        $( 'a.active', $tabs ).removeClass( 'active' );
        $link.addClass( 'active' );

        $panel = $( sHash );

        // panels
        $panels.not( $panel ).removeClass( 'active' ).hide();
        $panel.addClass( 'active' ).show();
    } );

    // current tab
    if ( window.location.hash ) $currentTab = $tabs.find( 'a[href="' + window.location.hash + '"]' );
    if ( !$currentTab || !$currentTab.length ) $currentTab = $tabs.first().find( 'a' );

    // activate current tab
    $currentTab.trigger( 'click' );

    // sortable tabs
    $tabsWrap.find( 'ul.tabs' ).sortable( {
        axis: 'y',
        containment: 'parent',
        stop: function( event ) {
            // reorder panels
            $.each( $( event.target).find( 'a' ).toArray().reverse(), function() {
                $panelsWrap.prepend( $panelsWrap.find( $( this).attr( 'href' ) ) );
            } );
        }
    } );

    /**
     * Patterns selection.
     */

    $tabsWrap.find( 'tr.patterns b').on( 'click', function() {
        var range = document.createRange();
        range.selectNodeContents( this );

        window.getSelection().addRange( range );
    } );

} )( jQuery, this, this.document );
