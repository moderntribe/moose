import { InnerBlocks, useBlockProps } from '@wordpress/block-editor';

export default function save( { attributes } ) {
	const blockProps = useBlockProps.save();

	const { menuToggleLabel } = attributes;

	return (
		<li { ...blockProps }>
			{ menuToggleLabel ? <button type="button" className="tribe-mega-menu-item__toggle" data-js="menu-menu-toggle">{ menuToggleLabel }</button> : '' }
			<InnerBlocks.Content />
		</li>
	);
}
