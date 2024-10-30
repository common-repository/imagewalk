import { hooks } from 'imagewalk';

import './components/menubar';
import './components/stream';
import './components/comment';
import './components/comments';
import './components/commentbox';
import './components/post';
import './page';

// Handle arbitrary upload button.
hooks.addAction( 'init', 'init_trigger_upload', function() {
	[ ...document.querySelectorAll( '[data-imagewalk=upload]' ) ].forEach( elem => {
		elem.addEventListener( 'click', function() {
			hooks.doAction( 'upload_open' );
		} );
	} );
} );
