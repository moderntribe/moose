import { InnerBlocks, useBlockProps } from '@wordpress/block-editor';

const TEMPLATE = [ [ 'core/paragraph' ] ];

export default function Edit() {
	const { className, ...blockProps } = useBlockProps();

	return (
		<div
			{ ...blockProps }
			className={ `${ className } swiper-slide swiper-no-swiping` }
		>
			<InnerBlocks template={ TEMPLATE } />
		</div>
	);
}
