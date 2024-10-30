<?php

class Imagewalk_Endpoint_Comments extends Imagewalk_Endpoint_Abstract {
	private static $_instance = NULL;

	public static function get_instance() {
		if ( NULL === self::$_instance ) {
			self::$_instance = new self;
		}
		return self::$_instance;
	}

	private function __construct() {
		$this->add_endpoint( 'get_comments', array( $this, 'ajax_get_comments' ), array( 'allow_guest' => TRUE ) );

		add_filter( 'imagewalk_assets_data', array( $this, 'assets_data' ) );
	}

	/**
	 * Assets data.
	 */
	public function assets_data( $data ) {
		$data[ 'comments' ] = array(
			'more' => get_option( 'imagewalk_post_comments_more', 8 ),
		);

		return $data;
	}

	/**
	 * Ajax endpoint to get comments for a spesific post.
	 */
	public function ajax_get_comments() {
		$post_id = (int) $_GET[ 'post_id' ];
		$limit = isset( $_GET[ 'limit' ] ) ? (int) $_GET[ 'limit' ] : 2;

		// Manually sanitize input since it contains '<' or '>' string.
		$compare = FALSE;
		if ( isset( $_GET[ 'ref' ] ) && preg_match( '#^[<>]\d+$#', $_GET[ 'ref' ] ) ) {
			$compare = $_GET[ 'ref' ];
		}

		$comments = Imagewalk_Model_Comments::get( $post_id, $limit, $compare );

		if ( is_wp_error( $comments ) ) {
			echo wp_json_encode( array( 'error' => $comments->get_error_message() ) );
		} else {
			echo wp_json_encode( array( 'success' => true, 'data' => $comments ) );
		}

		wp_die();
	}
}
