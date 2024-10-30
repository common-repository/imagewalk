import { encode } from 'qss';

/**
 * Generic XMLHttpRequest function.
 *
 * @param {Object} opts
 * @returns {XMLHttpRequest}
 */
export function request( opts = {} ) {
	let noop = function() {},
		{ url = '', method = 'GET', data, success = noop, error = noop, complete = noop } = opts,
		xhr = new XMLHttpRequest(),
		formData = null;

	// Prepare request parameters if data is provided.
	if ( typeof data === 'object' ) {
		// For the POST method, attach data via FormData.
		if ( method.match( /^post$/i ) ) {
			for ( let prop in data ) {
				if ( data.hasOwnProperty( prop ) ) {
					formData = formData || new FormData();
					formData.append( prop, data[ prop ] );
				}
			}
		}
		// Otherwise, append data to the URL.
		else {
			let sep = url.match( /\?/ ) ? '&' : '?';
			url = url.replace( /(#.*)?$/, `${ sep }${ encode( data ) }$1` );
		}
	}

	xhr.open( method, url, true );

	xhr.onload = function() {
		if ( xhr.readyState === xhr.DONE ) {
			if ( xhr.status === 200 ) {
				success( xhr.responseText );
			} else {
				error();
			}
			complete();
		}
	};

	xhr.onerror = function() {
		error();
		complete();
	};

	return xhr.send( formData );
}

/**
 * Perform XMLHttpRequest GET request.
 *
 * @param {Object} opts
 * @returns {XMLHttpRequest}
 */
export function get( opts = {} ) {
	return request( Object.assign( opts, { method: 'GET' } ) );
}

/**
 * Perform XMLHttpRequest POST request.
 *
 * @param {Object} opts
 * @returns {XMLHttpRequest}
 */
export function post( opts = {} ) {
	return request( Object.assign( opts, { method: 'POST' } ) );
}
