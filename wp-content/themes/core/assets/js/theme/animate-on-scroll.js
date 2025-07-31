/**
 * @module
 * @exports init
 * @description functions for handling elements that should change on scroll
 */

const el = {};

/**
 * @function handleIntersection
 *
 * @description Callback function for when an element comes into view
 *
 * @param {*} entries
 */
const handleIntersection = ( entries ) => {
	entries.forEach( ( entry ) => {
		if ( entry.isIntersecting ) {
			entry.target.classList.remove( 'is-exiting-view' );
			entry.target.classList.add( 'is-scrolled-into-view' );
			entry.target.classList.add( 'is-scrolled-into-view-first-time' );
		} else {
			entry.target.classList.remove( 'is-scrolled-into-view' );
			entry.target.classList.add( 'is-exiting-view' );
		}
	} );
};

/**
 * @function attachObservers
 *
 * @description attach intersection observers to elements
 */
const attachObservers = () => {
	if ( el.aosElements.length ) {
		const observer = new window.IntersectionObserver( handleIntersection, {
			threshold: 0.25,
		} );

		el.aosElements.forEach( ( element ) => observer.observe( element ) );
	}

	if ( el.aos50Elements.length ) {
		const observer = new window.IntersectionObserver( handleIntersection, {
			threshold: 0.5,
		} );

		el.aos50Elements.forEach( ( element ) => observer.observe( element ) );
	}

	if ( el.aos75Elements.length ) {
		const observer = new window.IntersectionObserver( handleIntersection, {
			threshold: 0.75,
		} );

		el.aos75Elements.forEach( ( element ) => observer.observe( element ) );
	}

	if ( el.aosFullElements.length ) {
		const observer = new window.IntersectionObserver( handleIntersection, {
			threshold: 1,
		} );

		el.aosFullElements.forEach( ( element ) =>
			observer.observe( element )
		);
	}
};

/**
 * @function cacheElements
 *
 * @description Cache elements for this module
 */
const cacheElements = () => {
	/**
	 * Note that the below selectors would need to change if the values of
	 * the animationPosition values change in theme.json (or the
	 * block-animations.js file).
	 */

	// grabs elements that should animate when the element is 25% in view
	el.aosElements = document.querySelectorAll( '.is-animated-on-scroll-25' );

	// grabs elements that should animate when 50% of the element is in view
	el.aos50Elements = document.querySelectorAll( '.is-animated-on-scroll-50' );

	// grabs elements that should animate when 75% of the element is in view
	el.aos75Elements = document.querySelectorAll( '.is-animated-on-scroll-75' );

	// grabs elements that should animate when the entire element is in view
	el.aosFullElements = document.querySelectorAll(
		'.is-animated-on-scroll-100'
	);
};

/**
 * @function init
 *
 * @description Kick off this module's functionality
 */
const init = () => {
	cacheElements();
	attachObservers();
};

export default init;
