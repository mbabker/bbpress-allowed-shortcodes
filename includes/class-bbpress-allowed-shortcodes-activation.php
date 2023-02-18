<?php

if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Activation integration class.
 *
 * Responsible for registering hooks to run when the plugin is activated, deactivated, and checking for post-update actions.
 */
final class BBPress_Allowed_Shortcodes_Activation {
	/**
	 * Singleton instance of the integration
	 */
	private static ?BBPress_Allowed_Shortcodes_Activation $instance = null;

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

			register_activation_hook( BBPRESS_ALLOWED_SHORTCODES_PLUGIN_FILE, [ self::$instance, 'install' ] );
			add_action( 'init', [ self::$instance, 'check_and_update_plugin' ] );
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
			throw new RuntimeException( 'The bbPress Allowed Shortcodes activation integration has not been booted.' );
		}

		return self::$instance;
	}

	/**
	 * Ensures the plugin is updated and applies updates if necessary.
	 */
	public function check_and_update_plugin(): void {
		if ( ! defined( 'IFRAME_REQUEST' ) && version_compare( get_option( 'bbpress_allowed_shortcodes_version' ), BBPRESS_ALLOWED_SHORTCODES_VERSION, '<' ) ) {
			$this->install();

			/**
			 * Fires after the plugin is updated.
			 */
			do_action( 'bbpress_allowed_shortcodes_updated' );
		}
	}

	/**
	 * Activation hook to run when the plugin is installed.
	 */
	public function install(): void {
		if ( ! is_blog_installed() ) {
			return;
		}

		// Check if we are not already running this routine
		if ( get_transient( 'bbpress_allowed_shortcodes_installing' ) === 'yes' ) {
			return;
		}

		// If we made it to here nothing is running yet, lets set the transient now
		set_transient( 'bbpress_allowed_shortcodes_installing', 'yes', MINUTE_IN_SECONDS * 10 );

		$this->set_default_options();
		$this->update_plugin_version();

		delete_transient( 'bbpress_allowed_shortcodes_installing' );

		/**
		 * Fires after the plugin is installed.
		 */
		do_action( 'bbpress_allowed_shortcodes_installed' );
	}

	/**
	 * Set the default options for the plugin.
	 */
	private function set_default_options(): void {
		add_option( 'bbpress_allowed_shortcodes_list', '', '', false );
	}

	/**
	 * Updates the plugin's version option
	 */
	private function update_plugin_version(): void {
		delete_option( 'bbpress_allowed_shortcodes_version' );
		add_option( 'bbpress_allowed_shortcodes_version', BBPRESS_ALLOWED_SHORTCODES_VERSION, '', false );
	}
}
