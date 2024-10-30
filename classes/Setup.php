<?php

class Imagewalk_Setup {
	public static function activate() {
		do_action( 'imagewalk_activate', get_pages( array( 'post_status', '' ) ) );
		flush_rewrite_rules();
	}

	public static function deactivate() {
		do_action( 'imagewalk_deactivate' );
		flush_rewrite_rules();
	}

	public static function uninstall() {
	}
}
