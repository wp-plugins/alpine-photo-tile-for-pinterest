<?php
/*
Plugin Name: Alpine PhotoTile for Pinterest
Plugin URI: http://thealpinepress.com/alpine-phototile-for-pinterest//
Description: The Alpine PhotoTile for Pinterest is capable of retrieving photos from a particular Pinterest user or board. The photos can be linked to the your Pinterest page, a specific URL, or to a Fancybox slideshow. Also, the Shortcode Generator makes it easy to insert the widget into posts without learning any of the code. This lightweight but powerful widget takes advantage of WordPress's built in JQuery scripts to create a sleek presentation that I hope you will like.
Version: 1.2.3
Author: the Alpine Press
Author URI: http://thealpinepress.com/
License: GNU General Public License v3.0
License URI: http://www.gnu.org/licenses/gpl-3.0.html

*/

  // Prevent direct access to the plugin 
  if (!defined('ABSPATH')) {
    exit(__( "Sorry, you are not allowed to access this page directly." ));
  }

  // Pre-2.6 compatibility to find directories
  if ( ! defined( 'WP_CONTENT_URL' ) )
    define( 'WP_CONTENT_URL', get_option( 'siteurl' ) . '/wp-content' );
  if ( ! defined( 'WP_CONTENT_DIR' ) )
    define( 'WP_CONTENT_DIR', ABSPATH . 'wp-content' );
  if ( ! defined( 'WP_PLUGIN_URL' ) )
    define( 'WP_PLUGIN_URL', WP_CONTENT_URL. '/plugins' );
  if ( ! defined( 'WP_PLUGIN_DIR' ) )
    define( 'WP_PLUGIN_DIR', WP_CONTENT_DIR . '/plugins' );

/**
 * Clear cache upon deactivation
 *  
 * @since 1.0.1
 *
 */
  function APTFPINbyTAP_remove(){
    if ( class_exists( 'PhotoTileForPinterestBot' ) ) {
      $bot = new PhotoTileForPinterestBot();
      $bot->clearAllCache();
    }
  }
  register_deactivation_hook( __FILE__, 'APTFPINbyTAP_remove' );
/**
 * Register Widget
 *  
 * @since 1.0.0
 *
 */
  function APTFPINbyTAP_widget_register() {register_widget( 'Alpine_PhotoTile_for_Pinterest' );}
  add_action('widgets_init','APTFPINbyTAP_widget_register');

  include_once( WP_PLUGIN_DIR.'/'.basename(dirname(__FILE__)).'/gears/alpinebot-primary.php' );
  include_once( WP_PLUGIN_DIR.'/'.basename(dirname(__FILE__)).'/gears/alpinebot-secondary.php' );
  include_once( WP_PLUGIN_DIR.'/'.basename(dirname(__FILE__)).'/gears/alpinebot-tertiary.php' );
  include_once( WP_PLUGIN_DIR.'/'.basename(dirname(__FILE__)).'/gears/plugin-widget.php' );
  include_once( WP_PLUGIN_DIR.'/'.basename(dirname(__FILE__)).'/gears/plugin-shortcode.php' );

/**
 * Load Admin JS and CSS
 *  
 * @ Since 1.0.0
 * @ Updated 1.2.3
 */
	function APTFPINbyTAP_admin_widget_script($hook){ 
    $bot = new PhotoTileForPinterestBot();
    wp_register_script($bot->wmenujs,$bot->url.'/js/'.$bot->wmenujs.'.js','',$bot->ver); 
    wp_register_style($bot->acss,$bot->url.'/css/'.$bot->acss.'.css','',$bot->ver);
    
    $bot->register_style_and_script(); // Register widget styles and scripts
         
    if( 'widgets.php' != $hook ){ return; }
    
    wp_enqueue_script( 'jquery');
    wp_enqueue_script($bot->wmenujs);
    wp_enqueue_style($bot->acss);
    add_action('admin_print_footer_scripts', 'APTFPINbyTAP_menu_toggles');
    
    // Only admin can trigger two week cache cleaning by visiting widgets.php
    $disablecache = $bot->get_option( 'cache_disable' );
    if ( !$disablecache ) { $bot->cleanCache(); }
	}
  add_action('admin_enqueue_scripts', 'APTFPINbyTAP_admin_widget_script'); 
  
