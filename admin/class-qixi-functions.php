<?php

if ( ! class_exists( 'Qixi_Functions' ) ) {
	class Qixi_Functions {
		public function __construct() {
			add_action( 'wp_ajax_qixi_get_download', array( $this, 'download_plugin' ) );
			add_action( 'wp_ajax_nopriv_qixi_get_download', array( $this, 'download_plugin' ) );

			add_action( 'wp_ajax_qixi_deactivate_license', array( $this, 'deactivate' ) );
			add_action( 'wp_ajax_nopriv_qixi_deactivate_license', array( $this, 'deactivate' ) );

			add_action( 'wp_ajax_qixi_activate_license', array( $this, 'activate' ) );
			add_action( 'wp_ajax_nopriv_qixi_activate_license', array( $this, 'activate' ) );

			add_action( 'wp_ajax_qixi_validate_token', array( $this, 'validate' ) );
			add_action( 'wp_ajax_nopriv_qixi_validate_token', array( $this, 'validate' ) );

			add_action( 'wp_ajax_qixi_create_nonce', array( $this, 'nonce' ) );
			add_action( 'wp_ajax_nopriv_qixi_create_nonce', array( $this, 'nonce' ) );
		}

		public function nonce() {
			if ( isset( $_GET['nonce'] ) ) {
				$key = sanitize_text_field( wp_unslash( $_GET['nonce'] ?? '' ) );

				echo sanitize_key( wp_unslash( wp_create_nonce( $key ) ) );
				die();
			}
		}

		public function activate() {
			$key      = sanitize_text_field( wp_unslash( $_GET['key'] ?? '' ) );
			$product  = sanitize_text_field( wp_unslash( $_GET['product'] ?? '' ) );
			$site_url = sanitize_text_field( wp_unslash( $_GET['site_url'] ?? '' ) );

		}

		public function deactivate() {
			$token    = sanitize_text_field( wp_unslash( $_GET['token'] ?? '' ) );
			$site_url = sanitize_text_field( wp_unslash( $_GET['$site_url'] ?? '' ) );

		}

		public function validate() {
			$token    = sanitize_text_field( wp_unslash( $_GET['token'] ?? '' ) );
			$site_url = sanitize_text_field( wp_unslash( $_GET['$site_url'] ?? '' ) );

		}

		public function download_plugin(){
			$token    = sanitize_text_field( wp_unslash( $_GET['token'] ?? '' ) );
			$package  = sanitize_text_field( wp_unslash( $_GET['package'] ?? '' ) );
			$site_url = sanitize_text_field( wp_unslash( $_GET['site_url'] ?? '' ) );

			if ( 'revslider' === $package ) {
				$url = 'https://www.svlstudios.com/extras/plugins/revslider.zip';
			} elseif ( 'js_composer' === $package ) {
				$url = 'https://www.svlstudios.com/extras/plugins/visual-composer.zip';
			}

			echo esc_url( $url );
			die();
		}
	}

	new Qixi_Functions();
}
