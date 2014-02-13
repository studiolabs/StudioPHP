<?php

class website extends application {

  public $format, $aRoute, $result,$response;

  public $sRootFolder = 'home';

  public $sDefaultPage = '_default';

  var $protocole = 'http://';



  public function getWebserviceRoute($uri) {
    $nocache = false;

    $url = explode('/', $uri);



    if($url[1] == "nocache"){

      array_splice($url, 1, 1);
      $nocache = true;

    }

    if(count($url) == 2 ){

      $controllerName = 'page';

      $actionName = $url[1];

    }else{

      $controllerName = $url[1];

      $actionName = $url[2];

    }


    return $this -> route($controllerName)-> get( array('controllerName' => $controllerName, 'actionName' => $actionName,'nocache'=>$nocache));


  }

  public function _init(&$pParent = null) {
    parent::_init($pParent);

    $this -> response = $this -> core -> component('response');

  }

  public function init() {

    try {


      if($_SERVER['REQUEST_URI'] == '/' || $_SERVER['REQUEST_URI'] == ''){
        $this -> format = 'html';
        $aRoute =  array('controllerName' => 'root', 'actionName' =>   $this->domain,'nocache'=>false);
      }else{

        list($url, $this -> format) = explode('.', $_SERVER['REQUEST_URI']);
        
        $pos = strpos($this -> format, '?');

        if ($pos > 0) {
          $this -> format = substr($this -> format, 0, $pos);
        }

        $aRoute = $this -> getWebserviceRoute($url);
      }


      $this->route =$aRoute;

      

      if($this->core->debug == true){

        $this->core->mark($aRoute['controllerName']."/".$aRoute['actionName']);
      }

      $aConfig = $this -> config -> controller($aRoute['controllerName'])->$aRoute['actionName']();
      
      
      $aParam = $this -> response -> _input -> getParam($aConfig,$_REQUEST);


      $this->cleanParam($aParam);

      $this -> result =  $this -> _action($aRoute, $aParam, $aConfig);

      $this -> show($this -> format, $this -> result, $aParam, $aRoute);

    } catch (Exception $e) {

     $this->core->exception($e);
     $this -> show('json', array('success' => false, 
       'response' =>array( 'errors' => $e -> getMessage())), $aParam,
     $aRoute, $e -> getCode());
     die();


   }

 }

 public function cleanParam(&$aParam){


 }


 public function call($sUrl,$aParam =array()) {

   if($this->core->debug == true){

    $this->core->mark($aRoute['controllerName']."/".$aRoute['actionName']);
  }



  try {


    list($url, $format) = explode('.', $sUrl);

    $pos = strpos($format, '?');

    if ($pos > 0) {
      $format = substr($format, 0, $pos);
    }

    $aRoute = $this -> getWebserviceRoute($url);

    

    $aConfig = $this -> config -> controller($aRoute['controllerName'])->$aRoute['actionName']();

    $aParam = $this -> response -> _input -> getFakeParam($aConfig,$aParam);

    return $this -> _action($aRoute, $aParam, $aConfig);


  } catch (Exception $e) {

    $this->core->error($e);

    return $this -> show('json', array('success' => false, 
     'response' =>array( 'errors' => $e -> getMessage())), $aParam,
    $this -> route, $e -> getCode());
  }

}


public function show($format, $result, $aParam, $aRoute = array(), $code = 200) {




  if( !is_array($result['response']) && !is_object($result['response'])){
    $result['response'] = array('result'=>$result['response']); 
  }


  if($result['success'] == true  ){
    $result['response']['success'] = true;
  }else{
    $result['response']['success'] = false;
    header("HTTP/1.0 404");
  }


  if($this->core->debug == true){
    ob_start(); 
  }

  switch($format) {

    case  'jpg' :
    $show = $this -> response -> showJPG($result['response']);

    break;
    case  'zip' :				 
    $show = $this -> response -> download($result['response']);
    break;

    case 'upload' :
    {
      $show = $this -> response -> showUploadJson($result['response']);
    }
    break;
    case 'redirect' :
    {
      $show = $this -> response -> redirect($result['response']);
    }
    break;
    case 'json' :
    {
      $show = $this -> response -> showJSON($result['response']);
    }
    break;

               // put security by domains
    case 'compile' :
    {
      $show = $this -> response -> showCompile($result['response'],$aParam, $aRoute);
    }
    break;
    case 'package' :
    {
      $show = $this -> response -> showPackage($result['response'],$aParam, $aRoute);
    }
    break; 
    case 'mail' :
    {
      $show = $this -> response -> sendMailHTML($result['response'], $aParam,$aRoute);
    }
    break;
    case 'smail' :
    {
      $show = $this -> response -> sendMail($result['response'], $aParam,$aRoute);
    }
    break;
    case 'mhtml' :
    {
      $show = $this -> response -> sendMailTemplate($result['response'],$aParam, $aRoute);
    }
    break;
    case 'js' :
    case 'html' :
    default :
    {
      $show = $this -> response -> showHTML($result['response'],$aParam, $aRoute);
    }
    break;
  }



  if($this->core->debug == true){

   ob_end_flush();

   $content = ob_get_contents();

   ob_end_clean();

   echo $content;

 }


}

public function get($sRoute, $aParam) {


  if($this->core->debug == true){

    $this->core->mark($sRoute);
  }

  $aRoute = array();

  $aRoute = $this->getWebserviceRoute('/'.$sRoute);


  $aConfig = $this -> config -> controller($aRoute['controllerName'])->$aRoute['actionName']();

  $aParam = $this -> response -> _input -> extractParam($aConfig,$aParam);

  $result = $this -> _action($aRoute, $aParam, $aConfig);

  if($result['response']){

    return $result['response'];
  }

  return null;

}

private function _action($aRoute, $aParam, $aConfig) {


  if($this->core->debug == true){

    $sMark = $aRoute['controllerName'].'/'.$aRoute['actionName'];

    $this->core->log($sMark.' - $aParam', $aParam);
    $this->core->log($sMark.' - $aConfig', $aConfig);

  }

  $oController = $this -> controller($aRoute['controllerName']);

  if ($aRoute['nocache'] == true){

    $oController->bReadCache = false;

  }

  $aResult =  $oController -> get($aRoute, $aParam, $aConfig);

  if($this->core->debug == true){

   
    $this->core->unmark($sMark);
    $this->core->log($sMark.' - $aResult', $aResult['response']);

  }


  return $aResult; 

}


}
?>
