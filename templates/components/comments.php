<?php if ( isset( $_imagewalk_template_data ) ): ?>
<?php $data = $_imagewalk_template_data; ?>

<div class="imgw-comments">
	<?php if ( $data[ 'comments' ][ 'more' ] ): ?>
	<div class="imgw-comments__more">
		<a href="<?php echo esc_url( $data[ 'url' ] ) ?>" data-btn="more">
			<?php _e( 'show more comments', 'imagewalk' ); ?>
		</a>
	</div>
	<?php endif; ?>

	<?php if ( count( $data[ 'comments' ][ 'items' ] ) ): ?>
	<?php foreach ( $data[ 'comments' ][ 'items' ] as $comment ): ?>
	<?php echo Imagewalk::get_template( 'components/comment', $comment ); ?>
	<?php endforeach; ?>
	<?php endif; ?>
</div>

<?php else: ?>

<div class="imgw-comments">
	<% if ( data.comments && data.comments.more ) { %>
	<div class="imgw-comments__more">
		<a href="${ data.url }" data-btn="more">
			<?php _e( 'show more comments', 'imagewalk' ); ?>
		</a>
	</div>
	<% } %>

	<% if ( data.comments && data.comments.items ) { %>
	<% data.comments.items.forEach( function( data ) { %>
	<?php echo Imagewalk::get_template( 'components/comment' ); ?>
	<% } ); %>
	<% } %>
</div>

<?php endif;
