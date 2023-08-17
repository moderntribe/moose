/**
 * @module
 * @exports ready
 * @description The core dispatcher for the dom ready event javascript.
 */

import animation from './block-animation.js';

/**
 * @function init
 * @description The core dispatcher for init across the codebase.
 */

const init = () => {
	// add block animation controls

	animation();

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
