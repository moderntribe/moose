// Utility to format icon names
export function formatIconName( name ) {
	if ( ! name ) {
		return '';
	}
	return name
		.split( '-' )
		.map( ( word ) => word.charAt( 0 ).toUpperCase() + word.slice( 1 ) )
		.join( ' ' );
}
