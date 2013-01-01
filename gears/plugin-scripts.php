<?php
/**
 * Alpine PhotoTile for Flickr: Styles and Scripts
 *
 * @since 1.1.1
 *
 */
 
  /////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  //////////////////////////////  Safely Enqueue Scripts  and Register Widget  ////////////////////////////////////
  /////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  
  // Load Admin JS and CSS
	function APTFPINbyTAP_admin_widget_script($hook){ 

    wp_deregister_script('APTFPINbyTAP_widget_menu');
    wp_register_script('APTFPINbyTAP_widget_menu',APTFPINbyTAP_URL.'/js/aptfpinbytap_widget_menu.js','',APTFPINbyTAP_VER);

    wp_deregister_style('APTFPINbyTAP_admin_css');   
    wp_register_style('APTFPINbyTAP_admin_css',APTFPINbyTAP_URL.'/css/aptfpinbytap_admin_style.css','',APTFPINbyTAP_VER);
        
    if( 'widgets.php' != $hook )
      return;
      
    wp_enqueue_script( 'jquery');
    wp_enqueue_style( 'farbtastic' );
    wp_enqueue_script( 'farbtastic' );
    
    wp_enqueue_script('APTFPINbyTAP_widget_menu');
        
    wp_enqueue_style('APTFPINbyTAP_admin_css');
    
    add_action('admin_print_footer_scripts', 'APTFPINbyTAP_menu_toggles');
    
    // Only admin can trigger two week cache cleaning by visiting widgets.php
    $disablecache = APTFPINbyTAP_get_option( 'cache_disable' );
    if ( class_exists( 'theAlpinePressSimpleCacheV2' ) && APTFPINbyTAP_CACHE && !$disablecache ) {
      $cache = new theAlpinePressSimpleCacheV2();
      $cache->setCacheDir( APTFPINbyTAP_CACHE );
      $cache->clean();
    }
	}
  add_action('admin_enqueue_scripts', 'APTFPINbyTAP_admin_widget_script'); 
  
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
  function APTFPINbyTAP_shortcode_select(){
    ?>
    <script type="text/javascript">
     jQuery(".auto_select").mouseenter(function(){
        jQuery(this).select();
      }); 
      if( jQuery('#<?php echo APTFPINbyTAP_SETTINGS; ?>-shortcode') ){

        jQuery("html,body").animate({ scrollTop: (jQuery('#<?php echo APTFPINbyTAP_SETTINGS; ?>-shortcode').offset().top-70) }, 2000);
      
      }

    </script>  
    <?php
  }
  // Load Display JS and CSS
  function APTFPINbyTAP_enqueue_display_scripts() {
    wp_enqueue_script( 'jquery' );
    
    wp_deregister_script('APTFPINbyTAP_tiles');
    wp_register_script('APTFPINbyTAP_tiles',APTFPINbyTAP_URL.'/js/aptfpinbytap_tiles.js','',APTFPINbyTAP_VER);
    
    wp_deregister_style('APTFPINbyTAP_widget_css'); // Since I wrote the scripts, deregistering and updating version are redundant in this case
    wp_register_style('APTFPINbyTAP_widget_css',APTFPINbyTAP_URL.'/css/aptfpinbytap_widget_style.css','',APTFPINbyTAP_VER);
    
    wp_register_style('pinterest_pinit',"http://assets.pinterest.com/js/pinit.js");
        
  }
  add_action('wp_enqueue_scripts', 'APTFPINbyTAP_enqueue_display_scripts');
  
  
/**
 * Enqueue admin scripts (and related stylesheets)
 */
  function APTFPINbyTAP_enqueue_admin_scripts() {

    wp_enqueue_script( 'jquery' );
    
    wp_enqueue_script('APTFPINbyTAP_widget_menu');
    wp_enqueue_style('APTFPINbyTAP_admin_css');
    
    add_action('admin_print_footer_scripts', 'APTFPINbyTAP_menu_toggles'); 
    add_action('admin_print_footer_scripts', 'APTFPINbyTAP_shortcode_select'); 
  }
?>