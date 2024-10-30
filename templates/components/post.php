<?php if ( isset( $_imagewalk_template_data ) ): ?>
<?php $data = $_imagewalk_template_data; ?>
<div class="imgw imgw-post"
		data-id="<?php echo esc_attr( $data[ 'id' ] ) ?>"
		data-user-id="<?php echo esc_attr( $data[ 'user' ][ 'id' ] ) ?>">
	<div class="imgw-post__header">
		<strong class="imgw-post__username">
			<a href="<?php echo esc_url( $data[ 'user' ][ 'url' ] ) ?>">
				<?php echo esc_html( $data[ 'user' ][ 'name' ] ) ?>
			</a>
		</strong>
		<?php if ( count( $data[ 'actions' ] ) ) : ?>
		<div class="imgw-post__actions" data-btn="actions">
			<button>
				<i class="imgw-icon">more_vert</i>
			</button>
			<div class="imgw-dropdown" data-dropdown="actions">
				<ul>
					<?php if ( in_array( 'delete', $data[ 'actions' ] ) ) : ?>
					<li>
						<a href="#" class="imgw-color--danger" data-btn="action-delete" data-id="<?php echo esc_attr( $data[ 'id' ] ) ?>">
							<?php _e( 'Delete post', 'imagewalk' ) ?>
						</a>
					</li>
					<?php endif; ?>
				</ul>
			</div>
		</div>
		<?php endif; ?>
	</div>
	<div class="imgw-post__image"><?php echo $data[ 'thumbnail' ] ?></div>
	<div class="imgw-post__info">
		<a href="<?php echo esc_url( $data[ 'url' ] ) ?>"><?php echo esc_html( $data[ 'date' ] ) ?></a>
	</div>
	<div class="imgw-post__content">
		<?php if ( ! empty( $data[ 'caption' ] ) ): ?>
		<div class="imgw-post__caption">
			<strong class="imgw-post__username">
				<a href="<?php echo esc_url( $data[ 'user' ][ 'url' ] ) ?>">
					<?php echo esc_html( $data[ 'user' ][ 'name' ] ) ?>
				</a>
			</strong>
			<span><?php echo esc_html( $data[ 'caption' ] ) ?></span>
		</div>
		<?php endif; ?>
		<?php echo Imagewalk::get_template( 'components/comments', $data ) ?>
	</div>
	<?php if ( is_user_logged_in() ) : ?>
		<?php if ( $data[ 'comment_allow' ] ) : ?>
		<?php echo Imagewalk::get_template( 'components/commentbox' ) ?>
		<?php endif; ?>
	<?php endif; ?>
</div>

<?php else: ?>

<div class="imgw imgw-post" data-id="${ data.id }" data-user-id="${ data.user.id }">
	<div class="imgw-post__header">
		<strong class="imgw-post__username">
			<a href="${ data.user.url }">${ data.user.name }</a>
		</strong>
		<% if ( data.actions.length ) { %>
		<div class="imgw-post__actions" data-btn="actions">
			<button>
				<i class="imgw-icon">more_vert</i>
			</button>
			<div class="imgw-dropdown" data-dropdown="actions">
				<ul>
					<% if ( data.actions.indexOf( 'delete' ) !== -1 ) { %>
					<li>
						<a href="#" class="imgw-color--danger" data-btn="action-delete" data-id="${ data.id }">
							<?php _e( 'Delete post', 'imagewalk' ) ?>
						</a>
					</li>
					<% }; %>
				</ul>
			</div>
		</div>
		<% }; %>
	</div>
	<div class="imgw-post__image">${ data.thumbnail }</div>
	<div class="imgw-post__info">
		<a href="${ data.url }">${ data.date }</a>
	</div>
	<div class="imgw-post__content">
		<% if ( data.caption ) { %>
		<div class="imgw-post__caption">
			<strong class="imgw-post__username">
				<a href="${ data.user.url }">${ data.user.name }</a>
			</strong>
			<span>${ data.caption }</span>
		</div>
		<% }; %>
		<?php echo Imagewalk::get_template( 'components/comments' ) ?>
	</div>
	<?php if ( is_user_logged_in() ) : ?>
		<% if ( data.comment_allow ) { %>
		<?php echo Imagewalk::get_template( 'components/commentbox' ) ?>
		<% }; %>
	<?php endif; ?>

</div>

<?php endif;
