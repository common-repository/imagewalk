<?php

abstract class Imagewalk_Page_Abstract {
	protected $page = NULL;
	protected $option_key = NULL;
	protected $shortcode = NULL;
	protected $template = NULL;

	protected $url = NULL;
	protected $has_permastruct = NULL;

	abstract protected function get_default_name();
	abstract protected function get_default_title();

	protected function __construct( $page_id = 'page' ) {
		$this->option_key = 'imagewalk_page_' . $page_id;
		$this->shortcode = 'imagewalk_' . $page_id;
		$this->template = 'pages/' . $page_id;

		$this->register_shortcode();

		add_action( 'imagewalk_activate', array( $this, 'maybe_register_page' ) );
		add_action( 'imagewalk_deactivate', array( $this, 'deregister_page' ) );

		add_filter( 'generate_rewrite_rules', array( $this, 'wp_generate_rewrite_rules' ) );
		add_filter( 'get_canonical_url', array( $this, 'wp_get_canonical_url' ), 10, 2 );
		add_filter( 'query_vars', array( $this, 'wp_query_vars' ) );
	}

	/**
	 * Handle shortcode registration.
	 */
	private function register_shortcode() {
		add_shortcode( $this->shortcode, function () {
			return Imagewalk::get_template( $this->template );
		} );
	}

	/**
	 * Get page shortcode.
	 *
	 * @return string
	 */
	public function get_shortcode() {
		return $this->shortcode;
	}

	/**
	 * Get registered page.
	 *
	 * @return WP_Post|null
	 */
	public function get_page() {
		$page = $this->page;
		if ( $page instanceof WP_Post ) {
			return $page;
		}

		$page_id = get_option( $this->option_key, NULL );
		if ( $page_id ) {
			$page = get_post( $page_id );
			if ( $page instanceof WP_Post ) {
				$this->page = $page;
				return $page;
			}
		}

		return NULL;
	}

	/**
	 * Get page url.
	 *
	 * @return string|null
	 */
	public function get_url() {
		if ( NULL === $this->url ) {
			$page = $this->get_page();
			$this->url = $page ? get_permalink( $page ) : FALSE;
			$this->has_permastruct = get_option( 'permalink_structure' ) ? TRUE : FALSE;
		}

		if ( FALSE === $this->url ) {
			return NULL;
		}

		return $this->url;
	}

	/**
	 * Handle page setup and registration if necessary.
	 *
	 * @param array $pages
	 */
	public function maybe_register_page( $pages, $opts = array() ) {
		$page_found = $this->get_page();

		if ( $page_found ) {
			// Current page still contains shortcode, no need to create a new page.
			if ( has_shortcode( $page_found->post_content, $this->shortcode ) ) {
				return;
			}
		}

		// Fix $pages parameter does not return array if it only has a single result.
		if ( ! is_array( $pages ) ) {
			$pages = array( $pages );
		}

		// Use an existing page if it already contains shortcode.
		foreach ( $pages as $page ) {
			if ( has_shortcode( $page->post_content, $this->shortcode ) ) {
				$this->page = $page_found = $page;
				update_option( $this->option_key, $page->ID, FALSE );
				break;
			}
		}

		if ( ! $page_found ) {
			// Always add menubar shortcode for newly-created page.
			$menubar = Imagewalk_Shortcode_Menubar::get_instance();
			$default_content = $menubar->get_tag() . " [$this->shortcode]";

			$page_id = wp_insert_post( array(
				'post_type' => 'page',
				'post_name' => $this->get_default_name(),
				'post_title' => $this->get_default_title(),
				'post_content' => $default_content,
				'post_status' => 'publish'
			), TRUE );

			if ( ! is_wp_error( $page_id ) ) {
				$this->page = get_post( $page_id );
				update_option( $this->option_key, $page_id, FALSE );
			}
		}
	}

	/**
	 * Remove registered page.
	 */
	public function deregister_page() {
		$this->page = NULL;
		delete_option( $this->option_key );
	}

	/**
	 * Register rewrite rules for this page.
	 *
	 * @param WP_Rewrite $wp_rewrite
	 * @return array
	 */
	public function wp_generate_rewrite_rules( $wp_rewrite ) {
		$page = $this->get_page();
		if ( ! $page ) {
			return;
		}

		$page_uri = get_page_uri( $page );
		$page_rules = array(
			"^$page_uri/([^/]+)/?" => 'index.php?page_id=' . $page->ID . '&imgw_id=$matches[1]'
		);

		$wp_rewrite->rules = $page_rules + $wp_rewrite->rules;
		return $wp_rewrite->rules;
	}

	/**
	 * Modify canonical URL for this page.
	 *
	 * @param array $query_vars
	 * @return array
	 */
	public function wp_get_canonical_url( $url, $post ) {
		$page = $this->get_page();
		if ( $page && $page->ID === $post->ID ) {
			$imgw_id = get_query_var( 'imgw_id' );
			if ( $imgw_id ) {
				if ( get_option( 'permalink_structure' ) ) {
					$url = trailingslashit( $url ) . user_trailingslashit( $imgw_id );
				} else {
					$url = add_query_arg( 'imgw_id', $imgw_id, $url );
				}
			}
		}

		return $url;
	}

	/**
	 * Register required query variable for this page.
	 *
	 * @param array $query_vars
	 * @return array
	 */
	public function wp_query_vars( $query_vars ) {
		if ( ! in_array( 'imgw_id', $query_vars ) ) {
			$query_vars[] = 'imgw_id';
		}

		return $query_vars;
	}
}
