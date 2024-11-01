<?php
/**
 * Holds the Latest Uploaded Torrents widget class
 *
 * @package WP-Trader
 * @subpackage Widget
 */

if ( !class_exists( 'Latest_Uploads' ) ) :

/**
 * Example Widget class.
 * This class handles everything that needs to be handled with the widget:
 * the settings, form, display, and update.  Nice!
 *
 * @since 0.1
 */
class Latest_Uploads extends WP_Widget {

	/**
	 * Widget setup.
	 */
	function Latest_Uploads() {
		/* Widget settings. */
		$widget_ops = array( 'classname' => 'Latest Uploads', 'description' => __('A widget which shows the latest uploaded torrents.', 'Latest Uploads') );

		/* Create the widget. */
		$this->WP_Widget( 'latest-uploads', __('Latest Uploads', 'latest-uploads'), $widget_ops, $control_ops );
	}

	/**
	 * How to display the widget on the screen.
	 */
	function widget( $args, $instance ) {
		extract( $args );

		/* Our variables from the widget settings. */
		$title = apply_filters('widget_title', $instance['title'] );
		$text = apply_filters( 'widget_text', $instance['text'] );
		/* Before widget (defined by themes). */
		echo $before_widget;

		/* Display the widget title if one was input (before and after defined by themes). */
		if ( $title )
			echo $before_title . $title . $after_title;

				global $current_user;
				get_currentuserinfo();
				?>
				<div id="Latest_Uploads" class="textwidget"><?php echo $text; ?></div>
				<?php

		/* After widget (defined by themes). */
		echo $after_widget;
	}

	/**
	 * Update the widget settings.
	 */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		/* Strip tags for title and name to remove HTML (important for text inputs). */
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['text'] = strip_tags( $new_instance['text'] );
		return $instance;
	}

	/**
	 * Displays the widget settings controls on the widget panel.
	 * Make use of the get_field_id() and get_field_name() function
	 * when creating your form elements. This handles the confusing stuff.
	 */
	function form( $instance ) {

		/* Set up some default widget settings. */
		$defaults = array( 'title' => __('Latest Uploads', 'latest-uploads'), 'text' => __('[latest_uploaded_torrents latestuploads="latest_uploads_template"] [/latest_uploaded_torrents]') );
		$instance = wp_parse_args( (array) $instance, $defaults ); 
		?>

		<!-- Widget Title: Text Input -->
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title:', 'latest-uploaded'); ?></label>
			<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" style="width:100%;" />
			<textarea rows="10" cols="30" id="<?php echo $this->get_field_id( 'text' ); ?>" name="<?php echo $this->get_field_name( 'text' ); ?>" style="width:100%;"><?php echo $instance['text']; ?></textarea>
		</p>

	<?php
	}
}
function latest_uploads() {
	register_widget( 'Latest_Uploads' );
}
add_action( 'widgets_init', 'latest_uploads' );

endif; // Class exists
?>