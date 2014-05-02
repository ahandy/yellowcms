<?php
class Tables extends Controller {
	public function __construct() {
		parent::__construct();
		$userObj = load_class('user', 'helpers');
		if(!$userObj -> loggedIn()) redirect(HTTP . 'login');
		
		$this -> tableObj = load_class("table", "helpers");
	}

	public function show($table = FALSE) {
		if(!$table || !$this -> tableObj -> checkTable($table)) throw503("Please supply a valid table name.");

		$this -> view -> columns = $this -> tableObj -> returnColumns($table, false, false);
		$this -> view -> columns[] = array('column_name' => 'Options', 'column_type' => 'tools');

		$this -> view -> rows = $this -> tableObj -> returnAllRows($table);

		$this -> view -> tables = $this -> tableObj -> returnTables();
		$this -> view -> table = $table;
		
		$this -> view -> load("tables");
	}
}
?>