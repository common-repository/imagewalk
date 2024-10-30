<?php

	$configs = array();
	if ( isset( $_imagewalk_template_data ) ) {
		$configs = $_imagewalk_template_data[ 'configs' ];
	}

	function _imagewalk_print_config_item( $config ) {
		?>
			<tr>
				<th scope="row">
					<label for="<?php echo esc_attr( $config[ 'id' ] ) ?>">
						<?php echo esc_html( $config[ 'title' ] ) ?>
					</label>
				</th>
				<td>
					<?php if ( 'checkbox' === $config[ 'type' ] ) : ?>
					<fieldset>
						<legend class="screen-reader-text">
							<span><?php echo esc_html( $config[ 'title' ] ) ?></span>
						</legend>
						<label for="<?php echo esc_attr( $config[ 'id' ] ) ?>">
							<input type="checkbox"
								name="<?php echo esc_attr( $config[ 'id' ] ) ?>"
								id="<?php echo esc_attr( $config[ 'id' ] ) ?>"
								value="1" <?php checked( '1', $config[ 'value' ] ); ?>>
								<?php echo esc_html( $config[ 'label' ] ) ?>
						</label>
					</fieldset>

					<?php elseif ( 'select' === $config[ 'type' ] ) : ?>
					<select name="<?php echo esc_attr( $config[ 'id' ] ) ?>" id="<?php echo esc_attr( $config[ 'id' ] ) ?>">
						<?php foreach ( $config[ 'options' ] as $option ) : ?>
						<?php $value = isset( $option[ 'value' ] ) ? $option[ 'value' ] : $option; ?>
						<option value="<?php echo esc_attr( $value ); ?>"  <?php selected( $value, $config[ 'value' ] ); ?>>
							<?php echo esc_html( isset( $option[ 'label' ] ) ? $option[ 'label' ] : $option ); ?>
						</option>
						<?php endforeach; ?>
					</select>

					<?php else : ?>
					<input type="text"
						name="<?php echo esc_attr( $config[ 'id' ] ) ?>"
						id="<?php echo esc_attr( $config[ 'id' ] ) ?>"
						value="<?php form_option( $config[ 'id' ] ); ?>" class="regular-text" />
					<?php endif; ?>

					<?php if ( isset( $config[ 'description' ] ) ) : ?>
					<p class="description" id="<?php str_replace( '_', '-', esc_attr( $config[ 'id' ] ) ) ?>-description">
						<?php echo $config[ 'description' ] ?>
					</p>
					<?php endif; ?>
				</td>
			</tr>
		<?php
	}

?>
<form method="post">
	<?php

		$inside_table = FALSE;

		foreach ( $configs as $configs_group ) {
			if ( ! $inside_table ) {
				echo '<table class="form-table" role="presentation">';
				$inside_table = TRUE;
			}

			if ( ! isset( $configs_group[ 'configs' ] ) ) {
				$config = $configs_group;
				echo _imagewalk_print_config_item( $config );
			} else {
				if ( $inside_table ) {
					echo '</table>';
					$inside_table = FALSE;
				}

				echo '<h2 class="title">' . esc_html( $configs_group[ 'title' ] ) . '</h2>';
				echo '<table class="form-table" role="presentation">';
				foreach ( $configs_group[ 'configs' ] as $config ) {
					echo _imagewalk_print_config_item( $config );
				}
				echo '</table>';
			}
		}

		if ( $inside_table ) {
			echo '</table>';
			$inside_table = FALSE;
		}

	?>
	<p class="submit">
		<input type="submit" name="submit" id="submit" class="button button-primary"
			value="<?php _e( 'Save Changes', 'imagewalk' ); ?>">
	</p>
</form>
