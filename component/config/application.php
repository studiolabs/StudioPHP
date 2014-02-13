<?php  
/**
 * Square-Framework
 *
 * An open source application development framework for PHP 4.3.2 or newer
 *
 * @package		Square-Framework
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2008 - 2009, EllisLab, Inc.
 * @license		http://Square-Framework.com/user_guide/license.html
 * @link		http://Square-Framework.com
 * @since		Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * Square-Framework Config Class
 *
 * This class contains functions that enable config files to be managed
 *
 * @package		Square-Framework
 * @subpackage	Libraries
 * @category	Libraries
 * @author		ExpressionEngine Dev Team
 * @link		http://Square-Framework.com/user_guide/libraries/config.html
 */
class config_application extends component{


	public $path = "";


	var $_aConfig = array();

	public function _require(){

		$this->core->component('config_controller');
		$this -> path = $this->application->path;

	}


	public function loadConfig($Path) {

		$filepath = $this -> path . 'config/' . $Path . '.php';

		return require ($filepath);

	}



	public function controller($sConfigName) {

		if (!isset($this -> _aConfig['controller'][$sConfigName])) {

			if($this -> loadConfig('controller/'.$sConfigName)){

				$sClassName = $sConfigName."_".$this->application->name."ControllerConfig";

				$this -> _aConfig['controller'][$sConfigName] = new $sClassName($this->core,$this->application);

			}

		}

		return $this -> _aConfig['controller'][$sConfigName];

	}

}

// END SF_Config class

/* End of file Config.php */
/* Location: ./system/libraries/Config.php */