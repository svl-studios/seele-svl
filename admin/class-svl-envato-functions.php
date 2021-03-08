<?php

if ( ! class_exists( 'SVL_Envato_Functions' ) ) {
	class SVL_Envato_Functions {
		public function __construct() {
			add_action( 'wp_ajax_svl_get_download', array( $this, 'download_plugin' ) );
			add_action( 'wp_ajax_nopriv_svl_get_download', array( $this, 'download_plugin' ) );

			add_action( 'wp_ajax_svl_deactivate_license', array( $this, 'deactivate' ) );
			add_action( 'wp_ajax_nopriv_svl_deactivate_license', array( $this, 'deactivate' ) );

			add_action( 'wp_ajax_svl_activate_license', array( $this, 'activate' ) );
			add_action( 'wp_ajax_nopriv_svl_activate_license', array( $this, 'activate' ) );

			add_action( 'wp_ajax_svl_validate_token', array( $this, 'validate' ) );
			add_action( 'wp_ajax_nopriv_svl_validate_token', array( $this, 'validate' ) );

			add_action( 'wp_ajax_svl_create_nonce', array( $this, 'nonce' ) );
			add_action( 'wp_ajax_nopriv_svl_create_nonce', array( $this, 'nonce' ) );
		}

		private function envato_sale_lookup( $code ) {
			$envato_token = 'REa9PE3LSFCOo6NbtP4CtXd5k172tanc';
			$user_agent   = 'SVL Studios: ' . sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ?? '' ) );

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
					$result     = 'error';
					$registered = false;

					if ( 400 === (int) $api_result['error'] ) {
						$message = 'License already registered.';
					} elseif ( 0 === $api_result['error'] ) {
						$message = 'Invalid code.';
					} elseif ( 200 !== (int) $api_result['error'] ) {
						$message = 'Failed to validate code due to an error: HTTP ' . $api_result['error'];
					}
				} else {

					// Make sure the product ID is Qixi.
					if ( (int) $product === $api_result['item']['id'] ) {

						// Get buyer.
						$buyer = strtolower( $api_result['buyer'] );

						// User array.
						$svl_users = get_option( 'svl_users' );

						$purchase_count = $api_result['purchase_count'];

						// Ensure buyer exists in our database.
						if ( array_key_exists( $buyer, $svl_users ) ) {
							$db_products = $svl_users[ $buyer ] ?? '';

							// Does item ID exist.
							if ( array_key_exists( $product, $db_products ) ) {
								$db_site_urls = $svl_users[ $buyer ][ $product ] ?? '';

								if ( array_key_exists( $site_url, $db_site_urls ) ) {
									$db_code = $svl_users[ $buyer ][ $product ][ $site_url ] ?? '';

									// site exists in database, is registered.  Is there a valid code?
									if ( '' !== $db_code && $db_code === $key ) {

										// Code exists and is valid.
										$registered = true;
									} else {

										// Code does not exist or is invalid.
										$registered = false;
										$message    = 'Invalid code.';
									}
								} else {
									$lic_count = count( $svl_users[ $buyer ][ $product ] );

									if ( $purchase_count > $lic_count ) {

										// Registered site does not exist.  If purchase count is greater than licenses in database,
										// then user can register another licence.
										$registered = true;
									} elseif ( $purchase_count <= $lic_count ) {

										// If purchase count equals or less than license in database, then reject register aattempt.
										$registered = false;
										$message    = 'Invalid code.';
									}
								}
							}
						}
					}
				}

				if ( $registered ) {

					// Write entry to the user database.
					$x = array(
						strtolower( $buyer ) => array(
							$product => array(
								$site_url => $key,
							),
						),
					);

					update_option( 'svl_users', $x );

					$message = '';
					$code    = $key;
					$result  = 'success';
				} else {
					$code   = '';
					$result = 'error';
				}

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

	new SVL_Envato_Functions();
}
