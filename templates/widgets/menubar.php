<?php

	$data = array();
	if ( isset( $_imagewalk_template_data ) ) {
		$data = $_imagewalk_template_data;
	}

?>
<div class="imgw imgw-widget imgw-widget--menubar">
	<?php echo Imagewalk::get_template( 'shortcodes/menubar', $data ); ?>
</div>
