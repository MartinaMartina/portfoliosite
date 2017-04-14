<?php if ( ! defined( 'ABSPATH' ) ) {
	die( 'Hi there!  I\'m just a plugin, not much I can do when called directly.' );
}

/**
 * Youxi Image Uploader Class
 *
 * This class creates an image uploader using WordPress 3.5 media uploader.
 *
 * @package   Youxi Core
 * @author    Mairel Theafila <maimairel@gmail.com>
 * @copyright Copyright (c) 2013-2016, Mairel Theafila
 */
if( ! class_exists( 'Youxi_Image_Form_Field' ) ) :

	class Youxi_Image_Form_Field extends Youxi_Form_Field {

		private static $media_template_printed = false;

		/**
		 * Constructor.
		 */
		public function __construct( $scope, $options, $allowed_hooks = array() ) {

			// Merge default options
			$this->default_options = array_merge( $this->default_options, array(
				'multiple'        => false, 
				'return_type'     => 'id', /* url | id */
				'return_url_size' => 'full', /* ignored if return_type is attachment */
				'frame_title'     => esc_html__( 'Choose Image(s)', 'youxi' ), 
				'frame_btn_text'  => esc_html__( 'Insert Image(s)', 'youxi' )
			));

			if( isset( $options['return_type'] ) ) {
				if( ! in_array( $options['return_type'], array( 'url', 'attachment', 'id' ) ) ) {
					unset( $options['return_type'] );
				} else if( 'attachment' == $options['return_type'] ) {
					$options['return_type'] = 'id';
				}
			}

			parent::__construct( $scope, $options, $allowed_hooks );
		}

		/**
		 * Enqueue Required Assets
		 */
		public function enqueue( $hook ) {

			if( parent::enqueue( $hook ) ) {

				$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

				wp_enqueue_media();
				
				wp_enqueue_script( 'youxi-file-uploader', self::field_assets_url( "js/youxi.form.upload{$suffix}.js" ), array( 'youxi-form-manager', 'jquery-ui-sortable', 'media-views' ), YOUXI_CORE_VERSION, true );
				wp_enqueue_style( 'youxi-file-uploader', self::field_assets_url( "css/youxi.form.upload{$suffix}.css" ), array( 'youxi-form' ), YOUXI_CORE_VERSION );

				if( ! self::$media_template_printed ) {
					add_action( 'print_media_templates', array( get_class(), 'print_media_templates' ) );
				}
			}
		}

		/**
		 * Sanitize the user submitted data
		 * 
		 * @param mixed The data to sanitize
		 *
		 * @return mixed The sanitized data
		 */
		public function sanitize( $data ) {

			if( $this->get_option( 'multiple' ) ) {

				if( ! is_array( $data ) || empty( $data ) ) {
					$data = array();
				}

				if( 'url' == $this->get_option( 'return_type' ) ) {
					$data = array_map( 'esc_url_raw', $data );
				} elseif( 'id' == $this->get_option( 'return_type' ) ) {
					$data = array_filter( $data, 'wp_attachment_is_image' );
				}

			} else {

				if( 'url' == $this->get_option( 'return_type' ) ) {
					$data = esc_url_raw( $data );
				} elseif( 'id' == $this->get_option( 'return_type' ) ) {
					if( ! wp_attachment_is_image( $data ) ) {
						$data = '';
					}
				}
			}

			return $data;
		}

		/**
		 * Helper method to filter an array of attributes before using it on a field
		 * 
		 * @param mixed The attributes to filter
		 *
		 * @return mixed The filtered attributes
		 */
		public function filter_field_attr( $attr ) {
			
			return array_merge( $attr, array(
				'class'                => esc_attr( 'youxi-media-uploader' ), 
				'data-field-name'      => $this->get_the_name(), 
				'data-multiple'        => esc_attr( $this->get_option( 'multiple' ) ? 'add' : false ), 
				'data-return-type'     => esc_attr( $this->get_option( 'return_type' ) ), 
				'data-return-url-size' => esc_attr( $this->get_option( 'return_url_size' ) ), 
				'data-title'           => esc_attr( $this->get_option( 'frame_title' ) ), 
				'data-button-text'     => esc_attr( $this->get_option( 'frame_btn_text' ) )
			));
		}

		/**
		 * Print the media template once
		 *
		 * @return string The media template
		 */
		public static function print_media_templates() {
			if( ! self::$media_template_printed ) {
				self::$media_template_printed = true;
?>
<script type="text/html" id="tmpl-youxi-media-field">
	<div class="youxi-media-preview-item">
		<div class="youxi-media-preview-img-wrap">
			<div class="youxi-media-preview-img-wrap-inner">
				<img src="{{ data.url }}" alt="">
			</div>
		</div>
		<input type="hidden" name="{{ data.fieldName }}{{ data.fieldNamePostfix }}" value="{{ data.id }}">
		<button type="button" class="youxi-media-preview-remove">&times;</button>
	</div>
</script>
<?php
			}
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

			if( ! $this->get_option( 'multiple' ) ) {
				$value = (array) $value;
			} elseif( ! is_array( $value ) || empty( $value ) ) {
				$value = array();
			}

			$o = '<div id="' . $this->get_the_ID() . '"' . Youxi_Form::render_attr( $attributes ) . '>';

				$o .= '<div class="youxi-media-previews">';

					foreach( $value as $val ): 

						if( 'url' == $this->get_option( 'return_type' ) ) {

							$attachment_id = attachment_url_to_postid( $val );
							$url = wp_get_attachment_thumb_url( $attachment_id );
							if( empty( $url ) ) {
								$url = $val;
							}

						} elseif( 'id' == $this->get_option( 'return_type' ) ) {
							$url = wp_get_attachment_thumb_url( $val );
						}

						if( ! isset( $url ) || empty( $url ) )
							continue;

						$has_items = true;

						$o .= '<div class="youxi-media-preview-item">';
							
							$o .= '<div class="youxi-media-preview-img-wrap">';
								
								$o .= '<div class="youxi-media-preview-img-wrap-inner">';
									
									$o .= '<img src="' . esc_url( $url ) . '" alt="">';
								
								$o .= '</div>';
							
							$o .= '</div>';
							
							$o .= '<input type="hidden" name="' . $this->get_the_name() . ( $this->get_option( 'multiple' ) ? '[]' : '' ) . '" value="' . esc_attr( $val ) . '">';
							
							$o .= '<button type="button" class="youxi-media-preview-remove">&times;</button>';
						
						$o .= '</div>';

					endforeach;

				$o .= '</div>';

				$button_atts = array();

				if( ! $this->get_option( 'multiple' ) && ! empty( $value ) ) {
					$button_atts['style'] = 'display: none;';
				}

				$o .= '<input type="hidden" class="youxi-media-no-item skip-criteria-check" name="' . $this->get_the_name() . '" value=""' . ( ! empty( $has_items ) ? ' disabled' : '' ) . '>';

				$o .= '<button type="button" class="youxi-media-button"' . Youxi_Form::render_attr( $button_atts ) . '>+</button>';

			$o .= '</div>';

			return $o;
		}
	}
endif;
