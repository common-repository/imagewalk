<?php

class Imagewalk_Admin_Settings_Navigation extends Imagewalk_Admin_Settings_Abstract {
	public static function info() {
		return array(
			'id' => 'navigation',
			'label' => __( 'Navigation', 'imagewalk' )
		);
	}

	protected function get_configs() {
		$pages = get_pages( array( 'post_status', '' ) );

		// Fix $pages parameter does not return array if it only has a single result.
		if ( ! is_array( $pages ) ) {
			$pages = array( $pages );
		}

		$page_home_shortcode = Imagewalk_Page_Home::get_instance()->get_shortcode();
		$page_post_shortcode = Imagewalk_Page_Post::get_instance()->get_shortcode();
		$page_user_shortcode = Imagewalk_Page_User::get_instance()->get_shortcode();

		return array(
			array(
				'id' => 'imagewalk_page_home',
				'title' => __( 'Stream page', 'imagewalk' ),
				'description' => __( 'Only pages which have <code>[' . $page_home_shortcode . ']</code> shortcode can be selected.', 'imagewalk' ),
				'type' => 'select',
				'value' => get_option( 'imagewalk_page_home' ),
				'options' => $this->get_pages_home( $pages )
			),
			array(
				'id' => 'imagewalk_page_post',
				'title' => __( 'Single post page', 'imagewalk' ),
				'description' => __( 'Only pages which have <code>[' . $page_post_shortcode . ']</code> shortcode can be selected.', 'imagewalk' ),
				'type' => 'select',
				'value' => get_option( 'imagewalk_page_post' ),
				'options' => $this->get_pages_post( $pages )
			),
			array(
				'id' => 'imagewalk_page_user',
				'title' => __( 'User profile page', 'imagewalk' ),
				'description' => __( 'Only pages which have <code>[' . $page_user_shortcode . ']</code> shortcode can be selected.', 'imagewalk' ),
				'type' => 'select',
				'value' => get_option( 'imagewalk_page_user' ),
				'options' => $this->get_pages_user( $pages )
			),
		);
	}

	protected function save() {
		parent::save();
		flush_rewrite_rules();
	}

	/**
	 * Get all pages which contain home page shortcode.
	 *
	 * @param array $pages
	 * @return array
	 */
	private function get_pages_home( $pages ) {
		$page_home = Imagewalk_Page_Home::get_instance();
		$page_home_shortcode = $page_home->get_shortcode();

		$pages_found = array();

		foreach ( $pages as $page ) {
			if ( has_shortcode( $page->post_content, $page_home_shortcode ) ) {
				$pages_found[] = array(
					'value' => $page->ID,
					'label' => $page->post_title
				);
			}
		}

		return $pages_found;
	}

	/**
	 * Get all pages which contain post page shortcode.
	 *
	 * @param array $pages
	 * @return array
	 */
	private function get_pages_post( $pages ) {
		$page_post = Imagewalk_Page_Post::get_instance();
		$page_post_shortcode = $page_post->get_shortcode();

		$pages_found = array();

		foreach ( $pages as $page ) {
			if ( has_shortcode( $page->post_content, $page_post_shortcode ) ) {
				$pages_found[] = array(
					'value' => $page->ID,
					'label' => $page->post_title
				);
			}
		}

		return $pages_found;
	}

	/**
	 * Get all pages which contain user page shortcode.
	 *
	 * @param array $pages
	 * @return array
	 */
	private function get_pages_user( $pages ) {
		$page_user = Imagewalk_Page_User::get_instance();
		$page_user_shortcode = $page_user->get_shortcode();

		$pages_found = array();

		foreach ( $pages as $page ) {
			if ( has_shortcode( $page->post_content, $page_user_shortcode ) ) {
				$pages_found[] = array(
					'value' => $page->ID,
					'label' => $page->post_title
				);
			}
		}

		return $pages_found;
	}

}
