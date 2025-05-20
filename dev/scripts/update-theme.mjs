/**
 * update-theme.mjs – v2
 *
 * Usage:
 *   node scripts/update-theme.mjs --css path/to/figma-vars.css --theme path/to/theme.json [--out path/to/out.json]
 *
 * The script ingests a CSS‑variables export from Figma and injects the values into a WordPress block‑theme
 * theme.json. Supported token categories out‑of‑the‑box:
 *   ◆ Colors      – brand, neutral, form
 *   ◆ Typography  – font-family, font-size
 *   ◆ (Everything else) is recorded under styles["custom"] to preserve the data for future mapping.
 *
 * Extend the `handlers` map at the bottom to add spacing, radius, shadows, etc.
 *
 * Dependencies (dev):
 *   npm i -D postcss postcss-safe-parser minimist fs-extra
 */

import fs from 'fs';
import path from 'path';
import postcss from 'postcss';
import safeParser from 'postcss-safe-parser';
import minimist from 'minimist';

// ───────────────────────────────────────────────────────────────────────────────
// CLI args
// ───────────────────────────────────────────────────────────────────────────────
const argv = minimist( process.argv.slice( 2 ), {
	string: [ 'css', 'theme', 'out' ],
	default: { css: 'figma-tokens.css', theme: 'theme.json', out: null },
} );

const cssPath = path.resolve( process.cwd(), argv.css );
const themePath = path.resolve( process.cwd(), argv.theme );
const outPath = argv.out ? path.resolve( process.cwd(), argv.out ) : themePath;

if ( ! fs.existsSync( cssPath ) ) {
	console.error( `\u274c  CSS token file not found: ${ cssPath }` );
	process.exit( 1 );
}
if ( ! fs.existsSync( themePath ) ) {
	console.error( `\u274c  theme.json not found: ${ themePath }` );
	process.exit( 1 );
}

// ───────────────────────────────────────────────────────────────────────────────
// Parse CSS → token map { token-name: value }
// ───────────────────────────────────────────────────────────────────────────────
const cssContent = fs.readFileSync( cssPath, 'utf8' );
const root = postcss().process( cssContent, { parser: safeParser } ).root;

const tokens = {};
root.walkDecls( ( decl ) => {
	if ( decl.prop.startsWith( '--' ) ) {
		tokens[ decl.prop.slice( 2 ) ] = decl.value.trim();
	}
} );

// ───────────────────────────────────────────────────────────────────────────────
// Load / prime theme.json structure
// ───────────────────────────────────────────────────────────────────────────────
const themeJson = JSON.parse( fs.readFileSync( themePath, 'utf8' ) );

function ensure( obj, pathArr, fallback = {} ) {
	let ref = obj;
	for ( const key of pathArr ) {
		if ( ! ( key in ref ) ) {
			ref[ key ] = {};
		}
		ref = ref[ key ];
	}
	return ref;
}

// Prime common branches
ensure( themeJson, [ 'settings' ] );

// ───────────────────────────────────────────────────────────────────────────────
// Mapping utilities
// ───────────────────────────────────────────────────────────────────────────────
const slugify = ( str ) =>
	str
		.replace( /[^a-zA-Z0-9]+/g, '-' )
		.replace( /^-|-$/g, '' )
		.toLowerCase();

function upsert( arr, slug, entry ) {
	const i = arr.findIndex( ( e ) => e.slug === slug );
	if ( i > -1 ) {
		arr[ i ] = { ...arr[ i ], ...entry };
	} else {
		arr.push( entry );
	}
}

// ───────────────────────────────────────────────────────────────────────────────
// Handlers
// Each receives (segments: string[], value: string)
// segments = tokenName.split('--')
// ───────────────────────────────────────────────────────────────────────────────
const handlers = {
	color( segments, value ) {
		const slug = slugify( segments.join( '-' ) );
		const palette = ensure(
			themeJson,
			[ 'settings', 'color', 'palette' ],
			[]
		);
		upsert( palette, slug, {
			slug,
			color: value,
			name: slug.replace( /-/g, ' ' ),
		} );
	},

	'font-family'( segments, value ) {
		const slug = slugify( segments.slice( 1 ).join( '-' ) ) || 'font';
		const families = ensure(
			themeJson,
			[ 'settings', 'typography', 'fontFamilies' ],
			[]
		);
		upsert( families, slug, {
			slug,
			fontFamily: value.replace( /['"]+/g, '' ),
			name: slug.replace( /-/g, ' ' ),
		} );
	},

	'font-size'( segments, value ) {
		const slug = slugify( segments.slice( 1 ).join( '-' ) );
		const sizes = ensure(
			themeJson,
			[ 'settings', 'typography', 'fontSizes' ],
			[]
		);
		upsert( sizes, slug, {
			slug,
			size: value,
			name: slug.replace( /-/g, ' ' ),
		} );
	},
};

// Aliases – tokens whose first segment should be treated as a color
const COLOR_ALIASES = new Set( [ 'brand', 'neutral', 'form', 'base' ] );

Object.entries( tokens ).forEach( ( [ token, value ] ) => {
	const segments = token.split( '--' );
	const head = segments[ 0 ];

	if ( COLOR_ALIASES.has( head ) ) {
		handlers.color( segments, value );
	} else if ( handlers[ head ] ) {
		handlers[ head ]( segments, value );
	} else {
		// Unknown token – stash it so nothing is lost. Adds to styles.custom css var map
		const custom = ensure( themeJson, [ 'styles', 'custom' ] );
		custom[ `--${ token }` ] = value;
	}
} );

// ───────────────────────────────────────────────────────────────────────────────
// Write file
// ───────────────────────────────────────────────────────────────────────────────
fs.writeFileSync( outPath, JSON.stringify( themeJson, null, 2 ) );
console.log(
	`\u2705  theme.json updated successfully → ${ path.relative(
		process.cwd(),
		outPath
	) }`
);
