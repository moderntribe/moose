/* global GLightbox */
/**
 * File kb-glight-video-init.js.
 * Gets video lighbox working for video popup.
 */

(function() {
	'use strict';
	var kadenceBlocksProVideoLightbox = {
		cache: [],
		wrapper: [],
		trigger: [],
		/**
		 * Initiate the script to process all
		 */
		initAll: function() {
			kadenceBlocksProVideoLightbox.cache = document.querySelectorAll( '.kadence-video-popup-link.kadence-video-type-local' );
			for ( let i = 0; i < kadenceBlocksProVideoLightbox.cache.length; i++ ) {
				kadenceBlocksProVideoLightbox.wrapper[i] = document.getElementById( kadenceBlocksProVideoLightbox.cache[ i ].getAttribute( 'data-popup-id' ) );
				kadenceBlocksProVideoLightbox.cache[ i ].addEventListener( 'click', function( event ) {
					event.preventDefault();
					kadenceBlocksProVideoLightbox.trigger[i] = GLightbox({
						elements: [ {
							'href' : kadenceBlocksProVideoLightbox.wrapper[i].querySelector( '.kadence-local-video-popup' ).getAttribute( 'src' ),
							'type' : 'video',
							'source' : 'local',
						}],
						touchNavigation: true,
						skin: 'kadence-dark ' + kadenceBlocksProVideoLightbox.cache[ i ].getAttribute( 'data-popup-class' ),
						loop: false,
						openEffect: 'fade',
						closeEffect: 'fade',
						autoplayVideos: true,
						preload: false,
						plyr: {
							css: kadence_pro_video_pop.plyr_css,
							js: kadence_pro_video_pop.plyr_js,
							config: {
								hideControls: true,
							}
						}
					});
					kadenceBlocksProVideoLightbox.trigger[i].open();
					// kadenceBlocksProVideoLightbox.trigger[i].on('slide_before_load', (data) => {
					// 	const popClass = data.trigger.getAttribute( 'data-popup-class' );
					// 	const ligthboxBody = document.getElementById( 'glightbox-body' );
					// 	if ( popClass && ligthboxBody ) {
					// 		ligthboxBody.classList = '';
					// 		ligthboxBody.classList.add( 'glightbox-container' );
					// 		ligthboxBody.classList.add( 'glightbox-kadence-dark' );
					// 		ligthboxBody.classList.add( popClass );
					// 	}
					//   });
				} );
			}
			const lightbox = GLightbox({
				selector: '.kadence-video-popup-link:not(.kadence-video-type-local)',
				touchNavigation: true,
				skin: 'kadence-dark',
				loop: false,
				openEffect: 'fade',
				closeEffect: 'fade',
				autoplayVideos: true,
				preload: false,
				plyr: {
					css: kadence_pro_video_pop.plyr_css,
					js: kadence_pro_video_pop.plyr_js,
					config: {
						hideControls: true,
					}
				}
			});
			lightbox.on('slide_before_load', (data) => {
				const popClass = data.trigger.getAttribute( 'data-popup-class' );
				const ligthboxBody = document.getElementById( 'glightbox-body' );
				if ( popClass && ligthboxBody ) {
					ligthboxBody.classList = '';
					ligthboxBody.classList.add( 'glightbox-container' );
					ligthboxBody.classList.add( 'glightbox-kadence-dark' );
					ligthboxBody.classList.add( popClass );
				}
			  });
		},
		// Initiate the menus when the DOM loads.
		init: function() {
			if ( typeof GLightbox == 'function' ) {
				kadenceBlocksProVideoLightbox.initAll();
			} else {
				var initLoadDelay = setInterval( function(){ if ( typeof GLightbox == 'function' ) { kadenceBlocksProVideoLightbox.initAll(); clearInterval(initLoadDelay); } }, 200 );
			}
		}
	}
	if ( 'loading' === document.readyState ) {
		// The DOM has not yet been loaded.
		document.addEventListener( 'DOMContentLoaded', kadenceBlocksProVideoLightbox.init );
	} else {
		// The DOM has already been loaded.
		kadenceBlocksProVideoLightbox.init();
	}
})();