import { addFilter } from '@wordpress/hooks';

/**
 * @function setAlignmentSupports
 * @description Filters alignment setting for specified core blocks.
 */

const setAlignmentSupports = ( settings, name ) => {
	if ( name !== 'core/embed' ) {
		return settings;
	}

	return Object.assign( {}, settings, {
		supports: Object.assign( {}, settings.supports, {
			align: [ 'wide', 'full' ],
		} ),
	} );
};

addFilter(
	'blocks.registerBlockType',
	'tribe/filter-alignment',
	setAlignmentSupports
);
