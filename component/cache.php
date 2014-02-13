<?php
// ------------------------------------------------------------------------

/**
 *
 *
 'CApcCache' => '/caching/CApcCache.php',
 'CCache' => '/caching/CCache.php',
 'CDbCache' => '/caching/CDbCache.php',
 'CDummyCache' => '/caching/CDummyCache.php',
 'CEAcceleratorCache' => '/caching/CEAcceleratorCache.php',
 'CFileCache' => '/caching/CFileCache.php',
 'CMemCache' => '/caching/CMemCache.php',
 'CWinCache' => '/caching/CWinCache.php',
 'CXCache' => '/caching/CXCache.php',
 'CZendDataCache' => '/caching/CZendDataCache.php',
 'CCacheDependency' => '/caching/dependencies/CCacheDependency.php',
 'CChainedCacheDependency' => '/caching/dependencies/CChainedCacheDependency.php',
 'CDbCacheDependency' => '/caching/dependencies/CDbCacheDependency.php',
 'CDirectoryCacheDependency' => '/caching/dependencies/CDirectoryCacheDependency.php',
 'CExpressionDependency' => '/caching/dependencies/CExpressionDependency.php',
 'CFileCacheDependency' => '/caching/dependencies/CFileCacheDependency.php',
 'CGlobalStateCacheDependency' => '/caching/dependencies/CGlobalStateCacheDependency.php',

 * @package     CodeIgniter
 * @subpackage  Libraries
 * @category    Core
 * @author      ExpressionEngine Dev Team
 * @link
 */
class cache extends component {
    

    public $engine;

    public $debug = false;

    // ------------------------------------------------------------------------
    // ------------------------------------------------------------------------

    /**
     * Get
     *
     * Look for a value in the cache.  If it exists, return the data
     * if not, return FALSE
     *
     * @param   string
     * @return  mixed       value that is stored/FALSE on failure
     */
    public function getData($aRoute, $aParam, $aConfig) {

        if($this->debug){
            //$this->core->log('getData',$aRoute, $aParam, $aConfig);
        }
        
         $sPath = join(DIRECTORY_SEPARATOR, $aRoute);




         
        $sfilePath = "data" . DIRECTORY_SEPARATOR . $sPath . DIRECTORY_SEPARATOR . serialize($aParam);

        return $this  -> get($sfilePath);
       
    }

    /**
     * Get
     *
     * Look for a value in the cache.  If it exists, return the data
     * if not, return FALSE
     *
     * @param   string
     * @return  mixed       value that is stored/FALSE on failure
     */
  

    /**
     * Set
     *
     * Set a value in the cache.  If it's done, return the data
     * if not, return FALSE
     *
     * @param   string
     * @return  mixed       value that is stored/FALSE on failure
     */
    public function setData($aRoute, $aConfig, $aParam, $oData) {

          if($this->debug){
            //$this->core->log('setData',$aRoute, $aConfig, $aParam, $oData);
        }

        if ( !isset($aConfig->validity)) {
            
            $expirationDate = 24 * 60 * 60;
        }else{
            
           $expirationDate = $aConfig->validity * 60;
            
        }
        
        $sPath = join(DIRECTORY_SEPARATOR, $aRoute);

       
        $sCacheName = serialize($aParam);


        $sfilePath = "data" . DIRECTORY_SEPARATOR . $sPath . DIRECTORY_SEPARATOR . $sCacheName;

        if ( !$this -> set($sfilePath, $oData, $expirationDate)){

            throw new Exception("Error setting cache Request");
            
        }else{
            return array($sfilePath,$sCacheName);
        }

    }

    public function getCacheKey($sKey, $aParam, $oResult= array()) {

            if($this->debug){
          //  //$this->core->log('getCacheKey',func_get_args());
        }

         
           if (!empty($aParam[ $sKey])) {
           

                return DIRECTORY_SEPARATOR.$sKey . DIRECTORY_SEPARATOR . serialize($aParam[ $sKey]);
              
            }else  if(!empty($oResult[ $sKey])){


                     
                return DIRECTORY_SEPARATOR . $sKey.DIRECTORY_SEPARATOR . serialize($oResult[ $sKey]);
                    
            }
      
        
        return '';

    }
    
    
    
    public function getCachePath($aDepRoute, $sKey, $aParam, $aResult=array()){

           if($this->debug){
         //   //$this->core->log('getCachePath',func_get_args());
        }
          
            $sCacheBasePath = "index" . DIRECTORY_SEPARATOR . $aDepRoute['controllerName'];
             

             $sCacheKey = $this -> getCacheKey($sKey, $aParam, $aResult);
                
             
                if($sCacheKey == ''){
                    $sCachePath = $sCacheBasePath .DIRECTORY_SEPARATOR . $aDepRoute['actionName'].DIRECTORY_SEPARATOR.serialize(array());
         
                }else{

                    $sCachePath = $sCacheBasePath .$sCacheKey;
         
                }
                
                return  $sCachePath;
    }
    
    
    
