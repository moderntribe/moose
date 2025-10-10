import delegate from 'delegate';

const el = {
	tabBlocks: document.querySelectorAll( '[data-js="tabs-block"]' ),
};

/**
 * @function initializeTabBlocks
 *
 * @description remove hidden attributes from the first tab panel in the block. Unfortunately we can't do this in the save() function due to block editor constraints
 */
const initializeTabBlocks = () => {
	el.tabBlocks.forEach( ( tabBlock ) => {
		const firstTabPanel = tabBlock.querySelector(
			'[role="tabpanel"]:first-child'
		);

		firstTabPanel.removeAttribute( 'hidden' );
	} );
};

/**
 * @function handleTabListKeyDown
 *
 * @description handle keyboard events within the tablist
 *
 * @param {*} e
 */
const handleTabListKeyDown = ( e ) => {
	// bail early if the key event isn't the left or right arrow keys
	const keyEvents = [ 'ArrowLeft', 'ArrowRight' ];

	if ( ! keyEvents.includes( e.key ) ) {
		return;
	}

	// get element indicies to determine where to go next
	const container = e.delegateTarget.closest( '[data-js="tabs-block"]' );
	const tabButtons = [ ...container.querySelectorAll( '[role="tab"]' ) ];
	const currentIndex = tabButtons.indexOf(
		container.ownerDocument.activeElement
	);
	const lastIndex = tabButtons.length - 1;

	// handle right arrow key
	if ( e.key === 'ArrowRight' ) {
		// If the current trigger is the last, then cycle to the first. Otherwise, go to the next.
		const nextIndex = currentIndex + 1 > lastIndex ? 0 : currentIndex + 1;
		switchTabs( tabButtons[ nextIndex ] );
		tabButtons[ nextIndex ].focus();
	}

	// handle left arrow key
	if ( e.key === 'ArrowLeft' ) {
		// If the current trigger is the first, then cycle to the last. Otherwise, go to the previous.
		const prevIndex = currentIndex - 1 < 0 ? lastIndex : currentIndex - 1;
		switchTabs( tabButtons[ prevIndex ] );
		tabButtons[ prevIndex ].focus();
	}
};

/**
 * @function hideTabPanel
 *
 * @description actions required to hide a particular tab
 *
 * @param {*} tab
 * @param {*} trigger
 */
const hideTabPanel = ( tab, trigger ) => {
	tab.setAttribute( 'hidden', true );
	trigger.setAttribute( 'aria-selected', 'false' );
	trigger.setAttribute( 'tabindex', '-1' );
};

/**
 * @function showTabPanel
 *
 * @description actions required to show a particular tab
 *
 * @param {*} tab
 * @param {*} trigger
 */
const showTabPanel = ( tab, trigger ) => {
	tab.removeAttribute( 'hidden' );
	trigger.setAttribute( 'aria-selected', 'true' );
	trigger.removeAttribute( 'tabindex' );
};

/**
 * @function switchTabs
 *
 * @description handles switching the selected tab
 *
 * @param {*} targetTabButton
 */
const switchTabs = ( targetTabButton ) => {
	// check if aria-controls is set
	const selectedTabId = targetTabButton.getAttribute( 'aria-controls' );

	if ( ! selectedTabId ) {
		return;
	}

	// grab all tab panels
	const container = targetTabButton.closest( '[data-js="tabs-block"]' );
	const tabs = container.querySelectorAll( '[role="tabpanel"]' );

	tabs.forEach( ( tab ) => {
		// grab tab button via the aria-labelledby attribute
		const tabButton = container.querySelector(
			`#${ tab.getAttribute( 'aria-labelledby' ) }`
		);

		// determine if we should hide or show this specific panel
		( tab.id === selectedTabId ? showTabPanel : hideTabPanel )(
			tab,
			tabButton
		);
	} );
};

/**
 * @function handleTabClick
 *
 * @description handle click on tab element
 *
 * @param {*} e
 */
const handleTabClick = ( e ) =>
	e.delegateTarget.getAttribute( 'aria-selected' ) === 'false'
		? switchTabs( e.delegateTarget )
		: false;

/**
 * @function bindEvents
 *
 * @description bind events to elements related to the module
 */
const bindEvents = () => {
	delegate( el.tabBlocks, '[role="tab"]', 'click', handleTabClick );
	delegate(
		el.tabBlocks,
		'[role="tablist"]',
		'keydown',
		handleTabListKeyDown
	);
};

/**
 * @function init
 *
 * @description kick off the functionality
 */
const init = () => {
	bindEvents();
	initializeTabBlocks();
};

init();
