<?php if ( ! defined( 'ABSPATH' ) ) {
	die( 'Hi there!  I\'m just a plugin, not much I can do when called directly.' );
}

/**
 * Youxi Taxonomy Class
 *
 * This class is a helper wrapper class for easily registering taxonomies.
 *
 * @package   Youxi Core
 * @author    Mairel Theafila <maimairel@gmail.com>
 * @copyright Copyright (c) 2013-2016, Mairel Theafila
 */
if( ! class_exists( 'Youxi_Taxonomy' ) ) :

	final class Youxi_Taxonomy {

		/**
		 * @access private
		 * @var string The name of the taxonomy 
		 */
		private $taxonomy;

		/**
		 * @access private
		 * @var array The arguments of the taxonomy
		 */
		private $taxonomy_args;

		/**
		 * @access private
		 * @var array The taxonomy term fields
		 */
		private $taxonomy_fields = array();

		/**
		 * @access private
		 * @var array The taxonomy field default arguments
		 */
		private static $taxonomy_field_defaults = array(
			'type'        => 'text', 
			'label'       => '', 
			'description' => '', 
			'std'         => '', 
			'choices'     => array(), 
			'min'         => false, 
			'max'         => false, 
			'step'        => false, 
			'sanitize_callback' => ''
		);

		/**
		 * @access private
		 * @var array The taxonomies already initialized through this class
		 */
		private static $_taxonomy_instances = array();

		/**
		 * Constructor
		 *
		 * @param string The ID of the taxonomy
		 * @param array The arguments to pass to register_taxonomy
		 */
		private function __construct( $taxonomy, $args = array() ) {
			$this->taxonomy       = $taxonomy;
			$this->taxonomy_args  = $args;

			add_action( $this->taxonomy . '_add_form_fields', array( $this, 'add_form_fields' ) );
			add_action( $this->taxonomy . '_edit_form_fields', array( $this, 'edit_form_fields' ) );

			add_action( 'create_' . $this->taxonomy, array( $this, 'save_form_fields' ) );
			add_action( 'edit_' . $this->taxonomy, array( $this, 'save_form_fields' ) );

			add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
		}

		/**
		 * Get a taxonomy object, makes sure that each taxonomy only has one object
		 *
		 * @param string The taxonomy name
		 * @param array The arguments to pass to register_taxonomy
		 */
		public static function get( $taxonomy, $args = array() ) {

			// Do not allow modifying these core taxonomies
			if( in_array( $taxonomy, array( 'nav_menu', 'link_category', 'post_format' ) ) ) {
				return;
			}

			// Do not allow modifying core taxonomy arguments
			if( in_array( $taxonomy, array( 'category', 'post_tag' ) ) ) {
				$args = array();
			}

			// Check if wrapper object already exists
			if( empty( self::$_taxonomy_instances[ $taxonomy ] ) ) {
				self::$_taxonomy_instances[ $taxonomy ] = new self( $taxonomy, $args );
			}

			return self::$_taxonomy_instances[ $taxonomy ];
		}

		/**
		 * Register the taxonomy, this should be called on the init hook
		 *
		 * @param string The name of the post type to register this taxonomy
		 */
		public function register( $post_type ) {
			if( ! taxonomy_exists( $this->taxonomy ) ) {
				register_taxonomy( $this->taxonomy, $post_type, $this->taxonomy_args );
			}
			register_taxonomy_for_object_type( $this->taxonomy, $post_type );
		}

		public function admin_enqueue_scripts( $hook ) {
			
			if( ( 'edit-tags.php' != $hook && 'term.php' != $hook ) || $this->taxonomy != get_current_screen()->taxonomy ) {
				return;
			}

			wp_enqueue_media();

			wp_enqueue_style( 'wp-color-picker' );
			wp_enqueue_script( 'wp-color-picker' );

			wp_enqueue_style( 'youxi-taxonomy-form-field', YOUXI_CORE_URL . 'admin/assets/css/youxi.taxonomy.form-field.css', array(), YOUXI_CORE_VERSION, 'screen' );
			wp_enqueue_script( 'youxi-taxonomy-form-field', YOUXI_CORE_URL . 'admin/assets/js/youxi.taxonomy.form-field.js', array( 'jquery' ), YOUXI_CORE_VERSION, true );
		}

		public function valid_taxonomy_fields() {
			return apply_filters( 'youxi_valid_taxonomy_fields', array( 'image', 'color', 'radio', 'checkbox', 'select', 'textarea', 'url', 'number', 'text' ) );
		}

		public function register_field( $field_id, $field_args ) {

			if( empty( $field_args['type'] ) || ! in_array( $field_args['type'], $this->valid_taxonomy_fields() ) ) {
				$field_args['type'] = 'text';
			}

			$field_args = wp_parse_args( $field_args, self::$taxonomy_field_defaults );
			$this->taxonomy_fields[ $field_id ] = $field_args;

			if( is_callable( $field_args['sanitize_callback'] ) ) {
				register_meta( 'term', $field_id, $field_args['sanitize_callback'] );
			}
		}

		protected function render_form_field( $field_id, $field_args, $field_value ) {

			if( 'image' == $field_args['type'] ) {

				$has_attachment = wp_attachment_is( 'image', $field_value );

				echo '<div class="youxi-taxonomy-image-field">';

					echo '<div class="youxi-taxonomy-image-field__img">';

						if( $has_attachment ) {
							echo wp_get_attachment_image( $field_value, 'thumbnail' );
						}

					echo '</div>';

					echo '<div class="youxi-taxonomy-image-field__remove"' . ( $has_attachment ? '' : ' style="display: none;"' ) . '>&times;</div>';

					echo '<input type="hidden" name="' . esc_attr( $field_id ) . '" value="' . esc_attr( $field_value ) . '" class="youxi-taxonomy-image-field__input">';

				echo '</div>';

			} elseif( 'color' == $field_args['type'] ) {

				echo '<input type="text" id="' . esc_attr( $field_id ) . '" name="' . esc_attr( $field_id ) . '" type="text" value="' . esc_attr( $field_value ) . '" maxlength="7" class="youxi-taxonomy-color-field" data-default-color="' . esc_attr( $field_args['std'] ) . '" data-hide="true" data-palette="true">';

			} elseif( 'radio' == $field_args['type'] ) {

				foreach( $field_args['choices'] as $key => $value ) : 

					echo '<label>';
						
						echo '<input type="radio" name="' . esc_attr( $field_id ) . '" value="' . esc_attr( $key ) . '"' . checked( $field_value, $key, false ) . '>';
						
						echo esc_html( $value );

					echo '</label><br>';

				endforeach;

			} elseif( 'checkbox' == $field_args['type'] ) {

				foreach( $field_args['choices'] as $key => $value ) : 
					
					echo '<label>';
						
						echo '<input type="checkbox" name="' . esc_attr( $field_id ) . '[]" value="' . esc_attr( $key ) . '"' . checked( $field_value, $key, false ) . '>';
						
						echo esc_html( $value );
					
					echo '</label><br>';

				endforeach;

			} elseif( 'select' == $field_args['type'] ) {

				echo '<select id="' . esc_attr( $field_id ) . '" name="' . esc_attr( $field_id ) . '" class="postform">';
					
					foreach( $field_args['choices'] as $key => $value ) : 
						
						echo '<option value="' . esc_attr( $key ) . '"' . selected( $field_value, $key, false ) . '>' . esc_html( $value ) . '</option>';
					
					endforeach;

				echo '</select>';

			} elseif( 'textarea' == $field_args['type'] ) {

				echo '<textarea id="' . esc_attr( $field_id ) . '" name="' . esc_attr( $field_id ) . '" rows="5" cols="50" class="large-text">' . esc_textarea( $field_value ) . '</textarea>';
			
			} elseif( 'url' == $field_args['type'] ) {

				echo '<input type="url" id="' . esc_attr( $field_id ) . '" name="' . esc_attr( $field_id ) . '" value="' . esc_attr( $field_value ) . '">';
			
			} elseif( 'number' == $field_args['type'] ) {

				echo '<input type="number" id="' . esc_attr( $field_id ) . '" name="' . esc_attr( $field_id ) . '" value="' . esc_attr( $field_value ) . '"';
				
				foreach( array( 'min', 'max', 'step' ) as $attr ) {

					if( ! empty( $field_args[ $attr ] ) ) {
						printf( ' %s="%d"', $attr, $field_args[ $attr ] );
					}
				}

				echo '">';

			} else {

				echo '<input type="text" id="' . esc_attr( $field_id ) . '" name="' . esc_attr( $field_id ) . '" value="' . esc_attr( $field_value ) . '">';
			
			}

			if( ! empty( $field_args['description'] ) ) {
				echo '<p class="description">' . esc_html( $field_args['description'] ) . '</p>';
			}
		}

		public function add_form_fields() {

			foreach( $this->taxonomy_fields as $field_id => $field_args ) : ?>

			<div class="form-field term-<?php echo esc_attr( $field_id ); ?>-wrap">
				<label for="<?php echo esc_attr( $field_id ); ?>"><?php echo esc_html( $field_args['label'] ); ?></label>
				<?php $this->render_form_field( $field_id, $field_args, $field_args['std'] ); ?>
			</div>

			<?php endforeach;
		}

		public function edit_form_fields( $term ) {

			foreach( $this->taxonomy_fields as $field_id => $field_args ) : 

				$field_value = get_term_meta( $term->term_id, "_$field_id", true );

			?>
			<tr class="form-field term-<?php echo esc_attr( $field_id ); ?>-wrap">
				<th scope="row"><label for="<?php echo esc_attr( $field_id ); ?>"><?php echo esc_html( $field_args['label'] ); ?></label></th>
				<td><?php $this->render_form_field( $field_id, $field_args, $field_value ); ?></td>
			</tr>

			<?php endforeach;
		}

		public function save_form_fields( $term_id ) {

			foreach( $this->taxonomy_fields as $field_id => $field_args ) {

				if( isset( $_POST[ $field_id ] ) ) {

					$old_value = get_term_meta( $term_id, "_$field_id", true );
					$new_value = $_POST[ $field_id ];

					if( is_callable( $field_args['sanitize_callback'] ) ) {
						$new_value = call_user_func( $field_args['sanitize_callback'], $new_value );
					}

					if( $old_value != $new_value ) {
						update_term_meta( $term_id, "_$field_id", $new_value );
					}

				} else {
					
					delete_term_meta( $term_id, "_{$field_id}" );
				}
			}
		}
	}
endif;
