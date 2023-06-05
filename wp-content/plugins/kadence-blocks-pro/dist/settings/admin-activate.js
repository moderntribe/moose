/**
 * Ajax install the Theme Plugin
 *
 */
(function($, window, document, undefined){
	"use strict";

	$(function(){
		$('.kt-install-blocks-btn').on( 'click', function( event ) {
			var $button = $( event.target );
			event.preventDefault();
			/**
			 * Keep button from running twice
			 */
			if ( $button.hasClass( 'updating-message' ) || $button.hasClass( 'button-disabled' ) ) {
				return;
			}

			/**
			 * Install a plugin
			 *
			 * @return void
			 */
			function installPlugin(){

				$.ajax({
					url : $button.data('install-url'),
					type: 'GET',
					data: {},
					beforeSend: function () {
						buttonStatusInProgress( $button.data('installing-label') );
					},
					success: function( reposnse ) {
						buttonStatusInstalled( $button.data('installed-label') );
						activatePlugin();
					},
					error: function (xhr, ajaxOptions, thrownError) {
						// Installation failed
						buttonStatusDisabled( 'Error' );
					}
				});
			}

			/**
			 * Activate a plugin
			 *
			 * @return void
			 */
			function activatePlugin(){

				$.ajax({
					url : $button.data('activate-url'),
					type: 'GET',
					data: {},
					beforeSend: function () {
						buttonStatusInProgress( $button.data('activating-label') );
					},
					success: function( reposnse ) {
						buttonStatusDisabled( $button.data('activated-label') );
						location.replace( $button.data('redirect-url') );
					},
					error: function (xhr, ajaxOptions, thrownError) {
						 // Activation failed
						console.log( xhr.responseText );
						buttonStatusDisabled( 'Error' );
					}
				});
			}

			/**
			 * Change button status to in-progress
			 *
			 * @return void
			 */
			function buttonStatusInProgress( message ){
				$button.addClass('updating-message').removeClass('button-disabled kt-not-installed installed').text( message );
			}

			/**
			 * Change button status to disabled
			 *
			 * @return void
			 */
			function buttonStatusDisabled( message ){
				$button.removeClass('updating-message kt-not-installed installed')
				.addClass('button-disabled')
				.text( message );
			}

			/**
			 * Change button status to installed
			 *
			 * @return void
			 */
			function buttonStatusInstalled( message ){
				$button.removeClass('updating-message kt-not-installed')
					.addClass('installed')
					.text( message );
			}


			if( $button.data('action') === 'install' ){
				installPlugin();
			} else if( $button.data('action') === 'activate' ){
				activatePlugin();
			}
		});
	});
})(jQuery, window, document);
