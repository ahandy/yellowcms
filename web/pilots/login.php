<?php
class Login extends Controller {
	public function __construct() {
		parent::__construct();
		$this -> callFunction = 'show';

		$this -> userObj = load_class('user', 'helpers');
		if($this -> userObj -> loggedin()) redirect(HTTP);
	}

	public function show() {
		$this -> view -> load('login', FALSE, FALSE);
	}

	public function process() {
		$loginModel = loadModel('login');
		$formObj = load_class('form', 'helpers');

		$combo = $loginModel -> checkCombo($formObj -> get('username'), $formObj -> get('password'));

		if($combo) {
			$this -> userObj -> save($formObj -> get('username'));
			if($formObj -> get('js')) echo 'success';
			else redirect(HTTP);
		}

		else {
			if($formObj -> get('js')) echo 'fail';
			else $this -> show();
		}
	}
}