/**
 * Custom Webpack Config
 *
 * Extends the WordPress WP-Scripts configuration for our local use.
 *
 * WP-Scripts webpack config documentation:
 * https://developer.wordpress.org/block-editor/reference-guides/packages/packages-scripts/#default-webpack-config
 */

const { resolve, extname } = require( 'path' );
const { sync: glob } = require( 'fast-glob' );
const pkg = require( './package.json' );
const BrowserSyncPlugin = require( 'browser-sync-webpack-plugin' );
const browserSyncOpts = require( './browsersync.config' );
const defaultConfig = require( '@wordpress/scripts/config/webpack.config' );
const RemoveEmptyScriptsPlugin = require( 'webpack-remove-empty-scripts' );

/**
 * General theme scripts & styles entry points
 *
 * The entry point names are prefixed with `assets` to direct their output into
 * an assets subdirectory in the root dist folder.
 */
const assetEntryPoints = () => {
	return {
		'assets/admin': resolve(
			pkg.config.coreThemeDir,
			'assets',
			'admin.js'
		),
		'assets/theme': resolve(
			pkg.config.coreThemeDir,
			'assets',
			'theme.js'
		),
		'assets/print': resolve(
			pkg.config.coreThemeDir,
			'assets',
			'print.pcss'
		),
	};
};

/**
 * Auto-find and load any block-based entry points.
 *
 * This is a simplified version of WP-Scripts' blocks.json entry point loader.
 * We want to support block.json entry points within subdirectories of the theme
 * blocks directory, and we can safely ignore other legacy entry point formats.
 *
 * @return {{}|undefined}	An object of block entry points or undefined of there are none.
 */
const blockEntryPoints = () => {
	/**
	 * Use `index.js` instead of `block.json` for glob b/c core blocks won't
	 * contain a `block.json` file (they are already registered), but they
	 * still may contain scripts or styles which should be processed.
	 */
	const coreBlockFiles = glob(
		`${ pkg.config.coreThemeBlocksDir }/**/index.js`,
		{ absolute: true }
	);

	if ( ! coreBlockFiles.length ) {
		return;
	}

	const entryPoints = {};

	coreBlockFiles.forEach( ( entryFilePath ) => {
		const entryName = entryFilePath
			.replace( extname( entryFilePath ), '' )
			.replace( `${ resolve( pkg.config.coreThemeDir ) }/`, '' );
		entryPoints[ entryName ] = entryFilePath;
	} );

	return entryPoints;
};

/**
 * The configuration for copying block.json files from the src to dist folder
 * doesn't work with our namespaced nested blocks structure. Thus, we have to
 * find the plugin's config in the greater config object and explicitly set
 * the destination location (`to:`) for the coped file(s).
 */
const copyPluginIndex = defaultConfig.plugins.findIndex(
	( plugin ) => plugin.patterns
);

if ( copyPluginIndex > -1 ) {
	const blockJsonPatternIndex = defaultConfig.plugins[
		copyPluginIndex
	].patterns.findIndex( ( pattern ) => pattern.from === '**/block.json' );

	if ( blockJsonPatternIndex > -1 ) {
		defaultConfig.plugins[ copyPluginIndex ].patterns[
			blockJsonPatternIndex
		] = {
			...defaultConfig.plugins[ copyPluginIndex ].patterns[
				blockJsonPatternIndex
			],
			to: `blocks/`,
		};
	}
}

module.exports = {
	...defaultConfig,
	entry: {
		...assetEntryPoints(),
		...blockEntryPoints(),
	},
	output: {
		...defaultConfig.output,
		path: resolve( pkg.config.coreThemeDir, 'dist' ), // Change the output path to `dist` instead of `build`
	},
	plugins: [
		...defaultConfig.plugins,

		/**
		 * Remove empty auto-generated index.js files
		 *
		 * Webpack auto-generates an empty index.js file for every entry point.
		 * When we create a styles-only entry point such as print.pcss
		 * this plugin deletes that empty index.js file after it is built.
		 */
		new RemoveEmptyScriptsPlugin( {
			stage: RemoveEmptyScriptsPlugin.STAGE_AFTER_PROCESS_PLUGINS,
		} ),

		/**
		 * Add browsersync so any file changes auto-reload the browser.
		 *
		 * WP-Scripts does make use of Webpack hot module reloading,
		 * but it doesn't support non-JS entry points at this time.
		 * For our purposes, broswersync is more helpful.
		 */
		new BrowserSyncPlugin( browserSyncOpts ), // Add browser-sync for dev reloads
	],
};
