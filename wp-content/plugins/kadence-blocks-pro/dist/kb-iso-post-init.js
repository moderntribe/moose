/**
 * init Isotope
 */
(function() {
	'use strict';
	window.kadencePostBlockISO = {
		/**
		 * Radio Button Group
		 */
		radioButtonGroup: function( buttonGroup ) {
			buttonGroup.addEventListener( 'click', function( event ) {
				// only work with buttons
				if ( !matchesSelector( event.target, 'button' ) ) {
					return;
				}
				buttonGroup.querySelector('.is-active').classList.remove('is-active');
				event.target.classList.add('is-active');
			});
		},
		/**
		 * Get element's offset.
		 */
		initContainer: function( container, iso, index ) {
			if ( container.querySelector('.kt-post-grid-wrap').classList.contains("kt-post-grid-layout-grid-wrap") ) {
				iso[index] = new Isotope( container.querySelector('.kt-post-grid-wrap'), {
					itemSelector: '.kt-post-masonry-item',
					layoutMode: 'fitRows'
				});
				// bind filter button click
				container.querySelector('.kb-post-filter-container').addEventListener( 'click', function( event ) {
					// only work with buttons
					if ( !matchesSelector( event.target, 'button' ) ) {
						return;
					}
					var filterValue = event.target.getAttribute('data-filter');
					// use matching filter function
					iso[index].arrange({ filter: filterValue });
				});
				window.kadencePostBlockISO.radioButtonGroup( container.querySelector('.kb-post-filter-container') );
			} else if ( container.querySelector('.kt-post-grid-wrap').classList.contains("kt-post-grid-layout-masonry-wrap") ) {
				iso[index] = new Isotope( container.querySelector('.kt-post-grid-wrap'), {
					itemSelector: '.kt-post-masonry-item',
					layoutMode: 'masonry'
				});
				// bind filter button click
				container.querySelector('.kb-post-filter-container').addEventListener( 'click', function( event ) {
					// only work with buttons
					if ( !matchesSelector( event.target, 'button' ) ) {
						return;
					}
					var filterValue = event.target.getAttribute('data-filter');
					// use matching filter function
					iso[index].arrange({ filter: filterValue });
				});
				window.kadencePostBlockISO.radioButtonGroup( container.querySelector('.kb-post-filter-container') );
			}
		},
		init: function() {
			var containers = document.querySelectorAll('.wp-block-kadence-postgrid.kb-filter-enabled');
			var iso = [];
			for ( var i=0, len = containers.length; i < len; i++ ) {
				window.kadencePostBlockISO.initContainer( containers[i], iso, i );
			}
		},
	}
	if ( 'loading' === document.readyState ) {
		// The DOM has not yet been loaded.
		document.addEventListener( 'DOMContentLoaded', window.kadencePostBlockISO.init );
	} else {
		// The DOM has already been loaded.
		window.kadencePostBlockISO.init();
	}
})();
