<?php

/**
 * ...
 */
add_action( 'wp_head', 'share__wp_head' );
function share__wp_head() {
	$post_types = share_get_option( 'post_types' );

	global $more;

	if ( ! $more || ! ( $post = get_post() ) || ! $post_types[ $post->post_type ] ) {
		return '';
	}

	$meta = array(
		'title' => get_the_title(),
		'description' => strip_tags( strip_shortcodes( $post->post_content ) ),
		'url' => get_the_permalink(),
		'site_name' => get_option( 'blogname' ),
		'locale' => get_locale(),
	);

	$meta['description'] = preg_replace( '/(\\n|\\r)/', ' ', $meta['description'] );
	$meta['description'] = preg_replace( '/\s+/', ' ', $meta['description'] );

	// image
	if ( $meta['image'] = get_post_thumbnail_id( $post->ID ) ) {
		$meta['image'] = wp_get_attachment_image_src( $meta['image'], 'large' )[0];
	}

	// let others change meta data
	$meta = apply_filters( 'share_meta', array_filter( $meta ) );
	$meta = array_filter( $meta );

	if ( ! empty( $meta['image'] ) ) {
		foreach ( array( 'og:image', 'twitter:image:src' ) as $property ) {
			echo '<meta property="' . $property . '" content="' . $meta['image'][0] . '" />';
		}
	}

	// title
	if ( ! empty( $meta['title'] ) ) {
		foreach ( array( 'og:title', 'twitter:title' ) as $property ) {
			echo '<meta property="' . $property . '" content="' . $meta['title'] . '" />';
		}
	}

	// og
	foreach ( array( 'description', 'url', 'site_name', 'locale' ) as $property ) {
		if ( empty( $meta['title'] ) ) {
			continue;
		}

		echo '<meta property="' . $property . '" content="' . $meta[ $property ] . '" />';
	}
}
