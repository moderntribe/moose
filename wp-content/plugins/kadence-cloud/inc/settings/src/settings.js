/* Global kadenceSettingsParams kadenceSettingsOptions */
/**
 * WordPress dependencies
 */
import SettingsField from './field';
import ResponsiveField from './responsivefield';
import kadenceTryParseJSON from './components/common/try-parse';
const { __, sprintf } = wp.i18n;
import { dispatch } from '@wordpress/data';
import { hasFilter } from '@wordpress/hooks';
import { Fragment, Component, RawHTML, render } from '@wordpress/element';
const { TabPanel, Panel, PanelBody, PanelRow, Button, Spinner } = wp.components;
class SettingsPanel extends Component {
	constructor() {
		super( ...arguments );
		this.onChange = this.onChange.bind( this );
		this.requiredVisibleCheck = this.requiredVisibleCheck.bind( this );
		this.requiredSingleCheck = this.requiredSingleCheck.bind( this );
		this.state = {
			update: false,
		}
	}
	onChange( setting_id, value ) {
		const newSettings = kadenceTryParseJSON( window.kadenceSettingsOptions );
		newSettings[ setting_id ] = value;
		window.kadenceSettingsOptions = JSON.stringify( newSettings );
		this.setState({ update: ! this.state.update });
	}
	requiredSingleCheck( setting, condition, value, settings ) {
		switch (condition) {
			case '!=':
				if ( settings[ setting ] != value ) {
					return true;
				} else {
					return false;
				}
				break;
		
			default:
				if ( settings[ setting ] == value ) {
					return true;
				} else {
					return false;
				}
				break;
		}
	}
	requiredVisibleCheck( field ) {
		const settings = kadenceTryParseJSON( window.kadenceSettingsOptions );
		if ( ! field.required ) {
			return true;
		}
		if ( undefined === field.required[0] ) {
			return true;
		}
		if ( Array.isArray( field.required[0] ) ) {
			let arrayLength = field.required.length;
			let show        = true;
			for( let i = 0 ; i < arrayLength; i++) {
				if ( undefined === field.required[i] || undefined === field.required[i][0] || undefined === field.required[i][1] || undefined === field.required[i][2] ) {
					return true;
				}
				let setting = field.required[i][0];
				let condition = field.required[i][1];
				let value = field.required[i][2];
				if ( 'true' === value ) {
					value = true;
				} else if ( 'false' === value ) {
					value = false;
				}
				if ( ! this.requiredSingleCheck( setting, condition, value, settings ) ) {
					return false;
					break;
				}
			}
			return true;
		} else {
			// Make sure we have the data.
			if ( undefined === field.required[0] || undefined === field.required[1] || undefined === field.required[2] ) {
				return true;
			}
			const setting = field.required[0];
			const condition = field.required[1];
			let value = field.required[2];
			if ( 'true' === value ) {
				value = true;
			} else if ( 'false' === value ) {
				value = false;
			}
			return this.requiredSingleCheck( setting, condition, value, settings );
		}
	}
	render() {
		const { section } = this.props;
		const control = this;
		const settings = kadenceTryParseJSON( window.kadenceSettingsOptions );
		return (
			 <Fragment>
				<h2>{ section.long_title ? section.long_title : section.title }</h2>
				{ Object.keys( section.fields ).map( function( key, index ) {
					if ( section.fields[ key ].responsive ) {
						return (
							<Fragment>
								{ control.requiredVisibleCheck( section.fields[ key ] ) ? 
									<ResponsiveField
										field={ {
											desktop: section.fields[ key ].desktop,
											tablet: section.fields[ key ].tablet,
											mobile: section.fields[ key ].mobile,
										} }
										fieldValue={ {
											desktop: ( undefined !== settings[ section.fields[ key ].desktop.id ] ? settings[ section.fields[ key ].desktop.id ] : undefined ),
											tablet: ( undefined !== settings[ section.fields[ key ].tablet.id ] ? settings[ section.fields[ key ].tablet.id ] : undefined ),
											mobile: ( undefined !== settings[ section.fields[ key ].mobile.id ] ? settings[ section.fields[ key ].mobile.id ] : undefined ),

										} }
										onChange={ {
											desktop: ( value ) => control.onChange( section.fields[ key ].desktop.id, value ),
											tablet: ( value ) => control.onChange( section.fields[ key ].tablet.id, value ),
											mobile: ( value ) => control.onChange( section.fields[ key ].mobile.id, value ),
										} }
									/>
								:
									''
								}
							</Fragment>
						);
					}
					return (
						<Fragment>
							{ control.requiredVisibleCheck( section.fields[ key ] ) ? 
								<SettingsField field={ section.fields[ key ] } fieldValue={ ( undefined !== settings[ section.fields[ key ].id ] ? settings[ section.fields[ key ].id ] : undefined ) } onChange={ ( value ) => control.onChange( section.fields[ key ].id, value ) } />
							:
								''
							}
						</Fragment>
					);
				} ) }
				<Button
					className="kadence-settings-save"
					isPrimary
					onClick={ () => this.props.saveSettings() }
				>
					{ __( 'Save', 'kadence-settings' ) }
				</Button>
			 </Fragment>
		 );
	 }
 }

 export default SettingsPanel;