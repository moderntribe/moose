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
				width="200"
				height="200"
				viewBox="0 0 200 200"
				fill="none"
				xmlns="http://www.w3.org/2000/svg"
			>
				<path
					fillRule="evenodd"
					clipRule="evenodd"
					d="M0 60C0 37.3563 18.3563 19 41 19H159C181.644 19 200 37.3563 200 60C200 82.6437 181.644 101 159 101H41C18.3563 101 0 82.6437 0 60ZM184 60C184 46.1929 172.807 35 159 35H41C27.1929 35 16 46.1929 16 60C16 73.8071 27.1929 85 41 85H159C172.807 85 184 73.8071 184 60Z"
					fill="currentcolor"
				/>
				<path
					fillRule="evenodd"
					clipRule="evenodd"
					d="M18 133C18 128.582 21.5817 125 26 125H174C178.418 125 182 128.582 182 133C182 137.418 178.418 141 174 141H26C21.5817 141 18 137.418 18 133Z"
					fill="currentcolor"
				/>
				<path
					fillRule="evenodd"
					clipRule="evenodd"
					d="M18 173C18 168.582 21.5817 165 26 165H132C136.418 165 140 168.582 140 173C140 177.418 136.418 181 132 181H26C21.5817 181 18 177.418 18 173Z"
					fill="currentcolor"
				/>
			</svg>
		),
	},

	/**
	 * @see ./edit.js
	 */
	edit: Edit,
} );
