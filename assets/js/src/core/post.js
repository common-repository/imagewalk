import template from 'lodash.template';
import { post } from 'imagewalk-data';

const TEMPLATE = post.template;

/**
 * Generate post html from data.
 *
 * @param {Object} data
 * @returns {string}
 */
export function toHtml( data = {} ) {
	let compiled = template( TEMPLATE, { variable: 'data' } );
	return compiled( data );
}

/**
 * Generate post element from data.
 *
 * @param {Object} data
 * @returns {HTMLElement}
 */
export function toElement( data = {} ) {
	let $temp = document.createElement( 'div' );
	$temp.innerHTML = toHtml( data );

	return $temp.firstChild;
}
