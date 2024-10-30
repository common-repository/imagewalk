import { $, ajax, device, hooks } from 'imagewalk';
import { ajax_url } from 'imagewalk-data';

const AJAX_URL = ajax_url;

class Commentbox {
	constructor( id, elem ) {
		this.id = id;

		this.$elem = $( elem );
		this.$input = this.$elem.find( 'input' );
		this.$button = this.$elem.find( 'button' );

		this.$button.on( 'click', () => this.onSubmit() );

		// Add enter-to-send on non-touch device.
		if ( ! device.isTouch() ) {
			this.$input.on( 'keydown', e => this.onKeydown( e ) );
		}
	}

	post( content ) {
		return new Promise( ( resolve, reject ) => {
			let json;

			ajax.post( {
				url: AJAX_URL,
				data: { action: 'imagewalk_add_comment', id: this.id, content },
				success: resp => {
					json = JSON.parse( resp );
					if ( json && json.success ) {
					}
				},
				complete: () => {
					if ( json && json.success ) {
						hooks.doAction( 'comment_added', this.id, json.data );
						resolve( json );
					} else {
						reject( ( json && json.error ) || '' );
					}
				}
			} );
		} );
	}

	onSubmit() {
		let content = this.$input.val().trim();

		if ( content ) {
			this.$input.prop( 'disabled', true );
			this.$button.prop( 'disabled', true );
			this.post( content )
				.then( () => this.$input.val( '' ) )
				.catch( error => alert( error ) )
				.then( () => {
					this.$input.prop( 'disabled', false );
					this.$button.prop( 'disabled', false );
				} );
		}
	}

	onKeydown( e ) {
		if ( 13 === e.keyCode ) {
			e.preventDefault();
			this.onSubmit();
		}
	}
}

hooks.addAction( 'post_render', 'commentbox', function( elem ) {
	let $commentbox = elem.querySelector( '.imgw-commentbox' ),
		postId;

	if ( $commentbox ) {
		postId = elem.getAttribute( 'data-id' );
		new Commentbox( postId, $commentbox );
	}
} );
