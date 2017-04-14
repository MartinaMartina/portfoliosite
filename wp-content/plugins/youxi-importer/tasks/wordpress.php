<?php if ( ! defined( 'ABSPATH' ) ) {
	die( 'Cheatin&#8217; uh?' );
}

if( ! class_exists( 'WP_Import' ) ) {
	require YOUXI_IMPORTER_DIR . 'vendor/wordpress-importer/wordpress-importer.php';
}

class Youxi_Importer_Task_WordPress extends Youxi_Importer_Task {

	public function __construct( $args ) {

		parent::__construct( $args );

		if( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) {

			$attachments = array();

			if( ! empty( $this->args['xml'] ) && is_file( $this->args['xml'] ) ) {

				$wxr_parser = new WXR_Parser();
				$data = $wxr_parser->parse( $this->args['xml'] );

				if( ! is_wp_error( $data ) ) {

					$attachments = wp_list_filter( $data['posts'], array( 'post_type' => 'attachment' ) );
					$attachments = array_values( $attachments );

					// Remove _wp_attachment_metadata (unused by WordPress importer)
					foreach( $attachments as &$attachment ) {

						if( empty( $attachment['postmeta'] ) )
							continue;

						foreach( $attachment['postmeta'] as $n => $meta_value ) {
							if( isset( $meta_value['key'] ) && '_wp_attachment_metadata' == $meta_value['key'] ) {
								unset( $attachment['postmeta'][ $n ] );
							}
						}
					}

					$this->js_params = array(
						'base_url'    => $data['base_url'], 
						'attachments' => $attachments
					);
				}
			}
		}
	}

	public function priority() {
		return 1;
	}

	public function messages() {
		return array(
			'attachmentStatus' => esc_html__( 'Importing attachments: {current} of {total}', 'youxi' ), 
			'wpStatus' => esc_html__( 'Importing posts, pages, comments, custom fields, categories, and tags', 'youxi' )
		);
	}

	public function run( $params ) {

		$params = wp_parse_args( $params, array(
			'import'     => 'wp', 
			'attachment' => array(), 
			'wp'         => array()
		));

		if( empty( $this->args['xml'] ) || ! is_file( $this->args['xml'] ) ) {
			return new WP_Error( 'wp_attachment_import_invalid_xml', sprintf( esc_html__( 'The provided WordPress XML import file does not exists', 'youxi' ), 
				$params['attachment']['post_title'] ) );
		}

		// Import a single attachment
		if( 'attachment' == $params['import'] ) {

			if( ! empty( $params['attachment']['post_id'] ) ) {

				$attachment_importer = new Youxi_WP_Attachment_Import();
				$attachment_importer->fetch_attachments = true;

				if( ! empty( $this->args['attachments_directory'] ) ) {
					$attachment_importer->attachments_directory = $this->args['attachments_directory'];
				}

				ob_start();

				set_time_limit(0);
				$attachment_importer->import( array(
					'posts'    => array( $params['attachment'] ), 
					'base_url' => $params['base_url']
				));

				$importer_output = ob_get_clean();

				if( ! empty( $attachment_importer->processed_posts ) ) {

					$result = array(
						'post_orphans'    => $attachment_importer->post_orphans, 
						'url_remap'       => $attachment_importer->url_remap, 
						'processed_posts' => $attachment_importer->processed_posts
					);

					if( ! empty( $importer_output ) ) {
						$result['message'] = $importer_output;
					}

					return json_encode( $result, JSON_FORCE_OBJECT );
				}

				return new WP_Error( 'wp_attachment_import_fail', sprintf( esc_html__( 'An unknown error has occured, the attachment &lsquo;%s&rsquo; was not imported', 'youxi' ), 
					$params['attachment']['post_title'] ) );
			}

			return new WP_Error( 'wp_attachment_import_invalid', esc_html__( 'An invalid attachment data was provided', 'youxi' ) );

		// Import everything except attachments
		} else if( 'wp' == $params['import'] ) {

			$wp_importer = new WP_Import();
			$wp_importer->fetch_attachments = false;

			if( ! empty( $params['wp'] ) ) {

				$wp_params = wp_parse_args( $params['wp'], array(
					'post_orphans'    => array(), 
					'url_remap'       => array(), 
					'processed_posts' => array()
				));
				$wp_importer->post_orphans    = $wp_params['post_orphans'];
				$wp_importer->url_remap       = $wp_params['url_remap'];
				$wp_importer->processed_posts = $wp_params['processed_posts'];
			}

			ob_start();

			set_time_limit(0);
			$wp_importer->import( $this->args['xml'] );

			return ob_get_clean();

		} else {
			return new WP_Error( 'wp_import_invalid', esc_html__( 'Invalid WordPress import task', 'youxi' ) );
		}
	}
}

