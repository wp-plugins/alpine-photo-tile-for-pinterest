<?php
/**
 * Alpine PhotoTile for Pinterest: Style Display Functions
 *
 * @since 1.0.0
 */
 
 
function APTFPINbyTAP_display_vertical($id, $options, $source_results){
  $APTFPINbyTAP_linkurl = $source_results['image_perms'];
  $APTFPINbyTAP_photocap = $source_results['image_captions'];
  $APTFPINbyTAP_photourl = $source_results['image_urls'];
  $APTFPINbyTAP_user_link = $source_results['user_link'];
  $APTFPINbyTAP_originalurl = $source_results['image_originals'];

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////       Check Content      /////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  if($options['pinterest_photo_number'] != count($APTFPINbyTAP_photourl)){$options['pinterest_photo_number']=count($APTFPINbyTAP_photourl);}
  
  for($i = 0;$i<count($APTFPINbyTAP_photocap);$i++){
    $APTFPINbyTAP_photocap[$i] = str_replace('"','',$APTFPINbyTAP_photocap[$i]);
  }
  
  if($APTFPINbyTAP_reduced_width && $APTFPINbyTAP_reduced_width<$APTFPINbyTAP_size ){
    $APTFPINbyTAP_style_width = $APTFPINbyTAP_reduced_width."px";   }
  else{   $APTFPINbyTAP_style_width = $APTFPINbyTAP_size."px";    }
  
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////   Begin the Content   /////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                    
  $output .= '<div id="'.$id.'-APTFPINbyTAP_container" class="APTFPINbyTAP_container_class">';     
  
  // Align photos
  $output .= '<div id="'.$id.'-vertical-parent" class="APTFPINbyTAP_parent_class" style="width:'.$options['pinterest_photo_size'].'px;max-width:'.$options['widget_max_width'].'%;padding:0px;';
  if( 'center' == $options['widget_alignment'] ){                          //  Optional: Set text alignment (left/right) or center
    $output .= 'margin:0px auto;text-align:center;';
  }
  else{
    $output .= 'float:' . $options['widget_alignment'] . ';text-align:' . $options['widget_alignment'] . ';';
  } 
  $output .= '">';
  
  $shadow = ($options['style_shadow']?'APTFPINbyTAP-img-shadow':'APTFPINbyTAP-img-noshadow');
  $border = ($options['style_border']?'APTFPINbyTAP-img-border':'APTFPINbyTAP-img-noborder');
  $curves = ($options['style_curve_corners']?'APTFPINbyTAP-img-corners':'APTFPINbyTAP-img-nocorners');
  
  for($i = 0;$i<$options['pinterest_photo_number'];$i++){
    $output .= '<div class="APTFPINbyTAP-pinterest-container" >';
      if( $options['pinterest_image_link'] ){ $output .= '<a href="' . $APTFPINbyTAP_linkurl[$i] . '" class="APTFPINbyTAP-vertical-link" target="_blank" title='."'". $APTFPINbyTAP_photocap[$i] ."'".'>'; }
      $output .= '<img id="'.$id.'-tile-'.$i.'" class="APTFPINbyTAP-image '.$shadow.' '.$border.' '.$curves.'" src="' . $APTFPINbyTAP_photourl[$i] . '" ';
      $output .= 'title='."'". $APTFPINbyTAP_photocap[$i] ."'".' alt='."'". $APTFPINbyTAP_photocap[$i] ."' "; // Careful about caps with ""
      $output .= 'border="0" hspace="0" vspace="0" />'; // Override the max-width set by theme
      if( $options['pinterest_image_link'] ){ $output .= '</a>'; }
      
      if($options['pinterest_pin_it_button']){
        $output .= '<a href="http://pinterest.com/pin/create/button/?media='.$APTFPINbyTAP_originalurl[$i].'&url='.get_option( 'siteurl' ).'" class="pin-it-button" count-layout="horizontal" target="_blank"><div class="APTFPINbyTAP-pin-it"></div></a>';
      }
    $output .= '</div>';
  }
  
  $APTFPINbyTAP_by_link  =  '<div id="'.$id.'-by-link" class="APTFPINbyTAP-by-link"><a href="http://thealpinepress.com/" style="COLOR:#C0C0C0;text-decoration:none;" title="Widget by The Alpine Press">TAP</a></div>';   
  if( !$options['widget_disable_credit_link'] ){
    $output .=  $APTFPINbyTAP_by_link;    
  }          
  // Close vertical-parent
  $output .= '</div>';    

  if($APTFPINbyTAP_user_link){ 
    $output .= '<div id="'.$id.'-display-link" class="APTFPINbyTAP-display-link-container" ';
    $output .= 'style="text-align:' . $options['widget_alignment'] . ';">'.$APTFPINbyTAP_user_link.'</div>'; // Only breakline if floating
  }

  // Close container
  $output .= '</div>';
  $output .= '<div class="APTFPINbyTAP_breakline"></div>';
 
  echo $output;
  
  if( $options['style_shadow'] || $options['style_border'] || $options['style_curve_corners'] ){
    echo '<script>
          jQuery(window).load(function() {
            if( jQuery().APTFPINbyTAPAdjustBordersPlugin ){
              jQuery("#'.$id.'-vertical-parent").APTFPINbyTAPAdjustBordersPlugin();
            }
          });
        </script>';  
  }
}  

