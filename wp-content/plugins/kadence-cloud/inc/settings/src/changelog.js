/**
 * WordPress dependencies
 */
 const { __ } = wp.i18n;
 const { Fragment } = wp.element;
 const { withFilters } = wp.components;
 const { TabPanel, Panel, PanelBody } = wp.components;
 import ChangelogItem from './changelog-item';
 
 export const ChangelogTab = () => {
	 const tabs = [
		 {
			 name: 'kadence',
			 title: __( 'Changelog', 'kadence-settings' ),
			 className: 'kadence-changelog-tab',
		 },
		 {
			 name: 'pro',
			 title: __( 'Pro Changelog', 'kadence-settings' ),
			 className: 'kadence-pro-changelog-tab',
		 },
	 ];
	 return (
		<Fragment>
			{ kadenceSettingsParams.changelog && (
				<Fragment>
					{ kadenceSettingsParams.proChangelog && Array.isArray( kadenceSettingsParams.proChangelog ) && 0 !== kadenceSettingsParams.proChangelog.length && (
						<TabPanel
						 	className="kadence-dashboard-changelog-tab-panel kadence-settings-dashboard-section-tabs"
							activeClass="active-tab"
							orientation="vertical"
							tabs={ tabs }>
							{
								 ( tab ) => {
									 switch ( tab.name ) {
										 case 'kadence':
											 return (
												 <Panel className="kadence-changelog-section tab-section">
													 <PanelBody
														 opened={ true }
													 >
														 { kadenceSettingsParams.changelog.map( ( item, index ) => {
															 return <ChangelogItem
																 item={ item }
																 index={ item }
															 />;
														 } ) }
													 </PanelBody>
												 </Panel>
											 );
 
										 case 'pro':
											 return (
												 <Panel className="pro-changelog-section tab-section">
													 <PanelBody
														 opened={ true }
													 >
														 { kadenceSettingsParams.proChangelog.map( ( item, index ) => {
															 return <ChangelogItem
																 item={ item }
																 index={ item }
															 />;
														 } ) }
													 </PanelBody>
												 </Panel>
											 );
									 }
								 }
							}
						</TabPanel>
					) }
					{ ( '' == kadenceSettingsParams.proChangelog || ( Array.isArray( kadenceSettingsParams.proChangelog ) && ! kadenceSettingsParams.proChangelog.length ) ) && (
						 <Fragment>
							{ kadenceSettingsParams.changelog.map( ( item, index ) => {
								 return <ChangelogItem
									 item={ item }
									 index={ item }
								 />;
							} ) }
						</Fragment>
					) }
				</Fragment>
			) }
		</Fragment>
	);
};
 
 export default withFilters( 'kadence_settings_changelog' )( ChangelogTab );