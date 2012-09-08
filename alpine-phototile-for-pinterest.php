<?php
/*
Plugin Name: Alpine PhotoTile for Pinterest
Plugin URI: http://thealpinepress.com/alpine-phototile-for-pinterest/
Description: The Alpine PhotoTile for Pinterest is one plugin in a series that creates a way of retrieving photos from various popular sites and displaying them in a stylish and uniform way. The plugin is capable of retrieving photos from a particular Pinterest user or board. This lightweight but powerful widget takes advantage of WordPress's built in JQuery scripts to create a sleek presentation that I hope you will like.
Version: 1.1.1.4
Author: the Alpine Press
Author URI: http://thealpinepress.com/
License: GNU General Public License v3.0
License URI: http://www.gnu.org/licenses/gpl-3.0.html

*/

/* ******************** DO NOT edit below this line! ******************** */

/* Prevent direct access to the plugin */
if (!defined('ABSPATH')) {
	exit(__( "Sorry, you are not allowed to access this page directly.", APTFPINbyTAP_DOMAIN ));
}

/* Pre-2.6 compatibility to find directories */
if ( ! defined( 'WP_CONTENT_URL' ) )
	define( 'WP_CONTENT_URL', get_option( 'siteurl' ) . '/wp-content' );
if ( ! defined( 'WP_CONTENT_DIR' ) )
	define( 'WP_CONTENT_DIR', ABSPATH . 'wp-content' );
if ( ! defined( 'WP_PLUGIN_URL' ) )
	define( 'WP_PLUGIN_URL', WP_CONTENT_URL. '/plugins' );
if ( ! defined( 'WP_PLUGIN_DIR' ) )
	define( 'WP_PLUGIN_DIR', WP_CONTENT_DIR . '/plugins' );


/* Set constants for plugin */
define( 'APTFPINbyTAP_URL', WP_PLUGIN_URL.'/'. basename(dirname(__FILE__)) . '' );
define( 'APTFPINbyTAP_DIR', WP_PLUGIN_DIR.'/'. basename(dirname(__FILE__)) . '' );
define( 'APTFPINbyTAP_CACHE', WP_CONTENT_DIR . '/cache/' . basename(dirname(__FILE__)) . '' );
define( 'APTFPINbyTAP_VER', '1.1.1.4' );
define( 'APTFPINbyTAP_DOMAIN', 'APTFPINbyTAP_domain' );
define( 'APTFPINbyTAP_HOOK', 'APTFPINbyTAP_hook' );
define( 'APTFPINbyTAP_SETTINGS', basename(dirname(__FILE__)).'-settings' );
define( 'APTFPINbyTAP_NAME', 'Alpine PhotoTile for Pinterest' );
//####### DO NOT CHANGE #######//
define( 'APTFPINbyTAP_SHORT', 'alpine-phototile-for-pinterest' );
define( 'APTFPINbyTAP_ID', 'APTFF_by_TAP' );
//#############################//
define( 'APTFPINbyTAP_INFO', 'http://thealpinepress.com/alpine-phototile-for-pinterest/' );

register_deactivation_hook( __FILE__, 'TAP_PhotoTile_Pinterest_remove' );
function TAP_PhotoTile_Pinterest_remove(){
  if ( class_exists( 'theAlpinePressSimpleCacheV2' ) && APTFPINbyTAP_CACHE ) {
    $cache = new theAlpinePressSimpleCacheV2();  
    $cache->setCacheDir( APTFPINbyTAP_CACHE );
    $cache->clearAll();
  }
}

// Register Widget
function APTFPINbyTAP_widget_register() {register_widget( 'Alpine_PhotoTile_for_Pinterest' );}
add_action('widgets_init','APTFPINbyTAP_widget_register');

  
  include_once( APTFPINbyTAP_DIR.'/gears/function-display.php');
  include_once( APTFPINbyTAP_DIR.'/gears/source-pinterest.php');
  include_once( APTFPINbyTAP_DIR.'/gears/plugin-widget.php'); 
  include_once( APTFPINbyTAP_DIR.'/gears/plugin-shortcode.php');
  include_once( APTFPINbyTAP_DIR.'/gears/function-cache.php');
  include_once( APTFPINbyTAP_DIR.'/gears/plugin-scripts.php');
  include_once( APTFPINbyTAP_DIR.'/gears/plugin-options.php');
    
  include_once( APTFPINbyTAP_DIR.'/admin/functions-admin-options-page.php'); 
    
?>
