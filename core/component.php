<?php


class component extends base{

    public function __construct(&$pCore,&$pApplication,$aConfig=array())
    {   
     
         parent::__construct($pCore,$pApplication);

		  foreach($aConfig as $key =>$value){
		 	
		 	$this->$key=$value;
		 }
		 
		 $this->_require();
		 $this->_init();
       
    } 
	
	public function _require(){
		
		
	}


	public function _init(){
		
		
	}


}