    public function setDependencie($aKey,$aDepRoute, $aParam, $aResult, $sCacheName, $sfilePath) {

              if($this->debug){
            //$this->core->log('setDependencie',$aKey,$aDepRoute, $aParam, $aResult, $sCacheName, $sfilePath);
        }
       
           if(count($aKey)==0){
                   
                        $sCachePath = $this->getCachePath($aDepRoute, '', $aParam, $aResult);
                         
                         $result = $this->createIndex($sCachePath, $sCacheName, $sfilePath);
                        if( !$result){
                           
                           return false;
                        }
                   
               }else{
       
                   foreach($aKey as $sKey){
                      
                       $sCachePath = $this->getCachePath($aDepRoute, $sKey, $aParam, $aResult);
                         

                       $result = $this->createIndex($sCachePath, $sCacheName, $sfilePath);
                        if( !$result){
                            
                           return false;
                       }
                       
                    }
           
               }

        return true;

    }
    
    public function removeDependencie($aKey,$aDepRoute, $aParam, $aResult) {

                if($this->debug){
            //$this->core->log('removeDependencie',$aKey,$aDepRoute, $aParam, $aResult);
        }
        
            $sCacheBasePath = "index" . DIRECTORY_SEPARATOR . $aDepRoute['controllerName'];
          
          
      if(count($aKey)==0){
                   
                        $sCachePath = $this->getCachePath($aDepRoute, '', $aParam, $aResult);
                         
                         if (!$this -> removeIndex($sCachePath)) {
                             return false;
                        }
               }else{
                
            foreach($aKey as $sKey){
                               
              $sCachePath = $this->getCachePath($aDepRoute, $sKey, $aParam, $aResult);
                               
                if (!$this -> removeIndex($sCachePath)) {
                     return false;
                }
      
            }
            
               }

        return true;

    }
    

    private function createIndex($sPath, $sCacheName, $sfilePath) {
               if($this->debug){
           //$this->core->log('createIndex',$sPath, $sCacheName, $sfilePath);
        }

         
        $aIndex = (array)$this -> get($sPath);
        
    
        if (isset($aIndex[$sCacheName])) {
            return true;
        } else {
            $aIndex[$sCacheName] = $sfilePath;

            $result = $this -> set($sPath, $aIndex, 0);

            return $result;
        }
    }

    private function removeIndex($sPath) {

                    if($this->debug){
            //$this->core->log('removeIndex',$sPath);
        }
    
        
        $aKey = (array)$this -> get($sPath);
    
        foreach ($aKey as $sIndex) {
            
            if($sIndex){
               $this -> delete($sIndex);
            }

        }

        return true;

    }

    // ------------------------------------------------------------------------

    /**
     * Cache Save
     *
     * @param   string      Unique Key
     * @param   mixed       Data to store
     * @param   int         Length of time (in seconds) to cache the data
     *
     * @return  boolean     true on success/false on failure
     */
    public function set($id, $data, $ttl = 60) {

         if($this->debug){
            //$this->core->log('set',$id, $data);
        }

        
        return $this -> engine -> set(md5($id), $data, $ttl);
    }


      public function get($id) { 

        $result = $this -> engine -> get(md5($id));

                     if($this->debug){
           //$this->core->log('get',$id, $result);
        }
        return  $result;
    }

    // ------------------------------------------------------------------------

    /**
     * Delete from Cache
     *
     * @param   mixed       unique identifier of the item in the cache
     * @return  boolean     true on success/false on failure
     */
    public function delete($id) {

                     if($this->debug){
           //$this->core->log('delete',$id);
        }
        return $this -> engine -> delete(md5($id));
    }

    // ------------------------------------------------------------------------

    /**
     * Clean the cache
     *
     * @return  boolean     false on failure/true on success
     */
    public function clean() {
        return $this -> engine -> clean();
    }

    // ------------------------------------------------------------------------

    /**
     * Cache Info
     *
     * @param   string      user/filehits
     * @return  mixed       array on success, false on failure
     */
    public function cache_info($type = 'user') {
        return $this -> engine -> cache_info($type);
    }

    // ------------------------------------------------------------------------

    /**
     * Get Cache Metadata
     *
     * @param   mixed       key to get cache metadata on
     * @return  mixed       return value from child method
     */
    public function get_metadata($id) {
        return $this -> engine -> get_metadata(md5($id));
    }

    // ------------------------------------------------------------------------
}

// End Class

/* End of file Cache.php */
/* Location: ./system/libraries/Cache/Cache.php */
