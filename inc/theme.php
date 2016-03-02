<?php

/**
 * Theme share links.
 *
 * @param string $url
 * @param bool $cache
 *
 * @return string
 */
function share_links( $url = '', $cache = TRUE ) {
	echo get_share_links( $url, $cache );
}

function get_share_links( $url = '', $cache = TRUE ) {
	if ( empty( $url ) ) {
		$url = get_the_permalink();
	}

	$post = get_post();

	if ( ( $show_counts = get_option( 'share_counts', FALSE ) ) && $post->ID ) {
		// get counts
		$shares = share_counts( $url, $cache );
	} else {
		$shares = array();
	}

	$networks = share_networks();
	if ( ! $post_types = get_option( 'share_post_types', array() ) ) {
		$post_types = array();
	}

	foreach ( $networks as $network => &$share ) {
		if ( ( ( empty( $post ) || ! array_key_exists( $post->post_type, $post_types ) )
		       && ! is_front_page() ) || ! $share
		) {
			unset( $networks[ $network ] );
			continue;
		}

		if ( $show_counts ) {
			if ( ! isset( $shares[ $network ] ) || is_null( $shares[ $network ] ) ) {
				$share = array();
			} else {
				$share = array(
					'text' => _n( '%d share', '%d shares', $shares[ $network ], 'share' ),
					'count' => $shares[ $network ],
				);
			}
		} else {
			$share = array();
		}

		$share += array(
			'prefix' => '',
			'text' => $network,
			'query' => array(),
			'suffix' => '',
		);

		switch ( $network ) {
			case 'Facebook':
				$share = array(
					         'href' => 'http://www.facebook.com/dialog/share_open_graph',
					         'query' => array(
						         'app_id' => 'YourAppID',
						         'display' => 'popup',
						         'action_type' => 'og.likes',
						         'action_properties' => json_encode( array( 'object' => $url ) ),
						         'redirect_uri' => $url,
					         )
				         ) + $share;

				if ( $show_counts ) {
					$share['text'] = _n( '%d like', '%d likes', $share['count'], 'share' );
				}
				break;

			case 'Twitter':
				$share = array(
					         'href' => 'https://twitter.com/intent/tweet',
					         'query' => array(
						         'url' => $url,
						         'text' => get_the_title( $post->ID ),
					         ),
				         ) + $share;

				if ( $show_counts ) {
					$share['text'] = _n( '%d tweet', '%d tweets', $share['count'], 'share' );
				}
				break;

			case 'Google+':
				$share = array(
					         'href' => 'https://plus.google.com/share',
					         'query' => array(
						         'url' => $url,
					         ),
				         ) + $share;
				break;

			case 'Pinterest':
				$share = array(
					         'href' => "javascript:void((function()%7Bvar%20e=document.createElement(&apos;script&apos;);e.setAttribute(&apos;type&apos;,&apos;text/javascript&apos;);e.setAttribute(&apos;charset&apos;,&apos;UTF-8&apos;);e.setAttribute(&apos;src&apos;,&apos;http://assets.pinterest.com/js/pinmarklet.js?r=&apos;+Math.random()*99999999);document.body.appendChild(e)%7D)());",
				         ) + $share;
				break;

			case 'Tumblr':
				$share = array(
					         'href' => 'http://www.tumblr.com/share/link',
					         'query' => array(
						         'url' => $url,
					         ),
				         ) + $share;
				break;

			case 'Email':
				$share = array(
					         'text' => __( 'Email', 'share' ),
					         'href' => 'mailto:',
					         'query' => array(
						         'subject' => sprintf( __( 'Found on %s. Have a look!', 'share' ), get_bloginfo( 'sitename' ) ),
						         'body' => $url,
					         ),
				         ) + $share;
				break;

			case 'Whatsapp':
				$share = array(
					         'text' => __( 'Whatsapp' ),
					         'href' => 'whatsapp://send',
					         'query' => array(
						         'text' => sprintf( __( 'Found on %s. Have a look!', 'share' ), get_bloginfo( 'sitename' ) ) . " \n" . $url,
					         ),
				         ) + $share;
				break;

			case 'SMS':
				$share = array(
					         'text' => __( 'SMS' ),
					         'href' => 'sms:',
					         'query' => array(
						         '' => '', // body has to be with & ... so it must second parameter
						         'body' => sprintf( __( 'Found on %s. Have a look!', 'share' ), get_bloginfo( 'sitename' ) ) . " \n" . $url,
					         ),
				         ) + $share;
				break;
		}

		// hide on non mobile
		if ( in_array( $network, array( 'Whatsapp', 'SMS' ) ) ) {
			if ( ! preg_match( '/(iPhone|iPod|iPad|BlackBerry|Pre|Palm|Googlebot-Mobile|mobi|Safari Mobile|Windows Mobile|Android|Opera Mini|mobile)/', $_SERVER['HTTP_USER_AGENT'], $matches ) ) {
				unset( $networks[ $network ] );
				continue;
			}
		}

		$share['network'] = $network;

		// let others change links
		$share = apply_filters( 'share_link', $share );
		if ( ! $share ) {
			unset( $networks[ $network ] );
			continue;
		}

		// Facebook fallback sharer
		if ( $network == 'Facebook' && ! is_numeric( $share['query']['app_id'] ) ) {
			$share = array(
				'href' => 'http://www.facebook.com/sharer.php',
				'query' => array(
					'u' => $url,
				),
			);
		}

		$share = $share['prefix'] . '<a class="share__link share__' . sanitize_title( $network ) . '" href=\''
		         . ( ! empty( $share['query'] ) ? add_query_arg( $share['query'], $share['href'] ) : $share['href'] ) . '\'>'
		         . '<span>' . sprintf( $share['text'], $share['count'] ) . '</span>'
		         . '</a>' . $share['suffix'];
	}

	if ( empty( $networks ) ) {
		return '';
	}

	return '<ul class="share"><li>' . implode( '</li><li>', $networks ) . '</li></ul>';
}
