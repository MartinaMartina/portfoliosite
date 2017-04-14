<?php if ( ! defined( 'ABSPATH' ) ) {
	die( 'Cheatin&#8217; uh?' );
}

final class Youxi_Templates {

	private $templates_directory;

	private $datastack = array();

	public function __construct() {
		$this->templates_directory = trailingslashit( apply_filters( 'youxi_templates_directory', 'templates' ) );
	}

	public function get( $slug, $name = null, $paths = array(), $userdata = array() ) {

		/*
			Push userdata to the internal stack array.
			Variables can be used inside the requsted template using Youxi()->templates->get_var();
		*/
		if( ! is_array( $userdata ) ) {
			$userdata = array();
		}
		$this->datastack[] = $userdata;		

		/* Start contstructing template paths */
		$templates = array();

		$paths = (array) $paths;
		$name = (string) $name;

		/* Subfolders */
		for( ; $paths; array_pop( $paths ) ) {

			$path = implode( '/', $paths );
			$path = trailingslashit( $this->templates_directory . $path );

			if( '' !== $name ) {
				$templates[] = $path . "{$slug}-{$name}.php";
			}
			$templates[] = $path . "{$slug}.php";
		}

		if( '' !== $name ) {
			$templates[] = $this->templates_directory . "{$slug}-{$name}.php";
		}
		$templates[] = $this->templates_directory . "{$slug}.php";

		/* Locate template */
		locate_template( $templates, true, false );

		/* Pop userdata from the internal stack array */
		array_pop( $this->datastack );
	}

	public function get_var( $key, $default = '' ) {

		$current = end( $this->datastack );

		if( is_array( $current ) && isset( $current[ $key ] ) ) {
			return $current[ $key ];
		}

		return $default;
	}
}
