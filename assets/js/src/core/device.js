import memoize from 'lodash.memoize';

/**
 * Detect if device has touch screen capability.
 *
 * @returns {boolean}
 */
export const isTouch = memoize( function() {
	return 'ontouchstart' in window;
} );
