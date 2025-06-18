/**
 * @module
 * @description Vanilla JS cross browser utilities
 */

/**
 * @function debounce
 * @description Run a callback after a specified wait duration.
 * @param {Function} callback
 * @param {number}   wait
 */

export const debounce = ( callback, wait = 200 ) => {
	let timeoutId = null;

	return ( ...args ) => {
		window.clearTimeout( timeoutId );

		timeoutId = window.setTimeout( () => {
			callback.apply( null, args );
		}, wait );
	};
};

/**
 * @function bodyLock
 * @description Lock or unlock page scrolling.
 * @param {boolean} lock
 */
export const bodyLock = ( lock = false ) => {
	if ( lock ) {
		document.body.style.overflow = 'hidden';
		return;
	}

	document.body.style.overflow = 'visible';
};
