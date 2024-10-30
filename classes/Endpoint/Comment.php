<?php

class Imagewalk_Endpoint_Comment extends Imagewalk_Endpoint_Abstract {
	private static $_instance = NULL;

	public static function get_instance() {
		if ( NULL === self::$_instance ) {
			self::$_instance = new self;
		}
		return self::$_instance;
	}

	private function __construct() {
		$this->add_endpoint( 'get_comment', array( $this, 'ajax_get_comment' ), array( 'allow_guest' => TRUE ) );
		$this->add_endpoint( 'add_comment', array( $this, 'ajax_add_comment' ) );
		$this->add_endpoint( 'edit_comment', array( $this, 'ajax_edit_comment' ) );
		$this->add_endpoint( 'delete_comment', array( $this, 'ajax_delete_comment' ) );

		add_filter( 'imagewalk_assets_data', array( $this, 'assets_data' ) );
	}

	/**
	 * Assets data.
	 */
	public function assets_data( $data ) {
		$data[ 'comment' ] = array(
			'template' => trim( Imagewalk::get_template( 'components/comment' ) ),
			'lang' => array(
				'delete' => __( 'Are you sure want to delete this comment?', 'imagewalk' )
			)
		);

		return $data;
	}

	/**
	 * Ajax endpoint to get a specific comment.
	 */
	public function ajax_get_comment() {
		$comment_id = (int) $_POST[ 'id' ];
		$comment = Imagewalk_Model_Comment::get( $comment_id );

		if ( is_wp_error( $comment ) ) {
			echo wp_json_encode( array( 'error' => $comment->get_error_message() ) );
		} else {
			echo wp_json_encode( array( 'success' => true, 'data' => $comment ) );
		}

		wp_die();
	}

	/**
	 * Ajax endpoint to add new comment to a post.
	 */
	public function ajax_add_comment() {
		$post_id = (int) $_POST[ 'id' ];
		$comment_content = trim( sanitize_text_field( $_POST[ 'content' ] ) );
		$comment = Imagewalk_Model_Comment::add( $post_id, $comment_content );

		if ( is_wp_error( $comment_data ) ) {
			echo wp_json_encode( array( 'error' => $comment->get_error_message() ) );
		} else {
			echo wp_json_encode( array( 'success' => true, 'data' => $comment ) );
		}

		wp_die();
	}

	/**
	 * Ajax endpoint to edit an existing comment.
	 */
	public function ajax_edit_comment() {
		$comment_id = (int) $_POST[ 'id' ];
		$comment_content = trim( sanitize_text_field( $_POST[ 'content' ] ) );
		$comment = Imagewalk_Model_Comment::edit( $comment_id, $comment_content );

		if ( is_wp_error( $comment ) ) {
			echo wp_json_encode( array( 'error' => $comment->get_error_message() ) );
		} else {
			echo wp_json_encode( array( 'success' => true, 'data' => $comment ) );
		}

		wp_die();
	}

	/**
	 * Ajax endpoint to delete an existing comment.
	 */
	public function ajax_delete_comment() {
		$comment_id = (int) $_POST[ 'id' ];
		$result = Imagewalk_Model_Comment::delete( $comment_id );

		if ( is_wp_error( $result ) ) {
			echo wp_json_encode( array( 'error' => $result->get_error_message() ) );
		} else {
			echo wp_json_encode( array( 'success' => true ) );
		}

		wp_die();
	}
}
