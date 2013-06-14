<?php
/**
 * Adds ABZ_Twitter_Feed_Widget widget.
 */
class ABZ_Twitter_Feed_Widget extends WP_Widget {
	//public static $should_load_scripts = false;

	/**
	 * Register widget with WordPress.
	 */
	public function __construct() {
		parent::__construct(
	 		'abz_twitter_feed_widget', // Base ID
			__( 'ABZ_PLUGIN_NAME', 'abz_twitter_feed' ), // Name
			array( 'description' => __( 'ABZ_WIDGET_DESCRIPTION', 'abz_twitter_feed' ), ) // Args
		);
		
		if (is_active_widget( '', '', 'abz_twitter_feed_widget' )) {
			add_action( 'wp_enqueue_scripts', 'abz_twitter_feed_enqueue_scripts' );
		}
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
		//self::$should_load_scripts = true;
	
		extract( $args );
		$title = apply_filters( 'widget_title', $instance['title'] );
		$default_text = $instance['defaulttext'];

		echo $before_widget;
		if ( ! empty( $title ) )
			echo $before_title . $title . $after_title;
		echo '<div class="abz_twitter_feed"></div> ';
		echo $after_widget;
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['defaulttext'] =  $new_instance['defaulttext'] ;
        $instance['count'] =  $new_instance['count'] ;

		return $instance;
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		}
		else {
			$title = __( 'DEFAULT_TITLE', 'abz_twitter_feed' );
		}

		if ( isset( $instance[ 'defaulttext' ] ) ) {
			$default_text = $instance[ 'defaulttext' ];
		}
		else {
			$default_text = __( 'WIDGET_DEFAULT_TEXT', 'abz_twitter_feed' );
		}
                if ( isset( $instance[ 'count' ] ) ) {
                        $count = $instance[ 'count' ];
                }
                else {
                        $count = __( 6, 'abz_twitter_feed' );
                }
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'DEFAULT_TITLE_LABLE', 'abz_twitter_feed' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<p>
		<label for="<?php echo $this->get_field_id( 'defaulttext' ); ?>"><?php _e( 'WIDGET_DEFAULT_TEXT_LABEL', 'abz_twitter_feed' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'defaulttext' ); ?>" name="<?php echo $this->get_field_name( 'defaulttext' ); ?>" type="text" value="<?php echo esc_attr( $default_text ); ?>" />
		</p>
                <p>
                <label for="<?php echo $this->get_field_id( 'count' ); ?>"><?php _e( 'Number of Tweets to Show', 'abz_twitter_feed' ); ?></label>
                <input class="widefat" id="<?php echo $this->get_field_id( 'count' ); ?>" name="<?php echo $this->get_field_name( 'count' ); ?>" type="number" value="<?php echo esc_attr( $count ); ?>" min="1" />
                </p>
		<?php 
	}

} // class ABZ_Twitter_Feed_Widget

// register ABZ_Twitter_Feed_Widget widget
add_action( 'widgets_init', create_function( '', 'register_widget( "abz_twitter_feed_widget" );' ) );
