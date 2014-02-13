<?php

class file_class extends component {

	public function format($sContent) {

		$aContent = explode("\n", trim($sContent));

		$i = 0;
		$z = 0;

		$end = count($aContent);

		while ($i != $end) {

			$ouvert += substr_count($aContent[$i], '{');
			$ferme += substr_count($aContent[$i], '}');

			$aContent[$i] = trim($aContent[$i]);

			if ($ouvert == $ferme) {
				$z--;
				$z--;
			}

			for ($t = $z; $t--; $t >= 0) {

				$aContent[$i] = "\t" . $aContent[$i];

			}

			$z = $ouvert - $ferme;

			if ($ouvert > $ferme) {
				$i++;
			} else {
				break;
			}
		}

		$sContent = str_replace("\n\n", "\n", join("\n", $aContent));

		return $sContent;

	}
	
	

	public function analyze($sPath) {

		$sContent = file_get_contents($sPath);

		$aReport = array('function'=>array());
		

		$aContent = explode("\n", $sContent);

		$sFind = 'class '; 

		$i = 0;

		foreach ($aContent as $iLine => $sLine) {

			$iFind = strpos($sLine, $sFind);



			if (is_integer($iFind) ) {

				if($i==0){
					
						
						$startClassName = $iFind + 6;
						
						$iExtend = strpos($sLine, ' extends ');
						
						$iStartClass = strpos($sLine, '{');

						if(is_integer($iExtend)){
							
							$endClassName = $iExtend -$startClassName  ;
							
							$aReport['extends'] = trim(substr($sLine, $iExtend, $iExtend-$startClassName));
							
							$aReport['className'] = trim(substr($sLine, $startClassName, $endClassName));
						
							
						}else{
							
							$endClassName = $iStartClass -$startClassName  ;
														
							$aReport['className'] = trim(substr($sLine, $startClassName, $endClassName));
										
						
						}
						

											
						$sFind = 'function ';

						$i = 1;

				}elseif($i==1){
							
						$startMethodName = $iFind + 9;
						$endMethodName = strpos($sLine, '(') - $startMethodName;
						
						$aReport['function'][]= array('name' =>trim(substr($sLine, $startMethodName, $endMethodName)),'startLine'=>$iLine);
										
				}

			}

			

		}

		return $aReport;

	}

	public function getFunction($sPath,$sFunctionName, $iStartLine) {

		$aContent = file($sPath);
     
		
		$sFunctionContent = "";

		foreach ($aContent as $iLine => $sLine) {

			$iFind = strpos($sLine, 'function ');

			if (is_integer($iFind) ) {
				
						
						$iFunction = strpos($sLine, $sFunctionName.'(');
					
						if(is_integer($iFunction)){
							    
                                                          
							$ouvert =0;
							$ferme =0;
							
							$i=1;

							
						}
						
			}
			
			if($i==1){
				
				//$this->core->log($sLine);
				$sFunctionContent .= $sLine;
				

						$ouvert += substr_count($sLine, '{');

						$ferme += substr_count($sLine, '}');
										

						if ($ouvert == $ferme) {
						
							break;
						}
				

				}
				
				
			
		}
		
		return $sFunctionContent;



	}

	public function findLineOf($sSearch, $aContent) {

		foreach ($aContent as $iLine => $sLine) {
			if (strpos($sLine, $sSearch) != false) {
				return $iLine;
			}
		}

		return false;
	}

	public function replace($sStart, $sType, $sPath, $sNewContent) {

		$analyze = array();

		$report = array();

		$aContent = file($sPath);

		switch ($sType) {
			case 'function' :
				$aContent = str_replace('function  ' . $sStart . '(', 'function ' . $sStart . '(', $aContent);

				$iStartLine = $this -> findLineOf('function ' . $sStart . '(', $aContent);

				if ($iStartLine != false) {

					$i = $iStartLine;

					while (isset($aContent[$i])) {

						$ouvert += substr_count($aContent[$i], '{');

						$ferme += substr_count($aContent[$i], '}');

						if ($ouvert > $ferme) {
							$i++;
						} else {
							break;
						}
					}

				}

				break;

			default :
				break;
		}

		$aNewContent = array_merge(array_slice($aContent, 0, $iStartLine), explode("\n", $sNewContent), array_slice($aContent, $i + 1));


        $sContent = str_replace("\n\n", "\n", join("\n", $aNewContent));
        
		return $sContent;

	}

	public function insert($sType, $sPath, $sNewContent) {
	    
        

		$sContent = file_get_contents($sPath);
        
        $sContent = str_replace("\n\n", "\n", join("\n", $sContent));

		return substr($sContent, 0, strrpos($sContent, '}')) . "\n\n" . $sNewContent . "\n\n" . substr($sContent, strrpos($sContent, '}'));

	}

}
