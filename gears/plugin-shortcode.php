<?php
/**
 * Alpine PhotoTile for Flickr: Shortcode
 *
 * @since 1.1.1
 *
 */
 
  function APTFPINbyTAP_generate_shortcode( $options, $optiondetails ){
    $short = '['.APTFPINbyTAP_SHORT;
    $trigger = '';
    
    foreach( $options as $key=>$value ){
      if($value && $optiondetails[$key]['short']){
        if( $optiondetails[$key]['child'] && $optiondetails[$key]['hidden'] ){
          $hidden = @explode(' ',$optiondetails[$key]['hidden']);
          if( !in_array( $options[ $optiondetails[$key]['child'] ] ,$hidden) ){
            $short .= ' '.$optiondetails[$key]['short'].'="'.$value.'"';
          }
        }else{
          $short .= ' '.$optiondetails[$key]['short'].'="'.$value.'"';
        }
      }
    }
    $short .= ']';
    
    return $short;
  }

  function APTFPINbyTAP_shortcode_function( $atts ) {
    wp_enqueue_style('APTFPINbyTAP_widget_css');
    wp_enqueue_script('APTFPINbyTAP_tiles');

    $optiondetails = APTFPINbyTAP_option_defaults();
    $options = array();
    
    foreach( $optiondetails as $opt=>$details ){
      $options[$opt] = $details['default'];
      if( $atts[ $details['short'] ] ){
        $options[$opt] = $atts[ $details['short'] ];
      }
    }
    if( $options['pinterest_pin_it_button'] ) {
      wp_enqueue_script('pinterest_pinit');
    }
    
    $id = rand(100, 1000);
    
    $source_results = APTFPINbyTAP_photo_retrieval($id, $options, $optiondetails);
    
    $return .= '<div id="'.APTFPINbyTAP_ID.'-by-shortcode-'.$id.'" class="APTFPINbyTAP_inpost_container">';
    $return .= $source_results['hidden'];
    if( $source_results['continue'] ){  
      switch ($options['style_option']) {
        case "vertical":
          $return .= APTFPINbyTAP_display_vertical($id, $options, $source_results);
        break;
        case "windows":
          $return .= APTFPINbyTAP_display_hidden($id, $options, $source_results);
        break; 
        case "bookshelf":
          $return .= APTFPINbyTAP_display_hidden($id, $options, $source_results);
        break;
        case "rift":
          $return .= APTFPINbyTAP_display_hidden($id, $options, $source_results);
        break;
        case "floor":
          $return .= APTFPINbyTAP_display_hidden($id, $options, $source_results);
        break;
        case "wall":
          $return .= APTFPINbyTAP_display_hidden($id, $options, $source_results);
        break;
        case "cascade":
          $return .= APTFPINbyTAP_display_cascade($id, $options, $source_results);
        break;
        case "gallery":
          $return .= APTFPINbyTAP_display_hidden($id, $options, $source_results);
        break;
      }
    }
    // If user does not have necessary extensions 
    // or error occured before content complete, report such...
    else{
      $return .= 'Sorry:<br>'.$source_results['message'];
    }
    $return .= $after_widget;
    $return .= '</div>';
    
    return $return;
  }
  add_shortcode( APTFPINbyTAP_SHORT, 'APTFPINbyTAP_shortcode_function' );
   
?>