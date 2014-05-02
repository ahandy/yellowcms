<?php

/*
*---------------------------------------------------------
* Making sure there was no direct access to the script
*---------------------------------------------------------
*/ 
defined('SYSTEM_PATH') OR exit('No direct access to the script is allowed');



/*
*---------------------------------------------------------
* Function that gets a setting from the settings.php 
* file and then returns it to the script that called it
* or returns null in case it does not exist
*---------------------------------------------------------
*/ 

if(!function_exists('get_setting')) {
	function get_setting($value) {
		static $_settings = array();
		// Loading settings
		if(empty($_settings)) {
			if(!file_exists(CONFIG_PATH . 'settings.php')) {
				throw503("The settings.php file was not found in " . CONFIG_PATH);
			}
			require CONFIG_PATH . 'settings.php';
			$_settings = $_setting;
		}
	
		// Returning the config parameter
		return (isset($_settings[$value])) ? $_settings[$value] : NULL;
	}
}



/*
*---------------------------------------------------------
* Function that loads a class by first checking if it 
* exists, then requiring in the script.
*---------------------------------------------------------
*/
if(!function_exists('load_class')) {
	function load_class($className, $classDirectory = CORE_FOLDER) {
		static $_loadedClasses = array();

		// If the class is already instantiated before, just return it
		$lc_className = strtolower($className);
		
		
		if(isset($_loadedClasses[$lc_className])) {
			return $_loadedClasses[$lc_className];
		}

		// Making sure there is a trailing slash and that
		// the class directory exists, if any.
		if(empty($classDirectory)) $classDirectory = CORE_FOLDER;

		$classDirectory = rtrim($classDirectory, '/') . DS;
		if(!is_dir(SYSTEM_PATH . $classDirectory)) {
			throw503(SYSTEM_PATH . $classDirectory . " was not found during the initialization of the class '{$className}'");
		}
	
		// Getting the appropriate file name for classes
		$class_filename = $lc_className . '.php';

		// Checking if the class file exists
		if(!file_exists(SYSTEM_PATH . $classDirectory . $class_filename)) {
			throw503("The file {$class_filename} was not found in " . SYSTEM_PATH . $classDirectory);
		}

		// Loading the class file
		require_once SYSTEM_PATH . $classDirectory . $class_filename;

		// Checking to see if the class exists
		if(!class_exists($className, FALSE)) {
			throw503("Class name {$className} could not be found inside its respective class file.");
		}

		// Keeping track of what we just loaded
		class_inid($lc_className, $classDirectory);
		$_loadedClasses[$lc_className] = new $className();

		return $_loadedClasses[$lc_className];
	}
}


/*
*---------------------------------------------------------
* Function that loads a file by checking if it exists
* first
*---------------------------------------------------------
*/
if(!function_exists('load_file')) {
	function load_file($filepath) {
		if(!file_exists($filepath)) {
			if(DEBUG_ALL) throw503("File {$filepath} was not found.");
			return false;
		}

		else {
			require_once $filepath;
			return true;
		}
	}
}


/*
*---------------------------------------------------------
* Function that sets the class as initialized so that
* it can later be accessed
*---------------------------------------------------------
*/ 
if(!function_exists('class_inid')) {
	function class_inid($className = '', $directory = '') {
		static $_loadedClassesList = array();

		if(!empty($className)) {
			$_loadedClassesList[$className] = $directory;
		}

		return $_loadedClassesList;
	}
}


/*
*---------------------------------------------------------
* Function that returns a URL from a param
*---------------------------------------------------------
*/ 
if(!function_exists('toUrl')) {
	function toUrl($url) {
		return HTTP_PATH . $url;
	}
}



