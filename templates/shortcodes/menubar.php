<?php

	$has_upload_button = 0;
	if ( is_user_logged_in() ) {
		if ( isset( $_imagewalk_template_data ) ) {
			$data = $_imagewalk_template_data;
			$has_upload_button = (int) $data[ 'upload_button' ];
		}
	}

?>
<div class="imgw imgw-menubar">
	<div class="imgw-menubar__left">
		<a href="<?php echo esc_url( Imagewalk::get_home_url() ) ?>"
			title="<?php esc_attr_e( 'Home', 'imagewalk' ) ?>">
			<i class="imgw-icon">home</i>
		</a>

		<?php if ( $has_upload_button ) : ?>
		<a href="#" data-btn="upload"
			title="<?php esc_attr_e( 'Upload', 'imagewalk' ) ?>">
			<i class="imgw-uploader--icon"></i>
		</a>
		<?php endif; ?>
	</div>

	<div class="imgw-menubar__right">
		<?php

			if ( ! is_user_logged_in() ) {
				// Register link.
				$link = '<a href="' . esc_url( wp_registration_url() ) . '">' . __( 'Register' ) . '</a>';
				$link = apply_filters( 'register', $link );
				echo $link;

				// Separator.
				echo ' &bull; ';

				// Login link.
				wp_loginout( get_permalink() );

			} else {
				// User link.
				$user = Imagewalk_Model_User::get( get_current_user_id() );
				echo '<a href="' . esc_url( $user[ 'url' ] ) . '" title="' .
					esc_attr( $user[ 'name' ] ) . '">' . $user[ 'name' ] . '</a>';

				// Separator.
				echo ' &bull; ';

				// Logout link.
				wp_loginout( Imagewalk::get_home_url() );
			}

		?>
	</div>
</div>
