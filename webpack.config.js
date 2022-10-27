/**
 * Custom Webpack Config
 *
 * Extends the WordPress WP-Scripts configuration for our local use.
 *
 * WP-Scripts webpack config documentation:
 * https://developer.wordpress.org/block-editor/reference-guides/packages/packages-scripts/#default-webpack-config
 */
const { resolve } = require( 'path' );
const pkg = require( './package.json' );
const BrowserSyncPlugin = require( 'browser-sync-webpack-plugin' );
const browserSyncOpts = require( './browsersync.config' );
const defaultConfig = require( '@wordpress/scripts/config/webpack.config' );
const { getWebpackEntryPoints } = require( '@wordpress/scripts/utils/config' );

/**
 * Update the css module.rules test to add .pcss as an option.
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
		...getWebpackEntryPoints(),
		admin: resolve(
			process.cwd(),
			pkg.directories.coreTheme,
			'assets',
			'admin.js'
		), // Add theme admin scripts & styles entry
		theme: resolve(
			process.cwd(),
			pkg.directories.coreTheme,
			'assets',
			'theme.js'
		), // Add theme public scripts & styles entry
	},
	output: {
		...defaultConfig.output,
		path: resolve( process.cwd(), pkg.directories.coreTheme, 'dist' ), // Change the output path to `dist` instead of `build`
	},
	module: {
		rules: moduleRules, // Modified module.rules supporting .pcss extension in addition to .css files.
	},
	plugins: [
		...defaultConfig.plugins,
		new BrowserSyncPlugin( browserSyncOpts ), // Add browsersync for dev reloads
	],
};

// Add .pcss extension to the splitChunks cache groups for block style chunks.
config.optimization.splitChunks.cacheGroups.style.test =
	/[\\/]style(\.module)?\.(sc|sa|c|pc)ss$/;

module.exports = config;
