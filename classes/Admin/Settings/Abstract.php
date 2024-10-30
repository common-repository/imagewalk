<?php

abstract class Imagewalk_Admin_Settings_Abstract {
	public function __construct() {
		if ( isset( $_POST[ 'submit' ] ) ) {
			$this->save();
		}

		$this->render();
	}

	abstract protected function get_configs();

	protected function render() {
		echo Imagewalk::get_template(
			'admin/settings/content',
			array( 'configs' => $this->get_configs() )
		);
	}

	protected function save() {
		foreach ( $this->get_configs() as $configs_group ) {
			if ( ! isset( $configs_group[ 'configs' ] ) ) {
				$config = $configs_group;
				update_option( $config[ 'id' ], $this->get_form_value( $config ) );
			} else {
				foreach ( $configs_group[ 'configs' ] as $config ) {
					update_option( $config[ 'id' ], $this->get_form_value( $config ) );
				}
			}
		}
	}

	protected function get_form_value( $config ) {
		$value = '';
		if ( isset( $_POST[ $config[ 'id' ] ] ) ) {
			$value = sanitize_text_field( $_POST[ $config[ 'id' ] ] );
		}

		return $value;
	}
}
