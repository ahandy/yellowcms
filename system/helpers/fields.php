<?php
class Fields extends Database {
	public function __construct() {
		parent::__construct();
		$this -> tableObj = load_class('table', 'helpers');
	}

	public function returnFields($table) {
		return $this -> db -> getRow(array('table_name' => $table), 'tables', FALSE, FALSE, 'position', 'DESC');
	}

	public function returnField($table, $field, $conditions = FALSE) {
		if(!$conditions) {
			$rows = $this -> db -> fetchAllRows($table);
		}

		else {
			$conditions = explode("\n", $conditions);
			$getCondition = array();
			$query = array();
			foreach($conditions as $condition) {
				$conditionsAr = explode("=", $condition);
				$column = trim($conditionsAr[0]);
				$value = trim($conditionsAr[1]);

				if(isset($conditionsAr[2])) {
					$value2 = trim($conditionsAr[2]);
					$query[] = "(`{$column}` = '{$value}' OR `{$column}` = '{$value2}')";
				}

				else {
					$query[] = "`{$column}` = '{$value}'";
				}
			}

			$rows = $this -> db -> getRow(implode(" AND ", $query), $table, FALSE);
		}

		$finalRows = array();
		foreach($rows as $row) {
			$finalRows[$row['id']] = $row[$field];
		}
		return $finalRows;
	}

	public function updateRow($table, $id, $post) {
		if(!is_numeric($id) || !$this -> tableObj -> checkRowId($id, $table)) return;

		foreach($post as $key => $val) {
			$column = $this -> tableObj -> checkColumnTable($key, $table);
			if(!$column) {
				unset($post[$key]);
			}
			else {
				$row = $this -> db -> getRow(array('column_name' => $key, 'table_name' => $table), 'tables');

				if($row['field_type'] == 'checkbox') {
					$post[$key] = 1;
				}

				else if($row['field_type'] == 'foreign' && $row['autocomplete'] == 1) {
					if(!$this -> db -> checkRow(array($row['foreign_column'] => $post[$key]), $row['foreign_table'])) {
						$this -> db -> insert(array($row['foreign_column'] => $post[$key]), $row['foreign_table']);
						$post[$key] = $this -> db -> lastId();
					}

					else {
						$post[$key] = $this -> db -> getValue('id', $row['foreign_table'], array($row['foreign_column'] => $post[$key]));
					}
				}
			}
		}

		$this -> db -> updateValue($post, array('id' => $id), $table);
	}

	public function updateField($value, $id, $column, $table) {
		return $this -> db -> updateValue(array($column => $value), array('id' => $id), $table);
	}

	public function editable($column, $table) {
		return $this -> db -> getValue('editable', 'tables', array('column_name' => $column, 'table_name' => $table));
	}

	public function returnRow($table, $id) {
		return $this -> db -> getRow(array('id' => $id), $table);
	}
 	 public function add($table, $field, $files) {
		if(!empty($files)) {
			if(!is_dir(WEB_PATH . "public/uploads/{$table}")) {
				mkdir(WEB_PATH . "public/uploads/{$table}");
			}
	
			if(file_exists(WEB_PATH . "public/uploads/{$table}/" . $files['file']['name'])) {
				$files['file']['name'] = time() . "_" . $files['file']['name'];
			}

			move_uploaded_file($files['file']['tmp_name'], WEB_PATH . "public/uploads/{$table}/" . $files['file']['name']);

			$field['file'] = $files['file']['name'];
		}
		foreach($field as $name => $value) {
			$row = $this -> db -> getRow(array('column_name' => $name, 'table_name' => $table), 'tables');
			if(!$row || empty($row)) {
				unset($field[$name]);
				continue;
			}
			else {
				if($row['field_type'] == 'checkbox') {
					$field[$name] = 1;
				}

				else if($row['field_type'] == 'foreign' && $row['autocomplete'] == 1) {
					if(!$this -> db -> checkRow(array($row['foreign_column'] => $field[$name]), $row['foreign_table'])) {
						$this -> db -> insert(array($row['foreign_column'] => $field[$name]), $row['foreign_table']);
						$field[$name] = $this -> db -> lastId();
					}

					else {
						$field[$name] = $this -> db -> getValue('id', $row['foreign_table'], array($row['foreign_column'] => $field[$name]));
					}
				}

				else if($row['field_type'] == 'password') {
					$encObj = load_class('encryption', 'helpers');
					$field[$name] = $encObj -> hashPassword($field[$name]);
				}
				else if($row['field_type'] == 'number' && empty($field[$name])) {
					$field[$name] = 0;
				}
			}
		}
		$this -> db -> insert($field, $table);
	}

	public function checkForFileUpload($table) {
		return $this -> db -> checkRow(array('table_name' => $table, 'field_type' => 'file'), 'tables');
	}
}