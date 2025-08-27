const fs = require( 'fs' );
const path = require( 'path' );

const COMPONENTS_DIR = path.resolve( __dirname, '../icons/components' );
const IMPORT_LINE = `import * as React from "react";`;

fs.readdirSync( COMPONENTS_DIR ).forEach( ( file ) => {
	if ( ! file.endsWith( '.js' ) ) {
		return;
	}

	const filePath = path.join( COMPONENTS_DIR, file );
	const content = fs.readFileSync( filePath, 'utf-8' );

	// Remove the line exactly matching the import
	const updated = content
		.split( '\n' )
		.filter( ( line ) => line.trim() !== IMPORT_LINE )
		.join( '\n' )
		.trimStart(); // trim start to remove leading empty lines if any

	fs.writeFileSync( filePath, updated, 'utf-8' );
} );

console.log( '✅ Removed React imports from generated components.' );
