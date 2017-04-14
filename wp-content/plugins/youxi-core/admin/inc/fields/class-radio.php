<?php if ( ! defined( 'ABSPATH' ) ) {
	die( 'Hi there!  I\'m just a plugin, not much I can do when called directly.' );
}

/**
 * Youxi Radio Button Class
 *
 * This class renders a radio button.
 *
 * @package   Youxi Core
 * @author    Mairel Theafila <maimairel@gmail.com>
 * @copyright Copyright (c) 2013-2016, Mairel Theafila
 */
if( ! class_exists( 'Youxi_Radio_Form_Field' ) ) :

	class Youxi_Radio_Form_Field extends Youxi_Form_Field {

		/**
		 * Constructor
		 */
		public function __construct( $scope, $options, $allowed_hooks = array() ) {
			// Merge default options
			$this->default_options = array_merge( $this->default_options, array(
				'inline' => true
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
			$choices = $this->get_option( 'choices' );
			return isset( $choices[ $data ] ) ? $data : $this->get_option( 'std' );
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

			$o = '<ul id="' . $this->get_the_ID() . '"' . Youxi_Form::render_attr( $attributes ) . '>';

				foreach( $this->get_option( 'choices' ) as $val => $choice ):

					$o .= '<li>';

						$o .= '<label>';

							$o .= '<input type="radio" name="' . $this->get_the_name() . '" value="' . esc_attr( $val ) . '" ' . checked( $val, $value, false ) . '>';
							
							$o .= ' ' . esc_html( $choice );

						$o .= '</label>';

					$o .= '</li>';

				endforeach;

			$o .= '</ul>';

			return $o;
		}
	}
endif;
