import { useBlockProps } from '@wordpress/block-editor';
import { __ } from '@wordpress/i18n';
export default function save( { attributes } ) {
	const { showStartingYear, startingYear, copyrightText } = attributes;
	const currentYear = new Date().getFullYear().toString();
	const title = copyrightText || wp.data.select('core').getSite().title;

	let displayDate;

	if ( showStartingYear && startingYear ) {
		displayDate = startingYear + '–' + currentYear;
	} else {
		displayDate = currentYear;
	}

	return (
		<p { ...useBlockProps.save() }>{ __( 'Copyright', 'tribe' ) } © { displayDate } { title }</p>
	);
}
