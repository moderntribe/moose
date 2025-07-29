import { registerBlockType } from '@wordpress/blocks';

import './style.pcss';

import Edit from './edit';
import save from './save';
import metadata from './block.json';

registerBlockType( metadata.name, {
	icon: (
		<svg
			width="24"
			height="24"
			viewBox="0 0 24 24"
			fill="none"
			xmlns="http://www.w3.org/2000/svg"
		>
			<path
				d="M4 15v-3H2V2h12v3h2v3h2v10H6v-3H4zm7-12c-1.1 0-2 .9-2 2h4a2 2 0 0 0-2-2zm-7 8V6H3v5h1zm7-3h4a2 2 0 1 0-4 0zm-5 6V9H5v5h1zm9-1a2 2 0 1 0 .001-3.999A2 2 0 0 0 15 13zm2 4v-2c-5 0-5-3-10-3v5h10z"
				fill="black"
			></path>
		</svg>
	),

	/**
	 * @see ./edit.js
	 */
	edit: Edit,

	/**
	 * @see ./save.js
	 */
	save,
} );
