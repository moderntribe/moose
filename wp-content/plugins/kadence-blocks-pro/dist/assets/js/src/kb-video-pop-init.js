/* eslint-disable no-var */
/* global SimpleLightbox */
/**
 * File kb-video-pop-init.js.
 * Gets video lighbox working for video popup block.
 */
( function() {
	'use strict';
	var kadenceBlocksProVideoLightbox = {
		/**
		 * Initiate the script to process all
		 */
		initAll: function() {
			var videos = document.querySelectorAll( '.kadence-video-popup-link' );
			for ( let i = 0; i < videos.length; i++ ) {
				if ( videos[ i ].classList.contains( 'kadence-video-type-local' ) ) {
					// eslint-disable-next-line vars-on-top
					var item = document.getElementById( videos[ i ].getAttribute( 'data-popup-id' ) );
					videos[ i ].addEventListener( 'click', function( event ) {
						event.preventDefault();
						//var auto = videos[ i ].getAttribute( 'data-popup-auto' );
						new SimpleLightbox.open( {
							content: item,
						} );
					} );
				}
			}
		},
		// Initiate the menus when the DOM loads.
		init: function() {
			if ( typeof SimpleLightbox == 'function' ) {
				kadenceBlocksProVideoLightbox.initAll();
			} else {
				// eslint-disable-next-line vars-on-top
				var initLoadDelay = setInterval( function() {
					if ( typeof SimpleLightbox == 'function' ) {
						kadenceBlocksProVideoLightbox.initAll();
						clearInterval( initLoadDelay );
					}
				}, 200 );
			}
		},
	};
	if ( 'loading' === document.readyState ) {
		// The DOM has not yet been loaded.
		document.addEventListener( 'DOMContentLoaded', kadenceBlocksProVideoLightbox.init );
	} else {
		// The DOM has already been loaded.
		kadenceBlocksProVideoLightbox.init();
	}
}() );
