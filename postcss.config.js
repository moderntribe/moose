/**
 * PostCSS Config
 *
 * Overrides WP-Scripts default config for postcss processing.
 *
 * Customizations:
 * - postcss-import: Add support for concatenating pcss partials via `@import` statements.
 *  	- Reference: https://www.npmjs.com/package/postcss-import
 * - postcssGlobalData: Add support for the referenced set of css partials to be used globally,
 * 		without individually importing the partials in every file where they are used.
 * - postcss-preset-env: Sets config to process all features (stage 0 and above)
 *  	- Adds autoprefixer support for css grid
 *  	- Removes any transformations on (don't modify) css custom properties, :focus-visible, and :focus-within
 *  	- Reference: https://www.npmjs.com/package/postcss-preset-env
 * - cssnano: minimizes css files ONLY when a production build is run
 */

const isProduction = process.env.NODE_ENV === 'production';
const pkg = require( './package.json' );
const { sync: glob } = require( 'fast-glob' );
const postcssGlobalData = require( '@csstools/postcss-global-data' );

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
	postcssGlobalData( {
		files: glob(
			`${ pkg.config.coreThemeDir }/assets/pcss/global/_*.pcss`,
			{ absolute: true }
		),
	} ),
	[
		'postcss-preset-env',
		{
			stage: 0,
			autoprefixer: { grid: true },
			features: {
				clamp: false,
				'custom-properties': false,
				'focus-visible-pseudo-class': false,
				'focus-within-pseudo-class': false,
				'logical-properties-and-values': false,
				'has-pseudo-class': true,
			},
		},
	],
];

module.exports = {
	plugins: isProduction
		? [ ...plugins, require( 'cssnano' )( cssNanoConfig ) ]
		: plugins,
};
