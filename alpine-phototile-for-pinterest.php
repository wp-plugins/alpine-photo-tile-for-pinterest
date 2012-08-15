<?php
/*
Plugin Name: Alpine PhotoTile for Pinterest
Plugin URI: http://thealpinepress.com/alpine-phototile-for-pinterest/
Description: The Alpine PhotoTile for Pinterest is one plugin in a series that creates a way of retrieving photos from various popular sites and displaying them in a stylish and uniform way. The plugin is capable of retrieving photos from a particular Pinterest user or board. This lightweight but powerful widget takes advantage of WordPress's built in JQuery scripts to create a sleek presentation that I hope you will like.
Version: 1.0.2
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
define( 'APTFPINbyTAP_VER', '1.0.2' );
define( 'APTFPINbyTAP_DOMAIN', 'APTFPINbyTAP_domain' );
define( 'APTFPINbyTAP_HOOK', 'APTFPINbyTAP_hook' );
define( 'APTFPINbyTAP_ID', 'APTFPINbyTAP' );
define( 'APTFPINbyTAP_INFO', 'http://thealpinepress.com/alpine-phototile-for-pinterest/' );

register_deactivation_hook( __FILE__, 'TAP_PhotoTile_Pinterest_remove' );
function TAP_PhotoTile_Pinterest_remove(){
  $cache = new theAlpinePressSimpleCacheV1();  
  $cache->setCacheDir( APTFPINbyTAP_CACHE );
  $cache->clearAll();
}

// Register Widget
function APTFPINbyTAP_widget_register() {register_widget( 'Alpine_PhotoTile_for_Pinterest' );}
add_action('widgets_init','APTFPINbyTAP_widget_register');

class Alpine_PhotoTile_for_Pinterest extends WP_Widget {

	function Alpine_PhotoTile_for_Pinterest() {
		$widget_ops = array('classname' => 'APTFPINbyTAP_widget', 'description' => __('Add images from Pinterest to your sidebar'));
		$control_ops = array('width' => 550, 'height' => 350);
		$this->WP_Widget(APTFPINbyTAP_DOMAIN, __('Alpine PhotoTile for Pinterest'), $widget_ops, $control_ops);
	}
  
	function widget( $args, $options ) {
		extract($args);
        
    // Set Important Widget Options    
    $id = $args["widget_id"];
    $defaults = APTFPINbyTAP_option_defaults();
    
    $source_results = APTFPINbyTAP_photo_retrieval($id, $options, $defaults);
    
    echo $before_widget . $before_title . $options['widget_title'] . $after_title;
    echo $source_results['hidden'];
    if( $source_results['continue'] ){  
      switch ($options['style_option']) {
        case "vertical":
          APTFPINbyTAP_display_vertical($id, $options, $source_results);
        break;
        case "windows":
          APTFPINbyTAP_display_hidden($id, $options, $source_results);
        break; 
        case "bookshelf":
          APTFPINbyTAP_display_hidden($id, $options, $source_results);
        break;
        case "rift":
          APTFPINbyTAP_display_hidden($id, $options, $source_results);
        break;
        case "floor":
          APTFPINbyTAP_display_hidden($id, $options, $source_results);
        break;
        case "wall":
          APTFPINbyTAP_display_hidden($id, $options, $source_results);
        break;
        case "cascade":
          APTFPINbyTAP_display_cascade($id, $options, $source_results);
        break;
        case "gallery":
          APTFPINbyTAP_display_hidden($id, $options, $source_results);
        break;
      }
    }
    // If user does not have necessary extensions 
    // or error occured before content complete, report such...
    else{
      echo 'Sorry:<br>'.$source_results['message'];
    }
    echo $after_widget;
  }
    
	function update( $newoptions, $oldoptions ) {
    $optiondetails = APTFPINbyTAP_option_defaults();
    foreach( $newoptions as $id=>$input ){
      $options[$id] = theAlpinePressMenuOptionsValidateV1( $input,$oldoptions[$id],$optiondetails[$id] );
    }
    return $options;
	}

	function form( $options ) {

    include( 'admin/widget-menu-form.php'); 

	}
}

  /////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  //////////////////////////////  Safely Enqueue Scripts  and Register Widget  ////////////////////////////////////
  /////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  
  // Load Admin JS and CSS
	function APTFPINbyTAP_admin_head_script(){ 
    // TODO - CREATE SEPERATE FUNCTIONS TO LOAD ADMIN PAGE AND WIDGET PAGE SCRIPTS
    wp_enqueue_script( 'jquery');
    // Replication Error caused by not loading new version of JS and CSS
    // Fix by always changing version number if changes were made
    wp_deregister_script('APTFPINbyTAP_widget_menu');
    wp_register_script('APTFPINbyTAP_widget_menu',APTFPINbyTAP_URL.'/js/aptfpinbytap_widget_menu.js','',APTFPINbyTAP_VER);
    wp_enqueue_script('APTFPINbyTAP_widget_menu');
        
    wp_deregister_style('APTFPINbyTAP_admin_css');   
    wp_register_style('APTFPINbyTAP_admin_css',APTFPINbyTAP_URL.'/css/aptfpinbytap_admin_style.css','',APTFPINbyTAP_VER);
    wp_enqueue_style('APTFPINbyTAP_admin_css');
    
    add_action('admin_print_footer_scripts', 'APTFPINbyTAP_menu_toggles');
    
    // Only admin can trigger two week cache cleaning
    $cache = new theAlpinePressSimpleCacheV1();
    $cache->setCacheDir( APTFPINbyTAP_CACHE );
    $cache->clean();
	}
  add_action('admin_enqueue_scripts', 'APTFPINbyTAP_admin_head_script'); // admin_init so that it is ready when page loads
  
  function APTFPINbyTAP_menu_toggles(){
    ?>
    <script type="text/javascript">
    if( jQuery().APTFPINbyTAPWidgetMenuPlugin  ){
      jQuery(document).ready(function(){
        jQuery('.APTFPINbyTAP-pinterest .APTFPINbyTAP-parent').APTFPINbyTAPWidgetMenuPlugin();
        
        jQuery(document).ajaxComplete(function() {
          jQuery('.APTFPINbyTAP-pinterest .APTFPINbyTAP-parent').APTFPINbyTAPWidgetMenuPlugin();
        });
      });
    }
    </script>  
    <?php   
  }
  
  // Load Display JS and CSS
  function APTFPINbyTAP_enqueue_display_scripts() {
    wp_enqueue_script( 'jquery' );
    
    wp_deregister_script('APTFPINbyTAP_tiles_and_slideshow');
    wp_enqueue_script('APTFPINbyTAP_tiles',APTFPINbyTAP_URL.'/js/aptfpinbytap_tiles.js','',APTFPINbyTAP_VER);
    
    wp_deregister_style('APTFPINbyTAP_widget_css'); // Since I wrote the scripts, deregistering and updating version are redundant in this case
    wp_register_style('APTFPINbyTAP_widget_css',APTFPINbyTAP_URL.'/css/aptfpinbytap_widget_style.css','',APTFPINbyTAP_VER);
    wp_enqueue_style('APTFPINbyTAP_widget_css');

    wp_enqueue_script('pinterest_pinit',"http://assets.pinterest.com/js/pinit.js");
    
  }
  add_action('wp_enqueue_scripts', 'APTFPINbyTAP_enqueue_display_scripts');
  
  include_once( 'admin/widget-options.php');
  include_once( 'admin/function-options-display.php'); 
  include_once( 'admin/function-options-sanitize.php'); 
  include_once( 'gears/source-pinterest.php');
  include_once( 'gears/display-functions.php');
  include_once( 'gears/function-cache.php');
    
?>
