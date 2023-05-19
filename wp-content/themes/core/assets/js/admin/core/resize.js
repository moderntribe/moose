/**
 * @module
 * @exports resize
 * @description Kicks in any third party plugins that operate on a sitewide basis.
 */

import { triggerCustomEvent } from 'utils/events';
import viewportDims from './viewport-dims';

const resize = () => {
	// code for resize events can go here

	viewportDims();

	triggerCustomEvent( 'modern_tribe/resize_executed' );

	console.log( 'Moose Admin: Resized' );
};

export default resize;
