<?php
/*
Plugin Name: Remove Double Space
Version: 0.1
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

register_activation_hook( __FILE__, 'jje_install_rds' );

/**
 * Initialize plugin
 *
 * Initializes plugin options on activation. Handles future upgrade tasks.
 *
 * @param none
 * @return none
 */
function jje_install_rds() {
	// Set defaults to false for all options
	$jje_rds_plugin_options = array(
		'remove_all_duplicates' => 0
	);
	
	// Initialize plugin options
	add_option( 'jje_rds_plugin_options', $jje_rds_plugin_options );
}

add_action( 'admin_menu', 'jje_rds_menu' );

/**
 * Adds an options page to the post menu and registers settings
 *
 * Uses Settings API to add an options page to the post menu and register settings
 *
 * @param none
 * @return none
 */
function jje_rds_menu() {
	add_posts_page( 'Remove Double Space Options', 'Remove Double Space', 'manage_options', 'jje-remove-double-space', 'jje_rds_options_do_page' );

	// Initialize plugin options
	register_setting( 'jje_rds_options', 'jje_rds_plugin_options' );
}

/**
 * Creates the page that controls the plugin options
 *
 * Uses Settings API to create a form and update appropriate plugin options in the database
 *
 * @param none
 * @return none
 */
function jje_rds_options_do_page() {
	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __('You do not have sufficient permissions to access this page.') );
	}
?>
	<div class="wrap">
		<h2>Remove Double Space Options</h2>
		<form method="post" action="options.php">
			<?php settings_fields('jje_rds_options'); ?>
			<?php $options = get_option('jje_rds_plugin_options'); ?>
			<p>Enable the setting below to turn on duplicate space replacement.</p>
			<table class="form-table">
				<tr valign="top"><th scope="row">Remove all duplicates</th>
					<td><input name="jje_rds_plugin_options[remove_all_duplicates]" type="checkbox" value="1" <?php checked('1', $options['remove_all_duplicates']); ?> /></td>
				</tr>
			</table>
			<p class="submit">
			<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
			</p>
			<br /><hr />
			<p>If you like this plugin, please consider buying me a cup of coffee!

			<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
				<input type="hidden" name="cmd" value="_s-xclick">
				<input type="hidden" name="hosted_button_id" value="JKWPDXGYLASCY">
				<input type="image" src="https://www.paypal.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
				<img alt="" border="0" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1">
			</form>
			</p>
			<p>For any suggestions, feedback or bugs, please use the support forum or the links below:<br />
			<a href="http://www.jjeaton.com/blog/remove-double-space-plugin/">Plugin Homepage</a> | <a href="http://www.jjeaton.com/">Author Homepage</a></p>
		</form>
	</div>
<?php 
}

/**
 * Replaces 2 consecutive spaces with one space.
 *
 * Will replace any consecutive duplicated whitespace with a single space
 *
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

// Run replacement everywhere 'the_content' is called (posts/feeds/etc.)
add_filter( 'the_content', 'jje_replace_double_space' );

?>
