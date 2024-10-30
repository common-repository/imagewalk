<?php

class Imagewalk_Shortcode_Menubar {
	// Singleton pattern.
	private static $_instance = NULL;
	public static function get_instance() {
		if ( NULL === self::$_instance ) {
			self::$_instance = new self;
		}
		return self::$_instance;
	}

	private $_shortcode = 'imagewalk_menubar';
	private $_shortcode_atts = array(
		'upload_button' => '1'
	);

	private function __construct() {
		add_shortcode( $this->_shortcode, function ( $atts ) {
			$atts = shortcode_atts( $this->_shortcode_atts, $atts );
			return Imagewalk::get_template( 'shortcodes/menubar', $atts );
		} );
	}

	/**
	 * Get shortcode.
	 *
	 * @return string
	 */
	public function get_shortcode() {
		return $this->_shortcode;
	}

	/**
	 * Get shortcode tag.
	 *
	 * @param array $atts
	 * @return string
	 */
	public function get_tag( $atts = array() ) {
		$atts = shortcode_atts( $this->_shortcode_atts, $atts );

		// Build shortcode tag.
		$shortcode = '[' . $this->_shortcode;
		foreach ( $atts as $key => $value ) {
			$shortcode .= ' ' . $key . '="' . $value . '"';
		}
		$shortcode .= ']';

		return $shortcode;
	}
}
