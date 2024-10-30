<?php

class Imagewalk_Model_Image {
	/**
	 * Add new image.
	 *
	 * @param string $img_base64
	 * @return int|WP_Error
	 */
	public static function add( $img_base64 ) {
		// Validate image data.
		if ( ! preg_match( '#^data:(image/(png|jpe?g));#', $img_base64, $matches ) ) {
			return new WP_Error( 'error', __( 'Invalid image data.', 'imagewalk' ) );
		}

		// Get image type.
		$img_type = $matches[ 1 ];
		if ( ! in_array( $img_type, Imagewalk::$allowed_img_types ) ) {
			return new WP_Error( 'error', __( 'Invalid image type.', 'imagewalk' ) );
		}

		// Decode image data.
		$img = str_replace( 'data:' . $img_type . ';base64,', '', $img_base64 );
		$img = str_replace( ' ', '+', $img );
		$img = base64_decode( $img );

		$upload_dir = wp_upload_dir();
		$upload_path = trailingslashit( $upload_dir[ 'basedir' ] ) . 'imagewalk/';

		// Create image upload directory if needed.
		if ( ! file_exists( untrailingslashit( $upload_path ) ) ) {
			if ( ! mkdir( untrailingslashit( $upload_path ) ) ) {
				return new WP_Error( 'error', __( 'Cannot create upload directory.', 'imagewalk' ) );
			}
		}

		// Set image extension based on the image type.
		$img_ext = $img_type === 'image/png' ? 'png' : 'jpg';

		// Save image data to the image upload directory..
		$tmp_name = md5( 'imagewalk_upload_' . microtime() ) . '.' . $img_ext;
		$success = file_put_contents( $upload_path . $tmp_name, $img );
		if ( ! $success ) {
			return new WP_Error( 'error', __( 'Cannot save uploaded file.', 'imagewalk' ) );
		}

		// Check for validity for the file by checking image size.
		// https://www.wordfence.com/learn/how-to-prevent-file-upload-vulnerabilities/
		if ( ! @getimagesize( $upload_path . $tmp_name ) ) {
			unlink( $upload_path . $tmp_name );
			return new WP_Error( 'error', __( 'File is not valid.', 'imagewalk' ) );
		}

		if ( ! function_exists( 'wp_handle_sideload' ) ) {
			require_once( ABSPATH . 'wp-admin/includes/file.php' );
		}

		if ( ! function_exists( 'wp_get_current_user' ) ) {
			require_once( ABSPATH . 'wp-includes/pluggable.php' );
		}

		$file = array();
		$file[ 'error' ] = '';
		$file[ 'tmp_name' ] = $upload_path . $tmp_name;
		$file[ 'name' ] = $tmp_name;
		$file[ 'type' ] = $img_type;
		$file[ 'size' ] = filesize( $upload_path . $tmp_name );

		$img_quality_100 = function () { return 100; };

		// Attach to the media library.
		add_filter( 'jpeg_quality', $img_quality_100 );
		$result = wp_handle_sideload( $file, array( 'test_form' => false ) );
		remove_filter( 'jpeg_quality', $img_quality_100 );

		if ( ! empty( $result[ 'error' ] ) ) {
			return new WP_Error( 'error', $result[ 'error' ] );
		}

		$attachment = array( 'post_mime_type' => $img_type, 'post_status' => 'inherit' );
		$attach_id = wp_insert_attachment( $attachment, $result[ 'file' ] );

		// Generate the metadata for the attachment, and update the database record.
		require_once( ABSPATH . 'wp-admin/includes/image.php' );
		$attach_data = wp_generate_attachment_metadata( $attach_id, $result[ 'file' ] );
		wp_update_attachment_metadata( $attach_id, $attach_data );

		return $attach_id;
	}

}
