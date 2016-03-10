<?php

/**
 * Share shortcode for inline use.
 */
add_shortcode( 'share', 'share_shortcode' );
function share_shortcode( $args ) {
	$args = shortcode_atts( array(
		'url' => '',
		'cache' => '',
	), $args );

	// string to boolean
	$args['cache'] = ( $args['cache'] != 'true' ? FALSE : TRUE );

	return get_share_links( $args['url'], $args['cache'] );
}
