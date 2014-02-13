<?php
/**
 *
 *
 * An open source application development framework
 *
 * @package		Square-Framework
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2008 - 2009, EllisLab, Inc.
 * @license		http://Square-Framework.com/user_guide/license.html
 * @link		http://Square-Framework.com
 * @since		Version 1.0
 * @filesource
 */


global $__CORE, $__INSTANCE;
global $CFG, $EXT, $BM, $UNI, $URI, $OUT, $RTR, $SEC, $IN, $LANG, $CI;


function & getCore() {
	return $GLOBALS['__CORE'];
}

function & getInstance() {
	return $GLOBALS['__INSTANCE'];
}


function _error_handler() {
	return getCore()->php_error(func_get_args());
}

function _exception_handler() {
	return getCore()->php_exception(func_get_args());
}


function console() {
	return getCore() -> log(func_get_args());
}


/**
 * Determines if the current version of PHP is greater then the supplied value
 *
 * Since there are a few places where we conditionally test for PHP > 5
 * we'll set a static variable.
 *
 * @access   public
 * @param    string
 * @return   bool    TRUE if the current version is $version or higher
 */
if (!function_exists('is_php')) {
	function is_php($version = '5.0.0') {
		static $_is_php;
		$version = (string)$version;

		if (!isset($_is_php[$version])) {
			$_is_php[$version] = (version_compare(PHP_VERSION, $version) < 0) ? FALSE : TRUE;
		}

		return $_is_php[$version];
	}

}

class core {

	var $_aFramework = array();
	// All these are set automatically. Don't mess with them.
	var $_aFile = array();
	var $_aComponent = array();

	var $_iTimeLimit = 300;
	var $debug = false;
	var $_sErrorLevel = 0;
	var $_sAppClass = '__application__';

	var $subclass_prefix = '';

	public function _require() {
		require_once SYSTEM_PATH . 'core/base.php';

		require_once SYSTEM_PATH . 'core/application.php';

		require_once SYSTEM_PATH . 'core/component.php';

		require_once SYSTEM_PATH . 'core/adapter.php';
	}

	function setCore(&$pCore) {
		global $__CORE;
		if (!$__CORE) {
			$__CORE = $pCore;
		}
	}

	function setInstance(&$pApplication) {
		global $__INSTANCE;
		if (!$__INSTANCE) {
			$__INSTANCE = $pApplication;
		}

		$this -> config = $this->component('config_core'); 


	}

	public function _init() {
		$this->setCore($this);
		class_alias($this->_sAppClass,'application');

		set_time_limit($this -> _iTimeLimit);
		error_reporting($this -> _sErrorLevel);

		set_error_handler('_error_handler');

		set_exception_handler('_exception_handler');



	}

	public function __construct() {
		$this -> _require();

		$this -> _init();
	}



	
	public function application($aApplicationConfig = array()) {

		if (!isset($this -> _oApplication)) {

			$this -> _oApplication = $this -> createApplication($aApplicationConfig);
			$this -> _oApplication -> _init();
			$this->setInstance($this -> _oApplication);

		}

		return $this -> _oApplication;
	}

	public function createApplication($aApplicationConfig, $sApplicationDirectory = 'application') {

		$sApplicationClass = $aApplicationConfig['name'] . '_' . $aApplicationConfig['type'];

		$sApplicationPath = $this -> loadApplication($aApplicationConfig['domain'],$aApplicationConfig['name'], $sApplicationClass, $aApplicationConfig['type'], $sApplicationDirectory);

		if ($sApplicationPath != false) {

			return new $sApplicationClass($this, $aApplicationConfig, $sApplicationPath);
			

		}else{
			throw new Exception("Error Creating application", 404);
			
		}

	}

	public function &loadApplication($sDomainName, $sApplicationName, $sApplicationClass, $sApplicationType, $sApplicationDirectory) {

		if (!class_exists($sApplicationType)) {
			$this -> loadApplicationType($sApplicationType);
		}


		if (!class_exists( '_' . $sApplicationDirectory . '_')) {

			$this->aClassAlias = array($sApplicationType, '_' . $sApplicationDirectory . '_');
			if (!class_alias($sApplicationType, '_' . $sApplicationDirectory . '_')) {
				return false;
			}
		}
		
		$sApplicationPath = APP_PATH.'server'.DIRECTORY_SEPARATOR.$sApplicationType . DIRECTORY_SEPARATOR;

		$sApplicationFilePath = APP_PATH.'server'.DIRECTORY_SEPARATOR . $sApplicationName . '.php';

		if (
			require ($sApplicationFilePath)) {

			if (!class_alias($sApplicationName, $sApplicationClass)) {
				return false;
			}

			return $sApplicationPath;
		}

		return false;
	}

