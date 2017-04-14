<?php if ( ! defined( 'ABSPATH' ) ) {
	die( 'Hi there!  I\'m just a plugin, not much I can do when called directly.' );
}

/**
 * Youxi Postselect Class
 *
 * This class renders a drag and drop posts dropdown list.
 *
 * @package   Youxi Core
 * @author    Mairel Theafila <maimairel@gmail.com>
 * @copyright Copyright (c) 2013-2016, Mairel Theafila
 */
if( ! class_exists( 'Youxi_Postselect_Form_Field' ) ) :

	if( ! class_exists( 'Youxi_Multiselect_Form_Field' ) )
		require 'class-multiselect.php';

	class Youxi_Postselect_Form_Field extends Youxi_Multiselect_Form_Field {

		/**
		 * Constructor.
		 */
		public function __construct( $scope, $options, $allowed_hooks = array() ) {

			// Merge default options
			$this->default_options = array_merge( $this->default_options, array(
				'post_type' => 'post'
			));

			parent::__construct( $scope, $options, $allowed_hooks );

			$posts = get_posts( array(
				'post_type'        => $this->get_option( 'post_type' ), 
				'posts_per_page'   => -1, 
				'suppress_filters' => false
			));

			$this->set_option( 'choices', wp_list_pluck( $posts, 'post_title', 'ID' ) );
		}
	}
endif;
