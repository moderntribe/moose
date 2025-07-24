/**
 * WordPress Dependencies
 */
import { createBlock } from '@wordpress/blocks';
import {
	useBlockProps,
	RichText,
	useInnerBlocksProps,
} from '@wordpress/block-editor';
import { Button, Flex, FlexItem } from '@wordpress/components';
import { useSelect, useDispatch } from '@wordpress/data';
import { useEffect } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import { SVG, Path } from '@wordpress/primitives';

import './editor.pcss';

export default function Edit( { clientId, attributes, setAttributes } ) {
	const blockProps = useBlockProps();
	const dispatch = useDispatch( 'core/block-editor' );
	const { removeBlocks } = useDispatch( 'core/block-editor' );
	const select = useSelect( 'core/block-editor' );
	const innerBlocks = select.getBlocks( clientId );
	const { currentActiveTabInstanceId, tabs } = attributes;

	/**
	 * setup inner block props and add classname to wrapper
	 */
	const innerBlocksProps = useInnerBlocksProps(
		{
			className: 'wp-block-tribe-tabs__tab-content',
		},
		{
			allowedBlocks: [ 'tribe/tab' ],
			template: [ [ 'tribe/tab' ] ],
			renderAppender: false,
		}
	);

	/**
	 * Update the current active tab to the client Id of the first tab
	 * This will only run once when the block is first added to the editor
	 */
	useEffect( () => {
		if ( innerBlocks.length === 0 || currentActiveTabInstanceId !== '' ) {
			return;
		}

		setAttributes( {
			currentActiveTabInstanceId: innerBlocks[ 0 ].attributes.blockId,
		} );
	}, [ innerBlocks, setAttributes, currentActiveTabInstanceId ] );

	/**
	 * set new tab state when innerBlocks or currentActiveTabInstanceId changes
	 */
	useEffect( () => {
		const data = innerBlocks.map( ( tab ) => {
			return {
				clientId: tab.clientId,
				id: tab.attributes.blockId,
				buttonId: 'button-' + tab.attributes.blockId,
				label: tab.attributes.tabLabel,
				isActive: currentActiveTabInstanceId === tab.attributes.blockId,
			};
		} );

		setAttributes( {
			tabs: data,
		} );
	}, [ innerBlocks, currentActiveTabInstanceId, setAttributes ] );

	/**
	 * @function updateTabLabel
	 *
	 * @description Dispatch action to matching tab to update its label
	 *
	 * @param {string} tabLabel
	 * @param {string} tabClientId
	 */
	const updateTabLabel = ( tabLabel, tabClientId ) => {
		dispatch.updateBlockAttributes( tabClientId, { tabLabel } );
	};

	/**
	 * @function addNewTab
	 *
	 * @description adds a new tab and an InnerBlock to match
	 *
	 * @param {number} positionToAdd
	 */
	const addNewTab = ( positionToAdd = innerBlocks.length ) => {
		// create block
		const newTab = createBlock( 'tribe/tab' );

		// add new tab
		dispatch
			.insertBlock( newTab, positionToAdd, clientId, true )
			.then( () => {
				// dispatch will return us a promise which we can use to set our new active tab instanceId
				const newInstanceId =
					select.getBlocks( clientId )[ positionToAdd ].attributes
						.blockId;

				// set new tab as active
				setAttributes( {
					currentActiveTabInstanceId: newInstanceId,
				} );
			} );
	};

	/**
	 * @function deleteTab
	 *
	 * @description handles removing tab & InnerBlock based on index
	 *
	 * @param {number} index
	 * @param {string} tabInstanceId
	 * @param {string} tabClientId
	 */
	const deleteTab = ( index, tabInstanceId, tabClientId ) => {
		// remove block from InnerBlocks
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

		if ( newActiveTabInstanceId === tabInstanceId ) {
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

	return (
		<div { ...blockProps }>
			<Flex
				className="wp-block-tribe-tabs__tabs"
				align="center"
				justify="flex-start"
			>
				{ tabs.map( ( tab, index ) => (
					<FlexItem
						className={
							'wp-block-tribe-tabs__tab' +
							( currentActiveTabInstanceId === tab.id
								? ' active-tab'
								: '' )
						}
						key={ 'tab-' + tab.id }
						style={ {
							display: 'flex',
							alignItems: 'center',
							justifyContent: 'flex-start',
							gap: 'var(--spacer-10)',
						} }
						onClick={ () => {
							setAttributes( {
								currentActiveTabInstanceId: tab.id,
							} );
						} }
					>
						<RichText
							tagName="span"
							className="wp-block-tribe-tabs__tab-label"
							value={ tab.label }
							onChange={ ( value ) =>
								updateTabLabel( value, tab.clientId )
							}
							allowedFormats={ [] }
							placeholder={ __( 'Tab Label', 'tribe' ) }
						/>
						<Button
							className="wp-block-tribe-tabs__tab-delete"
							variant="link"
							onClick={ ( e ) => {
								e.stopPropagation();
								deleteTab( index, tab.id, tab.clientId );
							} }
						>
							<span
								style={ {
									display: 'inline-flex',
									width: '24px',
									height: '24px',
								} }
							>
								<SVG
									xmlns="http://www.w3.org/2000/svg"
									viewBox="0 0 24 24"
								>
									<Path d="m13.06 12 6.47-6.47-1.06-1.06L12 10.94 5.53 4.47 4.47 5.53 10.94 12l-6.47 6.47 1.06 1.06L12 13.06l6.47 6.47 1.06-1.06L13.06 12Z" />
								</SVG>
							</span>
						</Button>
					</FlexItem>
				) ) }
				<FlexItem
					className="wp-block-tribe-tabs__add"
					justify="flex-start"
				>
					<Button variant="primary" onClick={ () => addNewTab() }>
						{ __( 'Add New Tab', 'tribe' ) }
					</Button>
				</FlexItem>
			</Flex>
			<div { ...innerBlocksProps } />
		</div>
	);
}
