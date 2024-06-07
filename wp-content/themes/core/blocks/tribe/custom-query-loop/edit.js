import { __ } from '@wordpress/i18n';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import {
	Button,
	ButtonGroup,
	FormTokenField,
	PanelBody,
	RangeControl,
	SelectControl,
	TextControl,
	ToggleControl,
} from '@wordpress/components';
import { withSelect } from '@wordpress/data';
import ServerSideRender from '@wordpress/server-side-render';

import './editor.pcss';

const CustomQueryLoopEdit = ( {
	props,
	postTypesList,
	taxonomiesList,
	taxonomyTerms,
	postInPosts,
} ) => {
	const blockProps = useBlockProps();
	const { attributes, isSelected, setAttributes } = props;
	const {
		columns,
		component,
		hasMetaQuery,
		hasPagination,
		hasTaxQuery,
		offset,
		order,
		orderby,
		postsPerPage,
		postIn,
		postTypes,
		taxQueryTaxonomies,
		taxQueryFields,
		taxQueryTerms,
		taxQueryOperators,
		taxQueryRelation,
		metaQueryKeys,
		metaQueryValues,
		metaQueryCompares,
		metaQueryTypes,
		metaQueryRelation,
	} = attributes;

	/**
	 * @function setPostIn
	 *
	 * @description handles setting post in setting values
	 *
	 * @param {*} selectedPosts
	 */
	const setPostIn = ( selectedPosts ) => {
		const matchingPosts = selectedPosts.map( ( selectedPost ) => {
			// if we've already added a value, it will appear as an object
			// in this case, we can just return the existing object
			if ( typeof selectedPost !== 'string' ) {
				return selectedPost;
			}

			// loop through post types array & posts
			for ( let i = 0; i < postTypes.length; i++ ) {
				for (
					let j = 0;
					j < postInPosts[ postTypes[ i ].slug ].length;
					j++
				) {
					if (
						selectedPost ===
						postInPosts[ postTypes[ i ].slug ][ j ].title.rendered
					) {
						return {
							id: postInPosts[ postTypes[ i ].slug ][ j ].id,
							value: postInPosts[ postTypes[ i ].slug ][ j ].title
								.rendered,
						};
					}
				}
			}

			return false;
		} );

		setAttributes( {
			postIn: matchingPosts,
		} );
	};

	/**
	 * @function removeMetaQueryByIndex
	 *
	 * @description removes meta query item via given index
	 *
	 * @param {*} index
	 */
	const removeMetaQueryByIndex = ( index ) => {
		const newMetaQueryKeys = [ ...metaQueryKeys ];
		const newMetaQueryValues = [ ...metaQueryValues ];
		const newMetaQueryCompares = [ ...metaQueryCompares ];
		const newMetaQueryTypes = [ ...metaQueryTypes ];

		newMetaQueryKeys.splice( index, 1 );
		newMetaQueryValues.splice( index, 1 );
		newMetaQueryCompares.splice( index, 1 );
		newMetaQueryTypes.splice( index, 1 );

		setAttributes( {
			metaQueryKeys: newMetaQueryKeys,
			metaQueryValues: newMetaQueryValues,
			metaQueryCompares: newMetaQueryCompares,
			metaQueryTypes: newMetaQueryTypes,
		} );
	};

	/**
	 * @function addMetaQuery
	 *
	 * @description adds a meta query to the array; renders new controls based on new length
	 */
	const addMetaQuery = () => {
		const newMetaQueryKeys = [ ...metaQueryKeys ];
		const newMetaQueryValues = [ ...metaQueryValues ];
		const newMetaQueryCompares = [ ...metaQueryCompares ];
		const newMetaQueryTypes = [ ...metaQueryTypes ];

		newMetaQueryKeys.push( '' );
		newMetaQueryValues.push( '' );
		newMetaQueryCompares.push( '=' );
		newMetaQueryTypes.push( 'CHAR' );

		setAttributes( {
			metaQueryKeys: newMetaQueryKeys,
			metaQueryValues: newMetaQueryValues,
			metaQueryCompares: newMetaQueryCompares,
			metaQueryTypes: newMetaQueryTypes,
		} );
	};

	/**
	 * @function setTaxonomyTermsByIndex
	 *
	 * @description creates terms array for saving
	 *
	 * @param {*} selectedTerms
	 * @param {*} taxonomy
	 * @param {*} index
	 */
	const setTaxonomyTermsByIndex = ( selectedTerms, taxonomy, index ) => {
		const newTaxQueryTerms = [ ...taxQueryTerms ];
		const newTerms = selectedTerms.map( ( selectedTerm ) => {
			// if we've already added a value, it will appear as an object
			// in this case, we can just return the existing object
			if ( typeof selectedTerm !== 'string' ) {
				return selectedTerm;
			}

			// if this is a new value it will appear as a string so we'll need to grab
			// the term values via the name the name is provided via the Suggestions
			// array in the FormTokenField component
			for ( let i = 0; i < taxonomyTerms[ taxonomy ].length; i++ ) {
				if ( taxonomyTerms[ taxonomy ][ i ].name === selectedTerm ) {
					return {
						name: selectedTerm,
						term_id: taxonomyTerms[ taxonomy ][ i ].id,
						slug: taxonomyTerms[ taxonomy ][ i ].slug,
						value: selectedTerm,
					};
				}
			}

			return false;
		} );

		newTaxQueryTerms[ index ] = JSON.stringify( newTerms );

		setAttributes( {
			taxQueryTerms: newTaxQueryTerms,
		} );
	};

	/**
	 * @function removeTaxQueryByIndex
	 *
	 * @description removes tax query item via given index
	 *
	 * @param {*} index
	 */
	const removeTaxQueryByIndex = ( index ) => {
		const newTaxQueryTaxonomies = [ ...taxQueryTaxonomies ];
		const newTaxQueryFields = [ ...taxQueryFields ];
		const newTaxQueryTerms = [ ...taxQueryTerms ];
		const newTaxQueryOperators = [ ...taxQueryOperators ];

		newTaxQueryTaxonomies.splice( index, 1 );
		newTaxQueryFields.splice( index, 1 );
		newTaxQueryTerms.splice( index, 1 );
		newTaxQueryOperators.splice( index, 1 );

		setAttributes( {
			taxQueryTaxonomies: newTaxQueryTaxonomies,
			taxQueryFields: newTaxQueryFields,
			taxQueryTerms: newTaxQueryTerms,
			taxQueryOperators: newTaxQueryOperators,
		} );
	};

	/**
	 * @function addTaxQuery
	 *
	 * @description adds a taxonomy query to the array; renders new controls based on new length
	 */
	const addTaxQuery = () => {
		const newTaxQueryTaxonomies = [ ...taxQueryTaxonomies ];
		const newTaxQueryFields = [ ...taxQueryFields ];
		const newTaxQueryTerms = [ ...taxQueryTerms ];
		const newTaxQueryOperators = [ ...taxQueryOperators ];

		newTaxQueryTaxonomies.push( 'category' );
		newTaxQueryFields.push( 'term_id' );
		newTaxQueryTerms.push( '[]' );
		newTaxQueryOperators.push( 'IN' );

		setAttributes( {
			taxQueryTaxonomies: newTaxQueryTaxonomies,
			taxQueryFields: newTaxQueryFields,
			taxQueryTerms: newTaxQueryTerms,
			taxQueryOperators: newTaxQueryOperators,
		} );
	};

	/**
	 * @function setPostTypes
	 *
	 * @description creates post types array for saving
	 *
	 * @param {*} selectedPostTypes
	 */
	const setPostTypes = ( selectedPostTypes ) => {
		const newPostTypes = selectedPostTypes.map( ( selectedPostType ) => {
			// if we've already added a value, it will appear as an object
			// in this case, we can just return the existing object
			if ( typeof selectedPostType !== 'string' ) {
				return selectedPostType;
			}

			// if this is a new value it will appear as a string so we'll need to grab
			// the post type values via the name the name is provided via the Suggestions
			// array in the FormTokenField component
			for ( let i = 0; i < postTypesList.length; i++ ) {
				if ( postTypesList[ i ].name === selectedPostType ) {
					return {
						name: selectedPostType,
						slug: postTypesList[ i ].slug,
						value: selectedPostType,
					};
				}
			}

			return false;
		} );

		setAttributes( {
			postTypes: newPostTypes,
		} );
	};

	/**
	 * @function postInSuggestions
	 *
	 * @description grabs post suggestions from provided post types
	 *
	 * @return {Array} array of suggestions
	 */
	const postInSuggestions = () => {
		const suggestions = [];

		if ( postTypes !== null && postTypes.length > 0 ) {
			for ( let i = 0; i < postTypes.length; i++ ) {
				if ( postInPosts[ postTypes[ i ].slug ] !== null ) {
					for (
						let j = 0;
						j < postInPosts[ postTypes[ i ].slug ].length;
						j++
					) {
						suggestions.push(
							postInPosts[ postTypes[ i ].slug ][ j ].title
								.rendered
						);
					}
				}
			}
		}

		return suggestions;
	};

	/**
	 * @function getTaxonomyTermSuggestions
	 *
	 * @description grabs term suggestions from provided taxonomy
	 *
	 * @param {*} taxonomy
	 *
	 * @return {Array} returns array of related term names
	 */
	const getTaxonomyTermSuggestions = ( taxonomy ) => {
		return taxonomyTerms !== null &&
			taxonomyTerms[ taxonomy ] !== undefined &&
			taxonomyTerms[ taxonomy ] !== null
			? taxonomyTerms[ taxonomy ].map( ( term ) => {
					return term.name;
			  } )
			: [];
	};

	/**
	 * @function postTypeSuggestions
	 *
	 * @description used to populate the post types FormTokenField suggestions
	 *
	 * @return {Array} returns array of post type names
	 */
	const postTypeSuggestions = () => {
		return postTypesList !== null
			? postTypesList.map( ( postType ) => {
					return postType.name;
			  } )
			: [];
	};

	return (
		<div { ...blockProps }>
			<ServerSideRender
				block="tribe/custom-query-loop"
				attributes={ props.attributes }
			/>
			{ isSelected && (
				<InspectorControls>
					<PanelBody title={ __( 'Block Settings', 'tribe' ) }>
						<SelectControl
							__nextHasNoMarginBottom={ true }
							__next40pxDefaultSize={ true }
							label={ __( 'Card Component', 'tribe' ) }
							help={ __(
								'The card component to be used in the query loop',
								'tribe'
							) }
							value={ component }
							options={ [
								{ value: 'post', label: 'Post' },
								{ value: 'search', label: 'Search' },
							] }
							onChange={ ( value ) => {
								setAttributes( {
									component: value,
								} );
							} }
						/>
						<RangeControl
							__nextHasNoMarginBottom={ true }
							__next40pxDefaultSize={ true }
							label={ __( 'Number of Columns', 'tribe' ) }
							help={ __(
								'The number of columns to display the selected components in.'
							) }
							value={ columns }
							initialPosition={ columns }
							max={ 4 }
							min={ 1 }
							onChange={ ( value ) => {
								setAttributes( {
									columns: value,
								} );
							} }
						/>
						<FormTokenField
							__next40pxDefaultSize={ true }
							label={ __( 'Post Types', 'tribe' ) }
							value={ postTypes }
							suggestions={ postTypeSuggestions() }
							onChange={ ( tokens ) => {
								setPostTypes( tokens );
							} }
						/>
						<TextControl
							__nextHasNoMarginBottom={ true }
							__next40pxDefaultSize={ true }
							label={ __( 'Posts Per Page', 'tribe' ) }
							help={ __(
								'The number of posts to show per page. Use "-1" to show all posts.'
							) }
							type="text"
							value={ postsPerPage }
							onChange={ ( value ) => {
								setAttributes( {
									postsPerPage: value,
								} );
							} }
						/>
						{ postTypes !== null && postTypes.length > 0 ? (
							<FormTokenField
								__next40pxDefaultSize={ true }
								label={ __( 'Include Posts', 'tribe' ) }
								value={ postIn }
								suggestions={ postInSuggestions() }
								maxSuggestions={ 20 }
								onChange={ ( tokens ) => {
									setPostIn( tokens );
								} }
							/>
						) : (
							''
						) }
						<TextControl
							__nextHasNoMarginBottom={ true }
							__next40pxDefaultSize={ true }
							label={ __( 'Offset', 'tribe' ) }
							help={ __(
								'The number of post to displace or pass over. This parameter is ignored if Posts Per Page is set to "-1".'
							) }
							type="text"
							value={ offset }
							onChange={ ( value ) => {
								setAttributes( {
									offset: value,
								} );
							} }
						/>
						<SelectControl
							__nextHasNoMarginBottom={ true }
							__next40pxDefaultSize={ true }
							label={ __( 'Order By', 'tribe' ) }
							help={ __(
								'Sort retrieved posts by parameter.',
								'tribe'
							) }
							value={ orderby }
							options={ [
								{ value: 'title', label: 'Title' },
								{ value: 'date', label: 'Date' },
								{
									value: 'modified',
									label: 'Last Modified Date',
								},
								{ value: 'rand', label: 'Random' },
								{ value: 'menu_order', label: 'Post Order' },
								{ value: 'post__in', label: 'Post In Array' },
							] }
							onChange={ ( value ) => {
								setAttributes( {
									orderby: value,
								} );
							} }
						/>
						<SelectControl
							__nextHasNoMarginBottom={ true }
							__next40pxDefaultSize={ true }
							label={ __( 'Order', 'tribe' ) }
							help={ __(
								'Designates the ascending or descending order of the orderby parameter.',
								'tribe'
							) }
							value={ order }
							options={ [
								{ value: 'ASC', label: 'Ascending' },
								{ value: 'DESC', label: 'Decending' },
							] }
							onChange={ ( value ) => {
								setAttributes( {
									order: value,
								} );
							} }
						/>
						<ToggleControl
							label={ __( 'Use Pagination?', 'tribe' ) }
							help={ __(
								'Enables pagination component for this query block.',
								'tribe'
							) }
							checked={ hasPagination }
							onChange={ ( value ) => {
								setAttributes( {
									hasPagination: value,
								} );
							} }
						/>
						<div>
							<ToggleControl
								label={ __( 'Use Taxonomy Query?', 'tribe' ) }
								help={ __(
									'Enables taxonomy queries for this instance',
									'tribe'
								) }
								checked={ hasTaxQuery }
								onChange={ ( value ) => {
									setAttributes( {
										hasTaxQuery: value,
									} );
								} }
							/>
							{ hasTaxQuery ? (
								<>
									{ taxQueryTaxonomies.length > 1 ? (
										<SelectControl
											__nextHasNoMarginBottom={ true }
											__next40pxDefaultSize={ true }
											label={ __(
												'Taxonomy Query Relation',
												'tribe'
											) }
											help={ __(
												'Determines the logical relationship between each inner taxonomy array when there is more than one.',
												'tribe'
											) }
											value={ taxQueryRelation }
											options={ [
												{ value: 'AND', label: 'AND' },
												{ value: 'OR', label: 'OR' },
											] }
											onChange={ ( value ) => {
												setAttributes( {
													taxQueryRelation: value,
												} );
											} }
										/>
									) : (
										''
									) }
									{ taxQueryTaxonomies.map(
										( taxonomy, index ) => {
											return (
												<div
													key={ 'tax-query-' + index }
													style={ {
														borderLeft:
															'2px solid var(--wp-components-color-accent, var(--wp-admin-theme-color, #3858e9))',
														paddingLeft: '10px',
														marginBottom: '24px',
													} }
												>
													<SelectControl
														__nextHasNoMarginBottom={
															true
														}
														__next40pxDefaultSize={
															true
														}
														label={ __(
															'Taxonomy',
															'tribe'
														) }
														value={ taxonomy }
														options={
															taxonomiesList !==
															null
																? taxonomiesList.map(
																		(
																			listTaxonomy
																		) => {
																			return {
																				value: listTaxonomy.slug,
																				label: listTaxonomy.name,
																			};
																		}
																  )
																: []
														}
														onChange={ (
															value
														) => {
															const newTaxQueryTaxonomies =
																[
																	...taxQueryTaxonomies,
																];

															newTaxQueryTaxonomies[
																index
															] = value;

															setAttributes( {
																taxQueryTaxonomies:
																	newTaxQueryTaxonomies,
															} );
														} }
													/>
													<SelectControl
														__nextHasNoMarginBottom={
															true
														}
														__next40pxDefaultSize={
															true
														}
														label={ __(
															'Field',
															'tribe'
														) }
														value={
															taxQueryFields[
																index
															]
														}
														options={ [
															{
																value: 'term_id',
																label: 'Term ID',
															},
															{
																value: 'name',
																label: 'Name',
															},
															{
																value: 'slug',
																label: 'Slug',
															},
														] }
														onChange={ (
															value
														) => {
															const newTaxQueryFields =
																[
																	...taxQueryFields,
																];

															newTaxQueryFields[
																index
															] = value;

															setAttributes( {
																taxQueryFields:
																	newTaxQueryFields,
															} );
														} }
													/>
													<FormTokenField
														__next40pxDefaultSize={
															true
														}
														label={ __(
															'Terms',
															'tribe'
														) }
														value={ JSON.parse(
															taxQueryTerms[
																index
															]
														) }
														suggestions={ getTaxonomyTermSuggestions(
															taxonomy
														) }
														onChange={ (
															tokens
														) => {
															setTaxonomyTermsByIndex(
																tokens,
																taxQueryTaxonomies[
																	index
																],
																index
															);
														} }
													/>
													<SelectControl
														__nextHasNoMarginBottom={
															true
														}
														__next40pxDefaultSize={
															true
														}
														label={ __(
															'Operator',
															'tribe'
														) }
														value={
															taxQueryOperators[
																index
															]
														}
														options={ [
															{
																value: 'IN',
																label: 'IN',
															},
															{
																value: 'NOT IN',
																label: 'NOT IN',
															},
															{
																value: 'AND',
																label: 'AND',
															},
															{
																value: 'EXISTS',
																label: 'EXISTS',
															},
															{
																value: 'NOT EXISTS',
																label: 'NOT EXISTS',
															},
														] }
														onChange={ (
															value
														) => {
															const newTaxQueryOperators =
																[
																	...taxQueryOperators,
																];

															newTaxQueryOperators[
																index
															] = value;

															setAttributes( {
																taxQueryOperators:
																	newTaxQueryOperators,
															} );
														} }
													/>
													{ taxQueryTaxonomies.length >
													1 ? (
														<ButtonGroup>
															<Button
																__next40pxDefaultSize={
																	true
																}
																variant="secondary"
																isDestructive={
																	true
																}
																onClick={ () =>
																	removeTaxQueryByIndex(
																		index
																	)
																}
															>
																{ __(
																	'Remove Taxonomy',
																	'tribe'
																) }
															</Button>
														</ButtonGroup>
													) : (
														''
													) }
												</div>
											);
										}
									) }
									<ButtonGroup>
										<Button
											__next40pxDefaultSize={ true }
											variant="primary"
											onClick={ () => addTaxQuery() }
										>
											{ __( 'Add Taxonomy', 'tribe' ) }
										</Button>
									</ButtonGroup>
								</>
							) : (
								''
							) }
						</div>
						<div style={ { marginTop: '24px' } }>
							<ToggleControl
								label={ __( 'Use Meta Query?', 'tribe' ) }
								help={ __(
									'Enables meta queries for this instance',
									'tribe'
								) }
								checked={ hasMetaQuery }
								onChange={ ( value ) => {
									setAttributes( {
										hasMetaQuery: value,
									} );
								} }
							/>
							{ hasMetaQuery ? (
								<>
									{ metaQueryKeys.length > 1 ? (
										<SelectControl
											__nextHasNoMarginBottom={ true }
											__next40pxDefaultSize={ true }
											label={ __(
												'Meta Query Relation',
												'tribe'
											) }
											help={ __(
												'Determines the logical relationship between each inner meta_query array when there is more than one.',
												'tribe'
											) }
											value={ metaQueryRelation }
											options={ [
												{ value: 'AND', label: 'AND' },
												{ value: 'OR', label: 'OR' },
											] }
											onChange={ ( value ) => {
												setAttributes( {
													metaQueryRelation: value,
												} );
											} }
										/>
									) : (
										''
									) }
									{ metaQueryKeys.map( ( key, index ) => {
										return (
											<div
												key={ 'meta-query-' + index }
												style={ {
													borderLeft:
														'2px solid var(--wp-components-color-accent, var(--wp-admin-theme-color, #3858e9))',
													paddingLeft: '10px',
													marginBottom: '24px',
												} }
											>
												<TextControl
													__nextHasNoMarginBottom={
														true
													}
													__next40pxDefaultSize={
														true
													}
													label={ __(
														'Key',
														'tribe'
													) }
													value={ key }
													onChange={ ( value ) => {
														const newMetaQueryKeys =
															[
																...metaQueryKeys,
															];

														newMetaQueryKeys[
															index
														] = value;

														setAttributes( {
															metaQueryKeys:
																newMetaQueryKeys,
														} );
													} }
												/>
												<TextControl
													__nextHasNoMarginBottom={
														true
													}
													__next40pxDefaultSize={
														true
													}
													label={ __(
														'Value',
														'tribe'
													) }
													help={ __(
														'This field only supports strings'
													) }
													value={
														metaQueryValues[ index ]
													}
													onChange={ ( value ) => {
														const newMetaQueryValues =
															[
																...metaQueryValues,
															];

														newMetaQueryValues[
															index
														] = value;

														setAttributes( {
															metaQueryValues:
																newMetaQueryValues,
														} );
													} }
												/>
												<SelectControl
													__nextHasNoMarginBottom={
														true
													}
													__next40pxDefaultSize={
														true
													}
													label={ __(
														'Compare',
														'tribe'
													) }
													value={
														metaQueryCompares[
															index
														]
													}
													options={ [
														{
															value: '=',
															label: '=',
														},
														{
															value: '!=',
															label: '!=',
														},
														{
															value: '>',
															label: '>',
														},
														{
															value: '>=',
															label: '>=',
														},
														{
															value: '<',
															label: '<',
														},
														{
															value: '<=',
															label: '<=',
														},
														{
															value: 'LIKE',
															label: 'LIKE',
														},
														{
															value: 'NOT LIKE',
															label: 'NOT LIKE',
														},
														{
															value: 'IN',
															label: 'IN',
														},
														{
															value: 'NOT IN',
															label: 'NOT IN',
														},
														{
															value: 'BETWEEN',
															label: 'BETWEEN',
														},
														{
															value: 'NOT BETWEEN',
															label: 'NOT BETWEEN',
														},
														{
															value: 'EXISTS',
															label: 'EXISTS',
														},
														{
															value: 'NOT EXISTS',
															label: 'NOT EXISTS',
														},
													] }
													onChange={ ( value ) => {
														const newMetaQueryCompares =
															[
																...metaQueryCompares,
															];

														newMetaQueryCompares[
															index
														] = value;

														setAttributes( {
															metaQueryCompares:
																newMetaQueryCompares,
														} );
													} }
												/>
												<SelectControl
													__nextHasNoMarginBottom={
														true
													}
													__next40pxDefaultSize={
														true
													}
													label={ __(
														'Type',
														'tribe'
													) }
													value={
														metaQueryTypes[ index ]
													}
													options={ [
														{
															value: 'NUMERIC',
															label: 'NUMERIC',
														},
														{
															value: 'BINARY',
															label: 'BINARY',
														},
														{
															value: 'CHAR',
															label: 'CHAR',
														},
														{
															value: 'DATE',
															label: 'DATE',
														},
														{
															value: 'DATETIME',
															label: 'DATETIME',
														},
														{
															value: 'DECIMAL',
															label: 'DECIMAL',
														},
														{
															value: 'SIGNED',
															label: 'SIGNED',
														},
														{
															value: 'TIME',
															label: 'TIME',
														},
														{
															value: 'UNSIGNED',
															label: 'UNSIGNED',
														},
													] }
													onChange={ ( value ) => {
														const newMetaQueryTypes =
															[
																...metaQueryTypes,
															];

														newMetaQueryTypes[
															index
														] = value;

														setAttributes( {
															metaQueryTypes:
																newMetaQueryTypes,
														} );
													} }
												/>
												{ metaQueryKeys.length > 1 ? (
													<ButtonGroup>
														<Button
															__next40pxDefaultSize={
																true
															}
															variant="secondary"
															isDestructive={
																true
															}
															onClick={ () =>
																removeMetaQueryByIndex(
																	index
																)
															}
														>
															{ __(
																'Remove Meta',
																'tribe'
															) }
														</Button>
													</ButtonGroup>
												) : (
													''
												) }
											</div>
										);
									} ) }
									<ButtonGroup>
										<Button
											__next40pxDefaultSize={ true }
											variant="primary"
											onClick={ () => addMetaQuery() }
										>
											{ __( 'Add Meta', 'tribe' ) }
										</Button>
									</ButtonGroup>
								</>
							) : (
								''
							) }
						</div>
					</PanelBody>
				</InspectorControls>
			) }
		</div>
	);
};

