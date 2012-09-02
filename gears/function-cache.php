<?php
/**
 * Alpine PhotoTile for Flickr: Cache Function
 * The PHP for simple caching functions
 *
 * @since 1.0.1
 *
 * Cache Class Version 2
 */
 
if ( !class_exists( 'theAlpinePressSimpleCacheV2' ) ) {
  class theAlpinePressSimpleCacheV2  {  

    private $cacheDir = 'wp-content/cache/the-alpine-press-cache'; 
    private $expiryInterval = 360; //1*60*60;  1 hour
    private $cleaningInterval = 1209600; //14*24*60*60;  2 weeks
    
    public function setCacheDir($val) {  $this->cacheDir = $val; }  
    public function setExpiryInterval($val) {  $this->expiryInterval = $val; }  
    public function getExpiryInterval($val) {  return (int)$this->expiryInterval; }  
    
    public function exists($key) {  
        $filename_cache = $this->cacheDir . '/' . $key . '.cache'; //Cache filename  
        $filename_info = $this->cacheDir . '/' . $key . '.info'; //Cache info  
      
        if (file_exists($filename_cache) && file_exists($filename_info)) {  
            $cache_time = file_get_contents ($filename_info) + (int)$this->expiryInterval; //Last update time of the cache file  
            $time = time(); //Current Time  
      
            $expiry_time = (int)$time; //Expiry time for the cache  
      
            if ((int)$cache_time >= (int)$expiry_time) {//Compare last updated and current time  
                return true;  
            }  
        }
        return false;  
    } 

    public function get($key)  {  
        $filename_cache = $this->cacheDir . '/' . $key . '.cache'; //Cache filename  
        $filename_info = $this->cacheDir . '/' . $key . '.info'; //Cache info  
      
        if (file_exists($filename_cache) && file_exists($filename_info))  {  
            $cache_time = file_get_contents ($filename_info) + (int)$this->expiryInterval; //Last update time of the cache file  
            $time = time(); //Current Time  
      
            $expiry_time = (int)$time; //Expiry time for the cache  
      
            if ((int)$cache_time >= (int)$expiry_time){ //Compare last updated and current time 
                return file_get_contents ($filename_cache);   //Get contents from file  
            }  
        }
        return null;  
    }  

    public function put($key, $content) {  
        $time = time(); //Current Time  
        
        if ( ! file_exists($this->cacheDir) ){  
            @mkdir($this->cacheDir);  
            
            $cleaning_info = $this->cacheDir . '/cleaning.info'; //Cache info 
            @file_put_contents ($cleaning_info , $time); // save the time of last cache update  
        }
        
        if ( file_exists($this->cacheDir) && is_dir($this->cacheDir) ){ 
            $dir = $this->cacheDir . '/';
            $filename_cache = $dir . $key . '.cache'; //Cache filename  
            $filename_info = $dir . $key . '.info'; //Cache info  
          
            @file_put_contents ($filename_cache ,  $content); // save the content  
            @file_put_contents ($filename_info , $time); // save the time of last cache update  
        }
    }
    
    public function clearAll() {
      $dir = $this->cacheDir . '/';

      if(is_dir($dir)){
        $opendir = @opendir($dir);
        while(false !== ($file = readdir($opendir))) {
            if($file != "." && $file != "..") {
                if(file_exists($dir.$file)) {
                    $file_array = @explode('.',$file);
                    $file_type = @array_pop( $file_array );
                    // only remove cache or info files
                    if( 'cache' == $file_type || 'info' == $file_type){
                        @chmod($dir.$file, 0777);
                        @unlink($dir.$file);
                    }
                }
                /*elseif(is_dir($dir.$file)) {
                    @chmod($dir.$file, 0777);
                    @chdir('.');
                    @destroy($dir.$file.'/');
                    @rmdir($dir.$file);
                }*/

            }
        }
        @closedir($opendir);
      }
    }
    
    
    public function clean() {
       
        $cleaning_info = $this->cacheDir . '/cleaning.info'; //Cache info 
              
        if (file_exists($cleaning_info))  {  
            $cache_time = file_get_contents ($cleaning_info) + (int)$this->cleaningInterval; //Last update time of the cache cleaning  
            $time = time(); //Current Time  
      
            $expiry_time = (int)$time; //Expiry time for the cache  
      
            if ((int)$cache_time < (int)$expiry_time){ //Compare last updated and current time     
                // Clean old files
                $dir = $this->cacheDir . '/';
                if(is_dir($dir)){
                    $opendir = @opendir($dir);
                    while(false !== ($file = readdir($opendir))) {                            
                        if($file != "." && $file != "..") {
                            if(is_dir($dir.$file)) {
                                //@chmod($dir.$file, 0777);
                                //@chdir('.');
                                //@destroy($dir.$file.'/');
                                //@rmdir($dir.$file);
                            }
                            elseif(file_exists($dir.$file)) {
                                $file_array = @explode('.',$file);
                                $file_type = @array_pop( $file_array );
                                $file_key = @implode( $file_array );
                                if( $file_type && $file_key && 'info' == $file_type){
                                    $filename_cache = $dir . $file_key . '.cache'; //Cache filename  
                                    $filename_info = $dir . $file_key . '.info'; //Cache info  
                                  
                                    if (file_exists($filename_cache) && file_exists($filename_info)) {  
                                        $cache_time = file_get_contents ($filename_info) + (int)$this->cleaningInterval; //Last update time of the cache file  
                                  
                                        $expiry_time = (int)$time; //Expiry time for the cache  
                                  
                                        if ((int)$cache_time < (int)$expiry_time) {//Compare last updated and current time  
                                            @chmod($filename_cache, 0777);
                                            @unlink($filename_cache);
                                            @chmod($filename_info, 0777);
                                            @unlink($filename_info);
                                        }  
                                    }
                                }
                            }
                        }
                    }
                    @closedir($opendir);
                }
                @file_put_contents ($cleaning_info , $time); // save the time of last cache cleaning        
            }
        }
    } 
  }  
}

?>