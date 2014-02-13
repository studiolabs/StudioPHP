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
class route extends component{
		
		var $_aAction = array();

		public function get($aRoute){

				if(isset($this->_aAction[$aRoute['actionName']])){

					$aRoute['actionName'] =$this->_aAction[$aRoute['actionName']];
		
				}

			return $aRoute;

		}	



}





// END Exceptions Class

/* End of file Exceptions.php */
/* Location: ./system/libraries/Exceptions.php */