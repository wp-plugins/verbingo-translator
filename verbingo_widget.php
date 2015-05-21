<?php

class verbingo_widget extends WP_Widget {

	// Constructor 
	function verbingo_widget() {
		$widget_ops = array( 'classname' => 'verbingo_sidebar', 'description' => __('Verbingo sidebar widget') );
		$this->WP_Widget( 'verbingo-widget', __('Verbingo Translation'), $widget_ops );
	}

	// Display widget with signup form in frontend 
	function widget( $args, $instance ) {
		extract( $args );

		// Get widget title set in dashboard
		$title = apply_filters('widget_title', $instance['title'] );

		echo $before_widget;

		// Display title and form
		if ( $title )
			echo $before_title . $title . $after_title;
			echo do_shortcode( '[verbingo-widget]' );

		echo $after_widget;
	}

	// Update widget 
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		// Strip tags from title to remove HTML 
		$instance['title'] = strip_tags( $new_instance['title'] );

		return $instance;
	}

	// Set widget and title in dashboard
	function form( $instance ) {

		$defaults = array( 'title' => __('Verbingo Translator') );
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>

		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Widget Title:'); ?></label>
		<input class="widefat" type='text' maxlength='50' id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" />
		</p>

	<?php
	}
}

?>