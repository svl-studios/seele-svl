(function( $ ) {
	'use strict';

	$( document ).ready(
		function() {
			var button;
			var address;
			var radio;
			var voteNow;

			button  = $( '.svl-merchant' );
			address = $( '.svl-merchant-address' );
			voteNow = $( '.svl-vote-now' );

			$.fn.matchHeight._throttle       = 500;
			$.fn.matchHeight._maintainScroll = true;

			$( '.row' ).each(
				function() {
					$( this ).children( '.vote-container' ).matchHeight();
					$( this ).find( '.svl-merchant' ).matchHeight();
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
					console.log( checked );

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
							message: '<h3>' + waitMsg + '</h3><br><h2><font color="' + svl_votes.color + '">' + checked + '</font></h2><br>Thank you!<br>Enjoy the rest of the ' + svl_votes.event + '!',
							theme: false,
							css: {
								width: '500px',
								padding: '5px'
							}
						}
					);

					setTimeout(
						() => {
							$.post(
								svl_votes.ajaxurl,
								data,
								function( response ) {
									console.log( response );
								}
							);

							$.unblockUI();

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

