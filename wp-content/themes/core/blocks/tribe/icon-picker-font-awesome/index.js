import { registerBlockType } from '@wordpress/blocks';
import edit from './edit';
import save from './save';
import metadata from './block.json';
import blockIcon from './block-icon';

import './style.pcss';

registerBlockType( metadata.name, {
	...metadata,
	icon: blockIcon,
	edit,
	save,
} );
