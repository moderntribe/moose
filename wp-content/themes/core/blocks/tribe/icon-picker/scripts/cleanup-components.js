const fs = require( 'fs' );
const path = require( 'path' );

const COMPONENTS_DIR = path.resolve( __dirname, '../icons/components' );

const jsFiles = fs
	.readdirSync( COMPONENTS_DIR )
	.filter( ( file ) => file.endsWith( '.js' ) );

if ( jsFiles.length === 0 ) {
	console.warn( '⚠️ No .js files found in icons/components to delete.' );
	process.exit( 0 );
}

jsFiles.forEach( ( file ) => {
	fs.unlinkSync( path.join( COMPONENTS_DIR, file ) );
} );

console.log(
	`🧹 Deleted ${ jsFiles.length } component file(s) from icons/components.`
);
