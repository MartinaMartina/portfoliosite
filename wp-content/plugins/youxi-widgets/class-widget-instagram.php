<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( 'Hi there!  I\'m just a plugin, not much I can do when called directly.' );
}

class Youxi_Instagram_Widget extends Youxi_WP_Widget {

	private static $ajax_hook_registered = false;

	public function __construct() {

		$widget_opts  = array( 'classname' => 'youxi-instagram-widget', 'description' => __( 'Use this widget to display your Instagram feed.', 'youxi' ) );
		$control_opts = array();

		// Initialize WP_Widget
		parent::__construct( 'instagram-widget', __( 'Youxi &raquo; Instagram', 'youxi' ), $widget_opts, $control_opts );

		if( ! self::$ajax_hook_registered ) {

			$ajax_action = apply_filters( 'youxi_widgets_instagram_ajax_action', 'youxi_widgets_get_instagram_feed' );

			if( ! has_action( "wp_ajax_{$ajax_action}"  ) ) {
				add_action( "wp_ajax_{$ajax_action}", array( 'Youxi_Instagram_Widget', 'get_feed' ) );
			}
			if( ! has_action( "wp_ajax_nopriv_{$ajax_action}" ) ) {
				add_action( "wp_ajax_nopriv_{$ajax_action}", array( 'Youxi_Instagram_Widget', 'get_feed' ) );
			}

			self::$ajax_hook_registered = true;
		}
	}


	public function widget( $args, $instance ) {
		
		extract( $args, EXTR_SKIP );

		$instance = wp_parse_args( (array) $instance, array(
			'title'    => '', 
			'username' => '', 
			'count'    => 8
		) );

		$instance = apply_filters( "youxi_widgets_{$this->id_base}_instance", $instance, $this->id );

		echo $before_widget;

		if( isset( $instance['title'] ) && ! empty( $instance['title'] ) )
			echo $before_title . apply_filters( 'widget_title', $instance['title'] ) . $after_title;

		$this->maybe_load_template( $id, $instance );

		echo $after_widget;
	}

	public function form( $instance ) {

		$vars = wp_parse_args( (array) $instance, array(
			'title' => __( 'My Instagram Feed', 'youxi' ), 
			'count' => 8
		));

		extract( $vars );

		?><p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php _e( 'Title', 'youxi' ); ?>:</label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'count' ) ); ?>"><?php _e( 'Maximum Photos', 'youxi' ); ?>:</label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'count' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'count' ) ); ?>" type="number" min="1" value="<?php echo esc_attr( $count ); ?>">
		</p>
		<?php 
	}

	public function update( $new_instance, $old_instance ) {

		$instance = array();

		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['count'] = absint( strip_tags( $new_instance['count'] ) );

		return apply_filters( "youxi_widgets_{$this->id_base}_new_instance", $instance, $this->id );
	}

	public function get_defaults() {

		$widget_name = preg_replace( array( '/\W/', '/_?widget_?/' ), '', $this->id_base );
				
		return apply_filters( "youxi_widgets_{$widget_name}_defaults", array(
			'ajaxAction' => apply_filters( 'youxi_widgets_instagram_ajax_action', 'youxi_widgets_get_instagram_feed' )
		));
	}

	public static function get_feed() {

		if( isset( $_REQUEST['instagram'] ) ) {

			if( ! class_exists( 'Youxi_Instagram' ) ) {
				require( YOUXI_WIDGETS_DIR . 'api/instagram/class-youxi-instagram.php' );
			}

			$access_token = apply_filters( 'youxi_widgets_instagram_access_token', '' );

			$request  = wp_parse_args( $_REQUEST['instagram'], array( 'count' => 8 ) );
			$response = Youxi_Instagram::get( $request['count'], $access_token );

			if( is_wp_error( $response ) ) {

				wp_send_json_error( array(
					'error_code'    => $response->get_error_code(), 
					'error_message' => $response->get_error_message(), 
					'error_data'    => $response->get_error_data()
				));
			}

			wp_send_json_success( $response );
		}

		wp_send_json_error();
	}
}