function APTFPINbyTAP_display_cascade($id, $options, $source_results){
  $APTFPINbyTAP_linkurl = $source_results['image_perms'];
  $APTFPINbyTAP_photocap = $source_results['image_captions'];
  $APTFPINbyTAP_photourl = $source_results['image_urls'];
  $APTFPINbyTAP_user_link = $source_results['user_link'];
  $APTFPINbyTAP_originalurl = $source_results['image_originals'];

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////       Check Content      /////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  if($options['pinterest_photo_number'] != count($APTFPINbyTAP_photourl)){$options['pinterest_photo_number']=count($APTFPINbyTAP_photourl);}
  
  for($i = 0;$i<count($APTFPINbyTAP_photocap);$i++){
    $APTFPINbyTAP_photocap[$i] = str_replace('"','',$APTFPINbyTAP_photocap[$i]);
  }
  
  if($APTFPINbyTAP_reduced_width && $APTFPINbyTAP_reduced_width<$APTFPINbyTAP_size ){
    $APTFPINbyTAP_style_width = $APTFPINbyTAP_reduced_width."px";   }
  else{   $APTFPINbyTAP_style_width = $APTFPINbyTAP_size."px";    }
  
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////   Begin the Content   /////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
          
  $output .= '<div id="'.$id.'-APTFPINbyTAP_container" class="APTFPINbyTAP_container_class">';     
  
  // Align photos
  $output .= '<div id="'.$id.'-cascade-parent" class="APTFPINbyTAP_parent_class" style="width:100%;max-width:'.$options['widget_max_width'].'%;padding:0px;';
  if( 'center' == $options['widget_alignment'] ){                          //  Optional: Set text alignment (left/right) or center
    $output .= 'margin:0px auto;text-align:center;';
  }
  else{
    $output .= 'float:' . $options['widget_alignment'] . ';text-align:' . $options['widget_alignment'] . ';';
  } 
  $output .= '">';
  
  $shadow = ($options['style_shadow']?'APTFPINbyTAP-img-shadow':'APTFPINbyTAP-img-noshadow');
  $border = ($options['style_border']?'APTFPINbyTAP-img-border':'APTFPINbyTAP-img-noborder');
  $curves = ($options['style_curve_corners']?'APTFPINbyTAP-img-corners':'APTFPINbyTAP-img-nocorners'); 
   
  for($col = 0; $col<$options['style_column_number'];$col++){
    $output .= '<div class="APTFPINbyTAP_cascade_column" style="width:'.(100/$options['style_column_number']- 1 - 1/$options['style_column_number']).'%;float:left;margin:0 0 0 1%;">';
    for($i = $col;$i<$options['pinterest_photo_number'];$i+=$options['style_column_number']){
      $output .= '<div  class="APTFPINbyTAP-pinterest-container" >';
      if( $options['pinterest_image_link'] ){ $output .= '<a href="' . $APTFPINbyTAP_linkurl[$i] . '" class="APTFPINbyTAP-vertical-link" target="_blank" title='."'". $APTFPINbyTAP_photocap[$i] ."'".'>'; }
      $output .= '<img id="'.$id.'-tile-'.$i.'" class="APTFPINbyTAP-image '.$shadow.' '.$border.' '.$curves.'" src="' . $APTFPINbyTAP_photourl[$i] . '" ';
      $output .= 'title='."'". $APTFPINbyTAP_photocap[$i] ."'".' alt='."'". $APTFPINbyTAP_photocap[$i] ."' "; // Careful about caps with ""
      $output .= 'border="0" hspace="0" vspace="0" />'; // Override the max-width set by theme
      if($options['pinterest_pin_it_button']){
        $output .= '<a href="http://pinterest.com/pin/create/button/?media='.$APTFPINbyTAP_originalurl[$i].'&url='.get_option( 'siteurl' ).'" class="pin-it-button" count-layout="horizontal" target="_blank"><div class="APTFPINbyTAP-pin-it"></div></a>';
      }
      if( $options['pinterest_image_link'] ){ $output .= '</a>'; }
      $output .= '</div>';
    }
    $output .= '</div>';
  }
  
  $output .= '<div class="APTFPINbyTAP_breakline"></div>';
    
  $APTFPINbyTAP_by_link  =  '<div id="'.$id.'-by-link" class="APTFPINbyTAP-by-link"><a href="http://thealpinepress.com/" style="COLOR:#C0C0C0;text-decoration:none;" title="Widget by The Alpine Press">TAP</a></div>';      
  if( !$options['widget_disable_credit_link'] ){
    $output .=  $APTFPINbyTAP_by_link;    
  }          
  // Close vertical-parent
  $output .= '</div>';    

  $output .= '<div class="APTFPINbyTAP_breakline"></div>';
  
  if($APTFPINbyTAP_user_link){ 
    if($options['widget_alignment'] == 'center'){                          //  Optional: Set text alignment (left/right) or center
      $output .= '<div id="'.$id.'-display-link" class="APTFPINbyTAP-display-link-container" ';
      $output .= 'style="width:100%;margin:0px auto;">'.$APTFPINbyTAP_user_link.'</div>';
    }
    else{
      $output .= '<div id="'.$id.'-display-link" class="APTFPINbyTAP-display-link-container" ';
      $output .= 'style="float:' . $options['widget_alignment'] . ';width:'.$options['pinterest_photo_size'].'px;max-width:'.$options['widget_max_width'].'%;"><center>'.$APTFPINbyTAP_user_link.'</center></div>'; // Only breakline if floating
    } 
  }

  // Close container
  $output .= '</div>';
  $output .= '<div class="APTFPINbyTAP_breakline"></div>';
 
  echo $output;
  
  echo '<script>
          jQuery(window).load(function() {
            if( jQuery().APTFPINbyTAPAdjustBordersPlugin ){
              jQuery("#'.$id.'-cascade-parent").APTFPINbyTAPAdjustBordersPlugin();
            }
          });
        </script>';
}


