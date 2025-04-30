import { __ } from '@wordpress/i18n';
import {
	BlockControls,
	RichText,
	useBlockProps,
	LinkControl,
} from '@wordpress/block-editor';
import {
	Popover,
	Toolbar,
	ToolbarButton,
	ToolbarGroup,
} from '@wordpress/components';
import { useState, useMemo, useRef } from '@wordpress/element';

export default function Edit( { attributes, setAttributes } ) {
	const itemLabelRef = useRef();

	const { text, url, target } = attributes;

	/**
	 * Use internal state instead of a ref to make sure that the component
	 * re-renders when the popover's anchor updates.
	 */
	const [ isEditingURL, setIsEditingURL ] = useState( false );

	/**
	 * When using the LinkControl component, it is best practice to use memoization
	 * for handling edits to the value prop (url, target, etc.)
	 * @see https://github.com/WordPress/gutenberg/tree/trunk/packages/block-editor/src/components/link-control#value
	 */
	const linkValue = useMemo(
		() => ( {
			url,
			title: text,
			opensInNewTab: target,
		} ),
		[ url, text, target ]
	);

	function startEditingURL() {
		setIsEditingURL( ( state ) => ! state );
	}

	function unlink() {
		setAttributes( {
			url: undefined,
			target: false,
		} );
		setIsEditingURL( false );
	}

	return (
		<div { ...useBlockProps() }>
			<BlockControls>
				<Toolbar>
					<ToolbarGroup label="Link">
						<ToolbarButton
							icon={ 'admin-links' }
							label="Link"
							onClick={ startEditingURL }
							isActive={ !! url }
						/>
					</ToolbarGroup>
				</Toolbar>
			</BlockControls>

			{ isEditingURL && (
				<Popover
					onClose={ () => {
						setIsEditingURL( false );
						itemLabelRef.current?.focus();
					} }
				>
					<LinkControl
						value={ linkValue }
						onChange={ ( linkState ) => {
							setAttributes( {
								url: linkState.url,
								target: linkState.opensInNewTab,
							} );
						} }
						onRemove={ () => {
							unlink();
							itemLabelRef.current?.focus();
						} }
					></LinkControl>
				</Popover>
			) }

			<RichText
				ref={ itemLabelRef }
				tagName="p"
				className="tribe-navigation-link__label"
				value={ text }
				onChange={ ( label ) => setAttributes( { text: label } ) }
				// this hides the default link button
				allowedFormats={ [] }
				placeholder={ __( 'Enter label text…', 'tribe' ) }
			/>
		</div>
	);
}
