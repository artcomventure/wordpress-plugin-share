<?php

// let Yoast do the work
include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
if ( is_plugin_active( 'wordpress-seo/wp-seo.php' ) ) return;

/**
 * Add meta tags.
 */
add_action( 'wp_head', 'share__wp_head' );
function share__wp_head() {
	$post_types = share_get_option( 'post_types' );

	global $more, $wp;

	if ( $more && ( $post = get_post() ) && $post_types[ $post->post_type ] ) {
		$meta = array(
			'title' => get_the_title(),
			'description' => $post->post_content,
			'url' => get_the_permalink(),
		);

		// image
		if ( $meta['image'] = get_post_thumbnail_id( $post->ID ) ) {
			if ( $meta['image'] = wp_get_attachment_image_src( $meta['image'], 'large' ) ) {
				$meta['image'] = $meta['image'][0];
			}
		}
	} else {
		$meta = array();
	}

	// defaults
	$meta += array(
		'title' => get_option( 'blogname' ),
		'description' => get_bloginfo( 'description', 'display' ),
		'url' => home_url( add_query_arg( array(), $wp->request ) ),
		'site_name' => get_option( 'blogname' ),
		'locale' => get_locale(),
	);

	if ( empty( $meta['image'] ) && function_exists( 'the_custom_logo' ) && ( $meta['image'] = get_theme_mod( 'custom_logo' ) ) ) {
		if ( $meta['image'] = wp_get_attachment_image_src( $meta['image'], 'large' ) ) {
			$meta['image'] = $meta['image'][0];
		}
	}

	// plain title/description
	$meta['title'] = preg_replace( '/(\\n|\\r)+/', ' ', strip_tags( str_replace( '<', ' <', $meta['title'] ) ) );
	$meta['title'] = preg_replace( '/\s+/', ' ', $meta['title'] );
	$meta['description'] = trim( preg_replace( '/(\\n|\\r)+/', ' ', strip_tags( str_replace( '<', ' <', strip_shortcodes( $meta['description'] ) ) ) ) );
	$meta['description'] = preg_replace( '/\s+/', ' ', $meta['description'] );

	// let others change meta data
	$meta = array_filter( $meta );
	$meta = apply_filters( 'share_rawmeta', $meta );

	$output = array(
		'<meta name="twitter:card" content="summary' . (!empty($meta['image']) ? '_large_image' : '') . '" />'
	);

	foreach (
		array(
			'image' => array( 'og', 'twitter' ),
			'title' => array( 'og', 'twitter' ),
			'description' => array( '', 'og', 'twitter' ),
			'url' => array( 'og' ),
			'site_name' => array( 'og' ),
			'locale' => array( 'og' ),
		) as $property => $protocols
	) {
		if ( empty( $meta[ $property ] ) ) {
			continue;
		}

		if ( ! is_array( $protocols ) ) {
			$protocols = array( $protocols );
		}

		foreach ( $protocols as $protocol ) {
            $output[ "{$protocol}:{$property}" ] = '<meta property="' . "{$protocol}:{$property}" . '" content="' . $meta[ $property ] . '" />';

            // add image dimensions
            if ( "{$protocol}:{$property}" == 'og:image' && ($size = @getimagesize( $meta[ $property ] )) ) {
                foreach ( array( 'width', 'height' ) as $key => $attribute ) {
                    $output[ "{$protocol}:{$property}:{$attribute}" ] = '<meta property="' . "{$protocol}:{$property}:{$attribute}" . '" content="' . $size[$key] . '" />';
                }
            }
		}
	}

	echo implode( "\n", apply_filters( 'share_meta', $output, $meta ) ) . "\n";
}
