/**
 * init Isotope
 */
(function() {
	'use strict';
	window.kadencePortfolioBlockISO = {
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
			if ( container.querySelector('.kb-portfolio-grid-wrap').classList.contains("kb-portfolio-grid-layout-grid-wrap") ) {
				iso[index] = new Isotope( container.querySelector('.kb-portfolio-grid-wrap'), {
					itemSelector: '.kb-portfolio-masonry-item',
					layoutMode: 'fitRows'
				});
				// bind filter button click
				container.querySelector('.kb-portfolio-filter-container').addEventListener( 'click', function( event ) {
					// only work with buttons
					if ( !matchesSelector( event.target, 'button' ) ) {
						return;
					}
					var filterValue = event.target.getAttribute('data-filter');
					// use matching filter function
					iso[index].arrange({ filter: filterValue });
				});
				kadencePortfolioBlockISO.radioButtonGroup( container.querySelector('.kb-portfolio-filter-container') );
			} else if ( container.querySelector('.kb-portfolio-grid-wrap').classList.contains("kb-portfolio-grid-layout-masonry-wrap") ) {
				iso[index] = new Isotope( container.querySelector('.kb-portfolio-grid-wrap'), {
					itemSelector: '.kb-portfolio-masonry-item',
					layoutMode: 'masonry'
				});
				// bind filter button click
				container.querySelector('.kb-portfolio-filter-container').addEventListener( 'click', function( event ) {
					// only work with buttons
					if ( !matchesSelector( event.target, 'button' ) ) {
						return;
					}
					var filterValue = event.target.getAttribute('data-filter');
					// use matching filter function
					iso[index].arrange({ filter: filterValue });
				});
				kadencePortfolioBlockISO.radioButtonGroup( container.querySelector('.kb-portfolio-filter-container') );
			}
		},
		init: function() {
			var containers = document.querySelectorAll('.wp-block-kadence-portfoliogrid.kb-filter-enabled');
			var iso = [];
			for ( var i=0, len = containers.length; i < len; i++ ) {
				kadencePortfolioBlockISO.initContainer( containers[i], iso, i );
			}
		},
	}
	if ( 'loading' === document.readyState ) {
		// The DOM has not yet been loaded.
		document.addEventListener( 'DOMContentLoaded', kadencePortfolioBlockISO.init );
	} else {
		// The DOM has already been loaded.
		kadencePortfolioBlockISO.init();
	}
})();
