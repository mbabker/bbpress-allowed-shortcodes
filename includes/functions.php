<?php

if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Wrapper for {@see do_shortcode} which restricts the function to only allowed shortcodes.
 *
 * @param string $content     Content to search for shortcodes.
 * @param bool   $ignore_html When true, shortcodes inside HTML elements will be skipped.
 *                            Default false.
 *
 * @return string Content with shortcodes filtered out.
 */
function bbpress_allowed_shortcodes_do_shortcode( string $content, bool $ignore_html = false ): string {
	add_filter( 'pre_do_shortcode_tag', 'bbpress_allowed_shortcodes_pre_do_shortcode_tag', 1, 4 );

	$content = do_shortcode( $content, $ignore_html );

	remove_filter( 'pre_do_shortcode_tag', 'bbpress_allowed_shortcodes_pre_do_shortcode_tag', 1 );

	return $content;
}

/**
 * Retrieves the list of allowed shortcodes.
 *
 * @return list<string> The list of allowed shortcodes.
 */
function bbpress_allowed_shortcodes_get_allowed_shortcode_list(): array {
	static $allowed_shortcodes = null;

	if ( $allowed_shortcodes === null ) {
		/**
		 * Filters the list of allowed shortcodes.
		 *
		 * @param list<string> $allowed_shortcodes The list of allowed shortcodes.
		 */
		$allowed_shortcodes = apply_filters(
			'bbpress_allowed_shortcodes_list',
			explode( ',', get_option( 'bbpress_allowed_shortcodes_list', '' ) ),
		);
	}

	return $allowed_shortcodes;
}

/**
 * Checks if the shortcode is an allowed shortcode, short-circuiting the {@see do_shortcode} function if not allowed.
 *
 * @param false|string $output Short-circuit return value. Either false or the value to replace the shortcode with.
 * @param string       $tag    Shortcode name.
 * @param array|string $attr   Shortcode attributes array or empty string.
 * @param array        $m      Regular expression match array.
 *
 * @return false|string The list of allowed shortcodes.
 */
function bbpress_allowed_shortcodes_pre_do_shortcode_tag( $output, $tag, $attr, $m ) {
	// Only run if another hook hasn't already short-circuited the process
	if ( $output !== false ) {
		return $output;
	}

	if ( ! in_array( $tag, bbpress_allowed_shortcodes_get_allowed_shortcode_list(), true ) ) {
		return $m[5] ?? null;
	}

	return $output;
}
