<?php /*

**************************************************************************

Copyright (C) 2013 Anton Channing

***** GPL3 *****
This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.

**************************************************************************/

	// This function essentially reruns wp_trim_excerpt() on the content 
	// but without stripping shortcodes.
	// Thanks to John P Bloch on WordPress Answers for this function:
	// http://wordpress.stackexchange.com/a/9456/5077
	function bbp_whitelist_do_shortcodes_in_excerpts( $text, $raw_text ) {
		if(empty($raw_text)) {
			$text = get_the_content('');
			$text = apply_filters('the_content', $text);
			$text = str_replace(']]>', ']]&gt;', $text);
			$text = strip_tags($text);
			$excerpt_length = apply_filters('excerpt_length', 55);
			$excerpt_more = apply_filters('excerpt_more', ' ' . '[...]');
			$words = preg_split("/[\n\r\t ]+/", $text, $excerpt_length + 1, PREG_SPLIT_NO_EMPTY);

			if ( count($words) > $excerpt_length ) {
				array_pop($words);
				$text = implode(' ', $words);
				$text = $text . $excerpt_more;
			} else {
				$text = implode(' ', $words);
			}
		}
		return $text;
	}
	add_filter( 'wp_trim_excerpt', 'bbp_whitelist_do_shortcodes_in_excerpts', 10, 2 );
	

	// These are modified versions of the wordpress functions for doing shortcodes,
	// that work with the 'safelist' of shortcodes instead of them all.  
	function bbp_whitelist_do_shortcode($content) {
		global $shortcode_tags;
		global $bbp_sc_whitelist;

		if (empty($shortcode_tags) || !is_array($shortcode_tags))
			return $content;

		$tagnames = array();
		foreach($shortcode_tags as $tag => $func) {
			if(in_array(strtolower($tag),$bbp_sc_whitelist->bbp_shortcode_whitelist)) $tagnames[] = $tag;
		}

		if (empty($tagnames)) return $content;

		$pattern = bbp_whitelist_get_shortcode_regex();
		return preg_replace_callback('/'.$pattern.'/s', 'bbp_whitelist_do_shortcode_tag', $content);
	}
		
	function bbp_whitelist_get_shortcode_regex() {
		global $shortcode_tags;
		global $bbp_sc_whitelist;

		$tagnames = array();
		foreach($shortcode_tags as $tag => $func) {
			if(in_array(strtolower($tag),$bbp_sc_whitelist->bbp_shortcode_whitelist)) $tagnames[] = $tag;
		}

		$tagregexp = join( '|', array_map('preg_quote', $tagnames) );

		// WARNING! Do not change this regex without changing do_shortcode_tag() and strip_shortcodes()
		return '(.?)\[('.$tagregexp.')\b(.*?)(?:(\/))?\](?:(.+?)\[\/\2\])?(.?)';
	}

	function bbp_whitelist_do_shortcode_tag( $m ) {
		global $shortcode_tags;
		global $bbp_sc_whitelist;

		$safe_sortcode_tags = array();
		foreach($shortcode_tags as $tag => $func) {
			if(in_array(strtolower($tag),$bbp_sc_whitelist->bbp_shortcode_whitelist)) $safe_sortcode_tags[$tag] = $func;
		}


		// allow [[foo]] syntax for escaping a tag
		if ( $m[1] == '[' && $m[6] == ']' ) {
			return substr($m[0], 1, -1);
		}

		$tag = $m[2];
		$attr = shortcode_parse_atts( $m[3] );

		if(!isset($safe_sortcode_tags[$tag])) return $m[0]; // Not in safe list, so return tag unchanged.

		if ( isset( $m[5] ) ) {
			// enclosing tag - extra parameter
			return $m[1] . call_user_func( $safe_sortcode_tags[$tag], $attr, $m[5], $tag ) . $m[6];
		} else {
			// self-closing tag
			return $m[1] . call_user_func( $safe_sortcode_tags[$tag], $attr, NULL,  $tag ) . $m[6];
		}
	}
?>
