<?php


class file extends component {        

  var $allowed_files = array( 'zip' => array("zip", "rar", "gz"),
    'image' =>array('jpg','png','jpeg','gif'),
    'document' => array('xlsx','html','docx','pptx','txt'));


  function mkdir($sPath,$bRecursive=false){

    if(!is_dir($sPath)){
      mkdir($sPath,0755,$bRecursive);
    }

  }

  public function uploadTo($file,$destination,$folder, $type='image'){


    $target_path = $this->application->content.$destination;

    $aFolder = explode('/',$folder);

    foreach($aFolder as $sFolder){

      if(!empty($sFolder)){
       $target_path .= DIRECTORY_SEPARATOR.$sFolder;
     }

   }

   $this->mkdir( $target_path, true);


   if(is_array($file)){

    $target_path .=  DIRECTORY_SEPARATOR. basename( $file['name']); 

    if(move_uploaded_file($file['tmp_name'], $target_path)) {
     return $target_path;
   }

  }else if(file_exists($file)){

     $target_path .=  DIRECTORY_SEPARATOR. basename($file); 

     if(rename($file,$target_path)){
      return $target_path;
    }

  } 

 return false;

}




   /*     $aFile = explode ('.', basename( strtolower($file['name'])));

        $finfo = new finfo(FILEINFO_MIME);

        
        //$this->core->log($finfo);
        $aType = explode('/',$finfo->file($file['tmp_name']));
/*
       if(in_array($aFile[1], $this->allowed_files[$type]) && in_array($aType[1], $this->allowed_files[$type])){
*/


     /*  }else{
               
           throw new Exception("File Type not allowed", 404);
           
         }*/

         public function getFileExtension($sPath){

           $aFile =    pathinfo($sPath); 

           return $aFile['extension'] ;

         }

         public function moveTo($sFilePath,$destination,$folder,$sFileName){


          $target_path = $this->application->content ;  


          $aDir = explode(DIRECTORY_SEPARATOR, $destination.DIRECTORY_SEPARATOR . $folder);      

          foreach($aDir as $sFolder){

            if($sFolder != ""){

              $target_path .= $sFolder. DIRECTORY_SEPARATOR ;  

              $this->mkdir($target_path);
            }


          }

          $extension = $this->getFileExtension($sFilePath);



          if(strpos($sFileName, $extension) == false){

            $sFileName =  $sFileName.'.'.$extension; 

          }

          $target_path .= $sFileName;

          if(file_exists( $target_path)){

            unlink($target_path);

          }

          if(rename($sFilePath, $target_path)) {
           return array(  $this->application->contentUrl. substr(
            $target_path,
            strlen($this->application->content)-1
            ),
           $target_path);
         } 

         return false;
       }

       public function moveToSubDomain($sFilePath,$destination,$folder,$name){

        $target_path = $this->application->content.DIRECTORY_SEPARATOR.$destination;

        $target_path .= DIRECTORY_SEPARATOR . $folder; 


        $this->mkdir($target_path);

        $aFile = explode ('.', basename( $sFilePath));

        $sFileName =  $name.'.'.$aFile[1]; 
        $target_path .= DIRECTORY_SEPARATOR.$sFileName;

        if(rename($sFilePath, $target_path)) {
         return array($destination.'.'.$this->application->vhost.DIRECTORY_SEPARATOR . $folder.DIRECTORY_SEPARATOR.$sFileName,$target_path);
       } 

       return false;
     }


     public function delete(){


     }


     public function info(){


     }

   }
