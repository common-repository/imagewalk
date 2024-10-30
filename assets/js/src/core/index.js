import debounce from 'lodash.debounce';
import memoize from 'lodash.memoize';
import throttle from 'lodash.throttle';
import template from 'lodash.template';
import { createHooks } from '@wordpress/hooks';
import $ from './element-helper';
import * as device from './device';
import * as ajax from './ajax';
import * as comment from './comment';
import * as post from './post';

const getMissingFeatures = memoize( function() {
	let missing = [];

	Object.assign || missing.push( 'Object.assign' );
	window.JSON || missing.push( 'JSON' );
	window.localStorage || missing.push( 'localStorage' );
	window.Promise || missing.push( 'Promise' );
	window.XMLHttpRequest || missing.push( 'XMLHttpRequest' );

	return missing.length ? missing : null;
} );

// Initialize
( function( global ) {
	let missing = getMissingFeatures(),
		hooks;

	if ( missing ) {
		missing = missing.join( ', ' );
		console.error( `Missing browser features: [${ missing }]. Please upgrade your browser.` );
		global.imagewalk = {};
		return;
	}

	hooks = createHooks();
	global.imagewalk = {
		fn: { debounce, memoize, throttle },
		$,
		device,
		ajax,
		hooks,
		comment,
		post,
		template
	};

	// Add no-touch class on non-touch device.
	hooks.addAction( 'init', 'core', () => {
		if ( ! device.isTouch() ) {
			document.body.className += ' imgw-no-touch';
		}
	} );

	// Init imagewalk.
	setTimeout( () => hooks.doAction( 'init' ), 1000 );
} )( window );
