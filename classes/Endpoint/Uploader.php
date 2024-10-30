<?php

class Imagewalk_Endpoint_Uploader extends Imagewalk_Endpoint_Abstract {
	private static $_instance = NULL;

	public static function get_instance() {
		if ( NULL === self::$_instance ) {
			self::$_instance = new self;
		}
		return self::$_instance;
	}

	private function __construct() {
		$this->add_endpoint( 'upload', array( $this, 'ajax_upload' ) );

		add_action( 'wp_enqueue_scripts', array( $this, 'load_assets' ) );
		add_filter( 'imagewalk_assets_data', array( $this, 'assets_data' ) );
	}

	/**
	 * Assets data.
	 */
	public function assets_data( $data ) {
		$data[ 'uploader' ] = array(
			'template' => trim( Imagewalk::get_template( 'components/uploader' ) )
		);

		return $data;
	}

	/**
	 * Load uploader scripts and stylesheets.
	 */
	public function load_assets() {
		wp_enqueue_style( 'imagewalk-uploader', Imagewalk::get_css( 'uploader' ), array( 'imagewalk-core' ) );
		wp_enqueue_script( 'imagewalk-uploader', Imagewalk::get_js( 'uploader' ), array( 'imagewalk-core' ), FALSE, TRUE );
	}

	/**
	 * Ajax endpoint for upload.
	 */
	public function ajax_upload() {
		// Get upload data.
		$img = $_POST[ 'img' ]; // base64 image data
		$caption = sanitize_text_field( $_POST[ 'caption' ] );

		// Save image data.
		$attach_id = Imagewalk_Model_Image::add( $img );

		if ( is_wp_error( $posts ) ) {
			echo wp_json_encode( array( 'error' => $attach_id->get_error_message() ) );
			wp_die();
		}

		// Insert post.
		$result = Imagewalk_Model_Post::add( array(
			'thumbnail' => $attach_id,
			'caption' => $caption
		) );

		if ( is_wp_error( $result ) ) {
			echo wp_json_encode( array( 'error' => $result->get_error_message() ) );
		} else {
			echo wp_json_encode( array( 'success' => true, 'data' => $result ) );
		}

		wp_die();
	}

}
