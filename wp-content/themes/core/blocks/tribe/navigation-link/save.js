import { useBlockProps } from '@wordpress/block-editor';

export default function save( { attributes } ) {
	const { text, url, target } = attributes;

	const blockProps = useBlockProps.save( {
		href: url,
		target: target && '_blank',
	} );

	return <>{ url && text && <a { ...blockProps }>{ text }</a> }</>;
}
