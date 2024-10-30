<?php

class Imagewalk_Model_Comments {
	private static $_query_compare = NULL;

	/**
	 * Get comments for a spesific post.
	 *
	 * @param int $post_id
	 * @param int $limit
	 * @param string $compare
	 * @return array
	 */
	public static function get( $post_id = 0, $limit = 2, $compare = FALSE ) {
		$comments = array();
		$comments_count = (int) get_comments_number( $post_id );
		$comments_more = FALSE;

		if ( $comments_count > 0 ) {
			$now = current_time( 'timestamp' );

			self::$_query_compare = NULL;
			if ( $compare && preg_match( '#^([<>])(\d+)$#', $compare, $matches ) ) {
				self::$_query_compare = array( $matches[ 1 ], (int) $matches[ 2 ] );
			}

			add_filter( 'comments_clauses', array( 'Imagewalk_Model_Comments', 'query_compare_set' ) );
			$query = new WP_Comment_Query;
			// Intentionally increase limit value by one to see if there are more comments available.
			// It still will return the maximum number of items as defined in the initial limit value.
			$comments_raw = $query->query( self::query_args( $post_id, $limit + 1 ) );
			remove_filter( 'comments_clauses', array( 'Imagewalk_Model_Comments', 'query_compare_set' ) );

			self::$_query_compare = NULL;

			if ( $comments_raw ) {
				if ( count( $comments_raw ) > $limit ) {
					$comments_more = TRUE;
					array_pop( $comments_raw );
				}

				$comments = self::query_result( $comments_raw );
			}
		}

		return array(
			'items' => $comments,
			'count' => $comments_count,
			'more' => $comments_more
		);
	}

	/**
	 * Comment search parameter builder.
	 *
	 * @param int $post_id
	 * @param int $limit
	 * @return array
	 */
	private static function query_args( $post_id, $limit ) {
		$order_asc = self::$_query_compare && self::$_query_compare[ 0 ] === '>';

		$args = array(
			'post_id' => $post_id,
			'number' => $limit,
			'order' => $order_asc ? 'ASC' : 'DESC',
			'orderby' => 'comment_ID'
		);

		return $args;
	}

	/**
	 * WP_Comment_Query comments_clauses filter.
	 *
	 * @param array $pieces
	 * @return array
	 */
	public static function query_compare_set( $pieces = array() ) {
		if ( self::$_query_compare ) {
			$pieces[ 'where' ] .= ' AND wp_comments.comment_ID ' . implode( ' ', self::$_query_compare );
		}

		return $pieces;
	}

	/**
	 * WP_Comment_Query result formatter.
	 *
	 * @param array $query
	 * @return array
	 */
	private static function query_result( $comments_raw ) {
		$comments = array();

		if ( $comments_raw ) {
			foreach ( $comments_raw as $comment ) {
				$comments[] = Imagewalk_Model_Comment::get( $comment );
			}
		}

		return array_reverse( $comments );
	}
}
