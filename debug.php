<?php

require SYSTEM_PATH . 'dev.php';
global $__CORE, $__INSTANCE;

class core_debug extends core_dev {

	var $_sErrorLevel = E_ALL;

	var $debug = true;

	var $_sAppClass = '__debug__';


	public function _require() {
		parent::_require();
		require_once SYSTEM_PATH . "debug/application.php";
		require_once SYSTEM_PATH . "debug/component.php";
	}


	public function _init() {
		parent::_init();
		$this -> firephp = FirePHP::getInstance(true);

	}

	public function & loadCiComponent($sClassName, $sDirectory = 'libraries', $aOption = array()) {

		$oCiComponent = parent::loadCiComponent($sClassName, $sDirectory, $aOption);

		$pComponent = new debug_component($this, $this -> _oApplication, $oCiComponent, $sClassName);

		return $pComponent;

	}



	public function & loadYiiComponent($sComponentName, $aOption) {

		$oYiiComponent = parent::loadYiiComponent($sComponentName, $aOption);

		$pComponent = new debug_component($this, $this -> _oApplication, $oYiiComponent, $sComponentName);

		return $pComponent;

	}

	

	public function & createComponent($sName, $aOption) {
	
		$pBehavior = parent::createComponent($sName, $aOption);
		
		$pDebug = new debug_component($this, $this -> _oApplication, $pBehavior, $sName);

		return $pDebug;

	}

}
