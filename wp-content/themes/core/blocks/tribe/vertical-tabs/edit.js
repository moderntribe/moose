import { createBlock } from '@wordpress/blocks';
import {
	RichText,
	useBlockProps,
	useInnerBlocksProps,
} from '@wordpress/block-editor';
import { Button, Flex, FlexItem, Tooltip } from '@wordpress/components';
import { useDispatch, useSelect } from '@wordpress/data';
import { useEffect } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import { Icon, trash } from '@wordpress/icons';

import './editor.pcss';

export default function Edit( { attributes, clientId, setAttributes } ) {
	const blockProps = useBlockProps( { className: 'b-vertical-tabs' } );
	const dispatch = useDispatch( 'core/block-editor' );
	const { removeBlocks } = useDispatch( 'core/block-editor' );
	const select = useSelect( 'core/block-editor' );
	const innerBlocks = select.getBlocks( clientId );
	const { currentActiveTabInstanceId, tabs } = attributes;

	// update the current active tab to the client Id of the first tab
	useEffect( () => {
		if ( innerBlocks.length === 0 || currentActiveTabInstanceId !== '' ) {
			return;
		}

		setAttributes( {
			currentActiveTabInstanceId: innerBlocks[ 0 ].attributes.blockId,
		} );
	}, [ innerBlocks, currentActiveTabInstanceId, setAttributes ] );

	/**
	 * @function removeTab
	 *
	 * @description removes a tab from the block; adds a new one if the last one was deleted; sets new active tab
	 *
	 * @param {*} index
	 * @param {*} tabBlockId
	 * @param {*} tabClientId
	 */
	const removeTab = ( index, tabBlockId, tabClientId ) => {
		removeBlocks( tabClientId );

		// Fetch new inner blocks
		const newInnerBlocks = select.getBlocks( clientId );

		// Add a new tab if we've deleted the last one
		if ( newInnerBlocks.length === 0 ) {
			addNewTab( newInnerBlocks.length );

			return;
		}

		// decide which block should be the new selected block
		let newActiveTabInstanceId = currentActiveTabInstanceId;

		if ( newActiveTabInstanceId === tabBlockId ) {
			// if we want the first block, show new "first block", any other tab, use the "next" tab
			newActiveTabInstanceId =
				index === 0
					? newInnerBlocks[ index ].attributes.blockId
					: newInnerBlocks[ index - 1 ].attributes.blockId;
		}

		setAttributes( {
			currentActiveTabInstanceId: newActiveTabInstanceId,
		} );
	};

	/**
	 * @function addNewTab
	 *
	 * @description creates a new tab block, adds it to the block and activates it
	 *
	 * @param {*} positionToAdd
	 */
	const addNewTab = ( positionToAdd = innerBlocks.length ) => {
		// create block
		const newTab = createBlock( 'tribe/vertical-tab' );

		// add new tab; dispatch will return us a promise which we can use to set our new active tab instanceId
		dispatch
			.insertBlock( newTab, positionToAdd, clientId, true )
			.then( () => {
				const newInnerBlocks = select.getBlocks( clientId );

				const newInstanceId =
					newInnerBlocks[ positionToAdd ].attributes.blockId;

				// set new tab as active
				setAttributes( {
					currentActiveTabInstanceId: newInstanceId,
				} );
			} );
	};

	/**
	 * @function updateTabAttributes
	 *
	 * @description updates attributes of specific block given attributes & clientId
	 *
	 * @param {*} tabAttributes
	 * @param {*} tabClientId
	 */
	const updateTabAttributes = ( tabAttributes, tabClientId ) => {
		// update child block attributes
		dispatch.updateBlockAttributes( tabClientId, tabAttributes );

		/**
		 * re-render parent block
		 *
		 * note: this is a workaround. We'll likely want to find a better
		 * solution we understand in the future
		 */
		setAttributes( {
			blockUpdated: Date.now(),
		} );
	};

	// setup inner block props and add classname to wrapper
	const innerBlocksProps = useInnerBlocksProps(
		{
			className: 'b-vertical-tabs__tab-content',
		},
		{
			allowedBlocks: [ 'tribe/vertical-tab' ],
			template: [ [ 'tribe/vertical-tab' ] ],
			renderAppender: false,
		}
	);

	// set new tab state when innerBlocks or currentActiveTabInstanceId changes
	useEffect( () => {
		const data = innerBlocks.map( ( tab ) => {
			return {
				clientId: tab.clientId,
				id: tab.attributes.blockId,
				buttonId: 'vt-button-' + tab.attributes.blockId,
				title: tab.attributes.title,
				content: tab.attributes.content,
				isSelected:
					currentActiveTabInstanceId === tab.attributes.blockId,
			};
		} );

		setAttributes( {
			tabs: data,
		} );
	}, [ innerBlocks, currentActiveTabInstanceId, setAttributes ] );

	return (
		<div { ...blockProps }>
			<div className="b-vertical-tabs__tab-container">
				{ tabs
					? tabs.map( ( tab, index ) => {
							return (
								<div
									key={ index }
									className={
										tab.isSelected
											? 'b-vertical-tabs__tab editor-is-selected'
											: 'b-vertical-tabs__tab'
									}
								>
									<Flex
										className="b-vertical-tabs__tab-header"
										align="flex-start"
										justify="space-between"
									>
										<FlexItem
											onClick={ () => {
												setAttributes( {
													currentActiveTabInstanceId:
														tab.id,
												} );
											} }
										>
											<RichText
												tagName="span"
												className="b-vertical-tabs__tab-title t-display-xx-small s-remove-margin--top"
												value={ tab.title }
												onChange={ ( value ) =>
													updateTabAttributes(
														{ title: value },
														tab.clientId
													)
												}
												allowedFormats={ [] }
												placeholder={ __(
													'Tab Heading',
													'tribe'
												) }
											/>
										</FlexItem>
										<FlexItem>
											<Tooltip
												text="Delete tab"
												delay={ 300 }
											>
												<Button
													__next40pxDefaultSize={
														true
													}
													variant="primary"
													isDestructive={ true }
													onClick={ ( e ) => {
														e.stopPropagation();
														removeTab(
															index,
															tab.id,
															tab.clientId
														);
													} }
												>
													<Icon
														icon={ trash }
														size={ 24 }
													/>
												</Button>
											</Tooltip>
										</FlexItem>
									</Flex>
									{ tab.isSelected ? (
										<div className="b-vertical-tabs__tab-hidden">
											<RichText
												tagName="span"
												className="b-vertical-tabs__tab-content"
												value={ tab.content }
												onChange={ ( value ) =>
													updateTabAttributes(
														{ content: value },
														tab.clientId
													)
												}
												allowedFormats={ [
													'core/bold',
													'core/italic',
												] }
												placeholder={ __(
													'Tab Description',
													'tribe'
												) }
											/>
										</div>
									) : (
										''
									) }
								</div>
							);
					  } )
					: '' }
				<Button
					__next40pxDefaultSize
					variant="primary"
					onClick={ () => addNewTab() }
					className="b-vertical-tabs__editor-add-tab"
				>
					{ __( 'Add New Tab', 'tribe' ) }
				</Button>
			</div>
			<div { ...innerBlocksProps } />
		</div>
	);
}
