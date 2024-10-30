<?php

	// Settings page URL.
	$url = 'admin.php?page=' . Imagewalk::$post_type . '-settings';

	$tabs = array();
	$tab_current = '';

	if ( isset( $_imagewalk_template_data ) ) {
		$data = $_imagewalk_template_data;
		$tabs = $data[ 'tabs' ];
		$tab_current = $data[ 'tab' ];
	}

?>
<div class="wrap">
	<h1 class="wp-heading-inline"><?php _e( 'Imagewalk Settings', 'imagewalk' ) ?></h1>
	<hr class="wp-header-end" />
	<nav class="nav-tab-wrapper wp-clearfix">
		<?php foreach ( $tabs as $index => $tab ) : ?>
		<a href="<?php echo $url ?><?php if ( $index ) echo '&tab=' . $tab[ 'id' ]; ?>"
			class="nav-tab<?php if ( $tab[ 'id' ] === $tab_current ) echo ' nav-tab-active'; ?>">
			<?php echo $tab[ 'label' ]; ?>
		</a>
		<?php endforeach; ?>
	</nav>
