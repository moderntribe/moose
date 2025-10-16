import { useBlockProps, useInnerBlocksProps } from '@wordpress/block-editor';
import { useInstanceId } from '@wordpress/compose';
import { useEffect } from '@wordpress/element';

import './editor.pcss';

export default function Edit( { context, setAttributes } ) {
	const TAB_TEMPLATE = [ [ 'core/paragraph' ] ];
	const activeTab =
		context[ 'tribe/vertical-tabs/currentActiveTabInstanceId' ];
	const instanceId = useInstanceId( Edit, 'vt-tab-content' );
	const blockProps = useBlockProps( {
		className: activeTab === instanceId ? 'active-tab' : '',
	} );
	const innerBlocksProps = useInnerBlocksProps( blockProps, {
		template: TAB_TEMPLATE,
	} );

	useEffect( () => {
		setAttributes( {
			blockId: instanceId,
		} );
	}, [ instanceId, setAttributes ] );

	return (
		<>
			<div { ...innerBlocksProps } />
		</>
	);
}