/**
 * Load JS to activate menu toggles
 *  
 * @since 1.0.0
 *
 */
  function APTFPINbyTAP_menu_toggles(){
    $bot = new PhotoTileForPinterestBot();
    ?>
    <script type="text/javascript">
    if( jQuery().AlpineWidgetMenuPlugin  ){
      jQuery(document).ready(function(){
        jQuery('.AlpinePhotoTiles-container.<?php echo $bot->domain;?> .AlpinePhotoTiles-parent').AlpineWidgetMenuPlugin();
        jQuery(document).ajaxComplete(function() {
          jQuery('.AlpinePhotoTiles-container.<?php echo $bot->domain;?> .AlpinePhotoTiles-parent').AlpineWidgetMenuPlugin();
        });
      });
    }
    </script>  
    <?php   
  }
/**
 * Load LS to highlight and select shortcode upon hovering
 *  
 * @since 1.0.0
 *
 */
  function APTFPINbyTAP_shortcode_select(){
    $bot = new PhotoTileForPinterestBot();
    ?>
    <script type="text/javascript">
      jQuery(".auto_select").mouseenter(function(){
        jQuery(this).select();
      }); 
      var div = jQuery('#<?php echo $bot->settings; ?>-shortcode');
      if( div && div.offset() ){
        jQuery("html,body").animate({ scrollTop: (40) }, 2000);
      } 
    </script>  
    <?php
  }

/**
 * Load Display JS and CSS
 *  
 * @ Since 1.0.0
 * @ Updated 1.2.3
 */
  function APTFPINbyTAP_enqueue_display_scripts() {
    $bot = new PhotoTileForPinterestBot();
    wp_enqueue_script( 'jquery' );

    $bot->register_style_and_script(); // Register widget styles and scripts
  }
  add_action('wp_enqueue_scripts', 'APTFPINbyTAP_enqueue_display_scripts');
  
/**
 * Setup the Theme Admin Settings Page
 *
 * @ Since 1.0.1 
 */
  function APTFPINbyTAP_admin_options() {
    $bot = new PhotoTileForPinterestBot();
    $page = add_options_page(__($bot->page), __($bot->page), 'manage_options', $bot->settings , 'APTFPINbyTAP_admin_options_page');
    /* Using registered $page handle to hook script load */
    add_action('admin_print_scripts-' . $page, 'APTFPINbyTAP_enqueue_admin_scripts');
  }
  // Load the Admin Options page
  add_action('admin_menu', 'APTFPINbyTAP_admin_options');
  
/**
 * Enqueue admin scripts (and related stylesheets)
 *
 * @ Since 1.0.0
 */
  function APTFPINbyTAP_enqueue_admin_scripts() {
    $bot = new PhotoTileForPinterestBot();
    wp_enqueue_script( 'jquery' );
    wp_enqueue_style( 'farbtastic' );
    wp_enqueue_script( 'farbtastic' );    
    wp_enqueue_script($bot->wmenujs);
    wp_enqueue_style($bot->acss);
    add_action('admin_print_footer_scripts', 'APTFPINbyTAP_menu_toggles'); 
    add_action('admin_print_footer_scripts', 'APTFPINbyTAP_shortcode_select'); 
  }
/**
 * Settings Page Markup
 *
 * @ Since 1.0.2
 */
  function APTFPINbyTAP_admin_options_page() { 
    if (!current_user_can('manage_options')) {
      wp_die( __('You do not have sufficient permissions to access this page.') );
    }
    $bot = new PhotoTileForPinterestBot();
    $bot->build_settings_page();
  }  
?>
