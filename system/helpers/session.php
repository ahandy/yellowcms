<?php
class Session {
	public function __construct() {

		register_shutdown_function('session_write_close');
		
		ini_set('session.hash_function', 'sha512');
		ini_set('session.hash_bits_per_character', 6);
		ini_set('session.use_only_cookies', 1);

		$cookieParams = session_get_cookie_params(); 
		session_set_cookie_params(get_setting('session_maxlifetime'), $cookieParams["path"], $cookieParams["domain"], get_setting('session_https'), FALSE); 
		session_name(get_setting('session_name'));
		if(is_dir(get_setting('session_save_path'))) session_save_path(get_setting('session_save_path'));

		$this -> init();
		$this -> security = load_class('security', 'helpers');
		$this -> encryption = load_class('encryption', 'helpers');
	}

	public function init() {
		session_start();

		if($this -> checkIfObsolete()) {
			$this -> clearSession();
			session_start();
		}

		else {
			if(rand(1, 100) < 100) {
				$this -> regenerateSession();
			}
		}
	}
	public function addChecks() {
		$this -> set('ip', $_SERVER['REMOTE_ADDR'], FALSE);
		$this -> set('useragent', $_SERVER['HTTP_USER_AGENT'], FALSE);
	}

	public function returnSession() {
		$decryptedSession = array();
		foreach($_SESSION as $id => $key) {
			$decryptedSession[$this -> encryption -> decode($id)] = $this -> encryption -> decode($key);
		}
		return $decryptedSession;
	}

	public function processChecks() {
		if(!empty($_SESSION) && ($this -> get('ip', FALSE) != $_SERVER['REMOTE_ADDR'] || $this -> get('useragent', FALSE) != $_SERVER['HTTP_USER_AGENT'])) {
			$this -> clearSession();
			if(DEBUG_ALL) throw503("Session hijacking alert.");
		}
	}

	public function regenerateSession() {
		if(isset($_SESSION['obsolete']) && $_SESSION['obsolete']) return;

		$_SESSION['obsolete'] = true;
		$_SESSION['expiry'] = time() + 15;

		session_regenerate_id(true);

		$new_id = session_id();
		session_write_close();

		session_id($new_id);
		session_start();

		// Now we unset the obsolete and expiration values for the session we want to keep
		unset($_SESSION['obsolete']);
		unset($_SESSION['expiry']);
	}

	public function checkIfObsolete() {
		if(isset($_SESSION['obsolete']) && (!isset($_SESSION['expiry']) || $_SESSION['expiry'] < time())) return true;
		else return false;
	}

	public function set($name, $value, $check = TRUE) {

		// add hijacking checks
		if($check) $this -> addChecks();

		// Cleaning the values
		$name = $this -> security -> cleanInput($name);
		$value = $this -> security -> cleanInput($value);
		
		if(get_setting('session_encrypt')) {
			// Encrypting them
			$name = $this -> encryption -> encode($name);
			$value = $this -> encryption -> encode($value);
		}

		$_SESSION[$name] = $value;
		return true;
	}

	public function rem($name, $check = TRUE) {
		if($check) $this -> processChecks();

		if(get_setting('session_encrypt')) {
			if(!is_null($this -> get($name))) unset($_SESSION[$this -> encryption -> encode($name)]);
		}

		else if(isset($_SESSION[$name])) unset($_SESSION[$name]);
	}

	public function get($name, $check = TRUE) {
		// preventing hijacking
		if($check) $this -> processChecks();

		if(get_setting('session_encrypt')) {
			// encoding name
			$name = $this -> encryption -> encode($name);
		}

		if(!isset($_SESSION[$name])) return NULL;

		if(get_setting('session_encrypt')) return $this -> encryption -> decode($_SESSION[$name]);
		else return $_SESSION[$name];
	}

	public function clearSession() {
		$_SESSION = array();
		if(session_id()) session_destroy();
		return true;
	}
}