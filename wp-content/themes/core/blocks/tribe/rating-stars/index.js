import { registerBlockType } from '@wordpress/blocks';
import metadata from './block.json';
import edit from './edit';
import save from './save';

import './style.pcss';

registerBlockType( metadata.name, {
	...metadata,
	edit,
	save,
} );
