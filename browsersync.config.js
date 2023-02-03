/**
 * Browsersync configuration
 *
 * Configures browsersync options for use with local dev URLs and trusted SSL certs using values from a
 * `local-config.json` file which may be set up on a per-machine basis.
 *
 * BrowserSync Docs:
 * https://browsersync.io/docs/options/
 */

/**
 * Check if a module exists before requiring it.
 *
 * @param {string} name
 * @return {string|void}	Returns the original file name if found.
 */
function moduleExists( name ) {
	try {
		return require.resolve( name );
	} catch ( e ) {
		console.warn(
			'Warning: local-config.json is missing. Did you create one?\n'
		);
	}
}

/**
 * Populate the local config object with fallbacks.
 */
const localConfig = moduleExists( './local-config.json' )
	? require( './local-config.json' )
	: {
			certsPath: '',
			host: 'localhost',
			protocol: 'http',
	  };

/**
 * Set up the browser sync to proxy the webpack dev server using our custom local-config.
 */
module.exports = {
	debugInfo: true,
	logConnections: true,
	notify: true,
	open: 'external',
	host: localConfig.host,
	proxy: `${ localConfig.protocol }://${ localConfig.host }`,
	...( localConfig.certsPath.length && {
		https: {
			key: `${ localConfig.certsPath }/${ localConfig.host }.key`,
			cert: `${ localConfig.certsPath }/${ localConfig.host }.crt`,
		},
	} ),
};
