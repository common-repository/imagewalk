import { ajax, fn, hooks, post } from 'imagewalk';
import { ajax_url, posts } from 'imagewalk-data';

const AJAX_URL = ajax_url;
const POSTS_INITIAL = +posts.initial;

class Stream {
	constructor( elem ) {
		this.id = +elem.getAttribute( 'data-id' );
		this.mode = elem.getAttribute( 'data-mode' );
		this.newest = null;
		this.oldest = null;

		this.$elem = elem;
		this.$loading = elem.querySelector( '[data-element=spinner]' );

		this.getInitial( POSTS_INITIAL ).then( data => {
			if ( data && data.length ) {
				this.autoload();
			} else {
				this.$loading.style.display = 'none';
			}
		} );

		hooks.addAction( 'upload_success', 'stream', () => this.onUploadSuccess() );
		hooks.addAction( 'post_deleted', 'stream', () => this.onPostDeleted() );
	}

	get( limit = 1, ref ) {
		return new Promise( ( resolve, reject ) => {
			let user_id = this.id || undefined,
				mode = this.mode || undefined,
				json;

			ajax.get( {
				url: AJAX_URL,
				data: { action: 'imagewalk_get_posts', limit, user_id, mode, ref },
				success: resp => {
					let prepend = ref && ref.match( /^>\d+$/ );

					json = JSON.parse( resp );
					this.render( json.data, prepend ? 'prepend' : null );
				},
				complete: () => {
					typeof json === 'undefined' ? reject() : resolve( json && json.data );
				}
			} );
		} );
	}

	getInitial( limit = 1 ) {
		return this.get( limit );
	}

	getOlder( limit = 2 ) {
		return this.get( limit, `<${ this.oldest }` );
	}

	getNewer( limit = 10 ) {
		return this.get( limit, `>${ this.newest }` );
	}

	render( posts, method ) {
		let prepend = method === 'prepend',
			$ref = this.$loading;

		if ( posts && posts.forEach ) {
			if ( prepend ) {
				$ref = this.$elem.firstChild;
			}

			posts.forEach( data => {
				let $post = post.toElement( data );

				this.$elem.insertBefore( $post, $ref );
				this.newest = Math.max( this.newest || +data.id, +data.id );
				this.oldest = Math.min( this.oldest || +data.id, +data.id );
				$ref = prepend ? $post : $ref;

				hooks.doAction( 'post_render', $post );
			} );
		}
	}

	autoload() {
		let newAutoload = fn.throttle( () => {
			// End throttle cycle if no more older posts is found.
			if ( this._autoloadEnd ) {
				window.removeEventListener( 'scroll', this.autoload );
				return;
			}

			// Queue for the next throttle cycle if the current one is still on progress.
			if ( this._autoloadProgress ) {
				this.autoload();
				return;
			}

			// Check if scroll position is reaching the bottom of the stream element.
			let position = this.$elem.getBoundingClientRect();
			if ( position.bottom < window.innerHeight ) {
				this._autoloadProgress = true;
				this.getOlder().then( json => {
					this._autoloadProgress = false;
					if ( ! ( json && json.length ) ) {
						this.$loading.style.display = 'none';
						this._autoloadEnd = true;
					}
				} );
			}
		}, 500 );

		this.autoload = newAutoload;
		this.autoload();
		window.addEventListener( 'scroll', this.autoload );
	}

	onUploadSuccess() {
		this.getNewer();
	}

	onPostDeleted() {
		this.autoload();
	}
}

hooks.addAction( 'init', 'init_stream', function() {
	[ ...document.querySelectorAll( '.imgw-stream' ) ].forEach( elem => {
		new Stream( elem );
	} );
} );
