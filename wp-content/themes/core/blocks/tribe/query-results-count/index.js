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
					d="M85.5 152C122.227 152 152 122.227 152 85.5C152 48.7731 122.227 19 85.5 19C48.7731 19 19 48.7731 19 85.5C19 122.227 48.7731 152 85.5 152ZM85.5 168C131.063 168 168 131.063 168 85.5C168 39.9365 131.063 3 85.5 3C39.9365 3 3 39.9365 3 85.5C3 131.063 39.9365 168 85.5 168Z"
					fill="url(#paint0_linear_5_382)"
				/>
				<path
					fillRule="evenodd"
					clipRule="evenodd"
					d="M152.343 152.343C155.467 149.219 160.533 149.219 163.657 152.343L193.657 182.343C196.781 185.467 196.781 190.533 193.657 193.657C190.533 196.781 185.467 196.781 182.343 193.657L152.343 163.657C149.219 160.533 149.219 155.467 152.343 152.343Z"
					fill="url(#paint1_linear_5_382)"
				/>
				<path
					fillRule="evenodd"
					clipRule="evenodd"
					d="M105.091 43.0476C109.483 43.5289 112.653 47.4795 112.172 51.8715L104.5 121.872C104.019 126.263 100.068 129.434 95.6764 128.952C91.2844 128.471 88.1142 124.52 88.5955 120.129L96.2668 50.1285C96.7481 45.7365 100.699 42.5663 105.091 43.0476Z"
					fill="url(#paint2_linear_5_382)"
				/>
				<path
					fillRule="evenodd"
					clipRule="evenodd"
					d="M75.8439 43.0476C80.2358 43.5289 83.4061 47.4795 82.9247 51.8715L75.2535 121.872C74.7722 126.263 70.8216 129.434 66.4296 128.952C62.0376 128.471 58.8674 124.52 59.3487 120.129L67.02 50.1285C67.5013 45.7365 71.4519 42.5663 75.8439 43.0476Z"
					fill="url(#paint3_linear_5_382)"
				/>
				<path
					fillRule="evenodd"
					clipRule="evenodd"
					d="M43 70.8333C43 66.4151 46.5817 62.8333 51 62.8333L121 62.8334C125.418 62.8334 129 66.4151 129 70.8334C129 75.2516 125.418 78.8334 121 78.8334L51 78.8333C46.5817 78.8333 43 75.2516 43 70.8333Z"
					fill="url(#paint4_linear_5_382)"
				/>
				<path
					fillRule="evenodd"
					clipRule="evenodd"
					d="M43 101.105C43 96.687 46.5817 93.1053 51 93.1053L121 93.1053C125.418 93.1053 129 96.687 129 101.105C129 105.524 125.418 109.105 121 109.105L51 109.105C46.5817 109.105 43 105.524 43 101.105Z"
					fill="url(#paint5_linear_5_382)"
				/>
				<defs>
					<linearGradient
						id="paint0_linear_5_382"
						x1="29.6524"
						y1="29.6524"
						x2="167.974"
						y2="173.08"
						gradientUnits="userSpaceOnUse"
					>
						<stop stopColor="#3050E5" />
						<stop offset="1" stopColor="#B66CFF" />
					</linearGradient>
					<linearGradient
						id="paint1_linear_5_382"
						x1="29.6524"
						y1="29.6524"
						x2="167.974"
						y2="173.08"
						gradientUnits="userSpaceOnUse"
					>
						<stop stopColor="#3050E5" />
						<stop offset="1" stopColor="#B66CFF" />
					</linearGradient>
					<linearGradient
						id="paint2_linear_5_382"
						x1="29.6524"
						y1="29.6524"
						x2="167.974"
						y2="173.08"
						gradientUnits="userSpaceOnUse"
					>
						<stop stopColor="#3050E5" />
						<stop offset="1" stopColor="#B66CFF" />
					</linearGradient>
					<linearGradient
						id="paint3_linear_5_382"
						x1="29.6524"
						y1="29.6524"
						x2="167.974"
						y2="173.08"
						gradientUnits="userSpaceOnUse"
					>
						<stop stopColor="#3050E5" />
						<stop offset="1" stopColor="#B66CFF" />
					</linearGradient>
					<linearGradient
						id="paint4_linear_5_382"
						x1="29.6524"
						y1="29.6524"
						x2="167.974"
						y2="173.08"
						gradientUnits="userSpaceOnUse"
					>
						<stop stopColor="#3050E5" />
						<stop offset="1" stopColor="#B66CFF" />
					</linearGradient>
					<linearGradient
						id="paint5_linear_5_382"
						x1="29.6524"
						y1="29.6524"
						x2="167.974"
						y2="173.08"
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
