<?php

class response extends component {

	var $_input;
	var $_output;

	public $param;

  public function _init(){
   parent::_init();

   $this -> _input = $this -> core-> component('request');

 }


 function showHTML($result, $aParam =array(),$aRoute) {

  header('Content-type: text/html; charset=utf-8');

  $sTemplatePath = $this -> application -> directory . 'template/' . $aRoute['controllerName'] . '/' . $aRoute['actionName'] . '.php';

  if(file_exists($sTemplatePath)){
   include($sTemplatePath);
   return  true;
 }

 throw new Exception("Template doesn't exist : ".$this -> application -> directory . 'template/' . $aRoute['controllerName'] . '/' . $aRoute['actionName'] . '.php', 404);

}


function showJPG($result) {

  header('Content-type: image/jpg');

  imagejpeg($result);

  imagedestroy($result);

}


function clearPhp($file){

  $cont  =  file_get_contents($file);

  $response ='class response ';

  if(strpos($cont ,$response) === false ){

    $cont =  str_replace ( array('_include') ,'____nclude', $cont);

    $cont =  str_replace ( array('_require') ,'____rquire', $cont);

    $cont =  str_replace ( array('include (','require (','require_once (','include_once (' ),'___fakea(', $cont);
      $cont =  str_replace ( array('include(','require(','require_once(','include_once(' ),'___fakeb(', $cont);

        $cont =  str_replace ( array('include','require','require_once','include_once') ,'//___fake', $cont);            
        $cont =  str_replace ( array('___fakea','___fakeb' ),'___fake', $cont);

        
        $cont =  str_replace ( 'class_alias($this->_sAppClass' ,'//class_alias($this->_sAppClass', $cont);
         $cont =  str_replace ( '$sApplicationDirectory . \'_\');' ,'$sApplicationDirectory . \'_\');continue;', $cont);



         $cont =  str_replace ( array('return//' ),'return ', $cont);
         $cont =  str_replace ( array('(//','( //' ),'( ', $cont);

           $class ="class ".$this->core->_sAppClass." ";
           if(strpos($cont ,$class) !== false){
            $cont .= "
            class_alias('".$this->core->_sAppClass."','application');
            ";
          }

          $extend ='class '.$this->core->aClassAlias[0].' extends application';
          if(strpos($cont ,$extend) !== false){
            $cont .= "
            class_alias('".$this->core->aClassAlias[0]."', '".$this->core->aClassAlias[1]."');
            ";
          }


        }

        $cont =  str_replace ( array('<?php','<?','?>') ,'', $cont);

        return    $cont ;
      }


      function showPackage($result,$aParam, $aRoute) {

        $files=get_included_files();

        echo json_encode(array('files' =>$files));
      }


