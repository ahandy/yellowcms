<?php
class User extends Database {
	public function __construct() {
		parent::__construct();
		$this -> sessionObj = load_class('session', 'helpers');
	}

	public function save($id) {
		$this -> sessionObj -> set('yllw', $id);
	}

	public function loggedIn() {
		return ($this -> sessionObj -> get('yllw')) ? true : false;
	}

	public function logout() {
		return $this -> sessionObj -> clearSession();
	}
}