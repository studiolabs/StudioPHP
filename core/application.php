<?php


class __application__  {

    public  $path;
    
    
    var $_aCache = array();
    var $_aDb = array();
    var $_aController = array();
    var $_aModel = array();
    var $_aRoute = array();
    var $_aLib = array();
    
    public $_default_cache_engine = "memcached";

    public $contentUrl = "/content";
    
    public $module = false;

    public $packaged = false;

    public function __construct(&$pCore,$aConfig,$sPath)
    {   
     $this->core = $pCore;

     foreach($aConfig as $key =>$value){

        $this->$key=$value;
    }
    
    $this->path = $sPath;

    $this->sClassName = get_class($this);
    
}   

public function _createObject( $className, $aOption){

    return  new $className($this->core,$this, $aOption);

}

public function _init(&$pParent = null){

  if($pParent !=null){
   $this->parent =$pParent;
}

$this -> config = $this -> core->component('config_application'); 
$this -> config->path = $this->path;



}

public function lib($sLib) {

   if (!isset($this -> _aLib[$sLib])) {

       $this -> loadLib($sLib);
       $this -> _aLib[$sLib] =$sLib;

   }

   return new $this -> _aLib[$sLib]();

}

public function loadLib($sName) {

    $filepath = $this -> path . 'lib/' . $sName . '.php';

    return require ($filepath);

}


public function db($sDb) {

    if (!isset($this -> _aDb[$sDb])) {

        if (!isset($this -> _oDb)) {
            $this -> _oDb = $this -> core->component('database');
        }

        $this -> _aDb[$sDb] = $this -> loadDatabase($sDb);
        

    }

    $this -> _oDb -> engine = $this -> _aDb[$sDb];

    return $this -> _oDb -> engine;

}

public function loadDatabase($sDb) {

    $server = $this -> core-> config -> server('database')->$sDb();

    switch($server['engine']) {

        case 'mysql' :
        
        return $this ->core-> loadYiiComponent('CDbConnection', array('connectionString' => 'mysql:host=' . $server['host'] . ';dbname=' . $server['db'], 'username' => $server['user'], 'password' => $server['password'],'enableParamLogging'=>true));

        break;
    }
}

public function cache($sCache) {

    if (!isset($this -> _aCache[$sCache])) {

        if (!isset($this -> _oCache)) {
            $this -> _oCache = $this ->core->component('cache');
        }

        $this -> _aCache[$sCache] = $this -> loadCache($sCache);

    }

    $this -> _oCache -> engine = $this -> _aCache[$sCache];

    return $this -> _oCache;

}

public function & loadCache($sCache) {

    $server = $this ->core->config -> server('cache')->$sCache();

    switch($sCache) {

        case 'memcached' :

        
        $cache = $this -> core->component('cache_memcached', array('servers' => $server));

        return $cache;

             //   return $this -> core->loadYiiComponent('CMemCache', array('useMemcached' => true, 'servers' => $server));

        
        break;

        case 'file' :
        return $this ->core-> loadYiiComponent('CFileCache');

        break;

        case 'apc' :
        return $this ->core-> loadYiiComponent('CApcCache');

        break;

        case 'db' :
        return $this ->core->loadYiiComponent('CDbCache');
        break;
    }
}


function controller($sName = '', $aOption = array()) {


    if (!isset($this -> _aController[$sName])) {

        if (!class_exists('controller')) {
            $this -> core->loadComponent('controller');
        }
        
        
        if ($this -> loadController($sName)) {


            $className = $sName."Controller";
            $this -> _aController[$sName] = $this->_createObject($className, $aOption);
            
            
            
            return $this -> _aController[$sName];
        }
    }

    return $this -> _aController[$sName];

}

public function loadController($sName) {

    $filepath = $this -> path . 'controller/' . $sName . '.php';

    return require ($filepath);

}




function route($sName = '', $aOption = array()) {

    if (!isset($this -> _aRoute[$sName])) {

        if (!class_exists('route')) {
            $this ->core->loadComponent('route');
        }

        $className = $sName."Route";


        if ($this -> loadRoute($sName,$className)) {

            $this -> _aRoute[$sName] = $this->_createObject($className, $aOption);

        }else{

            $className = "route";
            
            $this -> _aRoute[$sName] = $this->_createObject($className, $aOption);

        }
    }


    return $this -> _aRoute[$sName];

}

public function loadRoute($sName,$className) {

    $filepath = $this -> path . 'route/' . $sName . '.php';

    if(file_exists($filepath)){
        return require ($filepath);
    }


    if($this->packaged ==true){
        if(class_exists($className)){
            return true;
        }
    }

    
    return false;

}

function model($sName = '', $aOption = array()) {

    if (!isset($this -> _aModel[$sName])) {

        if (!class_exists('model')) {
            $this ->core->loadComponent('model');
        }

        if ($this -> loadModel($sName)) {
            $className = $sName."Model";
            
            $this -> _aModel[$sName] = $this->_createObject($className, $aOption);
            return $this -> _aModel[$sName];
        }
    }

    return $this -> _aModel[$sName];

}

public function loadModel($sName) {

    $filepath = $this -> path . 'model/' . $sName . '.php';

    return require ($filepath);

}


function link($sName = '', $aOption = array()) {

    if (!isset($this -> _aLink[$sName])) {

        if (!class_exists('model_link')) {
            $this ->core->loadComponent('model_link');
        }

        if ($this -> loadLink($sName)) {
            $className = $sName."Link";
            
            $this -> _aLink[$sName] = $this->_createObject($className, $aOption);
            return $this -> _aLink[$sName];
        }
    }

    return $this -> _aLink[$sName];

}

public function loadLink($sName) {

    $filepath = $this -> path . 'model/link/' . $sName . '.php';

    return require ($filepath);

}





}
