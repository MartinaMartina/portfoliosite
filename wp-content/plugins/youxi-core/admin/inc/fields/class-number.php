<?php if ( ! defined( 'ABSPATH' ) ) {
	die( 'Hi there!  I\'m just a plugin, not much I can do when called directly.' );
}

/**
 * Youxi Number Field Class
 *
 * This class renders a HTML5 number input.
 *
 * @package   Youxi Core
 * @author    Mairel Theafila <maimairel@gmail.com>
 * @copyright Copyright (c) 2013-2016, Mairel Theafila
 */
if( ! class_exists( 'Youxi_Number_Form_Field' ) ) :

	class Youxi_Number_Form_Field extends Youxi_Form_Field {

		/**
		 * Constructor
		 */
		public function __construct( $scope, $options, $allowed_hooks = array() ) {
			// Merge default options
			$this->default_options = array_merge( $this->default_options, array(
				'min'  => null, 
				'max'  => null, 
				'step' => 1
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

			foreach( array( 'min', 'max', 'step' ) as $option ) {
				$val = $this->get_option( $option );
				if( is_numeric( $val ) ) {
					$attr[ $option ] = $val;
				}
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

			$min = $this->get_option( 'min' );
			$max = $this->get_option( 'max' );

			$data = floatval( $data );
			if( is_numeric( $min ) ) {
				$data = max( $data, $min );
			}
			if( is_numeric( $max ) ) {
				$data = min( $data, $max );
			}

			return $data;
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
			return '<input id="' . $this->get_the_ID() . '" name="' . $this->get_the_name() . '" type="number" value="' . esc_attr( $value ) . '"' . Youxi_Form::render_attr( $attributes ) . '>';
		}
	}
endif;