class Youxi_WP_Attachment_Import extends WP_Import {

	function fetch_remote_file( $url, $post ) {
		// extract the file name and extension from the url
		$file_name = basename( $url );

		// get placeholder file in the upload dir with a unique, sanitized filename
		$upload = wp_upload_bits( $file_name, 0, '', $post['upload_date'] );
		if ( $upload['error'] )
			return new WP_Error( 'upload_dir_error', $upload['error'] );

		// If local attachments directory is provided
		if( ! empty( $this->attachments_directory ) ) {

			$local_path = trailingslashit( $this->attachments_directory ) . basename( $url );

			if( is_file( $local_path ) ) {

				// open up the placeholder file
				if( $out_fp = fopen( $upload['file'], 'w' ) ) {

					// write the local file to the placeholder file
					$content = @file_get_contents( $local_path );
					@fwrite( $out_fp, $content );
					fclose( $out_fp );
					clearstatcache();

					$use_local_file = true;
					$filesize = filesize( $upload['file'] );
				}
			}
		}

		if( ! $use_local_file ) {

			// fetch the remote url and write it to the placeholder file
			$headers = wp_get_http( $url, $upload['file'] );

			// request failed
			if ( ! $headers ) {
				@unlink( $upload['file'] );
				return new WP_Error( 'import_file_error', __('Remote server did not respond', 'youxi') );
			}

			// make sure the fetch was successful
			if ( $headers['response'] != '200' ) {
				@unlink( $upload['file'] );
				return new WP_Error( 'import_file_error', sprintf( __('Remote server returned error response %1$d %2$s', 'youxi'), esc_html($headers['response']), get_status_header_desc($headers['response']) ) );
			}

			$filesize = filesize( $upload['file'] );

			if ( isset( $headers['content-length'] ) && $filesize != $headers['content-length'] ) {
				@unlink( $upload['file'] );
				return new WP_Error( 'import_file_error', __('Remote file is incorrect size', 'youxi') );
			}

			if ( 0 == $filesize ) {
				@unlink( $upload['file'] );
				return new WP_Error( 'import_file_error', __('Zero size file downloaded', 'youxi') );
			}
		}

		$max_size = (int) $this->max_attachment_size();
		if ( ! empty( $max_size ) && $filesize > $max_size ) {
			@unlink( $upload['file'] );
			return new WP_Error( 'import_file_error', sprintf(__('Remote file is too large, limit is %s', 'youxi'), size_format($max_size) ) );
		}

		// keep track of the old and new urls so we can substitute them later
		$this->url_remap[$url] = $upload['url'];
		$this->url_remap[$post['guid']] = $upload['url']; // r13735, really needed?
		// keep track of the destination if the remote url is redirected somewhere else
		if ( ! $use_local_file && isset($headers['x-final-location']) && $headers['x-final-location'] != $url )
			$this->url_remap[$headers['x-final-location']] = $upload['url'];

		return $upload;
	}

	function import( $import_data ) {
		add_filter( 'import_post_meta_key', array( $this, 'is_valid_meta_key' ) );
		add_filter( 'http_request_timeout', array( &$this, 'bump_request_timeout' ) );

		$this->posts    = $import_data['posts'];
		$this->base_url = esc_url( $import_data['base_url'] );

		$this->process_posts();
	}
}
