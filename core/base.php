<?php


class base {
    
    var $core;
    var $application;

    public function __construct(&$pCore,&$pApplication)
    {   

         $this->core = $pCore;
		 
		 $this->application = $pApplication;

   } 
	
  
}
