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
class config_core extends component{


	public $path = "";


	var $_aConfig = array();

	public function _init(){

		$this->core->component('config_server');
		$this -> path = $this->application->path;

	}



	public function loadConfig($Path) {

		$filepath = $this -> path . 'config/' . $Path . '.php';

		return require ($filepath);

	}

	
	public function server($sConfigName) {

		if (!isset($this ->  _aConfig['server'][$sConfigName])) {

			if($this -> loadConfig($sConfigName)){

				$sClassName = $sConfigName."_".$this->application->name."ServerConfig";

				$this ->  _aConfig['server'][$sConfigName] = new $sClassName($this->core,$this->application);

			}

		}

		return $this ->  _aConfig['server'][$sConfigName];

	}




	public function env() {

		if (!isset($this ->  _aConfig['env'][ENV])) {

			if($this -> loadConfig('environnement/'.ENV)){

				$sClassName = $sConfigName."_".$this->application->name."EnvConfig";

				$this ->  _aConfig['env'][$sConfigName] = new $sClassName($this->core,$this->application);
				
			}

		}

		return $this -> _aController[$sConfigName];

	}



}

// END SF_Config class

/* End of file Config.php */
/* Location: ./system/libraries/Config.php */