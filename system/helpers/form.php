<?php
class Form extends Database {
	public function parseFormError($errors) {
		if(is_array($errors) && count($errors) > 1) $errors = implode('<br />', $errors);
		else if(is_array($errors) && count($errors) == 1) {
			$errors = array_values($errors)[0];
		}
		else return '';
		return "<p class='error'>{$errors}</p>";
	}

	public function parseSuccess($successMessage) {
		return "<p class='success'>{$successMessage}</p>";
	}

	/**
	* Only allow access through POST requests
	* @param boolean|string 	$submit 	if string, the name of the submit input, if false, disregards the submit buton check
	**/
	public function onlyPost($submit = FALSE, $redirect = FALSE) {
		if($_SERVER['REQUEST_METHOD'] != 'POST' ||
			($submit !== FALSE && !isset($_POST[$submit]))) {
			if($redirect) redirect($redirect);
			else throw503("Invalid request.");
		}

		else return true;
	}

	/**
	* Returns the $_POST value of a variable name
	* @param string $name 	the name of the input
	* @return string|null 	string with the value if exists, null if not
	**/
	public function get($name) {
		if(isset($_POST[$name])) return $_POST[$name];
		else return NULL;
	}

	public function parseForm($form) {
		$this -> scripts = array();

		// Getting form information
		$form_information = $this -> returnFormInformation($form[0]);
		unset($form[0]);

		$this -> table = $form_information['table'];

		echo "<form action='{$form_information['action']}' method='{$form_information['method']}' class='{$form_information['class']}' enctype='{$form_information['enctype']}'>";

			foreach($form as $field) {
				echo $this -> parseField($field);
			}

		echo "<div class='clear'></div></form>";
		echo implode($this -> scripts);
	}

	/**
	* Function that returns proper form attributes
	* @param array $information  array containing the form information
	**/
	public function returnFormInformation($information) {
		if(!isset($information['action'])) { 
			$information['action'] = '';
		}

		if(!isset($information['method'])) {
			$information['method'] = 'post';
		}

		if(!isset($information['enctype'])) {
			$information['enctype'] = 'multipart/form-data';
		}

		return $information;
	}

	public function parseField($field) {
		// If no title was supplied, this is an invalid field.
		if(!isset($field['title'])) {
			return 'Invalid field.';
		}

		$field = $this -> cleanField($field);

		return "<div class='form-field'>" . $this -> returnField($field) . "</div>";
	}

	public function cleanField($field, $post = false) {
		// If no name was supplied, default to clean title
		if(!isset($field['name'])) {
			$field['name'] = strtolower($field['title']);
			$field['name'] = str_replace(" ", "_", $field['name']);
		}

		// If no placeholder, default to title
		if(!isset($field['placeholder'])) {
			$field['placeholder'] = $field['title'];
		}

		// If no field was supplied, default to regular text input
		if(!isset($field['type'])) {
			$field['type'] = 'text';
		}

		// If form has been submitted and the field existed, store the value
		if($this -> get($field['name']) !== NULL) {
			if($field['type'] == 'radio' || $field['type'] == 'checkbox') $field['checked'] = true;
			$field['value'] = $this -> get($field['name']);
		}
		
		// Empty value if none supplied, same for class and id
		if(!isset($field['value'])) {
			$field['value'] = '';
		}

		if(!isset($field['class'])) {
			$field['class'] = '';
		}

		if(isset($this -> error[$field['name']])) $field['class'] .= " error";

		if(!isset($field['id'])) {
			$field['id'] = '';
		}

		if(!isset($field['attr'])) {
			$field['attr'] = array();
		}

		return $field;
	}

