/**
 * WordPress dependencies
 */
 const { __ } = wp.i18n;
 const { Fragment } = wp.element;
 const { withFilters } = wp.components;
 const { TabPanel, Panel, PanelBody } = wp.components;
 
 import kadenceTryParseJSON from './components/common/try-parse';
 export const DashTab = () => {
	const dash = ( kadenceSettingsParams.dash ? kadenceTryParseJSON( kadenceSettingsParams.dash ) : {} );
	 return (
		<Fragment>
			{ dash && (
				<div className="kadence-desk-help-inner">
					{ dash.title && (
						<h2>{ dash.title }</h2>
					) }
					{ dash.description && (
						<p>{ dash.description }</p>
					) }
					{ dash.video_url && (
						<div className="video-container">
							<iframe width="560" height="315" src={ dash.video_url } frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"></iframe>
						</div>
					) }
					{ dash.link_url && dash.link_text && (
						<a href={ dash.link_url } className="kadence-desk-button kadence-desk-button-second" target="_blank">{ dash.link_text }</a>
					) }
				</div>
			) }
		</Fragment>
	);
};
 
 export default withFilters( 'kadence_settings_dash' )( DashTab );