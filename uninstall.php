<?php
/**
 * @package almasder4wordpress
 * This file runs when the plugin in uninstalled (deleted).
 * This will not run when the plugin is deactivated.
 * Ideally you will add all your clean-up scripts here
 * that will clean-up unused meta, options, etc. in the database
 */


// If plugin is not being uninstalled, exit (do nothing)
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// remove the plugin options

delete_option("almasder4wordpress_active");
delete_option("almasder4wordpress_linelocation");
delete_option("almasder4wordpress_linetype");
delete_option("almasder4wordpress_lineprefix");
delete_option("almasder4wordpress_linecolor");

