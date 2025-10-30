import { registerBlockType } from '@wordpress/blocks';

import Edit from './edit';
import save from './save';
import metadata from './block.json';

// Deprecated versions
import saveV1 from './deprecated/v1/save';
import 'swiper/css';
import 'swiper/css/a11y';
import 'swiper/css/autoplay';
import 'swiper/css/navigation';
import 'swiper/css/pagination';
import './style.pcss';

const v1 = {
	save: saveV1,
};

registerBlockType( metadata.name, {
	/**
	 * @see ./edit.js
	 */
	edit: Edit,

	/**
	 * @see ./save.js
	 */
	save,

	deprecated: [ v1 ],
} );
