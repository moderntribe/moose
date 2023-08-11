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
					d="M157.458 4C161.876 4 165.458 7.58172 165.458 12V77.7122C165.458 79.8339 164.615 81.8688 163.115 83.369L86.4713 160.012C84.2927 162.191 81.7062 163.92 78.8597 165.099C76.0131 166.278 72.9621 166.885 69.881 166.885C66.7999 166.885 63.7489 166.278 60.9023 165.099C58.0557 163.92 55.4692 162.191 53.2906 160.012L9.44543 116.167C7.26669 113.989 5.53841 111.402 4.35927 108.556C3.18013 105.709 2.57324 102.658 2.57324 99.5769C2.57324 96.4958 3.18013 93.4449 4.35927 90.5983C5.53841 87.7517 7.26669 85.1652 9.44544 82.9866L86.0889 6.34315C87.5892 4.84285 89.624 4 91.7458 4L157.458 4ZM149.458 20L95.0594 20L20.759 94.3005C20.0661 94.9933 19.5163 95.8161 19.1413 96.7214C18.7663 97.6267 18.5732 98.597 18.5732 99.577C18.5732 100.557 18.7663 101.527 19.1413 102.433C19.5163 103.338 20.0659 104.16 20.7589 104.853L64.6045 148.699C65.2974 149.392 66.1201 149.942 67.0254 150.317C67.9307 150.692 68.9011 150.885 69.881 150.885C70.8609 150.885 71.8312 150.692 72.7365 150.317C73.6418 149.942 74.4644 149.392 75.1573 148.699L149.458 74.3985V20Z"
					fill="currentcolor"
				/>
				<path
					fillRule="evenodd"
					clipRule="evenodd"
					d="M189 34.8835C193.418 34.8835 197 38.4652 197 42.8835V93.0196C197 95.1413 196.157 97.1762 194.657 98.6764L99.7925 193.54C96.6683 196.665 91.603 196.665 88.4788 193.54C85.3546 190.416 85.3546 185.351 88.4788 182.227L181 89.7059V42.8835C181 38.4652 184.581 34.8835 189 34.8835Z"
					fill="currentcolor"
				/>
				<path
					fillRule="evenodd"
					clipRule="evenodd"
					d="M119 53.5C119 50.4624 116.537 48 113.5 48C110.462 48 108 50.4624 108 53.5C108 56.5376 110.462 59 113.5 59C116.537 59 119 56.5376 119 53.5ZM113.5 32C125.374 32 135 41.6259 135 53.5C135 65.3741 125.374 75 113.5 75C101.626 75 91.9997 65.3741 91.9997 53.5C91.9997 41.6259 101.626 32 113.5 32Z"
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
