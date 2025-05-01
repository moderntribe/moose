import { ready, triggerCustomEvent } from 'utils/events.js';
import { bodyLock } from 'utils/tools.js';
import { HEADER_BREAKPOINT } from 'config/options.js';

const el = {
	header: document.querySelector( '.site-header' ),
	wrapper: document.querySelector( '[data-js="site-header-search-wrapper"]' ),
};

const state = {
	searchActive: false,
};

/**
 * @function toggleOverlayOff
 *
 * @description removes class that displays overlay behind header elements
 */
const toggleOverlayOff = () => {
	state.searchActive = false;
	el.header.classList.remove( 'show-overlay' );
	el.wrapper.classList.remove( 'active' );
	el.overlay.setAttribute( 'aria-hidden', 'true' );
	bodyLock( false );
};

/**
 * @function toggleOverlayOff
 *
 * @description adds class that displays overlay behind header elements
 */
const toggleOverlayOn = () => {
	state.searchActive = true;
	triggerCustomEvent( 'modern_tribe/search_open' );
	el.header.classList.add( 'show-overlay' );
	el.wrapper.classList.add( 'active' );
	el.overlay.setAttribute( 'aria-hidden', 'false' );
	el.input.focus();
	bodyLock( true );
};

/**
 * @function handleSearchToggleClick
 *
 * @description handles adding/removing active class & aria attributes for global search overlay
 *
 * @param {Event} e
 */
const handleSearchToggleClick = ( e ) => {
	e.stopPropagation();

	if ( state.searchActive ) {
		toggleOverlayOff();
		return;
	}

	toggleOverlayOn();
};

/**
 * @function handlePropagation
 *
 * @description prevent propagation of events into search overlay
 *
 * @param {Event} e
 */
const handlePropagation = ( e ) => {
	e.stopPropagation();
};

const cacheElements = () => {
	el.wrapper = document.querySelector(
		'[data-js="site-header-search-wrapper"]'
	);
	el.toggle = el.wrapper.querySelector( '[data-js="toggle-search-overlay"]' );
	el.overlay = el.wrapper.querySelector(
		'[data-js="site-header-search-overlay"]'
	);
	el.input = el.wrapper.querySelector(
		'.site-header-search__overlay-form-input'
	);
};

/**
 * @function bindEvents
 *
 * @description add events to elements related to this module
 */
const bindEvents = () => {
	el.wrapper.addEventListener( 'click', handlePropagation );
	el.toggle.addEventListener( 'click', handleSearchToggleClick );

	if ( window.innerWidth >= HEADER_BREAKPOINT ) {
		document.addEventListener(
			'modern_tribe/off_nav_click',
			toggleOverlayOff
		);
		document.addEventListener(
			'modern_tribe/mega_menu_open',
			toggleOverlayOff
		);
		document.addEventListener(
			'modern_tribe/standard_menu_open',
			toggleOverlayOff
		);
	}
};

/**
 * @function initSearch
 *
 * @description sets up events on elements / initial classes
 */
const initSearch = () => {
	if ( ! el.header ) {
		return;
	}

	cacheElements();
	bindEvents();
};

ready( initSearch );
