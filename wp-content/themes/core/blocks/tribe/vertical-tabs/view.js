import { ready } from 'utils/events';

const WP_STACKED_BREAKPOINT = 781;
const el = {
	verticalTabsBlocks: null,
};
const state = {
	isMobile: false,
};
const selectors = {
	block: 'wp-block-tribe-vertical-tabs',
	tab: 'wp-block-tribe-vertical-tabs__tab',
	hiddenContent: 'wp-block-tribe-vertical-tabs__tab-hidden',
	tabContent: 'wp-block-tribe-vertical-tab',
	tabContentContainer: 'wp-block-tribe-vertical-tabs__tab-content',
};
const classes = {
	activeTab: 'active-tab',
};

/**
 * @function handleResize
 *
 * @description handle window resize event; conditionally move tab content
 */
const handleResize = () => {
	el.verticalTabsBlocks.forEach( ( block ) => {
		state.isMobile = window.innerWidth <= WP_STACKED_BREAKPOINT;

		maybeMoveTabContent( block );
	} );
};

/**
 * @function maybeMoveTabContent
 *
 * @description conditionally (based on width) copy tab content
 *
 * @param {*} block
 */
const maybeMoveTabContent = ( block ) => {
	const tabEls = block.querySelectorAll( `.${ selectors.tab }` );

	if ( ! state.isMobile ) {
		/* if tab content exists within tab content and we've moved to a desktop size,
		   move tab content to tab content container */
		tabEls.forEach( ( tabEl ) => {
			const tabContent = tabEl.getAttribute( 'aria-controls' );
			const tabContentEl = tabEl.querySelector( `#${ tabContent }` );

			// only try to copy if we find the tab content within the tab itself
			if ( tabContentEl ) {
				const tabContentCopy = tabContentEl.cloneNode( true );
				const tabContentContainer = block.querySelector(
					`.${ selectors.tabContentContainer }`
				);
				tabContentContainer.appendChild( tabContentCopy );
				tabContentEl.remove();
			}
		} );
	} else {
		/* if tab content exists within the tab content container and we've
		   moved to a mobile size, move tab content to tab itself */
		tabEls.forEach( ( tabEl ) => {
			const tabContent = tabEl.getAttribute( 'aria-controls' );
			const tabContentEl = tabEl
				.closest( `.${ selectors.block }` )
				.querySelector(
					`.${ selectors.tabContentContainer } #${ tabContent }`
				);

			// only try to copy if we find the tab content within the tab content container
			if ( tabContentEl ) {
				const tabContentCopy = tabContentEl.cloneNode( true );
				tabEl.appendChild( tabContentCopy );
				tabContentEl.remove();
			}
		} );
	}
};

/**
 * @function resetTabs
 *
 * @description resets "active" states on all tabs
 *
 * @param {*} tabEl
 */
const resetTabs = ( tabEl ) => {
	const tabParentEl = tabEl.closest( `.${ selectors.block }` );
	const tabs = tabParentEl.querySelectorAll( `.${ selectors.tab }` );

	tabs.forEach( ( tab ) => {
		const tabContent = tab.getAttribute( 'aria-controls' );
		const tabContentEl = tabEl
			.closest( `.${ selectors.block }` )
			.querySelector( `#${ tabContent }` );

		tab.setAttribute( 'aria-selected', 'false' );
		tab.setAttribute( 'tabindex', '0' );
		tabContentEl.setAttribute( 'hidden', '' );
	} );
};

/**
 * @function handleTabClick
 *
 * @description handle tab click event
 *
 * @param {*} e
 */
const handleTabClick = ( e ) => {
	const tabEl = e.currentTarget;

	if ( tabEl.classList.contains( classes.activeTab ) ) {
		return;
	}

	resetTabs( tabEl );

	const tabContent = tabEl.getAttribute( 'aria-controls' );
	const tabContentEl = tabEl
		.closest( `.${ selectors.block }` )
		.querySelector( `#${ tabContent }` );

	tabEl.setAttribute( 'aria-selected', 'true' );
	tabEl.setAttribute( 'tabindex', '-1' );
	tabContentEl.removeAttribute( 'hidden' );

	// on mobile, scroll to top of opened tab
	if ( state.isMobile ) {
		tabEl.scrollIntoView( {
			behavior: 'smooth',
		} );
	}
};

/**
 * @function bindEvents
 *
 * @description bind events to elements within the blocks
 */
const bindEvents = () => {
	el.verticalTabsBlocks.forEach( ( block ) => {
		// handle tab click event
		const tabs = block.querySelectorAll( `.${ selectors.tab }` );

		if ( tabs ) {
			tabs.forEach( ( tab ) => {
				tab.addEventListener( 'click', handleTabClick );
				tab.addEventListener( 'focus', handleTabClick );
			} );
		}
	} );

	// handle resize event
	document.addEventListener( 'modern_tribe/resize_executed', handleResize );
};

/**
 * @function setupBlocks
 *
 * @description handle setting up initial views of each block
 */
const setupBlocks = () => {
	el.verticalTabsBlocks.forEach( ( block ) => {
		// select the first "inner block" and show it
		const firstTabContentEl = block.querySelector(
			`.${ selectors.tabContent }:first-child`
		);

		firstTabContentEl.removeAttribute( 'hidden' );

		// conditionally (based on width) copy tab content into tabs for mobile view
		if ( window.innerWidth <= WP_STACKED_BREAKPOINT ) {
			state.isMobile = true;
			maybeMoveTabContent( block );
		}
	} );
};

/**
 * @function initVerticalTabs
 *
 * @description kick off this modules functionality
 */
const initVerticalTabs = () => {
	el.verticalTabsBlocks = document.querySelectorAll(
		`.${ selectors.block }`
	);

	if ( ! el.verticalTabsBlocks ) {
		return;
	}

	setupBlocks();
	bindEvents();
};

ready( initVerticalTabs );
