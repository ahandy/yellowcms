<?php
class Database {
	public function __construct() {
		$this -> db = load_class('db', 'core');
	}
}