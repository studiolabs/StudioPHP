<?php

require SYSTEM_PATH . 'core.php';
global $__CORE, $__INSTANCE;

class core_dev extends core {
	
	var $_iCountError=0;

	var $debug=true;
	var $trace=true;

	public function _require() {
		parent::_require();
		require_once (ROOT_PATH. 'lib/firephp-core/lib/FirePHPCore/FirePHP.class.php');
	}

	var $_sMark = '';

	public function _init() {
		parent::_init();

		if($this->debug ==true){

			$this -> firephp = FirePHP::getInstance(true);

			error_reporting(E_ALL ^ E_NOTICE);

		}
		

	}


	function logArray($sName, $data){


		$table = array();
		$keys =array();

		$table[] = array('','');


		if( count($data, COUNT_RECURSIVE) != count($data)){

			$this -> firephp->log(" [ ".$sName." ]");

			foreach ($data as $key => $value) {

				if(is_string($key)){
					$keys[] = $key;
				}

				if(is_array($value)){

					$this -> firephp -> log( $value,$key);
				}

			}

			$this -> firephp->log(" [ ".$sName." ]");

		}else{

			foreach ($data as $key => $value) {

				if(is_string($key)){
					$keys[] = $key;
				}

				$table[] = array($key,$value);

			}

			if(count($keys)>0){
				$sName.=' : [ ';
				$sName.=join($keys,', ');
				$sName.=' ]';
			}else{
				$sName.='('.count($data).')';
			}


			$this -> firephp -> table($sName,$table); 

		}

	}



	public function log() {
		$args = func_get_args();


		$debug = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);

		$this -> firephp -> log('line : '.$debug[1]['line'].'  file : '.$debug[1]['file']);

		if(count($args) == 1){

			return	$this -> firephp -> log($args[0]);


		}else if(count($args) == 2){

			if(is_string($args[0])){
				$sName = $args[0];

				if(is_array($args[1])){

					return $this->logArray($sName ,$args[1]);

				}

				return	$this -> firephp -> log($sName,$args[1]);
				
			}

		}

		return $this -> firephp -> log($args); 
	}


	var $_iMark = 0;

	var $_sLastMark= "";


	public function step($sNewMark) {

		$this -> firephp -> info($sNewMark); 


	}



	public function mark($sNewMark) {
	
		$this->_sLastMark = $sNewMark;

		$this -> firephp -> group($sNewMark, array('Collapsed'=>true)); 


	}


	public function unmark($iMark) {

			$this -> firephp -> groupEnd(); 

	}



	public function error($e) {

		//		$this -> firephp -> log('error',$e);
		if($this->debug ==true){
			$table = array();

			$table[] = array('' , '');
			$table[] = array('error' , $e -> getMessage());
			$table[] = array('file' , $e -> getFile());
			$table[] = array('line' , $e -> getLine());
			$this -> firephp -> table( "[error][".$this->_sLastMark ."] ".$e -> getMessage(),$table);

			$this->trace('[error] '.$args[1]);

		}


	}

	public function exception($e) {
		if($this->debug ==true){
			$table = array();


			$table[] = array('' , '');
			$table[] = array('exception' , $e -> getMessage());
			$table[] = array('file' , $e -> getFile());
			$table[] = array('line' , $e -> getLine());
			$this -> firephp -> table( "[exception][".$this->_sLastMark ."] ".$e -> getMessage(),$table);

			$this->trace("[exception]".$e -> getMessage());



		}
	}

	public function trace($name) {

		$ignore = array('php_error','_error_handler','__call','call_user_func_array');

		foreach (debug_backtrace() as $value) {

			if(! in_array($value['function'], $ignore )  ){

				$message = "";
				$file = "";
				$line = "";

				if(isset($value['class'])){
					$message .= $value['class'].$value['type'];
				}

				$message .= $value['function'];


				if(isset($value['file'])){
					$file =  substr($value['file'], strpos($value['file'],WORKSPACE_PATH));
				}

				if(isset($value['line'])){
					$line =  $value['line'];
				}

				$table[] = array($line, $file,$message );

			}

		}

		$this -> firephp -> table( " [trace] ".$name,$table);


	}



	public function php_error($args) {


		if($this->debug ==true){
			$table = array();

			$table[] = array('line','file','method');

			$this -> firephp -> error($args[1]." - ".substr($args[2],strpos($args[2],WORKSPACE_PATH)). ' line :'.$args[3]);


			$this->trace('[php_error] '.$args[1]);

		}


	}

	public function php_exception($e) {
		if($this->debug ==true){
			$this -> firephp -> log('exception',$e); 

			$this->trace('[php_exception] '.$args[1]);

		}
	}

}
