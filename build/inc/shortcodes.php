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

	return get_share_links( $args['url'], $args['cache'] );
}

/**
 * Follow shortcode for inline use.
 */
add_shortcode( 'follow', 'follow_shortcode' );
function follow_shortcode() {
	return get_follow_links();
}
