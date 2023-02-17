<?php /*

**************************************************************************
Copyright (C) 2011 Anton Channing

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

	class bbPressShortcodeWhitelist {
		public $bbp_shortcode_whitelist = array();

		// Plugin initialization
		function __construct() {
			// This version only supports WP 2.5+ (learn to upgrade please!)
			if ( !function_exists('add_shortcode') ) return;

			$this->set_enabled_codes();
		}	

		function add_filters() {
			// Add safe shortcode support to relevant filters 

			// Wordpress
			add_filter( 'get_comment_text', 'bbp_whitelist_do_shortcode' ); //adds safe shortcodes to blog comments

			// bbPress
			add_filter( 'bbp_get_reply_content', 'bbp_whitelist_do_shortcode' ); //adds safe shortcodes to forum posts

			// BuddyPress
			add_filter( 'bp_get_the_topic_post_content', 'bbp_whitelist_do_shortcode' ); //adds safe shortcodes to buddypress group forum posts
			add_filter( 'bp_get_activity_content_body', 'bbp_whitelist_do_shortcode', 1 ); //adds safe shortcodes to buddypress activity 
			add_filter( 'bp_get_activity_content', 'bbp_whitelist_do_shortcode', 1 ); //adds safe shortcodes to buddypress activity replies
			add_filter( 'bp_get_the_thread_message_content', 'bbp_whitelist_do_shortcode' ); //adds safe shortcodes to private messages
		}

		function get_known_plugins($tag_array) {
			$plugins = array();
			foreach((array)$tag_array as $tag) {
				$plugin = $this->get_known_plugin_by_tag($tag);
				if(false !== $plugin) $plugins[] = $plugin;
			}
			return $plugins;
		}

		function get_known_plugin_by_tag($tag) {
			$enabled_plugins = get_option('bbpscwl_enabled_plugins'); 

			if($enabled_plugins == '') $enabled_plugins = array();
			elseif(!is_array($enabled_plugins)) $enabled_plugins = unserialize($enabled_plugins);

			switch($tag) {
				case 'bbpress-bbcode':
					$plugin_name = 'bbPress2 BBCode';
					$plugin_author = 'Anton Channing';
					$shortcodes = array('b','i','u','s','img','url','center','quote','color','code','size','ul','ol','li','spoiler','guest','user');
					break;
				case 'video-audio-bbcodes':
					$plugin_name = "Video Audio BBCodes";
					$plugin_author = 'Anton Channing';

					$shortcodes = array('youtube','googlevideo','gvideo','vimeo','freesound');
					break;
				case '8tracks-shortcode':
					$plugin_name = "8tracks Shortcode";
					$plugin_author = 'Jonathan Martin';

					$shortcodes = array('8tracks');
					break;
				default:
					return false;
					break;
			}
			if(in_array($tag,$enabled_plugins)) $selected = ' checked="checked" '; else $selected = '';
			return array('name'=>$plugin_name,'tag'=>$tag,'author'=>$plugin_author,'shortcodes'=>$shortcodes,'selected'=>$selected);
		}

		function get_supported_plugins() {
			//Detect supported shortcode plugins
			$supported_plugins = array();
			if (is_plugin_active('bbpress-bbcode/bbpress2-bbcode.php')) {
			    	//plugin is activated
				$supported_plugins[] = $this->get_known_plugin_by_tag('bbpress-bbcode');
			}
			if (is_plugin_active('video-audio-bbcodes/video-audio-bbcodes.php')) {
			    	//plugin is activated
				$supported_plugins[] = $this->get_known_plugin_by_tag('video-audio-bbcodes');
			}

			return $supported_plugins;
		}
	
		function get_verified_plugins() {
			$verified_plugins = array();
			if (is_plugin_active('8tracks-shortcode/8tracks_shortcode.php')) {
			    	//plugin is activated
				$verified_plugins[] = $this->get_known_plugin_by_tag('8tracks-shortcode');
			}
			return $verified_plugins;
		}

		function get_other_plugins() {
			global $bbpscwl_selfdeclared_plugins;
			$other_plugins = array();

			$enabled_plugins = get_option('bbpscwl_enabled_plugins');  
			if($enabled_plugins == '') $enabled_plugins = array();
			elseif(!is_array($enabled_plugins)) $enabled_plugins = unserialize($enabled_plugins);

			if(!isset($bbpscwl_selfdeclared_plugins)) $bbpscwl_selfdeclared_plugins = array();
			foreach($bbpscwl_selfdeclared_plugins as $key => $plugin) {
				// Check if the plugin has a tag clash.  It may have been verified, but still have self declare code...
				if($this->get_known_plugin_by_tag($plugin['tag']) == false) {
					if(in_array($plugin['tag'],$enabled_plugins)) $selected = ' checked="checked" '; else $selected = '';
					$plugin['selected'] = $selected;

					$other_plugins[] =  $plugin;
				}
			}
			return $other_plugins;
		}

		function get_enabled_plugins() {
			$enabled_plugin_tags = get_option('bbpscwl_enabled_plugins');  
			if($enabled_plugin_tags == '') return array();
			elseif(!is_array($enabled_plugin_tags)) $enabled_plugin_tags = unserialize($enabled_plugin_tags);

			$enabled_plugins = $this->get_known_plugins($enabled_plugin_tags);

			$other_plugins = $this->get_other_plugins();
			foreach($other_plugins as $plugin) {
				if(in_array($plugin['tag'],$enabled_plugin_tags)) $enabled_plugins[] = $plugin;
			}
			return $enabled_plugins;
		}
		function set_enabled_codes() {
			$enabled_codes = explode(',',get_option('bbpscwl_enabled_codes'));  
			$enabled_plugins = $this->get_enabled_plugins();
			foreach($enabled_plugins as $plugin) {
				$enabled_codes = array_merge($plugin['shortcodes'],$enabled_codes);
			}

			$this->bbp_shortcode_whitelist = $enabled_codes;
		}
	}
?>
