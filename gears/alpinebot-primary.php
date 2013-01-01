<?php


class PhotoTileForPinterestBase {  

  /* Set constants for plugin */
  public $url;
  public $dir;
  public $cacheDir;
  public $ver = '1.2.1';
  public $vers = '1-2-1';
  public $domain = 'APTFPINbyTAP_domain';
  public $settings = 'alpine-photo-tile-for-pinterest-settings'; // All lowercase
  public $name = 'Alpine Photo Tile for Pinterest';
  public $info = 'http://thealpinepress.com/alpine-phototile-for-pinterest/';
  public $wplink = 'http://wordpress.org/extend/plugins/alpine-photo-tile-for-pinterest/';
  public $page = 'AlpineTile: Pinterest';
  public $hook = 'APTFPINbyTAP_hook';
  public $plugins = array('flickr','tumblr');

  public $root = 'AlpinePhotoTiles';
  public $wjs = 'AlpinePhotoTiles_script';
  public $wcss = 'AlpinePhotoTiles_style';
  public $wmenujs = 'AlpinePhotoTiles_menu_script';
  public $acss = 'AlpinePhotoTiles_admin_style';
  public $wdesc = 'Add images from Pinterest to your sidebar';
//####### DO NOT CHANGE #######//
  public $short = 'alpine-phototile-for-pinterest';
  public $id = 'APTFPIN_by_TAP';
//#############################//
  public $expiryInterval = 360; //1*60*60;  1 hour
  public $cleaningInterval = 1209600; //14*24*60*60;  2 weeks

  function __construct() {
    $this->url = untrailingslashit( plugins_url( '' , dirname(__FILE__) ) );
    $this->dir = untrailingslashit( plugin_dir_path( dirname(__FILE__) ) );
    $this->cacheDir = WP_CONTENT_DIR . '/cache/' . $this->settings;
  }
  
  function widget_positions(){
      $options = array(
      'top' => '',
      'left' => 'Pinterest Settings',
      'right' => 'Style Settings',
      'bottom' => 'Format Settings'
    );
    return $options;
  }
  function option_positions(){
    $positions = array(
      'generator' => array(
        'left' => 'Pinterest Settings',
        'right' => 'Style Settings',
        'bottom' => 'Format Settings'
      ),
      'plugin-settings' => array(
        'top' => 'Cache Options',
        'center' =>'Global Style Options'
      )
    );
    return $positions;
  }
/**
 * Plugin Admin Settings Page Tabs
 */
  function settings_page_tabs() {
    $tabs = array( 
      'general' => array(
        'name' => 'general',
        'title' => 'General',
      ),
      'generator' => array(
        'name' => 'generator',
        'title' => 'Shortcode Generator',
      ),
      'preview' => array(
        'name' => 'preview',
        'title' => 'Shortcode Preview',
      ),
      'plugin-settings' => array(
        'name' => 'plugin-settings',
        'title' => 'Plugin Settings',
      )
    );
    return $tabs;
  }
  
