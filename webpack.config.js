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
/*const {
	getWebpackEntryPoints: blockJsonEntryPoints,
} = require( '@wordpress/scripts/utils/config' );*/
const RemoveEmptyScriptsPlugin = require( 'webpack-remove-empty-scripts' );

/**
 * General theme scripts & styles entry points
 *
 * The entry point names are prefixed with `../assets` to direct their output into
 * an assets subdirectory in the root dist folder.
 */
const assetEntryPoints = () => {
	return {
		'assets/admin': resolve(
			pkg.directories.coreTheme,
			'assets',
			'admin.js'
		),
		'assets/theme': resolve(
			pkg.directories.coreTheme,
			'assets',
			'theme.js'
		),
		'assets/print': resolve(
			pkg.directories.coreTheme,
			'assets',
			'print.pcss'
		),
	};
};

const blockEntryPoints = () => {
	const coreBlockFiles = glob(
		`${ pkg.directories.coreTheme }blocks/**/index.js`,
		{
			absolute: true,
		}
	);

	if ( ! coreBlockFiles.length ) {
		return;
	}

	const entryPoints = {};

	coreBlockFiles.forEach( ( entryFilePath ) => {
		const entryName = entryFilePath
			.replace( extname( entryFilePath ), '' )
			.replace( `${ resolve( pkg.directories.coreTheme ) }/`, '' );
		entryPoints[ entryName ] = entryFilePath;
	} );

	return entryPoints;
};

/**
 * Update the css module rules test to add .pcss as an option.
 */
const moduleRules = defaultConfig.module.rules.map( ( rule ) => {
	const cssExp = /\.css$/; // Default rule.test as defined in the wp-scripts webpack config.
	return rule.test.toString() === cssExp.toString()
		? { ...rule, test: /\.(pc|c)ss$/ }
		: rule;
} );

const config = {
	...defaultConfig,
	entry: {
		...assetEntryPoints(),
		...blockEntryPoints(),
	},
	output: {
		...defaultConfig.output,
		path: resolve( pkg.directories.coreTheme, 'dist' ), // Change the output path to `dist` instead of `build`
	},
	module: {
		rules: moduleRules, // Modified module.rules supporting .pcss extension in addition to .css files.
	},
	plugins: [
		...defaultConfig.plugins,
		new RemoveEmptyScriptsPlugin( {
			stage: RemoveEmptyScriptsPlugin.STAGE_AFTER_PROCESS_PLUGINS,
		} ),
		new BrowserSyncPlugin( browserSyncOpts ), // Add browser-sync for dev reloads
	],
};

// Add .pcss extension to the splitChunks cache groups for block style chunks.
config.optimization.splitChunks.cacheGroups.style.test =
	/[\\/]style(\.module)?\.(sc|sa|c|pc)ss$/;

/**
 * The configuration for copying block.json files from the source to dist folders
 * is too greedy and ends up duplicating files already inside the dist directory.
 *
 * Thus, we have to find the plugin's config in the greater config object
 * and explicitly ignore that directory.
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
		].globOptions.ignore = '**/dist/**';
	}
}

module.exports = config;
