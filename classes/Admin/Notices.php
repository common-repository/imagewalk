<?php

class Imagewalk_Admin_Notices {
	public function __construct() {
		add_action( 'wp_ajax_imagewalk_notice_dismiss', array( $this, 'ajax_notice_dismiss' ) );
		add_action( 'admin_notices', array( $this, 'admin_notices' ) );
	}

	public function ajax_notice_dismiss( $links ) {
		$id = isset( $_POST[ 'id' ] ) ? sanitize_key( $_POST[ 'id' ] ) : '';

		if ( $id ) {
			update_option( 'imagewalk_notice_dismiss_' . $id, 1 );
		}

		wp_die();
	}

	public function admin_notices() {
		$this->admin_notices_greeting();
		$this->admin_notices_no_register();
	}

	private function admin_notices_greeting() {
		if ( (int) get_option( 'imagewalk_notice_dismiss_greeting' ) ) {
			return;
		}

		$message = __( '<strong>Imagewalk</strong> is successfully installed. Click <a href="%s" target="_blank">here</a> to create your first post!', 'imagewalk' );
		$page_home = Imagewalk_Page_Home::get_instance();

		?>
		<div class="notice notice-success is-dismissible" data-imagewalk="notice-greeting">
			<p><?php printf( $message, $page_home->get_url() ); ?></p>
		</div>
		<script type="text/javascript">
			jQuery( function( $ ) {
				var $notice = $( '.notice[data-imagewalk=notice-greeting]' )
				$notice.on( 'click', '.notice-dismiss', function() {
					var data = { action: 'imagewalk_notice_dismiss', id: 'greeting' }
					$.post( ajaxurl, data );
				} );
			} );
		</script>
		<?php
	}

	private function admin_notices_no_register() {
		if ( (int) get_option( 'imagewalk_notice_dismiss_no_register' ) ) {
			return;
		}

		if ( (int) get_option( 'users_can_register' ) ) {
			return;
		}

		$message = __( '<strong>Imagewalk</strong>: WordPress user registration on is currently disabled. Go to the <a href="%s">settings page</a> to enable it so that anyone can join the community.', 'imagewalk' );
		$settings_page_url = 'admin.php?page=' . Imagewalk::$post_type . '-settings';

		?>
		<div class="notice notice-warning is-dismissible" data-imagewalk="notice-no-register">
			<p><?php printf( $message, $settings_page_url ); ?></p>
		</div>
		<script type="text/javascript">
			jQuery( function( $ ) {
				var $notice = $( '.notice[data-imagewalk=notice-no-register]' );
				$notice.on( 'click', '.notice-dismiss', function() {
					var data = { action: 'imagewalk_notice_dismiss', id: 'no_register' }
					$.post( ajaxurl, data );
				} );
			} );
		</script>
		<?php
	}
}