  function option_defaults(){
    $options = array(
      'widget_title' => array(
        'name' => 'widget_title',
        'title' => 'Title : ',
        'type' => 'text',
        'description' => '',
        'since' => '1.1',
        'widget' => true,
        'tab' => '',
        'position' => 'top',
        'default' => ''
      ),
      'pinterest_source' => array(
        'name' => 'pinterest_source',
        'short' => 'src',
        'title' => 'Retrieve Photos From : ',
        'type' => 'select',
        'valid_options' => array(
          'user' => array(
            'name' => 'user',
            'title' => 'User'
          ),
          'board' => array(
            'name' => 'board',
            'title' => 'Board'
          )    
        ),
        'description' => '',
        'parent' => 'AlpinePhotoTiles-parent', 
        'trigger' => 'pinterest_source',
        'widget' => true,
        'tab' => 'generator',
        'position' => 'left',
        'default' => 'user'
      ),
      'pinterest_user_id' => array(
        'name' => 'pinterest_user_id',
        'short' => 'uid',
        'title' => 'Pinterest User ID : ',
        'type' => 'text',
        'sanitize' => 'nospaces',
        'description' => "",
        'child' => 'pinterest_source', 
        'hidden' => '',
        'widget' => true,
        'tab' => 'generator',
        'position' => 'left',        
        'default' => ''
      ),
      'pinterest_user_board' => array(
        'name' => 'pinterest_user_board',
        'title' => 'Pinterest Board Tag: ',
        'short' => 'board',
        'type' => 'text',
        'sanitize' => 'nospaces',
        'remove' => '&amp;', // First delete '&',
        'replace' => '-', // Then replace spaces with '-'
        'description' => '',
        'child' => 'pinterest_source', 
        'hidden' => 'user',
        'widget' => true,
        'tab' => 'generator',
        'position' => 'left',
        'default' => ''
      ),  
      'pinterest_image_link_option' => array(
        'name' => 'pinterest_image_link_option',
        'short' => 'imgl',
        'title' => 'Image Links : ',
        'type' => 'select',
        'valid_options' => array(
          'none' => array(
            'name' => 'none',
            'title' => 'Do not link images'
          ),
          'original' => array(
            'name' => 'original',
            'title' => 'Link to Image Source'
          ),
          'pinterest' => array(
            'name' => 'pinterest',
            'title' => 'Link to Pinterest Page'
          ),
          'link' => array(
            'name' => 'link',
            'title' => 'Link to URL Address'
          ),
          'fancybox' => array(
            'name' => 'fancybox',
            'title' => 'Use Fancybox'
          )               
        ),
        'description' => '',
        'widget' => true,
        'tab' => 'generator',
        'position' => 'left',
        'parent' => 'AlpinePhotoTiles-parent', 
        'trigger' => 'pinterest_image_link_option',
        'default' => 'pinterest'
      ),      
      'custom_link_url' => array(
        'name' => 'custom_link_url',
        'title' => 'Custom Link URL : ',
        'short' => 'curl',
        'type' => 'text',
        'sanitize' => 'url',
        'description' => '',
        'child' => 'pinterest_image_link_option', 
        'hidden' => 'none original pinterest fancybox',
        'widget' => true,
        'tab' => 'generator',
        'position' => 'left',
        'default' => ''
      ),
      'pinterest_pin_it_button' => array(
        'name' => 'pinterest_pin_it_button',
        'short' => 'pinit',
        'title' => 'Include Pin It Button.',
        'type' => 'checkbox',
        'description' => '',
        'widget' => true,
        'tab' => 'generator',
        'position' => 'left',
        'default' => ''
      ),       
      
      'pinterest_display_link' => array(
        'name' => 'pinterest_display_link',
        'short' => 'dl',
        'title' => 'Display link to Pinterest page.',
        'type' => 'checkbox',
        'description' => '',
        'widget' => true,
        'tab' => 'generator',
        'position' => 'left',
        'default' => ''
      ),    
      'pinterest_display_link_style' => array(
        'name' => 'pinterest_display_link_style',
        'short' => 'dlstyle',
        'title' => 'Pinterest link style : ',
        'type' => 'select',
        'valid_options' => array(
          'large' => array(
            'name' => 'large',
            'title' => 'Large'
          ),
          'medium' => array(
            'name' => 'medium',
            'title' => 'Medium'
          ),
          'small' => array(
            'name' => 'small',
            'title' => 'Small'
          ),
          'tiny' => array(
            'name' => 'tiny',
            'title' => 'Tiny'
          ),
          'text' => array(
            'name' => 'text',
            'title' => 'Text'
          )      
        ),
        'description' => '',
        'parent' => 'AlpinePhotoTiles-parent', 
        'trigger' => 'pinterest_display_link_style',
        'widget' => true,
        'tab' => 'generator',
        'position' => 'left',
        'default' => 'medium'
      ),  
      
      'pinterest_display_link_text' => array(
        'name' => 'pinterest_display_link_text',
        'short' => 'dltext',
        'title' => 'Link Text : ',
        'type' => 'text',
        'sanitize' => 'nohtml',
        'description' => '',
        'child' => 'pinterest_display_link_style', 
        'hidden' => 'large medium small tiny',
        'widget' => true,
        'tab' => 'generator',
        'position' => 'left',
        'default' => 'Pinterest'
      ),    

     
      'pinterest_photo_size' => array(
        'name' => 'pinterest_photo_size',
        'short' => 'size',
        'title' => 'Photo Size : ',
        'type' => 'select',
        'valid_options' => array(
          '75' => array(
            'name' => 75,
            'title' => '75px'
          ),
          '192' => array(
            'name' => 192,
            'title' => '192px'
          ),
          '554' => array(
            'name' => 554,
            'title' => '554px'
          ),
          '600' => array(
            'name' => 600,
            'title' => '600px'
          ),
          '930' => array(
            'name' => 930,
            'title' => '930px'
          )      
        ),
        'description' => '',
        'widget' => true,
        'tab' => 'generator',
        'position' => 'left',
        'default' => '192'
      ),
      'style_option' => array(
        'name' => 'style_option',
        'short' => 'style',
        'title' => 'Style : ',
        'type' => 'select',
        'valid_options' => array(
          'vertical' => array(
            'name' => 'vertical',
            'title' => 'Vertical'
          ),
          'windows' => array(
            'name' => 'windows',
            'title' => 'Windows'
          ),
          'bookshelf' => array(
            'name' => 'bookshelf',
            'title' => 'Bookshelf'
          ),
          'rift' => array(
            'name' => 'rift',
            'title' => 'Rift'
          ),
          'floor' => array(
            'name' => 'floor',
            'title' => 'Floor'
          ),
          'wall' => array(
            'name' => 'wall',
            'title' => 'Wall'
          ),
          'cascade' => array(
            'name' => 'cascade',
            'title' => 'Cascade'
          ),
          'gallery' => array(
            'name' => 'gallery',
            'title' => 'Gallery'
          )           
        ),
        'description' => '',
        'parent' => 'AlpinePhotoTiles-parent',
        'trigger' => 'style_option',
        'widget' => true,
        'tab' => 'generator',
        'position' => 'right',
        'default' => 'vertical'
      ),
      'style_shape' => array(
        'name' => 'style_shape',
        'short' => 'shape',
        'title' => 'Shape : ',
        'type' => 'select',
        'valid_options' => array(
          'rectangle' => array(
            'name' => 'rectangle',
            'title' => 'Rectangle'
          ),
          'square' => array(
            'name' => 'square',
            'title' => 'Square'
          )              
        ),
        'description' => '',
        'child' => 'style_option',
        'hidden' => 'vertical cascade floor wall rift bookshelf gallery',
        'widget' => true,
        'tab' => 'generator',
        'position' => 'right',
        'default' => 'vertical'
      ),          
      'style_photo_per_row' => array(
        'name' => 'style_photo_per_row',
        'short' => 'row',
        'title' => 'Photos per row : ',
        'type' => 'range',
        'min' => '1',
        'max' => '25',
        'description' => '',
        'child' => 'style_option',
        'hidden' => 'vertical cascade windows',
        'widget' => true,
        'tab' => 'generator',
        'position' => 'right',
        'default' => '4'
      ),
      'style_column_number' => array(
        'name' => 'style_column_number',
        'short' => 'col',
        'title' => 'Number of columns : ',
        'type' => 'range',
        'min' => '1',
        'max' => '10',
        'description' => '',
        'child' => 'style_option',
        'hidden' => 'vertical floor wall bookshelf windows rift gallery',
        'widget' => true,
        'tab' => 'generator',
        'position' => 'right',
        'default' => '2'
      ),     
      'style_gallery_height' => array(
        'name' => 'style_gallery_height',
        'short' => 'gheight',
        'title' => 'Gallery Size : ',
        'type' => 'select',
        'valid_options' => array(
          '2' => array(
            'name' => 2,
            'title' => 'XS'
          ),
          '3' => array(
            'name' => 3,
            'title' => 'Small'
          ),
          '4' => array(
            'name' => 4,
            'title' => 'Medium'
          ),
          '5' => array(
            'name' => 5,
            'title' => 'Large'
          ),
          '6' => array(
            'name' => 6,
            'title' => 'XL'
          ),
          '7' => array(
            'name' => 7,
            'title' => 'XXL'
          )             
        ),
        'description' => '',
        'child' => 'style_option',
        'hidden' => 'vertical cascade floor wall rift bookshelf windows',
        'widget' => true,
        'tab' => 'generator',
        'position' => 'right',
        'default' => '3'
      ),     
      'pinterest_photo_number' => array(
        'name' => 'pinterest_photo_number',
        'short' => 'num',
        'title' => 'Number of photos : ',
        'type' => 'range',
        'min' => '1',
        'max' => '25',
        'description' => '',
        'widget' => true,
        'tab' => 'generator',
        'position' => 'right',
        'default' => '4'
      ),
      'style_shadow' => array(
        'name' => 'style_shadow',
        'short' => 'shadow',
        'title' => 'Add slight image shadow.',
        'type' => 'checkbox',
        'description' => '',
        'widget' => true,
        'tab' => 'generator',
        'position' => 'right',
        'default' => ''
      ),   
      'style_border' => array(
        'name' => 'style_border',
        'short' => 'border',
        'title' => 'Add white image border.',
        'type' => 'checkbox',
        'description' => '',
        'widget' => true,
        'tab' => 'generator',
        'position' => 'right',
        'default' => ''
      ),   
      'style_highlight' => array(
        'name' => 'style_highlight',
        'short' => 'highlight',
        'title' => 'Highlight when hovering.',
        'type' => 'checkbox',
        'description' => '',
        'widget' => true,
        'tab' => 'generator',
        'position' => 'right',
        'default' => ''
      ),
      'style_curve_corners' => array(
        'name' => 'style_curve_corners',
        'short' => 'curve',
        'title' => 'Add slight curve to corners.',
        'type' => 'checkbox',
        'description' => '',
        'widget' => true,
        'tab' => 'generator',
        'position' => 'right',
        'default' => ''
      ),          
      'widget_alignment' => array(
        'name' => 'widget_alignment',
        'short' => 'align',
        'title' => 'Photo alignment : ',
        'type' => 'select',
        'valid_options' => array(
          'left' => array(
            'name' => 'left',
            'title' => 'Left'
          ),
          'center' => array(
            'name' => 'center',
            'title' => 'Center'
          ),
          'right' => array(
            'name' => 'right',
            'title' => 'Right'
          )            
        ),
        'widget' => true,
        'tab' => 'generator',
        'position' => 'bottom',
        'default' => 'center'
      ),    
      'widget_max_width' => array(
        'name' => 'widget_max_width',
        'short' => 'max',
        'title' => 'Max widget width (%) : ',
        'type' => 'text',
        'sanitize' => 'numeric',
        'min' => '1',
        'max' => '100',
        'description' => "To reduce the widget width, input a percentage (between 1 and 100). <br> If photos are smaller than widget area, reduce percentage until desired width is achieved.",
        'widget' => true,
        'tab' => 'generator',
        'position' => 'bottom',
        'default' => '100'
      ),        
      'widget_disable_credit_link' => array(
        'name' => 'widget_disable_credit_link',
        'short' => 'nocredit',
        'title' => 'Disable the tiny "TAP" link in the bottom left corner, though I have spent several months developing this plugin and would appreciate the credit.',
        'type' => 'checkbox',
        'description' => '',
        'widget' => true,
        'tab' => 'generator',
        'position' => 'bottom',
        'default' => ''
      ), 
      'cache_disable' => array(
        'name' => 'cache_disable',
        'title' => 'Disable feed caching: ',
        'type' => 'checkbox',
        'description' => '',
        'since' => '1.1',
        'tab' => 'plugin-settings',
        'position' => 'top',
        'default' => ''
      ), 
      'cache_time' => array(
        'name' => 'cache_time',
        'title' => 'Cache time (hours) : ',
        'type' => 'text',
        'sanitize' => 'numeric',
        'min' => '1',
        'description' => "Set the number of hours that a feed will be stored.",
        'since' => '1.1',
        'tab' => 'plugin-settings',
        'position' => 'top',
        'default' => '3'
      ), 
      'general_loader' => array(
        'name' => 'general_loader',
        'title' => 'Disable Loading Icon: ',
        'type' => 'checkbox',
        'description' => 'Remove the icon that appears while images are loading.',
        'since' => '1.1',
        'tab' => 'plugin-settings',
        'position' => 'center',
        'default' => ''
      ), 
      'general_highlight_color' => array(
        'name' => 'general_highlight_color',
        'title' => 'Highlight Color:',
        'type' => 'color',
        'description' => 'Click to choose link color.',
        'section' => 'settings',
        'tab' => 'general',
        'since' => '1.2',
        'tab' => 'plugin-settings',
        'position' => 'center',
        'default' => '#64a2d8'
      ), 
    );
    return $options;
  }
  
// END
}

?>
