import { InnerBlocks, useBlockProps } from '@wordpress/block-editor';

export default function save( { attributes } ) {
	const { blockId } = attributes;
	const blockProps = useBlockProps.save( {
		id: blockId,
		role: 'tabpanel',
		tabindex: '0',
		hidden: true,
		'aria-labelledby': 'button-' + blockId,
	} );

	return (
		<div { ...blockProps }>
			<InnerBlocks.Content />
		</div>
	);
}
