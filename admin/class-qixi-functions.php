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

			update_option( 'svl_users', array( 'SVLStudios' ) );
		}

		private function envato_sale_lookup( $code ) {
			$envato_token = 'REa9PE3LSFCOo6NbtP4CtXd5k172tanc';
			$user_agent   = 'SVL Studios: Qixi Theme';

			$code = trim( $code );
			if ( preg_match( '/^([a-f0-9]{8})-(([a-f0-9]{4})-){3}([a-f0-9]{12})$/i', $code ) ) {
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
			} else {
				return array(
					'error' => 0,
				);
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
				$site_url = str_replace( array( 'http://', 'https://' ), '', $site_url );

				$api_result = $this->envato_sale_lookup( $key );

				if ( isset( $api_result['error'] ) ) {
					$result = 'error';

					if ( 400 === (int) $api_result['error'] ) {
						$message = 'License already registered.';
					} elseif ( 0 === $api_result['error'] ) {
						$message = 'Invalid code.';
					} elseif ( 200 !== (int) $api_result['error'] ) {
						$message = 'Failed to validate code due to an error: HTTP ' . $api_result['error'];
					}
				} else {
					$message = '';
					$result  = 'success';
				}

				// Make sure the product ID is Qixi.
				if ( (int) $product === $api_result['item']['id'] ) {

					// Get buyer.
					$buyer = $api_result['buyer'];

					// User array.
					$svl_users = get_option( 'svl_users' );

					$purchase_count = $api_result['purchase_count'];

					// Ensure buyer exists in our database.
					if ( array_key_exists( $buyer, $svl_users ) ) {
						print_r( $svl_users[ $buyer ] );

						// Does item ID exist.
						if ( array_key_exists( $product, $svl_users[ $buyer ] ) ) {
							$lic_count = count( $svl_users[ $buyer ][ $product ] );

							if ( array_key_exists( $svl_users[ $buyer ][ $product ], $site_url ) ) {

								// site exists in database, is registered.
								$registered = true;
							} else {

								if ( $purchase_count > $lic_count ) {

									// Registered site does not exist.  If purchase count is greater than licenses in database,
									// then user can register another licence.
									$registered = true;
								} elseif ( $purchase_count <= $lic_count ) {

									// If purchase count equals or less than license in database, then reject register aattempt.
									$registered = false;
								}
							}

							foreach ( $svl_users[ $buyer ][ $product ] as $site => $code ) {
								if ( $site_url === $site ) {
									if ( $key === $code) {
										$registered = true;
									} else {
										$registered = false;
									}
								} else {

								}

								echo $site;
								echo $code;
							}

							echo count( $svl_users[ $buyer ][ $product ] );
						}
					}
				}




				// Write entry to the user database.
				$x = array(
					$buyer => array(
						$product => array(
							$site_url => $key,
						),
					),
				);

				update_option( 'svl_users', $x );
				print_r( get_option( 'svl_users' ) );
				die;

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

		public function download_plugin() {
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
