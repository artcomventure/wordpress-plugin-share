<?php

/**
 * Alter share link data for specific network.
 * ('NETWORK' is the sanitized name of the network)
 *
 * @param array $link
 *
 * @return array
 */
add_filter( 'share_link_NETWORK', 'hook__share_link_NETWORK' );
function hook__share_link_NETWORK( $link ) {
	return $link;
}

/**
 * Alter share link data.
 *
 * @param array $link
 *
 * @return array
 */
add_filter( 'share_link', 'hook__share_link' );
function hook__share_link( $link ) {
	return $link;
}

/**
 * Alter share count curl data.
 *
 * @param array $api
 * @param string $network
 *
 * @return array
 */
add_filter( 'share_count', 'hook__share_count', 10, 2 );
function hook__share_count( $api, $network ) {
	return $api;
}

/**
 * Alter share count curl data for specific network.
 * ('NETWORK' is the sanitized name of the network)
 *
 * @param array $api
 * @param string $network
 *
 * @return array
 */
add_filter( 'share_count_NETWORK', 'hook__share_count_NETWORK', 10, 2 );
function hook__share_count_NETWORK( $api, $network ) {
	return $api;
}

/**
 * Alter meta raw data.
 *
 * @param array $meta
 *
 * @return array
 */
add_filter( 'share_meta', 'hook__share_meta' );
function hook__share_rawmeta( $meta ) {
	return $meta;
}

/**
 * Alter meta data.
 *
 * @param array $output
 * @param array $meta
 *
 * @return array
 */
add_filter( 'share_meta', 'hook__share_meta', 10, 2 );
function hook__share_meta( $output, $meta ) {
	return $output;
}

/**
 * Patterns (key) to replaced by (value).
 *
 * @param array $patterns
 * @param bool $description Indicator for dynamic values.
 *
 * @return array
 */
add_filter( 'share_patterns', 'hook__share_patterns', 10, 2 );
function hook__share_patterns( $patterns, $description ) {
	return $patterns;
}

/**
 * Theme share link for specific network.
 * ('NETWORK' is the sanitized name of the network)
 *
 * @param string $output
 * @param array $share
 *
 * @return string
 */
add_filter( 'share_theme_link_NETWORK', 'hook__share_theme_link_NETWORK', 10, 2 );
function hook__share_theme_link_NETWORK( $output, $link ) {
	return $output;
}

/**
 * Theme share link.
 *
 * @param string $output
 * @param array $share
 *
 * @return string
 */
add_filter( 'share_theme_link', 'hook__share_theme_link', 10, 2 );
function hook__share_theme_link( $output, $link ) {
	return $output;
}

/**
 * Theme share list.
 *
 * @param string $output
 * @param array $links
 *
 * @return string
 */
add_filter( 'share_theme_list', 'hook__share_theme_list', 10, 2 );
function hook__share_theme_list( $output, $links ) {
	return $output;
}
