/**
 * @module
 * @exports ready
 * @description The core dispatcher for the dom ready event javascript.
 */

import { debounce } from '../../utils/tools';

import resize from './resize';
import viewportDims from './viewport-dims';
import blockStyles from './block-styles';

/**
 * @function bindEvents
 * @description Bind global event listeners here,
 */

const bindEvents = () => {
	window.addEventListener( 'resize', debounce( resize, 200 ) );
};

/**
 * @function init
 * @description The core dispatcher for init across the codebase.
 */

const init = () => {
	// set initial states

	viewportDims();

	// initialize global events

	bindEvents();

	// removes core block styles as needed
	blockStyles();

	console.info(
		'Moose Admin: Initialized all javascript that targeted document ready.'
	);
};

/**
 * @function domReady
 * @description Export our dom ready enabled init.
 */

const domReady = () => {
	wp.domReady( init );
};

export default domReady;
