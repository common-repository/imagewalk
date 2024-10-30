import { ajax, hooks } from 'imagewalk';
import { ajax_url, img_width, img_height, uploader } from 'imagewalk-data';
import Cropper from 'cropperjs';
import Pica from 'pica';

const pica = Pica();

const AJAX_URL = ajax_url;
const IMG_WIDTH = +img_width;
const IMG_HEIGHT = +img_height;
const TEMPLATE = uploader.template;

class Uploader {
	constructor() {
		// Triggered by other modules.
		hooks.addAction( 'upload_open', 'uploader', () => this.onOpen() );
	}

	init() {
		if ( this.$elem ) {
			return;
		}

		let $tmp = document.createElement( 'div' );
		$tmp.innerHTML = TEMPLATE.trim();

		this.$elem = $tmp.firstChild;
		this.$file = this.$elem.querySelector( '[data-id=file]' );
		this.$image = this.$elem.querySelector( '[data-id=image]' );
		this.$canvas = this.$elem.querySelector( '[data-id=canvas]' );
		this.$caption = this.$elem.querySelector( '[data-id=caption]' );
		this.$step1 = this.$elem.querySelector( '[data-step="1"]' );
		this.$step2 = this.$elem.querySelector( '[data-step="2"]' );

		this.$btnFlip = this.$elem.querySelector( '[data-btn=flip]' );
		this.$btnRotate = this.$elem.querySelector( '[data-btn=rotate]' );
		this.$btnCrop = this.$elem.querySelector( '[data-btn=crop]' );
		this.$btnUndo = this.$elem.querySelector( '[data-btn=undo]' );
		this.$btnSubmit = this.$elem.querySelector( '[data-btn=submit]' );
		this.$btnClose = this.$elem.querySelector( '[data-btn=close]' );

		this.$file.addEventListener( 'change', () => this.onSelectFile() );
		this.$btnFlip.addEventListener( 'click', () => this.flip() );
		this.$btnRotate.addEventListener( 'click', () => this.rotate() );
		this.$btnCrop.addEventListener( 'click', () => this.onConfirm() );
		this.$btnUndo.addEventListener( 'click', () => this.onUndo() );
		this.$btnSubmit.addEventListener( 'click', () => this.onSubmit() );
		this.$btnClose.addEventListener( 'click', e => this.onClose( e ) );

		document.body.appendChild( this.$elem );
	}

	show() {
		this.init();
		this.$step1.style.display = '';
		this.$step2.style.display = 'none';
		this.$elem.style.display = 'block';

		// Disable scroll on the document.body while the uploader modal is shown.
		this._documentOverflow = document.body.style.overflow;
		document.body.style.overflow = 'hidden';
	}

	hide() {
		if ( this.$elem ) {
			this.$elem.style.display = '';
			this.reset();

			// Restore scroll on the document.body.
			document.body.style.overflow = this._documentOverflow;
			delete this._documentOverflow;
		}
	}

	reset() {
		this.imgType = null;
		this.imgCanvas = null;

		if ( this.cropper ) {
			this.cropper.destroy();
			this.cropper = null;
			this.cropperFlip = null;
			this.$image.innerHTML = '';
			this.$canvas.innerHTML = '';
			this.$caption.value = '';
		}
	}

	flip() {
		if ( this.cropper ) {
			this.cropperFlip = 0 - ( this.cropperFlip || 1 );
			this.cropper.scaleX( this.cropperFlip );
		}
	}

	rotate() {
		if ( this.cropper ) {
			this.cropper.rotate( 90 );
		}
	}

	crop() {
		if ( ! this.cropper ) {
			return;
		}
	}

	maybeResize( image, imageType ) {
		return new Promise( resolve => {
			let width = image.naturalWidth,
				height = image.naturalHeight,
				maxDimension = 1024,
				canvas,
				aspectRatio;

			if ( ! Math.max( width, height ) > maxDimension ) {
				resolve( image );
				return;
			}

			aspectRatio = maxDimension / Math.max( width, height );
			canvas = document.createElement( 'canvas' );
			canvas.width = aspectRatio * width;
			canvas.height = aspectRatio * height;

			pica.resize( image, canvas )
				.then( result => pica.toBlob( result, imageType, 1 ) )
				.then( blob => {
					let image = new Image();
					image.onload = () => {
						resolve( image );
					};
					image.src = URL.createObjectURL( blob );
				} );
		} );
	}

	onOpen() {
		this.init();
		this.$file.click();
	}

	onClose( e ) {
		e.stopPropagation();
		this.hide();
	}

	onSelectFile() {
		let file = this.$file.files[ 0 ],
			fileReader;

		if ( ! file ) {
			return;
		}

		this.show();

		this.imgType = file.type;
		if ( -1 === [ 'image/png', 'image/jpeg' ].indexOf( this.imgType ) ) {
			this.imgType = 'image/png';
		}

		fileReader = new FileReader();
		fileReader.addEventListener( 'load', () => {
			let image = new Image();
			image.onload = () => {
				this.maybeResize( image, this.imgType ).then( image => {
					this.$file.form.reset();
					this.$image.innerHTML = '';
					this.$image.appendChild( image );
					this.$image.style.display = 'block';

					this.cropper = new Cropper( image, {
						aspectRatio: 1,
						background: false,
						center: false,
						cropBoxMovable: false,
						cropBoxResizable: false,
						dragMode: 'move',
						minCropBoxWidth: IMG_WIDTH,
						toggleDragModeOnDblclick: false,
						viewMode: 3
					} );
				} );
			};
			image.src = fileReader.result;
		} );
		fileReader.readAsDataURL( file );
	}

	onConfirm() {
		if ( ! this.cropper ) {
			return;
		}

		this.imgCanvas = this.cropper.getCroppedCanvas( {
			width: IMG_WIDTH,
			height: IMG_HEIGHT
		} );

		let canvas = document.createElement( 'canvas' );
		canvas.width = IMG_WIDTH;
		canvas.height = IMG_HEIGHT;
		canvas.getContext( '2d' ).drawImage( this.imgCanvas, 0, 0 );

		this.$canvas.innerHTML = '';
		this.$canvas.appendChild( canvas );
		this.$canvas.style.display = 'block';

		this.$step1.style.display = 'none';
		this.$step2.style.display = '';
	}

	onUndo() {
		this.imgCanvas = null;

		this.$step2.style.display = 'none';
		this.$step1.style.display = '';
	}

	onSubmit() {
		if ( ! this.imgCanvas ) {
			return;
		}

		let data = {
			action: 'imagewalk_upload',
			caption: this.$caption.value.trim(),
			img: this.imgCanvas.toDataURL( this.imgType, 1 )
		};

		ajax.post( {
			url: AJAX_URL,
			data,
			success: resp => this.onSubmitSuccess( resp ),
			error: () => this.onSubmitError()
		} );
	}

	onSubmitSuccess( resp ) {
		this.hide();
		hooks.doAction( 'upload_success', resp );
	}

	onSubmitError() {
		console.log( 'onSubmitError' );
	}
}

hooks.addAction( 'init', 'init_uploader', function() {
	new Uploader();
} );
