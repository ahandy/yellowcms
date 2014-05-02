<?php
class Rows extends Controller {
	public function __construct() {
		parent::__construct();
		
		$userObj = load_class('user', 'helpers');
		if(!$userObj -> loggedIn()) redirect(HTTP . 'login');
		
		$this -> tableObj = load_class("table", "helpers");
		$this -> fieldsObj = load_class("fields", "helpers");
		$this -> formsObj = load_class("form", "helpers");
	}

	public function add($table = FALSE, $success = FALSE) {
		if($success) $this -> view -> success = true;
		
		$this -> view -> form = $this -> returnForm($table, 'add');

		if(isset($_POST['submit'])) {
			if($this -> process($table, $_POST, $_FILES)) {
				redirect(HTTP . 'rows/add/' . $table . '/success');
			}
		}

		$this -> view -> tables = $this -> tableObj -> returnTables();
		$this -> view -> table = $table;
		$this -> view -> load('rows');
	}

	public function returnForm($table, $action, $id = FALSE) {
		if(!$table || !$this -> tableObj -> checkTable($table)) throw503("Please supply a valid table name.");
		$fields = $this -> fieldsObj -> returnFields($table);
		$formAr = array();

		$dropzone = ($this -> fieldsObj -> checkForFileUpload($table) && $action == 'add') ? 'dropzone': '';

		if($action == 'add') {
			$action = HTTP . 'rows/add/' . $table;
		}

		else {
			$action = HTTP . 'rows/edit/' . $table . '/' . $id;
		}
		$formAr[] =	array('table' => $table, 'action' =>  $action, 'class' => $dropzone);

		$text = load_class('text', 'helpers');
		foreach($fields as $field) {
			$conditions = array();
			if($field['required'] == 1) $conditions[] = 'required';
			if($field['field_type'] == 'number') $conditions[] = 'number';
			
			$title = $text -> returnDisplay($field['column_name']);

			$values = array();
			$field['class'] = '';
			$field['value'] = '';

			if($field['field_type'] == 'text' && $field['unique'] == 1) {
				$conditions[] = 'unique';
			}

			else if($field['field_type'] == 'email') {
				$field['field_type'] = 'text';
				$conditions[] = 'email';
			}
			else if($field['field_type'] == 'dropdown') {
				$dropdown_values = str_replace("\"", "", $field['dropdown_values']);
				$dropdown_values = explode(",", $dropdown_values);

				foreach($dropdown_values as $val) {
					$values[$val] = $val;
				}
				$conditions[] = 'from_values';
			}			

			else if($field['field_type'] == 'boolean_dropdown') {
				$dropdown_values = str_replace("\"", "", $field['dropdown_values']);
				$dropdown_values = explode(",", $dropdown_values);
				$i = 1;
				foreach($dropdown_values as $val) {
					$values[$i] = $val;
					$i--;
				}

				$field['value'] = 1;

				$field['field_type'] = 'dropdown';
				$conditions[] = 'from_values';
			}
			
			else if($field['field_type'] == 'foreign') {
				if($field['autocomplete'] == 1) {
					$field['field_type'] = 'text';
					$field['class'] = 'autocomplete';
					$conditions[] = 'autocomplete';
				}

				else {
					$field['field_type'] = 'dropdown';
					$conditions[] = 'from_values';
				}


				$values = $this -> fieldsObj -> returnField($field['foreign_table'], $field['foreign_column'], $field['foreign_condition']);
			}

			else if($field['field_type'] == 'mobile') {
				$field['field_type'] = 'number';
			}

			else if($field['field_type'] == 'file') {
				continue;
			}

			$formAr[] = array('type' => $field['field_type'], 'title' => $title['display'], 'attr' => $conditions, 'values' => $values, 'class' => $field['class'], 'id' => $field['id'], 'value' => $field['value']);
		}

		$formAr[] = array('type' => 'submit', 'title' => 'Submit', 'value' => 'Submit');
		return $formAr;
	}

	public function edit($table, $id) {
		$form = $this -> returnForm($table, 'edit', $id);
		$row = $this -> fieldsObj -> returnRow($table, $id);
		foreach($form as $k => &$field) {
			if($k == 0) continue;
			if(!isset($field['name'])) {
				$field['name'] = strtolower($field['title']);
				$field['name'] = str_replace(" ", "_", $field['name']);
			}

			if(!$this -> fieldsObj -> editable($field['name'], $table) && isset($row[$field['name']])) {
				unset($form[$k]);
				continue;
			}

			if($field['type'] == 'text' && !empty($field['values'])) {
				foreach($field['values'] as $k => $value) {
					if($row[$field['name']] == $k) {
						$field['value'] = $value;
					}
				}
			}
			else $field['value'] = isset($row[$field['name']]) ? $row[$field['name']] : $field['value'];
		}
		$this -> view -> form = $form;

		if(isset($_POST['submit'])) $this -> updateRow($table, $id, $_POST);

		$this -> view -> tables = $this -> tableObj -> returnTables();
		$this -> view -> table = $table;
		$this -> view -> load('rows');
	}

	private function process($table, $post, $file) {
		$formObj = load_class('form', 'helpers');
		$formObj -> onlyPost();

		$this -> view -> formErrors = $formObj -> validateForm($this -> view -> form, $post, $file, $table);
		if(empty($this -> view -> formErrors)) {
			$this -> fieldsObj -> add($table, $post, $file);
			return true;
		}
		else { return false; }
	}

	public function update($table, $id, $column) {
		$value = $this -> formsObj -> get('value');
		if(!is_numeric($id) || !$this -> tableObj -> checkRowId($id, $table) || !$this -> tableObj -> checkColumnTable($column, $table)) {
			echo "Error";
			return false;
		}

		$this -> fieldsObj -> updateField($value, $id, $column, $table);

		echo $value;
	}

	public function updateRow($table, $id, $post) {
		$this -> fieldsObj -> updateRow($table, $id, $post);
	}
}
?>