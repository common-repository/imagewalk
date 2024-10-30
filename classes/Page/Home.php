<?php

class Imagewalk_Page_Home extends Imagewalk_Page_Abstract {
	private static $_instance = NULL;

	public static function get_instance() {
		if ( NULL === self::$_instance ) {
			self::$_instance = new self;
		}
		return self::$_instance;
	}

	protected function __construct() {
		parent::__construct( 'home' );
	}

	/**
	 * Default homepage name (slug).
	 *
	 * @return string
	 */
	protected function get_default_name() {
		return 'photos';
	}

	/**
	 * Default homepage title.
	 *
	 * @return string
	 */
	protected function get_default_title() {
		return __( 'Photos', 'imagewalk' );
	}
}
