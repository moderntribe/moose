import { InnerBlocks, useBlockProps } from '@wordpress/block-editor';
import { useInstanceId } from '@wordpress/compose';
import { useEffect } from '@wordpress/element';

import './editor.pcss';

export default function Edit( { context, setAttributes } ) {
	const activeTab = context[ 'tribe/tabs/currentActiveTabInstanceId' ];
	const instanceId = useInstanceId( Edit, 'tab-content' );
	const blockProps = useBlockProps( {
		className: activeTab === instanceId ? 'active-tab' : '',
	} );

	useEffect( () => {
		setAttributes( {
			blockId: instanceId,
		} );
	}, [ instanceId, setAttributes ] );

	const TAB_TEMPLATE = [ [ 'core/paragraph' ] ];

	return (
		<div { ...blockProps }>
			<InnerBlocks template={ TAB_TEMPLATE } />
		</div>
	);
}
