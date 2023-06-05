/**
 * Video Pop
 */
jQuery( document ).ready( function( $ ) {
	function kbProGetParameterByName(name, url = '') {
		name = name.replace(/[\[\]]/g, '\\$&');
		var regex = new RegExp('[?&]' + name + '(=([^&#]*)|&|#|$)'),
			results = regex.exec(url);
		if (!results) return null;
		if (!results[2]) return '';
		return decodeURIComponent(results[2].replace(/\+/g, ' '));
	}
	$( '.kadence-video-popup-link.kadence-video-type-external' ).magnificPopup( {
		type: 'iframe',
		removalDelay: 400,
		mainClass: 'mfp-kt-blocks',
		iframe: {
			markup: '<div class="mfp-iframe-scaler kb-class">' +
					'<div class="mfp-close"></div>' +
					'<iframe class="mfp-iframe" frameborder="0" allow="autoplay; fullscreen *" allowfullscreen="true"></iframe>' +
					'</div>',
			patterns: {
				youtube: {
					index: 'youtube.com/',
					id: 'v=',
					src: 'https://www.youtube.com/embed/%id%?autoplay=1&rel=0'
				},
				youtu: {
					index: 'youtu.be',
					id: function( url ) {
						var start_time = kbProGetParameterByName('t', url );
						// Capture everything after the hostname, excluding possible querystrings.
						var m = url.match( /^.+youtu.be\/([^?]+)/ );
						if ( null !== m ) {
							if ( start_time ) {
								return m[1] + '?autoplay=1&rel=0&start=' + start_time;
							} else {
								return m[1] + '?autoplay=1&rel=0';
							}
						}
						return null;
					},
					// Use the captured video ID in an embed URL. 
					// Add/remove querystrings as desired.
					src: '//www.youtube.com/embed/%id%'
				},
				youtubenocookiewatch: {
					index: 'youtube-nocookie.com/watch',
					id: 'v=',		
					// 	// Capture everything after the hostname, excluding possible querystrings.
					// 	var m = url.match( /^.+youtube-nocookie.com\/([^?]+)/ );
					// 	if ( null !== m ) {
					// 		return m[1];
					// 	}
					// 	return null;
					// },
					// Use the captured video ID in an embed URL. 
					// Add/remove querystrings as desired.
					src: '//www.youtube-nocookie.com/embed/%id%?autoplay=1&rel=0'
				},
				youtubenocookie: {
					index: 'youtube-nocookie.com/embed',
					id: function( url ) {		
						// Capture everything after the hostname, excluding possible querystrings.
						var m = url.match( /^.+youtube-nocookie.com\/([^?]+)/ );
						if ( null !== m ) {
							return m[1];
						}
						return null;
					},
					// // Use the captured video ID in an embed URL. 
					// // Add/remove querystrings as desired.
					src: '//www.youtube-nocookie.com/%id%?autoplay=1&rel=0'
				}
			}
		},
		callbacks: {
			beforeOpen: function() {
				this.st.iframe.markup = '<div class="mfp-with-anim">' + this.st.iframe.markup + '</div>';
				this.st.mainClass = this.st.mainClass + ' kadence-vpop-anim-' + this.st.el.attr( 'data-effect' ) + ' ' + this.st.el.attr( 'data-popup-class' );
			},
		},
	} );
	$( '.kadence-video-popup-link.kadence-video-type-local' ).each( function() {
		var id   = $( this ).attr( 'data-popup-id' );
		var auto = $( this ).attr( 'data-popup-auto' );
		$( this ).magnificPopup( {
			mainClass: 'mfp-kt-blocks',
			removalDelay: 400,
			items: {
				src: '#' + id,
				type: 'inline',
			},
			callbacks: {
				beforeOpen: function() {
					this.st.mainClass = this.st.mainClass + ' kadence-vpop-anim-' + this.st.el.attr( 'data-effect' ) + ' ' + this.st.el.attr( 'data-popup-class' );
				},
				open: function() {
					// Play video on open:
					if ( 'true' == auto ) {
						$( this.content ).find( 'video' )[ 0 ].play();
					}
				},
				close: function() {
					// Pause video on close:
					$( this.content ).find( 'video' )[ 0 ].pause();
				},
			},
		} );
	} );
} );
