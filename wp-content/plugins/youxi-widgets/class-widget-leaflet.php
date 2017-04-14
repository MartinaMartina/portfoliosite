<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( 'Hi there!  I\'m just a plugin, not much I can do when called directly.' );
}

class Youxi_Leaflet_Map_Widget extends Youxi_WP_Widget {

	public function __construct() {

		$widget_opts  = array( 'classname' => 'youxi-leaflet-map-widget', 'description' => esc_html__( 'Use this widget to display a Leaflet Map.', 'youxi' ) );
		$control_opts = array( 'width' => '400px' );

		// Initialize WP_Widget
		parent::__construct( 'leaflet-map-widget', esc_html__( 'Youxi &raquo; Leaflet Map', 'youxi' ), $widget_opts, $control_opts );
	}

	public function widget( $args, $instance ) {

		extract( $args, EXTR_SKIP );

		$instance = wp_parse_args( (array) $instance, array(
			'title'        => '', 
			'center_lat'   => 0.0, 
			'center_lng'   => 0.0, 
			'zoom'         => 15, 
			'controls'     => array(), 
			'aspect_ratio' => array( 'w' => 16, 'h' => 9 ), 
			'markers'      => array(), 
			'access_token' => apply_filters( 'youxi_widgets_mapbox_access_token', '' )
		));

		$instance = apply_filters( "youxi_widgets_{$this->id_base}_instance", $instance, $this->id );

		echo $before_widget;

		if( isset( $instance['title'] ) && ! empty( $instance['title'] ) )
			echo $before_title . apply_filters( 'widget_title', $instance['title'] ) . $after_title;

		$this->maybe_load_template( $id, $instance );

		echo $after_widget;
	}

