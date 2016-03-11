<?php

/**
 * Plugin Name: Share
 * Plugin URI: https://github.com/artcomventure/wordpress-plugin-share
 * Description: Spread your content over social networks and more (Facebook, Twitter, Google+, Pinterest, Tumblr, Whatsapp, SMS, Email).
 * Version: 1.1.0
 * Text Domain: share
 * Author: artcom venture GmbH
 * Author URI: http://www.artcom-venture.de/
 */

if ( ! defined( 'SHARE_PLUGIN_URL' ) ) {
	define( 'SHARE_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
}

if ( ! defined( 'SHARE_PLUGIN_DIR' ) ) {
	define( 'SHARE_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
}

/**
 * Share settings options.
 *
 * @return array
 */
function share_get_options() {
	return array( 'share_counts', 'share_enabled', 'share_post_types' );
}

/**
 * Register settings options.
 */
add_action( 'admin_init', 'share__admin_init' );
function share__admin_init() {
	foreach ( share_get_options() as $setting ) {
		register_setting( 'share', $setting );
	}
}

/**
 * Register share admin page.
 */
add_action( 'admin_menu', 'share__admin_menu' );
function share__admin_menu() {
	add_options_page( __( 'Share', 'share' ), __( 'Share', 'share' ), 'manage_options', 'share-settings', 'share_settings_page' );
}

/**
 * Settings page markup.
 */
function share_settings_page() {
	wp_enqueue_script( 'share-admin', SHARE_PLUGIN_URL . 'js/admin.js', array( 'jquery-ui-sortable' ), '20160203' );
	wp_enqueue_style( 'share-admin', SHARE_PLUGIN_URL . 'css/admin.css', array(), '20160203' );

	include( SHARE_PLUGIN_DIR . 'inc/settings.php' );
}

/**
 * Enqueue scripts and styles.
 */
add_action( 'wp_enqueue_scripts', 'share_enqueue_scripts' );
function share_enqueue_scripts() {
	wp_enqueue_script( 'share', SHARE_PLUGIN_URL . 'js/share.min.js', array(), '20160202' );
	wp_enqueue_style( 'share', SHARE_PLUGIN_URL . 'css/share.min.css', array(), '20160311' );
}

/**
 * i18n.
 */
add_action( 'after_setup_theme', 'share__after_setup_theme' );
function share__after_setup_theme() {
	load_theme_textdomain( 'share', SHARE_PLUGIN_DIR . 'languages' );
}

/**
 * Collect all possible networks.
 */
function share_networks() {
	$networks = array(
		'Facebook' => 0,
		'Pinterest' => 0,
		'Twitter' => 0,
		'Tumblr' => 0,
		'Google+' => 0,
		'Email' => 0,
		'Whatsapp' => 0,
		'SMS' => 0,
	);

	// let others change/extend networks
	$networks = apply_filters( 'share_networks', $networks );

	// get config
	if ( ! $enabled = get_option( 'share_enabled', array() ) ) {
		$enabled = array();
	}

	// check if networks are still supported
	foreach ( $enabled as $network => $status ) {
		if ( ! array_key_exists( $network, $networks ) ) {
			unset( $networks[ $network ] );
		}
	}

	// merge defaults
	return $enabled + $networks;
}

/**
 * Get all share counts through social network APIs.
 *
 * @param string $url
 * @param bool $cache
 *
 * @return mixed
 */
function share_counts( $url = '', $cache = TRUE ) {
	if ( empty( $url ) ) {
		$url = get_the_permalink();
	}

	$post = get_post();
	$networks = share_networks();

	if ( $cache
	     && ( $shares = get_metadata( 'post', $post->ID, '_shares', TRUE ) )
	     // one hour cache
	     && HOUR_IN_SECONDS > time() - $shares['_updated']
	     // number of $shares = number of $networks + 2 (_overall and _updated)
	     && count( $networks ) + 2 <= count( $shares )
	) {
		return $shares;
	}

	// no cache, first call, outdated or sth. changed in $networks

	$shares = array(
		'_overall' => 0,
	);

	$ch = curl_init();
	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, TRUE );
	curl_setopt( $ch, CURLOPT_HTTPHEADER, array( 'Content-type: application/json' ) );

	foreach ( $networks as $network => $enabled ) {
		$args = array(
			'cURL' => "",
			'count' => '',
		);

		switch ( $network ) {
			default:
				break;

			case 'Facebook':
				$args['cURL'] = "http://api.facebook.com/method/links.getStats?urls=$url&format=json";
				$args['count'] = '[0]->total_count;';
				break;

			case 'Twitter':
				$args['cURL'] = "http://cdn.api.twitter.com/1/urls/count.json?url=$url";
				$args['count'] = '->count;';
				break;

			case 'Pinterest':
				$args['cURL'] = "http://api.pinterest.com/v1/urls/count.json?callback=json_decode&url=$url";
				$args['count'] = '->count;';
				break;

			case 'Google+':
				$args['cURL'] = "https://clients6.google.com/rpc";
				curl_setopt( $ch, CURLOPT_POST, 1 );
				curl_setopt( $ch, CURLOPT_POSTFIELDS, '[{"method":"pos.plusones.get","id":"p","params":{"nolog":true,"id":"' . $url . '","source":"widget","userId":"@viewer","groupId":"@self"},"jsonrpc":"2.0","key":"p","apiVersion":"v1"}]' );
				$args['count'] = '[0]->result->metadata->globalCounts->count;';
				break;
		}

		// let others change/extend curl action
		$args = apply_filters( 'share_count', $network, $args );

		if ( ! empty( $args['cURL'] ) ) {
			try {
				curl_setopt( $ch, CURLOPT_URL, $args['cURL'] );
				$shares[ $network ] = curl_exec( $ch );

				// get rid of callback function
				// we just need plain json
				$shares[ $network ] = preg_replace( '/^.*\((.*)\)$/', '\\1', $shares[ $network ] );;
				$shares[ $network ] = json_decode( $shares[ $network ] );

				// get count from json response
				// todo: check if is there's a better way then 'eval'
				eval( '$shares[$network] = $shares[$network]' . $args['count'] );
			} catch ( Exception $e ) {
				$shares[ $network ] = 0;
			}

			$shares['_overall'] += $shares[ $network ];
		} // no api available
		else {
			$shares[ $network ] = NULL;
		}
	}

	curl_close( $ch );

	$shares['_updated'] = time();

	// save data to post
	update_metadata( 'post', $post->ID, '_shares', $shares );

	return $shares;
}

// meta tags (og, twitter, ...)
include( SHARE_PLUGIN_DIR . '/inc/meta.php' );
// share links as widget
include( SHARE_PLUGIN_DIR . '/inc/widgets.php' );
// theme share links
include( SHARE_PLUGIN_DIR . '/inc/theme.php' );
// auto include shortcodes
foreach ( scandir( SHARE_PLUGIN_DIR . '/inc' ) as $file ) {
	if ( preg_match( '/shortcode\..+\.php/', $file ) ) {
		require SHARE_PLUGIN_DIR . '/inc/' . $file;
	}
}

// remove update notification
// ... just in case ;)
add_filter( 'site_transient_update_plugins', 'share__site_transient_update_plugins' );
function share__site_transient_update_plugins( $value ) {
	$plugin_file = plugin_basename( __FILE__ );

	if ( isset( $value->response[ $plugin_file ] ) ) {
		unset( $value->response[ $plugin_file ] );
	}

	return $value;
}

/**
 * Change details link to GitHub repository.
 */
add_filter( 'plugin_row_meta', 'share__plugin_row_meta', 10, 2 );
function share__plugin_row_meta( $links, $file ) {
	if ( plugin_basename( __FILE__ ) == $file ) {
		$plugin_data = get_plugin_data( WP_PLUGIN_DIR . '/' . $file );

		$links[2] = '<a href="' . $plugin_data['PluginURI'] . '">' . __( 'Plugin-Seite aufrufen' ) . '</a>';

		$links[] = '<a href="' . admin_url( 'options-general.php?page=share-settings' ) . '">' . __( 'Settings' ) . '</a>';
	}

	return $links;
}

/**
 * Delete traces on deactivation.
 */
register_deactivation_hook( __FILE__, 'share_deactivate' );
function share_deactivate() {
	foreach ( share_get_options() as $option ) {
		delete_option( $option );
	}

	delete_metadata( 'post', NULL, '_shares', '', TRUE );
}
