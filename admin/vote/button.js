/* global console, svl_votes, confirm, alert, jQuery, document */

function openNav() {
	document.getElementById("myNav").style.width = "100%";
}

function closeNav() {
	document.getElementById("myNav").style.width = "0%";
}

(function( $ ) {
	'use strict';

	$( document ).ready(
		function() {
			var button;
			var address;
			var radio;
			var voteNow;
			var clear;
			var table;

			button  = $( '.svl-merchant' );
			address = $( '.svl-merchant-address' );
			voteNow = $( '.svl-vote-now' );
			clear   = $( '.svl-clear-votes' );
			table   = $( '.svl-results-table' );

			if ( table.length === 0 ) {
				clear.addClass( 'hidden');
			} else {
				clear.removeClass( 'hidden');
			}

			$.fn.matchHeight._throttle       = 500;
			$.fn.matchHeight._maintainScroll = true;

			$( '.row' ).each(
				function() {
					$( this ).children( '.vote-container' ).matchHeight();
					$( this ).find( '.svl-merchant' ).matchHeight();
				}
			);

			openNav();

			clear.on (
				'click',
				function( e ) {
					var clearNonce;
					var data;

					if ( confirm( 'Are you sure you want to clear these voting results?  I mean really, REALLY sure?  Because once this is done, those results are gone forever.  So...be sure.' ) ) {
						clearNonce = table.data( 'nonce' );

						data = {
							action: 'svl_clear_ajax',
							nonce: clearNonce,
						};

						$.post(
							svl_votes.ajaxurl,
							data,
							function( response ) {
								console.log( response );

								if ( response === 'deleted' ) {
									location.reload( true );
								} else if (response === 'not deleted') {
									alert( 'Something went wrong.  Votes not cleared.  Better tell Kev...' );
								}

							}
						);
					} else {
						return;
					}
				}
			);

			button.on(
				'click',
				function( e ) {
					var skip = false;

					if ( $( this ).hasClass( 'selected' ) ) {
						skip = true;
					}

					button.removeClass( 'selected' );
					address.removeClass( 'selected' );
					voteNow.removeClass( 'disabled' );

					radio = $( this ).nextAll( 'input' );

					if ( ! skip ) {
						radio.prop( 'checked', true );

						$( this ).addClass( 'selected' );
						$( this ).next( '.svl-merchant-address' ).addClass( 'selected' );
					} else {
						radio.prop( 'checked',false );
						voteNow.addClass( 'disabled' );
					}
				}
			);

			voteNow.addClass( 'disabled' );

			voteNow.on(
				'click',
				function( e ) {
					var data;
					var checked;
					var waitMsg;

					checked = $( '.vote-item:checked' ).val();

					if ( '' === checked || undefined === checked || 'undefined' === checked ) {
						return;
					}

					data = {
						action: 'svl_votes_ajax',
						nonce: svl_votes.nonce,
						vote: checked
					};

					// Get default wait message.
					waitMsg = $( '#svl-voting-message h3' ).html();

					// Load wait message.
					$.blockUI(
						{
							message: '<h3>' + waitMsg + '</h3><br><h2 style="color:' + svl_votes.color + '">' + checked + '</font></h2><br>Thank you!<br>Enjoy the rest of the ' + svl_votes.event + '!',
							theme: false,
							css: {
								width: '500px',
								padding: '5px',
								top:  ($(window).height() - 400) /2 + 'px',
								left: ($(window).width() - 400) /2 + 'px'
							}
						}
					);

					setTimeout(function(){
							$.post(
								svl_votes.ajaxurl,
								data,
								function( response ) {
									console.log( response );
								}
							);

							$.unblockUI();

							openNav();

							button.removeClass( 'selected' );
							address.removeClass( 'selected' );
							voteNow.addClass( 'disabled' );
							button.nextAll( 'input' ).prop( 'checked', false );
						},
						5000
					);
				}
			);
		}
	);
})( jQuery );

