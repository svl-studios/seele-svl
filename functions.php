<?php
/**
 * Functions for Summerville Votes (Seele Child Theme).
 *
 * @package    Summerville Votes (Seele Child Theme)
 * @author     Requite Designs
 * @copyright  Copyright (c) 2019 Requite Designs
 * @link       http://www.requitedesigns.com
 * @since      1.0.0
 */

/**
 * Load child theme stylesheet.
 */
function req_childtheme_style() {
	global $svl_options;

	$theme     = wp_get_theme();
	$child_ver = $theme->get( 'Version' );

	wp_enqueue_style(
		'req-main-style-child-css',
		get_stylesheet_directory_uri() . '/style.css',
		array( 'req-main-styles-css' ),
		$child_ver,
		'all'
	);

	wp_enqueue_script(
		'redux-blockUI',
		get_stylesheet_directory_uri() . '/admin/vote/vendor/jquery.blockUI.js',
		array( 'jquery' ),
		$child_ver,
		true
	);

	wp_enqueue_script(
		'req-summerville-votes-button',
		get_stylesheet_directory_uri() . '/admin/vote/button.js',
		array( 'jquery' ),
		$child_ver,
		true
	);

	wp_localize_script(
		'req-summerville-votes-button',
		'svl_votes',
		array(
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
			'nonce'   => wp_create_nonce( 'svl_votes_ajax' ),
			'event'   => $svl_options['svl_event'],
			'color'   => $svl_options['svl_event_typo']['color'],
		)
	);
}

add_action( 'wp_enqueue_scripts', 'req_childtheme_style' );

/**
 * Load child theme specific functions
 */
function svl_setup() {
	require_once get_stylesheet_directory() . '/admin/vote/config.php';
}

add_action( 'after_setup_theme', 'svl_setup', 9 );

require_once get_stylesheet_directory() . '/admin/vote/shortcode.php';

/**
 * Run SVL Votes AJAX
 */
function svl_ajax() {
	if ( isset( $_POST['nonce'] ) ) {
		if ( wp_verify_nonce( sanitize_key( $_POST['nonce'] ), 'svl_votes_ajax' ) ) {
			$vote = isset( $_POST['vote'] ) ? sanitize_text_field( wp_unslash( $_POST['vote'] ) ) : '';

			if ( '' !== $vote ) {
				$votes = get_option('svl_votes', array() );
				print_r($votes);

				echo 'Vote cast: ' . esc_html( $vote );

				if ( isset( $votes[ $vote ] ) ) {
					$votes[ $vote ] ++;
				} else {
					$votes[ $vote ] = 1;
				}

				update_option( 'svl_votes', $votes );
			}
		}
	}

	die();
}

add_action( 'wp_ajax_svl_votes_ajax', 'svl_ajax' );
