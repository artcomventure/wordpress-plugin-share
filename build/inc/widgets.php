<?php

/**
 * Share Widget.
 */

add_action( 'widgets_init', 'share__widgets_init' );
function share__widgets_init() {
	register_widget( 'Share_Widget' );
}

class Share_Widget extends WP_Widget {

	function __construct() {
		$widget_ops = array(
			'description' => __( 'Let your content be shared.', 'share' )
		);

		$control_ops = array();

		WP_Widget::__construct( 'share',
			__( 'Share buttons', 'share' ),
			$widget_ops, $control_ops );
	}

	function widget( $args, $instance ) {
		extract( $args );

		global $more;

		if ( $more && ( $share_links = get_share_links() ) ) {
			$title = apply_filters( 'widget_title',
				empty( $instance['title'] ) ? '' : $instance['title'],
				$instance, $this->id_base );

			echo $before_widget;

			if ( $title ) {
				echo $before_title . $title . $after_title;
			}

			share_links();

			echo $after_widget;
		}
	}

	function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => '' ) );
		$title = strip_tags( $instance['title'] );

		echo '<p><label for="' . $this->get_field_id( 'title' ) . '">' . esc_html( __( 'Title:' ) ) . '</label> <input class="widefat" id="' . $this->get_field_id( 'title' ) . '" name="' . $this->get_field_name( 'title' ) . '" type="text" value="' . esc_attr( $title ) . '" /></p>';
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$new_instance = wp_parse_args( (array) $new_instance, array( 'title' => '' ) );
		$instance['title'] = strip_tags( $new_instance['title'] );

		return $instance;
	}
}
