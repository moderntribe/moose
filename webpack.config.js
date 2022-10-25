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
const { hasPostCSSConfig, hasCssnanoConfig } = require("@wordpress/scripts/utils");
const MiniCSSExtractPlugin = require( 'mini-css-extract-plugin' );
const postcssPlugins = require( '@wordpress/postcss-plugins-preset' );
const isProduction = process.env.NODE_ENV === 'production';

const defaultCssLoaders = [
	{
		loader: MiniCSSExtractPlugin.loader,
	},
	{
		loader: require.resolve( 'css-loader' ),
		options: {
			sourceMap: ! isProduction,
			modules: {
				auto: true,
			},
		},
	},
	{
		loader: require.resolve( 'postcss-loader' ),
		options: {
			// Provide a fallback configuration if there's not
			// one explicitly available in the project.
			...( ! hasPostCSSConfig() && {
				postcssOptions: {
					ident: 'postcss',
					sourceMap: ! isProduction,
					plugins: isProduction
						? [
							...postcssPlugins,
							require( 'cssnano' )( {
								// Provide a fallback configuration if there's not
								// one explicitly available in the project.
								...( ! hasCssnanoConfig() && {
									preset: [
										'default',
										{
											discardComments: {
												removeAll: false,
											},
										},
									],
								} ),
							} ),
						]
						: postcssPlugins,
				},
			} ),
		},
	},
];

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
		rules: [
			...defaultConfig.module.rules,
			{
				test: /\.pcss$/,
				use: defaultCssLoaders,
			},
		],
	},
	plugins: [
		...defaultConfig.plugins,
		new BrowserSyncPlugin( browserSyncOpts ), // Add browsersync for dev reloads
	],
};

config.optimization.splitChunks.cacheGroups.style.test =
	/[\\/]style(\.module)?\.(sc|sa|c|pc)ss$/;

module.exports = config;
