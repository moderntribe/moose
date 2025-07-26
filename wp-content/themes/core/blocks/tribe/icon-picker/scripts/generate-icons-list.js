const fs = require( 'fs' );
const path = require( 'path' );

// Define the directory containing icon components and the output file path
const COMPONENTS_DIR = path.resolve( __dirname, '../icons/components' );
const OUTPUT_FILE = path.resolve( __dirname, '../icons/icons-list.js' );

/**
 * Converts an icon key like 'icon-video-player' to a display name like 'Video Player'
 * @param {string} key - The kebab-case icon key
 * @return {string} Human-readable name
 */
function toName( key ) {
	return key
		.replace( /^icon-/, '' )
		.split( '-' )
		.map( ( word ) => word.charAt( 0 ).toUpperCase() + word.slice( 1 ) )
		.join( ' ' );
}

/**
 * Parses the component filename to generate:
 * - `key`: the kebab-case icon key (e.g. 'icon-video-player')
 * - `name`: the readable icon name (e.g. 'Video Player')
 * - `importName`: the PascalCase component name (e.g. 'IconVideoPlayer')
 * @param {string} filename - The icon component filename (e.g. 'IconVideoPlayer.js')
 * @return {{ importName: string, key: string, name: string }} An object containing the PascalCase component name,
 * kebab-case icon key, and human-readable icon name.
 */
function parseIconFile( filename ) {
	const baseName = path.basename( filename, '.js' );
	const kebab = baseName
		.replace( /^Icon/, '' )
		.replace( /([a-z0-9])([A-Z])/g, '$1-$2' )
		.toLowerCase();
	const key = `icon-${ kebab }`;
	const name = toName( key );
	return { importName: baseName, key, name };
}

// Read all component filenames in the directory and filter by .js extension
const files = fs
	.readdirSync( COMPONENTS_DIR )
	.filter( ( f ) => f.endsWith( '.js' ) );

// Collect import statements and icon metadata objects
const imports = [];
const items = [];

for ( const file of files ) {
	const { importName, key, name } = parseIconFile( file );
	imports.push(
		`import ${ importName } from './components/${ importName }';`
	);
	items.push(
		`  {\n    key: '${ key }',\n    name: '${ name }',\n    component: ${ importName },\n  },`
	);
}

// Generate the final output file content as a string
const output = `// Auto-generated file. Do not edit manually.

${ imports.join( '\n' ) }

export const ICONS_LIST = [
${ items.join( '\n' ) }
];
`;

// Write the generated content to the output file
fs.writeFileSync( OUTPUT_FILE, output );
console.log( '✅ icons-list.js updated in array format!' );
