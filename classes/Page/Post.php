<?php

class Imagewalk_Page_Post extends Imagewalk_Page_Abstract {
	private static $_instance = NULL;

	public static function get_instance() {
		if ( NULL === self::$_instance ) {
			self::$_instance = new self;
		}
		return self::$_instance;
	}

	protected function __construct() {
		parent::__construct( 'post' );

		if ( ! is_admin() ) {
			add_filter( 'pre_get_document_title', array( $this, 'set_document_title' ) );
			add_filter( 'the_title', array( $this, 'set_page_title' ), 10, 2 );
		}
	}

	/**
	 * Default post page name (slug).
	 *
	 * @return string
	 */
	protected function get_default_name() {
		return 'p';
	}

	/**
	 * Default post page title.
	 *
	 * @return string
	 */
	protected function get_default_title() {
		return __( 'Post', 'imagewalk' );
	}

	/**
	 * Get post page url.
	 *
	 * @param int $post_id
	 * @return string|null
	 */
	public function get_url( $post_id = NULL ) {
		$url = parent::get_url();

		if ( FALSE === $url ) {
			return NULL;
		}

		if ( NULL === $post_id ) {
			return $url;
		}

		if ( $this->has_permastruct ) {
			return trailingslashit( $url ) . user_trailingslashit( $post_id );
		}

		return add_query_arg( 'imgw_id', $post_id, $url );
	}

	/**
	 * Override document title on post page.
	 *
	 * @param string $title
	 * @return string
	 */
	public function set_document_title( $title ) {
		global $post;

		$page = $this->get_page();

		if ( $post->ID === $page->ID ) {
			$post_id = get_query_var( 'imgw_id' );
			$post_data = Imagewalk_Model_Post::get( $post_id );

			if ( $post_data ) {
				$user = $post_data[ 'user' ];
				return sprintf(
					__( 'Post by %s &mdash; %s', 'imagewalk' ),
					$user[ 'name' ],
					$post_data[ 'date' ]
				);
			}
		}

		return $title;
	}

	/**
	 * Override page title on post page.
	 *
	 * @param string $title
	 * @param int $id
	 * @return string
	 */
	public function set_page_title( $title, $id ) {
		global $post;

		if ( ! $post || $post->ID !== $id ) {
			return $title;
		}

		$page = $this->get_page();
		if ( $id === $page->ID ) {
			$post_id = get_query_var( 'imgw_id' );
			$post_data = Imagewalk_Model_Post::get( $post_id );

			if ( $post_data ) {
				$user = $post_data[ 'user' ];
				return sprintf(
					__( 'Post by %s', 'imagewalk' ),
					$user[ 'name' ]
				);
			}
		}

		return $title;
	}
}
