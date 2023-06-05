/* Global kadenceSettingsParams */
/**
 * Internal dependencies
 */
//  import HelpTab from './help';
//  import ChangelogTab from './changelog';
//  import ProSettings from './pro-extension';
//  import RecommendedTab from './recomended';
//  import StarterTab from './starter';
import InfoPanel from './panel';
import Notices from './notices';
import SaveOverlay from './overlay';
const {
	localStorage,
} = window;
const { apiFetch } = wp;
window.kadenceSettingsOptions = '';
 /**
 * Import Css
 */
import './editor.scss';
/**
 * WordPress dependencies
 */
const { __, sprintf } = wp.i18n;
import { dispatch } from '@wordpress/data';
import { Fragment, Component, RawHTML, render } from '@wordpress/element';
const { TabPanel, Panel, PanelBody, PanelRow, Button, Spinner } = wp.components;
import kadenceTryParseJSON from './components/common/try-parse';
class KadenceSettingsDashboard extends Component {
	constructor() {
		super( ...arguments );
		this.saveSettings = this.saveSettings.bind( this );
		this.state = {
			settings: ( kadenceSettingsParams.settings ? kadenceTryParseJSON( kadenceSettingsParams.settings ) : {} ),
			sections: ( kadenceSettingsParams.sections ? kadenceTryParseJSON( kadenceSettingsParams.sections ) : {} ),
			tabs: ( kadenceSettingsParams.tabs ? kadenceTryParseJSON( kadenceSettingsParams.tabs ) : {} ),
			isSaving: false,
			opt_name: kadenceSettingsParams.opt_name,
		};
	}
	componentDidMount() {
		window.kadenceSettingsOptions = kadenceSettingsParams.settings;
	}
	saveSettings() {
		this.setState( { isSaving: true } );
		const settings = kadenceTryParseJSON( window.kadenceSettingsOptions );
		kadenceSettingsParams.settings = JSON.stringify( settings );
		apiFetch( {
			path: '/wp/v2/settings',
			method: 'POST',
			data: { [kadenceSettingsParams.opt_name]: JSON.stringify( settings ) },
		} ).then( ( response ) => {
			window.kadenceSettingsOptions = response[kadenceSettingsParams.opt_name];
			this.setState( {
				isSaving: false,
			} );
			this.state.isSaving = false;
			dispatch( 'core/notices' ).createNotice(
				'success',
				__( 'Settings Saved', 'kadence-settings' ),
				{ type: 'snackbar' },
			);
		} );
	}
	render() {
		const { sections, tabs } = this.state;
		const real_tabs = [];
		{ Object.keys( tabs ).map( function( key, index ) {
			real_tabs.push( {
				name: tabs[ key ].id,
				title: tabs[ key ].title,
				className: 'kadence-settings-tab-' + tabs[ key ].id,
			} );
		} ) }
		 const KadenceDashTabPanel = () => (
			<TabPanel className="kadence-dashboard-tab-panel"
				 activeClass="active-tab"
				 tabs={ real_tabs }>
				 {
					 ( tab ) => {
						return (
							<Panel className="dashboard-section tab-section">
								<PanelBody
									opened={ true }
								>
									<div className="dashboard-modules-wrapper">
										<InfoPanel panel={ tab.name } sections={ sections } saveSettings={ () => this.saveSettings() } />
									</div>
								</PanelBody>
							</Panel>
						);
					 }
				 }
			 </TabPanel>
		 );
 
		 const MainPanel = () => (
			 <div className="tab-panel">
				{ real_tabs.length ?
					<KadenceDashTabPanel />
				:
					<Panel className="dashboard-section tab-section">
						<PanelBody
							opened={ true }
						>
							<div className="dashboard-modules-wrapper">
								<InfoPanel panel={ 'settings' } sections={ sections } saveSettings={ () => this.saveSettings() } />
							</div>
						</PanelBody>
					</Panel>
				}
			 </div>
		 );
 
		 return (
			 <Fragment>
				<MainPanel />
				<SaveOverlay show={ this.state.isSaving } />
				<Notices />
			 </Fragment>
		 );
	 }
 }
 
 wp.domReady( () => {
	 render(
		 <KadenceSettingsDashboard />,
		 document.querySelector( '.kadence_settings_dashboard_main' )
	 );
 } );
 