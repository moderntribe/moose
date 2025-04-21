import { ready, triggerCustomEvent } from 'utils/events.js';
import globalState from 'config/state.js';
import { HEADER_BREAKPOINT } from 'config/options.js';
import { bodyLock } from 'utils/tools.js';

const el = {
	body: null,
	toggle: null,
};

const classes = {
	mobileMenuShown: 'mobile-menu-shown',
};

/**
 * @function maybeRemoveActiveMobileMenuState
 *
 * @description checks if active mobile menu state can/should be removed before removing it
 */
const maybeRemoveActiveMobileMenuState = () => {
	if (
		el.body.classList.contains( classes.mobileMenuShown ) &&
		globalState.isMobileMenuShown &&
		window.innerWidth > HEADER_BREAKPOINT
	) {
		el.body.classList.remove( classes.mobileMenuShown );
		globalState.isMobileMenuShown = false;
		bodyLock( false );
	}
};

/**
 * @function handleMobileMenuToggleClick
 *
 * @description handle click of mobile menu toggle button
 */
const handleMobileMenuToggleClick = () => {
	if (
		el.body.classList.contains( classes.mobileMenuShown ) &&
		globalState.isMobileMenuShown
	) {
		el.body.classList.remove( classes.mobileMenuShown );
		globalState.isMobileMenuShown = false;
		bodyLock( false );
	} else {
		el.body.classList.add( classes.mobileMenuShown );
		globalState.isMobileMenuShown = true;
		bodyLock( true );
		triggerCustomEvent( 'modern_tribe/mobile_menu_open' );
	}
};

/**
 * @function bindEvents
 *
 * @description bind events to cached elements
 */
const bindEvents = () => {
	el.toggle.addEventListener( 'click', handleMobileMenuToggleClick );

	// handle resize
	document.addEventListener(
		'modern_tribe/resize_executed',
		maybeRemoveActiveMobileMenuState
	);
};

/**
 * @function cacheElements
 *
 * @description save elements for later use
 */
const cacheElements = () => {
	el.body = document.body;
	el.toggle = document.querySelector( '[data-js="menu-toggle"]' );
};

/**
 * @function init
 *
 * @description kick of this modules functionality
 */
const init = () => {
	cacheElements();
	bindEvents();
};

ready( init );
