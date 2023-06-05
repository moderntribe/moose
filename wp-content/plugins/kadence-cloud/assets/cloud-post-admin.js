/*global kb_admin_cloud_params */
;(function ( $, window ) {
	$('.kadence-cloud-rebuild-thumbnail').on( 'click', function( event ) {
		event.preventDefault();
		var $button = $( event.target );
		$button.closest( '.column-image' ).find( '.spinner' ).addClass( 'is-active' );
		$.ajax( {
			type: 'POST',
			url: kb_admin_cloud_params.ajax_url,
			data: {
				action           : 'kadence_cloud_regenerate_featured_image',
				post_id          : $button.data( 'post-id' ),
				security         : kb_admin_cloud_params.ajax_nonce
			},
			dataType: 'json',
			success: function( response ) {
				$button.closest( '.column-image' ).find( '.spinner' ).removeClass('is-active');
				if ( response && response.url ) {
					// have url.
					$has_image = $button.closest( '.column-image' ).find( 'img' );
					if ( $has_image.length ) {
						$has_image.attr('srcset', '' );
						$has_image.attr('src', response.url );
					} else {
						$button.closest( '.column-image' ).prepend('<img src="' + response.url + '" style="max-width:200px; border: 2px solid #eee; height:auto">');
					}
				}
				if ( response && ! response.success && response.data ) {
					$button.closest( '.column-image' ).prepend('<span>' + response.data + '</span>');
				}
				//console.log( response );
				// if ( response.success ) {
				// 	if ( 'done' === response.data.step ) {
						
				// 	} else {
						
				// 	}
				// }
			}
		} ).fail( function( response ) {
			$button.closest( '.column-image' ).find( '.spinner' ).removeClass('is-active');
			window.console.log( response );
		} );
	} );

})( jQuery, window );
