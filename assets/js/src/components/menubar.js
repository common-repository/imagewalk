import { hooks } from 'imagewalk';

class Menubar {
	constructor( elem ) {
		this.$elem = elem;
		this.$upload = elem.querySelector( '[data-btn=upload]' );

		// Guest user does not have upload button.
		if ( this.$upload ) {
			this.$upload.addEventListener( 'click', e => this.onUpload( e ) );
		}
	}

	onUpload( e ) {
		e.preventDefault();
		e.stopPropagation();

		hooks.doAction( 'upload_open' );
	}
}

hooks.addAction( 'init', 'init_menubar', function() {
	[ ...document.querySelectorAll( '.imgw-menubar' ) ].forEach( elem => {
		new Menubar( elem );
	} );
} );
