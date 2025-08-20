import { useBlockProps, InnerBlocks } from '@wordpress/block-editor';

export default function save() {
	const { className, ...blockProps } = useBlockProps.save();

	return (
		<div { ...blockProps } className={ `${ className } swiper-slide` }>
			<InnerBlocks.Content />
		</div>
	);
}
