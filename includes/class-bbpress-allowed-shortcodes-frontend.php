<?php

if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Allowed Shortcodes admin class.
 *
 * Manages the WordPress frontend integrations.
 */
final class BBPress_Allowed_Shortcodes_Frontend {
	/**
	 * Singleton instance of the integration
	 */
	private static ?BBPress_Allowed_Shortcodes_Frontend $instance = null;

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

			add_filter( 'wp_trim_excerpt', [ self::$instance, 'do_shortcodes_in_excerpts' ], 10, 2 );

			// Allow shortcodes in comments
			add_filter( 'get_comment_text', 'bbpress_allowed_shortcodes_do_shortcode' );

			// Allow shortcodes in bbPress content
			add_filter( 'bbp_get_reply_content', 'bbpress_allowed_shortcodes_do_shortcode' );
			add_filter( 'bbp_get_topic_content', 'bbpress_allowed_shortcodes_do_shortcode' );

			// Allow shortcodes in BuddyPress content
			add_filter( 'bp_get_the_topic_post_content', 'bbpress_allowed_shortcodes_do_shortcode' );
			add_filter( 'bp_get_activity_content_body', 'bbpress_allowed_shortcodes_do_shortcode', 1 );
			add_filter( 'bp_get_activity_content', 'bbpress_allowed_shortcodes_do_shortcode', 1 );
			add_filter( 'bp_get_the_thread_message_content', 'bbpress_allowed_shortcodes_do_shortcode' );
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
			throw new RuntimeException( 'The bbPress Allowed Shortcodes frontend integration has not been booted.' );
		}

		return self::$instance;
	}

	/**
	 * Re-runs {@see wp_trim_excerpt} on the content without stripping shortcodes.
	 *
	 * @param string $text The trimmed text.
	 * @param string $raw_excerpt The text prior to trimming.
	 *
	 * @return string The filtered trimmed text.
	 *
	 * @see https://wordpress.stackexchange.com/questions/9453/stop-shortcode-stripping-in-category-and-archive-pages/9456#9456
	 */
	public function do_shortcodes_in_excerpts( string $text, string $raw_excerpt ): string {
		if ( $raw_excerpt !== '' ) {
			return $text;
		}

		$text = get_the_content( '' );

		/** This filter is documented in wp-includes/post-template.php */
		$text = apply_filters( 'the_content', $text ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
		$text = str_replace( ']]>', ']]&gt;', $text );

		/* translators: Maximum number of words used in a post excerpt. */
		$excerpt_length = (int) _x( '55', 'excerpt_length' );

		/** This filter is documented in wp-includes/formatting.php */
		$excerpt_length = (int) apply_filters( 'excerpt_length', $excerpt_length ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound

		/** This filter is documented in wp-includes/formatting.php */
		$excerpt_more = apply_filters( 'excerpt_more', ' [&hellip;]' ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound

		return wp_trim_words( $text, $excerpt_length, $excerpt_more );
	}
}
