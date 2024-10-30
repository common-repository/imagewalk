<?php

class Imagewalk_Admin {
	public function __construct( $file ) {
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'load_assets' ) );
		add_filter( 'plugin_action_links_' . plugin_basename( $file ), array( $this, 'action_links' ) );
		add_filter( 'the_title', array( $this, 'the_title' ), 10, 2 );

		new Imagewalk_Admin_Notices();
	}

	/**
	 * Attach admin menu to the Dashboard.
	 */
	public function admin_menu() {
		$icon_base64 = 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIxMDAlIiBoZWlnaHQ9IjEwMCUiIHZpZXdCb3g9IjAgMCAyNCAyNCI+PHBhdGggZmlsbD0icmdiKDE1OCwxNjMsMTY4KSIgZD0iTTEzLjgxIDIuODZjLjE3LS4zIDAtLjctLjM1LS43NC0yLjYyLS4zNy01LjMuMjgtNy40NCAxLjg2LS4xOS4xNS0uMjUuNDMtLjEyLjY1bDMuMDEgNS4yMmMuMTkuMzMuNjcuMzMuODcgMGw0LjAzLTYuOTl6bTcuNDkgNS40N2MtLjk4LTIuNDctMi45Mi00LjQ2LTUuMzUtNS41LS4yMy0uMS0uNSAwLS42My4yMmwtMy4wMSA1LjIxYy0uMTkuMzIuMDUuNzQuNDQuNzRoOC4wOGMuMzUgMCAuNi0uMzUuNDctLjY3em0uMDcgMS42N2gtNi4yYy0uMzggMC0uNjMuNDItLjQzLjc1TDE5IDE4LjE0Yy4xNy4zLjYuMzUuODIuMDggMS43NC0yLjE4IDIuNDgtNS4wMyAyLjA1LTcuNzktLjAzLS4yNS0uMjUtLjQzLS41LS40M3pNNC4xOCA1Ljc5Yy0xLjczIDIuMTktMi40OCA1LjAyLTIuMDUgNy43OS4wMy4yNC4yNS40Mi41LjQyaDYuMmMuMzggMCAuNjMtLjQyLjQzLS43NUw1IDUuODdjLS4xOC0uMy0uNjEtLjM1LS44Mi0uMDh6TTIuNyAxNS42N2MuOTggMi40NyAyLjkyIDQuNDYgNS4zNSA1LjUuMjMuMS41IDAgLjYzLS4yMmwzLjAxLTUuMjFjLjE5LS4zMy0uMDUtLjc1LS40My0uNzVIMy4xN2MtLjM1LjAxLS42LjM2LS40Ny42OHptNy44MyA2LjIyYzIuNjIuMzcgNS4zLS4yOCA3LjQ0LTEuODYuMi0uMTUuMjYtLjQ0LjEzLS42NmwtMy4wMS01LjIyYy0uMTktLjMzLS42Ny0uMzMtLjg3IDBsLTQuMDQgNi45OWMtLjE3LjMuMDEuNy4zNS43NXoiLz48L3N2Zz4K';

		// Main menu.
		add_menu_page(
			'Imagewalk',
			'Imagewalk',
			'activate_plugins',
			Imagewalk::$post_type,
			'',
			$icon_base64,
			100
		);

		// Settings page menu.
		add_submenu_page(
			Imagewalk::$post_type,
			__( 'Settings', 'imagewalk' ),
			__( 'Settings', 'imagewalk' ),
			'activate_plugins',
			Imagewalk::$post_type . '-settings',
			array( $this, 'render_page_settings' )
		);
	}

	/**
	 * Render settings page.
	 */
	public function render_page_settings() {
		$tabs = array(
			Imagewalk_Admin_Settings_General::info(),
			Imagewalk_Admin_Settings_Navigation::info()
		);

		$tabs = apply_filters( 'imagewalk_admin_settings_tabs', $tabs );
		$tab = isset( $_GET[ 'tab' ] ) ? sanitize_title( $_GET[ 'tab' ] ) : $tabs[ 0 ][ 'id' ];

		$class_name = 'Imagewalk_Admin_Settings_' . ucfirst( $tab );
		if ( class_exists( $class_name ) ) {
			echo Imagewalk::get_template( 'admin/settings/header', array( 'tabs' => $tabs, 'tab' => $tab ) );
			new $class_name;
			echo Imagewalk::get_template( 'admin/settings/footer' );
		}
	}

	/**
	 * Load admin scripts and stylesheets.
	 */
	public function load_assets() {
		wp_enqueue_style( 'imagewalk-admin', Imagewalk::get_css( 'admin' ), FALSE );
	}

	/**
	 * Add additional plugin action links.
	 *
	 * @param array $links
	 * @return $links
	 */
	public function action_links( $links ) {
		$link = '<a href="' . admin_url( 'admin.php?page=' . Imagewalk::$post_type . '-settings' ) . '">'
			. __( 'Settings', 'imagewalk' ) . '</a>';

		array_unshift( $links, $link );

		return $links;
	}

	/**
	 * Filter empty post title.
	 *
	 * @param string $title
	 * @param int $post_id
	 * @return string
	 */
	public function the_title( $title, $post_id ) {
		if ( empty( $title ) ) {
			$post = get_post( $post_id );
			if ( $post && $post->post_type === Imagewalk::$post_type ) {
				$post_content = $post->post_content;
				if ( empty( $post_content ) ) {
					$post_content = __( '(no caption)', 'imagewalk' );
				}
				$title = $post_id . ' - ' . $post_content;
			}
		}

		return $title;
	}
}
