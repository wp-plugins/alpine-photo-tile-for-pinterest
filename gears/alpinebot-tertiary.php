<?php
/**
 * AlpineBot Tertiary
 * 
 * Feed fetching and additional back-end functions (mostly related to admin pages)
 * Contains ONLY unique functions
 * 
 */

class PhotoTileForPinterestTertiary extends PhotoTileForPinterestSecondary{  

//////////////////////////////////////////////////////////////////////////////////////
//////////////////        Unique Feed Fetch Functions        /////////////////////////
//////////////////////////////////////////////////////////////////////////////////////    

/**
 * Alpine PhotoTile for Pinterest: Photo Retrieval Function
 * The PHP for retrieving content from Pinterest.
 *
 * @ Since 1.0.0
 * @ Updated 1.2.3.1
 */
  function photo_retrieval(){
    $pinterest_options = $this->options;
    $defaults = $this->option_defaults();

    $key_input = array(
      'name' => 'pinterest',
      'info' => array(
        'vers' => $this->vers,
        'src' => $pinterest_options['pinterest_source'],
        'uid' => $pinterest_options['pinterest_user_id'],
        'brd' => $pinterest_options['pinterest_user_board'],
        'num' => $pinterest_options['pinterest_photo_number'],
        'link' => $pinterest_options['pinterest_display_link'],
        'text' => $pinterest_options['pinterest_display_link_text'],
        'style' => $pinterest_options['pinterest_display_link_style'],
        'size' => $pinterest_options['pinterest_photo_size'],
        )
      );
    $key = $this->key_maker( $key_input );
    if( $this->retrieve_from_cache( $key ) ){  return; } // Check Cache
        
    // Determine image size id
    switch ($pinterest_options['pinterest_photo_size']) {
      case 75:
        $size_id = '/75/';
      break;
      case 192:
        $size_id = '/192/';
      break;
      case 554:
        $size_id = '/550/';
      break;
      case 600:
        $size_id = '/600/';
      break;
      case 930:
        $size_id = '/600/';
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
        $this->hidden .= '<!-- Failed using simplexml_load_file() and XML @ '.$request.' -->';
        $this->success = false;
      }else{
        $title = $_pinterest_xml->channel->title;
        $link = $_pinterest_xml->channel->link;
        
        if(!$_pinterest_xml && !$_pinterest_xml->channel){
          $this->hidden .= '<!-- No photos found using simplexml_load_file() and XML @ '.$request.' -->';
          $this->success = false;
        }else{
          foreach( $_pinterest_xml->channel->item as $p ) { // This will prevent empty images from being added to linkurl.
            if( count($this->photos)<$pinterest_options['pinterest_photo_number'] ){
              // list of link urls
              $url = (string) $p->link; // ->i is equivalent of ['i'] for objects
              if( $url ){
                $the_photo = array();
                $the_photo['image_link'] = $url;
                $the_photo['image_title'] = (string) $p->title;
                $the_photo['image_caption'] = "";

                $content = (string) $p->description;
                // For Reference: regex references and http://php.net/manual/en/function.preg-match.php
                // Using the RSS feed will require some manipulation to get the image url from pinterest;
                // preg_replace is bad at skipping lines so we'll start with preg_match
                  // i sets letters in upper or lower case,
                @preg_match( "/img(.*?)>/i", $content , $matches ); // First, get image from feed.
                // Next, strip away everything surrounding the source url.
                  // . means any expression, and + means repeat previous
                $photourl_current = @preg_replace(array('/(.*)src="/i','/"(.*)/') , '',$matches[ 0 ]);
                
                $the_photo['image_source'] = @str_replace('/192/', $size_id, $photourl_current);
                $the_photo['image_original'] = @str_replace('/192/','/600/', $photourl_current);
                $this->photos[] = $the_photo;
                // Finally, change the size. [] specifies single character and \w is any word character
                //$the_photo['image_source'] = @preg_replace('/[_]\w[.]/', $size_id, $photourl_current );
                //$originalurl[$s] = @preg_replace('/[_]\w[.]/', '.', $photourl_current );
                
              }
            }
            else{
              break;
            }
          }
          if( !empty($this->photos) ){
            if( $pinterest_options['pinterest_display_link'] ) {
              $linkStyle = $pinterest_options['pinterest_display_link_style'];
              if ($linkStyle == 'large') { 
                $this->userlink .= '<div class="AlpinePhotoTiles-display-link" ><a href="http://pinterest.com/'. $pinterest_uid .'/" target="_blank" title="Follow Me on Pinterest">';
                $this->userlink .= '<img src="http://passets-cdn.pinterest.com/images/follow-on-pinterest-button.png" alt="Follow Me on Pinterest" border="0" class="AlpinePhotoTiles-image-link"/>';
                $this->userlink .= '</a></div>';
              } elseif ($linkStyle == 'medium') { 
                $this->userlink .= '<div class="AlpinePhotoTiles-display-link" ><a href="http://pinterest.com/'. $pinterest_uid .'/" target="_blank" title="Follow Me on Pinterest">';
                $this->userlink .= '<img src="http://passets-cdn.pinterest.com/images/pinterest-button.png" alt="Follow Me on Pinterest" border="0" class="AlpinePhotoTiles-image-link" />';
                $this->userlink .= '</a></div>';
              } elseif ($linkStyle == 'small') { 
                $this->userlink .= '<div class="AlpinePhotoTiles-display-link" ><a href="http://pinterest.com/'. $pinterest_uid .'/" target="_blank" title="Follow Me on Pinterest">';
                $this->userlink .= '<img src="http://passets-cdn.pinterest.com/images/big-p-button.png" width="61" height="61" alt="Follow Me on Pinterest" border="0" class="AlpinePhotoTiles-image-link" />';
                $this->userlink .= '</a></div>';
              } elseif ($linkStyle == 'tiny') { 
                $this->userlink .= '<div class="AlpinePhotoTiles-display-link" ><a href="http://pinterest.com/'. $pinterest_uid .'/" target="_blank" title="Follow Me on Pinterest" >';
                $this->userlink .= '<img src="http://passets-cdn.pinterest.com/images/small-p-button.png" width="16" height="16" alt="Follow Me on Pinterest" border="0" class="AlpinePhotoTiles-image-link"/>';
                $this->userlink .= '</a></div>';
              } elseif ($linkStyle == 'text' && $pinterest_options['pinterest_display_link_text']) {
                $this->userlink .= '<div class="AlpinePhotoTiles-display-link" >';
                $this->userlink .= '<a href="http://pinterest.com/'.$pinterest_uid.'/" target="_blank" >';
                $this->userlink .= $pinterest_options['pinterest_display_link_text'];
                $this->userlink .= '</a></div>';
              } else {
                $this->userlink .= '<div class="AlpinePhotoTiles-display-link" >';
                $this->userlink .= '<a href="http://pinterest.com/'.$pinterest_uid .'/" target="_blank" >';
                $this->userlink .= "Follow Me on Pinterest";
                $this->userlink .= '</a></div>';
              }
            }
            // If content successfully fetched, generate output...
            $this->success = true;
            $this->hidden .= '<!-- Success using simplexml_load_file() and XML -->';
          }else{
            $this->hidden .= '<!-- No photos found using simplexml_load_file() and XML @ '.$request.' -->';
            $this->success = false;
            $this->feed_found = true;
          }
        }
      }
    }
    
