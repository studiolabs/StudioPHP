<?php


class adapter extends base {
    
    public function __construct(&$pCore,&$pApplication,$aConfig=array())
    {   
     
          parent::__construct($pCore,$pApplication);
		 
		  foreach($aApplicationConfig as $key =>$value){
		 	
		 	$aApplicationConfig->$key=$value;
		 }
		 
		 $this->_init();
       
    } 
	
	
	private function _init(){
		
		
	}
  
}