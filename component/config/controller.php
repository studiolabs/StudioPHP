<?php  
/*
 * Square-Framework
 *
 * An open source module development framework for PHP 4.3.2 or newer
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
class config_controller extends component{


	public $aDeleteAction = array('_delete');

	public $aEditAction = array('_create','_update','_links','_save');

	public $aReadAction = array('detail','all','find','links');

	public $aSearchAction = array('find');

	var $_aParams = array();

	var $_aCreateParams = array();

	var $_aSearchParams= array();

	var $_aEditParams = array();

	var $_aKey = array();

	var $_aDeleteKey = array();

	public function _init(){

		foreach($this->_aKey as $sKey){
			$this->_aParams[$sKey]['key'] = true;
		}

		foreach($this->_aCreateParams as $sKey){
			$this->_aParams[$sKey]['key'] = true;
		}

		foreach($this->_aSearchParams as $sKey){
			$this->_aParams[$sKey]['key'] = true;
		}

	}


	public function getParam($sKey,$bAllowBlank=true){
		return $this->_getParam($sKey,$bAllowBlank);
	}


	private function _getParam($sKey,$bAllowBlank=true){
		$aParam = $this->_aParams[$sKey];
		$aParam['allowBlank']= $bAllowBlank;	
		return $aParam;
	}

	public function _create(){
		return $this->getCreateParams();
	}

	public function _update(){
		return $this->getEditParams();
	}

	public function _delete(){
		return $this->getDeleteParams();
	}

	public function detail(){
		return $this->getKeyParams();
	}

	public function links(){
		return $this->getKeyParams();
	}

	public function search(){

		return $this->getSearchParams();

	}

	public function find(){
		return $this->getSearchParams();
	}

	public function all(){
		return $this->getSearchParams();
	}



	public function getKeyParams (){
		$aConfig  = array();

		foreach($this->_aKey as $sKey){
			$aConfig[$sKey] = $this->_getParam($sKey,false);
		}

		return $aConfig;

	}


	public function getCreateParams (){
		$aConfig  = array();

		
		foreach($this->_aCreateParams as $sKey){
			$aConfig[$sKey] = $this->_getParam($sKey,false);
		}

		foreach($this->_aEditParams as $sKey){
			if(!isset($aConfig[$sKey]))
				$aConfig[$sKey] = $this->_getParam($sKey);
		}

		return $aConfig;

	}


	public function getEditParams (){

		$aConfig  = array();

		foreach($this->_aKey as $sKey){
			$aConfig[$sKey] = $this->_getParam($sKey,false);
		}

		foreach($this->_aCreateParams as $sKey){
			if(!isset($aConfig[$sKey]))
				$aConfig[$sKey] = $this->_getParam($sKey);
		}

		foreach($this->_aEditParams as $sKey){
			if(!isset($aConfig[$sKey]))
				$aConfig[$sKey] = $this->_getParam($sKey);
		}

		return $aConfig;

	}


	public function getDeleteParams (){

		$aConfig  = array();

		foreach($this->_aDeleteKey as $sKey){
			$aConfig[$sKey] = $this->_getParam($sKey,false);
		}

		foreach($this->_aSearchParams as $sKey){
			if(!isset($aConfig[$sKey]))
				$aConfig[$sKey] = $this->_getParam($sKey);
		}

		return $aConfig;

	}


	public function getSearchParams (){
		$aConfig  = array();

		foreach($this->_aCreateParams as $sKey){

			if(in_array($sKey, $this->_aKey)){
				$aConfig[$sKey] = $this->_getParam($sKey);
			}

		}

		foreach($this->_aSearchParams as $sKey){
			if(!isset($aConfig[$sKey]))
				$aConfig[$sKey] = $this->_getParam($sKey);
		}

		return $aConfig;

	}



}

// END SF_Config class

/* End of file Config.php */
/* Location: ./system/libraries/Config.php */