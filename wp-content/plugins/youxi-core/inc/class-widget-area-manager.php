<?php if ( ! defined( 'ABSPATH' ) ) {
	die( 'Hi there!  I\'m just a plugin, not much I can do when called directly.' );
}

/**
 * Youxi Widget Area classes
 *
 * These are classes used for adding custom widget areas
 *
 * @package   Youxi Core
 * @author    Mairel Theafila <maimairel@gmail.com>
 * @copyright Copyright (c) 2013-2016, Mairel Theafila
 */
if( ! class_exists( 'Youxi_Widget_Area' ) ) :

	final class Youxi_Widget_Area {

		public static function insert( $data ) {

			if( empty( $data['name'] ) ) {
				return new WP_Error( 'widget-area-invalid', esc_html__( 'The widget area name is required', 'youxi' ) );
			}

			$widget_area_name        = sanitize_text_field( $data['name'] );
			$widget_area_id          = sanitize_title( $widget_area_name );
			$widget_area_description = empty( $data['description'] ) ? '' : (string) $data['description'];

			$widget_areas = get_option( self::option_key(), array() );

			/* Attempt to create unique IDs when it already exists */
			for( $counter = 2; array_key_exists( $widget_area_id, $widget_areas ); $counter++ ) {
				$widget_area_id = sprintf( '%s-%d', sanitize_title( $widget_area_name ), $counter );
			}

			$widget_areas[ $widget_area_id ] = array(
				'id'          => $widget_area_id, 
				'name'        => $widget_area_name, 
				'description' => $widget_area_description
			);

			return update_option( self::option_key(), $widget_areas );
		}

		public static function get( $per_page, $page_number ) {
			$widget_areas = get_option( self::option_key(), array() );
			$widget_areas = self::sort( $widget_areas );
			return array_slice( $widget_areas, ( $page_number - 1 ) * $per_page, $per_page );
		}

		public static function delete( $id ) {
			$widget_areas = get_option( self::option_key(), array() );

			if( isset( $widget_areas[ $id ] ) ) {
				unset( $widget_areas[ $id ] );
				update_option( self::option_key(), $widget_areas );
			}
		}

		public static function option_key() {
			return apply_filters( 'youxi_widget_area_option_key', '_youxi_widget_areas' );
		}

		public static function count() {
			$widget_areas = get_option( self::option_key(), array() );
			return count( $widget_areas );
		}

		public static function _cmp_name_asc( $a, $b ) {
			return strcmp( $a['name'], $b['name'] );
		}

		public static function _cmp_name_desc( $a, $b ) {
			return strcmp( $b['name'], $a['name'] );
		}

		public static function sort( $widget_areas ) {

			$order = 'asc';
			if( isset( $_REQUEST['orderby'], $_REQUEST['order'] ) ) {
				$order = strtolower( $_REQUEST['order'] );
			}

			if( in_array( $order, array( 'asc', 'desc' ) ) ) {
				usort( $widget_areas, array( 'self', '_cmp_name_' . $order ) );
			}

			return $widget_areas;
		}
	}
endif;

