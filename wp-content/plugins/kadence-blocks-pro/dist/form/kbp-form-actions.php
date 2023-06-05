<?php
/**
 * Form Action Handling.
 *
 * @package Kadence Blocks Pro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * Main plugin class
 */
class KBP_Form_Actions {

	/**
	 * Instance Control
	 *
	 * @var null
	 */
	private static $instance = null;
	/**
	 * Instance Control
	 */
	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}
	/**
	 * Class Constructor.
	 */
	public function __construct() {
		add_action( 'kadence_blocks_form_submission', array( $this, 'process_actions' ), 10, 4 );
	}
	/**
	 * Process the form submit.
	 *
	 * @param array  $form_args the form settings.
	 * @param array  $fields the form fields.
	 * @param string $form_id the form ID.
	 * @param int    $post_id the post ID.
	 */
	public function process_actions( $form_args, $fields, $form_id, $post_id ) {
		if ( isset( $form_args ) && is_array( $form_args ) && isset( $form_args['actions'] ) ) {
			foreach ( $form_args['actions'] as $data ) {
				switch ( $data ) {
					case 'autoEmail':
						$auto_defaults = array(
							'subject' => __( 'Thanks for contacting us!', 'kadence-blocks-pro' ),
							'message' => __( 'Thanks for getting in touch, we will respond within the next 24 hours.', 'kadence-blocks-pro' ),
							'fromEmail' => '',
							'fromName' => '',
							'replyTo' => '',
							'cc' => '',
							'bcc' => '',
							'html' => true,
						);
						$auto_email_args = ( isset( $form_args['autoEmail'] ) && is_array( $form_args['autoEmail'] ) && isset( $form_args['autoEmail'][0] ) && is_array( $form_args['autoEmail'][0] ) ? $form_args['autoEmail'][0] : $auto_defaults );
						$subject     = isset( $auto_email_args['subject'] ) && ! empty( trim( $auto_email_args['subject'] ) ) ? $auto_email_args['subject'] : __( 'Thanks for contacting us!', 'kadence-blocks-pro' );
						$message     = isset( $auto_email_args['message'] ) && ! empty( trim( $auto_email_args['message'] ) ) ? $auto_email_args['message'] : __( 'Thanks for getting in touch, we will respond within the next 24 hours.', 'kadence-blocks-pro' );
						$reply_email = isset( $auto_email_args['replyTo'] ) && ! empty( trim( $auto_email_args['replyTo'] ) ) ? sanitize_email( trim( $auto_email_args['replyTo'] ) ) : false;
						$to          = isset( $auto_email_args['emailTo'] ) && ! empty( trim( $auto_email_args['emailTo'] ) ) ? $auto_email_args['emailTo'] : false;
						if ( strpos( $subject, '{field_' ) !== false ) {
							preg_match_all( '/{field_(.*?)}/', $subject, $match ); 
							if ( is_array( $match ) && isset( $match[1] ) && is_array( $match[1] ) ) {
								foreach ( $match[1] as $field_id ) {
									if ( isset( $field_id ) ) {
										$real_id = absint( $field_id ) - 1;
										if ( isset( $fields[ $real_id ] ) && is_array( $fields[ $real_id ] ) && isset( $fields[ $real_id ]['value'] ) ) {
											$subject = str_replace( '{field_' . $field_id . '}' , $fields[ $real_id ]['value'], $subject );
										}
									}
								}
							}
						}
						if ( strpos( $message, '{field_' ) !== false ) {
							preg_match_all( '/{field_(.*?)}/', $message, $match ); 
							if ( is_array( $match ) && isset( $match[1] ) && is_array( $match[1] ) ) {
								foreach ( $match[1] as $field_id ) {
									if ( isset( $field_id ) ) {
										$real_id = absint( $field_id ) - 1;
										if ( isset( $fields[ $real_id ] ) && is_array( $fields[ $real_id ] ) && isset( $fields[ $real_id ]['value'] ) ) {
											$message = str_replace( '{field_' . $field_id . '}' , $fields[ $real_id ]['value'], $message );
										}
									}
								}
							}
						}
						if ( ! $to ) {
							foreach ( $fields as $key => $data ) {
								if ( 'email' === $data['type'] ) {
									$to = $data['value'];
									break;
								}
							}
						}
						// Can't find someone to email?
						if ( ! $to ) {
							break;
						}
						if ( ! isset( $auto_email_args['html'] ) || ( isset( $auto_email_args['html'] ) && $auto_email_args['html'] ) ) {
							$args = array(
								'message' => $message,
								'fields' => $fields,
							);
							$email_content = kadence_blocks_pro_get_template_html( 'form-auto-email.php', $args );
						} else {
							$email_content = $message . "\n\n";
						}
						$body     = $email_content;
						if ( ! isset( $auto_email_args['html'] ) || ( isset( $auto_email_args['html'] ) && $auto_email_args['html'] ) ) {
							$headers  = 'Content-Type: text/html; charset=UTF-8' . "\r\n";
						} else {
							$headers  = 'Content-Type: text/plain; charset=UTF-8' . "\r\n";
						}
						if ( $reply_email ) {
							$headers .= 'Reply-To: <' . $reply_email . '>' . "\r\n";
						}
						if ( isset( $auto_email_args['fromEmail'] ) && ! empty( trim( $auto_email_args['fromEmail'] ) ) ) {
							$headers .= 'From: ' . ( isset( $auto_email_args['fromName'] ) && ! empty( trim( $auto_email_args['fromName'] ) ) ? trim( $auto_email_args['fromName'] ) . ' ' : '' ) . '<' . sanitize_email( trim( $auto_email_args['fromEmail'] ) ) . '>' . "\r\n";
						}
						$cc_headers = '';
						if ( isset( $auto_email_args['cc'] ) && ! empty( trim( $auto_email_args['cc'] ) ) ) {
							$cc_headers = 'Cc: ' . sanitize_email( trim( $auto_email_args['cc'] ) ) . "\r\n";
						}

						wp_mail( $to, $subject, $body, $headers . $cc_headers );
						if ( isset( $auto_email_args['bcc'] ) && ! empty( trim( $auto_email_args['bcc'] ) ) ) {
							$bcc_emails = explode( ',', $auto_email_args['bcc'] );
							foreach ( $bcc_emails as $bcc_email ) {
								wp_mail( sanitize_email( trim( $bcc_email ) ), $subject, $body, $headers );
							}
						}
						break;
					case 'entry':
						$entry_defaults = array(
							'name' => '',
							'userIP' => true,
							'userDevice' => true,
						);
						$entry_args = ( isset( $form_args['entry'] ) && is_array( $form_args['entry'] ) && isset( $form_args['entry'][0] ) && is_array( $form_args['entry'][0] ) ? $form_args['entry'][0] : $entry_defaults );
						$user_ip = ( ! isset( $entry_args['userIP'] ) || ( isset( $entry_args['userIP'] ) && $entry_args['userIP'] ) ? $this->get_client_ip() : ip2long( '0.0.0.0' ) );
						$browser = ( ! isset( $entry_args['userDevice'] ) || ( isset( $entry_args['userDevice'] ) && $entry_args['userDevice'] ) ? $this->get_browser() : false );
						$name    = ( isset( $entry_args['name'] ) && ! empty( trim( $entry_args['name'] ) ) ? trim( $entry_args['name'] ) : esc_attr( strip_tags( get_the_title( $post_id ) ) ) . ' ' . esc_attr__( 'submission', 'kadence-blocks-pro' ) );
						$referer = wp_get_referer();
						if ( strlen( $referer ) > 255 ) {
							$referer = strtok( $referer, '?' );
						}
						if ( strlen( $referer ) > 255 ) {
							$referer = substr( $referer, 0, 255 );
						}
						$data = array(
							'name'         => $name,
							'form_id'      => $form_id,
							'post_id'      => $post_id,
							'user_id'      => get_current_user_id(),
							'date_created' => current_time( 'mysql' ),
							'user_ip'      => $user_ip,
							'referer'      => $referer,
							'user_device'  => ( $browser ? $browser['name'] . '/' . $browser['platform'] : esc_html__( 'Not Collected', 'kadence-blocks-pro' ) ),
						);

						$entries  = new KBP\Queries\Entry();
						$entry_id = $entries->add_item( $data );
						if ( $entry_id ) {
							foreach ( $fields as $key => $meta_data ) {
								$response = $this->add_field( $entry_id, 'kb_field_' . $key, $meta_data );
							}
						}
						break;
					case 'sendinblue':
							$api_key = get_option( 'kadence_blocks_send_in_blue_api' );
							if ( empty( $api_key ) ) {
								return;
							}
							$sendinblue_default = array(
								'list' => array(),
								'map' => array(),
								'doubleOptin' => false,
								'templateId' => '',
								'redirectionUrl' => '',
							);
							//error_log( print_r( $form_args, true ) );
							$sendinblue_args = ( isset( $form_args['sendinblue'] ) && is_array( $form_args['sendinblue'] ) && isset( $form_args['sendinblue'][0] ) && is_array( $form_args['sendinblue'][0] ) ? $form_args['sendinblue'][0] : $sendinblue_default );
							$list = ( isset( $sendinblue_args['list'] ) ? $sendinblue_args['list'] : '' );
							$map = ( isset( $sendinblue_args['map'] ) && is_array( $sendinblue_args['map'] ) ? $sendinblue_args['map'] : array() );
							$templateId = ( isset( $sendinblue_args['templateId'] ) && ! empty( $sendinblue_args['templateId'] ) ? $sendinblue_args['templateId'] : false );
							if ( $templateId ) {
								$redirectionUrl = ( isset( $sendinblue_args['redirectionUrl'] ) && ! empty( $sendinblue_args['redirectionUrl'] ) ? $sendinblue_args['redirectionUrl'] : false );
								if ( $redirectionUrl ) {
									$double_optin = ( isset( $sendinblue_args['doubleOptin'] ) ? $sendinblue_args['doubleOptin'] : false );
								} else {
									$double_optin = false;
								}
							} else {
								$double_optin = false;
							}
							$body = array(
								'attributes' => array(),
							);
							if ( $double_optin ) {
								$body['templateId'] = $templateId;
								$body['redirectionUrl'] = $redirectionUrl;
							} else {
								$body['updateEnabled'] = true;
							}
							$email = false;
							if ( ! empty( $map ) ) {
								foreach ( $fields as $key => $data ) {
									if ( isset( $map[ $key ] ) && ! empty( $map[ $key ] ) ) {
										if ( 'email' === $map[ $key ] && ! $email ) {
											$email = $data['value'];
											$body['email'] = $data['value'];
										} elseif ( 'OPT_IN' === $map[ $key ] ) {
											if ( $data['value'] ) {
												$body['attributes'][ $map[ $key ] ] = true;
											} else {
												$body['attributes'][ $map[ $key ] ] = false;
											}
										} else {
											$body['attributes'][ $map[ $key ] ] = $data['value'];
										}
									}
								}
							} else {
								foreach ( $fields as $key => $data ) {
									if ( 'email' === $data['type'] ) {
										$email = $data['value'];
										$body['email'] = $data['value'];
										break;
									}
								}
							}
							if ( empty( $body['attributes'] ) ) {
								unset( $body['attributes'] );
							}
							if ( ! empty( $list ) ) {
								$lists = array(
									'listIds' => array(),
								);
								foreach ( $list as $key => $value ) {
									$lists['listIds'][] = $value['value'];
								}
							} else {
								$lists = array(
									'listIds' => array(),
								);
							}
							if ( $double_optin ) {
								$body['includeListIds'] = $lists['listIds'];
							} else {
								$body['listIds'] = $lists['listIds'];
							}
							//error_log( print_r( $body, true ) );
							if ( isset( $body[ 'email' ] ) ) {
								$api_url = ( $double_optin ? 'https://api.sendinblue.com/v3/contacts/doubleOptinConfirmation' : 'https://api.sendinblue.com/v3/contacts' );
								$response = wp_remote_post(
									$api_url,
									array(
										'method'  => 'POST',
										'timeout' => 10,
										'headers' => array(
											'accept'       => 'application/json',
											'content-type' => 'application/json',
											'api-key'      => $api_key,
										),
										'body'    => json_encode( $body ),
									)
								);
								//error_log( print_r( $response, true ) );
								if ( is_wp_error( $response ) ) {
									$error_message = $response->get_error_message();
									error_log( "Something went wrong: $error_message" );
								} else {
									if ( ! isset( $response['response'] ) || ! isset( $response['response']['code'] ) ) {
										error_log( __('No Response from SendInBlue', 'kadence-blocks-pro' ) );
										return;
									}
									if ( 400 === $response['response']['code'] ) {
										error_log( $response['response']['message'] );
										return;
									}
									// if ( 204 === $response['response']['code'] || 200 === $response['response']['code'] || 201 === $response['response']['code'] ) {
									// 	// Contact added or updated, now add to list.
									// 	$api_url = 'https://api.sendinblue.com/v3/contacts/' . urlencode( $email );
									// 	$response = wp_remote_post(
									// 		$api_url,
									// 		array(
									// 			'method'  => 'PUT',
									// 			'timeout' => 10,
									// 			'headers' => array(
									// 				'accept'       => 'application/json',
									// 				'content-type' => 'application/json',
									// 				'api-key'      => $api_key,
									// 			),
									// 			'body'    => json_encode( $lists ),
									// 		)
									// 	);
									// 	if ( is_wp_error( $response ) ) {
									// 		$error_message = $response->get_error_message();
									// 		error_log( "Something went wrong: $error_message" );
									// 	} else {
									// 		if ( ! isset( $response['response'] ) || ! isset( $response['response']['code'] ) ) {
									// 			error_log( __('No Response from SendInBlue to add to list', 'kadence-blocks-pro' ) );
									// 			return;
									// 		}
									// 		if ( 400 === $response['response']['code'] || 404 === $response['response']['code'] ) {
									// 			error_log( $response['response']['message'] );
									// 			return;
									// 		}
									// 	}
									// }
								}
							}
						break;
					case 'mailchimp':
						$api_key = get_option( 'kadence_blocks_mail_chimp_api' );
						if ( empty( $api_key ) ) {
							return;
						}
						$mailchimp_default = array(
							'list'        => array(),
							'groups'      => array(),
							'tags'      => array(),
							'map'         => array(),
							'doubleOptin' => false,
						);
						$mailchimp_args = ( isset( $form_args['mailchimp'] ) && is_array( $form_args['mailchimp'] ) && isset( $form_args['mailchimp'][0] ) && is_array( $form_args['mailchimp'][0] ) ? $form_args['mailchimp'][0] : $mailchimp_default );
						$list        = ( isset( $mailchimp_args['list'] ) ? $mailchimp_args['list'] : '' );
						$groups      = ( isset( $mailchimp_args['groups'] ) && is_array( $mailchimp_args['groups'] ) ? $mailchimp_args['groups'] : array() );
						$tags        = ( isset( $mailchimp_args['tags'] ) && is_array( $mailchimp_args['tags'] ) ? $mailchimp_args['tags'] : array() );
						$map         = ( isset( $mailchimp_args['map'] ) && is_array( $mailchimp_args['map'] ) ? $mailchimp_args['map'] : array() );
						$double_optin = ( isset( $mailchimp_args['doubleOptin'] ) ? $mailchimp_args['doubleOptin'] : false );
						$body   = array(
							'email_address' => '',
							'status_if_new' => 'subscribed',
							'status'        => 'subscribed',
						);
						if ( $double_optin ) {
							$body['status_if_new'] = 'pending';
							$body['double_optin']  = true;
						}
						if ( empty( $list ) || ! is_array( $list ) ) {
							return;
						}
						$key_parts = explode( '-', $api_key );
						if ( empty( $key_parts[1] ) || 0 !== strpos( $key_parts[1], 'us' ) ) {
							return;
						}
						$base_url = 'https://' . $key_parts[1] . '.api.mailchimp.com/3.0/';
						$email = false;
						if ( ! empty( $groups ) ) {
							foreach ( $groups as $id => $label ) {
								if ( ! isset( $body['interests'] ) ) {
									$body['interests'] = array();
								}
								$body['interests'][ $label['value'] ] = true;
							}
						}
						$tags_array = array();
						if ( ! empty( $tags ) ) {
							foreach ( $tags as $id => $tag_item ) {
								if ( ! isset( $body['tags'] ) ) {
									$body['tags'] = array();
								}
								$body['tags'][] = $tag_item['label'];
								$tags_array[] = array(
									'name'   => $tag_item['label'],
									'status' => 'active',
								);
							}
						}
						if ( ! empty( $map ) ) {
							foreach ( $fields as $key => $data ) {
								if ( isset( $map[ $key ] ) && ! empty( $map[ $key ] ) ) {
									if ( 'email' === $map[ $key ] && ! $email ) {
										$email = $data['value'];
										$body['email_address'] = $data['value'];
									} else {
										if ( ! isset( $body['merge_fields'] ) ) {
											$body['merge_fields'] = array();
										}
										$body['merge_fields'][ $map[ $key ] ] = $data['value'];
									}
								}
							}
						} else {
							foreach ( $fields as $key => $data ) {
								if ( 'email' === $data['type'] ) {
									$email = $data['value'];
									$body['email_address'] = $data['value'];
									break;
								}
							}
						}
						$list_id = ( isset( $list['value'] ) && ! empty( $list['value'] ) ? $list['value'] : '' );
						if ( empty( $list_id ) ) {
							return;
						}
						if ( isset( $body['email_address'] ) ) {
							$subscriber_hash = md5( strtolower( $body['email_address'] ) );
							$api_url = $base_url . 'lists/' . $list_id . '/members/' . $subscriber_hash;
							//error_log( $api_url );
							$response = wp_remote_post(
								$api_url,
								array(
									'method'  => 'PUT',
									'timeout' => 10,
									'headers' => array(
										'accept'       => 'application/json',
										'content-type' => 'application/json',
										'Authorization' => 'Basic ' . base64_encode( 'user:' . $api_key ),
									),
									'body'    => json_encode( $body ),
								)
							);
							if ( is_wp_error( $response ) ) {
								$error_message = $response->get_error_message();
								//error_log( "Something went wrong: $error_message" );
							} else {
								if ( ! isset( $response['response'] ) || ! isset( $response['response']['code'] ) ) {
									//error_log( __('Failed to Connect to MailChimp', 'kadence-blocks-pro' ) );
									return;
								}
								if ( 400 === $response['response']['code'] || 404 === $response['response']['code'] ) {
									//error_log( $response['response']['message'] );
									return;
								} else if ( 200 === $response['response']['code'] ) {
									// need to check if tags were added.
									$needs_update = false;
									$body = json_decode( wp_remote_retrieve_body( $response ), true );
									if ( ! empty( $tags_array ) && empty( $body['tags'] ) ) {
										$needs_update = true;
									} else if ( ! empty( $tags_array ) && ! empty( $body['tags'] ) && is_array( $body['tags'] ) ) {
										$current_tags = array();
										foreach ( $body['tags'] as $key => $data ) {
											$current_tags[] = $data['name'];
										}
										foreach ( $tags_array as $key => $data ) {
											if ( ! in_array( $data['name'], $current_tags ) ) {
												$needs_update = true;
												break;
											}
										}
									}
									if ( $needs_update ) {
										$tag_url = $base_url . 'lists/' . $list_id . '/members/' . $subscriber_hash . '/tags';
										$tag_response = wp_remote_post(
											$tag_url,
											array(
												'method'  => 'POST',
												'timeout' => 10,
												'headers' => array(
													'accept'       => 'application/json',
													'content-type' => 'application/json',
													'Authorization' => 'Basic ' . base64_encode( 'user:' . $api_key ),
												),
												'body'    => json_encode( array( 'tags' => $tags_array ) ),
											)
										);
										if ( is_wp_error( $tag_response ) ) {
											$error_message = $tag_response->get_error_message();
											//error_log( "Something went wrong: $error_message" );
										} else {
											if ( ! isset( $tag_response['response'] ) || ! isset( $tag_response['response']['code'] ) ) {
												//error_log( __('Failed to Connect to MailChimp', 'kadence-blocks-pro' ) );
												return;
											}
											if ( 204 === $tag_response['response']['code'] ) {
												//error_log( 'success' );
												return;
											}
										}
									}
								}
							}
						}
					break;
					case 'activecampaign':
						$api_key = get_option( 'kadence_blocks_activecampaign_api_key' );
						$api_url = get_option( 'kadence_blocks_activecampaign_api_base' );
						if ( empty( $api_key ) || empty( $api_url ) ) {
							return;
						}
						$activecampaign_default = array(
							'list'        => array(),
							'tags'      => array(),
							'map'         => array(),
							'doubleOptin' => false,
						);
						$activecampaign_args = ( isset( $form_args['activecampaign'] ) && is_array( $form_args['activecampaign'] ) && isset( $form_args['activecampaign'][0] ) && is_array( $form_args['activecampaign'][0] ) ? $form_args['activecampaign'][0] : $activecampaign_default );
						$list         = ( isset( $activecampaign_args['list'] ) ? $activecampaign_args['list'] : '' );
						$tags         = ( isset( $activecampaign_args['tags'] ) && is_array( $activecampaign_args['tags'] ) ? $activecampaign_args['tags'] : array() );
						$map          = ( isset( $activecampaign_args['map'] ) && is_array( $activecampaign_args['map'] ) ? $activecampaign_args['map'] : array() );
						$double_optin = ( isset( $activecampaign_args['doubleOptin'] ) ? $activecampaign_args['doubleOptin'] : false );
						if ( empty( $list ) || ! is_array( $list ) ) {
							return;
						}
						$email = false;
						$tags_array = array();
						if ( ! empty( $tags ) ) {
							foreach ( $tags as $tag_index => $tag_item ) {
								$tags_array[] = $tag_item['value'];
							}
						}
						$contact = array();
						$custom_fields = array();
						if ( ! empty( $map ) ) {
							foreach ( $fields as $key => $data ) {
								if ( isset( $map[ $key ] ) && ! empty( $map[ $key ] ) ) {
									if ( 'email' === $map[ $key ] && ! $email ) {
										$email = $data['value'];
										$contact['email'] = $data['value'];
									} elseif ( 'firstName' === $map[ $key ] ) {
										$contact['firstName'] = $data['value'];
									} elseif ( 'lastName' === $map[ $key ] ) {
										$contact['lastName'] = $data['value'];
									} elseif ( 'phone' === $map[ $key ] ) {
										$contact['phone'] = $data['value'];
									} else {
										$custom_fields[] = array( 'field' => $map[ $key ], 'value' => $data['value'] );
									}
								}
							}
						} else {
							foreach ( $fields as $key => $data ) {
								if ( 'email' === $data['type'] ) {
									$email = $data['value'];
									$contact['email'] = $data['value'];
									break;
								}
							}
						}
						if ( ! empty( $custom_fields ) ) {
							$contact['fieldValues'] = $custom_fields;
						}
						$list_array = array();
						if ( ! empty( $list ) ) {
							foreach ( $list as $list_index => $list_item ) {
								$list_array[] = $list_item['value'];
							}
						}
						if ( empty( $list_array ) ) {
							return;
						}
						if ( ! empty( $contact['email'] ) ) {
							$active_campaign = new KBP_Active_Campaign( $api_url, $api_key );
							$found_contact = $active_campaign->update_or_create_contact( $contact );
							if ( empty( $found_contact ) ) {
								error_log( __( 'No Response from Active Campaign', 'kadence-blocks-pro' ) );
								return;
							}
							$contact_list = $active_campaign->add_lists_to_contact( $found_contact, $list_array, $double_optin );
							if ( ! empty( $tags_array ) ) {
								$contact_tag = $active_campaign->add_tags_to_contact( $found_contact, $tags_array );
							}
							return;
						}
						break;
					case 'webhook':
						$webhook_defaults = array(
							'url' => '',
						);
						$webhook_args = ( isset( $form_args['webhook'] ) && is_array( $form_args['webhook'] ) && isset( $form_args['webhook'][0] ) && is_array( $form_args['webhook'][0] ) ? $form_args['webhook'][0] : $webhook_defaults );
						if ( empty( $webhook_args['url'] ) ) {
							return;
						}
						$map     = ( isset( $webhook_args['map'] ) && is_array( $webhook_args['map'] ) ? $webhook_args['map'] : array() );
						$user_ip = $this->get_client_ip();
						$browser = $this->get_browser();
						$name    = esc_attr( strip_tags( get_the_title( $post_id ) ) );
						$body = array(
							'post_name'    => $name,
							'post_url'      => wp_get_referer(),
							'post_id'      => $post_id,
							'form_id'      => $form_id,
							'user_id'      => get_current_user_id(),
							'user_ip'      => $user_ip,
							'user_device'  => ( $browser ? $browser['name'] . '/' . $browser['platform'] : esc_html__( 'Not Collected', 'kadence-blocks-pro' ) ),
							'date_created' => date_i18n( get_option( 'date_format' ) ),
							'time_created' => date_i18n( get_option( 'time_format' ) ),
						);
						if ( ! empty( $map ) ) {
							foreach ( $fields as $key => $data ) {
								if ( isset( $map[ $key ] ) && ! empty( $map[ $key ] ) ) {
									$body[ $map[ $key ] ] = $data['value'];
								} else {
									$body[ $data['label'] ] = $data['value'];
								}
							}
						} else {
							foreach ( $fields as $key => $data ) {
								$body[ $data['label'] ] = $data['value'];
							}
						}
						$args = array(
							'body' => $body,
						);
						$args = apply_filters( 'kadence_blocks_pro_webhook_args', $args );
						$response = wp_remote_post( $webhook_args['url'], $args );
						if ( 200 !== (int) wp_remote_retrieve_response_code( $response ) ) {
							return;
						}
					break;
				}
			}
		}
	}

	/**
	 * Add meta data field to a entry
	 *
	 * @param int    $entry_id      entry ID.
	 * @param string $meta_key      Meta data name.
	 * @param mixed  $meta_value    Meta data value. Must be serializable if non-scalar.
	 * @param bool   $unique        Optional. Whether the same key should not be added. Default false.
	 *
	 * @since 3.0
	 * @return false|int
	 */
	public function add_field( $entry_id, $meta_key, $meta_value, $unique = false ) {
		return add_metadata( 'kbp_form_entry', $entry_id, $meta_key, $meta_value, $unique );
	}
	/**
	 * Get User Agent browser and OS type
	 *
	 * @return array
	 */
	public function get_browser() {
		$u_agent  = $_SERVER['HTTP_USER_AGENT'];
		$bname    = 'Unknown';
		$platform = 'Unknown';
		$ub       = 'Unknown';
		$version  = '';

		// first get the platform
		if ( preg_match( '/linux/i', $u_agent ) ) {
			$platform = 'Linux';
		} elseif ( preg_match( '/macintosh|mac os x/i', $u_agent ) ) {
			$platform = 'MAC OS';
		} elseif ( preg_match( '/windows|win32/i', $u_agent ) ) {
			$platform = 'Windows';
		}

		// next get the name of the useragent yes seperately and for good reason
		if ( preg_match( '/MSIE/i', $u_agent ) && ! preg_match( '/Opera/i', $u_agent ) ) {
			$bname = 'Internet Explorer';
			$ub    = 'MSIE';
		} elseif ( preg_match( '/Trident/i', $u_agent ) ) {
			// this condition is for IE11.
			$bname = 'Internet Explorer';
			$ub = 'rv';
		} elseif ( preg_match( '/Firefox/i', $u_agent ) ) {
			$bname = 'Mozilla Firefox';
			$ub = 'Firefox';
		} elseif ( preg_match( '/Chrome/i', $u_agent ) ) {
			$bname = 'Google Chrome';
			$ub = 'Chrome';
		} elseif ( preg_match( '/Safari/i', $u_agent ) ) {
			$bname = 'Apple Safari';
			$ub = 'Safari';
		} elseif ( preg_match( '/Opera/i', $u_agent ) ) {
			$bname = 'Opera';
			$ub = 'Opera';
		} elseif ( preg_match( '/Netscape/i', $u_agent ) ) {
			$bname = 'Netscape';
			$ub = 'Netscape';
		}

		// finally get the correct version number.
		$known = array( 'Version', $ub, 'other' );
		$pattern = '#(?<browser>' . join( '|', $known ) .
			')[/|: ]+(?<version>[0-9.|a-zA-Z.]*)#';
		if ( ! preg_match_all( $pattern, $u_agent, $matches ) ) {
			// we have no matching number just continue.
		}

		// see how many we have.
		$i = count( $matches['browser'] );

		if ( $i != 1 ) {
			// we will have two since we are not using 'other' argument yet
			// see if version is before or after the name
			if ( strripos( $u_agent, 'Version' ) < strripos( $u_agent, $ub ) ) {
				$version = $matches['version'][0];
			} else {
				$version = $matches['version'][1];
			}
		} else {
			$version = $matches['version'][0];
		}

		// check if we have a number.
		if ( null === $version || '' === $version ) {
			$version = '';
		}

		return array(
			'userAgent' => $u_agent,
			'name'      => $bname,
			'version'   => $version,
			'platform'  => $platform,
			'pattern'   => $pattern,
		);
	}
	/**
	 * Get the client IP address
	 *
	 * @return string
	 */
	public function get_client_ip() {
		$ipaddress = 'UNKNOWN';
		$ip_keys = array(
			// Providers.
			'HTTP_CF_CONNECTING_IP', // Cloudflare.
			'HTTP_INCAP_CLIENT_IP', // Incapsula.
			'HTTP_X_CLUSTER_CLIENT_IP', // RackSpace.
			'HTTP_TRUE_CLIENT_IP', // Akamai.
			// Proxies.
			'HTTP_X_FORWARDED_FOR',
			'HTTP_X_FORWARDED',
			'HTTP_CLIENT_IP',
			'HTTP_X_REAL_IP',
			'HTTP_FORWARDED',
			'HTTP_FORWARDED_FOR',
			// Standard fallback.
			'REMOTE_ADDR',
		);
		foreach ( $ip_keys as $key ) {
			$value = isset( $_SERVER[ $key ] ) ? $_SERVER[ $key ] : '';
			if ( ! empty( $value ) && is_string( $value ) ) {
				foreach ( explode( ',', $value ) as $ip ) {
					// trim for safety measures.
					$ip = trim( $ip );
					// attempt to validate IP.
					if (
						//filter_var($ip, FILTER_VALIDATE_IP) // any valid IP address.
						filter_var( $ip, FILTER_VALIDATE_IP,
							FILTER_FLAG_IPV4 // allow only IPv4 addresses.
						)
					) {
						return $ip;
					}
				}
			}
		}
		return $ipaddress;
	}
}
KBP_Form_Actions::get_instance();
