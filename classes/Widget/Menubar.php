<?php

class Imagewalk_Widget_Menubar extends WP_Widget {
	public function __construct() {
		$id = 'imagewalk_widget_menubar';
		$title = __( 'Imagewalk Menubar', 'imagewalk' );
		$description = __( 'Display an Imagewalk menubar.', 'imagewalk' );

		parent::__construct( $id, $title, array(
			'classname' => 'imgw-widget',
			'description' => $description,
		) );
	}

	public function form( $instance ) {
		$upload_button = 1;
		if ( isset( $instance[ 'upload_button' ] ) ) {
			$upload_button = (int) $instance[ 'upload_button' ];
		}

		?>
		<p>
			<input type="checkbox" class="checkbox"
				id="<?php echo esc_attr( $this->get_field_id( 'upload_button' ) ); ?>"
				name="<?php echo esc_attr( $this->get_field_name( 'upload_button' ) ); ?>"
				value="1" <?php echo $upload_button ? ' checked' : '' ?>>
			<label for="<?php echo esc_attr( $this->get_field_id( 'upload_button' ) ); ?>">
				<?php esc_attr_e( 'Show upload button', 'imagewalk' ); ?>
			</label>
		</p>
		<?php
	}

	public function update( $new_instance, $old_instance ) {
		$instance = array();

		$instance[ 'upload_button' ] = 0;
		if ( isset( $new_instance[ 'upload_button' ] ) ) {
			$instance[ 'upload_button' ] = 1;
		}

		return $instance;
	}

	public function widget( $args, $instance ) {
		$upload_button = 1;
		if ( isset( $instance[ 'upload_button' ] ) ) {
			$upload_button = (int) $instance[ 'upload_button' ];
		}

		echo $args[ 'before_widget' ];
		echo Imagewalk::get_template( 'widgets/menubar', array( 'upload_button' => $upload_button ) );
		echo $args[ 'after_widget' ];
	}
}
