<?php

class Imagewalk {
	private static $_instance = NULL;
	private static $_base_path;
	private static $_base_url;

	public static $version = '1.0.1';
	public static $post_type = 'imagewalk';
	public static $text_domain = 'imagewalk';

	public static $allowed_img_types = array( 'image/jpeg', 'image/png' );
	public static $default_img_width = 500;
	public static $default_img_height = 500;

	public static function get_instance( $file ) {
		if ( NULL === self::$_instance ) {
			self::$_instance = new self( $file );
			self::$_base_path = plugin_dir_path( $file );
			self::$_base_url = plugin_dir_url( $file );
		}

		return self::$_instance;
	}

	private function __construct( $file ) {
		$this->register_endpoints();
		$this->register_shortcodes();
		$this->register_pages();

		add_action( 'init', array( $this, 'register_post_type' ) );
		add_action( 'widgets_init', array( $this, 'register_widgets' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'load_assets' ) );
		add_filter( 'post_type_link', array( $this, 'modify_post_type_link' ), 10, 2 );

		if ( is_admin() ) {
			// Administrator pages handling.
			new Imagewalk_Admin( $file );

			// Plugin setup hooks.
			register_activation_hook( $file, array( 'Imagewalk_Setup', 'activate' ) );
			register_deactivation_hook( $file, array( 'Imagewalk_Setup', 'deactivate' ) );
			register_uninstall_hook( $file, array( 'Imagewalk_Setup', 'uninstall' ) );
		}
	}

	/**
	 * Get predefined config.
	 *
	 * @param string $name
	 * @param mixed $default
	 * @param array $allowed
	 * @return mixed
	 */
	public static function get_config( $name, $default, $allowed = NULL ) {
		$value = $default;

		if ( defined( 'IMGW_' . $name ) ) {
			$config = constant( 'IMGW_' . $name );
			if ( ! is_array( $allowed ) || in_array( $config, $allowed ) ) {
				$value = $config;
			}
		}

		return $value;
	}

	/**
	 * Get URL of a stylesheet file.
	 *
	 * @param string $name
	 * @return string
	 */
	public static function get_css( $name ) {
		$base_url = self::$_base_url . 'assets' . DIRECTORY_SEPARATOR . 'css' . DIRECTORY_SEPARATOR;
		$file_url = $base_url . $name . '.css';

		return $file_url;
	}

	/**
	 * Get URL of a javascript file.
	 *
	 * @param string $name
	 * @return string
	 */
	public static function get_js( $name ) {
		$dir_sep = DIRECTORY_SEPARATOR;
		$base_url = self::$_base_url . 'assets' . $dir_sep . 'js' . $dir_sep . 'dist' . $dir_sep;
		$file_url = $base_url . $name . '.min.js';

		return $file_url;
	}

	/**
	 * Get template string.
	 *
	 * @param string $name
	 * @param object $data
	 * @return string
	 */
	public static function get_template( $name, $data = NULL ) {
		$base_path = self::$_base_path . 'templates' . DIRECTORY_SEPARATOR;
		$file_path = $base_path . $name . '.php';
		$template = '';

		if ( file_exists( $file_path ) ) {
			try {
				ob_start();
				// Template data.
				$_imagewalk_template_data = $data;
				include( $file_path );
				$template = ob_get_clean();
				// Contract multiple whitespaces.
				$template = preg_replace( '#\s+#', ' ', $template );
			} catch ( Exception $e ) {}
		}

		return $template;
	}

	/**
	 * Get home URL.
	 *
	 * @return string
	 */
	public static function get_home_url() {
		$home = Imagewalk_Page_Home::get_instance();
		$home_url = $home->get_url();

		if ( NULL === $home_url ) {
			return '';
		}

		return $home_url;
	}

	/**
	 * Custom post type registration.
	 */
	public function register_post_type() {
		$labels = array(
			'name' => __( 'Imagewalk Posts', 'imagewalk' ),
			'all_items' => __( 'All Posts', 'imagewalk' )
		);

		register_post_type( Imagewalk::$post_type, array(
			'public' => TRUE,
			'publicly_queryable' => TRUE,
			'show_ui' => TRUE,
			'show_in_menu' => Imagewalk::$post_type,

			'capability_type' => 'post',
			'capabilities' => array( 'create_posts' => 'do_not_allow' ),
			'map_meta_cap' => TRUE,
			'supports' => array( 'author', 'comments' ),

			'labels' => $labels,
		) );
	}

	/**
	 * Direct post type permalink to post page.
	 *
	 * @param string $post_link
	 * @param WP_Post $post
	 * @return string
	 */
	public function modify_post_type_link( $post_link, $post ) {
		if ( $post->post_type === Imagewalk::$post_type ) {
			$post_page = Imagewalk_Page_Post::get_instance();
			$post_url = $post_page->get_url( $post->ID );
			if ( $post_url ) {
				$post_link = $post_url;
			}
		}

		return $post_link;
	}

	/**
	 * Load main scripts and stylesheets.
	 */
	public function load_assets() {
		wp_register_style( 'imagewalk-deps', self::get_css( 'core' ), FALSE );

		wp_register_script( 'imagewalk-deps', self::get_js( 'core' ), FALSE, FALSE, TRUE );
		wp_localize_script( 'imagewalk-deps', 'imagewalk_data', apply_filters( 'imagewalk_assets_data', array(
			'site_url' => site_url(),
			'home_url' => home_url(),
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			'user_id' => get_current_user_id(),
			'is_multisite' => is_multisite(),
			'img_width' => self::get_config( 'IMG_WIDTH', self::$default_img_width ),
			'img_height' => self::get_config( 'IMG_HEIGHT', self::$default_img_height ),
		) ) );

		wp_enqueue_style( 'imagewalk-core', self::get_css( 'main' ), array( 'imagewalk-deps' ) );
		wp_enqueue_script( 'imagewalk-core', self::get_js( 'main' ), array( 'imagewalk-deps' ), FALSE, TRUE );
	}

	/**
	 * Register endpoint handlers.
	 */
	private function register_endpoints() {
		Imagewalk_Endpoint_Post::get_instance();
		Imagewalk_Endpoint_Posts::get_instance();
		Imagewalk_Endpoint_Comment::get_instance();
		Imagewalk_Endpoint_Comments::get_instance();
		Imagewalk_Endpoint_Uploader::get_instance();
	}

	/**
	 * Register shortcodes.
	 */
	private function register_shortcodes() {
		Imagewalk_Shortcode_Menubar::get_instance();
	}

	/**
	 * Register page handlers.
	 */
	private function register_pages() {
		Imagewalk_Page_Home::get_instance();
		Imagewalk_Page_Post::get_instance();
		Imagewalk_Page_User::get_instance();
	}

	/**
	 * Register widget handlers.
	 */
	public function register_widgets() {
		register_widget( 'Imagewalk_Widget_Menubar' );
	}
}
