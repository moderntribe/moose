/**
 * Registers a new block provided a unique name and an object defining its behavior.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-registration/
 */
import { registerBlockType } from '@wordpress/blocks';

/**
 * Lets webpack process CSS, SASS or SCSS files referenced in JavaScript files.
 * All files containing `style` keyword are bundled together. The code used
 * gets applied both to the front of your site and to the editor.
 *
 * @see https://www.npmjs.com/package/@wordpress/scripts#using-css
 */
import './style.pcss';
import './editor.pcss';

/**
 * Internal dependencies
 */
import Edit from './edit';
import metadata from './block.json';

/**
 * Every block starts by registering a new block type definition.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-registration/
 */
registerBlockType( metadata.name, {
	...metadata,

	icon: {
		src: (
			<svg
				xmlns="http://www.w3.org/2000/svg"
				width="200"
				height="200"
				viewBox="0 0 200 200"
				fill="#000"
			>
				<path d="M157.458 4a8 8 0 0 1 8 8v65.712a8 8 0 0 1-2.343 5.657l-76.644 76.643a23.46 23.46 0 0 1-7.612 5.087 23.47 23.47 0 0 1-17.958 0 23.46 23.46 0 0 1-7.612-5.087L9.445 116.167a23.46 23.46 0 0 1-5.086-7.611 23.47 23.47 0 0 1-1.786-8.979 23.46 23.46 0 0 1 6.872-16.59L86.089 6.343A8 8 0 0 1 91.746 4h65.712zm-8 16H95.059l-74.3 74.301a7.46 7.46 0 0 0-2.186 5.276c0 .98.193 1.95.568 2.856a7.46 7.46 0 0 0 1.618 2.42l43.846 43.846a7.46 7.46 0 0 0 10.552 0l74.301-74.301V20zM189 34.884a8 8 0 0 1 8 8V93.02a8 8 0 0 1-2.343 5.657L99.793 193.54a8 8 0 0 1-11.314-11.313L181 89.706V42.884a8 8 0 0 1 8-8zM119 53.5a5.5 5.5 0 1 0-11 0 5.5 5.5 0 1 0 11 0zM113.5 32c11.874 0 21.5 9.626 21.5 21.5S125.374 75 113.5 75 92 65.374 92 53.5 101.626 32 113.5 32z" />
			</svg>
		),
	},

	/**
	 * @see ./edit.js
	 */
	edit: Edit,
} );
