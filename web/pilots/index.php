<?php
class Index extends Controller {
	public function __construct() {
		parent::__construct();
		
		$userObj = load_class('user', 'helpers');
		if(!$userObj -> loggedIn()) redirect(HTTP . 'login');

		$this -> tableObj = load_class("table", "helpers");
		$this -> view -> tables = $this -> tableObj -> returnTables();
		
		$this -> callFunction = 'show';
	}

	public function show() {
		$this -> view -> load('index');
	}
}