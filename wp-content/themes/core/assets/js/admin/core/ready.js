/**
 * @module
 * @exports ready
 * @description The core dispatcher for the dom ready event javascript.
 */

import _ from 'lodash'; // eslint-disable-line import/no-extraneous-dependencies

import resize from './resize';
import viewportDims from './viewport-dims';
import blockStyles from './block-styles';

import { on, ready } from '../../utils/events';

/**
 * @function bindEvents
 * @description Bind global event listeners here,
 */

const bindEvents = () => {
	on( window, 'resize', _.debounce( resize, 200, false ) );
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
	ready( init );
};

export default domReady;
