/**
 * @module
 * @exports init
 * @description dynamic styles for post cards
 */

const el = {};

/**
 * @function setColumnBorders
 *
 * @description add column-end class to post cards that are at the end of columns
 */
const setColumnBorders = () => {
	if ( ! el.containers.length ) {
		return;
	}

	el.containers.forEach( ( container ) => {
		const postCards = container.querySelectorAll(
			'.c-post-card__layout-horizontal'
		);
		const total = postCards.length;

		if ( total > 0 ) {
			// Remove existing column-end classes
			postCards.forEach( ( card ) => {
				card.classList.remove( 'c-post-card__column-end' );
			} );

			// Get computed grid template columns to determine column count
			const computedStyle = window.getComputedStyle( container );
			const gridTemplateColumns = computedStyle.getPropertyValue(
				'grid-template-columns'
			);

			// Count the number of columns by splitting the grid-template-columns value
			const columnCount = Math.min(
				gridTemplateColumns
					.split( ' ' )
					.filter( ( val ) => val !== 'none' ).length,
				4
			);

			// Calculate the last item index for each column and add class
			for ( let col = 1; col <= columnCount; col++ ) {
				// Find the last item in this column
				const lastItemInColumn =
					Math.floor( ( total - col ) / columnCount ) * columnCount +
					col;

				if ( lastItemInColumn > 0 && lastItemInColumn <= total ) {
					const lastCard = postCards[ lastItemInColumn - 1 ]; // Convert to 0-based index
					if ( lastCard ) {
						lastCard.classList.add( 'c-post-card__column-end' );
					}
				}
			}
		}
	} );
};

/**
 * @function cacheElements
 *
 * @description Cache elements for this module
 */
const cacheElements = () => {
	el.containers = document.querySelectorAll( '.wp-block-post-template' );
};

/**
 * @function bindEvents
 *
 * @description Bind events for this module
 */
const bindEvents = () => {
	document.addEventListener(
		'modern_tribe/resize_executed',
		setColumnBorders
	);
};

/**
 * @function init
 *
 * @description Initializes the post cards dynamic styles.
 */
const init = () => {
	cacheElements();
	setColumnBorders();
	bindEvents();
};

export default init;
