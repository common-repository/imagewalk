<?php

class Imagewalk_Model_Posts {
	private static $_query_compare = NULL;

	/**
	 * Get posts.
	 *
	 * @param int $user_id
	 * @param int $limit
	 * @param string $compare
	 * @return array|WP_Error
	 */
	public static function get( $limit = 2, $user_id = FALSE, $compare = FALSE ) {
		$limit = min( 10, max( 1, $limit ) );

		self::$_query_compare = NULL;
		if ( $compare && preg_match( '#^([<>])(\d+)$#', $compare, $matches ) ) {
			self::$_query_compare = array( $matches[ 1 ], (int) $matches[ 2 ] );
		}

		add_filter( 'posts_where', array( 'Imagewalk_Model_Posts', 'query_compare_set' ) );
		$query = new WP_Query( self::query_args( $limit, $user_id ) );
		remove_filter( 'posts_where', array( 'Imagewalk_Model_Posts', 'query_compare_set' ) );

		self::$_query_compare = NULL;

		return self::query_result( $query );
	}

	/**
	 * WP_Query arguments builder.
	 *
	 * @param int $limit
	 * @param int $user_id
	 * @return array
	 */
	private static function query_args( $limit, $user_id = FALSE ) {
		$order_asc = self::$_query_compare && self::$_query_compare[ 0 ] === '>';

		$args = array(
			'post_type' => Imagewalk::$post_type,
			'post_status' => 'publish',
			'posts_per_page' => $limit,
			'order' => $order_asc ? 'ASC' : 'DESC',
			'orderby' => 'ID'
		);

		if ( $user_id ) {
			$args[ 'author' ] = $user_id;
		}

		return $args;
	}

	/**
	 * WP_Query posts_where clause filter.
	 *
	 * @param string $where
	 * @return string
	 */
	public static function query_compare_set( $where = '' ) {
		if ( self::$_query_compare ) {
			$where .= ' AND wp_posts.ID ' . implode( ' ', self::$_query_compare );
		}

		return $where;
	}

	/**
	 * WP_Query result formatter.
	 *
	 * @param WP_Query $query
	 * @return array
	 */
	private static function query_result( $query ) {
		$posts = array();

		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();
				$posts[] = Imagewalk_Model_Post::get( get_post() );
			}

			wp_reset_postdata();
		}

		return $posts;
	}
}
