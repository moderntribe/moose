<?php
/**
 * Kadence Settings
 *
 * @package Kadence Settings
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Kadence_Settings_Engine', false ) ) {
	/**
	 * Kadence Setting Engine Class
	 */
	class Kadence_Settings_Engine {

		/**
		 *  Option fields.
		 *
		 * @var array
		 */
		public static $fields = array();

		/**
		 * Option sections.
		 *
		 * @var array
		 */
		public static $sections = array();

		/**
		 * Option defaults.
		 *
		 * @var array
		 */
		public static $options_defaults = array();


		/**
		 * Option global args.
		 *
		 * @var array
		 */
		public static $args = array();
		/**
		 * Constructor function.
		 */
		public function __construct() {
			if ( ! defined( 'KADENCE_SETTINGS_PATH' ) ) {
				define( 'KADENCE_SETTINGS_PATH', plugin_dir_path( __FILE__ ) );
			}
			if ( ! defined( 'KADENCE_SETTINGS_URL' ) ) {
				define( 'KADENCE_SETTINGS_URL', trailingslashit( plugin_dir_url( __FILE__ ) ) );
			}
			if ( ! defined( 'KADENCE_SETTINGS_VERSION' ) ) {
				define( 'KADENCE_SETTINGS_VERSION', '1.5.4' );
			}
			add_action( 'init', array( $this, 'create_settings' ) );
		}
		/**
		 * Create Kadence Settings instance.
		 */
		public static function create_settings() {
			foreach ( self::$sections as $opt_name => $the_sections ) {
				if ( ! empty( $the_sections ) ) {
					self::load_settings( $opt_name );
				}
			}
		}
		/**
		 * Load Kadence Settings.
		 *
		 * @param string $opt_name Panel opt_name.
		 */
		public static function load_settings( $opt_name = '' ) {
			if ( empty( $opt_name ) ) {
				return;
			}

			if ( ! class_exists( 'Kadence_Settings' ) ) {
				return;
			}
			self::register( $opt_name );
			$args     = self::construct_args( $opt_name );
			$sections = self::construct_sections( $opt_name );
			new Kadence_Settings( $sections, $args );
		}
		/**
		 * Construct global arguments.
		 *
		 * @param string $opt_name Panel opt_name.
		 *
		 * @return array|mixed
		 */
		public static function construct_args( $opt_name ) {
			$args             = isset( self::$args[ $opt_name ] ) ? self::$args[ $opt_name ] : array();
			$args['opt_name'] = $opt_name;

			if ( ! isset( $args['menu_title'] ) ) {
				$args['menu_title'] = ucfirst( $opt_name ) . ' Options';
			}

			if ( ! isset( $args['page_title'] ) ) {
				$args['page_title'] = ucfirst( $opt_name ) . ' Options';
			}

			if ( ! isset( $args['page_slug'] ) ) {
				$args['page_slug'] = $opt_name . '_options';
			}

			return $args;
		}

		/**
		 * Construct option panel sections.
		 *
		 * @param string $opt_name Panel opt_name.
		 *
		 * @return array
		 */
		public static function construct_sections( $opt_name ) {
			$sections = array();

			if ( ! isset( self::$sections[ $opt_name ] ) ) {
				return $sections;
			}

			foreach ( self::$sections[ $opt_name ] as $section_id => $section ) {
				$section['fields'] = self::construct_fields( $opt_name, $section_id );
				$p                 = $section['priority'];

				while ( isset( $sections[ $p ] ) ) {
					$p++;
				}

				$sections[ $p ] = $section;
			}

			ksort( $sections );

			$final_sections = array();

			foreach ( $sections as $section_priority => $section ) {
				$final_sections[ $section['id'] ] = $section;
			}

			return $final_sections;
		}
		/**
		 * Construct option panel fields.
		 *
		 * @param string $opt_name   Panel opt_name.
		 * @param string $section_id ID of section.
		 *
		 * @return array
		 */
		public static function construct_fields( $opt_name = '', $section_id = '' ) {
			$fields = array();

			if ( ! empty( self::$fields[ $opt_name ] ) ) {
				foreach ( self::$fields[ $opt_name ] as $key => $field ) {
					if ( $field['section_id'] === $section_id ) {
						$p = esc_html( $field['priority'] );

						while ( isset( $fields[ $p ] ) ) {
							$p++;
						}
						$fields[ $p ] = $field;
					}
				}
			}

			ksort( $fields );

			return $fields;
		}
		/**
		 * Retrieve all sections from the option panel.
		 *
		 * @param string $opt_name Panel opt_name.
		 *
		 * @return array|mixed
		 */
		public static function get_sections( $opt_name = '' ) {
			if ( '' === $opt_name ) {
				return array();
			}
			self::check_opt_name( $opt_name );

			if ( ! empty( self::$sections[ $opt_name ] ) ) {
				return self::$sections[ $opt_name ];
			}

			return array();
		}
		/**
		 * Create multiple sections of the option panel.
		 *
		 * @param string $opt_name Panel opt_name.
		 * @param array  $sections Section ID.
		 */
		public static function set_sections( $opt_name = '', $sections = array() ) {
			if ( empty( $sections ) || '' === $opt_name ) {
				return;
			}

			self::check_opt_name( $opt_name );

			if ( ! empty( $sections ) ) {
				foreach ( $sections as $section ) {
					self::set_section( $opt_name, $section );
				}
			}
		}
		/**
		 * Sets a single option panel section.
		 *
		 * @param string $opt_name Panel opt_name.
		 * @param array  $section  Section data.
		 * @param bool   $replace  Replaces section instead of creating a new one.
		 */
		public static function set_section( $opt_name = '', $section = array(), $replace = false ) {
			if ( empty( $section ) || '' === $opt_name ) {
				return;
			}

			self::check_opt_name( $opt_name );

			$section['id'] = isset( $section['id'] ) ? $section['id'] : "{$opt_name}_{section}_" . wp_rand( 1, 9999 );

			if ( ! empty( $opt_name ) && is_array( $section ) && ! empty( $section ) ) {
				if ( ! isset( $section['id'] ) && ! isset( $section['title'] ) ) {
					return;
				}

				if ( ! isset( $section['priority'] ) ) {
					$section['priority'] = 10;
				}

				if ( isset( $section['fields'] ) ) {
					if ( ! empty( $section['fields'] ) && is_array( $section['fields'] ) ) {
						self::process_field_array( $opt_name, $section['id'], $section['fields'] );
					}
					unset( $section['fields'] );
				}

				self::$sections[ $opt_name ][ $section['id'] ] = $section;
			}
		}
		/**
		 * Compiles field array data.
		 *
		 * @param string $opt_name   Panel opt_name.
		 * @param string $section_id Section ID.
		 * @param array  $fields     Field data.
		 */
		private static function process_field_array( $opt_name = '', $section_id = '', $fields = array() ) {
			if ( ! empty( $opt_name ) && ! empty( $section_id ) && is_array( $fields ) && ! empty( $fields ) ) {
				foreach ( $fields as $field ) {
					if ( ! is_array( $field ) ) {
						continue;
					}
					self::set_field( $opt_name, $section_id, $field );
				}
			}
		}
		/**
		 * Creates an option panel field and adds to a section.
		 *
		 * @param string $opt_name   Panel opt_name.
		 * @param array  $section_id Section ID this field belongs to.
		 * @param array  $field      Field data.
		 */
		public static function set_field( $opt_name = '', $section_id = '', $field = array() ) {

			if ( ! is_array( $field ) || empty( $field ) || '' === $opt_name || '' === $section_id ) {
				return;
			}

			self::check_opt_name( $opt_name );

			$field['section_id'] = $section_id;

			if ( ! isset( $field['priority'] ) ) {
				$field['priority'] = 10;
			}
			$field['id'] = isset( $field['id'] ) ? $field['id'] : "{$opt_name}_{$section_id}_{$field['type']}_" . wp_rand( 1, 9999 );

			self::$fields[ $opt_name ][ $field['id'] ] = $field;
		}
		/**
		 * Create multiple fields of the option panel and apply to a section.
		 *
		 * @param string $opt_name   Panel opt_name.
		 * @param array  $section_id Section ID this field belongs to.
		 * @param array  $fields     Array of field arrays.
		 */
		public static function set_fields( $opt_name = '', $section_id = '', $fields = array() ) {
			if ( ! is_array( $fields ) || empty( $fields ) || '' === $opt_name || '' === $section_id ) {
				return;
			}
			self::check_opt_name( $opt_name );

			foreach ( $fields as $field ) {
				if ( is_array( $field ) ) {
					self::set_field( $opt_name, $section_id, $field );
				}
			}
		}
		/**
		 * Sets an option into the database.
		 *
		 * @param string $opt_name Panel opt_name.
		 * @param string $key      Option key.
		 * @param string $option   Option value.
		 *
		 * @return bool
		 */
		public static function set_option( $opt_name = '', $key = '', $option = '' ) {
			if ( '' === $key ) {
				return false;
			}

			self::check_opt_name( $opt_name );

			if ( '' !== $opt_name && '' !== $key ) {
				if ( 'network' === self::$args[$opt_name]['database'] ) {
					$setting_options = get_site_option( $opt_name );
				} else {
					$setting_options = get_option( $opt_name );
				}
				$setting_options[ $key ] = $option;
				if ( 'network' === self::$args[$opt_name]['database'] ) {
					return update_site_option( $opt_name, $setting_options );
				} else {
					return update_option( $opt_name, $setting_options );
				}
			} else {
				return false;
			}
		}
		/**
		 * Sets option panel global arguments.
		 *
		 * @param string $opt_name Panel opt_name.
		 * @param array  $args     Argument data.
		 */
		public static function set_args( $opt_name = '', $args = array() ) {
			if ( empty( $args ) || '' === $opt_name ) {
				return;
			}

			self::check_opt_name( $opt_name );

			if ( '' !== $opt_name && ! empty( $args ) && is_array( $args ) ) {
				self::$args[ $opt_name ] = wp_parse_args( $args, self::$args[ $opt_name ] );
			}
		}
		/**
		 * Retrieves single option from the database.
		 *
		 * @param string $opt_name Panel opt_name.
		 * @param string $key      Option key.
		 * @param string $default  Default value.
		 *
		 * @return mixed
		 */
		public static function get_option( $opt_name = '', $key = '', $default = null ) {
			self::check_opt_name( $opt_name );
			if ( ! empty( $opt_name ) && ! empty( $key ) ) {
				global $$opt_name;
				if ( 'network' === self::$args[$opt_name]['database'] ) {
					$values = get_site_option( $opt_name );
				} else {
					$values = get_option( $opt_name );
				}
				$$opt_name = $values;

				if ( ! isset( $values[ $key ] ) ) {
					if ( null === $default ) {
						$field = self::get_field( $opt_name, $key );

						if ( false !== $field ) {
							$sections = self::construct_sections( $opt_name );
							$defaults = self::default_values( $opt_name, $sections );

							if ( isset( $defaults[ $key ] ) ) {
								$default = $defaults[ $key ];
							}
						}
					}
				}
				$value = isset( $values[ $key ] ) ? $values[ $key ] : $default;

				return $value;
			} else {
				return false;
			}
		}
		/**
		 * Creates default options array.
		 *
		 * @param string $opt_name Panel opt_name.
		 * @param array  $sections Panel sections array.
		 *
		 * @return array
		 */
		public static function default_values( $opt_name, $sections = array() ) {
			// We want it to be clean each time this is run.
			if ( isset( self::$options_defaults[ $opt_name ] ) && ! empty( self::$options_defaults[ $opt_name ] ) ) {
				return self::$options_defaults[ $opt_name ];
			}
			if ( ! is_null( $sections ) && ! empty( $sections ) ) {

				// Fill the cache.
				foreach ( $sections as $sk => $section ) {
					if ( isset( $section['fields'] ) ) {
						foreach ( $section['fields'] as $k => $field ) {
							if ( empty( $field['id'] ) && empty( $field['type'] ) ) {
								continue;
							}
							if ( isset( $field['default'] ) ) {
								// phpcs:ignore WordPress.NamingConventions.ValidHookName
								self::$options_defaults[ $opt_name ][ $field['id'] ] = apply_filters( "kadence/{$opt_name}/field/{$field['type']}/defaults", $field['default'], $field );
							} else {
								self::$options_defaults[ $opt_name ][ $field['id'] ] = '';
							}
						}
					}
				}
			}
			return self::$options_defaults[ $opt_name ];
		}
		/**
		 * Register Option for use
		 */
		public static function register( $opt_name = '' ) {
			if ( empty( $opt_name ) || is_array( $opt_name ) ) {
				return;
			}
			register_setting(
				$opt_name . '_group',
				$opt_name,
				array(
					'type'              => 'string',
					'description'       => __( 'Config Kadence Settings Modules', 'kadence-blocks' ),
					'sanitize_callback' => ( isset( self::$args['sanitize'] ) && self::$args['sanitize'] ? 'sanitize_text_field' : '' ),
					'show_in_rest'      => true,
					'default'           => '',
				)
			);
		}
		/**
		 * Check opt_name integrity.
		 *
		 * @param string $opt_name Panel opt_name.
		 */
		public static function check_opt_name( $opt_name = '' ) {
			if ( empty( $opt_name ) || is_array( $opt_name ) ) {
				return;
			}
			if ( ! isset( self::$sections[ $opt_name ] ) ) {
				self::$sections[ $opt_name ] = array();
			}

			if ( ! isset( self::$args[ $opt_name ] ) ) {
				self::$args[ $opt_name ] = array();
			}

			if ( ! isset( self::$fields[ $opt_name ] ) ) {
				self::$fields[ $opt_name ] = array();
			}
			if ( ! isset( self::$options_defaults[ $opt_name ] ) ) {
				self::$options_defaults[ $opt_name ] = array();
			}
		}
	}
	new Kadence_Settings_Engine();
}
