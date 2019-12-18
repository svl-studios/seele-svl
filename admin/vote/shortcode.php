<?php
/**
 * Summerville Votes Shortcode
 *
 * @package     Requite Core
 * @author      Requite Designs
 * @copyright   Copyright (c) 2019,Requite Designs
 * @link        http://www.requitedesigns.com
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'SummervilleVotes' ) ) {

	/**
	 * Class SummervilleVotes
	 */
	class SummervilleVotes {

		/**
		 * SummervilleVotes constructor.
		 */
		public function __construct() {
			add_shortcode( 'summerville_votes', array( $this, 'shortcode' ) );
			add_shortcode( 'summerville_event', array( $this, 'shortcode' ) );
			add_shortcode( 'summerville_event_date', array( $this, 'shortcode' ) );
			add_shortcode( 'summerville_event_vote', array( $this, 'shortcode' ) );
			add_shortcode( 'summerville_vote_tag', array( $this, 'shortcode' ) );
			add_shortcode( 'summerville_votes_results', array( $this, 'shortcode' ) );
		}

		/**
		 * Summerville Votes shortcode output.
		 *
		 * @param array  $atts    Shortcode attributes.
		 * @param null   $content Shortcode content.
		 * @param string $tag     Shortcode tag.
		 */
		public function shortcode( $atts, $content = null, $tag ) {
			global $svl_options;

			if ( 'summerville_votes' === $tag ) {
				echo '<div id="myNav" class="overlay" onclick="closeNav()">';
				echo '<div class="overlay-content">';
				echo '<div class="center svl-event"><img src="' . esc_url( $svl_options['svl_event_image']['background-image'] ) . '"></div>';
				echo '<h3><a href="#">Tap the screen to begin</a></h3>';
				echo '</div>';
				echo '</div>';

				// Waiting message.
				echo '<div id="svl-voting-message" style="display:none;">';
				echo '<img src="' . esc_url( get_stylesheet_directory_uri() ) . '/admin/vote/img/busy.gif" /><strong> Submitting vote...</strong><br>';
				echo '</div>';

				echo '<div class="container-fluid">';
				echo '<div class="row">';

				foreach ( $svl_options['svl_merchants'] as $idx => $val ) {
					$pos = strpos( $val, '|' );

					if ( false === $pos ) {
						$name = $val;
					} else {
						$name    = substr( $val, 0, $pos );
						$address = substr( $val, $pos + 1 );
					}

					echo '<div class="vote-container col-sm-' . esc_attr( $svl_options['svl_merchants_per_row'] ) . '">';
					echo '<div class="req-animated-button center">';
					echo '<a class="svl-merchant main_button  coloured large_btn req-animated-button " href="javascript:;" style="">' . esc_html( $name ) . '</a>';

					if ( isset( $address) && '' !== $address ) {
						echo '<div class="svl-merchant-address">' . esc_html( $address ) . '</div>';
					}

					echo '<input type="radio" name="svl-merchants" class="vote-item" value="' . esc_attr( $name ) . '">';
					echo '</div>';
					echo '</div>';
				}

				echo '</div>';
				echo '</div>';
			} elseif ( 'summerville_event' === $tag ) {
				if ( 'text' === $svl_options['svl_event_title_mode'] ) {
					echo '<div class="center svl-event">' . esc_html( $svl_options['svl_event'] ) . '</div>';
				} else {
					// var_dump($svl_options['svl_event_image']);
					echo '<div class="center svl-event"><img src="' . esc_url( $svl_options['svl_event_image']['background-image'] ) . '"><a href="javascript:void(0)" class="closebtn" onclick="openNav()">&times;</a></div>';
				}
			} elseif ( 'summerville_event_date' === $tag ) {
				echo '<div class="center svl-event-date">' . esc_html( $svl_options['svl_event_date'] ) . '</div>';
			} elseif ( 'summerville_event_vote' === $tag ) {
				echo '<div class="center svl-event-vote">' . esc_html( $svl_options['svl_event_vote'] ) . '</div>';
			} elseif ( 'summerville_vote_tag' === $tag ) {
				echo '<div class="center svl-vote-tag">' . esc_html( $svl_options['svl_vote_tag'] ) . '</div>';
			} elseif ( 'summerville_votes_results' === $tag ) {
				$votes = get_option( 'svl_votes' );

				if ( isset( $votes ) && is_array( $votes ) ) {
					arsort( $votes );

					echo '<table class="svl-results-table" data-nonce="' . wp_create_nonce( 'svl-clear-nonce' ) . '">';
					echo '<thead>';
					echo '<tr>';
					echo '<td>';
					echo 'Merchant';
					echo '</td>';
					echo '<td>';
					echo 'Total Votes';
					echo '</td>';
					echo '</tr>';
					echo '</thead>';
					echo '<tbody>';

					foreach ( $votes as $merchant => $total ) {
						echo '<tr>';
						echo '<td>';
						echo esc_html( $merchant );
						echo '</td>';
						echo '<td>';
						echo esc_html( $total );
						echo '</td>';
						echo '</tr>';
					}

					echo '</tbody>';
					echo '</table>';
				} else {
					echo '<h3 class="center">No voting results to display.</h3>';
				}
			}
		}
	}

	new SummervilleVotes();
}
