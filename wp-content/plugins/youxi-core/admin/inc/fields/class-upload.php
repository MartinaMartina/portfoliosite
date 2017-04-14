<?php if ( ! defined( 'ABSPATH' ) ) {
	die( 'Hi there!  I\'m just a plugin, not much I can do when called directly.' );
}

/**
 * Youxi File Uploader Class
 *
 * This class creates a file uploader using WordPress 3.5 media uploader.
 *
 * @package   Youxi Core
 * @author    Mairel Theafila <maimairel@gmail.com>
 * @copyright Copyright (c) 2013-2016, Mairel Theafila
 */
if( ! class_exists( 'Youxi_Upload_Form_Field' ) ) :

	class Youxi_Upload_Form_Field extends Youxi_Form_Field {

		/**
		 * Constructor
		 */
		public function __construct( $scope, $options, $allowed_hooks = array() ) {
			// Merge default options
			$this->default_options = array_merge( $this->default_options, array(
				'frame_title'     => esc_html__( 'Choose File', 'youxi' ), 
				'frame_btn_text'  => esc_html__( 'Insert File', 'youxi' ), 
				'upload_btn_text' => esc_html__( 'Select File', 'youxi' ), 
				'remove_btn_text' => esc_html__( 'Remove File', 'youxi' ), 
				'return_type'     => 'id', 
				'enable_embed'    => true, 
				'library_type'    => ''
			));

			parent::__construct( $scope, $options, $allowed_hooks );
		}

		/**
		 * Enqueue Required Assets
		 */
		public function enqueue( $hook ) {

			if( parent::enqueue( $hook ) ) {

				$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

				wp_enqueue_media();
				wp_enqueue_script( 'youxi-file-uploader', self::field_assets_url( "js/youxi.form.upload{$suffix}.js" ), array( 'youxi-form-manager', 'jquery-ui-sortable', 'media-views' ), YOUXI_CORE_VERSION, true );
			}
		}

		/**
		 * Sanitize the user submitted data
		 * 
		 * @param mixed The data to sanitize
		 *
		 * @return mixed The sanitized data
		 */
		public function sanitize( $data ) {
			return 'attachment' == get_post_type( $data ) ? $data : '';
		}

		/**
		 * Helper method to filter an array of attributes before using it on a field
		 * 
		 * @param mixed The attributes to filter
		 *
		 * @return mixed The filtered attributes
		 */
		public function filter_field_attr( $attr ) {
			
			return array_merge( $attr, array(
				'class'                  => esc_attr( 'youxi-file-uploader' ), 
				'data-field-name'        => $this->get_the_name(), 
				'data-title'             => esc_attr( $this->get_option( 'frame_title' ) ), 
				'data-button-text'       => esc_attr( $this->get_option( 'frame_btn_text' ) ), 
				'data-return-type'       => esc_attr( $this->get_option( 'return_type' ) ), 
				'data-enable-embed'      => esc_attr( $this->get_option( 'enable_embed' ) ), 
				'data-library-type'      => esc_attr( $this->get_option( 'library_type' ) )
			));
		}

		/**
		 * Get the field's HTML markup
		 *
		 * @param mixed The field's current value (if it exists)
		 * @param array The HTML attributes to be added on the field
		 *
		 * @return string The field's HTML markup
		 */
		public function get_the_field( $value, $attributes = array() ) {

			if( 'attachment' == get_post_type( $value ) ) {
				$attachment_url = wp_get_attachment_url( $value );
			} else {
				$value = $attachment_url = '';
			}

			$o = '<div id="' . $this->get_the_ID() . '"' . Youxi_Form::render_attr( $attributes ) . '>';

				$o .= '<input type="hidden" class="youxi-uploader-value" name="' . $this->get_the_name() . '" value="' . esc_attr( $value ) . '">';
				$o .= '<input type="text" class="youxi-uploader-feedback" readonly value="' . esc_attr( $attachment_url ) . '"> ';
				
				$o .= '<button type="button" class="button button-primary youxi-uploader-button">' . esc_html( $this->get_option( 'upload_btn_text' ) ) . '</button> ';
				$o .= '<button type="button" class="button youxi-uploader-remove">' . esc_html( $this->get_option( 'remove_btn_text' ) ) . '</button>';

			$o .= '</div>';

			return $o;
		}
	}
endif;
