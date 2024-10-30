<div class="imgw imgw-uploader">
	<button class="imgw-btn imgw-uploader__close" data-btn="close">
		<span class="imgw-icon">close</span>
	</button>

	<div class="imgw-uploader__step" data-step="1">
		<div class="imgw-uploader__nav">
			<div class="imgw-uploader__navleft"></div>
			<div class="imgw-uploader__navright">
				<button class="imgw-btn" data-btn="crop">
					<span class="imgw-btn__icon imgw-btn__icon--right imgw-icon">arrow_forward</span>
					<span class="imgw-btn__label"><?php _e( 'Next', 'imagewalk' ); ?></span>
				</button>
			</div>
		</div>
		<div class="imgw-uploader__square">
			<div class="imgw-uploader__image" data-id="image"></div>
		</div>
		<div class="imgw-uploader__actions">
			<button class="imgw-btn" data-btn="flip">
				<span class="imgw-btn__icon imgw-icon">flip</span>
				<span class="imgw-btn__label"><?php _e( 'Flip', 'imagewalk' ); ?></span>
			</button>
			<button class="imgw-btn" data-btn="rotate">
				<span class="imgw-btn__icon imgw-icon">rotate_right</span>
				<span class="imgw-btn__label"><?php _e( 'Rotate', 'imagewalk' ); ?></span>
			</button>
		</div>
	</div>

	<div class="imgw-uploader__step" data-step="2">
		<div class="imgw-uploader__nav">
			<div class="imgw-uploader__navleft">
				<button class="imgw-btn" data-btn="undo">
					<span class="imgw-btn__icon imgw-icon">arrow_back</span>
					<span class="imgw-btn__label"><?php _e( 'Back', 'imagewalk' ); ?></span>
				</button>
			</div>
			<div class="imgw-uploader__navright">
				<button class="imgw-btn imgw-btn--blue" data-btn="submit">
					<span class="imgw-btn__icon imgw-btn__icon--right imgw-icon">send</span>
					<span class="imgw-btn__label"><?php _e( 'Post', 'imagewalk' ); ?></span>
				</button>
			</div>
		</div>
		<div class="imgw-uploader__square">
			<div class="imgw-uploader__image" data-id="canvas"></div>
		</div>
		<div class="imgw-uploader__form">
			<label><?php _e( 'Caption', 'imagewalk' ); ?></label>
			<input type="text" class="imgw-uploader__text" data-id="caption"
				placeholder="<?php esc_attr_e( 'Enter image caption here...', 'imagewalk' ) ?>" />
		</div>
	</div>

	<div class="imgw-uploader__file-mask">
		<form>
			<input type="file" data-id="file" accept=".jpg,.jpeg,.png" />
		</form>
	</div>
</div>
