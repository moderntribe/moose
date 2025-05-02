/**
 * @module
 * @exports masthead
 * @description global masthead and navigation scripts for mobile and desktop.
 */

import { HEADER_BREAKPOINT } from 'config/options.js';
import { triggerCustomEvent } from 'utils/events.js';

const el = {
	header: document.querySelector( '.site-header' ),
	navigation: document.querySelector( '.site-header__navigation' ),
};

const state = {
	elementsCloned: false,
};

/**
 * @function insertClonedNode
 *
 * @description appends or prepends a cloned node to the wrapper container
 * @param {HTMLElement} wrapper
 * @param {HTMLElement} node
 * @param {boolean} prepend
 */
const insertClonedNode = ( wrapper, node, prepend = true ) => {
	node.classList.add( 'cloned-mobile-element' );

	if ( ! prepend ) {
		wrapper.append( node );
		return;
	}

	wrapper.prepend( node );
};

/**
 * @function createMobileMenu
 *
 * @description inserts a cloned element to the designated wrapper and triggers a custom event
 */
const createMobileMenu = () => {
	/**
	 * Duplicate nodes for the mobile nav.
	 *
	 * USAGE: duplicate elements in the order you need them to stack. If you're prepending, duplicate the bottom element
	 * first. Order will be bottom to top. If you're appending, duplicate top item 1st.
	 */
	insertClonedNode( el.navigation, el.search );
	insertClonedNode( el.navigation, el.cta );
	insertClonedNode( el.navigation, el.utilityNav, false );

	triggerCustomEvent( 'modern_tribe/cloned_elements_attached' );
};

/**
 * @function cloneElements
 *
 * @description create a clone of various elements to use within the mobile menu.
 */
const cloneElements = () => {
	el.cta = el.header.querySelector( '.site-header__cta' ).cloneNode( true );
	el.search = el.header
		.querySelector( '[data-js="site-header-search-overlay"]' )
		.cloneNode( true );
	el.utilityNav = el.header
		.querySelector( '.site-header__utility-navigation' )
		.cloneNode( true );
	state.elementsCloned = true;
};

/**
 * @function handleResize
 *
 * @description run any function that need to be executed from the resize event.
 */
const handleResize = () => {
	if ( window.innerWidth < HEADER_BREAKPOINT && ! state.elementsCloned ) {
		cloneElements();
		createMobileMenu();
	}
};

/**
 * @function handleKeyboardEvents
 *
 * @description trigger custom events from `keyup` keyboard interactions.
 */
const handleKeyboardEvents = ( e ) => {
	if ( e.key === 'Escape' ) {
		triggerCustomEvent( 'modern_tribe/close_on_escape' );
	}
};

const bindEvents = () => {
	document.addEventListener( 'modern_tribe/resize_executed', handleResize );
	el.header.addEventListener( 'keyup', handleKeyboardEvents );
};

const init = () => {
	if ( ! el.header || ! el.navigation ) {
		return;
	}

	if ( window.innerWidth < HEADER_BREAKPOINT ) {
		cloneElements();
		createMobileMenu();
	}

	bindEvents();
};

export default init;
