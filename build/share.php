<?php

/**
 * Plugin Name: Share
 * Plugin URI: https://github.com/artcomventure/wordpress-plugin-share
 * Description: Spread your content over social networks and more (Facebook, Twitter, Google+, Pinterest, Tumblr, Whatsapp, SMS, Email).
 * Version: 1.2.1
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
 * Enqueue scripts and styles.
 */
add_action( 'wp_enqueue_scripts', 'share_enqueue_scripts' );
function share_enqueue_scripts() {
	wp_enqueue_script( 'share', SHARE_PLUGIN_URL . 'js/share.min.js', array(), '20160202' );

	// load default styles
	if ( share_get_option( 'css' ) ) {
		wp_enqueue_style( 'share', SHARE_PLUGIN_URL . 'css/share.css', array(), '20160312' );
	}
}

/**
 * i18n.
 */
add_action( 'after_setup_theme', 'share__after_setup_theme' );
function share__after_setup_theme() {
	load_theme_textdomain( 'share', SHARE_PLUGIN_DIR . 'languages' );
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
	$post = get_post();

	if ( ! $url && ! ( $url = get_the_permalink() ) ) {
		$url = $post->guid;
	}

	if ( ! $url = urldecode( $url ) ) {
		return array();
	}

	// string to boolean
	if ( ! is_bool( $cache ) ) {
		$cache = ( ! in_array( strtolower( $cache ), array( 'false', '0' ) ) ? TRUE : FALSE );
	}

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
		'_updated' => time(),
	);

	$ch = curl_init();
	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, TRUE );
	curl_setopt( $ch, CURLOPT_HTTPHEADER, array( 'Content-type: application/json' ) );

	foreach ( $networks as $network => $enabled ) {
		switch ( $network ) {
			default:
				$api = array();
				break;

			case 'Facebook':
				$api = array(
					'url' => 'http://api.facebook.com/method/links.getStats?' . build_query( array(
							'urls' => $url,
							'format' => 'json',
						) ),
					'callback' => function ( $json ) {
						return $json[0]->total_count;
					}
				);
				break;

			case 'Pinterest':
				$api = array(
					'url' => "http://api.pinterest.com/v1/urls/count.json?callback=json_decode&url={$url}",
					'callback' => function ( $json ) {
						return $json->count;
					}
				);
				break;

			case 'Linkedin':
				$api = array(
					'url' => "http://www.linkedin.com/countserv/count/share?url={$url}&format=json",
					'callback' => function ( $json ) {
						return $json->count;
					}
				);
				break;

			case 'Google+':
				$api = array(
					'method' => 'POST',
					'url' => 'https://clients6.google.com/rpc',
					'payload' => json_encode( array(
						array(
							'method' => 'pos.plusones.get',
							'id' => 'p',
							'params' => array(
								'nolog' => TRUE,
								'id' => $url,
								'source' => 'widget',
								'userId' => '@viewer',
								'groupId' => '@self'
							),
							'jsonrpc' => '2.0',
							'key' => 'p',
							'apiVersion' => 'v1',
						)
					) ),
					'callback' => function ( $json ) {
						return $json[0]->result->metadata->globalCounts->count;
					}
				);
				break;
		}

		// let others change/extend curl action
		$api = apply_filters( 'share_count', $api, $network );
		$api = apply_filters( 'share_count_' . sanitize_title( $network ), $api, $network );

		// merge defaults
		$api += array(
			'method' => 'GET',
			'url' => '',
			"callback" => function ( $response ) {
				return NULL;
			},
		);

		// set method
		if ( $api['method'] == 'POST' ) {
			curl_setopt( $ch, CURLOPT_POST, 1 );

			if ( isset( $api['payload'] ) ) {
				curl_setopt( $ch, CURLOPT_POSTFIELDS, $api['payload'] );
			}
//		} else {
//			curl_setopt( $ch, CURLOPT_HTTPGET, 1 );
		}

		curl_setopt( $ch, CURLOPT_URL, $api['url'] );

		if ( $shares[ $network ] = curl_exec( $ch ) ) {
			// we just need plain json
			$shares[ $network ] = preg_replace( '/^[a-z_]*\((.*)\)$/', '\\1', $shares[ $network ] );

			if ( $json = json_decode( $shares[ $network ] ) ) {
				$shares[ $network ] = $json;
			}

			try {
				// get counts
				$shares[ $network ] = $api['callback']( $shares[ $network ] );
				$shares['_overall'] += $shares[ $network ];
			} catch ( Exception $e ) {
				$shares[ $network ] = NULL;
			}
		} else {
			$shares[ $network ] = NULL;
		}
	}

	curl_close( $ch );

	// save data to post
	update_metadata( 'post', $post->ID, '_shares', $shares );

	return $shares;
}

// meta tags (og, twitter, ...)
include( SHARE_PLUGIN_DIR . 'inc/meta.php' );
// share links as widget
include( SHARE_PLUGIN_DIR . 'inc/widgets.php' );
// theme share links
include( SHARE_PLUGIN_DIR . 'inc/theme.php' );
// options
include( SHARE_PLUGIN_DIR . 'inc/options.php' );
// auto include shortcodes
foreach ( scandir( SHARE_PLUGIN_DIR . 'inc' ) as $file ) {
	if ( preg_match( '/shortcode\..+\.php/', $file ) ) {
		require SHARE_PLUGIN_DIR . 'inc/' . $file;
	}
}

/**
 * Remove update notification.
 * Plugin isn't hosted on WordPress.
 */
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

		$links[2] = '<a href="' . $plugin_data['PluginURI'] . '">' . __( 'Visit plugin site' ) . '</a>';

		$links[] = '<a href="' . admin_url( 'options-general.php?page=share-settings' ) . '">' . __( 'Settings' ) . '</a>';
	}

	return $links;
}

/**
 * Delete traces on deactivation.
 */
register_deactivation_hook( __FILE__, 'share_deactivate' );
function share_deactivate() {
	delete_option( 'share' );

	delete_metadata( 'post', NULL, '_shares', '', TRUE );
}
