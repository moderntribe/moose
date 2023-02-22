/**
 * PostCSS Config
 *
 * Overrides WP-Scripts default config for postcss processing.
 *
 * Customizations:
 * - postcss-import: Add support for concatenating pcss partials via `@import` statements.
 *  	- Reference: https://www.npmjs.com/package/postcss-import
 * - postcss-preset-env: Sets config to process all features (stage 0 and above)
 *  	- Adds autoprefixer support for css grid
 *  	- Removes any transformations on (don't modify) css custom properties, :focus-visible, and :focus-within
 *  	- Reference: https://www.npmjs.com/package/postcss-preset-env
 * - cssnano: minimizes css files ONLY when a production build is run
 */

const isProduction = process.env.NODE_ENV === 'production';

/**
 * Replicates WP-Scripts config for CSS Nano.
 */
const cssNanoConfig = {
	preset: [
		'default',
		{
			discardComments: {
				removeAll: true,
			},
		},
	],
};

const plugins = [
	'postcss-import',
	[
		'postcss-preset-env',
		{
			stage: 0,
			autoprefixer: { grid: true },
			features: {
				'custom-properties': false,
				'focus-visible-pseudo-class': false,
				'focus-within-pseudo-class': false,
			},
		},
	],
];

module.exports = {
	plugins: isProduction
		? [ ...plugins, require( 'cssnano' )( cssNanoConfig ) ]
		: plugins,
};
