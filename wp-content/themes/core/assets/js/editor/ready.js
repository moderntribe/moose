/**
 * @module
 * @exports ready
 * @description The core dispatcher for the dom ready event javascript.
 */

/**
 * @function init
 * @description The core dispatcher for init across the codebase.
 */

const init = () => {
	// intentionally left blank for now

	console.info(
		'Editor: Initialized all javascript that targeted document ready.'
	);
};

/**
 * @function ready
 * @description Export our dom ready enabled init.
 */

const ready = () => {
	init();
};

export default ready;
