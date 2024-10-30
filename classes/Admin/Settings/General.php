<?php

class Imagewalk_Admin_Settings_General extends Imagewalk_Admin_Settings_Abstract {
	public static function info() {
		return array(
			'id' => 'general',
			'label' => __( 'General', 'imagewalk' )
		);
	}

	protected function get_configs() {
		return array(
			array(
				'id' => 'users_can_register',
				'title' => __( 'Membership', 'imagewalk' ),
				'description' => __( '<strong>Notice:</strong> This setting changes the WordPress user registration setting, which can be found <a href="options-general.php#users_can_register">here</a>.', 'imagewalk' ),
				'type' => 'checkbox',
				'value' => get_option( 'users_can_register', 0 ),
				'label' => __( 'Enable WordPress user registration.', 'imagewalk' ),
			),
			array(
				'title' => __( 'Posts', 'imagewalk' ),
				'configs' => array(
					array(
						'id' => 'imagewalk_stream_posts_initial',
						'title' => __( 'Initial posts loaded', 'imagewalk' ),
						'description' => __( 'The number of initial posts to be loaded on the stream.', 'imagewalk' ),
						'type' => 'select',
						'value' => get_option( 'imagewalk_stream_posts_initial', 1 ),
						'options' => range( 1, 5 )
					),
				)
			),
			array(
				'title' => __( 'Comments', 'imagewalk' ),
				'configs' => array(
					array(
						'id' => 'imagewalk_post_comments_initial',
						'title' => __( 'Initial comments loaded', 'imagewalk' ),
						'description' => __( 'The number of initial comments to be loaded on each post.', 'imagewalk' ),
						'type' => 'select',
						'value' => get_option( 'imagewalk_post_comments_initial', 2 ),
						'options' => range( 1, 5 )
					),
					array(
						'id' => 'imagewalk_post_comments_more',
						'title' => __( 'More comments loaded', 'imagewalk' ),
						'description' => __( 'The number of more comments to be loaded every time the "show more comments" button is clicked.', 'imagewalk' ),
						'type' => 'select',
						'value' => get_option( 'imagewalk_post_comments_more', 8 ),
						'options' => range( 5, 20 )
					),
				)
			),
		);
	}
}
