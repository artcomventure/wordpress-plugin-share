<?php

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
			'description' => strip_tags( strip_shortcodes( $post->post_content ) ),
			'url' => get_the_permalink(),
		);

		// image
		if ( $meta['image'] = get_post_thumbnail_id( $post->ID ) ) {
			$meta['image'] = wp_get_attachment_image_src( $meta['image'], 'large' )[0];
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
		$meta['image'] = wp_get_attachment_image_src( $meta['image'], 'large' )[0];
	}

	$meta['description'] = preg_replace( '/(\\n|\\r)/', ' ', $meta['description'] );
	$meta['description'] = preg_replace( '/\s+/', ' ', $meta['description'] );

	// let others change meta data
	$meta = apply_filters( 'share_meta', array_filter( $meta ) );
	$meta = array_filter( $meta );

	$output = array();

	if ( ! empty( $meta['image'] ) ) {
		foreach ( array( 'og:image', 'twitter:image:src' ) as $property ) {
			$output[] = '<meta property="' . $property . '" content="' . $meta['image'] . '" />';
		}
	}

	// title
	if ( ! empty( $meta['title'] ) ) {
		foreach ( array( 'og:title', 'twitter:title' ) as $property ) {
			$output[] = '<meta property="' . $property . '" content="' . $meta['title'] . '" />';
		}
	}

	// og
	foreach ( array( 'description', 'url', 'site_name', 'locale' ) as $property ) {
		if ( empty( $meta['title'] ) ) {
			continue;
		}

		$output[] = '<meta property="' . $property . '" content="' . $meta[ $property ] . '" />';
	}

	echo implode( "\n", $output ) . "\n";
}
