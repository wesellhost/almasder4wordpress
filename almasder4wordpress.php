<?php

/**
 * @package almasder4wordpress
 */
/*
  /*
 * Plugin Name: almasder4wordpress
 * Version: 1.0
 * Plugin URI: http://shannaq.com/almasder4wordpress
 * Description: Add the ability to attach the original source publisher of the post to the post content. You can use this plugin to attach a line of text to the post content which show a line of text that refer for the original publisher, also you can attach a link to that text.
 * Author: Mohammed AlShannaq
 * Author URI: http://www.shannaq.com/
 * Requires at least: 4.0
 * Tested up to: 4.0
 *
 * Text Domain: almasder4wordpress
 * Domain Path: /lang/
 *
 * @package WordPress
 * @author Mohammed Alshannaq
 * @since 1.0.0
 */

if (!defined('ABSPATH'))
    exit;

// Load plugin class files
require_once( 'includes/class-almasder4wordpress.php' );
require_once( 'includes/class-almasder4wordpress-settings.php' );

// Load plugin libraries
require_once( 'includes/lib/class-almasder4wordpress-admin-api.php' );

/* not needed in this plugin */
//require_once( 'includes/lib/class-almasder4wordpress-post-type.php' );
//require_once( 'includes/lib/class-almasder4wordpress-taxonomy.php' );
/* end of not needed in this plugin */

/**
 * Returns the main instance of almasder4wordpress to prevent the need to use globals.
 *
 * @since  1.0.0
 * @return object almasder4wordpress
 */
function almasder4wordpress() {
    $instance = almasder4wordpress::instance(__FILE__, '1.0.0');

    if (is_null($instance->settings)) {
        $instance->settings = almasder4wordpress_Settings::instance($instance);
    }

    function almasder4wordpress_filter_the_content($content) {
        $custom_content = $content;

        $almasder4wordpress_active = get_option("almasder4wordpress_active");
        $almasder4wordpress_linelocation = get_option("almasder4wordpress_linelocation");
        $almasder4wordpress_linetype = get_option("almasder4wordpress_linetype");
        $almasder4wordpress_lineprefix = get_option("almasder4wordpress_lineprefix");
        $almasder4wordpress_linecolor = get_option("almasder4wordpress_linecolor");

        if ($almasder4wordpress_active == "yes") {
            $values = get_post_custom($post->ID);
            $almasder4wordpress_masdertext = esc_attr($values['_almasder4wordpress_text'][0]);
            $almasder4wordpress_masderlink = esc_attr($values['_almasder4wordpress_link'][0]);
           
            if ($almasder4wordpress_linetype == "text"){
                $masder_line = '<p style="color:'.$almasder4wordpress_linecolor.'">'.$almasder4wordpress_lineprefix.$almasder4wordpress_masdertext.' &nbsp;</p>';
            }
            if ($almasder4wordpress_linetype == "link"){
                $masder_line = '<p style="color:'.$almasder4wordpress_linecolor.'"><a target=_blank rel=nofollow style="color:'.$almasder4wordpress_linecolor.'" href='.$almasder4wordpress_masderlink.'>'.$almasder4wordpress_lineprefix.$almasder4wordpress_masderlink.'</a> &nbsp;</p>';
            };
            if ($almasder4wordpress_linetype == "href"){
                $masder_line = '<p style="color:'.$almasder4wordpress_linecolor.'"><a target=_blank rel=nofollow style="color:'.$almasder4wordpress_linecolor.'" href='.$almasder4wordpress_masderlink.'>'.$almasder4wordpress_lineprefix.$almasder4wordpress_masdertext.'</a> &nbsp;</p>';
            };
            

            if (is_single($post->ID)) {
                $custom_content = $masder_line;
                
                
                switch ($almasder4wordpress_linelocation) {
                    case 'above':
                        $custom_content .= $content;
                        break;
                    case 'below':
                        $custom_content = $content . $custom_content;
                        break;
                    default:
                        $custom_content = $content;
                        break;
                }
                
                
            }
        }
        return $custom_content;
    }

    add_filter('the_content', 'almasder4wordpress_filter_the_content');




    return $instance;
}

function almasder4wordpress_print_line() {
    echo "sam";
}

almasder4wordpress();
