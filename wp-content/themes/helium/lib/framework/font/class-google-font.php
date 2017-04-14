<?php if ( ! defined( 'ABSPATH' ) ) {
	die( 'Cheatin&#8217; uh?' );
}

/**
 * Youxi Google Font
 *
 * This class provides helper methods to ease working with Google Fonts
 *
 * @package   Youxi Themes Theme Utils
 * @author    Mairel Theafila <maimairel@gmail.com>
 * @copyright Copyright (c) 2014-2016, Mairel Theafila
 */

final class Youxi_Google_Font {

	protected static $cached_fonts;

	/**
	 * Parse a single variant Google Font string using regular expression: Roboto:400&subset=latin,latin-ext
	 */
	public static function parse_regex( $str ) {

		static $cache = array();

		if( is_string( $str ) ) {

			if( isset( $cache[ $str ] ) ) {
				return $cache[ $str ];
			}

			$matches = array();
			$pattern = '/^([a-zA-Z\d+ ]+)(?::([1-9]00|(?:(?:[1-3]|[5-9])00)?italic|regular))?(?:&subset=((?:[a-zA-Z-]+,)*(?:[a-zA-Z-]+)))?$/';

			if( preg_match( $pattern, $str, $matches ) ) {

				if( isset( $matches[1] ) && ( $family = $matches[1] ) ) {

					if( self::is_valid_family( $family ) ) {

						if( isset( $matches[2] ) ) {
							$variant = $matches[2];
						} else {
							$variant = '';
						}

						$variant = self::sanitize_variant( $family, $variant );
						$weight  = self::variant2weight( $variant );
						$style   = self::variant2style( $variant );

						if( isset( $matches[3] ) ) {
							$subsets = array_filter( explode( ',', $matches[3] ) );
						} else {
							$subsets = array();
						}

						return ( $cache[ $str ] = compact( 'family', 'variant', 'weight', 'style', 'subsets' ) );
					}
				}
			}
		}

		return null;
	}

	/**
	 * Parse a Google Font string using string functions: Roboto:400&subset=latin,latin-ext
	 */
	public static function parse_str( $str ) {

		static $cache = array();

		if( is_string( $str ) ) {

			if( isset( $cache[ $str ] ) ) {
				return $cache[ $str ];
			}

			// Split by subset first
			$pair = explode( '&subset=', $str );
			$len  = count( $pair );

			// We have subsets
			if( 2 == $len ) {
				$subsets = array_filter( explode( ',', $pair[1] ) );
				$str = $pair[0];
			} else {
				$subsets = array();
			}

			// Get family and variants
			$pair = explode( ':', $str );
			$len  = count( $pair );

			// We have family
			if( $len > 0 && ( $family = $pair[0] ) ) {

				if( self::is_valid_family( $family ) ) {

					// We have variant
					if( 2 == $len ) {
						$variant = $pair[1];
					} else {
						$variant = '';
					}

					$variant = self::sanitize_variant( $family, $variant );
					$weight  = self::variant2weight( $variant );
					$style   = self::variant2style( $variant );

					return ( $cache[ $str ] = compact( 'family', 'variant', 'weight', 'style', 'subsets' ) );
				}
			}
		}

		return null;
	}

	/**
	 * Convert Google Font string to CSS font family, font-style, font-weight
	 */
	public static function to_css( $str ) {

		if( is_string( $str ) && ( $font = self::parse_str( $str ) ) ) {
			return array(
				'font-family' => '"' . str_replace( '+', ' ', $font['family'] ) . '"', 
				'font-weight' => $font['weight'], 
				'font-style'  => $font['style']
			);
		}

		return null;
	}

