<?php

/*
*---------------------------------------------------------
* Making sure there was no direct access to the script
*---------------------------------------------------------
*/ 
defined('SYSTEM_PATH') OR exit('No direct access to the script is allowed');


/*
*---------------------------------------------------------
* Routes class that gets the correct file for each URL
*---------------------------------------------------------
*/ 
class Routes {

	// Initializing the router
	public function __construct() {
		require_once CONFIG_PATH . 'routes.php';
		global $routes;
		$this -> routes = $routes;
		$this -> iniRouter();
	}

	public function iniRouter() {
		$this -> checkForValidIndexPage();

		// If there was a direct match with a route
		if($this -> checkForRouteMatch()) {
			$this -> class_file_name = $this -> getRouteFileName();
			$this -> class_name = $this -> getRouteClassName();
			$this -> method_name = $this -> getRouteMethodName();
			$this -> method_parameters = $this -> getRouteMethodParameters();
		}

		// If there isn't
		else {
			$this -> url_params_array = $this -> parseUrlIntoArray();

			$class_name = $this -> getClassName($this -> url_params_array);
			$class_file_name = $this -> getFileName($class_name);

			// If the class file is a valid class file
			if($this -> checkClassFile($class_file_name)) {
				$this -> class_file_name = $class_file_name;
				$this -> class_name = $class_name;

				$this -> loadParams();
			}

			// If it isn't, load the default landing page
			else {
				if(DEBUG_ALL) throw503("The class file {$class_file_name} was not found in " . CONTROLLERS_PATH);
				$this -> class_file_name = $this -> index_file_name;
				$this -> class_name = $this -> index_page;
			}

			unset($this -> url_params_array);
		}
	}

	public function getRouteFileName() {
		return $this -> validRoute['controller'];
	}

	public function getRouteClassName() {
		return $this -> validRoute['class_name'];
	}

	public function getRouteMethodName() {
		return (isset($this -> validRoute['method']) && !empty($this -> validRoute['method'])) ? $this -> validRoute['method'] : NULL;
	}

	public function getRouteMethodParameters() {
		return (isset($this -> validRoute['parameters']) && !empty($this -> validRoute['parameters'])) ? $this -> validRoute['parameters'] : NULL;
	}
	
	public function checkForRouteMatch() {
		$url = rtrim(ltrim($this -> getUrlParams(), '/'), '/');
		if(isset($this -> routes[$url])) {
			$this -> validRoute = $this -> routes[$url];
			return true;
		}
		else return false;
	}
	// Making sure that a valid index page was 
	// given inside the controller class
	public function checkForValidIndexPage() {
		$this -> index_page = get_setting('index_page');
		$this -> index_file_name = $this -> getFileName($this -> index_page);

		if($this -> index_page == NULL || empty($this -> index_page) || !file_exists(CONTROLLERS_PATH . $this -> index_file_name)) {
			throw503("Please supply a valid index page in the config file.");
		}
	}

	// Parsing the url into an array using
	// the "/" as a separator
	public function parseUrlIntoArray() {
		$url_params = rtrim(ltrim($this -> getUrlParams(), '/'), '/') . '/';
		return explode('/', $url_params);
	}

	// Getting the server info or the default
	// landing page
	public function getUrlParams() {
		return (isset($_GET['path'])) ? $_GET['path'] : get_setting('index_page');
	}

	// Function that returns a proper file name
	// without underscores or uppercase letters
	public function getFileName($className) {
		return strtolower(str_replace('_', '', $className)) . '.php';
	}

	// Returning the class name from the array
	// which is the first element
	public function getClassName($url) {
		return $url[0];
	}

	// Check if the class file is a valid file
	public function checkClassFile($filename) {
		$filename = strtolower($filename);
		return file_exists(CONTROLLERS_PATH . $filename);
	}

	// Loading the parameters such as
	// the method and the parameters
	public function loadParams() {
		if(isset($this -> url_params_array[1])) {
			$this -> method_name = $this -> url_params_array[1];
			if(isset($this -> url_params_array[2])) {
				unset($this -> url_params_array[0]);
				unset($this -> url_params_array[1]);

				$this -> method_parameters = $this -> url_params_array;
			}
		}
	}
}