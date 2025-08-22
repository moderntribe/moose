/**
 * PostCSS Config
 *
 * Overrides WP-Scripts default config for postcss processing.
 *
 * Customizations:
 * - postcss-import: Add support for concatenating pcss partials via `@import` statements.
 *  	- Reference: https://www.npmjs.com/package/postcss-import
 * - postcss-global-data: Add support for the referenced set of css partials to be used globally,
 *  	without individually importing the partials in every file where they are used.
 *  	This plugin only makes css available for the pcss processor to "use" it does not inject
 *  	any css into the compiled css files. As such, it's only useful for partials that contain
 *  	data which will be "replaced" by the pcss parser such as custom media queries and custom selectors.
 *  	- Reference: https://www.npmjs.com/package/@csstools/postcss-global-data
 * - postcss-mixins: Add support for mixins.
 *  	- Will glob mixins from any files named `_mixins.pcss` within the theme pcss assets directory.
 *  	- Reference: https://github.com/postcss/postcss-mixins
 * - postcss-inline-svg: Add support for inline SVG icons.
 * - postcss-preset-env: Sets config to process all features (stage 0 and above)
 *  	- Adds autoprefixer support for css grid
 *  	- Removes any transformations on (don't modify) css custom properties, :focus-visible, and :focus-within
 *  	- Reference: https://www.npmjs.com/package/postcss-preset-env
 * - cssnano: minimizes css files ONLY when a production build is run
 */

const isProduction = process.env.NODE_ENV === 'production';
const pkg = require( './package.json' );

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
		'@csstools/postcss-global-data',
		{
			files: [
				`${ pkg.config.coreThemeDir }/assets/pcss/custom-selectors/_variables.pcss`,
				`${ pkg.config.coreThemeDir }/assets/pcss/icons/_variables.pcss`,
				`${ pkg.config.coreThemeDir }/assets/pcss/media-queries/_variables.pcss`,
			],
		},
	],
	[
		'postcss-mixins',
		{
			mixinsFiles: `${ pkg.config.coreThemeDir }/assets/pcss/**/_mixins.pcss`,
		},
	],
	[
		'postcss-inline-svg',
		{
			paths: [
				`${ pkg.config.coreThemeDir }/assets/media/icons`,
				`${ pkg.config.coreThemeDir }/blocks/tribe/rating-stars/icons`,
			],
		},
	],
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
			},
		},
	],
];

module.exports = {
	plugins: isProduction
		? [ ...plugins, require( 'cssnano' )( cssNanoConfig ) ]
		: plugins,
};
