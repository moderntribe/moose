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
const BrowserSyncPlugin = require( 'browser-sync-v3-webpack-plugin' );
const browserSyncOpts = require( './browsersync.config' );
const defaultConfig = require( '@wordpress/scripts/config/webpack.config' );
const RemoveEmptyScriptsPlugin = require( 'webpack-remove-empty-scripts' );

/**
 * Determine if we should support WP Scripts multi-config setup (modules)
 */
let baseConfig = defaultConfig;
const defaultConfigIsArray = Array.isArray( defaultConfig );

if ( defaultConfigIsArray ) {
	baseConfig = { ...defaultConfig[ 0 ] };
}

/**
 * Create alias config for easier imports within the theme
 */
const themeAliases = {
	utils: resolve( './wp-content/themes/core/assets/js/utils' ),
	common: resolve( './wp-content/themes/core/assets/js/common' ),
	config: resolve( './wp-content/themes/core/assets/js/config' ),
	blocks: resolve( './wp-content/themes/core/blocks' ),
	components: resolve( './wp-content/themes/core/assets/js/components' ),
};

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
		'assets/editor': resolve(
			pkg.config.coreThemeDir,
			'assets',
			'editor.js'
		),
		'assets/theme': resolve(
			pkg.config.coreThemeDir,
			'assets',
			'theme.js'
		),
		'assets/login': resolve(
			pkg.config.coreThemeDir,
			'assets',
			'login.pcss'
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
	 * Create files array using index.js, editor.js and view.js files
	 *
	 * We use `index.js` instead of `block.json` for glob b/c core blocks
	 * won't contain a `block.json` file (they are already registered),
	 * but they still may contain scripts or styles which should be processed.
	 */
	const files = [
		...glob( `${ pkg.config.coreThemeBlocksDir }/**/index.js`, {
			absolute: true,
		} ),
		...glob( `${ pkg.config.coreThemeBlocksDir }/**/editor.js`, {
			absolute: true,
		} ),
		...glob( `${ pkg.config.coreThemeBlocksDir }/**/view.js`, {
			absolute: true,
		} ),
	];

	if ( ! files.length ) {
		return;
	}

	const entryPoints = {};

	files.forEach( ( entryFilePath ) => {
		const entryName = entryFilePath
			.replace( extname( entryFilePath ), '' )
			.replace( `${ resolve( pkg.config.coreThemeDir ) }/`, '' );
		entryPoints[ entryName ] = entryFilePath;
	} );

	return entryPoints;
};

baseConfig = {
	...baseConfig,
	resolve: {
		...baseConfig.resolve,
		alias: {
			...baseConfig.resolve.alias,
			...themeAliases,
		},
	},
	entry: {
		...assetEntryPoints(),
		...blockEntryPoints(),
	},
	output: {
		...baseConfig.output,
		path: resolve( pkg.config.coreThemeDir, 'dist' ), // Change the output path to `dist` instead of `build`
	},
	plugins: [
		...baseConfig.plugins,

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
		new BrowserSyncPlugin( browserSyncOpts, { reload: false } ), // Add browser-sync for dev reloads
	],
};

/**
 * The configuration for copying block.json files from the src to dist folder
 * doesn't work with our namespaced nested blocks structure. Thus, we have to
 * find the plugin's config in the greater config object and explicitly set
 * the destination location (`to:`) for the copied file(s).
 */
const copyPluginIndex = baseConfig.plugins.findIndex(
	( plugin ) => plugin.patterns
);

if ( copyPluginIndex > -1 ) {
	baseConfig.plugins[ copyPluginIndex ].patterns.forEach(
		( pattern, patternIndex ) => {
			if ( pattern.from === '**/block.json' ) {
				baseConfig.plugins[ copyPluginIndex ].patterns[ patternIndex ] =
					{
						...baseConfig.plugins[ copyPluginIndex ].patterns[
							patternIndex
						],
						context: resolve( pkg.config.coreThemeDir, 'blocks/' ),
						to: resolve( pkg.config.coreThemeDir, 'dist/blocks/' ),
					};
			} else if ( pattern.from === '**/*.php' ) {
				baseConfig.plugins[ copyPluginIndex ].patterns[ patternIndex ] =
					{
						from: '**/*.php',
						noErrorOnMissing: true,
						context: resolve(
							pkg.config.coreThemeDir,
							'blocks/tribe'
						),
						to: resolve(
							pkg.config.coreThemeDir,
							'dist/blocks/tribe'
						),
					};
			}
		}
	);
}

if ( defaultConfigIsArray ) {
	const moduleConfig = {
		...defaultConfig[ 1 ],
		resolve: {
			...defaultConfig[ 1 ].resolve,
			alias: {
				...defaultConfig[ 1 ].resolve.alias,
				...themeAliases,
			},
		},
		output: {
			...defaultConfig[ 1 ].output,
			path: resolve( pkg.config.coreThemeDir, 'dist' ), // Change the output path to `dist` instead of `build`
		},
	};

	module.exports = [ baseConfig, moduleConfig ];
} else {
	module.exports = baseConfig;
}
