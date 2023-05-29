<?php

/**
 * Plugin Name: bbPress Allowed Shortcodes
 * Plugin URI: https://michaels.website
 * Update URI: https://michaels.website
 * Description: WordPress plugin adding support for shortcodes to bbPress content, based on the <a href="https://wordpress.org/plugins/bbpress2-shortcode-whitelist/">bbPress2 shortcode whitelist plugin</a>.
 * Version: 1.0.0
 * Author: Michael Babker
 * Author URI: https://michaels.website
 * Text Domain: bbpress-allowed-shortcodes
 * Domain Path: /languages
 * License: GPL-3.0+
 * License URI: https://www.gnu.org/licenses/gpl-3.0.txt
 * Requires at least: 6.1
 * Requires PHP: 7.4
 * Tested up to: 6.2
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! defined( 'BBPRESS_ALLOWED_SHORTCODES_PLUGIN_FILE' ) ) {
	/**
	 * Absolute path to this plugin file, for quick reference within the plugin classes
	 *
	 * @var string
	 */
	define( 'BBPRESS_ALLOWED_SHORTCODES_PLUGIN_FILE', __FILE__ );
}

if ( ! defined( 'BBPRESS_ALLOWED_SHORTCODES_VERSION' ) ) {
	/**
	 * Plugin version
	 *
	 * @var string
	 */
	define( 'BBPRESS_ALLOWED_SHORTCODES_VERSION', '1.0.0' );
}

// Include the functions files
require plugin_dir_path( BBPRESS_ALLOWED_SHORTCODES_PLUGIN_FILE ) . 'includes/functions.php';

// Include the autoloader and initialize it
require plugin_dir_path( BBPRESS_ALLOWED_SHORTCODES_PLUGIN_FILE ) . 'includes/class-bbpress-allowed-shortcodes-autoloader.php';

BBPress_Allowed_Shortcodes_Autoloader::register();

// Initialize the plugin instance and resources
BBPress_Allowed_Shortcodes_Plugin::boot();
