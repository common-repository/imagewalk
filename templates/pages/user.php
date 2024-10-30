<?php

	$user_id = NULL;
	$user_name = get_query_var( 'imgw_id' );

	if ( $user_name ) {
		$user = get_user_by( 'login', $user_name );
		if ( $user instanceof WP_User ) {
			$user_id = (int) $user->get( 'ID' );
		}
	} else if ( is_user_logged_in() ) {
		$user_id = get_current_user_id();
	}

	if ( $user_id ) {
		$user = Imagewalk_Model_User::get( $user_id );

		// Post counter text.
		$post_counts = (int) count_user_posts( $user_id, Imagewalk::$post_type );
		$post_counts = sprintf(
			_n( '<strong>%d</strong> post', '<strong>%d</strong> posts', $post_counts, Imagewalk::$post_type ),
			$post_counts
		);
	}
?>
<div class="imgw imgw-page imgw-page--user">
	<?php if ( $user_id ) : ?>
	<div class="imgw-user" data-id="<?php echo esc_attr( $user_id ) ?>">
		<div class="imgw-user__avatar">
			<img src="<?php echo esc_url( $user[ 'avatar' ] ) ?>" alt="<?php echo esc_html( $user[ 'name' ] ) ?>" />
		</div>
		<div class="imgw-user__info">
			<div class="imgw-user__username"><?php echo esc_html( $user[ 'name' ] ) ?></div>
			<div class="imgw-user__counts"><?php echo $post_counts ?></div>
		</div>
	</div>
	<div class="imgw-stream" data-id="<?php echo esc_attr( $user_id ) ?>" data-mode="grid">
		<div class="imgw-spinner" data-element="spinner"></div>
	</div>
	<?php else: ?>
	<div class="imgw-notice imgw-notice--error">
		<?php _e( 'User not found.', 'imagewalk' ) ?>
	</div>
	<?php endif; ?>
</div>
