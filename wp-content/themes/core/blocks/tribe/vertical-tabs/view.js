import tabs from 'utils/tabs';

/**
 * @function init
 *
 * @description kick off this modules functionality
 */
const init = () => {
	tabs( document.querySelectorAll( '.b-vertical-tabs' ), {
		keyEvents: [ 'ArrowUp', 'ArrowDown' ],
		contentMergesOnMobile: true,
	} );
};

init();
