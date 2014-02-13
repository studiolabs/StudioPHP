<?php  


/**
 * Square
 *
 * An open source application development framework for PHP 4.3.2 or newer
 *
 * @package		Square
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2008 - 2009, EllisLab, Inc.
 * @license		http://Square.com/user_guide/license.html
 * @link		http://Square.com
 * @since		Version 1.0
 * @filesource
 */



// ------------------------------------------------------------------------

/**
 * Exceptions Class
 *
 * @package		Square
 * @subpackage	Libraries
 * @category	Exceptions
 * @author		ExpressionEngine Dev Team
 * @link		http://Square.com/user_guide/libraries/exceptions.html
 */
class error extends component{
		
	public function _require(){
		
		
		require_once(SYSTEM_PATH.'lib/FirePHPCore/FirePHP.class.php');
	}
	
	
	public function _init(){
		
		$this->firephp = FirePHP::getInstance(true);

		
	}
	// --------------------------------------------------------------------

	function show()
	{	
		$this->firephp->error(func_get_args());
	}
	
	function info()
	{	
		$this->firephp->info(func_get_args());
	}	

	function warning()
	{	
		$this->firephp->warn(func_get_args());
	}	

	
	function exception()
	{	
		$this->firephp->error(func_get_args());
	
	}
	
	function log()
	{
		
		$var = func_get_args();
		
		if(count($var)==1 && isset($var[0])){
			$var =$var[0];
		}	
			
		$this->firephp->log($var);

	}
	



}





// END Exceptions Class

/* End of file Exceptions.php */
/* Location: ./system/libraries/Exceptions.php */