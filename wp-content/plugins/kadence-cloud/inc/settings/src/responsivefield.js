/* Global kadenceSettingsParams */
/**
 * WordPress dependencies
 */
const { __, sprintf } = wp.i18n;
import SettingsField from './field';
import { Fragment, Component, RawHTML, render } from '@wordpress/element';
import { TabPanel, Icon, TextControl, SelectControl, ToggleControl, Panel, PanelBody, PanelRow, Button } from '@wordpress/components';
import { desktop, tablet, mobile } from '@wordpress/icons';
 class ResponsiveField extends Component {
	constructor() {
		super( ...arguments );
		this.state = {
			value: ( undefined !== this.props.fieldValue ? this.props.fieldValue : this.props.field.default ),
			savedValue: ( undefined !== this.props.fieldValue ? this.props.fieldValue : this.props.field.default ),
		};
	}
	render() {
		return (
			<TabPanel
				className="kadence-settings-responsive-tabs"
				activeClass="active-device"
				tabs={ [
					{
						name: 'desktop',
						title: <Icon icon={ desktop } />,
					},
					{
						name: 'tablet',
						title: <Icon icon={ tablet } />,
					},
					{
						name: 'mobile',
						title: <Icon icon={ mobile } />,
					}
				] }
			>
				{
					( tab ) => {
						return (
							<SettingsField field={ this.props.field[ tab.name ] } fieldValue={ this.props.fieldValue[ tab.name ] } onChange={ this.props.onChange[ tab.name ] } />
						);
					}
				}
			</TabPanel>
		)
		
	}
}

 export default ResponsiveField;