	public function loadApplicationType($sApplicationType) {

		$sApplicationTypePath = SYSTEM_PATH . 'application'.DIRECTORY_SEPARATOR. $sApplicationType . '.php';

		if (
			require ($sApplicationTypePath)) {
			return true;

	}

	return false;
}

	/**
	 * Constructor
	 *
	 * Sets the path to the view files and gets the initial output buffering level
	 *
	 * @access  public
	 */
	function & framework($sFramework) {

		if (!isset($this -> _aFramework[$sFramework])) {

			//		setCore($this);
			$this -> _aFramework[$sFramework] = $this -> adapter('framework', $sFramework);

		}

		return $this -> _aFramework[$sFramework];

	}

	public function & loadYiiComponent($sComponentName, $aOption) {

		if (!empty($aOption)) {

			$aOption['class'] = $sComponentName;
			$oComponent = $this -> framework('yii') -> createComponent($aOption);

		} else {
			$oComponent = $this -> framework('yii') -> createComponent($sComponentName);

		}

		if (!$oComponent -> getIsInitialized())
			$oComponent -> init($aOption);

		return $oComponent;

	}

	public function & loadCiComponent($sClassName, $sDirectory = 'libraries', $aOption = array()) {

		$oComponent = $this -> framework('ci') -> load_class($sClassName, $sDirectory, 'CI_', $aOption);

		return $oComponent;

	}

	function loadAdapter($sFolder, $sName) {

		$sfilePath = SYSTEM_PATH . 'adapter/' . $sFolder . '/' . $sName . '.php';

		if (
			require ($sfilePath)) {
			return class_exists($sName);
	}

}

public function & createAdapter($sName, $aOption) {

	$oAdapter = new $sName($this, $this -> _oApplication, $aOption);
	return $oAdapter;

}

	/**
	 * adapter
	 *
	 * This function lets users load and instantiate Adapter.
	 *
	 * @access  public
	 * @param   string  the name of the Adapter
	 * @param   string  name for the model
	 * @param   bool    database connection
	 * @return  void
	 */
	function adapter($sType, $sName, $aOption = array()) {

		if (!isset($this -> _aAdapter[$sName])) {
			if ($this -> loadAdapter($sType, $sName)) {

				$this -> _aAdapter[$sName] = $this -> createAdapter($sName, $aOption);
				return $this -> _aAdapter[$sName];
			}
		}

		return $this -> _aAdapter[$sName];

	}

	/**
	 * loadComponent
	 *
	 * This function lets users load and instantiate Component.
	 *
	 * @access  public
	 * @param   string  the name of the Component
	 * @param   string  name for the model
	 * @param   bool    database connection
	 * @return  void
 */

	function loadComponent($sName, $sFolder = '') {

		$sfilePath = str_replace('_', '/', $sName);

		if ($sFolder != '') {

			$sfilePath = SYSTEM_PATH . 'component/' . $sFolder . '/' . $sfilePath . '.php';

		} else {
			$sfilePath = SYSTEM_PATH . 'component/' . $sfilePath . '.php';
		}

		if (
			require ($sfilePath)) {
			return class_exists($sName);
	}

}

public function & createComponent($sName, $aOption) {

	$oClass = new $sName($this, $this -> _oApplication, $aOption);

	return $oClass;
}

	/**
	 * component
	 *
	 * This function lets users load and instantiate Component.
	 *
	 * @access  public
	 * @param   string  the name of the Component
	 * @param   string  name for the model
	 * @param   bool    database connection
	 * @return  void
	 */

	
	function component($sName = '', $aOption = array()) {

		if (!isset($this -> _aComponent[$sName])) {
			if ($this -> loadComponent($sName)) {

				$this -> _aComponent[$sName] = $this -> createComponent($sName, $aOption);
				return $this -> _aComponent[$sName];
			}
		}

		return $this -> _aComponent[$sName];

	}



	public function error($e) {

	}

	public function log() {

	}

	public function mark($sNewMark) {

	}

	public function unmark($sNewMark) {


	}


	public function exception($e) {

	}


	public function php_error($e) {


	}

	public function php_exception($e) {


	}

}
