<?php
/**
 * REST API Kadence Starter Templates Rest.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * REST API Starter controller class.
 */
class Kadence_Cloud_Rest_Controller extends WP_REST_Controller {

	/**
	 * Include property name.
	 */
	const PROP_KEY = 'key';

	/**
	 * Include property name.
	 */
	const PROP_SITE = 'site';

		/**
	 * Include property name.
	 */
	const PROP_RETURN_COUNT = 'items';

	/**
	 * Include property name.
	 */
	const PROP_RETURN_PAGE = 'page';

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->namespace = 'kadence-cloud/v1';
		$this->rest_base = 'get';
		$this->info_base = 'info';
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
					'permission_callback' => '__return_true',
					'args'                => $this->get_collection_params(),
				),
			)
		);
		register_rest_route(
			$this->namespace,
			'/' . $this->info_base,
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_info' ),
					'permission_callback' => '__return_true',
					'args'                => $this->get_collection_params(),
				),
			)
		);
	}

	/**
	 * Retrieves a collection of objects.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
	 */
	public function get_info( $request ) {
		if ( $this->check_access( $request ) ) {
			$settings = json_decode( get_option( 'kadence_cloud' ), true );
			if ( isset( $settings['expires'] ) && ! empty( $settings['expires'] ) ) {
				if ( 'day' === $settings['expires'] ) {
					$expires = DAY_IN_SECONDS;
				} elseif ( 'week' === $settings['expires'] ) {
					$expires = WEEK_IN_SECONDS;
				} else {
					$expires = MONTH_IN_SECONDS;
				}
			} else {
				$expires = MONTH_IN_SECONDS;
			}
			$key = $request->get_param( self::PROP_KEY );
			$info = array(
				'name'    => ( isset( $settings['cloud_name'] ) && ! empty( $settings['cloud_name'] ) ? wp_kses_post( $settings['cloud_name'] ) : wp_kses_post( get_bloginfo( 'name' ) ) ),
				'slug'    => sanitize_title( md5( get_bloginfo( 'url' ) . $key ) ),
				'refresh' => ( isset( $settings['expires'] ) && ! empty( $settings['expires'] ) ? wp_kses_post( $settings['expires'] ) : 'month' ),
				'expires' => gmdate( 'Y-m-d H:i:s', strtotime( current_time( 'mysql' ) ) + $expires ),
			);
			return wp_send_json( $info );

		} else {
			return wp_send_json( __( 'Invalid Request, Incorrect Access Key', 'kadence-cloud' ) );
		}
	}

	/**
	 * Retrieves a collection of objects.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
	 */
	public function get_items( $request ) {
		$key         = $request->get_param( self::PROP_KEY );
		$per_page    = $request->get_param( self::PROP_RETURN_COUNT );
		$page_number = $request->get_param( self::PROP_RETURN_PAGE );

		if ( $this->check_access( $request ) ) {
			$request_extras = apply_filters( 'kadence_cloud_rest_request_extras', array(), $request );
			if ( ! empty( $per_page ) ) {
				$per_page = absint( $per_page );
			} else {
				$per_page = -1;
			}
			if ( ! empty( $page_number ) ) {
				$page_number = absint( $page_number );
			} else {
				$page_number = 1;
			}
			return $this->get_templates( $per_page, $page_number, $key, $request_extras );

		} else {
			return wp_send_json( __( 'Invalid Request, Incorrect Access Key', 'kadence-cloud' ) );
		}
	}
	/**
	 * Check if the request should get access to the files.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return Boolean true or false based on if access should be granted.
	 */
	public function check_access( $request ) {
		$access = false;
		$key    = $request->get_param( self::PROP_KEY );
		$keys   = array();
		$settings = json_decode( get_option( 'kadence_cloud' ), true );
		if ( isset( $settings['access_keys'] ) && ! empty( $settings['access_keys'] ) && is_array( $settings['access_keys'] ) ) {
			$access_keys = array();
			if ( isset( $settings['access_keys'][0] ) && is_array( $settings['access_keys'][0] ) ) {
				foreach ( $settings['access_keys'] as $the_key => $the_keys ) {
					if ( $the_keys['key'] === $key ) {
						$access = true;
						break;
					}
				}
			} else {
				$access_keys = $settings['access_keys'];
				if ( is_array( $access_keys ) && ! empty( $access_keys ) && in_array( $key, $access_keys ) ) {
					$access = true;
				}
			}
		}
		return apply_filters( 'kadence_cloud_rest_request_access', $access, $key, $request );
	}
	/**
	 * Retrieves a collection of objects.
	 */
	public function get_template_array( $template_query, $request_extras ) {
		$library = array();
		$fallback_image_args = apply_filters(
			'kadence_cloud_post_fallback_image_args',
			array(
				'w' => intval( 1280 ),
				'h' => intval( 800 ),
			)
		);
		if ( $template_query->have_posts() ) :
			while ( $template_query->have_posts() ) :
				$template_query->the_post();
				global $post;
				$slug_base  = sanitize_title( md5( get_bloginfo( 'url' ) ) );
				$slug       = $slug_base . '-' . get_the_ID();
				if ( $slug ) {
					$library[ $slug ] = array();
					$library[ $slug ]['slug'] = $slug;
					$library[ $slug ]['name'] = the_title_attribute( 'echo=0' );
					$terms_array = array();
					if ( get_the_terms( $post, 'kadence-cloud-categories' ) ) {
						$terms = get_the_terms( $post, 'kadence-cloud-categories' );
						if ( is_array( $terms ) ) {
							foreach ( $terms as $key => $value ) {
								$terms_array[ $value->slug ] = $value->name;
							}
						}
					}
					$library[ $slug ]['categories'] = $terms_array;
					$keywords_array = array();
					if ( get_the_terms( $post, 'kadence-cloud-keywords' ) ) {
						$terms = get_the_terms( $post, 'kadence-cloud-keywords' );
						if ( is_array( $terms ) ) {
							foreach ( $terms as $key => $value ) {
								$keywords_array[] = $value->name;
							}
						}
					}
					$library[ $slug ]['keywords'] = $keywords_array;
					if ( ! empty( $request_extras ) && is_array( $request_extras ) ) {
						foreach ( $request_extras as $key => $data ) {
							$library[ $slug ][ $key ] = $data;
						}
					}
					$post_extras = apply_filters( 'kadence_cloud_post_extra_args', array(), $post, $request_extras );
					if ( ! empty( $post_extras ) && is_array( $post_extras ) ) {
						foreach ( $post_extras as $key => $data ) {
							$library[ $slug ][ $key ] = $data;
						}
					}
					$library[ $slug ]['pro']    = apply_filters( 'kadence_cloud_post_is_pro', false, $post, $request_extras );
					$library[ $slug ]['locked'] = apply_filters( 'kadence_cloud_post_is_locked', false, $post, $request_extras );
					if ( apply_filters( 'kadence_cloud_post_send_content', true, $post, $request_extras ) ) {
						$library[ $slug ]['content'] = $post->post_content;
					}
					$library[ $slug ]['description'] = $post->post_excerpt;
					if ( ! has_post_thumbnail() ) {
						$library[ $slug ]['image'] = add_query_arg( $fallback_image_args, 'https://s0.wordpress.com/mshots/v1/' . rawurlencode( esc_url( get_permalink() ) ) );
						$library[ $slug ]['imageW'] = $fallback_image_args['w'];
						$library[ $slug ]['imageH'] = $fallback_image_args['h'];
					} else {
						$image = wp_get_attachment_image_src( get_post_thumbnail_id( $post ), 'full' );
						$library[ $slug ]['image']  = $image[0];
						$library[ $slug ]['imageW'] = $image[1];
						$library[ $slug ]['imageH'] = $image[2];
					}
				}
			endwhile;
		endif;
		return $library;
	}
	/**
	 * Retrieves a collection of objects.
	 */
	public function get_templates( $post_per_page = -1, $page_number = 1, $key = '', $request_extras = array() ) {
		$args = array(
			'post_type'      => 'kadence_cloud',
			'post_status'    => 'publish',
			'posts_per_page' => $post_per_page,
			'order'          => 'ASC',
			'orderby'        => 'menu_order',
			'offset'         => ( 1 < $page_number && -1 !== $post_per_page ? ( $page_number * $post_per_page ) : 0 ),
		);
		$settings = json_decode( get_option( 'kadence_cloud' ), true );
		if ( isset( $settings['access_keys'] ) && ! empty( $settings['access_keys'] ) && is_array( $settings['access_keys'] ) ) {
			if ( isset( $settings['access_keys'][0] ) && is_array( $settings['access_keys'][0] ) ) {
				foreach ( $settings['access_keys'] as $the_key => $the_keys ) {
					if ( $the_keys['key'] === $key ) {
						if ( isset( $the_keys['collections'] ) && ! empty( $the_keys['collections'] ) ) {
							$args['tax_query'] = array(
								array(
									'taxonomy' => 'kadence-cloud-collections',
									'field' => 'id',
									'terms' => explode( ',', $the_keys['collections'] ),
								),
							);
						}
						break;
					}
				}
			}
		}
		$args      = apply_filters( 'kadence_cloud_template_query_args', $args, $key, $request_extras );
		$templates = new WP_Query( $args );
		$library   = $this->get_template_array( $templates, $request_extras );

		wp_send_json( $library );
	}
	/**
	 * Retrieves the query params for the search results collection.
	 *
	 * @return array Collection parameters.
	 */
	public function get_collection_params() {
		$query_params  = parent::get_collection_params();

		$query_params[ self::PROP_KEY ] = array(
			'description' => __( 'The request key.', 'kadence-cloud' ),
			'type'        => 'string',
		);

		$query_params[ self::PROP_SITE ] = array(
			'description' => __( 'The request website.', 'kadence-cloud' ),
			'type'        => 'string',
		);

		$query_params[ self::PROP_RETURN_COUNT ] = array(
			'description' => __( 'Items to return.', 'kadence-cloud' ),
			'type'        => 'string',
		);

		$query_params[ self::PROP_RETURN_PAGE ] = array(
			'description' => __( 'The Page to return.', 'kadence-cloud' ),
			'type'        => 'string',
		);

		return $query_params;
	}
}
