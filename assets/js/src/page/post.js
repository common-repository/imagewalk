import { hooks } from 'imagewalk';

hooks.addAction( 'init', 'init_page_post', function() {
	[ ...document.querySelectorAll( '.imgw-page--post' ) ].forEach( elem => {
		let $posts = elem.querySelectorAll( '.imgw-post' );
		$posts.forEach( $post => {
			hooks.doAction( 'post_render', $post );
		} );
	} );
} );
