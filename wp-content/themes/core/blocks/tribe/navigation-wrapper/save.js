import { useBlockProps, InnerBlocks } from '@wordpress/block-editor';

export default function save( { attributes } ) {
	const { ariaLabel } = attributes;

	const blockProps = useBlockProps.save( {
		'aria-label': ariaLabel,
	} );

	return (
		<nav { ...blockProps }>
			<InnerBlocks.Content />
		</nav>
	);
}