function APTFPINbyTAP_display_hidden($id, $options, $source_results){
  $APTFPINbyTAP_linkurl = $source_results['image_perms'];
  $APTFPINbyTAP_photocap = $source_results['image_captions'];
  $APTFPINbyTAP_photourl = $source_results['image_urls'];
  $APTFPINbyTAP_user_link = $source_results['user_link'];
  $APTFPINbyTAP_originalurl = $source_results['image_originals'];

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////       Check Content      /////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  if($options['pinterest_photo_number'] != count($APTFPINbyTAP_photourl)){$options['pinterest_photo_number']=count($APTFPINbyTAP_photourl);}
  
  for($i = 0;$i<count($APTFPINbyTAP_photocap);$i++){
    $APTFPINbyTAP_photocap[$i] = str_replace('"','',$APTFPINbyTAP_photocap[$i]);
  }
  
  if($APTFPINbyTAP_reduced_width && $APTFPINbyTAP_reduced_width<$APTFPINbyTAP_size ){
    $APTFPINbyTAP_style_width = $APTFPINbyTAP_reduced_width."px";   }
  else{   $APTFPINbyTAP_style_width = $APTFPINbyTAP_size."px";    }
  
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////   Begin the Content   /////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
          
  $output .= '<div id="'.$id.'-APTFPINbyTAP_container" class="APTFPINbyTAP_container_class">';     
  
  // Align photos
  $output .= '<div id="'.$id.'-hidden-parent" class="APTFPINbyTAP_parent_class" style="width:'.$options['pinterest_photo_size'].'px;max-width:'.$options['widget_max_width'].'%;padding:0px;';
  if( 'center' == $options['widget_alignment'] ){                          //  Optional: Set text alignment (left/right) or center
    $output .= 'margin:0px auto;text-align:center;';
  }
  else{
    $output .= 'float:' . $options['widget_alignment'] . ';text-align:' . $options['widget_alignment'] . ';';
  } 
  $output .= '">';
  
  $output .= '<div id="'.$id.'-image-list" class="APTFPINbyTAP_image_list_class" style="display:none;visibility:hidden;">'; 
  
  $shadow = ($options['style_shadow']?'APTFPINbyTAP-img-shadow':'APTFPINbyTAP-img-noshadow');
  $border = ($options['style_border']?'APTFPINbyTAP-img-border':'APTFPINbyTAP-img-noborder');
  $curves = ($options['style_curve_corners']?'APTFPINbyTAP-img-corners':'APTFPINbyTAP-img-nocorners');
  
  for($i = 0;$i<$options['pinterest_photo_number'];$i++){
    if( $options['pinterest_image_link'] ){ $output .= '<a href="' . $APTFPINbyTAP_linkurl[$i] . '" class="APTFPINbyTAP-link" target="_blank" title='."'". $APTFPINbyTAP_photocap[$i] ."'".'>'; }
    $output .= '<img id="'.$id.'-tile-'.$i.'" class="APTFPINbyTAP-image '.$shadow.' '.$border.' '.$curves.'" src="' . $APTFPINbyTAP_photourl[$i] . '" ';
    $output .= 'title='."'". $APTFPINbyTAP_photocap[$i] ."'".' alt='."'". $APTFPINbyTAP_photocap[$i] ."' "; // Careful about caps with ""
    $output .= 'border="0" hspace="0" vspace="0" data-original="' . $APTFPINbyTAP_originalurl[$i]. '"/>'; // Override the max-width set by theme
    
    // Load original image size
    if( "gallery" == $options['style_option'] && $APTFPINbyTAP_originalurl[$i] ){
      $output .= '<img class="APTFPINbyTAP-original-image" src="' . $APTFPINbyTAP_originalurl[$i]. '" />';
    }
    
    if( $options['pinterest_image_link'] ){ $output .= '</a>'; }
  }
  $output .= '</div>';
  
  $APTFPINbyTAP_by_link  =  '<div id="'.$id.'-by-link" class="APTFPINbyTAP-by-link"><a href="http://thealpinepress.com/" style="COLOR:#C0C0C0;text-decoration:none;" title="Widget by The Alpine Press">TAP</a></div>';   
  if( !$options['widget_disable_credit_link'] ){
    $output .=  $APTFPINbyTAP_by_link;    
  }          
  // Close vertical-parent
  $output .= '</div>';      

  if($APTFPINbyTAP_user_link){ 
    if($options['widget_alignment'] == 'center'){                          //  Optional: Set text alignment (left/right) or center
      $output .= '<div id="'.$id.'-display-link" class="APTFPINbyTAP-display-link-container" ';
      $output .= 'style="width:100%;margin:0px auto;">'.$APTFPINbyTAP_user_link.'</div>';
    }
    else{
      $output .= '<div id="'.$id.'-display-link" class="APTFPINbyTAP-display-link-container" ';
      $output .= 'style="float:' . $options['widget_alignment'] . ';width:'.$options['pinterest_photo_size'].'px;max-width:'.$options['widget_max_width'].'%;"><center>'.$APTFPINbyTAP_user_link.'</center></div>'; // Only breakline if floating
    } 
  }

  // Close container
  $output .= '</div>';
 
  echo $output;
  
  echo '<script>
          jQuery(window).load(function() {
            if( jQuery().APTFPINbyTAPDisplayPlugin ){
              jQuery("#'.$id.'-hidden-parent").APTFPINbyTAPDisplayPlugin({
                style:"'.($options['style_option']?$options['style_option']:'windows').'",
                shape:"'.($options['style_shape']?$options['style_shape']:'square').'",
                perRow:"'.($options['style_photo_per_row']?$options['style_photo_per_row']:'3').'",
                imageLink:'.($options['pinterest_image_link']?'1':'0').',
                imageBorder:'.($options['style_border']?'1':'0').',
                imageShadow:'.($options['style_shadow']?'1':'0').',
                imageCurve:'.($options['style_curve_corners']?'1':'0').',
                pinIt:'.($options['pinterest_pin_it_button']?'1':'0').',
                siteURL:"'.get_option( 'siteurl' ).'"
              });
            }  
          });
        </script>';
}

?>