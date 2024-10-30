import { $, ajax, comment, hooks } from 'imagewalk';
import { ajax_url, comments } from 'imagewalk-data';

const AJAX_URL = ajax_url;
const COMMENTS_MORE = +comments.more;

class Comments {
	constructor( id, elem ) {
		this.id = id;
		this.$elem = $( elem );
		this.$more = this.$elem.find( '.imgw-comments__more' );
		this.$moreTrigger = this.$more.find( '[data-btn=more]' );

		this.$moreTrigger.on( 'click', e => this.onCommentsMore( e ) );

		hooks.addAction( 'comment_added', 'comments', ( id, data ) =>
			this.onCommentAdded( id, data )
		);

		// Initialize initial comments.
		this.$elem.find( '.imgw-comment' ).each( function( comment ) {
			hooks.doAction( 'comment_render', comment );
		} );
	}

	render( comments, method ) {
		let prepend = method === 'prepend',
			$ref = null;

		if ( comments && comments.forEach ) {
			if ( prepend ) {
				$ref = this.$more.get( 0 ).nextElementSibling;
			}

			comments.forEach( data => {
				let $comment = comment.toElement( data );
				this.$elem.get( 0 ).insertBefore( $comment, prepend ? $ref : null );
				hooks.doAction( 'comment_render', $comment );
			} );
		}
	}

	onCommentsMore( e ) {
		e.preventDefault();
		e.stopPropagation();

		if ( this._fetching ) {
			return;
		}

		this.$first = this.$elem.find( '.imgw-comment' );
		if ( ! this.$first.length ) {
			return;
		}

		let ref = this.$first.data( 'id' );

		this._fetching = true;
		ajax.get( {
			url: AJAX_URL,
			data: {
				action: 'imagewalk_get_comments',
				post_id: this.id,
				limit: COMMENTS_MORE,
				ref: `<${ ref }`
			},
			success: resp => {
				let json = JSON.parse( resp );

				if ( json && json.data ) {
					let { items, more } = json.data;

					if ( items && items.length ) {
						this.render( json.data.items, 'prepend' );
					}

					if ( ! more ) {
						this.$more.display( 'none' );
					}
				}
			},
			complete: () => {
				this._fetching = false;
			}
		} );
	}

	onCommentAdded( id, data ) {
		if ( id === this.id ) {
			let $comment = comment.toElement( data );
			this.$elem.get( 0 ).appendChild( $comment );
			hooks.doAction( 'comment_render', $comment );
		}
	}
}

hooks.addAction( 'post_render', 'comments', function( elem ) {
	let $comments = elem.querySelector( '.imgw-comments' ),
		postId;

	if ( $comments ) {
		postId = elem.getAttribute( 'data-id' );
		new Comments( postId, $comments );
	}
} );