    ////////////////////////////////////////////////////////
    ////      If still nothing found, try using RSS      ///
    ////////////////////////////////////////////////////////
    if( $this->success == false ) {
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

        if ($rss_data != NULL ){ // Check again
          foreach ( $rss_data as $item ) {
            if( count($this->photos)<$pinterest_options['pinterest_photo_number'] ){
              $content = $item['child']['']['description']['0']['data'];     
              if($content){
                $the_photo = array();
                $the_photo['image_link'] = $item['child']['']['link']['0']['data'];
                $the_photo['image_title'] = $item['child']['']['title']['0']['data'];
                $the_photo['image_caption'] = "";
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

                  $the_photo['image_source'] = @str_replace('/192/', $size_id, $photourl_current);
                  $the_photo['image_original'] = @str_replace('/192/','/600/', $photourl_current);
                  $this->photos[] = $the_photo;
                }
              }
            }
            else{
              break;
            }
          }
        }
        if( !empty($this->photos) ){
          if( $pinterest_options['pinterest_display_link'] ) {
            $linkStyle = $pinterest_options['pinterest_display_link_style'];
            if ($linkStyle == 'large') { 
              $this->userlink .= '<div class="AlpinePhotoTiles-display-link" ><a href="http://pinterest.com/'. $pinterest_uid .'/" target="_blank" title="Follow Me on Pinterest">';
              $this->userlink .= '<img src="http://passets-cdn.pinterest.com/images/follow-on-pinterest-button.png" alt="Follow Me on Pinterest" border="0" class="AlpinePhotoTiles-image-link"/>';
              $this->userlink .= '</a></div>';
            } elseif ($linkStyle == 'medium') { 
              $this->userlink .= '<div class="AlpinePhotoTiles-display-link" ><a href="http://pinterest.com/'. $pinterest_uid .'/" target="_blank" title="Follow Me on Pinterest">';
              $this->userlink .= '<img src="http://passets-cdn.pinterest.com/images/pinterest-button.png" alt="Follow Me on Pinterest" border="0" class="AlpinePhotoTiles-image-link" />';
              $this->userlink .= '</a></div>';
            } elseif ($linkStyle == 'small') { 
              $this->userlink .= '<div class="AlpinePhotoTiles-display-link" ><a href="http://pinterest.com/'. $pinterest_uid .'/" target="_blank" title="Follow Me on Pinterest">';
              $this->userlink .= '<img src="http://passets-cdn.pinterest.com/images/big-p-button.png" width="61" height="61" alt="Follow Me on Pinterest" border="0" class="AlpinePhotoTiles-image-link" />';
              $this->userlink .= '</a></div>';
            } elseif ($linkStyle == 'tiny') { 
              $this->userlink .= '<div class="AlpinePhotoTiles-display-link" ><a href="http://pinterest.com/'. $pinterest_uid .'/" target="_blank" title="Follow Me on Pinterest" >';
              $this->userlink .= '<img src="http://passets-cdn.pinterest.com/images/small-p-button.png" width="16" height="16" alt="Follow Me on Pinterest" border="0" class="AlpinePhotoTiles-image-link"/>';
              $this->userlink .= '</a></div>';
            } elseif ($linkStyle == 'text' && $pinterest_options['pinterest_display_link_text']) {
              $this->userlink .= '<div class="AlpinePhotoTiles-display-link" >';
              $this->userlink .= '<a href="http://pinterest.com/'.$pinterest_uid.'/" target="_blank" >';
              $this->userlink .= $pinterest_options['pinterest_display_link_text'];
              $this->userlink .= '</a></div>';
            } else {
              $this->userlink .= '<div class="AlpinePhotoTiles-display-link" >';
              $this->userlink .= '<a href="http://pinterest.com/'.$pinterest_uid .'/" target="_blank" >';
              $this->userlink .= "Follow Me on Pinterest";
              $this->userlink .= '</a></div>';
            }
          }
          // If content successfully fetched, generate output...
          $this->success = true;
          $this->hidden .= '<!-- Success using fetch_feed() and RSS -->';
        }else{
          $this->hidden .= '<!-- No photos found using fetch_feed() and RSS @ '.$request.' -->';  
          $this->success = false;
          $this->feed_found = true;
        }
      }
      else{
        $this->hidden .= '<!-- Failed using fetch_feed() and RSS @ '.$request.' -->';
        $this->success = false;
      }      
    }
      
    ///////////////////////////////////////////////////////////////////////
    //// If STILL!!! nothing found, report that Pinterest ID must be wrong ///
    ///////////////////////////////////////////////////////////////////////
    if( false == $continue ) {
      if($this->feed_found ){
        $this->message .= '- Pinterest feed was successfully retrieved, but no photos found.';
      }else{
        $this->message .= '- Pinterest feed not found. Please recheck your ID.';
      }
    }
      
    $this->results = array('continue'=>$this->success,'message'=>$this->message,'hidden'=>$this->hidden,'photos'=>$this->photos,'user_link'=>$this->userlink);

    $this->store_in_cache( $key );  // Store in cache
  }
  

  
  
