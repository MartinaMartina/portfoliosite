<?php if ( ! defined( 'ABSPATH' ) ) {
	die( 'Cheatin&#8217; uh?' );
}

/**
 * Youxi SCSS Compiler
 *
 * This class provides the necessary methods to compile SCSS
 *
 * @package   Youxi Themes Theme Utils
 * @author    Mairel Theafila <maimairel@gmail.com>
 * @copyright Copyright (c) 2014-2016, Mairel Theafila
 */

require get_template_directory() . '/lib/vendor/scssphp/scss.inc.php';

use Leafo\ScssPhp\Compiler;

final class Youxi_SCSS_Compiler {

	private static $instance;

	private $import_paths;

	private function __construct() {
		$this->import_paths = array( '' );
	}

	public static function get() {

		if( ! is_a( self::$instance, get_class() ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	public function set_import_paths( $path ) {
		$this->import_paths = (array) $path;
	}

	private function cache_key() {
		return apply_filters( 'youxi_scss_cache_key', 'youxi_scss_cache' );
	}

	private function read_cache( $hash ) {

		/* Get the cache object from wp_options table */
		$scss_cache = get_option( $this->cache_key(), array() );

		/* Return the cache if valid */
		if( isset( $scss_cache[ $hash ]['hash'], $scss_cache[ $hash ]['css'] ) ) {
			
			if( $hash === $scss_cache[ $hash ]['hash'] ) {
				return $scss_cache[ $hash ]['css'];
			} else {
				unset( $scss_cache[ $hash ] );
				update_option( $this->cache_key(), $scss_cache );
			}
		}
	}

	private function save_cache( $css, $hash ) {

		/* Get the cache object from wp_options table */
		$scss_cache = get_option( $this->cache_key(), array() );

		$scss_cache[ $hash ] = compact( 'css', 'hash' );

		/* Update the cache */
		if( ! add_option( $this->cache_key(), $scss_cache, '', 'no' ) ) {
			update_option( $this->cache_key(), $scss_cache );
		}
	}

	public function compile( $file, $vars = array(), $formatter = 'Leafo\ScssPhp\Formatter\Crunched' ) {

		$scss_hash = md5( $file . serialize( $vars ) . $formatter );
		if( $output = $this->read_cache( $scss_hash ) ) {
			return $output;
		}

		$compiler = new Compiler();
		$compiler->setImportPaths( $this->import_paths );

		$compiler->setVariables( $vars );
		$compiler->setFormatter( $formatter );

		try {

			$output = $compiler->compile( "@import '$file.scss';" );

			if( ! empty( $output ) ) {

				$this->save_cache( $output, $scss_hash );
				return $output;
			}

		} catch( Exception $e ) {

			return new WP_Error( 'scss', $e->getMessage() );
		}
	}
}
