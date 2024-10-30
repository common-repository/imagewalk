<?php

class Imagewalk_Model_Post {
	/**
	 * Check whether the current user can modify a specific post.
	 *
	 * @param int|WP_Post $post
	 * @return boolean
	 */
	public static function can_modify( $post ) {
		$can_modify = FALSE;

		if ( is_user_logged_in() ) {
			// Editor role or above should be able to modify post.
			if ( current_user_can( 'edit_others_posts' ) ) {
				$can_modify = TRUE;

			// Otherwise, post author can modify his own post.
			} else {
				if ( ! $post instanceof WP_Post ) {
					$post = get_post( $post );
				}
				if ( $post && ( (int) $post->post_author === get_current_user_id() ) ) {
					$can_modify = TRUE;
				}
			}
		}

		return $can_modify;
	}

	/**
	 * Get data from a specific post.
	 *
	 * @param int|WP_Post $post
	 * @return array|WP_Error
	 */
	public static function get( $post ) {
		if ( ! $post instanceof WP_Post ) {
			$post = get_post( $post );
			if ( ! $post ) {
				return new WP_Error( 'error', __( 'Post not found.', 'imagewalk' ) );
			}
		}

		$post_page = Imagewalk_Page_Post::get_instance();

		// Post data.
		$post_id = (int) $post->ID;
		$post_thumbnail = get_the_post_thumbnail( $post );
		$post_caption = $post->post_content;
		$post_date = human_time_diff( get_the_time( 'U', $post ), current_time( 'timestamp' ) );
		$post_url = $post_page->get_url( $post_id );
		$post_comment_allow = 'open' === $post->comment_status;

		$post_actions = array();
		if ( self::can_modify( $post ) ) {
			$post_actions[] = 'delete';
		}

		return array(
			'id' => $post_id,
			'thumbnail' => $post_thumbnail,
			'caption' => $post_caption,
			'date' => __( $post_date . ' ago', 'imagewalk' ),
			'url' => $post_url,
			'actions' => $post_actions,
			'comment_allow' => $post_comment_allow,
			'comments' => Imagewalk_Model_Comments::get( $post_id, get_option( 'imagewalk_post_comments_initial', 2 ) ),
			'user' => Imagewalk_Model_User::get( $post->post_author ),
		);
	}

	/**
	 * Add new post.
	 *
	 * @param array $post_data
	 * @return object|WP_Error
	 */
	public static function add( $post_data = array() ) {
		if ( ! is_user_logged_in() ) {
			return new WP_Error( 'error', __( 'User cannot add post.', 'imagewalk' ) );
		}

		// Insert post.
		$post_content = trim( wp_strip_all_tags( $post_data[ 'caption' ] ) );
		$post_id = wp_insert_post( array(
			'post_type' => Imagewalk::$post_type,
			'post_title' => '',
			'post_content' => $post_content,
			'post_status' => 'publish',
			'comment_status' => 'open'
		), TRUE );

		if ( is_wp_error( $post_id ) ) {
			return new WP_Error( 'error', $post_id->get_error_message() );
		}

		// Set post thumbnail.
		set_post_thumbnail( $post_id, $post_data[ 'thumbnail' ] );

		return self::get( $post_id );
	}

	/**
	 * Delete an existing post.
	 *
	 * @param int $post_id
	 * @return boolean|WP_Error
	 */
	public static function delete( $post_id = 0 ) {
		$post = get_post( $post_id );
		if ( ! $post ) {
			return new WP_Error( 'error', __( 'Post not found.', 'imagewalk' ) );
		}

		if ( ! self::can_modify( $post ) ) {
			return new WP_Error( 'error', __( 'User cannot delete post.', 'imagewalk' ) );
		}

		$result = wp_delete_post( $post_id );
		if ( ! $result ) {
			return new WP_Error( 'error', __( 'Delete post failed.', 'imagewalk' ) );
		}

		return TRUE;
	}
}
