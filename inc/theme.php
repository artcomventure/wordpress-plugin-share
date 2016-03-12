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

/**
 * Theme share links.
 *
 * @param string $url
 * @param bool $cache
 *
 * @return string
 */
function get_share_links( $url = '', $cache = TRUE ) {
	if ( empty( $url ) ) {
		$url = get_the_permalink();
	}

	$share_options = share_get_options();
	$networks = $share_options['networks'];
	$post_types = $share_options['post_types'];

	global $more;

	if ( ! $more || ! ( $post = get_post() ) || ! $post_types[ $post->post_type ] ) {
		return '';
	}

	if ( $share_options['counts'] && $post->ID ) {
		// get counts
		$shares = share_counts( $url, $cache );
	}

	foreach ( $networks as $network => $link ) {
		// disabled?
		if ( ! $link ) {
			unset( $networks[ $network ] );
			continue;
		}

		$shares[ $network ] = $share_options['counts'] && isset( $shares[ $network ] ) ? $shares[ $network ] : NULL;

		// collect link data
		switch ( $network ) {
			default:
				$link = array();
				break;

			case 'Facebook':
				$link = array(
					'text' => ( is_numeric( $shares[ $network ] )
						? _n( '%d like', '%d likes', $shares[ $network ], 'share' ) : '' ),
					'href' => 'http://www.facebook.com/dialog/share_open_graph',
					'query' => array(
						'app_id' => $share_options[ $network ]['app_id'],
						'display' => 'popup',
						'action_type' => 'og.likes',
						'action_properties' => json_encode( array( 'object' => $url ) ),
						'redirect_uri' => $url,
					)
				);
				break;

			case 'Twitter':
				$link = array(
					'text' => ( is_numeric( $shares[ $network ] )
						? _n( '%d tweet', '%d tweets', $shares[ $network ], 'share' ) : '' ),
					'href' => 'https://twitter.com/intent/tweet',
					'query' => array(
						'url' => $url,
						'text' => get_the_title( $post->ID ),
					),
				);
				break;

			case 'Google+':
				$link = array(
					'href' => 'https://plus.google.com/share',
					'query' => array(
						'url' => $url,
					),
				);
				break;

			case 'Pinterest':
				$link = array(
					'href' => "javascript:void((function()%7Bvar%20e=document.createElement(&apos;script&apos;);e.setAttribute(&apos;type&apos;,&apos;text/javascript&apos;);e.setAttribute(&apos;charset&apos;,&apos;UTF-8&apos;);e.setAttribute(&apos;src&apos;,&apos;http://assets.pinterest.com/js/pinmarklet.js?r=&apos;+Math.random()*99999999);document.body.appendChild(e)%7D)());",
				);
				break;

			case 'Tumblr':
				$link = array(
					'href' => 'http://www.tumblr.com/share/link',
					'query' => array(
						'url' => $url,
					),
				);
				break;

			case 'Email':
				$link = array(
					'text' => __( 'Email', 'share' ),
					'href' => 'mailto:',
					'query' => array(
						'subject' => '',
						'body' => '',
					),
				);
				break;

			case 'Whatsapp':
				$link = array(
					'href' => 'whatsapp://send',
					'query' => array(
						'text' => '',
					),
				);
				break;

			case 'SMS':
				$link = array(
					'href' => 'sms:',
					'query' => array(
						// body has to be with & ... therefor I leave the first param blank
						'' => '',
						'body' => '',
					),
				);
				break;

			case 'Linkedin':
				$link = array(
					'href' => 'https://www.linkedin.com/shareArticle',
					'query' => array(
						'mini' => TRUE,
						'url' => $url,
						'title' => get_the_title(),
						'summary' => strip_tags( strip_shortcodes( get_the_content() ) ),
						0,
					),
				);

				// shorten text
				if ( ( $summary = substr( $link['query']['summary'], 0, 100 ) ) != $link['query']['summary'] ) {
					$link['query']['summary'] = $summary . ' ...';
				}
				break;
		}

		// merge defaults
		$link = array_filter( $link ) + array(
				'prefix' => '',
				'text' => ( is_numeric( $shares[ $network ] )
					? _n( '%d share', '%d shares', $shares[ $network ], 'share' ) : $network ),
				'href' => '',
				'query' => array(),
				'suffix' => '',
			);

		// let others change links
		$link = apply_filters( 'share_link', array( 'network' => $network ) + $link, $url );
		$link = apply_filters( 'share_link_' . sanitize_title( $network ), $link, $url );

		if ( ! $link || ( is_array( $link ) && ! array_filter( $link ) ) ) {
			unset( $networks[ $network ] );
			continue;
		}

		// let others theme share link output
		if ( ! $networks[ $network ] = apply_filters( 'share_theme_link', '', $link ) ) {
			if ( ! $networks[ $network ] = apply_filters( 'share_theme_link_' . sanitize_title( $network ), '', $link ) ) {
				// default theme
				$networks[ $network ] = $link['prefix'];
				$networks[ $network ] .= '<a class="share__link share__' . sanitize_title( $network ) . '" href=\'';
				$networks[ $network ] .= ( ! empty( $link['query'] ) ? add_query_arg( $link['query'], $link['href'] ) : $link['href'] ) . '\'>';
				$networks[ $network ] .= '<span>' . sprintf( $link['text'], $link['count'] ) . '</span>';
				$networks[ $network ] .= '</a>' . $link['suffix'];
			}
		}
	}

	if ( empty( $networks ) ) {
		return '';
	}

	// let others theme share list output
	if ( $output = apply_filters( 'share_theme_list', '', $networks ) ) {
		return $output;
	}

	return '<ul class="share"><li>' . implode( '</li><li>', $networks ) . '</li></ul>';
}

