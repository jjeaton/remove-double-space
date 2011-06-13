<?php
/*
Plugin Name: Remove Double Space
Version: 0.2
Plugin URI: http://www.jjeaton.com/blog/remove-double-space-plugin/ 
Author: Josh Eaton
Author URI: http://www.jjeaton.com/
Description: Replace duplicate spaces with single spaces in posts.
License: GPL2
*/

/*  Copyright 2010  Josh Eaton  (email : josh at jjeaton com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/**
 * Main plugin Class
 *
 * Class that holds all functions for the class
 *
 * @since 0.2
 * @param none
 * @return none
 */
 
if ( is_admin() && ( !defined('DOING_AJAX') || !DOING_AJAX ) && !class_exists( 'JJERemoveDoubleSpace' ) ) {

	class JJERemoveDoubleSpace {

		// Set defaults to false for all options
		var $hook           = 'jje-remove-double-space';
		var $filename	    = 'remove-double-space/remove-double-space.php';
		var $longname	    = 'Remove Double Space Options';
		var $shortname	    = 'Remove Spaces';
		var $optionname     = 'jje_rds_plugin_options';
		var $homepage       = 'http://www.jjeaton.com/blog/remove-double-space-plugin/';
		var $plugin_options = array(
			'remove_all_duplicates' => false
		);
		
		/**
		 * JJERemoveDoubleSpace Constructor
		 *
		 * Adds hooks, sets up objects for use
		 *
		 * @since 0.2
		 * @param none
		 * @return instance of JJERemoveDoubleSpace
		 */
		function __construct() {
			register_activation_hook( __FILE__, array(&$this,'jje_install_rds') );
			add_action( 'admin_menu', array(&$this, 'jje_rds_menu') );
			add_action( 'admin_init', array(&$this, 'plugin_admin_init') );
			add_filter( 'the_content', array(&$this, 'jje_replace_double_space') ); // Run replacement everywhere 'the_content' is called (posts/feeds/etc.)
		}
		
		/**
		 * Initialize plugin
		 *
		 * Initializes plugin options on activation.
		 *
		 * @since 0.1
		 * @param none
		 * @return none
		 */
		function jje_install_rds() {
			error_log('Plugin was activated!');
			// Initialize plugin options
			//add_option( 'jje_rds_plugin_options', $plugin_options );
		}
		
		/**
		 * Adds an options page to the post menu and registers settings
		 *
		 * Uses Settings API to add an options page to the post menu and register settings
		 *
		 * @since 0.1
		 * @param none
		 * @return none
		 */
		function jje_rds_menu() {
			add_options_page( 'Remove Double Space Options', 'Remove Spaces', 'manage_options', 'jje-remove-double-space', array(&$this, 'plugin_options_create_page') );
		}
		
		function plugin_admin_init() {
			// Initialize plugin options
			register_setting( 'jje_rds_options_group', 'jje_rds_plugin_options', array(&$this, 'plugin_options_validate') );
			add_settings_section('jje_rds_plugin_main', '', array(&$this, 'plugin_settings_section'), 'jje-remove-double-space');
			add_settings_field('remove_all_duplicates', 'Remove all duplicates', array(&$this, 'plugin_setting_rad'), 'jje-remove-double-space', 'jje_rds_plugin_main');
		}

		/**
		 * Creates the page that controls the plugin options
		 *
		 * Uses Settings API to create a form and update appropriate plugin options in the database
		 *
		 * @since 0.1
		 * @param none
		 * @return none
		 */
		function plugin_options_create_page() {
			if ( !current_user_can( 'manage_options' ) )  {
				wp_die( __('You do not have sufficient permissions to access this page.') );
			}
		?>
			<div class="wrap">
				<h2>Remove Double Space Options</h2>
				<form method="post" action="options.php">
					<?php settings_fields('jje_rds_options_group'); ?>
					<?php do_settings_sections('jje-remove-double-space'); ?>
					<p class="submit">
					<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
					</p>
					<br /><hr />
					<p>If you like this plugin, please consider buying me a cup of coffee!
					<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&amp;hosted_button_id=JKWPDXGYLASCY"><img src="https://www.paypal.com/en_US/i/btn/btn_donateCC_LG.gif" alt="Donate here" /></a>
					</p>
					<p>For any suggestions, feedback or bugs, please use the support forum or the links below:<br />
					<a href="http://www.jjeaton.com/blog/remove-double-space-plugin/">Plugin Homepage</a> | <a href="http://www.jjeaton.com/">Author Homepage</a></p>
				</form>
			</div>
		<?php 
		}

		/* Settings API functions
		 * Create settings sections, fields, and validate options before saving
		 * @since 0.2
		 */
		function plugin_settings_section() {
			echo '<p>Enable the setting below to turn on duplicate space replacement.</p>';
		}
		
		function plugin_setting_rad() {
			$options = get_option('jje_rds_plugin_options');
			echo '<input name="jje_rds_plugin_options[remove_all_duplicates]" type="checkbox" value="1"' . checked($options['remove_all_duplicates'], true, false) . ' />';
			//. ($options['remove_all_duplicates'] ? ' checked="checked"' : "") . ' />';
		}
		
		function plugin_options_validate( $input ) {

			error_log('Options before: ' . serialize(get_option( 'jje_rds_plugin_options')));
			
			
			if (isset($input['remove_all_duplicates']) && $input['remove_all_duplicates'] == '1' ) {
				error_log('Options set to true!');
				$options['remove_all_duplicates'] = true;
			} else {
				error_log('Options set to false!');
				$options['remove_all_duplicates'] = false;
				//$options['remove_all_duplicates'] = 0;
			}
			
			error_log('Options after input: ' . serialize($options));
			$options = wp_parse_args($options, get_option( 'jje_rds_plugin_options' ) );
			//$options = wp_parse_args($input, $this->plugin_options );
			error_log('Options after: ' . serialize($options));
			return $options;
			
		}
		
		/**
		 * Replaces 2 consecutive spaces with one space.
		 *
		 * Will replace any consecutive duplicated whitespace with a single space
		 *
		 * @since 0.1
		 * @param string $text The text to have double-spaces replaced with single spaces
		 * @return string The converted text
		 */
		function jje_replace_double_space( $text ) {
			$options = get_option( 'jje_rds_plugin_options' );
			$sanitized_text = $text;
			
			// Check if the text is UTF-8
			// Had a lot of issues trying to match unicode whitespace
			// See resolution here: http://stackoverflow.com/questions/3137296/matching-duplicate-whitespace-with-preg-replace
			if ( seems_utf8( $text ) ) {
				if ( isset( $options['remove_all_duplicates'] ) && $options['remove_all_duplicates'] == 1 ) {
					$sanitized_text = preg_replace( '/[\p{Z}\s]{2,}/u', ' ', $text );
				}
			} else {
				if ( isset( $options['remove_all_duplicates'] ) && $options['remove_all_duplicates'] == 1 ) {
					$sanitized_text = preg_replace( '/\s\s+/', ' ', $sanitized_text );
				}
			}

			return $sanitized_text;
		} // end jje_replace_double_space()

	} // class JJERemoveDoubleSpace
} // if !class_exists

// Instantiate RDS class
$jje_remove_double_space = new JJERemoveDoubleSpace();

?>
