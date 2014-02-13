<?php

class user_session extends component {

	static public $_sessionStarted = false;

	static public function startSession() {
		if (User::$_sessionStarted) {
			return;
		}
		User::$_sessionStarted = true;

		/*Mise à jour pour l'iphone*/

		if ($_CONF['WEBSERVICE']['status'] == true) {
			if (true == isset($_COOKIE['jiwa_iphone_authToken'])) {
				session_id($_COOKIE['jiwa_iphone_authToken']);
			} else if (isset($_COOKIE["authToken"])) {
				if ($_COOKIE["authToken"]) {

					session_id($_COOKIE["authToken"]);
				}
			} else if (isset($_REQUEST["authToken"])) {
				if ($_REQUEST["authToken"]) {

					session_id($_REQUEST["authToken"]);
				}
			}

		}

		@session_start();

	}

	/**
	 * User::setCurrent()
	 *
	 * @return
	 */
	public function setCurrent() {
		global $_CONF;
		/*if (isset($_CONF['WEBSERVICE']['status']) && $_CONF['WEBSERVICE']['status'] == true)
		 {
		 global $bWsGrantedAccessWrite;
		 $aParam = $_POST+$_GET;

		 if (true == isset ($aParam['connectId']) && $bWsGrantedAccessWrite == "W")
		 {
		 $sReq = "select users.userId, users.userName, users.age, users.countryId, users.sec, users.reliability, users.email from users where users.userId = %s";
		 $db = DB::get("FRONT");
		 $user = $this->fetchRes($db->queryAll($sReq, $aParam['connectId']));

		 User::startSession();
		 $_SESSION['user'] = array (
		 'userId'=>$user->get("userId"),
		 'userName'=>$user->get("userName"),
		 'age'=>$user->get("age"),
		 'countryId'=>$user->get("countryId"),
		 'sex'=>$user->get("sex"),
		 'reliability'=>$user->get("reliability"),
		 'email'=>$user->get("email")
		 );

		 } else
		 {
		 $_SESSION['user'] = array (
		 'userId'=>0,
		 'userName'=>"",
		 'age'=>0,
		 'countryId'=>0,
		 'sex'=>"m",
		 'reliability'=>"f",
		 'email'=>"none@none.com"
		 );
		 }
		 } else
		 {*/
		User::startSession();
		$_SESSION['user'] = array('userId' => $this -> get("userId"), 'userName' => $this -> get("userName"), 'age' => $this -> get("age"), 'countryId' => $this -> get("countryId"), 'sex' => $this -> get("sex"), 'reliability' => $this -> get("reliability"), 'email' => $this -> get("email"));

		$db = DB::get("FRONT");

		if (isset($_COOKIE['ys-languageCode']) && preg_match('/en/', $_COOKIE['ys-languageCode'])) {
			$language = 0;
		} else {
			$language = 1;
		}

		$db -> queryVoid('UPDATE users SET dateLastLogin=CURRENT_DATE, languageId=%s WHERE userId=%s', $language, $this -> get("userId"));

		return session_id();

		//}
	}

	/**
	 * User::current()
	 *
	 * @param bool $dieIfNoUser
	 * @return
	 */
	static public function current() {
		global $_CONF;

		User::startSession();
		return (isset($_SESSION['user']['userId']) ? new User($_SESSION['user']) : null);
		/*
		 if ($_CONF['WEBSERVICE']['status'] == true)
		 {
		 $user = new User();
		 return $user;
		 } else
		 {
		 User::startSession();
		 return ( isset ($_SESSION['user'])? new User($_SESSION['user']):null);
		 }
		 */
	}

	/**
	 * User::currentId()
	 *
	 * @param bool $mandatory
	 * @return
	 */
	static public function currentId() {
		$user = User::current();
		return ($user ? $user -> get('userId') : null);
	}

	/**
	 * User::currentName()
	 *
	 * @return
	 */
	static public function currentName() {
		$user = User::current();
		return ($user ? $user -> get('userName') : null);
	}

	/**
	 * User::currentCache()
	 *
	 * @return
	 */
	static public function currentCache() {
		// TODO: cache!
		return json_encode(User::current() -> objView());
	}

	/**
	 * Check if a user is authentified and returns its userId
	 * @return
	 */
	public function checkAuth() {
		$resp = new Response();

		$userId = User::currentId();

		if ($userId > 0) {

			$resp["success"] = true;
			$db = DB::get("FRONT");
			$this -> controller -> param -> userId = $userId;
			$user = $this -> fetchRes($db -> queryAll('SELECT * FROM users WHERE userId = %s ', $userId));
			$resp["authToken"] = $user -> setCurrent();

		} else {

			$userId = $this -> loginWithCookie();

			if ($userId) {

				$db = DB::get("FRONT");
				$this -> controller -> param -> userId = $userId;
				$user = $this -> fetchRes($db -> queryAll('SELECT * FROM users WHERE userId = %s ', $userId));
				$resp["authToken"] = $user -> setCurrent();

				$resp["success"] = true;

				$this -> controller -> mine = true;
			} else {
				return $resp -> addFieldError("session", "NO_SESSION");
			}
		}

		return $resp;
	}

	/**
	 * User::reconnect()
	 *
	 * @param mixed $p
	 * @return
	 */
	public function reconnect($param) {

		$resp = new Response();

		$userId = User::currentId();

		if ($userId > 0) {

			$db = DB::get("FRONT");

			$this -> _fetchId($userId);

			$this -> controller -> param -> userId = $this -> get("userId");

			$this -> controller -> param -> inc = array('playlists', 'favoritePlaylists', 'angels', 'nbmails');

			$resp = $this -> controller -> get('details');

			$resp["success"] = true;

			return $resp;

		} else {
			return $resp -> addFieldError('email', 'INVALID_LOGIN');
		}
	}

	public function session() {
		$result = array();
		@session_start();
		$result['session'] = $_SESSION;
		$result['cookie'] = $_COOKIE;
		return $result;

		$_SESSION['SERVER'] = $_SERVER;

		if (!empty($_SESSION['HTTP_HOST']) && !empty($_SESSION['REQUEST_URI']) && !empty($ip)) {

			require_once 'libs/Blues/Cache/FSCache.php';

			$fsCache = new FSCache();

			$address = 'http://' . $_SESSION['HTTP_HOST'] . $_SESSION['REQUEST_URI'] . "/" . $ip;
			$key = "hosts/" . md5($address);

			$fsCache -> filePutContents($key, 'ok', 24 * 60 * 60);

		}

	}

	//GESTION DES PAYS : $_SESSION['user']['loginCountry'] => ICI EST STOCKER LE PAYS DE LOGIN DE LAPPLICATION

}