      function showCompile($result, $aParam, $aRoute) {


        $files=get_included_files();


        if($aParam['controllerName']){
          $aRoute['controllerName']=$aParam['controllerName'];
        }
        
        
        $content  = "<?php

        function ___fake(){
          return true;        
        }

        define('WORKSPACE_PATH', substr(__DIR__,0, strpos(__DIR__,'".$aRoute['controllerName']."')));

        define('ROOT_PATH', WORKSPACE_PATH.'".$aRoute['controllerName']."/');

        ";
        
        

        $start = array_shift($files);  
        

        foreach( $files as $index => $file){

          if(strpos(file_get_contents($file) ,"abstract class") !== false && $index>0){

           $oldfile = $files[$index-1];
           $files[$index-1] = $files[$index];
           $files[$index] = $oldfile;

         }


       }




       foreach( $files as $file){

        $content .= $this->clearPhp($file);

      }

      $content .= '

      $vhost  = explode(\'.\', "'.$_SERVER['SERVER_NAME'].'");

      $aConfig = array(
        \'vhost\' => $_SERVER[\'SERVER_NAME\'],
        \'directory\' =>  ROOT_PATH,
        \'content\' =>  WORKSPACE_PATH.\'content/\',
        \'protocol\' => \'http://\',
        \'type\' => \'website\',
        \'name\' => $vhost[1],
        \'env\' =>$vhost[2],
        \'domain\' => $vhost[0] ,
        \'packaged\' => true 
        );


$oCore = new core();
$oApplication = $oCore->application($aConfig );

$oApplication->init();

?>';

file_put_contents($this->application->content.$aRoute['controllerName']."_". $aRoute['actionName'].".php",$content);

echo json_encode(array('files' =>$this->application->content.$aRoute['controllerName']."_". $aRoute['actionName'].".php"));
}

function showJSON($result) {


  header('Content-type: text/javascript; charset=utf-8');


  echo json_encode($result);

  exit();

}


function redirect($result) {
  

 header('Location: '.$result['url']);	

 exit();	


}


function showUploadJson($result) {

  header('Content-type: text/html; charset=utf-8');

  echo json_encode($result);

  exit();

}




public function sendMailTemplate($result,$aParam, $aRoute){

  $sContent = $this->sendMailHTML($result,$aParam,$aRoute,false);

  header('Content-type: text/html; charset=utf-8');

  echo $sContent;

}


public function sendMailHTML($result,$aParam,$aRoute,$bSend = true){

  if($aRoute['controllerName'] =='email'){
    ob_start();
    if (!include ($this -> application -> directory . 'template/' . $aRoute['controllerName'] . '/' . $aRoute['actionName'] . '.php')) {

     throw new Exception("Template doesn't exist : ".$this -> application -> directory . 'template/' . $aRoute['controllerName'] . '/' . $aRoute['actionName'] . '.php', 404);

   }
   $sContent = ob_get_contents();

   ob_end_clean();

   $emo = $this->application->lib("emogrifier");

   $media = strpos($sContent, "@media screen");
   $endStyle = strpos($sContent, "</style>");
   $mediaqueries = substr($sContent, $media,$endStyle-$media);

   $sContent = substr($sContent,0, $media).substr($sContent,$endStyle);

   //$this->core->log('$media',$media);
   //$this->core->log('mediaqueries',$mediaqueries);
   //$this->core->log('$sContent',$sContent);


   $emo->setHTML($sContent);

   $sContent = $emo->emogrify();

   preg_match_all('/(?<=img )\s*src="\s*\s*(\S+)\s*"/', $sContent, $m);

   foreach($m[1] as $img) {

    if($this->application->env == "dev"){
      $sContent = str_replace($img,  'http://backhtml.lifeisbetteron.com:30075'.$img,$sContent);
    }else{
      $sContent = str_replace($img,  'https://www.libon.com/zendesk/lib'.$img,$sContent);
    }

  }

  $mail= $this -> core -> component('mail');

  $headPos = strpos($sContent, "</head");



  
  $sContent = substr($sContent,0, $headPos).'
  <style>

    '.$mediaqueries.'

  </style>
  '.substr($sContent,$headPos);

  
  if($bSend == true){
    $sended =  $mail->htmlSend($result['email'], $result['subject'], $sContent, $result['from'], $result['sender']);

    header('Content-type: text/html; charset=utf-8');

    echo json_encode(array('success'=>$sended));

  }else{

    return $sContent;
  }
  


}


}




public function sendMail($result,$aParam,$aRoute){

  $mail= $this -> core -> component('mail');
  
  $sended =  $mail->send($result['email'], $result['subject'], $result['content'], $result['from'], $result['sender']);

  header('Content-type: text/html; charset=utf-8');

  echo json_encode(array('success'=>$sended));

}



function download($result){

  if (file_exists($result['path'])) {
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename='.preg_replace("/[^a-zA-Z0-9]/", "_", $result['filename']) . ".".$result['format']);
    header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($result['path']));
    ob_clean();
    flush();
    readfile($result['path']);
    exit;
  }



}


}

