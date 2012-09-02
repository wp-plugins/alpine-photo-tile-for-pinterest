<?php
/**
 * Alpine PhotoTile for Pinterest: Widget Options
 *
 * @since 1.0.0
 *
 */
 
  function APTFPINbyTAP_option_positions(){
    $options = array(
      'top' => array(
        'title' => '',
        'options' =>array('widget_title')
      ),
      'left' => array(
        'title' => 'Pinterest Settings',
        'options' =>array('pinterest_source','pinterest_user_id','pinterest_user_board','pinterest_pin_it_button','pinterest_image_link','pinterest_display_link','pinterest_display_link_style','pinterest_photo_size' )
      ),
      'right' => array(
        'title' => 'Style Settings',
        'options' =>array('style_option','style_shape','style_gallery_height','style_photo_per_row','style_column_number','pinterest_photo_number','style_shadow','style_border','style_curve_corners')
      ),
      'bottom' => array(
        'title' => 'Format Settings',
        'options' =>array('widget_alignment','widget_max_width','widget_disable_credit_link')
      ),
    );
    return $options;
  }
  
  function APTFPINbyTAP_option_defaults(){
    $options = array(
      'widget_title' => array(
        'name' => 'widget_title',
        'title' => 'Title : ',
        'type' => 'text',
        'sanitize' => 'nohtml',
        'description' => '',
        'since' => '1.1',
        'default' => ''
      ),
      'pinterest_source' => array(
        'name' => 'pinterest_source',
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
        'parent' => 'APTFPINbyTAP-parent', 
        'trigger' => 'pinterest_source',
        'default' => 'user'
      ),
      'pinterest_user_id' => array(
        'name' => 'pinterest_user_id',
        'title' => 'Pinterest User ID : ',
        'type' => 'text',
        'sanitize' => 'nospaces',
        'description' => '',
        'child' => 'pinterest_source', 
        'hidden' => '',
        'since' => '1.1',
        'default' => ''
      ),
      'pinterest_user_board' => array(
        'name' => 'pinterest_user_board',
        'title' => 'Pinterest Board : ',
        'type' => 'text',
        'sanitize' => 'nospaces',
        'description' => '',
        'child' => 'pinterest_source', 
        'hidden' => 'user',
        'since' => '1.1',
        'default' => ''
      ),    
      'pinterest_pin_it_button' => array(
        'name' => 'pinterest_pin_it_button',
        'title' => 'Include Pin It Button.',
        'type' => 'checkbox',
        'description' => '',
        'since' => '1.1',
        'default' => ''
      ),       
      'pinterest_image_link' => array(
        'name' => 'pinterest_image_link',
        'title' => 'Link images to Pinterest source.',
        'type' => 'checkbox',
        'description' => '',
        'since' => '1.1',
        'default' => ''
      ),
      'pinterest_display_link' => array(
        'name' => 'pinterest_display_link',
        'title' => 'Display link to Pinterest page.',
        'type' => 'checkbox',
        'description' => '',
        'since' => '1.1',
        'default' => ''
      ),    
      'pinterest_display_link_style' => array(
        'name' => 'pinterest_display_link_style',
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
        'since' => '1.1',
        'default' => 'medium'
      ),      
      'pinterest_photo_size' => array(
        'name' => 'pinterest_photo_size',
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
        'since' => '1.1',
        'default' => '100'
      ),
      'style_option' => array(
        'name' => 'style_option',
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
        'parent' => 'APTFPINbyTAP-parent',
        'trigger' => 'style_option',
        'since' => '1.1',
        'default' => 'vertical'
      ),
      'style_shape' => array(
        'name' => 'style_shape',
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
        'since' => '1.1',
        'default' => 'vertical'
      ),          
      'style_photo_per_row' => array(
        'name' => 'style_photo_per_row',
        'title' => 'Photos per row : ',
        'type' => 'range',
        'min' => '1',
        'max' => '20',
        'description' => '',
        'child' => 'style_option',
        'hidden' => 'vertical cascade windows',
        'since' => '1.1',
        'default' => '4'
      ),
      'style_column_number' => array(
        'name' => 'style_column_number',
        'title' => 'Number of columns : ',
        'type' => 'range',
        'min' => '1',
        'max' => '10',
        'description' => '',
        'child' => 'style_option',
        'hidden' => 'vertical floor wall bookshelf windows rift gallery',
        'since' => '1.1',
        'default' => '2'
      ),      
      'style_gallery_height' => array(
        'name' => 'style_gallery_height',
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
        'since' => '1.1',
        'default' => '3'
      ),       
      'pinterest_photo_number' => array(
        'name' => 'pinterest_photo_number',
        'title' => 'Number of photos : ',
        'type' => 'range',
        'min' => '1',
        'max' => '20',
        'description' => '',
        'since' => '1.1',
        'default' => '4'
      ),
      'style_shadow' => array(
        'name' => 'style_shadow',
        'title' => 'Add slight image shadow.',
        'type' => 'checkbox',
        'description' => '',
        'since' => '1.1',
        'default' => ''
      ),   
      'style_border' => array(
        'name' => 'style_border',
        'title' => 'Add white image border.',
        'type' => 'checkbox',
        'description' => '',
        'since' => '1.1',
        'default' => ''
      ),
      'style_curve_corners' => array(
        'name' => 'style_curve_corners',
        'title' => 'Add slight curve to corners.',
        'type' => 'checkbox',
        'description' => '',
        'since' => '1.1',
        'default' => ''
      ),          
      'widget_alignment' => array(
        'name' => 'widget_alignment',
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
        'since' => '1.1',
        'default' => 'center'
      ),    
      'widget_max_width' => array(
        'name' => 'widget_max_width',
        'title' => 'Max widget width (%) : ',
        'type' => 'text',
        'sanitize' => 'int',
        'min' => '1',
        'max' => '100',
        'description' => "To reduce the widget width, input a percentage (between 1 and 100). If photos are smaller than widget area, reduce percentage until desired width is achieved.",
        'since' => '1.1',
        'default' => '100'
      ),        
      'widget_disable_credit_link' => array(
        'name' => 'widget_disable_credit_link',
        'title' => 'Disable the tiny link in the bottom left corner, though I have spent months developing this plugin and would appreciate the credit.',
        'type' => 'checkbox',
        'description' => '',
        'since' => '1.1',
        'default' => ''
      ),      
    );
    return $options;
  }
  
  
  ?>
