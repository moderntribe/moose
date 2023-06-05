/**
 * Load media uploader on pages with our custom metabox
 */
jQuery( document ).ready( function( $ ) {

	'use strict';

	// Instantiates the variable that holds the media library frame.
	var metaFileFrame;

	// Runs when the media button is clicked.
	$( '.kb-meta-file-upload' ).click( function( e ) {

		// Prevents the default action from occuring.
		e.preventDefault();
		wp.media.controller.Library.prototype.defaults.contentUserSetting = false;
		// Get the field target
		var field = $( this ).data( 'file-uploader-target' );
		if ( metaFileFrame ) {
			// Open frame
			metaFileFrame.open();
			return;
		} else {
			// Sets up the media library frame
			metaFileFrame = wp.media.frames.metaFileFrame = wp.media( {
				title: kb_meta_file.title,
				button: { text: kb_meta_file.button },
				library: {
					type: [ 'application/json' ]
				},
				multiple: false,
			} );
		}
		// Runs when an image is selected.
		metaFileFrame.on( 'select', function() {

			// Grabs the attachment selection and creates a JSON representation of the model.
			var media_attachment = metaFileFrame.state().get('selection').first().toJSON();

			// Sends the attachment Id  to our custom file input field.
			$( field ).val( media_attachment.id );
			$( '#kb_file_upload_btn' ).hide();
			$( 'ul.kb-icon-previews' ).hide();
			$( '.kb-file-title' ).hide();
			$( '.kb-meta-topbar-options' ).hide();
			$( '#kb_file_edit_btn' ).show();
			$( 'p.kb-meta-save-notice' ).show();
		} );

		// Runs when an image is selected.
		metaFileFrame.on( 'open', function() {
			if ( ! $( field ).val() ) {
				metaFileFrame.state().reset();
			}
		} );

		// Opens the media library frame.
		metaFileFrame.open();

	} );

} );
