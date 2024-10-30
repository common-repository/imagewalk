<?php

class Imagewalk_Endpoint_Post extends Imagewalk_Endpoint_Abstract {
	private static $_instance = NULL;

	public static function get_instance() {
		if ( NULL === self::$_instance ) {
			self::$_instance = new self;
		}
		return self::$_instance;
	}

	private function __construct() {
		$this->add_endpoint( 'delete_post', array( $this, 'ajax_delete_post' ) );

		add_filter( 'imagewalk_assets_data', array( $this, 'assets_data' ) );
	}

	/**
	 * Assets data.
	 */
	public function assets_data( $data ) {
		$data[ 'post' ] = array(
			'template' => trim( Imagewalk::get_template( 'components/post' ) ),
			'lang' => array(
				'delete' => __( 'Are you sure want to delete this post?', 'imagewalk' )
			)
		);

		return $data;
	}

	/**
	 * Ajax endpoint to delete an existing comment.
	 */
	public function ajax_delete_post() {
		$post_id = (int) $_POST[ 'id' ];
		$result = Imagewalk_Model_Post::delete( $post_id );

		if ( is_wp_error( $result ) ) {
			echo wp_json_encode( array( 'error' => $result->get_error_message() ) );
		} else {
			echo wp_json_encode( array( 'success' => true ) );
		}

		wp_die();
	}

	/**
	 * Get HTML representation of a post.
	 *
	 * TODO: This function should not be in endpoint.
	 *
	 * @param int $post_id
	 * @return string
	 */
	public function get_html( $post_id ) {
		$post = get_post( $post_id );
		if ( $post ) {
			$post_data = Imagewalk_Model_Post::get( $post );
			return Imagewalk::get_template( 'components/post', $post_data );
		}

		return __( 'Post not found.', 'imagewalk' );
	}
}
