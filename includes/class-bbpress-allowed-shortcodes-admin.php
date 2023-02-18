<?php

if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Allowed Shortcodes admin class.
 *
 * Manages the WordPress admin integrations.
 */
final class BBPress_Allowed_Shortcodes_Admin {
	/**
	 * Singleton instance of the integration
	 */
	private static ?BBPress_Allowed_Shortcodes_Admin $instance = null;

	/**
	 * Integration constructor.
	 *
	 * This constructor is private to force instantiation through the {@see boot} method.
	 */
	private function __construct() {
	}

	/**
	 * Boots the integration.
	 */
	public static function boot(): void {
		if ( self::$instance === null ) {
			self::$instance = new self();

			add_action( 'admin_menu', [ self::$instance, 'register_plugin_options_page' ] );
			add_action( 'admin_init', [ self::$instance, 'register_plugin_settings' ] );
		}
	}

	/**
	 * Fetch the integration's singleton instance.
	 *
	 * Ensures only one instance of the integration is loaded or can be loaded.
	 *
	 * @throws RuntimeException if trying to fetch the singleton instance before the integration has been booted.
	 */
	public static function instance(): self {
		if ( self::$instance === null ) {
			throw new RuntimeException( 'The bbPress Allowed Shortcodes admin integration has not been booted.' );
		}

		return self::$instance;
	}


	/**
	 * Registers the plugin's options page.
	 */
	public function register_plugin_options_page(): void {
		add_options_page(
			__( 'bbPress Allowed Shortcodes', 'bbpress-allowed-shortcodes' ),
			__( 'bbPress Allowed Shortcodes', 'bbpress-allowed-shortcodes' ),
			'administrator',
			'bbpress-allowed-shortcodes',
			[ $this, 'show_settings_page' ],
		);
	}

	/**
	 * Register the plugin's settings.
	 */
	public function register_plugin_settings(): void {
		register_setting( 'bbpress-allowed-shortcodes', 'bbpress_allowed_shortcodes_list' );

		add_settings_section(
			'bbpress-allowed-shortcodes-general-settings',
			__( 'General Configuration', 'bbpress-allowed-shortcodes' ),
			'__return_null',
			'bbpress-allowed-shortcodes',
		);

		add_settings_field(
			'bbpress_allowed_shortcodes_list',
			__( 'Allowed Shortcodes', 'bbpress-allowed-shortcodes' ),
			[ $this, 'print_bbpress_allowed_shortcodes_list_field' ],
			'bbpress-allowed-shortcodes',
			'bbpress-allowed-shortcodes-general-settings',
		);
	}

	/**
	 * Prints the allowed shortcodes list field.
	 */
	public function print_bbpress_allowed_shortcodes_list_field( array $args ): void {
		echo sprintf(
			'<input type="text" id="bbpress_allowed_shortcodes_list" name="bbpress_allowed_shortcodes_list" class="regular-text code" value="%s" />',
			esc_attr( get_option( 'bbpress_allowed_shortcodes_list', '' ) )
		);

		echo '<p class="description">A comma separated list of shortcodes that are allowed in bbPress content. For example, to add the shortcodes <code>[test]</code> and <code>[test2]</code>, enter "test,test2".</p>';
	}

	/**
	 * Method handling requests for the settings page.
	 *
	 * @return void
	 */
	public function show_settings_page() {
		include plugin_dir_path( BBPRESS_ALLOWED_SHORTCODES_PLUGIN_FILE ) . 'views/admin/html-page-plugin-settings.php';
	}
}
