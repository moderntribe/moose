import { registerBlockType } from '@wordpress/blocks';
import edit from './edit';
import save from './save';
import metadata from './block.json';

import './style.pcss';

registerBlockType( metadata.name, {
	...metadata,
	edit,
	save,
} );
