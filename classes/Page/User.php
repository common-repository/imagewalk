<?php

class Imagewalk_Page_User extends Imagewalk_Page_Abstract {
	private static $_instance = NULL;

	public static function get_instance() {
		if ( NULL === self::$_instance ) {
			self::$_instance = new self;
		}
		return self::$_instance;
	}

	protected function __construct() {
		parent::__construct( 'user' );

		if ( ! is_admin() ) {
			add_filter( 'pre_get_document_title', array( $this, 'set_document_title' ) );
			add_filter( 'the_title', array( $this, 'set_page_title' ), 10, 2 );
		}
	}

	/**
	 * Default user page name (slug).
	 *
	 * @return string
	 */
	protected function get_default_name() {
		return 'u';
	}

	/**
	 * Default user page title.
	 *
	 * @return string
	 */
	protected function get_default_title() {
		return __( 'User', 'imagewalk' );
	}

	/**
	 * Get user page url.
	 *
	 * @param int $user_id
	 * @return string|null
	 */
	public function get_url( $user_id = NULL ) {
		$url = parent::get_url();

		if ( FALSE === $url ) {
			return NULL;
		}

		if ( NULL === $user_id ) {
			return $url;
		}

		$user = get_userdata( $user_id );
		if ( ! $user ) {
			return NULL;
		}

		$user_name = $user->get( 'user_login' );

		if ( $this->has_permastruct ) {
			return trailingslashit( $url ) . user_trailingslashit( $user_name );
		}

		return add_query_arg( 'imgw_id', $user_name, $url );
	}

	/**
	 * Override document title on user page.
	 *
	 * @param string $title
	 * @return string
	 */
	public function set_document_title( $title ) {
		global $post;

		$page = $this->get_page();
		if ( $post->ID === $page->ID ) {
			$user_name = get_query_var( 'imgw_id' );
			$user_id = NULL;

			if ( $user_name ) {
				$user = get_user_by( 'login', $user_name );
				if ( $user instanceof WP_User ) {
					$user_id = (int) $user->get( 'ID' );
				}
			} else if ( is_user_logged_in() ) {
				$user_id = get_current_user_id();
			}

			if ( $user_id ) {
				$user = Imagewalk_Model_User::get( $user_id );

				return __( $user[ 'name' ], 'imagewalk' );
			}
		}

		return $title;
	}

	/**
	 * Override page title on user page.
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
			$user_name = get_query_var( 'imgw_id' );
			$user_id = NULL;

			if ( $user_name ) {
				$user = get_user_by( 'login', $user_name );
				if ( $user instanceof WP_User ) {
					$user_id = (int) $user->get( 'ID' );
				}
			} else if ( is_user_logged_in() ) {
				$user_id = get_current_user_id();
			}

			if ( $user_id ) {
				$user = Imagewalk_Model_User::get( $user_id );

				return __( $user[ 'name' ], 'imagewalk' );
			}
		}

		return $title;
	}
}