// Credits to codeIgniter for this function
if(!function_exists('redirect')) {
	/**
	 * Header Redirect
	 *
	 * Header redirect in two flavors
	 * For very fine grained control over headers, you could use the Output
	 * Library's set_header() function.
	 *
	 * @param        string        $uri        URL
	 * @param        string        $method        Redirect method
	 *                        'auto', 'location' or 'refresh'
	 * @param        int        $code        HTTP Response status code
	 * @return        void
	 */
	function redirect($uri = '', $method = 'auto', $code = NULL)
	{
	        if ( ! preg_match('#^(\w+:)?//#i', $uri))
	        {
	                if(!defined('HTTP')) {
	                	if(DEBUG_ALL) throw503("Please supply a valid argument for the redirect function. URL should be a proper URL. Supplied: {$uri}");
	                	return false;
	                }

	                $uri = HTTP . $uri;
	        }

	        // IIS environment likely? Use 'refresh' for better compatibility
	        if ($method === 'auto' && isset($_SERVER['SERVER_SOFTWARE']) && strpos($_SERVER['SERVER_SOFTWARE'], 'Microsoft-IIS') !== FALSE)
	        {
	                $method = 'refresh';
	        }
	        elseif ($method !== 'refresh' && (empty($code) OR ! is_numeric($code)))
	        {
	                if (isset($_SERVER['SERVER_PROTOCOL'], $_SERVER['REQUEST_METHOD']) && $_SERVER['SERVER_PROTOCOL'] === 'HTTP/1.1')
	                {
	                        $code = ($_SERVER['REQUEST_METHOD'] !== 'GET')
	                                ? 303        // reference: http://en.wikipedia.org/wiki/Post/Redirect/Get
	                                : 307;
	                }
	                else
	                {
	                        $code = 302;
	                }
	        }

	        switch ($method)
	        {
	                case 'refresh':
	                        header('Refresh:0;url='.$uri);
	                        break;
	                default:
	                        header('Location: '.$uri, TRUE, $code);
	                        break;
	        }
	        exit;
	}
}





/*
*---------------------------------------------------------
* Function that returns all the loaded classes
*---------------------------------------------------------
*/ 
if(!function_exists('returnLoadedClasses')) {
	function returnLoadedClasses() {
		return class_inid();
	}
}



/*
*---------------------------------------------------------
* Function that throws a 503 error and then exits script
*---------------------------------------------------------
*/
if(!function_exists('throw503')) {
	function throw503($message) {
		header('HTTP/1.1 503 Service Unavailable.', TRUE, 503);
		echo $message;
		exit();
	}
}


/*
*---------------------------------------------------------
* Function that loads a user inputted model
*---------------------------------------------------------
*/ 
if(!function_exists('loadModel')) {
	function loadModel($modelName, $modelFileName = FALSE) {
		static $_loadedModels = array();

		if(!$modelFileName) {
			$modelFileName = strtolower(str_replace(' ', '.', $modelName)) . '.php';
		}

		$modelName = $modelName . '_model';	
		$lc_modelName = strtolower($modelName);	

		if(isset($_loadedModels[$lc_modelName])) {
			return $_loadedModels[$lc_modelName];
		}

		if(!file_exists(MODELS_PATH . $modelFileName)) {
			if(DEBUG_ALL) throw503("Invalid model file name: " . MODELS_PATH . $modelFileName);
		}

		else {
			require_once MODELS_PATH . $modelFileName;

			if(in_array($modelName, class_inid())) {
				if(DEBUG_ALL) throw503("The class name you used for the model {$modelName} was already used.");
				return false;
			}

			if(!class_exists($modelName)) {
				if(DEBUG_ALL) throw503("Invalid class name supplied. {$modelName}");
				return NULL;
			}
		}

		// Keeping track of what we just loaded
		class_inid($lc_modelName, MODELS_PATH);
		$_loadedModels[$lc_modelName] = new $modelName();
		return $_loadedModels[$lc_modelName];
	}
}

/*
*---------------------------------------------------------
* Function that refreshes the page
*---------------------------------------------------------
*/ 
if(!function_exists('refresh')) {
	function refresh() {
		echo '<meta http-equiv="refresh" content="0">';
	}
}