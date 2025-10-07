import { useBlockProps, InnerBlocks } from '@wordpress/block-editor';

export default function Edit() {
	const blockProps = useBlockProps();

	return (
		<nav { ...blockProps }>
			<InnerBlocks />
		</nav>
	);
}
