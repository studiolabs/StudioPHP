<?php

class user_account extends component{
    
    public $account = array();
    
	public $opath_dir;
	
	private function _init(){
		$this->opath_dir = $this-> application->path.'lib/Opath/';		
	}
    
	function  _configure($param){
		
		$auth = $this->core->config->account('authenfication');

		if(isset($auth[$param])){
			
			$this->config = array_merge(array(
			'host' => $this-> application->protocole.$this-> application->host,
			'path' => '/',
			'callback_url' => $this-> application->protocole.$param.'.'.$this-> application->host.'/callback',
			'callback_transport' => 'session',
			'debug' => false,
			
			/**
		 	* Security settings
		 	*/
			'security_salt' =>$this->core->config->security('salt'),
			'security_iteration' => 300,
			'security_timeout' => '2 minutes'
			
		), $auth[$param]);
		
		/**
		 * Environment variables, including config
		 * Used mainly as accessors
		 */
		 
		$this->env = array_merge(array(
			'request_uri' => $_SERVER['REQUEST_URI'],
			'complete_path' => $this->config['host'].$this->config['path'],
			'lib_dir' => $this->opath_dir,
			'strategy_dir' => $this->opath_dir.'/Strategy/'
		), $this->config);
		
		if (!class_exists('OpauthStrategy')){
			$this->core->load->loadComponent('OpauthStrategy','adapter');
		}
	
		
		
		}
		
		
				
	}
	
	
	
	/**
	 * Run Opauth:
	 * Parses request URI and perform defined authentication actions based based on it.
	 */
	public function authentificate($strategy,$option,$action='request'){
			
			$this->_configure($strategy);
				$name = $strategy;
				$class = ucfirst(strategy).'Strategy';		

							
				require $this->opath_dir.'/Strategy/'.ucfirst(strategy).'/'.$class.'.php';
				$this->Strategy = new $class($strategy, $option);
				
				
				$this->Strategy->callAction($action);
		
	}
	
	
	
		
	/**
	 * Validate $auth response
	 * Accepts either function call or HTTP-based call
	 * 
	 * @param string $input = sha1(print_r($auth, true))
	 * @param string $timestamp = $_REQUEST['timestamp'])
	 * @param string $signature = $_REQUEST['signature']
	 * @param string $reason Sets reason for failure if validation fails
	 * @return boolean true: valid; false: not valid.
	 */
	public function validate($input = null, $timestamp = null, $signature = null, &$reason = null){
		$functionCall = true;
		if (!empty($_REQUEST['input']) && !empty($_REQUEST['timestamp']) && !empty($_REQUEST['signature'])){
			$functionCall = false;
			$provider = $_REQUEST['input'];
			$timestamp = $_REQUEST['timestamp'];
			$signature = $_REQUEST['signature'];
		}
		
		$timestamp_int = strtotime($timestamp);
		if ($timestamp_int < strtotime('-'.$this->env['security_timeout']) || $timestamp_int > time()){
			$reason = "Auth response expired";
			return false;
		}
		
		$hash = OpauthStrategy::hash($input, $timestamp, $this->env['security_iteration'], $this->env['security_salt']);
		
		if (strcasecmp($hash, $signature) !== 0){
			$reason = "Signature does not validate";
			return false;
		}
		
		return true;
	}
	
	/**
	 * Callback: prints out $auth values, and acts as a guide on Opauth security
	 * Application should redirect callback URL to application-side.
	 * Refer to example/callback.php on how to handle auth callback.
	 */
	public function callback($strategy){
		
		$this->_configure($strategy);
			
			
		$response = "<strong>Note: </strong>Application should set callback URL to application-side for further specific authentication process.\n<br>";
		
		switch($this->env['callback_transport']){
			case 'session':
                if (!isset($_SESSION)){
				    session_start();
			    }
				$response = $_SESSION['opauth'];
				unset($_SESSION['opauth']);
				break;
			case 'post':
				$response = unserialize(base64_decode( $_POST['opauth'] ));
				break;
			case 'get':
				$response = unserialize(base64_decode( $_GET['opauth'] ));
				break;
			default:
				echo '<strong style="color: red;">Error: </strong>Unsupported callback_transport.'."<br>\n";
				break;
		}
		
		/**
		 * Check if it's an error callback
		 */
		if (array_key_exists('error', $response)){
			echo '<strong style="color: red;">Authentication error: </strong> Opauth returns error auth response.'."<br>\n";
		}

		/**
		 * No it isn't. Proceed with auth validation
		 */
		else{
			if (empty($response['auth']) || empty($response['timestamp']) || empty($response['signature']) || empty($response['auth']['provider']) || empty($response['auth']['uid'])){
				echo '<strong style="color: red;">Invalid auth response: </strong>Missing key auth response components.'."<br>\n";
			}
			elseif (!$this->validate(sha1(print_r($response['auth'], true)), $response['timestamp'], $response['signature'], $reason)){
				echo '<strong style="color: red;">Invalid auth response: </strong>'.$reason.".<br>\n";
			}
			else{
				echo '<strong style="color: green;">OK: </strong>Auth response is validated.'."<br>\n";
			}
		}		
		
		return $response;
	
	}
	
	
	/**
	 * Prints out variable with <pre> tags
	 * Silence if Opauth is not in debug mode
	 * 
	 * @param mixed $var Object or variable to be printed
	 */	
	public function debug($var){
		////$this->core->log($var);
	}
	
    
}



