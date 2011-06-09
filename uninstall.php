<?php
/**
 * Uninstall module for remove double space plugin
 * Version 0.1
 */

// Only run uninstall procedure if file is run from admin interface
if( !defined( 'ABSPATH' ) && !defined( 'WP_UNINSTALL_PLUGIN' ) )
    exit();

// Delete all plugin options from database prior to uninstallation
delete_option('jje_rds_plugin_options');

?>
