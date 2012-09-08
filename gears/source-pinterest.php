<?php
/**
 * Alpine PhotoTile for Pinterest: Photo Retrieval Function
 * The PHP for retrieving content from Pinterest.
 *
 * @since 1.0.0
 */
 
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////    Generate Image Content    ////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function APTFPINbyTAP_photo_retrieval($id, $pinterest_options, $defaults){  
  $APTFPINbyTAP_pinterest_uid = apply_filters( APTFPINbyTAP_HOOK, empty($pinterest_options['pinterest_user_id']) ? 'uid' : $pinterest_options['pinterest_user_id'], $pinterest_options );
  $APTFPINbyTAP_pinterest_uid = @ereg_replace('[[:cntrl:]]', '', $APTFPINbyTAP_pinterest_uid ); // remove ASCII's control characters
  $APTFPINbyTAP_pinterest_board = apply_filters( APTFPINbyTAP_HOOK, empty($pinterest_options['pinterest_user_board']) ? 'board' : $pinterest_options['pinterest_user_board'], $pinterest_options );
  $APTFPINbyTAP_pinterest_board = @ereg_replace('[[:cntrl:]]', '', $APTFPINbyTAP_pinterest_board ); // remove ASCII's control characters

  $key = 'pinterest-'.$pinterest_options['pinterest_source'].'-'.$APTFPINbyTAP_pinterest_uid.'-'.$APTFPINbyTAP_pinterest_board.'-link-'.$pinterest_options['pinterest_display_link'].'-'.$pinterest_options['pinterest_display_link_style'].'-'.$pinterest_options['pinterest_photo_number'].'-'.$pinterest_options['pinterest_photo_size'];

  $disablecache = APTFPINbyTAP_get_option( 'cache_disable' );
  if ( class_exists( 'theAlpinePressSimpleCacheV2' ) && APTFPINbyTAP_CACHE && !$disablecache ) {
    $cache = new theAlpinePressSimpleCacheV2();  
    $cache->setCacheDir( APTFPINbyTAP_CACHE );
    
    if( $cache->exists($key) ) {
      $results = $cache->get($key);
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
  $APTFPINbyTAP_linkurl = array();
  $APTFPINbyTAP_photocap = array();
  $APTFPINbyTAP_photourl = array();

  
  // Determine image size id
  switch ($pinterest_options['pinterest_photo_size']) {
    case 75:
      $APTFPINbyTAP_size_id = '_t.';
    break;
    case 192:
      $APTFPINbyTAP_size_id = '_b.';
    break;
    case 554:
      $APTFPINbyTAP_size_id = '_c.';
    break;
    case 600:
      $APTFPINbyTAP_size_id = '_f.';
    break;
    case 930:
      $APTFPINbyTAP_size_id = '.';
    break;
  }    
  
  ///////////////////////////////////////////////////
  /// If nothing found, try using xml and rss_200 ///
  ///////////////////////////////////////////////////

  if ( !function_exists('simplexml_load_file') ) {
    $pinterest_uid = apply_filters( APTFPINbyTAP_HOOK, empty($pinterest_options['pinterest_user_id']) ? '' : $pinterest_options['pinterest_user_id'], $pinterest_options );
    switch ($pinterest_options['pinterest_source']) {
      case 'user':
        $request = 'http://pinterest.com/'.$pinterest_uid.'/feed.rss';
      break;
      case 'board':
        $pinterest_board = apply_filters( APTFPINbyTAP_HOOK, empty($pinterest_options['pinterest_user_board']) ? '' : $pinterest_options['pinterest_user_board'], $pinterest_options );
        $request = 'http://pinterest.com/'.$pinterest_uid.'/'.$pinterest_board.'/rss';
      break;
    }

    $_pinteresturl  = @urlencode( $request );	// just for compatibility
    $_pinterest_xml = @simplexml_load_file( $_pinteresturl,"SimpleXMLElement",LIBXML_NOCDATA); // @ is shut-up operator
    if($_pinterest_xml===false){ 
      $hidden .= '<!-- Failed using simplexml_load_file() and XML @ '.$request.' -->';
      $continue = false;
    }else{
      $APTFPINbyTAP_title = $_pinterest_xml->channel->title;
      $APTFPINbyTAP_link = $_pinterest_xml->channel->link;
      
      if(!$_pinterest_xml && !$_pinterest_xml->channel){
        $hidden .= '<!-- No photos found using simplexml_load_file() and XML @ '.$request.' -->';
        $continue = false;
      }else{
        $s = 0; // simple counter
        foreach( $_pinterest_xml->channel->item as $p ) { // This will prevent empty images from being added to APTFPINbyTAP_linkurl.
          if( $s<$pinterest_options['pinterest_photo_number'] ){
            // list of link urls
            $APTFPINbyTAP_linkurl[$s] = (string) $p->link; // ->i is equivalent of ['i'] for objects
            if($APTFPINbyTAP_linkurl[$s]){
              $content = (string) $p->description;
              // For Reference: regex references and http://php.net/manual/en/function.preg-match.php
              // Using the RSS feed will require some manipulation to get the image url from pinterest;
              // preg_replace is bad at skipping lines so we'll start with preg_match
                // i sets letters in upper or lower case,
              @preg_match( "/img(.*?)>/i", $content , $matches ); // First, get image from feed.
              // Next, strip away everything surrounding the source url.
                // . means any expression, and + means repeat previous
              $APTFPINbyTAP_photourl_current = @preg_replace(array('/(.*)src="/i','/"(.*)/') , '',$matches[ 0 ]);
              //echo $APTFPINbyTAP_photourl_current;
              // Finally, change the size. [] specifies single character and \w is any word character
              $APTFPINbyTAP_photourl[$s] = @preg_replace('/[_]\w[.]/', $APTFPINbyTAP_size_id, $APTFPINbyTAP_photourl_current );
              
              $APTFPINbyTAP_originalurl[$s] = @preg_replace('/[_]\w[.]/', '.', $APTFPINbyTAP_photourl_current );
              $APTFPINbyTAP_photocap[$s] = (string) $p->title;
            }
            $s++;
          }
          else{
            break;
          }
        }
        if(!empty($APTFPINbyTAP_linkurl) && !empty($APTFPINbyTAP_photourl)){
          if( $pinterest_options['pinterest_display_link'] ) {
            $linkStyle = $pinterest_options['pinterest_display_link_style'];
            if ($linkStyle == 'large') { 
              $APTFPINbyTAP_user_link .= '<a href="http://pinterest.com/'. $pinterest_uid .'/" target="_blank" title="Follow Me on Pinterest">';
              $APTFPINbyTAP_user_link .= '<img src="http://passets-cdn.pinterest.com/images/follow-on-pinterest-button.png" alt="Follow Me on Pinterest" border="0" class="APTFPINbyTAP-image-link"/>';
              $APTFPINbyTAP_user_link .= '</a>';
            } elseif ($linkStyle == 'medium') { 
              $APTFPINbyTAP_user_link .= '<a href="http://pinterest.com/'. $pinterest_uid .'/" target="_blank" title="Follow Me on Pinterest">';
              $APTFPINbyTAP_user_link .= '<img src="http://passets-cdn.pinterest.com/images/pinterest-button.png" alt="Follow Me on Pinterest" border="0" class="APTFPINbyTAP-image-link" />';
              $APTFPINbyTAP_user_link .= '</a>';
            } elseif ($linkStyle == 'small') { 
              $APTFPINbyTAP_user_link .= '<a href="http://pinterest.com/'. $pinterest_uid .'/" target="_blank" title="Follow Me on Pinterest">';
              $APTFPINbyTAP_user_link .= '<img src="http://passets-cdn.pinterest.com/images/big-p-button.png" width="61" height="61" alt="Follow Me on Pinterest" border="0" class="APTFPINbyTAP-image-link" />';
              $APTFPINbyTAP_user_link .= '</a>';
            } elseif ($linkStyle == 'tiny') { 
              $APTFPINbyTAP_user_link .= '<a href="http://pinterest.com/'. $pinterest_uid .'/" target="_blank" title="Follow Me on Pinterest" >';
              $APTFPINbyTAP_user_link .= '<img src="http://passets-cdn.pinterest.com/images/small-p-button.png" width="16" height="16" alt="Follow Me on Pinterest" border="0" class="APTFPINbyTAP-image-link"/>';
              $APTFPINbyTAP_user_link .= '</a>';
            } elseif ($linkStyle == 'text') {
              $APTFPINbyTAP_user_link .= '<div class="APTFPINbyTAP-display-link" >';
              $APTFPINbyTAP_user_link .= '<a href="http://pinterest.com/'.$pinterest_uid.'/" target="_blank" >';
              $APTFPINbyTAP_user_link .= $APTFPINbyTAP_title;
              $APTFPINbyTAP_user_link .= '</a></div>';
            } else {
              $APTFPINbyTAP_user_link .= '<div class="APTFPINbyTAP-display-link" >';
              $APTFPINbyTAP_user_link .= '<a href="http://pinterest.com/'.$pinterest_uid .'/" target="_blank" >';
              $APTFPINbyTAP_user_link .= $APTFPINbyTAP_title;
              $APTFPINbyTAP_user_link .= '</a></div>';
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
     
    $pinterest_uid = apply_filters( APTFPINbyTAP_HOOK, empty($pinterest_options['pinterest_user_id']) ? '' : $pinterest_options['pinterest_user_id'], $pinterest_options );
    switch ($pinterest_options['pinterest_source']) {
      case 'user':
        $request = 'http://pinterest.com/'.$pinterest_uid.'/feed.rss';
      break;
      case 'board':
        $pinterest_board = apply_filters( APTFPINbyTAP_HOOK, empty($pinterest_options['pinterest_user_board']) ? '' : $pinterest_options['pinterest_user_board'], $pinterest_options );
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
      $APTFPINbyTAP_title = @APTFPINbyTAP_specialarraysearch($rss,'title');
      $APTFPINbyTAP_title = $APTFPINbyTAP_title['0']['data'];
      $APTFPINbyTAP_link = @APTFPINbyTAP_specialarraysearch($rss,'link');
      $APTFPINbyTAP_link = $APTFPINbyTAP_link['0']['data'];
      $rss_data = @APTFPINbyTAP_specialarraysearch($rss,'item');

      $s = 0; // simple counter
      if ($rss_data != NULL ){ // Check again
        foreach ( $rss_data as $item ) {
          if( $s<$pinterest_options['pinterest_photo_number'] ){
            $APTFPINbyTAP_linkurl[$s] = $item['child']['']['link']['0']['data'];    
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
                $APTFPINbyTAP_photourl_current = @preg_replace(array('/(.+)src="/i','/"(.+)/') , '',$matches[ 0 ]);
                // Finally, change the size. 
                  // [] specifies single character and \w is any word character
                $APTFPINbyTAP_photourl[$s] = @preg_replace('/[_]\w[.]/', $APTFPINbyTAP_size_id, $APTFPINbyTAP_photourl_current );
                $APTFPINbyTAP_originalurl[$s] = @preg_replace('/[_]\w[.]/', '.', $APTFPINbyTAP_photourl_current );
                // Could set the caption as blank instead of default "Photo", but currently not doing so.
                $APTFPINbyTAP_photocap[$s] = $item['child']['']['title']['0']['data'];
                $s++;
              }
            }
          }
          else{
            break;
          }
        }
      }
      if(!empty($APTFPINbyTAP_linkurl) && !empty($APTFPINbyTAP_photourl)){
          if( $pinterest_options['pinterest_display_link'] ) {
            $linkStyle = $pinterest_options['pinterest_display_link_style'];
            if ($linkStyle == 'large') { 
              $APTFPINbyTAP_user_link .= '<a href="http://pinterest.com/'. $pinterest_uid .'/" target="_blank" title="Follow Me on Pinterest">';
              $APTFPINbyTAP_user_link .= '<img src="http://passets-cdn.pinterest.com/images/follow-on-pinterest-button.png" alt="Follow Me on Pinterest" border="0" class="APTFPINbyTAP-image-link"/>';
              $APTFPINbyTAP_user_link .= '</a>';
            } elseif ($linkStyle == 'medium') { 
              $APTFPINbyTAP_user_link .= '<a href="http://pinterest.com/'. $pinterest_uid .'/" target="_blank" title="Follow Me on Pinterest">';
              $APTFPINbyTAP_user_link .= '<img src="http://passets-cdn.pinterest.com/images/pinterest-button.png" alt="Follow Me on Pinterest" border="0" class="APTFPINbyTAP-image-link" />';
              $APTFPINbyTAP_user_link .= '</a>';
            } elseif ($linkStyle == 'small') { 
              $APTFPINbyTAP_user_link .= '<a href="http://pinterest.com/'. $pinterest_uid .'/" target="_blank" title="Follow Me on Pinterest">';
              $APTFPINbyTAP_user_link .= '<img src="http://passets-cdn.pinterest.com/images/big-p-button.png" width="61" height="61" alt="Follow Me on Pinterest" border="0" class="APTFPINbyTAP-image-link" />';
              $APTFPINbyTAP_user_link .= '</a>';
            } elseif ($linkStyle == 'tiny') { 
              $APTFPINbyTAP_user_link .= '<a href="http://pinterest.com/'. $pinterest_uid .'/" target="_blank" title="Follow Me on Pinterest" >';
              $APTFPINbyTAP_user_link .= '<img src="http://passets-cdn.pinterest.com/images/small-p-button.png" width="16" height="16" alt="Follow Me on Pinterest" border="0" class="APTFPINbyTAP-image-link"/>';
              $APTFPINbyTAP_user_link .= '</a>';
            } elseif ($linkStyle == 'text') {
              $APTFPINbyTAP_user_link .= '<div class="APTFPINbyTAP-display-link" >';
              $APTFPINbyTAP_user_link .= '<a href="http://pinterest.com/'.$pinterest_uid.'/" target="_blank" >';
              $APTFPINbyTAP_user_link .= $APTFPINbyTAP_title;
              $APTFPINbyTAP_user_link .= '</a></div>';
            } else {
              $APTFPINbyTAP_user_link .= '<div class="APTFPINbyTAP-display-link" >';
              $APTFPINbyTAP_user_link .= '<a href="http://pinterest.com/'.$pinterest_uid .'/" target="_blank" >';
              $APTFPINbyTAP_user_link .= $APTFPINbyTAP_title;
              $APTFPINbyTAP_user_link .= '</a></div>';
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
    
  $results = array('continue'=>$continue,'message'=>$message,'hidden'=>$hidden,'user_link'=>$APTFPINbyTAP_user_link,'image_captions'=>$APTFPINbyTAP_photocap,'image_urls'=>$APTFPINbyTAP_photourl,'image_perms'=>$APTFPINbyTAP_linkurl,'image_originals'=>$APTFPINbyTAP_originalurl);
  
  if( true == $continue  && !$disablecache && $cache ){     
    $cache_results = $results;
    if(!is_serialized( $cache_results  )) { $cache_results  = maybe_serialize( $cache_results ); }
    
    $cache->put($key, $cache_results);
    $cachetime = APTFPINbyTAP_get_option( 'cache_time' );
    if( $cachetime && is_numeric($cachetime) ){
      $cache->setExpiryInterval( $cachetime*60*60 );
    }
  }
  
  return $results;
}
?>