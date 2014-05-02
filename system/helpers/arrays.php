<?php
class Arrays extends Database {
	public function __construct() {
		parent::__construct();
	}

	public function fromDbIdField($table, $column, $conditions = FALSE) {
		if($conditions) $allRows = $this -> db -> getRow($conditions, $table, FALSE);
		else $allRows = $this -> db -> fetchAllRows($table);
		
		$rows = array();
		foreach($allRows as $row) {
			$rows[$row['id']] = $row[$column];
		}

		return $rows;
	}
}