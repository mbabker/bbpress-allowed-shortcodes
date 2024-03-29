<?php

if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Autoloader class.
 *
 * Adapted from WooCommerce' WC_Autoloader
 */
final class BBPress_Allowed_Shortcodes_Autoloader {
	/**
	 * Singleton instance of the plugin's autoloader
	 */
	private static ?BBPress_Allowed_Shortcodes_Autoloader $instance = null;

	/**
	 * Flag tracking whether the autoloader has been registered
	 */
	private static bool $registered = false;

	/**
	 * Path to the includes directory.
	 */
	private string $include_path;

	/**
	 * Autoloader constructor.
	 *
	 * This constructor is private to force instantiation through the {@see register} method.
	 *
	 * @param string $include_path Path to the includes directory.
	 */
	private function __construct( string $include_path ) {
		$this->include_path = $include_path;
	}

	/**
	 * Register the autoloader
	 */
	public static function register(): void {
		if ( ! self::$registered ) {
			self::$instance = new self( untrailingslashit( plugin_dir_path( BBPRESS_ALLOWED_SHORTCODES_PLUGIN_FILE ) ) . '/includes/' );

			spl_autoload_register( [ self::$instance, 'autoload' ] );

			self::$registered = true;
		}
	}

	/**
	 * Unregister the autoloader
	 */
	public static function unregister(): void {
		if ( self::$registered ) {
			spl_autoload_unregister( [ self::$instance, 'autoload' ] );

			self::$instance   = null;
			self::$registered = false;
		}
	}

	/**
	 * Auto-load function for plugin classes.
	 *
	 * @param string $class_name Class name.
	 */
	public function autoload( string $class_name ): void {
		$class_name = strtolower( $class_name );

		// Only attempt to load classes if they look like they come from this plugin
		if ( strpos( $class_name, 'bbpress_allowed_shortcodes_' ) !== 0 ) {
			return;
		}

		$file = $this->get_file_name_from_class_name( $class_name );

		$this->include_file_if_readable( $this->include_path . $file );
	}

	/**
	 * Take a class name and turn it into a file name.
	 *
	 * @param string $class_name Class name.
	 */
	private function get_file_name_from_class_name( string $class_name ): string {
		return 'class-' . str_replace( '_', '-', $class_name ) . '.php';
	}

	/**
	 * Scope isolated method to include a class file.
	 *
	 * @param string $path File path.
	 */
	private function include_file_if_readable( string $path ): void {
		if ( $path && is_readable( $path ) ) {
			include_once $path;
		}
	}
}
