/**
 * @module
 * @exports ready
 * @description The core dispatcher for the dom ready event javascript.
 */

import blockAnimations from './block-animations';

/**
 * @function init
 * @description The core dispatcher for init across the codebase.
 */

const init = () => {
	// initialize block animation controls in editor
	blockAnimations();

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
