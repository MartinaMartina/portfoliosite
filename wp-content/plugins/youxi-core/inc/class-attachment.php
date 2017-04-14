<?php if ( ! defined( 'ABSPATH' ) ) {
	die( 'Hi there!  I\'m just a plugin, not much I can do when called directly.' );
}

/**
 * Youxi Attachment Class
 *
 * This class is a helper wrapper class for easily adding attachment fields.
 *
 * @package   Youxi Core
 * @author    Mairel Theafila <maimairel@gmail.com>
 * @copyright Copyright (c) 2013-2016, Mairel Theafila
 */
if( ! class_exists( 'Youxi_Attachment' ) ) :

	final class Youxi_Attachment {

		/**
		 * @access private
		 * @var object The singleton instance
		 */
		private static $_instance = null;

		/**
		 * @access private
		 * @var array The registered attachment fields
		 */
		private $fields = array();

		/**
		 * Default arg values
		 *
		 * @access private
		 * @var array
		 */
		private static $defaults = array(
			/* WP arguments */
			'input'         => 'text', 
			'label'         => '', 
			'html'          => '', 
			'required'      => false, 
			'value'         => '', 
			'helps'         => '', 
			'extra_rows'    => array(), 
			'show_in_edit'  => true, 
			'show_in_modal' => true, 

			/* Custom arguments */
			'restrict_to'   => false, 
			'choices'       => array(), 
			'min'           => null, 
			'max'           => null, 
			'step'          => null
		);

		/**
		 * Get the singleton instance
		 */
		public static function instance() {
			if( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}

		/**
		 * Constructor
		 */
		private function __construct() {
			add_filter( 'attachment_fields_to_edit', array( $this, 'edit' ), 10, 2 );
			add_filter( 'attachment_fields_to_save', array( $this, 'save' ), 10, 2 );
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

		/**
		 * Register attachment fields
		 */
		public function register( $id, $args = array() ) {
			$this->fields[ $id ] = $args;
			return $this;
		}

		/**
		 * Output the registered attachment fields
		 */
		public function edit( $form_fields, $attachment ) {

			foreach( $this->fields as $field_id => $field ) {

				$field = wp_parse_args( $field, self::$defaults );

				if( wp_validate_boolean( $field['restrict_to'] ) && 
					wp_attachment_is( $field['restrict_to'], $attachment->ID ) ) {

					$meta_value = get_post_meta( $attachment->ID, '_' . $field_id, true );

					if( in_array( $field['input'], array( 'radio', 'select', 'checkbox', 'url', 'number' ) ) ) {

						$required_attr = $field['required'] ? ' required' : '';
						$field_name    = 'attachments[' . $attachment->ID . '][' . $field_id . ']';

						$html = '';

						if( 'select' == $field['input'] ) {

							$html = '<select name="' . esc_attr( $field_name ) . '"' . $required_attr . '>';

							foreach( $field['choices'] as $value => $label ) {
								$html .= '<option value="' . esc_attr( $value ) . '" ' . selected( $value, $meta_value, false ) . '>' . esc_html( $label ) . '</option>';
							}

							$html .= '</select>';

						} elseif( 'radio' == $field['input'] ) {

							$i = 0;
							foreach( $field['choices'] as $value => $label ) {

								$html .= '<label>';
									$html .= '<input type="radio" name="' . esc_attr( $field_name ) . '" ' . checked( $value, $meta_value, false ) . '> ';
									$html .= esc_html( $label );
								$html .= '</label><br>';

							}

						} elseif( 'checkbox' == $field['input'] ) {

							$html = '<input type="hidden" name="' . esc_attr( $field_name ) . '" value="0">';
							$html .= '<label>';
								$html .= '<input type="checkbox" name="' . esc_attr( $field_name ) . '" ' . checked( true, wp_validate_boolean( $meta_value ), false ) . '>';
								$html .= esc_html( $field['label'] );
							$html .= '</label>';

						} elseif( 'url' == $field['input'] ) {

							$html = '<input type="url" name="' . esc_attr( $field_name ) . '" value="' . esc_attr( $meta_value ) . '"' . $required_attr . '>';

						} elseif( 'number' == $field['input'] ) {

							$max_attr  = isset( $field['max'] ) ? ' max="' . esc_attr( $field['max'] ) . '"' : '';
							$min_attr  = isset( $field['min'] ) ? ' min="' . esc_attr( $field['min'] ) . '"' : '';
							$step_attr = isset( $field['step'] ) ? ' step="' . esc_attr( $field['step'] ) . '"' : '';

							$html = '<input type="number" name="' . esc_attr( $field_name ) . '" value="' . esc_attr( $meta_value ) . '"' . 
								$max_attr . $min_attr . $step_attr . $required_attr . '>';
						}

						$field['input'] = 'html';
						$field['html']  = $html;

					} elseif( 'text' != $field['input'] && 'textarea' != $field['input'] ) {
						continue;
					}

					$field['value'] = $meta_value;
					$form_fields[ $field_id ] = $field;
				}
			}

			return $form_fields;
		}

		/**
		 * Save the registered attachment fields
		 */
		public function save( $attachment, $request ) {

			foreach( $this->fields as $field_id => $field ) {

				$field = wp_parse_args( $field, self::$defaults );

				if( wp_validate_boolean( $field['restrict_to'] ) && 
					wp_attachment_is( $field['restrict_to'], $attachment['ID'] ) ) {

					if( isset( $request[ $field_id ] ) ) {

						$old_value = get_post_meta( $attachment['ID'], "_$field_id", true );
						$new_value = $request[ $field_id ];

						$is_valid = $this->validate( $field, $new_value );

						if( ! is_wp_error( $is_valid ) && $old_value != $new_value ) {
							update_post_meta( $attachment['ID'], "_$field_id", $new_value );
						} elseif( is_wp_error( $is_valid ) ) {
							$attachment['errors'][ $field_id ]['errors'][] = $is_valid->get_error_message();
						}
						
					} else {
						delete_post_meta( $attachment['ID'], "_$field_id" );
					}
				}
			}

			return $attachment;
		}

		/**
		 * Validate an attachment field
		 */
		public function validate( $field, $value ) {

			$field = wp_parse_args( $field, self::$defaults );

			if( $field['required'] && 0 == strlen( $value ) ) {
				return new WP_Error( 'req', sprintf( esc_html__( '%s is required.', 'youxi' ), $field['label'] ) );
			}

			switch( $field['input'] ) {
				case 'number':
					$step = isset( $field['step'] ) && is_numeric( $field['step'] ) ? $field['step'] : 1;

					if( ! is_numeric( $value ) || 
						( isset( $field['min'] ) && is_numeric( $field['min'] ) && $value < $field['min'] ) || 
						( isset( $field['max'] ) && is_numeric( $field['max'] ) && $value > $field['max'] ) ||
						( abs( ( round( $value / $step ) * $step ) - $value ) > 0.00000001 ) ) {
						return new WP_Error( 'num', sprintf( esc_html__( '%s is an invalid number.', 'youxi' ), $field['label'] ) );
					}
					break;
				case 'url':
					if( ! empty( $value ) && false === filter_var( $value, FILTER_VALIDATE_URL ) ) {
						return new WP_Error( 'url', sprintf( esc_html__( '%s is an invalid URL.', 'youxi' ), $field['label'] ) );
					}
					break;
			}

			return true;
		}
	}
endif;
