<?php


class generator extends component {


        var $aApplicationFolder = array('config'=> array('server','environnement','controller'),
                                        'controller'=>array(),
                                        'model'=>array('link'),
                                        'test'=>array(),
                                        'view' => array('engine','layout','template','menu'),
                                        'vhost' =>array());
        
        public function application($sApplicationName){
            
           $sApplicationName = strtolower($sApplicationName);
            
           $sContent = file_get_contents(ROOT_PATH.'tpl/application.tpl');
           
           $sContent = str_replace("{sApplicationName}", $sApplicationName, $sContent); 
           
           mkdir(ROOT_PATH.'application/'.$sApplicationName);
          
          
          foreach($this->aApplicationFolder as $sFolder => $aSubFolder){
              
               mkdir(ROOT_PATH.'application/'.$sApplicationName.'/'.$sFolder);
              
              
              foreach($aSubFolder as $sSubFolder){
                  
                   mkdir(ROOT_PATH.'application/'.$sApplicationName.'/'.$sFolder.'/'.$sSubFolder);
              
              }
              
          } 
          
          file_put_contents(ROOT_PATH.'application/'.$sApplicationName.'.php', $sContent);

        }



        public function module($sApplicationName){
            
           $sApplicationName = strtolower($sApplicationName);
            
           $sContent = file_get_contents(ROOT_PATH.'tpl/module.tpl');
           
           $sContent = str_replace("{sApplicationName}", $sApplicationName, $sContent); 
           
           mkdir(ROOT_PATH.'module/'.$sApplicationName);
          
          foreach($this->aApplicationFolder as $sFolder => $aSubFolder){
              
               mkdir(ROOT_PATH.'module/'.$sApplicationName.'/'.$sFolder);
              
              foreach($aSubFolder as $sSubFolder){
                  
                   mkdir(ROOT_PATH.'module/'.$sApplicationName.'/'.$sFolder.'/'.$sSubFolder);
                  
              }
              
          } 
          
          file_put_contents(ROOT_PATH.'module/'.$sApplicationName.'.php', $sContent);

        }  
        
        
         public function vhost($oApplication,$sVhostData){
            
           $sContent = file_get_contents(ROOT_PATH.'tpl/vhost/application_index.tpl');
           
            mkdir($oApplication->path.'/vhost/'.$sVhostData['name']);
           
           foreach($sVhostData as $index => $param){
                  $sContent = str_replace('{'.$index.'}', $param, $sContent); 
           }
           
           file_put_contents($oApplication->path.'/vhost/index.php', $sContent);

        } 

         
         public function webservice($oApplication,$aData){
             
                
              
                $sContent = file_get_contents(ROOT_PATH.'tpl/'.$sComponent.'.tpl');
           
                mkdir($oApplication->path.'/controller/'.$aData['name']);
           
               foreach($aData as $index => $param){
                  $sContent = str_replace('{'.$index.'}', $param, $sContent); 
               }
           
               file_put_contents($oApplication->path.'/controller/'.$aData['name'].'/'.$aData['name'].'.php', $sContent);

         } 
         
         
         public function controller($oApplication,$aData){
             
             
            $sFilePath  = $oApplication->path.'/controller/'.$aData['name'].'.php';
            
            if(file_exists($sFilePath)){
                
                throw new Exception("Controller Already exists", 1);
                
            }
                
             $sContent = file_get_contents(ROOT_PATH.'tpl/webservice/controller.tpl');
           
             mkdir($oApplication->path.'/config/controller/'.$aData['name']);
           
            foreach($aData as $index => $param){
                  $sContent = str_replace('{'.$index.'}', $param, $sContent); 
            }
           
            return file_put_contents($sFilePath, $sContent);
       
         } 

}
