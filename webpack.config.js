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

module.exports = {
	...defaultConfig,
	entry: {
		...getWebpackEntryPoints(),
		admin: resolve(
			process.cwd(),
			pkg.directories.coreTheme,
			'assets',
			'admin.js'
		), // Add theme admin scripts & styles entry
		public: resolve(
			process.cwd(),
			pkg.directories.coreTheme,
			'assets',
			'public.js'
		), // Add theme public scripts & styles entry
	},
	output: {
		...defaultConfig.output,
		path: resolve( process.cwd(), pkg.directories.coreTheme, 'dist' ), // Change the output path to `dist` instead of `build`
	},
	plugins: [
		...defaultConfig.plugins,
		new BrowserSyncPlugin( browserSyncOpts ), // Add browsersync for dev reloads
	],
};
