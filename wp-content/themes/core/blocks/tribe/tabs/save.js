import { InnerBlocks, useBlockProps } from '@wordpress/block-editor';
import { __ } from '@wordpress/i18n';

export default function save( props ) {
	const blockProps = {
		...useBlockProps.save(),
		'data-js': 'tabs-block',
	};
	const {
		attributes: { tabs },
	} = props;

	// TODO: add control for accessible label

	return (
		<section { ...blockProps }>
			<div className="wp-block-tribe-tabs__tab-nav">
				<div className="wp-block-tribe-tabs__tab-list" role="tablist">
					{ tabs.map( ( tab, index ) => {
						return (
							<button
								key={ tab.id }
								id={ tab.buttonId }
								type="button"
								className="wp-block-tribe-tabs__tab-item-button"
								aria-controls={ tab.id }
								role="tab"
								aria-selected={ index === 0 ? 'true' : 'false' }
								tabIndex={ index === 0 ? '-1' : false }
							>
								{ tab.label !== ''
									? tab.label
									: __( 'Tab Label', 'tribe' ) }
							</button>
						);
					} ) }
				</div>
			</div>
			<div className="tab-content">
				<InnerBlocks.Content />
			</div>
		</section>
	);
}
