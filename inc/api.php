<?php

/**
 * Alter share links.
 *
 * @param array $share
 *
 * @return array
 */
add_filter( 'share_link', 'hook__share_link' );
function hook__share_link( $share = array() ) {
	return $share;
}

/**
 * Alter share count curl.
 *
 * @param string $network
 * @param array $args
 *
 * @return array
 */
add_filter( 'share_count', 'hook__share_count', 10, 2 );
function hook__share_count( $network, $args ) {
	return $args;
}

/**
 * Alter meta data.
 *
 * @param array $meta
 *
 * @return array
 */
add_filter( 'share_meta', 'hook__share_meta' );
function hook__share_meta( $meta ) {
	return $meta;
}
