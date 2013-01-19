<?php


class PhotoTileForPinterestBot extends PhotoTileForPinterestBasic{  

   /**
   * Alpine PhotoTile for Pinterest: Photo Retrieval Function
   * The PHP for retrieving content from Pinterest.
   *
   * @since 1.0.0
   * @updated 1.0.3
   */
   
  /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  ///////////////////////////////////////////    Generate Image Content    ////////////////////////////////////////////////
  /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

  function photo_retrieval($id, $pinterest_options){
    $defaults = $this->option_defaults();
    
    $uid = apply_filters( $this->hook, empty($pinterest_options['pinterest_user_id']) ? 'uid' : $pinterest_options['pinterest_user_id'], $pinterest_options );
    $uid = @ereg_replace('[[:cntrl:]]', '', $uid ); // remove ASCII's control characters
    $board = apply_filters( $this->hook, empty($pinterest_options['pinterest_user_board']) ? 'board' : $pinterest_options['pinterest_user_board'], $pinterest_options );
    $board = @ereg_replace('[[:cntrl:]]', '', $board ); // remove ASCII's control characters

    $key = 'pinterest-'.$this->vers.'-'.$pinterest_options['pinterest_source'].'-'.$uid.'-'.$board.'-link-'.$pinterest_options['pinterest_display_link'].'-'.$pinterest_options['pinterest_display_link_style'].'-'.$pinterest_options['pinterest_photo_number'].'-'.$pinterest_options['pinterest_photo_size'];

    $disablecache = $this->get_option( 'cache_disable' );
    if ( !$disablecache ) {
      if( $this->cacheExists($key) ) {
        $results = $this->getCache($key);
        $results = @unserialize($results);
        if( count($results) ){
          $results['hidden'] .= '<!-- Retrieved from cache -->';
          return $results;
        }
      }
    }
    
    $message = '';
    $hidden = '';
    $continue = false;
    $feed_found = false;
    $linkurl = array();
    $photocap = array();
    $photourl = array();

    
    // Determine image size id
    switch ($pinterest_options['pinterest_photo_size']) {
      case 75:
        $size_id = '_t.';
      break;
      case 192:
        $size_id = '_b.';
      break;
      case 554:
        $size_id = '_c.';
      break;
      case 600:
        $size_id = '_f.';
      break;
      case 930:
        $size_id = '.';
      break;
    }    
    
    ///////////////////////////////////////////////////
    /// If nothing found, try using xml and rss_200 ///
    ///////////////////////////////////////////////////

    if ( function_exists('simplexml_load_file') ) {
      $pinterest_uid = apply_filters( $this->hook, empty($pinterest_options['pinterest_user_id']) ? '' : $pinterest_options['pinterest_user_id'], $pinterest_options );
      switch ($pinterest_options['pinterest_source']) {
        case 'user':
          $request = 'http://pinterest.com/'.$pinterest_uid.'/feed.rss';
        break;
        case 'board':
          $pinterest_board = apply_filters( $this->hook, empty($pinterest_options['pinterest_user_board']) ? '' : $pinterest_options['pinterest_user_board'], $pinterest_options );
          $request = 'http://pinterest.com/'.$pinterest_uid.'/'.$pinterest_board.'/rss';
        break;
      }

      $_pinteresturl  = @urlencode( $request );	// just for compatibility
      $_pinterest_xml = @simplexml_load_file( $_pinteresturl,"SimpleXMLElement",LIBXML_NOCDATA); // @ is shut-up operator
      if($_pinterest_xml===false){ 
        $hidden .= '<!-- Failed using simplexml_load_file() and XML @ '.$request.' -->';
        $continue = false;
      }else{
        $title = $_pinterest_xml->channel->title;
        $link = $_pinterest_xml->channel->link;
        
        if(!$_pinterest_xml && !$_pinterest_xml->channel){
          $hidden .= '<!-- No photos found using simplexml_load_file() and XML @ '.$request.' -->';
          $continue = false;
        }else{
          $s = 0; // simple counter
          foreach( $_pinterest_xml->channel->item as $p ) { // This will prevent empty images from being added to linkurl.
            if( $s<$pinterest_options['pinterest_photo_number'] ){
              // list of link urls
              $linkurl[$s] = (string) $p->link; // ->i is equivalent of ['i'] for objects
              if($linkurl[$s]){
                $content = (string) $p->description;
                // For Reference: regex references and http://php.net/manual/en/function.preg-match.php
                // Using the RSS feed will require some manipulation to get the image url from pinterest;
                // preg_replace is bad at skipping lines so we'll start with preg_match
                  // i sets letters in upper or lower case,
                @preg_match( "/img(.*?)>/i", $content , $matches ); // First, get image from feed.
                // Next, strip away everything surrounding the source url.
                  // . means any expression, and + means repeat previous
                $photourl_current = @preg_replace(array('/(.*)src="/i','/"(.*)/') , '',$matches[ 0 ]);
                //echo $photourl_current;
                // Finally, change the size. [] specifies single character and \w is any word character
                $photourl[$s] = @preg_replace('/[_]\w[.]/', $size_id, $photourl_current );
                
                $originalurl[$s] = @preg_replace('/[_]\w[.]/', '.', $photourl_current );
                $photocap[$s] = (string) $p->title;
              }
              $s++;
            }
            else{
              break;
            }
          }
          if(!empty($linkurl) && !empty($photourl)){
            if( $pinterest_options['pinterest_display_link'] ) {
              $linkStyle = $pinterest_options['pinterest_display_link_style'];
              if ($linkStyle == 'large') { 
                $user_link .= '<div class="AlpinePhotoTiles-display-link" ><a href="http://pinterest.com/'. $pinterest_uid .'/" target="_blank" title="Follow Me on Pinterest">';
                $user_link .= '<img src="http://passets-cdn.pinterest.com/images/follow-on-pinterest-button.png" alt="Follow Me on Pinterest" border="0" class="AlpinePhotoTiles-image-link"/>';
                $user_link .= '</a></div>';
              } elseif ($linkStyle == 'medium') { 
                $user_link .= '<div class="AlpinePhotoTiles-display-link" ><a href="http://pinterest.com/'. $pinterest_uid .'/" target="_blank" title="Follow Me on Pinterest">';
                $user_link .= '<img src="http://passets-cdn.pinterest.com/images/pinterest-button.png" alt="Follow Me on Pinterest" border="0" class="AlpinePhotoTiles-image-link" />';
                $user_link .= '</a></div>';
              } elseif ($linkStyle == 'small') { 
                $user_link .= '<div class="AlpinePhotoTiles-display-link" ><a href="http://pinterest.com/'. $pinterest_uid .'/" target="_blank" title="Follow Me on Pinterest">';
                $user_link .= '<img src="http://passets-cdn.pinterest.com/images/big-p-button.png" width="61" height="61" alt="Follow Me on Pinterest" border="0" class="AlpinePhotoTiles-image-link" />';
                $user_link .= '</a></div>';
              } elseif ($linkStyle == 'tiny') { 
                $user_link .= '<div class="AlpinePhotoTiles-display-link" ><a href="http://pinterest.com/'. $pinterest_uid .'/" target="_blank" title="Follow Me on Pinterest" >';
                $user_link .= '<img src="http://passets-cdn.pinterest.com/images/small-p-button.png" width="16" height="16" alt="Follow Me on Pinterest" border="0" class="AlpinePhotoTiles-image-link"/>';
                $user_link .= '</a></div>';
              } elseif ($linkStyle == 'text') {
                $user_link .= '<div class="AlpinePhotoTiles-display-link" >';
                $user_link .= '<a href="http://pinterest.com/'.$pinterest_uid.'/" target="_blank" >';
                $user_link .= $title;
                $user_link .= '</a></div>';
              } else {
                $user_link .= '<div class="AlpinePhotoTiles-display-link" >';
                $user_link .= '<a href="http://pinterest.com/'.$pinterest_uid .'/" target="_blank" >';
                $user_link .= $title;
                $user_link .= '</a></div>';
              }
            }
            // If content successfully fetched, generate output...
            $continue = true;
            $hidden .= '<!-- Success using simplexml_load_file() and XML -->';
          }else{
            $hidden .= '<!-- No photos found using simplexml_load_file() and XML @ '.$request.' -->';
            $continue = false;
            $feed_found = true;
          }
        }
      }
    }
    
    ////////////////////////////////////////////////////////
    ////      If still nothing found, try using RSS      ///
    ////////////////////////////////////////////////////////
    if( $continue == false ) {
      // RSS may actually be safest approach since it does not require PHP server extensions,
      // but I had to build my own method for parsing SimplePie Object so I will keep it as the last option.
      
      if(!function_exists('APTFPINbyTAP_specialarraysearch')){
        function APTFPINbyTAP_specialarraysearch($array, $find){
          foreach ($array as $key=>$value){
            if( is_string($key) && $key==$find){
              return $value;
            }
            elseif(is_array($value)){
              $results = APTFPINbyTAP_specialarraysearch($value, $find);
            }
            elseif(is_object($value)){
              $sub = $array->$key;
              $results = APTFPINbyTAP_specialarraysearch($sub, $find);
            }
            // If found, return
            if(!empty($results)){return $results;}
          }
          return $results;
        }
      }
       
      $pinterest_uid = apply_filters( $this->hook, empty($pinterest_options['pinterest_user_id']) ? '' : $pinterest_options['pinterest_user_id'], $pinterest_options );
      switch ($pinterest_options['pinterest_source']) {
        case 'user':
          $request = 'http://pinterest.com/'.$pinterest_uid.'/feed.rss';
        break;
        case 'board':
          $pinterest_board = apply_filters( $this->hook, empty($pinterest_options['pinterest_user_board']) ? '' : $pinterest_options['pinterest_user_board'], $pinterest_options );
          $request = 'http://pinterest.com/'.$pinterest_uid.'/'.$pinterest_board.'/rss';
        break;
      } 
      include_once(ABSPATH . WPINC . '/feed.php');
      
      if( !function_exists('return_noCache') ){
        function return_noCache( $seconds ){
          // change the default feed cache recreation period to 30 seconds
          return 30;
        }
      }

      add_filter( 'wp_feed_cache_transient_lifetime' , 'return_noCache' );
      $rss = @fetch_feed( $request );
      remove_filter( 'wp_feed_cache_transient_lifetime' , 'return_noCache' );

      if (!is_wp_error( $rss ) && $rss != NULL ){ // Check that the object is created correctly 
        // Bulldoze through the feed to find the items 
        $results = array();
        $title = @APTFPINbyTAP_specialarraysearch($rss,'title');
        $title = $title['0']['data'];
        $link = @APTFPINbyTAP_specialarraysearch($rss,'link');
        $link = $link['0']['data'];
        $rss_data = @APTFPINbyTAP_specialarraysearch($rss,'item');

        $s = 0; // simple counter
        if ($rss_data != NULL ){ // Check again
          foreach ( $rss_data as $item ) {
            if( $s<$pinterest_options['pinterest_photo_number'] ){
              $linkurl[$s] = $item['child']['']['link']['0']['data'];    
              $content = $item['child']['']['description']['0']['data'];     
              if($content){
                // For Reference: regex references and http://php.net/manual/en/function.preg-match.php
                // Using the RSS feed will require some manipulation to get the image url from pinterest;
                // preg_replace is bad at skipping lines so we'll start with preg_match
                // i sets letters in upper or lower case, s sets . to anything
                @preg_match("/<IMG.+?SRC=[\"']([^\"']+)/si",$content,$matches); // First, get image from feed.
                if($matches[ 0 ]){
                  // Next, strip away everything surrounding the source url.
                  // . means any expression and + means repeat previous
                  $photourl_current = @preg_replace(array('/(.+)src="/i','/"(.+)/') , '',$matches[ 0 ]);
                  // Finally, change the size. 
                    // [] specifies single character and \w is any word character
                  $photourl[$s] = @preg_replace('/[_]\w[.]/', $size_id, $photourl_current );
                  $originalurl[$s] = @preg_replace('/[_]\w[.]/', '.', $photourl_current );
                  // Could set the caption as blank instead of default "Photo", but currently not doing so.
                  $photocap[$s] = $item['child']['']['title']['0']['data'];
                  $s++;
                }
              }
            }
            else{
              break;
            }
          }
        }
        if(!empty($linkurl) && !empty($photourl)){
            if( $pinterest_options['pinterest_display_link'] ) {
              $linkStyle = $pinterest_options['pinterest_display_link_style'];
              if ($linkStyle == 'large') { 
                $user_link .= '<div class="AlpinePhotoTiles-display-link" ><a href="http://pinterest.com/'. $pinterest_uid .'/" target="_blank" title="Follow Me on Pinterest">';
                $user_link .= '<img src="http://passets-cdn.pinterest.com/images/follow-on-pinterest-button.png" alt="Follow Me on Pinterest" border="0" class="AlpinePhotoTiles-image-link"/>';
                $user_link .= '</a></div>';
              } elseif ($linkStyle == 'medium') { 
                $user_link .= '<div class="AlpinePhotoTiles-display-link" ><a href="http://pinterest.com/'. $pinterest_uid .'/" target="_blank" title="Follow Me on Pinterest">';
                $user_link .= '<img src="http://passets-cdn.pinterest.com/images/pinterest-button.png" alt="Follow Me on Pinterest" border="0" class="AlpinePhotoTiles-image-link" />';
                $user_link .= '</a></div>';
              } elseif ($linkStyle == 'small') { 
                $user_link .= '<div class="AlpinePhotoTiles-display-link" ><a href="http://pinterest.com/'. $pinterest_uid .'/" target="_blank" title="Follow Me on Pinterest">';
                $user_link .= '<img src="http://passets-cdn.pinterest.com/images/big-p-button.png" width="61" height="61" alt="Follow Me on Pinterest" border="0" class="AlpinePhotoTiles-image-link" />';
                $user_link .= '</a></div>';
              } elseif ($linkStyle == 'tiny') { 
                $user_link .= '<div class="AlpinePhotoTiles-display-link" ><a href="http://pinterest.com/'. $pinterest_uid .'/" target="_blank" title="Follow Me on Pinterest" >';
                $user_link .= '<img src="http://passets-cdn.pinterest.com/images/small-p-button.png" width="16" height="16" alt="Follow Me on Pinterest" border="0" class="AlpinePhotoTiles-image-link"/>';
                $user_link .= '</a></div>';
              } elseif ($linkStyle == 'text') {
                $user_link .= '<div class="AlpinePhotoTiles-display-link" >';
                $user_link .= '<a href="http://pinterest.com/'.$pinterest_uid.'/" target="_blank" >';
                $user_link .= $title;
                $user_link .= '</a></div>';
              } else {
                $user_link .= '<div class="AlpinePhotoTiles-display-link" >';
                $user_link .= '<a href="http://pinterest.com/'.$pinterest_uid .'/" target="_blank" >';
                $user_link .= $title;
                $user_link .= '</a></div>';
              }
            }
          // If content successfully fetched, generate output...
          $continue = true;
          $hidden .= '<!-- Success using fetch_feed() and RSS -->';
        }else{
          $hidden .= '<!-- No photos found using fetch_feed() and RSS @ '.$request.' -->';  
          $continue = false;
          $feed_found = true;
        }
      }
      else{
        $hidden .= '<!-- Failed using fetch_feed() and RSS @ '.$request.' -->';
        $continue = false;
      }      
    }
      
    ///////////////////////////////////////////////////////////////////////
    //// If STILL!!! nothing found, report that Pinterest ID must be wrong ///
    ///////////////////////////////////////////////////////////////////////
    if( false == $continue ) {
      if($feed_found ){
        $message .= '- Pinterest feed was successfully retrieved, but no photos found.';
      }else{
        $message .= '- Pinterest feed not found. Please recheck your ID.';
      }
    }
      
    $results = array('continue'=>$continue,'message'=>$message,'hidden'=>$hidden,'user_link'=>$user_link,'image_captions'=>$photocap,'image_urls'=>$photourl,'image_perms'=>$linkurl,'image_originals'=>$originalurl);
    
    if( true == $continue && !$disablecache ){     
      $cache_results = $results;
      if(!is_serialized( $cache_results  )) { $cache_results  = maybe_serialize( $cache_results ); }
      $this->putCache($key, $cache_results);
      $cachetime = $this->get_option( 'cache_time' );
      if( $cachetime && is_numeric($cachetime) ){
        $this->setExpiryInterval( $cachetime*60*60 );
      }
    }
    return $results;
  }
  
  
  
/**
 *  Function for printing vertical style
 *  
 *  @ Since 0.0.1
 */
  function display_vertical($id, $options, $source_results){
    $linkurl = $source_results['image_perms'];
    $photocap = $source_results['image_captions'];
    $photourl = $source_results['image_urls'];
    $userlink = $source_results['user_link'];
    $originalurl = $source_results['image_originals'];

  /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  //////////////////////////////////////////       Check Content      /////////////////////////////////////////////////////
  /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    if($options['pinterest_photo_number'] != count($linkurl)){$options['pinterest_photo_number']=count($linkurl);}
            
  /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  /////////////////////////////////////////////   Begin the Content   /////////////////////////////////////////////////////
  /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                      
    $output .= '<div id="'.$id.'-AlpinePhotoTiles_container" class="AlpinePhotoTiles_container_class">';     
    
    // Align photos
    $output .= '<div id="'.$id.'-vertical-parent" class="AlpinePhotoTiles_parent_class" style="width:'.$options['pinterest_photo_size'].'px;max-width:'.$options['widget_max_width'].'%;padding:0px;';
    if( 'center' == $options['widget_alignment'] ){                          //  Optional: Set text alignment (left/right) or center
      $output .= 'margin:0px auto;text-align:center;';
    }
    else{
      $output .= 'float:' . $options['widget_alignment'] . ';text-align:' . $options['widget_alignment'] . ';';
    } 
    $output .= '">';
    
    $shadow = ($options['style_shadow']?'AlpinePhotoTiles-img-shadow':'AlpinePhotoTiles-img-noshadow');
    $border = ($options['style_border']?'AlpinePhotoTiles-img-border':'AlpinePhotoTiles-img-noborder');
    $curves = ($options['style_curve_corners']?'AlpinePhotoTiles-img-corners':'AlpinePhotoTiles-img-nocorners');
    $highlight = ($options['style_highlight']?'AlpinePhotoTiles-img-highlight':'AlpinePhotoTiles-img-nohighlight');
    
    for($i = 0;$i<$options['pinterest_photo_number'];$i++){
      $output .= '<div class="AlpinePhotoTiles-pinterest-container" style="position:relative;display:block;" >';
      $has_link = false;
      $link = $options['pinterest_image_link_option'];
      if( 'original' == $link && !empty($photourl[$i]) ){
        $output .= '<a href="' . $photourl[$i] . '" class="AlpinePhotoTiles-link" target="_blank" title='."'". $photocap[$i] ."'".'>';
        $has_link = true;
      }elseif( ('pinterest' == $link || '1' == $link)&& !empty($linkurl[$i]) ){
        $output .= '<a href="' . $linkurl[$i] . '" class="AlpinePhotoTiles-link" target="_blank" title='."'". $photocap[$i] ."'".'>';
        $has_link = true;
      }elseif( 'link' == $link && !empty($options['custom_link_url']) ){
        $output .= '<a href="' . $options['custom_link_url'] . '" class="AlpinePhotoTiles-link" target="_blank" title='."'". $photocap[$i] ."'".'>'; 
        $has_link = true;
      }elseif( 'fancybox' == $link && !empty($originalurl[$i]) ){
        $output .= '<a href="' . $originalurl[$i] . '" class="AlpinePhotoTiles-link" target="_blank" title='."'". $photocap[$i] ."'".'>'; 
        $has_link = true;
      } 
      $output .= '<img id="'.$id.'-tile-'.$i.'" class="AlpinePhotoTiles-image '.$shadow.' '.$border.' '.$curves.' '.$highlight.'" src="' . $photourl[$i] . '" ';
      $output .= 'title='."'". $photocap[$i] ."'".' alt='."'". $photocap[$i] ."' "; // Careful about caps with ""
      $output .= 'border="0" hspace="0" vspace="0" style="margin:1px 0 5px 0;padding:0;max-width:100%;"/>'; // Override the max-width set by theme
      if($options['pinterest_pin_it_button']){
        $output .= '<a href="http://pinterest.com/pin/create/button/?media='.$originalurl[$i].'&url='.get_option( 'siteurl' ).'" class="AlpinePhotoTiles-pin-it-button" count-layout="horizontal" target="_blank"><div class="AlpinePhotoTiles-pin-it"></div></a>';
      }
      if( $has_link ){ $output .= '</a>'; }
      $output .= "</div>";
    }
    
    if( !$options['widget_disable_credit_link'] ){
      $by_link  =  '<div id="'.$id.'-by-link" class="AlpinePhotoTiles-by-link"><a href="http://thealpinepress.com/" style="COLOR:#C0C0C0;text-decoration:none;" title="Widget by The Alpine Press">TAP</a></div>';   
      $output .=  $by_link;    
    }          
    // Close vertical-parent
    $output .= '</div>';    

    if($userlink){ 
      $output .= '<div id="'.$id.'-display-link" class="AlpinePhotoTiles-display-link-container" ';
      $output .= 'style="text-align:' . $options['widget_alignment'] . ';">'.$userlink.'</div>'; // Only breakline if floating
    }

    // Close container
    $output .= '</div>';
    $output .= '<div class="AlpinePhotoTiles_breakline"></div>';
    
    $highlight = $this->get_option("general_highlight_color");
    $highlight = ($highlight?$highlight:'#64a2d8');
    
    if( $options['style_shadow'] || $options['style_border'] || $options['style_highlight']  ){
      $output .= '<script>
           jQuery(window).load(function() {
              if(jQuery().AlpineAdjustBordersPlugin ){
                jQuery("#'.$id.'-vertical-parent").AlpineAdjustBordersPlugin({
                  highlight:"'.$highlight.'"
                });
              }  
            });
          </script>';  
    }   
    if( $options['pinterest_image_link_option'] == "fancybox"  ){
      $output .= '<script>
                  jQuery(window).load(function() {
                    jQuery( "a[rel^=\'fancybox-'.$id.'\']" ).fancybox( { titleShow: false, overlayOpacity: .8, overlayColor: "#000" } );
                  })
                </script>';  
    } 
    return $output;
 
  }  
/**
 *  Function for printing cascade style
 *  
 *  @ Since 0.0.1
 */
  function display_cascade($id, $options, $source_results){
    $linkurl = $source_results['image_perms'];
    $photocap = $source_results['image_captions'];
    $photourl = $source_results['image_urls'];
    $userlink = $source_results['user_link'];
    $originalurl = $source_results['image_originals'];
    
  /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  //////////////////////////////////////////       Check Content      /////////////////////////////////////////////////////
  /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    if($options['pinterest_photo_number'] != count($linkurl)){$options['pinterest_photo_number']= count($linkurl);}
            
  /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  /////////////////////////////////////////////   Begin the Content   /////////////////////////////////////////////////////
  /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            
    $output .= '<div id="'.$id.'-AlpinePhotoTiles_container" class="AlpinePhotoTiles_container_class">';     
    
    // Align photos
    $output .= '<div id="'.$id.'-cascade-parent" class="AlpinePhotoTiles_parent_class" style="width:100%;max-width:'.$options['widget_max_width'].'%;padding:0px;';
    if( 'center' == $options['widget_alignment'] ){                          //  Optional: Set text alignment (left/right) or center
      $output .= 'margin:0px auto;text-align:center;';
    }
    else{
      $output .= 'float:' . $options['widget_alignment'] . ';text-align:' . $options['widget_alignment'] . ';';
    } 
    $output .= '">';
    
    $shadow = ($options['style_shadow']?'AlpinePhotoTiles-img-shadow':'AlpinePhotoTiles-img-noshadow');
    $border = ($options['style_border']?'AlpinePhotoTiles-img-border':'AlpinePhotoTiles-img-noborder');
    $curves = ($options['style_curve_corners']?'AlpinePhotoTiles-img-corners':'AlpinePhotoTiles-img-nocorners'); 
    $highlight = ($options['style_highlight']?'AlpinePhotoTiles-img-highlight':'AlpinePhotoTiles-img-nohighlight');
    
    for($col = 0; $col<$options['style_column_number'];$col++){
      $output .= '<div class="AlpinePhotoTiles_cascade_column" style="width:'.(100/$options['style_column_number']).'%;float:left;margin:0;">';
      $output .= '<div class="AlpinePhotoTiles_cascade_column_inner" style="display:block;margin:0 3px;overflow:hidden;">';
      for($i = $col;$i<$options['pinterest_photo_number'];$i+=$options['style_column_number']){
        $output .= '<div class="AlpinePhotoTiles-pinterest-container" style="position:relative;display:block;" >';
        $has_link = false;
        $link = $options['pinterest_image_link_option'];
        if( 'original' == $link && !empty($photourl[$i]) ){
          $output .= '<a href="' . $photourl[$i] . '" class="AlpinePhotoTiles-link" target="_blank" title='."'". $photocap[$i] ."'".'>';
          $has_link = true;
        }elseif( ('pinterest' == $link || '1' == $link)&& !empty($linkurl[$i]) ){
          $output .= '<a href="' . $linkurl[$i] . '" class="AlpinePhotoTiles-link" target="_blank" title='."'". $photocap[$i] ."'".'>';
          $has_link = true;
        }elseif( 'link' == $link && !empty($options['custom_link_url']) ){
          $output .= '<a href="' . $options['custom_link_url'] . '" class="AlpinePhotoTiles-link" target="_blank" title='."'". $photocap[$i] ."'".'>'; 
          $has_link = true;
        }elseif( 'fancybox' == $link && !empty($originalurl[$i]) ){
          $output .= '<a href="' . $originalurl[$i] . '" class="AlpinePhotoTiles-link" target="_blank" title='."'". $photocap[$i] ."'".'>'; 
          $has_link = true;
        }  
      
        $output .= '<img id="'.$id.'-tile-'.$i.'" class="AlpinePhotoTiles-image '.$shadow.' '.$border.' '.$curves.' '.$highlight.'" src="' . $photourl[$i] . '" ';
        $output .= 'title='."'". $photocap[$i] ."'".' alt='."'". $photocap[$i] ."' "; // Careful about caps with ""
        $output .= 'border="0" hspace="0" vspace="0" style="margin:1px 0 5px 0;padding:0;max-width:100%;"/>'; // Override the max-width set by theme
        if($options['pinterest_pin_it_button']){
          $output .= '<a href="http://pinterest.com/pin/create/button/?media='.$originalurl[$i].'&url='.get_option( 'siteurl' ).'" class="AlpinePhotoTiles-pin-it-button" count-layout="horizontal" target="_blank"><div class="AlpinePhotoTiles-pin-it"></div></a>';
        }
        if( $has_link ){ $output .= '</a>'; }
        $output .= '</div>';
      }
      $output .= '</div></div>';
    }
    $output .= '<div class="AlpinePhotoTiles_breakline"></div>';
      
    if( !$options['widget_disable_credit_link'] ){
      $by_link  =  '<div id="'.$id.'-by-link" class="AlpinePhotoTiles-by-link"><a href="http://thealpinepress.com/" style="COLOR:#C0C0C0;text-decoration:none;" title="Widget by The Alpine Press">TAP</a></div>';      
      $output .=  $by_link;    
    }          
    // Close cascade-parent
    $output .= '</div>';    

    $output .= '<div class="AlpinePhotoTiles_breakline"></div>';
    
    if($userlink){ 
      if($options['widget_alignment'] == 'center'){                          //  Optional: Set text alignment (left/right) or center
        $output .= '<div id="'.$id.'-display-link" class="AlpinePhotoTiles-display-link-container" ';
        $output .= 'style="width:100%;margin:0px auto;">'.$userlink.'</div>';
      }
      else{
        $output .= '<div id="'.$id.'-display-link" class="AlpinePhotoTiles-display-link-container" ';
        $output .= 'style="float:' . $options['widget_alignment'] . ';width:'.$options['pinterest_photo_size'].'px;max-width:'.$options['widget_max_width'].'%;"><center>'.$userlink.'</center></div>'; // Only breakline if floating
      } 
    }

    // Close container
    $output .= '</div>';
    $output .= '<div class="AlpinePhotoTiles_breakline"></div>';
   
    $highlight = $this->get_option("general_highlight_color");
    $highlight = ($highlight?$highlight:'#64a2d8');
    
    
    if( $options['style_shadow'] || $options['style_border'] || $options['style_highlight']  ){
      $output .= '<script>
           jQuery(window).load(function() {
              if(jQuery().AlpineAdjustBordersPlugin ){
                jQuery("#'.$id.'-cascade-parent").AlpineAdjustBordersPlugin({
                  highlight:"'.$highlight.'"
                });
              }  
            });
          </script>';  
    }   
    if( $options['pinterest_image_link_option'] == "fancybox"  ){
      $output .= '<script>
                  jQuery(window).load(function() {
                    jQuery( "a[rel^=\'fancybox-'.$id.'\']" ).fancybox( { titleShow: false, overlayOpacity: .8, overlayColor: "#000" } );
                  })
                </script>';  
    } 
    return $output;
    
  }

/**
 *  Function for printing and initializing JS styles
 *  
 *  @ Since 0.0.1
 */
  function display_hidden($id, $options, $source_results){
    $linkurl = $source_results['image_perms'];
    $photocap = $source_results['image_captions'];
    $photourl = $source_results['image_urls'];
    $userlink = $source_results['user_link'];
    $originalurl = $source_results['image_originals'];

  /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  //////////////////////////////////////////       Check Content      /////////////////////////////////////////////////////
  /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    if($options['pinterest_photo_number'] != count($linkurl)){$options['pinterest_photo_number']=count($linkurl);}
        
  /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  /////////////////////////////////////////////   Begin the Content   /////////////////////////////////////////////////////
  /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            
    $output .= '<div id="'.$id.'-AlpinePhotoTiles_container" class="AlpinePhotoTiles_container_class">';     
    
    // Align photos
    $output .= '<div id="'.$id.'-hidden-parent" class="AlpinePhotoTiles_parent_class" style="width:'.$options['pinterest_photo_size'].'px;max-width:'.$options['widget_max_width'].'%;padding:0px;';
    if( 'center' == $options['widget_alignment'] ){                          //  Optional: Set text alignment (left/right) or center
      $output .= 'margin:0px auto;text-align:center;';
    }
    else{
      $output .= 'float:' . $options['widget_alignment'] . ';text-align:' . $options['widget_alignment'] . ';';
    } 
    $output .= '">';
    
    $output .= '<div id="'.$id.'-image-list" class="AlpinePhotoTiles_image_list_class" style="display:none;visibility:hidden;">'; 
    
    $shadow = ($options['style_shadow']?'AlpinePhotoTiles-img-shadow':'AlpinePhotoTiles-img-noshadow');
    $border = ($options['style_border']?'AlpinePhotoTiles-img-border':'AlpinePhotoTiles-img-noborder');
    $curves = ($options['style_curve_corners']?'AlpinePhotoTiles-img-corners':'AlpinePhotoTiles-img-nocorners');
    
    for($i = 0;$i<$options['pinterest_photo_number'];$i++){
      $has_link = false;
      $link = $options['pinterest_image_link_option'];
      if( 'original' == $link && !empty($photourl[$i]) ){
        $output .= '<a href="' . $photourl[$i] . '" class="AlpinePhotoTiles-link" target="_blank" title='."'". $photocap[$i] ."'".'>';
        $has_link = true;
      }elseif( ('pinterest' == $link || '1' == $link)&& !empty($linkurl[$i]) ){
        $output .= '<a href="' . $linkurl[$i] . '" class="AlpinePhotoTiles-link" target="_blank" title='."'". $photocap[$i] ."'".'>';
        $has_link = true;
      }elseif( 'link' == $link && !empty($options['custom_link_url']) ){
        $output .= '<a href="' . $options['custom_link_url'] . '" class="AlpinePhotoTiles-link" target="_blank" title='."'". $photocap[$i] ."'".'>'; 
        $has_link = true;
      }elseif( 'fancybox' == $link && !empty($originalurl[$i]) ){
        $output .= '<a href="' . $originalurl[$i] . '" class="AlpinePhotoTiles-link" target="_blank" title='."'". $photocap[$i] ."'".'>'; 
        $has_link = true;
      }    
      $output .= '<img id="'.$id.'-tile-'.$i.'" class="AlpinePhotoTiles-image '.$shadow.' '.$border.' '.$curves.'" src="' . $photourl[$i] . '" ';
      $output .= 'title='."'". $photocap[$i] ."'".' alt='."'". $photocap[$i] ."' "; // Careful about caps with ""
      $output .= 'border="0" hspace="0" vspace="0" />'; // Override the max-width set by theme
      
      // Load original image size
      if( "gallery" == $options['style_option'] && $originalurl[$i] ){
        $output .= '<img class="AlpinePhotoTiles-original-image" src="' . $originalurl[$i]. '" />';
      }
      if( $has_link ){ $output .= '</a>'; }
    }
    $output .= '</div>';
    
    if( !$options['widget_disable_credit_link'] ){
      $by_link  =  '<div id="'.$id.'-by-link" class="AlpinePhotoTiles-by-link"><a href="http://thealpinepress.com/" style="COLOR:#C0C0C0;text-decoration:none;" title="Widget by The Alpine Press">TAP</a></div>';   
      $output .=  $by_link;    
    }          
    // Close vertical-parent
    $output .= '</div>';      

    if($userlink){ 
      if($options['widget_alignment'] == 'center'){                          //  Optional: Set text alignment (left/right) or center
        $output .= '<div id="'.$id.'-display-link" class="AlpinePhotoTiles-display-link-container" ';
        $output .= 'style="width:100%;margin:0px auto;">'.$userlink.'</div>';
      }
      else{
        $output .= '<div id="'.$id.'-display-link" class="AlpinePhotoTiles-display-link-container" ';
        $output .= 'style="float:' . $options['widget_alignment'] . ';width:'.$options['pinterest_photo_size'].'px;max-width:'.$options['widget_max_width'].'%;"><center>'.$userlink.'</center></div>'; // Only breakline if floating
      } 
    }

    // Close container
    $output .= '</div>';
    $disable = $this->get_option("general_loader");
    $highlight = $this->get_option("general_highlight_color");
    $highlight = ($highlight?$highlight:'#64a2d8');
    
    $output .= '<script>';
    
    if(!$disable){
      $output .= '
             jQuery(document).ready(function() {
              jQuery("#'.$id.'-AlpinePhotoTiles_container").addClass("loading"); 
             });';
    }
    $output .= '
           jQuery(window).load(function() {
            jQuery("#'.$id.'-AlpinePhotoTiles_container").removeClass("loading");
            if( jQuery().AlpinePhotoTilesPlugin ){
              jQuery("#'.$id.'-hidden-parent").AlpinePhotoTilesPlugin({
                id:"'.$id.'",
                style:"'.($options['style_option']?$options['style_option']:'windows').'",
                shape:"'.($options['style_shape']?$options['style_shape']:'square').'",
                perRow:"'.($options['style_photo_per_row']?$options['style_photo_per_row']:'3').'",
                imageLink:'.($options['pinterest_image_link']?'1':'0').',
                imageBorder:'.($options['style_border']?'1':'0').',
                imageShadow:'.($options['style_shadow']?'1':'0').',
                imageCurve:'.($options['style_curve_corners']?'1':'0').',
                imageHighlight:'.($options['style_highlight']?'1':'0').',
                fancybox:'.($options['pinterest_image_link_option'] == "fancybox"?'1':'0').',
                galleryHeight:'.($options['style_gallery_height']?$options['style_gallery_height']:'3').',
                highlight:"'.$highlight.'",
                pinIt:'.($options['pinterest_pin_it_button']?'1':'0').',
                siteURL:"'.get_option( 'siteurl' ).'"
              });
            }
          });
        </script>';
        
    return $output; 
  }
 
}

?>
