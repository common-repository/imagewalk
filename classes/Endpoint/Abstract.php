<?php

abstract class Imagewalk_Endpoint_Abstract {
	/**
	 * Register an ajax endpoint.
	 *
	 * @param string $name
	 * @param callable $handler
	 * @param object $opts
	 */
	protected function add_endpoint( $name, $handler, $opts = array() ) {
		add_action( 'wp_ajax_imagewalk_' . $name, $handler );
		if ( isset( $opts[ 'allow_guest' ] ) && $opts[ 'allow_guest' ] ) {
			add_action( 'wp_ajax_nopriv_imagewalk_' . $name, $handler );
		}
	}
}
