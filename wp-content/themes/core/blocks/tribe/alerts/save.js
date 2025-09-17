import { useBlockProps } from '@wordpress/block-editor';

export default function save( { attributes } ) {
	const blockProps = useBlockProps.save();

	const { exampleTextControl } = attributes;

	return (
		<section { ...blockProps }>
			<p>{ 'Alert – hello from the saved content!' }</p>
			{ exampleTextControl ? <p>{ exampleTextControl }</p> : '' }
		</section>
	);
}
