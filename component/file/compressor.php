<?php 



class file_compressor extends component{
    
    var $sYuiCompressorPath = 'bin/yuicompressor.2.4.8.jar';
	
	var $JavaCommand = 'java';
	
	
	public function js($sPathInput,$sPathOutput){
	
		 $sOutput = null;
	   	 $sReturn = null;	
		 //Création du fichier final minimisé

		 					//$this->core->log($this->JavaCommand.' -jar '.ROOT_PATH.$this->sYuiCompressorPath.' --type js '.$sPathInput.' -o '.$sPathOutput);

	     exec($this->JavaCommand.' -jar '.ROOT_PATH.'framework/'.$this->sYuiCompressorPath.' --type js '.$sPathInput.' -o '.$sPathOutput, $sOutput, $sReturn);
		
		 return $sReturn;
 
	}
	
	
	public function css($sPathInput,$sPathOutput){
	
		 $sOutput = null;
	   	 $sReturn = null;	
		 //Création du fichier final minimisé
	     exec($this->JavaCommand.' -jar '.ROOT_PATH.'framework/'.$this->sYuiCompressorPath.' --type css '.$sPathInput.' -o '.$sPathOutput, $sOutput, $sReturn);
		
		 return $sReturn;
 
	}

}
