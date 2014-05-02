<?php
class Table extends Database {
	public function __construct() {
		parent::__construct();
	}

	public function checkTable($table) {
		return $this -> db -> checkTable($table);
	}

	public function returnColumns($table, $full = false, $all = true) {
		if(!$all) $columnRows = $this -> db -> getRow(array('table_name' => $table, 'display' => 1), 'tables', FALSE);
		else $columnRows = $this -> db -> getRow(array('table_name' => $table), 'tables', FALSE);
		
		$columns = array();
		foreach($columnRows as $row) {
			if(!$full) $columns[] = array('column_name' => $row['column_name'], 'column_type' => $row['field_type']);
			else $columns[] = $row;
		}
		return $columns;
	}

	public function returnAllRows($table) {
		$columns = $this -> returnColumns($table, TRUE, false);

		$rows = $this -> db -> fetchAllRows($table);

		$cleanRows = array();
		$allImages = false;
		foreach($columns as $parent_column) {
			if($parent_column['field_type'] == 'file' && $parent_column['all_images_in_row'] == 1) {
				$allImages = true;

				foreach($columns as $column) {
					if($column['field_type'] == 'foreign') {
						$foreignColumn = $column['column_name'];
					}
					if($column['field_type'] != 'file') $allColumns[] = $column['column_name'];
				}

				$finalValue = array();
				foreach($rows as $k => $row) {

					$fieldName = array();
					foreach($allColumns as $column) {
						$fieldName[$row[$foreignColumn]][] = $row[$column];
					}

					$fieldName = implode("_", $fieldName[$row[$foreignColumn]]);
					if(!isset($finalValue[$fieldName]['file'])) $finalValue[$fieldName][$parent_column['column_name']] = "";
					foreach($columns as $column) {
						if($column['field_type'] == 'file') {
							$finalValue[$fieldName][$column['column_name']] .= "<img src='" . UPLOADS_PATH . "{$table}/{$row['file']}' width='50' height='50' alt='Image' />";
						}

						else if($column['field_type'] == 'foreign') {
							$finalValue[$fieldName][$column['column_name']] = $this -> db -> getValue($column['foreign_column'], $column['foreign_table'], array('id' => $row[$column['column_name']]));
						}

						else {
							$finalValue[$fieldName][$column['column_name']] = $row[$column['column_name']];
						}
					}
				}

				foreach($finalValue as $k => $val) {
					$cleanRows[$k] = $val;
				}
			}
		}

		if(!$allImages) {
			foreach($rows as $k => $row) {
				foreach($columns as $column) {
					if($column['field_type'] != 'foreign') {
						if($column['field_type'] == 'number') {
							$row[$column['column_name']] = number_format($row[$column['column_name']]);
						}

						$cleanRows[$row['id']][$column['column_name']] = $row[$column['column_name']];
					}
					else {
						$cleanRows[$row['id']][$column['column_name']] = $this -> db -> getValue($column['foreign_column'], $column['foreign_table'], array('id' => $row[$column['column_name']]));
					}
				}
			}
		}

		return $cleanRows;
	}

	public function returnTables() {
		$tables = $this -> db -> returnTables();
		$tablesAr = array();

		$text = load_class('text', 'helpers');
		foreach($tables as $k => $table) {
			$tablesAr[$k] = $text -> returnDisplay($table['Tables_in_cms']);
		}

		return $tablesAr;
	}

	public function checkRowId($id, $table) {
		return $this -> db -> checkRow(array('id' => $id), $table);
	}

	public function checkColumnTable($column, $table) {
		foreach($this -> returnColumns($table) as $columnAr) {
			if(in_array($column, $columnAr)) return $columnAr['column_type'];
		}
		return false;
	}
}