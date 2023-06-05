/* Global kadenceSettingsParams */
/**
 * WordPress dependencies
 */
const { __, sprintf } = wp.i18n;
import GenerateKey from '../common/generate-key';
import { useState, useCallback, Fragment } from  '@wordpress/element';
import map from 'lodash/map';
import { TabPanel, TextControl, TextareaControl, selectControl, ToggleControl, Panel, PanelBody, PanelRow, Button } from '@wordpress/components';
import { edit } from '@wordpress/icons';
import Select from 'react-select';
const { taxonomies } = kadenceSettingsParams;
/**
 * Build the Measure controls
 * @returns {object} Measure settings.
 */
 export default function TextRepeaterExpanded( {
	field,
	onChange,
	value,
} ) {
	const [ isEditNote, setEditNote ] = useState( false );
	const [ isRemovable, setIsRemovable ] = useState( false );
	let taxonomyOptions = [];
	if ( 'undefined' !== typeof taxonomies[ 'kadence_cloud'] ) {
		if ( taxonomies[ 'kadence_cloud' ].terms && taxonomies[ 'kadence_cloud' ].terms[ 'kadence-cloud-collections' ] ) {
			taxonomyOptions = taxonomies[ 'kadence_cloud' ].terms[ 'kadence-cloud-collections' ];
		}
	}
	const toggleEditNote = useCallback( ( index ) => {
		if ( index === isEditNote ) {
			setEditNote( false );
		} else {
			setEditNote( index );
		}
	} );
	const onChangeRemoveToggle = useCallback( ( index ) => {
		setIsRemovable( index );
	} );
	function onChangeKey( subValue, index ) {
		const newValue = value;
		if ( typeof newValue[index] !== 'object' ) {
			newValue[index] = {
				'key': '',
				'note': '',
				'collections': '',
			}
		}
		newValue[index].key = subValue;
		onChange( newValue );
	}
	function onChangeCollections( subValue, index ) {
		const newValue = value;
		if ( typeof newValue[index] !== 'object' ) {
			newValue[index] = {
				'key': '',
				'note': '',
				'collections': '',
			}
		}
		const updateSub = [];
		subValue.forEach(function (item, index) {
			updateSub.push( item.value );
		});
		const updateSubString = updateSub.join();
		newValue[index].collections = updateSubString;
		onChange( newValue );
	}
	function onChangeNote( subValue, index ) {
		const newValue = value;
		if ( typeof newValue[index] !== 'object' ) {
			newValue[index] = {
				'key': '',
				'note': '',
				'collections': '',
			}
		}
		newValue[index].note = subValue;
		onChange( newValue );
	}
	function onChangeRemoveItem( index ) {
		const newValue = value;
		newValue.splice(index, 1);
		onChange( newValue );
	}
	function addKey() {
		let newValue = value;
		if ( ! newValue ) {
			newValue = [];
		}
		const newItem = {
			'key': GenerateKey( 12 ),
			'note': '',
			'collections': '',
		}
		newValue.push( newItem );
		onChange( newValue );
	}
	function addEmpty() {
		let newValue = value;
		if ( ! newValue ) {
			newValue = [];
		}
		const newItem = {
			'key': '',
			'note': '',
			'collections': '',
		}
		newValue.push( newItem );
		onChange( newValue );
	}
	return (
		<div className={ 'components-base-control kadence-settings-text-repeater-control' }>
			{ field.title && (
				<label className="components-base-control__label">
					{ field.title }
				</label>
			) }
			{ value && value instanceof Array && (
				<Fragment>
					{ map( value, ( item, index ) => (
						<div className={ `components-base-control__inner kadence-settings-text-repeater-control-item${ undefined !== field?.label && '' !== field?.label ? ' kadence-settings-text-repeater-control-has-label' : ''}` }>
							<TextControl
								label={ undefined !== field?.label && '' !== field?.label ? field.label : undefined }
								disabled={ ( field.editable ? false : true ) }
								value={ ( typeof item === 'object' ? item.key : item ) }
								onChange={ ( value ) => onChangeKey( value, index ) }
							/>
							{ '' !== ( typeof item === 'object' ? item.note : '' ) && (
								<div className={ 'kadence-settings-text-repeater-note-preview' }>
									{ item.note }
								</div>
							) }
							<Button
								icon={ edit }
								isSmall
								label={ __( 'Edit', 'kadence-settings' ) }
								onClick={ () => toggleEditNote( index ) }
								aria-expanded={ index === isEditNote }
							/>
							{ isRemovable === index && (
								<Button
									isSmall
									isDestructive
									onClick={ () => onChangeRemoveItem( index ) }
								>
									{ __( 'Confirm Delete', 'kadence-settings' ) }
								</Button>
							) }
							{ isRemovable !== index && (
								<Button
									isSmall
									isDestructive
									onClick={ () => onChangeRemoveToggle( index ) }
								>
									{ field.remove_button ? field.remove_button : __( 'Remove', 'kadence-settings' ) }
								</Button>
							) }
							{ isEditNote === index && (
								<div className={ 'kadence-settings-text-repeater-note-control-item' }>
									<TextareaControl
										label={ undefined !== field?.note_label && '' !== field?.note_label ? field.note_label : __( 'Access Key Note' , 'kadence-settings' ) }
										value={ ( typeof item === 'object' ? item.note : '' ) }
										onChange={ ( value ) => onChangeNote( value, index ) }
									/>
									<div className="term-select-form-row components-base-control">
										<label htmlFor={ 'collections-selection' }>
											{ __( 'Select Collections', 'kadence-settings' ) }
										</label>
										<Select
											value={ ( typeof item === 'object' && item.collections ? taxonomyOptions.filter(({ value }) => item.collections.split(',').includes( value.toString() ) ) : '' ) }
											onChange={ ( value ) => {
												onChangeCollections( value, index );
											} }
											id={ 'collections-selection' }
											options={ taxonomyOptions }
											isMulti={ true }
											maxMenuHeight={ 300 }
											placeholder={ __( 'All', 'kadence-settings' ) }
										/>
									</div>
								</div>
							) }
						</div>
					) ) }
				</Fragment>
			) }
			<div className="kadence-settings-clear"></div>
			<Button
				className={ 'kadence-settings-repeater-add' }
				isSecondary
				isSmall
				onClick={ () => field.content === 'empty' ? addEmpty() : addKey() }
			>
				{ field.add_button ?  field.add_button : __( 'Add', 'kadence-settings' ) }
			</Button>
		</div>
	);
}
