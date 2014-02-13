<?php

if (class_exists('core')) {
    
    $classCore = 'core';
          
}

if (class_exists('core_test')) {
    
    $classCore = 'core_test';
          
}

if (class_exists('core_debug')) {
    
    $classCore = 'core_debug';
          
}

if (!class_exists('core_alias')) {
      
    class_alias($classCore, 'core_alias');
             
}

class core_module extends core_alias {
    

     public function module($aModuleConfig = array(), $aApplicationConfig = array()) {

        
        foreach($aApplicationConfig as $param => $value){
            
            if(!isset($aModuleConfig[$param])){
                  $aConfigModuleApplication[$param] = $value;
            }
           
        }
         
         
         if (!isset($this ->_oApplication -> core)) {
             
              	$this -> _oApplication =  $this -> createApplication($aModuleConfig,'module');
				$oParentApplication =  $this -> createApplication($aApplicationConfig);
				
				$this -> _oApplication -> _init($oParentApplication);
				$oParentApplication->_init();
                
				$this->setInstance($this -> _oApplication);
                
        }
     
        return $this -> _oApplication;
        
    }
     
     
     
     

}
