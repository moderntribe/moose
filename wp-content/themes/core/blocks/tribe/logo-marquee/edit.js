/**
 * WordPress Dependencies
 */

import {
	useBlockProps,
	MediaPlaceholder,
	BlockControls,
	MediaReplaceFlow,
} from '@wordpress/block-editor';
import { Button, ToolbarGroup } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import { useSelect } from '@wordpress/data';
import { store as coreStore } from '@wordpress/core-data';
import { trash } from '@wordpress/icons';
import './editor.pcss';

export default function Edit( { attributes, setAttributes, isSelected } ) {
	const { images } = attributes;
	const blockProps = useBlockProps();

	// Fetch media details for each image
	// eslint-disable-next-line no-unused-vars
	const imageData = useSelect(
		( select ) => {
			const { getMedia } = select( coreStore );
			return images.map( ( img ) => {
				if ( ! img.id ) {
					return null;
				}
				const media = getMedia( img.id );
				if ( ! media ) {
					return null;
				}
				return {
					id: img.id,
					url: img.url,
					alt: img.alt || media.alt_text,
					title: media.title?.raw || '',
				};
			} );
		},
		[ images ]
	);

	// Handle selection of new media items
	const onSelectImages = ( newImages ) => {
		setAttributes( {
			images: newImages.map( ( image ) => ( {
				id: image.id,
				url: image.url || image.source_url,
				alt: image.alt || image.alt_text || '',
			} ) ),
		} );
	};

	// Handle removing an image
	const removeImage = ( index ) => {
		const newImages = [ ...images ];
		newImages.splice( index, 1 );
		setAttributes( { images: newImages } );
	};

	// Basic image styles
	const imageStyle = {
		maxWidth: '200px',
		height: 'auto',
	};

	return (
		<>
			<BlockControls>
				{ images.length > 0 && (
					<ToolbarGroup>
						<MediaReplaceFlow
							mediaIds={ images.map( ( img ) => img.id ) }
							allowedTypes={ [ 'image' ] }
							accept="image/*"
							multiple
							onSelect={ onSelectImages }
						/>
					</ToolbarGroup>
				) }
			</BlockControls>
			<div { ...blockProps }>
				{ images.length === 0 ? (
					<MediaPlaceholder
						icon="format-gallery"
						labels={ {
							title: __( 'Logo Marquee' ),
							instructions: __(
								'Drag images, upload new ones or select files from your library.'
							),
						} }
						onSelect={ onSelectImages }
						accept="image/*"
						allowedTypes={ [ 'image' ] }
						multiple
					/>
				) : (
					<div className="logo-list">
						{ images.map( ( img, index ) => (
							<div key={ index } className="gallery-item">
								<div style={ { position: 'relative' } }>
									<img
										src={ img.url }
										alt={ img.alt }
										data-id={ img.id }
										style={ imageStyle }
									/>
									{ isSelected && (
										<Button
											className="remove-image-button"
											icon={ trash }
											onClick={ () =>
												removeImage( index )
											}
											style={ {
												position: 'absolute',
												top: '5px',
												right: '5px',
												backgroundColor:
													'rgba(0,0,0,0.7)',
												color: 'white',
												padding: '2px',
											} }
										/>
									) }
								</div>
							</div>
						) ) }
						{ isSelected && (
							<div
								className="logo-list-add-item"
								style={ {
									border: '1px dashed #ddd',
									display: 'flex',
									justifyContent: 'center',
									alignItems: 'center',
									width: '200px',
									height: '200px',
								} }
							>
								<MediaPlaceholder
									icon="plus"
									labels={ {
										title: __( 'Add to gallery' ),
										instructions: '',
									} }
									onSelect={ ( newImages ) => {
										setAttributes( {
											images: [
												...images,
												...newImages.map(
													( image ) => ( {
														id: image.id,
														url:
															image.url ||
															image.source_url,
														alt:
															image.alt ||
															image.alt_text ||
															'',
													} )
												),
											],
										} );
									} }
									accept="image/*"
									allowedTypes={ [ 'image' ] }
									multiple
									value={ {} }
								/>
							</div>
						) }
					</div>
				) }
			</div>
		</>
	);
}
