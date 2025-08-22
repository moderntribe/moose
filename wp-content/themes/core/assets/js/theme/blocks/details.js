/**
 * @module core-details
 *
 * @description Initializes a fix for inconsistent CSS transitions inside <details> elements.
 * Some browsers skip re-triggering transitions when <details> is toggled multiple times.
 * This function forces a layout reflow by calling getComputedStyle on each child element,
 * ensuring that transitions reliably fire every time the <details> element is opened.
 */
export default function details() {
	document.querySelectorAll( 'details' ).forEach( ( detailsEl ) => {
		detailsEl.addEventListener( 'toggle', () => {
			// Touch computed style to force layout flush
			[ ...detailsEl.children ].forEach( ( el ) => {
				if ( el.tagName !== 'SUMMARY' ) {
					void getComputedStyle( el ).opacity;
				}
			} );
		} );
	} );
}
