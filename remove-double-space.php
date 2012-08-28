<?php
/*
Plugin Name: Remove Double Space
Version: 0.3
Plugin URI: http://www.josheaton.org/blog/remove-double-space-plugin/
Author: Josh Eaton
Author URI: http://www.josheaton.org/
Description: Replace duplicate spaces with single spaces in posts.
License: GPL2
*/

/*  Copyright 2010-2012  Josh Eaton  (email : josh@josheaton.org)

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

if (!class_exists( 'JJERemoveDoubleSpace' )) {

	class JJERemoveDoubleSpace {

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
			add_filter( 'plugin_action_links', array(&$this, 'add_settings_link'), 10, 2 );
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
			// Merge options from database with defaults
			$options = wp_parse_args(get_option($this->optionname), $this->plugin_options);

			// Update or add options to db
			update_option( $this->optionname, $options );
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
			// Add a sub-menu to the 'Settings' menu
			add_options_page( $this->longname, $this->shortname, 'manage_options', $this->hook, array(&$this, 'plugin_options_create_page') );
		}

		function plugin_admin_init() {
			// Register and configure plugin group, sections and fields
			register_setting( 'jje_rds_options_group', $this->optionname, array(&$this, 'plugin_options_validate') );
			add_settings_section('jje_rds_plugin_main', '', array(&$this, 'plugin_settings_section'), $this->hook);
			add_settings_field('remove_all_duplicates', 'Remove all duplicates', array(&$this, 'plugin_setting_rad'), $this->hook, 'jje_rds_plugin_main');
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
					<?php do_settings_sections($this->hook); ?>
					<p class="submit">
					<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
					</p>
					<br /><hr />
					<p>If you like this plugin, please consider buying me a cup of coffee!
					<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&amp;hosted_button_id=JKWPDXGYLASCY"><img src="https://www.paypal.com/en_US/i/btn/btn_donateCC_LG.gif" alt="Donate here" /></a>
					</p>
					<p>For any suggestions, feedback or bugs, please use the support forum or the links below:<br />
					<a href="<?php echo $this->homepage; ?>">Plugin Homepage</a> | <a href="http://www.jjeaton.com/">Author Homepage</a></p>
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
			$options = get_option($this->optionname);
			echo '<input name="' . $this->optionname . '[remove_all_duplicates]" type="checkbox" value="1"' . checked($options['remove_all_duplicates'], true, false) . ' />';
		}

		function plugin_options_validate( $input ) {
			// Merge input array with options saved in the db
			$options = wp_parse_args($input, get_option( $this->optionname ) );

			// Check if remove_all_duplicates is set, then set to boolean value
			if (isset($input['remove_all_duplicates']) && $input['remove_all_duplicates'] == '1' ) {
				$options['remove_all_duplicates'] = true;
			} else {
				$options['remove_all_duplicates'] = false;
			}

			return $options;
		}

		function plugin_options_url() {
			return admin_url( 'options-general.php?page='.$this->hook );
		}

		/**
		 * Add a link to the settings page to the plugins list
		 */
		function add_settings_link( $links, $file ) {
			static $this_plugin;
			if( empty($this_plugin) ) $this_plugin = $this->filename;
			if ( $file == $this_plugin ) {
				$settings_link = '<a href="' . $this->plugin_options_url() . '">' . __('Settings') . '</a>';
				array_unshift( $links, $settings_link );
			}
			return $links;
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
			$options = get_option( $this->optionname );
			$sanitized_text = $text;

			// Check if the text is UTF-8
			// Had a lot of issues trying to match unicode whitespace
			// See resolution here: http://stackoverflow.com/questions/3137296/matching-duplicate-whitespace-with-preg-replace
			if ( seems_utf8( $text ) ) {
				if ( isset( $options['remove_all_duplicates'] ) && $options['remove_all_duplicates'] ) {
					$sanitized_text = preg_replace( '/[\p{Z}\s]{2,}/u', ' ', $text );
				}
			} else {
				if ( isset( $options['remove_all_duplicates'] ) && $options['remove_all_duplicates'] ) {
					$sanitized_text = preg_replace( '/\s\s+/', ' ', $sanitized_text );
				}
			}
			return $sanitized_text;
		} // end jje_replace_double_space()

	} // class JJERemoveDoubleSpace

	// Instantiate RDS class
	$jje_remove_double_space = new JJERemoveDoubleSpace();
} // if !class_exists
?>
