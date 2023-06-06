/**
 * @module
 * @exports resize
 * @description Kicks in any third party plugins that operate on a sitewide basis.
 */

import { triggerCustomEvent } from 'utils/events.js';
import viewportDims from './viewport-dims';

const resize = () => {
	// code for resize events can go here

	viewportDims();

	triggerCustomEvent( 'modern_tribe/resize_executed' );
};

export default resize;