//////////////////////////////////////////////////////////////////////////////////////
////////////////////        Unique Admin Functions        ////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////    
  
 /**
   * Alpine PhotoTile: Options Page
   *
   * @since 1.1.1
   *
   */
  function build_settings_page(){
    $optiondetails = $this->option_defaults();
    $currenttab = $this->get_current_tab();
    
    echo '<div class="wrap AlpinePhotoTiles_settings_wrap">';
    $this->admin_options_page_tabs( $currenttab );

      echo '<div class="AlpinePhotoTiles-container '.$this->domain.'">';
      
      if( 'general' == $currenttab ){
        $this->display_general();
      }elseif( 'add' == $currenttab ){
        $this->display_add();
      }elseif( 'preview' == $currenttab ){
        $this->display_preview();
      }else{
        $options = $this->get_all_options();     
        $settings_section = $this->id . '_' . $currenttab . '_tab';
        $submitted = ( ( isset($_POST[ "hidden" ]) && ($_POST[ "hidden" ]=="Y") ) ? true : false );

        if( $submitted ){
          $options = $this->SimpleUpdate( $currenttab, $_POST, $options );
          if( 'generator' == $currenttab ) {
            $short = $this->generate_shortcode( $options, $optiondetails );
          }
        }
        echo '<div class="AlpinePhotoTiles-'.$currenttab.'">';
          if( $_POST[$this->settings.'_'.$currenttab]['submit-'.$currenttab] == 'Delete Current Cache' ){
            $this->clearAllCache();
            echo '<div class="announcement">'.__("Cache Cleared").'</div>';
          }
          elseif( $_POST[$this->settings.'_'.$currenttab]['submit-'.$currenttab] == 'Save Settings' ){
            $this->clearAllCache();
            echo '<div class="announcement">'.__("Settings Saved").'</div>';
          }
          echo '<form action="" method="post">';
            echo '<input type="hidden" name="hidden" value="Y">';
            $this->display_options_form($options,$currenttab,$short);
          echo '</form>';
        echo '</div>';
      }
      echo '</div>'; // Close Container
    echo '</div>'; // Close wrap
  }  
 
}

?>
