/**
 * Mega Nav Item view script.
 *
 * Controls front end UI interactions with the mega-nav-item block in the masthead.
 */

import { ready, triggerCustomEvent } from 'utils/events.js';

const el = {
	standardMenuItems: document.querySelectorAll(
		'.wp-block-tribe-standard-menu-item'
	),
};

const state = {
	menuActive: false,
	activeItem: '',
}

const cacheElements = () => {
	el.masthead = document.querySelector( '.site-header' );
};

/**
 * @function maybeResetMenuItems
 *
 * @description close and reset any active menu items based on another event
 */
const maybeResetMenuItems = () => {
	document
		.querySelectorAll( '.wp-block-tribe-standard-menu-item' )
		.forEach( ( item ) => item.classList.remove( 'menu-item-active' ) );

	state.menuActive = false;
	state.activeItem = '';
};

/**
 * @function openMenuItem
 *
 * @description opens the sibling mega menu container on button click
 * @param {HTMLElement} wrapper
 */
const openMenuItem = ( wrapper ) => {
	maybeResetMenuItems();

	wrapper.classList.add( 'menu-item-active' );
	triggerCustomEvent( 'modern_tribe/standard_menu_open' );
	state.menuActive = true;
	state.activeItem = wrapper;
};

/**
 * @function closeMenuItem
 *
 * @description closes the sibling menu item container on button click
 * @param {HTMLElement} wrapper
 */
const closeMenuItem = ( wrapper ) => {
	wrapper.classList.remove( 'menu-item-active' );
	state.menuActive = false;
	state.activeItem = '';
};

/**
 * @function handleItemToggle
 *
 * @description handle mega menu button clicks to open or close the menu
 * @param {event} event
 */
const handleItemToggle = ( event ) => {
	const menuButton = event.currentTarget;
	const wrapper = menuButton.parentNode;

	// When opening a mega menu item that is not currently open, check to see if we need to close any open menu items.
	if ( ! wrapper.classList.contains( 'menu-item-active' ) ) {
		openMenuItem( wrapper );
		return;
	}

	closeMenuItem( wrapper );
};

/**
 * @function bindToggleEvents
 *
 * @description an isolated event binding function used to bind on DOM render as well as when cloned elements are created
 */
const bindToggleEvents = () => {
	document
		.querySelectorAll( '.wp-block-tribe-standard-menu-item' )
		.forEach( ( item ) => {
			const button = item.querySelector(
				'[data-js="standard-menu-item-toggle"]'
			);

			if ( ! button ) {
				return;
			}

			button.addEventListener( 'click', handleItemToggle );
		} );
};

/**
 * @function handleClickOutside
 * @description close this feature if a click occurs outside of its container
 * @param {Object} e
 */
const handleClickOutside = ( e ) => {
	if ( state.menuActive && ! state.activeItem.contains( e.target ) ) {
		maybeResetMenuItems();
	}
}

/**
 * @function bindEvents
 *
 * @description bind events either on ready or if a custom event is triggered
 */
const bindEvents = () => {
	bindToggleEvents();

	// Reset all menu items if any of these events are triggered
	document.addEventListener(
		'modern_tribe/search_open',
		maybeResetMenuItems
	);

	document.addEventListener(
		'modern_tribe/mega_menu_open',
		maybeResetMenuItems
	);

	document.addEventListener(
		'modern_tribe/close_on_escape',
		maybeResetMenuItems
	);

	// Bind click events to newly cloned menu items for the mobile nav
	document.addEventListener(
		'modern_tribe/cloned_elements_attached',
		bindToggleEvents
	);

	document.addEventListener( 'click', handleClickOutside );
};

/**
 * @function init
 *
 * @description kick of this modules functionality
 */
const init = () => {
	if ( ! el.standardMenuItems || el.standardMenuItems.length <= 0 ) {
		return;
	}

	cacheElements();
	bindEvents();
};

ready( init );
