import { $, ajax, hooks } from 'imagewalk';
import { ajax_url, comment as commentData } from 'imagewalk-data';

const AJAX_URL = ajax_url;

class Comment {
	constructor( elem ) {
		let $elem = $( elem );

		this.id = +$elem.data( 'id' );

		this.$elem = $elem;
		this.$toggle = $elem.find( '[data-btn=actions]' );
		this.$dropdown = $elem.find( '[data-dropdown=actions]' );

		this.$toggle.on( 'click', () => this.toggle() );
		this.$dropdown.on( 'click', e => this.onActions( e ) );
	}

	delete() {
		return new Promise( ( resolve, reject ) => {
			let json;

			ajax.post( {
				url: AJAX_URL,
				data: { action: 'imagewalk_delete_comment', id: this.id },
				success: resp => ( json = JSON.parse( resp ) ),
				complete: () => {
					if ( json && json.success ) {
						this.$elem.remove();
						hooks.doAction( 'comment_deleted', this.id );
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
				if ( confirm( commentData.lang.delete ) ) {
					this.delete().catch( error => error && alert( error ) );
				}
			}
		}

		this.hide();
	}
}

hooks.addAction( 'comment_render', 'comment', function( $comment ) {
	new Comment( $comment );
} );
