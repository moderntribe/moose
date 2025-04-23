/**
 * @module
 * @exports masthead
 * @description global masthead and navigation scripts for mobile and desktop.
 */

import { HEADER_BREAKPOINT } from 'config/options.js';

const el = {
	header: document.querySelector( '.site-header' ),
	navigation: document.querySelector( '.site-header__navigation' ),
};

const state = {
	elementsCloned: false,
}

const duplicateNode = ( wrapper, node, prepend = true ) => {
	node.classList.add( 'cloned-mobile-element' );

	if ( ! prepend ) {
		wrapper.append( node );
		return;
	}

	wrapper.prepend( node );
};

const createMobileMenu = () => {
	/**
	 * Duplicate nodes for the mobile nav.
	 *
	 * USAGE: duplicate elements in the order you need them to stack. If you're prepending, duplicate the bottom element
	 * first. Order will be bottom to top. If you're appending, duplicate top item 1st.
	 */
	duplicateNode( el.navigation, el.search );
	duplicateNode( el.navigation, el.cta );
	duplicateNode( el.navigation, el.utilityNav, false );
};

const handleResize = () => {
	if ( window.innerWidth < HEADER_BREAKPOINT && ! state.elementsCloned ) {
		cloneElements();
		createMobileMenu();
	}
};

const cloneElements = () => {
	el.cta = el.header.querySelector( '.site-header__cta' ).cloneNode( true );
	el.search = el.header
		.querySelector( '[data-js="site-header-search-overlay"]' )
		.cloneNode( true );
	el.utilityNav = el.header
		.querySelector( '.site-header__utility-nav' )
		.cloneNode( true );
	state.elementsCloned = true;
};

const bindEvents = () => {
	document.addEventListener( 'modern_tribe/resize_executed', handleResize );
};

const init = () => {
	if ( ! el.header || ! el.navigation ) {
		return;
	}

	if (window.innerWidth < HEADER_BREAKPOINT) {
		cloneElements();
		createMobileMenu();
	}

	bindEvents();
};

export default init;
