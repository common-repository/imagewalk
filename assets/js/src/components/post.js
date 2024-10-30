import { $, ajax, hooks } from 'imagewalk';
import { ajax_url, post as postData } from 'imagewalk-data';

const AJAX_URL = ajax_url;

class Post {
	constructor( elem ) {
		let $elem = $( elem ),
			$header = $elem.find( '.imgw-post__header' );

		this.id = +$elem.data( 'id' );

		this.$elem = $elem;
		this.$toggle = $header.find( '[data-btn=actions]' );
		this.$dropdown = $header.find( '[data-dropdown=actions]' );

		this.$toggle.on( 'click', () => this.toggle() );
		this.$dropdown.on( 'click', e => this.onActions( e ) );
	}

	delete() {
		return new Promise( ( resolve, reject ) => {
			let json;

			ajax.post( {
				url: AJAX_URL,
				data: { action: 'imagewalk_delete_post', id: this.id },
				success: resp => ( json = JSON.parse( resp ) ),
				complete: () => {
					if ( json && json.success ) {
						this.$elem.remove();
						hooks.doAction( 'post_deleted', this.id );
						resolve( json );
					} else {
						reject( ( json && json.error ) || '' );
					}
				}
			} );
		} );
	}

	show() {
		this.$dropdown.display( 'block' );

		// Add autohide trigger.
		setTimeout( () => {
			this.autohide = () => this.hide();
			$( document ).on( 'click', this.autohide );
		}, 100 );
	}

	hide() {
		this.$dropdown.display( '' );

		// Remove autohide trigger.
		if ( this.autohide ) {
			$( document ).off( 'click', this.autohide );
			delete this.autohide;
		}
	}

	toggle() {
		if ( 'none' === this.$dropdown.display() ) {
			this.show();
		} else {
			this.hide();
		}
	}

	onActions( e ) {
		e.preventDefault();

		if ( 'A' === e.target.tagName ) {
			let action = $( e.target ).data( 'btn' );

			if ( 'action-delete' === action ) {
				if ( confirm( postData.lang.delete ) ) {
					this.delete().catch( error => error && alert( error ) );
				}
			}
		}

		this.hide();
	}
}

hooks.addAction( 'post_render', 'post', function( $post ) {
	new Post( $post );
} );
