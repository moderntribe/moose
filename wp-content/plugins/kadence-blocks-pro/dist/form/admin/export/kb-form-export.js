/*global kb_admin_form_params */
;(function ( $, window ) {
	/**
	 * entryExportForm handles the export process.
	 */
	var entryExportForm = function( $btn ) {
		this.$btn = $btn;
		this.$form = this.$btn.closest( '#kb-form-entries-form' );
		this.xhr   = false;

		// Initial state.
		this.$form.find('.kadence-exporter-progress').val( 0 );

		// Methods.
		this.processStep = this.processStep.bind( this );

		// Events.
		$btn.on( 'click', { entryExportForm: this }, this.onSubmit );
	};

	/**
	 * Handle export form submission.
	 */
	entryExportForm.prototype.onSubmit = function( event ) {
		event.preventDefault();

		var currentDate    = new Date(),
			day            = currentDate.getDate(),
			month          = currentDate.getMonth() + 1,
			year           = currentDate.getFullYear(),
			timestamp      = currentDate.getTime(),
			filename       = 'kb-entry-export-' + day + '-' + month + '-' + year + '-' + timestamp + '.csv';

		event.data.entryExportForm.$form.addClass( 'kadence-exporter__exporting' );
		event.data.entryExportForm.$form.find('.kadence-exporter-progress').show();
		event.data.entryExportForm.$form.find('.kadence-exporter-progress').val( 0 );
		event.data.entryExportForm.$btn.prop( 'disabled', true );
		event.data.entryExportForm.processStep( 1, $( this ).serialize(), '', filename );
	};

	/**
	 * Process the current export step.
	 */
	entryExportForm.prototype.processStep = function( step, data, columns, filename ) {
		var $this         = this,
			//selected_columns = $( '.kadence-entry-exporter-columns' ).val(),
			//export_meta      = $( '#kadence-exporter-extra-meta:checked' ).length ? 1: 0,
			export_form_id  = $( '#filter-by-form' ).val();
		$.ajax( {
			type: 'POST',
			url: kb_admin_form_params.ajaxurl,
			data: {
				action           : 'kadence_form_entries_export',
				step             : step,
				columns          : columns,
				//selected_columns : selected_columns,
				//export_extra_meta      : export_extra_meta,
				export_form_id  : export_form_id,
				filename         : filename,
				security         : kb_admin_form_params.wpnonce
			},
			dataType: 'json',
			success: function( response ) {
				if ( response.success ) {
					if ( 'done' === response.data.step ) {
						$this.$form.find('.kadence-exporter-progress').val( response.data.percentage );
						$this.$form.find('.kadence-exporter-progress').hide();
						window.location = response.data.url;
						setTimeout( function() {
							$this.$form.removeClass( 'kadence-exporter__exporting' );
							$this.$btn.prop( 'disabled', false );
						}, 2000 );
					} else {
						$this.$form.find('.kadence-exporter-progress').val( response.data.percentage );
						$this.processStep( parseInt( response.data.step, 10 ), data, response.data.columns, filename );
					}
				}
			}
		} ).fail( function( response ) {
			window.console.log( response );
		} );
	};
	/**
	 * Function to call entryExportForm on jquery selector.
	 */
	$.fn.kb_entry_export = function() {
		new entryExportForm( this );
		return this;
	};

	$( '#kb-export-csv-submit' ).kb_entry_export();

})( jQuery, window );
