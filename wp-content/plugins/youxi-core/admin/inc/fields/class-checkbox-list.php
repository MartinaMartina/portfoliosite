<?php if ( ! defined( 'ABSPATH' ) ) {
	die( 'Hi there!  I\'m just a plugin, not much I can do when called directly.' );
}

/**
 * Youxi Checkbox List Class
 *
 * This class renders a list of checkboxes.
 *
 * @package   Youxi Core
 * @author    Mairel Theafila <maimairel@gmail.com>
 * @copyright Copyright (c) 2013-2016, Mairel Theafila
 */
if( ! class_exists( 'Youxi_Checkbox_List_Form_Field' ) ) :

	class Youxi_Checkbox_List_Form_Field extends Youxi_Form_Field {

		/**
		 * Constructor.
		 */
		public function __construct( $scope, $options, $allowed_hooks = array() ) {
			// Merge default options
			$this->default_options = array_merge( $this->default_options, array(
				'inline'      => true, 
				'uncheckable' => false, 
				'choices'     => array(), 
				'std'         => array()
			));

			parent::__construct( $scope, $options, $allowed_hooks );
		}

		/**
		 * Apply form item attributes filtering
		 * 
		 * @param array The current attributes of the field
		 *
		 * @return array The filtered attributes of the field
		 */
		public function filter_field_attr( $attr ) {
			$class = 'youxi-form-list';
			if( $this->get_option( 'inline' ) ) {
				$class .= ' inline';
			}

			if( isset( $attr['class'] ) ) {
				$attr['class'] = Youxi_Form::normalize_class( $class, $attr['class'] );
			} else {
				$attr['class'] = Youxi_Form::normalize_class( $class );
			}
			
			return parent::filter_field_attr( $attr );
		}

		/**
		 * Sanitize the user submitted data
		 * 
		 * @param mixed The data to sanitize
		 *
		 * @return mixed The sanitized data
		 */
		public function sanitize( $data ) {

			// Make sure the data is an array
			if( ! is_array( $data ) || empty( $data ) ) {
				$data = array();
			}
			
			// If uncheckable, allow only booleans
			if( $this->get_option( 'uncheckable' ) ) {
				return array_map( 'wp_validate_boolean', $data );
			}

			// else only allow valid values
			$valid_values = array_keys( $this->get_option( 'choices' ) );
			return array_intersect( $data, $valid_values );
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

			// Make sure the current value is an array
			if( is_scalar( $value ) ) {
				$value = array( $value );
			}
			if( ! is_array( $value ) || empty( $value ) ) {
				$value = array();
			}

			$o = '<ul id="' . $this->get_the_ID() . '"' . Youxi_Form::render_attr( $attributes ) . '>';

				foreach( $this->get_option( 'choices' ) as $key => $choice ):

					$o .= '<li>';

						$o .= '<label>';

							if( $this->get_option( 'uncheckable' ) ):
								$o .= '<input type="hidden" name="' . $this->get_the_name() . esc_attr( "[$key]" ) . '" value="0">';
								$o .= '<input type="checkbox" name="' . $this->get_the_name() . esc_attr( "[$key]" ) . '" value="1"' . checked( isset( $value[ $key ] ) ? $value[ $key ] : false, true, false ) . '>';
							else:
								$o .= '<input type="checkbox" name="' . $this->get_the_name() . '[]" value="' . esc_attr( $key ) . '"' . checked( in_array( $key, $value ), true, false ) . '>';
							endif;

							$o .= ' ' . esc_html( $choice );

						$o .= '</label>';

					$o .= '</li>';

				endforeach;

			$o .= '</ul>';

			return $o;
		}
	}
endif;
