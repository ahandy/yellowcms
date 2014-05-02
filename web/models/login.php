<?php
class Login_Model extends Database {
	public function __construct() {
		parent::__construct();
	}

	public function checkCombo($username, $pass) {
		$encObj = load_class('encryption', 'helpers');
		$pass = $encObj -> hashPassword($pass);

		return $this -> db -> checkRow(array('username' => $username, 'password' => $pass, 'active' => 1), 'users');
	}
}