<?php

if ( ! class_exists( 'Qixi_Functions' ) ) {
	class Qixi_Functions {
		public function __construct() {
			add_action( 'wp_ajax_qixi_get_download', array( $this, 'download_plugin' ) );
			add_action( 'wp_ajax_nopriv_qixi_get_download', array( $this, 'download_plugin' ) );
		}

		public function download_plugin(){
			$token    = sanitize_text_field( wp_unslash( $_POST['token'] ?? '' ) );
			$package  = sanitize_text_field( wp_unslash( $_POST['package'] ?? '' ) );
			$site_url = sanitize_text_field( wp_unslash( $_POST['site_url'] ?? '' ) );

			if ( 'revslider' === $package ) {
				$url = 'https://www.svlatudios.com/extras/plugins/revslider.zip';
			} elseif ( 'js_composer' === $package ) {
				$url = 'https://www.svlatudios.com/extras/plugins/visual-composer.zip';
			}

			echo esc_url( $url );
		}
	}

	new Qixi_Functions();
}