	/**
	 * Build a Google Font request object from a set of 
	 * strings based on the supplied settings.
	 */
	public static function build_request( $args, $settings ) {

		$families = array();
		$subsets  = array();

		foreach( $args as $key => $str ) {

			if( $font = self::parse_str( $str ) ) {

				/* Create a new Google Font family */
				if( ! isset( $families[ $font['family'] ] ) ) {
					$families[ $font['family'] ] = array();
				}

				/* Get setting */
				$setting = isset( $settings[ $key ] ) ? $settings[ $key ] : array();

				/* Font inclusion settings */
				$include_all_styles = isset( $setting['include_all_styles'] ) && $setting['include_all_styles'];
				$additional_weights = isset( $setting['additional_weights'] ) ? $setting['additional_weights'] : array();

				/* Include all styles */
				if( $include_all_styles ) {
					$families[ $font['family'] ] = array_merge( $families[ $font['family'] ], 
						Youxi_Google_Font::weight_variants( $font['family'], self::variant2weight( $font['variant'] ) ) );
				} else {
					$families[ $font['family'] ][] = $font['variant'];
				}

				/* Include additional weights */
				if( is_array( $additional_weights ) ) {
					
					foreach( $additional_weights as $weight ) {
						$weight_set = Youxi_Google_Font::weight_variants( $font['family'], $weight );
						if( $include_all_styles ) {
							$families[ $font['family'] ] = array_merge( $families[ $font['family'] ], $weight_set );
						} else {
							foreach( $weight_set as $var ) {
								$var_style = self::variant2style( $var );
								if( $font['style'] == $var_style ) {
									$families[ $font['family'] ][] = $var;
								}
							}
						}
					}
				}

				/* Make sure the family doesn't contain duplicate values */
				$families[ $font['family'] ] = array_unique( $families[ $font['family'] ] );

				/* Google Font subsets */
				if( isset( $font['subsets'] ) && is_array( $font['subsets'] ) ) {
					$subsets = array_merge( $subsets, $font['subsets'] );
					$subsets = array_unique( $subsets );
				}
			}
		}

		return compact( 'families', 'subsets' );
	}

	/**
	 * Check if a given Google Font family name is valid
	 */
	public static function is_valid_family( $family ) {

		$google_fonts = self::get_fonts();
		$family = preg_replace( '/\s+/', '+', $family );

		return isset( $google_fonts[ $family ] );
	}

	/**
	 * Sanitize a Google Font variant so it matches the actual presentation, 
	 * or return an empty string if the variant does not exists.
	 */
	public static function sanitize_variant( $family, $variant ) {

		$style  = self::variant2style( $variant );
		$weight = self::variant2weight( $variant );
		
		foreach( self::get_variants( $family ) as $var ) {
			$var_weight = self::variant2weight( $var );
			$var_style  = self::variant2style( $var );

			/* Bingo */
			if( $var_weight == $weight && $var_style == $style ) {
				return $var;
			}
		}

		return '';
	}

	/**
	 * Convert a Google Font variant to font-style
	 */
	public static function variant2style( $variant ) {
		return preg_match( '/italic/', $variant ) ? 'italic' : 'normal';
	}

	/**
	 * Convert a Google Font variant to font-weight
	 */
	public static function variant2weight( $variant ) {
		return preg_replace( array( '/^$|^(regular|italic)$/', '/italic$/' ), array( '400' ), $variant );
	}

	/**
	 * Construct a Google Font request URL from a set of requested fonts
	 */
	public static function request_url( $pairs, $subsets = array() ) {

		if( ! empty( $pairs ) ) {
			
			$urls = array();
			foreach( $pairs as $family => $variants ) {
				$variants = array_unique( $variants );
				sort( $variants, SORT_STRING );
				$urls[] = implode( ':', array( $family, implode( ',', $variants ) ) );
			}

			$query_args = array( 'family' => implode( '|', $urls ) );
			if( is_array( $subsets ) && ! empty( $subsets ) ) {
				$query_args['subset'] = implode( ',', $subsets );
			}

			return esc_url_raw( add_query_arg( $query_args, 'https://fonts.googleapis.com/css' ) );
		}

		return '';
	}

	/**
	 * Get all font variants based on a weight
	 */
	public static function weight_variants( $family, $weight ) {

		$variants = array();
		
		foreach( self::get_variants( $family ) as $variant ) {
			$variant_weight = self::variant2weight( $variant );
			if( $weight == $variant_weight ) {
				$variants[] = $variant;
			}
		}

		return $variants;
	}

	/**
	 * Get all variants of a font family
	 */
	public static function get_variants( $family ) {

		$google_fonts = self::get_fonts();
		$family = preg_replace( '/\s+/', '+', $family );

		if( isset( $google_fonts[ $family ]['variants'] ) ) {
			return $google_fonts[ $family ]['variants'];
		}

		return array();
	}

	/**
	 * Get all subsets of a font family
	 */
	public static function get_subsets( $family ) {

		$google_fonts = self::get_fonts();
		$family = preg_replace( '/\s+/', '+', $family );

		if( isset( $google_fonts[ $family ]['subsets'] ) ) {
			return $google_fonts[ $family ]['subsets'];
		}

		return array();
	}
	
	/**
	 * Fetch Google Fonts from PHP file
	 */
	public static function get_fonts() {
		return include 'google-fonts-29102016.php';
	}
}