	public function form( $instance ) {

		$vars = wp_parse_args( (array) $instance, array(
			'title'        => '', 
			'center_lat'   => 0.0, 
			'center_lng'   => 0.0, 
			'zoom'         => 15, 
			'controls'     => array(), 
			'aspect_ratio' => array( 'w' => 4, 'h' => 3 ), 
			'markers'      => array()
		));

		$available_controls = array(
			'zoom'        => esc_html__( 'Zoom', 'youxi' ), 
			'attribution' => esc_html__( 'Attribution', 'youxi' ), 
			'scale'       => esc_html__( 'Scale', 'youxi' )
		);

		extract( $vars, EXTR_PREFIX_ALL, 'leaflet' );

		?><p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php _e( 'Title', 'youxi' ); ?>:</label> 
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $leaflet_title ); ?>">
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'center_lat' ) ); ?>"><?php _e( 'Center Latitude', 'youxi' ); ?>:</label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'center_lat' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'center_lat' ) ); ?>" type="text" value="<?php echo esc_attr( $leaflet_center_lat ); ?>">
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'center_lng' ) ); ?>"><?php _e( 'Center Longitude', 'youxi' ); ?>:</label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'center_lng' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'center_lng' ) ); ?>" type="text" value="<?php echo esc_attr( $leaflet_center_lng ); ?>">
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'zoom' ) ); ?>"><?php _e( 'Zoom', 'youxi' ); ?>:</label>
			<input type="number" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'zoom' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'zoom' ) ); ?>" min="0" max="20" value="<?php echo esc_attr( $leaflet_zoom ); ?>">
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'aspect_ratio' ) ); ?>"><?php _e( 'Aspect Ratio (Width : Height)', 'youxi' ); ?>:</label><br>
			<input name="<?php echo esc_attr( $this->get_field_name( 'aspect_ratio' ) ); ?>[w]" type="text" value="<?php echo esc_attr( $leaflet_aspect_ratio['w'] ); ?>"> : 
			<input name="<?php echo esc_attr( $this->get_field_name( 'aspect_ratio' ) ); ?>[h]" type="text" value="<?php echo esc_attr( $leaflet_aspect_ratio['h'] ); ?>">
		</p>
		<p>
			<?php foreach( $available_controls as $id => $name ): ?>
			<input type="checkbox" class="checkbox" id="<?php echo esc_attr( $this->get_field_id( 'controls[' . $id . ']' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'controls[]' ) ); ?>" <?php checked( in_array( $id, $leaflet_controls ), true ) ?> value="<?php echo esc_attr( $id ); ?>">
			<label for="<?php echo esc_attr( $this->get_field_id( 'controls[' . $id . ']' ) ); ?>"><?php printf( esc_html__( 'Display %s Control', 'youxi' ), $name ) ?></label><br>
			<?php endforeach; ?>
		</p>
		<div class="youxi-repeater" data-tmpl="<?php echo esc_attr( $this->id ) ?>">
			<script id="tmpl-<?php echo esc_attr( $this->id ) ?>" type="text/html"><?php echo $this->get_template(); ?></script>
			<label for="<?php echo esc_attr( $this->get_field_id( 'markers' ) ); ?>"><?php _e( 'Markers', 'youxi' ); ?>:</label>
			<div class="youxi-repeater-items-wrap">

				<?php if( is_array( $leaflet_markers ) ) : ?>

					<?php foreach( $leaflet_markers as $index => $marker ) : ?>

						<?php echo $this->get_template( $index, $marker ); ?>

					<?php endforeach; ?>

				<?php endif; ?>

			</div>
			<button type="button" class="button button-small button-repeater-add"><?php echo _e( 'Add Marker', 'youxi' ) ?></button>
		</div>
		<?php
	}

	public function update( $new_instance, $old_instance ) {

		foreach( $new_instance['markers'] as &$marker ) {
			$marker['lat']  = floatval( $marker['lat'] );
			$marker['lng']  = floatval( $marker['lng'] );
			$marker['description'] = sanitize_text_field( $marker['description'] );
		}

		/* Aspect Ratio */
		$new_instance['aspect_ratio']['w'] = floatval( $new_instance['aspect_ratio']['w'] );
		$new_instance['aspect_ratio']['h'] = floatval( $new_instance['aspect_ratio']['h'] );

		/* Controls */
		$new_instance['controls'] = array_intersect( (array) $new_instance['controls'], array( 'zoom', 'attribution', 'scale' ) );

		$instance = array(
			'title'        => strip_tags( $new_instance['title'] ), 
			'center_lat'   => floatval( $new_instance['center_lat'] ), 
			'center_lng'   => floatval( $new_instance['center_lng'] ), 
			'zoom'         => absint( $new_instance['zoom'] ), 
			'controls'     => array_values( $new_instance['controls'] ), 
			'aspect_ratio' => $new_instance['aspect_ratio'], 
			'markers'      => array_values( $new_instance['markers'] )
		);

		return apply_filters( "youxi_widgets_{$this->id_base}_new_instance", $instance, $this->id );
	}

	public function enqueue() {

		if( parent::enqueue() ) {

			$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

			if( ! wp_script_is( 'leaflet' ) ) {
				wp_enqueue_script( 'leaflet', self::frontend_plugins_url( "leaflet/leaflet.js" ), array(), '0.7.7', true );
			}
			if( ! wp_style_is( 'leaflet' ) ) {
				wp_enqueue_style( 'leaflet', self::frontend_plugins_url( "leaflet/leaflet.css" ), array(), '0.7.7', 'screen' );
			}
		}
	}

	protected function get_template( $index = '{{ data.index }}', $values = array() ) {

		$values = wp_parse_args( $values, array(
			'lat' => 0.0, 
			'lng' => 0.0, 
			'description' => ''
		));

		ob_start(); ?>
		<table class="widefat youxi-repeater-item">
			<tr>
				<td colspan="2">
					<p>
						<strong><?php _e( 'Marker', 'youxi' ) ?></strong>
						<span style="float: right;">
							<a href="#" class="button-repeater-remove">&times;</a>
						</span>
					</p>
				</td>
			</tr>
			<tr>
				<td>
					<p>
						<label for="<?php echo esc_attr( $this->get_field_id( "markers[$index][lat]" ) ) ?>"><?php _e( 'Latitude', 'youxi' ) ?>:</label>
						<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( "markers[$index][lat]" ) ) ?>" name="<?php echo esc_attr( $this->get_field_name( "markers[$index][lat]" ) ) ?>" type="text" value="<?php echo esc_attr( $values['lat'] ) ?>">
					</p>
				</td>
				<td>
					<p>
						<label for="<?php echo esc_attr( $this->get_field_id( "markers[$index][lat]" ) ) ?>"><?php _e( 'Longitude', 'youxi' ) ?>:</label>
						<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( "markers[$index][lng]" ) ) ?>" name="<?php echo esc_attr( $this->get_field_name( "markers[$index][lng]" ) ) ?>" type="text" value="<?php echo esc_attr( $values['lng'] ) ?>">
					</p>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<p>
						<label for="<?php echo esc_attr( $this->get_field_id( "markers[$index][description]" ) ) ?>"><?php _e( 'Text', 'youxi' ) ?>:</label>
						<textarea class="widefat" id="<?php echo esc_attr( $this->get_field_id( "markers[$index][description]" ) ) ?>" rows="4" name="<?php echo esc_attr( $this->get_field_name( "markers[$index][description]" ) ) ?>"><?php echo esc_textarea( $values['description'] ) ?></textarea>
					</p>
				</td>
			</tr>
		</table>
		<?php return ob_get_clean();
	}
}