/**
 * Mega Nav Item view script.
 *
 * Controls front end UI interactions with the mega-nav-item block in the masthead.
 */

import { ready, triggerCustomEvent } from 'utils/events.js';

const el = {
	megaMenuItems: document.querySelectorAll( '.wp-block-tribe-mega-nav-item' ),
};

const cacheElements = () => {
	el.masthead = document.querySelector( '.site-header' );
};

const maybeResetMenuItems = () => {
	el.megaMenuItems.forEach( ( item ) =>
		item.classList.remove( 'menu-item-active' )
	);
};

const openMenuItem = ( wrapper ) => {
	maybeResetMenuItems();

	wrapper.classList.add( 'menu-item-active' );
	triggerCustomEvent( 'modern_tribe/mega_menu_open' );
};

const closeMenuItem = ( wrapper ) => {
	wrapper.classList.remove( 'menu-item-active' );
};

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

const bindEvents = () => {
	el.megaMenuItems.forEach( ( item ) => {
		item.querySelector( '[data-js="menu-item-toggle"]' ).addEventListener(
			'click',
			handleItemToggle
		);
	} );

	document.addEventListener(
		'modern_tribe/search_open',
		maybeResetMenuItems
	);

	document.addEventListener(
		'modern_tribe/standard_menu_open',
		maybeResetMenuItems
	);

	document.addEventListener(
		'modern_tribe/close_on_escape',
		maybeResetMenuItems
	);
};

const init = () => {
	if ( ! el.megaMenuItems || el.megaMenuItems.length <= 0 ) {
		return;
	}

	cacheElements();
	bindEvents();
};

ready( init );
