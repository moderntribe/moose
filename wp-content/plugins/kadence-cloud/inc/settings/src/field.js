/* Global kadenceSettingsParams */
/**
 * WordPress dependencies
 */
const { __, sprintf } = wp.i18n;
import TextRepeater from './components/repeater/text-repeater';
import TextRepeaterExpanded from './components/repeater/text-repeater-expand';
import ImageSelectControl from './components/image-select/image-select-control';
import RecaptchaPreview from './components/validation/recaptcha-preview';
import kadenceTryParseJSON from './components/common/try-parse';
import { Fragment, Component, RawHTML, render } from '@wordpress/element';
import { ColorPalette, TextControl, TextareaControl, SelectControl, ToggleControl, RangeControl, Panel, PanelBody, PanelRow, Button } from '@wordpress/components';
 class SettingsField extends Component {
	constructor() {
		super( ...arguments );
		this.state = {
			value: ( undefined !== this.props.fieldValue ? this.props.fieldValue : this.props.field.default ),
			savedValue: ( undefined !== this.props.fieldValue ? this.props.fieldValue : this.props.field.default ),
		};
	}
	render() {
		const { field } = this.props;
		const settings = kadenceTryParseJSON( window.kadenceSettingsOptions );
		switch ( field.type ) {
			case 'text':
				const currentVal = ( field.obfuscate && this.state.savedValue && this.state.savedValue === this.state.value ? this.state.value.replace(/(\w| )(?=(\w| ){4})/g, 'X') : this.state.value );
				return (
					<div className={ 'kadence-settings-component-field kadence-settings-field-type-' + field.type }>
						<TextControl
							label={ field.title }
							className={ 'kadence-settings-component-' + field.id }
							value={ currentVal }
							onChange={ ( value ) => {
								this.setState( { value: value } );
								this.props.onChange( value );
							} }
							help={ field.help ? ( field.helpLink ? <a href={ field.helpLink } target={ '_blank' }>{ field.help }</a> : <span dangerouslySetInnerHTML={ { __html: field.help } }/> ) : undefined }
							placeholder={ field.placeholder ? field.placeholder : undefined }
						/>
					</div>
				);
			case 'textarea':
				return (
					<div className={ 'kadence-settings-component-field kadence-settings-field-type-' + field.type }>
						<TextareaControl
							label={ field.title }
							className={ 'kadence-settings-component-' + field.id }
							value={ this.state.value }
							onChange={ ( value ) => {
								this.setState( { value: value } );
								this.props.onChange( value );
							} }
							help={ field.help ? ( field.helpLink ? <a href={ field.helpLink } target={ '_blank' }>{ field.help }</a> : <span dangerouslySetInnerHTML={ { __html: field.help } }/> ) : undefined }
							placeholder={ field.placeholder ? field.placeholder : undefined }
						/>
					</div>
				);
			case 'text_repeater':
				return (
					<div className={ 'kadence-settings-component-field kadence-settings-field-type-' + field.type }>
						<TextRepeater
							field={ field }
							value={ this.state.value }
							onChange={ ( value ) => {
								this.setState( { value: value } );
								this.props.onChange( value );
							} }
						/>
					</div>
				);
			case 'text_repeater_expanded':
				return (
					<div className={ 'kadence-settings-component-field kadence-settings-field-type-' + field.type }>
						<TextRepeaterExpanded
							field={ field }
							value={ this.state.value }
							onChange={ ( value ) => {
								this.setState( { value: value } );
								this.props.onChange( value );
							} }
						/>
					</div>
				);
			case 'select':
				const options = Object.keys( field.options ).map( function( key, index ) {
					return { value: key, label: field.options[ key ] }
				} );
				return (
					<div className={ 'kadence-settings-component-field kadence-settings-field-type-' + field.type }>
						<SelectControl
							label={ field.title }
							value={ this.state.value }
							className={ 'kadence-settings-component-' + field.id }
							options={ options }
							help={ field.help ? field.help : undefined }
							onChange={ ( value ) => {
								this.setState( { value: value } );
								this.props.onChange( value );
							} }
						/>
					</div>
				);
			case 'image_select':
				return (
					<div className={ 'kadence-settings-component-field kadence-settings-field-type-' + field.type }>
						<ImageSelectControl
							field={ field }
							value={ this.state.value }
							onChange={ ( value ) => {
								this.setState( { value: value } );
								this.props.onChange( value );
							} }
						/>
					</div>
				);
			case 'range':
				return (
					<div className={ 'kadence-settings-component-field kadence-settings-field-type-' + field.type }>
						<RangeControl
							label={ field.title }
							value={ this.state.value }
							className={ 'kadence-settings-component-' + field.id }
							help={ field.help ? field.help : undefined }
							initialPosition={ field.default ? field.default : undefined }
							min={ field.min ? field.min : undefined }
							max={ field.max ? field.max : undefined }
							step={ field.step ? field.step : undefined }
							allowReset={ true }
							onChange={ ( value ) => {
								this.setState( { value: value } );
								this.props.onChange( value );
							} }
						/>
					</div>
				);
			case 'color':
				// const themeColors = useSetting( 'color.palette.theme' );
				return (
					<div className={ 'kadence-settings-component-field kadence-settings-field-type-' + field.type }>
						<div className={ 'components-base-control' }>
							{ field.title && (
								<label className="components-base-control__label">
									{ field.title }
								</label>
							) }
							{ field.help && (
								<span className="components-base-control__help" dangerouslySetInnerHTML={ { __html: field.help } }>
								</span>
							) }
							<ColorPalette
								colors={ kadenceSettingsParams.themeColors && kadenceSettingsParams.themeColors[0] ? kadenceSettingsParams.themeColors[0] : [] }
								value={ this.state.value }
								className={ 'kadence-settings-component-' + field.id }
								default={ field.default ? field.default : undefined }
								clearable={ false }
								onChange={ ( value ) => {
									this.setState( { value: value } );
									this.props.onChange( value );
								} }
							/>
						</div>
					</div>
				);
			case 'switch':
				const currentChecked = ( undefined !== this.state.value && 0 == this.state.value ? false : this.state.value );
				return (
					<div className={ 'kadence-settings-component-field kadence-settings-field-type-' + field.type }>
						<ToggleControl
							label={ field.title ? field.title : undefined }
							className={ 'kadence-settings-component-' + field.id }
							checked={ currentChecked }
							help={ field.help ? <span dangerouslySetInnerHTML={ { __html: field.help } }/> : undefined }
							onChange={ ( value ) => {
								if ( ! value ) {
									this.setState( { value: 0 } );
									this.props.onChange( 0 );
								} else {
									this.setState( { value: value } );
									this.props.onChange( value );
								}
							} }
						/>
					</div>
				);
			case 'code_info':
				return (
					<div className={ 'kadence-settings-component-field kadence-settings-field-type-' + field.type }>
						<div className={ 'components-base-control kadence-settings-text-repeater-control' }>
							{ field.title && (
								<span className="components-base-control__label">
									{ field.title }
								</span>
							) }
							{ field.help && (
								<span className="components-base-control__help">
									{ field.help }
								</span>
							) }
							{ field.content && (
								<code className="components-base-control__code">
									{ field.content }
								</code>
							) }
						</div>
					</div>
				);
			case 'raw':
				return (
					<div className={ 'kadence-settings-component-field kadence-settings-field-type-' + field.type + ' kadence-settings-field-id-' + field.id }>
						<div className={ 'components-base-control' }>
							{ field.title && (
								<label className="components-base-control__label">
									{ field.title }
								</label>
							) }
							{ field.help && (
								<span className="components-base-control__help" dangerouslySetInnerHTML={ { __html: field.help } }>
								</span>
							) }
							{ field.content && (
								<div className="components-base-control__raw" dangerouslySetInnerHTML={ { __html: field.content } }>
								</div>
							) }
						</div>
					</div>
				);
			case 'recaptcha_preview':
				return (
					<div className={ 'kadence-settings-component-field kadence-settings-field-type-' + field.type }>
						<div className={ 'components-base-control' }>
							{ field.title && (
								<label className="components-base-control__label">
									{ field.title }
								</label>
							) }
							<RecaptchaPreview 
								settings={settings}
							/>
						</div>
					</div>
				);
			default:
				return (
					<div className={ 'kadence-settings-component-field' }>
						{ field.title }
					</div>
				);
		}
	}
}

 export default SettingsField;