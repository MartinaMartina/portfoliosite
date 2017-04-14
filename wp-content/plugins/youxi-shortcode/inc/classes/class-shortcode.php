<?php if ( ! defined( 'ABSPATH' ) ) {
	die( 'Hi there!  I\'m just a plugin, not much I can do when called directly.' );
}

/**
 * Youxi Shortcode class
 *
 * This shortcode wrapper class helps the rendering and set it up for use in the editor.
 *
 * @package   Youxi Shortcode
 * @author    Mairel Theafila <maimairel@yahoo.com>
 * @copyright Copyright (c) 2013-2016, Mairel Theafila
 */
if( ! class_exists( 'Youxi_Shortcode' ) ) :

	final class Youxi_Shortcode {

		/**
		 * The human readable label of the shortcode
		 *
		 * @access private
		 * @var string
		 */
		private $label;

		/**
		 * The category id this shortcode belongs to
		 *
		 * @access private
		 * @var string
		 */
		private $category;

		/**
		 * The priority of this shortcode
		 *
		 * @access private
		 * @var int
		 */
		private $priority;

		/**
		 * The registered tag name of the shortcode
		 *
		 * @access private
		 * @var string
		 */
		private $tag;

		/**
		 * The attribute arguments of the shortcode
		 *
		 * @access private
		 * @var array
		 */
		private $atts;

		/**
		 * The content arguments of the shortcode
		 *
		 * @access private
		 * @var array
		 */
		private $content;

		/**
		 * The callback to use for outputting the shortcode
		 *
		 * @access private
		 * @var mixed
		 */
		private $callback;

		/**
		 * The shortcode icon class name
		 *
		 * @access private
		 * @var string
		 */
		private $icon;

		/**
		 * Array of required JavaScripts required by this shortcode
		 *
		 * @access private
		 * @var array
		 */
		private $scripts = array();

		/**
		 * Array of required CSS stylesheets required by this shortcode
		 *
		 * @access private
		 * @var array
		 */
		private $styles = array();

		/**
		 * The fieldsets for organizing shortcode attributes
		 *
		 * @access private
		 * @var array
		 */
		private $fieldsets = array();

		/**
		 * Shortcode attribute serializers
		 *
		 * @access private
		 * @var array
		 */
		private $serializers;

		/**
		 * Shortcode attribute deserializers
		 *
		 * @access private
		 * @var array
		 */
		private $deserializers;

		/**
		 * Whether this is an inline shortcode that can be added to shortcode richtext editor
		 *
		 * @access private
		 * @var bool
		 */
		private $inline;

		/**
		 * Whether to put line breaks before and after the shortcode content
		 *
		 * @access private
		 * @var bool
		 */
		private $insert_nl;

		/**
		 * Whether this shortcode's content should be escaped.
		 * Shortcodes whose content's field type is a $_auto_escape will be automatically escaped.
		 *
		 * @access private
		 * @var bool
		 */
		private $escape;

		/**
		 * Whether this is an internal shortcode that should not be shown
		 *
		 * @access private
		 * @var bool
		 */
		private $internal;

		/**
		 * Whether the shortcode is a third party shortcode
		 *
		 * @access private
		 * @var bool
		 */
		private $third_party = false;

		/**
		 * Keep how many times shortcodes are used
		 *
		 * @access private
		 * @var bool
		 */
		private static $_counter = array();

		/**
		 * The default attribute values
		 *
		 * @access private
		 * @var array
		 */
		private $_default_atts = null;

		/**
		 * The default values
		 *
		 * @access private
		 * @var array
		 */
		private $_defaults = null;

		/**
		 * The default shortcode arguments
		 *
		 * @access private
		 * @var array
		 */
		private $_default_args = array(

			/**
			 * label The label of the shortcode
			 */
			'label' => '', 

			/**
			 * category The category, if not set the shortcode would be uncategorized
			 */
			'category' => false, 

			/**
			 * priority The priority affects the menu order of the shortcode
			 */
			'priority' => 10, 

			/**
			 * atts The available attributes arguments of the shortcode
			 */
			'atts' => array(), 

			/**
			 * content The content arguments of the shortcode
			 */
			'content' => array(), 

			/**
			 * callback The callback of the shortcode
			 */
			'callback' => null, 

			/**
			 * icon The icon of the shortcode
			 */
			'icon' => '', 

			/**
			 * scripts Array of required JavaScripts required by this shortcode
			 */
			'scripts' => array(), 

			/**
			 * styles Array of required CSS stylesheets required by this shortcode
			 */
			'styles' => array(), 

			/**
			 * fieldsets The fieldset for organizing shortcode attributes
			 */
			'fieldsets' => array(), 

			/**
			 * serializers Array of shortcode attribute JS serializers
			 */
			'serializers' => array(), 

			/**
			 * deserializers Array of shortcode attribute JS deserializers
			 */
			'deserializers' => array(), 

			/**
			 * inline Inline shortcodes will be added to a second level TinyMCE editor
			 */
			'inline' => false, 

			/**
			 * insert_nl Insert newlines before and after the shortcode content
			 */
			'insert_nl' => true, 

			/**
			 * inline Escaped shortcodes' content will not be parsed
			 */
			'escape' => false, 

			/**
			 * internal Whether this shortcode is only for internal use only
			 */
			'internal' => false, 

			/**
			 * third_party Whether the shortcode is a third party shortcode
			 */
			'third_party' => false
		);
		
		/**
		 * The field types to automatically escape
		 *
		 * @access private
		 * @var array
		 */
		private $_auto_escape = array( 'text', 'url', 'textarea', 'richtext' );
		
		/**
		 * Constructor
		 * 
		 * @return void
		 */
		public function __construct( $tag, $args ) {

			/* Parse shortcode arguments */
			$args = wp_parse_args( $args, $this->_default_args );
			$keys = array_keys( get_class_vars( __CLASS__ ) );

			foreach( $keys as $key ) {
				if( '_' != $key[0] && isset( $args[ $key ] ) ) {
					$this->{$key} = apply_filters( "youxi_shortcode_{$tag}_{$key}", $args[ $key ], $tag );
				}
			}

			/* Extract deserializers and serializers */
			$this->serializers = array();
			$this->deserializers = array();

			foreach( $this->atts as $name => $prop ) {
				if( isset( $this->atts[ $name ]['serialize'] ) ) {
					$this->serializers[ $name ] = $this->atts[ $name ]['serialize'];
					unset( $this->atts[ $name ]['serialize'] );
				}
				if( isset( $this->atts[ $name ]['deserialize'] ) ) {
					$this->deserializers[ $name ] = $this->atts[ $name ]['deserialize'];
					unset( $this->atts[ $name ]['deserialize'] );
				}
			}

			if( isset( $this->content['serialize'] ) ) {
				$this->serializers['content'] = $this->content['serialize'];
				unset( $this->content['serialize'] );
			}
			if( isset( $this->content['deserialize'] ) ) {
				$this->deserializers['content'] = $this->content['deserialize'];
				unset( $this->content['deserialize'] );
			}

			/* Assign defaults */
			$this->_default_atts = $this->get_default_atts();
			$this->_defaults = $this->get_defaults();

			/* Check whether the shortcode exists if third party is false */
			if( ! $this->third_party && shortcode_exists( $tag ) ) {
				$this->third_party = true;
			}

			/* Assign tag and add the shortcode */
			if( ! $this->third_party ) {
				$this->tag = self::prefix( $tag );
				add_shortcode( $this->tag, array( $this, 'render' ) );
			} else {
				$this->tag = $tag;
			}
		}

		/**
		 * Construct an array containing default atts values
		 *
		 * @return array Array containing default attribute values
		 */
		public function get_default_atts() {
			if( ! is_null( $this->_default_atts ) ) {
				return $this->_default_atts;
			}

			return array_map( array( $this, 'atts_array_map' ), $this->atts );
		}

		/**
		 * Construct an array containing default atts and content values
		 *
		 * @return array Array containing default attribute and content values
		 */
		public function get_defaults() {
			if( ! is_null( $this->_defaults ) ) {
				return $this->_defaults;
			}

			$content = $this->content;
			return array_map( array( $this, 'atts_array_map' ), array_merge( $this->atts, compact( 'content' ) ) );
		}

		/**
		 * Callback for getting the default attribute values
		 *
		 * @return mixed The default attribute value
		 */
		public function atts_array_map( $array ) {
			return is_array( $array ) && isset( $array['std'] ) ? $array['std'] : null;
		}

		/**
		 * The shortcode callback that calls the real callback of the shortcode
		 *
		 * @return string The shortcode's output
		 */
		public function render( $atts, $content, $tag ) {

			if( is_callable( $this->callback ) ) {

				$unprefixed_tag = self::unprefix( $tag );

				$atts = shortcode_atts( $this->_default_atts, $atts, $tag );

				$atts = apply_filters( "youxi_shortcode_pre_render_{$unprefixed_tag}_atts", $atts, $unprefixed_tag );
				$content = apply_filters( "youxi_shortcode_pre_render_{$unprefixed_tag}_content", $content, $unprefixed_tag );

				/* Call the real shortcode callback */
				$output = call_user_func( $this->callback, $atts, $content, $unprefixed_tag );

				/* Increment shortcode counter */
				if( ! isset( self::$_counter[ $tag ] ) ) {
					self::$_counter[ $tag ] = 1;
				} else {
					self::$_counter[ $tag ]++;
				}
				
				/* Return filtered output */
				return apply_filters( "youxi_shortcode_{$unprefixed_tag}_output", $output, $atts, $content, $unprefixed_tag );
			}

			return '';
		}

		/**
		 * Enqueue the shortcode related admin assets
		 */
		public function admin_enqueue_scripts( $hook ) {

			/* 
				No need to enqueue any asset if the current post type does not support 
				wp_editor as there won't be anyway to edit the shortcode.
			*/
			if( ! post_type_supports( get_post_type(), 'editor' ) ) {
				return;
			}

			$content = $this->content;
			$props = array_merge( $this->atts, compact( 'content' ) );

			foreach( $props as $name => $prop ) {
				if( is_array( $prop ) ) {
					$prop['name'] = $name;
					Youxi_Form_Field::force_enqueue( $this->tag, $prop, $hook );
				}
			}
		}

		/**
		 * Enqueue the shortcode frontend styles and scripts.
		 */
		public function enqueue() {
			
			foreach( (array) $this->scripts as $handle => $script ) {
				if( wp_script_is( $handle ) ) {
					continue;
				}
				$script = wp_parse_args( $script, array(
					'src' => false, 
					'deps' => array(), 
					'ver' => false, 
					'in_footer' => false
				));
				wp_enqueue_script( $handle, $script['src'], $script['deps'], $script['ver'], $script['in_footer'] );
			}

			foreach( (array) $this->styles as $handle => $style ) {
				if( wp_style_is( $handle ) ) {
					continue;
				}
				$style = wp_parse_args( $style, array(
					'src' => false, 
					'deps' => array(), 
					'ver' => false, 
					'media' => 'all'
				));
				wp_enqueue_style( $handle, $style['src'], $style['deps'], $style['ver'], $style['media'] );
			}
		}

		/**
		 * Get a list of allowed shortcode arguments
		 *
		 * @param mixed The args to include in the result
		 * @param bool Whether to return a single arg value
		 *
		 * @return array The shortcode's arguments
		 */
		public function get_args( $include = '*', $single = false ) {

			/* Filter the allowed shortcode args */
			$public_args = apply_filters( 'youxi_shortcode_allowed_shortcode_args', array( 'label', 'icon', 'inline', 'insert_nl', 'instant', 'escape', 'internal', 'third_party' ) );

			/* Get all accessible object vars */
			$class_keys  = array_keys( get_object_vars( $this ) );

			/* Validate the included args */
			if( '*' == $include || ! is_array( $include ) ) {
				$include = $public_args;
			}

			/* Intersect the includes to the allowed args */
			$include = array_intersect( $public_args, $include );

			$args = array();

			/* Loop through each included args */
			foreach( $include as $arg ) {
				switch( $arg ) {
					case 'instant':
						$args[ $arg ] = empty( $this->atts ) && empty( $this->content );
						break;
					case 'escape':
						$c = (array) $this->content;
						$args[ $arg ] = ( isset( $c['type'] ) && in_array( $c['type'], $this->_auto_escape ) ) || $this->{$arg};
						break;
					default:
						if( in_array( $arg, $class_keys ) ) {
							$args[ $arg ] = $this->{$arg};
						}
						break;
				}
			}

			return $single ? reset( $args ) : $args;
		}

		/**
		 * Remove the shortcode from Wordpress
		 */
		public function remove() {
			if( ! $this->third_party ) {
				remove_shortcode( $this->tag );
			}
		}

		/**
		 * Inaccessible attributes getter
		 */
		public function __get( $name ) {

			if( preg_match( '/^(label|category|priority|tag|atts|content|callback|icon|s(cript|tyle)s|fieldsets|(de)?serializers|in(line|sert_nl|ternal)|third_party|escape)$/', $name ) ) {
				return $this->$name;
			}

			$trace = debug_backtrace();
			trigger_error( 
				'Undefined property via __get(): ' . $name .
				' in ' . $trace[0]['file'] .
				' on line ' . $trace[0]['line'],
			E_USER_NOTICE );

			return null;
		}

		/**
		 * Convert shortcodes string to an array recursively
		 * 
		 * @param string The shortcodes string
		 *
		 * @return array The shortcodes converted into an array
		 */
		public static function to_array( $content, $parse_defaults = false ) {

			$result = array();

			preg_match_all( '/' . get_shortcode_regex() . '/s', $content, $matches, PREG_SET_ORDER );

			foreach( $matches as $m ) {

				// If we matched an escaped shortcode
				if ( $m[1] == '[' && $m[6] == ']' ) {
					continue;
				}

				/* Parse attributes */
				$atts    = shortcode_parse_atts( $m[3] );
				$content = isset( $m[5] ) ? $m[5] : null;
				$tag     = $m[2];

				/* Parse default attributes */
				if( $parse_defaults ) {
					$object = Youxi_Shortcode_Manager::get()->get_shortcode( $tag );
					if( is_a( $object, 'Youxi_Shortcode' ) ) {
						$atts = shortcode_atts( $object->get_default_atts(), $atts, $tag );
					}
				}

				/* Parse content */
				if( ! is_null( $content ) ) {
					$shortcode = Youxi_Shortcode_Manager::get()->get_shortcode( $tag );

					if( is_a( $shortcode, 'Youxi_Shortcode' ) && $shortcode->get_args( 'escape', true ) ) {
						$content = trim( $content );
					} else {
						$content = self::to_array( $content, $parse_defaults );
					}
				}

				/* Save the shortcode objects */
				$result[] = compact( 'atts', 'content', 'tag' );
			}

			return empty( $result ) ? trim( $content ) : $result;
		}

		/**
		 * Get a shortcode unique ID
		 */
		public static function uniqid( $tag ) {
			return self::prefix( $tag ) . '_' . self::count( $tag );
		}

		/**
		 * Count `$tag` shortcode
		 */
		public static function count( $tag ) {

			$tag = self::prefix( $tag );
			if( isset( self::$_counter[ $tag ] ) ) {
				return self::$_counter[ $tag ];
			}

			return 0;
		}

		/**
		 * Recount `$tag` shortcode
		 */
		public static function recount( $tag ) {
			unset( self::$_counter[ self::prefix( $tag ) ] );
		}

		/**
		 * Prefix a shortcode or get the prefix
		 */
		public static function prefix( $tag = null ) {

			$prefix = apply_filters( 'youxi_shortcode_prefix', '' );

			if( is_string( $prefix ) && ! empty( $prefix ) ) {

				if( is_string( $tag ) ) {
					return $prefix . self::unprefix( $tag );
				} else if( is_array( $tag ) ) {
					foreach( $tag as $i => $t ) {
						if( is_string( $t ) ) {
							$tag[ $i ] = $prefix . self::unprefix( $t );
						}
					}
					return $tag;
				}
				return $prefix;
			}

			return is_string( $tag ) || is_array( $tag ) ? $tag : '';
		}

		public static function unprefix( $tag ) {
			return preg_replace( '/^' . self::prefix() . '/', '', $tag );
		}

		public static function get_separator_shortcodes() {
			return apply_filters( 'youxi_shortcode_separator_shortcodes', self::prefix( array( 'separator' ) ) );
		}

		public static function get_container_shortcodes() {
			return apply_filters( 'youxi_shortcode_container_shortcodes', self::prefix( array( 'container' ) ) );
		}

		public static function get_column_container_shortcode() {
			return apply_filters( 'youxi_shortcode_column_container_shortcode', self::prefix( 'container' ) );
		}

		public static function get_row_shortcode() {
			return apply_filters( 'youxi_shortcode_row_shortcode', self::prefix( 'row' ) );
		}

		public static function get_column_shortcode() {
			return apply_filters( 'youxi_shortcode_column_shortcode', self::prefix( 'col' ) );
		}

		public static function get_column_count() {
			return apply_filters( 'youxi_shortcode_column_count', 12 );
		}

		public static function get_column_types() {
			return apply_filters( 'youxi_shortcode_column_types', array(
				'xs' => esc_html__( 'Extra Small (&lt; 576px)', 'youxi' ), 
				'sm' => esc_html__( 'Small (&ge; 576px)', 'youxi' ), 
				'md' => esc_html__( 'Medium (&ge; 768px)', 'youxi' ), 
				'lg' => esc_html__( 'Large (&ge; 992px)', 'youxi' ), 
				'xl' => esc_html__( 'Large (&ge; 1200px)', 'youxi' )
			) );
		}

		public static function get_column_default_type() {
			return apply_filters( 'youxi_shortcode_column_default_type', 'lg' );
		}

		public static function get_column_fieldsets() {

			$column_types = self::get_column_types();
			$column_fieldsets = array();

			foreach( $column_types as $type => $title ) {

				$column_fieldsets[ $type ] = compact( 'title' );

				if( $type == self::get_column_default_type() ) {
					$column_fieldsets[ $type ]['active'] = true;
				}
			}

			return $column_fieldsets;
		}

		public static function get_column_atts() {

			$column_types = self::get_column_types();
			$column_atts  = array();

			$choices_base = array();
			foreach( range( 1, self::get_column_count() ) as $size ) {
				if( $size > 1 ) {
					$choices_base[ $size ] = sprintf( esc_html__( '%d columns', 'youxi' ), $size );
				} else {
					$choices_base[ $size ] = sprintf( esc_html__( '%d column', 'youxi' ), $size );
				}
			}

			$choices_size = array(
				'inherit' => esc_html__( 'Do not assign a column size', 'youxi' )
			) + $choices_base;

			$choices_offset = array(
				'inherit' => esc_html__( 'Do not assign a column offset', 'youxi' ), 
				0 => esc_html__( '0 columns &ndash; no offset', 'youxi' )
			) + $choices_base;

			foreach( $column_types as $type => $title ) {

				foreach( array( 'size', 'push', 'pull' ) as $field ) {

					$column_atts[ "size_{$type}" ] = array(
						'type' => 'select', 
						'label' => esc_html__( 'Column Size', 'youxi' ), 
						'description' => esc_html__( 'Determine the column size.', 'youxi' ), 
						'fieldset' => $type, 
						'std' => ( $type == self::get_column_default_type() ? self::get_column_count() : 'inherit' ), 
						'choices' => ( $type == self::get_column_default_type() ? $choices_base : $choices_size ), 
					);
					$column_atts[ "offset_{$type}" ] = array(
						'type' => 'select', 
						'label' => esc_html__( 'Column Offset', 'youxi' ), 
						'description' => esc_html__( 'Determine the column offset.', 'youxi' ), 
						'fieldset' => $type, 
						'std' => 'inherit', 
						'choices' => $choices_offset
					);
				}
			}

			return $column_atts;
		}
	}
endif;
