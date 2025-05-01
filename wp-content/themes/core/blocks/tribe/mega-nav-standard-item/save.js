import { InnerBlocks, useBlockProps } from '@wordpress/block-editor';

export default function save( { attributes } ) {
	const { menuToggleLabel, hasSubMenu } = attributes;

	const blockProps = useBlockProps.save();

	return (
		<li { ...blockProps }>
			{ hasSubMenu && menuToggleLabel && (
				<button
					type="button"
					className="tribe-standard-menu-item__toggle"
					data-js="standard-menu-item-toggle"
				>
					{ menuToggleLabel }
				</button>
			) }
			{ hasSubMenu ? (
				<div className="site-header__standard-menu-item-wrapper">
					<InnerBlocks.Content />
				</div>
			) : (
				<InnerBlocks.Content />
			) }
		</li>
	);
}