export default withSelect( ( select, ownProps ) => {
	const { getEntityRecords, getPostTypes, getTaxonomies } = select( 'core' );
	const { attributes } = ownProps;
	const { postTypes } = attributes;
	let taxonomies = getTaxonomies();
	const taxonomyTerms = {};
	const posts = {};

	/**
	 * determine what taxonomies our block should get
	 * this is based off of the selected post types. If there is no post type
	 * selected or if the site has no taxonomies, this block of code doesn't run.
	 */
	if (
		taxonomies !== null &&
		taxonomies.length > 0 &&
		postTypes !== null &&
		postTypes.length > 0
	) {
		taxonomies = taxonomies.filter( ( taxonomy ) => {
			// loop through selected post types and check against current taxonomy
			for ( let i = 0; i < postTypes.length; i++ ) {
				if ( taxonomy.types.includes( postTypes[ i ].slug ) ) {
					return true;
				}
			}

			return false;
		} );
	}

	if ( taxonomies !== null && taxonomies.length > 0 ) {
		for ( let i = 0; i < taxonomies.length; i++ ) {
			taxonomyTerms[ taxonomies[ i ].slug ] = getEntityRecords(
				'taxonomy',
				taxonomies[ i ].slug
			);
		}
	}

	/* get posts based on post types set */
	if ( postTypes !== null && postTypes.length > 0 ) {
		for ( let i = 0; i < postTypes.length; i++ ) {
			posts[ postTypes[ i ].slug ] = getEntityRecords(
				'postType',
				postTypes[ i ].slug,
				{ per_page: -1 }
			);
		}
	}

	return {
		props: ownProps,
		postTypesList: getPostTypes(),
		taxonomiesList: taxonomies,
		taxonomyTerms,
		postInPosts: posts,
	};
} )( CustomQueryLoopEdit );
