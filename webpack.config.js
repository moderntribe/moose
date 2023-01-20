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
const RemoveEmptyScriptsPlugin = require( 'webpack-remove-empty-scripts' );

/**
 * Update the css module.rules test to add .pcss as an option.
 */
const moduleRules = defaultConfig.module.rules.map( ( rule ) => {
	const cssExp = /\.css$/; // Default rule.test as defined in the wp-scripts webpack config.
	return rule.test.toString() === cssExp.toString()
		? { ...rule, test: /\.(pc|c)ss$/ }
		: rule;
} );

const assetFileNames = ( plugin ) => {
	console.info( plugin );
	plugin.options.filename = 'assets/[name].css';
	return plugin;
};

const config = {
	...defaultConfig,
	entry: {
		...getWebpackEntryPoints(),
		admin: resolve( pkg.directories.coreTheme, 'assets', 'admin.js' ), // Add theme admin scripts & styles entry
		theme: resolve( pkg.directories.coreTheme, 'assets', 'theme.js' ), // Add theme public scripts & styles entry
		print: resolve( pkg.directories.coreTheme, 'assets', 'print.pcss' ),
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
		/*...defaultConfig.plugins.map( ( plugin ) => {
			if ( plugin.constructor.name === 'MiniCssExtractPlugin' ) {
				return assetFileNames( plugin );
			}
			return plugin;
		} ),*/
		new RemoveEmptyScriptsPlugin( {
			stage: RemoveEmptyScriptsPlugin.STAGE_AFTER_PROCESS_PLUGINS,
		} ),
		new BrowserSyncPlugin( browserSyncOpts ), // Add browsersync for dev reloads
	],
};

// Add .pcss extension to the splitChunks cache groups for block style chunks.
config.optimization.splitChunks.cacheGroups.style.test =
	/[\\/]style(\.module)?\.(sc|sa|c|pc)ss$/;

module.exports = config;
