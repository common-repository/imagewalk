<div class="imgw imgw-page imgw-page--post">
	<?php

		$id = (int) get_query_var( 'imgw_id' );

		if ( $id ) {
			$post_endpoint = Imagewalk_Endpoint_Post::get_instance();
			echo $post_endpoint->get_html( $id );
		} else {
			echo '<div class="imgw-notice imgw-notice--error">' .
				__( 'Post not found.', 'imagewalk' ) . '</div>';
		}

	?>
</div>
