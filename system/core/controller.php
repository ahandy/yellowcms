<?php
class Controller {
	public static $controller;

	public function __construct() {
		self::$controller =& $this;
		
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			if(get_setting('csrf_checker')) {
				$CSRF = load_class('csrf', 'helpers');
				$CSRF -> checkForCSRF();
			}

			if(get_setting('honeypot')) {
				$honeypot = load_class('honeypot', 'helpers');
				$honeypotCheck = $honeypot -> checkForHoneypot();
				if($honeypotCheck) redirect(HTTP);
			}
		}
		
		foreach(returnLoadedClasses() as $class => $directory) {
			$this -> $class = load_class($class, $directory);
		}
	}

	public static function returnController() {
		return self::$controller;
	}
}