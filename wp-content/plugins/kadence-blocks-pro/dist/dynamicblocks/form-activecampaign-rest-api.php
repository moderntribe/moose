<?php
/**
 * REST API ActiveCampaign controller customized for Kadence Form
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * REST API ActiveCampaign controller class.
 */
class KBP_ActiveCampaign_REST_Controller extends WP_REST_Controller {

	/**
	 * Include property name.
	 */
	const PROP_END_POINT = 'endpoint';

	/**
	 * Per page property name.
	 */
	const PROP_QUERY_ARGS = 'queryargs';


	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->namespace = 'kb-activecampaign/v1';
		$this->rest_base = 'get';
	}

	/**
	 * Registers the routes for the objects of the controller.
	 *
	 * @see register_rest_route()
	 */
	public function register_routes() {
		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base,
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_items' ),
					'permission_callback' => array( $this, 'get_items_permission_check' ),
					'args'                => $this->get_collection_params(),
				),
			)
		);
	}
	/**
	 * Checks if a given request has access to search content.
	 *
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return true|WP_Error True if the request has search access, WP_Error object otherwise.
	 */
	public function get_items_permission_check( $request ) {
		return current_user_can( 'edit_posts' );
	}

	/**
	 * Retrieves a collection of objects.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
	 */
	public function get_items( $request ) {
		$api_key    = get_option( 'kadence_blocks_activecampaign_api_key' );
		$api_base   = get_option( 'kadence_blocks_activecampaign_api_base' );
		$end_point  = $request->get_param( self::PROP_END_POINT );
		$query_args = $request->get_param( self::PROP_QUERY_ARGS );

		if ( empty( $api_key ) ) {
			return array();
		}
		if ( empty( $api_base ) ) {
			return array();
		}
		$active_campaign = new KBP_Active_Campaign( $api_base, $api_key );
		if ( $query_args && is_array( $query_args ) ) {
			$args = array();
			foreach ( $query_args as $key => $value ) {
				$value_parts = explode( '=', $value );
				$args[ $value_parts[0] ] = $value_parts[1];
			}
		}
		switch ( $end_point ) {
			case 'lists':
				$limit    = ( ! empty( $args['limit'] ) ? $args['limit'] : 200 );
				$search   = ( ! empty( $args['search'] ) ? $args['search'] : null );
				$response = $active_campaign->get_all_lists( $limit, $search );
				break;
			case 'tags':
				$search   = ( ! empty( $args['search'] ) ? $args['search'] : null );
				$response = $active_campaign->get_all_tags( $search );
				break;
			case 'fields':
				$limit    = ( ! empty( $args['limit'] ) ? $args['limit'] : 200 );
				$search   = ( ! empty( $args['search'] ) ? $args['search'] : null );
				$response = $active_campaign->get_all_fields( $limit, $search );
				break;
		}
		if ( ! empty( $response ) && is_array( $response ) ) {
			return $response;
		}
		return array();

	}
	/**
	 * Retrieves the query params for the search results collection.
	 *
	 * @return array Collection parameters.
	 */
	public function get_collection_params() {
		$query_params  = parent::get_collection_params();

		$query_params[ self::PROP_END_POINT ] = array(
			'description' => __( 'Actionable endpoint for api call.', 'kadence-blocks-pro' ),
			'type'        => 'string',
		);

		$query_params[ self::PROP_QUERY_ARGS ] = array(
			'description' => __( 'Query Args for url.', 'kadence-blocks-pro' ),
			'type'        => 'array',
		);

		return $query_params;
	}
}
