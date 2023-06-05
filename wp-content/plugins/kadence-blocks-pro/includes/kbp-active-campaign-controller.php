<?php
/**
 * KBP_Active_Campaign
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * KBP_Active_Campaign controller class.
 */
class KBP_Active_Campaign {

	/**
	 * The ActiveCampaign base url.
	 *
	 * @var string
	 */
	private $api_url;

	/**
	 * The ActiveCampaign API token.
	 *
	 * @var string
	 */
	private $api_key;

	/**
	 * The ActiveCampaign API Request Headers.
	 *
	 * @var string
	 */
	private $headers;

	/**
	 * Constructor.
	 */
	public function __construct( $api_url, $api_key ) {
		$this->api_url = $api_url;
		$this->api_key = $api_key;
		$this->headers = array(
			'Accept'       => 'application/json',
			'Content-Type' => 'application/json',
			'Api-Token'    => $this->api_key,
		);
	}
	/**
	 * Make Request.
	 *
	 * @param string $method
	 * @param string $endpoint
	 * @param array/null $query items to add to url
	 * @param array/null $body items to add to body
	 *
	 * @return Contact|null
	 */
	public function make_request( $method, $endpoint, $query = null, $body = null ) {
		$args = array(
			'method'  => $method,
			'timeout' => 10,
			'headers' => $this->headers,
		);
		if ( ! empty( $body ) ) {
			$args['body'] = json_encode( $body );
		}
		$request_url = rtrim( $this->api_url, '/' ) . '/api/3/' . $endpoint;
		if ( ! empty( $query ) ) {
			$request_url = add_query_arg( $query, $request_url );
		}
		if ( 'GET' === $method ) {
			$response = wp_safe_remote_get( $request_url, $args );
		} else {
			$response = wp_safe_remote_post( $request_url, $args );
		}
		if ( is_wp_error( $response ) ) {
			return false;
		}
		if ( 200 != (int) wp_remote_retrieve_response_code( $response ) && 201 != (int) wp_remote_retrieve_response_code( $response ) ) {
			return false;
		}
		$info = wp_remote_retrieve_body( $response );
		if ( empty( $info ) ) {
			return false;
		} else {
			return json_decode( $info, true );
		}
	}
	/**
	 * Find contact by email.
	 *
	 * @param string $email
	 *
	 * @return Contact|null
	 */
	public function find_contact( $email ) {
		$response = $this->make_request( 'GET', 'contacts', array( 'email' => $email ) );
		if ( ! $response ) {
			return false;
		} elseif ( ! isset( $response['contacts'] ) ) {
			return false;
		} elseif ( is_array( $response['contacts'] ) ) {
			return array_shift( $response['contacts'] );
		}
		return false;
	}
	/**
	 * Create new contact.
	 *
	 * @param array $contact array with contact information.
	 *
	 * Structure of Contact
	 * array:
	 *  email: string
	 *  firstName: string
	 *  lastName: string
	 *  phone: string
	 *  fieldValues: array
	 *    field: ID
	 *    value: string
	 *
	 * @return array/false
	 */
	public function create_contact( $contact ) {
		if ( empty( $contact['email'] ) ) {
			return false;
		}
		$response = $this->make_request( 'POST', 'contacts', null, array( 'contact' => $contact ) );
		if ( ! $response ) {
			return false;
		} elseif ( ! isset( $response['contact'] ) ) {
			return false;
		} elseif ( ! empty( $response['contact'] ) ) {
			return $response['contact'];
		}
		return false;
	}
	/**
	 * Find or create a contact.
	 *
	 * @param array $contact array with contact information.
	 *
	 * Structure of Contact
	 * array:
	 *  email: string
	 *  firstName: string
	 *  lastName: string
	 *  phone: string
	 *  fieldValues: array
	 *    field: ID
	 *    value: string
	 *
	 * @return array/false
	 */
	public function find_or_create_contact( $contact ) {
		if ( empty( $contact['email'] ) ) {
			return false;
		}
		$found_contact = $this->find_contact( $contact['email'] );

		if ( ! empty( $found_contact ) ) {
			return $found_contact;
		}

		return $this->create_contact( $contact );
	}
	/**
	 * Update or create an account.
	 *
	 * @param array $contact array with contact information.
	 *
	 * Structure of Contact
	 * array:
	 *  email: string
	 *  firstName: string
	 *  lastName: string
	 *  phone: string
	 *  fieldValues: array
	 *    field: ID
	 *    value: string
	 *
	 * @return array/false
	 */
	public function update_or_create_contact( $contact ) {
		if ( empty( $contact['email'] ) ) {
			return false;
		}
		$response = $this->make_request( 'POST', 'contact/sync', null, array( 'contact' => $contact ) );
		if ( ! $response ) {
			return false;
		} elseif ( ! isset( $response['contact'] ) ) {
			return false;
		} elseif ( ! empty( $response['contact'] ) ) {
			return $response['contact'];
		}
		return false;
	}
	/**
	 * Add contact to list.
	 *
	 * @param array $contact
	 * @param number $list_id
	 * @param boolean $double_optin true/false weather to set the status to 0 or 1.
	 *
	 * @return array
	 */
	public function add_list_to_contact( $contact, $list_id, $double_optin = false ) {
		if ( $double_optin ) {
			$status = 0;
			$full_contact = $this->make_request( 'GET', 'contacts/' . $contact['id'], null, null );
			if ( ! empty( $full_contact['contactLists'] ) && is_array( $full_contact['contactLists'] ) ) {
				foreach ( $full_contact['contactLists'] as $key => $list ) {
					if ( $list['list'] === $list_id ) {
						if ( $list['status'] == 1 ) {
							$status = 1;
						}
						break;
					}
				}
			}
		} else {
			$status = 1;
		}
		$args = array(
			'contact' => $contact['id'],
			'list'    => $list_id,
			'status'  => $status,
		);
		$response = $this->make_request( 'POST', 'contactLists', null, array( 'contactList' => $args ) );
		if ( ! $response ) {
			return false;
		} elseif ( ! isset( $response['contacts'] ) ) {
			return false;
		} elseif ( ! empty( $response['contacts'] ) ) {
			return $response['contacts'];
		}
		return false;
	}
	/**
	 * Add contact to list.
	 *
	 * @param array $contact
	 * @param array $lists
	 * @param number $status
	 *
	 * @return array
	 */
	public function add_lists_to_contact( $contact, $lists, $double_optin = false ) {
		foreach ( $lists as $list ) {
			$this->add_list_to_contact( $contact, $list, $double_optin );
		}
	}
	/**
	 * Find tag by name.
	 *
	 * @param string $name
	 *
	 * @return array
	 */
	public function find_tag( $name ) {
		$response = $this->make_request( 'GET', 'tags', array( 'search' => $name ) );
		if ( ! $response ) {
			return false;
		} elseif ( ! isset( $response['tags'] ) ) {
			return false;
		} elseif ( is_array( $response['tags'] ) ) {
			return array_shift( $response['tags'] );
		}
		return false;
	}
	/**
	 * Add tag to contact.
	 *
	 * @param array $contact
	 * @param number $tag_id
	 *
	 * @return array
	 */
	public function add_tag_to_contact( $contact, $tag_id ) {
		$args = array(
			'contact' => $contact['id'],
			'tag'     => $tag_id,
		);
		$response = $this->make_request( 'POST', 'contactTags', null, array( 'contactTag' => $args ) );
		if ( ! $response ) {
			return false;
		} elseif ( ! isset( $response['contactTag'] ) ) {
			return false;
		} elseif ( ! empty( $response['contactTag'] ) ) {
			return $response['contactTag'];
		}
	}
	/**
	 * @param array $contact
	 * @param array $tags
	 */
	public function add_tags_to_contact( $contact, $tags ) {
		foreach ( $tags as $tag ) {
			$this->add_tag_to_contact( $contact, $tag );
		}
	}
	/**
	 * Get all lists.
	 *
	 * @param number $limit the amount to limit in response.
	 * @param string/null $search a keyword to use for search.
	 *
	 * @return array
	 */
	public function get_all_lists( $limit = 20, $search = null ) {
		$args = array(
			'limit' => $limit,
		);
		if ( ! empty( $search ) ) {
			$args['filters[name]'] = $search;
		}
		$response = $this->make_request( 'GET', 'lists', $args );
		if ( ! $response ) {
			return false;
		} elseif ( ! isset( $response['lists'] ) ) {
			return false;
		} elseif ( ! empty( $response['lists'] ) ) {
			return $response['lists'];
		}
	}
	/**
	 * Get all tags.
	 *
 	 * @param string/null $search a keyword to use for search.
	 *
	 * @return array
	 */
	public function get_all_tags( $search = null ) {
		$args = null;
		if ( ! empty( $search ) ) {
			$args = array(
				'search' => $search,
			);
		}
		$response = $this->make_request( 'GET', 'tags', $args );
		if ( ! $response ) {
			return false;
		} elseif ( ! isset( $response['tags'] ) ) {
			return false;
		} elseif ( ! empty( $response['tags'] ) ) {
			return $response['tags'];
		}
	}
	/**
	 * Get all fields.
	 *
	 * @param number $limit the amount to limit in response.
	 *
	 * @return array
	 */
	public function get_all_fields( $limit = 100 ) {
		$args = array(
			'limit' => $limit,
		);
		$response = $this->make_request( 'GET', 'fields', $args );
		if ( ! $response ) {
			return false;
		} elseif ( ! isset( $response['fields'] ) ) {
			return false;
		} elseif ( ! empty( $response['fields'] ) ) {
			return $response['fields'];
		}
	}
}