if( ! class_exists( 'Youxi_Widget_Area_Management_Page' ) ) :

	final class Youxi_Widget_Area_Management_Page {

		private static $_instance = null;

		private $widget_area_list_table;

		private function __construct() {
			add_filter( 'set-screen-option', array( $this, 'set_screen' ), 10, 3 );
			add_action( 'admin_menu', array( $this, 'add_theme_page' ) );
		}

		/**
		 * You cannot clone this class.
		 */
		public function __clone() {
			_doing_it_wrong( __FUNCTION__, esc_html__( 'Cheatin&#8217; huh?', 'youxi' ), '1.0.0' );
		}

		/**
		 * You cannot unserialize instances of this class.
		 */
		public function __wakeup() {
			_doing_it_wrong( __FUNCTION__, esc_html__( 'Cheatin&#8217; huh?', 'youxi' ), '1.0.0' );
		}

		public static function instance() {
			if( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}

		public function set_screen( $status, $option, $value ) {
			return $value;
		}

		public function add_theme_page() {

			$hook = add_theme_page(
				esc_html__( 'Widget Areas', 'youxi' ), 
				esc_html__( 'Widget Areas', 'youxi' ), 
				'edit_theme_options', 
				'youxi-widget-area-manager', 
				array( $this, 'display' )
			);

			add_action( 'load-' . $hook, array( $this, 'load' ) );
		}

		private function action( $action ) {

			switch( $action ) :

				case 'add-widget-area':
					check_admin_referer( 'add-widget-area', '_wpnonce_add-widget-area' );
					Youxi_Widget_Area::insert( $_POST );
					break;

				case 'bulk-delete':
					check_admin_referer( 'bulk-widget-areas' );
					$widget_areas = (array) $_REQUEST['widget_areas'];
					foreach( $widget_areas as $widget_area_id ) {
						Youxi_Widget_Area::delete( $widget_area_id );
					}
					break;

				case 'delete':
					if( ! empty( $_REQUEST['widget_area_id'] ) ) {
						check_admin_referer( 'delete-widget-area_' . $_REQUEST['widget_area_id'] );
						Youxi_Widget_Area::delete( $_REQUEST['widget_area_id'] );
					}
					$redirect_url = remove_query_arg( array( '_wp_http_referer', '_wpnonce', 'action', 'widget_area_id' ) );
					break;

			endswitch;

			if( isset( $redirect_url ) ) {
				wp_redirect( $redirect_url );
				exit;
			}
		}

		public function load() {

			/* Create an instance of the widget area list table */
			$this->widget_area_list_table = new Youxi_Widget_Area_List_Table();

			/* Process actions */
			$this->action( $this->widget_area_list_table->current_action() );

			/* Prepare current items */
			$this->widget_area_list_table->prepare_items();

			/* Add per page screen option */
			add_screen_option( 'per_page', array(
				'default' => 10, 
				'option' => 'youxi_widget_areas_per_page'
			));
		}

		public function display() {

			?>
			<div class="wrap">

				<h2><?php echo get_admin_page_title(); ?></h2>

				<div id="col-container" class="wp-clearfix">

					<div id="col-left">

						<div class="col-wrap">

							<div class="form-wrap">

								<h2><?php esc_html_e( 'Add New Widget Area', 'youxi' ); ?></h2>

								<form method="post">

									<input type="hidden" name="action" value="add-widget-area">
									<?php wp_nonce_field( 'add-widget-area', '_wpnonce_add-widget-area' ); ?>
									
									<div class="form-field form-required widget-area-name-wrap">
										<label for="widget-area-name">Name</label>
										<input name="name" id="widget-area-name" type="text" value="" size="40" aria-required="true">
										<p><?php esc_html_e( 'The name of the widget area.', 'youxi' ); ?></p>
									</div>

									<div class="form-field widget-area-description-wrap">
										<label for="widget-area-description">Description</label>
										<textarea name="description" id="widget-area-description" rows="5" cols="40"></textarea>
										<p><?php esc_html_e( 'The description of the widget area.', 'youxi' ); ?></p>
									</div>

									<?php submit_button( esc_html__( 'Add New Widget Area', 'youxi' ) ); ?>

								</form>

							</div>

						</div>

					</div><!-- /col-left -->

					<div id="col-right">

						<div class="col-wrap">

							<form method="post">
								<?php $this->widget_area_list_table->display(); ?>
							</form>

						</div>

					</div><!-- /col-right -->

				</div>

			</div>
			<?php
		}
	}
endif;

if( ! function_exists( 'youxi_widget_area_init' ) ) :

function youxi_widget_area_init() {

	if( apply_filters( 'youxi_widget_area_enabled', true ) ) {
		Youxi_Widget_Area_Management_Page::instance();
	}
}
endif;
add_action( 'plugins_loaded', 'youxi_widget_area_init' );
