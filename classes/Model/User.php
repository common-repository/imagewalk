<?php

class Imagewalk_Model_User {
	/**
	 * Get data from a specific user.
	 *
	 * @param int|WP_User $user
	 * @return array|null
	 */
	public static function get( $user ) {
		if ( ! $user instanceof WP_User ) {
			$user = get_userdata( $user );
			if ( ! $user ) {
				return NULL;
			}
		}

		$user_page = Imagewalk_Page_User::get_instance();

		// User data.
		$user_id = (int) $user->get( 'ID' );
		$user_name = $user->get( 'user_login' );
		$user_avatar = get_avatar_url( $user_id );
		$user_url = $user_page->get_url( $user_id );

		return array(
			'id' => $user_id,
			'name' => $user_name,
			'avatar' => $user_avatar,
			'url' => $user_url
		);
	}
}
