<?php

class generator_webservice extends component{
    
    
    var $aActions = array('_create','_update','_delete','_link','details','find','all','links','search');
    
    
    public function _init(&$this){
        
        $this->path = $this->path;
    
    }
    
    public function create($aSchema){
        
        $this->controller($aSchema);
        
        foreach($this->$aActions as $sActionName){
            
            $aData = $this->$sActionName($aSchema);
            
            $aData['controllerName'] = $sActionName;
            
            $aData['actionName'] = $sActionName;
            
            $this->action($aData);
            
            $this->model($aData);
            
        }
        
    }
    
    
     public function copy($aSchema){
        
        foreach($this->$aActions as $sActionName){
            
            $aData = $this->$sActionName($aSchema);
          
            $this->controller($aData);
            
            $this->config('controller',$aData);
            
            $this->model($aData);
            
        }
        
    }
     
     
      public function delete($aSchema){
        
        foreach($this->$aActions as $sActionName){
            
            $aData = $this->$sActionName($aSchema);
          
            $this->controller($aData);
            
            $this->config('controller',$aData);
            
            $this->model($aData);
            
        }
        
    }
   /* 
    
    $schema = array(
'name' => 'image',
'field' => array('name' => array('type'=>'string', 'length'=>45, 'index'=>true, 'required' => true),
                 'path'=> array('type'=>'string', 'length'=>400 ),
                 'file-path'=> array('type'=>'path'),
                 'realpath'=> array('type'=>'string', 'length'=>400),
                 'position' => array('type'=>'integer', 'size'=>3)),           
 'link' =>array('parent' => array('album'),
                'child' => array('label')
                ),
 'history'=> true
);


    * 
    */
    public function getIds(&$aSchema){
        
        $aIds = array();
        
        if(!isset($aSchema['id'])){
           
           $aIds= array($aSchema['name'].'Id');
           
            $aNewParam = array();
        
            $aNewParam['type'] = 'integer';
            $aNewParam['required'] = true;
            
           foreach($aIds as $aId){
               if(!isset($aSchema['field'][$aId])){
                $aSchema['field'][$aId] =$aNewParam;
               }
           }
        
           
            
        }else{
            
            $aIds = $aSchema['id'];
        }
        
        
        return $aIds;
        
    }
    
    
    public function _create($aSchema){
        
        $aConfig = array();
        
        
        $aIds = $this->getIds($aSchema);
        
        foreach($aSchema['field'] as $sField =>$aField){
            
                 $param = array();

                if($aField['required'] == true){
                    
                    $param['allowBlank'] = false;
                    
                }

               switch($aField['type']){
                   
                   case 'string' : {
                        $param['type'] = $aField['type'];
                      
                      
                      if(isset($aField['length'])){
                          $param['maxLength'] = $aField['length'];
                      }
                       
                   } break;
                   
                   case 'file' : {
                      $param['type'] = $aField['type'];
                       
                   } break;
                   
                   default :{
                       
                       if(!isset($aField['type'])){
                           $aField['type'] = 'integer';
                       }
                       
                      $param['type'] = $aField['type'];
                       
                       if(isset($aField['max'])){
                          $param['max'] = $aField['max'];
                      }
                       
                        if(isset($aField['min'])){
                          $param['min'] = $aField['min'];
                      }
                        
                      if(isset($aField['length'])){
                          $param['maxLength'] = $aField['length'];
                      }
                       
                   }break;
                  
                    
                }
            
            $aConfig['param'][$sField] =$param;

        }

        foreach($aSchema['field'] as $sField => $aField){
            
              if(in_array($sField, $aIds) || $aField['index'] == true){
                  $aConfig['index'][]=$sField;
              }else if(is_string($aField['index'])){
                   $aConfig['index'][$aField['index']][]=$sField;
              }
        }
        
        
        return $aConfig;
        
    }
    
    public function action($aData){
        
         $sContent = file_get_contents(ROOT_PATH.'tpl/config/controller/'.$aData['actionName'].'.tpl');
        
         mkdir($this->path.'/config/controller/'.$aData['controllerName']);
           
         foreach($aData as $index => $param){
             $sContent = str_replace('{'.$index.'}', var_export($param,true), $sContent); 
         }
           
         file_put_contents($this->path.'/config/controller/'.$aData['controllerName'].'/'.$aData['actionName'].'.php', $sContent);
        
    }
    
    
    public function controller($aData){
        
         $sContent = file_get_contents(ROOT_PATH.'tpl/controller.tpl');
           
           
         foreach($aData as $index => $param){
             $sContent = str_replace('{'.$index.'}', $param, $sContent); 
         }
           
         file_put_contents($this->path.'/controller/'.$aData['name'].'.php', $sContent);
        
    }
    
    
    public function model(){
        
        
    }

    
    
    
}


?>