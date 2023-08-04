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
					d="M79 154.333H58.1667C43.8008 154.333 30.0233 148.626 19.865 138.468C9.70683 128.31 4 114.533 4 100.167C4 85.8007 9.70683 72.0232 19.865 61.865C30.0233 51.7068 43.8008 46 58.1667 46H79C81.2101 46 83.3298 46.8779 84.8926 48.4408C86.4554 50.0036 87.3333 52.1232 87.3333 54.3333C87.3333 56.5434 86.4554 58.6631 84.8926 60.2259C83.3298 61.7887 81.2101 62.6666 79 62.6666H58.1667C48.221 62.6666 38.6828 66.6175 31.6502 73.6501C24.6175 80.6828 20.6667 90.221 20.6667 100.167C20.6667 110.112 24.6175 119.651 31.6502 126.683C38.6828 133.716 48.221 137.667 58.1667 137.667H79C81.2101 137.667 83.3298 138.545 84.8926 140.107C86.4554 141.67 87.3333 143.79 87.3333 146C87.3333 148.21 86.4554 150.33 84.8926 151.893C83.3298 153.455 81.2101 154.333 79 154.333Z"
					fill="url(#paint0_linear_5_389)"
				/>
				<path
					d="M141.5 154.333H120.667C118.457 154.333 116.337 153.455 114.774 151.893C113.211 150.33 112.333 148.21 112.333 146C112.333 143.79 113.211 141.67 114.774 140.107C116.337 138.545 118.457 137.667 120.667 137.667H141.5C151.446 137.667 160.984 133.716 168.016 126.683C175.049 119.651 179 110.112 179 100.167C179 90.221 175.049 80.6827 168.016 73.6501C160.984 66.6175 151.446 62.6666 141.5 62.6666H120.667C118.457 62.6666 116.337 61.7887 114.774 60.2259C113.211 58.6631 112.333 56.5434 112.333 54.3333C112.333 52.1232 113.211 50.0036 114.774 48.4407C116.337 46.8779 118.457 46 120.667 46H141.5C155.866 46 169.643 51.7068 179.802 61.865C189.96 72.0232 195.667 85.8007 195.667 100.167C195.667 114.533 189.96 128.31 179.802 138.468C169.643 148.626 155.866 154.333 141.5 154.333Z"
					fill="url(#paint1_linear_5_389)"
				/>
				<path
					d="M141.5 108.5H58.1667C55.9565 108.5 53.8369 107.622 52.2741 106.059C50.7113 104.496 49.8333 102.377 49.8333 100.167C49.8333 97.9565 50.7113 95.8369 52.2741 94.2741C53.8369 92.7113 55.9565 91.8333 58.1667 91.8333H141.5C143.71 91.8333 145.83 92.7113 147.393 94.2741C148.955 95.8369 149.833 97.9565 149.833 100.167C149.833 102.377 148.955 104.496 147.393 106.059C145.83 107.622 143.71 108.5 141.5 108.5Z"
					fill="url(#paint2_linear_5_389)"
				/>
				<defs>
					<linearGradient
						id="paint0_linear_5_389"
						x1="30.4683"
						y1="60.9603"
						x2="95.7659"
						y2="180.752"
						gradientUnits="userSpaceOnUse"
					>
						<stop stopColor="#3050E5" />
						<stop offset="1" stopColor="#B66CFF" />
					</linearGradient>
					<linearGradient
						id="paint1_linear_5_389"
						x1="30.4683"
						y1="60.9603"
						x2="95.7659"
						y2="180.752"
						gradientUnits="userSpaceOnUse"
					>
						<stop stopColor="#3050E5" />
						<stop offset="1" stopColor="#B66CFF" />
					</linearGradient>
					<linearGradient
						id="paint2_linear_5_389"
						x1="30.4683"
						y1="60.9603"
						x2="95.7659"
						y2="180.752"
						gradientUnits="userSpaceOnUse"
					>
						<stop stopColor="#3050E5" />
						<stop offset="1" stopColor="#B66CFF" />
					</linearGradient>
				</defs>
			</svg>
		),
	},

	/**
	 * @see ./edit.js
	 */
	edit: Edit,
} );
