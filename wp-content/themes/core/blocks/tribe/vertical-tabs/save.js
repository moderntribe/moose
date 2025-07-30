import {
	RichText,
	useBlockProps,
	useInnerBlocksProps,
} from '@wordpress/block-editor';

export default function save( { attributes } ) {
	const blockProps = useBlockProps.save();
	const innerBlockProps = useInnerBlocksProps.save( {
		className: 'wp-block-tribe-vertical-tabs__tab-content',
	} );
	const { tabs } = attributes;

	return (
		<div { ...blockProps }>
			<div
				className="wp-block-tribe-vertical-tabs__tab-container"
				role="tablist"
			>
				{ tabs
					? tabs.map( ( tab, index ) => {
							return (
								<div
									key={ tab.id }
									id={ tab.buttonId }
									className="wp-block-tribe-vertical-tabs__tab"
									aria-controls={ tab.id }
									role="tab"
									aria-selected={
										index === 0 ? 'true' : 'false'
									}
									tabIndex={ index === 0 ? '-1' : false }
								>
									<RichText.Content
										tagName="h3"
										className="wp-block-tribe-vertical-tabs__tab-title t-display-xx-small s-remove-margin--top"
										value={ tab.title }
									/>
									<div className="wp-block-tribe-vertical-tabs__tab-hidden">
										<RichText.Content
											tagName="p"
											className="wp-block-tribe-vertical-tabs__tab-description"
											value={ tab.content }
										/>
										<div className="wp-block-tribe-vertical-tabs__buttons wp-block-buttons">
											<span className="wp-block-button is-style-ghost tribe-button-has-icon">
												<a
													href={ tab.linkUrl }
													className="wp-block-button__link wp-element-button"
												>
													{ tab.linkText }
												</a>
											</span>
										</div>
									</div>
								</div>
							);
					  } )
					: '' }
			</div>
			<div { ...innerBlockProps } />
		</div>
	);
}
