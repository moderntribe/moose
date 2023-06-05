/* Global kadenceSettingsParams */
/**
 * WordPress dependencies
 */
import SettingsPanel from './settings';
import ChangelogTab from './changelog';
import StartedTab from './started';
import DashTab from './dash';
import kadenceTryParseJSON from './components/common/try-parse';
const { __, sprintf } = wp.i18n;
const {
	localStorage,
} = window;
import { Fragment, Component } from '@wordpress/element';
const { TabPanel, Panel, PanelBody, PanelRow, Button, Spinner } = wp.components;
 class InfoPanel extends Component {
	constructor() {
		super( ...arguments );
	}
	render() {
		const { panel, sections } = this.props;
		const subTabs = [];
		{ Object.keys( sections ).map( function( key, index ) {
			subTabs.push( {
				name: sections[ key ].id,
				title: sections[ key ].title,
				className: 'kadence-sections-tab-' + sections[ key ].id,
			} );
		} ) }
		const activePanel = kadenceTryParseJSON( localStorage.getItem( 'kadenceSettingsPanel' ) );
		const onTabSelect = ( tabName ) => {
			const activeTab = activePanel;
			activeTab[kadenceSettingsParams.opt_name] = tabName;
			localStorage.setItem( 'kadenceSettingsPanel', JSON.stringify( activeTab ) );
		};
		const KadenceSettingsPanel = () => (
			<Fragment>
				{ 1 < subTabs.length ?
					<TabPanel
						className="kadence-settings-dashboard-section-tabs"
						activeClass="active-tab"
						orientation="vertical"
						initialTabName={ activePanel && activePanel[kadenceSettingsParams.opt_name] ? activePanel[kadenceSettingsParams.opt_name] : undefined }
						onSelect={ onTabSelect }
						tabs={ subTabs }
					>
						{
							( tab ) => {
								return (
									<Panel className="dashboard-section sub-tab-section">
										<PanelBody
											opened={ true }
										>
											<div className="dashboard-modules-wrapper">
												<SettingsPanel section={ sections[ tab.name ] } saveSettings={ () => this.props.saveSettings() } />
											</div>
										</PanelBody>
									</Panel>
								);
							}
						}
					</TabPanel>
				:
					<Panel className="dashboard-section sub-tab-section">
						<PanelBody
							opened={ true }
						>
							<div className="dashboard-modules-wrapper">
								<SettingsPanel section={ sections[Object.keys(sections)[0]] } saveSettings={ () => this.props.saveSettings() } />
							</div>
						</PanelBody>
					</Panel>
				}
			</Fragment>
		);
		 return (
			 <Fragment>
				{ 'settings' === panel &&  (
					<KadenceSettingsPanel />
				) }
				{ 'changelog' === panel &&  (
					<ChangelogTab />
				) }
				{ 'started' === panel &&  (
					<StartedTab />
				) }
				{ 'dash' === panel &&  (
					<DashTab />
				) }
			 </Fragment>
		 );
	 }
 }

 export default InfoPanel;