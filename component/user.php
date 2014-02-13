<?php

class user extends component{
    

    //openssl_random_pseudo_bytes

	public function passwordEncrypt(){
		//Use crypt() with a Blowfish cipher salt, or thousands of rounds of a random salt combined with the sha1() h

		$salt = '$2a$08$' . fCryptography::randomString(22, 'alphanumeric');
$hash = crypt($password, $salt);
$hashed_password = $salt . '||' . $hash;
	}

	public function hashPassword($password)
	{
		$salt = self::randomString(10);

		return self::hashWithSalt($password, $salt);
	}




	/**
	 * Performs a large iteration of hashing a string with a salt
	 *
	 * @param  string $source  The string to hash
	 * @param  string $salt    The salt for the hash
	 * @return string  An 80 character string of the Flourish fingerprint, salt and hashed password
	 */
	private function hashWithSalt($source, $salt)
	{
		$sha1 = sha1($salt . $source);
		for ($i = 0; $i < 1000; $i++) {
			$sha1 = sha1($sha1 . (($i % 2 == 0) ? $source : $salt));
		}

		return 'fCryptography::password_hash#' . $salt . '#' . $sha1;
	}

	public function randomString($length, $type='alphanumeric')
	{
		if ($length < 1) {
			throw new fProgrammerException(
				'The length specified, %1$s, is less than the minimum of %2$s',
				$length,
				1
			);
		}

		switch ($type) {
			case 'base64':
				$alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789+/';
				break;

			case 'alphanumeric':
				$alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
				break;

			case 'base56':
				$alphabet = 'abcdefghijkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ23456789';
				break;

			case 'alpha':
				$alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
				break;

			case 'base36':
				$alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
				break;

			case 'hexadecimal':
				$alphabet = 'abcdef0123456789';
				break;

			case 'numeric':
				$alphabet = '0123456789';
				break;

			default:
				$alphabet = $type;
		}

		$alphabet_length = strlen($alphabet);
		$output = '';

		for ($i = 0; $i < $length; $i++) {
			$output .= $alphabet[self::random(0, $alphabet_length-1)];
		}

		return $output;
	}


    public function authentificate (){
    	
		$this->auth  = $this->core->component('user_authentification');
		
    }
	
	
	 public function session(){
    	
		 if (!isset($this -> _session)) {

       		$this->_session  = $this->core->component('user_session');
		
			$this->_session->start();
        }

        return $this->_session;
		
    }
	 
	 
	public function account(){
	
		if (!isset($this -> _account)) {

       		$user = $this->core->model('user');
			
			if($this->isConnected()){
		
				$this->_account  = $user->getAccount();
				
			}else if ($this->authentificate()){
				
				$this->_account  = $user->getAccount();
			}
        }
		
		
		if (isset($this -> _account)) {
        		
        	return $this->_account;
			
		}else{
			
			return false;
		}
		
    }
	
	
	public function isConnected(){
		
		return $this->auth->is_authentificate(); 
	
	}


	public function getLocation(){
		
		return $this->auth->is_authentificate(); 
	
	}
	
    
    
    
}
