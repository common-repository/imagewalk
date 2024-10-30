<?php

class Imagewalk_Model_Comment {
	/**
	 * Check whether the current user can modify a specific comment.
	 *
	 * @param int|WP_Comment $comment
	 * @return boolean
	 */
	public static function can_modify( $comment ) {
		$can_modify = FALSE;

		if ( is_user_logged_in() ) {
			// Editor role or above should be able to modify comment.
			if ( current_user_can( 'edit_others_posts' ) ) {
				$can_modify = TRUE;

			// Otherwise, comment author can modify his own comment.
			} else {
				if ( ! $comment instanceof WP_Comment ) {
					$comment = get_comment( $comment );
				}
				if ( $comment && ( (int) $comment->user_id === get_current_user_id() ) ) {
					$can_modify = TRUE;
				}
			}
		}

		return $can_modify;
	}

	/**
	 * Get a specific comment.
	 *
	 * @param int|WP_Comment $comment
	 * @return array|WP_Error
	 */
	public static function get( $comment ) {
		if ( ! $comment instanceof WP_Comment ) {
			$comment = get_comment( $comment );
			if ( ! $comment ) {
				return new WP_Error( 'error', __( 'Comment not found.', 'imagewalk' ) );
			}
		}

		// Comment data.
		$comment_id = (int) $comment->comment_ID;
		$comment_content = $comment->comment_content;
		$comment_date = human_time_diff( get_comment_date( 'U', $comment_id ), current_time( 'timestamp' ) );

		$comment_actions = array();
		if ( self::can_modify( $comment ) ) {
			$comment_actions[] = 'delete';
		}

		return array(
			'id' => $comment_id,
			'content' => $comment_content,
			'date' => __( $comment_date . ' ago', 'imagewalk' ),
			'actions' => $comment_actions,
			'user' => Imagewalk_Model_User::get( $comment->user_id )
		);
	}

	/**
	 * Add new comment to a post.
	 *
	 * @param int $post_id
	 * @param string $content
	 * @return object|WP_Error
	 */
	public static function add( $post_id = 0, $content = '' ) {
		if ( ! is_user_logged_in() ) {
			return new WP_Error( 'error', __( 'User cannot add comment.', 'imagewalk' ) );
		}

		$user = wp_get_current_user();

		// Insert comment.
		$comment_id = wp_insert_comment( array(
			'user_id' => $user->ID,
			'comment_author' => $user->user_login,
			'comment_post_ID' => $post_id,
			'comment_content' => esc_html( $content )
		), TRUE );

		if ( is_wp_error( $comment_id ) ) {
			return new WP_Error( 'error', $comment_id->get_error_message() );
		}

		return self::get( $comment_id );
	}

	/**
	 * Edit an existing comment.
	 *
	 * @param int $comment_id
	 * @param string $content
	 * @return object|WP_Error
	 */
	public static function edit( $comment_id = 0, $content = '' ) {
		$comment = get_comment( $comment_id );
		if ( ! $comment ) {
			return new WP_Error( 'error', __( 'Comment not found.', 'imagewalk' ) );
		}

		if ( ! self::can_modify( $comment ) ) {
			return new WP_Error( 'error', __( 'User cannot edit comment.', 'imagewalk' ) );
		}

		$comment_result = wp_update_comment( array(
			'comment_ID' => $comment->comment_ID,
			'comment_content' => esc_html( $content )
		) );

		if ( ! $comment_result ) {
			return new WP_Error( 'error', __( 'Edit comment failed.', 'imagewalk' ) );
		}

		return self::get( $comment_id );
	}

	/**
	 * Delete an existing comment.
	 *
	 * @param int $comment_id
	 * @return boolean|WP_Error
	 */
	public static function delete( $comment_id = 0 ) {
		$comment = get_comment( $comment_id );
		if ( ! $comment ) {
			return new WP_Error( 'error', __( 'Comment not found.', 'imagewalk' ) );
		}

		if ( ! self::can_modify( $comment ) ) {
			return new WP_Error( 'error', __( 'User cannot delete comment.', 'imagewalk' ) );
		}

		$result = wp_delete_comment( $comment_id );
		if ( ! $result ) {
			return new WP_Error( 'error', __( 'Delete comment failed.', 'imagewalk' ) );
		}

		return TRUE;
	}
}
