<?php if ( isset( $_imagewalk_template_data ) ): ?>
<?php $data = $_imagewalk_template_data; ?>

<div class="imgw-comment" data-id="<?php echo esc_attr( $data[ 'id' ] ) ?>">
	<div class="imgw-comment__content">
		<strong class="imgw-comment__username">
			<a href="<?php echo esc_url( $data[ 'user' ][ 'url' ] ) ?>"><?php echo esc_html( $data[ 'user' ][ 'name' ] ) ?></a>
		</strong>
		<span><?php echo esc_html( $data[ 'content' ] ) ?></span>
	</div>

	<?php if ( count( $data[ 'actions' ] ) ) : ?>
	<div class="imgw-comment__actions" data-btn="actions">
		<button>
			<i class="imgw-icon">more_vert</i>
		</button>
		<div class="imgw-dropdown" data-dropdown="actions">
			<ul>
				<?php if ( in_array( 'delete', $data[ 'actions' ] ) ) : ?>
				<li>
					<a href="#" class="imgw-color--danger" data-btn="action-delete" data-id="<?php echo esc_attr( $data[ 'id' ] ) ?>">
						<?php _e( 'Delete comment', 'imagewalk' ) ?>
					</a>
				</li>
				<?php endif; ?>
			</ul>
		</div>
	</div>
	<?php endif; ?>
</div>

<?php else: ?>

<div class="imgw-comment" data-id="${ data.id }">
	<div class="imgw-comment__content">
		<strong class="imgw-comment__username">
			<a href="${ data.user.url }">${ data.user.name }</a>
		</strong>
		<span>${ data.content }</span>
	</div>

	<% if ( data.actions.length ) { %>
	<div class="imgw-comment__actions" data-btn="actions">
		<button>
			<i class="imgw-icon">more_vert</i>
		</button>
		<div class="imgw-dropdown" data-dropdown="actions">
			<ul>
				<% if ( data.actions.indexOf( 'delete' ) !== -1 ) { %>
				<li>
					<a href="#" class="imgw-color--danger" data-btn="action-delete" data-id="${ data.id }">
						<?php _e( 'Delete comment', 'imagewalk' ) ?>
					</a>
				</li>
				<% }; %>
			</ul>
		</div>
	</div>
	<% }; %>
</div>

<?php endif;
