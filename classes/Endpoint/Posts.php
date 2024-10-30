<?php

class Imagewalk_Endpoint_Posts extends Imagewalk_Endpoint_Abstract {
	private static $_instance = NULL;

	public static function get_instance() {
		if ( NULL === self::$_instance ) {
			self::$_instance = new self;
		}
		return self::$_instance;
	}

	private function __construct() {
		$this->query_compare = NULL;

		$this->add_endpoint( 'get_posts', array( $this, 'ajax_get_posts' ), array( 'allow_guest' => TRUE ) );

		add_filter( 'imagewalk_assets_data', array( $this, 'assets_data' ) );
	}

	/**
	 * Assets data.
	 */
	public function assets_data( $data ) {
		$data[ 'posts' ] = array(
			'initial' => get_option( 'imagewalk_stream_posts_initial', 1 )
		);

		return $data;
	}

	/**
	 * Ajax endpoint to get posts.
	 */
	public function ajax_get_posts() {
		$limit = isset( $_GET[ 'limit' ] ) ? (int) $_GET[ 'limit' ] : 2;
		$user_id = isset( $_GET[ 'user_id' ] ) ? (int) $_GET[ 'user_id' ] : FALSE;
		$mode = isset( $_GET[ 'mode' ] ) ? sanitize_text_field( $_GET[ 'mode' ] ) : FALSE;

		// Manually sanitize input since it contains '<' or '>' string.
		$compare = FALSE;
		if ( isset( $_GET[ 'ref' ] ) && preg_match( '#^[<>]\d+$#', $_GET[ 'ref' ] ) ) {
			$compare = $_GET[ 'ref' ];
		}

		$posts = Imagewalk_Model_Posts::get( $limit, $user_id, $compare );

		if ( is_wp_error( $posts ) ) {
			echo wp_json_encode( array( 'error' => $posts->get_error_message() ) );
		} else {
			echo wp_json_encode( array( 'success' => true, 'data' => $posts ) );
		}

		wp_die();
	}
}