/**
 * 'share_link_facebook' filter.
 */
add_filter( 'share_link_facebook', 'share_link_facebook', 100, 2 );
function share_link_facebook( $link, $url ) {
	// Facebook fallback sharer
	if ( isset( $link['query']['app_id'] ) && ! is_numeric( $link['query']['app_id'] ) ) {
		$link = array(
			        'href' => 'http://www.facebook.com/sharer.php',
			        'query' => array(
				        'u' => $url,
			        ),
		        ) + $link;
	}

	return $link;
}

/**
 * 'share_link' filter.
 */
add_filter( 'share_link', 'share_link', 0, 2 );
function share_link( $link, $url ) {
	// hide on non mobile
	if ( in_array( $link['network'], array( 'Whatsapp', 'SMS' ) ) ) {
		if ( ! preg_match( '/(iPhone|iPod|iPad|BlackBerry|Pre|Palm|Googlebot-Mobile|mobi|Safari Mobile|Windows Mobile|Android|Opera Mini|mobile)/', $_SERVER['HTTP_USER_AGENT'], $matches ) ) {
			return NULL;
		}
	}

	// share texts
	if ( in_array( $link['network'], array( 'Email', 'Whatsapp', 'SMS' ) )
	     && ( $options = share_get_option( $link['network'] ) )
	) {
		if ( ! $subject = $options['subject'] ) {
			$subject = share_default_subject();
		}

		if ( ! $text = $options['text'] ) {
			$text = share_default_text();
		}

		if ( ! isset( $options['subject'] ) ) {
			$text = $subject . ' ' . $text;
		}

		// get patterns and replacements
		$patterns = share_patterns();
		$patterns['url'] = $url;

		$replacement = array_values( $patterns );

		$patterns = array_keys( $patterns );
		$patterns = array_map( function ( $pattern ) {
			return '/\[' . $pattern . '\]/';
		}, $patterns );

		// eventually replace patterns
		foreach ( array( 'subject', 'text' ) as $string ) {
			$$string = preg_replace( $patterns, $replacement, $$string );
		}

		switch ( $link['network'] ) {
			case 'Email':
				$link['query']['subject'] = $subject;

			case 'SMS': // !!! and Email !!!
				$link['query']['body'] = $text;
				break;

			case 'Whatsapp':
				$link['query']['text'] = $text;
				break;
		}
	}

	return $link;
}
