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

		private function envato_sale_lookup( $code ) {
			$envato_token = 'REa9PE3LSFCOo6NbtP4CtXd5k172tanc';
			$user_agent   = 'SVL Studios: Qixi Theme';

			$code = trim( $code );
			if ( ! preg_match( '/^([a-f0-9]{8})-(([a-f0-9]{4})-){3}([a-f0-9]{12})$/i', $code ) ) {
				throw new Exception( 'Invalid code' );
			}

			$args = array(
				'timeout' => 20,
				'headers' => array(
					'Authorization' => 'Bearer ' . $envato_token,
					'User-Agent'    => $user_agent,
				),
			);

			$response = wp_remote_get( 'https://api.envato.com/v3/market/author/sale?code=' . $code, $args );

			if ( ! is_wp_error( $response ) && is_array( $response ) && ! empty( $response['body'] ) ) {
				return (array) json_decode( $response['body'], true );
			}
		}

		public function nonce() {
			if ( isset( $_GET['nonce'] ) ) {
				$key = sanitize_text_field( wp_unslash( $_GET['nonce'] ?? '' ) );

				echo sanitize_key( wp_unslash( wp_create_nonce( $key ) ) );
				die();
			}
		}

		public function activate() {
			if ( isset( $_GET['nonce'] ) && isset( $_GET['action'] ) && wp_verify_nonce( sanitize_key( wp_unslash( $_GET['nonce'] ) ), sanitize_key( wp_unslash( $_GET['action'] ) ) ) ) {
				$key      = sanitize_text_field( wp_unslash( $_GET['key'] ?? '' ) );
				$product  = sanitize_text_field( wp_unslash( $_GET['product'] ?? '' ) );
				$site_url = sanitize_text_field( wp_unslash( $_GET['site_url'] ?? '' ) );

				$api_result = $this->envato_sale_lookup( $key );

				if ( isset( $api_result['error'] ) ) {
					$result = 'error';

					if ( 400 === (int) $api_result['error'] ) {
						$message = 'License already registered.';
					} elseif ( 200 !== (int) $api_result['error'] ) {
						$message = 'Failed to validate code due to an error: HTTP ' . $api_result['error'];
					}
				} else {
					$message = '';
					$result  = 'success';
				}

				// Buyer
				// Purchase Count
				// Item[id]
				echo $api_result['buyer'];
				echo $api_result['purchase_count'];
				echo $api_result['item']['id'];

				$res = array(
					'result'  => $result,
					'message' => $message,
					'token'   => $code,
				);

				echo wp_json_encode( $res );
				die();
			}
		}

		public function deactivate() {
			if ( isset( $_GET['nonce'] ) && isset( $_GET['action'] ) && wp_verify_nonce( sanitize_key( wp_unslash( $_GET['nonce'] ) ), sanitize_key( wp_unslash( $_GET['action'] ) ) ) ) {
				$token    = sanitize_text_field( wp_unslash( $_GET['token'] ?? '' ) );
				$site_url = sanitize_text_field( wp_unslash( $_GET['$site_url'] ?? '' ) );

				$array = array(
					'result' => 'success',
				);

				echo wp_json_encode( $array );

				die();
			}
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
