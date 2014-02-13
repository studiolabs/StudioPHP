<?php

require SYSTEM_PATH . 'core.php';

global $__CORE, $__INSTANCE;

class core_test extends core {

	var $_sErrorLevel = E_ALL;

	public function _require() {
		parent::_require();
		require_once SYSTEM_PATH . "core/debug.php";

		require_once (SYSTEM_PATH . 'lib/FirePHPCore/FirePHP.class.php');
	}

	public function _init() {
		parent::_init();
		$this -> firephp = FirePHP::getInstance(true);
	}

	public function log() {

	
       $this -> firephp -> log(func_get_args()); 
   
	}

	public function exception($e) {


		ob_start();		
		debug_print_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
		ob_end_flush();

		
		$trace = explode('#',ob_get_contents());

		
		$this -> firephp -> table($trace, $e -> getMessage() . '  ----- file :' . $e -> getFile() . " line :" . $e -> getLine());
		

		ob_end_clean();
	}


}
