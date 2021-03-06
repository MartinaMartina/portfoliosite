<?php if ( ! defined( 'ABSPATH' ) ) {
	die( 'Hi there!  I\'m just a plugin, not much I can do when called directly.' );
}

/**
 * Youxi Admin Notice
 *
 * This class is a helper class for displaying multiple admin notices in a single admin notice box.
 *
 * @package   Youxi Core
 * @author    Mairel Theafila <maimairel@gmail.com>
 * @copyright Copyright (c) 2013-2016, Mairel Theafila
 */
if( ! class_exists( 'Youxi_Admin_Notice' ) ) :

	final class Youxi_Admin_Notice {

		private $errors = array();

		private $warnings = array();

		private static $_instance = null;

		public static function instance() {
			if( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}

		private function __construct() {
			add_action( 'admin_notices', array( $this, 'display_errors' ) );
			add_action( 'admin_notices', array( $this, 'display_warnings' ) );
		}

		public function __clone() {
			_doing_it_wrong( __FUNCTION__, esc_html__( 'Cheatin&#8217; huh?', 'youxi' ), '1.0.0' );
		}

		public function __wakeup() {
			_doing_it_wrong( __FUNCTION__, esc_html__( 'Cheatin&#8217; huh?', 'youxi' ), '1.0.0' );
		}

		public function add_error( $plugin, $message ) {
			if( ! empty( $message ) ) {
				if( ! isset( $this->errors[ $plugin ] ) ) {
					$this->errors[ $plugin ] = array();
				}
				$this->errors[ $plugin ][] = $message;
			}
		}

		public function add_warning( $plugin, $message ) {
			if( ! empty( $message ) ) {
				if( ! isset( $this->warnings[ $plugin ] ) ) {
					$this->warnings[ $plugin ] = array();
				}
				$this->warnings[ $plugin ][] = $message;
			}
		}

		public function display_errors() {
			if( ! empty( $this->errors ) ) {

				echo '<div class="error">';

					echo '<ul>';

					foreach( $this->errors as $file => $errors ):

						$data = get_plugin_data( $file );

						echo "<li><strong>{$data['Name']}</strong>";

							echo '<ul>';

								echo '<li>&raquo; ' . join( '</li><li>&raquo; ', $errors ) . '</li>';

							echo '</ul>';

						echo '</li>';

					endforeach;

					echo '</ul>';

					echo '<h3>' . sprintf( __( 'Lost? Open a support thread at Youxi Themes <a href="%s" target="_blank">support forum</a>.', 'youxi' ), 'http://support.youxithemes.com' ). '</h3>';

				echo '</div>';
			}
		}

		public function display_warnings() {
			if( ! empty( $this->warnings ) ) {

				echo '<div class="updated">';

					echo '<ul>';

					foreach( $this->warnings as $file => $warnings ):

						$data = get_plugin_data( $file );

						echo "<li><strong>{$data['Name']}</strong>";

							echo '<ul>';

								echo '<li>&raquo; ' . join( '</li><li>&raquo; ', $warnings ) . '</li>';

							echo '</ul>';

						echo '</li>';

					endforeach;

					echo '</ul>';

				echo '</div>';
			}
		}
	}
endif;
