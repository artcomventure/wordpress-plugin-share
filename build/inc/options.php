<?php

/**
 * Register settings options.
 */
add_action( 'admin_init', 'share__admin_init' );
function share__admin_init() {
	register_setting( 'share', 'share' );
}

/**
 * Register share admin page.
 */
add_action( 'admin_menu', 'share__admin_menu' );
function share__admin_menu() {
	add_options_page( __( 'Share', 'share' ), __( 'Share', 'share' ) . '/' . __( 'Follow', 'share' ), 'manage_options', 'share-settings', 'share_settings_page' );
}

/**
 * Settings page markup.
 */
function share_settings_page() {
	wp_enqueue_script( 'share-admin', SHARE_PLUGIN_URL . 'js/admin.js', array( 'jquery-ui-sortable' ), '20160510' );
	wp_enqueue_style( 'share-admin', SHARE_PLUGIN_URL . 'css/admin.css', array(), '20160510' );

	include( SHARE_PLUGIN_DIR . 'inc/options.form.php' );
}

/**
 * @return string
 */
function share_default_subject() {
	return sprintf( __( 'Found on %1$s. Have a look!', 'share' ), '[siteurl]' );
}

/**
 * @return string
 */
function share_default_text() {
	return '[url]';
}

/**
 * Replacement patterns.
 *
 * @param bool $description
 *
 * @return array
 */
function share_patterns( $description = FALSE ) {
	$patterns = apply_filters( 'share_patterns', array(), $description );

	// merge default
	$patterns = array(
		'url' => get_the_permalink(),
		'title' => get_the_title(),
		'sitename' => get_bloginfo( 'sitename' ),
		'siteurl' => get_bloginfo( 'siteurl' ),
	) + $patterns;

	// replace dynamic value by its description
	if ( $description ) {
		$patterns['url'] = __( 'URL of the page to share', 'share' );
		$patterns['title'] = __( 'Title of the page to share', 'share' );
	}

	return $patterns;
}

/**
 * @param string $option
 *
 * @return array|null
 */
function share_get_option( $option = '' ) {
	if ( ! $option ) {
		return NULL;
	}

	return share_get_options( $option );
}

/**
 * Get specific share option or all of them.
 *
 * @param string $option
 *
 * @return array|null
 */
function share_get_options( $option = '' ) {
	// backward compatibility prior 1.2.0
	$counts = get_option( 'share_counts', 0 );
	$post_types = get_option( 'share_post_types', array() );
	$networks = get_option( 'share_enabled', array() );

	// save options to new format
	if ( $counts || $post_types || $networks ) {
		update_option( 'share', array(
			'counts' => $counts,
			'post_types' => $post_types,
			'networks' => $networks,
		) );

		delete_option( 'share_counts' );
		delete_option( 'share_post_types' );
		delete_option( 'share_enabled' );
	}

	// share version >= 1.2.0

	// get share options
	$share = get_option( 'share', array() );

	// merge default
	$share = array_filter( $share ) + array(
			'count' => 0,
			'post_types' => array(),
			'networks' => array(),
			'follow' => array() // @since 1.3.0
		);

	// remove empty entries
	$share['follow'] = array_values( array_filter( array_map( 'array_filter', $share['follow'] ) ) );

	// get all post types
	$post_types = array_filter( get_post_types(), function ( $post_type ) {
		return ! in_array( $post_type, array( 'revision', 'nav_menu_item', 'custom_css', 'customize_changeset' ) );
	} );

	// merge post types
	foreach ( $post_types as $post_type ) {
		if ( ! array_key_exists( $post_type, $share['post_types'] ) ) {
			$share['post_types'][ $post_type ] = 0;
		}
	}

	// check if post types still exists
	foreach ( $share['post_types'] as $post_type => $enabled ) {
		if ( ! array_key_exists( $post_type, $post_types ) ) {
			unset( $share['post_types'][ $post_type ] );
		}
	}

	// default networks
	$networks = array(
		'Facebook' => 0,
		'Google+' => 0,
		'Twitter' => 0,
		'Email' => 0,
		'Linkedin' => 0,
		'Pinterest' => 0,
		'SMS' => 0,
		'Tumblr' => 0,
		'Whatsapp' => 0,
	);

	// let others extend networks
	$networks = apply_filters( 'share_networks', $networks );

	// check if saved (option) networks are still supported
	foreach ( $share['networks'] as $network => $status ) {
		if ( ! array_key_exists( $network, $networks ) ) {
			unset( $share['networks'][ $network ] );
		}
	}

	// merge default
	$share['networks'] += $networks;

	// return specific option
	if ( $option ) {
		if ( isset( $share[ $option ] ) ) {
			return $share[ $option ];
		}

		return NULL;
	}

	// return all options
	return $share;
}

/**
 * Wrapper for retrieving all available networks.
 */
function share_networks() {
	return share_get_option( 'networks' );
}
