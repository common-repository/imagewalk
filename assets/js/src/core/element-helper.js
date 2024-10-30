class ElementHelper {
	/**
	 * @param {Element|Array.<Element>|string} elements
	 */
	constructor( elements ) {
		if ( 'string' === typeof elements ) {
			this.elements = find( elements, document );
		} else {
			this.elements = [];

			if ( ! ( elements && elements.forEach ) ) {
				elements = [ elements ];
			}

			elements.forEach( element => {
				if ( element instanceof Element || element === document ) {
					this.elements.push( element );
				}
			} );
		}

		this.length = this.elements.length;
	}

	/**
	 * Get a single element item.
	 *
	 * @param {number} index
	 * @returns {Element}
	 */
	get( index ) {
		return this.elements[ index ];
	}

	/**
	 * Iterate trought the element items.
	 *
	 * @param {Function} fn
	 * @returns {ElementHelper}
	 */
	each( fn ) {
		this.elements.forEach( function( element, index ) {
			fn( element, index );
		} );

		return this;
	}

	/**
	 * Get or set element property.
	 *
	 * @param {string} key
	 * @param {*} [value]
	 * @returns {string|ElementHelper}
	 */
	prop( key, value ) {
		// Getter.
		if ( 'undefined' === typeof value ) {
			if ( this.elements.length ) {
				let element = this.elements[ 0 ];
				value = element[ key ];
			}

			return value;
		}

		// Setter.
		this.elements.forEach( element => {
			element[ key ] = value;
		} );

		return this;
	}

	/**
	 * Get or set element attribute.
	 *
	 * @param {string} key
	 * @param {*} [value]
	 * @returns {string|ElementHelper}
	 */
	attr( key, value ) {
		// Getter.
		if ( 'undefined' === typeof value ) {
			if ( this.elements.length ) {
				let element = this.elements[ 0 ];
				value = element.getAttribute( key );
			}

			return value;
		}

		// Setter.
		this.elements.forEach( element => {
			element.setAttribute( key, value );
		} );

		return this;
	}

	/**
	 * Get or set element data attribute.
	 *
	 * @param {string} key
	 * @param {*} [value]
	 * @returns {string|ElementHelper}
	 */
	data( key, value ) {
		return this.attr( `data-${ key }`, value );
	}

	/**
	 * Get or set element display style.
	 *
	 * @param {string} [value]
	 * @returns {string|ElementHelper}
	 */
	display( value ) {
		// Getter.
		if ( 'undefined' === typeof value ) {
			if ( this.elements.length ) {
				let element = this.elements[ 0 ],
					computedStyle = window.getComputedStyle( element );

				value = computedStyle.display;
			}

			return value;
		}

		// Setter.
		this.elements.forEach( element => {
			element.style.display = value;
		} );

		return this;
	}

	/**
	 * Get or set element value.
	 *
	 * @param {string} [value]
	 * @returns {string|ElementHelper}
	 */
	val( value ) {
		// Getter.
		if ( 'undefined' === typeof value ) {
			if ( this.elements.length ) {
				let element = this.elements[ 0 ];
				value = element.value;
			}

			return value;
		}

		// Setter.
		this.elements.forEach( element => {
			element.value = value;
		} );

		return this;
	}

	/**
	 * Remove element from document tree.
	 *
	 * @returns {ElementHelper}
	 */
	remove() {
		this.elements.forEach( element => {
			element.remove();
		} );

		this.elements = [];
		this.length = 0;

		return this;
	}

	/**
	 * Find element descendants by selector.
	 *
	 * @param {string} selector
	 * @returns {ElementHelper}
	 */
	find( selector ) {
		let elements = find( selector, this.elements );

		return new ElementHelper( elements );
	}

	/**
	 * Attach an event listener to the element.
	 *
	 * @param {string} eventName
	 * @param {Function} listener
	 * @returns {ElementHelper}
	 */
	on( eventName, listener ) {
		this.elements.forEach( element => {
			element.addEventListener( eventName, listener );
		} );

		return this;
	}

	/**
	 * Remove an event listener from the element.
	 *
	 * @param {string} eventName
	 * @param {Function} listener
	 * @returns {ElementHelper}
	 */
	off( eventName, listener ) {
		this.elements.forEach( element => {
			element.removeEventListener( eventName, listener );
		} );

		return this;
	}
}

/**
 * Find element descendants by selector.
 *
 * @param {string} selector
 * @param {Array.<Element} [context]
 * @returns {Array.<Element>}
 */
function find( selector, context ) {
	let result = [];

	if ( ! context ) {
		context = document;
	}

	if ( ! context.forEach ) {
		context = [ context ];
	}

	context.forEach( parent => {
		if ( parent instanceof Element || parent === document ) {
			[ ...parent.querySelectorAll( selector ) ].forEach( element => {
				if ( result.indexOf( element ) === -1 ) {
					result.push( element );
				}
			} );
		}
	} );

	return result;
}

export default function( elem ) {
	return new ElementHelper( elem );
}
