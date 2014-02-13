<?php

class controller extends component {

 public $cacheConfig = 'memcached';

 public $_oCache;
 
 public $_model = '';

 public $_deleteAction = array('_delete');
 

 public $_createAction = array('_create');

 public $_editAction = array('_create','_update');

 public $_readAction = array('detail','all','find');

 public $_searchAction = array('find');
 
 public $aResult =array('success'=>true);

 public $bReadCache = true;


 public $_dependencie = array( 
   '_delete' => array('detail','find','all'),
   '_create'  => array('detail','find','all'),
   '_update'  => array('detail','find','all'),
   '_links' => array('search','links')
   );
 
 function _create($oParam){

  $result = $this->application->model($this->_model)->create($oParam);
  
  if($result){
   return $result; 
 }
 
 return null;

}

function _update($oParam){


 $result = $this->application->model($this->_model)->update($oParam);
 
 console( '$result' , $result );
 if($result !== null){
   return $result; 
 }
 
return null;

}


function _delete($oParam){

  if($this->application->model($this->_model)->delete($oParam)){
    return true; 
  }
  
  return null;
  
}

function detail($oParam){

 console('oParam', $oParam);

 $result = $this->application->model($this->_model)->get($oParam);
 

 console('detail', $result);
 if($result){
  return $result;       
 }

 return null;

}

function _links($oParam){

 $result = $this->application->model($this->_model)->links($oParam);
 
 return $result;
}

function links($oParam){

 $result = $this->application->model($this->_model)->getLinks($oParam);
 
 return $result;
}


function search($aParam){

  $result['items'] = $this->application->model($this->_model)->search($aParam);
  $result['total'] = count( $result['items']);
  
  return $result;
  
}


function find($aParam){

  $result['items'] = $this->application->model($this->_model)->find($aParam);
  $result['total'] = count( $result['items']);
  
  return $result;
  
}

function all($aParam){

  $result['items'] = $this->application->model($this->_model)->all($aParam); 
  $result['total'] = count( $result['items']);
  return $result;
  
}

public function _init(){
 if($this->cacheConfig){
  $this->_oCache = $this->application->cache($this->cacheConfig);
}
}

public function _write($aRoute,$aParam, $aConfig) {

  $aResult['response'] = $this -> $aRoute['actionName']( $aParam);
  $aResult['success'] = true;
  
  $input = $this->core->component('request');    
  
  $aResultData = &$aResult['response'];

  if(isset($this->_dependencie[$aRoute['actionName']])){

    foreach($this->_dependencie[$aRoute['actionName']] as $aDependencie){

      $aRoute = array(
        'controllerName' => $aRoute['controllerName'],
        'actionName' => $aDependencie
        );
      

      $aConfig = $this -> application->config -> controller($aRoute['controllerName'])->$aRoute['actionName']();

      $aKey = array();
      
      foreach($aConfig as $keyParam => $keyConditionParam ){

        if(isset($keyConditionParam['key'])){
          if($keyConditionParam['key'] == true){
           $aKey[]= $keyParam;
         }
       }


     }

     

     $this->_oCache ->removeDependencie($aKey,$aRoute,$aParam,$aResultData);

   }
 }
 
 
 return $aResult;
}



public function _read($aRoute, $aParam, $aConfig) {


  if($this->application->env !='package' && $this->bReadCache == true ){

    $aResult = $this->_oCache-> getData($aRoute, $aParam, $aConfig);
    
    if ($aResult['success'] == true) {
      return $aResult;   
    }
    
  }

  $aResult['response'] = $this -> $aRoute['actionName']( $aParam);
  $aResult['success'] = true;

  
  list($sfilePath,$sCacheName) = $this->_oCache -> setData($aRoute, $aConfig, $aParam, $aResult);
  
  
  $aResultData = &$aResult['response'];
  
  
  
  $aKey=array();
  
  foreach($aConfig as $keyParam => $keyConditionParam ){

   if(isset($keyConditionParam['key'])){
    if($keyConditionParam['key'] == true){
     $aKey[]= $keyParam;
   }
 }
 
}

if( !$this->_oCache ->setDependencie($aKey,$aRoute,$aParam,$aResultData,$sCacheName,$sfilePath)){

 throw new Exception("Error setting cache dependencie");
 
}




return $aResult;

}


public function _exec($sAction,$aParam){

  $this->aResult['response'] = $this -> $sAction( $aParam);               
  return $this->aResult;
  
}

public function get($aRoute,$aParam,$aConfig) {

  $aResult = array();
  if ( $aRoute['actionName'][0] == '_' ) {

   if($aRoute['actionName'][1] == '_'){


    $this->__beforeExec($aRoute,$aParam,$aConfig);

    $aResult = $this ->_exec($aRoute['actionName'],$aParam);

    $this->__afterExec($aResult['response'],$aRoute,$aParam,$aConfig);


  }else{

    $this->__beforeWrite($aRoute,$aParam,$aConfig);

    $aResult =$this -> _write($aRoute, $aParam, $aConfig);

    $this->__afterWrite($aResult['response'],$aRoute,$aParam,$aConfig);

  }


} else {

  $this->__beforeRead($aRoute,$aParam,$aConfig);

  $aResult = $this -> _read($aRoute, $aParam, $aConfig);

  $this->__afterRead($aResult['response'],$aRoute,$aParam,$aConfig);


}


return $aResult;

}

public function  __beforeExec($aRoute,$aParam,$aConfig){

}

public function  __afterExec($aResult,$aRoute,$aParam,$aConfig){

}

public function  __beforeRead($aRoute,$aParam,$aConfig){

}

public function  __afterRead($aResult,$aRoute,$aParam,$aConfig){

}

public function  __beforeWrite($aRoute,$aParam,$aConfig){

}

public function  __afterWrite($aResult,$aRoute,$aParam,$aConfig){

}

}
