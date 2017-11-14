<?php

/**
 * Display share links.
 *
 * @param string $url
 * @param bool $cache
 *
 * @return string
 */
function share_links( $url = '', $cache = true ) {
	echo get_share_links( $url, $cache );
}

/**
 * Retrieve share links.
 *
 * @param string $url
 * @param bool $cache
 *
 * @return string
 */
function get_share_links( $url = '', $cache = true ) {
	if ( empty( $url ) ) {
		$url = get_the_permalink();
	}

	$share_options = share_get_options();
	$networks = $share_options['share'];
	$post_types = $share_options['post_types'];

	if ( ! ( $post = get_post() ) || ! $post_types[ $post->post_type ] ) {
		return '';
	}

	if ( $share_options['counts'] && $post->ID ) {
		// get counts
		$shares = share_counts( $url, $cache );
	}

	foreach ( $networks as $network => $link ) {
		// disabled?
		if ( ! $link['enabled'] ) {
			unset( $networks[ $network ] );
			continue;
		}

		// share count
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
						'app_id' => !empty( $share_options[ $network ]['app_id'] ) ? $share_options[ $network ]['app_id'] : NULL,
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
						'mini' => true,
						'url' => $url,
						'title' => get_the_title(),
						'summary' => strip_tags( strip_shortcodes( get_the_content() ) ),
					),
				);

				// shorten text
				if ( ( $summary = substr( $link['query']['summary'], 0, 100 ) ) != $link['query']['summary'] ) {
					$link['query']['summary'] = $summary . ' ...';
				}
				break;

            case 'Xing':
                $link = array(
                    'href' => 'https://www.xing-share.com/app/user',
                    'query' => array(
                        'op' => 'share;sc_p=xing-share;url=' . $url,
                    ),
                );
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
		$link = apply_filters( 'share_link', array( 'network' => $network ) + $link );
		$link = apply_filters( 'share_link_' . sanitize_title( $network ), $link );

		// no link data
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
				if ( filter_var( trim( $share_options['share'][ $network ]['icon'] ), FILTER_VALIDATE_URL ) ) {
					$networks[ $network ] .= '<img src="' . $share_options['share'][ $network ]['icon'] . '" />';
				} else if ( trim( $share_options['share'][ $network ]['icon'] ) ) {
					$networks[ $network ] .= '<i class="' . trim( $share_options['share'][ $network ]['icon'] ) . '"></i>';
				}
				$networks[ $network ] .= '<span' . ( trim( $share_options['share'][ $network ]['icon'] ) ? ' class="screen-reader-text"' : '' ) . '>' . sprintf( $link['text'], ( isset( $link['count'] ) ? $link['count'] : '' ) ) . '</span>';
				$networks[ $network ] .= '</a>' . $link['suffix'];
			}
		}
	}

	// no links at all
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
add_filter( 'share_link_facebook', 'share_link_facebook', 100 );
function share_link_facebook( $link ) {
	// Facebook fallback sharer
	if ( ! isset( $link['query']['app_id'] ) || ! is_numeric( $link['query']['app_id'] ) ) {
		$action_properties = json_decode( $link['query']['action_properties'] );

		$link = array(
			        'href' => 'http://www.facebook.com/sharer.php',
			        'query' => array(
				        'u' => $action_properties->object,
			        ),
		        ) + $link;
	}

	return $link;
}

/**
 * Filter mobile links on non mobile devices.
 */
add_filter( 'share_link', 'share_link_mobile', 0 );
function share_link_mobile( $link ) {
	if ( in_array( $link['network'], array( 'Whatsapp', 'SMS' ) ) ) {
		if ( ! preg_match( '/(iPhone|iPod|iPad|BlackBerry|Pre|Palm|Googlebot-Mobile|mobi|Safari Mobile|Windows Mobile|Android|Opera Mini|mobile)/', $_SERVER['HTTP_USER_AGENT'], $matches ) ) {
			return NULL;
		}
	}

	return $link;
}

/**
 * Default share texts.
 */
add_filter( 'share_link', 'share_link_default_texts' );
function share_link_default_texts( $link ) {
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

/**
 * Replace patterns.
 */
add_filter( 'share_link', 'share_link_replace_patterns', 1000 );
function share_link_replace_patterns( $link ) {
	if ( is_array( $link ) ) {
		// loop through $link data
		foreach ( $link as $key => $value ) {
			// replace patterns
			if ( is_string( $value ) ) {
				// get patterns and replacements
				$patterns = share_patterns();

				$replacements = array_values( $patterns );

				$patterns = array_keys( $patterns );
				$patterns = array_map( function ( $pattern ) {
					return '/\[' . $pattern . '\]/';
				}, $patterns );

				// eventually replace patterns
				$link[ $key ] = preg_replace( $patterns, $replacements, $value );
			} elseif ( is_array( $value ) ) {
				// recursive
				$link[ $key ] = share_link_replace_patterns( $value );
			}
		}
	}

	return $link;
}

/**
 * Display follow links.
 *
 * @return string
 */
function follow_links() {
	echo get_follow_links();
}

/**
 * Retrieve follow links.
 *
 * @return string
 */
function get_follow_links() {
	$networks = array();

	foreach ( share_get_option( 'follow' ) as $network ) {
		$icon = '';
		if ( filter_var( $network['icon'], FILTER_VALIDATE_URL ) ) {
			$icon = '<img src="' . $network['icon'] . '" />';
		} else if ( trim( $network['icon'] ) ) {
			$icon = '<i class="' . trim( $network['icon'] ) . '"></i>';
		}

		$link = '<a class="follow__link follow__' . sanitize_title( $network['network'] ) . '" title="' . sprintf( __( 'Follow us on %s', 'share' ), $network['network'] ) . '" href="' . $network['url'] . '" target="_blank">' .
		        $icon . '<span' . ( $icon ? ' class="screen-reader-text"' : '' ) . '>' . ( !empty( $network['network'] ) ? $network['network'] : $network['url'] ) . '</span>' .
		        '</a>';

		// let others change links
		$link = apply_filters( 'follow_link', $link, $network );

		if ( $link ) {
			$networks[] = $link;
		}
	}

	// no links at all
	if ( empty( $networks ) ) {
		return '';
	}

	// let others theme share list output
	if ( $output = apply_filters( 'follow_theme_list', '', $networks ) ) {
		return $output;
	}

	return '<ul class="follow"><li>' . implode( '</li><li>', $networks ) . '</li></ul>';
}

