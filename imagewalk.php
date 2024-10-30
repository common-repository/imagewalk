<?php
/**
 * Plugin Name: Imagewalk
 * Description: Instant image sharing plugin for WordPress. Imagewalk adds an image sharing feature to your site, which lets users share images or photos directly from their browser.
 * Version: 1.0.1
 * Author: Imagewalk
 * Author URI: https://imagewalk.io/
 * Text Domain: imagewalk
 * Domain Path: /languages/
 */

spl_autoload_register( function ( $class_name ) {
	if ( 0 === strpos( $class_name, 'Imagewalk' ) ) {
		$class_dir = realpath( plugin_dir_path( __FILE__ ) ) . DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR;
		$class_name = str_replace( 'Imagewalk_', '', $class_name );
		$class_file = str_replace( '_', DIRECTORY_SEPARATOR, $class_name ) . '.php';
		$class_path = $class_dir . $class_file;
		if ( file_exists( $class_path ) ) {
			require_once $class_path;
		}
	}
} );

Imagewalk::get_instance( __FILE__ );
