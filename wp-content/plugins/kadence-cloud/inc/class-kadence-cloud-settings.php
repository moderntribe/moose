<?php
/**
 * Kadence Cloud Settings Class
 *
 * @package Kadence Cloud
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Main Kadence_Cloud_Settings class
 */
class Kadence_Cloud_Settings {
	const OPT_NAME = 'kadence_cloud';

	/**
	 * Action on init.
	 */
	public function __construct() {
		require_once KADENCE_CLOUD_PATH . 'inc/settings/load.php';
		// Need to load this with priority higher then 10 so class is loaded.
		add_action( 'after_setup_theme', array( $this, 'add_sections' ), 20 );
	}
	/**
	 * Add sections to settings.
	 */
	public function add_sections() {
		if ( ! class_exists( 'Kadence_Settings_Engine' ) ) {
			return;
		}
		$args = array(
			'opt_name'                         => self::OPT_NAME,
			'menu_icon'                        => '',
			'menu_title'                       => __( 'Cloud Settings', 'kadence-cloud' ),
			'page_title'                       => __( 'Cloud Settings', 'kadence-cloud' ),
			'page_slug'                        => 'kadence-cloud-settings',
			'page_permissions'                 => 'manage_options',
			'menu_type'                        => 'submenu',
			'page_parent'                      => 'edit.php?post_type=kadence_cloud',
			'page_priority'                    => null,
			'footer_credit'                    => '',
			'class'                            => '',
			'admin_bar'                        => false,
			'admin_bar_priority'               => 999,
			'admin_bar_icon'                   => '',
			'show_import_export'               => false,
			'version'                          => KADENCE_CLOUD_VERSION,
			'logo'                             => KADENCE_CLOUD_URL . 'assets/kadence-cloud.png',
			'changelog'                        => KADENCE_CLOUD_PATH . 'changelog.txt',
		);
		$args['tabs'] = array(
			'settings' => array(
				'id' => 'settings',
				'title' => __( 'Settings', 'kadence-cloud' ),
			),
			'started' => array(
				'id' => 'started',
				'title' => __( 'Getting Started', 'kadence-cloud' ),
			),
			'changelog' => array(
				'id' => 'changelog',
				'title' => __( 'Changelog', 'kadence-cloud' ),
			),
		);
		$args['started'] = array(
			'title' => __( 'Welcome to Kadence Cloud', 'kadence-cloud' ),
			'description' => __( 'We are working on a getting started video to be added below here, it\'s coming soon.', 'kadence-cloud' ),
			'video_url' => '',
			'link_url' => 'https://kadencecloud.com/knowledge-base/',
			'link_text' => __( 'View Knowledge Base', 'kadence-cloud' ),
		);
		$args['sidebar'] = array(
			'facebook' => array(
				'title' => __( 'Web Creators Community', 'kadence-cloud' ),
				'description' => __( 'Join our community of fellow kadence users creating effective websites! Share your site, ask a question and help others.', 'kadence-cloud' ),
				'link' => 'https://www.facebook.com/groups/webcreatorcommunity',
				'link_text' => __( 'Join our Facebook Group', 'kadence-cloud' ),
			),
			'docs' => array(
				'title' => __( 'Documentation', 'kadence-cloud' ),
				'description' => __( 'Need help? We have a knowledge base full of articles to get you started.', 'kadence-cloud' ),
				'link' => 'https://kadencecloud.com/knowledge-base/',
				'link_text' => __( 'Browse Docs', 'kadence-cloud' ),
			),
			'support' => array(
				'title' => __( 'Support', 'kadence-cloud' ),
				'description' => __( 'Have a question, we are happy to help! Get in touch with our support team.', 'kadence-cloud' ),
				'link' => 'https://www.kadencewp.com/premium-support-tickets/',
				'link_text' => __( 'Submit a Ticket', 'kadence-cloud' ),
			),
		);
		Kadence_Settings_Engine::set_args( self::OPT_NAME, $args );
		Kadence_Settings_Engine::set_section(
			self::OPT_NAME,
			array(
				'id'     => 'kc_general',
				'title'  => __( 'General', 'kadence-cloud' ),
				'long_title'  => __( 'General Settings', 'kadence-cloud' ),
				'desc'   => '',
				'fields' => array(
					array(
						'id'       => 'cloud_name',
						'type'     => 'text',
						'title'    => __( 'Set Cloud Name', 'kadence-cloud' ),
						'help' => __( 'If unset the site title will be used.', 'kadence-cloud' ),
					),
					array(
						'id'       => 'expires',
						'type'     => 'select',
						'title'    => __( 'Force sites to resync after', 'kadence-cloud' ),
						'options'  => array(
							'day'   => __( 'One Day', 'kadence-cloud' ),
							'week'  => __( 'One Week', 'kadence-cloud' ),
							'month' => __( 'One Month', 'kadence-cloud' ),
						),
						'default'  => 'month',
					),
					array(
						'id'       => 'enable_flash',
						'type'     => 'switch',
						'title'    => __( 'Enable API Flash for screenshots.', 'kadence-cloud' ),
						'help' => __( 'This allows you to regenerate thumbnails with a click.', 'kadence-cloud' ),
						'default'  => 0,
					),
					array(
						'id'       => 'flash_api',
						'type'     => 'text',
						'title'    => __( 'Add API Flash Key', 'kadence-cloud' ),
						'help'     => __( 'Get your API Key here', 'kadence-cloud' ),
						'helpLink' => 'https://apiflash.com/',
						'required' => array( 'enable_flash', '=', 'true' ),
					),
				),
			)
		);
		Kadence_Settings_Engine::set_section(
			self::OPT_NAME,
			array(
				'id'     => 'kc_keys',
				'title'  => __( 'Access Keys', 'kadence-cloud' ),
				'long_title'  => __( 'Access Keys', 'kadence-cloud' ),
				'desc'   => '',
				'fields' => array(
					array(
						'id'         => 'site_url',
						'type'       => 'code_info',
						'title'      => __( 'Connection URL', 'kadence-cloud' ),
						'content'    => get_site_url(),
					),
					array(
						'id'         => 'access_keys',
						'type'       => 'text_repeater_expanded',
						'title'      => __( 'Access Keys', 'kadence-cloud' ),
						'add_button' => __( 'Generate Key', 'kadence-cloud' ),
						'editable'   => true,
						'content'    => 'key',
					),
				),
			)
		);
	}
}
new Kadence_Cloud_Settings();
