/**
 * WordPress dependencies
 */
 import { Fragment } from '@wordpress/element';
 const { Spinner } = wp.components;
 export default function SaveOverlay( {
	show,
} ) {
	 return (
		<Fragment>
			{ show && (
				<div className="kadence-settings-saving-overlay">
					<Spinner />
				</div>
			) }
		</Fragment>
	 );
}