	public function returnField($field) {

		// if it's a required field and HTML5 is enabled, use it
		$required = '';
		if(in_array('required', $field['attr'])) {
			$required = 'required';
		}

		// Check radio or checkbox if it's set
		$checked = '';
		if(isset($field['checked'])) {
			$checked = 'checked';
		}
		// general properties
		$properties = "name='{$field['name']}' id='{$field['id']}' class='{$field['class']}' {$required} {$checked}";
		$additional = '';

		// If the field is an input
		$input_types = array('text', 'radio', 'checkbox', 'hidden', 'submit', 'password', 'number');
		if(in_array($field['type'], $input_types)) {
			// If it's an e-mail, also use HTML5
			if($field['type'] == 'text' && in_array('email', $field['attr'])) $type = 'email';
			else $type = $field['type'];

			if($field['type'] == 'checkbox' || $field['type'] == 'radio') $prepend = $field['title'] . " ";
			else $prepend = "";

			if($field['type'] == 'text' && isset($field['values']) && !empty($field['values'])) {
				$this -> scripts[] = "<script>var {$field['name']} = [\"" . implode('","', $field['values']) . "\"]; </script>";
			}

			return "{$field['title']}<input type='{$type}' placeholder='{$field['placeholder']}' autocomplete='off' value='{$field['value']}' {$properties} />";
		}

		// If the field is a textarea
		else if($field['type'] == 'textarea') {
			return "{$field['title']}<textarea placeholder='{$field['placeholder']}' {$properties}>{$field['value']}</textarea>";
		}

		// If it is a dropdown
		else if($field['type'] == 'dropdown') {
			if(!isset($field['values'])) return 'Invalid dropdown supplied.'; // if no values were supplied

			$select = $field['title'] . " <select {$properties}>";
			foreach($field['values'] as $k => $value) {
				$selected = '';
				if($k == $field['value']) $selected = 'selected';
				$select .= "<option value='{$k}' {$selected}>{$value}</option>"; // each option has the index as value
			}
			$select .= "</select>";

			return $select;
		}

		// If it's a file
		else if($field['type'] == 'file') {
			return "<div class='fallback'><input type='file' {$properties} /></div>";
		}
	}

	public function validateForm($form, $post, $file = false, $table = false) {
		unset($form[0]); // removing form information

		$errors = array();
		foreach($form as $field) {
			$error = $this -> validateField($field, $post, $file, $table);
			if($error) {
				$errors[] = $error;
			}
		}
		return $errors;
	}

	public function validateField($field, $post, $file = false, $table = false) {
		$error = false;

		$field = $this -> cleanField($field, $post); // clean field

		// If it doesn't exist in post, add it as false
		if(!isset($post[$field['name']])) {
			$post[$field['name']] = false;
		}

		// If it's a file field, get the val
		if($field['type'] == 'file') {
			$postval = $file[$field['name']];
		}
		// Else if it's a regular post field
		else $postval = $post[$field['name']];

		$attributes = $field['attr'];

		// If the field is a required field
		if(in_array('required', $attributes)) {
			$attr_error = false;
			
			if($postval === false) $attr_error = true;

			// If it's a text field and it's empty
			if($field['type'] == 'text' && empty($postval)) {
				$attr_error = true; 
			} 

			if($field['type'] == 'radio' && $postval === false) {
				$attr_error = true;
			}

			// If it's a file upload field and no file was uploaded
			if($field['type'] == 'file' && 
				(!file_exists($postval['tmp_name']) || !is_uploaded_file($postval['tmp_name']))) {
				$attr_error = true;
			}

			if($attr_error) $error = $this -> returnError('required', $field['title']);
		}

		// If the field is a dropdown
		if(in_array('from_values', $attributes)) {
			if(!array_key_exists($postval, $field['values'])) {
				$error = $this -> returnError('from_values', $field['title']);
			}
		}

		if(in_array('number', $attributes)) {
			if(!is_numeric($postval) && !empty($postval)) {
				$error = $this -> returnError('number', $field['title']);
			}
		}

		if(in_array('unique', $attributes)) {
			if($this -> db -> checkRow(array($field['name'] => $postval), $table)){
				$error = $this -> returnError('unique', $field['title']);
			}
		}

		if(in_array('email', $attributes)) {
			if(!filter_var($postval, FILTER_VALIDATE_EMAIL)){
				$error = $this -> returnError('email', $field['title']);
			}
		}

		if($error) {
			$this -> error[$field['name']] = true;
		}

		return $error;
	}

	public function returnError($attr, $title) {
		$errors = array(
			'required' => "{$title} is a required field.",
			'number' => "{$title} is a numeric field.",
			'from_values' => "{$title} requires a value from the dropdown.",
			'unique' => "{$title} already exists.",
			'email' => "{$title} should be a valid email."
		);

		return $errors[$attr];
	}
}
?>